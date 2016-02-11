<?php
/*
Plugin Name: Register IPs
Version: 1.6.1
Description: Logs the IP of the user when they register a new account.
Author: Mika Epstein, Johnny White
Author URI: http://ipstenu.org
Plugin URI: http://halfelf.org/plugins/register-ip-ms

Register IP Copyright 2005 Johnny White
Register IP - MultiSite Copyright 2010-14 Mika Epstein

    This file is part of Register IPs, a plugin for WordPress.

    Register IP - MultiSite is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    Register IP - MultiSite is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WordPress.  If not, see <http://www.gnu.org/licenses/>.

*/

/* Internationlization */
load_plugin_textdomain('signup_ip', false, dirname(plugin_basename(__FILE__)) . '/language');

/* Version Check - This only works on 3.1.x branch! */
global $wp_version;
$exit_msg = "_e('This plugin requires WordPress 3.1 or newer.', signup_ip)";

if(version_compare($wp_version, "3.1", "<"))
{
        exit($exit_msg);
}

function helfripms_log_ip($user_id){
        $ip = $_SERVER['REMOTE_ADDR']; //Get the IP of the person registering
        update_user_meta($user_id, 'signup_ip', $ip); //Add user metadata to the usermeta table
}

// Hook into when the user is registered.
add_action('user_register', 'helfripms_log_ip');

// If you have the permissions to edit users, you can see their IP in their profile.
add_action('edit_user_profile', 'helfripms_show_ip_on_profile');

// Formatting for how it looks on the profile page.
function helfripms_show_ip_on_profile() {
        $user_id = $_GET['user_id'];
?>
        <h3><?php _e('Signup IP Address', 'signup_ip'); ?></h3>
        <p style="text-indent:15px;"><?php
        $ip_address = get_user_meta($user_id, 'signup_ip', true);
        echo $ip_address;
        ?></p>
<?php
}

// Add in a column header
function helfripms_signup_ip($column_headers) {
    $column_headers['signup_ip'] = __('IP Address', 'signup_ip');
    return $column_headers;
}

// Formatting output 
function helfripms_columns($value, $column_name, $user_id) {
        if ( $column_name == 'signup_ip' ) {
                $ip = get_user_meta($user_id, 'signup_ip', true);
                if ($ip != ""){
                        $ret = '<em>'.__($ip, 'signup_ip').'</em>';
                        return $ret;
                } else {
                        $ret = '<em>'.__('None Recorded', 'signup_ip').'</em>';
                        return $ret;
                }
        }
        return $value;
}

if ( is_multisite() ) {
        // Adding in filters for Multi Site installs
        add_filter('wpmu_users_columns', 'helfripms_signup_ip');
} else {
        // Adding in filtersfor Single Site installs
        add_filter('manage_users_columns', 'helfripms_signup_ip');
}

// Adding in action, since it's the same for MS and SS.
add_action('manage_users_custom_column',  'helfripms_columns', 10, 3);		

// donate link on manage plugin page
add_filter('plugin_row_meta', 'helfripms_donate_link', 10, 2);
function helfripms_donate_link($links, $file) {
        if ($file == plugin_basename(__FILE__)) {
                $donate_link = '<a href="https://store.halfelf.org/donate/">Donate</a>';
                $links[] = $donate_link;
        }
        return $links;
}