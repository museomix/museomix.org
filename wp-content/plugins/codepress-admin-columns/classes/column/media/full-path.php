<?php

/**
 * CPAC_Column_Media_Full_Path
 *
 * @since 2.0
 */
class CPAC_Column_Media_Full_Path extends CPAC_Column {

	/**
	 * @see CPAC_Column::init()
	 * @since 2.2.1
	 */
	public function init() {

		parent::init();

		// Properties
		$this->properties['type'] = 'column-full_path';
		$this->properties['label'] = __( 'Full path', 'codepress-admin-columns' );

		// Options
		$this->options['path_scope'] = 'full';
	}

	private function get_pathscope() {
		return $this->get_option( 'path_scope' );
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0
	 */
	function get_value( $id ) {

		$value = '';

		if ( $file = wp_get_attachment_url( $id ) ) {
			switch ( $this->get_pathscope() ) {
				case 'relative-domain':
					$file = str_replace( 'https://', 'http://', $file );
					$url = str_replace( 'https://', 'http://', home_url( '/' ) );

					if ( strpos( $file, $url ) === 0 ) {
						$file = '/' . substr( $file, strlen( $url ) );
					}

					break;
				case 'relative-uploads':
					$uploaddir = wp_upload_dir();
					$file = str_replace( 'https://', 'http://', $file );
					$url = str_replace( 'https://', 'http://', $uploaddir['baseurl'] );

					if ( strpos( $file, $url ) === 0 ) {
						$file = substr( $file, strlen( $url ) );
					}

					break;
			}

			$value = $file;
		}

		return $value;
	}

	/**
	 *
	 *
	 * @see CPAC_Column::display_settings()
	 * @since 2.3.4
	 */
	public function display_settings() {
		$this->display_field_path_scope();
	}

	/**
	 *
	 *
	 * @since 2.3.4
	 */
	public function display_field_path_scope() {

		$field_key = 'path_scope';
		$label = __( 'Path scope', 'codepress-admin-columns' );
		$description = __( 'Part of the file path to display', 'codepress-admin-columns' );

		?>
		<tr class="column_<?php echo $field_key; ?>">
			<?php $this->label_view( $label, $description, $field_key ); ?>
			<td class="input">
				<label for="<?php $this->attr_id( $field_key ); ?>-full">
					<input type="radio" value="full" name="<?php $this->attr_name( $field_key ); ?>" id="<?php $this->attr_id( $field_key ); ?>-full"<?php checked( $this->get_pathscope(), 'full' ); ?> />
					<?php _e( 'Full path', 'codepress-admin-columns' ); ?>
				</label>
				<br/>
				<label for="<?php $this->attr_id( $field_key ); ?>-relative-domain">
					<input type="radio" value="relative-domain" name="<?php $this->attr_name( $field_key ); ?>" id="<?php $this->attr_id( $field_key ); ?>-relative-domain"<?php checked( $this->get_pathscope(), 'relative-domain' ); ?> />
					<?php _e( 'Relative to domain', 'codepress-admin-columns' ); ?>
				</label>
				<br/>
				<label for="<?php $this->attr_id( $field_key ); ?>-relative-uploads">
					<input type="radio" value="relative-uploads" name="<?php $this->attr_name( $field_key ); ?>" id="<?php $this->attr_id( $field_key ); ?>-relative-uploads"<?php checked( $this->get_pathscope(), 'relative-uploads' ); ?> />
					<?php _e( 'Relative to main uploads folder ', 'codepress-admin-columns' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}

}