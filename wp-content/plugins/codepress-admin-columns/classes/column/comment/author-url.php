<?php
/**
 * CPAC_Column_Comment_Author_Url
 *
 * @since 2.0
 */
class CPAC_Column_Comment_Author_Url extends CPAC_Column {

	/**
	 * @see CPAC_Column::init()
	 * @since 2.2.1
	 */
	public function init() {

		parent::init();

		// Properties
		$this->properties['type']	 = 'column-author_url';
		$this->properties['label']	 = __( 'Author url', 'cpac' );
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0
	 */
	public function get_value( $id ) {
		return $this->get_shorten_url( $this->get_raw_value( $id ) );
	}

	/**
	 * @since 2.4.2
	 */
	public function get_raw_value( $id ) {
		$comment = get_comment( $id );
		return $comment->comment_author_url;
	}
}