<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_view_back_tmce extends WYSIJA_view_back{

    var $title='Tiny';
    var $icon='icon-options-general';
    var $scripts=array();

    function WYSIJA_view_back_tmce(){
        $this->WYSIJA_view_back();
    }

    function getScriptsStyles(){
        ?>
        <link rel='stylesheet' href='<?php $urlblog=get_bloginfo('wpurl');echo $urlblog ?>/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=widgets,global,wp-admin' type='text/css' media='all' />
        <link rel='stylesheet' id='colors-css'  href='<?php echo $urlblog ?>/wp-admin/css/colors-fresh.css' type='text/css' media='all' />
        <link rel='stylesheet' id='colors-css'  href='<?php echo $urlblog ?>/wp-includes/css/buttons.css' type='text/css' media='all' />
        <!--[if lte IE 7]>
        <link rel='stylesheet' id='ie-css'  href='<?php echo $urlblog ?>/wp-admin/css/ie.css' type='text/css' media='all' />
        <![endif]-->
        <link rel='stylesheet'  href='<?php echo $urlblog ?>/wp-content/plugins/wysija-newsletters/css/tmce/widget.css' type='text/css' media='all' />
        <?php wp_print_scripts('jquery'); ?>
        <script type="text/javascript" src="<?php echo $urlblog; ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script type='text/javascript' src='<?php echo $urlblog ?>/wp-content/plugins/wysija-newsletters/js/admin-tmce.js'></script>
        <?php
    }


    function head(){
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $this->title; ?></title>
<?php $this->getScriptsStyles() ?>
<base target="_self" />
</head>
<body>

<?php

    }

    function foot(){
        ?>

        </body>
        </html>
        <?php
    }

    function subscribersAdd($data){
        $this->head();
        ?>
        <form id="formTable" action="" style="display:block;" class="wp-core-ui" method="post" >
                <div id="subscriber-ccount-form">
                    <select id="wysija-list">
                        <option value="0">All</option>
                        <?php foreach ($data['lists'] as $list){ ?>
                            <option value="<?php echo $list['list_id']; ?>"><?php echo $list['name']; ?></option>
                        <?php } ?>
                    </select>
                    <?php if ($data['confirm_dbleoptin']) {?>
                        <br /><br />
                        <input type="checkbox" id="confirmedSubscribers"/><label><?php echo esc_attr(__('Confirmed subscribers only', WYSIJA)); ?></label>
                    <?php } ?>
                    <br /><br />
                    <input type="submit" id="subscribers-insert" class="button-primary action" name="doaction" value="<?php echo esc_attr(__('Insert', WYSIJA)); ?>">
                </div>
                <div style="clear:both;"></div>
         </form>
            <?php
        $this->foot();
    }
    
    function registerAdd($datawidget){
        $this->head();
        ?>
        <form id="formTable" action="" style="display:block;" class="wp-core-ui" method="post" >

                <div id="widget-form">

                    <?php
                    require_once(WYSIJA_WIDGETS.'wysija_nl.php');
                    $widgetNL=new WYSIJA_NL_Widget(true);
                    $widgetNL->form($datawidget);
                    ?>
                    <input type="hidden" name="widget_id" value="wysija-nl-<?php echo time(); ?>" />
                    <input type="submit" id="widget-insert" class="button-primary action" name="doaction" value="<?php echo esc_attr(__('Insert form', WYSIJA)); ?>">
                </div>

                <div style="clear:both;"></div>
         </form>
            <?php
        $this->foot();
    }

}
