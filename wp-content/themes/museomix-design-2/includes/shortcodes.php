<?php
/* bloc 'get involved'
   =================== */

add_shortcode( 'getinvolved', 'BlocGetInvolved' );
function BlocGetInvolved($langage){
	$bloc = ($langage=="en") ? get_field( 'infos_config',111 ) : $bloc = get_field( 'infos_config',718 );
	
	$bloc = htmlspecialchars_decode(strip_tags($bloc));
	return $bloc; 
}

/* Raccourci 'section'
   =================== */
add_shortcode( 'section', 'SectionDePage' );
function SectionDePage($atts){
	global $SectionsPage;

	extract(shortcode_atts(array(
		'titre' => '',
		'type' => '',
		'first' => ''
	),$atts));
	if(!isset($atts['first'])){
		// Seulement si on est pas ¨¤ la premi¨¨re section
		if (!empty($SectionsPage))
			$r .= '</section>';			
	}
	
	$id = strtolower(preg_replace('/\\W/','-',$atts['titre']));
	$cl = '';
	if(trim($atts['titre'])=='') $cl = ' section-2';
	elseif('sanstitre'==$atts['type']) $cl = ' section-3';
	$r .= '<section class="row-fluid section-1'.$cl.(isset($atts['first']) ? ' first ' : '').'" id="'.$id.'">';
	if(trim($atts['titre'])>''){
		$titre2 = preg_replace('/^(\\d+\\.)/',"<span class=\"num-compt\">$1</span>",$atts['titre']);
		$r .= '<div class="page-header"><h1 class="titre-section">'.$titre2.'</h1></div>';
		$SectionsPage[$id] = $atts['titre'];
	}
	return $r;
}

/* Liste des médias-sociaux associés ¨¤ une page
   ====================== */
add_shortcode( 'social', 'DisplaySocialMedia' );
function DisplaySocialMedia() {
	$r = '';
	$rows = get_field('medias_sociaux');
	//if(!$rows) $rows = get_field('medias_sociaux',782);
	if(!$rows) return '';
	//$lang = (get_field('langage') == "fr") ? "Suivez-nous !" : "Follow us !";
	//$r = ''.$lang.' ';

	foreach ($rows as $row) {
		$r .= '<a title="'.$row["link_title"].'" href="'.$row["url"].'""><img class="social_bouton" src="'.get_template_directory_uri().'/icons/icon-'.$row["reseau"].'.png" width="24" height="24"></a> ';
	}
	$r.= '';
	
	return $r;

}

/* Raccourci 'liste lieux'
   ====================== */
add_shortcode( 'liste-lieux', 'ListeLieux' );
function ListeLieux($atts){
	global $SectionsPage;
	extract(shortcode_atts(array(
		'edition' => '',
		'show_local_website' => '',
		'elements_number' => '4'
	), $atts));
	$lieux = get_pages(array('post_type'=>'museomix', 'order' => 'asc', 'orderby' => 'title'));
	if(!count($lieux)){ return ''; }
	$i = 0;
	$out = '';
	$out .= '<div class="lst-bloc-lieux ">';
	foreach($lieux as $lieu){
		$id = $lieu->ID;
		if(get_field('edition',$id)->post_title==$atts['edition']){
		if(!$imag=wp_get_attachment_image_src(get_field('vignette_lieu',$id),"thumbnail")) {
			$imag= wp_get_attachment_image_src(get_field('visuel_page',$id),"large");
		}
		$imag = $imag[0];
		if (($i % $elements_number == 0) || ($i == 0))
			$out .= '<div class="row-fluid">';
		
		$out .= '<div class="span'.(12/$elements_number).'" data-i="'.$i.'"><a class="ln-bloc-lieu btn btn-large" style="background-image: url('.$imag.')" href="'.get_the_permalink($lieu->ID).'"><span class="titre-bloc-lieu"><span class="tx-bloc-lieu">'.get_the_title($lieu->ID).'</span></span></a></div>';
		
		$i++;
		if (($i % $elements_number == 0) && ($i > 0) || (sizeof($lieux) == $i))
			$out .= '</div>';
		}
	}
	$out .= '</div>';
	if(!$out) return '';	
	return $out;
}


/* Flux live
   ====================== */
add_shortcode( 'live', 'ListeLive' );
function ListeLive($atts){

	/* */
	global $SectionsPage;
	if($SectionsPage){
		$t = '</section>';
	}else{
		$t = '';
	}
	extract(shortcode_atts(array(
		'category' => ''
	),$atts));
	if(!empty($atts['category'])) {
		$cat = $atts['category'];
	} else $cat = "global";

	//if(!is_page()) return '';
	$lives = get_posts(array('category_name'=>$cat,'posts_per_page'=>3));

	foreach($lives as $post): setup_postdata( $post );
		$id = preg_replace('/\\W/','-',$post->post_title);
		$SectionsPage[$id] = $post->post_title;
		$content = get_the_excerpt($post->ID);
		ICL_LANGUAGE_CODE === "en" ? $text = "Read more" : $text = "Lire la suite";
		$r[] = '<div ">
					<a class="ln-bloc-actualites" id="'.$id.'" href="'.get_permalink($post->ID).'" style="padding-top:50px;">
					<h2 class="titre-section">'.$post->post_title.'</h2></a>
					<span class="date-actualites" style="color: #888; margin: 0; text-decoration: none !important; background: #eee">le '.date_i18n('d M',strtotime($post->post_modified)).'</span>
					<br /><div class="">'.apply_filters('the_content',$content).' <a href="'.get_permalink($post->ID).'">'.$text.'</a></div>
					
				</div>';
	wp_reset_postdata();
	endforeach;

	if(!$r) return '';
	return ''.$t.'<section><div class="elm-bloc-actualites">'.implode($r,'</div><div class="elm-bloc-actualites">').'</div></section>';

}


/* Raccourci 'liste prototypes'
   ====================== */
add_shortcode( 'protos', 'ListePrototypes' );
function ListePrototypes($atts){
	$r = '';
	global $post;
	extract(shortcode_atts(array(
		'lieu' => ''
	),$atts));
	if(empty($atts['lieu'])) {
		$musId = $post->ID;
	} else {
		$musId = intval($atts['lieu']);
	}
	$protos = get_posts(array('post_type'=>'prototype','posts_per_page' => -1));
	$i = 1;
	foreach($protos as $proto){
		$id = $proto->ID;
	
		if(get_field('museomix',$id)->ID==$musId || $musId=="all" || (is_null(icl_object_id($id,'any',false,'en')) && get_field('museomix',$id)->ID == icl_object_id($atts['lieu'],'museomix',true)) || (is_null(icl_object_id($id,'any',false,'fr')) && get_field('museomix',$id)->ID==icl_object_id($atts['lieu'],'museomix',true)) ) {
			$titre = str_replace(' &#8211; ','<br />',$proto->post_title);
			$imag=get_field('visuel_prototype',$id);
			if(empty($imag)) {
				$imag= wp_get_attachment_image_src(get_field('visuel_page',$id),"thumbnail");
			} else {
				$imag = wp_get_attachment_image_src($imag,"thumbnail");
				
			}
			if (empty($imag[0]))
				$imag[0] = get_bloginfo('template_directory').'/images/logo_museomix_prototype.png';
			$r[] = '<a class="ln-bloc-lieu btn btn-large" style="background: #ffffff url('.$imag[0].') no-repeat center center;" href="'.get_permalink($id).'"><span class="titre-bloc-lieu" ><span class="tx-bloc-lieu">'.$titre.'</span></span></a>';

		}
		$i++;
	}
	if(!$r) return '';
	
	$out = '';
	
	/* We cut prototypes in groups of 3 elements */
	$prototypes_groups = array_chunk($r, 3);
	foreach($prototypes_groups as $group) {
		$out .= '<ul class="lst lst-bloc-lieux clearfix">';
		foreach($group as $prototype) {
			$out .= '<li class="elm-bloc-lieu span4 ">'.$prototype.'</li>';
		}
		$out .= '</ul>';
	}
	return $out;
}





/* Raccourci 'formulaire Google'
   ============================ */
add_shortcode( 'FORMULAIRE-GOOGLE', 'FormulaireGoogle' );
function FormulaireGoogle($atts){
	global $SectionsPage;
	extract(shortcode_atts(array(
		'url' => ''
	),$atts));

	$b = '<div class="bloc-flux bloc-googleform" data-requete="'.$atts['url'].'"><div id="anim-charg-1" class="anim-charg"></div></div>';
	$m = 
	'<div class="modal hide fade">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin: 10px 17px 0 0">&times;</button>
	<div class="modal-header"><h3>Candidature Muséomix 2013</h3>
	<div class="anim-charg" id="anim-charg-modal" style="display: block; position: absolute; width: 25px; height: 25px; left: 18px; margin-top: 5px;"></div>
	</div>
	<div class="modal-body"></div>
	<div class="modal-footer">
	</div>
	</div>';
	return $b.$m;
}


/* Raccourci 'formulaire Google'
   ============================ */
add_shortcode( 'TABLEUR-GOOGLE', 'TableurGoogle' );
function TableurGoogle($atts){
	global $SectionsPage;
	extract(shortcode_atts(array(
		'url' => ''
	),$atts));

	$b = '<div class="bloc-flux bloc-tableurgoogle" data-requete="'.$atts['url'].'"><div id="anim-charg-1" class="anim-charg"></div></div>';

	return $b;
}


/* Shortcode partenaires liste
   ==================================== */
add_shortcode( 'partners-list', 'ListePartners' );
function ListePartners($atts){
	extract(shortcode_atts(array(
		'level' => false,
		'type' => false
	),$atts));

if($atts['type'] === 'sponsor'):
$args = array(
		'post_type' => 'sponsor',
		'numberposts' => -1,
		'meta_query' => array('relation' => 'AND',
			array(
				'key' => 'partenariat',
				'value' => 'sponsor',
				'compare' => 'LIKE'
			),array(
				'key' => 'niveau_partenariat',
				'value' => $atts['level'],
				'compare' => 'LIKE'
			))
	);
elseif($atts['type'] === 'all'):
$args = array(
		'post_type' => 'sponsor',
		'numberposts' => -1,
		'meta_query' => array(array(
				'key' => 'niveau_partenariat',
				'value' => $atts['level'],
				'compare' => 'LIKE'
			))
	);
else:
$args = array(
		'post_type' => 'sponsor',
		'numberposts' => -1,
		'meta_query' => array('relation' => 'AND',
			array(
				'key' => 'partenariat',
				'value' => 'sponsor',
				'compare' => 'NOT LIKE'
			),array(
				'key' => 'niveau_partenariat',
				'value' => $atts['level'],
				'compare' => 'LIKE'
			))
	);
endif;

if($atts['level'] === "global"):
	$format = "portrait";
else:
	$format = "landscape";
endif;

	$the_query = get_posts($args);
	$do_not_duplicate = array();
	$result = '';
	foreach($the_query as $partner):
		
		$id = icl_object_id($partner->ID,'sponsor',true);
		if (in_array($id,$do_not_duplicate,true)){

		}else{
		$logo = get_field('logo', $id);
		$titre = get_the_title($id);
		if(get_field('texte_de_lien', $id)):
		$texte_lien = get_field('texte_de_lien', $id);
		else:
		$texte_lien = "Visiter le site";
		endif;
		$lien = get_field('lien', $id);
		$description = get_field('description_de_partenaire', $id);
		if($atts['level'] === 'local'):
			$list = get_field('partenariat', $id);
			if (is_array($list)):
				$part = "<p class='type'>".implode(', ', array_map("unslug",$list))."</p>";
			else:
				$part = "<p class='type'>".$list."</p>";
			endif;
			
		else:
			$part = "";
		endif;
		$result .= '<li><div class="partenaire-image-container">'.wp_get_attachment_image($logo,"large").'</div><div class="partenaire-content"><h3>'.$titre.'</h3>'.$part.'<p>'.$description.'</p><a href="'.$lien.'" target="blank">'.$texte_lien.'</a></div></li>';
		array_push($do_not_duplicate,$id);
	}	
	endforeach;
	
	$count = count($do_not_duplicate);
	if( $count % 2 !== 0):
		$format .= " odd-element";
	endif;
	$result .= '</ul>';
	$result = '<ul class="'.$format.'">' . $result;
	return $result;

}


/* Raccourci 'liste lieux'
   ====================== */
add_shortcode( 'communities', 'shortcodeCommunities' );
function shortcodeCommunities($atts){
	extract(shortcode_atts(array(
		'including' => '',
		'excluding' => '',
		'elements_number' => 4
	), $atts));
	
	$communities = get_posts(array('post_type'=>'community', 'order' => 'asc', 'orderby' => 'title',
		'posts_per_page' => -1));

	if(!count($communities)){
		return '';
	}
	$i = 0;
	$out = '';
	$out .= '<div class="lst-bloc-lieux ">';
	foreach($communities as $community){
		$id = $community->ID;
		if ($imag=wp_get_attachment_image_src(get_field('community_image',$id)['ID'],'thumbnail')) {
			$imag = $imag[0];
		} else {
			$imag = '';
		}
		if (($i % $elements_number == 0) || ($i == 0))
			$out .= '<div class="row-fluid">';
		
		$out .= '<div class="span'.(12/$elements_number).'" data-i="'.$i.'"><a class="ln-bloc-lieu btn btn-large community_block" style="background-image: url('.$imag.')" href="'.get_the_permalink($community->ID).'"><span class="titre-bloc-lieu"><span class="tx-bloc-lieu">'.get_the_title($community->ID).'</span></span></a></div>';
		
		$i++;
		if (($i % $elements_number == 0) && ($i > 0) || (sizeof($communities) == $i))
			$out .= '</div>';
		
	}
	$out .= '</div>';
	if(!$out) return '';	
	return $out;
}