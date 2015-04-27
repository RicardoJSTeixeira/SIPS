<?php
/*require 'lib_php/db.php';
require 'lib_php/user.php';

$username = filter_var($_POST["username"]);
$password = filter_var($_POST["password"]);

$user = new UserLogin($db);
if ($user->confirm_login()) {
    echo file_get_contents("view/spice.html");
} else {
    header("location: login.php");
}*/
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require '../../fs/php/plugins/pdo.php';
require '../../fs/php/classes/user.php';
require '../../fs/php/plugins/predis/redis_config.php';



$user = new user($db);
$user->confirm_login();

echo '<br/>'; //$single_server;

$client = new Predis\Client($single_server);


$retlength = $client->llen('list:users:socket:main@' . $_SESSION['user']->domain);
$retval = $client->lrange('list:users:socket:main@' . $_SESSION['user']->domain, 0, $retlength);
//var_dump(in_array( $_SESSION['user']->username, $retval));
//var_dump($retval);
//var_dump($_SESSION['user']);


if ($user->confirm_login() && !in_array($_SESSION['user']->username, $retval)) {
    echo file_get_contents("view/spice.html");
} else {
    echo file_get_contents("view/spice.html");
    //header("location:login.php");
}*/
?>