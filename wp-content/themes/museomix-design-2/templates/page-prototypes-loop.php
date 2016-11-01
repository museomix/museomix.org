<?php while ( have_posts() ) : the_post(); ?><div style="width:30%;border:1px solid Silver;display:inline-block;margin:0 1% 3px 0"><div style="margin:5px;display:inline-block;width:30%;">
			<?php
			$data = get_post_meta(get_the_ID(), 'visuel_prototype');

			if ($data[0]) {
				if ( (int)$data[0] > 0) {
					$url = wp_get_attachment_image_src($data[0], 'thumbnail')[0];
				}
					?>
				<img src="<?php echo $url; ?>" style="max-width:75px;background:Silver;height:75px " />
			<?php } ?>
		</div><div style="display:inline-block;width:63%">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</div>
	</div><?php endwhile; ?>