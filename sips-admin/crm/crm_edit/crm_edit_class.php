<?php

class crm_edit_class {

    protected $db;

    public function __construct($db, $user_level) {
        $this->db = $db;
        $this->user_level = $user_level;
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

        if ($count == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($row["data_last"]))
                $js["data_last"] = $row["data_last"];
            if (isset($row["status_name"]))
                $js["status_name"] = $row["status_name"];
            if (isset($row["status"]))
                $js["status"] = $row["status"];
        } else {
            $query = "SELECT vl.call_date data_last,vstatus.status_name,vstatus.status FROM vicidial_log_archive vl
                 LEFT JOIN   (select status,status_name from vicidial_statuses a union all select status,status_name from vicidial_campaign_statuses b) vstatus on vstatus.status=vl.status
                WHERE lead_id=:lead_id order by call_date desc limit 1 ";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id));
            $count = $stmt->rowCount();

            if ($count == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($row["data_last"]))
                    $js["data_last"] = $row["data_last"];
                if (isset($row["status_name"]))
                    $js["status_name"] = $row["status_name"];
                if (isset($row["status"]))
                    $js["status"] = $row["status"];
            }
        }
        if (!isset($js["full_name"]))
            $js["full_name"] = "Sem agente";
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

        if (count($dfields)) {
            $query = "SELECT " . implode(",", array_keys($dfields)) . "  FROM  vicidial_list WHERE  lead_id=:lead_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                $value = str_replace('!N', "\r\n", $value);
                $dfields[$key]["value"] = $value;
            }
        } else {
            $dfields["FIRST_NAME"] = array("display_name" => "Nome", "name" => "FIRST_NAME", "value" => "");
            $dfields["PHONE_NUMBER"] = array("display_name" => "Telefone", "name" => "PHONE_NUMBER", "value" => "");
            $dfields["ADDRESS3"] = array("display_name" => "Telemóvel", "name" => "ADDRESS3", "value" => "");
            $dfields["ALT_PHONE"] = array("display_name" => "Telefone Alternativo", "name" => "ALT_PHONE", "value" => "");
            $dfields["ADDRESS1"] = array("display_name" => "Morada", "name" => "ADDRESS1", "value" => "");
            $dfields["POSTAL_CODE"] = array("display_name" => "Código Postal", "name" => "POSTAL_CODE", "value" => "");
            $dfields["EMAIL"] = array("display_name" => "E-mail", "name" => "EMAIL", "value" => "");
            $dfields["COMMENTS"] = array("display_name" => "Comentários", "name" => "COMMENTS", "value" => "");
            $query = "SELECT FIRST_NAME,PHONE_NUMBER,ADDRESS3,ALT_PHONE,ADDRESS1,POSTAL_CODE,EMAIL,COMMENTS  FROM  vicidial_list WHERE  lead_id=:lead_id";
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

    public function get_feedbacks($campaign_id) {
        $feedback_options = array();
        $query = "SELECT status,status_name,sale FROM  (select status,status_name,sale from vicidial_campaign_statuses WHERE campaign_id=:campaign_id AND scheduled_callback!=1) a union all (select status,status_name,sale from vicidial_statuses)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $feedback_options [] = array("status" => $row["status"], "status_name" => $row["status_name"], "sale" => $row["sale"]);
        }
        return $feedback_options;
    }

    public function get_calls_outbound($lead_id) {
        $output = array("aaData" => array());
        $js1 = array();
        $js2 = array();
        $query = "SELECT
                        vl.call_date AS data,
                        vl.length_in_sec,
                        vl.phone_number,
                        vu.full_name,
                        vstatus.status_name,
                        vc.campaign_name,
                        vls.list_name,
                        vl.uniqueid, 
                        vl.lead_id, 
                        vl.list_id,
                        vl.campaign_id
                                       FROM 
                        vicidial_log vl
                left JOIN vicidial_users vu ON vl.user=vu.user
                left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                        vl.lead_id=:lead_id 
                ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
        return $output;
    }

    public function get_calls_inbound($lead_id) {
        $output = array("aaData" => array());
        $js1 = array();
        $js2 = array();
        $query = "SELECT     
                        vl.call_date AS data,
                        vl.length_in_sec,
                        vl.phone_number,
                        vu.full_name,
                        vstatus.status_name,
                        vc.group_name,
                        vls.list_name,
                        vl.uniqueid, 
                        vl.lead_id, 
                        vl.list_id,
                        vl.campaign_id
                                                              FROM 
                        vicidial_closer_log vl
                left JOIN vicidial_users vu ON vl.user=vu.user
                left JOIN vicidial_inbound_groups vc ON vl.campaign_id=vc.group_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                       vl.lead_id=:lead_id 
                ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
        return $output;
    }

    public function get_calls_archive_outbound($lead_id) {
        $output = array("aaData" => array());
        $js1 = array();
        $js2 = array();
        $query = "SELECT      
                        vl.call_date AS data,
                        vl.length_in_sec,
                        vl.phone_number,
                        vu.full_name,
                        vstatus.status_name,
                        vc.campaign_name,
                        vls.list_name,
                        vl.uniqueid, 
                        vl.lead_id, 
                        vl.list_id,
                        vl.campaign_id
                   
                FROM 
                        vicidial_log_archive vl
                left JOIN vicidial_users vu ON vl.user=vu.user
                left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                        vl.lead_id=:lead_id 
               ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);

        return $output;
    }

    public function get_calls_archive_inbound($lead_id) {
        $output = array("aaData" => array());
        $js1 = array();
        $js2 = array();
        $query = "SELECT      
                        vl.call_date AS data,
                        vl.length_in_sec,
                        vl.phone_number,
                        vu.full_name,
                        vstatus.status_name,
          vc.group_name,
                        vls.list_name,
                        vl.uniqueid, 
                        vl.lead_id, 
                        vl.list_id,
                        vl.campaign_id
                   
                FROM 
                        vicidial_closer_log_archive vl
                left JOIN vicidial_users vu ON vl.user=vu.user
               left JOIN vicidial_inbound_groups vc ON vl.campaign_id=vc.group_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                        vl.lead_id=:lead_id               ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);

        return $output;
    }

    public function get_recordings($lead_id) {
        $output = array("aaData" => array());
        $query = "SELECT 
                                start_time as data,
                                 start_time as data_inicio,
                                
                               end_time as data_fim,
                                length_in_sec,
                                full_name,
                                filename, 
                                location,
                                lead_id,
                                rl.user
                     
                        FROM 
                                recording_log rl
                        INNER JOIN vicidial_users vu ON rl.user=vu.user
                        WHERE 
                                lead_id=:lead_id 
                       ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
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

    public function check_has_script($campaign_id) {
        $js = array();
        $query = "SELECT count(*) from script_assoc where id_camp_linha=:campaign";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":campaign" => $campaign_id));
        return $stmt->fetch(PDO::FETCH_NUM);
    }

}
