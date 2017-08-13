<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class AC_Column_Media_Width extends AC_Column_Media_Meta {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-width' );
		$this->set_label( __( 'Width', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$value = $this->get_raw_value( $id );

		return $value ? $value . 'px' : $this->get_empty_char();
	}

	public function get_raw_value( $id ) {
		$value = parent::get_raw_value( $id );

		return ! empty( $value['width'] ) ? $value['width'] : false;
	}

}
