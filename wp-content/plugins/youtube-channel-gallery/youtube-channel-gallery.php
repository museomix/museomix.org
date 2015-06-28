<?php
/*
	Plugin Name: Youtube Channel Gallery
	Plugin URI: http://www.poselab.com/
	Description: Show a youtube video and a gallery of thumbnails for a youtube channel.
	Author: Javier Gómez Pose
	Author URI: http://www.poselab.com/
	Version: 2.0.3
	License: GPL2

		Copyright 2013 Javier Gómez Pose  (email : javierpose@gmail.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/

/**
 * widget class.
 */
class YoutubeChannelGallery_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		//localization
		load_plugin_textdomain( 'youtube-channel-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		add_shortcode( 'Youtube_Channel_Gallery', array( $this, 'YoutubeChannelGallery_Shortcode' ) );

		//load admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts_and_styles' ) );
		//load font-end styles
		add_action('wp_enqueue_scripts', array($this,'register_styles'));

		parent::__construct(
			'youtubechannelgallery_widget', // Base ID
			__( 'Youtube Channel Gallery', 'youtube-channel-gallery' ), // Name

			array( 'classname'  => 'youtubechannelgallery ytccf', 'description' => __( 'Show a youtube video and a gallery of thumbnails for a youtube channel', 'youtube-channel-gallery' ), ), // Args

			array( 'width' => 260 )
		);

        add_action('wp_ajax_ytc_next', array($this, 'nextVideos'));
        add_action('wp_ajax_nopriv_ytc_next', array($this, 'nextVideos'));

        add_action('wp_ajax_ytc_search', array($this, 'searchVideos'));
        add_action('wp_ajax_nopriv_ytc_search', array($this, 'searchVideos'));
	}


    function nextVideos() {

      $settings = $this->get_settings();
      $instance = $settings[$this->number];

      extract($instance);

      $token = $_POST['token'];
      $ytchag_playlist = $_POST['playlist'];
      $ytchag_id = $_POST['cid'];

      if ($ytchag_search_playlists && $ytchag_id) {
        $ytchag_feed_url = 'https://www.googleapis.com/youtube/v3/search';
        $ytchag_feed_url .= '?part=snippet';
        $ytchag_feed_url .= '&channelId=' . $ytchag_id;
        $ytchag_feed_url .= '&maxResults=' . $ytchag_maxitems;

        if ($ytchag_search_restrict) {
          $ytchag_feed_url .= '&q=' . $ytchag_search_restrict;
        }
        else {
          $ytchag_feed_url .= '&q=' . implode(',', array_map('toTag', explode('#', $ytchag_search_playlists)));
        }

        $ytchag_feed_url .= '&type=video';
        $ytchag_feed_url .= '&key=' . $ytchag_key;
        $ytchag_feed_url .= '&pageToken=' . $token;

      }
      else {

        $api = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=';
        $resto = '&maxResults=' . $ytchag_maxitems . '&key=' . $ytchag_key;

        $ytchag_feed_url = $api . $ytchag_playlist . $resto . '&pageToken=' . $token;
      }

      $transientId = 'ytc-' .md5( $ytchag_feed_url . $ytchag_user . $ytchag_maxitems . $token);

      $videos_result = $this->get_rss_data ( $ytchag_cache, $transientId, $ytchag_feed_url, $ytchag_cache_time);

      // Thumb order

      $modules = array();

      $modules[$ytchag_thumb_order_thumb . '1'] = 'thumb';
      $modules[$ytchag_thumb_order_title . '2'] = 'title';
      $modules[$ytchag_thumb_order_desc . '3'] = 'desc';

      ksort($modules);

      $json = json_decode($videos_result['body']);

      $ytchag_results_per_page = $json->pageInfo->resultsPerPage;
      $ytchag_total_results = $json->pageInfo->totalResults;

      if (isset($json->nextPageToken)) {
        $ytchag_next_token = $json->nextPageToken;
      }

      if (isset($json->prevPageToken)) {
        $ytchag_prev_token = $json->prevPageToken;
      }

      $thumbs = $this->getThumbs($json->items, $modules, $ytchag_thumb_width);

      include 'templates/thumbs.php';

      exit();
    }

    function searchVideos() {

      $settings = $this->get_settings();
      $instance = $settings[$this->number];

      extract($instance);

      $q = $_POST['token'];
      $ytchag_search_restrict = $_POST['tag'];
      $ytchag_id = $_POST['cid'];

      $ytchag_feed_url = 'https://www.googleapis.com/youtube/v3/search';
      $ytchag_feed_url .= '?part=snippet';
      $ytchag_feed_url .= '&channelId=' . $ytchag_id;
      $ytchag_feed_url .= '&maxResults=' . $ytchag_maxitems;

      if ($ytchag_search_restrict) {
        $q = $ytchag_search_restrict;
      }
      else {
        $q = implode(',', array_map('toTag', explode('#', $ytchag_search_playlists)));
      }

      $ytchag_feed_url .= '&q=' . $q;

      $ytchag_feed_url .= '&type=video';
      $ytchag_feed_url .= '&key=' . $ytchag_key;

      $transientId = 'ytc-' .md5( $ytchag_feed_url . $ytchag_user . $ytchag_maxitems . $q);

      $videos_result = $this->get_rss_data ( $ytchag_cache, $transientId, $ytchag_feed_url, $ytchag_cache_time);

      if ($videos_result['response']['code'] != 200) {
        echo '';
        exit();
      }

      // Thumb order

      $modules = array();

      $modules[$ytchag_thumb_order_thumb . '1'] = 'thumb';
      $modules[$ytchag_thumb_order_title . '2'] = 'title';
      $modules[$ytchag_thumb_order_desc . '3'] = 'desc';

      ksort($modules);

      $json = json_decode($videos_result['body']);

      $ytchag_results_per_page = $json->pageInfo->resultsPerPage;
      $ytchag_total_results = $json->pageInfo->totalResults;

      if (isset($json->nextPageToken)) {
        $ytchag_next_token = $json->nextPageToken;
      }

      if (isset($json->prevPageToken)) {
        $ytchag_prev_token = $json->prevPageToken;
      }

      $thumbs = $this->getThumbs($json->items, $modules, $ytchag_thumb_width);

      include 'templates/thumbs.php';

      exit();
    }

    function getThumbs($items, $modules, $thumb_width) {

      $thumbs = array();

      foreach ($items as $item) {

        $thumb = new stdClass();

        $thumb->id = $item->snippet->resourceId->videoId;
        $thumb->title = $item->snippet->title;
        $thumb->description = $item->snippet->description;
        $thumb->modules = $modules;

        if ($thumb_width <= 120) {
          $thumb->img = $item->snippet->thumbnails->default->url;
          $thumb->quality = 'default';
        }
        elseif ($thumb_width > 120 && $thumb_width <= 320) {
          $thumb->img = $item->snippet->thumbnails->medium->url;
          $thumb->quality = 'mqdefault';
        }
        elseif ($thumb_width > 320 && $thumb_width <= 480) {
          $thumb->img = $item->snippet->thumbnails->high->url;
          $thumb->quality = 'hqdefault';
        }
        elseif ($thumb_width > 480 && $thumb_width <= 640) {
          $thumb->img = $item->snippet->thumbnails->standard->url;
          $thumb->quality = 'sddefault';
        }
        else {
          $thumb->img = $item->snippet->thumbnails->maxres->url;
          $thumb->quality = 'maxresdefault';
        }

        $thumbs[] = $thumb;
      }

      return $thumbs;
    }

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}


		//echo $this->ytchag_rss_markup( $instance );
		echo $this->ytchag_json_markup( $instance );

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		// Feed options
		$instance['ytchag_key'] = strip_tags( $new_instance['ytchag_key'] );
		$instance['ytchag_feed'] = strip_tags( $new_instance['ytchag_feed'] );
		$instance['ytchag_user'] = strip_tags( $new_instance['ytchag_user'] );
		//$instance['ytchag_id'] = strip_tags( $new_instance['ytchag_id'] );
		//$instance['ytchag_user_uploads'] = strip_tags( $new_instance['ytchag_user_uploads'] );
		//$instance['ytchag_user_favorites'] = strip_tags( $new_instance['ytchag_user_favorites'] );
		$instance['ytchag_feed_order'] = strip_tags( $new_instance['ytchag_feed_order'] );
		$instance['ytchag_cache_time'] = strip_tags( $new_instance['ytchag_cache_time'] );
		$instance['ytchag_cache'] = strip_tags( $new_instance['ytchag_cache'] );

		// Player options
		$instance['ytchag_player'] = strip_tags( $new_instance['ytchag_player'] );
		$instance['ytchag_width_value'] = strip_tags( $new_instance['ytchag_width_value'] );
		$instance['ytchag_width_type'] = strip_tags( $new_instance['ytchag_width_type'] );
		$instance['ytchag_ratio'] = strip_tags( $new_instance['ytchag_ratio'] );
		$instance['ytchag_theme'] = strip_tags( $new_instance['ytchag_theme'] );
		$instance['ytchag_color'] = strip_tags( $new_instance['ytchag_color'] );
		$instance['ytchag_quality'] = strip_tags( $new_instance['ytchag_quality'] );
		$instance['ytchag_autoplay'] = strip_tags( $new_instance['ytchag_autoplay'] );
		$instance['ytchag_modestbranding'] = strip_tags( $new_instance['ytchag_modestbranding'] );
		$instance['ytchag_rel'] = strip_tags( $new_instance['ytchag_rel'] );
		$instance['ytchag_showinfo'] = strip_tags( $new_instance['ytchag_showinfo'] );
		$instance['ytchag_player_order'] = strip_tags( $new_instance['ytchag_player_order'] );

        // Search options
		$instance['ytchag_search_input'] = strip_tags( $new_instance['ytchag_search_input'] );
		$instance['ytchag_search_playlists'] = strip_tags( $new_instance['ytchag_search_playlists'] );
		$instance['ytchag_search_restrict'] = strip_tags( $new_instance['ytchag_search_restrict'] );
		$instance['ytchag_search_input_show'] = strip_tags( $new_instance['ytchag_search_input_show'] );
		$instance['ytchag_search_playlists_show'] = strip_tags( $new_instance['ytchag_search_playlists_show'] );
		$instance['ytchag_search_order'] = strip_tags( $new_instance['ytchag_search_order'] );

		// Thumbnail options
		$instance['ytchag_maxitems'] = strip_tags( $new_instance['ytchag_maxitems'] );
		$instance['ytchag_thumb_width'] = strip_tags( $new_instance['ytchag_thumb_width'] );
		$instance['ytchag_thumb_ratio'] = strip_tags( $new_instance['ytchag_thumb_ratio'] );
		$instance['ytchag_thumb_columns_phones'] = strip_tags( $new_instance['ytchag_thumb_columns_phones'] );
		$instance['ytchag_thumb_columns_tablets'] = strip_tags( $new_instance['ytchag_thumb_columns_tablets'] );
		$instance['ytchag_thumb_columns_md'] = strip_tags( $new_instance['ytchag_thumb_columns_md'] );
		$instance['ytchag_thumb_columns_ld'] = strip_tags( $new_instance['ytchag_thumb_columns_ld'] );
		$instance['ytchag_nofollow'] = strip_tags( $new_instance['ytchag_nofollow'] );
		$instance['ytchag_thumb_window'] = strip_tags( $new_instance['ytchag_thumb_window'] );
		$instance['ytchag_pagination_show'] = strip_tags( $new_instance['ytchag_pagination_show'] );
		$instance['ytchag_thumb_order_thumb'] = strip_tags( $new_instance['ytchag_thumb_order_thumb'] );
		$instance['ytchag_thumb_order_title'] = strip_tags( $new_instance['ytchag_thumb_order_title'] );
		$instance['ytchag_thumb_order_desc'] = strip_tags( $new_instance['ytchag_thumb_order_desc'] );
		$instance['ytchag_thumb_order'] = strip_tags( $new_instance['ytchag_thumb_order'] );

		$instance['ytchag_title'] = strip_tags( $new_instance['ytchag_title'] );
		$instance['ytchag_description'] = strip_tags( $new_instance['ytchag_description'] );
		$instance['ytchag_thumbnail_alignment'] = strip_tags( $new_instance['ytchag_thumbnail_alignment'] );
		$instance['ytchag_title_tag'] = strip_tags( $new_instance['ytchag_title_tag'] );
		$instance['ytchag_description_words_number'] = strip_tags( $new_instance['ytchag_description_words_number'] );

		// Link options
		$instance['ytchag_link'] = $new_instance['ytchag_link'];
		$instance['ytchag_link_tx'] = strip_tags( $new_instance['ytchag_link_tx'] );
		$instance['ytchag_link_window'] = strip_tags( $new_instance['ytchag_link_window'] );
		$instance['ytchag_link_order'] = strip_tags( $new_instance['ytchag_link_order'] );

        $instance['ytchag_promotion'] = $new_instance['ytchag_promotion'];

        if (isset($instance['ytchag_user']) && ($new_instance['ytchag_user'] !== $old_instance['ytchag_user'] || $instance['ytchag_user_uploads'] === '')) {

          $item = $this->getUserPlaylists($instance['ytchag_user'], $instance['ytchag_key'], $instance['ytchag_cache'], $instance['ytchag_cache_time']);
          $playlists = $item['contentDetails']['relatedPlaylists'];

          $instance['ytchag_id'] = $item['id'];
          $instance['ytchag_user_uploads'] = $playlists['uploads'];
          $instance['ytchag_user_favorites'] = $playlists['favorites'];
        }

		return $instance;
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title' => 'Videos',

			// Feed options
			'ytchag_key' => 'AIzaSyA0IBAaDqJxfQiqeYg_i2kVKW5P9ZLheVU',
			'ytchag_feed' => 'user',
			'ytchag_user' => 'youtube',
			'ytchag_id' => 'UUBR8-60-B28hp2BmDPdntcQ',
			'ytchag_user_uploads' => 'UUBR8-60-B28hp2BmDPdntcQ',
			'ytchag_user_favorites' => '',
			'ytchag_feed_order' => 'asc',
			'ytchag_cache_time' => '24',
			'ytchag_cache' => '',

			// Player options
			'ytchag_player' => '1',
			'ytchag_width_value' => '100',
			'ytchag_width_type' => '%',
			'ytchag_ratio' => '16x9',
			'ytchag_theme' => 'dark',
			'ytchag_color' => 'red',
			'ytchag_quality' => 'default',
			'ytchag_autoplay' => '',
			'ytchag_modestbranding' => '',
			'ytchag_rel' => '',
			'ytchag_showinfo' => '',
			'ytchag_player_order' => '1',

            // Search options
			'ytchag_search_input' => '',
			'ytchag_search_playlists' => '',
			'ytchag_search_restrict' => '',
			'ytchag_search_input_show' => '',
			'ytchag_search_playlists_show' => '',
			'ytchag_search_order' => '2',

			// Thumbnail options
			'ytchag_maxitems' => '8',
			'ytchag_thumb_width' => '480',
			'ytchag_thumb_ratio' => '16x9',
			'ytchag_thumb_columns_phones' => '2',
			'ytchag_thumb_columns_tablets' => '2',
			'ytchag_thumb_columns_md' => '2',
			'ytchag_thumb_columns_ld' => '2',
			'ytchag_nofollow' => '',
			'ytchag_thumb_window' => '',
			'ytchag_pagination_show' => '0',
			'ytchag_thumb_order_thumb' => '1',
			'ytchag_thumb_order_title' => '2',
			'ytchag_thumb_order_desc' => '3',
			'ytchag_thumb_order' => '3',

			'ytchag_title' => '',
			'ytchag_description' => '',
			'ytchag_thumbnail_alignment' => 'none',
			'ytchag_title_tag' => 'h5',
			'ytchag_description_words_number' => '',

			// Link options
			'ytchag_link' => '1',
			'ytchag_link_tx' => '',
			'ytchag_link_window' => '',
			'ytchag_link_order' => '4',

            'ytchag_promotion' => '1',


		);

		// any options not set get the default
		$instance = wp_parse_args( $instance, $defaults );
		// extract them for cleaner code
		extract( $instance, EXTR_SKIP );

        include('templates/admin_tabs.php');
	}


	/*--------------------------------------------------*/
	/* Private Functions
		/*--------------------------------------------------*/

    private function ytchag_json_markup($instance) {
		//$instance variables
		//--------------------------------

		// Feed options
		$ytchag_key = apply_filters( 'ytchag_key', $instance['ytchag_key'] );
		$ytchag_feed = apply_filters( 'ytchag_feed', $instance['ytchag_feed'] );
		$ytchag_user = apply_filters( 'ytchag_user', $instance['ytchag_user'] );
		$ytchag_id = apply_filters( 'ytchag_id', $instance['ytchag_id'] );
		$ytchag_user_uploads = apply_filters( 'ytchag_user_uploads', $instance['ytchag_user_uploads'] );
		$ytchag_user_favorites = apply_filters( 'ytchag_user_favorites', $instance['ytchag_user_favorites'] );
		$ytchag_feed_order = apply_filters( 'ytchag_feed_order', $instance['ytchag_feed_order'] );
		$ytchag_cache_time = (int) apply_filters( 'ytchag_cache_time', $instance['ytchag_cache_time'] );
		$ytchag_cache = apply_filters( 'ytchag_cache', $instance['ytchag_cache'] );

		// Player options
		$ytchag_player = apply_filters( 'ytchag_player', $instance['ytchag_player'] );
		$ytchag_width_value = apply_filters( 'ytchag_width_value', $instance['ytchag_width_value'] );
		$ytchag_width_type = apply_filters( 'ytchag_width_type', $instance['ytchag_width_type'] );
		$ytchag_ratio = apply_filters( 'ytchag_ratio', $instance['ytchag_ratio'] );
		$ytchag_theme = apply_filters( 'ytchag_theme', $instance['ytchag_theme'] );
		$ytchag_color = apply_filters( 'ytchag_color', $instance['ytchag_color'] );
		$ytchag_quality = apply_filters( 'ytchag_quality', $instance['ytchag_quality'] );
		$ytchag_autoplay = apply_filters( 'ytchag_autoplay', $instance['ytchag_autoplay'] );
		$ytchag_modestbranding = apply_filters( 'ytchag_modestbranding', $instance['ytchag_modestbranding'] );
		$ytchag_rel = apply_filters( 'ytchag_rel', $instance['ytchag_rel'] );
		$ytchag_showinfo = apply_filters( 'ytchag_showinfo', $instance['ytchag_showinfo'] );
		$ytchag_player_order = apply_filters( 'ytchag_player_order', $instance['ytchag_player_order'] );

        // Search options
		$ytchag_search_input = apply_filters( 'ytchag_search_input', $instance['ytchag_search_input'] );
		$ytchag_search_playlists = apply_filters( 'ytchag_search_playlists', $instance['ytchag_search_playlists'] );
		$ytchag_search_restrict = apply_filters( 'ytchag_search_restrict', $instance['ytchag_search_restrict'] );
		$ytchag_search_input_show = apply_filters( 'ytchag_search_input_show', $instance['ytchag_search_input_show'] );
		$ytchag_search_playlists_show = apply_filters( 'ytchag_search_playlists_show', $instance['ytchag_search_playlists_show'] );
		$ytchag_search_order = apply_filters( 'ytchag_search_order', $instance['ytchag_search_order'] );

		// Thumbnail options
		$ytchag_maxitems = apply_filters( 'ytchag_maxitems', $instance['ytchag_maxitems'] );
		$ytchag_thumb_width = apply_filters( 'ytchag_thumb_width', $instance['ytchag_thumb_width'] );
		$ytchag_thumb_ratio = apply_filters( 'ytchag_thumb_ratio', $instance['ytchag_thumb_ratio'] );
		$ytchag_thumb_columns_phones = apply_filters( 'ytchag_thumb_columns_phones', $instance['ytchag_thumb_columns_phones'] );
		$ytchag_thumb_columns_tablets = apply_filters( 'ytchag_thumb_columns_tablets', $instance['ytchag_thumb_columns_tablets'] );
		$ytchag_thumb_columns_md = apply_filters( 'ytchag_thumb_columns_md', $instance['ytchag_thumb_columns_md'] );
		$ytchag_thumb_columns_ld = apply_filters( 'ytchag_thumb_columns_ld', $instance['ytchag_thumb_columns_ld'] );
		$ytchag_nofollow = apply_filters( 'ytchag_nofollow', $instance['ytchag_nofollow'] );
		$ytchag_thumb_window = apply_filters( 'ytchag_thumb_window', $instance['ytchag_thumb_window'] );
		$ytchag_pagination_show = apply_filters( 'ytchag_pagination_show', $instance['ytchag_pagination_show'] );
		$ytchag_thumb_order_thumb = apply_filters( 'ytchag_thumb_order_thumb', $instance['ytchag_thumb_order_thumb'] );
		$ytchag_thumb_order_title = apply_filters( 'ytchag_thumb_order_title', $instance['ytchag_thumb_order_title'] );
		$ytchag_thumb_order_desc = apply_filters( 'ytchag_thumb_order_desc', $instance['ytchag_thumb_order_desc'] );
		$ytchag_thumb_order= apply_filters( 'ytchag_thumb_order', $instance['ytchag_thumb_order'] );

		$ytchag_title = apply_filters( 'ytchag_title', $instance['ytchag_title'] );
		$ytchag_description = apply_filters( 'ytchag_description', $instance['ytchag_description'] );
		$ytchag_thumbnail_alignment = apply_filters( 'ytchag_thumbnail_alignment', $instance['ytchag_thumbnail_alignment'] );
		$ytchag_title_tag = apply_filters( 'ytchag_title_tag', $instance['ytchag_title_tag'] );
		$ytchag_description_words_number = apply_filters( 'ytchag_description_words_number', $instance['ytchag_description_words_number'] );

		// Link options
		$ytchag_link = apply_filters( 'ytchag_link', $instance['ytchag_link'] );
		$ytchag_link_tx = apply_filters( 'ytchag_link_tx', $instance['ytchag_link_tx'] );
		$ytchag_link_window = apply_filters( 'ytchag_link_window', $instance['ytchag_link_window'] );
		$ytchag_link_order = apply_filters( 'ytchag_link_order', $instance['ytchag_link_order'] );

        $ytchag_promotion = apply_filters( 'ytchag_promotion', $instance['ytchag_promotion'] );

		//--------------------------------
		//end $instance variables


		//defaults
		//--------------------------------

		// Feed options
		$ytchag_key = ( $ytchag_key) ? $ytchag_key: 'AIzaSyA6oW5D-ZlSIG-OHSBOR25TMd3YDRU7HdU'; //default user
		$ytchag_feed = ( $ytchag_feed ) ? $ytchag_feed : 'user'; //default user
		$ytchag_feed_order = ( $ytchag_feed_order ) ? $ytchag_feed_order : 'asc'; //default ascending

		// Player options
		$ytchag_player = isset( $ytchag_player ) ? $ytchag_player : '1'; //player?
		$ytchag_width_value = isset( $ytchag_width_value) ? $ytchag_width_value : '100'; // width
		$ytchag_width_type = isset( $ytchag_width_type) ? $ytchag_width_type : '%'; // width
		$ytchag_theme = ( $ytchag_theme ) ? '&theme='. $ytchag_theme : ''; //default dark
		$ytchag_color = ( $ytchag_color ) ? '&color='. $ytchag_color : ''; //default red
		$ytchag_quality = ( $ytchag_quality ) ? $ytchag_quality : 'default'; //default default
		$ytchag_autoplay = ( $ytchag_autoplay ) ? '&autoplay='. $ytchag_autoplay : ''; //default 0
		$ytchag_modestbranding = ( $ytchag_modestbranding ) ? '' : '&modestbranding='. $ytchag_modestbranding; //default 0
		$ytchag_rel = ( $ytchag_rel ) ? '&rel='. $ytchag_rel : '&rel=0'; //default 1
		$ytchag_showinfo = ( $ytchag_showinfo ) ? '&showinfo='. $ytchag_showinfo : '&showinfo=0'; //default 1
		$ytchag_player_order = isset( $ytchag_player_order ) ? $ytchag_player_order : '1'; // order

        // Search options
		$ytchag_search_input = isset( $ytchag_search_input) ? $ytchag_search_input: ''; // search
		$ytchag_search_playlists = isset( $ytchag_search_playlists) ? $ytchag_search_playlists: ''; // search playlists
		$ytchag_search_restrict = isset( $ytchag_search_restrict) ? $ytchag_search_restrict: ''; // search
		$ytchag_search_input_show = isset( $ytchag_search_input_show) ? $ytchag_search_input_show : ''; // search
		$ytchag_search_playlists_show = isset( $ytchag_search_playlists_show) ? $ytchag_search_playlists_show : ''; // search playlists
		$ytchag_search_order = isset( $ytchag_search_order) ? $ytchag_search_order: ''; // search order

		// Thumbnail options
		$ytchag_maxitems = ( $ytchag_maxitems ) ? $ytchag_maxitems : 9;
		if ( (int) $ytchag_maxitems > 50 ) {
			$ytchag_maxitems = 50;
		}
		$ytchag_thumb_width = ( $ytchag_thumb_width ) ? $ytchag_thumb_width : 85;
		$ytchag_thumb_columns_phones = ( ( $ytchag_thumb_columns_phones ) || ( $ytchag_thumb_columns_phones != 0 ) ) ? $ytchag_thumb_columns_phones : 0;
		$ytchag_thumb_columns_tablets = ( ( $ytchag_thumb_columns_tablets ) || ( $ytchag_thumb_columns_tablets != 0 ) ) ? $ytchag_thumb_columns_tablets : 0;
		$ytchag_thumb_columns_md = ( ( $ytchag_thumb_columns_md ) || ( $ytchag_thumb_columns_md != 0 ) ) ? $ytchag_thumb_columns_md : 0;
		$ytchag_thumb_columns_ld = ( ( $ytchag_thumb_columns_ld ) || ( $ytchag_thumb_columns_ld != 0 ) ) ? $ytchag_thumb_columns_ld : 0;
		$ytchag_nofollow = ( $ytchag_nofollow ) ? ' rel="nofollow"' : '';
		$ytchag_thumb_window = ( ( $ytchag_thumb_window ) && ( $ytchag_player == 0 ) ) ? 'target="_blank"' : '';

		//title and desc
		$ytchag_title = ( $ytchag_title ) ? $ytchag_title : 0;
		$ytchag_description = ( $ytchag_description ) ? $ytchag_description : 0;
		$ytchag_thumbnail_alignment = ( $ytchag_thumbnail_alignment ) ? $ytchag_thumbnail_alignment : 'none';
		$ytchag_title_tag = ( $ytchag_title_tag ) ? $ytchag_title_tag : 'h5';
		$ytchag_description_words_number = ( $ytchag_description_words_number ) ? $ytchag_description_words_number : 10;

		// Link options
		$ytchag_link = ( $ytchag_link ) ? $ytchag_link : 0;
		$ytchag_link_tx = ( $ytchag_link_tx ) ? $ytchag_link_tx : __( 'Show more videos»', 'youtube-channel-gallery' );
		$ytchag_link_window = ( $ytchag_link_window ) ? 'target="_blank"' : '';
		$ytchag_link_order = ( $ytchag_link_order) ? $ytchag_link_order : '4';

        $ytchag_promotion = ( $ytchag_promotion ) ? $ytchag_promotion : 0;
		//--------------------------------
		//end defaults

		if ( empty( $ytchag_user ) ) {
			$content= '<p class="empty">' . __( 'There is no video to show.', 'youtube-channel-gallery' ) . '</p>';

		} else {

            $api = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=';
            $resto = '&maxResults=' . $ytchag_maxitems . '&key=' . $ytchag_key;

            if ($ytchag_feed !== 'playlist') {

              $item = $this->getUserPlaylists($ytchag_user, $ytchag_key, $ytchag_cache, $ytchag_cache_time);
              $playlists = $item['contentDetails']['relatedPlaylists'];

              $ytchag_id = $item['id'];
              $ytchag_user_uploads = $playlists['uploads'];
              $ytchag_user_favorites = $playlists['favorites'];
            }

            if ($ytchag_search_playlists && $ytchag_id) {

              $ytchag_feed_url = 'https://www.googleapis.com/youtube/v3/search';
              $ytchag_feed_url .= '?part=snippet';
              $ytchag_feed_url .= '&channelId=' . $ytchag_id;
              $ytchag_feed_url .= '&maxResults=' . $ytchag_maxitems;

              if ($ytchag_search_restrict) {
                $ytchag_feed_url .= '&q=' . $ytchag_search_restrict;
              }
              else {
                $ytchag_feed_url .= '&q=' . implode(',', array_map('toTag', explode('#', $ytchag_search_playlists)));
              }

              $ytchag_feed_url .= '&type=video';
              $ytchag_feed_url .= '&key=' . $ytchag_key;
            }
            elseif ( $ytchag_feed === 'user' ) {
              $ytchag_feed_url = $api . $ytchag_user_uploads . $resto;
              $ytchag_playlist = $ytchag_user_uploads;
            }
            elseif ($ytchag_feed === 'favorites') {
              $ytchag_feed_url = $api . $ytchag_user_favorites . $resto;
              $ytchag_playlist = $ytchag_user_favorites;
            }
            elseif ($ytchag_feed === 'playlist') {
              $ytchag_feed_url = $api . $ytchag_user . $resto;
              $ytchag_playlist = $ytchag_user;
            }

            $ytchag_link_url = 'https://www.youtube.com/channel/' . $ytchag_id;

            $transientId = 'ytc-' .md5( $ytchag_feed . $ytchag_user . $ytchag_maxitems );
            $videos_result = $this->get_rss_data ( $ytchag_cache, $transientId, $ytchag_feed_url, $ytchag_cache_time );

            ob_start();

            if ($videos_result['response']['code'] != 200) {
                $content= '<div class="vmcerror">' . sprintf( __( 'Message from server: %1$s. Check in YouTube if the id <a href="%2$s" target="_blank">%3$s</a> belongs to a %4$s. To locate the id of your %4$s check the <a href="http://wordpress.org/extend/plugins/youtube-channel-gallery/faq/" target="_blank">FAQ</a> of the plugin.', 'youtube-channel-gallery' ), $response_message, $ytchag_link_url, $ytchag_user, $ytchag_feed ) . '</div>';
            }
            else {
              $json = json_decode($videos_result['body']);

              if ($json->pageInfo->totalResults > 0) {

                $ytchag_results_per_page = $json->pageInfo->resultsPerPage;
                $ytchag_total_results = $json->pageInfo->totalResults;

                if (isset($json->nextPageToken)) {
                  $ytchag_next_token = $json->nextPageToken;
                }

                if (isset($json->prevPageToken)) {
                  $ytchag_prev_token = $json->prevPageToken;
                }

                // Thumb order

                $modules = array();

                $modules[$ytchag_thumb_order_thumb . '1'] = 'thumb';
                $modules[$ytchag_thumb_order_title . '2'] = 'title';
                $modules[$ytchag_thumb_order_desc . '3'] = 'desc';

                ksort($modules);

                $thumbs = $this->getThumbs($json->items, $modules, $ytchag_thumb_width);

                $content = '';

                $youtube_url = 'https://www.youtube.com';
                $youtubeid = $thumbs[0]->id;

                if ($ytchag_player > 0) {
                  $this->register_scripts();
                }

                // Order

                $modules = array();

                $modules[$ytchag_player_order . '1'] = 'player';
                $modules[$ytchag_thumb_order . '2'] = 'thumbs';
                $modules[$ytchag_search_order . '3'] = 'search';
                $modules[$ytchag_link_order . '4'] = 'link';

                ksort($modules);

                foreach ($modules as $module) {
                  if ($module === 'player' && $ytchag_player == 1) {
                    include 'templates/player.php';
                  }
                  elseif ($module !== 'player') {
                    include 'templates/' . $module . '.php';
                  }
                }
              }
            }

            $content .= ob_get_contents();
            ob_end_clean();
        }

        return $content;
    }

    function getUserPlaylists($user, $key, $cache, $cache_time) {

      $api = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=' . $user . '&key=' . $key;

      $transientId = 'ytc-' . md5($api);

      if ($cache == 1) {

        if (false === ($json_result = get_transient($transientId))) {

          $resp = wp_remote_get($api);
          $response_code = wp_remote_retrieve_response_code($resp);
          $response_message = wp_remote_retrieve_response_message($reps);

          if ($response_code == 200) {
            set_transient($transientId, $resp, $cache_time * HOUR_IN_SECONDS);
          }
        }
      }
      else {
        $resp = wp_remote_get($api);
        delete_transient( $transientId );
      }

      $json = json_decode($resp['body'], true);

      return $json['items'][0];
    }

	function get_rss_data( $ytchag_cache, $transientId, $ytchag_rss_url, $ytchag_cache_time ) {
		//use cache
		if ( $ytchag_cache == '1' ) {

			//if cache does not exist
			if ( false === ( $videos_result = get_transient( $transientId ) ) ) {
				//get rss
				$videos_result = wp_remote_get( $ytchag_rss_url );

				$response_code = wp_remote_retrieve_response_code( $videos_result );
				$response_message = wp_remote_retrieve_response_message( $videos_result );

				if ( $response_code == 200 ) {

					set_transient( $transientId, $videos_result, $ytchag_cache_time * HOUR_IN_SECONDS );
				}
			}

			//not to use cache
		} else {
			//get rss
			$videos_result = wp_remote_get( $ytchag_rss_url );

			//delete cache
			delete_transient( $transientId );
		}

		return $videos_result;
	}

	// load css
	public function register_styles() {
		wp_register_style( 'youtube-channel-gallery', plugins_url( '/styles.css', __FILE__ ) );
		wp_enqueue_style( 'youtube-channel-gallery' );
		wp_register_style( 'jquery.magnific-popup', plugins_url( '/magnific-popup.css', __FILE__ ) );
		wp_enqueue_style( 'jquery.magnific-popup' );
	}

	// load js
	private function register_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'youtube_player_api', 'https://www.youtube.com/player_api', false, false, true );
		wp_enqueue_script( 'youtube-channel-gallery', plugins_url( '/scripts.js', __FILE__ ), false, false, true );
		wp_enqueue_script( 'jquery.magnific-popup', plugins_url( '/jquery.magnific-popup.min.js', __FILE__ ), false, false, true );

        $protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
        );

        wp_localize_script( 'youtube-channel-gallery', 'ytcAjax', $params );
	}


	public function register_admin_scripts_and_styles( $hook ) {
		if ( 'widgets.php' != $hook )
			return;
		wp_enqueue_style( 'youtube-channel-gallery', plugins_url( '/admin-styles.css', __FILE__ ) );
		wp_enqueue_script( 'youtube-channel-gallery', plugins_url( '/admin-scripts.js', __FILE__ ), false, false, true );

	}

	/*--------------------------------------------------*/
	/* Shortcode
		/*--------------------------------------------------*/

	public function YoutubeChannelGallery_Shortcode( $atts ) {

		extract( shortcode_atts( array(

            // Feed options
            'key' => 'AIzaSyA0IBAaDqJxfQiqeYg_i2kVKW5P9ZLheVU',
            'feed' => 'user',
            'user' => 'youtube',
            'id' => 'UUBR8-60-B28hp2BmDPdntcQ',
            'user_uploads' => 'UUBR8-60-B28hp2BmDPdntcQ',
            'user_favorites' => '',
            'feed_order' => 'asc',
            'cache_time' => '24',
            'cache' => '',

            // Player options
            'player' => '1',
            'width_value' => '100',
            'width_type' => '%',
            'ratio' => '16x9',
            'theme' => 'dark',
            'color' => 'red',
            'quality' => 'default',
            'autoplay' => '',
            'modestbranding' => '',
            'rel' => '',
            'showinfo' => '',
            'player_order' => '1',

            // Search options
            'search_input' => '',
            'search_playlists' => '',
            'search_restrict' => '',
            'search_input_show' => '',
            'search_playlists_show' => '',
            'search_order' => '2',

            // Thumbnail options
            'maxitems' => '8',
            'thumb_width' => '480',
            'thumb_ratio' => '16x9',
            'thumb_columns_phones' => '2',
            'thumb_columns_tablets' => '2',
            'thumb_columns_md' => '4',
            'thumb_columns_ld' => '4',
            'nofollow' => '',
            'thumb_window' => '',
            'pagination_show' => '0',
            'thumb_order_thumb' => '1',
            'thumb_order_title' => '2',
            'thumb_order_desc' => '3',
            'thumb_order' => '3',

            'title' => '',
            'description' => '',
            'thumbnail_alignment' => 'none',
            'title_tag' => 'h5',
            'description_words_number' => '',

            // Link options
            'link' => '1',
            'link_tx' => '',
            'link_window' => '',
            'link_order' => '4',

            'promotion' => '1',

		), $atts ) );

		// Feed options
		$instance['ytchag_key'] = $key;
		$instance['ytchag_feed'] = $feed;
		$instance['ytchag_user'] = $user;
		$instance['ytchag_id'] = $id;
		$instance['ytchag_user_uploads'] = $user_uploads;
		$instance['ytchag_user_favorites'] = $user_favorites;
		$instance['ytchag_feed_order'] = $feed_order;
		$instance['ytchag_cache_time'] = $cache_time;
		$instance['ytchag_cache'] = $cache;

		// Player options
		$instance['ytchag_player'] = $player;
		$instance['ytchag_width_value'] = $width_value;
		$instance['ytchag_width_type'] = $width_type;
		$instance['ytchag_ratio'] = $ratio;
		$instance['ytchag_theme'] = $theme;
		$instance['ytchag_color'] = $color;
		$instance['ytchag_quality'] = $quality;
		$instance['ytchag_autoplay'] = $autoplay;
		$instance['ytchag_modestbranding'] = $modestbranding;
		$instance['ytchag_rel'] = $rel;
		$instance['ytchag_showinfo'] = $showinfo;
		$instance['ytchag_player_order'] = $player_order;

        // Search options
		$instance['ytchag_search_input'] = $search_input;
		$instance['ytchag_search_playlists'] = $search_playlists;
		$instance['ytchag_search_restrict'] = $search_restrict;
		$instance['ytchag_search_input_show'] = $search_input_show;
		$instance['ytchag_search_playlists_show'] = $search_playlists_show;
		$instance['ytchag_search_order'] = $search_order;

		// Thumbnail options
		$instance['ytchag_maxitems'] = $maxitems;
		$instance['ytchag_thumb_width'] = $thumb_width;
		$instance['ytchag_thumb_ratio'] = $thumb_ratio;
		$instance['ytchag_thumb_columns_phones'] = $thumb_columns_phones;
		$instance['ytchag_thumb_columns_tablets'] = $thumb_columns_tablets;
		$instance['ytchag_thumb_columns_md'] = $thumb_columns_md;
		$instance['ytchag_thumb_columns_ld'] = $thumb_columns_ld;
		$instance['ytchag_nofollow'] = $nofollow;
		$instance['ytchag_thumb_window'] = $thumb_window;
		$instance['ytchag_pagination_show'] = $pagination_show;
		$instance['ytchag_thumb_order_thumb'] = $thumb_order_thumb;
		$instance['ytchag_thumb_order_title'] = $thumb_order_title;
		$instance['ytchag_thumb_order_desc'] = $thumb_order_desc;
		$instance['ytchag_thumb_order'] = $thumb_order;

		$instance['ytchag_title'] = $title;
		$instance['ytchag_description'] = $description;
		$instance['ytchag_thumbnail_alignment'] = $thumbnail_alignment;
		$instance['ytchag_title_tag'] = $title_tag;
		$instance['ytchag_description_words_number'] = $description_words_number;

		// Link options
		$instance['ytchag_link'] = $link;
		$instance['ytchag_link_tx'] = $link_tx;
		$instance['ytchag_link_window'] = $link_window;
		$instance['ytchag_link_order'] = $link_order;

		$instance['ytchag_promotion'] = $promotion;


		//return '<div class="ytcshort youtubechannelgallery ytccf">'. $this->ytchag_rss_markup( $instance ) . '</div>';
		return '<div class="ytcshort youtubechannelgallery ytccf">'. $this->ytchag_json_markup( $instance ) . '</div>';

	} // YoutubeChannelGallery_Shortcode


} // class YoutubeChannelGallery_Widget

// register YoutubeChannelGallery_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "YoutubeChannelGallery_Widget" );' ) );

  function toTag($s) {
    return 'restrict#' . str_replace(' ', '_', strtolower($s));
  }


?>