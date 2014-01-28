<?php

/* Creation des rôles
   ================== */
function CreerRoles(){
	$admin = get_role('administrator');
	$local = get_role('coordinateur_local');	
	$role = $local;	
#	$role->add_cap( 'edit_partenaire' ); 

}
# add_action( 'admin_init', 'CreerRoles' );

/* Vérification rôle
   ================= */
function RoleEst($role,$idUtilisateur=null) {
 	if(is_numeric($idUtilisateur)){
		$utilisateur = get_userdata( $idUtilisateur );
    }else{
        $utilisateur = wp_get_current_user();
    }
    if(empty($utilisateur)){
		return false;
	}
    return in_array( $role, (array) $utilisateur->roles );
}


/* Filtre champs ACF par type d'utilisateur
   ======================================== */
   
# n'afficher que les catégories des lieux d'un coordinateur
add_filter('get_terms', 'FiltreListeCategories');
function FiltreListeCategories($objet)
{
	if(!is_admin()){
	    return $objet;	
	}
	if(RoleEst('coordinateur_local')){
		$id = get_current_user_id();
		$lieux = get_pages(array('post_type'=>'museomix','post_status'=>'publish,draft,private','authors'=>$id));
		foreach($lieux as $lieu){
			$idLieux[] = $lieu->ID;
		}
		foreach($objet as $n => $term){
			if(!in_array($term->description,$idLieux)&&'1'!==$term->term_id){
				unset($objet[$n]);
			}
		}
		#VT($objet);
		return $objet; 
	}
   return $objet;
}  
     
# ne pas afficher le champ 'édition' dans Lieu   
add_filter('acf/load_field/key=field_516d858ee3c69', 'FiltreLieuChampEdition');
function FiltreLieuChampEdition($champ)
{
	if(!is_admin()){
	    return $champ;	
	}
	if(RoleEst('coordinateur_local')){
		return ''; 
	}
    return $champ;
}
 
# contraindre le champ 'lieu' dans Prototype   
add_filter('acf/fields/post_object/query/key=field_516e4f5b08137', 'FiltrePrototypeChampLieu', 10, 3);
function FiltrePrototypeChampLieu( $args, $champ, $post )
{
	if(!is_admin()){
	    return $args;	
	}
	if(RoleEst('coordinateur_local')){
		$auteurId = get_current_user_id();
		$args['authors'] = $auteurId;
	}
    return $args;
}

/* gestion de meta-cap
   =================== */

add_filter( 'map_meta_cap', 'my_map_meta_cap', 10, 4 );
function my_map_meta_cap( $caps, $cap, $user_id, $args ) {
	if(RoleEst('administrator')){
		return $caps;  
	}
	$types = array('lieu','museum','prototype');
	foreach($types as $type){
		/* If editing, deleting, or reading a lieu, get the post and post type object. */
		if ( 'edit_'.$type == $cap || 'delete_'.$type == $cap || 'read_'.$type == $cap ) {
			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );
			/* Set an empty array for the caps. */
			$caps = array();
		}
		/* If editing a lieu, assign the required capability. */
		if ( 'edit_'.$type == $cap ) {
			if ( $user_id == $post->post_author )
				$caps[] = $post_type->cap->edit_posts;
			else
				$caps[] = $post_type->cap->edit_others_posts;
		}
		/* If deleting a lieu, assign the required capability. */
		elseif ( 'delete_'.$type == $cap ) {
			if ( $user_id == $post->post_author )
				$caps[] = $post_type->cap->delete_posts;
			else
				$caps[] = $post_type->cap->delete_others_posts;
		}
		/* If reading a private lieu, assign the required capability. */
		elseif ( 'read_'.$type == $cap ) {
			if ( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_posts;
		}
	}
	
	/* Return the capabilities required by the user. */
	return $caps;
}