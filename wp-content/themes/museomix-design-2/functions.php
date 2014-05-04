<?
add_action('init', 'my_custom_init');
function my_custom_init() {
	add_post_type_support( 'prototype', 'comments' );
}
add_action('after_setup_theme', 'my_theme_setup');
function my_theme_setup(){
    load_theme_textdomain('my_theme', get_template_directory());
}
/* initiation des menus personnalisés 
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

/* plugins ACF (inopérant)
   ======================= */
add_action('acf/register_fields', 'my_register_fields');
function my_register_fields()
{
	include_once('biblio/acf-field-date-time-picker/acf-date_time_picker.php');
	include_once('biblio/acf-location/acf-location.php');
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
		if (!empty($r))
			$r .= '</section>';
	}
	
	$id = preg_replace('/\\W/','-',$atts['titre']);
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

/* Liste des médias-sociaux associés à une page
   ====================== */
add_shortcode( 'social', 'DisplaySocialMedia' );
function DisplaySocialMedia() {
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
		'edition' => ''
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
			$r[] = '<a class="ln-bloc-lieu btn btn-large" style="background: #ffffff url('.$imag[0].') no-repeat center center;" href="'.get_permalink($id).'"><span class="titre-bloc-lieu" ><span class="tx-bloc-lieu">'.$titre.'</span></span></a>';
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

	if(!is_page()) return '';
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
				$tmpImage = array();
				$tmpImage[0] = $imag;
				$imag = $tmpImage;
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
	$type = $wp_query->query['post_type'];
    if (is_archive()&&in_array($type,$OrdreMenuTypesPages))
    {
        $wp_query->set( 'orderby', 'menu_order' );
        $wp_query->set( 'order', 'ASC' );
        return $wp_query;
    }
}

/* Nouvelle taille d'image */
add_image_size('vignette_prototype',400,400);
add_image_size('edition_thumbnail',200,200);
add_image_size('edition_banner',790);

/* types de pages à trier selon le menu
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
			$date = ($nbH ===0 ? 'à l\'instant' : __('il y a','museomix-design-2').' '.$nbH.' h');
		}else{
			$date = __('le','museomix-design-2').' '.date_i18n('d M',$temps);
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

/* Ajout support fonction "en-tête" de WordPress */
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
        } ?>