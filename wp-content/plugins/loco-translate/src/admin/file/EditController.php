<?php
/**
 * PO editor view
 */
class Loco_admin_file_EditController extends Loco_admin_file_BaseController {


    /**
     * {@inheritdoc}
     */
    public function init(){
        parent::init();
        $this->enqueueStyle('editor');
        //
        $file = $this->get('file');
        $bundle = $this->getBundle();
        // translators: %1$s is the file name, %2$s is the bundle name
        $this->set('title', sprintf( __('Editing %1$s in %2$s','loco'), $file->basename(), $bundle ) );
    }


    /**
     * {@inheritdoc}
     */
    public function getHelpTabs(){
        return array (
            __('Overview','default') => $this->view('tab-file-edit'),
        );
    }


    /**
     * {@inheritdoc}
     */
    public function render(){
        
        // file must exist for editing
        $file = $this->get('file');
        if( $fail = $this->getFileError($file) ){
            return $fail; 
        }
        
        // editor will be rendered
        $this->enqueueScript('editor');
        
        // Parse file data into JavaScript for editor
        try {
            $this->set('modified', $file->modified() );
            $data = Loco_gettext_Data::load( $file );
        }
        catch( Exception $e ){
            Loco_error_AdminNotices::add( Loco_error_Exception::convert($e) );
            $data = Loco_gettext_Data::dummy();
        }

        // Pre-populate PO headers with data that JavaScript doesn't have access to
        if( $locale = $this->get('locale') ){
            $data->localize( $locale );
            $lname = $locale->getName() or $lname = (string) $locale;
            $this->set( 'localeName', $lname );
        }
        
        // default is to permit editing of any file
        $readonly = false;

        
        // Establish if file belongs to a configured project
        try {
            $bundle = $this->getBundle();
            $project = $this->getProject();
        }
        // Fine if not, this just means sync isn't possible.
        catch( Loco_error_Exception $e ){
            Loco_error_AdminNotices::debug( sprintf("Sync is disabled because this file doesn't relate to a known set of translations", $bundle ) );
            $project = null;
        }
            
        // Establish PO/POT edit mode
        if( $locale ){
            // alternative POT file may be forced by PO headers
            $head = $data->getHeaders();
            if( $head->has('X-Loco-Template') ){
                $potfile = new Loco_fs_File($head['X-Loco-Template']);
                $potfile->normalize( $bundle->getDirectoryPath() );
            }
            // no way to get configured POT if invalid project
            else if( is_null($project) ){
                $potfile = null;
            }
            // else use project-configured template, assuming there is one
            else if( $potfile = $project->getPot() ){
                // Handle situation where project defines a localised file as the official template
                if( $potfile->equal($file) ){
                    $locale = null;
                    $potfile = null;
                }
            }
            if( $potfile ){
                // Validate template file as long as it exists
                if( $potfile->exists() ){
                    $potdata = Loco_gettext_Data::load( $potfile );
                    if( ! $potdata->equalSource($data) ){
                        Loco_error_AdminNotices::debug( sprintf( __("Translations don't match template. Run sync to update from %s",'loco'), $potfile->basename() ) );
                    }
                }
                // else template doesn't exist, so sync will be done to source code
                else {
                    // Loco_error_AdminNotices::debug( sprintf( __('Template file not found (%s)','loco'), $potfile->basename() ) );
                    $potfile = null;
                }
            }
        }
        
        // notify if template is locked (save and sync will be disabled)
        if( is_null($locale) && $project && $project->isPotLocked() ){
            Loco_error_AdminNotices::warn('Template is protected from updates by the bundle configuration');
            $readonly = true;
        }
        
        // back end expects paths relative to wp-content
        $wp_content = loco_constant('WP_CONTENT_DIR');
        
        $this->set( 'js', new Loco_mvc_ViewParams( array(
            'podata' => $data->jsonSerialize(),
            'locale' => $locale ? $locale->jsonSerialize() : null,
            'potpath' => $locale && $potfile ? $potfile->getRelativePath($wp_content) : null,
            'popath' => $this->get('path'),
            'readonly' => $readonly,
            'project' => $project ? array (
                'bundle' => $bundle->getId(),
                'domain' => $project->getId(),
            ) : null,
            'nonces' => $readonly ? null : array (
                'save' => wp_create_nonce('save'),
                'sync' => wp_create_nonce('sync'),
                'fsConnect' => wp_create_nonce('fsConnect'),
            ),
        ) ) );
        
        $this->set( 'ui', new Loco_mvc_ViewParams( array(
             'add'      => _x('Add','Editor button','loco'),
             'del'      => _x('Remove','Editor button','loco'),
             'help'     => _x('Help','Editor button','loco'),
             'save'     => _x('Save','Editor button','loco'),
             'sync'     => _x('Sync','Editor button','loco'),
             'revert'   => _x('Revert','Editor button','loco'),
             'fuzzy'    => _x('Fuzzy','Editor button','loco'),
             'download' => _x('Download','Editor button','loco'),
             'filter'   => __('Filter translations','loco'),
        ) ) );

        // Download form params
        $hidden = new Loco_mvc_HiddenFields( array(
            'path'   => '',
            'source' => '',
            'route'  => 'download',
            'action' => 'loco_download',
        ) );
        $this->set( 'dlFields', $hidden->setNonce('download') );
        $this->set( 'dlAction', admin_url('admin-ajax.php','relative') );
        
        // validate file system writableness for all operations involved in save
        $writable = $file->writable();
        
        // Check in advance if MO file can be compiled in this directory
        if( $writable ){
            $dummy = $file->cloneExtension('mo');
            if( ! ( $dummy->exists() ? $dummy->writable() : $dummy->creatable() ) ){
                $writable = false;
            }
            // Check in advance if backups will work in this directory
            else if( Loco_data_Settings::get()->num_backups ){
                $dummy = new Loco_fs_File( $file->dirname().'/does-not-exist.po~' );
                if( ! $dummy->creatable() ){
                    $writable = false;
                }
            }
        }

        // File system connect if any operations likely to fail
        if( ! $writable ){
            $this->prepareFsConnect( 'connect', $this->get('path') );
        }
        
        // set simpler title for breadcrumb
        $this->set('title', $file->basename() );
        
        // ok to render editor as either po or pot
        $tpl = $locale ? 'po' : 'pot';
        return $this->view( 'admin/file/edit-'.$tpl, array() );
    }
    
    
    
    
}