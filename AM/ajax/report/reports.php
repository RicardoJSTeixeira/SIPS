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
    case "novasMarc":
        include 'includes/novasMarc.php';
        break;
    case "lead_id_follow_up":
         include 'includes/novasLeadsFollowUp.php';
    default:
        break;
}