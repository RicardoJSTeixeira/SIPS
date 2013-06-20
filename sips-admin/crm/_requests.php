<?php
require("../../ini/_json_convert.php");
require("../../ini/dbconnect.php");
require("../../ini/functions.php");
foreach ($_POST as $key => $value) {
	${$key} = $value;
}
foreach ($_GET as $key => $value) {
	${$key} = $value;
}

/* Actualização das Dropdowns qnd se muda de campanha */
if($action == "campaign_change_db")
{
	$query = "SELECT list_id, list_name FROM vicidial_lists WHERE campaign_id='$sent_campaign'";
	$query = mysql_query($query,$link); 
	
	$rows = array();
	while($r = mysql_fetch_assoc($query)) {
    $rows['db_list'][] = $r; }
	print __json_encode($rows);
}
if($action == "campaign_change_feedback")
{
	$query = "SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id='$sent_campaign'";
	$query = mysql_query($query,$link); 
	
	$rows = array();
	while($r = mysql_fetch_assoc($query)) {
    $rows['feed_list'][] = $r; }
	print __json_encode($rows);
}
/* -------------------------------------------------- */

/* Contrução da tabela de resultados conforme os filtros escolhidos pelo utilizador */ 
if($action == "get_table_data") 
{

	# Contrução do Filtro das Datas
	$tExplode = explode("     ", $datai);
	$datai = DatePT2DateSQL($tExplode[0])." ".$tExplode[1];
	$tExplode = explode("     ", $dataf);
	$dataf = DatePT2DateSQL($tExplode[0])." ".$tExplode[1];
	if ($dataflag==1) 
	{
		
		$data_QUERY = " AND last_local_call_time >= '$datai' AND last_local_call_time <= '$dataf' ";
	}
	else
	{
		$data_QUERY = " AND entry_date >= '$datai' AND entry_date <= '$dataf' ";
	}
	# Construção do Filtro das Campanhas/BDs
	if($filtro_dbs=="all")
	{
		$query = "SELECT list_id FROM vicidial_lists WHERE campaign_id='$filtro_campanha'";
		$query = mysql_query($query,$link);
		$list_IN = Query2IN($query, 0);
	} 
	else 
	{
		$list_IN = "'".$filtro_dbs."'";
	}
	# Construção dos Filtros dos Operadores
	if($filtro_operador!='all')
	{
		$operador_QUERY = " AND user='$filtro_operador'";
	}
	# Construção dos Filtros dos Feedbacks
	if($filtro_feedback!='all')
	{
		$feedback_QUERY = " AND status='$filtro_feedback'";
	}
	
	


$aColumns = array('lead_id',  'first_name', 'phone_number', 'address1', 'last_local_call_time');


if($contact_id == "" || $contact_id == null)
{
    $sQuery = "
            SELECT first_name, phone_number, address1 ,last_local_call_time, lead_id
            FROM   vicidial_list
            WHERE list_id IN($list_IN)
            $data_QUERY
            $operador_QUERY
            $feedback_QUERY
            LIMIT 3000
            ";
} else {
        $sQuery = "
            SELECT first_name, phone_number, address1 ,last_local_call_time, lead_id
            FROM   vicidial_list
            WHERE lead_id= '$contact_id'
            LIMIT 1
            ";
}



		//echo $sQuery;
$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
$output = array("aaData" => array()	);
	
while ( $aRow = mysql_fetch_array( $rResult ) )
{
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		
		if($aColumns[$i]=='first_name') {
			if(strlen($aRow['first_name']) > 45) 
			{
				while(strlen($aRow['first_name']) > 45)
				{
					$aRow['first_name'] = substr($aRow['first_name'], 0, -1);
				}
			$aRow['first_name'] = $aRow['first_name']." ...";
			}  
			$row[] = "<a onclick='LoadHTML($aRow[lead_id]);' style='cursor:pointer'>".$aRow['first_name']."<img style='float:left' src='../../images/icons/livejournal_16.png'></a>"; } else {$row[] = $aRow[ $aColumns[$i] ];
                        
                        }
		
	}
	$output['aaData'][] = $row;
}
 echo __json_encode( $output );

}
#################################
if ($action == 'update_contact_field') {
	$query = "UPDATE vicidial_list SET $send_field='$send_field_value' where lead_id='$send_lead_id'";
	$query = mysql_query($query, $link);
	echo $query;
}
###############################
if ($action == 'update_feedback') {
	

	# Update vicidial_list
	$query = "UPDATE vicidial_list SET status='$send_feedback' WHERE lead_id='$send_lead_id'";	
	$querya = mysql_query($query);
	
	# Update vicidial_log 
	$query = "UPDATE vicidial_log SET status='$send_feedback' WHERE lead_id='$send_lead_id' ORDER BY call_date DESC LIMIT 1";
	$queryb = mysql_query($query);
	
	# Update vicidial_agent_log
	$query = "UPDATE vicidial_agent_log SET status='$send_feedback' WHERE lead_id='$send_lead_id' ORDER BY agent_log_id DESC LIMIT 1";
	$queryc = mysql_query($query);

	# Update vicidial_closer_log | inbound
	$query = "UPDATE vicidial_closer_log SET status='$send_feedback' WHERE lead_id='$send_lead_id'  ORDER BY call_date DESC LIMIT 1";
	$queryd = mysql_query($query);
	
	if($querya and $queryb and $queryc and $queryd){ echo 1;} else { echo 0;}

}
#############################
if ($action== 'get_list') {
	
	$query = "SELECT phone_number, first_name, address1 FROM vicidial_list where list_id=103";
	$query = mysql_query($query, $link) or die(mysql_error());
	
	$aColumns = array( 'A', 'B', 'C');
	
	$rows = array();
	
	
	while($r = mysql_fetch_array($query)) {
	    $rows[] = $r;
	}
	

echo __json_encode($rows);


	}
###########################################
if ($action == 'update_contact_field') {
	$query = "UPDATE vicidial_list SET $send_field='$send_field_value' where lead_id='$send_lead_id'";
	$query = mysql_query($query, $link) or die(mysql_error());
}
###########################################
if ($action == 'update_feedback') {
	
	mysql_query("START TRANSACTION");
	# Update vicidial_list
	$query = "UPDATE vicidial_list SET status='$send_feedback' WHERE lead_id='$send_lead_id'";	
	$querya = mysql_query($query);
	
	# Update vicidial_log 
	$query = "UPDATE vicidial_log SET status='$send_feedback' WHERE lead_id='$send_lead_id' ORDER BY call_date DESC LIMIT 1";
	$queryb = mysql_query($query);
	
	# Update vicidial_agent_log
	$query = "UPDATE vicidial_agent_log SET status='$send_feedback' WHERE lead_id='$send_lead_id' ORDER BY agent_log_id DESC LIMIT 1";
	$queryc = mysql_query($query);

	# Update vicidial_closer_log | inbound
	$query = "UPDATE vicidial_closer_log SET status='$send_feedback' WHERE lead_id='$send_lead_id'  ORDER BY call_date DESC LIMIT 1";
	$queryd = mysql_query($query);
	
	if($querya and $queryb and $queryc and $queryd){ mysql_query("COMMIT");} else {echo mysql_query("ROLLBACK"); echo "Erro ao Gravar na DB.";}

}
#########################################
if ($action== 'get_list') {
	
	$query = "SELECT phone_number, first_name, address1 FROM vicidial_list where list_id=103";
	$query = mysql_query($query, $link) or die(mysql_error());
	
	$aColumns = array( 'A', 'B', 'C');
	
	$rows = array();
	
	
	while($r = mysql_fetch_array($query)) {
	    $rows[] = $r;
	}
	

echo __json_encode($rows);
}




	

	
?> 