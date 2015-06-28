<div class="ytchg">
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'youtube-channel-gallery' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>

    <div id="tabs-<?php echo $this->id; ?>" class="ytchgtabs">
        <ul class="ytchgtabs-tabs">
            <li><a href=".tabs-1"><?php _e( 'Feed', 'youtube-channel-gallery' ); ?></a></li>
            <li><a href=".tabs-2"><?php _e( 'Player', 'youtube-channel-gallery' ); ?></a></li>
            <!-- <li><a href=".tabs-3"><?php _e( 'Search', 'youtube-channel-gallery' ); ?></a></li> -->
            <li><a href=".tabs-4"><?php _e( 'Thumbnails', 'youtube-channel-gallery' ); ?></a></li>
            <li><a href=".tabs-5"><?php _e( 'Link', 'youtube-channel-gallery' ); ?></a></li>
        </ul>


        <?php
/*
        Feed Tab
        --------------------
        */
?>
        <div id="tabs-<?php echo $this->id; ?>-1" class="ytchgtabs-content tabs-1">

            <p class="<?php echo $this->get_field_id( 'ytchag_key' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_key' ); ?>"><?php _e( 'YouTube API Key:', 'youtube-channel-gallery' ); ?></label>
                <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_key' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_key' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_key); ?>" />
                <span class="ytchag_info" title="<?php _e( 'Get your own YouTube API Key and put here.', 'youtube-channel-gallery' ); ?>">?</span>
            </p>

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
                [<?php echo esc_attr( $ytchag_user_uploads ); ?>]
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
                    <option value="2"<?php selected( $instance['ytchag_player'], '2' ); ?>><?php _e( 'show player in Magnific Popup', 'youtube-channel-gallery' ); ?></option>
                </select>
            </p>

            <span class="player_options">
                <p>
                    <?php _e( 'Width:', 'youtube-channel-gallery' ); ?>
                    <input class="" id="<?php echo $this->get_field_id( 'ytchag_width_value' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_width_value' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_width_value); ?>" />
                    <select class="" id="<?php echo $this->get_field_id( 'ytchag_width_type' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_width_type' ); ?>">
                        <option value="%"<?php selected( $instance['ytchag_width_type'], '%' ); ?>><?php _e( '%', 'youtube-channel-gallery' ); ?></option>
                        <option value="px"<?php selected( $instance['ytchag_width_type'], 'px' ); ?>><?php _e( 'px', 'youtube-channel-gallery' ); ?></option>
                    </select>
                </p>
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
                <p class="<?php echo $this->get_field_id( 'ytchag_player_order' ); ?>">
                    <label for="<?php echo $this->get_field_id( 'ytchag_player_order' ); ?>"><?php _e( 'Order:', 'youtube-channel-gallery' ); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_player_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_player_order' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_player_order); ?>" />
                </p>
            </span>

        </div>

        <?php
/*
        Search Tab
        --------------------
        */
?>

        <div id="tabs-<?php echo $this->id; ?>-3" class="ytchgtabs-content tabs-3">

            <p class="<?php echo $this->get_field_id( 'ytchag_search_input' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_search_input' ); ?>"><?php _e( 'Search input text:', 'youtube-channel-gallery' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_search_input' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_input' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_search_input); ?>" />
            </p>
            <p class="<?php echo $this->get_field_id( 'ytchag_search_playlists' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_search_playlists' ); ?>"><?php _e( 'Restrict search to (# separated):', 'youtube-channel-gallery' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_search_playlists' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_playlists' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_search_playlists); ?>" />
            </p>
            <p>
              <?php echo _e('You must assing this tags to your videos to restrict the search:', 'youtube-channel-gallery') ?>
              <br>
<?php  $campos = array(); ?>
<?php  if ($instance['ytchag_search_playlists']): ?>
<?php   $campos = explode('#', $instance['ytchag_search_playlists']); ?>
<?php endif; ?>
<span class="restrict"><?php echo implode(',', array_map('toTag', $campos)); ?></span>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'ytchag_search_restrict' ); ?>"><?php _e( 'Restrict search by default: It will overwrite results of feed tab)', 'youtube-channel-gallery' ); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_search_restrict' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_restrict' ); ?>">
                    <option value=""<?php selected( $instance['ytchag_search_restrict'], '' ); ?>><?php _e( 'None', 'youtube-channel-gallery' ); ?></option>
<?php   foreach ($campos as $c): ?>
<?php    $tag = toTag($c); ?>
                    <option value="<?php echo $tag ?>"<?php selected( $instance['ytchag_search_restrict'], $tag ); ?>><?php _e( $c, 'youtube-channel-gallery' ); ?></option>
<?php   endforeach; ?>
                </select>
            </p>
                <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_search_input_show'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_search_input_show' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_input_show' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'ytchag_search_input_show' ); ?>"><?php _e( 'Show search box', 'youtube-channel-gallery' ); ?></label>

            <br>
                <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_search_playlists_show'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_search_playlists_show' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_playlists_show' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'ytchag_search_playlists_show' ); ?>"><?php _e( 'Show select with Playlists', 'youtube-channel-gallery' ); ?></label>

            <br>
            <p class="<?php echo $this->get_field_id( 'ytchag_search_order' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_search_order' ); ?>"><?php _e( 'Order:', 'youtube-channel-gallery' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_search_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_order' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_search_order); ?>" />
            </p>
        </div>
        <?php
/*
        Thumbnails Tab
        --------------------
        */
?>
        <div id="tabs-<?php echo $this->id; ?>-4" class="ytchgtabs-content tabs-4">
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
                <br>
                <?php _e( 'Phones:', 'youtube-channel-gallery' ); ?>
                <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns_phones' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns_phones' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_columns_phones ); ?>" />
                <?php _e( 'Tablets:', 'youtube-channel-gallery' ); ?>
                <input size="1"class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns_tablets' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns_tablets' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_columns_tablets ); ?>" />
                <br>
                <?php _e( 'Medium Desktops:', 'youtube-channel-gallery' ); ?>
                <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns_md' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns_md' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_columns_md ); ?>" />
                <?php _e( 'Large Desktops:', 'youtube-channel-gallery' ); ?>
                <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns_ld' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns_ld' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_columns_ld ); ?>" />
            </p>



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
                            <label for="<?php echo $this->get_field_id( 'ytchag_title_tag' ); ?>"><?php _e( 'Title tag:', 'youtube-channel-gallery' ); ?></label>
                            <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_title_tag' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_title_tag' ); ?>">
                                <option value="h1"<?php selected( $instance['ytchag_title_tag'], 'h1' ); ?>><?php _e( 'h1', 'youtube-channel-gallery' ); ?></option>
                                <option value="h2"<?php selected( $instance['ytchag_title_tag'], 'h2' ); ?>><?php _e( 'h2', 'youtube-channel-gallery' ); ?></option>
                                <option value="h3"<?php selected( $instance['ytchag_title_tag'], 'h3' ); ?>><?php _e( 'h3', 'youtube-channel-gallery' ); ?></option>
                                <option value="h4"<?php selected( $instance['ytchag_title_tag'], 'h4' ); ?>><?php _e( 'h4', 'youtube-channel-gallery' ); ?></option>
                                <option value="h5"<?php selected( $instance['ytchag_title_tag'], 'h5' ); ?>><?php _e( 'h5', 'youtube-channel-gallery' ); ?></option>
                                <option value="h6"<?php selected( $instance['ytchag_title_tag'], 'h6' ); ?>><?php _e( 'h6', 'youtube-channel-gallery' ); ?></option>
                            </select>
                        </p>

                        <p>
                            <label for="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment' ); ?>"><?php _e( 'Thumbnail alignment:', 'youtube-channel-gallery' ); ?></label>
                            <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumbnail_alignment' ); ?>">
                                <option value="none"<?php selected( $instance['ytchag_thumbnail_alignment'], 'none' ); ?>><?php _e( 'none', 'youtube-channel-gallery' ); ?></option>
                                <option value="left"<?php selected( $instance['ytchag_thumbnail_alignment'], 'left' ); ?>><?php _e( 'Left', 'youtube-channel-gallery' ); ?></option>
                                <option value="right"<?php selected( $instance['ytchag_thumbnail_alignment'], 'right' ); ?>><?php _e( 'Right', 'youtube-channel-gallery' ); ?></option>
                            </select>
                        </p>

                        <p>
                            <label for="<?php echo $this->get_field_id( 'ytchag_description_words_number' ); ?>"><?php _e( 'Description words number:', 'youtube-channel-gallery' ); ?></label>
                            <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_description_words_number' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_description_words_number' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_description_words_number ); ?>" />
                            <span class="ytchag_info" title="<?php _e( 'Set the maximum number of words that will be displayed of the description. This field is useful when the descriptions of videos in the gallery have different sizes.', 'youtube-channel-gallery' ); ?>">?</span>
                        </p>
                    </div>
                </fieldset>

                <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_nofollow'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_nofollow' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_nofollow' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'ytchag_nofollow' ); ?>"><?php _e( 'Add "nofollow" attribute to links', 'youtube-channel-gallery' ); ?></label>
                <span class="ytchag_info" title="<?php _e( '"nofollow" attribute provides a way for webmasters to tell search engines "Don\'t follow this specific link."', 'youtube-channel-gallery' ); ?>">?</span>

                
                <span class="thumb_window">
                    </br>
                    <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_thumb_window'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_thumb_window' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_window' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_window' ); ?>"><?php _e( 'Open in a new window or tab', 'youtube-channel-gallery' ); ?></label>
                </span>
                <br>
<!--                 <br>
                <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_pagination_show'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_pagination_show' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_pagination_show' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'ytchag_pagination_show' ); ?>"><?php _e( 'Show pagination', 'youtube-channel-gallery' ); ?></label>
 -->                <p>
                    <label><?php _e( 'Order:', 'youtube-channel-gallery' ); ?></label>
                    <br>
                    <?php _e( 'Thumbnail:', 'youtube-channel-gallery' ); ?>
                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_order_thumb' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order_thumb' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_order_thumb); ?>" />
                    <?php _e( 'Title:', 'youtube-channel-gallery' ); ?>
                    <input size="1"class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_order_title' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order_title' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_order_title); ?>" />
                    <?php _e( 'Description:', 'youtube-channel-gallery' ); ?>
                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_order_desc' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order_desc' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_order_desc); ?>" />
                </p>
            </p>
            <p class="<?php echo $this->get_field_id( 'ytchag_thumb_order' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_thumb_order' ); ?>"><?php _e( 'Order:', 'youtube-channel-gallery' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumb_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_order); ?>" />
            </p>

        </div>


        <?php
/*
        Link Tab
        --------------------
        */
?>
        <div id="tabs-<?php echo $this->id; ?>-5" class="ytchgtabs-content tabs-5">

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

            <p>
                <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_promotion'], true, true ); ?> id="<?php echo $this->get_field_id( 'ytchag_promotion' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_promotion' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'ytchag_promotion' ); ?>"><?php _e( 'Show link to thank the developer', 'youtube-channel-gallery' ); ?></label>

            </p>

            <p class="<?php echo $this->get_field_id( 'ytchag_link_order' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_link_order' ); ?>"><?php _e( 'Order:', 'youtube-channel-gallery' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_link_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_link_order' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_link_order); ?>" />
            </p>

        </div>
    </div>



</div>

