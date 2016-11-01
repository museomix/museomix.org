=== InfiniteWP Client ===
Contributors: infinitewp
Tags: admin, administration, amazon, api, authentication, automatic, dashboard, dropbox, events, integration, manage, multisite, multiple, notification, performance, s3, security, seo, stats, tracking, infinitewp, updates, backup, restore, iwp, infinite
Requires at least: 3.1
Tested up to: 4.5.3
Stable tag: trunk

Install this plugin on unlimited sites and manage them all from a central dashboard.
This plugin communicates with your InfiniteWP Admin Panel.

== Description ==

[InfiniteWP](https://infinitewp.com/ "Manage Multiple WordPress") allows users to manage unlimited number of WordPress sites from their own server.

Main features:

*   Self-hosted system: Resides on your own server and totally under your control
*   One-click updates for WordPress, plugins and themes across all your sites
*   Instant backup and restore your entire site or just the database
*   One-click access to all WP admin panels
*   Bulk Manage plugins & themes: Activate & Deactive multiple plugins & themes on multiple sites simultaneously
*   Bulk Install plugins & themes in multiple sites at once
*   and more..

Visit us at [InfiniteWP.com](https://infinitewp.com/ "Manage Multiple WordPress").

Check out the [InfiniteWP Overview Video](https://www.youtube.com/watch?v=8wOMewY2EBY) below.

https://www.youtube.com/watch?v=8wOMewY2EBY

Credits: [Vladimir Prelovac](http://prelovac.com/vladimir) for his worker plugin on which the client plugin is being developed.


== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder
2. Go to the Plugins page and activate InfiniteWP Client
3. If you have not yet installed the InfiniteWP Admin Panel, visit [InfiniteWP.com](http://infinitewp.com/ "Manage Multiple WordPress"), download the free InfiniteWP Admin Panel & install on your server.
4. Add your WordPress site to the InfiniteWP Admin Panel and start using it.

== Screenshots ==

1. Sites & Group Management
2. Search WordPress Plugin Repository
3. Bulk Plugin & Theme Management
4. One-click access to WordPress admin panels
5. One-click updates

== Changelog ==

= 1.6.0 - June 27th 2016 =
* Feature: Activity log for updates and backups to be used in new version of client reporting beta will be saved and retrieved from the WP Admin instead of the IWP Admin Panel, provided the client reporting addon is active.
* Improvement: The code in the backup_status_table has been refactored.
* Fix: Failed backups with date “01 Jan 1970” were not cleared from the database.

= 1.5.1.3 - May 24th 2016 =
* Fix: "Unable to update File list table : Can’t DROP ‘thisFileName’; check that column/key exists" error would be thrown while taking Multi-call backups in the Multi-site WordPress environment.

= 1.5.1.2 - May 18th 2016 =
* Fix: If the file path is 192 characters or higher, it would throw a Zip error: unable to update the file list while performing multicall backup.
* Fix: For the first WP core update alone, the From Version was missing in the WP updates section of the Client Reports.

= 1.5.1.1 - Mar 18th 2016 =
* Improvement: Verifying backup uploaded to Amazon S3 utilized higher bandwidth.

= 1.5.1 - Mar 14th 2016 =
* Improvement: Some of the deprecated WP functions are replaced with newer ones.
* Fix: SQL error populated while converting the IWP backup tables to UTF8 or UTF8MB4 in certain WP sites.
* Fix: DB optimization not working on the WP Maintenance addon.
* Fix: Versions prior to WP v3.9 were getting the wpdb::check_connection() fatal error.

= 1.5.0 - Jan 9th 2016 =
* Improvement: Compatibility with PHP7.
* Improvement: Memory usage in Multi call backup now optimized.
* Improvement: Support for new Amazon S3 SDK. Added support for Frankfut bucket which will solve random errors in amazon backup. For php < v5.3.3 will use older S3 library.
* Improvement: Timeout will be reduced in Single call backup Zip Archive.
* Improvement: Client plugin will support MySQLi by using wpdb class.
* Improvement: All tables created by client plugin will use default DB engine.
* Improvement: Maintenance mode status also included in reload data. This will result in the IWP Admin Panel displaying relevant status colours.
* Improvement: Support for WP Maintenance Addon's new options - Clear trash comments, Clear trash posts, Unused posts metadata, Unused comments metadata, Remove pingbacks, Remove trackbacks.
* Improvement: Dedicated cacert.pem file introduced for Dropbox API." client plugin.
* Fix: Issue with IWP DB Table version not updating.
* Fix: Backup DB table now uses WP's charset (default UTF8). This will solve filename issues with foreign (umlaut) characters.
* Fix: Temp files not getting deleted while using single call backup in certain cases.

= 1.4.3 - Nov 18th 2015 =
* Improvement: Maintenance mode status also included in reload data. This will result in the IWP Admin Panel displaying relevant status colours.
* Fix: Maintenance mode shows off even it is in ON mode after the site is reloaded.

= 1.4.2.2 - Sep 24th 2015 =
* Improvement: Translation update support.
* Improvement: All executable files in client plugin should check the running script against the file name to prevent running directly for improved security.
* Improvement: Error message improved for premium plugin/theme when not registered with iwp process.
* Fix: Some admin theme blocks IWP Client from displaying activation key.
* Fix: Fatal error while calling wp_get_translation_updates() in WP versions lower than v3.7.

= 1.4.1 - Aug 31th 2015 =
* Fix: Branding should take effect which we lost in v1.4.0 without making any changes.

= 1.3.16 - Jul 28th 2015 =
* Fix: Dropbox download while restore create memory issue Fatal Error: Allowed Memory Size of __ Bytes Exhausted.

= 1.3.15 - Jul 8th 2015 =
* Improvement: Security improvement.
* Fix: Parent theme update showing as child theme update.
* Fix: Bug fixes.

= 1.3.14 - Jul 3rd 2015 =
* Fix: Bug fix.

= 1.3.13 - May 13th 2015 =
* Fix: In certain cases, a multi-call backup of a large DB missed a few table's data.

= 1.3.12 - Mar 31st 2015 =
* Fix: In a few servers, readdir() was creating "Empty reply from server" error and in WPEngine it was creating 502 error while taking backup
* Fix: .mp4 was excluding by default 

= 1.3.11 - Mar 27th 2015 =
* Improvement: using wp_get_theme() instead of get_current_theme() which is deprecated in WordPress      
* Fix: IWP failed to recognise the error from WP v4.0
* Fix: Restoring backup for second time
* Fix: $HTTP_RAW_POST_DATA is made global, which is conflicting with other plugin
* Fix: Install a plugin/theme from Install > My Computer from panel having IP and different port number
* Fix: Install a plugin/theme from Install > My Computer from panel protected by basic http authentication
* Fix: Google Webmaster Redirection not working with a few themes
* Fix: Bug fixes

= 1.3.10 - Jan 27th 2015 =
* Fix: Bug Fix - This version fixes an Open SSL bug that was introduced in v1.3.9. If you updated to v1.3.9 and are encountering connection errors, update the Client Plugin from your WP dashboards. You don't have to re-add the sites to InfiniteWP.

= 1.3.9 - Jan 26th 2015 =
* Fix: WP Dashboard jQuery conflict issue. 
* Fix: Empty reply from server created by not properly configured OpenSSL functions.
* Fix: Google Drive backup upload timeout issue.

= 1.3.8 - Dec 2nd 2014 =
* Fix: Fixed a security bug that would allow someone to put WP site into maintenance mode if they know the admin username. 

= 1.3.7 - Nov 21st 2014 =
* Fix: Dropbox SSL3 verification issue.

= 1.3.6 - Sep 1st 2014 =
* Fix: IWP's PCLZIP clash with other plugins. PCLZIP constants have been renamed to avoid further conflicts. This will fix empty folder error - "Error creating database backup folder (). Make sure you have correct write permissions."
* Fix: Amazon S3 related - Call to a member function list_parts() on a non-object in wp-content/plugins/iwp-client/backup.class.multicall.php on line 4587.

= 1.3.5 - Aug 19th 2014 =
* Improvement: Support for iThemes Security Pro.
* Fix: IWP's PCLZIP clash with other plugins.

= 1.3.4 - Aug 11th 2014 =
* Feature: Maintenance mode with custom HTML.
* New: WP site's server info can be viewed.
* Improvement: Simplified site adding process - One-click copy & paste.
* Improvement: New addons compatibility.

= 1.3.3 - Jul 28th 2014 =
* Fix: False "FTP verification failed: File may be corrupted" error.

= 1.3.2 - Jul 23rd 2014 =
* Fix: Dropbox backup upload in single call more then 50MB file not uploading issue.

= 1.3.1 - Jul 16th 2014 =
* Fix: "Unable to create a temporary directory" while cloning to exisiting site or restoring.
* Fix: Disabled tracking hit count.

= 1.3.0 - Jul 9th 2014 =
* Improvement: Multi-call backup & upload.
* Fix: Fatal error Call to undefined function get_plugin_data() while client plugin update.
* Fix: Bug fixes.


= 1.2.15 - Jun 23rd 2014 =
* Improvement: Support for backup upload to SFTP repository.
* Fix: Bug fixes.

= 1.2.14 - May 27th 2014 =
* Improvement: SQL dump taken via mysqldump made compatible for clone.

= 1.2.13 - May 14th 2014 =
* Fix: Google library conflict issues are fixed.

= 1.2.12 - May 7th 2014 =
* Improvement: Backup process will only backup WordPress tables which have configured prefix in wp-config.php.
* Improvement: Support for Google Drive for cloud backup addon.
* Improvement: Minor improvements.
* Fix: Bug fixes

= 1.2.11 - Apr 16th 2014 =
* Fix: Bug fixes

= 1.2.10 - Apr 10th 2014 =
* Fix: wp_iwp_redirect sql error is fixed


= 1.2.9 - Apr 9th 2014 =
* Improvement: Support for new addons.
* Fix: Strict Non-static method set_hit_count() and is_bot() fixed.

= 1.2.8 - Jan 21st 2014 =
* Fix: Minor security update

= 1.2.7 - Jan 13th 2014 =
* Fix: Activation failed on multiple plugin installation is fixed
* Fix: Dropbox class name conflit with other plugins is fixed
* Fix: Bug fixes

= 1.2.6 - Nov 18th 2013 =
* Fix: Bug fixes

= 1.2.5 - Oct 30th 2013 =
* Improvement: Compatible with WP updates 3.7+


= 1.2.4 - Oct 16th 2013 =
* Fix: Empty backup list when schedule backup is created/modified

= 1.2.3 - Sep 11th 2013 =
* Fix: Gravity forms update support

= 1.2.2 - Sep 6th 2013 =
* Improvement: Minor improvements for restore/clone
* Fix: Warning errors and bug fixes for restore/clone

= 1.2.1 - Aug 28th 2013 =
* Fix: Fatal error calling prefix method while cloning a fresh package to existing site

= 1.2.0 - Aug 26th 2013 =
* Improvement: Backup fail safe option now uses only php db dump and pclZip
* Improvement: Better feedback regarding completion of backups even in case of error
* Improvement: Restore using file system (better handling of file permissions)
* Fix: Notice issue with unserialise

= 1.1.10 - Apr 5th 2013 =
* Charset issue fixed for restore / clone
* Dropbox improved
* Cloning URL and folder path fixed


= 1.1.9 - Mar 22nd 2013 =
* Better error reporting
* Improved connection reliability

= 1.1.8 - Feb 21st 2013 =
* Minor fixes

= 1.1.7 - Feb 20th 2013 =
* Old backups retained when a site is restored
* Compatible with Better WP Security
* Compatible with WP Engine
* Improved backups
* Bug fixes

= 1.1.6 - Dec 14th 2012 =
* Multisite updates issue fixed

= 1.1.5 - Dec 13th 2012 =
* WP 3.5 compatibility
* Backup system improved
* Dropbox upload 500 error fixed

= 1.1.4 - Dec 7th 2012 =
* Bug in command line backup fixed

= 1.1.3 - Dec 3rd 2012 =
* Backup improved and optimize table while backing up fixed
* Excluding wp-content/cache & wp-content/w3tc/ by default
* Amazon S3 backup improved
* pclZip functions naming problem fixed
* get_themes incompatibility fixed

= 1.1.2 - Oct 5th 2012 =
* Respository issue when openSSL is not available, fixed
* Restore MySQL charset issue fixed
* Backups will not be removed when sites are re-added

= 1.1.1 - Oct 2nd 2012 =
* Improved backups
* Bug fixes

= 1.1.0 - Sep 11th 2012 =
* Premium addons bugs fixed
* Reload data improved

= 1.0.4 - Aug 28th 2012 =
* Premium addons compatibility
* Clearing cache and sending WP data
* Bugs fixed

= 1.0.3 - Jun 11th 2012 =
* WordPress Multisite Backup issue fixed
* Bugs fixed

= 1.0.2 - May 16th 2012 =
* Bugs fixed

= 1.0.1 - May 11th 2012 =
* WordPress Multisite support
* Bugs fixed

= 1.0.0 - Apr 25th 2012 =
* Public release
* Bugs fixed
* Feature Improvements

= 0.1.5 - Apr 18th 2012 =
* Client plugin update support from IWP Admin Panel 
* Backup file size format change


= 0.1.4 - Apr 2nd 2012 =
* Private beta release
