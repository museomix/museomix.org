<?php while ( have_posts() ) : the_post(); ?>
	<div style="width:45%;border:1px solid Silver;display:inline-block">
		<div style="margin:5px;display:inline-block;width:45%;">
			<?php if (get_field('visuel_prototype')) {
				if (is_int(get_field('visuel_prototype'))) {
					$url = wp_get_attachment_image_src(get_field('visuel_prototype'), 'thumbnail');
				} else {
					$url = get_field('visuel_prototype');
				}
					?>
				<img src="<?php echo $url; ?>" style="max-width:100px;background:Silver;height:75px " />
			<?php } ?>
		</div>
		<div style="display:inline-block;width:45%">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</div>
	</div>
<?php endwhile; ?>