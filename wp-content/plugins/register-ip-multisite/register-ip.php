<?php
/*
Plugin Name: Register IPs
Version: 1.7.1
Description: Logs the IP of the user when they register a new account.
Author: Mika Epstein, Johnny White
Author URI: http://halfelf.org
Plugin URI: http://halfelf.org/plugins/register-ip-ms
Text Domain: register-ip-multisite

	Copyright 2005 Johnny White
	Copyright 2010-16 Mika Epstein (ipstenu@halfelf.org)

    This file is part of Register IPs, a plugin for WordPress.

    Register IPs is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    Register IPs is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WordPress.  If not, see <http://www.gnu.org/licenses/>.

*/

class Register_IP_Multisite {

	/**
	 * Let's get this party started
	 *
	 * @since 1.7
	 * @access public
	 */

    public function __construct() {
        add_action( 'init', array( &$this, 'init' ) );
    }

	/**
	 * All init functions
	 *
	 * @since 1.7
	 * @access public
	 */

    public function init() {
		add_action( 'user_register', array( $this,'log_ip') );
		add_action( 'edit_user_profile', array( $this,'edit_user_profile') );
		add_action( 'manage_users_custom_column', array( $this,'columns'), 10, 3);		
		add_filter( 'plugin_row_meta', array( $this ,'donate_link'), 10, 2 );

		if ( is_multisite() ) {
			add_filter('wpmu_users_columns', array( $this ,'column_header_signup_ip'));
		} else {
			add_filter('manage_users_columns', array( $this ,'column_header_signup_ip'));
		}
	}

	/**
	 * Log the IP address
	 *
	 * @since 1.0
	 * @access public
	 */
	public function log_ip($user_id){
		$ip = $_SERVER['REMOTE_ADDR']; //Get the IP of the person registering
		update_user_meta($user_id, 'signup_ip', $ip); //Add user metadata to the usermeta table
	}

	/**
	 * Show the IP on a profile
	 *
	 * @since 1.0
	 * @access public
	 */
	public function edit_user_profile() {
	        $user_id = (int) $_GET['user_id'];
	?>
	        <h3><?php _e('Signup IP Address', 'register-ip-mutisite'); ?></h3>
	        <p style="text-indent:15px;"><?php
	        $ip_address = get_user_meta($user_id, 'signup_ip', true);
	        echo $ip_address;
	        ?></p>
	<?php
	}

	/**
	 * Column Header
	 *
	 * @since 1.0
	 * @access public
	 */
	public function column_header_signup_ip($column_headers) {
	    $column_headers['signup_ip'] = __('IP Address', 'register-ip-multisite');
	    return $column_headers;
	}
	
	/**
	 * Column Output
	 *
	 * @since 1.0
	 * @access public
	 */
	public function columns($value, $column_name, $user_id) {
        if ( $column_name == 'signup_ip' ) {
            $ip = get_user_meta($user_id, 'signup_ip', true);
            if ($ip != ""){
                $theip = $ip;
				if ( has_filter('ripm_show_ip') ) {
					$theip = apply_filters('ripm_show_ip', $theip);
				}
                return $theip;
            } else {
                $theip = '<em>'.__('None Recorded', 'register-ip-multisite').'</em>';
                return $theip;
            }
        }
	    return $value;
	}
	
	/**
	 * Slap a donate link back into the plugin links. Show some love
	 *
	 * @since 1.0
	 * @access public
	 */
	public function donate_link($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$donate_link = '<a href="https://store.halfelf.org/donate/">Donate</a>';
			$links[] = $donate_link;
		}
		return $links;
	}

}

new Register_IP_Multisite();