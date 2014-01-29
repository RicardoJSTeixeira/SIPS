var ha_perda = 1;
var lead_id = 0;
var reserva_id = 0;
var right_ear = 0;
var left_ear = 0;
var script;
var consult_audiogra = undefined;
$(function()
{

    lead_id = $("#calendar_client_modal").data().lead_id;
    reserva_id = $("#calendar_client_modal").data().id;

    $.get("view/script.html", function(data) {
        $("#main_consulta_div #script_placeholder").append(data);
        script = new render($("#main_consulta_div #render_zone"), "/sips-admin/script_dinamico/", 159, lead_id, 0, undefined, undefined, 0, 0);
        var config = new Object();
        config.save_overwrite = true;
        script.init(config);
    });


    $.get("view/audiograma/audiograma.html", function(data) {
        $('#main_consulta_div #audiograma_placeholder').html(data);
        consult_audiogra = new audiograma(lead_id);
    });
    $.post("ajax/consulta.php", {action: "get_client_info", lead_id: lead_id},
    function(data)
    {
        $("#main_consulta_div #table_client_info_body").append($("<tr>")
                .append($("<td>").text(data.nome))
                .append($("<td>").text(data.morada))
                .append($("<td>").text(data.data_nascimento))
                );
    }, "json");
});


//EXAME
$("#main_consulta_div input[name='ra']").change(function()
{
    //SE HA EXAME, PREENCHER AUDIOGRAMA
    if ($(this).val() == "yes")
    {
        $("#main_consulta_div #yes_exam_div").show();
    }
    else//SE NÃO HÁ EXAME PREENCHE RAZÕES
    {
        $("#main_consulta_div #no_exam_div").show();
        $("#main_consulta_div #script_placeholder").hide();
    }
    $("#main_consulta_div #inicial_option_div").hide();
    $("#main_consulta_div #terminar_consulta").prop("disabled", false);
});



//VENDA
$("#main_consulta_div input[name='vpa']").change(function()
{

    if ($(this).val() == "yes")
    {
        $("#main_consulta_div #venda_div").show();
        $("#main_consulta_div #no_venda_div").hide();
    }
    else
    {
        $("#main_consulta_div #venda_div").hide();
        $("#main_consulta_div #no_venda_div").show();
    }

});



$("#main_consulta_div #calcul_audiograma").click(function()
{
    var status = consult_audiogra.calculate(lead_id);
    if (status == "0") // SEM PERDA
    {

        ha_perda = 0;
    }
    else //COM PERDA
    {
        $("#main_consulta_div #venda_confirm_div").show();
        right_ear = $("#main_consulta_div #right_ear_value").val();
        left_ear = $("#main_consulta_div #left_ear_value").val();
    }
});



$("#main_consulta_div #validate_script").on("click", function()
{
    script.validate_manual();
});

$("#main_consulta_div #terminar_consulta").on("click", function()
{

    if ($("#main_consulta_div #pa_no").is(":checked"))//NÂO HA EXAME
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
                    if (consult_audiogra.validate())
                    {
                        script.submit_manual();

                        var status = consult_audiogra.calculate(function() {
                            consult_audiogra.save(lead_id);
                        });
                        if (status == "0") // SEM PERDA
                        {
                            ha_perda = 0;
                        }
                        else //COM PERDA
                        {
                            right_ear = $("#main_consulta_div #right_ear_value").val();
                            left_ear = $("#main_consulta_div #left_ear_value").val();
                        }
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
                    }
                }, false);


    }
}); 