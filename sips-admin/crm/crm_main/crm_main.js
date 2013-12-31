var crm_main = function(crm_main_zone, file_path)
{
    var select = [];
    var crm_edit_object;

    this.init = function()
    {
        $.get(file_path + "crm_main/crm_main.html", function(data) {
            crm_main_zone.append(data);
            select["campanha"] = crm_main_zone.find("#select_campanha");
            select["bd"] = crm_main_zone.find("#select_bd");
            select["agente"] = crm_main_zone.find("#select_agente");
            select["feedback"] = crm_main_zone.find("#select_feedback");
            select["cd"] = crm_main_zone.find("#select_cd");
            select["script"] = crm_main_zone.find("#select_script");
            crm_main_zone.find(".chosen-select").chosen({no_results_text: "Sem resultados"});
            crm_main_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
            get_campanha(function()
            {
                get_bd(function() {
                    get_agente(function() {
                        get_feedback(function() {
                            get_campos_dinamicos(function() {
                                get_script(function() {
                                    search();
                                });
                            });
                        });
                    });
                });
            });
        });
    };

    this.destroy = function()
    {
        crm_main_zone.empty().off();
    };
    function get_campanha(callback)
    {
        select["campanha"].empty();
        $.post(file_path + "crm_main/crm_main_request.php", {action: "get_campanha"},
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
    crm_main_zone.on("change", "#select_campanha", function()
    {
        get_bd(get_feedback(get_campos_dinamicos(get_script())));
    });
    function get_bd(callback)
    {
        select["bd"].empty();
        $.post(file_path + "crm_main/crm_main_request.php", {action: "get_bd", campaign_id: crm_main_zone.find("#select_campanha option:selected").val()},
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
        $.post(file_path + "crm_main/crm_main_request.php", {action: "get_agent"},
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
        $.post(file_path + "crm_main/crm_main_request.php", {action: "get_feedbacks", campaign_id: crm_main_zone.find("#select_campanha option:selected").val()},
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
        crm_main_zone.find(".cd_input_field").hide();
        $.post(file_path + "crm_main/crm_main_request.php", {action: "get_campos_dinamicos", campaign_id: crm_main_zone.find("#select_campanha option:selected").val()},
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

    crm_main_zone.on("change", "#select_cd", function()
    {
        if (select["cd"].find("option:selected").val() == "1")
        {
            crm_main_zone.find(".cd_input_field").hide();
        }
        else
        {
            crm_main_zone.find(".cd_input_field").show();
        }
    });
    crm_main_zone.on("click", "#button_tag_cd", function() {
        crm_main_zone.find("#cd_tag_div")
                .append($("<span>")
                        .data("info", {name: crm_main_zone.find("#select_cd option:selected").val(), value: crm_main_zone.find("#cd_input").val()})
                        .addClass("label-info label close_tag")
                        .text(crm_main_zone.find("#select_cd option:selected")
                                .text() + "->" + crm_main_zone.find("#cd_input").val())
                        .append(crm_main_zone.find("<button>", {class: 'btn-link'}).text("x")));
        crm_main_zone.find('#select_cd').val("").trigger('chosen:updated').trigger('change');
    });
    function get_script(callback)
    {

        crm_main_zone.find(".script_input_field").hide();
        select["script"].empty();
        $.post(file_path + "crm_main/crm_main_request.php", {action: "get_script", campaign_id: crm_main_zone.find("#select_campanha option:selected").val()},
        function(data)
        {
            var temp = "<option value='1'>Selecione um Campo do Script</option>";
            $.each(data, function()
            {
                if (this.type != "legend" && this.type != "textfield" && this.type != "datepicker" && this.type != "scheduler" && this.type != "ipl" && this.type != "pagination" && this.type != "tableinput")
                    temp += "<option data-type=" + this.type + " data-tag=" + this.tag + " value=" + this.id + ">" + get_script_translated_names(this.type) + "&#10142;" + this.name + "</option>";
            });
            select["script"].append(temp).trigger("chosen:updated");
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    crm_main_zone.on("change", "#select_script", function()
    {
        if (select["script"].find("option:selected").val() == "1")
            crm_main_zone.find(".script_input_field").hide();
        else
        {
            crm_main_zone.find(".script_input_field").hide();
            crm_main_zone.find("#button_tag_script").show();
            switch (select["script"].find("option:selected").data("type"))
            {
                case "textarea":
                case "texto":
                    crm_main_zone.find("#script_input_text").show();
                    break;
                case "multichoice":
                case "checkbox":
                case "radio":
                    crm_main_zone.find("#span_script_input_select").show();

                    $.post(file_path + "crm_main/crm_main_request.php", {action: "get_script_individual", id: crm_main_zone.find("#select_script option:selected").val()},
                    function(data)
                    {
                        crm_main_zone.find("#script_input_select").empty();
                        var dados = data.values_text;
                        var options = "";
                        
                        $.each(dados, function() {
                            options += "<option value='" + this + "'>" + this + "</option>";
                        });
                        crm_main_zone.find("#script_input_select").append(options);
                        crm_main_zone.find('#script_input_select').val("").trigger('chosen:updated');
                    }
                    , "json");
                    break;
                case "tableradio":
                    crm_main_zone.find("#span_script_input_select").show();
                    crm_main_zone.find("#script_input_select").empty();
                    $.post(file_path + "crm_main/crm_main_request.php", {action: "get_script_individual", id: crm_main_zone.find("#select_script option:selected").val()},
                    function(data)
                    {
                        var dados = data.values_text;
                        var titulos = data.placeholder;
                        var options = "";
                        $.each(dados, function(index1, value1) {
                            $.each(titulos, function(index2, value2) {
                                options += "<option value='" + dados[index1] + ";" + titulos[index2] + "'>" + dados[index1] + "&#10142;" + titulos[index2] + "</option>";
                            });
                        });
                        crm_main_zone.find("#script_input_select").append(options);
                        crm_main_zone.find('#script_input_select').val("").trigger('chosen:updated');
                    }
                    , "json");
                    break;
            }
        }
    });
    crm_main_zone.on("click", "#button_tag_script", function() {
        if (crm_main_zone.find("#script_input_text").is(":visible"))
            crm_main_zone.find("#script_tag_div")
                    .append($("<span>")
                            .data("info", {name: crm_main_zone.find("#select_script option:selected").data("tag"), value: crm_main_zone.find("#script_input_text").val()})
                            .addClass("label-info label close_tag")
                            .text(get_script_translated_names(crm_main_zone.find("#select_script option:selected").data("type")) + "->" + crm_main_zone.find("#script_input_text").val())
                            .append($("<button>", {class: 'btn-link'}).text("x")));
        else
            crm_main_zone.find("#script_tag_div")
                    .append($("<span>")
                            .data("info", {name: crm_main_zone.find("#select_script option:selected").data("tag"), value: crm_main_zone.find("#script_input_select option:selected").val()})
                            .addClass("label-info label close_tag")
                            .text(get_script_translated_names(crm_main_zone.find("#select_script option:selected").data("type")) + "->" + crm_main_zone.find("#script_input_select option:selected").val())
                            .append($("<button>", {class: 'btn-link'}).text("x")));
        crm_main_zone.find('#select_script').val("").trigger('chosen:updated').trigger('change');
    });
//-----------------------------------------------------------------------SEARCH
    crm_main_zone.on("click", "#search_button", function()
    {
        if (crm_main_zone.find("#date_form").validationEngine("validate"))
        {
            crm_main_zone.find("#button_filtro").toggleClass("icon-chevron-up").toggleClass("icon-chevron-down");
            crm_main_zone.find("#div_filtro_content").hide(500);
            crm_main_zone.find("#date_form").validationEngine("hideAll");
            search();
        }
    });
//-------------------------------------------------------TOGGLE DAS DATAS pelo CHECKBOX
    crm_main_zone.on("click", "#checkbox_alldate", function()
    {
        crm_main_zone.find(".form_datetime").toggle(200);
        crm_main_zone.find(".form_datetime").val("");
    });
//-----------------------------------------------------------------TOGGLE FILTROS
    crm_main_zone.on("click", "#button_filtro", function()
    {
        crm_main_zone.find("#div_filtro_content").toggle(500);
        $(this).toggleClass("icon-chevron-up");
        $(this).toggleClass("icon-chevron-down");
    });
    function search()
    {

        var tags_cd = [];
        var tags_script = [];
        $.each(crm_main_zone.find("#cd_tag_div span"), function()
        {
            tags_cd.push($(this).data("info"));
        });
        $.each(crm_main_zone.find("#script_tag_div span"), function()
        {
            tags_script.push($(this).data("info"));
        });
        var oTable = crm_main_zone.find('#info_table').dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": file_path + 'crm_main/crm_main_request.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_info"},
                {"name": "data_inicio", "value": crm_main_zone.find("#data_inicio").val()},
                {"name": "data_fim", "value": crm_main_zone.find("#data_fim").val()},
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
    $(crm_main_zone).on("click", ".close_tag", function()
    {
        $(this).remove();
    });
//----------------------------------------------------------------ABRIR CRM EDIT

    $(crm_main_zone).on("click", ".ver_cliente", function()
    {
        crm_edit_object = new crm_edit(crm_main_zone.find("#crm_edit_modal .modal-body"), "/sips-admin/crm/", $(this).data("lead_id"));
        crm_edit_object.init();

        crm_main_zone.find('#crm_edit_modal').modal('show');
    });
    crm_main_zone.on("click", "#close_modal", function()
    {
        crm_edit_object.destroy();
    });
};