<?php
/**
 * WPML_Element_Translation Class
 *
 * @package wpml-core
 * @abstract
 *
 */

abstract class WPML_Element_Translation {
	/** @var  String $element_join */
	protected $element_join;
	/** @var String[] $element_langs */
	protected $element_langs = array();
	/** @var Int[] $element_trids */
	protected $element_trids = array();
	/** @var String[] $element_source_langs */
	protected $element_source_langs = array();
	/** @var Array[] $translations */
	protected $translations = array();
	/** @var Array[] $trid_groups */
	protected $trid_groups = array();

	public function __construct() {
		$this->element_join = $this->get_element_join();
	}

	protected abstract function get_element_join();

	public abstract function is_translated_type( $element_type );

	/**
	 * Clears the cached translations.
	 */
	public function reload() {
		$this->element_trids        = array();
		$this->element_langs        = array();
		$this->element_source_langs = array();
		$this->translations         = array();
		$this->trid_groups          = array();
	}

	public function get_element_trid( $element_id ) {

		return $this->maybe_populate_cache ( $element_id )
			? $this->element_trids[ $element_id ] : null;
	}

	public function element_id_in( $element_id, $lang ) {

		return $this->maybe_populate_cache ( $element_id ) && isset( $this->translations[ $element_id ][ $lang ] )
			? $this->translations[ $element_id ][ $lang ] : null;
	}

	public function get_original_element( $element_id, $root = false ) {
		$source_lang = $this->maybe_populate_cache ( $element_id )
			? $this->element_source_langs[ $element_id ] : null;
		$res         = $source_lang === null ? $element_id : null;
		$res         = $res === null && !$root ? $this->translations[ $element_id ][ $source_lang ] : $res;
		if ( $res === null && $root ) {
			foreach ( $this->translations[ $element_id ] as &$trans_id ) {
				if ( !$this->element_source_langs[ $trans_id ] ) {
					$res = $trans_id;
					break;
				}
			}
		}

		return $res;
	}

	public function get_element_id( $lang, $trid ) {
		$this->maybe_populate_cache ( false, $trid );

		return isset( $this->trid_groups [ $trid ][ $lang ] ) ? $this->trid_groups [ $trid ][ $lang ] : null;
	}

	public function get_element_lang_code( $element_id ) {

		return $this->maybe_populate_cache ( $element_id )
			? $this->element_langs[ $element_id ] : null;
	}

	public function get_source_lang_code( $element_id ) {

		return $this->maybe_populate_cache ( $element_id )
			? $this->element_source_langs[ $element_id ] : null;
	}

	public function get_element_translations( $element_id, $trid = false, $actual_translations_only = false ) {
		$valid_element = $this->maybe_populate_cache ( $element_id, $trid );

		if ( $element_id ) {
			$res = $valid_element
				? ( $actual_translations_only
					? $this->filter_for_actual_trans ( $element_id ) : $this->translations[ $element_id ] ) : array();
		} elseif ( $trid ) {
			$res = isset( $this->trid_groups[ $trid ] ) ? $this->trid_groups[ $trid ] : array();
		}

		return isset( $res ) ? $res : array();
	}

	public function prefetch_ids( $element_ids ) {
		$element_ids = (array) $element_ids;
		$element_ids = array_diff ( $element_ids, array_keys ( $this->element_trids ) );
		if ( (bool) $element_ids === false ) {
			return;
		}

		global $wpdb;

		$trid_snippet = " tridt.element_id IN (". wpml_prepare_in($element_ids, '%d'). ")";
		$sql          = $this->build_sql($trid_snippet, $wpdb->prefix);
		$elements     = $wpdb->get_results ( $sql, ARRAY_A );
		
		$this->group_and_populate_cache( $elements);
	}

	private function build_sql(&$trid_snippet, &$prefix){

		return "SELECT t.element_id, t.language_code, t.source_language_code, t.trid
				    {$this->element_join}
				    JOIN {$prefix}icl_translations tridt
				      ON tridt.element_type = t.element_type
				      AND tridt.trid = t.trid
				    WHERE {$trid_snippet}";
	}

	private function maybe_populate_cache($element_id, $trid = false){
		if ( !$element_id && !$trid ) {
			return false;
		}

		if ( !$element_id && isset( $this->trid_groups [ $trid ] ) ) {
			return true;
		}
		
		if ( !$element_id || !isset( $this->translations[ $element_id ] ) ) {
			global $wpdb;
			if ( !$element_id ) {
				$trid_snippet = $wpdb->prepare ( " tridt.trid = %d ", $trid );
			} else {
				// preload 1000 items to reduce the number of SQL queries
				// This works on the assumption that the ids tend to be grouped near each other.
				$start        = (intval((intval( $element_id ) / 1000)) * 1000);
				$end          = (intval((intval( $element_id ) / 1000)) + 1) * 1000;
				$trid_snippet = $wpdb->prepare ( " tridt.element_id BETWEEN %d AND %d", $start, $end + 1);
			}
			$sql = $this->build_sql($trid_snippet, $wpdb->prefix);

			$elements = $wpdb->get_results ( $sql, ARRAY_A );
			
			$this->group_and_populate_cache($elements);
		}

		return isset( $this->translations[ $element_id ] );
	}

	private function group_and_populate_cache( $elements ) {
		$trids = array();
		foreach($elements as $element){
			$trid = $element['trid'];
			$trids[$trid] = isset($trids[$trid]) ? $trids[$trid] : array();
			$trids[$trid][] = $element;
		}
		foreach($trids as $trid_group){
			$this->populate_cache($trid_group);
		}
	}

	private function populate_cache( $elements ) {
		$element_ids = array();
		foreach ( $elements as $element ) {
			$trans_id                                = $element[ 'element_id' ];
			$trans_lang_code                         = $element[ 'language_code' ];
			$element_ids[ $trans_lang_code ]         = $trans_id;
			$this->element_trids[ $trans_id ]        = $element[ 'trid' ];
			$this->element_langs[ $trans_id ]        = $trans_lang_code;
			$this->element_source_langs[ $trans_id ] = $element[ 'source_language_code' ];
		}
		foreach ( $element_ids as $translation_id ) {
			$trid                                  = $this->element_trids[ $translation_id ];
			$this->trid_groups[ $trid ]            = $element_ids;
			$this->translations[ $translation_id ] = &$this->trid_groups[ $trid ];
		}
	}

	private function filter_for_actual_trans( $element_id ) {
		$res = $this->translations[ $element_id ];
		foreach ( $res as $lang => $element ) {
			if ( $this->element_source_langs[ $element ] !== $this->element_langs[ $element_id ] ) {
				unset( $res[ $lang ] );
			}
		}

		return $res;
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return string[] all language codes the post can be translated into
	 */
	public function get_allowed_target_langs( $post ) {
		global $sitepress;

		$active_languages = $sitepress->get_active_languages ();
		$can_translate    = array_keys ( $active_languages );
		$can_translate    = array_diff (
			$can_translate,
			array( $this->get_element_lang_code ( $post->ID ) )
		);

		return apply_filters ( 'wpml_allowed_target_langs', $can_translate, $post->ID, 'post' );
	}
}