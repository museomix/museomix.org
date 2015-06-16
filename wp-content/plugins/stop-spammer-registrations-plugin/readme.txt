=== Stop Spammers ===
Tags: spam, comment, registration, login
Requires at least: 3.0
Tested up to: 3.9
Contributors: Keith Graham
Stable tag: 5.9.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Stop Spammers Plugin checks comments and logins using many methods to stop spammers.

== Description == 
Stop Spammers is an aggressive spam plugin that stops spam registrations, logins and comments using multiple checks.  It looks for typical spammer bad behaviors and blocks those. It blocks access to users who anonymize their browsers, and it checks how long it takes to fill in a comment or login form and blocks users who are too fast.
The plugin is extremely aggressive and makes no apologies for occasionally blocking users who do not behave well. It will block users who install anonymizing plugins or turn off headers and cookies. Normal users will never know the plugin has been installed.
The plugin installs "honeypots" to fool spammers into leaving spam with a hidden form, where they will be marked as a spammer and blocked. 
The plugin is not active as long as your site is being browsed so that it does not block content, but only kicks in if a form is submitted.
When a spammer is detected, their address is added to a temporary cache so  that future attempts are immediately blocked without having to recheck the spammer all over again.
The plugin checks known spammer hosts such as Ubiquity Servers, disposable email addresses, very long email address and names, and HTTP_ACCEPT header.
White List Requests:
When a user is denied access to login, registration or comments, they are presented with a form which allows them to notify the Admin user that they wish to be allowed into the site. Spammers usually do not fill out the form, or if they do, it will be filled in with spam. 
When the user presses the submit button, the admin will get an email and can then white list the user so that the person will have access to the site. The admin should also clear the cache (on the stop spammer history settings page) and then the user will have access to the site.

History: 

The Stop Spammers plugin keeps a count of the spammers that it has blocked and displays this on the WordPress dashboard. It also displays the last hits on email or IP and it also shows a history of the times it has made a check, showing rejections, passing emails and errors. When there is data to display there will also be a button to clear out the data. You can control the size of the list and clear the history.
If a user tries to log in and passes all checks for spammers an icon appears next to the IP address. Only users you know should be allowed to login, so by clicking the icon, you can add the IP to your black list. 

Cache: 

The Stop Spammers plugin keeps track of a number of spammer emails and IP addresses in a cache to avoid pinging databases more often than necessary. The results are saved and displayed. You can control the length of the cache list and clear it at any time. The plugin caches IP addresses that do not fail, assuming that they may be valid users. In order to prevent re-checking these IP addresses, the plugin stores the last two IP addresses that passed all tests. Persistent spammers, who pass once, may be placed in the good cache allowing them access to comments for a short while. If this happens then the cache should be cleared to force a recheck on the spammer.


Network MU Multisite Installation Option: 

If you are running a networked WPMU system of blogs, you control this plugin from the network admin dashboard. The Spammer Multisite option appears on the Network Admin dashboard under settings. You can turn on the networked settings and the plugin will work as a global plugin protecting all websites and keeping the statistics for the websites in one location.


== Installation ==
1. Install the plugin using "add new" from the plugins menu item on the Wordpress control panel. Search for Stop Spammers and install.
OR
1. Download the plugin.
2. Upload the plugin to your wp-content/plugins directory.
THEN
3. Activate the plugin.
4. Under the settings, review options that are enabled. Update the white list. 

== Changelog ==

= 5.9.3 =
* addd a warning that plgin will not work without cloudflare plugin when using cloudflare.
* fixed a captcha bug. 

= 5.9.2 =
* fixed a captcha bug. 

= 5.9.1 =
* Fixed mistake in using transients.
* added a check that GD is loaded to make sure that the captcha will work.

= 5.9 =
* Removed all IP address reporting of when an ip addresses is found in header information. IP address is actively being spoofed by adding headers for a proxy server. This allows IPs to be pass lookup tests at SFS and Akismet. It also allows the reporting of good addresses as spam. Spammers may have been using whitelisted IP numbers to bypass checks, which is no longer possible.  
* Stopped trusting proxy server addresses - when a header from a proxy server is detected, I assume that it is an attempt to spoof IP address. This makes it difficult for Cloudflare, Proxy users and VPN users to use the site as they may be marked as spammers when they are not. I had to add a fix - see next point.
* Added a second chance CAPTCHA on failed events. A sysop can turn it off. This will prevent blocking good users who use proxy servers.  
* Added a switch to quickly turn off all database lookups. The plugin is only about 50% effective when this is done.
* fixed some bugs in TOR lookup and data sanitizing. 
* Made changes to data display to prevent cross browser or JavaScript attacks.
* Akismet is still turned off for now. I have yet to figure out why Akismet does not work.
* Deleted widget. Moved it into the main plugin for those who need it, but it is no longer a separate plugin. It will still show up on the widgets menu, but may have to be dragged to the sidebar again.

= 5.8 =
* Fixed security issue in white list requests.

= 5.7 =
* Change version on widget.

= 5.6 =
* fix problem with emails not working. By default wp_mail checks for spammers, the spammer could not send spam notifications to the admin. Removed hooks when logging white list requests.
* added a link to new user notifications so that admins could click the link to the user maintenance page directly. This looks like it should be a standard WordPress feature.
* Added a time out to the caches. Good ips time out in 1 hour. Bad ips time out in 4 hours. This forces a second check on ips after an hour so that a new spammer can't keep spamming. It also forces rechecks on bad spammers so that the reason code might be more reasonable and not just "cached bad ip". Admins will not have to log in to clear the cache as often.
* added a fix so there will be no conflicts with Google Authenticator plugin.
* Added automatic whitelist for vaultpress access.
* Removed Poison links from RSS feeds. 
* Added code to block Tor, by default turned off. 
* Disabled Akismet checks. I am getting numerous complaints that something has changed and Akismet is reporting users as spammers.

= 5.5 =
*  Fixed an the same issue in 5.4 for multisite. Wordpress changed the way the add actions on the plugin line work.

= 5.4 =
*  Fixed an issue with action links on the plugins page

= 5.3 =
* Restored Right Now and Plugins settings links. I only create the link if you have actually reached the settings or history pages so as not to be burned by things like WP 3.8 changing the way they find a page.
* made a check spam for user registrations. I have been saving IP addresses since 5.0 version.
* White listed Vaultpress so the plugin will not deny access.

= 5.2 =
* removed bad links in plugin until I have time to rewrite the code.
* fixed warning messages in $_SERVER checks.
* fixed undefined variable in Gravity Forms checking.
* fixed problem with login ip address recording that conflicts with some plugins.
* Nothing new here. I am taking out stuff that doesn't work. I will not be able to work on the plugin for the next few months, except to kill bugs.

= 5.1 =
* Fixed typo on spammer history page. Deleted links. Will add back in next version.

= 5.0 =
* Added poison links.
* Added email notifications on white list requests.
* Removed bbpress loading code because of conflicts. Not all checks will work on bbpress. Plugin might not stop bbpress registrations.
* Moved good cache tests to just before db lookups to prevent false negatives.
* Use cookies to check session speed so as not to cause problems with sites that can't handle sessions.
* Changed session so as not to restart the timer on subsequent checks. This prevents some redirections from appearing to be spam robots.
* Added new header checks for finding real IP. It now works with more proxy servers.
* Reorganized the way the plugin loads (again) to reduce overhead. Lazy loading works better now. 
* Fixed bugs in email domain checks. 
* Relaxed checks for http_referer so as not to fire when switching from https to http and back.
* Changed the way Red Herring forms are checked, has its own action now.
* Added IP lookup for CloudFlare.com.
* Ran code through a formatting program. Pretty code will not last long, though, since my IDE is notepad.
* Added a function that can be called by other plugins who wish to check for spam. 
* " if (function_exists('stop_spam_check')) stop_spam_check(); "
* Added liker.profile checks - if request has liker.profile and poison is checked then the spammer goes to bad ip cache.
* Might work with Gravity Forms. I made changes, but Gravity Forms is a pay plugin so I don't have access to test it.


== Frequently Asked Questions ==
= Help, I'm locked out of my Website =
Not everyone who is marked as a spammer is actually a spammer. It is quite possible that you have been marked as a spammer on one of the spammer databases. There is no "back door", because spammers could use it.
Rename stop-spammer-registrations.php to stop-spammer-registrations.xxx and then login. Rename it back and check the history logs for the reason why your were denied access. Was your email or IP address marked as spam in one of the databases? If so, contact the website that maintains the database and ask them to remove you. 
Check off the box, "Automatically add admins to white list" in the spammer options settings. Then save your settings. This puts your IP address into the white list. You should be able to logout and then log back in.
Use the button on the Stop Spammer settings page to see if you pass. You may have to uncheck some options in order to pass. 

= I have found a bug =
Please report it NOW. I fill try to fix it and incorporate the fix into the next release. I try to respond quickly to bugs that are possible to fix (all others take a few days). 
If you are adventurous you can download the latest versions of some of my plugins before I release them.
= I used an older version of the plugin and it worked, but the latest version breaks my site =
You can download previous versions of the plugin at: http://wordpress.org/extend/plugins/stop-spammer-registrations-plugin/developers/

Don't forget to report to me what the problem is so I can try to fix it.
= All spammers have the same IP =
I am finding more and more plugin users on hosts that do some kind of Network Address Translation (NAT) or are behind a firewall, router, or proxy that does not pass the original IP address to the web server. If the proxy does not support X-FORWARDED-FOR (XFF) type headers then there is little that you can do. You must uncheck the "Check IP" box and rely on the plugin to use the passive methods to eliminate spammers. These are good methods and will stop most spammers, but you cannot report spam without reporting yourself, and you cannot cache bad IP addresses.
= I can't log into WordPress from my Android/iPhone app. =
Check your log files to find out exactly why the app was rejected. It usually is often the HTTP_REFERER header was not sent correctly. This is one sign of badly written spam software. It is also, unfortunately, a sign of badly written login software. Uncheck the box on the Stop Spammer settings page "Block with missing or invalid HTTP_REFERER". I white list iPhones and iPads using Safari on some checks because of bugs in the headers it sends.
= I see errors in the error listing below the cache listing =
It could be that there is something in your system that is causing errors. Copy the errors and email them to me, or paste them into a comment on the WordPress plugin page. I will investigate and try to fix these errors.
= You plugin is stopping new spam registrations, but how do I clean up existing spam registrations? =
Unfortunately, WordPress did not record the IP address of User registrations prior to version 5.0. This is a design flaw in WordPress. They do record the IP of comments. I cannot run a check against logins without their IP address, so you have to remove users the old fashioned way, one at a time. I will be adding code to recheck login information for users who registered after the plugin's 5.0 version was installed.
You might try listing the emails of all registered users, and then deleting them. You can then ask all users to re-register, but that would probably annoy your legitimate users.
= I have a cool idea for a feature for Stop-Spammer-Registrations-Plugin. =
Most of the features in the plugin have come from the users of the plugin. By all means stop by my website and leave a comment. I read all of them, and if they are feasible, I try to include them.
= I would like to support your programming efforts =
I am slowing down maintenance on this plugin. I don't have time to work on it. Don't send me money.


== Screenshots ==

1. A shot of the Spammer History settings page.

== Support ==

2/21/2014: I am ending support of this plugin. I will be asking WordPress to remove it from the Repository as it causes too many problems.



