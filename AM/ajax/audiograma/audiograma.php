<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require("../../lib_php/db.php");

require("../../lib_php/user.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);




$variables = array();
$unique_id = time() . "." . rand(1, 1000);
switch ($action) {



    case 'save_audiograma':

        $result = array();
        foreach ($info as $value) {
            $result[preg_replace("/[Mask]|[0-9]/", '', $value["name"])][] = (object) array("name"=>$value["name"],"value"=>$value["value"]);
        }

      
        foreach ($result as $key => $value) {
            $variables = array();
            $query = "insert into spice_audiograma (lead_id,uniqueid,name,value) values(?,?,?,?)";
            $variables[] = $lead_id;
            $variables[] = $unique_id;
            $variables[] = $key;
            $variables[] = json_encode($value);
            $stmt = $db->prepare($query);
            $stmt->execute($variables);
        }
        echo json_encode(1);
        break;


    case "populate":
        $result=array();
        $query = "SELECT name,value FROM spice_audiograma where lead_id=? order by id asc";
        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row["value"]=  json_decode($row["value"]);
            $result[]=$row;
        }


        echo json_encode($result);
        break;
};
