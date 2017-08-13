<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Bad Config Files scan class.
 *
 * @package SecuPress
 * @subpackage SecuPress_Scan
 * @since 1.0
 */
class SecuPress_Scan_Bad_Config_Files extends SecuPress_Scan implements SecuPress_Scan_Interface {

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
		$this->title    = __( 'Check if your installation contains old or backed up <code>wp-config.php</code> files like <code>wp-config.bak</code>, <code>wp-config.old</code> etc.', 'secupress' );
		$this->more     = __( 'Some attackers will try to find some old and backed up config files to try to steal them. Prevent this kind of attack simply by removing them.', 'secupress' );
		$this->more_fix = __( 'Rename all these files using a random name and using the <code>.php</code> extension to prevent being downloaded.', 'secupress' );
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
			0   => __( 'You don\'t have old <code>wp-config</code> files.', 'secupress' ),
			1   => _n_noop( 'Your old <code>wp-config.php</code> file was successfully suffixed with %s.', 'Your old <code>wp-config.php</code> files were successfully suffixed with %s.', 'secupress' ),
			// "warning"
			100 => _n_noop( '%1$d old <code>wp-config.php</code> file was successfully suffixed with %2$s.', '%1$d old <code>wp-config.php</code> files were successfully suffixed with %2$s.', 'secupress' ),
			101 => _n_noop( 'Sorry, this file could not be renamed: %s', 'Sorry, those files could not be renamed: %s', 'secupress' ),
			// "bad"
			200 => _n_noop( 'Your installation should not contain this old or backed up <code>wp-config.php</code> file: %s.', 'Your installation should not contain these old or backed up <code>wp-config.php</code> files: %s.', 'secupress' ),
			201 => _n_noop( 'Sorry, the old <code>wp-config.php</code> file could not be renamed.', 'Sorry, the old <code>wp-config.php</code> files could not be renamed.', 'secupress' ),
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
		return __( 'http://docs.secupress.me/article/96-wp-config-php-file-backups-scan', 'secupress' );
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
		$files = static::get_files();

		if ( $files ) {
			// "bad"
			$files = self::wrap_in_tag( $files );
			$this->add_message( 200, array( count( $files ), $files ) );
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
		$files = static::get_files();

		// Should not happen.
		if ( ! $files ) {
			// "good"
			$this->add_fix_message( 0 );
			return parent::fix();
		}

		$wp_filesystem = secupress_get_filesystem();
		$count_all     = count( $files );
		$renamed       = array();
		$suffix        = '.' . time() . '.secupress.php';

		// Rename the files.
		foreach ( $files as $filename ) {
			$new_file = ABSPATH . $filename . $suffix;

			if ( $wp_filesystem->move( ABSPATH . $filename, $new_file ) ) {
				$wp_filesystem->chmod( $new_file, FS_CHMOD_FILE );
				// Counting the renamed files is safer that counting the not renamed ones.
				$renamed[] = $filename;
			}
		}

		$count_renamed = count( $renamed );

		if ( $count_renamed === $count_all ) {
			// "good": all files were renamed.
			$this->add_fix_message( 1, array( $count_all, '<code>' . $suffix . '</code>' ) );
		} elseif ( $count_renamed ) {
			// "warning": some files could not be renamed.
			$not_renamed = array_diff( $files, $renamed );
			$not_renamed = static::wrap_in_tag( $not_renamed );

			$this->add_fix_message( 100, array( $count_renamed, $count_renamed, '<code>' . $suffix . '</code>' ) );
			$this->add_fix_message( 101, array( count( $not_renamed ), $not_renamed ) );
		}
		else {
			// "bad": no files could not be renamed.
			$this->add_fix_message( 201, array( $count_all ) );
		}

		return parent::fix();
	}


	/** Tools. ================================================================================== */

	/**
	 * Get config files.
	 *
	 * @since 1.0
	 *
	 * @return (array) An array of file names.
	 */
	protected static function get_files() {
		$files = glob( ABSPATH . '*wp-config*.*' );
		$files = array_map( 'basename', $files );

		foreach ( $files as $k => $file ) {
			if ( 'php' === pathinfo( $file, PATHINFO_EXTENSION ) ) {
				unset( $files[ $k ] );
			}
		}

		return $files;
	}
}
