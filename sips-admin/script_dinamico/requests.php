<?php

require("../../ini/dbconnect.php");
require("../../ini/user.php");
require("../../ini/db.php");
require("script.php");
require("../../ini/htmlpurifier/HTMLPurifier.standalone.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

header('Content-Type: text/html; charset=utf-8');
header('Content-type: application/json');

$user = new users;

$purifier = new HTMLPurifier();


$script = new script($db);
switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//
    case "get_feedbacks":
        echo json_encode($script->get_feedbacks());
        break;

    case "get_schedule":
        echo json_encode($script->get_schedule($user->user_group));
        break;

    case "get_schedule_by_id":
        echo json_encode($script->get_schedule_by_id($ids));
        break;

    case "get_element_tags":
        echo json_encode($script->get_element_tags($id_script));
        break;

    case "get_client_info_by_lead_id":
        echo json_encode($script->get_client_info_by_lead_id($lead_id, $user->getUser($user_logged)));
        break;

    case "get_tag_fields":
        echo json_encode($script->get_tag_fields($id_script));
        break;

    case "get_camp_linha_by_id_script":
        echo json_encode($script->get_camp_linha_by_id_script($id_script));
        break;

    case "get_scripts":
        echo json_encode($script->get_scripts($user->user_group));
        break;

    case "get_scripts_by_campaign":
        echo json_encode($script->get_scripts_by_campaign($id_campaign));
        break;

    case "get_scripts_by_id_script":
        echo json_encode($script->get_scripts_by_id_script($id_script));
        break;

    case "get_results_to_populate":
        echo json_encode($script->get_results_to_populate($lead_id, $id_script));
        break;

    case "get_pages":
        echo json_encode($script->get_pages($id_script));
        break;

    case "get_data_render":
        echo json_encode($script->get_data_render($id_script));
        break;

    case "get_data":
        echo json_encode($script->get_data($id_script, $id_page));
        break;

    case "get_data_individual":
        echo json_encode($script->get_data_individual($id));
        break;

    case "get_rules_by_trigger":
        echo json_encode($script->get_rules_by_trigger($tag_trigger, $id_script));
        break;

    case "get_rules":
        echo json_encode($script->get_rules($id_script));
        break;

    case 'get_campaign_linha_inbound':
        echo json_encode($script->get_campaign_linha_inbound(implode("','", $user->allowed_campaigns), $user->allowed_campaigns));
        break;

    case 'check_duplicates_campaign_linha_inbound':
        echo json_encode($script->check_duplicates_campaign_linha_inbound($campaign, $linha_inbound));
        break;

    case 'iscloud':
        echo json_encode($script->iscloud());
        break;

    case 'has_rules':
        echo json_encode($script->has_rules($tag));
        break;

    case "get_image_pdf":
        echo json_encode($script->get_image_pdf());
        break;

    case "get_php_ajax":
        echo json_encode($script->get_php_ajax());
        break;
    //-----------------------------------------------//
    //-----------------EDIT---------------------------//
    //------------------------------------------------//
    case "edit_script":
        echo json_encode($script->edit_script($name, $id_script, $campaign));
        break;

    case "edit_page":
        echo json_encode($script->edit_page($old_pos, $new_pos, $id_script, $name, $id_pagina));
        break;

    case "edit_item":
        if (is_string($values_text)) {
            $clean_text = $purifier->purify($values_text);
        } else {
            $clean_text = $values_text;
        }
        echo json_encode($script->edit_item($id_script, $id_page, $type, $ordem, $dispo, $texto, $placeholder, $max_length, $clean_text, $default_value, ($required == "true") ? "1" : "0", ($hidden == "true") ? "1" : "0", $param1, $id));
        break;

    case "edit_item_order":
        echo json_encode($script->edit_item_order($ordem, $id));
        break;
    //------------------------------------------------//
    //-----------------ADD----------------------------//
    //------------------------------------------------//
    case "add_page":
        echo $script->add_page($id_script, $pos);
        break;

    case "add_script":
        echo $script->add_script($user->user_group);
        break;

    case "add_item":
        echo $script->add_item($id_page, $id_script, $type, $ordem, $dispo, $texto, $placeholder, $max_length, $values_text, $default_value, $required, $hidden, $param1);
        break;

    case "add_rules":
        echo $script->add_rules($tag_trigger2, $tag_trigger, $tag_target, $id_script, $tipo_elemento, $tipo, $param1, $param2);
        break;

    case "duplicate_script":
        echo $script->duplicate_script($user->user_group, $nome_script, $id_script);
        break;
    //------------------------------------------------//
    //-----------------DELETE-------------------------//
    //------------------------------------------------//
    case "delete_page":
        echo $script->delete_page($pos, $id_script, $id_pagina);
        break;

    case "delete_item":
        echo $script->delete_item($ordem, $id_page, $param1, $id);
        break;

    case "delete_script":
        echo $script->delete_script($id_script);
        break;

    case "delete_rule":
        echo $script->delete_rule($id);
        break;
    //------------------------------------------------//
    //-----------------FORM---------------------------//
    //------------------------------------------------//
    case "save_form_result":
        $temp = json_encode($script->save_form_result($id_script, $results, $user_id, $unique_id, $campaign_id, $lead_id, $admin_review));
        echo $temp;
        break;
}

