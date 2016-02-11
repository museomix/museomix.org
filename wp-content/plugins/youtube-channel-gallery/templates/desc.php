<?php $words = isset($playercontent) ? $ytchag_player_description_words_number : $ytchag_description_words_number;?>
<div class="ytc<?php echo isset($playercontent)? $playercontent : '';?>tdescription">
  <?php echo make_clickable(($words ? wp_trim_words( $thumb->description, $words, '...' ) : $thumb->description));?>
</div>