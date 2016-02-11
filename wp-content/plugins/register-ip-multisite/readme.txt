=== Register IPs ===
Contributors: Ipstenu, JohnnyWhite2007
Tags: IP, log, register, multisite, wpmu, ip
Requires at least: 3.1
Tested up to: 4.0
Stable tag: 1.6.1
Donate link: https://store.halfelf.org/donate/

When a new user registers, their IP address is logged. Works on Multisite and Single Site!

== Description ==

Spam is one thing, but trolls and sock puppets are another.  Sometimes people just decide they're going to be jerks and create multiple accounts with which to harass your honest users.  This plugin helps you fight back by logging the IP address used at the time of creation.

When a user registers, their IP is logged in the `wp_usermeta` under the signup_ip key.  Log into your WP install as an Admin and you can look at their profile or the users table to see what it is. For security purposes their IP is not displayed to them when they see their profile.

* [Plugin Site](http://halfelf.org/plugins/register-ip-ms/)
* [Donate](https://store.halfelf.org/donate/)

== Installation ==

No special activation needed.

== Frequently Asked Questions ==

= Why do some users say "None Recorded"? =
This is because the user was registered before the plugin was installed and/or activated.

= Who can see the IP? =
Admins and Network Admins. 

= Does this work on MultiSite? =
Yep! 

= If this works on SingleSite why the name? =
There's already a plugin called "Register IP", but it didn't work on MultiSite.  I was originally just going to make this a MultiSite-only install, but then I thought 'Why not just go full bore!'  Of course, I decided that AFTER I requested the name and you can't change names. So you can laugh.

= Does this work with BuddyPress? =
It works with BuddyPress on Multisite, so I presume single-site as well. If not, let me know!

= This makes my screen too wide! =
Sorry about that, but that's what happens when you add in more columns.

= What's the difference between MultiSite and SingleSite installs? =
On multisite only the Network admins who have access to Network Admin -> Users can see the IPs on the user list.

== Screenshots ==
1. Single Site (regular users menu)
2. Multisite (Network Admin -> Users menu)

== Changelog ==

= 1.6.1 =
* 22 June, 2014 by Ipstenu
* Typo

= 1.6 =
* 21 June, 2014 by Ipstenu
* Cleanup, function names, readme.

= 1.5 = 
* 17 April, 2012 by Ipstenu
* Readme cleanup, fixing URLs etc.

= 1.4 =
* 9 February 2012 by Ipstenu
* Adding in German (translated by Robert @ TalkPress)

= 1.3 =
* 4 October 2011 by Ipstenu
* Removing redundancy
* Full 3.3 WordPress compatibility

= 1.2 =
* 15 July 2011 by Ipstenu
* Dropping support for 3.0.x 

= 1.1 =
* 3 Dec 2010  by Ipstenu
* Critical fix to correct issue with 3.0.2!

= 1.0  =
* 24 Nov 2010 by Ipstenu
* Forward and backward compatibility with WordPress 3.1! Yay!

= 0.2.1 =
* 08 Nov 2010 by Ipstenu
* Critical Bugfix!  Typo 'wiped out' old IPs listed!

= 0.2 =
* 08 Nov 2010 by Ipstenu
* Internationalization
* Generated POT file so you could do what you want!

= 0.1 =
* Initial fork by Ipstenu
* Change to work in MultiSite AND Single Site
* BuddyPress Tested
