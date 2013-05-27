<?

function help($where, $text) {
    return '<a onclick="openNewWindow(\'../admin.php?ADD=99999#' . $where . '\')">' . $text . '</a>';
}

$PHP_AUTH_USER = $_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW = $_SERVER["PHP_AUTH_PW"];

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Lista CALL TIMES</title>

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
    </head>
    <body>
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">CALL TIME LISTINGS:</div>
                    <div class="pull-right"><a href="new_call_times.php" class="btn btn-large btn-primary">New</a></div>
                    <div class="clear"></div>
                </div>
                    <div id="wr"></div>
                <table class="table table-mod">
                    <thead>
                        <tr>
                            <th>CALLTIME ID</th>
                            <th>CALLTIME NAME</th>
                            <th>DEFAULT START</th>
                            <th>DEFAULT STOP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = "SELECT call_time_id,call_time_name,ct_default_start,ct_default_stop from vicidial_call_times order by call_time_id";
                        $rslt = mysql_query($stmt, $link);
                        $calltimes_to_print = mysql_num_rows($rslt);

                        while ($row = mysql_fetch_row($rslt)) {
                            ?>
                            <tr>
                                <td><a href="edit_call_times.php?call_time_id=<?=$row[0]?>"><i class="icon-pencil"></i> <?= $row[0] ?></a></td>
                                <td><?= $row[1] ?></td>
                                <td><?= $row[2] ?></td>
                                <td><?= $row[3] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (isset($_GET["success"])) { ?> 
            <script>
                $(function() {
                    makeAlert("#wr", "Successo", "Agora pode configurar ao pormenor o seu novo CALL TIME :-).", 4, false, false);
                });
            </script>
<?php } ?>
    </body>
</html>
