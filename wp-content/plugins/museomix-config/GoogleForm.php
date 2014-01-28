<?php

# 0Alcc-Ko5QEEMdHJHM282VmRqQVZIQWs1U2tUaGtwVlE

function LireGoogleForm($urlForm){
	$uri = $urlForm/*.'&embedded=true'*/;
	#$uri = 'https://docs.google.com/forms/d/1iM8vaJPkNw68pd4bnp708Y_EKYz_8lLVg32LDE0WyWI/viewform';
	
	$reponse = ConnecterPage($uri);
	preg_match('/<h1 class="ss-form-title".*?>.+?<\\/h1>/s',$reponse,$titre);
	$titre = $titre[0];
	preg_match('/<form.+?<\\/form>/s',$reponse,$form);
	$form = $form[0];
	preg_match('/<form.+?action="([^"]+?)"/',$form,$action);
	$action = $action[1];	
	$form = preg_replace('/(<input type="submit" name="submit" value=").+?(" id="ss-submit">)
<div class="ss-secondary-text">.+?<\\/div>/s',"$1envoyer$2",$form);
	$form = preg_replace('/<input type="submit" name="continue"/','<input type="submit" name="continue" onclick="alert(\'problème\');return false;"',$form);
	$adr = site_url();
	$form = preg_replace('/<form(.+?)action="(.+?)"(.+?)>/','<form$1action="'.$adr.'"$3 class="formulaire-google"><input type="hidden" name="actiongoogleform" value="$2" />',$form);

	$form = preg_replace('/<div class="ss-q-help ss-secondary-text" dir="ltr"><\\/div>/','',$form);

	return '<div class="resultat-import">'.$titre.$form.'</div>';
}

function EnvoyerGoogleForm(){	
	if(!isset($_POST['actiongoogleform'])){ return; }
	$posts = $_POST;
	foreach($posts as $champ => $val){
		$posts[$champ] = stripslashes($val);
	}
	$action = $posts['actiongoogleform'];
	unset($posts['actiongoogleform']);
	$posts = http_build_query($posts);	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$action);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS,$posts);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$reponse = curl_exec($curl);
	curl_close($curl);
	preg_match('/<div class="ss-custom-resp">(.+?)<\\/div>/s',$reponse,$confirmation);
	
	$r = '<div class="confirmation">'.$confirmation[1].'</div>';
	return '<div class="resultat-import">'.$r.'</div>';  	
	exit;
}

function LireTableurGoogle($urlForm){
	if($urlForm){ return '<div class="resultat-import">'.$urlForm.'</div>'; }
	#$uri = 'https://docs.google.com/spreadsheet/ccc?key=0Alcc-Ko5QEEMdHJHM282VmRqQVZIQWs1U2tUaGtwVlE#gid=0';
	$uri = 'https://docs.google.com/spreadsheet/pub?key=0AhjvXaV5z5AKdHQzT1RmMlE3aGhLNi1qUTB0SW44Z1E&output=html';
	$reponse = ConnecterPage($uri);
	preg_match('/<div id="content">(.+?)<\\/div>.*?<div id="footer"/s',$reponse,$bloc);
	$bloc = $bloc[1];
	preg_match('/<style>(.+?)<\\/style>/s',$bloc,$style);
	preg_match('/<table.+?\\/table>/s',$bloc,$table);
	$style = $style[1];
	$table = $table[0];
	$r = '<div class="style">'.$style.'</div>';
	$r .= '<div class="resultat-import">'.$table.'</div>';
	return '<div>'.$r.'</div>';  	
	exit;
	V(htmlentities($table));
}


/* afficher variable 
   ================= */
if(!function_exists('V')){
function V($v){
	echo '<pre style="word-wrap: break-word;">';
	var_dump($v);
	echo '</pre>';
}
}