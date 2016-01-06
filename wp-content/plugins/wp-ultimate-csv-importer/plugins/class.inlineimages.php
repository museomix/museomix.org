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
class WPImporter_inlineImages {

	/**
	 * Function for update with inline images data
	 * @param $postID
	 * @param $currentLimit
	 * @param $data_array
	 * @param $impObj
	 * @param $import_image_method
	 * @param $imgLoc
	 * @param $sampleURL
	 * @return int|WP_Error
	 */
	function importwithInlineImages($postID, $currentLimit, $data_array, $impObj, $import_image_method, $imgLoc, $sampleURL) {
		$helperObj = new WPImporter_includes_helper();
		$updatearray = array();
		$res_array = $this->capture_all_shortcodes($data_array['post_content'], $postID);
		if (isset($res_array['post_content'])) {
			$updatearray['post_content'] = $res_array['post_content'];
			$updatearray['ID'] = $postID;
		} else {
			$updatearray['ID'] = $postID;
		}
		$post_id = wp_update_post($updatearray);
		$impObj->insPostCount++;
		$res_array['inlineimage_shortcode'] = isset($res_array['inlineimage_shortcode']) ? $res_array['inlineimage_shortcode'] : '';
		$impObj->detailedLog[$currentLimit]['post_id'] = "<b>Created Post_ID - </b>" . $post_id . " - success , <b>Inline_images_shortcodes - </b>" . $res_array['inlineimage_shortcode'] . "";
		return $post_id;
	}

	/**
	 * Function to process the multi image if the image url given in post content
	 * @param $data_array
	 * @param $helperObj
	 * @param $currentLimit
	 * @param $impObj
	 * @param $import_image_method
	 * @param $imgLoc
	 * @param $sampleURL
	 * @return mixed
	 */
	public function process_multi_images($data_array, $helperObj, $currentLimit, $impObj, $import_image_method, $imgLoc, $sampleURL) {
		$content = $data_array['post_content'];
		$doc = new DOMDocument();
		$doc->loadHTML($content);
		$res = $this->getHtmlChar($doc, 'img');
		foreach ($res as $key => $image_url) {
			foreach ($image_url as $img_path) {
				$get_name = explode('/', $img_path);
				$count = count($get_name);
				$img_real_name = $get_name[$count - 1];
				$inline_img_slug = preg_replace("/\.[^.]*$/", "", $data_array['post_title']);
				$inline_img_slug = preg_replace(' ', '-', $inline_img_slug);
				$post_slug_value = strtolower($inline_img_slug);
				$dir = wp_upload_dir();
				$inlineimageDir = $dir['basedir'] . '/smack_inline_images';
				$inlineimageURL = $dir['baseurl'] . '/smack_inline_images';
				$media_location = $dir ['baseurl'];
				$get_media_settings = get_option('uploads_use_yearmonth_folders');
				if ($get_media_settings == 1) {
					$dirname = date('Y') . '/' . date('m');
					$full_path = $dir ['basedir'] . '/' . $dirname;
					$baseurl = $dir ['baseurl'] . '/' . $dirname;
				} else {
					$full_path = $dir ['basedir'];
					$baseurl = $dir ['baseurl'];
				}
				$eventKey = $_POST['postdata']['uploadedFile'];

				$inlineimageDirpath = $inlineimageDir . '/' . $eventKey;
				$imagelist = scanDirectories($inlineimageDirpath);
				if (!$imagelist) {
					echo 'Images not available!';
					die;
				}
				foreach ($imagelist as $imgwithloc) {
					if (strpos($imgwithloc, $img_real_name)) {
						$currentLoc = $imgwithloc;
					}
				}

				$exploded_currentLoc = explode("$eventKey", $currentLoc);
				if (!empty($exploded_currentLoc)) {
					$inlimg_curr_loc = $exploded_currentLoc[1];
				}

				$inlineimageURL = $inlineimageURL . '/' . $eventKey . $inlimg_curr_loc;

				$inline_img_path = $full_path;
				if ($import_image_method == 'imagewithextension' && $count == 1) {
					#					$new_img_path = $imgLoc . '/' . $img_real_name;
					$inline = $helperObj->get_fimg_from_URL($inlineimageURL, $inline_img_path, $img_real_name, $post_slug_value, $currentLimit, $impObj);
				} else {
					if ($sampleURL == null) {
						$inline = $helperObj->get_fimg_from_URL($img_path, $inline_img_path, $img_real_name, $post_slug_value, $currentLimit, $impObj);
					} else {
						#						$new_img_path = $sampleURL . '/' . $img_real_name;
						$inline = $helperObj->get_fimg_from_URL($inlineimageURL, $inline_img_path, $img_real_name, $post_slug_value, $currentLimit, $impObj);
					}
				}
				$inline_filepath = $inline_img_path . "/" . $img_real_name;
				if (@getimagesize($inline_filepath)) {
					$img = wp_get_image_editor($inline_filepath);
					if (!is_wp_error($img)) {
						$sizes_array = array(// #1 - resizes to 1024x768 pixel, square-cropped image
							array('width' => 1024, 'height' => 768, 'crop' => true), // #2 - resizes to 100px max width/height, non-cropped image
							array('width' => 100, 'height' => 100, 'crop' => false), // #3 - resizes to 100 pixel max height, non-cropped image
							array('width' => 300, 'height' => 100, 'crop' => false), // #3 - resizes to 624x468 pixel max width, non-cropped image
							array('width' => 624, 'height' => 468, 'crop' => false));
						$resize = $img->multi_resize($sizes_array);
					}
					$inline_file ['guid'] = $baseurl . "/" . $img_real_name;
					$inline_file ['post_title'] = $inline;
					$inline_file ['post_content'] = '';
					$inline_file ['post_status'] = 'attachment';
					$wp_upload_dir = wp_upload_dir();
					$attachment = array('guid' => $inline_file ['guid'], 'post_mime_type' => 'image/jpg', 'post_title' => preg_replace('/\.[^.]*$/', '', @basename($inline_file ['guid'])), 'post_content' => '', 'post_status' => 'inherit');
					if ($get_media_settings == 1) {
						$generate_attachment = $dirname . '/' . $inline;
					} else {
						$generate_attachment = $inline;
					}
					$uploadedImage = $wp_upload_dir['path'] . '/' . $inline;
					$attach_id = wp_insert_attachment($attachment, $generate_attachment, $post_id);
					$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
					wp_update_attachment_metadata($attach_id, $attach_data);
					set_post_thumbnail($post_id, $attach_id);
					$oldWord = $img_path;
					$newWord = $inline_file['guid'];
					$content = str_replace($oldWord, $newWord, $content);
				} else {
					$inline_file = false;
				}
			}
		}
		return $content;
	}

	/**
	 * Function to get HTML chars
	 * @param $dom_document
	 * @param $tagname
	 * @return array
	 */
	public function getHtmlChar($dom_document, $tagname) {
		$tagcontent = array();
		$dom_xpath = new DOMXpath($dom_document);
		$elements = $dom_document->getElementsByTagName($tagname);
		if (!is_null($elements)) {
			$i = 0;
			foreach ($elements as $element) {
				if ($tagname == 'img') {
					$nodes = $element->attributes;
				} else {
					$nodes = $element->childNodes;
				}

				foreach ($nodes as $node) {
					$nodevalue = trim($node->nodeValue);
					if ($tagname == 'img') {
						$nodename = trim($node->nodeName);
						if (isset($nodename) && !empty($nodename) && $nodename != NULL && $nodename == 'src') {
							$tagcontent[$tagname][$i] = $nodevalue;
							$i++;
						}
					} else {
						if (isset($nodevalue) && !empty($nodevalue) && $nodevalue != NULL) {
							$tagcontent[$tagname][$i] = $nodevalue;
							{
								$tagcontent[$tagname][$i] = $nodevalue;
								$i++;
							}
						}
					}
				}
			}
			return $tagcontent;
		}
	}

	/**
	 * @param $post_content
	 * @param $postID
	 * @return array
	 */
	public function capture_all_shortcodes($post_content, $postID) {
		$result = array();
		$pattern = "/([WPIMPINLINE:([\w]+)(.*?)(])/";
		$shortcode_prefix = "[WPIMPINLINE:";
		$post_content = str_replace("\n", "<br />", $post_content);
		preg_match_all($pattern, $post_content, $results, PREG_PATTERN_ORDER);
		$inlineimg_shortcodes = array();
		$inline_shortcode_count = 0;
		for ($i = 0; $i < count($results[0]); $i++) {
			$get_shortcode_pos = strpos($results[0][$i], $shortcode_prefix);
			$inlineimg_shortcodes[] = substr($results[0][$i], $get_shortcode_pos);
		}
		$inline_shortcode_count = count($inlineimg_shortcodes);
		foreach ($inlineimg_shortcodes as $shortkey => $shortcode) {

			$get_inlineimage_val = substr($shortcode, "13", -1);
			$image_attribute = explode('|', $get_inlineimage_val);
			$get_inlineimage_val = $image_attribute[0];
			$uploadDir = wp_upload_dir();
			$inlineimageDir = $uploadDir['basedir'] . '/smack_inline_images';
			$inlineimageURL = $uploadDir['baseurl'] . '/smack_inline_images';
			$wp_media_url = $uploadDir['baseurl'];

			$get_media_settings = get_option('uploads_use_yearmonth_folders');
			if ($get_media_settings == 1) {
				$dirname = date('Y') . '/' . date('m');
				$full_path = $uploadDir['basedir'] . '/' . $dirname;
				$baseurl = $uploadDir['baseurl'] . '/' . $dirname;
			} else {
				$full_path = $uploadDir['basedir'];
				$baseurl = $uploadDir['baseurl'];
			}

			$wp_media_path = $full_path;
			$eventKey = $_POST['postdata']['uploadedFile'];
			$inlineimageDirpath = $inlineimageDir . '/' . $eventKey;
			$imagelist = scanDirectories($inlineimageDirpath);
			$currentLoc = '';
			if (!$imagelist) {
				$noimage = WP_CONST_ULTIMATE_CSV_IMP_DIR . "images/noimage.png";
				$oldWord = $shortcode;
				$newWord = '<img src="' . $noimage . '" />';
				$post_content = str_replace($oldWord, $newWord, $post_content);
				$result['post_content'] = $post_content;
				$result['inlineimage_shortcode'] = "No-Image-count:" . $inline_shortcode_count;

			} else {
				foreach ($imagelist as $imgwithloc) {
					if (strpos($imgwithloc, $get_inlineimage_val)) {
						$currentLoc = $imgwithloc;
					}
				}

				$exploded_currentLoc = explode("$eventKey", $currentLoc);
				if (!empty($exploded_currentLoc) && isset($exploded_currentLoc[1])) {
					$inlimg_curr_loc = $exploded_currentLoc[1];
				} else {
					$inlimg_curr_loc = '';
				}

				$inlineimageURL = $inlineimageURL . '/' . $eventKey . $inlimg_curr_loc;

				$helperObj = new WPImporter_includes_helper();
				$helperObj->get_fimg_from_URL($inlineimageURL, $wp_media_path, $get_inlineimage_val, '', '', '');

				$wp_media_path = $wp_media_path . "/" . $get_inlineimage_val;
				if (@getimagesize($wp_media_path)) {
					$img = wp_get_image_editor($wp_media_path);
					if (!is_wp_error($img)) {
						$sizes_array = array(// #1 - resizes to 1024x768 pixel, square-cropped image
							array('width' => 1024, 'height' => 768, 'crop' => true), // #2 - resizes to 100px max width/height, non-cropped image
							array('width' => 100, 'height' => 100, 'crop' => false), // #3 - resizes to 100 pixel max height, non-cropped image
							array('width' => 300, 'height' => 100, 'crop' => false), // #3 - resizes to 624x468 pixel max width, non-cropped image
							array('width' => 624, 'height' => 468, 'crop' => false));
						$resize = $img->multi_resize($sizes_array);
					}
					$inline_file ['guid'] = $baseurl . "/" . $get_inlineimage_val;
					$inline_file ['post_title'] = $get_inlineimage_val;
					$inline_file ['post_content'] = '';
					$inline_file ['post_status'] = 'attachment';
					$wp_upload_dir = wp_upload_dir();
					$attachment = array('guid' => $inline_file ['guid'], 'post_mime_type' => 'image/jpeg', 'post_title' => preg_replace('/\.[^.]+$/', '', @basename($inline_file ['guid'])), 'post_content' => '', 'post_status' => 'inherit');
					if ($get_media_settings == 1) {
						$generate_attachment = $dirname . '/' . $get_inlineimage_val;
					} else {
						$generate_attachment = $get_inlineimage_val;
					}
					$uploadedImage = $wp_upload_dir['path'] . '/' . $get_inlineimage_val;
					//duplicate check
					global $wpdb;
					$existing_attachment = array();
					$query = $wpdb->get_results("select post_title from $wpdb->posts where post_type = 'attachment' and post_mime_type = 'image/jpeg'");

					foreach ($query as $key) {

						$existing_attachment[] = $key->post_title;

					}
					//duplicate check
					if (!in_array($attachment['post_title'], $existing_attachment)) {
						$attach_id = wp_insert_attachment($attachment, $generate_attachment, $postID);

						$attach_data = wp_generate_attachment_metadata($attach_id, $uploadedImage);
						wp_update_attachment_metadata($attach_id, $attach_data);
						set_post_thumbnail($postID, $attach_id);
					}

					// if($shortcode_mode == 'Inline') {
					$oldWord = $shortcode;
					$image_attribute[1] = isset($image_attribute[1]) ? $image_attribute[1] : '';
					$image_attribute[2] = isset($image_attribute[2]) ? $image_attribute[2] : '';
					$image_attribute[3] = isset($image_attribute[3]) ? $image_attribute[3] : '';
					$newWord = '<img src="' . $inline_file['guid'] . '" ' . $image_attribute[1] . ' ' . $image_attribute[2] . ' ' . $image_attribute[3] . ' />';
					$post_content = str_replace($oldWord, $newWord, $post_content);
					$result['post_content'] = $post_content;
					$result['inlineimage_shortcode'] = $inline_shortcode_count;
					// }
				}
			}

		}
		return $result;


	}
}

function scanDirectories($rootDir, $allData = array()) {
	// set filenames invisible if you want
	$invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
	// run through content of root directory
	if (!is_dir($rootDir)) {
		return false;
	}
	$dirContent = scandir($rootDir);
	foreach ($dirContent as $key => $content) {
		// filter all files not accessible
		$path = $rootDir . '/' . $content;
		if (!in_array($content, $invisibleFileNames)) {
			// if content is file & readable, add to array
			if (is_file($path) && is_readable($path)) {
				// save file name with path
				$allData[] = $path;
				// if content is a directory and readable, add path and name
			} elseif (is_dir($path) && is_readable($path)) {
				// recursive callback to open new directory
				$allData = scanDirectories($path, $allData);
			}
		}
	}
	return $allData;
}
