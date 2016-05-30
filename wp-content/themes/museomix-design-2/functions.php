<?php
if (!isset($SectionsPage) || !is_array($SectionsPage))
	$SectionsPage = array();
if (!isset($ContenusSections) || !is_array($ContenusSections))
	$ContenusSections = array();
	
	
add_action('init', 'my_custom_init');
function my_custom_init() {
	add_post_type_support( 'prototype', 'comments' );
}
add_action('after_setup_theme', 'my_theme_setup');
function my_theme_setup(){
    load_theme_textdomain('museomix', get_template_directory().'/lang');
}
/* initiation des menus personnalis¨¦s 
   ================================== */
register_nav_menus( array(
        'Menu_principal' => 'Navigation principale',
) );

/* configuration ordre selon type
   ============================== 
add_action( 'pre_get_posts', 'ConfigurerOrdreArchive'); 

function ConfigurerOrdreArchive($query){
	if(is_archive()):
	   $query->set( 'order', 'ASC' );
	   $query->set( 'orderby', 'title' );
	endif;    
};*/

add_action('pre_get_posts', 'OrdonnerListes');
function OrdonnerListes($query){
	if( is_admin() || ! $query->is_main_query() ) 
		return;
	if( is_home() ) {
        $query->set( 'posts_per_page', '100' );
    }
}

/* plugins ACF (inop¨¦rant)
   ======================= */
add_action('acf/register_fields', 'my_register_fields');
function my_register_fields()
{
	// include_once('biblio/acf-field-date-time-picker/acf-date_time_picker.php');
	// include_once('biblio/acf-location/acf-location.php');
}

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
	$r .= '<section class="section-1'.$cl.(isset($atts['first']) ? ' first ' : '').'" id="'.$id.'">';
	if(trim($atts['titre'])>''){
		$titre2 = preg_replace('/^(\\d+\\.)/',"<span class=\"num-compt\">$1</span>",$atts['titre']);
		$r .= '<div class="page-header"><h1 class="titre-section">'.$titre2.'</h1></div>';
		$SectionsPage[$id] = $atts['titre'];
	}
	return $r;
}

/* Liste des m¨¦dias-sociaux associ¨¦s ¨¤ une page
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
		'show_local_website' => ''
	),$atts));
	$lieux = get_pages(array('post_type'=>'museomix'));
	if(!count($lieux)){ return ''; }
	foreach($lieux as $lieu){
		$id = $lieu->ID;
		if(get_field('edition',$id)->post_title==$atts['edition']){
			$titre = str_replace(' &#8211; ','<br />',$lieu->post_title);
			if(!$imag=wp_get_attachment_image_src(get_field('vignette_lieu',$id),"thumbnail")) {
				$imag= wp_get_attachment_image_src(get_field('visuel_page',$id),"large");
			}
			$lien = get_permalink($id);
			$website = get_field('website', $id);
			if (!empty($website) && isset($atts['show_local_website']) && !empty($atts['show_local_website']))
				$lien = $website;
			$r[] = '<a class="ln-bloc-lieu btn btn-large" style="background: #ffffff url('.$imag[0].') no-repeat center center;" href="'.$lien.'"><span class="titre-bloc-lieu" ><span class="tx-bloc-lieu">'.$titre.'</span></span></a>';
		}
	}
	if(!$r) return '';
	return '<ul class="lst lst-bloc-lieux clearfix"><li class="elm-bloc-lieu">'.implode($r,'</li><li class="elm-bloc-lieu">').'</li></ul>';
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

function change_excerpt_more( $more ) {
	return '';
}
add_filter('excerpt_more', 'change_excerpt_more');

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
	}
	if(!$r) return '';
	return '<ul class="lst lst-bloc-lieux clearfix"><li class="elm-bloc-lieu">'.implode($r,'</li><li class="elm-bloc-lieu">').'</li></ul>';
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
/* tri des archives par ordre du menu
   ================================== */
add_filter('pre_get_posts', 'pre_get_posts_hook' );
function pre_get_posts_hook($wp_query) {
	global $OrdreMenuTypesPages;
	if (isset($wp_query->query['post_type'])) {
		$type = $wp_query->query['post_type'];
		if (is_archive()&&in_array($type,$OrdreMenuTypesPages))
		{
			$wp_query->set( 'orderby', 'menu_order' );
			$wp_query->set( 'order', 'ASC' );
			return $wp_query;
		}
	}
	return $wp_query;
}

/* Nouvelle taille d'image */
add_image_size('vignette_prototype',400,400);
add_image_size('edition_thumbnail',200,200);
add_image_size('edition_banner',790);
add_image_size('location_thumbnail',2000);

/* types de pages ¨¤ trier selon le menu
   ==================================== */
$OrdreMenuTypesPages = array('edition','page','museomix');

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

function DateBillet($temps){
		if(date('d m Y',$temps)==date('d m Y')){
			// if (isset($_GET['debug']))
				// echo "get_the_time=".date_i18n('U').'&temps='.$temps;
			$nbH = (int)floor((date_i18n('U')-$temps)/3600);
			if ($nbH===-1)
				$nbH = 0;
			$date = ($nbH ===0 ? '¨¤ l\'instant' : __('il y a','museomix').' '.$nbH.' h');
		}else{
			$date = __('le','museomix').' '.date_i18n('d M',$temps);
		}
		return $date;	
	}

function unslug($element){
	return str_replace('_', ' ', $element);
}


function limit_posts_per_archive_page() {
	if (!is_admin()) {
		if ( is_archive() )
			set_query_var('posts_per_archive_page', 10); // or use variable key: posts_per_page
		if ( is_category() )
			set_query_var('posts_per_page', 10); // or use variable key: posts_per_page
		if ( is_home() )
			set_query_var('posts_per_page', 10); // or use variable key: posts_per_page
	}
}
add_filter('pre_get_posts', 'limit_posts_per_archive_page');

function mix_pagination() {
	global $wp_query;

	$big = 999999999; // need an unlikely integer
	echo "<div id='pagination'>".paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $wp_query->max_num_pages
	) )."</div>";
}

/* Ajout support fonction "en-t¨ºte" de WordPress */
add_theme_support('custom-header');

/* Lister les commentaires */
function mytheme_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		extract($args, EXTR_SKIP);

		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
?>
		<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
		<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
		<?php endif; ?>
		<div class="comment-author vcard">
		<?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?> <span><?php printf( __('[%1$s]'), get_comment_date('d-m-y')) ?></span>: <?php edit_comment_link(__('(Edit)'),'  ','' );
			?>
		</div>
<?php if ($comment->comment_approved == '0') : ?>
		<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
		<br />
<?php endif; ?>
		<?php comment_text() ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
			<p>
			<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
			</p>
		</div>

		

		<div class="reply">

		</div>
		<?php if ( 'div' != $args['style'] ) : ?>
		</div>
		<?php endif; ?>
<?php
        }
function ContenuSection($id, $echo = true){
	global $post, $SectionsPage;
	if (isset($SectionsPage[$id]) && $SectionsPage[$id]) {
		return $SectionsPage[$id];
	}
	$contenu = '';
	switch($id) {
		case 'presentation':
			if('presentation'==$id){
				if($contenu=get_field('contexte')){
					$contenu = '<blockquote>'.$contenu.'</blockquote>';
				}else{
					//$contenu = '<span style="margin-left: 25px;color: #999;">pas de présentation (champ: contexte)</span>';		
				}

				$contenu .= DescriptionMusee();

				$other = get_field("other_content");
				if(!empty($other)) $contenu .= '<div class="bloc-contenu"><section class="section-1">'.$other.'</section></div>';


			}
			break;
		case 'actualites':
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
											<a class="small" href="'.get_permalink().'">'.__('En savoir plus','museomix').'</a></div></li>';
								

								$first = false;
							} else {
								$contenu .= '<li class="elm-bloc-actualites news-only-title"><a class="ln-bloc-actualites" href="'.get_permalink($post->ID).'">';
								$contenu .= '<span class="tx-bloc-actualites">'.get_the_title($post->ID).'</span>';
								$contenu .= '  <span class="date-actualites" style="font-size: 15px; color: #888; margin: 0; text-decoration: none !important; background: #eee">'.DateBillet(get_the_time('U')).'</span>';
								//$contenu .= '<br /><span class="extrait" style="color: #999; font-size: 15px; margin: 0; text-decoration: none !important">'.ExtraitBillet($post).'</span>';
								$contenu .= '</a></li>';
							}
						endwhile;
						if ((int)$newsNumber>0)
							$contenu .= '<li class="elm-bloc-actualites"><a href="'.get_category_link($catId).'">'.__('Tous les articles','museomix').'</a></li>';
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
				//$contenu = '<span style="margin-left: 25px;color: #999;">pas de compte Twitter (champ: compte Twitter)</span>';
			}		
			break;
		case 'prototypes':
			$contenu .= ListePrototypes(get_field("museomix"));
			break;
		case 'participer':
			$contenu .= BlocGetInvolved(ICL_LANGUAGE_CODE);
			break;
		case 'partenaires':
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
				$contenu = '<ul class="landscape"><li class="span6">'.implode('</li><li class="span6">',$liste).'</li></ul>';
			}else{
				//$contenu = '<span style="margin-left: 25px;color: #999;">pas de partenaires (champ: partenaires)</span>';		
			}


			break;
		case 'equipe':
			
			$contenu .= '<div class="row-fluid">';
			if($coord=get_field('coordinator_local')){
				foreach($coord as $coorg){
					if (empty($coorg['email']))
						$elm = '<td colspan="2"><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong>';
					else
						$elm = '<td><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong>';
					if(!empty($coorg['compte_twitter'])) $elm .=  '     <a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
					
					//.' <a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
					
						
					if (!empty($coorg['email']))
						$elm .=  '</td><td>'.$coorg['email'].'</td>';
					$elm .=  '<tr><td>'.$coorg['descriptif'].'</td></tr>';
					
					$principal[] = $elm;
				}	
				$contenu .= '<div class="span5 rond-5" style="float: left; background: #fff; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">';
				ICL_LANGUAGE_CODE == 'en' ? $coordinator = 'Local coordinators' : $coordinator = 'Coordinateurs locaux';
				$contenu .= '<h4 style="color: #666; padding-bottom: 10px; ">'.$coordinator.'</h4>';
				$contenu .= '<table class="table table-striped"><tr>'.implode('</tr><tr>',$principal).'</tr></table>';
				$contenu .= '</div>'; 
			}
			if($coorgs=get_field('co-organisateurs')){
				foreach($coorgs as $coorg){ 
					$elm = '<td><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong></td><td>';
					if(!empty($coorg['compte_twitter'])) $elm .=  '<a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
					$elm .=  '</td><td>'.$coorg['email'].'</td>';
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
				$contenu = '<span style="margin-left: 25px;color: #999;">pas d\équipe (champs: coordinateur local, co-organisateurs)</span>';		
			}else{
				$contenu .= '<div class="clear"></div>';
			}
			$contenu .='</div>';
			break;
		case 'galerie':
			

			$contenu .= '<div class="row-fluid">';
			$image_ids = get_field('galerie', false, false);
			 
			if(!empty($image_ids)) {
				$shortcode = '[gallery ids="' . implode(',', $image_ids) . '" link="file"]';
				$contenu .= do_shortcode( $shortcode );
			}
			$contenu .="</div>";

			break;
		case 'community':
			$contenu .= '<div class="row-fluid">';
			if($coord=get_field('coordinator_local')){
				foreach($coord as $coorg){
					if (empty($coorg['email']))
						$elm = '<td colspan="2"><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong>';
					else
						$elm = '<td><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong>';
					if(!empty($coorg['compte_twitter'])) $elm .=  '     <a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
					
					//.' <a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
					
						
					if (!empty($coorg['email']))
						$elm .=  '</td><td>'.$coorg['email'].'</td>';
					$elm .=  '<tr><td>'.$coorg['descriptif'].'</td></tr>';
					
					$principal[] = $elm;
				}	
				$contenu .= '<div class="span5 rond-5" style="float: left; background: #fff; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">';
				ICL_LANGUAGE_CODE == 'en' ? $coordinator = 'Local coordinators' : $coordinator = 'Coordinateurs locaux';
				$contenu .= '<h4 style="color: #666; padding-bottom: 10px; ">'.$coordinator.'</h4>';
				$contenu .= '<table class="table table-striped"><tr>'.implode('</tr><tr>',$principal).'</tr></table>';
				$contenu .= '</div>'; 
			}
			if($coorgs=get_field('co-organisateurs')){
				foreach($coorgs as $coorg){ 
					$elm = '<td><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong></td><td>';
					if(!empty($coorg['compte_twitter'])) $elm .=  '<a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>';
					$elm .=  '</td><td>'.$coorg['email'].'</td>';
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
				$contenu = '<span style="margin-left: 25px;color: #999;">pas d\équipe (champs: coordinateur local, co-organisateurs)</span>';		
			}else{
				$contenu .= '<div class="clear"></div>';
			}
			$contenu .='</div>';
			break;
	}
	
	if ($echo)
		echo $contenu;
	else
		return $contenu;
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
				$html .= '<div class=" museum">';
				$html .= '<div class="row-fluid"><div class="span7"><h2 class="museumTitre"><a href="'.$url.'">'.get_the_title($id).'</a></h2></div></div>';
				$html .= '<div class="row-fluid">';
				$imag = wp_get_attachment_image_src(get_field('image_musee',$id),"thumbnail");
				$html .= '<div class="span2"><a href="'.$url.'"><img src="'.$imag[0].'" width='.$imag[1].' height='.$imag[2].' class=""></a></div>';
				$html .= '<div class="span10">
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



function ExtraitBillet($thePost = null){
		global $post;
		if (empty($thePost))
			$thePost = $post;
		$max = 100;
		$texte = strip_tags($thePost->post_content);
		if(strlen($texte)>($max-10)){
			$texte = substr($texte,0,$max).'... ';
			#$texte .= '<br /><a style="white-space: nowrap;" href="'.get_permalink().'">lire la suite</a>';
		}
		return $texte;
	}

	function ContenuSectionProto($id){
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


function TitreSectionProto($id,$langage, $echo = true){
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
	


	if ($echo)
		echo $titre;
	else
		return $titre;
}


function TitreSection($id,$langage, $echo = true){
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
	
	if ($echo)
		echo $titre;
	else
		return $titre;
}


//We sort by title (based on year) the editions archive page
add_action( 'pre_get_posts', 'sorting_posts' );
function sorting_posts( $q ) {
   if( $q->is_main_query() && $q->get('post_type') == 'edition') {
      $q->set( 'orderby', 'title' );
      $q->set( 'order', 'DESC' );
   }
}
		?>