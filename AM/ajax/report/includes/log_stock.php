<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "log_stock_$type" . "_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');


fputcsv($output, array(
    'ID evento',
    'Data de Evento',
    'Agente',
    'Tipo',
    'Obs',
    'Data de Entrada',
    'Status'), ";");

/*
$query_log = "SELECT a.user,a.entry_date,a.data,a.status,b.event_date,b.record_id,b.type,b.note FROM spice_report_stock a inner join spice_log b on a.id=b.record_id where b.section='correio' and b.entry_date  BETWEEN :data_inicial AND :data_final;";
$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    fputcsv($output, array(
        $row['record_id'],
        $row['entry_date'],
        $row['user'],
        $row['type'],
        $row['note'],
        $row['event_date'],
        $row['status']), ";");
}

fclose($output);
*/