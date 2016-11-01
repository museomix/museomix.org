<?php

/**
 * CPAC_Column_User_Post_Count
 *
 * @since 2.0
 */
class CPAC_Column_User_Post_Count extends CPAC_Column {

	/**
	 * @see CPAC_Column::init()
	 * @since 2.2.1
	 */
	public function init() {
		parent::init();

		// Properties
		$this->properties['type'] = 'column-user_postcount';
		$this->properties['label'] = __( 'Post Count', 'codepress-admin-columns' );
		$this->properties['is_cloneable'] = true;

		// Options
		$this->options['post_type'] = '';
	}

	/**
	 * Get count
	 *
	 * @since 2.0
	 */
	public function get_count( $user_id ) {
		return $this->get_user_postcount( $user_id, $this->get_option( 'post_type' ) );
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0
	 */
	function get_value( $user_id ) {
		$value = '0';

		$count = $this->get_raw_value( $user_id );
		if ( $count > 0 ) {
			$value = "<a href='edit.php?post_type=" . $this->get_option( 'post_type' ) . "&author={$user_id}'>{$count}</a>";
		}

		return $value;
	}

	/**
	 * @see CPAC_Column::get_raw_value()
	 * @since 2.0.3
	 */
	function get_raw_value( $user_id ) {
		return $this->get_count( $user_id );
	}

	/**
	 * Display Settings
	 *
	 * @see CPAC_Column::display_settings()
	 * @since 2.0
	 */
	function display_settings() {
		$ptypes = array();

		if ( post_type_exists( 'post' ) ) {
			$ptypes['post'] = 'post';
		}

		if ( post_type_exists( 'page' ) ) {
			$ptypes['page'] = 'page';
		}

		$ptypes = array_merge( $ptypes, get_post_types( array(
			'_builtin' => false
		) ) );

		// get posttypes and name
		$post_types = array();
		foreach ( $ptypes as $type ) {
			$obj = get_post_type_object( $type );
			$post_types[ $type ] = $obj->labels->name;
		}

		?>
		<tr class="<?php $this->properties->type; ?>">
			<?php $this->label_view( __( 'Post Type', 'codepress-admin-columns' ), '', 'post_type' ); ?>
			<td class="input">
				<select name="<?php $this->attr_name( 'post_type' ); ?>" id="<?php $this->attr_id( 'post_type' ); ?>">
					<?php foreach ( $post_types as $key => $label ) : ?>
						<option value="<?php echo $key; ?>"<?php selected( $key, $this->get_option( 'post_type' ) ); ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php
	}
}