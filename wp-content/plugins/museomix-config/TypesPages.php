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
}
add_action( 'init', 'creer_posts_types' );

/* Post_types Editions
   =================== */
function TypesPagesEditions(){
	register_post_type(
		'edition',
		array( 
			'labels' => array(
				'name' => __('Edition','museomix-config'),
				'singular_name' => __('Edition','museomix-config'),
				'add_new' => __('Add','museomix-config'),
				'add_new_item' => __('Add an edition','museomix-config'),
				'edit_item' => __('Edit edition','museomix-config'),
				'new_item' => __('New edition','museomix-config'),
				'all_items' => __('All editions','museomix-config'),
				'view_item' => __('Show edition','museomix-config'),
				'search_items' => __('Search','museomix-config'),
				'not_found' => __('No element','museomix-config'),
				'not_found_in_trash' => __('No element in trash','museomix-config'), 
				'parent_item_colon' => '',
				'menu_name' => __('Editions','museomix-config') 
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
	if( !isset($taxono) || !$taxono)
		return;
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
				'not_found_in_trash' => __('No element in trash','museomix-config'), 
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
	if( !isset($taxono) || !$taxono)
		return;
}


/* Post_types Musées
   ================= */
function TypesPagesMuseum(){
	register_post_type(
		'museum',
		array( 
			'labels' => array(
				'name' => __('Museum','museomix-config'),
				'singular_name' => __('Museum','museomix-config'),
				'add_new' => __('Add','museomix-config'),
				'add_new_item' => __('Add a museum','museomix-config'),
				'edit_item' => __('Edit the museum','museomix-config'),
				'new_item' => __('New','museomix-config'),
				'all_items' => __('All museums','museomix-config'),
				'view_item' => __('Show museum','museomix-config'),
				'search_items' => __('Search','museomix-config'),
				'not_found' =>  __('No element','museomix-config'),
				'not_found_in_trash' => __('No element in trash','museomix-config'), 
				'parent_item_colon' => '',
				'menu_name' => __('Museums','museomix-config') 
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
	if( !isset($taxono) || !$taxono)
		return;
}


/* Post_types Prototype
   ==================== */
function TypesPagesPrototype(){
	register_post_type(
		'prototype',
		array( 
			'labels' => array(
				'name' => __('Prototype','museomix-config'),
				'singular_name' =>	__('Prototype','museomix-config'),
				'add_new' => __('Add','museomix-config'),
				'add_new_item' => __('Add a prototype','museomix-config'),
				'edit_item' => __('Edit the prototype','museomix-config'),
				'new_item' => __('New','museomix-config'),
				'all_items' => __('All prototypes','museomix-config'),
				'view_item' => __('Show prototype','museomix-config'),
				'search_items' => __('Search','museomix-config'),
				'not_found' =>  __('No element','museomix-config'),
				'not_found_in_trash' => __('No element in trash','museomix-config'), 
				'parent_item_colon' => '',
				'menu_name' => __('Prototypes','museomix-config') 
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
	if( !isset($taxono) || !$taxono)
		return;
}

/* Post_types Configuration
   ======================== */
function TypesPagesConfiguration(){
	register_post_type(
		'config',
		array( 
			'labels' => array(
				'name' => __('Component','museomix-config'),
				'singular_name' => __('Component','museomix-config'),
				'add_new' => __('Add','museomix-config'),
				'add_new_item' => __('Add a component','museomix-config'),
				'edit_item' => __('Edit component','museomix-config'),
				'new_item' => __('New','museomix-config'),
				'all_items' => __('All components','museomix-config'),
				'view_item' => __('Show component','museomix-config'),
				'search_items' => __('Search','museomix-config'),
				'not_found' =>  __('No element','museomix-config'),
				'not_found_in_trash' => __('No element in trash','museomix-config'), 
				'parent_item_colon' => '',
				'menu_name' => __('Components','museomix-config') 
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
				'name' => __('Partner','museomix-config'),
				'singular_name' => __('Partner','museomix-config'),
				'add_new' => __('Add','museomix-config'),
				'add_new_item' => __('Add a partner','museomix-config'),
				'edit_item' => __('Edit partner','museomix-config'),
				'new_item' =>	__('New','museomix-config'),
				'all_items' => __('All partners','museomix-config'),
				'view_item' => __('Show partner','museomix-config'),
				'search_items' => __('Search','museomix-config'),
				'not_found' =>  __('No element','museomix-config'),
				'not_found_in_trash' => __('No element in trash','museomix-config'), 
				'parent_item_colon' => '',
				'menu_name' => __('Partners','museomix-config') 
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
add_action( 'init', 'NewsMenu' );
add_action( 'admin_menu', 'RenommerMenuPost' );

function RenommerMenuPost(){
	global $menu;
	global $submenu;
	$menu[5][0] = __('News','museomix-config');
	$submenu['edit.php'][5][0] = __('All news','museomix-config');
	$submenu['edit.php'][10][0] = __('Add','museomix-config');
	$submenu['edit.php'][16][0] = __('keywords','museomix-config');
}

function NewsMenu() {
	global $wp_post_types;
	$labels = &$wp_post_types['post']->labels;
	$labels->name = __('News','museomix-config');
	$labels->singular_name = __('News','museomix-config');
	$labels->add_new = __('Add','museomix-config');
	$labels->add_new_item = __('Add','museomix-config');
	$labels->edit_item = __('Edit','museomix-config');
	$labels->new_item = __('New','museomix-config');
	$labels->all_item = __('All news','museomix-config');
	$labels->view_item = __('Show','museomix-config');
	$labels->search_items = __('Search','museomix-config');
	$labels->not_found = __('No element','museomix-config');
	$labels->not_found_in_trash = __('No element in trash','museomix-config');
	$labels->menu_name = __('News','museomix-config');
}