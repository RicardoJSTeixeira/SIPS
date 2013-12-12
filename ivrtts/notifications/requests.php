<?php
require("../database/db_connect.php");
ini_set("display_errors", "1");

foreach ($_POST as $key => $value) { ${$key} = $value; }
foreach ($_GET as $key => $value) { ${$key} = $value; }

switch($ZERO) {
    case "BuildNotifications" : BuildNotifications($User); break;
    case "GetMessages" : GetMessages($User, $Get); break;
    case "ReadMessage" : ReadMessage($MessageID); break;
	case "CampaignEnableNotifications" : CampaignEnableNotifications($sent_campaign, $sent_active, $sent_status); break;


    case 'GetNotifications' : GetNotifications($id_user, $notification); break;
    default : header("HTTP/1.1 500 Internal Server Error"); exit;
}


function BuildNotifications($User){
    global $db;
    $Today = date("Y-m-d");
    $params = array($User);
    $results = $db->rawQuery("SELECT admin_global FROM zero.user_notifications WHERE id_user = ? LIMIT 1", $params);
    foreach($results[0] as $key=>$value){
        if($value == 1){ 
            switch($key){
                case "admin_global": $sql = "user_auth_log != 0"; break;
            }
            $params1 = array($User);
            $results1 = $db->rawQuery("SELECT count(*) FROM zero.notifications WHERE id_user= ? AND $sql AND event_time >= $Today AND viewed != 1", $params1);
            $js[$key][] = $results1[0]['count(*)'];
        } else { 
            $js[$key][] = 0; 
            }
    }
	
	// CAMPAING ENABLER
	$params = array("Y");
    $results = $db->rawQuery("SELECT count(*) as num_camps FROM vicidial_campaigns A INNER JOIN zero.allowed_campaigns B ON A.campaign_id=B.campaigns WHERE A.active = ?", $params);
	$js['camp_enabler'][] = $results[0]['num_camps'];
    
    echo json_encode($js);
}

function GetMessages($User, $Get){
    global $db;
    $Today = date("Y-m-d");
    switch($Get){
        case "get-admin-global-messages": {
            $params = array($User);
            $results = $db->rawQuery("SELECT id_notification, viewed, user_auth_log FROM zero.notifications WHERE id_user = ? AND event_time >= $Today ORDER BY id_notification DESC", $params);    

            for($i=0;$i<count($results);$i++){
                foreach($results[$i] as $key => $value){ 
                    if($value !== 0){
                        switch($key){
                            case "id_user_notification": break;
                            case "user_auth_log": {
                                $params1 = array($value);
                                $results1 = $db->rawQuery("SELECT B.name, B.last_name, A.event, A.event_time FROM zero.user_auth_log A INNER JOIN zero.user_info B ON A.id_user=B.id_user WHERE A.id_user_auth_log = ? LIMIT 1", $params1); 
                                array_unshift_assoc($results1[0], "message_type", "user_auth_log");
                                array_unshift_assoc($results1[0], "id_notification", $results[$i]['id_notification']);
                                array_unshift_assoc($results1[0], "viewed", $results[$i]['viewed']);
                                
                                $js['messages'][] = $results1[0]; 
                            }
                        }
                    }
                }
            }
        echo json_encode($js); break; 
        }
		case "get-campaign-enabler": {
            $params = array();
            $results = $db->rawQuery("SELECT A.campaign_name, A.campaign_id, A.active, B.list_description  FROM vicidial_campaigns A INNER JOIN vicidial_lists B ON A.campaign_id=B.campaign_id INNER JOIN zero.allowed_campaigns C ON A.campaign_id=C.campaigns ORDER BY A.campaign_name");    
			
			
			
			

                foreach($results as $key => $value)
				{
					//array_unshift_assoc($value, "message_type", "campaign_enabler"); 
					$js['messages'][] = $value;
				}
			
			echo json_encode($js); 
			
   /*         for($i=0;$i<count($results);$i++){
                foreach($results[$i] as $key => $value){ 
                   
                        switch($key){
                            case "id_user_notification": break;
                            case "user_auth_log": {
                                $params1 = array($value);
                                $results1 = $db->rawQuery("SELECT B.name, B.last_name, A.event, A.event_time FROM zero.user_auth_log A INNER JOIN zero.user_info B ON A.id_user=B.id_user WHERE A.id_user_auth_log = ? LIMIT 1", $params1); 
                                array_unshift_assoc($results1[0], "message_type", "user_auth_log");
                                array_unshift_assoc($results1[0], "id_notification", $results[$i]['id_notification']);
                                array_unshift_assoc($results1[0], "viewed", $results[$i]['viewed']);
                                
                                $js['messages'][] = $results1[0]; 
                            }
                        }
                    
                }
            } */
        
        }
    }
    
}

function ReadMessage($MessageID){
    global $db;
    $params = array($MessageID);
    $db->rawUpdate("UPDATE zero.notifications SET viewed = 1 WHERE id_notification = ?", $params);    
}
   
    
function array_unshift_assoc(&$arr, $key, $val) 
{ 
    $arr = array_reverse($arr, true); 
    $arr[$key] = $val; 
    $arr = array_reverse($arr, true); 
    return count($arr); 
}

function CampaignEnableNotifications($sent_campaign, $sent_active, $sent_status)
{
	global $db;
	$params = array($sent_active, $sent_campaign);
	$results = $db->rawUpdate("UPDATE vicidial_campaigns SET active = ? WHERE campaign_id = ?", $params);
	
	$params = array($sent_status, $sent_campaign);
	$results = $db->rawUpdate("UPDATE vicidial_remote_agents SET status = ? WHERE campaign_id = ?", $params);
	    
}

     
?>
