jQuery( document ).ready(function() {
ultimatecsv_piechart();
ultimatecsv_linechart();
});

function ultimatecsv_piechart()
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
                document.getElementById('ultimatecsv_pieStats').innerHTML = "<h2 style='color: red;text-align: center;padding-top: 100px;' >No Imports Yet</h2>";
                return false;
                }
                Morris.Donut({
                        element: 'ultimatecsv_pieStats',
                        data: val//[
                                //{label: val[0][0], value: value[0][1]}
                                //{label: "page", value: 30},
                                //{label: "custompost", value: 20}
                        //]
                });
        }
});
}

function ultimatecsv_linechart() {
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
                        element: 'ultimatecsv_lineStats',
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

/*function pieStats()
{
jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
                    'action'   : 'firstchart',
                    'postdata' : 'firstchartdata',
                },
          dataType: 'json',
          cache: false,
          success: function(data) {
	var browser = JSON.parse(data);
		if (browser['label'] == 'No Imports Yet') {
		document.getElementById('pieStats').innerHTML = "<h2 style='color: red;text-align: center;padding-top: 100px;' >No Imports Yet</h2>";
		return false;
		}
           
              jQuery('#pieStats').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
 }
            }
        },
        series: [{
            type: 'pie',
            name: 'overall statistics',
          //  data: JSON.parse(data),
         data: browser
        }]
    });
}
        });
}
function lineStats()
{
jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
                    'action'   : 'secondchart',
                    'postdata' : 'secondchartdata',
                },
          dataType: 'json',
          cache: false,
         success: function(data) {
         var val = JSON.parse(data);
         var line =  [val[0],val[1],val[2],val[3],val[4],val[5]]; 
         jQuery('#lineStats').highcharts({
            title: {
                text: '',
                x: -5 //center
            },
            subtitle: {
 text: '',
                x: -5
            },
            xAxis: {
                categories:val.cat 
            },
            yAxis: {
                title: {
                text: 'Import (Nos)'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ' Nos'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series:line   });
    }
            });
}
*/
  
