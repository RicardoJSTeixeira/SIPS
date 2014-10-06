<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "log_Encomenda_$type" . "_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

fputcsv($output, array(
    'ID evento',
    'Data de criação',
    'Agente',
    'Tipo',
    'Tipo de encomenda',
    "Lead cliente",
    "Nº contrato",
    "Anexos",
    'Observaçoes',
    'Mensagem',
    'Data do evento',
    'Status',
"Comentários"), ";");

$status=array(0=>"Pedido Enviado",1=>"Aceite",2=>"Pendente");


$query_log = "SELECT a.id record_id,a.user,a.date,a.type enc_type,a.lead_id,a.contract_number,a.attachment,a.comments,a.status,b.event_date,a.status,b.type,b.note FROM spice_requisition a inner join (select max(id),record_id, type,note,event_date,section from  spice_log  where  section='Encomenda' group by record_id ) b on a.id=b.record_id   where a.date  BETWEEN :data_inicial AND :data_final;";
$stmt = $db->prepare($query_log);
 $stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $note = json_decode($row["note"]);
    fputcsv($output, array(
        $row['record_id'],
        $row['date'],
        $row['user'],
        $row['type'],
        $row['enc_type'],
        $row['lead_id'],
        $row['contract_number'],
        $row['attachment'],
        $note->obs,
        $note->msg,
        $row['event_date'],
       $status[(int)$row["status"]],
        $row['comments']), ";");
}

fclose($output);

