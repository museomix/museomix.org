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
 * "Copyright Smackcoders. 2014. All rights reserved".
 ********************************************************************************/
class WPImporter_includes_helper {

	public function __construct()
	{
		$this->getKeyVals();
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
	public $updatedPostCount=0;

	// @var string delimiter
	public $delim = ",";

	// @var array delilimters supported by CSV importer
	public $delim_avail = array(
			',',
			';'
			);

	// @var array wp field keys
	public $keys = array();

	// @var array for default columns
	public $defCols = array(
			'post_title' => null,
			'post_content' => null,
			'post_excerpt' => null,
			'post_date' => null,
			'post_name' => null,
			'post_tag' => null,
			'post_category' => null,
			'post_author' => null,
			'featured_image' => null,
			'post_parent' => 0,
			'post_status' => 0,
			'menu_order'  => 0,
                        'post_format' => 0,
                        'wp_page_template' => null,
			);

	// @var array CSV headers
	public $headers = array();

	public $capturedId=0;

	public $detailedLog = array();

	/* getImportDataConfiguration */
	public function getImportDataConfiguration(){
		$importDataConfig = "<div class='importstatus'id='importallwithps_div'>
			<table><tr><td>
			<label>Import with post status</label><span class='mandatory'> *</span></td><td>
			<div style='float:left;'>
			<select name='importallwithps' id='importallwithps' onChange='selectpoststatus();' >
			<option value='0'>Status as in CSV</option>
			<option value='1'>Publish</option>
			<option value='2'>Sticky</option>
			<option value='4'>Private</option>
			<option value='3'>Protected</option>
			<option value='5'>Draft</option>
			<option value='6'>Pending</option>
			</select></div>
			<div style='float:right;'>
			<a href='#' class='tooltip'>
			<img src='".WP_CONST_ULTIMATE_CSV_IMP_DIR."images/help.png' />
			<span class='tooltipPostStatus'>
			<img class='callout' src='".WP_CONST_ULTIMATE_CSV_IMP_DIR."images/callout.gif' />
			Select the status for the post  imported, if not defined within your csv .E.g.publish
			<img src='". WP_CONST_ULTIMATE_CSV_IMP_DIR."images/help.png' style='margin-top: 6px;float:right;' />
			</span></a> </div>
			</td></tr><tr><td>
			<div id='globalpassword_label' class='globalpassword' style='display:none;'><label>Password</label><span class='mandatory'> *</span></div></td><td>
			<div id='globalpassword_text' class='globalpassword' style='display:none;'><input type = 'text' id='globalpassword_txt' name='globalpassword_txt' placeholder='Password for all post'></div></td></tr></table>
			</div>";
		return $importDataConfig;
	}

	/**
	 * Get upload directory
	 */
	public function getUploadDirectory($check = 'plugin_uploads')
	{
		$upload_dir = wp_upload_dir();
		if($check == 'plugin_uploads'){
			return $upload_dir ['basedir'] . "/" . $this->uploadDir;
		}else{
			return $upload_dir ['basedir'];
		}
	}

	/**
	 *	generate help tooltip
	 *	@param string $content ** content to show on tooltip **
	 *	@return string $html ** generated HTML **
	 **/
	public function generatehelp($content, $mapping_style = NULL)
	{
		$html = '<div style = "'.$mapping_style.'"> <a href="#" class="tooltip">
			<img src="'.WP_CONST_ULTIMATE_CSV_IMP_DIR.'images/help.png" />
			<span class="tooltipPostStatus">
			<img class="callout" src="'.WP_CONST_ULTIMATE_CSV_IMP_DIR.'images/callout.gif" />
			'.$content.'
			<img src="'.WP_CONST_ULTIMATE_CSV_IMP_DIR.'images/help.png" style="margin-top: 6px;float:right;" />
			</span> </a> </div>';
		return $html;
	}

	public static function output_fd_page()
	{
              $get_pluginData = get_plugin_data(plugin_dir_path(__FILE__) . '../index.php');
		$plugin_version = get_option('ULTIMATE_CSV_IMPORTER_UPGRADE_FREE_VERSION');
		if(!$plugin_version) {
			$plugin_version = get_option('ULTIMATE_CSV_IMP_FREE_VERSION');
		}
		if ($get_pluginData['Version'] == '3.6' && $plugin_version == '') {
			if (file_exists(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . '/upgrade/migrationfreev3.6.php')) {
				require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . '/upgrade/migrationfreev3.6.php');
                                die();  
			}
		}
		else if (!isset($_REQUEST['__module']))
		{
                          if (!isset($_REQUEST['__module'])) {
                                wp_redirect(get_admin_url() . 'admin.php?page=' . WP_CONST_ULTIMATE_CSV_IMP_SLUG . '/index.php&__module=dashboard');

		}
                }
		require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY.'config/settings.php');
		require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY.'lib/skinnymvc/controller/SkinnyController.php');

		$c = new SkinnyControllerWPCsvFree;
		$c->main();
	}

	public function getSettings(){
		return get_option('wpcsvfreesettings');
	}

	public function renderMenu()
	{
		include(plugin_dir_path(__FILE__) . '../templates/menu.php');
	}

	public function requestedAction($action,$step){
		$actions = array('dashboard','settings','help','users','comments','eshop','wpcommerce','woocommerce','categories','customtaxonomy','export', 'mappingtemplate');
		if(!in_array($action,$actions)){
			include(plugin_dir_path(__FILE__) . '../templates/view.php');
		}else{
			include(plugin_dir_path(__FILE__) . '../modules/'.$action.'/actions/actions.php');
			include(plugin_dir_path(__FILE__) . '../modules/'.$action.'/templates/view.php');
		}
	}

	/**
	 * Move CSV to the upload directory
	 */
	public function move_file()
	{
		if ($_FILES ["csv_import"] ["error"] == 0) {
			$tmp_name = $_FILES ["csv_import"] ["tmp_name"];
			$this->csvFileName = $_FILES ["csv_import"] ["name"];
			move_uploaded_file($tmp_name, $this->getUploadDirectory() . "/$this->csvFileName");
		}
	}

	/**
	 * Check upload dirctory permission
	 */
	function checkUploadDirPermission()
	{
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
	function getKeyVals()
	{
		$cust_fields='';
		$acf_field=array();
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
		if($wpcsvfreesettings)
                if(in_array('aioseo',$wpcsvfreesettings)){
                        if(in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins)){
                                $seo_custoFields =array('SEO: keywords','SEO: description','SEO: title','SEO: noindex','SEO: nofollow','SEO: titleatr','SEO: menulabel','SEO: disable','SEO: disable_analytics','SEO: noodp','SEO: noydir');
                                foreach($seo_custoFields as $val)
                                        $this->defCols[$val]=$val;
                        }
                }
	}

	/**
	 * Function converts CSV data to formatted array.
	 * @param $file CSV input filename
	 * @param $delim delimiter for the CSV
	 * @return array formatted CSV output as array
	 */
	function csv_file_data($file)
	{
		$file = $this->getUploadDirectory().'/'.$file;
                require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY.'includes/Importer.php');
		$csv = new ImporterLib();
		$csv->delim($file);
		foreach($csv->data as $hkey => $hval) {
			foreach($hval as $hk => $hv) {
				$this->headers[] = $hk;
			}
			break;
		}
		return $csv->data; 
	}


	/**
	 * Manage duplicates
	 *
	 * @param string type = (title|content), string content
	 * @return boolean
	 */
	function duplicateChecks($type = 'title', $text, $gettype, $currentLimit, $postTitle)
	{
		global $wpdb;
		if ($type == 'content') {
			$htmlDecode = html_entity_decode($text);
			$strippedText = strip_tags($htmlDecode);
			$contentLength = strlen($strippedText);
			$allPosts_count = $wpdb->get_results("SELECT COUNT(ID) as count FROM $wpdb->posts WHERE post_type = \"{$gettype}\" and post_status IN('publish','future','draft','pending','private')");
			$allPosts_count = $allPosts_count[0]->count;
			$allPosts = $wpdb->get_results("SELECT ID,post_title,post_date,post_content FROM $wpdb->posts WHERE post_type = \"{$gettype}\" and post_status IN('publish','future','draft','pending','private')");
			foreach ($allPosts as $allPost) {
				$htmlDecodePCont = html_entity_decode($allPost->post_content);
				$strippedTextPCont = strip_tags($htmlDecodePCont);
				similar_text($strippedTextPCont, $strippedText, $p);
				if ($p == 100) {
					$this->dupPostCount++;
					$this->detailedLog[$currentLimit]['post_id'] = "Created record no $currentLimit - failed";
					return false;
				}
			}
			return true;
		} else if ($type == 'title') {
			$post_exist = $wpdb->get_results("select ID from " . $wpdb->posts . " where post_title = \"{$text}\" and post_type = \"{$gettype}\" and post_status in('publish','future','draft','pending','private')");
			if (count($post_exist) == 0 && ($text != null || $text != ''))
				return true;
		}
		$this->dupPostCount++;
		$this->detailedLog[$currentLimit]['post_id'] = "Created record no $currentLimit - failed";
		return false;
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
	public static function get_fimg_from_URL($f_img, $fimg_path, $fimg_name, $post_slug_value, $currentLimit = null, $logObj = ""){
		if($fimg_path!="" && $fimg_path){
			$fimg_path = $fimg_path . "/" . $post_slug_value . "-" . $fimg_name;
		}
		$ch = curl_init ($f_img);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$rawdata = curl_exec($ch);
		if(strpos($rawdata, 'Not Found') != 0) {
			$rawdata = false;
		}
		if ($rawdata == false) {
			if ($logObj == '') {
				$this->detailedLog[$currentLimit]['image'] = "<b>Image -</b> host not resolved";
			} else {
				$logObj->detailedLog[$currentLimit]['image'] = "<b>Image -</b> host not resolved";
			}
		} else {
                        if (file_exists($fimg_path)) {
                                unlink($fimg_path);
                        }
                        $fp = fopen($fimg_path, 'x');
                        fwrite($fp, $rawdata);
                        fclose($fp);
                        $logObj->detailedLog[$currentLimit]['image'] = "<b>Image -</b>" . $fimg_name;
                }
		curl_close($ch);
	}

	/**
	 * function to map the csv file and process it
	 *
	 * @return boolean
	 */
	function processDataInWP($data_rows,$ret_array,$session_arr,$currentLimit)
	{
		global $wpdb;
		$post_id = '';
		$new_post = array();
		$smack_taxo = array();
		$custom_array = array();
		$seo_custom_array= array();		
		$imported_feature_img = array();
		$headr_count = $ret_array['h2'];
		for ($i = 0; $i < count($data_rows); $i++) {
			if (array_key_exists('mapping' . $i, $ret_array)) { 
				if($ret_array ['mapping' . $i] != '-- Select --'){
					if ($ret_array ['mapping' . $i] != 'add_custom' . $i) {
						$strip_CF = strpos($ret_array['mapping' . $i], 'CF: ');
						$strip_SEO = strpos($ret_array['mapping'.$i],'SEO: ');
						if ($strip_CF === 0) {
							$custom_key = substr($ret_array['mapping' . $i], 4);
							$custom_array[$custom_key] = $data_rows[$i];
						} 
						elseif($strip_SEO === 0){
                                                        $seo_key = substr($ret_array['mapping'.$i], 5);
                                                        $seo_custom_array[$seo_key] = $data_rows[$i];
                                                }
						else {
							$new_post[$ret_array['mapping' . $i]] = $data_rows[$i];
						}
					} else {
						$new_post [$ret_array ['textbox' . $i]] = $data_rows [$i];
						$custom_array [$ret_array ['textbox' . $i]] = $data_rows [$i];
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
		if(is_array( $new_post )){
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
							if($get_media_settings == 1){
								$dirname = date('Y') . '/' . date('m');
								$full_path = $dir ['basedir'] . '/' . $dirname;
								$baseurl = $dir ['baseurl'] . '/' . $dirname;
							}else{
								$full_path = $dir ['basedir'];
								$baseurl = $dir ['baseurl'];
							}

							$f_img = $new_post [$ckey];
							$fimg_path = $full_path;

							$fimg_name = @basename($f_img);
							$fimg_name = preg_replace("/[^a-zA-Z0-9._\s]/", "", $fimg_name);
							$fimg_name = preg_replace('/\s/', '-', $fimg_name);
							$fimg_name = urlencode($fimg_name);
							
							$parseURL = parse_url($f_img);
							$path_parts = pathinfo($f_img);
							if(!isset($path_parts['extension']))
								$fimg_name = $fimg_name . '.jpg';

							$f_img_slug = preg_replace("/[^a-zA-Z0-9._\s]/", "", $new_post['post_title']);
							$f_img_slug = preg_replace('/\s/', '-', $f_img_slug);

							$post_slug_value = strtolower($f_img_slug);
							$this->get_fimg_from_URL($f_img, $fimg_path, $fimg_name, $post_slug_value, $currentLimit, $this);
							$filepath = $fimg_path."/" . $post_slug_value . "-" . $fimg_name;
	
							if(@getimagesize($filepath)){
								$img = wp_get_image_editor($filepath);
								if (!is_wp_error($img)) {
									$sizes_array = array(
											// #1 - resizes to 1024x768 pixel, square-cropped image
											array('width' => 1024, 'height' => 768, 'crop' => true),
											// #2 - resizes to 100px max width/height, non-cropped image
											array('width' => 100, 'height' => 100, 'crop' => false),
											// #3 - resizes to 100 pixel max height, non-cropped image
											array('width' => 300, 'height' => 100, 'crop' => false),
											// #3 - resizes to 624x468 pixel max width, non-cropped image
											array('width' => 624, 'height' => 468, 'crop' => false)
											);
									$resize = $img->multi_resize($sizes_array);
								}
								$file ['guid'] = $baseurl."/".$fimg_name;
								$file ['post_title'] = $fimg_name;
								$file ['post_content'] = '';
								$file ['post_status'] = 'attachment';
							}
							else	{
								$file = false;
							}
							break;
					}
				}
			}
		}

		if($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'] != 'custompost'){
			$data_array['post_type'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter'];
		}else{
			$data_array['post_type'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['custompostlist'];
		}
		if ($this->titleDupCheck == 'true')
			$this->postFlag = $this->duplicateChecks('title', $data_array ['post_title'], $data_array ['post_type'], $currentLimit, $data_array ['post_title']);

		if ($this->conDupCheck == 'true' && $this->postFlag)
			$this->postFlag = $this->duplicateChecks('content', $data_array ['post_content'], $data_array ['post_type'], $currentLimit, $data_array ['post_title']);

		if ($this->postFlag) {
			unset ($sticky);
			if (empty($data_array['post_status']))
				$data_array['post_status'] = null;

			if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['importallwithps'] != 0)
				$data_array['post_status'] = $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['importallwithps'];

			switch ($data_array ['post_status']) {
				case 1 :
					$data_array['post_status'] = 'publish';
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>publish";
					break;
				case 2 :
					$data_array['post_status'] = 'publish';
					$sticky = true;
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>sticky";
					break;
				case 3 :
					$data_array['post_status'] = 'publish';
					$data_array ['post_password'] = $_POST ['postsPassword'];
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>protected with password " . $data_array['post_password'];
					break;
				case 4 :
					$data_array ['post_status'] = 'private';
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>private";
					break;
				case 5 :
					$data_array ['post_status'] = 'draft';
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>draft";
					break;
				case 6 :
					$data_array ['post_status'] = 'pending';
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>pending";
					break;
				default :
					$poststatus = $data_array['post_status'] = strtolower($data_array['post_status']);
					if ($data_array['post_status'] != 'publish' && $data_array['post_status'] != 'private' && $data_array['post_status'] != 'draft' && $data_array['post_status'] != 'pending' && $data_array['post_status'] != 'sticky') {
						$stripPSF = strpos($data_array['post_status'], '{');
						if ($stripPSF === 0) {
							$poststatus = substr($data_array['post_status'], 1);
							$stripPSL = substr($poststatus, -1);
							if ($stripPSL == '}') {
								$postpwd = substr($poststatus, 0, -1);
								$data_array['post_status'] = 'publish';
								$data_array ['post_password'] = $postpwd;
								$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>protected with password " . $data_array['post_password'];
							} else {
								$data_array['post_status'] = 'publish';
								$data_array ['post_password'] = $poststatus;
								$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>protected with password " . $data_array['post_password'];
							}
						} else {
							$data_array['post_status'] = 'publish';
							$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>publish";
						}
					}
					if ($data_array['post_status'] == 'sticky') {
						$data_array['post_status'] = 'publish';
						$sticky = true;
						$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>sticky";
					}
					else {
						$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>" . $data_array['post_status'];
					}
			}
                         // Post Format Options

	    if(isset($data_array ['post_format'])) {
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
				    $post_format = 0;

		    }
	    }


			// Author name/id update
			if(isset($data_array ['post_author'])){
				$authorLen = strlen($data_array ['post_author']);
				$postuserid = $data_array ['post_author'];
				$checkpostuserid = intval($data_array ['post_author']);
				$postAuthorLen = strlen($checkpostuserid);
				$postauthor = array();

				if ($authorLen == $postAuthorLen) {
					$postauthor = $wpdb->get_results("select ID,user_login from $wpdb->users where ID = \"{$postuserid}\"");
					if(empty($postauthor) || !$postauthor[0]->ID) { // If user name are numeric Ex: 1300001
						$postauthor = $wpdb->get_results("select ID,user_login from $wpdb->users where user_login = \"{$postuserid}\"");
					}
				} else {
					$postauthor = $wpdb->get_results("select ID,user_login from $wpdb->users where user_login = \"{$postuserid}\"");
				}

				if (empty($postauthor) || !$postauthor[0]->ID) {
					$data_array ['post_author'] = 1;
					$admindet = $wpdb->get_results("select ID,user_login from $wpdb->users where ID = 1");
                                        $this->detailedLog[$currentLimit]['assigned_author'] = "<b>Author - not found (assigned to </b>" . $admindet[0]->user_login . ")";
					$this->noPostAuthCount++;
				} else {
					$data_array ['post_author'] = $postauthor [0]->ID;
					$this->detailedLog[$currentLimit]['assigned_author'] = "<b>Author - </b>" . $postauthor[0]->user_login;
				}
			}
			else{
				$data_array ['post_author'] = 1;
				$admindet = $wpdb->get_results("select ID,user_login from $wpdb->users where ID = 1");
                                $this->detailedLog[$currentLimit]['assigned_author'] = "<b>Author - not found (assigned to </b>" . $admindet[0]->user_login . ")";
				$this->noPostAuthCount++;
			}

			// Date format post
			$data_array ['post_date'] = str_replace('/', '-', $data_array ['post_date']);
			if (!isset($data_array ['post_date'])){
				$data_array ['post_date'] = date('Y-m-d H:i:s');
				$this->detailedLog[$currentLimit]['postdate'] = "<b>Date - </b>" . $data_array ['post_date'];
			}else{
				$data_array ['post_date'] = date('Y-m-d H:i:s', strtotime($data_array ['post_date']));
				$this->detailedLog[$currentLimit]['postdate'] = "<b>Date - </b>" . $data_array ['post_date'];
			}
			if(isset($data_array ['post_slug'])){
				$data_array ['post_name'] = $data_array ['post_slug'];
			}

			//add global password
			if($data_array){
				if($ret_array['importallwithps'] == 3){
					$data_array['post_password'] = $ret_array['globalpassword_txt'];
					$this->detailedLog[$currentLimit]['poststatus'] = "<b>Status - </b>protected with password " . $ret_array['globalpassword_txt'];
				}
			}
			if ($data_array) {
				$post_id = wp_insert_post($data_array);
				$this->detailedLog[$currentLimit]['post_id'] = "<b>Created Post_ID - </b>" . $post_id . " - success";
			}
			unset($postauthor);
			if ($post_id) {
				$uploaded_file_name=$session_arr['uploadedFile'];
				$real_file_name = $session_arr['uploaded_csv_name'];
				//                                $version = $session_arr['currentfileversion'];
				$action = $data_array['post_type'];
				/*				$version_arr=array();
								$version_arr=explode("(",$uploaded_file_name);
								$version_arr=explode(")",$version_arr[1]);
								$version=$version_arr[0]; */
				$get_imported_feature_image = array();
				$get_imported_feature_image = get_option('IMPORTED_FEATURE_IMAGES');
				if(is_array($get_imported_feature_image)){
					$imported_feature_img = array_merge($get_imported_feature_image, $imported_feature_img);
				}
				else{
					$imported_feature_img = $imported_feature_img;
				}
				update_option('IMPORTED_FEATURE_IMAGES', $imported_feature_img);
				$created_records[$action][] = $post_id;
				if($action == 'post'){
					$imported_as = 'Post';
				}
				if($action == 'page'){
					$imported_as = 'Page';
				}
				if($action != 'post' && $action != 'page'){
					$imported_as = 'Custom Post';
				}
				$keyword = $action;
				$this->insPostCount++;
				if (isset($sticky) && $sticky)
					stick_post($post_id);

				if (!empty ($custom_array)) {
					foreach ($custom_array as $custom_key => $custom_value) {
                                       update_post_meta($post_id, $custom_key, $custom_value);
					}
				}
                                		

                		// Import post formats added by fredrick marks
		              if(isset($post_format)) {
			       wp_set_object_terms($post_id, $post_format, 'post_format');

		                }          
                                //Import SEO Values     
                                if(!empty($seo_custom_array)){
                                        $this->importSEOfields($seo_custom_array,$post_id);
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
					$this->detailedLog[$currentLimit]['tags'] = "<b>Tags - </b>" .substr($this->detailedLog[$currentLimit]['tags'], 0, -1);
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
					foreach($assigned_categories as $cateKey => $cateVal) {
                                                $this->detailedLog[$currentLimit]['category'] .= $cateKey . "|";
                                        }
                                        $this->detailedLog[$currentLimit]['category'] = "<b>Category - </b>" .substr($this->detailedLog[$currentLimit]['category'], 0, -1);
					wp_set_object_terms($post_id, $split_cate, 'category');
				}
				// Add featured image
				if (!empty ($file)) {
					//$wp_filetype = wp_check_filetype(@basename($file ['guid']), null);
					$wp_upload_dir = wp_upload_dir();
					$attachment = array(
							'guid' => $file ['guid'],
							'post_mime_type' => 'image/jpeg',
							'post_title' => preg_replace('/\.[^.]+$/', '', @basename($file ['guid'])),
							'post_content' => '',
							'post_status' => 'inherit'
							);
					if($get_media_settings == 1){
						$generate_attachment = $dirname . '/' . $post_slug_value . '-' .  $fimg_name;
					}else{
						$generate_attachment = $fimg_name;
					}
					$uploadedImage = $wp_upload_dir['path'] . '/' . $post_slug_value . '-' . $fimg_name;
					$attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
					$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
					wp_update_attachment_metadata($attach_id, $attach_data);
					set_post_thumbnail($post_id, $attach_id);
				}
			}
			else{
				$skippedRecords[] = $_SESSION['SMACK_SKIPPED_RECORDS'];
			}
		}
		$this->detailedLog[$currentLimit]['verify_here'] = "<b>Verify Here -</b> <a href='" . get_permalink( $post_id ) . "' title='" . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $data_array['post_title'] ) ) . "' rel='permalink'>" . __( 'Web View' ) . "</a> | <a href='" . get_edit_post_link( $post_id, true ) . "' title='" . esc_attr( __( 'Edit this item' ) ) . "'>" . __( 'Admin View' ) . "</a>";

		unset($data_array);
	}

	// Create Data base for Statistic chart
	public static function activate() {
		if (!defined('PDO::ATTR_DRIVER_NAME')) {
			echo("Make sure you have enable the PDO extensions in your environment before activate the plugin!");
			die;
		}
		global $wpdb;
		$sql1="CREATE TABLE `smackcsv_pie_log` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`type` varchar(255) DEFAULT NULL,
			`value` int(11) DEFAULT NULL,
			PRIMARY KEY (`id`)
				) ENGINE=InnoDB;";

		$sql2="CREATE TABLE `smackcsv_line_log` (
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
		$importedTypes = array('Post','Page','Custom Post','Comments','Users','Eshop');
		foreach($importedTypes as $importedType){
			$querycheck = $wpdb->get_results("select *from smackcsv_pie_log where type = \"{$importedType}\"");
			if (count($querycheck) == 0){
				$sql4 = "insert into smackcsv_pie_log (type,value) values(\"$importedType\",0)";
				$wpdb->query($sql4);
			}
		}
	}

        //Drop Database While Deactivate plugin
        public function deactivate() {
                         global $wpdb;
                            $sql1 = "DROP TABLE smackcsv_pie_log;";
                           $wpdb->query($sql1);
 
                           $sql2 = "DROP TABLE smackcsv_line_log;";
                           $wpdb->query($sql2);


              }
         public function addPieChartEntry($imported_as, $count) {
                //add total counts
          global $wpdb;
          $getTypeID = $wpdb->get_results("select * from smackcsv_pie_log where type = '$imported_as'");
          if(count($getTypeID) == 0)
          $wpdb->insert('smackcsv_pie_log',array('type'=>$imported_as,'value'=>$count));       
          else
          $wpdb->update('smackcsv_pie_log', array('value' =>$getTypeID[0]->value+$count), array('id'=>$getTypeID[0]->id));
        }
         function addStatusLog($inserted,$imported_as){
                global $wpdb;
                $today = date('Y-m-d h:i:s');
                $mon = date("M",strtotime($today));
                $year = date("Y",strtotime($today));
                $wpdb->insert('smackcsv_line_log', array('month'=>$mon,'year'=>$year,'imported_type'=>$imported_as,'imported_on'=>date('Y-m-d h:i:s'), 'inserted'=>$inserted ));
        }

	/**
	 * Function for importing the all in seo data 
	 * Feature added by Fredrick on version3.5.4
	 */
	function importSEOfields($array,$postId)
	{
		$seo_opt = get_option('wpcsvfreesettings');
		if(in_array('aioseo',$seo_opt)){
			if(isset($array['keywords'])) {    $custom_array['_aioseop_keywords'] = $array['keywords']; } 
			if(isset($array['description'])) { $custom_array['_aioseop_description'] = $array['description']; }
			if(isset($array['title'])) {       $custom_array['_aioseop_title'] = $array['title']; }
			if(isset($array['noindex'])) {     $custom_array['_aioseop_noindex'] = $array['noindex']; }
			if(isset($array['nofollow'])) {    $custom_array['_aioseop_nofollow'] = $array['nofollow']; }
			if(isset($array['titleatr'])) {    $custom_array['_aioseop_titleatr'] = $array['titleatr']; }
			if(isset($array['menulabel'])) {   $custom_array['_aioseop_menulabel'] = $array['menulabel']; }
			if(isset($array['disable'])) {     $custom_array['_aioseop_disable'] = $array['disable']; }
			if(isset($array['disable_analytics'])) { $custom_array['_aioseop_disable_analytics'] = $array['disable_analytics']; }
			if(isset($array['noodp'])) { $custom_array['_aioseop_noodp'] = $array['noodp']; }
			if(isset($array['noydir'])) { $custom_array['_aioseop_noydir'] = $array['noydir']; }
		}
		if (! empty ( $custom_array )) {
			foreach ( $custom_array as $custom_key => $custom_value ) {
				update_post_meta ( $postId, $custom_key, $custom_value );
			}
		}

	}//importSEOfields ends

	/**
	 * Delete uploaded file after import process
	 */
	function deletefileafterprocesscomplete($uploadDir) {
		//array_map('unlink', glob("$uploadDir/*"));
		$files = array_diff(scandir($uploadDir), array('.','..')); 
		foreach ($files as $file) { 
			(is_dir("$uploadDir/$file")) ? rmdir("$uploadDir/$file") : unlink("$uploadDir/$file"); 
		} 
	}

	// Function convert string to hash_key
	public function convert_string2hash_key($value) {
		$file_name = hash_hmac('md5', "$value", 'secret');
		return $file_name;
	}

	// Function for common footer
	public function common_footer_for_other_plugin_promotions(){
		$content = '<div class="accordion-inner">
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/" target="_blank">Social All in One Bot</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/google-seo-author-snippet-plugin/" target="_blank">Google SEO Author Snippet</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/" target="_blank">WP Advanced Importer</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/" target="_blank">WP Sugar</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/category/free-wordpress-plugins/" target="_blank">WP Zoho crm Sync</a></label>

			<label class="plugintags"><a href="http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html" target="_blank">WP Ultimate CSV Importer Pro</a></label>
			<label class="plugintags"><a href="http://www.smackcoders.com/wordpress-sugar-integration-automated-multi-web-forms-generator-pro.html" target="_blank">WordPress Sugar Pro</a></label>
			<div style="position:relative;float:right;"><a href="http://www.smackcoders.com/"><img width=80 src="http://www.smackcoders.com/skin/frontend/default/megashop/images/logo.png" /></a></div>
			</div>';
		echo $content;
	}

	// Function for social sharing
	public function importer_social_profile_share() {
		$urlCurrentPage = "http://www.smackcoders.com/wp-ultimate-csv-importer.html";
		$fbimgsrc = WP_CONTENT_URL . "/plugins/" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/images/facebook.png";
		$googleimgsrc = WP_CONTENT_URL . "/plugins/" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/images/googleplus.png";
		$linkedimgsrc = WP_CONTENT_URL . "/plugins/" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/images/linkedin.png";
		$twitimgsrc = WP_CONTENT_URL . "/plugins/" . WP_CONST_ULTIMATE_CSV_IMP_SLUG . "/images/twitter.png";
		$strPageTitle = 'WP Ultimate CSV Importer';
		$linked_in_username = 'smackcoders';

		//Facebook
		$htmlShareButtons = '<span class="sociallink">';
		$htmlShareButtons .= '<a id="wpcsv_facebook_share" href="http://www.facebook.com/sharer.php?u=' . $urlCurrentPage  . '" target="_blank">';
		$htmlShareButtons .= '<img title="Facebook" class="wpcsv" src="' . $fbimgsrc . '" alt="Facebook" />';
		$htmlShareButtons .= '</a>';
		$htmlShareButtons .= '</span>';

		//Google Plus
		$htmlShareButtons .= '<span class="sociallink">';
		$htmlShareButtons .= '<a id="wpcsv_google_share" href="https://plus.google.com/share?url=' . $urlCurrentPage  . '" target="_blank" >';
		$htmlShareButtons .= '<img title="Google+" class="wpcsv" src="' . $googleimgsrc . '" alt="Google+" />';
		$htmlShareButtons .= '</a>';
		$htmlShareButtons .= '</span>';

		//Linked in
		$htmlShareButtons .= '<span class="sociallink">';
		$htmlShareButtons .= '<a id="wpcsv_linkedin_share" class="wpcsv_share_link" href="http://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($urlCurrentPage)  . '&title='.urlencode($strPageTitle).'&source='.$linked_in_username.'" target="_blank" >';
		$htmlShareButtons .= '<img title="LinkedIn" class="wpcsv" src="' . $linkedimgsrc . '" alt="LinkedIn" />';
		$htmlShareButtons .= '</a>';
		$htmlShareButtons .= '</span>';

		//Twitter
		$username = "smackcoders";
		// format the URL into friendly code
		$twitterShareText = urlencode(html_entity_decode($strPageTitle . ' ', ENT_COMPAT, 'UTF-8'));
		// twitter share link
		$htmlShareButtons .= '<span class="sociallink">';
		$htmlShareButtons .= '<a id="wpcsv_twitter_share" href="http://twitter.com/share?url=' . $urlCurrentPage .'&via='.$username.'&related='.$username.'&text=' . $twitterShareText . '" target="_blank">';
		$htmlShareButtons .= '<img title="Twitter" class="wpcsv" src="' . $twitimgsrc . '" alt="Twitter" />';
		$htmlShareButtons .= '</a>';
		$htmlShareButtons .= '</span>';
		echo $htmlShareButtons;
	}

	public function common_footer() {
		$get_pluginData = get_plugin_data(plugin_dir_path( __FILE__ ).'../index.php');
		$footer = '';
		$footer .= '<div style="padding:10px;">';
		$footer .= '<label class="plugintags"><a href="http://www.wpultimatecsvimporter.com" target="_blank">Home</a></label>                              <label class="plugintags"><a href="http://wiki.smackcoders.com/WP_Ultimate_CSV_Importer" target="_blank">Wiki</a></label>
	 <label class="plugintags"><a href="http://www.wpultimatecsvimporter.com" target="_blank">Tutorials</a></label>                  	<label class="plugintags"><a href="http://wiki.smackcoders.com/WP_Ultimate_CSV_Importer_Videos" target="_blank">Videos</a></label>
			<label class="plugintags"><a href="http://blog.smackcoders.com/wordpress-ultimate-csv-importer-csv-sample-files-and-updates.html" target="_blank">Sample Files</a></label>';
		$footer .= '</div>';
		$footer .= '<div style="padding:10px;margin-bottom:20px;">';
		if(isset ($_REQUEST['__module']) && $_REQUEST['__module'] != 'settings')
                       $footer .= "<div style='float:right;margin-top:-49px;'><a class='label label-info' href='".get_admin_url()."admin.php?page=".WP_CONST_ULTIMATE_CSV_IMP_SLUG."/index.php&__module=settings'>Click here to Enable any disabled module</a></div>";
		if(isset ($_REQUEST['__module']) && $_REQUEST['__module'] == 'settings') {
		   $footer .= "<div style='float:right;margin-top:-48px;'><span style='margin-right:20px;'><a class='label label-info' href='http://wordpress.org/plugins/wp-ultimate-csv-importer/developers/'>Get Old Versions</a></span><a class='label label-info' href='".get_admin_url()."admin.php?page=".WP_CONST_ULTIMATE_CSV_IMP_SLUG."/index.php&__module=support'>Click here to Get some useful links</a></div>";	
                   $footer .= "<div style='float:right;margin-right:15px;'> </span> Current Version: ".$get_pluginData['Version']." </div>";
		}
		if(isset ($_REQUEST['__module']) && $_REQUEST['__module'] != 'support' && $_REQUEST['__module'] != 'settings') {
		$footer .= "<div style='float:right;margin-right:225px;margin-top:-48px;'><span style='margin-right:20px;'> <a class='label label-info' href='http://wordpress.org/plugins/wp-ultimate-csv-importer/developers/'>Get Old Versions</a></span><a class='label label-info' href='".get_admin_url()."admin.php?page=".WP_CONST_ULTIMATE_CSV_IMP_SLUG."/index.php&__module=support'>Click here to Get some useful links</a></div>";	
                    $footer .= "<div style='float:right;margin-right:15px;'> Current Version: ".$get_pluginData['Version']." </div>";
		}
		if(isset ($_REQUEST['__module']) && $_REQUEST['__module'] == 'support'){
			$footer .= "<div style='float:right;margin-right:15px;'><span style='margin-right:20px;'>Current Version: ".$get_pluginData['Version']." </span><span style='margin-right:10px;'><a class='label label-info' href='http://wordpress.org/plugins/wp-ultimate-csv-importer/developers/'>Get Old Versions</a></span></div>";
		}
		$footer .= '</div>';
		$footer .= '<div style="float:right;margin-right:15px;margin-top:-10px;"> <label>Plugin By <a href="http://www.smackcoders.com"> Smackcoders</a></label> </div>';
                echo $footer;
	}

       function smack_csv_import_method() {
                
		$smack_csv_import_method = '<div class="importfileoption">
                         
			<div align="center" style="text-align:left;margin-top:-33px;">
			<div id="boxmethod1" class="method1">
			<label><span class="radio-icon"><input type="radio" name="importmethod" id="uploadfilefromcomputer" onclick="choose_import_method(this.id);" checked/></span> <span class="header-text">' . __('From Computer') . '</span> </label> <br>
			<!-- The fileinput-button span is used to style the file input field as button -->
			<div id="method1" style="display:block;height:40px;">
			<span class="btn btn-success fileinput-button">
			<span>' . __('Browse') . '</span>
			<input id="fileupload" type="file" name="files[]" multiple>
                        <a href="#" id="zip_process" style = "display:none">  Click Here To Process Zip </a>
			</span>';
		// The global progress bar 
		$smack_csv_import_method .= '<span style="padding-top:10px;">
			<div id="progress" class="progress">
			<div class="progress-bar progress-bar-success"></div>
			<div align="center" id="helpnotify" style="width:100%;"><p class="msgborder" style="color:green;">' . __('You can also drag and drop files here') . '</div>
			</div>
			</span>
			</div>
			</div>
                        <div  style = "opacity: 0.3;background-color: ghostwhite;">
                         <div id="boxmethod2" class="method2">
                        <label><span class="radio-icon"><input type="radio" name="importmethod" id="dwnldftpfile"  /></span> <span class="header-text">' . __('From FTP') . '</span> </label> <br>
                        </div>
                         <div id="boxmethod3" class="method3">
                        <label> <span class="radio-icon"><input type="radio" name="importmethod" id="dwnldextrfile"  /></span> <span class="header-text">' . __('From URL') . '</span></label> <br>
                         </div>
                          <div id="boxmethod4" class="method4">
                        <label><span class="radio-icon"><input type="radio" name="importmethod" id="useuploadedfile"  /></span> <span class="header-text">' . __('From Already Uploaded') . '</span></label> <br>
                         </div>
                         </div>

                        </div>
                        </div>';

		return $smack_csv_import_method;
	}
}


class CallWPImporterObj extends WPImporter_includes_helper
{
	private static $_instance = null;
	public static function getInstance()
	{
		if( !is_object(self::$_instance) )  //or if( is_null(self::$_instance) ) or if( self::$_instance == null )
			self::$_instance = new WPImporter_includes_helper();
		return self::$_instance;
	}
}



