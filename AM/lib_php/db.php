<?php
try {
    $oPropertiesJSON = file_get_contents("/opt/fscontact-server/modules/utils/pg/pg-settings.json");
    $oProperties = json_decode($oPropertiesJSON);

    $db = new PDO("pgsql:dbname=" . $oProperties->database . ";host=" . $oProperties->serverip, $oProperties->username, $oProperties->password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
