<?php
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");
?>

<?
$wallboard = $_GET['wallboard'];

//$query = mysql_query("SELECT template FROM sips_wallboards WHERE wallboard='$wallboard' GROUP BY template",$link) or die(mysql_error());

$row = mysql_fetch_row($query);
$template = $row[0];

if ($template == '0') {
    $autoOpen_edit_scheme = "true";
} else {
    $autoOpen_edit_scheme = "false";
}
?>


<script language="javascript" type="text/javascript" src="../../jquery/jqplot/jquery.jqplot.min.js"></script>
<script language="javascript" type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../jquery/jqplot/jquery.jqplot.css" />

<div class="cc-mstyle">
    <table>                                         
        <tr>
            <td id="icon32"><img src='../../images/icons/images_32.png' /></td>
            <td id='submenu-title' style='min-width:210px'> Configuração de Wallboards |</td>
            <td style='text-align:left; width:155px;'><table id="edit-scheme" style="cursor:pointer"><tr><td style="width:20px"><img src="../../images/icons/chart_curve_16.png"></td><td style="text-align:left;">Alterar Esquema</td></tr></table></td>
            <td style='text-align:left; width:155px;'></td>
            <td></td>

        </tr>
    </table>
</div>


<div id='work-area' style="min-width: 900px; min-height: 600px;">
    <br>    



    <div class="wb-canvas">
        <ul id="wb-grid">



        </ul>
    </div>




    <div id="dialog-edit-scheme" style="display:none">

        <ol id="s-edit-scheme">
            <li id="scheme1"><div class="fleft clearfix scheme-single"></div><div class="fright scheme-single"></div></li>
            <li id="scheme2"><div class="fleft clearfix scheme-double"></div><div class="fright scheme-double"></div><div class="fleft scheme-single"></div></li>
            <li id="scheme3"><div class="fleft clearfix scheme-double"></div><div class="fleft clearfix scheme-double"></div><div class="fleft clearfix scheme-double"></div><div class="fleft clearfix scheme-double"></div></li>
            <li id="scheme4"><div class="fleft clearfix scheme-triple"></div><div class="fleft clearfix scheme-triple"></div><div class="fleft clearfix scheme-triple"></div><div class="fleft clearfix scheme-single"></div></li>
            <li id="scheme5"><div class="fleft clearfix scheme-triple"></div><div class="fleft clearfix scheme-triple"></div><div class="fleft clearfix scheme-triple"></div><div class="fleft clearfix scheme-triple"></div><div class="fleft clearfix scheme-triple"></div><div class="fleft clearfix scheme-triple"></div></li>
            <li id="scheme6"><div class="fleft clearfix scheme-9single"></div><div class="fleft clearfix scheme-9single"></div><div class="fleft clearfix scheme-9single"></div></li>
            <li id="scheme7"><div class="fleft clearfix scheme-9single"></div><div class="fleft clearfix scheme-9single"></div><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div></li>
            <li id="scheme8"><div class="fleft clearfix scheme-9single"></div><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div></li>
            <li id="scheme9"><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div><div class="fleft clearfix scheme-9double"></div></li>
            <li id="scheme10"><div class="fleft clearfix scheme-9single"></div><div class="fleft clearfix scheme-9single"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div></li>
            <li id="scheme11"><div class="fleft clearfix scheme-9single"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div></li>
            <li id="scheme12"><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div><div class="fleft clearfix scheme-9triple"></div></li>
        </ol>


    </div> 

    <div id="dialog-new-graph" style="display:none">

        <div class="wizard-div" id="wizard-index">
            <button class="wizard-nav" id="b-rsl-chamadas">Feedbacks por Campanha</button>
        </div>

        <div class="wizard-div wizard-div-hidden" id="wizard-div-campaigns">
            <select class="select-multiple-dialog" multiple="multiple" id="wizard-campaigns"></select> <button class="wizard-nav button-next" id="b-next-campaings">Seguinte</button>
        </div>

        <div class="wizard-div wizard-div-hidden" id="wizard-div-feedbacks">
            <select id="wizard-feedbacks"></select> <button class="wizard-nav button-next" id="b-next-feedbacks">Seguinte</button>
        </div>

        <div class="wizard-div wizard-div-hidden" id="wizard-div-time">
            <select id="wizard-time"><option value="1hour">1 Hora</option><option value="4hour">4 Horas</option><option value="8hour">8 Horas</option><option value="1day">1 Dia</option></select> <button class="wizard-nav button-next" id="b-next-time">Seguinte</button>
        </div>

        <div class="wizard-div wizard-div-hidden" id="wizard-div-refresh">
            <select id="wizard-refresh"><option value="5sec">5 Segundos</option><option value="10sec">10 Segundos</option><option value="15sec">15 Segundos</option><option value="30sec">30 Segundos</option><option value="1min">1 Minuto</option><option value="5min">5 Minutos</option></select> <button class="wizard-nav button-next" id="b-next-refresh">Seguinte</button>
        </div>



        <div id="wizard-navigation-bar">
            <button id="b-index"> < Voltar ao Inicio</button>
        </div>


    </div>

    <div id="dialog-confirm-edit-scheme" style="display:none">
        Tem a certeza que quer alterar o esquema? Todas as definições referentes ao esquema anterior serão perdidas!
    </div>	


    <br>
</div>
<style>
    /* main window */
    .wb-canvas { width:100%; height:100%; margin:0 auto; min-width: 950px; min-height: 600px; padding:0px 0px 0px 65px; } 
    .wb-canvas li { border: 1px solid black; margin: 5px 5px 5px 5px; padding: 1px; float: left; width: 270px; height: 170px; font-size: 4em; text-align: center; font-size:12px; font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif; }

    .wb-grid-9long { border: 1px solid black; margin: 5px 5px 5px 5px; padding: 1px; float: left; width: 838px !important; height: 170px; font-size: 4em; text-align: center; font-size:12px; font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif; }
    .wb-grid-6single { border: 1px solid black; margin: 5px 5px 5px 5px; padding: 1px; float: left; width: 412px !important; height: 170px; font-size: 4em; text-align: center; font-size:12px; font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif; }
    .wb-grid-6square { border: 1px solid black; margin: 5px 5px 5px 5px; padding: 1px; float: left; width: 270px !important; height: 262px !important; font-size: 4em; text-align: center; font-size:12px; font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif; }

    .wb-grid-6squarelong { border: 1px solid black; margin: 5px 5px 5px 5px; padding: 1px; float: left; width: 838px !important; height: 262px !important; font-size: 4em; text-align: center; font-size:12px; font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif; }

    .wb-grid-4square { border: 1px solid black; margin: 5px 5px 5px 5px; padding: 1px; float: left; width: 412px !important; height: 262px !important; font-size: 4em; text-align: center; font-size:12px; font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif; }

    .button-next { float: right; }
    .wizard-div {min-height: 365px;}
    .wizard-div-hidden { display:none; }
    .wizard-div-visible { display:inline; }

    .select-multiple-dialog { height:158px; }

    /* template dialog */  
    #s-edit-scheme .ui-selecting { background: #FECA40; }
    #s-edit-scheme .ui-selected { background: #F39814; color: white; }
    #s-edit-scheme { list-style-type: none; margin: 0; padding: 0; width: 450px; }
    #s-edit-scheme li { margin: 3px; padding: 1px; float: left; width: 140px; height: 80px; text-align: center; }
    #s-edit-scheme div { border: 1px solid black; }

    .scheme-single { height:32px; width:132px; margin: 3px 3px 3px 3px; }
    .scheme-double { height:32px; width: 62px; margin: 3px 3px 3px 3px; }
    .scheme-triple { height:32px; width: 40px; margin: 3px 1px 3px 3px; }

    .scheme-9single {height: 20px; width: 132px;  margin: 3px 3px 1px 3px; }
    .scheme-9double {height: 20px; width: 62px;  margin: 3px 3px 1px 3px; }
    .scheme-9triple {height: 20px; width: 40px;  margin: 3px 1px 1px 3px; }

    .t-inner-li { width:20%; margin:0 auto; cursor:pointer; }


    /* random css */
    .fright { float:right; }
    .fleft { float:left; }
    .clearfix:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }
</style>


<script>
    var SchemeInUse = '<? echo $template; ?>';
    var Wallboard = '<? echo $wallboard; ?>';
    var CurrentDiv = '';

// EDIÇÃO DE TEMPLATES /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $(function() {
        $("#wb-grid").sortable({cursor: "move", grid: [20, 20], tolerance: "pointer"});
    });
    $(function() {
        $("#s-edit-scheme").selectable();
    });

    $('#edit-scheme').click(function() {
        $("#dialog-edit-scheme").dialog("open");
    });

    $("#dialog-edit-scheme").dialog({
        title: ' <span style="font-size:13px; color:black">Escolha do Esquema para o Wallboard</span> ',
        autoOpen: <? echo $autoOpen_edit_scheme; ?>,
        height: 500,
        width: 500,
        resizable: false,
        buttons: {
            "Ok": function()
            {
                $(this).dialog("close");
                $('#s-edit-scheme').each(function() {
                    $(this).find('li').each(function() {
                        var current = $(this);
                        if (current.hasClass("ui-selected")) {
                            if (current.attr("id") != SchemeInUse) {
                                $("#dialog-confirm-edit-scheme").dialog("open");
                            } else {
                                SchemeInUse = current.attr("id");
                            }
                        }
                    })
                })
            },
            "Cancelar": function() {
                $(this).dialog("close");
            }
        },
        open: function() {
            $("button").blur();
        }
    });

    $("#dialog-confirm-edit-scheme").dialog({
        title: ' <span style="font-size:13px; color:black">Alerta</span> ',
        autoOpen: false,
        height: 200,
        width: 200,
        resizable: false,
        buttons: {
            "Ok": function()
            {
                $(this).dialog("close");
                $('#s-edit-scheme').each(function() {
                    $(this).find('li').each(function() {
                        var current = $(this);
                        if (current.hasClass("ui-selected")) {
                            SchemeInUse = current.attr("id");
                        }
                    })
                })
                $.ajax({
                    type: "POST",
                    url: "_requests.php",
                    data: {action: "change-wallboard-scheme", sent_wallboard: Wallboard, sent_template: SchemeInUse},
                    success: function(data) {
                    }
                });
                UpdateGrid()
            },
            "Cancelar": function() {
                $(this).dialog("close");
                $("#dialog-edit-scheme").dialog("open");
            }
        },
        open: function() {
            $("button").blur();
        }
    });

    function AlterScheme() {
        $('#s-edit-scheme').each(function() {
            $(this).find('li').each(function() {
                var current = $(this);

                if (current.hasClass("ui-selected")) {
                    if (current.attr("id") != SchemeInUse) {
                        $("#dialog-confirm-edit-scheme").dialog("open");
                    } else {
                        SchemeInUse = current.attr("id");
                    }
                }
            })
        })
    }

    function UpdateGrid() {
        switch (SchemeInUse) {
            case "scheme1":
                $('#wb-grid').html("<li class='wb-grid-6squarelong'><div id='div1'><table class='t-inner-li'><tr><td><img src='../../images/icons/layer_add_16.png'></div></td><td>Adicionar Gráfico</td></tr></table></div></li><li class='wb-grid-6squarelong'><div id='div2'><table class='t-inner-li'><tr><td><img src='../../images/icons/layer_add_16.png'></div></td><td>Adicionar Gráfico</td></tr></table></div></li>");
                break;
            case "scheme2":
                $('#wb-grid').html("<li class='wb-grid-4square'></li><li class='wb-grid-4square'></li><li class='wb-grid-6squarelong'></li>");
                break;
            case "scheme3":
                $('#wb-grid').html("<li class='wb-grid-4square'></li><li class='wb-grid-4square'></li><li class='wb-grid-4square'></li><li class='wb-grid-4square'></li>");
                break;
            case "scheme4":
                $('#wb-grid').html("<li class='wb-grid-6square'></li><li class='wb-grid-6square'></li><li class='wb-grid-6square'></li><li class='wb-grid-6squarelong'></li>");
                break;
            case "scheme5":
                $('#wb-grid').html("<li class='wb-grid-6square'></li><li class='wb-grid-6square'></li><li class='wb-grid-6square'></li><li class='wb-grid-6square'></li><li class='wb-grid-6square'></li><li class='wb-grid-6square'></li>");
                break;
            case "scheme6":
                $('#wb-grid').html("<li class='wb-grid-9long'></li><li class='wb-grid-9long'></li><li class='wb-grid-9long'></li>");
                break;
            case "scheme7":
                $('#wb-grid').html("<li class='wb-grid-9long'></li><li class='wb-grid-9long'></li><li class='wb-grid-6single'></li><li class='wb-grid-6single'></li>");
                break;
            case "scheme8":
                $('#wb-grid').html("<li class='wb-grid-9long'></li><li class='wb-grid-6single'></li><li class='wb-grid-6single'></li><li class='wb-grid-6single'></li><li class='wb-grid-6single'></li>");
                break;
            case "scheme9":
                $('#wb-grid').html("<li class='wb-grid-6single'></li><li class='wb-grid-6single'></li><li class='wb-grid-6single'></li><li class='wb-grid-6single'></li><li class='wb-grid-6single'></li><li class='wb-grid-6single'></li>");
                break;
            case "scheme10":
                $('#wb-grid').html("<li class='wb-grid-9long'></li><li class='wb-grid-9long'></li><li></li><li></li><li></li>");
                break;
            case "scheme11":
                $('#wb-grid').html("<li class='wb-grid-9long'></li><li></li><li></li><li></li><li></li><li></li><li></li>");
                break;
            case "scheme12":
                $('#wb-grid').html("<li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>");
                break;
        }
    }

    $('.t-inner-li').live("click", function() {

        CurrentDiv = $(this).parent().attr("id");
        alert(CurrentDiv);
        $("#dialog-new-graph").dialog("open");
        $(".wizard-div").hide();
        $("#wizard-index").show();

    })


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// WIZARD DE GRAFICOS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $(".wizard-nav").click(function() {
        $(".wizard-div").hide();
    })

    $("#b-index").button().click(function() {
        $(".wizard-div").hide();
        $("#wizard-index").show();
    });
    $("#b-rsl-chamadas").button().click(function() {
        WizardShowCampaigns();
    });
    $("#b-next-campaings").button().click(function() {
        var sType = null;
        if ($('#wizard-campaigns option:selected').length == 1) {
            sType = "multiple";
        } else {
            sType = "single";
        }
        WizardShowFeedbacks(sType);
    });
    $("#b-next-feedbacks").button().click(function() {
        $("#wizard-div-time").show();
    });
    $("#b-next-feedbacks").button().click(function() {
        $("#wizard-div-time").show();
    });
    $("#b-next-time").button().click(function() {
        $("#wizard-div-refresh").show();
    });
    $("#b-next-refresh").button().click(function() {
        BuildGraph_FeedsPerCamps();
    });

    $('#new-graph').click(function() {
        $("#dialog-new-graph").dialog("open");
    });

    $("#dialog-new-graph").dialog({
        title: ' <span style="font-size:13px; color:black">Novo Gráfico</span> ',
        autoOpen: false,
        height: 500,
        width: 500,
        resizable: false,
        buttons: {
            "Fechar": function()
            {
                $(this).dialog("close");
            }

        },
        open: function() {
            $("button").blur();
        }
    });


    function WizardShowCampaigns()
    {
        $("#wizard-div-campaigns").show();

        var CampaignOptions = null;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "_requests.php",
            data: {action: "wizard-show-campaigns"},
            success: function(data) {
                var id = data.id;
                var name = data.name;
                $('#wizard-campaigns').empty();
                for (i = 0; i < id.length; i++) {
                    $('#wizard-campaigns').append("<option value='" + id[i] + "'>" + name[i] + "</option>")
                }
            }
        });
    }

    function WizardShowFeedbacks(sType)
    {
        if (sType == "single") {

            $('#wizard-feedbacks').removeProp("multiple");
            $('#wizard-feedbacks').removeClass("select-multiple-dialog");
            var multiple_campaigns = new Array();
            $('#wizard-campaigns option:selected').each(function() {
                multiple_campaigns.push($(this).val());
            })
        }
        else {
            $('#wizard-feedbacks').prop("multiple", "multiple");
            $('#wizard-feedbacks').addClass("select-multiple-dialog");
            var campaign_id = $('#wizard-campaigns option:selected').val();
        }
        $("#wizard-div-feedbacks").show();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "_requests.php",
            data: {action: "wizard-show-feedbacks", sent_campaign_id: campaign_id, sent_multiple_campaigns: multiple_campaigns, sent_type: sType},
            success: function(data) {
                var id = data.id;
                var name = data.name;
                $('#wizard-feedbacks').empty();
                for (i = 0; i < id.length; i++) {
                    $('#wizard-feedbacks').append("<option value='" + id[i] + "'>" + name[i] + "</option>")
                }
            }
        });
    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// CONTRUÇÃO DE GRÁFICOS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function BuildGraph_FeedsPerCamps() {

        if ($('#wizard-campaigns option:selected').length > 1) {

            var campaigns = new Array();
            $('#wizard-campaigns option:selected').each(function() {
                campaigns.push($(this).val());
            })
            var feedbacks = $('#wizard-feedbacks option:selected').val();

        } else {

            var feedbacks = new Array();
            $('#wizard-feedbacks option:selected').each(function() {
                feedbacks.push($(this).val());
            })
            var campaigns = $('#wizard-campaigns option:selected').val();

        }

        var time_int = $('#wizard-time option:selected').val();
        var refresh_int = $('#wizard-refresh option:selected').val();


        /*   alert(campaigns);
         alert(feedbacks);
         alert(time_int);
         alert(refresh_int);   */




        $.ajax({
            type: "POST",
            dataType: "json",
            url: "_requests.php",
            async: false,
            data: {action: "build_feedspercamps", sent_campaigns: campaigns, sent_feedbacks: feedbacks, sent_time_int: time_int},
            success: function(data) {

                var plot1 = $.jqplot('plot-test', [data.series], {
                    seriesDefaults: {
                        renderer: $.jqplot.BarRenderer,
                        rendererOptions: {fillToZero: true}
                    },
                    axesDefaults: {min: 0, max: 100},
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.CategoryAxisRenderer,
                            ticks: data.ticks

                        }
                    }
                });



            }
        });
    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $(document).ready(function() {
        UpdateGrid();
    });
</script>




<?php
#FOOTER
$footer = ROOT . "ini/footer.php";
require($footer);
?>