<?php

require 'lib/db.php';
require 'lib/user.php';

if (!empty($_POST) && isset($_POST["username"]) && isset($_POST["password"])) {
    $username = filter_var($_POST["username"]);
    $password = filter_var($_POST["password"]);

    $user = new UserLogin($db);
    if ($username == $user->login($username, $password)) {
        header('Location: index.php');
    } else {
        echo file_get_contents("view/login.html");
    }
} else {
    echo file_get_contents("view/login.html");
}