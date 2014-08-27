/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
    var table_consults = $("#table_audiograma").dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sAjaxSource": '/AM/ajax/report/audiograma.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "populate_consults"});
        },
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
    
    
    $('#export_btn').click(function(event) {
        event.preventDefault();
        table2csv(table_consults, 'view', '#table_audiograma');
    });
});
