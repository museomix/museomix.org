<?php if ($ytchag_search_input_show || $ytchag_search_select_show): ?>
	<div class="ytc-search">
		<?php if ($ytchag_search_input_show): ?>
			<input class="search-field" type="search" data-cid="<?php echo $ytchag_id ?>" data-wid="<?php echo $this->number ?>" placeholder="<?php echo $ytchag_search_input_text?>">
		<?php endif; ?>

		<?php if ($ytchag_search_select_show): ?>
			<?php  $campos = array(); ?>
			<?php if ($instance['ytchag_search_select_options']): ?>
				<?php $campos = explode('#', $instance['ytchag_search_select_options']); ?>
			<?php endif; ?>
			<select class="search-select" data-cid="<?php echo $ytchag_id ?>">
				<option value=""><?php echo _e('All', 'youtube-channel-gallery') ?></option>
				<?php foreach ($campos as $c): ?>
					<?php $tag = toTag($c); ?>
				    <option value="<?php echo $tag ?>"<?php selected( $instance['ytchag_search_select_default'], $tag ); ?>><?php _e( $c, 'youtube-channel-gallery' ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>
	</div>
<?php endif; ?>
