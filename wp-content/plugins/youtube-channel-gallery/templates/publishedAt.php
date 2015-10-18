<time datetime="<?php echo $thumb->publishedAt; ?>" class="ytc<?php echo isset($playercontent)? $playercontent : '';?>publishedAt">
	<?php echo date_i18n( get_option( 'date_format' ), strtotime( $thumb->publishedAt ) ); ?>
</time>