var select = [];

var crm_edit_object;
$(function()
{


    select["campanha"] = $("#select_campanha");
    select["bd"] = $("#select_bd");
    select["agente"] = $("#select_agente");
    select["feedback"] = $("#select_feedback");
    select["cd"] = $("#select_cd");
    select["script"] = $("#select_script");
    $(".chosen-select").chosen({no_results_text: "Sem resultados"});
    $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});

    get_campanha(get_bd(get_agente(get_feedback(get_campos_dinamicos(get_script(search()))))));

});


function get_campanha(callback)
{
    select["campanha"].empty();
    $.post("ajax/crm_main.php", {action: "campanha"},
    function(data)
    {
        var temp = "";
        $.each(data, function()
        {
            temp += "<option value=" + this.id + ">" + this.name + "</option>";
        });
        select["campanha"].append(temp).trigger("chosen:updated");

        if (typeof callback === "function")
        {
            callback();
        }
    }, "json");


}
$("#select_campanha").on("change", function()
{
    get_bd(get_feedback(get_campos_dinamicos(get_script())));
});

function get_bd(callback)
{
    select["bd"].empty();

    $.post("ajax/crm_main.php", {action: "bd", campaign_id: $("#select_campanha option:selected").val()},
    function(data)
    {
        var temp = "<option value=''>Selecione uma Base Dados</option>";
        $.each(data, function()
        {
            temp += "<option value=" + this.id + ">" + this.name + "</option>";
        });
        select["bd"].append(temp).trigger("chosen:updated");

        if (typeof callback === "function")
        {
            callback();
        }
    }, "json");
}
function get_agente(callback)
{
    select["agente"].empty();
    $.post("ajax/crm_main.php", {action: "agent"},
    function(data)
    {

        var temp = "<option value=''>Selecione um Agente</option>";
        $.each(data, function()
        {
            temp += "<option value=" + this.id + ">" + this.name + "</option>";
        });
        select["agente"].append(temp).trigger("chosen:updated");

        if (typeof callback === "function")
        {
            callback();
        }
    }, "json");
}
function get_feedback(callback)
{
    select["feedback"].empty();
    $.post("ajax/crm_main.php", {action: "feedbacks", campaign_id: $("#select_campanha option:selected").val()},
    function(data)
    {
        var temp = "<option value=''>Selecione um feedback</option>";
        $.each(data, function()
        {
            temp += "<option value=" + this.id + ">" + this.name + "</option>";
        });
        select["feedback"].append(temp).trigger("chosen:updated");

        if (typeof callback === "function")
        {
            callback();
        }
    }, "json");
}

function get_campos_dinamicos(callback)
{
    select["cd"].empty();
    $(".cd_input_field").hide();
    $.post("ajax/crm_main.php", {action: "campos_dinamicos", campaign_id: $("#select_campanha option:selected").val()},
    function(data)
    {
        var temp = "<option value='1'>Selecione um Campo Dinâmico</option>";
        $.each(data, function()
        {
            temp += "<option value=" + this.id + ">" + this.name + "</option>";
        });
        select["cd"].append(temp).trigger("chosen:updated");

        if (typeof callback === "function")
        {
            callback();
        }
    }, "json");
}

$("#select_cd").on("change", function()
{
    if (select["cd"].find("option:selected").val() == "1")
    {
        $(".cd_input_field").hide();

    }
    else
    {
        $(".cd_input_field").show();
    }

});

$("#button_tag_cd").on("click", function() {
    $("#cd_tag_div")
            .append($("<span>")
                    .data("info", {name: $("#select_cd option:selected").val(), value: $("#cd_input").val()})
                    .addClass("label-info label close_tag")
                    .text($("#select_cd option:selected")
                            .text() + "->" + $("#cd_input").val())
                    .append($("<button>", {class: 'btn-link'}).text("x")));
    $('#select_cd').val("").trigger('chosen:updated').trigger('change');
});

function get_script(callback)
{
    $(".script_input_field").hide();
    select["script"].empty();
    $.post("ajax/crm_main.php", {action: "script", campaign_id: $("#select_campanha option:selected").val()},
    function(data)
    {
        var temp = "<option value='1'>Selecione um Campo do Script</option>";
        $.each(data, function()
        {
            temp += "<option data-type=" + this.type + " data-tag=" + this.tag + " value=" + this.id + ">" + get_script_translated_names(this.type) + "->" + this.name + "</option>";
        });
        select["script"].append(temp).trigger("chosen:updated");

        if (typeof callback === "function")
        {
            callback();
        }

    }, "json");
}

$("#select_script").on("change", function()
{

    if (select["script"].find("option:selected").val() == "1")
        $(".script_input_field").hide();
    else
    {
        $(".script_input_field").hide();
        $("#button_tag_script").show();


        switch (select["script"].find("option:selected").data("type"))
        {
            case "textarea":
            case "texto":
                $("#script_input_text").show();
                break;
            case "multichoice":
            case "checkbox":
            case "radio":
                $("#span_script_input_select").show();
                $("#script_input_select").empty();
                $.post("ajax/crm_main.php", {action: "get_script_individual", id: $("#select_script option:selected").val()},
                function(data)
                {
                    var dados = data.values_text;
                    var options = "";
                    $.each(dados, function() {
                        options += "<option value='" + this + "'>" + this + "</option>";
                    });
                    $("#script_input_select").append(options);

                    $('#script_input_select').val("").trigger('chosen:updated');
                }
                , "json");
            case "tableradio":
                $("#span_script_input_select").show();
                $("#script_input_select").empty();
                $.post("ajax/crm_main.php", {action: "get_script_individual", id: $("#select_script option:selected").val()},
                function(data)
                {

                    var dados = data.values_text;

                    var titulos = data.placeholder;
                    var options = "";
                    $.each(dados, function(index1, value1) {
                        $.each(titulos, function(index2, value2) {
                            options += "<option value='" + dados[index1] + ";" + titulos[index2] + "'>" + dados[index1] + "---" + titulos[index2] + "</option>";
                        });
                    });
                    $("#script_input_select").append(options);


                    $('#script_input_select').val("").trigger('chosen:updated');
                }
                , "json");
                break;
        }
    }
});

$("#button_tag_script").on("click", function() {

    if ($("#script_input_text").is(":visible"))
        $("#script_tag_div")
                .append($("<span>")
                        .data("info", {name: $("#select_script option:selected").data("tag"), value: $("#script_input_text").val()})
                        .addClass("label-info label close_tag")
                        .text(get_script_translated_names($("#select_script option:selected").data("type")) + "->" + $("#script_input_text").val())
                        .append($("<button>", {class: 'btn-link'}).text("x")));
    else
        $("#script_tag_div")
                .append($("<span>")
                        .data("info", {name: $("#select_script option:selected").data("tag"), value: $("#script_input_select option:selected").val()})
                        .addClass("label-info label close_tag")
                        .text(get_script_translated_names($("#select_script option:selected").data("type")) + "->" + $("#script_input_select option:selected").val())
                        .append($("<button>", {class: 'btn-link'}).text("x")));




    $('#select_script').val("").trigger('chosen:updated').trigger('change');
});


//-----------------------------------------------------------------------SEARCH
$("#search_button").on("click", function()
{
    if ($("#date_form").validationEngine("validate"))
    {
        search();
    }
});


//-------------------------------------------------------TOGGLE DAS DATAS pelo CHECKBOX
$("#checkbox_alldate").on("click", function()
{
    $(".form_datetime").toggle(200);
    $(".form_datetime").val("");
});



//-----------------------------------------------------------------TOGGLE FILTROS
$("#button_filtro").on("click", function()
{
    $("#div_filtro_content").toggle(500);
    $(this).toggleClass("icon-chevron-up");
    $(this).toggleClass("icon-chevron-down");
});


function search()
{
    var tags_cd = [];
    var tags_script = [];



    $.each($("#cd_tag_div span"), function()
    {

        tags_cd.push($(this).data("info"));
    });

    $.each($("#script_tag_div span"), function()
    {
        tags_script.push($(this).data("info"));
    });

    var oTable = $('#info_table').dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/crm_main.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "get_info"},
            {"name": "data_inicio", "value": $("#data_inicio").val()},
            {"name": "data_fim", "value": $("#data_fim").val()},
            {"name": "campanha", "value": select["campanha"].find("option:selected").val()},
            {"name": "bd", "value": select["bd"].find("option:selected").val()},
            {"name": "agente", "value": select["agente"].find("option:selected").val()},
            {"name": "feedback", "value": select["feedback"].find("option:selected").val()},
            {"name": "cd", "value": JSON.stringify(tags_cd)},
            {"name": "script", "value": JSON.stringify(tags_script)}
            );
        },
        "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Nome"}, {"sTitle": "Telefone"}, {"sTitle": "Morada"}, {"sTitle": "Ultima Chamada"}],
        "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
    });
}


function get_script_translated_names(type)
{
    var temp_type = "";
    switch (type)
    {
        case "texto":
            temp_type = "Caixa Texto";
            break;
        case "pagination":
            temp_type = "Paginação";
            break;
        case "radio":
            temp_type = "Botão Radio";
            break;
        case "checkbox":
            temp_type = "Botão Resposta Múltipla";
            break;
        case "multichoice":
            temp_type = "Lista de Opções";
            break;
        case "textfield":
            temp_type = "Campo Texto";
            break;
        case "legend":
            temp_type = "Titulo";
            break;
        case "tableradio":
            temp_type = "Tabela Botões Radio";
            break;
        case "datepicker":
            temp_type = "Seletor Tempo Hora";
            break;
        case "scheduler":
            temp_type = "Calendário";
            break;
        case "textarea":
            temp_type = "Input de Texto";
            break;
        case "ipl":
            temp_type = "Imagem/PDF/Link";
            break;
    }
    return temp_type;
}


//-----------------------------------------------------------------Fechar TAGS
$(document).off("click", ".close_tag");
$(document).on("click", ".close_tag", function()
{
    $(this).remove();

});


//----------------------------------------------------------------ABRIR CRM EDIT
$(document).off("click", ".ver_cliente");
$(document).on("click", ".ver_cliente", function()
{
    crm_edit_object = new crm_edit($("#crm_edit_modal .modal-body"), "/sips-admin/crm/crm_edit/", $(this).data("lead_id"));
    crm_edit_object.init();
    $('#crm_edit_modal').modal('show');
});

$("#close_modal").on("click", function()
{
    crm_edit_object.destroy();
});
