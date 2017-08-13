<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Login Errors Disclose scan class.
 *
 * @package SecuPress
 * @subpackage SecuPress_Scan
 * @since 1.0
 */
class SecuPress_Scan_Login_Errors_Disclose extends SecuPress_Scan implements SecuPress_Scan_Interface {

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


	/** Init and messages. ====================================================================== */

	/**
	 * Init.
	 *
	 * @since 1.0
	 */
	protected function init() {
		$this->title    = __( 'Check if your WordPress lists the some login errors.', 'secupress' );
		$this->more     = __( 'Error messages displayed on the login page are a useful information for an attacker: they should not be displayed, or at least, should be less specific.', 'secupress' );
		$this->more_fix = __( 'Hide errors on login page to avoid being read by attackers.', 'secupress' );
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
			0   => __( 'You are currently not displaying <strong>login errors</strong>.', 'secupress' ),
			1   => __( 'Protection activated', 'secupress' ),
			// "bad"
			200 => __( '<strong>Login errors</strong> should not be displayed.', 'secupress' ),
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
		return __( 'http://docs.secupress.me/article/129-login-error-message-scan', 'secupress' );
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
		$messages = secupress_login_errors_disclose_get_messages( false );
		$messages = '	' . implode( "<br />\n	", $messages ) . "<br />\n";
		/** This filter is documented in wp-login.php */
		$messages = apply_filters( 'login_errors', $messages );

		$pattern = secupress_login_errors_disclose_get_messages();
		$pattern = '@\s(' . implode( '|', $pattern ) . ')<br />\n@';

		if ( preg_match( $pattern, $messages ) ) {
			// "bad"
			$this->add_message( 200 );
		} else {
			// "good"
			$this->add_message( 0 );
		}

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
		$messages = secupress_login_errors_disclose_get_messages( false );
		$messages = '	' . implode( "<br />\n	", $messages ) . "<br />\n";
		/** This filter is documented in wp-login.php */
		$messages = apply_filters( 'login_errors', $messages );

		$pattern = secupress_login_errors_disclose_get_messages();
		$pattern = '@\s(' . implode( '|', $pattern ) . ')<br />\n@';

		if ( preg_match( $pattern, $messages ) ) {

			secupress_activate_submodule( 'discloses', 'login-errors-disclose' );

			// "good"
			$this->add_fix_message( 1 );
		} else {
			// "good"
			$this->add_fix_message( 0 );
		}

		return parent::fix();
	}
}
