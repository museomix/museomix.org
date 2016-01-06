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

$impCE = new WPImporter_includes_helper();
$nonce_Key = $impCE->create_nonce_key();
?>
<div style="width:100%;">
	<div id="accordion">
		<table class="table-importer">
			<tr>
				<td>
					<h3><?php echo __('CSV Import Options', 'wp-ultimate-csv-importer'); ?></h3>

					<div
						id='sec-one' <?php if ($_REQUEST['step'] != 'uploadfile') { ?> style='display:none;' <?php } ?>>
						<?php if (is_dir($impCE->getUploadDirectory('default'))) {
							if (!is_writable($impCE->getUploadDirectory('default'))) {
								if (!chmod($impCE->getUploadDirectory('default'), 0777)) { ?>
									<input type='hidden' id='is_uploadfound' name='is_uploadfound'
										   value='notfound'/> <?php
								}
							} else { ?>
								<input type='hidden' id='is_uploadfound' name='is_uploadfound' value='found'/>
							<?php } ?>
						<?php } else { ?>
							<input type='hidden' id='is_uploadfound' name='is_uploadfound' value='notfound'/>
						<?php } ?>
						<div class="warning" id="warning" name="warning"
							 style="display:none;margin: 4% 0 4% 22%;"></div>
						<form
							action='<?php echo admin_url() . 'admin.php?page=' . WP_CONST_ULTIMATE_CSV_IMP_SLUG . '/index.php&__module=' . $_REQUEST['__module'] . '&step=mapping_settings' ?>'
							id='browsefile' enctype="multipart/form-data" method='post' name='browsefile'>
							<div class="importfile" align='center'>
								<div id='filenamedisplay'></div>
								<!--<form class="add:the-list: validate" style="clear:both;" method="post" enctype="multipart/form-data" onsubmit="return file_exist();">-->
								<div class="container">
									<?php echo $impCE->smack_csv_import_method(); ?>

									<input type='hidden' id="pluginurl" value="<?php echo WP_CONTENT_URL; ?>">
									<input type='hidden' id='dirpathval' name='dirpathval'
										   value='<?php echo ABSPATH; ?>'/>
									<?php $uploadDir = wp_upload_dir(); ?>
									<input type="hidden" id="uploaddir" value="<?php if (isset($uploadDir)) {
										echo $uploadDir['basedir'];
									} ?>">
									<input type="hidden" id="uploadFileName" name="uploadfilename" value="">
									<input type='hidden' id='uploadedfilename' name='uploadedfilename' value=''>
									<input type='hidden' id='upload_csv_realname' name='upload_csv_realname' value=''>
									<input type='hidden' id='current_file_version' name='current_file_version' value=''>
									<input type='hidden' id='current_module' name='current_module'
										   value='<?php if (isset($_REQUEST['__module'])) {
											   echo $_REQUEST['__module'];
										   } ?>'>
									</span>
									<!-- The global progress bar -->
									<div class="form-group" style="padding-bottom:20px;">
										<table>
										<tr>
								<div style="float:right;">
									<input type='button' name='clearform' id='clearform'
										   title='<?php echo __("Clear", 'wp-ultimate-csv-importer'); ?>'
										   value='<?php echo $impCE->reduceStringLength(__("Clear", 'wp-ultimate-csv-importer'), 'Clear'); ?>'
										   onclick="Reload();" class='btn btn-warning' style="margin-right:15px;"/>

									<input type='submit' name='importfile' id='importfile'
										   title='<?php echo __("Next", 'wp-ultimate-csv-importer'); ?>'
										   value='<?php echo $impCE->reduceStringLength(__("Next", 'wp-ultimate-csv-importer'), 'Next'); ?> >>'
										   disabled class='btn btn-primary' style="margin-right:15px;"/>
								</div>
			</tr>
		</table>
		<!--<div class="warning" id="warning" name="warning" style="display:none;"></div>-->
		<!-- The container for the uploaded files -->
		<div id="files" class="files"></div>
		<br>
	</div>
		<input type='hidden' name='importid' id='importid'>
	<!--	<div class='section-one' align='center'>
		<input type = 'button' name='clearform' id='clearform' value='Clear' onclick="Reload();" class = 'btn btn-warning' />
		<input type = 'submit' name='importfile' id='importfile' value='Next>>' disabled class = 'btn btn-primary' />
		<input type = 'hidden' name = 'importid' id = 'importid' >
		</div> -->
	</form>
</div>
</div>
</td>
</tr>
<tr>
	<td>
		<form name='mappingConfig'
			  action="<?php echo admin_url(); ?>admin.php?page=<?php echo WP_CONST_ULTIMATE_CSV_IMP_SLUG; ?>/index.php&__module=<?php echo $_REQUEST['__module'] ?>&step=importoptions"
			  method="post" onsubmit="return import_csv();">
			<div class='msg' id='showMsg' style='display:none;'></div>
			<?php $_SESSION['SMACK_MAPPING_SETTINGS_VALUES'] = $_POST;
			if (isset($_POST['mydelimeter'])) {
				$_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['delim'] = $_POST['mydelimeter'];
			}
			$wpcsvsettings = array();
			$custom_key = array();
			$wpcsvsettings = get_option('wpcsvfreesettings');
			?>
			<h3><?php echo __('Map CSV to WP fields/attributes', 'wp-ultimate-csv-importer'); ?></h3>
			<?php if (isset($_REQUEST['step']) && $_REQUEST['step'] == 'mapping_settings')  { ?>
			<div id='sec-two' <?php if ($_REQUEST['step'] != 'mapping_settings') { ?> style='display:none;' <?php } ?> >
				<div class='mappingsection'>
					<h2>
						<div
							class="secondformheader"><?php echo __('Import Data Configuration', 'wp-ultimate-csv-importer'); ?></div>
					</h2>
					<?php
					if (isset($_FILES['inlineimages'])) {
						if (isset($_POST['uploadfilename']) && $_POST['uploadfilename'] != '') {
							$get_file_name = $_POST['uploadfilename'];
							$filehashkey = $impCE->convert_string2hash_key($get_file_name);
						}
						$uploaded_compressedFile = $_FILES['inlineimages']['tmp_name'];
						$get_basename_zipfile = explode('.', $_FILES['inlineimages']['name']);
						$basename_zipfile = $get_basename_zipfile[0];
						$location_to_extract = $uploadDir['basedir'] . '/smack_inline_images/' . $filehashkey;
						$extracted_image_location = $uploadDir['baseurl'] . '/smack_inline_images/' . $filehashkey;
						if (class_exists('ZipArchive')) {
							$zip = new ZipArchive;
							if (!empty($uploaded_compressedFile)) {
								if ($zip->open($uploaded_compressedFile) === TRUE) {
									$zip->extractTo($location_to_extract);
									$zip->close();
									$extracted_status = 1;
								} else {
									$extracted_status = 0;
								}
							}
						}
					}
					?>
					<?php if (isset($_REQUEST['__module']) && $_REQUEST['__module'] == 'custompost') { ?>
						<div class='importstatus' style='display:true;'>
							<input type="hidden" id="customposts" name="customposts" value="">

							<div style='float:left'><label
									id="importalign"><?php echo __('Select Post Type', 'wp-ultimate-csv-importer'); ?> </label>
								<span class="mandatory"> * </span></div>
							<div style='float:left;margin-right:10px'>
								<select name='custompostlist' id='custompostlist'>
									<option value='select'>---Select---</option>
									<?php
									foreach (get_post_types() as $key => $value) {
										if (($value != 'featured_image') && ($value != 'attachment') && ($value != 'wpsc-product') && ($value != 'wpsc-product-file') && ($value != 'revision') && ($value != 'nav_menu_item') && ($value != 'post') && ($value != 'page') && ($value != 'wp-types-group') && ($value != 'wp-types-user-group')) { ?>
											<option id="<?php echo($value); ?>"> <?php echo($value); ?> </option>
										<?php }
									} ?>
								</select>
							</div>
							<div style='float:left'>
								<a href="#" class="tooltip">
									<img src="<?php echo WP_CONST_ULTIMATE_CSV_IMP_DIR; ?>images/help.png"/>
			<span class="tooltipCustompost">
			<img class="callout" src="<?php echo WP_CONST_ULTIMATE_CSV_IMP_DIR; ?>images/callout.gif"/>
			<strong><?php echo __('Select your custompost type', 'wp-ultimate-csv-importer'); ?></strong>
			<img src="<?php echo WP_CONST_ULTIMATE_CSV_IMP_DIR; ?>images/help.png"
				 style="margin-top: 6px;float:right;"/>
			</span>
								</a>
							</div>
						</div>
					<?php } ?>
					<?php echo $impCE->getImportDataConfiguration(); ?>
				</div>
				<div id='mappingheader' class='mappingheader'>
					<?php
					//   $impCE = CallSkinnyObj::getInstance();
					$allcustomposts = '';
					$mFieldsArr = '';
					$delimeter = '';
					$mappingFields_arr = array();
					$filename = '';
					$records = '';
					if (isset($_POST['uploadfilename']) && $_POST['uploadfilename'] != '') {
						$file_name = $_POST['uploadfilename'];
						$filename = $impCE->convert_string2hash_key($file_name);

					}
					if (isset($_POST['mydelimeter'])) {
						$delimeter = $_POST['mydelimeter'];
					}
					if (isset($_POST['upload_csv_realname']) && $_POST['upload_csv_realname'] != '') {
						$uploaded_csv_name = $_POST['upload_csv_realname'];
					}

					$getrecords = $impCE->csv_file_data($filename);
					$getcustomposts = get_post_types();
					foreach ($getcustomposts as $keys => $value) {
						if (($value != 'featured_image') && ($value != 'attachment') && ($value != 'wpsc-product') && ($value != 'wpsc-product-file') && ($value != 'revision') && ($value != 'nav_menu_item') && ($value != 'post') && ($value != 'page') && ($value != 'wp-types-group') && ($value != 'wp-types-user-group')) {
							$allcustomposts .= $value . ',';
						}
					}
					?>
					<table style="font-size: 12px;" class="table table-striped" id='FIELDGRP'>
						<tr>
								<div align='center' style='float:right;'>
									<?php $cnt = count($impCE->defCols) + 2;
									$cnt1 = count($impCE->headers);
									$records = count($getrecords);
									$imploaded_array = implode(',', $impCE->headers); ?>
									<input type='hidden' id='imploded_header' name='imploded_array'
										   value='<?php if (isset($imploaded_array)) {
											   echo $imploaded_array;
										   } ?>'>
									<input type='hidden' id='h1' name='h1' value="<?php if (isset($cnt)) {
										echo $cnt;
									} ?>"/>
									<input type='hidden' id='h2' name='h2' value="<?php if (isset($cnt1)) {
										echo $cnt1;
									} ?>"/>
									<input type='hidden' name='selectedImporter' id='selectedImporter'
										   value="<?php if (isset($_REQUEST['__module'])) {
											   echo $_REQUEST['__module'];
										   } ?>"/>
									<input type="hidden" id="prevoptionindex" name="prevoptionindex" value=""/>
									<input type="hidden" id="prevoptionvalue" name="prevoptionvalue" value=""/>
									<input type='hidden' id='current_record' name='current_record' value='0'/>
									<input type='hidden' id='totRecords' name='totRecords'
										   value='<?php if (isset($records)) {
											   echo $records;
										   } ?>'/>
									<input type='hidden' id='tmpLoc' name='tmpLoc'
										   value='<?php echo WP_CONST_ULTIMATE_CSV_IMP_DIR; ?>'/>
									<input type='hidden' id='nonceKey' name='wpnonce'
										   value='<?php echo $nonce_Key; ?>'/>
									<input type='hidden' id='uploadedFile' name='uploadedFile'
										   value="<?php if (isset($filename)) {
											   echo $filename;
										   } ?>"/>
									<!-- real uploaded filename -->
									<input type='hidden' id='uploaded_csv_name' name='uploaded_csv_name'
										   value="<?php if (isset($uploaded_csv_name)) {
											   echo $uploaded_csv_name;
										   } ?>"/>
									<input type='hidden' id='select_delimeter' name='select_delimeter'
										   value="<?php if (isset($delimeter)) {
											   echo $delimeter;
										   } ?>"/>
									<input type='hidden' id='stepstatus' name='stepstatus'
										   value='<?php if (isset($_REQUEST['step'])) {
											   echo $_REQUEST['step'];
										   } ?>'/>
									<input type='hidden' id='mappingArr' name='mappingArr' value=''/>
									<input type='hidden' id='inline_image_location' name='inline_image_location'
										   value='<?php if (isset($extracted_image_location)) {
											   echo $extracted_image_location;
										   } ?>'/>
								</div>
						</tr>
						<?php
						$count = 0;
						if (isset($_REQUEST['__module']) && $_REQUEST['__module'] == 'page') {
							unset($impCE->defCols['post_category']);
							unset($impCE->defCols['post_tag']);
						}
						if (isset($_REQUEST['__module']) && $_REQUEST['__module'] != 'page') {
							unset($impCE->defCols['menu_order']);
							unset($impCE->defCols['wp_page_template']);
						}
						?>
						<tr>
							<td colspan='4' class="left_align columnheader"
								style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
								<div id='custfield_core' style='font-size:18px; font-family:times;'><b><?php echo __('WordPress Fields:','wp-ultimate-importer'); ?></b>
								</div>
							</td>
						</tr>
						<tr>
							<td class="left_align columnheader" style = 'padding-left:170px;'>
								<b><?php echo __('WP FIELDS', 'wp-ultimate-csv-importer'); ?></b></td>
							<td class="columnheader" style = 'padding-left:55px;'><b><?php echo __('CSV HEADER', 'wp-ultimate-csv-importer'); ?></b>
							</td>
							<td>	</td>
							<td></td>
						</tr>
						<?php
						foreach ($impCE->defCols as $key => $value) {
							if (!strstr($key, 'CF:') && !strstr($key, 'SEO:')) {
								?>

								<tr>
									<td class="left_align" style = 'padding-left:150px;'>
										<input type='hidden' name='fieldname<?php print($count); ?>'
											   id='fieldname<?php print($count); ?>' value= '<?php echo $key; ?>' />
										<label
											class='wpfields'><?php print('<b>' . $key . '</b></label><br><label class="samptxt" style="padding-left:20px">[Name: ' . $key . ']'); ?></label>
									</td>

									<td>
										
										<?php if($key == 'post_status'){ ?>
                                                                               <select name="mapping<?php print($count); ?>" id="mapping<?php print($count); ?>" onChange="changefield();">
                                                                               <?php }else{ ?>
                                                                               <select name="mapping<?php print($count); ?>"
                                                                                               id="mapping<?php print($count); ?>">
                                                                               <?php } ?>
											<option><?php echo __('-- Select --', WP_CONST_ULTIMATE_CSV_IMP_SLUG); ?></option>
											<?php foreach ($impCE->headers as $key1 => $value1) { ?>

												<option><?php echo $value1; ?></option>
											<?php } ?>
										</select>
										<script type="text/javascript">
											jQuery("select#mapping<?php print($count); ?>").find('option').each(function () {
												if (jQuery(this).val() == "<?php print($key);?>") {
													jQuery(this).prop('selected', true);
												}
											});
										</script>

									</td>
									<td>

									</td>
									<td></td>
								</tr>
								<?php
								$count++;
							}
						}
						?>
						<input type='hidden' id='wpfields' name='wpfields' value='<?php echo($count) ?>'/>
					</table>
					<?php
					foreach ($mappingFields_arr as $mkey => $mval) {
						$mFieldsArr .= $mkey . ',';
					}
					$mFieldsArr = substr($mFieldsArr, 0, -1);
					?>
					<table style="font-size: 12px;" class="table table-striped" id='CF_FIELDGRP'>
						<tr>

							<td colspan=5 class='left_align columnheader'
								style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
								<div id='custfield_core' style='font-size:18px; font-family:times;'><b><?php echo __('Custom Fields:','wp-ultimate-csv-importer'); ?></b>
								</div>

							</td>
						</tr>
						<?php
						foreach ($impCE->defCols as $key => $value) {
							if (strstr($key, 'CF:')) {
								?>
								<tr>
									<td class="left_align" style='width:53%; padding-left:170px;'>
										<input type='hidden' name='corefieldname<?php print($count); ?>'
											   id='corefieldname<?php print($count); ?>' value='<?php echo $key; ?>'/>

										<label
											class='wpfields'><?php print('<b>' . $value . '</b></label><br><label class="samptxt" style="padding-left:20px">[Name: ' . $value . ']'); ?></label>
									</td>
									<td>
										<select name="coremapping<?php print($count); ?>"
												id="coremapping<?php print($count); ?>">
											<option><?php echo __('-- Select --', WP_CONST_ULTIMATE_CSV_IMP_SLUG); ?></option>
											<?php foreach ($impCE->headers as $key1 => $value1) { ?>
												<option><?php echo $value1; ?></option>
											<?php } ?>
										</select>

										<script type="text/javascript">
											jQuery("select#mapping<?php print($count); ?>").find('option').each(function () {
												if (jQuery(this).val() == "<?php print($key);?>") {
													jQuery(this).prop('selected', true);
												}
											});
										</script>
									</td>

									<td>
									</td>
									<td></td>
								</tr>
								<?php
								$count++;
							}
						} ?>
						<input type='hidden' id='customfields' name='customfields' value='<?php echo($count) ?>'/>
					</table>
					<table>
						<tr>
							<td colspan='4'>
								<input type='button' class='btn btn-primary' name='addcustomfd' value='Add Custom Field'
									   style='margin-left:20px;margin-bottom:15px;margin-top:20px;'
									   onclick='addcorecustomfield(CF_FIELDGRP);'>
								<input type='hidden' id ='addcorecustomfields' value ='' >
							</td>
						</tr>
					</table>
					<?php
					$wpcsvfreesettings = get_option('wpcsvfreesettings');
					$active_plugins = get_option('active_plugins');
							if (in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins)) {
								?>
								<table style="font-size: 12px;" class="table table-striped" id='SEO_FIELDGRP'>
									<tr>

										<td colspan=5 class='left_align columnheader'
											style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
											<div id='custfield_core' style='font-size:18px; font-family:times;'><b><?php echo __('SEO Fields:','wp-ultimate-csv-importer'); ?></b>
											</div>

										</td>
									</tr>
									<?php
									foreach ($impCE->defCols as $key => $value) {
										if (strstr($key, 'SEO:')) {
											$value = str_replace('SEO:', '', $value)
											?>
											<tr>
												<td class="left_align" style='width:53%; padding-left:170px;'>
													<input type='hidden' name='seofieldname<?php print($count); ?>'
														   id='seofieldname<?php print($count); ?>'
														   value='<?php echo $key; ?>'/>

													<label
														class='wpfields'><?php print('<b>' . $value . '</b></label><br><label class="samptxt" style="padding-left:20px">[Name: ' . $value . ']'); ?></label>
												</td>
												<td>
													<select name="seomapping<?php print($count); ?>"
															id="seomapping<?php print($count); ?>">
														<option><?php echo __('-- Select --', WP_CONST_ULTIMATE_CSV_IMP_SLUG); ?></option>
														<?php foreach ($impCE->headers as $key1 => $value1) { ?>
															<option><?php echo $value1; ?></option>
														<?php } ?>
													</select>

													<script type="text/javascript">
														jQuery("select#mapping<?php print($count); ?>").find('option').each(function () {
															if (jQuery(this).val() == "<?php print($key);?>") {
																jQuery(this).prop('selected', true);
															}
														});
													</script>
												</td>
												<td>
												<td>
												</td>
												<td></td>
											</tr>
											<?php
											$count++;
										}
									} ?>
									<input type='hidden' id='seofields' name='seofields' value='<?php echo($count) ?>'/>
								</table>
							<?php }
					 ?>
					<?php $basic_count = $count - 1; ?>
					<input type="hidden" id="basic_count" name="basic_count" value="<?php echo $basic_count; ?>"/>
					<input type="hidden" id="corecustomcount" name="corecustomcount" value=0 />
					<input type="hidden" id="mapping_fields_array" name="mapping_fields_array"
						   value="<?php if (isset($mFieldsArr)) {
							   print_r($mFieldsArr);
						   } ?>"/>

					<div>
						<div class="goto_import_options" align=center>
							<div class="mappingactions" style="margin-top:26px;">
								<input type='button' id='clear_mapping' class='clear_mapping btn btn-warning'
									   name='clear_mapping'
									   title='<?php echo __("Clear Mapping", 'wp-ultimate-csv-importer'); ?>'
									   value='<?php echo __('Clear', 'wp-ultimate-csv-importer');
									   echo ' ';
									   echo $impCE->reduceStringLength(__(' Mapping', 'wp-ultimate-csv-importer'), 'Mapping'); ?>'
									   onclick='clearMapping();' style='float:left'/>
							</div>
							<div class="mappingactions">
								<input type='submit' id='goto_importer_setting'
									   title='<?php echo __("Next", 'wp-ultimate-csv-importer'); ?>'
									   class='goto_importer_setting btn btn-info' name='goto_importer_setting'
									   value='<?php echo $impCE->reduceStringLength(__('Next', 'wp-ultimate-csv-importer'), 'Next'); ?> >>'/>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</form>
	</td>
</tr>
<tr>
	<td>
		<h3><?php echo __('Settings and Performance', 'wp-ultimate-csv-importer'); ?></h3>
		<?php if (isset($_REQUEST['step']) && $_REQUEST['step'] == 'importoptions') { ?>
		<div id='sec-three' <?php if ($_REQUEST['step'] != 'importoptions') { ?> style='display:none;' <?php } ?> >
			<?php //$prevoptionindex='';?>
			<input type="hidden" id="prevoptionindex" name="prevoptionindex" value="<?php
			if (isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['prevoptionindex'])) {
				echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['prevoptionindex'];
			}
			?>"/>
			<?php if (isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES'])) { ?>
				<input type="hidden" id="prevoptionvalue" name="prevoptionvalue"
					   value="<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['prevoptionvalue']; ?>"/>
				<input type='hidden' id='current_record' name='current_record'
					   value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['current_record']; ?>'/>
				<input type='hidden' id='tot_records' name='tot_records'
					   value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?>'/>
				<input type='hidden' id='checktotal' name='checktotal'
					   value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?>'/>
				<input type='hidden' id='stepstatus' name='stepstatus'
					   value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['stepstatus']; ?>'/>
				<input type='hidden' id='selectedImporter' name='selectedImporter'
					   value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter']; ?>'/>
			<?php } ?>
			<input type='hidden' id='tmpLoc' name='tmpLoc' value='<?php echo WP_CONST_ULTIMATE_CSV_IMP_DIR; ?>'/>
			<?php if (isset($_POST)) { ?>
				<input type='hidden' id='checkfile' name='checkfile' value='<?php echo $_POST['uploadedFile']; ?>'/>
				<input type='hidden' id='select_delim' name='select_delim'
					   value='<?php echo $_POST['select_delimeter']; ?>'/>
				<input type='hidden' id='uploadedFile' name='uploadedFile1'
					   value='<?php echo $_POST['uploadedFile']; ?>'/>
				<input type='hidden' id='inline_image_location' name='location_inlineimages'
					   value='<?php echo $_POST['inline_image_location']; ?>'/>
				<input type='hidden' id='mappingArr' name='mappingArr' value=''/>
			<?php } ?>
			<!-- Import settings options -->
			<div class="postbox" id="options" style=" margin-bottom:0px;">
				<div class="inside">
					<label id="importalign"><input type='radio' id='importNow' name='importMode' value=''
												   onclick='choose_import_mode(this.id);'
												   checked/> <?php echo __("Import right away", 'wp-ultimate-csv-importer'); ?>
					</label>
					<label id="importalign"><input type='radio' id='scheduleNow' name='importMode' value=''
												   disabled/> <?php echo __("Schedule now", 'wp-ultimate-csv-importer'); ?>
					<img src="<?php echo WP_CONTENT_URL; ?>/plugins/<?php echo WP_CONST_ULTIMATE_CSV_IMP_SLUG; ?>/images/pro_icon.gif" title="PRO Feature"/> </label>

					<div id='schedule' style='display:none'>
						<input type='hidden' id='select_templatename' name='#select_templatename'
							   value='<?php if (isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['templateid'])) {
								   echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['templateid'];
							   } ?>'>
					</div>
					<div id='importrightaway' style='display:block'>

						<form method="POST">
							<ul id="settings">
								<li>
									<label id="importalign"><input name='duplicatecontent' id='duplicatecontent'
																   type="checkbox"
																   value=""> <?php echo __('Detect duplicate post content', 'wp-ultimate-csv-importer'); ?>
									</label> <br>
									<input type='hidden' name='wpnoncekey' id='wpnoncekey'
										   value='<?php echo $nonce_Key; ?>'/>
									<label id="importalign"><input name='duplicatetitle' id='duplicatetitle'
																   type="checkbox"
																   value=""> <?php echo __('Detect duplicate post title', 'wp-ultimate-csv-importer'); ?>
									</label> <br>
									<label
										id="importalign"><?php echo __('No. of posts/rows per server request', 'wp-ultimate-csv-importer'); ?></label><span
										class="mandatory" style="margin-left:-13px;margin-right:10px">*</span> <input
										name="importlimit" id="importlimit" type="text" value="1" placeholder="10"
										onblur="check_allnumeric(this.value);"></label> <?php echo $impCE->helpnotes(); ?>
									<br>
									<span class='msg' id='server_request_warning'
										  style="display:none;color:red;margin-left:-10px;"><?php echo __('You can set upto', 'wp-ultimate-csv-importer'); ?><?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?><?php echo __('per request.', 'wp-ultimate-csv-importer'); ?></span>
									<input type="hidden" id="currentlimit" name="currentlimit" value="0"/>
									<input type="hidden" id="tmpcount" name="tmpcount" value="0"/>
									<input type="hidden" id="terminateaction" name="terminateaction" value="continue"/>
									<label
										id="innertitle"><?php echo __('Inline image options', 'wp-ultimate-csv-importer'); ?></label><br/>
									<label id='importalign'> <input type='checkbox' id='multiimage' name='multiimage'
																	value=''> <?php echo __('Insert Inline Images', 'wp-ultimate-csv-importer'); ?>
									</label><br>
									<input type='hidden' id='inlineimagevalue' name='inlineimagevalue' value='none'/>
								</li>
							</ul>
							<input id="startbutton" class="btn btn-primary" type="button"
								   value="<?php echo __('Import Now', 'wp-ultimate-csv-importer'); ?>"
								   style="color: #ffffff;background:#2E9AFE;"
								   onclick="importRecordsbySettings('<?php echo site_url(); ?>');">
							<input id="terminatenow" class="btn btn-danger btn-sm" type="button"
								   value="<?php echo __('Terminate Now', 'wp-ultimate-csv-importer'); ?>"
								   style="display:none;" onclick="terminateProcess();"/>
							<input class="btn btn-warning" type="button"
								   value="<?php echo __('Reload', 'wp-ultimate-csv-importer'); ?>" id="importagain"
								   style="display:none" onclick="import_again();"/>
							<input id="continuebutton" class="btn btn-lg btn-success" type="button"
								   value="<?php echo __('Continue', 'wp-ultimate-csv-importer'); ?>"
								   style="display:none;color: #ffffff;" onclick="continueprocess();">
							<div id="ajaxloader" style="display:none"><img
									src="<?php echo WP_CONST_ULTIMATE_CSV_IMP_DIR; ?>images/ajax-loader.gif"> <?php echo __('Processing...', 'wp-ultimate-csv-importer'); ?>
							</div>

							<div class="clear"></div>
						</form>
					</div>
					<div class="clear"></div>
					<br>
				</div>
			</div>
			<?php } ?>
			<!-- Code Ends Here-->
		</div>
	</td>
</tr>
</table>
</div>
<div style="width:100%;">
	<div id="accordion">
		<table class="table-importer">
			<tr>
				<td>
					<h3><?php echo __("Summary", 'wp-ultimate-csv-importer'); ?></h3>

					<div id='reportLog' class='postbox' style='display:none;'>
						<input type='hidden' name='csv_version' id='csv_version'
							   value="<?php if (isset($_POST['uploaded_csv_name'])) {
								   echo $_POST['uploaded_csv_name'];
							   } ?>">

						<div id="logtabs" class="logcontainer">
							<div id="log" class='log'>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>

<!-- Promotion footer for other useful plugins -->
<div class="promobox" id="pluginpromo" style="width:98%;">
	<div class="accordion-group">
		<div class="accordion-body in collapse">
			<div>
				<?php $impCE->common_footer(); ?>
			</div>
		</div>
	</div>
</div>
</div>
