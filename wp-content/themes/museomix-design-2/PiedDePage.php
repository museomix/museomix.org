<?php

function AfficheMenu($id){
	//$menus = get_nav_menu_locations();

	//$menu = $menus['Menu_principal'];
	$nav_menu = wp_get_nav_menu_object($id);
	$r = '<h3>'.$nav_menu->name.'</h3><ul class="unstyled">';
	$liste = wp_get_nav_menu_items($id);
	//$liste = wp_get_nav_menu_items($menu);
	foreach($liste as $elm){
		$cl = (EstMenuCourant($elm)) ? 'active' : '';
		$r .= '<li class="'.$cl.'"><a href="'.$elm->url.'" class="bouton-nav">'.$elm->title.'</a></li>';
	}
	echo $r.'</ul>';
}
?>

<footer class="foot">
	<div class="container">
		<div class="row">
			<div class="span3">
				<?php
					$mid = 15;
					echo AfficheMenu($mid);
				?>
			</div>
			<div class="span3">
				<?php
					$mid = 17;
					echo AfficheMenu($mid);
				?>
			</div>
			<div class="span3">
				<?php
					$mid = 12;
					echo AfficheMenu($mid);
				?>
			</div>
			<div class="span3">
				<?php
				$id_by_language = icl_object_id(862,'config',true,ICL_LANGUAGE_CODE);
				$bloc = htmlspecialchars_decode(strip_tags(get_field('infos_config',$id_by_language)));
				echo $bloc;
				?>
			</div>



		</div>

	</div>


</footer>
	<?php wp_footer(); ?>

	<?php VT(); ?>

</body>

</html>
