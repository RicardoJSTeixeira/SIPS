<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Lisbon');
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


foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
$user = new UserLogin($db);
$user->confirm_login();

switch ($action) {



    case "get_log_history":
        $query = "select * from spice_log order by event_date";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["note"] = json_decode($row["note"]);
            $result[] = $row;
        }
        echo $result;
        break;
}