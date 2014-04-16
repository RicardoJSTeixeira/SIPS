<?php

ini_set(display_errors, 1);
error_reporting(E_ALL);

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

require '../report_builder/excelwraper.php';
require '../phpexcel/Classes/PHPExcel.php';
require '../../../ini/db.php';


$server = "http://$VARDB_server:10000/ccstats/v0/";
//var_dump($server);


switch ($action) {
    case 'outTotais': outTotais($campaign, $start, $end, $db, $server);
        break;
    case 'outPause': outPause($campaign, $start, $end, $db, $server);
        break;
    case 'outHour': outHour($campaign, $start, $end, $pause, $db, $server);
        break;
    case 'outTime': outTime($campaign, $start, $end, $pause, $db, $server);
        break;
}

function outTotais($campaign, $start, $end, $db, $server) {
    $series[] = array(' ', 'Totals');

    $sql = "select * FROM `vicidial_campaign_statuses` Where campaign_id ='" . $campaign . "' and selectable ='y'";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $Human = array();
    $Sucess = array();
    $DNC = array();
    $util = array();
    $NUtil = array();
    $Unworkable = array();
    $Callback = array();
    $Complete = array();
    $Drop[] = 'Drop';

    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
        if ($data->human_answered === 'Y') {
            $Human[] = $data->status;
        }
        if ($data->sale === 'Y') { //sucesso
            $Sucess[] = $data->status;
        }
        if ($data->dnc === 'Y') { // DNC
            $DNC[] = $data->status;
        }
        if ($data->customer_contact === 'Y') { //util
            $util[] = $data->status;
        }
        if ($data->not_interested === 'Y') { // Nao util
            $NUtil[] = $data->status;
        }
        if ($data->unworkable === 'Y') { // Unworkable
            $Unworkable[] = $data->status;
        }
        if ($data->scheduled_callback === 'Y') { //Callback
            $Callback[] = $data->status;
        }
        if ($data->completed === 'Y') { //Complete
            $Complete[] = $data->status;
        }
    }

    $get_total = file_get_contents($server."total/calls/$start/$end?campaign=$campaign");
    $total_content = json_decode($get_total);
    $series[] = array('Total', $total_content[0]->calls);

    if ($Human) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $Human));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('Human', $total_content[0]->calls);
        } else {
            $series[] = array('Human', 0);
        }
    }
    if ($Sucess) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $Sucess));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('Sucesso', $total_content[0]->calls);
        } else {
            $series[] = array('Sucesso', 0);
        }
    }
    if ($DNC) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $DNC));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('DNC', $total_content[0]->calls);
        } else {
            $series[] = array('DNC', 0);
        }
    }
    if ($util) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $util));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('Util', $total_content[0]->calls);
        } else {
            $series[] = array('Util', 0);
        }
    }
    if ($NUtil) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $NUtil));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('N Util', $total_content[0]->calls);
        } else {
            $series[] = array('N Util', 0);
        }
    }
    if ($Unworkable) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $Unworkable));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('Unwork', $total_content[0]->calls);
        } else {
            $series[] = array('Unwork', 0);
        }
    }
    if ($Callback) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $Callback));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('Callback', $total_content[0]->calls);
        } else {
            $series[] = array('Callback', 0);
        }
    }
    if ($Complete) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $Complete));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('Complete', $total_content[0]->calls);
        } else {
            $series[] = array('Complete', 0);
        }
    }
    if ($Drop) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?campaign=$campaign&status=" . implode(',', $Drop));
        $total_content = json_decode($get_total);
        if ($total_content) {
            $series[] = array('Drop', $total_content[0]->calls);
        } else {
            $series[] = array('Drop', 0);
        }
    }

    creat($series, 'Totais', 'bars');
}

function outPause($campaign, $start, $end, $db, $server) {
    $pause = array();
    $series[] = array(' ', 'Pause');

    $sql_pausas = 'select pause_code, pause_code_name from vicidial_pause_codes where campaign_id like "' . $campaign . '" ';
    $query_pausas = $db->prepare($sql_pausas);
    $query_pausas->execute();
    While ($pausas = $query_pausas->fetch(PDO::FETCH_OBJ)) {
        $pause[$pausas->pause_code] = $pausas->pause_code_name;
    }

    $get_total = file_get_contents($server . "total/agent_log/$start/$end?by=sub_status&campaign=$campaign");
    $total_content = json_decode($get_total);

    foreach ($total_content as $value) {
        $name = $value->sub_status;
        if (isset($pause[$name])) {
            $name = $pause[$value->sub_status];
        }

        if ($value->sub_status <> NULL) {
            $series[] = array($name, $value->sum_pause);
        }
    }

    creat($series, 'Pause', 'bars');
}

function outHour($campaign, $start, $end, $pause, $db, $server) {
    $series = explode(",", $pause);

    $sql = "select * FROM `vicidial_campaign_statuses` Where campaign_id ='" . $campaign . "' and selectable ='y'";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $Human = array();
    $Sucess = array();
    $DNC = array();
    $util = array();
    $NUtil = array();
    $Unworkable = array();
    $Callback = array();
    $Complete = array();
    $Drop[] = 'Drop';

    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
        if ($data->human_answered === 'Y') {
            $Human[] = $data->status;
        }
        if ($data->sale === 'Y') { //sucesso
            $Sucess[] = $data->status;
        }
        if ($data->dnc === 'Y') { // DNC
            $DNC[] = $data->status;
        }
        if ($data->customer_contact === 'Y') { //util
            $util[] = $data->status;
        }
        if ($data->not_interested === 'Y') { // Nao util
            $NUtil[] = $data->status;
        }
        if ($data->unworkable === 'Y') { // Unworkable
            $Unworkable[] = $data->status;
        }
        if ($data->scheduled_callback === 'Y') { //Callback
            $Callback[] = $data->status;
        }
        if ($data->completed === 'Y') { //Complete
            $Complete[] = $data->status;
        }
    }

    if (in_array("total", $series)) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign");
        $total_content = json_decode($get_total);

        foreach ($total_content as $value) {
            $total[$value->hour] = round($value->length / 60);
        }
    }
    if (in_array("talk", $series)) {
        $get_total1 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Human));
        $total_content1 = json_decode($get_total1);

        foreach ($total_content1 as $value) {
            $talk[$value->hour] = round($value->length / 60);
        }
    }
    if (in_array("drop", $series)) {
        $get_total2 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=DROP");
        $total_content2 = json_decode($get_total2);

        foreach ($total_content2 as $value) {
            $drop[$value->hour] = round($value->length / 60);
        }
    }
    if (in_array("util", $series)) {
        $get_total3 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $util));
        $total_content3 = json_decode($get_total3);

        foreach ($total_content3 as $value) {
            $util[$value->hour] = round($value->length / 60);
        }
    }
    if (in_array("sucesso", $series)) {
        $get_total4 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Sucess));
        $total_content4 = json_decode($get_total4);

        foreach ($total_content4 as $value) {
            $sucesso[$value->hour] = round($value->length / 60);
        }
    }
    if (in_array("callback", $series)) {
        $get_total5 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Callback));
        $total_content5 = json_decode($get_total5);

        foreach ($total_content5 as $value) {
            $callback[$value->hour] = round($value->length / 60);
        }
    }
    if (in_array("complete", $series)) {
        $get_total6 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Complete));
        $total_content6 = json_decode($get_total6);

        foreach ($total_content6 as $value) {
            $complete[$value->hour] = round($value->length / 60);
        }
    }
    if (in_array("nutil", $series)) {
        $get_total7 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $NUtil));
        $total_content7 = json_decode($get_total7);

        foreach ($total_content7 as $value) {
            $nutil[$value->hour] = round($value->length / 60);
        }
    }
    if (in_array("unwork", $series)) {
        $get_total8 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Unworkable));
        $total_content8 = json_decode($get_total8);

        foreach ($total_content8 as $value) {
            $unwork[$value->hour] = round($value->length / 60);
        }
    }

    $ar[0][0] = '+';
    for ($i = 1; $i <= count($series); $i++) {
        $ar[0][$i] = $series[$i - 1];
    }

    for ($i = 1; $i <= 24; $i++) {
        $ar[$i][0] = $i;
        for ($y = 0; $y <= count($series) - 1; $y++) {
            if ($series[$y] === 'total') {
                $ar[$i][] = $total[$i - 1];
            }
            if ($series[$y] === 'talk') {
                $ar[$i][] = $talk[$i - 1];
            }
            if ($series[$y] === 'drop') {
                $ar[$i][] = $drop[$i - 1];
            }
            if ($series[$y] === 'util') {
                $ar[$i][] = $util[$i - 1];
            }
            if ($series[$y] === 'sucesso') {
                $ar[$i][] = $sucesso[$i - 1];
            }
            if ($series[$y] === 'callback') {
                $ar[$i][] = $callback[$i - 1];
            }
            if ($series[$y] === 'complete') {
                $ar[$i][] = $complete[$i - 1];
            }
            if ($series[$y] === 'nutil') {
                $ar[$i][] = $nutil[$i - 1];
            }
            if ($series[$y] === 'unwork') {
                $ar[$i][] = $unwork[$i - 1];
            }
        }
    }


    creat($ar, 'Hours', 'bars');
}

function outTime($campaign, $start, $end, $pause, $db, $server) {
    $series = explode(",", $pause);

    $sql = "select * FROM `vicidial_campaign_statuses` Where campaign_id ='" . $campaign . "' and selectable ='y'";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $Human = array();
    $Sucess = array();
    $DNC = array();
    $util = array();
    $NUtil = array();
    $Unworkable = array();
    $Callback = array();
    $Complete = array();
    $Drop[] = 'Drop';

    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
        if ($data->human_answered === 'Y') {
            $Human[] = $data->status;
        }
        if ($data->sale === 'Y') { //sucesso
            $Sucess[] = $data->status;
        }
        if ($data->dnc === 'Y') { // DNC
            $DNC[] = $data->status;
        }
        if ($data->customer_contact === 'Y') { //util
            $util[] = $data->status;
        }
        if ($data->not_interested === 'Y') { // Nao util
            $NUtil[] = $data->status;
        }
        if ($data->unworkable === 'Y') { // Unworkable
            $Unworkable[] = $data->status;
        }
        if ($data->scheduled_callback === 'Y') { //Callback
            $Callback[] = $data->status;
        }
        if ($data->completed === 'Y') { //Complete
            $Complete[] = $data->status;
        }
    }

    if (in_array("total", $series)) {
        $get_total = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign");
        $total_content = json_decode($get_total);

        foreach ($total_content as $value) {
            $total[$value->hour] = $value->calls;
        }
    }
    if (in_array("talk", $series)) {
        $get_total1 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Human));
        $total_content1 = json_decode($get_total1);

        foreach ($total_content1 as $value) {
            $talk[$value->hour] = $value->calls;
        }
    }
    if (in_array("drop", $series)) {
        $get_total2 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=DROP");
        $total_content2 = json_decode($get_total2);

        foreach ($total_content2 as $value) {
            $drop[$value->hour] = $value->calls;
        }
    }
    if (in_array("util", $series)) {
        $get_total3 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $util));
        $total_content3 = json_decode($get_total3);

        foreach ($total_content3 as $value) {
            $util[$value->hour] = $value->calls;
        }
    }
    if (in_array("sucesso", $series)) {
        $get_total4 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Sucess));
        $total_content4 = json_decode($get_total4);

        foreach ($total_content4 as $value) {
            $sucesso[$value->hour] = $value->calls;
        }
    }
    if (in_array("callback", $series)) {
        $get_total5 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Callback));
        $total_content5 = json_decode($get_total5);

        foreach ($total_content5 as $value) {
            $callback[$value->hour] = $value->calls;
        }
    }
    if (in_array("complete", $series)) {
        $get_total6 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Complete));
        $total_content6 = json_decode($get_total6);

        foreach ($total_content6 as $value) {
            $complete[$value->hour] = $value->calls;
        }
    }
    if (in_array("nutil", $series)) {
        $get_total7 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $NUtil));
        $total_content7 = json_decode($get_total7);

        foreach ($total_content7 as $value) {
            $nutil[$value->hour] = $value->calls;
        }
    }
    if (in_array("unwork", $series)) {
        $get_total8 = file_get_contents($server . "total/calls/$start/$end?by=hour&campaign=$campaign&status=" . implode(',', $Unworkable));
        $total_content8 = json_decode($get_total8);

        foreach ($total_content8 as $value) {
            $unwork[$value->hour] = $value->calls;
        }
    }

    $ar[0][0] = '+';
    for ($i = 1; $i <= count($series); $i++) {
        $ar[0][$i] = $series[$i - 1];
    }

    for ($i = 1; $i <= 24; $i++) {
        $ar[$i][0] = $i;
        for ($y = 0; $y <= count($series) - 1; $y++) {
            if ($series[$y] === 'total') {
                $ar[$i][] = $total[$i - 1];
            }
            if ($series[$y] === 'talk') {
                $ar[$i][] = $talk[$i - 1];
            }
            if ($series[$y] === 'drop') {
                $ar[$i][] = $drop[$i - 1];
            }
            if ($series[$y] === 'util') {
                $ar[$i][] = $util[$i - 1];
            }
            if ($series[$y] === 'sucesso') {
                $ar[$i][] = $sucesso[$i - 1];
            }
            if ($series[$y] === 'callback') {
                $ar[$i][] = $callback[$i - 1];
            }
            if ($series[$y] === 'complete') {
                $ar[$i][] = $complete[$i - 1];
            }
            if ($series[$y] === 'nutil') {
                $ar[$i][] = $nutil[$i - 1];
            }
            if ($series[$y] === 'unwork') {
                $ar[$i][] = $unwork[$i - 1];
            }
        }
    }


    creat($ar, 'Timeline', 'lines');
}

function creat($series, $titule, $type) {
    $toExcel = new excelwraper(New PHPExcel(), 'Report', 18, 10);
    ob_start();
    date_default_timezone_set('Europe/London');
    $toExcel->maketable($series, TRUE, $titule, NULL, NULL, 'chart2', 'r', $type, $type, TRUE, TRUE);
    $toExcel->backGroundStyle('FFFFFF');
    $toExcel->save('Report', TRUE);
    ob_end_clean();

    $toExcel->send();
}
