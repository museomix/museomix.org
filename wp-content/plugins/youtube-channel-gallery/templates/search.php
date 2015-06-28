<?php if ($ytchag_search_input_show): ?>
<input class="search" type="search" data-cid="<?php echo $ytchag_id ?>" data-wid="<?php echo $this->number ?>" placeholder="<?php echo $ytchag_search_input?>">
<?php endif; ?>
<?php if ($ytchag_search_playlists_show): ?>
<?php  $campos = array(); ?>
<?php  if ($instance['ytchag_search_playlists']): ?>
<?php   $campos = explode('#', $instance['ytchag_search_playlists']); ?>
<?php endif; ?>
<select class="search-select">
  <option value=""><?php echo _e('None', 'youtube-channel-gallery') ?></option>
<?php   foreach ($campos as $c): ?>
<?php    $tag = toTag($c); ?>
                    <option value="<?php echo $tag ?>"<?php selected( $instance['ytchag_search_restrict'], $tag ); ?>><?php _e( $c, 'youtube-channel-gallery' ); ?></option>
<?php   endforeach; ?>
</select>
<?php endif; ?>
