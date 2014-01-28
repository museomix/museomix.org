<?php

/*
Plugin Name: WP Realtime Sitemap
Plugin URI: http://goo.gl/ri9xU
Description: Adds a sitemap to your Wordpress blog that is always up-to-date. Add `[wp-realtime-sitemap]` to any page or post and the site map will be added there. Use Settings->WP Realtime Sitemap to set options.
Version: 1.5.4
Author: Daniel Tweedy
Author URI: http://goo.gl/jdOfL
License: GPL2
*/

/*  Copyright 2010  Daniel Tweedy  (contact me : http://goo.gl/Jqrg6)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('WPRealtimeSitemap_Version', '1.5.4');

if (!class_exists('WPRealtimeSitemap')) {
	class WPRealtimeSitemap {
		var $plugin_base = '';

		function WPRealtimeSitemap() { //constructor
			// Add Init
			add_action('init', array(&$this, 'addInit'));

			// Add Meta
			add_filter('plugin_row_meta', array(&$this, 'addMeta'), 10, 2);

			// Add Action Links
			add_filter('plugin_action_links', array(&$this, 'addActionLinks'), 10, 2);

			// Add Help
			add_filter('contextual_help', array(&$this, 'addHelp'), 10, 2 );

			// Add filter for WordPress 2.8 changed backend box system !
			add_filter('screen_layout_columns', array(&$this, 'addScreenLayoutColumns'), 10, 2);

			// Add Settings Panel
//			add_action('admin_head', array($this, 'someFunctionHere'));
			add_action('admin_menu', array($this, 'addOptions'));
			add_action('admin_init', array($this, 'addOptionsInit'));
			add_action('admin_print_styles', array($this, 'addAdminPrintCSS'));
			add_action('admin_print_scripts', array($this, 'addAdminPrintJS'));

			// Add Form Shortcode
			add_shortcode('wp-realtime-sitemap', array($this, 'showOutput'));

			$options = get_option('plugin_wp_realtime_sitemap_settings');

			// If the version number is set and is not the latest, then call the upgrade function
			if (false !== get_option('plugin_wp_realtime_sitemap_version') && get_option('plugin_wp_realtime_sitemap_version') !== WPRealtimeSitemap_Version) {
				add_action('admin_notices', array($this, 'upgradeSettingsNotice'), 12);
			}

			// Install Settings - Doesn't fire on Updates!!
			register_activation_hook(__FILE__, array(&$this, 'installSettings'));

			// Uninstall Settings
			register_deactivation_hook(__FILE__, array(&$this, 'uninstallSettings'));
		}

		function addInit() {
			$this->plugin_base_url = WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), '', plugin_basename(__FILE__));
			$this->plugin_base_dir = WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), '', plugin_basename(__FILE__));

			// Localization
			load_plugin_textdomain('wp-realtime-sitemap', false, dirname(plugin_basename(__FILE__)) . '/language');
		}

		function addMeta($links, $file) {
			$plugin_file = basename(__FILE__);
			if (basename($file) == $plugin_file) {
				$faq_link = '<a href="http://goo.gl/QNiRH">' . __('FAQ', 'wp-realtime-sitemap') . '</a>';
				array_unshift($links, $faq_link);
				$support_link = '<a href="http://goo.gl/kosme">' . __('Support', 'wp-realtime-sitemap') . '</a>';
				array_unshift($links, $support_link);
				$donate_link = '<a href="http://goo.gl/ddoa5">' . __('Donate with PayPal', 'wp-realtime-sitemap') . '</a>';
				array_unshift($links, $donate_link);
				$amazon_link = '<a href="http://goo.gl/yrM92">' . __('Amazon Wishlist', 'wp-realtime-sitemap') . '</a>';
				array_unshift($links, $amazon_link);
				$amazon_link = '<a href="http://goo.gl/Jqrg6">' . __('Contact Me', 'wp-realtime-sitemap') . '</a>';
				array_unshift($links, $amazon_link);
			}
			return $links;
		}

		function addActionLinks($links, $file) {
			$plugin_file = basename(__FILE__);
			if (basename($file) == $plugin_file) {
				$settings_link = '<a href="options-general.php?page='.$plugin_file.'">' . __('Settings', 'wp-realtime-sitemap') . '</a>';
				array_unshift($links, $settings_link);
			}
			return $links;
		}

		function addHelp( $help, $screen ) {
			if ( $screen == 'settings_page_wp-realtime-sitemap' ) {
				$help .= '<h5>' . __('WP Realtime Sitemap Help', 'wp-realtime-sitemap') . '</h5><div class="metabox-prefs"><p>';
				$help .= '<a href="http://goo.gl/yrM92">' . __('Amazon Wishlist', 'wp-realtime-sitemap') . '</a><br />';
				$help .= '<a href="http://goo.gl/ddoa5">' . __('Donate with PayPal', 'wp-realtime-sitemap').'</a><br />';
				$help .= '<a href="http://goo.gl/kosme">' . __('Support', 'wp-realtime-sitemap').'</a><br />';
				$help .= '<a href="http://goo.gl/QNiRH">' . __('FAQ', 'wp-realtime-sitemap').'</a><br />';
				$help .= '<a href="http://goo.gl/bRO8F">' . __('Home Page', 'wp-realtime-sitemap').'</a><br />';
				$help .= '<a href="http://goo.gl/Jqrg6">' . __('Contact Me', 'wp-realtime-sitemap') . '</a><br />';
				$help .= __('Please read the plugin information and FAQ, before asking a question.', 'wp-realtime-sitemap') . '</p></div>';
			}

			return $help;
		}

		// For WordPress 2.8 we have to tell, that we support 2 columns !
		function addScreenLayoutColumns($columns, $screen) {
			// bugfix: $this->pagehook is not valid because it will be set at hook 'admin_menu' but
			// multisite pages or user dashboard pages calling different menu an menu hooks!
			if (!defined('WP_NETWORK_ADMIN') && !defined('WP_USER_ADMIN')) {
				if ($screen == $this->pagehook) {
					$columns[$this->pagehook] = 2;
				}
			}

			return $columns;
		}

		function addSubMenu() {
			$plugin_file = basename(__FILE__);

			$settingsCSS		= (!isset($_GET['sub'])) ? ' class="current"' : '';
			$supportCSS		= (!isset($_GET['sub']) && $_GET['sub'] == 'support') ? ' class="current"' : '';
			$translationsCSS	= (!isset($_GET['sub']) && $_GET['sub'] == 'translations') ? ' class="current"' : '';

			$sublinks = '<ul class="subsubsub">';
			$sublinks .= '<li><a href="options-general.php?page=' . $plugin_file . '"' . $settingsCSS . '>' . __('Settings', 'wp-realtime-sitemap') . '</a> |</li>';
			$sublinks .= '<li><a href="options-general.php?page=' . $plugin_file . '&amp;sub=support"' . $supportCSS . '>' . __('Support', 'wp-realtime-sitemap') . '</a> |</li>';
			$sublinks .= '<li><a href="options-general.php?page=' . $plugin_file . '&amp;sub=translations"' . $translationsCSS . '>' . __('Translations', 'wp-realtime-sitemap') . '</a> |</li>';
			$sublinks .= '</ul><br /><br />';

			return $sublinks;
		}

		function addAdminPrintCSS() {
			wp_register_style('admin_style', plugins_url('/css/admin-style.css' , __FILE__ ));
			wp_enqueue_style('admin_style');
		}

		function addAdminPrintJS() { }

		function addOptions() {
			$this->pagehook = add_options_page(__('WP Realtime Sitemap Options', 'wp-realtime-sitemap'), __('WP Realtime Sitemap', 'wp-realtime-sitemap'), 'manage_options', basename(__FILE__), array(&$this, 'showAdminScreen'));
		}

		function showAdminScreen() {
			return $this->_optionsForm();
		}

		function addOptionsInit() {
			// If the version number is set and is not the latest, then call the upgrade function
			if (false !== get_option('plugin_wp_realtime_sitemap_version') && get_option('plugin_wp_realtime_sitemap_version') !== WPRealtimeSitemap_Version) {
				$this->installSettings();

				update_option('plugin_wp_realtime_sitemap_version', WPRealtimeSitemap_Version);
			}

			register_setting('update_settings', 'plugin_wp_realtime_sitemap_settings', array($this, '_formValidate'));

			add_settings_section('menu_settings', __('Menu Settings', 'wp-realtime-sitemap'), array($this, 'menu_section_text'), __FILE__);
			add_settings_field('menu_id', __('Menu ID', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'menu_settings', array('dbfield' => 'menu_id', 'section' => 'menu'));

			add_settings_section('page_settings', __('Page Settings', 'wp-realtime-sitemap'), array($this, 'page_section_text'), __FILE__);
			add_settings_field('page_sort_column', __('Order By', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'page_settings', array('dbfield' => 'page_sort_column', 'section' => 'page'));
			add_settings_field('page_sort_order', __('Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'page_settings', array('dbfield' => 'page_sort_order', 'section' => 'page'));
			add_settings_field('page_exclude', __('Exclude IDs', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'page_settings', array('dbfield' => 'page_exclude', 'section' => 'page'));
			add_settings_field('page_depth', __('Hierarchy Depth', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'page_settings', array('dbfield' => 'page_depth', 'section' => 'page'));
			add_settings_field('page_show_date', __('Display Date', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'page_settings', array('dbfield' => 'page_show_date', 'section' => 'page'));
// NOTE: this is currently not working, see http://core.trac.wordpress.org/ticket/10745
//			add_settings_field('page_number', __('Limit', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'page_settings', array('dbfield' => 'page_number', 'section' => 'page'));

			add_settings_section('post_settings', __('Post & Custom Post Type Settings', 'wp-realtime-sitemap'), array($this, 'post_section_text'), __FILE__);
			add_settings_field('post_orderby', __('Order By', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'post_settings', array('dbfield' => 'post_orderby', 'section' => 'post'));
			add_settings_field('post_order', __('Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'post_settings', array('dbfield' => 'post_order', 'section' => 'post'));
			add_settings_field('post_exclude', __('Exclude IDs', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'post_settings', array('dbfield' => 'post_exclude', 'section' => 'post'));
			add_settings_field('post_show_date', __('Display Date', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'post_settings', array('dbfield' => 'post_show_date', 'section' => 'post'));
			add_settings_field('post_numberposts', __('Limit', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'post_settings', array('dbfield' => 'post_numberposts', 'section' => 'post'));
			add_settings_field('post_show_categories', __('Display Categories', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'post_settings', array('dbfield' => 'post_show_categories', 'section' => 'post'));

			add_settings_section('archive_settings', __('Archive Settings', 'wp-realtime-sitemap'), array($this, 'archive_section_text'), __FILE__);
			add_settings_field('archive_type', __('Type', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'archive_settings', array('dbfield' => 'archive_type', 'section' => 'archive'));
			add_settings_field('archive_limit', __('Limit', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'archive_settings', array('dbfield' => 'archive_limit', 'section' => 'archive'));
			add_settings_field('archive_show_post_count', __('Post Count', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'archive_settings', array('dbfield' => 'archive_show_post_count', 'section' => 'archive'));

			add_settings_section('category_settings', __('Category Settings', 'wp-realtime-sitemap'), array($this, 'category_section_text'), __FILE__);
			add_settings_field('category_tagcloud', __('Show as a Tag Cloud', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'category_settings', array('dbfield' => 'category_tagcloud', 'section' => 'category'));
			add_settings_field('category_orderby', __('Order By', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'category_settings', array('dbfield' => 'category_orderby', 'section' => 'category'));
			add_settings_field('category_order', __('Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'category_settings', array('dbfield' => 'category_order', 'section' => 'category'));
			add_settings_field('category_show_post_count', __('Post Count', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'category_settings', array('dbfield' => 'category_show_post_count', 'section' => 'category'));
			add_settings_field('category_hide_empty', __('Hide Empty', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'category_settings', array('dbfield' => 'category_hide_empty', 'section' => 'category'));
			add_settings_field('category_exclude', __('Exclude IDs', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'category_settings', array('dbfield' => 'category_exclude', 'section' => 'category'));
			add_settings_field('category_number', __('Limit', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'category_settings', array('dbfield' => 'category_number', 'section' => 'category'));
			add_settings_field('category_depth', __('Hierarchy Depth', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'category_settings', array('dbfield' => 'category_depth', 'section' => 'category'));

			add_settings_section('tag_settings', __('Tag Settings', 'wp-realtime-sitemap'), array($this, 'tag_section_text'), __FILE__);
			add_settings_field('tags_tagcloud', __('Show as a Tag Cloud', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'tag_settings', array('dbfield' => 'tags_tagcloud', 'section' => 'tag'));
			add_settings_field('tags_orderby', __('Order By', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'tag_settings', array('dbfield' => 'tags_orderby', 'section' => 'tag'));
			add_settings_field('tags_order', __('Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'tag_settings', array('dbfield' => 'tags_order', 'section' => 'tag'));
			add_settings_field('tags_show_post_count', __('Post Count', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'tag_settings', array('dbfield' => 'tags_show_post_count', 'section' => 'tag'));
			add_settings_field('tags_hide_empty', __('Hide Empty', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'tag_settings', array('dbfield' => 'tags_hide_empty', 'section' => 'tag'));
			add_settings_field('tags_exclude', __('Exclude IDs', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'tag_settings', array('dbfield' => 'tags_exclude', 'section' => 'tag'));
			add_settings_field('tags_number', __('Limit', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'tag_settings', array('dbfield' => 'tags_number', 'section' => 'tag'));

			add_settings_section('header_settings', __('Header Settings', 'wp-realtime-sitemap'), array($this, 'header_section_text'), __FILE__);
			add_settings_field('menu_header', __('Menu Header', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'header_settings', array('dbfield' => 'menu_header', 'section' => 'header'));
			add_settings_field('pages_header', __('Pages Header', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'header_settings', array('dbfield' => 'pages_header', 'section' => 'header'));
			add_settings_field('posts_header', __('Posts Header', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'header_settings', array('dbfield' => 'posts_header', 'section' => 'header'));
			add_settings_field('archives_header', __('Archives Header', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'header_settings', array('dbfield' => 'archives_header', 'section' => 'header'));
			add_settings_field('categories_header', __('Categories Header', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'header_settings', array('dbfield' => 'categories_header', 'section' => 'header'));
			add_settings_field('tags_header', __('Tags Header', 'wp-realtime-sitemap'), array($this, '_formTextInput'), __FILE__, 'header_settings', array('dbfield' => 'tags_header', 'section' => 'header'));

			add_settings_section('display_settings', __('Display Settings', 'wp-realtime-sitemap'), array($this, 'display_section_text'), __FILE__);
			add_settings_field('show_menu', __('Show Menu', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'display_settings', array('dbfield' => 'show_menu', 'section' => 'display'));
			add_settings_field('show_pages', __('Show Pages', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'display_settings', array('dbfield' => 'show_pages', 'section' => 'display'));
			add_settings_field('show_posts', __('Show Posts', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'display_settings', array('dbfield' => 'show_posts', 'section' => 'display'));
			add_settings_field('show_custom_post_types', __('Show ALL Custom Post Types', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'display_settings', array('dbfield' => 'show_custom_post_types', 'section' => 'display'));
			add_settings_field('show_archives', __('Show Archives', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'display_settings', array('dbfield' => 'show_archives', 'section' => 'display'));
			add_settings_field('show_categories', __('Show Categories', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'display_settings', array('dbfield' => 'show_categories', 'section' => 'display'));
			add_settings_field('show_tags', __('Show Tags', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'display_settings', array('dbfield' => 'show_tags', 'section' => 'display'));
			add_settings_field('show_promote', __('Help Promote', 'wp-realtime-sitemap') . ' ' . __('WP Realtime Sitemap', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'display_settings', array('dbfield' => 'show_promote', 'section' => 'display'));

			add_settings_section('order_settings', __('Order Settings', 'wp-realtime-sitemap'), array($this, 'order_section_text'), __FILE__);
			add_settings_field('first_order', __('1st Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'order_settings', array('dbfield' => 'first_order', 'section' => 'order'));
			add_settings_field('second_order', __('2nd Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'order_settings', array('dbfield' => 'second_order', 'section' => 'order'));
			add_settings_field('third_order', __('3rd Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'order_settings', array('dbfield' => 'third_order', 'section' => 'order'));
			add_settings_field('fourth_order', __('4th Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'order_settings', array('dbfield' => 'fourth_order', 'section' => 'order'));
			add_settings_field('fifth_order', __('5th Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'order_settings', array('dbfield' => 'fifth_order', 'section' => 'order'));
			add_settings_field('sixth_order', __('6th Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'order_settings', array('dbfield' => 'sixth_order', 'section' => 'order'));
			add_settings_field('seventh_order', __('7th Order', 'wp-realtime-sitemap'), array($this, '_formSelectInput'), __FILE__, 'order_settings', array('dbfield' => 'seventh_order', 'section' => 'order'));
		}

		function menu_section_text($args) {
			echo '<p>' . __('Menu ID should contain the ID of the menu you wish to show on the sitemap.  If left blank will show the last menu created in Appearance -> Menus.', 'wp-realtime-sitemap') . '</p>';
		}

		function page_section_text($args) {
			echo '<p>' . __('Exclude IDs must be either left blank, or be a comma seperated list of IDs.', 'wp-realtime-sitemap') . '</p>';
		}

		function post_section_text() {
			echo '<p>' . __('Exclude IDs must be either left blank, or be a comma seperated list of IDs.', 'wp-realtime-sitemap') . '</p>';
			echo '<p>' . __('Display Date is to show the date that the post itself was created/published.', 'wp-realtime-sitemap') . '</p>';
			echo '<p>' . __('Limit is used to apply a limit as to how many posts should be shown on the sitemap, useful for if you have a very large blog and as such a large number of posts, use -1 to show all posts and not apply a limit.', 'wp-realtime-sitemap') . '</p>';
		}

		function archive_section_text($args) {
			echo '<p>' . __('Limit is used to apply a limit as to how many archives should be shown on the sitemap, useful for if you have a very large blog and as such a large number of archives, leave blank to show all archives and not apply a limit.', 'wp-realtime-sitemap') . '</p>';
                }

		function category_section_text($args) {
			echo '<p>' . __('All options apply to the tag cloud, unless otherwise explicitly stated.', 'wp-realtime-sitemap') . '</p>';
			echo '<p>' . __('Exclude IDs must be either left blank, or be a comma seperated list of IDs.', 'wp-realtime-sitemap') . '</p>';
			echo '<p>' . __('Limit is used to apply a limit as to how many categories should be shown on the sitemap, useful for if you have a very large blog and as such a large number of categories, use 0 to show all categories and not apply a limit.', 'wp-realtime-sitemap') . '</p>';
		}

		function tag_section_text($args) {
			echo '<p>' . __('All options apply to the tag cloud, unless otherwise explicitly stated.', 'wp-realtime-sitemap') . '</p>';
			echo '<p>' . __('Exclude IDs must be either left blank, or be a comma seperated list of IDs.', 'wp-realtime-sitemap') . '</p>';
			echo '<p>' . __('Limit is used to apply a limit as to how many tags should be shown on the sitemap, useful for if you have a very large blog and as such a large number of tags, use 0 to show all tags and not apply a limit.', 'wp-realtime-sitemap') . '</p>';
		}

		function header_section_text() {
			echo '<p>' . __('This section is optional, you can change the names of the sections if you wish from the defaults.  Defaults are used when the below options are left blank/empty.', 'wp-realtime-sitemap') . '</p>';
		}

		function display_section_text() {
			echo '<p>' . __('Choose what you would like to be displayed on your site map.', 'wp-realtime-sitemap') . '</p>';
		}

		function order_section_text($args) {
			echo '<p>' . __('Some common sense is required when choosing the order below, it is possible to for example choose Pages, for 1st, 2nd, 3rd and so on, so this will then option the pages on the sitemap 3 times.  Please make an effort to choose each possible option only once.', 'wp-realtime-sitemap') . '</p>';
		}

		function _formValidDefaults($args) {
			$values	= false;

			switch($args['dbfield']) {
				case 'menu_id':
					$values = array('alphanum' => 'alphanum');
					break;

				case 'page_sort_column':
					$values = array(
						'post_title'	=> __('Alphabetically (by title) (Default)', 'wp-realtime-sitemap'),
						'menu_order'	=> __('Page order', 'wp-realtime-sitemap'),
						'post_date'	=> __('Creation time', 'wp-realtime-sitemap'),
						'post_modified'	=> __('Time last modified', 'wp-realtime-sitemap'),
						'ID'		=> __('Numeric Page ID', 'wp-realtime-sitemap'),
						'post_author'	=> __('Page author', 'wp-realtime-sitemap'),
						'post_name'	=> __('Alphabetically (by post slug)', 'wp-realtime-sitemap'),
					);
					break;

				case 'page_sort_order':
					$values = array(
						'ASC'		=> __('Sort from lowest to highest (Default)', 'wp-realtime-sitemap'),
						'DESC'		=> __('Sort from highest to lowest', 'wp-realtime-sitemap'),
					);
					break;

				case 'page_depth':
					$values = array(
						'0'		=> __('Displays pages at any depth and arranges them hierarchically in nested lists (Default)', 'wp-realtime-sitemap'),
						'-1'		=> __('Displays pages at any depth and arranges them in a single, flat list', 'wp-realtime-sitemap'),
						'1'		=> __('Displays top-level Pages only', 'wp-realtime-sitemap'),
					);
					break;

				case 'page_show_date':
					$values = array(
						''		=> __('Display no date (Default)', 'wp-realtime-sitemap'),
						'modified'	=> __('Display the date last modified', 'wp-realtime-sitemap'),
						'created'	=> __('Display the date first created', 'wp-realtime-sitemap'),
					);
					break;

/*				case 'page_number':
					$values = array('numeric' => 'numeric');
					break; */

				case 'post_orderby':
					$values = array(
						'post_date'	=> __('Sort by post date (Default)', 'wp-realtime-sitemap'),
						'author'	=> __('Sort by the numeric author IDs', 'wp-realtime-sitemap'),
						'category'	=> __('Sort by the numeric category IDs', 'wp-realtime-sitemap'),
						'content'	=> __('Sort by content', 'wp-realtime-sitemap'),
						'date'		=> __('Sort by creation date', 'wp-realtime-sitemap'),
						'ID'		=> __('Sort by numeric Post ID', 'wp-realtime-sitemap'),
						'modified'	=> __('Sort by last modified date', 'wp-realtime-sitemap'),
						'name'		=> __('Sort by stub', 'wp-realtime-sitemap'),
						'parent'	=> __('Sort by parent ID', 'wp-realtime-sitemap'),
						'password'	=> __('Sort by password', 'wp-realtime-sitemap'),
						'rand'		=> __('Randomly sort results', 'wp-realtime-sitemap'),
						'status'	=> __('Sort by status', 'wp-realtime-sitemap'),
						'title'		=> __('Sort by title', 'wp-realtime-sitemap'),
						'type'		=> __('Sort by type', 'wp-realtime-sitemap'),
					);
					break;

				case 'post_order':
					$values = array(
						'ASC'		=> __('Sort from lowest to highest (Default)', 'wp-realtime-sitemap'),
						'DESC'		=> __('Sort from highest to lowest', 'wp-realtime-sitemap'),
					);
					break;

				case 'post_show_date':
					$values = array(
						'Yes'		=> __('Yes/On', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'post_numberposts':
					$values = array('numeric' => 'numeric');
					break;

				case 'post_show_categories':
					$values = array(
						'Yes'		=> __('Yes/On', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'archive_type':
					$values = array(
						'yearly'	=> __('Yearly', 'wp-realtime-sitemap'),
						'monthly'	=> __('Monthly (Default)', 'wp-realtime-sitemap'),
						'daily'		=> __('Daily', 'wp-realtime-sitemap'),
						'weekly'	=> __('Weekly', 'wp-realtime-sitemap'),
					);
					break;

				case 'archive_limit':
					$values = array('numeric' => 'numeric');
					break;

				case 'archive_show_post_count':
					$values = array(
						true		=> __('True (Default)', 'wp-realtime-sitemap'),
						false		=> __('False', 'wp-realtime-sitemap'),
					);
					break;

				case 'category_tagcloud':
					$values = array(
						'Yes'		=> __('Yes/On', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'category_orderby':
					$values = array(
						'ID'		=> __('ID', 'wp-realtime-sitemap'),
						'name'		=> __('Name (Default)', 'wp-realtime-sitemap'),
						'slug'		=> __('Slug', 'wp-realtime-sitemap'),
						'count'		=> __('Count', 'wp-realtime-sitemap'),
						'term_group'	=> __('Term Group', 'wp-realtime-sitemap'),
					);
					break;

				case 'category_order':
					$values = array(
						'ASC'		=> __('Sort from lowest to highest (Default)', 'wp-realtime-sitemap'),
						'DESC'		=> __('Sort from highest to lowest', 'wp-realtime-sitemap'),
					);
					break;

				case 'category_show_post_count':
					$values = array(
						true		=> __('True', 'wp-realtime-sitemap'),
						false		=> __('False (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'category_hide_empty':
					$values = array(
						true		=> __('True (Default)', 'wp-realtime-sitemap'),
						false		=> __('False', 'wp-realtime-sitemap'),
					);
					break;

				case 'category_number':
					$values = array('numeric' => 'numeric');
					break;

				case 'category_depth':
					$values = array(
						'0'		=> __('All Categories and child Categories (Default)', 'wp-realtime-sitemap'),
						'-1'		=> __('All Categories displayed in flat (no indent) form', 'wp-realtime-sitemap'),
						'1'		=> __('Show only top level Categories', 'wp-realtime-sitemap'),
					);
					break;

				case 'tags_tagcloud':
					$values = array(
						'Yes'		=> __('Yes/On', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'tags_orderby':
					$values = array(
						'ID'		=> __('ID', 'wp-realtime-sitemap'),
						'name'		=> __('Name (Default)', 'wp-realtime-sitemap'),
						'slug'		=> __('Slug', 'wp-realtime-sitemap'),
						'count'		=> __('Count', 'wp-realtime-sitemap'),
						'term_group'	=> __('Term Group', 'wp-realtime-sitemap'),
					);
					break;

				case 'tags_order':
					$values = array(
						'ASC'		=> __('Sort ascending (Default)', 'wp-realtime-sitemap'),
						'DESC'		=> __('Sort descending', 'wp-realtime-sitemap'),
					);
					break;

				case 'tags_show_post_count':
					$values = array(
						true		=> __('True', 'wp-realtime-sitemap'),
						false		=> __('False (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'tags_hide_empty':
					$values = array(
						true		=> __('True (Default)', 'wp-realtime-sitemap'),
						false		=> __('False', 'wp-realtime-sitemap'),
					);
					break;

				case 'tags_number':
					$values = array('numeric' => 'numeric');
					break;

				case 'show_menu':
					$values = array(
						'Yes'		=> __('Yes/On', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'show_pages':
					$values = array(
						'Yes'		=> __('Yes/On (Default)', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off', 'wp-realtime-sitemap'),
					);
					break;

				case 'show_posts':
					$values = array(
						'Yes'		=> __('Yes/On (Default)', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off', 'wp-realtime-sitemap'),
					);
					break;

				case 'show_custom_post_types':
					$values = array(
						'Yes'		=> __('Yes/On', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'show_archives':
					$values = array(
						'Yes'		=> __('Yes/On (Default)', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off', 'wp-realtime-sitemap'),
					);
					break;

				case 'show_categories':
					$values = array(
						'Yes'		=> __('Yes/On (Default)', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off', 'wp-realtime-sitemap'),
					);
					break;

				case 'show_tags':
					$values = array(
						'Yes'		=> __('Yes/On', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off (Default)', 'wp-realtime-sitemap'),
					);
					break;

				case 'show_promote':
					$values = array(
						'Yes'		=> __('Yes/On (Default)', 'wp-realtime-sitemap'),
						'No'		=> __('No/Off', 'wp-realtime-sitemap'),
					);
					break;

				case 'first_order':
					$values = array(
						'menu'		=> __('Menu', 'wp-realtime-sitemap'),
						'pages'		=> __('Pages', 'wp-realtime-sitemap'),
						'posts'		=> __('Posts', 'wp-realtime-sitemap'),
						'custom_posts'	=> __('Custom Post Types', 'wp-realtime-sitemap'),
						'archives'	=> __('Archives', 'wp-realtime-sitemap'),
						'categories'	=> __('Categories', 'wp-realtime-sitemap'),
						'tags'		=> __('Tags', 'wp-realtime-sitemap'),
					);
					break;

				case 'second_order':
					$values = array(
						'menu'		=> __('Menu', 'wp-realtime-sitemap'),
						'pages'		=> __('Pages', 'wp-realtime-sitemap'),
						'posts'		=> __('Posts', 'wp-realtime-sitemap'),
						'custom_posts'	=> __('Custom Post Types', 'wp-realtime-sitemap'),
						'archives'	=> __('Archives', 'wp-realtime-sitemap'),
						'categories'	=> __('Categories', 'wp-realtime-sitemap'),
						'tags'		=> __('Tags', 'wp-realtime-sitemap'),
					);
					break;

				case 'third_order':
					$values = array(
						'menu'		=> __('Menu', 'wp-realtime-sitemap'),
						'pages'		=> __('Pages', 'wp-realtime-sitemap'),
						'posts'		=> __('Posts', 'wp-realtime-sitemap'),
						'custom_posts'	=> __('Custom Post Types', 'wp-realtime-sitemap'),
						'archives'	=> __('Archives', 'wp-realtime-sitemap'),
						'categories'	=> __('Categories', 'wp-realtime-sitemap'),
						'tags'		=> __('Tags', 'wp-realtime-sitemap'),
					);
					break;

				case 'fourth_order':
					$values = array(
						'menu'		=> __('Menu', 'wp-realtime-sitemap'),
						'pages'		=> __('Pages', 'wp-realtime-sitemap'),
						'posts'		=> __('Posts', 'wp-realtime-sitemap'),
						'custom_posts'	=> __('Custom Post Types', 'wp-realtime-sitemap'),
						'archives'	=> __('Archives', 'wp-realtime-sitemap'),
						'categories'	=> __('Categories', 'wp-realtime-sitemap'),
						'tags'		=> __('Tags', 'wp-realtime-sitemap'),
					);
					break;

				case 'fifth_order':
					$values = array(
						'menu'		=> __('Menu', 'wp-realtime-sitemap'),
						'pages'		=> __('Pages', 'wp-realtime-sitemap'),
						'posts'		=> __('Posts', 'wp-realtime-sitemap'),
						'custom_posts'	=> __('Custom Post Types', 'wp-realtime-sitemap'),
						'archives'	=> __('Archives', 'wp-realtime-sitemap'),
						'categories'	=> __('Categories', 'wp-realtime-sitemap'),
						'tags'		=> __('Tags', 'wp-realtime-sitemap'),
					);
					break;

				case 'sixth_order':
					$values = array(
						'menu'		=> __('Menu', 'wp-realtime-sitemap'),
						'pages'		=> __('Pages', 'wp-realtime-sitemap'),
						'posts'		=> __('Posts', 'wp-realtime-sitemap'),
						'custom_posts'	=> __('Custom Post Types', 'wp-realtime-sitemap'),
						'archives'	=> __('Archives', 'wp-realtime-sitemap'),
						'categories'	=> __('Categories', 'wp-realtime-sitemap'),
						'tags'		=> __('Tags', 'wp-realtime-sitemap'),
					);
					break;

				case 'seventh_order':
					$values = array(
						'menu'		=> __('Menu', 'wp-realtime-sitemap'),
						'pages'		=> __('Pages', 'wp-realtime-sitemap'),
						'posts'		=> __('Posts', 'wp-realtime-sitemap'),
						'custom_posts'	=> __('Custom Post Types', 'wp-realtime-sitemap'),
						'archives'	=> __('Archives', 'wp-realtime-sitemap'),
						'categories'	=> __('Categories', 'wp-realtime-sitemap'),
						'tags'		=> __('Tags', 'wp-realtime-sitemap'),
					);
					break;
			}

			return $values;
		}

		function _formInfoText($args) {
			switch($args['dbfield']) {
				case 'menu_id':
					$infotext = '';
					break;

				case 'page_sort_column':
					$infotext = '';
					break;

				case 'page_sort_order':
					$infotext = '';
					break;

				case 'page_exclude':
					$infotext = __('Comma separated list of IDs.', 'wp-realtime-sitemap');
					break;

				case 'page_depth':
					$infotext = '';
					break;

				case 'page_show_date':
					$infotext = '';
					break;

/*				case 'page_number':
					$infotext = __('leave blank to show all.', 'wp-realtime-sitemap');
					break; */

				case 'post_orderby':
					$infotext = '';
					break;

				case 'post_order':
					$infotext = '';
					break;

				case 'post_exclude':
					$infotext = __('Comma separated list of IDs.', 'wp-realtime-sitemap');
					break;

				case 'post_show_date':
					$infotext = '';
					break;

				case 'post_numberposts':
					$infotext = __('-1 to show all.', 'wp-realtime-sitemap');
					break;

				case 'archive_type':
					$infotext = '';
					break;

				case 'archive_limit':
					$infotext = __('leave blank to show all.', 'wp-realtime-sitemap');
					break;

				case 'archive_show_post_count':
					$infotext = '';
					break;

				case 'category_tagcloud':
					$infotext = '';
					break;

				case 'category_orderby':
					$infotext = '';
					break;

				case 'category_order':
					$infotext = '';
					break;

				case 'category_show_post_count':
					$infotext = __('This is ignored with the tag cloud setting.', 'wp-realtime-sitemap');
					break;

				case 'category_hide_empty':
					$infotext = __('This is ignored with the tag cloud setting.', 'wp-realtime-sitemap');
					break;

				case 'category_exclude':
					$infotext = __('Comma separated list of IDs.', 'wp-realtime-sitemap');
					break;

				case 'category_number':
					$infotext = __('0 to show all.', 'wp-realtime-sitemap');
					break;

				case 'category_depth':
					$infotext = '';
					break;

				case 'tags_tagcloud':
					$infotext = '';
					break;

				case 'tags_orderby':
					$infotext = '';
					break;

				case 'tags_order':
					$infotext = '';
					break;

				case 'tags_show_post_count':
					$infotext = __('This is ignored with the tag cloud setting.', 'wp-realtime-sitemap');
					break;

				case 'tags_hide_empty':
					$infotext = __('This is ignored with the tag cloud setting.', 'wp-realtime-sitemap');
					break;

				case 'tags_exclude':
					$infotext = __('Comma separated list of IDs.', 'wp-realtime-sitemap');
					break;

				case 'tags_number':
					$infotext = __('0 to show all tags.', 'wp-realtime-sitemap');
					break;

				case 'menu_header':
					$infotext = '';
					break;

				case 'pages_header':
					$infotext = '';
					break;

				case 'posts_header':
					$infotext = '';
					break;

				case 'archives_header':
					$infotext = '';
					break;

				case 'categories_header':
					$infotext = '';
					break;

				case 'tags_header':
					$infotext = '';
					break;

				case 'show_menu':
					$infotext = '';
					break;

				case 'show_pages':
					$infotext = '';
					break;

				case 'show_posts':
					$infotext = '';
					break;

				case 'show_custom_post_types':
					$infotext = '';
					break;

				case 'show_archives':
					$infotext = '';
					break;

				case 'show_categories':
					$infotext = '';
					break;

				case 'show_tags':
					$infotext = '';
					break;

				case 'show_promote':
					$infotext = __('This places a small link under the sitemap, letting others know how your sitemap was made.', 'wp-realtime-sitemap');
					break;

				case 'first_order':
					$infotext = '';
					break;

				case 'second_order':
					$infotext = '';
					break;

				case 'third_order':
					$infotext = '';
					break;

				case 'fourth_order':
					$infotext = '';
					break;

				case 'fifth_order':
					$infotext = '';
					break;

				case 'sixth_order':
					$infotext = '';
					break;

				case 'seventh_order':
					$infotext = '';
					break;
			}

			return $infotext;
		}

		function _formSelectInput($args) {
			// Get current options from the database.
			extract(get_option('plugin_wp_realtime_sitemap_settings'));

			$values		= $this->_formValidDefaults($args);
			$infotext	= $this->_formInfoText($args);

			if (isset($values) && is_array($values)) {
				$optionFormat = '<option value="%s"%s>%s</option>';

				$output = '<select name="plugin_wp_realtime_sitemap_settings[' . $args['dbfield'] . ']">';

				foreach ($values as $key => $label) {
					$output .= sprintf($optionFormat, $key, selected($$args['dbfield'], $key, false), $label);
				}

				$output .= '</select><br />' . $infotext;
			}

			echo $output;
		}

		function _formTextInput($args) {
			// Get current options from the database.
			extract(get_option('plugin_wp_realtime_sitemap_settings'));

			$infotext	= $this->_formInfoText($args);

			if (array_key_exists('dbfield', $args) && isset($infotext)) {
				echo '<input name="plugin_wp_realtime_sitemap_settings[' . $args['dbfield'] . ']" size="30" type="text" value="' . $$args['dbfield'] . '" /><br />' . $infotext;
			}
		}

		function _formValidate($inputs) {
			$plugin_wp_realtime_sitemap_settings = get_option('plugin_wp_realtime_sitemap_settings');

			$validInputs = array(
				// MENU SETTINGS
				'menu_id'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'menu_id'))),
									'default' => '',
									'errormsg' => __('Menu Settings', 'wp-realtime-sitemap') . ' > ' . __('Menu ID', 'wp-realtime-sitemap') . ': ' . __('This must be alpha-numeric for it to be valid, please try again.', 'wp-realtime-sitemap'),
								),

				// PAGE SETTINGS
				'page_sort_column'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'page_sort_column'))),
									'default' => '',
									'errormsg' => __('Page Settings', 'wp-realtime-sitemap') . ' > ' . __('Order By', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'page_sort_order'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'page_sort_order'))),
									'default' => '',
									'errormsg' => __('Page Settings', 'wp-realtime-sitemap') . ' > ' . __('Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'page_exclude'			=> array(
									'valid' => array(),
									'default' => '',
									'errormsg' => __('Page Settings', 'wp-realtime-sitemap') . ' > ' . __('Exclude', 'wp-realtime-sitemap') . ': ' . __('This must be comment seperated for it to be valid, please try again.', 'wp-realtime-sitemap'),
								),
				'page_depth'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'page_depth'))),
									'default' => '',
									'errormsg' => __('Page Settings', 'wp-realtime-sitemap') . ' > ' . __('Hierarchy Depth', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'page_show_date'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'page_show_date'))),
									'default' => '',
									'errormsg' => __('Page Settings', 'wp-realtime-sitemap') . ' > ' . __('Display Date', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
/* 				'page_number'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'page_number'))),
									'default' => '',
									'errormsg' => __('Page Settings', 'wp-realtime-sitemap') . ' > ' . __('Limit', 'wp-realtime-sitemap') . ': ' . __('Only integers or -1 are accepted as valid inputs.', 'wp-realtime-sitemap'),
								), */

				// POST SETTINGS
				'post_orderby'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'post_orderby'))),
									'default' => '',
									'errormsg' => __('Post Settings', 'wp-realtime-sitemap') . ' > ' . __('Order By', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'post_order'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'post_order'))),
									'default' => '',
									'errormsg' => __('Post Settings', 'wp-realtime-sitemap') . ' > ' . __('Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'post_exclude'			=> array(
									'valid' => array(),
									'default' => '',
									'errormsg' => __('Post Settings', 'wp-realtime-sitemap') . ' > ' . __('Exclude', 'wp-realtime-sitemap') . ': ' . __('This must be comment seperated list of IDs for it to be valid, please try again.', 'wp-realtime-sitemap'),
								),
				'post_show_date'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'post_show_date'))),
									'default' => '',
									'errormsg' => __('Post Settings', 'wp-realtime-sitemap') . ' > ' . __('Display Date', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'post_numberposts'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'post_numberposts'))),
									'default' => '',
									'errormsg' => __('Post Settings', 'wp-realtime-sitemap') . ' > ' . __('Limit', 'wp-realtime-sitemap') . ': ' . __('Only integers are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'post_show_categories'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'post_show_categories'))),
									'default' => '',
									'errormsg' => __('Post Settings', 'wp-realtime-sitemap') . ' > ' . __('Display Categories', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),

				// ARCHIVE SETTINGS
				'archive_type'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'archive_type'))),
									'default' => '',
									'errormsg' => __('Archive Settings', 'wp-realtime-sitemap') . ' > ' . __('Archive Type', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'archive_limit'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'archive_limit'))),
									'default' => '',
									'errormsg' => __('Archive Settings', 'wp-realtime-sitemap') . ' > ' . __('Limit', 'wp-realtime-sitemap') . ': ' . __('Only integers are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'archive_show_post_count'	=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'archive_show_post_count'))),
									'default' => '',
									'errormsg' => __('Archive Settings', 'wp-realtime-sitemap') . ' > ' . __('Post Count', 'wp-realtime-sitemap') . ': ' . __('Only True or False are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),

				// CATEGORY SETTINGS
				'category_tagcloud'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'category_tagcloud'))),
									'default' => '',
									'errormsg' => 'category_tagcloud',
								),
				'category_orderby'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'category_orderby'))),
									'default' => '',
									'errormsg' => __('Category Settings', 'wp-realtime-sitemap') . ' > ' . __('Order By', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'category_order'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'category_order'))),
									'default' => '',
									'errormsg' => __('Category Settings', 'wp-realtime-sitemap') . ' > ' . __('Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'category_show_post_count'	=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'category_show_post_count'))),
									'default' => '',
									'errormsg' => __('Category Settings', 'wp-realtime-sitemap') . ' > ' . __('Post Count', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'category_hide_empty'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'category_hide_empty'))),
									'default' => '',
									'errormsg' => __('Category Settings', 'wp-realtime-sitemap') . ' > ' . __('Hide Empty', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'category_exclude'		=> array(
									'valid' => array(),
									'default' => '',
									'errormsg' => 'category_exclude',
								),
				'category_number'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'category_number'))),
									'default' => '',
									'errormsg' => __('Category Settings', 'wp-realtime-sitemap') . ' > ' . __('Limit', 'wp-realtime-sitemap') . ': ' . __('Only integers or -1 are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),

				// TAG SETTINGS
				'tags_tagcloud'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'tags_tagcloud'))),
									'default' => '',
									'errormsg' => 'tags_tagcloud',
								),
				'tags_orderby'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'tags_orderby'))),
									'default' => '',
									'errormsg' => __('Tag Settings', 'wp-realtime-sitemap') . ' > ' . __('Order By', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'tags_order'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'tags_order'))),
									'default' => '',
									'errormsg' => __('Tag Settings', 'wp-realtime-sitemap') . ' > ' . __('Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'tags_show_post_count'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'tags_show_post_count'))),
									'default' => '',
									'errormsg' => __('Tag Settings', 'wp-realtime-sitemap') . ' > ' . __('Post Count', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'tags_hide_empty'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'tags_hide_empty'))),
									'default' => '',
									'errormsg' => __('Tag Settings', 'wp-realtime-sitemap') . ' > ' . __('Order By', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'tags_number'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'tags_number'))),
									'default' => '',
									'errormsg' => __('Tag Settings', 'wp-realtime-sitemap') . ' > ' . __('Limit', 'wp-realtime-sitemap') . ': ' . __('Only integers or -1 are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),

				// HEADER SETTINGS


				// DISPLAY SETTINGS
				'show_menu'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'show_menu'))),
									'default' => '',
									'errormsg' => __('Display Settings', 'wp-realtime-sitemap') . ' > ' . __('Show Menu', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'show_pages'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'show_pages'))),
									'default' => '',
									'errormsg' => __('Display Settings', 'wp-realtime-sitemap') . ' > ' . __('Show Pages', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'show_posts'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'show_posts'))),
									'default' => '',
									'errormsg' => __('Display Settings', 'wp-realtime-sitemap') . ' > ' . __('Show Posts', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'show_custom_post_types'	=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'show_custom_post_types'))),
									'default' => '',
									'errormsg' => __('Display Settings', 'wp-realtime-sitemap') . ' > ' . __('Show Custom Post Types', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'show_archives'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'show_archives'))),
									'default' => '',
									'errormsg' => __('Display Settings', 'wp-realtime-sitemap') . ' > ' . __('Show Archives', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'show_categories'		=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'show_categories'))),
									'default' => '',
									'errormsg' => __('Display Settings', 'wp-realtime-sitemap') . ' > ' . __('Show Categories', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'show_tags'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'show_tags'))),
									'default' => '',
									'errormsg' => __('Display Settings', 'wp-realtime-sitemap') . ' > ' . __('Show Tags', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),
				'show_promote'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'show_promote'))),
									'default' => '',
									'errormsg' => __('Display Settings', 'wp-realtime-sitemap') . ' > ' . __('Show Promote', 'wp-realtime-sitemap') . ': ' . __('Only Yes/On or No/Off are accepted as valid inputs.', 'wp-realtime-sitemap'),
								),

				// ORDER SETTINGS
				'first_order'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'first_order'))),
									'default' => '',
									'errormsg' => __('Order Settings', 'wp-realtime-sitemap') . ' > ' . __('1st Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'second_order'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'second_order'))),
									'default' => '',
									'errormsg' => __('Order Settings', 'wp-realtime-sitemap') . ' > ' . __('2nd Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'third_order'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'third_order'))),
									'default' => '',
									'errormsg' => __('Order Settings', 'wp-realtime-sitemap') . ' > ' . __('3rd Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'fourth_order'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'fourth_order'))),
									'default' => '',
									'errormsg' => __('Order Settings', 'wp-realtime-sitemap') . ' > ' . __('4th Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'fifth_order'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'fifth_order'))),
									'default' => '',
									'errormsg' => __('Order Settings', 'wp-realtime-sitemap') . ' > ' . __('5th Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'sixth_order'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'sixth_order'))),
									'default' => '',
									'errormsg' => __('Order Settings', 'wp-realtime-sitemap') . ' > ' . __('6th Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
				'seventh_order'			=> array(
									'valid' => array_keys($this->_formValidDefaults(array('dbfield' => 'seventh_order'))),
									'default' => '',
									'errormsg' => __('Order Settings', 'wp-realtime-sitemap') . ' > ' . __('7th Order', 'wp-realtime-sitemap') . ': ' . __('The option choosen is not valid, please try again.', 'wp-realtime-sitemap'),
								),
			);

			// MENU SETTINGS
//			if(!is_numeric($inputs['menu_id']) || !is_string($inputs['menu_id']) || (!isset($inputs['menu_id']) && empty($inputs['menu_id']))) {
			if(!is_numeric($inputs['menu_id']) && !empty($inputs['menu_id'])) {
				$plugin_wp_realtime_sitemap_settings['menu_id'] = $validInputs['menu_id']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['menu_id']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['menu_id'] = $inputs['menu_id'];
			}

			// PAGE SETTINGS
			if(!in_array($inputs['page_sort_column'], $validInputs['page_sort_column']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['page_sort_column'] = $validInputs['page_sort_column']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['page_sort_column']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['page_sort_column'] = $inputs['page_sort_column'];
			}

			if(!in_array($inputs['page_sort_order'], $validInputs['page_sort_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['page_sort_order'] = $validInputs['page_sort_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['page_sort_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['page_sort_order'] = $inputs['page_sort_order'];
			}

			$plugin_wp_realtime_sitemap_settings['page_exclude'] = $inputs['page_exclude'];

			if(!is_numeric($inputs['page_depth'])) {
				$plugin_wp_realtime_sitemap_settings['page_depth'] = $validInputs['page_depth']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['page_depth']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['page_depth'] = $inputs['page_depth'];
			}

			if(!in_array($inputs['page_show_date'], $validInputs['page_show_date']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['page_show_date'] = $validInputs['page_show_date']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['page_show_date']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['page_show_date'] = $inputs['page_show_date'];
			}

/* 			if(!is_numeric($inputs['page_number']) && !empty($inputs['page_number'])) {
				$plugin_wp_realtime_sitemap_settings['page_number'] = $validInputs['page_number']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['page_number']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['page_number'] = $inputs['page_number'];
			} */

			// POST SETTINGS
			if(!in_array($inputs['post_order'], $validInputs['post_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['post_order'] = $validInputs['post_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['post_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['post_order'] = $inputs['post_order'];
			}

			if(!in_array($inputs['post_orderby'], $validInputs['post_orderby']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['post_orderby'] = $validInputs['post_orderby']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['post_orderby']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['post_orderby'] = $inputs['post_orderby'];
			}

			$plugin_wp_realtime_sitemap_settings['post_exclude'] = $inputs['post_exclude'];

			if(!in_array($inputs['post_show_date'], $validInputs['post_show_date']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['post_show_date'] = $validInputs['post_show_date']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['post_show_date']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['post_show_date'] = $inputs['post_show_date'];
			}

			if(!is_numeric($inputs['post_numberposts'])) {
				$plugin_wp_realtime_sitemap_settings['post_numberposts'] = $validInputs['post_numberposts']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['post_numberposts']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['post_numberposts'] = $inputs['post_numberposts'];
			}

			if(!in_array($inputs['post_show_categories'], $validInputs['post_show_categories']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['post_show_categories'] = $validInputs['post_show_categories']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['post_show_categories']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['post_show_categories'] = $inputs['post_show_categories'];
			}

			// ARCHIVE SETTINGS
			if(!in_array($inputs['archive_type'], $validInputs['archive_type']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['archive_type'] = $validInputs['archive_type']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['archive_type']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['archive_type'] = $inputs['archive_type'];
			}

			if(!is_numeric($inputs['archive_limit']) && !empty($inputs['archive_limit'])) {
				$plugin_wp_realtime_sitemap_settings['archive_limit'] = $validInputs['archive_limit']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['archive_limit']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['archive_limit'] = $inputs['archive_limit'];
			}

			if(!in_array($inputs['archive_show_post_count'], $validInputs['archive_show_post_count']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['archiveshow_post_count'] = $validInputs['archive_show_post_count']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['archive_show_post_count']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['archive_show_post_count'] = $inputs['archive_show_post_count'];
			}

			// CATEGORY SETTINGS
			if(!in_array($inputs['category_tagcloud'], $validInputs['category_tagcloud']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['category_tagcloud'] = $validInputs['category_tagcloud']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['category_tagcloud']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['category_tagcloud'] = $inputs['category_tagcloud'];
			}

			if(!in_array($inputs['category_orderby'], $validInputs['category_orderby']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['category_orderby'] = $validInputs['category_orderby']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['category_orderby']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['category_orderby'] = $inputs['category_orderby'];
			}

			if(!in_array($inputs['category_order'], $validInputs['category_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['category_order'] = $validInputs['category_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['category_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['category_order'] = $inputs['category_order'];
			}

			if(!in_array($inputs['category_show_post_count'], $validInputs['category_show_post_count']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['category_show_post_count'] = $validInputs['category_show_post_count']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['category_show_post_count']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['category_show_post_count'] = $inputs['category_show_post_count'];
			}

			if(!in_array($inputs['category_hide_empty'], $validInputs['category_hide_empty']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['category_hide_empty'] = $validInputs['category_hide_empty']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['category_hide_empty']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['category_hide_empty'] = $inputs['category_hide_empty'];
			}

			$plugin_wp_realtime_sitemap_settings['category_exclude'] = $inputs['category_exclude'];

			if(!is_numeric($inputs['category_depth'])) {
				$plugin_wp_realtime_sitemap_settings['category_depth'] = $validInputs['category_depth']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['category_depth']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['category_depth'] = $inputs['category_depth'];
			}

			if(!is_numeric($inputs['category_number'])) {
				$plugin_wp_realtime_sitemap_settings['category_number'] = $validInputs['category_number']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['category_number']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['category_number'] = $inputs['category_number'];
			}

			// TAG SETTINGS
			if(!in_array($inputs['tags_tagcloud'], $validInputs['tags_tagcloud']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['tags_tagcloud'] = $validInputs['tags_tagcloud']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['tags_tagcloud']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['tags_tagcloud'] = $inputs['tags_tagcloud'];
			}

			if(!in_array($inputs['tags_orderby'], $validInputs['tags_orderby']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['tags_orderby'] = $validInputs['tags_orderby']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['tags_orderby']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['tags_orderby'] = $inputs['tags_orderby'];
			}

			if(!in_array($inputs['tags_order'], $validInputs['tags_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['tags_order'] = $validInputs['tags_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['tags_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['tags_order'] = $inputs['tags_order'];
			}

			if(!in_array($inputs['tags_show_post_count'], $validInputs['tags_show_post_count']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['tags_show_post_count'] = $validInputs['tags_show_post_count']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['tags_show_post_count']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['tags_show_post_count'] = $inputs['tags_show_post_count'];
			}

			if(!in_array($inputs['tags_hide_empty'], $validInputs['tags_hide_empty']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['tags_hide_empty'] = $validInputs['tags_hide_empty']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['tags_hide_empty']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['tags_hide_empty'] = $inputs['tags_hide_empty'];
			}

			$plugin_wp_realtime_sitemap_settings['tags_exclude'] = $inputs['tags_exclude'];

			if(!is_numeric($inputs['tags_number'])) {
				$plugin_wp_realtime_sitemap_settings['tags_number'] = $validInputs['tags_number']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['tags_number']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['tags_number'] = $inputs['tags_number'];
			}

			// HEADER SETTINGS
			$plugin_wp_realtime_sitemap_settings['menu_header']		= mysql_real_escape_string($inputs['menu_header']);
			$plugin_wp_realtime_sitemap_settings['pages_header']		= mysql_real_escape_string($inputs['pages_header']);
			$plugin_wp_realtime_sitemap_settings['posts_header']		= mysql_real_escape_string($inputs['posts_header']);
			$plugin_wp_realtime_sitemap_settings['archives_header']		= mysql_real_escape_string($inputs['archives_header']);
			$plugin_wp_realtime_sitemap_settings['categories_header']	= mysql_real_escape_string($inputs['categories_header']);
			$plugin_wp_realtime_sitemap_settings['tags_header']		= mysql_real_escape_string($inputs['tags_header']);

			// DISPLAY SETTINGS
			if(!in_array($inputs['show_menu'], $validInputs['show_menu']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['show_menu'] = $validInputs['show_menu']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['show_menu']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['show_menu'] = $inputs['show_menu'];
			}

			if(!in_array($inputs['show_pages'], $validInputs['show_pages']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['show_pages'] = $validInputs['show_pages']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['show_pages']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['show_pages'] = $inputs['show_pages'];
			}

			if(!in_array($inputs['show_posts'], $validInputs['show_posts']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['show_posts'] = $validInputs['show_posts']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['show_posts']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['show_posts'] = $inputs['show_posts'];
			}

			if(!in_array($inputs['show_custom_post_types'], $validInputs['show_custom_post_types']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['show_custom_post_types'] = $validInputs['show_custom_post_types']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['show_custom_post_types']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['show_custom_post_types'] = $inputs['show_custom_post_types'];
			}

			if(!in_array($inputs['show_archives'], $validInputs['show_archives']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['show_archives'] = $validInputs['show_archives']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['show_archives']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['show_archives'] = $inputs['show_archives'];
			}

			if(!in_array($inputs['show_categories'], $validInputs['show_categories']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['show_categories'] = $validInputs['show_categories']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['show_categories']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['show_categories'] = $inputs['show_categories'];
			}

			if(!in_array($inputs['show_tags'], $validInputs['show_tags']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['show_tags'] = $validInputs['show_tags']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['show_tags']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['show_tags'] = $inputs['show_tags'];
			}

			if(!in_array($inputs['show_promote'], $validInputs['show_promote']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['show_promote'] = $validInputs['show_promote']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['show_promote']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['show_promote'] = $inputs['show_promote'];
			}

			// ORDER SETTINGS
			if(!in_array($inputs['first_order'], $validInputs['first_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['first_order'] = $validInputs['first_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['first_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['first_order'] = $inputs['first_order'];
			}

			if(!in_array($inputs['second_order'], $validInputs['second_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['second_order'] = $validInputs['second_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['second_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['second_order'] = $inputs['second_order'];
			}

			if(!in_array($inputs['third_order'], $validInputs['third_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['third_order'] = $validInputs['third_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['third_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['third_order'] = $inputs['third_order'];
			}

			if(!in_array($inputs['fourth_order'], $validInputs['fourth_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['fourth_order'] = $validInputs['fourth_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['fourth_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['fourth_order'] = $inputs['fourth_order'];
			}

			if(!in_array($inputs['fifth_order'], $validInputs['fifth_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['fifth_order'] = $validInputs['fifth_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['fifth_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['fifth_order'] = $inputs['fifth_order'];
			}

			if(!in_array($inputs['sixth_order'], $validInputs['sixth_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['sixth_order'] = $validInputs['sixth_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['sixth_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['sixth_order'] = $inputs['sixth_order'];
			}

			if(!in_array($inputs['seventh_order'], $validInputs['seventh_order']['valid'])) {
				$plugin_wp_realtime_sitemap_settings['seventh_order'] = $validInputs['seventh_order']['default'];

				add_settings_error('plugin_wp_realtime_sitemap_settings', 'settings_updated', $validInputs['seventh_order']['errormsg']);
			} else {
				$plugin_wp_realtime_sitemap_settings['seventh_order'] = $inputs['seventh_order'];
			}

			return $plugin_wp_realtime_sitemap_settings;
		}

		function _optionsForm() {
			// check user has access to change settings for this plugin.
			if (!current_user_can('manage_options')) {
				wp_die( __('You do not have sufficient permissions to access this page.', 'wp-realtime-sitemap') );
			}

			$post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');

			// Get current options from the database.
			extract(get_option('plugin_wp_realtime_sitemap_settings'));
?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e('WP Realtime Sitemap Options', 'wp-realtime-sitemap'); ?></h2>

		<p><?php _e('WP Realtime Sitemap is free to use, however, it has required a great deal of time and effort to develop and if it has been useful you can help support this development by making a small donation. This will act as an incentive for me to carry on developing, providing countless hours of support, and including new features and suggestions. You get some useful software and I get to carry on making it. Everybody wins.', 'wp-realtime-sitemap'); ?></p>
		<p><?php _e('If you have yet to rate my plugin then can you please do that also, thank you!', 'wp-realtime-sitemap'); ?></p>

		<div style="width:450px; height: 194px; border:1px solid #dddddd; background:#fff; padding:20px 20px; float: left;">
			<h3 style="margin:0; padding:0;"><?php _e('Donate with PayPal', 'wp-realtime-sitemap'); ?></h3>
			<p><?php echo sprintf(esc_attr__('If you like this plugin and find it useful, help keep this plugin free and actively developed by clicking the %s button or send me a gift from my %s. Also dont forget to follow me on %s.', 'wp-realtime-sitemap'), '<a href="http://goo.gl/ddoa5" title="' . __('Donate with PayPal', 'wp-realtime-sitemap') . '">' . __('donate', 'wp-realtime-sitemap') . '</a>', '<a href="http://goo.gl/yrM92" title="' . __('Amazon WishList', 'wp-realtime-sitemap') . '">' . __('Amazon WishList', 'wp-realtime-sitemap') . '</a>', '<a href="http://goo.gl/gxzzu" title="' . __('Twitter', 'wp-realtime-sitemap') . '">' . __('Twitter', 'wp-realtime-sitemap') . '</a>'); ?></p>
			<map name="donatemap">
				<area border="1" shape="rect" coords="0,0,108,54" href="http://goo.gl/ddoa5" alt="<?php _e('Donate with PayPal', 'wp-realtime-sitemap'); ?>" />
				<area border="1" shape="rect" coords="113,0,221,54" href="http://goo.gl/yrM92" alt="<?php _e('Amazon WishList', 'wp-realtime-sitemap'); ?>" />
				<area border="1" shape="rect" coords="226,0,334,54" href="http://goo.gl/gxzzu" alt="<?php _e('Twitter', 'wp-realtime-sitemap'); ?>" />
			</map>
			<img border="1" src="<?php echo $this->plugin_base_url; ?>/images/donate.jpg" usemap="#donatemap" />
		</div>

		<div style="width:450px; border:1px solid #dddddd; background:#fff; padding:20px 20px; float: left; margin-left: 20px;">
			<h3 style="margin:0; padding:0;"><?php _e('ThemeFuse Original WP Themes', 'wp-realtime-sitemap'); ?></h3>
			<p><?php echo sprintf(esc_attr__('If you are interested in buying an original wp theme I would recommend %s. They make some amazing wp themes, that have a cool 1 click auto install feature and excellent after care support services. Check out some of their themes!', 'wp-realtime-sitemap'), '<a href="http://goo.gl/zhJTn" title="' . __('ThemeFuse', 'wp-realtime-sitemap') . '">' . __('ThemeFuse', 'wp-realtime-sitemap') . '</a>'); ?></p>
			<a style="border:none;" href="http://goo.gl/zhJTn"><img style="border:none;" src="<?php echo $this->plugin_base_url; ?>/images/themefuse.jpg" /></a>
		</div>

		<br style="clear: both;" />

		<p><?php _e('This plugin can easily be styled by using the following ids below should you need to, if you require more flexibility than this please feel free to suggest something', 'wp-realtime-sitemap'); ?></p>

		<ul>
			<li>wp-realtime-sitemap-menu</li>
			<li>wp-realtime-sitemap-pages</li>
			<li>wp-realtime-sitemap-posts</li>
<?php if (count($post_types) > 0) { foreach ($post_types as $post_type) { ?>
			<li>wp-realtime-sitemap-<?php echo $post_type->name; ?> *custom post type*</li>
<?php } } ?>
			<li>wp-realtime-sitemap-archives</li>
			<li>wp-realtime-sitemap-categories</li>
			<li>wp-realtime-sitemap-tags</li>
		</ul>

		<form method="post" action="options.php">
		<?php settings_fields('update_settings'); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit"><input name="wp_realtime_sitemap_update" value="<?php _e('Save Changes', 'wp-realtime-sitemap'); ?>" type="submit" class="button-primary" /></p>
		</form>
	</div><?php
		}

		function showOutput($atts, $content=null, $code='') {
			global $wpdb, $table_prefix, $post;

			$menu		= '';
			$pages		= '';
			$posts		= '';
			$custom_posts	= array();
			$archives	= '';
			$categories	= '';
			$tags		= '';
			$promote	= '';

			// Only perform plugin functionality if post/page text has the shortcode in the page.
			if (preg_match('|wp-realtime-sitemap|', $code)) {
				// Get option values from the database.
				$all_options = get_option('plugin_wp_realtime_sitemap_settings');
				extract($all_options);

				extract(shortcode_atts(array('show' => 'all'), $atts));

				// Nav Menu: Yes/No? - Appearance -> Menus
 				if (($all_options['show_menu'] != 'No' && $all_options['show_menu'] != 'Off') && ($show == 'menu' || $show == 'all')) {
 					$menu_header = (empty($menu_header)) ? __('Menu', 'wp-realtime-sitemap') : $menu_header;

					$menu = '<div id="wp-realtime-sitemap-menu"><h3>' . $menu_header . '</h3>';
					$menu .= '<ul>' . wp_nav_menu(array('menu' => $menu_id, 'container' => false, 'items_wrap' => '%3$s', 'echo' => '0')) . '</ul>';
					$menu .= '</div>';
				}

				// Pages: Yes/No?
				if (($all_options['show_pages'] != 'No' && $all_options['show_pages'] != 'Off') && ($show == 'pages' || $show == 'all')) {
					$pages_header = (empty($pages_header)) ? __('Pages', 'wp-realtime-sitemap') : $pages_header;

					$pages = '<div id="wp-realtime-sitemap-pages"><h3>' . $pages_header . '</h3>';
					$pages .= '<ul>' . wp_list_pages(array('sort_column' => $page_sort_column, 'sort_order' => $page_sort_order, 'exclude' => $page_exclude, 'depth' => $page_depth, 'show_date' => $page_show_date, 'title_li' => '', 'echo' => '0')) . '</ul></div>';
				}

				// Posts: Yes/No?
				if (($all_options['show_posts'] != 'No' && $all_options['show_posts'] != 'Off') && ($show == 'posts' || $show == 'all')) {
					$posts = '';

					if ($all_options['post_show_categories'] != 'No') {

						$thecategories = get_categories(array('type' => 'post', 'orderby' => $category_orderby, 'order' => $category_order, 'hide_empty' => $category_hide_empty, 'exclude' => $category_exclude, 'hierarchical' => '1', 'number' => $category_number, 'taxonomy' => 'category'));

						if (count($thecategories) > 0) {
							foreach($thecategories as $category) {

								$posts .= '<li><a href="' . get_category_link($category->term_id) . '" title="' . $category->category_description . '">' . $category->name . '</a><ul>';

								// Set options for post query
								$theposts = get_posts(array(
									'numberposts'	=> $post_numberposts,
									'category'	=> $category->cat_ID,
									'orderby'	=> $post_orderby,
									'order'		=> $post_order,
									'exclude'	=> $post_exclude,
									'post_type'	=> 'post',
								));

								if (count($theposts) > 0) {
									foreach($theposts as $post) {
										setup_postdata($post);

										$extra = '';

										if ($post_show_date == 'Yes')
											$extra = ' <span>' . get_the_date() . '</span>';

										$posts .= '<li><a href="' . get_permalink() . '" title="' . sprintf(esc_attr__('Permalink to %s', 'wp-realtime-sitemap'), the_title_attribute('echo=0')) . '" rel="bookmark">' . get_the_title() . '</a>' . $extra . '</li>';
									}
									
									wp_reset_postdata();
								}
								
								$posts .= '</ul></li>';
							}

							$posts_header = (empty($posts_header)) ? __('Posts', 'wp-realtime-sitemap') : $posts_header;

							$posts = '<div id="wp-realtime-sitemap-posts"><h3>' . $posts_header . '</h3><ul>' . $posts . '</ul></div>';
						}

					} else {
						// Set options for post query
						$theposts = get_posts(array(
							'numberposts'	=> $post_numberposts,
							'orderby'	=> $post_orderby,
							'order'		=> $post_order,
							'exclude'	=> $post_exclude,
							'post_type'	=> 'post',
						));

						if (count($theposts) > 0) {
							foreach($theposts as $post) {
								setup_postdata($post);

								$extra = '';

								if ($post_show_date == 'Yes')
									$extra = ' <span>' . get_the_date() . '</span>';

								$posts .= '<li><a href="' . get_permalink() . '" title="' . sprintf(esc_attr__('Permalink to %s', 'wp-realtime-sitemap'), the_title_attribute('echo=0')) . '" rel="bookmark">' . get_the_title() . '</a>' . $extra . '</li>';
							}

							wp_reset_postdata();

							$posts_header = (empty($posts_header)) ? __('Posts', 'wp-realtime-sitemap') : $posts_header;

							$posts = '<div id="wp-realtime-sitemap-posts"><h3>' . $posts_header . '</h3><ul>' . $posts . '</ul></div>';
						}
					}
				}

				// Custom Post Types: Yes/No?
				if (($all_options['show_custom_post_types'] != 'No' && $all_options['show_custom_post_types'] != 'Off') && ($show == 'custom-posts' || $show == 'all')) {
					$post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');

					foreach ($post_types as $post_type) {
						$custom_post_type_posts = '';

						// Set options for post query
						$theposts = get_posts(array(
							'numberposts'	=> $post_numberposts,
							'orderby'	=> $post_orderby,
							'order'		=> $post_order,
							'exclude'	=> $post_exclude,
							'post_type'	=> $post_type->name,
						));

						if (count($theposts) > 0) {
							foreach((array)$theposts as $post) {

								setup_postdata($post);

								$extra = '';

								if ($post_show_date == 'Yes')
									$extra = ' <span>' . get_the_date() . '</span>';

								$custom_post_type_posts .= '<li><a href="' . get_permalink() . '" title="' . sprintf(esc_attr__('Permalink to %s', 'wp-realtime-sitemap'), the_title_attribute('echo=0')) . '" rel="bookmark">' . get_the_title() . '</a>' . $extra . '</li>';
							}

							wp_reset_postdata();

							$custom_posts[$post_type->name] = '<div id="wp-realtime-sitemap-' . strtolower($post_type->name) . '"><h3>' . $post_type->labels->name . '</h3><ul>' . $custom_post_type_posts . '</ul></div>';
						}
					}
				}

				// Archives: Yes/No?
				if (($all_options['show_archives'] != 'No' && $all_options['show_archives'] != 'Off') && ($show == 'archives' || $show == 'all')) {
					$archives_header = (empty($archives_header)) ? __('Archives', 'wp-realtime-sitemap') : $archives_header;

					$archives = '<div id="wp-realtime-sitemap-archives"><h3>' . $archives_header . '</h3>';
					$archives .= '<ul>' . wp_get_archives(array('type' => $archive_type, 'limit' => $archive_limit, 'show_post_count' => $archive_show_post_count, 'echo' => 0)) . '</ul></div>';
				}

				// Categories: Yes/No?
				if (($all_options['show_categories'] != 'No' && $all_options['show_categories'] != 'Off') && ($show == 'categories' || $show == 'all')) {
					$categories_header = (empty($categories_header)) ? __('Categories', 'wp-realtime-sitemap') : $categories_header;

					$categories = '<div id="wp-realtime-sitemap-categories"><h3>' . $categories_header . '</h3>';

					// Tag Cloud: Yes/No?
					if ($all_options['category_tagcloud'] != 'No' && $all_options['category_tagcloud'] != 'Off') {
						$categories .= '<p>' . wp_tag_cloud(array('number' => $category_number, 'format' => 'flat', 'orderby' => $category_orderby, 'order' => $category_order, 'taxonomy' => 'category', 'echo' => '0')) . '</p>';
					} else {
						$categories .= '<ul>' . wp_list_categories(array('orderby' => $category_orderby, 'order' => $category_order, 'show_count' => $category_show_post_count, 'hide_empty' => $category_hide_empty, 'exclude' => $category_exclude, 'hierarchical' => '1', 'title_li' => '', 'number' => $category_number, 'echo' => '0', 'depth' => $category_depth, 'taxonomy' => 'category')) . '</ul>';
					}

					$categories .= '</div>';
				}

				// Tags: Yes/No?
				if (($all_options['show_tags'] != 'No' && $all_options['show_tags'] != 'Off') && ($show == 'tags' || $show == 'all')) {
					$tags_header = (empty($tags_header)) ? __('Tags', 'wp-realtime-sitemap') : $tags_header;

					$tags = '<div id="wp-realtime-sitemap-tags"><h3>' . $tags_header . '</h3>';

					// Tag Cloud: Yes/No?
					if ($all_options['tags_tagcloud'] != 'No' && $all_options['tags_tagcloud'] != 'Off') {
						$tags .= '<p>' . wp_tag_cloud(array('number' => $tags_number, 'format' => 'flat', 'orderby' => $tags_orderby, 'order' => $tags_order, 'taxonomy' => 'post_tag', 'echo' => '0')) . '</p>';
					} else {
						$tags .= '<ul>' . wp_list_categories(array('orderby' => $tags_orderby, 'order' => $tags_order, 'show_count' => $tags_show_post_count, 'hide_empty' => (boolean)$tags_hide_empty, 'exclude' => $tags_exclude, 'hierarchical' => '1', 'title_li' => '', 'number' => $tags_number, 'echo' => '0', 'taxonomy' => 'post_tag')) . '</ul>';
					}

					$tags .= '</div>';
				}

				if (($all_options['show_promote'] != 'No' && $all_options['show_promote'] != 'Off') && ($show == 'all')) {
					$promote = '<p align="center">Sitemap created with <a href="http://goo.gl/ri9xU" title="WP Realtime Sitemap">WP Realtime Sitemap</a>.</p>';
				}

				// Left in temporary for backwards compatibility.
				if ($show == 'menu') {
					return $menu;
				}

				if ($show == 'pages') {
					return $pages;
				}

				if ($show == 'posts') {
					return $posts;
				}

				if ($show == 'custom-posts') {
					return implode('', $custom_posts);
				}

				if ($show == 'archives') {
					return $archives;
				}

				if ($show == 'categories') {
					return $categories;
				}

				if ($show == 'tags') {
					return $tags;
				}

				if ($show == 'all') {
					$custom_posts = implode('', $custom_posts);
					return $$all_options['first_order'] . $$all_options['second_order'] . $$all_options['third_order'] . $$all_options['fourth_order'] . $$all_options['fifth_order'] . $$all_options['sixth_order'] . $$all_options['seventh_order'] . $promote;
				}
			}
		}

		function installSettings() {
		   	global $wpdb, $wp_roles, $wp_version;

			// Check for capability
			if (!current_user_can('activate_plugins'))
				return;

			// Get any settings from the database
			$options = get_option('plugin_wp_realtime_sitemap_settings');

			// If no options set in the database, set to array().
			if (empty($options)) $options = array();

			// Check if any missing settings
			$options = array_merge($options, array_diff_key($this->_defaultSettings(), $options));

			// Install settings into the database
			update_option('plugin_wp_realtime_sitemap_settings', $options);
		}

		function _defaultSettings() {
			$defaults = array(
				'menu_id'			=> '',

				'page_sort_column'		=> 'post_title',
				'page_sort_order'		=> 'ASC',
				'page_exclude'			=> '',
				'page_depth'			=> '0',
				'page_show_date'		=> '',
//				'page_number'			=> '',

				'post_orderby'			=> 'post_date',
				'post_order'			=> 'ASC',
				'post_exclude'			=> '',
				'post_show_date'		=> 'No',
				'post_numberposts'		=> '-1',
				'post_show_categories'		=> 'No',

				'archive_type'			=> 'monthly',
				'archive_limit'			=> '',
				'archive_show_post_count'	=> 1,	// false

				'category_tagcloud'		=> 'No',
				'category_orderby'		=> 'name',
				'category_order'		=> 'ASC',
				'category_show_post_count'	=> 0,	// false
				'category_hide_empty'		=> 1,	// true
				'category_exclude'		=> '',
				'category_number'		=> '0',
				'category_depth'		=> '0',

				'tags_tagcloud'			=> 'No',
				'tags_orderby'			=> 'name',
				'tags_order'			=> 'ASC',
				'tags_show_post_count'		=> 0,	// false
				'tags_hide_empty'		=> 1,	// true
				'tags_exclude'			=> '',
				'tags_number'			=> '0',

				'menu_header'			=> '',
				'pages_header'			=> '',
				'posts_header'			=> '',
				'archives_header'		=> '',
				'categories_header'		=> '',
				'tags_header'			=> '',

				'show_menu'			=> 'No',
				'show_pages'			=> 'Yes',
				'show_posts'			=> 'Yes',
				'show_custom_post_types'	=> 'No',
				'show_archives'			=> 'Yes',
				'show_categories'		=> 'Yes',
				'show_tags'			=> 'No',
				'show_promote'			=> 'Yes',

				'first_order'			=> 'menu',
				'second_order'			=> 'pages',
				'third_order'			=> 'posts',
				'fourth_order'			=> 'custom_posts',
				'fifth_order'			=> 'archives',
				'sixth_order'			=> 'categories',
				'seventh_order'			=> 'tags',

				'install_date'			=> time(),
			);

			return $defaults;
		}

		function upgradeSettingsNotice() {
			echo '<div class="wprs-update-nag">' . sprintf(__('WP Realtime Sitemap was just updated... Please visit the %sPlugin Options Page%s and re-save your preferences.', 'wp-realtime-sitemap'), '<a href="options-general.php?page=wp-realtime-sitemap.php" style="color: #ca0c01">', '</a>') . '</div>';
		}

		function uninstallSettings() {
		   	global $wpdb, $wp_roles, $wp_version;

			delete_option('plugin_wp_realtime_sitemap_settings');

			// Delete old unused database entries - v1.0
			delete_option('wp_realtime_sitemap_orderby');
			delete_option('wp_realtime_sitemap_private');
			delete_option('wp_realtime_sitemap_pages');
			delete_option('wp_realtime_sitemap_posts');
			delete_option('wp_realtime_sitemap_tags');
			delete_option('wp_realtime_sitemap_archives');
			delete_option('wp_realtime_sitemap_displayorder');

			// Delete old unused database entries - v1.1
			delete_option('wp_realtime_sitemap_orderby');
			delete_option('wp_realtime_sitemap_showprivate');
			delete_option('wp_realtime_sitemap_showpages');
			delete_option('wp_realtime_sitemap_showposts');
			delete_option('wp_realtime_sitemap_showarchives');
			delete_option('wp_realtime_sitemap_showcategories');
			delete_option('wp_realtime_sitemap_showcategoriesastc');
			delete_option('wp_realtime_sitemap_showtags');
			delete_option('wp_realtime_sitemap_showtagsastc');
			delete_option('wp_realtime_sitemap_displayorder');

			if (false !== get_option('plugin_wp_realtime_sitemap_version') || get_option('plugin_wp_realtime_sitemap_version') == '') {
				update_option('plugin_wp_realtime_sitemap_version', WPRealtimeSitemap_Version);
			}
		}
	}
}

if( class_exists('WPRealtimeSitemap') )
	$wpRealtimeSitemap = new WPRealtimeSitemap();

?>
