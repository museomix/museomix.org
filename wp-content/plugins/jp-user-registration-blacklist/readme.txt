=== Plugin Name ===
Contributors: 5x5m2bswv2
Donate link: 
Tags: user registration, spam, blacklist
Requires at least: 3.0.1
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Prevent User Registration Spam, and Track New User IP address

== Description ==

### Update: 6/2015 ###
Changed e-mail token and math problem confirmation logic.  Previously, everything happened all at once, and the user would get prompted mutliple times for the math problem.
Now, the user must answer the math problem first, before the e-mail token is sent.  Once the math problem is correctly answered within the session, it isn't asked again.  The user must now enter the correct token, which results in successful registration.
SPECIAL THANKS TO OLEG M. FOR HELPING ME IDENTIFY, FIX, AND TEST THE ERROR!!

### Update: 5/2015 ###
Added E-mail confirmation token.  Prevents registration until user enters a token sent via e-mail.

### Features: ###

* Prevent users from registering, if their IP or e-mail address is listed in the "Comments" blacklist (Settings..Discussion)
* E-mails user a 4-digit token, and requires the user to enter the token in order to register.
* Users must solve a simple math problem (Add two one-digit numbers).
* Places user IP address in "Website" field.

This is a super-simple user registration spam countermeasure.  I searched for a plugin that was SIMPLE and EFFECTIVE.  I looked at quite a few plugins that promised the desired effect, but were either cumbersome, or included too many unneeded or unwanted features.  Likewise, there are some very simple plugins that are less-than-effective.


### This plugin is VERY simple: ###

If the user's IP or e-mail address is listed in the "Discussion" comments blacklist, it prevents the user from registering.  This functionality should really be built in to WordPress, so, you're welcome.

When the user registers, they are presented with a simple math problem - adding a 3-digit number to a 1-digit number.  99% of the "user reg spam" is based on bots being able to attack the basic WordPress registration form.  By adding even a simple math problem, most bots will fail, removing your site as a target of opportunity.  Criminals go after what's easy - if you make it slightly more difficult for them, they will go after someone else.

Once the user solves the math problem, they are sent a 4 digit token via e-mail, and must enter that token to continue registration.

Finally, knowing the location from where your users register allows you to more effectively evaluate and block the source.  This plugin adds the user's IP address (at the time of registration)  to the "Website" field.

Go to http://whois.arin.net to find out who they are.  If you decide to block the IP,  add the IP address, part of the IP address, or e-mail domain to the "Discussion" comments blacklist, and ANY user registrations from an IP address matching that pattern will be blocked.


== Installation ==

### Installation: ###

1. Upload `JPUserRegTools.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in the WordPress Dashboard.  Click "Activate" underneath "JP User Registration Blacklist".
1. Add full or partial IP addresses or e-mail addresses to the Settings..Discussion `Comments Blacklist`, one per line.
1. NOTE:  This plugin works with the default settings.  No configuration is required.  Customize settings by clicking the "Settings" link underneath "JP User Registration Blacklist" plugin, on the Plugins page.

### Example: ###

Adding 176.24. to the comments blacklist blocks:

* 176.24.anything.anything
* 176.24.1.1
* 176.24.1.254
* 176.24.255.1
* 176.24.255.254
* Etc...

Adding 176.24.10 (no trailing dot) to the comments blacklist blocks:
* 176.24.10.x
* 176.24.10x.x
* 176.24.100.x
* 176.24.101.x
* Etc...

Go to [the ARIN website](http://whois.arin.net) to figure out what the correct IP range is.

Start by just blocking a single IP address.  If you keep getting user registrations from other, similar IP addresses, block the whole range!

Adding .pl to the commets blacklist blocks:
* anyone@anydomain.pl
* Someone@somedomain.pl
* Etc...

### Blacklist ###
Starting with 1.6.1, I will be providing regular updates for blacklisted IP addresses and networks that I've accumulated.

Copy the entries below to the *Settings* ... *Discussions* ... *Comment Blacklist* section, scroll down, and click *Save*

5.157.  
5.165.  
5.226.  
5.227.  
5.228.  
5.229.  
5.230.  
5.231.  
5.255.  
14.102.109.  
23.27.  
23.28.  
23.29.  
23.89.  
23.94.  
23.95.  
23.229.  
23.232.  
23.245.  
23.246.  
23.247.  
31.132.4.  
37.59.  
37.215.  
45.58.50.  
45.61.46.  
46.102.98.  
50.2.  
50.3.  
59.174.  
66.118.  
66.248.  
69.12.  
74.211.96.  
78.157.212.  
83.143.242.  
89.36.  
89.44.  
89.47.  
93.81.  
93.118.  
94.249.242.  
95.59.108.  
104.128.  
104.140.  
104.206.  
104.247.  
107.168.  
108.62.102.  
108.179.224.  
109.169.5.  
109.200.20.  
110.89.  
113.196.  
117.26.  
117.174.  
117.211.  
136.0.  
142.4.211.  
155.94.  
158.222.  
162.244.  
162.245.  
167.160.  
172.245.  
173.213.  
173.232.  
173.234.  
178.162.  
183.11.  
183.207.  
185.24.  
188.68.  
188.208.  
188.209.  
188.210.  
188.211.  
188.212.  
188.213.  
188.214.  
188.215.  
188.240.  
190.151.  
191.101.243.  
192.3.  
192.77.  
192.99.  
192.124.  
192.125.  
192.126.  
192.127.  
192.128.  
192.171.  
192.200.  
192.227.  
192.230.  
192.240.  
192.255.  
193.254.251.  
195.154.  
198.49.  
198.50.  
198.51.  
198.52.  
198.53.  
198.54.  
198.55.  
198.56.  
202.69.  
202.99.172.  
204.44.112.  
204.14.87.  
209.141.60.  
209.161.96.  
213.108.202.  
beselfsmart.com  
emailind.com  
mail.bg  
producrate.com  
pockmail.com  



== Frequently Asked Questions ==

= How do I configure this plugin? =

From the dashboard, select "Plugins".  Underneath "JP User Registration Blacklist", click "Settings".

= What does the user see? =

Check out the screen shots.  
* If they don't answer the math problem correctly, the registration is denied, with a simple message.<br>
* If their IP or e-mail address is blocked, they get a generic "try again" message.  This is intentional, to avoid disclosing WHY they are being blocked, making it harder to bypass.<br>
* If they fail the math problem AND their IP is blocked, they get both messages.

= Why does the user's IP start with http:// ? =

That's a WordPress thing.  Ignore the http://.  I may decide to add a custom field later, but for now, simple is better.

== Screenshots ==

1. #6/19/2014: Spammer, caught live!#
2. #I don't think "Tanesha Kessler" lives in Romania#
3. #BLOCK the whole network#
4. This is what the user sees during registration.  Note the math problem in line 3.
5. This is what the user sees if registration fails.  Note that the red text in the upper-left is NOT displayed
6. Once successfully registered, the user's IP address appears in the website field.  Note that the red text is NOT displayed.
7. Admin options screen

== Changelog ==

= 1.7.2 =
* 6/2015 - Fixed the sequencing so that the user must FIRST solve the math problem correclty, THEN gets e-mailed and prompted for the token.

= 1.7 =
* 5/2015 - Added E-mail registration token

= 1.6 =
* 8/29/2014 - Updated math problem to be slightly more complex - 3digit + 1digit (previously 1digit + 1digit).  I have seen a slight uptick in registrations from hosted server locations, leading me to think that there are bots out there that look for, and solve the 1digit+1digit math problem.

= 1.5 =
* 6/12/2014 - Randomly-generated seed for the math problem (prevents hacking), Randomly-generated math problem field name (further prevents bots), Admin options panel, ability to customize error messages.

= 1.4 =
* 6/5/2014 - Math problem is now randomly generated (dynamic).

= 1.3 =
* 5/11/2014 - Added e-mail address patten matching.

= 1.2 =
* 5/2/2014 - Initial version.

== Upgrade Notice ==

= 1.6 =
Upgrade, to get:
* Slightly more complex math problem (3digit + 1digit)

== Configuration ==

### To Configure the Plugin: ###

NOTE:  NO CONFIGURATION IS REQUIRED.  This plugin is fully-functional using the default values.

In the Plugins page, click "Settings" underneath the "JP User Registration Blacklist" plugin.

* Seed:  This value determines how the answer to the math problem is masked.  Periodically change this, to keep the spammers and criminals at bay.  The initial value is randomly-generated.

* Failed Math Response:  Error message displayed to the user, if they fail to correctly solve the math problem.

* Rejected IP or E-mail:  Error message displayed to the user, if their IP or e-mail is blocked.  Keep this simple and generic, to keep them from knowing why they are being blocked.

* Form field name for math problem:  This field name contains the user's answer to the math problem.  Periodically change this, to keep the bots away.  The initial value is randomly-generated.


### To Block an IP address ###
1.  In the WordPress Dashboard, go to "Settings...Discuss"
1.  To block all or part of an IP address, add it on its own line to "Comments Blacklist"
1.  To block all or part of an e-mail address, add it on its own line to "Comments Blacklist"
1.  Click "Save"
(For more details, see Examples)

