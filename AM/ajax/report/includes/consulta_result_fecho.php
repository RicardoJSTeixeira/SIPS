<?php

require "$root/AM/lib_php/calendar.php";
$calendar = new Calendars($db);
$users = new UserControler($db, $user);

$curTime = date("Y-m-d_H:i:s");
$filename = "consulta_result_fecho_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

//tipos de exame
$rs = $calendar->getResTypeRaw();
$rs = implode(",", $rs);

//users obj
$oUsersTMP = $users->getAll(5);
$oUsers = Array();
foreach ($oUsersTMP as &$value) {
    $value->siblings = json_decode($value->siblings);
    $oUsers[$value->user] = $value;
}

fputcsv($output, array(
    'User',
    'Total de consultas',
    'Novas',
    'Remarcadas',
    'Total Abertas',
    '% Abertas',
    'Fechadas',
    '% Fechadas'), ";");

$query_log = "SELECT b.consulta, b.exame, b.venda, b.closed, b.terceira_pessoa, b.left_ear, b.right_ear, a.id_resource "
        . "FROM sips_sd_reservations a "
        . "INNER JOIN spice_consulta b ON a.id_reservation=b.reserva_id "
        . "WHERE a.id_reservation_type IN ($rs) AND a.start_date BETWEEN :data_inicial AND :data_final ";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

$info = array();
$default = array(
    "total_consulta" => 0,
    "total_consulta_aberta" => 0,
    "total_consulta_nova" => 0,
    "total_consulta_remarcada" => 0,
    "total_consulta_fechada" => 0,
);
$total = $default;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!$info[$row["user"]]) {
        $info[$row["user"]] = $default;
    }
    $info[$row["user"]]["total_consulta"] += 1;
    $info[$row["user"]]["total_consulta_nova"] += (int) $row["changed"] == 0 ? 1 : 0;
    $info[$row["user"]]["total_consulta_remarcada"] += (int) $row["changed"];
    $info[$row["user"]]["total_consulta_aberta"] +=(int) $row["closed"] == 0 ? 1 : 0;
    $info[$row["user"]]["total_consulta_fechada"] += (int) $row["closed"];
}

$final = array();
foreach ($info as $username => $userData) {
    if ($oUsers[$username]->user_level == UserControler::ASM) {
        $final[$username] = $userData;
    }
}

foreach ($final as $username => &$dadData) {
    foreach ($oUsers[$username]->siblings as $sibling) {
        if ($info[$sibling]) {
            $dadData["dispenser"][] = $info[$sibling];
        }
    }
}

foreach ($final as &$dadData) {
    fputcsv($output, array("ASM"), ";");
    fputcsv($output, array(
        $dadData['user'],
        $dadData['total_consulta'],
        $dadData['total_consulta_nova'],
        $dadData['total_consulta_remarcada'],
        $dadData['total_consulta_aberta'],
        round($dadData['total_consulta_aberta'] / $dadData['total_consulta'], 2),
        $dadData['total_consulta_fechada'],
        round($dadData['total_consulta_fechada'] / $dadData['total_consulta'], 2)), ";");

    $total["total_consulta"] += (int) $dadData["total_consulta"];
    $total["total_consulta_aberta"] += (int) $dadData["total_consulta_aberta"];
    $total["total_consulta_nova"] += (int) $dadData["total_consulta_nova"];
    $total["total_consulta_remarcada"] += (int) $dadData["total_consulta_remarcada"];
    $total["total_consulta_fechada"] += (int) $dadData["total_consulta_fechada"];

    foreach ($dadData["dispenser"] as $userData) {

        $total["total_consulta"] += (int) $userData["total_consulta"];
        $total["total_consulta_aberta"] += (int) $userData["total_consulta_aberta"];
        $total["total_consulta_nova"] += (int) $userData["total_consulta_nova"];
        $total["total_consulta_remarcada"] += (int) $userData["total_consulta_remarcada"];
        $total["total_consulta_fechada"] += (int) $userData["total_consulta_fechada"];
        fputcsv($output, array(
            $userData['user'],
            $userData['total_consulta'],
            $userData['total_consulta_nova'],
            $userData['total_consulta_remarcada'],
            $userData['total_consulta_aberta'],
            round($userData['total_consulta_aberta'] / $userData['total_consulta'], 2),
            $userData['total_consulta_fechada'],
            round($userData['total_consulta_fechada'] / $userData['total_consulta'], 2)), ";");
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