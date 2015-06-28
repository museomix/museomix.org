<?php
get_header();
get_template_part('Menu');
get_template_part('TitrePage');

$locations = get_posts(array(
	'post_type' => 'museomix',
	'meta_query' => array(
		array(
			'key' => 'community', // name of custom field
			'value' => '"' . get_the_ID() . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
			'compare' => 'LIKE'
		)
	)
));
$social_networks = get_field('social_networks');

?>
<div class="container">
	<div class="row-fluid">
		<div class="span3 hidden-phone hidden-tablet sidebar-nav bloc-page">
			<?php
			if (!empty($social_networks)) : ?>
				<h4><?php _e('Find them on:', 'museomix'); ?></h4>
				<ul>
					<?php foreach($social_networks as $social_network) {
						?>
						<li><a target="_blank" href="<?php echo $social_network['url']; ?>"><?php echo $social_network['network']; ?></a></li>
					<?php } ?>
				</ul>
			<?php endif; ?>
			<?php
			if (!empty($locations)) : ?>
				<br />
				<h4><?php _e('Locations:', 'museomix'); ?></h4>
				<ul>
					<?php foreach($locations as $location) {
						$edition = get_field('edition', $location->ID);
						$museums = get_field('museum', $location->ID);
						$museum = $museums[0];
						?>
						<li><a href="<?php echo get_permalink($museum->ID); ?>"><?php echo  $museum->post_title.'</a> (<a href="'.get_permalink($location->ID).'">'.$location->post_title.'</a>'; ?> - <?php echo $edition->post_title; ?>)</li>
					<?php } ?>
				</ul>
			<?php endif; ?>
		</div>
		<div class="bloc-page span9 centered">
			<div class="contenu-page">
				<div class="bloc-contenu" style="">
					<?php the_content() ;?>
				</div>
			</div>
		</div>	
	</div>
</div>
<?php get_template_part('PiedDePage'); ?>