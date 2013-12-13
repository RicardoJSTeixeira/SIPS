<?php

require("functions.php");
require("../../ini/db.php");
require("../notifications/functions.php");

ini_set("display_errors", "1");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

switch ($zero) {
    case 'Login' : Login($email, $password);
        break;
    case 'Logout' : Logout();
        break;
    default : header("HTTP/1.1 500 Internal Server Error");
        exit;
}

function Login($email, $password) {
    global $db;
    $now = date('Y-m-d H:i:s');
    $params = array($email);

    $stmt = $db->prepare("SELECT id_user, password, salt FROM zero.users WHERE email = ? LIMIT 1");
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_BOTH);

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_browser = $_SERVER['HTTP_USER_AGENT'];
    if (count($results) !== 0) {
        $id_user = $results[0]['id_user'];
        $password = hash('sha512', $password . $results[0]['salt']);
        if ($password === $results[0]['password']) {
            SessionStart();
            $_SESSION['id_user'] = $results[0]['id_user'];
            $_SESSION['login_string'] = hash('sha512', $password . $ip_address . $user_browser);
            $params = array($id_user, $ip_address, 'login', $now);

            $stmt = $db->prepare("INSERT INTO zero.user_auth_log (id_user, ip, event, event_time) VALUES (?, INET_ATON(?), ?, ?)");
            $stmt->execute($params);
            $LastInsertId = $db->lastInsertId();
            Notification($LastInsertId, $now, "admin_global");

            $js['result'] = array(true, "Login Successful", $id_user, $ip_address);
            echo json_encode($js);
        } else {
            // user found, wrong password => insert into user_login_log
            $params = array($id_user, $ip_address, 'password', $now);
            $stmt = $db->prepare("INSERT INTO zero.user_auth_log (id_user, ip, event, event_time) VALUES (?, INET_ATON(?), ?, ?)");
            $stmt->execute($params);
            $js['result'] = array(false, "Wrong Password");
            echo json_encode($js);
        }
    } else {
        // no user found => insert into login_attempts
        $params = array($ip_address, $now);
        $stmt = $db->prepare("INSERT INTO zero.login_attempts (ip, event_time) VALUES (INET_ATON(?), ?)");
        $stmt->execute($params);
        $js['result'] = array(false, "No Such User Exists");
        echo json_encode($js);
    }
}

function Logout() {
    global $db;
    SessionStart();
    $id_user = $_SESSION['id_user'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    // Unset all session values 
    $_SESSION = array();
    // get session parameters 
    $cookies = session_get_cookie_params();
    // Delete the actual cookie.
    setcookie(session_name(), '', time() - 42000, $cookies["path"], $cookies["domain"], $cookies["secure"], $cookies["httponly"]);
    // Destroy session
    session_destroy();
    // Record logout in DB
    $now = date('Y-m-d H:i:s');
    $params = array($id_user, $ip_address, 'logout', $now);

    $stmt = $db->prepare("INSERT INTO zero.user_auth_log (id_user, ip, event, event_time) VALUES (?, INET_ATON(?), ?, ?)");
    $stmt->execute($params);
    $LastInsertId = $db->lastInsertId();

    Notification($LastInsertId, $now, "admin_global");
    $js['result'] = array(true, "Logout Successful", $id_user);
    echo json_encode($js);
}
