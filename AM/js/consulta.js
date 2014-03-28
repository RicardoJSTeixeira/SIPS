var
        ha_perda = 1,
        lead_id = 0,
        reserva_id = 0,
        right_ear = 0,
        left_ear = 0,
        script,
        consult_audiogra = undefined,
        consult_status = "";

$(function()
{
    rse = getUrlVars();
    lead_id = atob(decodeURIComponent(rse.id));
    reserva_id = atob(decodeURIComponent(rse.rs));

    script = new render($("#main_consulta_div #script_placeholder"), "/sips-admin/script_dinamico/", 159, lead_id, 0, undefined, undefined, 0, 0);
    var config = {save_overwrite: true};
    script.init(config);

    $('#main_consulta_div #audiograma_placeholder').load("view/audiograma/audiograma.html", function() {
        consult_audiogra = new audiograma(lead_id);
    });

    $.post("ajax/consulta.php", {action: "get_client_info", lead_id: lead_id},
    function(data)
    {
        $("#main_consulta_div #table_client_info_body").append(
                $("<tr>")
                .append($("<td>").text(data.nome))
                .append($("<td>").text(data.morada))
                .append($("<td>").text(data.data_nascimento))
                );
    }, "json");
});

//EXAME
$("#main_consulta_div #pa_no").click(function()
{
    consult_status = "no_exam";
    //SE NÃO HÁ EXAME PREENCHE RAZÕES
    $("#main_consulta_div")
            .find("#no_exam_div")
            .show()
            .end()
            .find("#script_placeholder")
            .hide()
            .end()
            .find("#inicial_option_div")
            .hide()
            .end()
            .find("#terminar_consulta")
            .removeClass("hidden");
});

$("#main_consulta_div #pa_yes").click(function()
{
    consult_status = "yes_exam";
    $("#main_consulta_div")
            .find("#yes_exam_div")
            .show()
            .end()
            .find("#inicial_option_div")
            .hide()
            .end()
            .find("#terminar_consulta_no_exame")
            .removeClass("hidden");
    $("#main_consulta_div #validate_audio_script").removeClass("hidden");
});

$("#main_consulta_div #terminar_consulta_no_exame").click(function()
{
    $("#main_consulta_div")
            .find("#no_exam_div")
            .show()
            .end()
            .find("#yes_exam_div").hide();
    $(this).prop("disabled", true);
});
//VENDA
$("#main_consulta_div input[name='vp_a']").change(function()
{
    if ($(this).val() === "yes")
    {
        $("#main_consulta_div #venda_div").show();
        var config = new Object();
        config.mensal = false;
        requisition1 = new requisition($("#main_requisition_div"), config);
        requisition1.init();
        requisition1.new_requisition_destroy($("#venda_div"));
        requisition1.new_requisition($("#venda_div"), false, lead_id);

        $("#main_consulta_div #no_venda_div").hide();
    }
    else
    {
        $("#main_consulta_div #venda_div").hide();
        $("#main_consulta_div #no_venda_div").show();
    }
});

$("#main_consulta_div #validate_audio_script").on("click", function()
{
    script.validate_manual(function()
    {
        consult_audiogra.validate(function() {
            var status = consult_audiogra.calculate();

            if (status !== "0") //COM PERDA
            {
                $("#main_consulta_div #venda_confirm_div").show();
                right_ear = $("#main_consulta_div #right_ear_value").val();
                left_ear = $("#main_consulta_div #left_ear_value").val();
                $("#main_consulta_div #venda_yes").prop("checked", true).trigger("change");
                $("#main_consulta_div #terminar_consulta").removeClass("hidden");
                $("#main_consulta_div #terminar_consulta_no_exame").addClass("hidden");
                ha_perda = 1;
            }
            else if (status === "0") // SEM PERDA 
            {
                $("#main_consulta_div #venda_confirm_div").show();
                $("#main_consulta_div #venda_no").prop("checked", true).trigger("change");
                $("#main_consulta_div #terminar_consulta").removeClass("hidden");
                $("#main_consulta_div #terminar_consulta_no_exame").addClass("hidden");
                ha_perda = 0;
            }
        });
    }, false);
});

$("#main_consulta_div #terminar_consulta").on("click", function()
{
    if (consult_status === "no_exam")//NÂO HA EXAME
    {
        var exame_razao = new Array();
        $.each($("#main_consulta_div #no_exam_div input[type='checkbox'][name='ne']:checked"), function()
        {
            exame_razao.push($(this).val());
        });
        $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "0", exame_razao: exame_razao, venda: 0, venda_razao: "", left_ear: 0, right_ear: 0, tipo_aparelho: "", descricao_aparelho: "", feedback: ""}, function() {
            $.jGrowl('Consulta gravada sem exame', {life: 3000});
            $("#div_consult").hide();
            $("#div_master").show();

        }, "json");
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
                                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 1, venda_razao: "", left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), tipo_aparelho: $("#main_consulta_div #venda_div input[type='radio'][name='v_ta']:checked").val(), descricao_aparelho: $("#main_consulta_div #dma option:selected").text(), feedback: temp_feedback}, function() {
                                    $.jGrowl('Consulta gravada com venda', {life: 3000});
                                    $("#div_consult").hide();
                                    $("#div_master").show();

                                }, "json");
                            }
                            else//NÂO HA VENDA
                            {
                                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: $("#main_consulta_div #no_venda_select option:selected").text(), left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), tipo_aparelho: "", descricao_aparelho: "", feedback: temp_feedback}, function() {
                                    $.jGrowl('Consulta gravada sem venda', {life: 3000});
                                    $("#div_consult").hide();
                                    $("#div_master").show();

                                }, "json");
                            }


                        }
                        else//NAO HA PERDA
                        {
                            $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: "", left_ear: $("#main_consulta_div #left_ear_value").val(), right_ear: $("#main_consulta_div #right_ear_value").val(), tipo_aparelho: "", descricao_aparelho: "", feedback: "Sem perda"}, function() {
                                $.jGrowl('Consulta gravada sem perda', {life: 3000});
                                $("#div_consult").hide();
                                $("#div_master").show();

                            }, "json");
                        }

                    }, false);

                }, false);


    }
}); 