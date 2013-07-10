<? require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) { 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

function PauseListBuilder($CampaignID, $AllowedCampaigns, $PauseName, $PauseTime, $Flag, $link)
{
	if($Flag == "ALL" || $Flag == "REFRESH" || $Flag == "DISABLED" || $Flag == "EDIT")
	{
		if($Flag == "DISABLED"){ $visible = 0; } else { $visible = 1; }
		$query = "SELECT pause_code, pause_code_name, max_time, active FROM vicidial_pause_codes WHERE campaign_id='$CampaignID' AND visible = '$visible' GROUP BY pause_code ORDER BY pause_code_name DESC";
		
		$js['debug'] = $query;
		
		$query = mysql_query($query, $link) or die(mysql_error());
		
		if(mysql_num_rows($query) > 0)
		{
			while($row = mysql_fetch_assoc($query))
			{
				$js['pause_code'][] = $row['pause_code'];
				$js['pause_code_name'][] = $row['pause_code_name'];
				$js['max_time'][] = ($row['max_time'] / 60);
				$js['active'][] = $row['active'];
			}
			echo json_encode($js);	
		}
		else
		{
			if($Flag != "DISABLED")
			{
				$query = "SELECT pause_code, pause_code_name, max_time, active, visible FROM vicidial_pause_codes WHERE campaign_id = '$CampaignID' GROUP BY pause_code ORDER BY pause_code_name DESC";
				$query = mysql_query($query, $link) or die(mysql_error());
				if(mysql_num_rows($query) == 0){ exit(); }
				while($row = mysql_fetch_assoc($query))
				{
					mysql_query("INSERT INTO vicidial_pause_codes (pause_code, pause_code_name, max_time, active, visible, campaign_id) VALUES ('$row[pause_code]', '$row[pause_code_name]', '$row[max_time]', '0', '$row[visible]', '$CampaignID')") or die(mysql_error());
					$js['pause_code'][] = $row['pause_code'];
					$js['pause_code_name'][] = $row['pause_code_name'];
					$js['max_time'][] = ($row['max_time'] / 60);
					$js['active'][] = $row['active'];
				}
				echo json_encode($js);
			}
			
		}
	} 
	else 
	{
		$query = mysql_query("SELECT pause_code_name FROM vicidial_pause_codes WHERE TRIM(pause_code_name) COLLATE utf8_bin = '$PauseName' AND campaign_id = '$CampaignID'", $link);
		if(mysql_num_rows($query) == 0)
		{
			$query = mysql_query("SELECT DISTINCT pause_code FROM vicidial_pause_codes", $link);
			$num_pauses = ((mysql_num_rows($query)) + 1);
			while(strlen($num_pauses) < 5) 
			{ 
				$num_pauses = "0".$num_pauses; 
			}
			
			$new_pause_code = "P".$num_pauses;
			
			$js['pause_code'][] = $new_pause_code;
			$js['pause_code_name'][] = $PauseName;
			$js['max_time'][] = $PauseTime;
			
			
					
			$PauseTime = $PauseTime*60;
			
			mysql_query("UPDATE sips_campaign_stats SET pauses = pauses + 1 WHERE campaign_id='$CampaignID'") or die(mysql_error());
			
			foreach($AllowedCampaigns as $key=>$value)
			{
				if($value == $CampaignID)
				{
					$active = "Y";
				}
				else
				{
					$active  = "N";
				}
				$query = "INSERT INTO vicidial_pause_codes (pause_code, pause_code_name, campaign_id, max_time, active ) VALUES ('$new_pause_code', '$PauseName', '$value', '$PauseTime', '$active')";
				mysql_query($query, $link) or die(mysql_error());
			}
		}
		echo json_encode($js);
	}
}

function PauseActiveInactive($CampaignID, $PauseID, $PauseStatus, $NumPauses, $link)
{
	mysql_query("UPDATE vicidial_pause_codes SET active='$PauseStatus' WHERE pause_code ='$PauseID' AND campaign_id='$CampaignID'") or die(mysql_error());
	mysql_query("UPDATE sips_campaign_stats SET pauses = pauses+('$NumPauses') WHERE campaign_id='$CampaignID'") or die(mysql_error());
}

function HidePause($CampaignID, $PauseID, $link)
{
	$query = mysql_query("SELECT campaign_id FROM vicidial_pause_codes WHERE pause_code='$PauseID' AND active=1") or die(mysql_error());
	
	while($row = mysql_fetch_row($query))
	{
		mysql_query("UPDATE sips_campaign_stats SET pauses = pauses-1 WHERE campaign_id='$row[0]'") or die(mysql_error());
	}
	
	mysql_query("UPDATE vicidial_pause_codes SET active = 0, visible = 0 WHERE pause_code = '$PauseID' ") or die(mysql_error());
}

function SelectAllPauses($CampaignID, $TotalPauses, $link)
{
	mysql_query("UPDATE vicidial_pause_codes SET active='1' WHERE campaign_id='$CampaignID' AND visible = 1", $link) or die(mysql_error());
	mysql_query("UPDATE sips_campaign_stats SET pauses='$TotalPauses' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function DeactivateAllPauses($CampaignID, $link)
{
	mysql_query("UPDATE vicidial_pause_codes SET active='0' WHERE campaign_id='$CampaignID' AND visible = 1", $link) or die(mysql_error());
	mysql_query("UPDATE sips_campaign_stats SET pauses='0' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function ReactivatePause($PauseID, $link)
{
	mysql_query("UPDATE vicidial_pause_codes SET active = 0, visible = 1 WHERE pause_code = '$PauseID' ") or die(mysql_error());
}

function GetPauseEditCampaigns($AllowedCampaigns, $PauseID, $link)
{
    if(in_array("ALL-CAMPAIGNS-", $AllowedCampaigns)){
  $query = mysql_query("SELECT A.campaign_id, A.campaign_name, B.active FROM vicidial_campaigns A INNER JOIN vicidial_pause_codes B ON A.campaign_id=B.campaign_id WHERE pause_code='$PauseID' GROUP BY A.campaign_id ORDER BY A.campaign_name");
	}else{
 $query = mysql_query("SELECT A.campaign_id, A.campaign_name, B.active FROM vicidial_campaigns A INNER JOIN vicidial_pause_codes B ON A.campaign_id=B.campaign_id WHERE A.campaign_id IN('".implode("','", $AllowedCampaigns)."') AND pause_code='$PauseID' GROUP BY A.campaign_id ORDER BY A.campaign_name");
   }
	while($row = mysql_fetch_assoc($query))
	{
		$js['c_id'][] = $row['campaign_id'];
		$js['c_name'][] = $row['campaign_name'];
		$js['active'][] = $row['active'];
	}
        $js['query']=$query;
	echo json_encode($js);
	
}

function SaveEditedPause($PauseID, $PauseName, $PauseTime, $EditedCampaignsID, $EditedCampaignsActive, $AlteredPauses, $link)
{
	$PauseTime = $PauseTime*60;
	foreach($EditedCampaignsID as $key=>$value)
	{
		mysql_query("UPDATE vicidial_pause_codes SET pause_code_name = '$PauseName' , max_time='$PauseTime', active='$EditedCampaignsActive[$key]' WHERE pause_code = '$PauseID' AND campaign_id='$value' ", $link) or die(mysql_error());
		
		mysql_query("UPDATE sips_campaign_stats SET pauses = pauses+($AlteredPauses[$value]) WHERE campaign_id='$value'", $link) or die(mysql_error());
	}
}

function ApplyPausesToAllCampaings($PauseIDs, $PauseNames, $PauseStatus, $PauseTimes, $link)
{
	for($i=0;$i<count($PauseIDs);$i++)
	{
		$PauseTimes[$i] = ($PauseTimes[$i]*60);
		$query = "UPDATE vicidial_pause_codes SET pause_code_name='$PauseNames[$i]', active='$PauseStatus[$i]', max_time='$PauseTimes[$i]' WHERE pause_code ='$PauseIDs[$i]'";
		mysql_query($query, $link);
		$counter += $PauseStatus[$i];
	}
	mysql_query("UPDATE sips_campaign_stats SET pauses = '$counter'", $link) or die(mysql_error());

}

switch($action)
{
	case "PauseListBuilder": PauseListBuilder($CampaignID, $AllowedCampaigns, $PauseName, $PauseTime, $Flag, $link); break;
	case "PauseActiveInactive": PauseActiveInactive($CampaignID, $PauseID, $PauseStatus, $NumPauses, $link); break;
	case "HidePause": HidePause($CampaignID, $PauseID, $link); break;
	case "SelectAllPauses": SelectAllPauses($CampaignID, $TotalPauses, $link); break;
	case "DeactivateAllPauses": DeactivateAllPauses($CampaignID, $link); break;
	case "ReactivatePause": ReactivatePause($PauseID, $link); break;
	case "GetPauseEditCampaigns": GetPauseEditCampaigns($AllowedCampaigns, $PauseID, $link); break;
	case "SaveEditedPause": SaveEditedPause($PauseID, $PauseName, $PauseTime, $EditedCampaignsID, $EditedCampaignsActive, $AlteredPauses, $link); break;
	case "ApplyPausesToAllCampaings": ApplyPausesToAllCampaings($PauseIDs, $PauseNames, $PauseStatus, $PauseTimes, $link); break;
}








































?>