
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
                    <script type="text/javascript" src="/bootstrap/js/jquery.flot.pie.min.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/jquery.flot.resize.min.js"></script>
                    <script type="text/javascript" src="/bootstrap/js/jquery.flot.time.min.js"></script>

                    <script type="text/javascript" src="/bootstrap/js/jquery.jgrowl.js"></script>

                    <style>
                              .PanelWB{position:absolute;
                                       -webkit-border-radius: 3px;
                                       -moz-border-radius: 3px;
                                       border-radius: 3px;
                                       box-shadow: 5px 5px 13px -6px rgba(0, 0, 0, 0.3) ;
                              }
                              .ui-widget-header{background: none !important;}

                              .inbound_grid_title{
                                        border-bottom:3px solid #FFFFFF;
                                        background-color: #050430;
                                        font-size: 0.7em;
                                        color: #FFFFFF;
                                        padding-top:2%;
                                        padding-bottom: 0.3%;
                                        text-align: center; 
                              }
                              .inbound_grid_content{
                                        vertical-align: middle;
                                        text-align: center;
                                        color: #000000;
                                        font-size: 2.2em;
                                        padding-top:0.4em;
                              }
                              .inbound_grid_div{
                                        border:3px solid rgb(43, 80, 38);
                                        box-shadow: 5px 5px 13px -6px rgba(0, 0, 0, 0.3) ;
                                        width:7.5em;
                                        height:4em;
                                        background-color: rgb(228, 228, 228);
                              }
                              .inbound_title{
                                        border:3px solid rgb(43, 80, 38);
                                        text-align: center; 
                                        color: #FFFFFF;
                                        padding-top:0.8%;
                                        padding-bottom:0.8%;
                                        background-color:rgb(38, 52, 109);
                              }
                              .legend {
                                        display:block!important;
                              }
                    </style>
          </head>    

          <body>
                    <div style="width:100%;height:100%">
                              <div id="MainLayout" class="ui-widget-header"></div>
                              <div id="jGrowl" class="top-right jGrowl" ><div class="jGrowl-notification" ></div></div>
                    </div>

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
                              //       10    graph_type;
                              //       11    Param1    

                              $(document).ready(function() {

                                        $("#MainLayout").attr("style", "width:100%;height:100%;position:absolute;background-color:#F5F5F5;");
                                        var parameters = window.name.split(";");
                                        var windwidth = +parameters[1]; //parameter;
                                        var windheight = +parameters[2]; //parameter;
                                        layout = +parameters[0]; //parameter
                                        $("[data-t=tooltip]").tooltip({placement: "left", html: true});
                                        $.post("Requests.php", {action: "wbe", layout_Id: layout},
                                        function(data)
                                        {
                                                  wbes = [];
                                                  $.each(data, function(index, value) {
                                                            wbes.push([this.id, this.name, this.posX, this.posY, this.width, this.height, this.layout_Id, this.query_text, this.opcao_query, this.update_time, this.graph_type, this.param1]);
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
                                                                    .append($("<div>").addClass("grid-content").attr("id", wbes[i][0] + "WBEGD")
                                                                    .append($("<div>").attr("id", wbes[i][0] + "WBE").attr("style", "width:" + (width - 20) + "px;height:" + (height - 75) + "px;padding: 0px;").attr("data-t", "tooltip").attr("title", "Tempo de Actualização: " + (wbes[i][9] / 1000) + " seg.")))
                                                                    );
//mostra o group ou venda ou entao etc..

                                                            $("#" + wbes[i][0] + "WBEGD").append($("<div>").addClass("pull-left").text(wbes[i][11]));
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
                              //øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø
                              //UPDATE GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««
                              function plot_update(data)
                              {

                                        var max_y = 0;
                                        var plot;
                                        var updation;
                                        var wbe = data;
                                        var painel = $("#" + wbe[0] + "WBE");
                                        var result;
                                        var result2;
                                        var soma_result = [];
                                        var data1;
                                        var dates = [];

//teste
//SELECT count(lead_id) FROM `vicidial_log` where campaign_id="c00095" and call_date between "2013-05-29 17:15:00" and "2013-05-29 17:20:00"




                                        get_values_update();
                                        function get_values_update()
                                        {
                                                                               

                                                  $.post("Requests.php", {action: wbe[10], selected_query: wbe[7]},
                                                  function(data)
                                                  {
//////////////////////////////////////////////////////////////////////////////////////////INBOUND////////////////////////////////////////////////////////////////////////////////////////////////
                                                            if (data === null)
                                                            {
                                                                      clearTimeout(updation);
                                                                      painel.remove();
                                                                      $("#" + wbe[0] + "Main").remove();
                                                                      $.jGrowl("O gráfico de Linhas " + wbe[1] + " não apresenta resultados", {life: 10000});
                                                                      return false;
                                                            }
                                                            if (wbe[8] === "Feedbacks por campanha")
                                                            {
                                                                      data1 = [];
                                                                      dates = [];
                                                                      soma_result = []
                                                                      result = [];
                                                                      result2 = [];
                                                                      $.each(data, function(index, value) {
                                                                                data1.push(data[index].lead_id);
                                                                                var temp = new Date(data[index].call_date);
                                                                                temp.setHours(temp.getHours() + 1);
                                                                                dates.push(temp);
                                                                      });

                                                                      for (var i = 0; i < data1.length; ++i) {
                                                                                result.push([new Date(dates[i]).getTime(), data1[i]]);
                                                                                soma_result.push([new Date(dates[i]).getTime(), data1[i]]);
                                                                      }
                                                            }

//fazer replace da query vicidial_log para vicidial_closer_log
                                                            var query = wbe[7].replace("vicidial_log", "vicidial_closer_log");
                                                            $.post("Requests.php", {action: wbe[10], selected_query: query},
                                                            function(data)
                                                            {
//////////////////////////////////////////////////////////////////////////////////////////OUTBOUND////////////////////////////////////////////////////////////////////////////////////////////////
                                                                      if (data === null)
                                                                      {
                                                                                clearTimeout(updation);
                                                                                painel.remove();
                                                                                $("#" + wbe[0] + "Main").remove();
                                                                                $.jGrowl("O gráfico de Linhas " + wbe[1] + " não apresenta resultados", {life: 10000});
                                                                                return false;
                                                                      }
                                                                      if (wbe[8] === "Feedbacks por campanha")
                                                                      {
                                                                                data1 = [];
                                                                                $.each(data, function(index, value) {
                                                                                          data1.push(data[index].lead_id);

                                                                                });
                                                                                for (var i = 0; i < data1.length; ++i) {
                                                                                          result2.push([new Date(dates[i]).getTime(), data1[i]]);
                                                                                          soma_result[i][1] = parseInt(soma_result[i][1], 10) + parseInt(data1[i], 10);
                                                                                }
                                                                                for (var a = 0; a < soma_result.length; a++)
                                                                                {
                                                                                          if (soma_result[a][1] > max_y)
                                                                                                    max_y = soma_result[a][1];
                                                                                }
                                                                      }
                                                                      var information = [{
                                                                                          data: result,
                                                                                          label: "Outbound",
                                                                                          color: "#00FF00"
                                                                                },
                                                                                {
                                                                                          data: result2,
                                                                                          label: "Inbound",
                                                                                          color: "#0000FF"
                                                                                },
                                                                                {
                                                                                          data: soma_result,
                                                                                          label: "Soma",
                                                                                          color: "#FF0000"
                                                                                }];

                                                                      var options = {
                                                                                series: {shadowSize: 0}, // drawing is faster without shadows
                                                                                yaxis: {min: 0, max: max_y+10},
                                                                                xaxis: {mode: "time", timeformat: "%H:%M", minTickSize: [5, "minute"],
                                                                                          min: (dates[0]),
                                                                                          max: (dates[dates.length - 1])

                                                                                },
                                                                                series: {
                                                                                          lines: {
                                                                                                    lineWidth: 1,
                                                                                                    steps: false,
                                                                                                    show: true
                                                                                          }
                                                                                }
                                                                      };


                                                                      plot = $.plot(painel, information, options);


                                                                      updation = setTimeout(get_values_update, wbe[9]);
                                                            }, "json");
                                                  }, "json");
                                        }
                              }
                              //øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø
                              //PIE GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««
                              function plot_pie(data) {
                                        var wbe = data;
                                        var updation;
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
                                                                      painel.remove();
                                                                      $("#" + wbe[0] + "Main").remove();
                                                                      $.jGrowl("O gráfico de Tarte " + wbe[1] + " não apresenta resultados", {life: 10000});
                                                                      return false;
                                                            }
                                                            if (wbe[8] == "total de feedbacks por campanha")
                                                            {
                                                                      var i = 0;
                                                                      $.each(data, function(index, value) {
                                                                                data1.push({label: (this.status_name), data: +this.count});
                                                                      });
                                                                      if (i == 0)//se so houver 1 resultado ele n faz render, entao adiciona-se 1 elemento infimo
                                                                                data1.push({label: ("zero"), data: 0.001});
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
                                                                                                              threshold: 0.05
                                                                                                    }
                                                                                          }
                                                                                },
                                                                                legend: {
                                                                                          show: false
                                                                                },
                                                                                grid: {
                                                                                          hoverable: false,
                                                                                          clickable: false
                                                                                }});
                                                            }
                                                            updation = setTimeout(get_values_pie, wbe[9]);
                                                  }, "json");
                                        }
                              }
                              //øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø
                              //INBOUND WALLBOARD  »»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»»˛»»»»»»»»»»»»»»»»»»»»»»˛»»»»»»»»»»»»»˛»»»»»»»»»»»»»»»»»»
                              function   inbound_wallboard(data)
                              {

                                        var ready = 0;
                                        var queue = 0;
                                        var paused = 0;
                                        var incall = 0;
                                        var updation;
                                        var wbe = data;
                                        var id = data[0];
                                        var first_time_plot = true;
                                        var plot;
                                        //carregar a dropbox com as campanhas do group especificado
                                        var panel = $("#" + wbe[0] + "Main");
                                        var font_size = ((panel.width() / 50) + (panel.height() / 100));
                                        panel.empty();
                                        panel.append($("<div>").attr("style", "height:98%;font-size:" + font_size + "px;background-color: rgb(210, 215, 215); padding-left:1%;padding-right:1%;padding-top:1%;").attr("data-t", "tooltip").attr("title", "Tempo de Actualização: " + (wbe[9] / 1000) + " seg.")


                                                .append($("<div>").append($("<label>").addClass("inbound_title").text(wbe[1])))//titulo do inbound



                                                .append($("<table>").attr("style", "height:80%;")


                                                //top                    
                                                .append($("<tr>")

                                                .append($("<td>")
                                                .append($("<div>").addClass(" inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Disponiveis")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "agente_dispo" + id)))))


                                                .append($("<td>")
                                                .append($("<div>").addClass("inbound_grid_div ")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Espera")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "agente_espera" + id)))))


                                                .append($("<td>")
                                                .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Pausa")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "agente_pause" + id)))))


                                                .append($("<td>")
                                                .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Em chamada")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "agente_incall" + id)))))


                                                .append($("<td>")
                                                .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Totais")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "agente_total" + id))))))



                                                //left/right    
                                                .append($("<tr>")

                                                .append($("<td>")
                                                .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("TMA")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "tma1" + id)))))

                                                //graph
                                                .append($("<td>")
                                                .append($("<div>").attr("style", "width:75%;height:55%;position:absolute; ").attr("id", "plot_inbound" + id))))


                                                .append($("<tr>")

                                                .append($("<td>")
                                                .append($("<div >").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("SLA1")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "sla1" + id))))))


                                                .append($("<tr>")

                                                .append($("<td>")
                                                .append($("<div>").addClass(" inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("SLA2")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "sla2" + id))))))
                                                ));
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

                                                            var hold_sec_stat_one;
                                                            var hold_sec_stat_two;
                                                            var hold_sec_answer_calls;
                                                            var hold_sec_drop_calls;
                                                            var hold_sec_queue_calls;
                                                            var inGroupDetail;
                                                            var agent_non_pause_sec;
                                                            var answer_sec_pct_rt_stat_one;
                                                            var answer_sec_pct_rt_stat_two;
                                                            var AVG_ANSWERagent_non_pause_sec = 0;
                                                            callsToday = +data[0].callsToday;
                                                            dropsToday = +data[0].dropsToday;
                                                            answersToday = +data[0].answersToday;
                                                            hold_sec_stat_one = +data[0].hold_sec_stat_one;
                                                            hold_sec_stat_two = +data[0].hold_sec_stat_two;
                                                            hold_sec_answer_calls = +data[0].hold_sec_answer_calls;
                                                            hold_sec_drop_calls = +data[0].hold_sec_drop_calls;
                                                            hold_sec_queue_calls = +data[0].hold_sec_queue_calls;
                                                            inGroupDetail = +data[0].inGroupDetail;
                                                            agent_non_pause_sec = +data[0].agent_non_pause_sec;
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
                                                                                if (agent_non_pause_sec > 0)
                                                                                {
                                                                                          $AVG_ANSWERagent_non_pause_sec = (($answersTODAY / agent_non_pause_sec) * 60);
                                                                                          $AVG_ANSWERagent_non_pause_sec = Math.round($AVG_ANSWERagent_non_pause_sec).toFixed(2);
                                                                                }
                                                                                else
                                                                                {
                                                                                          $AVG_ANSWERagent_non_pause_sec = 0;
                                                                                }

                                                                      }
                                                                      else
                                                                      {
                                                                                PCThold_sec_stat_one = 0;
                                                                                PCThold_sec_stat_two = 0;
                                                                                AVGhold_sec_answer_calls = 0;
                                                                      }
//update dos valores na table 



                                                                      var tma1 = document.getElementById("tma1" + id);
                                                                      tma1.innerHTML = AVG_ANSWERagent_non_pause_sec;
                                                                      var agente_total = document.getElementById("agente_total" + id);
                                                                      agente_total.innerHTML = (ready + queue + paused + incall);
                                                                      var agente_espera = document.getElementById("agente_espera" + id);
                                                                      agente_espera.innerHTML = queue;
                                                                      var agente_disponivel = document.getElementById("agente_dispo" + id);
                                                                      agente_disponivel.innerHTML = ready;
                                                                      var agente_pause = document.getElementById("agente_pause" + id);
                                                                      agente_pause.innerHTML = paused;
                                                                      var agente_incall = document.getElementById("agente_incall" + id);
                                                                      agente_incall.innerHTML = incall;
                                                                      var sla1 = document.getElementById("sla1" + id);
                                                                      if (PCThold_sec_stat_one > 0)
                                                                                sla1.innerHTML = Math.round((answer_sec_pct_rt_stat_one * 100) / PCThold_sec_stat_one).toFixed() + "%";
                                                                      else
                                                                                sla1.innerHTML = 0;
                                                                      var sla2 = document.getElementById("sla2" + id);
                                                                      if (PCThold_sec_stat_two > 0)
                                                                                sla2.innerHTML = Math.round((answer_sec_pct_rt_stat_two * 100) / PCThold_sec_stat_two).toFixed() + "%";
                                                                      else
                                                                                sla2.innerHTML = 0;
                                                                      var painel = $("#plot_inbound" + id);
                                                                      var data = [
                                                                                {label: '% Cumprida', data: [[1, PCThold_sec_stat_one], [2, PCThold_sec_stat_two]], color: "#0000FF"},
                                                                                {label: 'SLA', data: [[1, answer_sec_pct_rt_stat_one], [2, answer_sec_pct_rt_stat_two]], color: "#FF8000"}];
                                                                      if (first_time_plot)
                                                                      {
                                                                                var options = {
                                                                                          series: {stack: 0,
                                                                                                    lines: {show: false, steps: false},
                                                                                                    bars: {show: true, barWidth: 0.9, align: 'center'}},
                                                                                          xaxis: {tickLength: 0, ticks: [[1, 'SLA1->' + answer_sec_pct_rt_stat_one + 'segs'], [2, 'SLA2->' + answer_sec_pct_rt_stat_two + 'segs']]},
                                                                                };
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
                              //øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø

                              //window exit
                              //clean updaters
                              $(window).bind('beforeunload', function() {
                                        $("#MainLayout .PanelWB").remove();
                              });

                    </script>



          </body>


</html>




