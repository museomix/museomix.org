<?php
$ContenuPage = apply_filters('the_content',get_the_content()); 

global $SectionsPage, $ContenusSections;

	InitGabaritPage(ICL_LANGUAGE_CODE);
	
	function InitGabaritPage($langage){
		global $post, $SectionsPage;
		if('museomix'==$post->post_type){
			if(ICL_LANGUAGE_CODE=="en") {
				$menu_default_items = array(
					'presentation'=> 'Presentation',
					'prototypes'=> 'Prototypes',
					'actualites'=> 'News',
					/*'participer'=> 'Participate',*/
					'partenaires'=> 'Partners',
					'equipe'=> 'Team',
					'galerie'=> 'Gallery'
				);


			} else {
				$menu_default_items = array(
					'presentation'=> 'Présentation',
					'prototypes'=> 'Prototypes',
					'actualites'=> 'Actualités',
					/*'participer'=> 'Participez',*/
					'partenaires'=> 'Partenaires',
					'equipe'=> 'Equipe',
					'galerie'=> 'Galerie'
				);
			}
			foreach($menu_default_items as $t => $v):
				$length = mb_strlen(strip_tags(ContenuSection($t, false)));
				if ($length > 0):
					$SectionsPage[$t] = $v;
				endif;
			endforeach;
		}

		if(('prototype')==$post->post_type){
			if($langage=="en") {
				$menu_default_items = array(
					'scenario'=> 'User case',
					'intentions'=> 'Goals',
					'materiel'=> 'Tools & techs',
					'experience'=> 'Things learned...',
					'faq'=> 'FAQ',
					'equipe'=> 'Team',
				);
				foreach($menu_default_items as $t => $v):
					if ($t === "equipe"):
						if (get_field("descriptif_equipe", $post->ID) || get_field("photo_equipe", $post->ID)):
							$SectionsPage[$t] = $v;
						endif;
					elseif (get_field($t, $post->ID) !== '' && get_field($t, $post->ID) !== false):
						$SectionsPage[$t] = $v;
					endif;
				endforeach;
			} else {
				$menu_default_items = array(
					'scenario'=> 'Scénario utilisateur',
					'intentions'=> 'Objectifs',
					'materiel'=> 'Outils & techniques',
					'experience'=> 'Retour d\'expérience',
					'faq'=> 'FAQ',
					'equipe'=> 'Equipe',
				);
				foreach($menu_default_items as $t => $v):
					if ($t === "equipe"):
						
						if (get_field("descriptif_equipe", $post->ID) || get_field("photo_equipe", $post->ID)):
							$SectionsPage[$t] = $v;
						endif;
					elseif (get_field($t, $post->ID)):
						$SectionsPage[$t] = $v;
					endif;
				endforeach;
			}
		}
	}
	 
?>

<?php
if (!is_front_page())
{
	if($SectionsPage) { ?>
	<div class="span3 hidden-phone hidden-tablet sidebar-nav" style="float: left; min-height: 1px;" >
		<ul class="nav nav-list bs-docs-sidenav" data-spy="affix" data-offset-top="<?php echo  (is_front_page() ? 472 : 200); ?>">
		<?php foreach($SectionsPage as $id => $titre): ?>

			<?php $titre = preg_replace('/^\\d+\\.\\s*/','',$titre);
			
			if ('prototype' == $post->post_type)
			{
				$tmpContent = ContenuSectionProto($id);
				$ContenusSections[$id]['txt'] = $tmpContent;
				$ContenusSections[$id]['title'] = strip_tags(trim(TitreSectionProto($id,ICL_LANGUAGE_CODE, false)));
				if (empty($ContenusSections[$id]['title']))
					$ContenusSections[$id]['title'] = $titre;
				$menuItemTest = mb_strlen($tmpContent);
			}
			elseif(in_array($post->post_type, array('page', 'edition')))
				$menuItemTest = 1;
			else
			{
				$tmpContent = ContenuSection($id, false);
				$ContenusSections[$id]['txt'] = $tmpContent;
				$ContenusSections[$id]['title'] = strip_tags(trim(TitreSection($id,ICL_LANGUAGE_CODE, false)));
				if (empty($ContenusSections[$id]['title']))
					$ContenusSections[$id]['title'] = $titre;
				$menuItemTest = mb_strlen($tmpContent);
			}
			if ($menuItemTest>0){
			?>
				<li><a class="ln-nav-page" style="width: 190px;" href="#<?php echo $id ?>"><i class="icon-chevron-right"></i> <?php echo $titre; ?></a></li>
			<?php
			}
		endforeach; 
		?>
		</ul>      
	</div>
	<?php
	}
}
?>
