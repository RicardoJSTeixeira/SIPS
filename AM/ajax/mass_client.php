<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
set_time_limit(1);

require '../lib_php/db.php';
require '../lib_php/calendar.php';
require '../lib_php/user.php';
require '../lib_php/logger.php';

$user = new UserLogin($db);
$user->confirm_login();
$u = $user->getUser();
$log = new Logger($db, $u);
$ccm = filter_var_array($_POST["ccm"]);
$names = filter_var_array($_POST["cname"]);
$mnames = filter_var_array($_POST["cmname"]);
$lnames = filter_var_array($_POST["clname"]);
$moradas = filter_var_array($_POST["cmorada"]);
$local = filter_var_array($_POST["clocal"]);
$postal = filter_var_array($_POST["cpostal"]);
$tels = filter_var_array($_POST["ctel"]);
$tlm = filter_var_array($_POST["ctlm"]);
$emails = filter_var_array($_POST["cemail"]);
$cbd = filter_var_array($_POST["cbd"]);
$toissues = filter_var_array($_POST["ctoissue"]);
$rcm = filter_var($_POST["recomendado"]);
$query_log=array();
$query = "INSERT INTO vicidial_list (entry_date, status,user, list_id,extra1, first_name, middle_initial, last_name, address1, city, postal_code, phone_number, address3, email, date_of_birth, extra6, extra7) Values (NOW(), 'NEW', :user, :list_id, :ccm, :name, :mname, :lname, :morada, :local, :postal, :tel, :tlm, :email, :bday, :toissue, :rcm)";
$stmt = $db->prepare($query);
foreach ($names as $key => $name) {
    $query_log[$value["name"]]= $value["value"];
    $stmt->execute(array(":user" => $u->username, ":list_id" => $u->list_id, ":ccm" => $ccm[$key], ":name" => $name, ":mname" => $mnames[$key], ":lname" => $lnames[$key], ":morada" => $moradas[$key], ":local" => $local[$key], ":postal" => $postal[$key], ":tel" => $tels[$key], ":tlm" => $ctlm[$key], ":email" => $emails[$key], ":bday" => $cbd[$key], ":toissue" => $toissues[$key], ":rcm" => $rcm));
    $log->set($db->lastInsertId(), Logger::T_INS, Logger::S_CLT, json_encode(array("User" => $u->username, "LIST_ID" => $u->list_id, "EXTRA1" => $ccm[$key], "FIRST_NAME" => $name, "MIDDLE_INITIAL" => $mnames[$key], "LAST_NAME" => $lnames[$key], "ADDRESS1" => $moradas[$key], "CITY" => $local[$key], "POSTAL_CODE" => $postal[$key], "PHONE_NUMBER" => $tels[$key], "ADDRESS3" => $ctlm[$key], "EMAIL" => $emails[$key], "DATE_OF_BIRTH" => $cbd[$key], "EXTRA6" => $toissues[$key], "EXTRA7" => $rcm)), logger::A_APV);
}

echo json_encode(true);
