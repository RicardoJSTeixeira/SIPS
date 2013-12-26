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


    this.init = function()
    {
        $.get(file_path + "crm_edit.html", function(data) {
            crm_edit_zone.append(data);
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
                                                crm_edit_zone.find("#dynamic_field_edit_div").show();
                                            }
                                            else
                                            {
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
                                                    get_dynamic_fields();
                                                    crm_edit_zone.find("#lead_edit_save_button").hide();
                                                    crm_edit_zone.find("#lead_edit_button").text("Editar Dados do Cliente");
                                                    crm_edit_zone.find("#dynamic_field_div input,textarea").prop("disabled", true);
                                                    me.edit_dynamic_field = 0;
                                                }
                                                else//EDITA
                                                {
                                                    crm_edit_zone.find("#lead_edit_save_button").show();
                                                    crm_edit_zone.find("#lead_edit_button").text("Cancelar Edição");
                                                    crm_edit_zone.find("#dynamic_field_div input,textarea").prop("disabled", false);
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
                                                if ($("#textarea_comment").val().length)
                                                    $.post(file_path + "crm_edit_request.php", {action: "add_info_crm", lead_id: me.lead_id, option: crm_edit_zone.find('input[name="radio_confirm_group"]:checked').val(), campaign_id: me.campaign_id, agent: crm_edit_zone.find("#agente_selector option:selected").val(), comment: crm_edit_zone.find("#textarea_comment").val()},
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
        });
    };
    this.destroy = function()
    {
        crm_edit_zone.empty();
        crm_edit_zone.off();
    };

    function get_user_level(callback)
    {
        $.post(file_path + "crm_edit_request.php", {action: "get_user_level"},
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
        $.post(file_path + "crm_edit_request.php", {action: "get_agentes"},
        function(data)
        {
            crm_edit_zone.find("#agente_selector").empty();
            var temp = "";
            $.each(data, function(index, value)
            {
                temp += "<option value=" + this.user + ">" + this.full_name + "</option>";
            });
            crm_edit_zone.find("#agente_selector").append(temp);
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function get_lead_info(callback)
    {
        $.post(file_path + "crm_edit_request.php", {action: "get_lead_info", lead_id: me.lead_id},
        function(data)
        {
            var data_load = moment(data.data_load);
            var data_last = moment(data.data_last);



            me.campaign_id = data.campaign_id;
            me.feedback = data.status;
            crm_edit_zone.find("#lead_info_tbody").append("<tr><td>" + me.lead_id + "</td>" +
                    "<td>" + data.phone_number + "</td>" +
                    "<td>" + data.list_name + "</td>" +
                    "<td>" + data.campaign_name + "</td>" +
                    "<td>" + data.full_name + "</td>" +
                    "<td>" + data.status_name + "</td>" +
                    "<td>" + data.called_count + "</td></tr>");
            crm_edit_zone.find("#lead_info_time_tbody").append("<tr><td>" +(data_load) ? data_load.lang("pt").format("D-MMMM-YYYY HH-mm-ss"):"" + "</td>" +
                    "<td>" + (data_load) ? data_load.fromNow() : "" + "</td>" +
                    "<td>" + (data_last) ? data_last.lang("pt").format("D-MMMM-YYYY HH-mm-ss") : "" + "</td>" +
                    "<td>" + (data_last) ? data_last.fromNow() : "" + "</td></tr>");
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function get_dynamic_fields(callback)
    {
        $.post(file_path + "crm_edit_request.php", {action: "get_dynamic_fields", lead_id: me.lead_id, campaign_id: me.campaign_id},
        function(data)
        {
            var dynamic_fields = "";
            var dynamic_field = "";
            crm_edit_zone.find("#dynamic_field_div").empty();

            if (Object.size(data))
            {
                me.has_dynamic_fields = true;
                $.each(data, function()
                {
                    dynamic_field =
                            " <div class='control-group'>" +
                            "     <label class='control-label'>" + this.display_name + "</label>" +
                            "          <div class='controls' >";
                    if (this.name == "COMMENTS")
                        dynamic_field += "<textarea disabled name=" + this.name + " id=" + this.name + " class='span9' >" + this.value + "</textarea>";
                    else
                        dynamic_field += "     <input disabled type=text name=" + this.name + " id=" + this.name + " class='span9' value=" + this.value + ">";
                    dynamic_field += "   <span id=" + this.name + "></span>" +
                            "   </div>" +
                            "   </div>";
                    dynamic_fields = dynamic_fields + dynamic_field;
                });
                crm_edit_zone.find("#dynamic_field_div").append(dynamic_fields);
            }
            else
            {
                crm_edit_zone.find("#dynamic_field_div").append("<span>Sem Campos Dinamicos</span>");
                me.has_dynamic_fields = false;
            }
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function get_feedbacks(callback)
    {
        $.post(file_path + "crm_edit_request.php", {action: "get_feedbacks", campaign_id: me.campaign_id, feedback: me.feedback},
        function(data)
        {
            crm_edit_zone.find("#feedback_list").append(data);
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
            "bSortClasses": true,
            "bProcessing": true,
            "bDestroy": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": file_path + 'crm_edit_request.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_calls"},
                {"name": "lead_id", "value": me.lead_id},
                {"name": "campaign_id", "value": me.campaign_id},
                {"name": "file_path", "value": me.file_path});
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
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": file_path + 'crm_edit_request.php',
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

    function save_dynamic_fields()
    {
        var fields = new Array();
        $.each(crm_edit_zone.find("#dynamic_field_div input,textarea"), function()
        {
            fields.push({"name": this.name, "value": $(this).val()});
        });
        $.post(file_path + "crm_edit_request.php", {action: "save_dynamic_fields", lead_id: me.lead_id, fields: fields},
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
        $.post(file_path + "crm_edit_request.php", {action: "save_feedback", lead_id: me.lead_id, feedback: crm_edit_zone.find("#feedback_list option:selected").val()}, "json");
    }

    function get_validation(callback)
    {

        $.post(file_path + "crm_edit_request.php", {action: "get_info_crm_confirm_feedback", lead_id: me.lead_id},
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
                            .append($("<td>").text($("#agente_selector option[value=" + this.agent + "]").text()))
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