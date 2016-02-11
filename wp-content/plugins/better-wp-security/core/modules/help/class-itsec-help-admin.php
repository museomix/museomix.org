<?php

class ITSEC_Help_Admin {

	function run() {

		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 */
	public function add_admin_meta_boxes() {

		add_meta_box(
			'itsec_help_info',
			__( 'Help', 'better-wp-security' ),
			array( $this, 'add_help_intro' ),
			'security_page_toplevel_page_itsec_help',
			'normal',
			'core'
		);

	}

	/**
	 * Build and echo the away mode description
	 *
	 * @return void
	 */
	public function add_help_intro() {

		echo '<p>' . __( 'Website security is a complicated subject, but we have experts that can help.', 'better-wp-security' ) . '</p>';

		echo '<p><strong>' . __( 'Community Support from WordPress.org', 'better-wp-security' ) . '</strong><br />';
		echo __( 'Since you are using the free version of iThemes Security from WordPress.org, you can get free support from the WordPress community.', 'better-wp-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://wordpress.org/support/plugin/better-wp-security" target="_blank">' . __( 'Get Free Support', 'better-wp-security' ) . '</a></p>';
		echo '<hr>';

		echo '<p><strong>' . __( 'Support & Pro Features with iThemes Security Pro', 'better-wp-security' ) . '</strong><br />';
		echo __( 'Get added peace of mind with professional support from our expert team and pro features to take your site security to the next level with iThemes Security Pro.', 'better-wp-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://www.ithemes.com/security" target="_blank">' . __( 'Get iThemes Security Pro', 'better-wp-security' ) . '</a></p>';
		echo '<hr>';

		echo '<p><strong>' . __( 'Hack Repair', 'better-wp-security' ) . '</strong><br />';
		echo __( 'Has your site been hacked? Contact Sucuri, our recommended hack repair partner, to get things back in order.', 'better-wp-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="https://ithemes.com/sucuri" target="_blank">' . __( 'Get hack repair', 'better-wp-security' ) . '</a></p>';

	}

}
