
$(function() {

    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
            vars[key] = value;
        });
        return vars;
    }



    $('#downloadTemplate').click(function() {

        var url = "../constructor.php?templateId=" + $('#selectTemplateList').val() + "&action=templateDownload";

        document.location.href = url;

    });


    $.post("../constructor.php", {action: 'getTemplateList'}, function(data) {

        var options = [];
        $.each(data, function() {

            options.push(new Option(this.name, this.id_template));

        });

        $("#selectTemplateList").append(options).trigger("chosen:updated");
        $('#selectTemplateList').val(getUrlVars().templateId);
        $('#selectTemplateList').change();

    }, 'json');

    //console.log(getUrlVars().templateId);

    function graphConstruct(data, selector, name) {

        var obj = {
            "xScale": "ordinal",
            "yScale": "linear",
            "main": [
                {
                    "className": name,
                    "data": data}
            ]
        };
     
        return new xChart("bar", obj, selector);

    }
    ;


    $('#selectTemplateList').on('change', function() {

        $.post("../constructor.php", {action: 'constructPreview', templateId: $('#selectTemplateList').val()}, function(data) {

            browserPreview(data);

        }, 'json');

    });
    
    //.append($("<div>"),{class:"btn btn-large btn-inverse"})
    /* $.getJSON('../rphandle.json', function(data) {
     console.log(data);
     });*/

    function browserPreview(data) {

        $('.agrupador').remove();

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
                                    ))


                            ).find("ul");


            //dentro desse  abjecto existe um array com uma lista de  objectos, for each para percorrer essa lista. 
            $.each(this.values, function() {
                //array que esta a ser feito o push(inserção de informação) com nome e o valor de cada objecto do array
                if (this.value == 'n/a') {
                    this.value=0;
                }
                graph.push({"x": this.name.substring(0, 12) + "...", "y": this.value});

                // continuação da construção da pagina em Jquery
                tmp.append(
                        $("<li>")
                        // nome do objecto
                        .append(
                                $("<span>", {class: "number"}).append(this.value))// valor desse objecto
                        .append(":" + this.name)
                        );
            });

            //.append($("<button>"), {class:"btn btn-large btn-inverse"});


            $("#content").append(tmp.end());
            /* vai chamar o função para criar o grafico, no graph vai a lista de objectos, cada objecto  com 1 nome e valor, 
             o #graph+index á o id do grafico, nome do grafico */
            var x = graphConstruct(graph, "#graph" + index, ".graph" + index);
        });

    }

});



