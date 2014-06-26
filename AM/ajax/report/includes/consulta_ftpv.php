<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "consulta_ftpv_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

fputcsv($output, array(
    'Total de consultas Fechadas',
    'Consultas com Teste',
    '% Consultas com Teste',
    'Consultas com perda',
    '% Consultas com perda',
    'Consultas sem venda',
    '% Consultas sem venda',), ";");

$query_log = "SELECT a.consulta,a.exame,a.venda,a.closed,c.user,c.user_level,c.full_name,a.left_ear,a.right_ear,c.closer_campaigns"
        . " from spice_consulta a inner join sips_sd_reservations b on a.reserva_id=b.id_reservation inner join vicidial_users c on c.user=b.id_user where a.data between :data_inicial and :data_final ";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

$info = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($info[$row["user"]]) {
        (int) $row["consulta"] ? $info[$row["user"]]["consulta"] += 1 : $info[$row["user"]]["n_consulta"] += 1;
        (int) $row["exame"] ? $info[$row["user"]]["exame"] += 1 : $info[$row["user"]]["n_exame"] += 1;
        (int) $row["venda"] ? $info[$row["user"]]["venda"] += 1 : $info[$row["user"]]["n_venda"] += 1;
        (int) $row["closed"] ? $info[$row["user"]]["closed"] += 1 : $info[$row["user"]]["n_closed"] += 1;

        if ((int) $row["left_ear"] > 35 || (int) $row["right_ear"] > 35)
            $info[$row["user"]]["perda"] += 1;
    } else {

        $info[$row["user"]]["consulta"] = (int) $row["consulta"];
        $info[$row["user"]]["n_consulta"] = (int) $row["consulta"] ? 0 : 1;

        $info[$row["user"]]["exame"] = (int) $row["exame"];
        $info[$row["user"]]["n_exame"] = (int) $row["exame"] ? 0 : 1;


        $info[$row["user"]]["venda"] = (int) $row["venda"];
        $info[$row["user"]]["n_venda"] = (int) $row["venda"] ? 0 : 1;

        $info[$row["user"]]["closed"] = (int) $row["closed"];
        $info[$row["user"]]["n_closed"] = (int) $row["closed"]? 0 : 1;


        if ((int) $row["left_ear"] > 35 || (int) $row["right_ear"] > 35)
            $info[$row["user"]]["perda"] = 1;
        else
            $info[$row["user"]]["perda"] = 0;
        $info[$row["user"]]["user"] = $row["user"];
        $info[$row["user"]]["user_level"] = $row["user_level"];
        $info[$row["user"]]["full_name"] = $row["full_name"];
         $info[$row["user"]]["children"] = json_decode($row["closer_campaigns"]);
    };
}
var_dump($info);

foreach ($info as &$value) {
    fputcsv($output, array(
        $value['exame'] + $value['perda'] + $value['sem venda'],
        $value['consulta'],
        $value['consulta'],
        $value['consulta'],
        $value['consulta'],
        $value['consulta'],
        $value['consulta']), ";");
}


fclose($output);
