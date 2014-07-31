<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "consulta_ftpv_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

fputcsv($output, array(
    'User',
    'Total de consultas Fechadas',
    'Consultas com Teste',
    '% Consultas com Teste',
    'Consultas com perda',
    '% Consultas com perda',
    'Consultas sem venda',
    '% Consultas sem venda',
    'Terceira pessoa',
    '% Terceira pessoa',), ";");

$query_log = "SELECT a.consulta,a.exame,a.venda,a.closed,a.terceira_pessoa,c.user,c.user_level,c.full_name,a.left_ear,a.right_ear,c.closer_campaigns from spice_consulta a  inner join  vicidial_users c on c.user=a.user where c.user_group='SPICE' and a.data between :data_inicial and :data_final ";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

$info = array();
$total = array();
$total["consulta_fechada"] = 0;
$total["consulta"] = 0;
$total["perda"] = 0;
$total["n_venda"] = 0;
$total["terceira_pessoa"] = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row["terceira_pessoa"] = json_decode($row["terceira_pessoa"]);

    if ($info[$row["user"]]) {
        (int) $row["consulta"] ? $info[$row["user"]]["consulta"] ++ : $info[$row["user"]]["n_consulta"] ++;
        (int) $row["exame"] ? $info[$row["user"]]["exame"] ++ : $info[$row["user"]]["n_exame"] ++;
        (int) $row["venda"] ? $info[$row["user"]]["venda"] ++ : $info[$row["user"]]["n_venda"] ++;
        (int) $row["closed"] ? $info[$row["user"]]["closed"] ++ : $info[$row["user"]]["n_closed"] ++;

        if ((int) $row["left_ear"] > 35 || (int) $row["right_ear"] > 35)
            $info[$row["user"]]["perda"] += 1;


        if (count($row["terceira_pessoa"])) {
            $info[$row["user"]]["terceira_pessoa"] ++;
        }
    } else {
        $info[$row["user"]]["consulta"] = (int) $row["consulta"];
        $info[$row["user"]]["n_consulta"] = (int) $row["consulta"] ? 0 : 1;

        $info[$row["user"]]["exame"] = (int) $row["exame"];
        $info[$row["user"]]["n_exame"] = (int) $row["exame"] ? 0 : 1;

        $info[$row["user"]]["venda"] = (int) $row["venda"];
        $info[$row["user"]]["n_venda"] = (int) $row["venda"] ? 0 : 1;

        $info[$row["user"]]["closed"] = (int) $row["closed"];
        $info[$row["user"]]["n_closed"] = (int) $row["closed"] ? 0 : 1;


        $info[$row["user"]]["perda"] = ((int) $row["left_ear"] > 25 || (int) $row["right_ear"] > 25) ? 1 : 0;

        $info[$row["user"]]["user"] = $row["user"];
        $info[$row["user"]]["user_level"] = $row["user_level"];
        $info[$row["user"]]["full_name"] = $row["full_name"];

        if ($row["user_level"] == 5) {
            $info[$row["user"]]["children"] = [];
            $temp = json_decode($row["siblings"]);
            if ($temp)
                $info[$row["user"]]["children"] = $temp;
        }
        $info[$row["user"]]["terceira_pessoa"] = (count($row["terceira_pessoa"])) ? 1 : 0;
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
    fputcsv($output, array(
        "ASM",
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        ""), ";");
    $cons_fechadas = $value['consulta'] + $value['perda'] + $value['n_venda'];
    $total["consulta_fechada"] +=$cons_fechadas;
    $total["consulta"] += (int) $value["consulta"];
    $total["perda"] +=(int) $info[$value["user"]]["perda"];
    $total["n_venda"] += (int) $value["n_venda"];
    $total["terceira_pessoa"] += (int) $value["terceira_pessoa"];
    
    fputcsv($output, array(
        $value['user'],
        $cons_fechadas,
        $value['consulta'],
        round($value['consulta'] / $cons_fechadas, 2),
        $value['perda'],
        round($value['perda'] / $cons_fechadas, 2),
        $value['n_venda'],
        round($value['n_venda'] / $cons_fechadas, 2),
        $value['terceira_pessoa'],
        round($value['terceira_pessoa'] / $cons_fechadas, 2)), ";");
    
    foreach ($value["dispenser"] as $value1) {
        $cons_fechadas = $value1['consulta'] + $value1['perda'] + $value1['n_venda'];
        $total["consulta_fechada"] +=$cons_fechadas;
        $total["consulta"] += (int) $value1["consulta"];
        $total["perda"] +=(int) $info[$value1["user"]]["perda"];
        $total["n_venda"] += (int) $value1["n_venda"];
        $total["terceira_pessoa"] += (int) $value1["terceira_pessoa"];
        fputcsv($output, array(
            $value1['user'],
            $cons_fechadas,
            $value1['consulta'],
            round($value1['consulta'] / $cons_fechadas, 2),
            $value1['perda'],
            round($value1['perda'] / $cons_fechadas, 2),
            $value1['n_venda'],
            round($value1['n_venda'] / $cons_fechadas, 2),
            $value1['terceira_pessoa'],
            round($value1['terceira_pessoa'] / $cons_fechadas, 2)), ";");
    }
}

//TOTAL
fputcsv($output, array(
    "Total",
    $total["consulta_fechada"],
    $total['consulta'],
    round($total['consulta'] / $total["consulta_fechada"], 2),
    $total['perda'],
    round($total['perda'] / $total["consulta_fechada"], 2),
    $total['n_venda'],
    round($total['n_venda'] / $total["consulta_fechada"], 2),
    $total['terceira_pessoa'],
    round($total['terceira_pessoa'] / $total["consulta_fechada"], 2)), ";");


fclose($output);
