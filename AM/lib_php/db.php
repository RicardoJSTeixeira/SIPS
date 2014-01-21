<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {$far.="../";}
define("ROOT", $far);

$json_data = file_get_contents(ROOT."/AM/config/db.json");
$db_config = (object) json_decode($json_data);

$host = "mysql:host=" . $db_config->ip . ";dbname=" . $db_config->database . ";charset=utf8";

try {
    $db = new PDO($host, $db_config->username, $db_config->password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

