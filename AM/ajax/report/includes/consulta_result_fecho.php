<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "consulta_result_fecho_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

fputcsv($output, array(
    'User',
    'Total de consultas',
    'Novas',
    'Remarcadas',
    'Total Abertas',
    '% Abertas',
    'Fechadas',
    '% Fechadas',), ";");

$query_log = "SELECT a.consulta,a.exame,a.venda,a.closed,a.terceira_pessoa,c.user,c.user_level,c.full_name,a.left_ear,a.right_ear,c.closer_campaigns from spice_consulta a  inner join  vicidial_users c on c.user=a.user where c.user_group='SPICE' and a.data between :data_inicial and :data_final ";
$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));
$info = array();
$total = array();
$total["total_consulta"] = 0;
$total["total_consulta_aberta"] = 0;
$total["total_consulta_nova"] = 0;
$total["total_consulta_remarcada"] = 0;
$total["total_consulta_fechada"] = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($info[$row["user"]]) {
        $info[$row["user"]]["total_consulta"] += 1;
        $info[$row["user"]]["total_consulta_nova"] += (int) $row["changed"] == 0 ? 1 : 0;
        $info[$row["user"]]["total_consulta_remarcada"] += (int) $row["changed"];
        $info[$row["user"]]["total_consulta_aberta"] +=(int) $row["closed"] == 0 ? 1 : 0;
        $info[$row["user"]]["total_consulta_fechada"] += (int) $row["closed"];
    } else {
        $info[$row["user"]]["total_consulta"] = 1;
        $info[$row["user"]]["total_consulta_nova"] = (int) $row["changed"] == 0 ? 1 : 0;
        $info[$row["user"]]["total_consulta_remarcada"] = (int) $row["changed"];
        $info[$row["user"]]["total_consulta_aberta"] = (int) $row["closed"] == 0 ? 1 : 0;
        $info[$row["user"]]["total_consulta_fechada"] = (int) $row["closed"];
        $info[$row["user"]]["total_consulta"] = $info[$row["user"]]["total_consulta_aberta"] + $info[$row["user"]]["total_consulta_fechada"];
        $info[$row["user"]]["user"] = $row["user"];
        $info[$row["user"]]["user_level"] = $row["user_level"];
        $info[$row["user"]]["full_name"] = $row["full_name"];
        $info[$row["user"]]["children"] = json_decode($row["closer_campaigns"]);
    }
}

$final = array();
foreach ($info as &$value) {
    if ($value["user_level"] == 5) {
        $final[$value["user"]] = $value;
    }
}

foreach ($final as &$value) {
    foreach ($value["children"] as &$value1) {
        if ($info[$value1])
            $value["dispenser"][] = $info[$value1];
    }
}

 
foreach ($final as &$value) {
    fputcsv($output, array("ASM"), ";");
    fputcsv($output, array(
        $value['user'],
        $value['total_consulta'],
        $value['total_consulta_nova'],
        $value['total_consulta_remarcada'],
        $value['total_consulta_aberta'],
        round($value['total_consulta_aberta'] / $value['total_consulta'], 2),
        $value['total_consulta_fechada'],
        round($value['total_consulta_fechada'] / $value['total_consulta'], 2)), ";");

    $total["total_consulta"] += (int) $value["total_consulta"];
    $total["total_consulta_aberta"] += (int) $value["total_consulta_aberta"];
    $total["total_consulta_nova"] += (int) $value["total_consulta_nova"];
    $total["total_consulta_remarcada"] += (int) $value["total_consulta_remarcada"];
    $total["total_consulta_fechada"] += (int) $value["total_consulta_fechada"];


    foreach ($value["dispenser"] as $value1) {
  

        $total["total_consulta"] += (int) $value1["total_consulta"];
        $total["total_consulta_aberta"] += (int) $value1["total_consulta_aberta"];
        $total["total_consulta_nova"] += (int) $value1["total_consulta_nova"];
        $total["total_consulta_remarcada"] += (int) $value1["total_consulta_remarcada"];
        $total["total_consulta_fechada"] += (int) $value1["total_consulta_fechada"];
        fputcsv($output, array(
            $value1['user'],
            $value1['total_consulta'],
            $value1['total_consulta_nova'],
            $value1['total_consulta_remarcada'],
            $value1['total_consulta_aberta'],
            round($value1['total_consulta_aberta'] / $value1['total_consulta'], 2),
            $value1['total_consulta_fechada'],
            round($value1['total_consulta_fechada'] / $value1['total_consulta'], 2)), ";");
    }
}

//TOTAL
fputcsv($output, array(
    "Total",
    $total['total_consulta'],
    $total['total_consulta_nova'],
    $total['total_consulta_remarcada'],
    $total['total_consulta_aberta'],
    round($total['total_consulta_aberta'] / $total['total_consulta'], 2),
    $total['total_consulta_fechada'],
    round($total['total_consulta_fechada'] / $total['total_consulta'], 2)), ";");


fclose($output);
