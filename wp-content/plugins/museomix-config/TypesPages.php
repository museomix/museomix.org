<?php

function museomixconfig_init() {
  load_plugin_textdomain( 'museomix-config', false, dirname( plugin_basename( __FILE__ ) ) ); 
}
add_action('plugins_loaded', 'museomixconfig_init');

/* Creation des post_types
   ======================= */
function creer_posts_types(){
	TypesPagesEditions();
	TypesPagesMuseomix();
	TypesPagesMuseum();
	TypesPagesPrototype();
	TypesPagesConfiguration();
	TypesPagesSponsor();
#	flush_rewrite_rules();
}
add_action( 'init', 'creer_posts_types' );

/* Post_types Editions
   =================== */
function TypesPagesEditions(){
	register_post_type(
		'edition',
		array( 
			'labels' => array(
				'name' => 'Editions',
				'singular_name' => 'Edition',
				'add_new' => 'Ajouter',
				'add_new_item' => 'ajouter une Edition',
				'edit_item' => 'éditer l\'Edition',
				'new_item' => 'nouvelle édition',
				'all_items' => 'Toutes les Editions',
				'view_item' => 'voir l\'édition',
				'search_items' => 'rechercher',
				'not_found' =>  'aucun élément',
				'not_found_in_trash' => 'aucun élément dans la poubelle', 
				'parent_item_colon' => '',
				'menu_name' => 'Editions' 
			),
			'public' => true,
			'menu_position' => 20,
			'hierarchical' => true,
			'supports' => array('title'),
			'has_archive' => true,
			'rewrite' => array('slug'=>'editions'),
			'supports' => array('title','editor'),
			'capability_type' => 'page',
		)
	);	
	if( ! $taxono){ return; }
}

/* Post_types Museomix
   =================== */
function TypesPagesMuseomix(){
	$supports = (RoleEst('administrator'))? array('title','editor','author') : array('title','editor');
	register_post_type(
		'museomix',
		array( 
			'labels' => array(
				'name' => __('Locations','museomix-config'),
				'singular_name' => __('Location','museomix-config'),
				'add_new' => __('Add','museomix-config'),
				'add_new_item' => __('Add a location','museomix-config'),
				'edit_item' => __('Edit the location','museomix-config'),
				'new_item' => __('New','museomix-config'),
				'all_items' => __('All the locations','museomix-config'),
				'view_item' => __('View the location','museomix-config'),
				'search_items' => __('Search','museomix-config'),
				'not_found' =>  __('No element','museomix-config'),
				'not_found_in_trash' => __('No element in the trash','museomix-config'), 
				'parent_item_colon' => '',
				'menu_name' => __('Locations','museomix-config') 
			),
			'public' => true,
			'hierarchical' => true,
			'has_archive' => true,
			'rewrite' => array('slug'=>'localisation'),
			'supports' => $supports,
			'capability_type' => 'lieu',
			'capabilities' => array(
				'read_post' => 'read_lieu',
				'edit_post' => 'edit_lieu',
				'delete_post' => 'delete_lieu',
				'edit_posts' => 'edit_lieux',
				'edit_others_posts' => 'edit_others_lieux',
				'publish_posts' => 'publish_lieux',
				'edit_published_posts' => 'edit_published_lieux',
				'read_private_posts' => 'read_private_lieux',
			),
			#'map_meta_cap' => true,
		)
	);	
	if( ! $taxono){ return; }
}


/* Post_types Musées
   ================= */
function TypesPagesMuseum(){
	register_post_type(
		'museum',
		array( 
			'labels' => array(
				'name' => 'Musée',
				'singular_name' => 'Musée',
				'add_new' => 'Ajouter',
				'add_new_item' => 'ajouter un Musée',
				'edit_item' => 'éditer le Musée',
				'new_item' => 'nouveau',
				'all_items' => 'Tous les Musées',
				'view_item' => 'voir le Musée',
				'search_items' => 'rechercher',
				'not_found' =>  'aucun élément',
				'not_found_in_trash' => 'aucun élément dans la poubelle', 
				'parent_item_colon' => '',
				'menu_name' => 'Musées' 
			),
			'public' => true,
			'hierarchical' => true,
			'has_archive' => true,
			'rewrite' => array('slug'=>'museums'),
			'supports' => array('title','editor'),
			'capability_type' => 'museum',
			'capabilities' => array(
				'read_post' => 'read_museum',
				'edit_post' => 'edit_museum',
				'delete_post' => 'delete_museum',
				'edit_posts' => 'edit_museums',
				'delete_posts' => 'delete_museums',
				'edit_others_posts' => 'edit_others_museums',
				'publish_posts' => 'publish_museums',
				'read_private_posts' => 'read_private_museums',
			),
		)
	);	
	if( ! $taxono){ return; }
}


/* Post_types Prototype
   ==================== */
function TypesPagesPrototype(){
	register_post_type(
		'prototype',
		array( 
			'labels' => array(
				'name' => 'Prototype',
				'singular_name' => 'Prototype',
				'add_new' => 'Ajouter',
				'add_new_item' => 'ajouter un Prototype',
				'edit_item' => 'éditer le Prototype',
				'new_item' => 'nouveau',
				'all_items' => 'Tous les Prototypes',
				'view_item' => 'voir le Prototype',
				'search_items' => 'rechercher',
				'not_found' =>  'aucun élément',
				'not_found_in_trash' => 'aucun élément dans la poubelle', 
				'parent_item_colon' => '',
				'menu_name' => 'Prototypes' 
			),
			'public' => true,
			'hierarchical' => true,
			'has_archive' => true,
			'rewrite' => array('slug'=>'prototypes'),
			'capability_type' => 'prototype',
			'capabilities' => array(
				'read_post' => 'read_prototype',
				'edit_post' => 'edit_prototype',
				'delete_post' => 'delete_prototype',
				'edit_posts' => 'edit_prototypes',
				'delete_posts' => 'delete_prototypes',
				'edit_others_posts' => 'edit_others_prototypes',
				'publish_posts' => 'publish_prototypes',
				'read_private_posts' => 'read_private_prototypes',
			),
		)
	);	
	if( ! $taxono){ return; }
}

/* Post_types Configuration
   ======================== */
function TypesPagesConfiguration(){
	register_post_type(
		'config',
		array( 
			'labels' => array(
				'name' => 'Composant',
				'singular_name' => 'Composant',
				'add_new' => 'Ajouter',
				'add_new_item' => 'ajouter un Composant',
				'edit_item' => 'éditer le Composant',
				'new_item' => 'nouveau',
				'all_items' => 'Tous les composants',
				'view_item' => 'voir l\'élément',
				'search_items' => 'rechercher',
				'not_found' =>  'aucun élément',
				'not_found_in_trash' => 'aucun élément dans la poubelle', 
				'parent_item_colon' => '',
				'menu_name' => 'Composants' 
			),
			'public' => false,
			'hierarchical' => true,
			'show_ui' => true,
			'supports' => array('title'),
			'capability_type' => 'page',
		)
	);	
}

/* Post_types Sponsors
   =================== */
function TypesPagesSponsor(){
	register_post_type(
		'sponsor',
		array( 
			'labels' => array(
				'name' => 'Partenaire',
				'singular_name' => 'Partenaire',
				'add_new' => 'Ajouter',
				'add_new_item' => 'ajouter un Partenaire',
				'edit_item' => 'éditer le Partenaire',
				'new_item' => 'nouveau',
				'all_items' => 'Tous les Partenaires',
				'view_item' => 'voir le Partenaire',
				'search_items' => 'rechercher',
				'not_found' =>  'aucun élément',
				'not_found_in_trash' => 'aucun élément dans la poubelle', 
				'parent_item_colon' => '',
				'menu_name' => 'Partenaires' 
			),
			'public' => true,
			'hierarchical' => true,
			'has_archive' => true,
			'rewrite' => array('slug'=>'partenaires'),
			'supports' => array('title','editor'),
			'capability_type' => 'partenaire',
			'capabilities' => array(
				'read_post' => 'read_partenaire',
				'edit_post' => 'edit_partenaire',
				'delete_post' => 'delete_partenaire',
				'edit_posts' => 'edit_partenaires',
				'delete_posts' => 'delete_partenaires',
				'edit_others_posts' => 'edit_others_partenaires',
				'publish_posts' => 'publish_partenaires',
				'read_private_posts' => 'read_private_partenaires',
			),
		)
	);
}


/* renommer post en news
   ===================== */
add_action( 'init', 'RenommerObjetPost' );
add_action( 'admin_menu', 'RenommerMenuPost' );

function RenommerMenuPost(){
	global $menu;
	global $submenu;
	$menu[5][0] = 'News';
	$submenu['edit.php'][5][0] = 'Toutes les News';
	$submenu['edit.php'][10][0] = 'ajouter une News';
	$submenu['edit.php'][16][0] = 'mots-clés';
}

function RenommerObjetPost() {
	global $wp_post_types;
	$labels = &$wp_post_types['post']->labels;
	$labels->name = 'News';
	$labels->singular_name = 'News';
	$labels->add_new = 'Ajouter';
	$labels->add_new_item = 'ajouter une News';
	$labels->edit_item = 'éditer';
	$labels->new_item = 'nouveau';
	$labels->all_item = 'Toutes les News';
	$labels->view_item = 'voir la News';
	$labels->search_items = 'recherche une News';
	$labels->not_found = 'aucun élément.';
	$labels->not_found_in_trash = 'aucun élément dans la poubelle';
	$labels->menu_name = 'Actualités';
}


/* renommer pages en infos
   ======================= */
add_action( 'init', 'RenommerObjetPage' );
add_action( 'admin_menu', 'RenommerMenuPage' );

function RenommerMenuPage(){
	global $menu;
	global $submenu;
	$menu[20][0] = 'À Propos';
	$submenu['edit.php?post_type=page'][5][0] = 'Tous les À Propos';
	$submenu['edit.php?post_type=page'][10][0] = 'ajouter un À Propos';
	$submenu['edit.php?post_type=page'][16][0] = 'mots-clés';
}

function RenommerObjetPage() {
	global $wp_post_types;
	$labels = &$wp_post_types['page']->labels;
	$labels->name = 'À Propos';
	$labels->singular_name = 'À Propos';
	$labels->add_new = 'ajouter un À Propos';
	$labels->add_new_item = 'ajouter un À Propos';
	$labels->edit_item = 'éditer le À Propos';
	$labels->new_item = 'nouveau';
	$labels->all_item = 'Tous les À Propos';
	$labels->view_item = 'voir le À Propos';
	$labels->search_items = 'recherche un À Propos';
	$labels->not_found = 'aucun élément';
	$labels->not_found_in_trash = 'aucun élément dans la poubelle';
	$labels->menu_name = 'Infos';
}

/* renommer menu ACF
   ================= */
add_action( 'admin_menu', 'RenommerMenuACF' );

function RenommerMenuACF(){
	global $menu;
	global $submenu;
	$menu[81][0] = 'Modèles';
#	$submenu['edit.php?post_type=acf'][5][0] = 'Tous les modèles';
#	$submenu['edit.php'][10][0] = 'ajouter une News';
#	$submenu['edit.php'][16][0] = 'mots-clés';
}

/* calcul automatique des titres
   ============================= */
#add_filter( 'wp_insert_post_data' , 'CalculTitreAuto' , '99', 2 );

function CalculTitreAuto($data , $postarr){
	global $post;
	$id = $post->ID;
	/*if($data['post_type']=='museomix'){ 
		if( ! isset($_POST['fields']) ){ return $data; }
		$edition = $_POST['fields']['field_516d858ee3c69'];
		$edition = get_the_title($edition);
		$ville = $_POST['fields']['field_516d845564f8b'];
		$titre = $ville.' '.$edition;
		#$data['post_title'] = $titre;
		#$data['post_name'] = sanitize_title( $titre ).'-'.$id;
	}*/
	#add_action("save_post", "flush");
	return $data;
}




