<?php

require_once('../func/reserve_utils.php');

if (isset($_POST['nr'])) {
    $nr = preg_replace($only_nr, '', $_POST["nr"]);
    $query = "DELETE FROM `sips_sd_cp` WHERE `id_cp`='$nr';";
    mysql_query($query, $link) or die(json_encode(array("sucess" => 0)) . "  " . mysql_error());
    echo json_encode(array("sucess" => 1));
    exit;
}

if (!(isset($_POST["cp"]) && isset($_POST["ref"]) )) {
    echo "error";
    exit;
}

$cp = $_POST["cp"];
$ref = $_POST["ref"];

$query = "INSERT INTO `sips_sd_cp` (`cp`, `tecnico`)
            VALUES ('" . mysql_real_escape_string($cp) . "',
                        '" . mysql_real_escape_string($ref) . "');";

mysql_query($query, $link) or die(json_encode(array("sucess" => 0)) . "  " . mysql_error());
echo json_encode(array("sucess" => 1, "cp" => $cp, "ref" => $ref, "id" => mysql_insert_id($link)));
