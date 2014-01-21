<?php

require 'lib_php/db.php';
require 'lib_php/user.php';

$username = filter_var($_POST["username"]);
$password = filter_var($_POST["password"]);

$user = new UserLogin($db);

$user->logout();
header('Location: index.php');