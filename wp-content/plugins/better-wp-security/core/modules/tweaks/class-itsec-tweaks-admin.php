<?php

class ITSEC_Tweaks_Admin {

	private
		$settings,
		$core,
		$module_path;

	function run( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_tweaks' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_filter( 'itsec_file_modules', array( $this, 'register_file' ) ); //register tooltip action
		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) ); //initialize admin area

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'save_network_options' ) ); //save multisite options
		}


		add_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
		add_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_litespeed_server_config_modification' ) );
		add_filter( 'itsec_filter_wp_config_modification', array( $this, 'filter_wp_config_modification' ) );
	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * s@since 4.0
	 *
	 * @return void
	 */
	public function add_admin_meta_boxes() {

		$id    = 'tweaks_system';
		$title = __( 'System Tweaks', 'better-wp-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_tweaks_system' ),
			'security_page_toplevel_page_itsec_settings',
			'advanced',
			'core'
		);

		$this->core->add_toc_item(
			array(
				'id'    => $id,
				'title' => $title,
			)
		);

		$id    = 'tweaks_wordpress';
		$title = __( 'WordPress Tweaks', 'better-wp-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_tweaks_wordpress' ),
			'security_page_toplevel_page_itsec_settings',
			'advanced',
			'core'
		);

		$this->core->add_toc_item(
			array(
				'id'    => $id,
				'title' => $title,
			)
		);

		if ( is_multisite() ) {

			$id    = 'tweaks_multisite';
			$title = __( 'Multi-site Tweaks', 'better-wp-security' );

			add_meta_box(
				$id,
				$title,
				array( $this, 'metabox_tweaks_multisite' ),
				'security_page_toplevel_page_itsec_settings',
				'advanced',
				'core'
			);

			$this->core->add_toc_item(
				array(
					'id'    => $id,
					'title' => $title,
				)
			);

		}

	}

	/**
	 * echos Disable Directory Browsing Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_server_directory_browsing() {

		if ( isset( $this->settings['directory_browsing'] ) && $this->settings['directory_browsing'] === true ) {
			$directory_browsing = 1;
		} else {
			$directory_browsing = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_directory_browsing" name="itsec_tweaks[directory_browsing]" value="1" ' . checked( 1, $directory_browsing, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_directory_browsing">' . __( 'Disable Directory Browsing', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Prevents users from seeing a list of files in a directory when no index file is present.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Long URL Strings Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_server_long_url_strings() {

		if ( isset( $this->settings['long_url_strings'] ) && $this->settings['long_url_strings'] === true ) {
			$long_url_strings = 1;
		} else {
			$long_url_strings = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_long_url_strings" name="itsec_tweaks[long_url_strings]" value="1" ' . checked( 1, $long_url_strings, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_long_url_strings">' . __( 'Filter Long URL Strings', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Limits the number of characters that can be sent in the URL. Hackers often take advantage of long URLs to try to inject information into your database.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Filter Non-English Characters Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_server_non_english_characters() {

		if ( isset( $this->settings['non_english_characters'] ) && $this->settings['non_english_characters'] === true ) {
			$non_english_characters = 1;
		} else {
			$non_english_characters = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_non_english_characters" name="itsec_tweaks[non_english_characters]" value="1" ' . checked( 1, $non_english_characters, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_non_english_characters">' . __( 'Filter Non-English Characters', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Filter out non-english characters from the query string. This should not be used on non-english sites and only works when "Filter Suspicious Query String" has been selected.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Protect Files Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_server_protect_files() {

		if ( isset( $this->settings['protect_files'] ) && $this->settings['protect_files'] === true ) {
			$protect_files = 1;
		} else {
			$protect_files = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_protect_files" name="itsec_tweaks[protect_files]" value="1" ' . checked( 1, $protect_files, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_protect_files">' . __( 'Protect System Files', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description"> ' . __( 'Prevent public access to readme.html, readme.txt, wp-config.php, install.php, wp-includes, and .htaccess. These files can give away important information on your site and serve no purpose to the public once WordPress has been successfully installed.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Filter Request MethodsField
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_server_request_methods() {

		if ( isset( $this->settings['request_methods'] ) && $this->settings['request_methods'] === true ) {
			$request_methods = 1;
		} else {
			$request_methods = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_request_methods" name="itsec_tweaks[request_methods]" value="1" ' . checked( 1, $request_methods, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_request_methods">' . __( 'Filter Request Methods', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Filter out hits with the trace, delete, or track request methods.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Filter Suspicious Query Strings Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_server_suspicious_query_strings() {

		if ( isset( $this->settings['suspicious_query_strings'] ) && $this->settings['suspicious_query_strings'] === true ) {
			$suspicious_query_strings = 1;
		} else {
			$suspicious_query_strings = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_suspicious_query_strings" name="itsec_tweaks[suspicious_query_strings]" value="1" ' . checked( 1, $suspicious_query_strings, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_suspicious_query_strings">' . __( 'Filter Suspicious Query Strings in the URL', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'These are very often signs of someone trying to gain access to your site but some plugins and themes can also be blocked.', 'better-wp-security' ) . '</label>';

		echo $content;

	}

	/**
	 * echos Remove write permissions Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_server_write_permissions() {

		if ( isset( $this->settings['write_permissions'] ) && $this->settings['write_permissions'] === true ) {
			$write_permissions = 1;
		} else {
			$write_permissions = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_write_permissions" name="itsec_tweaks[write_permissions]" value="1" ' . checked( 1, $write_permissions, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_write_permissions">' . __( 'Remove File Writing Permissions', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Prevents scripts and users from being able to write to the wp-config.php file and .htaccess file. Note that in the case of this and many plugins this can be overcome however it still does make the files more secure. Turning this on will set the UNIX file permissions to 0444 on these files and turning it off will set the permissions to 0664.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Force Unique Nicename Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_disable_unused_author_pages() {

		if ( isset( $this->settings['disable_unused_author_pages'] ) && $this->settings['disable_unused_author_pages'] === true ) {
			$disable_unused_author_pages = 1;
		} else {
			$disable_unused_author_pages = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_disable_unused_author_pages" name="itsec_tweaks[disable_unused_author_pages]" value="1" ' . checked( 1, $disable_unused_author_pages, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_disable_unused_author_pages"> ' . __( "Disables a user's author page if their post count is 0.", 'better-wp-security' ) . '</label>';
		$content .= '<p class="description"> ' . __( "This makes it harder for bots to determine usernames by disabling post archives for users that don't post to your site.", 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Force Unique Nicename Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_force_unique_nicename() {

		if ( isset( $this->settings['force_unique_nicename'] ) && $this->settings['force_unique_nicename'] === true ) {
			$force_unique_nicename = 1;
		} else {
			$force_unique_nicename = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_force_unique_nicename" name="itsec_tweaks[force_unique_nicename]" value="1" ' . checked( 1, $force_unique_nicename, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_force_unique_nicename"> ' . __( 'Force users to choose a unique nickname', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description"> ' . __( "This forces users to choose a unique nickname when updating their profile or creating a new account which prevents bots and attackers from easily harvesting user's login usernames from the code on author pages. Note this does not automatically update existing users as it will affect author feed urls if used.", 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Disable Login Errors Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_login_errors() {

		if ( isset( $this->settings['login_errors'] ) && $this->settings['login_errors'] === true ) {
			$enabled = 1;
		} else {
			$enabled = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_login_errors" name="itsec_tweaks[login_errors]" value="1" ' . checked( 1, $enabled, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_login_errors"> ' . __( 'Disable login error messages', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description"> ' . __( 'Prevents error messages from being displayed to a user upon a failed login attempt.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Reduce Comment Spam Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_comment_spam() {

		if ( isset( $this->settings['comment_spam'] ) && $this->settings['comment_spam'] === true ) {
			$comment_spam = 1;
		} else {
			$comment_spam = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_comment_spam" name="itsec_tweaks[comment_spam]" value="1" ' . checked( 1, $comment_spam, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_comment_spam">' . __( 'Reduce Comment Spam', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'This option will cut down on comment spam by denying comments from bots with no referrer or without a user-agent identified.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Hide Core Update Notifications Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_core_updates() {

		if ( isset( $this->settings['core_updates'] ) && $this->settings['core_updates'] === true ) {
			$core_updates = 1;
		} else {
			$core_updates = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_core_updates" name="itsec_tweaks[core_updates]" value="1" ' . checked( 1, $core_updates, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_core_updates">' . __( 'Hide Core Update Notifications', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Hides core update notifications from users who cannot update core. Please note that this only makes a difference in multi-site installations.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Disable XML-RPC Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_disable_xmlrpc() {

		if ( isset( $this->settings['disable_xmlrpc'] ) && $this->settings['disable_xmlrpc'] === true ) {

			$setting = 2;

		} elseif ( ! isset( $this->settings['disable_xmlrpc'] ) || ( isset( $this->settings['disable_xmlrpc'] ) && $this->settings['disable_xmlrpc'] === false ) ) {

			$setting = 0;

		} elseif ( isset( $this->settings['disable_xmlrpc'] ) ) {

			$setting = $this->settings['disable_xmlrpc'];

		}

		echo '<p>' . sprintf( __( 'WordPress\'s XML-RPC feature allows external services to access and modify content on the site. Common example of services that make use of XML-RPC are <a href="%1$s">the Jetpack plugin</a>, <a href="%2$s">the WordPress mobile app</a>, and <a href="%3$s">pingbacks</a>. If the site does not use a service that requires XML-RPC, select the "Disable XML-RPC" setting as disabling XML-RPC prevents attackers from using the feature to attack the site.', 'better-wp-security' ), esc_url( 'https://jetpack.me/' ), esc_url( 'https://apps.wordpress.org/' ), esc_url( 'https://make.wordpress.org/support/user-manual/building-your-wordpress-community/trackbacks-and-pingbacks/#pingbacks' ) ) . '</p>';

		echo '<p><select id="itsec_tweaks_server_disable_xmlrpc" name="itsec_tweaks[disable_xmlrpc]">';
		echo '<option value="2" ' . selected( $setting, '2' ) . '>' . __( 'Disable XML-RPC (recommended)', 'better-wp-security' ) . '</option>';
		echo '<option value="1" ' . selected( $setting, '1' ) . '>' . __( 'Disable Pingbacks', 'better-wp-security' ) . '</option>';
		echo '<option value="0" ' . selected( $setting, '0' ) . '>' . __( 'Enable XML-RPC', 'better-wp-security' ) . '</option>';
		echo '</select></p>';

		printf(
			'<ul><li>%s</li><li>%s</li><li>%s</li></ul>',
			__( '<strong>Disable XML-RPC</strong> - XML-RPC is disabled on the site. This setting is highly recommended if Jetpack, the WordPress mobile app, pingbacks, and other services that use XML-RPC are not used.', 'better-wp-security' ),
			__( '<strong>Disable Pingbacks</strong> - Only disable pingbacks. Other XML-RPC features will work as normal. Select this setting if you require features such as Jetpack or the WordPress Mobile app.', 'better-wp-security' ),
			__( '<strong>Enable XML-RPC</strong> - XML-RPC is fully enabled and will function as normal. Use this setting only if the site must have unrestricted use of XML-RPC.', 'better-wp-security' )
		);

	}

	/**
	 * Setting to control whether multiple authentications per XML-RPC request are allowed.
	 *
	 * @since 5.1.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_allow_xmlrpc_multiauth() {
		if ( isset( $this->settings['allow_xmlrpc_multiauth'] ) ) {
			$setting = (bool) $this->settings['allow_xmlrpc_multiauth'];
		} else {
			$setting = true;
		}

		echo '<p>' . sprintf( __( 'WordPress\'s XML-RPC feature allows hundreds of username and password guesses per request. Use the recommended "Block" setting below to prevent attackers from exploiting this feature.', 'better-wp-security' ) ) . '</p>';

		echo '<p><select id="itsec_tweaks_server_allow_xmlrpc_multiauth" name="itsec_tweaks[allow_xmlrpc_multiauth]">';
		echo '<option value="0" ' . selected( $setting, false ) . '>' . __( 'Block (recommended)', 'better-wp-security' ) . '</option>';
		echo '<option value="1" ' . selected( $setting, true ) . '>' . __( 'Allow', 'better-wp-security' ) . '</option>';
		echo '</select></p>';

		echo '<ul>';
		echo '<li>' . __( '<strong>Block</strong> - Blocks XML-RPC requests that contain multiple login attempts. This setting is highly recommended.', 'better-wp-security' ) . '</li>';
		echo '<li>' . __( '<strong>Allow</strong> - Allows XML-RPC requests that contain multiple login attempts. Only use this setting if a service requires it.', 'better-wp-security' ) . '</li>';
		echo '</ul>';
	}

	/**
	 * echos Remove EditURI Header Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_edituri_header() {

		if ( isset( $this->settings['edituri_header'] ) && $this->settings['edituri_header'] === true ) {
			$edituri_header = 1;
		} else {
			$edituri_header = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_edituri_header" name="itsec_tweaks[edituri_header]" value="1" ' . checked( 1, $edituri_header, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_edituri_header">' . __( 'Remove the RSD (Really Simple Discovery) header. ', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Removes the RSD (Really Simple Discovery) header. If you don\'t integrate your blog with external XML-RPC services such as Flickr then the "RSD" function is pretty much useless to you.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Disable File Editor Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_file_editor() {

		if ( isset( $this->settings['file_editor'] ) && $this->settings['file_editor'] === true ) {
			$file_editor = 1;
		} else {
			$file_editor = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_file_editor" name="itsec_tweaks[file_editor]" value="1" ' . checked( 1, $file_editor, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_file_editor">' . __( 'Disable File Editor', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Disables the file editor for plugins and themes requiring users to have access to the file system to modify files. Once activated you will need to manually edit theme and other files using a tool other than WordPress.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Hide Plugin Update Notifications Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_plugin_updates() {

		if ( isset( $this->settings['plugin_updates'] ) && $this->settings['plugin_updates'] === true ) {
			$plugin_updates = 1;
		} else {
			$plugin_updates = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_plugin_updates" name="itsec_tweaks[plugin_updates]" value="1" ' . checked( 1, $plugin_updates, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_plugin_updates">' . __( 'Hide Plugin Update Notifications', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Hides plugin update notifications from users who cannot update plugins. Please note that this only makes a difference in multi-site installations.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Replace jQuery Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_safe_jquery() {

		if ( isset( $this->settings['safe_jquery'] ) && $this->settings['safe_jquery'] === true ) {
			$safe_jquery = 1;
		} else {
			$safe_jquery = 0;
		}

		$raw_version = get_site_option( 'itsec_jquery_version' );
		$is_safe     = ITSEC_Lib::safe_jquery_version() === true;

		if ( $raw_version !== false ) {
			$version = sprintf( __( 'Your current jQuery version is %1$s' ), $raw_version );
		} else {
			$version = sprintf(
				__( 'Your current jQuery version is undetermined. Please <a href="%1$s" target="_blank">check your homepage</a> to see if you even need this feature' ),
				site_url()
			);
		}

		if ( $is_safe === true ) {
			$color = 'green';
		} else {
			$color = 'red';
		}

		if ( $is_safe !== true && $raw_version !== false ) {
			echo '<input type="checkbox" id="itsec_tweaks_wordpress_safe_jquery" name="itsec_tweaks[safe_jquery]" value="1" ' . checked( 1, $safe_jquery, false ) . '/>';
		}

		echo '<label for="itsec_tweaks_wordpress_safe_jquery">' . __( 'Enqueue a safe version of jQuery', 'better-wp-security' ) . '</label>';
		echo '<p class="description">' . __( 'Remove the existing jQuery version used and replace it with a safe version (the version that comes default with WordPress).', 'better-wp-security' ) . '</p>';

		echo '<p class="description" style="color: ' . $color . '">' . $version . '.</p>';
		printf(
			'<p class="description">%s <a href="%s" target="_blank">%s</a> %s</p>',
			__( 'Note that this only checks the homepage of your site and only for users who are logged in. This is done intentionally to save resources. If you think this is in error ', 'better-wp-security' ),
			site_url(),
			__( 'click here to check again.', 'better-wp-security' ),
			__( 'This will open your homepage in a new window allowing the plugin to determine the version of jQuery actually being used. You can then come back here and reload this page to see your version.', 'better-wp-security' )
		);

	}

	/**
	 * echos Hide Theme Update Notifications Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_theme_updates() {

		if ( isset( $this->settings['theme_updates'] ) && $this->settings['theme_updates'] === true ) {
			$theme_updates = 1;
		} else {
			$theme_updates = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_theme_updates" name="itsec_tweaks[theme_updates]" value="1" ' . checked( 1, $theme_updates, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_theme_updates">' . __( 'Hide Theme Update Notifications', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Hides theme update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Disable PHP In Uploads Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_uploads_php() {

		if ( isset( $this->settings['uploads_php'] ) && $this->settings['uploads_php'] === true ) {
			$uploads_php = 1;
		} else {
			$uploads_php = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_uploads_php" name="itsec_tweaks[uploads_php]" value="1" ' . checked( 1, $uploads_php, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_uploads_php">' . __( 'Disable PHP in Uploads', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'Disable PHP execution in the uploads directory. This will prevent uploading of malicious scripts to uploads.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Remove Windows Live Writer Header Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function tweaks_wordpress_wlwmanifest_header() {

		if ( isset( $this->settings['wlwmanifest_header'] ) && $this->settings['wlwmanifest_header'] === true ) {
			$wlwmanifest_header = 1;
		} else {
			$wlwmanifest_header = 0;
		}

		$content = '<input type="checkbox" id="itsec_tweaks_server_wlwmanifest_header" name="itsec_tweaks[wlwmanifest_header]" value="1" ' . checked( 1, $wlwmanifest_header, false ) . '/>';
		$content .= '<label for="itsec_tweaks_server_wlwmanifest_header">' . __( 'Remove the Windows Live Writer header. ', 'better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'This is not needed if you do not use Windows Live Writer or other blogging clients that rely on this file.', 'better-wp-security' ) . '</p>';

		echo $content;

	}

	public function filter_litespeed_server_config_modification( $modification ) {
		return $this->filter_apache_server_config_modification( $modification, 'litespeed' );
	}

	public function filter_apache_server_config_modification( $modification, $server = 'apache' ) {
		$input = get_site_option( 'itsec_tweaks' );

		if ( true === $input['protect_files'] ) {
			$files = array(
				'.htaccess',
				'readme.html',
				'readme.txt',
				'install.php',
				'wp-config.php',
			);

			$modification .= "\n";
			$modification .= "\t# " . __( 'Protect System Files - Security > Settings > System Tweaks > System Files', 'better-wp-security' ) . "\n";

			foreach ( $files as $file ) {
				$modification .= "\t<files $file>\n";

				if ( 'apache' === $server ) {
					$modification .= "\t\t<IfModule mod_authz_core.c>\n";
					$modification .= "\t\t\tRequire all denied\n";
					$modification .= "\t\t</IfModule>\n";
					$modification .= "\t\t<IfModule !mod_authz_core.c>\n";
					$modification .= "\t\t\tOrder allow,deny\n";
					$modification .= "\t\t\tDeny from all\n";
					$modification .= "\t\t</IfModule>\n";
				} else {
					$modification .= "\t\t<IfModule mod_litespeed.c>\n";
					$modification .= "\t\t\tOrder allow,deny\n";
					$modification .= "\t\t\tDeny from all\n";
					$modification .= "\t\t</IfModule>\n";
				}

				$modification .= "\t</files>\n";
			}
		}

		if ( 2 == $input['disable_xmlrpc'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Disable XML-RPC - Security > Settings > WordPress Tweaks > XML-RPC', 'better-wp-security' ) . "\n";
			$modification .= "\t<files xmlrpc.php>\n";

			if ( 'apache' === $server ) {
				$modification .= "\t\t<IfModule mod_authz_core.c>\n";
				$modification .= "\t\t\tRequire all denied\n";
				$modification .= "\t\t</IfModule>\n";
				$modification .= "\t\t<IfModule !mod_authz_core.c>\n";
				$modification .= "\t\t\tOrder allow,deny\n";
				$modification .= "\t\t\tDeny from all\n";
				$modification .= "\t\t</IfModule>\n";
			} else {
				$modification .= "\t\t<IfModule mod_litespeed.c>\n";
				$modification .= "\t\t\tOrder allow,deny\n";
				$modification .= "\t\t\tDeny from all\n";
				$modification .= "\t\t</IfModule>\n";
			}

			$modification .= "\t</files>\n";
		}

		if ( true == $input['directory_browsing'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Disable Directory Browsing - Security > Settings > System Tweaks > Directory Browsing', 'better-wp-security' ) . "\n";
			$modification .= "\tOptions -Indexes\n";
		}


		$rewrites = '';

		if ( true == $input['protect_files'] ) {
			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Protect System Files - Security > Settings > System Tweaks > System Files', 'better-wp-security' ) . "\n";
			$rewrites .= "\t\tRewriteRule ^wp-admin/includes/ - [F]\n";
			$rewrites .= "\t\tRewriteRule !^wp-includes/ - [S=3]\n";
			$rewrites .= "\t\tRewriteCond %{SCRIPT_FILENAME} !^(.*)wp-includes/ms-files.php\n";
			$rewrites .= "\t\tRewriteRule ^wp-includes/[^/]+\.php$ - [F]\n";
			$rewrites .= "\t\tRewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F]\n";
			$rewrites .= "\t\tRewriteRule ^wp-includes/theme-compat/ - [F]\n";
		}

		if ( true === $input['uploads_php'] ) {
			require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-utility.php' );

			$dir = ITSEC_Lib_Utility::get_relative_upload_url_path();

			if ( ! empty( $dir ) ) {
				$dir = preg_quote( $dir );

				$rewrites .= "\n";
				$rewrites .= "\t\t# " . __( 'Disable PHP in Uploads - Security > Settings > System Tweaks > Uploads', 'better-wp-security' ) . "\n";
				$rewrites .= "\t\tRewriteRule ^$dir/.*\.(?:php[1-6]?|pht|phtml?)$ - [NC,F]\n";
			}
		}

		if ( true == $input['request_methods'] ) {
			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Filter Request Methods - Security > Settings > System Tweaks > Request Methods', 'better-wp-security' ) . "\n";
			$rewrites .= "\t\tRewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK) [NC]\n";
			$rewrites .= "\t\tRewriteRule ^.* - [F]\n";
		}

		if ( true == $input['suspicious_query_strings'] ) {
			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Filter Suspicious Query Strings in the URL - Security > Settings > System Tweaks > Suspicious Query Strings', 'better-wp-security' ) . "\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} \.\.\/ [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} ^.*\.(bash|git|hg|log|svn|swp|cvs) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} etc/passwd [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} boot\.ini [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} ftp\:  [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} http\:  [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} https\:  [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|%3D) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} ^.*(%24&x).* [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} ^.*(127\.0).* [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} ^.*(globals|encode|localhost|loopback).* [NC,OR]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} ^.*(request|concat|insert|union|declare).* [NC]\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} !^loggedout=true\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} !^action=jetpack-sso\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} !^action=rp\n";
			$rewrites .= "\t\tRewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in_.*$\n";
			$rewrites .= "\t\tRewriteCond %{HTTP_REFERER} !^http://maps\.googleapis\.com(.*)$\n";
			$rewrites .= "\t\tRewriteRule ^.* - [F]\n";
		}

		if ( true == $input['non_english_characters'] ) {
			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Filter Non-English Characters - Security > Settings > System Tweaks > Non-English Characters', 'better-wp-security' ) . "\n";
			$rewrites .= "\t\tRewriteCond %{QUERY_STRING} ^.*(%0|%A|%B|%C|%D|%E|%F).* [NC]\n";
			$rewrites .= "\t\tRewriteRule ^.* - [F]\n";
		}

		if ( true == $input['comment_spam'] ) {
			$valid_referers = $this->get_valid_referers( 'apache' );

			$rewrites .= "\n";
			$rewrites .= "\t\t# " . __( 'Reduce Comment Spam - Security > Settings > System Tweaks > Comment Spam', 'better-wp-security' ) . "\n";
			$rewrites .= "\t\tRewriteCond %{REQUEST_METHOD} POST\n";
			$rewrites .= "\t\tRewriteCond %{REQUEST_URI} /wp-comments-post\.php\$\n";

			if ( empty( $valid_referers ) || in_array( '*', $valid_referers ) ) {
				$rewrites .= "\t\tRewriteCond %{HTTP_USER_AGENT} ^$\n";
			} else {
				foreach ( $valid_referers as $index => $referer ) {
					if ( '*.' == substr( $referer, 0, 2 ) ) {
						$referer = '([^/]+.)?' . substr( $referer, 2 );
					}

					$referer = str_replace( '.', '\.', $referer );
					$referer = rtrim( $referer, '/' );

					$valid_referers[$index] = $referer;
				}
				$valid_referers = implode( '|', $valid_referers );

				$rewrites .= "\t\tRewriteCond %{HTTP_USER_AGENT} ^$ [OR]\n";
				$rewrites .= "\t\tRewriteCond %{HTTP_REFERER} !^https?://($valid_referers)(/|$) [NC]\n";
			}

			$rewrites .= "\t\tRewriteRule ^.* - [F]\n";
		}

		if ( ! empty( $rewrites ) ) {
			$modification .= "\n";
			$modification .= "\t<IfModule mod_rewrite.c>\n";
			$modification .= "\t\tRewriteEngine On\n";
			$modification .= $rewrites;
			$modification .= "\t</IfModule>\n";
		}


		return $modification;
	}

	public function filter_nginx_server_config_modification( $modification ) {
		$input = get_site_option( 'itsec_tweaks' );

		if ( true === $input['protect_files'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Protect System Files - Security > Settings > System Tweaks > System Files', 'better-wp-security' ) . "\n";
			$modification .= "\tlocation ~ /\.ht { deny all; }\n";
			$modification .= "\tlocation ~ wp-config.php { deny all; }\n";
			$modification .= "\tlocation ~ readme.html { deny all; }\n";
			$modification .= "\tlocation ~ readme.txt { deny all; }\n";
			$modification .= "\tlocation ~ /install.php { deny all; }\n";
			$modification .= "\tlocation ^wp-includes/(.*).php { deny all; }\n";
			$modification .= "\tlocation ^/wp-admin/includes(.*)$ { deny all; }\n";
		}

		if ( 2 == $input['disable_xmlrpc'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Disable XML-RPC - Security > Settings > WordPress Tweaks > XML-RPC', 'better-wp-security' ) . "\n";
			$modification .= "\tlocation ~ xmlrpc.php { deny all; }\n";
		}

		// Rewrite Rules for Disable PHP in Uploads
		if ( true === $input['uploads_php'] ) {
			require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-utility.php' );

			$dir = ITSEC_Lib_Utility::get_relative_upload_url_path();

			if ( ! empty( $dir ) ) {
				$dir = preg_quote( $dir );

				$modification .= "\n";
				$modification .= "\t# " . __( 'Disable PHP in Uploads - Security > Settings > System Tweaks > Uploads', 'better-wp-security' ) . "\n";
				$modification .= "\tlocation ^$dir/(.*).php(.?) { deny all; }\n";
			}
		}

		// Apache rewrite rules for disable http methods
		if ( true == $input['request_methods'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Filter Request Methods - Security > Settings > System Tweaks > Request Methods', 'better-wp-security' ) . "\n";
			$modification .= "\tif (\$request_method ~* \"^(TRACE|DELETE|TRACK)\") { return 403; }\n";
		}

		// Process suspicious query rules
		if ( true == $input['suspicious_query_strings'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Filter Suspicious Query Strings in the URL - Security > Settings > System Tweaks > Suspicious Query Strings', 'better-wp-security' ) . "\n";
			$modification .= "\tset \$susquery 0;\n";
			$modification .= "\tif (\$args ~* \"\\.\\./\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"\.(bash|git|hg|log|svn|swp|cvs)\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"etc/passwd\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"boot.ini\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"ftp:\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"http:\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"https:\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"(<|%3C).*script.*(>|%3E)\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"mosConfig_[a-zA-Z_]{1,21}(=|%3D)\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"base64_encode\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"(%24&x)\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"(127.0)\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"(globals|encode|localhost|loopback)\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args ~* \"(request|insert|concat|union|declare)\") { set \$susquery 1; }\n";
			$modification .= "\tif (\$args !~ \"^loggedout=true\") { set \$susquery 0; }\n";
			$modification .= "\tif (\$args !~ \"^action=jetpack-sso\") { set \$susquery 0; }\n";
			$modification .= "\tif (\$args !~ \"^action=rp\") { set \$susquery 0; }\n";
			$modification .= "\tif (\$http_cookie !~ \"^.*wordpress_logged_in_.*\$\") { set \$susquery 0; }\n";
			$modification .= "\tif (\$http_referer !~ \"^http://maps.googleapis.com(.*)\$\") { set \$susquery 0; }\n";
			$modification .= "\tif (\$susquery = 1) { return 403; } \n";
		}

		// Process filtering of foreign characters
		if ( true == $input['non_english_characters'] ) {
			$modification .= "\n";
			$modification .= "\t# " . __( 'Filter Non-English Characters - Security > Settings > System Tweaks > Non-English Characters', 'better-wp-security' ) . "\n";
			$modification .= "\tif (\$args ~* \"(%0|%A|%B|%C|%D|%E|%F)\") { return 403; }\n";
		}

		// Process Comment spam rules
		if ( true == $input['comment_spam'] ) {
			$valid_referers = $this->get_valid_referers( 'nginx' );

			$modification .= "\n";
			$modification .= "\t# " . __( 'Reduce Comment Spam - Security > Settings > System Tweaks > Comment Spam', 'better-wp-security' ) . "\n";
			$modification .= "\t# " . __( 'Help reduce spam', 'better-wp-security' ) . "\n";
			$modification .= "\tlocation /wp-comments-post.php {\n";
			$modification .= "\t\tlimit_except POST { deny all; }\n";
			$modification .= "\t\tif (\$http_user_agent ~ \"^$\") { return 403; }\n";

			if ( ! empty( $valid_referers ) && ! in_array( '*', $valid_referers ) ) {
				$modification .= "\t\tvalid_referers " . implode( ' ', $valid_referers ) . ";\n";
				$modification .= "\t\tif (\$invalid_referer) { return 403; }\n";
			}

			$modification .= "\t}\n";
		}

		return $modification;
	}

	protected function get_valid_referers( $server_type ) {
		$valid_referers = array();

		if ( 'apache' === $server_type ) {
			$domain = ITSEC_Lib::get_domain( get_site_url() );

			if ( '*' == $domain ) {
				$valid_referers[] = $domain;
			} else {
				$valid_referers[] = "*.$domain";
			}
		} else if ( 'nginx' === $server_type ) {
			$valid_referers[] = 'server_names';
		} else {
			return array();
		}

		$valid_referers[] = 'jetpack.wordpress.com/jetpack-comment/';
		$valid_referers = apply_filters( 'itsec_filter_valid_comment_referers', $valid_referers, $server_type );

		if ( is_string( $valid_referers ) ) {
			$valid_referers = array( $valid_referers );
		} else if ( ! is_array( $valid_referers ) ) {
			$valid_referers = array();
		}

		foreach ( $valid_referers as $index => $referer ) {
			$valid_referers[$index] = preg_replace( '|^https?://|', '', $referer );
		}

		return $valid_referers;
	}

	public function filter_wp_config_modification( $modification ) {
		$input = get_site_option( 'itsec_tweaks', false );

		if ( ! is_array( $input ) ) {
			return $modification;
		}


		if ( isset( $input['file_editor'] ) && $input['file_editor'] ) {
			$modification .= "define( 'DISALLOW_FILE_EDIT', true ); // " . __( 'Disable File Editor - Security > Settings > WordPress Tweaks > File Editor', 'better-wp-security' ) . "\n";
		}

		return $modification;
	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function initialize_admin() {

		add_settings_section(
			'tweaks_server',
			__( 'Configure Server Tweaks', 'better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'tweaks_wordpress',
			__( 'Configure WordPress Tweaks', 'better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'tweaks_multisite',
			__( 'Configure Multisite Tweaks', 'better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		//Add settings fields
		add_settings_field(
			'itsec_tweaks[protect_files]',
			__( 'System Files', 'better-wp-security' ),
			array( $this, 'tweaks_server_protect_files' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_server'
		);

		if ( ITSEC_Lib::get_server() != 'nginx' ) {

			add_settings_field(
				'itsec_tweaks[directory_browsing]',
				__( 'Directory Browsing', 'better-wp-security' ),
				array( $this, 'tweaks_server_directory_browsing' ),
				'security_page_toplevel_page_itsec_settings',
				'tweaks_server'
			);

		}

		add_settings_field(
			'itsec_tweaks[request_methods]',
			__( 'Request Methods', 'better-wp-security' ),
			array( $this, 'tweaks_server_request_methods' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_server'
		);

		add_settings_field(
			'itsec_tweaks[suspicious_query_strings]',
			__( 'Suspicious Query Strings', 'better-wp-security' ),
			array( $this, 'tweaks_server_suspicious_query_strings' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_server'
		);

		add_settings_field(
			'itsec_tweaks[non_english_characters]',
			__( 'Non-English Characters', 'better-wp-security' ),
			array( $this, 'tweaks_server_non_english_characters' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_server'
		);

		add_settings_field(
			'itsec_tweaks[long_url_strings]',
			__( 'Long URL Strings', 'better-wp-security' ),
			array( $this, 'tweaks_server_long_url_strings' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_server'
		);

		add_settings_field(
			'itsec_tweaks[write_permissions]',
			__( 'File Writing Permissions', 'better-wp-security' ),
			array( $this, 'tweaks_server_write_permissions' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_server'
		);

		add_settings_field(
			'itsec_tweaks[uploads_php]',
			__( 'Uploads', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_uploads_php' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_server'
		);

		add_settings_field(
			'itsec_tweaks[wlwmanifest_header]',
			__( 'Windows Live Writer Header', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_wlwmanifest_header' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[edituri_header]',
			__( 'EditURI Header', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_edituri_header' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[comment_spam]',
			__( 'Comment Spam', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_comment_spam' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[file_editor]',
			__( 'File Editor', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_file_editor' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[disable_xmlrpc]',
			__( 'XML-RPC', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_disable_xmlrpc' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[allow_xmlrpc_multiauth]',
			__( 'Multiple Authentication Attempts per XML-RPC Request', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_allow_xmlrpc_multiauth' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[safe_jquery]',
			__( 'Replace jQuery With a Safe Version', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_safe_jquery' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[login_errors]',
			__( 'Login Error Messages', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_login_errors' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[force_unique_nicename]',
			__( 'Force Unique Nickname', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_force_unique_nicename' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		add_settings_field(
			'itsec_tweaks[disable_unused_author_pages]',
			__( 'Disable Extra User Archives', 'better-wp-security' ),
			array( $this, 'tweaks_wordpress_disable_unused_author_pages' ),
			'security_page_toplevel_page_itsec_settings',
			'tweaks_wordpress'
		);

		if ( is_multisite() ) {

			add_settings_field(
				'itsec_tweaks[theme_updates]',
				__( 'Theme Update Notifications', 'better-wp-security' ),
				array( $this, 'tweaks_wordpress_theme_updates' ),
				'security_page_toplevel_page_itsec_settings',
				'tweaks_multisite'
			);

			add_settings_field(
				'itsec_tweaks[plugin_updates]',
				__( 'Plugin Update Notifications', 'better-wp-security' ),
				array( $this, 'tweaks_wordpress_plugin_updates' ),
				'security_page_toplevel_page_itsec_settings',
				'tweaks_multisite'
			);

			add_settings_field(
				'itsec_tweaks[core_updates]',
				__( 'Core Update Notifications', 'better-wp-security' ),
				array( $this, 'tweaks_wordpress_core_updates' ),
				'security_page_toplevel_page_itsec_settings',
				'tweaks_multisite'
			);

		}

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_tweaks',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_tweaks_system() {

		echo '<p>' . __( 'These are advanced settings that may be utilized to further strengthen the security of your WordPress site.', 'better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Note: These settings are listed as advanced because they block common forms of attacks but they can also block legitimate plugins and themes that rely on the same techniques. When activating the settings below, we recommend enabling them one by one to test that everything on your site is still working as expected.', 'better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Remember, some of these settings might conflict with other plugins or themes, so test your site after enabling each setting.', 'better-wp-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'tweaks_server', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes', 'better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_tweaks_wordpress() {

		echo '<p>' . __( 'These are advanced settings that may be utilized to further strengthen the security of your WordPress site.', 'better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Note: These settings are listed as advanced because they block common forms of attacks but they can also block legitimate plugins and themes that rely on the same techniques. When activating the settings below, we recommend enabling them one by one to test that everything on your site is still working as expected.', 'better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Remember, some of these settings might conflict with other plugins or themes, so test your site after enabling each setting.', 'better-wp-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'tweaks_wordpress', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes', 'better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_tweaks_multisite() {

		echo '<p>' . __( 'These are advanced settings that may be utilized to further strengthen the security of your WordPress site.', 'better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Note: These settings are listed as advanced because they block common forms of attacks but they can also block legitimate plugins and themes that rely on the same techniques. When activating the settings below, we recommend enabling them one by one to test that everything on your site is still working as expected.', 'better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Remember, some of these settings might conflict with other plugins or themes, so test your site after enabling each setting.', 'better-wp-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'tweaks_multisite', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes', 'better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Register ban users for file writer
	 *
	 * @param  array $file_modules array of file writer modules
	 *
	 * @return array                   array of file writer modules
	 */
	public function register_file( $file_modules ) {

		$file_modules['tweaks'] = array(
			'rewrite' => array( $this, 'save_rewrite_rules' ),
			'config'  => array( $this, 'save_config_rules' ),
		);

		return $file_modules;

	}

	/**
	 * Sanitize and validate input
	 *
	 * @param  Array $input array of input fields
	 *
	 * @return Array         Sanitized array
	 */
	public function sanitize_module_input( $input ) {

		global $itsec_globals;

		$input['protect_files']               = ( isset( $input['protect_files'] ) && intval( $input['protect_files'] == 1 ) ? true : false );
		$input['directory_browsing']          = ( isset( $input['directory_browsing'] ) && intval( $input['directory_browsing'] == 1 ) ? true : false );
		$input['request_methods']             = ( isset( $input['request_methods'] ) && intval( $input['request_methods'] == 1 ) ? true : false );
		$input['suspicious_query_strings']    = ( isset( $input['suspicious_query_strings'] ) && intval( $input['suspicious_query_strings'] == 1 ) ? true : false );
		$input['non_english_characters']      = ( isset( $input['non_english_characters'] ) && intval( $input['non_english_characters'] == 1 ) ? true : false );
		$input['long_url_strings']            = ( isset( $input['long_url_strings'] ) && intval( $input['long_url_strings'] == 1 ) ? true : false );
		$input['write_permissions']           = ( isset( $input['write_permissions'] ) && intval( $input['write_permissions'] == 1 ) ? true : false );
		$input['wlwmanifest_header']          = ( isset( $input['wlwmanifest_header'] ) && intval( $input['wlwmanifest_header'] == 1 ) ? true : false );
		$input['edituri_header']              = ( isset( $input['edituri_header'] ) && intval( $input['edituri_header'] == 1 ) ? true : false );
		$input['theme_updates']               = ( isset( $input['theme_updates'] ) && intval( $input['theme_updates'] == 1 ) ? true : false );
		$input['plugin_updates']              = ( isset( $input['plugin_updates'] ) && intval( $input['plugin_updates'] == 1 ) ? true : false );
		$input['core_updates']                = ( isset( $input['core_updates'] ) && intval( $input['core_updates'] == 1 ) ? true : false );
		$input['comment_spam']                = ( isset( $input['comment_spam'] ) && intval( $input['comment_spam'] == 1 ) ? true : false );
		$input['file_editor']                 = ( isset( $input['file_editor'] ) && intval( $input['file_editor'] == 1 ) ? true : false );
		$input['disable_xmlrpc']              = isset( $input['disable_xmlrpc'] ) ? intval( $input['disable_xmlrpc'] ) : 0;
		$input['allow_xmlrpc_multiauth']      = isset( $input['allow_xmlrpc_multiauth'] ) ? (bool) $input['allow_xmlrpc_multiauth'] : true;
		$input['uploads_php']                 = ( isset( $input['uploads_php'] ) && intval( $input['uploads_php'] == 1 ) ? true : false );
		$input['safe_jquery']                 = ( isset( $input['safe_jquery'] ) && intval( $input['safe_jquery'] == 1 ) ? true : false );
		$input['login_errors']                = ( isset( $input['login_errors'] ) && intval( $input['login_errors'] == 1 ) ? true : false );
		$input['force_unique_nicename']       = ( isset( $input['force_unique_nicename'] ) && intval( $input['force_unique_nicename'] == 1 ) ? true : false );
		$input['disable_unused_author_pages'] = ( isset( $input['disable_unused_author_pages'] ) && intval( $input['disable_unused_author_pages'] == 1 ) ? true : false );

		if ( ! isset( $this->settings['allow_xmlrpc_multiauth'] ) ) {
			$this->settings['allow_xmlrpc_multiauth'] = null;
		}

		if (
			( $input['protect_files'] !== $this->settings['protect_files'] ||
			  $input['directory_browsing'] !== $this->settings['directory_browsing'] ||
			  $input['request_methods'] !== $this->settings['request_methods'] ||
			  $input['suspicious_query_strings'] !== $this->settings['suspicious_query_strings'] ||
			  $input['non_english_characters'] !== $this->settings['non_english_characters'] ||
			  $input['comment_spam'] !== $this->settings['comment_spam'] ||
			  $input['disable_xmlrpc'] !== $this->settings['disable_xmlrpc'] ||
			  $input['allow_xmlrpc_multiauth'] !== $this->settings['allow_xmlrpc_multiauth'] ||
			  $input['uploads_php'] !== $this->settings['uploads_php']
			) ||
			isset( $itsec_globals['settings']['write_files'] ) && $itsec_globals['settings']['write_files'] === true
		) {

			add_site_option( 'itsec_rewrites_changed', true );

		}

		if ( $input['file_editor'] !== $this->settings['file_editor'] ) {

			add_site_option( 'itsec_config_changed', true );

		}


		if ( $input['write_permissions'] === true ) {
			// Always set permissions to 0444 when saving the settings.
			// This ensures that the file permissions are fixed each time the settings are saved.

			$new_permissions = 0444;
		} else if ( $input['write_permissions'] !== $this->settings['write_permissions'] ) {
			// Only revert the settings to the defaults when disabling the setting.
			// This avoids changing the file permissions when the setting has yet to be enabled and disabled.

			$new_permissions = 0664;
		}

		if ( isset( $new_permissions ) ) {
			// Only change the permissions when needed.

			require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
			require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-file.php' );

			$server_config_file = ITSEC_Lib_Config_File::get_server_config_file_path();
			$wp_config_file = ITSEC_Lib_Config_File::get_wp_config_file_path();

			ITSEC_Lib_File::chmod( $server_config_file, $new_permissions );
			ITSEC_Lib_File::chmod( $wp_config_file, $new_permissions );
		}


		if ( is_multisite() ) {

			$this->settings = $input;

		}

		return $input;

	}

	/**
	 * Prepare and save options in network settings
	 *
	 * @return void
	 */
	public function save_network_options() {

		if ( isset( $_POST['itsec_tweaks'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'better-wp-security' ) );
			}

			update_site_option( 'itsec_tweaks', $_POST['itsec_tweaks'] ); //we must manually save network options

		}

	}

	/**
	 * Add header for server tweaks
	 *
	 * @return void
	 */
	public function server_tweaks_intro() {

		echo '<h2 class="settings-section-header">' . __( 'Server Tweaks', 'better-wp-security' ) . '</h2>';
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

		$vars['itsec_tweaks'] = array(
			'protect_files'               => '0:b',
			'directory_browsing'          => '0:b',
			'request_methods'             => '0:b',
			'suspicious_query_strings'    => '0:b',
			'non_english_characters'      => '0:b',
			'long_url_strings'            => '0:b',
			'write_permissions'           => '0:b',
			'uploads_php'                 => '0:b',
			'wlwmanifest_header'          => '0:b',
			'edituri_header'              => '0:b',
			'comment_spam'                => '0:b',
			'file_editor'                 => '0:b',
			'disable_xmlrpc'              => '0:b',
			'allow_xmlrpc_multiauth'      => '0:b',
			'core_updates'                => '0:b',
			'plugin_updates'              => '0:b',
			'theme_updates'               => '0:b',
			'safe_jquery'                 => '0:b',
			'login_errors'                => '0:b',
			'force_unique_nicename'       => '0:b',
			'disable_unused_author_pages' => '0:b',
		);

		return $vars;

	}

	/**
	 * Add header for WordPress Multisite tweaks
	 *
	 * @return void
	 */
	public function wordpress_multisite_tweaks_intro() {

		echo '<h2 class="settings-section-header">' . __( 'Multisite Tweaks', 'better-wp-security' ) . '</h2>';
	}

	/**
	 * Add header for WordPress tweaks
	 *
	 * @return void
	 */
	public function wordpress_tweaks_intro() {

		echo '<h2 class="settings-section-header">' . __( 'WordPress Tweaks', 'better-wp-security' ) . '</h2>';
	}

}
