<?php

// database & mysqli
require("../database/db_connect.php");
ini_set("display_errors", "1");
// post & get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

// function filter
switch ($zero) {
    case 'InsertNewUser' : InsertNewUser($email, $password, $db, $firstname, $lastname);
        break;
    default : header("HTTP/1.1 500 Internal Server Error");
        exit;
}

// functions    
function InsertNewUser($email, $password, $db, $firstname, $lastname) {
    $params0 = array($email);
    $result0 = $db->rawQuery("SELECT id_user FROM zero.users WHERE email = ?", $params0);
    if (count($result0) === 0) {


        $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
        $password = hash('sha512', $password . $salt);

        $params1 = array($email, $password, $salt);
        $db->rawInsert("INSERT INTO zero.users (email, password, salt) VALUES (?, ?, ?)", $params1);

        $lastID = $db->getInsertId();

        $params2 = array($lastID, $firstname, $lastname);
        $db->rawInsert("INSERT INTO zero.user_info (id_user, name, last_name) VALUES (?, ?, ?)", $params2);

        $params3 = array($lastID, 1);
        $db->rawInsert("INSERT INTO zero.user_notifications (id_user, admin_global) VALUES (?, ?)", $params3);
        $js['result'] = array(true, "User Created");
        echo json_encode($js);
    } else {
        $js['result'] = array(false, "User Exists");
        echo json_encode($js);
    }
}

?>