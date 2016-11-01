<?php

	global $CatActualites;

	InitPageActualites();

	function InitPageActualites(){
		global $CatActualites;
		$CatActualites = array();
		$cat = get_categories('type=post&hide_empty=1');
		foreach($cat as $c){
			if(1==$c->term_id) continue;
			#VT($c);
			$CatActualites[$c->term_id] = $c->name; 
		}
	}
	
	function ClasseMenuCategories($id=false){

		if($id=='toutes' && is_home())
		 	echo 'active';

		elseif($id=='defaut' && is_category() && get_category(1)->name==get_queried_object()->name)
		 	echo 'active';	
	
		elseif($id && is_category() && $id==get_queried_object()->term_id)
		 	echo 'active';	
	}



?>
<div class="row-fluid">
	<div class="menu-actualites span3" style="float: left; min-height: 1px;">
	  
		<ul class="lst-menu-actualites">

			<li  ><a class="ln-menu-actualites <?php ClasseMenuCategories('toutes'); ?>" href="<?php echo get_permalink(get_option('page_for_posts')); ?>">Toutes</a></li> 	

		<?php if(count($CatActualites)): ?>

			<li ><a class="ln-menu-actualites <?php ClasseMenuCategories('defaut'); ?>"  style="margin-top: 2px;" href="<?php echo get_category_link(1); ?>"><?php echo get_category(1)->name; ?></a></li>	

		<?php endif; ?>

		<?php foreach($CatActualites as $id => $titre): ?>

			<li><a class="ln-menu-actualites <?php ClasseMenuCategories($id); ?>" href="<?php echo get_category_link($id); ?>"><?php echo $titre; ?></a></li>

		<?php endforeach; ?>

		</ul>

	</div>

	<div class="bloc-page span9">

		<div class="contenu-page">

			<?php if ( have_posts() ) : ?>
			
			
				<?php while ( have_posts() ) : the_post();
					setup_postdata($post);
					?>
			
					<h3><?php the_title(); ?></h3>
			
				<?php endwhile; ?>
			
				<?php the_content(); ?>
			
			<?php endif; ?>

		</div>

	</div>
</div>