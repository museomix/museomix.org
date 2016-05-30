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

$siteurl = get_option('siteurl');
$noncedata = $skinnyData['wp_nonce'];
$impCE = new WPImporter_includes_helper();
?>
<div class="accordion" id="accordion2" style='width:98%;' xmlns="http://www.w3.org/1999/html">
                        <div class="accordion-group">
                           <div id="collapseTwo" class="accordion-body in collapse">
				<span style="margin: 4% 0px 4% 22%; color: red; font-weight: bold; text-align: center;" name="warning" id="warning" ><p><?php echo esc_html__('Check','wp-ultimate-csv-importer');?> <a href='<?php echo $siteurl?>/wp-admin/admin.php?page=wp-ultimate-csv-importer/index.php&__module=settings' target ='blank'> <?php echo esc_html__('here','wp-ultimate-csv-importer'); ?> </a> <?php echo esc_html__('your system configuration before proceeding the export. It may help to prevent from facing server configuration issues.','wp-ultimate-csv-importer');?> </span><span style='position:relative;left:4px;'></span></p></span>
                                        <div class="accordion-inner">
<div>
        <label><h3 id="exporttitle"><?php echo esc_html__('Export Data With Advanced Filters','wp-ultimate-csv-importer');?></h3></label>
</div>
<div style="margin-left:20px;">
	<?php if(!isset($_REQUEST['step'])) { ?>
	<form class="form-horizontal" method="post" name="exportmodule" id="exportmodule" action='<?php echo esc_url(add_query_arg(array('page'=> WP_CONST_ULTIMATE_CSV_IMP_SLUG . '/index.php','__module' => sanitize_text_field($_REQUEST['__module']),'step'=>'exportfilters'), $impCE->baseUrl))?>' onsubmit="return export_module();" >
		<div id="exportaspecificmodule">
			<div class="table-responsive" id="exporttable">
				<table class='table exportmodule'>
					<th colspan='2'><label class='h-exportmodule'><h3 id="innertitle"><?php echo esc_html__('Select your module to export the data','wp-ultimate-csv-importer')?> </h3></label></th>
					<tr>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="post"><span id="align"><?php echo esc_html__('Post','wp-ultimate-csv-importer'); ?></span> </label></td>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="eshop"><span id="align"><?php echo esc_html__('Eshop','wp-ultimate-csv-importer'); ?></span> </label></td>
					</tr>
					<tr>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="page"><span id="align"> <?php echo esc_html__('Page','wp-ultimate-csv-importer'); ?></span> </label></td>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="wpcommerce"><span id="align"> <?php echo esc_html__('Wp-Commerce','wp-ultimate-csv-importer'); ?></span></label></td>
					</tr>
					<tr>
						<td class='exportdatatype'>
							<label> <input type="radio" name="export" value="custompost"><span id="align"> <?php echo esc_html__('Custom Post','wp-ultimate-csv-importer'); ?></span></label>
							<select name="export_post_type" id="export_post_type" style="margin-left:10px">
								<option><?php echo esc_html__('--Select--','wp-ultimate-csv-importer'); ?></option>
								<?php
								foreach (get_post_types() as $key => $value) {
									if (($value !== 'featured_image') && ($value !== 'attachment') && ($value !== 'wpscduct') && ($value !== 'wpscduct-file') && ($value !== 'revision') && ($value !== 'nav_menu_item') && ($value !== 'post') && ($value !== 'page') && ($value !== 'wp-types-group') && ($value !== 'wp-types-user-group') && ($value !== 'product') && ($value !== 'product_variation') && ($value !== 'shop_order') && ($value !== 'shop_coupon') && ($value !== 'acf') && ($value !== 'acf-field') && ($value !== 'acf-field-group') && ($value !== '_pods_pod') && ($value !== '_pods_field') && ($value !== 'shop_order_refund') && ($value !== 'shop_webhook')) {
										?>
										<option id="<?php echo($value); ?>"> <?php echo($value); ?> </option>
										<?php
									}
								}
								?>
							</select>
						</td>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="woocommerce"><span id="align"> <?php echo esc_html__('Woo-Commerce','wp-ultimate-csv-importer'); ?></span></label></td>
					</tr>
					<tr>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="categories"><span id="align"> <?php echo esc_html__('Category','wp-ultimate-csv-importer'); ?></span></label></td>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="marketpress"><span id="align"> <?php echo esc_html__('Marketpress','wp-ultimate-csv-importer'); ?></span></label></td>
					</tr>
					<tr>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="tags"><span id="align"> <?php echo esc_html__('Tags','wp-ultimate-csv-importer'); ?> </label></span></td>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="customerreviews"><span id="align"> <?php echo esc_html__('Customer Reviews','wp-ultimate-csv-importer'); ?></span></label></td>
					</tr>
					<tr>
						<td class='exportdatatype'>
							<label> <input type="radio" name="export" value="customtaxonomy"><span id="align"> <?php echo esc_html__('Custom Taxonomy','wp-ultimate-csv-importer'); ?></span></label>
							<select name="export_taxo_type" id="export_taxo_type" style="margin-left:10px;">
								<option><?php echo esc_html__('--Select--','wp-ultimate-csv-importer'); ?></option>
								<?php
								foreach (get_taxonomies() as $key => $value) {
									if (($value !== 'category') && ($value !== 'post_tag') && ($value !== 'nav_menu') && ($value !== 'link_category') && ($value !== 'post_format') && ($value !== 'product_tag') && ($value !== 'wpsc_product_category') && ($value !== 'wpsc-variation')) {
										?>
										<option id="<?php echo($value); ?>"> <?php echo($value); ?> </option>
										<?php
									}
								}
								?>
							</select></td>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="comments"><span id="align"> <?php echo esc_html__('Comments','wp-ultimate-csv-importer'); ?></span></label></td>
					</tr>
					<tr>
						<td class='exportdatatype'><label> <input type="radio" name="export" value="users"><span id="align"> <?php echo esc_html__('Users','wp-ultimate-csv-importer'); ?></span></label></td>
						<td class='exportdatatype'></td>
					</tr>
				</table>
				<div class='' style="padding: 15px;float: right;"><input type="submit" name="proceedtoexclusion" id="proceedtoexclusion" value="<?php echo esc_html__('Proceed','wp-ultimate-csv-importer');?>" class='btn btn-success'></div>
			</div>
		</div>
	</form>
</div>
<?php } ?>
	<!-- Export Module Filter and Exclusion list -->
	<?php if(isset($_REQUEST['step']) && sanitize_text_field($_REQUEST['step']) === 'exportfilters') { ?>
	<?php $export_type = isset($_REQUEST['export']) ? sanitize_text_field($_REQUEST['export']) : '' ;?>
	<div id="exportmodulefilter" style="">
	<div class="table-responsive">
        <form name="exportbasedonfilters" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>" >
        <?php wp_nonce_field('parseDataToExport','smack_nonce_key'); ?>
	<table style='width:100%;' class='table exportmodule'>
	<th colspan='2'><label class='h-exportmodule'><h3 id="innertitle"> <?php echo esc_html__('To export data based on the filters','wp-ultimate-csv-importer'); ?></h3></label></th>
	<tr>
        <td><label><input type='checkbox' name='getdatawithdelimiter' id='getdatawithdelimiter' value='getdatawithdelimiter' onclick='addexportfilter(this.id);' /><span id="align"> <?php echo esc_html__('Export data with auto delimiters');?></span></label>
        <div id='delimiterstatus' style='padding:15px;margin-left:24px;display:none;'>
        <label id='delistatus'><b> Delimiters </b></label>
        <select name='postwithdelimiter' id='postwithdelimiter' style = 'margin-left:5px;margin-top:-7px;'>
	<option>Select</option>
        <option>,</option>
        <option>:</option>
        <option>;</option>
        <option>{Tab}</option>
        <option>{Space}</option>
        </select>
	<label><b>Other Delimiters</b> </label><input type = 'text' name='other_delimiter' id ='other_delimiter' size=6>
        </div>
        </td>
        </tr>
	<?php if($export_type !== 'categories' && $export_type !== 'tags' && $export_type !== 'customtaxonomy') {?>
	<tr>
	<td><label><input type='checkbox' name='getdataforspecificperiod' id='getdataforspecificperiod' value='getdataforspecificperiod' onclick='addexportfilter(this.id);' /><span id="align"> <?php echo esc_html__('Export data for the specific period','wp-ultimate-csv-importer');?></span></label>
	<div id='specificperiodexport' style='padding:10px;margin-left:29px;display:none;'>
	<label id='periodstartfrom'><b> <?php echo esc_html__('Start From','wp-ultimate-csv-importer');?> </b></label>
	<input type='text' class='form-control' name='postdatefrom' style='cursor:default;width:25%;' readonly id='postdatefrom' value='' onchange='validateDateIntervals();' />
        <label id='periodendto'><b><?php echo esc_html__('End To','wp-ultimate-csv-importer');?> </b></label>
        <input type='text' class='form-control' name='postdateto' style='cursor:default;width:25%;' readonly id='postdateto' value='' onchange='validateDateIntervals();'/>
	</div>
	</td>
        </tr>
	<?php }?>
	<?php if($export_type !== 'users' && $export_type !== 'categories' && $export_type !== 'tags' && $export_type !== 'customtaxonomy' && $export_type !== 'customerreviews' && $export_type !== 'comments') {?>
	<tr>
	<td><label><input type='checkbox' name='getdatawithspecificstatus' id='getdatawithspecificstatus' value='getdatawithspecificstatus' onclick='addexportfilter(this.id);' /><span id="align"> <?php echo esc_html__('Export data with the specific status','wp-ultimate-csv-importer');?></span></label>
        <div id='specificstatusexport' style='padding:15px;margin-left:24px;display:none;'>
	<label id='status'><b> <?php echo esc_html__('Status','wp-ultimate-csv-importer'); ?> </b></label>
	<select name='specific_status' id='specific_status' style = 'margin-left:5px;margin-top:-7px;'>
	<option><?php echo esc_html__('All','wp-ultimate-csv-importer'); ?></option>
        <option><?php echo esc_html__('Publish','wp-ultimate-csv-importer'); ?></option>
        <option><?php echo esc_html__('Sticky','wp-ultimate-csv-importer'); ?></option>
        <option><?php echo esc_html__('Private','wp-ultimate-csv-importer'); ?></option>
        <option><?php echo esc_html__('Protected','wp-ultimate-csv-importer'); ?></option>
        <option><?php echo esc_html__('Draft','wp-ultimate-csv-importer'); ?></option>
        <option><?php echo esc_html__('Pending','wp-ultimate-csv-importer'); ?></option>
	</select>
	</div>
	</td>
	</tr>
	<?php }
	if($export_type !== 'users' && $export_type !== 'categories' && $export_type !== 'tags' && $export_type !== 'customtaxonomy' && $export_type !== 'customerreviews') {?>
	<tr>
	<td><label><input type='checkbox' name='getdatabyspecificauthors' id='getdatabyspecificauthors' value='getdatabyspecificauthors' onclick='addexportfilter(this.id);' /><span id="align"> <?php echo esc_html__('Export data by specific authors','wp-ultimate-csv-importer');?></span></label>
	<div id='specificauthorexport' style='padding:15px;margin-left:24px;display:none;'>
        <label id='authors'><b> <?php echo esc_html__('Authors','wp-ultimate-csv-importer'); ?> </b></label>
	<?php $blogusers = get_users( 'blog_id=1&orderby=nicename' ); ?>
	<select name='specific_authors' id='specific_authors' style = 'margin-left:5px;margin-top:-7px;'>
        <option value='0'><?php echo esc_html__('All','wp-ultimate-csv-importer'); ?></option>
	<?php foreach( $blogusers as $user ) { ?>
		<option value='<?php echo esc_html( $user->ID ); ?>'> <?php echo esc_html( $user->display_name ); ?> </option>
	<?php } ?>
	</select>
	</div>
	</td>
	</tr>
	<?php } ?>
	<tr>
	<td><!--<label><input type='checkbox' name='getdatabasedonexclusions' id='getdatabasedonexclusions' value='getdatabasedonexclusions' onclick='addexportfilter(this.id);' /><span id="align"> <?php echo esc_html__('Export data based on specific exclusions','wp-ultimate-csv-importer');?> </span></label>-->
	<div id="exclusiongrouplist" style="display:none;">
	<?php
	#print('<pre>'); print_r($skinnyData); print('</pre>'); #die;
	if(isset($_REQUEST['export_post_type']) && $_REQUEST['export_post_type'] != '--Select--') {
		$exportAs = sanitize_text_field($_REQUEST['export_post_type']);
	} elseif(isset($_REQUEST['export_taxo_type']) && $_REQUEST['export_taxo_type'] != '--Select--') {
		$exportAs = sanitize_text_field($_REQUEST['export_taxo_type']);
	} else {
		$exportAs = '';
	}
	?>
	<div class="panel-group" id="accordion" style = "width:98.3%;margin-top:-5px;padding-bottom: 20px;">
	<input type ='hidden' name='moduletobeexport' id ='moduletobeexport' value='<?php echo isset($_POST['export']) ? sanitize_text_field($_POST['export']) : '' ;?>' >
	<input type='hidden' name='export_cust_type' id='export_cust_type' value='<?php echo $exportAs; ?>' />
	<input type='hidden' name='export_taxo_type' id='export_taxo_type' value='<?php echo $exportAs; ?>' />
	<input type="hidden" name="optional_type" id="optional_type" value="<?php echo $exportAs; ?>" >
	<input type="hidden" name="offset" id="offset" value="0" >
	<input type="hidden" name="limit" id="limit" value="10" >
	<input type="hidden" name="total_row_count" id="total_row_count" value="" >
		<?php $available_groups = isset($skinnyData['available_groups']) ? $skinnyData['available_groups'] : array() ;
		$classifyObj = new WPClassifyFields();
		$get_export_exclusions = array();
		update_option('wp_ultimate_csv_importer_export_exclusion',$get_export_exclusions);
		$cvallabel = '';
		$node = 1; ?>
		<?php if(in_array('CORE', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Core_Fields" data-parent="#accordion">
					<div class="panel-title">
						<b> <?php echo esc_html__("WordPress Fields: ",'wp-ultimate-csv-importer'); ?></b>
						<span class = 'fa fa-toggle-up pull-right' id = 'Core_Fields_h_span'> </span>
					</div>
				</div>
				<div id="Core_Fields" class="panel-collapse collapse in" style="height:auto;">
					<div class="grouptitlecontent" id="corefields_content" style="height:250px;">
						<div style="padding-top:5px">
		   <span style="margin-left:800px;">
                      	<a id="check" onclick="exportselectall(this.id,'CORE');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'CORE');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
              	  </span>
						</div>

						<?php
						$get_core_fields = $classifyObj->WPCoreFields($skinnyData['export_type']);
						$set_group_height = 0;
						if(is_array($get_core_fields) && !empty($get_core_fields['CORE'])) {
							$fields_count = count($get_core_fields['CORE']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['CORE'] as $ckey => $cval) {
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'],0,19) . '..';
								}else
									$cvalname = $cval['name'];
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title = '{$cval['label']}'><input type='checkbox' class='CORE_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);'/>".$cvallabel."<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#if($j == $set_row_count)
							echo "</div>";
							if($skinnyData['export_type'] === 'users'){
								$set_group_height = $set_row_count * 45;
								$set_group_height = "$set_group_height" +"100"."px";
							}
							else {
								$set_group_height = $set_row_count * 35;
								$set_group_height = "$set_group_height" +"100"."px";
							}
						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>"; echo esc_html__('No fields Found!','wp-ultimate-csv-importer'); echo "</p>";
							$set_group_height = 'auto';
						}
						?>

					</div>
					<script type="text/javascript">
						document.getElementById('corefields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('CCTM', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Cctm_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("CCTM Custom Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Cctm_Fields_h_span'> </span> </div>
				</div>
				<div id="Cctm_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="cctmfields_content">
						<div>
                   <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'CCTM');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'CCTM');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                   </span>
						</div>
						<?php
						$get_core_fields = array();
						$get_core_fields = $classifyObj->CCTMCustomFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['CCTM'])) {
							$fields_count = count($get_core_fields['CCTM']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['CCTM'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title ='{$cval['label']}'><input type='checkbox' class='CCTM_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"10"."px";
						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>";echo esc_html__('No fields Found!','wp-ultimate-csv-importer');echo "</p>";
							$set_group_height = 'auto';
						}?>
					</div>
					<script type="text/javascript">
						document.getElementById('cctmfields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('ACF', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Acf_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("ACF Custom Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Acf_Fields_h_span'> </span> </div>
				</div>
				<div id="Acf_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="acffields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'ACF');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'ACF');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->ACFCustomFields();
						#var_dump($get_core_fields); #die;
						if(is_array($get_core_fields) && !empty($get_core_fields['ACF'])) {
							$fields_count = count($get_core_fields['ACF']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['ACF'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20)
									$cvalname = substr($cval['name'],0,19). '..';
								else
									$cvalname = $cval['name'];
								echo "<span class='col-sm-3 exclusion-list'><label title ='{$cval['label']}'><input type='checkbox' class='ACF_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							# if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";
						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>";echo esc_html__('No fields Found!','wp-ultimate-csv-importer');echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('acffields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('RF', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Rf_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("ACF Repeater Custom Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Rf_Fields_h_span'> </span> </div>
				</div>
				<div id="Rf_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="repeaterfields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'RF');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'RF');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->ACFCustomFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['RF'])) {
							$fields_count = count($get_core_fields['RF']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['RF'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title='{$cval['label']}'><input type='checkbox' class='RF_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";

						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>";echo esc_html__('No fields Found!','wp-ultimate-csv-importer');echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('repeaterfields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('TYPES', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Types_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("Types Custom Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Types_Fields_h_span'> </span> </div>
				</div>
				<div id="Types_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="typesfields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'TYPES');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'TYPES');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer') ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->TypesCustomFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['TYPES'])) {
							$fields_count = count($get_core_fields['TYPES']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['TYPES'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title = '{$cval['label']}'><input type='checkbox' class='TYPES_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";
						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>"; echo esc_html__('No fields Found!','wp-ultimate-csv-importer'); echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('typesfields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('PODS', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Pods_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("PODS Custom Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Pods_Fields_h_span'> </span> </div>
				</div>
				<div id="Pods_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="podsfields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'PODS');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'PODS');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->PODSCustomFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['PODS'])) {
							$fields_count = count($get_core_fields['PODS']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['PODS'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title='{$cval['label']}'><input type='checkbox' class='PODS_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";
						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>";echo esc_html__('No fields Found!','wp-ultimate-csv-importer');echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('podsfields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('AIOSEO', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Aioseo_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("All in One SEO Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Aioseo_Fields_h_span'> </span> </div>
				</div>
				<div id="Aioseo_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="aioseofields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'AIOSEO');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'AIOSEO');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->aioseoFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['AIOSEO'])) {
							$fields_count = count($get_core_fields['AIOSEO']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['AIOSEO'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title='{$cval['label']}'><input type='checkbox' class='AIOSEO_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";

						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>No fields Found!</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('aioseofields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('YOASTSEO', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Yoastseo_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("WordPress Yoast SEO Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Yoastseo_Fields_h_span'> </span> </div>
				</div>
				<div id="Yoastseo_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="yoastseofields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'YOASTSEO');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'YOASTSEO');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->yoastseoFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['YOASTSEO'])) {
							$fields_count = count($get_core_fields['YOASTSEO']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['YOASTSEO'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title='{$cval['label']}'><input type='checkbox' class='YOASTSEO_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";
						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>";echo esc_html__('No fields Found!','wp-ultimate-csv-importer'); echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('yoastseofields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('CORECUSTFIELDS', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Othercustom_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("Other WP Custom Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Othercustom_Fields_h_span'> </span> </div>
				</div>
				<div id="Othercustom_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="othercustomfields_content">
						<div>
               <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'CORECUSTFIELDS');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'CORECUSTFIELDS');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?> </a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->commonMetaFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['CORECUSTFIELDS'])) {
							$fields_count = count($get_core_fields['CORECUSTFIELDS']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['CORECUSTFIELDS'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title = '{$cval['label']}' ><input type='checkbox' class='CORECUSTFIELDS_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#            if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 50;
							$set_group_height = "$set_group_height" +"100"."px";

						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>"; echo esc_html__('No fields Found!','wp-ultimate-csv-importer'); echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('othercustomfields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('WPMEMBERS', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Wpmember_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("WP Member Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Wpmember_Fields_h_span'> </span> </div>
				</div>
				<div id="Wpmember_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="wpmemberfields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'WPMEMBERS');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'WPMEMBERS');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->wpmembersFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['WPMEMBERS'])) {
							$fields_count = count($get_core_fields['WPMEMBERS']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['WPMEMBERS'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title='{$cval['label']}'><input type='checkbox' class='WPMEMBERS_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#            if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";

						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>";echo esc_html__('No fields Found!','wp-ultimate-csv-importer');echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('wpmemberfields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('ECOMMETA', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Ecommeta_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("Ecommerce Meta Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Ecommeta_Fields_h_span'> </span> </div>
				</div>
				<div id="Ecommeta_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="ecommetafields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'ECOMMETA');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'ECOMMETA');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->ecommerceMetaFields($skinnyData['export_type']);
						if(is_array($get_core_fields) && !empty($get_core_fields['ECOMMETA'])) {
							$fields_count = count($get_core_fields['ECOMMETA']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['ECOMMETA'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title='{$cval['label']}'><input type='checkbox' class='ECOMMETA_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#            if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 50;
							$set_group_height = "$set_group_height" +"100"."px";

						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>"; echo esc_html__('No fields Found!','wp-ultimate-csv-importer'); echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('ecommetafields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('WPECOMMETA', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Wpecomcust_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("WP-eCommerce Custom Fields: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Wpecomcust_Fields_h_span'> </span> </div>
				</div>
				<div id="Wpecomcust_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="wpecomcustfields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'WPECOMMETA');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'WPECOMMETA');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer'); ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->wpecommerceCustomFields();
						if(is_array($get_core_fields) && !empty($get_core_fields['WPECOMMETA'])) {
							$fields_count = count($get_core_fields['WPECOMMETA']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['WPECOMMETA'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title='{$cval['label']}'><input type='checkbox' class='WPECOMMETA_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#            if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";

						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>"; echo esc_html__('No fields Found!','wp-ultimate-csv-importer'); echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('wpecomcustfields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>
		<?php if(in_array('TERMS', $available_groups)) { ?>
			<div class="panel panel-default">
				<div class="panel-heading" data-toggle="collapse" data-target="#Termtaxos_Fields" data-parent="#accordion">
					<div class="panel-title"> <b> <?php echo esc_html__("Terms & Taxonomies: ",'wp-ultimate-csv-importer'); ?></b> <span class = 'fa fa-toggle-down pull-right'id = 'Termtaxos_Fields_h_span'> </span> </div>
				</div>
				<div id="Termtaxos_Fields" class="panel-collapse collapse" style="height:auto;">
					<div class="grouptitlecontent " id="termtaxofields_content">
						<div>
                <span style="margin-left:800px;">
                        <a id="check" onclick="exportselectall(this.id,'TERMS');" value="Check All"><?php echo esc_html__('Check All','wp-ultimate-csv-importer'); ?></a></span>
                        <span style="margin-left:1px;">
                        <a id="uncheck" onclick="exportselectall(this.id,'TERMS');" value="Un Check All "> <?php echo esc_html__('/ Uncheck All','wp-ultimate-csv-importer') ?></a>
                </span>
						</div>
						<?php
						$get_core_fields = $classifyObj->termsandtaxos($skinnyData['export_type']);
						if(is_array($get_core_fields) && !empty($get_core_fields['TERMS'])) {
							$fields_count = count($get_core_fields['TERMS']);
							$set_row_count = ceil( $fields_count / 4 );
							$i=1; $j=1;
							echo "<div>";
							foreach($get_core_fields['TERMS'] as $ckey => $cval) {
								if(strlen($cval['label']) > 26){
									$cvallabel = substr($cval['label'], 0, 25) . '..';
								}else{
									$cvallabel = $cval['label'];
								}
								if(strlen($cval['name']) > 20){
									$cvalname = substr($cval['name'], 0, 19) . '..';
								}else{
									$cvalname = $cval['name'];
								}
								echo "<span class='col-sm-3 exclusion-list'><label title='{$cval['label']}'><input type='checkbox' class='TERMS_class' name='".$cval['name']."' id='column".$node."' onclick='exportexclusion(this.name, this.id);' />" . $cvallabel . "<label title = '{$cval['name']}' class = 'samptxt' style='margin-left: 27px;'>[ ".$cvalname." ]</label></label></span>";
								if(ceil($i % 4) == 0) {
									if($j <= $set_row_count) {
										echo "</div><div>";
									} else {
										echo "</div>";
									}
									$j++;
								}
								$i++;
								$node++;
							}
							#if($j == $set_row_count)
							echo "</div>";
							$set_group_height = $set_row_count * 45;
							$set_group_height = "$set_group_height" +"100"."px";
						} else {
							echo "<p style='color:red;text-align:center;padding:20px;'>"; echo esc_html__('No fields Found!','wp-ultimate-csv-importer'); echo "</p>";
							$set_group_height = 'auto';
						}
						?>
					</div>
					<script type="text/javascript">
						document.getElementById('termtaxofields_content').style.height = '<?php echo $set_group_height; ?>';
					</script>
				</div>
			</div>
		<?php } ?>

	</div>
	</div>
	</td>
	</tr>
	</table>
	<script type = 'text/javascript'>
		jQuery(document).ready(function() {
			jQuery('#postdatefrom').datepicker({
				dateFormat : 'yy-mm-dd'
			});
			jQuery('#postdateto').datepicker({
                                dateFormat : 'yy-mm-dd'
                        });
		});
	</script>
	<div class='form-group exportedas'>
	<label class='col-sm-2 control-label'><b><?php echo esc_html__('File Name: ','wp-ultimate-csv-importer'); ?></b></label>
	<div class='col-sm-6'>
	<input class='form-control' type='text' name='export_filename' id='export_filename' value='' placeholder="export_as_<?php echo(date("Y-m-d")); ?>" size="18" style="margin-left:-67px;margin-top:-8px;">
	</div>
	<a id="download_file_link" href="" target="_blank" class="col-sm-4" style="margin-top: -10px; display: none;"> <input type="button" name="download_file" id="download_file" class="btn btn-primary" style="display: none;" value="Download"></a>
	</div>
	<div style="padding:15px;width:100% !important;" class='col-sm-3'>
	<input type='hidden' name='export' id='export' value='<?php if(isset($_POST['export'])){ echo sanitize_text_field($_POST['export']);} ?>' />
	<?php if(isset($skinnyData['export_cpt_type'])) {
		$export_cpt_type = $skinnyData['export_cpt_type'];
	} else {
		$export_cpt_type = '';
	}
	if(isset($skinnyData['export_custtaxo_type'])) {
		$export_custtaxo_type = $skinnyData['export_custtaxo_type'];
	} else {
		$export_custtaxo_type = '';
	} ?>

	<input type='hidden' name='export_cpt_type' id='export_cpt_type' value='<?php echo $export_cpt_type; ?>' />
	<input type='hidden' name='export_custtaxo_type' id='export_custtaxo_type' value='<?php echo $export_custtaxo_type; ?>' />
    <input type="button" name="backtomodulechooser" id="backtomodulechooser" value="<?php echo esc_attr__('<< Back');?>" class='btn btn-danger' onclick="window.location.href = '<?php echo admin_url() . 'admin.php?page=' . WP_CONST_ULTIMATE_CSV_IMP_SLUG . '/index.php&__module=' . $_REQUEST['__module']  ?>'" style="float:left;">
    <input type="button" name="proceed_to_export" id="proceed_to_export" value="<?php echo esc_attr__('Export','wp-ultimate-csv-importer');?>" class='btn btn-success' style="float:right;" onclick="igniteExport();">
	</div>
	</form>
	</div>
	</div>
	<?php } ?>
	<!-- Export Filters with exclusions ends here -->
	</div>
	</div>
	</div>
	</div>
<script type = 'text/javascript'>
jQuery(document).ready(function()
{
        jQuery('#Core_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Core_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });

        jQuery('#Core_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Core_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });

        jQuery('#Cctm_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Cctm_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });

        jQuery('#Cctm_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Cctm_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });

        jQuery('#Types_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Types_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });

        jQuery('#Types_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Types_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });

        jQuery('#Acf_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Acf_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
	 jQuery('#Acf_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Acf_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
	jQuery('#Rf_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Rf_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Rf_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Rf_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
	jQuery('#Pods_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Pods_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Pods_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Pods_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
	jQuery('#Aioseo_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Aioseo_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Aioseo_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Aioseo_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
	jQuery('#Yoastseo_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Yoastseo_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Yoastseo_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Yoastseo_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
	jQuery('#Wpmember_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Wpmember_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Wpmember_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Wpmember_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
	jQuery('#Termtaxos_Fields').on('hidmen.bs.collapse', function ()
        {
                jQuery("#Termtaxos_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Termtaxos_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Termtaxos_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
	jQuery('#Wpecomcust_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Wpecomcust_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Wpecomcust_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Wpecomcust_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
        jQuery('#Ecommeta_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Ecommeta_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Ecommeta_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Ecommeta_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });
	jQuery('#Othercustom_Fields').on('hidden.bs.collapse', function ()
        {
                jQuery("#Othercustom_Fields_h_span").addClass('fa-toggle-down').removeClass('fa-toggle-up');
        });
         jQuery('#Othercustom_Fields').on('show.bs.collapse', function ()
        {
                jQuery("#Othercustom_Fields_h_span").addClass('fa-toggle-up').removeClass('fa-toggle-down');
        });

});
</script>
<script>
	jQuery(function(){
		var current_effect = jQuery('#waitMe_ex_effect').val();
		jQuery('#proceed_to_export').click(function(){
			run_waitMe(current_effect);
		});
		jQuery('#waitMe_ex_close').click(function(){
			jQuery('#wpwrap').waitMe('hide');
		});
		jQuery('#waitMe_ex_effect').change(function(){
			current_effect = jQuery(this).val();
			run_waitMe(current_effect);
		});
		jQuery('#waitMe_ex_effect').click(function(){
			current_effect = jQuery(this).val();
		});
		function run_waitMe(effect){
			jQuery('#wpwrap').waitMe({
				effect: effect,
				text: 'Please wait, Your export is in progress...',
				bg: 'rgba(255,255,255,0.7)',
				color: '#47A447',
				maxSize: '',
				source: 'img.svg',
				onClose: function() {}
			});
		}
		var current_body_effect = jQuery('#waitMe_ex_body_effect').val();
		jQuery('#waitMe_ex_body').click(function(){
			run_waitMe_body(current_body_effect);
		});
		jQuery('#waitMe_ex_body_effect').change(function(){
			current_body_effect = jQuery(this).val();
			run_waitMe_body(current_body_effect);
		});
		function run_waitMe_body(effect){
			jQuery('body').addClass('waitMe_body');
			var img = '';
			var text = '';
			if(effect == 'img'){
				img = 'background:url(\'img.svg\')';
			} else if(effect == 'text'){
				text = 'Loading...';
			}
			var elem = jQuery('<div class="waitMe_container ' + effect + '"><div style="' + img + '">' + text + '</div></div>');
			jQuery('body').prepend(elem);
			setTimeout(function(){
				jQuery('body.waitMe_body').addClass('hideMe');
				setTimeout(function(){
					jQuery('body.waitMe_body').find('.waitMe_container:not([data-waitme_id])').remove();
					jQuery('body.waitMe_body').removeClass('waitMe_body hideMe');
				},200);
			},4000);
		}
	});
</script>
