<?php

class FacetWP_API
{

    function __construct() {
        add_action( 'rest_api_init', array( $this, 'register' ) );
    }


    // PHP < 5.3
    function register() {
        register_rest_route( 'facetwp/v1/', '/fetch', array(
            'methods' => 'POST',
            'callback' => array( $this, 'callback' ),
            'permission_callback' => array( $this, 'permission_callback' )
        ) );
    }


    // PHP < 5.3
    function callback( $request ) {
        $data = $request->get_param( 'data' );
        $params = empty( $data ) ? array() : json_decode( $data, true );
        return $this->process_request( $params );
    }


    // PHP < 5.3
    function permission_callback( $request ) {
        return apply_filters( 'facetwp_api_can_access', false, $request );
    }


    function process_request( $params = array() ) {
        global $wpdb;

        $defaults = array(
            'facets' => array(
                // 'category' => array( 'acf' )
            ),
            'query_args' => array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 10,
                'paged' => 1,
            ),
            'settings' => array(
                'first_load' => true
            )
        );

        $params = array_merge( $defaults, $params );
        $facet_types = FWP()->helper->facet_types;
        $valid_facets = array();
        $facets = array();

        // Set default "posts_per_page" and "paged"
        if ( empty( $params['query_args']['posts_per_page'] ) ) {
            $params['query_args']['posts_per_page'] = 10;
        }

        if ( empty( $params['query_args']['paged'] ) ) {
            $params['query_args']['paged'] = 1;
        }

        // Validate facets and set FWP()->facet->facets
        // The latter is required by FWP()->helper->facet_setting_exists()
        foreach ( $params['facets'] as $facet_name => $facet_value ) {
            $facet = FWP()->helper->get_facet_by_name( $facet_name );
            if ( false !== $facet ) {
                $facet['selected_values'] = (array) $facet_value;
                $valid_facets[ $facet_name ] = $facet;
                FWP()->facet->facets[] = $facet;
            }
        }

        // Get bucket of post IDs
        FWP()->facet->query_args = $params['query_args'];
        $post_ids = FWP()->facet->get_filtered_post_ids();

        // SQL WHERE used by facets
        $where_clause = empty( $post_ids ) ? '' : "AND post_id IN (" . implode( ',', $post_ids ) . ")";

        // Check if empty
        if ( 0 === $post_ids[0] && 1 === count( $post_ids ) ) {
            $post_ids = array();
        }

        // Get valid facets and their values
        foreach ( $valid_facets as $facet_name => $facet ) {
            $args = array(
                'facet' => $facet,
                'where_clause' => $where_clause,
                'selected_values' => $facet['selected_values'],
            );

            $facet_data = array(
                'name'          => $facet['name'],
                'label'         => $facet['label'],
                'type'          => $facet['type'],
                'selected'      => $facet['selected_values'],
            );

            // Load facet choices if available
            if ( method_exists( $facet_types[ $facet['type'] ], 'load_values' ) ) {
                $choices = $facet_types[ $facet['type'] ]->load_values( $args );
                foreach ( $choices as $key => $choice ) {
                    $choices[ $key ] = array(
                        'value'     => $choice['facet_value'],
                        'label'     => $choice['facet_display_value'],
                        'depth'     => (int) $choice['depth'],
                        'count'     => (int) $choice['counter'],
                    );
                }
                $facet_data['choices'] = $choices;
            }

            $facets[ $facet_name ] = $facet_data;
        }

        $page = (int) $params['query_args']['paged'];
        $per_page = (int) $params['query_args']['posts_per_page'];
        $total_rows = count( $post_ids );
        $total_pages = ceil( $total_rows / $per_page );
        $offset = ( $per_page * ( $page - 1 ) );
        $results = array_slice( $post_ids, $offset, $per_page );

        // Generate the output
        $output = array(
            'results' => $results,
            'facets' => $facets,
            'pager' => array(
                'page' => $page,
                'per_page' => $per_page,
                'total_rows' => $total_rows,
                'total_pages' => $total_pages,
            )
        );

        return $output;
    }
}
