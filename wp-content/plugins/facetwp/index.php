<?php
/*
Plugin Name: FacetWP
Description: Advanced Filtering for WordPress
Version: 2.6.5
Author: FacetWP, LLC
Author URI: https://facetwp.com/

Copyright 2016 FacetWP, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

defined( 'ABSPATH' ) or exit;

class FacetWP
{

    public $ajax;
    public $facet;
    public $helper;
    public $indexer;
    public $display;
    private static $instance;


    function __construct() {

        // setup variables
        define( 'FACETWP_VERSION', '2.6.5' );
        define( 'FACETWP_DIR', dirname( __FILE__ ) );
        define( 'FACETWP_URL', plugins_url( '', __FILE__ ) );
        define( 'FACETWP_BASENAME', plugin_basename( __FILE__ ) );

        // get the gears turning
        include( FACETWP_DIR . '/includes/class-updater.php' );
        include( FACETWP_DIR . '/includes/class-init.php' );
    }


    /**
     * Singleton
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}


function FWP() {
    return FacetWP::instance();
}


$facetwp = FWP();
