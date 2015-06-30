<?php

require_once('../func/reserve_utils.php');

if (isset($_POST['pedido'])) {
    $pedido = preg_replace($only_nr, '', $_POST['pedido']);
} else {
    exit;
}

if ($pedido == 128) {
    $user = $_POST['user'];
    $cal = $_POST['cal'];
    $type = $_POST['type'];

    $q = "INSERT INTO `sips_sd_agent_ref` (`ref_id`, `user`, `id_calendar`, `cal_type`) VALUES (NULL, '$user', '$cal', '$type');";
    mysql_query($q, $link) or die(mysql_error());
    $last = mysql_insert_id();
    log_admin("CALENDAR_AGENT_REF", "ADD REF", $last, $q, $_POST['cr'], $_SERVER['REMOTE_ADDR']);
    if ($type == "1") {
        $q = "SELECT  `full_name` ,  `display_text` ,  `cal_type` 
                                                FROM  `sips_sd_agent_ref` a
                                                INNER JOIN sips_sd_schedulers b ON a.id_calendar = b.id_scheduler INNER JOIN vicidial_users c ON a.user = c.user
                                                WHERE ref_id=$last";
    } else {
        $q = "SELECT `full_name` ,  `display_text` ,  `cal_type` 
                                                FROM  `sips_sd_agent_ref` a
                                                INNER JOIN sips_sd_resources b ON a.id_calendar = b.id_resource INNER JOIN vicidial_users c ON a.user = c.user
                                                WHERE ref_id=$last";
    }

    $result = mysql_query($q, $link) or die(mysql_error());
    list($utilizador, $desc_cal, $tipo) = mysql_fetch_array($result);

    echo json_encode(array("last" => $last, "utilizador" => $utilizador, "desc_cal" => $desc_cal, "tipo" => strtr($tipo, array("RESOURCE" => "Recurso", "SCHEDULER" => "Calendário"))));
}

if ($pedido == 667) {
    $id = $_POST['id'];

    $q = "Delete From sips_sd_agent_ref Where ref_id=$id";
    mysql_query($q, $link) or die(mysql_error());
    log_admin("CALENDAR_AGENT_REF", "DEL REF", $id, $q, $_POST['cr'], $_SERVER['REMOTE_ADDR']);
    echo json_encode(array("done"));
}
