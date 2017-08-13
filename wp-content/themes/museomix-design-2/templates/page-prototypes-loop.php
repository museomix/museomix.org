<?php while ( have_posts() ) : the_post(); ?><div style="width:30%;border:1px solid Silver;display:inline-block;margin:0 1% 3px 0"><div style="margin:5px;display:inline-block;width:30%;">
			<?php
			$data = get_post_meta(get_the_ID(), 'visuel_prototype');
			$data2 = get_post_meta(get_the_ID(), 'photo_equipe');
			$url = null;

				if (isset($data[0]) && ((int)$data[0] > 0)) {
					$url = wp_get_attachment_image_src($data[0], 'thumbnail')[0];
				} else if (isset($data2[0]) && ((int)$data2[0] > 0)) {
					
					$url = wp_get_attachment_image_src($data2[0], 'thumbnail')[0];
				}
				if ($url) {
					?>
				<img src="<?php echo $url; ?>" style="max-width:75px;background:Silver;height:75px " />
			<?php } ?>
		</div><div style="display:inline-block;width:63%">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</div>
	</div><?php endwhile; ?>