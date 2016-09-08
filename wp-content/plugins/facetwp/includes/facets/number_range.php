<?php

class FacetWP_Facet_Number_Range
{

    function __construct() {
        $this->label = __( 'Number Range', 'fwp' );

        add_filter( 'facetwp_index_row', array( $this, 'index_row' ), 5, 2 );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $value = $params['selected_values'];
        $value = empty( $value ) ? array( '', '', ) : $value;
        $output .= '<label>' . __( 'Min', 'fwp' ) . '</label>';
        $output .= '<input type="text" class="facetwp-number facetwp-number-min" value="' . $value[0] . '" />';
        $output .= '<label>' . __( 'Max', 'fwp' ) . '</label>';
        $output .= '<input type="text" class="facetwp-number facetwp-number-max" value="' . $value[1] . '" />';
        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $values = $params['selected_values'];
        $where = '';

        $start = ( '' == $values[0] ) ? false : $values[0];
        $end = ( '' == $values[1] ) ? false : $values[1];

        $is_dual = ! empty( $facet['source_other'] ) && false !== $start && false !== $end;
        $is_intersect = FWP()->helper->facet_setting_is( $facet, 'compare_type', 'intersect' );

        /**
         * Intersect compare
         * @link http://stackoverflow.com/a/325964
         */
        if ( $is_dual && $is_intersect ) {
            $where .= " AND (facet_value + 0) <= '$end'";
            $where .= " AND (facet_display_value + 0) >= '$start'";
        }
        else {
            if ( false !== $start ) {
                $where .= " AND (facet_value + 0) >= '$start'";
            }
            if ( false !== $end ) {
                $where .= " AND (facet_display_value + 0) <= '$end'";
            }
        }

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' $where";
        return $wpdb->get_col( $sql );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/number_range', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-source-other').val(obj.source_other);
        $this.find('.facet-compare-type').val(obj.compare_type);
    });

    wp.hooks.addFilter('facetwp/save/number_range', function($this, obj) {
        obj['source'] = $this.find('.facet-source').val();
        obj['source_other'] = $this.find('.facet-source-other').val();
        obj['compare_type'] = $this.find('.facet-compare-type').val();
        return obj;
    });

    wp.hooks.addAction('facetwp/change/number_range', function($this) {
        $this.closest('.facetwp-row').find('.facet-source-other').trigger('change');
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/refresh/number_range', function($this, facet_name) {
        var min = $this.find('.facetwp-number-min').val() || '';
        var max = $this.find('.facetwp-number-max').val() || '';
        FWP.facets[facet_name] = ('' != min || '' != max) ? [min, max] : [];
    });

    wp.hooks.addFilter('facetwp/selections/number_range', function(output, params) {
        return params.selected_values[0] + ' - ' + params.selected_values[1];
    });

    wp.hooks.addAction('facetwp/ready', function() {
        $(document).on('blur', '.facetwp-number-min, .facetwp-number-max', function() {
            FWP.autoload();
        });
    });
})(jQuery);
</script>
<?php
    }


    /**
     * (Admin) Output settings HTML
     */
    function settings_html() {
        $sources = FWP()->helper->get_data_sources();
?>
        <tr>
            <td>
                <?php _e('Other data source', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'Use a separate value for the upper limit?', 'fwp' ); ?></div>
                </div>
            </td>
            <td>
                <select class="facet-source-other">
                    <option value=""><?php _e( 'None', 'fwp' ); ?></option>
                    <?php foreach ( $sources as $group ) : ?>
                    <optgroup label="<?php echo $group['label']; ?>">
                        <?php foreach ( $group['choices'] as $val => $label ) : ?>
                        <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php _e('Compare type', 'fwp'); ?>:</td>
            <td>
                <select class="facet-compare-type">
                    <option value=""><?php _e( 'Basic', 'fwp' ); ?></option>
                    <option value="intersect"><?php _e( 'Intersect', 'fwp' ); ?></option>
                </select>
            </td>
        </tr>
<?php
    }


    /**
     * Index the 2nd data source
     * @since 2.1.1
     */
    function index_row( $params, $class ) {
        if ( $class->is_overridden ) {
            return $params;
        }

        $facet = FWP()->helper->get_facet_by_name( $params['facet_name'] );

        if ( 'number_range' == $facet['type'] && ! empty( $facet['source_other'] ) ) {
            $other_params = $params;
            $other_params['facet_source'] = $facet['source_other'];
            $rows = $class->get_row_data( $other_params );
            $params['facet_display_value'] = $rows[0]['facet_display_value'];
        }

        return $params;
    }
}
