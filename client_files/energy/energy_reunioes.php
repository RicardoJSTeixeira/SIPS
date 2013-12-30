<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$PHP_AUTH_USER = $_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW = $_SERVER["PHP_AUTH_PW"];

#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");

$data = date('Y-m-d');

if (isset($_POST['data'])) {
    $data = $_POST['data'];
} else {
    $data = date('Y-m-d');
}
$datafim = date("Y-m-d", strtotime("+1 day" . $_POST['data']));
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SMS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />


        <script type="text/javascript" src="/jquery/jsdatatable/plugins/plugin.fnAjaxReload.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/plugins/datetimepicker.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/language/pt-pt.js"></script>
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css" />

        <style>
            #loader{
                background: #f9f9f9;
                top: 0px;
                left: 0px;
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 2;
            }
            #loader > img{
                position:absolute;
                left:50%;
                top:50%;
                margin-left: -33px;
                margin-top: -33px;
            }
            /*.dt-column-center { text-align:center;}*/
            
            #sales_list td:nth-child(n+6){text-align:center;cursor:pointer;}
        </style>
    </head>
    <body>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>

        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left"><img src='energy_icon.png' />Reuniões Energy</div>
                    <div class="pull-right">
                        <form name="report" action="energy_reunioes.php" target="_self" method="post">
                            <div class="stat-input-date">
                                <input type="text" name="regular" id='data' class="input-date-min" value="<?= $data ?>">
                                <div class="fieldIcon"><i class="icon-calendar"></i></div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <table id='sales_list' class="table table-mod-2">
                        <thead></thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                    <div class="clear"></div>


                </div>
            </div>
        </div>

        <script>
            function dadoscli(valor)
            {
                var url = "energy_print.php?id=" + valor;

                window.open(url, '_blank');
            }

            function dadoscli2(valor)
            {
                var url = "energy_print2.php?id=" + valor;

                window.open(url, '_blank');
            }

            function vercli(valor)
            {
                var url = "/sips-admin/admin_modify_lead.php?lead_id=" + valor;

                window.open(url, '_blank');
            }


            var dates = "<?= $data ?>";
            var oTable = $('#sales_list').dataTable({
                "aaSorting": [[0, 'asc']],
                "iDisplayLength": 50,
                "sDom": '<"top">rt<"bottom"><"cont">',
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '_requests.php',
                "fnServerParams": function(aoData)
                {
                    aoData.push(
                            {"name": "action", "value": "get_sales_list"},
                    {"name": "sent_campaign_id", "value": "<?= $campaign_id; ?>"},
                    {"name": "sent_data", "value": dates}
                    );
                },
                "aoColumns": [
                    {"sTitle": "#", "sWidth": "16px", "sClass": "dt-column-main"},
                    {"sTitle": "Operador", "sWidth": "32px", "sClass": "dt-column-center"},
                    {"sTitle": "Cliente", "sWidth": "256px", "sClass": "dt-column-center", "sType": "string"},
                    {"sTitle": "Telefone", "sWidth": "16px", "sClass": "dt-column-center", "sType": "string"},
                    {"sTitle": "Hora da Venda", "sWidth": "78px", "sClass": "dt-column-center", "sType": "string"},
                    {"sTitle": "Folha Reunião", "sWidth": "16px", "sClass": "dt-column-center", "sType": "string"},
                    {"sTitle": "Relatório de Visita", "sWidth": "16px", "sClass": "dt-column-center", "sType": "string"},
                    {"sTitle": "Editar", "sWidth": "16px", "sClass": "dt-column-center", "sType": "string"}
                ],
                "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"},
                "fnDrawCallback": function(oSettings, json) {
                    $('#sales_list').css({"width": "100%"});
                }
            });

            $('#data').change(function() {
                oTable.fnClearTable(0);
                dates = $("#data").val();
                oTable.fnReloadAjax();
            });

            $(function() {
                $("#data").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "yy-mm-dd"});

                $("#loader").fadeOut("slow");

            });

        </script>
    </body>
</html>
