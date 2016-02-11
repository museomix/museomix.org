<?php

if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

class wp_native_dashboard_loginselector {
	function wp_native_dashboard_loginselector($permit_template_tags) {
		add_action('login_head', array(&$this, 'on_login_head'));
		if ($permit_template_tags)
			add_action('wp_head', array(&$this, 'on_login_head'));
		add_action('login_form', array(&$this, 'on_login_form'));
		add_action('wp_login', array(&$this, 'on_wp_login'));
	}
	
	function on_login_head() {
		?>
		<style type="text/css">
		#wp_native_dashboard_language {padding: 2px;border-width: 1px;border-style: solid;height: 2em;vertical-align:top;margin-top: 2px;font-size:16px;width:100%; }
		#wp_native_dashboard_language option { padding-left: 4px; }
		</style>
		<?php
	}
	
	function on_login_form() {
		?>
		<label><?php _e('Language', 'wp-native-dashboard'); ?></label><br/>
		<select id="wp_native_dashboard_language" name="wp_native_dashboard_language" tabindex="30">
		<?php
		$langs = wp_native_dashboard_collect_installed_languages();
		
		$loc = get_locale();
		foreach($langs as $lang) { 
			echo "<option value=\"$lang\"";
			if ($loc == $lang) echo ' selected="selected"';
			echo ">".wp_native_dashboard_get_name_of($lang)."</option>"; 
		}		
		?>
		</select>
		<br/><br/>
		<?php		
	}
	
	function on_wp_login($who) {
		//TODO: standardize the USER-META behavoir
		global $wp_version;
		$langs = wp_native_dashboard_collect_installed_languages();
		if (!isset($_POST['wp_native_dashboard_language']) || !in_array($_POST['wp_native_dashboard_language'], $langs)) return;
		if (version_compare($wp_version, '3.0', '>=')) {
			$user = get_user_by( 'login', $who );
			update_user_meta((int)$user->ID, 'wp_native_dashboard_language', $_POST['wp_native_dashboard_language']);		
		} else {
			update_usermeta(get_profile('ID', $who), 'wp_native_dashboard_language', $_POST['wp_native_dashboard_language']);		
		}
	}
}

?>