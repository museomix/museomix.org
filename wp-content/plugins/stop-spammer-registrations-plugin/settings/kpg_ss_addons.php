<?php
/*
	Stop Spammers Plugin 
	Options Setup Page for MU switch
	
*/
if (!defined('ABSPATH')) exit; // just in case

if(!current_user_can('manage_options')) {
	die('Access Denied');
}
?>

<div class="wrap">
<p>Programmers may write Add-Ons that extend this plugin.</p>
<p>There are three sample addons available at <a href="http://www.blogseye.com/beta-test-plugins/" target="_blank">Blogseye.com</a>. The addon for Red Herring Forms, especially, seems to stop a lot of spam. I will be moving it to a premium site, soon, so download it now before it is gone.</p>
<p>Add-ons are plugins that hook into the Stop Spammer plugin to add functionality.<p>
<p>I have written a sample addon plugin that checks for Tor Exit points available on my site. This catches a couple of hits a day on my site.</p>
<p>I have written several premium add-ons that will be available. 
They extend reporting, allow updating of .htaccess files ip deny entries, and blocking certain types of exploit attempts not covered by the basic plugin.
I will provide links to these add-ons as soon as the plugin is complete and stable.</p> 
<p>Some of the addons that I am working on:<br>
Reporting: This is an addon that collects all data on spam in a SQL tables and produces reports and graphs.<br>
Export to Excel: Exports collected spam data to Excel<br>
Red Herring Forms: I removed red herring forms from the plugin because they clashed with many themes. This will allow users to install a red herring form and honey pot links.<br>
Scan for Spammers: Scan registered users for spam. This will run on a cron job in the background and produce a report.</p>
<p>I am researching using a digital sales plugin, or a pay site like Sellfy, CodeCanyon or WpEden for selling these addons.
</p>
<?php
// get a list of all the addons using the filter
$addons=array();
$a1=apply_filters('kpg_ss_addons_allow',$addons);
$a3=apply_filters('kpg_ss_addons_deny',$addons);
$a5=apply_filters('kpg_ss_addons_get',$addons);
$addons=array_merge($a1,$a3,$a5);
if (empty($addons)) {
	echo "No addons installed<br";
} else {
?>
<fieldset style="border:thin solid black;padding:6px;width:100%;">
<legend><span style="font-weight:bold;font-size:1.2em" >Installed Addons</span></legend>
<ol>
<?php
	foreach($addons as $add) {
	$ad0=$add[0];
	$ad1=$add[1];
	$ad2=$add[2];
	$ad3=$add[3];
	$reason=be_load($add,$ad1);
	echo "<li>$ad1: by $ad2, $ad3</li>";
	
	}
?>
</ol>
</fieldset>
<?php
}
?>
</div>
