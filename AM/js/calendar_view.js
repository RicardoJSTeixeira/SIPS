$(function() {

    var init = function(data) {
        var
                sch,
                modal_ext = $("#calendar_client_modal"),
                client = getUrlVars();


        function startC(data) {console.log(client)
            sch = new calendar($("#calendar"), data, modal_ext, $('#external-events'), client);

            sch.initModal(modal_ext);
            sch.reserveConstruct(data.tipo);

            sch.makeRefController(data.refs);
        }

        if (client.id) {
            client.id = atob(client.id);
            $.post("/AM/ajax/client.php", {action: "default", id: client.id}, function(clientI) {
                client = clientI;
                startC(data);
                sch.userWidgetPopulate();
            }, "json");
        } else {
            startC(data);
        }


        $(document)
                .off("click", ".no_consult_button")
                .on("click", ".no_consult_button", function()
                {

                    var calendar_client = sch.modal_ext.data();
                    $.post("/AM/ajax/consulta.php",
                            {
                                action: "insert_consulta",
                                reserva_id: calendar_client.id,
                                lead_id: calendar_client.lead_id,
                                consulta: 0,
                                consulta_razao: $("#select_no_consult option:selected").text(),
                                exame: "0",
                                exame_razao: "",
                                venda: 0,
                                venda_razao: "",
                                left_ear: 0,
                                right_ear: 0,
                                tipo_aparelho: "",
                                descricao_aparelho: "",
                                feedback: "Sem consulta"
                            }
                    , "json");
                    sch.modal_ext.modal("hide");
                    sch.modal_ext.find(".popover").hide();
                });

    };



    $.post("/AM/ajax/calendar.php",
            {action: "Init"},
    init, "json");
});