<?php

require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
ini_set("display_errors", "1");

if ($action == "GetCampaignTotals") {
    $js['feitas'] = 0;
    $js['ouvidas'] = 0;
    $js['declinadas'] = 0;
    $js['ouvidas'] = 0;
    $js['naoatendidas'] = 0;

    $params = array($sent_campaign);
    $stmt = $db->prepare("SELECT count(*) as count FROM vicidial_log WHERE campaign_id = ?");
    $stmt->execute($params);
    $result0 = $stmt->fetchAll(PDO::FETCH_BOTH);

    $js['feitas'] = $result0[0]['count'];


    $params = array($sent_campaign);

    $stmt = $db->prepare("SELECT status, count(*) as count FROM vicidial_log WHERE campaign_id = ? GROUP BY status");
    $stmt->execute($params);
    $result1 = $stmt->fetchAll(PDO::FETCH_BOTH);

    foreach ($result1 as $key => $value) {
        switch ($value['status']) {
            case "MSG001": {
                    $js['ouvidas'] += $value['count'];
                    break;
                }
            case "MSG002": {
                    $js['declinadas'] += $value['count'];
                    break;
                }
            case "MSG003": {
                    $js['declinadas'] += $value['count'];
                    break;
                }
        }
    }

    $js['atendidas'] = $js['ouvidas'] + $js['declinadas'];
    $js['naoatendidas'] = $js['feitas'] - $js['atendidas'];


    echo json_encode($js);
}

if ($action == "GetDatabaseTotals") {

    $js['MSG001'] = 0;
    $js['MSG002'] = 0;
    $js['MSG003'] = 0;
    $js['MSG004'] = 0;
    $js['MSG005'] = 0;
    $js['MSG006'] = 0;
    $js['MSG007'] = 0;
    $js['NEW'] = 0;
    $js['OUTROS'] = 0;

    $params0 = array($sent_campaign);

    $stmt = $db->prepare("SELECT list_id FROM vicidial_lists WHERE campaign_id = ?");
    $stmt->execute($params0);
    $result0 = $stmt->fetchAll(PDO::FETCH_BOTH);

    $params1 = array($result0[0]['list_id']);

    $stmt = $db->prepare("SELECT status FROM vicidial_list WHERE list_id = ?");
    $stmt->execute($params1);
    $result1 = $stmt->fetchAll(PDO::FETCH_BOTH);

    foreach ($result1 as $key => $value) {
        switch ($value['status']) {
            case "MSG001": {
                    $js['MSG001'] ++;
                    break;
                }
            case "MSG002": {
                    $js['MSG002'] ++;
                    break;
                }
            case "MSG003": {
                    $js['MSG003'] ++;
                    break;
                }
            case "MSG004": {
                    $js['MSG004'] ++;
                    break;
                }
            case "MSG005": {
                    $js['MSG005'] ++;
                    break;
                }
            case "MSG006": {
                    $js['MSG006'] ++;
                    break;
                }
            case "MSG007": {
                    $js['MSG007'] ++;
                    break;
                }
            case "NEW": {
                    $js['NEW'] ++;
                    break;
                }
            default : {
                    $js['OUTROS'] ++;
                    break;
                }
        }
    }

    echo json_encode($js);
}

if ($action == "GetRealtimeTotals") {


    $js['ouvidas']['Hour'] = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23);
    $js['ouvidas']['Count'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    $js['declinadas']['Hour'] = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23);
    $js['declinadas']['Count'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

    $js['feitas']['Hour'] = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23);
    $js['feitas']['Count'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);


    $today = date("Y-m-d");
    $params0 = array($sent_campaign, $today);
    $stmt = $db->prepare("SELECT Hour(call_date) AS Hour, count(status) AS Count, status FROM vicidial_log WHERE campaign_id= ? AND call_date > ? GROUP BY Hour(call_date), status ORDER BY call_date");
    $stmt->execute($params0);
    $result0 = $stmt->fetchAll(PDO::FETCH_BOTH);

    foreach ($result0 as $key0 => $value0) {

        foreach ($js['feitas']['Hour'] as $key4 => $value4) {
            if ($value0['Hour'] == $value4) {
                $js['feitas']['Count'][$value4] += $value0['Count'];
            }
        }


        switch ($value0['status']) {
            case 'MSG001' : {
                    foreach ($js['ouvidas']['Hour'] as $key1 => $value1) {
                        if ($value0['Hour'] == $value1) {
                            $js['ouvidas']['Count'][$value1] = $value0['Count'];
                        }
                    }
                    break;
                }
            case 'MSG002' : {
                    foreach ($js['declinadas']['Hour'] as $key2 => $value2) {
                        if ($value0['Hour'] == $value2) {
                            $js['declinadas']['Count'][$value2] += $value0['Count'];
                        }
                    }
                    break;
                }
            case 'MSG003' : {
                    foreach ($js['declinadas']['Hour'] as $key3 => $value3) {
                        if ($value0['Hour'] == $value3) {
                            $js['declinadas']['Count'][$value3] += $value0['Count'];
                        }
                    }
                    break;
                }
        }
    }


    echo json_encode($js);
}