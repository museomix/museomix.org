=== Youtube Channel Gallery ===
Contributors: javitxu123
Donate link: http://poselab.com/
Tags: widget, gallery, youtube, channel, user, sidebar, video, youtube playlist, html5, iframe, Youtube channel, youtube videos, API 3
Requires at least: 2.8
Tested up to: 4.3.1
Stable tag: 2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show a YouTube video and a gallery of thumbnails for a YouTube channel.

== Description ==

`WARNING: The name of some Shortcode options has changed since version 2`

Show a YouTube video and a gallery of thumbnails for a YouTube user channel.


= Demo: =
You can see a demo of the plugin in the following URLs:

* [Widget Demo](http://poselab.com/en/youtube-channel-gallery/) | [ES](http://poselab.com/youtube-channel-gallery)
* [Demo only with thumbnails](http://poselab.com/en/youtube-channel-gallery-demo-only-with-thumbnails/) | [ES](http://poselab.com/youtube-channel-gallery-demo-solo-con-miniaturas)
* [Demo with title and description and thumbnail at top](http://poselab.com/en/youtube-channel-gallery-demo-with-the-title-description-and-thumbnail-shown-at-the-top/) | [ES](http://poselab.com/youtube-channel-gallery-demo-con-titulo-y-descripcion-y-miniatura-arriba)
* [Demo with title and thumbnail at top](http://poselab.com/en/youtube-channel-gallery-demo-with-the-title-and-thumbnail-at-the-top/) | [Demo with title and thumbnail at top](http://poselab.com/youtube-channel-gallery-demo-con-titulo-y-miniatura-arriba)
* [Demo with title and description and thumbnail on the left](http://poselab.com/en/youtube-channel-gallery-demo-with-title-description-and-thumbnail-on-the-left/) | [ES](http://poselab.com/youtube-channel-gallery-demo-con-titulo-y-descripcion-y-miniatura-a-la-izquierda)
* [Demo with title and thumbnail at bottom](http://poselab.com/en/youtube-channel-gallery-demo-with-title-and-thumbnail-at-the-bottom/) | [ES](http://poselab.com/youtube-channel-gallery-demo-con-titulo-y-miniatura-abajo)
* [Demo with playlist](http://poselab.com/en/youtube-channel-gallery-demo-with-playlist/) | [ES](http://poselab.com/youtube-channel-gallery-demo-con-lista-de-reproduccion/)


= Features: =
* Display latest thumbnail videos from YouTube user channel playlist.
* When you click on one of the thumbnails the video plays on player.
* This plugin uses the YouTube IFrame player API that allows YouTube to serve an HTML5 player, rather than a Flash player, for mobile devices that do not support Flash.
* YouTube Data API v3.
* You can choose to use this plugin as a widget or as a shortcode.
* You can use multiple instances of the plugin on the same page.


= Widget fields: =
Description of the different fields of the plugin:

* **Title:** Widget Title.

**Feed tab:**

* **Key:** You must insert your own API key. The API key inserted is an example and if you do not change the API key for yours you may receive an error message of exceeded quota. This is necessary in version 3 of the YouTube API, [Obtaining authorization credentials](https://developers.google.com/youtube/registering_an_application). Shortcode attribute: key; value: API key. (Required).

* **Video feed type:** option to select the feed type to use to show videos. You can select Uploaded by a user, User's favorites, User's likes or Playlist. Shortcode attribute: feed; value: user (default), favorites, likes or playlist. (Optional).

* **Identify by:** option to select the type you want id to use to identify your channel, your user name or id channel. Shortcode attribute: identify_by; value: username (default) or channelid. (Optional).

* **YouTube user id or playlist id:** the user id of the user's Youtube videos you want to show or the id of the playlist. Shortcode attribute: user; value: String. (Required).
* **Order:** this option appears if you select "Uploaded by a user" as Video feed type. Shortcode attribute: feed_order; value: date (default), rating, relevance, title, videoCount or viewCount. (Optional).
   *   date: Resources are sorted in reverse chronological order based on the date they were created.
   *   rating: Resources are sorted from highest to lowest rating.
   *   relevance: Resources are sorted based on their relevance to the search query. This is the default value for this parameter.
   *   title: Resources are sorted alphabetically by title.
   *   videoCount: Channels are sorted in descending order of their number of uploaded videos.
   *   viewCount: Resources are sorted from highest to lowest number of views.

* **Cache time (hours):** Hours that RSS data is saved in database, to not make a request every time the page is displayed. Assign this value according to how often you upgrade your playlist in YouTube. Shortcode attribute: cache_time; value: Number. (Optional).

* **Activate cache:** If you disable this field the cache will be deleted and will not be used. This is useful to refresh immediately the YouTube RSS used by the plugin. Reenable the cache when the gallery shows the changes you made in your YouTube account. Shortcode attribute: cache; values: 0(default) or 1 . (Optional).


**Player tab:**

* **Player:** select where you want to play the video. Shortcode attribute: player; values: 0, 1 (default) or 2. (Optional).
   *   Without player: When you click on thumbnails they will go to the YouTube video page.
   *   Show player: Youtube player will be shown and when you click on a thumbnail the video will start playing on this player.
   *   Magnific Popup: Youtube player will not be shown and when you click on a thumbnail a [modal window](http://dimsemenov.com/plugins/magnific-popup/) will appear and the video will begin playing in it.

* **Width:** Width of player. Shortcode attribute:** width_value; values: Number. 100 (default). (Optional).

* **Width dimensions:** dimension of player width. Shortcode attribute: width_type; values: % (default) or px. (Optional).

* **Aspect ratio:** indicates the proportions of the player, standard (4:3) or widescreen (16:9) format. Shortcode attribute: ratio; values: 4x3 or 16x9 (default). (Optional).

* **Theme:** display player controls (like a 'play' button or volume control) within a dark or light control bar. Shortcode attribute: theme; values: dark (default) or light. (Optional).

* **Progress bar color:** specifies the color that will be used in the player's video progress bar to highlight the amount of the video that the viewer has already seen. Shortcode attribute: color; values: red (default) or white. (Optional).

* **Video quality:** sets the suggested video quality for the videos. The suggested quality parameter value can be small, medium, large, hd720, hd1080, highres or default. YouTube recommend that you set the parameter value to default, which instructs YouTube to select the most appropriate playback quality, which will vary for different users, videos, systems and other playback conditions. If you set suggested quality level that is not available for the video, then the quality will be set to the next lowest level that is available. Shortcode attribute: quality; values: small, medium, large, hd720, hd1080, highres or default (default). (Optional).

* **Autoplay:** automatically play the initial video when the player loads. Shortcode attribute: autoplay; values: 0 (default) or 1. (Optional).

* **Show YouTube logo:** Activate this field to show the YouTube logo in the control bar. Setting the color parameter to white will show the YouTube logo in the control bar. Shortcode attribute: modestbranding; values: 0 (default) or 1. (Optional).

* **Show related videos:** this parameter indicates whether the player should show related videos when playback of the initial video ends. Shortcode attribute: rel; values: 0 (default) or 1. (Optional).

* **Show info (title, uploader):** display information like the video title and rating before the video starts playing. Shortcode attribute: showinfo; values: 0 (default) or 1. (Optional).

* **Show title:** it displays the title of the player. Shortcode attribute: player_title; values: 0 (default) or 1. (Optional).

* **Show published date:** it shows the date of the video of the player in the format set in the General Settings of Wordpress. Shortcode attribute: player_published_date; values: 0 (default) or 1. (Optional).

* **Show description:** it shows the description of the video of the player. Shortcode attribute: player_description; values: 0 (default) or 1. (Optional).

* **Title tag:** select an appropriate tag for the title of the video of the player. Shortcode attribute: player_title_tag; values: h1, h2, h3 (default), h4, h5, h6. (Optional).

* **Description words number:** the maximum number of words displayed in the description of the video of the player. Shortcode attribute: player_description_words_number; value: Number. (Optional).

* **Tab order:** order of player. Shortcode attribute: player_order; values: Number. 1 (default). (Optional).


**Search tab:**

* **Search input text:** The text to be displayed on the search input to search in the youtube account. Shortcode attribute: search_input_text; values: String. Search... (default). (Optional).

* **Show search box:** select where you want to play the video. Shortcode attribute: search_input_show; values: 0, 1 (default) or 2. (Optional).

* **Tab order:** order of Search tab. Shortcode attribute: thumb_order; values: Number. 2 (default). (Optional).


**Thumbnails tab:**

* **Number of videos to show:** it must be a number indicating the number of thumbnails to be displayed for each page. Shortcode attribute: maxitems; value: Number. (Optional).

* **Thumbnail resolution:** indicates the resolution of thumbnails Default (120x90 px), Medium (320x180), High (480x360). Shortcode attribute: thumb_width; value: Number. 320 (default). (Optional).

* **Aspect ratio:** indicates the proportions of the thumbnails, standard (4:3) or widescreen (16:9) format. Shortcode attribute: thumb_ratio; values: 4x3 or 16x9 (default). (Optional).

* **Thumbnail columns:** it allows to control the number of columns in which the thumbnails are distributed. It uses [Bootstrap Grid system](http://getbootstrap.com/css/#grid) to allow responsive behavior. Shortcode attribute: thumb_columns_phones (Phones), thumb_columns_tablets (Tablets), thumb_columns_md (Medium Desktops), thumb_columns_ld (Large Desktops); value: Number. Max value 12. (Optional).

* **Show duration:** it displays the duration of each video. Shortcode attribute: duration; values: 0 (default) or 1. (Optional).

* **Show title:** it displays the title of the thumbnail with a link to play the video in the player. Shortcode attribute: title; values: 0 (default) or 1. (Optional).

* **Show published date:** it shows the date of the video in the format set in the General Settings of Wordpress . Shortcode attribute: published_date; values: 0 (default) or 1. (Optional).

* **Show description:** it shows the description of the thumbnail with the number of specified words. Shortcode attribute: description; values: 0 (default) or 1. (Optional).

* **Title tag:** select an appropriate tag for the title. Shortcode attribute: title_tag; values: h1, h2, h3, h4, h5 (default), h6. (Optional).

* **Description words number:** the maximum number of words displayed in the description. Shortcode attribute: description_words_number; value: Number. (Optional).

* **Thumbnail alignment:** it defines the alignment of the thumbnail respect to its description and title. Shortcode attribute: thumbnail_alignment; values: none (default), left, right. (Optional).

* **Thumbnail alignment width:** this option will be displayed if the alignment Thumbnail option is set as left or right. It is used to assign a relative size to thumbnails. Shortcode attribute: thumbnail_alignment_width; values: extra_small, small, half (default), large, extra_large. (Optional).

* **Minimum size with alignment:** it is useful to only use the alignment from a selected size. For example, if you do not want to use alignment on mobile devices, so that the thumbnails are not visible too small, you can select tablets option. Shortcode attribute: thumbnail_alignment_device; values: all (default), tablets, medium, large. (Optional).

* **Add "nofollow" attribute to links:** "nofollow" attribute provides a way for webmasters to tell search engines "Don't follow this specific link". Shortcode attribute: nofollow; values: 0 (default) or 1. (Optional).

* **Open in a new window or tab:** this option only appears if you select to use the gallery without player. Thumbnails links will open in a new window or tab. Shortcode attribute: thumb_window; values: 0 (default) or 1. (Optional).

* **Show pagination:** It shows a simple pagination with Next and Previous buttons, and information of page number and total pages. Take into account the warning from google: "Please note that the value is an approximation and may not represent an exact value. In addition, the maximum value is 1,000,000". I have observed that this value does not work properly on Youtube accounts with many videos. Shortcode attribute: thumb_pagination; values: 0 or 1 (default). (Optional).

* **Thumbnail content tab order:** order of elements of Thumbnail content tab. Default order: Thumbnail, Title, Published date, Description. Shortcode attributes: thumb_order_thumb, thumb_order_title, thumb_order_publishedAt, thumb_order_desc; values: Number. (Optional).

* **Tab 0rder:** order of Thumbnails tab. Shortcode attribute: thumb_order; values: Number. 3 (default). (Optional).


**Link tab:**

* **Link text:** field to customize the text of the link to the gallery on YouTube. Shortcode attribute: link_tx; value: String. (Optional).

* **Show link to channel:** option to display a link to the YouTube user channel. Shortcode attribute: link; values: 0 (default) or 1. (Optional).

* **Open in a new window or tab:** option to open the link to YouTube in a new window or tab . Shortcode attribute: link_window; values: 0 (default) or 1. (Optional).

* **Show link to thank the developer:** option to add a small link to home page of the developer . Shortcode attribute: promotion; values: 0 or 1 (default). (Optional).

* **Tab 0rder:** order of Link tab. Shortcode attribute: link_order; values: Number. 4 (default). (Optional).


= Shortcode syntax: =
Example of shortcode use, remember to change your_Google_API_key for your own. Look at the [help](http://poselab.com/en/youtube-channel-gallery-help):

`[Youtube_Channel_Gallery user="googledevelopers" key="your_Google_API_key" maxitems="16" thumb_columns_tablets="4" title="1"]`


= Languages: =
* Brazilian portuguese (pt_BR) - [lojainterativa.com](http://www.lojainterativa.com).
* Italian (it_IT) - [Marco Milesi](https://profiles.wordpress.org/milmor).
* Norwegian bokmål (nb_NO) - Harald Fjogstad
* Serbo-Croatian (sr_RS) - [WebHostingGeeks.com](http://webhostinggeeks.com)
* Spanish (es_ES) - [PoseLab](http://poselab.com/)

If you have created your own language pack, or have an update of an existing one, you can [send me](mailto:javier@poselab.com) your gettext PO and MO so that I can bundle it into the Youtube Channel Gallery.


== Installation ==

1. Upload the *.zip copy of this plugin into your WordPress through your 'Plugin' admin page.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place the widget in your desired sidebar through the "widgets" admin page.


== Frequently Asked Questions ==

= Where is the “widgets” admin page? =

The “widgets” admin page is found in the administrator part (wp-admin) of your WordPress site. Go to Appearance > Widgets.

= How do I find the YouTube user id? =

To find your channel's user ID and channel ID, sign in to YouTube and check your [advanced account settings](https://www.youtube.com/account) page.

= How do I get a Google API key? =

You can find how to get your Google API key and use it in the plugin in the [video tutorials](http://poselab.com/en/youtube-channel-gallery-help).

= How do I find a YouTube playlist id? =

If you go to a playlist you will get the following url format: https://www.youtube.com/playlist?list=PL33942589618ABDE3. The playlist id is what you have after list=. In this example, the playlist id is PL33942589618ABDE3.

= Thumbnails links go to the YouTube page instead of playing the video in the player? =

If another plugin or your theme throws a javascript error before Youtube Channel Gallery has been executed, it will prevent Youtube Channel Gallery JavaScript from functioning properly, so thumbnails links will go to the YouTube page instead of playing the video in the player.

= Are you using a CDN, such as Cloudflare, MaxCDN,..., a cache plugin, sucha as WP Super Cache, W3 Total Cache,... or minification plugin, and the plugin is not working correctly?  =

You have to purge or delete de cache of the CDN, plugin chache or minification plugin. Maybe, your website is using an old version of CSS or JS.


== Screenshots ==

1. Youtube Channel Gallery widget: Feed and Player tabs.
2. Youtube Channel Gallery widget: Thumbnails and Link tabs.
3. Youtube Channel Gallery example.
4. Youtube Channel Gallery example.
5. Youtube Channel Gallery example.


== Changelog ==

= 2.4 =
* Added search tab.
* Fixed thumbnail alignment not working correctly.
* Added Serbo-Croatian translation (sr_RS).
* RTL language Support in the front-end.
* Added Arab translation with "Next»" and "«Previous" strings to check RTL language support.
* Updated Magnific Popup to v1.0.0 - 2015-09-17.
* Added Text Domain in the plugin header to be imported into the translate.wordpress.org translation system.
* Fields for change text of pagination.
* Improvements in CSS.


= 2.3.2 =
* Deleted file_get_contents function for avoid error in some hostings.
* Added Norwegian bokmål (nb_NO).

= 2.3.2 =
* Stop other videos if there is more than one instance of the plugin in the same page.
* Corrected problem with pagination when there are multiple instances in the same page.
* Added option to show video duration in thumbnails.
* Added options to show title, description and published date below the player.
* Added option to select description words number in player description.
* Option to select title tag of player.
* Added option to show published date in thumbnail content.
* Added option to order published date.
* Convert plain text URI to HTML links in description text.
* Added Customizer support.
* Correction for shortcodes inside pre tags.
* Error control in thumbnail alignment.
* Error control in max columns.
* Help as jquery tooltips.
* Some little optimizations.
* Error message optimization.
* Deleted default Google API Key because quota exceded and added link to Googl Developers Console and hto help.
* Updated spanish translation and pot file.

= 2.2.2 =
* Error message optimization to improve users' debug.
* Changes in Spanish translation.

= 2.2.1 =
* Added option to use channel id or username to identify your Youtube channel.

= 2.2 =
* Optimization of cache use.
* Fixed Playlists order option and added the options allowed by YouTube api 3: Date Order, Rating Order, Relevance Order, Title Order, View Count Order.
* Fixed thumbnail link in pages with multiple instances of the plugin.
* Added pagination of thumbnails. Take into account YouTube API does not correctly calculate the total number of videos in channels with a lot of videos.
* Added new video feed types: user's favorites and user's likes.
* JavaScript optimization.
* Link optimization.
* Error message optimization.
* Rows optimization: solution for column numbers that are not multiples between them.
* Added support for grids of 5, 7, 8, 9, 10 and 11.
* Improved interface of widget.
* id in top of styles.css for bigger precedence.
* Redesign system of aligned thumbnails to right or left with a relative thumbnail size to make it responsive and selection of minimum size of alignment.
* Added field and shortcode attribute thumbnail_alignment_width.
* Added field and shortcode attribute thumbnail_alignment_device.
* Control of private videos.

= 2.0.2 =
* Delete debug element.

= 2.0.1 =
* Change short_open_tag in thumbs.php for compatibility with php < 5.4.

= 2.0.0 =
* Update in order to make it compatible with YouTube API 3.
* Option to set player width.
* Options to set the order of elements of each tab.
* Options to improve responsive behavior based on Bootstrap grid system.
* Options to select title tag.
* Optimization of alignment behavior.
* Options to set the order of elements of thumbnails tab.
* Option to promotion.
* Rewritten code.

= 1.8.7 =
* Fixed problem with SSL.
* Changes in CSS to correct IE columns.

= 1.8.6 =
* Check accounts suspended.
* SSL support (checks if HTTPS or on Port 443). Be aware that the YouTube player is not fully compatible with SSL. See [HTTPS Support for YouTube Embeds](http://apiblog.youtube.com/2011/02/https-support-for-youtube-embeds.html)
* Changes in CSS to correct IE support.

= 1.8.5 =
* Added option to show only thumbnails without player.
* Added option to add target="_blank" to thumbnails if the option without player is selected.
* Control 50 thumbnails limit.
* Changes in CSS.
* Changes in admin CSS and JS.
* Update language file.

= 1.8.4 =
* Changes in CSS.
* Added option to add "nofollow" attribute to links.
* Improvements in the code.

= 1.8.3 =
* Fixed error with alignments without title or description.
* Changes in CSS.

= 1.8.2 =
* Improvements in widget admin JavaScript.
* Corrected misspellings in Spanish.
* Added default options to the elements of the links tab.
* Added div around iframe to allow maximum width of player adding to your the style.css: .ytcplayer-fixwidthwrapper{max-width:500px}
* Changes in CSS.

= 1.8.1 =
* Added some classes to html.
* Fixed thumbnail size error when the number of columns is equal to 0 or 1.

= 1.8 =
* Replaced SimplePie for SimpleXML to solve problems that many users have with Simplepie.
* Replaced SimplePie FeedCache for Transients API cache data to manage cache of RSS.
* Added a field to set the number of hours to keep the data before refreshing.
* Added a checkbox to deactivate the cache. Useful to check or flush new videos.
* Added default values.
* Changed the max-results parameter to 50 to allow the maximum display value of a single request.
* Fixed row counter with multiple instances of the plugin in the same page.
* Responsive design.
* Deleted video width field to make video player width responsive.
* Thumbnail width field is used for top and bottom alignments to obtain the most appropriate thumbnails from rss.
* Added checkbox Show YouTube logo, which if is unchecked, will prevent the YouTube logo from displaying in the control bar.
* Added contextual help to widget.
* Parameter orderby=reversedPosition of Google Data API is not working. The descending order of the playlist is created without the parameter of the api.
* Increased the width of the widget to show in a single row the tabs in Spanish.
* Changes in CSS.
* Update language file.

= 1.7.10 =
* max-results=50 parameter removed from playlists. Some users have reported plugin crashes. After check it out I found that the plugin fails with multiple playlist because of this parameter randomly.

= 1.7.9 =
* Changed the max-results parameter to 50 to allow the maximum display value of a single request. The plugin will not show more than 50 videos at least until version 2

= 1.7.8 =
* Fixed scroll to player script.
* CSS improvements.
* Moved screenshots to assets folder.

= 1.7.7 =
* Check descriptions in playlists because are in media:description.

= 1.7.6 =
* Add &wmode=transparent to the YouTube iframe url in order to allow layers to overlap it.

= 1.7.5.1 =
* Fixed order of playlists videos, now by ascending and descending position.
* Added control to select playlist order.
* Fixed for attributes of some wigdet labels.


= 1.7.5 =
* Corrected order of playlists videos.
* Enqueue of admin style only in widget page.
* admin-styles.css tweaks.
* Update widget form after drag-and-drop (WP save bug).
* Added scroll to player only if not in view.

= 1.7.4.2 =
* Corrected problem with jQuery.noConflict.
* Corrected link to title.

= 1.7.4.1 =
* Corrected bug in pages with multiple galleries.

= 1.7.4 =
* Corrected thumbnail size selection in playlists.

= 1.7.3 =
* Improved management of thumbnails.
* Corrected thumbnail size selection in playlists.

= 1.7.2 =
* Added playlist feed support.
* Added control to select video quality.
* Added control to open link to Youtube in a new window.
* Improved accessibility of video links.
* Fixed bug with 1 column.
* CSS improvements.

= 1.6.2 =
* Fixed bug with columns.
* Fixed issue with CSS.

= 1.6.1 =
* Added options to show title and description with thumbnails.
* Added new classes to better manage the final appearance (rows, columns, even, odd, number of row and column).
* Calculated width between thumbnails.

= 1.5.4 =
* Corrected error when file_get_contents() is disabled in the server configuration by allow_url_fopen=0.
* Corrected error with Show info (title, uploader) field.

= 1.5.3 =
* Added tabs to the widget interface to better organize the fields.
* Added new fields to control the player (Aspect ratio, Progress bar color, Autoplay, Show related videos, Show info).
* Added Aspect ratio field to thumbnails.
* Added Link text field to Links.
* Added class to last thumbnail of each row to delete the margin-right in CSS.
* Added class to first thumbnail of each row to clear float in CSS.
* Check that the inserted username exists.
* Changes in CSS.

= 1.4.8.1 =
* Fixed warning: Cannot modify header information...

= 1.4.8 =
* Fixed bug with shortcode position.
* Deleted decimals to thumbnail heights.
* Added background-size to CSS of thumbnails to control image size.
* Added Brazilian Portuguese (pt_BR). Thanks to Rodny.

= 1.4.7 =
* Removed parameter 'origin' from Youtube iframe Player to solve the issue that some users have on clicking the thumbnails.

= 1.4.6 =
* Tweak on CSS.
* Trying to correct issue that some users have with the origin parameter of the player.

= 1.4.5 =
* Tweaks on CSS.
* Now the CSS and JS files are loaded only on the page in which appears the plugin.
* Correction on JS, because it did not work in IE and FF.
* Code organization. More OOP.

= 1.4.2 =
* Fixed issue with CSS.

= 1.4.1 =
* Added width and height to player.
* Reverted name variable prefixes.

= 1.4 =
* Added shortcode feature.
* Multiple instances of the plugin on the same page.
* Added theme selector.
* Improved use of Iframe YouTube Player API (now synchronous).
* Added effect: hover on thumbnails to display a play button.

= 1.0 =
* Initial Release.

