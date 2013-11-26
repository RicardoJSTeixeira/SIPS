<? require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) { 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

function RecycleListBuilder($CampaignID, $CampaignLists, $RecycleID, $RecycleName, $RecycleDelay, $RecycleTries, $AllowedCampaigns, $Flag, $link)
{
	if($Flag == "ALL" || $Flag == "REFRESH")
	{
		$query = "SELECT A.status, A.attempt_delay, A.attempt_maximum, A.active, B.status_name AS sysname, C.status_name AS campname FROM vicidial_lead_recycle A LEFT JOIN vicidial_statuses B ON A.status=B.status LEFT JOIN vicidial_campaign_statuses C ON A.status=C.status  WHERE A.campaign_id='$CampaignID' GROUP BY A.status ORDER BY sysname, campname DESC";
		
		$query = mysql_query($query, $link) or die(mysql_error());
		
		if(mysql_num_rows($query) > 0)
		{
			while($row = mysql_fetch_assoc($query))
			{
				$js['recycle'][] = $row['status'];
				$js['attempt_delay'][] = ($row['attempt_delay'] / 60);
				$js['attempt_maximum'][] = $row['attempt_maximum'];
				$js['active'][] = $row['active'];
				if($row['sysname'] == NULL){ $js['recycle_name'][] = $row['campname']; } else { $js['recycle_name'][] = $row['sysname']; }
			}
				
		}

		$CampaignLists = implode("','", $CampaignLists);
		$campaign_recycle = implode("','", $js['recycle']);
		$query = "SELECT status, count(*) FROM vicidial_list a inner join vicidial_lists b on a.list_id = b.list_id WHERE a.list_id IN ('$CampaignLists') AND status IN ('$campaign_recycle') and b.active = 'Y' GROUP BY status";
		$query = mysql_query($query, $link) or die(mysql_error());
		if(mysql_num_rows($query) > 0)
		{
			while($row = mysql_fetch_row($query))
			{
                            foreach($js['recycle'] as $index => $value)
                            {
                                    if($row[0] == $value)
                                    {       
                                            $queryX = "SELECT count(lead_id) FROM vicidial_list a inner join vicidial_lists b on a.list_id = b.list_id WHERE a.list_id IN ('$CampaignLists') AND status like '$value' and b.active = 'Y' and SUBSTRING_INDEX(a.called_since_last_reset,'Y', -1) < (select attempt_maximum from vicidial_lead_recycle where campaign_id like '$CampaignID' and status like '$value') GROUP BY status";
                                            $queryX = mysql_query($queryX, $link) or die(mysql_error());
                                            $rowX = mysql_fetch_row($queryX);
                                            $js['recycle_count'][$index] = $rowX[0]; break;
                                    }

                            }
                        }
		}
		else
		{
			foreach($js['recycle'] as $index => $value) { $js['recycle_count'][] = 0; }
		}
			

	echo json_encode($js);	
	} 
	else 
	{
		$RecycleDelay = ($RecycleDelay * 60);
		
		
		//$array_allowed = explode("','", $sent_allowed_campaigns);
			
			foreach($AllowedCampaigns as $key=>$value)
			{
											

					if($value == $CampaignID) { $active = "Y"; } else { $active = "N";}
					mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$value', '$RecycleID', '$RecycleDelay', '$RecycleTries', '$active')") or die(mysql_error());

			}
			
			$CampaignLists = implode("','", $CampaignLists);
			$query = "SELECT count(*) FROM vicidial_list WHERE list_id IN ('$CampaignLists') AND status = '$RecycleID'";
			$query = mysql_query($query, $link) or die(mysql_error());
			$row = mysql_fetch_row($query);
			
			mysql_query("UPDATE sips_campaign_stats SET recycle = recycle + 1 WHERE campaign_id='$CampaignID'") or die(mysql_error());
			
			
			$js['recycle'][] = $RecycleID;
			$js['recycle_count'][] = $row[0];
			$js['attempt_delay'][] = ($RecycleDelay / 60);
			$js['attempt_maximum'][] = $RecycleTries;
			$js['recycle_name'][] =  $RecycleName;
			$js['active'][] = "Y";
				
		echo json_encode($js);
	}
}

function RecycleAvailFeeds($CampaignID, $link)
{
	$query = "SELECT A.status, A.status_name FROM vicidial_campaign_statuses A  LEFT OUTER JOIN vicidial_lead_recycle B ON A.status = B.status AND A.campaign_id = B.campaign_id WHERE A.campaign_id = '$CampaignID' AND B.status IS NULL ORDER BY A.status_name ";
	$query = mysql_query($query, $link) or die(mysql_error());
	
	while($row = mysql_fetch_assoc($query))
	{
		$js['status'][] = $row['status'];
		$js['status_name'][] = $row['status_name'];
	}
	echo json_encode($js);
	
}

function RecycleActiveInactive($CampaignID, $RecycleID, $RecycleActive, $NumRecycle, $link)
{
	mysql_query("UPDATE vicidial_lead_recycle SET active='$RecycleActive' WHERE status='$RecycleID' AND campaign_id='$CampaignID'") or die(mysql_error());
	mysql_query("UPDATE sips_campaign_stats SET recycle = recycle+($NumRecycle) WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
	
	if($sent_recycle_status == "Y")
	{
		mysql_query("UPDATE vicidial_campaigns SET dial_statuses = CONCAT(' $RecycleID', dial_statuses) WHERE campaign_id='$CampaignID'") or die(mysql_error());
	}
	else
	{
		mysql_query("UPDATE vicidial_campaigns SET dial_statuses = REPLACE(dial_statuses, ' $RecycleID', '') WHERE campaign_id='$CampaignID'") or die(mysql_error());
	}
	
}

function ApplyRecycleToAllCampaings($RecycleID, $RecycleActive, $RecycleDelay, $RecycleTries, $link)
{
	$counter = 0;
	
	for($i=0;$i<count($RecycleID);$i++)
	{
		$RecycleDelay[$i] = ($RecycleDelay[$i] * 60);
		$query = "UPDATE vicidial_lead_recycle SET active='$RecycleActive[$i]', attempt_delay='$RecycleDelay[$i]', attempt_maximum='$RecycleTries[$i]' WHERE status ='$RecycleID[$i]'";
		mysql_query($query, $link) or die(mysql_error());
		if($RecycleActive[$i] == "Y"){ $counter++; }
	}
	mysql_query("UPDATE sips_campaign_stats SET recycle = '$counter'", $link) or die(mysql_error());
}

// dialog edit recycle

function GetRecycleDetails($CampaignID, $CampaignLists, $RecycleID, $link)
{
	//VARS
	$aColumns = array('tentativas', 'contactos', 'reset', 'detalhes', 'index');
	$aTries = array("Não Chamado", "Chamado", "1ª Reciclagem", "2ª Reciclagem", "3ª Reciclagem", "4ª Reciclagem", "5ª Reciclagem", "6ª Reciclagem", "7ª Reciclagem", "8ª Reciclagem", "9ª Reciclagem", "10ª Reciclagem");
	$output = array("aaData" => array() );
 

 
	$CampaignLists = preg_replace("/,/", "','", $CampaignLists); 
	 
    $sQuery = "SELECT called_since_last_reset AS tentativas, count(*) AS contactos FROM vicidial_list WHERE list_id IN ('$CampaignLists') AND status='$RecycleID' GROUP BY called_since_last_reset ORDER BY FIELD(called_since_last_reset, 'N', 'Y', 'Y1', 'Y2', 'Y3', 'Y4', 'Y5', 'Y6', 'Y7', 'Y8', 'Y9', 'Y10')";
	
    
    
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
			switch($aRow['tentativas'])
			{
				case "N": $index=0; $try_type = "Não Chamado"; break;
				case "Y": $index=1; $try_type = "Chamado"; break;
				default: $explode = explode("Y", $aRow['tentativas']); $index = $explode[1]+1; $try_type = $explode[1]."ª Reciclagem"; break; 				
			}
            switch($aColumns[$i]) 
            { 
                case "tentativas": $row[] = $try_type; break;
				case "contactos": $row[] = $aRow[ $aColumns[$i] ]; break;
                case "reset": if($index == 0) {$cursor = "style='cursor: default !important;'"; } else {$cursor = ""; } $row[] = "<img $cursor class='css-dt-icon recycle-details-reset-single' title='Realiza um Reset aos Contactos que têm este número de tentativas.' src='icons/mono_undo_16.png'>"; break; 
				case "detalhes": $row[] = "<img class='css-dt-icon recycle-details-view-contacts' inner-try-type='$aRow[tentativas]' title='Ver Contactos' src='icons/mono_right_expand_16.png'>"; break; 
				case "index": $row[] = $index; break;
				default: $row[] = $aRow[ $aColumns[$i] ];
            }
        }
		$output['aaData'][] = $row;
    }
  
	foreach($output['aaData'] as $index => $array)
	{
		unset($aTries[$array[4]]);
	}

	foreach($aTries as $index => $value)
	{
		$output['aaData'][] = array($value, 0, "<img class='css-dt-icon  recycle-details-reset-single' src='icons/mono_undo_16.png'>", "<img class='css-dt-icon no-pointer' src='icons/mono_right_expand_16.png'>", $index);
	} 
	
echo json_encode( $output );	
}

function DialogEditRecycleOnSave($RecycleID, $RecycleDelay, $RecycleTries, $CampaignID, $link)
{
	$RecycleDelay = ($RecycleDelay * 60);
	mysql_query("UPDATE vicidial_lead_recycle SET attempt_delay = '$RecycleDelay', attempt_maximum='$RecycleTries' WHERE campaign_id='$CampaignID' AND status='$RecycleID'", $link) or die(mysql_error());
}

function EditRecycleResetSingleTries($RecycleID, $Index, $CampaignLists, $link)
{
	$CampaignLists = implode("','", $CampaignLists);
	
	if($Index == 1){ $Index = "Y"; } else { $Index = "Y".($Index-1); }
	
	mysql_query("UPDATE vicidial_list SET called_since_last_reset = 'N' WHERE list_id IN ('$CampaignLists') AND status='$RecycleID' AND called_since_last_reset = '$Index'") or die(mysql_error());
}

function EditRecycleResetAllTries($RecycleID, $CampaignLists, $link)
{
	//$CampaignLists = preg_replace("/,/" , "/','/", $CampaignLists);
	
	//echo "UPDATE vicidial_list SET called_since_last_reset = 'N' WHERE list_id IN ('$campaign_lists') AND status='$sent_recycle'";
	
	mysql_query("UPDATE vicidial_list SET called_since_last_reset = 'N' WHERE list_id IN ('".implode("','", $CampaignLists)."') AND status='$RecycleID'") or die(mysql_error());
}

// dialog contact details

function TableRecycleContactDetailsInit($RecycleID, $TryType, $CampaignLists, $link)
{
	//VARS
	$aColumns = array('first_name', 'phone_number', 'total_calls', 'last_call', 'reset', 'dnc');
	$output = array("aaData" => array() );
	
	//$CampaignLists = preg_replace("/,/", "/','/", $CampaignLists); 
	
	 
    $sQuery = "	SELECT A.lead_id, B.first_name AS first_name, B.phone_number AS phone_number, B.called_count AS total_calls, DATE_FORMAT(MAX(A.call_date), '%H:%i:%s %e/%c/%Y') AS last_call FROM vicidial_log A 
				INNER JOIN vicidial_list B ON A.lead_id=B.lead_id 
				WHERE B.list_id IN($CampaignLists)
				AND B.status='$RecycleID' AND B.called_since_last_reset = '$TryType' 
				GROUP BY B.lead_id";
    $rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            switch($aColumns[$i]) 
            { 
                case "first_name": $row[] = $aRow['first_name']; break;
				case "phone_number": $row[] = $aRow['phone_number']; break;
                case "total_calls": $row[] = $aRow['total_calls']; break; 
				case "last_call": $row[] = $aRow['last_call']; break; 
				case "reset": $row[] = "<img class='css-dt-icon recycle-contact-details-single-reset' lead-id='$aRow[lead_id]' title='Realiza um Reset a este Contacto.' src='icons/mono_undo_16.png'>"; break;
				case "dnc": $row[] =  "<img class='css-dt-icon recycle-contact-details-single-disable' lead-id='$aRow[lead_id]' title='Retira este contacto da Reciclagem.' src='icons/mono_minus_16.png'>"; break;
				default: $row[] = $aRow[ $aColumns[$i] ];
            }
        }
		$output['aaData'][] = $row;
    }

echo json_encode( $output );	
}

function EditRecycleContactDetailsResetAll($RecycleID, $TryType, $CampaignLists, $link)
{
	mysql_query("UPDATE vicidial_list SET called_since_last_reset = 'N' WHERE list_id IN ('".implode("','", $CampaignLists )."') AND status='$RecycleID' AND called_since_last_reset = '$TryType'") or die(mysql_error());
}

function EditRecycleContactDetailsDisableAll($RecycleID, $TryType, $CampaignLists, $link)
{
	mysql_query("UPDATE vicidial_list SET called_since_last_reset = 'N', status='MRYS' WHERE list_id IN ('".implode("','", $CampaignLists )."') AND status='$RecycleID' AND called_since_last_reset = '$TryType'") or die(mysql_error());
	$js['affected_rows'] = mysql_affected_rows();
	echo json_encode($js);
}

function EditRecycleContactDetailsResetSingle($LeadID, $link)
{
	mysql_query("UPDATE vicidial_list SET called_since_last_reset = 'N' WHERE lead_id='$LeadID'") or die(mysql_error());
}

function EditRecycleContactDetailsDisableSingle($LeadID, $link)
{
	mysql_query("UPDATE vicidial_list SET called_since_last_reset = 'N', status = 'MRYS' WHERE lead_id='$LeadID'") or die(mysql_error());
}

function DialogRecycleResetCallbacksOnSave($CampaignID, $link)
{
    $query = "SELECT lead_id FROM vicidial_callbacks WHERE campaign_id='$CampaignID' and status != 'INACTIVE'";
    $query = mysql_query($query, $link) or die(mysql_error());
    
    
    
    while($row = mysql_fetch_assoc($query))
    {
        $js['lead_id'][] = $row['lead_id'];
        
         mysql_query("UPDATE vicidial_list SET status='NEW', user='', called_since_last_reset ='N', called_count = 0, last_local_call_time ='2008-01-01 00:00:00' WHERE lead_id='$row[lead_id]'", $link) or die(mysql_error());
         mysql_query("DELETE FROM vicidial_callbacks WHERE lead_id='$row[lead_id]'", $link) or die(mysql_error());
        
    }
    
    
    
    
    
    
    echo json_encode($js);
    
}

switch($action)
{
	case "RecycleListBuilder": RecycleListBuilder($CampaignID, $CampaignLists, $RecycleID, $RecycleName, $RecycleDelay, $RecycleTries, $AllowedCampaigns, $Flag, $link); break;
	case "RecycleAvailFeeds": RecycleAvailFeeds($CampaignID, $link); break;
	case "RecycleActiveInactive": RecycleActiveInactive($CampaignID, $RecycleID, $RecycleActive, $NumRecycle, $link); break;
	case "ApplyRecycleToAllCampaings": ApplyRecycleToAllCampaings($RecycleID, $RecycleActive, $RecycleDelay, $RecycleTries, $link); break;
	case "GetRecycleDetails": GetRecycleDetails($CampaignID, $CampaignLists, $RecycleID, $link); break;
	case "DialogEditRecycleOnSave": DialogEditRecycleOnSave($RecycleID, $RecycleDelay, $RecycleTries, $CampaignID, $link); break;
	case "EditRecycleResetSingleTries": EditRecycleResetSingleTries($RecycleID, $Index, $CampaignLists, $link); break;
	case "EditRecycleResetAllTries": EditRecycleResetAllTries($RecycleID, $CampaignLists, $link); break;
	case "TableRecycleContactDetailsInit": TableRecycleContactDetailsInit($RecycleID, $TryType, $CampaignLists, $link); break;	
	case "EditRecycleContactDetailsResetAll": EditRecycleContactDetailsResetAll($RecycleID, $TryType, $CampaignLists, $link); break;
	case "EditRecycleContactDetailsDisableAll": EditRecycleContactDetailsDisableAll($RecycleID, $TryType, $CampaignLists, $link); break;
	case "EditRecycleContactDetailsResetSingle": EditRecycleContactDetailsResetSingle($LeadID, $link); break;
	case "EditRecycleContactDetailsDisableSingle": EditRecycleContactDetailsDisableSingle($LeadID, $link); break;
        case "DialogRecycleResetCallbacksOnSave": DialogRecycleResetCallbacksOnSave($CampaignID, $link); break;
}
?>
