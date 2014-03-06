



var wbes = [];
var layout;



function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
    });
    return vars;
}






$(document).ready(function() {


    $("[data-t=tooltip]").tooltip({placement: "left", html: true});
    $("#MainLayout").css("width", "100%").css("height", "100%").css("position", "absolute").css("background-color", "#F5F5F5").css("font-size", "1.35em");


    layout = getUrlVars()["id"];
    ; //parameter
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
            var left = (wbes[i][3] * temp_window.width()) / 904;
            var top = (wbes[i][4] * temp_window.height()) / 512;
            var width = (wbes[i][5] * temp_window.width()) / 904;
            var height = (wbes[i][6] * temp_window.height()) / 512;

            $("#MainLayout")
                    .append($("<div>").addClass("PanelWB ui-widget-content")
                            .css("position", "absolute")
                            .css("left", left + "px")
                            .css("top", top + "px")
                            .css("height", height + "px")
                            .css("width", width + "px")
                            .data("id", wbes[i][0] + "Main")
                            .attr("id", wbes[i][0] + "Main")
                            .draggable({containment: '#MainLayout'})
                            .append($("<div>").addClass("grid-title")
                                    .append($("<div>").addClass("pull-left").text(wbes[i][2]))
                                    .append($("<div>").addClass("pull-right-letter_button ").attr("data-t", "tooltip").attr("title", "Alteração do tamanho de letra")
                                            .append($("<a>").addClass("btn btn-info icon-text-height").attr("id", "letter_size_popover" + wbes[i][0]).attr("data-toggle", "popover")
                                                    .attr("data-content", "<div class='btn-group'><button class='btn btn-primary icon-plus increase_em'></button><button  class='btn icon-minus decrease_em'></button></div>")))
                                    .append($("<div>").addClass("pull-right").attr("id", "right_title" + wbes[i][0])))
                            .append($("<div>").addClass("grid-content").attr("id", wbes[i][0] + "WBEGD")
                                    .append($("<div>").attr("id", wbes[i][0] + "WBE").css("width", (width - 20) + "px").css("height", (height - 75) + "px").css("padding", "0px").attr("data-t", "tooltip").attr("title", "Tempo de Actualização: " + (wbes[i][7] / 1000) + " seg.")))
                            );
            if (wbes[i][8] !== "5") {
                if ($.cookie(wbes[i][0] + "Main") > 0) {
                    $("#" + wbes[i][0] + "Main").data("letter_size", $.cookie(wbes[i][0] + "Main"));
                    $("#" + wbes[i][0] + "Main").css("font-size", +$.cookie(wbes[i][0] + "Main"));

                } else {
                    $("#" + wbes[i][0] + "Main").data("letter_size", "18");
                    $.cookie(wbes[i][0] + "Main", 18);
                }
            }


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
                $("#" + wbes[i][0] + "Main").css("height", "auto");
                dataTable_top(wbes[i]);
            }
            $('#letter_size_popover' + wbes[i][0]).popover({html: true});


            i++;
        });

    }, "json");


    setInterval("location.reload(true)", 120000);




});





$(document).on("click", ".increase_em", function(e) {

    var b = $(this).closest(".PanelWB");
    b.data().letter_size = (+b.data().letter_size) + 1;
    b.css("font-size", b.data().letter_size);
    $.cookie(b.data().id, b.data().letter_size);
});
$(document).on("click", ".decrease_em", function(e) {

    var b = $(this).closest(".PanelWB");
    b.data().letter_size = (+b.data().letter_size) - 1;
    b.css("font-size", b.data().letter_size);
    $.cookie(b.data().id, b.data().letter_size);
});
$(document).on("click", "#increase_em_datatop", function(e) {

    var b = $(this).closest(".PanelWB").closest("div");
    b.data().letter_size_datatop = (+b.data().letter_size_datatop) + 0.07;
    $.cookie(b.data().id, b.data().letter_size_datatop);
    var temp = +b.data().letter_size_datatop;
    b.find('tbody tr').each(function() {

        $(this).css("font-size", temp + "em");
        temp = temp + -0.1;
    });
});
$(document).on("click", "#decrease_em_datatop", function(e) {

    var b = $(this).closest(".PanelWB").closest("div");
    b.data().letter_size_datatop = b.data().letter_size_datatop - 0.07;
    $.cookie(b.data().id, b.data().letter_size_datatop);
    var temp = +b.data().letter_size_datatop;
    b.find('tbody tr').each(function() {

        $(this).css("font-size", temp + "em");
        temp = temp - 0.1;
    });
});



$(document).on("mouseenter", ".PanelWB", function(e) {
    $(".pull-right-letter_button").stop().fadeIn(600);
});
$(document).on("mouseleave", ".PanelWB", function(e) {
    $(".pull-right-letter_button").hide();
});

//BAR GRAPh ««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««««
function plot_bar(data)
{
    var plot;
    var data2 = [];
    var ticksA = [];
    var updation;
    var wbe = data;
    var painel = $("#" + wbe[0] + "WBE");
    var information = [];
    var max_y = 0;
    var colors = new Array("red", "green", "blue", "orange", "black");
    get_values_bar();
    function get_values_bar()
    {
        $.post("Requests.php", {action: "2", datasets: wbe[9]},
        function(data)
        {
            if (data === null)
            {
                clearTimeout(updation);
                painel.remove();
                $("#" + wbe[0] + "Main").remove();
                $.jGrowl("O gráfico de Barras " + wbe[2] + " não apresenta resultados", {sticky: 10000});
                return false;
            }
            data2 = [];
            var i = 0;
            var label = "não definido";
            var sum = 0;
            $.each(data, function(index, value) {
                if (wbe[9][index].chamadas === "0")
                    label = wbe[9][index].param1 + " por " + wbe[9][index].param2;
                else
                    label = "Chamadas " + wbe[9][index].chamadas + " por " + wbe[9][index].param1;
                sum = +data[index];
                if (sum <= 0)
                {
                    if (wbe[9][index].hasData)
                    {
                        $.jGrowl("A barra de " + label + " não apresenta resultados", {sticky: true});
                        wbe[9][index].hasData = false;
                    }
                    information.push(
                            {
                                label: "sem dados",
                                data: 0,
                                bars: {show: true, barWidth: 0.8, align: "center"},
                                color: colors[index]
                            });
                }
                else
                {
                    wbe[9][index].hasData = true;
                    ticksA.push([i, +data[index]]);
                    data2.push([i, +data[index]]);
                    if (+data[index] > max_y)
                    {
                        max_y = +data[index];
                    }
                    information.push(
                            {
                                label: label,
                                data: data2,
                                bars: {show: true, barWidth: 0.8, align: "center", fill: 0.6},
                                color: colors[index]
                            });
                    sum = 0;
                    i++;
                }

                data2 = [];
            });
            max_y = (max_y * 100) / 75;
            $.plot(painel, information, {xaxis: {ticks: ticksA}, yaxis: {min: 0, max: max_y}, legend: {show: true}});
            max_y = 0;
            information = [];
            ticksA = [];
            data2 = [];
            updation = setTimeout(get_values_bar, wbe[7]);
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

            var verifier = 0; //verifica se o dataset tem algum lead_id>0
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
                            result.push([temp, obj[prop][k].leads]);
                        }
                        var label_text = "";
                        if (verifier > 0) {
                            if (wbe[9][aux].chamadas === "0") {
                                if (wbe[9][aux].status_feedback === "1")
                                    label_text = "Todos os feedbacks de " + wbe[9][aux].param1;
                                else
                                    label_text = wbe[9][aux].param1 + " de " + wbe[9][aux].param2;
                            } else
                                label_text = "Chamadas " + wbe[9][aux].chamadas + " de " + wbe[9][aux].param1;
                            information.push({data: result, label: label_text});
                            result = [];
                            verifier = 0;
                            wbe[9][aux].hasData = true;
                        }
                        else
                        {
                            result = [];
                            information.push({data: 0, label: "Sem resultados"});
                            if (wbe[9][aux].hasData)
                            {
                                $.jGrowl("A Linha " + wbe[9][aux].opcao_query + " do grafico " + wbe[2] + " não apresenta resultados", {sticky: true});
                                wbe[9][aux].hasData = false;
                            }
                            verifier = 0;
                        }
                    }
                    aux++;
                }
            }


            max_y = (max_y * 100) / 65;
            var tick_size = Math.round(max_y / 10);
            var options = {
                series: {shadowSize: 0, show: true}, // drawing is faster without shadows
                yaxis: {min: 0, max: max_y, tickSize: tick_size},
                xaxis: {mode: "time", minTickSize: [5, "minute"],
                    min: (dates[0]),
                    max: (dates[dates.length - 1])
                }

            };
            plot = $.plot(painel, information, options);
            var temp = 9;
            switch (plot.getData().length)
            {
                case 1:
                    temp = 5;
                    break;
                case 2:
                    temp = 6;
                    break;
                case 3:
                    temp = 7;
                    break;
                case 4:
                    temp = 8;
                    break;
                case 5:
                    temp = 9;
                    break;
            }


            for (var aa = 0; aa < plot.getData().length; aa++)
            {
                plot.getData()[aa].lines.lineWidth = temp;
                temp = temp - 3 + aa;
            }
            plot.draw();
            max_y = 0;
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
    var painer_content = $("#" + wbe[0] + "WBEGD");
    painer_content.css("overflow-y", "auto").css("overflow-x", "hidden");
    if (wbe[9][0].status_feedback === "1")
        var feedbacks_string = "1";
    else
    {
        var feedbacks = wbe[9][0].status_feedback.split(',');
        var feedbacks_string = "";
        if (feedbacks.length > 1) {
            feedbacks_string = "status='" + feedbacks[0] + "'";
            for (var i = 1; i < feedbacks.length; i++) {
                feedbacks_string = feedbacks_string + " or status='" + feedbacks[i] + "'";
            }
        }
        else
            feedbacks_string = "status='" + feedbacks[0] + "'";
    }
    var right_title = $("#right_title" + wbe[0]);
    right_title.text(wbe[9][0].param2 + " / " + wbe[9][0].param1);
    get_values_pie();
    function get_values_pie()
    {
        var data1 = [];
        $.post("Requests.php",
                {action: "3", status: feedbacks_string, opcao: wbe[9][0].codigo_query, tempo: wbe[9][0].tempo, campaign_id: wbe[9][0].campaign_id, user_group: wbe[9][0].user_group, linha_inbound: wbe[9][0].linha_inbound, user: wbe[9][0].user},
        function(data)
        {
            if (data === null)
            {
                clearTimeout(updation);
                painel.remove();
                $("#" + wbe[0] + "Main").remove();
                $.jGrowl("O gráfico de Tarte " + wbe[2] + " não apresenta resultados", {life: 20000});
                return false;
            }
            var i = 0;
            $.each(data, function(index, value) {
                data1.push({label: ((this.count) + " -- " + this.status_name), data: +this.count});
                i++;
            });
            if (i === 0)//se so houver 1 resultado ele n faz render, entao adiciona-se 1 elemento infimo
                data1.push({label: ("zero"), data: 0.001});
            $.plot(painel, data1, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        combine: {
                            color: '#999',
                            threshold: 0.01,
                            label: "Outros"},
                        label: {
                            show: true,
                            radius: 2 / 3,
                            formatter: function(label, series) {
                                return '<div style="font-size:11px;text-align:center;color:black;"><label class="label label-info">' + Math.round(series.percent) + '%</label></div>';
                            },
                            threshold: 0.02
                        }
                    }
                },
                legend: {
                    show: true
                },
                grid: {
                    hoverable: false,
                    clickable: false
                }});
            updation = setTimeout(get_values_pie, wbe[7]);
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

    var panel = $("#" + wbe[0] + "Main");
    var font_size = ((panel.width() / 50) + (panel.height() / 110));
    panel.empty();
 
    //Outbound
    if (data[9][0]["param1"] == 1)
    {
        panel.append($("<div>").css("height", "98%").css("font-size", font_size + "px").css("background-color", "rgb(210, 215, 215)").css("padding-left", "1%").css("padding-right", "1%").css("padding-top", "1%").addClass("legend_inbound").attr("data-t", "tooltip").attr("title", "Tempo de Actualização: " + (wbe[7] / 1000) + " seg.")
                .append($("<div>").append($("<label>").addClass("inbound_title").text(wbe[2])))//titulo do inbound
                .append($("<table>").css("height", "80%").css("width", "100%")
                        //top                    
                        .append($("<tr>")
                                .append($("<td>")
                                        .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Chamadas Efectuadas")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "chamadas_efectuadas" + id)))))
                                .append($("<td>")
                                        .append($("<div>").addClass(" inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Chamadas Atendidas")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "chamadas_atendidas" + id)))))
                                .append($("<td>")
                                        .append($("<div>").addClass("inbound_grid_div_large")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Chamadas não Atendidas")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "chamadas_n_atendidas" + id))))))
                         //left/right    
                        .append($("<tr>")
                                //graph
                                 .append($("<tr>")
                                .append($("<td>")
                                        .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("TMA")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "tma1" + id))))))

                                .append($("<td>").css("vertical-align", "top")
                                        .append($("<div>").css("top", "80%").css("right", "3%").attr("id", "legend_div" + id).css("position", "absolute").css("z-index", "10").css("background-color", "#FFFFFF").css("opacity", "0.75"))
                                        .append($("<div>").attr("style", "width:66%;height:45%;position:absolute; ").attr("id", "plot_inbound" + id))))

 
                       
                        ));
        get_values_outbound();
    }
//inbound
    else
    {

        panel.append($("<div>").css("height", "98%").css("font-size", font_size + "px").css("background-color", "rgb(210, 215, 215)").css("padding-left", "1%").css("padding-right", "1%").css("padding-top", "1%").addClass("legend_inbound").attr("data-t", "tooltip").attr("title", "Tempo de Actualização: " + (wbe[7] / 1000) + " seg.")

                .append($("<div>").append($("<label>").addClass("inbound_title").text(wbe[2])))//titulo do inbound
                .append($("<table>").css("height", "80%").css("width", "100%")

                        //top                    
                        .append($("<tr>")



                                .append($("<td>")
                                        .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Chamadas Recebidas")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "chamadas_totais" + id)))))

                                .append($("<td>")
                                        .append($("<div>").addClass(" inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Chamadas Atendidas")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "chamadas_atendidas" + id)))))


                                .append($("<td>")
                                        .append($("<div>").addClass("inbound_grid_div ")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Chamadas Perdidas")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "chamadas_perdidas" + id)))))


                                .append($("<td>")
                                        .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("Chamadas em Espera")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "chamadas_espera" + id))))))



                        //left/right    
                        .append($("<tr>")
                                //graph
                                .append($("<td>")
                                        .append($("<div >").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("SLA1").attr("id", "sla1_title" + id)))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "sla1" + id)))))

                                .append($("<td>").css("vertical-align", "top")
                                        .append($("<div>").css("top", "80%").css("right", "1%").attr("id", "legend_div" + id).css("position", "absolute").css("z-index", "10").css("background-color", "#FFFFFF").css("opacity", "0.75"))
                                        .append($("<div>").attr("style", "width:70%;height:55%;position:absolute; ").attr("id", "plot_inbound" + id))))


                        .append($("<tr>")
                                .append($("<td>")
                                        .append($("<div>").addClass("inbound_grid_div")
                                                .append($("<div>").addClass("inbound_grid_title").append($("<label>").text("TMA")))
                                                .append($("<div>").addClass("inbound_grid_content").append($("<label>").attr("id", "tma1" + id))))))
                        ));
        get_values_inbound();
    }





    function get_values_inbound()
    {
        $.post("Requests.php", {action: "get_agents_inbound", linha_inbound: wbe[9][0].linha_inbound},
        function(data1)
        {
            ready = 0;
            queue = 0;
            paused = 0;
            incall = 0;
            var a = 0;
            $.each(data1, function(index, value)
            {
                switch (data1[a])
                {
                    case "READY":
                        ready++;
                        break;
                    case "CLOSER":
                        ready++;
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
            $.post("Requests.php", {action: "get_calls_queue", linha_inbound: wbe[9][0].linha_inbound},
            function(data4)
            {
                $.each(data4, function(index, value)
                {
                    queue = +data4[0];
                });
                $.post("Requests.php", {action: "4_inbound", linha_inbound: wbe[9][0].linha_inbound},
                function(data3)
                {

                    var chamadas_recebidas = data3[0].chamadas_recebidas;
                    var tma1 = data3[0].tma1;


                    var chamadas_atendidas_val = data3[0].chamadas_atendidas;
                    var chamadas_perdidas_val = data3[0].chamadas_perdidas;

                    var tma_todas_chamadas = 0;

                    if (+data3[0].tma > 0)
                    {
                        var totalSec = +data3[0].tma;
                        totalSec = Math.round(totalSec / chamadas_atendidas_val);
                        if (/^\d+$/.test(totalSec))
                            tma_todas_chamadas = secondsToString(totalSec);

                    }

                    $.post("Requests.php", {action: "inbound_groups_info", group_id: wbe[9][0].linha_inbound},
                    function(data5)
                    {

                        answer_sec_pct_rt_stat_one = +data5[0].answer_sec_pct_rt_stat_one;
                        answer_sec_pct_rt_stat_two = +data5[0].answer_sec_pct_rt_stat_two;
//update dos valores na table 

                        var chamadas_totais_obj = document.getElementById("chamadas_totais" + id);
                        chamadas_totais_obj.innerHTML = +chamadas_recebidas;

                        var chamadas_atendidas_obj = document.getElementById("chamadas_atendidas" + id);
                        chamadas_atendidas_obj.innerHTML = +chamadas_atendidas_val;
                        var chamadas_perdidas_obj = document.getElementById("chamadas_perdidas" + id);

                        if (chamadas_perdidas_val !== "0" && (chamadas_perdidas_val / chamadas_recebidas) > 0)
                            chamadas_perdidas_obj.innerHTML = chamadas_perdidas_val + "-" + Math.round((chamadas_perdidas_val / chamadas_recebidas) * 100) + "%";
                        else
                            chamadas_perdidas_obj.innerHTML = chamadas_perdidas_val + "- 0%";
                        var chamadas_espera_obj = document.getElementById("chamadas_espera" + id);
                        chamadas_espera_obj.innerHTML = queue;
                        var tma1_element = document.getElementById("tma1" + id);
                        tma1_element.innerHTML = tma_todas_chamadas;
                        var sla1 = document.getElementById("sla1" + id);
                        var sla1_title = document.getElementById("sla1_title" + id);
                        if (tma1 > 0)
                        {
                            sla1.innerHTML = Math.round(tma1) + "%";
                            sla1_title.innerHTML = "SLA1->" + Math.round(answer_sec_pct_rt_stat_one) + "sec";
                        }
                        else
                            sla1.innerHTML = 0;

                        var painel = $("#plot_inbound" + id);
                        var data_array = [];
                        data_array.push({label: ready + " - Agentes Disponiveis", data: ready});
                        data_array.push({label: paused + " - Agentes Indisponiveis", data: paused});
                        data_array.push({label: incall + " - Agentes em Chamada", data: incall});
                        if ((ready + queue + paused + incall) == "0")
                        {

                            data_array = [];
                            data_array.push({label: ("Sem agentes online"), data: 0});
                        }

                        var temp = 0;
                        $.plot(painel, data_array, {
                            series: {
                                pie: {
                                    innerRadius: 0.06,
                                    show: true,
                                    radius: ($("#MainLayout").width() - $("#MainLayout").height()),
                                    label: {
                                        show: true,
                                        formatter: function(label, series) {

                                            return '<div style="float:rigth;font-size:18px;color:black;">' + Math.round(series.percent) + '%</div>';
                                        },
                                        background: {
                                            opacity: 0.5,
                                            color: '#FFFFFF'
                                        }
                                    }
                                }
                            },
                            legend: {
                                show: true,
                                container: $("#legend_div" + wbe[0])
                            },
                            grid: {
                                hoverable: false,
                                clickable: false
                            }});
                    }, "json");
                }, "json");
            }, "json");
            updation = setTimeout(get_values_inbound, wbe[7]);
        }, "json");
    }
    function get_values_outbound()
    {

        $.post("Requests.php", {action: "get_agents_outbound", campanha: wbe[9][0].linha_inbound},
        function(data1)
        {
            ready = 0;
            queue = 0;
            paused = 0;
            incall = 0;
            var a = 0;
            $.each(data1, function(index, value)
            {
                switch (data1[a])
                {
                    case "READY":
                        ready++;
                        break;
                    case "CLOSER":
                        ready++;
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
            $.post("Requests.php", {action: "4_outbound", campanha: wbe[9][0].linha_inbound},
            function(data3)
            {
                var chamadas_efectuadas = data3[0].chamadas_efectuadas;
                var tma1 = data3[0].tma1;
                var chamadas_atendidas = data3[0].chamadas_atendidas;
                var chamadas_n_atendidas = data3[0].chamadas_n_atendidas;
                var tma_todas_chamadas = 0;
                if (+data3[0].tma > 0)
                {
                    var totalSec = +data3[0].tma;
                  
                    totalSec = Math.round(totalSec / chamadas_atendidas); 
               
                    if (/^\d+$/.test(totalSec))
                        tma_todas_chamadas = secondsToString(totalSec);
 
                }  






                $.post("Requests.php", {action: "inbound_groups_info", group_id: wbe[9][0].linha_inbound},
                function(data5)
                {

                    answer_sec_pct_rt_stat_one = +data5[0].answer_sec_pct_rt_stat_one;
                    answer_sec_pct_rt_stat_two = +data5[0].answer_sec_pct_rt_stat_two;
//update dos valores na table 

                    var chamadas_efectuadas_obj = document.getElementById("chamadas_efectuadas" + id);
                    chamadas_efectuadas_obj.innerHTML = +chamadas_efectuadas;

                    var chamadas_atendidas_obj = document.getElementById("chamadas_atendidas" + id);
                    chamadas_atendidas_obj.innerHTML = +chamadas_atendidas;
                    var chamadas_n_atendidas_obj = document.getElementById("chamadas_n_atendidas" + id);

                    if (chamadas_n_atendidas !== "0" && (chamadas_n_atendidas / chamadas_efectuadas) > 0)
                        chamadas_n_atendidas_obj.innerHTML = chamadas_n_atendidas + "-" + Math.round((chamadas_n_atendidas / chamadas_efectuadas) * 100) + "%";
                    else
                        chamadas_n_atendidas_obj.innerHTML = chamadas_n_atendidas + "- 0%";

                    var tma1_element = document.getElementById("tma1" + id);
                    tma1_element.innerHTML = tma_todas_chamadas;


                    var painel = $("#plot_inbound" + id);
                    var data_array = [];
                    data_array.push({label: ready + " - Agentes Disponiveis", data: ready});
                    data_array.push({label: paused + " - Agentes Indisponiveis", data: paused});
                    data_array.push({label: incall + " - Agentes em Chamada", data: incall});
                    if ((ready + queue + paused + incall) == "0")
                    {

                        data_array = [];
                        data_array.push({label: ("Sem agentes online"), data: 0});
                    }

                    var temp = 0;
                    $.plot(painel, data_array, {
                        series: {
                            pie: {
                                innerRadius: 0.06,
                                show: true,
                                radius: ($("#MainLayout").width() - $("#MainLayout").height()),
                                label: {
                                    show: true,
                                    formatter: function(label, series) {

                                        return '<div style="float:rigth;font-size:18px;color:black;">' + Math.round(series.percent) + '%</div>';
                                    },
                                    background: {
                                        opacity: 0.5,
                                        color: '#FFFFFF'
                                    }
                                }
                            }
                        },
                        legend: {
                            show: true,
                            container: $("#legend_div" + wbe[0])
                        },
                        grid: {
                            hoverable: false,
                            clickable: false
                        }});
                }, "json");
            }, "json");
        }, "json");
        updation = setTimeout(get_values_outbound, wbe[7]);

    }
}
//øøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøøø

function   dataTable_top(data)
{
    var wbe = data;
    var updation;
    if (wbe[9][0].status_feedback === "1")
        var feedbacks_string = "1";
    else
    {
        var feedbacks = wbe[9][0].status_feedback.split(',');
        var feedbacks_string = "";
        if (feedbacks.length > 1) {
            feedbacks_string = "status='" + feedbacks[0] + "'";
            for (var i = 1; i < feedbacks.length; i++) {
                feedbacks_string = feedbacks_string + " or status='" + feedbacks[i] + "'";
            }
        }
        else
            feedbacks_string = "status='" + feedbacks[0] + "'";
    }
    var panel = $("#" + wbe[0] + "Main");
    panel.empty();
    panel.append($("<div>").css("overflow", "hidden")
            .append($("<div>").addClass("grid-title")
                    .append($("<div>").addClass("pull-right").text(wbe[9][0].param1))
                    .append($("<div>").addClass("pull-right-letter_button").attr("data-t", "tooltip").attr("title", "Alteração do tamanho de letra")
                            .append($("<a>")
                                    .addClass("btn btn-info  icon-text-height")
                                    .attr("id", "letter_size_popover" + wbe[0])
                                    .attr("data-toggle", "popover")
                                    .attr("data-content", "<div class='btn-group'><button id='increase_em_datatop' class='btn btn-primary icon-plus'></button><button id='decrease_em_datatop' class='btn icon-minus'></button></div>")))
                    .append($("<div>").addClass("pull-left").text(wbe[2])))
            .append($("<table>").addClass("table table-striped table-mod").css("heigth", "100%").css("width", "100%")
                    .append($("<thead>")
                            .append($("<tr>")
                                    .append($("<td>").text("Nome"))
                                    .append($("<td>").text(wbe[9][0].custom_colum_name))
                                    .append($("<td>").text("TMA"))))
                    .append($("<tbody>").attr("id", "tbody_id" + wbe[0])
                            )));


    if ($.cookie(wbe[0] + "Main") > 0) {
        panel.data("letter_size_datatop", $.cookie(wbe[0] + "Main"));
    } else {
        panel.data("letter_size_datatop", "1.2");
        $.cookie($.cookie(wbe[0] + "Main"), 1.2);
    }

    var Opcao = 0;
    if (wbe[9][0].campanha != "0")
        Opcao = 1;
    if (wbe[9][0].grupo_user != "0")
        Opcao = 2;
    if (wbe[9][0].grupo_inbound != "0")
        Opcao = 3;
    $('#letter_size_popover' + wbe[0]).popover({html: true});
    get_values_dataTop();
    function get_values_dataTop()
    {
        $.post("Requests.php",
                {action: "5", status: feedbacks_string, opcao: Opcao, tempo: wbe[9][0].tempo, campaign_id: wbe[9][0].campanha, user_group: wbe[9][0].grupo_user, linha_inbound: wbe[9][0].grupo_inbound, limit: wbe[9][0].limit, mode: wbe[9][0].mode},
        function(data)
        {
            if (data === null)
            {
                if (updation != "")
                    clearTimeout(updation);
                panel.remove();
                $("#" + wbe[0] + "Main").remove();
                $.jGrowl("A tabela " + wbe[2] + " não apresenta resultados", {life: 5000});
                return false;
            }
            var tbody = $("#tbody_id" + wbe[0]);
            tbody.empty();
            var letter_size = +panel.data().letter_size_datatop;

            $.each(data, function(index, value) {
//calculo do TMA de segundos para hora:minuto:segundo
                var totalSec = +data[index].tma;
                var total_feedbacks = +data[index].count_feedbacks;
                totalSec = Math.round(totalSec / total_feedbacks);
                var hours = parseInt(totalSec / 3600) % 24;
                var minutes = parseInt(totalSec / 60) % 60;
                var seconds = totalSec % 60;
                if (hours === 0)
                    var result = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
                else if (minutes === 0 && hours === 0)
                    var result = (seconds < 10 ? "0" + seconds : seconds);
                else
                    var result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
                if (+data[index].count_feedbacks > 0)
                {
                    tbody.append($("<tr>").css("font-size", letter_size + "em")
                            .append($("<td>").text(data[index].user))
                            .append($("<td>").text(data[index].count_feedbacks).css("text-align", "center"))
                            .append($("<td>").text(result)));
                    letter_size = letter_size - 0.03;

                }
            });
        }
        , "json");
        updation = setTimeout(get_values_dataTop, wbe[7]);
    }
}



function secondsToString(seconds)
{

var numdays = Math.floor(seconds / 86400);
var numhours = Math.floor((seconds % 86400) / 3600);
var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
var numseconds = ((seconds % 86400) % 3600) % 60;

return numminutes + " : " + numseconds ;

}