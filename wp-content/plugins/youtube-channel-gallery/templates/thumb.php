<div class="ytcthumb-cont <?php echo $thumb->privacyStatus?>">
  <a class="ytcthumb <?php echo (($ytchag_player == 2) ? 'popup-youtube' : 'ytclink')?>" href="https://www.youtube.com/watch?v=<?php echo $thumb->id?>" data-playerid="ytcplayer<?php echo $plugincount?>" data-quality="<?php echo $thumb->quality?>" title="<?php echo $thumb->title?>" style="background-image:url(<?php echo $thumb->img?>);" <?php echo ($ytchag_nofollow ? 'rel="nofollow"' : '')?> <?php echo ($ytchag_thumb_window ? 'target="_blank"' : '')?>>
    <?php if ($thumb->privacyStatus == 'private'): ?>
    	<div class="private-text"><?php _e( 'Private video', 'youtube-channel-gallery');?></div>
    <?php endif; ?>
    <div class="ytcplay"></div>
  </a>
  <?php if ($ytchag_duration): ?>
	  <span class="video-time">
	  		<span><?php echo $thumb->duration;?></span>
	  </span>
  <?php endif; ?>
</div>

