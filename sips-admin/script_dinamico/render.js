var render = function(script_zone, file_path, script_id, lead_id, unique_id, user_id, campaign_id, admin_review, ext_events)
{

    var
            me = this,
            array_id = [],
            events = {
                onEverethingCompleted: function() {

                }
            };
    $.extend(true, events, ext_events);
    this.script_id = script_id;
    this.lead_id = lead_id;
    this.unique_id = unique_id;
    this.user_id = user_id;
    this.campaign_id = campaign_id;
    this.admin_review = admin_review;
    this.client_info = {};
    this.validado_function = false;
    this.nao_validado_function = false;
    this.has_script = true;
    this.config = new Object();
    this.init = function(ext_config)
    {


        me.config.save_overwrite = false;
        me.config.input_disabled = false;
        $.extend(true, me.config, ext_config);
        $.ajaxSetup({cache: false});
        before_all(function() {
            update_script(function() {
                update_info(function() {
                    insert_element(function() {
                        starter(function() {
                            if (me.config.input_disabled)
                            {
                                script_zone.find("input").prop("disabled", true).end()
                                        .find("select").prop("disabled", true).end();
                            }
                            events.onEverethingCompleted(me);
                        });
                    });
                });
            });
        });
    };
    function before_all(callback)
    {
        if (me.lead_id)
        {
            $.post(file_path + "requests.php", {action: "get_client_info_by_lead_id", lead_id: me.lead_id, user_logged: user_id},
            function(info1)
            {
                if (Object.size(info1))
                    me.client_info = info1;
                if (typeof callback === "function")
                {
                    callback();
                }

            }, "json");
        }
        else
        if (typeof callback === "function")
        {
            callback();
        }
    }

    function starter(callback)
    {
        if (admin_review !== 1)
        {
            $("#script_form .pag_div").hide().first().show();
            $("#script_form #admin_submit").hide();
            rules(function() {
                tags(function() {
                    me.populate_script(function() {
                        script_zone.find("#script_form").validationEngine(
                                {
                                    onValidationComplete: function(form, status)
                                    {
                                        if (status) {
                                            if (typeof me.validado_function === "function")
                                            {
                                                me.validado_function();
                                            }
                                        } else {
                                            if (typeof me.nao_validado_function === "function")
                                            {
                                                me.nao_validado_function();
                                            }
                                        }
                                    }
                                });
                    });
                });
            });
        }
        else
        {
            script_zone.find("#script_form .pagination_class").remove();
            script_zone.find("#script_form .botao").remove();
            script_zone.find("#script_form #admin_submit").show();
            me.populate_script();
            script_zone.find(".item").show();
        }
        if (typeof callback === "function")
        {
            callback();
        }
    }

    $.get(file_path + "items/items.html", function(data) {
        script_zone.append($("<div>").attr("id", "script_dummie").css("display", "none"));
        script_zone.append($("<form>").attr("id", "script_form").addClass("formular"));
        script_zone.find("#script_form").append($("<div>").attr("id", "script_div").css("width", "100%").css("margin", "0 auto"));
        script_zone.find("#script_dummie").html(data);
        array_id["radio"] = 0;
        array_id["checkbox"] = 0;
        array_id["input"] = 0;
        $(script_zone).on("click", "#script_div .previous_pag", function(e) {
            e.preventDefault();
            var temp = script_zone.find(".pag_div:visible").prev(".pag_div");
            if (temp.length)
            {
                $(".pag_div").hide();
                temp.show();
            }
        });
        $(script_zone).on("click", "#script_div .next_pag", function(e) {
            e.preventDefault();
            me.validate_manual(function() {
                var temp = script_zone.find(".pag_div:visible").next(".pag_div");
                if (temp.length)
                {
                    $(".pag_div").hide();
                    temp.show();
                }
            }, false);
        });
        $(script_zone).on("click", "#script_div .scheduler_button_go", function(e) {
            e.preventDefault();
            var select = $(this).prev("select");
            if (select.find("option:selected").val() > 0)
            {
                var url = '/sips-admin/reservas/views/calendar_container.php?sch=' + $(this).prev("select").val() + '&user=' + user_id + '&lead=' + lead_id + '&id_elemento=' + $(this).prev("select").data("element_tag");
                window.open(url, 'Calendario', 'fullscreen=yes, scrollbars=auto,status=1');
            }
        });
        $(script_zone).on("click", "#script_div .pdf_button", function(e)
        {
            var url = file_path + "files/" + $(this).attr("file");
            window.open(url, 'PDF', 'fullscreen=no, scrollbars=auto');
        });
    });
//UPDATES DE INFO
    function update_script(callback)
    {
        if (me.script_id !== undefined)
        {
            $.post(file_path + "requests.php", {action: "get_scripts_by_id_script", id_script: me.script_id},
            function(data)
            {
                if (Object.size(data))
                {
                    if (data !== null)
                    {
                        me.script_id = data.id;
                        if (typeof callback === "function")
                        {
                            callback();
                        }
                    }
                }
                else
                {
                    me.has_script = false;
                    $.jGrowl('Sem script', {life: 3000});
                }

            }, "json");
        }
        else
        {
            var camp_linha = 0;
            if (campaign_id !== "")
            {
                camp_linha = campaign_id;
            }
            $.post(file_path + "requests.php", {action: "get_render_scripts_by_campaign", id_campaign: camp_linha, lead_id: me.lead_id},
            function(data)
            {
                if (Object.size(data))
                {
                    me.script_id = data.id;
                    if (typeof callback === "function")
                    {
                        callback();
                    }
                }
                else
                {
                    me.has_script = false;
                    $.jGrowl('Sem script', {life: 3000});
                }
            }, "json");
        }
    }

    function update_info(callback)
    {
        script_zone.find(".datetimepicker").remove();
        $.post(file_path + "requests.php", {action: "get_data_render", id_script: me.script_id, lead_id: me.lead_id},
        function(data)
        {
            $("#script_div").empty();
            var item;
            $.each(data, function() {
                item = script_zone.find('#script_dummie .' + this.type + '_class').clone();
                item.attr("id", this.tag).data("info", this);
                if (!script_zone.find("#script_div #" + this.id_page + "pag").length) {
                    script_zone.find("#script_div").append($("<div>").addClass("pag_div").attr("id", this.id_page + "pag"));
                }
                script_zone.find("#script_div #" + this.id_page + "pag").append(item);
            });
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    }

    function insert_element(callback)
    {
        var info = [];
        var element = 0;
        $.each(script_zone.find("#script_div .pag_div .item"), function() {
            element = $(this);
            element.removeAttr("title");
            element.find(".label_titulo").remove();
            info = $(this).data("info");
            if (info.hidden)
                element.css("display", "none");
            switch (info.type)
            {
                case "texto":
                    element.find(".label_geral")[0].innerHTML = info.texto;
                    var input = element.find(".input_texto")[0];
                    input.placeholder = info.placeholder;
                    input.maxLength = info.max_length;
                    input.name = info.tag;
                    if (info.default_value)
                        if (info.default_value.name != 0 && Object.size(me.client_info) && !me.admin_review)
                        {
                            input.value = me.client_info[info.default_value.name.toLowerCase()];
                        }
                    var pattern = [];
                    if (info.required)
                        pattern.push("required");
                    switch (info.param1)
                    {
                        case "normal":
                            pattern.push("[custom[onlyLetterNumberSymbol]]");
                            break;
                        case "letter":
                            pattern.push("[custom[onlyLetterSp]]");
                            break;
                        case "number":
                            pattern.push("[custom[onlyNumberSp]]");
                            break;
                        case "email":
                            pattern.push("[custom[email]]");
                            break;
                        case "postal":
                            pattern.push("[custom[postcodePT]]");
                            break;
                        case "nib":
                            pattern.push("funcCall[checknib]");
                            break;
                        case "nif":
                            pattern.push("funcCall[checknif]");
                            break;
                        case "credit_card_m":
                            pattern.push("funcCall[isValidMastercard]");
                            break;
                        case "credit_card_v":
                            pattern.push("funcCall[isValidVISA]");
                            break;
                        case "credit_card_d":
                            pattern.push("funcCall[isValidDebit]");
                            break;
                        case "ajax":
                            {
                                pattern.push("ajax[rule" + info.tag + "]]");
                                $.validationEngineLanguage.allRules["rule" + info.tag] = {
                                    "url": file_path + "files/" + info.values_text.file,
                                    "alertText": info.values_text.not_validado,
                                    "alertTextOk": info.values_text.validado,
                                    "alertTextLoad": "* A validar, por favor aguarde"
                                };
                            }
                            break;
                    }
                    if (pattern.length > 0)
                        element.find(".input_texto").addClass(" validate[" + pattern.join(",") + "]");
                    break;
                case "radio":
                    element.empty();
                    element.append($("<label>").addClass("label_radio label_geral").text($("#radio_edit").val()));
                    element.find(".label_radio")[0].innerHTML = info.texto;
                    var radios = info.values_text;
                    for (var count = 0; count < radios.length; count++)
                    {
                        if (info.required)
                            element.append($("<input>")
                                    .attr("type", "radio")
                                    .addClass("validate[required]")
                                    .attr("value", radios[count])
                                    .attr("id", array_id["radio"] + "radio")
                                    .attr("name", info.tag))
                                    .append($("<label>")
                                            .addClass("radio_name radio inline tagReplace")
                                            .attr("for", array_id["radio"] + "radio")
                                            .html("<span></span>" + radios[count])
                                            );
                        else
                            element.append($("<input>")
                                    .attr("type", "radio")
                                    .attr("value", radios[count])
                                    .attr("id", array_id["radio"] + "radio")
                                    .attr("name", info.tag))
                                    .append($("<label>")
                                            .addClass("radio_name radio inline tagReplace")
                                            .attr("for", array_id["radio"] + "radio")
                                            .html("<span></span>" + radios[count])
                                            );
                        if (info.dispo === "v")
                            element.append($("<br>"));
                        array_id["radio"] = array_id["radio"] + 1;
                    }
                    break;
                case "checkbox":
                    element.empty();
                    element.append($("<label>").addClass("label_checkbox label_geral").text($("#checkbox_edit").val()));
                    element.find(".label_checkbox")[0].innerHTML = info.texto;
                    var checkboxs = info.values_text;
                    for (var count = 0; count < checkboxs.length; count++)
                    {
                        if (info.required)
                            element.
                                    append($("<input>")
                                            .attr("type", "checkbox")
                                            .addClass("validate[minCheckbox[1]]")
                                            .attr("value", checkboxs[count])
                                            .attr("id", array_id["checkbox"] + "checkbox")
                                            .attr("name", info.tag))
                                    .append($("<label>")
                                            .addClass("checkbox_name checkbox inline tagReplace")
                                            .attr("for", array_id["checkbox"] + "checkbox")
                                            .html("<span></span>" + checkboxs[count])
                                            );
                        else
                            element.append($("<input>")
                                    .attr("type", "checkbox")
                                    .attr("value", checkboxs[count])
                                    .attr("id", array_id["checkbox"] + "checkbox")
                                    .attr("name", info.tag))
                                    .append($("<label>")
                                            .addClass("checkbox_name checkbox inline tagReplace")
                                            .attr("for", array_id["checkbox"] + "checkbox")
                                            .html("<span></span>" + checkboxs[count])
                                            );
                        if (info.dispo === "v")
                            element.append($("<br>"));
                        array_id["checkbox"] = array_id["checkbox"] + 1;
                    }
                    break;
                case "multichoice":
                    element.empty();
                    element.append($("<label>").addClass("label_multichoice label_geral tagReplace").text(info.texto));
                    var multichoices = info.values_text;
                    if (info.required)
                        element.append($("<select>").addClass("multichoice_select validate[required]").attr("name", info.tag));
                    else
                        element.append($("<select>").addClass("multichoice_select").attr("name", info.tag));
                    var select = element.find(".multichoice_select");
                    var options = "<option value=''>Selecione uma opção</option>";
                    for (var count = 0; count < multichoices.length; count++)
                    {
                        options += "<option value='" + multichoices[count] + "'>" + multichoices[count] + "</option>";
                    }
                    select.append(options);
                    break;
                case "textfield":
                    element.find(".label_geral")[0].innerHTML = info.values_text;
                    element.find(".label_geral")[0].name = info.tag;
                    break;
                case "legend":
                    element.find(".label_geral")[0].innerHTML = info.values_text;
                    element.find(".label_geral")[0].name = info.tag;
                    break;
                case "tableradio":
                    element.find(".label_geral")[0].innerHTML = info.texto;
                    var tr_head = element.find(".tr_head");
                    tr_head.empty();
                    var titulos = info.placeholder;
                    tr_head.append($("<td>"));
                    for (var count = 0; count < titulos.length; count++)
                    {
                        tr_head.append($("<td>").text(titulos[count]));
                    }
                    var tr_body = element.find(".tr_body");
                    tr_body.empty();
                    var perguntas = info.values_text;
                    var trbody_last;
                    for (var count = 0; count < perguntas.length; count++)
                    {
                        tr_body.append($("<tr>")
                                .append($("<td>").text(perguntas[count]).addClass("td_row")));
                        trbody_last = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                            if (info.required)
                            {
                                trbody_last.append($("<td>")
                                        .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").addClass("validate[required]").attr("value", titulos[count2]).attr("name", info.tag + "###" + perguntas[count]))
                                        .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio").html("<span></span>")));
                            }
                            else
                            {
                                trbody_last.append($("<td>")
                                        .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").attr("value", titulos[count2]).attr("name", info.tag + "###" + perguntas[count]))
                                        .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio").html("<span></span>")));
                            }
                            array_id["radio"] = array_id["radio"] + 1;
                        }
                    }
                    break;
                case "tableinput":
                    element.find(".label_geral")[0].innerHTML = info.texto;
                    var tr_head = element.find(".tr_head");
                    tr_head.empty();
                    var titulos = info.placeholder;
                    tr_head.append($("<td>"));
                    for (var count = 0; count < titulos.length; count++)
                    {
                        tr_head.append($("<td>").text(titulos[count]));
                    }
                    var tr_body = element.find(".tr_body");
                    tr_body.empty();
                    var perguntas = info.values_text;
                    var trbody_last;
                    for (var count = 0; count < perguntas.length; count++)
                    {
                        tr_body.append($("<tr>")
                                .append($("<td>").text(perguntas[count]).addClass("td_row")));
                        trbody_last = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                            if (info.required)
                            {
                                trbody_last.append($("<td>")
                                        .append($("<input>").attr("type", "text").attr("maxLength", 25).attr("titulo", titulos[count2]).attr("id", array_id["input"] + "tableinput").addClass("input-medium validate[required] ").attr("name", info.tag + "###" + perguntas[count] + "###" + titulos[count2])));
                            }
                            else
                            {
                                trbody_last.append($("<td>")
                                        .append($("<input>").attr("type", "text").attr("maxLength", 25).attr("titulo", titulos[count2]).attr("id", array_id["input"] + "tableinput").addClass("input-medium").attr("name", info.tag + "###" + perguntas[count] + "###" + titulos[count2])));
                            }
                            array_id["input"] = array_id["input"] + 1;
                        }
                    }
                    break;
                case "datepicker":
                    element.find(".label_geral")[0].innerHTML = info.texto;
                    if (info.required)
                        element.find(".form_datetime").addClass("validate[required] text-input datepicker");
                    element.find(".form_datetime")[0].name = info.tag;
                    var options = {};
                    options.format = "yyyy-mm-dd hh:ii";
                    options.minView = 0;
                    options.autoclose = true;
                    options.language = "pt";
                    switch (info.placeholder)
                    {
                        case "0":
                            options.format = 'yyyy-mm-dd hh:ii';
                            options.minView = 0;
                            break;
                        case "1":
                            options.format = 'yyyy-mm-dd hh';
                            options.minView = 1;
                            break;
                        case "2":
                            options.format = 'yyyy-mm-dd';
                            options.minView = 2;
                            break;
                        case "3":
                            options.format = 'dd-mm-yyyy';
                            options.minView = 2;
                            break;
                        default:
                            options.format = 'yyyy-mm-dd';
                            options.minView = 2;
                            break;
                    }
                    if (info.values_text.type == "dynamic")//dynamic
                    {
                        if (info.values_text.data_inicial != "#|#|#|#")
                        {
                            var tempo1 = info.values_text.data_inicial.split("|");
                            var time1 = moment();
                            if (tempo1[0] != "#")
                                time1.add('year', tempo1[0]);
                            if (tempo1[1] != "#")
                                time1.add('month', tempo1[1]);
                            if (tempo1[2] != "#")
                                time1.add('day', tempo1[2]);
                            if (tempo1[3] != "#")
                                time1.add('hour', tempo1[3]);
                            options.startDate = time1.toDate();
                        }
                        if (info.values_text.data_final != "#|#|#|#")
                        {
                            var tempo2 = info.values_text.data_final.split("|");
                            var time2 = moment();
                            if (tempo2[0] != "#")
                                time2.add('year', tempo2[0]);
                            if (tempo2[1] != "#")
                                time2.add('month', tempo2[1]);
                            if (tempo2[2] != "#")
                                time2.add('day', tempo2[2]);
                            if (tempo2[3] != "#")
                                time2.add('hour', tempo2[3]);
                            options.endDate = time2.toDate();
                        }
                    } else if (info.values_text.type == "fixed")//fixed
                    {
                        options.startDate = info.values_text.data_inicial;
                        options.endDate = info.values_text.data_final;
                    }

                    if (info.max_length == 1)
                        options.daysOfWeekDisabled = [0, 6];
                    script_zone.find("input[name='" + info.tag + "']").datetimepicker(options).keypress(function(e) {
                        e.preventDefault();
                    }).bind("cut copy paste", function(e) {
                        e.preventDefault();
                    });
                    break;
                case "scheduler":
                    element.find(".scheduler_button_go").attr("id", info.id + "go_button");
                    var select = element.find(".scheduler_select");
                    element.find(".label_geral")[0].innerHTML = info.texto;
                    element.find(".scheduler_select").addClass("validate[funcCall[scheduler_verif]]");
                    $.post(file_path + "requests.php", {action: "get_schedule_by_id", ids: info.values_text.join(",")},
                    function(info3)
                    {
                        $.each(info3, function(index, value) {
                            select.append("<option value=" + this.id + ">" + this.text + "</option>");
                        });
                        select.val("").trigger("chosen:updated");
                    }, "json");
                    element.find(".scheduler_select").data("element_tag", info.tag)
                            .data("max_marc", info.max_length)
                            .data("obrig_marc", info.param1).data("live_marc", 0);
                    break;
                case "textarea":
                    element.find(".label_geral")[0].innerHTML = info.texto;
                    element.find(".input_textarea")[0].name = info.tag;
                    if (info.required)
                        element.find(".input_textarea").addClass("validate[required]");
                    break;
                case "ipl":
                    element.find(".label_geral")[0].innerHTML = info.texto;
                    element.find("span").remove();
                    if (info.param1 == "1")
                    {
                        if (info.values_text.length > 3)
                        {
                            element.append($("<img>").attr("src", file_path + 'files\/' + info.values_text));
                        }
                    }
                    else if (info.param1 == "2")
                    {
                        if (info.values_text.length > 0)
                        {
                            element.append($("<button>").addClass("pdf_button btn btn-primary icon-folder-open").attr("file", info.values_text).text(" Ver PDF"));
                        }
                    }
                    else
                    {
                        element.append($("<a>").attr("href", "http://" + info.values_text).text(info.values_text));
                    }
                    break;
                case "button":
                    element.find(".botao").text(info.texto);
                    if (info.param1.length)
                    {
                        $(script_zone).on("click", "#script_div #" + info.tag + " button", function()//atribuir os ons a cada value
                        {
                            var this_elements = [];
                            var this_info = $(this).closest(".item").data("info");
                            $.each(this_info.default_value, function()
                            {
                                switch ($("[name=" + ~~this + "]").parents(".item").data("info").type)
                                {
                                    case "radio":
                                        this_elements.push({name: ~~this, value: $("[name=" + ~~this + "]:checked").val()});
                                        break;
                                    case "checkbox":
                                        var temp = [];
                                        $.each($("[name=" + ~~this + "]:checked"), function()
                                        {
                                            temp.push($(this).val());
                                        });
                                        this_elements.push({name: ~~this, value: temp});
                                        break;
                                    case "select":
                                        this_elements.push({name: ~~this, value: $("[name=" + ~~this + "]:selected").val()});
                                        break;
                                    default:
                                        this_elements.push({name: ~~this, value: $("[name=" + ~~this + "]").val()});
                                        break;
                                }
                            });
                            $.ajax({
                                type: this_info.param1,
                                url: file_path + "proxy.php",
                                data: {csurl: this_info.values_text, elements: this_elements},
                                dataType: "json",
                                success: function(data)
                                {
                                    $.each(data, function()
                                    {
                                        if (~~this.name)
                                            if ($("[name=" + ~~this.name + "]"))
                                                switch ($("[name=" + ~~this.name + "]").parents(".item").data("info").type)
                                                {
                                                    case "radio":
                                                        $("[name=" + ~~this.name + "][value='" + this.value + "']").prop("checked", true);
                                                        break;
                                                    default:
                                                        $("[name=" + ~~this.name + "]").val(this.value);
                                                        break;
                                                }
                                    });
                                }
                            });
                        });
                    }
                    break;
            }
        });
        if (typeof callback === "function")
        {
            callback();
        }
    }

    this.populate_script = function(callback)
    {
        $.post(file_path + "requests.php", {action: "get_results_to_populate", search_spice: me.config.save_overwrite, lead_id: me.lead_id, id_script: me.script_id, unique_id: me.unique_id},
        function(data)
        {
            if (Object.size(data))
            {
                $.each(data, function() {
                    switch (this.type)
                    {
                        case "textarea":
                        case "texto":
                            script_zone.find("#script_div #" + this.tag_elemento + " :input").val(this.valor);
                            break;
                        case "radio":
                        case "checkbox":
                            script_zone.find("#script_div #" + this.tag_elemento + " :input[value='" + this.valor + "']").attr('checked', true);
                            break;
                        case "multichoice":
                            script_zone.find("#script_div #" + this.tag_elemento + " :input").val(this.valor);
                            break;
                        case "tableradio":
                            script_zone.find("#script_div #" + this.tag_elemento + " tbody tr:contains(" + this.param1 + ") :input[value='" + this.valor + "']").attr('checked', true);
                            break;
                        case "tableinput":
                            var temp = this.param1.split(";");
                            script_zone.find("#script_div #" + this.tag_elemento + " tbody tr:contains(" + temp[1] + ") :input[titulo='" + temp[0] + "']").val(this.valor);
                            break;
                        case "datepicker":
                            script_zone.find("#script_div #" + this.tag_elemento + " :input").val(this.valor);
                            break;
                    }
                });
            }

            if (typeof callback === "function")
            {
                callback();
            }
        }, "json");
    };
//RULES  
    function rules_work(data)
    {
        switch (data.tipo)
        {
            case "hide":
                var target = data.tag_target;
                for (var count2 = 0; count2 < target.length; count2++)
                {
                    script_zone.find("#script_div #" + target[count2]).fadeOut(200);
                }
                break;
            case "show":
                var target = data.tag_target;
                for (var count2 = 0; count2 < target.length; count2++)
                {
                    script_zone.find("#script_div #" + target[count2]).fadeIn(250);
                }
                break;
            case "goto":

                if (admin_review != "1")
                {
                    script_zone.find(".pag_div").fadeOut(250);
                    script_zone.find("#script_div #" + data.tag_target + "pag").fadeIn(300);
                }
                break;
        }
    }
    function rules(callback)
    {
        $.post(file_path + "requests.php", {action: "get_rules", id_script: me.script_id},
        function(data)
        {
            $.each(data, function(index, value) {
                switch (this.tipo_elemento)
                {
                    case "texto":
                        switch (this.param1)
                        {
                            case "value_input":

                                $(script_zone).on("keyup", "#script_div #" + this.tag_trigger, function()//atribuir os ons a cada value
                                {
                                    var pattern = new RegExp('\\b' + data[index].tag_trigger2, 'i');
                                    if (script_zone.find("#script_div #" + data[index].tag_trigger + " input").val().match(pattern))
                                    {
                                        rules_work(data[index]);
                                    }
                                }
                                );
                                break;
                            case "answer":

                                $(script_zone).on("focusout", "#script_div #" + this.tag_trigger, function()//atribuir os ons a cada value
                                {
                                    rules_work(data[index]);
                                }
                                );
                                break;
                        }
                        break;
                    case "radio":
                        switch (this.param1)
                        {
                            case "value_select":
                                var values = this.tag_trigger2;
                                for (var count = 0; count < values.length; count++)
                                {
                                    $(script_zone).on("click", "#script_div #" + this.tag_trigger + " input[value='" + values[count] + "']", function()//atribuir os ons a cada value
                                    {

                                        rules_work(data[index]);
                                    }
                                    );
                                }
                                break;
                        }
                        break;
                    case "checkbox":
                        switch (this.param1)
                        {
                            case "value_select":
                                var values = this.tag_trigger2;
                                for (var count = 0; count < values.length; count++)
                                {
                                    $(script_zone).on("click", "#script_div #" + this.tag_trigger + " input[value='" + values[count] + "']", function()//atribuir os ons a cada value
                                    {
                                        rules_work(data[index]);
                                    }
                                    );
                                }
                                break;
                        }
                        break;
                    case"multichoice":
                        switch (this.param1)
                        {
                            case "value_select":
                                var values = this.tag_trigger2;
                                for (var count = 0; count < values.length; count++)
                                {
                                    $(script_zone).on("change", "#script_div #" + this.tag_trigger, function()//atribuir os ons a cada value
                                    {
                                        if (data[index].tag_trigger2.indexOf($("#script_div #" + data[index].tag_trigger + " option:selected").val()) > -1)
                                            rules_work(data[index]);
                                    });
                                }
                                break;
                        }
                        break;
                    case "tableradio":
                        switch (this.param1)
                        {
                            case "value_select":
                                var linhas = this.tag_trigger2;
                                for (var count = 0; count < linhas.length; count++)
                                {
                                    var values = linhas[count].split(";");
                                    $(script_zone).on("click", "#script_div #" + this.tag_trigger + " tr:contains('" + values[0] + "') input[value='" + values[1] + "']", function()
                                    {
                                        rules_work(data[index]);
                                    }
                                    );
                                }
                                break;
                            case "answer":
                                $(script_zone).on("click", "#script_div #" + this.tag_trigger + " input", function()
                                {
                                    if (script_zone.find("#script_div #" + data[index].tag_trigger).find("input:checked").length === (script_zone.find("#script_div #" + data[index].tag_trigger + " .tr_body").find("tr").length))
                                        rules_work(data[index]);
                                });
                                break;
                        }
                        break;
                    case "tableinput":
                        if (this.param1 == "answer")
                        {
                            $(script_zone).on("focusout", "#script_div #" + this.tag_trigger + " input", function()
                            {
                                if (script_zone.find("#script_div #" + data[index].tag_trigger).find("input[value!='']").length === (script_zone.find("#script_div #" + data[index].tag_trigger + " .tr_body").find("td").find("input").length))
                                    rules_work(data[index]);
                            });
                        }
                        break;
                    case "datepicker":
                        switch (this.param1)
                        {
                            case "answer":
                                $(script_zone).on("change", "#script_div #" + this.tag_trigger, function()//atribuir os ons a cada value
                                {
                                    rules_work(data[index]);
                                }
                                );
                                break;
                            case "date":
                                $(script_zone).on("change", "#script_div #" + this.tag_trigger + " .form_datetime", function()//atribuir os ons a cada value
                                {
                                    var temp = data[index];
                                    if (temp.param2.type == "fixed") {
                                        var tempo1, tempo2;
                                        time1 = moment(temp.param2.data_inicial);
                                        time2 = moment(temp.param2.data_final);
                                        if (temp.param2.data_inicial != "" && temp.param2.data_final != "")
                                        {

                                            if ((time1.isBefore($(this).val()) || time1.isSame($(this).val())) && (time2.isAfter($(this).val()) || time2.isSame($(this).val())))
                                                rules_work(data[index]);
                                        }
                                        else
                                        {
                                            if (temp.param2.data_inicial != "")
                                                if (time1.isBefore($(this).val()) || time1.isSame($(this).val()))
                                                    rules_work(data[index]);
                                            if (temp.param2.data_final != "")
                                                if (time2.isAfter($(this).val()) || time2.isSame($(this).val()))
                                                    rules_work(data[index]);
                                        }
                                    }
                                    else
                                    {
                                        var tempo1 = temp.param2.data_inicial.split("|");
                                        var time1 = moment();
                                        var tempo2 = temp.param2.data_final.split("|");
                                        var time2 = moment();
                                        if (temp.param2.data_inicial != "#|#|#|#")
                                        {
                                            if (tempo1[0] != "#")
                                                time1.add('year', tempo1[0]);
                                            if (tempo1[1] != "#")
                                                time1.add('month', tempo1[1]);
                                            if (tempo1[2] != "#")
                                                time1.add('day', tempo1[2]);
                                            if (tempo1[3] != "#")
                                                time1.add('hour', tempo1[3]);
                                        }
                                        if (temp.param2.data_final != "#|#|#|#")
                                        {
                                            if (tempo2[0] != "#")
                                                time2.add('year', tempo2[0]);
                                            if (tempo2[1] != "#")
                                                time2.add('month', tempo2[1]);
                                            if (tempo2[2] != "#")
                                                time2.add('day', tempo2[2]);
                                            if (tempo2[3] != "#")
                                                time2.add('hour', tempo2[3]);
                                        }

                                        if (temp.param2.data_inicial != "#|#|#|#" && temp.param2.data_final != "#|#|#|#")
                                        {
                                            if ((time1.isBefore($(this).val()) || time1.isSame($(this).val())) && (time2.isAfter($(this).val()) || time2.isSame($(this).val())))
                                                rules_work(data[index]);
                                        }
                                        else
                                        {
                                            if (temp.param2.data_inicial != "#|#|#|#")
                                            {

                                                if (time1.isBefore($(this).val()) || time1.isSame($(this).val()))
                                                    rules_work(data[index]);
                                            }
                                            if (temp.param2.data_final != "#|#|#|#")
                                            {
                                                if (time2.isAfter($(this).val()) || time2.isSame($(this).val()))
                                                    rules_work(data[index]);
                                            }
                                        }
                                    }
                                });
                                break;
                        }
                        break;
                    case "textarea":
                        switch (this.param1)
                        {
                            case "answer":
                                $(script_zone).on("focusout", "#script_div #" + this.tag_trigger, function()//atribuir os ons a cada value
                                {
                                    rules_work(data[index]);
                                }
                                );
                                break;
                        }
                        break;
                    case "button":
                        $(script_zone).on("click", "#script_div #" + this.tag_trigger, function()//atribuir os ons a cada value
                        {
                            rules_work(data[index]);
                        }
                        );
                        break;
                }
            });
            if (typeof callback === "function")
            {
                callback();
            }

        }, "json");
    }
    function tags(callback)
    {
        $.each(script_zone.find(".tagReplace"), function()
        {
            var this_label = $(this);
            $(script_zone).on("change", "#script_div #" + this_label.data("id") + " input,#" + this_label.data("id") + " select", function() {
                $("." + this_label.data("id") + "tag").text($(this).val());
            });
        });
        if (typeof callback === "function")
        {
            callback();
        }
    }
//FORM MANIPULATION
    $(script_zone).on("submit", "#script_form", function(e)
    {
        e.preventDefault();
    });
    $('html').bind('keypress', function(e)
    {
        if (e.keyCode == 13)
        {
            return false;
        }
    });
    this.submit_manual = function(callback)
    {
        $.post(file_path + "requests.php", {action: "save_form_result", "save_overwrite": me.config.save_overwrite, id_script: me.script_id, results: $("#script_form").serializeArray(), user_id: me.user_id, unique_id: me.unique_id, campaign_id: me.campaign_id, lead_id: me.lead_id, admin_review: me.admin_review},
        function() {
            admin_review = 0;
            if (typeof callback === "function")
            {
                callback();
            }
        }, "json").fail(function() {
            console.log("FAIL saving data because-->id_script-" + me.script_id + "|user_id->" + me.user_id + "|unique_id->" + me.unique_id + "|campaign_id->" + me.campaign_id + "|lead_id->" + me.lead_id + "|admin_review->" + me.admin_review);
        });
    };
    this.validate_manual = function(validado, nao_validado)
    {
        me.validado_function = validado;
        me.nao_validado_function = nao_validado;
        script_zone.find("#script_form").submit();
    };
    $(script_zone).on("click", ".botao", function(e)
    {
        e.preventDefault();
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
};
// VALIDATION ENGINE ESPECIFIC RULES
function checknif(field, rules, i, options) {

    var nif = field.val();
    var c;
    var checkDigit = 0;
    if (nif != null && nif.length == 9) {
        c = nif.charAt(0);
        if (c == '1' || c == '2' || c == '5' || c == '6' || c == '8' || c == '9') {
            checkDigit = c * 9;
            for (i = 2; i <= 8; i++) {
                checkDigit += nif.charAt(i - 1) * (10 - i);
            }
            checkDigit = 11 - (checkDigit % 11);
            if (checkDigit >= 10) {
                checkDigit = 0;
            }
            if (checkDigit !== parseInt(nif.charAt(8))) {

                return "Introduza um NIF correto";
            }
        }
        else
            return "Introduza um NIF correto";
    }
    else
        return "Introduza um NIF correto";
}

function checknib(field, rules, i, options) {
    if (field.val().match(/^\d+$/))
    {
        var pin_nib = field.val();
        var w_dig_controlo = pin_nib.substr(19, 2) * 1;
        var w_total = 0;
        for (w_index = 0; w_index <= 18; w_index++) {
            var w_digito = pin_nib.substr(w_index, 1) * 1;
            w_total = ((w_total + w_digito) * 10) % 97;
        }
        w_total = 98 - ((w_total * 10) % 97);
        if (w_total !== w_dig_controlo) {
            return "Introduza um NIB correto";
        }
    }
    else
        return "Introduza um NIB correto";
}

function scheduler_verif(field, rules, i, options) {
    var live = parseInt(field.data("live_marc"));
    var obrig = parseInt(field.data("obrig_marc"));
    if (live < obrig)
    {
        if (obrig - live == 1)
            return "Falta 1 marcação";
        else
            return "Faltam " + (obrig - live) + " marcações";
    }
}

function isValidCard(cardNumber) {


    var ccard = new Array(cardNumber.length);
    var i = 0;
    var sum = 0;
    // 6 digit is issuer identifier
    // 1 last digit is check digit
    // most card number > 11 digit
    if (cardNumber.length < 11) {
        return false;
    }
// Init Array with Credit Card Number
    for (i = 0; i < cardNumber.length; i++) {
        ccard[i] = parseInt(cardNumber.charAt(i));
    }
// Run step 1-5 above above
    for (i = 0; i < cardNumber.length; i = i + 2) {
        ccard[i] = ccard[i] * 2;
        if (ccard[i] > 9) {
            ccard[i] = ccard[i] - 9;
        }
    }
    for (i = 0; i < cardNumber.length; i++) {
        sum = sum + ccard[i];
    }
    return ((sum % 10) == 0);
}

function isValidVISA(field, rules, i, options) {
    cardNumber = field.val();
    if (cardNumber.charAt(0) == '4' && (cardNumber.length == 13 || cardNumber.length == 16)) {
        if (!isValidCard(cardNumber))
            return "Nº de Cartão invalido";
    }
    else
        return "Insira um número de cartão Visa Válido";
}

function isValidMastercard(field, rules, i, options) {
    cardNumber = field.val();
    if (cardNumber.charAt(0) == '5' && (cardNumber.charAt(1) == '1' || cardNumber.charAt(1) == '5') && cardNumber.length == 16) {
        if (!isValidCard(cardNumber))
            return "Nº de Cartão invalido";
    }
    else
        return "Insira um número de cartão Mastercard Válido";
}

function isValidDebit(field, rules, i, options) {
    cardNumber = field.val();
    if (!isValidCard(cardNumber))
        return "Nº de Cartão de Débito invalido";
}


