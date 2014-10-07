<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "log_correio_$type" . "_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');


fputcsv($output, array(
    'ID evento',
    'Data Criação',
    'Agente',
     "Carta porte",
    'Observaçoes',
    'Mensagem',
    'Comentários',
    'Pedido',
    'Pendente',
    'Aprovado'), ";");


$query_log = "SELECT a.id, a.user,a.entry_time,a.carta_porte,a.data_envio,a.anexo,a.comments,a.status ,max(IF(b.status=2 OR b.status=1 ,b.note,'')) note,MAX(IF(b.status=0,b.event_date,'')) pedido,MAX(IF(b.status=2,b.event_date,'') ) pendente,MAX(IF(b.status=1,b.event_date,'') ) aprovado FROM spice_report_correio a inner join spice_log b on a.id=b.record_id where   b.section='Correio' and    a.entry_time  BETWEEN :data_inicial AND :data_final  GROUP BY record_id;";
$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $note = json_decode($row["note"]);
    fputcsv($output, array(
        $row['id'],
        $row['entry_time'],
        $row['user'],
        $row['carta_porte'],
        $note->obs,
        $note->msg,
        $row['comments'],
        $row['pedido'],
        $row['pendente'],
        $row['aprovado']), ";");
}

fclose($output);

