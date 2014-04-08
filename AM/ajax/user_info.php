<?php

require '../lib_php/db.php';
require '../lib_php/user.php';

$user = new UserLogin($db);
$user->confirm_login();

    $data=$user->getUser();
    echo json_encode(array("name"=>$data->name,"user_level"=>$data->user_level,"username"=>$data->username));