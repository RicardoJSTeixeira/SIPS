<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "log_frota_$type" . "_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');


fputcsv($output, array(
    'ID evento',
    'Data Criação',
    'Agente',
    'Tipo',
    "Matricula",
    "Viatura",
    "Km",
     'Observações',
    'Mensagem',
    'Comentários',
    'Pedido',
    'Pendente',
    'Aprovado'), ";");



$query_log = "SELECT a.id record_id,a.user,a.entry_time,a.matricula,a.km,a.viatura,a.comments,a.ocorrencia,a.status,b.type,max(IF(b.status=2 OR b.status=1 ,b.note,'')) note,MAX(IF(b.status=0,b.event_date,'')) pedido,MAX(IF(b.status=2,b.event_date,'') ) pendente,MAX(IF(b.status=1,b.event_date,'') ) aprovado FROM spice_report_frota a inner join   spice_log  b   on a.id=b.record_id where b.section='Frota' and a.entry_time  BETWEEN :data_inicial AND :data_final group by record_id;";
$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $note = json_decode($row["note"]);
    fputcsv($output, array(
        $row['record_id'],
        $row['entry_time'],
        $row['user'],
        $row['type'],
        $row['matricula'],
        $row['viatura'],
        $row['km'],
        $note->obs,
        $note->msg,
        $row['comments'],
        $row['pedido'],
        $row['pendente'],
        $row['aprovado']), ";");
}

fclose($output);

