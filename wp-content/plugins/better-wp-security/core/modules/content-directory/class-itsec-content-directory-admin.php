<?php

class ITSEC_Content_Directory_Admin {

	private
		$last_error,
		$core,
		$module_path,
		$is_modified_by_it_security;

	function run( $core ) {

		$this->core        = $core;
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );

		if ( ! empty( $_POST ) ) {
			add_action( 'itsec_admin_init', array( $this, 'process_post_data' ) );
		}

		if ( ! $this->is_custom_directory() || $this->is_modified_by_it_security() ) {
			add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) );
		}
	}
	
	protected function get_wp_config_define_warning() {
		return __( 'Do not remove. Removing this line could break your site. Added by Security > Settings > Change Content Directory.', 'better-wp-security' );
	}
	
	protected function get_wp_config_define( $name, $value, $include_warning_comment = true ) {
		$name = str_replace( "'", "\\'", $name );
		$value = str_replace( "'", "\\'", $value );
		$line = "define( '$name', '$value' );";
		
		if ( $include_warning_comment ) {
			$line .= ' // ' . $this->get_wp_config_define_warning();
		}
		
		return $line;
	}
	
	protected function get_wp_config_modification( $dir, $url, $include_warning_comment = true ) {
		$modification  = $this->get_wp_config_define( 'WP_CONTENT_DIR', $dir, $include_warning_comment ) . "\n";
		$modification .= $this->get_wp_config_define( 'WP_CONTENT_URL', $url, $include_warning_comment );
		
		return $modification;
	}
	
	protected function get_wp_config_define_expression( $include_warning_comment = true ) {
		$expression = $this->get_wp_config_modification( 'WILDCARD', 'WILDCARD', $include_warning_comment );
		$expression = preg_quote( $expression, '|' );
		$expression = str_replace( ' ', '\s*', $expression );
		$expression = str_replace( 'WILDCARD', "[^']+", $expression );
		$expression = "|$expression|";
		
		if ( $include_warning_comment ) {
			$expression = str_replace( "\n", "\s*[\r\n]+\s*", $expression );
		} else {
			$expression = str_replace( "\n", "\s*", $expression );
		}
		
		return $expression;
	}
	
	/**
	 * Add meta boxes to primary options pages
	 *
	 * @param array $available_pages array of available page_hooks
	 */
	public function add_admin_meta_boxes() {
		add_meta_box(
			'content_directory_options',
			__( 'Change Content Directory', 'better-wp-security' ),
			array( $this, 'metabox_advanced_settings' ),
			'security_page_toplevel_page_itsec_advanced',
			'advanced',
			'core'
		);
	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function process_post_data() {
		if ( isset( $_POST['undo_change_content_directory'] ) ) {
			$this->undo_change_content_directory();
		} else if ( isset( $_POST['itsec_enable_content_dir'] ) && 'true' == $_POST['itsec_enable_content_dir'] && ! $this->is_custom_directory() ) {
			$this->process_directory();
		}
	}
	
	protected function undo_change_content_directory() {
		if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'itsec-undo-change-content-directory' ) ) {
			$this->show_error( __( 'Unable to undo the change to the content directory due to a failed nonce verification.', 'better-wp-security' ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		$this->change_content_directory( 'wp-content' );
	}
	
	protected function show_redirect_message() {
		if ( empty( $_GET['message'] ) ) {
			return;
		}
		
		if ( false === strpos( $_GET['message'], '|' ) ) {
			$name = $_GET['message'];
		} else {
			list( $name, $arg ) = explode( '|', $_GET['message'], 2 );
		}
		
		if ( 'undo-success' === $name ) {
			echo '<div class="updated fade"><p><strong>' . __( 'The Content Directory change was successfully changed back to <code>wp-content</code>.', 'better-wp-security' ) . "</strong></p></div>\n";
		} else if ( 'change-success' === $name ) {
			echo '<div class="updated fade"><p><strong>' . sprintf( __( 'The Content Directory was successfully changed to <code>%s</code>.', 'better-wp-security' ), $arg ) . "</strong></p></div>\n";
		}
	}
	
	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_advanced_settings() {
		global $itsec_globals;
		
		$this->show_redirect_message();
		
		if ( $this->is_custom_directory() || $this->is_modified_by_it_security() ) {
			$dir_name = substr( WP_CONTENT_DIR, strrpos( WP_CONTENT_DIR, '/' ) + 1 );
			echo '<p>' . sprintf( __( 'The <code>wp-content</code> directory is available at <code>%s</code>.', 'better-wp-security' ), $dir_name ) . '</p>';
			
			if ( $this->is_modified_by_it_security() ) {
				
?>
	<form method="post" action="?page=toplevel_page_itsec_advanced&settings-updated=true" class="itsec-form">
		<?php wp_nonce_field( 'itsec-undo-change-content-directory', 'wp_nonce' ); ?>
		
		<div class="itsec-warning-message"><?php printf( __( '<span>IMPORTANT:</span> Ensure that you <a href="%s">create a database backup</a> before undoing the Content Directory change.', 'better-wp-security' ), admin_url( 'admin.php?page=toplevel_page_itsec_backups' ) ); ?></div>
		<div class="itsec-warning-message"><?php _e( '<span>WARNING:</span> Undoing the Content Directory change when images and other content were added after the change <strong>will break your site</strong>. Only undo the Content Directory change if absolutely necessary.', 'better-wp-security' ); ?></div>
		
		<p class="submit">
			<input type="submit" class="button-primary" name="undo_change_content_directory" value="<?php _e( 'Undo Content Directory Change', 'better-wp-security' ); ?>" />
		</p>
	</form>
<?php
				
			} else {
				echo '<p>' . __( 'No further actions are available on this page.', 'better-wp-security' ) . '</p>';
			}
		} else {

			echo '<p>' . __( 'By default, WordPress stores files for plugins, themes, and uploads in a directory called <code>wp-content</code>. Some older and less intelligent bots hard coded this directory in order to look for vulnerable files. Modern bots are intelligent enough to locate this folder programmatically, thus changing the Content Directory is no longer a recommended security step.', 'better-wp-security' ) . '</p>';
			echo '<p>' . __( 'This tool provides an undo feature after changing the Content Directory. Since not all plugins, themes, or site contents function properly with a renamed Content Directory, please verify that the site is functioning correctly after the change. If any issues are encountered, the undo feature should be used to undo the change. Please note that the undo feature is only available when the changes added to the <code>wp-config.php</code> file for this feature are unmodified.', 'better-wp-security' ) . '</p>';
			echo '<div class="itsec-warning-message">' . __( '<span>IMPORTANT:</span> Deactivating or uninstalling this plugin will not revert the changes made by this feature.', 'better-wp-security' ) . '</div>';
			echo '<div class="itsec-warning-message">' . sprintf( __( '<span>IMPORTANT:</span> Ensure that you <a href="%s">create a database backup</a> before changing the Content Directory.', 'better-wp-security' ), admin_url( 'admin.php?page=toplevel_page_itsec_backups' ) ) . '</div>';
			echo '<div class="itsec-warning-message">' . __( '<span>WARNING:</span> Changing the name of the Content Directory on a site that already has images and other content referencing it <strong>will break your site</strong>. For this reason, we highly recommend only changing the Content Directory on a fresh WordPress install.', 'better-wp-security' ) . '</div>';

			if ( false !== apply_filters( 'itsec_filter_can_write_to_files', false ) ) {

				?>

				<form method="post" action="?page=toplevel_page_itsec_advanced&settings-updated=true" class="itsec-form">

					<?php wp_nonce_field( 'ITSEC_admin_save', 'wp_nonce' ); ?>

					<table class="form-table">
						<tr valign="top">
							<th scope="row" class="settinglabel">
								<label for="itsec_enable_content_dir"><?php _e( 'Enable Change Directory Name', 'better-wp-security' ); ?></label>
							</th>
							<td class="settingfield">
								<input type="checkbox" id="itsec_enable_content_dir" name="itsec_enable_content_dir" value="true"/>

								<p class="description"><?php _e( 'Check this box to enable Content Directory renaming.', 'better-wp-security' ); ?></p>
							</td>
						</tr>
						<tr valign="top" id="content_directory_name_field">
							<th scope="row" class="settinglabel">
								<label for="itsec_content_name"><?php _e( 'Directory Name', 'better-wp-security' ); ?></label>
							</th>
							<td class="settingfield">
								<input id="itsec_content_name" name="name" type="text" value="wp-content"/>

								<p class="description"><?php _e( 'Enter a new directory name to replace "wp-content." You may need to log in again after performing this operation.', 'better-wp-security' ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Change Content Directory', 'better-wp-security' ); ?>"/>
					</p>
				</form>

				<?php

			} else {
				echo '<p>' . sprintf( __( 'You must allow this plugin to write to the wp-config.php file on the <a href="%s">Settings</a> page to use this feature.', 'better-wp-security' ), admin_url( 'admin.php?page=toplevel_page_itsec_settings' ) ) . '</p>';
			}
		}
	}

	public function process_directory() {
		if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'ITSEC_admin_save' ) ) {
			$this->show_error( __( 'Unable to change the Content Directory due to a failed nonce verification.', 'better-wp-security' ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		if ( $this->is_custom_directory() ) {
			$this->show_error( __( 'The <code>wp-content</code> directory has already been renamed. No Directory Name changes have been made.', 'better-wp-security' ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		
		$dir_name = sanitize_file_name( $_POST['name'] );
		
		if ( empty( $dir_name ) ) {
			$this->show_error( __( 'The Directory Name cannot be empty.', 'better-wp-security' ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		if ( 'wp-content' === $dir_name ) {
			$this->show_error( __( 'You have not chosen a new name for wp-content. Nothing was saved.', 'better-wp-security' ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		if ( preg_match( '{^(?:/|\\|[a-z]:)}i', $dir_name ) ) {
			$this->show_error( sprintf( __( 'The Directory Name cannot be an absolute path. Please supply a path that is relative to <code>ABSPATH</code> (<code>%s</code>).', 'better-wp-security' ), ABSPATH ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		
		$this->change_content_directory( $dir_name );
	}
	
	protected function change_content_directory( $dir_name ) {
		if ( 'wp-content' == $dir_name ) {
			$undo = true;
		} else {
			$undo = false;
		}
		
		
		if ( 0 === strpos( WP_CONTENT_DIR, ABSPATH ) ) {
			$old_name = substr( WP_CONTENT_DIR, strlen( ABSPATH ) );
			$new_name = $dir_name;
		} else {
			$old_name = WP_CONTENT_DIR;
			$new_name = ABSPATH . $dir_name;
		}
		
		$old_dir = WP_CONTENT_DIR;
		$new_dir = ABSPATH . $dir_name;
		
		if ( file_exists( $new_dir ) ) {
			if ( $undo ) {
				$this->show_error( sprintf( __( 'A file or directory already exists at <code>%s</code>. The Content Directory change has not been undone. Please remove the existing file or directory and try again.', 'better-wp-security' ), $new_dir ) );
			} else {
				$this->show_error( sprintf( __( 'A file or directory already exists at <code>%s</code>. No Directory Name changes have been made. Please choose a new Directory Name or remove the existing file or directory and try again.', 'better-wp-security' ), $new_dir ) );
			}
			
			$this->show_network_admin_notice();
			
			return false;
		}
		
		
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		
		
		$old_permissions = ITSEC_Lib_Directory::get_permissions( $old_dir );
		$result = rename( $old_dir, $new_dir );
		
		if ( ! $result ) {
			$this->show_error( sprintf( __( 'Unable to rename the <code>%1$s</code> directory to <code>%2$s</code>. This could indicate a file permission issue or that your server does not support the supplied name as a valid directory name. No config file or directory changes have been made.', 'better-wp-security' ), $old_name, $new_name ) );
			$this->show_network_admin_notice();
			
			return;
		}

		// Make sure ITSEC_Core knows it's in a different place
		$itsec_core = ITSEC_Core::get_instance();
		$itsec_core->plugin_file = str_replace( $old_name, $new_name, $itsec_core->get_plugin_file() );


		$new_permissions = ITSEC_Lib_Directory::get_permissions( $new_dir );
		
		if ( is_int( $old_permissions) && is_int( $new_permissions ) && ( $old_permissions != $new_permissions ) ) {
			$result = ITSEC_Lib_Directory::chmod( $new_dir, $old_permissions );
			
			if ( is_wp_error( $result ) ) {
				$this->show_error( sprintf( __( 'Unable to set the permissions of the new Directory Name (<code>%1$s</code>) to match the permissions of the old Directory Name. You may have to manually change the permissions of the directory to <code>%2$s</code> in order for your site to function properly.', 'better-wp-security' ), $new_name, $old_permissions ) );
			}
		}
		
		
		if ( $undo ) {
			$expression = $this->get_wp_config_define_expression();
			$expression = substr( $expression, 0, -1 );
			$expression .= "[\r\n]*|";
			
			$modification_result = ITSEC_Lib_Config_File::remove_from_wp_config( $expression );
		} else {
			$modification = $this->get_wp_config_modification( $new_dir, get_option( 'siteurl' ) . "/$dir_name" );
			
			$modification_result = ITSEC_Lib_Config_File::append_wp_config( $modification, true );
		}
		
		
		if ( is_wp_error( $modification_result ) ) {
			$rename_result = rename( $new_dir, $old_dir );
			
			if ( $rename_result ) {
				ITSEC_Lib_Directory::chmod( $old_dir, $old_permissions );
				
				$this->show_error( sprintf( __( 'Unable to update the <code>wp-config.php</code> file. No directory or config file changes have been made. %1$s (%2$s)', 'better-wp-security' ), $modification_result->get_error_message(), $modification_result->get_error_code() ) );
				
				$this->show_error( sprintf( __( 'In order to change the content directory on your server, you will have to manually change the configuration and rename the directory. Details can be found <a href="%s">here</a>.', 'better-wp-security' ), 'https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder' ) );
			} else {
				$this->show_error( sprintf( __( 'CRITICAL ERROR: The <code>%1$s</code> directory was successfully renamed to the new name (<code>%2$s</code>). However, an error occurred when updating the <code>wp-config.php</code> file to configure WordPress to use the new content directory. iThemes Security attempted to rename the directory back to its original name, but an unknown error prevented the rename from working as expected. In order for your site to function properly, you will either need to manually rename the <code>%2$s</code> directory back to <code>%1$s</code> or manually update the <code>wp-config.php</code> file with the necessary modifications. Instructions for making this modification can be found <a href="%3$s">here</a>.', 'better-wp-security' ), $old_name, $new_name, 'https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder' ) );
				
				$this->show_error( sprintf( __( 'Details on the error that prevented the <code>wp-config.php</code> file from updating is as follows: %1$s (%2$s)', 'better-wp-security' ), $modification_result->get_error_message(), $modification_result->get_error_code() ) );
			}
			
			return;
		}


		$backup = get_site_option( 'itsec_backup' );

		if ( $backup !== false && isset( $backup['location'] ) ) {

			$backup['location'] = str_replace( $old_dir, $new_dir, $backup['location'] );
			update_site_option( 'itsec_backup', $backup );

		}

		$global = get_site_option( 'itsec_global' );

		if ( $global !== false && ( isset( $global['log_location'] ) || isset( $global['nginx_file'] ) ) ) {

			if ( isset( $global['log_location'] ) ) {
				$global['log_location'] = str_replace( $old_dir, $new_dir, $global['log_location'] );
			}

			if ( isset( $global['nginx_file'] ) ) {
				$global['nginx_file'] = str_replace( $old_dir, $new_dir, $global['nginx_file'] );
			}

			update_site_option( 'itsec_global', $global );

		}

		$this->show_network_admin_notice();
		
		if ( $undo ) {
			wp_redirect( admin_url( "admin.php?page={$_GET['page']}&message=undo-success" ) );
		} else {
			wp_redirect( admin_url( "admin.php?page={$_GET['page']}&message=change-success" . urlencode( "|$dir_name" ) ) );
		}
		
		exit();
	}
	
	// TODO: Created from old code. Needs to be rebuilt.
	protected function show_error( $message ) {
		add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, 'error' );
		
		$this->last_error = $message;
	}
	
	// TODO: Created from old code. Needs to be rebuilt.
	protected function show_network_admin_notice() {
		if ( is_multisite() ) {
			if ( empty( $this->last_error ) ) {
				$this->core->show_network_admin_notice( false );
			} else {
				$error_handler = new WP_Error();
				$error_handler->add( 'error', $this->last_error );
				
				$this->core->show_network_admin_notice( $error_handler );
			}
		}
	}

	/**
	 * Adds fields that will be tracked for Google Analytics
	 *
	 * @since 4.0
	 *
	 * @param array $vars tracking vars
	 *
	 * @return array tracking vars
	 */
	public function tracking_vars( $vars ) {

		$vars['content_directory'] = array(
			'enabled' => '0:b',
		);

		return $vars;

	}
	
	protected function is_custom_directory() {
		if ( isset( $GLOBALS['__itsec_content_directory_is_custom_directory'] ) ) {
			return $GLOBALS['__itsec_content_directory_is_custom_directory'];
		}
		
		if ( ABSPATH . 'wp-content' !== WP_CONTENT_DIR ) {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = true;
		} else if ( get_option( 'siteurl' ) . '/wp-content' !== WP_CONTENT_URL ) {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = true;
		} else {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = false;
		}
		
		return $GLOBALS['__itsec_content_directory_is_custom_directory'];
	}
	
	protected function is_modified_by_it_security() {
		if ( ! $this->is_custom_directory() ) {
			return false;
		}
		if ( isset( $this->is_modified_by_it_security ) ) {
			return $this->is_modified_by_it_security;
		}
		
		
		$this->is_modified_by_it_security = false;
		
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		
		$wp_config_file = ITSEC_Lib_Config_File::get_wp_config_file_path();
		
		if ( empty( $wp_config_file ) ) {
			return false;
		}
		
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-file.php' );
		
		$wp_config = ITSEC_Lib_File::read( $wp_config_file );
		
		if ( is_wp_error( $wp_config ) ) {
			return false;
		}
		
		$define_expression = $this->get_wp_config_define_expression();
		
		if ( ! preg_match( $define_expression, $wp_config ) ) {
			return false;
		}
		
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-utility.php' );
		
		$wp_config_without_comments = ITSEC_Lib_Utility::strip_php_comments( $wp_config );
		
		if ( is_wp_error( $wp_config_without_comments ) ) {
			return false;
		}
		
		$define_expression_without_comment = $this->get_wp_config_define_expression( false );
		
		if ( ! preg_match( $define_expression_without_comment, $wp_config_without_comments ) ) {
			return false;
		}
		
		
		$this->is_modified_by_it_security = true;
		
		return true;
	}
}
