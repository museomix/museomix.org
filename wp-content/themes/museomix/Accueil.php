<?php while ( have_posts() ) : the_post(); ?>

<?php # code inclusion Vimeo  ?>

<div style="height: 438px;background: #fff; text-align: center; margin: 30px 0 0">

	<iframe src="http://player.vimeo.com/video/68336319?byline=0&amp;portrait=0&amp;color=FFEC00" width="780" height="438" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>

</div>

<?php # fin code inclusion Vimeo  ?>

<?php # code contenu page Accueil  ?>

<div style="">

	<?php # the_content(); ?>

</div>

<?php # fin code contenu page Accueil  ?>

<?php endwhile; ?>

<?php # code Flux Twitter  ?>

<div class="" style="background: #eee; padding: 30px 30px 50px;">
 
	<div class="bloc-flux flux-twitter" data-requete="@museomix">
	
		<h3 class="titre-bloc-flux">
			
			@<a class="ln-titre-bloc-flux" target="ext" href="https://twitter.com/museomix">Museomix global</a>
	
		</h3>

		<div id="anim-charg-1" class="anim-charg"></div>
	
	</div>

	<div class="bloc-flux flux-twitter" data-requete="@museomixuk">
	
		<h3 class="titre-bloc-flux">

			@<a class="ln-titre-bloc-flux" target="ext" href="https://twitter.com/museomixuk">Museomix uk</a>
	
		</h3>

		<div id="anim-charg-4" class="anim-charg"></div>
	
	</div>

	<div class="bloc-flux flux-twitter" data-requete="@museomixenNord">
	
		<h3 class="titre-bloc-flux">

			@<a class="ln-titre-bloc-flux" target="ext" href="https://twitter.com/MuseomixenNord">Museomix en Nord</a>
	
		</h3>

		<div id="anim-charg-5" class="anim-charg"></div>
	
	</div>

	<div class="bloc-flux flux-twitter" data-requete="@museomixQc">
	
		<h3 class="titre-bloc-flux">

			@<a class="ln-titre-bloc-flux" target="ext" href="https://twitter.com/MuseomixQc">Museomix Québec</a>
	
		</h3>

		<div id="anim-charg-6" class="anim-charg"></div>
	
	</div>

	<div class="bloc-flux flux-twitter" data-requete="@museomixNantes">
	
		<h3 class="titre-bloc-flux">

			@<a class="ln-titre-bloc-flux" target="ext" href="https://twitter.com/MuseomixNantes">Museomix Nantes</a>
	
		</h3>

		<div id="anim-charg-7" class="anim-charg"></div>
	
	</div>

	<div class="bloc-flux flux-twitter" data-requete="@museomixra">
	
		<h3 class="titre-bloc-flux">

			@<a class="ln-titre-bloc-flux" target="ext" href="https://twitter.com/museomixra">Museomix Rhône-Alpes</a>
	
		</h3>

		<div id="anim-charg-7" class="anim-charg"></div>
	
	</div>



<div class="bloc-page" style="position: relative; padding-top: 23px;">

	<div class="contenu-page">

		<?php if ( have_posts() ) : ?>
		
			<?php if( !is_singular('post') ): ?>
		
			<ul style="font-size: 18px; list-style-type: none; padding: 0; margin: 0; ">
		
			<?php while ( have_posts() ) : the_post(); ?>
		
				<li class="elm-bloc-actualites">
					
					<a class="ln-bloc-actualites" href="<?php the_permalink(); ?>">
					
						<span class="tx-bloc-actualites"><?php the_title(); ?></span>
					
						<span class="date-actualites" style="font-size: 15px; color: #888; margin: 0; text-decoration: none !important; background: #eee"><?php echo DateBillet(strtotime(get_the_time('Y/m/d g:i:s A'))); ?></span>
					
						<br /><span class="extrait" style="color: #999; font-size: 15px; margin: 0; text-decoration: none !important"><?php the_post(); ?></span>
					
					</a>
				
				</li>
		
			<?php endwhile; ?>
		
			</ul>

		<?php endif; ?>
	</div>
</div>


<!--
	<div class="bloc-flux flux-twitter" data-requete="$museomix">

		<h3 class="titre-bloc-flux">

			#<a class="ln-titre-bloc-flux" target="ext" href="https://twitter.com/search?q=museomix">museomix</a>


		</h3>

		<div id="anim-charg-2" class="anim-charg"></div>

	</div>


	<div class="bloc-flux flux-agenda" data-requete="$museomix">

		<h3 class="titre-bloc-flux">
			agenda

		</h3>

		<div id="anim-charg-3" class="anim-charg"></div>

	</div>

-->
	<div style="clear: both;"></div>
	
</div>

<div style="min-height: 60px;">


</div>

<?php # fin code Flux Twitter  ?>