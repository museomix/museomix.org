<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

/**
 * Themes Update scan class.
 *
 * @package SecuPress
 * @subpackage SecuPress_Scan
 * @since 1.0
 */
class SecuPress_Scan_Themes_Update extends SecuPress_Scan implements SecuPress_Scan_Interface {

	/** Constants. ============================================================================== */

	/**
	 * Class version.
	 *
	 * @var (string)
	 */
	const VERSION = '1.0.1';


	/** Properties. ============================================================================= */

	/**
	 * The reference to the *Singleton* instance of this class.
	 *
	 * @var (object)
	 */
	protected static $_instance;

	/**
	 * Tells if the fix must occur after all other scans and fixes, while no other scan/fix is running.
	 *
	 * @var (bool)
	 */
	protected $delayed_fix = true;


	/** Init and messages. ====================================================================== */

	/**
	 * Init.
	 *
	 * @since 1.0
	 */
	protected function init() {
		$this->title    = __( 'Check if your themes are up to date.', 'secupress' );
		$this->more     = __( 'It is very important to keep your WordPress installation up to date. If you cannot update because of a theme, contact its author and submit your issue.', 'secupress' );
		$this->more_fix = __( 'Update all your themes that are not up to date.', 'secupress' );
	}


	/**
	 * Get messages.
	 *
	 * @since 1.0
	 *
	 * @param (int) $message_id A message ID.
	 *
	 * @return (string|array) A message if a message ID is provided. An array containing all messages otherwise.
	 */
	public static function get_messages( $message_id = null ) {
		$messages = array(
			// "good"
			0   => __( 'Your themes are up to date.', 'secupress' ),
			// "warning"
			100 => _n_noop( '<strong>%d symlinked theme</strong> is not up to date, and cannot be updated automatically.', '<strong>%d symlinked themes</strong> are not up to date, and cannot be updated automatically.', 'secupress' ),
			// "bad"
			200 => _n_noop( '<strong>%1$d theme</strong> is not up to date: %2$s.',  '<strong>%1$d themes</strong> are not up to date: %2$s.', 'secupress' ),
			// "cantfix"
			300 => __( 'Some themes could not be updated correctly.', 'secupress' ),
			301 => _n_noop( '<strong>%d symlinked theme</strong> is not up to date, and cannot be updated automatically.', '<strong>%d symlinked themes</strong> are not up to date, and cannot be updated automatically.', 'secupress' ),
		);

		if ( isset( $message_id ) ) {
			return isset( $messages[ $message_id ] ) ? $messages[ $message_id ] : __( 'Unknown message', 'secupress' );
		}

		return $messages;
	}


	/** Getters. ================================================================================ */

	/**
	 * Get the documentation URL.
	 *
	 * @since 1.2.3
	 *
	 * @return (string)
	 */
	public static function get_docs_url() {
		return __( 'http://docs.secupress.me/article/119-theme-update-scan', 'secupress' );
	}


	/** Scan. =================================================================================== */

	/**
	 * Scan for flaw(s).
	 *
	 * @since 1.0
	 *
	 * @return (array) The scan results.
	 */
	public function scan() {
		ob_start();

		wp_update_themes();
		$themes           = get_site_transient( 'update_themes' );
		$themes           = ! empty( $themes->response ) && is_array( $themes->response ) ? array_keys( $themes->response ) : array();
		$symlinked_themes = array();

		if ( $themes ) {
			$symlinked_themes = array_filter( $themes, 'secupress_is_theme_symlinked' );
			$themes           = array_diff( $themes, $symlinked_themes );
			$themes           = array_flip( $themes );
			$themes           = array_intersect_key( wp_get_themes(), $themes );
			$themes           = wp_list_pluck( $themes, 'Name' );
		}

		ob_flush();

		if ( $count = count( $themes ) ) {
			// "bad"
			$this->add_message( 200, array( $count, $count, self::wrap_in_tag( $themes ) ) );
		}

		if ( $count = count( $symlinked_themes ) ) {
			// "warning"
			$this->add_message( 100, array( $count, $count ) );
		}

		// "good"
		$this->maybe_set_status( 0 );

		return parent::scan();
	}


	/** Fix. ==================================================================================== */

	/**
	 * Try to fix the flaw(s).
	 *
	 * @since 1.0
	 *
	 * @return (array) The fix results.
	 */
	public function fix() {
		$themes = get_site_transient( 'update_themes' );
		$themes = ! empty( $themes->response ) && is_array( $themes->response ) ? array_keys( $themes->response ) : array();

		if ( $themes ) {
			$symlinked_themes = array_filter( $themes, 'secupress_is_theme_symlinked' );
			$themes           = array_diff( $themes, $symlinked_themes );
		}

		if ( $themes ) {
			ob_start();
			@set_time_limit( 0 );

			// Remove the WP upgrade process for translation since it will output data, use our own based on core but using a silent upgrade.
			remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
			add_action( 'upgrader_process_complete', 'secupress_async_upgrades', 20 );

			include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

			$nonce    = 'bulk-update-themes';
			$url      = implode( ',', $themes );
			$url      = 'update.php?action=update-selected-themes&amp;themes=' . urlencode( $url );
			$skin     = new Automatic_Upgrader_Skin( array( 'nonce' => $nonce, 'url' => $url ) );
			$upgrader = new Theme_Upgrader( $skin );

			$upgrader->bulk_upgrade( $themes );

			ob_end_clean();
		}

		// Test if we succeeded.
		$themes = get_site_transient( 'update_themes' );
		$themes = ! empty( $themes->response ) && is_array( $themes->response ) ? array_keys( $themes->response ) : array();

		if ( ! $themes ) {
			// "good"
			$this->add_fix_message( 0 );
		} else {
			$symlinked_themes = array_filter( $themes, 'secupress_is_theme_symlinked' );
			$themes           = array_diff( $themes, $symlinked_themes );

			if ( $count = count( $symlinked_themes ) ) {
				// "cantfix"
				$this->add_fix_message( 301, array( $count, $count ) );
			} else {
				// "cantfix"
				$this->add_fix_message( 300 );
			}
		}

		return parent::fix();
	}
}
