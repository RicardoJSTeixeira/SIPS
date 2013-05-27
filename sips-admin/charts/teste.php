<html>
<head>

<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="../../jqplot/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="../../jqplot/jquery.jqplot.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../jqplot/jquery.jqplot.css" />


<script type="text/javascript" src="../../jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="../../jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="../../jqplot/plugins/jqplot.pointLabels.min.js"></script>

<script type="text/javascript" src="../../jqplot/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="../../jqplot/plugins/jqplot.donutRenderer.min.js"></script>

<script type="text/javascript">

function dochart() {
    // For horizontal bar charts, x an y values must will be "flipped"
    // from their vertical bar counterpart.
    var plot2 = $.jqplot('chart1', [
        [[2,1], [4,2], [6,3], [3,4]],
        [[5,1], [1,2], [3,3], [4,4]],
        [[4,1], [7,2], [1,3], [2,4]]], {
        seriesDefaults: {
            renderer:$.jqplot.BarRenderer,
            // Show point labels to the right ('e'ast) of each bar.
            // edgeTolerance of -15 allows labels flow outside the grid
            // up to 15 pixels.  If they flow out more than that, they
            // will be hidden.
            pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
            // Rotate the bar shadow as if bar is lit from top right.
            shadowAngle: 135,
            // Here's where we tell the chart it is oriented horizontally.
            rendererOptions: {
                barDirection: 'horizontal'
            }
        },
        axes: {
            yaxis: {
                renderer: $.jqplot.CategoryAxisRenderer
            }
        }
    });
}

function pie_chamadas_resumo(TargetDiv, MinDate, MaxDate, Campaign, Group, User) {
	
	TargetDiv = 'div1';
	MinDate = '2012-01-30';
	MaxDate = '2012-04-03';
	Campaign = 'ZON001,MEO001,TESTCAMP';
	Group = '';
	User = '';
	
	$.ajax({
			
			type: "POST",
			dataType: "json",
			url: "requests.php",
			data: {action: "log-chamadas-resumo", MinDate: MinDate, MaxDate: MaxDate, Campaign: Campaign, Group: Group},
			error: function()
			{
				alert("Ocorreu um Erro.");
			},
			success: function(data)	
			{
			
				var plot2 = $.jqplot('chart1', [data.aaData], {
        seriesDefaults: {
            renderer:$.jqplot.BarRenderer,
            // Show point labels to the right ('e'ast) of each bar.
            // edgeTolerance of -15 allows labels flow outside the grid
            // up to 15 pixels.  If they flow out more than that, they
            // will be hidden.
            pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
            // Rotate the bar shadow as if bar is lit from top right.
            shadowAngle: 135,
            // Here's where we tell the chart it is oriented horizontally.
            rendererOptions: {
                barDirection: 'vertical'
            }
        },
        axes: {
            yaxis: {
                renderer: $.jqplot.CategoryAxisRenderer
            }
        }
    });
				
			}
		});
	
	
	
}



</script>
</head>
<body>

<input type='button' onclick='dochart();' value='hardcoded' />
<input type='button' onclick='pie_chamadas_resumo();' value='dinamico' />

<div id="chart1" style="height:400px;width:300px; "></div>

<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>