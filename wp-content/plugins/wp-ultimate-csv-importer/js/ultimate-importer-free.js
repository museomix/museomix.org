var jQuery = jQuery.noConflict();
jQuery(document).ready(function () {
    jQuery('.dropdown-toggle').dropdown('toggle');
    var checkmodule = document.getElementById('checkmodule').value;
    if (checkmodule != 'dashboard' && checkmodule != 'filemanager' && checkmodule != 'support' && checkmodule != 'export' && checkmodule != 'settings' && checkmodule != 'mappingtemplate' && checkmodule != 'schedulemapping') {
        var get_log = document.getElementById('log').innerHTML;
        if (!jQuery.trim(jQuery('#log').html()).length) {
            document.getElementById('log').innerHTML = '<p style="margin:15px;color:red;">' + wp_ultimate_translate_importer.dashboard_msg + '</p>';
        }
    }
    if (checkmodule == 'custompost') {
        var step = jQuery('#stepstatus').val();
        if (step == 'mapping_settings') {
            var cust_post_list_count = jQuery('#cust_post_list_count').val();
            if (cust_post_list_count == '0')
                document.getElementById('cust_post_empty').style.display = '';
        }
    }
    if (checkmodule != 'filemanager' && checkmodule != 'settings' && checkmodule != 'support' && checkmodule != 'export' && checkmodule != 'mappingtemplate' && checkmodule != 'schedulemapping') {
        var checkfile = jQuery('#checkfile').val();
        var dir_path = jQuery('#dirpathval').val();
        var uploadedFile = jQuery('#uploadedFile').val();
        var noncekey = jQuery('#nonceKey').val();
        var get_log = jQuery('#log').val();
        var checkmodule = jQuery('#checkmodule').val();
        if (!jQuery.trim(jQuery('#log').html()).length) {
            if (checkmodule != 'dashboard')
                document.getElementById('log').innerHTML = '<p style="margin:15px;color:red;">' + wp_ultimate_translate_importer.dashboard_msg + '</p>';
        }
    }
});

jQuery(function() {
    jQuery('marquee').mouseover(function() {
        jQuery(this).attr('scrollamount',0,0);
    }).mouseout(function() {
        jQuery(this).attr('scrollamount',5,0);
    });
});

function prepareUpload(){
    var check_upload_dir = document.getElementById('is_uploadfound').value;
    if (check_upload_dir == 'notfound') {
        document.getElementById('browsefile').style.display = 'none';
        jQuery('#defaultpanel').css('visibility', 'hidden');
        jQuery('<p/>').text("").appendTo('#warning');
        jQuery("#warning").empty();
        jQuery('#warning').css('display', 'inline');
        jQuery('<p/>').text("Warning:   Sorry. There is no uploads directory Please create it with write permission.").appendTo('#warning');
        jQuery('#warning').css('color', 'red');
        jQuery('#warning').css('font-weight', 'bold');
        jQuery('#progress .progress-bar').css('visibility', 'hidden');
    }
    else {
        var uploadPath = document.getElementById('uploaddir').value;
        var curraction = document.getElementById('current_module').value;
        var frmdata = new FormData();
        var uploadfile_data = jQuery('#fileupload').prop('files')[0];
        frmdata.append('files', uploadfile_data);
        frmdata.append('action','uploadfilehandle');
        frmdata.append('curr_action', curraction);
        frmdata.append('uploadPath', uploadPath);
	frmdata.append('secure_key',wp_ultimate_translate_importer.secure_key);
        jQuery.ajax({
            url : ajaxurl,
            type : 'post',
            data : frmdata,
            cache: false,
            contentType : false,
            processData: false,
            success : function(data) {
                var fileobj =JSON.parse(data);
                jQuery.each(fileobj,function(objkey,objval){
                    jQuery.each(objval,function(o_key,file){
                        document.getElementById('uploadFileName').value=file.name;
                        var filewithmodule = file.uploadedname.split(".");
                        var check_file = filewithmodule[filewithmodule.length - 1];
                        if(check_file != "csv" && check_file != "txt") {
                            alert(wp_ultimate_translate_importer.fileformatmsg);
                            return false;
                        }
                        if(check_file == "csv"){
                            var filenamecsv = file.uploadedname.split(".csv");
                            file.uploadedname = filenamecsv[0] + curraction + ".csv";
                        }
                        if(check_file == "txt"){
                            var filenametxt = file.uploadedname.split(".txt");
                            file.uploadedname = filenametxt[0] + curraction + ".txt";
                        }
                        document.getElementById('upload_csv_realname').value = file.uploadedname;
                        document.getElementById('progressbar').value = '100';
                        var get_version1 = file.name.split(curraction);
                        var get_version2 = get_version1[1].split(".csv");
                        var get_version3 = get_version2[0].split("-");
                        document.getElementById('current_file_version').value = get_version3[1];
                        jQuery('#uploadedfilename').val(file.uploadedname);
                        jQuery( "#filenamedisplay" ).empty();
                        if(file.size>1024 && file.size<(1024*1024))
                        {
                            var fileSize =(file.size/1024).toFixed(2)+' kb';
                        }
                        else if(file.size>(1024*1024))
                        {
                            var fileSize =(file.size/(1024*1024)).toFixed(2)+' mb';
                        }
                        else
                        {
                            var fileSize= (file.size)+' byte';
                        }
                        jQuery('<p/>').text((file.name)+' - '+fileSize).appendTo('#filenamedisplay');
                        jQuery('#importfile').attr('disabled', false);
                    });
                });

            }
        });
    }
}

function selectpoststatus() {
    var poststate = document.getElementById('wpfields').value;
    var importer = document.getElementById('selectedImporter').value;
    var ps = document.getElementById("importallwithps");
    var selectedpsindex = ps.options[ps.selectedIndex].value;
    if (selectedpsindex == 6) {
        document.getElementById('globalpassword_label').style.display = "block";
        document.getElementById('globalpassword_text').style.display = "block";
        document.getElementById('globalpassword_txt').focus();
    }
    else {
        document.getElementById('globalpassword_label').style.display = "none";
        document.getElementById('globalpassword_text').style.display = "none";
    }
    var totdropdown = document.getElementById('h2').value;
    var total = parseInt(totdropdown);
    if (selectedpsindex != '0') {

        for (var i = 0; i < poststate; i++) {

            dropdown = document.getElementById("fieldname" + i);
            if(dropdown.value == "post_status"){
                document.getElementById("mapping"+i).selectedIndex = "0";
            }
        }

    }
}

function changefield()
{
    var importer = document.getElementById('selectedImporter').value;
    var poststate = document.getElementById('wpfields').value;
    for(var i=0;i < poststate;i++)
    {
        dropdown = document.getElementById("fieldname"+i);
        if(dropdown.value == "post_status"){

            if(document.getElementById("mapping"+i).selectedIndex != 0)
                document.getElementById("importallwithps").selectedIndex = "0";
        }
    }
    var ps = document.getElementById("importallwithps");
    var selectedpsindex = ps.options[ps.selectedIndex].value;
    if(selectedpsindex == 0){
        document.getElementById('globalpassword_label').style.display = "none";
        document.getElementById('globalpassword_text').style.display = "none";
    }
}


// Function for add customfield

function addcorecustomfield(id){
    var table_id = id;
    var newrow = table_id.insertRow(-1);
    var count = document.getElementById('basic_count').value;
    count = parseInt(count)+1;
    newrow.id = 'custrow'+count;
    var filename = document.getElementById('uploadedFile').value;
    var row_count = document.getElementById('corecustomcount').value;
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            'filename' : filename,
            'corecount' : count,
            'action' : 'addcorecustomfd',
	    'secure_key' : wp_ultimate_translate_importer.secure_key
        },
        success: function (response) {
            newrow.innerHTML = response;
            row_count = parseInt(row_count) + 1;
            document.getElementById('corecustomcount').value = row_count;
            document.getElementById('basic_count').value = count;
        }
    });

}


function clearMapping() {
    var importer = document.getElementById('selectedImporter').value;
    var wpfield = document.getElementById('wpfields').value;
    var termfield = 0;
    if(document.getElementById('termfields')){
    termfield = document.getElementById('termfields').value;
    }
    for (var j = 0; j < wpfield; j++) {
        document.getElementById('mapping' + j).selectedIndex = "0";
    }
    if (importer != 'users') {
        var customfield = document.getElementById('customfields').value;
        for (var j = wpfield; j < customfield; j++) {
            document.getElementById('coremapping' + j).selectedIndex = "0";
        }
	for(var j = customfield; j < termfield; j++) {
	    document.getElementById('term_mapping'+j).selectedIndex = "0";
	}
        if(document.getElementById("seofields") && document.getElementById("addcorecustomfields")){
            var seofield = document.getElementById('seofields').value;
            var addcorecustomfield= document.getElementById('basic_count').value;
        }
        if(seofield != null && addcorecustomfield != null){
	if(termfield != 0){
            for(var j=termfield;j<seofield;j++) {
                document.getElementById('seomapping'+j).selectedIndex = "0";
            }
	}
	else {
		           for(var j=customfield;j<seofield;j++) {
                document.getElementById('seomapping'+j).selectedIndex = "0";
            }
	}
            for(var j=seofield;j<=addcorecustomfield;j++) {
                document.getElementById('addcoremapping'+j).selectedIndex = "0";
            }
        }
        else if(document.getElementById("seofields")){
            var seofield = document.getElementById('seofields').value;
            if(seofield != null){
                for(var j=termfield;j<seofield;j++) {
                    document.getElementById('seomapping'+j).selectedIndex = "0";
                }
            }
        }
        else if(document.getElementById("addcorecustomfields")){
            var addcorecustomfield= document.getElementById('basic_count').value;
            if(addcorecustomfield != null){
                for(var j=customfield;j<=addcorecustomfield;j++) {
                    document.getElementById('addcoremapping'+j).selectedIndex = "0";
                }
            }
        }
    }
}


function shownotification(msg, alerts) {
    var newclass;
    var divid = "notification_wp_csv";

    if (alerts == 'success')
        newclass = "alert alert-success";
    else if (alerts == 'danger')
        newclass = "alert alert-danger";
    else if (alerts == 'warning')
        newclass = "alert alert-warning";
    else
        newclass = "alert alert-info";

    jQuery('#' + divid).removeClass()
    jQuery('#' + divid).html(msg);
    jQuery('#' + divid).addClass(newclass);
    // Scroll
    jQuery('html,body').animate({
            scrollTop: jQuery("#" + divid).offset().top
        },
        'slow');
}

function import_csv() {
    // code added by goku to check whether templatename
    var mapping_checked = jQuery('#mapping_templatename_checked').is(':checked');
    var mapping_tempname = jQuery('#mapping_templatename').val();
    var mapping_checked_radio = jQuery('input[name=tempaction]:radio:checked').val();
    if (mapping_checked || mapping_checked_radio == 'saveas') {
        if (mapping_checked_radio == 'saveas')
            mapping_tempname = jQuery('#mapping_templatename_edit').val();

        if (jQuery.trim(mapping_tempname) == '') {
            alert(wp_ultimate_translate_importer.emptytemplate);
            return false;
        }
        else {
            // check templatename already exists
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                async: false,
                data: {
                    'action': 'checktemplatename',
                    'templatename': mapping_tempname,
                },
                success: function (data) {
                    if (data != 0) {
                        jQuery('#mapping_templatename').val('');
                    }
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
    }
    var mapping_tempname = jQuery('#mapping_templatename').val();
    if (mapping_checked_radio == 'saveas')
        if (mapping_tempname == '' && (mapping_checked || mapping_templatename_edit == 'saveas')) {
            alert(wp_ultimate_translate_importer.exist_template);
            return false;
        }
    // code ends here on checking templatename
    var total = document.getElementById('wpfields').value;
    var importer = document.getElementById('selectedImporter').value;
    var pwdvalidation = document.getElementById('importallwithps');
    if(importer != 'users')
        var selectedpsindex = pwdvalidation.options[pwdvalidation.selectedIndex].value;
    var header_count = document.getElementById('h2').value;
    var array = new Array();
    var wparray = new Array();
    var val1, val2, val3, val4, val5, val6, val7, error_msg, chk_status_in_csv, post_status_msg;
    val1 = val2 = val3 = val4 = val5 = val6 = val7 = post_status_msg = post_type = 'Off';
    for (var i = 0; i < total; i++) {
        var value = document.getElementById("mapping" + i).value;
        array[i] = value;
        var wpvalue = document.getElementById("fieldname"+ i).value;
        wparray[i] = wpvalue;
    }
    if (importer == 'post' || importer == 'page' || importer == 'custompost' || importer == 'eshop') {
        if (importer == 'custompost') {
            var getSelectedIndex = document.getElementById('custompostlist');
            var SelectedIndex = getSelectedIndex.value;
            if (SelectedIndex != 'select')
                post_type = 'On';
        }
        chk_status_in_csv = document.getElementById('importallwithps').value;
        if (chk_status_in_csv != 0)
            post_status_msg = 'On';
        if(selectedpsindex == 6) {
            var checkpwd = document.getElementById('globalpassword_txt').value;
            if(checkpwd != '')
                val7 = 'On';
        }
        for (var j = 0; j < wparray.length; j++) {
            if (wparray[j] == 'post_title' && array[j] != '-- Select --') {
                val1 = 'On';
            }
            if (post_status_msg == 'Off') {
                if (wparray[j] == 'post_status' && array[j] != '-- Select --')
                    post_status_msg = 'On';
            }
        }
        if (selectedpsindex == 6){
            if (importer != 'custompost' && val1 == 'On' && post_status_msg == 'On' && val7 == 'On') {
                return true;
            }
            else if (importer == 'custompost' && val1 == 'On'  && post_status_msg == 'On' && post_type=='On' && val7 == 'On') {
                return true;
            }
            else {
                error_msg = '';
                if (val7 == 'Off')
                    error_msg += "password";
                if (val1 == 'Off')
                    error_msg += " post_title";
                if(importer == 'custompost') {
                    if (SelectedIndex == 'select')
                        error_msg += " post_type";
                }
                if (post_status_msg == 'Off')
                    error_msg += " post_status";
                showMapMessages('error', 'Error: ' + error_msg + wp_ultimate_translate_importer.mandatory_msg );
                return false;
            }

        }
        else {
            if (importer != 'custompost' && val1 == 'On' && post_status_msg == 'On') {
                return true;
            }
            else if (importer == 'custompost' && val1 == 'On' && post_status_msg == 'On' && post_type == 'On') {
                return true;
            }
            else {
                error_msg = '';
                if (val1 == 'Off')
                    error_msg += " post_title";
                if (importer == 'custompost') {
                    if (SelectedIndex == 'select')
                        error_msg += " post_type,";
                }
                if (post_status_msg == 'Off')
                    error_msg += " post_status";
                showMapMessages('error', 'Error: ' + error_msg + wp_ultimate_translate_importer.mandatory_msg);
                return false;
            }
        }
    }
// validation starts
    else if (importer == 'users') {
        var val1 = val2 = val3 = 'Off';
        var errmsg = "";
        for (var j = 0; j < array.length; j++) {
            if (wparray[j] == 'user_login' && array[j] != '-- Select --')
                val1 = 'On';
            if (wparray[j] == 'user_email' && array[j] != '-- Select --')
                val2 = 'On';
            if (wparray[j] == 'role' && array[j] != '-- Select --')
                val3 = 'On';
        }
        if (val1 == 'On' && val2 == 'On' && val3 == 'On') {
            return true;
        }
        else {
            if(val1 == 'Off')
                errmsg += "user_login ,";
            if(val2 == 'Off')
                errmsg += "user_email ," ;
            if(val3 == 'Off')
                errmsg += "role";
            showMapMessages('error', errmsg + wp_ultimate_translate_importer.generalmsg);
            return false;
        }
    }
// validation ends
}

function showMapMessages(alerttype, msg) {
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'trans_alert_str',
            'type': alerttype,
            'message': msg,
        },
        success: function (response) {
            //      alert(response);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        },
    });
    jQuery("#showMsg").addClass("maperror");
    document.getElementById('showMsg').innerHTML = msg;
    document.getElementById('showMsg').className += ' ' + alerttype;
    document.getElementById('showMsg').style.display = '';
    jQuery("#showMsg").fadeOut(10000);
}

function filezipopen() {
    var advancemedia = document.getElementById('advance_media_handling').checked;
    if (advancemedia == true)
        document.getElementById('filezipup').style.display = '';
    else
        document.getElementById('filezipup').style.display = 'none';

}
//var allowedextension ={ '.zip' : 1 };
function checkextension(filename) {
    var allowedextension = {'.zip': 1};
    var match = /\..+$/;
    var ext = filename.match(match);
    if (allowedextension[ext]) {
        return true;
    }
    else {
        alert(wp_ultimate_translate_importer.validatefile);
        //will clear the file input box.
        location.reload();
        return false;
    }

}


function inline_image_option(id) {
    document.getElementById('startbutton').disabled = false;
    var selected_option = document.getElementById(id).value;
    document.getElementById('inlineimagevalue').value = selected_option;
    if (selected_option == 'inlineimage_location') {
        var image_location = document.getElementById('imagelocation').value;
        document.getElementById('inlineimagevalue').value = image_location;
    }
}

function customimagelocation(val) {
    document.getElementById('inlineimagevalue').value = val;
}

function importRecordsbySettings(siteurl) {
    var importlimit = document.getElementById('importlimit').value;
    var check_limit = check_allnumeric(importlimit);
	if(!check_limit){
		return false;
	}
    var noncekey = document.getElementById('wpnoncekey').value;
    var get_requested_count = importlimit;
    var tot_no_of_records = document.getElementById('checktotal').value;
    var importas = document.getElementById('selectedImporter').value;
    var uploadedFile = document.getElementById('checkfile').value;
    var step = document.getElementById('stepstatus').value;
    var currentlimit = document.getElementById('currentlimit').value;
    var tmpCnt = document.getElementById('tmpcount').value;
    var no_of_tot_records = document.getElementById('tot_records').value;
    var importinlineimage = false;
    var imagehandling = false;
    var inline_image_location = false;
    var useexistingimages = false;
    var currentModule = document.getElementById('current_module').value;
    //alert('currentModule: ' + currentModule); alert('ImportAs: ' + importas);
    if (currentModule != 'users' && currentModule != 'comments') {
        importinlineimage = document.getElementById('multiimage').checked;
        imagehandling = document.getElementById('inlineimagevalue').value;
        inline_image_location = document.getElementById('inline_image_location').value;
        useexistingimages = document.getElementById('useexistingimages').checked;
    }
    var get_log = document.getElementById('log').innerHTML;
    document.getElementById('reportLog').style.display = '';
    document.getElementById('terminatenow').style.display = '';
    if (get_requested_count != '') {
        //return true;
    } else {
        document.getElementById('showMsg').style.display = "";
        document.getElementById('showMsg').innerHTML = '<p id="warning-msg" class="alert alert-warning">' + wp_ultimate_translate_importer.reqfdmsg + '</p>';
        jQuery("#showMsg").fadeOut(10000);
        return false;
    }
    if (parseInt(get_requested_count) <= parseInt(no_of_tot_records)) {
        document.getElementById('server_request_warning').style.display = 'none';
    } else {
        document.getElementById('server_request_warning').style.display = '';
        return false;
    }
    if (get_log == '<p style="margin:15px;color:red;">' + wp_ultimate_translate_importer.dashboard_msg + '</p>') {
        document.getElementById('log').innerHTML = '<p style="margin-left:10px;color:red;">' + wp_ultimate_translate_importer.import_progress + '</p>';
        document.getElementById('startbutton').disabled = true;
    }
    document.getElementById('ajaxloader').style.display = "";
    var tempCount = parseInt(tmpCnt);
    var totalCount = parseInt(tot_no_of_records);
    if (tempCount >= totalCount) {
        document.getElementById('ajaxloader').style.display = "none";
        document.getElementById('startbutton').style.display = "none";
        document.getElementById('importagain').style.display = "";
        document.getElementById('terminatenow').style.display = "none";
        return false;
    }
    var advancemedia = "";
    var dupContent = "";
    var dupTitle = "";
    if (importas == 'post' || importas == 'page' || importas == 'custompost' || importas == 'eshop') {
        advancemedia = document.getElementById('multiimage').checked;
        dupContent = document.getElementById('duplicatecontent').checked;
        dupTitle = document.getElementById('duplicatetitle').checked;
    }
    var postdata = new Array();
    postdata = {
        'dupContent': dupContent,
        'dupTitle': dupTitle,
        'importlimit': importlimit,
        'get_requested_count': get_requested_count,
        'limit': currentlimit,
        'totRecords': tot_no_of_records,
        'selectedImporter': importas,
        'uploadedFile': uploadedFile,
        'tmpcount': tmpCnt,
        'importinlineimage': importinlineimage,
        'inlineimagehandling': imagehandling,
        'inline_image_location': inline_image_location,
        'advance_media': advancemedia,
        'useexistingimages': useexistingimages,
        'wpnonce': noncekey
    }

    var tmpLoc = document.getElementById('tmpLoc').value;
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'importByRequest',
            'postdata': postdata,
            'siteurl': siteurl,
        },
        success: function (data) {
            if (parseInt(tmpCnt) == parseInt(tot_no_of_records)) {
                document.getElementById('terminatenow').style.display = "none";
            }
            if (parseInt(tmpCnt) <= parseInt(tot_no_of_records)) {
                var terminate_action = document.getElementById('terminateaction').value;
                currentlimit = parseInt(currentlimit) + parseInt(importlimit);
                document.getElementById('currentlimit').value = currentlimit;
                console.log('impLmt: ' + importlimit + 'totRecds: ' + tot_no_of_records);
                document.getElementById('tmpcount').value = parseInt(tmpCnt) + parseInt(importlimit);
                if (terminate_action == 'continue') {
                    setTimeout(function () {
                        importRecordsbySettings()
                    }, 0);
                } else {
                    document.getElementById('log').innerHTML += data + '<br/>';
                    if (parseInt(tmpCnt) < parseInt(tot_no_of_records) - 1)
                        document.getElementById('log').innerHTML += "<p style='margin-left:10px;color:red;'>" +wp_ultimate_translate_importer.terminateImport + "</p>";
                    document.getElementById('ajaxloader').style.display = "none";
                    document.getElementById('startbutton').style.display = "none";
                    document.getElementById('terminatenow').style.display = "none";
                    document.getElementById('continuebutton').style.display = "";
                    return false;
                }
            } else {
                document.getElementById('ajaxloader').style.display = "none";
                document.getElementById('startbutton').style.display = "none";
                document.getElementById('importagain').style.display = "";
                return false;
            }
            document.getElementById('log').innerHTML += data + '<br/>';

        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
}

// Terminate import process
function terminateProcess() {
    document.getElementById('terminateaction').value = 'terminate';
}

function continueprocess() {
    var tot_no_of_records = document.getElementById('checktotal').value;
    var tmpCnt = document.getElementById('tmpcount').value;
    var currentlimit = document.getElementById('currentlimit').value;
    var importlimit = document.getElementById('importlimit').value;
    var tot_no_of_records = document.getElementById('checktotal').value;

    if (parseInt(tmpCnt) > parseInt(tot_no_of_records)) {
        document.getElementById('terminatenow').style.display = "none";
    } else {
        document.getElementById('terminatenow').style.display = "";
    }
    if (parseInt(tmpCnt) < parseInt(tot_no_of_records))
        document.getElementById('log').innerHTML += "<div style='margin-left:10px;color:green;'>" + wp_ultimate_translate_importer.continueImport + "</div></br>";
    document.getElementById('ajaxloader').style.display = "";
    document.getElementById('startbutton').style.display = "";
    document.getElementById('continuebutton').style.display = "none";
    document.getElementById('terminateaction').value = 'continue';
    
    setTimeout(function () {
        importRecordsbySettings()
    }, 0);
}

function saveSettings() {
    jQuery('#ShowMsg').css("display", "");
    jQuery('#ShowMsg').delay(2000).fadeOut();
}

function Reload() {
    window.location.reload();
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (typeof haystack[i] == 'object') {
            if (arrayCompare(haystack[i], needle)) {
                return true;
            }
        } else {
            if (haystack[i] == needle) {
                return true;
            }
        }
    }
    return false;
}

function import_again() {
    var get_current_url = document.getElementById('current_url').value;
    window.location.assign(get_current_url);
}

function check_allnumeric(inputtxt) {
    var numbers = /^[0-9]+$/;
    if (inputtxt.match(numbers)) {
        no_of_tot_records = document.getElementById('tot_records').value;
    if (parseInt(inputtxt) <= parseInt(no_of_tot_records)) {
        document.getElementById('server_request_warning').style.display = 'none';
    } else {
        document.getElementById('server_request_warning').style.display = '';
        return false;
    }
        return true;
    }
    else {
        if (inputtxt == '')
            alert(wp_ultimate_translate_importer.reqfdmsg);
        else
            alert(wp_ultimate_translate_importer.validate_recordnum);
        return false;
    }
}

function export_module() {
    var get_selected_module = document.getElementsByName('export');
    var customlist = document.getElementById('export_post_type').value;
    var customtaxonomy = document.getElementById('export_taxo_type').value;
    for (var i = 0, length = get_selected_module.length; i < length; i++) {
        if (get_selected_module[i].checked) {
            // do whatever you want with the checked radio
            //alert(get_selected_module[i].value);
            // only one radio can be logically checked, don't check the rest
            //break;
            if(get_selected_module[i].value == 'custompost'){
                if(customlist == '--Select--'){
                    showMapMessages('error',wp_ultimate_translate_importer.customlist);
                    return false;
                }
            }
            if(get_selected_module[i].value == 'customtaxonomy'){
                if(customtaxonomy == '--Select--'){
                    showMapMessages('error',wp_ultimate_translate_importer.customtaxonomy);
                    return false;
                }
            }
            return true;
        }
    }
    showMapMessages('error', wp_ultimate_translate_importer.validate_exportmsg);
    return false;
}

function export_check(value) {
    if (value == 'woocommerce' || value == 'wpcommerce' || value == 'marketpress' || value == 'users' || value == 'category' || value == 'tags' || value == 'customtaxonomy' || value == 'customerreviews') {
        document.getElementById(value).checked = false;
        document.getElementById('ShowMsg').style.display = "";
        value = value.toUpperCase();
        document.getElementById('warning-msg').innerHTML = value + wp_ultimate_translate_importer.ultimatepromsg;
        jQuery('#ShowMsg').delay(7000).fadeOut();
    }
}

function choose_import_method(id) {
    if (id == 'uploadfilefromcomputer') {
        document.getElementById('boxmethod1').style.border = "1px solid #ccc";
        document.getElementById('method1').style.display = '';
        document.getElementById('method1').style.height = '40px';
    }
}
function choose_import_mode(id) {
    if (id == 'importNow') {
        document.getElementById('importrightaway').style.display = '';
        document.getElementById('reportLog').style.display = '';
        document.getElementById('schedule').style.display = 'none';
    }
    if (id == 'scheduleNow') {
        document.getElementById('schedule').style.display = '';
        document.getElementById('importrightaway').style.display = 'none';
        document.getElementById('reportLog').style.display = 'none';
    }
}

function exportexclusion(name, id) {
    var selected_node = document.getElementById(id).checked;
    var export_module_type = document.getElementById('moduletobeexport').value;
    var customposts_type = document.getElementById('export_cust_type').value;
    var taxonomies = document.getElementById('export_taxo_type').value;
    if(selected_node == true)
        var exclusion_status = 'enable';
    else
        var exclusion_status = 'disable';
    var doaction = new Array({'exclusion_status': exclusion_status, 'exclusion_node': name,'export_module':export_module_type,'customposts_type':customposts_type,'taxonomies':taxonomies});
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'UpdateExportExclusion',
            'postdata': doaction,
        },
        type: 'post',
        success: function (response) {
        }
    });
}

//Export check All
function exportselectall(param, group)
{
    var res = new Array();
    var export_module_type = document.getElementById('moduletobeexport').value;
    var result = document.getElementsByClassName(group+'_class');
    var customposts_type = document.getElementById('export_cust_type').value;
    var taxonomies = document.getElementById('export_taxo_type').value;
    var result = document.getElementsByClassName(group+'_class');
    for(var j=0;j<result.length;j++) {
        var name1 = result[j].name;
        res[j] = name1;
    }
    for(var i=0;i<result.length;i++) {
        var ans = result[i].id;
        var check = document.getElementById(ans).checked;
        if(check == true) {
            if(param == 'uncheck')
                document.getElementById(ans).checked = false;
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action': 'UpdateExportExclusion',
                    'eventdoneby': param,
                    'export_module':export_module_type,
                    'cust_posts_type':customposts_type,
                    'taxo_type':taxonomies,
                    'result':res,
                },
                success: function (response) {
//                        alert(response); return false;
                }
            });

        }
        else {
            if(param == 'check')
                document.getElementById(ans).checked = true;
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action': 'UpdateExportExclusion',
                    'eventdoneby': param,
                    'export_module':export_module_type,
                    'cust_posts_type':customposts_type,
                    'taxo_type':taxonomies,
                    'result':res,
                },
                success: function (response) {
//                        alert(response); return false;
                }
            });
        }
    }
}

function addexportfilter(id) {
    if (document.getElementById(id).checked == true) {
        if (id == 'getdataforspecificperiod') {
            document.getElementById('specificperiodexport').style.display = '';
            document.getElementById('periodstartfrom').style.display = '';
            document.getElementById('postdatefrom').style.display = '';
            document.getElementById('periodendto').style.display = '';
            document.getElementById('postdateto').style.display = '';
        }
        else if (id == 'getdatawithspecificstatus') {
            document.getElementById('specificstatusexport').style.display = '';
            document.getElementById('status').style.display = '';
            document.getElementById('specific_status').style.display = '';
        }
        else if (id == 'getdatabyspecificauthors') {
            document.getElementById('specificauthorexport').style.display = '';
            document.getElementById('authors').style.display = '';
            document.getElementById('specific_authors').style.display = '';
        }
        else if (id == 'getdatawithdelimiter') {
            document.getElementById('delimiterstatus').style.display = '';
        }
        else if(id == 'getdatabasedonexclusions') {
            document.getElementById('exclusiongrouplist').style.display = '';
        }
    } else if (document.getElementById(id).checked == false) {
        if (id == 'getdataforspecificperiod') {
            document.getElementById('specificperiodexport').style.display = 'none';
            document.getElementById('periodstartfrom').style.display = 'none';
            document.getElementById('postdatefrom').style.display = 'none';
            document.getElementById('periodendto').style.display = 'none';
            document.getElementById('postdateto').style.display = 'none';
        }
        else if (id == 'getdatawithspecificstatus') {
            document.getElementById('specificstatusexport').style.display = 'none';
            document.getElementById('status').style.display = 'none';
            document.getElementById('specific_status').style.display = 'none';
        }
        else if (id == 'getdatabyspecificauthors') {
            document.getElementById('specificauthorexport').style.display = 'none';
            document.getElementById('authors').style.display = 'none';
            document.getElementById('specific_authors').style.display = 'none';
        }
        else if (id == 'getdatawithdelimiter') {
            document.getElementById('delimiterstatus').style.display = 'none';
        }
        else if(id == 'getdatabasedonexclusions') {
            document.getElementById('exclusiongrouplist').style.display = 'none';
        }
    }
}

function igniteExport() {
    var exclusion_header_list = JSON.parse( "" || "{}");
    var filterOptions = new Array('getdatawithdelimiter', 'getdataforspecificperiod', 'getdatawithspecificstatus', 'getdatabyspecificauthors', 'getdatabasedonexclusions');
    var items = jQuery("form :input").map(function(index, elm) {
        return {id: elm.id, name: elm.name, type:elm.type, value: jQuery(elm).val()};
    });
    jQuery.each(items, function(i, d){
        if(d.name != '' && d.name != null && d.name != '_token' && d.type == 'checkbox') {
            if(jQuery.inArray(d.name, filterOptions) == -1) {
                if (jQuery('#' + d.id).prop( "checked" )) {
                    exclusion_header_list[d.name] = true; //d.type;
                }
            }
        }
    });
    console.log(exclusion_header_list);
    var module = jQuery('#moduletobeexport').val();
    var is_custom_delimiter = false;
    if(jQuery('#getdatawithdelimiter').prop( "checked" )) {
        is_custom_delimiter = true;
    }
    var smack_nonce_key = jQuery('#smack_nonce_key').val();
    var delimiter = jQuery('#postwithdelimiter').val();
    var optional_delimiter = jQuery('#other_delimiter').val();
    var optionalType = jQuery('#optional_type').val();
    var offset = jQuery('#offset').val();
    var limit = jQuery('#limit').val();
    var total_row_count = jQuery('#total_row_count').val();
    //alert(optionalType);
    var is_data_for_specific_period = false;
    if(jQuery('#getdataforspecificperiod').prop( "checked" )) {
        is_data_for_specific_period = true;
    }
    var from_date = jQuery('#postdatefrom').val();
    var to_date = jQuery('#postdateto').val();
    var is_data_for_specific_status = false;
    if(jQuery('#getdatawithspecificstatus').prop( "checked" )) {
        is_data_for_specific_status = true;
    }
    var specific_status = jQuery('#specific_status').val();
    var is_data_for_specific_authors = false;
    if(jQuery('#getdatabyspecificauthors').prop( "checked" )) {
        is_data_for_specific_authors = true;
    }
    var specific_authors = jQuery('#specific_authors').val();
    var is_data_with_specific_exclusions = false;
    if(jQuery('#getdatabasedonexclusions').prop( "checked" )) {
        is_data_with_specific_exclusions = true;
    }
    var conditions = {
        'delimiter': {
            'is_check': is_custom_delimiter,
            'delimiter': delimiter,
            'optional_delimiter': optional_delimiter,
        },
        'specific_period': {
            'is_check': is_data_for_specific_period,
            'from': from_date,
            'to': to_date,
        },
        'specific_status': {
            'is_check': is_data_for_specific_status,
            'status': specific_status,
        },
        'specific_authors': {
            'is_check': is_data_for_specific_authors,
            'author': specific_authors,
        },
    };
    var eventExclusions = {
        'is_check': is_data_with_specific_exclusions,
        'exclusion_headers': exclusion_header_list,
    };
    var fileName = jQuery('#export_filename').val();
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        dataType: "json",
        //async: false,
        data: {
            'action': 'parseDataToExport',
            'nonceKey': smack_nonce_key,
            'module': module,
            'optionalType': optionalType,
            'conditions': conditions,
            'eventExclusions': eventExclusions,
            'fileName': fileName,
            'offset': offset,
            'limit': limit,
        },
        success: function (response) {
            //alert(response.new_offset); return false;
            //var new_offset = parseInt(data.offset) + parseInt(data.limit);
            //jQuery('#proceed_to_export').disabled = true;
            //$("#rbutton'+i+'").attr("disabled","disabled");
            if(response != null) {
                jQuery('input[type="button"]').prop('disabled', true);
                jQuery("a#download_file_link").css('display', '');
                jQuery("#download_file").css('display', '');
                jQuery('#download_file').prop('disabled', false);
                jQuery("a#download_file_link").attr("href", response.exported_file);
                jQuery('#offset').val(response.new_offset);
                if (parseInt(response.new_offset) >= parseInt(response.total_row_count)) {
                    jQuery('#wpwrap').waitMe('hide');
                    return false;
                }
                igniteExport();
            }
            console.log (response);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
}
