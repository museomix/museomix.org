<?php

// ********************* FIELD NAMES *************************
function JPURB_field_list()
{
	// *** Field name and label ***
	$fields = array(
		'seed'			=> array(
			'label' 	=> 'Seed',
			'help'		=> 
				'Random seed value.  Change this periodically to keep 
				bots from hacking your math problem.',
			'type'		=> 'text'
			),

		'MathReject'		=> array(
			'label'		=> 'Failed Math Response',
			'help'		=>
				'Error message, when user fails the math problem.',
			'type'		=> 'textarea'
			),

		'ACLReject'		=> array(
			'label'		=> 'Rejected IP or E-mail',
			'help'		=>
				'Error message, when user\'s IP or e-mail is blocked.  
				Keep this generic, so the criminal won\'t know why 
				they are rejected.',
			'type'		=> 'textarea'
			),

		'MathProblemFieldName'	=> array(
			'label'		=> 'Form field name for math problem',
			'help'		=>
				'This is the registration form\'s field name.  
				Change periodically to keep the criminals guessing.',
			'type'		=> 'text'
			)
	);
	
	return $fields;
}

// ************** DEFAULT FIELD VALUES **************
function JPURB_default_seed()
{
	return(mt_rand(100,999));
}

function JPURB_default_MathReject()
{
	return "You suck at math!";
}

function JPURB_default_ACLReject()
{
	return "There was a technical problem.  Please try again later.";
}

function JPURB_default_MathProblemFieldName()
{
	$math='Ma'.mt_rand(10,99).'th';
	return $math;
}

function JPURB_default_all()
{
	$opt=array(
		'seed'			=> JPURB_default_seed(),
		'MathReject'		=> JPURB_default_MathReject(),
		'ACLReject'		=> JPURB_default_ACLReject(),
		'MathProblemFieldName'	=> JPURB_default_MathProblemFieldName()
	);

	return $opt;
}


// ******************* PAGE FRIENDLY TITLE ************************
function JPURB_page_title()
{
	return 'JP User Registration Blacklist';
}


// ******************* PAGE NAME SLUG ************************
function JPURB_page_name()
{
	return basename( __FILE__, '_Settings.php' ).'-Settings';
}



// ******************* OPTION NAME (STORED IN DATABASE) ************************
function JPURB_option_name()
{
	return basename( __FILE__, '_Settings.php' ).'_options';
}
function JPURB_option_group()
{
	return basename( __FILE__, '_Settings.php' ).'_option_group';
}
function JPURB_section_id()
{
	return basename( __FILE__, '_Settings.php' ).'_section_id';
}



// ************ ADMIN OPTIONS PAGE ****************

class JPUserRegToolsSettingsPage
{

	// *** Holds the values to be used in the fields callbacks ***
	private $options;
	private $fields;

	// *** Class Constructor ***
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	// *** Add Options Page ***
	public function add_plugin_page()
	{
		// This page will be under "Plugins"
		add_plugins_page(
			JPURB_page_title(),		 			// Page Title
			JPURB_page_title().' Settings',				// Menu Text
			'manage_options', 					// Permissions
			JPURB_page_name(),	 				// Page Slug
			array( $this, 'create_admin_page' )			// Callback
		);
	}

	// *** Options page callback ***
	public function create_admin_page()
	{
		// *** STORE FIELD NAMES AND LABELS ***
		$this->fields = JPURB_field_list();
		
		// *** GET OPTIONS FROM DATABASE ***
		$this->options = $this->sanitize( get_option( JPURB_option_name() ) );

		// *** FORM ***
		?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<?php if( isset($_GET['settings-updated']) ) { ?>
		<div id="message" class="updated">
			<p><strong><?php _e('Settings have been saved.') ?></strong></p>
		</div>
		<?php } ?>
		<form method="post" action="options.php">
		<?php

		// *** PRINT OPTIONS GROUP ***
                settings_fields( JPURB_option_group() );   
                do_settings_sections( JPURB_page_name() );

		// *** SUBMIT ***
                submit_button(); 

		// *** CLOSING HTML ***
		?>
		</form></div>
		<?php
	}

	// *** Add Settings to Page **
	public function page_init()
	{

		$fields=JPURB_field_list();

		register_setting(
			JPURB_option_group(),				// Option group
			JPURB_option_name(),				// Option name
			array( $this, 'sanitize' )			// Sanitize
		);

		add_settings_section(
			JPURB_section_id(),				// Section ID
			JPURB_page_title(),				// Title
			array( $this, 'print_section_info' ),		// Callback
			JPURB_page_name()				// Page
		);

		// *** FIELDS ***
		foreach( $fields as $k => $v) {
			add_settings_field(
				$k,					// ID
				$v['label'],				// Title 
				array( $this, 'JPURB_field' ),		// Callback
				JPURB_page_name(),			// Page
				JPURB_section_id(),			// Section ID
				$k					// Callback Arg
			);      
		}
	}


	// *** Sanitize each setting field as needed ***
	public function sanitize( $input )
	{
		// *** Default values ***
		$defaults = JPURB_default_all();

		// *** Return Buffer ***
		$new_input = $defaults;

		if( $input=='' )
			$input=$defaults;

		// *** Loop through values ***
		foreach( $input as $k => $v ) {

			// *** If field has a value ***
			if( isset( $input[$k] ) ) {

				// *** Sanitize text field ***
				$text = sanitize_text_field( $input[$k] );

				// *** Correct blank fields ***
				if ( $text=='' )
					$text=$defaults[$k];

				$new_input[$k]=$text;
			}
		}

		// ****** FIELD-SPECIFIC VALIDATIONS ******

		// *** SEED NUMERIC ***
		// *   Convert to numeric   *
		$new_input['seed'] = absint( $new_input['seed'] );
		// *   Correct zero value   *
		if ( $new_input['seed']==0 )
			$new_input['seed']=$defaults['seed'];

		// *** Return buffer ***
		return $new_input;
	}


	// *** Print the Section text ***
	public function print_section_info()
	{
		print '<H3>Customize Settings:</H3>';
	}


	// *** GENERIC FIELD CALLBACK ***
	public function JPURB_field($arg)
	{
		$inp='<input type="text" id="%s" name="%s[%s]" value="%s" />';

		if( $this->fields[$arg]['type'] == 'textarea' )
			$inp='<textarea rows=3 cols=40 id="%s" name="%s[%s]">%s</textarea>';

		printf(
			$inp,
			$arg,
			JPURB_option_name(),
			$arg,
			isset( $this->options[$arg] ) ? esc_attr( $this->options[$arg]) : ''
		);
		printf( '<BR>%s', $this->fields[$arg]['help'] );
	}
}


// *************** GENERATE SETTINGS LINK ********************
function JPURB_SettingsLink($links) { 
	$settings_link = '<a href="plugins.php?page='.JPURB_page_name().'">Settings</a>';
	
	//array_unshift($links, $settings_link); 
	$links[]=$settings_link;
	
	return $links; 
}



?>