<?php
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
 * "Copyright Smackcoders. 2015. All rights reserved".
 ********************************************************************************/

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly
$impObj = new WPImporter_includes_helper();
$nonceKey = $impObj->create_nonce_key();
if (!wp_verify_nonce($nonceKey, 'smack_nonce')) {
	die('You are not allowed to do this operation.Please contact your admin.');
}
$impCheckobj = CallWPImporterObj::checkSecurity();
if ($impCheckobj != 'true') {
	die($impCheckobj);
}

$post = $page = $custompost = $users = $eshop = $settings = $support = $dashboard = $filemanager = $mappingtemplate = $schedulemapping = $export = '';
$active_plugins = get_option('active_plugins');
if(in_array('eshop/eshop.php', $active_plugins)){
	$eshop = true;
}
$custompost = true;
$impCEM = CallWPImporterObj::getInstance();
$get_settings = array();
$get_settings = $impCEM->getSettings();
$requestedModule = sanitize_text_field($_REQUEST['__module']);
$requestedAction = isset($_REQUEST['action']) ? sanitize_text_field($_REQUEST['action']) : '' ;
$mod = isset($requestedModule) ? $requestedModule : '';
$module = $manager = '';
if (is_array($get_settings) && !empty($get_settings)) {
	foreach ($get_settings as $key) {
		$key = true;
	}
}
if (isset($requestedAction) && $requestedAction != '') {
	$action = $requestedAction;
	$$action = 'activate';
} else {
	if (isset($mod) && !empty($mod)) {
		$module_array = array('post', 'page', 'custompost', 'users', 'eshop', 'dashboard');
		if(is_array($module_array) && !empty($module_array)){
		foreach ($module_array as $val) {
			if ($val = $mod) {
				$$mod = 'activate';
				if ($mod != 'filemanager' && $mod != 'schedulemapping' && $mod != 'mappingtemplate' && $mod != 'support' && $mod != 'export' && $mod != 'settings' && $mod != 'dashboard') {
					$module = 'activate';
					$manager = 'deactivate';
					$dashboard = 'deactivate';
				} else {
					if ($mod != 'support' && $mod != 'export' && $mod != 'settings' && $mod != 'dashboard') {
						$manager = 'activate';
						$module = 'deactivate';
						$dashboard = 'deactivate';
					} else {
						if ($mod == 'dashboard') {
							$manager = 'deactivate';
							$module = 'deactivate';
						}
					}
				}
			}
		}
		}
	} else {
		if (!isset($_REQUEST['action'])) {
			$dashboard = 'deactivate';
		}
	}
}
$tab_inc = 1;
$menuHTML = "<nav class='navbar navbar-default' role='navigation'>
   <div>
      <ul class='nav navbar-nav'>
         <li class = '".sanitize_html_class($dashboard)."' >";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'dashboard'), $impObj->baseUrl)) . "'> " . esc_html__('Dashboard', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>
         <li class='dropdown ".sanitize_html_class($module)."'>
            <a href='#'  data-toggle='dropdown'>
               " . esc_html__('Imports', 'wp-ultimate-csv-importer') . "
               <b class='caret'></b>
            </a>
            <ul class='dropdown-menu'>";
$menuHTML .= "<li class= '".sanitize_html_class($post)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'post', 'step' => 'uploadfile'), $impObj->baseUrl)) . "'> " . esc_html__('Post', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";
$menuHTML .= "<li class = '".sanitize_html_class($page)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'page', 'step' => 'uploadfile'), $impObj->baseUrl)) . "'> " . esc_html__('Page', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";
if ($custompost) {
	$menuHTML .= "<li class = '".sanitize_html_class($custompost)."'>";
	$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'custompost', 'step' => 'uploadfile'), $impObj->baseUrl)) . "'> " . esc_html__('Custom Post', 'wp-ultimate-csv-importer') . "</a>";
	$menuHTML .= "</li>";
}
$menuHTML .= "<li class = '".sanitize_html_class($users)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'users', 'step' => 'uploadfile'), $impObj->baseUrl)) . "'> " . esc_html__('Users', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";

if ($eshop) {
	$menuHTML .= "<li class = '".sanitize_html_class($eshop)."'>";
	$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'eshop', 'step' => 'uploadfile'), $impObj->baseUrl)) . "'> " . esc_html__('Eshop', 'wp-ultimate-csv-importer') . "</a>";
	$menuHTML .= "</li>";
}
$menuHTML .= "</ul>
         </li>";
$menuHTML .= "<li class='dropdown ".sanitize_html_class($manager)."'>";
$menuHTML .= "<a href='#'  data-toggle='dropdown'>" . esc_html__('Managers', 'wp-ultimate-csv-importer') . " <b class='caret'></b></a>";
$menuHTML .= "<ul class='dropdown-menu'>";
$menuHTML .= "<li class = '".sanitize_html_class($filemanager)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'filemanager'), $impObj->baseUrl)) . "'> " . esc_html__('File Manager', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";
$menuHTML .= "<li class = '".sanitize_html_class($schedulemapping)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'schedulemapping'), $impObj->baseUrl)) . "'> " . esc_html__('Smart Scheduler', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";
$menuHTML .= "<li class = '".sanitize_html_class($mappingtemplate)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'mappingtemplate'), $impObj->baseUrl)) . "'> " . esc_html__('Templates', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";
$menuHTML .= "</ul>
         </li>";
$menuHTML .= "<li class = '".sanitize_html_class($export)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'export'), $impObj->baseUrl)) . "'> " . esc_html__('Export', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";
$menuHTML .= "<li class=  '".sanitize_html_class($settings)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'settings'), $impObj->baseUrl)) . "'> " . esc_html__('Settings', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";
$menuHTML .= "<li class = '".sanitize_html_class($support)."'>";
$menuHTML .= "<a href='" . esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'support'), $impObj->baseUrl)) . "'> " . esc_html__('Support', 'wp-ultimate-csv-importer') . "</a>";
$menuHTML .= "</li>";
$menuHTML .= "<li><a href=".esc_url('https://www.smackcoders.com/wp-ultimate-csv-importer-pro.html?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic')." target='_blank'>" . esc_html__('Go Pro Now', 'wp-ultimate-csv-importer') . "</a></li>
         <li ><a href=".esc_url('http://demo.smackcoders.com/wp-ultimate-csv-importer/wp-admin/admin.php?page=wp-ultimate-csv-importer-pro/index.php&__module=dashboard')." target='_blank'>" . esc_html__('Try Live Demo Now', 'wp-ultimate-csv-importer') . "</a></li>
      </ul>";
$plugin_version = get_option('ULTIMATE_CSV_IMP_VERSION');
$menuHTML .= "</div>";
$menuHTML .= "<div class='msg' id = 'showMsg' style = 'display:none;'></div>";
$menuHTML .= "<input type='hidden' id='current_url' name='current_url' value='" . get_admin_url() . "admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=" . sanitize_text_field($_REQUEST['__module']) . "&step=uploadfile'/>";
$menuHTML .= "<input type='hidden' name='checkmodule' id='checkmodule' value='" . sanitize_text_field($_REQUEST['__module']) . "' />";
$menuHTML .= "</nav>";
echo $menuHTML;
