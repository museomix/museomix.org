<?php

class FacetWP_Facet_Hierarchy
{

    function __construct() {
        $this->label = __( 'Hierarchy', 'fwp' );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $from_clause = $wpdb->prefix . 'facetwp_index f';
        $where_clause = $params['where_clause'];

        $output = '';
        $selected_values = (array) $params['selected_values'];

        // Orderby
        $orderby = 'counter DESC, f.facet_display_value ASC';
        if ( 'display_value' == $facet['orderby'] ) {
            $orderby = 'f.facet_display_value ASC';
        }
        elseif ( 'raw_value' == $facet['orderby'] ) {
            $orderby = 'f.facet_value ASC';
        }

        // Visible results
        $num_visible = ctype_digit( $facet['count'] ) ? $facet['count'] : 10;

        $max_depth = 0;
        $facet_parent_id = 0;

        // Determine the parent_id and depth
        if ( ! empty( $selected_values[0] ) ) {

            $value = $selected_values[0];
            $taxonomy = str_replace( 'tax/', '', $facet['source'] );

            // Associate array of term IDs with term information
            $depths = FWP()->helper->get_term_depths( $taxonomy );

            // Lookup the term ID from its slug
            $sql = "
            SELECT t.term_id
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id AND tt.taxonomy = %s
            WHERE t.slug = %s
            LIMIT 1";
            $facet_parent_id = (int) $wpdb->get_var( $wpdb->prepare( $sql, $taxonomy, $value ) );

            $max_depth = (int) $depths[ $facet_parent_id ]['depth'];
            $last_parent_id = $facet_parent_id;

            $prev_links = array();
            for ( $i = 0; $i <= $max_depth; $i++ ) {
                $prev_links[] = array(
                    'value' => $depths[ $last_parent_id ]['slug'],
                    'label' => $depths[ $last_parent_id ]['name'],
                );
                $last_parent_id = (int) $depths[ $last_parent_id ]['parent_id'];
            }

            $prev_links[] = array(
                'value' => '',
                'label' => __( 'Any', 'fwp' ),
            );

            // Reverse the navigation
            $prev_links = array_reverse( $prev_links );
            $num_links = count( $prev_links );

            foreach ( $prev_links as $counter => $prev_link ) {
                if ( $counter == ( $num_links - 1 ) ) {
                    $active = ' checked';
                }
                else {
                    $active = '';
                    $prev_link['label'] = '&#8249; ' . $prev_link['label'];
                }
                if ( 0 < $counter ) {
                    $output .= '<div class="facetwp-depth">';
                }
                $output .= '<div class="facetwp-link' . $active . '" data-value="' . $prev_link['value'] . '">' . $prev_link['label'] . '</div>';
            }
        }

        // Update the WHERE clause
        $where_clause .= " AND parent_id = '$facet_parent_id'";

        $orderby = apply_filters( 'facetwp_facet_orderby', $orderby, $facet );
        $from_clause = apply_filters( 'facetwp_facet_from', $from_clause, $facet );
        $where_clause = apply_filters( 'facetwp_facet_where', $where_clause, $facet );

        $sql = "
        SELECT f.facet_value, f.facet_display_value, COUNT(*) AS counter
        FROM $from_clause
        WHERE f.facet_name = '{$facet['name']}' $where_clause
        GROUP BY f.facet_value
        ORDER BY $orderby";

        $results = $wpdb->get_results( $sql );

        $key = 0;

        if ( ! empty( $prev_links ) ) {
            $output .= '<div class="facetwp-depth">';
        }

        if ( ! empty( $results ) ) {
            foreach ( $results as $key => $result ) {
                if ( $key == (int) $num_visible ) {
                    $output .= '<div class="facetwp-overflow facetwp-hidden">';
                }
                $output .= '<div class="facetwp-link" data-value="' . $result->facet_value . '">';
                $output .= $result->facet_display_value . ' <span class="facetwp-counter">(' . $result->counter . ')</span>';
                $output .= '</div>';
            }
        }

        if ( $num_visible <= $key ) {
            $output .= '</div>';
            $output .= '<a class="facetwp-toggle">' . __( 'See more', 'fwp' ) . '</a>';
            $output .= '<a class="facetwp-toggle facetwp-hidden">' . __( 'See less', 'fwp' ) . '</a>';
        }

        for ( $i = 0; $i <= $max_depth; $i++ ) {
            $output .= '</div>';
        }

        if ( ! empty( $prev_links ) ) {
            $output .= '</div>';
        }

        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $selected_values = implode( "','", $selected_values );

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' AND facet_value IN ('$selected_values')";
        return $wpdb->get_col( $sql );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/hierarchy', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-orderby').val(obj.orderby);
        $this.find('.facet-count').val(obj.count);
    });

    wp.hooks.addFilter('facetwp/save/hierarchy', function($this, obj) {
        obj['source'] = $this.find('.facet-source').val();
        obj['orderby'] = $this.find('.facet-orderby').val();
        obj['count'] = $this.find('.facet-count').val();
        return obj;
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output admin settings HTML
     */
    function settings_html() {
?>
        <tr>
            <td><?php _e( 'Sort by', 'fwp' ); ?>:</td>
            <td>
                <select class="facet-orderby">
                    <option value="count"><?php _e( 'Highest Count', 'fwp' ); ?></option>
                    <option value="display_value"><?php _e( 'Display Value', 'fwp' ); ?></option>
                    <option value="raw_value"><?php _e( 'Raw Value', 'fwp' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e( 'Count', 'fwp' ); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'The maximum number of facet choices to show', 'fwp' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="facet-count" value="10" /></td>
        </tr>
<?php
    }
}
