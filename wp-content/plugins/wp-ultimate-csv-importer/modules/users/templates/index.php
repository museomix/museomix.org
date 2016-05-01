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
require_once(WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . '/includes/WPImporter_includes_helper.php');
?>

<div style="width:100%;">
	<div id="accordion">
		<?php
		$impCE = new WPImporter_includes_helper();
		$nonce_Key = $impCE->create_nonce_key();
		?>
		<table class="table-importer">
			<tr>
				<td>
					<h3><?php echo esc_html__('CSV Import Options', 'wp-ultimate-csv-importer'); ?></h3>

					<div
							id='sec-one' <?php if (sanitize_text_field($_REQUEST['step']) !== 'uploadfile') { ?> style='display:none;' <?php } ?>>
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
						<form action='<?php echo esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => sanitize_text_field($_REQUEST['__module']), 'step' => 'mapping_settings'), $impCE->baseUrl)); ?>'
							  id='browsefile' method='post' name='browsefile'>
							<div class="importfile" align='center'>
								<div id='filenamedisplay'> </div>
								<form class="add:the-list: validate" style="clear:both;" method="post"
									  enctype="multipart/form-data" onsubmit="return file_exist();">
									<div class="container">
										<?php echo $impCE->smack_csv_import_method(); ?>
										<input type='hidden' id="pluginurl" value="<?php echo WP_CONTENT_URL; ?>">
										<input type='hidden' id='dirpathval' name='dirpathval'
											   value='<?php echo ABSPATH; ?>'/>
										<?php $uploadDir = wp_upload_dir(); ?>
										<input type="hidden" id="uploaddir"
											   value="<?php if (isset($uploadDir['basedir'])) {
												   echo $uploadDir['basedir'];
											   } ?>">
										<input type="hidden" id="uploadFileName" name="uploadfilename" value="">
										<input type='hidden' id='uploadedfilename' name='uploadedfilename' value=''>
										<input type='hidden' id='upload_csv_realname' name='upload_csv_realname'
											   value=''>
										<input type='hidden' id='current_file_version' name='current_file_version'
											   value=''>
										<input type='hidden' id='current_module' name='current_module'
											   value='<?php if (isset($_REQUEST['__module'])) {
												   echo sanitize_text_field($_REQUEST['__module']);
											   } ?>'>
										</span>
										<!-- The global progress bar -->
										<div class="form-group" style="padding-bottom:20px;">
											<table>
												<tr>
													<div style="float:right;">
														<input type='button' name='clearform' id='clearform'
															   value='<?php echo esc_attr__("Clear", 'wp-ultimate-csv-importer'); ?>'
															   onclick="Reload();"
															   class='btn btn-warning' style="margin-right:15px"/>
														<input type='submit' name='importfile' id='importfile'
															   title='<?php echo esc_attr__('Next', 'wp-ultimate-csv-importer'); ?>'
															   value='<?php echo $impCE->reduceStringLength(esc_attr__("Next", 'wp-ultimate-csv-importer'), 'Next');
															   echo(" >>"); ?>' disabled
															   class='btn btn-primary' style="margin-right:15px"/>
													</div>
												</tr>
											</table>
											<div class="warning" id="warning" name="warning" style="display:none"></div>
											<!-- The container for the uploaded files -->
											<div id="files" class="files"></div>
											<br>
										</div>
								</form>
							</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<form action='<?php echo esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => sanitize_text_field($_REQUEST['__module']), 'step' => 'importoptions'), $impCE->baseUrl)); ?>' name = 'mappingConfig' method="post" onsubmit="return import_csv();">
						<div class='msg' id='showMsg' style='display:none;'></div>
						<?php $_SESSION['SMACK_MAPPING_SETTINGS_VALUES'] = $_POST;
						$wpcsvsettings = array();
						$custom_key = array();
						$mappingFields_arr = array();
						$wpcsvsettings = get_option('wpcsvfreesettings');
						?>
						<h3><?php echo esc_html__('Map CSV to WP fields/attributes', 'wp-ultimate-csv-importer'); ?></h3>
						<?php if (isset($_REQUEST['step']) && sanitize_text_field($_REQUEST['step']) === 'mapping_settings') { ?>
						<div id='sec-two' <?php if (sanitize_text_field($_REQUEST['step']) !== 'mapping_settings') { ?> style='display:none;' <?php } ?> >
							<div class='mappingsection'>
								<h2>
									<div
											class="secondformheader"><?php echo esc_html__('Import Data Configuration', 'wp-ultimate-csv-importer'); ?></div>
								</h2>
								<div class='importstatus'>
								</div>
								<div id='mappingheader' class='mappingheader'>
									<?php
									if (isset($_POST['uploadfilename']) && sanitize_file_name($_POST['uploadfilename']) != '') {
										$file_name = sanitize_file_name($_POST['uploadfilename']);
										$filename = $impCE->convert_string2hash_key($file_name);
									}
									if (isset($_POST['upload_csv_realname']) && sanitize_file_name($_POST['upload_csv_realname']) != '') {
										$uploaded_csv_name = sanitize_file_name($_POST['upload_csv_realname']);
									}
					                                $total_row_count = '';
									$parserObj = new SmackCSVParser();
									$file = $impCE->getUploadDirectory() . '/' . $filename;
									$parserObj->parseCSV($file, 0, -1);
									$headers = $parserObj->get_CSVheaders();
									$headers = $headers[0];
									$total_row_count = $parserObj->total_row_count - 1;
									$getcustomposts = get_post_types();
									$allcustomposts = '';
									if(!empty($getcustomposts) && is_array($getcustomposts)) {
									foreach ($getcustomposts as $keys => $value) {
										if (($value !== 'featured_image') && ($value !== 'attachment') && ($value !== 'wpsc-product') && ($value !== 'wpsc-product-file') && ($value !== 'revision') && ($value !== 'nav_menu_item') && ($value !== 'post') && ($value !== 'page') && ($value !== 'wp-types-group') && ($value !== 'wp-types-user-group')) {
											$allcustomposts .= $value . ',';
										}
									}
									}
									?>
									<table style="font-size: 12px;" class='table table-striped'>
										<tr>
											<div align='center' style='float:right;'>
												<?php $cnt = count($impCE->defCols) + 2;
												$cnt1 = count($headers);
												$records = $total_row_count ?>
												<input type='hidden' id='h1' name='h1' value="<?php if (isset($cnt)) {
													echo $cnt;
												} ?>"/>
												<input type="hidden" id="h2" name="h2" value="<?php if (isset($cnt1)) {
													echo $cnt1;
												} ?>"/>
												<input type='hidden' name='selectedImporter' id='selectedImporter'
													   value="<?php if (isset($_REQUEST['__module'])) {
														   echo sanitize_text_field($_REQUEST['__module']);
													   } ?>"/>
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
												<input type='hidden' id='stepstatus' name='stepstatus'
													   value='<?php if (isset($_REQUEST['step'])) {
														   echo sanitize_text_field($_REQUEST['step']);
													   } ?>'/>
											</div>
										</tr>
										<?php
										$count = 0;
										$usersObj = new UsersActions();
										?>
										<tr>
											<td colspan='4' class="left_align columnheader"
												style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
												<div id='custfield_core' style='font-size:18px; font-family:times;'><b><?php echo esc_html__('WordPress Fields:','wp-ultimate-importer'); ?></b>
												</div>
											</td>
										</tr>
										<tr>
											<td class="left_align columnheader" style='padding-left:170px;'>
												<b><?php echo esc_html__('WP FIELDS', 'wp-ultimate-csv-importer'); ?></b></td>
											<td class="columnheader" style ='padding-left:55px;'>
												<b><?php echo esc_html__('CSV HEADER', 'wp-ultimate-csv-importer'); ?></b></td>
											<td>	</td>
											<td></td>
										</tr>
										<?php
										if(!empty($usersObj->defCols) && is_array($usersObj->defCols)) {
										foreach ($usersObj->defCols as $key => $value) {
											?>
											<tr>
												<td class="left_align" style='padding-left:150px;'>
													<input type='hidden' name='fieldname<?php print($count); ?>'
														   id='fieldname<?php print($count); ?>' value= '<?php echo $key; ?>'/>
													<label
															class='wpfields'><?php print('<b>' . $key . '</b></label><br><label class="samptxt" style="padding-left:20px">[Name: ' . $key . ']'); ?></label>
												</td>

												<td>
													<select name="mapping<?php print($count); ?>"
															id="mapping<?php print($count); ?>">
														<option>-- Select --</option>
														<?php 
														if(!empty($headers) && is_array($headers)){
														foreach ($headers as $key1 => $value1) { ?>

															<option><?php echo $value1; ?></option>
														<?php } 
														}?>
													</select>
													<script type="text/javascript">
														jQuery("select#mapping<?php print(esc_js($count)); ?>").find('option').each(function () {
															if (jQuery(this).val() == "<?php print(esc_js($key));?>") {
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
									<div>
										<div class="goto_import_options" style ='padding-left:330px;'>
											<div class="mappingactions" style="margin-top:26px;">
												<input type='button' id='clear_mapping'
													   title='<?php echo esc_attr__('clear Mapping', 'wp-ultimate-csv-importer'); ?>'
													   class='clear_mapping btn btn-warning' name='clear_mapping'
													   value='<?php echo esc_attr__("Clear", 'wp-ultimate-csv-importer');
													   echo ' ';
													   echo $impCE->reduceStringLength(esc_attr__(" Mapping", 'wp-ultimate-csv-importer'), 'Mapping'); ?>'
													   onclick='clearMapping();' style='float:left'/>
											</div>
											<div class="mappingactions">
												<input type='submit' id='goto_importer_setting'
													   title='<?php echo esc_attr__("Next", 'wp-ultimate-csv-importer'); ?>'
													   class='goto_importer_setting btn btn-info' name='goto_importer_setting'
													   value='<?php echo $impCE->reduceStringLength(esc_attr__("Next", 'wp-ultimate-csv-importer'), 'Next'); ?> >>'/>
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
					<h3><?php echo esc_html__('Settings and Performance', 'wp-ultimate-csv-importer'); ?></h3>
					<?php if (isset($_REQUEST['step']) && sanitize_text_field($_REQUEST['step']) === 'importoptions') { ?>
					<div id='sec-three' <?php if (sanitize_text_field($_REQUEST['step']) !== 'importoptions') { ?> style='display:none;' <?php } ?> >
						<?php if (isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES'])) { ?>
							<input type='hidden' id='current_record' name='current_record'
								   value='<?php echo sanitize_text_field($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['current_record']); ?>'/>
							<input type='hidden' id='tot_records' name='tot_records'
								   value='<?php echo sanitize_text_field($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']); ?>'/>
							<input type='hidden' id='checktotal' name='checktotal'
								   value='<?php echo sanitize_text_field($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']); ?>'/>
							<input type='hidden' id='stepstatus' name='stepstatus'
								   value='<?php echo sanitize_text_field($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['stepstatus']); ?>'/>
							<input type='hidden' id='selectedImporter' name='selectedImporter'
								   value='<?php echo sanitize_text_field($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter']); ?>'/>
						<?php } ?>
						<?php if (isset($_POST)) { ?>
							<input type='hidden' id='tmpLoc' name='tmpLoc' value='<?php echo WP_CONST_ULTIMATE_CSV_IMP_DIR; ?>'/>
							<input type='hidden' id='checkfile' name='checkfile' value='<?php echo sanitize_file_name($_POST['uploadedFile']); ?>'/>
							<input type='hidden' id='uploadedFile' name='uploadedFile'
								   value='<?php echo sanitize_file_name($_POST['uploadedFile']); ?>'/>
						<?php } ?>
						<!-- Import settings options -->
						<div class="postbox" id="options" style=" margin-bottom:0px;">
							<div class="inside">
								<label id='importalign'><input type='radio' id='importNow' name='importMode' value=''
															   onclick='choose_import_mode(this.id);'
															   checked/> <?php echo esc_html__("Import right away", 'wp-ultimate-csv-importer'); ?>
								</label>
								<label id='importalign'><input type='radio' id='scheduleNow' name='importMode' value=''
															   onclick='choose_import_mode(this.id);'
															   disabled/> <?php echo esc_html__("Schedule now", 'wp-ultimate-csv-importer'); ?>
									<img src="<?php echo esc_url(WP_CONTENT_URL.'/plugins/'.WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/images/pro_icon.gif');?>" title="PRO Feature"/> </label>

								<div id='schedule' style='display:none'>
									<input type='hidden' id='select_templatename' name='#select_templatename'
										   value='<?php if (isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['templateid'])) {
											   echo sanitize_text_field($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['templateid']);
										   } ?>'>
								</div>
								<div id='importrightaway' style='display:block'>
									<form method="POST">
										<ul id="settings">
											<li>

												<input type='hidden' name='wpnoncekey' id='wpnoncekey'
													   value='<?php echo $nonce_Key; ?>'/>
												<label
														id='importalign'><?php echo esc_html__('No. of posts/rows per server request', 'wp-ultimate-csv-importer'); ?></label>
												<span class="mandatory" style="margin-left:-13px;margin-right:10px">*</span> <input
														name="importlimit" id="importlimit" type="text" value="1" placeholder="10"
														onblur="check_allnumeric(this.value);"></label> <?php echo $impCE->helpnotes(); ?>
												<br>
									<div>
									<span class='msg' id='server_request_warning'
										  style="display:none;color:red;margin-left:-10px;"><?php echo esc_html__('You can set upto', 'wp-ultimate-csv-importer'); ?><?php echo ' '.sanitize_text_field($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']).' '; ?><?php echo esc_html__('per request.', 'wp-ultimate-csv-importer'); ?></span>
									</div>
												<input type="hidden" id="currentlimit" name="currentlimit" value="1"/>
												<input type="hidden" id="tmpcount" name="tmpcount" value="0"/>
												<input type="hidden" id="terminateaction" name="terminateaction" value="continue"/>
											</li>
										</ul>
										<input id="startbutton" class="btn btn-primary" type="button"
											   value="<?php echo esc_attr__('Import Now', 'wp-ultimate-csv-importer'); ?>"
											   style="color: #ffffff;background:#2E9AFE;" onclick="importRecordsbySettings();"/>
										<input id="terminatenow" class="btn btn-danger btn-sm" type="button"
											   value="<?php echo esc_attr__('Terminate Now', 'wp-ultimate-csv-importer'); ?>"
											   style="display:none;" onclick="terminateProcess();"/>
										<input class="btn btn-warning" type="button"
											   value="<?php echo esc_attr__('Reload', 'wp-ultimate-csv-importer'); ?>" id="importagain"
											   style="display:none;" onclick="import_again();"/>
										<input id="continuebutton" class="btn btn-lg btn-success" type="button"
											   value="<?php echo esc_attr__('Continue', 'wp-ultimate-csv-importer'); ?>"
											   style="display:none;color: #ffffff;" onclick="continueprocess();">

										<div id="ajaxloader" style="display:none"><img
													src="<?php echo esc_url(WP_CONST_ULTIMATE_CSV_IMP_DIR.'images/ajax-loader.gif');?>"> <?php echo esc_html__('Processing...', 'wp-ultimate-csv-importer'); ?>
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
						<h3><?php echo esc_html__("Summary", 'wp-ultimate-csv-importer'); ?></h3>

						<div id='reportLog' class='postbox' style='display:none;'>
							<input type='hidden' name='csv_version' id='csv_version'
								   value="<?php if (isset($_POST['uploaded_csv_name'])) {
									   echo sanitize_text_field($_POST['uploaded_csv_name']);
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
