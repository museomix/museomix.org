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
$mod = isset($_REQUEST['__module']) ? $_REQUEST['__module'] : '';
$module = $manager = '';
if (is_array($get_settings) && !empty($get_settings)) {
	foreach ($get_settings as $key) {
		$key = true;
	}
}
if (isset($_POST['post_csv']) && $_POST['post_csv'] == 'Import') {
	$dashboard = 'activate';
} else {
	if (isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];

		$$action = 'activate';
	} else {
		if (isset($mod) && !empty($mod)) {
			$module_array = array('post', 'page', 'custompost', 'users', 'eshop', 'dashboard');
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
		} else {
			if (!isset($_REQUEST['action'])) {
				$dashboard = 'deactivate';
			}
		}
	}
}
$tab_inc = 1;

$menuHTML = "<nav class='navbar navbar-default' role='navigation'>
   <div>
      <ul class='nav navbar-nav'>
         <li  class = '{$dashboard}' ><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=dashboard'  >" . __('Dashboard', 'wp-ultimate-csv-importer') . "</a></li>
         <li class='dropdown {$module} '>
            <a href='#'  data-toggle='dropdown'>
               " . __('Imports', 'wp-ultimate-csv-importer') . "
               <b class='caret'></b>
            </a>
            <ul class='dropdown-menu'>
               <li class= '{$post}'><a href= 'admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=post&step=uploadfile'>" . __('Post', 'wp-ultimate-csv-importer') . "</a></li>
               <li class = '{$page}'><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=page&step=uploadfile'>" . __('Page', 'wp-ultimate-csv-importer') . "</a></li>";
if ($custompost) {
               $menuHTML .= "<li class = '{$custompost}'><a href= 'admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=custompost&step=uploadfile'>" . __('Custom Post', 'wp-ultimate-csv-importer') . "</a></li>";
}
	$menuHTML .= "<li class = '{$users}'><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=users&step=uploadfile'>" . __('Users', 'wp-ultimate-csv-importer') . "</a></li>";

if ($eshop) {
	$menuHTML .= "<li class = '{$eshop}'><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=eshop&step=uploadfile'>" . __('Eshop', 'wp-ultimate-csv-importer') . "</a></li>";
}
$menuHTML .= "</ul>
         </li>
         <li class='dropdown {$manager}'>
            <a href='#'  data-toggle='dropdown'>
		" . __('Managers', 'wp-ultimate-csv-importer') . "
		 <b class='caret'></b>
	    </a>
            <ul class='dropdown-menu'>
               <li class = '{$filemanager}'><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=filemanager'>" . __('File Manager', 'wp-ultimate-csv-importer') . "</a></li>
               <li class = '{$schedulemapping}'><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=schedulemapping'>" . __('Smart Scheduler', 'wp-ultimate-csv-importer') . "</a></li>
               <li class = '{$mappingtemplate}'><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=mappingtemplate'>" . __('Templates', 'wp-ultimate-csv-importer') . "</a></li>
            </ul>
         </li>";
$menuHTML .= "<li class = '{$export}'><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=export'>" . __('Export', 'wp-ultimate-csv-importer') . "</a></li>";
$menuHTML .= "<li class=  '{$settings}'><a href='admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=settings'  />" . __('Settings', 'wp-ultimate-csv-importer') . "</a></li>
         <li class = '{$support}'><a href= 'admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=support'>" . __('Support', 'wp-ultimate-csv-importer') . "</a></li>
         
         <li ><a href='https://www.wpultimatecsvimporter.com?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic' target='_blank'>" . __('Go Pro Now', 'wp-ultimate-csv-importer') . "</a></li>
         <li ><a href='http://demo.smackcoders.com/demowpthree/wp-admin/admin.php?page=wp-ultimate-csv-importer-pro/index.php&__module=dashboard' target='_blank'>" . __('Try Live Demo Now', 'wp-ultimate-csv-importer') . "</a></li>
      </ul>";
$plugin_version = get_option('ULTIMATE_CSV_IMP_VERSION');
$menuHTML .= "</div>";
$menuHTML .= "<div class='msg' id = 'showMsg' style = 'display:none;'></div>";
$menuHTML .= "<input type='hidden' id='current_url' name='current_url' value='" . get_admin_url() . "admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=" . $_REQUEST['__module'] . "&step=uploadfile'/>";
$menuHTML .= "<input type='hidden' name='checkmodule' id='checkmodule' value='" . $_REQUEST['__module'] . "' />";

$menuHTML .= "
</nav>";

echo $menuHTML;
