function generateWidgetTemplate(e){void 0===e?window.parent.WysijaPopup.cancel():(wysijaAJAX.task="wysija_form_generate_template",wysijaAJAX.wysijaData=Base64.encode(Object.toJSON(e).gsub('\\"','"').gsub('"[{',"[{").gsub('}]"',"}]")),new Ajax.Request(wysijaAJAX.ajaxurl,{method:"post",parameters:wysijaAJAX,onSuccess:function(e){window.parent.WysijaPopup.success(e.responseJSON.result)},onFailure:function(){window.parent.WysijaPopup.cancel()}}))}function saveData(e){switch(void 0!==e.label&&(e.label=window.parent.WysijaForm.encodeHtmlValue(e.label)),e.type){case"input":break;case"submit":break;case"html":e.text=window.parent.WysijaForm.encodeHtmlValue(e.text);break;case"text":e.text=window.parent.WysijaForm.encodeHtmlValue(e.text);break;case"list":var t=$("lists-selection").select("input");if(0===t.length)throw new Error(window.parent.wysijatrans.list_cannot_be_empty);var i=[];t.each(function(e){i.push({list_id:+e.readAttribute("data-list"),is_checked:+e.checked})}),e.values=i}return delete e.name,delete e.field,delete e.type,delete e.submit,e}function hideError(){$("widget-settings-error").update("").hide(),window.parent.WysijaPopup.setDimensions()}function displayError(e){$("widget-settings-error").update(e.message).show(),window.parent.WysijaPopup.setDimensions()}function setupSortableList(){if($$("ul.sortable").length>0){var e=$$(".sortable").first();Sortable.create(e,{tag:"li",scroll:window,handle:"handle",constraint:"vertical"})}}function setAvailableLists(){var e=$("lists-selection").select("input").map(function(e){return $(e).readAttribute("data-list")});$("lists-available").select("option").each(function(t){e.include(t.value)&&t.remove()}),$("lists-add-container")[0===$("lists-available").length?"hide":"show"]()}document.observe("dom:loaded",function(){switch($("widget-settings-form").type.value){case"list":$("lists-add-container").on("click","a.add",function(){if($("lists-available").selectedIndex>=0){var e={name:$("lists-available").options[$("lists-available").selectedIndex].innerHTML,list_id:$F("lists-available")},t=new Template($("list-selection-template").innerHTML);$("lists-selection").insert(t.evaluate(e)),setupSortableList(),setAvailableLists(),window.parent.WysijaPopup.setDimensions()}return!1}),$("lists-selection").on("click","a.remove",function(e,t){$("lists-available").insert(new Element("option",{value:$(t).previous("input").readAttribute("data-list")}).update($(t).previous("label").innerHTML)),$(t).up("li").remove(),setupSortableList(),setAvailableLists(),window.parent.WysijaPopup.setDimensions()}),setupSortableList(),setAvailableLists()}$("widget-settings-submit").observe("click",function(e){e.preventDefault(),hideError();var t=$H(),i=$("widget-settings-form").serialize(!0);t.set("type",i.type),t.set("field",i.field),t.set("name",i.name);try{t.set("params",saveData(i)),generateWidgetTemplate(t)}catch(e){displayError(e)}return!1})});