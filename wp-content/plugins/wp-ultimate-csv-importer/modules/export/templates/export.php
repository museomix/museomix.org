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

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
if(!isset($_SERVER['HTTP_REFERER'])) {
	die('Your requested url were wrong! Please contact your admin.');
}
$nonce = $_POST['nonceKey'];
if ( ! wp_verify_nonce( $nonce, $_POST['action'] ) ) {
	// This nonce is not valid.
	die( 'Security check: Your requested URL is wrong! Please, Contact your administrator.' );
} else {
	// The nonce was valid.
	// Do stuff here.
}

$ExportObj = new WPCSVProExportData();
#$ExportObj->exportData($_POST);

class WPCSVProExportData {

	var $headers = array();

	var $module;                    // Ex: post (or) page (or) product

	var $exportType = 'csv';        // Ex: csv (or) xml

	var $optionalType = null;       // Can specify the Taxonomy (or) Custom Posts name to be export

	var $conditions = array();      // Conditional based export

	var $eventExclusions = array(); // Export with specific columns, Can exclude the unwanted headers.

	var $fileName;                  // Export the data with the specific filename.

	var $offset = 0;

	var $limit = 10;

	var $totalRowCount;

	var $data = array();

	# use first line/entry as field names
	var $heading = true;

	# delimiter (comma) and enclosure (double quote)
	var $delimiter = ',';
	var $enclosure = '"';

	# preferred delimiter characters, only used when all filtering method
	# returns multiple possible delimiters (happens very rarely)
	var $auto_preferred = ",;\t.:|";

	# only used by output() function
	var $output_delimiter = ',';

	var $linefeed = "\r\n";

	public function __construct() {
		$this->module          = sanitize_text_field($_POST['module']);
		$this->exportType      = 'csv';
		$this->conditions      = is_array($_POST['conditions']) && !empty($_POST['conditions']) ? $_POST['conditions'] : array();
		$this->optionalType    = sanitize_text_field($_POST['optionalType']);
		$this->eventExclusions = is_array($_POST['eventExclusions']) && !empty($_POST['eventExclusions']) ? $_POST['eventExclusions'] : array();
		$this->fileName        = !empty($_POST['fileName']) && $_POST['fileName'] != '' ? sanitize_text_field($_POST['fileName']) : 'exportas_'.date("Y").'-'.date("m").'-'.date("d"); //'Post.csv';
		$this->offset          = sanitize_text_field($_POST['offset']);
		$this->limit           = sanitize_text_field($_POST['limit']);
		#print_r($this); die;
		$this->exportData($_POST);
	}

	/**
	 * The actions index method
	 * @param array $request
	 * @return array
	 */
	public function exportData($request) {
		if(sanitize_text_field($request['module']) == 'categories') {
			$this->WPImpExportCategories($request);
		}
		else if(sanitize_text_field($request['module']) == 'tags') {
			$this->WPImpExportTags($request);
		}
		else if(sanitize_text_field($request['module']) == 'customtaxonomy') {
			$this->WPImpExportTaxonomies($request);
		}
		else if(sanitize_text_field($request['module']) == 'customerreviews') {
			$this->WPImpExportCustomerReviews($request);
		}
		else if(sanitize_text_field($request['module']) == 'comments') {
			$this->WPImpExportComments($request);
		}
		else if(sanitize_text_field($request['module']) == 'users') {
			$this->WPImpExportUsers($request);
		}
		else {
			$this->FetchDataByPostTypes($request);#die;
		}
	}

	/**
	 * @param $exclusionList
	 *
	 * @return array
	 */
	public function generateCSVHeadersbasedonExclusions($exclusionList) {
		$Headers = array();
		foreach($exclusionList as $key => $val) {
			if($val == 'true')
				$Headers[] = $key;
		}
		return $Headers;
	}

	/**
	 * @param $exporttype
	 * @param $request
	 *
	 * @return array
	 */
	public function generateCSVHeaders($exporttype, $request) {
		global $wpdb;
		$Header = array();
		$wpfieldsObj = new WPClassifyFields();
		$unwantedHeader = array('_eshop_product', '_wp_attached_file', '_wp_page_template', '_wp_attachment_metadata', '_encloseme');
		if($exporttype == 'woocommerce' || $exporttype == 'marketpress')
			$post_type = 'product';
		else if($exporttype == 'wpcommerce')
			$post_type = 'wpsc-product';
		else if($exporttype == 'eshop')
			$post_type = 'post';
		else if($exporttype == 'custompost') {
			$post_type = sanitize_text_field($request['optionalType']);
		}
		else
			$post_type = $exporttype;
		$header_query1 = $wpdb->prepare("SELECT wp.* FROM $wpdb->posts wp where post_type = %s",$post_type);
		//$header_query1 = "SELECT wp.* FROM  $wpdb->posts wp where post_type = '$post_type'";
		//$header_query2 = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM $wpdb->posts wp JOIN $wpdb->postmeta wpm ON wpm.post_id = wp.ID where wp.post_type = %s and meta_key NOT IN()");
		$header_query2 = "SELECT post_id, meta_key, meta_value FROM  $wpdb->posts wp JOIN $wpdb->postmeta wpm  ON wpm.post_id = wp.ID where wp.post_type = '$post_type' and meta_key NOT IN ('_edit_lock','_edit_last') and meta_key NOT LIKE 'field_%' and meta_key NOT LIKE '_wp_types%'";
		//		$header_query2 = "SELECT wp.*,wpm.post_id,wpm.meta_key,wpm.meta_value FROM $wpdb->posts wp join $wpdb->postmeta wpm where wp.post_type = '$post_type' and wpm.meta_key NOT IN ('_edit_lock','_edit_last') and wpm.meta_key NOT LIKE 'field_%' and wpm.meta_key NOT LIKE '_wp_types%'";
		//$header_query2 = "SELECT wp.*,wpm.post_id,wpm.meta_key,wpm.meta_value FROM $wpdb->posts wp join $wpdb->postmeta wpm" ;
		$result_header_query1 = $wpdb->get_results($header_query1);
		$result_header_query2 = $wpdb->get_results($header_query2);
		if($exporttype != 'woocommerce' && $exporttype != 'marketpress' && $exporttype != 'wpcommerce' && $exporttype != 'eshop') {
			foreach ($result_header_query1 as $rhq1_key) {
				foreach ($rhq1_key as $rhq1_headkey => $rhq1_headval) {
					if (!in_array($rhq1_headkey, $Header))
						$Header[] = $rhq1_headkey;
				}
			}
			$unwantedHeader = array();
			foreach($this->getACFvalues() as $acfKey => $acfVal) {
				$unwantedHeader[] = '_' . $acfKey;
				if(!in_array($acfKey, $unwantedHeader)) {
					$Header[] = $acfKey;
					$unwantedHeader[] = $acfKey;
				}
			}
			$customfields = array_merge($this->getTypesFields(),$this->getAIOSEOfields(),$this->getYoastSEOfields());
			if(!empty($customfields)){
				foreach($customfields as $fdkey => $fdval){
					if(!in_array($fdkey, $unwantedHeader)) {
						$Header[] = $fdval;
						$unwantedHeader[] = $fdkey;
					}
				}
			}
			foreach ($result_header_query2 as $rhq2_headkey) {
				if (!in_array($rhq2_headkey->meta_key, $Header)) {
					if(!in_array($rhq2_headkey->meta_key, $unwantedHeader)) {
						$Header[] = $rhq2_headkey->meta_key;
					}
				}
			}
			$alltaxonomies = get_taxonomies();
			if(!empty($alltaxonomies)){
				foreach($alltaxonomies as $alltaxkey){
					$Header[] = $alltaxkey;
				}
			}
			//echo '<pre>';print_r($alltaxonomies);echo '</pre>';
			//print('<pre>'); print_r($Header); die;
			if(!in_array('featured_image', $Header))
				$Header[] = 'featured_image';

			$this->generateHeaders($Header);
			#return $this->headers;
		} else {
			$ProHeader = array();
			switch($exporttype){
				case 'woocommerce':{
					$ecommerceHeaders = $this->WoocommerceMetaHeaders();
					break;
				}
				case 'marketpress':{
					$ecommerceHeaders = $this->MarketPressHeaders();
					break;
				}
				case 'wpcommerce' :{
					$ecommerceHeaders = $this->WpeCommerceHeaders();
					foreach($wpfieldsObj->wpecommerceCustomFields() as $wpcustomfield_key => $wpcustomfield_val) {
						foreach ($wpcustomfield_val as $wpcfkey => $wpcfvalue) {
							if(!in_array($wpcfvalue['name'], $ProHeader)) {
								$ProHeader[] = $wpcfvalue['name'];
							}
						}
					}
					break;
				}
				case 'eshop':{
					$ecommerceHeaders = $this->EshopHeaders();
					break;
				}
			}
			foreach($ecommerceHeaders as $ecomkey => $ecom_hval){
				if(in_array($ecom_hval,$Header))
					$ProHeader[] = $ecomkey;
				else
					$ProHeader[] = $ecomkey;

			}
			foreach($this->getACFvalues() as $acfKey => $acfVal) {
				if(!in_array($acfKey, $unwantedHeader)) {
					$ProHeader[] = $acfKey;
				}
			}
			$getcustomfields = array_merge($this->getTypesFields(),$this->getAIOSEOfields(),$this->getYoastSEOfields());
			if(!empty($getcustomfields)){
				foreach($getcustomfields as $cfdkey => $cfdval){
					if(!in_array($cfdkey,$unwantedHeader))
						$ProHeader[] = $cfdval;
				}
			}
			foreach ($result_header_query2 as $rhq2_headkey) {
				if (!in_array($rhq2_headkey->meta_key, $ProHeader)) {
					if(!in_array($rhq2_headkey->meta_key, $unwantedHeader)) {
						$ProHeader[] = $rhq2_headkey->meta_key;
					}
				}
			}
			if(!in_array('featured_image',$ProHeader))
				$ProHeader[] = 'featured_image';

			$this->generateHeaders($ProHeader);
			# return $this->headers;
		}
	}

	public function get_records_based_on_post_types ($module, $optionalType, $conditions) {
		global $wpdb; #, $uci_admin;
		/* if($module == 'CustomPosts') {
			$module = $optionalType;
		} else {
			$module = $uci_admin->import_post_types($module);
		} */
		#$module = $exporttype;
		if($module == 'woocommerce' || $module == 'marketpress')
			$module = 'product';
		if($module == 'wpcommerce')
			$module = 'wpsc-product';
		if($module == 'eshop')
			$module = 'post';
		if($module == 'custompost')
			$module = sanitize_text_field($optionalType);

		$get_post_ids = "select DISTINCT ID from $wpdb->posts p join $wpdb->postmeta pm ";
		$get_post_ids .= " where p.post_type = '$module'";

		// Check for specific status
		if($conditions['specific_status']['is_check'] == 'true') {
			if(isset($conditions['specific_status']['status']) && sanitize_text_field($conditions['specific_status']['status']) == 'All') {
				$get_post_ids .= " and p.post_status in ('publish','draft','future','private','pending')";
			} else if(isset($conditions['specific_status']['status']) && (sanitize_text_field($conditions['specific_status']['status']) == 'Publish' || sanitize_text_field($conditions['specific_status']['status']) == 'Sticky')) {
				$get_post_ids .= " and p.post_status in ('publish')";
			} else if(isset($conditions['specific_status']['status']) && sanitize_text_field($conditions['specific_status']['status']) == 'Draft') {
				$get_post_ids .= " and p.post_status in ('draft')";
			} else if(isset($conditions['specific_status']['status']) && sanitize_text_field($conditions['specific_status']['status']) == 'Scheduled') {
				$get_post_ids .= " and p.post_status in ('future')";
			} else if(isset($conditions['specific_status']['status']) && sanitize_text_field($conditions['specific_status']['status']) == 'Private') {
				$get_post_ids .= " and p.post_status in ('private')";
			} else if(isset($conditions['specific_status']['status']) && sanitize_text_field($conditions['specific_status']['status']) == 'Pending') {
				$get_post_ids .= " and p.post_status in ('pending')";
			} else if(isset($conditions['specific_status']['status']) && sanitize_text_field($conditions['specific_status']['status']) == 'Protected') {
				$get_post_ids .= " and p.post_status in ('publish') and post_password != ''";
			}
		} else {
			$get_post_ids .= " and p.post_status in ('publish','draft','future','private','pending')";
		}

		// Check for specific period
		if($conditions['specific_period']['is_check'] == 'true') {
			$get_post_ids .= " and p.post_date >= '" . $conditions['specific_period']['from'] . "' and p.post_date <= '" . $conditions['specific_period']['to'] . "'";
		}
		if($module == 'eshop')
			$get_post_ids .= " and pm.meta_key = '_eshop_product'";
		if($module == 'woocommerce')
			$get_post_ids .= " and pm.meta_key = '_sku'";
		if($module == 'marketpress')
			$get_post_ids .= " and pm.meta_key = 'mp_sku'";
		if($module == 'wpcommerce')
			$get_post_ids .= " and pm.meta_key = '_wpsc_sku'";

		// Check for specific authors
		if($conditions['specific_authors']['is_check'] == 'true') {
			if(isset($conditions['specific_authors']['author']) && $conditions['specific_authors']['author'] != 0) {
				$get_post_ids .= " and p.post_author = {$conditions['specific_authors']['author']}";
			}
		}

		$get_total_row_count = $wpdb->get_col($get_post_ids);
		$this->totalRowCount = count($get_total_row_count);
		$offset_limit = " order by ID asc limit $this->offset, $this->limit";
		$query_with_offset_limit = $get_post_ids . $offset_limit;
		$result = $wpdb->get_col($query_with_offset_limit);

		// Get sticky post alone on the specific post status
		if($conditions['specific_status']['is_check'] == 'true') {
			if(isset($conditions['specific_status']['status']) && sanitize_text_field($conditions['specific_status']['status']) == 'Sticky') {
				$get_sticky_posts = get_option('sticky_posts');
				foreach($get_sticky_posts as $sticky_post_id) {
					if(in_array($sticky_post_id, $result))
						$sticky_posts[] = $sticky_post_id;
				}
				return $sticky_posts;
			}
		}
		return $result;
	}

	/**
	 * @param $exporttype
	 * @param $request
	 *
	 * @return array
	 */
	public function get_all_record_ids($exporttype, $request) {
		global $wpdb;
		$post_type = $exporttype;
		$get_post_ids = "select DISTINCT ID from $wpdb->posts p join $wpdb->postmeta pm ";
		if($post_type == 'woocommerce' || $post_type == 'marketpress')
			$post_type = 'product';
		if($post_type == 'wpcommerce')
			$post_type = 'wpsc-product';
		if($post_type == 'eshop')
			$post_type = 'post';
		if($post_type == 'custompost')
			$post_type = sanitize_text_field($request['export_cpt_type']);

		$get_post_ids .= " where p.post_type = '$post_type'";
		if(isset($request['getdatawithspecificstatus'])) {
			if(isset($request['postwithstatus']) && sanitize_text_field($request['postwithstatus']) == 'All') {
				$get_post_ids .= " and p.post_status in ('publish','draft','future','private','pending')";
			} else if(isset($request['postwithstatus']) && (sanitize_text_field($request['postwithstatus']) == 'Publish' || sanitize_text_field($request['postwithstatus']) == 'Sticky')) {
				$get_post_ids .= " and p.post_status in ('publish')";
			} else if(isset($request['postwithstatus']) && sanitize_text_field($request['postwithstatus']) == 'Draft') {
				$get_post_ids .= " and p.post_status in ('draft')";
			} else if(isset($request['postwithstatus']) && sanitize_text_field($request['postwithstatus']) == 'Scheduled') {
				$get_post_ids .= " and p.post_status in ('future')";
			} else if(isset($request['postwithstatus']) && sanitize_text_field($request['postwithstatus']) == 'Private') {
				$get_post_ids .= " and p.post_status in ('private')";
			} else if(isset($request['postwithstatus']) && sanitize_text_field($request['postwithstatus']) == 'Pending') {
				$get_post_ids .= " and p.post_status in ('pending')";
			} else if(isset($request['postwithstatus']) && sanitize_text_field($request['postwithstatus']) == 'Protected') {
				$get_post_ids .= " and p.post_status in ('publish') and post_password != ''";
			}
		} else {
			$get_post_ids .= " and p.post_status in ('publish','draft','future','private','pending')";
		}
		if(isset($request['getdataforspecificperiod'])) {
			$get_post_ids .= " and p.post_date >= '" . $request['postdatefrom'] . "' and p.post_date <= '" . $request['postdateto'] . "'";
		}
		if($exporttype == 'eshop')
			$get_post_ids .= " and pm.meta_key = '_eshop_product'";
		if($post_type == 'woocommerce')
			$get_post_ids .= " and pm.meta_key = '_sku'";
		if($post_type == 'marketpress')
			$get_post_ids .= " and pm.meta_key = 'mp_sku'";
		if($post_type == 'wpcommerce')
			$get_post_ids .= " and pm.meta_key = '_wpsc_sku'";

		/*                if($exporttype == 'woocommerce') {
								$post_type = 'product';
					$get_post_ids = "select DISTINCT ID from $wpdb->posts p join $wpdb->postmeta pm on pm.post_id = p.ID where post_type = '$post_type' and post_status in ('publish','draft','future','private','pending') and pm.meta_key = '_sku'";
				} */
		if(isset($request['getdatabyspecificauthors'])) {
			if(isset($request['postauthor']) && $request['postauthor'] != 0) {
				$get_post_ids .= " and p.post_author = {$request['postauthor']}";
			}
		}
		#print_r($get_post_ids); die;
		//echo '<pre>';print_r($get_post_ids);echo '</pre>';
		$result = $wpdb->get_col($get_post_ids);
		//echo '<pre>';print_r($result);echo '</pre>';die('kkk');
		if(isset($request['getdatawithspecificstatus'])) {
			if(isset($request['postwithstatus']) && sanitize_text_field($request['postwithstatus']) == 'Sticky') {
				$get_sticky_posts = get_option('sticky_posts');
				foreach($get_sticky_posts as $sticky_post_id) {
					if(in_array($sticky_post_id, $result))
						$sticky_posts[] = $sticky_post_id;
				}
				return $sticky_posts;
			}
		}
		#print_r($get_sticky_posts);
		#print_r($result);die;
		return $result;
	}

	/**
	 * @param $postID
	 *
	 * @return array
	 */
	public function getPostDatas($postID) {
		global $wpdb;
		$PostData = array();
		//$query1 = "SELECT wp.* FROM $wpdb->posts wp where ID=$postID";
		$query1 = $wpdb->prepare("SELECT wp.* FROM $wpdb->posts wp where ID=%d",$postID);
		$result_query1 = $wpdb->get_results($query1);
		if (!empty($result_query1)) {
			foreach ($result_query1 as $posts) {
				foreach ($posts as $post_key => $post_value) {
					if ($post_key == 'post_status') {
						if (is_sticky($postID)) {
							$PostData[$post_key] = 'Sticky';
							$post_status = 'Sticky';
						} else {
							$PostData[$post_key] = $post_value;
							$post_status = $post_value;
						}
					} else {
						$PostData[$post_key] = $post_value;
					}
					if ($post_key == 'post_password') {
						if ($post_value) {
							$PostData['post_status'] = "{" . $post_value . "}";
						} else {
							$PostData['post_status'] = $post_status;
						}
					}
					if ($post_key == 'comment_status') {
						if ($post_value == 'closed') {
							$PostData['comment_status'] = 0;
						}
						if ($post_value == 'open') {
							$PostData['comment_status'] = 1;
						}
					}
				}
			}
		}
		return $PostData;
	}

	/**
	 * @param $postID
	 *
	 * @return array|null|object
	 */
	public function getPostMetaDatas($postID) {
		global $wpdb;
		//		$query2 = "SELECT post_id, meta_key, meta_value FROM $wpdb->posts wp JOIN $wpdb->postmeta wpm  ON wpm.post_id = wp.ID where meta_key NOT IN ('_edit_lock','_edit_last') AND ID=$postID";
		$query2 = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM $wpdb->posts wp JOIN $wpdb->postmeta wpm ON wpm.post_id = wp.ID where meta_key NOT IN (%s,%s) AND ID=%d",'_edit_lock','_edit_last',$postID);
		#print($query2); print('<br>');
		$result = $wpdb->get_results($query2);
		return $result;
	}

	/**
	 *
	 */
	public function getTypesFields() {
		$wptypesfields = get_option('wpcf-fields');
		#print('<pre>'); print_r($wptypesfields);
		$typesfields = array();
		if(!empty($wptypesfields) && is_array($wptypesfields)) {
			foreach($wptypesfields as $typeFkey){
				$typesfields[$typeFkey['meta_key']] = $typeFkey['name'];
			}
		}
		return $typesfields;
	}

	/**
	 * @param null $field_type
	 *
	 * @return array
	 */
	public function getACFvalues($field_type = null) {
		global $wpdb;
		$multi_optional_fields = $acf_fields = array();
		// Code for ACF fields
		$get_acf_fields = $wpdb->get_col ( "SELECT meta_value FROM $wpdb->postmeta
                                GROUP BY meta_key
                                HAVING meta_key LIKE 'field_%'
                                ORDER BY meta_key" );
		if(!empty($get_acf_fields) && is_array($get_acf_fields)) {
			foreach ( $get_acf_fields as $acf_value ){
				$get_acf_field = @unserialize($acf_value);
				$acf_fields[$get_acf_field['name']] = "CF: ".$get_acf_field['name'];
				$acf_fields_slug[$get_acf_field['name']] = "_".$get_acf_field['name'];

				if($get_acf_field['type'] == 'checkbox'){
					$multi_optional_fields[] = $get_acf_field['name'];
				}
				if($get_acf_field['type'] == 'relationship') {
					$multi_optional_fields[] = $get_acf_field['name'];
				}
			} // Code ends here
		}
		//if($field_type == 'checkbox' || $field_type == 'relationship')
			return $multi_optional_fields;
		/*else
			return $acf_fields; */
	}

	/**
	 *
	 */
	public function getACFprovalues(){
		global $wpdb;
		$acfchckbx = array();
		$get_acfpro_fields = $wpdb->get_results("SELECT post_content,post_excerpt FROM $wpdb->posts where post_name LIKE 'field_%'");
		if(!empty($get_acfpro_fields) && is_array($get_acfpro_fields)){
			foreach($get_acfpro_fields as $acfpro_key => $acfpro_value){
				$get_acfpro_fd = @unserialize($acfpro_value->post_content);
				if($get_acfpro_fd['type'] == 'checkbox'){
					$acfchckbx[] = $acfpro_value->post_excerpt;
				}
			}
		}
		return $acfchckbx;
	}

	/**
	 *
	 */
	public function getAIOSEOfields() {
		$aioseofields = array('_aioseop_keywords' => 'seo_keywords',
		                      '_aioseop_description'	=> 'seo_description',
		                      '_aioseop_title'	=> 'seo_title',
		                      '_aioseop_noindex'	=> 'seo_noindex',
		                      '_aioseop_nofollow'	=> 'seo_nofollow',
		                      '_aioseop_disable'	=> 'seo_disable',
		                      '_aioseop_disable_analytics' => 'seo_disable_analytics',
		                      '_aioseop_noodp'	=> 'seo_noodp',
		                      '_aioseop_noydir'	=> 'seo_noydir',);
		return $aioseofields;
	}

	/**
	 *
	 */
	public function getYoastSEOfields () {
		$yoastseofields = array('_yoast_wpseo_focuskw'	=> 'focus_keyword',
		                        '_yoast_wpseo_title'	=> 'title',
		                        '_yoast_wpseo_metadesc'	=> 'meta_desc',
		                        '_yoast_wpseo_meta-robots-noindex' => 'meta-robots-noindex',
		                        '_yoast_wpseo_meta-robots-nofollow' => 'meta-robots-nofollow',
		                        '_yoast_wpseo_meta-robots-adv'	=> 'meta-robots-adv',
		                        '_yoast_wpseo_sitemap-include'	=> 'sitemap-include',
		                        '_yoast_wpseo_sitemap-prio'	=> 'sitemap-prio',
		                        '_yoast_wpseo_canonical'	=> 'canonical',
		                        '_yoast_wpseo_redirect'		=> 'redirect',
		                        '_yoast_wpseo_opengraph-description' =>	'opengraph-description',
		                        '_yoast_wpseo_google-plus-description'	=> 'google-plus-description',
		);
		return $yoastseofields;
	}

	/**
	 * @param $postID
	 * @param $type
	 * @param $Header_data
	 *
	 * @return array
	 */
	public function getAllTerms($postID, $type, $Header_data) {
		// Tags & Categories
		$TermsData = array();
		if($type == 'woocommerce' || $type == 'marketpress') {
			$exporttype = 'product';
			$postTags = $postCategory = '';
			$taxonomies = get_object_taxonomies($exporttype);
			$get_tags = get_the_terms( $postID, 'product_tag' );
			if($get_tags){
				foreach($get_tags as $tags){
					$postTags .= $tags->name.',';
				}
			}
			$postTags = substr($postTags,0,-1);
			$TermsData['product_tag'] = $postTags;
			foreach ($taxonomies as $taxonomy) {
				if($taxonomy == 'product_cat' || $taxonomy == 'product_category'){
					$get_categotries =wp_get_post_terms( $postID, $taxonomy );
					if($get_categotries){
						foreach($get_categotries as $category){
							$postCategory .= $category->name.'|';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
					$TermsData['product_category'] = $postCategory;
				}
			}
		} else if($type == 'wpcommerce') {
			$exporttype = 'wpsc-product';
			$postTags = $postCategory = '';
			$taxonomies = get_object_taxonomies($exporttype);
			$get_tags = get_the_terms( $postID, 'product_tag' );
			if($get_tags){
				foreach($get_tags as $tags){
					$postTags .= $tags->name.',';
				}
			}
			$postTags = substr($postTags,0,-1);
			$TermsData['product_tag'] = $postTags;
			foreach ($taxonomies as $taxonomy) {
				if($taxonomy == 'wpsc_product_category'){
					$get_categotries =wp_get_post_terms( $postID, $taxonomy );
					if($get_categotries){
						foreach($get_categotries as $category){
							$postCategory .= $category->name.'|';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
					$TermsData['product_category'] = $postCategory;
				}
			}
		} else {
			global $wpdb;
			$postTags = $postCategory = '';
			//$taxobj_id = $wpdb->get_col("select term_taxonomy_id from $wpdb->term_relationships where object_id = $postID");
			$taxobj_id = $wpdb->get_col($wpdb->prepare("select term_taxonomy_id from $wpdb->term_relationships where object_id = %d",$postID));
			if(!empty($taxobj_id)){
				foreach($taxobj_id as $taxid){
					//$taxonomytype = $wpdb->get_col("select taxonomy from $wpdb->term_taxonomy where term_taxonomy_id = $taxid");
					$taxonomytype = $wpdb->get_col($wpdb->prepare("select taxonomy from $wpdb->term_taxonomy where term_taxonomy_id = %d",$taxid));
					if(!empty($taxonomytype)){
						foreach($taxonomytype as $tagtype){
							if($tagtype == 'category')
								$tagtype = 'post_category';
							if(in_array($tagtype,$Header_data)){
								if($tagtype != 'post_tag' ){
									//$taxonomydata = $wpdb->get_col("select name from $wpdb->terms where term_id = $taxid");
									$taxonomydata = $wpdb->get_col($wpdb->prepare("select name from $wpdb->terms where term_id = %d",$taxid));
									if(!empty($taxonomydata)){
										if(isset($TermsData[$tagtype]))
											$TermsData[$tagtype] = $TermsData[$tagtype] . ',' . $taxonomydata[0];
										else
											$TermsData[$tagtype] = $taxonomydata[0];
									}
								}
								else {
									if(!isset($TermsData['post_tag'])){
										$get_tags = wp_get_post_tags($postID, array('fields' => 'names'));
										foreach ($get_tags as $tags) {
											$postTags .= $tags . ',';
										}
										$postTags = substr($postTags, 0, -1);
										$TermsData[$tagtype] = $postTags;
									}
								}
								if(!isset($TermsData['category'])){
									$get_categotries = wp_get_post_categories($postID, array('fields' => 'names'));
									foreach ($get_categotries as $category) {
										$postCategory .= $category . '|';
									}
									$postCategory = substr($postCategory, 0, -1);
									$TermsData['category'] = $postCategory;
								}

							}

							else{
								$TermsData[$tagtype] = '';
							}
						}
					}
				}
			}
		}
		return $TermsData;
	}

	/**
	 *
	 */
	public function MarketPressHeaders() {
		$marketpressHeaders = array('product_title' => 'post_title', 'product_content' => 'post_content', 'product_excerpt' => 'post_excerpt', 'product_publish_date' => 'post_date', 'product_slug' => 'post_name', 'product_status' => 'post_status', 'product_parent' => 'post_parent', 'comment_status' => 'comment_status', 'ping_status' => 'ping_status', 'menu_order' => 'menu_order', 'post_author' => 'post_author', 'variation' => 'mp_var_name', 'SKU' => 'mp_sku', 'regular_price' => 'mp_price', 'is_sale' => 'mp_is_sale', 'sale_price' => 'mp_sale_price', 'track_inventory' => 'mp_track_inventory', 'inventory' => 'mp_inventory', 'track_limit' => 'mp_track_limit', 'limit_per_order' => 'mp_limit', 'product_link' => 'mp_product_link', 'is_special_tax' => 'mp_is_special_tax', 'special_tax' => 'mp_special_tax', 'sales_count' => 'mp_sales_count', 'extra_shipping_cost' => 'mp_shipping', 'file_url' => 'mp_file', 'product_category' => 'product_category', 'product_tag' => 'post_tag', 'featured_image' => 'featured_image',);
		return $marketpressHeaders;
	}

	/**
	 *
	 */
	public function WpeCommerceHeaders() {
		$wpecommerceHeaders = array('post_date' => 'post_date', 'post_content' => 'post_content', 'post_title' => 'post_title', 'post_excerpt' => 'post_excerpt', 'post_name' => 'post_name', 'stock' => '_wpsc_stock', 'price' => '_wpsc_price', 'sale_price' => '_wpsc_special_price', 'SKU' => '_wpsc_sku', 'product_tags' => 'product_tag', 'product_category' => null, 'featured_image' => 'featured_image', 'custom_meta' => null, 'wpsc_is_donation' => '_wpsc_is_donation', 'notify_when_none_left' => 'notify_when_none_left', 'unpublish_when_none_left' => 'unpublish_when_none_left', 'taxable_amount' => 'wpec_taxes_taxable_amount', 'is_taxable' => 'wpec_taxes_taxable', 'external_link' => 'external_link', 'external_link_text' => 'external_link_text', 'external_link_target' => 'external_link_target', 'no_shipping' => 'no_shipping', 'weight' => 'weight', 'weight_unit' => 'weight_unit', 'height' => 'height', 'height_unit' => 'height_unit', 'width' => 'width', 'width_unit' => 'width_unit', 'length' => 'length', 'length_unit' => 'length_unit', 'dimension_unit' => null, 'shipping' => 'shipping', 'merchant_notes' => 'merchant_notes', 'enable_comments' => 'enable_comments', 'quantity_limited' => 'quantity_limited', 'special' => 'special', 'display_weight_as' => 'display_weight_as', 'state' => 'state', 'quantity' => 'quantity', 'table_price' => 'table_price', 'google_prohibited' => 'google_prohibited',);
		return $wpecommerceHeaders;
	}

	/**
	 *
	 */
	public function WoocommerceMetaHeaders() {
		$woocomHeaders = array('product_publish_date' => 'post_date', 'product_content' => 'post_content', 'product_name' => 'post_title', 'product_short_description' => 'post_excerpt', 'product_slug' => 'post_name', 'post_parent' => 0, 'product_category' => 'post_category', 'product_tag' => 'post_tag', 'post_type' => null, 'product_type'  => '_product_type', 'product_shipping_class' => '_product_shipping_class', 'product_status' => 'post_status', 'visibility' => '_visibility', 'tax_status' => '_tax_status', 'product_attribute_name' => '_product_attribute_name', 'product_attribute_value' => '_product_attribute_value', 'product_attribute_visible' => '_product_attribute_visible', 'product_attribute_variation' => '_product_attribute_variation', 'featured_image' => 'featured_image', 'product_attribute_taxonomy' => '_product_attribute_taxonomy', 'tax_class' => '_tax_class', 'file_paths' => '_file_paths', 'comment_count' => null, 'menu_order' 	=> 0, 'comment_status'=> null, 'edit_last' => null, 'edit_lock' => null, 'thumbnail_id' => null, 'visibility' => '_visibility', 'stock_status' => '_stock_status', 'stock_qty' => '_stock', 'total_sales' => null, 'downloadable' => 'downloadable', 'downloadable_files' => '_downloadable_files', 'virtual' => '_virtual', 'regular_price' => '_regular_price', 'sale_price' => '_sale_price', 'purchase_note' => null, 'featured_product' => '_featured', 'weight' => null, 'length' => null, 'width' => null, 'height' => null, 'sku' => '_sku', 'upsell_ids' => '_upsell_ids', 'crosssell_ids' => '_crosssell_ids', 'sale_price_dates_from' => '_sale_price_dates_from', 'sale_price_dates_to' => '_sale_price_dates_to', 'price' => null,'sold_individually' => '_price', 'manage_stock' => '_manage_stock', 'backorders' => '_backorders', 'product_image_gallery' => '__product_image_gallery', 'product_url' => '_product_url', 'button_text' => '_button_text', 'downloadable_files' => null, 'download_limit' => '_download_limit', 'download_expiry' => '_download_expiry', 'download_type'=> null, 'min_variation_price' => null, 'max_variation_price'=> null, 'min_price_variation_id' => null, 'max_price_variation_id' => null, 'min_variation_regular_price' => null, 'max_variation_regular_price' => null, 'min_regular_price_variation_id' => null, 'max_regular_price_variation_id' => null, 'min_variation_sale_price' => null, 'max_variation_sale_price' => null, 'min_sale_price_variation_id' => null, 'max_sale_price_variation_id' => null, 'default_attributes' => null, 'product_author' => 'post_author',);
		return $woocomHeaders;
	}

	/**
	 *
	 */
	public function EshopHeaders() {
		$eshopHeaders = array('post_title' => 'post_title', 'post_content' => 'post_content', 'post_excerpt' => 'post_excerpt', 'post_date' => 'post_date', 'post_name' => 'post_name', 'post_status' => 'post_status', 'post_author' => 'post_author', 'post_parent' => 0, 'comment_status' => 'open', 'ping_status' => 'open', 'SKU' => 'sku', 'products_option' => 'products_option', 'sale_price' => 'sale_price', 'regular_price' => 'regular_price', 'description' => 'description', 'shiprate' => 'shiprate', 'optset' => null, 'featured_product' => 'featured', 'product_in_sale' => '_eshop_sale', 'stock_available' => '_eshop_stock', 'cart_option' => 'cart_radio', 'category' => 'post_category', 'tags' => 'post_tag', 'featured_image' => null,);
		return $eshopHeaders;
	}

	public function FetchDataByPostTypes ($request) {
		global $wpdb;
		$Header = array();
		$header_query1 = "SELECT * FROM $wpdb->posts";
		$header_query2 = $wpdb->prepare("SELECT post_id, meta_key, meta_value FROM  $wpdb->posts wp JOIN $wpdb->postmeta wpm ON wpm.post_id = wp.ID where meta_key NOT IN (%s, %s)", '_edit_lock', '_edit_last');
		$result_header_query1 = $wpdb->get_results($header_query1);
		$result_header_query2 = $wpdb->get_results($header_query2);
		foreach ($result_header_query1 as $rhq1_key) {
			foreach ($rhq1_key as $rhq1_headkey => $rhq1_headval) {
				if (!in_array($rhq1_headkey, $Header))
					$Header[] = $rhq1_headkey;
			}
		}
		foreach ($result_header_query2 as $rhq2_headkey) {
			if (!in_array($rhq2_headkey->meta_key, $Header)) {
				if($rhq2_headkey->meta_key == 'mp_shipping_info' )
				{
					$mp_ship_header= unserialize($rhq2_headkey->meta_value);
					foreach($mp_ship_header as $mp_ship_key => $mp_value) { $Header[] = "msi: ".$mp_ship_key; }
				}
				if($rhq2_headkey->meta_key == 'mp_billing_info' )
				{
					$mp_ship_header= unserialize($rhq2_headkey->meta_value);
					foreach($mp_ship_header as $mp_ship_key => $mp_value) { $Header[] = "mbi: ".$mp_ship_key; }
				}

				if ($rhq2_headkey->meta_key != '_eshop_product' && $rhq2_headkey->meta_key != '_wp_attached_file' && $rhq2_headkey->meta_key != 'mp_shipping_info' && $rhq2_headkey->meta_key != 'mp_billing_info' )
					$Header[] = $rhq2_headkey->meta_key;
			}
		}
		$this->generateHeaders($Header);

		#$this->generateHeaders($this->module, $this->optionalType);
		$recordsToBeExport = $this->get_records_based_on_post_types($this->module, $this->optionalType, $this->conditions);
		if(!empty($recordsToBeExport)) :
			foreach($recordsToBeExport as $postId) {
				$this->data[$postId] = $this->getPostsDataBasedOnRecordId($postId);
				$this->getTermsAndTaxonomies($postId, $this->module, $this->optionalType);
				$this->getPostsMetaDataBasedOnRecordId($postId, $this->module, $this->optionalType);
				#$this->getTypesFields();
			}
		endif;
		$result = $this->finalDataToExport($this->data);
		$this->proceedExport($result);
	}

	public function getPostsDataBasedOnRecordId ($id) {
		global $wpdb;
		$PostData = array();
		$query1 = $wpdb->prepare("SELECT wp.* FROM $wpdb->posts wp where ID=%d", $id);
		$result_query1 = $wpdb->get_results($query1);
		if (!empty($result_query1)) {
			foreach ($result_query1 as $posts) {
				foreach ($posts as $post_key => $post_value) {
					if ($post_key == 'post_status') {
						if (is_sticky($id)) {
							$PostData[$post_key] = 'Sticky';
							$post_status = 'Sticky';
						} else {
							$PostData[$post_key] = $post_value;
							$post_status = $post_value;
						}
					} else {
						$PostData[$post_key] = $post_value;
					}
					if ($post_key == 'post_password') {
						if ($post_value) {
							$PostData['post_status'] = "{" . $post_value . "}";
						} else {
							$PostData['post_status'] = $post_status;
						}
					}
					if ($post_key == 'comment_status') {
						if ($post_value == 'closed') {
							$PostData['comment_status'] = 0;
						}
						if ($post_value == 'open') {
							$PostData['comment_status'] = 1;
						}
					}
				}
			}
		}
		#$this->data[$id] = $PostData;
		return $PostData;
	}

	public function getPostsMetaDataBasedOnRecordId ($id) {
		global $wpdb;
		$query = $wpdb->prepare("SELECT post_id,meta_key,meta_value FROM $wpdb->posts wp JOIN $wpdb->postmeta wpm ON wpm.post_id = wp.ID where meta_key NOT IN (%s,%s) AND ID=%d", '_edit_lock', '_edit_last', $id);
		$result = $wpdb->get_results($query);
		#print_r($result);

		if(!empty($result)) {
			foreach ( $result as $key => $value ) {
				if ( $value->meta_key == '_thumbnail_id' ) {
					$attachment_file                       = null;
					$get_attachment                        = $wpdb->prepare( "select guid from $wpdb->posts where ID = %d AND post_type = %s", $value->meta_value, 'attachment' );
					$attachment                            = $wpdb->get_results( $get_attachment );
					$attachment_file                       = $attachment[0]->guid;
					$this->data[ $id ][ $value->meta_key ] = '';
					$value->meta_key                       = 'featured_image';
					$this->data[ $id ][ $value->meta_key ] = $attachment_file;
				} else {
					if( $value->meta_key != '_product_attributes' )
						$metaValue = maybe_unserialize( $value->meta_value );
					if ( is_array( $metaValue ) && count( $metaValue ) >= 1 ) {
						$metaData = '';
						foreach ( $metaValue as $item ) {
							$metaData .= $item . ',';
						}
						if(count( $metaValue ) == 1)
							$metaData = substr($metaData, 0, -1);
						$this->data[ $id ][ $value->meta_key ] = $metaData;
					} else {
						$this->data[ $id ][ $value->meta_key ] = $value->meta_value;
					}
				}
			}
		}
		#return $result;
	}

	/**
	 * Function used to fetch the Terms & Taxonomies for the specific posts
	 *
	 * @param $id
	 * @param $type
	 * @param $optionalType
	 */
	public function getTermsAndTaxonomies ($id, $type, $optionalType) {
		if($type == 'woocommerce' || $type == 'marketpress') {
			$type = 'product';
			if(!in_array('product_tag', $this->headers))
				$this->headers[] = 'product_tag';
			if(!in_array('product_category', $this->headers))
				$this->headers[] = 'product_category';
			$postTags = $postCategory = '';
			// Fetch all Tags to the specific record
			$get_tags = get_the_terms( $id, 'product_tag' );
			if(is_array( $get_tags )){
				foreach($get_tags as $tags){
					$postTags .= $tags->name . ',';
				}
			}
			$postTags = substr($postTags, 0, -1);
			// Fetch all Categories to the specific record
			$taxonomies = get_object_taxonomies($type);
			foreach ($taxonomies as $taxonomy) {
				if($taxonomy == 'product_cat' || $taxonomy == 'product_category'){
					$get_categories = wp_get_post_terms( $id, $taxonomy );
					if(is_array( $get_categories )){
						foreach($get_categories as $category){
							$postCategory .= $category->name . '|';
						}
					}
					$postCategory = substr($postCategory, 0 , -1);
				}
			}
			$this->data[$id]['product_tag'] = $postTags;
			$this->data[$id]['product_category'] = $postCategory;
		} else if($type == 'wpecommerce') {
			if(!in_array('product_tag', $this->headers))
				$this->headers[] = 'product_tag';
			if(!in_array('product_category', $this->headers))
				$this->headers[] = 'product_category';
			$type = 'wpsc-product';
			$postTags = $postCategory = $postTaxonomy = '';
			// Fetch all Tags to the specific record
			$get_tags = get_the_terms( $id, 'product_tag' );
			if(is_array( $get_tags )){
				foreach($get_tags as $tags){
					$postTags .= $tags->name . ',';
				}
			}
			$postTags = substr($postTags,0,-1);
			// Fetch all Categories to the specific record
			$get_categories = wp_get_post_terms( $id, 'wpsc_product_category' );
			if(is_array( $get_categories )){
				foreach($get_categories as $category){
					$postCategory .= $category->name . '|';
				}
			}
			$postCategory = substr($postCategory, 0 , -1);
			// Fetch all Taxonomies to the specific record
			$this->data[$id]['product_tag'] = $postTags;
			$this->data[$id]['product_category'] = $postCategory;
		} else {
			global $wpdb;
			if(!in_array('post_tag', $this->headers))
				$this->headers[] = 'post_tag';
			if(!in_array('post_category', $this->headers))
				$this->headers[] = 'post_category';
			$postTags = $postCategory = '';
			// Fetch all Tags to the specific record
			$get_tags = wp_get_post_tags($id, array('fields' => 'names'));
			foreach ($get_tags as $tags) {
				$postTags .= $tags . ',';
			}
			$postTags = substr($postTags, 0, -1);
			// Fetch all Tags to the specific record
			$get_categories = wp_get_post_categories($id, array('fields' => 'names'));
			foreach ($get_categories as $category) {
				$postCategory .= $category . '|';
			}
			$postCategory = substr($postCategory, 0, -1);
			$this->data[$id]['post_category'] = $postCategory;
			$this->data[$id]['post_tag'] = $postTags;
		}
		// Fetch all Tags to the specific record
		$taxonomies = get_object_taxonomies($type);
		foreach ($taxonomies as $taxonomy) {
			$postTaxonomy = '';
			if( $taxonomy != 'category' || $taxonomy != 'post_tag' || $taxonomy != 'product_cat' || $taxonomy != 'product_category' || $taxonomy != 'wpsc_product_category' || $taxonomy != 'product_tag' ) {
				if(!in_array($taxonomy, $this->headers))
					$this->headers[] = $taxonomy;
				$get_terms = wp_get_post_terms( $id, $taxonomy );
				if(is_array( $get_terms )) {
					foreach($get_terms as $term){
						$postTaxonomy .= $term->name . '|';
					}
				}
				$postTaxonomy = substr($postTaxonomy, 0 , -1);
				$this->data[$id][$taxonomy] = $postTaxonomy;
			}
		}
		#return $TermsData;
	}

	/* public function WPImpPROExportData_Old($request) {
		global $wpdb;
		$PostMetaData = array();
		//$export_delimiter = ',';
		$PostData = array();
		#print_r($request); die;
		$exporttype = $request['module'];
		$wpcsvsettings=get_option('wpcsvprosettings');
		$export_delimiter = $this->set_exportdelimiter();
		if($_POST['export_filename'])
			$csv_file_name = $request['fileName'].'.csv';
		else
			$csv_file_name = 'exportas_'.date("Y").'-'.date("m").'-'.date("d").'.csv';
		$wptypesfields = get_option('wpcf-fields');
		#$exclusion_list = get_option('wp_ultimate_csv_importer_export_exclusion');
		$exclusion_list = $request['eventExclusions']['exclusion_headers'];

		$this->generateCSVHeaders($exporttype, $request);
		//}
		#$this->headers = $Header;
		$result = $this->get_records_based_on_post_types($this->module, $this->optionalType, $this->conditions);
		#$result = $this->get_all_record_ids($exporttype, $request);
		#print_r($this); die;
		#print('<pre>'); print_r($this->headers); print_r($result); print('</pre>'); die;
		$fieldsCount = count($result);
		if(isset($result)) {
			foreach ($result as $postID) {
				#$pId = $pId . ',' . $postID;
				$PostData[$postID] = $this->getPostDatas($postID);
				#print('<pre>'); print_r($PostData); #die;
				$result_query2 = $this->getPostMetaDatas($postID);
				$possible_values = array('s:', 'a:', ':{');
				if (!empty($result_query2)) {
					foreach ($result_query2 as $postmeta) {
						$typesFserialized = 0;
						$isFound = explode('wpcf-',$postmeta->meta_key);
						if(count($isFound) == 2){
							foreach($wptypesfields as $typesKey => $typesVal){
								if($postmeta->meta_key == 'wpcf-'.$typesKey){															$typesis_serialize = @unserialize($postmeta->meta_value);
									if($typesis_serialize !== false)
										$typesFserialized = 1;
									else
										$typesFserialized = 0;
									if($typesFserialized == 1){
										$getMetaData = get_post_meta($postID, $postmeta->meta_key);
										if(!is_array($getMetaData[0])){
											$get_all_values = unserialize($getMetaData[0]);
											$get_values = $get_all_values[0];
										} else {
											$get_values = $getMetaData[0];
										}
										$typesFVal = null;
										if($typesVal['type'] == 'checkboxes'){
											foreach($get_values as $authorKey => $authorVal) {
												foreach($typesVal['data']['options'] as $doKey => $doVal){
													if($doKey == $authorKey)
														$typesFVal .= $doVal['title'].',';
												}
											}
											$typesFVal = substr($typesFVal, 0, -1);
										} elseif($typesVal['type'] == 'skype') {
											$typesFVal = $get_values['skypename'];
										}
										$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $typesFVal;
									} else {
										$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $postmeta->meta_value;
									}
								}
							}
						} else {
							// ACF checkbox fields
							$acfserialized = 0;
							if(array_key_exists($postmeta->meta_key, $this->getACFvalues())) {
								$acfis_serialize = @unserialize($postmeta->meta_value);
								print_r($acfis_serialize);
								if($acfis_serialize !== false)
									$acfserialized = 1;
								else
									$acfserialized = 0;

								print('Is Serialized: ' . $acfserialized);
								if($acfserialized == 0) {
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $postmeta->meta_value;
								} else {
									$acf_checkboxes = $this->getACFvalues();
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = '';
									if(in_array($postmeta->meta_key, $acf_checkboxes)) {
										$get_all_values = unserialize($postmeta->meta_value);
										foreach($get_all_values as $optKey => $optVal) {
											$PostMetaData[$postmeta->post_id][$postmeta->meta_key] .= $optVal . ',';
										}
										$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = substr($PostMetaData[$postmeta->post_id][$postmeta->meta_key], 0, -1);
									}
								}
								print_r($PostMetaData); die;
							}
							// ACF checkbox fields ends here
							// WooCommerce product meta datas
							else if ($postmeta->meta_key == '_product_attributes') {
								#print_r($postmeta->meta_value); #die;
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = '';
								$product_attribute_name = $product_attribute_value = $product_attribute_visible = $product_attribute_variation = '';
								$PostMetaData[$postmeta->post_id]['_product_attribute_name'] = '';
								$PostMetaData[$postmeta->post_id]['_product_attribute_value'] = '';
								$PostMetaData[$postmeta->post_id]['_product_attribute_visible'] = '';
								$PostMetaData[$postmeta->post_id]['_product_attribute_variation'] = '';
								$eshop_products_unser1 = unserialize($postmeta->meta_value);
								$check_attr_count1 = count($eshop_products_unser1);
								$check_attr_count2 = 0;
								if($check_attr_count1 == 1){
									$eshop_products_unser2 = @unserialize($eshop_products_unser1);
									$check_attr_count2 = count($eshop_products_unser2);
								}
								if($check_attr_count1 < $check_attr_count2){
									$unserialized_attributes = $eshop_products_unser2;
								}else{
									$unserialized_attributes = $eshop_products_unser1;
								}

								foreach ($unserialized_attributes as $key) {
									foreach($key as $attr_header => $attr_value){
										if($attr_header == 'name')
											$product_attribute_name .= $attr_value.'|';
										if($attr_header == 'value')
											$product_attribute_value .= $attr_value.'|';
										if($attr_header == 'is_visible')
											$product_attribute_visible .= $attr_value.'|';
										if($attr_header == 'is_variation'){
											if(isset($attr_value))
												$product_attribute_variation .= $attr_value.'|';
										}
									}
								}
								$PostMetaData[$postmeta->post_id]['_product_attribute_name'] = substr($product_attribute_name, 0, -1);
								$PostMetaData[$postmeta->post_id]['_product_attribute_value'] = substr($product_attribute_value, 0, -1);
								$PostMetaData[$postmeta->post_id]['_product_attribute_visible'] = substr($product_attribute_visible, 0, -1);
								$PostMetaData[$postmeta->post_id]['_product_attribute_variation'] = substr($product_attribute_variation, 0, -1);
								#print('<pre>'); print_r($PostMetaData); die;
							}
							else if ($postmeta->meta_key == '_upsell_ids') {
								$upsellids = array();
								$crosssellids = array();
								#print('<pre>'); print('VALUE for _upsell_ids: '); print_r($postmeta->meta_value); #print_r($PostMetaData); print('</pre>'); #die;
								if($postmeta->meta_value != '' && $postmeta->meta_value != null) {
									$upsell_ids = '';
									$upsellids = unserialize($postmeta->meta_value);
									if(is_array($upsellids)){
										foreach($upsellids as $upsellID){
											$upsell_ids .= $upsellID.',';
										}
										$PostMetaData[$postmeta->post_id]['_upsell_ids'] = substr($upsell_ids, 0, -1);
									}else{
										$PostMetaData[$postmeta->post_id]['_upsell_ids'] = '';
									}
									#print('<pre>'); print('VALUE for _upsell_ids: '); print_r($postmeta->meta_value); print_r($PostMetaData);
								}
							}
							#print('<pre>'); print('VALUE for _upsell_ids: '); print_r($postmeta->meta_value); print_r($PostMetaData); print('</pre>'); #die;
							else if ($postmeta->meta_key == '_crosssell_ids') {
								if($postmeta->meta_value != '' && $postmeta->meta_value != null) {
									$crosssellids = unserialize($postmeta->meta_value);
									$crosssell_ids = '';
									if(is_array($crosssellids)){
										foreach($crosssellids as $crosssellID){
											$crosssell_ids .= $crosssellID.',';
										}
										$PostMetaData[$postmeta->post_id]['_crosssell_ids'] = substr($crosssell_ids, 0, -1);
									}else{
										$PostMetaData[$postmeta->post_id]['_crosssell_ids'] = '';
									}
								}
							}
							else if ($postmeta->meta_key == '_downloadable_files') {
								if($postmeta->meta_value != '' && $postmeta->meta_value != null) {
									$downloadable_files = unserialize($postmeta->meta_value);
									if(is_array($downloadable_files)){
										$downloadable_all = $downloadable_value = '';
										foreach($downloadable_files as $dkey => $dval){
											$downloadable_key = $dkey;
											foreach($dval as $down_key => $down_val) {
												$downloadable_value .= $down_val . ',';
											}
										}
										$downloadable_all .= $downloadable_key . ',' . $downloadable_value;
										$PostMetaData[$postmeta->post_id]['_downloadable_files'] = substr($downloadable_all ,0, -1);
									}else{
										$PostMetaData[$postmeta->post_id]['_downloadable_files'] = '';
									}
								}
							}
							else if ($postmeta->meta_key == '_thumbnail_id') {
								$attachment_file = '';
								//$get_attachement = "select guid from $wpdb->posts where ID = $postmeta->meta_value AND post_type = 'attachment'";
								$get_attachement = $wpdb->prepare("select guid from $wpdb->posts where ID = %d AND post_type = %s",$postmeta->meta_value,'attachment');
								$attachment = $wpdb->get_results($get_attachement);
								$attachment_file = $attachment[0]->guid;
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = '';
								$postmeta->meta_key = 'featured_image';
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $attachment_file;
							}
							else if ($postmeta->meta_key == '_visibility') {
								if($postmeta->meta_value == 'visible')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 1;
								if($postmeta->meta_value == 'catalog')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 2;
								if($postmeta->meta_value == 'search')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 3;
								if($postmeta->meta_value == 'hidden')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 4;
							}
							else if ($postmeta->meta_key == '_stock_status') {
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $postmeta->meta_value;
							}
							else if ($postmeta->meta_key == '_tax_status') {
								if($postmeta->meta_value == 'taxable')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 1;
								if($postmeta->meta_value == 'shipping')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 2;
								if($postmeta->meta_value == 'none')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 3;
							}
							else if ($postmeta->meta_key == '_tax_class') {
								if($postmeta->meta_value == '')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 1;
								if($postmeta->meta_value == 'reduced-rate')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 2;
								if($postmeta->meta_value == 'zero-rate')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 3;
							}
							else if ($postmeta->meta_key == '_backorders') {
								if($postmeta->meta_value == 'no')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 1;
								if($postmeta->meta_value == 'notify')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 2;
								if($postmeta->meta_value == 'yes')
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = 3;
							}
							else if ($postmeta->meta_key == '_featured') {
								if($postmeta->meta_value == 'no')
									$PostMetaData[$postmeta->post_id]['featured_product'] = 1;
								if($postmeta->meta_value == 'yes')
									$PostMetaData[$postmeta->post_id]['featured_product'] = 2;
								if($postmeta->meta_value == 'zero-rate')
									$PostMetaData[$postmeta->post_id]['featured_product'] = 3;
							}
							else if ($postmeta->meta_key == '_product_type') {
								if($postmeta->meta_value == 'simple')
									$PostMetaData[$postmeta->post_id]['product_type'] = 1;
								if($postmeta->meta_value == 'grouped')
									$PostMetaData[$postmeta->post_id]['product_type'] = 2;
								if($postmeta->meta_value == 'variable')
									$PostMetaData[$postmeta->post_id]['product_type'] = 4;
							}
							else if ($postmeta->meta_key == 'products') {
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = '';
								if(isset($eshop_products)) {
									$eshop_products = unserialize($eshop_products);
									foreach ($eshop_products as $key) {
										$PostMetaData[$postmeta->post_id][$postmeta->meta_key] .= $key['option'] . '|' . $key['price'] . '|' . $key['saleprice'] . ',';
									}
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = substr($PostMetaData[$postmeta->post_id][$postmeta->meta_key], 0, -1);
								}
							} // WooCommerce product meta datas end here
							// MarketPress product meta datas starts here
							else if ($postmeta->meta_key == 'mp_var_name') {
								$mp_variations = null;
								$all_variations = unserialize($postmeta->meta_value);
								if(!empty($all_variations)){
									foreach($all_variations as $variation_name) {
										$mp_variations .= $variation_name . ',';
									}
								}
								$mp_variations = substr($mp_variations, 0, -1);
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $mp_variations;
							}
							else if ($postmeta->meta_key == 'mp_sale_price') {
								$mp_sale_prices = null;
								$all_sale_prices = unserialize($postmeta->meta_value);
								if(!empty($all_sale_prices)){
									foreach($all_sale_prices as $mp_sale_price_value) {
										$mp_sale_prices .= $mp_sale_price_value . ',';
									}
								}
								$mp_sale_prices = substr($mp_sale_prices, 0, -1);
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $mp_sale_prices;
							}
							else if ($postmeta->meta_key == 'mp_price') {
								$mp_prod_prices = null;
								$all_mp_prod_prices = unserialize($postmeta->meta_value);
								if(!empty($all_mp_prod_prices)){
									foreach($all_mp_prod_prices as $mp_prod_price_value) {
										$mp_prod_prices .= $mp_prod_price_value . ',';
									}
								}
								$mp_prod_prices = substr($mp_prod_prices, 0, -1);
								if(isset($mp_prod_prices))
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $mp_prod_prices;
							}
							else if ($postmeta->meta_key == 'mp_sku') {
								$mp_sku = null;
								$all_mp_prod_sku = unserialize($postmeta->meta_value);
								if(!empty($all_mp_prod_sku)){
									foreach($all_mp_prod_sku as $mp_prod_sku) {
										$mp_sku .= $mp_prod_sku. ',';
									}
								}
								$mp_sku = substr($mp_sku, 0, -1);
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $mp_sku;
							}
							else if ($postmeta->meta_key == 'mp_shipping') {
								$mp_prod_shipping_value = unserialize($postmeta->meta_value);
								$mp_shipping_value = $mp_prod_shipping_value['extra_cost'];
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $mp_shipping_value;
							}
							else if ($postmeta->meta_key == 'mp_inventory') {
								$mp_inventory_value = null;
								$mp_prod_inventory_values = unserialize($postmeta->meta_value);
								if(!empty($mp_prod_inventory_values)){
									foreach($mp_prod_inventory_values as $inventory_values) {
										$mp_inventory_value .= $inventory_values. ',';
									}
								}
								$mp_inventory_value = substr($mp_inventory_value, 0, -1);
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $mp_inventory_value;
							} // MarketPress product meta datas ends here
							// WP e-Commerce product meta datas starts here
							else if ($postmeta->meta_key == '_wpsc_product_metadata') {
								$wpecomm_product_metadata = unserialize($postmeta->meta_value);
								#print('<pre>'); print_r($wpecomm_product_metadata); die;
								foreach($wpecomm_product_metadata as $prod_md_key => $prod_md_val) {
									if($prod_md_key == 'dimensions') { #die('summa');
										foreach($prod_md_val as $prod_dimen_key => $prod_dimen_val) {
											$PostMetaData[$postmeta->post_id][$prod_dimen_key] = $prod_dimen_val;
										}
									}
									else if($prod_md_key == 'shipping') {
										$shipping = null;
										foreach($prod_md_val as $prod_ship_key => $prod_ship_val) {
											$shipping .= $prod_ship_val . ',';
										}
										$shipping = substr($shipping, 0, -1);
										$PostMetaData[$postmeta->post_id][$prod_md_key] = $shipping;
									}
									else if($prod_md_key == 'table_rate_price') {
										foreach($prod_md_val as $table_rate_key => $table_rate_val) {
											if($table_rate_key == 'quantity') {
												$trq_val = null;
												foreach($table_rate_val as $trq) {
													$trq_val .= $trq . '|';
												}
												$trq_val = substr($trq_val, 0, -1);
												$PostMetaData[$postmeta->post_id][$table_rate_key] = $trq_val;
											} else if($table_rate_key == 'table_price') {
												$tbl_price_amt = null;
												foreach($table_rate_val as $tbl_price) {
													$tbl_price_amt .= $tbl_price . '|';
												}
												$tbl_price_amt = substr($tbl_price_amt, 0, -1);
												$PostMetaData[$postmeta->post_id][$table_rate_key] = $tbl_price_amt;
											} else {
												$PostMetaData[$postmeta->post_id][$table_rate_key] = $table_rate_val;
											}
										}
									}
									else {
										$PostMetaData[$postmeta->post_id][$prod_md_key] = $prod_md_val;
									}
								}
							}
							// Wp e-Commerce product meta datas ends here
							// Eshop product meta datas starts here
							else if ($postmeta->meta_key == 'featured') {
								$isFeatured = strtolower($postmeta->meta_value);
								$PostMetaData[$postmeta->post_id]['featured_product'] = $isFeatured;
							}
							else if ($postmeta->meta_key == 'sale') {
								$is_prod_sale = strtolower($postmeta->meta_value);
								$PostMetaData[$postmeta->post_id]['product_in_sale'] = $is_prod_sale;
							}
							else if ($postmeta->meta_key == '_eshop_stock') {
								if($postmeta->meta_value == 1) {
									$stock_available = 'yes';
								} else {
									$stock_available = 'no';
								}
								$PostMetaData[$postmeta->post_id]['stock_available'] = $stock_available;
							}
							else if ($postmeta->meta_key == 'cart_radio') {
								$PostMetaData[$postmeta->post_id]['cart_option'] = $postmeta->meta_value;
							}
							else if ($postmeta->meta_key == 'shiprate') {
								$PostMetaData[$postmeta->post_id]['shiprate'] = $postmeta->meta_value;
							}
							else if ($postmeta->meta_key == '_eshop_product') {
								$product_attr_details = unserialize($postmeta->meta_value);
								$prod_option = $sale_price = $reg_price = null;
								#print('<pre>');print_r($product_attr_details); #die;
								foreach($product_attr_details as $prod_att_det_Key => $prod_att_det_Val) {
									if($prod_att_det_Key == 'sku') {
										$PostMetaData[$postmeta->post_id]['sku'] = $prod_att_det_Val;
									}
									else if($prod_att_det_Key == 'products') {
										foreach($prod_att_det_Val as $all_prod_options) {
											$prod_option .= $all_prod_options['option'] . ',';
											$sale_price .= $all_prod_options['saleprice'] . ',';
											$reg_price .= $all_prod_options['price'] . ',';
										}
										$prod_option = substr($prod_option, 0, -1);
										$sale_price = substr($sale_price, 0, -1);
										$reg_price = substr($reg_price, 0, -1);
										$PostMetaData[$postmeta->post_id]['products_option'] = $prod_option;
										$PostMetaData[$postmeta->post_id]['sale_price'] = $sale_price;
										$PostMetaData[$postmeta->post_id]['regular_price'] = $reg_price;
									}
								}
								#$PostMetaData[$postmeta->post_id]['cart_option'] = $postmeta->meta_value;
							}
							// Eshop product meta datas ends here
							else if ($postmeta->meta_key == '_thumbnail_id') {
								$attachment_file = '';
								$get_attachement = "select guid from $wpdb->posts where ID = $postmeta->meta_value AND post_type = 'attachment'";
								$attachment = $wpdb->get_results($get_attachement);
								$attachment_file = $attachment[0]->guid;
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = '';
								$postmeta->meta_key = 'featured_image';
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $attachment_file;
							}
							else if(is_array($this->getACFvalues()) && in_array($postmeta->meta_key, $this->getACFvalues())){
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = '';
								$eshop_products = unserialize($eshop_products); //print_r($eshop_products);
								foreach ($eshop_products as $key) {
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] .= $key . ',';
								}
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = substr($PostMetaData[$postmeta->post_id][$postmeta->meta_key], 0, -1);
							}
							else if(is_array($this->getACFprovalues()) && in_array($postmeta->meta_key,$this->getACFprovalues())){
								$chckval = @unserialize($postmeta->meta_value);
								if(!empty($chckval)){
									$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = implode(',',$chckval);
								}

							}
							else {
								$PostMetaData[$postmeta->post_id][$postmeta->meta_key] = $postmeta->meta_value;
							}
						}
					}
				}
				#				print('PostMetaData: '); print('<pre>'); print_r($Header); print_r($PostMetaData); #die;
				$TermsData[$postID] = $this->getAllTerms($postID, $exporttype, $this->headers);
			}

			$ExportData = array();
			// Merge all arrays
			//echo '<pre>'; print_r($TermsData); die;
			// echo '<pre>'; print_r($PostData); die('sds');
			//echo '<pre>'; print_r($PostMetaData); echo '</pre>';
			foreach ($PostData as $pd_key => $pd_val) {
				//echo '<pre>'; print_r($pd_key); echo '</pre>'; die('jj');
				if (array_key_exists($pd_key, $PostMetaData)) {
					$ExportData[$pd_key] = array_merge($PostData[$pd_key], $PostMetaData[$pd_key]);
					//  echo '<pre>'; print_r($ExportData); die('exist');
				} else {
					$ExportData[$pd_key] = $PostData[$pd_key];
				}
				if (array_key_exists($pd_key, $TermsData)) {
					if (empty($ExportData[$pd_key]))
						$ExportData[$pd_key] = array();
					$ExportData[$pd_key] = array_merge($ExportData[$pd_key], $TermsData[$pd_key]);
				}
			}
		}
		//echo '<pre>';print_r($ExportData);echo '</pre>';die();
		//print('<pre>'); print_r($Header);
		//print('<pre>'); print_r($ExportData); die;
		#print_r($this->WoocommerceMetaHeaders()); #die;
		if($exporttype == 'woocommerce' || $exporttype == 'eshop' || $exporttype == 'wpcommerce' || $exporttype == 'marketpress')
			$ExportData = $this->set_ecomdata($exporttype, $this->headers, $ExportData);

		#			print('<pre>'); print_r($ExportData); print_r($this->getAIOSEOfields()); print_r($this->getYoastSEOfields());die;
		$CSVContent = array();
		$otherfields = array();
		//            echo '<pre>'; print_r($this->getTypesFields()); echo '</pre>'; echo '<pre>';print_r($Header);echo '</pre>';
		foreach ($this->headers as $header_key) {
			if (is_array($ExportData)) {
				foreach ($ExportData as $ED_key => $ED_val) {
					if(isset($header_key)){
						if (array_key_exists($header_key, $ED_val)) {
							$CSVContent[$ED_key][$header_key] = $ED_val[$header_key];}
						else {
							$CSVContent[$ED_key][$header_key] = null; }
						if (is_array($this->getAIOSEOfields()) && in_array($header_key, $this->getAIOSEOfields()))
							$otherfields = array_merge($otherfields,$this->getAIOSEOfields());
						if (is_array($this->getYoastSEOfields()) && in_array($header_key, $this->getYoastSEOfields()))
							$otherfields = array_merge($otherfields,$this->getYoastSEOfields());
						if (is_array($this->getTypesFields()) && in_array($header_key, $this->getTypesFields() ))
							$otherfields = array_merge($otherfields,$this->getTypesFields());
						else if (is_array($this->getTypesFields()) && array_key_exists('wpcf-'.$header_key,$this->getTypesFields()))
							$otherfields = array_merge($otherfields,$this->getTypesFields());
						if(!empty($otherfields)){
							foreach($otherfields as $otherkey => $otherval){
								if($header_key == $otherval){
									if(!empty($ED_val[$otherkey]))
										$CSVContent[$ED_key][$otherval] = $ED_val[$otherkey];
									else
										$CSVContent[$ED_key][$otherval] = null;
								}
								else if($otherkey == 'wpcf-'.$header_key){
									//echo '<pre>';print_r($header_key);echo '<pre>';print_r($otherval);echo '</pre>';echo 'jjjjjjjjjjj';die;
									if(is_array($CSVContent[$ED_key])){
										if(!empty($ED_val[$otherkey]) && !array_key_exists($otherval,$CSVContent[$ED_key])){
											$CSVContent[$ED_key][$otherval] = $ED_val[$otherkey];
											unset($CSVContent[$ED_key][$header_key]);
										}
									}
									else
										$CSVContent[$ED_key][$otherval] = null;

								}
							}
						}
						if(array_key_exists('_wpsc_'.$header_key,$ED_val)) { // WP e-Commerce Custom Fiels
							if( is_serialized( $ED_val['_wpsc_'.$header_key] ) ) {
								$unserialized_wpcf_data = unserialize( $ED_val['_wpsc_'.$header_key] );
								if(!empty($unserialized_wpcf_data))
									$CSVContent[$ED_key][$header_key] = implode('|',$unserialized_wpcf_data);
							} else {
								$CSVContent[$ED_key][$header_key] = $ED_val['_wpsc_'.$header_key];
							}
						}
					}
					else {
						$CSVContent[$ED_key][$header_key] = null;
					}
				}
			}
		}
		$result = $this->finalDataToExport($CSVContent);
		$this->proceedExport($result);
	} */
	public function finalDataToExport ($data) {
		$result = array();
		foreach ($this->headers as $hKey) {
			foreach ( $data as $recordId => $rowValue ) {
				if(array_key_exists($hKey, $rowValue)):
					$result[$recordId][$hKey] = $rowValue[$hKey];
				else:
					$result[$recordId][$hKey] = '';
				endif;
			}
		}
		return $result;
	}

	public function proceedExport ($data) {
		#print_r($data); die('sssss');
		#$loggerObj = new Logging();
		#print_r($loggerObj); die;
		$csvData = $this->unParse($data, $this->headers);
		if(!is_dir(WP_CONST_ULTIMATE_CSV_IMP_EXPORT_DIR)) {
			wp_mkdir_p(WP_CONST_ULTIMATE_CSV_IMP_EXPORT_DIR);
		}
		$file = WP_CONST_ULTIMATE_CSV_IMP_EXPORT_DIR . $this->fileName . '.' . $this->exportType;
		#print ($file); die;
		if ($this->offset == 0) :
			if(file_exists($file))
				unlink($file);
		endif;
		try {
			file_put_contents( $file, $csvData, FILE_APPEND | LOCK_EX );
		} catch (Exception $e) {
			print_r($e);
			#$loggerObj->logW('', $e);
		}
		#print_r($csvData);
		#print_r($result);
		$this->offset = $this->offset + $this->limit;
		#print ('Offset: ' . $this->offset . ' || Limit: ' . $this->limit);
		$export_file_url = WP_CONST_ULTIMATE_CSV_IMP_EXPORT_URL . $this->fileName . '.' . $this->exportType;
		$responseTojQuery = array('new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $export_file_url);
		echo json_encode($responseTojQuery);
	}


	/**
	 * Create CSV data from array
	 * @param array $data       2D array with data
	 * @param array $fields     field names
	 * @param bool $append      if true, field names will not be output
	 * @param bool $is_php      if a php die() call should be put on the first
	 *                          line of the file, this is later ignored when read.
	 * @param null $delimiter   field delimiter to use
	 *
	 * @return string           CSV data (text string)
	 */
	public function unParse ( $data = array(), $fields = array(), $append = false , $is_php = false, $delimiter = null) {
		if ( !is_array($data) || empty($data) ) $data = &$this->data;
		if ( !is_array($fields) || empty($fields) ) $fields = &$this->titles;
		if ( $delimiter === null ) $delimiter = $this->delimiter;

		$string = ( $is_php ) ? "<?php header('Status: 403'); die(' '); ?>".$this->linefeed : '' ;
		$entry = array();

		// create heading
		if ($this->offset == 0) :
			if ( $this->heading && !$append && !empty($fields) ) {
				foreach( $fields as $key => $value ) {
					$entry[] = $this->_enclose_value($value);
				}
				$string .= implode($delimiter, $entry).$this->linefeed;
				$entry = array();
			}
		endif;

		// create data
		foreach( $data as $key => $row ) {
			foreach( $row as $field => $value ) {
				$entry[] = $this->_enclose_value($value);
			}
			$string .= implode($delimiter, $entry).$this->linefeed;
			$entry = array();
		}

		return $string;
	}

	/**
	 * Enclose values if needed
	 *  - only used by unParse()
	 * @param null $value
	 *
	 * @return mixed|null|string
	 */
	public function _enclose_value ($value = null) {
		if ( $value !== null && $value != '' ) {
			$delimiter = preg_quote($this->delimiter, '/');
			$enclosure = preg_quote($this->enclosure, '/');
			if($value[0]=='=') $value="'".$value; # Fix for the Comma separated vulnerabilities.
			if ( preg_match("/".$delimiter."|".$enclosure."|\n|\r/i", $value) || ($value{0} == ' ' || substr($value, -1) == ' ') ) {
				$value = str_replace($this->enclosure, $this->enclosure.$this->enclosure, $value);
				$value = $this->enclosure.$value.$this->enclosure;
			}
		}
		return $value;
	}

	/**
	 * @param $request
	 */
	public function WPImpExportCategories($request) {
		$this->headers = array('name', 'slug', 'description', 'wpseo_title', 'wpseo_desc', 'wpseo_canonical', 'wpseo_noindex', 'wpseo_sitemap_include');
		$this->generateHeaders($this->headers);
		$get_all_terms = get_categories("hide_empty=0&number=$this->limit&offset=$this->offset");

		$this->totalRowCount = wp_count_terms( 'category' );
		if(!empty($get_all_terms)) {
			foreach( $get_all_terms as $termKey => $termValue ) {
				$termID = $termValue->term_id;
				$termName = $termValue->cat_name;
				$termSlug = $termValue->slug;
				$termDesc = $termValue->category_description;
				$termParent = $termValue->parent;
				if($termParent == 0) {
					$TERM_DATA[$termID]['name'] = $termName;
				} else {
					$termParentName = get_cat_name( $termParent );
					$TERM_DATA[$termID]['name'] = $termParentName . '|' . $termName;
				}
				$TERM_DATA[$termID]['slug'] = $termSlug;
				$TERM_DATA[$termID]['description'] = $termDesc;
			}
		}
		if(in_array('wordpress-seo/wp-seo.php', $this->get_active_plugins())) {
			$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
			if ( isset( $seo_yoast_taxonomies['category'] ) ) {
				foreach ( $seo_yoast_taxonomies['category'] as $taxoKey => $taxoValue ) {
					$taxoID                                         = $taxoKey;
					$TERM_DATA[ $taxoID ]['wpseo_title']           = $taxoValue['wpseo_title'];
					$TERM_DATA[ $taxoID ]['wpseo_desc']            = $taxoValue['wpseo_desc'];
					$TERM_DATA[ $taxoID ]['wpseo_canonical']       = $taxoValue['wpseo_canonical'];
					$TERM_DATA[ $taxoID ]['wpseo_noindex']         = $taxoValue['wpseo_noindex'];
					$TERM_DATA[ $taxoID ]['wpseo_sitemap_include'] = $taxoValue['wpseo_sitemap_include'];
				}
			}
		}

		$result = $this->finalDataToExport($TERM_DATA);
		$this->proceedExport($result);
	}

	/**
	 * @param $request
	 */
	public function WPImpExportTags($request) {
		$this->headers = array('name', 'slug', 'description', 'wpseo_title', 'wpseo_desc', 'wpseo_canonical', 'wpseo_noindex', 'wpseo_sitemap_include');
		$this->generateHeaders($this->headers);
		$get_all_terms = get_tags("hide_empty=0&number=$this->limit&offset=$this->offset");
		$this->totalRowCount = wp_count_terms( 'post_tag' );
		if(!empty($get_all_terms)) {
			foreach( $get_all_terms as $termKey => $termValue ) {
				$termID = $termValue->term_id;
				$termName = $termValue->name;
				$termSlug = $termValue->slug;
				$termDesc = $termValue->description;
				$termParent = $termValue->parent;
				if($termParent == 0) {
					$TERM_DATA[$termID]['name'] = $termName;
				} else {
					$termParentName = get_cat_name( $termParent );
					$TERM_DATA[$termID]['name'] = $termParentName . '|' . $termName;
				}
				$TERM_DATA[$termID]['slug'] = $termSlug;
				$TERM_DATA[$termID]['description'] = $termDesc;
			}
		}
		if(in_array('wordpress-seo/wp-seo.php', $this->get_active_plugins())) {
			$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
			if ( isset( $seo_yoast_taxonomies['category'] ) ) {
				foreach ( $seo_yoast_taxonomies['category'] as $taxoKey => $taxoValue ) {
					$taxoID                                         = $taxoKey;
					$TERM_DATA[ $taxoID ]['wpseo_title']           = $taxoValue['wpseo_title'];
					$TERM_DATA[ $taxoID ]['wpseo_desc']            = $taxoValue['wpseo_desc'];
					$TERM_DATA[ $taxoID ]['wpseo_canonical']       = $taxoValue['wpseo_canonical'];
					$TERM_DATA[ $taxoID ]['wpseo_noindex']         = $taxoValue['wpseo_noindex'];
					$TERM_DATA[ $taxoID ]['wpseo_sitemap_include'] = $taxoValue['wpseo_sitemap_include'];
				}
			}
		}
		$result = $this->finalDataToExport($TERM_DATA);
		$this->proceedExport($result);
	}

	/**
	 * @param $request
	 */
	public function WPImpExportTaxonomies($request) {
		$this->headers = array('name', 'slug', 'description', 'wpseo_title', 'wpseo_desc', 'wpseo_canonical', 'wpseo_noindex', 'wpseo_sitemap_include');
		$this->generateHeaders($this->headers);
		$taxonomy_name = sanitize_text_field($this->optionalType);
		$get_all_terms = get_terms($taxonomy_name, array(
			'hide_empty' => false,
			'number' => $this->limit,
			'offset' => $this->offset
		));

		$this->totalRowCount = wp_count_terms( $taxonomy_name );
		if(!empty($get_all_terms)) {
			foreach( $get_all_terms as $termKey => $termValue ) {
				$termID = $termValue->term_id;
				$termName = $termValue->name;
				$termSlug = $termValue->slug;
				$termDesc = $termValue->description;
				$termParent = $termValue->parent;
				if($termParent == 0) {
					$TERM_DATA[$termID]['name'] = $termName;
				} else {
					$termParentName = get_cat_name( $termParent );
					$TERM_DATA[$termID]['name'] = $termParentName . '|' . $termName;
				}
				$TERM_DATA[$termID]['slug'] = $termSlug;
				$TERM_DATA[$termID]['description'] = $termDesc;
			}
		}
		if(in_array('wordpress-seo/wp-seo.php', $this->get_active_plugins())) {
			$seo_yoast_taxonomies = get_option( 'wpseo_taxonomy_meta' );
			if ( isset( $seo_yoast_taxonomies['category'] ) ) {
				foreach ( $seo_yoast_taxonomies['category'] as $taxoKey => $taxoValue ) {
					$taxoID                                         = $taxoKey;
					$TERM_DATA[ $taxoID ]['wpseo_title']           = $taxoValue['wpseo_title'];
					$TERM_DATA[ $taxoID ]['wpseo_desc']            = $taxoValue['wpseo_desc'];
					$TERM_DATA[ $taxoID ]['wpseo_canonical']       = $taxoValue['wpseo_canonical'];
					$TERM_DATA[ $taxoID ]['wpseo_noindex']         = $taxoValue['wpseo_noindex'];
					$TERM_DATA[ $taxoID ]['wpseo_sitemap_include'] = $taxoValue['wpseo_sitemap_include'];
				}
			}
		}
		$result = $this->finalDataToExport($TERM_DATA);
		$this->proceedExport($result);
	}

	/**
	 * @param $request
	 */
	public function WPImpExportCustomerReviews($request) {
		global $wpdb;
		$ExportData = array();
		$exporttype = sanitize_text_field($request['export']);
		$wpcsvsettings = get_option('wpcsvprosettings');
		$export_delimiter = $this->set_exportdelimiter();
		$result = array();
		if($_POST['export_filename'])
			$csv_file_name =$_POST['export_filename'].'.csv';
		else
			$csv_file_name='exportas_'.date("Y").'-'.date("m").'-'.date("d").'.csv';

		$this->headers = array( 'id', 'date_time', 'reviewer_name', 'reviewer_email', 'reviewer_ip', 'review_title', 'review_text', 'review_response', 'status', 'review_rating', 'reviewer_url', 'page_id', 'custom_field1', 'custom_field2', 'custom_field3', 'review_format');
		$this->generateHeaders($this->headers);
		$get_available_plugin_lists = get_option('active_plugins');
		if(in_array('wp-customer-reviews/wp-customer-reviews-3.php', $get_available_plugin_lists)) {
			$header_query = "SELECT * FROM $wpdb->posts where post_type = 'wpcr3_review'";

			$conditions = $this->conditions;
			// Check for specific period
			if($conditions['specific_period']['is_check'] == 'true') {
				$header_query .= " and p.post_date >= '" . $conditions['specific_period']['from'] . "' and p.post_date <= '" . $conditions['specific_period']['to'] . "'";
			}

			$get_total_row_count = $wpdb->get_col($header_query);
			$this->totalRowCount = count($get_total_row_count);
			$header_query .= " order by ID asc limit $this->offset, $this->limit";
			$reviews = $wpdb->get_results($wpdb->prepare($header_query));

			if( !empty($reviews) ) {
				foreach ( $reviews as $key => $val ) {
					$result[ $val->ID ]['id']          = $val->ID;
					$result[ $val->ID ]['date_time']   = $val->post_date;
					$result[ $val->ID ]['review_text'] = $val->post_content;
					if ( $val->post_password == '' ):
						$result[ $val->ID ]['status'] = $val->post_status;
					else:
						$result[ $val->ID ]['status'] = $val->post_password;
					endif;
					$meta_query  = "SELECT * FROM $wpdb->postmeta WHERE post_id = $val->ID";
					$review_meta = $wpdb->get_results( $wpdb->prepare( $meta_query ) );
					foreach ( $review_meta as $mkey => $mval ) {
						switch ($mval->meta_key) {
							case 'wpcr3_review_ip':
								$result[ $val->ID ]['reviewer_ip'] = $mval->meta_value;
								break;
							case 'wpcr3_review_post':
								$result[ $val->ID ]['page_id'] = $mval->meta_value;
								$reviewFormat = get_post_meta($mval->meta_value, 'wpcr3_format');
								$result[ $val->ID ]['review_format'] = $reviewFormat[0];
								break;
							case 'wpcr3_review_name':
								$result[ $val->ID ]['reviewer_name'] = $mval->meta_value;
								break;
							case 'wpcr3_review_email':
								$result[ $val->ID ]['reviewer_email'] = $mval->meta_value;
								break;
							case 'wpcr3_review_rating':
								$result[ $val->ID ]['review_rating'] = $mval->meta_value;
								break;
							case 'wpcr3_review_title':
								$result[ $val->ID ]['review_title'] = $mval->meta_value;
								break;
							case 'wpcr3_review_website':
								$result[ $val->ID ]['reviewer_url'] = $mval->meta_value;
								break;
							case 'wpcr3_review_admin_response':
								$result[ $val->ID ]['review_response'] = $mval->meta_value;
								break;
							case 'wpcr3_f1':
								$result[ $val->ID ]['custom_field1'] = $mval->meta_value;
								break;
							case 'wpcr3_f2':
								$result[ $val->ID ]['custom_field2'] = $mval->meta_value;
								break;
							case 'wpcr3_f3':
								$result[ $val->ID ]['custom_field3'] = $mval->meta_value;
								break;
						}
					}
				}
			}
			if( !empty($result) ) {
				foreach ($this->headers as $hKey) {
					foreach ( $result as $recordId => $rowValue ) {
						if(array_key_exists($hKey, $rowValue)):
							$ExportData[$recordId][$hKey] = $rowValue[$hKey];
						else:
							$ExportData[$recordId][$hKey] = '';
						endif;
					}
				}
			}
		} else {
			$header_query = "SELECT * FROM  wp_wpcreviews";
			$get_total_row_count = $wpdb->get_col($header_query);
			$this->totalRowCount = count($get_total_row_count);
			$header_query .= " order by id asc limit $this->offset, $this->limit";
			$result = $wpdb->get_results( $header_query );
			if ( ! empty( $result ) ) {
				foreach ( $result as $rhq1_key ) {
					foreach ( $rhq1_key as $rhq1_headkey => $rhq1_headval ) {
						if ( ! in_array( $rhq1_headkey, $this->headers ) ) {
							$Header[] = $rhq1_headkey;
						}
					}
				}
				$postData = array();
				if(isset($result)) {
					$i =0;
					foreach ($result as $postID) {
						foreach ($postID as $Key => $Val){
							$postData[$i][$Key] = $Val;
						}
						$i++;
					}
					$ExportData = $postData;
					$j=0;
					if(!empty($Header)){
						foreach($Header as $key)
						{
							$key=$j;
							$header[] = $key;
							$j++;
						}
					}
				}
			}
		}
		$result = $this->finalDataToExport($ExportData);
		$this->proceedExport($result);
	}

	/**
	 * @param $request
	 */
	public function WPImpExportComments($request) {
		global $wpdb;
		$get_comments = "select *from $wpdb->comments";
		$get_comments .= " where comment_approved in (0,1)";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			$get_comments .= " and c.comment_date >= '" . $this->conditions['specific_period']['from'] . "' and c.comment_date <= '" . $this->conditions['specific_period']['to'] . "'";
		}
		// Check for specific authors
		if($this->conditions['specific_authors']['is_check'] == 'true') {
			if(isset($this->conditions['specific_authors']['author']) && $this->conditions['specific_authors']['author'] != 0) {
				$get_user_info = get_userdata($this->conditions['specific_authors']['author']);
				$user_email = $get_user_info->data->user_email;
				$get_comments .= " and c.comment_author_email = $user_email"; //{$this->conditions['specific_authors']['author']}";
			}
		}
		$get_comments .= " order by comment_ID asc limit $this->offset, $this->limit";
		$comments = $wpdb->get_results( $get_comments );
		$this->totalRowCount = count($comments);
		if(!empty($comments)) {
			foreach($comments as $commentInfo) {
				foreach($commentInfo as $commentKey => $commentVal) {
					if(!in_array($commentKey, $this->headers)) {
						$this->headers[] = $commentKey;
					}
					$this->data[$commentInfo->comment_ID][$commentKey] = $commentVal;
				}
			}
		}
		$result = $this->finalDataToExport($this->data);
		$this->proceedExport($result);
	}

	/**
	 * @param $request
	 */
	public function WPImpExportUsers($request) {
		global $wpdb;
		$Header = array();
		$header_query1 = "SELECT * FROM $wpdb->users";
		$header_query2 = $wpdb->prepare("SELECT user_id, meta_key, meta_value FROM  $wpdb->users wp JOIN $wpdb->usermeta wpm ON wpm.user_id = wp.ID where meta_key NOT IN (%s,%s)",'_edit_lock','_edit_last');
		$result_header_query1 = $wpdb->get_results($header_query1);
		$result_header_query2 = $wpdb->get_results($header_query2);
		foreach ($result_header_query1 as $rhq1_key) {
			foreach ($rhq1_key as $rhq1_headkey => $rhq1_headval) {
				if (!in_array($rhq1_headkey, $Header))
					$Header[] = $rhq1_headkey;
			}
		}
		foreach ($result_header_query2 as $rhq2_headkey) {
			if (!in_array($rhq2_headkey->meta_key, $Header)) {
				if($rhq2_headkey->meta_key == 'mp_shipping_info' )
				{
					$mp_ship_header= unserialize($rhq2_headkey->meta_value);
					foreach($mp_ship_header as $mp_ship_key => $mp_value) { $Header[] = "msi: ".$mp_ship_key; }
				}
				if($rhq2_headkey->meta_key == 'mp_billing_info' )
				{
					$mp_ship_header= unserialize($rhq2_headkey->meta_value);
					foreach($mp_ship_header as $mp_ship_key => $mp_value) { $Header[] = "mbi: ".$mp_ship_key; }
				}

				if ($rhq2_headkey->meta_key != '_eshop_product' && $rhq2_headkey->meta_key != '_wp_attached_file' && $rhq2_headkey->meta_key != 'mp_shipping_info' && $rhq2_headkey->meta_key != 'mp_billing_info' )
					$Header[] = $rhq2_headkey->meta_key;
			}
		}
		#$this->headers = $Header;
		$this->generateHeaders($Header);
		#$this->generateHeaders($this->module, $this->optionalType);
		$get_available_user_ids = "select DISTINCT ID from $wpdb->users u join $wpdb->usermeta um on um.user_id = u.ID";
		$get_available_user_ids .= " order by ID asc limit $this->offset, $this->limit";
		$availableUsers = $wpdb->get_col($get_available_user_ids);
		$this->totalRowCount = count($availableUsers);
		if(!empty($availableUsers)) {
			$whereCondition = '';
			foreach($availableUsers as $userId) {
				if($whereCondition != ''):
					$whereCondition = $whereCondition . ',' . $userId;
				else:
					$whereCondition = $userId;
				endif;
				// Prepare the user details to be export
				$query_to_fetch_users = "SELECT * FROM $wpdb->users where ID in ($whereCondition);";
				$users = $wpdb->get_results($query_to_fetch_users);
				if(!empty($users)) {
					foreach($users as $userInfo) {
						foreach($userInfo as $userKey => $userVal) {
							$this->data[$userId][$userKey] = $userVal;
						}
					}
				}
				// Prepare the user meta details to be export
				$query_to_fetch_users_meta = $wpdb->prepare("SELECT user_id, meta_key, meta_value FROM  $wpdb->users wp JOIN $wpdb->usermeta wpm  ON wpm.user_id = wp.ID where ID= %d", $userId);
				$userMeta = $wpdb->get_results($query_to_fetch_users_meta);
				if(!empty($userMeta)) {
					foreach($userMeta as $userMetaInfo) {
						if($userMetaInfo->meta_key == 'wp_capabilities') {
							$userRole = $this->getUserRole($userMetaInfo->meta_value);
							$this->data[ $userId ][ 'role' ] = $userRole;
						} else {
							$this->data[ $userId ][ $userMetaInfo->meta_key ] = $userMetaInfo->meta_value;
						}
					}
				}
			}
		}
		#print '<pre>'; print_r($this->data);
		$result = $this->finalDataToExport($this->data);
		#print_r($result); print '</pre>'; die;
		$this->proceedExport($result);
	}

	public function getUserRole ($capability = null) {
		if($capability != null) {
			$getRole = unserialize($capability);
			foreach($getRole as $roleName => $roleStatus) {
				$role = $roleName;
			}
			return $role;
		} else {
			return 'subscriber';
		}
	}

	public function get_active_plugins() {
		$active_plugins = get_option('active_plugins');
		return $active_plugins;
	}

	public function generateHeaders ($headers = array()) {
		#global $uci_admin;
		/* $integrations = $uci_admin->available_widgets($module, $optionalType);
		$headers = array();
		if(!empty($integrations)) :
			foreach($integrations as $widget_name => $group_name) {
				$fields = $uci_admin->get_widget_fields($widget_name, $module, $optionalType, 'export');
				if(!empty($fields)) {
					foreach($fields as $groupKey => $fieldArray) {
						if(!empty($fieldArray)) {
							foreach ( $fieldArray as $fKey => $fVal ) {
								if(!in_array($fVal['name'], $headers))
									$headers[] = $fVal['name'];
							}
						}
					}
				}
			}
		endif; */

		/*if(!empty($headers)) {
			foreach($headers as $hKey) {
				if(!in_array($hKey, $headers))
					$headers[] = $hKey;
			}
		}*/

		#print_r($headers); print_r($this->eventExclusions); die;
		$this->headers = array();
		if($this->eventExclusions['is_check'] == 'true') :
			$headers_with_exclusion = $this->applyEventExclusion($headers);
			$this->headers = $headers_with_exclusion;
		else:
			$this->headers = $headers;
		endif;
		#print_r($this->headers); die;
	}

	public function applyEventExclusion ($headers) {
		$header_exclusion = array();
		$exceptions = array('ID', 'term_id', 'user_id',);
		foreach ($headers as $hVal) {
			if(array_key_exists($hVal, $this->eventExclusions['exclusion_headers']) || in_array($hVal, $exceptions)) :
				$header_exclusion[] = $hVal;
			endif;
		}
		return $header_exclusion;
	}

	/**
	 *
	 **/

	public function set_exportdelimiter(){
		if(isset($_POST['getdatawithdelimiter']) && isset($_POST['postwithdelimiter']) && $_POST['postwithdelimiter'] != 'Select'){
			if($_POST['postwithdelimiter'] == "{Space}")
				$export_delimiter = " ";
			elseif($_POST['postwithdelimiter'] == "{Tab}")
				$export_delimiter = "\t";
			else
				$export_delimiter = $_POST['postwithdelimiter'];
		}elseif(isset($_POST['getdatawithdelimiter']) && !empty($_POST['others_delimiter'])){

			$export_delimiter = $_POST['others_delimiter'];
		}else{

			$export_delimiter = ',';
		}
		return $export_delimiter;
	}

	public function set_ecomdata($exporttype,$Header,$ExportData){
		switch($exporttype){
			case 'woocommerce' :{
				$ecomheader = $this->WoocommerceMetaHeaders();
				break;
			}
			case 'marketpress' :{
				$ecomheader = $this->MarketPressHeaders();
				break;
			}
			case 'wpcommerce' :{
				$ecomheader = $this->WpeCommerceHeaders();
				break;
			}
			case 'eshop' :{
				$ecomheader = $this->EshopHeaders();
				break;
			}
			default:{
				$ecomheader = array();
				break;
			}
		}
		foreach($Header as $hkey) {
			foreach($ExportData as $edkey => $edval) {
				if(!empty($ecomheader)){
					foreach($ecomheader as $ecomkey => $ecomval) {
						if(array_key_exists($ecomval, $ExportData[$edkey])) {
							$ExportData[$edkey][$ecomkey] = $edval[$ecomval];
							if($exporttype == 'woocommerce' || $exporttype == 'marketpress')
								unset($ExportData[$edkey][$ecomval]);
						}
					}
				}
			}
		}
		return $ExportData;

	}
}
