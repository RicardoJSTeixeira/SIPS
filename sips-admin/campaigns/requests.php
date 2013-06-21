<?php
require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) { 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

if($action == "RollbackIncompleteCampaign")
{
/*	$sent_campaign_id = "W00001"; 
	if($sent_campaign_id != "")  
	{
	mysql_query("DELETE FROM vicidial_campaigns WHERE campaign_id = '$sent_campaign_id'", $link) or die(mysql_error());
	mysql_query("DELETE FROM sips_campaign_stats WHERE campaign_id = '$sent_campaign_id'", $link) or die(mysql_error());
	mysql_query("DELETE FROM vicidial_campaign_stats WHERE campaign_id = '$sent_campaign_id'", $link) or die(mysql_error());
	mysql_query("DELETE FROM vicidial_lists WHERE campaign_id = '$sent_campaign_id'", $link) or die(mysql_error());
	mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns = REPLACE(allowed_campaigns, ' $sent_campaign_id', '')", $link) or die(mysql_error());
	
	mysql_query("DELETE FROM vicidial_pause_codes WHERE campaign_id = '$sent_campaign_id'", $link) or die(mysql_error());
	mysql_query("DELETE FROM vicidial_campaign_statuses WHERE campaign_id = '$sent_campaign_id'", $link) or die(mysql_error()); 
	mysql_query("DELETE FROM vicidial_admin_log WHERE record_id = '$sent_campaign_id'", $link) or die(mysql_error()); 
	mysql_query("DELETE FROM vicidial_lead_recycle WHERE campaign_id = '$sent_campaign_id'", $link) or die(mysql_error());	
	}  */
}

if($action == "dT_campaign-monitor") 
{
	$js = array("aaData" => array());
	$js['user'] = $_SERVER["PHP_AUTH_USER"];
	$aColumns = array( 'campaign_id', 'campaign_name', 'active', 'dial_method', 'auto_dial_level', 'pauses', 'feedbacks', 'recycle', 'script', 'fields', 'db-count', 'creation_date'); 
	
    $query = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$js[user]'", $link)) or die(mysql_error());
	
	$js['user_group'] = $query['user_group'];
	
    $query = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$js[user_group]'", $link)) or die(mysql_error());
    
	$js['allowed_campaigns'] = explode(" ", trim(preg_replace("/ -/", '', $query['allowed_campaigns'])));

    $sQuery = "
            SELECT A.campaign_id, A.campaign_name, A.active, A.dial_method, A.auto_dial_level, B.pauses, B.feedbacks, B.recycle, B.dynamic_fields, DATE_FORMAT(B.creation_date, '%H:%i:%s <br> %e/%c/%Y') as creation_date
            FROM   vicidial_campaigns A
			INNER JOIN sips_campaign_stats B ON A.campaign_id=B.campaign_id
			WHERE A.campaign_id IN('". implode("','", $js['allowed_campaigns'] ). "') AND A.campaign_id LIKE 'W%'
            ";
    $rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
  
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            switch($aColumns[$i]) 
            { 
				case "campaign_name": { if($aRow['pauses'] == 0 || $aRow['feedbacks'] == 0 || $aRow['dynamic_fields'] == 0){ $camp_warning_text = "style='color:red'"; $camp_warning = "<img class='mono-icon' title='Campanha com erros na configuração, por favor edite a Campanha e corrija estes erros.' src='icons/mono_alert_16.png'>";} else {$camp_warning = ""; $camp_warning_text = "";} $row[] = "<div style='float:left'><span $camp_warning_text>$aRow[campaign_name]</div><div  style='float:left; top:-3px'>$camp_warning</div></span>";  break;} 
                case "campaign_id": { $row[] = "<img id='img-campaign-edit' class='icon pointer' html-campaign-id='$aRow[campaign_id]' src='/images/icons/mono_wrench_16.png'>"; break; }
                case "dial_method": if($aRow[ $aColumns[$i] ] == "RATIO"){ $row[] = "Automática"; } else { $row[] = "Manual"; }; break;
				case "auto_dial_level": { $row[] = "x".$aRow['auto_dial_level']; } ; break;
                case "active": if($aRow[ $aColumns[$i] ]=="Y"){ $row[] = "<img title='Clique para alterar.' class='elem-pointer' id='cmp-enabler-".$aRow['campaign_id']."' onclick=\"CampaignEnabler('$aRow[campaign_id]');\" src='/images/icons/tick_16.png'>"; } else { $row[] = "<img title='Clique para alterar.' class='elem-pointer' onclick=\"CampaignEnabler('$aRow[campaign_id]');\" id='cmp-enabler-".$aRow['campaign_id']."' src='/images/icons/cross_16.png'>"; }; break; 
				case "pauses": {if($aRow[$aColumns[$i]] == 0){$row[] = "<b><span style='color:red'>Não</span></b>";} else { $row[] = $aRow[$aColumns[$i]]; } break;}
				case "feedbacks": {if($aRow[$aColumns[$i]] == 0){$row[] = "<b><span style='color:red'>Não</span></b>";} else { $row[] = $aRow[$aColumns[$i]]; } break;}
				case "script": { $query = mysql_query("SELECT campaign_id FROM vicidial_lists_fields WHERE campaign_id='$aRow[campaign_id]'") or die(mysql_query()); if(mysql_num_rows($query) > 0){ $row[] = "Sim"; } else { $row[] = "Não"; } break;  }
				case "fields": { if($aRow['dynamic_fields'] > 0){  $row[] = $aRow['dynamic_fields']; } else { $row[] = "<b><span style='color:red'>Não</span></b>";}   break;}
				case "recycle": { $user_recycle = $aRow[$aColumns[$i]] - 7; $row[] = "<b>7</b> + ".$user_recycle; break;}
				case "db-count": { $query = mysql_query("SELECT SUM(list_description) FROM vicidial_lists WHERE campaign_id = '$aRow[campaign_id]'") or die(mysql_error); $query = mysql_fetch_row($query); if(mysql_num_rows($query) > 0) { $row[] = $query[0]; } else { $row[] = 0; } break;   }
				default: $row[] = $aRow[ $aColumns[$i] ];
            }
        }
    $js['aaData'][] = $row;
    }
    echo json_encode( $js );
}

?>