<?php

require("../../ini/dbconnect.php");
if (isset($_GET['client'])) {
    $client = $_GET['client'];
} else {
    $client = $_POST['client'];
}
if (isset($_GET['campaign_id'])) {
    $campaign_id = $_GET['campaign_id'];
} else {
    $campaign_id = $_POST['campaign_id'];
}
if (isset($_GET['lead_id'])) {
    $lead_id = $_GET['lead_id'];
} else {
    $lead_id = $_POST['lead_id'];
}
if (isset($_GET['dispoAtt'])) {
    $dispoAtt = $_GET['dispoAtt'];
} else {
    $dispoAtt = $_POST['dispoAtt'];
}


$oPost=mysql_real_escape_string(json_encode($_POST));
$oGet=mysql_real_escape_string(json_encode($_GET));
$logQuery="INSERT INTO sales_actions (lead_id, post, get, type) VALUES ('$lead_id', '$oPost', '$oGet', 'non_sale');";
mysql_query($logQuery, $link);

switch ($client) {
    case 'connecta' : {
            confirmacao($lead_id, $dispoAtt, $link);
            break;
        }
}

function confirmacao($lead_id, $dispoAtt, $link) {

    function removeConfirm($lead_id, $link) {
        $qdelete = "update crm_confirm_feedback_last set sale = '1' where lead_id='" . mysql_real_escape_string($lead_id) . "';";
        mysql_query($qdelete, $link) or die(mysql_error());
    }

    if ($dispoAtt["completed"] == 'true') {
        removeConfirm($lead_id, $link);
    } else {
        return false;
    }
}
