<?php
// dumped the utility functions into it's own separate file
// I am trying to keep the plugin foorprint down as low as possible

// rename each function with an _l and then call after a load

function kpg_append_file_l($filename,$content) {
	// this writes content to a file in the uploads director in the 'stop-spammer-registrations' directory
	// changed to write to the current directory - content_dir is a bad place
	$file=dirname(__FILE__).'/'.$filename;
	$f=@fopen($file,'a');
	if (!$f) return false;
	fwrite($f,$content);
	fclose($f);
	@chmod($file,0640); // read/write for owner and owners groups.
	return true;
}
function kpg_read_file_l($filename) {
	// read file
	$file=dirname(__FILE__).'/'.$filename;
	if (file_exists($file)) {
		return file_get_contents($file);
	}
	return "File not found";
}
function kpg_file_exists_l($filename) {
	$file=dirname(__FILE__).'/'.$filename;
	if (!file_exists($file)) return false;
	return filesize($file);
}
function kpg_file_delete_l($filename) {
	$file=dirname(__FILE__).'/'.$filename;
	return @unlink($file);
}
// debug functions
// change the debug=false to debug=true to start debugging.
// the plugin will drop a file sfs_debug_output.txt in the current directory (root, wp-admin, or network) 
// directory must be writeable or plugin will crash.

function sfs_errorsonoff_l($old=null) {
	$debug=true;  // change to true to debug, false to stop all debugging.
	if (!$debug) return;
	if (empty($old)) return set_error_handler("sfs_ErrorHandler");
	restore_error_handler();
}
function sfs_debug_msg_l($msg) {
	// used to aid debugging. Adds to debug file
	$debug=false;
	if (!$debug) return;
	$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
	// get the program that is running
	$sname=$_SERVER["REQUEST_URI"];	
	if (empty($sname)) {
		$sname=$_SERVER["SCRIPT_NAME"];	
	}
	
	$f='';
	$f=@fopen(dirname(__FILE__)."/.sfs_debug_output.txt",'a');
	if(empty($f)) return false;
	@fwrite($f,$now.": ".$sname.", ".$msg."\r\n");
	@fclose($f);

}
function sfs_ErrorHandler_l($errno, $errmsg, $filename, $linenum, $vars) {
	// write the answers to the file
	// we are only conserned with the errors and warnings, not the notices
	//if ($errno==E_NOTICE || $errno==E_WARNING) return false;
	if ($errno==2048) return; // wordpress throws deprecated all over the place.
	$serrno="";
	if (
			(strpos($filename,'stop-spam')===false)
			&&(strpos($filename,'sfr_mu')===false)
			&&(strpos($filename,'settings.php')===false)
			&&(strpos($filename,'options-general.php')===false)
			) return false;
	switch ($errno) {
	case E_ERROR: 
		$serrno="Fatal run-time errors. These indicate errors that can not be recovered from, such as a memory allocation problem. Execution of the script is halted. ";
		break;
	case E_WARNING: 
		$serrno="Run-time warnings (non-fatal errors). Execution of the script is not halted. ";
		break;
	case E_NOTICE: 
		$serrno="Run-time notices. Indicate that the script encountered something that could indicate an error, but could also happen in the normal course of running a script. ";
		break;
		default;
		$serrno="Unknown Error type $errno";
	}
	if (strpos($errmsg,'modify header information')) return false;

	$msg="
	Error number: $errno
	Error type: $serrno
	Error Msg: $errmsg
	File name: $filename
	Line Number: $linenum
	---------------------
	";
	// write out the error
	$f='';
	$f=@fopen(dirname(__FILE__)."/.sfs_debug_output.txt",'a');
	if(empty($f)) return false;
	@fwrite($f,$msg);
	@fclose($f);
	return false;
}
function sfs_handle_ajax_sub_l($data) {
	if (!ipChkk()) {
		return;
	}

	// get the stuff from the $_GET and call stop forum spam
	// this tages the stuff from the get and uses it to do the get from sfs
	// get the configuration items
	$options=kpg_sp_get_options();
	if (empty($options)) { // can't happen?
		echo "No Options set";
		exit();
	}
	//print_r($options);
	extract($options);
	// get the comment_id parameter	
	$comment_id=urlencode($_GET['comment_id']);
	if (empty($comment_id)) {
		echo "No comment id found";
		exit();
	}
	// need to pass the blog id also
	$blog='';
	$blog=$_GET['blog_id'];
	if ($blog!='') {
		if (function_exists('switch_to_blog')) switch_to_blog($blog);
	} 
	// get the comment
	$comment=get_comment( $comment_id, ARRAY_A );
	if (empty($comment)) {
		echo "No comment found for $comment_id";
		exit();
	}
	//print_r($comment);
	$email=urlencode($comment['comment_author_email']);
	$uname=urlencode($comment['comment_author']);
	$ip_addr=$comment['comment_author_IP'];
	// code added as per Paul at sto Forum Spam
	$content=$comment['comment_content'];
	$evidence=$comment['comment_author_url'];
	if ($blog!='') {
		if (function_exists('restore_current_blog')) restore_current_blog();
	}

	if (empty($evidence)) $evidence='';
	preg_match_all('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@',$content, $post, PREG_PATTERN_ORDER);
	$urls1=array();
	$urls2=array();
	if (is_array($post)&&is_array($post[1])) $urls1 = array_unique($post[1]); else $urls1 = array(); 
	//bbcode
	preg_match_all('/\[url=(.+)\]/iU', $content, $post, PREG_PATTERN_ORDER);
	if (is_array($post)&&is_array($post[0])) $urls2 = array_unique($post[0]); else $urls2 = array(); 
	$urls3=array_merge($urls1,$urls2);
	if (is_array($urls3)) $evidence.="\r\n".implode("\r\n",$urls3);	
	$evidence=urlencode(trim($evidence,"\r\n"));
	if (strlen($evidence)>128) $evidence=substr($evidence,0,125).'...';
	
	if (empty($apikey)) {
		echo "Cannot Report Spam without API Key";
		exit();
	}
	$hget="http://www.stopforumspam.com/add.php?ip_addr=$ip_addr&api_key=$apikey&email=$email&username=$uname&evidence=$evidence";
	//echo $hget;
	$ret=@kpg_sfs_reg_getafile($hget);
	if (stripos($ret,'data submitted successfully')!==false) {
		echo $ret;
	} else if (stripos($ret,'recent duplicate entry')!==false) {
		echo ' recent duplicate entry ';
	} else {
		echo $ret;
	}
}


function sfs_handle_ajax_check_l($data) {
	if (!ipChkk()) {
		echo "not enabled";
		exit();
	}
	// this does a call to the sfs site to check a known spammer
	// returns success or not
	$query="http://www.stopforumspam.com/api?ip=91.186.18.61";
	$check='';
	$check=kpg_sfs_reg_getafile($query);
	if (!empty($check)) {
		$check=trim($check);
		$check=trim($check,'0');
		if (substr($check,0,4)=="ERR:") {
			echo "Access to the Stop Forum Spam Database shows errors\r\n";
			echo "response was $check\r\n";
		}
		//Access to the Stop Forum Spam Database is working
		$n=strpos($check,'<response success="true">');
		if ($n===false) {
			echo "Access to the Stop Forum Spam Database is not working\r\n";
			echo "response was\r\n $check\r\n";
		} else {
			echo "Access to the Stop Forum Spam Database is working";
		}
	} else {
		echo "No response from the Stop Forum Spam AP Call\r\n";
	}
	return;
}


function sfs_handle_ajax_new_l() {
	if (!ipChkk()) {
		echo "not enabled";
		exit();
	}
	// this is the call that handles the call to ajax
	// step 1: Create the script that handles the action
	?>
	<script type="text/javascript" >
	var sfs_ajax_who=null; //use this to update the message in the click
	function sfs_ajax_report_spam(t,id,blog,url) {
		sfs_ajax_who=t;
		
		var data= {
action: 'sfs_sub',
			blog_id: blog,
			comment_id: id,
			ajax_url: url
		}
		jQuery.get(ajaxurl, data, sfs_ajax_return_spam);
	}
	function sfs_ajax_return_spam(response) {
		sfs_ajax_who.innerHTML="Spam reported";
		sfs_ajax_who.style.color="green";
		sfs_ajax_who.style.fontWeight="bolder";
		//alert(response);
		if (response.indexOf('data submitted successfully')>0) {
			return false;
		}
		if (response.indexOf('recent duplicate entry')>0) {
			sfs_ajax_who.innerHTML="Spam Already reported";
			sfs_ajax_who.style.color="brown";
			sfs_ajax_who.style.fontWeight="bolder";
			return false;
		}
		sfs_ajax_who.innerHTML="Error reporting spam";
		sfs_ajax_who.style.color="red";
		sfs_ajax_who.style.fontWeight="bolder";
		alert(response);
		return false;
	}
	</script>
	<?php		
}

// search functions
// badly in need of an overhaul
function kpg_wildcard($needle,$haystack) {
	$needle=strtolower($needle);
	foreach($haystack as $val) {
		if (strpos($val,'*')!==false) {
			// wild card searches - 
			$n1=strtolower(substr($val,0,strpos($val,'*')));
			if ($n1==substr(strtolower($needle),0,strlen($n1))) return true;
		}
	}
	return false;
}
function kpg_sp_searchi_l($needle,$haystack) { // searches case insensitive for occurrence in array
	// ignore case in_array
	if (empty($needle)) return false;
	if (empty($haystack)) return false;
	if (!is_array($haystack)) return false;
	if (kpg_wildcard($needle,$haystack)) return true;	
	foreach($haystack as $val) {
		if (strtolower($val)==strtolower($needle)) return true;
	}
	return false;
}

function kpg_sp_search_ip_l($needle,$haystack) {
	// ignore case in_array
	if (empty($needle)) return false;
	if (empty($haystack)) return false;
	if (!is_array($haystack)) return false;
	foreach($haystack as $sip=>$val) {
		if (strtolower($sip)==strtolower($needle)) return true;
		if (kpg_ip_range($sip,$needle)) return true;
	}
	return false;
}
function kpg_sp_searchi_ip_l($needle,$haystack) { // search for IP, but also use subnet notation
	// ignore case in_array
	if (empty($needle)) return false;
	if (empty($haystack)) return false;
	if (!is_array($haystack)) return false;
	if (kpg_wildcard($needle,$haystack)) return true;	
	foreach($haystack as $sip) {
		if (strtolower($sip)==strtolower($needle)) return true;
		if (kpg_ip_range($sip,$needle)) return true;
	}
	return false;
}

function kpg_ip_range_l($ipr,$ip) {
	if (count(explode('.',$ip))!=4) return false;
	if (strpos($ipr,'/')===false) return false;
	$ips=explode('/',$ipr);
	if (count($ips)!=2) return false;
	$ip1=$ips[0];
	$m=$ips[1];
	if (count(explode('.',$ip1))!=4) return false;
	$mask=(pow(2,32)-1)-(pow(2,32-$m)-1);
	$i=abs(ip2long($ip));
	$r=abs(ip2long($ip1) & ($mask));
	$mask=$mask ^ -1;
	$r2=abs(ip2long($ip1) | ($mask));
	if ($r<$r2) { // since the result can be negative when converted to an integer and get screwed up
		if ($i>=$r&&$i<=$r2) return true;
		return false;
	}
	if ($i>=$r2&&$i<=$r) return true;
	return false;
}

function kpg_sp_searchL_l($needle,$haystack) {
	// search the end of a string case insensitive
	if (empty($needle)) return false;
	if (empty($haystack)) return false;
	// check for wild cards
	// this searches for TLDs at the end of the email
	// the needle has the tld of the email without the '.'
	$needle='.'.strtolower($needle).'~';
	foreach($haystack as $val) {
		$val='.'.strtolower($val).'~';
		if (strpos($val,$needle)!==false) return true;
	}
	return false;
}
// more
function kpg_sfs_reg_getafile_l($f) {
	// try this using Wp_Http
	if( !class_exists( 'WP_Http' ) )
	include_once( ABSPATH . WPINC. '/class-http.php' );
	$request = new WP_Http;
	$result = $request->request( $f );
	// see if there is anything there
	if (empty($result)) return '';
	
	if (is_array($result)) {
		$ansa=$result['body']; 
		return $ansa;
	}
	if (is_object($result) ) {
		$ansa='ERR: '.$result->get_error_message();
	}
	return '';
}
function kpg_sfs_reg_add_user_to_blacklist_l($options) {
	// remove from whitelist and add to blacklist
	$options=kpg_sp_get_options();
	$wlist=$options['wlist'];
	$blist=$options['blist'];
	//$ip=kpg_get_ip();
	$ip=$_SERVER['REMOTE_ADDR'];
	if (in_array($ip,$wlist)) { // remove it
		$k=array_search($ip,$wlist);
		$wlist=array_splice($wlist,$k,1);
	}
	if (!in_array($ip,$blist)) { // add it
		$blist[count($blist)]=$ip;
	}
	$options['wlist']=$wlist;
	$options['blist']=$blist;
	update_option('kpg_stop_sp_reg_options',$options);
}
function kpg_sfs_reg_add_user_to_goodip_l() { // in case I need it later
}
function kpg_sfs_reg_add_user_to_badip_l() {
	// badips in stats
	$ret=true;
	$stats=kpg_sp_get_stats();
	$badips=$stats['badips'];
	$goodips=$stats['goodips'];
	//$ip=kpg_get_ip();
	$ip=$_SERVER['REMOTE_ADDR'];
	$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
	if (array_key_exists($ip,$goodips)) {
		unset($goodips[$ip]);
		$stats['goodips']=$goodips;
	}
	if (!array_key_exists($ip,$badips)) {
		$badips[$ip]=$now;
		$stats['badips']=$badips;
	} else {
		// don't count bad guys already in bad ips.
		$ret=false; // already there don't update log or cache
	}
	update_option('kpg_stop_sp_reg_stats',$stats);
	return $ret;
}

function kpg_sfs_reg_add_user_to_whitelist_l($options) {
	$addtowhitelist=$options['addtowhitelist'];
	$wlist=$options['wlist'];
	//$ip=kpg_get_ip();
	$ip=$_SERVER['REMOTE_ADDR'];
	if ($addtowhitelist=='Y'&&!in_array($ip,$wlist)) {
		// add this ip to your white list
		$wlist[count($wlist)]=$ip;
		$options['wlist']=$wlist;
	}
	update_option('kpg_stop_sp_reg_options',$options);

}



?>