=== Youtube Channel Gallery ===
Contributors: javitxu123
Donate link: http://poselab.com/
Tags: widget, gallery, youtube, channel, user, sidebar, video, youtube playlist, html5, iframe, Youtube channel, youtube videos, API 3
Requires at least: 2.8
Tested up to: 3.8.8
Stable tag: 2.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show a YouTube video and a gallery of thumbnails for a YouTube channel.

== Description ==

`WARNING: The name of some Shortcode options has change`

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
* [Demo with changed order](http://poselab.com/en/youtube-channel-gallery-demo-with-changed-order/) | [ES](http://poselab.com/youtube-channel-gallery-demo-con-orden-cambiado/)


= Features: =
* Display latest thumbnail videos from YouTube user channel or playlist.
* When you click on one of the thumbnails the video plays at the top.
* This plugin uses the YouTube IFrame player API that allows YouTube to serve an HTML5 player, rather than a Flash player, for mobile devices that do not support Flash.
* You can choose to use this plugin as a widget or as a shortcode.
* You can use multiple instances of the plugin on the same page.


= Widget fields: =
Description of the different fields of the plugin:

* **Title:** Widget Title.

**Feed tab:**

* **Key:** You must insert your own API key. The API key inserted is an example anf if you do not change the API key for yours you may receive an error message of exceeded quota. This is necessary in version 3 of the YouTube API, [Obtaining authorization credentials](https://developers.google.com/youtube/registering_an_application). Shortcode attribute: key; value: API key. (Required).
* **Video feed type:** option to select the feed type to use to show videos. Yo can select uploaded by the user or playlist. Shortcode attribute: feed; value: user (default) or playlist. (Optional).
* **YouTube user id/YouTube playlist id:** the user id of the user's Youtube videos you want to show or the id of the playlist. Shortcode attribute: user; value: String. (Required).
* **Playlist order:** this option appears if you selected playlist as Video feed type. You can show videos in a playlist by ascending or descending order. Shortcode attribute: feedorder; value: asc (default) or desc. (Optional).
* **Cache time (hours):** Hours that RSS data is saved in database, to not make a request every time the page is displayed. Assign this value according to how often you upgrade your playlist in YouTube. Shortcode attribute: cache_time; value: Number. (Optional).
* **Activate cache:** If you disable this field the cache will be deleted and will not be used. This is useful to refresh immediately the YouTube RSS used by the plugin. Reenable the cache when the gallery shows the changes you made in your YouTube account. Shortcode attribute: cache; values: 0 or 1 (default). (Optional).

**Player tab:**

* **Player:** Select whether you want to display the gallery with, without player or in Magnific Popup. Shortcode attribute: player; values: 0, 1 (default) or 2. (Optional).
* **Width:** Width of player. Shortcode attribute:** width_value; values: Number. 100 (default). (Optional).
* **Width dimensions:** dimensionn of player width. Shortcode attribute:** width_type; values: % (default) or px. (Optional).
* **Aspect ratio:** indicates the proportions of the player, standard (4:3) or widescreen (16:9) format. Shortcode attribute:** ratio; values: 4x3 (default) or 16x9. (Optional).
* **Theme:** display player controls (like a 'play' button or volume control) within a dark or light control bar. Shortcode attribute: theme; values: dark (default) or light. (Optional).
* **Progress bar color:** specifies the color that will be used in the player's video progress bar to highlight the amount of the video that the viewer has already seen. Shortcode attribute: color; values: red (default) or white. (Optional).
* **Video quality:** sets the suggested video quality for the videos. The suggested quality parameter value can be small, medium, large, hd720, hd1080, highres or default. YouTube recommend that you set the parameter value to default, which instructs YouTube to select the most appropriate playback quality, which will vary for different users, videos, systems and other playback conditions. If you set suggested quality level that is not available for the video, then the quality will be set to the next lowest level that is available. Shortcode attribute: quality; values: small, medium, large, hd720, hd1080, highres or default (default). (Optional).
* **Autoplay:** automatically play the initial video when the player loads. Shortcode attribute: autoplay; values: 0 (default) or 1. (Optional).
* **Show YouTube logo:** Activate this field to show the YouTube logo in the control bar. Setting the color parameter to white will show the YouTube logo in the control bar. Shortcode attribute: modestbranding; values: 0 (default) or 1. (Optional).
* **Show related videos:** this parameter indicates whether the player should show related videos when playback of the initial video ends. Shortcode attribute: rel; values: 0 (default) or 1. (Optional).
* **Show info (title, uploader):** display information like the video title and rating before the video starts playing. Shortcode attribute: showinfo; values: 0 (default) or 1. (Optional).
* **Order:** order of player. Shortcode attribute: player_order; values: Number. 1 (default). (Optional).

**Thumbnails tab:**

* **Number of videos to show:** it must be a number indicating the number of thumbnails to be displayed. Shortcode attribute: maxitems; value: Number. (Optional).
* **Thumbnail width:** indicates the width of the thumbnails. The height is automatically generated based on the aspect ratio selected. Shortcode attribute: thumb_width; value: Number. (Optional).
* **Aspect ratio:** indicates the proportions of the thumbnails, standard (4:3) or widescreen (16:9) format. Shortcode attribute: thumb_ratio; values: 4x3 (default) or 16x9. (Optional).
* **Thumbnail columns:** it allows to control the number of columns in which the thumbnails are distributed. It uses [Bootstrap Grid system](http://getbootstrap.com/css/#grid) to allow responsive behavior. Shortcode attribute: thumb_columns_phones (Phones), thumb_columns_tablets (Tablets), thumb_columns_md (Medium Desktops), thumb_columns_ld (Large Desktops); value: Number. Max value 12. (Optional).
* **Add "nofollow" attribute to links:** "nofollow" attribute provides a way for webmasters to tell search engines "Don't follow this specific link". Shortcode attribute: nofollow; values: 0 (default) or 1. (Optional).
* **Open in a new window or tab:** this option only appears if you select to use the gallery without player. Thumbnails links will open in a new window or tab. Shortcode attribute: thumb_window; values: 0 (default) or 1. (Optional).
* **Show title:** it displays the title of the thumbnail with a link to play the video in the player. Shortcode attribute: title; values: 0 (default) or 1. (Optional).
* **Show description:** it shows the description of the thumbnail with the number of specified words. Shortcode attribute: description; values: 0 (default) or 1. (Optional).
* **Title tag:** select an appropriate tag for the title. Shortcode attribute: title_tag; values: h1, h2, h3, h4, h5 (default), h6. (Optional).
* **Thumbnail alignment:** it defines the alignment of the thumbnail respect to its description and title. Shortcode attribute: thumbnail_alignment; values: none (default), left, right. (Optional).
* **Description words number:** the maximum number of words displayed in the description. Shortcode attribute: description_words_number; value: Number. (Optional).

**Link tab:**

* **Link text:** field to customize the text of the link to the gallery on YouTube. Shortcode attribute: link_tx; value: String. (Optional).
* **Show link to channel:** option to display a link to the YouTube user channel. Shortcode attribute: link; values: 0 (default) or 1. (Optional).
* **Open in a new window or tab:** option to open the link to YouTube in a new window or tab . Shortcode attribute: link_window; values: 0 (default) or 1. (Optional).
* **Show link to thank the developer:** option to add a small link to home page of the developer . Shortcode attribute: promotion; values: 0 or 1 (default). (Optional).


= Shortcode syntax: =
In the following example are many attributes that can be used with the shortcode and explained above:

`[Youtube_Channel_Gallery user="Autodesk" videowidth="580" ratio="16x9" theme="light" color="white" autoplay="0" rel="0" showinfo="0" maxitems="16" thumb_width="125" thumb_ratio="16x9" thumb_columns_ld="4" promotion="0" pagination_show="0"]`


= Languages: =
* Brazilian portuguese (pt_BR) - [lojainterativa.com](http://www.lojainterativa.com).
* Italian (it_IT) - [Marco Milesi](https://profiles.wordpress.org/milmor).
* Spanish (es_ES) - [PoseLab](http://poselab.com/)


If you have created your own language pack, or have an update of an existing one, you can [send me](mailto:javierpose@gmail.com) your gettext PO and MO so that I can bundle it into the Youtube Channel Gallery.


== Installation ==

1. Upload the *.zip copy of this plugin into your WordPress through your 'Plugin' admin page.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place the widget in your desired sidebar through the "widgets" admin page.


== Frequently Asked Questions ==

= Where is the “widgets” admin page? =

The “widgets” admin page is found in the administrator part (wp-admin) of your WordPress site. Go to Appearance > Widgets.

= How do I find the YouTube user id? =

The username who uploaded a video to Youtube is located below each video, where says something like in this example, "Published on June 25, 2012 by DisneyShorts", where DisneyShorts is the username. Click on the user name and you will find the user id in the url of that page: https://www.youtube.com/user/DisneyShorts. DisneyShorts is the id of that user.

= How do I find a YouTube playlist id? =

If you go to a playlist you will get the following url format: https://www.youtube.com/playlist?list=PL33942589618ABDE3. The playlist id is what you have after list=. In this example, the playlist id is PL33942589618ABDE3.

= I selected showing my playlist in descending order but the latest videos are not displayed, why? =

This will happen if your playlist has more than 1000 videos because YouTube API has this limit.

= Thumbnails links go to the YouTube page instead of playing the video in the player? =

If another plugin or your theme throws a javascript error before Youtube Channel Gallery has been executed, it will prevent Youtube Channel Gallery JavaScript from functioning properly, so thumbnails links will go to the YouTube page instead of playing the video in the player.

= The plugin throws the following error in the error console of Google Chrome: Blocked a frame with origin "http://www.youtube.com" from accessing a frame with origin "http://myweb.com". Protocols, domains, and ports must match.  =

I think this is a browser error because this also happens to players that can be seen in https://developers.google.com/youtube/.

= If the plugin is used on a page using SSL, the player will throw warnings in the browser console =

See [HTTPS Support for YouTube Embeds](http://apiblog.youtube.com/2011/02/https-support-for-youtube-embeds.html):
"The actual video bitstream, and some additional content loaded by the YouTube player may still be accessed via standard HTTP connections when you use an HTTPS URL in your embed code."


== Screenshots ==

1. Youtube Channel Gallery widget: Feed and Player tabs.
2. Youtube Channel Gallery widget: Thumbnails and Link tabs.
3. Youtube Channel Gallery example.
4. Youtube Channel Gallery example.
5. Youtube Channel Gallery example.


== Changelog ==

= 2.0.3 =
* Added brazilian portuguese translation by [lojainterativa.com](http://www.lojainterativa.com).
* Added italian translation by [Marco Milesi](https://profiles.wordpress.org/milmor).

= 2.0.2 =
* Delete debug element.

= 2.0.1 =
* Change short_open_tag in thumbs.php for compability with php < 5.4.

= 2.0.0 =
* Update in order to make it compatible with YouTube API 3.
* Option to set player width.
* Options to set the order of elements of each tab.
* Options to improve responsive behavior based on Bootstrap grid system.
* Options to select title tag.
* Optimization of alignment behavior.
* Options to set the order of elements of thumbnails tab.
* Option to promotion.
* Rewritted code.

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
* Added new classes to better manage the final appearance (rows, columns, even, odd, number of row an column).
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
* Check that the inserted user name exists.
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



== Upgrade Notice ==

= 1.5.3 =
* New fields to control the player