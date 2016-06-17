<?php
/*
 * Template Name: Full width page
 * Description: Full width page
 */
?>
<?php
get_header(); 
get_template_part('Menu');
get_template_part('TitrePage');
?>
<div class="container">
	<div class="bloc-page span12 ">
		<?php the_content(); ?>
	</div>
</div>
<?php get_template_part('PiedDePage'); ?>