<?php

final class ITSEC_Modules {
	/**
	 * @var ITSEC_Modules - Static property to hold our singleton instance
	 */
	static $instance = false;

	private $_available_modules = false;
	private $_module_paths = array();
	private $_active_modules = false;
	private $_module_settings = false;
	private $_settings_files_loaded = false;

	protected function __construct() {
		$itsec_core = ITSEC_Core::get_instance();

		register_activation_hook( $itsec_core->get_plugin_file(), array( $this, 'run_activation' ) );
		register_deactivation_hook( $itsec_core->get_plugin_file(), array( $this, 'run_deactivation' ) );

		// Action triggered from another part of Security which runs when the settings page is loaded.
		add_action( 'itsec_load_settings_page', array( $this, 'load_settings_page' ) );
	}

	/**
	 * Function to instantiate our class and make it a singleton
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Registers a single module
	 *
	 * @param string $slug The unique slug to use for the module
	 * @param string $path The path to the module. Considered absolute if it starts with a / and relative to the plugin root if it does not
	 *
	 * @return bool|WP_Error True on success and WP_Error on failure
	 */
	public function register_module( $slug, $path ) {
		$itsec_core = ITSEC_Core::get_instance();

		$slug = sanitize_title_with_dashes( $slug );
		if ( $path[0] != DIRECTORY_SEPARATOR ) {
			$path = path_join( dirname( $itsec_core->get_plugin_file() ), $path );
		}
		$this->_module_paths[ $slug ] = $path;
		return true;
	}

	/**
	 * Deregisters a single module
	 *
	 * @param string $slug The unique slug to use for the module
	 *
	 * @return bool
	 */
	public function deregister_module( $slug ) {
		$slug = sanitize_title_with_dashes( $slug );
		if ( isset( $this->_module_paths[ $slug ] ) ) {
			unset( $this->_module_paths[ $slug ] );
			return true;
		}
		return false;
	}

	public function get_module_settings( $module = false ) {
		if ( false === $this->_module_settings ) {
			$this->_module_settings = get_site_option( 'itsec_module_settings', array() );

			if ( ! is_array( $this->_module_settings ) ) {
				$this->_module_settings = array();
			}
		}

		if ( ! is_string( $module ) ) {
			return $this->_module_settings;
		}

		if ( isset( $this->_module_settings[$module] ) && is_array( $this->_module_settings[$module] ) ) {
			return $this->_module_settings[$module];
		}

		return array();
	}

	public function set_module_settings( $settings, $module = false ) {
		if ( ! is_array( $settings ) ) {
			return false;
		}

		$module_settings = $this->get_module_settings();

		if ( is_string( $module ) ) {
			$module_settings[$module] = $settings;
		} else {
			$module_settings = array_merge( $module_settings, $settings );
		}

		update_site_option( 'itsec_module_settings', $module_settings );

		$this->_module_settings = $module_settings;
	}

	public function get_available_modules() {
		if ( false !== $this->_available_modules ) {
			return $this->_available_modules;
		}

		$path = dirname( __FILE__ ) . '/modules';

		if ( ! is_array( $this->_module_paths ) ) {
			$this->_module_paths = array();
		}

		$this->_available_modules = array_keys( $this->_module_paths );

		return $this->_available_modules;
	}

	public function get_active_modules() {
		if ( false !== $this->_active_modules ) {
			return $this->_active_modules;
		}

		$this->_active_modules = get_site_option( 'itsec_active_modules', false );

		if ( ! is_array( $this->_active_modules ) ) {
			// The modules in this list are active when the plugin is first activated.
			$this->_active_modules = apply_filters( 'itsec-default-active-modules', array(
				'404-detection',
				'away-mode',
				'ban-users',
				'brute-force',
				'core',
				'backup',
				'file-change',
				'help',
				'hide-backend',
				'ip-check',
				'malware',
				'ssl',
				'strong-passwords',
				'tweaks',

				'admin-user',
				'salts',
				'content-directory',
				'database-prefix',
			) );
		}

		return $this->_active_modules;
	}

	/**
	 * Deactive a single module using it's ID
	 *
	 * @param string $module_id The ID of the module to remove
	 *
	 * @return bool True if the module is active and was deactivated, false if it was not active
	 */
	public function deactivate_module( $module_id ) {
		$active_modules = $this->get_active_modules();
		if ( isset( $active_modules[ $module_id ] ) ) {
			unset( $active_modules[ $module_id ] );
			$this->set_active_modules( $active_modules );
			return true;
		}
		return false;
	}

	public function set_active_modules( $modules ) {
		if ( ! is_array( $modules ) ) {
			return false;
		}

		$available_modules = $this->get_available_modules();
		$active_modules = $this->get_active_modules();

		// By using $available_modules as the source of which modules to check for addition and removing, information
		// about active modules that are currently not available (such as pro modules when running free) is preserved.
		foreach ( $available_modules as $available_module ) {
			if ( in_array( $available_module, $modules ) ) {
				if ( ! in_array( $available_module, $active_modules ) ) {
					$active_modules[] = $available_module;
				}
			} else {
				$index = array_search( $available_module, $active_modules, true );

				if ( false !== $index ) {
					unset( $active_modules[$index] );
				}
			}
		}

		update_site_option( 'itsec_active_modules', $active_modules );

		$this->_active_modules = $active_modules;
	}

	protected function load_module_file( $file, $modules = false ) {
		if ( ! in_array( $file, array( 'init.php', 'active.php', 'setup.php' ) ) ) {
			return;
		}

		if ( ! is_array( $modules ) ) {
			$modules = $this->get_available_modules();
		}

		foreach ( $modules as $module ) {
			if ( ! empty( $this->_module_paths[$module] ) && file_exists( "{$this->_module_paths[$module]}/{$file}" ) ) {
				include_once( "{$this->_module_paths[$module]}/{$file}" );
			}
		}
	}

	public function init_modules() {
		// The init.php file allows for loading code that needs to run even when the module is not active.
		$this->load_module_file( 'init.php' );

		// The active.php file is for code that will only run when the module is active.
		$active_modules = $this->get_active_modules();
		$this->load_module_file( 'active.php', $active_modules );
	}

	public function run_activation() {
		$this->load_module_file( 'setup.php' );

		do_action( 'itsec_modules_do_plugin_activation' );
	}

	public function run_deactivation() {
		$this->load_module_file( 'setup.php' );

		do_action( 'itsec_modules_do_plugin_deactivation' );
	}

	public static function run_uninstall() {
		$itsec_modules = self::get_instance();
		$itsec_modules->uninstall();
	}

	public function uninstall() {
		$this->load_module_file( 'setup.php' );

		do_action( 'itsec_modules_do_plugin_uninstall' );
	}

	public function run_upgrade( $old_version, $new_version ) {
		$this->load_module_file( 'setup.php' );

		do_action( 'itsec_modules_do_plugin_upgrade', $old_version, $new_version );
	}

	public function load_settings_page() {
		if ( $this->_settings_files_loaded ) {
			return;
		}

		$modules = $this->get_available_modules();

		foreach ( $modules as $path ) {
			include( "$path/settings.php" );
		}

		$this->_settings_files_loaded = true;
	}
}
ITSEC_Modules::get_instance();

abstract class ITSEC_Module_Init {
	protected $_id   = '';
	protected $_name = '';
	protected $_desc = '';

	public function get_id() {
		return $this->_id;
	}

	public function get_name() {
		return $this->_name;
	}

	public function get_desc() {
		return $this->_desc;
	}
}
