$(function() {

    var init = function(data) {
        var sch;
        console.log($("#calendar_client_modal"));
        sch = new calendar($("#calendar_day"), data,$("#calendar_client_modal"), $('#ext-events'));
        sch.reserveConstruct(data.tipo);
        sch.initModal();

    };
    $.post("ajax/calendar.php",
            {init: true, dash: true},
    init, "json");

    //LISTAR CONSULTAS ABERTAS,FECHADAS
    var allTable = $('#table_allm').dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/dashboard.php',
        "fnServerParams": function(aoData) {
            aoData.push(
                    {"name": "action", "value": "populate_allm"},
            {"name": "id_user"});
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
            aoData.push(
                    {"name": "action", "value": "populate_ncsm"},
            {"name": "id_user"});
        },
        "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Nome"}, {"sTitle": "Morada"}, {"sTitle": "Data"}],
        "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"}
    });

    $.post("ajax/dashboard.php", {action: "populate_mp"}, function(data) {
        $("#table_tbody_mp").empty();
        var temp = "";
        $.each(data, function()
        {
            if (moment().diff(this.start_date, "days") > 7)//Verificar se a data das marcaçoes pendentes excede 1semana/7dias
                temp += "<tr class='error'><td>" + this.first_name + "</td><td>" + moment().from(this.start_date, true) + "</td></tr>";
            else
                temp += "<tr><td>" + this.first_name + "</td><td>" + moment().from(this.start_date, true) + "</td></tr>";
        });
        $("#table_tbody_mp").append(temp);
    }, "json");

    $("#div_master").on("click", ".criar_marcacao", function()
    {
        $("#div_master").hide();
        $("#div_calendar")
                .load("view/calendar.html")
                .show()
                .data().lead_id = $(this).data().lead_id;
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
        var config = new Object();
        config.mensal = false;
        requisition1 = new requisition($("#div_requisition"), config);

        requisition1.init();

        requisition1.new_requisition_destroy($("#new_requisiton_modal .modal-body"));
        requisition1.new_requisition($("#new_requisiton_modal .modal-body"), 0, $(this).data().lead_id);

        $("#new_requisiton_modal").modal("show");
    });

});
 