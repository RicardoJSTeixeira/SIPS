<?php

$aType = explode("_", $action);
$type = array_pop($aType);

$curTime = date("Y-m-d_H:i:s");
$filename = "novas_leads_followUp_$type" . "_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

function audioCalc($ar500, $al500, $ar1000, $al1000, $ar2000, $al2000, $ar4000, $al4000)
{
    $right_ear = (object)array("value" => 0, "text" => "Sem Perda");
    $left_ear = (object)array("value" => 0, "text" => "Sem Perda");

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
    return (object)array("right" => $right_ear, "left" => $left_ear, "result" => $result);
}

$extractor = function ($a) {
    return $a->value;
};

$defaultProdutos = array(
    "direito" => array(
        "gama" => "",
        "marca" => "",
        "modelo" => ""
    ),
    "esquerdo" => array(
        "gama" => "",
        "marca" => "",
        "modelo" => ""
    ),
    "tipo" => ""
);

function array_map_if_arr($method, $arr)
{
    if (is_array($arr))
        return array_map($method, $arr);
    else
        return array();
}

fputcsv($output, array(
    'Title',
    'Campaign No.',
    'First Name',
    'Middle Name',
    'Surname',
    'Address 1',
    'Address 2',
    'Address 3',
    'County',
    'Post Code',
    'Area Code',
    'No. Porta',
    'City',
    'Concelho',
    'Country Code',
    'Phone No.',
    'Mobile Phone No.',
    'Work Phone No.',
    'Email',
    'Insurance Scheme Presc.',
    'Date of Birth',
    'No.',
    'Update contact',
    'Service Request',
    'Territory Code',
    'Salesperson Code',
    'On Hold',
    'Exclude Reason Code',
    'Pensionner',
    'Want Info from other companies',
    'Appointment time',
    'Appointment date',
    'Visit Location',
    'Branch',
    'Comments',
    'Salesperson Team',
    'Tipo Cliente',
    "Contact No.",
    "Interaction Log Entry No.",
    "Sugar Contact No.",
    "ID Consulta SPICE",
    "Date Entered",
    "No Contact Reason",
    "Date Start",
    "No Test Reason",
    "No Sell Reason",
    "Assigned UserId",
    "Salesperson Code",
    "Campaign No.",
    "Audiogram",
    "Appointment Result",
    "3a Pessoa",
    "Nome 3a Pessoa",
    "Tipo Perda Dto.",
    "Tipo Perda Esq.",
    "Pct. Perda Dto.",
    "Pct. Perda Esq.",
    "Marca Dto.",
    "Marca Esq.",
    "Gama Dto.",
    "Gama Esq.",
    "Modelo Dto.",
    "Modelo Esq.",
    "Resultado",
    "Tipo de Venda"), ";");

if ($type == "dispenser") {
    $dispens_cc = "NO";
} else {
    $dispens_cc = "YES";
}

$query_log = "SELECT * FROM (SELECT
                a.lead_id
                a.title,
                a.extra1,
                a.first_name,
                a.middle_initial,
                a.last_name,
                a.address1,
                a.address2,
                a.address3,
                a.state,
                a.postal_code,
                a.extra3,
                a.extra10,
                a.city,
                a.province,
                a.country_code,
                a.phone_number,
                a.alt_phone,
                a.email,
                a.date_of_birth,
                a.extra2,
                a.comments
                FROM vicidial_list a
                INNER JOIN vicidial_users b ON a.user=b.user
                WHERE a.entry_date BETWEEN :data_inicial AND :data_final AND a.extra6=:type AND a.status='NEW' AND b.user_group=:user_group AND b.user_level<5)
                a INNER JOIN
                (SELECT extra2 'cod cliente', a.extra_id as 'interaction log', a.lead_id 'sugar ref', id_reservation , a.entry_date, consulta_razao ,start_date, exame_razao, venda_razao, f.user, alias_code as 'salesperson code', extra1 'camp cod', IF(exame,'YES','NO') 'exame', feedback, terceira_pessoa
            FROM sips_sd_reservations a
            INNER JOIN sips_sd_resources g ON a.id_resource = g.id_resource
            LEFT JOIN vicidial_list d ON a.lead_id = d.lead_id
            INNER JOIN spice_consulta f ON a.id_reservation=f.reserva_id
            WHERE f.closed=1 AND f.data BETWEEN :data_inicial AND :data_final $where)
            b ON a.lead_id=b.'sugar ref'
            INNER JOIN
            (SELECT uniqueid, extra2 'codCliente', a.extra_id AS 'itLogID', a.lead_id , id_reservation , a.entry_date, f.user, consulta_razao, alias_code AS 'salespersonCode', f.produtos, f.venda, MAX(IF(g.name='AR',g.value,''))'AR',MAX(IF(g.name='AL',g.value,'')) 'AL',MAX(IF(g.name='BCR',g.value,'')) 'BCR',MAX(IF(g.name='BCL',g.value,'')) 'BCL',MAX(IF(g.name='ULLR',g.value,'')) 'ULLR',MAX(IF(g.name='ULLL',g.value,'')) 'ULLL'
                FROM sips_sd_reservations a
                INNER JOIN sips_sd_resources b ON a.id_resource = b.id_resource
                INNER JOIN vicidial_list d ON a.lead_id = d.lead_id
                INNER JOIN spice_consulta f ON a.id_reservation=f.reserva_id
                INNER JOIN spice_audiograma g ON a.id_reservation=g.uniqueid
                WHERE f.closed=1 AND f.exame=1 AND f.data BETWEEN :data_inicial AND :data_final $where GROUP BY g.uniqueid)
                c  ON b.id_reservation=c.uniqueid";
$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59", ":type" => $dispens_cc, ":user_group" => $u->user_group));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


    $ar = array_map_if_arr($extractor, json_decode($row->AR));
    $al = array_map_if_arr($extractor, json_decode($row->AL));

    if ((bool)$row->venda) {
        $produtos = json_decode($row->produtos, true);
        $produtos = (is_array($produtos)) ? array_replace_recursive($defaultProdutos, $produtos) : $defaultProdutos;
    } else {
        $produtos = $defaultProdutos;
    }

    $audioResult = audioCalc($ar[1], $al[1], $ar[2], $al[2], $ar[3], $al[3], $ar[5], $al[5]);


    fputcsv($output, array(
        $row['title'],
        $row['extra1'],
        $row['first_name'],
        $row['middle_initial'],
        $row['last_name'],
        $row['address1'],
        $row['address2'],
        $row['address3'],
        $row['state'],
        $row['postal_code'],
        $row['extra3'],
        $row['extra10'],
        $row['city'],
        $row['province'],
        $row['country_code'],
        $row['phone_number'],
        $row['alt_phone'],
        "",
        $row['email'],
        $row['extra5'],
        $row['date_of_birth'],
        $row['extra2'],
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
        $row['comments'],
        "",
        "",//fim novas leads
        $row['cod cliente'],
        $row['interaction log'],
        $row['sugar ref'],
        $row['id_reservation'],
        $row['entry_date'],
        $row['consulta_razao'],
        $row['start_date'],
        $row['exame_razao'],
        $row['venda_razao'],
        $row['user'],
        $row['salesperson code'],
        $row['camp cod'],
        $row['exame'],
        $row['feedback'],
        $row['terceira_pessoa'],//fim consultas
        $audioResult->right->text,
        $audioResult->left->text,
        $audioResult->right->value,
        $audioResult->left->value,
        $produtos['direito']['marca'],
        $produtos['esquerdo']['marca'],
        $produtos['direito']['gama'],
        $produtos['esquerdo']['gama'],
        $produtos['direito']['modelo'],
        $produtos['esquerdo']['modelo'],
        $audioResult->result,
        $produtos['tipo']
    ), ";");
}

fclose($output);
