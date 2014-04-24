var requests = function(basic_path, options_ext)
{
    var me = this;
    this.basic_path = basic_path;
    this.config = {};
    $.extend(true, this.config, options_ext);


    this.init_apoio_marketing = function()
    {
        $.get("/AM/view/requests/apoio_marketing_modal.html", function(data) {
            me.basic_path.append(data);
        });
    };

    this.init_relatorio_correio = function()
    {
        $.get("/AM/view/requests/relatorio_correio_modal.html", function(data) {
            me.basic_path.append(data);
        });
    };

    this.init_relatorio_frota = function()
    {
        $.get("/AM/view/requests/relatorio_frota_modal.html", function(data) {
            me.basic_path.append(data);
        });
    };

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------APOIO MARKETING--------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    this.new_apoio_marketing = function(am_zone)
    {
        var ldpinput_count = 1;
        am_zone.empty().off();
        $.get("/AM/view/requests/apoio_marketing.html", function(data) {
            am_zone.append(data);
            $('#apoio_am_form').stepy({backLabel: "Anterior", nextLabel: "Seguinte", next: function() {
                    if (am_zone.find("#apoio_am_form").validationEngine("validate"))
                        return true;
                    else
                        return false;
                }, finishButton: false});
            am_zone.find("#data_rastreio1").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, startDate: moment().format("YYYY-MM-DD")})
                    .on('changeDate', function() {
                        $("#data_rastreio2").datetimepicker('setStartDate', moment($(this).val()).format('YYYY-MM-DD'));
                    });
            am_zone.find("#data_rastreio2").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, startDate: moment().format("YYYY-MM-DD")})
                    .on('changeDate', function() {
                        $("#data_rastreio1").datetimepicker('setEndDate', moment($(this).val()).format('YYYY-MM-DD'));
                    });
                    
            am_zone.find("#data_inicio1").datetimepicker({format: 'hh:ii', autoclose: true, language: "pt",  startView: 1, maxView: 1, startDate: moment().format("YYYY-MM-DD")})
                    .on('changeDate', function() {
                        $("#data_inicio2").datetimepicker('setStartDate', moment().format('YYYY-MM-DDT'+$(this).val()));
                    });
            am_zone.find("#data_inicio2").datetimepicker({format: 'hh:ii', autoclose: true, language: "pt",  startView: 1, maxView: 1, startDate: moment().format("YYYY-MM-DD")})
                    .on('changeDate', function() {
                        $("#data_inicio1").datetimepicker('setEndDate', moment().format('YYYY-MM-DDT'+$(this).val()));
                        $("#data_fim1").datetimepicker('setStartDate', moment().format('YYYY-MM-DDT'+$(this).val()));
                    });
                    
            am_zone.find("#data_fim1").datetimepicker({format: 'hh:ii', autoclose: true, language: "pt",  startView: 1, maxView: 1, startDate: moment().format("YYYY-MM-DD")})
                    .on('changeDate', function() {
                        $("#data_fim2").datetimepicker('setStartDate', moment().format('YYYY-MM-DDT'+$(this).val()));
                    });
            am_zone.find("#data_fim2").datetimepicker({format: 'hh:ii', autoclose: true, language: "pt",  startView: 1, maxView: 1, startDate: moment().format("YYYY-MM-DD")})
                    .on('changeDate', function() {
                        $("#data_fim1").datetimepicker('setEndDate', moment().format('YYYY-MM-DDT'+$(this).val()));
                    });

            //Adiciona Linhas
            am_zone.find("#button_ldptable_add_line").click(function(e)
            {
                e.preventDefault();
                am_zone.find("#table_tbody_ldp").append("<tr><td><input type='text' name='ldp_cp" + ldpinput_count + "' class='linha_cp validate[required,custom[onlyNumberSp]]'></td><td><input type='text' name='ldp_freg" + ldpinput_count + "' class='linha_freg validate[required]'></td><td> <button class='btn btn-danger button_ldptable_remove_line icon-alone' ><i class='icon-minus'></i></button></td></tr>");
                ldpinput_count++;
            }).click();
            //Remove Linhas
            am_zone.on("click", ".button_ldptable_remove_line", function(e)
            {
                e.preventDefault();
                $(this).parent().parent().remove();
            });

            //SUBMIT
            am_zone.on("click", "#submit_am", function(e)
            {
                e.preventDefault();
                if (am_zone.find("#apoio_am_form").validationEngine("validate") && am_zone.find("#table_tbody_ldp tr").length)
                {
                    var local_publicidade_array = [];
                    $.each(am_zone.find("#table_tbody_ldp").find("tr"), function(data)
                    {
                        local_publicidade_array.push({cp: $(this).find(".linha_cp").val(), freguesia: $(this).find(".linha_freg").val()});
                    });
                    $.post("/AM/ajax/requests.php", {action: "criar_apoio_marketing",
                        data_inicial: am_zone.find("#data_rastreio1").val(),
                        data_final: am_zone.find("#data_rastreio2").val(),
                        horario: {
                            inicio1: am_zone.find("#data_inicio1").val(),
                            inicio2: am_zone.find("#data_inicio2").val(),
                            fim1: am_zone.find("#data_fim1").val(),
                            fim2: am_zone.find("#data_fim2").val()},
                        localidade: am_zone.find("#input_localidade").val(),
                        local: am_zone.find("#input_local_rastreio").val(),
                        morada: am_zone.find("#input_morada_rastreio").val(),
                        comments: am_zone.find("#input_observaçoes").val(),
                        local_publicidade: local_publicidade_array},
                    function(data1)
                    {
                        $('#apoio_am_form').stepy('step', 1);
                        am_zone.find(":input").val("");
                        $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                    }, "json");
                }
            });
        });
    };
    this.get_apoio_marketing_to_datatable = function(am_zone)
    {
        var apoio_markting_table = am_zone.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/requests.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_apoio_marketing_to_datatable"}, {"name": "show_admin", "value": 1});
            },
            "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Data pedido"}, {"sTitle": "Data inicial rastreio"}, {"sTitle": "Data final rastreio"}, {"sTitle": "Horario"}, {"sTitle": "Localidade"}, {"sTitle": "Local"}, {"sTitle": "Morada"}, {"sTitle": "Observações"}, {"sTitle": "Local publicidade"}, {"sTitle": "Status"}, {"sTitle": "Opções"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });

        am_zone.on("click", ".ver_horario", function()
        {
            var id = $(this).data().apoio_marketing_id;
            $.post("ajax/requests.php", {action: "get_horario_from_apoio_marketing", id: id}, function(data) {
                me.basic_path.find("#ver_horario_modal #inicio1").text(data[0].inicio1);
                me.basic_path.find("#ver_horario_modal #inicio2").text(data[0].inicio2);
                me.basic_path.find("#ver_horario_modal #fim1").text(data[0].fim1);
                me.basic_path.find("#ver_horario_modal #fim2").text(data[0].fim2);
                me.basic_path.find("#ver_horario_modal").modal("show");
            }, "json");
        });

        am_zone.on("click", ".ver_local_publicidade", function()
        {
            var id = $(this).data().apoio_marketing_id;

            $.post("/AM/ajax/requests.php", {action: "get_locais_publicidade_from_apoio_marketing", id: id}, function(data) {
                me.basic_path.find("#ver_local_publicidade_modal #tbody_ver_local_publicidade").empty();
                $.each(data, function()
                {
                    me.basic_path.find("#ver_local_publicidade_modal #tbody_ver_local_publicidade").append("<tr><td>" + this.cp + "</td><td>" + this.freguesia + "</td></tr>");
                });
                me.basic_path.find("#ver_local_publicidade_modal").modal("show");
            }, "json");
        });


        am_zone.on("click", ".accept_apoio_marketing", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "accept_apoio_marketing", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Aprovado");
                apoio_markting_table.fnReloadAjax();
            }, "json");
        });

        am_zone.on("click", ".decline_apoio_marketing", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "decline_apoio_marketing", id: $(this).val()}, function() {
                this_button.parent().prev().text("Rejeitado");
                apoio_markting_table.fnReloadAjax();
            }, "json");
        });

    };


//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------CORREIO--------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    this.new_relatorio_correio = function(rc_zone)
    {
        rc_zone.empty().off();
        $.get("/AM/view/requests/relatorio_correio.html", function(data) {
            rc_zone.append(data);
            rc_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
            //SUBMIT
            rc_zone.on("click", "#submit_rc", function(e)
            {
                e.preventDefault();
                if (rc_zone.find("#relatorio_correio_form").validationEngine("validate"))
                {
                    var docs_objs = [];
                    $.each(rc_zone.find("#doc_obj_table_tbody tr"), function()
                    {
                        docs_objs.push($(this).find("input").val());
                    });
                    $.post("ajax/requests.php", {action: "criar_relatorio_correio",
                        carta_porte: rc_zone.find("#input_carta_porte").val(),
                        data: rc_zone.find("#data_envio_datetime").val(),
                        doc: rc_zone.find("#input_doc").val(),
                        lead_id: rc_zone.find("#input_lead_id").val(),
                        client_name: rc_zone.find("#input_client_name").val(),
                        input_doc_obj_assoc: docs_objs,
                        comments: rc_zone.find("#input_comments").val().length ? rc_zone.find("#input_comments").val() : "Sem observações"},
                    function(data1)
                    {
                        rc_zone.find(":input").val("");
                        $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                    }, "json");
                }
            });

            rc_zone.find("#add_line_obj_doc").click(function(e)
            {
                e.preventDefault();
                rc_zone.find("#doc_obj_table_tbody").append("<tr><td><input class='input-xlarge validate[required]'  type='text' /></td>    <td><button class='btn btn-danger remove_doc_obj icon-alone'><i class='icon icon-minus'></i></button></td></tr>");
            }).click();

            rc_zone.on("click", ".remove_doc_obj", function(e)
            {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
        });
    };
    this.get_relatorio_correio_to_datatable = function(rc_zone)
    {
        var relatorio_correio_table = rc_zone.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/requests.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_relatorio_correio_to_datatable"},
                {"name": "show_admin", "value": 1}
                );
            },
            "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Carta Porte"}, {"sTitle": "Data Envio"}, {"sTitle": "Documento"}, {"sTitle": "Lead id"}, {"sTitle": "Anexo", "sWidth":"75px"}, {"sTitle": "Observações"}, {"sTitle": "Status"}, {"sTitle": "Opções", "sWidth":"50px"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });

        rc_zone.on("click", ".accept_report_correio", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "accept_report_correio", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Aprovado");
                relatorio_correio_table.fnReloadAjax();
            }, "json");
        });

        rc_zone.on("click", ".decline_report_correio", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "decline_report_correio", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Rejeitado");
                relatorio_correio_table.fnReloadAjax();
            }, "json");
        });
        rc_zone.on("click", ".ver_anexo_correio", function(e)
        {

            e.preventDefault();
            var id_anexo = $(this).data().anexo_id;
            var anexo_number = 1;
            $.post("ajax/requests.php", {action: "get_anexo_correio", id: id_anexo},
            function(data1)
            {
                var tbody = me.basic_path.find("#tbody_ver_anexo_correio");
                tbody.empty();
                $.each(data1, function()
                {

                    tbody.append("<tr><td class='chex-table'><input type='checkbox' id='anexo" + anexo_number + "' name='cci'><label class='checkbox inline' for='anexo" + anexo_number + "'><span></span>  </label></td><td>" + this + "</td></tr>");
                    anexo_number++;
                });

                me.basic_path.find("#ver_anexo_correio_modal").modal("show");
            }, "json");


        });
    };

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------FROTA--------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    this.new_relatorio_frota = function(rf_zone)
    {
        var rfinput_count = 2;
        rf_zone.empty().off();
        $.get("/AM/view/requests/relatorio_frota.html", function(data) {
            rf_zone.append(data);
            rf_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
            rf_zone.find(".rf_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
            //Adiciona Linhas
            rf_zone.find( "#button_rf_table_add_line").click(function(e)
            {
                e.preventDefault();
                rf_zone.find("#table_tbody_rf").append("<tr><td> <input size='16' type='text' name='rf_data" + rfinput_count + "' class='rf_datetime validate[required] linha_data' readonly id='rf_datetime" + rfinput_count + "' placeholder='Data'></td><td><input class='validate[required] linha_ocorrencia' type='text' name='rf_ocorr" + rfinput_count + "'></td><td>  <input class='validate[required] linha_km' type='number' value='0' name='rf_km" + rfinput_count + "' min='0'></td><td><button class='btn btn-danger button_rf_table_remove_line icon-alone'><i class='icon-minus'></i></button></td></tr>");
                rf_zone.find("#rf_datetime" + rfinput_count).datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                rfinput_count++;
            }).click();
            //Remove Linhas
            rf_zone.on("click", ".button_rf_table_remove_line", function(e)
            {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
            //SUBMIT
            rf_zone.on("click", "#submit_rf", function(e)
            {
                e.preventDefault();
                if (rf_zone.find("#relatorio_frota_form").validationEngine("validate"))
                {
                    var ocorrencias_array = [];
                    $.each(rf_zone.find("#table_tbody_rf").find("tr"), function(data)
                    {
                        ocorrencias_array.push(
                                {data: $(this).find(".linha_data").val(),
                                    ocorrencia: $(this).find(".linha_ocorrencia").val(),
                                    km: $(this).find(".linha_km").val()});
                    });
                    $.post("/AM/ajax/requests.php", {action: "criar_relatorio_frota",
                        data: rf_zone.find("#input_data").val(),
                        matricula: rf_zone.find("#input_matricula").val(),
                        km: rf_zone.find("#input_km").val(),
                        viatura: rf_zone.find(":radio[name='rrf']:checked").val(),
                        ocorrencias: ocorrencias_array,
                        comments: rf_zone.find("#input_comments").val().length ? rf_zone.find("#input_comments").val() : ""},
                    function()
                    {
                        rf_zone.find(":input").val("");
                        $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                    }, "json");
                }
            });
        });
    };

    this.get_relatorio_frota_to_datatable = function(rf_zone)
    {
        var relatorio_frota_table = rf_zone.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/requests.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_relatorio_frota_to_datatable"},
                {"name": "show_admin", "value": 1});
            },
            "aoColumns": [{"sTitle": "id"}, {"sTitle": "user"}, {"sTitle": "data"}, {"sTitle": "Matricula"}, {"sTitle": "Km"}, {"sTitle": "Viatura"}, {"sTitle": "Observações"}, {"sTitle": "Ocorrencias", "sWidth":"150px"}, {"sTitle": "Status"}, {"sTitle": "Opções", "sWidth":"50px"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });

        rf_zone.on("click", ".ver_ocorrencias", function()
        {
            var id = $(this).data().relatorio_frota_id;
            $.post("ajax/requests.php", {action: "get_ocorrencias_frota", id: id}, function(data) {

                var tbody = me.basic_path.find("#ver_occorrencia_frota_modal #ver_occorrencia_frota_tbody");
                tbody.empty();
                $.each(data, function()
                {
                    tbody.append("<tr><td>" + this.data + "</td><td>" + this.km + "</td><td>" + this.ocorrencia + "</td></tr>");
                });
                me.basic_path.find("#ver_occorrencia_frota_modal").modal("show");
            }, "json");
        });

        rf_zone.on("click", ".accept_report_frota", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "accept_report_frota", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Aprovado");
                relatorio_frota_table.fnReloadAjax();
            }, "json");
        });

        rf_zone.on("click", ".decline_report_frota", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "decline_report_frota", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Rejeitado");
                relatorio_frota_table.fnReloadAjax();
            }, "json");
        });
    };
};