var crm_main = function(crm_main_zone, file_path)
{
    var select = [];
    var crm_edit_object;
    var config = [];
//----------------------------------- BASIC FUNCTIONS
    this.init = function(ext_config) {
        config = new Object();
        config.campaign = true;
        config.linha_inbound = true;
        config.bd = true;
        config.dynamic_fields = true;
        config.agent = true;
        config.feedback = true;
        config.script = true;
        config.lead = true;
        config.phone_number = true;
        config.date = true;
        config.marcacao_cliente = false;

        $.extend(true, config, ext_config);

        $.get(file_path + "crm_main/crm_main.html", function(data) {
            crm_main_zone.append(data);
            select["campanha"] = crm_main_zone.find("#select_campanha");
            select["linha_inbound"] = crm_main_zone.find("#select_linha_inbound");
            select["bd"] = crm_main_zone.find("#select_bd");
            select["agente"] = crm_main_zone.find("#select_agente");
            select["feedback"] = crm_main_zone.find("#select_feedback");
            select["cd"] = crm_main_zone.find("#select_cd");
            select["script"] = crm_main_zone.find("#select_script");
            crm_main_zone.find(".chosen-select").chosen({no_results_text: "Sem resultados"});
            crm_main_zone.find("#data_inicio").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).on('changeDate', function(ev) {
                crm_main_zone.find("#data_fim").datetimepicker('setStartDate', $(this).val());
            });
            crm_main_zone.find("#data_fim").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).on('changeDate', function(ev) {
                crm_main_zone.find("#data_inicio").datetimepicker('setEndDate', $(this).val());
            });

            if (!config.date)
                crm_main_zone.find(".form_datetime").parent("div").parent("div").parent("div").hide();
            if (!config.campaign) {
                show_hide_filters(2);
                crm_main_zone.find("#radio_campanha").prop("disabled", true);
            }
            if (!config.linha_inbound) {
                show_hide_filters(1);
                crm_main_zone.find("#radio_linha_inbound").prop("disabled", true);
            }
            get_campanha(function() {
                get_linha_inbound(function() {
                    get_bd(function() {
                        get_agente(function() {
                            get_feedback(function() {
                                get_campos_dinamicos(function() {
                                    get_script(function() {
                                        crm_main_zone.find('#info_table_client').hide();
                                        crm_main_zone.find('#info_table_calls').hide();
                                        show_hide_filters(1);
                                    });
                                });
                            });
                        });
                    });
                });
            });
        },'html');

    };
    this.destroy = function()
    {
        crm_main_zone.empty().off();
    };

//-----------------------------------FILTERS    
    function get_campanha(callback) {

        if (config.campaign) {
            select["campanha"].empty();
            $.post(file_path + "crm_main/crm_main_request.php", {action: "get_campanha"},
            function(data) {
                var temp = "";
                $.each(data, function() {
                    temp += "<option value=" + this.id + ">" + this.name + "</option>";
                });
                select["campanha"].append(temp).trigger("chosen:updated");
                if (typeof callback === "function") {
                    callback();
                }
            }, "json");
        }
        else {
            select["campanha"].parent("div").parent("div").hide();
        }
    }

    function get_linha_inbound(callback) {
        if (config.linha_inbound) {
            select["linha_inbound"].empty();
            $.post(file_path + "crm_main/crm_main_request.php", {action: "get_linha_inbound"},
            function(data) {
                var temp = "";
                $.each(data, function()
                {
                    temp += "<option value=" + this.id + ">" + this.name + "</option>";
                });
                select["linha_inbound"].append(temp).trigger("chosen:updated");
                if (typeof callback === "function")
                {
                    callback();
                }
            }, "json");
        }
        else {
            select["linha_inbound"].parent("div").parent("div").hide();
        }
    }
    
    crm_main_zone.on("change", "#select_campanha", function() {
        get_bd(get_feedback(get_campos_dinamicos(get_script())));
        crm_main_zone.find("#script_tag_div").empty();
        crm_main_zone.find("#cd_tag_div").empty();
    });
    
    function get_bd(callback) {
        if (config.bd) {
            select["bd"].empty();
            $.post(file_path + "crm_main/crm_main_request.php", {action: "get_bd", campaign_id: crm_main_zone.find("#select_campanha option:selected").val()},
            function(data) {
                var temp = "<option value=''>Todas as Base Dados</option>";
                $.each(data, function() {
                    temp += "<option value=" + this.id + ">" + this.name + "</option>";
                });
                select["bd"].append(temp).trigger("chosen:updated");
                if (typeof callback === "function") {
                    callback();
                }
            }, "json");
        }
        else
        {
            select["bd"].parent("div").parent("div").hide();
        }
    }
    
    function get_agente(callback)
    {
        if (config.agent) {
            select["agente"].empty();
            $.post(file_path + "crm_main/crm_main_request.php", {action: "get_agent"},
            function(data) {
                var temp = "<option value=''>Todos os Agentes</option>";
                $.each(data, function() {
                    temp += "<option value='" + this.user + "'>" + this.full_name + "</option>";
                });
                select["agente"].append(temp).trigger("chosen:updated");
                if (typeof callback === "function") {
                    callback();
                }
            }, "json");
        }
        else {
            select["agente"].parent("div").parent("div").hide();
        }
    }
    
    function get_feedback(callback) {
        if (config.feedback) {
            select["feedback"].empty();
            $.post(file_path + "crm_main/crm_main_request.php", {action: "get_feedbacks", campaign_id: crm_main_zone.find("#select_campanha option:selected").val()},
            function(data) {
                var temp = "<option value=''>Todos os feedbacks</option>";
                $.each(data, function() {
                    temp += "<option value='" + this.id + "'>" + this.name + "</option>";
                });
                select["feedback"].append(temp).trigger("chosen:updated");
                if (typeof callback === "function") {
                    callback();
                }
            }, "json");
        }
        else {
            select["feedback"].parent("div").parent("div").hide();
        }
    }
    
    function get_campos_dinamicos(callback) {
        if (config.dynamic_fields) {
            select["cd"].empty();
            crm_main_zone.find(".cd_input_field").hide();
            $.post(file_path + "crm_main/crm_main_request.php", {action: "get_campos_dinamicos", campaign_id: crm_main_zone.find("#select_campanha option:selected").val()},
            function(data) {
                var temp = "<option value='1'>Todos os Campos Dinâmicos</option>";
                $.each(data, function()
                {
                    temp += "<option value='" + this.id + "'>" + this.name + "</option>";
                });
                select["cd"].append(temp).trigger("chosen:updated");
                if (typeof callback === "function")
                {
                    callback();
                }
            }, "json");
        }
        else {
            select["cd"].parent("div").parent("div").hide();
        }
    }
    
    function get_script(callback) {
        if (config.script) {
            crm_main_zone.find(".script_input_field").hide();
            select["script"].empty();
            $.post(file_path + "crm_main/crm_main_request.php", {action: "get_script", campaign_id: crm_main_zone.find("#select_campanha option:selected").val()},
            function(data) {
                var temp = "<option value='1'>Todos os Campos do Script</option>";
                $.each(data, function()
                {
                    if (this.type !== "legend" && this.type !== "textfield" && this.type !== "datepicker" && this.type !== "scheduler" && this.type !== "ipl" && this.type !== "pagination" && this.type !== "tableinput")
                        temp += "<option data-type='" + this.type + "' data-tag='" + this.tag + "' value='" + this.id + "'>" + get_script_translated_names(this.type) + "&#10142;" + this.name + "</option>";
                });
                select["script"].append(temp).trigger("chosen:updated");
                if (typeof callback === "function")
                {
                    callback();
                }
            }, "json");
        }
        else {
            select["script"].parent("div").parent("div").hide();
        }
    }

    crm_main_zone.on("click", "input[name='rft']", function() {
        if (~~$(this).val() === 1) {
            show_hide_filters(1);
        }
        else {
            show_hide_filters(2);
        }

    });

    function show_hide_filters(order) {
        switch (order) {
            //mostra o campaing e esconde a linha de inbound
            case 1:
                crm_main_zone.find("#div_filtro_linha_inbound").hide();
                crm_main_zone.find("#div_filtro_campanha").show();
                crm_main_zone.find("#div_filtro_bd").show();
                crm_main_zone.find("#client_search_sub_option").show();
                crm_main_zone.find("#radio_client").prop("disabled", false);
                break;
                //mostra a linha de inbound  e esconde a campaign 
            case 2:
                crm_main_zone.find("#script_tag_div").empty();
                crm_main_zone.find("#div_filtro_linha_inbound").show();
                crm_main_zone.find("#div_filtro_campanha").hide();
                crm_main_zone.find("#div_filtro_bd").hide();
                crm_main_zone.find("#client_search_sub_option").hide();
                crm_main_zone.find("#radio_chamada").prop("checked", true);
                crm_main_zone.find("#radio_client").prop("disabled", true);
                break;
            case 3:
                break;

        }
    }
//------------------------------------------------------CREATE TAGS
    crm_main_zone.on("change", "#select_cd", function() {
        if (~~select["cd"].find("option:selected").val() === 1) {
            crm_main_zone.find(".cd_input_field").hide();
        }
        else {
            crm_main_zone.find(".cd_input_field").show();
        }
    });

    crm_main_zone.on("click", "#button_tag_cd", function(e) {
        e.preventDefault();
        crm_main_zone.find("#cd_tag_div")
                .append($("<span>").addClass("tooltip_filtro")
                        .data("info", {name: crm_main_zone.find("#select_cd option:selected").val(), value: crm_main_zone.find("#cd_input").val()})

                        .html(crm_main_zone.find("#select_cd option:selected")
                                .text() + " &#10142; " + crm_main_zone.find("#cd_input").val())
                        .append($("<a>", {class: 'btn-link close_tag tooltip_filtro_button_area '}).append($("<i>", {class: "tooltip_filtro_button icon-remove-circle"}))));
        crm_main_zone.find('#select_cd').val("").trigger('chosen:updated').trigger('change');
        crm_main_zone.find("#cd_input").val("");
    });

    crm_main_zone.on("change", "#select_script", function() {
        if (~~select["script"].find("option:selected").val() === 1)
            crm_main_zone.find(".script_input_field").hide();
        else {
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

    crm_main_zone.on("click", "#button_tag_script", function(e) {
        e.preventDefault();

        if (crm_main_zone.find("#script_input_text").is(":visible"))
            crm_main_zone.find("#script_tag_div")
                    .append($("<span>").addClass("tooltip_filtro")
                            .data("info", {name: crm_main_zone.find("#select_script option:selected").data("tag"), value: crm_main_zone.find("#script_input_text").val()})

                            .html(get_script_translated_names(crm_main_zone.find("#select_script option:selected").data("type")) + " &#10142; " + crm_main_zone.find("#script_input_text").val())
                            .append($("<a>", {class: 'btn-link close_tag tooltip_filtro_button_area '}).append($("<i>", {class: "tooltip_filtro_button icon-remove-circle"}))));
        else
            crm_main_zone.find("#script_tag_div")
                    .append($("<span>").addClass("tooltip_filtro")
                            .data("info", {name: crm_main_zone.find("#select_script option:selected").data("tag"), value: crm_main_zone.find("#script_input_select option:selected").val()})

                            .html(get_script_translated_names(crm_main_zone.find("#select_script option:selected").data("type")) + " &#10142; " + crm_main_zone.find("#script_input_select option:selected").val())
                            .append($("<a>", {class: 'btn-link close_tag tooltip_filtro_button_area '}).append($("<i>", {class: "tooltip_filtro_button icon-remove-circle"}))));
        crm_main_zone.find('#select_script').val("").trigger('chosen:updated').trigger('change');
        crm_main_zone.find("#script_input_text").val("");
    });
//-----------------------------------------------------------------------SEARCH

    crm_main_zone.on("submit", "#filter_form", function(e) {
        e.preventDefault();
        if (crm_main_zone.find("#input_lead").val() !== "" || crm_main_zone.find("#input_phone").val() !== "") {
            crm_main_zone.find("#button_filtro").removeClass("icon-chevron-up").removeClass("icon-chevron-down").addClass("icon-chevron-down");
            crm_main_zone.find("#div_filtro_content").hide("blind");
            $(this).validationEngine("hideAll");

            if (crm_main_zone.find("#radio_client").is(":checked"))
                search("client");
            else
                search("calls");
        }
        else {
            if ($(this).validationEngine("validate"))
            {
                crm_main_zone.find("#button_filtro").removeClass("icon-chevron-up").removeClass("icon-chevron-down").addClass("icon-chevron-down");
                crm_main_zone.find("#div_filtro_content").hide("blind");
                $(this).validationEngine("hideAll");
                if (crm_main_zone.find("#radio_client").is(":checked"))
                    search("client");
                else
                    search("calls");
            }
        }
    });

    function search(type)
    {

        var tags_cd = [];
        var tags_script = [];
        $.each(crm_main_zone.find("#cd_tag_div span"), function() {
            tags_cd.push($(this).data("info"));
        });
        $.each(crm_main_zone.find("#script_tag_div span"), function() {
            tags_script.push($(this).data("info"));
        });

        if (type === "client") {
            crm_main_zone.find("#table_client").show();
            crm_main_zone.find("#table_calls").hide();
            crm_main_zone.find('#info_table_client').dataTable({
                "aaSorting": [[0, "asc"]],
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": file_path + 'crm_main/crm_main_request.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_info_client"},
                    {"name": "data_inicio", "value": crm_main_zone.find("#data_inicio").val()},
                    {"name": "data_fim", "value": crm_main_zone.find("#data_fim").val()},
                    {"name": "campanha", "value": select["campanha"].find("option:selected").val()},
                    {"name": "linha_inbound", "value": select["linha_inbound"].find("option:selected").val()},
                    {"name": "campaign_linha_inbound", "value": crm_main_zone.find("input[name='rft']:checked").val()},
                    {"name": "bd", "value": select["bd"].find("option:selected").val()},
                    {"name": "agente", "value": select["agente"].find("option:selected").val()},
                    {"name": "feedback", "value": select["feedback"].find("option:selected").val()},
                    {"name": "cd", "value": JSON.stringify(tags_cd)},
                    {"name": "script_info", "value": JSON.stringify(tags_script)},
                    {"name": "phone_number", "value": crm_main_zone.find("#input_phone").val()},
                    {"name": "lead_id", "value": crm_main_zone.find("#input_lead").val()},
                    {"name": "type_search", "value": crm_main_zone.find("#type_search_radio_call").is(":checked") ? "last_call" : "load"}

                    );
                },
                "fnDrawCallback": function()
                {
                    crm_main_zone.find('#info_table_client').show();

                    toggle_resultado("show");
                    if (!config.marcacao_cliente)
                        crm_main_zone.find(".criar_marcacao").hide();

                },
                "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Nome"}, {"sTitle": "Telefone"}, {"sTitle": "User"}, {"sTitle": "Feedback"}, {"sTitle": "Data Chamada"}],
                "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
            });
        }
        else {
            crm_main_zone.find("#table_client").hide();
            crm_main_zone.find("#table_calls").show();
            crm_main_zone.find('#info_table_calls').dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": file_path + 'crm_main/crm_main_request.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "get_info_calls"},
                    {"name": "data_inicio", "value": crm_main_zone.find("#data_inicio").val()},
                    {"name": "data_fim", "value": crm_main_zone.find("#data_fim").val()},
                    {"name": "campanha", "value": select["campanha"].find("option:selected").val()},
                    {"name": "linha_inbound", "value": select["linha_inbound"].find("option:selected").val()},
                    {"name": "campaign_linha_inbound", "value": crm_main_zone.find("input[name='rft']:checked").val()},
                    {"name": "bd", "value": select["bd"].find("option:selected").val()},
                    {"name": "agente", "value": select["agente"].find("option:selected").val()},
                    {"name": "feedback", "value": select["feedback"].find("option:selected").val()},
                    {"name": "cd", "value": JSON.stringify(tags_cd)},
                    {"name": "script_info", "value": JSON.stringify(tags_script)},
                    {"name": "phone_number", "value": crm_main_zone.find("#input_phone").val()},
                    {"name": "lead_id", "value": crm_main_zone.find("#input_lead").val()});
                },
                "fnDrawCallback": function()
                {
                    crm_main_zone.find('#info_table_calls').show();
                    toggle_resultado("show");
                    if (!config.marcacao_cliente)
                        crm_main_zone.find(".criar_marcacao").hide();

                },
                "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Nome"}, {"sTitle": "Telefone"}, {"sTitle": "User"}, {"sTitle": "Feedback"}, {"sTitle": "Duração da Chamada"}, {"sTitle": "Data Chamada"}],
                "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
            });
        }
    }

    //SUB OPÇÃO DO CLIENTE PARA ESPECIFICAR SE É PRA FAZER LOAD POR DATA DA ULTIMA CHAMADA OU DATA DA 
    $(crm_main_zone).on("click", "input[name='sccf']", function() {
        if (~~$(this).val() === 1) {
            crm_main_zone.find("#client_search_sub_option").css("display", "block");
        }
        else {
            crm_main_zone.find("#client_search_sub_option").css("display", "none");
            ;
        }
    });
//--------TOGGLE FILTROS
    crm_main_zone.on("click", "#button_filtro", function() {
        crm_main_zone.find("#div_filtro_content").toggle("blind");
        $(this).toggleClass("icon-chevron-up").toggleClass("icon-chevron-down");
    });

    crm_main_zone.on("click", "#button_resultado", function() {
        if (crm_main_zone.find("#resultado_div").is(":visible")) {
            toggle_resultado("hide");
        }
        else {
            toggle_resultado("show");
        }
    });

   
//------------------------------------------------------------EXTRA FUNCTIONS
    function toggle_resultado(type) {
        if (type === "show") {
            crm_main_zone.find("#resultado_div").show("blind");
            if (crm_main_zone.find("#button_resultado").hasClass('icon-chevron-down'))
                crm_main_zone.find("#button_resultado").removeClass('icon-chevron-down').addClass("icon-chevron-up");
        }
        else {
            crm_main_zone.find("#resultado_div").hide("blind");
            if (crm_main_zone.find("#button_resultado").hasClass('icon-chevron-up'))
                crm_main_zone.find("#button_resultado").removeClass('icon-chevron-up').addClass("icon-chevron-down");
        }
    }

    function get_script_translated_names(type) {
        var temp_type = "";
        switch (type) {
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

    
//----------Fechar TAGS
    $(crm_main_zone).on("click", ".close_tag", function() {
        $(this).parent().remove();
    });
//----------ABRIR CRM EDIT

    $(crm_main_zone).on("click", ".ver_cliente", function() {
        crm_edit_object = new crm_edit(crm_main_zone.find("#client_area"), "/sips-admin/crm/", $(this).data("lead_id"));
        crm_edit_object.destroy();

        crm_edit_object.init(function() {
            crm_main_zone.find('#loading').hide();
        });
        crm_main_zone.find('#client_div').show("blind");

        toggle_resultado("hide");
    });
    
    crm_main_zone.on("click", "#close_client_div", function() {
        crm_edit_object.destroy();
        crm_main_zone.find('#client_div').hide("blind");
        crm_main_zone.find("#button_resultado").trigger("click");
    });
};