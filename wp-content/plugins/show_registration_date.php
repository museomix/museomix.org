<?php

/*
Plugin Name: Show Registration Date
Description: Show registration date
Version: 0.1
Author: Ipstenu
Author URI: http://www.ipstenu.org/

        This plugin is free software; you can redistribute it and/or modify
        it under the terms of the GNU General Public License as published by
        the Free Software Foundation; either version 2 of the License, or
        (at your option) any later version.

        This plugin is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
        GNU General Public License for more details.

*/

// Register the column - Registered
function registerdate($columns) {
    $columns['registerdate'] = __('Registered', 'registerdate');
    return $columns;
}
add_filter('manage_users_columns', 'registerdate');

// Display the column content
function registerdate_columns( $value, $column_name, $user_id ) {
        if ( 'registerdate' != $column_name )
           return $value;
        $user = get_userdata( $user_id );
        $registerdate = $user->user_registered;
        //$registerdate = date("Y-m-d", strtotime($registerdate));
        return $registerdate;
}
add_action('manage_users_custom_column',  'registerdate_columns', 10, 3);

function registerdate_column_sortable($columns) {
          $custom = array(
      // meta column id => sortby value used in query
          'registerdate'    => 'registered',
          );
      return wp_parse_args($custom, $columns);
}

add_filter( 'manage_users_sortable_columns', 'registerdate_column_sortable' );

function registerdate_column_orderby( $vars ) {
        if ( isset( $vars['orderby'] ) && 'registerdate' == $vars['orderby'] ) {
                $vars = array_merge( $vars, array(
                        'meta_key' => 'registerdate',
                        'orderby' => 'meta_value'
                ) );
        }

        return $vars;
}

add_filter( 'request', 'registerdate_column_orderby' );
?>