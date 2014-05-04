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


function DescriptionMusee() {
	global $post;
	$mus = get_field('museum');

	$html = '<section id="musees">';

	if($mus){
		foreach($mus as $nomChamp => $valeur ){	
			if(is_object($valeur)){
				$id = $valeur->ID;
				$url = get_field('lien',$id);
				$html .= '<div class="row museum">';
				$html .= '<div class="row"><div class="span7"><h2 class="museumTitre"><a href="'.$url.'">'.get_the_title($id).'</a></h2></div></div>';
				$html .= '<div class="row">';
				$imag = wp_get_attachment_image_src(get_field('image_musee',$id),"thumbnail");
				$html .= '<div class="span2"><a href="'.$url.'"><img src="'.$imag[0].'" width='.$imag[1].' height='.$imag[2].' class=""></a></div>';
				$html .= '<div class="span5">
							<table class="table">
							<tr><td><i class="icon-home"></i></td><td>'.get_field('court_descriptif_musee',$id).'</td></tr>
							<tr><td><i class="icon-map-marker"></i></td><td>'.get_field('adresse',$id).' <br />'.get_field('ville',$id).' '.get_field('pays',$id).'</td></tr>
							<tr><td><i class="icon-calendar"></i></td><td>'.get_field('horaires',$id).'</td></tr>
							</table>


							</div>';
				//echo '<a href="'.get_permalink($id).'">'.get_the_title($id).'</a>';
			}
			$html .= '</div></div>';
		}
	}

	$html .= '</div>';

	return $html;

}



function ExtraitBillet($thePost){
		
		$max = 100;
		$texte = strip_tags($thePost->post_content);
		if(strlen($texte)>($max-10)){
			$texte = substr($texte,0,$max).'... ';
			#$texte .= '<br /><a style="white-space: nowrap;" href="'.get_permalink().'">lire la suite</a>';
		}
		return $texte;
	}

function ContenuSection($id){
	global $post;
	if('presentation'==$id){
		if($contenu=get_field('contexte')){
			$contenu = '<blockquote>'.$contenu.'</blockquote>';
		}else{
			$contenu = '<span style="margin-left: 25px;color: #999;">pas de présentation (champ: contexte)</span>';		
		}

		$contenu .= DescriptionMusee();

		$other = get_field("other_content");
		if(!empty($other)) $contenu .= '<div class="bloc-contenu"><section class="section-1">'.$other.'</section></div>';


	}
	elseif('actualites'==$id){
		/* For news display on location pages */
		$cat = get_categories('type=post&hide_empty=1');
		$newsNumber = 0;
		foreach($cat as $c){
			if($catId = $c->term_id) {
				$lieu_id = substr(category_description($catId),3,2);
				$first = true;
				if($lieu_id == $post->ID) {
				
					$actu_query = new WP_Query('cat='.$catId.'&post_status=publish&posts_per_page=5&page=1');
  					$contenu .= '<div class="contenu_page"><ul style="font-size: 18px; list-style-type: none; padding: 0; margin: 0; ">';
					while ($actu_query->have_posts()) : $actu_query->the_post();
						$newsNumber++;
						setup_postdata($post);
						if($first) {
							$contenu .= '<li class="elm-bloc-actualites news-title-and-excerpt"><div class=""  style="max-width: 730px;"">
										<a class="ln-bloc-actualites" href="'.get_permalink($post->ID).'">
										<h3 class="titre-section">'.$post->post_title.'</h3></a>
										<span class="date-actualites" style="color: #888; margin: 0; text-decoration: none !important; background: #eee">le '.date_i18n('d M',strtotime($post->post_modified)).'</span>
										<br /><div class="">'.get_the_excerpt().'</div>
										<a class="small" href="'.get_permalink().'">'.__('En savoir plus','museomix-design-2').'</a></div></li>';
							

							$first = false;
						} else {
							$contenu .= '<li class="elm-bloc-actualites news-only-title"><a class="ln-bloc-actualites" href="'.get_permalink($post->ID).'">';
							$contenu .= '<span class="tx-bloc-actualites">'.get_the_title($post->ID).'</span>';
							$contenu .= '&nbsp; <span class="date-actualites" style="font-size: 15px; color: #888; margin: 0; text-decoration: none !important; background: #eee">'.DateBillet(get_the_time('U')).'</span>';
							//$contenu .= '<br /><span class="extrait" style="color: #999; font-size: 15px; margin: 0; text-decoration: none !important">'.ExtraitBillet($post).'</span>';
							$contenu .= '</a></li>';
						}
					endwhile;
					if ((int)$newsNumber>0)
						$contenu .= '<li class="elm-bloc-actualites"><a href="'.get_category_link($catId).'">'.__('Tous les articles','museomix-design-2').'</a></li>';
					$contenu .= '</ul></div>';
					wp_reset_postdata();
				}
			}
		}
		

		if($twitter=get_field('compte_twitter')){
			$contenu .= '<div class="bloc-flux bloc-flux-local flux-twitter" data-requete="@'.$twitter.'" style="min-height: 0; max-width: none; width: 99%; margin: 0 0 25px; border: 5px solid #eee;">'; 
			$contenu .= '<h3 class="titre-bloc-flux">';
			$contenu .= '@<a class="ln-titre-bloc-flux" target="ext" href="https://twitter.com/'.$twitter.'">'.$twitter.'</a>';
			$contenu .= '</h3>';
			$contenu .= '<div id="anim-charg-1" class="anim-charg"></div>';
			$contenu .= '</div>';
			$contenu .= '<div class="clear"></div>';
		}else{
			$contenu = '<span style="margin-left: 25px;color: #999;">pas de compte Twitter (champ: compte Twitter)</span>';
		}
	

	
	}
	elseif('prototypes'==$id){
		$contenu .= ListePrototypes(get_field("museomix"));
	}
	elseif('participer'==$id){
		$contenu .= BlocGetInvolved(ICL_LANGUAGE_CODE);
	}
	elseif('partenaires'==$id){
		if($partenaires=get_field('sponsor')){
			$liste = array();

			foreach($partenaires as $partner){

		$ids = icl_object_id($partner->ID,'sponsor',true);



		$logo = get_field('logo', $ids);
		$titre = get_the_title($ids);
		if(get_field('texte_de_lien', $ids)):
		$texte_lien = get_field('texte_de_lien', $ids);
		else:
		$texte_lien = "Visiter le site";
		endif;
		$lien = get_field('lien', $ids);
		$description = get_field('description_de_partenaire', $ids);

			$list = get_field('partenariat', $ids);
			if (is_array($list)):
				$part = "<p class='type'>".implode(', ', array_map("unslug",$list))."</p>";
			else:
				$part = "<p class='type'>".$list."</p>";
			endif;

		$elm = '<div class="partenaire-image-container">'.wp_get_attachment_image($logo,"large").'</div><div class="partenaire-content"><h3>'.$titre.'</h3>'.$part.'<p>'.$description.'</p><a href="'.$lien.'" target="blank">'.$texte_lien.'</a></div>';
array_push($liste,$elm);

			}
			$contenu = '<ul class="landscape"><li>'.implode('</li><li>',$liste).'</li></ul>';
		}else{
			$contenu = '<span style="margin-left: 25px;color: #999;">pas de partenaires (champ: partenaires)</span>';		
		}


	}
	elseif('equipe'==$id){
		$contenu .= '<div class="row-fluid">';
		if($coord=get_field('coordinator_local')){
			foreach($coord as $coorg){ 
				$elm = '<td><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong>';
				if(!empty($coorg['compte_twitter'])) $elm .=  ' &nbsp; &nbsp; <a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
				
				//.' <a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
				$elm .=  '<tr><td>'.$coorg['descriptif'].'</td></tr>';
				
				$principal[] = $elm;
			}	
			$contenu .= '<div class="span5 rond-5" style="float: left; background: #fff; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">';
			ICL_LANGUAGE_CODE == 'en' ? $coordinator = 'Local coordinator' : $coordinator = 'Coordinateur local';
			$contenu .= '<h4 style="color: #666; padding-bottom: 10px; ">'.$coordinator.'</h4>';
			$contenu .= '<table class="table table-striped"><tr>'.$principal[0].'</tr></table>';
			$contenu .= '</div>'; 
		}
		if($coorgs=get_field('co-organisateurs')){
			foreach($coorgs as $coorg){ 
				$elm = '<td><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong></td><td>';
				if(!empty($coorg['compte_twitter'])) $elm .=  '<a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
				$elm .=  '</td><td>'.$coorg['descriptif'].'</td>';
				$liste[] = $elm;

			}		
			$contenu .= '<div class="span7 rond-5" style="float: left; background: #fff; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">';
			ICL_LANGUAGE_CODE == 'en' ? $organizer = 'Co-organizers' : $organizer = 'Co-organisateurs';
			$contenu .= '<h4 style="color: #666; padding-bottom: 10px; ">'.$organizer.'</h4>';
			$contenu .= '<table class="table table-striped"><tr>'.implode('</tr><tr>',$liste).'</tr></table>';

			//$contenu .= '<ul class="lst-coorg"><li class="li-coorg">'.implode('</li><li class="li-coorg">',$liste).'</li></ul>';
			$contenu .= '</div>'; 
		}
		if(!$coord&&!$coorg){
			$contenu = '<span style="margin-left: 25px;color: #999;">pas d\'équipe (champs: coordinateur local, co-organisateurs)</span>';		
		}else{
			$contenu .= '<div class="clear"></div>';
		}
		$contenu .='</div>';
	} 
	elseif('galerie'==$id){

		$contenu .= '<div class="row-fluid">';
		$image_ids = get_field('galerie', false, false);
		 
		if(!empty($image_ids)) {
			$shortcode = '[gallery ids="' . implode(',', $image_ids) . '" link="file"]';
			$contenu .= do_shortcode( $shortcode );
		}
		$contenu .="</div>";

	}
	echo $contenu; 
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
				?>
				
					<section class="section-1 sec1" id="<?php echo $id; ?>" style="min-height: 300px; position: relative;"> 
						
						<?php #if('presentation'!=$id&&'participer'!=$id): ?>
						
						<div class="page-header">
						
							<?php TitreSection($id,ICL_LANGUAGE_CODE); ?>
						
						</div>
						
						<?php #endif; ?>
						
						<?php ContenuSection($id); ?>
	
					</section>
				 
				<?php } ; ?>
	 
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

