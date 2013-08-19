<?php require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) 
{ 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) 
{
    ${$key} = $value;
}





function DialerOptionsBuilder($CampaignID, $link){
    $result = mysql_query("SELECT inbound_queue_no_dial, available_only_ratio_tally, adaptive_dl_diff_target, dial_method, adaptive_dropped_percentage, adaptive_maximum_level, adaptive_intensity, dial_timeout, campaign_cid FROM vicidial_campaigns WHERE campaign_id = '$CampaignID'") or die(mysql_error());
    while($row = mysql_fetch_assoc($result)){
        $js['inbound_queue'][] = $row['inbound_queue_no_dial'];
        $js['avail_agents'][] = $row['available_only_ratio_tally'];
        $js['difftarget'][] = $row['adaptive_dl_diff_target'];
        $js['dialmethod'][] = $row['dial_method'];
        $js['droppercent'][] = $row['adaptive_dropped_percentage'];
        $js['maxadaptiveratio'][] = $row['adaptive_maximum_level'];
        $js['adaptiveintensity'][] = $row['adaptive_intensity'];
        $js['dialtimeout'][] = $row['dial_timeout'];
        $js['cid'][] = $row['campaign_cid'];
    }
    echo json_encode($js);
}


function PredictiveInboundQueueSwitch($CampaignID, $AllowOutbound, $link)
{
    mysql_query("UPDATE vicidial_campaigns SET inbound_queue_no_dial='$NoOutbound' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error()); 
}


function PredictiveAvailAgentsSwitch($CampaignID, $AvailAgents, $link){
     mysql_query("UPDATE vicidial_campaigns SET available_only_ratio_tally='$AvailAgents' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error()); 
}

function PredictiveDiffTarget($CampaignID, $SliderValue, $link){
    mysql_query("UPDATE vicidial_campaigns SET adaptive_dl_diff_target='$SliderValue' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function PredictiveMethodSwitch($CampaignID, $PredictiveMethod, $link){
    mysql_query("UPDATE vicidial_campaigns SET dial_method='$PredictiveMethod' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function PredictiveDropPercentage($CampaignID, $DropPercentage, $link){
    mysql_query("UPDATE vicidial_campaigns SET adaptive_dropped_percentage='$DropPercentage' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}


function PredictiveMaxRatio($CampaignID, $MaxRatio, $link){
    mysql_query("UPDATE vicidial_campaigns SET adaptive_maximum_level='$MaxRatio' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}


function PredictiveIntensitySwitch($CampaignID, $SliderValue, $link){
    mysql_query("UPDATE vicidial_campaigns SET adaptive_intensity='$SliderValue' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function CampaignTimeout($CampaignID, $Timeout, $link){
    mysql_query("UPDATE vicidial_campaigns SET dial_timeout='$Timeout' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

function CampaignCID($CampaignID, $CID, $link){
    mysql_query("UPDATE vicidial_campaigns SET campaign_cid='$CID' WHERE campaign_id='$CampaignID'", $link) or die(mysql_error());
}

switch($action)
{
    case "DialerOptionsBuilder": DialerOptionsBuilder($CampaignID, $link); break;
    case "PredictiveInboundQueueSwitch": PredictiveInboundQueueSwitch($CampaignID, $NoOutbound, $link); break;
    case "PredictiveAvailAgentsSwitch": PredictiveAvailAgentsSwitch($CampaignID, $AvailAgents, $link); break;
    case "PredictiveDiffTarget": PredictiveDiffTarget($CampaignID, $SliderValue, $link); break;
    case "PredictiveMethodSwitch": PredictiveMethodSwitch($CampaignID, $PredictiveMethod, $link); break;
    case "PredictiveDropPercentage": PredictiveDropPercentage($CampaignID, $DropPercentage, $link); break;
    case "PredictiveMaxRatio": PredictiveMaxRatio($CampaignID, $MaxRatio, $link); break;
    case "PredictiveIntensitySwitch": PredictiveIntensitySwitch($CampaignID, $SliderValue, $link); break;
    case "CampaignTimeout": CampaignTimeout($CampaignID, $Timeout, $link); break; 
    case "CampaignCID": CampaignCID($CampaignID, $CID, $link); break; 
}
















?>