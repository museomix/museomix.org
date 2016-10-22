<?php
/*
 * Template Name: Moteur de recherche prototypes
 */
?>
<?php
get_header(); 
get_template_part('Menu');
get_template_part('TitrePage');


?>
<div class="container">
	<div class="row-fluid">
		<div class="menu-actualites span3 moteur-recherche">
			<h3>Lieux</h3>
			<?php echo facetwp_display( 'facet', 'lieux' ); ?>
			<h3>Années</h3>
			<?php echo facetwp_display( 'facet', 'annee' ); ?>
			<h3>Prototype perennisé ?</h3>
			<?php echo facetwp_display( 'facet', 'prototype_perennise' ); ?>
			<h3>Thème</h3>
			<?php echo facetwp_display( 'facet', 'theme' ); ?>
			<h3>Technologie</h3>
			<?php echo facetwp_display( 'facet', 'technologie' ); ?>
			<h3>Mots-clés</h3>
			<?php echo facetwp_display( 'facet', 'mots_cles' ); ?>
			<h3>Concerne</h3>
			<?php echo facetwp_display( 'facet', 'perimeter' ); ?>
			<!-- <h3>Rechercher</h3> -->
			<?php //echo facetwp_display( 'facet', 'rechercher' ); ?>
		</div><div class="bloc-page span9">
			<?php echo facetwp_display( 'pager' ); ?>
			<?php echo facetwp_display( 'template', 'prototypes' ); ?>
			<?php echo facetwp_display( 'pager' ); ?>
		</div>		
</div>
<?php get_template_part('PiedDePage'); ?>