<?php



/* modifications du sélecteur d'auteur de pages
   ============================================  */
add_filter('wp_dropdown_users', 'SelecteurAuteur');
function SelecteurAuteur($output){
	global $post;
	if(is_admin()){
		$liste = get_users( array('role'=>'coordinateur_local', 'orderby' => 'display_name') );
		if(!count($liste)) return $output;
		foreach($liste as $u){
			if((int)$post->post_author===(int)$u->ID){
				continue; 
			}
			$info = get_userdata($u->ID);
			$r[] = '<option value="'.$info->ID.'">'.$info->display_name.'</option>';
		}
		if($r){
			$output = str_replace('</select>',implode($r).'</select>',$output);
		}
	}
	return $output;
}





