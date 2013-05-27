<?php

date_default_timezone_set('Europe/Lisbon');
$event_date = date("Y-m-d H:i:s");
$user = $_SERVER["PHP_AUTH_USER"];
require("../../ini/_json_convert.php");
require("../../ini/dbconnect.php");
require("../../ini/functions.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

if ($action == "get_email_list") {
    $aColumns = array('sent_by_campaign', 'sent_by_user', 'event_date', 'sent_to_client', 'sent_to_email', 'sent_to_result');
    $sQuery = "
            SELECT sent_by_campaign, sent_by_user, event_date, sent_to_client, sent_to_email, sent_to_result FROM email_log WHERE event_date between '$sent_data 00:00:00' AND '$sent_data 23:59:59'
            ";
    //echo $sQuery;
    $rResult = mysql_query($sQuery, $link) or die(mysql_error());
    $output = array("aaData" => array());

    while ($aRow = mysql_fetch_array($rResult)) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            switch ($aColumns[$i]) {


                case "sent_by_campaign": {
                        $querym = mysql_query("SELECT campaign_name FROM vicidial_campaigns WHERE campaign_id='" . $aRow[$aColumns[$i]] . "'", $link);
                        $querym = mysql_fetch_row($querym);
                        $row[] = $querym[0];
                    } break;
                case "sent_to_result": if ($aRow['sent_to_result'] == "SENT") {
                        $row[] = "Enviado";
                    } else {
                        $row[] = "Erro ao Enviar";
                    } break;
                default : $row[] = $aRow[$aColumns[$i]];
                    break;
            }
        }
        $output['aaData'][] = $row;
    }
    echo __json_encode($output);
}


if ($action == "get_sales_list") {
    $aColumns = array('lead_id', 'full_name', 'first_name', 'phone_number', 'call_date', 'verfolha', 'verfolha2', 'vercli');
    $sQuery = "SELECT a.lead_id, b.full_name, first_name, a.phone_number, call_date FROM vicidial_list a INNER JOIN vicidial_users b ON a.user=b.user INNER JOIN vicidial_log c ON a.lead_id=c.lead_id  WHERE c.status='S00005' AND call_date between '$sent_data 00:00:00' AND '$sent_data 23:59:59'";
    //echo $sQuery;
    $rResult = mysql_query($sQuery, $link) or die(mysql_error());
    $output = array("aaData" => array());

    while ($aRow = mysql_fetch_array($rResult)) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            switch ($aColumns[$i]) {
                case "verfolha": $row[] = "<img onclick=dadoscli('" . $aRow['lead_id'] . "') src='../../images/icons/document_inspector_16.png'>";
                    break;
                case "verfolha2": $row[] = "<img onclick=dadoscli2('" . $aRow['lead_id'] . "') src='../../images/icons/document_inspector_16.png'>";
                    break;
                case "vercli": $row[] = "<img onclick=vercli('" . $aRow['lead_id'] . "') src='../../images/icons/document_inspector_16.png'>";
                    break;
                case "call_date": $row[] = date("H:i", strtotime($aRow[$aColumns[$i]]));
                    break;
                case "full_name": $row[] = ucwords($aRow[$aColumns[$i]]);
                    break;
                default : $row[] = $aRow[$aColumns[$i]];
                    break;
            }
        }
        $output['aaData'][] = $row;
    }
    echo __json_encode($output);
}
?>