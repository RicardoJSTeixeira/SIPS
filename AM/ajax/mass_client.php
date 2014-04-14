<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
set_time_limit(1);

require '../lib_php/db.php';
require '../lib_php/calendar.php';
require '../lib_php/user.php';

$user = new UserLogin($db);
$user->confirm_login();
$u = $user->getUser();

$names = filter_var_array($_POST["cname"]);
$moradas = filter_var_array($_POST["cmorada"]);
$tels = filter_var_array($_POST["ctel"]);
$emails = filter_var_array($_POST["cemail"]);
$toissues = filter_var_array($_POST["ctoissue"]);

$query = "Insert Into vicidial_list (entry_date,status,user,list_id,first_name,address1,phone_number,email,extra6) Values (NOW(),'NEW',:user,:list_id,:name,:morada,:tel,:email,:toissue)";
$stmt = $db->prepare($query);
foreach ($names as $key => $name) {
    $stmt->execute(array(":user" => $u->username, ":list_id" => $u->list_id, ":name" => $name, ":morada" => $moradas[$key], ":tel" => $tels[$key], ":email" => $emails[$key], ":toissue" => $toissues[$key]));
}

echo json_encode(true);
