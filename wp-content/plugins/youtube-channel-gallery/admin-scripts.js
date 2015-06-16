jQuery(document).ready(function($) {						

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
		$( '#widgets-right .widget[id*="youtubechannelgallery_widget"]' ).each( function() {
			ytchg_SetChosen( $(this) );
			show_title_description( $(this) );
			changeFeedType( $(this) );
			changeplayerType( $(this) );
			
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
		$('.ytchg-tit-desc a', $widget).click(
			function(event){
				$tit_desc_link = event.currentTarget;
				click_title_description( $tit_desc_link );
				return false;
			}
		);

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
		$parentWidget = $tit_desc_link.parents('.widget');
		
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
	
	function show_title_description ( widget ) {
		$widget = $(widget);
		$tabs3 = $('.tabs-3', $widget);

		if( $('.ytchg-tit', $tabs3).is(':checked') || $('.ytchg-desc', $tabs3).is(':checked')){
			$('.ytchg-title-and-description', $tabs3).show();
			$('fieldset.ytchg-field-tit-desc', $tabs3).addClass('ytchg-fieldborder active');
		} else{
			$('.ytchg-title-and-description', $tabs3).hide();
		}
	}


	//Feed label title
	//---------------

	function changeFeedType ( widget ) {
		$widget = $(widget);

		var feedSelect = '.tabs-1 select[id*="ytchag_feed"]';
		var userLabel = 'label[for*="ytchag_user"]';
		var feedOrder = 'p[class*="ytchag_feed_order"]';

		if($(feedSelect + ' option:selected', $widget).val() === 'user'){
			$('.feed_user_id_label', $widget).show();
			$('.feed_playlist_id_label', $widget).hide();
			$(feedOrder, $widget).slideUp('fast');
		}
		if($(feedSelect + ' option:selected', $widget).val() === 'playlist'){
			$('.feed_playlist_id_label', $widget).show();
			$('.feed_user_id_label', $widget).hide();
			$(feedOrder, $widget).slideDown('fast');
		}
	}


	//Feed label title
	//---------------

	function changeplayerType ( widget ) {
		$widget = $(widget);

		var playerSelect = '.tabs-2 select[id*="ytchag_player"]';
		var player_options = '.tabs-2 .player_options';
		var thumb_window = '.tabs-3 .thumb_window';

		if($(playerSelect + ' option:selected', $widget).val() === '0'){
			$(thumb_window, $widget).show();
			$(player_options, $widget).slideUp('fast');
		}
		if($(playerSelect + ' option:selected', $widget).val() === '1'){
			$(thumb_window, $widget).hide();
			$(player_options, $widget).slideDown('fast');
		}
	}

});