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



    $.post("ajax/consulta.php", {action: "get_consulta", reserva_id: reserva_id},
    function(data)
    {
        if (data)
        {

            $("#main_consulta_div").find("#options_div").hide();
            $("#main_consulta_div")
                    .find("#audiograma_main_div").show().end()
                    .find("#script_main_div").show().end()
                    .find("#first_info_div").hide().end()
                    .find("#exam_outcome_div").find("input").prop("disabled", true).end()
                    .find("select").prop("disabled", true).end();
            $("#main_consulta_div #exam_outcome_div").show();
            if (data.venda)
                $("#main_consulta_div #venda_yes").prop("checked", true);
            else
            {
                $("#main_consulta_div #venda_no").prop("checked", true);
                $("#main_consulta_div #no_venda_select").val(data.venda_razao);
                $("#main_consulta_div #no_venda_div").show();
            }
            if (data.closed)
            {
                consult_closed = true;
            }

        }

        script = new render($("#main_consulta_div #script_placeholder"), "/sips-admin/script_dinamico/", 159, lead_id, reserva_id, undefined, undefined, 0, 0);

        var config = {save_overwrite: true, input_disabled: consult_closed};
        script.init(config);



        $('#main_consulta_div #audiograma_placeholder').load("view/audiograma/audiograma.html", function() {
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









        $.post("ajax/consulta.php", {action: "get_client_info", lead_id: lead_id},
        function(data)
        {
            $("#main_consulta_div #client_name").text(data.nome);
            $("#main_consulta_div #client_address").text(data.morada);
            $("#main_consulta_div #client_birth_date").text(data.data_nascimento);
        }, "json");



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
            .find("#inicial_option_div").hide();
});

$("#main_consulta_div #pa_yes").click(function()
{
    consult_status = "yes_exam";
    $("#main_consulta_div").find("#options_div").show();
    $("#main_consulta_div")
            .find("#audiograma_main_div").show().end()
            .find("#script_main_div").show().end()
            .find("#first_info_div").hide().end()
            .find("#terminar_consulta_no_exame_div").show().end();
    $("#main_consulta_div #validate_audio_script_div").show();
    $("#main_consulta_div").find("#first_info_div")
            .hide();
});


//VENDA
$("#main_consulta_div input[name='vp_a']").change(function()
{
    if ($(this).val() === "yes")
    {
        $("#main_consulta_div #no_venda_div").hide();
    }
    else
    {
        $("#main_consulta_div #no_venda_div").show();
    }
});

$("#main_consulta_div #validate_audio_script").on("click", function()
{
    consult_audiogra.validate(function() {
        script.validate_manual(function()
        {
            $('html, body').animate({scrollTop: 0}, 'fast');
            var status = consult_audiogra.calculate();
            $("#main_consulta_div #exam_outcome_div").show();

            if (status !== "0") //COM PERDA
            {
                right_ear = $("#main_consulta_div #right_ear_value").val();
                left_ear = $("#main_consulta_div #left_ear_value").val();
                $("#main_consulta_div #venda_confirm_div");
                $("#main_consulta_div #terminar_consulta_div").show();
                $("#main_consulta_div #terminar_consulta_no_exame_div").hide();
                ha_perda = 1;
            }
            else if (status === "0") // SEM PERDA 
            {

                $("#main_consulta_div #terminar_consulta_div").show();
                $("#main_consulta_div #terminar_consulta_no_exame_div").hide();
                ha_perda = 0;
            }
        });
    }, false);
});



$("#new_request_button").click(function()
{
    $.history.push("view/new_requisition.html?lead_id=" + lead_id);
});

$("#new_marcacao_button").click(function()
{
    var en = btoa(lead_id);
    $.history.push("view/calendar.html?id=" + en);
});

//OPTIONS DIV--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$("#main_consulta_div #terminar_consulta").on("click", function()
{

    if (consult_closed)
        $.history.push('view/dashboard.html');

    else
    {


        if (consult_status === "no_exam")//NÂO HA EXAME
        {
            var exame_razao = new Array();
            $.each($("#main_consulta_div #no_exam_div input[type='checkbox'][name='ne']:checked"), function()
            {
                exame_razao.push($(this).val());
            });
            if (exame_razao.length)
            {
                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "0", exame_razao: exame_razao, venda: 0, venda_razao: "", left_ear: 0, right_ear: 0, tipo_aparelho: "", descricao_aparelho: "", feedback: "", closed: 0}, function() {
                    $.jGrowl('Consulta gravada sem exame', {life: 3000});
                    $("#marcacao_modal").modal("show");

                }, "json");
            }
            else
                $.jGrowl('Selecione pelo menos uma razão', {life: 4000});
        }
        else//HA EXAME
        {
            script.validate_manual(
                    function() {
                        consult_audiogra.validate(function() {
                            script.submit_manual();
                            consult_audiogra.save(lead_id, false);
                            if (ha_perda)// HA PERDA
                            {
                                var temp_feedback = "";
                                if (parseInt(right_ear) >= parseInt(left_ear))
                                    temp_feedback = $("#main_consulta_div #right_ear").text();
                                else
                                    temp_feedback = $("#main_consulta_div #left_ear").text();
                                if ($("#main_consulta_div #venda_yes").is(":checked"))//HA VENDA
                                {
                                    $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 1, venda_razao: "", left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), feedback: temp_feedback, closed: 1}, function() {
                                        $.jGrowl('Consulta gravada com venda', {life: 3000});
                                        $("#encomenda_modal").modal("show");
                                    }, "json");
                                }
                                else//NÂO HA VENDA
                                {
                                    $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: $("#main_consulta_div #no_venda_select option:selected").val(), left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), feedback: temp_feedback, closed: 1}, function() {
                                        $.jGrowl('Consulta gravada sem venda', {life: 3000});
                                        $("#marcacao_modal").modal("show");
                                    }, "json");
                                }
                            }
                            else//NAO HA PERDA
                            {
                                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: "", left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), feedback: "Sem perda", closed: 1}, function() {
                                    $.jGrowl('Consulta gravada sem perda', {life: 3000});
                                    $("#marcacao_modal").modal("show");
                                }, "json");
                            }
                        }, false);
                    }, false);
        }
    }
});
$("#main_consulta_div #terminar_consulta_no_exame").click(function()
{
    consult_status = "no_exam";
    $("#main_consulta_div")
            .find("#validate_audio_script_div").hide().end()
            .find("#terminar_consulta_no_exame_div").hide().end()
            .find("#terminar_consulta_div").show().end()
            .find("#script_main_div").hide().end()
            .find("#audiograma_main_div").hide().end()
            .find("#first_info_div").show().end()
            .find("#inicial_option_div").hide().end()
            .find("#no_exam_div").show().end();
});


//SCRIPT VALIDATE AND SAVE
$("#main_consulta_div #validate_script_button").click(function()
{
    script.validate_manual(function()
    {
        $.jGrowl('Script validado com sucesso!', {life: 3000});
    }, false);
});

//AUDIOGRAMA VALIDATE AND SAVE
$("#main_consulta_div #validate_audiograma_button").click(function()
{
    consult_audiogra.validate(function()
    {
        $.jGrowl('AudioGrama validado com sucesso!', {life: 3000});
    }, false);
});

 