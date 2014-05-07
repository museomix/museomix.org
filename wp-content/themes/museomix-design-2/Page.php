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

function TitreSection($id,$langage){
	global $post;
	$titre = '';
	if('actualites'==$id){
		$titre = ($langage=="en") ? '<h1>News</h1>' : '<h1>Actualités</h1>';
	}
	elseif('presentation'==$id){
		$titre = '<h1></h1>';
	}
	elseif('partenaires'==$id){
		$titre = ($langage=="en") ? '<h1>Partners</h1>' : '<h1>Partenaires</h1>';
	}
	elseif('prototypes'==$id){
		$titre = ($langage=="en") ? '<h1>Prototypes</h1>' : '<h1>Prototypes</h1>';
	}
	elseif('equipe'==$id){
		$titre = ($langage=="en") ? '<h1>Team</h1>' : '<h1>Equipe</h1>';
	}
	elseif('galerie'==$id){
		$titre = ($langage=="en") ? '<h1>Gallery</h1>' : '<h1>Galerie</h1>';
	}
	


	echo $titre; 
}




?>

<?php 
remove_filter ('the_content', 'wpautop');

global $ContenuPage, $SectionsPage;  

if(!$ContenuPage){ $ContenuPage = apply_filters('the_content',get_the_content());} 
		
?>


<div class="bloc-page span9 centered">

	<div class="contenu-page">
	
		<?php if(trim(get_the_content())>''||'museomix'==$post->post_type): ?>
	
		<div class="bloc-contenu" style="<?php /* if($SectionsPage) echo 'margin-top: -70px;' */?>">
	
			<?php #if(266==$post->ID && 1 !== get_current_user_id()):  ?>

			<!--div class="alert alert-info">formulaire en maintenance. Merci de rééssayer plus tard.</div-->

			<?php if('museomix'==$post->post_type):  ?>
	
				<?php
				$SectionsPageId = 0;
				foreach($SectionsPage as $id => $titre) {
					if (mb_strlen(ContenuSection($id))>0) {
					?>
					
						<section class="section-1 sec1" id="<?php echo $id; ?>" style="min-height: 300px; position: relative;"> 
							
							<?php #if('presentation'!=$id&&'participer'!=$id): ?>
							
							<div class="page-header">
							
								<?php TitreSection($id,ICL_LANGUAGE_CODE); ?>
							
							</div>
							
							<?php #endif; ?>
							
							<?php ContenuSection($id); ?>
		
						</section>
					 
					<?php
					}
				}
				?>
	 
			<?php else: ?>
				<?php
				//We check if the "Edition" has a banner and we display it
				if ($post->post_type == 'edition')
				{
					$thumb = get_field('visuel_page_edition');
					if (!empty($thumb))
					{
						echo wp_get_attachment_image($thumb['id'], 'edition_banner', array('class' => 'edition_banner'));
					}
				}
				?>
				<?php the_content(); ?>
			
				<?php
				
				if($SectionsPage){ echo '</section>' ; } ?>
				
			<?php endif; ?>			
	
		</div>
		
		<?php endif; ?>		
	
	</div>

</div>

