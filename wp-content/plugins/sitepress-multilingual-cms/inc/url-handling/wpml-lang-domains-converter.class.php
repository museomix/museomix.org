<?php

class WPML_Lang_Domains_Converter extends WPML_URL_Converter {

	/** @var string[] $domains */
	protected $domains = array();

	public function __construct( $domains, $default_language, $hidden_languages ) {
		parent::__construct ( $default_language, $hidden_languages );
		$this->domains =  preg_replace ( '#^(http|https)://#', '', $domains );
		add_filter ( 'login_url', array( $this, 'convert_url' ) );
		add_filter ( 'logout_url', array( $this, 'convert_url' ) );
	}

	protected function get_lang_from_url_string( $url ) {
		$url = preg_replace ( '#^(http|https)://#', '', $url );
		foreach ( $this->domains as $code => $domain ) {
			if ( strpos ( $url, $domain ) === 0 ) {
				$lang = $code;
				break;
			}
		}

		return isset( $lang ) ? $lang : null;
	}

	protected function convert_url_string( $url, $lang ) {
		if ( is_admin() && $this->is_url_admin( $url ) ) {
			return $url;
		}

		$domains           = $this->domains;
		$absolute_home_url = $this->get_abs_home();
		$new_url           = $url;
		$is_https          = strpos( $new_url, 'https://' ) === 0;
		if ( $is_https ) {
			$new_url = preg_replace( '#^https://#', 'http://', $new_url );
		}
		$new_url = strpos( $new_url, 'http://' ) === 0 ? $new_url : 'http://' . $new_url;

		$domain  = isset( $domains[ $lang ] ) ? 'http://' . $domains[ $lang ] : $absolute_home_url;
		$lang    = $this->get_language_from_url( $url );
		$old_url = isset( $domains[ $lang ] ) ? 'http://' . $domains[ $lang ] : $absolute_home_url;
		$new_url = str_replace( untrailingslashit( $old_url ), untrailingslashit( $domain ), $new_url );
		if ( $is_https ) {
			$new_url = preg_replace( '#^http://#', 'https://', $new_url );
		}

		return $new_url;
	}
}