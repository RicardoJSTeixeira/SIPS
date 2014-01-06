<?php

class crm_edit_class {

    protected $db;

    public function __construct($db, $user_level) {
        $this->db = $db;
        $this->user_level = $user_level;
    }

    public function get_agentes($user_allowed_campaigns, $user_is_all_campaigns, $user_level) {
        $allowed_camps_regex = implode("|", $user_allowed_campaigns);
        if (!$user_is_all_campaigns) {
            $ret = "WHERE allowed_campaigns REGEXP '$allowed_camps_regex'";


            $user_groups = "";

            $stmt = $this->db->prepare("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user_groups .= "'$row[user_group]',";
            }
            $user_groups = rtrim($user_groups, ",");


            $stmt = $this->db->prepare("SELECT `user` FROM `vicidial_users` WHERE user_group in ($user_groups) AND user_level < $user_level");
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmp .= "$row[user]|";
            }
            $tmp = rtrim($tmp, "|");
            $users_regex = "Where user REGEXP '^$tmp'";
        }
        $js = array();
        $stmt = $this->db->prepare("SELECT user,full_name  FROM vicidial_users $users_regex group by user order by full_name ");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("user" => $row["user"], "full_name" => $row["full_name"]);
        }
        return $js;
    }

    public function get_lead_info($lead_id) {
        $js = array();
        $query = "SELECT 
                        vdlf.campaign_id,
                        vdc.campaign_name,
                        vdlf.list_id,
                        vdlf.list_name,
                        vu.full_name,
                        vdl.called_count,
                        vdl.modify_date data_last,
                        vstatus.status_name,
                        vstatus.status,
                         vdl.last_local_call_time data_last,
                       vstatus.status_name,
                        vstatus.status,
                        
                     vdl.entry_date AS data_load,
                        vdl.phone_number
                        FROM vicidial_lists vdlf
                    
                left JOIN vicidial_list vdl ON  vdl.list_id=vdlf.list_id                                                 
                left JOIN vicidial_campaigns vdc ON vdc.campaign_id=vdlf.campaign_id
                LEFT JOIN vicidial_users vu ON vu.user=vdl.user
                 LEFT JOIN   (select status,status_name from vicidial_statuses a union all select status,status_name from vicidial_campaign_statuses b) vstatus on vstatus.status=vdl.status
                WHERE lead_id=:lead_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $js = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $query = "SELECT vl.call_date data_last,vstatus.status_name,vstatus.status FROM vicidial_log vl
                 LEFT JOIN   (select status,status_name from vicidial_statuses a union all select status,status_name from vicidial_campaign_statuses b) vstatus on vstatus.status=vl.status
                WHERE lead_id=:lead_id order by call_date desc limit 1 ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $count = $stmt->rowCount();
        if ($count) {
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $js["data_last"] = $row["data_last"];
            $js["status_name"] = $row["status_name"];
            $js["status"] = $row["status"];
            
        } else {
            $query = "SELECT vl.call_date data_last,vstatus.status_name,vstatus.status FROM vicidial_log_archive vl
                 LEFT JOIN   (select status,status_name from vicidial_statuses a union all select status,status_name from vicidial_campaign_statuses b) vstatus on vstatus.status=vl.status
                WHERE lead_id=:lead_id order by call_date desc limit 1 ";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id));
            $count = $stmt->rowCount();
            if ($count) {
                
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $js["data_last"] = $row["data_last"];
                $js["status_name"] = $row["status_name"];
                $js["status"] = $row["status"];
            }
        }






        return $js;
    }

    public function get_dynamic_fields($lead_id, $campaign_id) {

        $dfields = array();
        $query = "SELECT Name,Display_name   FROM vicidial_list_ref WHERE campaign_id=:campaign_id AND active=1 Order by field_order ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dfields[$row["Name"]] = array("display_name" => $row["Display_name"], "name" => $row["Name"], "value" => "");
        }

        if ($dfields) {
            $query = "SELECT " . implode(",", array_keys($dfields)) . "  FROM  vicidial_list WHERE  lead_id=:lead_id";

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            foreach ($row as $key => $value) {

                $value = str_replace('!N', "\r\n", $value);
                $dfields[$key]["value"] = $value;
            }
        }

        return $dfields;
    }

    public function get_feedbacks($feedback, $campaign_id) {
        $feedback_options = array();
        $query = "SELECT status,status_name,sale FROM  (select status,status_name,sale from vicidial_campaign_statuses WHERE campaign_id=:campaign_id AND scheduled_callback!=1) a union all (select status,status_name,sale from vicidial_statuses)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row["status"] == $feedback)
                $feedback_options .= "<option data-sale='$row[sale]' selected value='$row[status]'>$row[status_name]</option>\n";
            else
                $feedback_options .= "<option data-sale='$row[sale]' value='$row[status]'>$row[status_name]</option>\n";
        }
        return $feedback_options;
    }

    public function get_calls($lead_id, $campaign_id, $file_path, $user_name, $user_pass, $user_level) {
        $output = array("aaData" => array());
        $js1 = array();
        $js2 = array();
        
        $query = "SELECT vl.uniqueid, 
                        vl.lead_id, 
                        vl.list_id,
                        vl.campaign_id,
                        vl.call_date AS data,
                        vl.start_epoch,
                        vl.end_epoch,
                        vl.length_in_sec,
                        vl.phone_code,
                        vl.phone_number,
                        vl.user,
                        vl.comments,
                        vl.processed,
                        vl.user_group,
                        vl.term_reason,
                        vl.alt_dial,
                        vu.full_name,
                        vstatus.status_name,
                        vc.campaign_name,
                        vls.list_name
                FROM 
                        vicidial_log vl
                left JOIN vicidial_users vu ON vl.user=vu.user
                left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                        vl.lead_id=:lead_id 
                ORDER BY
                        end_epoch 
                DESC;";

        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $count = $stmt->rowCount();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($user_level > 5)
                $preview_button = " <div class='view-button edit_item'><a class='btn btn-mini btn-primary' target='_new' href='" . $file_path . "script_placeholder.html?lead_id=$lead_id&campaign_id=$campaign_id&user=$user_name&pass=$user_pass&isadmin=1&unique_id=" . $row["uniqueid"] . "'><i class='icon-bookmark'></i>Script</a></div>";
            else
                $preview_button = "";

            $output['aaData'][] = array("0" => $row["data"], "1" => gmdate("H:i:s", $row["length_in_sec"]), "2" => $row["phone_number"], "3" => $row["full_name"], "4" => $row["status_name"], "5" => $row["campaign_name"], "6" => $row["list_name"] . $preview_button);
        }
        $query = "SELECT                                                                         
                        vl.uniqueid, 
                        vl.lead_id, 
                        vl.list_id,
                        vl.campaign_id,
                        vl.call_date AS data,
                        vl.start_epoch,
                        vl.end_epoch,
                        vl.length_in_sec,
                        vl.phone_code,
                        vl.phone_number,
                        vl.user,
                        vl.comments,
                        vl.processed,
                        vl.user_group,
                        vl.term_reason,
                        vu.full_name,
                        vstatus.status_name,
                        vc.campaign_name,
                        vls.list_name
                FROM 
                        vicidial_closer_log vl
                left JOIN vicidial_users vu ON vl.user=vu.user
                left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                       vl.lead_id=:lead_id 
                ORDER BY
                        end_epoch 
                DESC;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $count = $count + $stmt->rowCount();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($user_level > 5)
                $preview_button = " <div class='view-button edit_item'><a class='btn btn-mini btn-primary' target='_new' href='" . $file_path . "script_placeholder.html?lead_id=$lead_id&campaign_id=$campaign_id&user=$user_name&pass=$user_pass&isadmin=1&unique_id=" . $row["uniqueid"] . "'><i class='icon-bookmark'></i>Script</a></div>";
            else
                $preview_button = "";
            $output['aaData'][] = array("0" => $row["data"], "1" => gmdate("H:i:s", $row["length_in_sec"]), "2" => $row["phone_number"], "3" => $row["full_name"], "4" => $row["status_name"], "5" => $row["campaign_name"], "6" => $row["list_name"] . $preview_button);
        }

        if ($count == 0) {
            $query = "SELECT vl.uniqueid, 
                        vl.lead_id, 
                        vl.list_id,
                        vl.campaign_id,
                        vl.call_date AS data,
                        vl.start_epoch,
                        vl.end_epoch,
                        vl.length_in_sec,
                        vl.phone_code,
                        vl.phone_number,
                        vl.user,
                        vl.comments,
                        vl.processed,
                        vl.user_group,
                        vl.term_reason,
                        vl.alt_dial,
                        vu.full_name,
                        vstatus.status_name,
                        vc.campaign_name,
                        vls.list_name
                FROM 
                        vicidial_log_archive vl
                left JOIN vicidial_users vu ON vl.user=vu.user
                left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                        vl.lead_id=:lead_id 
                ORDER BY
                        end_epoch 
                DESC;";

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id));

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                if ($user_level > 5)
                    $preview_button = " <div class='view-button edit_item'><a class='btn btn-mini btn-primary' target='_new' href='" . $file_path . "script_placeholder.html?lead_id=$lead_id&campaign_id=$campaign_id&user=$user_name&pass=$user_pass&isadmin=1&unique_id=" . $row["uniqueid"] . "'><i class='icon-bookmark'></i>Script</a></div>";
                else
                    $preview_button = "";

                $output['aaData'][] = array("0" => $row["data"], "1" => gmdate("H:i:s", $row["length_in_sec"]), "2" => $row["phone_number"], "3" => $row["full_name"], "4" => $row["status_name"], "5" => $row["campaign_name"], "6" => $row["list_name"] . $preview_button);
            }
        }



        return $output;
    }

    public function get_recordings($lead_id, $user_level) {
        $output = array("aaData" => array());
        $query = "SELECT 
                                DATE_FORMAT(start_time,'%d-%m-%Y') AS data,
                                DATE_FORMAT(start_time,'%H:%i:%s') AS hora_inicio,
                                DATE_FORMAT(end_time,'%H:%i:%s') AS hora_fim,
                                length_in_sec,
                                filename,
                                location,
                                lead_id,
                                rl.user,
                                full_name
                        FROM 
                                recording_log rl
                        INNER JOIN vicidial_users vu ON rl.user=vu.user
                        WHERE 
                                lead_id=:lead_id 
                        ORDER BY 
                                recording_id 
                        DESC;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            if ($user_level > 5){
                 $mp3File = "#";
                        if (strlen($row[location]) > 0) {
                        //if lan
                        if ($this->reserved_ip($this->get_client_ip())) {
                        $mp3File = $row[location];
                        } else {
                        $tmp = explode("/", $row[location]);
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
                        $mp3File = $curpage . $port . "/RECORDINGS/MP3/$row[filename]-all.mp3";
                        }
                        $audioPlayer = "Há gravação";
                        } else {
                        $audioPlayer = "Não há gravação!";
                        }

                $preview_button = "<div class='view-button edit_item'><a href='$mp3File' target='_self' class='btn btn-mini btn-primary'><i class='icon-play'></i>Ouvir</a></div>";
            }else{
            $preview_button = "";
            }
            $output['aaData'][] = array("0" => $row["data"], "1" => $row["hora_inicio"], "2" => $row["hora_fim"], "3" => gmdate("H:i:s", $row["length_in_sec"]), "4" => $row["full_name"] . $preview_button);
        }
        return $output;
    }

    public function save_dynamic_fields($lead_id, $fields) {
        $fields_update = "";

        foreach ($fields as $value) {
            if ($value["value"] != "")
                $fields_update.=$value["name"] . "='" . $value["value"] . "',";
        }
        $fields_update = rtrim($fields_update, ",");
        $query = "UPDATE vicidial_list SET $fields_update where lead_id=:lead_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        return 1;
    }

    public function save_feedback($lead_id, $feedback) {
# Update vicidial_list
        $query = "UPDATE vicidial_list SET status=:feedback WHERE lead_id=:lead_id";
        $stmt1 = $this->db->prepare($query);
        $stmt1->execute(array(":lead_id" => $lead_id, ":feedback" => $feedback));

# Update vicidial_log 
        $query = "UPDATE vicidial_log SET status=:feedback WHERE lead_id=:lead_id ORDER BY call_date DESC LIMIT 1";
        $stmt2 = $this->db->prepare($query);
        $stmt2->execute(array(":lead_id" => $lead_id, ":feedback" => $feedback));

# Update vicidial_agent_log
        $query = "UPDATE vicidial_agent_log SET status=:feedback WHERE lead_id=:lead_id ORDER BY agent_log_id DESC LIMIT 1";
        $stmt3 = $this->db->prepare($query);
        $stmt3->execute(array(":lead_id" => $lead_id, ":feedback" => $feedback));
# Update vicidial_closer_log | inbound
        $query = "UPDATE vicidial_closer_log SET status=:feedback WHERE lead_id=:lead_id  ORDER BY call_date DESC LIMIT 1";
        $stmt4 = $this->db->prepare($query);
        $stmt4->execute(array(":lead_id" => $lead_id, ":feedback" => $feedback));

        if ($stmt1 and $stmt2 and $stmt3 and $stmt4) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function add_info_crm($lead_id, $option, $campaign_id, $agent, $comment, $user_id) {

        if ($option == "1") {
            $feedback = "Validado";
            $vl_validation = "S";
            $sale = 1;
        } else if ($option == "0") {
            $feedback = "Não validado";
            $vl_validation = "N";
            $sale = 0;
        } else {
            $feedback = "Por validar";
            $vl_validation = "R";
            $sale = 2;
        }
        $query = "INSERT INTO `crm_confirm_feedback`(`id`, `lead_id`, `feedback`, `sale`, `campaign`, `agent`, `comment`,date,admin) VALUES (NULL,:lead_id,:feedback,:sale,:campaign_id,:agent,:comment,:date,:user_id)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id, ":feedback" => $feedback, ":sale" => $sale, ":campaign_id" => $campaign_id, ":agent" => $agent, ":comment" => $comment, ":date" => date('Y-m-d H:i:s'), ":user_id" => $user_id));

        $query = "Update vicidial_list set validation='S' where lead_id=:lead_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));



        $query = "SELECT EXISTS(SELECT * FROM crm_confirm_feedback_last WHERE lead_id=:lead_id) as count";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['count'] == "0") {
            $query = "INSERT INTO `crm_confirm_feedback_last`(`id`, `lead_id`, `feedback`, `sale`, `campaign`, `agent`, `comment`,date,admin) VALUES (NULL,:lead_id,:feedback,:sale,:campaign_id,:agent,:comment,:date,:user_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id, ":feedback" => $feedback, ":sale" => $sale, ":campaign_id" => $campaign_id, ":agent" => $agent, ":comment" => $comment, ":date" => date('Y-m-d H:i:s'), ":user_id" => $user_id));
        } else {
            $query = "UPDATE crm_confirm_feedback_last SET feedback=:feedback,sale=:sale,agent=:agent,comment=:comment,date=:date,admin=:user_id WHERE lead_id=:lead_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id, ":feedback" => $feedback, ":sale" => $sale, ":agent" => $agent, ":comment" => $comment, ":date" => date('Y-m-d H:i:s'), ":user_id" => $user_id));
        }

        return 1;
    }

    public function get_info_crm_confirm_feedback($lead_id) {
        $js = array();
        $query = "SELECT `id`, `lead_id`, `feedback`, `sale`, `campaign`,admin, `agent`, `comment`,date FROM crm_confirm_feedback where lead_id=:lead_id order by id asc";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $js[] = array("id" => $row["id"], "lead_id" => $row["lead_id"], "feedback" => $row["feedback"], "sale" => $row["sale"], "campaign" => $row["campaign"], "admin" => $row["admin"], "agent" => $row["agent"], "comment" => $row["comment"], "date" => $row["date"]);
        }

        return $js;
    }
    
    
    private function curPageURL() {
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

private function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if ($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if ($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

private function reserved_ip($ip) {
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

}
