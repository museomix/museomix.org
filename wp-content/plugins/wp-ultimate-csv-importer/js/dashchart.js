jQuery( document ).ready(function() {
var get_module = document.getElementById('checkmodule').value;
if(get_module == 'dashboard') {
	piechart();
	linechart();
}
});
function piechart()
{
jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
                    'action'   : 'firstultimatecsvchart',
                    'postdata' : 'firstchartdata',
                },
          dataType: 'json',
          cache: false,
          success: function(data) {
                var val = JSON.parse(data);
		if (val['label'] == 'No Imports Yet') {
                document.getElementById('pieStats').innerHTML = "<h2 style='color: red;text-align: center;padding-top: 100px;' >No Imports Yet</h2>";
                return false;
                }
                Morris.Donut({
                        element: 'pieStats',
                        data: val//[
                                //{label: val[0][0], value: value[0][1]}
                                //{label: "page", value: 30},
                                //{label: "custompost", value: 20}
                        //]
		});
        }
});
}

function linechart() {
jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
                    'action'   : 'secondultimatecsvchart',
                    'postdata' : 'secondchartdata',
                },
          dataType: 'json',
          cache: false,
          success: function(result) {
                console.log(result);
                var val = JSON.parse(result);
                var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                 Morris.Line({
			element: 'lineStats',
			data   : val,
			xkey: 'year',
			ykeys: ['post', 'page','custompost','users','eshop'],
			labels: ['post', 'page','custompost','users','eshop'],
			lineColors:['gray','red','blue','black','orange'],
			xLabelFormat: function(x) { // <--- x.getMonth() returns valid index
				var month = months[x.getMonth()];
				return month;
			},
  			dateFormat: function(x) {
    				var month = months[new Date(x).getMonth()];
    				return month;
  			},

                });
        }
});
}

 
