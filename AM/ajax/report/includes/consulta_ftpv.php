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
    'Sem consulta',
    '% Sem consulta',
    'Consultas sem Teste',
    '% Consultas sem Teste',
    'Consultas com Teste',
    '% Consultas com Teste',
    'Consultas sem perda',
    '% Consultas sem perda',
    'Consultas com perda',
    '% Consultas sem perda',
    'Consultas sem venda',
    '% Consultas sem venda',
    'Consultas com venda',
    '% Consultas com venda',
    'Terceira pessoa',
    '% Terceira pessoa',), ";");

$query_log = "SELECT a.consulta, a.exame, a.venda, a.closed, a.terceira_pessoa, a.user, a.left_ear, a.right_ear
                FROM spice_consulta a
                WHERE a.data BETWEEN :data_inicial AND :data_final;";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

$info = array();
$default = array(
    "closed" => 0,
    "n_consulta" => 0,
    "n_exame" => 0,
    "exame" => 0,
    "n_perda" => 0,
    "perda" => 0,
    "n_venda" => 0,
    "venda" => 0,
    "terceira_pessoa" => 0
);

$total = $default;

while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
    $row->terceira_pessoa = json_decode($row->terceira_pessoa);

    if (!$info[$row->user]) {
        $info[$row->user] = $default;
    }

    if ((int)$row->consulta) {
        $info[$row->user]["consulta"]++;
    } else {
        $info[$row->user]["n_consulta"]++;
    }

    if ((int)$row->exame) {
        $info[$row->user]["exame"]++;
    } else {
        $info[$row->user]["n_exame"]++;
    }

    if ((int)$row->venda) {
        $info[$row->user]["venda"]++;
    } else {
        $info[$row->user]["n_venda"]++;
    }

    if ((int)$row->closed) {
        $info[$row->user]["closed"]++;
    } else {
        $info[$row->user]["n_closed"]++;
    }

    if ((int)$row->left_ear > 35 || (int)$row->right_ear > 35) {
        $info[$row->user]["perda"]++;
    } else {
        $info[$row->user]["n_perda"]++;
    }

    if (count($row->terceira_pessoa)) {
        $info[$row->user]["terceira_pessoa"]++;
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

foreach ($final as $admName => &$dadData) {
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
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        ""), ";");


    $total["closed"] += $dadData["closed"];
    $total["n_consulta"] += (int)$dadData["n_consulta"];
    $total["n_exame"] += (int)$dadData["n_exame"];
    $total["exame"] += (int)$dadData["exame"];
    $total["n_perda"] += (int)$dadData["n_perda"];
    $total["perda"] += (int)$dadData["perda"];
    $total["n_venda"] += (int)$dadData["n_venda"];
    $total["terceira_pessoa"] += (int)$dadData["terceira_pessoa"];

    fputcsv($output, array(
        $admName,
        $dadData["closed"],
        $dadData['n_consulta'],
        ($dadData['n_consulta'] != 0 AND $dadData["closed"] != 0) ? round($dadData['n_consulta'] / $dadData["closed"], 2) * 100 : 0,
        $dadData['n_exame'],
        ($dadData['n_exame'] != 0 AND $dadData["closed"] != 0) ? round($dadData['n_exame'] / $dadData["closed"], 2) * 100 : 0,
        $dadData['exame'],
        ($dadData['exame'] != 0 AND $dadData["closed"] != 0) ? round($dadData['exame'] / $dadData["closed"], 2) * 100 : 0,
        $dadData['n_perda'],
        ($dadData['n_perda'] != 0 AND $dadData["closed"] != 0) ? round($dadData['n_perda'] / $dadData["closed"], 2) * 100 : 0,
        $dadData['perda'],
        ($dadData['perda'] != 0 AND $dadData["closed"] != 0) ? round($dadData['perda'] / $dadData["closed"], 2) * 100 : 0,
        $dadData['n_venda'],
        ($dadData['n_venda'] != 0 AND $dadData["closed"] != 0) ? round($dadData['n_venda'] / $dadData["closed"], 2) * 100 : 0,
        $dadData['venda'],
        ($dadData['venda'] != 0 AND $dadData["closed"] != 0) ? round($dadData['venda'] / $dadData["closed"], 2) * 100 : 0,
        $dadData['terceira_pessoa'],
        ($dadData['terceira_pessoa'] != 0 AND $dadData["closed"] != 0) ? round($dadData['terceira_pessoa'] / $dadData["closed"], 2) * 100 : 0), ";");

    foreach ($dadData["dispenser"] as $username => $userData) {

        $total["closed"] += $userData["closed"];
        $total["n_consulta"] += (int)$userData["n_consulta"];
        $total["n_exame"] += (int)$userData["n_exame"];
        $total["exame"] += (int)$userData["exame"];
        $total["n_perda"] += (int)$userData["n_perda"];
        $total["perda"] += (int)$userData["perda"];
        $total["n_venda"] += (int)$userData["n_venda"];
        $total["terceira_pessoa"] += (int)$userData["terceira_pessoa"];
        fputcsv($output, array(
            $username,
            $userData["closed"],
            $userData['n_consulta'],
            ($userData['n_consulta'] != 0 AND $userData["closed"] != 0) ? round($userData['n_consulta'] / $userData["closed"], 2) * 100 : 0,
            $userData['n_exame'],
            ($userData['n_exame'] != 0 AND $userData["closed"] != 0) ? round($userData['n_exame'] / $userData["closed"], 2) * 100 : 0,
            $userData['exame'],
            ($userData['exame'] != 0 AND $userData["closed"] != 0) ? round($userData['exame'] / $userData["closed"], 2) * 100 : 0,
            $userData['n_perda'],
            ($userData['n_perda'] != 0 AND $userData["closed"] != 0) ? round($userData['n_perda'] / $userData["closed"], 2) * 100 : 0,
            $userData['perda'],
            ($userData['perda'] != 0 AND $userData["closed"] != 0) ? round($userData['perda'] / $userData["closed"], 2) * 100 : 0,
            $userData['n_venda'],
            ($userData['n_venda'] != 0 AND $userData["closed"] != 0) ? round($userData['n_venda'] / $userData["closed"], 2) * 100 : 0,
            $userData['terceira_pessoa'],
            ($userData['terceira_pessoa'] != 0 AND $userData["closed"] != 0) ? round($userData['terceira_pessoa'] / $userData["closed"], 2) * 100 : 0), ";");
    }
}

//TOTAL
fputcsv($output, array(
    "Total",
    $total["closed"],
    $total['n_consulta'],
    ($total['n_consulta'] != 0 AND $total["closed"] != 0) ? round($total['n_consulta'] / $total["closed"], 2) * 100 : 0,
    $total['n_exame'],
    ($total['n_exame'] != 0 AND $total["closed"] != 0) ? round($total['n_exame'] / $total["closed"], 2) * 100 : 0,
    $total['exame'],
    ($total['exame'] != 0 AND $total["closed"] != 0) ? round($total['exame'] / $total["closed"], 2) * 100 : 0,
    $total['n_perda'],
    ($total['n_perda'] != 0 AND $total["closed"] != 0) ? round($total['n_perda'] / $total["closed"], 2) * 100 : 0,
    $total['perda'],
    ($total['perda'] != 0 AND $total["closed"] != 0) ? round($total['perda'] / $total["closed"], 2) * 100 : 0,
    $total['n_venda'],
    ($total['n_venda'] != 0 AND $total["closed"] != 0) ? round($total['n_venda'] / $total["closed"], 2) * 100 : 0,
    $total['terceira_pessoa'],
    ($total['terceira_pessoa'] != 0 AND $total["closed"] != 0) ? round($total['terceira_pessoa'] / $total["closed"], 2) * 100 : 0), ";");


fclose($output);
