<?php

class WPML_TM_Troubleshooting {
	static function init() {
		add_action( 'after_setup_complete_troubleshooting_functions', array( 'WPML_TM_Troubleshooting', 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( 'WPML_TM_Troubleshooting', 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_icl_sync_jobs', array( 'WPML_TM_Troubleshooting', 'icl_sync_jobs' ) );
		add_action( 'wp_ajax_icl_cms_id_fix', array( 'WPML_TM_Troubleshooting', 'icl_cms_id_fix' ) );
		add_action( 'wp_ajax_sync_cancelled', array( 'WPML_TM_Troubleshooting', 'sync_cancelled' ) );
		add_action( 'wp_ajax_sync_cancelled_do_delete',
					array( 'WPML_TM_Troubleshooting', 'sync_cancelled_do_delete' ) );
	}

	static function admin_enqueue_scripts() {
		wp_register_script( 'wpml-tm-troubleshooting', WPML_TM_URL . '/res/js/wpml-tm-troubleshooting.js', array( 'jquery' ), WPML_TM_VERSION );

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == ICL_PLUGIN_FOLDER . '/menu/troubleshooting.php' ) {
				wp_localize_script( 'wpml-tm-troubleshooting', 'tm_troubleshooting_data', self::js_data() );
				wp_enqueue_script( 'wpml-tm-troubleshooting' );
			}
		}
	}

	static function js_data() {
		$data = array(
			'nonce'   => array(
				'icl_sync_jobs'            => wp_create_nonce( 'icl_sync_jobs' ),
				'icl_cms_id_fix'           => wp_create_nonce( 'icl_cms_id_fix' ),
				'sync_cancelled'           => wp_create_nonce( 'sync_cancelled' ),
				'sync_cancelled_do_delete' => wp_create_nonce( 'sync_cancelled_do_delete' ),
			),
			'strings' => array(
				'done' => __( 'Done', 'sitepress' )
			),
		);

		return $data;
	}

	static function icl_sync_jobs() {
		global $sitepress, $wpdb;

		//TODO: handle this with Translation Proxy

		$project  = TranslationProxy::get_current_project();
		$requests = $project->jobs();
		if ( ! empty( $requests ) ) {
			foreach ( $requests as $request ) {

				//Check that it works with strings too

				$cms_id_full = $request->cms_id;

				$job_data        = explode( '_', $cms_id_full );
				$job_data_length = count( $job_data );

				//If we don't have at least 4 items, the cms_id value is malformed
				if ( $job_data_length < 4 ) {
					continue;
				}

				//Get data from the end, to avoid the case of a post type with a '_' on his name
				$post_id         = $job_data[ $job_data_length - 3 ];
				$source_language = $job_data[ $job_data_length - 2 ];
				$target_language = $job_data[ $job_data_length - 1 ];

				$post_type_data = array_slice( $job_data, 0, $job_data_length - 3 );
				$post_type      = implode( '_', $post_type_data );

				$trid = $sitepress->get_element_trid( $post_id, 'post_' . $post_type );
				if ( ! $trid ) {
					continue;
				}
				$translations = $sitepress->get_element_translations( $trid, 'post_' . $post_type );
				if ( ! $translations || ! isset( $translations[ $target_language ] ) ) {
					continue;
				}

				$translation    = $translations[ $target_language ];
				$translation_id = $translation->translation_id;
				if ( ! $translation_id ) {
					continue;
				}

				$tr = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}icl_translations WHERE translation_id=%d", $translation_id ) );
				if ( empty( $tr ) ) {
					$trs = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}icl_translation_status WHERE translation_id=%d", $translation_id ) );
					if ( ! empty( $trs ) ) {
						$translation_package = unserialize( $trs->translation_package );
						$original_id         = $translation_package[ 'contents' ][ 'original_id' ][ 'data' ];
						list( $trid, $element_type ) = $wpdb->get_row( "
                                SELECT trid, element_type
                                FROM {$wpdb->prefix}icl_translations
                                WHERE element_id={$original_id}
                                AND element_type LIKE 'post\\_%'
                            ", ARRAY_N );
						if ( $trid ) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND language_code=%s", array( $trid, $target_language ) ) );
							$recover = array(
								'translation_id'       => $translation_id,
								'element_type'         => $element_type,
								'trid'                 => $trid,
								'language_code'        => $target_language,
								'source_language_code' => $source_language
							);
							$wpdb->insert( $wpdb->prefix . 'icl_translations', $recover );
						}
					}
				}
			}
		}

		// Do a check to see if the icl_translation_status is consistent.
		// There was a problem with the cancel logic leaving it in a status where
		// Translations couldn't be sent.

		global $iclTranslationManagement;

		$res_prepared = "SELECT rid, status, needs_update, md5, translation_package FROM {$wpdb->prefix}icl_translation_status";
		$res          = $wpdb->get_results( $res_prepared );
		foreach ( $res as $row ) {
			if ( $row->status == ICL_TM_NOT_TRANSLATED || $row->needs_update == 1 ) {

				$translation_package = unserialize( $row->translation_package );
				$original_id         = $translation_package[ 'contents' ][ 'original_id' ][ 'data' ];

				$post_md5 = $iclTranslationManagement->post_md5( $original_id );

				if ( $post_md5 == $row->md5 ) {
					// The md5 shouldn't be the same if it's not translated or needs update.
					// Add a dummy md5 and mark it as needs_update.
					$data       = array( 'needs_update' => 1, 'md5' => 'DUMMY_HASH' );
					$data_where = array( 'rid' => $row->rid );
					$wpdb->update( $wpdb->prefix . 'icl_translation_status', $data, $data_where );
				}
			}
		}
		die();
	}

	static function icl_cms_id_fix() {
		echo wp_json_encode( array( 'errors' => 0, 'message' => __( 'Done', 'sitepress' ), 'cont' => 0 ) );
		die();
	}

	static function sync_cancelled() {
		global $wpdb, $sitepress;

		$project  = TranslationProxy::get_current_project();
		$requests = $project->cancelled_jobs();

		if ( $requests === false ) {
			echo wp_json_encode( array( 'errors' => 1, 'message' => 'Failed fetching jobs list from the server.' ) );
			exit;
		}

		$cms_ids = array();
		if ( ! empty( $requests ) ) {
			foreach ( $requests as $request ) {
				$cms_ids[ ] = $request->cms_id;
			}
		}

		// get jobs that are in progress
		$translations_sql      = "
                SELECT t.element_id, t.element_type, t.language_code, t.source_language_code, t.trid,
                    s.rid, s._prevstate, s.translation_id
                FROM {$wpdb->prefix}icl_translation_status s
                JOIN {$wpdb->prefix}icl_translations t
                    ON t.translation_id = s.translation_id
                WHERE s.translation_service=%s
                AND s.status = %d
            ";
		$translations_prepared = $wpdb->prepare( $translations_sql, array( TranslationProxy::get_current_service_id(), ICL_TM_IN_PROGRESS ) );
		$translations          = $wpdb->get_results( $translations_prepared );

		$jobs2delete         = array();
		$translations2cancel = array();
		foreach ( $translations as $t ) {
			$original_id_sql      = "SELECT element_id FROM {$wpdb->prefix}icl_translations
                                     WHERE trid=%d AND source_language_code IS NULL";
			$original_id_prepared = $wpdb->prepare( $original_id_sql, $t->trid );
			$original_id          = $wpdb->get_var( $original_id_prepared );
			$cms_id               = sprintf( '%s_%d_%s_%s', preg_replace( '#^post_#', '', $t->element_type ), $original_id, $t->source_language_code, $t->language_code );
			if ( in_array( $cms_id, $cms_ids ) ) {
				$_lang_details          = $sitepress->get_language_details( $t->source_language_code );
				$lang_from              = $_lang_details[ 'english_name' ];
				$_lang_details          = $sitepress->get_language_details( $t->language_code );
				$lang_to                = $_lang_details[ 'english_name' ];
				$jobs2delete[ ]         = '<a href="' . get_permalink( $original_id ) . '">' . get_the_title( $original_id ) . '</a>' . sprintf( ' - from %s to %s', $lang_from, $lang_to );
				$translations2cancel[ ] = $t;
			}
		}

		$response_message = '';

		if ( $jobs2delete && $translations2cancel ) {
			$response_message .= '<div class="error clear" style="padding-top:5px;font-size:11px;">';
			$response_message .= __( 'About to cancel these jobs:', 'sitepress' );
			$response_message .= '<br />';
			$response_message .= '<ul style="margin-left:10px;">';
			$response_message .= '<li>';
			$response_message .= join( '</li><li>', $jobs2delete );
			$response_message .= '</li>';
			$response_message .= '</ul>';
			$response_message .= '<br />';
			$response_message .= '<a id="icl_ts_cancel_ok" href="#" class="button-secondary">';
			$response_message .= __( 'OK', 'sitepress' );
			$response_message .= '</a>&nbsp;';
			$response_message .= '<a id="icl_ts_cancel_cancel" href="#" class="button-secondary">';
			$response_message .= __( 'Cancel', 'sitepress' );
			$response_message .= '</a>';
			$response_message .= '</div>';

			$response_errors = 0;
			$response_data   = array( 't2c' => serialize( $translations2cancel ) );
		} elseif ( $project->errors ) {
			$response_message = join( '<br/>', $project->errors );
			$response_errors  = count( $project->errors );
			$response_data    = false;
		} else {
			$response_message = __( 'Nothing to cancel.', 'sitepress' );
			$response_errors  = 0;
			$response_data    = false;
		}

		$response = array(
			'errors'  => $response_errors,
			'message' => $response_message,
			'data'    => $response_data
		);

		echo wp_json_encode( $response );
		die();
	}

	static function sync_cancelled_do_delete() {
		global $wpdb;

		$translations = unserialize( stripslashes( $_POST[ 't2c' ] ) );
		if ( is_array( $translations ) ) {
			foreach ( $translations as $t ) {
				$job_id = $wpdb->get_var( $wpdb->prepare( "SELECT job_id FROM {$wpdb->prefix}icl_translate_job WHERE rid=%d AND revision IS NULL", $t->rid ) );
				if ( $job_id ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}icl_translate_job WHERE job_id=%d", $job_id ) );
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}icl_translate WHERE job_id=%d", $job_id ) );
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}icl_translate_job SET revision = NULL WHERE rid=%d ORDER BY job_id DESC LIMIT 1", $t->rid ) );
				}

				if ( ! empty( $t->_prevstate ) ) {
					$_prevstate = unserialize( $t->_prevstate );
					$data       = array(
						'status'              => $_prevstate[ 'status' ],
						'translator_id'       => $_prevstate[ 'translator_id' ],
						'needs_update'        => $_prevstate[ 'needs_update' ],
						'md5'                 => $_prevstate[ 'md5' ],
						'translation_service' => $_prevstate[ 'translation_service' ],
						'translation_package' => $_prevstate[ 'translation_package' ],
						'timestamp'           => $_prevstate[ 'timestamp' ],
						'links_fixed'         => $_prevstate[ 'links_fixed' ]
					);
					$data_where = array( 'translation_id' => $t->translation_id );
					$wpdb->update( $wpdb->prefix . 'icl_translation_status', $data, $data_where );
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}icl_translation_status SET _prevstate = NULL WHERE translation_id=%d", $t->translation_id ) );
				} else {
					$data       = array( 'status' => ICL_TM_NOT_TRANSLATED, 'needs_update' => 0 );
					$data_where = array( 'translation_id' => $t->translation_id );
					$wpdb->update( $wpdb->prefix . 'icl_translation_status', $data, $data_where );
				}
			}
		}

		echo wp_json_encode( array( 'errors' => 0, 'message' => 'OK' ) );
		die();
	}

	static function menu() {
		global $wpdb, $sitepress;

		if ( ! class_exists( 'TranslationManagement' ) ) {
			return;
		}

		$site_id    = $sitepress->get_setting( 'site_id' );
		$access_key = $sitepress->get_setting( 'access_key' );

		?>
		<h4><?php _e( 'Translation Management', 'sitepress' ) ?></h4>
		<?php
		$current_service_name = TranslationProxy::get_current_service_name();
		if ( $site_id && $access_key ) {
			?>
			<p>
				<input id="icl_sync_jobs" type="button" class="button-secondary"
					   value="<?php echo sprintf( __( 'Synchronize translation jobs with %s', 'sitepress' ),
												  $current_service_name ); ?>"/><br/>
				<small
					style="margin-left:10px;"><?php echo sprintf( __( 'Fixes links between translation entries in the database and %s.',
																	  'sitepress' ),
																  $current_service_name ); ?></small>
			</p>
			<p>
				<input id="icl_cms_id_fix" type="button" class="button-secondary"
					   value="<?php _e( 'CMS ID fix', 'sitepress' ) ?>"/>
					<span id="icl_cms_id_fix_prgs" style="display: none;"><?php
						$fixing_count_sql
											   = "
						SELECT COUNT(*)
						FROM {$wpdb->prefix}icl_translations t
						JOIN {$wpdb->prefix}icl_translation_status s
							ON t.translation_id=s.translation_id
						WHERE t.element_type LIKE 'post\\_%'
							AND t.source_language_code IS NOT NULL
							AND s.translation_service=%s
						";
						$fixing_count_prepared = $wpdb->prepare( $fixing_count_sql, array( $current_service_name ) );
						$fixing_count          = $wpdb->get_var( $fixing_count_prepared );
						printf( __( 'fixing %s/%d', 'sitepress' ),
								'<span id="icl_cms_id_fix_prgs_cnt">0</span>',
								$fixing_count ) ?></span><br/>
				<small
					style="margin-left:10px;"><?php _e( "Updates translation in progress with new style identifiers for documents. The new identifiers depend on the document being translated and the languages so it's not possible to get out of sync when translations are being deleted locally.",
														'sitepress' ) ?></small>
			</p>
			<p>
				<input id="icl_sync_cancelled" type="button" class="button-secondary"
					   value="<?php echo sprintf( __( 'Check cancelled jobs on %s', 'sitepress' ),
												  $current_service_name ); ?>"/><br/>
				<small
					style="margin-left:10px;"><?php echo sprintf( __( 'When using the translation pickup mode cancelled jobs on %s need to be synced manually.',
																	  'sitepress' ),
																  $current_service_name ) ?></small>
			</p>
			<div id="icl_sync_cancelled_resp" class="clear"></div>
			<input type="hidden" id="icl_ts_t2c" value=""/>
			<?php
		}
	}

}
