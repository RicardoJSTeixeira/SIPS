<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "audiograma_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');
$data=array();
    $u = $user->getUser();
        $output['aaData'] = array();
        $query = "SELECT extra2 'codCliente', '' as 'itLogID', a.lead_id , id_reservation , a.entry_date, f.user, consulta_razao, '' as 'salespersonCode' , MAX(IF(g.name='AR',g.value,''))'AR',MAX(IF(g.name='AL',g.value,'')) 'AL',MAX(IF(g.name='BCR',g.value,'')) 'BCR',MAX(IF(g.name='BCL',g.value,'')) 'BCL',MAX(IF(g.name='ULLR',g.value,'')) 'ULLR',MAX(IF(g.name='ULLL',g.value,'')) 'ULLL' "
                . "FROM sips_sd_reservations a "
                . "INNER JOIN vicidial_list d ON a.lead_id = d.lead_id "
                . "INNER JOIN spice_consulta f ON a.id_reservation=f.reserva_id "
                . "INNER JOIN spice_audiograma g ON a.id_reservation=g.uniqueid "
                . "WHERE f.closed=1 group by g.uniqueid limit 20000";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $extractor = function($a) {
            return $a->value;
        };

        function audioCalc($ar500, $al500, $ar1000, $al1000, $ar2000, $al2000, $ar4000, $al4000) {
            $right_ear = (object) array("value" => 0, "text" => "");
            $left_ear = (object) array("value" => 0, "text" => "");

            $right_ear->value = (($ar500 * 4) + ($ar1000 * 3) + ($ar2000 * 2) + ($ar4000 * 1)) / 10;
            $left_ear->value = (($al500 * 4) + ($al1000 * 3) + ($al2000 * 2) + ($al4000 * 1)) / 10;

            if ($right_ear->value < 35 && $left_ear->value < 35) {
                $result = "Sem Perda";
            } else {
                $result = "Perda";
                if ($right_ear->value >= 35 && $right_ear->value < 65) {
                    $right_ear->text = "Perda";
                } else if ($right_ear->value >= 65) {
                    $right_ear->text = "Perda Power";
                }
                if ($left_ear->value >= 35 && $left_ear->value < 65) {
                    $left_ear->text = "Perda";
                } else if ($left_ear->value >= 65) {
                    $left_ear->text = "Perda Power";
                }
            }
            return (object) array("right" => $right_ear, "left" => $left_ear, "result" => $result);
        }

        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $al = array_map($extractor, json_decode($row->AL));
            $ar = array_map($extractor, json_decode($row->AR));
            $bcl = array_map($extractor, json_decode($row->BCL));
            $bcr = array_map($extractor, json_decode($row->BCR));
            $ulll = array_map($extractor, json_decode($row->ULLL));
            $ullr = array_map($extractor, json_decode($row->ULLR));

            $audioResult = audioCalc($ar[1], $al[1], $ar[2], $al[2], $ar[3], $al[3], $ar[5], $al[5]);
            $data[] = array_merge(array($row->codCliente, $row->itLogID, $row->id_reservation, $row->lead_id, $row->entry_date, $row->user), $al, $ar, $bcl, $bcr, $ulll, $ullr, array($audioResult->right->text, $audioResult->left->text, $audioResult->right->value, $audioResult->left->value, 0, 0, 0, 0, 0, 0, $audioResult->result, 0));
        }












fputcsv($output, array(
    'Contact no.',
    'Iteraction Log Entry No.',
    'Spice contact No.',
    'Id Consulta Spice',
    'Date Entered',
    'Assigned UserId',
    'Air Right 250',
    'Air Right 500',
    'Air Right 1000',
    'Air Right 1500',
    'Air Right 2000',
    'Air Right 3000',
    'Air Right 4000',
    'Air Right 6000',
    'Air Right 8000',
    'Air Right Mask',
    'Air Right 250',
    'Air Right 500',
    'Air Right 1000',
    'Air Right 1500',
    'Air Right 2000',
    'Air Right 3000',
    'Air Right 4000',
    'Air Right 6000',
    'Air Right 8000',
    'Air Right Mask',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250',
    'Air Right 250'), ";");

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
        (int) $row["consulta"] ? $info[$row["user"]]["consulta"] += 1 : $info[$row["user"]]["n_consulta"] += 1;
        (int) $row["exame"] ? $info[$row["user"]]["exame"] += 1 : $info[$row["user"]]["n_exame"] += 1;
        (int) $row["venda"] ? $info[$row["user"]]["venda"] += 1 : $info[$row["user"]]["n_venda"] += 1;
        (int) $row["closed"] ? $info[$row["user"]]["closed"] += 1 : $info[$row["user"]]["n_closed"] += 1;

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


        if ((int) $row["left_ear"] > 35 || (int) $row["right_ear"] > 35)
            $info[$row["user"]]["perda"] = 1;
        else
            $info[$row["user"]]["perda"] = 0;
        $info[$row["user"]]["user"] = $row["user"];
        $info[$row["user"]]["user_level"] = $row["user_level"];
        $info[$row["user"]]["full_name"] = $row["full_name"];

        if ($row["user_level"] == 5) {
            $info[$row["user"]]["children"] = [];
            $temp = explode('","', $row["closer_campaigns"]);
            if ($temp)
                $info[$row["user"]]["children"] = $temp;
        }
        if (count($row["terceira_pessoa"]))
            $info[$row["user"]]["terceira_pessoa"] = 1;
        else
            $info[$row["user"]]["terceira_pessoa"] = 0;
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
