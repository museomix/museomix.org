<div class="ytc-pslb ytc-thumbnails container-fluid ytc-thumb<?php echo $ytchag_thumb_ratio?> ytc-thumb-align-<?php echo $ytchag_thumbnail_alignment?>">

<?php $multiplos = multiplos(array($ytchag_thumb_columns_phones, $ytchag_thumb_columns_tablets, $ytchag_thumb_columns_md, $ytchag_thumb_columns_ld)); ?>
<?php if (!$multiplos): ?>
	<div class="ytc-row row">
<?php endif; ?>

	<?php $i = 0; ?>
	<?php $col = 1; ?>
    <?php
    	$columns = max($ytchag_thumb_columns_phones, $ytchag_thumb_columns_tablets, $ytchag_thumb_columns_md, $ytchag_thumb_columns_ld);
    	$columns == 0 ? 1 : $columns;
    ?>
	<?php foreach ($thumbs as $thumb): ?>

    <?php if ($multiplos): ?>
	    <?php if ( $columns!=0 & $i % $columns === 0 ): ?>
		<?php    if ($i > 0): ?>
			</div>
		<?php    endif; ?>
		<div class="ytc-row row">
		<?php    $col += 1; ?>
		<?php    $i = 0; ?>
		<?php    endif; ?>
	<?php    endif; ?>

<?php
	$xs = $ytchag_thumb_columns_phones ? $ytchag_thumb_columns_phones : '2';
	$sm = $ytchag_thumb_columns_tablets ? $ytchag_thumb_columns_tablets : $xs;
	$md = $ytchag_thumb_columns_md ? $ytchag_thumb_columns_md : $sm;
	$lg = $ytchag_thumb_columns_ld ? $ytchag_thumb_columns_ld : $md;
	$col_xs = $xs ? 'col-xs-' . format_dec((12 / $xs)) : '';
	$col_sm = $sm ? 'col-sm-' . format_dec((12 / $sm)) : '';
	$col_md = $md ? 'col-md-' . format_dec((12 / $md)) : '';
	$col_lg = $lg ? 'col-lg-' . format_dec((12 / $lg)) : '';
?>
		<div class="ytc-column <?php echo $col_xs?> <?php echo $col_sm?> <?php echo $col_md?> <?php echo $col_lg?>">
				<?php
					if ($ytchag_thumbnail_alignment !== 'left' && $ytchag_thumbnail_alignment !== 'right') {
						foreach ($thumb->modules as $module) {
							if ($module === 'title' && $ytchag_title) {
								include 'title.php';
							}
							elseif ($module === 'publishedAt' && $ytchag_publishedAt) {
								include 'publishedAt.php';
							}
							elseif ($module === 'desc' && $ytchag_description) {
								include 'desc.php';
							}
							elseif ($module !== 'title' && $module !== 'publishedAt' && $module !== 'desc') {
								include $module . '.php';
							}
						}
					} else {
						//
						//min device
						if ($ytchag_thumbnail_alignment_device == 'all'){
							$thumb_align_device = 'col-xs-';
						} elseif ($ytchag_thumbnail_alignment_device == 'medium') {
							$thumb_align_device = 'col-md-';
						} elseif ($ytchag_thumbnail_alignment_device == 'large') {
							$thumb_align_device = 'col-lg-';
						} else {
							$thumb_align_device = 'col-sm-';
						}

						//thumb size
						if ($ytchag_thumbnail_alignment_width == 'extra_small'){
							$thumb_align_width = '4';
						} elseif ($ytchag_thumbnail_alignment_width == 'small') {
							$thumb_align_width = '5';
						} elseif ($ytchag_thumbnail_alignment_width == 'large') {
							$thumb_align_width = '7';
						} elseif ($ytchag_thumbnail_alignment_width == 'extra_large') {
							$thumb_align_width = '8';
						} else {
							$thumb_align_width = '6';
						}
						echo '<div class="row">';
						if ($ytchag_thumbnail_alignment == 'left') {
							echo '<div class="' . $thumb_align_device . $thumb_align_width . '">';
							include 'thumb.php';
							echo '</div>';
						}
						echo '<div class="' . $thumb_align_device . ( 12 - $thumb_align_width ) . '">';
						foreach ($thumb->modules as $module) {
							if ($module === 'title' && $ytchag_title) {
								include 'title.php';
							}
							elseif ($module === 'publishedAt' && $ytchag_publishedAt) {
								include 'publishedAt.php';
							}
							elseif ($module === 'desc' && $ytchag_description) {
								include 'desc.php';
							}
						}
						echo '</div>';
						if ($ytchag_thumbnail_alignment == 'right') {
							echo '<div class="' . $thumb_align_device . $thumb_align_width . '">';
							include 'thumb.php';
							echo '</div>';
						}
						echo '</div>';//end of row
					}
				?>
		</div> <?php //end col- ?>
    	<?php $multiplos ? '' : visible(array($xs,$sm,$md,$lg), $i); ?>

		<?php  $i += 1; ?>
		<?php endforeach; ?>

	</div> <?php //end row ?>

	<?php //Pagination ?>
	<?php if ($ytchag_thumb_pagination): ?>
		<div class="ytc-pagination row">
			<div class="col-xs-4 ytc-previous">
				<?php if (isset($ytchag_prev_token)): ?>
					<a class="ytc-paginationlink ytc-previous" data-cid="<?php echo $ytchag_id ?>" data-wid="<?php echo $plugincount ?>" data-playlist="<?php echo $ytchag_playlist?>" data-token="<?php echo $ytchag_prev_token?>">
						<?php echo ($ytchag_prev_text ? $ytchag_prev_text : _e('«Previous', 'youtube-channel-gallery'))?>
					</a>
				<?php endif; ?>
			</div>
			<div class="col-xs-4 ytc-numeration">
				<div class="ytc-numeration-inner">
					<?php $total_pages = ceil( $ytchag_total_results / $ytchag_results_per_page );?>
					<span class="ytc-currentpage">1</span><span class="ytc-separator">/</span><span class="ytc-totalpages"><?php echo $total_pages ?></span>
				</div>
			</div>
			<div class="col-xs-4 ytc-next">
				<?php if (isset($ytchag_next_token)): ?>
					<a class="ytc-paginationlink ytc-next" data-cid="<?php echo $ytchag_id ?>" data-wid="<?php echo $plugincount ?>" data-playlist="<?php echo $ytchag_playlist?>" data-token="<?php echo $ytchag_next_token?>">
						<?php echo ($ytchag_next_text ? $ytchag_next_text : _e('Next»', 'youtube-channel-gallery'))?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

</div> <?php  //end container ?>

