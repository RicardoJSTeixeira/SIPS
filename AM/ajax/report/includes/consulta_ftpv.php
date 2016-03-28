<?php

/** @var PDO $db */
$users = new UserControler($db, $u);

$curTime = date("Y-m-d_H:i:s");
$filename = "consulta_kpi_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

//users obj
$oASMTMP = $users->getAll(UserControler::ASM);
$oASM = Array();
foreach ($oASMTMP as &$user) {
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
    'Consultas fechadas',
    'No Show',
    '% No Show',
    'Show',
    '% Show',
    'Sem teste',
    '% Sem teste',
    'Com teste',
    '% Com teste',
    'Sem perda',
    '% Sem perda',
    'Com perda',
    '% Com perda',
    'Sem venda',
    '% Sem venda',
    'Com venda',
    '% Com venda',
    'Terceira pessoa',
    '% Terceira pessoa'), ";");

$query_log = "SELECT a.consulta, a.exame, a.venda, a.closed, a.terceira_pessoa, a.user, a.left_ear, a.right_ear, a.feedback
                FROM spice_consulta a
                INNER JOIN sips_sd_reservations b ON a.reserva_id=b.id_reservation
                WHERE a.data BETWEEN :data_inicial AND :data_final AND closed $where;";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

$info = array();
$default = array(
    "closed" => 0,
    "n_consulta" => 0,
    "consulta" => 0,
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

    //verifica se o alias já existe no obj, se não cria com o default
    if (!$info[$oUsers[$row->user]->alias]) {
        $info[$oUsers[$row->user]->alias] = $default;
    }

    //logica de contabilização dos dados
    if ((int)$row->closed) {
        $info[$oUsers[$row->user]->alias]["closed"]++;

        if ((int)$row->consulta) {
            $info[$oUsers[$row->user]->alias]["consulta"]++;

            if (count($row->terceira_pessoa)) {
                $info[$oUsers[$row->user]->alias]["terceira_pessoa"]++;
            }

            if ((int)$row->exame) {
                $info[$oUsers[$row->user]->alias]["exame"]++;

                if ($row->feedback !== "SPERD") {
                    $info[$oUsers[$row->user]->alias]["perda"]++;

                    if ((int)$row->venda) {
                        $info[$oUsers[$row->user]->alias]["venda"]++;
                    } else {
                        $info[$oUsers[$row->user]->alias]["n_venda"]++;
                    }

                } else {
                    $info[$oUsers[$row->user]->alias]["n_perda"]++;
                }

            } else {
                $info[$oUsers[$row->user]->alias]["n_exame"]++;
            }

        } else {
            $info[$oUsers[$row->user]->alias]["n_consulta"]++;
        }

    } else {
        $info[$oUsers[$row->user]->alias]["n_closed"]++;
    }

}
$final = array();
foreach ($oASM as $user) {
    $final[$user->user] = ($info[$user->alias]) ? $info[$user->alias] : $default;
}

foreach ($final as $username => &$dadData) {
    $dadData["dispenser"] = Array();
    foreach ($oASM[$username]->siblings as $sibling) {
        $dadData["dispenser"][$oUsers[$sibling]->alias] = ($info[$oUsers[$sibling]->alias]) ? $info[$oUsers[$sibling]->alias] : $default;
    }
}

$to_check = $u->user_level < 7;
foreach ($final as $admName => &$dadData) {
    if ($to_check) {
        if ($u->username != $admName)
            continue;
    }
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

    $total["closed"] += (int)$dadData["closed"];
    $total["n_consulta"] += (int)$dadData["n_consulta"];
    $total["consulta"] += (int)$dadData["consulta"];
    $total["n_exame"] += (int)$dadData["n_exame"];
    $total["exame"] += (int)$dadData["exame"];
    $total["n_perda"] += (int)$dadData["n_perda"];
    $total["perda"] += (int)$dadData["perda"];
    $total["n_venda"] += (int)$dadData["n_venda"];
    $total["venda"] += (int)$dadData["venda"];
    $total["terceira_pessoa"] += (int)$dadData["terceira_pessoa"];

    fputcsv($output, array(
        $admName,
        $dadData["closed"],
        $dadData['n_consulta'],
        divide($dadData['n_consulta'], $dadData["closed"]),
        $dadData['consulta'],
        divide($dadData['consulta'], $dadData["closed"]),
        $dadData['n_exame'],
        divide($dadData['n_exame'], $dadData["consulta"]),
        $dadData['exame'],
        divide($dadData['exame'], $dadData["consulta"]),
        $dadData['n_perda'],
        divide($dadData['n_perda'], $dadData["exame"]),
        $dadData['perda'],
        divide($dadData['perda'], $dadData["exame"]),
        $dadData['n_venda'],
        divide($dadData['n_venda'], $dadData["perda"]),
        $dadData['venda'],
        divide($dadData['venda'], $dadData["perda"]),
        $dadData['terceira_pessoa'],
        divide($dadData['terceira_pessoa'], $dadData["consulta"])), ";");

    foreach ($dadData["dispenser"] as $username => $userData) {

        $total["closed"] += (int)$userData["closed"];
        $total["n_consulta"] += (int)$userData["n_consulta"];
        $total["consulta"] += (int)$userData["consulta"];
        $total["n_exame"] += (int)$userData["n_exame"];
        $total["exame"] += (int)$userData["exame"];
        $total["n_perda"] += (int)$userData["n_perda"];
        $total["perda"] += (int)$userData["perda"];
        $total["n_venda"] += (int)$userData["n_venda"];
        $total["venda"] += (int)$userData["venda"];
        $total["terceira_pessoa"] += (int)$userData["terceira_pessoa"];
        fputcsv($output, array(
            $username,
            $userData["closed"],
            $userData['n_consulta'],
            divide($userData['n_consulta'], $userData["closed"]),
            $userData['consulta'],
            divide($userData['consulta'], $userData["closed"]),
            $userData['n_exame'],
            divide($userData['n_exame'], $userData["consulta"]),
            $userData['exame'],
            divide($userData['exame'], $userData["consulta"]),
            $userData['n_perda'],
            divide($userData['n_perda'], $userData["exame"]),
            $userData['perda'],
            divide($userData['perda'], $userData["exame"]),
            $userData['n_venda'],
            divide($userData['n_venda'], $userData["perda"]),
            $userData['venda'],
            divide($userData['venda'], $userData["perda"]),
            $userData['terceira_pessoa'],
            divide($userData['terceira_pessoa'], $userData["consulta"])), ";");
    }
}

//TOTAL
fputcsv($output, array(
    "Total",
    $total["closed"],
    $total['n_consulta'],
    divide($total['n_consulta'], $total["closed"]),
    $total['consulta'],
    divide($total['consulta'], $total["closed"]),
    $total['n_exame'],
    divide($total['n_exame'], $total["consulta"]),
    $total['exame'],
    divide($total['exame'], $total["consulta"]),
    $total['n_perda'],
    divide($total['n_perda'], $total["exame"]),
    $total['perda'],
    divide($total['perda'], $total["exame"]),
    $total['n_venda'],
    divide($total['n_venda'], $total["perda"]),
    $total['venda'],
    divide($total['venda'], $total["perda"]),
    $total['terceira_pessoa'],
    divide($total['terceira_pessoa'], $total["consulta"])), ";");

fclose($output);