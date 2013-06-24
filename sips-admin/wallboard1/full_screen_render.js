


var wbes;
var layout;
$(document).ready(function() {

      $("#MainLayout").attr("style", "width:100%;height:100%;position:absolute;background-color:#F5F5F5;");
      var parameters = window.name.split(";");
      var windwidth = +parameters[1]; //parameter;
      var windheight = +parameters[2]; //parameter;
      layout = +parameters[0]; //parameter
      $("[data-t=tooltip]").tooltip({placement: "left", html: true});
      $.post("Requests.php", {action: "wbe", id_layout: layout},
      function(data)
      {
            wbes = [];
            $.each(data, function(index, value) {
                  wbes.push([this.id, this.id_layout, this.name, this.pos_x, this.pos_y, this.width, this.height, this.update_time, this.graph_type, this.dataset]);
            });
            var i = 0;
            var temp_window = $(window);
            $.each(wbes, function(index, value) {
                  var left = (wbes[i][3] * temp_window.width()) / windwidth;
                  var top = (wbes[i][4] * temp_window.height()) / windheight;
                  var width = (wbes[i][5] * temp_window.width()) / windwidth;
                  var height = (wbes[i][6] * temp_window.height()) / windheight;
                  $("#MainLayout").append($("<div>").addClass("PanelWB ui-widget-content").attr("style", "position: absolute;    left:" + left + "px;top:" + top + "px; width:" + width + "px;height:" + height + "px;").attr("id", wbes[i][0] + "Main").draggable({containment: '#MainLayout'})
                          .append($("<div>").addClass("grid-title")
                          .append($("<div>").addClass("pull-left").text(wbes[i][2]))
                          )
                          .append($("<div>").addClass("grid-content").attr("id", wbes[i][0] + "WBEGD")
                          .append($("<div>").attr("id", wbes[i][0] + "WBE").attr("style", "width:" + (width - 20) + "px;height:" + (height - 75) + "px;padding: 0px;").attr("data-t", "tooltip").attr("title", "Tempo de Actualização: " + (wbes[i][7] / 1000) + " seg.")))
                          );
                  if (wbes[i][8] === "1")//update
                  {
                        plot_update(wbes[i]);
                  }
                  if (wbes[i][8] === "2")//bar
                  {
                        plot_bar(wbes[i]);
                  }
                  if (wbes[i][8] === "3")//pie
                  {
                        plot_pie(wbes[i]);
                  }
                  if (wbes[i][8] === "4")//Inbound stuff
                  {
                        inbound_wallboard(wbes[i]);
                  }
                  if (wbes[i][8] === "5")//DataTable top
                  {
                        dataTable_top(wbes[i]);
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
      var result = [];
      var information = [];
      var dates = [];

      //--------------wbes
      //0      id,
      // 1       id_layout,
      //   2      name,
      //     3     pos_x, 
      //       4   pos_y,
      //       5    width, 
      //       6    height,
      //       7      update_time, 
      //       8      graph_type;
      //   9      Array[8]
      //0: id
      //1: id_wallboard
      //2: codigo_query
      //3: opcao_query
      //4: tempo
      //5: user
      //6: user_group
      //7: campaign_id
      //8:linha_inbound
      //9: mode
      //10: status_feedback
      //11: chamadas

      get_values_update();



      function get_values_update()
      {


            $.post("Requests.php", {action: "1", datasets: wbe[9]},
            function(dataBase)
            {

                  var data = dataBase;
                  if (data === null)
                  {
                        clearTimeout(updation);
                        painel.remove();
                        $("#" + wbe[0] + "Main").remove();
                        $.jGrowl("A Linha " + wbe[2] + " não apresenta resultados", {life: 10000});
                        return false;
                  }
                  var verifier = 0;//verifica se o dataset tem algum lead_id>0
                  var aux = 0;
                  for (var key in data) {
                        var obj = data[key];
                        for (var prop in obj) {
                              if (obj.hasOwnProperty(prop)) {
                                    for (var k = 0; k < obj[prop].length; k++)
                                    {
                                          if (obj[prop][k].leads > max_y)
                                          {
                                                max_y = +obj[prop][k].leads;
                                          }
                                          verifier += +obj[prop][k].leads;
                                          var temp = new Date(obj[prop][k].call_date);
                                          temp.setHours(temp.getHours() + 1);
                                          dates.push(temp);
                                          result.push([new Date(temp).getTime(), obj[prop][k].leads]);
                                    }
                                    if (verifier > 0) {
                                          if (wbe[9][aux].param1 !== "0")
                                                information.push({data: result, label: wbe[9][aux].opcao_query + " -> " + wbe[9][aux].param1});
                                          else
                                                information.push({data: result, label: wbe[9][aux].opcao_query});

                                          result = [];
                                          verifier = 0;
                                    }
                                    else
                                    {
                                          result = [];
                                          information.push({data: result, label: "Sem resultados"});
                                          $.jGrowl("A Linha " + wbe[9][aux].opcao_query + " do grafico " + wbe[2] + " não apresenta resultados", {life: 5000});
                                          verifier = 0;


                                    }

                              }
                              aux++;
                        }
                  }




                  var options = {
                        series: {shadowSize: 0}, // drawing is faster without shadows
                        yaxis: {min: 0, max: max_y + 35, tickSize: 10},
                        xaxis: {mode: "time", timeformat: "%H:%M", minTickSize: [5, "minute"],
                              min: (dates[0]),
                              max: (dates[dates.length - 1])

                        },
                        series: {
                              lines: {
                                    lineWidth: 2,
                                    steps: false,
                                    show: true
                              }
                        }
                  };

                  plot = $.plot(painel, information, options);

                  information = [];
                  result = [];
                  dates = [];

                  updation = setTimeout(get_values_update, wbe[7]);


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
                                                threshold: 0.02
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
      //--------------wbes
      //0      id,
      // 1       id_layout,
      //   2      name,
      //     3     pos_x, 
      //       4   pos_y,
      //       5    width, 
      //       6    height,
      //       7      update_time, 
      //       8      graph_type;
      //   9      Array[8]
      //0: id
      //1: id_wallboard
      //2: codigo_query
      //3: opcao_query
      //4: tempo
      //5: user
      //6: user_group
      //7: campaign_id
      //8:linha_inbound
      //9: mode
      //10: status_feedback
      //11: chamadas
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
      panel.append($("<div>").attr("style", "height:98%;font-size:" + font_size + "px;background-color: rgb(210, 215, 215); padding-left:1%;padding-right:1%;padding-top:1%;").attr("data-t", "tooltip").attr("title", "Tempo de Actualização: " + (wbe[7] / 1000) + " seg.")


              .append($("<div>").append($("<label>").addClass("inbound_title").text(wbe[9][0].param1)))//titulo do inbound



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
            var querie = 'SELECT a.status  FROM `vicidial_live_agents` a left join vicidial_live_inbound_agents b on a.user=b.user where closer_campaigns  like "% ' + wbe[9][0].linha_inbound + ' %"';
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
            $.post("Requests.php", {action: wbe[8], group_id: wbe[9][0].linha_inbound},
            function(data)
            {
                  /* (chamadas_efectuadas => $callsTODAY, 
                   * chamadas_perdidas => $dropsTODAY
                   * ,chamadas_atendidas => $answersTODAY,
                   * tma1=>$PCThold_sec_stat_one,
                   * tma2=>$PCThold_sec_stat_two,
                   * tme_chamadas_atendidas=>$AVGhold_sec_answer_calls,
                   * tme_chamadas_perdidas=>$AVGhold_sec_drop_calls,
                   * tme_todas_chamadas=>$AVGhold_sec_queue_calls);*/
                  var tma1 = data[0].tma1;
                  var tma2 = data[0].tma2;
                  var tme_todas_chamadas = data[0].tme_todas_chamadas;
                  $.post("Requests.php", {action: "inbound_groups_info", group_id: wbe[9][0].linha_inbound},
                  function(data1)
                  {
                        answer_sec_pct_rt_stat_one = data1[0].answer_sec_pct_rt_stat_one;
                        answer_sec_pct_rt_stat_two = data1[0].answer_sec_pct_rt_stat_two;

//update dos valores na table 
                        var tma1_element = document.getElementById("tma1" + id);
                        tma1_element.innerHTML = tme_todas_chamadas + "s";
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
                        if (tma1 > 0)
                              sla1.innerHTML = Math.round((answer_sec_pct_rt_stat_one * 100) / tma1).toFixed() + "%";
                        else
                              sla1.innerHTML = 0;
                        var sla2 = document.getElementById("sla2" + id);
                        if (tma2 > 0)
                              sla2.innerHTML = Math.round((answer_sec_pct_rt_stat_two * 100) / tma2).toFixed() + "%";
                        else
                              sla2.innerHTML = 0;
                        var painel = $("#plot_inbound" + id);
                        var data = [
                              {label: 'Cumprido', data: [[1, tma1], [2, tma2]]},
                              {label: 'bar', data: [[1, answer_sec_pct_rt_stat_one], [2, answer_sec_pct_rt_stat_two]]}];
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
                  updation = setTimeout(get_values_inbound, wbe[7]);
            }, "json");
      }
}
//øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø

function   dataTable_top(data)
{
      //--------------wbes
      //0      id,
      // 1       id_layout,
      //   2      name,
      //     3     pos_x, 
      //       4   pos_y,
      //       5    width, 
      //       6    height,
      //       7      update_time, 
      //       8      graph_type;
      //   9      Array[8]

      //0: id
      //1: id_wallboard
      //tempo
      //campanha
      //grupo_inbound
      //grupo_user
      //status_feedback
      //limit
      //custom_colum_name
      var wbe = data;
   var updation;

      var feedbacks = wbe[9][0].status_feedback.split(',');

      var panel = $("#" + wbe[0] + "Main");
      panel.empty();
      panel.append($("<div>")
              .append($("<div>").addClass("grid-title")
              .append($("<div>").addClass("pull-left")
              .text(wbe[2])))
              .append($("<table>").css("heigth", "100%").css("width", "100%").addClass("table table-striped table-mod")
              .append($("<thead>")
              .append($("<tr>")
              .append($("<td>").text("Nome"))
              .append($("<td>").text(wbe[9][0].custom_colum_name))
              .append($("<td>").text("TMA"))


              )


              )//fim do thead
              .append($("<tbody>").attr("id", "tbody_id" + wbe[0])


              )//fim do tbody
              )//fim da table
              );//fim da div

      var Opcao = 0;
      if (wbe[9][0].campanha != "0")
            Opcao = 1;
      if (wbe[9][0].grupo_user != "0")
            Opcao = 2;
      if (wbe[9][0].grupo_inbound != "0")
            Opcao = 3;

      var feedbacks_string = "";
      if (feedbacks.length > 1) {
            feedbacks_string = "status='" + feedbacks[0] + "'";
            for (var i = 1; i < feedbacks.length; i++) {
                  feedbacks_string = feedbacks_string + " or status='" + feedbacks[i] + "'";
            }
      }
      else
            feedbacks_string = "status='" + feedbacks[0] + "'";

//////////TESTAR MANDAR STATUS A 1 CASO SEJA ALL DO OUTRO LADO

      get_values_inbound();
      function get_values_inbound()
      {
            $.post("Requests.php",
                    {action: "5", status: feedbacks_string, opcao: Opcao, tempo: wbe[9][0].tempo, campaign_id: wbe[9][0].campanha, user_group: wbe[9][0].grupo_user, linha_inbound: wbe[9][0].grupo_inbound, limit: wbe[9][0].limit},
            function(data)
            {
                  if (data === null)
                  {
                     if(updation!="")
                        clearTimeout(updation);
                        panel.remove();
                        $("#" + wbe[0] + "Main").remove();
                        $.jGrowl("A tabela" + wbe[2] + " não apresenta resultados", {life: 10000});
                        return false;
                  }



                  var tbody = $("#tbody_id" + wbe[0]);
                  tbody.empty();

                  var letter_size = 18;
                  $.each(data, function(index, value) {




//calculo do TMA de segundos para hora:minuto:segundo
                        var totalSec = data[index].tma;
                        var hours = parseInt(totalSec / 3600) % 24;
                        var minutes = parseInt(totalSec / 60) % 60;
                        var seconds = totalSec % 60;
                        if (hours === 0)
                              var result = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
                        else if (minutes === 0 && hours === 0)
                              var result = (seconds < 10 ? "0" + seconds : seconds);
                        else
                              var result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);




                        tbody.append($("<tr>").css("font-size", letter_size + "px")
                                .append($("<td>").text(data[index].user))//fim do td)
                                .append($("<td>").text(data[index].count_feedbacks))
                                .append($("<td>").text(result))
                                );//fim do tr 
                        letter_size--;


                  });
            }
            , "json");

            updation = setTimeout(get_values_inbound, wbe[7]);


      }
      /*dataTable
       // top 
       //5 ou 10
       //top man tem q ser maior
       
       
       escolher feedback, ou soma de feedbacks
       
       user/resultado/tma/nºchamadas
       
       tma=> tempo medio em chamada
       
       escolher por
       campanha
       ou
       grupo inbound
       ou
       grupo user
       **/



//vicidial_users tem o nome completo do user e as closer_campaigns(linha_inbound)







}

//window exit
$(window).bind('beforeunload', function() {
      $("#MainLayout .PanelWB").remove();
}
);

