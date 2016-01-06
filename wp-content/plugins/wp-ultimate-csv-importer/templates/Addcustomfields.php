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
$filename = isset($_POST['filename']) ? $_POST['filename'] : '';
$count = isset($_POST['corecount']) ? $_POST['corecount'] : '';
$impobj = new WPImporter_includes_helper();
$getrec = $impobj->csv_file_data($filename);
$csvheaders = $impobj->headers;
$returndata = "<tr><td class='left_align' style='width:54.5%; padding-left:150px;'><input type='text' name='addcorefieldname$count' id = 'addcorefieldname$count'/></td>";
$returndata .= "<td class='left_align'> <select name='addcoremapping$count' id='addcoremapping$count' class='uiButton'>";
$returndata .= "<option id = 'select'>-- Select --</option>";
foreach ($csvheaders as $headerkey => $headervalue) {
	$returndata .= "<option value = '$headervalue'>$headervalue</option>";
}
$returndata .= "</select></td>";
$returndata .= "<td></td><td></td></tr>";
print_r($returndata);
die;
