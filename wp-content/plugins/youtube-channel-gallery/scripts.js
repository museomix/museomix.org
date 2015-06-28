/*------------------------------------------------------------
Plugin Name: Youtube Channel Gallery
Plugin URI: http://www.poselab.com/
Version: 2.0.0
Description: Show a youtube video and a gallery of thumbnails for a youtube channel.
------------------------------------------------------------*/	
jQuery(document).ready(function($) {
	
	//thumbnails
	var ytcplayer = {};
    $('.youtubechannelgallery').on('click', '.ytclink', function(e) {
		var iframeid = $(this).attr('data-playerid');
		var youtubeid = $(this).attr('href').split("watch?v=")[1];
		var quality = $(this).attr('data-quality');
		checkIfInView($('#' + iframeid));
		ytcplayVideo (iframeid, youtubeid, quality);

		return false;
	});

	$('.popup-youtube').magnificPopup({
		disableOn: 700,
		type: 'iframe',
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: false,

		fixedContentPos: false,
        gallery: {
          enabled:true
        }
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
	rowDivs = [],
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

    $('.widget.youtubechannelgallery').on('click', '.ytc-paginationlink', function(e) {

      var token = $(this).data('token'),
          playlist = $(this).data('playlist'),
          cid = $(this).data('cid'),
          wid = $(this).data('wid');

	  $.ajax({
	    url: ytcAjax.ajaxurl,
	    type: 'POST',
	    data: {
	      action: 'ytc_next',
	      token: token,
	      cid: cid,
	      playlist: playlist
        },
        success: function(data) {

          var widget = $('#youtubechannelgallery_widget-' + wid);

          widget.find('.ytc-thumbnails').empty();

          widget.find('.ytc-thumbnails').prepend(data);

          $('.popup-youtube').magnificPopup({
              disableOn: 700,
              type: 'iframe',
              mainClass: 'mfp-fade',
              removalDelay: 160,
              preloader: false,

              fixedContentPos: false,
				gallery: {
				  enabled:true
				}
          });
        }
      });

	  return true;
    });

    $('.widget.youtubechannelgallery').on('keyup blur', '.search', function(e) {

      var $this = $(this);

      if (e.type === 'blur' || e.type === 'focusout' || (e.type === 'keyup' && e.which === 13)) {

        var $widget = $('.widget.youtubechannelgallery'),
            wid = $this.data('wid'),
            cid = $this.data('cid'),
            tag = $widget.find('.select-tag').val();

        $.ajax({
          url: ytcAjax.ajaxurl,
          type: 'POST',
          data: {
            action: 'ytc_search',
	        cid: cid,
            q: this.value,
            tag: tag
          },
          success: function(data) {

            if (data === '') {
              $this.val('');
              return;
            }

            var widget = $('#youtubechannelgallery_widget-' + wid);

            widget.find('ytc-thumbnails').remove();
            widget.find('.ytc-paginationlink').remove();

            widget.prepend(data);

            $('.popup-youtube').magnificPopup({
                disableOn: 700,
                type: 'iframe',
                mainClass: 'mfp-fade',
                removalDelay: 160,
                preloader: false,

                fixedContentPos: false,
		        gallery: {
		          enabled:true
		        }
            });
          }
        });

      }
      return true;
    });
});
