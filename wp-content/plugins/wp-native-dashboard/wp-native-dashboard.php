<?php
/*
Plugin Name: WP Native Dashboard
Plugin URI: http://www.code-styling.de/english/development/wordpress-plugin-wp-native-dashboard-en
Description: You can configure your blog working at administration with different languages depends on users choice and capabilities the admin has been enabled.
Author: Heiko Rabe
Author URI: http://www.code-styling.de/
Version: 1.3.12
Text Domain: wp-native-dashboard

License:
 ==============================================================================
 Copyright 2009-2013 Heiko Rabe  (email : info@code-styling.de)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//avoid direct calls to this file where wp core files not present
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

if (!function_exists('plugins_url')) {
	function plugins_url($plugin) {  return WP_PLUGIN_URL . '/' . dirname(plugin_basename($plugin)); }
}

if (!function_exists('get_site_url')) {
	function get_site_url() { return get_option('site_url'); }
}

if ( !defined('WP_PLUGIN_URL') ) 
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
	
if ( !defined('WP_LANG_DIR') ) 
	define('WP_LANG_DIR', WP_CONTENT_URL.'/languages');

include_once(dirname(__file__).'/language-names.php');
		
function wp_native_dashboard_collect_installed_languages() {
	$installed = array();
	$d = @opendir(WP_LANG_DIR);
	if (!$d) return array('en_US');
	while(false !== ($item = readdir($d))) {
		$f = str_replace("\\", '/', WP_LANG_DIR.'/' . $item);
		if ('.' == $item || '..' == $item)
			continue;
		if (is_file($f)){
			if (preg_match("/^([a-z][a-z]_[A-Z][A-Z]|[a-z][a-z]|[a-z][a-z][a-z]?)\.mo$/", $item, $h)) {
				$installed[] = $h[1];
			}
		}
	}
	closedir($d);
	if (!in_array('en_US', $installed)) $installed[] = 'en_US';
	sort($installed);
	return $installed;
}	

//WP 3.0 compatibility
if(!function_exists('update_user_meta')) {
	function update_user_meta($user_id, $meta_key, $meta_value, $prev_value = '') {
		return update_usermeta($user_id, $meta_key, $meta_value);
	}
}

function wp_native_dashboard_get_name_of($locale) {
	global $wpnd_language_names, $my_wp_native_dashboard;
	list($lang,) = explode('_', $locale);
	$name = isset($wpnd_language_names[$lang]) ? $wpnd_language_names[$lang] : __('-n.a.-', 'wp-native-dashboard');
	$abbr = ($my_wp_native_dashboard->with_abbreviations() ? "&nbsp;<i>($locale)</i>" : ''); 
	return "<b>$name</b>$abbr";
}

function wp_native_dashboard_is_rtl_language($locale) {
	$rtl = array('ar', 'ckb', 'fa', 'he', 'ur', 'ug');
	return in_array(array_shift(split('_',$locale)), $rtl);
}

function wp_native_dashboard_rtl_extension_file_content() {
	return '<?php'."\n".'$text_direction = \'rtl\';'."\n".'?>"';
}
	
function wp_native_dashboard_custom_language_selector() { 
	global $my_wp_native_dashboard;
	if($my_wp_native_dashboard->with_tag_support())
		do_action('login_form'); 
}

class wp_native_dashboard {

	function wp_native_dashboard() {
		//options defaults
		$this->defaults 							= new stdClass;
		$this->defaults->version					= '1.0';
		$this->defaults->installed					= false;
		$this->defaults->enable_login_selector 		= false;
		$this->defaults->enable_login_custom		= false;
		$this->defaults->enable_profile_extension	= false;
		$this->defaults->enable_language_switcher 	= false;
		$this->defaults->enable_adminbar_switcher	= false;
		$this->defaults->enable_locale_abbreviations= false;
		$this->defaults->translate_front_adminbar	= false;
		$this->defaults->cleanup_on_deactivate		= false;
		
		//try to get the options now
		$this->options								= get_option('wp-native-dashboard', $this->defaults);
		
		//compat
		if (!isset($this->options->enable_adminbar_switcher)) $this->options->enable_adminbar_switcher = false;
		if (!isset($this->options->translate_front_adminbar)) $this->options->translate_front_adminbar = false;
		if (!isset($this->options->enable_locale_abbreviations)) $this->options->enable_locale_abbreviations = true;
		if (!isset($this->options->enable_login_custom)) $this->options->enable_login_custom = false;

		//keep it for later use
		//bugfix: for FORCE_SSL_ADMIN / $this->plugin_url = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__));
		$this->plugin_url							= plugins_url(dirname(plugin_basename(__FILE__)));
				
		//detect the current main version
		global $wp_version;
		preg_match("/^(\d+)\.(\d+)(\.\d+|)/", $wp_version, $hits);
		$this->root_tagged_version = $hits[1].'.'.$hits[2];
		$this->tagged_version = $this->root_tagged_version;
		if (!empty($hits[3])) $this->tagged_version .= $hits[3];
		
		//register at plugin activation/deactivation hooks
		register_activation_hook(plugin_basename(__FILE__), array(&$this, 'activate_plugin'));
		register_deactivation_hook(plugin_basename(__FILE__), array(&$this, 'deactivate_plugin'));		
		
		//if the activation has been failed, show it
		$this->_display_version_errors();
		
		add_filter('locale', array(&$this, 'on_locale'), 9999);
		add_action('init', array(&$this, 'on_init'));
		add_action('admin_init', array(&$this, 'on_admin_init'));
		add_action('admin_menu', array(&$this, 'on_admin_menu'));
		//add filter for WordPress 2.8 changed backend box system !
		add_filter('screen_layout_columns', array(&$this, 'on_screen_layout_columns'), 10, 2);
		add_action('admin_post_wp_native_dashoard_save_settings', array(&$this, 'on_save_settings'));
		
		$this->i18n_loaded = false;
		
		global $wp_version;
		$this->no_dashboard_headline = version_compare($wp_version, '3.0', '>=');
		
		$this->user_agent_is_wp_native_dashboard = ($_SERVER["HTTP_USER_AGENT"] == "WP_NATIVE_DASHBOARD");
		
		if ( $this->user_agent_is_wp_native_dashboard ) {
			//suppress all except the frontend admin bar rendering
			ob_start();
			add_action('shutdown', array(&$this, 'on_shutdown_render_admin_bar'), 0);
		}
		
		$this->orig_locale = false;
				
	}
	
	function with_abbreviations() {
		return $this->options->enable_locale_abbreviations;
	}
	
	function with_tag_support() {
		return $this->options->enable_login_custom;
	}
	
	//check required versions
	function _get_version_errors() {
		$res = array();
		global $wp_version;
		if (!version_compare($wp_version, '2.7alpha', '>=')) {
			$res['WordPress Version'] = array('required' => '2.7alpha', 'found' =>  $wp_version);
		}
		if (!version_compare(phpversion(), '4.4.3', '>=')) {
			$res['PHP Version'] = array('required' => '4.4.3', 'found' =>  phpversion());
		}
		return $res;
	}
	
	//display potential install errors
	function _display_version_errors() {
		if (isset($_GET['action']) && isset($_GET['plugin']) && ($_GET['action'] == 'error_scrape') && ($_GET['plugin'] == plugin_basename(__FILE__) )) {
			$version_error = $this->_get_version_errors();
			if (count($version_error) != 0) {
				echo "<table>";
				echo "<tr style=\"font-size: 12px;\"><td><strong style=\"border-bottom: 1px solid #000;\">Plugin can not be activated.</strong></td><td> | required</td><td> | actual</td></tr>";			
				foreach($version_error as $key => $value) {
					echo "<tr style=\"font-size: 12px;\"><td>$key</td><td align=\"center\"> &gt;= <strong>".$value['required']."</strong></td><td align=\"center\"><span style=\"color:#f00;\">".$value['found']."</span></td></tr>";
				}
				echo "</table>";
			}
		}
	}

	//try to activate this plugin and rollback on insufficiant requirements
	function activate_plugin() {
		$version_error = $this->_get_version_errors();
		if (count($version_error) != 0) {
			$current = get_option('active_plugins');
			array_splice($current, array_search(plugin_basename(__FILE__), $current), 1 );
			update_option('active_plugins', $current);
			exit();
		}
		if (!$this->options->installed) {
			$this->options->installed = true;
			add_option('wp-native-dashboard', $this->options);
		}
		
	}
		
	//deactivate the plugin and make a decision what's to cleanup	
	function deactivate_plugin() {
		if ($this->options->cleanup_on_deactivate) {
			delete_option('wp-native-dashboard');
			//cleanup the user dependend settings (cleanup of all profiles affected currently not supported by WP core!)
			//TODO: standardize the USER-META behavoir
			global $wpdb;
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key = %s", 'wp_native_dashboard_language') );
		}
	}
	
	//add the language if different
	function on_admin_bar_page_lang() {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
			'id'     => 'csl-current-locale',
			'parent' => 'top-secondary',
			'title'  => '<i style="font-size: 10px;">'.__("Language", "wp-native-dashboard").': </i>'.wp_native_dashboard_get_name_of($this->orig_locale),
			'meta'   => array( 'class' => '' ),
		) );
	}
	
	//setup the correct user prefered language
	function on_locale($loc) {
		if($this->orig_locale === false) $this->orig_locale = $loc;
		$skip = !$this->options->enable_login_selector && !$this->options->enable_profile_extension && !$this->options->enable_language_switcher && !$this->options->enable_adminbar_switcher;
		if ((is_admin() && !$skip) || ($this->options->translate_front_adminbar && $this->user_agent_is_wp_native_dashboard)) {
			if (function_exists('wp_get_current_user')) {
				$u = wp_get_current_user();
				if (!is_object($u) || $u->ID == 0) return $loc; //bugfix: for ajax based login's
				if (!isset($u->wp_native_dashboard_language)) {
					if ($loc) 
						$u->wp_native_dashboard_language = $loc;
					else
						$u->wp_native_dashboard_language = 'en_US';
				}
				
				if(($u->wp_native_dashboard_language != 'en_US') && !@file_exists(WP_LANG_DIR.'/' . $u->wp_native_dashboard_language.'.mo')) 
					return $loc ? $loc : 'en_US';
				return $u->wp_native_dashboard_language;
			}
		}
		return $loc;
	}
	
	function _load_translation_file() {
		if ($this->i18n_loaded) return; 
		load_plugin_textdomain('wp-native-dashboard', false, dirname( plugin_basename(__FILE__) ) . '/i18n' );
		$this->i18n_loaded = true;
	}
	
	function on_admin_init() { 
		//do all stuff while we are at admin center, 3.6 compatibility
		if (is_admin()) {
			//load the language switcher ajax module if it has been enabled to provide the dropdown extenstion 
			if ($this->options->enable_language_switcher || $this->options->enable_adminbar_switcher) { 
				require_once(dirname(__FILE__).'/langswitcher.php');
				$this->langswitcher = new wp_native_dashboard_langswitcher($this->plugin_url, $this->options->enable_language_switcher || $this->options->enable_adminbar_switcher, $this->options->enable_adminbar_switcher);
				$this->_load_translation_file();
				wp_enqueue_script('jquery');
			}
		}		
	}
	
	function on_init() {
		//some modules need to be loaded here, because they have to support ajax or affect the login page :-)
		//load the login selector module if it has been enabled to provide language choise at login screen
		if ($this->options->enable_login_selector /*&& (is_admin() || defined('DOING_AJAX'))*/) { 
			require_once(dirname(__FILE__).'/loginselector.php');
			$this->loginselector = new wp_native_dashboard_loginselector($this->options->enable_login_custom);
			$this->_load_translation_file();
			if (is_admin()) wp_enqueue_script('jquery');
		}
		if (($this->options->enable_login_selector || $this->options->enable_language_switcher || $this->options->enable_adminbar_switcher) && (is_admin() || defined('DOING_AJAX'))) {
			require_once(dirname(__FILE__).'/automattic.php');
			$this->automattic = new wp_native_dashboard_automattic($this->tagged_version, $this->root_tagged_version);
			$this->_load_translation_file();
			if (is_admin()) wp_enqueue_script('jquery');
		}
		////--------------------------
		//front end admin bar handling
		if($this->options->translate_front_adminbar && !is_admin() && is_user_logged_in() && !$this->user_agent_is_wp_native_dashboard) {
			add_action('wp_before_admin_bar_render', array(&$this, 'on_start_suppress_admin_bar'), 0);
			add_action('wp_after_admin_bar_render', array(&$this, 'on_request_suppressed_admin_bar_translated'),9999);
		}
		if($this->options->translate_front_adminbar && !is_admin() && is_user_logged_in() && $this->user_agent_is_wp_native_dashboard) {
			add_action('admin_bar_menu', array(&$this, 'on_admin_bar_page_lang'), 999 );
			add_action('wp_before_admin_bar_render', array(&$this, 'on_start_capture_admin_bar'), 0);
			add_action('wp_after_admin_bar_render', array(&$this, 'on_end_capture_admin_bar'), 9999);
		}
	}
	
	function on_start_capture_admin_bar() {
		ob_start();
	}
	
	function on_end_capture_admin_bar() {
		$this->frontend_admin_bar = ob_get_clean();
	}
	
	function on_shutdown_render_admin_bar() {
		ob_end_clean();
		echo $this->frontend_admin_bar;
	}
	function on_start_suppress_admin_bar() {
		ob_start();
	}
	
	function on_request_suppressed_admin_bar_translated() {
		ob_end_clean();
		$cookies = array();
		foreach($_COOKIE as $key => $val) {
			 $cookie = new WP_Http_Cookie( $key );
			 $cookie->name = $key;
			 $cookie->value = $val;
			 $cookie->expires = mktime( 0, 0, 0, date('m'), date('d') + 7, date('Y') ); // expires in 7 days
			 $cookie->path = '/';
			 //$cookie->domain = get_site_url();
			 $cookies[] = $cookie;
		}

		$requested_url  = is_ssl() ? 'https://' : 'http://';
		$requested_url .= $_SERVER['HTTP_HOST'];
		$requested_url .= $_SERVER['REQUEST_URI'];

		$response = wp_remote_get(
			$requested_url, 
			array(
				'sslverify' => false,
				'cookies' => $cookies,
				'user-agent' => 'WP_NATIVE_DASHBOARD'
			)
		);
		if (!is_object($response) && isset($response['body']))
			echo $response['body'];
	}
		
	function on_admin_menu() {
		//load the personal profile setting extension if needed
		if ($this->options->enable_profile_extension) { 
			require_once(dirname(__FILE__).'/personalprofile.php');
			$this->langswitcher = new wp_native_dashboard_personalprofile();
		}		
		$this->_load_translation_file();
		//add our own option page, you can also add it to different sections or use your own one
		$this->pagehook = add_options_page(__("Native Dashboard", "wp-native-dashboard" ), __("Native Dashboard", "wp-native-dashboard" ), 'manage_options', 'wp-native-dashboard', array(&$this, 'on_show_page'));
		//register  callback gets call prior your own page gets rendered
		add_action('load-'.$this->pagehook, array(&$this, 'on_load_page'));		
	}

	//for WordPress 2.8 we have to tell, that we could support 2 columns, but currently only set to 1
	function on_screen_layout_columns($columns, $screen) {
		//bugfix: $this->pagehook is not valid because it will be set at hook 'admin_menu' but 
		//multisite pages or user dashboard pages calling different menu an menu hooks!
		if (!defined( 'WP_NETWORK_ADMIN' ) && !defined( 'WP_USER_ADMIN' )) {
			if ($screen == $this->pagehook) {
				$columns[$this->pagehook] = 1;
			}
		}
		return $columns;
	}
	
	function on_save_settings() {
		if (!is_user_logged_in() || !current_user_can('manage_options') )
			wp_die( __('Cheatin&#8217; uh?') );			
		//cross check the given referer
		check_admin_referer('wp_native_dashoard_save_settings');
		//handle here the DB saving of configuration options,
		$this->options = $this->defaults;
		foreach(array_keys(get_object_vars($this->defaults)) as $key) {
			if (isset($_POST[$key])) 
				$this->options->$key = $_POST[$key];
		}
		update_option('wp-native-dashboard', $this->options);
		wp_redirect($_POST['_wp_http_referer']);				
	}
	
	//will be executed if wordpress core detects this page has to be rendered
	function on_load_page() {
		//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		wp_enqueue_script('jquery-ui-dialog');
		
		//  enqueue here your scripts/css needed for page or load some additional data 
		global $text_direction;
		if ($text_direction == 'rtl') 
			wp_enqueue_style('wp-native-dashboard-css-rtl', $this->plugin_url.'/css/style-rtl.css');
		else
			wp_enqueue_style('wp-native-dashboard-css', $this->plugin_url.'/css/style.css');
			
		wp_enqueue_style('wp-native-dashboard-ui', $this->plugin_url.'/css/ui.all.css');

		//add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore				
		add_meta_box('wp-native-dashboard-acl', __('Capabilities', 'wp-native-dashboard'), array(&$this, 'on_print_metabox_content_acl'), $this->pagehook, 'normal', 'core');		
		add_meta_box('wp-native-dashboard-installed-i18n', __('Installed Languages', 'wp-native-dashboard'), array(&$this, 'on_print_metabox_installed_i18n'), $this->pagehook, 'normal', 'core');
		add_meta_box('wp-native-dashboard-download-i18n', __('Downloads', 'wp-native-dashboard').' <small style="font-weight:normal;">(<i>svn.automattic.com</i>)</small>', array(&$this, 'on_print_metabox_automattic_i18n'), $this->pagehook, 'normal', 'core');
		global $wp_version;
		if (version_compare($wp_version, '3.3', '>=')) {
			$screen = get_current_screen();
			//$request = unserialize(csp_fetch_remote_content('http://api.wordpress.org/plugins/info/1.0/codestyling-localization'));
			$screen->add_help_tab(array(
				'title' => __('Overview','wp-native-dashboard'),
				'id' => 'overview',
				'content' => '',
				'callback' => array($this, 'on_callback_help_overview')
			));		
			$screen->add_help_tab(array(
				'title' => __('Template Tags','wp-native-dashboard'),
				'id' => 'template_tags',
				'content' => '',
				'callback' => array($this, 'on_callback_help_template_tags')
			));		
		}
	}
	
	function on_callback_help_overview() {
?>
	<p>
		<strong>WP Native Dashboard </strong> - <em>"<?php _e('... use your backend with your prefered language', "wp-native-dashboard"); ?>"</em>
	</p>
	<p>
	<?php _e('While get in touch with WordPress you will find out, that the initial delivery package comes only with english localization. If you want WordPress to show your native language, you have to provide the appropriated language file at languages folder. This files will be used to replace the english text phrases during the process of page generation. This translation capability has the origin at the gettext functionality which currently been used across a wide range of open source projects.', "wp-native-dashboard"); ?>
	</p>
	<p style="margin-top: 50px;padding-top:10px; border-top: solid 1px #ccc;">
		<small class="alignright" style="position:relative; margin-top: -30px; color: #aaa;">&copy; 2008 - 2013 by Heiko Rabe</small>
		<a href="http://wordpress.org/extend/plugins/wp-native-dashboard/" target="_blank">Plugin Directory</a> | 
		<a href="http://wordpress.org/extend/plugins/wp-native-dashboard/changelog/" target="_blank">Change Logs</a> | 
		<a href="<?php echo $this->plugin_url."/license.txt";?>" target="_blank">License</a> 
		<a class="alignright" href="http://wordpress.org/extend/plugins/codestyling-localization/" target="_blank"><?php _e('Translate WordPress, Themes and Plugins into your Language',"wp-native-dashboard");?></a>
	</p>
<?php
	}	
	
	function on_callback_help_template_tags() {
?>
	<p>
		<strong><?php _e('Template Tags within your Plugins or Themes',"wp-native-dashboard"); ?></strong>
	</p>
	<p>
		<?php _e('If you need the select box from normal login screen within your own login implementation, you can activate the template tags for. Add the following codelines into your widget, template or whereever required.',"wp-native-dashboard"); ?>
	</p>
	<pre>
	&lt;?php 
		if (function_exists('wp_native_dashboard_custom_language_selector')) 
			wp_native_dashboard_custom_language_selector(); 
	?&gt;
	</pre>
<?php
	}

	function on_print_metabox_content_acl($data) {
		?>
			<p>
				<input id="enable_login_selector" type="checkbox" value="1" name="enable_login_selector"<?php if ($this->options->enable_login_selector) echo ' checked="checked"'; ?> />
				<?php _e('extend the <em>WordPress Logon Screen</em> to choose a language too.', "wp-native-dashboard"); ?>
			</p>
			<p style="margin-left: 16px;">
				<input id="enable_login_custom" type="checkbox" value="1" name="enable_login_custom"<?php if ($this->options->enable_login_custom) echo ' checked="checked"'; ?> />
				<?php _e('enable template tag usage of login selector (see help screen).', "wp-native-dashboard"); ?>
			</p>
			<p>
				<input id="enable_profile_extension" type="checkbox" value="1" name="enable_profile_extension"<?php if ($this->options->enable_profile_extension) echo ' checked="checked"'; ?> />
				<?php _e('extend <a href="profile.php" target="_blank">Personal Profile Settings</a> with users prefered language.', "wp-native-dashboard"); ?>
			</p>
			<?php if ($this->no_dashboard_headline == FALSE) : ?>
			<p>
				<input id="enable_language_switcher" type="checkbox" value="1" name="enable_language_switcher"<?php if ($this->options->enable_language_switcher) echo ' checked="checked"'; ?> />
				<?php _e('extend <em>Admin Center Headline</em> with a language quick selector.', "wp-native-dashboard"); ?>
			</p>
			<?php endif; ?>
			<?php if (function_exists('is_admin_bar_showing')) : ?>
			<p>
				<input id="enable_adminbar_switcher" type="checkbox" value="1" name="enable_adminbar_switcher"<?php if ($this->options->enable_adminbar_switcher) echo ' checked="checked"'; ?> />
				<?php _e('extend <em>Backend Admin Bar</em> with a language quick selector.', "wp-native-dashboard"); ?>
			</p>
			<p>
				<input id="translate_front_adminbar" type="checkbox" value="1" name="translate_front_adminbar"<?php if ($this->options->translate_front_adminbar) echo ' checked="checked"'; ?> />
				<?php _e('translate <em>Frontend Admin Bar</em> using backend selected language.', "wp-native-dashboard"); ?>
			</p>
			<p>
				<input id="enable_locale_abbreviations" type="checkbox" value="1" name="enable_locale_abbreviations"<?php if ($this->options->enable_locale_abbreviations) echo ' checked="checked"'; ?> />
				<?php _e('enable locale abbreviations behind the language name.', "wp-native-dashboard"); ?>
			</p>
			<?php endif; ?>
			<p class="csp-read-more">
				<em><a href="javascript:void(0)" onclick="jQuery(this).slideUp();jQuery('#wpf-languages').slideDown();"><?php _e('read more &raquo;', "wp-native-dashboard"); ?></a><span id="wpf-languages" style="display:none;"><?php _e('If you are using one of the current available <a href="http://wordpress.org/extend/plugins/search.php?q=multilingual" target="_blank">multilingual plugins</a>, which permits you writing and publishing posts in several languages, you may also have the need, that native speaking authors should be able to choose their prefered backend language while writing. It\'s your decision if and how this will be possible. This feature set does not impact your frontend language (defined by config or by any multilingual plugin).', "wp-native-dashboard"); ?></span></em>
			</p>
		<?php
	}
	
	function on_print_metabox_installed_i18n() {
		$installed = wp_native_dashboard_collect_installed_languages();
		?>
		<p><?php _e('Your WordPress installation currectly supports this list of languages at your Dashboard.','wp-native-dashboard'); ?></p>
		<table id="table_local_i18n" class="widefat fixed" cellspacing="0">
			<tbody>
				<?php
				$state=0;
				foreach($installed as $lang) {
					$state = ($state + 1) % 2;
					$mo = str_replace('\\','/', WP_LANG_DIR.'/'.$lang.'.mo');
					?>
					<tr id="tr-i18n-installed-<?php echo $lang; ?>" class="<?php if ($state) echo 'alternate'; ?>">
						<td><span class="i18n-file csp-<?php echo $lang; ?>"><?php echo wp_native_dashboard_get_name_of($lang); ?></span></td>
						<td><?php echo (wp_native_dashboard_is_rtl_language($lang) ? __('right to left', 'wp-native-dashboard') : '&nbsp;'); ?></td>
						<td><?php echo (is_file($mo) ? filesize($mo). '&nbsp;Bytes' : '-n.a.-'); ?></td>
						<td><?php if($lang != 'en_US') : ?><a class="csp-delete-local-file" href="<?php echo $mo; ?>"><?php _e('Delete','wp-native-dashboard'); ?></a>&nbsp;<span><img src="images/loading.gif" class="ajax-feedback" title="" alt="" /></span><?php endif; ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php	
	}

	function __list_versions_by_de_de() {
		$url 		= 'http://svn.automattic.com/wordpress-i18n/de_DE/tags/';
		$response = @wp_remote_get($url);
		$error = is_wp_error($response);
		$versions = array();
		if(!$error) {
			$lines = split("\n",$response['body']);
			foreach($lines as $line) {
				if (preg_match("/href\s*=\s*\"(\S+)\/\"/", $line, $hits)) {
					if (in_array($hits[1], array('..', 'http://subversion.tigris.org'))) continue;
					$versions[] = $hits[1];
				}
			}
			sort($versions);
		}
		return array_reverse($versions);
	}
	
	function on_print_metabox_automattic_i18n() {
		global $wp_version;
		$color = '#21759B';
		$perc = 0.0;
		$revision = null;
		?>
		<p><?php echo sprintf(__('A lot of languages should be provided by polyglott translation teams as download into your WordPress installation.','wp-native-dashboard'), $revision); ?></p>
		<p class="csp-read-more center">
			<?php 
				_e('Available for download:', 'wp-native-dashboard'); 
				$versions = $this->__list_versions_by_de_de();
				if(count($versions) > 0) {
					echo "<select id=\"svn_wp_version\" name=\"svn_wp_version\">";
					foreach($versions as $version) {
						echo "<option value=\"$version\"";selected($wp_version, $version);echo">$version</option>";
					}
					echo "</select>";
				}
			?> <a id="csp-check-repository" href="#svn"><?php _e('check repository &raquo;','wp-native-dashboard'); ?></a> <span><img src="images/loading.gif" class="ajax-feedback" title="" alt="" /></span></p>
		<div id="svn-downloads">
			<div class="progressbar" style="display:none;">
				<div class="widget" style="height:12px; border:1px solid #DDDDDD; background-color:#F9F9F9;width:100%; margin: 3px 0;">
					<div class="widget" style="width: <?php echo min($perc, 100.0) ?>%;height:100%;background-color:<?php echo $color; ?>!important;background-image:none;border-width:0px;text-shadow:0 1px 0 #000000;color:#FFFFFF;text-align:right;font-weight:bold;font-size:8px;margin-bottom:4px;"><div style="padding:0 10px 0 0; white-space:nowrap;word-wrap:normal!important;overflow: hidden;"><?php echo $perc; ?>&nbsp;%</div></div>
				</div>
			</div>			
			<table id="table_svn_i18n" class="widefat fixed" cellspacing="0" style="display:none">
				<tbody></tbody>
			</table>
		</div>
		<?php
	}
	
	//executed to show the plugins complete admin page
	function on_show_page() {
		global $screen_layout_columns;
		$data = null;
		?>
		<div id="howto-metaboxes-general" class="wrap">
		<?php screen_icon('wp-native-dashboard'); ?>
		<h2><?php _e("Native Dashboard Settings", "wp-native-dashboard"); ?></h2>
		<form action="admin-post.php" method="post">
			<?php wp_nonce_field('wp_native_dashoard_save_settings'); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			<input type="hidden" name="action" value="wp_native_dashoard_save_settings" />
		
			<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
				<div id="side-info-column" class="inner-sidebar">
					<?php do_meta_boxes($this->pagehook, 'side', $data); ?>
				</div>
				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<?php do_meta_boxes($this->pagehook, 'normal', $data); ?>
						<br/>
						<p class="csp-read-more">
							<span class="alignright csp-copyright">copyright &copy 2008 - 2013 by Heiko Rabe</span>
							<label for="cleanup_on_deactivate" class="alignleft">
								<input id="cleanup_on_deactivate" type="checkbox" value="1" name="cleanup_on_deactivate"<?php if ($this->options->cleanup_on_deactivate) echo ' checked="checked"'; ?> />
								<span class="csp-warning"><?php _e('cleanup all settings at plugin deactivation.', 'wp-native-dashboard'); ?></span>
							</label>
							<br class="clear"/>
						</p>
						<p>
							<input type="submit" value="Save Changes" class="button-primary" name="Submit"/>	
						</p>
					</div>
				</div>
				<br class="clear"/>
			</div>	
		</form>
		</div>
		<div id="csp-credentials"></div>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
		});
		//]]>
	</script>
		
		<?php
	}
}

$my_wp_native_dashboard = new wp_native_dashboard();

?>