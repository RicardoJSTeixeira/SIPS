
<?php
require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
          ${$key} = $value;
}
foreach ($_GET as $key => $value) {
          ${$key} = $value;
}
?>

<!DOCTYPE HTML>
<html>

          <head>
                    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
                    <title>SIPS</title> 

                    <link type="text/css" rel="stylesheet" href="/bootstrap/css/jquery.jgrowl.css">
                    <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css">
                    <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
                    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
                    <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
                    <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />


                    <script type="text/javascript" src="/jquery/jquery-1.9.1.js"></script>
                    <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.10.2.custom.min.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/jquery.flot.min.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/jquery.flot.pie.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/jquery.flot.resize.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/jquery.flot.orderBars.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/jquery.jgrowl.js"></script>
                    <style>
                              .PanelWB{position:absolute;
                                       -webkit-border-radius: 3px;
                                       -moz-border-radius: 3px;
                                       border-radius: 3px;
                                       box-shadow: 5px 5px 13px -6px rgba(0, 0, 0, 0.3) ;
                              }
                              .ui-widget-header{background: none !important; background-color: #F3F3F3!important;}

                    </style>
          </head>    

          <body>



                    <div id="MainLayout" class="ui-widget-header" style="position:absolute;width:100%;height:100%"></div>

                    <div id="jGrowl" class="top-right jGrowl" ><div class="jGrowl-notification" ></div></div>








                    <script language="javascript">




                              var wbes;
                              var layout;

                              //      wbes
                              //0      id,
                              // 1       name,
                              //   2      posX,
                              //     3     posY, 
                              //       4   width,
                              //       5    height, 
                              //       6    layout_Id,
                              //      7      query_text, 
                              //       8     opcao_query,
                              //       9      update_time, 
                              //       10      graph_type]);



                              /* se vieres nos grupos de Inbound
                               mesmo nas ultimas definicoes
                               está la para definires o SL1 e SL2
                               essa definição é em segundos
                               e o que isso quer dizer é o seguinte:
                               imagina que SL1 é 30 e SL2 é 90
                               o valor a apresentar é uma percentagem das chamadas atendidas em 30 segundos (para SL1) e outra percentagem das atendidas em 90 segundos (para SL2)
                               se vires no relatorio em tempo real
                               quando seleccionas so uma campanha, por exemplo na acustica medica
                               aparecem te aqueles blocos
                               e la ta os SL
                               ja com os valores calculados
                               ok, e no grafico é para aparecer q dados?
                               as percentagens
                               tipo poes assim:
                               SLA 1 (30 seconds) - 86%
                               SLA 2 (90 seconds) - 97%
                               */











                              $(document).ready(function() {
                                        var parameters = window.name.split(";");
                                        var windwidth = +parameters[1]; //parameter;
                                        var windheight = +parameters[2]; //parameter;
                                        layout = +parameters[0]; //parameter
                                        document.getElementById("MainLayout").style.background = "#F5F5F5";
                                        $("[data-t=tooltip]").tooltip({placement: "left", html: true});
                                        $.post("Requests.php", {action: "wbe", layout_Id: layout},
                                        function(data)
                                        {
                                                  wbes = [];
                                                  $.each(data, function(index, value) {
                                                            wbes.push([this.id, this.name, this.posX, this.posY, this.width, this.height, this.layout_Id, this.query_text, this.opcao_query, this.update_time, this.graph_type]);
                                                  });
                                                  var i = 0;
                                                  var temp_window = $(window);
                                                  $.each(wbes, function(index, value) {
                                                            var left = (wbes[i][2] * temp_window.width()) / windwidth;
                                                            var top = (wbes[i][3] * temp_window.height()) / windheight;
                                                            var width = (wbes[i][4] * temp_window.width()) / windwidth;
                                                            var height = (wbes[i][5] * temp_window.height()) / windheight;
                                                            $("#MainLayout").append($("<div>").addClass("PanelWB ui-widget-content").attr("style", "position: absolute;    left:" + left + "px;top:" + top + "px; width:" + width + "px;height:" + height + "px;").attr("id", wbes[i][0] + "Main").draggable({containment: '#MainLayout'})
                                                                    .append($("<div>").addClass("grid-title")
                                                                    .append($("<div>").addClass("pull-left").text(wbes[i][1]))
                                                                    .append($("<div>").addClass("pull-right").text(wbes[i][8])))
                                                                    .append($("<div>").addClass("grid-content")
                                                                    .append($("<div>").attr("id", wbes[i][0] + "WBE").attr("style", "width:" + (width - 20) + "px;height:" + (height - 75) + "px;padding: 0px;").attr("data-t", "tooltip").attr("title", "Tempo de Actualização: " + (wbes[i][9] / 1000) + " seg."))));
                                                            if (wbes[i][10] == 1)//update
                                                            {
                                                                      plot_update(wbes[i]);
                                                            }
                                                            if (wbes[i][10] == 2)//bar
                                                            {
                                                                      plot_bar(wbes[i]);
                                                            }
                                                            if (wbes[i][10] == 3)//pie
                                                            {
                                                                      plot_pie(wbes[i]);
                                                            }
                                                            if (wbes[i][10] == 4)//Inbound stuff
                                                            {
                                                                      inbound_wallboard(wbes[i]);
                                                            }
                                                            i++;
                                                  });
                                        }, "json");
                              });


                              //LOAD DOS GRAFICOS
                              //BASE DADOS


                              //BAR GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««
                              function plot_bar(data)
                              {
                                        var plot;
                                        var data2 = [];
                                        var ticksA = [];
                                        var updation;
                                        var wbe = data;
                                        var painel = $("#" + wbe[0] + "WBE");
                                        var first_time = true;
                                        get_values_bar();
                                        function get_values_bar()
                                        {
                                                  $.post("Requests.php", {action: wbe[10], selected_query: wbe[7]},
                                                  function(data)
                                                  {
                                                            if (data === null)
                                                            {
                                                                      clearTimeout(updation);
                                                                      painel.remove();
                                                                      $("#" + wbe[0] + "Main").remove();
                                                                      $.jGrowl("O gráfico de Barras " + wbe[1] + " não apresenta resultados", {life: 10000});
                                                                      return false;
                                                            }
                                                            if (wbe[8] == "total de vendas por user")
                                                            {
                                                                      data2 = [];
                                                                      $.each(data, function(index, value) {
                                                                                data2.push([this.user, +this.status_count]);
                                                                      });
                                                                      var data = [];
                                                                      ticksA = [];
                                                                      for (i = 0; i < data2.length; i++)
                                                                      {
                                                                                data.push([i, data2[i][1]]);
                                                                                ticksA.push([i, data2[i][0]]);
                                                                      }
                                                            }
                                                            if (wbe[8] == "total de cenas por user")
                                                            {
                                                                      data2 = [];
                                                                      $.each(data, function(index, value) {
                                                                                data2.push([this.user, +this.status_count]);
                                                                      });
                                                                      var data = [];
                                                                      ticksA = [];
                                                                      for (i = 0; i < data2.length; i++)
                                                                      {
                                                                                data.push([i, data2[i][1] * 4]);
                                                                                ticksA.push([i, data2[i][0] * 5]);
                                                                      }
                                                            }
                                                            if (first_time)
                                                            {
                                                                      var dataset = [{label: "Operadores", data: data}];
                                                                      var options = {legend: {noColumns: 1, position: "nw"}, series: {bars: {show: true, barWidth: 0.2, order: 1, lineWidth: 2}}, bars: {align: "center", barWidth: 0.5}, xaxis: {axisLabel: "Operadores", axisLabelUseCanvas: true, axisLabelFontSizePixels: 3, axisLabelFontFamily: 'Verdana, Arial', axisLabelPadding: 10, ticks: ticksA}};
                                                                      plot = $.plot(painel, dataset, options);
                                                                      first_time = false;
                                                            }
                                                            else
                                                            {
                                                                      var dataset = [{label: "Operadores", data: data}];
                                                                      plot.setData(dataset);
                                                                      plot.draw();
                                                            }
                                                            updation = setTimeout(get_values_bar, wbe[9]);
                                                  }, "json");
                                        }

                              }
                              //BAR GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««
                              //UPDATE GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««
                              function plot_update(data)
                              {
                                        var plot;
                                        var updation;
                                        var wbe = data;
                                        var painel = $("#" + wbe[0] + "WBE");
                                        var first_time = true;
                                        var result;
                                        var data1;
                                        get_values_update();
                                        function get_values_update()
                                        {
                                                  $.post("Requests.php", {action: wbe[10], selected_query: wbe[7] + " limit 150"},
                                                  function(data)
                                                  {
                                                            if (data === null)
                                                            {
                                                                      clearTimeout(updation);
                                                                      painel.remove();
                                                                      $("#" + wbe[0] + "Main").remove();
                                                                      $.jGrowl("O gráfico de Linhas " + wbe[1] + " não apresenta resultados", {life: 10000});
                                                                      return false;
                                                            }
                                                            if (wbe[8] === "total de chamadas" || wbe[8] === "total de chapadas")
                                                            {
                                                                      data1 = [];
                                                                      $.each(data, function(index, value) {
                                                                                data1.push(+value);
                                                                      });
                                                                      result = [];
                                                                      for (var i = 0; i < data1.length; ++i) {
                                                                                result.push([i, data1[i]]);
                                                                      }
                                                            }

                                                            if (first_time)
                                                            {
                                                                      var options = {
                                                                                series: {shadowSize: 0}, // drawing is faster without shadows
                                                                                yaxis: {min: 0, max: 120},
                                                                                xaxis: {show: false},
                                                                                colors: ["#2686d2"],
                                                                                series: {
                                                                                          lines: {
                                                                                                    lineWidth: 1,
                                                                                                    fill: true,
                                                                                                    fillColor: {colors: [{opacity: 0.5}, {opacity: 1.0}]},
                                                                                                    steps: false,
                                                                                                    show: true

                                                                                          }, points: {show: false}
                                                                                }
                                                                      };
                                                                      plot = $.plot(painel, [result], options);
                                                                      first_time = false;
                                                            }
                                                            else
                                                            {
                                                                      plot.setData([result]);
                                                                      plot.draw();
                                                            }
                                                            updation = setTimeout(get_values_update, wbe[9]);
                                                  }, "json");
                                        }
                              }
                              //UPDATE GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««  
                              //PIE GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««
                              function plot_pie(data) {
                                        var wbe = data;
                                        var updation;
                                        var dataIsNull = false;
                                        var painel = $("#" + wbe[0] + "WBE");
                                        var first_time = true;
                                        get_values_pie();
                                        function get_values_pie(opcao, selectedQuery)
                                        {
                                                  var data1 = [];
                                                  $.post("Requests.php", {action: wbe[10], selected_query: wbe[7]},
                                                  function(data)
                                                  {
                                                            if (data === null)
                                                            {
                                                                      clearTimeout(updation);
                                                                      dataIsNull = true;
                                                                      painel.remove();
                                                                      $("#" + wbe[0] + "Main").remove();
                                                                      $.jGrowl("O gráfico de Tarte " + wbe[1] + " não apresenta resultados", {life: 10000});
                                                                      return false;
                                                            }
                                                            if (wbe[8] == "total de feedbacks por campanha")
                                                            {
                                                                      $.each(data, function(index, value) {
                                                                                data1.push({label: (this.status_name), data: +this.count});
                                                                      });
                                                            }
                                                            if (first_time)
                                                            {
                                                                      $.plot(painel, data1, {
                                                                                series: {
                                                                                          pie: {
                                                                                                    show: true,
                                                                                                    radius: 1,
                                                                                                    label: {
                                                                                                              show: true,
                                                                                                              radius: 2 / 3,
                                                                                                              formatter: function(label, series) {
                                                                                                                        return '<div style="font-size:11px;text-align:center;padding:2px;color:black;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
                                                                                                              },
                                                                                                              threshold: 0.02
                                                                                                    }
                                                                                          }
                                                                                },
                                                                                legend: {
                                                                                          show: false
                                                                                },
                                                                                grid: {
                                                                                          hoverable: false,
                                                                                          clickable: true
                                                                                }});
                                                            }
                                                            updation = setTimeout(get_values_pie, wbe[9]);
                                                  }, "json");
                                        }
                              }
                              //PIE GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««*/
                              //INBOUND WALLBOARD  »»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»˛»»»»»»»»»»»»»»»»»»»»»»˛»»»»»»»»»»»»»˛»»»»»»»»»»»»»»»»»»
                              function   inbound_wallboard(data)
                              {

                                        var ready = 0;
                                        var queue = 0;
                                        var paused = 0;
                                        var incall = 0;
                                        var updation;
                                        var wbe = data;
                                        var first_time_plot = true;
                                        var plot;
                                        //carregar a dropbox com as campanhas do group especificado
                                        var panel = $("#" + wbe[0] + "Main");
                                        var font_size = ((panel.width() / 100) + (panel.height() / 100));
                                        panel.empty();




                                        panel.append($("<div>").addClass("grid-title").attr("style", "text-align:center;   background-color: #F5F6CE;").text(wbe[1]));
                                        panel.append($("<div>").attr("style", "height:90%;width:100%;background-color: #F9p5F1;font-size: " + font_size + "px;")
                                                .append($("<table>").addClass("table  table-mod").attr("style", "height:100%;width:100%; ")
                                                .append($("<tbody>")
                                                .append($("<tr>")
                                                .append($("<td>").append($("<div align='center'>").addClass("grid-title  ").text("Agentes em Espera")).append($("<div>").addClass("grid-content").attr("id", "agente_espera")))
                                                .append($("<td>").append($("<div>").addClass("grid-title ").text("TMA")).append($("<div>").addClass("grid-content").attr("id", "tma1")))
                                                .append($("<td>").append($("<div>").addClass("grid-title ").text("Agentes Totais")).append($("<div>").addClass("grid-content").attr("id", "agente_total"))))
                                                .append($("<tr>")
                                                .append($("<td>").append($("<div>").addClass("grid-title ").text("SLA1")).append($("<div>").addClass("grid-content").attr("id", "sla1")))
                                                .append($("<td colspan='2' rowspan='2' style='position:relative'>").append($("<div>").attr("id", "plot_inbound").attr("style", "height:100%;width:90%;position:absolute"))))
                                                .append($("<tr>")
                                                .append($("<td>").append($("<div>").addClass("grid-title ").text("SLA2")).append($("<div>").addClass("grid-content").attr("id", "sla2"))))
                                                )));

                                        get_values_inbound();
                                        function get_values_inbound()
                                        {
                                                  var querie = 'SELECT a.status  FROM `vicidial_live_agents` a left join vicidial_live_inbound_agents b on a.user=b.user where closer_campaigns  like "% ' + wbe[8] + ' %"';
                                                  $.post("Requests.php", {action: "get_agents", query_text: querie},
                                                  function(data)
                                                  {
                                                            ready = 0;
                                                            queue = 0;
                                                            paused = 0;
                                                            incall = 0;
                                                            var a = 0;
                                                            $.each(data, function(index, value)
                                                            {
                                                                      switch (data[a])
                                                                      {
                                                                                case "READY":
                                                                                          ready++;
                                                                                          break;
                                                                                case "QUEUE":
                                                                                          queue++;
                                                                                          break;
                                                                                case"PAUSED":
                                                                                          paused++;
                                                                                          break;
                                                                                case"INCALL":
                                                                                          incall++;
                                                                                          break;
                                                                      }
                                                                      a++;
                                                            });
                                                  }, "json");


                                                  $.post("Requests.php", {action: wbe[10], selected_query: wbe[7]},
                                                  function(data)
                                                  {
                                                            var i = 0;
                                                            var callsToday;
                                                            var dropsToday;
                                                            var answersToday;
                                                            var VSCcat1;
                                                            var VSCcat1tally;
                                                            var VSCcat2;
                                                            var VSCcat2tally;
                                                            var VSCcat3;
                                                            var VSCcat3tally;
                                                            var VSCcat4;
                                                            var VSCcat4tally;
                                                            var hold_sec_stat_one;
                                                            var hold_sec_stat_two;
                                                            var hold_sec_answer_calls;
                                                            var hold_sec_drop_calls;
                                                            var hold_sec_queue_calls;
                                                            var inGroupDetail;

                                                            var answer_sec_pct_rt_stat_one;
                                                            var answer_sec_pct_rt_stat_two;

                                                            callsToday = +data[0].callsToday;
                                                            dropsToday = +data[0].dropsToday;
                                                            answersToday = +data[0].answersToday;
                                                            VSCcat1 = +data[0].VSCcat1;
                                                            VSCcat1tally = +data[0].VSCcat1tally;
                                                            VSCcat2 = +data[0].VSCcat2;
                                                            VSCcat2tally = +data[0].VSCcat2tally;
                                                            VSCcat3 = +data[0].VSCcat3;
                                                            VSCcat3tally = +data[0].VSCcat3tally;
                                                            VSCcat4 = +data[0].VSCcat4;
                                                            VSCcat4tally = +data[0].VSCcat4tally;
                                                            hold_sec_stat_one = +data[0].hold_sec_stat_one;
                                                            hold_sec_stat_two = +data[0].hold_sec_stat_two;
                                                            hold_sec_answer_calls = +data[0].hold_sec_answer_calls;
                                                            hold_sec_drop_calls = +data[0].hold_sec_drop_calls;
                                                            hold_sec_queue_calls = +data[0].hold_sec_queue_calls;
                                                            inGroupDetail = +data[0].inGroupDetail;

                                                            $.post("Requests.php", {action: "inbound_groups_info", group_id: wbe[8]},
                                                            function(data1)
                                                            {
                                                                      answer_sec_pct_rt_stat_one = data1[0].answer_sec_pct_rt_stat_one;
                                                                      answer_sec_pct_rt_stat_two = data1[0].answer_sec_pct_rt_stat_two;


                                                                      if ((dropsToday > 0) && (answersToday > 0))
                                                                      {
                                                                                drpctToday = ((dropsToday / callsToday) * 100);
                                                                                drpctToday = (drpctToday).toFixed(2);
                                                                      }
                                                                      else
                                                                      {
                                                                                drpctToday = 0;
                                                                      }
                                                                      if (callsToday > 0)
                                                                      {
                                                                                AVGhold_sec_queue_calls = (hold_sec_queue_calls / callsToday);
                                                                                AVGhold_sec_queue_calls = (AVGhold_sec_queue_calls).toFixed();
                                                                      }
                                                                      else
                                                                      {
                                                                                AVGhold_sec_queue_calls = 0;
                                                                      }
                                                                      if (dropsToday > 0)
                                                                      {
                                                                                AVGhold_sec_drop_calls = (hold_sec_drop_calls / dropsToday);
                                                                                AVGhold_sec_drop_calls = (AVGhold_sec_drop_calls).toFixed();
                                                                      }
                                                                      else
                                                                      {
                                                                                AVGhold_sec_drop_calls = 0;
                                                                      }
                                                                      if (answersToday > 0)
                                                                      {
                                                                                PCThold_sec_stat_one = ((hold_sec_stat_one / answersToday) * 100);
                                                                                PCThold_sec_stat_one = (PCThold_sec_stat_one).toFixed(2);
                                                                                PCThold_sec_stat_two = ((hold_sec_stat_two / answersToday) * 100);
                                                                                PCThold_sec_stat_two = (PCThold_sec_stat_two).toFixed(2);
                                                                                AVGhold_sec_answer_calls = (hold_sec_answer_calls / answersToday);
                                                                                AVGhold_sec_answer_calls = (AVGhold_sec_answer_calls).toFixed();
                                                                      }
                                                                      else
                                                                      {
                                                                                PCThold_sec_stat_one = 0;
                                                                                PCThold_sec_stat_two = 0;
                                                                                AVGhold_sec_answer_calls = 0;

                                                                      }
//update dos valores na table




                                                                      var agente_espera = document.getElementById("agente_espera");
                                                                      agente_espera.innerHTML = "<label style='font-size: 2em;' >" + ready + "</label>";
                                                                      var tma1 = document.getElementById("tma1");
                                                                      tma1.innerHTML = "<label style='font-size: 2em;' >" + AVGhold_sec_queue_calls + "</label>";
                                                                      var agente_total = document.getElementById("agente_total");
                                                                      agente_total.innerHTML = "<label style='font-size: 2em;' >" + (ready + queue + paused + incall) + "</label>";
                                                                      var sla1 = document.getElementById("sla1");
                                                                      sla1.innerHTML = "<label style='font-size: 2em;' >" + Math.round((answer_sec_pct_rt_stat_one * 100) / PCThold_sec_stat_one).toFixed() + "%</label> ";
                                                                      var sla2 = document.getElementById("sla2");
                                                                      sla2.innerHTML = "<label style='font-size: 2em;' >" + Math.round((answer_sec_pct_rt_stat_two * 100) / PCThold_sec_stat_two).toFixed() + "%</label>";



                                                                      var painel = $("#plot_inbound");
                                                                      var data = [
                                                                                {label: 'Cumprido', data: [[1, PCThold_sec_stat_one], [2, PCThold_sec_stat_two]]},
                                                                                {label: 'bar', data: [[1, answer_sec_pct_rt_stat_one], [2, answer_sec_pct_rt_stat_two]]}];


                                                                      if (first_time_plot)
                                                                      {
                                                                                var options = {
                                                                                          series: {stack: 0,
                                                                                                    lines: {show: false, steps: false},
                                                                                                    bars: {show: true, barWidth: 0.9, align: 'center'}},
                                                                                          xaxis: {ticks: [[1, 'SLA1'], [2, 'SLA2']]}};

                                                                                plot = $.plot(painel, data, options);

                                                                                first_time = false;

                                                                      }
                                                                      else
                                                                      {

                                                                                plot.setData(data);
                                                                                plot.draw();
                                                                      }



                                                            }, "json");

                                                            updation = setTimeout(get_values_inbound, wbe[9]);
                                                  }, "json");
                                        }







                              }
                              //INBOUND WALLBOARD  »»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»˛»»»»»»»»»»»»»»»»»»»»»»˛»»»»»»»»»»»»»˛»»»»»»»»»»»»»»»»»»







                              //window exit
                              //clean updaters
                              $(window).bind('beforeunload', function() {
                                        $(".PanelWB").remove();
                              });

                              /*
                               var chamadas_efectuadas = document.getElementById("chamadas_efectuadas");
                               chamadas_efectuadas.innerHTML = "<h2> " + callsToday + "</h2>";
                               var chamadas_perdidas = document.getElementById("chamadas_perdidas");
                               chamadas_perdidas.innerHTML = "Chamadas perdidas: " + dropsToday;
                               var chamadas_atendidas = document.getElementById("chamadas_atendidas");
                               chamadas_atendidas.innerHTML = "Chamadas atendidas: " + answersToday;
                               
                               var tme_chamadas = document.getElementById("tme_chamadas");
                               tme_chamadas.innerHTML = "Tempo Médio em Espera para todas as Chamadas: " + AVGhold_sec_queue_calls;
                               var tme_chamadas_perdidas = document.getElementById("tme_chamadas_perdidas");
                               tme_chamadas_perdidas.innerHTML = "Tempo Médio em Espera para Chamadas Perdidas: " + AVGhold_sec_drop_calls;
                               var tme_chamadas_atendidas = document.getElementById("tme_chamadas_atendidas");
                               tme_chamadas_atendidas.innerHTML = "Tempo Médio em Espera para Chamadas Atendidas:" + AVGhold_sec_answer_calls;
                               */
                    </script>
          </body>


</html>




