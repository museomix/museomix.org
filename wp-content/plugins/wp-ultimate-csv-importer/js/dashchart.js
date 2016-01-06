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

/*
function linechart()
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
	/*	var line = new Morris.Line({
		  // ID of the element in which to draw the chart.
			  element: 'lineStats',
			  // Chart data records -- each entry in this array corresponds to a point on
			  // the chart.
			  data: [
			    { year: '2015-02 post', No: 20 },
			    { year: '2010-03 users', No: 10 },
			    { year: '2012-04 page', No: 25 },
			    { year: '2013-05 custompost', No: 45 },
			    { year: '2011-06 eshop', No: 50 }
			  ],
			  // The name of the data record attribute that contains x-values.
			  xkey: 'year',
			  xLabels: "month",
			  // A list of names of data record attributes that contain y-values.
			  ykeys: ['No'],
			  // Labels for the ykeys -- will be displayed when you hover over the
			  // chart.
			  labels: ['No']
		});
		function formatDate(myDate){
		var m_names = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

		var d = new Date(myDate);
		
		var curr_month = d.getMonth();
		//var curr_year = d.getFullYear();
		//return (m_names[curr_month] + "-" + curr_year);
		return (m_names[curr_month]);
	}
	
	new Morris.Line({
		element: 'financial-year-sales-graph',
		data: [
			{ month: '2013-07', sales: 52325 },
			{ month: '2013-08', sales: 65432 },
			{ month: '2013-09', sales: 52125 },
			{ month: '2013-10', sales: 23265 },
			{ month: '2013-11', sales: 25125 },
			{ month: '2013-12', sales: 63256 },
			{ month: '2014-01', sales: 52365 },
			{ month: '2014-02', sales: 65954 },
			{ month: '2014-03', sales: 55255 },
			{ month: '2014-04', sales: 66236 },
			{ month: '2014-05', sales: 52369 },
			{ month: '2014-06', sales: 85214 }
		],
		// The name of the data record attribute that contains x-values.
		xkey: 'month',
		// A list of names of data record attributes that contain y-values.
		ykeys: ['sales'],
		// Labels for the ykeys -- will be displayed when you hover over the
		// chart.
		labels: ['Sales'],
		xLabelFormat: function(str){
			return formatDate(str);
		},
		preUnits: '$'
	});

	}
});
}

function pieStats()
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
	// Removed the val[2] for comments (smackcoders)
         var line =  [val[0],val[1],val[3],val[4],val[5]]; 
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
