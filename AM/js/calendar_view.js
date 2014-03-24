$(function() {

    var init = function(data) {
        var
                sch,
                modal_ext = $("#calendar_client_modal"),
                lead_id,
                id = getUrlVars().id;

        if (id) {
            id = atob(id);
            console.log(id);
            lead_id = id;
        }

        sch = new calendar($("#calendar"), data, modal_ext, $('#external-events'), lead_id);

        sch.initModal(modal_ext);
        sch.reserveConstruct(data.tipo);

        sch.makeRefController(data.refs);

        if (id) {
            sch.userWidgetPopulate();
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
            {init: true},
    init, "json");
});