var requests = function(basic_path, options_ext)
{
    var me = this;
    var basic_path = basic_path;
    this.config = {};
    $.extend(true, this.config, options_ext);

    if (SpiceU.user_level < 5) {
        $("#principal button.btn-info").hide();
    }

    this.apoio_marketing = {
        init: function()
        {
            $.get("/AM/view/requests/apoio_marketing_modal.html", function(data) {
                basic_path.append(data);
            }, 'html');
        },
        new : function(am_zone)
        {
            var ldpinput_count = 1;
            am_zone.empty().off();
            $.get("/AM/view/requests/apoio_marketing.html", function(data) {
                am_zone.append(data);
                $('#apoio_am_form').stepy({backLabel: "Anterior", nextLabel: "Seguinte", next: function() {
                        if (am_zone.find("#apoio_am_form").validationEngine("validate"))
                            return true;
                        else
                            return false;
                    }, finishButton: false});
                am_zone.find("#data_rastreio1").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, startDate: moment().format("YYYY-MM-DD")})
                        .on('changeDate', function() {
                            $("#data_rastreio2").datetimepicker('setStartDate', moment($(this).val()).format('YYYY-MM-DD'));
                        });
                am_zone.find("#data_rastreio2").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, startDate: moment().format("YYYY-MM-DD")})
                        .on('changeDate', function() {
                            $("#data_rastreio1").datetimepicker('setEndDate', moment($(this).val()).format('YYYY-MM-DD'));
                        });
                am_zone.find("#data_inicio1").datetimepicker({format: 'hh:ii', autoclose: true, language: "pt", startView: 1, maxView: 1, startDate: moment().format("YYYY-MM-DD"), minuteStep: 30})
                        .on('changeDate', function() {
                            $("#data_inicio2").datetimepicker('setStartDate', moment().format('YYYY-MM-DDT' + $(this).val()));
                        });
                am_zone.find("#data_inicio2").datetimepicker({format: 'hh:ii', autoclose: true, language: "pt", startView: 1, maxView: 1, startDate: moment().format("YYYY-MM-DD"), minuteStep: 30})
                        .on('changeDate', function() {
                            $("#data_inicio1").datetimepicker('setEndDate', moment().format('YYYY-MM-DDT' + $(this).val()));
                            $("#data_fim1").datetimepicker('setStartDate', moment().format('YYYY-MM-DDT' + $(this).val()));
                        });
                am_zone.find("#data_fim1").datetimepicker({format: 'hh:ii', autoclose: true, language: "pt", startView: 1, maxView: 1, startDate: moment().format("YYYY-MM-DD"), minuteStep: 30})
                        .on('changeDate', function() {
                            $("#data_fim2").datetimepicker('setStartDate', moment().format('YYYY-MM-DDT' + $(this).val()));
                        });
                am_zone.find("#data_fim2").datetimepicker({format: 'hh:ii', autoclose: true, language: "pt", startView: 1, maxView: 1, startDate: moment().format("YYYY-MM-DD"), minuteStep: 30})
                        .on('changeDate', function() {
                            $("#data_fim1").datetimepicker('setEndDate', moment().format('YYYY-MM-DDT' + $(this).val()));
                        });
                //Adiciona Linhas
                am_zone.find("#button_ldptable_add_line").click(function(e) {
                    e.preventDefault();
                    am_zone.find("#table_tbody_ldp").append("<tr><td><input type='text' name='ldp_cp" + ldpinput_count + "' class='linha_cp validate[required,custom[onlyNumberSp]]'></td><td><input type='text' name='ldp_freg" + ldpinput_count + "' class='linha_freg validate[required]'></td><td> <button class='btn btn-danger button_ldptable_remove_line icon-alone' ><i class='icon-minus'></i></button></td></tr>");
                    ldpinput_count++;
                }).click();
                //Remove Linhas
                am_zone.on("click", ".button_ldptable_remove_line", function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
                am_zone.on("change", "[name='horario_check']", function(e) {

                    if ($(this).val() == 1)
                    {
                        am_zone.find("#horario_manha").show();
                        am_zone.find("#horario_tarde").show();
                    }
                    else if ($(this).val() == 2)
                    {
                        am_zone.find("#horario_manha").show();
                        am_zone.find("#horario_tarde").hide();
                    }
                    else
                    {
                        am_zone.find("#horario_manha").hide();
                        am_zone.find("#horario_tarde").show();
                    }
                });

                //SUBMIT
                am_zone.on("click", "#submit_am", function(e)
                {
                    e.preventDefault();
                    if (am_zone.find("#apoio_am_form").validationEngine("validate"))
                    {
                        if (am_zone.find("#table_tbody_ldp tr").length)
                        {
                            var local_publicidade_array = [];
                            $.each(am_zone.find("#table_tbody_ldp").find("tr"), function(data) {
                                local_publicidade_array.push({cp: $(this).find(".linha_cp").val(), freguesia: $(this).find(".linha_freg").val()});
                            });
                            $.post("/AM/ajax/requests.php", {action: "criar_apoio_marketing",
                                data_inicial: am_zone.find("#data_rastreio1").val(),
                                data_final: am_zone.find("#data_rastreio2").val(),
                                horario: {
                                    tipo: am_zone.find("[name='horario_check']:checked").val(),
                                    inicio1: am_zone.find("#data_inicio1").val(),
                                    inicio2: am_zone.find("#data_inicio2").val(),
                                    fim1: am_zone.find("#data_fim1").val(),
                                    fim2: am_zone.find("#data_fim2").val()},
                                localidade: am_zone.find("#input_localidade").val(),
                                local: am_zone.find("#input_local_rastreio").val(),
                                morada: am_zone.find("#input_morada_rastreio").val(),
                                comments: am_zone.find("#input_observaçoes").val(),
                                local_publicidade: local_publicidade_array},
                            function(data1)
                            {
                                $('#apoio_am_form').stepy('step', 1);
                                am_zone.find(":input").val("");
                                $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                            }, "json");
                        }
                        else
                            $.jGrowl("Selecione pelo menos uma Freguesia/Código de Postal", {life: 4000});
                    }
                });
            });
        },
        get_to_datatable: function(am_zone)
        {
            var apoio_markting_table = am_zone.dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requests.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_apoio_marketing_to_datatable"});
                },
                "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Dispenser", "bVisible": (SpiceU.user_level > 5)}, {"sTitle": "Data pedido"}, {"sTitle": "Data inicial rastreio"}, {"sTitle": "Data final rastreio"}, {"sTitle": "Horario", "sWidth": "75px"}, {"sTitle": "Localidade"}, {"sTitle": "Local"}, {"sTitle": "Morada"}, {"sTitle": "Observações"}, {"sTitle": "Local publicidade", "sWidth": "75px"}, {"sTitle": "Estado"}, {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_AM').click(function(event) {
                event.preventDefault();
                table2csv(apoio_markting_table, 'full', '#' + am_zone[0].id);
            });
            am_zone.on("click", ".ver_horario", function()
            {
                var id = $(this).data().apoio_marketing_id;
                $.post("ajax/requests.php", {action: "get_horario_from_apoio_marketing", id: id}, function(data) {
                    basic_path.find("#ver_horario_modal .horario_all_master").hide();

                    if (data[0].tipo == 1)
                        basic_path.find("#ver_horario_modal .horario_all_master").show();
                    if (data[0].tipo == 2)
                        basic_path.find("#ver_horario_modal #horario_manha").show();
                    if (data[0].tipo == 3)
                        basic_path.find("#ver_horario_modal #horario_tarde").show();


                    basic_path.find("#ver_horario_modal #manha_inicio").text(data[0].inicio1);
                    basic_path.find("#ver_horario_modal #manha_fim").text(data[0].inicio2);
                    basic_path.find("#ver_horario_modal #tarde_inicio").text(data[0].fim1);
                    basic_path.find("#ver_horario_modal #tarde_fim").text(data[0].fim2);

                    basic_path.find("#ver_horario_modal").modal("show");
                }, "json");
            });
            am_zone.on("click", ".ver_local_publicidade", function()
            {
                var id = $(this).data().apoio_marketing_id;
                $.post("/AM/ajax/requests.php", {action: "get_locais_publicidade_from_apoio_marketing", id: id}, function(data) {
                    basic_path.find("#ver_local_publicidade_modal #tbody_ver_local_publicidade").empty();
                    $.each(data, function()
                    {
                        basic_path.find("#ver_local_publicidade_modal #tbody_ver_local_publicidade").append("<tr><td>" + this.cp + "</td><td>" + this.freguesia + "</td></tr>");
                    });
                    basic_path.find("#ver_local_publicidade_modal").modal("show");
                }, "json");
            });
            am_zone.on("click", ".accept_apoio_marketing", function()
            {
                var this_button = $(this);
                $.post('/AM/ajax/requests.php', {action: "accept_apoio_marketing", id: $(this).val()}, function() {
                    this_button.parent("td").prev().text("Aprovado");
                    apoio_markting_table.fnReloadAjax();
                }, "json");
            });
            am_zone.on("click", ".decline_apoio_marketing", function()
            {
                var this_button = $(this);
                bootbox.prompt("Qual o motivo?", function(result) {
                    if (result !== null) {
                        $.post('/AM/ajax/requests.php', {action: "decline_apoio_marketing", id: this_button.val(), motivo: result}, function() {
                            this_button.parent().prev().text("Rejeitado");
                            apoio_markting_table.fnReloadAjax();
                        }, "json");
                    }
                });
            });
        }
    };
    this.relatorio_frota = {
        init: function()
        {
            $.get("/AM/view/requests/relatorio_frota_modal.html", function(data) {
                basic_path.append(data);
            }, 'html');
        },
        new : function(rf_zone)
        {
            var rfinput_count = 2;
            rf_zone.empty().off();
            var availableTags = ["Revisão", "Mudança de óleo", "Mudança de pneus"];
            $.get("/AM/view/requests/relatorio_frota.html", function(data) {
                rf_zone.append(data);
                rf_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                rf_zone.find(".rf_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                rf_zone.find("#input_km").autoNumeric('init', {mDec: '0'});

                //Adiciona Linhas
                rf_zone.find("#button_rf_table_add_line").click(function(e)
                {
                    e.preventDefault();
                    rf_zone.find("#table_tbody_rf").append("<tr><td> <input size='16' type='text' name='rf_data" + rfinput_count + "' class='rf_datetime validate[required] linha_data span' readonly id='rf_datetime" + rfinput_count + "' placeholder='Data'></td><td><input class='validate[required] linha_ocorrencia span' type='text' name='rf_ocorr" + rfinput_count + "'></td><td><input class='validate[required] linha_km text-right ' type='text' value='0' maxlength='6' name='rf_km" + rfinput_count + "' size='16'/></td><td><button class='btn btn-danger button_rf_table_remove_line icon-alone'><i class='icon-minus'></i></button></td></tr>");
                    rf_zone.find("#rf_datetime" + rfinput_count).datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                    rf_zone.find("[name='rf_km" + rfinput_count + "']").autoNumeric('init', {mDec: '0'});
                    $("[name='rf_ocorr" + rfinput_count + "']").autocomplete({source: availableTags});
                    rfinput_count++;
                }).click();
                //Remove Linhas
                rf_zone.on("click", ".button_rf_table_remove_line", function(e)
                {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
                //SUBMIT
                rf_zone.on("click", "#submit_rf", function(e)
                {
                    e.preventDefault();
                    if (rf_zone.find("#relatorio_frota_form").validationEngine("validate"))
                    {
                        if (rf_zone.find("#table_tbody_rf tr").length)
                        {
                            var soma = 0;
                            $.each(rf_zone.find("#table_tbody_rf").find(".linha_km"), function()
                            {
                                soma = soma + ~~$(this).autoNumeric('get');
                            });
                            if (!soma)
                            {
                                $.jGrowl("Insira pelo menos uma ocorrência ");
                                return false;
                            }
                            if (soma > ~~rf_zone.find("#input_km").val())
                            {
                                $.jGrowl("O número de Kms nas ocorrência é superior aos Kms totais no relatório");
                            }
                            else
                            {
                                var ocorrencias_array = [];
                                $.each(rf_zone.find("#table_tbody_rf").find("tr"), function(data)
                                {
                                    ocorrencias_array.push(
                                            {data: $(this).find(".linha_data").val(),
                                                ocorrencia: $(this).find(".linha_ocorrencia").val(),
                                                km: $(this).find(".linha_km").autoNumeric('get')});
                                });
                                $.post("/AM/ajax/requests.php", {action: "criar_relatorio_frota",
                                    data: rf_zone.find("#input_data").val(),
                                    matricula: rf_zone.find("#input_matricula").val(),
                                    km: rf_zone.find("#input_km").val(),
                                    viatura: rf_zone.find(":radio[name='rrf']:checked").val(),
                                    ocorrencias: ocorrencias_array,
                                    comments: rf_zone.find("#input_comments").val().length ? rf_zone.find("#input_comments").val() : ""},
                                function()
                                {
                                    rf_zone.find(":input").val("");
                                    $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                                }, "json");
                            }
                        }
                        else
                            $.jGrow("selecione pelo menos uma ocorrencia", {life: 4000});
                    }
                });
            });
        },
        get_to_datatable: function(rf_zone)
        {
            var relatorio_frota_table = rf_zone.dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requests.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_relatorio_frota_to_datatable"});
                },
                "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Dispenser", "bVisible": (SpiceU.user_level > 5)}, {"sTitle": "data"}, {"sTitle": "Matricula"}, {"sTitle": "Km"}, {"sTitle": "Viatura"}, {"sTitle": "Observações"}, {"sTitle": "Ocorrencias", "sWidth": "150px"}, {"sTitle": "Estado"}, {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_F').click(function(event) {
                event.preventDefault();
                table2csv(relatorio_frota_table, 'full', '#' + rf_zone[0].id);
            });
            rf_zone.on("click", ".ver_ocorrencias", function()
            {
                var id = $(this).data().relatorio_frota_id;
                $.post("ajax/requests.php", {action: "get_ocorrencias_frota", id: id}, function(data) {

                    var tbody = basic_path.find("#ver_occorrencia_frota_modal #ver_occorrencia_frota_tbody");
                    tbody.empty();
                    $.each(data, function()
                    {
                        tbody.append("<tr><td>" + this.data + "</td><td>" + this.km + "</td><td>" + this.ocorrencia + "</td></tr>");
                    });
                    basic_path.find("#ver_occorrencia_frota_modal").modal("show");
                }, "json");
            });
            rf_zone.on("click", ".accept_report_frota", function()
            {
                var this_button = $(this);
                $.post('/AM/ajax/requests.php', {action: "accept_report_frota", id: $(this).val()}, function() {
                    this_button.parent("td").prev().text("Aprovado");
                    relatorio_frota_table.fnReloadAjax();
                }, "json");
            });
            rf_zone.on("click", ".decline_report_frota", function()
            {
                var this_button = $(this);
                bootbox.confirm("Tem a certeza?", function(result) {
                    if (result) {
                        $.post('/AM/ajax/requests.php', {action: "decline_report_frota", id: this_button.val()}, function() {
                            this_button.parent("td").prev().text("Rejeitado");
                            relatorio_frota_table.fnReloadAjax();
                        }, "json");
                    }
                });
            });
        }};
    this.relatorio_correio = {
        init: function()
        {
            $.get("/AM/view/requests/relatorio_correio_modal.html", function(data) {
                basic_path.append(data);
            }, 'html');
        },
        new : function(rc_zone)
        {
            rc_zone.empty().off();
            $.get("/AM/view/requests/relatorio_correio.html", function(data) {
                rc_zone.append(data);
                rc_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
                //SUBMIT
                rc_zone.on("click", "#submit_rc", function(e)
                {
                    e.preventDefault();
                    if (rc_zone.find("#relatorio_correio_form").validationEngine("validate"))
                    {
                        if (rc_zone.find("#doc_obj_table_tbody tr").length)
                        {
                            var docs_objs = [];
                            $.each(rc_zone.find("#doc_obj_table_tbody tr"), function()
                            {
                                docs_objs.push({value: $(this).find("input").val(), confirmed: false});
                            });
                            $.post("ajax/requests.php", {action: "criar_relatorio_correio",
                                carta_porte: rc_zone.find("#input_carta_porte").val(),
                                data: rc_zone.find("#data_envio_datetime").val(),
                                doc: rc_zone.find("#input_doc").val(),
                                lead_id: rc_zone.find("#input_lead_id").val(),
                                client_name: rc_zone.find("#input_client_name").val(),
                                input_doc_obj_assoc: docs_objs,
                                comments: rc_zone.find("#input_comments").val().length ? rc_zone.find("#input_comments").val() : "Sem observações"},
                            function()
                            {
                                rc_zone.find(":input").val("");
                                $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                            }, "json");
                        }
                        else
                            $.jGrowl("Selecione pelo menos um ", {life: 4000});
                    }

                });
                rc_zone.find("#add_line_obj_doc").click(function(e)
                {
                    e.preventDefault();
                    rc_zone.find("#doc_obj_table_tbody").append("<tr><td><input class='validate[required] span' type='text' /></td> <td><button class='btn btn-danger remove_doc_obj icon-alone'><i class='icon icon-minus'></i></button></td></tr>");
                }).click();
                rc_zone.on("click", ".remove_doc_obj", function(e)
                {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
            });
        },
        get_to_datatable: function(rc_zone)
        {
            var relatorio_correio_table = rc_zone.dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requests.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_relatorio_correio_to_datatable"}
                    );
                },
                "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Dispenser", "bVisible": (SpiceU.user_level > 5)}, {"sTitle": "Carta Porte"}, {"sTitle": "Data Envio"}, {"sTitle": "Documento"}, {"sTitle": "Ref.ª de Cliente"}, {"sTitle": "Anexo", "sWidth": "75px"}, {"sTitle": "Observações"}, {"sTitle": "Estado"}, {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_C').click(function(event) {
                event.preventDefault();
                table2csv(relatorio_correio_table, 'full', '#' + rc_zone[0].id);
            });
            rc_zone.on("click", ".accept_report_correio", function()
            {
                if ($(this).parents("td").prev().prev().prev().find("button").data().approved)
                {
                    var this_button = $(this);
                    $.post('/AM/ajax/requests.php', {action: "accept_report_correio", id: $(this).val()}, function() {
                        this_button.parent("td").prev().text("Aprovado");
                        relatorio_correio_table.fnReloadAjax();
                    }, "json");
                }
                else
                    $.jGrowl("Verifique os anexos 1º, antes de aprovar.");
            });
            rc_zone.on("click", ".decline_report_correio", function()
            {
                var this_button = $(this);
                bootbox.confirm("Tem a certeza?", function(result) {
                    if (result) {
                        $.post('/AM/ajax/requests.php', {action: "decline_report_correio", id: this_button.val()}, function() {
                            this_button.parent("td").prev().text("Rejeitado");
                            relatorio_correio_table.fnReloadAjax();
                        }, "json");
                    }
                });
            });
            rc_zone.on("click", ".ver_anexo_correio", function(e)
            {
                e.preventDefault();
                var id_anexo = $(this).data().anexo_id;
                var anexo_number = 1;
                $.post("ajax/requests.php", {action: "get_anexo_correio", id: id_anexo},
                function(data1)
                {
                    var tbody = basic_path.find("#tbody_ver_anexo_correio");
                    tbody.empty();
                    $.each(data1, function()
                    {
                        tbody.append("<tr><td class='chex-table'><input type='checkbox' value='" + id_anexo + "' class='checkbox_confirm_anexo' " + ((~~this.confirmed) ? "checked" : "") + " " + ((SpiceU.user_level < 5) ? "disabled" : "") + " id='anexo" + anexo_number + "' name='cci'><label class='checkbox inline' for='anexo" + anexo_number + "'><span></span> </label></td><td>" + this.value + "</td></tr>");
                        anexo_number++;
                    });
                    basic_path.find(".anexo_exit_button").data("id_correio", id_anexo);
                    basic_path.find("#ver_anexo_correio_modal").modal("show");
                }, "json");
            });
            basic_path.on("click", ".anexo_exit_button", function(e)
            {
                var anexo_array = [];
                var this_button = $(this);
                var data_ver_button = rc_zone.find("[data-anexo_id='" + this_button.data().id_correio + "']");
                data_ver_button.data().approved = 1;
                $.each(this_button.parents("#ver_anexo_correio_modal").find("tr"), function()
                {
                    anexo_array.push({value: $(this).find("td").last().text(), confirmed: ~~$(this).find("td").first().find(":checkbox").is(":checked")});

                    if (!~~~~$(this).find("td").first().find(":checkbox").is(":checked"))
                        data_ver_button.data().approved = 0;
                });
                $.post("/AM/ajax/requests.php", {action: "save_anexo_correio", id: this_button.data().id_correio, anexos: anexo_array}, "json");
            });
        }
    };
    this.relatorio_mensal_stock = {
        init: function() {
//             $.get("/AM/view/requests/relatorio_mensal_stock_modal.html", function(data) {
//             basic_path.append(data);
//             }, 'html');
        },
        new : function(rc_zone) {
            rc_zone.empty().off();
            $.get("/AM/view/requests/relatorio_mensal_stock.html", function(data) {
                rc_zone.append(data);
                rc_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).val(moment().format('YYYY-MM-DD'));
                //SUBMIT
                rc_zone.on("click", "#submit_rfms", function(e) {
                    e.preventDefault();
                    if (rc_zone.find("#relatorio_mensal_stock_form").validationEngine("validate")) {
                        var prdt_objs = [];
                        if (rc_zone.find("#table_tbody_rfms tr").length)
                        {
                            $.each(rc_zone.find("#table_tbody_rfms tr"), function() {
                                prdt_objs.push({quantidade: $(this).find(".quant").val(), descricao: $(this).find(".desc").val(), serie: $(this).find(".serie").val(), obs: $(this).find(".obs").val()});
                            });
                            $.post("ajax/requests.php",
                                    {
                                        action: "criar_relatorio_mensal_stock",
                                        data: rc_zone.find("#input_data").val(),
                                        produtos: prdt_objs
                                    },
                            function(data1)
                            {
                                rc_zone.find(":input").val("");
                                $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                            }, "json");
                        }
                        else
                            $.jGrowl("Preencha pelo menos uma linha.");
                    }
                });
                rc_zone.find("#button_rfms_table_add_line").click(function(e) {
                    e.preventDefault();
                    rc_zone.find("#table_tbody_rfms").append("<tr><td><input class='validate[required] span text-right quant' type='text' /></td> <td><input class='validate[required] span desc' type='text' /></td> <td><input class='validate[required] span serie' type='text' /></td> <td><textarea class='span obs' ></textarea></td> <td><button class='btn btn-danger remove_doc_obj icon-alone'><i class='icon icon-minus'></i></button></td></tr>");
                    rc_zone.find(".quant").autotab('number');
                }).click();
                rc_zone.on("click", ".remove_doc_obj", function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
            });
        },
        get_to_datatable: function(rc_zone) {
            var relatorio_stock_table = rc_zone.dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requests.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_relatorio_stock_to_datatable"}
                    );
                },
                "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Dispenser", "bVisible": (SpiceU.user_level > 5)}, {"sTitle": "Data", "sWidth": "75px"}, {"sTitle": "Produtos", "sWidth": "75px"}, {"sTitle": "Estado"}, {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_S').click(function(event) {
                event.preventDefault();
                table2csv(relatorio_stock_table, 'full', '#' + rc_zone[0].id);
            });
            rc_zone.on("click", ".accept_report_stock", function()
            {
                var this_button = $(this);
                $.post('/AM/ajax/requests.php', {action: "accept_report_stock", id: $(this).val()}, function() {
                    this_button.parent("td").prev().text("Aprovado");
                    relatorio_stock_table.fnReloadAjax();
                }, "json");
            });
            rc_zone.on("click", ".decline_report_stock", function()
            {
                var this_button = $(this);
                bootbox.confirm("Tem a certeza?", function(result) {
                    if (result) {
                        $.post('/AM/ajax/requests.php', {action: "decline_report_stock", id: this_button.val()}, function() {
                            this_button.parent("td").prev().text("Rejeitado");
                            relatorio_stock_table.fnReloadAjax();
                        }, "json");
                    }
                });
            });
            rc_zone.on("click", ".ver_itens", function(e)
            {

                e.preventDefault();
                var id_anexo = $(this).data().stock_id;
                $.post("ajax/requests.php", {action: "get_itens_stock", id: id_anexo},
                function(data)
                {
                    var
                            content,
                            tbody = "";
                    $.each(data, function()
                    {
                        tbody += "<tr><td>" + this.quantidade + "</td><td>" + this.descricao + "</td><td>" + this.serie + "</td><td>" + this.obs + "</td></tr>";
                    });
                    content = "<table class='table table-mod table-bordered table-striped table-condesed'>\n\
                                <thead><tr><th>#</th><th>Descrição</th><th>Nº Serie</th><th>Observações</th></tr></thead>\n\
                                <tbody>" + tbody + "</tbody>\n\
                               </table>";
                    bootbox.alert(content);
                }, "json");
            });
        }
    }
    this.relatorio_movimentacao_stock = {
        init: function() {
//            $.get("/AM/view/requests/relatorio_movimentacao_stock_modal.html", function(data) {
//                basic_path.append(data);
//            }, 'html');
        },
        new : function(rc_zone) {
            rc_zone.empty().off();
            $.get("/AM/view/requests/relatorio_movimentacao_stock.html", function(data) {
                rc_zone.append(data);
                rc_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).val(moment().format('YYYY-MM-DD'));
                //SUBMIT
                rc_zone.on("click", "#submit_rfms", function(e) {
                    e.preventDefault();
                    if (rc_zone.find("#relatorio_movimentacao_stock_form").validationEngine("validate")) {
                        var prdt_objs = [];
                        if (rc_zone.find("#table_tbody_rfms tr").length)
                        {
                            $.each(rc_zone.find("#table_tbody_rfms tr"), function() {
                                prdt_objs.push({quantidade: $(this).find(".quant").val(), destinario: $(this).find(".desc").val(), descricao: $(this).find(".desc").val(), serie: $(this).find(".serie").val(), obs: $(this).find(".obs").val()});
                            });
                            $.post("ajax/requests.php",
                                    {
                                        action: "criar_relatorio_movimentacao_stock",
                                        data: rc_zone.find("#input_data").val(),
                                        produtos: prdt_objs
                                    },
                            function()
                            {
                                rc_zone.find(":input").val("");
                                $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                            }, "json");
                        }
                        else
                            $.jGrowl("Preencha pelo menos uma linha.");
                    }
                });
                rc_zone.find("#button_rfms_table_add_line").click(function(e) {
                    e.preventDefault();
                    rc_zone.find("#table_tbody_rfms").append("<tr><td><input class='validate[required] span text-right quant' type='text' /></td> <td><input class='validate[required] span dest' type='text' /></td> <td><input class='validate[required] span desc' type='text' /></td> <td><input class='validate[required] span serie' type='text' /></td> <td><textarea class='span obs' ></textarea></td> <td><button class='btn btn-danger remove_doc_obj icon-alone'><i class='icon icon-minus'></i></button></td></tr>");
                    rc_zone.find(".quant").autotab('number');
                }).click();
                rc_zone.on("click", ".remove_doc_obj", function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
            });
        },
        get_to_datatable: function(rc_zone) {
            var relatorio_moviment_stock_table = rc_zone.dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requests.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_relatorio_movimentacao_to_datatable"}
                    );
                },
                "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Dispenser", "bVisible": (SpiceU.user_level > 5)}, {"sTitle": "Data", "sWidth": "75px"}, {"sTitle": "Produtos", "sWidth": "75px"}, {"sTitle": "Estado"}, {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_MS').click(function(event) {
                event.preventDefault();
                table2csv(relatorio_moviment_stock_table, 'full', '#' + rc_zone[0].id);
            });
            rc_zone.on("click", ".accept_report_movimentacao", function()
            {
                var this_button = $(this);
                $.post('/AM/ajax/requests.php', {action: "accept_report_movimentacao", id: $(this).val()}, function() {
                    this_button.parent("td").prev().text("Aprovado");
                    relatorio_moviment_stock_table.fnReloadAjax();
                }, "json");
            });
            rc_zone.on("click", ".decline_report_movimentacao", function()
            {
                var this_button = $(this);
                bootbox.confirm("Tem a certeza?", function(result) {
                    if (result) {
                        $.post('/AM/ajax/requests.php', {action: "decline_report_movimentacao", id: this_button.val()}, function() {
                            this_button.parent("td").prev().text("Rejeitado");
                            relatorio_moviment_stock_table.fnReloadAjax();
                        }, "json");
                    }
                });
            });
            rc_zone.on("click", ".ver_itens", function(e)
            {

                e.preventDefault();
                var id_anexo = $(this).data().movimentacao_id;
                $.post("ajax/requests.php", {action: "get_itens_movimentacao", id: id_anexo},
                function(data)
                {
                    var
                            content,
                            tbody = "";
                    $.each(data, function()
                    {
                        tbody += "<tr><td>" + this.quantidade + "</td><td>" + this.destinario + "</td><td>" + this.descricao + "</td><td>" + this.serie + "</td><td>" + this.obs + "</td></tr>";
                    });
                    content = "<table class='table table-mod table-bordered table-striped table-condesed'>\n\
                                <thead><tr><th>#</th><th>Destinatário</th><th>Descrição</th><th>Nº Serie</th><th>Observações</th></tr></thead>\n\
                                <tbody>" + tbody + "</tbody>\n\
                               </table>";
                    bootbox.alert(content);
                }, "json");
            });
        }
    };
};