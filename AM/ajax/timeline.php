<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Lisbon');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);


require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/user.php";
require "$root/AM/lib_php/logger.php";

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();
$log = new Logger($db, $user->getUser());

switch ($action) {

    case "get_filtered":
        echo json_encode($log->get_all_filtered($section, $date_start, $date_end));
        break;

    case "get_all":
        echo json_encode($log->getAll());
        break;
}