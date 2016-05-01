<?php
/**
 * CPAC_Column_Post_Word_Count
 *
 * @since 2.0
 */
class CPAC_Column_Post_Word_Count extends CPAC_Column {

	/**
	 * @see CPAC_Column::init()
	 * @since 2.2.1
	 */
	public function init() {
		parent::init();

		$this->properties['type']	 	= 'column-word_count';
		$this->properties['label']	 	= __( 'Word count', 'codepress-admin-columns' );
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0
	 */
	function get_value( $post_id ) {
		$count = $this->get_raw_value( $post_id );

		return $count ? $count : $this->get_empty_char();
	}

	/**
	 * @see CPAC_Column::get_raw_value()
	 * @since 2.0.3
	 */
	function get_raw_value( $post_id ) {
		return $this->str_count_words( $this->strip_trim( get_post_field( 'post_content', $post_id ) ) );
	}

	/**
	 * @see CPAC_Column::apply_conditional()
	 * @since 2.0
	 */
	function apply_conditional() {
		return post_type_supports( $this->get_post_type(), 'editor' );
	}
}