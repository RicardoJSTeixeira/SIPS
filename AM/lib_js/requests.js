var requests = function(basic_path, options_ext) {
    var me = this;
    var basic_path = basic_path;
    this.config = {};
    $.extend(true, this.config, options_ext);
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    //-------------------------------RELATORIO APOIO MARKETING----------------------------RELATORIO APOIO MARKETING----------------------RELATORIO APOIO MARKETING------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    this.apoio_marketing = {
        init: function(callback) {
            $.get("/AM/view/requests/apoio_marketing_modal.html", function(data) {
                basic_path.append(data);
                if (typeof callback === "function") {
                    callback();
                }
            }, 'html');
        },
        new : function(am_zone) {
            var ldpinput_count = 1;
            am_zone.empty().off();
            $.get("/AM/view/requests/apoio_marketing.html", function(data) {
                am_zone.append(data);
                $('#apoio_am_form').stepy({backLabel: "Anterior", nextLabel: "Seguinte", next: function() {
                        return ~~am_zone.find("#apoio_am_form").validationEngine("validate");
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
                    am_zone.find("#table_tbody_ldp").append("<tr><td><input type='text' name='ldp_cp" + ldpinput_count + "' class='linha_cp validate[required,custom[onlyNumberSp]]' maxlength='4'></td><td><input type='text' name='ldp_freg" + ldpinput_count + "' class='linha_freg validate[required]'></td><td> <button class='btn btn-danger button_ldptable_remove_line icon-alone' ><i class='icon-minus'></i></button></td></tr>");
                    ldpinput_count++;
                }).click();
                //Remove Linhas
                am_zone.on("click", ".button_ldptable_remove_line", function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
                am_zone.on("change", "[name='horario_check']", function(e) {
                    if (~~$(this).val() === 1) {
                        am_zone.find("#horario_manha").show().end()
                                .find("#horario_tarde").show();
                    }
                    else if (~~$(this).val() === 2) {
                        am_zone.find("#horario_manha").show().end()
                                .find("#horario_tarde").hide();
                    }
                    else {
                        am_zone.find("#horario_manha").hide().end()
                                .find("#horario_tarde").show();
                    }
                    am_zone.find(".form_datetime_hour").val("").datetimepicker('setStartDate').datetimepicker('setEndDate');
                });
                //SUBMIT
                am_zone.find("#apoio_am_form").submit(function(e) {
                    e.preventDefault();
                    am_zone.find("#submit_am").prop('disabled', true);
                    if (am_zone.find("#apoio_am_form").validationEngine("validate")) {
                        if (am_zone.find("#table_tbody_ldp tr").length) {
                            $("#apoio_am_form :input").attr('readonly', true);
                            var local_publicidade_array = [];
                            $.each(am_zone.find("#table_tbody_ldp").find("tr"), function() {
                                local_publicidade_array.push({cp: $(this).find(".linha_cp").val(), freguesia: $(this).find(".linha_freg").val()});
                            });

                            $.msg();
                            $.post("/AM/ajax/requests.php", {action: "criar_apoio_marketing",
                                data_inicial: am_zone.find("#data_rastreio1").val(),
                                data_final: am_zone.find("#data_rastreio2").val(),
                                horario: {
                                    tipo: ~~am_zone.find("[name='horario_check']:checked").val(),
                                    inicio1: am_zone.find("#data_inicio1").val(),
                                    inicio2: am_zone.find("#data_inicio2").val(),
                                    fim1: am_zone.find("#data_fim1").val(),
                                    fim2: am_zone.find("#data_fim2").val()},
                                localidade: am_zone.find("#input_localidade").val(),
                                local: am_zone.find("#input_local_rastreio").val(),
                                morada: am_zone.find("#input_morada_rastreio").val(),
                                comments: am_zone.find("#input_observaçoes").val(),
                                local_publicidade: local_publicidade_array},
                            function() {
                                $('#apoio_am_form').stepy('step', 1);
                                am_zone.find("#apoio_am_form").get(0).reset();
                                am_zone.find("#submit_am").prop('disabled', false);
                                $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                                $("#apoio_am_form :input").attr('readonly', false);
                                $.msg('unblock');
                            }, "json").fail(function(data) {
                                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                $.msg('unblock', 5000);
                            });
                        }
                        else
                            $.jGrowl("Selecione pelo menos uma Freguesia/Código de Postal", {life: 4000});
                    }
                });


            });
        },
        get_to_datatable: function(am_zone) {
            var apoio_markting_table = am_zone.dataTable({
                "aaSorting": [[18, "asc"]],
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requests.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_apoio_marketing_to_datatable"});
                },
                "aoColumns": [{"sTitle": "ID", "sWidth": "35px"},
                    {"sTitle": "User", "bVisible": (SpiceU.user_level > 5)}, 
                    {"sTitle": "Data pedido"},
                    {"sTitle": "Data inicial/Data final", "sWidth": "65px"}, 
                    {"sTitle": "Data final", "sWidth": "65px"},
                    {"sTitle": "Horario", "sWidth": "46px"},
                    {"sTitle": "Localidade"}, 
                    {"sTitle": "Local"},
                    {"sTitle": "Morada"},
                    {"sTitle": "Observações"}, 
                    {"sTitle": "Pub.", "sWidth": "1px"},
                    {"sTitle": "Cod."},
                    {"sTitle": "Total", "sWidth": "5px"},
                    {"sTitle": "c/ Perda", "sWidth": "1px"},
                    {"sTitle": "Vendas", "sWidth": "1px"}, 
                    {"sTitle": "Valor", "sWidth": "1px"},
                    {"sTitle": "Estado", "sWidth": "60px"},
                    {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "sort", "bVisible": false}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_AM').click(function(event) {
                event.preventDefault();
                table2csv(apoio_markting_table, 'full', '#' + am_zone[0].id);
            });
            am_zone.on("click", ".ver_horario", function() {
                var id = ~~$(this).data().apoio_marketing_id;
                $.msg();
                $.post("ajax/requests.php", {action: "get_horario_from_apoio_marketing", id: id}, function(horario) {
                    basic_path.find("#ver_horario_modal .horario_all_master").hide();
                    if (~~horario.tipo === 1)
                        basic_path.find("#ver_horario_modal .horario_all_master").show();
                    if (~~horario.tipo === 2)
                        basic_path.find("#ver_horario_modal #horario_manha").show();
                    if (~~horario.tipo === 3)
                        basic_path.find("#ver_horario_modal #horario_tarde").show();
                    basic_path.find("#ver_horario_modal #manha_inicio").text(horario.inicio1);
                    basic_path.find("#ver_horario_modal #manha_fim").text(horario.inicio2);
                    basic_path.find("#ver_horario_modal #tarde_inicio").text(horario.fim1);
                    basic_path.find("#ver_horario_modal #tarde_fim").text(horario.fim2);
                    basic_path.find("#ver_horario_modal").modal("show");
                    $.msg('unblock');
                }, "json").fail(function(data) {
                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
            });
            am_zone.on("click", ".ver_local_publicidade", function() {
                var id = ~~$(this).data().apoio_marketing_id;
                $.msg();
                $.post("/AM/ajax/requests.php", {action: "get_locais_publicidade_from_apoio_marketing", id: id}, function(data) {
                    basic_path.find("#ver_local_publicidade_modal #tbody_ver_local_publicidade").empty();
                    $.each(data, function() {
                        basic_path.find("#ver_local_publicidade_modal #tbody_ver_local_publicidade").append("<tr><td>" + this.cp + "</td><td>" + this.freguesia + "</td></tr>");
                    });
                    basic_path.find("#ver_local_publicidade_modal").modal("show");
                    $.msg('unblock');
                }, "json").fail(function(data) {
                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
            });
            am_zone.on("click", ".accept_apoio_marketing", function() {
                var this_button = $(this);
                bootbox.prompt("Comentários?", function(result) {
                    if (result !== null) {
                        $.msg();
                        $.post('/AM/ajax/requests.php', {action: "accept_apoio_marketing", id: this_button.val(), message: result}, function() {
                            this_button.parent("td").prev().text("Aprovado");
                            apoio_markting_table.fnReloadAjax();
                            $.msg('unblock');
                        }, "json").fail(function(data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                });
            });
            am_zone.on("click", ".decline_apoio_marketing", function() {
                var this_button = $(this);
                bootbox.prompt("Qual o motivo?", function(result) {
                    if (result !== null) {
                        $.msg();
                        $.post('/AM/ajax/requests.php', {action: "decline_apoio_marketing", id: this_button.val(), message: result}, function() {
                            this_button.parent().prev().text("Rejeitado");
                            apoio_markting_table.fnReloadAjax();
                            $.msg('unblock');
                        }, "json").fail(function(data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                });
            });
        }
    };
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    //-------------------------------RELATORIO FROTA----------------------------RELATORIO FROTA----------------------RELATORIO FROTA------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    this.relatorio_frota = {
        init: function() {
            $.get("/AM/view/requests/relatorio_frota_modal.html", function(data) {
                basic_path.append(data);
            }, 'html');
        },
        new : function(rf_zone) {
            var rfinput_count = 2;
            rf_zone.empty().off();
            var availableTags = ["Revisão", "Mudança de óleo", "Mudança de pneus"];
            $.get("/AM/view/requests/relatorio_frota.html", function(data) {
                rf_zone.append(data);
                rf_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                rf_zone.find(".rf_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                rf_zone.find("#input_km").autoNumeric('init', {mDec: '0'});
                //Adiciona Linhas
                rf_zone.find("#button_rf_table_add_line").click(function(e) {
                    e.preventDefault();
                    rf_zone.find("#table_tbody_rf").append("<tr><td> <input size='16' type='text' name='rf_data" + rfinput_count + "' class='rf_datetime  linha_data span' readonly id='rf_datetime" + rfinput_count + "' placeholder='Data'  data-prompt-position='topRight:120'></td><td><input class='linha_ocorrencia span' type='text' name='rf_ocorr" + rfinput_count + "'></td><td><input class='linha_km text-right ' type='text' value='0' maxlength='6' name='rf_km" + rfinput_count + "' size='16'/></td><td><button class='btn btn-danger button_rf_table_remove_line icon-alone'><i class='icon-minus'></i></button></td></tr>");
                    rf_zone.find("#rf_datetime" + rfinput_count).datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                    rf_zone.find("[name='rf_km" + rfinput_count + "']").autoNumeric('init', {mDec: '0'});
                    $("[name='rf_ocorr" + rfinput_count + "']").autocomplete({source: availableTags});
                    rfinput_count++;
                }).click();
                //Remove Linhas
                rf_zone.on("click", ".button_rf_table_remove_line", function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
                //SUBMIT
                rf_zone.find("#relatorio_frota_form").submit(function(e) {
                    e.preventDefault();
                    var can_submit = 1;
                    if (rf_zone.find("#relatorio_frota_form").validationEngine("validate")) {
                        if (rf_zone.find("#table_tbody_rf tr").length) {
                            $.each(rf_zone.find("#table_tbody_rf").find(".linha_km"), function() {
                                if (~~$(this).autoNumeric('get') > ~~rf_zone.find("#input_km").autoNumeric('get')) {
                                    $.jGrowl("O número de Kms numa das ocorrências é superior aos Kms totais no relatório");
                                    can_submit = 0;
                                }
                            });
                            if (can_submit) {
                                var ocorrencias_array = [];
                                $.each(rf_zone.find("#table_tbody_rf").find("tr"), function() {
                                    if ($(this).find(".linha_data").val().length && $(this).find(".linha_ocorrencia").val().length && $(this).find(".linha_km").val().length)
                                        ocorrencias_array.push({
                                            data: $(this).find(".linha_data").val(),
                                            ocorrencia: $(this).find(".linha_ocorrencia").val(),
                                            km: $(this).find(".linha_km").autoNumeric('get')});
                                });
                                $("#relatorio_frota_form :input").attr('readonly', true);
                                $.msg();
                                $.post("/AM/ajax/requests.php", {action: "criar_relatorio_frota",
                                    data: rf_zone.find("#input_data").val(),
                                    matricula: rf_zone.find("#input_matricula").val(),
                                    km: rf_zone.find("#input_km").autoNumeric('get'),
                                    viatura: rf_zone.find(":radio[name='rrf']:checked").val(),
                                    ocorrencias: ocorrencias_array,
                                    comments: rf_zone.find("#input_comments").val().length ? rf_zone.find("#input_comments").val() : ""},
                                function() {
                                    rf_zone.find("#relatorio_frota_form").get(0).reset();
                                    $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                                    $("#relatorio_frota_form :input").attr('readonly', false);
                                    $.msg('unblock');
                                }, "json").fail(function(data) {
                                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                    $.msg('unblock', 5000);
                                });

                            }
                        }
                        else
                            $.jGrow("selecione pelo menos uma ocorrencia", {life: 4000});
                    }
                });
            });
        },
        get_to_datatable: function(rf_zone) {
            var relatorio_frota_table = rf_zone.dataTable({
                "aaSorting": [[10, "asc"]],
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requests.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_relatorio_frota_to_datatable"});
                },
                "aoColumns": [{"sTitle": "ID", "sWidth": "35px"},
                    {"sTitle": "User", "sWidth": "80px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "Data", "sWidth": "80px"},
                    {"sTitle": "Matrícula", "sWidth": "60px"},
                    {"sTitle": "Km", "sWidth": "65px"},
                    {"sTitle": "Viatura", "sWidth": "90px"},
                    {"sTitle": "Observações"},
                    {"sTitle": "Ocorrências", "sWidth": "40px"},
                    {"sTitle": "Estado", "sWidth": "80px"},
                    {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "sort", "bVisible": false}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_F').click(function(event) {
                event.preventDefault();
                table2csv(relatorio_frota_table, 'full', '#' + rf_zone[0].id);
            });
            rf_zone.on("click", ".ver_ocorrencias", function() {
                var id = $(this).data().relatorio_frota_id;
                $.msg();
                $.post("ajax/requests.php", {action: "get_ocorrencias_frota", id: id}, function(data) {
                    var tbody = basic_path.find("#ver_occorrencia_frota_modal #ver_occorrencia_frota_tbody");
                    tbody.empty();
                    $.each(data, function() {
                        tbody.append("<tr><td>" + this.data + "</td><td>" + this.km + "</td><td>" + this.ocorrencia + "</td></tr>");
                    });
                    basic_path.find("#ver_occorrencia_frota_modal").modal("show");
                    $.msg('unblock');
                }, "json").fail(function(data) {
                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
            });
            rf_zone.on("click", ".accept_report_frota", function() {
                var this_button = $(this);
                bootbox.prompt("Comentários?", function(result) {
                    if (result !== null) {
                        $.msg();
                        $.post('/AM/ajax/requests.php', {action: "accept_report_frota", id: this_button.val(), message: result}, function() {
                            this_button.parent("td").prev().text("Aprovado");
                            relatorio_frota_table.fnReloadAjax();
                            $.msg('unblock');
                        }, "json").fail(function(data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                });
            });
            rf_zone.on("click", ".decline_report_frota", function() {
                var this_button = $(this);
                bootbox.prompt("Qual o motivo?", function(result) {
                    if (result !== null) {
                        $.msg();
                        $.post('/AM/ajax/requests.php', {action: "decline_report_frota", id: this_button.val(), message: result}, function() {
                            this_button.parent("td").prev().text("Rejeitado");
                            relatorio_frota_table.fnReloadAjax();
                            $.msg('unblock');
                        }, "json").fail(function(data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                });
            });
        }};
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    //-------------------------------RELATORIO CORREIO----------------------------RELATORIO CORREIO----------------------RELATORIO CORREIO------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    this.relatorio_correio = {
        init: function() {
            $.get("/AM/view/requests/relatorio_correio_modal.html", function(data) {
                basic_path.append(data);
                if (SpiceU.user_level <= 5)
                {
                    basic_path.find("#correio_modal_div .anexo_add_button").hide();
                    basic_path.find("#anexo_save_button").off().text("Sair").data("dismiss", "modal");
                }
            }, 'html');
        },
        new : function(rc_zone) {
            rc_zone.empty().off();
            $.get("/AM/view/requests/relatorio_correio.html", function(data) {
                rc_zone.append(data);
                rc_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
                //SUBMIT
                rc_zone.find("#relatorio_correio_form").submit(function(e) {
                    e.preventDefault();
                    if (rc_zone.find("#relatorio_correio_form").validationEngine("validate")) {
                        if (rc_zone.find("#doc_obj_table_tbody tr").length) {
                            var docs_objs = [];
                            $.each(rc_zone.find("#doc_obj_table_tbody tr"), function() {
                                docs_objs.push({anexo: $(this).find(".input_anexo").val(), n_doc: $(this).find(".input_n_doc").val(), lead_id: $(this).find(".input_lead_id").val(), confirmed: false, admin: 0});
                            });
                            $("#relatorio_correio_form :input").attr('readonly', false);
                            $.msg();
                            $.post("ajax/requests.php", {action: "criar_relatorio_correio",
                                carta_porte: rc_zone.find("#input_carta_porte").val(),
                                data: rc_zone.find("#data_envio_datetime").val(),
                                doc: rc_zone.find("#input_doc").val(),
                                lead_id: rc_zone.find("#input_lead_id").val(),
                                client_name: rc_zone.find("#input_client_name").val(),
                                input_doc_obj_assoc: docs_objs,
                                comments: rc_zone.find("#input_comments").val().length ? rc_zone.find("#input_comments").val() : "Sem observações"},
                            function() {
                                rc_zone.find("#relatorio_correio_form").get(0).reset();
                                $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                                $.msg('unblock');
                            }, "json").fail(function(data) {
                                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                $.msg('unblock', 5000);
                            });
                        }
                        else
                            $.jGrowl("Selecione pelo menos um ", {life: 4000});
                    }

                });
                //Criar e Remover Linhas (CRIAÇÂO)-----------------
                rc_zone.find("#add_line_obj_doc").click(function(e) {
                    e.preventDefault();
                    rc_zone.find("#doc_obj_table_tbody").append("<tr>\n\
<td><input class='validate[required] span input_anexo' type='text' /></td>\n\
<td><input class='validate[required] span input_n_doc' type='text' /></td>\n\
<td><input class='validate[required] span input_lead_id' type='text' /></td>\n\
<td><button class='btn btn-danger remove_doc_obj icon-alone'><i class='icon icon-minus'></i></button></td></tr>");
                }).click();
                rc_zone.on("click", ".remove_doc_obj", function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
                //---------------------------------------------------
            });
        },
        get_to_datatable: function(rc_zone) {

            var relatorio_correio_table = rc_zone.dataTable({
                "aaSorting": [[8, "asc"]],
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
                "aoColumns": [{"sTitle": "ID", "sWidth": "35px"},
                    {"sTitle": "User", "sWidth": "80px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "Carta Porte", "sWidth": "60px"},
                    {"sTitle": "Data Envio", "sWidth": "70px"},
                    {"sTitle": "Documento", "sWidth": "30px"},
                    {"sTitle": "Observações"},
                    {"sTitle": "Estado", "sWidth": "80px"},
                    {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "sort", "bVisible": false}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_C').click(function(event) {
                event.preventDefault();
                table2csv(relatorio_correio_table, 'full', '#' + rc_zone[0].id);
            });
            rc_zone.on("click", ".accept_report_correio", function() {
                var this_button = $(this);
                if ($(this).parents("td").prev().prev().prev().find("button").data().approved) {
                    bootbox.prompt("Comentários?", function(result) {
                        if (result !== null) {
                            $.msg();

                            $.post('/AM/ajax/requests.php', {action: "accept_report_correio", id: this_button.val(), message: result}, function() {
                                this_button.parent("td").prev().text("Aprovado");
                                relatorio_correio_table.fnReloadAjax();
                                $.msg('unblock');
                            }, "json").fail(function(data) {
                                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                $.msg('unblock', 5000);
                            });
                        }
                    });
                }
                else
                    $.jGrowl("Verifique os anexos 1º, antes de aprovar.");
            });
            rc_zone.on("click", ".decline_report_correio", function() {
                var this_button = $(this);
                bootbox.prompt("Qual o motivo?", function(result) {
                    if (result !== null) {
                        $.msg();
                        $.post('/AM/ajax/requests.php', {action: "decline_report_correio", id: this_button.val(), message: result}, function() {
                            this_button.parent("td").prev().text("Rejeitado");
                            this_button.parent("tr").find(".ver_anexo_correio").data("aproved", 0);
                            relatorio_correio_table.fnReloadAjax();
                            $.msg('unblock');
                        }, "json").fail(function(data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                });
            });
            basic_path.on("click", "#anexo_add_button", function(e) {
                e.preventDefault();
                $("#correio_modal_div #add_anexo_div").show();
                $("#correio_modal_div .anexo_menu_button").hide();
            });
            basic_path.on("click", "#correio_modal_div #add_anexo_yes_button", function(e) {
                e.preventDefault();
                if ($("#correio_modal_div #add_anexo_div").validationEngine("validate")) {
                    var anexo_array = [];
                    var data_button = $(this).parents(".modal").find("#correio_modal_div #anexo_save_button");
                    var temp = {anexo: $("#correio_modal_div #add_anexo_ficheiro").val(), n_doc: $("#correio_modal_div #add_anexo_documento").val(), lead_id: $("#correio_modal_div #add_anexo_lead_id").val(), confirmed: 0, admin: 1};
                    anexo_array.push(temp);
                    $.each($("#ver_anexo_correio_modal #tbody_ver_anexo_correio").find("tr"), function() {
                        anexo_array.push({anexo: $(this).find(".input_anexo").text(), n_doc: $(this).find(".input_n_doc").text(), lead_id: $(this).find(".input_lead_id").text(), confirmed: ~~$(this).find(".checkbox_confirm_anexo").is(":checked"), admin: ~~$(this).hasClass("warning")});
                    });
                    $.msg();
                    $.post('/AM/ajax/requests.php', {action: "save_anexo_correio", id: data_button.data().id_correio, anexos: anexo_array}, function() {
                        var id_anexo = ~~basic_path.find("#tbody_ver_anexo_correio").find("tr").last().find(".checkbox_confirm_anexo").val() + 1;
                        basic_path.find("#tbody_ver_anexo_correio").append("<tr class='warning'><td class='chex-table'><input " + ((SpiceU.user_level < 5) ? "disabled" : "") + " type='checkbox' value='" + id_anexo + "' class='checkbox_confirm_anexo '  id='anexo" + id_anexo + "' name='cci'><label class='checkbox inline' for='anexo" + id_anexo + "'><span></span> </label></td><td class='input_anexo'>" + temp.anexo + "</td><td class='input_n_doc'>" + temp.n_doc + "</td><td class='input_lead_id'>" + temp.lead_id + "</td></tr>");
                        $("#correio_modal_div #add_anexo_div")[0].reset();
                        $("#correio_modal_div #add_anexo_div").hide();
                        $("#correio_modal_div .anexo_menu_button").show();
                        $("#anexo_modal_correio_warning").show();
                        $.msg('unblock');
                    }, "json").fail(function(data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
                }
            });
            basic_path.on("click", "#correio_modal_div #add_anexo_no_button", function(e) {
                e.preventDefault();
                $("#correio_modal_div #add_anexo_div")[0].reset();
                $("#correio_modal_div #add_anexo_div").hide();
                $("#correio_modal_div .anexo_menu_button").show();
            });
            rc_zone.on("click", ".ver_anexo_correio", function(e) {
                e.preventDefault();
                var
                        id_anexo = ~~$(this).data().anexo_id,
                        anexo_number = 1,
                        status = ~~$(this).data().approved;
                $("#anexo_add_button").toggle(SpiceU.user_level > 5 ? true : false);
                $.msg();
                $.post("ajax/requests.php", {action: "get_anexo_correio", id: id_anexo},
                function(data) {
                    var tbody = basic_path.find("#tbody_ver_anexo_correio").empty();
                    var alert_class = "class='alert'";
                    $("#anexo_modal_correio_warning").hide();
                    $.each(data, function() {
                        if (~~this.admin) {
                            $("#anexo_modal_correio_warning").show();
                            alert_class = "class='warning'";
                        }
                        else {
                            alert_class = "";
                        }
                        tbody.append("<tr " + alert_class + " ><td class='chex-table'><input " + ((status || (SpiceU.user_level < 5)) ? "disabled" : "") + " type='checkbox' value='" + id_anexo + "' class='checkbox_confirm_anexo ' " + ((~~this.confirmed) ? "checked" : "") + " id='anexo_correio_" + anexo_number + "' name='cci'><label class='checkbox inline' for='anexo_correio_" + anexo_number + "'><span></span></label></td><td class='input_anexo'>" + this.anexo + "</td><td class='input_n_doc'>" + this.n_doc + "</td><td class='input_lead_id'>" + this.lead_id + "</td></tr>");
                        anexo_number++;
                    });
                    basic_path.find("#correio_modal_div #anexo_save_button").data("id_correio", id_anexo);
                    basic_path.find("#ver_anexo_correio_modal").modal("show");
                    $.msg('unblock');
                }, "json").fail(function(data) {
                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
            });
            basic_path.on("click", "#correio_modal_div #anexo_save_button", function(e) {
                var
                        anexo_array = [],
                        this_button = $(this),
                        data_ver_button = rc_zone.find("[data-anexo_id='" + this_button.data().id_correio + "']");
                if (~~data_ver_button.data().approved)
                    return false;
                data_ver_button.data().approved = 1;
                $.each(this_button.parents("#ver_anexo_correio_modal").find("#tbody_ver_anexo_correio").find("tr"), function() {
                    anexo_array.push({anexo: $(this).find(".input_anexo").text(), n_doc: $(this).find(".input_n_doc").text(), lead_id: $(this).find(".input_lead_id").text(), confirmed: ~~$(this).find(".checkbox_confirm_anexo").is(":checked"), admin: ~~$(this).hasClass("warning")});
                    if (!~~$(this).find("td").first().find(":checkbox").is(":checked")) {
                        data_ver_button.data().approved = 0;
                    }
                });
                $.msg();
                $.post("/AM/ajax/requests.php", {action: "save_anexo_correio", id: this_button.data().id_correio, anexos: anexo_array}, function(data) {
                    $.msg('unblock');
                }, "json");
            });
        }
    };
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    //-------------------------------RELATORIO MENSAL STOCK----------------------------RELATORIO MENSAL STOCK----------------------RELATORIO MENSAL STOCK-----
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    this.relatorio_mensal_stock = {
        init: function() {
            $.get("/AM/view/requests/relatorio_mensal_stock_modal.html", function(data) {
                basic_path.append(data);

                if (SpiceU.user_level <= 5) {
                    basic_path.find("#stock_save_button").off().text("Sair").data("dismiss", "modal");
                }
            }, 'html');
        },
        new : function(rms_zone) {
            rms_zone.empty().off();
            $.get("/AM/view/requests/relatorio_mensal_stock.html", function(data) {
                rms_zone.append(data);
                rms_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).val(moment().format('YYYY-MM-DD'));
                rms_zone.find("#product_template").chosen({no_results_text: "Sem resultados"});

                //SUBMIT
                rms_zone.find("#relatorio_mensal_stock_form").submit(function(e) {
                    e.preventDefault();
                    if (rms_zone.find("#relatorio_mensal_stock_form").validationEngine("validate")) {
                        var prdt_objs = [];
                        if (rms_zone.find("#table_tbody_rfms tr").length) {
                            $.each(rms_zone.find("#table_tbody_rfms tr"), function() {
                                prdt_objs.push({quantidade: $(this).find(".quant").val(), descricao: $(this).find(".desc").val(), serie: $(this).find(".serie").val(), obs: $(this).find(".obs").val()});
                            });
                            $("#relatorio_mensal_stock_form :input").attr('readonly', true);
                            $.msg();
                            $.post("ajax/requests.php", {
                                action: "criar_relatorio_mensal_stock",
                                data: rms_zone.find("#input_data").val(),
                                produtos: prdt_objs
                            },
                            function() {
                                rms_zone.find("#relatorio_mensal_stock_form").get(0).reset();
                                $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                                $("#relatorio_mensal_stock_form :input").attr('readonly', false);
                                $.msg('unblock');
                            }, "json").fail(function(data) {
                                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                $.msg('unblock', 5000);
                            });
                        }
                        else
                            $.jGrowl("Preencha pelo menos uma linha.");
                    }
                });
                rms_zone.find("#button_rfms_template_product").click(function(e) {
                    e.preventDefault();
                    $.msg();
                    $.post('/AM/ajax/products.php', {action: "get_produto_by_id", id: 525}, function(data) {
                        rms_zone.find("#table_tbody_rfms").empty();
                        rms_zone.find("#button_rfms_table_add_line").click();
                        rms_zone.find("#table_tbody_rfms").find("tr").last().find(".desc").val(data.name);
                        if (data.children) {
                            $.each(data.children, function() {
                                rms_zone.find("#button_rfms_table_add_line").click();
                                rms_zone.find("#table_tbody_rfms").find("tr").last().find(".desc").val(this.name);
                            })
                        }
                        $.msg('unblock');
                    }, "json").fail(function(data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
                })

                rms_zone.find("#button_rfms_table_add_line").click(function(e) {
                    e.preventDefault();
                    rms_zone
                            .find("#table_tbody_rfms")
                            .append("<tr><td><input class='validate[required] span text-right quant' type='text' data-prompt-position='topRight:120' /></td> <td><input class='validate[required] span desc' type='text' /></td> <td><input class='validate[required] span serie' type='text' /></td> <td><textarea class='span obs' ></textarea></td> <td><button class='btn btn-danger remove_doc_obj icon-alone'><i class='icon icon-minus'></i></button></td></tr>")
                            .end()
                            .find(".quant")
                            .autotab('number');
                }).click();
                rms_zone.on("click", ".remove_doc_obj", function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });

                rms_zone.on("change", ".product_template", function(e) {


                });

            });
        },
        get_to_datatable: function(rms_zone) {
            var relatorio_stock_table = rms_zone.dataTable({
                "aaSorting": [[6, "asc"]],
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
                "aoColumns": [{"sTitle": "ID", "sWidth": "35px"},
                    {"sTitle": "User", "sWidth": "80px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "Data", "sWidth": "75px"},
                    {"sTitle": "Produtos", "sWidth": "75px"},
                    {"sTitle": "Estado"},
                    {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "sort", "bVisible": false}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_S').click(function(event) {
                event.preventDefault();
                table2csv(relatorio_stock_table, 'full', '#' + rms_zone[0].id);
            });
            rms_zone.on("click", ".accept_report_stock", function() {
                var this_button = $(this);
                if (this_button.parents("td").prev().prev().find("button").data().approved) {
                    bootbox.prompt("Comentários?", function(result) {
                        if (result !== null) {
                            $.msg();
                            $.post('/AM/ajax/requests.php', {action: "accept_report_stock", id: this_button.val(), message: result}, function() {
                                this_button.parent("td").prev().text("Aprovado");
                                relatorio_stock_table.fnReloadAjax();
                                $.msg('unblock');
                            }, "json").fail(function(data) {
                                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                $.msg('unblock', 5000);
                            });
                        }
                    });
                }
                else
                    $.jGrowl("Verifique os anexos 1º, antes de aprovar.");
            });
            rms_zone.on("click", ".decline_report_stock", function() {
                var this_button = $(this);
                bootbox.prompt("Qual o motivo?", function(result) {
                    if (result !== null) {
                        $.msg();
                        $.post('/AM/ajax/requests.php', {action: "decline_report_stock", id: this_button.val(), message: result}, function() {
                            this_button.parent("td").prev().text("Rejeitado");
                            this_button.parent("tr").find(".ver_produto_stock").data("aproved", 0);
                            relatorio_stock_table.fnReloadAjax();
                            $.msg('unblock');
                        }, "json").fail(function(data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                });
            });
            basic_path.on("click", "#stock_add_button", function(e) {
                e.preventDefault();
                $("#stock_modal_div #add_stock_div").show();
                $("#stock_modal_div .stock_menu_button").hide();
            });
            basic_path.on("click", "#stock_modal_div #add_stock_yes_button", function(e) {
                e.preventDefault();
                if ($("#stock_modal_div #add_stock_div").validationEngine("validate")) {
                    var prod_array = [];
                    var data_button = $(this).parents(".modal").find("#stock_modal_div #stock_save_button");
                    var temp = {quantidade: $("#add_stock_div #add_stock_quantidade").val(), descricao: $("#add_stock_div #add_stock_descricao").val(), serie: $("#add_stock_div #add_stock_serie").val(), obs: $("#add_stock_div #add_stock_obs").val(), confirmed: 0, admin: 1};
                    prod_array.push(temp);
                    $.each($("#ver_anexo_stock_modal #tbody_ver_produto_mensal_stock").find("tr"), function() {
                        prod_array.push({quantidade: $(this).find(".td_helper_quantidade").text(), descricao: $(this).find(".td_helper_descricao").text(), serie: $(this).find(".td_helper_serie").text(), obs: $(this).find(".td_helper_obs").text(), confirmed: ~~$(this).find("td").first().find(":checkbox").is(":checked"), admin: ~~$(this).hasClass("warning")});
                    });
                    $.msg();
                    $.post('/AM/ajax/requests.php', {action: "save_stock", id: data_button.data().id_stock, produtos: prod_array}, function() {
                        var id_anexo = ~~basic_path.find("#tbody_ver_produto_mensal_stock").find("tr").last().find(".checkbox_confirm_anexo").val() + 1;
                        basic_path.find("#tbody_ver_produto_mensal_stock").append("<tr class='warning'><td class='chex-table'><input " + ((SpiceU.user_level < 5) ? "disabled" : "") + " type='checkbox' value='" + id_anexo + "' class='checkbox_confirm_anexo '  id='anexo" + id_anexo + "' name='cci'><label class='checkbox inline' for='anexo" + id_anexo + "'><span></span> </label></td><td class='td_helper_quantidade'>" + temp.quantidade + "</td><td class='td_helper_descricao'>" + temp.descricao + "</td><td class='td_helper_serie'>" + temp.serie + "</td><td class='td_helper_obs'>" + temp.obs + "</td></tr>");
                        $("#stock_modal_div #add_stock_div")[0].reset();
                        $("#stock_modal_div #add_stock_div").hide();
                        $("#stock_modal_div .stock_menu_button").show();
                        $("#stock_modal_correio_warning").show();
                        $.msg('unblock');
                    }, "json").fail(function(data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
                }
            });
            basic_path.on("click", "#stock_modal_div #add_stock_no_button", function(e) {
                e.preventDefault();
                $("#stock_modal_div #add_stock_div")[0].reset();
                $("#stock_modal_div #add_stock_div").hide();
                $("#stock_modal_div .stock_menu_button").show();
            });
            basic_path.on("click", "#stock_save_button", function(e) {
                var
                        anexo_array = [],
                        this_button = $(this),
                        data_ver_button = rms_zone.find("[data-stock_id='" + this_button.data().id_stock + "']");
                if (~~data_ver_button.data().approved)
                    return false;
                data_ver_button.data().approved = 1;
                $.each($("#ver_anexo_mensal_stock_modal #tbody_ver_produto_mensal_stock").find("tr"), function()
                {
                    anexo_array.push({quantidade: $(this).find(".td_helper_quantidade").text(), descricao: $(this).find(".td_helper_descricao").text(), serie: $(this).find(".td_helper_serie").text(), obs: $(this).find(".td_helper_obs").text(), confirmed: ~~$(this).find("td").first().find(":checkbox").is(":checked"), admin: ~~$(this).hasClass("warning")});
                    if (!~~$(this).find("td").first().find(":checkbox").is(":checked"))
                        data_ver_button.data().approved = 0;
                });
                $.msg();
                $.post("/AM/ajax/requests.php", {action: "save_stock", id: this_button.data().id_stock, produtos: anexo_array}, function(data) {
                    $.msg('unblock');
                }, "json");
            });
            rms_zone.on("click", ".ver_produto_stock", function(e) {
                e.preventDefault();
                var
                        id_stock = ~~$(this).data().stock_id,
                        anexo_number = 1,
                        status = ~~$(this).data().approved;
                $("#stock_add_button").toggle(SpiceU.user_level > 5 ? true : false);
                $.msg();
                $.post("ajax/requests.php", {action: "get_itens_stock", id: id_stock},
                function(data1) {
                    var tbody = basic_path.find("#tbody_ver_produto_mensal_stock").empty();
                    var alert_class = "class='alert'";
                    $("#stock_modal_correio_warning").hide();
                    $.each(data1, function() {
                        if (~~this.admin) {
                            $("#stock_modal_correio_warning").show();
                            alert_class = "class='warning'";
                        }
                        else {
                            alert_class = "";
                        }
                        tbody.append("<tr " + alert_class + " ><td class='chex-table'><input " + ((status || (SpiceU.user_level < 5)) ? "disabled" : "") + " type='checkbox' value='" + id_stock + "' class='checkbox_confirm_anexo' " + ((~~this.confirmed) ? "checked" : "") + " id='anexo_stock_" + anexo_number + "' name='cci'><label class='checkbox inline' for='anexo_stock_" + anexo_number + "'><span></span> </label></td><td class='td_helper_quantidade'>" + this.quantidade + "</td><td class='td_helper_descricao'>" + this.descricao + "</td><td class='td_helper_serie'>" + this.serie + "</td><td class='td_helper_obs'>" + this.obs + "</td></tr>");
                        anexo_number++;
                    });
                    basic_path.find("#stock_save_button").data("id_stock", id_stock);
                    basic_path.find("#ver_anexo_mensal_stock_modal").modal("show");
                    $.msg('unblock');
                }, "json").fail(function(data) {
                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
            });
        }
    };
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    //-------------------------------RELATORIO MOVIMENTAÇAO STOCK----------------------------RELATORIO MOVIMENTAÇAO STOCK----------------------RELATORIO MOVIMENTAÇAO STOCK------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    this.relatorio_movimentacao_stock = {
        init: function() {
            $.get("/AM/view/requests/relatorio_movimentacao_stock_modal.html", function(data) {
                basic_path.append(data);
                if (SpiceU.user_level <= 5) {
                    basic_path.find("#mov_save_button").off().text("Sair").data("dismiss", "modal");
                }
            }, 'html');
        },
        new : function(rmovs) {
            rmovs.empty().off();
            $.get("/AM/view/requests/relatorio_movimentacao_stock.html", function(data) {
                rmovs.append(data);
                rmovs.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).val(moment().format('YYYY-MM-DD'));
                //SUBMIT
                rmovs.find("#relatorio_movimentacao_stock_form").submit(function(e) {
                    e.preventDefault();
                    if (rmovs.find("#relatorio_movimentacao_stock_form").validationEngine("validate")) {
                        var prdt_objs = [];
                        if (rmovs.find("#table_tbody_rfms tr").length) {
                            $.each(rmovs.find("#table_tbody_rfms tr"), function() {
                                prdt_objs.push({quantidade: $(this).find(".quant").val(), destinatario: $(this).find(".destinatario").val(), descricao: $(this).find(".descricao").val(), serie: $(this).find(".serie").val(), obs: $(this).find(".obs").val()});
                            });
                            $("#relatorio_movimentacao_stock_form :input").attr('readonly', true);
                            $.msg();
                            $.post("ajax/requests.php", {
                                action: "criar_relatorio_movimentacao_stock",
                                data: rmovs.find("#input_data").val(),
                                produtos: prdt_objs
                            },
                            function() {
                                rmovs.find("#relatorio_movimentacao_stock_form").get(0).reset();
                                $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                                $("#relatorio_movimentacao_stock_form :input").attr('readonly', false);
                                $.msg('unblock');
                            }, "json").fail(function(data) {
                                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                $.msg('unblock', 5000);
                            });
                        }
                        else
                            $.jGrowl("Preencha pelo menos uma linha.");
                    }
                });
                //Criar e Remover Linhas (CRIAÇÂO)-----------------
                rmovs.find("#button_rfms_table_add_line").click(function(e) {
                    e.preventDefault();
                    rmovs
                            .find("#table_tbody_rfms")
                            .append("<tr><td><input class='validate[required] span text-right quant' type='text' data-prompt-position='topRight:120' /></td> <td><input class='validate[required] span destinatario' type='text' data-prompt-position='topRight:120' /></td> <td><input class='validate[required] span descricao' type='text' data-prompt-position='topRight:120' /></td> <td><input class='validate[required] span serie' type='text' data-prompt-position='topRight:120' /></td> <td><textarea class='span obs' ></textarea></td> <td><button class='btn btn-danger remove_doc_obj icon-alone'><i class='icon icon-minus'></i></button></td></tr>")
                            .end()
                            .find(".quant")
                            .autotab('number');
                }).click();
                rmovs.on("click", ".remove_doc_obj", function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
                //------------------------------------------------------
            });
        },
        get_to_datatable: function(rmovs) {
            var relatorio_moviment_stock_table = rmovs.dataTable({
                "aaSorting": [[6, "asc"]],
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
                "aoColumns": [{"sTitle": "ID", "sWidth": "35px"},
                    {"sTitle": "User", "sWidth": "80px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "Data", "sWidth": "125px"},
                    {"sTitle": "Produtos", "sWidth": "75px"},
                    {"sTitle": "Estado"},
                    {"sTitle": "Opções", "sWidth": "50px", "bVisible": (SpiceU.user_level > 5)},
                    {"sTitle": "sort", "bVisible": false}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            $('#export_MS').click(function(event) {
                event.preventDefault();
                table2csv(relatorio_moviment_stock_table, 'full', '#' + rmovs[0].id);
            });
            rmovs.on("click", ".accept_report_movimentacao", function() {
                var this_button = $(this);
                if (this_button.parents("td").prev().prev().find("button").data().approved) {
                    bootbox.prompt("Comentários?", function(result) {
                        if (result !== null) {
                            $.msg();
                            $.post('/AM/ajax/requests.php', {action: "accept_report_movimentacao", id: this_button.val(), message: result}, function() {
                                this_button.parent("td").prev().text("Aprovado");
                                relatorio_moviment_stock_table.fnReloadAjax();
                                $.msg('unblock');
                            }, "json").fail(function(data) {
                                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                $.msg('unblock', 5000);
                            });
                        }
                    });
                }
                else
                    $.jGrowl("Verifique os anexos 1º, antes de aprovar.");
            });
            rmovs.on("click", ".decline_report_movimentacao", function() {
                var this_button = $(this);
                bootbox.prompt("Qual o motivo?", function(result) {
                    if (result !== null) {
                        $.msg();
                        $.post('/AM/ajax/requests.php', {action: "decline_report_movimentacao", id: this_button.val(), message: result}, function() {
                            this_button.parent("td").prev().text("Rejeitado");
                            relatorio_moviment_stock_table.fnReloadAjax();
                            $.msg('unblock');
                        }, "json").fail(function(data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                });
            });
            basic_path.on("click", "#mov_add_button", function(e) {
                e.preventDefault();
                $("#mov_modal_div #add_mov_div").show();
                $("#mov_modal_div .mov_menu_button").hide();
            });
            basic_path.on("click", "#mov_modal_div #add_mov_yes_button", function(e) {
                e.preventDefault();
                if ($("#mov_modal_div #add_mov_div").validationEngine("validate")) {
                    var prod_array = [];
                    var data_button = $(this).parents(".modal").find("#mov_modal_div #mov_save_button");
                    var temp = {destinatario: $("#add_mov_div #add_mov_destin").val(), quantidade: $("#add_mov_div #add_mov_quantidade").val(), descricao: $("#add_mov_div #add_mov_descricao").val(), serie: $("#add_mov_div #add_mov_serie").val(), obs: $("#add_mov_div #add_mov_obs").val(), confirmed: 0, admin: 1};
                    prod_array.push(temp);
                    $.each($("#ver_anexo_mov_stock_modal #tbody_ver_produto_movimentacao_mov").find("tr"), function() {
                        prod_array.push({destinatario: $(".td_helper_destinatario").val(), quantidade: $(this).find(".td_helper_quantidade").text(), descricao: $(this).find(".td_helper_descricao").text(), serie: $(this).find(".td_helper_serie").text(), obs: $(this).find(".td_helper_obs").text(), confirmed: ~~$(this).find("td").first().find(":checkbox").is(":checked"), admin: ~~$(this).hasClass("warning")});
                    });
                    $.msg();
                    $.post('/AM/ajax/requests.php', {action: "save_mov_stock", id: data_button.data().id_movimentacao, produtos: prod_array}, function() {
                        var id_anexo = ~~basic_path.find("#tbody_ver_produto_mensal_mov").find("tr").last().find(".checkbox_confirm_anexo").val() + 1;
                        basic_path.find("#tbody_ver_produto_movimentacao_mov").append("<tr class='warning'><td class='chex-table'><input " + ((SpiceU.user_level < 5) ? "disabled" : "") + " type='checkbox' value='" + id_anexo + "' class='checkbox_confirm_anexo '  id='anexo" + id_anexo + "' name='cci'><label class='checkbox inline' for='anexo" + id_anexo + "'><span></span> </label></td><td class='td_helper_destinatario'>" + temp.destinatario + "</td><td class='td_helper_quantidade'>" + temp.quantidade + "</td><td class='td_helper_descricao'>" + temp.descricao + "</td><td class='td_helper_serie'>" + temp.serie + "</td><td class='td_helper_obs'>" + temp.obs + "</td></tr>");
                        $("#mov_modal_div #add_mov_div")[0].reset();
                        $("#mov_modal_div #add_mov_div").hide();
                        $("#mov_modal_div .mov_menu_button").show();
                        $("#mov_modal_correio_warning").show();
                        $.msg('unblock');
                    }, "json").fail(function(data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
                }
            });
            basic_path.on("click", "#mov_modal_div #add_mov_no_button", function(e) {
                e.preventDefault();
                $("#mov_modal_div #add_mov_div")[0].reset();
                $("#mov_modal_div #add_mov_div").hide();
                $("#mov_modal_div .mov_menu_button").show();
            });
            basic_path.on("click", "#mov_save_button", function(e) {
                var
                        anexo_array = [],
                        this_button = $(this),
                        data_ver_button = rmovs.find("[data-movimentacao_id='" + this_button.data().id_movimentacao + "']");
                if (~~data_ver_button.data().approved)
                    return false;
                data_ver_button.data().approved = 1;
                $.each($("#ver_anexo_mov_stock_modal #tbody_ver_produto_movimentacao_mov").find("tr"), function()
                {
                    anexo_array.push({destinatario: $(this).find(".td_helper_destinatario").text(), quantidade: $(this).find(".td_helper_quantidade").text(), descricao: $(this).find(".td_helper_descricao").text(), serie: $(this).find(".td_helper_serie").text(), obs: $(this).find(".td_helper_obs").text(), confirmed: ~~$(this).find("td").first().find(":checkbox").is(":checked"), admin: ~~$(this).hasClass("warning")});
                    if (!~~$(this).find("td").first().find(":checkbox").is(":checked"))
                        data_ver_button.data().approved = 0;
                });
                $.msg();
                $.post("/AM/ajax/requests.php", {action: "save_mov_stock", id: this_button.data().id_movimentacao, produtos: anexo_array}, function(data) {
                    $.msg('unblock');
                }, "json");
            });
            rmovs.on("click", ".ver_produto_mov_stock", function(e) {
                e.preventDefault();
                var
                        id_movimentacao = ~~$(this).data().movimentacao_id,
                        anexo_number = 1,
                        status = ~~$(this).data().approved;

                $("#mov_add_button").toggle(SpiceU.user_level > 5 ? true : false);
                $.msg();
                $.post("ajax/requests.php", {action: "get_itens_movimentacao", id: id_movimentacao},
                function(data1) {
                    var tbody = basic_path.find("#tbody_ver_produto_movimentacao_mov").empty();
                    var alert_class = "class='alert'";
                    $("#mov_modal_correio_warning").hide();
                    $.each(data1, function() {
                        if (~~this.admin) {
                            $("#mov_modal_correio_warning").show();
                            alert_class = "warning";
                        }
                        else {
                            alert_class = "";
                        }
                        tbody.append("\
                            <tr class='" + alert_class + "'>\n\
                                <td><input " + ((status || (SpiceU.user_level < 5)) ? "disabled" : "") + " type='checkbox' value='" + id_movimentacao + "' class='checkbox_confirm_anexo' " + ((~~this.confirmed) ? "checked" : "") + " id='anexo_mov_" + anexo_number + "' name='cci'><label class='checkbox inline' for='anexo_mov_" + anexo_number + "'><span></span> </label></td>\n\
                                <td class='td_helper_destinatario'>" + this.destinatario + "</td>\n\
                                <td class='td_helper_quantidade'>" + this.quantidade + "</td>\n\
                                <td class='td_helper_descricao'>" + this.descricao + "</td>\n\
                                <td class='td_helper_serie'>" + this.serie + "</td>\n\
                                <td class='td_helper_obs'>" + this.obs + "</td>\n\
                            </tr>");
                        anexo_number++;
                    });
                    basic_path.find("#mov_save_button").data("id_movimentacao", id_movimentacao);
                    basic_path.find("#ver_anexo_mov_stock_modal").modal("show");
                    $.msg('unblock');
                }, "json").fail(function(data) {
                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
            });

        }
    };
};

 