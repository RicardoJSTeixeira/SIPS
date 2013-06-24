<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");

function query_pop_select($query) {
    global $link;
    $result = mysql_query($query, $link);
    $options = "";
    while ($row = mysql_fetch_array($result)) {
        $options.="<option value='$row[0]'>$row[1]</option>";
    }
    return $options;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-tagmanager.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-tagmanager.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/colorpicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <style>
            .chzn-select{
                width: 350px;
            }
            #loader{
                background: #fff;
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
                    <div class="pull-left">Referencia de Calendários para Agentes</div>
                    <div class="pull-right"><button class="btn btn-large btn-primary" data-toggle="modal" data-target="#newRef">Novo</button></div>
                    <div class="clear"></div>
                </div>
                <table class="table table-mod">
                    <thead>
                        <tr>
                            <th>Utilizador</th>
                            <th>Descrição Calendário</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysql_query("SELECT ref_id, full_name, display_text, cal_type FROM (
                                                (SELECT  `ref_id` ,  `user` ,  `display_text` ,  `cal_type` 
                                                FROM  `sips_sd_agent_ref` a
                                                INNER JOIN sips_sd_schedulers b ON a.id_calendar = b.id_scheduler
                                                WHERE cal_type =1)
                                                UNION ALL (SELECT  `ref_id` ,  `user` ,  `display_text` ,  `cal_type` 
                                                FROM  `sips_sd_agent_ref` a
                                                INNER JOIN sips_sd_resources b ON a.id_calendar = b.id_resource
                                                WHERE cal_type =2))a
                                                INNER JOIN vicidial_users b ON a.user = b.user", $link);
                        while ($row = mysql_fetch_array($result)) {
                            ?>
                            <tr>
                                <td><?= $row[1] ?></td>
                                <td><?= $row[2] ?></td>
                                <td><?= strtr($row[3], array("RESOURCE" => "Recurso", "SCHEDULER" => "Calendário")) ?>
                                    <div class="view-button"><a href="#" class="btn  btn-mini activator"> <i class="icon-trash"></i><span>Eliminar</span></a></div></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div> 
        </div> 

        <!-- Modal -->
        <div id="newRef" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="modalLabel">Nova Referência</h3>
            </div>
            <div class="modal-body">
                <select><?= query_pop_select("Select user,full_name From vicidial_users Where 1") ?></select>
                <select><?= query_pop_select("Select id_scheduler,display_text From sips_sd_schedulers Where 1") ?></select>
                <select><?= query_pop_select("Select id_resource,display_text From sips_sd_resources Where 1") ?></select>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
                <button class="btn btn-primary">Gravar</button>
            </div>
        </div>

        <script>
            $(function() {
                $("#loader").fadeOut("slow");
            });
        </script>
    </body>
</html>