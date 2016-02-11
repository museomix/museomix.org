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
$siteurl = get_option('siteurl');
$noncedata = $skinnyData['wp_nonce'];
?>
<div class="accordion" id="accordion2" style='width:98%;'>
	<div class="accordion-group">
		<div id="collapseTwo" class="accordion-body in collapse">
			<span style="margin: 4% 0px 4% 22%; color: red; font-weight: bold;" name="warning" id="warning">
				<p>
					<marquee>
						<span><?php echo __('Check your system configuration before proceeding the export. It may help to prevent from facing server configuration issues', 'wp-ultimate-csv-importer'); ?></span>
						<span style='position:relative;left:4px;'>
							<a href='<?php echo esc_url(add_query_arg(array('page' => WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/index.php', '__module' => 'settings'), $impCE->baseUrl)); ?>'><?php echo __('Click here', 'wp-ultimate-csv-importer'); echo ' '; ?></a>
							<?php echo __('to refer your server configuration.'); ?>
						</span>
					</marquee>
				</p>
			</span>
			<div class="accordion-inner">
				<div>
					<span class="settings-icon">
						<img src="<?php echo esc_url(WP_CONTENT_URL.'/plugins/'.WP_CONST_ULTIMATE_CSV_IMP_SLUG.'/images/export.png');?>" width="24" height="24"/>
					</span>
					<label>
						<h3 id="exporttitle"><?php echo __('Export Data With Advanced Filters', 'wp-ultimate-csv-importer'); ?></h3>
					</label>
				</div>
				<div style="margin-left:20px;">
					<form id="exportmoduleform" class="form-horizontal" method="post" name="exportmodule"
						  action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" onsubmit="return export_module();">
						<?php wp_nonce_field('export_file', 'my-nonce'); ?>
						<input name="action" value="export_file" type="hidden">
						<div class="table-responsive">
							<table style='width:100%;' class='table exportmodule'>
								<th colspan='2'>
									<label class='h-exportmodule'>
										<h3 id="innertitle"><?php echo __('To export data based on the filters', 'wp-ultimate-csv-importer'); ?></h3>
									</label>
								</th>
								<tr>
									<td>
										<label>
											<input type='checkbox' name='getdatawithdelimiter' id='getdatawithdelimiter' value='getdatawithdelimeter' onclick='addexportfilter(this.id);'/>
											<span id="align"><?php echo __('Export data with auto delimiter', 'wp-ultimate-csv-importer'); ?></span>
										</label>
										<div id='delimeter' style='padding:10px;display:none;'>
											<label id='delistatus'><b><?php echo __('Delimiters', 'wp-ultimate-csv-importer'); ?> </b></label>
											<select name='postwithdelimiter' id='postwithdelimiter'>
												<option><?php echo __('Select', 'wp-ultimate-csv-importer'); ?></option>
												<option>,</option>
												<option>:</option>
												<option>;</option>
												<option>{Tab}</option>
												<option>{Space}</option>
											</select>
											<label><b><?php echo __('Other Delimiters', 'wp-ultimate-csv-importer'); ?></b>
											</label><input type='text' name='others_delimiter' id='others_delimiter' size=6 />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<label>
											<input type='checkbox' name='getdataforspecificperiod' id='getdataforspecificperiod' value='getdataforspecificperiod' onclick='addexportfilter(this.id);'/>
											<span id="align"><?php echo __('Export data for the specific period', 'wp-ultimate-csv-importer'); ?></span>
										</label>
										<div id='specificperiodexport' style='padding:10px;display:none;'>
											<label id='periodstartfrom'><b><?php echo __('Start From', 'wp-ultimate-csv-importer'); ?> </b></label>
											<input type='text' class='form-control' name='postdatefrom' style='cursor:default;width:25%;' readonly id='postdatefrom' value=''/>
											<label id='periodendto'><b><?php echo __('End To', 'wp-ultimate-csv-importer'); ?> </b></label>
											<input type='text' class='form-control' name='postdateto' style='cursor:default;width:25%;' readonly id='postdateto' value=''/>
											<input type='hidden' name='nonce' id='nonce' value='<?php if (isset($noncedata)) { echo $noncedata; } ?>' />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<label>
											<input type='checkbox' name='getdatawithspecificstatus' id='getdatawithspecificstatus' value='getdatawithspecificstatus' onclick='addexportfilter(this.id);'/>
											<span id="align"><?php echo __('Export data with the specific status', 'wp-ultimate-csv-importer'); ?></span>
										</label>
										<div id='specificstatusexport' style='padding:10px;display:none;'>
											<label id='status'><b><?php echo __('Status', 'wp-ultimate-csv-importer'); ?> </b></label>
											<select name='postwithstatus' id='postwithstatus'>
												<option>All</option>
												<option>Publish</option>
												<option>Sticky</option>
												<option>Private</option>
												<option>Protected</option>
												<option>Draft</option>
												<option>Pending</option>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<label>
											<input type='checkbox' name='getdatabyspecificauthors' id='getdatabyspecificauthors' value='getdatabyspecificauthors' onclick='addexportfilter(this.id);'/>
											<span id="align"><?php echo __('Export data by specific authors', 'wp-ultimate-csv-importer'); ?></span>
										</label>
										<div id='specificauthorexport' style='padding:10px;display:none;'>
											<label id='authors'><b><?php echo __('Authors', 'wp-ultimate-csv-importer'); ?></b></label>
											<?php $blogusers = get_users('blog_id=1&orderby=nicename'); ?>
											<select name='postauthor' id='postauthor'>
												<option value='0'>All</option>
												<?php 
												if(is_array($blogusers) && !empty($blogusers)){
												foreach ($blogusers as $user) { ?>
													<option value='<?php echo esc_html($user->ID); ?>'> <?php echo esc_html($user->display_name); ?> </option>
												<?php } 
												}?>
											</select>
										</div>
									</td>
								</tr>
							</table>
							<script type='text/javascript'>
								jQuery(document).ready(function () {
									jQuery('#postdatefrom').datepicker({
										dateFormat: 'yy-mm-dd'
									});
									jQuery('#postdateto').datepicker({
										dateFormat: 'yy-mm-dd'
									});
								});
							</script>
						</div>
						<div class="table-responsive" id="exporttable">
							<table class='table exportmodule'>
								<th colspan='2'>
									<label class='h-exportmodule'>
										<h3 id="innertitle"><?php echo __('Select your module to export the data', 'wp-ultimate-csv-importer'); ?></h3>
									</label></th>
								<tr>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="post" />
											<span id="align"><?php echo __('Post', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
									<td class='exportdatatype'>
										<label> <input type="radio" name="export" value="eshop">
											<span id="align"><?php echo __('Eshop', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
								</tr>
								<tr>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="page">
											<span id="align"><?php echo __('Page', 'wp-ultimate-csv-importer'); ?></span>
										</label></td>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="wpcommerce">
											<span id="align"><?php echo __('Wp-Commerce</span', 'wp-ultimate-csv-importer'); ?>
										</label>
									</td>
								</tr>
								<tr>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="custompost">
											<span id="align"><?php echo __('Custom Post', 'wp-ultimate-csv-importer'); ?></span>
										</label>
										<select name="export_post_type" style="margin-left:10px" id="export_post_type">
											<option><?php echo __('--Select--', 'wp-ultimate-csv-importer'); ?></option>
											<?php
											if(is_array(get_post_types())){
											foreach (get_post_types() as $key => $value) {
												if (($value != 'featured_image') && ($value != 'attachment') && ($value != 'wpsc-product') && ($value != 'wpsc-product-file') && ($value != 'revision') && ($value != 'nav_menu_item') && ($value != 'post') && ($value != 'page') && ($value != 'wp-types-group') && ($value != 'wp-types-user-group') && ($value != 'product') && ($value != 'product_variation') && ($value != 'shop_order') && ($value != 'shop_coupon') && ($value != 'acf')) {?>
													<option id="<?php echo($value); ?>"> <?php echo($value); ?> </option>
													<?php
												}
											}
											}
											?>
										</select>
									</td>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="woocommerce">
											<span id="align"><?php echo __('Woo-Commerce', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
								</tr>
								<tr>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="category" />
											<span id="align"><?php echo __('Category', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="marketpress" />
											<span id="align"><?php echo __('Marketpress', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
								</tr>
								<tr>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="tags" />
											<span id="align"><?php echo __('Tags', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="customerreviews" />
											<span id="align"><?php echo __('Customer Reviews', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
								</tr>
								<tr>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="customtaxonomy" />
											<span id="align"><?php echo __('Custom Taxonomy', 'wp-ultimate-csv-importer'); ?></span>
										</label>
										<select name="export_taxo_type" style="margin-left:10px;" id="export_taxo_type">
											<option><?php echo __('--Select--', 'wp-ultimate-csv-importer'); ?></option>
											<?php
											if(is_array(get_taxonomies())){
											foreach (get_taxonomies() as $key => $value) {
												if (($value != 'category') && ($value != 'post_tag') && ($value != 'nav_menu') && ($value != 'link_category') && ($value != 'post_format') && ($value != 'product_tag') && ($value != 'wpsc_product_category') && ($value != 'wpsc-variation')) { ?>
													<option id="<?php echo($value); ?>"> <?php echo($value); ?> </option>
													<?php
												}
											}
											}
											?>
										</select></td>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="comments" />
											<span id="align"><?php echo __('Comments', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
								</tr>
								<tr>
									<td class='exportdatatype'>
										<label>
											<input type="radio" name="export" value="users" />
											<span id="align"><?php echo __('Users', 'wp-ultimate-csv-importer'); ?></span>
										</label>
									</td>
									<td class='exportdatatype'></td>
								</tr>
							</table>
						</div>
						<div class='form-group exportedas'>
							<label class='col-sm-2 control-label'><b><?php echo __('File Name:', 'wp-ultimate-csv-importer'); ?> </b></label>
							<div class='col-sm-6'>
								<input class='form-control' type='text' name='export_filename' id='export_filename' value='' placeholder="export_as_<?php echo(date("Y-m-d")); ?>" size="18" />
							</div>
							<div class='col-sm-3'>
								<input type="submit" id="exportbutton" name="exportbutton" value="<?php echo __('Export', 'wp-ultimate-csv-importer'); ?>" class='btn btn-primary' />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
