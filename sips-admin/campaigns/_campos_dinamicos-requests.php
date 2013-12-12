<?php require("../../ini/dbconnect.php");
require("../../ini/user.php");

foreach ($_POST as $key => $value) { 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users();

function FieldsListBuilder($CampaignID, $AllFields, $FieldID, $FieldDisplayName, $FieldReadOnly, $Flag, $link)
{
	
	if($Flag == "ALL" || $Flag == "DIALOG")
	{
		$query = mysql_query("SELECT Name, Display_name, readonly, active, field_order FROM vicidial_list_ref WHERE campaign_id='$CampaignID' ORDER BY field_order DESC") or die(mysql_error());
		if(mysql_num_rows($query))
		{
			
			
			while($row = mysql_fetch_assoc($query))
			{
				if($row['active'])
				{
					$js['name'][] =  $row['Name'];
					$js['displayname'][] = $row['Display_name'];
					$js['readonly'][] = $row['readonly'];
					$js['order'][] = $row['field_order'];
				
				}
			}
		}
		else
		{
			foreach($AllFields as $index => $value)
			{
				switch($value)
				{
					case "FIRST_NAME": $active = 1; $displayname = "Nome"; $order = 1; break;
					case "PHONE_NUMBER": $active = 1; $displayname = "Telefone"; $order = 2;  break;
					case "ALT_PHONE": $active = 1; $displayname = "Telefone Alternativo"; $order = 3; break;
					case "ADDRESS3": $active = 1; $displayname = "Telemóvel"; $order = 4; break;
					case "ADDRESS1": $active = 1; $displayname = "Morada"; $order = 5; break;
					case "POSTAL_CODE": $active = 1; $displayname = "Código Postal"; $order = 6; break;
					case "EMAIL": $active = 1; $displayname = "E-mail"; $order = 7; break;
					case "COMMENTS": $active = 1; $displayname = "Comentários"; $order = 8; break;
					default : $active = 0; $displayname = $value; $order = 0;
				}
				
				if($active)
				{
					$js['name'][] = $value;
					$js['displayname'][] = $displayname;
					$js['readonly'][] = 0;
					$js['order'][] = $order;
				}
				
				mysql_query("INSERT INTO vicidial_list_ref (Name, Display_name, readonly, active, campaign_id, field_order) VALUES ('$value', '$displayname', '0', '$active', '$CampaignID', '$order')");
				mysql_query("UPDATE sips_campaign_stats SET dynamic_fields = 8 WHERE campaign_id='$CampaignID'") or die(mysql_error());
			}	
		}

	}
	else
	{
		//$js["debug"] = "SELECT Display_name FROM vicidial_list_ref WHERE Display_name COLLATE utf8_bin = '$FieldDisplayName' AND campaign_id = '$CampaignID'";
		
		$query = mysql_query("SELECT Display_name FROM vicidial_list_ref WHERE Display_name COLLATE utf8_bin = '$FieldDisplayName' AND campaign_id = '$CampaignID'", $link) or die(mysql_error());
		if(mysql_num_rows($query) == 0)
		{
			mysql_query("UPDATE vicidial_list_ref SET field_order = field_order+1 WHERE campaign_id='$CampaignID' AND field_order > 0") or die(mysql_error());
			mysql_query("UPDATE vicidial_list_ref SET Display_name='$FieldDisplayName', readonly='$FieldReadOnly', active=1, campaign_id='$CampaignID', field_order=1 WHERE campaign_ID='$CampaignID' AND Name='$FieldID'") or die(mysql_error());
			mysql_query("UPDATE sips_campaign_stats SET dynamic_fields = dynamic_fields + 1 WHERE campaign_id='$CampaignID'") or die(mysql_query());
			$js['name'][] = $FieldID;
			$js['displayname'][] = $FieldDisplayName;
			$js['readonly'][] = $FieldReadOnly;
			$js['order'][] = 1;
				
		}
		else 
		{
			$js['duplicate'] = "error";
		}
	}
echo json_encode($js);  


}

function FieldsReadOnlySwitch($CampaignID, $FieldID, $ReadOnly, $link)
{
	mysql_query("UPDATE vicidial_list_ref SET readonly='$ReadOnly' WHERE campaign_id='$CampaignID' AND Name='$FieldID'") or die(mysql_error());
}

function ReOrderFields($CampaignID, $SortedFields, $link)
{
	foreach($SortedFields as $index => $value)
	{
		mysql_query("UPDATE vicidial_list_ref SET field_order='".($index+1)."' WHERE Name='$value' AND campaign_id='$CampaignID'") or die(mysql_error());
	}
}

function RemoveField($CampaignID, $FieldID, $link)
{
	$result = mysql_fetch_row(mysql_query("SELECT field_order FROM vicidial_list_ref WHERE campaign_id='$CampaignID' AND Name ='$FieldID'")) or die(mysql_error());
	mysql_query("UPDATE vicidial_list_ref SET field_order = field_order - 1 WHERE field_order > $result[0] AND campaign_id='$CampaignID'") or die(mysql_error());
	mysql_query("UPDATE vicidial_list_ref SET Display_name = Name, readonly=0, active=0, field_order=0 WHERE campaign_id='$CampaignID' AND Name='$FieldID'") or die(mysql_error());
	mysql_query("UPDATE sips_campaign_stats SET dynamic_fields = dynamic_fields - 1 WHERE campaign_id='$CampaignID'") or die(mysql_query());
    
    
        $query = "Insert into vicidial_admin_log(`admin_log_id`, `event_date`, `user`, `ip_address`, `event_section`, `event_type`, `record_id`, `event_code`, `event_sql`)"
                . "values(NULL,'" . date("Y-m-d H:i:s") . "','" . $user->id . "','" . $user->ip . "','DYNAMIC_FIELD','DELETE','$CampaignID','ADMIN DELETE DYNAMIC FIELD','several queries->campaigns/_campos_dinamicos-request->function RemoveField')";
        mysql_query($query) or die(mysql_error());
    
}

function DialogFieldsEditOnSave($CampaignID, $FieldID, $FieldName, $link)
{
	$js["debug"] = "SELECT Display_name FROM vicidial_list_ref WHERE Display_name COLLATE utf8_bin = '$FieldName' AND campaign_id = '$CampaignID'";
	
	$query = mysql_query("SELECT Display_name FROM vicidial_list_ref WHERE Display_name COLLATE utf8_bin = '$FieldName' AND campaign_id = '$CampaignID'", $link) or die(mysql_error());
	if(mysql_num_rows($query)==0)
	{
		mysql_query("UPDATE vicidial_list_ref SET Display_name = '$FieldName' WHERE campaign_id='$CampaignID' AND Name = '$FieldID'") or die(mysql_error());
		$js['flag'] = true;
	}
	else
	{
		$js['flag'] = false;
	}
echo json_encode($js);
}

function DialogFieldsApplyToAllCampaignsOnSave($CampaignID, $AllowedCampaigns, $link)
{
	$result = mysql_query("SELECT Name, Display_name, readonly, active, field_order FROM vicidial_list_ref WHERE campaign_id='$CampaignID'") or die(mysql_error());
	
	$count = 0;
	while($row = mysql_fetch_assoc($result))
	{
		if($row['active'] == 1){$count++;}
		mysql_query("UPDATE vicidial_list_ref SET Display_name = '$row[Display_name]', readonly = '$row[readonly]', active = '$row[active]', field_order = '$row[field_order]' WHERE campaign_id IN ('".implode("','", $AllowedCampaigns)."') AND Name = '$row[Name]'") or die(mysql_error());
	}
	mysql_query("UPDATE sips_campaign_stats SET dynamic_fields = $count") or die(mysql_query());
}

function DialogFieldsCopyOnOpen($CampaignID, $ModAllowedCampaigns, $link)
{
	if(in_array("ALL-CAMPAIGNS-", $ModAllowedCampaigns)){
  $query = "SELECT campaign_id, campaign_name FROM vicidial_campaigns  ORDER BY campaign_name";
	}else{
 $query = "SELECT campaign_id, campaign_name FROM vicidial_campaigns WHERE campaign_id IN ('".implode("','", $ModAllowedCampaigns)."') ORDER BY campaign_name";
   }
   $result = mysql_query($query) or die(mysql_error());
	
	while($row = mysql_fetch_assoc($result))
	{
		$js['c_id'][] = $row['campaign_id'];
		$js['c_name'][] = $row['campaign_name'];
	}
	echo json_encode($js);
}

function BtnCopyFields($CampaignID, $CopyCampaignID, $link)
{
	$result = mysql_query("SELECT Name, Display_name, readonly, active, field_order FROM vicidial_list_ref WHERE campaign_id='$CopyCampaignID'") or die(mysql_error());
	$count = 0;
	while($row = mysql_fetch_assoc($result))
	{
		if($row['active'] == 1) $count++;
		mysql_query("UPDATE vicidial_list_ref SET Display_name = '$row[Display_name]', readonly = '$row[readonly]', active = '$row[active]', field_order = '$row[field_order]' WHERE campaign_id = '$CampaignID' AND Name = '$row[Name]'") or die(mysql_error());
	}
	mysql_query("UPDATE sips_campaign_stats SET dynamic_fields = $count WHERE campaign_id='$CampaignID'") or die(mysql_query());
}


switch($action)
{
	case "FieldsListBuilder": FieldsListBuilder($CampaignID, $AllFields, $FieldID, $FieldDisplayName, $FieldReadOnly, $Flag, $link); break;
	case "FieldsReadOnlySwitch": FieldsReadOnlySwitch($CampaignID, $FieldID, $ReadOnly, $link); break;
	case "ReOrderFields": ReOrderFields($CampaignID, $SortedFields, $link); break;
	case "RemoveField": RemoveField($CampaignID, $FieldID, $link); break;
	case "DialogFieldsEditOnSave": DialogFieldsEditOnSave($CampaignID, $FieldID, $FieldName, $link); break;
	case "DialogFieldsApplyToAllCampaignsOnSave": DialogFieldsApplyToAllCampaignsOnSave($CampaignID, $AllowedCampaigns, $link); break;
	case "DialogFieldsCopyOnOpen": DialogFieldsCopyOnOpen($CampaignID, $ModAllowedCampaigns, $link); break;
	case "BtnCopyFields": BtnCopyFields($CampaignID, $CopyCampaignID, $link); break;	
}





































