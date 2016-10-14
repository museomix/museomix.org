<?php
add_action( 'init', 'create_prototypes_taxonomies', 0 );

function create_prototypes_taxonomies() {
	$labels = array(
		'name'              => _x( 'Theme', 'taxonomy general name', 'museomix' ),
		'singular_name'     => _x( 'Theme', 'taxonomy singular name', 'museomix' ),
		'search_items'      => __( 'Search Themes', 'museomix' ),
		'all_items'         => __( 'All Themes', 'museomix' ),
		'parent_item'       => __( 'Parent Themes', 'museomix' ),
		'parent_item_colon' => __( 'Parent Themes:', 'museomix' ),
		'edit_item'         => __( 'Edit Theme', 'museomix' ),
		'update_item'       => __( 'Update Theme', 'museomix' ),
		'add_new_item'      => __( 'Add New Theme', 'museomix' ),
		'new_item_name'     => __( 'New Theme Name', 'museomix' ),
		'menu_name'         => __( 'Themes', 'museomix' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'theme_prototype' ),
	);

	register_taxonomy( 'theme_prototype', array( 'prototype' ), $args );
	
	$labels = array(
		'name'              => _x( 'Technology', 'taxonomy general name', 'museomix' ),
		'singular_name'     => _x( 'Technology', 'taxonomy singular name', 'museomix' ),
		'search_items'      => __( 'Search Technology', 'museomix' ),
		'all_items'         => __( 'All Technologies', 'museomix' ),
		'parent_item'       => __( 'Parent Technology', 'museomix' ),
		'parent_item_colon' => __( 'Parent Technology:', 'museomix' ),
		'edit_item'         => __( 'Edit Technology', 'museomix' ),
		'update_item'       => __( 'Update Technology', 'museomix' ),
		'add_new_item'      => __( 'Add New Technology', 'museomix' ),
		'new_item_name'     => __( 'New Technology Name', 'museomix' ),
		'menu_name'         => __( 'Technologies', 'museomix' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'technologie' ),
	);

	register_taxonomy( 'technologie', array( 'prototype' ), $args );
	
	$labels = array(
		'name'              => _x( 'Perimeter', 'taxonomy general name', 'museomix' ),
		'singular_name'     => _x( 'Perimeter', 'taxonomy singular name', 'museomix' ),
		'search_items'      => __( 'Search Perimeter', 'museomix' ),
		'all_items'         => __( 'All Perimeters', 'museomix' ),
		'parent_item'       => __( 'Parent Perimeter', 'museomix' ),
		'parent_item_colon' => __( 'Parent Perimeter:', 'museomix' ),
		'edit_item'         => __( 'Edit Perimeter', 'museomix' ),
		'update_item'       => __( 'Update Perimeter', 'museomix' ),
		'add_new_item'      => __( 'Add New Perimeter', 'museomix' ),
		'new_item_name'     => __( 'New Perimeter Name', 'museomix' ),
		'menu_name'         => __( 'Perimeters', 'museomix' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'perimetre' ),
	);

	register_taxonomy( 'perimetre', array( 'prototype' ), $args );
}
?>