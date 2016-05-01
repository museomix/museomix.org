<?php
/**
 * @package wpml-core
 * @subpackage post-edit-screen
 */

/** @var $this SitePress */
/** @global $post WP_post */
/** @global $iclTranslationManagement TranslationManagement */
/** @global WPML_Post_Translation $wpml_post_translations */

global $wpdb, $wp_post_types, $iclTranslationManagement, $wpml_post_translations;

$this->noscript_notice();

$status_display = new WPML_Post_Status_Display( $this->get_active_languages() );
$allowed_target_langs       = $wpml_post_translations->get_allowed_target_langs( $post );
$icl_can_translate_post     = ! empty( $allowed_target_langs );

$active_languages = $this->get_active_languages();
$default_language = $this->get_default_language();
$current_language = $this->get_current_language();
if ( $post->ID && $post->post_status != 'auto-draft' ) {
	$res  = $this->get_element_language_details( $post->ID, 'post_' . $post->post_type );
	$trid = @intval( $res->trid );
	if ( $trid ) {
		$element_lang_code = $res->language_code;
	} else {
		$translation_id    = $this->set_element_language_details( $post->ID, 'post_' . $post->post_type, null, $current_language );
		$trid_sql          = "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE translation_id = %d";
		$trid_prepared     = $wpdb->prepare( $trid_sql, array( $translation_id ) );
		$trid              = $wpdb->get_var( $trid_prepared );
		$element_lang_code = $current_language;
	}
} else {
	$trid              = isset( $_GET[ 'trid' ] ) ? intval( $_GET[ 'trid' ] ) : false;
	$element_lang_code = isset( $_GET[ 'lang' ] ) ? strip_tags( $_GET[ 'lang' ] ) : $current_language;
}

$translations = array();
if ( $trid ) {
	$translations = $this->get_element_translations( $trid, 'post_' . $post->post_type );
}

$selected_language = $element_lang_code ? $element_lang_code : $current_language;

if ( isset( $_GET[ 'lang' ] ) ) {
	$_selected_language = strip_tags( $_GET[ 'lang' ] );
} else {
	$_selected_language = $selected_language;
}

/**
 * @var $untranslated array
 */
$untranslated = array();
if ( $_selected_language != $default_language ) {
	$untranslated = $this->get_posts_without_translations( $_selected_language, $default_language, 'post_' . $post->post_type );
}

/**
 * @var $source_language bool|string
 */
$source_language = isset( $_GET[ 'source_lang' ] ) ? filter_input ( INPUT_GET, 'source_lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : false;

$is_original = false;
if ( !$source_language ) {
	if ( isset( $translations[ $selected_language ] ) ) {
		$selected_content_translation      = $translations[ $selected_language ];
		$is_original = $selected_content_translation->original;
		if(!$is_original) {
			$selected_content_language_details = $this->get_element_language_details( $selected_content_translation->element_id, 'post_' . $post->post_type );
			if ( isset( $selected_content_language_details ) && isset( $selected_content_language_details->source_language_code ) ) {
				$source_language = $selected_content_language_details->source_language_code;
			}
		}
	}
}
//globalize some variables to make them available through hooks
global $icl_meta_box_globals;
$icl_meta_box_globals = array(
	'active_languages'  => $active_languages,
	'translations'      => $translations,
	'selected_language' => $selected_language
);

$icl_lang_duplicate_of = get_post_meta($post->ID, '_icl_lang_duplicate_of', true);

$post_type_label = wpml_mb_strtolower( $wp_post_types[ $post->post_type ]->labels->singular_name != "" ? $wp_post_types[ $post->post_type ]->labels->singular_name : $wp_post_types[ $post->post_type ]->labels->name );

if($icl_lang_duplicate_of): ?>
	<div class="icl_cyan_box"><?php
		printf(__('This document is a duplicate of %s and it is maintained by WPML.', 'sitepress'),
		       '<a href="'.get_edit_post_link($icl_lang_duplicate_of).'">' .
		       get_the_title($icl_lang_duplicate_of) . '</a>');
		?>
		<p><input id="icl_translate_independent" class="button-secondary" type="button" value="<?php _e('Translate independently', 'sitepress') ?>" /></p>
		<?php wp_nonce_field('reset_duplication_nonce', '_icl_nonce_rd') ?>
		<i><?php printf(__('WPML will no longer synchronize this %s with the original content.', 'sitepress'), $post->post_type); ?></i>
	</div>

	<span style="display:none"> <?php /* Hide everything else; */ ?>
<?php endif; ?>

	<div id="icl_document_language_dropdown" class="icl_box_paragraph">
		<p>
			<label for="icl_post_language"><strong><?php printf(__('Language of this %s', 'sitepress'), $post_type_label ); ?></strong></label>
		</p>

		<?php
		$icl_post_language_disabled = disabled( false, $icl_can_translate_post, false );
		?>
		<select name="icl_post_language" id="icl_post_language" <?php echo $icl_post_language_disabled; ?>>
			<?php
			foreach ( $active_languages as $lang ) {
				if ( ( isset( $lang[ 'code' ] )
				       && $lang[ 'code' ] != $selected_language
				       && ! in_array( $lang[ 'code' ], $allowed_target_langs ) )
				     || ( isset( $translations[ $lang[ 'code' ] ]->element_id )
				          && $translations[ $lang[ 'code' ] ]->element_id != $post->ID )
				) {
					continue;
				}
				$icl_post_language_selected = selected( true,
				                                        icl_is_selected_post_language( $lang[ 'code' ],
				                                                                       $selected_language ),
				                                        false );
				?>
				<option value="<?php echo $lang[ 'code' ] ?>" <?php echo $icl_post_language_selected; ?>>
					<?php echo $lang[ 'display_name' ] ?>
				</option>
			<?php
			}
			?>
		</select>
		<input type="hidden" name="icl_trid" value="<?php echo $trid ?>" />
	</div>
<?php
if (isset($translations) && count($translations) == 1 && count(SitePress::get_orphan_translations($trid, $post->post_type, $this->get_current_language())) > 0){

	$language_name = $this->get_display_language_name( $selected_language, $this->get_default_language() );
	?>
	<div id="icl_document_connect_translations_dropdown" class="icl_box_paragraph">
		<p>
			<a class="js-set-post-as-source" href="#">
				<?php _e( 'Connect with translations', 'sitepress' ); ?>
			</a>
		</p>
		<input type="hidden" id="icl_connect_translations_post_id" name="icl_connect_translations_post_id" value="<?php echo $post->ID; ?>"/>
		<input type="hidden" id="icl_connect_translations_trid" name="icl_connect_translations_trid" value="<?php echo $trid; ?>"/>
		<input type="hidden" id="icl_connect_translations_post_type" name="icl_connect_translations_post_type" value="<?php echo $post->post_type; ?>"/>
		<input type="hidden" id="icl_connect_translations_language" name="icl_connect_translations_language" value="<?php echo $this->get_current_language(); ?>"/>
		<?php wp_nonce_field( 'get_orphan_posts_nonce', '_icl_nonce_get_orphan_posts' ); ?>
	</div>

	<div class="hidden">
		<div id="connect_translations_dialog" 	title="<?php _e( 'Choose a post to assign','sitepress' ); ?>"
		     data-set_as_source-text="<?php echo esc_attr( sprintf(__("Make %s the original language for this %s",'sitepress'), $language_name, $post->post_type));?>"
		     data-alert-text="<?php echo esc_attr(__("Please make sure to save your post, if you've made any change, before proceeding with this action!",'sitepress'));?>"
		     data-cancel-label="<?php echo esc_attr(__( 'Cancel','sitepress' )); ?>"
		     data-ok-label="<?php echo esc_attr(__( 'Ok','sitepress' )); ?>"
			>
				<p class="js-ajax-loader ajax-loader">
					<?php _e('Loading') ?>&hellip; <span class="spinner"></span>
				</p>
				<div class="posts-found js-posts-found">
					<p id="post-label">
						<?php _e( 'Type a post title', 'sitepress' ); ?>:
					</p>
					<input id="post_search" type="text">
				</div>
				<p class="js-no-posts-found no-posts-found"><?php _e('No posts found','sitepress') ?></p>
				<input type="hidden" id="assign_to_trid">
		</div>
		<div id="connect_translations_dialog_confirm" 	title="<?php echo esc_attr(__( 'Connect this post?','sitepress' )); ?>"
		     data-cancel-label="<?php echo esc_attr(__( 'Cancel','sitepress' )); ?>"
		     data-assign-label="<?php echo esc_attr(__( 'Assign','sitepress' )); ?>"
			>
				<p>
					<span class="ui-icon ui-icon-alert"></span>
					<?php _e( 'You are about to connect the current post with these following posts','sitepress' ); ?>:
				</p>
				<div id="connect_translations_dialog_confirm_list">
					<p class="js-ajax-loader ajax-loader">
						<?php _e('Loading') ?>&hellip; <span class="spinner"></span>
					</p>
				</div>
				<?php wp_nonce_field( 'get_posts_from_trid_nonce', '_icl_nonce_get_posts_from_trid' ); ?>
				<?php wp_nonce_field( 'connect_translations_nonce', '_icl_nonce_connect_translations' ); ?>
		</div>
	</div>

<?php
}
?>
	<div id="translation_of_wrap">
		<?php
		if (!$is_original && ( $selected_language != $source_language || ( isset( $_GET[ 'lang' ] ) && $_GET[ 'lang' ] != $source_language ) ) && 'all' != $this->get_current_language() ) {
			$disabled = ( ( empty( $_GET[ 'action' ] ) || $_GET[ 'action' ] != 'edit' ) && $trid ) ? ' disabled="disabled"' : false;
			?>

			<div id="icl_translation_of_panel" class="icl_box_paragraph">
				<?php echo __( 'This is a translation of', 'sitepress' ); ?>&nbsp;
				<select name="icl_translation_of" id="icl_translation_of"<?php echo $disabled; ?>>
					<?php
					if (!$is_original || !$source_language || $source_language == $selected_language ) {
						if ( $trid ) {
							if(!$source_language) {
								$source_language = $default_language;
							}
							?>
							<option value="none"><?php echo __( '--None--', 'sitepress' ); ?></option>
							<?php
							//get source
							$source_element_id = $wpdb->get_var( $wpdb->prepare("SELECT element_id
                                                                                 FROM {$wpdb->prefix}icl_translations
                                                                                 WHERE trid = %d
                                                                                  AND language_code = %s",
                                                                                 $trid, $source_language ));
							if ( !$source_element_id ) {
								// select the first id found for this trid
								$source_element_id = $wpdb->get_var(
                                    $wpdb->prepare(" SELECT element_id
                                                     FROM {$wpdb->prefix}icl_translations
                                                     WHERE trid=%d",
                                                     $trid ) );
							}
							if ( $source_element_id && $source_element_id != $post->ID ) {
								$src_language_title = $wpdb->get_var( $wpdb->prepare("SELECT post_title
                                                                                      FROM {$wpdb->prefix}posts
                                                                                      WHERE ID = %d",
                                                                                      $source_element_id ) );
							}
							if ( isset( $src_language_title ) && !isset( $_GET[ 'icl_ajx' ] ) ) {
								?>
								<option value="<?php echo $source_element_id ?>" selected="selected"><?php echo esc_html( $src_language_title ); ?>&nbsp;</option>
							<?php
							}
						} else {
							?>
							<option value="none" selected="selected"><?php echo __( '--None--', 'sitepress' ); ?></option>
						<?php
						}
						if (icl_is_language_active($selected_language)) {
							foreach ( $untranslated as $translation_of_id => $translation_of_title ) {
								?>
								<option value="<?php echo $translation_of_id ?>"><?php echo esc_html( $translation_of_title ); ?>&nbsp;</option>
							<?php
							}
						}
					} else {
						if ( $trid ) {

							// add the source language
                            $source_element_id = $wpdb->get_var( $wpdb->prepare("SELECT element_id
                                                                                 FROM {$wpdb->prefix}icl_translations
                                                                                 WHERE trid = %d
                                                                                  AND language_code = %s",
                                                                                $trid, $source_language ));
							if ( $source_element_id ) {
                                $src_language_title = $wpdb->get_var( $wpdb->prepare("SELECT post_title
                                                                                      FROM {$wpdb->prefix}posts
                                                                                      WHERE ID = %d",
                                                                                     $source_element_id ) );
							}
							if ( isset( $src_language_title ) ) {
								?>
								<option value="<?php echo $source_element_id; ?>" selected="selected"><?php echo esc_html( $src_language_title ); ?></option>
							<?php
							}
						} else {
							?>
							<option value="none" selected="selected"><?php echo __( '--None--', 'sitepress' ); ?></option>
						<?php
						}
					}
					?>
				</select>
				<?php //Add hidden value when the dropdown is hidden ?>
				<?php
				if ( $disabled && !empty($source_element_id) ) {
					?>
					<input type="hidden" name="icl_translation_of" id="icl_translation_of_hidden" value="<?php echo $source_element_id; ?>">
				<?php
				}
				?>
			</div>
		<?php
		}
		?>
	</div><!--//translation_of_wrap--><?php // don't delete this html comment ?>

	<br clear="all" />

<?php if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'edit' && $trid ): ?>

	<?php do_action('icl_post_languages_options_before', $post->ID);?>

	<div id="icl_translate_options">
		<?php
		// count number of translated and un-translated pages.
		$translations_found = 0;
		$untranslated_found = 0;
		foreach($active_languages as $lang) {
			if($selected_language==$lang['code']) continue;
			if(isset($translations[$lang['code']]->element_id)) {
				$translations_found += 1;
			} else {
				$untranslated_found += 1;
			}
		}
		?>
		<?php if ( $untranslated_found ): ?>
		<p style="clear:both;"><b><?php _e('Translate this Document', 'sitepress'); ?></b></p>
		<table width="100%" id="icl_untranslated_table" class="icl_translations_table">
			<tr>
				<th>&nbsp;</th>
				<th align="right"><?php _e('Translate', 'sitepress') ?></th>
				<th align="right" width="10" style="padding-left:8px;"><?php _e('Duplicate', 'sitepress') ?></th>
			</tr>
			<?php $oddev = 1; ?>
			<?php foreach($active_languages as $lang): if($selected_language==$lang['code']) continue; ?>
				<tr <?php if($oddev < 0): ?>class="icl_odd_row"<?php endif; ?>>
					<?php if(!isset($translations[$lang['code']]->element_id)):?>
						<?php $oddev = $oddev*-1; ?>
						<td style="padding-left: 4px;">
							<?php echo $lang['display_name'] ?>
						</td>
						<td align="right">
							<?php echo $status_display->get_status_html (
								$post->ID,
								$lang[ 'code' ]
							); ?>
						</td>
						<td align="right">
							<?php
							// do not allow creating duplicates for posts that are being translated
							$ddisabled = '';
							$dtitle = esc_attr__('create duplicate', 'sitepress');
							if(defined('WPML_TM_VERSION')){
								$translation_id = $wpdb->get_var($wpdb->prepare("
                                SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND language_code=%s"
									, $trid, $lang['code']));
								if($translation_id){
									$translation_status = $wpdb->get_var($wpdb->prepare("
                                    SELECT status FROM {$wpdb->prefix}icl_translation_status WHERE translation_id=%d"
										, $translation_id));
									if(!is_null($translation_status) && $translation_status < ICL_TM_COMPLETE){
										$ddisabled = ' disabled="disabled"';
										$dtitle    = esc_attr__("Can't create a duplicate. A translation is in progress.", 'sitepress');
									}
								}
							}
							?>
							<input<?php echo $ddisabled?> type="checkbox" name="icl_dupes[]" value="<?php echo $lang['code'] ?>" title="<?php echo $dtitle ?>" />
						</td>

					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="3" align="right">
					<input id="icl_make_duplicates" type="button" class="button-secondary" value="<?php echo esc_attr('Duplicate', 'sitepress') ?>" disabled="disabled" style="display:none;" />
					<?php wp_nonce_field('make_duplicates_nonce', '_icl_nonce_mdup'); ?>
				</td>
			</tr>
		</table>
		<?php endif; ?>
		<?php if($translations_found > 0): ?>
			<?php if(!empty($iclTranslationManagement)){ $dupes = $iclTranslationManagement->get_duplicates($post->ID); } ?>
			<div class="icl_box_paragraph">

				<b><?php _e('Translations', 'sitepress') ?></b>
				(<a class="icl_toggle_show_translations" href="#" <?php if(empty($this->settings['show_translations_flag'])):?>style="display:none;"<?php endif;?>><?php _e('hide','sitepress')?></a><a class="icl_toggle_show_translations" href="#" <?php if(!empty($this->settings['show_translations_flag'])):?>style="display:none;"<?php endif;?>><?php _e('show','sitepress')?></a>)
				<?php wp_nonce_field('toggle_show_translations_nonce', '_icl_nonce_tst') ?>
				<table width="100%" class="icl_translations_table" id="icl_translations_table" <?php if(empty($this->settings['show_translations_flag'])):?>style="display:none;"<?php endif;?>>
					<?php $oddev = 1; ?>
					<?php foreach($active_languages as $lang): if($selected_language==$lang['code']) continue; ?>
						<tr <?php if($oddev < 0): ?>class="icl_odd_row"<?php endif; ?>>
							<?php if(isset($translations[$lang['code']]->element_id)):?>
								<?php $oddev = $oddev*-1; ?>
								<td style="padding-left: 4px;">
									<?php echo $lang['display_name'] ?>
									<?php if(isset($dupes[$lang['code']])) echo ' (' . __('duplicate', 'sitepress') . ')'; ?>
								</td>
								<td align="right" >
									<?php echo $status_display->get_status_html (
										$post->ID,
										$lang[ 'code' ]
									); ?>
								</td>

							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</table>

			</div>

		<?php endif; ?>



	</div>
<?php endif; ?>

<?php do_action('icl_post_languages_options_after') ?>

<?php if(get_post_meta($post->ID, '_icl_lang_duplicate_of', true)): ?>
	</span> <?php /* Hide everything else; */ ?>
<?php endif;