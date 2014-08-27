<?php

$users = new UserControler($db, $u);

$curTime = date("Y-m-d_H:i:s");
$filename = "consulta_kpi_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

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
    'Total de consultas Fechadas',
    'Consultas com Teste',
    '% Consultas com Teste',
    'Consultas com perda',
    '% Consultas com perda',
    'Consultas sem venda',
    '% Consultas sem venda',
    'Terceira pessoa',
    '% Terceira pessoa',), ";");

$query_log = "SELECT a.consulta, a.exame, a.venda, a.closed, a.terceira_pessoa, a.user, a.left_ear, a.right_ear "
        . "FROM spice_consulta a "
        . "WHERE a.data BETWEEN :data_inicial AND :data_final;";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

$info = array();
$default = array(
    "consulta" => 0,
    "n_consulta" => 0,
    "exame" => 0,
    "n_exame" => 0,
    "venda" => 0,
    "n_venda" => 0,
    "closed" => 0,
    "n_closed" => 0,
    "perda" => 0,
    "consulta" => 0,
);
$total = array(
    "consulta_fechada" => 0,
    "consulta" => 0,
    "perda" => 0,
    "n_venda" => 0,
    "terceira_pessoa" => 0);

while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
    $row->terceira_pessoa = json_decode($row->terceira_pessoa);

    if (!$info[$row->user]) {
        $info[$row->user] = $default;
    }
    (int) $row->consulta ? $info[$row->user]["consulta"] ++ : $info[$row->user]["n_consulta"] ++;
    (int) $row->exame ? $info[$row->user]["exame"] ++ : $info[$row->user]["n_exame"] ++;
    (int) $row->venda ? $info[$row->user]["venda"] ++ : $info[$row->user]["n_venda"] ++;
    (int) $row->closed ? $info[$row->user]["closed"] ++ : $info[$row->user]["n_closed"] ++;

    if ((int) $row->left_ear > 35 || (int) $row->right_ear > 35) {
        $info[$row->user]["perda"] ++;
    }

    if (count($row->terceira_pessoa)) {
        $info[$row->user]["terceira_pessoa"] ++;
    }
}
$final = array();
foreach ($oUsers as $user) {
    if ($user->user_level == UserControler::ASM) {
        $final[$user->user] = ($info[$user->user]) ? $info[$user->user] : $default;
    }
}

foreach ($final as $username => &$dadData) {
    $dadData["dispenser"] = Array();
    foreach ($oUsers[$username]->siblings as $sibling) {
        $dadData["dispenser"][$sibling] = ($info[$sibling]) ? $info[$sibling] : $default;
    }
}


foreach ($final as &$dadData) {
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
    $cons_fechadas = $dadData['consulta'] + $dadData['perda'] + $dadData['n_venda'];
    $total["consulta_fechada"] +=$cons_fechadas;
    $total["consulta"] += (int) $dadData["consulta"];
    $total["perda"] +=(int) $info[$dadData["user"]]["perda"];
    $total["n_venda"] += (int) $dadData["n_venda"];
    $total["terceira_pessoa"] += (int) $dadData["terceira_pessoa"];

    fputcsv($output, array(
        $dadData['user'],
        $cons_fechadas,
        $dadData['consulta'],
        ($dadData['consulta'] != 0 AND $cons_fechadas != 0) ? round($dadData['consulta'] / $cons_fechadas, 2) * 100 : 0,
        $dadData['perda'],
        ($dadData['perda'] != 0 AND $cons_fechadas != 0) ? round($dadData['perda'] / $cons_fechadas, 2) * 100 : 0,
        $dadData['n_venda'],
        ($dadData['n_venda'] != 0 AND $cons_fechadas != 0) ? round($dadData['n_venda'] / $cons_fechadas, 2) * 100 : 0,
        $dadData['terceira_pessoa'],
        ($dadData['terceira_pessoa'] != 0 AND $cons_fechadas != 0) ? round($dadData['terceira_pessoa'] / $cons_fechadas, 2) * 100 : 0), ";");

    foreach ($dadData["dispenser"] as $userData) {
        $cons_fechadas = $userData['consulta'] + $userData['perda'] + $userData['n_venda'];
        $total["consulta_fechada"] +=$cons_fechadas;
        $total["consulta"] += (int) $userData["consulta"];
        $total["perda"] +=(int) $info[$userData["user"]]["perda"];
        $total["n_venda"] += (int) $userData["n_venda"];
        $total["terceira_pessoa"] += (int) $userData["terceira_pessoa"];
        fputcsv($output, array(
            $userData['user'],
            $cons_fechadas,
            $userData['consulta'],
            ($userData['consulta'] != 0 AND $cons_fechadas != 0) ? round($userData['consulta'] / $cons_fechadas, 2) * 100 : 0,
            $userData['perda'],
            ($userData['perda'] != 0 AND $cons_fechadas != 0) ? round($userData['perda'] / $cons_fechadas, 2) * 100 : 0,
            $userData['n_venda'],
            ($userData['n_venda'] != 0 AND $cons_fechadas != 0) ? round($userData['n_venda'] / $cons_fechadas, 2) * 100 : 0,
            $userData['terceira_pessoa'],
            ($userData['terceira_pessoa'] != 0 AND $cons_fechadas != 0) ? round($userData['terceira_pessoa'] / $cons_fechadas, 2) * 100 : 0), ";");
    }
}

//TOTAL
fputcsv($output, array(
    "Total",
    $total["consulta_fechada"],
    $total['consulta'],
    ($total['terceira_pessoa'] != 0 AND $total["consulta_fechada"] != 0) ? round($total['consulta'] / $total["consulta_fechada"], 2) * 100 : 0,
    $total['perda'],
    ($total['perda'] != 0 AND $total["consulta_fechada"] != 0) ? round($total['perda'] / $total["consulta_fechada"], 2) * 100 : 0,
    $total['n_venda'],
    ($total['n_venda'] != 0 AND $total["consulta_fechada"] != 0) ? round($total['n_venda'] / $total["consulta_fechada"], 2) * 100 : 0,
    $total['terceira_pessoa'],
    ($total['terceira_pessoa'] != 0 AND $total["consulta_fechada"] != 0) ? round($total['terceira_pessoa'] / $total["consulta_fechada"], 2) * 100 : 0), ";");


fclose($output);
