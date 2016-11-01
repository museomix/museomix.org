<?php

/**
 * CPAC_Column_User_Description
 *
 * @since 2.0
 */
class CPAC_Column_User_Description extends CPAC_Column {

	/**
	 * @see CPAC_Column::init()
	 * @since 2.2.1
	 */
	public function init() {
		parent::init();

		$this->properties['type'] = 'column-user_description';
		$this->properties['label'] = __( 'Description', 'codepress-admin-columns' );

		$this->options['excerpt_length'] = 30;
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0
	 */
	function get_value( $user_id ) {
		return $this->get_raw_value( $user_id );
	}

	/**
	 * @see CPAC_Column::get_raw_value()
	 * @since 2.0.3
	 */
	function get_raw_value( $user_id ) {
		return $this->get_shortened_string( get_the_author_meta( 'user_description', $user_id ), $this->get_option( 'excerpt_length' ) );
	}

	/**
	 * @see CPAC_Column::display_settings()
	 * @since 2.0
	 */
	function display_settings() {
		$this->display_field_excerpt_length();
	}
}