var crm_edit = function(crm_edit_zone, file_path, lead_id)
{
    var me = this;
    this.file_path = file_path;
    this.user_level = 0;
    this.lead_id = lead_id;
    this.campaign_id = "";
    this.feedback = "";
    this.edit_dynamic_field = 0;
    this.has_dynamic_fields;

//----------------------------------- BASIC FUNCTIONS
    this.init = function(callback)
    {

  
        $.get(file_path + "crm_edit/crm_edit.html", function(data) {
            crm_edit_zone.append(data);
            crm_edit_zone.find(".chosen-select").chosen({no_results_text: "Sem resultados"});

            get_user_level(function() {
                get_lead_info(function() {
                    get_dynamic_fields(function() {
                        get_feedbacks(function() {
                            get_calls(function() {
                                get_recordings(function() {
                                    get_agentes(function() {
                                        get_validation(function() {
                                            if (me.user_level > 5 && me.has_dynamic_fields)
                                            {
                                                crm_edit_zone.find("#modify_feedback_status").show();
                                                crm_edit_zone.find("#dynamic_field_edit_div").show();
                                            }
                                            else
                                            {
                                                crm_edit_zone.find("#modify_feedback_status").hide();
                                                crm_edit_zone.find("#dynamic_field_edit_div").hide();
                                            }
                                            $(crm_edit_zone).on("click", "#confirm_feedback", function()
                                            {
                                                if (!crm_edit_zone.find("#confirm_feedback_div").is(":visible"))
                                                    get_validation(function() {
                                                        crm_edit_zone.find("#confirm_feedback_div").show(600);
                                                    });
                                                else
                                                    crm_edit_zone.find("#confirm_feedback_div").hide(400);
                                            });
                                            $(crm_edit_zone).on("click", "#lead_edit_button", function()
                                            {
                                                if (me.edit_dynamic_field)//CANCELA
                                                {
                                                    crm_edit_zone.find("#inputcontainer")[0].reset();
                                                    crm_edit_zone.find("#lead_edit_save_button").hide();
                                                    crm_edit_zone.find("#lead_edit_button").text("Editar Dados do Cliente");
                                                    crm_edit_zone.find(".dynamic_field_divs input,textarea").prop("readonly", true);
                                                    me.edit_dynamic_field = 0;
                                                }
                                                else//EDITA
                                                {
                                                    crm_edit_zone.find("#lead_edit_save_button").show();
                                                    crm_edit_zone.find("#lead_edit_button").text("Cancelar Edição");
                                                    crm_edit_zone.find(".dynamic_field_divs input,textarea").prop("readonly", false);
                                                    me.edit_dynamic_field = 1;
                                                }
                                            });

                                            $(crm_edit_zone).on("click", "#lead_edit_save_button", function()
                                            {
                                                save_dynamic_fields();
                                            });

                                            $(crm_edit_zone).on("change", "#feedback_list", function()
                                            {
                                                if (me.user_level > 5)
                                                {
                                                    if ($(this).find("option:selected").data("sale") == "Y")
                                                    {
                                                        crm_edit_zone.find("#confirm_feedback").show();
                                                    }
                                                    else
                                                    {
                                                        crm_edit_zone.find("#confirm_feedback").hide();
                                                        crm_edit_zone.find("#confirm_feedback_div").hide();
                                                    }
                                                    save_feedback();
                                                }
                                            });



                                            //insere nova entrada
                                            $(crm_edit_zone).on("click", "#confirm_feedback_button", function()
                                            {
                                                if (crm_edit_zone.find("#textarea_comment").val().length)
                                                    $.post(file_path + "crm_edit/crm_edit_request.php", {action: "add_info_crm", lead_id: me.lead_id, option: crm_edit_zone.find('input[name="radio_confirm_group"]:checked').val(), campaign_id: me.campaign_id, agent: crm_edit_zone.find("#agente_selector option:selected").val(), comment: crm_edit_zone.find("#textarea_comment").val()},
                                                    function(data)
                                                    {
                                                        get_validation();
                                                    }, "json");
                                            });
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
            if (typeof callback === "function")
            {
                callback();
            }
        });
    };
    this.destroy = function()
    {
        crm_edit_zone.empty().off();
    };
//-----------------------------------BASIC INFO
    function get_user_level(callback)
    {
        $.post(file_path + "crm_edit/crm_edit_request.php", {action: "get_user_level"},
        function(data)
        {
            me.user_level = data;
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function get_agentes(callback)
    {
        $.post(file_path + "crm_edit/crm_edit_request.php", {action: "get_agentes"},
        function(data)
        {
            crm_edit_zone.find("#agente_selector").empty();
            var temp = "";
            $.each(data, function(index, value)
            {
                temp += "<option value=" + this.user + ">" + this.full_name + "</option>";
            });
            crm_edit_zone.find("#agente_selector").append(temp).trigger("chosen:updated");
            ;
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function get_lead_info(callback)
    {
        $.post(file_path + "crm_edit/crm_edit_request.php", {action: "get_lead_info", lead_id: me.lead_id},
        function(data)
        {
            var data_load_fromNow = "";
            var data_load_format = "";


            var data_last_fromNow = "";
            var data_last_format = "";
            var data_is_archive = false;
            if (data.data_load)
            {
                data_load_fromNow = moment(data.data_load).fromNow();
                data_load_format = moment(data.data_load).format("D-MMMM-YYYY HH:mm:ss");
            }

            if (data.data_last)
            {
                data_last_fromNow = moment(data.data_last).fromNow();
                data_last_format = moment(data.data_last).format("D-MMMM-YYYY HH:mm:ss");
                data_is_archive = moment(data.data_last).isBefore(moment().subtract('month', 2));
            }





            me.campaign_id = data.campaign_id;
            me.feedback = data.status;
            crm_edit_zone.find("#lead_info_tbody").append("<tr><td>" + me.lead_id + "</td>" +
                    "<td>" + data.phone_number + "</td>" +
                    "<td>" + data.list_name + "</td>" +
                    "<td>" + data.campaign_name + "</td>" +
                    "<td>" + data.full_name + "</td>" +
                    "<td>" + data.status_name + "</td>" +
                    "<td>" + data.called_count + "</td></tr>");
            crm_edit_zone.find("#lead_info_time_tbody").append("<tr><td>" + data_load_format + "</td>" +
                    "<td>" + data_load_fromNow + "</td>" +
                    "<td>" + data_last_format + "</td>" +
                    "<td>" + data_last_fromNow + "</td></tr>");

            if (data_is_archive)
                $.jGrowl('As chamadas carregadas pertencem à tabela de arquivo(+ de 2 Meses)', {life: 5000});
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function get_dynamic_fields(callback)
    {
        $.post(file_path + "crm_edit/crm_edit_request.php", {action: "get_dynamic_fields", lead_id: me.lead_id, campaign_id: me.campaign_id},
        function(data)
        {

            var dynamic_field = "";
            crm_edit_zone.find("#dynamic_field_div1").empty();
            crm_edit_zone.find("#dynamic_field_div2").empty();

            me.has_dynamic_fields = true;
            var controler = 1;

            $.each(data, function()
            {
                dynamic_field =
                        " <div class='control-group'>" +
                        "     <label class='control-label'>" + this.display_name + "</label>" +
                        "          <div class='controls' >";
                if (this.name == "COMMENTS")
                {
                    dynamic_field += "<textarea readonly name='" + this.name + "' id='" + this.name + "'  >" + this.value + "</textarea>";
                }
                else
                {
                    dynamic_field += "     <input readonly type=text name='" + this.name + "' id='" + this.name + "'  value='" + this.value + "'>";
                    dynamic_field += "   <span id=" + this.name + "></span>" +
                            "   </div>" +
                            "   </div>";
                }


                if (controler) {
                    controler = 0;
                    crm_edit_zone.find("#dynamic_field_div1").append(dynamic_field);
                }
                else
                {
                    controler = 1;
                    crm_edit_zone.find("#dynamic_field_div2").append(dynamic_field);
                }
            });


            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function get_feedbacks(callback)
    {
        $.post(file_path + "crm_edit/crm_edit_request.php", {action: "get_feedbacks", campaign_id: me.campaign_id},
        function(data)
        {
            var options = "";
            $.each(data, function()
            {
                if (this.status == me.feedback)
                    options += "<option data-sale=" + this.sale + " selected value=" + this.status + ">" + this.status_name + "</option>";
                else
                    options += "<option data-sale=" + this.sale + " value=" + this.status + ">" + this.status_name + "</option>";
            });

            crm_edit_zone.find("#feedback_list").append(options);
            if (crm_edit_zone.find("#feedback_list option:selected").data("sale") == "Y" && me.user_level > 5)
                crm_edit_zone.find("#confirm_feedback").show();
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function get_calls(callback)
    {
        var Table_chamadas = crm_edit_zone.find('#chamadas_realizadas').dataTable({
                "aaSorting": [[ 0, "desc" ]],
            "bSortClasses": true,
            "bProcessing": true,
            "bDestroy": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": file_path + 'crm_edit/crm_edit_request.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_calls_all"},
                {"name": "lead_id", "value": me.lead_id}               );
            },
            "aoColumns": [{"sTitle": "Data"}, {"sTitle": "Duração"}, {"sTitle": "Número"}, {"sTitle": "Operador"}, {"sTitle": "Feedback"}, {"sTitle": "Campanha"}, {"sTitle": "Base de Dados"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });
        if (typeof callback === "function")
        {
            callback();
        }
    }

    function get_recordings(callback)
    {
        var Table_recording = crm_edit_zone.find('#chamadas_gravadas').dataTable({
                "aaSorting": [[ 0, "desc" ]],
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": file_path + 'crm_edit/crm_edit_request.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_recordings"},
                {"name": "lead_id", "value": me.lead_id});
            },
            "aoColumns": [{"sTitle": "Data"}, {"sTitle": "Inicio da Gravação"}, {"sTitle": "Fim da Gravação"}, {"sTitle": "Duração"}, {"sTitle": "Operador"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });
        if (typeof callback === "function")
        {
            callback();
        }
    }

//-------------------------------------Save changes
    function save_dynamic_fields()
    {
        var fields = new Array();
        $.each(crm_edit_zone.find(".dynamic_field_divs input,textarea"), function()
        {
            fields.push({"name": this.name, "value": $(this).val()});
        });

        $.post(file_path + "crm_edit/crm_edit_request.php", {action: "save_dynamic_fields", lead_id: me.lead_id, fields: fields},
        function(data)
        {
            crm_edit_zone.find("#lead_edit_save_button").hide();
            crm_edit_zone.find("#lead_edit_button").text("Editar Dados do Cliente");
            me.edit_dynamic_field = 0;
            get_dynamic_fields();
        }, "json");
    }


    function save_feedback()
    {
        $.post(file_path + "crm_edit/crm_edit_request.php", {action: "save_feedback", lead_id: me.lead_id, feedback: crm_edit_zone.find("#feedback_list option:selected").val()}, "json");
    }

    function get_validation(callback)
    {

        $.post(file_path + "crm_edit/crm_edit_request.php", {action: "get_info_crm_confirm_feedback", lead_id: me.lead_id},
        function(data)
        {
            crm_edit_zone.find("#comment_log_tbody").empty();
            crm_edit_zone.find("#radio_confirm_no").prop("checked", true);
            if (Object.size(data))
            {
                $.each(data, function() {
                    crm_edit_zone.find("#comment_log_tbody").append($("<tr>")
                            .append($("<td>").text(this.comment))
                            .append($("<td>").text(this.feedback))
                            .append($("<td>").text(crm_edit_zone.find("#agente_selector option[value=" + this.agent + "]").text()))
                            .append($("<td>").text(this.admin))
                            .append($("<td>").text(this.date))
                            );
                    if (this.sale == "1")
                        crm_edit_zone.find("#radio_confirm_yes").prop("checked", true);
                    else if (this.sale == "0")
                        crm_edit_zone.find("#radio_confirm_no").prop("checked", true);
                    else
                        crm_edit_zone.find("#radio_confirm_return").prop("checked", true);
                    crm_edit_zone.find("#agente_selector option[value=" + this.agent + "]").prop("selected", true);
                    crm_edit_zone.find("#div_comentarios").show();
                    crm_edit_zone.find("#textarea_comment").val("");
                });
            }
            else
            {
                crm_edit_zone.find("#textarea_comment").val("");
                crm_edit_zone.find("#div_comentarios").hide();
                crm_edit_zone.find("#radio_confirm_no").prop("checked", true);
            }
            if (typeof callback === "function")
                callback();
        }, "json");
    }

//----------------------------------------Extra functions
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
}