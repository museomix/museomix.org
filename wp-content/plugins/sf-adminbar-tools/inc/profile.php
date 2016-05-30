<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

/*------------------------------------------------------------------------------------------------*/
/* !ADD SETTINGS SECTIONS AND FIELDS TO THE USERS PROFILE ======================================= */
/*------------------------------------------------------------------------------------------------*/

add_action( 'load-profile.php', 'sfabt_add_settings_fields' );

function sfabt_add_settings_fields() {
	$user_id = get_current_user_id();

	add_settings_section( 'sfabt', 'SF Adminbar Tools', null, 'adminbar-tools' );

	// Admins list
	add_settings_field( 'admins', __( 'Who\'s gonna use this plugin?', 'sf-adminbar-tools' ), 'sfabt_admins_field', 'adminbar-tools', 'sfabt' );

	// Kill Heartbeat FFS!
	$label = version_compare( $GLOBALS['wp_version'], '3.6' ) >= 0 ? __( 'Disable posts autosave, post lock, authentication check, and everything related to Heartbeat (for you only).', 'sf-adminbar-tools' ) : __( 'Stop posts autosave (for you only).', 'sf-adminbar-tools' );
	$args  = array(
		'label_for'   => 'sf-abt-no-autosave',
		'values'      => array( 1 => $label ),
		'value'       => (int) get_user_meta( $user_id, 'sf-abt-no-autosave', true ),
		'description' => __( 'When you\'re on a post edit screen, WordPress keeps a track of your current status very frequently with ajax calls. This can be boring if you\'re working in your JavaScript console, so you can disable it here.', 'sf-adminbar-tools' ),
	);
	add_settings_field( 'no-autosave', __( 'Console spamming', 'sf-adminbar-tools' ), 'sfabt_checkbox_field', 'adminbar-tools', 'sfabt', $args );

	// Yoast
	if ( defined( 'WPSEO_VERSION' ) ) {
		$args = array(
			'label_for'   => 'sf-abt-no-wpseo',
			'values'      => array( 1 => __( 'Remove WP SEO from the landscape (for you only).', 'sf-adminbar-tools' ) ),
			'value'       => (int) get_user_meta( $user_id, 'sf-abt-no-wpseo', true ),
			'description' => __( 'Will remove all WP SEO columns, meta boxes, and adminbar items. I\'m seriously getting sick of hiding all this stuff everywhere.', 'sf-adminbar-tools' ),
		);
		add_settings_field( 'no-wpseo', __( 'Screen spamming', 'sf-adminbar-tools' ), 'sfabt_checkbox_field', 'adminbar-tools', 'sfabt', $args );
	}

	// Add more fields and sections
	do_action( 'sfabt-settings', $args );
}


/*------------------------------------------------------------------------------------------------*/
/* !PRINT THE FIELDS ============================================================================ */
/*------------------------------------------------------------------------------------------------*/

add_action( 'show_user_profile', 'sfabt_show_user_fields', 11 );

function sfabt_show_user_fields( $profileuser ) {
	do_settings_sections( 'adminbar-tools' );
}


/*------------------------------------------------------------------------------------------------*/
/* !FIELDS ====================================================================================== */
/*------------------------------------------------------------------------------------------------*/

// !Coworkers list field

function sfabt_admins_field( $o ) {
	global $wp_roles;

	// Get roles.
	$roles = array();

	if ( isset( $wp_roles->role_objects[ SFABT_CAP ] ) ) {
		$roles[] = SFABT_CAP;
	} else {
		foreach ( $wp_roles->role_objects as $role => $object ) {
			if ( isset( $object->capabilities[ SFABT_CAP ] ) && $object->capabilities[ SFABT_CAP ] ) {
				$roles[] = $role;
			}
		}
	}

	if ( empty( $roles ) ) {
		echo '<p>' . sprintf( __( 'ERROR: I could not find roles with the capability "%s"', 'sf-adminbar-tools' ), esc_attr( SFABT_CAP ) ) . '</p>';
		return;
	}

	// Display users ordered by role.
	$inputs  = array();
	$user_id = get_current_user_id();
	$options = sfabt_get_options();

	foreach ( $roles as $role ) {
		$users = get_users( array( 'role' => $role ) );

		if ( ! empty( $users ) ) {
			if ( isset( $wp_roles->role_names[ $role ] ) ) {
				$inputs[] = '<span class="description">' . translate_user_role( $wp_roles->role_names[ $role ] ) . '</span>';
			}

			foreach ( $users as $admin ) {
				$inputs[] = '<label><input type="checkbox" name="_sf_abt[coworkers][' . $admin->ID . ']" value="' . $admin->ID . '"' . ( isset( $options['coworkers'][ $admin->ID ] ) ? ' checked="checked"' : '' ) . '/> ' . get_avatar( $admin->ID, 16, '' ) . ' ' . ( $user_id === $admin->ID ? __( 'Me', 'sf-adminbar-tools' ) : $admin->display_name ) . '</label>';
			}
		}
	}

	if ( empty( $inputs ) ) {
		echo '<p>' . sprintf( __( 'ERROR: I could not find users with the capability "%s"', 'sf-adminbar-tools' ), esc_attr( SFABT_CAP ) ) . '</p>';
		return;
	}

	echo '<fieldset><p>' . implode( "</p>\n<p>", $inputs ) . "</p>\n</fieldset>\n";

}


// !A simple checkbox

function sfabt_checkbox_field( $o ) {
	$val = isset( $o['value'] ) ? $o['value'] : $o['options'][ $o['label_for'] ];
	$i   = 0;
	foreach ( $o['values'] as $value => $label ) {
		echo "\t\t\t\t" . '<label><input type="checkbox" id="' . $o['label_for'] . ( $i ? $i : '' ) . '" name="_sf_abt[' . $o['label_for'] . ']" value="' . $value . '"' . checked( $val, $value, false ) . '/> ' . $label . "</label><br/>\n";
		$i++;
	}

	if ( ! empty( $o['description'] ) ) {
		echo "\t\t\t\t<p class='description'>" . $o['description'] . "</p>\n";
	}
}


/*------------------------------------------------------------------------------------------------*/
/* !SAVE THE OPTIONS AND USER METAS ============================================================= */
/*------------------------------------------------------------------------------------------------*/

add_action( 'personal_options_update', 'sfabt_update_user_options' );

function sfabt_update_user_options( $user_id ) {
	if ( ! isset( $_POST['_sf_abt'] ) || ! $user_id || ! defined( 'IS_PROFILE_PAGE' ) || ! IS_PROFILE_PAGE ) {
		return;
	}
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
		return;
	}

	$options = $_POST['_sf_abt'];
	$metas   = apply_filters( 'sfabt_saved_user_metas', array( 'sf-abt-no-autosave', 'sf-abt-no-wpseo' ) );

	// Save metas
	foreach ( $metas as $meta ) {
		if ( ! empty( $options[ $meta ] ) ) {
			update_user_meta( $user_id, $meta, 1 );
		} else {
			delete_user_meta( $user_id, $meta );
		}
		unset( $options[ $meta ] );
	}

	// Save options
	$options = sfabt_update_options( $options );
}
