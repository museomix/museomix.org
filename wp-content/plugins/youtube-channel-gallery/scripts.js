/*------------------------------------------------------------
Plugin Name: Youtube Channel Gallery
Plugin URI: http://www.poselab.com/
Version: 1.8.7
Description: Show a youtube video and a gallery of thumbnails for a youtube channel.
------------------------------------------------------------*/	
jQuery(document).ready(function($) {
	
	//thumbnails
	var ytcplayer = {};
	$('.ytclink').click(function(){
		var iframeid = $(this).attr('data-playerid');
		var youtubeid = $(this).attr('href').split("watch?v=")[1];
		var quality = $(this).attr('data-quality');
		checkIfInView($('#' + iframeid));
		ytcplayVideo (iframeid, youtubeid, quality);

		return false;
	});

	function ytcplayVideo (iframeid, youtubeid, quality) {
		if(iframeid in ytcplayer) { 
			ytcplayer[iframeid].loadVideoById(youtubeid); 
		}else{
			ytcplayer[iframeid] = new YT.Player(iframeid, {
				events: { 
					'onReady': function(){
						ytcplayer[iframeid].loadVideoById(youtubeid);
						ytcplayer[iframeid].setPlaybackQuality(quality);
					}
				}
			});
		}

	}


	//Scroll to element only if not in view - jQuery
	//http://stackoverflow.com/a/10130707/1504078
	function checkIfInView(element){
		if($(element).offset()){
			if($(element).offset().top < $(window).scrollTop()){
			//scroll up
			$('html,body').animate({scrollTop:$(element).offset().top - 10}, 500);
		}
		else if($(element).offset().top + $(element).height() > $(window).scrollTop() + (window.innerHeight || document.documentElement.clientHeight)){
			//scroll down
			$('html,body').animate({scrollTop:$(element).offset().top - (window.innerHeight || document.documentElement.clientHeight) + $(element).height() + 10}, 500);}
		}
	}


	//Equal Height Blocks in Rows
	//http://css-tricks.com/equal-height-blocks-in-rows/
	var currentTallest = 0,
	currentRowStart = 0,
	rowDivs = new Array(),
	$el,
	topPosition = 0;

	jQuery('.ytc-td-bottom .ytc-row .ytctitledesc-cont').each(function() {

		$el = jQuery(this);
		topPostion = $el.position().top;
		
		if (currentRowStart != topPostion) {
			// we just came to a new row.  Set all the heights on the completed row
			for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
				rowDivs[currentDiv].height(currentTallest);
			}
			// set the variables for the new row
			rowDivs.length = 0; // empty the array
			currentRowStart = topPostion;
			currentTallest = $el.height();
			rowDivs.push($el);
		} else {
			// another div on the current row.  Add it to the list and check if it's taller
			rowDivs.push($el);
			currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
		}

		// do the last row
		for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
			rowDivs[currentDiv].height(currentTallest);
		}
 
	});

});
