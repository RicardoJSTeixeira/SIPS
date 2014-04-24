$(function() {
    var init = function(data, user) {
        var
                sch,
                modals = {};

        modals.client = $("#calendar_client_modal");
        modals.special = $("#calendar_special_event");

        data.config.height = "488";

        sch = new calendar($("#calendar_day"), data, modals, $('#ext-events'), {}, user);
        sch.reserveConstruct(data.tipo);
        sch.initModal();


        //LISTAR CONSULTAS ABERTAS,FECHADAS
        var
                columns_allm = [{"sTitle": "ID", "sWidth": "70px"}, {"sTitle": "Nome"}, {"sTitle": "Cod. Ref.", "sWidth": "70px"}, {"sTitle": "Cod. Cliente", "sWidth": "70px"}, {"sTitle": "Nif", "sWidth": "70px"}, {"sTitle": "Postal", "sWidth": "70px"}, {"sTitle": "Morada"}, {"sTitle": "Data", "sWidth": "270px"}],
                columns_ncsm = [{"sTitle": "ID", "sWidth": "70px"}, {"sTitle": "Nome"}, {"sTitle": "Cod. Ref.", "sWidth": "70px"}, {"sTitle": "Cod. Cliente", "sWidth": "70px"}, {"sTitle": "Nif", "sWidth": "70px"}, {"sTitle": "Postal", "sWidth": "70px"}, {"sTitle": "Morada"}, {"sTitle": "Data", "sWidth": "240px"}],
                columns_ncsm_r = [{"sTitle": "Nome Recomendador", "sWidth": "140px"},{"sTitle": "Cod. Ref. Rec.", "sWidth": "70px"},{"sTitle": "ID", "sWidth": "70px"}, {"sTitle": "Nome"}, {"sTitle": "Cod. Ref.", "sWidth": "70px"}, {"sTitle": "Cod. Cliente", "sWidth": "70px"}, {"sTitle": "Nif", "sWidth": "70px"}, {"sTitle": "Postal", "sWidth": "70px"}, {"sTitle": "Morada"}, {"sTitle": "Data", "sWidth": "240px"}];

        if (user.user_level > 5) {
            columns_allm.push({"sTitle": "User", "sWidth": "70px"});
            columns_ncsm.push({"sTitle": "User", "sWidth": "70px"});
            columns_ncsm_r.push({"sTitle": "User", "sWidth": "70px"});
        }
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


    };

    $.post("/AM/ajax/calendar.php",
            {action: "dashboardInit"},
    function(data) {
        init(data, SpiceU);
    }, "json");


    $.post("ajax/dashboard.php", {action: "populate_mp"}, function(data) {
        var
                t = $("#table_tbody_mp").empty(),
                temp = "";
        $.each(data, function() {
            temp += "<tr " + ((moment().diff(this.start_date, "days") > 7) ? "class='error'" : "") + "><td>" + this.first_name + "</td><td>" + moment().from(this.start_date, true) + " <div class='view-button'><button class='btn btn-mini icon-alone initC' data-cid='" + this.lead_id + "' data-rid='" + this.id_reservation + "' title='Iniciar Consulta'><i class='icon-share-alt'></i></button></div></td></tr>";
        });
        t.append(temp);
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

        var cliente = new crm_edit($("#cliente_modal .modal-body"), "/sips-admin/crm/", $(this).data("lead_id"));
        cliente.destroy();
        cliente.init();
        $("#cliente_modal").modal("show");
    });

    $("#div_master").on("click", ".criar_encomenda", function()
    {
        var config = new Object(), m;
        config.mensal = false;
        requisition1 = new requisition(config);

        m = $("#new_requisiton_modal");
        requisition1.new_requisition(m.find(".modal-body"), 0, $(this).data().lead_id);

        m.modal("show");
    });

    $("#table_tbody_mp").on('click', '.initC', function() {
        var
                data = $(this).data(),
                en = btoa(data.cid),
                rs = btoa(data.id);
        $.history.push("view/consulta.html?id=" + encodeURIComponent(en) + "&rs=" + encodeURIComponent(rs));
    });

});