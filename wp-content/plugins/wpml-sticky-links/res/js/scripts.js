jQuery(document).ready(function(){
        jQuery('#icl_save_sl_options').submit(wpml_sticky_links_save_options);
})

function wpml_sticky_links_save_options() {
    var thisf = jQuery(this);
    thisf.find(':submit').attr('disabled','disabled').after(wpml_sticky_links_ajxloaderimg);
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: "action=wpml_sticky_links_save_options&"+thisf.serialize(),
        success: function(msg){
            thisf.find(':submit').removeAttr('disabled').next().fadeOut();
        }
    });
    return false;
}
