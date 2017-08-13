=== Admin Bar Tools ===
Contributors: GregLone
Tags: admin, admin bar, bar, query, screen, tool, dev, template
Requires at least: 3.1
Tested up to: 4.7.0
Stable tag: trunk
License: GPLv3

Adds some small interesting tools to the admin bar for Developers.

== Description ==
The plugin adds a new tab in your admin bar with simple but useful indications and tools.

* Displays the number of queries in your page and the amount of time to generate the page.
* Displays php memory usage, php memory limit, and php version.
* Display WP_DEBUG state and error reporting value.

**In your site front-end:**

* List the template and all template parts used in the current page (template parts added with <code>get_template_part()</code>). Compatible with WooCommerce.
* WP_Query: click the *$wp_query* item will open a lightbox with the content of $wp_query. Click the lightbox title to reload the value, click outside the lightbox to close it.

**In your site administration:**

* Current screen: a dropdown containing lots of things:
1. Three lists of useful hooks (actions). The indicator to the right of the line tells you how many times the hook has been triggered (a "x" means the plugin doesn't know, because the hook occurs after the admin bar). A "P" means the hook has a parameter: hover it for more details. Click a hook (on its text) to auto-select its code, for example: click *admin_init* to select <code>add_action( 'admin_init', '' );</code>.
2. $...now: this dropdown contains the value of the well-known variables $pagenow, $typenow and $taxnow.
3. Finally, you can know the current page id and base.

You can decide who's gonna use this plugin (go to your profile page for all the settings). This way, the plugin's items won't show up to other users (your client for example).

Version 3 is not tested with old versions of WordPress yet.

= Translations =
* English
* French

= Important note: browser requirement =
You'll need a modern browser to use correctly this plugin. I used some CSS3 features without fallback to keep it simple (you don't work with a dinosaur, right?).

== Installation ==

1. Extract the plugin folder from the downloaded ZIP file.
2. Upload the `sf-admin-bar-tools` folder to your *wp-content/plugins/* directory.
3. Activate the plugin from the "Plugins" page.

== Frequently Asked Questions ==

None, yet.
Check out [the plugin page on my blog](https://www.screenfeed.fr/plugin-wp/sf-admin-bar-tools/) for more infos or tips (sorry guys, it's in French, but feel free to leave a comment in English if you need help).

== Screenshots ==

1. Admin side: list the most important hooks "before headers".
2. Admin side: click a hook, you're ready to copy/paste.
3. Front side: see the <code>WP_Query</code> object value.
4. Front side: see the template and list all template parts used in the current page.
5. By the way, WooCommerce templates are also listed.
6. The settings in your profile page.

== Changelog ==

= 3.0.4 =
* 2016/11/27
* Ready for WP 4.7.
* Fixed php warnings related to the new `WP_Hook` class. Thanks Sébastien Serre for alerting me.

= 3.0.3 =
* 2016/04/03
* Ready for WP 4.5.
* Code quality improvements.

= 3.0.2 =
* 2015/11/07
* Bugfix: avoid annoying message caused by <code>is_embed()</code> in WP 4.4.0.

= 3.0.1 =
* 2015/06/08
* Bugfix: avoid php notices when no template parts are found.
* Improvement: the "Hide WP SEO" checkbox also removes the fields in taxonomy screens now.
* Removed all unused old files. SVN, I hate you so much.

= 3.0 =
* 2015/03/30
* Two years without any update: it's time to rebuild everything from the ground with unicorns and kittens!
* The main focus of this release is to repair broken things and remove obsolete features. It's a major rewrite.
* New: in front-end, list the template and all template parts used in the current page. Compatible with WooCommerce.
* New: if WP SEO is installed, you can remove all its columns et metaboxes (they bore me).
* New: if WPML is installed, you will have a link to the "hidden tools" (dangerous weapons that will blow up your site if you don't know what you do (　ﾟДﾟ)＜!!).
* Removed: the admin bar can no longer be shrinked.
* Removed: coworking feature. Did somebody use it? It was a big mess for only this "tiny" thing.
* Changed: the settings are in your profile page. Some of them are now user preferences.
* Improved: more hooks listed in the admin area.
* Improved: display the number of times the hooks are hit (for real this time).
* Improved: hook code selection.
* Improved: the "disable auto-save" feature now works with new WordPress releases. It also removes auto-lock, auth-check ("XXX is currently editing this post"), and all the things related to Heartbeat.
* Todo: meh.

= 2.1.1 =
* 2013/01/26
* Bugfix in settings page (a missing BR tag)

= 2.1 =
* 2013/01/26
* New: Auto "subscribe" when the plugin is activated. No need to rush to the settings page after activation now.
* New tool: `pre_print_r()`. It's a kind of improved `print_r()` to use where you need: wrap with a `<pre>` tag, choose how to display it (or not) to other users with 2 parameters.
* New: add your own options in the settings page. See the two action hooks 'sf-abt-settings' and 'sf-abt-preferences'. Now there's a new system to deal with the plugin options, see the 'sf_abt_default_options', 'sf_abt_sanitization_functions' and 'sf_abt_sanitize_settings' filters.
* New section "Personal preferences" in the plugin settings page, with the two following options:
* The cowork tree and statuses are refreshed every 5 minutes and on window focus. Now you can disable this.
* When you're on a post edit screen, WordPress autosave your post every minute. Now you can disable this.
* New: Enable the "All Options" options menu.
* Enhancement: if you use the Debug Bar plugin, its admin bar item has an icon on a small screen now (icon from http://gentleface.com/free_icon_set.html).
* Fix: in rares occasions, the admin submenus were displayed under content.
* Fix: use `wp_get_theme()` only if exists (WP 3.4).
* Fix: check WordPress version.

= 2.0.1 =
* 2012/10/17
* Bugfix in settings page

= 2.0 =
* 2012/10/16 - Major release
* Bugfix: jQuery is now launched correctly in themes where it's not already present.
* Enhancement: the main item is now located at the far right of the admin bar. I think it's more convenient for the "retract" functionality.
* Enhancement: now there's a small indicator for the "Fix/unfix admin menu" functionality.
* Enhancement: the $wp_query lightbox works on a 404 page.
* New tool: cowork.
* New indicators: php memory, php version, WP_DEBUG state, error_reporting level, current front-end template.
* New tool: hooks list in administration.
* Thanks a lot to juliobox for some of the awesome ideas :)

= 1.0.1 =
* 2012/06/16
* Minor CSS fix for WP 3.4: the floated admin menu was partially hidden under the admin bar.

= 1.0 =
* 2012/06/10 - First public release

== Upgrade Notice ==
