<?php

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$root/ini/dbconnect.php");
require("$root/ini/user.php");
require("$root/ini/db.php");
require("$root/sips-admin/crm/crm_edit/crm_edit_class.php");


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
        echo $user->user_level;
        break;
    case "get_lead_info":
        echo json_encode($crmEdit->get_lead_info($lead_id));
        break;
    case "get_dynamic_fields":
        echo json_encode($crmEdit->get_dynamic_fields($lead_id, $campaign_id));
        break;
    case "get_feedbacks":
        echo json_encode($crmEdit->get_feedbacks($campaign_id));
        break;
    case "get_agentes":
        echo json_encode($user->get_agentes($user->allowed_campaigns, $user->is_all_campaigns));
        break;
    //calls-----calls-------------calls-------------calls------------calls------------calls------------calls------------calls------------calls--------------------------------calls


    case "get_calls_all":

        $has_script = false;

        $calls_out = $crmEdit->get_calls_outbound($lead_id);
        $calls_in = $crmEdit->get_calls_inbound($lead_id);
        foreach ($calls_out as $value) {
            $calls[] = $value;
        }
        foreach ($calls_in as $value) {
            $calls[] = $value;
        }

        if (count($calls, 1) > 0) {
            foreach ($calls as $key => &$value) {
                $value[1] = gmdate("H:i:s", $value[1]);
                $has_script = false;
                $count = $crmEdit->check_has_script($value["campaign_id"]);
    
                if ($count[0] > 0) {
                    $has_script = true;
                }
                if ($user->user_level > 5 && $has_script) {
                    $value[9] = $value[9] . " <div class='view-button edit_item'><a class='btn btn-mini btn-primary' target='_new' href='" . $file_path . "crm_edit/script_placeholder.html?lead_id=" .$lead_id . "&campaign_id=" . $value["campaign_id"] . "&user=$user->full_name&pass=$user->password&isadmin=1&unique_id=" . $value["uniqueid"] . "'><i class='icon-bookmark'></i>Script</a></div>";
                }
            }
            $output['aaData'] = $calls;
            echo json_encode($output);
            break;
        }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------

    case "get_recordings":
        //  $preview_button = 
        $recordings = $crmEdit->get_recordings($lead_id);
        $curpage = curPageURL();
        $folder = "MP3";
        foreach ($recordings as $key => &$value1) {
            foreach ($value1 as &$value) {
                $value[0] = date("Y-m:d", strtotime($value[0]));
                $value[1] = date("H:i:s", strtotime($value[1]));
                $value[2] = date("H:i:s", strtotime($value[2]));
                $value[3] = gmdate("H:i:s", $value[3]);
                if ($user->user_level > 5) {
                    $mp3File = "#";
                    if (strlen($value["location"]) > 0) {
                        //if lan
                        if (reserved_ip($user->ip)) {
                            $mp3File = $value["location"];
                        } else {
                            $tmp = explode("/", $value["location"]);
                            $ip = $tmp[2];
                            $tmp = explode(".", $ip);
                            $ip = $tmp[3];

                            switch ($ip) {
                                case "248":
                                    $port = ":20248";
                                    break;
                                case "247":
                                    $port = ":20247";
                                    break;
                                default:
                                    $port = "";
                                    break;
                            }
                            if (strpos($value["location"], '/FTP/'))
                                $folder = "FTP";
                            else
                                $folder = "MP3";
                            $mp3File = $curpage . $port . "/RECORDINGS/$folder/" . $value["filename"] . "-all.mp3";
                        }
                        $value[4] = $value[4] . "<div class='view-button edit_item'><a href='$mp3File' target='_self' class='btn btn-mini btn-primary'><i class='icon-play'></i>Ouvir</a></div>";
                    }
                }
            }
        }
        echo json_encode($recordings);
        break;
    case "save_dynamic_fields":
        echo json_encode($crmEdit->save_dynamic_fields($lead_id, $fields));
        break;
    case "save_feedback":
        echo json_encode($crmEdit->save_feedback($lead_id, $feedback));
        break;
    case "add_info_crm":
        echo json_encode($crmEdit->add_info_crm($lead_id, $option, $campaign_id, $agent, $comment, $user->id));
        break;
    case "get_info_crm_confirm_feedback":
        echo json_encode($crmEdit->get_info_crm_confirm_feedback($lead_id));
        break;
}

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"]; //. $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"]; //.$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function reserved_ip($ip) {
    $reserved_ips = array(// not an exhaustive list
        '167772160' => 184549375, /*    10.0.0.0 -  10.255.255.255 */
        '3232235520' => 3232301055, /* 192.168.0.0 - 192.168.255.255 */
        '2130706432' => 2147483647, /*   127.0.0.0 - 127.255.255.255 */
        '2851995648' => 2852061183, /* 169.254.0.0 - 169.254.255.255 */
        '2886729728' => 2887778303, /*  172.16.0.0 -  172.31.255.255 */
        '3758096384' => 4026531839, /*   224.0.0.0 - 239.255.255.255 */
    );

    $ip_long = sprintf('%u', ip2long($ip));

    foreach ($reserved_ips as $ip_start => $ip_end) {
        if (($ip_long >= $ip_start) && ($ip_long <= $ip_end)) {
            return TRUE;
        }
    }
    return FALSE;
}
