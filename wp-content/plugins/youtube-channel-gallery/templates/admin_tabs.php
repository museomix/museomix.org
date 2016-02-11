<div class="ytchg">
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'youtube-channel-gallery' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>

    <div id="tabs-<?php echo $this->id; ?>" class="ytchgtabs">
        <ul class="ytchgtabs-tabs">
            <li><a href=".tabs-1"><?php _e( 'Feed', 'youtube-channel-gallery' ); ?></a></li>
            <li><a href=".tabs-2"><?php _e( 'Player', 'youtube-channel-gallery' ); ?></a></li>
            <li style="<?php echo ($instance['ytchag_feed'] == 'user' ? '' : 'display:none')?>"><a href=".tabs-3"><?php _e( 'Search', 'youtube-channel-gallery' ); ?></a></li>
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
            <div class="row">
                <div class="col-md-12">
                    <label for="<?php echo $this->get_field_id( 'ytchag_key' ); ?>"><?php _e( 'YouTube API Key:', 'youtube-channel-gallery' ); ?></label>
                    <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_key' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_key' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_key); ?>" />
                    <span class="ytchag_info" title="<?php _e( 'Get your own YouTube API Key and put here.', 'youtube-channel-gallery' ); ?>">?</span>
                    <div class="info"><?php _e( 'Get <a href="https://console.developers.google.com/" target="_blank">your Google API key</a>. See <a href="http://poselab.com/en/youtube-channel-gallery-help/" target="_blank">help</a>', 'youtube-channel-gallery' ); ?></div>
                </div>
            </div>

            <p>
                <label for="<?php echo $this->get_field_id( 'ytchag_feed' ); ?>"><?php _e( 'Video feed type:', 'youtube-channel-gallery' ); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_feed' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_feed' ); ?>">
                    <option value="user"<?php selected( $instance['ytchag_feed'], 'user' ); ?>><?php _e( 'Uploaded by a user', 'youtube-channel-gallery' ); ?></option>
                    <option value="favorites"<?php selected( $instance['ytchag_feed'], 'favorites' ); ?>><?php _e( 'User\'s favorites', 'youtube-channel-gallery' ); ?></option>
                    <option value="likes"<?php selected( $instance['ytchag_feed'], 'likes' ); ?>><?php _e( 'User\'s likes', 'youtube-channel-gallery' ); ?></option>
                    <option value="playlist"<?php selected( $instance['ytchag_feed'], 'playlist' ); ?>><?php _e( 'Playlist', 'youtube-channel-gallery' ); ?></option>
                </select>
            </p>
            <div class="row">
                <div class="col-md-4 identify_by">
                    <label for="<?php echo $this->get_field_id( 'ytchag_identify_by' ); ?>"><?php _e( 'Identify by:', 'youtube-channel-gallery' ); ?></label>
                    <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_identify_by' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_identify_by' ); ?>">
                        <option value="username"<?php selected( $instance['ytchag_identify_by'], 'username' ); ?>><?php _e( 'Username', 'youtube-channel-gallery' ); ?></option>
                        <option value="channelid"<?php selected( $instance['ytchag_identify_by'], 'channelid' ); ?>><?php _e( 'Channel ID', 'youtube-channel-gallery' ); ?></option>
                    </select>
                </div>
                <div class="col-md-8 user">
                    <label class="feed_user_id_label" for="<?php echo $this->get_field_id( 'ytchag_user' ); ?>"><?php _e( 'YouTube id:', 'youtube-channel-gallery' ); ?></label>
                    <label class="feed_playlist_id_label" for="<?php echo $this->get_field_id( 'ytchag_user' ); ?>"><?php _e( 'YouTube playlist id:', 'youtube-channel-gallery' ); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_user' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_user' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_user ); ?>" />
                </div>
            </div>

            <p class="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>"><?php _e( 'Playlist order:', 'youtube-channel-gallery' ); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_feed_order' ); ?>">
                    <option value="date"<?php selected( $instance['ytchag_feed_order'], 'date' ); ?>><?php _e( 'Date Order', 'youtube-channel-gallery' ); ?></option>
                    <option value="rating"<?php selected( $instance['ytchag_feed_order'], 'rating' ); ?>><?php _e( 'Rating Order', 'youtube-channel-gallery' ); ?></option>
                    <option value="relevance"<?php selected( $instance['ytchag_feed_order'], 'relevance' ); ?>><?php _e( 'Relevance Order', 'youtube-channel-gallery' ); ?></option>
                    <option value="title"<?php selected( $instance['ytchag_feed_order'], 'title' ); ?>><?php _e( 'Title Order', 'youtube-channel-gallery' ); ?></option>
                    <option value="videoCount"<?php selected( $instance['ytchag_feed_order'], 'videoCount' ); ?>><?php _e( 'Video Count Order', 'youtube-channel-gallery' ); ?></option>
                    <option value="viewCount"<?php selected( $instance['ytchag_feed_order'], 'viewCount' ); ?>><?php _e( 'View Count Order', 'youtube-channel-gallery' ); ?></option>
                </select>
            </p>

            <div class="row flex">
                <div class="col-md-6">
                    <div class="<?php echo $this->get_field_id( 'ytchag_cache_time' ); ?>">
                        <label for="<?php echo $this->get_field_id( 'ytchag_cache_time' ); ?>"><?php _e( 'Cache time (hours):', 'youtube-channel-gallery' ); ?></label><br>
                        <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_cache_time' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_cache_time' ); ?>" type="number" size="1" value="<?php echo esc_attr( $ytchag_cache_time ); ?>" />
                        <span class="ytchag_info" title="<?php _e( 'Hours that RSS data is saved in database, to not make a request every time the page is displayed. Assign this value according to how often you upgrade your playlist in YouTube.', 'youtube-channel-gallery' ); ?>">?</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="<?php echo $this->get_field_id( 'ytchag_cache' ); ?>">
                        &nbsp;<br>
                        <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_cache'], true, true ); ?> id="<?php echo $this->get_field_id( 'ytchag_cache' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_cache' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'ytchag_cache' ); ?>"><?php _e( 'Activate cache', 'youtube-channel-gallery' ); ?></label>
                        <span class="ytchag_info" title="<?php _e( 'If you disable this field the cache will be deleted and will not be used. This is useful to refresh immediately the YouTube RSS used by the plugin. Reenable the cache when the gallery shows the changes you made in your youtube account.', 'youtube-channel-gallery' ); ?>">?</span>
                    </div>
                </div>
            </div>
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
                    <option value="1"<?php selected( $instance['ytchag_player'], '1' ); ?>><?php _e( 'show player', 'youtube-channel-gallery' ); ?></option>
                    <option value="2"<?php selected( $instance['ytchag_player'], '2' ); ?>><?php _e( 'show player in Magnific Popup', 'youtube-channel-gallery' ); ?></option>
                </select>
            </p>

            <span class="player_options">
                <div class="row">
                    <div class="col-md-6">
                        <label for="<?php echo $this->get_field_id( 'ytchag_width_value' ); ?>"><?php _e( 'Width:', 'youtube-channel-gallery' ); ?></label><br>
                        <input class="" id="<?php echo $this->get_field_id( 'ytchag_width_value' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_width_value' ); ?>" type="number" min="1" max="9999" size="1" value="<?php echo esc_attr( $ytchag_width_value); ?>" />
                        <select class="" id="<?php echo $this->get_field_id( 'ytchag_width_type' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_width_type' ); ?>">
                            <option value="%"<?php selected( $instance['ytchag_width_type'], '%' ); ?>><?php _e( '%', 'youtube-channel-gallery' ); ?></option>
                            <option value="px"<?php selected( $instance['ytchag_width_type'], 'px' ); ?>><?php _e( 'px', 'youtube-channel-gallery' ); ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="<?php echo $this->get_field_id( 'ytchag_ratio' ); ?>"><?php _e( 'Aspect ratio:', 'youtube-channel-gallery' ); ?></label><br>
                        <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_ratio' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_ratio' ); ?>">
                            <option value="4x3"<?php selected( $instance['ytchag_ratio'], '4x3' ); ?>><?php _e( 'Standard (4x3)', 'youtube-channel-gallery' ); ?></option>
                            <option value="16x9"<?php selected( $instance['ytchag_ratio'], '16x9' ); ?>><?php _e( 'Widescreen (16x9)', 'youtube-channel-gallery' ); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="<?php echo $this->get_field_id( 'ytchag_theme' ); ?>"><?php _e( 'Theme:', 'youtube-channel-gallery' ); ?></label>
                        <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_theme' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_theme' ); ?>">
                            <option value="dark"<?php selected( $instance['ytchag_theme'], 'dark' ); ?>><?php _e( 'Dark', 'youtube-channel-gallery' ); ?></option>
                            <option value="light"<?php selected( $instance['ytchag_theme'], 'light' ); ?>><?php _e( 'Light', 'youtube-channel-gallery' ); ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="<?php echo $this->get_field_id( 'ytchag_color' ); ?>"><?php _e( 'Progress bar color:', 'youtube-channel-gallery' ); ?></label>
                        <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_color' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_color' ); ?>">
                            <option value="red"<?php selected( $instance['ytchag_color'], 'red' ); ?>><?php _e( 'Red', 'youtube-channel-gallery' ); ?></option>
                            <option value="white"<?php selected( $instance['ytchag_color'], 'white' ); ?>><?php _e( 'White', 'youtube-channel-gallery' ); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
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
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_autoplay'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_autoplay' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_autoplay' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'ytchag_autoplay' ); ?>"><?php _e( 'Autoplay', 'youtube-channel-gallery' ); ?></label>
                    </div>
                    <div class="col-md-6">
                        <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_modestbranding'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_modestbranding' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_modestbranding' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'ytchag_modestbranding' ); ?>"><?php _e( 'Show YouTube logo', 'youtube-channel-gallery' ); ?></label>
                        <span class="ytchag_info" title="<?php _e( 'Activate this field to show the YouTube logo in the control bar. Setting the color parameter to white will show the YouTube logo in the control bar.', 'youtube-channel-gallery' ); ?>">?</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_rel'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_rel' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_rel' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'ytchag_rel' ); ?>"><?php _e( 'Show related videos', 'youtube-channel-gallery' ); ?></label>
                        <span class="ytchag_info" title="<?php _e( 'Activate this field to show related videos when playback of the video ends.', 'youtube-channel-gallery' ); ?>">?</span>
                    </div>
                    <div class="col-md-6">
                        <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_showinfo'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_showinfo' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_showinfo' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'ytchag_showinfo' ); ?>"><?php _e( 'Show info', 'youtube-channel-gallery' ); ?></label>
                        <span class="ytchag_info" title="<?php _e( 'Activate this field to display information like the video title and uploader before the video starts playing.', 'youtube-channel-gallery' ); ?>">?</span>
                    </div>
                </div>


                <fieldset class="ytchg-field-tit-desc">
                    <legend class="ytchg-tit-desc">
                        <a href="#"><?php _e( 'Show additional content', 'youtube-channel-gallery' ); ?></a>
                    </legend>

                    <div class="ytchg-title-and-description ytchgtabs-content">

                        <div class="row">
                            <div class="col-md-6">
                                <input class="checkbox ytchg-tit" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_player_title'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_player_title' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_player_title' ); ?>" />
                                <label for="<?php echo $this->get_field_id( 'ytchag_player_title' ); ?>"><?php _e( 'Show title', 'youtube-channel-gallery' ); ?></label>
                            </div>
                            <div class="col-md-6">
                                <input class="checkbox ytchg-desc" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_player_publishedAt'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_player_publishedAt' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_player_publishedAt' ); ?>" />
                                <label for="<?php echo $this->get_field_id( 'ytchag_player_publishedAt' ); ?>"><?php _e( 'Show published date', 'youtube-channel-gallery' ); ?></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input class="checkbox ytchg-desc" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_player_description'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_player_description' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_player_description' ); ?>" />
                                <label for="<?php echo $this->get_field_id( 'ytchag_player_description' ); ?>"><?php _e( 'Show description', 'youtube-channel-gallery' ); ?></label>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="<?php echo $this->get_field_id( 'ytchag_player_title_tag' ); ?>"><?php _e( 'Title tag:', 'youtube-channel-gallery' ); ?></label>
                                <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_player_title_tag' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_player_title_tag' ); ?>">
                                    <option value="h1"<?php selected( $instance['ytchag_player_title_tag'], 'h1' ); ?>><?php _e( 'h1', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h2"<?php selected( $instance['ytchag_player_title_tag'], 'h2' ); ?>><?php _e( 'h2', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h3"<?php selected( $instance['ytchag_player_title_tag'], 'h3' ); ?>><?php _e( 'h3', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h4"<?php selected( $instance['ytchag_player_title_tag'], 'h4' ); ?>><?php _e( 'h4', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h5"<?php selected( $instance['ytchag_player_title_tag'], 'h5' ); ?>><?php _e( 'h5', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h6"<?php selected( $instance['ytchag_player_title_tag'], 'h6' ); ?>><?php _e( 'h6', 'youtube-channel-gallery' ); ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="<?php echo $this->get_field_id( 'ytchag_player_description_words_number' ); ?>"><?php _e( 'Description words:', 'youtube-channel-gallery' ); ?></label><br>
                                <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_player_description_words_number' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_player_description_words_number' ); ?>" type="number" min="1" max="99" size="1" value="<?php echo esc_attr( $ytchag_player_description_words_number ); ?>" />
                                <span class="ytchag_info" title="<?php _e( 'Set the maximum number of words that will be displayed of the description. This field is useful when the descriptions of videos in the gallery have different sizes.', 'youtube-channel-gallery' ); ?>">?</span>
                            </div>
                        </div>
                    </div>
                </fieldset>



                <p class="<?php echo $this->get_field_id( 'ytchag_player_order' ); ?>">
                    <label for="<?php echo $this->get_field_id( 'ytchag_player_order' ); ?>"><?php _e( 'Tab order:', 'youtube-channel-gallery' ); ?></label>
                    <input class="" id="<?php echo $this->get_field_id( 'ytchag_player_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_player_order' ); ?>" type="number" min="1" max="10" size="1" value="<?php echo esc_attr( $ytchag_player_order); ?>" />
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

            <p class="<?php echo $this->get_field_id( 'ytchag_search_input_text' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_search_input_text' ); ?>"><?php _e( 'Search input text:', 'youtube-channel-gallery' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_search_input_text' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_input_text' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_search_input_text); ?>" />
            </p>
            <p class="<?php echo $this->get_field_id( 'ytchag_search_select_options' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_search_select_options' ); ?>"><?php _e( 'Restrict search to (# separated):', 'youtube-channel-gallery' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_search_select_options' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_select_options' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_search_select_options); ?>" />
            </p>
            <p>
              <?php echo _e('You must assing this tags to your videos to restrict the search:', 'youtube-channel-gallery') ?>
              <br>
<?php  $campos = array(); ?>
<?php  if ($instance['ytchag_search_select_options']): ?>
<?php   $campos = explode('#', $instance['ytchag_search_select_options']); ?>
<?php endif; ?>
<span class="restrict"><?php echo implode(',', array_map('toTag', $campos)); ?></span>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'ytchag_search_select_default' ); ?>"><?php _e( 'Select option by default:', 'youtube-channel-gallery' ); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_search_select_default' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_select_default' ); ?>">
                    <option value=""<?php selected( $instance['ytchag_search_select_default'], '' ); ?>><?php _e( 'All', 'youtube-channel-gallery' ); ?></option>
<?php   foreach ($campos as $c): ?>
<?php    $tag = toTag($c); ?>
                    <option value="<?php echo $tag ?>"<?php selected( $instance['ytchag_search_select_default'], $tag ); ?>><?php _e( $c, 'youtube-channel-gallery' ); ?></option>
<?php   endforeach; ?>
                </select>
            </p>
                <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_search_input_show'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_search_input_show' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_input_show' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'ytchag_search_input_show' ); ?>"><?php _e( 'Show search box', 'youtube-channel-gallery' ); ?></label>

            <br>
                <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_search_select_show'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_search_select_show' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_select_show' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'ytchag_search_select_show' ); ?>"><?php _e( 'Show select to restrict', 'youtube-channel-gallery' ); ?></label>

            <br>
            <p class="<?php echo $this->get_field_id( 'ytchag_search_order' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_search_order' ); ?>"><?php _e( 'Tab order:', 'youtube-channel-gallery' ); ?></label>
                <input class="" id="<?php echo $this->get_field_id( 'ytchag_search_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_search_order' ); ?>" type="number" min="1" max="10" size="1" value="<?php echo esc_attr( $ytchag_search_order); ?>" />
            </p>
        </div>
        <?php
/*
        Thumbnails Tab
        --------------------
        */
?>
        <div id="tabs-<?php echo $this->id; ?>-4" class="ytchgtabs-content tabs-4">

            <div class="row">
                <div class="col-md-6">
                    <label for="<?php echo $this->get_field_id( 'ytchag_maxitems' ); ?>"><?php _e( 'Number of videos to show:', 'youtube-channel-gallery' ); ?></label><br>
                    <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_maxitems' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_maxitems' ); ?>" type="number" min="1" max="50" size="1" value="<?php echo esc_attr( $ytchag_maxitems ); ?>" />
                    <span class="ytchag_info" title="<?php _e( 'The plugin can display a maximum of 50 videos for each page.', 'youtube-channel-gallery' ); ?>">?</span>
                </div>
                <div class="col-md-6">
                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_width' ); ?>"><?php _e( 'Thumbnail resolution:', 'youtube-channel-gallery' ); ?></label><br>
                    <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumb_width' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_width' ); ?>">
                        <option value="120"<?php selected( $instance['ytchag_thumb_width'], '120' ); ?>><?php _e( 'Default (120x90 px)', 'youtube-channel-gallery' ); ?></option>
                        <option value="320"<?php selected( $instance['ytchag_thumb_width'], '320' ); ?>><?php _e( 'Medium (320x180)', 'youtube-channel-gallery' ); ?></option>
                        <option value="480"<?php selected( $instance['ytchag_thumb_width'], '480' ); ?>><?php _e( 'High (480x360)', 'youtube-channel-gallery' ); ?></option>
                    </select>
                </div>
            </div>

            <p>

                <label for="<?php echo $this->get_field_id( 'ytchag_thumb_ratio' ); ?>"><?php _e( 'Aspect ratio:', 'youtube-channel-gallery' ); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumb_ratio' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_ratio' ); ?>">
                    <option value="4x3"<?php selected( $instance['ytchag_thumb_ratio'], '4x3' ); ?>><?php _e( 'Standard (4x3)', 'youtube-channel-gallery' ); ?></option>
                    <option value="16x9"<?php selected( $instance['ytchag_thumb_ratio'], '16x9' ); ?>><?php _e( 'Widescreen (16x9)', 'youtube-channel-gallery' ); ?></option>
                </select>
            </p>


                <div class="row">
                    <div class="col-md-12">
                        <label for="<?php echo $this->get_field_id( 'ytchag_thumb_columns' ); ?>"><?php _e( 'Thumbnail columns:', 'youtube-channel-gallery' ); ?></label>
                    </div>
                    <div class="col-md-5">
                        <div class="table">
                            <div class="table-row">
                                <div>
                                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_columns_phones' ); ?>"><?php _e( 'Phones:', 'youtube-channel-gallery' ); ?></label>
                                </div>
                                <div>
                                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns_phones' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns_phones' ); ?>" type="number" min="1" max="12" value="<?php echo esc_attr( $ytchag_thumb_columns_phones ); ?>" /><br>
                                </div>
                            </div>
                            <div class="table-row">
                                <div>
                                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_columns_tablets' ); ?>"><?php _e( 'Tablets:', 'youtube-channel-gallery' ); ?></label>
                                </div>
                                <div>
                                    <input size="1"class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns_tablets' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns_tablets' ); ?>" type="number" min="1" max="12" value="<?php echo esc_attr( $ytchag_thumb_columns_tablets ); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="table">
                            <div class="table-row">
                                <div>
                                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_columns_md' ); ?>"><?php _e( 'Medium Desktops:', 'youtube-channel-gallery' ); ?></label>
                                </div>
                                <div>
                                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns_md' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns_md' ); ?>" type="number" min="1" max="12" value="<?php echo esc_attr( $ytchag_thumb_columns_md ); ?>" /><br>
                                </div>
                            </div>
                            <div class="table-row">
                                <div>
                                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_columns_ld' ); ?>"><?php _e( 'Large Desktops:', 'youtube-channel-gallery' ); ?></label>
                                </div>
                                <div>
                                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns_ld' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns_ld' ); ?>" type="number" min="1" max="12" value="<?php echo esc_attr( $ytchag_thumb_columns_ld ); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            <div class="row">
                <div class="col-md-5">
                    <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_duration'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_duration' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_duration' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'ytchag_duration' ); ?>"><?php _e( 'Show duration', 'youtube-channel-gallery' ); ?></label>
                </div>
                <div class="col-md-7">
                    <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_nofollow'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_nofollow' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_nofollow' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'ytchag_nofollow' ); ?>"><?php _e( 'Add "nofollow" to links', 'youtube-channel-gallery' ); ?></label>
                    <span class="ytchag_info" title="<?php _e( str_replace('"','&quot;','"nofollow" attribute provides a way for webmasters to tell search engines "Don\'t follow this specific link."'), 'youtube-channel-gallery' ); ?>">?</span>
                </div>
            </div>



                <fieldset class="ytchg-field-tit-desc">
                    <legend class="ytchg-tit-desc">
                        <a href="#"><?php _e( 'Show additional content', 'youtube-channel-gallery' ); ?></a>
                    </legend>

                    <div class="ytchg-title-and-description ytchgtabs-content">

                        <div class="row">
                            <div class="col-md-6">
                                <input class="checkbox ytchg-tit" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_title'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_title' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_title' ); ?>" />
                                <label for="<?php echo $this->get_field_id( 'ytchag_title' ); ?>"><?php _e( 'Show title', 'youtube-channel-gallery' ); ?></label>
                            </div>
                            <div class="col-md-6">
                                <input class="checkbox ytchg-desc" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_publishedAt'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_publishedAt' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_publishedAt' ); ?>" />
                                <label for="<?php echo $this->get_field_id( 'ytchag_publishedAt' ); ?>"><?php _e( 'Show published date', 'youtube-channel-gallery' ); ?></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input class="checkbox ytchg-desc" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_description'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_description' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_description' ); ?>" />
                                <label for="<?php echo $this->get_field_id( 'ytchag_description' ); ?>"><?php _e( 'Show description', 'youtube-channel-gallery' ); ?></label>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="<?php echo $this->get_field_id( 'ytchag_title_tag' ); ?>"><?php _e( 'Title tag:', 'youtube-channel-gallery' ); ?></label>
                                <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_title_tag' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_title_tag' ); ?>">
                                    <option value="h1"<?php selected( $instance['ytchag_title_tag'], 'h1' ); ?>><?php _e( 'h1', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h2"<?php selected( $instance['ytchag_title_tag'], 'h2' ); ?>><?php _e( 'h2', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h3"<?php selected( $instance['ytchag_title_tag'], 'h3' ); ?>><?php _e( 'h3', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h4"<?php selected( $instance['ytchag_title_tag'], 'h4' ); ?>><?php _e( 'h4', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h5"<?php selected( $instance['ytchag_title_tag'], 'h5' ); ?>><?php _e( 'h5', 'youtube-channel-gallery' ); ?></option>
                                    <option value="h6"<?php selected( $instance['ytchag_title_tag'], 'h6' ); ?>><?php _e( 'h6', 'youtube-channel-gallery' ); ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="<?php echo $this->get_field_id( 'ytchag_description_words_number' ); ?>"><?php _e( 'Description words:', 'youtube-channel-gallery' ); ?></label><br>
                                <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_description_words_number' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_description_words_number' ); ?>" type="number" min="1" max="99" size="1" value="<?php echo esc_attr( $ytchag_description_words_number ); ?>" />
                                <span class="ytchag_info" title="<?php _e( 'Set the maximum number of words that will be displayed of the description. This field is useful when the descriptions of videos in the gallery have different sizes.', 'youtube-channel-gallery' ); ?>">?</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment' ); ?>"><?php _e( 'Thumbnail alignment:', 'youtube-channel-gallery' ); ?></label>
                                <select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumbnail_alignment' ); ?>">
                                    <option value="none"<?php selected( $instance['ytchag_thumbnail_alignment'], 'none' ); ?>><?php _e( 'none', 'youtube-channel-gallery' ); ?></option>
                                    <option value="left"<?php selected( $instance['ytchag_thumbnail_alignment'], 'left' ); ?>><?php _e( 'Left', 'youtube-channel-gallery' ); ?></option>
                                    <option value="right"<?php selected( $instance['ytchag_thumbnail_alignment'], 'right' ); ?>><?php _e( 'Right', 'youtube-channel-gallery' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="row align-options">
                            <div class="col-md-6">
                                <label for="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment_width' ); ?>"><?php _e( 'Thumbnail width:', 'youtube-channel-gallery' ); ?></label><br>
                                <select class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment_width' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumbnail_alignment_width' ); ?>">
                                    <option value="extra_small"<?php selected( $instance['ytchag_thumbnail_alignment_width'], 'extra_small' ); ?>><?php _e( 'Extra small', 'youtube-channel-gallery' ); ?></option>
                                    <option value="small"<?php selected( $instance['ytchag_thumbnail_alignment_width'], 'small' ); ?>><?php _e( 'Small', 'youtube-channel-gallery' ); ?></option>
                                    <option value="half"<?php selected( $instance['ytchag_thumbnail_alignment_width'], 'half' ); ?>><?php _e( 'Half', 'youtube-channel-gallery' ); ?></option>
                                    <option value="large"<?php selected( $instance['ytchag_thumbnail_alignment_width'], 'large' ); ?>><?php _e( 'Large', 'youtube-channel-gallery' ); ?></option>
                                    <option value="extra_large"<?php selected( $instance['ytchag_thumbnail_alignment_width'], 'extra_large' ); ?>><?php _e( 'Extra large', 'youtube-channel-gallery' ); ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment_device' ); ?>"><?php _e( 'Min. size with alignment:', 'youtube-channel-gallery' ); ?></label><br>
                                <select class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment_device' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumbnail_alignment_device' ); ?>">
                                    <option value="all"<?php selected( $instance['ytchag_thumbnail_alignment_device'], 'all' ); ?>><?php _e( 'All', 'youtube-channel-gallery' ); ?></option>
                                    <option value="tablets"<?php selected( $instance['ytchag_thumbnail_alignment_device'], 'tablets' ); ?>><?php _e( 'Tablets', 'youtube-channel-gallery' ); ?></option>
                                    <option value="medium"<?php selected( $instance['ytchag_thumbnail_alignment_device'], 'medium' ); ?>><?php _e( 'Medium Desktops', 'youtube-channel-gallery' ); ?></option>
                                    <option value="large"<?php selected( $instance['ytchag_thumbnail_alignment_device'], 'large' ); ?>><?php _e( 'Large devices', 'youtube-channel-gallery' ); ?></option>
                                </select>
                                <span class="ytchag_info" title="<?php _e( 'Thumbnails will be aligned only from the size of selected device to prevent unwanted effects in small sizes.', 'youtube-channel-gallery' ); ?>">?</span>
                            </div>
                        </div>
                    </div>
                </fieldset>


                <div class="row thumb_window">
                    <div class="col-md-6">
                        <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_thumb_window'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_thumb_window' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_window' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'ytchag_thumb_window' ); ?>"><?php _e( 'Open in a new window', 'youtube-channel-gallery' ); ?></label>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <label><?php _e( 'Thumbnail content tab order:', 'youtube-channel-gallery' ); ?></label>
                    </div>
                    <div class="col-md-6">
                        <div class="table">
                            <div class="table-row">
                                <div>
                                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_order_thumb' ); ?>"><?php _e( 'Thumbnail:', 'youtube-channel-gallery' ); ?></label>
                                </div>
                                <div>
                                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_order_thumb' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order_thumb' ); ?>" type="number" min="1" max="3" value="<?php echo esc_attr( $ytchag_thumb_order_thumb); ?>" />
                                </div>
                            </div>
                            <div class="table-row">
                                <div>
                                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_order_title' ); ?>"><?php _e( 'Title:', 'youtube-channel-gallery' ); ?></label>
                                </div>
                                <div>
                                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_order_title' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order_title' ); ?>" type="number" min="1" max="3" value="<?php echo esc_attr( $ytchag_thumb_order_title); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table">
                            <div class="table-row">
                                <div>
                                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_order_publishedAt' ); ?>"><?php _e( 'Published date:', 'youtube-channel-gallery' ); ?></label>
                                </div>
                                <div>
                                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_order_publishedAt' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order_publishedAt' ); ?>" type="number" min="1" max="3" value="<?php echo esc_attr( $ytchag_thumb_order_publishedAt); ?>" />
                                </div>
                            </div>
                            <div class="table-row">
                                <div>
                                    <label for="<?php echo $this->get_field_id( 'ytchag_thumb_order_desc' ); ?>"><?php _e( 'Description:', 'youtube-channel-gallery' ); ?></label>
                                </div>
                                <div>
                                    <input size="1" class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_order_desc' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order_desc' ); ?>" type="number" min="1" max="3" value="<?php echo esc_attr( $ytchag_thumb_order_desc); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <input class="checkbox" type="checkbox" value="1" <?php checked( (bool) $instance['ytchag_thumb_pagination'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_thumb_pagination' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_pagination' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'ytchag_thumb_pagination' ); ?>"><?php _e( 'Show pagination', 'youtube-channel-gallery' ); ?></label>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <label for="<?php echo $this->get_field_id( 'ytchag_prev_text' ); ?>"><?php _e( 'Previous text:', 'youtube-channel-gallery' ); ?></label><br>
                        <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_prev_text' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_prev_text' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_prev_text ); ?>" />
                    </div>
                    <div class="col-md-6">
                        <label for="<?php echo $this->get_field_id( 'ytchag_next_text' ); ?>"><?php _e( 'Next text:', 'youtube-channel-gallery' ); ?></label><br>
                        <input class="widefat wideinfo" id="<?php echo $this->get_field_id( 'ytchag_next_text' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_next_text' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_next_text ); ?>" />
                    </div>
                </div>


            <p class="<?php echo $this->get_field_id( 'ytchag_thumb_order' ); ?>">
                <label for="<?php echo $this->get_field_id( 'ytchag_thumb_order' ); ?>"><?php _e( 'Tab order:', 'youtube-channel-gallery' ); ?></label>
                <input class="" id="<?php echo $this->get_field_id( 'ytchag_thumb_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_order' ); ?>" type="number" min="1" max="10" size="1" value="<?php echo esc_attr( $ytchag_thumb_order); ?>" />
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
                <label for="<?php echo $this->get_field_id( 'ytchag_link_order' ); ?>"><?php _e( 'Tab order:', 'youtube-channel-gallery' ); ?></label>
                <input class="" id="<?php echo $this->get_field_id( 'ytchag_link_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_link_order' ); ?>" type="number" min="1" max="3" size="1" value="<?php echo esc_attr( $ytchag_link_order); ?>" />
            </p>

        </div>
    </div>



</div>

