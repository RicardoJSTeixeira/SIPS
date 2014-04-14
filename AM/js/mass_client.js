$(function() {
    var zone = $("#mass_client_form");

    zone.find("#plus-client").click(function(e) {
        e.preventDefault();

        zone.find("#mass_client_table tbody").append(
                $("<tr>")
                .append($("<td>")
                        .append($("<input>", {type: "text", name: "cname[]", placeholder: "", class: "span validate[required]"}))
                        )
                .append($("<td>")
                        .append($("<input>", {type: "text", name: "cmorada[]", placeholder: "", class: "span validate[required]"}))
                        )
                .append($("<td>")
                        .append($("<input>", {type: "text", name: "ctel[]", placeholder: "", class: "span validate[required,custom[onlyNumberSp],minSize[9]]"}))
                        )
                .append($("<td>")
                        .append($("<input>", {type: "text", name: "cemail[]", placeholder: "", class: "span validate[custom[email]]"}))
                        )
                .append($("<td>")
                        .append($("<select>", {name: "ctoissue[]", class: "span validate[required]"}).append([new Option("Seleccione uma Opção", ""), new Option("Yes", "YES"), new Option("No", "NO")]))
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
        if (zone.validationEngine('validate')) {
            $.post("ajax/mass_client.php", $(this).serialize(), function() {
                $.jGrowl('Clientes Criados com Successo.', {life: 3000});
                zone.find("#mass_client_table tbody").empty();
            }, "json");
        }
    });
});


