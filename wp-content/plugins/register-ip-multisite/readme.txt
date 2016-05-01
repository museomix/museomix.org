=== Register IPs ===
Contributors: Ipstenu, JohnnyWhite2007
Tags: IP, log, register, multisite,
Requires at least: 3.1
Tested up to: 4.5
Stable tag: 1.7.1
Donate link: https://store.halfelf.org/donate/

When a new user registers, their IP address is logged. Multisite and Single Site!

== Description ==

Spam is one thing, but trolls and sock puppets are another.  Sometimes people just decide they're going to be jerks and create multiple accounts with which to harass your honest users.  This plugin helps you fight back by logging the IP address used at the time of creation.

When a user registers, their IP is logged in the `wp_usermeta` under the signup_ip key. Log into your WP install as an Admin and you can look at their profile or the users table to see what it is. For security purposes their IP is not displayed to them when they see their profile.

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

= How can I filter the IPs to, say, link to an IP checker? =

There's a filter! Toss this in an MU plugin:

`
function filter_ripm_show_ip($theip) {
	$theip = '<a href="https://duckduckgo.com/?q='.$theip.'">'.$theip.'</a>';
	return $theip;
}
add_filter('ripm_show_ip', 'filter_ripm_show_ip');
`

== Screenshots ==
1. Single Site (regular users menu)
2. Multisite (Network Admin -> Users menu)

== Changelog ==

= 1.7.1 =
* 09 March, 2016 by Ipstenu
* Translation gaff on one line

= 1.7 =
* 07 March, 2016 by Ipstenu
* Moved to a class instead of badly named functions
* Added in filter `ripm_show_ip` to allow people to filter the IP (and add urls as needed - see FAQ)

= 1.6.1 =
* 22 June, 2014 by Ipstenu
* Typo

= 1.6 =
* 21 June, 2014 by Ipstenu
* Cleanup, function names, readme.