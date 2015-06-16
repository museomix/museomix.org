<?php
// this module has all the code for rejecting a user.
// I added this to make it easy to incorporate the Really-Simple-Captcha plugin to give rejected users a second chance.
if (!defined('ABSPATH')) exit; // just in case
function kpg_reject_spam_l($rejectmessage,$notify,$chkcaptcha,$whodunnit) {
    // check to see if really simple captcha is loaded.
	
	if (!extension_loaded('gd') || !function_exists('gd_info')) {
		$chkcaptcha!='N';
	}
	if ($notify=='Y'&&$chkcaptcha!='Y' ) {
		// need to offer blocked user the chance to whitelisted
		// reason is in $whodunnit.
		$knonce=wp_create_nonce('kpgstopspam_wlrequest');
 		$r2="
		<p>
		'$chkcaptcha'
		If you feel that you have been blocked in error, you may notify the Admin and ask to placed on the
		system white list. This site's admin will be notified and can review the reason why you were blocked.<br/>
			<form name=\"DOIT2\" method=\"POST\" action=\"\">
			<input type=\"hidden\" name=\"kip\" value=\"$ip\" />
			<input type=\"hidden\" name=\"kem\" value=\"$em\" />
			<input type=\"hidden\" name=\"kau\" value=\"$author\" />
			<input type=\"hidden\" name=\"knot\" value=\"$whodunnit\" />
			<input type=\"hidden\" name=\"knotify_key\" value=\"$knonce\" />
			Please ask nicely to be admitted here.<br/>
			<textarea cols=\"80\" rows=\"5\" name=\"kinf\" value\"\"></textarea>
			<input type=\"submit\" value=\"Request to be added to the White List\" />
			</form>
		</p>
		
		";
		$rejectmessage=$rejectmessage.$r2;
		wp_die("$rejectmessage","Login Access Denied",array('response' => 403));
		exit();
		
	}
	if ($chkcaptcha=='Y') {
    // first set up the red herring pages for login and comment
	$rhnonce=wp_create_nonce('kpgstopspam_redherring');
    $action1=esc_url( site_url( 'wp-login.php', 'login_post' ) );
    $action2=site_url( '/wp-comments-post.php' );
	$rh="
	<div style=\"visibility:hidden;display:none;\">
	<br/>
	<br/>
	<br/>
	<form name=\"loginform1\" id=\"loginform1\" action=\"$action1\" method=\"post\" style=\"visibility:hidden;display:none;\">
	<p>
	<label for=\"user_login\">User Name<br />
	<input type=\"text\" name=\"log\"  value=\"\" size=\"20\"  /></label>
	</p>
	<p>
	<label for=\"user_pass\">Password<br />
	<input type=\"password\" name=\"pwd\"  value=\"\" size=\"20\"  /></label>
	</p>
	<p class=\"forgetmenot\"><label for=\"rememberme\"><input name=\"rememberme\" type=\"checkbox\" checked=\"checked\"  value=\"$rhnonce;\"  />Remember Me</label></p>
	<p class=\"submit\">
	<!-- 
	<input type=\"submit\" name=\"wp-submit\"  value=\"Log In\"  />
	-->
	<input type=\"hidden\" name=\"testcookie\" value=\"1\" />
	</p>
	<input id=\"akismet_comment_nonce\" name=\"akismet_comment_nonce\" value=\"$rhnonce\" type=\"hidden\">
	</form>
	</div>
	<div style=\"visibility:hidden;display:none;\">
	<br/>
	<br/>
	<br/>
	<form action=\"$action2\" method=\"post\" id=\"commentform1\" style=\"visibility:hidden;display:none;\">
	<p><input name=\"author\" id=\"author\" value=\"\" size=\"22\"  aria-required=\"true\" type=\"text\">
	<label for=\"author\"><small>Name (required)</small></label></p>

	<p><input name=\"email\" id=\"email\" value=\"\" size=\"22\"  aria-required=\"true\" type=\"text\">
	<label for=\"email\"><small>Mail (will not be published) (required)</small></label></p>

	<p><input name=\"url\" id=\"url\" value=\"\" size=\"22\" type=\"text\">
	<label for=\"url\"><small>Website</small></label></p>
	<p><textarea name=\"comment\" id=\"comment\" cols=\"58\" rows=\"10\" ></textarea></p>

	<p><!-- 
	<input name=\"submit\" id=\"submit\" value=\"Submit Comment\" type=\"submit\"> 
	-->
	<input name=\"comment_post_ID\" value=\"666\" id=\"comment_post_ID\" type=\"hidden\">
	<input name=\"comment_parent\" id=\"comment_parent\" value=\"0\" type=\"hidden\">
	</p>

	<p style=\"display: none;\"><input id=\"akismet_comment_nonce\" name=\"akismet_comment_nonce\" value=\"$rhnonce\" type=\"hidden\"></p>
	</form>
	</div>
	
	
	";
	


	// try to see if captcha works
		
		$alpha="7F234567BPABCDEFCH4JKNPNXPERSTUVFXYZ"; // some letters removed to prevent confusion
		$rand="";
		
		
		for ($j=0;$j<5;$j++) {
		  $rand.=substr($alpha,rand(0,35),1);
		}
		// try this with ajax
		$url=admin_url('admin-ajax.php')."?action=sfs_captcha";


		set_transient( "KPG_SECRET_WORD".$_SERVER['REMOTE_ADDR'],$rand,60 );
	$cnonce=wp_create_nonce('kpgstopspam_captcha_key');
		$sform="
		<p>You need to prove you are not a robot before you can continue.</p>
			<img src=\"$url\" />
	<form action=\"\" method=\"post\">
	<p>Enter the five letters shown above and press enter</p>
	<input type=\"text\" name=\"captcha_value\"  value=\"\" size=\"6\"  />
	</p>
	<input type=\"submit\"  value=\"\" style=\"display:none;visibility:hidden;\" />
	</p>
	<input  name=\"captcha_key\" value=\"$cnonce\" type=\"hidden\">
	</form>
	<p>(If you cannot see the image clearly, then just refresh the page)</p>
		";
	// save the post info in a transient
	set_transient("KPG_CAPTCHA".$_SERVER['REMOTE_ADDR'],$_POST,60);

		wp_die($rh.$sform,"Login Access Denied - second chance",array('response' => 403));
	exit();
	}
	wp_die("$rejectmessage","Login Access Denied",array('response' => 403));
	exit();
}

function kpg_chk_whitelist_l() {
	// this could be a white list request.
	//sfs_debug_msg("checking white list request");
	@remove_filter('wp_mail','kpg_sfs_reg_check_send_mail'); 
	// we have arrived at a whitelist request 
	//sfs_debug_msg("found white list request");
	//sfs_debug_msg("good nonce on white list request");
	$kinf=$_POST['kinf'];
	$kinf=trim($kinf);
	$kinf=sanitize_text_field($kinf);
	$kinf=remove_accents($kinf);
	$kinf=utf8_decode($kinf);
	$kinf=really_clean($kinf);
	$kip=$_POST['kip'];
	$kip=sanitize_text_field($kip);
	$kip=remove_accents($kip);
	$kip=utf8_decode($kip);
	$kip=really_clean($kip);
	$kem=$_POST['kem'];
	$kem=sanitize_text_field($kem);
	$kem=remove_accents($kem);
	$kem=utf8_decode($kem);
	$kem=really_clean($kem);
	$kau=$_POST['kau'];
	$kau=sanitize_text_field($kau);
	$kau=remove_accents($kau);
	$kau=utf8_decode($kau);
	$kau=really_clean($kau);
	$knot=$_POST['knot'];
	$knot=sanitize_text_field($knot);
	$knot=remove_accents($knot);
	$knot=utf8_decode($knot);
	$knot=really_clean($knot);
	
	if (empty($kinf)) {
		wp_die(__("Empty response. Request not recorded"),__("Access Denied"),array('response' => 403));
		exit();
	}
	if (!empty($kinf)) { // ignore if user did not give a reason
		// got it, update the white list request queue
		// grab the stats and add it to the wlreq array
		$options=kpg_sp_get_options();
		$wlreqmail=$options['wlreqmail'];
		$to=get_option('admin_email');
		$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
		$stats=kpg_sp_get_stats();
		$wlreq=$stats['wlreq'];
		$wlreq[]=array($now,$kip,$kem,$kau,$knot.", Mail sent=$wlreqmail to $to",$kinf);
		$stats['wlreq']=$wlreq;
		update_option('kpg_stop_sp_reg_stats',$stats);
		// check here to see if we should send email
		if ($wlreqmail=='Y') {
			//send email to the admin
			// send the admin an email
			$sturl=admin_url("options-general.php?page=stop-spammer-registrations-plugin/settings/stop-spam-reg-stats.php");
			if ($muswitch=="Y") $sturl=admin_url("network/settings.php?page=stop-spammer-registrations-plugin/settings/stop-spam-reg-stats.php");
			$subject='White list request from blog '.get_bloginfo('name');
			
			$message="
Sysop,
A request has been received from someone who has been marked as a spammer by the STOP SPAMMER plugin.
You have are being notified because you have checked off the box on the settings page indicating that you wanted this email.
The information from the request is:
Time: $now
User IP: ". $kip ."
User email: ". $kem ."
Author Name: ". $kau ."
Spam Reason: ". $knot ."
Users Message: ". $kinf ."

Please be aware that the user has been recognized as a potential spammer. Some spam robots are already filling out the request form with a bogus explanation. If the user is from Ubiquity servers, or is found on dnsbl, Stop Forum Spam, or Akismet, don't let the person into your system unless you are sure the plugin is in error.

You can add this user to your whitelist from the Stop Spammers statistics page. You may wish to clear the cache to make sure the user can get into the system.

$sturl

Stop Spammers Plugin";
			@wp_mail( $to, $subject, $message );
			
		}
		wp_die(__("A white list request has been recorded"),__("Access Pending"),array('response' => 403));
		
		exit();
	}
		

}
function sfs_handle_ajax_captcha_l() {
	if (!extension_loaded('gd') || !function_exists('gd_info')) {
		// show the image without letters perhaps. Maybe make an error png2wbmp
		header("Content-type: image/png");
		readfile('base.png'); // replace this with the error png
		return;
	}
	// displays the captcha image
	// if there are errors then copy the url from the img tag and see what is going on.
    $word=get_transient( "KPG_SECRET_WORD".$_SERVER['REMOTE_ADDR'] );
	// Keith's how to create a captcha image in just a few lines of code	 
	$im    = imagecreatefrompng(realpath(dirname(__FILE__)).'/base.png');
    imagefilter($im, IMG_FILTER_BRIGHTNESS,rand(0,90));
	$font=realpath(dirname(__FILE__)).'/headache.ttf';
	for($j=0;$j<strlen($word);$j++) {
	   kpg_cpatcha_printletter($im,substr($word,$j,1),5+($j*28),60,$font);
	}
    imagefilter($im, IMG_FILTER_SMOOTH,2); // blurs the image just slightly - take out if you need a clearer image.
	header("Content-type: image/png");
 	imagepng($im);
	imagedestroy($im);
	exit(0);
	  
}
 	function kpg_cpatcha_printletter($im,$letter,$x,$y,$font) {
	  $bas=rand(-12,+4);
	  for ($j=0;$j<4;$j++) {
		$c1=rand(70,140);
		$c2=rand(70,140);
		$c3=rand(70,140);
		$sz=rand(24,30);
		$rot=rand(-5,5);
		$color = imagecolorallocate($im, $c1, $c2, $c3);
		$x1=$x+rand(-2,+2);
		$y1=$y+rand(-1,+1)+$bas;
		
		imagettftext($im, $sz, $rot, $x1,$y1, $color, $font, $letter);
	  }
	  return(0);
	}
function kpg_chk_captcha_l() {
	@remove_action('init','kpg_chk_poison'); 
	@remove_action('init','kpg_chk_whitelist'); 
	@remove_action('init','kpg_chk_captcha'); 
	@remove_action('init','kpg_load_all_checks'); 
	@remove_action('init','kpg_sf_mu_load'); 	
    $captcha=$_POST['captcha_value'];
    $word=get_transient( "KPG_SECRET_WORD".$_SERVER['REMOTE_ADDR'] );
	$word=strtoupper($word);
	$captcha=strtoupper($captcha);
	delete_transient( "KPG_SECRET_WORD".$_SERVER['REMOTE_ADDR'] );
	if (!empty($word)&&strlen($word)==5&&$captcha==$word) {
	//echo "checking captcha $word = $captcha";
		// made it exit with $_POST items all set
		// increment the count
		// restore post
		$ip=$_SERVER['REMOTE_ADDR'];
		$pp=get_transient("KPG_CAPTCHA".$_SERVER['REMOTE_ADDR']);
		delete_transient("KPG_CAPTCHA".$_SERVER['REMOTE_ADDR']);
		// copy to the post
		// add the current guy to the good cache and take him out of the bad cache
		$stats=kpg_sp_get_stats();
		$goodips=$stats['goodips'];
		$badips=$stats['badips'];
		$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
		$goodips[$ip]=$now;
		unset($badips[$ip]);
		$cnt=$stats['cntcap'];
		$cnt++;
		$stats['badips']=$badips;
		$stats['goodips']=$goodips;
		$stats['cntcap']=$cnt;
		// save the stats
		update_option('kpg_stop_sp_reg_stats',$stats);
		$_POST=$pp;
		global $kpg_check_sempahore;
		$kpg_check_sempahore=true;
		return;
	}
	//echo "failed $word=$captcha";
		$stats=kpg_sp_get_stats();
		$cnt=$stats['cntncap'];
		$cnt++;
		$stats['cntncap']=$cnt;
		// save the stats
		update_option('kpg_stop_sp_reg_stats',$stats);
	wp_die("CAPTCHA value does no match<br>Go back, refresh, and try again","Login Access Denied",array('response' => 403));
	exit();
}

?>