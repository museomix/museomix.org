<?php

class FacetWP_Facet_Date_Range
{

    function __construct() {
        $this->label = __( 'Date Range', 'fwp' );

        add_filter( 'facetwp_index_row', array( $this, 'index_row' ), 5, 2 );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $value = $params['selected_values'];
        $value = empty( $value ) ? array( '', '' ) : $value;
        $fields = empty( $params['facet']['fields'] ) ? 'both' : $params['facet']['fields'];

        if ( 'exact' == $fields ) {
            $output .= '<input type="text" class="facetwp-date facetwp-date-min" value="' . $value[0] . '" placeholder="' . __( 'Date', 'fwp' ) . '" />';
        }
        if ( 'both' == $fields || 'start_date' == $fields ) {
            $output .= '<input type="text" class="facetwp-date facetwp-date-min" value="' . $value[0] . '" placeholder="' . __( 'Start Date', 'fwp' ) . '" />';
        }
        if ( 'both' == $fields || 'end_date' == $fields ) {
            $output .= '<input type="text" class="facetwp-date facetwp-date-max" value="' . $value[1] . '" placeholder="' . __( 'End Date', 'fwp' ) . '" />';
        }
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

        $start = empty( $values[0] ) ? false : $values[0];
        $end = empty( $values[1] ) ? false : $values[1];

        $is_dual = ! empty( $facet['source_other'] );
        $is_intersect = FWP()->helper->facet_is( $facet, 'compare_type', 'intersect' );

        /**
         * Intersect compare
         * @link http://stackoverflow.com/a/325964
         */
        if ( $is_dual && $is_intersect ) {
            $start = ( false !== $start ) ? $start : '0000-00-00';
            $end = ( false !== $end ) ? $end : '3000-12-31';

            $where .= " AND (LEFT(facet_value, 10) <= '$end')";
            $where .= " AND (LEFT(facet_display_value, 10) >= '$start')";
        }
        elseif ( 'exact' == $facet['fields'] ) {
            if ( $start ) {
                $where .= " AND LEFT(facet_value, 10) = '$start'";
            }
        }
        else {
            if ( $start ) {
                $where .= " AND LEFT(facet_value, 10) >= '$start'";
            }
            if ( $end ) {
                $where .= " AND LEFT(facet_display_value, 10) <= '$end'";
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
    wp.hooks.addAction('facetwp/load/date_range', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-source-other').val(obj.source_other);
        $this.find('.facet-compare-type').val(obj.compare_type);
        $this.find('.facet-date-fields').val(obj.fields);
        $this.find('.facet-format').val(obj.format);
    });

    wp.hooks.addFilter('facetwp/save/date_range', function($this, obj) {
        obj['source'] = $this.find('.facet-source').val();
        obj['source_other'] = $this.find('.facet-source-other').val();
        obj['compare_type'] = $this.find('.facet-compare-type').val();
        obj['fields'] = $this.find('.facet-date-fields').val();
        obj['format'] = $this.find('.facet-format').val();
        return obj;
    });

    wp.hooks.addAction('facetwp/change/date_range', function($this) {
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
        $i18n = array(
            'months' => array(
                __( 'January', 'fwp' ),
                __( 'February', 'fwp' ),
                __( 'March', 'fwp' ),
                __( 'April', 'fwp' ),
                __( 'May', 'fwp' ),
                __( 'June', 'fwp' ),
                __( 'July', 'fwp' ),
                __( 'August', 'fwp' ),
                __( 'September', 'fwp' ),
                __( 'October', 'fwp' ),
                __( 'November', 'fwp' ),
                __( 'December', 'fwp' ),
            ),
        );

        FWP()->display->json['datepicker'] = $i18n;
        FWP()->display->assets['flatpickr.css'] = FACETWP_URL . '/assets/js/flatpickr/flatpickr.min.css';
        FWP()->display->assets['flatpickr.js'] = FACETWP_URL . '/assets/js/flatpickr/flatpickr.min.js';
    }


    /**
     * Output admin settings HTML
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
        <tr>
            <td><?php _e('Fields to show', 'fwp'); ?>:</td>
            <td>
                <select class="facet-date-fields">
                    <option value="both"><?php _e( 'Start + End Dates', 'fwp' ); ?></option>
                    <option value="exact"><?php _e( 'Exact Date', 'fwp' ); ?></option>
                    <option value="start_date"><?php _e( 'Start Date', 'fwp' ); ?></option>
                    <option value="end_date"><?php _e( 'End Date', 'fwp' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Display format', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content">See available <a href="https://chmln.github.io/flatpickr/#dateformat" target="_blank">format characters</a></div>
                </div>
            </td>
            <td><input type="text" class="facet-format" value="" placeholder="Y-m-d" /></td>
        </tr>
<?php
    }


    /**
     * (Front-end) Attach settings to the AJAX response
     */
    function settings_js( $params ) {
        $format = empty( $params['facet']['format'] ) ? 'Y-m-d' : $params['facet']['format'];
        return array( 'format' => $format );
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

        if ( 'date_range' == $facet['type'] && ! empty( $facet['source_other'] ) ) {
            $other_params = $params;
            $other_params['facet_source'] = $facet['source_other'];
            $rows = $class->get_row_data( $other_params );
            $params['facet_display_value'] = $rows[0]['facet_display_value'];
        }

        return $params;
    }
}
