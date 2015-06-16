<?php
/*
	Stop Spammers Plugin 
	Checks core.
	This stuff is no loaded unless there is a $_POST and one of the trigger fields is detected.
*/
if (!defined('ABSPATH')) exit; // just in case

/************************************************************
* 	kpg_sfs_check()
*	This is the generic email check so that it can be called
*	from several different hooks
*   returns the email if good. Dies if bad
*
*************************************************************/
function kpg_sfs_check($email='',$author='',$ip,$pwd='') {
	// use test@test.com to test if the system is working
	//sfs_debug_msg("starting");
	global $kpg_check_sempahore;
	if ($kpg_check_sempahore);
	global $sfs_check_activation;
	$sname=$_SERVER["REQUEST_URI"];	
	if (empty($sname)) {
		$sname=$_SERVER["SCRIPT_NAME"];	
	}
	if (empty($sname)) {
		$sname=' none? ';
	}
	$em=$email;
	$content='';
	if (array_key_exists('comment',$_POST)) {
		$content=$_POST['comment'];
	}
	// even though there may be problems with the wp-cron code - don't check it
	// some crons jobs use email or user id to update something.
	// it may be a hacker, but cron always fails the speed test.
	if(strpos($sname,'wp-cron.php')!==false) return;

	$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
	$options=kpg_sp_get_options();
	extract($options);
	$stats=kpg_sp_get_stats();
	extract($stats);
	
	// it may be that the server is trying to check its own ip 
	// not sure how this happens, but at least one user reported it.
	if ($ip==$_SERVER['SERVER_ADDR']) {
		$chkip='N'; // don't check yourself. - just check the non ip stuff.
	}
	// if the check for admin
	if (!class_exists('GoogleAuthenticator')&&$chkadmin=='Y'&&strpos($sname,'wp-login.php')!==false&&function_exists('wp_authenticate')) {
		if (array_key_exists('log',$_POST)&&array_key_exists('pwd',$_POST)) {
			$log=$_POST['log'];
			$pwd=$_POST['pwd'];
			$user=wp_authenticate($log,$pwd);
			if (!is_wp_error($user)) {
				// check to see if the user is good.
				// check roles
				$caps=$user->caps; 
				
				if (!empty($caps) && array_key_exists('administrator',$caps)) {
					if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now:$ip,$em,$author,$sname,Admin Login\r\n");
					return; // the user is good - let them in
				}
			}
		}
	}
    // always check wp-login.php?action=register
	if ($email!=$sfs_check_activation && $email!='test@test.com' ) {
		// from a user who wanted to exclude some of the checking.	
		if ($chkcomments!='Y') {
			if (strpos($sname,'wp-comments-post.php')!==false) return $email;
		}
		if ($chklogin!='Y') {
			if (strpos($sname,'wp-login.php')!==false && strpos($sname,'action=register')===false) return $email;
		}
		if ($chksignup!='Y') {
			if (strpos($sname,'wp-signup.php')!==false) return $email;
		}
		if ($chkxmlrpc!='Y') {
			if (strpos($sname,'xmlrpc.php')!==false) return $email;
		}
	}
	
	//sfs_debug_msg("continue 1");
	
	// clean up cache and history	
	while (count($badips)>$kpg_sp_cache) array_shift($badips);
	while (count($goodips)>$kpg_sp_good) array_shift($goodips);
	// timeout goodips - don't keep for more than an hour
	$nowtimeout=date('Y/m/d H:i:s',time()-3600 + ( get_option( 'gmt_offset' ) * 3600 ));
	// goodips last an hour.
	foreach($goodips as $key=>$data) {
		if ($data<$nowtimeout) {
			unset($goodips[$key]);
		}
	}
	// bad ips last 4 hours
	$nowtimeout=date('Y/m/d H:i:s',time()-(4*3600) + ( get_option( 'gmt_offset' ) * 3600 ));
	foreach($badips as $key=>$data) {
		if ($data<$nowtimeout) {
			unset($badips[$key]);
		}
	}
	//$goodips=array(); // limiting good ips to just a few
	while (count($hist)>$kpg_sp_hist) array_shift($hist);
	$stats['badips']=$badips;
	$stats['goodips']=$goodips;
	$stats['hist']=$hist;
	$sname=$_SERVER["REQUEST_URI"];	
	if (empty($sname)) {
		$sname=$_SERVER["SCRIPT_NAME"];	
	}
	if (empty($sname)) {
		$sname=' none? ';
	}
	$blog='';
	if (function_exists('is_multisite') && is_multisite()) {
		global $blog_id;
		if (!isset($blog_id)||$blog_id!=1) {
			$blog=$blog_id;
		}
	}
	if ($email!=$sfs_check_activation && $email!='test@test.com') {
		$email=trim($email);
		$email=strip_tags($email);

		// cleanup the input that is breaking the serialize functions here (I hope)
		$em=sanitize_email($email);
		$em=sanitize_text_field($em);
		$em=remove_accents($em);
		$em=utf8_decode($em);
		$em=really_clean($em);
	}
	$author=trim($author);
	$author=sanitize_text_field($author);
	$author=remove_accents($author);
	$author=utf8_decode($author);
	$author=really_clean($author);
	$pwd=trim($pwd);
	$pwd=sanitize_text_field($pwd);
	$pwd=remove_accents($pwd);
	$pwd=utf8_decode($pwd);
	$pwd=really_clean($pwd);
	$whodunnit='';
	// think of other things that might kill the serialize functions
	if (strlen($author)>80) $author=substr($author,0,77).'...';
	if (strlen($pwd)>32) $pwd=substr($pwd,0,30).'...';
	if (strlen($em)>80) $em=substr($em,0,80).'...';
	// set up hist channel
	$hist[$now]=array($ip,$em,$author,$sname,'',$blog);
	
	// check all of the ones that do not require file access
	$deny=false;
	// testing area goes here before other checks including white list
	// move this down past the white lists after testing is done
	
	// first check white lists 
	//sfs_debug_msg("continue 2");
	// white list vaultpress backups
	/*
	from vaultpress site:
		From Address	To Address
		207.198.112.1	207.198.112.254
		207.198.113.1	207.198.113.254	
	*/
	if (substr($ip,0,12)=='207.198.112.'||substr($ip,0,12)=='207.198.113.') return $email;
	
	// paypal is whitelisted
	if ($email!=$sfs_check_activation && $email!='test@test.com') {
		if (!$deny&& $chkip=='Y'&&kpg_sp_checkPayPal($ip)){
			$hist[$now][4]='White List PayPal';
			$stats['hist']=$hist;
			$cntwhite++;
			$stats['cntwhite']=$cntwhite;
			update_option('kpg_stop_sp_reg_stats',$stats);
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now:$ip,$em,$author,$sname,White List PayPal\r\n");
			return $email;
		}
		if (!$deny&& $chkip=='Y'&&(kpg_sp_searchi_ip($ip,$wlist))) {
			$hist[$now][4]='White List IP';
			$stats['hist']=$hist;
			$cntwhite++;
			$stats['cntwhite']=$cntwhite;
			update_option('kpg_stop_sp_reg_stats',$stats);
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now:$ip,$em,$author,$sname,White List IP\r\n");
			return $email;
		}
		if (!$deny&&!empty($em)&&kpg_sp_searchi($em,$wlist)) {
			$hist[$now][4]='White List EMAIL';
			$stats['hist']=$hist;
			$cntwhite++;
			$stats['cntwhite']=$cntwhite;
			update_option('kpg_stop_sp_reg_stats',$stats);
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now:$ip,$em,$author,$sname,White List EMAIL\r\n");
			return $email;
		}
		
	}
	/***********************************************************************************
	
		From here on in the checks are for spammers
	
	
	************************************************************************************/
	// not white listed, now try the simple rejects that don't require remote access.
	// check plugins if the noplugins is no - no checks if the url has the /plugin/ string
	if (!$deny&&$chkip!='Y'&&$noplugins=='Y'&&strpos($sname,'/plugins/')!==false) { 
		$hist[$now][4]='Not checking plugin forms';
		$stats['hist']=$hist;
		$cntgood++;
		$stats['cntgood']=$cntgood;
		update_option('kpg_stop_sp_reg_stats',$stats);
		if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now:$ip,$em,$author,$sname,Not checking plugin forms\r\n");
		return $email;
	}

	if ($email=='test@test.com') {
		$deny=true;
		$whodunnit.='test email';
	}

	//sfs_debug_msg("continue 3");
	
	// begin by checking the caches for bad ips. Do this before the regular checks
	// this way only the first appearance of a bad actor is recorded by type

	if (!$deny && $chkip=='Y'&&kpg_sp_search_ip($ip,$badips)) {
		$whodunnit.='Cached bad ip';
		$deny=true;
		$cntcacheip++;
	} 
	if (!$deny && $chkip=='Y') {
		if (kpg_sp_searchi_ip($ip,$blist)) {
			$whodunnit.='Black List IP';
			$deny=true;
			$cntblip++;
		}
	}
	// Ubiquity servers rent their servers to spammers and should be blocked
	if (!$deny&&$chkubiquity=='Y'&& $chkip=='Y') {
		$ansa=kpg_check_ubiquity($ip);
		if ($ansa!==false) {
			$deny=true;
			$whodunnit.=$ansa;
			$cntubiquity++;
		}
	}
	// check to see if the user is admin
	$log="";
	if (array_key_exists('log',$_POST)) $log=$_POST['log'];
	if (!$deny && $chkadminlog=='Y' && array_key_exists('log',$_POST) && strtolower($log)=='admin') {
		//hit on the admin login
		if (get_user_by( 'login',$log)===false) {
			// hit on admin but no admin
			$whodunnit.="Attack on userid '$log'";
			$deny=true;
			$cntadminlog++;
		} else {
			//$hist[$now][4]=' found login';
		}
	} else {
		// testing that the admin check is working.
		//$hist[$now][4]=' $chkadminlog '.$_POST['log'];
	}
	//sfs_debug_msg("continue 4");
	
	// check to see if we are timing out
	if (!$deny && $chksession!='N' && $sesstime>0) {
		if (!defined("WP_CACHE")||(!WP_CACHE)) { 
			// checking the session does not work if there is caching plugin
			// in fact we might not be here if caching is turned on.
			// we are in a comment - we need to check the transient variable
			// only works for comments - not doing logins because I can login in under a second
			// modify this so only login is excluded. If we are this far then we need to
			// check the POST
			//if (strpos($sname,'wp-comments-post.php')!==false
			// can't include login.php because in firefox its filled in and I just press the button.
			//||strpos($sname,'wp-login.php')!==false
			//	||strpos($sname,'signup.php')!==false
			//) 
			if (strpos($sname,'wp-login.php')===false) { 
				if (isset($_COOKIE['kpg_stop_spammers_time'])) {
					$stime=$_COOKIE['kpg_stop_spammers_time'];
					$tm=strtotime("now")-$stime;
					if ($tm>0&&$tm<=$sesstime) { // zero seconds is wrong, too. it means that session was set somewhere.
						// takes longer than 4 seconds to really type a comment
						$whodunnit.="Too Quick ($tm)";
						$deny=true;
						$cntsession++;
					} else {
						$whodunnit.="($tm) "; // to follow timing
					}
				}
			}
		}
	}
	
	// check to see if it is coming from the red herring form
	$nonce='';
	if (!$deny&&array_key_exists('akismet_comment_nonce',$_POST)) {
		$nonce=$_POST['akismet_comment_nonce'];
		if (!empty($nonce)&&kpg_verify_nonce($nonce,'kpgstopspam_redherring')) { 
			$whodunnit.='Red Herring';
			$deny=true;
			$cntrh++;
		}
	}
	//sfs_debug_msg("continue 5");
	
	if (!$deny&&array_key_exists('rememberme',$_POST)) {
		$nonce=$_POST['rememberme'];
		if (!empty($nonce)&&kpg_verify_nonce($nonce,'kpgstopspam_redherring')) { 
			$whodunnit.='Red Herring';
			$deny=true;
			$cntrh++;
		}
	}
	//sfs_debug_msg("continue 6");
	if (!$deny&&!empty($_GET)&&array_key_exists('redir',$_GET)) {
		$nonce=$_GET['redir'];
		if (!empty($nonce)&&kpg_verify_nonce($nonce,'kpgstopspam_redherring')) { 
			$whodunnit.='Red Herring';
			$deny=true;
			$cntrh++;
		}
	}
	$ref='';
	if (array_key_exists('HTTP_REFERER',$_SERVER)) {
		$ref=$_SERVER['HTTP_REFERER'];
	}
	$ua='';
	if (array_key_exists('HTTP_USER_AGENT',$_SERVER)) {
		$ua=$_SERVER['HTTP_USER_AGENT'];
	}
	
	
	
	// try checking to see if there is a referrer
	if (!$deny&&$chkreferer=='Y') {
		// someone is sending a post. Therefore the referer must be from our site.
		// apple safari on the iphone does not send the referrer so we need to ignore this.
		if (strpos(strtolower($ua),'iphone')===false&&strpos(strtolower($ua),'ipad')===false) {
			// require the referer
			// check to see if our domain is found in the referer
			$host=$_SERVER['HTTP_HOST'];
			if (!empty($ref)&&!empty($host)) { 
				// some servers have an empty host for some reason
				// some servers and links from https to http and back don't send a referer
				if (strpos(strtolower($ref),strtolower($host))===false) {
					// bad referer
					$whodunnit.="http referer";
					$deny=true;
					$cntreferer++;
				}
			}
		}
	}
	
	if (!$deny && $chkagent=='Y') {
		if (!array_key_exists('HTTP_USER_AGENT',$_SERVER)) {
			$whodunnit.='Missing User Agent';
			$deny=true;
		}
		if (!$deny) {
			$bua=kpg_check_bad_agents();
			if ($bua!==false) {
				$deny=true;
				$whodunnit.='Blacklist User agent:'.$bua;
				$cntagent++;
			}
		}
	}
	
	if (!$deny && !empty($badTLDs)) {
		// check the ending to see if the tld should be banned
		// get the tld
		// need the last occurrence of '.' in $em
		$tj=strrpos($em,'.');
		if ($tj!==false) {
			$tld=substr($em,$tj+1);
			if (kpg_sp_searchL($tld,$badTLDs)) {
				$whodunnit.='Bad TLD';
				$deny=true;
				$cnttld++;
			}
		}
	}

	// These are the simple email checks
	if (!empty($em)) {
		if ($em=='dukang2004@yahoo.com') { // repeat bad customer
			$whodunnit.='dukang2004 jerk';
			$deny=true;
			$cntblem++;
		}
		if (!$deny && kpg_sp_searchi($em,$blist)) {
			$whodunnit.='Black List EMAIL';
			$deny=true;
			$cntblem++;
		}
		if (!$deny) { 
			$emdomain=explode('@',$em);
			if (count($emdomain)==2&&kpg_sp_searchi($emdomain[1],$baddomains)) {
				$whodunnit.='Blocked Domain';
				$deny=true;
				$cntemdom++;
			}
		}
		if (!$deny && $chklong=='Y' && strlen($em)>64) {
			$deny=true;
			$whodunnit.='email too long';
			$cntlong++;
		}
		if (!$deny && $chklong=='Y' && strlen($em)<5) {
			$deny=true;
			$whodunnit.='email too short';
			$cntlong++;
		}
		if (!$deny && $chkdisp=='Y') {
			$ansa=kpg_check_disp($em);
			if ($ansa!==false) {
				$deny=true;
				$whodunnit.='Disposable em:'.$em;
				$cntdisp++;
			}
		}
		if (!$deny && $chkspamwords=='Y') {
			$ansa=kpg_check_spamwords($em,$spamwords);
			if ($ansa!==false) {
				$deny=true;
				$whodunnit.='Email Spamwords:'.$ansa;
				$cntspamwords++;
			}
		}
	}
	//sfs_debug_msg("continue 6");
	// check the comment content for spamwords
	if (!$deny && $chkspamwords=='Y' && !empty($content)) {
		$ansa=kpg_check_spamwords($content,$spamwords);
		if ($ansa!==false) {
			$deny=true;
			$whodunnit.='Comment Spamwords:'.$ansa;
			$cntspamwords++;
		}
	} 
	// check the author field
	// getting a lot of huge author names
	if (!empty($author)) {
		if (!$deny && $chklong=='Y' && strlen($author)>64) {
			$whodunnit.='long author name '.strlen($author);
			$deny=true;
			$cntlongauth++;
		}
		if (!$deny && $chkspamwords=='Y') {
			$ansa=kpg_check_spamwords($author,$spamwords);
			if ($ansa!==false) {
				$deny=true;
				$whodunnit.='Author Spamwords:'.$ansa;
				$cntspamwords++;
			}
		}
	}
	$accept_head=false; 
	if (array_key_exists('HTTP_ACCEPT',$_SERVER)) $accept_head=true; // real browsers send HTTP_ACCEPT
	if (!$deny&&$accept=='Y'&&!$accept_head) {
		// no accept header - real browsers send the HTTP_ACCEPT header
		$whodunnit.='No Accept header;';
		$deny=true;
		$cntaccept++;
	}

	// check here for good bad cache - move this down because spammers reuse the ip and we might just catch them
	if (!$deny&& $chkip=='Y'&&kpg_sp_search_ip($ip,$goodips)) {
		$hist[$now][4]='Cached good ip';
		$stats['hist']=$hist;
		$cntgood++;
		$stats['cntgood']=$cntgood;
		update_option('kpg_stop_sp_reg_stats',$stats);
		if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now:$ip,$em,$author,$sname,Cached good ip\r\n");
		return $email;
	}

	
	//sfs_debug_msg("continue 7");
	
	// try akismet
	// turn off akismet for the nonce
	$chkakismet='N';
	if (!$deny&& $chkip=='Y'&&$chkakismet=='Y' && ipChkk() &&(strpos($sname,'login.php')!==false||strpos($sname,'register.php')!==false||strpos($sname,'signup.php')!==false)) { 
		$ansa=kpg_akismet_check($ip);
		if ($ansa!==false) {
			$deny=true;
			$whodunnit.='Akismet';
			$cntakismet++;
		}
	}
    $chkakismetcomments='N';
	if (!$deny&& $chkip=='Y'&&$chkakismetcomments=='Y'&&(strpos($sname,'login.php')===false&&strpos($sname,'register.php')===false&&strpos($sname,'signup.php')===false)) { 
		$ansa=kpg_akismet_check($ip);
		if ($ansa!==false) {
			$deny=true;
			$whodunnit.='Akismet';
			$cntakismet++;
		}
	}

	// here is the database lookups section. Simple checks did not work. We need to do a lookup
	//sfs_debug_msg("continue 8");
	if (!$deny && $chksfs=='Y' && $chkip=='Y' && ipChkk() ) { 
		$query="http://www.stopforumspam.com/api?ip=$ip";
		// no longer checking email on sfs
		/*
		if ($chkemail=='Y'&&!empty($em)) {
			$query=$query."&email=$em";
		}
		*/
		$check='';
		$check=kpg_sfs_reg_getafile($query);
		if (!empty($check)) {
			if (substr($check,0,4)=="ERR:") {
				$whodunnit.=$check.', ';
			}
			$lastseen='';
			$frequency='';
			$n=strpos($check,'<appears>yes</appears>');
			if ($n!==false) {
				if (strpos($check,'<lastseen>',$n)!==false) {
					$k=strpos($check,'<lastseen>',$n);
					$k+=10;
					$j=strpos($check,'</lastseen>',$k);
					$lastseen=date('Y-m-d',time() + ( get_option( 'gmt_offset' ) * 3600 ));
					if (($j-$k)>12&&($j-$k)<24) $lastseen=substr($check,$k,$j-$k); // should be about 20 characters
					if (strpos($lastseen,' ')) $lastseen=substr($lastseen,0,strpos($lastseen,' ')); // trim out the time to save room.
					if (strpos($check,'<frequency>',$n)!==false) {
						$k=strpos($check,'<frequency>',$n);
						$k+=11;
						$j=strpos($check,'</frequency',$k);
						$frequency='9999';			
						if (($j-$k)&&($j-$k)<7) $frequency=substr($check,$k,$j-$k); // should be a number greater than 0 and probably no more than a few thousand.
					}
				}

				// have freqency and lastseen date - make these options in next release
				// check freq and age
				if (!empty($frequency) && !empty($lastseen) && ($frequency!=255) && ($frequency>=$sfsfreq) && (strtotime($lastseen)>(time()-(60*60*24*$sfsage))) )   { 
					//if ( ($frequency>=$sfsfreq) && (strtotime($lastseen)>(time()-(60*60*24*$sfsage))) )   { 
					// frequency we got from the db, sfsfreq is the min we'll accept (default 0)
					// sfsage is the age in days. we get lastscene from
					$deny=true;
					$whodunnit.="SFS, $lastseen, $frequency";
					$cntsfs++;
				}
			}
		}
		//$whodunnit.="Passed SFS, $query $check";
	} 
	
	//sfs_debug_msg("continue 9");

	if (!$deny&&$chkdnsbl=='Y'&& $chkip=='Y' && ipChkk() ) {
		$ansa=@kpg_check_all_dnsbl($ip);
		if ($ansa!==false) {
			$deny=true;
			$whodunnit.=$ansa;
			$cntdnsbl++;
		}
	}
	//sfs_debug_msg("continue 10");
	if (!$deny&&$honeyapi!=''&& $chkip=='Y' && ipChkk() ) {
		$ansa=kpg_dnsbl_data($ip,'.dnsbl.httpbl.org',$honeyapi);
		if ($ansa!==false) {
			$deny=true;
			$whodunnit.="HTTP:bl $ansa";
			$cnthp++;
		}
	}
	//sfs_debug_msg("continue 11");
	if (!$deny&&$botscoutapi!=''&& $chkip=='Y' && ipChkk() ) {
		// try the ip on botscoutapi
		$query="http://botscout.com/test/?ip=$ip&key=$botscoutapi";
		$check='';
		$check=@kpg_sfs_reg_getafile($query);
		if (!empty($check)) {
			if (substr($check,0,4)=="ERR:") {
				$whodunnit.=$check.', ';
			}
			if(strpos($check,'|')) {
				$result=explode('|',$check);
				if (count($result)>2) {
					//  Y|IP|3 - found, type, database occurences
					if ($result[0]=='Y'&&$result[2]>$botfreq) {
						$deny=true;
						$whodunnit.='BotScout, '.$result[2];
						$cntbotscout++;
					}
				}
			}
		}
	}
	if (!$deny&&$chktor=='Y'&& $chkip=='Y' ) {
		$ansa=@kpg_check_tor($ip);
		if ($ansa!==false) {
			$deny=true;
			$whodunnit.="Tor";
			$cnttor++;
		}
	}
    // one last check
	$xip=kpg_get_ip();
	if (!$deny&&$xip!=$ip) {
			$deny=true;
			$whodunnit.=" Spoofed IP";
			$cntspoof++;
	}
	
	
	
	
	//sfs_debug_msg("continue 12");
	$hist[$now][4].=$whodunnit;
	if (!$deny) {
		if ($ip!="127.0.0.1") { // don't cache 127.0.0.1 if passes - for testing.
			$hist[$now][4].=' passed';
			$goodips[$ip]=$now;
			$stats['hist']=$hist;
			$stats['cntpassed']=$cntpassed+1;
			$stats['goodips']=$goodips; // uncomment to cache good ips.
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now:$ip,$em,$author,$sname,Passed all\r\n");
			update_option('kpg_stop_sp_reg_stats',$stats);
			// comment out log in production
			//kpg_stop_spam_log();
		}
		return;
	}
	if (!empty($pwd)) $hist[$now][2]=$author.'/'.$pwd;

	if ($email!=''&&$email==$sfs_check_activation) {
		// failed activation check
		// report reason
		echo "<br/>Reason code: $whodunnit <br/>
		ip: $ip<br/>
		server uri: $sname<br/>
		MU blog number: $blog<br/>
		HTTP_REFERE: $ref<br/>
		User agent: $ua<br/>
		Accept head present: $accept_head<br/>
		<br/>";
		
		return false;
	}

	// update the history files.
	// record the last few guys that have  tried to spam
	// add the bad spammer to the history list
	// Cache the bad guy
	if ($email!='test@test.com') {
		$spcount++;
		$spmcount++;
		$stats['spcount']=$spcount;
		$stats['spmcount']=$spmcount;
		if (!empty($ip)&& $chkip=='Y' && $ip!="127.0.0.1") $badips[$ip]=$now;
		asort($badips);
		while (count($badips)>$kpg_sp_cache) array_shift($badips);
		$stats['badips']=$badips;
	}
	// I am sick and tired of this guy filling up my logs: dukang2004@yahoo.com
	if ($email!='dukang2004@yahoo.com'&&$email!='test@test.com' ) { // special case don't bother to log for now.
		$stats['hist']=$hist;
	}
	if ($email!='test@test.com') {
		// reason types
		$stats['cntsfs']=$cntsfs;
		$stats['cntreferer']=$cntreferer;
		$stats['cntdisp']=$cntdisp;
		$stats['cntrh']=$cntrh;

		$stats['cntdnsbl']=$cntdnsbl;
		$stats['cntubiquity']=$cntubiquity;
		$stats['cntakismet']=$cntakismet;
		$stats['cntspamwords']=$cntspamwords;
		$stats['cntsession']=$cntsession;

		$stats['cntlong']=$cntlong;
		$stats['cntagent']=$cntagent;
		$stats['cnttld']=$cnttld;
		$stats['cntemdom']=$cntemdom;			
		$stats['cntcacheip']=$cntcacheip;
		
		$stats['cntcacheem']=$cntcacheem;
		$stats['cnthp']=$cnthp;
		$stats['cntbotscout']=$cntbotscout;
		$stats['cntblem']=$cntblem;
		$stats['cntlongauth']=$cntlongauth;
		
		$stats['cntblip']=$cntblip;
		$stats['cntaccept']=$cntaccept;
		$stats['cntpassed']=$cntpassed;
		$stats['cntwhite']=$cntwhite;
		$stats['cntgood']=$cntgood;

		$stats['cntadminlog']=$cntadminlog;
		$stats['cntspoof']=$cntspoof;
		
		//
		update_option('kpg_stop_sp_reg_stats',$stats);
		// write out the log file
		$clogfile=kpg_file_exists('.history_log.txt');
		if ($clogfile===false) $clogfile=0;
		if ($logfilesize>0&&$clogfile<=$logfilesize) {
			// ok to write the log file.
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: $ip,$em,$author,$sname,$whodunnit\r\n");
		}
	}
	if ($redir=='Y'&&!empty($redirurl)) {
		header('HTTP/1.1 307 Moved');
		header('Status: 307 Moved');
		header("location: $redirurl"); 
		exit();
	} 

	
	// add the reason code to the login message
	$rejectmessage=str_replace('[reason]',$whodunnit,$rejectmessage);
	$rejectmessage=str_replace('[ip]',$ip,$rejectmessage);
	//$captcha='Y';
	//if ($chkcaptcha!='N') $chkcaptcha='Y'; // what is happening here?
	kpg_reject_spam($rejectmessage,$notify,$chkcaptcha,$whodunnit);
	
}
/*************************************************
*  Utility checks
*************************************************/
function kpg_check_bad_agents() {
	$badagents=array("asterias","Atomic_Email_Hunter","b2w/0.1","BackDoorBot/1.0","Black Hole","BlowFish/1.0","BotALot","BotRightHere","BuiltBotTough","Bullseye/1.0","BunnySlippers","Cegbfeieh","CheeseBot","CherryPicker","CherryPickerElite/1.0","CherryPickerSE/1.0","CopyRightCheck","cosmos","Crescent","Crescent Internet ToolPak HTTP OLE Control v.1.0","discobot","DittoSpyder","DOC","Download Ninja","EmailCollector","EmailSiphon","EmailWolf","EroCrawler","ExtractorPro","Fasterfox","Fetch","Foobot","grub-client","Harvest/1.5","hloader","httplib","HTTrack","humanlinks","ieautodiscovery","InfoNaviRobot","JennyBot","k2spider","Kenjin Spider","Keyword Density/0.9","larbin","LexiBot","libWeb/clsHTTP","libwww","LinkextractorPro","linko","LinkScan/8.1a Unix","LinkWalker","LNSpiderguy","lwp-trivial","lwp-trivial/1.34","Mata Hari","Microsoft.URL.Control","Microsoft URL Control - 5.01.4511","Microsoft URL Control - 6.00.8169","MIIxpc","MIIxpc/4.2","Missigua Locator","Mister PiX","moget","moget/2.1","MSIECrawler","NetAnts","NICErsPRO","NPBot","Offline Explorer","Openfind","Openfind data gathere","ProPowerBot/2.14","ProWebWalker","QueryN Metasearch","RepoMonkey","RepoMonkey Bait & Tackle/v1.01","RMA","sitecheck.Internetseer.com","SiteSnagger","SnapPreviewBot","SpankBot","spanner","suzuran","Szukacz/1.4","Teleport","TeleportPro","Teleport Pro/1.29","Telesoft","TurnitinBot","The Intraformant","TheNomad","TightTwatBot","Titan","toCrawl/UrlDispatcher","True_Robot","True_Robot/1.0","turingos","UbiCrawler","URLy Warning","VCI","VCI WebViewer VCI WebViewer Win32","Web Image Collector","Web Downloader/6.9","WebAuto","WebBandit","WebBandit/3.50","WebCopier","WebCopier v4.0","WebEnhancer","WebmasterWorldForumBot","WebReaper","WebSauger","Website Quester","Webster Pro","WebStripper","WebZip","WebZip/4.0","Wget","Wget/1.5.3","Wget/1.6","WWW-Collector-E","Xenu's","Xenu's Link Sleuth 1.1c","Zao","Zeus","Zeus 32297 Webster Pro V2.9 Win32","ZyBORG","Java/1.");
	$agent=$_SERVER['HTTP_USER_AGENT'];
	if (empty($agent)) return false;
	foreach ($badagents as $a) {
		if (strpos(strtolower($agent),strtolower($a))!==false) {
			return $a;
		}
	}
	return false;
}
function kpg_check_spamwords($chk,$spamwords) {
	// list of common spam words form wordpress: http://codex.wordpress.org/Spam_Words
	// these should be safe except for sites selling drugs, porn or gambling
	// there has to be better lists than this somewhere. This is dated and not especially applicable, although safe.
	// if these appear in email address or user id, we don't want them.
	if(empty($spamwords)) return false;
	if(empty($chk)) return false;
	$c=strtolower($chk);
	$c=str_replace(' ','-',$c);
	$c=str_replace('_','-',$c);
	$c=str_replace('.','-',$c);
	foreach ($spamwords as $s) {
		$s=strtolower($s);
		if (strpos($c,$s)!==false) {
			return $s;
		}
	}
	return false;

}
function kpg_check_disp($em) {
	if (empty($em)) return false;

	$disposables=array('0815.ru','0clickemail.com','0wnd.net','0wnd.org','10minutemail.com','1chuan.com','1zhuan.com','20minutemail.com','2prong.com','3d-painting.com','4warding.com','4warding.net','4warding.org','675hosting.com','675hosting.net','675hosting.org','6url.com','75hosting.com','75hosting.net','75hosting.org','9ox.net','a-bc.net','afrobacon.com','ajaxapp.net','amilegit.com','amiri.net','amiriindustries.com','anonbox.net','anonymail.dk','anonymbox.com','antichef.com','antichef.net','antispam.de','baxomale.ht.cx','beefmilk.com','binkmail.com','bio-muesli.net','blogmyway.org','bobmail.info','bodhi.lawlita.com','bofthew.com','brefmail.com','bsnow.net','bugmenot.com','bumpymail.com','buyusedlibrarybooks.org','casualdx.com','centermail.com','centermail.net','chogmail.com','choicemail1.com','cool.fr.nf','correo.blogos.net','cosmorph.com','courriel.fr.nf','courrieltemporaire.com','curryworld.de','cust.in','dacoolest.com','dandikmail.com','deadaddress.com','deadspam.com','despam.it','despammed.com','devnullmail.com','dfgh.net','digitalsanctuary.com','discardmail.com','discardmail.de','disposableaddress.com','disposeamail.com','disposemail.com','dispostable.com','dm.w3internet.co.uk example.com','dodgeit.com','dodgit.com','dodgit.org','dontreg.com','dontsendmespam.de','dotmsg.com','dresssmall.com','dump-email.info','dumpandjunk.com','dumpmail.de','dumpyemail.com','e4ward.com','email60.com','emaildienst.de','emailias.com','emailinfive.com','emailmiser.com','emailtemporario.com.br','emailto.de','emailwarden.com','emailxfer.com','emz.net','enterto.com','ephemail.net','etranquil.com','etranquil.net','etranquil.org','explodemail.com','fakeinbox.com','fakeinformation.com','fakemailz.com','fastacura.com','fastchevy.com','fastchrysler.com','fastkawasaki.com','fastmazda.com','fastmitsubishi.com','fastnissan.com','fastsubaru.com','fastsuzuki.com','fasttoyota.com','fastyamaha.com','filzmail.com','fizmail.com','footard.com','forgetmail.com','frapmail.com','front14.org','fux0ringduh.com','garliclife.com','get1mail.com','getonemail.com','getonemail.net','ghosttexter.de','girlsundertheinfluence.com','gishpuppy.com','gowikibooks.com','gowikicampus.com','gowikicars.com','gowikifilms.com','gowikigames.com','gowikimusic.com','gowikinetwork.com','gowikitravel.com','gowikitv.com','great-host.in','greensloth.com','gsrv.co.uk','guerillamail.biz','guerillamail.com','guerillamail.net','guerillamail.org','guerrillamail.com','guerrillamail.net','guerrillamailblock.com','h8s.org','haltospam.com','hatespam.org','hidemail.de','hotpop.com','ieatspam.eu','ieatspam.info','ihateyoualot.info','iheartspam.org','imails.info','imstations.com','inboxclean.com','inboxclean.org','incognitomail.com','incognitomail.net','ipoo.org','irish2me.com','iwi.net','jetable.com','jetable.fr.nf','jetable.net','jetable.org','jnxjn.com','junk1e.com','kasmail.com','kaspop.com','killmail.com','killmail.net','klassmaster.com','klassmaster.net','klzlk.com','kulturbetrieb.info','kurzepost.de','lifebyfood.com','link2mail.net','litedrop.com','lookugly.com','lopl.co.cc','lortemail.dk','lovemeleaveme.com','lr78.com','maboard.com','mail.by','mail.mezimages.net','mail2rss.org','mail333.com','mail4trash.com','mailbidon.com','mailblocks.com','mailcatch.com','maileater.com','mailexpire.com','mailfreeonline.com','mailin8r.com','mailinater.com','mailinator.com','mailinator.net','mailinator2.com','mailincubator.com','mailme.lv','mailmoat.com','mailnator.com','mailnull.com','mailquack.com','mailshell.com','mailsiphon.com','mailslapping.com','mailzilla.com','mailzilla.org','mbx.cc','mega.zik.dj','meinspamschutz.de','meltmail.com','messagebeamer.de','mierdamail.com','mintemail.com','moncourrier.fr.nf','monemail.fr.nf','monmail.fr.nf','mt2009.com','mx0.wwwnew.eu','mycleaninbox.net','myspaceinc.com','myspaceinc.net','myspaceinc.org','myspacepimpedup.com','myspamless.com','mytrashmail.com','neomailbox.com','nervmich.net','nervtmich.net','netmails.com','netmails.net','netzidiot.de','neverbox.com','no-spam.ws','nobulk.com','noclickemail.com','nogmailspam.info','nomail.xl.cx','nomail2me.com','nospam.ze.tc','nospam4.us','nospamfor.us','nowmymail.com','nurfuerspam.de','objectmail.com','obobbo.com','oneoffemail.com','oneoffmail.com','onewaymail.com','oopi.org','ordinaryamerican.net','ourklips.com','outlawspam.com','owlpic.com','pancakemail.com','pimpedupmyspace.com','poofy.org','pookmail.com','privacy.net','proxymail.eu','punkass.com','putthisinyourspamdatabase.com','quickinbox.com','rcpt.at','recode.me','recursor.net','recyclemail.dk','regbypass.comsafe-mail.net','rejectmail.com','rklips.com','safersignup.de','safetymail.info','sandelf.de','saynotospams.com','selfdestructingmail.com','sendspamhere.com','shiftmail.com','shitmail.me','shortmail.net','sibmail.com','skeefmail.com','slaskpost.se','slopsbox.com','smellfear.com','snakemail.com','sneakemail.com','sofort-mail.de','sogetthis.com','soodonims.com','spam.la','spamavert.com','spambob.com','spambob.net','spambob.org','spambog.com','spambog.de','spambog.ru','spambox.info','spambox.us','spamcannon.com','spamcannon.net','spamcero.com','spamcon.org','spamcorptastic.com','spamcowboy.com','spamcowboy.net','spamcowboy.org','spamday.com','spamex.com','spamfree24.com','spamfree24.de','spamfree24.eu','spamfree24.info','spamfree24.net','spamfree24.org','spamgourmet.com','spamgourmet.net','spamgourmet.org','spamherelots.com','spamhereplease.com','spamhole.com','spamify.com','spaminator.de','spamkill.info','spaml.com','spaml.de','spammotel.com','spamobox.com','spamoff.de','spamslicer.com','spamspot.com','spamthis.co.uk','spamthisplease.com','spamtrail.com','speed.1s.fr','suremail.info','tempalias.com','tempe-mail.com','tempemail.biz','tempemail.com','tempemail.net','tempinbox.co.uk','tempinbox.com','tempomail.fr','temporarily.de','temporaryemail.net','temporaryforwarding.com','temporaryinbox.com','thankyou2010.com','thisisnotmyrealemail.com','throwawayemailaddress.com','tilien.com','tmailinator.com','tradermail.info','trash-amil.com','trash-mail.at','trash-mail.com','trash-mail.de','trash2009.com','trashdevil.com','trashdevil.de','trashmail.at','trashmail.com','trashmail.de','trashmail.me','trashmail.net','trashmail.org','trashmailer.com','trashymail.com','trashymail.net','turual.com','twinmail.de','tyldd.com','uggsrock.com','upliftnow.com','uplipht.com','venompen.com','viditag.com','viewcastmedia.com','viewcastmedia.net','viewcastmedia.org','walala.org','wegwerfadresse.de','wegwerfmail.de','wegwerfmail.net','wegwerfmail.org','wetrainbayarea.com','wetrainbayarea.org','wh4f.org','whopy.com','whyspam.me','wilemail.com','willselfdestruct.com','winemaven.info','wronghead.com','wuzup.net','wuzupmail.net','wwwnew.eu','xagloo.com','xemaps.com','xents.com','xmaily.com','xoxy.net','yep.it','yogamaven.com','yopmail.com','yopmail.fr','yopmail.net','yuurok.com','zippymail.info','zoemail.org');
	$emdomain=explode('@',$em);
	if (count($emdomain)==2&&in_array(strtolower($emdomain[1]),$disposables)) {
		// the email is a disposable email address
		// do you really want this guy????
		return true;
	}
	return false;
}

function kpg_akismet_check($ip) {
	if (empty($ip)) return false;
	// give akismet a try - it seems to know more than anyone
	$api_key=get_option('wordpress_api_key');
	$agent=$_SERVER['HTTP_USER_AGENT'];
	$blogurl=site_url();
	$api_key=urlencode($api_key);
	$agent=urlencode($agent);
	$blogurl=urlencode($blogurl);
	if (empty($api_key)||empty($agent)||empty($blogurl)) return false;
	$request="blog=$blogurl&user_ip=$ip&user_agent=$agent";
	$host = $http_host = $api_key.'.rest.akismet.com';
	$path = '/1.1/comment-check';
	$port = 80;
	$akismet_ua = "WordPress/3.1.1 | Akismet/2.5.3";
	$content_length = strlen( $request );
	$http_request  = "POST $path HTTP/1.0\r\n";
	$http_request .= "Host: $host\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$http_request .= "Content-Length: {$content_length}\r\n";
	$http_request .= "User-Agent: {$akismet_ua}\r\n";
	$http_request .= "\r\n";
	$http_request .= $request;
	$response = '';
	//$f=fopen('akismet.txt',"a");
	if( false != ( $fs = @fsockopen( $http_host, $port, $errno, $errstr, 10 ) ) ) {
		fwrite( $fs, $http_request );
		while ( !feof( $fs ) )
		$response .= fgets( $fs, 1160 ); // One TCP-IP packet
		fclose( $fs );
		//fwrite($f,"\r\n$response\r\n");
		$response = explode( "\r\n\r\n", $response, 2 );
	}
	//fwrite($f,"\r\n$request\r\n");
	//fwrite($f,"\r\n$http_request\r\n");
	//fclose($f);
	if ( 'true' == $response[1] )
	return true;
	else
	return false;
}	

function kpg_check_ubiquity($ip) {
	if (empty($ip)) return false;
	if (strpos($ip,'.')===false) return false;
	$userve=array(
	'XSServer',
	array('46.251.228.0','46.251.229.255'),
	array('109.230.197.0','109.230.197.255'),
	array('109.230.213.0','109.230.213.255'),
	array('109.230.216.0','109.230.217.255'),
	array('109.230.220.0','109.230.223.255'),
	array('109.230.246.0','109.230.246.255'),
	array('109.230.248.0','109.230.249.255'),
	array('109.230.251.0','109.230.251.255'),
	'Ubiquity-Nobis',
	array('23.19.0.0','23.19.255.255'),
	array('64.120.0.0','64.120.127.255'),
	array('67.201.0.0','67.201.7.255'),
	array('67.201.40.0','67.201.40.255'),
	array('67.201.48.0','67.201.49.255'),
	array('69.147.224.0','69.147.225.255'),
	array('69.174.60.0','69.174.63.255'),
	array('70.32.32.0','70.32.47.255'),
	array('72.37.145.0','72.37.145.255'),
	array('72.37.204.0','72.37.204.255'),
	array('72.37.218.0','72.37.219.255'),
	array('72.37.221.0','72.37.221.255'),
	array('72.37.222.0','72.37.223.255'),
	array('72.37.224.0','72.37.231.255'),
	array('72.37.237.0','72.37.237.255'),
	array('72.37.242.0','72.37.243.255'),
	array('72.37.246.0','72.37.247.255'),
	array('108.62.0.0','108.62.255.255'),
	array('173.208.0.0','173.208.127.255'),
	array('173.234.0.0','173.234.255.255'),
	array('174.34.128.0','174.34.191.255'),
	array('216.6.224.0','216.6.239.255'),
	array('176.31.50.64','176.31.50.95'),
	'Balticom',
	array('46.23.32.0','46.23.47.255'),
	array('82.193.64.0','82.193.95.255'),
	array('83.99.128.0','83.99.255.255'),
	array('109.73.96.0','109.73.111.255'),
	array('212.142.64.0','212.142.127.255'),
	'Everhost',
	array('31.2.216.0','31.2.223.255'),
	array('31.47.208.0','31.47.215.255'),
	array('31.220.128.0','31.220.131.255'),
	array('46.108.155.0','46.108.155.255'),
	array('89.42.8.0','89.42.8.255'),
	array('89.42.108.0','89.42.109.255'),
	array('89.44.16.0','89.44.31.255'),
	array('93.118.64.0','93.118.79.255'),
	array('94.60.152.0','94.60.159.255'),
	array('94.60.160.0','94.60.191.255'),
	array('94.60.192.0','94.60.199.255'),
	array('94.63.0.0','94.63.31.255'),
	array('94.63.32.0','94.63.47.255'),
	array('94.63.56.0','94.63.63.255'),
	array('94.63.64.0','94.63.71.255'),
	array('94.63.128.0','94.63.135.255'),
	array('94.63.152.0','94.63.159.255'),
	array('94.63.192.0','94.63.207.255'),
	array('94.177.4.0','94.177.5.255'),
	array('95.64.24.0','95.64.31.255'),
	array('95.64.32.0','95.64.32.255'),
	array('95.64.41.0','95.64.41.255'),
	array('95.64.42.0','95.64.42.255'),
	array('95.64.110.0','95.64.111.255'),
	array('95.128.168.0','95.128.168.255'),
	array('95.128.174.0','95.128.175.255'),
	array('95.187.0.0','95.187.127.255'),
	array('178.255.36.0','178.255.37.255'),
	array('178.255.38.0','178.255.38.255'),
	array('188.208.0.0','188.208.15.255'),
	array('188.215.0.0','188.215.0.255'),
	array('188.215.32.0','188.215.35.255'),
	array('188.229.19.0','188.229.19.255'),
	array('188.229.20.0','188.229.23.255'),
	array('188.229.38.0','188.229.38.255'),
	array('188.229.103.0','188.229.103.255'),
	array('188.229.104.0','188.229.111.255'),
	array('188.229.124.0','188.229.127.255'),
	array('188.240.36.0','188.240.39.255'),
	array('188.240.160.0','188.240.175.255'),
	array('188.240.192.0','188.240.223.255'),
	array('188.247.128.0','188.247.128.255'),
	array('188.247.228.0','188.247.229.255'),
	'FDC',
	array('67.159.0.0','67.159.63.255'),
	array('66.90.64.0','66.90.127.255'),
	array('208.53.128.0','208.53.191.255'),
	array('50.7.0.0','50.7.255.255'),
	array('204.45.0.0','204.45.255.255'),
	array('76.73.0.0','76.73.255.255'),
	array('74.63.64.0','74.63.127.255'),
	'Exetel',
	array('109.230.244.0','109.230.245.255'),
	array('31.214.155.0','31.214.155.255'),
	'Virpus',
	array('50.115.160.0','50.115.175.255'),
	array('173.0.48.0','173.0.63.255'),
	array('199.119.224.0','199.119.227.255'),
	array('199.180.128.0','199.180.135.255'),
	array('208.89.208.0','208.89.215.255'),
	'MiscSpamServer',
	array('74.63.222.74','74.63.222.74'),
	array('86.181.176.121','86.181.176.121'),
	array('98.126.4.202','98.126.4.202'),
	array('98.126.251.234','98.126.251.234'),
	array('188.168.0.0','188.168.255.255'),
	array('81.17.22.21','81.17.22.21'),
	array('66.219.17.212','66.219.17.212'),
	array('46.29.248.0','46.29.249.255'),
	array('74.221.208.0','74.221.223.255'),
	array('109.169.57.204','109.169.57.204'),
	array('184.22.139.0','184.22.139.255'),
	array('99.187.246.108','99.187.246.108'),
	array('195.62.24.0','195.62.25.255'),
	array('141.105.65.151','141.105.65.151'),
	array('146.0.74.0','146.0.74.255'),
	array('194.28.112.0','194.28.115.255'),
	array('159.224.130.96','159.224.130.96')
	);
	$srv='';
	// convert to long string with leading zeros
	$x=explode('.',$ip);
	$ipl=str_pad($x[0],3,"0", STR_PAD_LEFT).str_pad($x[1],3,"0", STR_PAD_LEFT).str_pad($x[2],3,"0", STR_PAD_LEFT); 
	for ($j=0;$j<count($userve);$j++) {
		if (!is_array($userve[$j])) {
			$srv=$userve[$j];
		} else {
			$ips=$userve[$j];
			$x=explode('.',$ips[0]);
			$st=str_pad($x[0],3,"0", STR_PAD_LEFT).str_pad($x[1],3,"0", STR_PAD_LEFT).str_pad($x[2],3,"0", STR_PAD_LEFT); 
			$x=explode('.',$ips[1]);
			$en=str_pad($x[0],3,"0", STR_PAD_LEFT).str_pad($x[1],3,"0", STR_PAD_LEFT).str_pad($x[2],3,"0", STR_PAD_LEFT); 
			if ($ipl>=$st && $ipl<=$en) { 
				// bad one
				return $srv;
			}
		}
	}
	return false;
}
function kpg_dnsbl_data_old($ip,$data,$apikey='') {
	$lookup = implode('.', array_reverse(explode ('.', $ip ))) . $data;
	if (!empty($apikey)) $lookup=$apikey.'.'.$lookup;
	$result = explode( '.', gethostbyname($lookup));
	$retip=$ip;
	if (count($result)==4) $retip=$result[3].'.'.$result[2].'.'.$result[1].'.'.$result[0];
	if (count($result)==4&& $retip!=$ip) {
		if ($result[0] == 127) {
			// query successful
			// 127 is a good lookup hit
			//  [3] = type of threat - we are only interested in comment spam at this point - if user demand I will change.
			// [2] is the threat level. 25 is recommended
			// [1] is numbr of days since last report
			// spammers are type 1 to 7
			if ($result[2]>=25 && ($result[3]>=1&&$result[3]<=7)&&$result[1]>0) {
				return $data.':'.$result[0].','.$result[1].','.$result[2].','.$result[3];
			} 
		} 
	} 
	return false;
}

function kpg_dnsbl_data($ip,$data,$apikey='') {
	$lookup = implode('.', array_reverse(explode ('.', $ip ))) . $data;
	if (!empty($apikey)) $lookup=$apikey.'.'.$lookup;
	$ret=gethostbyname($lookup);
	$result = explode( '.', $ret);
	$retip=$ip;

	if (count($result)==8) array_shift($result);
	$ret=$result[0].'.'.$result[1].'.'.$result[2].'.'.$result[3];
	if (count($result)>4) $retip=$result[3].'.'.$result[2].'.'.$result[1].'.'.$result[0];
	if (count($result)>4 && $retip!=$ip) {
		if ($result[0] == 127) {
			// query successful
			// 127 is a good lookup hit
			//  [3] = type of threat - we are only interested in comment spam at this point - if user demand I will change.
			// [2] is the threat level. 25 is recommended
			// [1] is numbr of days since last report
			// spameers are type 1 to 7
			if ($result[2]>=25 && ($result[3]>=1&&$result[3]<=7)&&$result[1]>0) {
				return $data.':'.$result[0].','.$result[1].','.$result[2].','.$result[3];
			} 
		} 
	} 
	return false;
}
// from irongeek.com
function kpg_check_tor($ip){
	$sp=$_SERVER['SERVER_PORT'];
	if (empty($sp)) $sp=80;
	$sa=$_SERVER['SERVER_ADDR'];
	if ($sa=='127.0.0.1') return false;
	$sa=implode('.', array_reverse(explode ('.', $sa )));
	$sip=implode('.', array_reverse(explode ('.', $ip )));
	$lookup=$sip.'.'.$sp.'.'.$sa.'.ip-port.exitlist.torproject.org';
	$ret=gethostbyname($lookup);
	if ($ret=="127.0.0.2") {
		return true;
	}
	return false;
}


function kpg_check_all_dnsbl($ip) {
	if (empty($ip)) return false; // disable while I think about this.
	// just for the heck of it, I found a bunch of blacklist sites
	// these use the dns returns but don't need an api key as far as I know
	
	// altered to only check spamhaus. everybody else was too slow.
	$iplist = array(
	'sbl.spamhaus' 	=> '.sbl.spamhaus.org', 
	'xbl.spamhaus' 	=> '.xbl.spamhaus.org'
	//'dsbl' 	 	=> '.list.dsbl.org', // too slow
	// 'sorbs' 	=> '.dnsbl.sorbs.net', // sorbs reports everyone as 10.0.0.127
	//'spamcop' 	=> '.bl.spamcop.net',
	//'ordb' 		=> '.relays.ordb.org',
	//'njabl' 	=> '.dnsbl.njabl.org'
	); 
	foreach($iplist as $key=>$data) {
		$ansa=kpg_dnsbl_data($ip,$data);
		if ($ansa!==false) {
			return $ansa;
		}
	}
	return false;
}

function kpg_sp_checkPayPal($ip) { // returns true if a whitelisted paypal ip
	$paypal=array('173.0.88.66','173.0.88.98','173.0.84.66','173.0.84.98','66.211.168.91','66.211.168.123','173.0.88.67','173.0.88.99','173.0.84.99','173.0.84.67','66.211.168.92','66.211.168.124','173.0.88.69','173.0.88.101','173.0.84.69','173.0.84.101','66.211.168.126','66.211.168.194','173.0.88.68','173.0.88.100','173.0.84.68','173.0.84.100','66.211.168.125','66.211.168.195','173.0.81.1','173.0.81.33','66.211.170.66','216.113.188.100','66.211.168.93','173.0.80.0/20','64.4.240.0/20','66.211.160.0/19','118.214.15.186','118.215.103.186','118.215.119.186','118.215.127.186','118.215.15.186','118.215.151.186','118.215.159.186','118.215.167.186','118.215.199.186','118.215.207.186','118.215.215.186','118.215.231.186','118.215.255.186','118.215.39.186','118.215.63.186','118.215.7.186','118.215.79.186','118.215.87.186','118.215.95.186','202.43.63.186','69.192.31.186','72.247.111.186','88.221.43.186','92.122.143.186','92.123.151.186','92.123.159.186','92.123.163.186','92.123.167.186','92.123.179.186','92.123.183.186','92.123.199.186','92.123.203.186','92.123.207.186','92.123.211.186','92.123.215.186','92.123.219.186','92.123.247.186','92.123.255.186','95.100.31.186','96.16.199.186','96.16.23.186','96.16.247.186','96.16.255.186','96.16.39.186','96.16.55.186','96.17.47.186','96.6.239.186','96.6.79.186','96.7.175.186','96.7.191.186','96.7.199.186','96.7.231.186','96.7.247.186','216.113.188.64','216.113.188.34','173.0.84.178','173.0.84.212','173.0.88.178','173.0.88.212','66.211.168.136','66.211.168.66','173.0.88.203','173.0.84.171','173.0.84.203','173.0.88.171','66.211.168.142','66.211.168.150','173.0.84.76','173.0.88.76','173.0.84.108','173.0.88.108','66.211.168.158','66.211.168.180','118.214.15.186','118.215.103.186','118.215.119.186','118.215.127.186','118.215.15.186','118.215.151.186','118.215.159.186','118.215.167.186','118.215.199.186','118.215.207.186','118.215.215.186','118.215.231.186','118.215.255.186','118.215.39.186','118.215.63.186','118.215.7.186','118.215.79.186','118.215.87.186','118.215.95.186','202.43.63.186','69.192.31.186','72.247.111.186','88.221.43.186','92.122.143.186','92.123.151.186','92.123.159.186','92.123.163.186','92.123.167.186','92.123.179.186','92.123.183.186','92.123.199.186','92.123.203.186','92.123.207.186','92.123.211.186','92.123.215.186','92.123.219.186','92.123.247.186','92.123.255.186','95.100.31.186','96.16.199.186','96.16.23.186','96.16.247.186','96.16.255.186','96.16.39.186','96.16.55.186','96.17.47.186','96.6.239.186','96.6.79.186','96.7.175.186','96.7.191.186','96.7.199.186','96.7.231.186','96.7.247.186',
	// sandbox
	'173.0.82.75','173.0.82.91','173.0.82.77','173.0.82.78','173.0.82.79','173.0.82.75','173.0.82.126','173.0.82.83','173.0.82.84','173.0.82.86','173.0.82.89','173.0.82.101'
	);
	return in_array($ip,$paypal);
}

function kpg_stop_spam_log() {
	// when trying to find new spammers use this to dump info about those who pass all tests.
	// Of the comments and logins that pass, very few are not from spammers or hackers.
	$r="\r\n=============================================\r\n";
	$r.=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
	$r.="\r\n".print_r($_POST,true);
	$r.="\r\n".print_r($_SERVER,true);
	$r.="\r\n";
	// save to a file
	$f=fopen(dirname(__FILE__)."/sfs_log.txt",'a');
	fwrite($f,$r);
	fclose($f);
	return;
}


?>