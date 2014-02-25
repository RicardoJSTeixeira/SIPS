<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', 'On');
require("../../ini/db.php");

// post & get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

switch ($action) {
    case 'get_sms_report' : get_sms($db);
        break;
    
    default : header("HTTP/1.1 500 Internal Server Error");
        exit;
}

function get_sms($db) {
    
    $stmt = $db->prepare("select status_date, destination, content, nr_sms, IF (status = 0, 'Delivered', IF (status = 1, 'Pending', IF (status = 2, 'Failed', 'Unknown'))) as status, process from sms.sms_status_report");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_NUM);
    $js = array("aaData" => $results);
    echo json_encode($js);
}