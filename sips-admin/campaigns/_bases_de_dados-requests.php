<?php require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) { 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

function DBListBuilder($CampaignID, $Flag, $link)
{
	
	if($Flag == "ALL" || $Flag == "REFRESH")
	{
		$result = mysql_query("SELECT list_id, list_name, list_description, event_date create_date, active FROM vicidial_lists a Left Join (SELECT event_date,`record_id`  FROM `vicidial_admin_log` WHERE `event_type` LIKE 'ADD' AND event_section like 'LISTS') b ON a.list_id=record_id WHERE campaign_id = '$CampaignID' AND visible = 1") or die(mysql_error());
		$js["bla"]="SELECT list_id, list_name, list_description, event_date create_date, active FROM vicidial_lists a Left Join (SELECT event_date,`record_id`  FROM `vicidial_admin_log` WHERE `event_type` LIKE 'ADD' AND event_section like 'LISTS') b ON a.list_id=record_id WHERE campaign_id = '$CampaignID' AND visible = 1";
                while($row = mysql_fetch_assoc($result))
		{
			if(preg_match("/998/", $row['list_id']))
			{
				$js['db_id_manual'] = $row['list_id'];
				$js['db_name_manual'] = $row['list_name'];
				$query = mysql_fetch_row(mysql_query("SELECT count(*) FROM vicidial_list WHERE list_id='$row[list_id]'")) or die(mysql_error());
				$js['db_leads_manual'] = $query[0];
				$js['db_active_manual'] = $row['active'];
			}
			else
			{
				$js['db_id'][] = $row['list_id'];
				$js['db_name'][] = $row['list_name'];
				$js['db_leads'][] = $row['list_description'];
                                $js['db_create'][] = date("t-m-o",strtotime($row['create_date']));
				$js['db_active'][] = $row['active'];
			}
		}
		echo json_encode($js);
	}


	if($Flag == "DISABLED")
	{
		$result = mysql_query("SELECT list_id, list_name, list_description, event_date create_date, active FROM vicidial_lists a Left Join (SELECT event_date,`record_id`  FROM `vicidial_admin_log` WHERE `event_type` LIKE 'ADD') b ON a.list_id=record_id WHERE campaign_id = '$CampaignID' AND visible = 0") or die(mysql_error());
		while($row = mysql_fetch_assoc($result))
		{
			$js['db_id'][] = $row['list_id'];
			$js['db_name'][] = $row['list_name'];
			$js['db_leads'][] = $row['list_description'];
			$js['db_create'][] = date("t-m-o",strtotime($row['create_date']));
			$js['db_active'][] = $row['active'];
		}
		echo json_encode($js);
	}
	
	
	
	
	
	
	
}

function DBSwitch($DBID, $Active, $link)
{
	mysql_query("UPDATE vicidial_lists SET active = '$Active' WHERE list_id='$DBID'") or die(mysql_error());
	echo "UPDATE vicidial_lists SET active = '$Active' WHERE list_id='$DBID'";
}

function SelectAllDBs($CampaignID, $link)
{
	mysql_query("UPDATE vicidial_lists SET active = 'Y' WHERE campaign_id = '$CampaignID' AND visible = 1") or die(mysql_error());
}

function UnselectAllDBs($CampaignID, $link)
{
	mysql_query("UPDATE vicidial_lists SET active = 'N' WHERE campaign_id = '$CampaignID' AND visible = 1") or die(mysql_error());
}

function DeactivateDB($DBID, $Active, $link)
{
	mysql_query("UPDATE vicidial_lists SET visible = 0, active = 'N' WHERE list_id = '$DBID'") or die(mysql_error());
}

function ActivateDB($DBID, $Active, $link)
{
	mysql_query("UPDATE vicidial_lists SET visible = 1, active = 'N' WHERE list_id = '$DBID'") or die(mysql_error());
}

function DialogDBEditOnOpen($DBID, $AllowedCampaigns, $link)
{
	$result = mysql_fetch_row(mysql_query("SELECT campaign_id FROM vicidial_lists WHERE list_id='$DBID'")) or die(mysql_query());
	
	$campaign = $result[0];
	
	$result = mysql_query("SELECT campaign_name, campaign_id FROM vicidial_campaigns WHERE campaign_id IN ('".implode("','", $AllowedCampaigns)."') ORDER BY campaign_name") or die(mysql_error());
	
	while($row = mysql_fetch_assoc($result))
	{
		if($campaign == $row['campaign_id'])
		{
			$js['selected'][] = "selected='selected'";
		}
		else
		{
			$js['selected'][] = "";
		}
		$js['campaign_id'][] = $row['campaign_id'];
		$js['campaign_name'][] = $row['campaign_name'];
	}
	
	echo json_encode($js);
}

function GetTableEditDB($DBID, $link)
{
	//VARS
	$aColumns = array('status_name', 'count', 'action1');
	$output = array("aaData" => array());
	
    $sQuery = "SELECT B.status_name, count(lead_id) AS count, B.status  FROM vicidial_list A LEFT JOIN (SELECT status, status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status, status_name FROM vicidial_statuses GROUP BY status) B ON A.status=B.status WHERE A.list_id='$DBID' GROUP BY B.status_name";
    $rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            switch($aColumns[$i]) 
            { 
				case "action1": $row[] = "<img class='css-dt-icon editdbs-view-details' feedback-id='$aRow[status]' title='' src='icons/mono_right_expand_16.png'>"; break;
				default: $row[] = $aRow[ $aColumns[$i] ];
            }
        }
		$output['aaData'][] = $row;
    }

echo json_encode( $output );
}

function TableDbsContactDetailsInit($DBID, $editedDBFeed, $link)
{
		//VARS
	$aColumns = array('first_name', 'phone_number', 'total_calls', 'last_call', 'epochdate');
	$output = array("aaData" => array() );
	
	//$CampaignLists = preg_replace("/,/", "/','/", $CampaignLists); 
	
	 
    $sQuery = "	SELECT A.lead_id, B.first_name AS first_name, B.phone_number AS phone_number, B.called_count AS total_calls, DATE_FORMAT(MAX(A.call_date), '%d/%c/%Y %H:%i:%s') AS last_call, UNIX_TIMESTAMP(MAX(A.call_date)) as epochdate FROM vicidial_log A 
				RIGHT JOIN vicidial_list B ON A.lead_id=B.lead_id 
				WHERE B.list_id = '$DBID'
				AND B.status='$editedDBFeed'
				GROUP BY B.lead_id";
    $rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            switch($aColumns[$i]) 
            { 
                case "first_name": if($aRow['first_name'] == null){ $row[] = "<span style='color:grey'><i>Sem registo</i></span>"; } else { $row[] = $aRow['first_name']; } break;
				case "phone_number": $row[] = $aRow['phone_number']; break;
                case "total_calls": $row[] = $aRow['total_calls']; break; 
				case "last_call": if($aRow['last_call'] == null){ $row[] = "<span style='color:grey'><i>Sem registo</i></span>"; } else { $row[] = $aRow['last_call']; } break; 
			//	case "reset": $row[] = "<img class='css-dt-icon recycle-contact-details-single-reset' lead-id='$aRow[lead_id]' title='Realiza um Reset a este Contacto.' src='icons/mono_undo_16.png'>"; break;
			//	case "dnc": $row[] =  "<img class='css-dt-icon recycle-contact-details-single-disable' lead-id='$aRow[lead_id]' title='Retira este contacto da Reciclagem.' src='icons/mono_minus_16.png'>"; break;
				
				default: $row[] = $aRow[ $aColumns[$i] ];
            }
        }
		$output['aaData'][] = $row;
    }

echo json_encode( $output );	

}

function DialogDBEditOnSave($DBID, $changeName, $changeCampaign, $link)
{
	mysql_query(" UPDATE vicidial_lists SET list_name = '$changeName', campaign_id ='$changeCampaign' WHERE list_id='$DBID' ") or die(mysql_error());
}

function NewDB($CampaignID, $DBName, $link)
{

	$result = mysql_fetch_row(mysql_query("SELECT count(*) FROM vicidial_lists WHERE list_id > 999999 AND list_id < 99800001")) or die(mysql_error());
	
	$js['db'] = $result[0] + 1000000;
	$js['name'] = $DBName;
	
	mysql_query("INSERT INTO vicidial_lists (list_id, list_name, list_description, campaign_id, active, visible) VALUES ('$js[db]', '$DBName', 0, '$CampaignID', 'Y', '1')") or die(mysql_error());
	
	echo json_encode($js);
}

function DBMatchFields($CampaignID, $ConvertedFile, $link)
{
	$file = fopen("/tmp/$ConvertedFile", "r");
	$buffer = rtrim(fgets($file, 4096));
	$headers = explode("\t", $buffer);
	
	foreach($headers as $key=>$value)
	{
		$js['headers'][] = $value;
	}
    
	flush();
	fclose($file);	

	$sQuery = mysql_query("SELECT Name, Display_name FROM vicidial_list_ref WHERE campaign_id = '$CampaignID' AND active = '1' ORDER BY field_order",$link);
	
	while ($row = mysql_fetch_row($sQuery))
    {
        $js['name'][] = $row[0];
        $js['display_name'][] = $row[1];
    }
    echo json_encode( $js );
}

function DBWizardMatchFields($DBID, $MatchFields, $ListFields, $ConvertedFile, $link)
{

	
	// REGEX
	$field_regx = "/['\"`\\;]/";
	
	// INIS
	$entry_date = date("Y-m-d H:i:s");
	$last_local_call_time = "2008-01-01 00:00:00";
	$gmt_offset = '0';
	$called_since_last_reset = 'N';
	$file = fopen("/tmp/$ConvertedFile", "r");
	$headers = explode("\t", rtrim(fgets($file, 4096)));
	
	
	foreach($ListFields as $index => $value)
	{
		if($value != "---")
		{
			$mysqlListFields .= strtolower($value).", ";
			$mysqlListFieldsIndex[] = 1;	
		}
		else
		{
			$mysqlListFieldsIndex[] = 0;
		}
	}
	

	while (!feof($file)) 
	{
		
		$buffer = rtrim(fgets($file, 4096));
		if(strlen($buffer) > 0) {
		
		$buffer = stripslashes($buffer);
		$buffer = explode("\t", $buffer);
		
		$mysqlValues = "";
		$ErrorCode = 0;
		$LineCount++;
		foreach($mysqlListFieldsIndex as $index => $value)
		{
			if($value > 0)
			{
				$mysqlValues .= "'".preg_replace($field_regx, "", $buffer[$index])."', ";
				
				if($ListFields[$index] == "PHONE_NUMBER" /*|| $ListFields[$index] == "ALT_PHONE" || $ListFields[$index] == "ADDRESS3"*/)
				{

						 
						 $buffer[$index] = preg_replace("/[^0-9]/", "", $buffer[$index]);
						 
						 if(strlen($buffer[$index]) <> 9)
						 {
							 $ErrorCode = 1;
						 }
						 
						 
						 

						 
				}
			}
		}
		

		
		
		if($ErrorCode <> 0)
		{
			$js['error_line'][] = $LineCount + 1;
			switch($ErrorCode)
			{
				case 1: $js['error_text'][] = "Número de Telefone inválido. Os campos 'Telefone', 'Telefone Alternativo', e 'Telemóvel' apenas podem conter nove números.";
			}
			$TotalErrors++;
		}
		else
		{
			mysql_query("INSERT INTO vicidial_list 
							(".$mysqlListFields."entry_date, called_since_last_reset, gmt_offset_now, last_local_call_time, list_id, status ) 
							VALUES 
							(".$mysqlValues."'$entry_date', '$called_since_last_reset', '$gmt_offset', '$last_local_call_time', '$DBID', 'NEW')") or die(mysql_error());
							
			$js['insert_id'][] = mysql_insert_id();				
							
			$js['insert'][] = "	INSERT INTO vicidial_list 
							(".$mysqlListFields."entry_date, called_since_last_reset, gmt_offset_now, last_local_call_time, list_id, status ) 
							VALUES 
							(".$mysqlValues."'$entry_date', '$called_since_last_reset', '$gmt_offset', '$last_local_call_time', '$DBID', 'NEW')
						";	
						
			$TotalInserted++;						
							
		}
		
		
		}
		
		
	}
	
	mysql_query("UPDATE vicidial_lists SET list_description = (list_description + '$TotalInserted') WHERE list_id='$DBID'") or die(mysql_error());

	$js['totalloaded'] = $LineCount;
	$js['totalinserted'] = $TotalInserted;
	$js['totalerrors'] = $TotalErrors;
	
	echo json_encode( $js );



	
}

function  DBWizardDenyLeads($LeadsToDelete, $DBID, $link)
{
	foreach($LeadsToDelete as $index => $value)
	{
		if($value != null) mysql_query("DELETE FROM vicidial_list WHERE lead_id='$value'") or die(mysql_error());
	}
	mysql_query("UPDATE vicidial_lists SET list_description = list_description - ($index + 1) WHERE list_id = '$DBID'") or die(mysql_query());
}

switch($action)
{
	case "DBListBuilder" : DBListBuilder($CampaignID, $Flag, $link); break;
	case "DBSwitch" : DBSwitch($DBID, $Active, $link); break;
	case "DeactivateDB" : DeactivateDB($DBID, $Active, $link); break;
	case "ActivateDB" : ActivateDB($DBID, $Active, $link); break;
	case "SelectAllDBs" : SelectAllDBs($CampaignID, $link); break;
	case "UnselectAllDBs" : UnselectAllDBs($CampaignID, $link); break;
	case "DialogDBEditOnOpen" : DialogDBEditOnOpen($DBID, $AllowedCampaigns, $link); break;
	case "GetTableEditDB" : GetTableEditDB($DBID, $link); break;
	case "TableDbsContactDetailsInit" : TableDbsContactDetailsInit($DBID, $editedDBFeed, $link); break;
	case "DialogDBEditOnSave" : DialogDBEditOnSave($DBID, $changeName, $changeCampaign, $link); break;
	case "NewDB" : NewDB($CampaignID, $DBName, $link); break;
	case "DBMatchFields" : DBMatchFields($CampaignID, $ConvertedFile, $link); break;
	case "DBWizardMatchFields" : DBWizardMatchFields($DBID, $MatchFields, $ListFields, $ConvertedFile, $link); break;
	case "DBWizardDenyLeads" : DBWizardDenyLeads($LeadsToDelete, $DBID, $link); break;
}

?>