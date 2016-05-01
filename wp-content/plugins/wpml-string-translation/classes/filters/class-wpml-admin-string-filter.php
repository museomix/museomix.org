<?php

class WPML_Admin_String_Filter extends WPML_Displayed_String_Filter {

	private $registered_string_cache = array();
	
	/** @var  WPML_ST_String_Factory $string_factory */
	private $string_factory;

	/**
	 * @param wpdb                         $wpdb
	 * @param SitePress                    $sitepress
	 * @param string                       $language
	 * @param WPML_ST_String_Factory       $string_factory
	 * @param WPML_Displayed_String_Filter $existing_filter
	 */
	public function __construct( &$wpdb, &$sitepress, $language, &$string_factory, $existing_filter = null ) {
		parent::__construct( $wpdb, $sitepress, $language, $existing_filter );
		$this->string_factory = &$string_factory;
	}

	public function translate_by_name_and_context( $untranslated_text, $name, $context = "", &$has_translation = null ) {
		if ( $untranslated_text ) {
			$translation = $this->string_from_registered( $name, $context );
			if ( $translation === false && $untranslated_text !== false && $this->use_original_cache ) {
				// lookup translation from original text
				$key         = md5( $untranslated_text );
				$translation = isset( $this->original_cache[ $key ] ) ? $this->original_cache[ $key ] : false;
			}

			if ( $translation === false ) {
				$this->register_string( $context, $name, $untranslated_text );
				$translation = $untranslated_text;
			}
		} else {
			$translation = parent::translate_by_name_and_context( $untranslated_text, $name, $context );
		}
		$has_translation = $translation !== false && $translation != $untranslated_text;

		return $translation !== false ? $translation : $untranslated_text;
	}

	public function register_string( $context, $name, $value, $allow_empty_value = false, $source_lang = '' ) {
		global $WPML_Sticky_Links;

		$name = trim( $name ) ? $name : md5( $value );
		/* cpt slugs - do not register them when scanning themes and plugins
		 * if name starting from 'URL slug: '
		 * and context is different from 'WordPress'
		 */
		if ( substr( $name, 0, 10 ) === 'URL slug: ' && 'WordPress' !== $context ) {
			return false;
		}

		list( $domain, $context, $key ) = $this->key_by_name_and_context( $name, $context );
		list( $name, $context ) = $this->truncate_name_and_context( $name, $context );

		if ( $source_lang == '' ) {
			$lang_of_domain = new WPML_Language_Of_Domain( $this->sitepress );
			$domain_lang    = $lang_of_domain->get_language( $domain );
			$source_lang    = $domain_lang ? $domain_lang
				: ( strpos( $domain, 'admin_texts_' ) === 0
				    || $name === 'Tagline' || $name === 'Blog Title'
					? $this->sitepress->get_user_admin_language( get_current_user_id() ) : 'en' );
			$source_lang    = $source_lang ? $source_lang : 'en';
		}

		$res = $this->get_registered_string( $domain, $context, $name );
		if ( $res ) {
			$string_id = $res['id'];
			/*
			 * If Sticky Links plugin is active and set to change links in Strings,
			 * we need to process $value and change links into sticky before comparing
			 * with saved in DB $res->value.
			 * Otherwise after every String Translation screen refresh status of this string
			 * will be changed into 'needs update'
			 */
			$alp_settings = get_option( 'alp_settings' );
			if ( ! empty( $alp_settings['sticky_links_strings'] ) // do we have setting about sticky links in strings?
			     && $alp_settings['sticky_links_strings'] // is this set to TRUE?
			     && defined( 'WPML_STICKY_LINKS_VERSION' )
			) { // sticky links plugin is active?
				require_once ICL_PLUGIN_PATH . '/inc/absolute-links/absolute-links.class.php';
				$absolute_links_object = new AbsoluteLinks;
				$alp_broken_links      = array();
				$value                 = $absolute_links_object->_process_generic_text( $value, $alp_broken_links );
			}
			$update_string = array();
			if ( $value != $res['value'] ) {
				$update_string['value'] = $value;
			}
			$existing_lang = $this->string_factory->find_by_id($res['id'])->get_language();
			if ( ! empty( $update_string ) ) {
				if ( $existing_lang == $source_lang ) {
					$this->wpdb->update( $this->wpdb->prefix . 'icl_strings', $update_string, array( 'id' => $string_id ) );
					$this->wpdb->update( $this->wpdb->prefix . 'icl_string_translations',
						array( 'status' => ICL_TM_NEEDS_UPDATE ),
						array( 'string_id' => $string_id ) );
					icl_update_string_status( $string_id );
				} else {
					$orig_data = array( 'string_id' => $string_id, 'language' => $source_lang );
					$update_string['status'] = ICL_TM_COMPLETE;
					if ( $this->wpdb->get_var( $this->wpdb->prepare( "SELECT COUNT(*)
																	  FROM {$this->wpdb->prefix}icl_string_translations
																	  WHERE string_id = %d
																	  	AND language = %s",
						$string_id, $source_lang ) )
					) {
						$this->wpdb->update( $this->wpdb->prefix . 'icl_string_translations',
							$update_string,
							$orig_data );
					} else {
						$this->wpdb->insert( $this->wpdb->prefix . 'icl_string_translations',
							array_merge( $update_string, $orig_data ) );
					}
					icl_update_string_status( $string_id );
				}
			}
		} else {
			$string_id = $this->save_string( $value, $allow_empty_value, $source_lang, $domain, $context, $name );
		}

		if ( defined( 'WPML_TM_PATH' ) && ! empty( $WPML_Sticky_Links ) && $WPML_Sticky_Links->settings['sticky_links_strings'] ) {
			require_once WPML_TM_PATH . '/inc/translation-proxy/wpml-pro-translation.class.php';
			WPML_Pro_Translation::_content_make_links_sticky( $string_id, 'string', false );
		}

		if ( ! isset( $this->name_cache[ $key ] ) ) {
			$this->name_cache[ $key ] = $value;
		}

		return $string_id;
	}
	
	private function get_registered_string( $domain, $context, $name ) {
		
		if ( ! isset( $this->registered_string_cache[ $domain ] ) ) {
			// preload all the strings for this domain.

			$query = $this->wpdb->prepare( "SELECT id, value, gettext_context, name FROM {$this->wpdb->prefix}icl_strings WHERE context=%s",
										   $domain,
										   $context,
										   $name );
			$res   = $this->wpdb->get_results( $query );
			$this->registered_string_cache[ $domain ] = array();
			
			foreach( $res as $string ) {
				$this->registered_string_cache[ $domain ][ md5( $domain . $string->name . $string->gettext_context ) ] = array( 'id'    => $string->id,
																															    'value' => $string->value
																															  );
			}
		}

		$key = md5( $domain . $name . $context );
		if ( ! isset( $this->registered_string_cache[ $domain ][ $key ] ) ) {
			$this->registered_string_cache[ $domain ][ $key ] = null;
		}
		return $this->registered_string_cache[ $domain ][ $key ];
	}
	
	

	private function save_string( $value, $allow_empty_value, $language, $domain, $context, $name ) {
		if ( $allow_empty_value || 0 !== strlen( $value ) ) {
			$this->wpdb->insert( $this->wpdb->prefix . 'icl_strings', array(
				'language'                => $language,
				'context'                 => $domain,
				'gettext_context'         => $context,
				'domain_name_context_md5' => md5( $domain . $name . $context ),
				'name'                    => $name,
				'value'                   => $value,
				'status'                  => ICL_TM_NOT_TRANSLATED,
			) );
			$string_id = $this->wpdb->insert_id;
			if ( $string_id === 0 ) {
				throw new Exception( 'Count not add String with arguments: value: ' . $value . ' allow_empty_value:' . $allow_empty_value . ' language: ' . $language );
			}

			icl_update_string_status( $string_id );
			
			$key = md5( $domain . $name . $context );
			$this->registered_string_cache[ $domain ][ $key ] = array( 'id'    => $string_id,
																	   'value' => $value
																	 );
		} else {
			$string_id = 0;
		}

		return $string_id;
	}
}