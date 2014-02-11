
var requisition1;
$(function()
{
    requisition1 = new requisition($("#main_requisition"));

    requisition1.init();
    requisition1.get_current_requisitions($("#view_requisition_datatable"), 0);


    var Table_view_product = $('#view_product_datatable').dataTable({
        "aaSorting": [[6, "asc"]],
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/admin.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "listar_produtos_to_datatable"});
        },
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Parente"}, {"sTitle": "Max requisições mensais"}, {"sTitle": "Max requisições especiais"}, {"sTitle": "Categoria"}, {"sTitle": "Tipo"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });

});


$("#button_filtro_new_requisition").click(function()
{
    if (!$("#new_requisition_div").is(":visible"))
    {
        requisition1.new_requisition($("#new_requisition_div"), $("#view_requisition_datatable"));
    }
    $("#new_requisition_div").toggle("blind");
    $(this).toggleClass("icon-chevron-down").toggleClass("icon-chevron-up");
});