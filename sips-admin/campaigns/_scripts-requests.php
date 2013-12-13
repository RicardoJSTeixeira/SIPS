<?php require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) { 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

function ScriptListBuilder($CampaignID, $AllowedCampaigns, $link)
{
    if(in_array("ALL-CAMPAIGNS-", $AllowedCampaigns)){
   	$query = "SELECT A.campaign_id, B.campaign_name, count(*) AS num_fields FROM vicidial_lists_fields A INNER JOIN vicidial_campaigns B ON A.campaign_id=B.campaign_id GROUP BY A.campaign_id ORDER BY B.campaign_name"; // WHERE campaign_id IN ('".implode("','" , $AllowedCampaigns)."')
    }else{
     $query = "SELECT A.campaign_id, B.campaign_name, count(*) AS num_fields FROM vicidial_lists_fields A INNER JOIN vicidial_campaigns B ON A.campaign_id=B.campaign_id WHERE A.campaign_id IN ('".implode("','" , $AllowedCampaigns)."') GROUP BY A.campaign_id ORDER BY B.campaign_name";
    }
    $query = mysql_query($query) or die(mysql_error());
	
	while($row = mysql_fetch_assoc($query))
	{
		$js['campaign_id'][] = $row['campaign_id'];
		$js['campaign_name'][] = $row['campaign_name'];
		$js['num_fields'][] = $row['num_fields'];
	}
	
	$result = mysql_fetch_row(mysql_query("SELECT count(*) FROM vicidial_lists_fields WHERE campaign_id='$CampaignID'")) or die(mysql_error());

	$js['current_script_fields'] = $result[0];
	
	
	
	
	
	echo json_encode($js);
}

function SubmitCopyScript($CampaignID, $ScriptToCopy, $link)
{
    global $VARDB_server;
    global $VARDB_port;

	$ScriptToCopy = strtoupper($ScriptToCopy);

    $connection = mysql_connect("$VARDB_server:$VARDB_port", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk", $connection);
    
    mysql_query("DROP TABLE IF EXISTS custom_$CampaignID") or die (mysql_error());
    mysql_query("CREATE TABLE IF NOT EXISTS custom_$CampaignID LIKE custom_$ScriptToCopy") or die (mysql_error());
    
    mysql_query("DELETE FROM vicidial_lists_fields WHERE campaign_id='$CampaignID'") or die(mysql_error());
    mysql_query("INSERT INTO vicidial_lists_fields 
                        (SELECT '', `list_id`, `field_label`, `field_name`, `field_description`, `field_rank`, `field_help`, `field_type`, `field_options`, `field_size`, `field_max`, `field_default`, `field_cost`, `field_required`, `name_position`, `multi_position`, `field_order`, '$CampaignID', `action` 
                        FROM vicidial_lists_fields WHERE campaign_id='$ScriptToCopy'); ") or die(mysql_error());
    
	$js['affected'] = mysql_affected_rows();
	
	mysql_close($connection);
    
	echo json_encode($js);
	


	
}

function SubmitRemoveScript($CampaignID, $link)
{
    global $VARDB_server;
    global $VARDB_port;
            
    $con = mysql_connect("$VARDB_server:$VARDB_port", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk", $con);
    
  	mysql_query("DROP TABLE IF EXISTS custom_$sent_campaign_id") or die (mysql_error());
        
    mysql_query("DELETE FROM vicidial_lists_fields WHERE campaign_id='$sent_campaign_id'") or die(mysql_error());
    
    mysql_close($con);
}

switch($action)
{
	case "ScriptListBuilder": ScriptListBuilder($CampaignID, $AllowedCampaigns, $link); break;
	case "SubmitCopyScript": SubmitCopyScript($CampaignID, $ScriptToCopy, $link); break;
	case "SubmitRemoveScript": SubmitRemoveScript($CampaignID, $link); break;
}
