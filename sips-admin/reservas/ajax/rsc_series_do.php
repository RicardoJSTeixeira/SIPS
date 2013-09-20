<?php

require_once('../func/reserve_utils.php');

if (isset($_POST['nr'])) {
    $nr = preg_replace($only_nr, '', $_POST["nr"]);
    $query = "DELETE FROM `sips_sd_series` WHERE `id_serie`='$nr';";
    mysql_query($query, $link) or die(json_encode(array("sucess" => 0)) . "  " . mysql_error());
    echo json_encode(array("sucess" => 1));
    exit;
}

if (!(isset($_POST["beg"]) && isset($_POST["end"]) && isset($_POST["d_start"]) && isset($_POST["d_end"]) && isset($_POST["id"]))) {
    echo "error";
    exit;
}

$beg = preg_replace($only_nr_p, '', $_POST["beg"]) . ":00";
$end = preg_replace($only_nr_p, '', $_POST["end"] . ":00");
$d_start = preg_replace($only_nr, '', $_POST["d_start"]);
$d_end = preg_replace($only_nr, '', $_POST["d_end"]);
$id_resource = preg_replace($only_nr, '', $_POST["id"]);

$query = "INSERT INTO `sips_sd_series` (`start_time`, `end_time`, `day_of_week_start`, `day_of_week_end`, `id_resource`)
            VALUES ( '" . mysql_real_escape_string($beg) . "',
			'" . mysql_real_escape_string($end) . "',
			" . mysql_real_escape_string($d_start) . ",
			" . mysql_real_escape_string($d_end) . ",
                        " . mysql_real_escape_string($id_resource) . ");";

mysql_query($query, $link) or die(json_encode(array("sucess" => 0)) . "  " . mysql_error());
echo json_encode(array("sucess" => 1, "time_start" => substr($beg, 0, -3), "time_end" => substr($end, 0, -3), "d_start" => $d_start, "d_end" => $d_end, "id" => mysql_insert_id($link)));
?>
