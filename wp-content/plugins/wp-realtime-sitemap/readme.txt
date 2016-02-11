=== WP Realtime Sitemap ===
Contributors: Rincewind
Donate link: http://goo.gl/ddoa5
Tags: seo, navigation, site, map, sitemap, sitemaps, posts, pages, custom, post, types, wpmu, wordpress, multisite, multiuser, bilingual, i18n, l10n, language, multilanguage, multilingual, translation, qtranslate
Requires at least: 3.0
Tested up to: 3.2
Stable tag: trunk

A sitemap plugin to make it easier for your site to show all your pages, posts, archives, categories and tags in an easy to read format without any need for template modification or html/php knowledge in a page, my plugin does not create a .xml for any search engines this is outside of what this plugin was designed to do.

== Description ==

A sitemap plugin to make it easier for your site to show all your pages, posts, archives, categories and tags in an easy to read format without any need for template modification or html/php knowledge in a page, my plugin does not create a .xml for any search engines this is outside of what this plugin was designed to do.

1. Order the output anyway you want in the plugin settings page.
1. Order the output of the WP Nav Menu, Pages, Posts, Custom Post Types, Archives, Categories and Tags.
1. Show/hide WP Nav Menu, Pages, Posts, Custom Post Types, Archives, Categories and Tags.
1. Optionally show categories and/or tags as a bullet list, or as a tag cloud.
1. Exclude Pages, Posts, Custom Post Types, Categories and Tags IDs.
1. Limit the amount of posts, custom post types, archives, categories and tags displayed.
1. Change the archive type from the WordPress default.
1. Show/hide Categories and Tags which have no posts associated to them.
1. Show/hide how many posts are in each Archive, Category or Tag.
1. Optionally name the sections different from the default of Pages, Posts, Archives, Categories and Tags.
1. Hierarchical list of pages and categories.
1. Supports I18n for translation.
1. Supports use of the wordpress shortcode for including the sitemap in pages and posts.
1. Supports menus created with the inbuilt WordPress Menu Editor.
1. Works on WordPress Multisite (WPMU) blogs.
1. Comes with an uninstaller, if you dont want it anymore just deactivate the plugin and delete it from within wordpress and it will delete all of its settings itself.

I cant think of anything else that I personally would need this plugin to do for my own use, if anyone feels it doesn't meet what they need, or has any suggestions as to how to make it better then do please get in touch with me and I will see what I can do to accomodate your requests.

WP Realtime Sitemap is available in:-

* English by [Daniel Tweedy](http://www.daniel-tweedy.co.uk).
* Brazilian Portuguese by Gervasio Antonio. **needs updating**
* Czech by Libor Cerny. **needs updating**
* Dutch by [Martien van de Griendt](http://www.vandegriendtwebsites.nl).
* German by [Andreas Breitschopp](http://www.ab-weblog.com/de/).
* Romanian translation by [Luke Tyler](http://www.enjoyprepaid.com).
* Russian by [ssvictors](http://wordpress.org/support/profile/ssvictors) and [Igor Dubilej](http://www.itransition.com).
* Spanish by Francois-Xavier Gonzalez. **needs updating**

Please rate this plugin and/or make a [donation](http://goo.gl/ddoa5 "PayPal donation") if you find it useful, thank you.

== Installation ==

= Instructions for installing via download from wordpress.org =

1. Download and extract the Plugin zip file.
1. Upload the files to `/wp-content/plugins/wp-realtime-sitemap` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

= Instruction for installing from within your own blog =

1. Login to the admin interface.
1. Click Plugins in the left hand menu navigation.
1. Click on "Add New" next to the Plugins header in the main content area.
1. Enter "WP Realtime Sitemap" (without quotes) in the textbox and click the "Search Plugins" button.
1. In the list of relevant plugins click the "Install" link for "WP Realtime Sitemap" on the right hand side of the page.
1. Click the "Install Now" button on the popup page.
1. Click "Activate Plugin" to finish installation.

= Display the sitemap on a page or post =

1. Click Pages in the left hand menu navigation.
1. Click on "Add New" in the left hand menu navigation or click on "Add New" next to the Pages header in the main content area.
1. Give your page a title I suggest Sitemap, and put `[wp-realtime-sitemap]` into the wysiwyg box.
1. Now save or update the page and click on the View page link at the top to see your new sitemap.

**Note**: If you already have a page for your sitemap then put the shortcode `[wp-realtime-sitemap]` in this pages wysiwyg box instead of creating a new page.

Please see the [FAQ](http://goo.gl/QNiRH "FAQ") and [Other Notes](http://goo.gl/bVQBL "Other Notes") tab for further help.

== Frequently Asked Questions ==

= I would like to make a donation how can I do this? =

You can make a Pay Pal donation by clicking [here](http://goo.gl/ddoa5 "PayPal donation"), or click on "Donate to this plugin" in the right hand side box where it says FYI, your donation will be very gratefully received thank you!

= What should I call the page that I add the sitemap to? =

You can call it whatever you like. I would suggest you call it Site Map.

= I have some pages that I need but are to be hidden and not on the sitemap =

My plugin only shows posts and pages that have the status as published, so if you wish to have a post of page be published but not to be shown, change its status to "privately published" and it will disappear off the sitemap, you can do this easily when editing a post/page with the Publish box on the left hand side, I have included a screeshot to show what to set this box to.  You can also exclude by ID now in the settings.

= I cant get the other short code options to work =

These are now depreciated and should no longer be used, only the shortcode `[wp-realtime-sitemap]` should be in your sitemap page now, all other options are now decided by the admin interface for the plugin and this will always be the case moving forward.

= Do I need to add the &lt;!--wp-realtime-sitemap--&gt; to a Post or a Page? =

This method is no longer supported, please use the shortcode `[wp-realtime-sitemap]` instead in a page or a post, please see the [Installation](http://goo.gl/bRO8F "Installation") tab for further help.

= Is there a php code so I can add it to a php template file? =

This method is no longer supported, please use the shortcode `[wp-realtime-sitemap]` instead in a page or a post, please see the [Installation](http://goo.gl/bRO8F "Installation") tab for further help.

== Screenshots ==

1. Settings page in the admin area.
1. Output as displayed on Twenty Ten theme.
1. How to hide a post and/or page off the sitemap using the published privately option in WordPress.

== Examples ==

The shortcode will use the admin interface for all its configurable options the shortcode is now only used for where to display/output the sitemap at.

Show the sitemap: `[wp-realtime-sitemap]`.

Ordering is done within the plugin settings page.

= Old depreciated options for reference only =

The example shortcodes below are for reference only for use using version below v1.5.2, please do not use these past this version as they are unlikely to be included in future versions, and should therefore be treated as depreciated.

Show everything: `[wp-realtime-sitemap show="all"]`.

Show pages: `[wp-realtime-sitemap show="pages"]`.

Show posts: `[wp-realtime-sitemap show="posts"]`.

Show custom post types: `[wp-realtime-sitemap show="custom-posts"]`.

Show archives: `[wp-realtime-sitemap show="archives"]`.

Show categories: `[wp-realtime-sitemap show="categories"]`.

Show tags: `[wp-realtime-sitemap show="tags"]`.

== Translations ==

If you're multi-lingual then you may want to consider donating a translation, WordPress is available in several different languages, see [http://codex.wordpress.org/WordPress_in_Your_Language](http://goo.gl/9TlYK "WordPress in Your Language") for more information.

Currently translated into the following languages :-

* Brazilian Portuguese by Gervasio Antonio. **needs updating**
* Czech by Libor Cerny. **needs updating**
* Dutch by [Martien van de Griendt](http://www.vandegriendtwebsites.nl).
* German by [Andreas Breitschopp](http://www.ab-weblog.com/de/).
* Romanian translation by [Luke Tyler](http://www.enjoyprepaid.com).
* Russian by [ssvictors](http://wordpress.org/support/profile/ssvictors) and [Igor Dubilej](http://www.itransition.com).
* Spanish by Francois-Xavier Gonzalez. **needs updating**

All translators will have a link to their website placed on the plugin homepage on my site, and on the wordpress plugin homepage, in addition to being an individual supporter.

Full details of producing a translation can be found in this [guide to translating WordPress plugins](http://goo.gl/Q5LhT "guide to translating WordPress plugins").

== Changelog ==

= 1.5.4 =
* Fixed issue with the custom post types.
* Correct the shortcode mentioned in the description.
* Romanian translation by [Luke Tyler](http://www.enjoyprepaid.com).
* Russian translation updated by [Igor Dubilej](http://www.itransition.com).

= 1.5.3 =
* Added "show_promote" to default options.
* Added upgrade procedure as wordpress changed the way the activation hook works, no longer fires on upgrade only activation.
* German translation by [Andreas Breitschopp](http://www.ab-weblog.com/de/).
* Made some minor changes to the plugin readme.txt to make it easier for people to understand how to use the plugin.

= 1.5.2 =
* Fixed issue with variable name for custom post types.
* Added option to show categories with posts in a hierarchy.
* Added option to show menu created with the menu maker in wordpress.
* Dutch translation by [Martien van de Griendt](http://www.vandegriendtwebsites.nl).
* Updated wp-realtime-sitemap.pot, wp-realtime-sitemap.po and the rest of the .po translation files.
* Added option to order the output in admin interface, individual short codes for menu, pages, posts, custom post types, archives, categories and tags are now obsolete, these have been kept in for now for backwards compatibility.
* All shortcodes are now depreciated, please use `[wp-realtime-sitemap]`.  Ordering is done within the plugin settings page.

= 1.5.1 =
* Fixed issue with default settings being set incorrectly.
* Fixed issue where tags tag cloud was showing categories instead.
* Added missing code to be able to change Posts header.
* Updated wp-realtime-sitemap.pot, wp-realtime-sitemap.po and the rest of the .po translation files.

= 1.5 =
* Completely written all of the options in the admin interface.
* Option to exclude pages, posts, custom post types, archives, categories and tags from the output.
* Now able to limit posts, custom post types, archives, categories and tags from the output.
* No option to limit pages as this is currently broken in WordPress.
* More options for sorting that wasn't included previously.
* Option to change the archive type no longer fixed to monthly.
* Removed sorting options from the WordPress shortcode.
* Fixed code so only runs the code for the section chosen not all sections.

= 1.4.8 =
* Added custom post types, if this was something you have been waiting for, or have requested then please consider making a [donation](http://goo.gl/ddoa5 "PayPal donation") thank you!
* Added ability to change the names of the sections from the defaults of Pages, Posts, Archives, Categories and Tags, this is optional if there blank/empty will use the defaults.
* No longer using query_posts to display pages, posts and custom post types, now using get_posts now works correctly with WPMU.
* Added Screenshots of admin interface, and the output of the plugin.
* Fixed bug where was showing comments and comment form on the sitemap page, a great big thank you to [eceleste](http://wordpress.org/support/profile/eceleste) for help with this fix.
* Fixed issue where was output html which wasn't valid for posts, missing the double quotes round the url, thanks to [GreyIBlackJay](http://wordpress.org/support/profile/greyiblackjay) for spotting this.

= 1.4.7.2 =
* Changed constructor so the localization files are initialized with the plugin.
* Spanish translation by François-Xavier Gonzalez.

= 1.4.7.1 =
* Fixed some duplication errors in the language files.
* Russian translation by [ssvictors](http://wordpress.org/support/profile/ssvictors).

= 1.4.7 =
* Minor fix to the new variable names, some instances where the old ones were still referenced, instead of the new ones.

= 1.4.6 =
* Updated code to be more cleaner and easier to understand.
* Used WordPress Settings API for options form, and added validation.
* Updated the localization files, still fully translatable right down to the admin area.

= 1.4.5 =
* Removed database code from admin_init as was being called on every admin page.
* Added post limit to show x number of posts only, currently limited to 9999.

= 1.4.4 =
* Added option to reset database settings back to defaults.
* Fixed code when using `[wp-realtime-sitemap show="all"]` and not correctly showing tags and/or categories as tag clouds or not.
* Changed activation code to better upgrade database settings, and clean up old data from the database that is now no longer needed.
* Brazilian Portuguese translation by Gervasio Antonio.
* Admin interface now fully translatable.

= 1.4.3 =
* Fixed issue where overwritting `sort_column` variable.

= 1.4.2 =
* Fixed minor bug, where content output would be before whatever was put into the wysiwyg editor, instead of after.
* Wrapped date for posts in a span tag so easier for this to be styled.

= 1.4.1 =
* Minor security update added nonce field to the form, to check request came from your site and not someone elses site who was using the same plugin.

= 1.4 =
* Hot Fix: Removed comment replacement code in favour of shortcodes instead, this was needed to fix an issue on some blogs where php memory limit is set to 64MB.
* Added options to choose to have post count and post date output with the sitemap.
* Streamlined options in the database now instead of several rows for the options in the database, there is now only 1.
* Added code to clean up database from the old way to the new way, preserving your current options also.

= 1.3 =
* Hierarchical list of categories.
* Change code for tags to use wordpress inbuilt functions instead.
* Supports I18n for translation.

= 1.2 =
* Updated code, added settings, support and donate link.
* Fixed display bug.

= 1.1 =
* Optionally show categories and tags as a bullet list, or as a tag cloud.
* Hierarchical list of pages.

= 1.0 =
* First version.

== Upgrade Notice ==

= 1.5.3 =
Please change your code in the page you have your sitemap on to `[wp-realtime-sitemap]`, and re-save the settings for the plugin.

= 1.4.6 =
Renamed form options for Show post count, Show date, Post limit, as a result of this I do regrettably have to tell you you will need to visit the settings page and submit your settings back into the database for these options, otherwise your sitemap will not display on your site.

= 1.4 =
You will need to change the code you have in your pages/posts to show the sitemap, please see plugin page on wordpress.org for more info.

= 1.1 =
Before upgrading you MUST delete the old plugin from your wordpress installation, BEFORE installing the new version! I changed the name of some of the variables stored in the database.
