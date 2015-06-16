<?php
/*
	Plugin Name: Youtube Channel Gallery
	Plugin URI: http://www.poselab.com/
	Description: Show a youtube video and a gallery of thumbnails for a youtube channel.
	Author: Javier Gómez Pose
	Author URI: http://www.poselab.com/
	Version: 1.8.7
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

		parent::__construct(
			'youtubechannelgallery_widget', // Base ID
			__( 'Youtube Channel Gallery', 'youtube-channel-gallery' ), // Name

			array( 'classname'  => 'youtubechannelgallery ytccf', 'description' => __( 'Show a youtube video and a gallery of thumbnails for a youtube channel', 'youtube-channel-gallery' ), ), // Args

			array( 'width' => 260 )
		);
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

		echo $this->ytchag_rss_markup( $instance );

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		// Feed options
		$instance['ytchag_feed'] = strip_tags( $new_instance['ytchag_feed'] );
		$instance['ytchag_user'] = strip_tags( $new_instance['ytchag_user'] );
		$instance['ytchag_feed_order'] = strip_tags( $new_instance['ytchag_feed_order'] );
		$instance['ytchag_cache_time'] = strip_tags( $new_instance['ytchag_cache_time'] );
		$instance['ytchag_cache'] = strip_tags( $new_instance['ytchag_cache'] );

		// Player options
		$instance['ytchag_player'] = strip_tags( $new_instance['ytchag_player'] );
		$instance['ytchag_ratio'] = strip_tags( $new_instance['ytchag_ratio'] );
		$instance['ytchag_theme'] = strip_tags( $new_instance['ytchag_theme'] );
		$instance['ytchag_color'] = strip_tags( $new_instance['ytchag_color'] );
		$instance['ytchag_quality'] = strip_tags( $new_instance['ytchag_quality'] );
		$instance['ytchag_autoplay'] = strip_tags( $new_instance['ytchag_autoplay'] );
		$instance['ytchag_modestbranding'] = strip_tags( $new_instance['ytchag_modestbranding'] );
		$instance['ytchag_rel'] = strip_tags( $new_instance['ytchag_rel'] );
		$instance['ytchag_showinfo'] = strip_tags( $new_instance['ytchag_showinfo'] );

		// Thumbnail options
		$instance['ytchag_maxitems'] = strip_tags( $new_instance['ytchag_maxitems'] );
		$instance['ytchag_thumb_width'] = strip_tags( $new_instance['ytchag_thumb_width'] );
		$instance['ytchag_thumb_ratio'] = strip_tags( $new_instance['ytchag_thumb_ratio'] );
		$instance['ytchag_thumb_columns'] = strip_tags( $new_instance['ytchag_thumb_columns'] );
		$instance['ytchag_nofollow'] = strip_tags( $new_instance['ytchag_nofollow'] );
		$instance['ytchag_thumb_window'] = strip_tags( $new_instance['ytchag_thumb_window'] );

		$instance['ytchag_title'] = strip_tags( $new_instance['ytchag_title'] );
		$instance['ytchag_description'] = strip_tags( $new_instance['ytchag_description'] );
		$instance['ytchag_thumbnail_alignment'] = strip_tags( $new_instance['ytchag_thumbnail_alignment'] );
		$instance['ytchag_description_words_number'] = strip_tags( $new_instance['ytchag_description_words_number'] );

		// Link options
		$instance['ytchag_link'] = $new_instance['ytchag_link'];
		$instance['ytchag_link_tx'] = strip_tags( $new_instance['ytchag_link_tx'] );
		$instance['ytchag_link_window'] = strip_tags( $new_instance['ytchag_link_window'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title' => 'Videos',

			// Feed options
			'ytchag_feed' => 'user',
			'ytchag_user' => 'youtube',
			'ytchag_feed_order' => 'asc',
			'ytchag_cache_time' => '24',
			'ytchag_cache' => '1',

			// Player options
			'ytchag_player' => '1',
			'ytchag_ratio' => '4x3',
			'ytchag_theme' => 'dark',
			'ytchag_color' => 'red',
			'ytchag_quality' => 'default',
			'ytchag_autoplay' => '',
			'ytchag_modestbranding' => '',
			'ytchag_rel' => '',
			'ytchag_showinfo' => '',

			// Thumbnail options
			'ytchag_maxitems' => '9',
			'ytchag_thumb_width' => '90',
			'ytchag_thumb_ratio' => '4x3',
			'ytchag_thumb_columns' => '3',
			'ytchag_nofollow' => '',
			'ytchag_thumb_window' => '',

			'ytchag_title' => '',
			'ytchag_description' => '',
			'ytchag_thumbnail_alignment' => 'top',
			'ytchag_description_words_number' => '',

			// Link options
			'ytchag_link' => '',
			'ytchag_link_tx' => '',
			'ytchag_link_window' => '',


		);

		// any options not set get the default
		$instance = wp_parse_args( $instance, $defaults );
		// extract them for cleaner code
		extract( $instance, EXTR_SKIP );

?>

			<div class="ytchg">
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'youtube-channel-gallery' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>

				<div id="tabs-<?php echo $this->id; ?>" class="ytchgtabs">
					<ul class="ytchgtabs-tabs">
						<li><a href=".tabs-1"><?php _e( 'Feed', 'youtube-channel-gallery' ); ?></a></li>
						<li><a href=".tabs-2"><?php _e( 'Player', 'youtube-channel-gallery' ); ?></a></li>
						<li><a href=".tabs-3"><?php _e( 'Thumbnails', 'youtube-channel-gallery' ); ?></a></li>
						<li><a href=".tabs-4"><?php _e( 'Link', 'youtube-channel-gallery' ); ?></a></li>
					</ul>


					<?php
		/*
					Feed Tab
					--------------------
					*/
?>
					<div id="tabs-<?php echo $this->id; ?>-1" class="ytchgtabs-content tabs-1">

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_feed' ); ?>"><?php _e( 'Video feed type:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_feed' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_feed' ); ?>">
								<option value="user"<?php selected( $instance['ytchag_feed'], 'user' ); ?>><?php _e( 'Uploaded by a user', 'youtube-channel-gallery' ); ?></option>
								<?php /*<option value="favorites"<?php selected( $instance['ytchag_feed'], 'favorites' ); ?>><?php _e( 'User\'s favorites', 'youtube-channel-gallery' ); ?></option>*/?>
								<option value="playlist"<?php selected( $instance['ytchag_feed'], 'playlist' ); ?>><?php _e( 'Playlist', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

						<p>
							<label class="feed_user_id_label" for="<?php echo $this->get_field_id( 'ytchag_user' ); ?>"><?php _e( 'YouTube user id:', 'youtube-channel-gallery' ); ?></label>
							<label class="feed_playlist_id_label" for="<?php echo $this->get_field_id( 'ytchag_user' ); ?>"><?php _e( 'YouTube playlist id:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_user' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_user' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_user ); ?>" />
						</p>

						<p class="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>">
							<label for="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>"><?php _e( 'Playlist order:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_feed_order' ); ?>">
								<option value="asc"<?php selected( $instance['ytchag_feed_order'], 'asc' ); ?>><?php _e( 'Ascending Order', 'youtube-channel-gallery' ); ?></option>
								<option value="desc"<?php selected( $instance['ytchag_feed_order'], 'desc' ); ?>><?php _e( 'Descending Order', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

						<p class="<?php echo $this->get_field_id( 'ytchag_cache_time' ); ?>">
							<label for="<?php echo $this->get_field_id( 'ytchag_cache_time' ); ?>"><?php _e( 'Cache time (hours):', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_cache_time' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_cache_time' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_cache_time ); ?>" />
							<span class="ytchag_info" title="<?php _e( 'Hours that RSS data is saved in database, to not make a request every time the page is displayed. Assign this value according to how often you upgrade your playlist in YouTube.', 'youtube-channel-gallery' ); ?>">?</span>
						</p>

						<p class="<?php echo $this->get_field_id( 'ytchag_cache' ); ?>">
							<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_cache'], true, true ); ?> id="<?php echo $this->get_field_id( 'ytchag_cache' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_cache' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_cache' ); ?>"><?php _e( 'Activate cache', 'youtube-channel-gallery' ); ?></label>
							<span class="ytchag_info" title="<?php _e( 'If you disable this field the cache will be deleted and will not be used. This is useful to refresh immediately the YouTube RSS used by the plugin. Reenable the cache when the gallery shows the changes you made in your youtube account.', 'youtube-channel-gallery' ); ?>">?</span>
						</p>

					</div>


					<?php
		/*
					Player Tab
					--------------------
					*/
?>
					<div id="tabs-<?php echo $this->id; ?>-2" class="ytchgtabs-content tabs-2">

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_player' ); ?>"><?php _e( 'Player:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_player' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_player' ); ?>">
								<option value="0"<?php selected( $instance['ytchag_player'], '0' ); ?>><?php _e( 'Without player', 'youtube-channel-gallery' ); ?></option>
								<option value="1"<?php selected( $instance['ytchag_player'], '1' ); ?>><?php _e( 'show player above thumbnails', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

						<span class="player_options">
							<p>
								<label for="<?php echo $this->get_field_id( 'ytchag_ratio' ); ?>"><?php _e( 'Aspect ratio:', 'youtube-channel-gallery' ); ?></label>
								<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_ratio' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_ratio' ); ?>">
									<option value="4x3"<?php selected( $instance['ytchag_ratio'], '4x3' ); ?>><?php _e( 'Standard (4x3)', 'youtube-channel-gallery' ); ?></option>
									<option value="16x9"<?php selected( $instance['ytchag_ratio'], '16x9' ); ?>><?php _e( 'Widescreen (16x9)', 'youtube-channel-gallery' ); ?></option>
								</select>
							</p>

							<p>
								<label for="<?php echo $this->get_field_id( 'ytchag_theme' ); ?>"><?php _e( 'Theme:', 'youtube-channel-gallery' ); ?></label>
								<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_theme' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_theme' ); ?>">
									<option value="dark"<?php selected( $instance['ytchag_theme'], 'dark' ); ?>><?php _e( 'Dark', 'youtube-channel-gallery' ); ?></option>
									<option value="light"<?php selected( $instance['ytchag_theme'], 'light' ); ?>><?php _e( 'Light', 'youtube-channel-gallery' ); ?></option>
								</select>
							</p>

							<p>
								<label for="<?php echo $this->get_field_id( 'ytchag_color' ); ?>"><?php _e( 'Progress bar color:', 'youtube-channel-gallery' ); ?></label>
								<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_color' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_color' ); ?>">
									<option value="red"<?php selected( $instance['ytchag_color'], 'red' ); ?>><?php _e( 'Red', 'youtube-channel-gallery' ); ?></option>
									<option value="white"<?php selected( $instance['ytchag_color'], 'white' ); ?>><?php _e( 'White', 'youtube-channel-gallery' ); ?></option>
								</select>
							</p>

							<p>
								<label for="<?php echo $this->get_field_id( 'ytchag_quality' ); ?>"><?php _e( 'Video quality:', 'youtube-channel-gallery' ); ?></label>
								<select class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_quality' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_quality' ); ?>">
									<option value="default"<?php selected( $instance['ytchag_quality'], 'default' ); ?>><?php _e( 'default', 'youtube-channel-gallery' ); ?></option>
									<option value="highres"<?php selected( $instance['ytchag_quality'], 'highres' ); ?>><?php _e( 'highres', 'youtube-channel-gallery' ); ?></option>
									<option value="hd1080"<?php selected( $instance['ytchag_quality'], 'hd1080' ); ?>><?php _e( 'hd1080', 'youtube-channel-gallery' ); ?></option>
									<option value="hd720"<?php selected( $instance['ytchag_quality'], 'hd720' ); ?>><?php _e( 'hd720', 'youtube-channel-gallery' ); ?></option>
									<option value="large"<?php selected( $instance['ytchag_quality'], 'large' ); ?>><?php _e( 'large', 'youtube-channel-gallery' ); ?></option>
									<option value="medium"<?php selected( $instance['ytchag_quality'], 'medium' ); ?>><?php _e( 'medium', 'youtube-channel-gallery' ); ?></option>
									<option value="small"<?php selected( $instance['ytchag_quality'], 'small' ); ?>><?php _e( 'small', 'youtube-channel-gallery' ); ?></option>
								</select>
								<span class="ytchag_info" title="<?php _e( 'Default value enables YouTube to select the most appropriate playback quality. If you select a quality level that is not available for the video, then the quality will be set to the next lowest level that is available.', 'youtube-channel-gallery' ); ?>">?</span>
							</p>

								<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_autoplay'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_autoplay' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_autoplay' ); ?>" />
								<label for="<?php echo $this->get_field_id( 'ytchag_autoplay' ); ?>"><?php _e( 'Autoplay', 'youtube-channel-gallery' ); ?></label>

							<br>

								<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_modestbranding'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_modestbranding' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_modestbranding' ); ?>" />
								<label for="<?php echo $this->get_field_id( 'ytchag_modestbranding' ); ?>"><?php _e( 'Show YouTube logo', 'youtube-channel-gallery' ); ?></label>
								<span class="ytchag_info" title="<?php _e( 'Activate this field to show the YouTube logo in the control bar. Setting the color parameter to white will show the YouTube logo in the control bar.', 'youtube-channel-gallery' ); ?>">?</span>

							<br>

								<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_rel'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_rel' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_rel' ); ?>" />
								<label for="<?php echo $this->get_field_id( 'ytchag_rel' ); ?>"><?php _e( 'Show related videos', 'youtube-channel-gallery' ); ?></label>
								<span class="ytchag_info" title="<?php _e( 'Activate this field to show related videos when playback of the video ends.', 'youtube-channel-gallery' ); ?>">?</span>

							<br>

								<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_showinfo'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_showinfo' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_showinfo' ); ?>" />
								<label for="<?php echo $this->get_field_id( 'ytchag_showinfo' ); ?>"><?php _e( 'Show info', 'youtube-channel-gallery' ); ?></label>
								<span class="ytchag_info" title="<?php _e( 'Activate this field to display information like the video title and uploader before the video starts playing.', 'youtube-channel-gallery' ); ?>">?</span>
						</span>

					</div>


					<?php
		/*
					Thumbnails Tab
					--------------------
					*/
?>
					<div id="tabs-<?php echo $this->id; ?>-3" class="ytchgtabs-content tabs-3">
						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_maxitems' ); ?>"><?php _e( 'Number of videos to show:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_maxitems' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_maxitems' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_maxitems ); ?>" />
							<span class="ytchag_info" title="<?php _e( 'The plugin can display a maximum of 50 videos. This limitation will change in a future release.', 'youtube-channel-gallery' ); ?>">?</span>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_thumb_width' ); ?>"><?php _e( 'Thumbnail width:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_thumb_width' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_width' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_width ); ?>" />
							<span class="ytchag_info" title="<?php _e( 'This field is used to assign the appropriate quality of thumbnail images in top and bottom alignments and to assign width to thumbnails in left and right alignments. If the quality of thumbnail images is not enough, insert a larger value. If you are unsure you can assign one of the following values​​: 120, 320, 480 or 640', 'youtube-channel-gallery' ); ?>">?</span>
						</p>

						<p>

							<label for="<?php echo $this->get_field_id( 'ytchag_thumb_ratio' ); ?>"><?php _e( 'Aspect ratio:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumb_ratio' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_ratio' ); ?>">
								<option value="4x3"<?php selected( $instance['ytchag_thumb_ratio'], '4x3' ); ?>><?php _e( 'Standard (4x3)', 'youtube-channel-gallery' ); ?></option>
								<option value="16x9"<?php selected( $instance['ytchag_thumb_ratio'], '16x9' ); ?>><?php _e( 'Widescreen (16x9)', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_thumb_columns' ); ?>"><?php _e( 'Thumbnail columns:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_columns ); ?>" />
						</p>


							<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_nofollow'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_nofollow' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_nofollow' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_nofollow' ); ?>"><?php _e( 'Add "nofollow" attribute to links', 'youtube-channel-gallery' ); ?></label>
							<span class="ytchag_info" title="<?php _e( '"nofollow" attribute provides a way for webmasters to tell search engines "Don\'t follow this specific link."', 'youtube-channel-gallery' ); ?>">?</span>

							
							<span class="thumb_window">
								</br>
								<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_thumb_window'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_thumb_window' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_window' ); ?>" />
								<label for="<?php echo $this->get_field_id( 'ytchag_thumb_window' ); ?>"><?php _e( 'Open in a new window or tab', 'youtube-channel-gallery' ); ?></label>
							</span>

						<p>
							<fieldset class="ytchg-field-tit-desc">
								<legend class="ytchg-tit-desc">
									<a href="#"><?php _e( 'Show title or description', 'youtube-channel-gallery' ); ?></a>
								</legend>

								<div class="ytchg-title-and-description ytchgtabs-content">

									<p>
										<input class="checkbox ytchg-tit" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_title'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_title' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_title' ); ?>" />
										<label for="<?php echo $this->get_field_id( 'ytchag_title' ); ?>"><?php _e( 'Show title', 'youtube-channel-gallery' ); ?></label>
									</p>

									<p>
										<input class="checkbox ytchg-desc" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_description'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_description' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_description' ); ?>" />
										<label for="<?php echo $this->get_field_id( 'ytchag_description' ); ?>"><?php _e( 'Show description', 'youtube-channel-gallery' ); ?></label>
									</p>

									<p>
										<label for="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment' ); ?>"><?php _e( 'Thumbnail alignment:', 'youtube-channel-gallery' ); ?></label>
										<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumbnail_alignment' ); ?>">
											<option value="left"<?php selected( $instance['ytchag_thumbnail_alignment'], 'left' ); ?>><?php _e( 'Left', 'youtube-channel-gallery' ); ?></option>
											<option value="right"<?php selected( $instance['ytchag_thumbnail_alignment'], 'right' ); ?>><?php _e( 'Right', 'youtube-channel-gallery' ); ?></option>
											<option value="top"<?php selected( $instance['ytchag_thumbnail_alignment'], 'top' ); ?>><?php _e( 'Top', 'youtube-channel-gallery' ); ?></option>
											<option value="bottom"<?php selected( $instance['ytchag_thumbnail_alignment'], 'bottom' ); ?>><?php _e( 'Bottom', 'youtube-channel-gallery' ); ?></option>
										</select>
									</p>

									<p>
										<label for="<?php echo $this->get_field_id( 'ytchag_description_words_number' ); ?>"><?php _e( 'Description words number:', 'youtube-channel-gallery' ); ?></label>
										<input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_description_words_number' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_description_words_number' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_description_words_number ); ?>" />
										<span class="ytchag_info" title="<?php _e( 'Set the maximum number of words that will be displayed of the description. This field is useful when the descriptions of videos in the gallery have different sizes.', 'youtube-channel-gallery' ); ?>">?</span>
									</p>
								</div>
							</fieldset>
						</p>



					</div>


					<?php
		/*
					Link Tab
					--------------------
					*/
?>
					<div id="tabs-<?php echo $this->id; ?>-4" class="ytchgtabs-content tabs-4">

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_link_tx' ); ?>"><?php _e( 'Link text:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_link_tx' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_link_tx' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_link_tx ); ?>" />
						</p>

						<p>
							<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_link'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_link' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_link' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_link' ); ?>"><?php _e( 'Show link to channel', 'youtube-channel-gallery' ); ?></label>

						</br>

							<input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_link_window'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_link_window' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_link_window' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_link_window' ); ?>"><?php _e( 'Open in a new window or tab', 'youtube-channel-gallery' ); ?></label>
						</p>

					</div>
				</div>



			</div>

			<?php
	}


	/*--------------------------------------------------*/
	/* Private Functions
		/*--------------------------------------------------*/
	private function ytchag_rss_markup( $instance ) {

		//$instance variables
		//--------------------------------

		// Feed options
		$ytchag_feed = apply_filters( 'ytchag_feed', $instance['ytchag_feed'] );
		$ytchag_user = apply_filters( 'ytchag_user', $instance['ytchag_user'] );
		$ytchag_feed_order = apply_filters( 'ytchag_feed_order', $instance['ytchag_feed_order'] );
		$ytchag_cache_time = (int) apply_filters( 'ytchag_cache_time', $instance['ytchag_cache_time'] );
		$ytchag_cache = apply_filters( 'ytchag_cache', $instance['ytchag_cache'] );

		// Player options
		$ytchag_player = apply_filters( 'ytchag_player', $instance['ytchag_player'] );
		$ytchag_ratio = apply_filters( 'ytchag_ratio', $instance['ytchag_ratio'] );
		$ytchag_theme = apply_filters( 'ytchag_theme', $instance['ytchag_theme'] );
		$ytchag_color = apply_filters( 'ytchag_color', $instance['ytchag_color'] );
		$ytchag_quality = apply_filters( 'ytchag_quality', $instance['ytchag_quality'] );
		$ytchag_autoplay = apply_filters( 'ytchag_autoplay', $instance['ytchag_autoplay'] );
		$ytchag_modestbranding = apply_filters( 'ytchag_modestbranding', $instance['ytchag_modestbranding'] );
		$ytchag_rel = apply_filters( 'ytchag_rel', $instance['ytchag_rel'] );
		$ytchag_showinfo = apply_filters( 'ytchag_showinfo', $instance['ytchag_showinfo'] );

		// Thumbnail options
		$ytchag_maxitems = apply_filters( 'ytchag_maxitems', $instance['ytchag_maxitems'] );
		$ytchag_thumb_width = apply_filters( 'ytchag_thumb_width', $instance['ytchag_thumb_width'] );
		$ytchag_thumb_ratio = apply_filters( 'ytchag_thumb_ratio', $instance['ytchag_thumb_ratio'] );
		$ytchag_thumb_columns = apply_filters( 'ytchag_thumb_columns', $instance['ytchag_thumb_columns'] );
		$ytchag_nofollow = apply_filters( 'ytchag_nofollow', $instance['ytchag_nofollow'] );
		$ytchag_thumb_window = apply_filters( 'ytchag_thumb_window', $instance['ytchag_thumb_window'] );

		$ytchag_title = apply_filters( 'ytchag_title', $instance['ytchag_title'] );
		$ytchag_description = apply_filters( 'ytchag_description', $instance['ytchag_description'] );
		$ytchag_thumbnail_alignment = apply_filters( 'ytchag_thumbnail_alignment', $instance['ytchag_thumbnail_alignment'] );
		$ytchag_description_words_number = apply_filters( 'ytchag_description_words_number', $instance['ytchag_description_words_number'] );

		// Link options
		$ytchag_link = apply_filters( 'ytchag_link', $instance['ytchag_link'] );
		$ytchag_link_tx = apply_filters( 'ytchag_link_tx', $instance['ytchag_link_tx'] );
		$ytchag_link_window = apply_filters( 'ytchag_link_window', $instance['ytchag_link_window'] );
		//--------------------------------
		//end $instance variables


		//defaults
		//--------------------------------

		// Feed options
		$ytchag_feed = ( $ytchag_feed ) ? $ytchag_feed : 'user'; //default user
		$ytchag_feed_order = ( $ytchag_feed_order ) ? $ytchag_feed_order : 'asc'; //default ascending

		// Player options
		$ytchag_player = isset( $ytchag_player ) ? $ytchag_player : '1'; //player?
		$ytchag_theme = ( $ytchag_theme ) ? '&theme='. $ytchag_theme : ''; //default dark
		$ytchag_color = ( $ytchag_color ) ? '&color='. $ytchag_color : ''; //default red
		$ytchag_quality = ( $ytchag_quality ) ? $ytchag_quality : 'default'; //default default
		$ytchag_autoplay = ( $ytchag_autoplay ) ? '&autoplay='. $ytchag_autoplay : ''; //default 0
		$ytchag_modestbranding = ( $ytchag_modestbranding ) ? '' : '&modestbranding='. $ytchag_modestbranding; //default 0
		$ytchag_rel = ( $ytchag_rel ) ? '&rel='. $ytchag_rel : '&rel=0'; //default 1
		$ytchag_showinfo = ( $ytchag_showinfo ) ? '&showinfo='. $ytchag_showinfo : '&showinfo=0'; //default 1

		// Thumbnail options
		$ytchag_maxitems = ( $ytchag_maxitems ) ? $ytchag_maxitems : 9;
		if ( (int) $ytchag_maxitems > 50 ) {
			$ytchag_maxitems = 50;
		}
		$ytchag_thumb_width = ( $ytchag_thumb_width ) ? $ytchag_thumb_width : 85;
		$ytchag_thumb_columns = ( ( $ytchag_thumb_columns ) || ( $ytchag_thumb_columns != 0 ) ) ? $ytchag_thumb_columns : 0;
		$ytchag_nofollow = ( $ytchag_nofollow ) ? ' rel="nofollow"' : '';
		$ytchag_thumb_window = ( ( $ytchag_thumb_window ) && ( $ytchag_player == 0 ) ) ? 'target="_blank"' : '';

		//title and desc
		$ytchag_title = ( $ytchag_title ) ? $ytchag_title : 0;
		$ytchag_description = ( $ytchag_description ) ? $ytchag_description : 0;
		$ytchag_thumbnail_alignment = ( $ytchag_thumbnail_alignment ) ? $ytchag_thumbnail_alignment : 'top';
		$ytchag_description_words_number = ( $ytchag_description_words_number ) ? $ytchag_description_words_number : 10;

		// Link options
		$ytchag_link = ( $ytchag_link ) ? $ytchag_link : 0;
		$ytchag_link_tx = ( $ytchag_link_tx ) ? $ytchag_link_tx : __( 'Show more videos»', 'youtube-channel-gallery' );
		$ytchag_link_window = ( $ytchag_link_window ) ? 'target="_blank"' : '';
		//--------------------------------
		//end defaults




		// YouTube feed types
		//--------------------------------

		// only if user name inserted
		if ( empty( $ytchag_user ) ) {
			$content= '<p class="empty">' . __( 'There is no video to show.', 'youtube-channel-gallery' ) . '</p>';

		} else {
			$youtube_feed_url = 'http://gdata.youtube.com/feeds/api';
			$youtube_url = 'https://www.youtube.com';

			// links
			if ( $ytchag_feed == 'user' ) {
				$ytchag_rss_url  = $youtube_feed_url . '/users/' . $ytchag_user . '/uploads?v=2&max-results='. $ytchag_maxitems;
				$ytchag_link_url  = $youtube_url . '/user/' . $ytchag_user;
			}
			if ( $ytchag_feed == 'favorites' ) {
				$ytchag_rss_url  = $youtube_feed_url . '/users/' . $ytchag_user . '/favorites';
				$ytchag_link_url  = $youtube_url . '/user/' . $ytchag_user . '/favorites';
			}
			if ( $ytchag_feed == 'playlist' ) {
				$ytchag_rss_url  = $youtube_feed_url . '/playlists/' . $ytchag_user . '?v=2&max-results=' . $ytchag_maxitems;
				$ytchag_link_url  = $youtube_url . '/playlist?list=' . $ytchag_user;
			}

			//HTTP API

			$transientId = 'ytc-' .md5( $ytchag_feed . $ytchag_user . $ytchag_maxitems );

			$videos_result = $this->get_rss_data ( $ytchag_cache, $transientId, $ytchag_rss_url, $ytchag_cache_time );
			$rss = simplexml_load_string( $videos_result['body'] );

			$response_code = wp_remote_retrieve_response_code( $videos_result );
			$response_message = wp_remote_retrieve_response_message( $videos_result );
			$entries = $rss;

			if ( $ytchag_feed == 'playlist' && $ytchag_feed_order == 'desc' ) {
				$totalResults = $rss->children( 'openSearch', true )->totalResults;

				//get rss playlist again with the last videos. YouTube does not load in the first request, even if the orderby parameter is set.

				//Youtube feed limit is 1000
				if ( $totalResults >= 1000 ) {
					$startindex = 1000 - $ytchag_maxitems + 1;
				} elseif ( $ytchag_maxitems >= $totalResults ) {
					$startindex = 1;
				} else {
					$startindex = $totalResults - $ytchag_maxitems + 1;
				}

				$ytchag_rss_url = $youtube_feed_url . '/playlists/' . $ytchag_user . '?v=2&start-index=' . $startindex . '&max-results=' . $ytchag_maxitems;// . '&orderby=reversedPosition';

				$transientId = 'ytc-' .md5( $ytchag_feed . $ytchag_user . $ytchag_feed_order . $ytchag_maxitems );

				$videos_result = $this->get_rss_data ( $ytchag_cache, $transientId, $ytchag_rss_url, $ytchag_cache_time );
				$rss = simplexml_load_string( $videos_result['body'] );

				$response_code = wp_remote_retrieve_response_code( $videos_result );
				$response_message = wp_remote_retrieve_response_message( $videos_result );

				// parameter orderby=reversedPosition of Google Data API is not working, so I will use this to reverse the order
				//get entries

				$new_rss = new stdClass();
				for ( $i = sizeof( $rss->entry ) - 1; $i >= 0; --$i ) {
					$new_rss->entry[$i] = $rss->entry[$i];
				}
				$entries = $new_rss;

			}

		}



		// content
		//--------------------------------
		if ( $response_code != 200 ) {
			$content= '<div class="vmcerror">' . sprintf( __( 'Message from server: %1$s. Check in YouTube if the id <a href="%2$s" target="_blank">%3$s</a> belongs to a %4$s. To locate the id of your %4$s check the <a href="http://wordpress.org/extend/plugins/youtube-channel-gallery/faq/" target="_blank">FAQ</a> of the plugin.', 'youtube-channel-gallery' ), $response_message, $ytchag_link_url, $ytchag_user, $ytchag_feed ) . '</div>';
		} else {

			//playlist descending order
			//get totalResults from playlist rss to order correctly videos



			$thumb_count = 0;
			$column = 0;
			static $plugincount = 0;
			$rowcount = 0;
			$namespaces=$rss->getNameSpaces( true ); // access all the namespaces used in the tree
			array_unshift( $namespaces, "" ); // add a blank at the beginning of the array to deal with the unprefixed default



			foreach ( $entries->entry as $entry ) {
				// get nodes in media: namespace for media information
				$media = $entry->children( 'http://search.yahoo.com/mrss/' );

				// get video player URL
				$url = $media->group->player->attributes();

				// get video player id
				$yt = $media->children( 'http://gdata.youtube.com/schemas/2007' );
				$youtubeid = $yt->videoid;

				// get video title
				$title = $media->group->title;

				// get video description
				$description = $media->group->description;
				
				//check if thumbnails exist (to avoid Accounts suspended)
				if(!isset($media->group->thumbnail[0])){
					continue;
				}

				//default url thumbnail
				$thumb_attrs = $media->group->thumbnail[0]->attributes();
				$thumbnail = $thumb_attrs['url'];

				$thumbs = $media->group->thumbnail;
				$thumb_attrs = array();
				$index = 0;

				// get thumbnails attributes: url | height | width
				//mqdefault: 320x180 (16:9)
				foreach ( $thumbs as $thumb ) {
					$attrstring="";
					foreach ( $namespaces as $ns ) {
						foreach ( $thumb->attributes( $ns ) as $attr => $value ) { // get all attributes, whatever namespace they might be in
							$thumb_attrs[$index][$attr] = $value;
							$attrstring.=$attr . ': ' . $thumb_attrs[$index][$attr] . "| ";
						}
					}
					$index++;
				}


				// default; w: 120; h: 90; 4x3
				// mqdefault; w: 320; h: 180; 16x9
				// hqdefault; w: 480; h: 360; 4x3
				// sddefault; w: 640; h: 480; 4x3

				// Thumbnails
				//--------------------------------

				//thumbnail height
				if ( $ytchag_thumb_ratio == '16x9' ) {
					$ytchag_thumb_height = round( ( $ytchag_thumb_width * 9 ) / 16 );
				} else {
					$ytchag_thumb_height = round( ( $ytchag_thumb_width * 3 ) / 4 );
				}

				//sort array by width
				foreach ( $thumb_attrs as $key => $row ) {
					$new_thumb_attrs[$key]  = $row['width'];
				}
				array_multisort( $new_thumb_attrs, SORT_NUMERIC, $thumb_attrs );
				unset( $new_thumb_attrs[$key] );

				// get appropriate thumbnail width
				$thumbcorrectW = $this->get_appropriate_thumbnail( $thumb_attrs, $ytchag_thumb_width, $ytchag_thumb_height, 'defaults' );
				if ( !isset( $thumbcorrectW ) ) {
					$thumbcorrectW = $this->get_appropriate_thumbnail( $thumb_attrs, $ytchag_thumb_width, $ytchag_thumb_height, 'other' );
				}

				//index in array of thumbnail width
				$thumbcorrectWIndex = $this->array_search_multi( $thumb_attrs, 'width', $thumbcorrectW );

				//appropriate url thumbnail
				$thumb = $thumbcorrectWIndex[0]['url'];


				//rows and columns control

				$column++;
				$columnlastfirst = $tableclass = $columnnumber = '';
				if ( $ytchag_thumb_columns !=0 && $column == 1 ) {
					$columnlastfirst = ' ytccell-first';
					$rowcount++;
					$row_oddeven = ( $rowcount%2==1 )?' ytc-r-odd':' ytc-r-even';
					$tableclass = ' ytc-table';
					$columnnumber = ' ytc-columns'. $ytchag_thumb_columns;

				}
				if ( $ytchag_thumb_columns !=0 && $column%$ytchag_thumb_columns == 0 ) {
					$columnlastfirst = ' ytccell-last';
				}// end columns control

				//check if title or description
				$ytchag_thumbnail_fixed_witdh = '';
				$title_and_description_alignment_class = '';


				if ( $ytchag_title || $ytchag_description ) {
					$title_and_description_alignment_class = ' ytc-td-' . $ytchag_thumbnail_alignment;

					//fixed width for columns 0 or with alignment
					if ( $ytchag_thumbnail_alignment == 'left' || $ytchag_thumbnail_alignment == 'right' ) {
						$ytchag_thumbnail_fixed_witdh = ' style="width: ' . $ytchag_thumb_width . 'px; "';
					}
				}
				//fixed width for columns 0 or with alignment
				if ( $ytchag_thumb_columns ==0 ) {
					$ytchag_thumbnail_fixed_witdh = ' style="width: ' . $ytchag_thumb_width . 'px; "';
				}



				//The content
				//--------------------------------

				//Show me the player: iframe player
				if ( $thumb_count == 0 ) {
					//count the plugin occurrences on page
					$plugincount++;

					// Load css
					$this->register_styles();

					$content = '';

					//player
					if ( $ytchag_player == '1' ) {
						require 'templates/player.php';
					}

					$content.= '<ul class="ytchagallery ytccf' . $tableclass . $title_and_description_alignment_class . $columnnumber . ' ytc-thumb' . $ytchag_thumb_ratio . '">';

				} // if player end
				$thumb_count++;



				//title and description content

				if ( $ytchag_title || $ytchag_description ) {
					$title_and_description_content= '<div class="ytctitledesc-cont">';

					if ( $ytchag_title ) {
						$title_and_description_content.= '<h5 class="ytctitle"><a class="ytclink" href="' . $youtube_url . '/watch?v=' . $youtubeid . '" data-playerid="ytcplayer' . $plugincount . '" data-quality="' . $ytchag_quality . '" alt="' . $title . '" title="' . $title . '" ' . $ytchag_nofollow . '>' . $title . '</a></h5>';
					}

					if ( $ytchag_description ) {
						$description = wp_trim_words( $description, $num_words = $ytchag_description_words_number, $more = '&hellip;' );
						$title_and_description_content.= '<div class="ytctdescription">' . $description . '</div>';
					}

					$title_and_description_content.= '</div>';
				} else {
					$title_and_description_content = '';
				}
				//end title and description content


				//----
				if ( $ytchag_thumb_columns !=0 && $column == 1 ) {
					$content.=  "\n\n" .'<div class="ytccf ytc-row ytc-r-' . $rowcount . $row_oddeven . ' ">' . "\n\n";
				}

				//$content.= '$column: ' + $column;
				$content.=  "\n\n" . '	<li class="ytccell-' . $column . $columnlastfirst . '">';//style="width: ' . $ytchag_thumb_width . 'px; "

				$content.= '<div class="ytcliinner">';

				if ( $ytchag_thumbnail_alignment == 'bottom' ) {
					$content.= $title_and_description_content;

				}

				$content.= '<div class="ytcthumb-cont"' . $ytchag_thumbnail_fixed_witdh . '>';
				$content.= '<a class="ytcthumb ytclink" ' .$ytchag_thumb_window. ' href="' . $youtube_url . '/watch?v=' . $youtubeid . '" data-playerid="ytcplayer' . $plugincount . '" data-quality="' . $ytchag_quality . '" title="' . $title . '" style="background-image:url(' . $thumb . ')" ' . $ytchag_nofollow . '>';
				$content.= '<div class="ytcplay"></div>';
				$content.= '</a>';
				$content.= '</div>';

				if ( $ytchag_thumbnail_alignment != 'bottom' ) {
					$content.= $title_and_description_content;
				}

				$content.= '</div>';

				$content.= '</li>' . "\n\n";

				//----
				if ( $ytchag_thumb_columns !=0 && $column%$ytchag_thumb_columns == 0 ) {
					$column = 0;
					$columnlastfirst = ' ytccell-last';
					$content.= '</div>' . "\n\n\n";
				}
				if ( $thumb_count == $ytchag_maxitems ) {
					break;
				}
			} //foreach end

			//if last row
			if ( $ytchag_thumb_columns !=0 && $columnlastfirst != ' ytccell-last' ) {
				$content.= '</div>' . "\n\n\n";
			}

			$content.= '</ul>';

			//link to youtube.com gallery
			if ( $ytchag_link ) {
				$content.= '<a href="' . $ytchag_link_url . '" class="ytcmore" ' . $ytchag_link_window .' ' . $ytchag_nofollow .' >' . $ytchag_link_tx . '</a>';
			}
			//--}
		}

		return $content;

	}//ytchag_rss_markup



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


	function get_appropriate_thumbnail( $thumb_attrs, $ytchag_thumb_width, $ytchag_thumb_height, $type ) {
		// get appropriate thumbnail width

		// to check only this type of thumbnails
		$ytchag_thumb_size_names = array( 'default', 'mqdefault', 'hqdefault', 'sddefault' );

		foreach ( $thumb_attrs as $row ) {

			if ( $type == 'defaults' ) {
				if ( in_array( $row['name'], $ytchag_thumb_size_names ) ) {
					if ( $row['width'] >= $ytchag_thumb_width && $row['height'] >= $ytchag_thumb_height ) {
						return $row['width'];
					}
				}
			} else {
				if ( !in_array( $row['name'], $ytchag_thumb_size_names ) ) {
					if ( $row['width'] >= $ytchag_thumb_width && $row['height'] >= $ytchag_thumb_height ) {
						return $row['width'];
					}
				}
			}
		}
	}

	function array_search_multi( $array, $key, $value ) {
		$results = array();

		if ( is_array( $array ) ) {
			if ( isset( $array[$key] ) && $array[$key] == $value )
				$results[] = $array;

			foreach ( $array as $subarray )
				$results = array_merge( $results, $this->array_search_multi( $subarray, $key, $value ) );
		}

		return $results;
	}

	// load css
	private function register_styles() {
		wp_enqueue_style( 'youtube-channel-gallery', plugins_url( '/styles.css', __FILE__ ), false, false, 'all' );
	}

	// load js
	private function register_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'youtube_player_api', 'https://www.youtube.com/player_api', false, false, true );
		wp_enqueue_script( 'youtube-channel-gallery', plugins_url( '/scripts.js', __FILE__ ), false, false, true );
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
					'user' => 'youtube',

					// Feed options
					'feed' => 'user',
					'feedorder' => 'asc',
					'cache_time' => '24',
					'cache' => '1',

					// Player options
					'player' => '1',
					'ratio' => '4x3',
					'theme' => 'dark',
					'color' => 'red',
					'quality' => 'default',
					'autoplay' => '',
					'modestbranding' => '',
					'rel' => '',
					'showinfo' => '',

					// Thumbnail options
					'maxitems' => '9',
					'thumbwidth' => '90',
					'thumbratio' => '4x3',
					'thumbcolumns' => '3',
					'nofollow' => '',
					'thumb_window' => '',

					'title' => '',
					'description' => '',
					'thumbnail_alignment' => 'top',
					'descriptionwordsnumber' => '',

					// Link options
					'link' => '',
					'link_tx' => '',
					'link_window' => ''

				), $atts ) );

		// Feed options
		$instance['ytchag_feed'] = $feed;
		$instance['ytchag_user'] = $user;
		$instance['ytchag_feed_order'] = $feedorder;
		$instance['ytchag_cache_time'] = $cache_time;
		$instance['ytchag_cache'] = $cache;

		// Player options
		$instance['ytchag_player'] = $player;
		$instance['ytchag_ratio'] = $ratio;
		$instance['ytchag_theme'] = $theme;
		$instance['ytchag_color'] = $color;
		$instance['ytchag_quality'] = $quality;
		$instance['ytchag_autoplay'] = $autoplay;
		$instance['ytchag_modestbranding'] = $modestbranding;
		$instance['ytchag_rel'] = $rel;
		$instance['ytchag_showinfo'] = $showinfo;

		// Thumbnail options
		$instance['ytchag_maxitems'] = $maxitems;
		$instance['ytchag_thumb_width'] = $thumbwidth;
		$instance['ytchag_thumb_ratio'] = $thumbratio;
		$instance['ytchag_thumb_columns'] = $thumbcolumns;
		$instance['ytchag_nofollow'] = $nofollow;
		$instance['ytchag_thumb_window'] = $thumb_window;

		$instance['ytchag_title'] = $title;
		$instance['ytchag_description'] = $description;
		$instance['ytchag_thumbnail_alignment'] = $thumbnail_alignment;
		$instance['ytchag_description_words_number'] = $descriptionwordsnumber;

		// Link options
		$instance['ytchag_link'] = $link;
		$instance['ytchag_link_tx'] = $link_tx;
		$instance['ytchag_link_window'] = $link_window;


		return '<div class="ytcshort youtubechannelgallery ytccf">'. $this->ytchag_rss_markup( $instance ) . '</div>';

	} // YoutubeChannelGallery_Shortcode


} // class YoutubeChannelGallery_Widget

// register YoutubeChannelGallery_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "YoutubeChannelGallery_Widget" );' ) );

?>
