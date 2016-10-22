<?php

if (get_field('is_light_page')) {
	add_filter( 'body_class', function( $classes ) {
		return array_merge( $classes, array( 'light' ) );
	} );
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" /> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php wp_title( '|', true, 'right' ); bloginfo('name'); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="shortcut icon" href="<?php echo home_url(); ?>/wp-content/uploads/2013/06/favicon.ico" type="image/x-icon">
	<link href="<?php echo get_template_directory_uri(); ?>/biblio/bootstrap/css/bootstrap.css" rel="stylesheet" />
	<!--link href="<?php echo get_template_directory_uri(); ?>/biblio/sst-style.css" rel="stylesheet" /-->
	<link href="<?php echo get_template_directory_uri(); ?>/biblio/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
	<link href="<?php echo get_template_directory_uri(); ?>/style.css" rel="stylesheet" /> 
	<link href="<?php echo get_template_directory_uri(); ?>/style-annexe.css" rel="stylesheet" /> 
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="<?php echo get_template_directory_uri(); ?>/biblio/html5shiv.js"></script>
    <![endif]-->
    
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript">
		$ServUrl = '<?php echo network_site_url( '/' ); ?>';
	</script>
	<script src="<?php echo get_template_directory_uri(); ?>/scripts.js"></script>
	
<?php wp_head(); ?>
</head>

<body data-spy="scroll" data-target=".sidebar-nav" id="<?php echo $post->post_name; ?>" <?php body_class((is_front_page() ? ' home ' : '')); ?>>

	<!--div style="">

		<h1 class="bloc-titre">
		
			<a href="<?php echo home_url(); ?>" class="bouton-titre"><?php bloginfo( 'name' ); ?></a>
			
		</h1>

	</div-->
