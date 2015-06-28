<?php
/**
 * CPAC_Column_Comment_Date
 *
 * @since 2.0
 */
class CPAC_Column_Comment_Date extends CPAC_Column {

	/**
	 * @see CPAC_Column::init()
	 * @since 2.2.1
	 */
	public function init() {

		parent::init();

		// Properties
		$this->properties['type']	 = 'column-date';
		$this->properties['label']	 = __( 'Date', 'cpac' );
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0
	 */
	public function get_value( $id ) {

		$date = $this->get_raw_value( $id );

		$value = sprintf( __( 'Submitted on <a href="%1$s">%2$s at %3$s</a>' ),
			esc_url( get_comment_link( $id ) ),
			$this->get_date( $date ),
			$this->get_time( $date )
		);

		return "<div class='submitted-on'>{$value}</div>";
	}

	/**
	 * @since 2.4.2
	 */
	public function get_raw_value( $id ) {
		$comment = get_comment( $id );
		return $comment->comment_date;
	}
}