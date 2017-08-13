<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Bad Request Methods scan class.
 *
 * @package SecuPress
 * @subpackage SecuPress_Scan
 * @since 1.0
 */
class SecuPress_Scan_Bad_Request_Methods extends SecuPress_Scan implements SecuPress_Scan_Interface {

	/** Constants. ============================================================================== */

	/**
	 * Class version.
	 *
	 * @var (string)
	 */
	const VERSION = '1.0.2';


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
		$this->title    = __( 'Check if bad request methods can access your website.', 'secupress' );
		$this->more     = __( 'There are malicious scripts and bots out there, hammering your site with bad HTTP GET requests. Let\'s check if your website can handle that.', 'secupress' );
		$this->more_fix = sprintf(
			__( 'Activate the option %1$s in the %2$s module.', 'secupress' ),
			'<em>' . __( 'Block Bad Request Methods', 'secupress' ) . '</em>',
			'<a href="' . esc_url( secupress_admin_url( 'modules', 'firewall' ) ) . '#row-bbq-headers_request-methods-header">' . __( 'Firewall', 'secupress' ) . '</a>'
		);
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
			0   => __( 'You are currently blocking bad request methods.', 'secupress' ),
			1   => __( 'Protection activated', 'secupress' ),
			// "warning"
			100 => _n_noop( 'Unable to determine the status of your homepage for %s request method.', 'Unable to determine the status of your homepage for %s request methods.', 'secupress' ),
			101 => sprintf(
				/** Translators: 1 is the name of a protection, 2 is the name of a module. */
				__( 'But you can activate the %1$s protection from the module %2$s.', 'secupress' ),
				'<strong>' . __( 'Block Bad Request Methods', 'secupress' ) . '</strong>',
				'<a target="_blank" href="' . esc_url( secupress_admin_url( 'modules', 'firewall' ) ) . '#row-bbq-headers_request-methods-header">' . __( 'Firewall', 'secupress' ) . '</a>'
			),
			// "bad"
			200 => _n_noop( 'Your website should block %s request method.', 'Your website should block %s request methods.', 'secupress' ),
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
		return __( 'http://docs.secupress.me/article/112-bad-request-method-scan', 'secupress' );
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
		// These methods should be blocked.
		$methods = array( 'TRACK', 'OPTIONS', 'CONNECT', 'SECUPRESS_TEST_' . time() );

		if ( secupress_is_submodule_active( 'sensitive-data', 'restapi' ) ) {
			// Sub-module activated === REST API disabled === these methods should also be blocked.
			$methods = array_merge( $methods, array( 'PUT', 'PATCH', 'DELETE' ) );
		}

		$bads         = array();
		$warnings     = array();
		$request_args = $this->get_default_request_args();

		foreach ( $methods as $method ) {

			$request_args['method'] = $method;
			$response = wp_remote_request( add_query_arg( secupress_generate_key( 6 ), secupress_generate_key( 8 ), user_trailingslashit( home_url() ) ), $request_args );

			if ( ! is_wp_error( $response ) ) {

				if ( 200 === wp_remote_retrieve_response_code( $response ) && '' !== wp_remote_retrieve_body( $response ) ) {
					// "bad"
					$bads[] = '<code>' . $method . '</code>';
				}
			} elseif ( 'http_request_failed' !== $response->get_error_code() ) {
				// "warning"
				$warnings[] = '<code>' . $method . '</code>';
			}
		}

		if ( $bads ) {
			// "bad"
			$this->add_message( 200, array( count( $bads ), $bads ) );
		}

		if ( $warnings ) {
			// "warning"
			$this->add_message( 100, array( count( $warnings ), $warnings ) );
			$this->add_message( 101 );
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
		// Activate.
		secupress_activate_submodule( 'firewall', 'request-methods-header' );

		// "good"
		$this->add_fix_message( 1 );

		return parent::fix();
	}
}
