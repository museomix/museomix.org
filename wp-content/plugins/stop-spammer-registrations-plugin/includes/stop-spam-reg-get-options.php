<?php

function kpg_sp_get_stats_l() {
	// check to see if we need to load the option redirector
	$stats=get_option('kpg_stop_sp_reg_stats');
	if (empty($stats)||!is_array($stats)) $stats=array();
	$options=array(
	'badips'=>array(),
	'goodips'=>array(),
	'hist'=>array(),
	'wlreq'=>array(),
	
	'spcount'=>0,
	'spmcount'=>0,
	
	'cntsfs'=>0,
	'cntreferer'=>0,
	
	'cntdisp'=>0,
	'cntrh'=>0,
	'cntdnsbl'=>0,
	
	'cntubiquity'=>0,
	'cntakismet'=>0,		
	'cntspamwords'=>0,
	
	'cntsession'=>0,
	'cntlong'=>0,
	'cntagent'=>0,
	
	'cnttld'=>0,
	'cntemdom'=>0,		
	'cntcacheip'=>0,

	'cntcacheem'=>0,
	'cnthp'=>0,		
	'cntbotscout'=>0,

	'cntblem'=>0,		
	'cntlongauth'=>0,
	'cntblip'=>0,

	'cntaccept'=>0,
	
	'cntpassed'=>0,		
	'cntwhite'=>0,	
	'cntgood'=>0,	
	
	'autoload'=>'N',
	'spmdate'=>'installation',

	'spdate'=>'last cleared',
	'cntadminlog'=>0,
	'poisoncnt'=>0,
	'cntspoof'=>0,
	'cnttor'=>0,
	'cntcap'=>0,
	'cntncap'=>0
	
	
	);
	$ansa=array_merge($options,$stats);
	if (!is_array($ansa['wlreq'])) $ansa['wlreq']=array();
	if (!is_array($ansa['badips'])) $ansa['badips']=array();
	if (!is_array($ansa['hist'])) $ansa['hist']=array();
	if (!is_array($ansa['goodips'])) $ansa['goodips']=array();
	if (!is_numeric($ansa['spcount'])) $ansa['spcount']=0;
	if (!is_numeric($ansa['spmcount'])) $ansa['spmcount']=0;
	if ($ansa['spcount']==0) {
		$ansa['spdate']=date('Y/m/d',time() + ( get_option( 'gmt_offset' ) * 3600 ));
		update_option('kpg_stop_sp_reg_stats',$ansa);
	}
	if ($ansa['spmcount']==0) {
		$ansa['spmdate']=date('Y/m/d',time() + ( get_option( 'gmt_offset' ) * 3600 ));
		update_option('kpg_stop_sp_reg_stats',$ansa);
	}
	if ($ansa['autoload']=='N') {
		delete_option('kpg_stop_sp_reg_stats');
		$ansa['autoload']='Y';
		add_option('kpg_stop_sp_reg_stats',$ansa, 0, 'no' );
	}

	return $ansa;
}

/*


*/
function kpg_sp_get_options_l() {
	// first see if we need to load the option redirecor
	$opts=get_option('kpg_stop_sp_reg_options');
	if (empty($opts)||!is_array($opts)) {
		$opts=array();
	}

	$options=array(
	'wlist'=>array(),
	'blist'=>array(),
	'baddomains'=>array(),
	'badTLDs'=>array(),
	'apikey'=>'',
	'honeyapi'=>'',
	'botscoutapi'=>'',
	'accept'=>'Y',
	'nobuy'=>'N',
	'chkemail'=>'Y',
	'chkip'=>'Y',
	'chksfs'=>'Y',
	'chkreferer'=>'Y',
	'chkdisp'=>'Y',
	'redherring'=>'Y',
	'chkdnsbl'=>'Y',
	'chkubiquity'=>'Y',
	'noplugins'=>'N',
	'chktor'=>'N',
	'chkakismet'=>'N',
	'chkakismetcomments'=>'N',
	'chkcomments'=>'Y',
	'chkspamwords'=>'N',
	'chklogin'=>'Y',
	'chksession'=>'Y',
	'sesstime'=>4,
	'chksignup'=>'Y',
	'chklong'=>'Y',
	'chkagent'=>'Y',
	'chkxmlrpc'=>'Y',
	'chkwpmail'=>'Y',
	'chkwplogin'=>'N',
	'chkadmin'=>'Y',
	'addtowhitelist'=>'Y',
	'sfsfreq'=>0,
	'hnyage'=>9999,
	'botfreq'=>0,
	'sfsage'=>9999,
	'hnylevel'=>5,
	'botage'=>9999,
	'kpg_sp_cache'=>25,
	'kpg_sp_cache_em'=>10,
	'kpg_sp_hist'=>25,
	'kpg_sp_good'=>2,
	'redirurl'=>'', 
	'redir'=>'N',
	'chkadminlog'=>'N',
	'logfilesize'=>0,
	'autoload'=>'N',
	'firsttime'=>'Y',
	'wlreqmail'=>'Y',
	'poison'=>'Y',
	'chkcaptcha'=>'Y',
	'rejectmessage'=>"Access Denied<br/>
This site is protected by the Stop Spammer Registrations Plugin.<br/>",
	'spamwords'=>array("-online","4u","4-u","adipex","advicer","baccarrat","blackjack","bllogspot","booker","byob","car-rental-e-site","car-rentals-e-site","carisoprodol","casino","chatroom","cialis","coolhu","credit-card-debt","credit-report","cwas","cyclen","cyclobenzaprine","dating-e-site","day-trading","debt-consolidation","debt-consolidation","discreetordering","duty-free","dutyfree","equityloans","fioricet","flowers-leading-site","freenet-shopping","freenet","gambling-","hair-loss","health-insurancedeals","homeequityloans","homefinance","holdem","hotel-dealse-site","hotele-site","hotelse-site","incest","insurance-quotes","insurancedeals","jrcreations","levitra","macinstruct","mortgagequotes","online-gambling","onlinegambling","ottawavalleyag","ownsthis","paxil","penis","pharmacy","phentermine","poker-chip","poze","pussy","rental-car-e-site","ringtones","roulette ","shemale","slot-machine","thorcarlson","top-site","top-e-site","tramadol","trim-spa","ultram","valeofglamorganconservatives","viagra","vioxx","xanax","zolus","ambien","poker","bingo","allstate","insurnce","work-at-home","workathome","home-based","homebased","weight-loss","weightloss","additional-income","extra-income","email-marketing","sibutramine","seo-","fast-cash"),
	// new fields
	'notify'=>'Y',
	// permanent links to options and history
	'net_history_link'=>'',
	'net_options_link'=>'',
	'history_link'=>'',
	'options_link'=>''

	);
	$ansa=array_merge($options,$opts);
	// check the yn questions
	
	$ynfields=array(
	'chksession','chkdisp','chksfs','chkubiquity',
	'chkwplogin','chkakismet','chkakismetcomments','noplugins',
	'chkcomments','chklogin','chksignup','chklong',
	'chkagent','chkxmlrpc','addtowhitelist','chkadmin',
	'chkspamwords','chkwpmail','redherring','chktor',
	'chkdnsbl','chkemail','chkip','chkreferer','chkcaptcha',
	'nobuy','redir','accept','poison','wlreqmail'
);
	foreach ($ynfields as $yn) {
		if ($ansa[$yn]!='Y') $ansa[$yn]='N';
	}
	if (!is_array($ansa['wlist'])) $ansa['wlist']=array();
	if (!is_array($ansa['blist'])) $ansa['blist']=array();
	if (!is_array($ansa['baddomains'])) $ansa['baddomains']=array();
	if (!is_array($ansa['badTLDs'])) $ansa['badTLDs']=array();
	if (empty($ansa['apikey'])) $ansa['apikey']='';
	if (empty($ansa['honeyapi'])) $ansa['honeyapi']='';
	if (empty($ansa['botscoutapi'])) $ansa['botscoutapi']='';
	if (!isset($ansa['kpg_sp_cache'])) $ansa['kpg_sp_cache']=25;
	if (!isset($ansa['kpg_sp_cache_em'])) $ansa['kpg_sp_cache_em']=10;
	if (!isset($ansa['kpg_sp_hist'])) $ansa['kpg_sp_hist']=25;
	if (!isset($ansa['kpg_sp_good'])) $ansa['kpg_sp_good']=2;
	if (!is_numeric($ansa['kpg_sp_good'])) $ansa['kpg_sp_good']=2;
	if (!is_numeric(trim($ansa['logfilesize']))) $ansa['logfilesize']=0;
	if (!is_array($ansa['spamwords'])) $ansa['spamwords']=array();
	if (!is_numeric($ansa['sesstime'])) $ansa['sesstime']=4;
	if ($ansa['autoload']=='N') {
		delete_option('kpg_stop_sp_reg_options');
		$ansa['autoload']='Y';
		if(!add_option('kpg_stop_sp_reg_options',$ansa, 0, 'no' )) {
			if (!update_option('kpg_stop_sp_reg_options',$ansa)) {
			}
		}
	}
	return $ansa;
}
?>