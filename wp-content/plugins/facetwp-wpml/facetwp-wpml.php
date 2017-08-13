<?php
/*
Plugin Name: FacetWP - WPML
Description: WPML support for FacetWP
Version: 1.2.2
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub URI: facetwp/facetwp-wpml
*/

defined( 'ABSPATH' ) or exit;

class FWP_WPML
{

    function __construct() {
        add_action( 'init' , array( $this, 'init' ) );
    }


    /**
     * Initialize
     */
    function init() {
        if ( defined( 'ICL_SITEPRESS_VERSION' ) && function_exists( 'FWP' ) ) {
            add_action( 'wp_footer', array( $this, 'wp_footer' ), 30 );
            add_filter( 'facetwp_query_args', array( $this, 'facetwp_query_args' ), 10, 2 );
            add_filter( 'facetwp_indexer_query_args', array( $this, 'indexer_query_args' ) );
            add_action( 'facetwp_indexer_post', array( $this, 'set_post_langcode' ) );

            // Require WPML String Translation
            if ( function_exists( 'icl_register_string' ) ) {
                add_action( 'admin_init', array( $this, 'register_strings' ) );
                add_filter( 'facetwp_i18n', array( $this, 'facetwp_i18n' ) );
            }
        }
    }


    /**
     * Put the language into FWP_HTTP
     */
    function wp_footer() {
        $lang = ICL_LANGUAGE_CODE;
        echo "<script>var FWP_HTTP = FWP_HTTP || {}; FWP_HTTP.lang = '$lang';</script>";
    }


    /**
     * Query posts for the current language
     */
    function facetwp_query_args( $args, $class ) {
        if ( isset( $class->http_params['lang'] ) ) {
            $GLOBALS['sitepress']->switch_lang( $class->http_params['lang'] );
        }
        return $args;
    }


    /**
     * Index all languages
     */
    function indexer_query_args( $args ) {
        if ( function_exists( 'is_checkout' ) && is_checkout() ) {
            return $args;
        }

        if ( -1 == $args['posts_per_page'] ) {
            $GLOBALS['sitepress']->switch_lang( 'all' );
        }

        $args['suppress_filters'] = true; // query posts in all languages
        return $args;
    }


    /**
     * Set the indexer language code
     */
    function set_post_langcode( $params ) {
        $post_id = $params['post_id'];
        $language_code = $this->get_post_langcode( $post_id );
        $GLOBALS['sitepress']->switch_lang( $language_code );
    }


    /**
     * Find a post's language code
     */
    function get_post_langcode( $post_id ) {
        global $wpdb;

        $query = $wpdb->prepare( "SELECT language_code FROM {$wpdb->prefix}icl_translations WHERE element_id = %d", $post_id );
        return $wpdb->get_var( $query );
    }


    /**
     * Register dynamic strings
     */
    function register_strings() {
        $facets = FWP()->helper->get_facets();
        $whitelist = array( 'label', 'label_any', 'placeholder' );

        if ( ! empty( $facets ) ) {
            foreach ( $facets as $facet ) {
                foreach ( $whitelist as $k ) {
                    if ( ! empty( $facet[ $k ] ) ) {
                        icl_register_string( 'FacetWP', $facet[ $k ], $facet[ $k ] );
                    }
                }
            }
        }
    }


    /**
     * Handle string translations
     */
    function facetwp_i18n( $string ) {
        $lang = ICL_LANGUAGE_CODE;
        $default = $GLOBALS['sitepress']->get_default_language();

        if ( isset( FWP()->facet->http_params['lang'] ) ) {
            $lang = FWP()->facet->http_params['lang'];
        }

        if ( $lang != $default ) {
            $has_translation = null; // passed by reference
            return icl_translate( 'FacetWP', $string, false, false, $has_translation, $lang );
        }

        return $string;
    }
}


$fwp_wpml = new FWP_WPML();
