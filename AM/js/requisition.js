
var requisition1;
$(function()
{
    var config = new Object();
    requisition1 = new requisition(config);
    requisition1.get_current_requisitions($("#view_requisition_datatable"), $("#ver_produto_requisition_modal"), 0);

    config = new Object();
    config.product_editable = false;
    product = new products(config);

    product.init_to_datatable($("#view_product_datatable"), $("#ver_produto_modal"));


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