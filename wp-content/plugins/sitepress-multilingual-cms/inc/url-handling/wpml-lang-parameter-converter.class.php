<?php

class WPML_Lang_Parameter_Converter extends WPML_URL_Converter {

	public function __construct($default_language, $hidden_languages){
		parent::__construct($default_language, $hidden_languages);
		add_filter( 'request', array( $this, 'request_filter' ) );
	}

	protected function get_lang_from_url_string( $url ) {

		return $this->lang_by_param ( $url, false );
	}

	protected function convert_url_string( $url, $lang_code ) {
		$old_lang_code = $this->get_lang_from_url_string ( $url );
		$lang_code     = (bool) $lang_code === false ? $this->default_language : $lang_code;
		$lang_code     = $lang_code === $this->default_language ? "" : $lang_code;
		if ( (bool) $old_lang_code !== false ) {
			$replace = $lang_code === "" ? "" : '?lang=' . $lang_code;
			$url     = str_replace ( '?lang=' . $old_lang_code, $replace, $url );
			$replace = str_replace ( '?', '&', $replace );
			$url     = str_replace ( '&lang=' . $old_lang_code, $replace, $url );
			$url     = strpos($url, '?') === false ? $url . '?lang=' . $lang_code : $url;
		}

		if ( strpos ( $url, 'lang=' . $lang_code ) === false ) {
			$url .= ( strpos ( $url, '?' ) === false ? '?' : '&' ) . 'lang=' . $lang_code;
		}

		$url = str_replace ( '?lang=&', '?', $url );
		$url = str_replace ( '&lang=&', '&', $url );
		$url = str_replace ( '&lang=/', '', trailingslashit ( $url ) );
		$url = str_replace ( '?lang=/', '', $url );
		$url = str_replace ( '//?', '/?', $url );

		return untrailingslashit ( $url );
	}

	public function request_filter( $request ) {
		if ( !defined( 'WP_ADMIN' ) && isset( $request[ 'lang' ] ) ) {
			// Count the parameters that have settings and remove our 'lang ' setting it's the only one.
			// This is required so that home page detection works for other languages.
			$count = 0;
			foreach ( $request as $data ) {
				if ( $data !== '' ) {
					$count += 1;
				}
			}
			if ( $count == 1 ) {
				unset( $request[ 'lang' ] );
			}
		}

		return $request;
	}
}