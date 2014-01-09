<?php

require '../lib/db.php';
require '../lib/user.php';

$user = new UserLogin($db);
$user->confirm_login();

    $data=$user->getUser();
    echo json_encode(array("name"=>$data->name));