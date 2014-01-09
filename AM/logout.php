<?php

require 'lib/db.php';
require 'lib/user.php';

$username = filter_var($_POST["username"]);
$password = filter_var($_POST["password"]);

$user = new UserLogin($db);

$user->logout();
header('Location: index.php');