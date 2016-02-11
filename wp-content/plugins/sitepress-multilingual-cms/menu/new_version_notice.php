<?php
    if (!isset($_GET['force_new_version_notice'])) {
        
        // hide on non WPML pages
        $mtchs = array(ICL_PLUGIN_FOLDER);
        if(defined('WPML_CMS_NAV_VERSION')) $mtchs[] = WPML_CMS_NAV_PLUGIN_FOLDER;
        if(defined('WPML_STICKY_LINKS_VERSION')) $mtchs[] = WPML_STICKY_LINKS_FOLDER;
        if(defined('WPML_ST_VERSION')) $mtchs[] = WPML_ST_FOLDER;
        if(defined('WPML_TM_VERSION')) $mtchs[] = WPML_TM_FOLDER;
        
        if (!isset($_GET['page']) || !preg_match('@^('.join('|', $mtchs).')/@', $_GET['page'])) {
            return;
        }
        
        if (!isset($_GET['page']) || $_GET['page'] == ICL_PLUGIN_FOLDER . '/menu/support.php') {
            // don't show on the support page
            return;
        }
        
    }    

    global $wpml_plugins;                     
    if ( !function_exists( 'get_plugins' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    $plugins = get_plugins();

    $current = get_site_transient( 'update_plugins' );
    if ( ! is_object($current) ) {
        $current = new stdClass;
    }
        
    $upgrade_required = false;
    $subsrcription_required = false;
    foreach($plugins as $key => $plugin) {
        if (in_array($plugin['Name'], $wpml_plugins)) {
            
            if (isset($current->response[$key])) {
                if ( @version_compare($current->response[$key]->new_version, $plugin['Version'], '>') ) {
                    $upgrade_required = true;
                }
                if (!isset($current->response[$key]->package)) {
                    $subsrcription_required = true;
                }
            }
            
        }
        
    }

    if (!$upgrade_required && !$subsrcription_required) {
        return;
    }
    
    if ($subsrcription_required) {
        $message = sprintf(__('Your WPML subscription details are required. <a%s>More info</a>', 'sitepress'), ' href="' . admin_url('admin.php?page=' . ICL_PLUGIN_FOLDER .'/menu/support.php') . '"');
    } else {
        $message = sprintf(__('A new version of WPML is available. This version contains important security fixes, improved performance and new features. <a href="%s">Upgrade now</a>', 'sitepress'), rtrim(get_option('siteurl'),'/') . '/wp-admin/plugins.php?s=wpml');
    }
?>
<?php if (!isset($_GET['force_new_version_notice'])): ?>
    <br clear="all" />
    <div id="icl_new_version_message" class="updated message fade" style="clear:both;margin-top:5px;">
        <p><?php printf($message); ?></p>
    </div>
<?php else: ?>
    <div id="icl_new_version_message" class="icl_yellow_box" >
        <p><?php printf($message); ?></p>
    </div>
<?php endif; ?>
