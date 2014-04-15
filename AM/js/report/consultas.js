$(function() {
    var table_consults = $("#table_consults").dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sAjaxSource": '/AM/ajax/report/consultas.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "populate_consults"});
        },
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
    
    
    $('#export_btn').click(function(event) {
        event.preventDefault();
        table2csv(table_consults, 'full', '#table_consults');
    });
});