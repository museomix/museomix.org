<?php

class WPML_TP_Polling_Box {

	/**
	 * Renders the html for the TP polling pickup box
	 *
	 * @return string
	 */
	public function render() {
		ob_start();
		?>
		<div id="icl_tm_pickup_wrap">
			<div class="icl_cyan_box">
				<div id="icl_tm_pickup_wrap_errors" class="icl_tm_pickup_wrap"
				     style="display:none"><p></p></div>
				<div id="icl_tm_pickup_wrap_completed"
				     class="icl_tm_pickup_wrap" style="display:none"><p></p>
				</div>
				<div id="icl_tm_pickup_wrap_cancelled"
				     class="icl_tm_pickup_wrap" style="display:none"><p></p>
				</div>
				<div id="icl_tm_pickup_wrap_error_submitting"
				     class="icl_tm_pickup_wrap" style="display:none"><p></p>
				</div>
				<p id="icl_pickup_nof_jobs"></p>
				<p><input type="button" class="button-secondary"
				          data-reloading-text="<?php _e( 'Reloading:',
					          'wpml-translation-management' ) ?>" value=""
				          id="icl_tm_get_translations"/></p>
				<p id="icl_pickup_last_pickup"></p>
			</div>
			<div id="tp_polling_job" style="display:none"></div>
		</div>
		<br clear="all"/>
		<?php
		wp_nonce_field( 'icl_pickup_translations_nonce',
			'_icl_nonce_pickup_t' );
		wp_nonce_field( 'icl_populate_translations_pickup_box_nonce',
			'_icl_nonce_populate_t' );
		wp_enqueue_script( 'wpml-tp-polling-setup' );

		return ob_get_clean();
	}
}