jQuery(document).ready(function(){         
    jQuery('#icl_navigation_show_cat_menu').change(function(){
        if(jQuery(this).attr('checked')){
            jQuery('#icl_cat_menu_contents').fadeIn();
        }else{
            jQuery('#icl_cat_menu_contents').fadeOut();
        }
    })
    jQuery('#icl_navigation_form').submit(wpmlCMSNavSaveForm);
    jQuery('#icl_navigation_caching_clear').click(clearNavigationCache);        
});

function clearNavigationCache() {
    var thisb = jQuery(this);
    thisb.attr('disabled','disabled').after(wpml_cms_nav_ajxloaderimg);
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: "action=wpml_cms_nav_clear_nav_cache",
        success: function(msg){
            thisb.removeAttr('disabled').next().fadeOut();
        }
    });
}

function wpmlCMSNavSaveForm(){
    var thisf = jQuery(this);
    thisf.find(':submit').attr('disabled','disabled').after(wpml_cms_nav_ajxloaderimg);    
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: "action=wpml_cms_nav_save_form&"+thisf.serialize(),
        success: function(msg){
            thisf.find(':submit').removeAttr('disabled').next().fadeOut();    
        }
    });
    return false;
}