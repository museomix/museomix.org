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

class WPImporter_includes_helper {

	public $baseUrl;

	public function __construct() {
		$this->getKeyVals();
		$this->baseUrl = admin_url('admin.php');
	}

	// @var string CSV upload directory name
	public $uploadDir = 'ultimate_importer';

	// @var boolean post title check
	public $titleDupCheck = false;

	// @var boolean content title check
	public $conDupCheck = false;

	// @var boolean for post flag
	public $postFlag = true;

	// @var int duplicate post count
	public $dupPostCount = 0;

	// @var int inserted post count
	public $insPostCount = 0;

	// @var int no post author count
	public $noPostAuthCount = 0;

	// @var int updated post count
	public $updatedPostCount = 0;

	// @var string delimiter
	public $delim = ",";

	// @var array delilimters supported by CSV importer
	public $delim_avail = array(',', ';');

	// @var array wp field keys
	public $keys = array();

	// @var Multi images
	public $MultiImages = false;

	// @var array for default columns
	public $defCols = array('post_title' => null, 'post_content' => null, 'post_excerpt' => null, 'post_date' => null, 'post_name' => null, 'post_author' => null, 'featured_image' => null, 'post_parent' => 0, 'post_status' => 0, 'menu_order' => 0, 'post_format' => 0, 'wp_page_template' => null,);

	// @var array CSV headers
	public $headers = array();

	public $capturedId = 0;

	public $detailedLog = array();

	/* getImportDataConfiguration */
	public function getImportDataConfiguration() {
		$importDataConfig = "<div class='importstatus'id='importallwithps_div'>
			<table><tr><td>
			<label id='importalign'>" . __('Import with post status', 'wp-ultimate-csv-importer') . "</label><span class='mandatory'> *</span></td><td>
			<div style='float:left;margin-right:10px;'>
			<select name='importallwithps' id='importallwithps' onChange='selectpoststatus();' >
			<option value='0'>Status as in CSV</option>
			<option value='1'>Publish</option>
			<option value='2'>Sticky</option>
			<option value='3'>Private</option>
			<option value='6'>Protected</option>
			<option value='4'>Draft</option>
			<option value='5'>Pending</option>
			</select></div>
			<div style='float:right;'>
			<a href='#' class='tooltip'>
			<img src='" . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . 'images/help.png')."' />
			<span class='tooltipPostStatus'>
			<img class='callout' src='" . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . 'images/callout.gif')."' />
			" . __('Select the status for the post  imported, if not defined within your csv .E.g.publish', 'wp-ultimate-csv-importer') . "
			<img src='" . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . 'images/help.png')."' style='margin-top: 6px;float:right;' />
			</span></a> </div>
			</td></tr><tr><td>
			<div id='globalpassword_label' class='globalpassword' style='display:none;'><label>" . __('Password', 'wp-ultimate-csv-importer') . "</label><span class='mandatory'> *</span></div></td><td>
			<div id='globalpassword_text' class='globalpassword' style='display:none;'><input type = 'text' id='globalpassword_txt' name='globalpassword_txt' placeholder=" . __('Password for all post', 'wp-ultimate-csv-importer') . "></div></td></tr></table>
			</div>";
		return $importDataConfig;
	}

	/**
	 * Get upload directory
	 */
	public function getUploadDirectory($check = 'plugin_uploads') {
		$upload_dir = wp_upload_dir();
		if ($check == 'plugin_uploads') {
			return $upload_dir ['basedir'] . "/" . $this->uploadDir;
		} else {
			return $upload_dir ['basedir'];
		}
	}

	/**
	 *    generate help tooltip
	 * @param string $content ** content to show on tooltip **
	 * @return string $html ** generated HTML **
	 **/
	public function generatehelp($content, $mapping_style = NULL) {
		$html = '<div style = "' . $mapping_style . '"> <a href="#" class="tooltip">
			<img src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/help.png").'" />
			<span class="tooltipPostStatus">
			<img class="callout" src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/callout.gif").'" />
			' . $content . '
			<img src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/help.png").'" style="margin-top: 6px;float:right;" />
			</span> </a> </div>';
		return $html;
	}

	public static function output_fd_page() {
		$get_pluginData = get_plugin_data(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'index.php');
		$plugin_version = get_option('ULTIMATE_CSV_IMPORTER_UPGRADE_FREE_VERSION');
		if (!$plugin_version) {
			$plugin_version = get_option('ULTIMATE_CSV_IMP_FREE_VERSION');
		}
		if ($get_pluginData['Version'] == '3.6' && $plugin_version == '') {
			if (file_exists(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . '/upgrade/migrationfreev3.6.php')) {
				require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . '/upgrade/migrationfreev3.6.php');
				die();
			}
		} else {
			if (!isset($_REQUEST['__module'])) {
				if (!isset($_REQUEST['__module'])) {
					wp_redirect(get_admin_url() . 'admin.php?page=' . WP_CONST_ULTIMATE_CSV_IMP_SLUG . '/index.php&__module=dashboard');
					exit();
				}
			}
		}
		require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'config/settings.php');
		require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'lib/skinnymvc/controller/SkinnyController.php');

		$c = new SkinnyControllerWPCsvFree;
		$c->main();
	}

	public function getSettings() {
		return get_option('wpcsvfreesettings');
	}

	public function renderMenu() {
		include(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'templates/menu.php');
	}

	public function requestedAction($action, $step) {
		$actions = array('dashboard', 'settings', 'help', 'users', 'comments', 'eshop', 'wpcommerce', 'woocommerce', 'categories', 'customtaxonomy', 'export', 'mappingtemplate');
		if (!in_array($action, $actions)) {
			include(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'templates/view.php');
		} else {
			include(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'modules/' . $action . '/actions/actions.php');
			include(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'modules/' . $action . '/templates/view.php');
		}
	}

	/**
	 * Move CSV to the upload directory
	 */
	public function move_file() {
		if ($_FILES ["csv_import"] ["error"] == 0) {
			$tmp_name = $_FILES ["csv_import"] ["tmp_name"];
			$this->csvFileName = $_FILES ["csv_import"] ["name"];
			move_uploaded_file($tmp_name, $this->getUploadDirectory() . "/$this->csvFileName");
		}
	}

	/**
	 * Check upload dirctory permission
	 */
	function checkUploadDirPermission() {
		$this->getUploadDirectory();
		$upload_dir = wp_upload_dir();
		if (!is_dir($upload_dir ['basedir'])) {
			print " <div style='font-size:16px;margin-left:20px;margin-top:25px;'>" . $this->t("UPLOAD_PERMISSION_ERROR") . "
				</div><br/>
				<div style='margin-left:20px;'>
				<form class='add:the-list: validate' method='post' action=''>
				<input type='submit' class='button-primary' name='Import Again' value='" . $this->t("IMPORT_AGAIN") . "'/>
				</form>
				</div>";
			$this->freeze();
		} else {
			if (!is_dir($this->getUploadDirectory())) {
				wp_mkdir_p($this->getUploadDirectory());
			}
		}
	}

	/**
	 * Get field colum keys
	 */
	function getKeyVals() {
		$cust_fields = '';
		$acf_field = array();
		$wpcsvfreesettings = array();
		global $wpdb;
		$active_plugins = get_option('active_plugins');
		$limit = ( int )apply_filters('postmeta_form_limit', 150);
		$this->keys = $wpdb->get_col("SELECT meta_key FROM $wpdb->postmeta
				GROUP BY meta_key
				HAVING meta_key NOT LIKE '\_%' and meta_key NOT LIKE 'field_%'
				ORDER BY meta_key
				LIMIT $limit");

		foreach ($this->keys as $val) {
			$this->defCols ["CF: " . $val] = $val;
		}
		$wpcsvfreesettings = get_option('wpcsvfreesettings');
		if (in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins)) {
			$seo_custoFields = array('SEO: keywords', 'SEO: description', 'SEO: title', 'SEO: noindex', 'SEO: nofollow', 'SEO: titleatr', 'SEO: menulabel', 'SEO: disable', 'SEO: disable_analytics', 'SEO: canonical_url','SEO: noodp', 'SEO: noydir');
			if(is_array($seo_custoFields)){
			foreach ($seo_custoFields as $val) {
				$this->defCols[$val] = $val;
			}
			}
		}
		$taxo = get_taxonomies();
		foreach ($taxo as $taxokey => $taxovalue) {
			if($taxokey != 'link_category' && $taxokey != 'nav_menu' && $taxokey != 'post_format'){
				$get_taxo_label = get_taxonomy( $taxokey );
				if($taxokey == 'post_tag'){
					$taxo_label = 'post_tag';
					$taxokey = $taxo_label;
				}
				else if($taxokey == 'category'){
					$taxo_label = 'post_category';
					$taxokey = $taxo_label;
				}
				else {
					$taxo_label = $get_taxo_label->labels->name;
				}
				$this->defCols["TERMS:" .$taxo_label] = $taxokey;
			}
		} 
	}

	/**
	 * Function converts CSV data to formatted array.
	 * @param $file CSV input filename
	 * @param $delim delimiter for the CSV
	 * @return array formatted CSV output as array
	 */
	function csv_file_data($file,$startlimit,$endlimit = null) {
		$file = $this->getUploadDirectory() . '/' . $file;
		require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'includes/SmackCSVParser.php');
		if(file_exists($file)){
			$csv = new SmackCSVParser();
			$data = $csv->parseCSV($file,$startlimit,$endlimit);
			foreach ($data as $hkey => $hval) {
				foreach ($hval as $hk => $hv) {
					$this->headers[] = $hk;
				}
				break;
			}
			return $data;
		}else{
			return false;
		}
	}
	function csv_file_readdata($file,$path,$delim) {
                $data_rows = array();
                $path = $this->getUploadDirectory() . '/' . $file;
		require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'includes/SmackCSVParser.php');
                $csv = new SmackCSVParser();
                $csv->parseCSV($path,1,1);
                $delim = $csv->delimiter;
                # Check whether file is present in the given file location
                $fileexists = file_exists($path);
                if ($fileexists) {
                        $resource = fopen($path, 'r');
                        $init = 0;
                        while ($keys = fgetcsv($resource, '', $delim, '"')) {
                                if ($init == 0) {
                                        $this->headers = $keys;
                                } else {
                                        if (!(($keys[0] == null) && (count($keys) == 1))) {
                                                array_push($data_rows, $keys);
                                        }
                                }
                                $init++;
                        }
                        fclose($resource);
                        ini_set("auto_detect_line_endings", false);
                } else {
			return false;
                }
                return $data_rows;
        }

	function get_availgroups($module) {
		$groups = array();
		if ($module == 'post' || $module == 'page' || $module == 'custompost' || $module == 'eshop') {
			$groups = array('','core','addcore','seo');
		}
		if ($module == 'users') {
			$groups = array('');
		}
		return $groups;
	}

	/**
	 * Manage duplicates
	 *
	 * @param string type = (title|content), string content
	 * @return boolean
	 */
	function duplicateChecks($type = 'title', $text, $gettype, $currentLimit, $postTitle) {
		global $wpdb;
		if ($type == 'content') {
			$htmlDecode = html_entity_decode($text);
			$strippedText = strip_tags($htmlDecode);
			$contentLength = strlen($strippedText);
			$allPosts_count = $wpdb->get_results($wpdb->prepare("SELECT COUNT(ID) as count FROM $wpdb->posts WHERE post_type = %s and post_status in(%s,%s,%s,%s,%s)",$gettype,'publish','future','draft','pending','private'));
			$allPosts_count = $allPosts_count[0]->count;
			$allPosts = $wpdb->get_results($wpdb->prepare("SELECT ID,post_title,post_date,post_content FROM $wpdb->posts WHERE post_type = %s and post_status in(%s,%s,%s,%s,%s)",$gettype,'publish','future','draft','pending','private'));
			foreach ($allPosts as $allPost) {
				$htmlDecodePCont = html_entity_decode($allPost->post_content);
				$strippedTextPCont = strip_tags($htmlDecodePCont);
				similar_text($strippedTextPCont, $strippedText, $p);
				if ($p == 100) {
					$this->dupPostCount++;
					$this->detailedLog[$currentLimit]['verify_here'] = "Post-content Already Exists. It can't be imported.";
					return false;
				}
			}
			return true;
		} else {
			if ($type == 'title') {
				$post_exist = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_type = %s and post_status in(%s,%s,%s,%s,%s)",$text,$gettype,'publish','future','draft','pending','private'));
				if (!(count($post_exist) == 0 && ($text != null || $text != ''))) {
					$this->dupPostCount++;
					$this->detailedLog[$currentLimit]['verify_here'] = "Post-title Already Exists. It can't be imported.";
					return false;
				}
				return true;
			} else {
				if ($type == 'title && content') {
					$post_exist = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_content = %s and post_status in(%s,%s,%s,%s,%s)",$postTitle,$text,'publish','future','draft','pending','private'));
					if (!(count($post_exist) == 0 && ($text != null || $text != ''))) {
						$this->dupPostCount++;
						$this->detailedLog[$currentLimit]['verify_here'] = "Post-title and post-content Already Exists. It can't be imported.";
						return false;
					}
					return true;
				}
			}
		}
	}

	/**
	 * function to fetch the featured image from remote URL
	 * @param $f_img
	 * @param $fimg_path
	 * @param $fimg_name
	 * @param $post_slug_value
	 * @param $currentLimit
	 * @param string $logObj
	 */
	public static function get_fimg_from_URL($f_img, $fimg_path, $fimg_name, $post_slug_value, $currentLimit = null, $logObj = "") {
		$f_img = str_replace(" ", "%20", $f_img);
		if ($fimg_path != "" && $fimg_path) {
			$fimg_path = $fimg_path . "/" . $fimg_name;
		}
		$ch = curl_init($f_img);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$rawdata = curl_exec($ch);
		if (strpos($rawdata, 'Not Found') != 0) {
			$rawdata = false;
		}
		if ($rawdata == false) {
			if ($logObj == '') {
				$logObj->detailedLog[$currentLimit]['image'] = "<b>" . __('Image', 'wp-ultimate-csv-importer') . " -</b> " . __('host not resolved', 'wp-ultimate-csv-importer');
			} else {
				$logObj->detailedLog[$currentLimit]['image'] = "<b>" . __('Image', 'wp-ultimate-csv-importer') . " -</b>" . __('host not resolved', 'wp-ultimate-csv-importer');
			}
		} else {
			if (file_exists($fimg_path)) {
				unlink($fimg_path);
			}
			$fp = fopen($fimg_path, 'x');
			fwrite($fp, $rawdata);
			fclose($fp);
			$logObj->detailedLog[$currentLimit]['image'] = "<b>" . __('Image', 'wp-ultimate-csv-importer') . " -</b>" . $fimg_name;
		}
		curl_close($ch);
		return $fimg_name;
	}

	/**
	 * function to map the csv file and process it
	 *
	 * @param $data_rows
	 * @param $ret_array
	 * @param $session_arr
	 * @param $currentLimit
	 * @param $extractedimagelocation
	 * @param $importinlineimageoption
	 * @param null $sample_inlineimage_url
	 * @param bool $useexistingimages
	 *
	 * @return boolean
	 */
	function processDataInWP($data_rows, $ret_array, $session_arr, $currentLimit, $extractedimagelocation, $importinlineimageoption, $sample_inlineimage_url = null, $useexistingimages = false) {
		global $wpdb;
		$post_id = '';
		$new_post = array();
		$smack_taxo = array();
		$custom_array = array();
		$seo_custom_array = array();
		$imported_feature_img = array();
		$headr_count = $ret_array['h2'];
		for ($i = 0; $i <= $ret_array['basic_count']; $i++) {
			if (array_key_exists('corefieldname' . $i, $ret_array)) {
				if ($ret_array['coremapping' . $i] != '-- Select --' && $ret_array['coremapping' . $i] != '') {
					$mappedindex = str_replace('CF: ', '', $ret_array['corefieldname' . $i]);
					if (array_key_exists($ret_array['coremapping' . $i], $data_rows)) {
						$new_post[$mappedindex] = $data_rows[$ret_array['coremapping' . $i]];
					}
				}
			} else {
				if (array_key_exists('seofieldname' . $i, $ret_array)) {
					if ($ret_array['seomapping' . $i] != '-- Select --' && $ret_array['seomapping' . $i] != '') {
						$mappedindex = str_replace('SEO: ', '', $ret_array['seofieldname' . $i]);
						if (array_key_exists($ret_array['seomapping' . $i], $data_rows)) {
							$seo_custom_array[$mappedindex] = $data_rows[$ret_array['seomapping' . $i]];
						}
					}
				} else {
					if (array_key_exists('addcorefieldname' . $i, $ret_array)) {
						if ($ret_array['addcoremapping' . $i] != '-- Select --' && $ret_array['addcoremapping' . $i] != '') {
							if (array_key_exists($ret_array['addcoremapping' . $i], $data_rows)) {
								$custom_array[$ret_array['addcorefieldname' . $i]] = $data_rows[$ret_array['addcoremapping' . $i]];
							}
						}

					} else {
						if (array_key_exists('mapping' . $i, $ret_array)) {
							if ($ret_array ['mapping' . $i] != '-- Select --') {
								if (array_key_exists($ret_array['mapping' . $i], $data_rows)) {
									$new_post[$ret_array['fieldname' . $i]] = $data_rows[$ret_array['mapping' . $i]];
								}
							}
						} else {
							if (array_key_exists('termfieldname' . $i, $ret_array)){
								if($ret_array['term_mapping' . $i] != '-- Select --' && $ret_array['term_mapping' . $i] != ''){
									$mappedindex = str_replace('TERMS: ', '', $ret_array['termfieldname' . $i]);
									if (array_key_exists($ret_array['term_mapping' . $i], $data_rows)) {
										$new_post[$mappedindex] = $data_rows[$ret_array['term_mapping' . $i]];
									}
								}
							}

						}

					}
				}
			}
		}
		for ($inc = 0; $inc < count($data_rows); $inc++) {
			foreach ($this->keys as $k => $v) {
				if (array_key_exists($v, $new_post)) {
					$custom_array [$v] = $new_post [$v];
				}
			}
		}
		if (is_array($new_post)) {
			foreach ($new_post as $ckey => $cval) {
				$this->postFlag = true;
				$taxo = get_taxonomies();
				foreach ($taxo as $taxokey => $taxovalue) {
					if ($taxokey != 'category' && $taxokey != 'link_category' && $taxokey != 'post_tag' && $taxokey != 'nav_menu' && $taxokey != 'post_format') {
						if ($taxokey == $ckey) {
							$smack_taxo [$ckey] = $new_post [$ckey];
						}
					}
				}

				$taxo_check = 0;
				if (!isset($smack_taxo[$ckey])) {
					$smack_taxo [$ckey] = null;
					$taxo_check = 1;
				}
				if ($ckey != 'post_category' && $ckey != 'post_tag' && $ckey != 'featured_image' && $ckey != $smack_taxo [$ckey] && $ckey != 'wp_page_template') {
					if ($taxo_check == 1) {
						unset($smack_taxo[$ckey]);
						$taxo_check = 0;
					}
					if (array_key_exists($ckey, $custom_array)) {
						$darray [$ckey] = $new_post [$ckey];
					} else {
						if (array_key_exists($ckey, $smack_taxo)) {
							$data_array[$ckey] = null;
						} else {
							$data_array[$ckey] = $new_post [$ckey];
						}
					}
				} else {
					switch ($ckey) {
						case 'post_tag' :
							$tags [$ckey] = $new_post [$ckey];
							break;
						case 'post_category' :
							$categories [$ckey] = $new_post [$ckey];
							break;
						case 'wp_page_template' :
							$custom_array['_wp_page_template'] = $new_post [$ckey];
							break;
						case 'featured_image' :
							require_once(ABSPATH . "wp-includes/pluggable.php");
							require_once(ABSPATH . 'wp-admin/includes/image.php');
							$dir = wp_upload_dir();
							$get_media_settings = get_option('uploads_use_yearmonth_folders');
							if ($get_media_settings == 1) {
								$dirname = date('Y') . '/' . date('m');
								$full_path = $dir ['basedir'] . '/' . $dirname;
								$baseurl = $dir ['baseurl'] . '/' . $dirname;
							} else {
								$full_path = $dir ['basedir'];
								$baseurl = $dir ['baseurl'];
							}

							$f_img = $new_post [$ckey];
							$fimg_path = $full_path;

							$fimg_name = @basename($f_img);
							$fimg_name = str_replace(' ', '-', trim($fimg_name));
							$fimg_name = preg_replace('/[^a-zA-Z0-9._\-\s]/', '', $fimg_name);
							$fimg_name = urlencode($fimg_name);
							$parseURL = parse_url($f_img);
							$path_parts = pathinfo($f_img);
							if (!isset($path_parts['extension'])) {
								$fimg_name = $fimg_name . '.jpg';
							}
							$f_img_slug = '';
							#$featured_image = str_replace(' ', '-', trim($path_parts['filename']));
							$featured_image = trim($path_parts['filename']);
							$f_img_slug = strtolower(str_replace(' ', '-', trim($f_img_slug)));
							$f_img_slug = preg_replace('/[^a-zA-Z0-9._\-\s]/', '', $f_img_slug);
							$post_slug_value = strtolower($f_img_slug);
							#if (array_key_exists('extension', $path_parts)) {
							if ($useexistingimages == 'false') {
								$fimg_name = wp_unique_filename($fimg_path, trim($fimg_name));
							}
							$this->get_fimg_from_URL($f_img, $fimg_path, $fimg_name, $post_slug_value, $currentLimit, $this);
							$filepath = $fimg_path . "/" . $fimg_name;
							if (@getimagesize($filepath)) {
								$img = wp_get_image_editor($filepath);
								$file ['guid'] = $baseurl . "/" . $fimg_name;
								$file ['post_title'] = $featured_image;
								$file ['post_content'] = '';
								$file ['post_status'] = 'attachment';
							} else {
								$file = false;
							}
							break;
					}
				}
			}
		}

		if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'] != 'custompost') {
			$data_array['post_type'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'];
		} else {
			$data_array['post_type'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['custompostlist'];
		}
		if ($this->titleDupCheck == 'true') {
			$this->postFlag = $this->duplicateChecks('title', $data_array ['post_title'], $data_array ['post_type'], $currentLimit, $data_array ['post_title']);
		}

		if ($this->conDupCheck == 'true' && $this->postFlag) {
			$this->postFlag = $this->duplicateChecks('content', $data_array ['post_content'], $data_array ['post_type'], $currentLimit, $data_array ['post_title']);
		}

		if ($this->titleDupCheck == 'true' && $this->conDupCheck == 'true') {
			$this->postFlag = $this->duplicateChecks('title && content', $data_array ['post_content'], $data_array ['post_type'], $currentLimit, $data_array ['post_title']);
		}

		// Date format post
		if (!isset($data_array ['post_date'])) {
                       $data_array ['post_date'] = current_time('Y-m-d H:i:s');
                       $this->detailedLog[$currentLimit]['postdate'] = "<b>" . __('Date', 'wp-ultimate-csv-importer') . " - </b>" . $data_array ['post_date'];
                } else {
                       if(strtotime($data_array ['post_date'])){
                               $data_array ['post_date'] = date('Y-m-d H:i:s', strtotime($data_array ['post_date']));
                               $this->detailedLog[$currentLimit]['postdate'] = "<b>" . __('Date', 'wp-ultimate-csv-importer') . " - </b>" . $data_array ['post_date'];
                       }
                       else {
                               $data_array ['post_date'] = current_time('Y-m-d H:i:s');
                               $this->detailedLog[$currentLimit]['postdate'] = "<b>" . __('Date', 'wp-ultimate-csv-importer') . " - </b>" . $data_array ['post_date'] . ' . Unformatted date so current date was replaced.';
                       }
               }
		if (isset($data_array ['post_slug'])) {
			$data_array ['post_name'] = $data_array ['post_slug'];
		}


		if ($this->postFlag) {
			unset ($sticky);
			if (empty($data_array['post_status'])) {
				$data_array['post_status'] = null;
			}

			if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['importallwithps'] != 0) {
				$data_array['post_status'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['importallwithps'];
			}

			switch ($data_array ['post_status']) {
				case 1 :
					$data_array['post_status'] = 'publish';
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('publish', 'wp-ultimate-csv-importer');
					break;
				case 2 :
					if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'] == 'post') {
						$data_array['post_status'] = 'publish';
						$sticky = true;
						$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('sticky', 'wp-ultimate-csv-importer');
					} else {
						$data_array['post_status'] = 'publish';
						$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('publish', 'wp-ultimate-csv-importer');
					}
					break;
				case 3 :
					$data_array ['post_status'] = 'private';
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('private', 'wp-ultimate-csv-importer');
					break;
				case 4 :
					$data_array ['post_status'] = 'draft';
					$data_array['post_date_gmt'] = $data_array['post_date'];
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('draft', 'wp-ultimate-csv-importer');
					break;
				case 5 :
					$data_array ['post_status'] = 'pending';
					$data_array['post_date_gmt'] = $data_array['post_date'];
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('pending', 'wp-ultimate-csv-importer');
					break;
				default :
					$poststatus_pwd = $data_array['post_status'];
					$poststatus = $data_array['post_status'] = strtolower($data_array['post_status']);
					if ($data_array['post_status'] == 'pending') {
						$data_array['post_status'] = 'pending';
						$data_array['post_date_gmt'] = $data_array['post_date'];
						$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('pending', 'wp-ultimate-csv-importer');
					}
					if ($data_array['post_status'] == 'draft') {
						$data_array['post_status'] = 'draft';
						$data_array['post_date_gmt'] = $data_array['post_date'];
						$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('draft', 'wp-ultimate-csv-importer');
					}
					if ($data_array['post_status'] == 'publish') {
						$data_array['post_status'] = 'publish';
						$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('publish', 'wp-ultimate-csv-importer');
					}
					if ($data_array['post_status'] == 'private') {
						$data_array['post_status'] = 'private';
						$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('private', 'wp-ultimate-csv-importer');
					}

					if ($data_array['post_status'] != 'publish' && $data_array['post_status'] != 'private' && $data_array['post_status'] != 'draft' && $data_array['post_status'] != 'pending' && $data_array['post_status'] != 'sticky') {
						$stripPSF = strpos($data_array['post_status'], '{');
						if ($stripPSF === 0) {
							$poststatus = substr($poststatus_pwd, 1);
							$stripPSL = substr($poststatus, -1);
							if ($stripPSL == '}') {
								$postpwd = substr($poststatus, 0, -1);
								$data_array['post_status'] = 'publish';
								$data_array ['post_password'] = $postpwd;
								if (strlen($postpwd) !=0)
									$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('protected with password', 'wp-ultimate-csv-importer');
								else
									$this->detailedLog[$currentLimit]['poststatus'] = "<b>". __('Status','csv-import')." - </b>".__('publish','csv-import');
							} else {
								$data_array['post_status'] = 'publish';
								$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('publish', 'wp-ultimate-csv-importer');
							}
						} else {
							$data_array['post_status'] = 'publish';
							$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('publish', 'wp-ultimate-csv-importer');
						}
					}
					if ($data_array['post_status'] == 'sticky') {
						if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'] == 'post') {
							$data_array['post_status'] = 'publish';
							$sticky = true;
							$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('sticky', 'wp-ultimate-csv-importer');
						} else {
							$data_array['post_status'] = 'publish';
							$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('publish', 'wp-ultimate-csv-importer');
						}

					}
			}
			// Post Format Options
			if (isset($data_array ['post_format'])) {
				$post_format = 0;
				switch ($data_array ['post_format']) {
					case 1 :
						$post_format = 'post-format-aside';
						break;
					case 2 :
						$post_format = 'post-format-image';
						break;
					case 3 :
						$post_format = 'post-format-video';
						break;
					case 4 :
						$post_format = 'post-format-audio';
						break;
					case 5 :
						$post_format = 'post-format-quote';
						break;
					case 6 :
						$post_format = 'post-format-link';
						break;
					case 7 :
						$post_format = 'post-format-gallery';
						break;
					default :
						if ($data_array['post_format'] == 'post-format-aside') {
							$post_format = 'post-format-aside';
							break;
						}
						if ($data_array['post_format'] == 'post-format-image') {
							$post_format = 'post-format-image';
							break;
						}
						if ($data_array['post_format'] == 'post-format-video') {
							$post_format = 'post-format-video';
							break;
						}
						if ($data_array['post_format'] == 'post-format-audio') {
							$post_format = 'post-format-audio';
							break;
						}
						if ($data_array['post_format'] == 'post-format-quote') {
							$post_format = 'post-format-quote';
							break;
						}
						if ($data_array['post_format'] == 'post-format-link') {
							$post_format = 'post-format-link';
							break;
						}
						if ($data_array['post_format'] == 'post-format-gallery') {
							$post_format = 'post-format-gallery';
							break;
						}
						$post_format = 0;
				}
			}

			// Author name/id update
			if (isset($data_array ['post_author'])) {
				$authorLen = strlen($data_array ['post_author']);
				$postuserid = $data_array ['post_author'];
				$checkpostuserid = intval($data_array ['post_author']);
				$postAuthorLen = strlen($checkpostuserid);
				$postauthor = array();

				if ($authorLen == $postAuthorLen) {
					$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where ID = %d",$postuserid));
					if (empty($postauthor) || !$postauthor[0]->ID) { 
						$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where user_login = %s",$postuserid));
					}
				} else {
					$postauthor = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where user_login = %s",$postuserid));
				}

				if (empty($postauthor) || !$postauthor[0]->ID) {
					$data_array ['post_author'] = 1;
					$admindet = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where ID = %d",1));
					$this->detailedLog[$currentLimit]['assigned_author'] = "<b>" . __('Author - not found (assigned to', 'wp-ultimate-csv-importer') . " </b>" . $admindet[0]->user_login . ")";
					$this->noPostAuthCount++;
				} else {
					$data_array ['post_author'] = $postauthor [0]->ID;
					$this->detailedLog[$currentLimit]['assigned_author'] = "<b>" . __('Author', 'wp-ultimate-csv-importer') . " - </b>" . $postauthor[0]->user_login;
				}
			} else {
				$data_array ['post_author'] = 1;
				$admindet = $wpdb->get_results($wpdb->prepare("select ID,user_login from $wpdb->users where ID = %d",1));
				$this->detailedLog[$currentLimit]['assigned_author'] = "<b>" . __('Author - not found (assigned to', 'wp-ultimate-csv-importer') . " </b>" . $admindet[0]->user_login . ")";
				$this->noPostAuthCount++;
			}

			//add global password
			if ($data_array) {
				if ($ret_array['importallwithps'] == 6) {
					$data_array['post_password'] = $ret_array['globalpassword_txt'];
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>" . __('Status', 'wp-ultimate-csv-importer') . " - </b>" . __('protected with password', 'wp-ultimate-csv-importer') . $ret_array['globalpassword_txt'];
				}
			}
			if ($data_array) {
				if ($this->MultiImages == 'true') { // Inline image import feature by fredrick marks
					$inlineImagesObj = new WPImporter_inlineImages();
					$postid = wp_insert_post($data_array);
					$post_id = $inlineImagesObj->importwithInlineImages($postid, $currentLimit, $data_array, $this, $importinlineimageoption, $extractedimagelocation, $sample_inlineimage_url, $useexistingimages);
				} else {
					/* Check post parent is exist or not */
					if(isset($data_array['post_parent']) && isset($data_array['post_type']) && $data_array['post_type'] == 'page'){
						global $wpdb;
						$postparent = $data_array['post_parent'];
						if(is_numeric($postparent)){
							$get_data = $wpdb->get_results($wpdb->prepare("select post_title from $wpdb->posts where id = %d",$postparent));
							if(empty($get_data)){
								$data_array['post_parent'] = '';
							}
						}else{
					
							$post_tit_id = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_title = %s and post_type = %s order by ID DESC",$postparent,$data_array['post_type']));

							$post_name_id = $wpdb->get_results($wpdb->prepare("select ID from $wpdb->posts where post_name = %s and post_type = %s order by ID DESC",$postparent,$data_array['post_type']));
							if($post_tit_id){
								$data_array['post_parent'] = $post_tit_id[0]->ID;
							}elseif($post_name_id){
								$data_array['post_parent'] = $post_name_id[0]->ID;
							}
						}
					}
					$post_id = wp_insert_post($data_array);
					if($post_id != false) {
						$this->detailedLog[$currentLimit]['post_id'] = "<b>" . __('Created Post_ID', 'wp-ultimate-csv-importer') . " - </b>" . $post_id . " - success";
					}else{
						$this->detailedLog[$currentLimit]['Failed'] = "<b>" . __('Can not import the record. It may have unsupported values ', 'wp-ultimate-csv-importer') . "</b>"; 
					}
				}
			}
			unset($postauthor);
			if ($post_id) {
				$uploaded_file_name = $session_arr['uploadedFile'];
				$real_file_name = $session_arr['uploaded_csv_name'];
				$action = $data_array['post_type'];
				$get_imported_feature_image = array();
				$get_imported_feature_image = get_option('IMPORTED_FEATURE_IMAGES');
				if (is_array($get_imported_feature_image)) {
					$imported_feature_img = array_merge($get_imported_feature_image, $imported_feature_img);
				} else {
					$imported_feature_img = $imported_feature_img;
				}
				update_option('IMPORTED_FEATURE_IMAGES', $imported_feature_img);
				$created_records[$action][] = $post_id;
				if ($action == 'post') {
					$imported_as = 'Post';
				}
				if ($action == 'page') {
					$imported_as = 'Page';
				}
				if ($action != 'post' && $action != 'page') {
					$imported_as = 'Custom Post';
				}
				$keyword = $action;
				$this->insPostCount++;
				if (isset($sticky) && $sticky) {
					stick_post($post_id);
				}

				if (!empty ($custom_array)) {
					foreach ($custom_array as $custom_key => $custom_value) {
						update_post_meta($post_id, $custom_key, $custom_value);
					}
				}

				// Import post formats added by fredrick marks
				if (isset($post_format)) {
					wp_set_object_terms($post_id, $post_format, 'post_format');

				}
				//Import SEO Values     
				if (!empty($seo_custom_array)) {
					$this->importSEOfields($seo_custom_array, $post_id);
				}

				// Create custom taxonomy to post
				if (!empty ($smack_taxo)) {
					foreach ($smack_taxo as $taxo_key => $taxo_value) {
						if (!empty($taxo_value)) {
							$split_line = explode('|', $taxo_value);
							wp_set_object_terms($post_id, $split_line, $taxo_key);
						}
					}
				}

				// Create/Add tags to post
				if (!empty ($tags)) {
					$this->detailedLog[$currentLimit]['tags'] = "";
					foreach ($tags as $tag_key => $tag_value) {
						$this->detailedLog[$currentLimit]['tags'] .= $tag_value . "|";
						wp_set_post_tags($post_id, $tag_value);
					}
					$this->detailedLog[$currentLimit]['tags'] = "<b>" . __('Tags', 'wp-ultimate-csv-importer') . " - </b>" . substr($this->detailedLog[$currentLimit]['tags'], 0, -1);
				}

				// Create/Add category to post
				if (!empty ($categories)) {
					$this->detailedLog[$currentLimit]['category'] = "";
					$assigned_categories = array();
					$split_cate = explode('|', $categories ['post_category']);
					foreach ($split_cate as $key => $val) {
						if (is_numeric($val)) {
							$split_cate[$key] = 'uncategorized';
							$assigned_categories['uncategorized'] = 'uncategorized';
						}
						$assigned_categories[$val] = $val;
					}
					foreach ($assigned_categories as $cateKey => $cateVal) {
						$this->detailedLog[$currentLimit]['category'] .= $cateKey . "|";
					}
					$this->detailedLog[$currentLimit]['category'] = "<b>" . __('Category', 'wp-ultimate-csv-importer') . " - </b>" . substr($this->detailedLog[$currentLimit]['category'], 0, -1);
					wp_set_object_terms($post_id, $split_cate, 'category');
				}
				// Add featured image
				if (!empty ($file)) {
					//$wp_filetype = wp_check_filetype(@basename($file ['guid']), null);
					$wp_upload_dir = wp_upload_dir();
					$attachment = array('guid' => $file ['guid'], 'post_mime_type' => 'image/jpeg', 'post_title' => preg_replace('/[^a-zA-Z0-9._\-\s]/', '', @basename($file ['post_title'])), 'post_content' => '', 'post_status' => 'inherit');
					if ($get_media_settings == 1) {
						$generate_attachment = $dirname . '/' . $fimg_name;
					} else {
						$generate_attachment = $fimg_name;
					}
					$uploadedImage = $wp_upload_dir['path'] . '/' . $fimg_name;
					/*$attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
					$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
					wp_update_attachment_metadata($attach_id, $attach_data);*/
					if($useexistingimages == 'true') {
						$existing_attachment = array();
						$query = $wpdb->get_results($wpdb->prepare("select post_title from $wpdb->posts where post_type = %s and post_mime_type = %s",'attachment','image/jpeg'));
						if(!empty($query)) {
							foreach ( $query as $key ) {
								$existing_attachment[] = $key->post_title;
							}
						}
						if ( ! in_array( $attachment['post_title'], $existing_attachment ) ) {
							$attach_id   = wp_insert_attachment( $attachment, $generate_attachment, $post_id );
							$attach_data = wp_generate_attachment_metadata( $attach_id, $uploadedImage );
							wp_update_attachment_metadata( $attach_id, $attach_data );
						} else {
							$query2 = $wpdb->get_results( $wpdb->prepare( "select ID from $wpdb->posts where post_title = %s  and post_type = %s", $attachment['post_title'], 'attachment' ) );
							foreach ( $query2 as $key2 ) {
								$attach_id = $key2->ID;
							}
						}
					} else {
						$attach_id   = wp_insert_attachment( $attachment, $generate_attachment, $post_id );
						$attach_data = wp_generate_attachment_metadata( $attach_id, $uploadedImage );
						wp_update_attachment_metadata( $attach_id, $attach_data );
					}
					set_post_thumbnail($post_id, $attach_id);
				}
			} else {
				$skippedRecords[] = $_SESSION['SMACK_SKIPPED_RECORDS'];
			}
			$this->detailedLog[$currentLimit]['verify_here'] = "<b>Verify Here -</b> <a href='" . get_permalink($post_id) . "' title='" . esc_attr(sprintf(__('View &#8220;%s&#8221;'), $data_array['post_title'])) . "' rel='permalink' target='_blank'>" . __('Web View', 'wp-ultimate-csv-importer') . "</a> | <a href='" . get_edit_post_link($post_id) . "' title='" . esc_attr(__('Edit this item', 'wp-ultimate-csv-importer')) . "' target='_blank'>" . __('Admin View', 'wp-ultimate-csv-importer') . "</a>";
		}
		unset($data_array);
	}

	// Create Data base for Statistic chart
	public static function activate() {
		if (!defined('PDO::ATTR_DRIVER_NAME')) {
			echo __("Make sure you have enable the PDO extensions in your environment before activate the plugin!", 'wp-ultimate-csv-importer');
			die;
		}
		global $wpdb;
		$sql1 = "CREATE TABLE `smackcsv_pie_log` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`type` varchar(255) DEFAULT NULL,
			`value` int(11) DEFAULT NULL,
			PRIMARY KEY (`id`)
				) ENGINE=InnoDB;";

		$sql2 = "CREATE TABLE `smackcsv_line_log` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`month` varchar(60) DEFAULT NULL,
			`year` varchar(60) DEFAULT NULL,
			`imported_type` varchar(60) DEFAULT NULL, 
			`imported_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`inserted` int(11) DEFAULT NULL,

			PRIMARY KEY (`id`)
				) ENGINE=InnoDB;";
		$wpdb->query($sql1);
		$wpdb->query($sql2);
		$importedTypes = array('Post', 'Page', 'Custom Post', 'Comments', 'Users', 'Eshop');
		if(is_array($importedTypes)){
		foreach ($importedTypes as $importedType) {
			$querycheck = $wpdb->get_results($wpdb->prepare("select *from smackcsv_pie_log where type = %s",$importedType));
			if (count($querycheck) == 0) {
				$sql4 = "insert into smackcsv_pie_log (type,value) values(\"$importedType\",0)";
				$wpdb->query($sql4);
			}
		}
		}
		$saveSettings = array('savesettings' => 'Save', 'post' => 'post', 'page' => 'page', 'custompost' => 'custompost', 'drop_table' => 'off', 'debug_mode' => 'disable_debug', 'export_delimiter' => ';',);
		update_option('wpcsvfreesettings', $saveSettings);
	}

	//Drop Database While Deactivate plugin
	public function deactivate() {
		global $wpdb;
		$sql1 = "DROP TABLE smackcsv_pie_log;";
		$wpdb->query($sql1);

		$sql2 = "DROP TABLE smackcsv_line_log;";
		$wpdb->query($sql2);

		update_option('wpcsvfreesettings', '');
	}

	public function addPieChartEntry($imported_as, $count) {
		//add total counts
		global $wpdb;
		$getTypeID = $wpdb->get_results($wpdb->prepare("select * from smackcsv_pie_log where type = %s",$imported_as));
		if (count($getTypeID) == 0) {
			$wpdb->insert('smackcsv_pie_log', array('type' => $imported_as, 'value' => $count));
		} else {
			$wpdb->update('smackcsv_pie_log', array('value' => $getTypeID[0]->value + $count), array('id' => $getTypeID[0]->id));
		}
	}

	function addStatusLog($inserted, $imported_as) {
		global $wpdb;
		$today = date('Y-m-d h:i:s');
		$mon = date("M", strtotime($today));
		$year = date("Y", strtotime($today));
		$wpdb->insert('smackcsv_line_log', array('month' => $mon, 'year' => $year, 'imported_type' => $imported_as, 'imported_on' => date('Y-m-d h:i:s'), 'inserted' => $inserted));
	}

	/**
	 * Function for importing the all in seo data
	 * Feature added by Fredrick on version3.5.4
	 */
	function importSEOfields($array, $postId) {
		$seo_opt = get_option('active_plugins');
		if (in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $seo_opt)) {
			if (isset($array['keywords'])) {
				$custom_array['_aioseop_keywords'] = $array['keywords'];
			}
			if (isset($array['description'])) {
				$custom_array['_aioseop_description'] = $array['description'];
			}
			if (isset($array['title'])) {
				$custom_array['_aioseop_title'] = $array['title'];
			}
			if (isset($array['noindex'])) {
				$custom_array['_aioseop_noindex'] = $array['noindex'];
			}
			if (isset($array['nofollow'])) {
				$custom_array['_aioseop_nofollow'] = $array['nofollow'];
			}
			if (isset($array['titleatr'])) {
				$custom_array['_aioseop_titleatr'] = $array['titleatr'];
			}
			if (isset($array['menulabel'])) {
				$custom_array['_aioseop_menulabel'] = $array['menulabel'];
			}
			if (isset($array['disable'])) {
				$custom_array['_aioseop_disable'] = $array['disable'];
			}
			if (isset($array['disable_analytics'])) {
				$custom_array['_aioseop_disable_analytics'] = $array['disable_analytics'];
			}
			if(isset($array['canonical_url'])){
                               $custom_array['_aioseop_custom_link'] = $array['canonical_url'];
                       }
			if (isset($array['noodp'])) {
				$custom_array['_aioseop_noodp'] = $array['noodp'];
			}
			if (isset($array['noydir'])) {
				$custom_array['_aioseop_noydir'] = $array['noydir'];
			}
		}
		if (!empty ($custom_array)) {
			foreach ($custom_array as $custom_key => $custom_value) {
				update_post_meta($postId, $custom_key, $custom_value);
			}
		}

	}//importSEOfields ends

	/**
	 * Delete uploaded file after import process
	 */
	function deletefileafterprocesscomplete($uploadDir) {
		$files = array_diff(scandir($uploadDir), array('.', '..'));
		foreach ($files as $file) {
			(is_dir("$uploadDir/$file")) ? rmdir("$uploadDir/$file") : unlink("$uploadDir/$file");
		}
	}

	// Function convert string to hash_key
	public function convert_string2hash_key($value) {
		$file_name = hash_hmac('md5', "$value", 'secret');
		return $file_name;
	}

	// Function to show common notice for PRO Feature
	public function common_notice_for_pro_feature() {
		return "<p align='center'> <label style='color:red;'> " . __('This feature is only available in Pro!', 'wp-ultimate-csv-importer') . " </label> <a href='".esc_url('http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html')."' target='_blank'>" . __('Go Pro Now', 'wp-ultimate-csv-importer') . "</a> </p>";
	}

	// Function for common footer
	public function common_footer_for_other_plugin_promotions() {
		$content = '<div class="accordion-inner">
			<label class="plugintags"><a href='.esc_url("https://www.smackcoders.com/vtigercrm-magento-connector.html?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic").' target="_blank">VTiger 6 Magento Sync</a></label>
			<label class="plugintags"><a href='.esc_url("https://www.smackcoders.com/vtigercrm-mailchimp-integration.html?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic").' target="_blank">VTiger 6 Mailchimp</a></label>
			<label class="plugintags"><a href='.esc_url("https://www.smackcoders.com/vtiger-crm-quickbooks-integration-module-online.html/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic").' target="_blank">Vtiger QuickBooks</a></label>
			<label class="plugintags"><a href='.esc_url("https://www.smackcoders.com/xero-vtiger-crm-6-0-integration.html/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic").' target="_blank">Vtiger Xero Sync</a></label>
			<label class="plugintags"><a href='.esc_url("https://www.smackcoders.com/hr-payroll.html/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic").' target="_blank">Vtiger HR and Payroll</a></label>

			<label class="plugintags"><a href='.esc_url("https://www.wpultimatecsvimporter.com/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic").' target="_blank">WP Ultimate CSV Importer Pro</a></label>
			<label class="plugintags"><a href='.esc_url("https://www.smackcoders.com/product/crm-sugar-wordpress-web-forms-builder?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic").' target="_blank">WordPress Sugar Pro</a></label>
			<div style="position:relative;float:right;"><a href='.esc_url("http://www.smackcoders.com/").'><img width=80 src='.esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR."images/smackcoders-logo.png").' /></a></div>
			</div>';
		echo $content;
	}

	public function common_footer() {
		$get_pluginData = get_plugin_data(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . 'index.php');
		$footer = '';
		$footer .= '<div style="padding:10px;">';
		$footer .= '<label class="plugintags"><a href='.esc_url("http://www.wpultimatecsvimporter.com").' target="_blank">' . __("Home", 'wp-ultimate-csv-importer') . '</a></label>
			<label class="plugintags"><a href='.esc_url("https://www.wpultimatecsvimporter.com/documentation/all-import/wordpress-ultimate-csv-importer/?utm_source=WpOrg&utm_medium=Readme&utm_campaign=ListingTraffic").' target="_blank">' . __('Tutorials', 'wp-ultimate-csv-importer') . '</a></label><label class="plugintags"><a href='.esc_url("https://www.youtube.com/watch?v=S1P8KebpLaU&list=PL2k3Ck1bFtbTtSX3hEFoBvK_Ka0pfYwp_?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic").' target="_blank">' . __("Videos", 'wp-ultimate-csv-importer') . '</a></label>
			<label class="plugintags"><a href='.esc_url("https://www.smackcoders.com/blog/wordpress-ultimate-csv-importer-csv-sample-files-and-updates.html?utm_source=WpOrg&utm_medium=Readme&utm_campaign=ListingTraffic").' target="_blank">' . __("Sample Files", 'wp-ultimate-csv-importer') . '</a></label>';
		$footer .= '</div>';
		$footer .= '<div style="padding:10px;margin-bottom:20px;">';
		if (isset ($_REQUEST['__module']) && $_REQUEST['__module'] != 'settings') {
			//			$footer .= "<div style='float:right;margin-top:-49px;'><a class='label label-info' href='" . get_admin_url() . "admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=settings'>" . __('Click here to Enable any disabled module', 'wp-ultimate-csv-importer') . "</a></div>";
		}
		if (isset ($_REQUEST['__module']) && $_REQUEST['__module'] == 'settings') {
			$footer .= "<div style='float:right;margin-top:-48px;'><span style='margin-right:20px;'><a class='label label-info' href=".esc_url('http://wordpress.org/plugins/wp-ultimate-csv-importer/developers/').">" . __('Get Old Versions', 'wp-ultimate-csv-importer') . "</a></span><a class='label label-info' href='" . get_admin_url() . "admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=support'>" . __('Click here to Get some useful links') . "</a></div>";
			$footer .= "<div style='float:right;margin-right:15px;'> </span> " . __('Current Version', 'wp-ultimate-csv-importer') . ":" . $get_pluginData['Version'] . " </div>";
		}
		if (isset ($_REQUEST['__module']) && $_REQUEST['__module'] != 'support' && $_REQUEST['__module'] != 'settings') {
			$footer .= "<div style='float:right;margin-top:-48px;'><span style='margin-right:20px;'> <a class='label label-info' href=".esc_url('http://wordpress.org/plugins/wp-ultimate-csv-importer/developers/').">" . __('Get Old Versions', 'wp-ultimate-csv-importer') . "</a></span><a class='label label-info' href='" . get_admin_url() . "admin.php?page=" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/index.php&__module=support'>" . __('Click here to Get some useful links', 'wp-ultimate-csv-importer') . "</a></div>";
			$footer .= "<div style='float:right;margin-right:15px;'> " . 'Current Version' . ": " . $get_pluginData['Version'] . " </div>";
		}
		if (isset ($_REQUEST['__module']) && $_REQUEST['__module'] == 'support') {
			$footer .= "<div style='float:right;margin-right:15px;'><span style='margin-right:20px;'>" . __('Current Version', 'wp-ultimate-csv-importer') . ": " . $get_pluginData['Version'] . " </span><span style='margin-right:10px;'><a class='label label-info' href=".esc_url('http://wordpress.org/plugins/wp-ultimate-csv-importer/developers/').">" . __('Get Old Versions', 'wp-ultimate-csv-importer') . "</a></span></div>";
		}
		$footer .= '</div>';
		$footer .= '<div style="float:right;margin-right:15px;margin-top:-10px;"> <label>Plugin By <a href='.esc_url("http://www.smackcoders.com").'> Smackcoders</a></label> </div>';
		echo $footer;
	}

	function smack_csv_import_method() {

		$smack_csv_import_method = '<div class="importfileoption">

			<div align="center" style="text-align:left;margin-top:-33px;">
			<div id="boxmethod1" class="method1">
			<label><span class="radio-icon"><input type="radio" name="importmethod" id="uploadfilefromcomputer" onclick="choose_import_method(this.id);" checked/></span> <span class="header-text" id="importopt">' . __('From Computer', 'wp-ultimate-csv-importer') . '</span> </label> <br>
			<!-- The fileinput-button span is used to style the file input field as button -->
			<div id="method1" style="display:block;height:40px;">
			<progress id ="progressbar" value="0" max="100"> </progress>
			<span class="btn btn-success fileinput">
			<span>' . __('Browse', 'wp-ultimate-csv-importer') . '</span>
			<input id="fileupload" type="file" name="files[]" multiple onchange="prepareUpload()">
			<a href="#" id="zip_process" style = "display:none">  Click Here To Process Zip </a>
			</span>';
		// The global progress bar 
		$smack_csv_import_method .= '</div>
			</div>
			<div  style = "opacity: 0.3;background-color: ghostwhite;">
			<div id="boxmethod2" class="method2">
			<label><span class="radio-icon"><input type="radio" name="importmethod" id="dwnldftpfile"  disabled/></span> <span class="header-text" id="importopt">' . __('From FTP', 'wp-ultimate-csv-importer') . '</span> </label> <img src="' . esc_url(WP_CONTENT_URL . "/plugins/" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/images/pro_icon.gif").'" title="PRO Feature" /> <br>
			</div>
			<div id="boxmethod3" class="method3">
			<label> <span class="radio-icon"><input type="radio" name="importmethod" id="dwnldextrfile" disabled/></span> <span class="header-text" id="importopt">' . __('From URL', 'wp-ultimate-csv-importer') . '</span></label> <img src="' . esc_url(WP_CONTENT_URL . "/plugins/" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/images/pro_icon.gif").'" title="PRO Feature" /> <br>
			</div>
			<div id="boxmethod4" class="method4">
			<label><span class="radio-icon"><input type="radio" name="importmethod" id="useuploadedfile"  disabled/></span> <span class="header-text" id="importopt">' . __('From Already Uploaded', 'wp-ultimate-csv-importer') . '</span></label> <img src="' . esc_url(WP_CONTENT_URL . "/plugins/". WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/images/pro_icon.gif").'" title="PRO Feature" /> <br>
			</div>
			</div>
			</div>
			</div>';
		$curr_module = $_REQUEST['__module'];
		if ($curr_module == 'post' || $curr_module == 'page' || $curr_module == 'custompost' || $curr_module == 'eshop') {
			$smack_csv_import_method .= '<div class="media_handling">
			<span class="advancemediahandling"> <b><label> <input type="checkbox" name="advance_media_handling" id="advance_media_handling"   onclick = "filezipopen();" /> ' . __("Advance Media Handling", 'wp-ultimate-csv-importer') . ' </label></b> </span>
			<span id = "filezipup" style ="display:none;">
			<span class="advancemediahandling" style="padding-left:30px;"> <input type="file" name="inlineimages" id="inlineimages" onchange ="checkextension(this.value);" /> </span>
			</span>
			</div>';
		}

		return $smack_csv_import_method;
	}

	function helpnotes($media_option = null) {
		$smackhelpnotes = '<span style="position:absolute;margin-top:6px;margin-left:15px;">
			<a href="#" class="tooltip">
			<img src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/help.png").'" />
			<span class="tooltipPostStatus">
			<img class="callout" src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/callout.gif").'" />
			Default value is 1. You can give any value based on your environment configuration.
			<img src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/help.png").'" style="margin-top: 6px;float:right;" />
			</span>
			</a>
			</span>';
		if($media_option == 'skipDuplicate') {
			$smackhelpnotes = '<span style="position:absolute;margin-top:6px;margin-left:15px;text-align: justify;">
			<a href="#" class="tooltip">
			<img src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/help.png").'" />
			<span class="tooltipPostStatus">
			<img class="callout" src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/callout.gif").'" />
			1) If checked, skips image with same name and assigns the existing image to the new post. <br>
			2) If unchecked, image name is renamed as imagename-number(logo-1.png) similar to the WordPress media handling.
			<img src="' . esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/help.png").'" style="margin-top: 6px;float:right;" />
			</span>
			</a>
			</span>';
		}
		return $smackhelpnotes;
	}

	function create_nonce_key() {
		return wp_create_nonce('smack_nonce');
	}

	function reduceStringLength($convert_str, $checktext) {
		if ($checktext == 'Enable' || $checktext == 'Disable' || $checktext == 'Mapping') {
			if (strlen($convert_str) > 7) {
				$convert_str = substr($convert_str, 0, 5) . '..';
			}
		} else {
			if ($checktext == 'caticonEnable' || $checktext == 'caticonDisable') {
				if (strlen($convert_str) > 7) {
					$convert_str = substr($convert_str, 0, 4) . '..';
				}
			} else {
				if ($checktext == 'Enabled' || $checktext == 'Disabled') {
					if (strlen($convert_str) > 8) {
						$convert_str = substr($convert_str, 0, 3) . '..';
					}
				} else {
					if ($checktext == 'Check All' || $checktext == 'Uncheck All') {
						if (strlen($convert_str) > 12) {
							$convert_str = substr($convert_str, 0, 10) . '..';
						}
					} else {
						if ($checktext == 'Yes' || $checktext == 'No') {
							if (strlen($convert_str) > 4) {
								$convert_str = substr($convert_str, 0, 2) . '..';
							}
						} else {
							if ($checktext == 'Next' && strlen($convert_str) > 5) {
								$convert_str = substr($convert_str, 0, 4) . '..';
							}
						}
					}
				}
			}
		}
		return $convert_str;
	}

	public function getStatsWithDate() {
		global $wpdb;
		$returnArray = array();
		$plot =array();
		$get_imptype = array('Post','Page','Comments','Custom Post','Users','Eshop');
		$mon_year = array(11 => 'Nov',10 =>'Oct',9 =>'Sep',8 =>'Aug',7 => 'Jul', 6 => 'Jun', 5 => 'May' ,4 => 'Apr', 3 => 'Mar', 2 => 'Feb', 1 => 'Jan',12 => 'Dec');
		$today = date("Y-m-d H:i:s");
		for($i = 0; $i <= 11; $i++) {
			$month[] = date("M", strtotime( $today." -$i months"));
			$year[]  = date("Y", strtotime( $today." -$i months"));
		}
		foreach($month as $mkey) {
			foreach($year as $ykey) {
				$mon_num = array_search($mkey,$mon_year);
				$postCount = $pageCount = $customCount = $userCount = $shopCount = 0;
				$j = 0;
				$plot = $wpdb->get_results($wpdb->prepare("select inserted,imported_type from smackcsv_line_log where imported_type in (%s,%s,%s,%s,%s,%s) and month = %s and year = %s",'Post','Page','Comments','Custom Post','Users','Eshop',$mkey,$ykey));
				foreach($plot as $pkey) {
					switch ($pkey->imported_type) {
						case 'Post':
							$postCount += $pkey->inserted;
							break;
						case 'Page':
							$pageCount += $pkey->inserted;
							break;
						case 'Custom Post':
							$customCount += $pkey->inserted;
							break;
						case 'Users':
							$userCount  += $pkey->inserted;
							break;
						case 'Eshop':
							$shopCount  += $pkey->inserted;
							break;
						default:
							break;
					}
					$returnArray[$j] = array('year' => ''.$ykey.'-'.$mon_num.'','post' => (int)$postCount,'page' => (int)$pageCount,'custompost' => (int)$customCount, 'users' => (int)$userCount, 'eshop' => (int)$shopCount);
					$j++;
				}
			}
		}
		if(empty($returnArray)){
			$returnArray[$j] = array('year' => ''.date('Y').'-'.date('m').'','post' => 0,'page' => 0,'custompost' => 0, 'users' => 0, 'eshop' => 0);
		}
		$reqarr = array();
		$reqarr[0] = $returnArray[count($returnArray) - 1];
		return json_encode($reqarr);
	}

	public function piechart()
	{
		ob_clean();
		global $wpdb;
		$blog_id = 1;
		$returnArray = array();
		$imptype = array('Post','Page','Comments','Custom Post','Users','Eshop');
		$i = 0;
		foreach($imptype as $imp) {
			$OverviewDetails = $wpdb->get_results($wpdb->prepare("select * from smackcsv_pie_log where type = %s and value != %d",$imp,0));
			foreach($OverviewDetails as  $overview){
				//$returnArray[$i][0] = $overview->type;
				//$returnArray[$i][1] = (int)$overview->value;
				$returnArray[$i] = array(
						'label'   => ''.$overview->type.'',
						'value'   => ''.(int)$overview->value.'',
				);
				$i++;
			}
		}
		if(empty($returnArray ) ){
			$returnArray['label']  = 'No Imports Yet' ;
		}
		return json_encode($returnArray);
	}
}

class CallWPImporterObj extends WPImporter_includes_helper {
	private static $_instance = null;

	public static function getInstance() {
		if (!is_object(self::$_instance))  //or if( is_null(self::$_instance) ) or if( self::$_instance == null )
		{
			self::$_instance = new WPImporter_includes_helper();
		}
		return self::$_instance;
	}

	public static function checkSecurity() {
		$msg = 'You are not allowed to do this operation! Please contact your admin';
		if (!function_exists('session_status')) {
			if (session_id() == '') {
				return $msg;
			} else {
				return 'true';
			}
		} else {
			if (session_status() != PHP_SESSION_ACTIVE) {
				return $msg;
			} else {
				if (!defined('ABSPATH')) {
					return $msg;
				} else {
					if (php_sapi_name() == "cli") {
						return $msg;
					} else {
						return 'true';
					}
				}
			}
		}
	}
}

class WPImpCSVParserLib {

	/*

		Class: parseCSV v0.4.3 beta
		http://code.google.com/p/parsecsv-for-php/


		Fully conforms to the specifications lined out on wikipedia:
		 - http://en.wikipedia.org/wiki/Comma-separated_values

		Based on the concept of Ming Hong Ng's CsvFileParser class:
		 - http://minghong.blogspot.com/2006/07/csv-parser-for-php.html



		Copyright (c) 2007 Jim Myhrberg (jim@zydev.info).

		Permission is hereby granted, free of charge, to any person obtaining a copy
		of this software and associated documentation files (the "Software"), to deal
		in the Software without restriction, including without limitation the rights
		to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
		copies of the Software, and to permit persons to whom the Software is
		furnished to do so, subject to the following conditions:

		The above copyright notice and this permission notice shall be included in
		all copies or substantial portions of the Software.

		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
		IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
		FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
		AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
		LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
		OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
		THE SOFTWARE.



		Code Examples
		----------------
		# general usage
		$csv = new parseCSV('data.csv');
		print_r($csv->data);
		----------------
		# tab delimited, and encoding conversion
		$csv = new parseCSV();
		$csv->encoding('UTF-16', 'UTF-8');
		$csv->delimiter = "\t";
		$csv->parse('data.tsv');
		print_r($csv->data);
		----------------
		# auto-detect delimiter character
		$csv = new parseCSV();
		$csv->auto('data.csv');
		print_r($csv->data);
		----------------
		# modify data in a csv file
		$csv = new parseCSV();
		$csv->sort_by = 'id';
		$csv->parse('data.csv');
		# "4" is the value of the "id" column of the CSV row
		$csv->data[4] = array('firstname' => 'John', 'lastname' => 'Doe', 'email' => 'john@doe.com');
		$csv->save();
		----------------
		# add row/entry to end of CSV file
		#  - only recommended when you know the extact sctructure of the file
		$csv = new parseCSV();
		$csv->save('data.csv', array(array('1986', 'Home', 'Nowhere', '')), true);
		----------------
		# convert 2D array to csv data and send headers
		# to browser to treat output as a file and download it
		$csv = new parseCSV();
		$csv->output (true, 'movies.csv', $array);
		----------------


	*/


	/**
	 * Configuration
	 * - set these options with $object->var_name = 'value';
	 */

	# use first line/entry as field names
	var $heading = true;

	# override field names
	var $fields = array();

	# sort entries by this field
	var $sort_by = null;
	var $sort_reverse = false;

	# sort behavior passed to ksort/krsort functions
	# regular = SORT_REGULAR
	# numeric = SORT_NUMERIC
	# string  = SORT_STRING
	var $sort_type = null;

	# delimiter (comma) and enclosure (double quote)
	var $delimiter = ',';
	var $enclosure = '"';

	# basic SQL-like conditions for row matching
	var $conditions = null;

	# number of rows to ignore from beginning of data
	var $offset = null;

	# limits the number of returned rows to specified amount
	var $limit = null;

	# number of rows to analyze when attempting to auto-detect delimiter
	var $auto_depth = 15;

	# characters to ignore when attempting to auto-detect delimiter
	var $auto_non_chars = "a-zA-Z0-9\n\r";

	# preferred delimiter characters, only used when all filtering method
	# returns multiple possible delimiters (happens very rarely)
	var $auto_preferred = ",;\t.:|";

	# character encoding options
	var $convert_encoding = false;
	var $input_encoding = 'ISO-8859-1';
	var $output_encoding = 'ISO-8859-1';

	# used by unparse(), save(), and output() functions
	var $linefeed = "\r\n";

	# only used by output() function
	var $output_delimiter = ',';
	var $output_filename = 'data.csv';

	# keep raw file data in memory after successful parsing (useful for debugging)
	var $keep_file_data = false;

	/**
	 * Internal variables
	 */

	# current file
	var $file;

	# loaded file contents
	var $file_data;

	# error while parsing input data
	#  0 = No errors found. Everything should be fine :)
	#  1 = Hopefully correctable syntax error was found.
	#  2 = Enclosure character (double quote by default)
	#      was found in non-enclosed field. This means
	#      the file is either corrupt, or does not
	#      standard CSV formatting. Please validate
	#      the parsed data yourself.
	var $error = 0;

	# detailed error info
	var $error_info = array();

	# array of field values in data parsed
	var $titles = array();

	# two dimentional array of CSV data
	var $data = array();


	/**
	 * Constructor
	 * @param   input   CSV file or string
	 * @return  nothing
	 */
	function parseCSV($input = null, $offset = null, $limit = null, $conditions = null) {
		if ($offset !== null) {
			$this->offset = $offset;
		}
		if ($limit !== null) {
			$this->limit = $limit;
		}
		if (count($conditions) > 0) {
			$this->conditions = $conditions;
		}
		if (!empty($input)) {
			$this->parse($input);
		}
	}


	// ==============================================
	// ----- [ Main Functions ] ---------------------
	// ==============================================

	/**
	 * Parse CSV file or string
	 * @param   input   CSV file or string
	 * @return  nothing
	 */
	function parse($input = null, $offset = null, $limit = null, $conditions = null) {
		if ($input === null) {
			$input = $this->file;
		}
		if (!empty($input)) {
			if ($offset !== null) {
				$this->offset = $offset;
			}
			if ($limit !== null) {
				$this->limit = $limit;
			}
			if (count($conditions) > 0) {
				$this->conditions = $conditions;
			}
			if (is_readable($input)) {
				$this->data = $this->parse_file($input);
			} else {
				$this->file_data = &$input;
				$this->data = $this->parse_string();
			}
			if ($this->data === false) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Save changes, or new file and/or data
	 * @param   file     file to save to
	 * @param   data 2D array with data
	 * @param   append   append current data to end of target CSV if exists
	 * @param   fields   field names
	 * @return  true or false
	 */
	function save($file = null, $data = array(), $append = false, $fields = array()) {
		if (empty($file)) {
			$file = &$this->file;
		}
		$mode = ($append) ? 'at' : 'wt';
		$is_php = (preg_match('/\.php$/i', $file)) ? true : false;
		return $this->_wfile($file, $this->unparse($data, $fields, $append, $is_php), $mode);
	}

	/**
	 * Generate CSV based string for output
	 * @param   filename    if specified, headers and data will be output directly to browser as a downloable file
	 * @param   data 2D array with data
	 * @param   fields      field names
	 * @param   delimiter   delimiter used to separate data
	 * @return  CSV data using delimiter of choice, or default
	 */
	function output($filename = null, $data = array(), $fields = array(), $delimiter = null) {
		if (empty($filename)) {
			$filename = $this->output_filename;
		}
		if ($delimiter === null) {
			$delimiter = $this->output_delimiter;
		}
		$data = $this->unparse($data, $fields, null, null, $delimiter);
		if ($filename !== null) {
			header('Content-type: application/csv');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			echo $data;
		}
		return $data;
	}

	/**
	 * Convert character encoding
	 * @param   input    input character encoding, uses default if left blank
	 * @param   output   output character encoding, uses default if left blank
	 * @return  nothing
	 */
	function encoding($input = null, $output = null) {
		$this->convert_encoding = true;
		if ($input !== null) {
			$this->input_encoding = $input;
		}
		if ($output !== null) {
			$this->output_encoding = $output;
		}
	}

	/**
	 * Auto-Detect Delimiter: Find delimiter by analyzing a specific number of
	 * rows to determine most probable delimiter character
	 * @param   file           local CSV file
	 * @param   parse          true/false parse file directly
	 * @param   search_depth   number of rows to analyze
	 * @param   preferred      preferred delimiter characters
	 * @param   enclosure      enclosure character, default is double quote (").
	 * @return  delimiter character
	 */
	function auto($file = null, $parse = true, $search_depth = null, $preferred = null, $enclosure = null) {

		if ($file === null) {
			$file = $this->file;
		}
		if (empty($search_depth)) {
			$search_depth = $this->auto_depth;
		}
		if ($enclosure === null) {
			$enclosure = $this->enclosure;
		}

		if ($preferred === null) {
			$preferred = $this->auto_preferred;
		}

		if (empty($this->file_data)) {
			if ($this->_check_data($file)) {
				$data = &$this->file_data;
			} else {
				return false;
			}
		} else {
			$data = &$this->file_data;
		}

		$chars = array();
		$strlen = strlen($data);
		$enclosed = false;
		$n = 1;
		$to_end = true;

		// walk specific depth finding posssible delimiter characters
		for ($i = 0; $i < $strlen; $i++) {
			$ch = $data{$i};
			$nch = (isset($data{$i + 1})) ? $data{$i + 1} : false;
			$pch = (isset($data{$i - 1})) ? $data{$i - 1} : false;

			// open and closing quotes
			if ($ch == $enclosure) {
				if (!$enclosed || $nch != $enclosure) {
					$enclosed = ($enclosed) ? false : true;
				} elseif ($enclosed) {
					$i++;
				}

				// end of row
			} elseif (($ch == "\n" && $pch != "\r" || $ch == "\r") && !$enclosed) {
				if ($n >= $search_depth) {
					$strlen = 0;
					$to_end = false;
				} else {
					$n++;
				}

				// count character
			} elseif (!$enclosed) {
				if (!preg_match('/[' . preg_quote($this->auto_non_chars, '/') . ']/i', $ch)) {
					if (!isset($chars[$ch][$n])) {
						$chars[$ch][$n] = 1;
					} else {
						$chars[$ch][$n]++;
					}
				}
			}
		}

		// filtering
		$depth = ($to_end) ? $n - 1 : $n;
		$filtered = array();
		foreach ($chars as $char => $value) {
			if ($match = $this->_check_count($char, $value, $depth, $preferred)) {
				$filtered[$match] = $char;
			}
		}

		// capture most probable delimiter
		ksort($filtered);
		$this->delimiter = reset($filtered);

		// parse data
		if ($parse) {
			$this->data = $this->parse_string();
		}

		return $this->delimiter;

	}


	// ==============================================
	// ----- [ Core Functions ] ---------------------
	// ==============================================

	/**
	 * Read file to string and call parse_string()
	 * @param   file   local CSV file
	 * @return  2D array with CSV data, or false on failure
	 */
	function parse_file($file = null) {
		if ($file === null) {
			$file = $this->file;
		}
		if (empty($this->file_data)) {
			$this->load_data($file);
		}
		return (!empty($this->file_data)) ? $this->parse_string() : false;
	}

	/**
	 * Parse CSV strings to arrays
	 * @param   data   CSV string
	 * @return  2D array with CSV data, or false on failure
	 */
	function parse_string($data = null) {
		if (empty($data)) {
			if ($this->_check_data()) {
				$data = &$this->file_data;
			} else {
				return false;
			}
		}

		$white_spaces = str_replace($this->delimiter, '', " \t\x0B\0");

		$rows = array();
		$row = array();
		$row_count = 0;
		$current = '';
		$head = (!empty($this->fields)) ? $this->fields : array();
		$col = 0;
		$enclosed = false;
		$was_enclosed = false;
		$strlen = strlen($data);

		// walk through each character
		for ($i = 0; $i < $strlen; $i++) {
			$ch = $data{$i};
			$nch = (isset($data{$i + 1})) ? $data{$i + 1} : false;
			$pch = (isset($data{$i - 1})) ? $data{$i - 1} : false;

			// open/close quotes, and inline quotes
			if ($ch == $this->enclosure) {
				if (!$enclosed) {
					if (ltrim($current, $white_spaces) == '') {
						$enclosed = true;
						$was_enclosed = true;
					} else {
						$this->error = 2;
						$error_row = count($rows) + 1;
						$error_col = $col + 1;
						if (!isset($this->error_info[$error_row . '-' . $error_col])) {
							$this->error_info[$error_row . '-' . $error_col] = array('type' => 2, 'info' => 'Syntax error found on row ' . $error_row . '. Non-enclosed fields can not contain double-quotes.', 'row' => $error_row, 'field' => $error_col, 'field_name' => (!empty($head[$col])) ? $head[$col] : null,);
						}
						$current .= $ch;
					}
				} elseif ($nch == $this->enclosure) {
					$current .= $ch;
					$i++;
				} elseif ($nch != $this->delimiter && $nch != "\r" && $nch != "\n") {
					for ($x = ($i + 1); isset($data{$x}) && ltrim($data{$x}, $white_spaces) == ''; $x++) {
					}
					if ($data{$x} == $this->delimiter) {
						$enclosed = false;
						$i = $x;
					} else {
						if ($this->error < 1) {
							$this->error = 1;
						}
						$error_row = count($rows) + 1;
						$error_col = $col + 1;
						if (!isset($this->error_info[$error_row . '-' . $error_col])) {
							$this->error_info[$error_row . '-' . $error_col] = array('type' => 1, 'info' => 'Syntax error found on row ' . (count($rows) + 1) . '. ' . 'A single double-quote was found within an enclosed string. ' . 'Enclosed double-quotes must be escaped with a second double-quote.', 'row' => count($rows) + 1, 'field' => $col + 1, 'field_name' => (!empty($head[$col])) ? $head[$col] : null,);
						}
						$current .= $ch;
						$enclosed = false;
					}
				} else {
					$enclosed = false;
				}

				// end of field/row
			} elseif (($ch == $this->delimiter || $ch == "\n" || $ch == "\r") && !$enclosed) {
				$key = (!empty($head[$col])) ? $head[$col] : $col;
				$row[$key] = ($was_enclosed) ? $current : trim($current);
				$current = '';
				$was_enclosed = false;
				$col++;

				// end of row
				if ($ch == "\n" || $ch == "\r") {
					if ($this->_validate_offset($row_count) && $this->_validate_row_conditions($row, $this->conditions)) {
						if ($this->heading && empty($head)) {
							$head = $row;
						} elseif (empty($this->fields) || (!empty($this->fields) && (($this->heading && $row_count > 0) || !$this->heading))) {
							if (!empty($this->sort_by) && !empty($row[$this->sort_by])) {
								if (isset($rows[$row[$this->sort_by]])) {
									$rows[$row[$this->sort_by] . '_0'] = &$rows[$row[$this->sort_by]];
									unset($rows[$row[$this->sort_by]]);
									for ($sn = 1; isset($rows[$row[$this->sort_by] . '_' . $sn]); $sn++) {
									}
									$rows[$row[$this->sort_by] . '_' . $sn] = $row;
								} else {
									$rows[$row[$this->sort_by]] = $row;
								}
							} else {
								$rows[] = $row;
							}
						}
					}
					$row = array();
					$col = 0;
					$row_count++;
					if ($this->sort_by === null && $this->limit !== null && count($rows) == $this->limit) {
						$i = $strlen;
					}
					if ($ch == "\r" && $nch == "\n") {
						$i++;
					}
				}

				// append character to current field
			} else {
				$current .= $ch;
			}
		}
		$this->titles = $head;
		if (!empty($this->sort_by)) {
			$sort_type = SORT_REGULAR;
			if ($this->sort_type == 'numeric') {
				$sort_type = SORT_NUMERIC;
			} elseif ($this->sort_type == 'string') {
				$sort_type = SORT_STRING;
			}
			($this->sort_reverse) ? krsort($rows, $sort_type) : ksort($rows, $sort_type);
			if ($this->offset !== null || $this->limit !== null) {
				$rows = array_slice($rows, ($this->offset === null ? 0 : $this->offset), $this->limit, true);
			}
		}
		if (!$this->keep_file_data) {
			$this->file_data = null;
		}
		return $rows;
	}

	/**
	 * Create CSV data from array
	 * @param   data 2D array with data
	 * @param   fields      field names
	 * @param   append      if true, field names will not be output
	 * @param   is_php      if a php die() call should be put on the first
	 *                      line of the file, this is later ignored when read.
	 * @param   delimiter   field delimiter to use
	 * @return  CSV data (text string)
	 */
	function unparse($data = array(), $fields = array(), $append = false, $is_php = false, $delimiter = null) {
		if (!is_array($data) || empty($data)) {
			$data = &$this->data;
		}
		if (!is_array($fields) || empty($fields)) {
			$fields = &$this->titles;
		}
		if ($delimiter === null) {
			$delimiter = $this->delimiter;
		}

		$string = ($is_php) ? "<?php header('Status: 403'); die(' '); ?>" . $this->linefeed : '';
		$entry = array();

		// create heading
		if ($this->heading && !$append && !empty($fields)) {
			foreach ($fields as $key => $value) {
				$entry[] = $this->_enclose_value($value);
			}
			$string .= implode($delimiter, $entry) . $this->linefeed;
			$entry = array();
		}

		// create data
		foreach ($data as $key => $row) {
			foreach ($row as $field => $value) {
				$entry[] = $this->_enclose_value($value);
			}
			$string .= implode($delimiter, $entry) . $this->linefeed;
			$entry = array();
		}

		return $string;
	}

	/**
	 * Load local file or string
	 * @param   input   local CSV file
	 * @return  true or false
	 */
	function load_data($input = null) {
		$data = null;
		$file = null;
		if ($input === null) {
			$file = $this->file;
		} elseif (file_exists($input)) {
			$file = $input;
		} else {
			$data = $input;
		}
		if (!empty($data) || $data = $this->_rfile($file)) {
			if ($this->file != $file) {
				$this->file = $file;
			}
			if (preg_match('/\.php$/i', $file) && preg_match('/<\?.*?\?>(.*)/ims', $data, $strip)) {
				$data = ltrim($strip[1]);
			}
			if ($this->convert_encoding) {
				$data = iconv($this->input_encoding, $this->output_encoding, $data);
			}
			if (substr($data, -1) != "\n") {
				$data .= "\n";
			}
			$this->file_data = &$data;
			return true;
		}
		return false;
	}


	// ==============================================
	// ----- [ Internal Functions ] -----------------
	// ==============================================

	/**
	 * Validate a row against specified conditions
	 * @param   row          array with values from a row
	 * @param   conditions   specified conditions that the row must match
	 * @return  true of false
	 */
	function _validate_row_conditions($row = array(), $conditions = null) {
		if (!empty($row)) {
			if (!empty($conditions)) {
				$conditions = (strpos($conditions, ' OR ') !== false) ? explode(' OR ', $conditions) : array($conditions);
				$or = '';
				foreach ($conditions as $key => $value) {
					if (strpos($value, ' AND ') !== false) {
						$value = explode(' AND ', $value);
						$and = '';
						foreach ($value as $k => $v) {
							$and .= $this->_validate_row_condition($row, $v);
						}
						$or .= (strpos($and, '0') !== false) ? '0' : '1';
					} else {
						$or .= $this->_validate_row_condition($row, $value);
					}
				}
				return (strpos($or, '1') !== false) ? true : false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Validate a row against a single condition
	 * @param   row          array with values from a row
	 * @param   condition   specified condition that the row must match
	 * @return  true of false
	 */
	function _validate_row_condition($row, $condition) {
		$operators = array('=', 'equals', 'is', '!=', 'is not', '<', 'is less than', '>', 'is greater than', '<=', 'is less than or equals', '>=', 'is greater than or equals', 'contains', 'does not contain',);
		$operators_regex = array();
		foreach ($operators as $value) {
			$operators_regex[] = preg_quote($value, '/');
		}
		$operators_regex = implode('|', $operators_regex);
		if (preg_match('/^(.+) (' . $operators_regex . ') (.+)$/i', trim($condition), $capture)) {
			$field = $capture[1];
			$op = $capture[2];
			$value = $capture[3];
			if (preg_match('/^([\'\"]{1})(.*)([\'\"]{1})$/i', $value, $capture)) {
				if ($capture[1] == $capture[3]) {
					$value = $capture[2];
					$value = str_replace("\\n", "\n", $value);
					$value = str_replace("\\r", "\r", $value);
					$value = str_replace("\\t", "\t", $value);
					$value = stripslashes($value);
				}
			}
			if (array_key_exists($field, $row)) {
				if (($op == '=' || $op == 'equals' || $op == 'is') && $row[$field] == $value) {
					return '1';
				} elseif (($op == '!=' || $op == 'is not') && $row[$field] != $value) {
					return '1';
				} elseif (($op == '<' || $op == 'is less than') && $row[$field] < $value) {
					return '1';
				} elseif (($op == '>' || $op == 'is greater than') && $row[$field] > $value) {
					return '1';
				} elseif (($op == '<=' || $op == 'is less than or equals') && $row[$field] <= $value) {
					return '1';
				} elseif (($op == '>=' || $op == 'is greater than or equals') && $row[$field] >= $value) {
					return '1';
				} elseif ($op == 'contains' && preg_match('/' . preg_quote($value, '/') . '/i', $row[$field])) {
					return '1';
				} elseif ($op == 'does not contain' && !preg_match('/' . preg_quote($value, '/') . '/i', $row[$field])) {
					return '1';
				} else {
					return '0';
				}
			}
		}
		return '1';
	}

	/**
	 * Validates if the row is within the offset or not if sorting is disabled
	 * @param   current_row   the current row number being processed
	 * @return  true of false
	 */
	function _validate_offset($current_row) {
		if ($this->sort_by === null && $this->offset !== null && $current_row < $this->offset) {
			return false;
		}
		return true;
	}

	/**
	 * Enclose values if needed
	 *  - only used by unparse()
	 * @param   value   string to process
	 * @return  Processed value
	 */
	function _enclose_value($value = null) {
		if ($value !== null && $value != '') {
			$delimiter = preg_quote($this->delimiter, '/');
			$enclosure = preg_quote($this->enclosure, '/');
			if($value[0]=='=') $value="'".$value;
			if (preg_match("/" . $delimiter . "|" . $enclosure . "|\n|\r/i", $value) || ($value{0} == ' ' || substr($value, -1) == ' ')) {
				$value = str_replace($this->enclosure, $this->enclosure . $this->enclosure, $value);
				$value = $this->enclosure . $value . $this->enclosure;
			}
		}
		return $value;
	}

	/**
	 * Check file data
	 * @param   file   local filename
	 * @return  true or false
	 */
	function _check_data($file = null) {
		if (empty($this->file_data)) {
			if ($file === null) {
				$file = $this->file;
			}
			return $this->load_data($file);
		}
		return true;
	}


	/**
	 * Check if passed info might be delimiter
	 *  - only used by find_delimiter()
	 * @return  special string used for delimiter selection, or false
	 */
	function _check_count($char, $array, $depth, $preferred) {
		if ($depth == count($array)) {
			$first = null;
			$equal = null;
			$almost = false;
			foreach ($array as $key => $value) {
				if ($first == null) {
					$first = $value;
				} elseif ($value == $first && $equal !== false) {
					$equal = true;
				} elseif ($value == $first + 1 && $equal !== false) {
					$equal = true;
					$almost = true;
				} else {
					$equal = false;
				}
			}
			if ($equal) {
				$match = ($almost) ? 2 : 1;
				$pref = strpos($preferred, $char);
				$pref = ($pref !== false) ? str_pad($pref, 3, '0', STR_PAD_LEFT) : '999';
				return $pref . $match . '.' . (99999 - str_pad($first, 5, '0', STR_PAD_LEFT));
			} else {
				return false;
			}
		}
	}

	/**
	 * Read local file
	 * @param   file   local filename
	 * @return  Data from file, or false on failure
	 */
	function _rfile($file = null) {
		if (is_readable($file)) {
			if (!($fh = fopen($file, 'r'))) {
				return false;
			}
			$data = fread($fh, filesize($file));
			fclose($fh);
			return $data;
		}
		return false;
	}

	/**
	 * Write to local file
	 * @param   file     local filename
	 * @param   string   data to write to file
	 * @param   mode     fopen() mode
	 * @param   lock     flock() mode
	 * @return  true or false
	 */
	function _wfile($file, $string = '', $mode = 'wb', $lock = 2) {
		if ($fp = fopen($file, $mode)) {
			flock($fp, $lock);
			$re = fwrite($fp, $string);
			$re2 = fclose($fp);
			if ($re != false && $re2 != false) {
				return true;
			}
		}
		return false;
	}
}
