<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

/*------------------------------------------------------------------------------------------------*/
/* !Add frontend nodes ========================================================================== */
/*------------------------------------------------------------------------------------------------*/

add_action( 'sfabt_add_nodes_inside', 'sfabt_add_frontend_nodes_inside', 1 );

function sfabt_add_frontend_nodes_inside( $wp_admin_bar ) {
	// !ITEM LEVEL 1: WP_Query ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$vars = apply_filters( 'sfabt-lightbox-vars', array( 'wp_query' ) );

	if ( ! empty( $vars ) && is_array( $vars ) ) {
		foreach ( $vars as $var ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-main',
				'id'     => 'sfabt-var-' . $var,
				'title'  => '<button class="sf-no-button sfabt-get-var-button"><span>$' . $var . '</span><span class="sfabt-spin" title="' . esc_attr__( 'Loading...', 'sf-adminbar-tools' ) . '"></span></button>',
				'meta'   => array(
					'class'    => 'sfabt-var hide-if-no-js',
					'tabindex' => 0,
					'title'    => sprintf( __( 'Display %s\'s value', 'sf-adminbar-tools' ), '$' . $var ),
				),
			) );
		}
	}
}


add_action( 'sfabt_add_nodes_after', 'sfabt_add_frontend_nodes_after', 1 );

function sfabt_add_frontend_nodes_after( $wp_admin_bar ) {
	global $template;

	// !ITEM LEVEL 2: Template ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	if ( $template ) {

		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-main',
			'id'     => 'sfabt-template',
			'title'  => sfabt_translate_template_path( $template ),
			'meta'   => array(
				'title' => __( 'Template' ),
			),
		) );

		$all_located = sf_cache_data( 'sfabt_template_parts' );
		$all_located = $all_located ? array_values( array_filter( $all_located ) ) : false;

		if ( ! empty( $all_located ) ) {
			$i          = 0;
			$last_templ = false;
			$last_count = 1;

			foreach ( $all_located as $index => $located ) {
				$suffix = '';

				if ( isset( $all_located[ $index + 1 ] ) ) {

					if ( $all_located[ $index + 1 ] === $located ) {
						$last_templ = $located;
						++$last_count;
						continue;
					}
					if ( $located === $last_templ ) {
						$suffix = '<span class="action-indic"><span class="action-count">' . $last_count . '</span></span>';
						++$last_count;
					} else {
						$last_templ = $located;
						$last_count = 1;
					}

				} elseif ( $located === $last_templ ) {
					$suffix = '<span class="action-indic"><span class="action-count">' . $last_count . '</span></span>';
				}

				$templ_tmp = sfabt_translate_template_path( $located );

				$wp_admin_bar->add_node( array(
					'parent' => 'sfabt-template',
					'id'     => 'sfabt-template-part-' . $i,
					'title'  => $templ_tmp . $suffix,
					'meta'   => array(
						'title' => __( 'Template part', 'sf-adminbar-tools' ),
					),
				) );
				++$i;
			}
		}
	}

}


/*------------------------------------------------------------------------------------------------*/
/* !FILTER ALL HOOKS TO GET THE TEMPLATE PARTS (SIC). =========================================== */
/*------------------------------------------------------------------------------------------------*/

add_action( 'all', 'sfabt_store_template_parts', 10, 3 );

function sfabt_store_template_parts( $tag, $slug = null, $name = null ) {
	$other_actions = array(
		'get_header'  => 'header',
		'get_sidebar' => 'sidebar',
		'get_footer'  => 'footer',
	);

	if ( isset( $other_actions[ $tag ] ) ) {
		$name = $slug;
		$slug = $other_actions[ $tag ];
	} elseif ( 0 === strpos( $tag, 'get_template_part_' ) ) {
		// OK
	} elseif ( 'wc_get_template_part' === $tag || 'wc_get_template' === $tag ) {
		// WooCommerce
		sfabt_maybe_cache_template_part( $slug );
		return;
	} else {
		if ( 'comments_template' === $tag ) {
			// This info will be useful later in <code>sfabt_store_comments_template()</code> (maybe).
			sf_cache_data( 'sfabt-comments_template_file', $slug );
		}
		return;
	}

	if ( $slug ) {
		$templates = array();
		$name      = (string) $name;

		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		$located = locate_template( $templates, false, false );

		if ( ! $located && isset( $other_actions[ $tag ] ) ) {
			$located = ABSPATH . WPINC . "/theme-compat/{$slug}.php";
		}

		sfabt_maybe_cache_template_part( $located );
	}
}


add_filter( 'comments_template', 'sfabt_store_comments_template', PHP_INT_MAX );

function sfabt_store_comments_template( $include ) {

	if ( file_exists( $include ) ) {
		$located = $include;
	} else {
		// See <code>sfabt_store_template_parts()</code>.
		$file = sf_cache_data( 'sfabt-comments_template_file' );

		if ( ! $file || 0 !== strpos( $file, STYLESHEETPATH ) ) {
			// Should not happen unless the world falls appart.
			return $include;
		}
		$file = str_replace( STYLESHEETPATH, TEMPLATEPATH, $file );

		if ( file_exists( $file ) ) {
			$located = $file;
		} else {
			$located = ABSPATH . WPINC . '/theme-compat/comments.php';
		}
	}

	sfabt_maybe_cache_template_part( $located );

	return $include;
}


function sfabt_maybe_cache_template_part( $located ) {
	if ( $located ) {
		$all_located   = sf_cache_data( 'sfabt_template_parts' );
		$all_located   = is_array( $all_located ) ? $all_located : array();
		$all_located[] = $located;
		sf_cache_data( 'sfabt_template_parts', $all_located );
	}
}


/*------------------------------------------------------------------------------------------------*/
/* !LIGHTBOX ==================================================================================== */
/*------------------------------------------------------------------------------------------------*/

add_action( 'wp', 'sfabt_lightbox', 0 );

function sfabt_lightbox() {
	if ( ! isset( $_GET['sfabt-var'], $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'sfabt_get-var' ) ) {
		return;
	}

	$vars    = apply_filters( 'sfabt-lightbox-vars', array( 'wp_query' ) );
	$var     = esc_attr( $_GET['sfabt-var'] );
	$sfabt_q = __( 'Variable not found', 'sf-adminbar-tools' );

	if ( 'wp_query' === $var ) {
		$sfabt_q = $GLOBALS['wp_query'];

		if ( ! isset( $sfabt_q->queried_object ) ) {
			$sfabt_q->queried_object = $sfabt_q->get_queried_object();
		}

		if ( ! isset( $sfabt_q->queried_object_id ) ) {
			$sfabt_q->queried_object_id = $sfabt_q->get_queried_object_id();
		}

		if ( ! empty( $sfabt_q->queried_object->post_content ) && ! sfabt_is_content_wrapped( $sfabt_q->queried_object->post_content ) ) {
			$sfabt_q->queried_object->post_content = '<div class="sfabt-post-content">' . $sfabt_q->queried_object->post_content . '</div>';
		}

		if ( ! empty( $sfabt_q->post->post_content ) && ! sfabt_is_content_wrapped( $sfabt_q->post->post_content ) ) {
			$sfabt_q->post->post_content = '<div class="sfabt-post-content">' . $sfabt_q->post->post_content . '</div>';
		}

		if ( ! empty( $sfabt_q->posts ) ) {
			foreach ( $sfabt_q->posts as $i => $sp_abt_p ) {
				if ( ! sfabt_is_content_wrapped( $sp_abt_p->post_content ) ) {
					$sfabt_q->posts[ $i ]->post_content = '<div class="sfabt-post-content">' . $sfabt_q->posts[ $i ]->post_content . '</div>';
				}
			}
		}
	} elseif ( in_array( $var, $vars ) ) {
		$sfabt_q = isset( $GLOBALS[ $var ] ) ? $GLOBALS[ $var ] : $sfabt_q;
		$sfabt_q = apply_filters( 'sfabt-lightbox-var-content', $sfabt_q, $var );
	}

	nocache_headers();
	if ( is_404() ) {
		@header( 'HTTP/1.0 200 OK' );
	}

	print_r( $sfabt_q );
	die();
}


/*------------------------------------------------------------------------------------------------*/
/* !Utilities =================================================================================== */
/*------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'sf_cache_data' ) ) :
	function sf_cache_data( $key ) {
		static $datas = array();

		$func_get_args = func_get_args();

		if ( array_key_exists( 1, $func_get_args ) ) {
			if ( null === $func_get_args[1] ) {
				unset( $datas[ $key ] );
			} else {
				$datas[ $key ] = $func_get_args[1];
			}
		}

		return isset( $datas[ $key ] ) ? $datas[ $key ] : null;
	}
endif;


if ( ! function_exists( 'wp_normalize_path' ) ) :
	function wp_normalize_path( $path ) {
		$path = str_replace( '\\', '/', $path );
		$path = preg_replace( '|/+|','/', $path );
		return $path;
	}
endif;


function sfabt_translate_template_path( $template, $symlinked = false ) {
	global $wp_plugin_paths;
	static $stylesheet_directory;
	static $template_directory;
	static $theme_compat_directory;
	static $plugins_directory;
	static $muplugins_directory;

	if ( ! isset( $stylesheet_directory ) ) {
		$stylesheet_directory   = wp_normalize_path( trailingslashit( get_stylesheet_directory() ) );
		$template_directory     = wp_normalize_path( trailingslashit( get_template_directory() ) );
		$theme_compat_directory = wp_normalize_path( ABSPATH . WPINC . '/theme-compat/' );
		$plugins_directory      = wp_normalize_path( WP_PLUGIN_DIR . '/' );
		$muplugins_directory    = wp_normalize_path( WPMU_PLUGIN_DIR . '/' );
	}

	$template = wp_normalize_path( $template );
	$text     = '%s <span class="sfabt-template-position">(%s)</span>';

	// Child theme
	if ( is_child_theme() ) {
		if ( 0 === strpos( $template, $stylesheet_directory ) ) {
			$text = $symlinked ? str_replace( '(%s)', '(' . _x( '%s, symlinked', 'theme', 'sf-adminbar-tools' ) . ')', $text ) : $text;
			return sprintf( $text, str_replace( $stylesheet_directory, '', $template ), __( 'child theme', 'sf-adminbar-tools' ) );
		}
		if ( 0 === strpos( $template, $template_directory ) ) {
			$text = $symlinked ? str_replace( '(%s)', '(' . _x( '%s, symlinked', 'theme', 'sf-adminbar-tools' ) . ')', $text ) : $text;
			return sprintf( $text, str_replace( $template_directory, '', $template ), __( 'parent theme', 'sf-adminbar-tools' ) );
		}
	}
	// Theme
	elseif ( 0 === strpos( $template, $stylesheet_directory ) ) {
		$text = $symlinked ? str_replace( '(%s)', '(' . _x( '%s, symlinked', 'theme', 'sf-adminbar-tools' ) . ')', $text ) : $text;
		return sprintf( $text, str_replace( $stylesheet_directory, '', $template ), __( 'theme', 'sf-adminbar-tools' ) );
	}

	// Theme compat
	if ( 0 === strpos( $template, $theme_compat_directory ) ) {
		$text = $symlinked ? str_replace( '(%s)', '(' . _x( '%s, symlinked', 'theme', 'sf-adminbar-tools' ) . ')', $text ) : $text;
		return sprintf( $text, str_replace( $theme_compat_directory, '', $template ), __( 'theme compat', 'sf-adminbar-tools' ) );
	}
	// Plugin
	if ( 0 === strpos( $template, $plugins_directory ) ) {
		$text = $symlinked ? str_replace( '(%s)', '(' . _x( '%s, symlinked', 'plugin', 'sf-adminbar-tools' ) . ')', $text ) : $text;
		return sprintf( $text, str_replace( $plugins_directory, '', $template ), __( 'plugin', 'sf-adminbar-tools' ) );
	}
	// MU Plugin
	if ( 0 === strpos( $template, $muplugins_directory )  ) {
		$text = $symlinked ? str_replace( '(%s)', '(' . _x( '%s, symlinked', 'plugin', 'sf-adminbar-tools' ) . ')', $text ) : $text;
		return sprintf( $text, str_replace( $muplugins_directory, '', $template ), __( 'Must-Use plugin', 'sf-adminbar-tools' ) );
	}
	// Symlinked
	if ( ! $symlinked && is_array( $wp_plugin_paths ) && $wp_plugin_paths ) {
		foreach ( $wp_plugin_paths as $local_path => $symlink_path ) {
			if ( 0 === strpos( $template, $symlink_path ) ) {
				return sfabt_translate_template_path( str_replace( $symlink_path, $local_path, $template ), true );
			}
		}
	}

	return str_replace( ABSPATH, '', $template );
}


function sfabt_is_content_wrapped( $content ) {
	if ( empty( $content ) ) {
		return true;
	}
	return 0 === strpos( $content, '<div class="sfabt-post-content">' );
}
