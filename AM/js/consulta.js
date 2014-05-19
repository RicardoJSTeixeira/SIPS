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
$(function()
{
    rse = getUrlVars();
    lead_id = atob(decodeURIComponent(rse.id));
    reserva_id = atob(decodeURIComponent(rse.rs));
    
    $("#dModelo, #dGama, #eModelo, #eGama").chosen({no_results_text: "Sem resultados", width: "100%"});
    $('[data-toggle~="tooltip"]').tooltip({container: 'body'});
    
    $.post("ajax/consulta.php", {action: "get_consulta", reserva_id: reserva_id},
    function(data)
    {
        if (data)
        {

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
            if (data.closed)
            {
                consult_closed = true;
            }

        }

        script = new render($("#main_consulta_div #script_placeholder"), "/sips-admin/script_dinamico/", undefined, lead_id, reserva_id, SpiceU.username, SpiceU.camp, 0, 0);

        var config = {save_overwrite: true, input_disabled: consult_closed};
        script.init(config);

        $('#main_consulta_div #audiograma_placeholder').load("view/audiograma.html", function() {
            consult_audiogra = new audiograma(lead_id);
            if (consult_closed)
            {
                $("#main_consulta_div").find("#audiograma_main_div")
                        .find(":input").prop("disabled", true).end()
                        .find("button").prop("disabled", true).end().end()
                        .find("#script_main_div")
                        .find(":input").prop("disabled", true).end()
                        .find("button").prop("disabled", true).end();
            }
        });

        var client_box = new clientBox({id: reserva_id, byReserv: true});
        client_box.init();

    }, "json");
});

//EXAME
$("#main_consulta_div #pa_no").click(function()
{
    consult_status = "no_exam";
    $("#main_consulta_div").find("#options_div").show();
    $("#main_consulta_div")
            .find("#no_exam_div").show().end()
            .find("#terminar_consulta_div").show().end()
            .find("#inicial_option_div").hide().end()
            .find("#terminar_consulta_no_exame").hide();

});

$("#main_consulta_div #pa_yes").click(function()
{
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
$("#main_consulta_div input[name='vp_a']").change(function()
{
    result = $(this).val() === "yes";
    $("#main_consulta_div")
            .find("#no_venda_div")
            .toggle(!result)
            .end()
            .find("#yes_venda_div")
            .toggle(result);
});

$("#main_consulta_div #validate_audio_script").on("click", function()
{
    var that = $(this).parent();
    consult_audiogra.validate(function() {
        script.validate_manual(function()
        {
            that.hide();
            $('html, body').animate({scrollTop: $(document).height()}, 'fast');
            var status = consult_audiogra.calculate();
            $("#main_consulta_div #exam_outcome_div").show();

            if (status !== "0") //COM PERDA
            {
                right_ear = $("#main_consulta_div #right_ear_value").val();
                left_ear = $("#main_consulta_div #left_ear_value").val();
                $("#main_consulta_div #venda_confirm_div");
                $("#main_consulta_div #terminar_consulta_div").show();
                ha_perda = 1;
            }
            else if (status === "0") // SEM PERDA 
            {

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
                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "0", exame_razao: $("#main_consulta_div #no_exam_div input[name='ne']:checked").val(), venda: 0, venda_razao: "", left_ear: 0, right_ear: 0, tipo_aparelho: "", descricao_aparelho: "", produtos: produtos, feedback: "STEST", closed: 1}, function() {
                    $.jGrowl('Consulta gravada sem exame', {life: 3000});
                    $("#marcacao_modal").modal("show");
                }, "json");
            }
        }
        else//HA EXAME
        {
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
                                    console.log(produtos);
                                    $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 1, venda_razao: "", left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), produtos: produtos, feedback: "TV", closed: 1}, function() {
                                        $.jGrowl('Consulta gravada com venda', {life: 3000});
                                        $("#encomenda_modal").modal("show");
                                    }, "json");
                                }
                                else//NÂO HA VENDA
                                {
                                    $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: $("#main_consulta_div #no_venda_select option:selected").val(), left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), produtos: produtos, feedback: "TNV", closed: 1}, function() {
                                        $.jGrowl('Consulta gravada sem venda', {life: 3000});
                                        $("#marcacao_modal").modal("show");
                                    }, "json");
                                }
                            }
                            else//NAO HA PERDA
                            {
                                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: "", left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), produtos: produtos, feedback: "SPERD", closed: 1}, function() {
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
    $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: ~~$("#main_consulta_div #venda_yes").is(":checked"), venda_razao: $("#main_consulta_div #no_venda_select option:selected").val(), left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), produtos: produtos, feedback: "", closed: 0}, function() {
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
                    me.y += 20;
                    return me.y;
                };
            };
    doc.text(20, y.pos(20), 'Exmo(a) Senhor(a),');
    doc.text(20, y.pos(), 'Vimos apresentar a nossa melhor proposta para a solução');
    doc.text(20, y.pos(), 'correctiva adequada à sua perda auditiva:');
    doc.setFontSize(22);
    doc.text(20, y.pos(10), 'Proposta 1');
    doc.setFontSize(16);
    doc.text(20, y.pos(), p1.modelo);
    doc.text(20, y.pos(), p1.valor + "€");
    doc.text(20, y.pos(), p1.quantidade + ' Prótese(s) digital(ais) de última geração');
    doc.text(20, y.pos(), p1.entrada + '% Entrada ' + p1.valor * (p1.entrada / 100) + '€');
    doc.text(20, y.pos(), p1.meses + ' Prestações ' + (p1.valor - (p1.valor * (p1.entrada / 100))) / p1.meses + '€');
    doc.setFontSize(22);
    doc.text(20, y.pos(10), 'Proposta 2');
    doc.setFontSize(16);
    doc.text(20, y.pos(), p2.modelo);
    doc.text(20, y.pos(), p2.valor + "€");
    doc.text(20, y.pos(), p2.quantidade + ' Prótese(s) digital(ais) de última geração');
    doc.text(20, y.pos(), p2.entrada + '% Entrada ' + p2.valor * (p2.entrada / 100) + '€');
    doc.text(20, y.pos(), p2.meses + ' Prestações ' + (p2.valor - (p2.valor  * (p2.entrada / 100))) / p2.meses + '€');
    doc.setFontSize(22);
    doc.text(20, y.pos(10), 'Proposta 3');
    doc.setFontSize(16);
    doc.text(20, y.pos(), p3.modelo);
    doc.text(20, y.pos(), p3.valor + "€");
    doc.text(20, y.pos(), p3.quantidade + ' Prótese(s) digital(ais) de última geração');
    doc.text(20, y.pos(), p3.entrada + '% Entrada ' + p3.valor * (p3.entrada / 100) + '€');
    doc.text(20, y.pos(), p3.meses + ' Prestações ' + (p3.valor - (p3.valor * (p3.entrada / 100))) / p3.meses + '€');
    //last = doc.table(5, 20, EData.bInfo, ['Dispenser', 'Tipo', 'Id Cliente', 'Data', 'Nr de contrato', 'Referencia', 'Estado'], {autoSize: true, printHeaders: true});

    doc.save(moment().format());
});