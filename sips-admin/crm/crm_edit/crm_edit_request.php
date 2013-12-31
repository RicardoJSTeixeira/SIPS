<?php

require("../../../ini/dbconnect.php");
require("../../../ini/user.php");
require("../../../ini/db.php");
require("crm_edit_class.php");


foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


header('Content-Type: text/html; charset=utf-8');
header('Content-type: application/json');

$user = new mysiblings($db);
    
$crmEdit = new crm_edit_class($db, $user->user_level);


switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//
    case "get_user_level":
        echo  $user->user_level;
        break;

    case "get_lead_info":
        echo json_encode($crmEdit->get_lead_info($lead_id));
        break;
    case "get_dynamic_fields":
        echo json_encode($crmEdit->get_dynamic_fields($lead_id, $campaign_id));
        break;
    case "get_feedbacks":
        echo json_encode($crmEdit->get_feedbacks($feedback, $campaign_id));
        break;
    case "get_calls":
        echo json_encode($crmEdit->get_calls($lead_id,$campaign_id,$file_path,$user->full_name,$user->password,$user->user_level));
        break;
    case "get_recordings":
        echo json_encode($crmEdit->get_recordings($lead_id,$user->user_level));
        break;
    case "save_dynamic_fields":
        echo json_encode($crmEdit->save_dynamic_fields($lead_id, $fields));
        break;
    case "save_feedback":
        echo json_encode($crmEdit->save_feedback($lead_id, $feedback));
        break;
    case "get_agentes":
        echo json_encode($user->get_agentes());
        break;
    
     case "add_info_crm":
        echo json_encode($crmEdit->add_info_crm($lead_id, $option, $campaign_id, $agent, $comment, $user->id));
        break;
    
     case "get_info_crm_confirm_feedback":
        echo json_encode($crmEdit->get_info_crm_confirm_feedback($lead_id));
        break;
    
}