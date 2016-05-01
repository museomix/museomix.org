<?php

require_once ICL_PLUGIN_PATH . "/inc/taxonomy-term-translation/wpml-term-language-synchronization.class.php";

class WPML_Config
{
	static $wpml_config_files = array();
    static $active_plugins = array();

	public static function load_config()
	{
		global $pagenow;

		if ( !( is_admin() && !wpml_is_ajax() && ( !isset( $_POST[ 'action' ] ) || $_POST[ 'action' ] != 'heartbeat' ) ) ) {
			return;
		}

		$white_list_pages = array(
			'theme_options',
			'plugins.php',
			'themes.php',
			ICL_PLUGIN_FOLDER . '/menu/languages.php',
			ICL_PLUGIN_FOLDER . '/menu/theme-localization.php',
			ICL_PLUGIN_FOLDER . '/menu/translation-options.php',
		);
		if (defined('WPML_ST_FOLDER')) {
			$white_list_pages[] = WPML_ST_FOLDER . '/menu/string-translation.php';
		}
		if(defined('WPML_TM_FOLDER')) {
			$white_list_pages[] = WPML_TM_FOLDER . '/menu/main.php';
		}

		//Runs the load config process only on specific pages
		$current_page = isset($_GET[ 'page' ]) ? $_GET[ 'page' ] : null;
		if((isset( $current_page ) && in_array( $current_page, $white_list_pages)) || (isset($pagenow) && in_array($pagenow, $white_list_pages))) {
			self::load_config_run();
		}
	}

	static function load_config_run() {
		global $sitepress;
		self::load_config_pre_process();
		self::load_plugins_wpml_config();
		self::load_theme_wpml_config();
		self::parse_wpml_config_files();
		self::load_config_post_process();
		$sitepress->save_settings();
	}

	static function load_config_pre_process() {
		global $iclTranslationManagement;
		$tm_settings = $iclTranslationManagement->settings;

		if ( ( isset( $tm_settings[ 'custom_types_readonly_config' ] ) && is_array( $tm_settings[ 'custom_types_readonly_config' ] ) ) ) {
			$iclTranslationManagement->settings[ '__custom_types_readonly_config_prev' ] = $tm_settings[ 'custom_types_readonly_config' ];
		} else {
			$iclTranslationManagement->settings[ '__custom_types_readonly_config_prev' ] = array();
		}
		$iclTranslationManagement->settings[ 'custom_types_readonly_config' ] = array();

		if ( ( isset( $tm_settings[ 'custom_fields_readonly_config' ] ) && is_array( $tm_settings[ 'custom_fields_readonly_config' ] ) ) ) {
			$iclTranslationManagement->settings[ '__custom_fields_readonly_config_prev' ] = $tm_settings[ 'custom_fields_readonly_config' ];
		} else {
			$iclTranslationManagement->settings[ '__custom_fields_readonly_config_prev' ] = array();
		}
		$iclTranslationManagement->settings[ 'custom_fields_readonly_config' ] = array();
	}

	static function load_plugins_wpml_config() {
		if ( is_multisite() ) {
			// Get multi site plugins
			$plugins = get_site_option( 'active_sitewide_plugins' );
			if ( !empty( $plugins ) ) {
				foreach ( $plugins as $p => $dummy ) {
                    if(!self::check_on_config_file($dummy)){
                        continue;
                    }
					$plugin_slug = dirname( $p );
					$config_file = WP_PLUGIN_DIR . '/' . $plugin_slug . '/wpml-config.xml';
					if ( trim( $plugin_slug, '\/.' ) && file_exists( $config_file ) ) {
						self::$wpml_config_files[ ] = $config_file;
					}
				}
			}
		}

		// Get single site or current blog active plugins
		$plugins = get_option( 'active_plugins' );
		if ( !empty( $plugins ) ) {
			foreach ( $plugins as $p ) {
                if(!self::check_on_config_file($p)){
                    continue;
                }

				$plugin_slug = dirname( $p );
				$config_file = WP_PLUGIN_DIR . '/' . $plugin_slug . '/wpml-config.xml';
				if ( trim( $plugin_slug, '\/.' ) && file_exists( $config_file ) ) {
					self::$wpml_config_files[ ] = $config_file;
				}
			}
		}

		// Get the must-use plugins
		$mu_plugins = wp_get_mu_plugins();

		if ( !empty( $mu_plugins ) ) {
			foreach ( $mu_plugins as $mup ) {
                if(!self::check_on_config_file($mup)){
                    continue;
                }

				$plugin_dir_name  = dirname( $mup );
				$plugin_base_name = basename( $mup, ".php" );
				$plugin_sub_dir   = $plugin_dir_name . '/' . $plugin_base_name;
				if ( file_exists( $plugin_sub_dir . '/wpml-config.xml' ) ) {
					$config_file                = $plugin_sub_dir . '/wpml-config.xml';
					self::$wpml_config_files[ ] = $config_file;
				}
			}
		}

		return self::$wpml_config_files;
	}

    static function check_on_config_file( $name ){

        if(empty(self::$active_plugins)){
            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            self::$active_plugins = get_plugins();
        }
        $config_index_file_data = maybe_unserialize(get_option('wpml_config_index'));
        $config_files_arr = maybe_unserialize(get_option('wpml_config_files_arr'));

        if(!$config_index_file_data || !$config_files_arr){
            return true;
        }


        if(isset(self::$active_plugins[$name])){
            $plugin_info = self::$active_plugins[$name];
            $plugin_slug = dirname( $name );
            $name = $plugin_info['Name'];
            $config_data = $config_index_file_data->plugins;
            $config_files_arr = $config_files_arr->plugins;
            $config_file = WP_PLUGIN_DIR . '/' . $plugin_slug . '/wpml-config.xml';
            $type = 'plugin';

        }else{
            $config_data = $config_index_file_data->themes;
            $config_files_arr = $config_files_arr->themes;
            $config_file = get_template_directory() . '/wpml-config.xml';
            $type = 'theme';
        }

        foreach($config_data as $item){
            if($name == $item->name && isset($config_files_arr[$item->name])){
                if($item->override_local || !file_exists( $config_file )){
                    end(self::$wpml_config_files);
                    $key = key(self::$wpml_config_files)+1;
                    self::$wpml_config_files[$key] = new stdClass();
                    self::$wpml_config_files[$key]->config = icl_xml2array($config_files_arr[$item->name]);
                    self::$wpml_config_files[$key]->type = $type;
                    self::$wpml_config_files[$key]->admin_text_context = basename( dirname( $config_file ) );
                    return false;
                }else{
                    return true;
                }
            }
        }

        return true;

    }

	static function load_theme_wpml_config()
	{
        $theme_data = wp_get_theme();
        if(!self::check_on_config_file($theme_data->get('Name'))){
            return self::$wpml_config_files;
        }

		if ( get_template_directory() != get_stylesheet_directory() ) {
			$config_file = get_stylesheet_directory() . '/wpml-config.xml';
			if ( file_exists( $config_file ) ) {
				self::$wpml_config_files[ ] = $config_file;
			}
		}

		$config_file = get_template_directory() . '/wpml-config.xml';
		if ( file_exists( $config_file ) ) {
			self::$wpml_config_files[ ] = $config_file;
		}

		return self::$wpml_config_files;
	}
	
	static function get_theme_wpml_config_file() {
		if ( get_template_directory() != get_stylesheet_directory() ) {
			$config_file = get_stylesheet_directory() . '/wpml-config.xml';
			if ( file_exists( $config_file ) ) {
				return $config_file;
			}
		}

		$config_file = get_template_directory() . '/wpml-config.xml';
		if ( file_exists( $config_file ) ) {
			return $config_file;
		}
		
		return false;
		
	}

	static function parse_wpml_config_files()
	{
		if ( !empty( self::$wpml_config_files ) ) {

			$config_all[ 'wpml-config' ] = array(
				'custom-fields'              => array(),
				'custom-types'               => array(),
				'taxonomies'                 => array(),
				'admin-texts'                => array(),
				'language-switcher-settings' => array()
			);

			foreach ( self::$wpml_config_files as $file ) {
				if ( is_object( $file ) ) {
					$config             = $file->config;
					$type               = $file->type;
					$admin_text_context = $file->admin_text_context;
				} else {
					$config             = icl_xml2array( file_get_contents( $file ) );
					$type               = ( dirname( $file ) == get_template_directory() || dirname( $file ) == get_stylesheet_directory() ) ? 'theme' : 'plugin';
					$admin_text_context = basename( dirname( $file ) );
				}

				if ( isset( $config[ 'wpml-config' ] ) ) {
                    $wpml_config     = $config[ 'wpml-config' ];
                    $wpml_config_all = $config_all[ 'wpml-config' ];
                    $wpml_config_all = self::parse_config_index($wpml_config_all, $wpml_config, 'custom-field', 'custom-fields');
					$wpml_config_all = self::parse_config_index($wpml_config_all, $wpml_config, 'custom-type', 'custom-types');
                    $wpml_config_all = self::parse_config_index($wpml_config_all, $wpml_config, 'taxonomy', 'taxonomies');
					//admin-texts
					if ( isset( $wpml_config[ 'admin-texts' ][ 'key' ] ) ) {
						if ( ! is_numeric( key( @current( $wpml_config[ 'admin-texts' ] ) ) ) ) { //single
							$wpml_config[ 'admin-texts' ][ 'key' ][ 'type' ]    = $type;
							$wpml_config[ 'admin-texts' ][ 'key' ][ 'context' ] = $admin_text_context;
							$wpml_config_all[ 'admin-texts' ][ 'key' ][ ]       = $wpml_config[ 'admin-texts' ][ 'key' ];
						} else {
							foreach ( (array) $wpml_config[ 'admin-texts' ][ 'key' ] as $cf ) {
								$cf[ 'type' ]                                             = $type;
								$cf[ 'context' ]                                          = $admin_text_context;
								$wpml_config_all[ 'admin-texts' ][ 'key' ][ ] = $cf;
							}
						}
					}

					//language-switcher-settings
					if ( isset( $wpml_config[ 'language-switcher-settings' ][ 'key' ] ) ) {
						if ( !is_numeric( key( $wpml_config[ 'language-switcher-settings' ][ 'key' ] ) ) ) { //single
							$wpml_config_all[ 'language-switcher-settings' ][ 'key' ][ ] = $wpml_config[ 'language-switcher-settings' ][ 'key' ];
						} else {
							foreach ( $wpml_config[ 'language-switcher-settings' ][ 'key' ] as $cf ) {
								$wpml_config_all[ 'language-switcher-settings' ][ 'key' ][ ] = $cf;
							}
						}
					}
                    $config_all[ 'wpml-config' ] = $wpml_config_all;
				}
			}

			$config_all = apply_filters( 'icl_wpml_config_array', $config_all );
			$config_all = apply_filters( 'wpml_config_array', $config_all );

			self::parse_wpml_config( $config_all );
		}
	}

    private static function parse_config_index( $config_all, $wpml_config, $index_sing, $index_plur ) {
        if ( isset( $wpml_config[ $index_plur ][ $index_sing ] ) ) {
            if ( isset( $wpml_config[ $index_plur ][ $index_sing ][ 'value' ] ) ) { //single
                $config_all[ $index_plur ][ $index_sing ][ ] = $wpml_config[ $index_plur ][ $index_sing ];
            } else {
                foreach ( (array) $wpml_config[ $index_plur ][ $index_sing ] as $cf ) {
                    $config_all[ $index_plur ][ $index_sing ][ ] = $cf;
                }
            }
        }

        return $config_all;
    }

    static function load_config_post_process()
	{
		global $iclTranslationManagement;

		$changed = false;
		if ( isset( $iclTranslationManagement->settings[ '__custom_types_readonly_config_prev' ] ) ) {
			foreach ( $iclTranslationManagement->settings[ '__custom_types_readonly_config_prev' ] as $pk => $pv ) {
				if ( !isset( $iclTranslationManagement->settings[ 'custom_types_readonly_config' ][ $pk ] ) || $iclTranslationManagement->settings[ 'custom_types_readonly_config' ][ $pk ] != $pv ) {
					$changed = true;
					break;
				}
			}
		}
		if ( isset( $iclTranslationManagement->settings[ 'custom_types_readonly_config' ] ) ) {
			foreach ( $iclTranslationManagement->settings[ 'custom_types_readonly_config' ] as $pk => $pv ) {
				if ( !isset( $iclTranslationManagement->settings[ '__custom_types_readonly_config_prev' ][ $pk ] ) || $iclTranslationManagement->settings[ '__custom_types_readonly_config_prev' ][ $pk ] != $pv ) {
					$changed = true;
					break;
				}
			}
		}
		if ( isset( $iclTranslationManagement->settings[ 'custom_fields_readonly_config' ]  ) && isset($iclTranslationManagement->settings[ '__custom_fields_readonly_config_prev' ]) ) {
			foreach ( $iclTranslationManagement->settings[ '__custom_fields_readonly_config_prev' ] as $cf ) {
				if ( !in_array( $cf, $iclTranslationManagement->settings[ 'custom_fields_readonly_config' ] ) ) {
					$changed = true;
					break;
				}
			}

			foreach ( $iclTranslationManagement->settings[ 'custom_fields_readonly_config' ] as $cf ) {
				if ( !in_array( $cf, $iclTranslationManagement->settings[ '__custom_fields_readonly_config_prev' ] ) ) {
					$changed = true;
					break;
				}
			}
		}

		if ( $changed ) {
			$iclTranslationManagement->save_settings();
		}

	}

	static function parse_wpml_config( $config ) {
		global $sitepress, $sitepress_settings, $iclTranslationManagement;

		// custom fields
		self::parse_custom_fields( $config );

		// custom types
		self::update_tm_settings( $config, 'custom-type', 'custom-types' );

		// taxonomies
		self::update_tm_settings( $config, 'taxonomy', 'taxonomies' );

		// admin texts
		self::parse_admin_texts( $config );

		// language-switcher-settings
		if ( empty( $sitepress_settings[ 'language_selector_initialized' ] ) || ( isset( $_GET[ 'restore_ls_settings' ] ) && $_GET[ 'restore_ls_settings' ] == 1 ) ) {
			if ( !empty( $config[ 'wpml-config' ][ 'language-switcher-settings' ] ) ) {

				if ( !is_numeric( key( $config[ 'wpml-config' ][ 'language-switcher-settings' ][ 'key' ] ) ) ) {
					$cfgsettings[ 0 ] = $config[ 'wpml-config' ][ 'language-switcher-settings' ][ 'key' ];
				} else {
					$cfgsettings = $config[ 'wpml-config' ][ 'language-switcher-settings' ][ 'key' ];
				}
				$iclsettings = $iclTranslationManagement->read_settings_recursive( $cfgsettings );

				$iclsettings[ 'language_selector_initialized' ] = 1;

				$sitepress->save_settings( $iclsettings );

				if ( !empty( $sitepress_settings[ 'setup_complete' ] ) && !empty( $_GET[ 'page' ] ) ) {
					wp_redirect( admin_url( 'admin.php?page=' . $_GET[ 'page' ] . '&icl_ls_reset=default#icl_save_language_switcher_options' ) );
				}
			}
		}
	}

	/**
	 * @param $config
	 *
	 * @return mixed
	 */
	protected static function parse_custom_fields( $config )
	{
		global $iclTranslationManagement;
		if ( !empty( $config[ 'wpml-config' ][ 'custom-fields' ] ) ) {
			if ( !is_numeric( key( current( $config[ 'wpml-config' ][ 'custom-fields' ] ) ) ) ) {
				$cf[ 0 ] = $config[ 'wpml-config' ][ 'custom-fields' ][ 'custom-field' ];
			} else {
				$cf = $config[ 'wpml-config' ][ 'custom-fields' ][ 'custom-field' ];
			}
			foreach ( $cf as $c ) {
				if ( $c[ 'attr' ][ 'action' ] == 'translate' ) {
					$action = 2;
				} elseif ( $c[ 'attr' ][ 'action' ] == 'copy' ) {
					$action = 1;
				} else {
					$action = 0;
				}
				$iclTranslationManagement->settings[ 'custom_fields_translation' ][ $c[ 'value' ] ] = $action;
				$custom_fields_readonly_config_set = isset( $iclTranslationManagement->settings[ 'custom_fields_readonly_config' ] ) && is_array( $iclTranslationManagement->settings[ 'custom_fields_readonly_config' ] );
				if(!$custom_fields_readonly_config_set) {
					$iclTranslationManagement->settings[ 'custom_fields_readonly_config' ] = array();
				}
				if ( !in_array( $c[ 'value' ], $iclTranslationManagement->settings[ 'custom_fields_readonly_config' ] ) ) {
					$iclTranslationManagement->settings[ 'custom_fields_readonly_config' ][ ] = $c[ 'value' ];
				}
			}
		}
	}

	private static function sync_settings( $config, $section_singular, $section_plural, $read_only_section ) {
		global $sitepress, $iclTranslationManagement;

		if ( ! empty( $config[ 'wpml-config' ][ $section_plural ] ) ) {
			$sync_index  = $section_plural . '_sync_option';
			$sync_option = $sitepress->get_setting( $sync_index, array() );
			if ( ! is_numeric( key( current( $config[ 'wpml-config' ][ $section_plural ] ) ) ) ) {
				$cf[ 0 ] = $config[ 'wpml-config' ][ $section_plural ][ $section_singular ];
			} else {
				$cf = $config[ 'wpml-config' ][ $section_plural ][ $section_singular ];
			}

			foreach ( $cf as $c ) {
				$sync_existing_setting                                                     = isset( $sync_option[ $c[ 'value' ] ] )
					? $sync_option[ $c[ 'value' ] ] : false;
				$translate                                                                 = intval( $c[ 'attr' ][ 'translate' ] );
				$iclTranslationManagement->settings[ $read_only_section ][ $c[ 'value' ] ] = $translate;
				$sync_option[ $c[ 'value' ] ]                                              = $translate;

				// this has just changed. save.
				if ( $translate && $translate != $sync_existing_setting ) {
					if ( $section_plural === 'taxonomies' ) {
						$sitepress->verify_taxonomy_translations( $c[ 'value' ] );
					} else {
						$sitepress->verify_post_translations( $c[ 'value' ] );
					}
					$iclTranslationManagement->save_settings();
				}
			}

			$sitepress->set_setting( $sync_index, $sync_option );
			self::maybe_add_filter( $section_plural );
		}
	}

	private static function maybe_add_filter( $config_type ) {
		global $wpml_settings_helper;

		wpml_load_settings_helper();

		if ( $config_type === 'taxonomies' ) {
			add_filter( 'get_translatable_taxonomies',
						array( $wpml_settings_helper, '_override_get_translatable_taxonomies' ) );
		} elseif ( $config_type === 'custom-types' ) {
			add_filter( 'get_translatable_documents',
						array( $wpml_settings_helper, '_override_get_translatable_documents' ) );
		}
	}

	public static function get_custom_fields_translation_settings($translation_actions = array(0)) {
		$iclTranslationManagement = wpml_load_core_tm ();
		$section          = 'custom_fields_translation';

		$result = array();
		$tm_settings = $iclTranslationManagement->settings;
		if(isset( $tm_settings[ $section ])) {

			foreach ( $tm_settings[ $section ] as $meta_key => $translation_type ) {
				if ( in_array($translation_type, $translation_actions) ) {
					$result[] = $meta_key;
				}
			}
		}

		return $result;
	}

	private static function update_tm_settings( $config, $section_singular, $section_plural ) {

		$config[ 'wpml-config' ] = array_filter ( $config[ 'wpml-config' ] );
		if ( !isset( $config[ 'wpml-config' ][ $section_plural ] )
		     || !isset( $config[ 'wpml-config' ][ $section_plural ][ $section_singular ] ) ) {
			return false;
		}

		$iclTranslationManagement = wpml_load_core_tm ();
		$read_only_section          = $section_plural . '_readonly_config';
		self::sync_settings ( $config, $section_singular, $section_plural, $read_only_section );

		// taxonomies - check what's been removed
		if ( !empty( $iclTranslationManagement->settings[ $read_only_section ] ) ) {
			$config_values           = array();
			foreach ( $config[ 'wpml-config' ][ $section_plural ][ $section_singular ] as $config_value ) {
				$config_values[ $config_value[ 'value' ] ] = $config_value[ 'attr' ][ 'translate' ];
			}
			foreach ( $iclTranslationManagement->settings[ $read_only_section ] as $key => $translation_option ) {
				if ( !isset( $config_values[ $key ] ) ) {
					unset( $iclTranslationManagement->settings[ $read_only_section ][ $key ] );
				}
			}

			$iclTranslationManagement->save_settings ();
		}
	}

	/**
	 * @param $config
	 *
	 * @return array
	 */
	protected static function parse_admin_texts( $config )
	{
		if (class_exists('WPML_Admin_Texts')) {
			$wpml_admin_text = WPML_Admin_Texts::get_instance();
			$wpml_admin_text->parse_config ( $config );
		}

	}
}
