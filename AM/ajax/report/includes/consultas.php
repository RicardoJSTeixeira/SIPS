<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "novas_leads_followUp" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

fputcsv($output, array(
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
    "Nome 3a Pessoa"), ";");

$query = "SELECT extra2 'cod cliente', '' as 'interaction log', a.lead_id 'sugar ref', id_reservation , a.entry_date, consulta_razao ,start_date, exame_razao, venda_razao, f.user, alias_code as 'salesperson code', extra1 'camp cod', IF(exame,'YES','NO'), feedback, terceira_pessoa "
        . "FROM sips_sd_reservations a "
        . "INNER JOIN sips_sd_resources g ON a.id_resource = g.id_resource "
        . "INNER JOIN vicidial_list d ON a.lead_id = d.lead_id "
        . "INNER JOIN spice_consulta f ON a.id_reservation=f.reserva_id "
        . "WHERE f.closed=1 AND a.start_date BETWEEN :data_inicial AND :data_final;";

$stmt = $db->prepare($query);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $terceira_pessoa = json_decode(array_pop($row));
    if (count($terceira_pessoa)) {
        $row[] = $terceira_pessoa->tipo;
        $row[] = $terceira_pessoa->nome;
    } else {
        $row[] = "";
        $row[] = "";
    }

    fputcsv($output, $row, ";");
}

fclose($output);