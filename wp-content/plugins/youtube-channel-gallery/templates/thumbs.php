<div class="ytc-pslb ytc-thumbnails container-fluid ytc-thumb<?php echo $ytchag_thumb_ratio?> ytc-thumb-align-<?php echo $ytchag_thumbnail_alignment?>">

	<?php $i = 0; ?>
	<?php $col = 1; ?>
	<?php foreach ($thumbs as $thumb): ?>
	<?php  if ($i % $ytchag_thumb_columns_ld === 0): ?>
	<?php    if ($i > 0): ?>
		</div>
	<?php    endif; ?>

	<div class="ytc-row row">
<?php    $col += 1; ?>
<?php    $i = 0; ?>
<?php    endif; ?>
<?php $xs = $ytchag_thumb_columns_phones ? $ytchag_thumb_columns_phones : '1'; ?>
<?php $sm = $ytchag_thumb_columns_tablets ? $ytchag_thumb_columns_tablets : '2'; ?>
<?php $md = $ytchag_thumb_columns_md ? $ytchag_thumb_columns_md : '3'; ?>
<?php $lg = $ytchag_thumb_columns_ld ? $ytchag_thumb_columns_ld : '4'; ?>
<?php 
	$gutter = 7;
	if ($ytchag_thumbnail_alignment == 'left') {
		$padding = 'style="padding:0 '.$gutter.'px 0 '. ($ytchag_thumb_width + 2*$gutter) .'px!important;"';
	}
	if ($ytchag_thumbnail_alignment == 'right') {
		$padding = 'style="padding:0 '. ($ytchag_thumb_width + 2*$gutter) .'px 0 '.$gutter.'px!important;"';
	}
?>

		<div class="ytc-column col-xs-<?php echo 12/$xs?> col-sm-<?php echo 12/$sm?> col-md-<?php echo 12/$md?> col-lg-<?php echo 12/$lg?>" <?php echo $padding?>>
				<?php 
					foreach ($thumb->modules as $module) {
						if ($module === 'title' && $ytchag_title) {
							include 'title.php';
						}
						elseif ($module === 'desc' && $ytchag_description) {
							include 'desc.php';
						}
						elseif ($module !== 'title' && $module !== 'desc') {
							include $module . '.php';
						}
					}
				?>
		</div> <?php //end col- ?>

		<?php  $i += 1; ?>
		<?php endforeach; ?>

	</div> <?php //end row ?>

	<?php //Pagination ?>
	<?php if ($ytchag_pagination_show): ?>
		<div class="ytc-pagination row">
			<div class="col-xs-4 ytc-previous">
				<?php if ($ytchag_prev_token): ?>
				<a class="ytc-paginationlink ytc-previous" data-cid="<?php echo $ytchag_id ?>" data-wid="<?php echo $this->number ?>" data-playlist="<?php echo $ytchag_playlist?>" data-token="<?php echo $ytchag_prev_token?>"><?php _e( '«Previous', 'youtube-channel-gallery' );?></a>
				<?php endif; ?>
			</div>
			<div class="col-xs-4 ytc-numeration">
				<?php $total_pages = ceil( $ytchag_total_results / $ytchag_results_per_page );?>
					<span class="ytc-currentpage">1</span><span class="ytc-separator">/</span><span class="ytc-totalpages"><?php echo $total_pages ?></span>
			</div>
			<div class="col-xs-4 ytc-next">
				<?php if ($ytchag_next_token): ?>
				<a class="ytc-paginationlink ytc-next" data-cid="<?php echo $ytchag_id ?>" data-wid="<?php echo $this->number ?>" data-playlist="<?php echo $ytchag_playlist?>" data-token="<?php echo $ytchag_next_token?>"><?php _e( 'Next»', 'youtube-channel-gallery' );?></a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

</div> <?php  //end container ?>

