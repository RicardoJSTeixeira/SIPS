<?php

require("../../ini/db.php");
require("../lib/translater.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
ini_set("display_errors", "1");

$dt = new dictionary($db);

switch ($action) {
    case "insert":
        try {
            echo json_encode(array("id" => $dt->set($origin, $trans)));
        } catch (Exception $exc) {
            if (preg_match("/Duplicate/", $exc->getMessage())) {
                echo json_encode(array("error" => $exc->getCode(), "msg" => "The translation that you wrote is already in the dictionary."));
            } else {
                echo json_encode(array("error" => $exc->getCode(), "msg" => $exc->getMessage()));
            }
        }
        break;
    case "delete":
        echo json_encode($dt->del($id));
        break;
    case "getAll":
        $table = array();
        foreach ($dt->get() as $value) {
            $table[] = array($value->original, $value->translation, "<button class='btn btn-danger icon-alone ' data-id='$value->id'><i class='icon-trash'></i></button>");
        }
        echo json_encode(array("aaData" => $table));
        break;

    default:
        break;
}