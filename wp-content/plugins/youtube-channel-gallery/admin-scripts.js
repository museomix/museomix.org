jQuery( function ( $ ) {
	function init( widget_el, is_cloned ) {
		$(document).ajaxSuccess(function(e, xhr, settings) {

			// on added or saved
			if(settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=youtubechannelgallery_widget') != -1) {
				$widget = $(e.currentTarget.activeElement).parents('.widget');
					ytchg_ActivateChosen();
					ytchg_init_tabs();


			}

			// on re-ordered
			if(settings.data.search('action=widgets-order') != -1) {
				ytchg_ActivateChosen();
			}
		});

		// on load
		function ytchg_ActivateChosen() {
			$( '.widget[id*="youtubechannelgallery_widget"]' ).each( function() {
				ytchg_SetChosen( $(this) );
				show_title_description( $(this), '.tabs-2' );
				show_title_description( $(this), '.tabs-4' );
				changeFeedType( $(this) );
				changeplayerType( $(this) );
				changeAlignThumb( $(this) );
				add_tooltips( $(this) );
				searchTab( $(this) );
			});
		}

		// fire on page load
		ytchg_ActivateChosen();
		ytchg_init_tabs();


		function ytchg_init_tabs() {
		// hide content but first
		$('.ytchgtabs-tabs li:first-child').addClass('active');
		$('.ytchgtabs .ytchgtabs-content:not(:first-of-type)').hide();
		}


		function ytchg_SetChosen( widget ) {
			$widget = $(widget);
			$selectList = $('.ytchgtabs', $widget);

			//tabs
			$('.ytchgtabs-tabs li a', $widget).click(
				function(event){
					$selectList = event.currentTarget;
					ytchg_update_tabs( $selectList );
					return false;
				}
			);

			// link open title and description
			$('.tabs-2 .ytchg-tit-desc a', $widget).click(
				function(event){
					$tit_desc_link = event.currentTarget;
					click_title_description( $tit_desc_link );
					return false;
				}
			);
			$('.tabs-4 .ytchg-tit-desc a', $widget).click(
				function(event){
					$tit_desc_link = event.currentTarget;
					click_title_description( $tit_desc_link );
					return false;
				}
			);
			var alignSelect = '.ytchg-field-tit-desc select[id*="ytchag_thumbnail_alignment"]';
			$(alignSelect, $widget).change(
				function (event) {
					$align_change = event.currentTarget;
					$current_widget = $(event.currentTarget).parents('.widget');
					changeAlignThumb ($current_widget);
			});

			//feed
			var feedSelect = '.tabs-1 select[id*="ytchag_feed"]';
			$(feedSelect, $widget).change(
				function (event) {
					$feed_change = event.currentTarget;
					$current_widget = $(event.currentTarget).parents('.widget');
					changeFeedType ($current_widget);
			});

			//player?
			var playerSelect = '.tabs-2 select[id*="ytchag_player"]';
			$(playerSelect, $widget).change(
				function (event) {
					$player_change = event.currentTarget;
					$current_widget = $(event.currentTarget).parents('.widget');
					changeplayerType ($current_widget);
			});

		}


		function ytchg_update_tabs( selectList ) {
			$selectList = $(selectList);
			$parentWidget = $selectList.parents('.widget');

			//not work on the current tab
			if(!$selectList.parent().hasClass('active')){
				//tabs
				$('.ytchgtabs-tabs li', $parentWidget).removeClass('active');
				$selectList.parent().addClass('active');

				//content tabs
				var currentTab = $selectList.attr('href');
				//slideUp and slideDown to give it animation
				$('.ytchgtabs > div', $parentWidget).slideUp('fast');
				$(currentTab, $parentWidget).slideDown('fast');
			}
			return false;
		}

		function click_title_description( tit_desc_link ) {
			$tit_desc_link = $(tit_desc_link);
			$parentWidget = $tit_desc_link.parents('.ytchgtabs-content');

			if(!$('fieldset.ytchg-field-tit-desc', $parentWidget).hasClass('active')){
				$('.ytchg-title-and-description', $parentWidget).slideDown('fast',function(){
					$('.ytchg-field-tit-desc', $parentWidget).addClass('ytchg-fieldborder active');
				});
			} else{
				$('.ytchg-title-and-description', $parentWidget).slideUp('fast',function(){
					$('.ytchg-field-tit-desc', $parentWidget).removeClass('ytchg-fieldborder active');
				});
			}
		}


		//checkboxes with associated content
		//---------------

		function show_title_description ( widget, tab ) {
			$widget = $(widget);
			$tabs = $(tab, $widget);

			if( $('.ytchg-tit', $tabs).is(':checked') || $('.ytchg-desc', $tabs).is(':checked')){
				$('.ytchg-title-and-description', $tabs).show();
				$('fieldset.ytchg-field-tit-desc', $tabs).addClass('ytchg-fieldborder active');
			} else{
				$('.ytchg-title-and-description', $tabs).hide();
			}
		}


		//Feed label title
		//---------------

		function changeFeedType ( widget ) {
			$widget = $(widget);

			var feedSelect = '.tabs-1 select[id*="ytchag_feed"]';
			var userLabel = 'label[for*="ytchag_user"]';
			var feedOrder = 'p[class*="ytchag_feed_order"]';

			// user / playlist label
			if(['user', 'favorites', 'likes'].indexOf($(feedSelect + ' option:selected', $widget).val()) !== -1){
				$('.feed_user_id_label', $widget).show();
				$('.feed_playlist_id_label', $widget).hide();
				$('.identify_by', $widget).show();
				$('.user', $widget).removeClass('col-md-12').addClass('col-md-8');
			}
	        else if(['playlist'].indexOf($(feedSelect + ' option:selected', $widget).val()) !== -1){
				$('.feed_playlist_id_label', $widget).show();
				$('.feed_user_id_label', $widget).hide();
				$('.identify_by', $widget).hide();
				$('.user', $widget).removeClass('col-md-8').addClass('col-md-12');
			}

			// order
			if(['user'].indexOf($(feedSelect + ' option:selected', $widget).val()) !== -1){
				$(feedOrder, $widget).slideDown('fast');
			}
	        else{
				$(feedOrder, $widget).slideUp('fast');
			}
		}


		//Feed label title
		//---------------

		function changeAlignThumb ( widget ) {
			$widget = $(widget);

			var alignmentSelect = 'select[id*="ytchag_thumbnail_alignment"]';
			var align_options = '.align-options';

			if($(alignmentSelect + ' option:selected', $widget).val() === 'none'){
				$(align_options, $widget).slideUp('fast');
			} else{
				$(align_options, $widget).slideDown('fast');
			}
		}


		//Player type
		//---------------

		function changeplayerType ( widget ) {
			$widget = $(widget);

			var playerSelect = '.tabs-2 select[id*="ytchag_player"]';
			var player_options = '.tabs-2 .player_options';
			var thumb_window = '.tabs-4 .thumb_window';

			if($(playerSelect + ' option:selected', $widget).val() === '0'){
				$(thumb_window, $widget).show();
				$(player_options, $widget).slideUp('fast');
			}
			if($(playerSelect + ' option:selected', $widget).val() === '1'){
				$(thumb_window, $widget).hide();
				$(player_options, $widget).slideDown('fast');
			}
			if($(playerSelect + ' option:selected', $widget).val() === '2'){
				$(thumb_window, $widget).hide();
				$(player_options, $widget).slideUp('fast');
			}
		}


		//Search tab
		//---------------

		function searchTab(widget) {

	      this.llamada = this.llamada || {};

	      if (this.llamada[widget[0].id]) {
	        return;
	      }
	      else {
	        this.llamada[widget[0].id] = 1;
	      }

		  $widget = $(widget);

	      $widget.on('keyup', '[id$="ytchag_search_select_options"]', function(e) {

	        var campos = this.value.split('#'),
	            $select =  $widget.find('[id$="ytchag_search_select_default"]'),
	            restrict = '',
	            options = '';


	        $.each(campos, function(i, c) {

	          var tag = c.toLocaleLowerCase().replace(/ /g, '_');

	          if (c !== '') {
	            restrict += (restrict ? ',' : '') + 'restrict_' + tag;
	            options += '<option value="' + tag + '">' + c + '</option>';
	          }
	        });

	        $widget.find('.restrict').html(restrict);
	        $select.find('option:gt(0)').remove();
	        $select.append(options);

	        return true;
	      });

	      $widget.on('change', '[id$="ytchag_feed"]', function(e) {
	        if ($(this).val() === 'user') {
	          $widget.find('.ytchgtabs-tabs > li:eq(2)').show();
	        }
	        else {
	          $widget.find('.ytchgtabs-tabs > li:eq(2)').hide();
	        }
	      });
	    }

		function add_tooltips(widget) {

		    $( ".ytchag_info" ).tooltip(
		    	{
		    	tooltipClass: "ytchgtooltip",
		    	position: { my: "center bottom-28px", at: "center bottom", collision: "none" },

		    });
		}

	}


	function on_form_update( e, widget_el ) {
		if ( 'youtubechannelgallery_widget' === widget_el.find( 'input[name="id_base"]' ).val() ) {
			init( widget_el, 'widget-added' === e.type );
		}
	}

	$( document ).on( 'widget-updated', on_form_update );
	$( document ).on( 'widget-added', on_form_update );

	$( '.widget[id*="youtubechannelgallery_widget"]' ).each( function () {
		init( $( this ) );
	} );

} );