<?php
/**
 * Base controller for global plugin configuration screens 
 */
abstract class Loco_admin_config_BaseController extends Loco_mvc_AdminController {


    /**
     * {@inheritdoc}
     */
    public function init(){
        parent::init();
        // navigate between config view siblings
        $tabs = new Loco_admin_Navigation;
        $this->set( 'tabs', $tabs );
        $actions = array (
            ''  => __('Settings','loco'),
            'version'  => __('Version','loco'),
        );
        $suffix = (string) $this->get('action');
        foreach( $actions as $action => $name ){
            $href = Loco_mvc_AdminRouter::generate( 'config-'.$action, $_GET );
            $tabs->add( $name, $href, $action === $suffix );
        }
    }
    


    /**
     * {@inheritdoc}
     */
    public function getHelpTabs(){
        return array (
            __('Overview','default') => $this->view('tab-settings'),
        );
    }
    
    
}