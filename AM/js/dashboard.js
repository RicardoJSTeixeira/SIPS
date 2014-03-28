$(function() {
    var init = function(data) {
        var sch;

        sch = new calendar($("#calendar_day"), data, $("#calendar_client_modal"), $('#ext-events'));
        sch.reserveConstruct(data.tipo);
        sch.initModal();
    };
    $.post("ajax/calendar.php",
            {action: "dashboardInit"},
    init, "json");

    //LISTAR CONSULTAS ABERTAS,FECHADAS
    var allTable = $('#table_allm').dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/dashboard.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "populate_allm"});
        },
        "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Nome"}, {"sTitle": "Morada"}, {"sTitle": "Data"}],
        "fnDrawCallback": function(oSettings, json) {
            $('#table_allm').show();
        },
        "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
    });

    var ncsmTable = $('#table_ncsm').dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/dashboard.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "populate_ncsm"});
        },
        "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Nome"}, {"sTitle": "Morada"}, {"sTitle": "Data"}],
        "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
    });

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
        var en=btoa($(this).data().lead_id);
        $.history.push("view/calendar.html?id="+en);
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
 