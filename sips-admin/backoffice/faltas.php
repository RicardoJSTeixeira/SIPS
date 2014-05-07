<?
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $path.="../";
}
define("ROOT", $path);

require(ROOT . "ini/dbconnect.php");

$data = (isset($_POST['data'])) ? $_POST['data'] : date('Y-m-d');
$datafim = date("Y-m-d", strtotime("+1 day" . $data));
$datainicio = $data;
$current_admin = $_SERVER['PHP_AUTH_USER'];


$query = "select a.user_group,allowed_campaigns,user_level from vicidial_users a inner join `vicidial_user_groups` b on a.user_group=b.user_group where user='$current_admin'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_assoc($result);


$allowed_camps_regex = str_replace(" ", "|", trim(rtrim($row['allowed_campaigns'], " -")));
$user_level = $row['user_level'];

if ($row['user_group'] == "ADMIN") {
    $ret = "";
} else {
    $ret = "WHERE allowed_campaigns REGEXP '$allowed_camps_regex'";
}
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
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.9.2.custom.min.js"></script>
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
        </style> 
    </head>
    <body>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <div class=content>
            <div class="grid">
                <div class="grid-title" style="border:0">
                    <div class="pull-left">Faltas e Pausas</div>
                    <div class="pull-right">
                        <form name="users" target="_self" method="post">
                            <div class="stat-input-date" style="display:inline">
                                <input type="text" name="data" id='data' class="input-date-min" value="<?= $data ?>">
                                <div class="fieldIcon"><i class="icon-calendar"></i> </div>
                            </div>
                            <input type="submit" value="Pesquisar" class="btn" style="margin-top: -9px;">
                        </form>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <div>




                <?php
                $result = mysql_query("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ") or die(mysql_error());
                $i = 0;
                while ($rugroups = mysql_fetch_assoc($result)) {
                    $tabela = "";
                    $campaigns = "";
                    if (!eregi("ALL-CAMPAIGNS", $rugroups['allowed_campaigns'])) {
                        $campaigns = " AND campaign_id IN ('" . str_replace(" ", "','", rtrim(trim($rugroups['allowed_campaigns']), " -")) . "') ";
                    }
                    ?>
                    <div class='grid'>
                        <div class='grid-title'>
                            <div class='pull-left'><?= $rugroups[user_group] ?></div>
                            <div class='pull-right'></div>
                            <div class='clear'></div>
                        </div>
                        <table class='table table-mod table-striped'>
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Login</th>
                                    <th>Logout</th>
                                    <?php
                                    $pausecodes = mysql_query("SELECT `pause_code`, `pause_code_name`, `max_time` FROM `vicidial_pause_codes` WHERE active=1 $campaigns GROUP BY `pause_code` ORDER BY `pause_code`") or die(mysql_error());
                                    $pausequery1 = "";
                                    $pausequery2 = "";
                                    $tempos = array();
                                    for ($i = 0; $i < mysql_num_rows($pausecodes); $i++) {
                                        $tmp = mysql_fetch_assoc($pausecodes);
                                        $tempos[$i] = $tmp['max_time'];
                                        ?>
                                        <th><?= $tmp[pause_code_name] ?></th>
                                        <?php
                                        $pausequery1.=",IFNULL(a.`$tmp[pause_code]`,0) '$tmp[pause_code]' ";
                                        $pausequery2.=",SUM(IF (`sub_status`='$tmp[pause_code]',`pause_sec`,0)) '$tmp[pause_code]' ";
                                    }
                                    ?>
                            </thead>
                            <tbody>
                                    <?php
                                    $query = "SELECT b.`user`,b.`full_name`,IFNULL(c.`login`,'Falta') LOGIN, IFNULL(d.`LOGOUT`,'') LOGOUT
		$pausequery1
			FROM `vicidial_users` b LEFT JOIN 
			(SELECT user
		$pausequery2
			FROM `vicidial_agent_log`
		WHERE `event_time` between '$datainicio' AND '$datafim'
		GROUP BY `user`
			) a 
		ON a.`user`=b.`user` 
		LEFT JOIN
			(SELECT user,`event_date` LOGIN
			FROM `vicidial_user_log` WHERE `event_date` between '$datainicio' AND '$datafim' AND event='LOGIN' ORDER BY event_epoch ASC ) c
		ON b.user=c.user
		LEFT JOIN
			(SELECT user,`event_date` LOGOUT
			FROM `vicidial_user_log` WHERE `event_date` between '$datainicio' AND '$datafim' AND event='LOGOUT' ORDER BY event_epoch DESC ) d
		ON b.user=d.user
		WHERE b.`user_group`='$rugroups[user_group]' AND b.`active`='Y' AND b.user_level<$user_level
		GROUP BY b.`user`";
//echo $query;
                                    $query = mysql_query($query) or die(mysql_error());
                                    
                                        echo $tabela;

                                    while ($row = mysql_fetch_row($query)) {
                                        ?>
                                    <TR> 
                                    <?php
                                    for ($column_num = 0; $column_num < mysql_num_fields($query); $column_num++) {
                                        if ($column_num == 0) {
                                            $id_user = $row[$column_num];
                                            continue;
                                        }
                                        $red = ($row[$column_num] == "Falta" or ($column_num > 3 AND $row[$column_num] > $tempos[$column_num - 4] AND $tempos[$column_num - 4] != 0));

                                        $td_content = ($column_num < 4) ? $row[$column_num] : (($row[$column_num]>3600)?date("G:i:s", $row[$column_num]):date("i:s", $row[$column_num]));
                                        ?>
                                            <td><?= ($red) ? '<span class="label label-important">' : "" ?><?= $td_content ?><?= ($red) ? '</span>' : '' ?></td>
                                        <?php } ?>
                                    </TR>
                                    <?php } ?>
                            </tbody>
                        </table>
                    </div> 
                                <?php } ?>


            </div>

            <script language="JavaScript">
                $(function() {
                    $("#data").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: "yy-mm-dd",
                        maxDate: '0'
                    });
                    $("#loader").fadeOut("slow");
                });
            </script>
    </body>
</html>
