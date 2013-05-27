<?php

ini_set("display_errors", "1");

require('../dbconnect.php');
if (isset($_GET["ACTION"])) {
    $ACTION = $_GET["ACTION"];
} elseif (isset($_POST["ACTION"])) {
    $ACTION = $_POST["ACTION"];
}
if (isset($_GET["user"])) {
    $user = $_GET["user"];
} elseif (isset($_POST["user"])) {
    $user = $_POST["user"];
}

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


################################################################################
### update force ready
################################################################################
if ($ACTION == 'updateforceready') {
    $query = "INSERT INTO sips_forceready_control (user) VALUES ('$user')";
    $query = mysql_query($query, $link);
//echo "completed";
}

if ($action == "get_graph_data" && $sent_series == "calls_hora") { 


 $back8 = date("h", strtotime("-8 hours"));
  


    $query = "SELECT count(*),DATE_FORMAT(call_date, '%H') FROM `vicidial_log` WHERE campaign_id IN($sent_allowed_campaigns) AND `call_date` between DATE(NOW() - interval 8 hour) and NOW() GROUP BY hour(`call_date`)";
    $result = mysql_query($query, $link) or die(mysql_error());
    $i = 0;
    $total_calls= Array();
    while ($row = mysql_fetch_array($result)) {
        while ($back8 != $row[1]) {
            $total_calls[$i][0] = $back8;
            $total_calls[$i][1] = 0;
            $back8++;
            $i++;
        }
        $total_calls[$i][0] = $back8;
        $total_calls[$i][1] = $row[0];
        $back8++;
        $i++;
    }
    echo json_encode(array(label=>"Chamadas Realizadas",data=>$total_calls)); 
}
if ($action == "get_graph_data" && $sent_series == "calls_atendidas") { 


 	$back8 = date("h", strtotime("-8 hours"));
  


    $query = "SELECT count(*),DATE_FORMAT(call_date, '%H') FROM `vicidial_log` WHERE campaign_id IN($sent_allowed_campaigns) AND `call_date` between DATE(NOW() - interval 8 hour) and NOW() AND status IN('MSG001','MSG002','MSG003','MSG004','MSG005','MSG006','MSG007','MSG008','MSG009','MSG0010') GROUP BY hour(`call_date`)";
    $result = mysql_query($query, $link) or die(mysql_error());
    $i = 0;
    $total_calls= Array();
    while ($row = mysql_fetch_array($result)) {
        while ($back8 != $row[1]) {
            $total_calls[$i][0] = $back8;
            $total_calls[$i][1] = 0;
            $back8++;
            $i++;
        }
        $total_calls[$i][0] = $back8;
        $total_calls[$i][1] = $row[0];
        $back8++;
        $i++;
    }
    echo json_encode(array(label=>"Chamadas Atendidas",data=>$total_calls)); 
}
if ($action == "get_graph_data" && $sent_series == "msg_entregues") { 


 	$back8 = date("h", strtotime("-8 hours"));
  


    $query = "SELECT count(*),DATE_FORMAT(call_date, '%H') FROM `vicidial_log` WHERE campaign_id IN($sent_allowed_campaigns) AND `call_date` between DATE(NOW() - interval 8 hour) and NOW() AND status IN('MSG001') GROUP BY hour(`call_date`)";
    $result = mysql_query($query, $link) or die(mysql_error());
    $i = 0;
    $total_calls= Array();
    while ($row = mysql_fetch_array($result)) {
        while ($back8 != $row[1]) {
            $total_calls[$i][0] = $back8;
            $total_calls[$i][1] = 0;
            $back8++;
            $i++;
        }
        $total_calls[$i][0] = $back8;
        $total_calls[$i][1] = $row[0];
        $back8++;
        $i++;
    }
    echo json_encode(array(label=>"Mensagens Entregues",data=>$total_calls)); 
}
?>