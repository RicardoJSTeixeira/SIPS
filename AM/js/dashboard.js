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
       columns_allm=[{"sTitle": "ID", "sWidth": "70px"}, {"sTitle": "Nome"}, {"sTitle": "Cod. Ref.", "sWidth": "70px"}, {"sTitle": "Cod. Cliente", "sWidth": "70px"}, {"sTitle": "Nif", "sWidth": "70px"}, {"sTitle": "Postal", "sWidth": "70px"}, {"sTitle": "Morada"}, {"sTitle": "Data", "sWidth": "400px"}],
       columns_ncsm=[{"sTitle": "ID", "sWidth": "70px"}, {"sTitle": "Nome"}, {"sTitle": "Cod. Ref.", "sWidth": "70px"}, {"sTitle": "Cod. Cliente", "sWidth": "70px"}, {"sTitle": "Nif", "sWidth": "70px"}, {"sTitle": "Postal", "sWidth": "70px"}, {"sTitle": "Morada"}, {"sTitle": "Data", "sWidth": "220px"}];
        
        if(user.user_level>5){
            columns_allm.push({"sTitle": "User", "sWidth": "70px"});
            columns_ncsm.push({"sTitle": "User", "sWidth": "70px"});
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

    $('#table_ncsm_toissue').dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/dashboard.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "populate_ncsm_toissue"});
        },
        "aoColumns": columns_ncsm,
        "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
    });
        
    $('#table_ncsm_nottoissue').dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/dashboard.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "populate_ncsm_nottoissue"});
        },
        "aoColumns": columns_ncsm,
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
            temp += "<tr " + ((moment().diff(this.start_date, "days") > 7) ? "class='error'" : "") + "><td>" + this.first_name + "</td><td>" + moment().from(this.start_date, true) + "</td></tr>";
        });
        t.append(temp);
    }, "json");

    $("#div_master").on("click", ".criar_marcacao", function()
    {
        var en = btoa($(this).data().lead_id);
        $.history.push("view/calendar.html?id=" + en);
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

});