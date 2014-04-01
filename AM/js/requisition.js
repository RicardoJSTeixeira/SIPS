
var requisition1;
$(function()
{
    var config = new Object();
    requisition1 = new requisition($("#extra_div"), config);
    requisition1.init(function()
    {
        requisition1.get_current_requisitions($("#view_requisition_datatable"), 0);
    });


  

    var config = new Object();
    config.product_editable = false;
    product = new products($("#extra_div1"), config);
    product.init(function() {
        product.init_to_datatable($("#view_product_datatable"));
    });



});


$("#button_new_requisition").click(function()
{
    requisition1.new_requisition($("#new_requisition_div"), null);
    $("#new_requisition_modal").modal("show");
});