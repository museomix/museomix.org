<?php

/* VT Debug
   ======== */
function DeboggeurAdmin() {
	if($GLOBALS['VAR_TEST']){
		VT($GLOBALS['VAR_TEST']);
	}
	VT();
}

add_action('admin_footer', 'DeboggeurAdmin');

function VT($v='sortie'){
	if(get_current_user_id()!==1&&get_current_user_id()!==8){ return; }
	global $VarTest;
	if($v!=='sortie'){
		if($v===NULL) { $v = 'NULL' ; }
		elseif($v===false) { $v = 'false' ; }
		elseif($v===true) { $v = 'true' ; }
		$VarTest[] = $v;
		return;
	}
	if($v=='sortie'&&!is_array($VarTest)){ return; }
	echo '<textarea cols="30" rows="10" style="width 100px; position: fixed; right: 30px; top: 200px; z-index: 100; bottom: 10px; height: auto; overflow: auto;">';
	ob_start();
	foreach($VarTest as $c => $m){
		if(!is_array($m)&&!is_object($m)){ $m = (string)$m;}
		var_dump($m);
		echo "-----".PHP_EOL;
	}
	$a=ob_get_contents();
	ob_end_clean();
	echo htmlspecialchars($a,ENT_QUOTES); 
	echo '</textarea>';
}

function ConnecterPage($uri){
	$curl = curl_init($uri);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);	
	$reponse = curl_exec($curl);
	curl_close($curl);
	return $reponse;
}
