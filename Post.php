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

	function ExtraitBillet(){
		global $post;
		$max = 100;
		$texte = strip_tags($post->post_content);
		if(strlen($texte)>($max-10)){
			$texte = substr($texte,0,$max).'... ';
			$texte .= '<a style="white-space: nowrap;" href="'.get_permalink().'">lire la suite</a>';
		}
		echo $texte;

	}

?>

<div class="menu-actualites" style="float: left; min-height: 1px;">
  
	<ul class="lst-menu-actualites">

		<li style="width: 220px;"><a class="ln-menu-actualites <?php ClasseMenuCategories('toutes'); ?>" href="<?php echo get_permalink(get_option('page_for_posts')); ?>">Toutes</a></li> 	

	<?php if(count($CatActualites)): ?>

		<li style="width: 220px; "><a class="ln-menu-actualites <?php ClasseMenuCategories('defaut'); ?>"  style="margin-top: 2px;" href="<?php echo get_category_link(1); ?>"><?php echo get_category(1)->name; ?></a></li>	

	<?php endif; ?>

	<?php foreach($CatActualites as $id => $titre): ?>

		<li style="width: 220px; "><a class="ln-menu-actualites <?php ClasseMenuCategories($id); ?>" href="<?php echo get_category_link($id); ?>"><?php echo $titre; ?></a></li>

	<?php endforeach; ?>

	</ul>

</div>

<div class="bloc-page">

	<div class="contenu-page">

		<?php if ( have_posts() ) : ?>
		
			<ul style="font-size: 18px; list-style-type: none; padding: 10px; margin: 0; background: #eee;">
		
			<?php while ( have_posts() ) : the_post();
				setup_postdata($post);
				?>
		
				<li class="elm-bloc-actualites 1">
					
					<a class="ln-bloc-actualites" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					
					<span style="color: #999; font-size: 15px; margin: 0 10px;"><?php ExtraitBillet(); ?></span>
				
				</li>
		
			<?php endwhile; ?>
		
			</ul>
		
		<?php endif; ?>

	</div>

</div>