<?php

require_once('../func/reserve_utils.php');
if (isset($_POST['pedido'])) {
    $pedido = preg_replace($only_nr, '', $_POST['pedido']);
} else {
    exit;
}

if (isset($_POST['start']) && isset($_POST['end']) && isset($_POST['resource'])) {
    $start = $_POST['start'];
    $end = $_POST['end'];
    $resource = preg_replace($only_nr, '', $_POST['resource']);
} else {
    exit;
}

if (!(checkDateTime($start) && checkDateTime($end))) {
    exit;
}

if ($pedido == 1) {
    $query = "Select id_reservation FROM sips_sd_reservations 
    WHERE id_resource='" . mysql_real_escape_string($resource) . "' 
    AND start_date='" . $start . "' AND end_date='" . $end . "';";

    $result = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_assoc($result);

    echo json_encode(array("sucess" => "1", "id" => $row["id_reservation"]));
    exit;
} elseif ($pedido == 2) {

    $query = "Select count(*) existe FROM sips_sd_reservations 
		WHERE id_resource='" . mysql_real_escape_string($resource) . "' 
		AND start_date='" . $start . "' AND end_date='" . $end . "';";

    $result = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_assoc($result);

    if ($row[existe] > 0) {
        echo json_encode(array("sucess" => "0", "message" => "Já existe."));
        exit;
    }


    if (isset($_POST['id'])) {
        $id = preg_replace($only_nr, '', $_POST['id']);
    }

    $query = "UPDATE `sips_sd_reservations` SET
       `start_date`='" . mysql_real_escape_string($start) . "',
       `end_date`='" . mysql_real_escape_string($end) . "',
       `id_resource`='" . mysql_real_escape_string($resource) . "'
           WHERE
       `id_reservation`='" . mysql_real_escape_string($id) . "';";
    mysql_query($query) or die(mysql_error());
    echo json_encode(array("sucess" => "1", "message" => "Sucesso"));
} else {
    exit;
}
?>