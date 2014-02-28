<?php
#error_reporting(E_ALL ^ E_DEPRECATED);
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
    case 'get_sms_report' : get_sms($db, $filters, $start_date, $end_date);
        break;
    
    default : header("HTTP/1.1 500 Internal Server Error");
        exit;
}

function get_sms($db, $filters, $start_date, $end_date) {
    
    
    
   # echo "select status_date, destination, content, nr_sms, IF (status = 0, 'Delivered', IF (status = 1, 'Pending', IF (status = 2, 'Failed', 'Unknown'))) as status, process from sms.sms_status_report where status_date between '$start_date' and '$end_date'";
    $stmt = $db->prepare("select status_date, destination, content, nr_sms, IF (status = 0, 'Delivered', IF (status = 1, 'Pending', IF (status = 2, 'Failed', 'Unknown'))) as status, process from sms.sms_status_report where status_date between '$start_date 00:00:01' and '$end_date 23:59:59' and status in ($filters)");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_NUM);
    
    
    
    $js = array("aaData" => $results);
    echo json_encode($js);
}