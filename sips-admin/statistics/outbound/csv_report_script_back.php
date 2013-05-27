<?php
require ("../../dbconnect_noutf8.php");
require ("../../functions.php");
require ("../../../ini/functions.php");
header('Content-Type: text/csv; charset=utf-8');
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
} elseif (isset($_POST["feedback"])) {$feedbacks = $_POST["feedback"];
}

if($feedbacks[0] != '--TODOS--' AND count($feedbacks)>0){	
	for ($i=0; $i < count($feedbacks); $i++) {
		$array_feedbacks .= (($i != 0) ? "," : "")."'$feedbacks[$i]'";	
	}
	$feedbacks_str = "AND vcs.status IN ($array_feedbacks)";
}else{
	$feedbacks_str = "";
}

//$query_date = date("Y-m-d", strtotime($query_date));
$end_date = date('Y-m-d', strtotime(date("Y-m-d", strtotime($end_date)) . " +1 day"));

$stmt="SELECT Name, Display_name FROM vicidial_list_ref WHERE Campaign_id = '$campanha_id' and active = 1 ORDER BY indice ASC";
$rslt=mysql_query($stmt, $link) or die(mysql_error());
for ($i=0; $i < mysql_num_rows($rslt); $i++) { 
	$row=mysql_fetch_row($rslt);
	$campos .= (($i != 0) ? "," : "")."  C.`$row[0]` as '$row[1]' "; 
}

$stmt="SHOW tables LIKE 'custom_$campanha_id';";
//echo $stmt;
$rslt=mysql_query($stmt, $link) or die(mysql_error()); 
$tem_Script = false;
if (mysql_num_rows($rslt)>0){
	$tem_Script = 1;
	$stmt="SELECT field_label, field_name FROM vicidial_lists_fields WHERE campaign_id = '$campanha_id' AND field_type IN ('AREA', 'SELECT', 'MULTI', 'RADIO', 'CHECKBOX', 'DATE', 'TIME', 'TEXT') ORDER BY field_rank ASC";
	//echo $stmt;
	$rslt=mysql_query($stmt, $link) or die(mysql_error());
	for ($i=0; $i < mysql_num_rows($rslt); $i++) { 
		$row=mysql_fetch_row($rslt);
		$campos .= ",  B.`$row[0]` as '$row[1]' ";	
	}
}

$sQuery = "SELECT list_id FROM vicidial_lists WHERE	campaign_id='$campanha_id'";
$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
$inAllLists = Query2IN($rQuery, 0);


$script_query = (($tem_Script == 1) ? "RIGHT JOIN custom_$campanha_id B ON A.lead_id = B.lead_id" : "");
$stmt="SELECT A.list_id AS 'Base de Dados', A.call_date AS 'Data da Chamada', $campos, vcs.status_name as 'Feedback' FROM vicidial_log A $script_query INNER JOIN vicidial_list AS C ON C.lead_id=A.lead_id INNER JOIN (SELECT status,status_name,campaign_id FROM vicidial_campaign_statuses UNION ALL SELECT status,status_name,'BANANA' FROM vicidial_statuses) vcs ON vcs.status = A.status  WHERE A.call_date BETWEEN '$query_date' AND '$end_date' $feedbacks_str AND (vcs.campaign_id = '$campanha_id' OR vcs.campaign_id = 'BANANA') AND A.list_id IN($inAllLists) ORDER BY Nome";
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

#date("Y-m-d H:i", strtotime(date('Y-m-d', strtotime($date))." +$time1[0] hours $time1[1] minutes "));

$counter=0;
while($row = mysql_fetch_assoc($rslt)) {
	#if($row['Tipo da Chamada']=='VDCL'){$row['Tipo da Chamada']='INBOUND';} else {$row['Tipo da Chamada']='OUTBOUND';}
	
	#$gmt_offset = $row['gmt_offset_now'];
	
	#if($row["Data da Chamada"]!=null){ $row["Data da Chamada"] = $row["Data da Chamada"]." ".date("Y-m-d H:i", strtotime(date('Y-m-d H:i', strtotime($row["Data da Chamada"]))." +5 hours "));}
	
/*	if($counter==0) { $prev_row = $row; $prev_number = $row['Nº Telefone']; } else { 
	
	if($row['Nº Telefone'] != $prev_number) {
	
	if($counter==0) { }
	fputcsv($output, $row,";",'"'); 
	$prev_row = $row;
	$prev_number = $row['Nº Telefone'];
	} else {
		
		
		
		
		}
	
	} 
	
	
	
	
	//if($row['phone_number']==$prev_phone) {  }
	
    
	
	
	
	
	
	
$counter++;	*/

fputcsv($output, $prev_row,";",'"');

}
//print_r($row);
fclose($output);
?>
