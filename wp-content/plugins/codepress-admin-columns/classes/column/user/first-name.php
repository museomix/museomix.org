<?php

/**
 * CPAC_Column_User_First_Name
 *
 * @since 2.0
 */
class CPAC_Column_User_First_Name extends CPAC_Column {

	/**
	 * @see CPAC_Column::init()
	 * @since 2.2.1
	 */
	public function init() {
		parent::init();

		$this->properties['type'] = 'column-first_name';
		$this->properties['label'] = __( 'First name', 'codepress-admin-columns' );
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0
	 */
	public function get_value( $user_id ) {
		return $this->get_raw_value( $user_id );
	}

	/**
	 * @see CPAC_Column::get_raw_value()
	 * @since 2.0.3
	 */
	public function get_raw_value( $user_id ) {
		$userdata = get_userdata( $user_id );

		return $userdata->first_name;
	}
}