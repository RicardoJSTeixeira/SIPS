var script_id = 0;

var current_template = 0;
var campaign = "";
var base_dados = [];
$(function() {
    $("#report_bd").hide();
    $("#report_linha_inbound").hide();
    $(".chzn-select").chosen({no_results_text: "Sem resultados"});
    $("#form_filter").validationEngine();
    $("#column_items_form").validationEngine();
    $("#template_form").validationEngine();

    $("#datetime_from").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).on('changeDate', function(ev) {
        $("#datetime_to").datetimepicker('setStartDate', moment($(this).val()).format('YYYY-MM-DD'));
        $("#datetime_to").datetimepicker('setEndDate', moment($(this).val()).add('months', 1).format('YYYY-MM-DD'));
    });
    $("#datetime_to").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).on('changeDate', function(ev) {
        $("#datetime_from").datetimepicker('setEndDate', moment($(this).val()).format('YYYY-MM-DD'));
        $("#datetime_from").datetimepicker('setStartDate', moment($(this).val()).subtract('months', 1).format('YYYY-MM-DD'));
    });

    $("#datetime_to").change(function()
    {
        if (!$(this).val().length)
            $("#datetime_from").datetimepicker('setStartDate', null).datetimepicker('setEndDate', null);
    });
    $("#datetime_from").change(function()
    {
        if (!$(this).val().length)
            $("#datetime_to").datetimepicker('setStartDate', null).datetimepicker('setEndDate', null);
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
            if ($("#select_base_dados optgroup[label='" + this.campaign_name + "']").length)
            {
                $("#select_base_dados optgroup[label='" + this.campaign_name + "']").append("<option data-campaign_id=" + this.campaign_id + " value=" + this.id + " > " + (this.active == "Y" ? "Activa" : "Inactiva") + " - " + this.name + " </option>");
            }
            else
            {
                $("#select_base_dados").append("<optgroup class='tag_group' data-campaign_id='" + this.campaign_id + "' label='" + this.campaign_name + "'></optgroup>");
                $("#select_base_dados optgroup[label='" + this.campaign_name + "']").append("<option data-campaign_id=" + this.campaign_id + " value=" + this.id + " >  " + (this.active == "Y" ? "Activa" : "Inactiva") + " - " + this.name + "</option>");
            }
        });

        $("#select_base_dados").val("").trigger("liszt:updated");
        update_template();



        $("#select_campanha").change(function()
        {
            update_template();
        });

        $("#select_base_dados").change(function()
        {
            update_template();
            if ($("#select_base_dados :selected").length == 0)
            {
                $("#select_base_dados option").prop("disabled", false);
                $("#select_base_dados").trigger("liszt:updated");
            }
            else
            {
                $("#select_base_dados option").prop("disabled", true);
                $("#select_base_dados optgroup[label='" + $("#select_base_dados :selected").parent().attr("label") + "'] option").prop("disabled", false).trigger("liszt:updated");
            }
        });


        $(".radio_opcao").on("click", function()
        {

            update_template();
            $("#select_base_dados option").prop("disabled", false);
            $("#select_base_dados").val("").trigger("liszt:updated");

            $(".select_opcao").hide();
            if ($(this).val() == "1")
            {
                $("#report_campanha").show();
            }
            else if ($(this).val() == "2") {
                $("#report_linha_inbound").show();
            } else
            {
                $("#report_bd").show();
            }
        });

        $(".radio_dados").on("click", function()
        {
            $(".time_div").show();
            $("#radio1,#radio3").prop("disabled", false);
                  
            if ($(this).val() == 4)
            {
                $("#radio3").prop("checked", true);
                $(".radio_opcao").prop("disabled", true);
                $(".select_opcao").hide();
                $("#report_bd").show();
                $(".time_div").hide();
            }
            
            update_template();
        });




    }, "json");
    $("#column_order").sortable({
        stop: function(event, ui) {
            update_elements();
        }});
});









function update_template()
{
    campaign = "";


    if ($("#radio1").is(":checked")) {
        campaign = $("#select_campanha option:selected").val();
        get_templates(campaign);
    }
    if ($("#radio2").is(":checked")) {
        campaign = $("#select_linha_inbound option:selected").val();
        get_templates(campaign);
    }
    if ($("#radio3").is(":checked"))
    {
        campaign = ($("#select_base_dados option:selected").data("campaign_id"));
        get_templates(campaign);
    }




}

function get_templates(campaign)
{


    $.post("requests.php", {action: "check_has_script", campaign_id: campaign},
    function(data)
    {
        if (Object.size(data))
        {
            script_id = data[0];
            $("#span_script_name").text("Nome do Script ->  " + data[1]);
            $("#download_report").prop("disabled", false);
            $("#edit_template_div_opener_button").prop("disabled", false);
            $("#delete_template_button").prop("disabled", false);
            $("#template_div").show();
            $("#oc_template").empty();
            $.post("requests.php", {action: "get_template", campaign_id: campaign},
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
                    $("#column_order_title").show().text("Ordernação de colunas");
                }
                else
                {
                    $("#column_order").empty();
                    $("#edit_template_div_opener_button").prop("disabled", true);
                    $("#delete_template_button").prop("disabled", true);
                    $("#oc_template").append("<option value=''>Crie um template</option>");
                    $("#column_order_title").hide();
                }
            }, "json");
        }
        else
        {

            $("#span_script_name").text("");
            $("#download_report").prop("disabled", true);
            $("#column_order").empty();
            $("#column_order").append("<li>Sem Script</li>");
            $("#template_div").hide();
        }
    }, "json");


}





function get_elements_by_template(template)
{
    $.post("requests.php", {action: "get_elements_by_template", id: template}, function(data)
    {
        $("#column_order").empty();
        if (Object.size(data))
        {
            $("#download_report").prop("disabled", false);
            $("#column_order_title").text("Ordernação de colunas");
            $.each(data, function()
            {
                $("#column_order").append("<li class='ui-state-default'><input id=" + this.id + "  class='validate[required]'   type='text'  data-param_1='" + this.param_1 + "'  data-type='" + this.type + "' value='" + this.texto + "'>" + ((this.type === "campo_dinamico") ? "" : "<span class='label'>" + this.id + "</span>" + get_name_by_type(this.type)) + "<span class='btn icon-alone remove_list_item_button icon-remove btn-link' data-id='" + this.id + "'></span></li>");
            });
        }
        else
        {
            $("#column_order_title").text("Houve uma alteração nos campos do script, apague esta template e crie outra para actualizar os campos.");
            $("#download_report").prop("disabled", true);
        }
    }, "json");
}

function update_elements(type)
{
    var elements = new Array();
    var items = $("#column_order  li input");
    $.each(items, function()
    {
        elements.push({"id": this.id, "type": this.getAttribute("data-type"), "texto": this.value, "param_1": this.getAttribute("data-param_1")});
    });


    $.post("requests.php", {action: "update_elements_order", elements: elements, id: $("#oc_template option:selected").val()}, function()
    {
        if (type == "delete")
        {
            get_elements_by_template($("#oc_template option:selected").val());
        }
    }, "json");
}


$("#oc_template").on("change", function()
{
    get_elements_by_template($("#oc_template option:selected").val());
});

$("#download_report").on("click", function(e)
{

    e.preventDefault();
    if ($("#form_filter").validationEngine('validate') && $("#template_form").validationEngine('validate') && $("#column_items_form").validationEngine('validate'))
    {

        //$('#loading').show();
        $("#download_report").prop("disabled", true);
        var ordered_tags = new Array();
        var items = $("#column_order  li input");
        $.each(items, function()
        {
            ordered_tags.push({"id": this.id, "type": this.getAttribute("data-type"), "texto": this.value, "param_1": this.getAttribute("data-param_1")});

        });

        if ($("#radio1").is(":checked"))
        {
            $.post("requests.php", {action: "report_outbound", tipo: 1, data_inicio: $("#datetime_from").val(), data_fim: $("#datetime_to").val(), campaign_id: $("#select_campanha option:selected").val(), field_data: $("#oc_template").val(), result_filter: $(".radio_dados:checked").val()},
            function(data)
            {
                $("#download_report").prop("disabled", false);
                $('#loading').hide();
                document.location.href = "requests.php?action=get_report_file&file=" + data;
            }, "json");
        }
        else if ($("#radio2").is(":checked"))
        {

        }
        else
        {
            $.post("requests.php", {action: "report_outbound", tipo: 3, data_inicio: $("#datetime_from").val(), data_fim: $("#datetime_to").val(), list_id: $("#select_base_dados").val(), campaign_id: $("#select_base_dados option:selected").data("campaign_id"), field_data: $("#oc_template").val(), result_filter: $(".radio_dados:checked").val()},
            function(data)
            {
                $("#download_report").prop("disabled", false);
                $('#loading').hide();
                document.location.href = "requests.php?action=get_report_file&file=" + data;
            }, "json");
        }


    }
});





Object.size = function(a)
{
    var count = 0;
    var i;
    for (i in a) {
        if (a.hasOwnProperty(i)) {
            count++;
        }
    }
    return count;
};





$("#new_template_div_opener_button").on("click", function(e)
{
    e.preventDefault();
    $("#new_template_div").toggle(500);
    $("#new_template_input").val("");
});

$("#new_template_button").on("click", function()
{
    if ($("#new_template_input").val() != "")
    {
        $.post("requests.php", {action: "create_template", campaign_id: campaign, template: $("#new_template_input").val(), script_id: script_id}, "json");
        $("#new_template_div").toggle(500);

        get_templates(campaign);
    }
    else
        $("#new_template_input").attr("placeholder", "Escreva o nome da template antes de criar");
});


$("#edit_template_div_opener_button").on("click", function(e)
{
    e.preventDefault();
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


$("#delete_template_button").on("click", function(e)
{
    e.preventDefault();
    $.post("requests.php", {action: "delete_template", id: $("#oc_template option:selected").val()}, function()
    {
        $("#new_template_div").hide();
        get_templates(campaign);
    }, "json");
});

$("#main_div").off("change", ".remove_list_item_button");
$("#main_div").on("click", ".remove_list_item_button", function()
{
    $(this).closest("li")[0].remove();
    update_elements("delete");
});


$("#main_div").off("change", "#column_order li input");
$("#main_div").on("change", "#column_order li input", function()
{
    update_elements("edit");
});




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
