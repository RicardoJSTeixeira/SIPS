<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
if(isset($_GET['id'])){$id_camp=$_GET['id'];}else{exit();}
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
        <script type="text/javascript" src="/jquery/jsdatatable/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />


    <body id="main_body" >


        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Listagem</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>

                <table class="table table-mod">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Nº Telefone</th>
                            <th>Mensagem</th>
                            <th>Cartão</th>
                        </tr>
                    </thead>
                    <tbody>
<?php


$query = "SELECT DATA, phone_number, MSG, cartao FROM `sms_list` WHERE id_sms_campaign=$id_camp ORDER BY DATA DESC";
$query=mysql_query($query) or die(mysql_error());
while ($linha=mysql_fetch_assoc($query)) {
    ?>
                            <tr>
                                <td>
    <?= $linha[DATA] ?>
                                </td>
                                <td>
    <?= $linha[phone_number] ?>
                                </td>
                                <td>
                                    <abbr title="<?= $linha[MSG] ?>">
    <?= (strlen($linha[MSG]) > 90) ? substr($linha[MSG], 0, (87)) . '...' : $linha[MSG] ?>
                                    </abbr>
                                </td>
                                <td>
    <?= $linha[cartao] ?>
                                </td>
                            </tr>
<?php }
?>

                    </tbody>
                </table>
            </div>
        </div>

    </body>
</html>