<?php

// database & mysqli
require("../../ini/db.php");
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
    $stmt=$db->prepare("SELECT id_user FROM zero.users WHERE email=:email");
        $stmt->execute(array(":email"=>$params0));
        $result0=$stmt->fetchAll(PDO::FETCH_OBJ);
        if (count($result0)===0) {


        $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
        $password = hash('sha512', $password . $salt);

        $params1 = array($email, $password, $salt);
        
        
        $stmt=$db->prepare("INSERT INTO zero.users (email, password, salt) VALUES (?, ?, ?)");
        $stmt->execute($params1);
        $lastID = $db->lastInsertId();

        $params2 = array($lastID, $firstname, $lastname);
        $stmt=$db->prepare("INSERT INTO zero.user_info (id_user, name, last_name) VALUES (?, ?, ?)");
        $stmt->execute($params2);
        
        $params3 = array($lastID, 1);
        $stmt=$db->prepare("INSERT INTO zero.user_notifications (id_user, admin_global) VALUES (?, ?)");
        $stmt->execute($params3);
        $js['result'] = array(true, "User Created");
        echo json_encode($js);
    } else {
        $js['result'] = array(false, "User Exists");
        echo json_encode($js);
    }
}
