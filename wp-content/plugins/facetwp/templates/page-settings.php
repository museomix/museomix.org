<?php

global $wpdb;

// Translations
$i18n = array(
    'All post types' => __( 'All post types', 'fwp' ),
    'Indexing complete' => __( 'Indexing complete', 'fwp' ),
    'Indexing' => __( 'Indexing', 'fwp' ),
    'Saving' => __( 'Saving', 'fwp' ),
    'Loading' => __( 'Loading', 'fwp' ),
    'Importing' => __( 'Importing', 'fwp' ),
    'Activating' => __( 'Activating', 'fwp' ),
    'Are you sure?' => __( 'Are you sure?', 'fwp' ),
    'Select some items' => __( 'Select some items', 'fwp' ),
);

// An array of facet type objects
$facet_types = FWP()->helper->facet_types;

// Get taxonomy list
$taxonomies = get_taxonomies( array(), 'object' );

// Get post types & taxonomies for the Query Builder
$builder_taxonomies = array();
foreach ( $taxonomies as $tax ) {
    $builder_taxonomies[ $tax->name ] = $tax->labels->singular_name;
}

$builder_post_types = array();
$post_types = get_post_types( array( 'public' => true ), 'objects' );
foreach ( $post_types as $type ) {
    $builder_post_types[ $type->name ] = $type->labels->name;
}

// Clone facet settings HTML
$facet_clone = array();
foreach ( $facet_types as $name => $class ) {
    $facet_clone[ $name ] = __( 'This facet type has no additional settings.', 'fwp' );
    if ( method_exists( $class, 'settings_html' ) ) {
        ob_start();
        $class->settings_html();
        $facet_clone[ $name ] = ob_get_clean();
    }
}

// Activation status
$message = __( 'Not yet activated', 'fwp' );
$activation = get_option( 'facetwp_activation' );
if ( ! empty( $activation ) ) {
    $activation = json_decode( $activation );
    if ( 'success' == $activation->status ) {
        $message = __( 'License active', 'fwp' );
        $message .= ' (' . __( 'expires', 'fwp' ) . ' ' . date( 'M j, Y', strtotime( $activation->expiration ) ) . ')';
    }
    else {
        $message = $activation->message;
    }
}

// Settings
$settings = FWP()->helper->settings;

// Export feature
$export = array();

foreach ( $settings['facets'] as $facet ) {
    $export['facet-' . $facet['name']] = 'Facet - ' . $facet['label'];
}

foreach ( $settings['templates'] as $template ) {
    $export['template-' . $template['name']] = 'Template - '. $template['label'];
}

// Data sources
$sources = FWP()->helper->get_data_sources();

?>

<script src="<?php echo FACETWP_URL; ?>/assets/js/src/event-manager.js?ver=<?php echo FACETWP_VERSION; ?>"></script>
<script src="<?php echo FACETWP_URL; ?>/assets/js/src/query-builder.js?ver=<?php echo FACETWP_VERSION; ?>"></script>
<script src="<?php echo FACETWP_URL; ?>/assets/js/fSelect/fSelect.js?ver=<?php echo FACETWP_VERSION; ?>"></script>
<?php
foreach ( $facet_types as $class ) {
    $class->admin_scripts();
}
?>
<script src="<?php echo FACETWP_URL; ?>/assets/js/admin.js?ver=<?php echo FACETWP_VERSION; ?>"></script>
<script>
FWP.i18n = <?php echo json_encode( $i18n ); ?>;
FWP.nonce = '<?php echo wp_create_nonce( 'fwp_admin_nonce' ); ?>';
FWP.settings = <?php echo json_encode( $settings ); ?>;
FWP.clone = <?php echo json_encode( $facet_clone ); ?>;
FWP.builder = {
    post_types: <?php echo json_encode( $builder_post_types ); ?>,
    taxonomies: <?php echo json_encode( $builder_taxonomies ); ?>
};
</script>
<link href="<?php echo FACETWP_URL; ?>/assets/css/admin.css?ver=<?php echo FACETWP_VERSION; ?>" rel="stylesheet">
<link href="<?php echo FACETWP_URL; ?>/assets/js/fSelect/fSelect.css?ver=<?php echo FACETWP_VERSION; ?>" rel="stylesheet">

<div class="facetwp-header">
    <span class="facetwp-logo" title="FacetWP">&nbsp;</span>
    <span class="facetwp-header-nav">
        <a class="facetwp-tab" rel="welcome"><?php _e( 'Welcome', 'fwp' ); ?></a>
        <a class="facetwp-tab" rel="facets"><?php _e( 'Facets', 'fwp' ); ?></a>
        <a class="facetwp-tab" rel="templates"><?php _e( 'Templates', 'fwp' ); ?></a>
        <a class="facetwp-tab" rel="settings"><?php _e( 'Settings', 'fwp' ); ?></a>
        <a class="facetwp-tab" rel="support"><?php _e( 'Support', 'fwp' ); ?></a>
    </span>
</div>

<div class="wrap">

    <div class="facetwp-response"></div>
    <div class="facetwp-loading"></div>

    <!-- Welcome tab -->

    <div class="facetwp-region facetwp-region-welcome about-wrap">
        <h1><?php _e( 'Welcome to FacetWP', 'fwp' ); ?> <span class="version"><?php echo FACETWP_VERSION; ?></span></h1>
        <div class="about-text">Thank you for choosing FacetWP. Check out our intro video.</div>
        <a href="https://facetwp.com/documentation/getting-started/" target="_blank">
            <img src="https://i.imgur.com/U4ko9Eh.png" width="575" height="323" />
        </a>
    </div>

    <!-- Facets tab -->

    <div class="facetwp-region facetwp-region-facets">
        <div class="flexbox">
            <div class="left-side">
                <span class="btn-wrap">
                    <a class="button facetwp-add"><?php _e( 'Add New', 'fwp' ); ?></a>
                </span>
                <span class="btn-wrap hidden">
                    <a class="button facetwp-back"><?php _e( 'Back', 'fwp' ); ?></a>
                </span>
            </div>
            <div class="right-side">
                <a class="button facetwp-rebuild"><?php _e( 'Re-index', 'fwp' ); ?></a>
                <a class="button-primary facetwp-save"><?php _e( 'Save Changes', 'fwp' ); ?></a>
            </div>
        </div>

        <div class="facetwp-content-wrap">
            <ul class="facetwp-cards"></ul>
            <div class="facetwp-content"></div>
        </div>
    </div>

    <!-- Templates tab -->

    <div class="facetwp-region facetwp-region-templates">
        <div class="flexbox">
            <div class="left-side">
                <span class="btn-wrap">
                    <a class="button facetwp-add"><?php _e( 'Add New', 'fwp' ); ?></a>
                </span>
                <span class="btn-wrap hidden">
                    <a class="button facetwp-back"><?php _e( 'Back', 'fwp' ); ?></a>
                </span>
            </div>
            <div class="right-side">
                <a class="button-primary facetwp-save"><?php _e( 'Save Changes', 'fwp' ); ?></a>
            </div>
        </div>

        <div class="facetwp-content-wrap">
            <div class="facetwp-alert">
                Did you know there's <a href="https://facetwp.com/documentation/template-configuration/" target="_blank">another way</a> to setup templates?
            </div>
            <ul class="facetwp-cards"></ul>
            <div class="facetwp-content"></div>
        </div>
    </div>

    <!-- Settings tab -->

    <div class="facetwp-region facetwp-region-settings">
        <div class="flexbox">
            <div class="left-side">
                <div class="facetwp-settings-nav">
                    <a data-tab="general">General</a>
                    <?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
                    <a data-tab="woocommerce">WooCommerce</a>
                    <?php endif; ?>
                    <a data-tab="backup">Backup</a>
                </div>
            </div>
            <div class="right-side">
                <a class="button-primary facetwp-save"><?php _e( 'Save Changes', 'fwp' ); ?></a>
            </div>
        </div>

        <div class="facetwp-content-wrap">

            <!-- General settings -->

            <div class="facetwp-settings-section" data-tab="general">
                <table>
                    <tr>
                        <td><?php _e( 'License Key', 'fwp' ); ?></td>
                        <td>
                            <input type="text" class="facetwp-license" style="width:300px" value="<?php echo get_option( 'facetwp_license' ); ?>" />
                            <input type="button" class="button button-small facetwp-activate" value="<?php _e( 'Activate', 'fwp' ); ?>" />
                            <div class="facetwp-activation-status field-notes"><?php echo $message; ?></div>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td>
                            <?php _e( 'Google Maps API Key', 'fwp' ); ?>
                            <div class="facetwp-tooltip">
                                <span class="icon-question">?</span>
                                <div class="facetwp-tooltip-content">
                                    An API key is required for Proximity facets
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="facetwp-setting" data-name="gmaps_api_key" style="width:300px" />
                            <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#step-1-get-an-api-key-from-the-google-api-console" target="_blank">Get an API key</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e( 'Separators', 'fwp' ); ?>
                        </td>
                        <td>
                            34
                            <input type="text" style="width:20px" class="facetwp-setting" data-name="thousands_separator" />
                            567
                            <input type="text" style="width:20px" class="facetwp-setting" data-name="decimal_separator" />
                            89
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e( 'Loading Animation', 'fwp' ); ?>
                        </td>
                        <td>
                            <select class="facetwp-setting slim" data-name="loading_animation">
                                <option value=""><?php _e( 'Spin', 'fwp' ); ?></option>
                                <option value="fade"><?php _e( 'Fade', 'fwp' ); ?></option>
                                <option value="none"><?php _e( 'None', 'fwp' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e( 'Debug Mode', 'fwp' ); ?>
                        </td>
                        <td>
                            <select class="facetwp-setting slim" data-name="debug_mode">
                                <option value="off"><?php _e( 'Off', 'fwp' ); ?></option>
                                <option value="on"><?php _e( 'On', 'fwp' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- WooCommerce settings -->

            <div class="facetwp-settings-section" data-tab="woocommerce">
                <table>
                    <tr>
                        <td style="width:240px">
                            <?php _e( 'Support product variations?', 'fwp' ); ?>
                            <div class="facetwp-tooltip">
                                <span class="icon-question">?</span>
                                <div class="facetwp-tooltip-content">
                                    Enable if your store uses variable products.
                                </div>
                            </div>
                        </td>
                        <td>
                            <select class="facetwp-setting slim" data-name="wc_enable_variations">
                                <option value="no"><?php _e( 'No', 'fwp' ); ?></option>
                                <option value="yes"><?php _e( 'Yes', 'fwp' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:240px">
                            <?php _e( 'Include all products?', 'fwp' ); ?>
                            <div class="facetwp-tooltip">
                                <span class="icon-question">?</span>
                                <div class="facetwp-tooltip-content">
                                    Show facet choices for out-of-stock products?
                                </div>
                            </div>
                        </td>
                        <td>
                            <select class="facetwp-setting slim" data-name="wc_index_all">
                                <option value="no"><?php _e( 'No', 'fwp' ); ?></option>
                                <option value="yes"><?php _e( 'Yes', 'fwp' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Backup settings -->

            <div class="facetwp-settings-section" data-tab="backup">
                <table>
                    <tr>
                        <td>
                            <?php _e( 'Export', 'fwp' ); ?>
                        </td>
                        <td valign="top">
                            <select class="export-items" multiple="multiple" style="width:250px; height:100px">
                                <?php foreach ( $export as $val => $label ) : ?>
                                <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a class="button export-submit"><?php _e( 'Export', 'fwp' ); ?></a>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td>
                            <?php _e( 'Import', 'fwp' ); ?>
                        </td>
                        <td>
                            <div><textarea class="import-code" placeholder="<?php _e( 'Paste the import code here', 'fwp' ); ?>"></textarea></div>
                            <div><input type="checkbox" class="import-overwrite" /> <?php _e( 'Overwrite existing items?', 'fwp' ); ?></div>
                            <div style="margin-top:5px"><a class="button import-submit"><?php _e( 'Import', 'fwp' ); ?></a></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Support tab -->

    <div class="facetwp-region facetwp-region-support">
        <div class="facetwp-content-wrap">
            <?php include( FACETWP_DIR . '/templates/page-support.php' ); ?>
        </div>
    </div>

    <!-- Hidden: clone settings -->

    <div class="hidden clone-facet">
        <div class="facetwp-row">
            <div class="table-row code-unlock">
                This facet is locked to prevent changes. <button class="unlock">Unlock now</button>
            </div>
            <table>
                <tr>
                    <td><?php _e( 'Label', 'fwp' ); ?>:</td>
                    <td>
                        <input type="text" class="facet-label" value="New facet" />
                        &nbsp; &nbsp;
                        <?php _e( 'Name', 'fwp' ); ?>: <span class="facet-name" contentEditable="true">new_facet</span>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( 'Facet type', 'fwp' ); ?>:</td>
                    <td>
                        <select class="facet-type">
                            <?php foreach ( $facet_types as $name => $class ) : ?>
                            <option value="<?php echo $name; ?>"><?php echo $class->label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="facetwp-show name-source">
                    <td>
                        <?php _e( 'Data source', 'fwp' ); ?>:
                    </td>
                    <td>
                        <select class="facet-source">
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
            </table>
            <hr />
            <table class="facet-fields"></table>
        </div>
    </div>

    <div class="hidden clone-template">
        <div class="facetwp-row">
            <div class="table-row code-unlock">
                This template is locked to prevent changes. <button class="unlock">Unlock now</button>
            </div>
            <div class="table-row">
                <input type="text" class="template-label" value="New template" />
                &nbsp; &nbsp;
                <?php _e( 'Name', 'fwp' ); ?>: <span class="template-name" contentEditable="true">new_template</span>
            </div>
            <div class="table-row">
                <div class="side-link open-builder"><?php _e( 'Open query builder', 'fwp' ); ?></div>
                <div class="row-label"><?php _e( 'Query Arguments', 'fwp' ); ?></div>
                <textarea class="template-query"></textarea>
            </div>
            <div class="table-row">
                <div class="side-link"><a href="https://facetwp.com/documentation/template-configuration/#display-code" target="_blank"><?php _e( 'What goes here?', 'fwp' ); ?></a></div>
                <div class="row-label"><?php _e( 'Display Code', 'fwp' ); ?></div>
                <textarea class="template-template"></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Modal window -->

<div class="media-modal">
    <button class="button-link media-modal-close"><span class="media-modal-icon"></span></button>
    <div class="media-modal-content">
        <div class="media-frame">
            <div class="media-frame-title">
                <h1><?php _e( 'Query Builder', 'fwp' ); ?></h1>
            </div>
            <div class="media-frame-router">
                <div class="media-router">
                    <?php _e( 'Which posts would you like to use for the content listing?', 'fwp' ); ?>
                </div>
            </div>
            <div class="media-frame-content">
                <div class="modal-content-wrap">
                    <div class="flexbox">
                        <div class="qb-area"></div>
                        <div class="qb-area-results">
                            <textarea class="qb-results" readonly></textarea>
                            <button class="button qb-send"><?php _e( 'Send to editor', 'fwp' ); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="media-modal-backdrop"></div>
