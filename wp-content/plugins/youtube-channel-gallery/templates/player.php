<?php

	// Load js
	$this->register_scripts();

	//player
	//------------

	// to control the width of the 
	$content = '<div class="ytcplayer-fixwidthwrapper">';
	$content .= '<div class="ytcplayer-wrapper ytc-player' . $ytchag_ratio . '">';
	
	//iframe
	$content .= '<iframe id="ytcplayer' . $plugincount . '" class="ytcplayer" allowfullscreen src="' . $youtube_url . '/embed/' . $youtubeid . '?version=3' . $ytchag_theme . $ytchag_color .  $ytchag_autoplay.  $ytchag_modestbranding . $ytchag_rel . $ytchag_showinfo .'&enablejsapi=1&wmode=transparent" frameborder="0"></iframe>';
	$content .= '</div></div>';
