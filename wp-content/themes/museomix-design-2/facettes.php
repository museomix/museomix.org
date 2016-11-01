<?php
/*
 * Template Name: Page de recherche
 * Description: Page de recherche
 */
?>
<?php
get_header(); 
get_template_part('Menu');
get_template_part('TitrePage');
?>
<div class="container">
		<div class="bloc-page  ">
<?php
echo "Année";
echo facetwp_display( 'facet', 'annee' );
echo "Lieu";
echo facetwp_display( 'facet', 'lieu' );

// Display a template
echo facetwp_display( 'pager' );

echo facetwp_display( 'template', 'prototypes' );

echo facetwp_display( 'pager' );
// Display pagination
?>
	</div>
</div>
<?php wp_footer(); ?>
