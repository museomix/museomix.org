<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );


$this->set_current_section( 'bbq_headers' );
$this->add_section( __( 'Bad Headers', 'secupress' ) );


$main_field_name = $this->get_field_name( 'user-agents-header' );

$this->add_field( array(
	'title'             => __( 'Block Bad User Agents', 'secupress' ),
	'label_for'         => $main_field_name,
	'plugin_activation' => true,
	'type'              => 'checkbox',
	'value'             => (int) secupress_is_submodule_active( 'firewall', 'user-agents-header' ),
	'label'             => __( 'Yes, protect my site from bad user-agents', 'secupress' ),
	'helpers'           => array(
		array(
			'type'        => 'description',
			'description' => __( 'Bots are commonly use their own headers containing some known bad user agent. You can block them to prevent their unwanted visits.', 'secupress' ),
		),
	),
) );


$this->add_field( array(
	'title'        => __( 'User-Agents List', 'secupress' ),
	'description'  => __( 'Automatically block any user agent containing any HTML tag in it or containing more than 255 characters automatically.', 'secupress' ),
	'depends'      => $main_field_name,
	'label_for'    => $this->get_field_name( 'user-agents-list' ),
	'type'         => 'textarea',
	'label'        => __( 'List of User Agents to block', 'secupress' ),
	'helpers'      => array(
		array(
			'type'        => 'description',
			'description' => __( 'Add or remove User Agents you want to be blocked. Separate user agents with commas.', 'secupress' ),
		),
	),
) );


$this->add_field( array(
	'title'             => __( 'Block Bad Request Methods', 'secupress' ),
	'description'       => __( 'The 3 known safe request methods are <code>GET</code>, <code>POST</code> and <code>HEAD</code>.', 'secupress' ),
	'label_for'         => $this->get_field_name( 'request-methods-header' ),
	'plugin_activation' => true,
	'type'              => 'checkbox',
	'value'             => (int) secupress_is_submodule_active( 'firewall', 'request-methods-header' ),
	'label'             => __( 'Yes, protect my site from bad request methods', 'secupress' ),
	'helpers'           => array(
		array(
			'type'        => 'description',
			'description' => __( 'Some other request methods can be used to retrieve information from your site, avoid them!', 'secupress' ),
		),
	),
) );
