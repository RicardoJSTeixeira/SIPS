<?
$ouvidas_Hour = "[" . implode(",", $_POST['rtouvidas']['Hour']) . "]";
$ouvidas_Count = "[" . implode(",", $_POST['rtouvidas']['Count']) . "]";

$declinadas_Hour = "[" . implode(",", $_POST['rtdeclinadas']['Hour']) . "]";
$declinadas_Count = "[" . implode(",", $_POST['rtdeclinadas']['Count']) . "]";

$feitas_Hour = "[" . implode(",", $_POST['rtfeitas']['Hour']) . "]";
$feitas_Count = "[" . implode(",", $_POST['rtfeitas']['Count']) . "]";
?>

<!--Statistics Box Start-->
<div class="grid">
    <div class="grid-title">
        <div class="pull-left">Realtime Graph</div>
        <div class="pull-right">

        </div>
        <div class="clear"></div>   
    </div>

    <!-- Information data -->
    <!--       <div class="information-data">
                <div class="data">
                <p class="date-figures">935</p>
                <p class="date-title">Tikets</p>
            </div>
            <div class="data">
                <p class="date-figures">2316$</p>
                <p class="date-title">Earnings</p>
            </div>
            <div class="data">
                <p class="date-figures">165</p>
                <p class="date-title">Comments</p>
            </div>
            <div class="data data-last">
                <p class="date-figures">95%</p>
                <p class="date-title">Updates</p>
            </div>
            <div class="data">
                <p class="date-figures">165</p>
                <p class="date-title">Views</p>
            </div>
                </div>
            <div class="clear"></div>
    <!-- Information data end -->
    <div class="grid-content overflow">

        <div id="chart3"></div>




    </div>
</div>
<!--Statistics Box END-->


<div class="row-fluid">
    <!--Grid 2  Start-->
    <div class="grid span6">
        <div class="grid-title">
            <div class="pull-left">Campaign Totals</div>
            <div class="pull-right"><!-- <img class='pointer' style='opacity:0.66; margin-top:3px' src='../images/users/icon_refresh_24.png' />--></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <div id="chart1"></div>
            <div class="clear"></div>
        </div>    
    </div>
    <!--Grid 2  END-->

    <!--Grid 2  Start-->
    <div class="grid span6">
        <div class="grid-title">
            <div class="pull-left">Database Status</div>
            <div class="pull-right"></div>
            <div class="clear"></div>
        </div>
        <div class="grid-content">
            <div id="chart2"></div>
            <div class="clear"></div>
        </div>    
    </div>
    <!--Grid 2  END-->
</div>







<script>



    function CampaignTotals(Feitas, Atendidas, NaoAtendidas, Ouvidas, Declinadas)
    {

        var s1 = [Feitas];
        var s2 = [Atendidas];
        var s3 = [NaoAtendidas];
        var s4 = [Ouvidas];
        var s5 = [Declinadas];

        // Can specify a custom tick Array.
        // Ticks should match up one for each y value (category) in the series.
        var ticks = [''];

        var plot1 = $.jqplot('chart1', [s1, s2, s3, s4, s5], {
            // The "seriesDefaults" option is an options object that will
            // be applied to all series in the chart.
            seriesDefaults: {
                renderer: $.jqplot.BarRenderer,
                rendererOptions: {fillToZero: true},
                pointLabels: {show: true}
            },
            // Custom labels for the series are specified with the "label"
            // option on the series option.  Here a series option object
            // is specified for each series.
            series: [
                {label: 'Chamadas Feitas'},
                {label: 'Chamadas Atendidas'},
                {label: 'Chamadas Não Atendidas'},
                {label: 'Mensagens Ouvidas'},
                {label: 'Mensagens Declinadas'}

            ],
            // Show the legend and put it outside the grid, but inside the
            // plot container, shrinking the grid to accomodate the legend.
            // A value of "outside" would not shrink the grid and allow
            // the legend to overflow the container.
            legend: {
                show: true,
                placement: 'outsideGrid'
            },
            axes: {
                // Use a category axis on the x axis and use our custom ticks.
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks

                },
                // Pad the y axis just a little so bars can get close to, but
                // not touch, the grid boundaries.  1.2 is the default padding.
                yaxis: {
                    pad: 1.05,
                    min: 0
                            //tickOptions: {formatString: '$%d'}
                }
            }
        });


    }

    function DatabaseTotals(MSG001, MSG002, MSG003, MSG004, MSG005, MSG006, MSG007, NEW, OUTROS)
    {

        var data = [
            ['Ouviu Msg', MSG001], ['Declinou', MSG002], ['Atendeu mas Declinou', MSG003],
            ['Por Marcar', NEW], ['Outros', OUTROS]
        ];

        var plot2 = jQuery.jqplot('chart2', [data],
                {
                    seriesDefaults: {
                        renderer: jQuery.jqplot.PieRenderer,
                        rendererOptions: {
                            // Turn off filling of slices.
                            fill: false,
                            showDataLabels: true,
                            // Add a margin to seperate the slices.
                            sliceMargin: 6,
                            // stroke the slices with a little thicker line.
                            lineWidth: 4,
                            dataLabels: 'value'
                        }
                    },
                    legend: {show: true, location: 'e'}
                }
        );

    }

    function RealtimeTotals(OuvidasHour, OuvidasCount, DeclinadasHour, DeclinadasCount, FeitasHour, FeitasCount)
    {
        /*	console.log(FeitasCount);
         console.log(DeclinadasCount);
         console.log(OuvidasCount); */




        var plot2 = $.jqplot('chart3', [OuvidasCount, DeclinadasCount, FeitasCount], {
            // Give the plot a title.
            // You can specify options for all axes on the plot at once with
            // the axesDefaults object.  Here, we're using a canvas renderer
            // to draw the axis label which allows rotated text.
            axesDefaults: {
                labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
            },
            seriesDefaults: {
                rendererOptions: {
                    //////
                    // Turn on line smoothing.  By default, a constrained cubic spline
                    // interpolation algorithm is used which will not overshoot or
                    // undershoot any data points.
                    //////
                    smooth: true
                }
            },
            legend: {show: true},
            series: [{label: 'Mensagens Ouvidas'}, {label: 'Mensagens Declinadas'}, {label: 'Chamadas Feitas'}],
            // An axes object holds options for all axes.
            // Allowable axes are xaxis, x2axis, yaxis, y2axis, y3axis, ...
            // Up to 9 y axes are supported.
            axes: {
                // options for each axis are specified in seperate option objects.
                xaxis: {
                    ticks: [[1, "00:00"], [2, "01:00"], [3, "02:00"], [4, "03:00"], [5, "04:00"], [6, "05:00"], [7, "06:00"], [8, "07:00"], [9, "08:00"], [10, "09:00"], [11, "10:00"], [12, "11:00"], [13, "12:00"], [14, "13:00"], [15, "14:00"], [16, "15:00"], [17, "16:00"], [18, "17:00"], [19, "18:00"], [20, "19:00"], [21, "20:00"], [22, "21:00"], [23, "22:00"], [24, "23:00"]],
                    pad: 0
                },
                yaxis: {
                    tickInterval: 1,
                    min: 0
                }
            }
        });
    }


    $(document).ready(function() {

        CampaignTotals(<?= $_POST['totals1']; ?>,<?= $_POST['totals2']; ?>,<?= $_POST['totals3']; ?>,<?= $_POST['totals4']; ?>,<?= $_POST['totals5']; ?>);
        DatabaseTotals(<?= $_POST['dbtotals1']; ?>,<?= $_POST['dbtotals2']; ?>,<?= $_POST['dbtotals3']; ?>,<?= $_POST['dbtotals4']; ?>,<?= $_POST['dbtotals5']; ?>,<?= $_POST['dbtotals6']; ?>,<?= $_POST['dbtotals7']; ?>,<?= $_POST['dbtotals_new']; ?>, <?= $_POST['dbtotals_outros']; ?>);
        RealtimeTotals(<?= $ouvidas_Hour ?>,<?= $ouvidas_Count ?>, <?= $declinadas_Hour ?>, <?= $declinadas_Count ?>, <?= $feitas_Count ?>, <?= $feitas_Count ?>);


    });



</script>