<?php

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . 'ini/dbconnect.php');

$inOUT = $_POST[inOUT];

$agent_log_id = $_POST[agent_log_id];
//leads dos novos clientes
$lead_ids = $_POST[lead_id];
//lead_original
$lead_id_o = $_POST[lead_id_o];

foreach ($lead_ids as $lead_id) {


$rand = mt_rand(0, 255);
$unique_id = date("U") . "." . $rand;

    $query = "Select phone_number from vicidial_list where lead_id='$lead_id'";
    $result = mysql_query($query) or die(mysql_error() . header('HTTP/1.1 500 Internal Server Error'));
    $row = mysql_fetch_assoc($result);
    $phone_number = $row[phone_number];

   


    $stmtA = "INSERT INTO vicidial_agent_log Select null, `user`, `server_ip`, `event_time`, '$lead_id', `campaign_id`, `pause_epoch`, `pause_sec`, `wait_epoch`, `wait_sec`, `talk_epoch`, `talk_sec`, `dispo_epoch`, `dispo_sec`, 'NOVOCL', `user_group`, `comments`, `sub_status`, `dead_epoch`, `dead_sec`, `processed`, '$unique_id' from vicidial_agent_log where agent_log_id='$agent_log_id';";
    mysql_query($stmtA) or die(mysql_error() . header('HTTP/1.1 500 Internal Server Error'));

    if ($inOUT == "OUT") {
        $stmtA = "INSERT INTO vicidial_log SELECT '$unique_id', '$lead_id', `list_id`, `campaign_id`, `call_date`, `start_epoch`, `end_epoch`, `length_in_sec`, 'NOVOCL', `phone_code`, '$phone_number', `user`, `comments`, `processed`, `user_group`, `term_reason`, `alt_dial` FROM `vicidial_log` where  lead_id ='$lead_id_o' ORDER BY call_date DESC LIMIT 1;";
        mysql_query($stmtA) or die(mysql_error() . header('HTTP/1.1 500 Internal Server Error'));
    } else {
        $stmtA = "INSERT INTO vicidial_closer_log SELECT null, '$lead_id', `list_id`, `campaign_id`, `call_date`, `start_epoch`, `end_epoch`, `length_in_sec`, 'NOVOCL', `phone_code`,  '$phone_number', `user`, `comments`, `processed`, `queue_seconds`, `user_group`, `xfercallid`, `term_reason`, '$unique_id', `agent_only`, `queue_position` FROM `vicidial_closer_log` where lead_id='$lead_id_o' ORDER BY call_date DESC LIMIT 1;";
        mysql_query($stmtA) or die(mysql_error() . header('HTTP/1.1 500 Internal Server Error'));
    }
}
echo 'Novo Cliente Done';
?>
