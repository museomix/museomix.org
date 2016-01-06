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
?>

<div id="support_con">
	<div style="width:99%;">

		<div class="contactus" id="contactus" style="height:480px">
				<div style='position:relative;left:10%;bottom:10px;width:45%;'>
					<h3><?php echo __('Smackcoders Support', 'wp-ultimate-csv-importer'); ?> </h3><br />

						<a href="https://smackcoders.freshdesk.com/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic" target="_blank">
                                                <img height= "120" width="220"src = 'https://www.wpultimatecsvimporter.com/wp-content/uploads/2015/07/support.png'>
						</a>

					<p style="line-height:20px;padding-top:20px; padding-left:14px;"><?php echo __('Click', 'wp-ultimate-csv-importer');
						echo ' ';
						echo __('here', 'wp-ultimate-csv-importer'); ?> <a
							href="https://www.smackcoders.com/blog/category/web-development-news/?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic"
							target="_blank"> <?php echo __('Recent News', 'wp-ultimate-csv-importer'); ?> </a>
					</p>

					<p style="line-height:20px; padding-left:14px;"><?php echo __('For', 'wp-ultimate-csv-importer'); ?> <a
							href="http://www.youtube.com/user/smackcoders/channels"
							target="_blank"> <?php echo __('Youtube Channel', 'wp-ultimate-csv-importer'); ?> </a></p>

					<p style="line-height:20px; padding-left:14px;"><?php echo __('To Know the detail of', 'wp-ultimate-csv-importer'); ?>
						<a href="https://www.smackcoders.com/store/products-46/wordpress.html?utm_source=WpPlugin&utm_medium=Free&utm_campaign=SupportTraffic"
						   target="_blank"> <?php echo __('Other Plugins', 'wp-ultimate-csv-importer'); ?> </a></p>
				</div>
<!-- For Vedio -->
                    <div style = "position:relative;left:70%;bottom:66%";>
                                <h3 style='padding-left:22px'><?php echo __('Video Walk Through', 'wp-ultimate-csv-importer'); ?></h3>
                        </div>

				<div id = 'data' style = "position:relative;left:65%;bottom:63%";>
				<div id="video">
					<iframe width="560" height="315" src="https://www.youtube.com/embed/c-2wNw61d6s" frameborder="0" allowfullscreen></iframe>
                                </div>
 
			</div>
<!-- End -->

		</div>
	</div>

	<!-- Promotion footer for other useful plugins -->
	<div class="promobox" id="pluginpromo" style="width:99%;">
		<div class="accordion-group">
			<div class="accordion-body in collapse">
				<div>
					<?php $impCE->common_footer_for_other_plugin_promotions(); ?>
				</div>
			</div>
		</div>
		
	</div>
</div>
