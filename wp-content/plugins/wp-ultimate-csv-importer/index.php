<?php
/******************************
 * Plugin Name: WP Ultimate CSV Importer
 * Description: A plugin that helps to import the data's from a CSV file.
 * Version: 3.8.3
 * Author: smackcoders.com
 * Text Domain: wp-ultimate-csv-importer
 * Domain Path: /languages
 * Plugin URI: http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html
 * Author URI: http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html
 */

/*********************************************************************************
 * WP Ultimate CSV Importer is a Tool for importing CSV for the Wordpress
 * plugin developed by Smackcoder. Copyright (C) 2014 Smackcoders.
 *
 * WP Ultimate CSV Importer is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY WP Ultimate
 * CSV Importer, WP Ultimate CSV Importer DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * WP Ultimate CSV Importer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * WP Ultimate CSV Importer copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2014. All rights reserved".
 ********************************************************************************/
if ( ! defined( 'ABSPATH' ) ) 
        exit; // Exit if accessed directly

$get_debug_mode = get_option('wpcsvfreesettings');
if(isset($get_debug_mode['debug_mode']) && $get_debug_mode['debug_mode'] != 'enable_debug') {
	error_reporting(0);
	ini_set('display_errors', 'Off');
}

@ob_start();
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');
/**
 * To Start Session
 */
function myStartSession() {
	if (!session_id()) {
		session_start();
	}
}
/**
 * To Destroy session
 */
function myEndSession() {
	session_destroy();
}
if ( empty( $GLOBALS['wp_rewrite'] ) )
        $GLOBALS['wp_rewrite'] = new WP_Rewrite();

define('WP_CONST_ULTIMATE_CSV_IMP_URL', 'http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html');
define('WP_CONST_ULTIMATE_CSV_IMP_NAME', 'WP Ultimate CSV Importer');
define('WP_CONST_ULTIMATE_CSV_IMP_SLUG', 'wp-ultimate-csv-importer');
define('WP_CONST_ULTIMATE_CSV_IMP_SETTINGS', 'WP Ultimate CSV Importer');
define('WP_CONST_ULTIMATE_CSV_IMP_VERSION', '3.8.2');
define('WP_CONST_ULTIMATE_CSV_IMP_DIR', WP_PLUGIN_URL . '/' . WP_CONST_ULTIMATE_CSV_IMP_SLUG . '/');
define('WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY', plugin_dir_path(__FILE__));
define('WP_CSVIMP_PLUGIN_BASE', WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY);

if (!class_exists('SkinnyControllerWPCsvFree')) {
	require_once('lib/skinnymvc/controller/SkinnyController.php');
}

add_action('plugins_loaded','load_lang_files');

function load_lang_files(){
$csv_importer_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
load_plugin_textdomain( 'wp-ultimate-csv-importer', false, $csv_importer_dir);
}

require_once('plugins/class.inlineimages.php');
require_once('includes/WPImporter_includes_helper.php');

# Activation & Deactivation 
register_activation_hook(__FILE__, array('WPImporter_includes_helper', 'activate'));
register_deactivation_hook(__FILE__, array('WPImporter_includes_helper', 'deactivate'));

function action_csv_imp_admin_menu() {
	if(!function_exists('wp_get_current_user')) {
		include(ABSPATH . "wp-includes/pluggable.php");
	}
	if(is_multisite()) {
		if ( current_user_can( 'administrator' ) ) { 
			add_menu_page(WP_CONST_ULTIMATE_CSV_IMP_SETTINGS, WP_CONST_ULTIMATE_CSV_IMP_NAME, 'manage_options', __FILE__, array('WPImporter_includes_helper', 'output_fd_page'), WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/icon.png");
		} else if ( current_user_can( 'author' ) || current_user_can( 'editor' ) ) {
			$HelperObj = new WPImporter_includes_helper();
			$settings = $HelperObj->getSettings();
			if(isset($settings['enable_plugin_access_for_author']) && $settings['enable_plugin_access_for_author'] == 'enable_plugin_access_for_author') {
				add_menu_page(WP_CONST_ULTIMATE_CSV_IMP_SETTINGS, WP_CONST_ULTIMATE_CSV_IMP_NAME, '2', __FILE__, array('WPImporter_includes_helper', 'output_fd_page'), WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/icon.png");
			}
		}
	}
	else {
		if ( current_user_can( 'administrator' ) ) {
                        add_menu_page(WP_CONST_ULTIMATE_CSV_IMP_SETTINGS, WP_CONST_ULTIMATE_CSV_IMP_NAME, 'manage_options', __FILE__, array('WPImporter_includes_helper', 'output_fd_page'), WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/icon.png");
                }
		else if ( current_user_can( 'author' ) || current_user_can( 'editor' ) ) {
			$HelperObj = new WPImporter_includes_helper();
			$settings = $HelperObj->getSettings();
			if(isset($settings['enable_plugin_access_for_author']) && $settings['enable_plugin_access_for_author'] == 'enable_plugin_access_for_author') {
				add_menu_page(WP_CONST_ULTIMATE_CSV_IMP_SETTINGS, WP_CONST_ULTIMATE_CSV_IMP_NAME, '2', __FILE__, array('WPImporter_includes_helper', 'output_fd_page'), WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/icon.png");
			}
		} /*else if ( current_user_can( 'administrator' ) ) {
			add_menu_page(WP_CONST_ULTIMATE_CSV_IMP_SETTINGS, WP_CONST_ULTIMATE_CSV_IMP_NAME, 'manage_options', __FILE__, array('WPImporter_includes_helper', 'output_fd_page'), WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/icon.png");
		}*/
	}
}
add_action("admin_menu" , "action_csv_imp_admin_menu"); 

function action_csv_imp_admin_init() {
	if (isset($_REQUEST['page']) && ($_REQUEST['page'] == 'wp-ultimate-csv-importer/index.php' || $_REQUEST['page'] == 'page')) {
		
		// Code for use the global wordpress functons in javascript

	/*	wp_register_script('ultimate-importer-free', plugins_url('js/ultimate-importer-free.js', __FILE__));
		wp_enqueue_script('ultimate-importer-free',plugins_url('js/ultimate-importer-free.js', __FILE__),array('ultimate-importer-free'));
		$pa = array(
				'plugin_url' => plugins_url(),
				'ajax_url'   => admin_url( 'admin-ajax.php'),
				'abspath' => ABSPATH,
			   );
		wp_localize_script( 'ultimate-importer-free', 'csvimporterfree', $pa);*/

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', plugins_url('css/jquery-ui.css', __FILE__));
		wp_register_script('ultimate-importer-js', plugins_url('js/ultimate-importer-free.js', __FILE__));
		wp_enqueue_script('ultimate-importer-js');
		wp_register_script('ultimate-importer-button', plugins_url('js/buttons.js', __FILE__));
		wp_enqueue_script('ultimate-importer-button');
		wp_enqueue_style('ultimate_importer_font_awesome', plugins_url('css/font-awesome.css', __FILE__));
		wp_register_script('jquery-min', plugins_url('js/jquery.js', __FILE__));
		wp_enqueue_script('jquery-min');
		wp_register_script('jquery-widget', plugins_url('js/jquery.ui.widget.js', __FILE__));
		wp_enqueue_script('jquery-widget');
		wp_register_script('jquery-fileupload', plugins_url('js/jquery.fileupload.js', __FILE__));
		wp_enqueue_script('jquery-fileupload');
		wp_register_script('bootstrap-collapse', plugins_url('js/bootstrap-collapse.js', __FILE__));
		wp_enqueue_script('bootstrap-collapse');
		wp_enqueue_style('style', plugins_url('css/style.css', __FILE__));
		wp_enqueue_style('jquery-fileupload', plugins_url('css/jquery.fileupload.css', __FILE__));
		wp_enqueue_style('bootstrap-css', plugins_url('css/bootstrap.css', __FILE__));
		wp_enqueue_style('ultimate-importer-css', plugins_url('css/main.css', __FILE__));
		// For chart js
		wp_enqueue_script('high_chart', plugins_url('js/highcharts.js', __FILE__));
		wp_enqueue_script('export_module', plugins_url('js/exporting.js', __FILE__));
		wp_enqueue_script('pie_chart', plugins_url('js/highcharts-3d.js', __FILE__));
		wp_enqueue_script('dropdown', plugins_url('js/dropdown.js', __FILE__));

		wp_enqueue_script('data', plugins_url('js/dashchart.js', __FILE__));

	}
}

add_action('admin_init', 'action_csv_imp_admin_init');

// Move Pages above Media
function smackcsvfree_change_menu_order( $menu_order ) {
   return array(
       'index.php',
       'edit.php',
       'edit.php?post_type=page',
       'upload.php',
       'wp-ultimate-csv-importer/index.php',
   );
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'smackcsvfree_change_menu_order' );

function firstchart() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_firstchart', 'firstchart');

function shownextrecords() {
	require_once("templates/readfile.php");
	die();
}
add_action('wp_ajax_shownextrecords','shownextrecords');

function uploadfilehandle() {
	require_once("lib/jquery-plugins/uploader.php");
	die();
}
add_action('wp_ajax_uploadfilehandle','uploadfilehandle');

function secondchart() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_secondchart', 'secondchart');

function thirdchart() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_thirdchart', 'thirdchart');


function roundchart() {
	global $wpdb;
	ob_flush();
	require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'lib/skinnymvc/core/base/SkinnyBaseActions.php');
	require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'lib/skinnymvc/core/SkinnyActions.php');

	require_once("modules/dashboard/actions/actions.php");
	?>
	<?php
	$myObj = new DashboardActions(); 

	$content = "<form name='piechart' onload='pieStats();'> <div id ='pieStats' style='height:250px;'>";
	$content .= $myObj->piechart();
	$content .= "</div></form>"; 
	echo $content;
}

function linetwoStats() {
	global $wpdb;
	ob_flush();
	require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'lib/skinnymvc/core/base/SkinnyBaseActions.php');
	require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'lib/skinnymvc/core/SkinnyActions.php');

	require_once("modules/dashboard/actions/actions.php");
	?>
	<?php
	$myObj = new DashboardActions(); 

	$content = "<form name='piechart' onload='pieStats();'> <div id ='lineStats' style='height:250px'>";
	$content .= $myObj->getStatsWithDate();
	$content .= "</div></form>"; 
	echo $content;
}


function wpcsvimporter_add_dashboard_widgets() {

	wp_enqueue_script('dashpiechart', plugins_url('js/dashchart-widget.js', __FILE__));
	wp_enqueue_script('high_chart', plugins_url('js/highcharts.js', __FILE__));
	wp_enqueue_script('export_module', plugins_url('js/exporting.js', __FILE__));
	wp_enqueue_script('pie_chart', plugins_url('js/highcharts-3d.js', __FILE__));
	wp_add_dashboard_widget('wpcsvimporter_dashboard_piehart', 'Ultimate-CSV-Importer-Statistics', 'roundchart',$screen = get_current_screen() , 'advanced' ,'high' );
	wp_add_dashboard_widget('wpcsvimporter_dashboard_linechart', 'Ultimate-CSV-Importer-Activity', 'linetwoStats',$screen = get_current_screen(),'advanced','high');
}

add_action('wp_dashboard_setup', 'wpcsvimporter_add_dashboard_widgets');

/**
 * To Process the Import
 */
function importByRequest() {
	require_once("templates/import.php");
	die;
}
add_action('wp_ajax_importByRequest', 'importByRequest');

/**
 *To translate the alert strings
 */
function trans_alert_str(){
if(isset($_POST['alertmsg']))
echo __($_POST['alertmsg'],'wp-ultimate-csv-importer');
die();

}
add_action('wp_ajax_trans_alert_str' , 'trans_alert_str');

/**
 *
 */
function export_submit(){
	require_once("modules/export/templates/export.php");
	die;
}
add_action('wp_ajax_export_file','export_submit');	

/**
 *
 */
function addcorecustomfd(){
	require_once("templates/Addcustomfields.php");
	die;
}

add_action('wp_ajax_addcorecustomfd','addcorecustomfd');

