<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$root/AM/lib_php/db.php");

require("$root/AM/lib_php/user.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);

$variables = array();
$unique_id = filter_var($_POST['reservation_id']);
switch ($action) {



    case 'save_audiograma':

        $result = array();
        foreach ($info as $value) {
            $result[preg_replace("/[Mask]|[0-9]/", '', $value["name"])][] = (object) array("name" => $value["name"], "value" => $value["value"]);
        }


        foreach ($result as $key => $value) {
            $query = "INSERT INTO spice_audiograma (lead_id, uniqueid, name, value, date) VALUES (:lead_id, :unique_id, :name, :value, :date)";
            $stmt = $db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id, ":unique_id" => $unique_id, ":name" => $key, ":value" => json_encode($value), ":date" => date('Y-m-d H:i:s')));
        }
        echo json_encode(1);
        break;


    case "populate":
        $result = array();
        $query = "SELECT name, value FROM spice_audiograma WHERE lead_id=:lead_id ORDER BY date DESC, name ASC LIMIT 6 ";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["value"] = json_decode($row["value"]);
            $result[] = $row;
        }
        echo json_encode($result);
        break;
}