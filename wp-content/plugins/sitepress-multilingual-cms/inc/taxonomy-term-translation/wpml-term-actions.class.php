<?php

/**
 * WPML_Term_Actions Class
 *
 * @package    wpml-core
 * @subpackage taxonomy-term-translation
 *
 */
class WPML_Term_Actions {

	/**
	 * Gets the language under which a term is to be saved from the HTTP request and falls back on existing data in
	 * case the HTTP request does not contain the necessary data.
	 * If no language can be determined for the term to be saved under the default language is used as a fallback.
	 *
	 * @param int    $tt_id Taxonomy Term ID of the saved term
	 * @param string $post_action
	 * @param string $taxonomy
	 * @return null|string
	 */
	private function get_term_lang( $tt_id, $post_action, $taxonomy ) {
		global $sitepress;

		$term_lang = filter_input ( INPUT_POST, 'icl_tax_' . $taxonomy . '_language', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$term_lang = $term_lang ? $term_lang : $this->get_term_lang_ajax ( $taxonomy, $post_action );
		$term_lang = $term_lang ? $term_lang : $this->get_lang_from_post ( $post_action, $tt_id );

		$term_lang = $term_lang ? $term_lang : $sitepress->get_current_language ();
		$term_lang = apply_filters ( 'wpml_create_term_lang', $term_lang );
		$term_lang = $sitepress->is_active_language ( $term_lang ) ? $term_lang
			: $sitepress->get_default_language ();

		return $term_lang;
	}

	/**
	 * If no language could be set from the WPML $_POST variables as well as from the HTTP Referrer, then this function
	 * uses fallbacks to determine the language from the post the the term might be associated to.
	 * A post language determined from $_POST['icl_post_language'] will be used as term language.
	 * Also a check for whether the publishing of the term happens via quickpress is performed in which case the term
	 * is always associated with the default language.
	 * Next a check for the 'inline-save-tax' and the 'editedtag' action is performed. In case the check returns true
	 * the language of the term is not changed from what is saved for it in the database.
	 * If no term language can be determined from the above the $_POST['post_ID'] is checked as a last resort and in
	 * case it contains a valid post_ID the posts language is associated with the term.
	 *
	 * @param string $post_action
	 * @param int    $tt_id
	 *
	 * @return string|null Language code of the term
	 */
	private function get_lang_from_post( $post_action, $tt_id ) {
		/** @var WPML_Term_Translation $wpml_term_translations */
		global $sitepress, $wpml_term_translations, $wpml_post_translations;

		$icl_post_lang = filter_input ( INPUT_POST, 'icl_post_language' );
		$term_lang     = $post_action === 'editpost' && $icl_post_lang ? $icl_post_lang : null;
		$term_lang     = $post_action === 'post-quickpress-publish' ? $sitepress->get_default_language ()
			: $term_lang;
		$term_lang     = !$term_lang && $post_action === 'inline-save-tax' || $post_action === 'editedtag'
			? $wpml_term_translations->get_element_lang_code ( $tt_id ) : $term_lang;
		$term_lang     = !$term_lang && $post_action === 'inline-save'
			? $wpml_post_translations->get_element_lang_code (
				filter_input ( INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT )
			) : $term_lang;

		return $term_lang;
	}

	/**
	 * This function tries to determine the terms language from the HTTP Referer. This is used in case of ajax actions
	 * that save the term.
	 *
	 * @param string $taxonomy
	 * @param string $post_action
	 *
	 * @return null|string
	 */
	private function get_term_lang_ajax( $taxonomy, $post_action ) {
		global $sitepress, $wpml_post_translations;

		if ( filter_input ( INPUT_POST, '_ajax_nonce' ) !== null && $post_action === 'add-' . $taxonomy ) {
			$referrer = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : '';
			parse_str ( (string) parse_url ( $referrer, PHP_URL_QUERY ), $qvars );
			$term_lang = !empty( $qvars[ 'post' ] ) && $sitepress->is_translated_post_type (
				get_post_type ( $qvars[ 'post' ] )
			)
				? $wpml_post_translations->get_element_lang_code ( $qvars[ 'post' ] )
				: ( isset( $qvars[ 'lang' ] ) ? $qvars[ 'lang' ] : null );
		}

		return isset( $term_lang ) ? $term_lang : null;
	}

	private function get_saved_term_trid( $tt_id, $post_action ) {
		/** @var WPML_Term_Translation $wpml_term_translations */
		global $wpml_term_translations;

		if ( $post_action === 'editpost' ) {
			$trid = $wpml_term_translations->get_element_trid ( $tt_id );
		} elseif ( $post_action === 'editedtag' ) {
			$translation_of = filter_input ( INPUT_POST, 'icl_translation_of', FILTER_VALIDATE_INT );
			$translation_of = $translation_of ? $translation_of : filter_input ( INPUT_POST, 'icl_translation_of' );

			$trid = $translation_of === 'none' ? false
				: ( $translation_of
					? $wpml_term_translations->get_element_trid ( $translation_of )
					: $trid = filter_input ( INPUT_POST, 'icl_trid', FILTER_SANITIZE_NUMBER_INT )
				);
		} else {
			$trid = filter_input( INPUT_POST, 'icl_trid', FILTER_SANITIZE_NUMBER_INT );
			$trid = $trid
				? $trid
				: $wpml_term_translations->get_element_trid(
					filter_input( INPUT_POST, 'icl_translation_of', FILTER_VALIDATE_INT )
				);
			$trid = $trid ? $trid : $wpml_term_translations->get_element_trid( $tt_id );
		}

		return $trid;
	}

	/**
	 *
	 * @global SitePress             $sitepress
	 * @global WPML_Term_Translation $wpml_term_translations
	 *
	 * @ignore $cat_id
	 *
	 * @param int                    $tt_id    Taxonomy Term ID of the saved Term
	 * @param string                 $taxonomy Taxonomy of the saved Term
	 *
	 */
	public function save_term_actions( $cat_id, $tt_id, $taxonomy ) {
		/** @var WPML_Term_Translation $wpml_term_translations */
		global $sitepress, $wpml_term_translations;

		if ( !is_taxonomy_translated ( $taxonomy ) ) {
			return;
		};

		$post_action = filter_input ( INPUT_POST, 'action' );
		$term_lang   = $this->get_term_lang ( $tt_id, $post_action, $taxonomy );
		$trid        = $this->get_saved_term_trid ( $tt_id, $post_action );

		$src_language = $wpml_term_translations->get_source_lang_code ( $tt_id );

		$sitepress->set_element_language_details ( $tt_id, 'tax_' . $taxonomy, $trid, $term_lang, $src_language );
	}
}