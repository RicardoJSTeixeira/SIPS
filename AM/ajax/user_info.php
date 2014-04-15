<?php

require '../lib_php/db.php';
require '../lib_php/user.php';

$user = new UserLogin($db);
$user->confirm_login();

    $data=$user->getUser();
    echo json_encode(array("name"=>(string)$data->name,"user_level"=>(int)$data->user_level,"username"=>(string)$data->username,"camp"=>(string)$data->campaign));