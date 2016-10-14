<?php
/* PHP file for news lists */
	global $CatActualites;

	InitPageActualites();

	function InitPageActualites(){
		global $CatActualites;
		$CatActualites = array();
		$cat = get_categories('type=post&hide_empty=1');
		foreach($cat as $c){
			if(1==$c->term_id) continue;
			#VT($c);
			$CatActualites[$c->term_id] = '@'.$c->name; 
		}
	}
	
	function ClasseMenuCategories($id=false){

		global $post;

		if($id=='toutes' && is_home())
		 	echo 'active';

		elseif($id=='1' && is_category() && get_category(1)->name==get_queried_object()->name)
		 	echo 'active';	
	
		elseif($id && is_category() && $id==get_queried_object()->term_id)
		 	echo 'active';	
		 	
		elseif($id && is_singular('post')){
			$cat = get_the_category($post->ID);
			if($id==$cat[0]->term_id){
			 	echo 'active';			 	
			}
		 }
		 
		 
	}


	
	
	
	function LienDateBillet(){
		global $post;
		$j = get_the_date('j');
		$m = get_the_date('n');
		$a = get_the_date('Y');
		return get_day_link($a,$m,$j);
	}

?>
<div class="row-fluid">
	<div class="menu-actualites span3">
	  
		<ul class="lst-menu-actualites sidebar-nav">

			<li class="li-menu-actualites"><a class="ln-menu-actualites ln-menu-actualites-tous <?php ClasseMenuCategories('toutes'); ?>" href="<?php echo get_permalink(get_option('page_for_posts')); ?>">Toutes</a></li> 	

		<?php if(count($CatActualites)): ?>

			<li class="li-menu-actualites"><a class="ln-menu-actualites <?php ClasseMenuCategories(1); ?>"  style="" href="<?php echo get_category_link(1); ?>"><?php echo '@'.get_category(1)->name; ?></a></li>	

		<?php endif; ?>

		<?php foreach($CatActualites as $id => $titre): ?>

			<li class="li-menu-actualites"><a class="ln-menu-actualites <?php ClasseMenuCategories($id); ?>" href="<?php echo get_category_link($id); ?>"><?php echo $titre; ?></a></li>

		<?php endforeach; ?>

		</ul>

	</div>

	<div class="bloc-page span9" id="news-list">

		<div class="contenu-page">

			<?php if ( have_posts() ) : ?>
			
				<?php if( !is_singular('post') ): ?>

			
				<ul style="font-size: 18px; list-style-type: none; padding: 0; margin: 0; ">
			
				<?php while ( have_posts() ) : the_post();
					setup_postdata($post);
					?>
			
					<li class="elm-bloc-actualites news-title-and-excerpt">
						
						<a class="ln-bloc-actualites" href="<?php the_permalink(); ?>">
							<span class="tx-bloc-actualites"><?php the_title(); ?></span>
							<span class="date-actualites" style="font-size: 15px; color: #888; margin: 0; text-decoration: none !important; background: #eee"><?php echo DateBillet(get_the_time('U')); ?></span>
						</a>
						<div class="extrait" style="color: #999; font-size: 15px; margin: 0; text-decoration: none !important"><?php echo get_the_excerpt(); ?>
							<br />
							<a class="small" href="<?php echo get_permalink(); ?>"><?php _e('Read more','museomix'); ?></a>
						</div>
					
					</li>
			
				<?php endwhile; ?>
			
				</ul>

				<?php else: ?>

					<?php the_post(); ?>


				<div>
					<h2 class="titre-section" style="margin-bottom:20px;"><?php TitreContenu(); ?></h2>
					<span class="date-billet" style="color: #999; display: inline-block; padding: 1px 0; margin: 0 0 15px 0; font-size: 15px;">le <?php echo date_i18n('d M',strtotime(get_the_time('Y/m/d g:i:s A'))); ?></span>
								

				</div>
				
					<div style="line-height: 23px; color: #333; max-width: 730px;">
					
						<div class="">

							<div style="max-width: 600px;">

							<?php the_content(); ?>		
					
							</div>
							
						</div>
		
						<div class="partage" style="float: right; margin-top: 13px;">
							<a title="partager via Twitter" data-service="twitter" class="ln-partage btn icon-twitter-2">Tw<span class="tx-partage">twitter</span></a>
							<a title="partager via Google+" data-service="google-plus" class="ln-partage btn icon-google-plus-2">G+<span class="tx-partage">google-plus</span></a>
							<a title="partager via LinkedIn" data-service="linkedin" class="ln-partage btn icon-linkedin-2">In<span class="tx-partage">linkedin</span></a>
							<a title="partager via Facebook" data-service="facebook" class="ln-partage btn icon-facebook-2">Fb<span class="tx-partage">facebook</span></a>
						</div>
						
						<div class="clear">
							<?php previous_post_link('<span class="bt-suiv-prec bt-prec" style="left: 40px; bottom:20px;" ">%link</span>',''); ?>
					<?php next_post_link('<span class="bt-suiv-prec bt-suiv" style="left: 80px;  bottom:20px;">%link</span>',''); ?>
					</div> 
					</div>
					
				<?php endif; ?>
			
			<?php endif; ?>

		</div>
		<?php
			mix_pagination();
		?>
		<div class="clear"></div> 

	</div>
</div>