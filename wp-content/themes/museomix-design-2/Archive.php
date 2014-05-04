<?php

/* affichage titre archive automatique
   =================================== */
function TitreArchive(){
	$titre = get_the_title();
	$titre = str_replace(' &#8211; ','<br />',$titre);
	echo $titre;
}

?>

<div class="" style="min-width: 230px; float: left; margin-left: 0; margin-top: 30px; margin-bottom: 50px; margin-left: 30px; min-height: 250px;">

<?php if ( have_posts() ) : ?>

	<ul style="font-size: 18px; list-style-type: none; padding: 0; margin: 0;">

	<?php while ( have_posts() ) : the_post(); ?>

		<li class="elm-bloc-archive">
			
			<a class="ln-bloc-archive btn btn-large" href="<?php the_permalink(); ?>">
				<?php
				//We check if the "Edition" has a thumbnail and we display it
				if ($post->post_type == 'edition')
				{
					$thumb = get_field('visuel_listitem_edition');
					if (!empty($thumb))
					{
						echo '<div class="edition_thumbnail">'.wp_get_attachment_image($thumb['id'], 'edition_thumbnail').'</div>';
					}
				}
				?>
			
				<span class="titre-bloc-archive">
			
					<span style="display: inline-block; margin: 0 10px"><?php TitreArchive(); ?></span>

				</span>
			
			</a>
		
		</li>

	<?php endwhile; ?>

	</ul>

<?php endif; ?>

</div>
