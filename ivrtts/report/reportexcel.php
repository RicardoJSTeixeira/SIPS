<?php

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


$tempo = json_decode($tempo, true);

$dataLinha1_Core = file_get_contents("http://localhost:10000/ccstats/v0/count/calls?by=database.campaign,status," . implode($tempo, ',') . "&database.campaign.oid=$campaign_id");
$dataLinha1 = json_decode($dataLinha1_Core, true);

$dataLinha2_Core = file_get_contents("http://localhost:10000/ccstats/v0/avg/calls/length_in_sec?by=database.campaign,status," . implode($tempo, ',') . "&database.campaign.oid=$campaign_id");
$dataLinha2 = json_decode($dataLinha2_Core, true);

$dataTotal_Core = file_get_contents("http://localhost:10000/ccstats/v0/count/calls?by=database.campaign,status&database.campaign.oid=$campaign_id");
$dataTotal = json_decode($dataTotal_Core, true);

$dataTotalPie_Core = file_get_contents("http://localhost:10000/ccstats/v0/count/calls?by=database.campaign,status&database.campaign.oid=$campaign_id");
$dataTotalPie = json_decode($dataTotalPie_Core, true);

$dataTotalHora_Core = file_get_contents("http://localhost:10000/ccstats/v0/sum/calls/length_in_sec?by=database.campaign,status&database.campaign.oid=$campaign_id");
$dataTotalHora = json_decode($dataTotalHora_Core, true);


$data=(array(
		array('+',	2010,	2011,	2012),
		array('Q1',   12,   15,		21),
		array('Q2',   56,   73,		86),
		array('Q3',   52,   61,		69),
		array('Q4',   30,   32,		0),
	));

$toExcel = new excelwraper(New PHPExcel(), "report");

$toExcel->maketable($data, FALSE);
$toExcel->maketable($data, FALSE);
$toExcel->maketable($data, FALSE);
$toExcel->maketable($data, FALSE);
$toExcel->backGroundStyle('FFFFFF');
$toExcel->addsheet('MoreTables');
$toExcel->maketable($data, FALSE);
$toExcel->maketable($data, FALSE);
$toExcel->maketable($data, FALSE);
$toExcel->maketable($data, FALSE);
$toExcel->backGroundStyle('FFFFFF');

$toExcel->addsheet('ReportGraph');

//TRANSFORM LINHA1
$p = array();
$pOutros = array('Outros');
$header = array('+');
foreach ($dataLinha1 as $value) {
    $ref = "";
    foreach ($tempo as $tempinho) {
        $ref.="-" . $value['_id'][$tempinho];
    }
    $ref = ltrim($ref, '-');

    if (!isset($header[$ref])) {
        $header[$ref] = $ref;
    }

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

$toExcel->maketable(transpose($dataExcel),TRUE, 'Totais', NULL, NULL, 'chart1', 'r', 'lines', 'lines', TRUE, TRUE);



//TRANSFORM LINHA2
$p = array();
$pOutros = array('Outros');
$header = array('+');

foreach ($dataLinha2 as $value) {
    $ref = "";
    foreach ($tempo as $tempinho) {
        $ref.="-" . $value['_id'][$tempinho];
    }
    $ref = ltrim($ref, '-');

    if (!isset($header[$ref])) {
        $header[$ref] = $ref;
    }
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
            $p[$value['_id']['status']['oid']][$ref] = $value['avg'];
            break;
        default :
            $pOutros[$ref]+= round($value['avg']);
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

$toExcel->maketable(transpose($dataExcel), TRUE, 'Media da Duração da Chamada em Minutos', NULL, NULL, 'chart1', 'r', 'lines', 'lines', TRUE, TRUE);



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


$toExcel->maketable(transpose($dataExcel), TRUE, 'Total Chamadas por Feedback', NULL, NULL, 'chart2', 'r', 'bars', 'bars', TRUE, TRUE);



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



$toExcel->maketable(transpose($dataExcel), TRUE, 'Duração total por Feedback', NULL, NULL, 'chart3', 'r', 'bars', 'bars', TRUE, TRUE);




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

$toExcel->selectsheet(0);

$toExcel->save('Report', TRUE);
//ob_end_clean();

$toExcel->send();
