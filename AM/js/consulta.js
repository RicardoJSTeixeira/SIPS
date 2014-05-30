var
        ha_perda = 1,
        lead_id = 0,
        reserva_id = 0,
        right_ear = 0,
        left_ear = 0,
        script,
        consult_audiogra = undefined,
        consult_status = "",
        consult_closed = false;
var client_box;
$(function()
{
    rse = getUrlVars();
    lead_id = atob(decodeURIComponent(rse.id));
    reserva_id = atob(decodeURIComponent(rse.rs));

    $("#dModelo, #dGama, #eModelo, #eGama").chosen({no_results_text: "Sem resultados", width: "100%"});
    $('[data-toggle~="tooltip"]').tooltip({container: 'body'});

    $.post("ajax/consulta.php", {action: "get_consulta", reserva_id: reserva_id},
    function(data) {
        if (data) {
            if (data.terceira_pessoa.tipo) {
                $("#ca_s").prop("checked", true);
                $("#3_pessoa_div").show().find("input");
                $("[name='tp'][value='" + data.terceira_pessoa.tipo + "']").prop(":checked", true);
                $("#3_pessoa_input").val(data.terceira_pessoa.nome);
            }
            else {
                $("#ca_n").prop("checked", true);
            }

            $("#main_consulta_div")
                    .find("#options_div")
                    .toggle(!data.closed)
                    .end()
                    .find("#exit_save")
                    .show()
                    .end()
                    .find("#terminar_consulta_div")
                    .show()
                    .end()
                    .find("#validate_audio_script_save_div")
                    .show()
                    .end()
                    .find("#audiograma_main_div")
                    .show()
                    .end()
                    .find("#script_main_div")
                    .show()
                    .end()
                    .find("#first_info_div")
                    .hide()
                    .end()
                    .find("#exam_outcome_div")
                    .find("input")
                    .prop("disabled", data.closed)
                    .end()
                    .find("select")
                    .prop("disabled", data.closed)
                    .end()
                    .find("[name=ne]")
                    .val([data.exame_razao])
                    .end();

            if (data.produtos) {
                $("#main_consulta_div")
                        .find("#dGama")
                        .val(data.produtos.direito.gama)
                        .end()
                        .find("#eGama")
                        .val(data.produtos.esquerdo.gama)
                        .end()
                        .find("#dMarca")
                        .val(data.produtos.direito.marca)
                        .end()
                        .find("#eMarca")
                        .val(data.produtos.esquerdo.marca)
                        .end()
                        .find("#dModelo")
                        .val(data.produtos.direito.modelo)
                        .end()
                        .find("#eModelo")
                        .val(data.produtos.esquerdo.modelo)
                        .end()
                        .find("[name=tp_vd]")
                        .val([data.produtos.tipo]);
            }

            $("#main_consulta_div #exam_outcome_div").show();
            if (data.exame) {
                if (data.venda)
                    $("#main_consulta_div #venda_yes").prop("checked", true).change();
                else
                {
                    $("#main_consulta_div")
                            .find("#venda_no").prop("checked", true).change().end()
                            .find("#no_venda_select").val(data.venda_razao).end()
                            .find("#no_venda_div").show();
                }
            } else {
                if (data.closed) {
                    $("#main_consulta_div")
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

        script = new render($("#main_consulta_div #script_placeholder"), "/sips-admin/script_dinamico/", undefined, lead_id, reserva_id, SpiceU.username, SpiceU.camp, 0, 0);

        var config = {save_overwrite: true, input_disabled: consult_closed};
        script.init(config);

        $('#main_consulta_div #audiograma_placeholder').load("view/audiograma.html", function() {
            consult_audiogra = new audiograma(lead_id);
            if (consult_closed) {
                $("#main_consulta_div").find("#audiograma_main_div")
                        .find(":input").prop("disabled", true).end()
                        .find("button").prop("disabled", true).end().end()
                        .find("#script_main_div")
                        .find(":input").prop("disabled", true).end()
                        .find("button").prop("disabled", true).end().end()
                        .find("#3_pessoa_master_div")
                        .find(":input").prop("disabled", true);
            }
        });

        client_box = new clientBox({id: reserva_id, byReserv: true});
        client_box.init();

    }, "json");
});

$("#main_consulta_div [name='ca']").change(function() {
    $("#3_pessoa_div").toggle(~~$(this).val());
});

//EXAME
$("#main_consulta_div #pa_no").click(function() {
    consult_status = "no_exam";
    $("#main_consulta_div").find("#options_div").show();
    $("#main_consulta_div")
            .find("#no_exam_div").show().end()
            .find("#terminar_consulta_div").show().end()
            .find("#inicial_option_div").hide().end()
            .find("#terminar_consulta_no_exame").hide();
});

$("#main_consulta_div #pa_yes").click(function() {
    consult_status = "yes_exam";
    $("#main_consulta_div").find("#options_div").show();
    $("#main_consulta_div")
            .find("#audiograma_main_div").show().end()
            .find("#script_main_div").show().end()
            .find("#first_info_div").hide().end()
            .find("#terminar_consulta_no_exame").show().end();
    $("#main_consulta_div #validate_audio_script_div").show();
    $("#main_consulta_div #validate_audio_script_save_div").show();
    $("#main_consulta_div").find("#first_info_div")
            .hide();
});

//VENDA
$("#main_consulta_div input[name='vp_a']").change(function() {
    result = $(this).val() === "yes";
    $("#main_consulta_div")
            .find("#no_venda_div")
            .toggle(!result)
            .end()
            .find("#yes_venda_div")
            .toggle(result);
});

$("#main_consulta_div #validate_audio_script").on("click", function() {
    var that = $(this).parent();
    consult_audiogra.validate(function() {
        script.validate_manual(function() {
            that.hide();
            $('html, body').animate({scrollTop: $(document).height()}, 'fast');
            var status = consult_audiogra.calculate();
            $("#main_consulta_div #exam_outcome_div").show();

            if (status !== "0") { //COM PERDA
                right_ear = $("#main_consulta_div #right_ear_value").val();
                left_ear = $("#main_consulta_div #left_ear_value").val();
                $("#main_consulta_div #venda_confirm_div");
                $("#main_consulta_div #terminar_consulta_div").show();
                ha_perda = 1;
            }
            else if (status === "0") { // SEM PERDA 
                $("#main_consulta_div #terminar_consulta_div").show();
                ha_perda = 0;
            }
        });
    }, false);
});

$("#new_request_button").click(function()
{
    var en = btoa(lead_id);
    $.history.push("view/new_requisition.html?id=" + en);
});

$(".new_marcacao_button").click(function()
{
    var en = btoa(lead_id);
    $.history.push("view/calendar.html?id=" + en);
});

//OPTIONS DIV--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$("#main_consulta_div #terminar_consulta").on("click", function() {


    if (consult_closed) {
        $.history.push('view/dashboard.html');
    }
    else
    {
        var produtos = {
            direito: {marca: $("#main_consulta_div #dMarca").val(), gama: $("#main_consulta_div #dGama").val(), modelo: $("#main_consulta_div #dModelo").val()},
            esquerdo: {marca: $("#main_consulta_div #eMarca").val(), gama: $("#main_consulta_div #eGama").val(), modelo: $("#main_consulta_div #eModelo").val()},
            tipo: $("#main_consulta_div input[name=tp_vd]:checked").val()
        };

        if (consult_status === "no_exam")//NÂO HA EXAME
        {
            if ($("#main_consulta_div #no_exam_div input[name='ne']:checked").length)
            {
                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "0", exame_razao: $("#main_consulta_div #no_exam_div input[name='ne']:checked").val(), venda: 0, venda_razao: "", left_ear: 0, right_ear: 0, tipo_aparelho: "", descricao_aparelho: "", produtos: produtos, feedback: "STEST", terceira_pessoa: $("#ca_s").is(":checked") ? {tipo: $("[name='tp']").val(), nome: $("#3_pessoa_input").val()} : [], closed: 1}, function() {
                    $.jGrowl('Consulta gravada sem exame', {life: 3000});
                    $("#marcacao_modal").modal("show");
                }, "json");
            }
        }
        else//HA EXAME
        {
            if ($("#ca_s").is(":checked")) {
                if (!$("#3_pessoa_input").val().length)
                {
                    $.jGrowl("Certifique-se que preenche correctamente a caixa de 3ª pessoa no topo", {life: 4000});
                    return false;
                }
            }
            script.validate_manual(
                    function() {
                        consult_audiogra.validate(function() {
                            script.submit_manual();
                            consult_audiogra.save(lead_id, reserva_id, false);

                            if (ha_perda)// HA PERDA
                            {
                                if (!$("[name=vp_a]:checked").length) {
                                    $.jGrowl('Indique se há venda.', {life: 3000});
                                    return false;
                                }
                                //HA VENDA
                                if ($("#main_consulta_div #venda_yes").is(":checked"))
                                {
                                    $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 1, venda_razao: "", left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), produtos: produtos, feedback: "TV", terceira_pessoa: $("#ca_s").is(":checked") ? {tipo: $("[name='tp']").val(), nome: $("#3_pessoa_input").val()} : [], closed: 1}, function() {
                                        $.jGrowl('Consulta gravada com venda', {life: 3000});
                                        $("#encomenda_modal").modal("show");
                                    }, "json");
                                }
                                else//NÂO HA VENDA
                                {
                                    $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: $("#main_consulta_div #no_venda_select option:selected").val(), left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), produtos: produtos, feedback: "TNV", terceira_pessoa: $("#ca_s").is(":checked") ? {tipo: $("[name='tp']").val(), nome: $("#3_pessoa_input").val()} : [], closed: 1}, function() {
                                        $.jGrowl('Consulta gravada sem venda', {life: 3000});
                                        $("#marcacao_modal").modal("show");
                                    }, "json");
                                }
                            }
                            else//NAO HA PERDA
                            {
                                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: "", left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), produtos: produtos, feedback: "SPERD", terceira_pessoa: $("#ca_s").is(":checked") ? {tipo: $("[name='tp']").val(), nome: $("#3_pessoa_input").val()} : [], closed: 1}, function() {
                                    $.jGrowl('Consulta gravada sem perda', {life: 3000});
                                    $("#marcacao_modal").modal("show");
                                }, "json");
                            }
                        }, false);
                    }, false);
        }

    }
});
$("#main_consulta_div #exit_save").click(function() {

    var produtos = {
        direito: {marca: $("#main_consulta_div #dMarca").val(), gama: $("#main_consulta_div #dGama").val(), modelo: $("#main_consulta_div #dModelo").val()},
        esquerdo: {marca: $("#main_consulta_div #eMarca").val(), gama: $("#main_consulta_div #eGama").val(), modelo: $("#main_consulta_div #eModelo").val()},
        tipo: $("#main_consulta_div input[name=tp_vd]:checked").val()
    };

    script.submit_manual();
    consult_audiogra.save(lead_id, reserva_id, false);
    $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: ~~$("#main_consulta_div #venda_yes").is(":checked"), venda_razao: $("#main_consulta_div #no_venda_select option:selected").val(), left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), produtos: produtos, feedback: "", terceira_pessoa: $("#ca_s").is(":checked") ? {tipo: $("[name='tp']").val(), nome: $("#3_pessoa_input").val()} : [], closed: 0}, function() {
        $.jGrowl('Consulta gravada', {life: 3000});
    });

});

$("#main_consulta_div #terminar_consulta_no_exame").click(function() {
    consult_status = "no_exam";
    $("#main_consulta_div")
            .find("#validate_audio_script_save_div").hide().end()
            .find("#validate_audio_script_div").hide().end()
            .find("#terminar_consulta_no_exame").hide().end()
            .find("#terminar_consulta_div").show().end()
            .find("#script_main_div").hide().end()
            .find("#audiograma_main_div").hide().end()
            .find("#first_info_div").show().end()
            .find("#inicial_option_div").hide().end()
            .find("#no_exam_div").show().end()
            .find("#exam_outcome_div").hide().end();
});

//AUDIOGRAMA VALIDATE AND SAVE
$("#main_consulta_div #validate_audiograma_button").click(function() {
    consult_audiogra.validate(function()
    {
        consult_audiogra.calculate();
    }, function()
    {
        $.jGrowl('AudioGrama validado com sucesso!', {life: 3000});
    });
});

$(".onlynumber").autotab("numeric");

$("#print_proposta").click(function() {

    if ($("#proposta_form").validationEngine("validate"))
    {
        var
                modal = $("#proposta_modal"),
                p1 = {modelo: modal.find("#p1modelo").val(), valor: modal.find("#p1valor").val(), quantidade: modal.find("#p1qt").val(), entrada: modal.find("#p1entrada").val(), meses: modal.find("#p1meses").val()},
        p2 = {modelo: modal.find("#p2modelo").val(), valor: modal.find("#p2valor").val(), quantidade: modal.find("#p2qt").val(), entrada: modal.find("#p2entrada").val(), meses: modal.find("#p2meses").val()},
        p3 = {modelo: modal.find("#p3modelo").val(), valor: modal.find("#p3valor").val(), quantidade: modal.find("#p3qt").val(), entrada: modal.find("#p3entrada").val(), meses: modal.find("#p3meses").val()},
        doc = new jsPDF('p', 'pt', 'a4', true),
                y = new function() {
                    var me = this;
                    this.y = 0;
                    this.pos = function(mais) {
                        if (mais) {
                            me.y += mais;
                        }
                        me.y += 16;
                        return me.y;
                    };
                },
                image = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/4QBoRXhpZgAASUkqAAgAAAADABIBAwABAAAAAQAAADEBAgAQAAAAMgAAAGmHBAABAAAAQgAAAAAAAABTaG90d2VsbCAwLjE4LjAAAgACoAkAAQAAAJQBAAADoAkAAQAAAIMAAAAAAAAA/+EJ9Gh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNC40LjAtRXhpdjIiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyIgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iIGV4aWY6UGl4ZWxYRGltZW5zaW9uPSI0MDQiIGV4aWY6UGl4ZWxZRGltZW5zaW9uPSIxMzEiIHRpZmY6SW1hZ2VXaWR0aD0iNDA0IiB0aWZmOkltYWdlSGVpZ2h0PSIxMzEiIHRpZmY6T3JpZW50YXRpb249IjEiLz4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3hwYWNrZXQgZW5kPSJ3Ij8+/9sAQwADAgIDAgIDAwMDBAMDBAUIBQUE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";

        doc.addImage(image, 'JPEG', 25, 25, 231, 75);
        doc.setFontSize(13);
        doc.text(20, y.pos(135), 'Refª Cliente: ' + client_box.get_info().refClient);
        doc.setFontSize(12);
        doc.text(20, y.pos(10), 'Exmo(a) Senhor(a) ' + client_box.get_info().name + ',');

        doc.text(20, y.pos(), 'Vimos apresentar a nossa melhor proposta para a solução');
        doc.text(20, y.pos(), 'correctiva adequada à sua perda auditiva:');
        doc.setFontSize(14);
        doc.text(20, y.pos(10), '- Proposta 1:');
        doc.text(165, y.y, p1.modelo);
        doc.setFontSize(10);
        doc.text(110, y.pos(15), p1.valor + " €");
        doc.text(200 - (p1.quantidade.length * 7), y.y, p1.quantidade + ' Prótese(s) digital(ais) de última geração');
        doc.text(200 - (p1.entrada.length * 7) - 10, y.pos(20), p1.entrada + '% Entrada ');
        doc.text(285, y.y, (p1.valor * (p1.entrada / 100)).toFixed(2) + '€');
        doc.text(200 - (p1.meses.length * 7), y.pos(), p1.meses + ' Prestações ');
        doc.text(285, y.y, (p1.valor - (p1.valor * (p1.entrada / 100)) / p1.meses).toFixed(2) + '€');
        if (p2.valor) {
            doc.setFontSize(14);
            doc.text(20, y.pos(10), '- Proposta 2:');
            doc.text(165, y.y, p2.modelo);
            doc.setFontSize(10);
            doc.text(110, y.pos(15), p2.valor + " €");
            doc.text(200 - (p2.quantidade.length * 7), y.y, p2.quantidade + ' Prótese(s) digital(ais) de última geração');
            doc.text(200 - (p2.entrada.length * 7) - 10, y.pos(20), p2.entrada + '% Entrada ');
            doc.text(285, y.y, (p2.valor * (p2.entrada / 100)).toFixed(2) + '€');
            doc.text(200 - (p2.meses.length * 7), y.pos(), p2.meses + ' Prestações ');
            doc.text(285, y.y, (p2.valor - (p2.valor * (p2.entrada / 100)) / p2.meses).toFixed(2) + '€');
        }
        if (p3.valor) {
            doc.setFontSize(14);
            doc.text(20, y.pos(10), '- Proposta 3:');
            doc.text(165, y.y, p3.modelo);
            doc.setFontSize(10);
            doc.text(110, y.pos(15), p3.valor + " €");
            doc.text(200 - (p3.quantidade.length * 7), y.y, p3.quantidade + ' Prótese(s) digital(ais) de última geração');
            doc.text(200 - (p3.entrada.length * 7) - 10, y.pos(20), p3.entrada + '% Entrada ');
            doc.text(285, y.y, (p3.valor * (p3.entrada / 100)).toFixed(2) + '€');
            doc.text(200 - (p3.meses.length * 7), y.pos(), p3.meses + ' Prestações ');
            doc.text(285, y.y, (p3.valor - (p3.valor * (p3.entrada / 100)) / p3.meses).toFixed(2) + '€');
        }
        //last = doc.table(5, 20, EData.bInfo, ['Dispenser', 'Tipo', 'Id Cliente', 'Data', 'Nr de contrato', 'Referencia', 'Estado'], {autoSize: true, printHeaders: true});

        doc.setFontSize(11);
        doc.text(20, y.pos(20), 'A presente proposta tem validade durante o mês corrente,');
        doc.text(30, y.pos(), 'devendo fora deste prazo ser confirmada com o nosso audioprotesista:');
        doc.text(40, y.pos(10), 'Ludgero Lopes - 912 741 803');

        doc.text(20, y.pos(20), 'No dia da colocação deve fazer-se acompanhar dos seguintes documentos:');
        doc.text(40, y.pos(), '- Bilhete de identidade');
        doc.text(40, y.pos(), '- Número de Contribuinte');

        doc.text(20, y.pos(20), 'No caso de aquisição com pagamento a prestações c/ADC:');
        doc.text(40, y.pos(), '- Comprovativo de IBAN');
        doc.text(40, y.pos(), '- BIC/SWIFT (com nomes dos titulares)');
        doc.text(40, y.pos(), '- Documento de Identificação de quem assina ADC');
        doc.save(moment().format());
    }
});