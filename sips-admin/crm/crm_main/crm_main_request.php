<?php

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

require("$root/ini/dbconnect.php");
require("$root/ini/user.php");
require("$root/ini/db.php");
require("$root/sips-admin/script_dinamico/script.php");
require("$root/sips-admin/crm/crm_main/crm_main_class.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


header('Content-Type: text/html; charset=utf-8');
header('Content-type: application/json');

$user = new mysiblings($db);
    
$crmMain = new crm_main_class($db);
$script = new script($db);

switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//
    case "get_campanha":
        echo json_encode($user->get_campaigns());
        break;
      case "get_linha_inbound":
        echo json_encode($crmMain->get_linha_inbound());
        break;
    case "get_bd":
        echo json_encode($crmMain->get_bd($campaign_id));
        break;
    case "get_agent":
        echo json_encode($user->get_agentes());
        break;
    case "get_feedbacks":
        echo json_encode($user->get_feedbacks($campaign_id));
        break;
    case "get_campos_dinamicos":
        echo json_encode($crmMain->get_campos_dinamicos($campaign_id));
        break;
    case "get_script":
        echo json_encode($script->get_script_by_campaign($campaign_id));
        break;
    case "get_script_individual":
        echo json_encode($script->get_data_individual($id));
        break;
    case "get_info_client":
        echo json_encode($crmMain->get_info_client($data_inicio, $data_fim, $campanha,$linha_inbound,$campaign_linha_inbound, $bd, $agente, $feedback, $cd, $script_info, $lead_id, $phone_number, $type_search));
        break;
    case "get_info_calls":
        echo json_encode($crmMain->get_info_calls($data_inicio, $data_fim, $campanha,$linha_inbound,$campaign_linha_inbound, $bd, $agente, $feedback, $cd, $script_info, $lead_id, $phone_number));
        break;
}
