<?php

class FacetWP_Facet_Search
{

    function __construct() {
        $this->label = __( 'Search', 'fwp' );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $value = (array) $params['selected_values'];
        $value = empty( $value ) ? '' : stripslashes( $value[0] );
        $placeholder = isset( $params['facet']['placeholder'] ) ? $params['facet']['placeholder'] : __( 'Enter keywords', 'fwp' );
        $placeholder = facetwp_i18n( $placeholder );
        $output .= '<span class="facetwp-search-wrap">';
        $output .= '<i class="facetwp-btn"></i>';
        $output .= '<input type="text" class="facetwp-search" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
        $output .= '</span>';
        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {

        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $selected_values = is_array( $selected_values ) ? $selected_values[0] : $selected_values;

        if ( empty( $selected_values ) ) {
            return 'continue';
        }

        // Default WP search
        $search_args = array(
            's' => $selected_values,
            'posts_per_page' => 200,
            'fields' => 'ids',
        );

        $search_args = apply_filters( 'facetwp_search_query_args', $search_args, $params );

        $query = new WP_Query( $search_args );

        return (array) $query->posts;
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/search', function($this, obj) {
        $this.find('.facet-search-engine').val(obj.search_engine);
        $this.find('.facet-placeholder').val(obj.placeholder);
    });

    wp.hooks.addFilter('facetwp/save/search', function($this, obj) {
        obj['search_engine'] = $this.find('.facet-search-engine').val();
        obj['placeholder'] = $this.find('.facet-placeholder').val();
        return obj;
    });

    wp.hooks.addAction('facetwp/change/search', function($this) {
        $this.closest('.facetwp-row').find('.name-source').hide();
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
    wp.hooks.addAction('facetwp/refresh/search', function($this, facet_name) {
        var val = $this.find('.facetwp-search').val() || '';
        FWP.facets[facet_name] = val;
    });

    /**
    * Event handlers
    */
    $(document).on('facetwp-loaded', function() {
        $('.facetwp-search').trigger('keyup');
    });

    $(document).on('keyup', '.facetwp-facet .facetwp-search', function(e) {
        var $facet = $(this).closest('.facetwp-facet');

        if ('' == $(this).val()) {
            $facet.find('.facetwp-btn').removeClass('reset');
        }
        else {
            $facet.find('.facetwp-btn').addClass('reset');
        }

        if (13 == e.keyCode) {
            FWP.autoload();
        }
    });

    $('.facetwp-type-search').on('click', '.facetwp-btn', function(e) {
        var $this = $(this);
        var $facet = $this.closest('.facetwp-facet');
        var facet_name = $facet.attr('data-name');

        if ($this.hasClass('reset')) {
            $facet.find('.facetwp-search').val('');
            FWP.facets[facet_name] = [];
            FWP.set_hash();
            FWP.fetch_data();
        }
        else {
            $facet.find('.facetwp-search').trigger('keyup');
        }
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output admin settings HTML
     */
    function settings_html() {
        $engines = apply_filters( 'facetwp_facet_search_engines', array() );
?>
        <tr>
            <td><?php _e('Search engine', 'fwp'); ?>:</td>
            <td>
                <select class="facet-search-engine">
                    <option value=""><?php _e( 'WP Default', 'fwp' ); ?></option>
                    <?php foreach ( $engines as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Placeholder text', 'fwp' ); ?>:</td>
            <td><input type="text" class="facet-placeholder" value="" /></td>
        </tr>
<?php
    }
}
