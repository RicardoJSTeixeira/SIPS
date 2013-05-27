<?php

require '../func/reserve_utils.php';

$query="SELECT a.start_date 'data inicio', a.end_date 'data fim', c.alias_code 'tecnico', id_user 'utilizador local' FROM sips_sd_reservations a INNER JOIN sips_sd_reservations_acu b ON a.start_date=b.start_date AND a.id_resource=b.id_resource INNER JOIN sips_sd_resources c ON a.id_resource=c.id_resource
UNION ALL
SELECT a.start_date 'data inicio', NULL 'data fim', b.alias_code 'tecnico', 'importado' as 'utilizador local' FROM sips_sd_reservations_acu a INNER JOIN sips_sd_resources b ON a.id_resource=b.id_resource WHERE  a.start_date=a.start_date AND a.id_resource=a.id_resource;";

$filename="Duplicados.csv";

    // send response headers to the browser
    header('Content-Type: text/csv; charset=utf8');
    header('Content-Disposition: attachment;filename=' . $filename);
    $fp = fopen('php://output', 'w');


    $result = mysql_query($query, $link) or die(mysql_error());
    
    // output header row (if at least one row exists)
    $row = mysql_fetch_assoc($result);
    if ($row) {
        fputcsv($fp, array_keys($row));
        // reset pointer back to beginning
        mysql_data_seek($result, 0);
    }


    while ($row = mysql_fetch_assoc($result)) {
        fputcsv($fp, $row);
    }

    fclose($fp);

?>
