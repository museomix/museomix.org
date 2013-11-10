<?php 
	global $SectionsPage; 
	
	$ContenuPage = apply_filters('the_content',get_the_content()); 
	 
	InitGabaritPage(ICL_LANGUAGE_CODE);
	
	function InitGabaritPage($langage){
		global $post, $SectionsPage;
		if('museomix'==$post->post_type){
			if(ICL_LANGUAGE_CODE=="en") {
				$SectionsPage = array(
					'presentation'=> 'Presentation',
					'prototypes'=> 'Prototypes',
					'actualites'=> 'News',
					/*'participer'=> 'Participate',*/
					'partenaires'=> 'Partners',
					'equipe'=> 'Team',
					'galerie'=> 'Gallery'
				);

			} else {
				$SectionsPage = array(
					'presentation'=> 'Présentation',
					'prototypes'=> 'Prototypes',
					'actualites'=> 'Actualités',
					/*'participer'=> 'Participez',*/
					'partenaires'=> 'Partenaires',
					'equipe'=> 'Equipe',
					'galerie'=> 'Galerie'
				);
			}
		}

		if(('prototype')==$post->post_type){
			if($langage=="en") {
				$SectionsPage = array(
					'scenario'=> 'User case',
					'intentions'=> 'Goals',
					'materiel'=> 'Tools & techs',
					'experience'=> 'Things learned...',
					'faq'=> 'FAQ',
					'equipe'=> 'Team',
				);

			} else {
				$SectionsPage = array(
					'scenario'=> 'Scénario utilisateur',
					'intentions'=> 'Objectifs',
					'materiel'=> 'Outils & techniques',
					'experience'=> 'Retour d\'expérience',
					'faq'=> 'FAQ',
					'equipe'=> 'Equipe',
				);
			}
		}
	}
	 
?>

<?php if($SectionsPage) { ?>

<div class="span3 hidden-phone hidden-tablet sidebar-nav" style="float: left; min-height: 1px;" >
      
		<ul class="nav nav-list bs-docs-sidenav" data-spy="affix" data-offset-top="<?php echo  (is_front_page() ? 472 : 200); ?>">

		<?php foreach($SectionsPage as $id => $titre): ?>

			<?php $titre = preg_replace('/^\\d+\\.\\s*/','',$titre); ?>

			<li><a class="ln-nav-page" style="width: 190px;" href="#<?php echo $id ?>"><i class="icon-chevron-right"></i> <?php echo $titre; ?></a></li>

		<?php endforeach; 
		?>

        </ul>

      
</div>
<?php } else { ?>

<div class="span2 hidden-phone hidden-tablet sidebar-nav" style="float: left; min-height: 1px;">
</div>

<? } ?>
