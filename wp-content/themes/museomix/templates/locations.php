<?php
/*
 * Template Name: Lieux/Location page
 * Description: La page des lieux / The locations page
 */
?>
<?php
get_header(); 
get_template_part('Menu');
get_template_part('TitrePage');
$edition_to_display = get_field('edition');
$locations = new WP_Query( array(
	'post_type' => 'museomix', //The post type for locations
	'meta_key' => 'edition',
	'meta_value' => $edition_to_display->ID
));
?>
<div class="container">
		<div class="bloc-page  ">
			<div class="lst-bloc-lieux ">
				<?php
				$i = 0;
				while ( $locations->have_posts() ) :
					$locations->the_post();
					if(!$imag=wp_get_attachment_image_src(get_field('vignette_lieu',$id),"thumbnail")) {
						$imag= wp_get_attachment_image_src(get_field('visuel_page',$id),"large");
					}
					$imag = $imag[0];
				if (($i % 4 == 0) || ($i == 0))
					echo '<div class="row-fluid">';
				?>
					<div class="span3" data-i = "<?php echo $i; ?>">
						<a class="ln-bloc-lieu btn btn-large" style="background-image: url(<?php echo $imag; ?>);" href="<?php the_permalink(); ?>">
							<span class="titre-bloc-lieu">
								<span class="tx-bloc-lieu">
									<?php the_title(); ?>
								</span>
							</span>
						</a>
					</div>
				<?php
				$i++;
				if (($i % 4 == 0) && ($i > 0) || ($locations->found_posts == $i))
					echo '</div>';
				
				endwhile;
				?>
			</div>
		</div>
</div>
<?php get_template_part('PiedDePage'); ?>