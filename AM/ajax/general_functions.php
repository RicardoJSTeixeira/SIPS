<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require '../../ini/db.php';
require '../../ini/dbconnect.php';
require '../../ini/user.php';
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users($db);



switch ($action) {



    case "get_user_level"://ALL MARCAÇOES
               echo json_encode($user->user_level);
            break;
};
