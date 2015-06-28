<div class="ytcthumb-cont"<?php echo (($ytchag_thumbnail_alignment != 'none') ? 'style="width:'.$ytchag_thumb_width.'px!important;"' : '')?>>
  <a class="ytcthumb <?php echo (($ytchag_player == 2) ? 'popup-youtube' : 'ytclink')?>" href="https://www.youtube.com/watch?v=<?php echo $thumb->id?>" data-playerid="ytcplayer<?php echo $plugincount?>" data-quality="<?php echo $thumb->quality?>" title="<?php echo $thumb->title?>" style="background-image:url(<?php echo $thumb->img?>);" <?php echo ($ytchag_nofollow ? 'rel="nofollow"' : '')?> <?php echo ($ytchag_thumb_window ? 'target="_blank"' : '')?>>
    <div class="ytcplay"></div>
  </a>
</div>

