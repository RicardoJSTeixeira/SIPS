<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/user.php";
require "$root/AM/lib_php/msg_alerts.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();
$u = $user->getUser();
$message = new messages($db, $u->username);
$alert = new alerts($db, $u->username);

switch ($action) {

    case "get_unread_messages":
        echo json_encode($message->getAll());
        break;

    case "edit_message_status":
        echo json_encode($message->setReaded($id_msg));
        break;

    case "edit_message_status_by_user":
        echo json_encode($message->setAllReaded($u->username));
        break;

    case "get_alerts":
        echo json_encode($alert->getAll());
        break;

    case "set_readed":
        echo json_encode($alert->setReaded($id_msg));
        break;

    case "set_all_readed":
        echo json_encode($alert->setAllReaded($u->username));
        break;

    case "make_alert":
        echo json_encode($alert->make($is_for, $alert, $section, $record_id,$cancel));
        break;
}
