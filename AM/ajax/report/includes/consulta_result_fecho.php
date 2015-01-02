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
$oASMTMP = $users->getAll(UserControler::ASM);
$oASM = Array();
foreach ($oASMTMP as &$user) {

    if ($u->user_level < UserControler::ADM AND $u->username !== $user->user)
        continue;

    $siblings = json_decode($user->siblings);
    $user->siblings = (is_array($siblings)) ? $siblings : Array();
    $oASM[$user->user] = $user;
}

$oUsersTMP = $users->getAll();
$oUsers = Array();
foreach ($oUsersTMP as &$user) {
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

$query_log = "SELECT b.user, b.consulta, b.exame, b.venda, b.closed, b.terceira_pessoa, b.left_ear, b.right_ear, a.id_resource, a.changed
                FROM sips_sd_reservations a
                LEFT JOIN spice_consulta b ON a.id_reservation=b.reserva_id
                WHERE a.id_reservation_type IN ($rs) AND b.data BETWEEN :data_inicial AND :data_final AND closed;";

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
    if (!$info[$oUsers[$row->user]->alias]) {
        $info[$oUsers[$row->user]->alias] = $default;
    }
    $info[$oUsers[$row->user]->alias]["total"] += 1;
    $info[$oUsers[$row->user]->alias]["novas"] += (int)$row->changed == 0 ? 1 : 0;
    $info[$oUsers[$row->user]->alias]["remarcadas"] += (int)($row->changed) ? 1 : 0;
    $info[$oUsers[$row->user]->alias]["abertas"] += (int)$row->closed == 0 ? 1 : 0;
    $info[$oUsers[$row->user]->alias]["fechadas"] += (int)$row->closed;
}

$final = array();
foreach ($oASM as $user) {
    $final[$user->user] = ($info[$oUsers[$user->user]->alias]) ? $info[$user->user] : $default;
}

foreach ($final as $username => &$dadData) {
    $dadData["dispenser"] = Array();
    foreach ($oASM[$username]->siblings as $sibling) {
        $dadData["dispenser"][$oUsers[$sibling]->alias] = ($info[$oUsers[$sibling]->alias]) ? $info[$oUsers[$sibling]->alias] : $default;
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
        divide($dadData['abertas'], $dadData['total']),
        $dadData['fechadas'],
        divide($dadData['fechadas'], $dadData['total'])), ";");

    $total["total"] += (int)$dadData["total"];
    $total["abertas"] += (int)$dadData["abertas"];
    $total["novas"] += (int)$dadData["novas"];
    $total["remarcadas"] += (int)$dadData["remarcadas"];
    $total["fechadas"] += (int)$dadData["fechadas"];

    foreach ($dadData["dispenser"] as $username => $userData) {

        $total["total"] += (int)$userData["total"];
        $total["abertas"] += (int)$userData["abertas"];
        $total["novas"] += (int)$userData["novas"];
        $total["remarcadas"] += (int)$userData["remarcadas"];
        $total["fechadas"] += (int)$userData["fechadas"];
        fputcsv($output, array(
            $username,
            $userData['total'],
            $userData['novas'],
            $userData['remarcadas'],
            $userData['abertas'],
            divide($userData['abertas'], $userData['total']),
            $userData['fechadas'],
            divide($userData['fechadas'], $userData['total'])), ";");
    }
}

//TOTAL
fputcsv($output, array(
    "Total",
    $total['total'],
    $total['novas'],
    $total['remarcadas'],
    $total['abertas'],
    divide($total['abertas'], $total['total']),
    $total['fechadas'],
    divide($total['fechadas'], $total['total'])), ";");

fclose($output);
