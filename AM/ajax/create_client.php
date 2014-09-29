<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require("../lib_php/db.php");
require '../lib_php/logger.php';
require("../lib_php/user.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
$user = new UserLogin($db);
$user->confirm_login();
$log = new Logger($db, $user->getUser());
$variables = array();
$js = array();
switch ($action) {
    case "get_fields":
        $u = $user->getUser();
        $query = "SET CHARACTER SET utf8;";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $query = "SELECT name,display_name,field_order FROM `vicidial_list_ref` WHERE campaign_id=? AND active='1' ORDER BY field_order ASC";
        $variables[] = $u->campaign;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        echo json_encode($stmt->fetchAll(PDO::FETCH_OBJ));
        break;

    case "create_client":
        $variables[] = date("Y-m-d H:i:s");
        $variables[] = "NEW";
        $u = $user->getUser();
        $variables[] = $u->username;
        $variables[] = $u->list_id;
        $query_log=array();
        foreach ($info as $value) {
            if ($value["name"] != "compart") {
                $fields = $fields . " , " . $value["name"];
                $values = $values . ", ? ";
                $variables[] = $value["value"];
                if($value["value"])
                $query_log[$value["name"]]= $value["value"];
            }
        }
        $query = "INSERT INTO vicidial_list (entry_date,status,user,list_id $fields) VALUES (?,?,?,? $values) ";
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $log->set($db->lastInsertId(), Logger::T_INS, Logger::S_CLT, json_encode($query_log), logger::A_APV);
        echo json_encode($db->lastInsertId());
        break;
}

