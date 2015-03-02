var ojqTables = {por_abrir: {}, abertos: {}, fechados: {}, expirados: {}};
var aConcessMail = [];

$(function () {
    $.ajaxSetup({cache: false});
    $("#form_enviar_mail").validationEngine();
    $("#dateform").validationEngine();
    $(".chzn-select").chosen({no_results_text: "Sem resultados"});

    $.getJSON("emails.json", function (data) {
        aConcessMail = data.concessionarios;
        var sConceOpt = "<option value=''>Selecione um Concessionário</option> ";
        $.each(data.concessionarios, function (index) {
            sConceOpt += "<option value=" + index + ">" + this.nome + "-" + this.servico + "</option>";
        });

        $("#concessionarios")
            .append(sConceOpt)
            .val("")
            .trigger("liszt:updated");

        $("#concessionarios_report")
            .append(sConceOpt)
            .val("")
            .trigger("liszt:updated");

    });

    $("#tabs").tabs();
    $(".datetime_range").datetimepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        language: "pt",
        minView: 2
    }).keypress(function (e) {
        e.preventDefault();
    }).bind("cut copy paste", function (e) {
        e.preventDefault();
    });

    var jqModalPA = $("#modal_por_abrir");
    var ojqModalPA = {
        nome: jqModalPA.find("#nome_cliente"),
        campanha: jqModalPA.find("#campanha"),
        data: jqModalPA.find("#data"),
        select_c: jqModalPA.find("#select_comentario"),
        textarea_c_valor: jqModalPA.find("#textarea_comentario_valor"),
        t_reclamacao: jqModalPA.find("#tipo_reclamacao"),
        tp_reclamacao: jqModalPA.find("#tipificacao_reclamacao"),
        r_enviar: jqModalPA.find("#radio1"),
        r_fechar: jqModalPA.find("#radio2")
    };
    var jqModalAF = $("#modal_abertos_fechados");
    var ojqModalAF = {
        nome: jqModalAF.find("#nome_cliente_af"),
        campanha: jqModalAF.find("#campanha_af"),
        data: jqModalAF.find("#data_af"),
        comentario: jqModalAF.find("#comentario_af"),
        estado: jqModalAF.find("#checkbox1"),
        t_reclamacao: jqModalAF.find("#tipo_reclamacao_af"),
        tp_reclamacao: jqModalAF.find("#tipificacao_reclamacao_af"),
        email_destino: jqModalAF.find("#emails_destino_af")
    };
    var jqModalE = $("#modal_expirados");
    var ojqModalE = {
        nome: jqModalE.find("#nome_cliente_e"),
        campanha: jqModalE.find("#campanha_e"),
        data: jqModalE.find("#data_e"),
        comentario: jqModalE.find("#comentario_e"),
        estado: jqModalE.find("#estado_e"),
        t_reclamacao: jqModalE.find("#tipo_reclamacao_e"),
        tp_reclamacao: jqModalE.find("#tipificacao_reclamacao_e"),
        email: jqModalE.find("#emails_destino_e")
    };
    $("#button_send_mail").click(function () {
        if ($("#radio2").is(":checked") || $("#email_agentes").val() !== null) {
            jqModalPA.find(".modal-footer button").prop("disabled", true);
            $("#button_send_mail").text("A Gravar...");
            var
                oInfo = $(this).data("info"),
                comentario = ojqModalPA.textarea_c_valor.val(),
                t_reclamacao = ojqModalPA.t_reclamacao.find("option:selected").text(),
                tp_reclamacao = ojqModalPA.tp_reclamacao.find("option:selected").text();

            $.post("requests.php",
                {
                    action: "send_mail",
                    lead_id: oInfo.lead_id,
                    nome: oInfo.nome,
                    campanha: oInfo.campanha,
                    comentario: comentario,
                    email: $("#email_agentes").val(),
                    tipo: $("#radio2").is(":checked"),
                    tipo_reclamacao: t_reclamacao,
                    tipificacao_reclamacao: tp_reclamacao,
                    concessionario: $("#concessionarios").val()
                },
                function () {
                    jqModalPA.find(".modal-footer button").prop("disabled", false);
                    $("#button_pesquisa").click();
                    $("#modal_por_abrir").modal("hide");
                    $("#button_send_mail").text("Gravar");
                }, "json")
                .fail(function () {
                    jqModalPA.find(".modal-footer button").prop("disabled", false);
                    $("#button_pesquisa").click();
                    $("#modal_por_abrir").modal("hide");
                    $("#button_send_mail").text("Gravar");
                    $.jGrowl('O Email não foi enviado', {life: 3000});
                    return false;
                });
        }
        else {
            $.jGrowl('Escolha um ou vários emails', {life: 3000});
        }
    });
    $("#button_gravar").click(function () {
        $("#button_pesquisa").click();
        if (jqModalAF.find("#checkbox1").is(":checked")) {
            $.post("requests.php", {action: "edit_estado", id: $(this).data("id")}, function (data) {

            }, "json")
                .fail(function () {
                    return false;
                });
        }
    });
    $(document).on("click", ".ver_reclamacao", function () {
        $("#email_agentes").empty().trigger("liszt:updated");
        $("#concessionarios").val("").trigger("liszt:updated");
        var oInfo = $(this).data("info");
        jqModalAF.find("#button_gravar").prop("disabled", false);
        switch (oInfo.tipo) {
            case "por_abrir":
                ojqModalPA.nome.text(oInfo.nome);
                ojqModalPA.campanha.text(oInfo.campanha);
                ojqModalPA.data.text(oInfo.data);
                $("#button_send_mail").data("info", $(this).data("info"));
                ojqModalPA.select_c.empty();
                $.post("requests.php", {
                    action: "get_script_fields",
                    campaign_id: oInfo.campaign_id,
                    lead_id: oInfo.lead_id
                }, function (aoReclamacao) {
                    ojqModalPA.textarea_c_valor.text("Não existe texto de reclamação");
                    $.each(aoReclamacao, function () {
                        ojqModalPA.select_c.append("<option data-valor='" + this.valor + "' value=" + this.id + " >T" + this.tag + "-" + this.texto + "</option>");
                        ojqModalPA.textarea_c_valor.text(this.valor);
                    });
                    ojqModalPA.select_c.find("option:last-child").prop("selected", true);
                }, "json")
                    .fail(function () {
                        return false;
                    });
                $("#modal_por_abrir").modal('show');
                break;

            case "Abertos":
                ojqModalAF.nome.text(oInfo.nome);
                ojqModalAF.campanha.text(oInfo.campanha);
                ojqModalAF.data.text(oInfo.data);
                ojqModalAF.comentario.text(oInfo.comentario);
                ojqModalAF.t_reclamacao.text(oInfo.tipo_reclamacao);
                ojqModalAF.tp_reclamacao.text(oInfo.tipificacao_reclamacao);
                ojqModalAF.email_destino.text(oInfo.email.join("\n"));
                jqModalAF.find("#myModalLabel").text("Reclamações abertas");
                $("#checkbox1").prop("checked", false);
                jqModalAF.find("#button_gravar").data("id", this.id.slice(0, -1));
                jqModalAF.modal('show');
                break;

            case "Fechados":
                ojqModalAF.nome.text(oInfo.nome);
                ojqModalAF.campanha.text(oInfo.campanha);
                ojqModalAF.data.text(oInfo.data);
                ojqModalAF.comentario.text(oInfo.comentario);
                ojqModalAF.t_reclamacao.text(oInfo.tipo_reclamacao);
                ojqModalAF.tp_reclamacao.text(oInfo.tipificacao_reclamacao);
                ojqModalAF.email_destino.text(oInfo.email.join("\n"));
                jqModalAF.find("#myModalLabel").text("Reclamações fechadas");
                $("#checkbox1").prop("checked", true);
                jqModalAF.find("#button_gravar").prop("disabled", true);
                jqModalAF.modal('show');
                break;

            default:
                ojqModalE.nome.text(oInfo.nome);
                ojqModalE.campanha.text(oInfo.campanha);
                ojqModalE.data.text(oInfo.data);
                ojqModalE.comentario.text(oInfo.comentario);
                ojqModalE.estado.text(oInfo.tipo);
                ojqModalE.t_reclamacao.text(oInfo.tipo_reclamacao);
                ojqModalE.tp_reclamacao.text(oInfo.tipificacao_reclamacao);
                ojqModalE.email.text(oInfo.email.join("\n"));
                jqModalE.modal('show');
                break;
        }


    });
    $(document).on("change", "#select_comentario", function () {
        ojqModalPA.textarea_c_valor.text(ojqModalPA.select_c.find("option:selected").data("valor"));
    });
//TABLE POR ABRIR
    ojqTables.por_abrir = $('#table_por_abrir').dataTable({
        "bProcessing": true,
        "aoColumns": [{"bSortable": true}, {"bSortable": true}, {"bSortable": true}],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }

    });
    // TABLE ABERTOS
    ojqTables.abertos = $('#table_abertos').dataTable({
        "bProcessing": true,
        "aoColumns": [{"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    });
    //TABLE FECHADOS
    ojqTables.fechados = $('#table_fechados').dataTable({
        "bProcessing": true,
        "aoColumns": [{"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    });
    //TABLE EXPIRADOS
    ojqTables.expirados = $('#table_expirados').dataTable({
        "bProcessing": true,
        "aoColumns": [{"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    });

    $("#concessionarios").change(function () {
        var aoInterlocutores = "";

        if (+$(this, "option:selected").val() >= 0)
            $.each(aConcessMail[$(this, "option:selected").val()].interlocutores, function () {
                aoInterlocutores += "<option value=" + this.email + ">" + this.nome + "</option>";
            });

        $("#email_agentes")
            .empty()
            .append(aoInterlocutores)
            .find("option")
            .prop("selected", true)
            .end()
            .trigger("liszt:updated");
    });

    $("#button_pesquisa").click(function (e) {
        e.preventDefault();
        if ($("#dateform").validationEngine('validate')) {
            $("#report_reclamacao").removeClass("hide");
            ojqTables.por_abrir.fnClearTable();
            ojqTables.abertos.fnClearTable();
            ojqTables.fechados.fnClearTable();
            ojqTables.expirados.fnClearTable();
            $.post("requests.php", {
                    action: "get_table_data",
                    data_inicio: $("#datetime_from").val(),
                    data_fim: $("#datetime_to").val()
                },
                function (oData) {

                    ojqTables.por_abrir.dataTable().fnAddData(oData.aPorAbrir);

                    ojqTables.abertos.dataTable().fnAddData(oData.aAbertos);

                    ojqTables.fechados.dataTable().fnAddData(oData.aFechados);

                    ojqTables.expirados.dataTable().fnAddData(oData.aExpirados);

                }, "json")
                .fail(function () {
                    return false;
                });
        }
    });

    $("#report_download").click(function () {
        if ($("#concessionarios_report").val() != "") {
            if ($("#dateform").validationEngine('validate')) {
                document.location.href = "requests.php?data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&action=write_to_file&concessionario_id=" + $("#concessionarios_report").val() + "&concessionario_name=" + $("#concessionarios_report option:selected").text();
            }
            else
                $.jGrowl('Preencha os campos de data', {life: 3000});
        }
        else
            $.jGrowl('Escolha um concessionario', {life: 3000});
    });
});