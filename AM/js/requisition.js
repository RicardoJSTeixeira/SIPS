
var requisition1;
$(function()
{
    requisition1 = new requisition($("#main_requisition"));

    requisition1.init();
    requisition1.get_current_requisitions($("#view_requisition_datatable"), 0);

    config = new Object();
    product = new products(config);
    product.init_to_datatable($("#view_product_datatable"));


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