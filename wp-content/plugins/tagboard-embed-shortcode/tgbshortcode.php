<?php
/**
 * Plugin Name: Tagboard Embed Shortcode
 * Plugin URI: http://tagboard.com
 * Description: A simple way to embed your tagboard into your Wordpress blog. It uses a shortcode and supports embedding into Pages/Posts/Widgets.
 * Version: 0.2
 * Author: Jordan Larrigan
 * Author URI: http://tagboard.com
 * License: GPLv3 or later
 */

add_action( 'admin_menu', 'tagboard_menu' );

function tagboard_menu() {
	add_options_page( 'Tagboard FAQ', 'Tagboard FAQ', 'manage_options', 'tgbshortcode', 'tagboard_plugin_options' );
}

function tagboard_shortcode($atts) {
   extract(shortcode_atts(array(
      'id' => "WhyWorkAtTagboard/146433",
      'postlimit' => "50",
      'mobilelimit' => "10",
      'darkmode' => false,
      'fixedheight' => false,
      'autoLoad' => false,
   ), $atts));
	return '<div id="tagboard-embed"></div>
			<script>var tagboardOptions = {tagboard:"'.$id.'",postCount: "'.$postlimit.'",mobilePostCount:"'.$mobilelimit.'",darkMode:"'.$darkmode.'",fixedHeight:"'.$fixedheight.'",autoLoad:"'.$autoLoad.'"};</script>
			<script src="https://tagboard.com/public/js/embed.js"></script>';
}

add_shortcode('tagboard', 'tagboard_shortcode');

// Add settings link on plugin page
function tgb_faq_link($links) { 
  $settings_link = '<a href="options-general.php?page=tgbshortcode">FAQ</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'tgb_faq_link' );


function tagboard_plugin_options() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<style>
		pre { border:1px dashed #E1E1E1; color:#333344; background:#FAFAFA; font-family:monospace; overflow:auto; font-size:12px; padding:0.5em; }
	</style>
	<div class="wrap">
		<img style="width:200px; margin-top:20px; margin-bottom:20px;" src="https://static.tagboard.com/public/img/tagboard.svg" alt="Tagboard.com">
		<h3>Basic Usage</h3>
		<div>Requirements: You must have a Tagboard account with embedding enabled. <a href="http://tagboard.com">http://tagboard.com</a></div><br/>
		<div>You should be able to use the shortcode in a widget, page or post by simply using the shortcode with the required id parameter. This parameter is the unique ID for your tagboard. This can be found on the Embed tab in your tagboard’s settings page. The resulting shortcode should look something like this.  </div>
		<pre>[tagboard id="WhyWorkAtTagboard/146433"]</pre>
		<h3>Advanced Usage</h3>
		<div>You are able to pass the advanced embed options via your shortcode. See the example below for the list of options you can choose from.</div>	
		<pre>[tagboard id="WhyWorkAtTagboard/146433" postlimit="10" mobilelimit="5" darkmode="true" fixedheight="true"]</pre>
		<h3>Defaults</h3>
		<div>You have the ability to choose the default embed settings by simply not adding them to your shortcode. These are the defaults.</div>
		<pre>
postlimit="50" 
mobilelimit="10" 
darkmode="false" 
fixedheight="false"</pre>
		<div>For additional help or other questions about Tagboard please check out <a href="https://tagboard.com" target="_blank">Tagboard.com</a> and our <a href="http://support.tagboard.com" target="_blank">support site</a> </div>
	</div>
<?php } ?>