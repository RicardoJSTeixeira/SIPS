var
    ha_perda = 1,
    lead_id = 0,
    reserva_id = 0,
    right_ear = 0,
    left_ear = 0,
    script,
    consult_audiogra = undefined,
    consult_status = "",
    consult_closed = false,
    client_box,
    $main_consulta_div = $("#main_consulta_div"),
    gama = ["", "AM 100 (swift 70+)", "AM 90 (swift 90+)", "AM Dual M5", "Acto", "Agil", "Alta", "Alta Pro", "Chili 5", "Chili 7", "Chili 9", "Dual M7", "Dual M9", "AM Hit (BTE)", "AM Hit (Intra, Rite, BTE PW)", "Ino", "Ino Pro", "Intiga 10", "Intiga 6", "Intiga 8", "Nera", "Nera Pro", "Sumo DM"],
    modelo = ["", "CIC", "CIC PW", "CIC 75", "CIC 85", "MIC", "MIC PW", "MIC 75", "MIC 85", "ITC", "ITC PW", "ITC 75", "ITC 85", "ITC 90", "ITC 100", "ITE", "ITE 75", "ITE 85", "ITE 90", "ITE 100", "ITEFS 75", "ITEFS 85", "ITEFS 90", "ITEFS 100", "BTE", "BTE PW", "BTE 85", "BTE 100", "MINI-BTE", "MINI-BTE 85", "RITE", "RITE M", "RITE PW", "RITE 85", "RITE 100", "MINI-RITE M", "MINI-RITE PW", "MINI-RITE 85", "MINI-RITE 100"];
$(function () {
    $.msg();
    rse = getUrlVars();
    lead_id = atob(decodeURIComponent(rse.id));
    reserva_id = atob(decodeURIComponent(rse.rs));
    $("#dModelo, #eModelo").html(modelo.map(function (a) {
        return new Option(a, a);
    }));
    $("#dGama, #eGama, #p1modelo, #p2modelo, #p3modelo").html(gama.map(function (a) {
        return new Option(a, a);
    }));
    $("#dModelo, #dGama, #eModelo, #eGama, #p1modelo, #p2modelo, #p3modelo").chosen({no_results_text: "Sem resultados", width: "100%"});
    $('[data-toggle~="tooltip"]').tooltip({container: 'body'});
    $.msg();
    $.post("ajax/consulta.php", {action: "get_consulta", reserva_id: reserva_id},
        function (data) {
            if (data) {
                if (data.terceira_pessoa.tipo) {
                    $("#ca_s").prop("checked", true);
                    $("#3_pessoa_div").show();
                    $("[name='tp'][value='" + data.terceira_pessoa.tipo + "']").prop("checked", true);
                    $("#3_pessoa_input").val(data.terceira_pessoa.nome);
                }
                else
                    $("#ca_n").prop("checked", true);

                if (data.proposta_comercial) {
                    $("#button_create_proposta_comercial").hide();
                }
                $main_consulta_div
                    .find("#options_div").show().end()
                    .find("#exit_save").show().end()
                    .find("#terminar_consulta_div").toggle(!data.closed).end()
                    .find("#validate_audio_script_save_div").toggle(!data.closed).end()
                    .find("#audiograma_main_div").show().end()
                    .find("#script_main_div").show().end()
                    .find("#first_info_div").hide().end()
                    .find("#exam_outcome_div").end().find("[name=ne]").val([data.exame_razao]).end()
                    .find("input").prop("disabled", data.closed).end()
                    .find("select").prop("disabled", data.closed);
                if (data.produtos) {
                    $main_consulta_div
                        .find("#dGama").val(data.produtos.direito.gama).trigger("chosen:updated").end()
                        .find("#eGama").val(data.produtos.esquerdo.gama).trigger("chosen:updated").end()
                        .find("#dMarca").val(data.produtos.direito.marca).end()
                        .find("#eMarca").val(data.produtos.esquerdo.marca).end()
                        .find("#dModelo").val(data.produtos.direito.modelo).trigger("chosen:updated").end()
                        .find("#eModelo").val(data.produtos.esquerdo.modelo).trigger("chosen:updated").end()
                        .find("[name=tp_vd]").val([data.produtos.tipo]);
                }
                $main_consulta_div.find("#exam_outcome_div").show();
                if (data.exame) {
                    if (data.venda) {
                        $main_consulta_div.find("#venda_yes").prop("checked", true).change();
                        $("#bnova_enc").show();
                    }
                    else {
                        $main_consulta_div
                            .find("#venda_no").prop("checked", true).change().end()
                            .find("#no_venda_select").val(data.venda_razao).end()
                            .find("#no_venda_div").show();
                    }
                } else {
                    if (data.closed) {
                        $main_consulta_div
                            .find("#first_info_div").show().end()
                            .find("#inicial_option_div").hide().end()
                            .find("#no_exam_div").show().end()
                            .find("[name=ne]").prop("disabled", true);
                    }
                }
                if (data.closed) {
                    consult_closed = true;
                }
            }
            script = new render($main_consulta_div.find("#script_placeholder"), "/sips-admin/script_dinamico/", undefined, lead_id, reserva_id, SpiceU.username, SpiceU.camp, 0, 0);
            var config = {save_overwrite: true, input_disabled: consult_closed};
            script.init(config);
            $main_consulta_div.find('#audiograma_placeholder').load("view/audiograma.html", function () {
                consult_audiogra = new Audiograma(lead_id);
                if (consult_closed) {
                    $main_consulta_div.find("#audiograma_main_div")
                        .find(":input").prop("disabled", true).end()
                        .find("button").prop("disabled", true).end().end()
                        .find("#script_main_div")
                        .find(":input").prop("disabled", true).end()
                        .find("button").prop("disabled", true).end().end()
                        .find("#3_pessoa_master_div")
                        .find(":input").prop("disabled", true).end().end()
                        .find("#options_div")
                        .find("#actions_exame").hide().end()
                        .find("#wconsulta-fechada").show();
                }
            });
            client_box = new ClientBox({id: reserva_id, byReserv: true});
            client_box.init();

            $.msg('unblock');
        },
        "json").fail(function () {
            $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
            $.msg('unblock', 5000);
        });
    $main_consulta_div.find("[name='ca']").change(function () {
        $("#3_pessoa_div").toggle(Boolean(~~$(this).val()));
    });

//EXAME
    $main_consulta_div.find("#pa_no").click(function () {
        consult_status = "no_exam";
        $main_consulta_div
            .find("#options_div").show().end()
            .find("#no_exam_div").show().end()
            .find("#terminar_consulta_div").show().end()
            .find("#inicial_option_div").hide().end()
            .find("#actions_exame").hide();
    });

    $main_consulta_div.find("#pa_yes").click(function () {
        consult_status = "yes_exam";
        $main_consulta_div
            .find("#options_div").show().end()
            .find("#audiograma_main_div").show().end()
            .find("#script_main_div").show().end()
            .find("#first_info_div").hide().end()
            .find("#actions_exame").show().end()
            .find("#validate_audio_script_div").show().end()
            .find("#validate_audio_script_save_div").show().end()
            .find("#first_info_div").hide();
    });

//VENDA
    $main_consulta_div.find("input[name='vp_a']").change(function () {
        result = $(this).val() === "yes";
        $main_consulta_div
            .find("#no_venda_div")
            .toggle(!result)
            .end()
            .find("#yes_venda_div")
            .toggle(result);
    });

    $main_consulta_div.find("#validate_audio_script").on("click", function () {
        var that = $(this).parent();
        consult_audiogra.validate(function () {
            script.validate_manual(function () {
                that.hide();
                $('html, body').animate({scrollTop: $(document).height()}, 'fast');
                ha_perda = consult_audiogra.calculate();
                $main_consulta_div.find("#exam_outcome_div").show();

                if (ha_perda) { //COM PERDA
                    right_ear = $main_consulta_div.find("#right_ear_value").val();
                    left_ear = $main_consulta_div.find("#left_ear_value").val();
                    $main_consulta_div.find("#terminar_consulta_div").show();
                }
                else { // SEM PERDA 
                    $main_consulta_div.find("#terminar_consulta_div").show();
                }
            });
        }, false);
    });

    $("#new_request_button, #bnova_enc").click(function () {
        var en = btoa(lead_id);
        $.history.push("view/new_requisition.html?id=" + en);
    });

    $(".new_marcacao_button").click(function () {
        var en = btoa(lead_id);
        $.history.push("view/calendar.html?id=" + en);
    });

//OPTIONS DIV--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $main_consulta_div.find(" #terminar_consulta").on("click", function () {
        if (consult_closed) {
            $.history.push('view/dashboard.html');
        }
        else {
            var produtos = {
                direito: {
                    marca: $main_consulta_div.find(" #dMarca").val(),
                    gama: $main_consulta_div.find("#dGama").val(),
                    modelo: $main_consulta_div.find("#dModelo").val()
                },
                esquerdo: {
                    marca: $main_consulta_div.find(" #eMarca").val(),
                    gama: $main_consulta_div.find(" #eGama").val(),
                    modelo: $main_consulta_div.find("#eModelo").val()
                },
                tipo: $main_consulta_div.find(" input[name=tp_vd]:checked").val()
            };

            if ($("#ca_s").is(":checked")) {
                if (!$("#3_pessoa_input").val().length) {
                    $.jGrowl("Certifique-se que preenche correctamente o nome da 3ª pessoa", {life: 4000});
                    scrollTop();
                    return false;
                }
            } else if (!$("[name=ca]:checked").length) {
                $.jGrowl("Preencha correctamente se há 3ª pessoa", {life: 4000});
                scrollTop();
                return false;
            }

            if (consult_status === "no_exam") {//NÂO HA EXAME
                if ($main_consulta_div.find(" #no_exam_div input[name='ne']:checked").length) {
                    $.msg();
                    $.post("ajax/consulta.php", {
                        action: "insert_consulta",
                        reserva_id: reserva_id,
                        lead_id: lead_id,
                        consulta: 1,
                        consulta_razao: "",
                        exame: "0",
                        exame_razao: $main_consulta_div.find(" #no_exam_div input[name='ne']:checked").val(),
                        venda: 0,
                        venda_razao: "",
                        left_ear: 0,
                        right_ear: 0,
                        tipo_aparelho: "",
                        descricao_aparelho: "",
                        produtos: produtos,
                        feedback: "STEST",
                        terceira_pessoa: $("#ca_s").is(":checked") ? {tipo: $("[name='tp']:checked").val(), nome: $("#3_pessoa_input").val()} : [],
                        closed: 1
                    }, function () {
                        $.jGrowl('Consulta gravada sem exame', {life: 3000});
                        $.msg('unblock');
                        $("#marcacao_modal").modal("show");
                    }, "json").fail(function () {
                        $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                        $.msg('unblock', 5000);
                    });
                }
            }
            else {//HA EXAME
                if ($("#no_venda_form").validationEngine("validate")) {
                    script.validate_manual(
                        function () {
                            consult_audiogra.validate(function () {
                                script.submit_manual();
                                consult_audiogra.save(lead_id, reserva_id, false);

                                if (ha_perda) {// HA PERDA 
                                    if (!$("[name=vp_a]:checked").length) {
                                        $.jGrowl('Indique se há venda.', {life: 3000});
                                        return false;
                                    }
                                    if ($main_consulta_div.find(" #venda_yes").is(":checked")) {//HA VENDA
                                        $.msg();
                                        $.post("ajax/consulta.php", {
                                            action: "insert_consulta",
                                            reserva_id: reserva_id,
                                            lead_id: lead_id,
                                            consulta: 1,
                                            consulta_razao: "",
                                            exame: "1",
                                            exame_razao: "",
                                            venda: 1,
                                            venda_razao: "",
                                            left_ear: $main_consulta_div.find("#left_ear_value").val(),
                                            right_ear: $main_consulta_div.find("#right_ear_value").val(),
                                            produtos: produtos,
                                            feedback: "TV",
                                            terceira_pessoa: $("#ca_s").is(":checked") ? {
                                                tipo: $("[name='tp']:checked").val(),
                                                nome: $("#3_pessoa_input").val()
                                            } : [],
                                            closed: 1
                                        }, function () {
                                            $.jGrowl('Consulta gravada com venda', {life: 3000});
                                            $.msg('unblock');
                                            $("#encomenda_modal").modal("show");
                                        }, "json").fail(function () {
                                            $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                                            $.msg('unblock', 5000);
                                        });
                                    }
                                    else {//NÂO HA VENDA
                                        $.msg();
                                        $.post("ajax/consulta.php", {
                                            action: "insert_consulta",
                                            reserva_id: reserva_id,
                                            lead_id: lead_id,
                                            consulta: 1,
                                            consulta_razao: "",
                                            exame: "1",
                                            exame_razao: "",
                                            venda: 0,
                                            venda_razao: $main_consulta_div.find("#no_venda_select option:selected").val(),
                                            left_ear: $main_consulta_div.find("#left_ear_value").val(),
                                            right_ear: $main_consulta_div.find("#right_ear_value").val(),
                                            produtos: produtos,
                                            feedback: "TNV",
                                            terceira_pessoa: $("#ca_s").is(":checked") ? {
                                                tipo: $("[name='tp']:checked").val(),
                                                nome: $("#3_pessoa_input").val()
                                            } : [],
                                            closed: 1
                                        }, function () {
                                            $.jGrowl('Consulta gravada sem venda', {life: 3000});
                                            $.msg('unblock');
                                            $("#marcacao_modal").modal("show");
                                        }, "json").fail(function () {
                                            $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                                            $.msg('unblock', 5000);
                                        });
                                    }
                                }
                                else {//NAO HA PERDA
                                    $.msg();
                                    $.post("ajax/consulta.php", {
                                        action: "insert_consulta",
                                        reserva_id: reserva_id,
                                        lead_id: lead_id,
                                        consulta: 1,
                                        consulta_razao: "",
                                        exame: "1",
                                        exame_razao: "",
                                        venda: 0,
                                        venda_razao: $main_consulta_div.find("#no_venda_select option:selected").val(),
                                        left_ear: $main_consulta_div.find("#left_ear_value").val(),
                                        right_ear: $main_consulta_div.find("#right_ear_value").val(),
                                        produtos: produtos,
                                        feedback: "SPERD",
                                        terceira_pessoa: $("#ca_s").is(":checked") ? {
                                            tipo: $("[name='tp']:checked").val(),
                                            nome: $("#3_pessoa_input").val()
                                        } : [],
                                        closed: 1
                                    }, function () {
                                        $.jGrowl('Consulta gravada sem perda', {life: 3000});
                                        $.msg('unblock');
                                        $("#marcacao_modal").modal("show");
                                    }, "json").fail(function () {
                                        $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                                        $.msg('unblock', 5000);
                                    });
                                }
                            }, false);
                        }, false);
                }
            }
        }
    });

    $("#dGama,#eGama").change(function () {
        $("[name=tp_vd]").val(($("#eGama").val().length > 0 && $("#dGama").val().length > 0) ? ["Bilateral"] : ["Unilateral"]);
    });

    $main_consulta_div.find("#exit_save").click(function () {
        $.msg();
        var produtos = {
            direito: {
                marca: $main_consulta_div.find("#dMarca").val(),
                gama: $main_consulta_div.find("#dGama").val(),
                modelo: $main_consulta_div.find(" #dModelo").val()
            },
            esquerdo: {
                marca: $main_consulta_div.find("#eMarca").val(),
                gama: $main_consulta_div.find(" #eGama").val(),
                modelo: $main_consulta_div.find("#eModelo").val()
            },
            tipo: $main_consulta_div.find(" input[name=tp_vd]:checked").val()
        };

        script.submit_manual();
        consult_audiogra.save(lead_id, reserva_id, false);
        $.msg();
        $.post("ajax/consulta.php", {
            action: "insert_consulta",
            reserva_id: reserva_id,
            lead_id: lead_id,
            consulta: 1,
            consulta_razao: "",
            exame: "1",
            exame_razao: "",
            venda: ~~$main_consulta_div.find("#venda_yes").is(":checked"),
            venda_razao: $main_consulta_div.find("#no_venda_select option:selected").val(),
            left_ear: $main_consulta_div.find("#left_ear_value").val(),
            right_ear: $main_consulta_div.find("#right_ear_value").val(),
            produtos: produtos,
            feedback: "",
            terceira_pessoa: $("#ca_s").is(":checked") ? {tipo: $("[name='tp']").val(), nome: $("#3_pessoa_input").val()} : [],
            closed: 0
        }, function () {
            $.jGrowl('Consulta gravada', {life: 3000});
            $.msg('unblock');
        }, "json").fail(function () {
            $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
            $.msg('unblock', 5000);
        });


    });

    $main_consulta_div.find("#terminar_consulta_no_exame").click(function () {
        consult_status = "no_exam";
        $main_consulta_div
            .find("#validate_audio_script_save_div").hide().end()
            .find("#validate_audio_script_div").hide().end()
            .find("#actions_exame").hide().end()
            .find("#terminar_consulta_div").show().end()
            .find("#script_main_div").hide().end()
            .find("#audiograma_main_div").hide().end()
            .find("#first_info_div").show().end()
            .find("#inicial_option_div").hide().end()
            .find("#no_exam_div").show().end()
            .find("#exam_outcome_div").hide().end();
    });
    $(".onlynumber").autotab("numeric");

    $("#button_create_proposta_comercial").click(function (e) {
        e.preventDefault();
        $("#2_proposta").hide();
        $("#3_proposta").hide();
        $("#add_proposta_linha").show();
        $(".select_modelo_proposta").val("").trigger("chosen:updated");
        $("#proposta_form").get(0).reset();
    });

    $(".delete_proposta_line").click(function(e)
    {
        e.preventDefault();
        $(this).parents("tr").hide();
        $("#add_proposta_linha").show();
    })

    $("#save_proposta").click(function () {
        if ($("#proposta_form").validationEngine("validate")) {
            $.msg();
            var modal = $("#proposta_modal");
            var proposta = [];
            proposta.push({
                modelo: modal.find("#p1modelo").val(),
                valor: modal.find("#p1valor").val(),
                quantidade: modal.find("#p1qt").val(),
                entrada: modal.find("#p1entrada").val(),
                meses: modal.find("#p1meses").val()
            });
            if ($("#2_proposta").is(":visible"))
                proposta.push({
                    modelo: modal.find("#p2modelo").val(),
                    valor: modal.find("#p2valor").val(),
                    quantidade: modal.find("#p2qt").val(),
                    entrada: modal.find("#p2entrada").val(),
                    meses: modal.find("#p2meses").val()
                });
            if ($("#3_proposta").is(":visible"))
                proposta.push({
                    modelo: modal.find("#p3modelo").val(),
                    valor: modal.find("#p3valor").val(),
                    quantidade: modal.find("#p3qt").val(),
                    entrada: modal.find("#p3entrada").val(),
                    meses: modal.find("#p3meses").val()
                });
            $.msg();
            $.post("ajax/users.php", {action: "save_proposta", reserva_id: reserva_id, lead_id: lead_id, proposta: proposta}, function () {

                modal.modal("hide");
                $.msg('replace', 'Proposta gravada com sucesso!');
                $("#button_create_proposta_comercial").hide();
                $.msg('unblock', 1500);
            }, "json").fail(function () {
                $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                $.msg('unblock', 5000);
            });
        }
    });

    $("#add_proposta_linha").click(function (e) {
        e.preventDefault();
        if (!$("#2_proposta").is(":visible"))
            $("#2_proposta").show();
        else {
            $("#3_proposta").show();

        }
    })
    $("#remove_proposta_linha").click(function (e) {
        e.preventDefault();
        if ($("#3_proposta").is(":visible"))
            $("#3_proposta").hide();
        else {
            $("#2_proposta").hide();

        }
    })


})