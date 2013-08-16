<?php
require('../ini/dbconnect.php');
require('../requests/_json_convert.php');
foreach ($_POST as $key => $value) {
	${$key} = $value;
}
foreach ($_GET as $key => $value) {
	${$key} = $value;
}
if ($action == 'update_status') {
        $is_cb = "SELECT scheduled_callback from vicidial_campaign_statuses where status='$send_feedback' LIMIT 1 UNION ALL SELECT scheduled_callback from vicidial_statuses where status='$send_feedback' LIMIT 1";
        $is_cb = mysql_query($is_cb);
        $is_cb = mysql_fetch_row($is_cb);
        $is_cb = $is_cb[0];
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
        
        echo $is_cb;
        
}

if ($action == 'update_contact_field') {
	$query = "UPDATE vicidial_list SET $send_field='$send_field_value' where lead_id='$send_lead_id'";
	$query = mysql_query($query, $link) or die(mysql_error());
}
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
if ($action== 'get_list') {
	
	$query = "SELECT phone_number, first_name, address1 FROM vicidial_list where list_id=103";
	$query = mysql_query($query, $link) or die(mysql_error());
	
	$aColumns = array( 'A', 'B', 'C');
	
	$rows = array();
	
	
	while($r = mysql_fetch_array($query)) {
	    $rows[] = $r;
	}
	

echo __json_encode($rows);



	//print_r($rows);
	
	
	/* 	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => 6000,
		"iTotalDisplayRecords" => 6000,
		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $query ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			
				$row[] = $aRow[ $aColumns[$i] ];
			
		}
		$output['aaData'][] = $row;
	}
	
	echo __json_encode( $output ); */
	}



/*if ($action == 'reset_feedback') {
	$query = "UPDATE vicidial_list SET set modify_date='0000-00-00 00:00:00', status='NEW', user='', called_since_last_reset='N',called_count='0',last_local_call_time='2008-01-01 00:00:00' WHERE lead_id='$lead_id'";
		
}




/*	# Update vicidial_agent_log
	$query = "UPDATE vicidial_agent_log SET status='$send_feedback' WHERE lead_id='$send_lead_id' ORDER BY agent_log_id DESC LIMIT 1";
	$query = mysql_query($query, $link) or die(mysql_error());
	if(!$query){exit;}
	
	# Update vicidial_closer_log | inbound
	$query = "UPDATE vicidial_closer_log SET status='$send_feedback' WHERE lead_id='$send_lead_id'  ORDER BY call_date DESC LIMIT 1";
	$query = mysql_query($query, $link) or die(mysql_error());
	if(!$query){exit;}

/*	

	
	# talvez se acrescente esta opção
	
	if ($add_closer_record > 0)
		{
		### insert a NEW record to the vicidial_closer_log table 
		$stmt="INSERT INTO vicidial_closer_log (lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed) values('" . mysql_real_escape_string($lead_id) . "','" . mysql_real_escape_string($list_id) . "','" . mysql_real_escape_string($campaign_id) . "','" . mysql_real_escape_string($parked_time) . "','$NOW_TIME','$STARTtime','1','" . mysql_real_escape_string($status) . "','" . mysql_real_escape_string($phone_code) . "','" . mysql_real_escape_string($phone_number) . "','$PHP_AUTH_USER','" . mysql_real_escape_string($comments) . "','Y')";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	 */
	


?>