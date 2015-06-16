<?php
/*
	Stop Spammers Plugin 
	Options Setup Page
	stop-spam-reg-options.php
*/
if (!defined('ABSPATH')) exit; // just in case

if(!current_user_can('manage_options')) {
	die('Access Denied');
}

sfs_errorsonoff();

?>

<div class="wrap">
  <h2>Stop Spammers Plugin Options</h2>
  <?php
global $sfs_check_activation;
$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
$options=kpg_sp_get_options();
extract($options);



$muswitch='N';
if (function_exists('is_multisite') && is_multisite()) {
	$muswitch=get_option('kpg_muswitch');
	if (empty($muswitch)) $muswitch='Y';
	if ($muswitch!='N') $muswitch='Y';
}

// see if we are here for the first time
	$sname=$_SERVER["REQUEST_URI"];	
	if (empty($sname)) {
		$sname='';
	}
	if (empty($sname)) {
		// doesn't work on this machine
	} else if (empty($options_link)) { 
		$options_link=$sname;
		$options['options_link']=$options_link;
		update_option('kpg_stop_sp_reg_options',$options);
		if ($muswitch=='Y') {
			$net_options_link=$sname;
			$options['net_options_link']=$net_options_link;
			update_option('kpg_stop_sp_reg_options',$options);
		}
	} else if ($options_link==$sname) {
		// already done
	} else if($option_link!=substr($sname,0,strlen($option_link))) {
		// store the new option - here because the link must have changed
		$options_link=$sname;
		$options['options_link']=$options_link;
		update_option('kpg_stop_sp_reg_options',$options);
		if ($muswitch=='Y') {
			$net_options_link=$sname;
			$options['net_options_link']=$net_options_link;
			update_option('kpg_stop_sp_reg_options',$options);
		}
	}

//$ip=kpg_get_ip();
$ip=$_SERVER['REMOTE_ADDR'];
if ($firsttime=='Y') {
	// check the IP for the first time
	kpg_sfs_check_load();
	if (kpg_sfs_check($sfs_check_activation,'Check IP',$ip)===false) {
		// break the installation
		echo "<br/>Your current configuration reports that you will be denied access as a spammer.<br/>
			Do not use this plugin until you can resolve this issue.";
		if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: IP Check Failed"."\r\n");
	} else {
		echo "<h2>Your IP address passed all plugin spam checks</h2>";
		if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: Ip Check Passes"."\r\n");
	}
	// update first time
	$firsttime='N';
	$options['firsttime']='N';
	update_option('kpg_stop_sp_reg_options',$options);
}
$wordpress_api_key=get_option('wordpress_api_key');
if (empty($wordpress_api_key)) $wordpress_api_key='';
$nonce='';
if (array_key_exists('kpg_stop_spammers_control',$_POST)) $nonce=$_POST['kpg_stop_spammers_control'];
if (!empty($nonce) && wp_verify_nonce($nonce,'kpgstopspam_update')) { 
	if (array_key_exists('action',$_POST)) {
		if (array_key_exists('wordpress_api_key',$_POST)) {
			$wordpress_api_key=stripslashes($_POST['wordpress_api_key']);
			if ($wordpress_api_key!='na') update_option('wordpress_api_key',$wordpress_api_key);
		} else {
			$wordpress_api_key='na';
		}
		
		// check all the yes/no questions - need to take up less room
		$ynfields=array(
		'chksession','chkdisp','chksfs','chkubiquity',
		'chkwplogin','chkakismet','chkakismetcomments','noplugins',
		'chkcomments','chklogin','chksignup','chklong',
		'chkagent','chkxmlrpc','addtowhitelist','chkadmin','chkadminlog',
		'chkspamwords','chkwpmail','redherring',
		'chkdnsbl','chkemail','chkip','chkreferer','chktor','chkcaptcha',
		'nobuy','redir','accept','notify','poison','wlreqmail'
);
		foreach ($ynfields as $yn) {
			$tyn='N';
			if (array_key_exists($yn,$_POST)) {
				$tyn=stripslashes($_POST[$yn]);
			}
			if ($tyn!='Y') $tyn='N';
			$options[$yn]=$tyn;
		}
		
		
		if (array_key_exists('sesstime',$_POST)) {
			$sesstime=stripslashes($_POST['sesstime']);
		} else {
			$sesstime=4;
		}
		if (!is_numeric($sesstime)||$sesstime<0||$sesstime>10) $sesstime=4;
		$options['sesstime']=$sesstime;
		
		
		if (array_key_exists('logfilesize',$_POST)) {
			$logfilesize=trim(stripslashes($_POST['logfilesize']));
		} else {
			$logfilesize=0;
		}
		if (!is_numeric($logfilesize)) $logfilesize=0;
		if (empty($logfilesize)||$logfilesize<0) $logfilesize=0;
		if ($logfilesize>500000) $logfilesize=500000;
		$options['logfilesize']=$logfilesize;
		
		if (array_key_exists('apikey',$_POST)) {
			$apikey=stripslashes($_POST['apikey']);
			$options['apikey']=$apikey;
			//echo "<br/>updated apikey $apikey<br/>";
		}
		if (array_key_exists('honeyapi',$_POST)) $honeyapi=stripslashes($_POST['honeyapi']);
		$options['honeyapi']=$honeyapi;
		if (array_key_exists('botscoutapi',$_POST)) $botscoutapi=stripslashes($_POST['botscoutapi']);
		$options['botscoutapi']=$botscoutapi;
		if (array_key_exists('blist',$_POST)) {
			$blist=$_POST['blist'];
			$blist=explode("\n",$blist);
			$tblist=array();
			foreach($blist as $bl) {
				$bl=trim($bl);
				if (!empty($bl)) $tblist[]=$bl;
			}
			$options['blist']=$tblist;				
			$blist=$tblist;
		}
		if (array_key_exists('spamwords',$_POST)) {
			$spamwords=$_POST['spamwords'];
			$spamwords=explode("\n",$spamwords);
			$tblist=array();
			foreach($spamwords as $bl) {
				$bl=trim($bl);
				if (!empty($bl)) $tblist[]=$bl;
			}
			$options['spamwords']=$tblist;				
			$spamwords=$tblist;
		}
		if (array_key_exists('wlist',$_POST)) {
			$wlist=$_POST['wlist'];
			$wlist=explode("\n",$wlist);
			$tblist=array();
			foreach($wlist as $bl) {
				$bl=trim($bl);
				if (!empty($bl)) $tblist[]=$bl;
			}
			$options['wlist']=$tblist;				
			$wlist=$tblist;
		}
		
		

		if (array_key_exists('badTLDs',$_POST)) {
			$badTLDs=$_POST['badTLDs'];
			$badTLDs=explode("\n",$badTLDs);
			$tblist=array();
			foreach($badTLDs as $bl) {
				$bl=trim($bl);
				if (!empty($bl)) $tblist[]=$bl;
			}
			$options['badTLDs']=$tblist;				
			$badTLDs=$tblist;
		}

		if (array_key_exists('baddomains',$_POST)) {
			$baddomains=$_POST['baddomains'];
			$baddomains=explode("\n",$baddomains);
			$tblist=array();
			foreach($baddomains as $bl) {
				$bl=trim($bl);
				if (!empty($bl)) $tblist[]=$bl;
			}
			$options['baddomains']=$tblist;				
			$baddomains=$tblist;
		}
		// update the freq and age options
		if (array_key_exists('sfsfreq',$_POST)) $sfsfreq=trim(stripslashes($_POST['sfsfreq']));
		if (array_key_exists('hnyage',$_POST)) $hnyage=trim(stripslashes($_POST['hnyage']));
		if (array_key_exists('botfreq',$_POST)) $botfreq=trim(stripslashes($_POST['botfreq']));
		if (array_key_exists('sfsage',$_POST)) $sfsage=trim(stripslashes($_POST['sfsage']));
		if (array_key_exists('hnylevel',$_POST)) $hnylevel=trim(stripslashes($_POST['hnylevel']));
		if (array_key_exists('botage',$_POST)) $botage=trim(stripslashes($_POST['botage']));
		if (array_key_exists('rejectmessage',$_POST)) $rejectmessage=trim(stripslashes($_POST['rejectmessage']));
		if (array_key_exists('redirurl',$_POST)) $redirurl=trim(stripslashes($_POST['redirurl']));
		
		if (array_key_exists('kpg_sp_cache',$_POST)) $kpg_sp_cache=trim(stripslashes($_POST['kpg_sp_cache']));
		if (array_key_exists('kpg_sp_cache_em',$_POST)) $kpg_sp_cache_em=trim(stripslashes($_POST['kpg_sp_cache_em']));
		// dividing the cache into an email cache, an ip cache, and good cache with different sizes
		
		if (array_key_exists('kpg_sp_hist',$_POST)) $kpg_sp_hist=trim(stripslashes($_POST['kpg_sp_hist']));
		if (array_key_exists('kpg_sp_good',$_POST)) $kpg_sp_good=trim(stripslashes($_POST['kpg_sp_good']));
		// check for numerics in the fields
		if (!is_numeric($sfsfreq)) $sfsfreq=0; 
		if (!is_numeric($hnyage)) $hnyage=0;
		if (!is_numeric($botfreq)) $botfreq=0; 
		if (!is_numeric($hnylevel)) $hnylevel=5;
		if (!is_numeric($botage)) $botage=9999; 
		if (!is_numeric($sfsage)) $sfsage=9999;	
		if (!is_numeric($kpg_sp_cache)) $kpg_sp_cache=25;	
		if (!is_numeric($kpg_sp_cache_em)) $kpg_sp_cache_em=10;	
		if (!is_numeric($kpg_sp_hist)) $kpg_sp_hist=25;	
		if (!is_numeric($kpg_sp_good)) $kpg_sp_good=2;	
		$options['sfsfreq']=$sfsfreq;
		$options['hnyage']=$hnyage;
		$options['botfreq']=$botfreq;
		$options['sfsage']=$sfsage;
		$options['hnylevel']=$hnylevel;
		$options['botage']=$botage;
		$options['kpg_sp_cache']=$kpg_sp_cache;
		$options['kpg_sp_cache_em']=$kpg_sp_cache_em;
		$options['kpg_sp_hist']=$kpg_sp_hist;
		$options['kpg_sp_good']=$kpg_sp_good;
		$options['redirurl']=$redirurl;
		$options['rejectmessage']=$rejectmessage;
		$options['test 1']='Y';
		//echo "<br/>Updating options<br/>";
		//echo "<br/>Got options 2 size=".count($options).' apikey='.$options['apikey'].'<br/>';
		if (!update_option('kpg_stop_sp_reg_options',$options)) {
			delete_option('kpg_stop_sp_reg_options');
			$options['autoload']='Y';
			if(!add_option('kpg_stop_sp_reg_options',$options, 0, 'no' )) {
				if (!update_option('kpg_stop_sp_reg_options',$options)) {
				}
			}
		}
		extract($options); // extract again to get the new options
		if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: updated options"."\r\n");
		echo "<h2>Options Updated</h2>";

	} else if (array_key_exists('kpg_stop_check_me',$_POST)) {		
		// validate the current users's spam
		//echo "Validating Check Your IP<br/>";
		//$ip=kpg_get_ip();
		$ip=$_SERVER['REMOTE_ADDR'];
		kpg_sfs_check_load();
		if (kpg_sfs_check($sfs_check_activation,'Check IP',$ip)===false) {
			// break the installation
			echo "<br/>Your current configuration reports that you will be denied access as a spammer.<br/>
					Do not use this plugin until you can resolve this issue.";
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: IP Check Failed"."\r\n");
		} else {
			echo "<h2>Your IP address passed all plugin spam checks</h2>";
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: Ip Check Passes"."\r\n");
		}
	} else if (array_key_exists('kpg_stop_delete_log',$_POST)) {	
		// delete the log
		$f=dirname(__FILE__)."/../sfs_debug_output.txt";
		if (file_exists($f)) {
			unlink($f);
			echo "<h2>Deleted Error Log File</h2>";
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: Error Log Deleted"."\r\n");
		}
	}

} else {
	// echo "no nonce<br/>";
}

$nonce=wp_create_nonce('kpgstopspam_update');

	if (array_key_exists('HTTP_CF_CONNECTING_IP',$_SERVER)&& !function_exists( 'cloudflare_init' )) {
		echo "<p style=\"color:red;font-style::italic;\">
	   CloudFlare Remote IP address detected. Please install the <a href=\"http://wordpress.org/plugins/cloudflare/\">CloudFlare Plugin</a>.
	   This plugin requires CloudFlare plugin when using CloudFlare.
	</p>";
	}

?>




  <p><a href="http://www.blogseye.com/checkspam/" target="_blank">Check an IP address to see if it passes spam checks.</a></p>
  <?PHP	
if ($addtowhitelist=='Y'&&in_array($ip,$wlist)) {
	?>
  <p><strong>Your current IP is in your white list. This will keep you from being locked out in the future</strong></p>
  <?php
}
// check to see if you are using the admin login id.
$current_user = wp_get_current_user();
$current_user_name=$current_user->user_login;
//$ip=kpg_get_ip();
$ip=$_SERVER['REMOTE_ADDR'];

if ($current_user_name=='admin') {
	?>
  <strong style="color:red;">Your current ID is 'admin'. This is very dangerous. You should rename the admin id to something else.<br/>
  There is a very simple admin renamer plugin at a <a href="http://wordpress.org/extend/plugins/admin-username-changer/">Admin username changer</a> (do not use in MU)</strong>
  <?php
} else {
	echo "<p>You are currently logged in as '$current_user_name'</p>";
}
	echo "<p>Your IP address is '$ip'</p>";

// check that ip address is the same as in the one web server reports
$rip=$_SERVER['REMOTE_ADDR'];
if ($ip!=$rip) {
	echo "<p>Your server reports that your IP address is $rip.<br/>";
	echo "but XFF reports your actual IP address appears to be $ip.<br/>This may be because you are behind a firewall or proxy server. This can cause some problems checking spammer IP addresses.</p>";
}
$me=$options['options_link'];
$sme=$options['history_link'];
if ($muswitch=="Y") {
	$me=$options['net_options_link'];
	$sme=$options['net_history_link'];
}

if (!empty($me)) {
	echo "<p><a style=\"font-style:italic;\" href=\"$me\">Stop Spammer Options</a>";
	echo"</p>";
}
if (!empty($sme)) {
	echo "<p><a style=\"font-style:italic;\" href=\"$sme\">Stop Spammer History</a>";
	echo"</p>";
}


?>
  <h4>Self Check</h4>
  <p>In some configurations this plugin will erroneously report that you are a spammer. Please verify your status by clicking the &quot;Check Your IP&quot; button. This will run your IP through the spam checks. If it fails and thinks you might be a spammer then quickly deactivate the plugin so it will not lock you out of your own blog. </p>
  <p> This plugin is very aggressive and may generate false positives. Please check every time the plugin is updated and every time you change the options to make sure that the plugin does not think you are a spammer. </p>
  <form method="post" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_check_me" value="true" />
    <p class="submit">
      <input class="button-primary" value="Check Your IP" type="submit" />
    </p>
  </form>
  <?php	
if (empty($spmcount))  $spmcount=0;
if ($spmcount>0) {
	?>
  <h3>Stop Spammers has stopped <?php echo $spmcount; ?> spammers since installation</h3>
  <?php 
}
if (empty($spcount))  $spcount=0;
if ($spcount>0) {
	?>
  <h3>Stop Spammers has stopped <?php echo $spcount; ?> spammers since last cleared</h3>
  <?php 

} 
$num_comm = wp_count_comments( );
$num = number_format_i18n($num_comm->spam);
if ($num_comm->spam>0&&$muswitch!='Y') {	
	?>
  <p>There are <a href='edit-comments.php?comment_status=spam'><?php echo $num; ?></a> spam comments waiting for you to report them</p>
  <?php 
}
$num_comm = wp_count_comments( );
$num = number_format_i18n($num_comm->moderated);
if ($num_comm->moderated>0&&$muswitch!='Y') {	
	?>
  <p>There are <a href='edit-comments.php?comment_status=moderated'><?php echo $num; ?></a> spam comments waiting to be moderated</p>
  <?php 
}
?>
  <p style="font-weight:bold;">The Stop Spammers Plugin is installed and working correctly.</p>
  <p style="font-weight:bold;">Version 5.9.3</p>
  <p>Please note that support for this plugin will be ending soon</p>
  <script type="text/javascript" >
function kpg_show_hide_how() {
	id=document.getElementById("kpg_stop_spam_div");
	if (id.style.display=='none') {
		id.style.display="block";
	} else {
		id.style.display="none";
	}
	return false;
}
</script>
  <a href="#" onclick="return kpg_show_hide_how();">How the plugin works</a>
  <div id="kpg_stop_spam_div" style="display:none;">
    <p>Checks known spammer hosts such  as Ubiquity Servers, disposable email addresses, very long email address and  names, and HTTP_ACCEPT header. Checks for robots that hit your site too fast,  and puts a fake comment and login screen where only spammers will find them. In  all the plugin uses a dozen different strategies to block spammers. </p>
    <p style="font-weight:bold;">How the plugin works: </p>
    <p>
      The Stop Spammers plugin works by checking anyone who tries to register, login, or leave a comment. This effectively blocks spammers who try to register on blogs or leave spam. </p>
    <p>Optionally the plugin will also check for disposable email addresses, check for the lack of a HTTP_ACCEPT header, and check against several DNSBL lists such as Spamhaus.org. It also checks against spammer hosts like Ubiquity-Nobis, XSServer, Balticom, Everhost, FDC, Exetel, Virpus and other servers, which are a major source of Spam Comments. </p>
    <p>Rejects very long email addresses and very long author names since spammers can't resist putting there message everywhere. It also rejects form POST data where the HTTP_REFERER header does not match your domain, because spammers often forget to include the correct referring site information in their software.</p>
    <p>The plugin will install a &quot;Red Herring&quot; comment form that will be invisible to normal users. Spammers will find this form and try to do their dirty deed using it. This results in the IP address being added to the deny list. This feature is turned off by default because the form might screw up your theme. Turn the option on and check your theme. If the form (a one pixel box) changes your theme presentation then turn the feature off. I highly recommend that you try this option. It stops a ton of spam.</p>
    <p>The plugin can check how long it takes a  spammer to read the comment submit form and then post the comment. If this  takes less than 4 seconds, then the commenter is a spammer. A human cannot fill  out email, comment, and then submit the comment in less than 4 seconds. This is the best way to stop spam. </p>
    <p> The plugin installs a Poison Link that can only be seen by spammers. If they follow it, it adds
      the spammers IP address to the cache of bad IP addresses so that the user will be denied, even
      if they pass all tests. </p>
     <p><span style="font-weight:bold;">Cache: </span></p>
    <p>The Stop Spammers plugin keeps track of a number of spammer emails and IP addresses in a cache to avoid pinging databases more often than necessary. The results are saved and displayed. You can control the length of the cache list and clear it at any time. </p>
    <p><span style="font-weight:bold;">Reporting Spam : </span></p>
    <p>On the comments moderation page, the plugin adds extra options to check comments against the various databases and to report to the Stop Forum Spam database. You will need a Stop Forum Spam API key in order to report spam/ </p>
    <p><span style="font-weight:bold;">Network MU Installation Option : </span></p>
    <p> If you are running a networked WPMU system of blogs, you can optionally control this plugin from the control panel of the main blog. By checking the 'Networked ON' radio button, the individual blogs will not see the options page. The Network buttons only appear if you have a Networked installation.</p>
    <p><strong>Debugging:</strong></p>
    <p>If the plugin is having trouble it drops a file in the plugin&rsquo;s directory named sfs_debug_output.txt. If the file is produced it will appear at the bottom of this page along with a button to delete it. Common errors include many which are not really an issue. From time to time you will see an error that reports that PHP could not redirect a page. This happens on some systems where the spammer is rejected, but the access denied screen does not appear correctly. I believe this is caused by data in the spam comment.</p>
    <p>If you do not want to see any errors you can search in the plugin code for the line that contains &quot;debug=true;&quot; and change the true to false, so the line is &quot;debug=false;&quot; (a semicolon at the end is required by PHP). I have set the plugin to report problems by default (debug=true;). </p>
    <p>Please report any errors that you see. </p>
     <p>The Stop Spammers plugin is ON when it is installed and enabled. To turn it off just disable the plugin from the plugin menu.. </p>
    <p>You may see your own email or IP in the cache as spammers try to use it to leave comments. You may have to white list your own email if that is the case, to keep the plugin from locking you out.</p>
     <hr/>
    <p>&nbsp;</p>
  </div>
  <form method="post" action="">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <h4>Prevent Lockouts</h4>
    <p>This plugin aggressively checks for spammers and is unforgiving to the point where even you may get locked out of your own blog when you log off and try to log back in. There are two options which help prevent this, but these options can make it easier for a spammer to hack your site.<br/>
      When you are confident that the plugin is working uncheck these boxes.</p>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Automatically add admins to white list:</td>
        <td align="center" valign="top"><input name="addtowhitelist" type="checkbox" value="Y" <?php if ($addtowhitelist=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Whenever an admin hits the options page his IP will be added to the white list. This prevents admins from being locked out. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check credentials on all login attempts:</td>
        <td align="center" valign="top"><input name="chkadmin" type="checkbox" value="Y" <?php if ($chkadmin=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Normally the plugin checks for spammers before Wordpress can try to log in a user. If you check this box, every attempt to login will be tested for a valid user. This may allow a hacker to guess your user id and password by making thousands of attempts to login. This is turned on initially to prevent you from being locked out of your own blog, but should be unchecked after you verify that the plugin does not think you are a spammer.</td>
      </tr>
    </table>
    <h4>IP Checking:</h4>
    In some cases it is impossible to check the IP address of incoming spammers. Some hosts do not pass on the the correct IP Address. Some users use proxy servers to protect their site. In these cases the plugin does not work at all. Some protection from Red Herring, User Agent, Session Speed, Referrer and other checks, still allows the plugin to work fairly well, but spammers cannot be cached or banned by IP.
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Check IP address</td>
        <td align="center" valign="top"><input name="chkip" type="checkbox" value="Y" <?php if ($chkip=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Unchecking this cripples the plugin and should only be done if the user is behind some kind of router or firewall that cannot see the incoming IP address. If it looks like everyone has the same IP then uncheck this box.</td>
      </tr>
    </table>
	<?PHP
	if (ipChkk()) {
	?>
    <h4>API Keys:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Enable StopForumSpam Lookups:</td>
        <td align="center" valign="top"><input name="chksfs" type="checkbox" value="Y" <?php if ($chksfs=='Y') echo  "checked=\"checked\"";?>/>
        </td>
        <td valign="top">You may want to disable checking of the SFS db. By default this is on. Uncheck it to turn off.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Your StopForumSpam.com API Key:</td>
        <td align="center" valign="top"><input size="32" name="apikey" type="text" value="<?php echo $apikey; ?>"/></td>
        <td valign="top">(optional)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top"> Project Honeypot API Key:</td>
        <td align="center" valign="top"><input size="32" name="honeyapi" type="text" value="<?php echo $honeyapi; ?>"/></td>
        <td valign="top"> (For HTTP:bl blacklist lookup, if not blank)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">BotScout API Key:</td>
        <td align="center" valign="top"><input size="32" name="botscoutapi" type="text" value="<?php echo $botscoutapi; ?>"/></td>
        <td valign="top">(For BotScout.com lookup, if not blank)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Wordpress API Key:</td>
        <td align="center" valign="top"><input size="32" name="wordpress_api_key" type="text" value="<?php echo $wordpress_api_key; ?>"/></td>
        <td valign="top">(For use with Akismet DB. This will work without the Akismet plugin if you put enter your Wordpress API Key here.)</td>
      </tr>
    </table>
    <h4>Spam Limits:</h4>
    You can set the minimum settings to allow possible spammers to use your site. <br/>
    You may wish to forgive spammers with few incidents or no recent activity. I would recommend that to be on the safe side you should block users who appear on the spam database unless they specifically ask to be white listed. Allowed values are 0 to 9999. Only numbers are accepted.
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Deny spammers found on Stop Forum Spam with more than
          <input size="3" name="sfsfreq" type="text" value="<?php echo $sfsfreq; ?>"/>
          incidents, and occurring less than
          <input size="4" name="sfsage" type="text" value="<?php echo $sfsage; ?>"/>
          days ago. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Deny spammers found on Project HoneyPot with incidents less than
          <input size="3" name="hnyage" type="text" value="<?php echo $hnyage; ?>"/>
          days ago, and with more than
          <input size="4" name="hnylevel" type="text" value="<?php echo $hnylevel; ?>"/>
          threat level. (25 threat level is average, threat level 5 is fairly low.)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Deny spammers found on BotScout with more than
          <input size="3" name="botfreq" type="text" value="<?php echo $botfreq; ?>"/>
          incidents.</td>
      </tr>
    </table>
    <br/>
	<?PHP
	 }
	?>
    <h4>Other Checks:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Block Spam missing the HTTP_ACCEPT header:</td>
        <td align="center" valign="top"><input name="accept" type="checkbox" value="Y" <?php if ($accept=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Blocks users who have incomplete headers.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Block invalid HTTP_REFERER:</td>
        <td align="center" valign="top"><input name="chkreferer" type="checkbox" value="Y" <?php if ($chkreferer=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Blocks users who send form data, but the HTTP_REFERER does not match your domain. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check email address in addition to IP:</td>
        <td align="center" valign="top"><input name="chkemail" type="checkbox" value="Y" <?php if ($chkemail=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Most spammers use random, faked or other people's email, so this may be of limited value. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Deny disposable email addresses:</td>
        <td align="center" valign="top"><input name="chkdisp" type="checkbox" value="Y" <?php if ($chkdisp=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Some real comments might use disposable email, but probably not </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check for long emails or author name:</td>
        <td align="center" valign="top"><input name="chklong" type="checkbox" value="Y" <?php if ($chklong=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Spammers like to use long names and emails. This rejects these if the are over 64 characters in length (optional)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check for missing HTTP_USER_AGENT:</td>
        <td align="center" valign="top"><input name="chkagent" type="checkbox" value="Y" <?php if ($chkagent=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Browsers always include a user agent string when they access a site. A missing user agent is usually a spammer using poorly written software or a leach who is stealing the pages from your site.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check for quick responses (disabled if caching is active):</td>
        <td align="center" valign="top"><input name="chksession" type="checkbox" value="Y" <?php if ($chksession=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">The plugin will drop a cookie with the current time in it. When the user enters a comment or tries to log into the system, the time is checked. If the user responds too fast, he is a spammer. Use the timeout value in the next option to limit control this. (Stops the most spammers of all the methods listed here.)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Response Timeout value:</td>
        <td align="center" valign="top"><input name="sesstime" type="text" value="<?php echo $sesstime;?>" size="2"/>
        </td>
        <td align="left" valign="top">This is the time used to determine if a spammer has filled out a form too quickly. Humans take more than 10 seconds, at least, to fill out forms. The default is 4 seconds. If a user takes 4 seconds or less to fill out a form they are not human and are denied. Users who use automatic passwords (like FireFox users) may show up as false positives so keep this low. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Use a Red Herring form:</td>
        <td align="center" valign="top"><input name="redherring" type="checkbox" value="Y" <?php if ($redherring=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Places a fake comment form on web pages to trap spammers. If they bite, their IP address is added to the bad cache. Normal users should not be able to see the Red Herring form. Check your theme after enabling this feature to make sure that it does not alter your blog's presentation. (Very very good way to stop spammers.)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check against DNSBL lists such as Spamhaus.org:</td>
        <td align="center" valign="top"><input name="chkdnsbl" type="checkbox" value="Y" <?php if ($chkdnsbl=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Primarily used for email spam, but might stop comment spam. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Blacklist searches for Wordpress PHP files:</td>
        <td align="center" valign="top"><input name="chkwplogin" type="checkbox" value="Y" <?php if ($chkwplogin=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">When WordPress detects a 404 file not found for someone trying to find wp-signup, this is someone probing your site to find your login so the IP is added to your bad IP cache. This is off by default. (This no longer checks wp-login.php due to the possibility of locking the admin out.)</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Blacklist login attempts using 'admin' userid:</td>
        <td align="center" valign="top"><input name="chkadminlog" type="checkbox" value="Y" <?php if ($chkadminlog=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">When a spammer starts hitting the login page with a userid of 'admin' and there is no 'admin' user then it is a spammer trying to figure your password. Black list immediately. This only works if you have do not have the user 'admin'. It is dangerous to have a user name 'admin'.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check against list of Ubiquity-Nobis and other Spam Server IPs:</td>
        <td align="center" valign="top"><input name="chkubiquity" type="checkbox" value="Y" <?php if ($chkubiquity=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Hosting companies who tolerate spammers are the source of much Comment Spam</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check for TOR:</td>
        <td align="center" valign="top"><input name="chktor" type="checkbox" value="Y" <?php if ($chktor=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Block comments from users coming from a know TOR node.</td>
      </tr>
 	<?PHP
	if (ipChkk()) {
	?>
     <tr bgcolor="white">
        <td valign="top">Check IP against the Akismet db on logins:</td>
        <td align="center" valign="top"><input name="chkakismet" type="checkbox" value="Y" <?php if ($chkakismet=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">(Temporarily disabled) If the Akismet API key is set, then you may use Akismet to check logins or registrations, logins and signups.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP against the Akismet db on comments:</td>
        <td align="center" valign="top"><input name="chkakismetcomments" type="checkbox" value="Y" <?php if ($chkakismetcomments=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">(Temporarily disabled) If the Akismet API key is set, then you may use Akismet to check Comments and other actions (except logins) for spammers. Please note that Akismet does a much better job of managing comment spam. This, however, will extend Akismet checks to all form submissions that deal with IDs, emails and passwords. This does not mark comments as spam - it just blocks them completely.</td>
      </tr>
 	<?PHP
	}
	?>
     
      <tr bgcolor="white">
        <td valign="top">Check Poison Links:</td>
        <td align="center" valign="top"><input name="poison" type="checkbox" value="Y" <?php if ($poison=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Places a &quot;poison link&quot; on your web pages. It will be invisible to everyone except robots. If a robot follows this link it will immediately be placed in the cache of bad IPs and prevented from commenting or regisering in the future. </td>
      </tr>
      </tr>
      
      <tr bgcolor="white">
        <td valign="top">Send email on White List Request:</td>
        <td align="center" valign="top"><input name="wlreqmail" type="checkbox" value="Y" <?php if ($wlreqmail=='Y') echo  "checked=\"checked\"";?>/></td>
        <td align="left" valign="top">Sends an email notification to the sysop whenever someone fills out the white list request form.</td>
      </tr>
    </table>
    <br/>
    <h4>Lists:</h4>
    <span style="font-size:.8em;font-style:italic;">You can use an '*' as a wild card but only at the end of the ip or email. It is not a true wild card. It really means 'starts with'. So you can say '235.5.*' but you can't say '146.*.23.1' - this will be interpreted as '146.*'. </span>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">White List:</td>
        <td align="center" valign="top"><textarea name="wlist" cols="32" rows="8"><?php 
for ($k=0;$k<count($wlist);$k++) {
	echo $wlist[$k]."\r\n";
}
?>
</textarea></td>
        <td valign="top">put IP addresses or emails here that you don't want blocked. One email or IP to a line.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Black List:</td>
        <td align="center" valign="top"><textarea name="blist" cols="32" rows="8"><?php
	for ($k=0;$k<count($blist);$k++) {
		echo $blist[$k]."\r\n";
	}
	?>
</textarea></td>
        <td valign="top">put IP addresses or emails here that want blocked. One email or IP to a line.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top"> Blocked Email Domains:</td>
        <td align="center" valign="top"><textarea name="baddomains" cols="32" rows="8"><?php
	for ($k=0;$k<count($baddomains);$k++) {
		echo $baddomains[$k]."\r\n";
	}
	?>
</textarea></td>
        <td valign="top"><p>Put the domains you want blocked here. e.g. dresssmall.com. This will block all comments and registrations that use this domain for emails.</p>
          <p>One domain per line. </p></td>
      </tr>
      <tr bgcolor="white">
        <td valign="top"> Blocked TLDs:</td>
        <td align="center" valign="top"><textarea name="badTLDs" cols="32" rows="8"><?php
	for ($k=0;$k<count($badTLDs);$k++) {
		echo $badTLDs[$k]."\r\n";
	}
	?>
</textarea></td>
        <td valign="top"><p>Put the TLDs (Top Level Domains) that you want blocked. A TLD is the last part of a domain like .COM or .NET. You can block emails from various countries this way by adding a TLD such as .CN or .RU (these will block Russia and China).<br/>
            Enter the TLD name including the '.' e.g. .XXX<br/>
            A list of TLDs can be found at <a href="http://en.wikipedia.org/wiki/List_of_Internet_top-level_domains" target="_blank">Wikipedia List of Internet top-level domains</a>.</p>
          <p>One TLD per line. </p></td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check Spam Words:</td>
        <td valign="top"><input name="chkspamwords" type="checkbox" value="Y" <?php if ($chkspamwords=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Use the spam words list to check email and author fields. </td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Spam Words List:</td>
        <td valign="top"><textarea name="spamwords" cols="32" rows="8"><?php
	for ($k=0;$k<count($spamwords);$k++) {
		echo $spamwords[$k]."\r\n";
	}
	?>
</textarea></td>
        <td valign="top"><p>If a word here shows up in an email address or author field then block the comment. (Wild cards do not work here. </p>
          <p>One word per line </p></td>
      </tr>
    </table>
    <br/>
    <h4>Events to Check:</h4>
    You can specify which events to check. You may not want to check logins or registrations. You may wish to allow any comment and let Akismet handle things.
    Uncheck a box if you want to disable the plugin for specific Wordpress functions
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Check IP on wp-comment.php:</td>
        <td valign="top"><input name="chkcomments" type="checkbox" value="Y" <?php if ($chkcomments=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP and email every time the wp-comments.php file is loaded.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP on wp-login.php:</td>
        <td valign="top"><input name="chklogin" type="checkbox" value="Y" <?php if ($chklogin=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP and email every time the wp-login.php file is loaded. 
		If you un-check this, the plugin will still check for action=register, which uses wp-login.php.
		So you can un-check this to allow logins, but still check registrations.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP on wp-signup.php:</td>
        <td valign="top"><input name="chksignup" type="checkbox" value="Y" <?php if ($chksignup=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP and email every time the wp-signup.php file is loaded.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP on xmlrpc.php:</td>
        <td valign="top"><input name="chkxmlrpc" type="checkbox" value="Y" <?php if ($chkxmlrpc=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP and email every time the xmlrpc.php file is loaded. This will check ping backs and other remote calls</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Check IP on wp_mail:</td>
        <td valign="top"><input name="chkwpmail" type="checkbox" value="Y" <?php if ($chkwpmail=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">Check IP whenever wordpress sends mail to prevent spammers from sending mail to you or anyone else.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Don't check plugin forms:</td>
        <td valign="top"><input name="noplugins" type="checkbox" value="Y" <?php if ($noplugins=='Y') echo  "checked=\"checked\"";?>/></td>
        <td valign="top">This disables the spam check if the Wordpress form appears to be in a plugin folder. This may prevent blocking of spam during some eCommerce type functions.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top"></td>
      </tr>
    </table>
    <br/>
    <h4>History and Cache Size:</h4>
    You can change the number of entries to keep in your history and cache. The size of these items is an issue and will cause problems with some WordPress installations. It is best to keep these small.<br/>
    <table align="center" cellspacing="1"  style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td valign="top">Bad IP Cache Size:</td>
        <td valign="top"><select name="kpg_sp_cache">
            <option value="0" <?php if ($kpg_sp_cache=='0') echo "selected=\"true\""; ?>>0</option>
            <option value="10" <?php if ($kpg_sp_cache=='10') echo "selected=\"true\""; ?>>10</option>
            <option value="25" <?php if ($kpg_sp_cache=='25') echo "selected=\"true\""; ?>>25</option>
            <option value="50" <?php if ($kpg_sp_cache=='50') echo "selected=\"true\""; ?>>50</option>
            <option value="75" <?php if ($kpg_sp_cache=='75') echo "selected=\"true\""; ?>>75</option>
            <option value="100" <?php if ($kpg_sp_cache=='100') echo "selected=\"true\""; ?>>100</option>
            <option value="200" <?php if ($kpg_sp_cache=='200') echo "selected=\"true\""; ?>>200</option>
          </select></td>
        <td valign="top">Select the number of items to save in the bad IP cache. Avoid making too big as it can cause the plugin to run out of memory.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Bad Email Cache Size:</td>
        <td valign="top"><select name="kpg_sp_cache_em">
            <option value="0" <?php if ($kpg_sp_cache_em=='0') echo "selected=\"true\""; ?>>0</option>
            <option value="10" <?php if ($kpg_sp_cache_em=='10') echo "selected=\"true\""; ?>>10</option>
            <option value="25" <?php if ($kpg_sp_cache_em=='25') echo "selected=\"true\""; ?>>25</option>
            <option value="50" <?php if ($kpg_sp_cache_em=='50') echo "selected=\"true\""; ?>>50</option>
            <option value="75" <?php if ($kpg_sp_cache_em=='75') echo "selected=\"true\""; ?>>75</option>
            <option value="100" <?php if ($kpg_sp_cache_em=='100') echo "selected=\"true\""; ?>>100</option>
            <option value="200" <?php if ($kpg_sp_cache_em=='200') echo "selected=\"true\""; ?>>200</option>
          </select></td>
        <td valign="top">Select the number of items to save in the bad Email cache. Keep small, as emails seldom work to stop spammers.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">History Size:</td>
        <td valign="top"><select name="kpg_sp_hist">
            <option value="10" <?php if ($kpg_sp_hist=='10') echo "selected=\"true\""; ?>>10</option>
            <option value="25" <?php if ($kpg_sp_hist=='25') echo "selected=\"true\""; ?>>25</option>
            <option value="50" <?php if ($kpg_sp_hist=='50') echo "selected=\"true\""; ?>>50</option>
            <option value="75" <?php if ($kpg_sp_hist=='75') echo "selected=\"true\""; ?>>75</option>
            <option value="100" <?php if ($kpg_sp_hist=='100') echo "selected=\"true\""; ?>>100</option>
          </select></td>
        <td valign="top">Select the number of items to save in the History. Keep small.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">Good Cache Size:</td>
        <td valign="top"><select name="kpg_sp_good">
            <option value="1" <?php if ($kpg_sp_good=='1') echo "selected=\"true\""; ?>>1</option>
            <option value="2" <?php if ($kpg_sp_good=='2') echo "selected=\"true\""; ?>>2</option>
            <option value="3" <?php if ($kpg_sp_good=='3') echo "selected=\"true\""; ?>>3</option>
            <option value="4" <?php if ($kpg_sp_good=='4') echo "selected=\"true\""; ?>>4</option>
          </select></td>
        <td valign="top">A temporary white list so that once a user passes they will not be checked again soon. Early spammers can slip in here and get carte blanche, so limit to 1 or two entries.</td>
      </tr>
      <tr bgcolor="white">
        <td valign="top">History Log file size:</td>
        <td valign="top"><input  name="logfilesize" type="text" value="<?php echo $logfilesize; ?>"/>
        </td>
        <td valign="top">A log file can be created in the content folder that contains a log of all transactions and stopped spammers. This can be much longer than the recent history shown in the history page. It can also grow to be very large. The file size entered here will be the max size in bytes of the file. If it excedes this size the plugin will no longer write to it. The file can be viewed and cleared on the history page. Enter 0 here if you don't want to write to a log.</td>
      </tr>
    </table>
    <br/>
    <h4>Access Denied Options:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <!-- thead style="height:0;margin:0;padding:0;">
	<tr style="height:0;margin:0;padding:0;" bgcolor="white"><th width="120px"></th><th width="20px"></th><th width="200px"></th><th></th><tr>
	</thead -->
      <tbody>
        <tr bgcolor="white">
          <td valign="top">Spammer Message:</td>
          <td colspan="2" valign="top"><textarea id="rejectmessage" name="rejectmessage" cols="32" rows="5"><?php echo $rejectmessage; ?></textarea></td>
          <td valign="top">This message is only visible to spammers. It only shows if spammers are rejected at the time login or comment form is displayed. You can use the shortcode <i>[reason]</i> to include the deny reason code with the message. You can also use <i>[ip]</i> in your message which would be the user's ip address. (You may not want to give spammers hints on how they were denied.)</td>
        </tr>
        <tr bgcolor="white">
          <td valign="top">Send spammer to another web page:</td>
          <td align="center" valign="top"><input type="checkbox" name ="redir" value="Y" <?php if ($redir=='Y') echo "checked=\"checked\""; ?> />
          </td>
          <td valign="top" colspan="2">Check this to send spammers to the URL below.</td>
        </tr>
        <tr bgcolor="white">
          <td valign="top">Redirect URL:</td>
          <td valign="top" colspan="3"><input size="77" name="redirurl" type="text" value="<?php echo $redirurl; ?>"/></td>
        </tr>
        <tr bgcolor="white">
          <td valign="top" colspan="4">If you want you can send the spammer to a web page. This can be a custom page explaining terms of service, or a nasty message </td>
        </tr>
        <tr bgcolor="white">
          <td valign="top">Let blocked users request to be added to the white list: </td>
          <td align="center" valign="top"><input type="checkbox" name ="notify" value="Y" <?php if ($notify=='Y') echo "checked=\"checked\""; ?> />
          </td>
          <td valign="top" colspan="2">In the case where a user is blocked, but the user feels that it is a mistake, you can display a link at the end of the block notification so that a user can request to be white-listed. The list of whitelist requests shows up in the statistics screen with the reason codes and an admin can decide if the user should be white listed. </td>
        </tr>
       <tr bgcolor="white">
          <td valign="top">Give blocked users a second chance with a CAPTCHA Form: </td>
          <td align="center" valign="top"><input type="checkbox" name ="chkcaptcha" value="Y" <?php if ($chkcaptcha=='Y') echo "checked=\"checked\""; ?> />
          </td>
          <td valign="top" colspan="2">The plugin is extremely aggressive and will probably block some small number of legitimate users. You can give users a second chance by displaying a CAPTCHA image and asking them to type in the letters that they see. This prevents lockouts, so do not uncheck this box unless you are very sure that people will not be locked out of your system.<br>If you can see this image with the word TEST then you can use captcha.
		  <?php
		  $url=admin_url('admin-ajax.php')."?action=sfs_captcha";
		  set_transient( "KPG_SECRET_WORD".$_SERVER['REMOTE_ADDR'],"TEST",10 );
		  ?>
		  <img src="<?php echo $url; ?>" /></td>
        </tr>
      </tbody>
    </table>
    <br/>
    </p>
    <br/>
    <p class="submit">
      <input class="button-primary" value="Save Changes" type="submit" />
    </p>
  </form>
  <p>&nbsp;</p>
  <?php
$f=dirname(__FILE__)."/../sfs_debug_output.txt";
if (file_exists($f)) {
	?>
  <h3>Error Log</h3>
  <p>If debugging is turned on, the plugin will drop a record each time it encounters a PHP error. 
    Most of these errors are not fatal and do not effect the operation of the plugin. Almost all come from the unexpected data that
    spammers include in their effort to fool us. The author's goal is to eliminate any and
    all errors. </p>
  <form method="post" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_delete_log" value="true" />
    <p class="submit">
      <input class="button-primary" value="Delete Error Log File" type="submit" />
    </p>
  </form>
  <pre>
<?php readfile($f); ?>
</pre>
  <?php
	}
?>
</div>
<?php

	sfs_errorsonoff('off');
?>
