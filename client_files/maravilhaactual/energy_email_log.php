<?
#HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
 ?>



<?
$data = date('Y-m-d');
    
    if (isset($_POST['data'])) { $data = $_POST['data']; } else { $data = date('Y-m-d'); }
    $datafim = date("Y-m-d", strtotime("+1 day".$_POST['data']));

    
    ?>


<div class="cc-mstyle">
<table border=0>
<tr>
<td id='icon32'><img src='energy_icon.png' /></td>
<td id='submenu-title'>Reuniões Energy</td>

<form name="report" action="energy_reunioes.php" target="_self" method="post">
<td style="width:25%">&nbsp;</td>
<td style="text-align:right">Escolha o dia:</td>
<td style="text-align:left"><input type="text" readonly name='data' id='data' value='<? echo $data; ?>' style="width:169px; text-align:center" />
</td>
</form>
</tr>
</table>
</div>



<br />
<br />

<div style="width:90%; margin:0 auto;">
<table id='email-list'>
<thead></thead>
<tbody></tbody>
<tfoot></tfoot>
</table>
<br><br>
</div>


</div>
<style>
.dt-column-center { text-align:center;}
</style>
<script>


$(function(){
$("#data").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"yy-mm-dd",
            showOn: "button",
            buttonImage: "/images/icons/date_32.png",
            buttonImageOnly: true});
});
    var dates = "<?php echo $data; ?>"
    var oTable = $('#email-list').dataTable( {
        "aaSorting": [[0, 'asc']],
        "iDisplayLength": 50,
        "sDom": '<"top">rt<"bottom"><"cont">',
        "bSortClasses": false,
        "bJQueryUI": true,  
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": '_requests.php',
        "fnServerParams": function (aoData) 
        {
            aoData.push( 
                        { "name": "action", "value": "get_email_list" },
                        { "name": "sent_campaign_id", "value": "<?php echo $campaign_id; ?>" },
                        { "name": "sent_data", "value": dates }
                       )
        },
        "aoColumns": [ 
                        { "sTitle": "Campanha", "sWidth":"16px", "sClass":"dt-column-main" },
                        { "sTitle": "Operador", "sWidth":"32px", "sClass":"dt-column-center" }, 
                        { "sTitle": "Data de Envio", "sWidth": "256px", "sClass": "dt-column-center", "sType" : "string" },
                        { "sTitle": "Cliente", "sWidth": "16px", "sClass": "dt-column-center", "sType" : "string" },
                        { "sTitle": "Email Cliente", "sWidth": "78px", "sClass": "dt-column-center", "sType" : "string" },
                        { "sTitle": "Envio", "sWidth": "16px", "sClass": "dt-column-center", "sType" : "string" }
                                                      
                     ], 
        "oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
        "fnDrawCallback": function(oSettings, json){ $('#email-list').css({"width":"100%"}) }
        });

$('#data').change(function(){ 
    oTable.fnClearTable(0); 
    dates = $("#data").val();
    oTable.fnReloadAjax();  
});





</script>
</body>
</html>
