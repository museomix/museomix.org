<?php
/**
 * @package JPUserRegistrationBlacklist
 * @version 1.7.2
 */
/*
Plugin Name: JP User Registration Blacklist
Plugin URI: https://wordpress.org/plugins/jp-user-registration-blacklist
Description: Apply comment IP and e-mail address blacklist rules to user registrations.  Puts user's IP in user's website field.  Solve a simple math problem to register.
Author: Justin Parr
Version: 1.7.2
Author URI: http://justinparrtech.com
*/

// **************** SETTINGS INCLUDE *************************
require_once basename( __FILE__,'.php').'_Settings.php';


// **************** READ SETTINGS ****************************
$JPURB_Opts=get_option(JPURB_option_name(), JPURB_default_all() );


// **************** SEED FOR MATH PROBLEM ************************
function JP_seed () {
	global $JPURB_Opts;

	return $JPURB_Opts['seed'];
}

// *************** ADD A MATH PROBLEM AND EMAIL TOKEN TO THE USER REG FORM ***************
add_action('register_form','JP_verifyMath_register_form');
function JP_verifyMath_register_form (){
	global $JPURB_Opts;

	$jpdbg="hidden";
	//$jpdbg="text";


	$c=$_POST['JPREG'];
	if ($c=="") {
		// *** Data for Math problem ***
		$a=mt_rand(101,900);
		$b=mt_rand(1,10);
		$c=$a+$b+JP_seed();

		// *** E-mail token ***
		$aa=mt_rand(1,9);
		for ($i=1;$i<8;$i++)
			$aa=$aa.mt_rand(0,9);
	}
	else {
		// *** Create two new math factors based on original answer ***
		$b=mt_rand(1,10);
		$a=$c-JP_Seed()-$b;

		// *** Read existing e-mail token ***
		$aa=$_POST["TTT"];
	}

	$d=$a+$b;
	$f=$JPURB_Opts['MathProblemFieldName'];

	if($jpdbg=="text") {
		echo("<BR><BR>");
		foreach($_POST as $k=>$v)
			echo("$k = $v<BR>");
	}

	if($_POST[$f]!=$d) {
		?>
		<p>
		<label for="<?php echo("$f"); ?>">Solve: <?php echo("Add $a and $b "); ?><br />
		<input type="text" name="<?php echo("$f"); ?>" id="<?php echo("$f"); ?>" class="input" value="0" size="25" /></label>
		</p>
		<?php
	}
	else {

		// *** Construct e-mail details ***
		$uu=get_bloginfo('url');
		$bb=get_bloginfo();
		$hh=array("content-type: text/html");
		$ss='['.$bb.'] User Activation Token';
		$mm='Thanks for registering with us!<BR><BR>';
		$mm=$mm.'Here is your token: <B>'.substr($_POST["TTT"],2,4).'</B><BR><BR>';
		$mm=$mm.'Enter this in the "Token" field, and click REGISTER.<BR><BR>';
		$mm=$mm.'If you did NOT sign up for '.$bb.', <BR>IMMEDIATELY CHANGE YOUR ';
		$mm=$mm.'E-MAIL PASSWORD<BR>';
		$mm=$mm.'<A HREF="'.$uu.'">'.$uu.'</A>';


		// *** Check for blank user e-mail -- can't send e-mail!! ***
		$ue=$_POST['user_email'];
		if($ue == ""){
			?>
			<P>
			<BR><b>You will need to enter a valid e-mail address in order 
				to receive an e-mail token.</B><BR><BR>
			</p>
			<?php
		}
		else {
			//*** Send E-mail ***
			$jpsnt=wp_mail( $ue, $ss, $mm, $hh );

			if($jpdbg=="text") {
				echo("<BR>User Email = $ue");
				echo("<BR>Subject = $ss");
				echo("<BR>Header = $hh");
				echo("<BR>Message = $mm<BR>");
				echo("<BR>Mail=".$jpsnt."<BR>");
			}

			if($jpsnt==1) {
				?>
				<p>
				<label for="TOKEN">Token (check your e-mail): <br />
				<input type="text" name="TOKEN" id="TOKEN" 
					class="input" value="" size="25" /></label>	
				</p>
				<?php
			}
			else {
				?>
				<P>
				<BR><b>There was a problem sending an e-mail to:
				<?php echo("$ue"); ?><BR><BR>
					You will need to enter a valid e-mail address in order 
					to receive an e-mail token.</B><BR><BR>
				</p>
				<?php
			}
		}

		//*** Retain user's correct math response ***
		?>
		<input type="<?php echo("$jpdbg"); ?>" name="<?php echo("$f"); ?>" 
			value="<?php echo("$d"); ?>" />
		<?php
	}

	//*** Persistent tokens - math result and e-mail token ***
	?>
	<input type="<?php echo("$jpdbg"); ?>" name="JPREG" value="<?php echo("$c"); ?>" />
	<input type="<?php echo("$jpdbg"); ?>" name="TTT" value="<?php echo("$aa"); ?>" />
	<?php

}

// **************** PREVENT REGISTRATION IF USER FAILS MATH PROBLEM OR E-MAIL TOKEN *************
add_filter('registration_errors', 'JP_verifyMath_registration_errors', 10, 3);
function JP_verifyMath_registration_errors ($errors, $sanitized_user_login, $user_email) {
	global $JPURB_Opts;

	$f=$JPURB_Opts['MathProblemFieldName'];
	$m=$JPURB_Opts['MathReject'];

	if ( $_POST[$f]!=($_POST['JPREG']-JP_seed()) )
		$errors->add( 'first_name_error', __($m,'mydomain') );
	else if((substr($_POST["TTT"],2,4)!=$_POST["TOKEN"]) and ($user_email != "")) 
		$errors->add( 'mail_auth_error', "An e-mail has been sent to $user_email. Check your e-mail for a token code, then enter the token code below. - " );

	return $errors;
}




// **************** PREVENT REGISTRATION IF USER IP IN BLACKLIST *************
add_filter('registration_errors', 'JP_verifyIP_registration_errors', 10, 3);
function JP_verifyIP_registration_errors ($errors, $sanitized_user_login, $user_email) {
	global $JPURB_Opts;

	$m=$JPURB_Opts['ACLReject'];

	if ( wp_blacklist_check('', $user_email, '', '', $_SERVER['REMOTE_ADDR'], '') )
		$errors->add( 'first_name_error', __($m,'mydomain') );

	return $errors;
}

// **************** SET WEBSITE (URL) TO IP ADDRESS *************
add_action('user_register', 'JP_addIP_user_register');
function JP_addIP_user_register ($user_id) {
	//update_user_meta($user_id, 'url', $_SERVER['REMOTE_ADDR']);
	wp_update_user( array( 'ID' => $user_id, 'user_url' => $_SERVER['REMOTE_ADDR'] ) );
}


// *************** ADD SETTINGS LINK TO PLUGINS PAGE **************
$plugin = plugin_basename(__FILE__); 
if ( is_admin() )
	add_filter("plugin_action_links_$plugin", 'JPURB_SettingsLink' );


// ************* INSTANTIATE SETTINGS PAGE ******************
if( is_admin() )
	$my_settings_page = new JPUserRegToolsSettingsPage();


// ************* ACTIVATION HOOK ******************
function JPURB_activate() {
	$JPURB_Opts=get_option(JPURB_option_name(), JPURB_default_all() );

	update_option(JPURB_option_name(),$JPURB_Opts);
}
register_activation_hook( __FILE__, 'JPURB_activate' );

?>