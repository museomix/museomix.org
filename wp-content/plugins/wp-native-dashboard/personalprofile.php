<?php

if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

class wp_native_dashboard_personalprofile {
	function wp_native_dashboard_personalprofile() {
		add_action('profile_personal_options', array(&$this, 'on_profile_personal_options'));
		add_action('personal_options_update', array(&$this, 'on_personal_options_update'));
	}
	
	function on_profile_personal_options() {
		?>
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row"><?php _e('Language', 'wp-native-dashboard'); ?></th>
			<td>
				<label>
				<select name="wp_native_dashboard_language">
				<?php
				$langs = wp_native_dashboard_collect_installed_languages();
				
				//TODO: standardize the USER-META behavoir
				$u = wp_get_current_user();
				if (!isset($u->wp_native_dashboard_language)){
					$u->wp_native_dashboard_language = get_locale();
					//persist it now for later update only
					update_user_meta($u->ID, 'wp_native_dashboard_language', $u->wp_native_dashboard_language);
				}
				foreach($langs as $lang) { 
					echo "<option value=\"$lang\"";
					if ($u->wp_native_dashboard_language == $lang) echo ' selected="selected"';
					echo ">".wp_native_dashboard_get_name_of($lang)."</option>"; 
				}						
				?>
				</select>
				<?php _e('Select your prefered language that will be used to show the Admin Center.','wp-native-dashboard'); ?>
				</label>
			</td>
		</tr>		
		</tbody>
		</table>
		<?php
	}
	
	function on_personal_options_update() {
		//TODO: standardize the USER-META behavoir
		$u = wp_get_current_user();
		update_user_meta($u->ID, 'wp_native_dashboard_language', $_POST['wp_native_dashboard_language']);		
	}
}

?>