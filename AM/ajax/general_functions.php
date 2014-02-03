<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require '../../ini/db.php';
require '../../ini/dbconnect.php';
require '../../ini/user.php';
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users($db);



switch ($action) {



    case "get_user_level"://ALL MARCAÇOES
        echo json_encode($user->user_level);
        break;

    case "get_unread_messages":
        $js=array();
        $query = "SELECT `id_msg`,`from`,`msg`,`event_date` from sips_msg where `to`=? and delivered=0";
        $stmt = $db->prepare($query);
        $stmt->execute(array($user->id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id_msg" => $row["id_msg"], "from" => $row["from"], "msg" => $row["msg"], "event_date" => $row["event_date"]);
        }
        echo json_encode($js);
        break;

    case "edit_message_status":
        $query = "UPDATE  sips_msg set delivered=1 where `id_msg`=?";
        $stmt = $db->prepare($query);
        $stmt->execute(array($id_msg));
        echo json_encode(1);
        break;
    
    case "edit_message_status_by_user":
        $query = "UPDATE  sips_msg set delivered=1 where `to`=?";
        $stmt = $db->prepare($query);
        $stmt->execute(array($user->id));
        echo json_encode(1);
        break;
}
