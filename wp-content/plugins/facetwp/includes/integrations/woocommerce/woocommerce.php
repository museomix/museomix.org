<?php

class FacetWP_Integration_WooCommerce
{

    function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
        add_filter( 'facetwp_facet_sources', array( $this, 'facet_sources' ) );
        add_filter( 'facetwp_indexer_post_facet', array( $this, 'index_wc_values' ), 10, 2 );
    }


    /**
     * Run WooCommerce handlers on facetwp-refresh
     * @since 2.0.9
     */
    function front_scripts() {
        wp_enqueue_script( 'query-string', FACETWP_URL . '/assets/js/src/query-string.js', array(), FACETWP_VERSION );
        wp_enqueue_script( 'facetwp-woocommerce', FACETWP_URL . '/includes/integrations/woocommerce/woocommerce.js', array( 'jquery' ), FACETWP_VERSION );
    }


    /**
     * Add WooCommerce-specific data sources
     * @since 2.1.4
     */
    function facet_sources( $sources ) {
        $sources['woocommerce'] = array(
            'label' => __( 'WooCommerce', 'fwp' ),
            'choices' => array(
                'woocommerce/stock_status'  => __( 'Stock Status' ),
                'woocommerce/on_sale'       => __( 'On Sale' ),
            )
        );
        return $sources;
    }


    /**
     * Index WooCommerce-specific values
     * @since 2.1.4
     */
    function index_wc_values( $return, $params ) {
        $facet = $params['facet'];
        $defaults = $params['defaults'];

        if ( 0 === strpos( $facet['source'], 'woocommerce' ) ) {
            $product = new WC_Product( $defaults['post_id'] );

            // Stock Status
            if ( 'woocommerce/stock_status' == $facet['source'] ) {
                $in_stock = $product->is_in_stock();
                $defaults['facet_value'] = (int) $in_stock;
                $defaults['facet_display_value'] = $in_stock ? __( 'In Stock', 'fwp' ) : __( 'Out of Stock', 'fwp' );
                FWP()->indexer->index_row( $defaults );
            }

            // On Sale
            elseif ( 'woocommerce/on_sale' == $facet['source'] ) {
                if ( $product->is_on_sale() ) {
                    $defaults['facet_value'] = 1;
                    $defaults['facet_display_value'] = __( 'On Sale', 'fwp' );
                    FWP()->indexer->index_row( $defaults );
                }
            }

            return true;
        }

        return $return;
    }
}


if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    new FacetWP_Integration_WooCommerce();
}
