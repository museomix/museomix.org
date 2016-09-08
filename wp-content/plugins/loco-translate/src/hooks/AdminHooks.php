<?php
/**
 * Common hooks for all admin contexts
 */
class Loco_hooks_AdminHooks extends Loco_hooks_Hookable {


    /**
     * Main entry point from loco.php bootstrap file
     * @return Loco_hooks_AdminHooks
     */
    public static function init(){
        add_action('admin_notices', array('Loco_compat_Failure','print_hook_failure') );
        return new Loco_hooks_AdminHooks;
    }


    
    /**
     * {@inheritdoc}
     */
    public function __construct(){
        parent::__construct();
        // Ajax router will be called directly in tests
        // @codeCoverageIgnoreStart
        if( loco_doing_ajax() ){
            $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
            // initialize Ajax router before hook fired so we can handle output buffering
            if( 'loco_' === substr($action,0,5)  && isset($_REQUEST['route']) ){
                new Loco_mvc_AjaxRouter;
                Loco_package_Listener::create();
            }
        }
        // @codeCoverageIgnoreEnd
        // page router required on all pages as it hooks in the menu
        else {
            new Loco_mvc_AdminRouter;
            // we don't know we will render a page yet, but we need to listen for text domain hooks as early as possible
            if( isset($_GET['page']) && 'loco' === substr($_GET['page'],0,4) ){
                Loco_package_Listener::create();
                // trigger post-upgrade process if required
                $opts = Loco_data_Settings::get();
                if( $opts->migrate() ){
                    // would trigger upgrade handlers here in future releases
                }
            }
            // we'll need our own translations on all admin pages not just our own, for menu items etc..
            $domainPath = dirname( loco_plugin_self() ).'/languages';
            load_plugin_textdomain( 'loco', false, $domainPath );
        }
    }



    /**
     * "admin_init" callback.
     * Unhooks failure notice that would fire if this hook was not successful
     */
    public function on_admin_init(){
        remove_action( 'admin_notices', array('Loco_compat_Failure','print_hook_failure') );
    }



    /**
     * plugin_action_links action callback
     */
    public function on_plugin_action_links( $links, $plugin = '' ){
         try {
             if( $plugin && current_user_can('loco_admin') && Loco_package_Plugin::get_plugin($plugin) ){
                // ok to add "translate" link into meta row
                $href = Loco_mvc_AdminRouter::generate('plugin-view', array( 'bundle' => $plugin) );
                $links[] = '<a href="'.esc_attr($href).'">'.esc_html__('Translate','loco').'</a>';
             }
         }
         catch( Exception $e ){
             // $links[] = esc_html( 'Debug: '.$e->getMessage() );
         }
         return $links;
    }



    /**
     * deactivate_plugin action callback
     *
    public function on_deactivate_plugin( $plugin, $network = false ){
        if( loco_plugin_self() === $plugin ){
            // TODO flush all our transient cache entries
            // "DELETE FROM ___ WHERE `option_name` LIKE '_transient_loco_%' OR `option_name` LIKE '_transient_timeout_loco_%'";
        }
    }*/


    /*public function filter_all( $hook ){
        error_log( $hook, 0 );
    }*/
    
}
