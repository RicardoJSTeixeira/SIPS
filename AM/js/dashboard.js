$(function() {
    var init = function(data, user) {
        var
                sch,
                modals = {};

        modals.client = $("#calendar_client_modal");
        modals.special = $("#calendar_special_event");
        modals.mkt = $("#calendar_mkt");
        modals.acf = $("#calendar_acf");
        data.config.height = "488";

        sch = new calendar($("#calendar_day"), data, modals, $('#ext-events'), {}, user);
        sch.reserveConstruct(data.tipo);
        sch.initModal();


        //LISTAR CONSULTAS ABERTAS,FECHADAS
        var
                columns_allm = [{"sTitle": "ID", "sWidth": "50px", bVisible: false}, {"sTitle": "Cod. Mkt.", "sWidth": "70px"}, {"sTitle": "Ref. Cliente", "sWidth": "70px"}, {"sTitle": "Nif", "sWidth": "63px"}, {"sTitle": "Nome"}, {"sTitle": "Morada"}, {"sTitle": "Cod. Postal", "sWidth": "55px"}, {"sTitle": "Localidade", "sWidth": "70px"}, {"sTitle": "Telefone 1", "sWidth": "64px"}, {"sTitle": "Telefone 2", "sWidth": "64px"}, {"sTitle": "Estado", "sWidth": "50px"}, {"sTitle": "Data", "sWidth": "120px"}, {"sTitle": "Resultado Consulta", "sWidth": "65px"}, {"sTitle": "Feedback sem Consulta", "sWidth": "65px"}, {"sTitle": "Feedback sem venda", "sWidth": "65px"}, {"sTitle": "User", "sWidth": "150px"}],
                columns_ncsm = [{"sTitle": "ID", "sWidth": "50px", bVisible: false}, {"sTitle": "Cod. Mkt.", "sWidth": "70px"}, {"sTitle": "Ref. Cliente", "sWidth": "70px"}, {"sTitle": "Nif", "sWidth": "63px"}, {"sTitle": "Nome"}, {"sTitle": "Morada"}, {"sTitle": "Cod. Postal", "sWidth": "55px"}, {"sTitle": "Localidade", "sWidth": "70px"}, {"sTitle": "Telefone 1", "sWidth": "64px"}, {"sTitle": "Telefone 2", "sWidth": "64px"}, {"sTitle": "Data", "sWidth": "240px"}, {"sTitle": "User", "sWidth": "70px"}];
        //columns_ncsm_r = [{"sTitle": "Nome Recomendador", "sWidth": "140px", bVisible: false}, {"sTitle": "Cod. Mkt. Rec.", "sWidth": "70px"}, {"sTitle": "ID", "sWidth": "50px"}, {"sTitle": "Nome"}, {"sTitle": "Cod. Mkt.", "sWidth": "70px"}, {"sTitle": "Referência", "sWidth": "70px"}, {"sTitle": "Nif", "sWidth": "63px"}, {"sTitle": "Postal", "sWidth": "55px"}, {"sTitle": "Localidade", "sWidth": "70px"}, {"sTitle": "Telefone 1", "sWidth": "64px"}, {"sTitle": "Telefone 2", "sWidth": "64px"}, {"sTitle": "Morada"}, {"sTitle": "Data", "sWidth": "240px"},{"sTitle": "Dispenser", "sWidth": "70px"}];


        $('#table_allm').dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": 'ajax/dashboard.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "populate_allm"});
            },
            "aoColumns": columns_allm,
            "fnDrawCallback": function(oSettings, json) {
                $('#table_allm').show();
            },
            "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
        });

        $('#table_ncsm').dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": 'ajax/dashboard.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "populate_ncsm"});
            },
            "aoColumns": columns_ncsm,
            "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
        });

        /*
         $('#table_ncsm_r').dataTable({
         "bSortClasses": false,
         "bProcessing": true,
         "bDestroy": true,
         "sPaginationType": "full_numbers",
         "sAjaxSource": 'ajax/dashboard.php',
         "fnServerParams": function(aoData) {
         aoData.push({"name": "action", "value": "populate_ncsm_r"});
         },
         "aoColumns": columns_ncsm_r,
         "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
         });
         */

    };

    $.post("/AM/ajax/calendar.php",
            {action: "dashboardInit"},
    function(data) {
        init(data, SpiceU);
    }, "json");


    $.post("ajax/dashboard.php", {action: "populate_mp"}, function(data) {
        var
                t = $("#table_tbody_mp").empty(),
                temp = "",
                v6 = 0,
                v3 = 0,
                isV6 = false;
        $.each(data, function() {
            if (isV6 = (moment().diff(this.start_date, "days") > 6)) {//!!é mesmo isto que se quer não é erro!!
                v6++;
            }
            if ((moment().diff(this.start_date, "days") > 3)) {//!!é mesmo isto que se quer não é erro!!
                v3++;
            }
            temp += "<tr " + ((isV6) ? "class='error'" : "") + ">\n\
                        <td>" + this.first_name + "</td>\n\
                        <td>" + moment().from(this.start_date, true) + " <div class='view-button'>\n\
                            <button class='btn btn-mini icon-alone initC' data-cid='" + this.lead_id + "' data-rid='" + this.id_reservation + "' data-toggle='tooltip' title='Iniciar Consulta'><i class='icon-share-alt'></i></button>\n\
                            <button class='btn btn-mini icon-alone initSC' data-cid='" + this.lead_id + "' data-rid='" + this.id_reservation + "' data-toggle='tooltip' title='Sem Consulta'><i class='icon-minus-sign'></i></button></div>\n\
                        </td>\n\
                    </tr>";
        });
        t.append(temp)
                .find(".initSC")
                .popover({
                    placement: "left",
                    html: true,
                    title: "Não há consulta",
                    content: '<form  id="no_consult_confirm">\n\
                                <select id="select_no_consult" class="validate[required]">\n\
                                    <option value="">Seleccione um opção</option>\n\
                                    <option value="DEST">Desistiu</option>\n\
                                    <option value="FAL">Faleceu</option>\n\
                                    <option value="TINV">Telefone Invalido</option>\n\
                                    <option value="NOSHOW">No Show</option>\n\
                                    <option value="NAT">Ninguém em casa</option>\n\
                                    <option value="MOR">Morada Errada</option>\n\
                                    <option value="NTEC">Técnico não foi</option>\n\
                                </select>\n\
                                <button class="btn btn-primary">Fechar</button>\n\
                            </form>',
                    trigger: 'click'
                })
                .end()
                .on("submit", "#no_consult_confirm", function(e)
                {
                    e.preventDefault();
                    var
                            that = $(this),
                            clientData = that.closest('.view-button').find('.initSC').data(),
                            cResult = that.find("#select_no_consult").val();
                    if (that.validationEngine('validate')) {
                        $.post("/AM/ajax/consulta.php",
                                {
                                    action: "insert_consulta",
                                    reserva_id: clientData.rid,
                                    lead_id: clientData.cid,
                                    closed: 1,
                                    consulta: 0,
                                    consulta_razao: cResult,
                                    exame: "0",
                                    exame_razao: "",
                                    venda: 0,
                                    venda_razao: "",
                                    left_ear: 0,
                                    right_ear: 0,
                                    tipo_aparelho: "",
                                    produtos: "",
                                    descricao_aparelho: "",
                                    feedback: "SCONS"
                                },
                        function() {
                            $.jGrowl('Consulta fechada com sucesso!');
                            dropOneConsult();
                            that.closest('tr').remove();
                        }
                        , "json");
                    }
                })
                .find('[data-toggle~="tooltip"]').tooltip({container: 'body'});
        //localStorage.v6 = v6;
        //localStorage.v3 = v3;
        consultasMais();
    }, "json");

    $("#div_master").on("click", ".criar_marcacao", function()
    {
        var en = btoa($(this).data().lead_id);
        $.history.push("view/calendar.html?id=" + en);
    });
    $("#div_master").on("click", ".recomendacoes", function()
    {
        var en = btoa($(this).data().lead_id);
        $.history.push("view/mass_client.html?id=" + en);
    });

    $("#div_master").on("click", ".ver_cliente", function()
    {
        var client = new cliente_info($(this).data("lead_id"), null);
        client.init(null);

    });

    $("#div_master").on("click", ".criar_encomenda", function()
    {
        var
                data = $(this).data(),
                en = btoa(data.lead_id);
        $.history.push("view/new_requisition.html?id=" + en);
    });

    $("#table_tbody_mp").on('click', '.initC', function() {
        var
                data = $(this).data(),
                en = btoa(data.cid),
                rs = btoa(data.rid);
        $(this).tooltip('destroy');
        $.history.push("view/consulta.html?id=" + encodeURIComponent(en) + "&rs=" + encodeURIComponent(rs));
    });

});
