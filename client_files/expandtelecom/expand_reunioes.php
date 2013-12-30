<?
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");
?>

<?
$data = date('Y-m-d');

//$query1 = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]';", $link)) or die(mysql_error());
$query2 = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='ExpandTelecom';", $link)) or die(mysql_error());
$AllowedCampaigns = "'" . preg_replace("/ /", "','", preg_replace("/ -/", '', $query2['allowed_campaigns'])) . "'";
?>


<div class="cc-mstyle">
    <table border=0>
        <tr>
            <td id='icon32'><img src='/images/icons/to_do_list_32.png' /></td>
            <td id='submenu-title'>Reuniões Expand</td>

        <form name="report" action="expand_reunioes.php" target="_self" method="post">
            <td style="width:25%">&nbsp;</td>
            <td style="text-align:right">Escolha o dia:</td>
            <td style="text-align:left"><input type="text" readonly name='data' id='data' value='<? echo $data; ?>' style="width:169px; text-align:center" />
            </td>
        </form>
        </tr>
    </table>
</div>



<br />

<center>Testem a ferramenta e erros/melhorias que encontrem contactem-nos pelo 219201281! Bom trabalho.</center>
<br />

<br />

<div style="width:90%; margin:0 auto;">
    <table id='sales_list'>
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
    function dadoscli(valor)
    {
        var url = "expand_print.php?id=" + valor;
        window.open(url, '_blank');
    }



    $(function() {
        $("#data").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: "/images/icons/date_32.png",
            buttonImageOnly: true});
    });
    var dates = "<?php echo $data; ?>"
    var oTable = $('#sales_list').dataTable({
        "aaSorting": [[0, 'asc']],
        "iDisplayLength": 50,
        "sDom": '<"top">rt<"bottom"><"cont">',
        "bSortClasses": false,
        "bJQueryUI": true,
        "bProcessing": true,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": '_requests.php',
        "fnServerParams": function(aoData)
        {
            aoData.push(
                    {"name": "action", "value": "get_sales_list"},
            {"name": "sent_campaign_id", "value": "<?php echo $AllowedCampaigns; ?>"},
            {"name": "sent_data", "value": dates}
            )
        },
        "aoColumns": [
            {"sTitle": "ID do Contacto", "sWidth": "16px", "sClass": "dt-column-main"},
            {"sTitle": "Operador", "sWidth": "32px", "sClass": "dt-column-center"},
            {"sTitle": "Cliente", "sWidth": "256px", "sClass": "dt-column-center", "sType": "string"},
            {"sTitle": "Telefone", "sWidth": "16px", "sClass": "dt-column-center", "sType": "string"},
            {"sTitle": "Data da Venda", "sWidth": "78px", "sClass": "dt-column-center", "sType": "string"},
            {"sTitle": "Ver Folha Reunião", "sWidth": "16px", "sClass": "dt-column-center", "sType": "string"}
        ],
        "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"},
        "fnDrawCallback": function(oSettings, json) {
            $('#sales_list').css({"width": "100%"})
        }
    });

    $('#data').change(function() {
        oTable.fnClearTable(0);
        dates = $("#data").val();
        oTable.fnReloadAjax();
    })


</script>
</body>
</html>
