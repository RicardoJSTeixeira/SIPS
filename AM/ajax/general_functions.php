<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/user.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();
$u=$user->getUser();

switch ($action) {

    case "get_user_level"://ALL MARCAÇOES
        echo json_encode($u->user_level);
        break;

    case "get_unread_messages":
        $query = "SELECT `id_msg`,`from`,`msg`,`event_date` from sips_msg where `to`=? and delivered=0";
        $stmt = $db->prepare($query);
        $stmt->execute(array($u->username));
        $js = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($js);
        break;

    case "edit_message_status":
        $query = "UPDATE  sips_msg set delivered=1 where `id_msg`=:id";
        $stmt = $db->prepare($query);
        echo json_encode($stmt->execute(array(":id"=>$id_msg)));
        break;
    
    case "edit_message_status_by_user":
        $query = "UPDATE  sips_msg set delivered=1 where `to`=:id";
        $stmt = $db->prepare($query);
        echo json_encode($stmt->execute(array(":id"=>$u->username)));
        break;
}