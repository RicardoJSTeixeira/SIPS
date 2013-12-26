var ha_perda = 1;
var lead_id = 0;
var reserva_id = 0;
var right_ear = 0;
var left_ear = 0;
var script;
$(function()
{




    var consult_audiogra = undefined;
    lead_id = $("#calendar_client").data().lead_id;
    reserva_id = $("#calendar_client").data().id;

    $.get("view/script.html", function(data) {
        $("#script_placeholder").append(data);
        script = new render($("#render_zone"), "/sips-admin/script_dinamico/", 159, lead_id, undefined, undefined, undefined, 0);
        script.init();
    });


    $.get("view/audiograma/audiograma.html", function(data) {
        $('#audiograma_placeholder').html(data);
        consult_audiogra = new audiograma(lead_id);
    });
    $.post("ajax/consulta.php", {action: "get_client_info", lead_id: lead_id},
    function(data)
    {
        $("#table_client_info_body").append($("<tr>")
                .append($("<td>").text(data.nome))
                .append($("<td>").text(data.morada))
                .append($("<td>").text(data.data_nascimento))
                );
    }, "json");
});

//SE HA EXAME, PREENCHER AUDIOGRAMA
$("#pa_yes").on("click", function()
{
    $("#yes_exam_div").show();
    $("#inicial_option_div").hide();
});
//SE NÃO HÁ EXAME PREENCHE RAZÕES
$("#pa_no").on("click", function()
{
    $("#no_exam_div").show();
    $("#inicial_option_div").hide();
    $("#script_placeholder").hide();
});



$(document).off("click", "#calcular_audiograma");
$(document).on("click", "#calcular_audiograma", function()
{

    if ($("#all_ear_value").val() == "0") // SEM PERDA
    {
        $("#all_ear").text("Sem perda em ambos os ouvidos");
        ha_perda = 0;
    }
    else //COM PERDA
    {
        $("#venda_confirm_div").show();
        right_ear = $("#right_ear_value").val();
        left_ear = $("#left_ear_value").val();


    }
});



$("#venda_yes").on("click", function()
{
    $("#venda_div").show();
    $("#no_venda_div").hide();
});
$("#venda_no").on("click", function()
{
    $("#venda_div").hide();
    $("#no_venda_div").show();
});


$("#save_script").on("click", function()
{
    script.validate_manual(script.submit_manual(), false);
});

$("#terminar_consulta").on("click", function()
{
    if ($("#pa_no").is(":checked"))//NÂO HA EXAME
    {
        var exame_razao = new Array();
        $.each($("#no_exam_div input[type='checkbox'][name='ne']:checked"), function()
        {
            exame_razao.push($(this).val());
        });
        $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "0", exame_razao: exame_razao, venda: 0, venda_razao: "", left_ear: 0, right_ear: 0, tipo_aparelho: "", descricao_aparelho: "", feedback: ""}, "json");
    }
    else//HA EXAME
    {
        if (ha_perda)// HA PERDA
        {
            var temp_feedback = "";

            if (parseInt(right_ear) >= parseInt(left_ear))
                temp_feedback = $("#right_ear").text();
            else
                temp_feedback = $("#left_ear").text();


            if ($("#venda_yes").is(":checked"))//HA VENDA
            {
                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 1, venda_razao: "", left_ear: $("#left_ear_value").val(), right_ear: $("#right_ear_value").val(), tipo_aparelho: $("#venda_div input[type='radio'][name='v_ta']:checked").val(), descricao_aparelho: $("#dma option:selected").text(), feedback: temp_feedback}, "json");
            }
            else//NÂO HA VENDA
            {
                $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: $("#no_venda_select option:selected").text(), left_ear: $("#left_ear_value").val(), right_ear: $("#right_ear_value").val(), tipo_aparelho: "", descricao_aparelho: "", feedback: temp_feedback}, "json");
            }


        }
        else//NAO HA PERDA
        {
            $.post("ajax/consulta.php", {action: "insert_consulta", reserva_id: reserva_id, lead_id: lead_id, consulta: 1, consulta_razao: "", exame: "1", exame_razao: "", venda: 0, venda_razao: "", left_ear: $("#left_ear_value").val(), right_ear: $("#right_ear_value").val(), tipo_aparelho: "", descricao_aparelho: "", feedback: "Sem perda"}, "json");
        }
    }





    $("#c_master").show();
    $("#c_consult").hide();
}); 