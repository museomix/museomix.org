=== WP Native Dashboard ===
Contributors: codestyling
Tags: wordpress, dashboard, multi-lingual, languages, backend, localization, plugin
Requires at least: 2.7
Tested up to: 3.6
Stable tag: 1.3.12

Enables selection of administration language either by logon, dashboard quick switcher or user profile setting.

== Description ==

This plugin enables the selection of your prefered language the dashboard (and whole administration) will be shown during your work.
Several options can be enabled and also combinations out of:

1. logon screen extension - user can specify his/her prefered language during logon 
1. dashboard quick switcher extension - user can easily switch language at every admin page
1. WordPress admin bar switcher - user can switch at active admin bar the language as usual
1. BuddyPress admin bar switcher - user can switch at active admin bar the language as usual
1. user profile setting - each user can define at profile his/her prefered language

The plugin also includes a repository scan on demand (svn.automattic.com) for available language file downloads.
You can download the required files into your installation and immediately use them at admin pages.
The new administration page is restricted to administrators only, the profile setting also work for subscriber.
The dashboard quick switcher handling has been changed startion WordPress version 3.0 because there is no longer a header. You can use the quickswitcher at the admin bar anyway, this is prefered use case at higher versions of WordPress.

= Download and File Management = 

Starting with version 1.1.0 of this plugin it uses now the WordPress build-in file management from core. If the plugin detects, that you are not permitted to write directly to disk, it uses the FTP user credentials for download and remove of language files.

= WordPress / WPMU and BuddyPress =

If you have a local WordPress community providing their own download repository for language files, please let me know, if you would like to get it integrated.
Because i didn't found an official language file repository for BuddyPress and WPMU, it currently only permits WordPress language file downloads.
If you have more specific informations about, please let me know, it's easy to integrate a new download section (also with detection the kind of WP).

= Requirements =

1. WordPress version 2.7 and later
1. PHP Interpreter version 4.4.2 or later

Please visit [the official website](http://www.code-styling.de/english/development/wordpress-plugin-wp-native-dashboard-en "WP Native Dashboard") for further details, documentation and the latest information on this plugin.

== Installation ==

1. Uncompress the download package
1. Upload folder including all files and sub directories to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Navigate to your Dashboard and enjoy status informations

== Changelog ==
= Version 1.3.12 =
* noop: created to solve the wordpress.org packaging issue for *.zip files

= Version 1.3.11 =
* feature: showing page language at frontend admin bar, if admin bar should be displayed with backend language
* bugfix: compatibility with WordPress 3.6, avoid non object access

= Version 1.3.10 =
* bugfix: ajax based login plugins (sidebar logins) did not work as expected
* bugfix: admin bar translations was broken, if WordPress is installed at sub folder
* bugfix: flag for en_GB provided
* feature: added 299 locales to ensure full support

= Version 1.3.9 =
* bugfix: using SSL either forced or fully did produce a mixed mode admin page delivery
* bugfix: translation of frontend admin bar now works with any type of extension (includes debug-bar plugin).
* feature: added plugin help screen for WordPress >= 3.3
* feature: additional option to remove locale abbreviation brackets
* feature: new option for using login language selector within your own login plugins/widgets (see help screen)

= Version 1.3.8 =
* bugfix: build in language "en_US" could not longer be selected, if WPLANG explicitely defined.
* feature: supports new translation files introduced with WordPress 3.4 version
* feature: frontend admin bar can be shown translated with current users backend language (new settings option)

= Version 1.3.7 = 
* bugfix: stylesheet correction for admin bar integration
* bugfix: svn download doesn't work at some installations but doesn't show the error for
* bugfix: case sensitive admin url may lead to unpredicable behavior (especially at multisite folder installs)
* bugfix: WordPress versions equal or higher than 3.0 will not longer support head switcher but admin bar switcher
* bugfix: downloaded language will be show immediately at the selector dropdown after success.
* bugfix: delete language file but active selected will be reverted to 'en_US'
* feature: language search now provides a selectable fallback version for language file downloads
* feature: supports Galego language
* feature: supports Mongolian language
* feature: supports Georgian language
* feature: supports Uighur language
* feature: supports Albanian language
* feature: supports Burmese language

= Version 1.3.6 =
* feature: supports now BuddyPress own Adminbar to be able to switch the languages.
* remark: BuddyPress badly supports RTL languages especially for AdminBar, so my AdminBar switcher may not work for RTL (BuddyPress Bug)

= Version 1.3.5 =
* bugfix: switching language at site or user admin pages did not work

= Version 1.3.4 =
* bugfix: screen_layout_column throws info messages at multisite or user dashboard pages

= Version 1.3.3 =
* feature: language switcher can now be configured to occure in WP Admin Bar (WP >= 3.0)

= Version 1.3.2 =
* bugfix: avoid javascript error if dashboard langswitcher is off but language eigther gets delete or downloaded
* bugfix: how to get the icon image of user credential has been changed at WP 3.0 core and gots fixed
* remark: how to use SSH for downloading files is explained here: [SSH and WordPress](http://www.firesidemedia.net/dev/wordpress-install-upgrade-ssh/ "WordPress Tutorial: Using SSH to Install/Upgrade")

= Version 1.3.1 =
* clean working debug mode (removed deprecated warnings)
* bugfix for user specific values during config
* checked RTL support and reported new trac ticket: http://core.trac.wordpress.org/ticket/14129
* language download supports now WP 3.0 new capabilities (ms-xx_XX.mo / xx_XX.css / xx_XX-ie.css / ms-xx_XX.css) 
* bugfix login selector for WP 3.0
* login permanently denied by some languages like ru_RU (please read Frequently Asked Questions)

= Version 1.3.0 =
* several checks at PHP 5.3 or higher were missing and may stop some functions

= Version 1.2.0 =
* missing language folder (US original version) denies download
* accidentally empty language display, if no mo is installed (en_US is always inline)

= Version 1.1.0 = 
* full support of core file system usage (FTP if necessary)
* locale to language name mapping introduced (user friendly namings)
* beautyfied some UI states (alternate rows correction after download)

= Version 1.0.1 =
* Forcing jQuery usage even if a backend page (from another plugin eg.) doesn't make use of.
* providing official page link for supporting purpose.

= Version 1.0 =
* initial version


== Frequently Asked Questions ==
= History? =
Please visit [the official website](http://www.code-styling.de/english/development/wordpress-plugin-wp-native-dashboard-en "WP Native Dashboard") for further details, documentation and the latest information on this plugin.

= Where can I get more information? =
Please visit [the official website](http://www.code-styling.de/english/development/wordpress-plugin-wp-native-dashboard-en "WP Native Dashboard") for further details, documentation and the latest information on this plugin.

= Why I can't login anymore if I did download and activate as example ru_RU? =
This happens if you did not define you own phrases inside the wp-config.php file.
If the default is still in place at any of those defines like define('AUTH_KEY', 'put your unique phrase here'); the internal validation fails.
The failure reason is the additional file needed for russian language named "ru_RU.php" in cooperation with your wp-config.php file. 
Inside this file the default phrase has been changed to $wp_default_secret_key = 'впишите сюда уникальную фразу'; instead of original 'put your unique phrase here'.
This can be solved if you modify the wp-config.php file with your own phases as highly recommended by WP core teams too.

== Screenshots ==
1. dashboard quick switcher 
1. user profile setting extension
1. extended WordPress login screen
1. administration page
1. download scan process
1. full administration page
1. user credentials required for writing
