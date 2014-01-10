$(function()
{
    $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
});





// PRODUTOS
$("#view_product_button").click(function()
{
    if ($("#view_product_div").is(":visible"))
    {
        $("#view_product_div").hide("blind");
    }
    else
    {
        $("#view_product_div").show("blind");
            var Table_view_product = $('#view_product_datatable').dataTable({
        "aaSorting": [[0, "desc"]],
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/admin.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "listar_produtos"});
        },
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Parent"}, {"sTitle": "Alone"}, {"sTitle": "Max requisições mês"}, {"sTitle": "Max requisições semana"}, {"sTitle": "Categoria"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
    }





});
$("#add_product_button").click(function()
{
    $("#add_product_div").toggle("blind");
});
$("#remove_product_button").click(function()
{
    $("#remove_product_div").toggle("blind");
});

$("#edit_product_button").click(function()
{
    $("#edit_product_div").toggle("blind");
});

// CLIENTES
// relatorio de clientes sem marcação
$("#download_csm_button").click(function()
{
    $("#download_csm_div").toggle("blind");
});
$("#download_excel_csm_button").click(function()
{
    if ($("#date_form").validationEngine("validate"))
    {
        document.location.href = "ajax/admin.php?action=download_excel_csm";
    }
});
  