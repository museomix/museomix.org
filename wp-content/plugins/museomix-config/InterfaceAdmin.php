<?php




/* Ajout fichier js et css admin
   ============================= */   
function EntetesAdmin() {
	echo '<link rel="stylesheet" type="text/css" href="'
			.plugins_url('wp-admin.css', __FILE__). '" />'
			.'<script type="text/javascript" src="'
			.plugins_url('wp-admin.js', __FILE__).'"></script>';
}
add_action('admin_head', 'EntetesAdmin');

/* reordonner le menu admin
   ======================== */
add_filter('custom_menu_order', 'ReordonnerMenu'); 
add_filter('menu_order', 'ReordonnerMenu');

function ReordonnerMenu($menu_ord) {  
    if (!$menu_ord) return true;  
    $menu = array(  
        'index.php', // Tableau de bord  
        'separator1',   
        'edit.php', 
        'edit.php?post_type=page',  
        'edit.php?post_type=edition',  
        'edit.php?post_type=museomix',  
        'edit.php?post_type=prototype',  
        'edit.php?post_type=museum',  
        'edit.php?post_type=sponsor',  
        'separator2', //   
        'upload.php', // Media  
        'nav-menus.php', // menus
        'edit.php?post_type=config', // Composants  
        'edit.php?post_type=acf', // Modèles
        'separator-last',   
        'users.php', // Utilisateurs  
        'options-general.php', // Réglages  
        'plugins.php', // Extensions
        'theme-editor.php', // Editeur de thème 
#       'tools.php', // Outils
#		'link-manager.php', // Liens
#       'edit-comments.php', // Commentaires  
    ); 
    // remove_menu_page( 'edit-comments.php' );
    //remove_menu_page( 'tools.php' );
    // remove_menu_page( 'themes.php' );

    if(RoleEst('coordinateur_local')){
	    unset($menu['edit.php?post_type=page']);    	
	    remove_menu_page( 'edit.php?post_type=page' );
	    unset($menu['upload.php']);    	
	    remove_menu_page( 'upload.php' ); 
	    remove_menu_page( 'separator2' ); 
    }
    if(RoleEst('administrator')){
    	add_menu_page( 'éditeur du thème', __('Theme','museomix-config'), 'edit_theme_options', 'theme-editor.php');
    }
    return $menu;
}  

/* remanier les sous-menus
   ======================= */   
add_action('admin_menu', 'ModifierSousMenuAdmin');
function ModifierSousMenuAdmin() {   
	global $submenu;
	# déplacement du sous-menu 'Tableau de bord/Mises à jour'
	$misesajour = $submenu['index.php'][10];	
	add_submenu_page( 
		'options-general.php', 
		$misesajour[0], 
		$misesajour[0],
		$misesajour[1],
		$misesajour[2],
		''
	);
	remove_submenu_page( 'index.php', 'update-core.php' );	
    if(RoleEst('coordinateur_local')){
		remove_submenu_page( 'edit.php?post_type=museomix', 'post-new.php?post_type=museomix' );	
    }
	# déplacement du sous-menu 'Themes/Menus'
	$navmenus = $submenu['themes.php'][10];	
	add_menu_page( 
		$navmenus[0], 
		$navmenus[0],
		$navmenus[1],
		$navmenus[2]
	);
}

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




/* afficher les ids des pages
   ========================== */  
add_filter('manage_posts_columns', 'ColonnesIDs', 5);
    add_action('manage_posts_custom_column', 'ColonneId', 5, 2);
    add_filter('manage_pages_columns', 'ColonnesIDs', 5);
    add_action('manage_pages_custom_column', 'ColonneId', 5, 2);
function ColonnesIDs($defaults){
    $defaults['colonne_id'] = 'id';
    return $defaults;
}
function ColonneId($column_name, $id){
        if($column_name === 'colonne_id'){
                echo $id;
    }
}

/* rediriger après login
   =====================  */
function my_login_redirect( $redirect_to, $request, $user ){
    return home_url().'/wp-admin/index.php';
}
add_filter("login_redirect", "my_login_redirect", 10, 3);


/* Config Tableau de bord
   ====================== */
function remove_dashboard_widgets() {
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
#	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['welcome-panel']);
#	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
#	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
} 
add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );

add_action( 'load-index.php', 'remove_welcome_panel' );

function remove_welcome_panel()
{
    remove_action('welcome_panel', 'wp_welcome_panel');
    $user_id = get_current_user_id();
    if (0 !== get_user_meta( $user_id, 'show_welcome_panel', true ) ) {
        update_user_meta( $user_id, 'show_welcome_panel', 0 );
    }
}

/* notice admin menu général
   =========================  */
function NoticeMenuGeneral(){
    global $pagenow;
    if ( $pagenow == 'plugins.php' ) {
         echo '<div class="updated">
             <p>This notice only appears on the plugins page.</p>
         </div>';
    }
}
add_action('admin_notices', 'NoticeMenuGeneral');


