=== Plugin Name ===
Contributors: timwhitlock
Tags: translation, translators, localization, localisation, l10n, i18n, Gettext, PO, MO, productivity
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 2.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Translate WordPress plugins and themes directly in your browser


== Description ==

Loco Translate provides in-browser editing of WordPress translation files.

It also provides localization tools for developers, such as extracting strings and generating templates.

Features include:

* Built-in translation editor within WordPress admin
* Create and update language files directly in your theme or plugin
* Extraction of translatable strings from your source code
* Native MO file compilation without the need for Gettext on your system
* Support for PO features including comments, references and plural forms
* PO source view with clickable source code references
* Protected language directory for saving custom translations
* Configurable PO file backups
* Built-in WordPress locale codes


Official [Loco](https://localise.biz/) WordPress plugin by <a href="//twitter.com/timwhitlock">@timwhitlock</a> / <a rel="author" href="https://plus.google.com/106703751121449519322">Tim Whitlock</a>


== Installation ==

= Installing manually: =

1. Unzip all files to the `/wp-content/plugins/loco-translate` directory
2. Log into WordPress admin and activate the 'Loco Translate' plugin through the 'Plugins' menu
3. Go to *Loco Translate > Home* in the left-hand menu to start translating

= Basic usage: =

Translators: To translate a theme into your language, follow these steps:

1. Create the protected languages directory at `wp-content/languages/loco/themes`
2. Ensure this directory writeable by the web server
3. Find the theme in the list at *Loco Translate > Themes*
4. Click `+ New language` and follow the on-screen prompts.


Developers: To translate your own theme or plugin for distribution, follow these steps:

1. Create a `languages` subdirectory in your bundle’s root directory
2. Ensure this directory writeable by the web server
3. Find the bundle at either *Loco Translate > Themes* or *Loco Translate > Plugins*
4. Click `+ Create template` and follow the on-screen prompts to extract your strings.
5. Click `+ New language` and follow the on-screen prompts to add your own translations.



More information on using the plugin is [available here](https://localise.biz/wordpress/plugin).


== Frequently Asked Questions ==

= How do I use it? = 

Try our [beginner's guide](https://localise.biz/wordpress/plugin/beginners), or the more [technical overview](https://localise.biz/wordpress/plugin/overview) if you’re familiar with WordPress localization.

= How do I get help? =

Please see [getting help with Loco Translate](https://localise.biz/wordpress/plugin/support) and note that personal support by email is not available for this plugin. 
Help is provided via the [plugin support forum](https://wordpress.org/support/plugin/loco-translate) only.



== Screenshots ==

1. Translating strings in the browser with the Loco PO Editor
2. Showing translation progress for theme language files



== Changelog ==

= 2.0.5 =
* Deferred missing tokenizer warning
* Allows editing of files in unconfigured sets
* Added maximum PHP file size for string extraction
* Display of PHP fatal errors during Ajax

= 2.0.4 =
* Reduced session failures to debug notices
* Added wp_roles support for WP < 4.3
* Fixed domain listener bugs

= 2.0.3 =
* Added support for Windows servers
* Removed incomplete config warning on bundle overview

= 2.0.2 =
* Fixed bug when absolute path used to get plugins
* Added loco_plugins_data filter
* Added theme Template Name header extraction
* Minor copy amends

= 2.0.1 =
* Added help link in settings page
* Fixed opendir warnings in legacy code
* Catching session errors during init
* Removing meta row link when plugin not found

= 2.0.0 =
* First release of completely rebuilt version 2


== Upgrade Notice ==

= 2.0.5 =
* Various bug fixes and improvements


== More info ==

* [About the plugin](https://localise.biz/wordpress/plugin/beginners)
* [Beginner's guide to translating a theme](https://localise.biz/wordpress/plugin/beginners)
* [Technical overview](https://localise.biz/wordpress/plugin/overview)
* [Getting help](https://localise.biz/wordpress/plugin/support)

== Coming soon ==

These features are on our todo list. There's no particular timeframe for any of them and they're in no particular order:

* Integration with Google and Bing for automatic translation
* Integration with Loco API for collaborative translation
* Screens showing installed bundles per language


== Keyboard shortcuts ==

The PO file editor supports the following keyboard shortcuts for faster translating:

* Done and Next: `Ctrl ↵`
* Next string: `Ctrl ↓`
* Previous string: `Ctrl ↑`
* Next untranslated: `Shift Ctrl ↓`
* Previous untranslated: `Shift Ctrl ↑`
* Copy from source text: `Ctrl B`
* Clear translation: `Ctrl K`
* Toggle Fuzzy: `Ctrl U`
* Save PO / compile MO: `Ctrl S`

Mac users can use ⌘ Cmd instead of Ctrl.


== Translators ==

Many thanks to the translators of version 1.

Please don’t submit translations for version 2 yet. English strings are still being finalised.
