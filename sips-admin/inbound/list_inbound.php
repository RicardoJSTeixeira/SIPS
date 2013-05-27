<?
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");

$stmt = "SELECT group_id,group_name,queue_priority,active,call_time_id,group_color from vicidial_inbound_groups order by group_id";
$rslt = mysql_query($stmt, $link);
$ingroups_to_print = mysql_num_rows($rslt);
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

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
        <style>
            table tbody td{
                vertical-align: middle !important; 
            }
        </style>
    </head>
    <body>	
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Grupos Inbound</div>
                    <div class="pull-right"><a href="new_inbound_group.php" class="btn btn-large btn-primary">Novo</a></div>
                    <div class="clear"></div>
                </div>
                <div id="wr"></div>
                <table class="table table-mod">
                    <thead>
                        <tr>
                            <th>Grupo Inbound</th>
                            <th>Nome</th>
                            <th>Prioridade</th>
                            <th>Activo</th>
                            <th>Horas</th>
                            <th>Cor do Grupo</th>
                        </tr>
                    </thead>
                    <tbody>


                        <?php
                        $o = 0;
                        while ($ingroups_to_print > $o) {
                            $row = mysql_fetch_row($rslt);
                            ?>
                            <tr>
                                <td><a href="edit_inbound.php?group_id=<?= $row[0] ?>" ><i class="icon-pencil"></i> <?= $row[0] ?></a></td>
                                <td><?= $row[1] ?></td>
                                <td><?= $row[2] ?></td>
                                <td><?= ($row[3] == "Y") ? "Sim" : "Não" ?></td>
                                <td><?= $row[4] ?></td>
                                <td bgcolor="<?= $row[5] ?>"><font size=1></td>
                            </tr>
    <?php
    $o++;
}
?>    
                    </tbody>
                </table>
            </div>
        </div>
<?php if (isset($_GET["success"])) { ?> 
            <script>
                $(function() {
                    makeAlert("#wr", "Successo", "Agora pode configurar ao pormenor o seu novo Grupo de Inboud :-).", 4, false, false);
                });
            </script>
<?php } 
#FOOTER
        $footer = ROOT . "ini/footer.php";
        require($footer);
        ?>