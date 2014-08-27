<?php

require "$root/AM/lib_php/calendar.php";
$calendar = new Calendars($db);
$users = new UserControler($db, $u);

$curTime = date("Y-m-d_H:i:s");
$filename = "consulta_result_fecho_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

//tipos de exame
$rs = $calendar->getResTypeRaw();
$rs = implode(",", $rs);

//users obj
$oUsersTMP = $users->getAll(UserControler::ASM);
$oUsers = Array();
foreach ($oUsersTMP as &$user) {
    $siblings = json_decode($user->siblings);
    $user->siblings = (is_array($siblings)) ? $siblings : Array();
    $oUsers[$user->user] = $user;
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

$query_log = "SELECT b.user, b.consulta, b.exame, b.venda, b.closed, b.terceira_pessoa, b.left_ear, b.right_ear, a.id_resource, a.changed "
        . "FROM sips_sd_reservations a "
        . "INNER JOIN spice_consulta b ON a.id_reservation=b.reserva_id "
        . "WHERE a.id_reservation_type IN ($rs) AND a.start_date BETWEEN :data_inicial AND :data_final ";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

$info = array();
$default = array(
    "total" => 0,
    "abertas" => 0,
    "novas" => 0,
    "remarcadas" => 0,
    "fechadas" => 0,
);
$total = $default;

while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
    if (!$info[$row->user]) {
        $info[$row->user] = $default;
    }
    $info[$row->user]["total"] += 1;
    $info[$row->user]["novas"] += (int) $row->changed == 0 ? 1 : 0;
    $info[$row->user]["remarcadas"] += (int) ($row->changed) ? 1 : 0;
    $info[$row->user]["abertas"] +=(int) $row->closed == 0 ? 1 : 0;
    $info[$row->user]["fechadas"] += (int) $row->closed;
}

$final = array();
foreach ($oUsers as $user) {
    if ($user->user_level == UserControler::ASM) {
        $final[$user->user] = ($info[$user->user]) ? $info[$user->user] : $default;
    }
}
#var_dump($info);exit;
foreach ($final as $username => &$dadData) {
    $dadData["dispenser"] = Array();
    foreach ($oUsers[$username]->siblings as $sibling) {
        $dadData["dispenser"][$sibling] = ($info[$sibling]) ? $info[$sibling] : $default;
    }
}

foreach ($final as $admName => &$dadData) {
    fputcsv($output, array("ASM"), ";");
    fputcsv($output, array(
        $admName,
        $dadData['total'],
        $dadData['novas'],
        $dadData['remarcadas'],
        $dadData['abertas'],
        ($dadData['abertas'] != 0 AND $dadData['total'] != 0) ? round($dadData['abertas'] / $dadData['total'], 2) * 100 : 0,
        $dadData['fechadas'],
        ($dadData['fechadas'] != 0 AND $dadData['total'] != 0) ? round($dadData['fechadas'] / $dadData['total'], 2) * 100 : 0), ";");

    $total["total"] += (int) $dadData["total"];
    $total["abertas"] += (int) $dadData["abertas"];
    $total["novas"] += (int) $dadData["novas"];
    $total["remarcadas"] += (int) $dadData["remarcadas"];
    $total["fechadas"] += (int) $dadData["fechadas"];

    foreach ($dadData["dispenser"] as $username => $userData) {

        $total["total"] += (int) $userData["total"];
        $total["abertas"] += (int) $userData["abertas"];
        $total["novas"] += (int) $userData["novas"];
        $total["remarcadas"] += (int) $userData["remarcadas"];
        $total["fechadas"] += (int) $userData["fechadas"];
        fputcsv($output, array(
            $username,
            $userData['total'],
            $userData['novas'],
            $userData['remarcadas'],
            $userData['abertas'],
            ($userData['abertas'] != 0 AND $userData['total'] != 0) ? round($userData['abertas'] / $userData['total'], 2) * 100 : 0,
            $userData['fechadas'],
            ($userData['fechadas'] != 0 AND $userData['total'] != 0) ? round($userData['fechadas'] / $userData['total'], 2) * 100 : 0), ";");
    }
}

//TOTAL
fputcsv($output, array(
    "Total",
    $total['total'],
    $total['novas'],
    $total['remarcadas'],
    $total['abertas'],
    ($total['abertas'] != 0 AND $total['total'] != 0) ? round($total['abertas'] / $total['total'], 2) * 100 : 0,
    $total['fechadas'],
    ($total['fechadas'] != 0 AND $total['total'] != 0) ? round($total['fechadas'] / $total['total'], 2) * 100 : 0), ";");

fclose($output);
