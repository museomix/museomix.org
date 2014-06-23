<?php
/**
 * @package Museomix-config
 */
/*
Plugin Name: Museomix-config
Plugin URI: 
Description: Plugin d'extension et de configuration de Wordpress pour le projet Museomix.
Author: 
Author URI: 
License: 
*/


/* configuration générale 
   ======================  */  

define(’WP_POST_REVISIONS’, 5);   

require_once(dirname(__FILE__).'/Utilitaires.php');
require_once(dirname(__FILE__).'/TypesPages.php');
require_once(dirname(__FILE__).'/Roles.php');
if(is_admin()){
	require_once(dirname(__FILE__).'/InterfaceAdmin.php');
	require_once(dirname(__FILE__).'/Messages.php');
}

/* cacher barre d'outil admin sur le site
   ====================================== */
//add_filter('show_admin_bar', '__return_false');   

add_filter('template', 'ThemeVariable');
add_filter('stylesheet', 'ThemeVariable');

function ThemeVariable($theme){
	$theme = "museomix-design-2";
	if(get_current_user_id()===1){
		;
	}
	return $theme;
}


/* après initialisation de Wordpress 
   =================================  */   

add_action('wp',MuseomixExtensions);

function MuseomixExtensions(){
	global $wp;
	if(
		'fluxtwitter'==$_GET['action']
		||'fluxagenda'==$_GET['action']
		||'fluxtwitterjson'==$_GET['action']
	){
		require_once(dirname(__FILE__).'/Flux.php');
		if('fluxtwitter'==$_GET['action']){
			$r = FluxTwitter($_GET['requete']);
		}elseif('fluxagenda'==$_GET['action']){
			$r = FluxAgenda($_GET['requete']);
		}
		if($r){
			echo '<div>'.$r.'</div>';
		}
		exit;
	}elseif('googleform'==$_GET['action']){
		require_once(dirname(__FILE__).'/GoogleForm.php');
		$r = LireGoogleForm($_GET['requete']);
		if($r){
			echo '<div>'.$r.'</div>';
		}		
		exit;
	}elseif('tableurgoogle'==$_GET['action']){
		require_once(dirname(__FILE__).'/GoogleForm.php');
		$r = LireTableurGoogle($_GET['requete']);
		if($r){
			echo '<div>'.$r.'</div>';
		}		
		exit;
	}
	if(isset($_POST['actiongoogleform'])){
		require_once(dirname(__FILE__).'/GoogleForm.php');
		$r = EnvoyerGoogleForm();
		if($r){
			echo '<div>'.$r.'</div>';
		}		
		exit;
	}
	#VT(phpversion());

}




