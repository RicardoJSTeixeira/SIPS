
$(function() {

    $('#downloadTemplate').click(function() {

        var url = "../constructor.php?templateId=" + $('#selectTemplateList').val() + "&action=templateDownload";
        document.location.href = url;
    });
    $.post("../constructor.php", {action: 'getTemplateListUser'}, function(data) {

        var options = [];
        $.each(data, function() {

            options.push(new Option(this.name, this.id_template));
        });
        $("#selectTemplateList").append(options).trigger("chosen:updated");
    }, 'json');
    function graphConstruct(data, selector, name, status, propertyOf) {

        var obj = {
            "xScale": "ordinal",
            "yScale": "linear",
            "main": [
                {
                    "className": name,
                    "data": data

                }
            ]
        };
        var tt = document.createElement('div'),
                leftOffset, //= -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset; // = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        tt.style.zIndex = 1000;
        var opts = {
            "tickHintX": 10,
            'yMin': 0,
            "mouseover": function(d, i) {
                var pos = $(this).offset();
                $(tt).text((d.x) + ': ' + d.y)
                        .css({top: currentMousePos.y + 5, left: currentMousePos.x - 2})
                        .show();
            },
            "mouseout": function() {
                tt.style.display = 'none';
            },
            'click': function(d) {
                clickBar(d);
            }
        };
        return new xChart("bar", obj, selector, opts);
    }

    $('#selectTemplateList').on('change', function() {

        $.post("../constructor.php", {action: 'constructPreview', templateId: $('#selectTemplateList').val()}, function(data) {

            browserPreview(data);
        }, 'json');
    });
    function browserPreview(data) {

        $('.agrupador').remove();
        var table = $('<table>', {id: 'my'})
                .append($('<thead>')
                        .append($('<a>').append($('<i>', {class: ' icon-download', title: "Download para ficheiro .XLSX"})))
                        .append($('<tr>')
                                .append($('<th>').text('Agent'))
                                .append($('<th>').text('Calls'))
                                .append($('<th>').text('Length'))
                                ))
                .append($('<tbody>')
                        .append($('<tr>')
                                .append($('<td>').text('exemplo'))
                                )
                        );
        //variaveis
        var
                tmp;
        //for each , vai percorrer o array de objectos do ficheiro rphandle.json
        $.each(data, function(index) {
            //var graph e um array
            graph = [];
            // construção da pagina com Jquery, da tabela
            tmp = $("<div>", {class: "row agrupador"})
                    .append($("<div>", {class: "widget col-sm-3"})
                            .append(
                                    $("<h2>")
                                    .append(
                                            $("<span>", {class: "glyphicons charts"})
                                            .append(
                                                    $("<i>"))
                                            ).append(this.name)// nome do primeiro objecto dentro do array
                                    )
                            .append(
                                    $("<hr>"))
                            .append(
                                    $("<div>", {class: "content"})
                                    .append(
                                            $("<div>", {class: "sparkLineStats"})
                                            .append(
                                                    $("<ul>", {class: "unstyled"}))
                                            )
                                    )

                            )
                    // Jquery do Grafico          
                    .append($("<div>", {class: "col-sm-9"})
                            .append(
                                    $("<div>", {class: "box"})
                                    ).append(($("<div>", {class: "box-header"})).append($("<h2>")).append($("<i>", {class: "icon-bar-chart"})).append($("<span>", {class: "break"})))

                            .append((
                                    $("<div>", {class: "box-content"})
                                    ).append(
                                    $("<figure>", {'class': "demo", id: "graph" + index, style: " height: 400px;"})
                                    ).append($('<table>', {id: 'table' + index, class: 'table table-bordered table-striped'})//.css('display', 'none')

                                    ).append($('<div>', {class: 'clearfix'}))
                                    )

                            ).find("ul");
            //dentro desse  abjecto existe um array com uma lista de  objectos, for each para percorrer essa lista. 
            $.each(this.values, function() {

                //array que esta a ser feito o push(inserção de informação) com nome e o valor de cada objecto do array
                if (this.value == null) {
                    this.value = 0;
                }

                graph.push({"x": this.name.substring(0, 12) + "...", "y": this.value, status: this.status, propertyOf: this.propertyOf, timeStart: data[0].start, timeEnd: data[0].end, tableId: 'table' + index});
                // continuação da construção da pagina em Jquery
                tmp.append(
                        $("<li>")
                        // nome do objecto
                        .append(
                                $("<span>", {class: "number"}).append(this.value))// valor desse objecto
                        .append($('<b>').append(":" + this.name))
                        )
                        ;
            });
            //.append($("<button>"), {class:"btn btn-large btn-inverse"});

            $("#content").append(tmp.end());
            /* vai chamar o função para criar o grafico, no graph vai a lista de objectos, cada objecto  com 1 nome e valor, 
             o #graph+index á o id do grafico, nome do grafico */
            var x = graphConstruct(graph, "#graph" + index, ".graph" + index);
        });
    }
    currentMousePos = {};
    $(document).mousemove(function(event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
    });
    function clickBar(d) {

        var Table_chamadas = $('#' + d.tableId).dataTable({
            "bSortClasses": true,
            "bProcessing": true,
            "bDestroy": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": "../constructor.php",
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "getStatusInfo"},
                {"name": "timeStart", "value": d.timeStart},
                {"name": "timeEnd", "value": d.timeEnd},
                {"name": "campaignId", "value": d.propertyOf},
                {"name": "status", "value": d.status});
            },
            "aoColumns": [{"sTitle": 'Agent'},
                {"sTitle": 'Calls'},
                {"sTitle": 'Length'}
            ],
            /*
             "fnDrawCallback": function(oSettings, json) {
             $('.tooltip_chamadas').tooltip();
             },*/
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });
    }

});



