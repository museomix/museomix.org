jQuery(function(e){function t(){wysijaAJAX.popTitle=wysijatrans.previewemail,wysijaAJAX.dataType="json",wysijaAJAX.task="send_preview",jQuery("#campaignstep3").length>0&&(wysijaAJAX.data=jQuery("#campaignstep3").serializeArray(),wysijaAJAX.id=jQuery("#email_id").val()),wysijaAJAX.receiver=jQuery("#preview-receiver").val(),jQuery.WYSIJA_SEND()}function i(){return"function"==typeof saveWYSIJA?saveWYSIJA(function(){t()}):t(),!1}function a(){var t={};return e(".checkbox.checklists").each(function(){e(this).attr("checked")&&(t[e(this).attr("id")]={total:parseInt(e("#"+e(this).attr("id")+"count").val()),title:e(this).attr("alt")})}),t}function s(){var t=a(),i=0,s="";for(var r in t)i+=parseInt(t[r].total),s+=t[r].title+", ";if(s=s.substr(0,s.length-2),void 0!=wysijatrans.alertsend&&!e("#scheduleit").attr("checked")){var n=wysijatrans.alertsend;return n=n.replace("[#]",i),n=n.replace("[#nms]",s),confirm(n)?!0:!1}return void 0!=wysijatrans.ignoreprevious&&e("#ignore_subscribers").attr("checked")?confirm(wysijatrans.ignoreprevious)?!0:!1:!0}function r(){return e("#wysija-send-spamtest").hasClass("disabled")?!1:(WysijaPopup.showLoading(),WysijaPopup.showOverlay(),saveWYSIJA(function(){n()}),void 0)}function n(){wysijaAJAX.popTitle=wysijatrans.previewemail,wysijaAJAX.dataType="json",wysijaAJAX.task="send_spamtest",e.ajax({type:"POST",url:wysijaAJAX.ajaxurl,data:wysijaAJAX,success:function(t){if(WysijaPopup.hideLoading(),t.result.result){e("#wysija-spam-results").attr("href",t.result.urlredirect).fadeIn("slow");var i=parseInt(e("#counttriesleft").html())-1;e("#counttriesleft").html(i),0>=i&&e("#wysija-send-spamtest").addClass("disabled"),WysijaPopup.hideOverlay()}else t.result.notriesleft&&alert(t.result.notriesleft),WysijaPopup.hideOverlay()},error:function(e){alert("Request error not JSON:"+e.responseText),delete wysijaAJAXcallback.onSuccess},dataType:wysijaAJAX.dataType})}e(".action-send-spam-test").click(function(){return tb_show(wysijatrans.processqueue,e(this).attr("href")+"&KeepThis=true&TB_iframe=true&height=618&width=1000",null),tb_showIframe(),!1}),e("#wj-send-preview").click(i),e("#submit-send").click(s),e(document).ready(function(){function t(t){e("#scheduleit").attr("checked")?(e(".schedule-row").show(),e("#submit-send").val(wysijatrans.schedule)):(e(".schedule-row").hide(),e("#submit-send").val(t))}if("function"!=typeof saveWYSIJA){e("#datepicker-day").datepicker({minDate:0,showOn:"focus",dateFormat:"yy-mm-dd"});var i=e("#submit-send").val();t(i),e("#scheduleit").change(function(){t(i)})}}),e("#wysija-send-spamtest").click(r),e("#link-back-step2").click(function(){return e("#hid-redir").attr("value","savelastback"),e("#campaignstep3").submit(),!1})}),window.mailpoet="object"==typeof window.mailpoet?window.mailpoet:{$:{}},function(e,t){"use strict";var i=t.mailpoet,a=i.fn={};a.isGoodFromAddress=function(e){var i=e.split("@")[1];return"undefined"==typeof i||""===i?!1:i.toLowerCase()!==t.location.host.toLowerCase()?!1:!0},a.isGmailAddress=function(e){var t=e.split("@")[1];return"undefined"==typeof t||""===t?!1:"gmail.com"!==t.toLowerCase()?!1:!0}}(jQuery,window),function(e,t){"use strict";var i=t.mailpoet;e(t).load(function(){"object"==typeof e.fn.tooltip&&(i.$.from_email=e("#from_email"),i.$.from_email.tooltip({animation:!0,placement:"bottom",trigger:"manual",html:!0,title:function(){return i.$.from_email.data("message")}}).on({"verifyEmail.mailpoet":function(){if(i.fn.isGoodFromAddress(i.$.from_email.val()))return i.$.from_email.data("message","").tooltip("hide");if("true"===wysijatrans.emailCheck.isGmail)return i.$.from_email.data("message","").tooltip("hide");var e="";return e=i.fn.isGmailAddress(i.$.from_email.val())?wysijatrans.emailCheck.gmailText:wysijatrans.emailCheck.text,i.$.from_email.is(":visible")?i.$.from_email.data("message",e).tooltip("show"):i.$.from_email.tooltip("hide")},keyup:function(){i.$.from_email.trigger("verifyEmail.mailpoet")}}).trigger("verifyEmail.mailpoet"))})}(jQuery,window);