<?php $tag = isset($playercontent) ? $ytchag_player_title_tag : $ytchag_title_tag;?>
<<?php echo $tag?> class="ytc<?php echo isset($playercontent)? $playercontent : '';?>title">
	<?php if(!isset($playercontent)): ?>
  		<a class="<?php echo (($ytchag_player == 2) ? 'popup-youtube' : 'ytclink')?>" href="https://www.youtube.com/watch?v=<?php echo $thumb->id?>" data-playerid="ytcplayer<?php echo $plugincount?>" data-quality="<?php echo $thumb->quality?>" alt="<?php echo $thumb->title?>" title="<?php echo $thumb->title?>" <?php echo ($ytchag_nofollow ? 'rel="nofollow"' : '')?> <?php echo ($ytchag_thumb_window ? 'target="_blank"' : '')?>>
	<?php endif; ?>
  			<?php echo $thumb->title;?>
	<?php if(!isset($playercontent)): ?>
		</a>
	<?php endif; ?>
</<?php echo $tag?>>