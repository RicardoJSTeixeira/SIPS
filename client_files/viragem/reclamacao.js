////open_page("../client_files/viragem/reclamacao.html")

var tables = {por_abrir: {}, abertos: {}, fechados: {}, expirados: {}};
var concess_mail = [];

$(function() {
    $.ajaxSetup({cache: false});
    $("#form_enviar_mail").validationEngine();
    $("#dateform").validationEngine();
    $(".chzn-select").chosen({no_results_text: "Sem resultados"});

    $.getJSON("emails.json", function(data) {
        concess_mail = data.concessionarios;
        var temp = "<option value=''>Selecione um Concessionário</option> ";
        $.each(data.concessionarios, function(index) {
            temp += "<option value=" + index + ">" + this.nome + "-" + this.servico + "</option>";
        });
        $("#concessionarios").append(temp);
        $("#concessionarios").val("").trigger("liszt:updated");
        $("#concessionarios_report").append(temp);
        $("#concessionarios_report").val("").trigger("liszt:updated");

    });

    $("#tabs").tabs();
    $(".datetime_range").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).keypress(function(e) {
        e.preventDefault();
    }).bind("cut copy paste", function(e) {
        e.preventDefault();
    });

    var modal_pa = {
        nome: $("#modal_por_abrir #nome_cliente"),
        campanha: $("#modal_por_abrir #campanha"),
        data: $("#modal_por_abrir #data"),
        select_c: $("#modal_por_abrir #select_comentario"),
        textarea_c_valor: $("#modal_por_abrir #textarea_comentario_valor"),
        t_reclamacao: $("#modal_por_abrir #tipo_reclamacao"),
        tp_reclamacao: $("#modal_por_abrir #tipificacao_reclamacao"),
        r_enviar: $("#modal_por_abrir #radio1"),
        r_fechar: $("#modal_por_abrir #radio2")
    };
    var modal_af = {
        nome: $("#modal_abertos_fechados #nome_cliente_af"),
        campanha: $("#modal_abertos_fechados #campanha_af"),
        data: $("#modal_abertos_fechados #data_af"),
        comentario: $("#modal_abertos_fechados #comentario_af"),
        estado: $("#modal_abertos_fechados #checkbox1"),
        t_reclamacao: $("#modal_abertos_fechados #tipo_reclamacao_af"),
        tp_reclamacao: $("#modal_abertos_fechados #tipificacao_reclamacao_af"),
        email_destino: $("#modal_abertos_fechados #emails_destino_af")
    };
    var modal_e = {
        nome: $("#modal_expirados #nome_cliente_e"),
        campanha: $("#modal_expirados #campanha_e"),
        data: $("#modal_expirados #data_e"),
        comentario: $("#modal_expirados #comentario_e"),
        estado: $("#modal_expirados #estado_e"),
        t_reclamacao: $("#modal_expirados #tipo_reclamacao_e"),
        tp_reclamacao: $("#modal_expirados #tipificacao_reclamacao_e"),
        email: $("#modal_expirados #emails_destino_e")
    };
    $("#button_send_mail").click(function() {
        if ($("#radio2").is(":checked") || $("#email_agentes").val() !== null) {
            $("#modal_por_abrir .modal-footer button").prop("disabled", true);
            $("#button_send_mail").text("A Gravar...");
            var info = $(this).data("info");
            var comentario = modal_pa.textarea_c_valor.val();
            var t_reclamacao = modal_pa.t_reclamacao.find("option:selected").text();
            var tp_reclamacao = modal_pa.tp_reclamacao.find("option:selected").text();
            $.post("requests.php",
                    {action: "send_mail", lead_id: info.lead_id, nome: info.nome, campanha: info.campanha, comentario: comentario, email: $("#email_agentes").val(), tipo: $("#radio2").is(":checked"), tipo_reclamacao: t_reclamacao, tipificacao_reclamacao: tp_reclamacao, concessionario: $("#concessionarios").val()},
            function() {
                $("#modal_por_abrir .modal-footer button").prop("disabled", false);
                $("#button_pesquisa").click();
                $("#modal_por_abrir").modal("hide");
                $("#button_send_mail").text("Gravar");
            }, "json")
                    .fail(function() {
                        $("#modal_por_abrir .modal-footer button").prop("disabled", false);
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
    $("#button_gravar").click(function() {
        $("#button_pesquisa").click();
        if ($("#modal_abertos_fechados #checkbox1").is(":checked"))
        {
            $.post("requests.php", {action: "edit_estado", id: $(this).data("id")}, function(data) {

            }, "json")
                    .fail(function() {
                        return false;
                    });
        }
    });
    $(document).on("click", ".ver_reclamacao", function() {
        $("#email_agentes").empty().trigger("liszt:updated");
        $("#concessionarios").val("").trigger("liszt:updated");
        var info = $(this).data("info");
        $("#modal_abertos_fechados #button_gravar").prop("disabled", false);
        switch (info.tipo) {
            case ("por_abrir"):
                modal_pa.nome.text(info.nome);
                modal_pa.campanha.text(info.campanha);
                modal_pa.data.text(info.data);
                $("#button_send_mail").data("info", $(this).data("info"));
                modal_pa.select_c.empty();
                $.post("requests.php", {action: "get_script_fields", campaign_id: info.campaign_id, lead_id: info.lead_id}, function(data) {
                    modal_pa.textarea_c_valor.text("Não existe texto de reclamação");
                    $.each(data, function() {
                        modal_pa.select_c.append("<option data-valor='" + this.valor + "' value=" + this.id + " >T" + this.tag + "-" + this.texto + "</option>");
                        modal_pa.textarea_c_valor.text(this.valor);
                    });
                    modal_pa.select_c.find("option:last-child").prop("selected", true);
                }, "json")
                        .fail(function() {
                            return false;
                        });
                $("#modal_por_abrir").modal('show');
                break;

            case ("Abertos"):
                modal_af.nome.text(info.nome);
                modal_af.campanha.text(info.campanha);
                modal_af.data.text(info.data);
                modal_af.comentario.text(info.comentario);
                modal_af.t_reclamacao.text(info.tipo_reclamacao);
                modal_af.tp_reclamacao.text(info.tipificacao_reclamacao);
                modal_af.email_destino.text(info.email.join("\n"));
                $("#modal_abertos_fechados #myModalLabel").text("Reclamações abertas");
                $("#checkbox1").prop("checked", false);
                $("#modal_abertos_fechados #button_gravar").data("id", this.id.slice(0, -1));
                $("#modal_abertos_fechados").modal('show');
                break;

            case ("Fechados"):
                modal_af.nome.text(info.nome);
                modal_af.campanha.text(info.campanha);
                modal_af.data.text(info.data);
                modal_af.comentario.text(info.comentario);
                modal_af.t_reclamacao.text(info.tipo_reclamacao);
                modal_af.tp_reclamacao.text(info.tipificacao_reclamacao);
                modal_af.email_destino.text(info.email.join("\n"));
                $("#modal_abertos_fechados #myModalLabel").text("Reclamações fechadas");
                $("#checkbox1").prop("checked", true);
                $("#modal_abertos_fechados #button_gravar").prop("disabled", true);
                $("#modal_abertos_fechados").modal('show');
                break;

            default:
                modal_e.nome.text(info.nome);
                modal_e.campanha.text(info.campanha);
                modal_e.data.text(info.data);
                modal_e.comentario.text(info.comentario);
                modal_e.estado.text(info.tipo);
                modal_e.t_reclamacao.text(info.tipo_reclamacao);
                modal_e.tp_reclamacao.text(info.tipificacao_reclamacao);
                modal_e.email.text(info.email.join("\n"));
                $("#modal_expirados").modal('show');
                break;
        }


    });
    $(document).on("change", "#select_comentario", function() {
        modal_pa.textarea_c_valor.text(modal_pa.select_c.find("option:selected").data("valor"));
    });
//TABLE POR ABRIR
    tables.por_abrir = $('#table_por_abrir').dataTable({
        "bProcessing": true,
        "aoColumns": [{"bSortable": true}, {"bSortable": true}, {"bSortable": true}],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }

    });
    // TABLE ABERTOS
    tables.abertos = $('#table_abertos').dataTable({
        "bProcessing": true,
        "aoColumns": [{"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    });
    //TABLE FECHADOS
    tables.fechados = $('#table_fechados').dataTable({
        "bProcessing": true,
        "aoColumns": [{"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    });
    //TABLE EXPIRADOS
    tables.expirados = $('#table_expirados').dataTable({
        "bProcessing": true,
        "aoColumns": [{"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}, {"bSortable": true}],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    });

    $("#concessionarios").change(function() {
        $("#email_agentes").empty();
        var temp = "";
        if ($(this, "option:selected").val() >= "0")
            $.each(concess_mail[$(this, "option:selected").val()].interlocutores, function()
            {
                temp += "<option value=" + this.email + ">" + this.nome + "</option>";
            });
        $("#email_agentes").append(temp);
        $("#email_agentes option").prop("selected", true);
        $("#email_agentes").trigger("liszt:updated");
    });

    $("#button_pesquisa").click(function(e) {
        e.preventDefault();
        if ($("#dateform").validationEngine('validate')) {
            $("#report_reclamacao").removeClass("hide");
            $.post("requests.php", {action: "get_table_data", data_inicio: $("#datetime_from").val(), data_fim: $("#datetime_to").val()},
            function(data) {
                tables.por_abrir.fnClearTable();
                $.each(data.por_abrir, function() {
                    tables.por_abrir.dataTable().fnAddData(
                            [this.nome, this.campanha, this.data + "<div class='view-button'><button id='" + this.lead_id + "L' class='btn btn-mini icon-reorder ver_reclamacao'> Ver </button></div"]);
                    $("#" + this.lead_id + "L").data("info", this);
                });
                tables.abertos.fnClearTable();
                $.each(data.abertos, function() {
                    tables.abertos.dataTable().fnAddData(
                            [this.id, this.nome, this.campanha, this.tipo_reclamacao, this.tipificacao_reclamacao, this.data + "<div class='view-button'><button id='" + this.id + "I' class='btn btn-mini icon-reorder ver_reclamacao'> Ver </button></div"]);
                    $("#" + this.id + "I").data("info", this);
                });
                tables.fechados.fnClearTable();
                $.each(data.fechados, function() {
                    tables.fechados.dataTable().fnAddData(
                            [this.id, this.nome, this.campanha, this.tipo_reclamacao, this.tipificacao_reclamacao, this.data + "<div class='view-button'><button id='" + this.id + "I' class='btn btn-mini icon-reorder ver_reclamacao'> Ver </button></div"]);
                    $("#" + this.id + "I").data("info", this);
                });
                tables.expirados.fnClearTable();
                $.each(data.expirados, function() {
                    tables.expirados.dataTable().fnAddData(
                            [this.id, this.nome, this.campanha, this.tipo_reclamacao, this.tipificacao_reclamacao, this.data, this.tipo + "<div class='view-button'><button id='" + this.id + "I' class='btn btn-mini icon-reorder ver_reclamacao'> Ver </button></div"]);
                    $("#" + this.id + "I").data("info", this);
                });
            }, "json")
                    .fail(function() {
                        return false;
                    });
        }
    });

    $("#report_download").click(function() {
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