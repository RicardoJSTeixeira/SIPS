<?php
function SessionStart() {
    $session_name = 'ZERO_SESSION'; 
    $secure = false; // Set to true if using https.
    $httponly = true; // This stops javascript being able to access the session id. 
    ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
    $cookieParams = session_get_cookie_params(); // Gets current cookies params.
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
    session_name($session_name); // Sets the session name to the one set above.
    session_start(); // Start the php session
    session_regenerate_id(true); // regenerated the session, delete the old one. 
}
function isLogged() {
    SessionStart();
    global $db;
    if(isset($_SESSION['id_user'], $_SESSION['login_string'])){
        $id_user = $_SESSION['id_user'];
        $login_string = $_SESSION['login_string'];
        $ip_address = $_SERVER['REMOTE_ADDR']; 
        $user_browser = $_SERVER['HTTP_USER_AGENT']; 
        $params = array($id_user);
        $results = $db->rawQuery("SELECT password FROM zero.users WHERE id_user = ? LIMIT 1", $params);
        if(count($results) === 1){
            $login_check = hash('sha512', $results[0]['password'].$ip_address.$user_browser);
            if($login_check === $login_string){
                return true;
            } else { return false; }
        } else { return false; }
    } else { return false; }   
}
?>
