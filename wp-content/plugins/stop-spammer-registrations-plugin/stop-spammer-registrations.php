<?PHP
/*
Plugin Name: Stop Spammer Registrations Plugin
Plugin URI: http://wordpress.org/plugins/stop-spammer-registrations-plugin/
Description: The Stop Spammer Registrations Plugin checks against Spam Databases to to prevent spammers from registering or making comments.
Version: 5.9.3
Author: Keith P. Graham

This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

// support will end soon.

// mainline for Stop Spammers. 

// just to be absolutely safe
if (!defined('ABSPATH')) exit;

// before going any further, do the mu stuff
$muswitch='N';
$kpg_check_sempahore=false;
/*


multisite works by hooking the get_option and set_options functions so that all stats and options
reference blog #1

There is an option - set only in blog 1 kpg_muswitch. 
If the switch is on, only the network can see and set options and stats.
If it is 'N' then the plugin acts like a simple plugin that is not network aware.

By default starting with version 5.0 the switch is set to 'Y'

*/
if (function_exists('is_multisite') && is_multisite()) {
	$muswitch='Y';
	switch_to_blog(1);
	// get the mu option
	$muswitch=get_option('kpg_muswitch');
	if (empty($muswitch)) $muswitch='Y';
	if ($muswitch!='N') $muswitch='Y';
	restore_current_blog();
	if ($muswitch=='Y') {
		kpg_sp_require('includes/sfr-mu-option-fix.php');
		kpg_sp_require('includes/stop-spammers-mu.php');
		// moved the action links here
		if (has_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'kpg_sp_plugin_action_links' ) ) 
		remove_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'kpg_sp_plugin_action_links' );
		add_filter( 'network_admin_plugin_action_links_' . plugin_basename( __FILE__ ), 'kpg_sp_plugin_action_links_mu' );

	}
} 
// hooks for mu and regular

if ($muswitch=='Y') {
	add_action('init','kpg_sf_mu_load'); // loads up normally	
	// network admin
	add_action('network_admin_menu', 'kpg_sfs_reg_net_admin_menus');

} else {
	add_action('init','kpg_load_all_checks'); 
	// non-network admin
	add_action('admin_menu', 'kpg_sfs_reg_admin_menus');
	add_action('network_admin_menu', 'kpg_sfs_reg_nonet_admin_menus');
}
add_action('init','kpg_chk_poison'); // check to see if we are here through a poison link
add_action('init','kpg_chk_whitelist'); // check to see if we are here through a white list request
add_action('init','kpg_chk_captcha'); // check to see if we are here through a white list request

// premium
// I eventually want to offer premium features, so these functions will eventually be included 
// in a premium file.
kpg_sfs_premium();

function kpg_load_all_checks() {
	
	// remove the hooks so we don't recurse.
	remove_action('init','kpg_load_all_checks'); // loads up normally

	if (function_exists('is_user_logged_in')) { 
		if(is_user_logged_in()) {
			return;
		}
	}
	
	
	kpg_load_all_checks_no_post();

	if(!isset($_POST)||empty($_POST)) { // no post defined
		//$_SESSION['kpg_stop_spammers_time']=time();
		if (! isset($_COOKIE['kpg_stop_spammers_time'])) { // if previous set do not reset
			setcookie( 'kpg_stop_spammers_time', strtotime("now"), strtotime('+1 min'));
		}
		return;
	}

	// I am using a plugin with your-email, your-name fields - might as well test them, too.
	$postfields=array('akismet_comment_nonce','bbp_anonymous_email','email','user_email','user_login',
	'author','your-name','your-email','log','psw','pass1','your_username','post_password',
	'your_email','signup_username','signup_email','user_name','blogname','signup_for',
	'excerpt','blog_name','url','contact_us_enquiry_email'); 
	// future merge this with the options custom trigger fields
	$is_in_post=false;
	foreach ($postfields as $postf) {
		if (array_key_exists($postf,$_POST)||array_key_exists(strtoupper($postf),$_POST)) {
			$is_in_post=true;
			break;
		}
	}
	$grav=false;
	if (!$is_in_post) {
		// did not find it. Check more generally
		foreach($_POST as $key=>$data) {
			$key=strtolower($key);
			if (stripos($key,'name')!==false||
					stripos($key,'signup')!==false||
					stripos($key,'input_')!==false|| // gravity forms??
					stripos($key,'user')!==false||
					stripos($key,'pass')!==false||
					stripos($key,'pwd')!==false||
					stripos($key,'psw')!==false||
					stripos($key,'email')!==false) {
				$is_in_post=true;
				if (stripos($key,'input_')!==false) $grav=true;
				break;
			}
		}
	}
	if (!$is_in_post) {
		// did not find it.
		if (!isset($_COOKIE['kpg_stop_spammers_time'])) { // if previous set do not reset
			setcookie( 'kpg_stop_spammers_time', strtotime("now"), strtotime('+1 min'));
		}
		return;
	}
	// here we can check to see if the posted data is correct
	
	// get the email author and ip
	$em='';
	if (array_key_exists('email',$_POST)) {
		$em=$_POST['email'];
	} else if (array_key_exists('user_email',$_POST)) {
		$em=$_POST['user_email'];
	} else if (array_key_exists('signup_email',$_POST)) {
		$em=$_POST['signup_email'];
	} else if (array_key_exists('bbp_anonymous_email',$_POST)) {
		$em=$_POST['bbp_anonymous_email'];
	} else if (array_key_exists('your-email',$_POST)) {
		$em=$_POST['your-email'];
	} else if (array_key_exists('your_email',$_POST)) {
		$em=$_POST['your_email'];
	} else if (array_key_exists('cntctfrm_contact_email',$_POST)) { // I'm using the contact form from BWS contact form, so might as well support it.
		$em=$_POST['cntctfrm_contact_email'];
	}else if (array_key_exists('contact_us_enquiry_email',$_POST)) { // gravity contact form here
		$em=$_POST['contact_us_enquiry_email'];
	}
	if (!isset($grav)) $grav=false;
	if ($grav) {
		// this is gravity, look for the @ sign to get an email
		foreach ($_POST as $inp=>$val) {
			if (strpos($inp,"input_")!==false) {
			    
				if (strpos($val,'@')!==false) {
					$em=$val;
					break;
				}
			}
		}
	}
	
	if (strpos($em,'@')===false) { // not an email, but a username (or some other crap)
		$em='';
	}
	// see if they have an author or username
	$author='';
	$pwd='';
	// final fix for bbPress from Rob Cain - thanks
	if (array_key_exists('author',$_POST)) {
		$author=$_POST['author'];
	} else if (array_key_exists('user_name',$_POST)) {
		$author=$_POST['user_name'];
	} else if (array_key_exists('your-name',$_POST)) {
		$author=$_POST['your-name'];
	} else if (array_key_exists('your_name',$_POST)) {
		$author=$_POST['your_name'];
	} else if (array_key_exists('user_login',$_POST)) {
		$author=$_POST['user_login'];
		if (array_key_exists('pass1',$_POST)) {
			$pwd=$_POST['pass1'];
		}
	} else if (array_key_exists('your_username',$_POST)) {
		$author=$_POST['your_username'];
	} else if (array_key_exists('cntctfrm_contact_name',$_POST)) {
		$author=$_POST['cntctfrm_contact_name'];
	} else if (array_key_exists('signup_username',$_POST)) {
		$author=$_POST['signup_username'];
	} else if (array_key_exists('log',$_POST)) {
		$author=$_POST['log'];
		if (array_key_exists('pwd',$_POST)) {
			$pwd=$_POST['pwd'];
		}
	} // add your_username your_email
	//echo "\r\n<!--\r\n step 4 \r\n-->\r\n";
	// get the ip 
	@remove_filter('wp_mail','kpg_sfs_reg_check_send_mail'); // we are going to check - remove the email check
	//$ip=kpg_get_ip();
	// now we are only using the actual ip sent to us by the host
	$ip=$_SERVER['REMOTE_ADDR'];
	//  this is called once in "init" no need to call it ever again
	//sfs_debug_msg("kpg_load_all_checks");

	sfs_errorsonoff();
	kpg_sfs_check_load();
	$ansa=kpg_sfs_check($em,$author,$ip,$pwd);
	sfs_errorsonoff('off');
	
	return;
}
function kpg_chk_poison() {
	// check to see if anyone showed up from clicking a poison link
	// added ++liker.profile_URL++ code - seems to be a misguided spam bot
	$sname="";
	if (array_key_exists("SCRIPT_URL",$_SERVER)) $sname=$_SERVER['SCRIPT_URL'];
	if (empty($sname)&&array_key_exists("SCRIPT_URI",$_SERVER)) $sname=$_SERVER['SCRIPT_URI'];
	if (empty($sname)&&array_key_exists("ORIG_PATH_INFO",$_SERVER)) $sname=$_SERVER['ORIG_PATH_INFO'];
	if (empty($sname)&&array_key_exists("REDIRECT_SCRIPT_URL",$_SERVER)) $sname=$_SERVER['REDIRECT_SCRIPT_URL'];
	if (empty($sname)&&array_key_exists("REDIRECT_SCRIPT_URI",$_SERVER)) $sname=$_SERVER['REDIRECT_SCRIPT_URI'];
	if (empty($sname)&&array_key_exists("QUERY_STRING",$_SERVER)) $sname=$_SERVER['QUERY_STRING'];
	$liker=false;
	if (strpos($sname,'liker.profile')!==false) $liker=true;
   
	if($liker ||(isset($_GET)&&!empty($_GET)&&(array_key_exists('commentid',$_GET)||array_key_exists('loginid',$_GET)))) {
		//maybe from a poison link
		$pnonce1=$_GET['commentid'];
		$pnonce2=$_GET['loginid'];
		$ret=true;
		if ($liker||kpg_verify_nonce($pnonce1,'kpgstopspam_poison')||kpg_verify_nonce($pnonce2,'kpgstopspam_poison')) { 
			// hit the poison link
			// add to badips and remove from goodips cache
			$options=kpg_sp_get_options();
			if($options['poison']=='Y') {
				$ret=kpg_sfs_reg_add_user_to_badip();
				// update stats if it's a new ip
				if ($ret) {
					$stats=kpg_sp_get_stats();
					$stats['poisoncnt']++;
					// do the log here
					//add to hist
					$blog='';
					if (function_exists('is_multisite') && is_multisite()) {
						global $blog_id;
						if (!isset($blog_id)||$blog_id!=1) {
							$blog=$blog_id;
						}
					}
					//$ip=kpg_get_ip();
					$ip=$_SERVER['REMOTE_ADDR'];
					$ref="/";
					if (array_key_exists('HTTP_REFERER',$_SERVER)) {
						$ref=$_SERVER['HTTP_REFERER'];
					}
					$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
					$hist=$stats['hist'];
					$hist[$now]=array($ip,"","",$ref,'Poison link',$blog);			
					$stats['hist']=$hist;
					update_option('kpg_stop_sp_reg_stats',$stats);
				}
			}
		}
	}
	return;
}


function stop_spam_check($ip='',$email='',$author='') {
	// this function can be used to call a spam check from other plugins
	// you can call this and just check the ip with stop_spam_check() and the plugin will do all the work.
	// your code is:
/****************************************
	if (function_exists('stop_spam_check')) stop_spam_check();
*****************************************/
	// add that line to plugins or themes when you need to check a login or comment.
	// if you know the email, author, you can check those also. If you have a better way of getting ip you can use it.
	// do not call on every page - only call when there is a new comment, registration or login, otherwise it will slow your site quite a bit.
	if (empty($ip)) $ip=$_SERVER['REMOTE_ADDR']; // $ip=kpg_get_ip();
	sfs_errorsonoff();
	//sfs_debug_msg("stop_spam_check");
	kpg_sfs_check_load();
	$ansa=kpg_sfs_check($email,$author,$ip,'');
	sfs_errorsonoff('off');
	return $ansa;
}
function kpg_load_all_checks_no_post() {
	add_action( 'template_redirect', 'kpg_sfs_check_404s' ); // check if bogus search for wp-login
	// optional checks
	add_filter('wp_mail','kpg_sfs_reg_check_send_mail');
	add_action('comment_form_before','kpg_sfs_red_herring_comment'); // moved to comment form before
	add_filter('login_message','kpg_sfs_red_herring_login');	
	add_filter('before_signup_form','kpg_sfs_red_herring_signup');
	//$options=kpg_sp_get_options();

	return;
}
/************************************************************
*
* show a bogus form. If the form is hit then this is a spammer
*
*************************************************************/
function kpg_sfs_red_herring_comment($query) {
	if (function_exists('is_feed') && is_feed()) return $query;
	@remove_action('comment_form_before','kpg_sfs_red_herring_comment');
	@remove_filter('before_signup_form','kpg_sfs_red_herring_signup');	 
	@remove_filter('login_message','kpg_sfs_red_herring_login');	
	// check (late) to see if we should continue
	$options=kpg_sp_get_options();
// poison link
	if ($options['poison']=='Y') {
		$pnonce=wp_create_nonce('kpgstopspam_poison');
		echo '<span style="position:absolute;left:-100px;width:0;visibility:hidden;display:none;">
		<a style="visibility:hidden;" href="'.site_url().'?commentid='.$pnonce.'" rel="nofollow">Add Comment</a>
		<a style="visibility:hidden;" href="'.site_url().'?loginid='.$pnonce.'" rel="nofollow">Register</a>
		</span>';
	}
	
	
	
	if (array_key_exists('redherring',$options)&&$options['redherring']=='Y') {
		// continue
	} else {
		return $query;
	}
	// these checks probably are not useful in this version.
	$sname=kpg_sfs_get_SCRIPT_URI();
	if (strpos($sname,'wp-login.php')!==false) return $query; // only add fake comments to comment pages?
	if (empty($sname)) return $query;
	if (strpos($sname,'/feed')) return $query;
	$rhnonce=wp_create_nonce('kpgstopspam_redherring');
	?>
	<div style="visibility:hidden;display:none;">
	<br/>
	<br/>
	<br/>
	<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="commentform1" style="visibility:hidden;display:none;">
	<p><input name="author" id="author" value="" size="22"  aria-required="true" type="text">
	<label for="author"><small>Name (required)</small></label></p>

	<p><input name="email" id="email" value="" size="22"  aria-required="true" type="text">
	<label for="email"><small>Mail (will not be published) (required)</small></label></p>

	<p><input name="url" id="url" value="" size="22" type="text">
	<label for="url"><small>Website</small></label></p>
	<p><textarea name="comment" id="comment" cols="58" rows="10" ></textarea></p>

	<p><!-- 
	<input name="submit" id="submit" value="Submit Comment" type="submit"> 
	-->
	<input name="comment_post_ID" value="<?php echo get_the_ID();?>" id="comment_post_ID" type="hidden">
	<input name="comment_parent" id="comment_parent" value="0" type="hidden">
	</p>

	<p style="display: none;"><input id="akismet_comment_nonce" name="akismet_comment_nonce" value="<?php echo $rhnonce;?>" type="hidden"></p>
	</form>
	</div>
	<?php
	return $query;
}

/************************************************************
*
* show a bogus form. If the form is hit then this is a spammer
*
*************************************************************/
function kpg_sfs_red_herring_signup() {
	@remove_action('comment_form_before','kpg_sfs_red_herring_comment');
	@remove_filter('before_signup_form','kpg_sfs_red_herring_signup');	 
	@remove_filter('login_message','kpg_sfs_red_herring_login');
	// here we don't check options until it is actually time to do the red herring
	$options=kpg_sp_get_options();
	if (array_key_exists('redherring',$options)&&$options['redherring']=='Y') {
		// continue
	} else {
		return;
	}

	$rhnonce=wp_create_nonce('kpgstopspam_redherring');
	// put a bugus signup form with the akismet nonce - maybe doesn't work but it might
	?>
	<div style="visibility:hidden;display:none;">
	<br/>
	<br/>
	<br/>
	<form id="setupform1" method="post" action="wp-signup.php" style="visibility:hidden;display:none;">

	<input type="hidden" name="stage" value="validate-user-signup"  style="visibility:hidden;display:none;" />
	<p style="visibility:hidden;display:none;"><input id="akismet_comment_nonce" name="akismet_comment_nonce" value="<?php echo $rhnonce;?>" type="hidden"></p>		
	<p>
	<input id="signupblog" type="radio" name="signup_for" value="blog"  checked='checked' />
	<label class="checkbox" for="signupblog">Gimme a site!</label>
	<br />
	<input id="signupuser" type="radio" name="signup_for" value="user"  />
	<label class="checkbox" for="signupuser">Just a username, please.</label>
	</p>
	<!-- 
	<p class="submit"><input type="submit" class="submit" value="Next" /></p>
	-->
	</form>
	</div>

	<?php
	return;
} // end if red herring signup
/************************************************************
*
* show a bogus form. If the form is hit then this is a spammer
*
*************************************************************/
function kpg_sfs_red_herring_login($message) {
	@remove_action('comment_form_before','kpg_sfs_red_herring_comment');
	@remove_filter('before_signup_form','kpg_sfs_red_herring_signup');	 
	@remove_filter('login_message','kpg_sfs_red_herring_login');	
	// here we don't check options until it is actually time to do the red herring
	$options=kpg_sp_get_options();
	if (array_key_exists('redherring',$options)&&$options['redherring']=='Y') {
		// continue
	} else {
		return $message;
	}
	$rhnonce=wp_create_nonce('kpgstopspam_redherring');
	?>
	<div style="visibility:hidden;display:none;">
	<br/>
	<br/>
	<br/>


	<form name="loginform1" id="loginform1" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post" style="visibility:hidden;display:none;">
	<p>
	<label for="user_login">User Name<br />
	<input type="text" name="log"  value="" size="20"  /></label>
	</p>
	<p>
	<label for="user_pass">Password<br />
	<input type="password" name="pwd"  value="" size="20"  /></label>
	</p>
	<p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" checked="checked"  value="<?php echo $rhnonce; ?>"  />Remember Me</label></p>
	<p class="submit">
	<!-- 
	<input type="submit" name="wp-submit"  value="Log In"  />
	-->
	<input type="hidden" name="testcookie" value="1" />
	</p>
	<input id="akismet_comment_nonce" name="akismet_comment_nonce" value="<?php echo $rhnonce;?>" type="hidden">
	</form>



	</div>
	<?php
	return $message;
}


/************************************************************
* 	kpg_sfs_reg_check_send_mail()
*	Hooked from wp_mail
*	this returns the params
*************************************************************/
function kpg_sfs_reg_check_send_mail($stuff) {
	@remove_filter('wp_mail','kpg_sfs_reg_check_send_mail');
	$options=kpg_sp_get_options();
	if (array_key_exists('chkwpmail',$options)&&$options['chkwpmail']=='Y'){
		// continue
	} else {
		return $stuff;
	}
	if(is_user_logged_in()) {
		return $stuff;
	}
	$email='';
	$header=array();
	if (is_array($stuff)&&array_key_exists('header',$stuff)) $header=$stuff['header'];
	if (is_array($header)&&array_key_exists('from',$stuff)) $email=$stuff['from'];
	$from_name='';
	$from_email=$email;
	if ( strpos($email, '<' ) !== false ) {
		$from_name = substr( $email, 0, strpos( $email, '<' ) - 1 );
		$from_name = str_replace( '"', '', $from_name );
		$from_name = trim( $from_name );
		$from_email = substr( $email, strpos( $email, '<' ) + 1 );
		$from_email = str_replace( '>', '', $from_email );
		$from_email = trim( $from_email );
	}
	// get the ip 
	//$ip=kpg_get_ip();
	$ip=$_SERVER['REMOTE_ADDR'];
	// now call the generic checker
	//sfs_debug_msg("kpg_sfs_reg_check_send_mail");
	sfs_errorsonoff();
	kpg_sfs_check_load();
	kpg_sfs_check($from_email,$from_name,$ip); 
	sfs_errorsonoff('off');
	return $stuff;

}
function kpg_sfs_get_SCRIPT_URI() {
	$sname='';
	if (array_key_exists("SCRIPT_URI",$_SERVER)) {
		$sname=$_SERVER["SCRIPT_URI"];	
	}
	if (empty($sname)) {
		$sname=$_SERVER["REQUEST_URI"];	
	}
	return $sname;

}
/************************************************************
* 	kpg_sfs_check_404s()
*	
*	If there is a 404 error on wp-login it is a spammer 
*   This just caches badips for spiders trolling for a login
*************************************************************/
function kpg_sfs_check_404s() {
	sfs_errorsonoff();
	kpg_sfs_check_404();
	sfs_errorsonoff('off');
	return;
}
function kpg_sfs_check_404() {
	// fix request_uri on IIS
	remove_action('template_redirect', 'kpg_sfs_check_404s');
	if (!isset($_SERVER['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
		if (isset($_SERVER['QUERY_STRING'])) { 
			$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; 
		}
	}	
	if (!array_key_exists('SCRIPT_URI',$_SERVER)) {
		$sname=$_SERVER["REQUEST_URI"];
		if (strpos($sname,'?')!==false) $sname=substr($sname,0,strpos($sname,'?'));
		$_SERVER['SCRIPT_URI']=$sname;
	}
	if (!is_404()) return;
	$plink = $_SERVER['REQUEST_URI']; 
	if (strpos($plink,'?')!==false)  $plink=substr($plink,0,strpos($plink,'?'));
	if (strpos($plink,'#')!==false)  $plink=substr($plink,0,strpos($plink,'#'));
	$plink=basename($plink);
	if (strpos($plink."\t","wp-signup.php\t")===false 
			&& strpos($plink."\t","wp-register.php\t")===false // where is this?
			&& strpos($plink."\t","wp-comments-post.php\t")===false
			&& strpos($plink."\t","xmlrpc.php\t")===false) {
		return;
	}

	$options=kpg_sp_get_options();	
	// check to see if we should even be here
	if (!array_key_exists('chkwplogin',$options) || $options['chkwplogin']!='Y') return;	
	
	//$ip=kpg_get_ip();
	$ip=$_SERVER['REMOTE_ADDR'];
	// check the white lists to prevent accidental blockage
	$wlist=$options['wlist'];
	if ((kpg_sp_searchi($ip,$wlist))) {
		return;
	}
	
	
	$stats=kpg_sp_get_stats();

	// have a bogus hit on a login or signup
	// register the bad ip
	$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
	$badips=$stats['badips'];
	if (!empty($ip)) $badips[$ip]=$now;
	asort($badips);
	$stats['badips']=$badips;
	// put into the history list
	$blog='';
	if (function_exists('is_multisite') && is_multisite()) {
		global $blog_id;
		if (!isset($blog_id)||$blog_id!=1) {
			$blog=$blog_id;
		}
	}
	$hist=$stats['hist'];
	$hist[$now]=array($ip,'-','-',$plink,"404 on $plink, added to reject cache.",$blog);
	$hist[$now][4]="404 on $plink, added to reject cache.";
	$stats['hist']=$hist;
	update_option('kpg_stop_sp_reg_stats',$stats);
	return;
}


/************************************************************
*  function kpg_sfs_check_admin()
* Checks to see if the current admin can login
*************************************************************/
register_activation_hook( __FILE__, 'kpg_sfs_check_admin' );
$sfs_check_activation=substr(md5(uniqid(rand(), true)), 16, 16);
function kpg_sfs_check_admin() {
	global $sfs_check_activation;
	$options=kpg_sp_get_options();
	$options['firsttime']='Y';
	kpg_sfs_reg_add_user_to_whitelist($options); // also saves options

}	

// this checks to see if there is an ip forwarded involved here and corrects the IP
function kpg_get_ip() {
	// took out cloudflare - ruined everything
	
	$ip=$_SERVER['REMOTE_ADDR'];
	// Opera turbo? ["HTTP_X_FORWARDED_FOR"]
	if (array_key_exists('HTTP_X_FORWARDED_FOR',$_SERVER)) {
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}  else if (array_key_exists('X_FORWARDED_FOR',$_SERVER)) {
		$ip=$_SERVER['X_FORWARDED_FOR'];
	}  else if (array_key_exists('HTTP-X-FORWARDED-FOR',$_SERVER)) {
		$ip=$_SERVER['HTTP-X-FORWARDED-FOR'];
	} else if (array_key_exists('X-FORWARDED-FOR',$_SERVER)) {
		$ip=$_SERVER['X-FORWARDED-FOR'];
	} else {
		// search for lower case versions
		if (function_exists('getallheaders')) {
			$hlist=getallheaders();
			foreach ($hlist as $key => $data) {
				// can be X-FORWARDED-FOR or HTTP-X-FORWARDED-FOR upper or lower case
				if (strpos(strtoupper($key),'X-FORWARDED-FOR')!==false) { 
					$ip=$data;
					break;
				}
			}
		}
	}
	// some of these return a list of ips with commas - check for a list
	$ip=trim($ip);
	$ip=trim($ip,',');
	if (strpos($ip,',')!==false) {
		$ips=explode(',',$ip);
		$ip=trim($ips[count($ips)-1]); // gets the last ip - most likely to be spoofed?
	}
	if ((ip2long($ip) === FALSE) || (ip2long($ip) === -1)) $ip = ''; // way to to test for a valid ip

	if (empty($ip)) $ip=$_SERVER['REMOTE_ADDR']; // in case I screwed it up
	return $ip;
}
// still getting errors from bad data. I am now stripping all but ascii characters from 32 to 126
// email and user ideas are now plain 7 bit ascii as our founding fathers intended.
// there has to be a built-in php function to do this, but I did not find it. 
// There is an MB_ convert, but it did not work on all of my php hosts, so I think it may not be part of a standard install
function really_clean($s) {
	// try to get all non 7-bit things out of the string
	if (empty($s)) return $s;
	$ss=array_slice(unpack("c*", "\0".$s), 1);
	if (empty($ss)) return $s;
	$s='';
	for ($j=0;$j<count($ss);$j++) {
		if ($ss[$j]<127&&$ss[$j]>31) $s.=pack('C',$ss[$j]);
	}
	return $s;
}

function kpg_sfs_reg_check($actions,$comment) {
    if (!ipChkk()) return $actions;
	$email=urlencode($comment->comment_author_email);
	$ip=$comment->comment_author_IP;
	$action="<a title=\"Check Stop Forum Spam (SFS)\" target=\"_stopspam\" href=\"http://www.stopforumspam.com/search.php?q=$ip\">Check SFS</a> |
	<a title=\"Check Project HoneyPot\" target=\"_stopspam\" href=\"http://www.projecthoneypot.org/search_ip.php?ip=$ip\">Check HoneyPot</a>";
	$actions['check_spam']=$action;
	return $actions;
}

function kpg_sfs_reg_report($actions,$comment) {
    if (!ipChkk()) return $actions;

	// need to add a new action to the list
	
	$email=urlencode($comment->comment_author_email);
	if (empty($email)){
		return $actions;
	}
	$options=kpg_sp_get_options();
	extract($options);
	
	$ID=$comment->comment_ID;
	$email=urlencode($comment->comment_author_email);
	$exst='';
	$uname=urlencode($comment->comment_author);
	$ip=$comment->comment_author_IP;
	$content=$comment->comment_content;
	
	$evidence=$comment->comment_author_url;
	if (empty($evidence)) $evidence='';
	preg_match_all('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@',$content, $post, PREG_PATTERN_ORDER);
	if (is_array($post)&&is_array($post[1])) $urls1 = array_unique($post[1]); else $urls1 = array(); 
	//bbcode
	preg_match_all('/\[url=(.+)\]/iU', $content, $post, PREG_PATTERN_ORDER);
	if (is_array($post)&&is_array($post[0])) $urls2 = array_unique($post[0]); else $urls2 = array(); 
	$urls3=array_merge($urls1,$urls2);
	if (is_array($urls3)) $evidence.="\r\n".implode("\r\n",$urls3);	
	$evidence=urlencode(trim($evidence,"\r\n"));
	if (strlen($evidence)>128) $evidence=substr($evidence,0,125).'...';
	$target=" target=\"_blank\" ";
	$href="href=\"http://www.stopforumspam.com/add.php?username=$uname&email=$email&ip_addr=$ip&evidence=$evidence&api_key=$apikey\" ";
	$onclick='';
	$blog=1;
	global $blog_id;
	if (!isset($blog_id)||$blog_id!=1) {
		$blog=$blog_id;
	}
	$ajaxurl=admin_url('admin-ajax.php');
	if (!empty($apikey)) {
		//$target="target=\"kpg_sfs_reg_if1\"";
		// make this the xlsrpc call.
		$href="href=\"#\"";
		$onclick="onclick=\"sfs_ajax_report_spam(this,'$ID','$blog','$ajaxurl');return false;\"";
	}
	if (!empty($email)) {
		$action="<a $exst title=\"Report to Stop Forum Spam (SFS)\" $target $href $onclick class='delete:the-comment-list:comment-$ID::delete=1 delete vim-d vim-destructive'>Report to SFS</a>";
		$actions['report_spam']=$action;
	}
	return $actions;
}

function kpg_sfs_reg_admin_menus() {
	if(!current_user_can('manage_options')) return;
	$options=kpg_sp_get_options();
	kpg_sfs_reg_add_user_to_whitelist($options);
	//add_options_page('Stop Spammers', 'Stop Spammers', 'manage_options','stopspammersoptions','settings/stop-spam-reg-options.php');
	$ppath=plugin_dir_path( __FILE__ ).'/settings/';
	add_options_page('Stop Spammers', 'Stop Spammers','manage_options',$ppath.'stop-spam-reg-options.php');
	add_options_page('Stop Spammers History', 'Spammer History', 'manage_options',$ppath.'stop-spam-reg-stats.php');
	add_action('rightnow_end', 'kpg_sp_rightnow');
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'kpg_sp_plugin_action_links' );
	add_filter('comment_row_actions','kpg_sfs_reg_check',1,2);	
	add_filter('comment_row_actions','kpg_sfs_reg_report',1,2);	
}

function kpg_sfs_reg_net_admin_menus() {
	if(!current_user_can('manage_network_options')) return;
	$ppath=plugin_dir_path( __FILE__ ).'/settings/';
	add_submenu_page('settings.php', 'Stop Spammers', 'Stop Spammers', 'manage_options', $ppath.'stop-spam-reg-options.php');
	add_submenu_page('settings.php', 'Stop Spammers History', 'Spammer History', 'manage_options',$ppath.'stop-spam-reg-stats.php');
	add_submenu_page('settings.php', 'Stop Spammers MU', 'Spammer Multisite', 'manage_options',$ppath.'stop-spammers-mu-settings.php');
	add_action('mu_rightnow_end','kpg_sp_rightnow');
	add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'kpg_sp_plugin_action_links' );
	add_filter('comment_row_actions','kpg_sfs_reg_check',1,2);	
	add_filter('comment_row_actions','kpg_sfs_reg_report',1,2);	
}


function kpg_sfs_reg_nonet_admin_menus() {
	if(!current_user_can('manage_network_options')) return;
	$ppath=plugin_dir_path( __FILE__ ).'/settings/';
	add_submenu_page('settings.php', 'Stop Spammers MU', 'Spammer Multisite', 'manage_options',$ppath.'stop-spammers-mu-settings.php');
}




function kpg_sp_plugin_action_links( $links ) {
	$options=kpg_sp_get_options();
	$me=$options['options_link'];
	if (!empty($me))  {
		$links[] = "<a href=\"$me\">".__('Settings').'</a>';
	} 
	return $links;
}


function kpg_sfs_reg_uninstall() {
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	delete_option('kpg_stop_sp_reg_options'); 
	delete_option('kpg_stop_sp_reg_stats'); 
	return;
}  



if ( function_exists('register_uninstall_hook') ) {
	register_uninstall_hook(__FILE__, 'kpg_sfs_reg_uninstall');
}

function kpg_sp_rightnow() {
	if (has_filter( 'mu_rightnow_end', 'kpg_sp_rightnow_mu' ) ) 
	return;
	$stats=kpg_sp_get_stats();
	extract($stats);
	$options=kpg_sp_get_options();
	$me=$options['history_link'];
	if (empty($me)) return;
	if ($spmcount>0) {
		// steal the akismet stats css format 
		// get the path to the plugin
		echo "<p><a style=\"font-style:italic;\" href=\"$me\">Stop Spammers</a> has prevented $spmcount spammers from registering or leaving comments.";
		echo"</p>";
	} else {
		echo "<p><a style=\"font-style:italic\" href=\"$me\">Stop Spammers</a> has not stopped any spammers, yet.";
		echo"</p>";
	}
	if (count($wlreq)==1) {
		echo "<p><a style=\"font-style:italic;\" href=\"$me\">".count($wlreq)." user</a> has been denied access and requested that you add them to the white list";
		echo"</p>";
	} else if (count($wlreq)>0) {
		echo "<p><a style=\"font-style:italic;\" href=\"$me\">".count($wlreq)." users</a> have been denied access and requested that you add them to the white list";
		echo"</p>";
	}
	// now we have to warn about using cloudflare.
	if (array_key_exists('HTTP_CF_CONNECTING_IP',$_SERVER)&& !function_exists( 'cloudflare_init' )) {
		echo "<p style=\"color:red;font-style::italic;\">
		CloudFlare Remote IP address detected. Please install the <a href=\"http://wordpress.org/plugins/cloudflare/\">CloudFlare Plugin</a>.
	</p>";
	}
	
}
if (ipChkk()) {
add_action('wp_ajax_nopriv_sfs_sub', 'sfs_handle_ajax_sub');	
add_action('wp_ajax_sfs_sub', 'sfs_handle_ajax_sub');	
add_action('wp_ajax_sfs_check', 'sfs_handle_ajax_check');	// used to check if ajax reporting works
}
add_action('wp_ajax_nopriv_sfs_captcha', 'sfs_handle_ajax_captcha');	// displays the image
add_action('wp_ajax_sfs_captcha', 'sfs_handle_ajax_captcha');	// displays the image

/******************************************
* try ajax version of reporting
* right out of the api playbook
******************************************/
if (ipChkk()) {
	add_action('admin_head', 'sfs_handle_ajax_new');
}
function kpg_verify_nonce($a,$b) {
	if (function_exists('wp_verify_nonce')) {
		return wp_verify_nonce($a, $b);
	}
	return false;
}

function kpg_sfs_get_admin_options($file) {
	// network path
	// wp-admin/network/settings.php
	$ppath=plugin_dir_path( __FILE__ ).'/settings/';
	return $ppath;
}

// load the optional files
// use includes so as to make the core plugin smaller when not working

function kpg_sfs_check_load() {
	// these are the spam checking functions
	require_once('includes/stop-spam-reg-checks.php');
}
//
// these functions have been moved to the utility include to save load time if plugin is not needed.
//
function kpg_append_file($filename,$content) {
	if (!function_exists('kpg_append_file_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_append_file_l($filename,$content);
}
function kpg_read_file($filename) {
	if (!function_exists('kpg_read_file_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_read_file_l($filename);
}
function kpg_file_exists($filename) {
	if (!function_exists('kpg_file_exists_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_file_exists_l($filename);
}
function kpg_file_delete($filename) {
	if (!function_exists('kpg_file_delete_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_file_delete_l($filename);
}
function sfs_errorsonoff($old=null) {
	if (!function_exists('sfs_errorsonoff_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return sfs_errorsonoff_l($old);
}
function sfs_ErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
	if (!function_exists('sfs_ErrorHandler_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return sfs_ErrorHandler_l($errno, $errmsg, $filename, $linenum, $vars);
}
function sfs_handle_ajax_sub($data) {
	if (!function_exists('sfs_handle_ajax_sub_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return sfs_handle_ajax_sub_l($data);
}
function sfs_handle_ajax_check($data) {
	if (!function_exists('sfs_handle_ajax_check_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return sfs_handle_ajax_check_l($data);
}
function sfs_handle_ajax_new() {
	if (!function_exists('sfs_handle_ajax_new_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return sfs_handle_ajax_new_l();
}
function sfs_handle_ajax_captcha() {
	if (!function_exists('sfs_handle_ajax_captcha_l')) kpg_sp_require('includes/stop-spam-deny.php');
	return sfs_handle_ajax_captcha_l();
}
// search functions
function kpg_sp_searchi($needle,$haystack) {
	if (!function_exists('kpg_sp_searchi_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_sp_searchi_l($needle,$haystack);
}

function kpg_sp_search_ip($needle,$haystack) {
	if (!function_exists('kpg_sp_search_ip_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_sp_search_ip_l($needle,$haystack);
}
function kpg_sp_searchi_ip($needle,$haystack) {
	if (!function_exists('kpg_sp_searchi_ip_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_sp_searchi_ip_l($needle,$haystack);
}

function kpg_ip_range($ipr,$ip) {
	if (!function_exists('kpg_ip_range_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_ip_range_l($ipr,$ip);
}
function kpg_sp_searchL($needle,$haystack) {
	if (!function_exists('kpg_sp_searchL_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_sp_searchL_l($needle,$haystack);
}
function kpg_sfs_reg_getafile($f) {
	if (!function_exists('kpg_sfs_reg_getafile_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_sfs_reg_getafile_l($f);
}
function kpg_sfs_reg_add_user_to_whitelist($options) {
	if (!function_exists('kpg_sfs_reg_add_user_to_whitelist_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_sfs_reg_add_user_to_whitelist_l($options);
}
function kpg_sfs_reg_add_user_to_blacklist() {
	if (!function_exists('kpg_sfs_reg_add_user_to_blacklist_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_sfs_reg_add_user_to_blacklist_l($options);
}
function kpg_sfs_reg_add_user_to_badip() {
	if (!function_exists('kpg_sfs_reg_add_user_to_badip_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return kpg_sfs_reg_add_user_to_badip_l($options);
}
// late options load
function kpg_sp_get_stats() {
	if (!function_exists('kpg_sp_get_stats_l')) kpg_sp_require('includes/stop-spam-reg-get-options.php');
	return kpg_sp_get_stats_l();
}
function kpg_sp_get_options() {
	if (!function_exists('kpg_sp_get_options_l')) kpg_sp_require('includes/stop-spam-reg-get-options.php');
	return kpg_sp_get_options_l();
}
function sfs_debug_msg($msg) {
	if (!function_exists('sfs_debug_msg_l')) kpg_sp_require('includes/stop-spam-utils.php');
	return sfs_debug_msg_l($msg);
}
function kpg_reject_spam($rejectmessage,$notify,$chkcaptcha,$whodunnit) {
 	if (!function_exists('kpg_reject_spam_l')) kpg_sp_require('includes/stop-spam-deny.php');
	return kpg_reject_spam_l($rejectmessage,$notify,$chkcaptcha,$whodunnit);
}


function stop_spam_widget_register() {
    if (!class_exists('Stop_Spam_Widget')) {
		kpg_sp_require('includes/stop-spam-widget.php');
	}
	register_widget( 'Stop_Spam_Widget' );
}
add_action( 'widgets_init', 'stop_spam_widget_register' );

function kpg_chk_captcha() {
// check to see if we are in a post from the captcha
	if(isset($_POST)&&!empty($_POST)&&array_key_exists('captcha_key',$_POST)) {
		$knonce=$_POST['captcha_key'];
		if (kpg_verify_nonce($knonce,'kpgstopspam_captcha_key')) {
			if (!function_exists('kpg_chk_captcha_l')) kpg_sp_require('includes/stop-spam-deny.php');
			return kpg_chk_captcha_l();
		}
    }
 
}


function kpg_chk_whitelist() {
	if(isset($_POST)&&!empty($_POST)&&array_key_exists('knotify_key',$_POST)) {
		$knonce=$_POST['knotify_key'];
		if (kpg_verify_nonce($knonce,'kpgstopspam_wlrequest')) {
			if (!function_exists('kpg_chk_whitelist_l')) kpg_sp_require('includes/stop-spam-deny.php');
    echo "kpg_chk_whitelist";
			return kpg_chk_whitelist_l();
		}
	}
}


function kpg_sp_require($file) {
	// require once takes a moment to load a file, so you can't call functions immediately
	// this is a flaw or even a bug in PHP 5.
	// this tries to load the file in one function so it can be called from another.
	require_once($file);
}

// move kpg_sfs_premium to an include file in next version.
// 
function kpg_sfs_premium() {
	global $wp_version;
	if(version_compare($wp_version, "3.1", "<"))return;
	add_action('user_register', 'kpg_new_user_ip');
	add_action('wp_login', 'kpg_log_user_ip', 10, 2);
}
function kpg_new_user_ip($user_id) {
	// add the users ip to new users
	//$ip=kpg_get_ip();
	 $ip=$_SERVER['REMOTE_ADDR'];
	 update_user_meta($user_id, 'signup_ip', $ip);
}
function kpg_log_user_ip($user_login="", $user="") {
    if (empty($user)) return;
    if (empty($user_login)) return;
	// add the users ip to new users
	if (!isset($user->ID)) return;
	$user_id=$user->ID;
	//$ip=kpg_get_ip();
	$ip=$_SERVER['REMOTE_ADDR'];
	$oldip=get_user_meta($user_id,  'signup_ip', true );
	if (empty($oldip) || $ip!=$oldip) {
		update_user_meta($user_id, 'signup_ip', $ip);
	}
}
// add user ip to user maint 
add_action('manage_users_custom_column',  'kpg_sfs_ip_column', 10, 3);
if ( is_multisite() ) {
        add_filter('wpmu_users_columns', 'kpg_sfs_ip_column_head');
} else {
         add_filter('manage_users_columns', 'kpg_sfs_ip_column_head');
}
function kpg_sfs_ip_column_head($column_headers) {
   $column_headers['signup_ip'] = 'User IP';
    return $column_headers;

}
function kpg_sfs_ip_column($value, $column_name, $user_id) {
    if (!ipChkk()) return $value;
	// get the ip for this column
	if ($column_name == 'signup_ip' ) {
		$signup_ip = get_user_meta($user_id, 'signup_ip', true);
		$signup_ip2=$signup_ip;
		$ipline="";
		if (!empty($signup_ip)) {
			$ipline = apply_filters( 'ip2link', $signup_ip2 ); // if the ip2link plugin is installed.
			// now add the check 
			$user_info = get_userdata($user_id);
			$useremail=urlencode($user_info->user_email); // for reporting
			$userurl=urlencode($user_info->user_url); 
			$username=$user_info->display_name; 
			
			$action=" <a title=\"Check Stop Forum Spam (SFS)\" target=\"_stopspam\" href=\"http://www.stopforumspam.com/search.php?q=$signup_ip\">Check SFS</a>, 
			<a title=\"Check Project HoneyPot\" target=\"_stopspam\" href=\"http://www.projecthoneypot.org/search_ip.php?ip=$signup_ip\">Check HoneyPot</a>";
			
			$options=kpg_sp_get_options();
			$apikey=$options['apikey'];
			if (!empty($apikey)) {
				$action=$action . ", <a title=\"Check Stop Forum Spam (SFS)\" target=\"_stopspam\" href=\"http://www.stopforumspam.com/add.php?username=$username&email=$useremail&ip_addr=$signup_ip&evidence=$userurl&api_key=$apikey\">Report to SFS</a>";
			
			}
			return $ipline.'<span style="font-size:.8em">'.$action.'</span>';
		}
		return "_";
	}
	return $value;

}	

function kpg_hook_user_notification() {
	// trying to replace the new user notifications function. If it does not exist, then it must
	// be here. I think it loads on pluggable, so I might try in init before pluggable loads.
	if ( !function_exists('wp_new_user_notification') ) {
		function wp_new_user_notification($user_id, $plaintext_pass = '') {
			$user = get_userdata( $user_id );
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
			$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
			$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";
			// additional line to link back to moderation page
			$message.="\r\n Check users ".admin_url("users.php")." \r\n";
			// done
			@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
			if ( empty($plaintext_pass) )
				return;
			$message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
			$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
			$message .= wp_login_url() . "\r\n";
			wp_mail($user->user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
		}
	}
}
	kpg_hook_user_notification(); // hook user notification if user is in post. Might be preempted by pluggable. We'll see
// turns external site (like sfs) on and off. Return false to disable lookups.
function ipChkk() {
	return true;
}







?>