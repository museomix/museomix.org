<?php get_header(); ?>

	<?php get_template_part('Menu'); ?>
	
	<?php get_template_part('TitrePage'); ?>	

  <div class="container">
 
	<?php if( is_front_page()&&false): ?>

		<?php get_template_part('Accueil'); ?>

	<?php elseif( is_home() || is_category() || is_date() ): ?>

		<?php get_template_part('Actualites'); ?>

	<?php elseif(is_archive()): ?>

		<?php get_template_part('Archive'); ?>

	<?php elseif(is_singular('post')): ?>

		<?php get_template_part('Post'); ?>

	<?php elseif(is_singular() || is_front_page() ): ?>

    <div class="row-fluid">
    
    	<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part('MenuPage'); ?>		

    		<?php if('prototype'==$post->post_type): ?>
			<?php get_template_part('Prototype'); ?>
			<?php else: ?>	
			<?php get_template_part('Page_template'); ?>
			<?php endif; ?>
		
		<?php endwhile; ?>

	</div>

	<?php endif; ?>

	</div>

<?php get_template_part('PiedDePage'); ?>