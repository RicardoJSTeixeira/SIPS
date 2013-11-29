var script_id = 0;
var campaign = "";
var current_template = 0;
$(function() {
    $("#report_bd").hide();
    $("#report_linha_inbound").hide();
    $(".chzn-select").chosen({no_results_text: "Sem resultados"});

    $("#form_filter").validationEngine();
    $(".datetime_range").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).keypress(function(e) {
        e.preventDefault();
    }).bind("cut copy paste", function(e) {
        e.preventDefault();
    });


    $.post("requests.php", {action: "get_select_options"},
    function(data)
    {
        var temp = "";
        $("#select_campanha").empty();
        $.each(data.campanha, function()
        {
            temp = temp + "<option value=" + this.id + " > " + this.name + "</option>";
        });
        $("#select_campanha").append(temp);
        temp = "";
        $("#select_campanha").val("").trigger("liszt:updated");
        $("#select_campanha").trigger("change");

        $("#select_linha_inbound").empty();
        $.each(data.linha_inbound, function()
        {
            temp = temp + "<option value=" + this.id + " > " + this.name + "</option>";
        });
        $("#select_linha_inbound").append(temp);
        temp = "";
        $("#select_linha_inbound").val("").trigger("liszt:updated");

        $("#select_base_dados").empty();
        $.each(data.bd, function()
        {
            temp = temp + "<option data-campaign_id=" + this.campaign_id + " value=" + this.id + " > " + this.name + "</option>";
        });
        $("#select_base_dados").append(temp);
        $("#select_base_dados").val("").trigger("liszt:updated");

    }, "json");


    $("#column_order").sortable({
        stop: function(event, ui) {
            update_elements();
        }});
});






$("#download_report").on("click", function(e)
{
    e.preventDefault();
    if ($("#form_filter").validationEngine('validate'))
    {

        if ($("#radio1").is(":checked"))
            campaign = $("#select_campanha option:selected").val();
        if ($("#radio3").is(":checked"))
            campaign = $("#select_base_dados option:selected").data("campaign_id");

        $("#co_modal").data("campaign", campaign);
        get_templates(campaign);
        $("#co_modal").modal("show");
        $("#crs_contacts").prop("checked", true);

    }

});




$("#download_report_button").on("click", function()
{


    var ordered_tags = new Array();
    var items = $("#column_order  li input");
    $.each(items, function()
    {

        ordered_tags.push({"id": this.id, "type": this.getAttribute("data-type"), "texto": this.value});

    });


    if ($("#radio1").is(":checked"))
    {
        document.location.href = "requests.php?action=report&tipo=1&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&campaign_id=" + $("#select_campanha option:selected").val() + "&allctc=" + $("#allcontacts").is(":checked") + "&field_data=" + JSON.stringify(ordered_tags) + "&only_with_result=" + $("#crs_contacts").is(":checked");
    }
    else if ($("#radio2").is(":checked"))
    {
        document.location.href = "requests.php?action=report&tipo=2&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&campaign_id=" + $("#select_linha_inbound option:selected").val() + "&allctc=" + $("#allcontacts").is(":checked") + "&only_with_result=" + $("#crs_contacts").is(":checked");
    }
    else
    {
        document.location.href = "requests.php?action=report&tipo=3&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&list_id=" + $("#select_base_dados option:selected").val() + "&campaign_id=" + $("#select_base_dados option:selected").data("campaign_id") + "&allctc=" + $("#allcontacts").is(":checked") + "&field_data=" + JSON.stringify(ordered_tags) + "&only_with_result=" + $("#crs_contacts").is(":checked");
    }

});




$("#allcontacts").on("click", function()
{
    $(".time_div").toggle(500);
});

$(".radio_opcao").on("click", function()
{
    $(".select_opcao").hide();
    if ($(this).val() == "1")
    {
        $("#report_campanha").show();
    }
    else if ($(this).val() == "2")
        $("#report_linha_inbound").show();
    else
    {
        $("#report_bd").show();
    }
});


function update_elements(type)
{
    var elements = new Array();
    var items = $("#column_order  li input");
    $.each(items, function()
    {
        elements.push({"id": this.id, "type": this.getAttribute("data-type"), "texto": this.value});
    });


    $.post("requests.php", {action: "update_elements_order", elements: elements, id: $("#oc_template option:selected").val()}, function()
    {
        if (type == "delete")
        {
            get_elements_by_template($("#oc_template option:selected").val());
        }
    }, "json");
}





//MODAL-----------------------------------------------------------------------------------


function get_templates(campaign)
{

    $.post("requests.php", {action: "check_has_script", campaign_id: campaign},
    function(data)
    {
        if (data.length)
        {
            script_id = data;
            $("#download_report_button").prop("disabled", false);
            $("#edit_template_div_opener_button").prop("disabled", false);
            $("#delete_template_button").prop("disabled", false);
            $("#template_div").show();
            $("#oc_template").empty();
            $("#div_crs").show();
            $.post("requests.php", {action: "get_template", campaign_id: campaign, script_id: data},
            function(data1)
            {
                if (data1.length)
                {
                    $.each(data1, function()
                    {
                        if (this.id == current_template)
                            $("#oc_template").append("<option value=" + this.id + " selected>" + this.template + "</option>");
                        else
                            $("#oc_template").append("<option value=" + this.id + ">" + this.template + "</option>");

                    });
                    $("#oc_template").trigger("change");
                }
                else
                {
                    $("#column_order").empty();
                    $("#download_report_button").prop("disabled", true);
                    $("#edit_template_div_opener_button").prop("disabled", true);
                    $("#delete_template_button").prop("disabled", true);
                    $("#oc_template").append("<option>Crie um template</option>");
                }
            }, "json");
        }
        else
        {
            $("#column_order").empty();
            $("#column_order").append("<li>Sem Script</li>");
            $("#template_div").hide();
            $("#div_crs").hide();

            $("#download_report_button").prop("disabled", true);

        }
    }, "json");
}

function get_elements_by_template(template)
{
    $.post("requests.php", {action: "get_elements_by_template", id: template}, function(data)
    {
        $("#column_order").empty();
        $.each(data, function()
        {
            $("#column_order").append("<li class='ui-state-default'><input id=" + this.id + "    type='text'   data-type='" + this.type + "' value='" + this.texto + "'>" + ((this.type === "campo_dinamico") ? "" : "<span class='label'>" + this.id + "</span>" + get_name_by_type(this.type)) + "<span class='btn icon-alone remove_list_item_button icon-remove btn-link' data-id='" + this.id + "'></span></li>");
        });

    }, "json");
}

$("#oc_template").on("change", function()
{
    get_elements_by_template($("#oc_template option:selected").val());

});

$("#new_template_div_opener_button").on("click", function()
{
    $("#new_template_div").toggle(500);
    $("#new_template_input").val("");
});

$("#new_template_button").on("click", function()
{
    if ($("#new_template_input").val() != "")
    {
        $.post("requests.php", {action: "create_template", campaign_id: $("#co_modal").data("campaign"), template: $("#new_template_input").val(), script_id: script_id}, "json");
        $("#new_template_div").toggle(500);
        get_templates($("#co_modal").data("campaign"));
    }
    else
        $("#new_template_input").attr("placeholder", "Escreva o nome da template antes de criar");
    ;
});


$("#edit_template_div_opener_button").on("click", function()
{
    $("#edit_template_div").toggle(500);
    $("#edit_template_input").val($("#oc_template option:selected").text());
});

$("#edit_template_button").on("click", function()
{

    $.post("requests.php", {action: "edit_template", id: $("#oc_template option:selected").val(), template: $("#edit_template_input").val()}, function()
    {
        current_template = $("#oc_template option:selected").val();
        $("#edit_template_div").toggle(500);

        get_templates(campaign);
    }, "json");


});


$("#delete_template_button").on("click", function()
{
    $.post("requests.php", {action: "delete_template", id: $("#oc_template option:selected").val()}, function()
    {
        get_templates(campaign);
    }, "json");
});

$(document).off("click", ".remove_list_item_button");
$(document).on("click", ".remove_list_item_button", function()
{
    $(this).closest("li")[0].remove();
    update_elements("delete");

});


$(document).off("change", "#column_order li input");
$(document).on("change", "#column_order li input", function()
{

    update_elements("edit");

});

//MODAL-----------------------------------------------------------------------------------



function get_name_by_type(type)
{
    switch (type)
    {
        case "texto":
            return "Caixa de texto";
            break;
        case "pagination":
            return "Paginação";
            break;
        case "radio":
            return "Botão radio";
            break;
        case "checkbox":
            return "Botão resposta multipla";
            break;
        case "multichoice":
            return "Lista de Opções";
            break;
        case "textfield":
            return "Campo de Texto";
            break;
        case "legend":
            return "Titulo";
            break;
        case "tableradio":
            return "Tabela botões radio";
            break;
        case "datepicker":
            return "Seletor tempo e hora";
            break;
        case "scheduler":
            return "Calendário";
            break;
        case "textarea":
            return "Input de texto";
            break;
        case "ipl":
            return  "Imagem/PDF/Link";
            break;
    }
}
