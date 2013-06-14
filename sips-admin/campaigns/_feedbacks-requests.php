<? require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) { 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

function FeedListBuilder($CampaignID, $AllowedCampaigns, $FeedName, $Human, $Callback, $Flag, $link)
{
		if($Flag == "ALL" || $Flag == "REFRESH" || $Flag == "DISABLED" || $Flag == "EDIT")
		{
		if($Flag == "DISABLED"){ $visible = 0; } else { $visible = 1; }
		$query = "SELECT status, status_name, selectable, human_answered, scheduled_callback FROM vicidial_campaign_statuses WHERE campaign_id = '$CampaignID' AND visible = '$visible' GROUP BY status ORDER BY status_name DESC";
		$query = mysql_query($query, $link) or die(mysql_error());
		
		if(mysql_num_rows($query) > 0)
		{
			while($row = mysql_fetch_assoc($query))
			{
				$js['status'][] = $row['status'];
				$js['status_name'][] = $row['status_name'];
				$js['selectable'][] = $row['selectable'];
				$js['human'][] = $row['human_answered'];
				$js['callback'][] = $row['scheduled_callback'];
			}
			echo json_encode($js);	
		}
		else
		{
			echo json_encode($js);
		}
	} 
	else 
	{
		$query = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE TRIM(status_name) COLLATE utf8_bin = '$FeedName' AND campaign_id IN('".implode("','", $AllowedCampaigns)."')", $link);
		if(mysql_num_rows($query) == 0)
		{
			$query = mysql_query("SELECT DISTINCT status FROM vicidial_campaign_statuses", $link);
			$num_status = ((mysql_num_rows($query)) + 1);
			while(strlen($num_status) < 5) 
			{ 
				$num_status = "0".$num_status; 
			}
			
			$new_status = "S".$num_status;
			
			$js['status'][] = $new_status;
			$js['status_name'][] = $FeedName;
			$js['human'][] = $Human;
			$js['callback'][] = $Callback;
			
			
			mysql_query("UPDATE sips_campaign_stats SET feedbacks = feedbacks + 1 WHERE campaign_id='$CampaignID'") or die(mysql_error());
					
			foreach($AllowedCampaigns as $key=>$value)
			{
				if($value = $CampaignID)
				{
					$selectable = "Y";
				}
				else
				{
					$selectable = "N";
				}
					$query = "INSERT INTO vicidial_campaign_statuses (status, status_name, campaign_id, selectable, human_answered, scheduled_callback) VALUES ('$new_status', '$FeedName', '$value', '$selectable' ,'$Human', '$Callback')";
					mysql_query($query, $link) or die(mysql_error());		

			}
		}
		echo json_encode($js);		
	}

	
}

function ActivateAllFeeds($CampaignID, $affectedRows, $link)
{
	mysql_query("UPDATE vicidial_campaign_statuses SET selectable = 'Y' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());

	mysql_query("UPDATE sips_campaign_stats SET feedbacks = $affectedRows WHERE campaign_id='$CampaignID'") or die(mysql_error());
}

function DeactivateAllFeeds($CampaignID, $link)
{
	mysql_query("UPDATE vicidial_campaign_statuses SET selectable = 'N' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
	
	mysql_query("UPDATE sips_campaign_stats SET feedbacks = 0 WHERE campaign_id='$CampaignID'") or die(mysql_error());
}

function FeedActiveInactive($CampaignID, $FeedID, $FeedStatus, $NumFeeds, $link)
{
	mysql_query("UPDATE vicidial_campaign_statuses SET selectable='$FeedStatus' WHERE status='$FeedID' AND campaign_id='$CampaignID'") or die(mysql_error());
	mysql_query("UPDATE sips_campaign_stats SET feedbacks = feedbacks+($NumFeeds) WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function HumanActiveInactive($CampaignID, $FeedID, $FeedStatus, $link)
{
	mysql_query("UPDATE vicidial_campaign_statuses SET human_answered = '$FeedStatus' WHERE status='$FeedID'") or die(mysql_error());
}

function CallbackActiveInactive($CampaignID, $FeedID, $FeedStatus, $link)
{
	mysql_query("UPDATE vicidial_campaign_statuses SET scheduled_callback = '$FeedStatus' WHERE status='$FeedID'") or die(mysql_error());
}

function HideFeed($FeedID, $link)
{
	$query = mysql_query("SELECT campaign_id FROM vicidial_campaign_statuses WHERE status='$FeedID' AND selectable='Y'") or die(mysql_error());
	
	while($row = mysql_fetch_row($query))
	{
		mysql_query("UPDATE sips_campaign_stats SET feedbacks = feedbacks-1 WHERE campaign_id='$row[0]'") or die(mysql_error());
	}
	
	mysql_query("UPDATE vicidial_campaign_statuses SET selectable = 'N', visible = 0 WHERE status = '$FeedID' ") or die(mysql_error());
}

function ShowFeed($FeedID, $link)
{
	mysql_query("UPDATE vicidial_campaign_statuses SET selectable = 'N', visible = 1 WHERE status = '$FeedID' ") or die(mysql_error());
}

function SaveEditedFeed($FeedID, $FeedName, $EditedCampaignID, $EditedCampaignActive, $AlteredFeeds, $link)
{
	foreach($EditedCampaignID as $key=>$value)
	{
		mysql_query(" UPDATE vicidial_campaign_statuses SET status_name = '$FeedName', selectable='$EditedCampaignActive[$key]' WHERE status = '$FeedID' AND campaign_id='$value' ", $link) or die(mysql_error());
		
		mysql_query("UPDATE sips_campaign_stats SET feedbacks = feedbacks+($AlteredFeeds[$value]) WHERE campaign_id='$value'", $link) or die(mysql_error());
	}
}

function GetFeedEditCampaigns($FeedID, $AllowedCampaigns, $link)
{
	$query = mysql_query("SELECT A.campaign_id, A.campaign_name, B.selectable FROM vicidial_campaigns A INNER JOIN vicidial_campaign_statuses B ON A.campaign_id=B.campaign_id WHERE A.campaign_id IN('".implode("','", $AllowedCampaigns)."') AND status='$FeedID' ORDER BY A.campaign_name") or die(mysql_error());

	while($row = mysql_fetch_assoc($query))
	{
		$js['c_id'][] = $row['campaign_id'];
		$js['c_name'][] = $row['campaign_name'];
		$js['selectable'][] = $row['selectable'];
	}
	echo json_encode($js);
	
}

function ApplyFeedsToAllCampaings($CampaignID, $FeedID, $FeedName, $FeedActive, $Callback, $Human, $link)
{
	$counter = 0;
	for($i=0;$i<count($FeedID);$i++)
	{
		$query = "UPDATE vicidial_campaign_statuses SET status_name='$FeedName[$i]', selectable='$FeedActive[$i]', human_answered='$Human[$i]', scheduled_callback='$Callback[$i]' WHERE status ='$FeedID[$i]'";
		mysql_query($query, $link) or die(mysql_error());
		if($FeedActive[$i] == "Y"){ $counter++; }
	}
	mysql_query("UPDATE sips_campaign_stats SET feedbacks = '$counter'", $link) or die(mysql_error());
}

switch($action)
{
	case "FeedListBuilder" : FeedListBuilder($CampaignID, $AllowedCampaigns, $FeedName, $Human, $Callback, $Flag, $link); break; 
	case "ActivateAllFeeds" : ActivateAllFeeds($CampaignID, $affectedRows, $link); break; 
	case "DeactivateAllFeeds" : DeactivateAllFeeds($CampaignID, $link); break;
	case "FeedActiveInactive" : FeedActiveInactive($CampaignID, $FeedID, $FeedStatus, $NumFeeds, $link); break;	 
	case "HumanActiveInactive" : HumanActiveInactive($CampaignID, $FeedID, $FeedStatus, $link); break; 
	case "CallbackActiveInactive" : CallbackActiveInactive($CampaignID, $FeedID, $FeedStatus, $link); break; 
	case "HideFeed" : HideFeed($FeedID, $link); break; 
	case "ShowFeed" : ShowFeed($FeedID, $link); break; 
	case "SaveEditedFeed" : SaveEditedFeed($FeedID, $FeedName, $EditedCampaignID, $EditedCampaignActive, $AlteredFeeds, $link); break; 	
	case "GetFeedEditCampaigns" : GetFeedEditCampaigns($FeedID, $AllowedCampaigns, $link); break; 	
	case "ApplyFeedsToAllCampaings" : ApplyFeedsToAllCampaings($CampaignID, $FeedID, $FeedName, $FeedActive, $Callback, $Human, $link); break; 		
}
?>

