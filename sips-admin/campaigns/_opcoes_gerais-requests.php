<?php require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) 
{ 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) 
{
    ${$key} = $value;
}

function MiscOptionsBuilder($User, $UserGroup, $AllowedCampaigns, $CampaignID, $Flag, $link)
{
	$query = "SELECT user_group, group_name FROM vicidial_user_groups";
	$query = mysql_query($query, $link) or die(mysql_error());
	while($row = mysql_fetch_row($query))
	{
		$js['user_groups_id'][] = $row[0];
		$js['user_groups_name'][] = $row[1];
	}

	if($Flag == "NEW")
	{
		$query = mysql_fetch_row(mysql_query("SELECT count(*) FROM vicidial_campaigns WHERE campaign_id LIKE 'W%'", $link)) or die(mysql_error());
		$campaign_number = ($query[0]+1);
		while(strlen($campaign_number) < 5) { $campaign_number = "0".$campaign_number; }
		$js['new_campaign_id'] = "W".$campaign_number;
		$js['new_campaign_name'] = "Campanha $campaign_number";
		
		$query = "
			INSERT INTO vicidial_campaigns 
			(
			campaign_id,
			campaign_name,
			campaign_description,
			active,
			lead_order,
			allow_closers,
			hopper_level,
			auto_dial_level,
			next_agent_call,
			local_call_time,
			dial_timeout,
			dial_prefix,
			allcalls_delay,
			campaign_recording,
			campaign_rec_filename,
			scheduled_callbacks,
			drop_call_seconds,
			drop_action,
			dial_method,
			adaptive_dropped_percentage,
			no_hopper_leads_logins,
			scheduled_callbacks_count,
			scheduled_callbacks_alert,
			dial_statuses,
			agent_pause_codes_active,
			omit_phone_code,
			auto_alt_dial,
			available_only_ratio_tally,
			manual_dial_list_id,
			status_display_fields,
			use_auto_hopper,
			auto_trim_hopper
			)
			VALUES      (
			'$js[new_campaign_id]',
			'$js[new_campaign_name]',
			'',
			'Y',
			'RANDOM',
			'Y',
			'50',
			'2',
			'longest_wait_time',
			'24hours',
			'35',
			'X',
			'0',
			'ALLFORCE',
			'CAMPAIGN_AGENT_CUSTPHONE_FULLDATE',
			'Y',
			'0',
			'HANGUP',
			'RATIO',
			'2',
			'Y',
			'LIVE',
			'BLINK_RED',
			' DC PU PDROP ERI NA DROP B NEW -',
			'FORCE',
			'Y',
			'ALT_AND_ADDR3',
			'Y',
			'998$campaign_number', 
			'NONE',
			'Y',
			'Y'); ";
		mysql_query($query, $link) or die(mysql_error());
	
	
		$query = "
			INSERT INTO vicidial_campaign_stats
			(campaign_id)
			VALUES      
			('$js[new_campaign_id]');";
		mysql_query($query,$link) or die(mysql_error());
		
	
		$query = "INSERT INTO vicidial_lists (list_id, list_name, campaign_id, active) VALUES (998$campaign_number, 'Chamadas Manuais - $js[new_campaign_id]', '$js[new_campaign_id]', 'N')";
		mysql_query($query,$link) or die(mysql_error());
	
	
		$query = "UPDATE vicidial_user_groups SET allowed_campaigns = CONCAT(' $js[new_campaign_id]', allowed_campaigns) WHERE user_group = '$UserGroup' ";
		mysql_query($query, $link) or die(mysql_error());
			
		
		
		$now = date("Y-m-d H:i:s");
		
		mysql_query("INSERT INTO sips_campaign_stats (campaign_id, creation_date, recycle) VALUES ('$js[new_campaign_id]', '$now', 7)", $link) or die(mysql_error());	
		
		
		//$query = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$user_group[user_group]';", $link)) or die(mysql_error());
		//$js['allowed'] = "'" . preg_replace("/ /","','" , preg_replace("/ -/",'',trim($query['allowed_campaigns']))) . "'";
		
		
		// Create Default Recycle
		$query = mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$js[new_campaign_id]', 'ERI', '300', '10', 'Y')") or die(mysql_error());	
		$query = mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$js[new_campaign_id]', 'PDROP', '300', '10', 'Y')") or die(mysql_error());
		$query = mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$js[new_campaign_id]', 'PU', '300', '10', 'Y')") or die(mysql_error());
		$query = mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$js[new_campaign_id]', 'DC', '300', '10', 'Y')") or die(mysql_error());
		
		$query = mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$js[new_campaign_id]', 'NA', '3600', '10', 'Y')") or die(mysql_error());
		$query = mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$js[new_campaign_id]', 'B', '1800', '10', 'Y')") or die(mysql_error());				
		$query = mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$js[new_campaign_id]', 'DROP', '180', '10', 'Y')") or die(mysql_error());
		
		// Get all pauses from other campaigns and create them for the new campaign
		$query = mysql_query("SELECT pause_code, pause_code_name, max_time, visible FROM vicidial_pause_codes WHERE campaign_id IN('".implode("','", $AllowedCampaigns)."') GROUP BY pause_code", $link) or die(mysql_error());
		while($row = mysql_fetch_row($query))
		{
			mysql_query("INSERT INTO vicidial_pause_codes (pause_code, pause_code_name, campaign_id, max_time, active, visible ) VALUES ('$row[0]', '$row[1]', '$js[new_campaign_id]', '$row[2]', 'N', '$row[3]')") or die(mysql_error());
		}
	
		// Get all feeds from other campaigns and create them for the new campaign
		$query = mysql_query("SELECT status, status_name, human_answered, scheduled_callback, visible FROM vicidial_campaign_statuses WHERE campaign_id IN('".implode("','", $AllowedCampaigns)."') GROUP BY status", $link) or die(mysql_error());
		while($row = mysql_fetch_row($query))
		{
			mysql_query("INSERT INTO vicidial_campaign_statuses (status, status_name, selectable, human_answered, scheduled_callback, campaign_id, visible) VALUES ('$row[0]', '$row[1]', 'N', '$row[2]', '$row[3]', '$js[new_campaign_id]', '$row[4]')") or die(mysql_error());
		}
		
			
	}
	else
	{
		$query = mysql_query("SELECT campaign_name, campaign_description, active, dial_method, auto_dial_level, campaign_recording, lead_order, next_agent_call, my_callback_option FROM vicidial_campaigns WHERE campaign_id='$CampaignID' LIMIT 1", $link) or die(mysql_error());
		$result = mysql_fetch_assoc($query) or die(mysql_error());
		
		$js['c_name'] = $result['campaign_name'];
		$js['c_description'] = $result['campaign_description'];
		$js['c_active'] = $result['active'];
		$js['c_dial_method'] = $result['dial_method'];
		$js['c_auto_dial_level'] = $result['auto_dial_level'];
		$js['c_recording'] = $result['campaign_recording'];
		$js['c_lead_order'] = $result['lead_order'];
		$js['c_next_agent_call'] = $result['next_agent_call'];
                                            $js['c_my_callback_option'] = $result['my_callback_option'];
		
		$query = mysql_query("SELECT user_group FROM vicidial_user_groups WHERE allowed_campaigns LIKE '%$CampaignID%'") or die(mysql_error());
		while($result = mysql_fetch_row($query))
		{
			$js['selected_user_groups'][] = $result[0];
		}
	
		//$query = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$user_group[user_group]';", $link)) or die(mysql_error());
		//$js['allowed'] = "'" . preg_replace("/ /","','" , preg_replace("/ -/",'',trim($query['allowed_campaigns']))) . "'";	
		
		$query = mysql_query("SELECT list_id FROM vicidial_lists WHERE campaign_id='$CampaignID'") or die(mysql_error());
		
		while($row = mysql_fetch_row($query))
		{
			$js['campaign_lists'][] = $row[0];
		}
	}
	echo json_encode($js);
}

function EditCampaignRatio($CampaignID, $Ratio, $link)
{
	mysql_query("UPDATE vicidial_campaigns SET auto_dial_level='$Ratio' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function EditCallAtrib($CampaignID, $Value, $link)
{
	switch($Value)
	{
		case 1: { $campaign_next_call = "longest_wait_time"; break; }
		case 2: { $campaign_next_call = "random"; break; }
		case 3: { $campaign_next_call = "fewest_calls"; break; }
	}
	mysql_query("UPDATE vicidial_campaigns SET next_agent_call='$campaign_next_call' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function GetCampaignDialStatuses($CampaignID, $link)
{
	$query = mysql_query("SELECT dial_statuses FROM vicidial_campaigns WHERE campaign_id='$CampaignID'", $link);
	$row = mysql_fetch_row($query);
	$js['result'] = trim($row[0]);
	$row[0] = preg_replace("/ -/", "", trim($row[0])); 
	$dial_status = explode(" ", $row[0]);
	$query = mysql_query("SELECT status, TRIM(status_name) FROM vicidial_statuses WHERE status NOT IN('QUEUE', 'INCALL', 'CALLBK', 'CBHOLD', 'NAOEX', 'DNC') ORDER BY TRIM(status_name)", $link);
	while($row = mysql_fetch_row($query))
	{ 
		$statuses[] = $row[0];
		$statuses_names[] = $row[1];
	}
	$query = mysql_query("SELECT status, TRIM(status_name) FROM vicidial_campaign_statuses WHERE campaign_id = '$CampaignID' AND scheduled_callback = 'N' GROUP BY status ORDER BY TRIM(status_name)", $link);

	while($row = mysql_fetch_row($query))
	{
		$statuses[] = $row[0];
		$statuses_names[] = $row[1];
	}


	$check_recycle = implode("','", $statuses);

	
	$query = mysql_query("SELECT status FROM vicidial_lead_recycle WHERE status IN ('$check_recycle') AND campaign_id='$CampaignID' AND active='Y'") or die(mysql_error());
	while($row = mysql_fetch_assoc($query))
	{
		$on_recycle[] = $row['status'];
	}

	

	foreach($statuses as $key=>$value)
	{
		foreach ($dial_status as $key1=>$value1)
		{
			if($value == $value1)
			{
				$selected = 1; break;
			}
			else
			{
				$selected = 0;
			}
		}
		
		foreach ($on_recycle as $key2 => $value2)
		{
			if($value2 == $value)
			{
				$recycle = 1; break;
			}
			else
			{
				$recycle = 0;
			}
		}
		
		$js['status'][] = trim($value);
		$js['status_name'][] = trim($statuses_names[$key]);
		$js['selected'][] = $selected;
		$js['recycle'][] = $recycle;
	}
	echo json_encode($js);

	
}

function SaveCampaignDialStatus($CampaignID, $EditedDialStatus, $link)
{
	foreach($EditedDialStatus as $key=>$value)
	{
		$new_dial_status .= " ".$value;
	}
	$new_dial_status = $new_dial_status." -";
	mysql_query("UPDATE vicidial_campaigns SET dial_statuses = '$new_dial_status' WHERE campaign_id= '$CampaingID'", $link) or die(mysql_error());	

}

function EditCampaignAllowedGroups($CampaignID, $EditedUserGroup, $AddOrRemove, $link)
{
	if($AddOrRemove)
	{
		mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns = CONCAT(' $CampaignID', allowed_campaigns) WHERE user_group = '$EditedUserGroup'", $link) or die(mysql_error());	
	}
	else
	{
		mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns = REPLACE(allowed_campaigns, ' $CampaignID', '') WHERE user_group = '$EditedUserGroup'", $link) or die(mysql_error());
	}

}

function EditCampaignAllowedGroupsALL($CampaignID, $AllGroups, $link)
{
	foreach($AllGroups as $key=>$value)
	{
		mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns = CONCAT(' $CampaignID', allowed_campaigns) WHERE user_group = '$value'", $link) or die(mysql_error());
	}
}

function EditCampaignAllowedGroupsNONE($CampaignID, $NoGroups, $link)
{
	foreach($NoGroups as $key=>$value)
	{
		mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns = REPLACE(allowed_campaigns, ' $CampaignID', '') WHERE user_group = '$value'", $link) or die(mysql_error());
	}
}

function EditCampaignActive($CampaignID, $CampaignActive, $link)
{
	mysql_query("UPDATE vicidial_campaigns SET active='$CampaignActive' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());	
}

function EditCampaignType($CampaignID, $CampaignType, $TempRatio, $link)
{
	mysql_query("UPDATE vicidial_campaigns SET auto_dial_level='$TempRatio', dial_method='$CampaignType' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());	
}

function EditCampaignRecording($CampaignID, $CampaignRecording, $link)
{
	mysql_query("UPDATE vicidial_campaigns SET campaign_recording='$CampaignRecording' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function EditLeadOrder($CampaignID, $LeadOrder, $link)
{
	mysql_query("UPDATE vicidial_campaigns SET lead_order='$LeadOrder' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function EditCampaignName($CampaignID, $CampaignName, $link)
{
	mysql_query("UPDATE vicidial_campaigns SET campaign_name='$CampaignName' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function EditCampaignDescription($CampaignID, $CampaignDescription, $link)
{
	mysql_query("UPDATE vicidial_campaigns SET campaign_description='$CampaignDescription' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function CampaignCallbackType($CampaignID, $Type, $link){
    mysql_query("UPDATE vicidial_campaigns SET my_callback_option = '$Type' WHERE campaign_id = '$CampaignID'", $link) or die(mysql_error());
}

switch($action)
{
    case "MiscOptionsBuilder": MiscOptionsBuilder($User, $UserGroup, $AllowedCampaigns, $CampaignID, $Flag, $link); break;
    case "EditCampaignRatio": EditCampaignRatio($CampaignID, $Ratio, $link); break;
    case "EditCallAtrib": EditCallAtrib($CampaignID, $Value, $link); break;
    case "GetCampaignDialStatuses": GetCampaignDialStatuses($CampaignID, $link); break;
    case "SaveCampaignDialStatus": SaveCampaignDialStatus($CampaignID, $EditedDialStatus, $link); break;
    case "EditCampaignAllowedGroups": EditCampaignAllowedGroups($CampaignID, $EditedUserGroup, $AddOrRemove, $link); break;	
    case "EditCampaignAllowedGroupsALL": EditCampaignAllowedGroupsALL($CampaignID, $AllGroups, $link); break;	
    case "EditCampaignAllowedGroupsNONE": EditCampaignAllowedGroupsNONE($CampaignID, $NoGroups, $link); break;	
    case "EditCampaignActive": EditCampaignActive($CampaignID, $CampaignActive, $link); break;	
    case "EditCampaignType": EditCampaignType($CampaignID, $CampaignType, $TempRatio, $link); break;	
    case "EditCampaignRecording": EditCampaignRecording($CampaignID, $CampaignRecording, $link); break;	
    case "EditLeadOrder": EditLeadOrder($CampaignID, $LeadOrder, $link); break;	
    case "EditCampaignName": EditCampaignName($CampaignID, $CampaignName, $link); break;
    case "EditCampaignDescription": EditCampaignDescription($CampaignID, $CampaignDescription, $link); break;
    case "CampaignCallbackType": CampaignCallbackType($CampaignID, $Type, $link); break;
}

?>