<?php
/* distribution des listes de relations
   ==================================== */
function Relations(){
	global $post;
	$type = $post->post_type;
	if('edition'==$type){
		ListeRelations('museomix');
	}
	if('museomix'==$type){
		ListeRelations('prototype');
	}
	if('museum'==$type){
		ListeRelationsObjets('museomix');
	}
	if('sponsor'==$type){
		ListeRelationsObjets('museomix');
		ListeRelationsObjets('edition');
	}
	if('prototype'==$type){
		#ListeRelationsObjets('museomix');
	}
}

/* affichage liste relations
   ========================= */
function ListeRelations($typeCible){
	global $post;
	$typeCourant = $post->post_type;
	$id = $post->ID;
	$pages = get_pages(array('post_type'=>$typeCible,'meta_key'=>$typeCourant,'meta_value'=>$id));
	$total = count($pages);
	if(!$total){return;}
	$typeRel = get_post_type_object($typeCible);
	#$titreRelations = ($total>1) ? $typeRel->labels->menu_name : $typeRel->label;
	$titreRelations = $typeRel->labels->menu_name;
	if(count($pages)){
		foreach($pages as $page){
			$id = $page->ID;
			$r .= '<li style="margin: 6px 0;"><a href="'.get_permalink($id).'">'.get_the_title($id).'</a></li>';
		}
		echo '<h4 class="titre-para">'.$titreRelations.'</h4>';
		echo '<ul style="list-style-type: none; margin: 0;">'.$r.'</ul>';
	}
}

/* affichage liste relations objets
   ================================ */
function ListeRelationsObjets($typeCible){
	global $post;
	$typeCourant = $post->post_type;
	$idCourant = $post->ID;
	$pages = get_pages(array('post_type'=>$typeCible));
	$total = count($pages);
	if(!$total){return;}

	foreach($pages as $page){
		$idPage = $page->ID; 
		if($valeurs=get_field($typeCourant,$idPage)){
			foreach($valeurs as $val){
				if($val->ID==$idCourant){
					$r .= '<li style="margin: 6px 0;"><a href="'.get_permalink($idPage).'">'.get_the_title($idPage).'</a></li>';
				}
			}
		}
	}

	$typeRel = get_post_type_object($typeCible);
	$titreRelations = $typeRel->labels->menu_name;

	if($r){
		echo '<h4 class="titre-para">'.$titreRelations.'</h4>';
		echo '<ul style="list-style-type: none; margin: 0;">'.$r.'</ul>';	
	}

}





?>

<?php 

	global $ContenuPage, $SectionsPage, $ContenusSections;  
	if(!$ContenuPage){ $ContenuPage = apply_filters('the_content',get_the_content());} 
		
?>


<div class="bloc-page span9">

	<div class="contenu-page">
	
		<div class="bloc-contenu prototypes-page">
	<section class="section-1"  style="min-height:100px;"><?php 
			
					if($contenu=get_field('resume_du_projet')){
						$photo=get_field('visuel_prototype');
						if($photo){
							echo '<img style="float:left;margin-right:20px;" class="logo-elm-part" src="'.wp_get_attachment_url($photo, 'thumbnail').'">';
						}
						echo '<blockquote>'.$contenu.'</blockquote>';
					}
						?>
					</section>
				<?php				
				foreach($SectionsPage as $id => $titre):  ?>
	 				<?php
					$section = ContenuSectionProto($id);
					if(!empty($section)): ?>
					<section class="section-1 liste-prototype-lieu" id="<?php echo $id; ?>" style="min-height: 300px; position: relative;"> 
						
						<?php #if('presentation'!=$id&&'participer'!=$id): ?>
						
						<div class="page-header">
						
							<h1><?php echo $ContenusSections[$id]['title']; ?></h1>
						
						</div>
						
						<?php #endif; ?>
						
						<?php echo ContenuSectionProto($id); ?>
	
					</section>
				 	<?php endif; ?>
				<?php endforeach; ?>

	<?php global $withcomments;
		$withcomments = 1;
		comments_template( ); ?>
	
		</div>
	
	</div>

</div>

