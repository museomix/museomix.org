<?php

/* ajouts Tableau de bord
   ====================== */
function Ajout_TDB() {
	global $wp_meta_boxes;
	if(RoleEst('administrator')){
		wp_add_dashboard_widget('message_test', ' ', 'MessageAppel');			
	
		global $wp_meta_boxes;
		$my_widget = $wp_meta_boxes['dashboard']['normal']['core']['message_test'];
		unset($wp_meta_boxes['dashboard']['normal']['core']['message_test']);
		$wp_meta_boxes['dashboard']['side']['core']['message_test'] = $my_widget;
	}
} 
add_action('wp_dashboard_setup', 'Ajout_TDB' );


/* Messages Accueil TDB
   ==================== */
function MessageAppel() {
	$id = 56; 
	echo get_field('infos_config',$id);
} 

