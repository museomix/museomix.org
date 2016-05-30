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
/**
* This class is used for classifying the core-fields, meta-fields, terms, taxonomies,
* custom-posts and eCommerce meta-fields based on their requested module.
* Date: 25/05/2016
* Plugin: WP Ultimate CSV Importer
* Author: Sujin
*/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class WPClassifyFields {

	public $wpcsvsettings = array();

	public function __construct() {
		$this->wpcsvsettings = get_option('wpcsvfreesettings');
	}

	public $def_mpcols = array('Shipping Email' => 'msi_email',
			'Shipping Name' => 'msi_name',
			'Shipping Address1' => 'msi_address1',
			'Shipping Address2' => 'msi_address2',
			'Shipping City' => 'msi_city',
			'Shipping State' => 'msi_state',
			'Shipping Zip' => 'msi_zip',
			'Shipping Country' => 'msi_country',
			'Shipping Phone' => 'msi_phone',
			'Billing Email' => 'mbi_email',
			'Billing Name' => 'mbi_name',
			'Billing Address1' => 'mbi_address1',
			'Billing Address2' => 'mbi_address2',
			'Billing City' => 'mbi_city',
			'Billing State' => 'mbi_state',
			'Billing Zip' => 'mbi_zip',
			'Billing Country' => 'mbi_country',
			'Billing Phone' => 'mbi_phone'
			);

	public $def_wccols = array('Billing First Name' => 'billing_first_name',
			'Billing Last Name' => 'billing_last_name',
			'Billing Company' => 'billing_company',
			'Billing Address1' => 'billing_address_1',
			'Billing Address2' => 'billing_address_2',
			'Billing City' => 'billing_city',
			'Billing PostCode' => 'billing_postcode',
			'Billing State' => 'billing_state',
			'Billing Country' => 'billing_country',
			'Billing Phone' => 'billing_phone',
			'Billing Email' => 'billing_email',
			'Shipping First Name' => 'shipping_first_name',
			'Shipping Last Name' => 'shipping_last_name',
			'Shipping Company' => 'shipping_company',
			'Shipping Address1' => 'shipping_address_1',
			'Shipping Address2' => 'shipping_address_2',
			'Shipping City' => 'shipping_city',
			'Shipping PostCode' => 'shipping_postcode',
			'Shipping State' => 'shipping_state',
			'Shipping Country' => 'shipping_country',
			'API Consumer Key' => 'woocommerce_api_consumer_key',
			'API Consumer Secret' => 'woocommerce_api_consumer_secret',
			'API Key Permissions' => 'woocommerce_api_key_permissions',
			'Shipping Region' => '_wpsc_shipping_region' ,
			'Billing Region' => '_wpsc_billing_region',
			'Cart' => '_wpsc_cart'
				);
	/**
	 * Function to get WP core fields based on requested module
	 * @param $module
	 */
	function WPCoreFields($module) {
		$active_plugins = get_option('active_plugins');
		if($module !== 'users' && $module !== 'customtaxonomy' && $module !== 'customerreviews' && $module !== 'categories' && $module !== 'comments' && $module !== 'woocommerce') {
			#$idefCols = array('core' => array('title' => array('label' => 'Post Title', 'name' => 'post_title'), 'content' => array('label' => 'Post Content', 'name' => 'post_content'),));
			$defCols = array('Post Title' => 'post_title',
			                 'Post Content' => 'post_content',
			                 'Post Excerpt' => 'post_excerpt',
			                 'Post Date' => 'post_date',
			                 'Post Slug' => 'post_name',
			                 'Post Author' => 'post_author',
			                 'Post Parent' => 'post_parent',
			                 'Post Status' => 'post_status',
			                 'Menu Order' => 'menu_order',
			                 'Comment Status' => 'comment_status',
			                 'Ping Status'	=> 'ping_status',
			                 'Featured Image' => 'featured_image',
				//'Language Code' => 'language_code',
			);
			if(in_array('events-manager/events-manager.php', $active_plugins) && $module === 'custompost'){
				$customarray = array(
					'Event_start_date' => 'event_start_date',
					'Event_end_date' => 'event_end_date',
					'Event_start_time' => 'event_start_time',
					'Event_end_time' => 'event_end_time',
					'Event_all_day' => 'event_all_day',
					//'Event_rsvp' => 'event_rsvp',
					'Event_rsvp_date' => 'event_rsvp_date',
					'Event_rsvp_time' => 'event_rsvp_time',
					'Event_recurrence' => 'event_recurrence',
					'Event_rsvp_spaces' => 'event_rsvp_spaces',
					'Event_spaces' => 'event_spaces',
					'Recurrence_interval' => 'recurrence_interval',
					'Recurrence_freq' => 'recurrence_freq',
					'Recurrence_byday' => 'recurrence_byday',
					'Recurrence_byweekno'=>'recurrence_byweekno',
					'Recurrence_days' => 'recurrence_days',
					'Recurrence_rsvp_days' => 'recurrence_rsvp_days',
					'Location_name' => 'location_name',
					'Location_address' => 'location_address',
					'Location_town' =>'location_town',
					'Location_state' => 'location_state',
					'Location_postcode' => 'location_postcode',
					'Location_region' => 'location_region',
					'Location_country' => 'location_country',
					'Ticket_name' => 'ticket_name',
					'Ticket_description' => 'ticket_description',
					'Ticket_price' => 'ticket_price',
					'Ticket_start_date' => 'ticket_start_date',
					'Ticket_end_date' => 'ticket_end_date',
					'Ticket_start_time' => 'ticket_start_time',
					'Ticker_end_time' => 'ticket_end_time',
					'Ticket_min' => 'ticket_min',
					'Ticket_max' => 'ticket_max',
					'Ticket_spaces' => 'ticket_spaces',
					'Ticket_members' => 'ticket_members',
					'Ticket_members_roles' =>'ticket_members_roles',
					'Ticket_guests' => 'ticket_guests',
					'Ticket_required' => 'ticket_required',
				);
				foreach($customarray as $key => $value){
					$defCols[$key] = $value;
				}
			}
			if (in_array('sitepress-multilingual-cms/sitepress.php', $active_plugins)) {
				$defCols['Language Code'] = 'language_code';
			}
		}
		if($module === 'post' || $module === 'custompost') {
			$defCols['Post Format'] = 'post_format';
		}
		if($module === 'page') {
			$defCols['Page Template'] = 'wp_page_template';
		}
		if($module === 'woocommerce') {
			$defCols = array('Post Title' => 'post_title',
			                 'Post Content' => 'post_content',
			                 'Post Excerpt' => 'post_excerpt',
			                 'Post Date' => 'post_date',
			                 'Post Slug' => 'post_name',
			                 'Post Author' => 'post_author',
			                 'Post Parent' => 'post_parent',
			                 'Post Status' => 'post_status',
			                 'Menu Order' => 'menu_order',
			                 'Comment Status' => 'comment_status',
			                 'Ping Status' => 'ping_status',
			                 'Featured Image' => 'featured_image',
			);
			if (in_array('sitepress-multilingual-cms/sitepress.php', $active_plugins)) {
				$defCols['Language Code'] = 'language_code';
			}


		}
		/*	if($module == 'marketpress'){
				$defCols = array('Post Title' => 'product_title',
						'Post Content' => 'Product_content',
						'Post Excerpt' => 'product_excerpt',
						'Post Date' =>  'product_publish_date',
						'Post Slug' => 'product_slug',
						'Post Author' => 'post_author',
						'Post Parent' => 'product_parent',
						'Post Status' => 'product_status',
						'Menu Order' => 'menu_order',
						'Comment Status' => 'comment_status',
						'Ping Status' => 'ping_status',
						'Featured Image' => 'featured_image',
						);
			}*/
		if($module === 'users') {
			$defCols = array('User Login' => 'user_login',
			                 'User Pass' => 'user_pass',
			                 'First Name' => 'first_name',
			                 'Last Name' => 'last_name',
			                 'Nick Name' => 'nickname',
			                 'User Email' => 'user_email',
			                 'User URL' => 'user_url',
			                 'User Nicename' => 'user_nicename',
			                 'User Registered' => 'user_registered',
			                 'User Activation Key' => 'user_activation_key',
			                 'User Status' => 'user_status',
			                 'Display Name' => 'display_name',
			                 'User Role' => 'role',
				//'Capabilities' => 'wp_capabilities',
			);
			$wpcsvfreesettings = get_option('wpcsvfreesettings');
			#if(in_array('marketpress',$wpcsvfreesettings )) {
				if(in_array('marketpress/marketpress.php', $active_plugins) || in_array('wordpress-ecommerce/marketpress.php', $active_plugins)) {
					foreach($this->def_mpcols as $mp_key => $mp_val) {
						$defCols[$mp_key] = $mp_val;
					}
				}
			#}
			#if(in_array('woocommerce',$wpcsvfreesettings )) {
				if(in_array('woocommerce/woocommerce.php', $active_plugins)) {
					foreach($this->def_wccols as $woo_key => $woo_val) {
						$defCols[$woo_key] = $woo_val;
					}
				}
			#}
		}
		if($module === 'customtaxonomy') {
			$defCols = array('Taxonomy Name' => 'name',
			                 'Taxonomy Slug' => 'slug',
			                 'Taxonomy Description' => 'description',
			                 'SEO Title' => 'wpseo_title',
			                 'SEO Description' => 'wpseo_desc',
			                 'Canonical' => 'wpseo_canonical',
			                 'Noindex this category' => 'wpseo_noindex',
			                 'Include in sitemap?' => 'wpseo_sitemap_include',
			);
		}
		if($module === 'customerreviews') {
			$defCols = array(
				'Review Id'     => 'review_id',
				'Review Date Time' => 'date_time',
				'Reviewer Name' => 'reviewer_name',
				'Reviewer Email' => 'reviewer_email',
				'Reviewer IP' => 'reviewer_ip',
				'Review Title' => 'review_title',
				'Review Text' => 'review_text',
				'Review Response' => 'review_response',
				'Review Status' => 'status',
				'Review Rating' => 'review_rating',
				'Review URL'	=> 'reviewer_url',
				'Review to Post/Page Id' => 'page_id',
				'Custom Field #1' => 'custom_field1',
				'Custom Field #2' => 'custom_field2',
				'Custom Field #3' => 'custom_field3',
				'Review Format' => 'review_format',
			);
		}
		if($module === 'categories') {
			$defCols = array('Category Name' => 'name',
			                 'Category Slug' => 'slug',
			                 'Category Description' => 'description',
			                 'SEO Title' => 'wpseo_title',
			                 'SEO Description' => 'wpseo_desc',
			                 'Canonical' => 'wpseo_canonical',
			                 'Noindex this category' => 'wpseo_noindex',
			                 'Include in sitemap?' => 'wpseo_sitemap_include',
			);
			if (in_array('sitepress-multilingual-cms/sitepress.php', $active_plugins)) {
				$defCols['Language Code'] = 'language_code';
			}

		}
		if($module === 'comments') {
			$defCols = array('Comment Post Id' => 'comment_post_ID',
			                 'Comment Author' => 'comment_author',
			                 'Comment Author Email' => 'comment_author_email',
			                 'Comment Author URL' => 'comment_author_url',
			                 'Comment Content' => 'comment_content',
			                 'Comment Author IP' => 'comment_author_IP',
			                 'Comment Date' => 'comment_date',
			                 'Comment Approved' => 'comment_approved',
			);
		}
		if($module === 'woocommerce_variations'){
			$defCols = array('Product Id' => 'PRODUCTID',
			                 'Parent Sku' => 'PARENTSKU',
			                 'Variation Sku' => 'variation_sku',
			                 'Variation ID' => 'VARIATIONID');

		}
		if($module === 'woocommerce_coupons'){
			$defCols = array('Coupon Code' => 'coupon_code',
			                 'Description' => 'description',
			                 'Coupon Id' =>'COUPONID');
		}
		if($module === 'woocommerce_orders'){
			$defCols = array('Customer Note' => 'customer_note',
			                 'Post Status' => 'post_status',
			                 'Order Id' => 'ORDERID');

		}
		if($module === 'woocommerce_refunds'){
			$defCols = array('Post Parent' => 'post_parent',
			                 'Post Excerpt' => 'post_excerpt',
			                 'Refund Id' => 'REFUNDID');
		}
		if($module === 'marketpress'){
			if (in_array('marketpress/marketpress.php', $active_plugins)) {
				$defCols['Product Id'] = 'PRODUCTID';
				$defCols['Variation ID'] = 'VARIATIONID';
			}
		}
		foreach ( $defCols as $key => $val ) {
			$coreFields['CORE'][$val]['label'] = $key;
			$coreFields['CORE'][$val]['name'] = $val;
		}

		return $coreFields;
	}

	/**
	 * Function to get ACF custom fields & ACF repeater fields
	 */
	public function ACFCustomFields() {
		$acf_field = $customFields = array();
		global $wpdb;
		$active_plugins = get_option('active_plugins');
		$wpcsvfreesettings = get_option('wpcsvfreesettings');
		#if (array_key_exists('acfcustomfield', $wpcsvfreesettings)) {
			$acf_value = array();
			if (in_array('advanced-custom-fields/acf.php', $active_plugins) || in_array('advanced-custom-fields-pro/acf.php', $active_plugins)) {
				/** For Support the ACF Pro before and after version of 5.3.7 **/
				$acf_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields';
				$acfPath = WP_PLUGIN_DIR . '/advanced-custom-fields-pro';
				if((is_dir($acf_pluginPath) && is_dir($acf_pluginPath . '/pro')) || is_dir($acfPath))  {
					//$get_acf_groups = $wpdb->get_col("SELECT ID FROM $wpdb->posts where post_type = 'acf-field-group'");
					$get_acf_groups = $wpdb->get_col($wpdb->prepare("select ID from $wpdb->posts where post_type = %s",'acf-field-group'));
					$group_id_arr = $repeater_field_arr = $flexible_field_arr = "";
					$getCustomFieldsArr = array();
					$rep_customFields = array();
					foreach($get_acf_groups as $groupID) {
						$group_id_arr .= $groupID . ',';
					}
					$group_id_arr = substr($group_id_arr, 0, -1);
					if(!empty($group_id_arr))
						$get_acf_fields = $wpdb->get_results("SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in ($group_id_arr)");
					#print_r($get_acf_fields); #die('smackcoders');
					if(!empty($get_acf_fields)) {
						foreach($get_acf_fields as $acfpro_fields) {
							$unserialized_field_content = unserialize($acfpro_fields->post_content);
							if($unserialized_field_content['type'] == 'repeater') {
								$repeater_field_arr .= $acfpro_fields->ID . ",";
								//multi sup repeater
								$repeater_field = substr($repeater_field_arr, 0, -1);
								//echo '<pre>'; print_r($repeater_field_ar); echo '</pre>';
								$get_sub_fields = $wpdb->get_results("SELECT ID, post_title, post_content, post_excerpt, post_name FROM $wpdb->posts where post_parent in ($repeater_field)");
								foreach($get_sub_fields as $get_sub_key){
									$unserial_sub = unserialize($get_sub_key->post_content);
									if($unserial_sub['type'] == 'repeater')
										$repeater_field_arr .= $get_sub_key->ID. ",";
								}
								//multi sup repeater
							} else if($unserialized_field_content['type'] == 'flexible_content') {
								$flexible_field_arr .= $acfpro_fields->ID . ",";
							} else if($unserialized_field_content['type'] == 'message' || $unserialized_field_content['type'] == 'tab') {
								$customFields["ACF"][$acfpro_fields->post_name]['label'] = $acfpro_fields->post_title;
								$customFields["ACF"][$acfpro_fields->post_name]['name'] = $acfpro_fields->post_name;
							} else {
								#						$getCustomFieldsArr[$acfpro_fields->post_title] = $acfpro_fields->post_excerpt;
								if($acfpro_fields->post_excerpt != null || $acfpro_fields->post_excerpt != '') {
									$customFields["ACF"][$acfpro_fields->post_excerpt]['label'] = $acfpro_fields->post_title;
									$customFields["ACF"][$acfpro_fields->post_excerpt]['name'] = $acfpro_fields->post_excerpt;
								}
							}
						}
					}
					//echo '<pre>'; print_r($get_fields); echo '</pre>';
					#print_r($repeater_field_arr); die;
					#print('<pre>'); print_r($customFields); print('</pre>');die;
					$repeater_field_arr = substr($repeater_field_arr, 0, -1);
					$flexible_field_arr = substr($flexible_field_arr, 0, -1);

					if(!empty($repeater_field_arr)) {
						$get_acf_repfields = $wpdb->get_results("SELECT ID, post_title, post_content, post_excerpt FROM $wpdb->posts where post_parent in ($repeater_field_arr)");
					}
					if(!empty($get_acf_repfields)) {
						foreach($get_acf_repfields as $acfpro_repfields) {
							$rep_customFields[$acfpro_repfields->post_title] = $acfpro_repfields->post_excerpt;
							$check_exist_key = "ACF: " . $acfpro_repfields->post_title;
							if(array_key_exists($check_exist_key, $customFields)) {
								unset($customFields[$check_exist_key]);
							}
							$customFields["RF"][$acfpro_repfields->post_excerpt]['label'] = $acfpro_repfields->post_title;
							$customFields["RF"][$acfpro_repfields->post_excerpt]['name'] = $acfpro_repfields->post_excerpt;
						}
					}
					if(!empty($flexible_field_arr)) {
						$get_acf_flexible_content_fields = $wpdb->get_results("SELECT ID, post_title, post_content, post_excerpt FROM $wpdb->posts where post_parent in ($flexible_field_arr)");
					}
					if(!empty($get_acf_flexible_content_fields)) {
						foreach($get_acf_flexible_content_fields as $acfpro_fcfields) {
							$fc_customFields[$acfpro_fcfields->post_title] = $acfpro_fcfields->post_excerpt;
							$check_exist_key = "ACF: " . $acfpro_fcfields->post_title;
							if(array_key_exists($check_exist_key, $customFields)) {
								unset($customFields[$check_exist_key]);
							}
							$customFields["RF"][$acfpro_fcfields->post_excerpt]['label'] = $acfpro_fcfields->post_title;
							$customFields["RF"][$acfpro_fcfields->post_excerpt]['name'] = $acfpro_fcfields->post_excerpt;
						}
					}
					foreach($getCustomFieldsArr as $acf_custfields) {
						if(!in_array($acf_custfields, $rep_customFields)) {
							#						$customFields["ACF"][$acf_custfields] = $acf_custfields;
						}
						else {
							#						unset($customFields ["ACF: " . $acf_custfields]);
						}
					}
				}
				else {
					if (in_array('advanced-custom-fields/acf.php', $active_plugins)) {
						$get_acf_fields = $wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta
							GROUP BY meta_key
							HAVING meta_key LIKE 'field_%'
							ORDER BY meta_key");
						$acf_value = $get_acf_fields;
					}

					#print('<pre>'); print_r($get_acf_fields); die;
					if (is_array($acf_value) && !empty($acf_value)) {
						foreach ($acf_value as $value) {
							$get_acf_field = unserialize($value);
							$customFields["ACF"][$get_acf_field['name']]['label'] = $get_acf_field['label'];
							$customFields["ACF"][$get_acf_field['name']]['name'] = $get_acf_field['name'];
							$acf_field[] = $get_acf_field['name'];
						}
					}
					/*foreach ($keys as $val) {
					  if (!in_array($val, $acf_field)) {
					  $customFields ["CF: " . $val] = $val;
					  }
					  }*/
					// Get ACF repeater Fields
					$get_repeater_fields = $wpdb->get_col("SELECT ID FROM $wpdb->posts where post_name like 'acf_%' and post_type = 'acf'");
					foreach( $get_repeater_fields as $fieldGroupId ) {
						$get_field_details = $wpdb->get_col("select meta_value from $wpdb->postmeta where post_id = $fieldGroupId and meta_key like 'field_%'");
						foreach( $get_field_details as $repFieldDet ) {
							$repeaterFields = unserialize($repFieldDet);
							foreach( $repeaterFields as $fieldKey => $fieldVal ) {
								if($fieldKey == 'sub_fields') {
									for($a=0; $a<count($fieldVal); $a++) {
										$customFields['RF'][$fieldVal[$a]['name']]['label'] = $fieldVal[$a]['label'];
										$customFields['RF'][$fieldVal[$a]['name']]['name']  = $fieldVal[$a]['name'];
									}
								}
							}
						}
					}
					// ACF repeater fields code ends here
				}
			}
		#}
		return $customFields;
	}

	/**
	 * Function to get PODS custom fields
	 */
	function PODSCustomFields() {
		$active_plugins = get_option('active_plugins');
		global $wpdb;
		$podsFields = array();
		if (in_array('pods/init.php', $active_plugins)) {
			//$get_pods_fields = $wpdb->get_results("SELECT post_title, post_name FROM $wpdb->posts where post_type = '_pods_field'");
			$get_pods_fields = $wpdb->get_results($wpdb->prepare("SELECT post_title, post_name FROM $wpdb->posts where post_type = %s",'_pods_field'));
			foreach($get_pods_fields as $pods_field) {
				$podsFields["PODS"][$pods_field->post_name]['label'] = $pods_field->post_title; 
				$podsFields["PODS"][$pods_field->post_name]['name'] = $pods_field->post_name;
			}
		}
		return $podsFields;
	}

	/**
	 * Function to get Types custom fields
	 */
	function TypesCustomFields() {
		$active_plugins = get_option('active_plugins');
		$typesFields = array();
		if (in_array('types/wpcf.php', $active_plugins)) {
			if(isset($_REQUEST['import_type']) && sanitize_text_field($_REQUEST['import_type']) === 'users') {
                                $getUserMetas = get_option('wpcf-usermeta');
                                if(is_array($getUserMetas)) {
                                        foreach ($getUserMetas as $optKey => $optVal) {
                                                $typesFields["TYPES"][$optVal['slug']]['label'] = $optVal['name'];
                                                $typesFields["TYPES"][$optVal['slug']]['name'] = $optVal['slug'];
                                        }
                                }
                        }
			else {
				$getOptions = get_option('wpcf-fields');
				if(is_array($getOptions)) {
					foreach ($getOptions as $optKey => $optVal) {
						$typesFields["TYPES"][$optVal['slug']]['label'] = $optVal['name'];
						$typesFields["TYPES"][$optVal['slug']]['name'] = $optVal['slug'];
					}
				}
			}
			/*if(isset($_REQUEST['import_type']) && $_REQUEST['import_type'] == 'users') {
				$getUserMetas = get_option('wpcf-usermeta');
				if(is_array($getUserMetas)) {
					foreach ($getUserMetas as $optKey => $optVal) {
						$typesFields["TYPES"][$optVal['slug']]['label'] = $optVal['name'];
						$typesFields["TYPES"][$optVal['slug']]['name'] = $optVal['slug'];
					}
				}
			}
			*/
		}
		return $typesFields;
	}

	/**
	 * Function to get CCTM custom fields
	 */
	function CCTMCustomFields() {
		$active_plugins = get_option('active_plugins');
		$cctmFields = array();
		if (in_array('custom-content-type-manager/index.php', $active_plugins)) {
			$getOptions = get_option('cctm_data');
			$get_cctm_fields = $getOptions['custom_field_defs'];
			foreach ($get_cctm_fields as $optKey => $optVal) {
				$cctmFields["CCTM"][$optVal['name']]['label'] = $optVal['label']; 
				$cctmFields["CCTM"][$optVal['name']]['name'] = $optVal['name'];
			}
		}
		return $cctmFields;
	}

	/**
	 * Function to get All in One SEO fields
	 */
	function aioseoFields() {
		$active_plugins = get_option('active_plugins');
		$aioseoFields = array();
		if (in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins)) {
			$seoFields = array('Keywords' => 'keywords', 
					'Description' => 'description', 
					'Title' => 'title', 
					'NOINDEX' => 'noindex', 
					'NOFOLLOW' => 'nofollow', 
					'Title Atr' => 'titleatr', 
					'Menu Label' => 'menulabel', 
					'Disable' => 'disable', 
					'Disable Analytics' => 'disable_analytics', 
					'NOODP' => 'noodp', 
					'NOYDIR' => 'noydir'
					);
			foreach ($seoFields as $key => $val) {
				$aioseoFields['AIOSEO'][$val]['label'] = $key;
				$aioseoFields['AIOSEO'][$val]['name'] = $val;
			}
		}
		return $aioseoFields;
	}

	/**
	 * Function to get WordPress Yoast SEO fields
	 */
	function yoastseoFields() {
		$active_plugins = get_option('active_plugins');
		$yoastseoFields = array();
		if (in_array('wordpress-seo/wp-seo.php', $active_plugins)) {
			$seoFields = array('SEO Title' => 'title', 
					'Meta Description' => 'meta_desc', 
					'Meta Robots Index' => 'meta-robots-noindex', 
					'Meta Robots Follow' => 'meta-robots-nofollow', 
					'Meta Robots Advanced' => 'meta-robots-adv', 
					'Meta Keywords' => 'meta_keywords',
					'Include in Sitemap' => 'sitemap-include', 
					'Sitemap Priority' => 'sitemap-prio', 
					'Canonical URL' => 'canonical', 
					'301 Redirect' => 'redirect', 
					'Facebook Title' => 'opengraph-title', 
					'Facebook Description' => 'opengraph-description', 
					'Facebook Image' => 'opengraph-image', 
					'Twitter Title' => 'twitter-title', 
					'Twitter Description' => 'twitter-description', 
					'Twitter Image' => 'twitter-image', 
					'Google+ Title' => 'google-plus-title', 
					'Google+ Description' => 'google-plus-description', 
					'Google+ Image' => 'google-plus-image', 
					'Focus Keyword' => 'focus_keyword'
					);
			foreach ($seoFields as $key => $val) {
				$yoastseoFields['YOASTSEO'][$val]['label'] = $key;
				$yoastseoFields['YOASTSEO'][$val]['name'] = $val;
			}
		}
		return $yoastseoFields;
	}

	/**
	 * Function to get WP Members fields
	 */
	function wpmembersFields() {
		$active_plugins = get_option('active_plugins');
		$wpmemberFields = array();
		if (in_array('wp-members/wp-members.php', $active_plugins)) {
			$wpmembers_fields = get_option('wpmembers_fields');
			if (is_array($wpmembers_fields) && !empty($wpmembers_fields)) {
				foreach ($wpmembers_fields as $get_fields) {
					$wpmemberFields['WPMEMBERS'][$get_fields[2]]['label'] = $get_fields[1];
					$wpmemberFields['WPMEMBERS'][$get_fields[2]]['name'] = $get_fields[2];
				}
			}
		}

		return $wpmemberFields;
	}

	/**
	 * Function to get WP-eCommerce custom fields for WP-eCommerce add-on
	 */
	function wpecommerceCustomFields() {
		$active_plugins = get_option('active_plugins');
		$wpecomCustomFields = array();
		if(in_array('wp-e-commerce-custom-fields/custom-fields.php',$active_plugins)) {
		$get_wpecom_custom_fields = get_option('wpsc_cf_data');
		$wpecom_custom_fields = maybe_unserialize($get_wpecom_custom_fields);
		if(!empty($wpecom_custom_fields)) {
			foreach($wpecom_custom_fields as $key => $val) {
				$wpecomCustomFields['WPECOMMETA'][$val['slug']]['label'] = $val['name']; 
				$wpecomCustomFields['WPECOMMETA'][$val['slug']]['name'] = $val['slug'];
			}
		}
		}
		return $wpecomCustomFields;
	}

	/**
	 * Function to get all eCommerce Meta fields based on requested module
	 */
	function ecommerceMetaFields($module) {
		$ecommerceMetaFields = array();
		$MetaFields = array();
		$active_plugins = get_option('active_plugins');
		if($module === 'eshop') {
			if(in_array('eshop/eshop.php',$active_plugins)){
			$MetaFields = array('SKU' => 'sku', 
					'Product Options' => 'products_option', 
					'Sale Price' => 'sale_price', 
					'Regular Price' => 'regular_price', 
					'Description' => 'description', 
					'Shipping Rate' => 'shiprate', 
					'Featured Product' => 'featured_product', 
					'Product in sale' => 'product_in_sale', 
					'Stock Available' => 'stock_available', 
					'Show Options as' => 'cart_option'
					);
			}
		}
		if($module === 'wpcommerce') {
			if(in_array('wp-e-commerce/wp-shopping-cart.php',$active_plugins)){
			$MetaFields = array('Stock' => 'stock', 
					'Price' => 'price', 
					'Sale Price' => 'sale_price', 
					'SKU' => 'sku', 
					'Notify Stock Runs Out' => 'notify_when_none_left', 
					'UnPublish If Stock Runs' => 'unpublish_when_none_left', 
					'Taxable Amount' => 'taxable_amount', 
					'Is Taxable' => 'is_taxable', 
					'External Link' => 'external_link', 
					'External Link Text' => 'external_link_text', 
					'External Link Target' => 'external_link_target', 
					'No Shipping' => 'no_shipping', 
					'Weight' => 'weight', 
					'Weight Unit' => 'weight_unit', 
					'Height' => 'height', 
					'Height Unit' => 'height_unit', 
					'Width' => 'width', 
					'Width Unit' => 'width_unit', 
					'Length' => 'length', 
					'Length Unit'  => 'length_unit', 
					'Dimension Unit' => 'dimension_unit', 
					'Shipping' => 'shipping', 
					'Custom Name' => 'custom_name', 
					'Custom Description' => 'custom_desc', 
					'Merchant Notes' => 'merchant_notes', 
					'Enable Comments' => 'enable_comments', 
					'Quantity Limited' => 'quantity_limited', 
					'Special' => 'special', 
					'Display Weight As' => 'display_weight_as', 
					'State' => 'state', 
					'Quantity' => 'quantity', 
					'Table Price' => 'table_price', 
					'Google Prohibited' => 'google_prohibited'
						);
			}
		}
		if($module === 'woocommerce_products' || $module === 'woocommerce') {
			if(in_array('woocommerce/woocommerce.php',$active_plugins)){
			$MetaFields = array('Product Shipping Class' => 'product_shipping_class', 
					'Visibility' => 'visibility', 
					'Tax Status' => 'tax_status',
					'Product Type' => 'product_type',
					'Product Attribute Name' => 'product_attribute_name', 
					'Product Attribute Value' => 'product_attribute_value', 
					'Product Attribute Visible' => 'product_attribute_visible', 
					'Product Attribute Variation' => 'product_attribute_variation', 
					'Product Attribute Position' => 'product_attribute_position', 
					'Featured Product' => 'featured_product', 
					'Product Attribute Taxonomy' => 'product_attribute_taxonomy', 
					'Tax Class' => 'tax_class', 
					'File Paths' => 'file_paths', 
					'Edit Last' => 'edit_last', 
					'Edit Lock' => 'edit_lock',
					'Thumbnail Id' => 'thumbnail_id', 
//					'Visibility' => 'visibility', 
					'Stock Status' => 'stock_status', 
					'Stock Quantity' => 'stock_qty', 
					'Total Sales' => 'total_sales', 
					'Downloadable' => 'downloadable', 
					'Downloadable Files' => 'downloadable_files', 
					'Virtual' => 'virtual', 
					'Regular Price' => 'regular_price', 
					'Sale Price' => 'sale_price', 
					'Purchase Note' => 'purchase_note', 
					'Weight' => 'weight', 
					'Length' => 'length', 
					'Width' => 'width', 
					'Height' => 'height', 
					'SKU' => 'sku', 
					'UpSells Id' => 'upsell_ids', 
					'CrossSells Id' => 'crosssell_ids', 
					'Sales Price Date From' => 'sale_price_dates_from', 
					'Sales Price Date To' => 'sale_price_dates_to', 
					'Price' => 'price', 
					'Sold Individually' => 'sold_individually', 
					'Manage Stock' => 'manage_stock', 
					'Backorders' => 'backorders', 
					'Stock' => 'stock', 
					'Product Image Gallery' => 'product_image_gallery', 
					'Product URL' => 'product_url', 
					'Button Text' => 'button_text', 
					'Downloadable Files' => 'downloadable_files', 
					'Download Limit' => 'download_limit', 
					'Download Expiry' => 'download_expiry', 
					'Download Type' => 'download_type', 
					'Min Variation Price' => 'min_variation_price', 
					'Max Variation Price' => 'max_variation_price', 
					'Min Price Variation Id' => 'min_price_variation_id', 
					'Max Price Variation Id' => 'max_price_variation_id', 
					'Min Variation Regular Price' => 'min_variation_regular_price', 
					'Max Variation Regular Price' => 'max_variation_regular_price', 
					'Min Regular Price Variation Id' => 'min_regular_price_variation_id', 
					'Max Regular Price Variation Id' => 'max_regular_price_variation_id', 
					'Min Variation Sale Price' => 'min_variation_sale_price', 
					'Max Variation Sale Price' => 'max_variation_sale_price', 
					'Min Sale Price Variation Id' => 'min_sale_price_variation_id', 
					'Max Sale Price Variation Id' => 'max_sale_price_variation_id', 
					'Default Attributes' => 'default_attributes',
					);
					if(in_array('woocommerce-chained-products/woocommerce-chained-products.php',$active_plugins)){ 
						$chain_product = array(
						'Chained Product Detail' => 'chained_product_detail', 
						'Chained Product Manage Stock' => 'chained_product_manage_stock', 
						);
						foreach($chain_product as $key => $value){
							$MetaFields[$key] = $value;
						}
					}
					if(in_array('woocommerce-product-retailers/woocommerce-product-retailers.php', $active_plugins)){
						$retailers = array(
						'Retailers Only Purchase' => 'wc_product_retailers_retailer_only_purchase', 
						'Retailers Use Buttons' => 'wc_product_retailers_use_buttons', 
						'Retailers Product Button Text' => 'wc_product_retailers_product_button_text', 
						'Retailers Catalog Button Text' => 'wc_product_retailers_catalog_button_text', 
						'Retailers Id' => 'wc_product_retailers_id', 
						'Retailers Price' => 'wc_product_retailers_price', 
						'Retailers URL' => 'wc_product_retailers_url',
						);
						foreach($retailers as $key => $value){
							$MetaFields[$key] = $value;
						}
					}
					if(in_array('woocommerce-product-addons/product-addons.php',$active_plugins)){ 
						$product_Addons = array(
						'Product Addons Exclude Global' => 'product_addons_exclude_global', 
						'Product Addons Group Name' => 'product_addons_group_name', 
						'Product Addons Group Description' => 'product_addons_group_description', 
						'Product Addons Type' => 'product_addons_type', 
						'Product Addons Position' => 'product_addons_position', 
						'Product Addons Required' => 'product_addons_required', 
						'Product Addons Label Name' => 'product_addons_label_name', 
						'Product Addons Price' => 'product_addons_price', 
						'Product Addons Minimum' => 'product_addons_minimum', 
						'Product Addons Maximum' => 'product_addons_maximum', 
						);
						foreach($product_Addons as $key => $value){
							$MetaFields[$key] = $value;
						}
					}
					if(in_array('woocommerce-warranty/woocommerce-warranty.php', $active_plugins)){
						$warranty = array(
						'Warranty Label' => 'warranty_label', 
						'Warranty Type' => 'warranty_type', 
						'Warranty Length' => 'warranty_length', 
						'Warranty Value' => 'warranty_value', 
						'Warranty Duration' => 'warranty_duration', 
						'Warranty Addons Amount' => 'warranty_addons_amount', 
						'Warranty Addons Value' => 'warranty_addons_value', 
						'Warranty Addons Duration' => 'warranty_addons_duration', 
						'No Warranty Option' => 'no_warranty_option',
						);
						foreach($warranty as $key => $value){
							$MetaFields[$key] = $value;
						}
					}
					if(in_array('woocommerce-pre-orders/woocommerce-pre-orders.php', $active_plugins)){
						$pre_orders = array(
						'Pre-Orders Enabled' => 'preorders_enabled', 
						'Pre-Orders Fee' => 'preorders_fee', 
						'Pre-Orders When to Charge' => 'preorders_when_to_charge', 
						'Pre-Orders Availabilty Datetime' => 'preorders_availability_datetime'  
						);
						foreach($pre_orders as $key => $value){
							$MetaFields[$key] = $value;
						}
					}
						
			}
		}
		if($module === 'woocommerce_variations'){
			if(in_array('woocommerce/woocommerce.php',$active_plugins)){
                        $MetaFields = array('Product Type' => 'product_type',
                                        'Product Attribute Name' => 'product_attribute_name',
                                        'Product Attribute Value' => 'product_attribute_value',
                                        'Product Attribute Visible' => 'product_attribute_visible',
                                        'Product Attribute Variation' => 'product_attribute_variation',
                                        'Product Attribute Position' => 'product_attribute_position',
                                        'Featured' => 'featured',
                                        'Downloadable Files' => 'downloadable_files',
                                        'Download Limit' => 'download_limit',
                                        'Download Expiry' => 'download_expiry',
					'Price' => 'price',
                                        'Sales Price Date From' => 'sale_price_dates_from',
                                        'Sales Price Date To' => 'sale_price_dates_to',
                                        'Regular Price' => 'regular_price',
                                        'Sale Price' => 'sale_price',
                                        'Purchase Note' => 'purchase_note',
                                        'Default Attributes' => 'default_attributes',
					'Custom Attributes' => 'custom_attributes',
                                        'UpSells Id' => 'upsell_ids',
                                        'CrossSells Id' => 'crosssell_ids',
                                        'Weight' => 'weight',
                                        'Length' => 'length',
                                        'Width' => 'width',
                                        'Height' => 'height',
                                        'Downloadable' => 'downloadable',
                                        'Virtual' => 'virtual',
                                        'Stock' => 'stock',
                                        'Stock Status' => 'stock_status',
                                        'Stock Quantity' => 'stock_qty',
                                        'Sold Individually' => 'sold_individually',
                                        'Manage Stock' => 'manage_stock',
                                        'Backorders' => 'backorders',
                                        'SKU' => 'sku',
                                        'Thumbnail Id' => 'thumbnail_id',
                                        'Visibility' => 'visibility',
                                        'Edit Last' => 'edit_last',
                                        'Edit Lock' => 'edit_lock');
			}
		}
		if($module === 'woocommerce_coupons'){
			if(in_array('woocommerce/woocommerce.php',$active_plugins)){
                        $MetaFields = array('Discount Type' => 'discount_type',
                                        'Coupon Amount' => 'coupon_amount',
                                        'Individual Use' => 'individual_use',
                                        'Product Ids' => 'product_ids',
                                        'Exclude Product Ids' => 'exclude_product_ids',
                                        'Usage Limit' => 'usage_limit',
                                        'Usage Limit Per User' => 'usage_limit_per_user',
                                        'Limit Usage' => 'limit_usage_to_x_items',
                                        'Expiry Date' => 'expiry_date',
                                        'Free Shipping' => 'free_shipping',
                                        'Exclude Sale Items' => 'exclude_sale_items',
                                        'Product Categories' => 'product_categories',
                                        'Exclude Product Categories' => 'exclude_product_categories',
                                        'Minimum Amount' => 'minimum_amount',
                                        'Maximum Amount' => 'maximum_amount',
                                        'Customer Email' => 'customer_email');
			}
                }
		if($module === 'woocommerce_orders'){
				if(in_array('woocommerce/woocommerce.php',$active_plugins)){
                                $MetaFields = array('Recorded Sales' => 'recorded_sales',
                                'Payment Method Title' => 'payment_method_title',
                                'Payment Method' => 'payment_method',
                                'Transaction Id' => 'transaction_id',
                                'Billing First Name' => 'billing_first_name',
                                'Billing Last Name' => 'billing_last_name',
                                'Billing Company' => 'billing_company',
                                'Billing Address1' => 'billing_address_1',
                                'Billing Address2' => 'billing_address_2',
                                'Billing City' => 'billing_city',
                                'Billing PostCode' => 'billing_postcode',
                                'Billing State' => 'billing_state',
                                'Billing Country' => 'billing_country',
                                'Billing Phone' => 'billing_phone',
                                'Billing Email' => 'billing_email',
                                'Shipping First Name' => 'shipping_first_name',
                                'Shipping Last Name' => 'shipping_last_name',
                                'Shipping Company' => 'shipping_company',
                                'Shipping Address1' => 'shipping_address_1',
                                'Shipping Address2' => 'shipping_address_2',
                                'Shipping City' => 'shipping_city',
                                'Shipping PostCode' => 'shipping_postcode',
                                'Shipping State' => 'shipping_state',
                                'Shipping Country' => 'shipping_country',
                                'Customer User' =>'customer_user',
                                'Order Key' => 'order_key',
                                'Order Currency' => 'order_currency',
                                'Order Shipping Tax' => 'order_shipping_tax',
                                'Order Tax' => 'order_tax',
                                'Order Total' => 'order_total',
                                'Cart Discount Tax' => 'cart_discount_tax',
                                'Cart Discount' => 'cart_discount',
                                'Order Shipping' => 'order_shipping',
				'ITEM: name' => 'item_name',
                                'ITEM: type' => 'item_type',
                                'ITEM: variation_id' => 'item_variation_id',
				'ITEM: product_id' => 'item_product_id',
                                'ITEM: line_subtotal' => 'item_line_subtotal',
                                'ITEM: line_subtotal_tax' => 'item_line_subtotal_tax',
                                'ITEM: line_total' => 'item_line_total',
                                'ITEM: line_tax' => 'item_line_tax',
                                'ITEM: line_tax_data' => 'item_line_tax_data',
                                'ITEM: tax_class' => 'item_tax_class',
                                'ITEM: qty' => 'item_qty',
                                'FEE: name' => 'fee_name',
                                'FEE: type' => 'fee_type',
                                'FEE: tax_class' => 'fee_tax_class',
                                'FEE: line_total' => 'fee_line_total',
                                'FEE: line_tax' => 'fee_line_tax',
                                'FEE: line_tax_data' => 'fee_line_tax_data',
                                'FEE: line_subtotal' => 'fee_line_subtotal',
                                'FEE: line_subtotal_tax' => 'fee_line_subtotal_tax',
                                'SHIPMENT: name' => 'shipment_name',
                                'SHIPMENT: method_id' => 'shipment_method_id',
                                'SHIPMENT: cost' => 'shipment_cost',
                                'SHIPMENT: taxes' => 'shipment_taxes',);
			}
		}
		if($module === 'woocommerce_refunds'){
			if(in_array('woocommerce/woocommerce.php',$active_plugins)){
                        $MetaFields = array('Recorded Sales' => 'recorded_sales',
                                        'Refund Amount' => 'refund_amount',
                                        'Order Shipping Tax' => 'order_shipping_tax',
                                        'Order Tax' => 'order_tax',
                                        'Order Shipping' => 'order_shipping',
                                        'Cart Discount' => 'cart_discount',
                                        'Cart Discount Tax' => 'cart_discount_tax',
                                        'Order Total' => 'order_total',
                                        'Customer User' =>'customer_user');
			}
                }
		if($module === 'marketpress') {
			if(in_array('wordpress-ecommerce/marketpress.php',$active_plugins)){
			$MetaFields = array('Variation' => 'variation', 
					'SKU' => 'SKU', 
					'Regular Price' => 'regular_price', 
					'Is Sale' => 'is_sale', 
					'Sale Price' => 'sale_price', 
					'Track Inventory' => 'track_inventory', 
					'Inventory' => 'inventory', 
					'Track Limit' => 'track_limit', 
					'Limit Per Order' => 'limit_per_order', 
					'Product Link' => 'product_link', 
					'Is Special Tax' => 'is_special_tax', 
					'Special Tax' => 'special_tax', 
					'Sales Count' => 'sales_count', 
					'Extra Shipping Cost' => 'extra_shipping_cost', 
					'File URL' => 'file_url'
					
					);
			}
			if(in_array('marketpress/marketpress.php',$active_plugins)){
			$MetaFields = array( 'Product Type' => 'product_type',
				     	 'SKU' => 'sku',
					 'Thumbnail Id' => 'thumbnail_id',
				     	 'Sort Price' => 'sort_price',
				     	 'Regular Price' => 'regular_price',
				     	 'Per Order Limit' => 'per_order_limit',
				     	 'Has Sale' => 'has_sale',
					 'Sale Price Start Date' => 'sale_price_start_date',
					 'Sale Price End Date' => 'sale_price_start_date',
					 'File URL' => 'file_url',
                                         'External URL' => 'external_url',
					 'Special Tax Rate' => 'special_tax_rate',
					 'Charge Tax' => 'charge_tax',
					 'Charge Shipping' => 'charge_shipping',
					 'Weight Pounds' => 'weight_pounds',
					 'Weight Ounces' => 'weight_ounces',
					 'Weight Extra Shipping Cost' => 'weight_extra_shipping_cost',
					 'Inventory Tracking' => 'inventory_tracking',
					 'Inv Inventory' => 'inv_inventory',
					 'Inv Out_Of Stock Purchase' => 'inv_out_of_stock_purchase',
					 'Related Products' => 'related_products',
					 'Product Images' => 'mp_product_images',
					 'Has Variation' => 'has_variation',
					 'Variation Name' => 'mp_variation_name',
					 'Variation Value' => 'mp_variation_value',
					 'Has Variation Content' => 'has_variation_content',
					 'Variation Content Type' => 'variation_content_type',
					 'Variation Content Desc' => 'variation_content_desc',
					
				);
	
			}
		}
		if($module === 'eshop' || $module === 'wpcommerce' || $module === 'woocommerce_products' || $module === 'woocommerce_variations' || $module === 'woocommerce_coupons' || $module === 'woocommerce_orders' || $module === 'marketpress' || $module === 'woocommerce' ) {
			if(!empty($MetaFields)){ 
			foreach($MetaFields as $key => $val) {
				$ecommerceMetaFields['ECOMMETA'][$val]['label'] = $key;
				$ecommerceMetaFields['ECOMMETA'][$val]['name'] = $val;
			}
			}
		}

		return $ecommerceMetaFields;
	}

	/**
	 * Function to get Common custom fields based on requested module
	 */
	function commonMetaFields() {
		global $wpdb;
		$commonMetaFields = $podsCustomFields = $typesCustomFields = $cctmCustomFields = $acfCustomFields = $acfentries = $ecommerceMetaentries = array();
		$keys = $wpdb->get_col("SELECT meta_key FROM $wpdb->postmeta
				GROUP BY meta_key
				HAVING meta_key NOT LIKE '\_%' and meta_key NOT LIKE 'field_%' and meta_key NOT LIKE 'wpcf-%'
				ORDER BY meta_key");
		foreach ($this->PODSCustomFields() as $pods_fieldname_arr) {
			foreach ($pods_fieldname_arr as $key => $val) {
				$podsCustomFields[] = $key;
			}
		}		
		foreach ($this->CCTMCustomFields() as $cctm_fieldname_arr) {
			foreach ($cctm_fieldname_arr as $key => $val) {
				$cctmCustomFields[] = $key;
			}
		}
		foreach ($this->TypesCustomFields() as $types_fieldname_arr) {
			foreach ($types_fieldname_arr as $key => $val) {
				$typesCustomFields[] = $key;
			}
		}
		foreach ($this->ACFCustomFields() as $acf_fieldname_arr) {
			foreach ($acf_fieldname_arr as $key => $val) {
				$acfCustomFields[] = $key;
			}
		}
		$ecomModules = array('eshop', 'woocommerce_products',  'woocommerce_variations','woocommerce_coupons', 'wpcommerce', 'marketpress');
		foreach ($ecomModules as $em) {
			foreach ($this->ecommerceMetaFields($em) as $key => $val) {
				foreach($val as $k => $v) {
					$ecommerceMetaentries[$k] = $k;
				}
			}
			foreach ($this->WPCoreFields($em) as $key => $val) {
                                foreach($val as $k => $v) {
                                        $ecommerceMetaentries[$k] = $k;
                                }
			}
		}
		foreach($keys as $k) {
			foreach($acfCustomFields as $fkey) {
				if(strstr($fkey, $k))
					$acfentries[$k] = $k;
			}
		}
		foreach ($keys as $val) {
			if(!in_array($val, $podsCustomFields) && !in_array($val, $cctmCustomFields) && !in_array($val, $typesCustomFields) && !in_array($val, $acfentries) && !in_array($val, $acfCustomFields) && !in_array($val, $ecommerceMetaentries)) {
				$commonMetaFields['CORECUSTFIELDS'][$val]['label'] = $val;
				$commonMetaFields['CORECUSTFIELDS'][$val]['name'] = $val;
			}
		}

		return $commonMetaFields;
	}

	/**
	 * Function to get Terms & Taxonomies based on requested module
	 */
	function termsandtaxos($module) {
		$termtaxos = array();
		if($module === 'eshop' || $module === 'post' || $module === 'custompost') {
			$termtaxos['TERMS']['post_category']['label'] = 'Categories';
			$termtaxos['TERMS']['post_category']['name'] = 'post_category';
			$termtaxos['TERMS']['post_tag']['label'] = 'Tags';
			$termtaxos['TERMS']['post_tag']['name'] = 'post_tag';
			if($module === 'custompost'){
				$termtaxos['TERMS']['event_category']['label'] = 'Event Categories';
				$termtaxos['TERMS']['event_category']['name'] = 'event_category';
				$termtaxos['TERMS']['event_tag']['label'] = 'Event Tags';
				$termtaxos['TERMS']['event_tag']['name'] = 'event_tag';
			}
		}
		if($module === 'woocommerce_products' || $module === 'woocommerce_variations' || $module ==='woocommerce_coupons' || $module === 'woocommerce_orders'|| $module === 'woocommerce_refunds') {
			$termtaxos['TERMS']['product_category']['label'] = 'Categories';
			$termtaxos['TERMS']['product_category']['name'] = 'product_category';
			$termtaxos['TERMS']['product_tag']['label'] = 'Tags';
			$termtaxos['TERMS']['product_tag']['name'] = 'product_tag';
		}
		if($module === 'wpcommerce') {
			$termtaxos['TERMS']['product_category']['label'] = 'Categories';
			$termtaxos['TERMS']['product_category']['name'] = 'product_category';
			$termtaxos['TERMS']['product_tag']['label'] = 'Tags';
			$termtaxos['TERMS']['product_tag']['name'] = 'product_tag';
		}
		if($module === 'marketpress') {
			$termtaxos['TERMS']['product_category']['label'] = 'Categories';
			$termtaxos['TERMS']['product_category']['name'] = 'product_category';
			$termtaxos['TERMS']['product_tag']['label'] = 'Tags';
			$termtaxos['TERMS']['product_tag']['name'] = 'product_tag';
		}

		$taxo = get_taxonomies();
		foreach ($taxo as $taxokey => $taxovalue) {
			if ($taxokey != 'category' && $taxokey != 'link_category' && $taxokey != 'post_tag' && $taxokey != 'nav_menu' && $taxokey != 'post_format' && $taxokey != 'product_type' && $taxokey != 'event-tags' && $taxokey != 'event-categories') {
				if (!array_key_exists($taxokey, $termtaxos)) {
					$get_taxo_label = get_taxonomy( $taxokey );
					$taxo_label = $get_taxo_label->labels->singular_name;
					$termtaxos['TERMS'][$taxokey]['label'] = $taxo_label;
					$termtaxos['TERMS'][$taxokey]['name'] = $taxokey; 
				}
			}
		} 

		return $termtaxos;
	}

	/**
	 * Function to get all taxonomies
	 */
	function getallTaxonomies() {
		$termtaxos = array();
		$taxo = get_taxonomies();
		foreach ($taxo as $taxokey => $taxovalue) {
			if ($taxokey != 'category' && $taxokey != 'link_category' && $taxokey != 'post_tag' && $taxokey != 'nav_menu' && $taxokey != 'post_format') {
				if (!array_key_exists($taxokey, $termtaxos)) {
					$get_taxo_label = get_taxonomy( $taxokey );
					$taxo_label = $get_taxo_label->labels->singular_name;
					$termtaxos['TAXO'][$taxokey]['label'] = $taxo_label;
					$termtaxos['TAXO'][$taxokey]['name'] = $taxokey;
				}
			}
		}
		return $termtaxos;
	}

	/**
	 *
	 */
	public function getCPTPosts () {
		// Get CPT UI
		$cust_post_ui = $cpt_post_arr = array();
		$cust_post_ui = get_option('cpt_custom_post_types');
		if(!empty ($cust_post_ui)) {
			foreach ($cust_post_ui as $cust_list) {
				$cpt_post_arr[] = $cust_list['name'];
			}
		}
		return $cpt_post_arr;
	}

	/**
	 *
	 */
	public function getCCTMPosts () {
		// Get CCTM post type
		$cctm_post_arr = array();
		$cctm_post_type = get_option('cctm_data');
		if (!empty($cctm_post_type)) {
			foreach ($cctm_post_type['post_type_defs'] as $cctmptkey => $cctmptval) {
				if(array_key_exists('post_type', $cctmptval)) {
					$cctm_post_arr[] = $cctmptkey;
				}
			}
		}
		return $cctm_post_arr;
	}

	/**
	 *
	 */
	public function getPODSPosts () {
		global $wpdb;
		// Get PODS post type
		$pods_post_arr = array();
                $pods_taxon = array();
                $i =0;
                //$pods_ptid = $wpdb->get_col("select post_id from $wpdb->postmeta where meta_key = 'type' and meta_value = 'taxonomy'");
		$pods_ptid = $wpdb->get_col($wpdb->prepare("select post_id from $wpdb->postmeta where meta_key = %s and meta_value = %s",'type','taxonomy'));
                foreach($pods_ptid as $val)
                {
                //$pods_taxon[$i] = $wpdb->get_col("select post_name from $wpdb->posts where post_type ='_pods_pod' and id=$val");
		$pods_taxon[$i] = $wpdb->get_col($wpdb->prepare("select post_name from $wpdb->posts where post_type = %s and id = %d",'_pods_pod',$val));
                $i++;
                }
                $j = 0;
                foreach($pods_taxon as $key=>$val){
                        foreach($val as $key=>$data){
                                $podstax_val[$j] = $data;
                                $j++;
                        }
                }
		//$get_all_pods_post = $wpdb->get_col("select post_name from $wpdb->posts where post_type ='_pods_pod'");
		$get_all_pods_post = $wpdb->get_col($wpdb->prepare("select post_name from $wpdb->posts where post_type = %s",'_pods_pod'));
		foreach($get_all_pods_post as $podspost) {
                        if(!empty($podstax_val) && !in_array($podspost,$podstax_val))
			$pods_post_arr[] = $podspost;
		}
		return $pods_post_arr;
	}

	/**
	 *
	 */
	public function getTypesPosts () {
		// Get Types post type
		$types_post_arr = array();
		$types_post_type = get_option('wpcf-custom-types');
		if (!empty($types_post_type)) {
			foreach($types_post_type as $tpt_key => $tpt_val) {
				$types_post_arr[] = $tpt_key;
			}
		}
		return $types_post_arr;
	}

	public function getallTerms() {
		global $wpdb;
                $options = "<select name='wptermslist' id='wptermslist' style='margin-top:11px;margin-left:10px';>
                        <option id='select'>---Select---</option>
			<option value='Tags'>Tags</option>
			<option value='Category'>Category</option>
			</select>";
		return $options;
	}

	public function getallCustomTaxonomies() {
		$options = "<select name='wptaxolist' id='wptaxolist' style='margin-top:11px;margin-left:10px';>
			<option id='select'>---Select---</option>";
		if($this->getallTaxonomies()) {
			foreach($this->getallTaxonomies() as $taxoKey => $taxoArr) {
				foreach($taxoArr as $taxoname => $taxovalue) {
					$options .= "<option value='".$taxoname."'>$taxoname</option>";
				}
			}
		}
		$options .= "</select>";
		return $options;
	}

	/**
	 * Function to get all custom post types
	 */
	public function getallCustomPosts() {
		global $wpdb;
		$options = "<select name='custompostlist' id='custompostlist' style='margin-top:11px;margin-left:10px';>
			<option id='select'>---Select---</option>";
		$cust_post_list_count = 0;
		$allPostTypes = array();
		$pods_post_arr = array();
		$cctm_post_arr = array();
		$cpt_post_arr = array();
		$pods_others = array('post','page','user','comment','wpsc-product','product','product-variation','shop_order','shop_order_refund','shop_coupon','wpsc_product_file','mp_order');
		$active_plugins = get_option('active_plugins');

		//echo '<pre>'; print_r($this->wpcsvsettings); echo '</pre>';
		foreach (get_post_types() as $key => $value) {
			#if(isset($this->wpcsvsettings['cptuicustompost']) &&( $this->wpcsvsettings['cptuicustompost'] == 'enable') && !in_array($value, $allPostTypes)) {
			if(in_array('custom-post-type-ui/custom-post-type-ui.php',$active_plugins)){
				foreach($this->getCPTPosts() as $cptKey) {
					$allPostTypes[$cptKey] = $cptKey;
				}
			}
			#} if(isset($this->wpcsvsettings['cctmcustompost']) &&( $this->wpcsvsettings['cctmcustompost'] == 'enable') && !in_array($value, $allPostTypes)) {
			if(in_array('custom-content-type-manager/index.php',$active_plugins)){
				foreach($this->getCCTMPosts() as $cctmKey) {
					$allPostTypes[$cctmKey] = $cctmKey;
				}
			}
			#} if(isset($this->wpcsvsettings['podscustompost']) &&( $this->wpcsvsettings['podscustompost'] == 'enable')&& !in_array($value, $allPostTypes)) {
			if(in_array('pods/init.php',$active_plugins)){
				foreach($this->getPODSPosts() as $podsKey) {
					if(!in_array($podsKey,$pods_others))
						$allPostTypes[$podsKey] = $podsKey;
				}
			}
			#} if(isset($this->wpcsvsettings['typescustompost']) &&( $this->wpcsvsettings['typescustompost'] == 'enable')&& !in_array($value, $allPostTypes)) {
			if(in_array('types/wpcf.php',$active_plugins)){
				foreach($this->getTypesPosts() as $typesKey) {
					$allPostTypes[$typesKey] = $typesKey;
				}
			}
			#}
			if(!in_array($value, $this->getCPTPosts()) && !in_array($value, $this->getCCTMPosts()) &&  !in_array($value, $this->getPODSPosts()) && !in_array($value, $this->getTypesPosts()) && ($value != 'featured_image') && ($value != 'attachment') && ($value != 'wpsc-product') && ($value != 'wpsc-product-file') && ($value != 'revision') && ($value != 'nav_menu_item') && ($value != 'post') && ($value != 'page') && ($value != 'wp-types-group') && ($value != 'wp-types-user-group') && ($value != 'product') && ($value != 'product_variation') && ($value != 'shop_order') && ($value != 'shop_coupon') && ($value != 'acf') && ($value != 'acf-field') && ($value != 'acf-field-group') && ($value != '_pods_pod') && ($value != '_pods_field') && ($value != 'shop_order_refund') && ($value != 'shop_webhook') && !in_array($value, $allPostTypes)) {
				$allPostTypes[$value] = $value;
			}
		}
		foreach($allPostTypes as $posttypes) {
			$options .= "<option id=" . $posttypes . ">" . $posttypes . "</option>";
			$cust_post_list_count++;
		}
		$options .= "</select> ";
		return $options;
	}

	/**
	 *
	 */
	function get_availgroups($module) {
		$groups = array();
		if($module === 'post') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'CORECUSTFIELDS', 'TERMS', 'TAXO');
		}
                if($module === 'page') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'CORECUSTFIELDS', 'TERMS', 'TAXO');
                }
                if($module === 'custompost') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'CORECUSTFIELDS', 'TERMS', 'TAXO');
                }
                if($module === 'users') {
			$groups = array('CORE', 'ACF', 'RF', 'WPMEMBERS', 'TYPES');
                }
                if($module === 'customtaxonomy') {
			$groups = array('CORE', 'ACF');
                }
                if($module === 'customerreviews') {
			$groups = array('CORE');
                }
                if($module === 'comments') {
			$groups = array('CORE');
                }
                if($module === 'eshop') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'ECOMMETA', 'CORECUSTFIELDS', 'TERMS', 'TAXO');
                }
                if($module === 'wpcommerce') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'ECOMMETA', 'CORECUSTFIELDS', 'WPECOMMETA', 'TERMS', 'TAXO');
                }
                if($module === 'woocommerce' || $module === 'woocommerce_products' || $module === 'woocommerce_variations' || $module === 'woocommerce_coupons' || $module === 'woocommerce_orders' || $module === 'woocommerce_refunds') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'ECOMMETA', 'CORECUSTFIELDS', 'TERMS', 'TAXO');
                }
                if($module === 'marketpress') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'ECOMMETA', 'CORECUSTFIELDS', 'TERMS', 'TAXO');
                }
                if($module === 'categories') {
			$groups = array('CORE');
                }
		return $groups;
	}
}
