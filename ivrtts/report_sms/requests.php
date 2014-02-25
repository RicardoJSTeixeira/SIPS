<?php
require("../../ini/db.php");

// post & get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

switch ($zero) {
    case 'get_sms_report' : get_sms($db);
        break;
    
    default : header("HTTP/1.1 500 Internal Server Error");
        exit;
}

function get_sms($db) {
    
    $stmt = $db->prepare("select status_date, destination, content, nr_sms, IF (status == 0, 'Delivered', IF (status == 1, 'Pending', IF (status == 2, 'Failed'))) as status, process from sms_status_report");
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $js = array("aaData" => $results);
    echo json_encode($js);
}