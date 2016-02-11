<?php $embed_responsive = $ytchag_ratio == '4x3' ? 'embed-responsive-4by3' : 'embed-responsive-16by9'; ?>
<div class="ytc-pslb ytcplayer-wrapper" style="width:<?php echo $ytchag_width_value?><?php echo $ytchag_width_type?>;">
  <div class="embed-responsive <?php echo $embed_responsive?>">
    <iframe id="ytcplayer<?php echo $plugincount?>" class="ytcplayer" allowfullscreen src="<?php echo $youtube_url?>/embed/<?php echo $youtubeid?>?version=3<?php echo $ytchag_theme?><?php echo $ytchag_color?><?php echo $ytchag_autoplay?><?php echo $ytchag_modestbranding?><?php echo $ytchag_rel?><?php echo $ytchag_showinfo?>&enablejsapi=1&wmode=transparent" frameborder="0"></iframe>
  </div>
</div>
