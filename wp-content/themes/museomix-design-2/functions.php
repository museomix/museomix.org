<?phpinclude_once('includes/taxonomies.php');
if (!isset($SectionsPage) || !is_array($SectionsPage))
	$SectionsPage = array();
if (!isset($ContenusSections) || !is_array($ContenusSections))
	$ContenusSections = array();

/* Cache array */
$page_details = array();
	
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

function change_excerpt_more( $more ) {
	return '';
}
add_filter('excerpt_more', 'change_excerpt_more');

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

/* types de pages ¡§¡è trier selon le menu
   ==================================== */
$OrdreMenuTypesPages = array('edition','page','museomix');


function DateBillet($temps){
		if(date('d m Y',$temps)==date('d m Y')){
			// if (isset($_GET['debug']))
				// echo "get_the_time=".date_i18n('U').'&temps='.$temps;
			$nbH = (int)floor((date_i18n('U')-$temps)/3600);
			if ($nbH===-1)
				$nbH = 0;
			$date = ($nbH ===0 ? '¡§¡è l\'instant' : __('since','museomix').' '.$nbH.' h');
		}else{
			$date = __('the','museomix').' '.date_i18n('d M',$temps);
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

/* Ajout support fonction "en-t¡§ote" de WordPress */
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
	global $post, $ContenusSections, $page_details;

	/* We use a global array to cache get_fields calls */
	if (!isset($page_details[$post->ID])) {
		$details = get_fields($post->ID);
		$page_details[$post->ID] = $details;
	} else {
		$details = $page_details[$post->ID];
	}
	
	if (isset($ContenusSections[$id]['txt']) && $ContenusSections[$id]['txt']) {
		return $ContenusSections[$id]['txt'];
	}
	$contenu = '';
	switch($id) {
		case 'presentation':
			if('presentation'==$id){
				$contexte = get_details('context', $details);
				if($contexte) {
					$contenu = '<blockquote>'.$contexte.'</blockquote>';
				}else{
					//$contenu = '<span style="margin-left: 25px;color: #999;">pas de pr¨¦sentation (champ: contexte)</span>';		
				}

				$contenu .= DescriptionMusee();

				$other_content = get_details('other_content', $details);
				if ($other_content) {
					$contenu .= '<div class="bloc-contenu"><section class="section-1">'.$other_content.'</section></div>';
				}
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
											<a class="small" href="'.get_permalink().'">'.__('Read more','museomix').'</a></div></li>';
								

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
							$contenu .= '<li class="elm-bloc-actualites"><a href="'.get_category_link($catId).'">'.__('All articles','museomix').'</a></li>';
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
			
			/* Local coordinators */
			$coords = get_details('coordinator_local', $details);
			if($coords){
				foreach($coords as $coord){ 
					$elm = '<tr><td><strong>'.$coord['prenom'].' '.$coord['nom_de_famille'].'</strong></td>';
					$elm .=  '<td>'.($coord['email'] ? '<a href="mailto:'.$coord['email'].'">'.$coord['email'].'</a>' : '').'</td></tr>';
					$elm .=  '<tr><td colspan="2">'.$coord['descriptif'].
						(!empty($coord['compte_twitter'])
							? ' (<a href=http://twitter.com/@'.$coord['compte_twitter'].'>@'.$coord['compte_twitter'].'</a>)'
							: ''
						).
					'</td></tr>';
					$liste[] = $elm;
				}
				$contenu .= '<div class="span6 rond-5" style="float: left; background: #fff; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">';
				$coordinator = __('Museum side', 'museomix');
				$contenu .= '<h4 style="color: #666; padding-bottom: 10px; ">'.$coordinator.'</h4>';
				$contenu .= '<table class="table table-striped">'.implode('',$liste).'</table>';
				$contenu .= '</div>'; 
			}
			$liste = null;
			
			/* Co-organizers */
			$coorgs = get_details('co-organisateurs', $details);
			if($coorgs){
				foreach($coorgs as $coorg){ 
					$elm = '<tr><td><strong>'.$coorg['prenom'].' '.$coorg['nom_de_famille'].'</strong></td>';
					$elm .=  '<td>'.($coorg['email'] ? '<a href="mailto:'.$coorg['email'].'">'.$coorg['email'].'</a>' : '').'</td></tr>';
					$elm .=  '<tr><td colspan="2">'.$coorg['descriptif'].
						(!empty($coorg['compte_twitter'])
							? ' (<a href=http://twitter.com/@'.$coorg['compte_twitter'].'>@'.$coorg['compte_twitter'].'</a>)'
							: ''
						).
					'</td></tr>';
					$liste[] = $elm;
				}		
				$contenu .= '<div class="span6 rond-5" style="float: left; background: #fff; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">';
				$organizer = __('Community side', 'museomix');
				$contenu .= '<h4 style="color: #666; padding-bottom: 10px; ">'.$organizer.'</h4>';
				$contenu .= '<table class="table table-striped">'.implode('',$liste).'</table>';
				$contenu .= '</div>'; 
			}
			$liste = null;
			if(!$coord && !$coorg){
				$contenu = '<span style="margin-left: 25px;color: #999;">pas d\¨¦quipe (champs: coordinateur local, co-organisateurs)</span>';		
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
			/* Community linked to this element */
			$linked_community = get_details('community', $details);
			if (!isset($linked_community[0])) {
				break;
			}
			$contenu .= '<div class="row-fluid">';
			$community = get_post($linked_community[0]);
			$linked_community = null;
			
			$community_details = get_fields($community->ID);
			
			$contenu .= '<p><strong>'.$community->post_title.'</strong></p>';
			
			/* Social networks retrieval */
			if (isset($community_details['social_networks'])) {
				$contenu .= '<ul>';
				foreach($community_details['social_networks'] as $network) {
					$contenu .= '<li>'.$network['network'].' : <a href="'.$network['url'].'">'.$network['url'].'</a></li>';
				}
				if (isset($community_details['website']) && !empty($community_details['website'])) {
					$contenu .= '<li>'.__('Website','museomix').' : <a href="'.$community_details['website'].'">'.$community_details['website'].'</a></li>';
				}
				$contenu .= '</ul>';				
			}
			$contenu .= apply_filters( 'the_content', $community->post_content);
			$contenu .= '</div>';
			break;
		case 'playground':
		
			/* playground linked to this element */
			$playgrounds = get_details('playground', $details);

			if (!isset($playgrounds[0])) {
				break;
			}
			$contenu .= '<div class="row-fluid">';
			$contenu .= '<dl>';
			foreach($playgrounds as $playground) {
				$contenu .= '<dt>'.$playground['title'].'</dt>';
				$contenu .= '<dd>'.$playground['description'].'</dd>';
			}			
			$contenu .= '</dl>';
			$contenu .= '</div>';
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
		if($photo=get_field('photo_equipe')){			if (is_int($photo)) {				$url = wp_get_attachment_image_src($photo, 'thumbnail')[0];			} else {				$url = $photo;			}
			$contenu .= '<img style="margin:30px;" src="'.$url.'">';
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
	$titles = array(
		'scenario' => __('User case', 'museomix'),
		'intentions' => __('Goals', 'museomix'),
		'materiel' => __('Tools & techs', 'museomix'),
		'experience' => __('Things learned...', 'museomix'),
		'faq' => __('FAQ', 'museomix'),
		'equipe' => __('Team', 'museomix'),
	);
	$titre .= '<h1>'.$titles[$id].'</h1>';
	
	if ($echo)
		echo $titre;
	else
		return $titre;
}


function TitreSection($id,$langage, $echo = true){
	global $post;
	$titre = '';
	$titles = array(
		'actualites' => __('News', 'museomix'),
		'presentation' => __('Presentation', 'museomix'),
		'partenaires' => __('Partners', 'museomix'),
		'prototypes' => __('Prototypes', 'museomix'),
		'equipe' => __('Team', 'museomix'),
		'galerie' => __('Team', 'museomix'),
	);
	if (!isset($titles[$id])) {
		return;
	}
	$titre .= '<h1>'.$titles[$id].'</h1>';
	
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

/* Custom function to enhance get_fields */
function get_details($field, $array) {
	if (isset($array[$field])) {
		return $array[$field];
	}
	return false;
}/* Source : https://pippinsplugins.com/retrieve-attachment-id-from-image-url/ */function get_attachment_id_from_url($image_url) {	global $wpdb;	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));     if (isset($attachment[0])) {		return $attachment[0];	}	return 0;}add_action('wp_login','wpdb_capture_user_last_login', 10, 2);
function wpdb_capture_user_last_login($user_login, $user){
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}