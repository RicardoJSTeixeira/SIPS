<?php


$json_data = file_get_contents("db.json");
$db_config = (object) json_decode($json_data);

$host = "mysql:host=" . $db_config->ip . ";dbname=" . $db_config->database . ";charset=utf8";

try {
    $db = new PDO($host, $db_config->username, $db_config->password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

