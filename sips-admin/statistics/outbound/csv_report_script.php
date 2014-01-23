<?php 
require ("../../dbconnect.php");
require ("../../functions.php");
require ("../../../ini/functions.php");
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
echo "\xEF\xBB\xBF";
header('Content-Disposition: attachment; filename=Report_Script_'.date("Y-m-d_H:i:s").'.csv');

 
if (isset($_GET["query_date"])) {$query_date = $_GET["query_date"];
} elseif (isset($_POST["query_date"])) {$query_date = $_POST["query_date"];
}
if (isset($_GET["end_date"])) {$end_date = $_GET["end_date"];
} elseif (isset($_POST["end_date"])) {$end_date = $_POST["end_date"];
}
if (isset($_GET["campanha"])) {$campanha_id = $_GET["campanha"];
} elseif (isset($_POST["campanha"])) {$campanha_id = $_POST["campanha"];
}
if (isset($_GET["feedback"])) {$feedbacks = $_GET["feedback"];
} elseif (isset($_POST["feedback"])) {$feedbacks = $_POST["feedback"];}

if($feedbacks[0] != '--TODOS--' AND count($feedbacks)>0){	
	for ($i=0; $i < count($feedbacks); $i++) {
		$array_feedbacks .= (($i != 0) ? "," : "")."'$feedbacks[$i]'";	
	}
	$feedbacks_str = "AND vcs.status IN ($array_feedbacks)";
}else{
	$feedbacks_str = "";
}

if (isset($_POST["usar_sistema"])) {
    $status="(SELECT status,status_name FROM vicidial_campaign_statuses WHERE campaign_id LIKE '$campanha_id' UNION ALL SELECT status,status_name FROM vicidial_statuses)";
}else{
    $status="(SELECT status,status_name FROM vicidial_campaign_statuses WHERE campaign_id LIKE '$campanha_id' )";
}

$end_date = date('Y-m-d', strtotime(date("Y-m-d", strtotime($end_date)) . " +1 day"));

$stmt="SELECT Name, Display_name FROM vicidial_list_ref WHERE Campaign_id = '$campanha_id' AND active=1  ORDER BY field_order ASC";
$rslt=mysql_query($stmt, $link) or die(mysql_error());
for ($i=0; $i < mysql_num_rows($rslt); $i++) { 
	$row=mysql_fetch_row($rslt);
	$campos .= (($i != 0) ? "," : "")."  A.`$row[0]` as '".mysql_real_escape_string($row[1])."' ";
}

$stmt="SHOW tables LIKE 'custom_$campanha_id';";
$rslt=mysql_query($stmt, $link) or die(mysql_error());
$tem_Script = false;
if (mysql_num_rows($rslt)>0){
	$tem_Script = 1;
	$stmt="SELECT field_label, field_name FROM vicidial_lists_fields WHERE campaign_id = '$campanha_id' AND field_type IN ('AREA', 'SELECT', 'MULTI', 'RADIO', 'CHECKBOX', 'DATE', 'TIME', 'TEXT') ORDER BY field_rank ASC";
	$rslt=mysql_query($stmt, $link) or die(mysql_error());
	for ($i=0; $i < mysql_num_rows($rslt); $i++) { 
		$row=mysql_fetch_row($rslt);
		$campos .= ",  B.`$row[0]` as '".mysql_real_escape_string($row[1])."' ";	 
	}
} 

//echo $lists_IN;

$script_query = (($tem_Script == 1) ? "LEFT JOIN custom_$campanha_id B ON A.lead_id = B.lead_id" : "");
$stmt=" SELECT  X.list_name AS 'Base de Dados', A.entry_date AS 'Data de Entrada', vcdlog.call_date AS 'Data da Chamada', vcdlog.DateTries AS 'Chamadas para este Número', Z.full_name AS 'Operador',A.lead_id as Lead_ID, $campos, vcs.status_name as 'Feedback', D.callback_time AS 'Data do Call-Back', D.comments AS 'Comentários do Call-Back' 
        FROM vicidial_list A 
        $script_query 
        INNER JOIN $status vcs ON vcs.status = A.status
		INNER JOIN (SELECT count(lead_id) AS DateTries, lead_id, call_date FROM vicidial_log WHERE call_date BETWEEN '$query_date' AND '$end_date' GROUP BY lead_id ORDER BY call_date DESC) vcdlog ON vcdlog.lead_id=A.lead_id
		INNER JOIN (SELECT count(lead_id) AS DateTries, lead_id, call_date FROM vicidial_log WHERE call_date BETWEEN '$query_date' AND '$end_date' GROUP BY lead_id ORDER BY call_date DESC) vcdlogtoo ON vcdlog.status=A.status
        LEFT JOIN (SELECT lead_id, callback_time, comments from vicidial_callbacks WHERE entry_time BETWEEN '$query_date' AND '$end_date' GROUP BY (lead_id)) D ON (D.lead_id=A.lead_id AND A.status = 'CBHOLD') 
        LEFT JOIN vicidial_users AS Z ON Z.user=A.user
        INNER JOIN vicidial_lists X ON X.list_id=A.list_id
        $feedbacks_str 
        AND X.campaign_id = '$campanha_id' ORDER by A.lead_id ";
/*
$stmt=" SELECT  X.list_name AS 'Base de Dados', A.last_local_call_time AS 'Data da Chamada', DateTries AS 'Chamadas para este Número', Z.full_name AS 'Operador', $campos, vcs.status_name as 'Feedback', D.callback_time AS 'Data do Call-Back', D.comments AS 'Comentários do Call-Back' 
        FROM vicidial_list A 
        $script_query 

        INNER JOIN $status vcs ON vcs.status = A.status
		INNER JOIN (SELECT count(lead_id) AS DateTries, lead_id FROM vicidial_log WHERE call_date BETWEEN '$query_date' AND '$end_date' GROUP BY lead_id) vcdlog ON vcdlog.lead_id=A.lead_id 
        LEFT JOIN (SELECT lead_id, callback_time, comments from vicidial_callbacks WHERE entry_time BETWEEN '$query_date' AND '$end_date' GROUP BY (lead_id)) D ON (D.lead_id=A.lead_id AND A.status = 'CBHOLD') 
        LEFT JOIN vicidial_users AS Z ON Z.user=A.user
        INNER JOIN vicidial_lists X ON X.list_id=A.list_id

        WHERE  A.last_local_call_time BETWEEN '$query_date' AND '$end_date' 
        $feedbacks_str 
        AND X.campaign_id = '$campanha_id' ORDER by A.lead_id
        ";
*/

//DEBUG da QUERY final
//echo $stmt; 
$rslt=mysql_query($stmt, $link) or die(mysql_error());



$output = fopen('php://output', 'w');
fputcsv($output, $rslt,";",'"');
// output header row (if at least one row exists)
$row = mysql_fetch_assoc($rslt);
if($row) {
	fputcsv($output, array_keys($row),";",'"');
	// reset pointer back to beginning
	mysql_data_seek($rslt, 0);
}



while($row = mysql_fetch_assoc($rslt)) {
	
	
    fputcsv($output, $row,";",'"');
}
print_r($row);
fclose($output);
?>
