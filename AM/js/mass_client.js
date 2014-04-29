$(function() {
    var
            me = this,
            zone = $("#mass_client_form"),
            client = getUrlVars();


    this.init = function(zone, rid) {
        var recomendado = rid || "";
        zone.append($("<input>", {type: "hidden", value: recomendado, name: "recomendado"}));

        zone.find("#plus-client").click(function(e) {
            e.preventDefault();

            zone.find("#mass_client_table tbody").append(
                    $("<tr>")
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "ccm[]", placeholder: "", class: "span validate[required]"}))
                            )
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "cname[]", placeholder: "", class: "span validate[required]"}))
                            )
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "cmorada[]", placeholder: "", class: "span " + ((!~~recomendado) ? "validate[required]" : "")}))
                            )
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "clocal[]", placeholder: "", class: "span validate[required]"}))
                            )
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "cpostal[]", placeholder: "", class: "span " + ((!~~recomendado) ? "validate[minSize[4]]" : "")}))
                            )
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "ctel[]", placeholder: "", class: "span validate[required,custom[onlyNumberSp],minSize[9]]"}))
                            )
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "ctlm[]", placeholder: "", class: "span"}))
                            )
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "cemail[]", placeholder: "", class: "span validate[custom[email]]"}))
                            )
                    .append($("<td>")
                            .append($("<input>", {type: "text", name: "cbd[]", placeholder: "", readonly: true, class: ((!~~recomendado) ? "validate[required]" : "")})
                                    .datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2, initialDate: new Date(moment().subtract('years', 65).format())}))
                            )
                    .append($("<td>")
                            .append($("<select>", {name: "ctoissue[]", class: "span validate[required]"}).append([new Option("Seleccione uma Opção", ""), new Option("CallCenter", "YES"), new Option("Dispenser", "NO")]))
                            )
                    .append($("<td>")
                            .append($("<button>", {class: "btn btn-danger icon-alone rmvc"})
                                    .click(function(e) {
                                        e.preventDefault();
                                        $(this).closest("tr").remove();
                                    })
                                    .append($("<i>", {class: "icon-minus"})))
                            )
                    );
        }).click();

        zone.validationEngine();

        zone.submit(function(e) {
            e.preventDefault();
            if (zone.validationEngine('validate') && zone[0].length > 2) {
                $.post("ajax/mass_client.php", $(this).serialize(), function() {
                    $.jGrowl('Clientes Criados com Successo.', {life: 3000});
                    zone.find("#mass_client_table tbody").empty();
                }, "json");
            }
        });
    };

    if (client.id) {
        var client_box = new clientBox({id: atob(client.id)});
        client_box.init();
        me.init(zone, atob(client.id));
        zone.find(".grid-title .pull-left").text('Recomendações Pedidas');
    } else {
        me.init(zone);
    }
});


