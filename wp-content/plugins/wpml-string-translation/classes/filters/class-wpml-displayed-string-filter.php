<?php

/**
 * Class WPML_Displayed_String_Filter
 *
 * Handles all string translating when rendering translated strings to the user, unless auto-registering is
 * active for strings.
 */
class WPML_Displayed_String_Filter extends WPML_WPDB_And_SP_User {

	protected $language;
	protected $original_cache = array();
	protected $name_cache = array();
	protected $untranslated_cache = array();
	protected $use_original_cache;
	protected $cache_is_warm = false;

	/**
	 * @param wpdb $wpdb
	 * @param SitePress $sitepress
	 * @param string $language
	 * @param null|object $existing_filter
	 */
	public function __construct( &$wpdb, &$sitepress, $language, $existing_filter = null ) {
		parent::__construct( $wpdb, $sitepress );
		$this->language           = $language;
		
		if ( $existing_filter ) {
			$this->original_cache = $existing_filter->original_cache;
			$this->name_cache = $existing_filter->name_cache;
			$this->untranslated_cache = $existing_filter->untranslated_cache;
			$this->use_original_cache = $existing_filter->use_original_cache;
			$this->cache_is_warm = $existing_filter->cache_is_warm;
		} else {
			$this->use_original_cache = $this->use_original_cache();
		}
	}

	/**
	 * @param string       $untranslated_text
	 * @param string       $name
	 * @param string|array $context
	 * @param null|bool    $has_translation
	 *
	 * @return bool|false|string
	 */
	public function translate_by_name_and_context( $untranslated_text, $name, $context = "", &$has_translation = null ) {
		$res = $this->string_from_registered( $name, $context );
		$untranslated_text_has_content = !is_array($untranslated_text) && strlen( $untranslated_text ) !== 0 ? true : false;
		if ( $res === false
		     && $untranslated_text_has_content === true
		     && $this->use_original_cache && substr( $name, 0, 10 ) !== 'URL slug: '
			 && ! ( $context === 'default' || $context === 'Wordpess' )
		) {
			// Didn't find a translation with the exact name and context
			// lookup translation from original text (but don't do it for URL slug or default or Wordpress domain)
			$key = md5( $untranslated_text );
			$res = isset( $this->original_cache[ $key ] ) ? $this->original_cache[ $key ] : false;
		}
		list( , , $key ) = $this->key_by_name_and_context( $name, $context );
		$has_translation = $res !== false && ! isset( $this->untranslated_cache[ $key ] ) ? true : null;
		$res             = $res === false && $untranslated_text_has_content === true ? $untranslated_text : $res;
		$res             = $res === false ? $this->get_original_value( $name, $context ) : $res;

		return $res;
	}

	/**
	 * @param string $name
	 * @param string $context
	 *
	 * Tries to retrieve a string from the cache and runs fallback logic for the default WP context
	 *
	 * @return bool
	 */
	protected function string_from_registered( $name, $context = "" ) {
		if ( $this->cache_is_warm === false ) {
			$this->warm_cache();
		}

		$res = $this->get_string_from_cache( $name, $context );
		$res = $res === false ? $this->try_fallback_domain( $name, $context ) : $res;
		
		return $res;
	}
	
	private function try_fallback_domain( $name, $context ) {

		$res = false;

		if ($context === 'default' ) {
			$res = $this->get_string_from_cache( $name, 'WordPress' );
		} elseif ( $context === 'WordPress' ) {
			$res = $this->get_string_from_cache( $name, 'default' );
		} elseif ( is_array( $context ) && isset ( $context[ 'domain' ] ) ) {
			if ( $context[ 'domain' ] === 'default' ) {
				$context[ 'domain' ] = 'WordPress';
				$res = $this->get_string_from_cache( $name, $context );
			} elseif ( $context[ 'domain' ] === 'Wordpress' ) {
				$context[ 'domain' ] = 'default';
				$res = $this->get_string_from_cache( $name, $context );
			}
		}

		return $res;
	}

	/**
	 * Populates the caches in this object
	 *
	 * @param string|null          $name
	 * @param string|string[]|null $context
	 * @param string               $untranslated_value
	 */
	protected function warm_cache( $name = null, $context = null, $untranslated_value = "" ) {
		$res_args    = array( ICL_TM_COMPLETE, $this->language, $this->language );

		$filter = '';
		if ( null !== $name ) {
			list( , , $key ) = $this->key_by_name_and_context( $name, $context );
			if ( isset( $this->name_cache[ $key ] ) ) {
				return;
			} else {
				$name_cache[ $key ]               = $untranslated_value;
				$this->untranslated_cache[ $key ] = true;
				$filter                           = ' WHERE s.name=%s';
				$res_args[] = $name;
			}
		} else {
			$this->cache_is_warm = true;
		}

		$res_query   = "
					SELECT
						st.value AS tra,
						s.value AS org,
						s.domain_name_context_md5 AS ctx
					FROM {$this->wpdb->prefix}icl_strings s
					LEFT JOIN {$this->wpdb->prefix}icl_string_translations st
						ON s.id=st.string_id
							AND st.status=%d
							AND st.language=%s
							AND s.language!=%s
					{$filter}
					";
		$res_prepare = $this->wpdb->prepare( $res_query, $res_args );
		$res         = $this->wpdb->get_results( $res_prepare, ARRAY_A );

		$name_cache = array();
		$warm_cache = array();
		foreach ( $res as $str ) {
			if ( $str['tra'] != null ) {
				$name_cache[ $str['ctx'] ] = &$str['tra'];
			} else {
				$name_cache[ $str['ctx'] ] = &$str['org'];
			}
			$this->untranslated_cache[ $str['ctx'] ] = $str['tra'] == '' ? true : null;
			// use the original cache if some string were registered with 'plugin XXXX' or 'theme XXXX' context
			// This is how they were registered before the 3.2 release of WPML
			if ( $this->use_original_cache ) {
				$warm_cache[ md5( stripcslashes( $str['org'] ) ) ] = stripcslashes( $name_cache[ $str['ctx'] ] );
			}
		}

		$this->original_cache = $warm_cache;
		$this->name_cache     = $name_cache;
	}
	
	/**
	 * @param string          $name
	 * @param string|string[] $context
	 *
	 * @return array
	 */
	protected function truncate_name_and_context( $name, $context) {
		if ( is_array( $context ) ) {
			$domain          = isset ( $context[ 'domain' ] ) ? $context[ 'domain' ] : '';
			$gettext_context = isset ( $context[ 'context' ] ) ? $context[ 'context' ] : '';
		} else {
			$domain = $context;
			$gettext_context = '';
		}
		list( $name, $domain ) = array_map( array(
			$this,
			'truncate_long_string'
		), array( $name, $domain ) );

		return array( $name . $gettext_context, $domain );
	}

	protected function key_by_name_and_context( $name, $context ) {
		if ( is_array( $context ) ) {
			$domain          = isset ( $context['domain'] ) ? $context['domain'] : '';
			$gettext_context = isset ( $context['context'] ) ? $context['context'] : '';
		} else {
			$domain          = $context;
			$gettext_context = '';
		}
		$domain = $this->truncate_long_string( $domain );

		return array(
			$domain,
			$gettext_context,
			md5( $domain . $name . $gettext_context )
		);
	}

	/**
	 * Truncates a string to the maximum string table column width
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	private function truncate_long_string( $string ) {

		return mb_strlen( $string ) > WPML_STRING_TABLE_NAME_CONTEXT_LENGTH
			? substr( $string, 0,
				WPML_STRING_TABLE_NAME_CONTEXT_LENGTH )
			: $string;
	}

	/**
	 * @param string       $name
	 * @param string|array $context
	 *
	 * @return string|bool|false
	 */
	private function get_original_value( $name, $context ) {

		static $domains_loaded = array();

		list( $domain, $gettext_context, $key ) = $this->key_by_name_and_context( $name, $context );
		if ( ! isset( $this->name_cache[ $key ] ) ) {
			if ( ! in_array( $domain, $domains_loaded ) ) {
				// preload all strings in this context
				$query   = $this->wpdb->prepare(
					"SELECT value, name FROM {$this->wpdb->prefix}icl_strings WHERE context = %s",
					$domain
				);
				$results = $this->wpdb->get_results( $query );
				foreach ( $results as $string ) {
					$string_key = md5( $domain . $string->name . $gettext_context );
					if ( ! isset( $this->name_cache[ $string_key ] ) ) {
						$this->name_cache[ $string_key ] = $string->value;
					}
				}
				$domains_loaded[] = $domain;
			}

			if ( ! isset( $this->name_cache[ $key ] ) ) {
				$this->name_cache[ $key ] = false;
			}
		}

		return $this->name_cache[ $key ];
	}

	private function get_string_from_cache( $name, $context ) {
		list( $name, $context ) = $this->truncate_name_and_context( $name, $context );
		$key = md5( $context . $name );
		$res = isset( $this->name_cache[ $key ] ) ? $this->name_cache[ $key ] : false;

		return $res;
	}

	/**
	 * Checks if the site uses strings registered by a version older than WPML 3.2 and caches the result
	 *
	 * @return bool
	 */
	private function use_original_cache() {
		$string_settings = $this->sitepress->get_setting( 'st', array() );
		if ( ! isset( $string_settings['use_original_cache'] ) ) {
			// See if any strings have been registered with 'plugin XXXX' or 'theme XXXX' context
			// This is how they were registered before the 3.2 release of WPML
			// We only need to do this once and then save the result
			$query = "
						SELECT COUNT(*)
						FROM {$this->wpdb->prefix}icl_strings
						WHERE context LIKE 'plugin %' OR context LIKE 'theme %' ";
			$found = $this->wpdb->get_var( $query );

			$string_settings['use_original_cache'] = $found > 0 ? true : false;
			$this->sitepress->set_setting( 'st', $string_settings, true );
		}

		return (bool) $string_settings['use_original_cache'];
	}
}