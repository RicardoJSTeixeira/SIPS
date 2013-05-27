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
        </style>
    </head>
    <body>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>

        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Enviadas por unidade</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div id="wr"></div>
                <table class="table table-mod">
                    <thead>
                        <tr>
                            <th>Enviadas</th>
                            <th>Ver lista de SMSs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT count(*) as enviadas FROM `sms_list` WHERE id_sms_campaign=998";
                        $query = mysql_query($query);
                        while ($linha = mysql_fetch_assoc($query)) {
                            ?>
                            <tr>
                                <td><?= $linha[enviadas] ?></td>
                                <td><a href="sms_list.php?id=998"><i class='icon-picture'></i> ver</a></td>
                            </tr>
                        <?php }
                        ?>

                    </tbody>
                </table>
            </div>


            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Enviadas por Campanha</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <table class="table table-mod-2" id="camps">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mensagem</th>
                                <th>Operador</th>
                                <th>Enviadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT a.ID_sms_campaign as ID,a.texto,a.operador,count(*) as enviadas FROM `sms_campaigns` a inner join `sms_list` b ON a.id_sms_campaign=b.id_sms_campaign group by a.id_sms_campaign ORDER BY a.ID_sms_campaign DESC";
                            $query = mysql_query($query);
                            while ($linha = mysql_fetch_assoc($query)) {
                                ?>
                                <tr>
                                    <td>
                                        <?= $linha[ID] ?>
                                    </td>
                                    <td>
                                        <abbr title="<?= $linha[texto] ?>">
                                            <?= "<a href='sms_list.php?id=$linha[ID]'><i class='icon-picture'></i> ver</a> <a href='sms_form_edit.php?&id_camp=$linha[ID]' ><i class='icon-arrow-right'></i> continuar</a> " . ((strlen($linha[texto]) > 63) ? substr($linha[texto], 0, (60)) . '...' : $linha[texto]) ?>
                                        </abbr>
                                    </td>
                                    <td>
                                        <?= ($linha[operador] == "9") ? "Todos" : $linha[operador] ?> 
                                    </td>
                                    <td>
                                        <?= $linha[enviadas] ?>
                                    </td>
                                </tr>
                            <?php }
                            ?>

                        </tbody>
                    </table>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <script>$(function() {
                $('#camps').dataTable({
                    "aaSorting": [[0, "desc"]],
                    "oLanguage": {
                        "sLengthMenu": "Mostrar _MENU_ registos por pagina",
                        "sZeroRecords": "Nada encontrado - desculpe",
                        "sInfo": "A ver de _START_ a _END_ do total de _TOTAL_ registos",
                        "sInfoEmpty": "Sem registos",
                        "sInfoFiltered": "(Filtrado do total de _MAX_ registos)"
                    }
                });

                $("#loader").fadeOut("slow");
            });
        </script>
    </body>
</html>