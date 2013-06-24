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
    
    $q = "INSERT INTO `asterisk`.`sips_sd_agent_ref` (`ref_id`, `user`, `id_calendar`, `cal_type`) VALUES (NULL, '$user', '$cal', '$type');";
    mysql_query($q, $link) or die(mysql_error());
    echo json_encode(array("last"=>mysql_insert_id()));
}

if ($pedido == 667) {
    $id = $_POST['id'];

    $q = "Delete From sips_sd_agent_ref Where ref_id=$id";
    mysql_query($q, $link) or die(mysql_error());
    echo json_encode(array("done"));
}
?>
