var requisition1;
$(function()
{
    requisition1 = new requisition($("#extra_div"), {});
    requisition1.init(function()
    {
        requisition1.get_current_requisitions($("#view_requisition_datatable"), 0);
    });
});

$("#button_new_requisition").click(function()
{
    $.history.push("view/new_requisition.html");
});