**1.3.3**

* **Fix**
	* Handled dependency to icl_js_escape() function
	* Added support for converting links to Custom Post Types with translated slugs
	* mysql_* functions doesn't show deprecated notice when PHP >= 5.5
	* Several fixes to achieve compatibility with WordPress 3.9
	* Updated links to wpml.org
	* Handled case where ICL_PLUGIN_PATH constant is not defined (i.e. when plugin is activated before WPML core)
	* Fixed problem with Sticky Links and Custom Taxonomies
	* Fixed problem with additional language code in Sticky Links
	* Fixed Korean locale in .mo file name

**1.3.2**

* **Fix**
	* Handled dependency from SitePress::get_setting()
	* Removed dependency to SitePress when instantiating the class
	* Updated translations
	* Fixed possible javascript exception in Firefox, when using event.preventDefault();

**1.3.1**

* **Features**
	* Added WPML capabilities (see online documentation)
	* SSL support for included CSS and Javascripts now is properly handled
	* Support for links to custom post type is working now as expected
	* Links was not changed into sticky when default language was not English. Now it's fixed.
