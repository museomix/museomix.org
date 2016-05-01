<?php
/*
Plugin Name: Admin Columns
Version: 2.5.6.2
Description: Customize columns on the administration screens for post(types), pages, media, comments, links and users with an easy to use drag-and-drop interface.
Author: AdminColumns.com
Author URI: https://www.admincolumns.com
Plugin URI: https://www.admincolumns.com
Text Domain: codepress-admin-columns
Domain Path: /languages
License: GPLv2

Copyright 2011-2016  AdminColumns.com  info@admincolumns.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin information
define( 'CPAC_VERSION', '2.5.6.2' ); // Current plugin version
define( 'CPAC_UPGRADE_VERSION', '2.0.0' ); // Latest version which requires an upgrade
define( 'CPAC_URL', plugin_dir_url( __FILE__ ) );
define( 'CPAC_DIR', plugin_dir_path( __FILE__ ) );

// Only run plugin in the admin interface
if ( ! is_admin() ) {
	return false;
}

/**
 * Dependencies
 *
 * @since 1.3.0
 */
require_once CPAC_DIR . 'classes/utility.php';
require_once CPAC_DIR . 'classes/third_party.php';
require_once CPAC_DIR . 'includes/arrays.php';

/**
 * The Admin Columns Class
 *
 * @since 1.0
 */
class CPAC {

	/**
	 * Admin Columns add-ons class instance
	 *
	 * @since 2.2
	 * @access private
	 * @var CPAC_Addons
	 */
	private $_addons;

	/**
	 * Admin Columns settings class instance
	 *
	 * @since 2.2
	 * @access private
	 * @var CPAC_Settings
	 */
	private $_settings;

	/**
	 * Admin Columns plugin upgrade class instance
	 *
	 * @since 2.2.7
	 * @access private
	 * @var CPAC_Upgrade
	 */
	private $_upgrade;

	/**
	 * Registered storage model class instances
	 * Array of CPAC_Storage_Model instances, with the storage model keys (e.g. post, page, wp-users) as keys
	 *
	 * @since 2.0
	 * @var array
	 */
	private $storage_models;

	/**
	 * @since 2.4.9
	 */
	private $current_storage_model;

	/**
	 * @since 2.5
	 */
	protected static $_instance = null;

	/**
	 * @since 2.5
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * @since 1.0
	 */
	function __construct() {

		register_activation_hook( __FILE__, array( $this, 'set_capabilities' ) );

		// Hooks
		add_action( 'init', array( $this, 'localize' ) );
		add_action( 'wp_loaded', array( $this, 'after_setup' ) ); // Setup callback, important to load after set_storage_models
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 1, 2 );
		add_filter( 'list_table_primary_column', array( $this, 'set_primary_column' ), 20, 1 );

		// Populating columns
		add_action( 'admin_init', array( $this, 'set_columns' ) );

		// Settings
		include_once CPAC_DIR . 'classes/settings.php';
		$this->_settings = new CPAC_Settings( $this );

		// Addons
		include_once CPAC_DIR . 'classes/addons.php';
		$this->_addons = new CPAC_Addons( $this );

		// Upgrade
		require_once CPAC_DIR . 'classes/upgrade.php';
		$this->_upgrade = new CPAC_Upgrade( $this );

		// Settings
		include_once CPAC_DIR . 'classes/review_notice.php';
		new CPAC_Review_Notice( $this );
	}

	/**
	 * Fire callbacks for admin columns setup completion
	 *
	 * @since 2.2
	 */
	public function after_setup() {

		/**
		 * Fires when Admin Columns is fully loaded
		 * Use this for setting up addon functionality
		 *
		 * @since 2.0
		 *
		 * @param CPAC $cpac_instance Main Admin Columns plugin class instance
		 */
		do_action( 'cac/loaded', $this );

		// Current listings page storage model
		if ( $storage_model = $this->get_current_storage_model() ) {
			do_action( 'cac/current_storage_model', $storage_model );
		}
	}

	/**
	 * @since 2.2
	 * @uses load_plugin_textdomain()
	 */
	public function localize() {
		load_plugin_textdomain( 'codepress-admin-columns', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * @since 2.2.4
	 */
	public function scripts() {

		$minified = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'cpac-admin-columns', CPAC_URL . "assets/js/admin-columns{$minified}.js", array( 'jquery', 'jquery-qtip2' ), CPAC_VERSION );
		wp_register_script( 'jquery-qtip2', CPAC_URL . "external/qtip2/jquery.qtip{$minified}.js", array( 'jquery' ), CPAC_VERSION );
		wp_register_style( 'jquery-qtip2', CPAC_URL . "external/qtip2/jquery.qtip{$minified}.css", array(), CPAC_VERSION, 'all' );
		wp_register_style( 'cpac-columns', CPAC_URL . "assets/css/column{$minified}.css", array(), CPAC_VERSION, 'all' );

		if ( $this->get_current_storage_model() ) {
			add_filter( 'admin_body_class', array( $this, 'admin_class' ) );
			add_action( 'admin_head', array( $this, 'admin_scripts' ) );

			wp_enqueue_script( 'cpac-admin-columns' );
			wp_enqueue_style( 'jquery-qtip2' );
			wp_enqueue_style( 'cpac-columns' );
		}
	}

	/**
	 * Add capabilty to administrator to manage admin columns.
	 * You can use the capability 'manage_admin_columns' to grant other roles this privilidge as well.
	 *
	 * @since 2.0.4
	 */
	public function set_capabilities() {
		if ( $role = get_role( 'administrator' ) ) {
			$role->add_cap( 'manage_admin_columns' );
		}
	}

	/**
	 * Set the primary columns for the Admin Columns columns
	 *
	 * @since 2.5.5
	 */
	public function set_primary_column( $default ) {
		if ( $storage_model = $this->get_current_storage_model() ) {
			if ( ! $storage_model->get_column_by_name( $default ) ) {
				$default = key( $storage_model->get_columns() );
			}
		}

		return $default;
	}

	/**
	 * Get registered storage models
	 *
	 * @since 2.5
	 */
	public function get_storage_models() {
		if ( empty( $this->storage_models ) ) {

			$storage_models = array();

			// Load storage model class files and column base class files
			require_once CPAC_DIR . 'classes/storage_model.php';
			require_once CPAC_DIR . 'classes/storage_model/post.php';
			require_once CPAC_DIR . 'classes/storage_model/user.php';
			require_once CPAC_DIR . 'classes/storage_model/media.php';
			require_once CPAC_DIR . 'classes/storage_model/comment.php';

			// Create a storage model per post type
			foreach ( $this->get_post_types() as $post_type ) {
				$storage_model = new CPAC_Storage_Model_Post( $post_type );
				$storage_models[ $storage_model->key ] = $storage_model;
			}

			// Create other storage models
			$storage_model = new CPAC_Storage_Model_User();
			$storage_models[ $storage_model->key ] = $storage_model;

			$storage_model = new CPAC_Storage_Model_Media();
			$storage_models[ $storage_model->key ] = $storage_model;

			$storage_model = new CPAC_Storage_Model_Comment();
			$storage_models[ $storage_model->key ] = $storage_model;

			if ( apply_filters( 'pre_option_link_manager_enabled', false ) ) { // as of 3.5 link manager is removed
				require_once CPAC_DIR . 'classes/storage_model/link.php';

				$storage_model = new CPAC_Storage_Model_Link();
				$storage_models[ $storage_model->key ] = $storage_model;
			}

			/**
			 * Filter the available storage models
			 * Used by external plugins to add additional storage models
			 *
			 * @since 2.0
			 *
			 * @param array $storage_models List of storage model class instances ( [key] => [CPAC_Storage_Model object], where [key] is the storage key, such as "user", "post" or "my_custom_post_type")
			 * @param object $this CPAC
			 */
			$this->storage_models = apply_filters( 'cac/storage_models', $storage_models, $this );
		}

		return $this->storage_models;
	}

	/**
	 * Retrieve a storage model object based on its key
	 *
	 * @since 2.0
	 *
	 * @param string $key Storage model key (e.g. post, page, wp-users)
	 *
	 * @return bool|CPAC_Storage_Model Storage Model object (or false, on failure)
	 */
	public function get_storage_model( $key ) {
		$models = $this->get_storage_models();

		return isset( $models[ $key ] ) ? $models[ $key ] : false;
	}

	/**
	 * Only set columns on current screens or on specific ajax calls
	 *
	 * @since 2.4.9
	 */
	public function set_columns() {

		// Listings screen
		$storage_model = $this->get_current_storage_model();

		// WP Ajax calls (not AC)
		if ( $model = cac_wp_is_doing_ajax() ) {
			$storage_model = $this->get_storage_model( $model );
		}

		if ( $storage_model ) {
			$storage_model->init_listings_layout();
			$storage_model->init_manage_columns();
		}
	}

	/**
	 * Get column object
	 *
	 * @since 2.5.4
	 * @param $storage_key CPAC_Storage_Model->key
	 * @param $layout_id CPAC_Storage_Model->layout
	 * @param $column_name CPAC_Column->name
	 *
	 * @return object CPAC_Column Column onject
	 */
	public function get_column( $storage_key, $layout_id, $column_name ) {
		$column = false;
		if ( $storage_model = $this->get_storage_model( $storage_key ) ) {
			$storage_model->set_layout( $layout_id );
			$column = $storage_model->get_column_by_name( $column_name );
		}

		return $column;
	}

	/**
	 * Get storage model object of currently active storage model
	 * On the users overview page, for example, this returns the CPAC_Storage_Model_User object
	 *
	 * @since 2.2.4
	 *
	 * @return CPAC_Storage_Model
	 */
	public function get_current_storage_model() {
		if ( ! $this->current_storage_model && $this->is_columns_screen() && $this->get_storage_models() ) {
			foreach ( $this->get_storage_models() as $storage_model ) {
				if ( $storage_model->is_current_screen() ) {
					$this->current_storage_model = $storage_model;
					break;
				}
			}
		}

		return $this->current_storage_model;
	}

	/**
	 * Get a list of post types for which Admin Columns is active
	 *
	 * @since 1.0
	 *
	 * @return array List of post type keys (e.g. post, page)
	 */
	private function get_post_types() {
		$post_types = array();

		if ( post_type_exists( 'post' ) ) {
			$post_types['post'] = 'post';
		}
		if ( post_type_exists( 'page' ) ) {
			$post_types['page'] = 'page';
		}

		$post_types = array_merge( $post_types, get_post_types( array(
			'_builtin' => false,
			'show_ui'  => true
		) ) );

		/**
		 * Filter the post types for which Admin Columns is active
		 *
		 * @since 2.0
		 *
		 * @param array $post_types List of active post type names
		 */
		return apply_filters( 'cac/post_types', $post_types );
	}

	/**
	 * Get a list of taxonomies supported by Admin Columns
	 *
	 * @since 1.0
	 *
	 * @return array List of taxonomies
	 */
	public function get_taxonomies() {
		$taxonomies = get_taxonomies( array( 'public' => true ) );
		if ( isset( $taxonomies['post_format'] ) ) {
			unset( $taxonomies['post_format'] );
		}

		/**
		 * Filter the post types for which Admin Columns is active
		 *
		 * @since 2.0
		 *
		 * @param array $post_types List of active post type names
		 */
		return apply_filters( 'cac/taxonomies', $taxonomies );
	}

	/**
	 * Add a settings link to the Admin Columns entry in the plugin overview screen
	 *
	 * @since 1.0
	 * @see filter:plugin_action_links
	 */
	public function add_settings_link( $links, $file ) {

		if ( $file != plugin_basename( __FILE__ ) ) {
			return $links;
		}

		array_unshift( $links, '<a href="' . esc_url( admin_url( "options-general.php?page=codepress-admin-columns" ) ) . '">' . __( 'Settings' ) . '</a>' );

		return $links;
	}

	/**
	 * Adds a body class which is used to set individual column widths
	 *
	 * @since 1.4.0
	 *
	 * @param string $classes body classes
	 *
	 * @return string
	 */
	public function admin_class( $classes ) {
		if ( $storage_model = $this->get_current_storage_model() ) {
			$classes .= " cp-{$storage_model->key}";
		}

		return $classes;
	}

	/**
	 * Admin CSS for Column width and Settings Icon
	 *
	 * @since 1.4.0
	 */
	public function admin_scripts() {
		if ( ! ( $storage_model = $this->get_current_storage_model() ) ) {
			return;
		}

		$css_column_width = '';
		$edit_link = '';

		// CSS: columns width
		if ( $columns = $storage_model->get_stored_columns() ) {
			foreach ( $columns as $name => $options ) {

				if ( ! empty( $options['width'] ) && is_numeric( $options['width'] ) && $options['width'] > 0 ) {
					$unit = isset( $options['width_unit'] ) ? $options['width_unit'] : '%';
					$css_column_width .= ".cp-{$storage_model->key} .wrap table th.column-{$name} { width: {$options['width']}{$unit} !important; }";
				}

				// Load custom column scripts, used by 3rd party columns
				if ( $column = $storage_model->get_column_by_name( $name ) ) {
					$column->scripts();
				}
			}
		}

		// JS: edit button
		$general_options = get_option( 'cpac_general_options' );
		if ( current_user_can( 'manage_admin_columns' ) && ( ! isset( $general_options['show_edit_button'] ) || '1' === $general_options['show_edit_button'] ) ) {
			$edit_link = $storage_model->get_edit_link();
		}

		?>
		<?php if ( $css_column_width ) : ?>
			<style type="text/css">
				<?php echo $css_column_width; ?>
			</style>
		<?php endif; ?>
		<?php if ( $edit_link ) : ?>
			<script type="text/javascript">
				jQuery( document ).ready( function() {
					jQuery( '.tablenav.top .actions:last' ).append( '<a href="<?php echo $edit_link; ?>" class="cpac-edit add-new-h2"><?php _e( 'Edit columns', 'codepress-admin-columns' ); ?></a>' );
				} );
			</script>
		<?php endif; ?>

		<?php

		/**
		 * Add header scripts that only apply to column screens.
		 * @since 2.3.5
		 *
		 * @param object CPAC Main Class
		 */
		do_action( 'cac/admin_head', $storage_model, $this );
	}

	public function get_first_storage_model_key() {
		$keys = array_keys( (array) $this->get_storage_models() );

		return array_shift( $keys );
	}

	public function get_first_storage_model() {
		$models = array_values( $this->get_storage_models() );

		return isset( $models[0] ) ? $models[0] : false;
	}

	/**
	 * @since 2.5
	 */
	public function use_delete_confirmation() {
		return apply_filters( 'ac/delete_confirmation', true );
	}

	/**
	 * Whether this request is a columns screen (i.e. a content overview page)
	 *
	 * @since 2.2
	 * @return bool Returns true if the current screen is a columns screen, false otherwise
	 */
	public function is_columns_screen() {
		global $pagenow;
		$columns_screen = in_array( $pagenow, array( 'edit.php', 'upload.php', 'link-manager.php', 'edit-comments.php', 'users.php', 'edit-tags.php' ) );

		/**
		 * Filter whether the current screen is a columns screen (i.e. a content overview page)
		 * Useful for advanced used with custom content overview pages
		 *
		 * @since 2.2
		 *
		 * @param bool $columns_screen Whether the current request is a columns screen
		 */
		return apply_filters( 'cac/is_columns_screen', $columns_screen );
	}

	/**
	 * Whether the current screen is the Admin Columns settings screen
	 *
	 * @since 2.2
	 *
	 * @param strong $tab Specifies a tab screen (optional)
	 *
	 * @return bool True if the current screen is the settings screen, false otherwise
	 */
	public function is_settings_screen( $tab = '' ) {
		return cac_is_setting_screen( $tab );
	}

	/**
	 * Whether the current screen is a screen in which Admin Columns is used
	 * Used to quickly check whether storage models should be loaded
	 *
	 * @since 2.2
	 * @return bool Whether the current screen is an Admin Columns screen
	 */
	public function is_cac_screen() {

		/**
		 * Filter whether the current screen is a screen in which Admin Columns is active
		 *
		 * @since 2.2
		 *
		 * @param bool $is_cac_screen Whether the current screen is an Admin Columns screen
		 */
		return apply_filters( 'cac/is_cac_screen', $this->is_columns_screen() || cac_is_doing_ajax() || $this->is_settings_screen() );
	}

	/**
	 * Get admin columns settings class instance
	 *
	 * @since 2.2
	 * @return CPAC_Settings Settings class instance
	 */
	public function settings() {
		return $this->_settings;
	}

	/**
	 * Get admin columns add-ons class instance
	 *
	 * @since 2.2
	 * @return CPAC_Addons Add-ons class instance
	 */
	public function addons() {
		return $this->_addons;
	}

	/**
	 * Get admin columns upgrade class instance
	 *
	 * @since 2.2.7
	 * @return CPAC_Upgrade Upgrade class instance
	 */
	public function upgrade() {
		return $this->_upgrade;
	}

	/**
	 * Check whether the Advanced Custom Fields plugin is active
	 *
	 * @since 2.4.9
	 *
	 * @return bool Whether the Advanced Custom Fields plugin is active
	 */
	public function is_plugin_acf_active() {
		return class_exists( 'acf', false );
	}

	/**
	 * Check whether the WooCommerce plugin is active
	 *
	 * @since 2.4.9
	 *
	 * @return bool Whether the WooCommerce plugin is active
	 */
	public function is_plugin_woocommerce_active() {
		return class_exists( 'WooCommerce', false );
	}
}

function cpac() {
	return CPAC::instance();
}

// Global for backwards compatibility.
$GLOBALS['cpac'] = cpac();