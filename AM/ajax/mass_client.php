<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
set_time_limit(1);

require '../lib_php/db.php';
require '../lib_php/calendar.php';
require '../lib_php/user.php';

/** @var PDO $db */
$user = new UserLogin($db);
$user->confirm_login();
$u = $user->getUser();

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

$query = "INSERT INTO vicidial_list (entry_date, status,user, list_id,extra1, first_name, middle_initial, last_name, address1, city, postal_code, phone_number, address3, email, date_of_birth, extra6, extra7, security_phrase) Values (NOW(), 'NEW', :user, :list_id, :ccm, :name, :mname, :lname, :morada, :local, :postal, :tel, :tlm, :email, :bday, :toissue, :rcm, 'SPICE')";
$stmt = $db->prepare($query);
foreach ($names as $key => $name) {
    $stmt->execute(array(":user" => $u->username, ":list_id" => $u->list_id, ":ccm" => $ccm[$key], ":name" => $name, ":mname" => $mnames[$key], ":lname" => $lnames[$key], ":morada" => $moradas[$key], ":local" => $local[$key], ":postal" => $postal[$key], ":tel" => $tels[$key], ":tlm" => $tlm[$key], ":email" => $emails[$key], ":bday" => $cbd[$key], ":toissue" => $toissues[$key], ":rcm" => $rcm));
}

echo json_encode(true);
