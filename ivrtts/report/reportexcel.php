<?php

ini_set('memory_limit', '256M');
require("../../ini/db.php");
//ob_start();
date_default_timezone_set('Europe/London');

function transpose($array) {
    array_unshift($array, null);
    return call_user_func_array('array_map', $array);
}

//vai dissecar a váriaveis  que vêm do Post e Get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

//PHPExcel/Classes/Writer/Excel2007/Chart.php file ficheiro alterado(linha 109 -119)hardcoded
//caso a variavel seja 1 entra no if e devolve o excell,se não:exit;
//inclui o ficheiro PHPExcel
require '../../ini/phpexcel/PHPExcel.php';

require './excelwraper.php';

$dataLinha1_Core1 = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/" . $tempo . "/" . $min . "/" . $max . "?campaign=$campaign_id");
$dataLinha1_Core2 = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/" . $tempo . "/" . $min . "/" . $max . "?campaign=$campaign_id&status=MSG001,MSG002,MSG003,MSG004,MSG005,MSG006,MSG007,NEW");
$dataLinha1_Core3 = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/" . $tempo . "/" . $min . "/" . $max . "?campaign=$campaign_id&status=$statuses");

$myData1 = array(json_decode($dataLinha1_Core1, true), json_decode($dataLinha1_Core2, true), json_decode($dataLinha1_Core3, true));

$dataLinha2_Core1 = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/" . $tempo . "/" . $min . "/" . $max . "?campaign=$campaign_id");
$dataLinha2_Core2 = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/" . $tempo . "/" . $min . "/" . $max . "?campaign=$campaign_id&status=MSG001,MSG002,MSG003,MSG004,MSG005,MSG006,MSG007,NEW");
$dataLinha2_Core3 = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/timeline/calls/" . $tempo . "/" . $min . "/" . $max . "?campaign=$campaign_id&status=$statuses");

$myData2 = array(json_decode($dataLinha2_Core1, true), json_decode($dataLinha2_Core2, true), json_decode($dataLinha2_Core3, true));

$dataTotal_Core = file_get_contents("http://localhost:10000/ccstats/v0/count/calls?by=database.campaign,status&database.campaign.oid=$campaign_id");
$dataTotal = json_decode($dataTotal_Core, true);
//status
$dataTotalPie_Core = file_get_contents("http://localhost:10000/ccstats/v0/count/calls?by=database.campaign,status&database.campaign.oid=$campaign_id");
$dataTotalPie = json_decode($dataTotalPie_Core, true);

$dataTotalHora_Core = file_get_contents("http://localhost:10000/ccstats/v0/sum/calls/length_in_sec?by=database.campaign,status&database.campaign.oid=$campaign_id");
$dataTotalHora = json_decode($dataTotalHora_Core, true);
//ob_start();
$toExcel = new excelwraper(New PHPExcel(), "report", 18, 10);

//TRANSFORM LINHA1

$dataExcel = array();
$finalData = array();

$dataExcel[] = array('+', 'Total Calls', 'Total Message', 'Total System Feedbacks');

foreach ($myData1 as $value) {

    foreach ($value as $data) {

        $finalData[$data['stamp']][] = $data['calls'];
    }
}

foreach ($finalData as $key => $value) {
    $temp = array();
    $temp[] = $key;
    foreach ($value as $value1) {

        $temp[] = $value1;
    }
    $dataExcel[] = $temp;
}

$toExcel->maketable($dataExcel, TRUE, 'Totals', NULL, NULL, 'chart1', 'r', 'lines', 'lines', TRUE, TRUE);

//TRANSFORM LINHA2
$dataExcel = array();
$finalData = array();

$dataExcel[] = array('+', 'Total Duration Calls', 'Total Durantion Message', 'Total Duration System Feedbacks');

foreach ($myData2 as $value) {

    foreach ($value as $data) {

        $finalData[$data['stamp']][] = $data['length'];
    }
}

foreach ($finalData as $key => $value) {
    $temp = array();
    $temp[] = $key;
    foreach ($value as $value1) {

        $temp[] = $value1;
        
    }

    $dataExcel[] = $temp;
}
$toExcel->maketable($dataExcel, TRUE, 'Duration of Calls in Seconds', NULL, NULL, 'chart1', 'r', 'lines', 'lines', TRUE, TRUE);
//TRANSFORM TOTAL
$p = array();
$pOutros = array('Outros');
$header = array('+', 'Total');
foreach ($dataTotal as $value) {
    switch ($value['_id']['status']['oid']) {
        case "MSG001":
        case "MSG002":
        case "MSG003":
        case "MSG004":
        case "MSG005":
        case "MSG006":
        case "MSG007":
        case "NEW":
            if (!isset($p[$value['_id']['status']['oid']])) {
                $p[$value['_id']['status']['oid']]['title'] = $value['_id']['status']['designation'];
            }
            $p[$value['_id']['status']['oid']][$ref] = $value['count'];
            break;
        default :
            $pOutros[$ref]+= $value['count'];
            break;
    }
}
$dataExcel = array();
$dataExcel[] = $header;

foreach ($p as $value) {
    $dataExcel[] = $value;
}

if (count($pOutros) > 1) {
    $dataExcel[] = $pOutros;
}

$toExcel->maketable(transpose($dataExcel), TRUE, 'Total Calls By Feedback', NULL, NULL, 'chart2', 'r', 'bars', 'bars', TRUE, TRUE);

//TRANFORM total/3600
$p = array();
$pOutros = array('Outros');
$header = array('+', 'Total');
foreach ($dataTotalHora as $value) {
    switch ($value['_id']['status']['oid']) {
        case "MSG001":
        case "MSG002":
        case "MSG003":
        case "MSG004":
        case "MSG005":
        case "MSG006":
        case "MSG007":
        case "NEW":
            if (!isset($p[$value['_id']['status']['oid']])) {
                $p[$value['_id']['status']['oid']]['title'] = $value['_id']['status']['designation'];
            }
            $p[$value['_id']['status']['oid']][$ref] = $value['sum'];
            break;
        default :
            $pOutros[$ref]+= round($value['sum']);
            break;
    }
}

$dataExcel = array();
$dataExcel[] = $header;

foreach ($p as $value) {
    $dataExcel[] = $value;
}

if (count($pOutros) > 1) {
    $dataExcel[] = $pOutros;
}

$toExcel->maketable(transpose($dataExcel), TRUE, 'Total Duration By Feedbacks', NULL, NULL, 'chart3', 'r', 'bars', 'bars', TRUE, TRUE);

//Transform Pie
$p = array();
$pOutros = array('Outros');
$header = array('+', 'Total');
foreach ($dataTotalPie as $value) {
    switch ($value['_id']['status']['oid']) {
        case "MSG001":
        case "MSG002":
        case "MSG003":
        case "MSG004":
        case "MSG005":
        case "MSG006":
        case "MSG007":
        case "NEW":
            if (!isset($p[$value['_id']['status']['oid']])) {
                $p[$value['_id']['status']['oid']]['title'] = $value['_id']['status']['designation'];
            }
            $p[$value['_id']['status']['oid']][$ref] = $value['count'];
            break;
        default :
            $pOutros[$ref]+= $value['count'];
            break;
    }
}
$dataExcel = array();
$dataExcel[] = $header;

foreach ($p as $value) {
    $dataExcel[] = $value;
}

if (count($pOutros) > 1) {
    $dataExcel[] = $pOutros;
}

$toExcel->maketable(($dataExcel), TRUE, 'Feedbacks', NULL, NULL, 'chart4', null, 'pie', NULL, TRUE, TRUE);

$toExcel->backGroundStyle('FFFFFF');

$toExcel->addsheet('Leads', 3, 0);

$header = array('Campanha:');

$title[] = $header;

$queryData = "SELECT `campaign_name` FROM `vicidial_campaigns` WHERE `campaign_id`=?";

$stmt = $db->prepare($queryData);

$stmt->execute(array($campaign_id));

$title[] = $stmt->fetch(PDO::FETCH_NUM);

$toExcel->maketable($title, FALSE);

////////////////////////////////////////////////////////////////////////////-------------------------------------------------
$data = array();

//MAX TRIES RECYCLE
$recycle = array();
$query = "select status,attempt_maximum from vicidial_lead_recycle where campaign_id=:campaign_id and active='Y'";
$stmt = $db->prepare($query);
$stmt->execute(array(":campaign_id" => $campaign_id));
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $recycle[$row["status"]] = $row["attempt_maximum"];
}

$header = array('Call Date', 'Call Duration (Sec.)', 'final state', ' Total Number Tries', 'Telephone Number', 'Lead ID', 'Message 1', 'Message 2', 'Max Tries');

$data[] = $header;

$queryData = "SELECT  vlg.call_date,vlg.length_in_sec,vstatus.status,vstatus.status_name,vl.called_count,vl.phone_number,vl.lead_id,vl.comments,vl.email,vl.called_since_last_reset FROM `vicidial_list` vl FORCE INDEX(list_id)
LEFT JOIN `vicidial_lists`vls ON vl.list_id = vls.list_id
LEFT JOIN vicidial_log vlg On vlg.lead_id = vl.lead_id
LEFT JOIN (SELECT vstat.status,vstat.status_name FROM vicidial_statuses vstat UNION ALL SELECT vcstat.status,vcstat.status_name FROM vicidial_campaign_statuses vcstat WHERE vcstat.campaign_id =?) vstatus ON vstatus.status =vlg.status WHERE vls.campaign_id=?";


$stmt = $db->prepare($queryData);
$stmt->execute(array($campaign_id, $campaign_id,));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $temp = (int) str_replace("Y", "", $row["called_since_last_reset"]);

    if (isset($recycle[$row["status"]])) {
        if ($temp >= $recycle[$row["status"]]) {
            $row["called_since_last_reset"] = "Sim";
        } else {
            $row["called_since_last_reset"] = "Não";
        }
    } else {
        $row["called_since_last_reset"] = "Sem limite";
    }
    unset($row["status"]);
    $data[] = $row;
}

$toExcel->maketable($data, FALSE);

$toExcel->selectsheet(0);

$toExcel->save('Report', TRUE);
ob_end_clean();

$toExcel->send();