<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/ini/db.php";
require "$root/ini/dbconnect.php";
require "$root/ini/user.php";
require "$root/AM/lib_php/requests.php";
    
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users($db);

$requests=new requests($db, $user->user_level, $user->id);

switch ($action) {
    case "criar_relatorio_frota":
        echo json_encode($requests->create_relatorio_frota($data, $matricula, $km, $viatura, $ocorrencias, $comments));
        break;
}

    