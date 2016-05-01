<?php

class WPML_Post_Edit_Screen {

	/**
	 * @param Int $lang_negotiation_type the language negotiation type currently used.
	 */
	public function __construct( $lang_negotiation_type ) {
		if ( $lang_negotiation_type == 2 ) {
			add_filter( 'preview_post_link', array( $this, 'preview_post_link_filter' ), 10, 1 );
			add_filter( 'preview_page_link ', array( $this, 'preview_post_link_filter' ), 10, 1 );
		}
		add_action( 'icl_post_languages_options_after', array( $this, 'copy_from_original' ) );
	}

	/**
	 * Filters the preview links on the post edit screen so that they always point to the currently used language
	 * domain. This ensures that the user can actually see the preview, as he might not have the login cookie set for
	 * the actual language domain of the post.
	 *
	 * @param string $link
	 *
	 * @return mixed
	 */
	public function preview_post_link_filter( $link ) {
		/** @var WPML_Post_Translation $wpml_post_translations */
        $original_host = filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_STRING);
		if ( $original_host ) {
			$domain = parse_url( $link, PHP_URL_HOST );
			$link   = str_replace( '//' . $domain . '/', '//' . $original_host . '/', $link );
		}

		return $link;
	}

	function copy_from_original() {
		global $post;

		if ( !post_type_supports( $post->post_type, 'editor' ) ) {
			return;
		}

		global $wpml_post_translations, $sitepress;
		$show             = false;
		$source_lang_name = false;
		$trid             = null;
		$source_lang = filter_input ( INPUT_GET, 'source_lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$source_lang = $source_lang === 'all' ? $sitepress->get_default_language() : $source_lang;
		$lang = filter_input ( INPUT_GET, 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ($source_lang && $trid = filter_input ( INPUT_GET, 'trid', FILTER_SANITIZE_NUMBER_INT ) ) {
			$_lang_details    = $sitepress->get_language_details ( $source_lang );
			$source_lang_name = $_lang_details[ 'display_name' ];
			$show             = true;
		} elseif ( isset( $_GET[ 'post' ] )
		           && $lang !== $sitepress->get_default_language ()
		) {
			$source_lang      = $wpml_post_translations->get_source_lang_code ( $post->ID );
			$_lang_details    = $sitepress->get_language_details ( $source_lang );
			$source_lang_name = $_lang_details[ 'display_name' ];
			$show             = $source_lang && $source_lang !== $lang;
		}

		if ( $show && isset( $source_lang ) ) {
	
			if ( !$trid ) {
				$trid     = $wpml_post_translations->get_element_trid ( $post->ID );
			}
			
			$this->display_copy_from_button ( $source_lang, $source_lang_name, $post, $trid );
			$this->display_set_as_dupl_btn ( $post, $source_lang_name, $wpml_post_translations->get_original_post_ID( $trid ), $lang );
		}
	}

	private function display_copy_from_button( $source_lang, $source_lang_name, $post, $trid ) {
		$disabled = trim ( $post->post_content ) ? ' disabled="disabled"' : '';
		wp_nonce_field ( 'copy_from_original_nonce', '_icl_nonce_cfo_' . $trid );
		echo '<input id="icl_cfo" class="button-secondary" type="button" value="' . sprintf (
				__ ( 'Copy content from %s', 'sitepress' ),
				$source_lang_name
			) . '"
				onclick="icl_copy_from_original(\'' . esc_js ( $source_lang ) . '\', \'' . esc_js (
			     $trid
		     ) . '\')"' . $disabled . ' style="white-space:normal;height:auto;line-height:normal;"/>';
		icl_pop_info (
			__ (
				"This operation copies the content from the original language onto this translation. It's meant for when you want to start with the original content, but keep translating in this language. This button is only enabled when there's no content in the editor.",
				'sitepress'
			),
			'question'
		);
		echo '<br clear="all" />';
	}

	private function display_set_as_dupl_btn( $post, $source_lang_name, $original_post_id, $post_lang ) {
		wp_nonce_field ( 'set_duplication_nonce', '_icl_nonce_sd' ) ?>
		<input id="icl_set_duplicate" type="button" class="button-secondary"
		       value="<?php printf ( __ ( 'Overwrite with %s content.', 'sitepress' ), $source_lang_name ) ?>"
		       style="white-space:normal;height:auto;line-height:normal;"
			   data-wpml_original_post_id="<?php echo $original_post_id; ?>"
			   data-post_lang="<?php echo $post_lang; ?>"/>
		<span style="display: none;"><?php echo esc_js (
				sprintf (
					__ (
						'The current content of this %s will be permanently lost. WPML will copy the %s content and replace the current content.',
						'sitepress'
					),
					$post->post_type,
					$source_lang_name
				)
			); ?></span>
		<?php icl_pop_info (
			__ (
				"This operation will synchronize this translation with the original language. When you edit the original, this translation will update immediately. It's meant when you want the content in this language to always be the same as the content in the original language.",
				'sitepress'
			),
			'question'
		); ?>
		<br clear="all"/>
		<?php
	}
}