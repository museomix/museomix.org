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

	if('scenario'==$id){
		$titre = ($langage=="en") ? '<h1>User case</h1>' : '<h1>Scénario utilisateur</h1>';
	}
	elseif('intentions'==$id){
		$titre = ($langage=="en") ? '<h1>Goals</h1>' : '<h1>Objectifs</h1>';
	}
	elseif('materiel'==$id){
		$titre = ($langage=="en") ? '<h1>Tools & techs</h1>' : '<h1>Outils & techniques</h1>';
	}
	elseif('experience'==$id){
		$titre = ($langage=="en") ? '<h1>Things learned...</h1>' : '<h1>Retour d\'Expérience</h1>';
	}
	elseif('faq'==$id){
		$titre = ($langage=="en") ? '<h1>FAQ</h1>' : '<h1>FAQ</h1>';
	}
	elseif('equipe'==$id){
		$titre = ($langage=="en") ? '<h1>Team</h1>' : '<h1>Equipe</h1>';
	}
	


	echo $titre; 
}


function ContenuSection($id){
	global $post;


	if('scenario'==$id){
		if($zone=get_field('scenario')){

				$contenu .= $zone;
		}
	}
	elseif('intentions'==$id){
		if($zone=get_field('intentions')){
			$contenu .= $zone;
		}
	}
	elseif('materiel'==$id){
		if($zone=get_field('materiel')){
			$contenu .= $zone;
		}
	}
	elseif('experience'==$id){
		if($zone=get_field('experience')){
			$contenu .= $zone;
		}

	}
	elseif('faq'==$id){
		if($zone=get_field('faq')){
			$contenu .= $zone;
		}

	}
	elseif('equipe'==$id){
		if($photo=get_field('photo_equipe')){
			$contenu .= '<img style="margin:30px;" src="'.$photo.'">';
		}
		if($zone=get_field('descriptif_equipe')){
			$contenu .= '<p>'.$zone.'</p>';
		}
		if($equips=get_field('equipiers')){
			foreach($equips as $equip){ 
				$elm = '<td><strong>'.$equip['prenom'].' '.$equip['nom_de_famille'].'</strong></td>';
				$elm = '<td>'.$equip['mission'].'</td><td>';
				if(!empty($equip['compte_twitter'])) $elm .=  '<a href=http://twitter.com/@'.$equip['compte_twitter'].'>@'.$equip['compte_twitter'].'</a>';
				$elm .=  '</td><td>'.$equip['email'].'</td>';
				$elm = '<td>'.$equip['link'].'</td>';
				$liste[] = $elm;
			}		
			//$contenu .= '<div class="span7 rond-5" style="float: left; background: #fff; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">';
			//$contenu .= '<h4 style="color: #666; padding-bottom: 10px; ">Co-organisateurs</h4>';
			$contenu .= '<table class="table table-striped"><tr>'.implode('</tr><tr>',$liste).'</tr></table>';

			//$contenu .= '<ul class="lst-coorg"><li class="li-coorg">'.implode('</li><li class="li-coorg">',$liste).'</li></ul>';
			//$contenu .= '</div>'; 
		}
	}

	return $contenu; 
}

?>

<?php 

	global $ContenuPage, $SectionsPage;  
	if(!$ContenuPage){ $ContenuPage = apply_filters('the_content',get_the_content());} 
		
?>


<div class="bloc-page span9">

	<div class="contenu-page">
	
		<div class="bloc-contenu prototypes-page">
	<section class="section-1"  style="min-height:100px;"><?php 
			
					if($contenu=get_field('resume_du_projet')){

						if($photo=get_field('visuel_prototype')){
							echo '<img style="float:left;margin-right:20px;" class="logo-elm-part" src="'.$photo.'">';
						}
						echo '<blockquote>'.$contenu.'</blockquote>';
					}
						?>
					</section>
				<?php foreach($SectionsPage as $id => $titre):  ?>
	 				<?php if(!is_null(ContenuSection($id))): ?>
					<section class="section-1 liste-prototype-lieu" id="<?php echo $id; ?>" style="min-height: 300px; position: relative;"> 
						
						<?php #if('presentation'!=$id&&'participer'!=$id): ?>
						
						<div class="page-header">
						
							<?php TitreSection($id,ICL_LANGUAGE_CODE); ?>
						
						</div>
						
						<?php #endif; ?>
						
						<?php echo ContenuSection($id); ?>
	
					</section>
				 	<?php endif; ?>
				<?php endforeach; ?>

	<?php global $withcomments;
		$withcomments = 1;
		comments_template( ); ?>
	
		</div>
	
	</div>

</div>

