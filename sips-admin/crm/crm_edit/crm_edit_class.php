<?php

class crm_edit_class {

    protected $db;

    public function __construct($db, $user_level) {
        $this->db = $db;
        $this->user_level = $user_level;
    }

    public function get_lead_info($lead_id) {
        $js = array();
        $query = "SELECT vdlist.lead_id,vdlist.phone_number,vdlist.entry_date AS data_load ,vdc.campaign_id, vdc.campaign_name,vdlists.list_name,vdlists.list_id
             FROM vicidial_list vdlist 
                  left join vicidial_lists vdlists on vdlists.list_id=vdlist.list_id
                  left join vicidial_campaigns vdc on vdc.campaign_id=vdlists.campaign_id
             WHERE vdlist.lead_id=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($lead_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $js = $row;
        $query = "SELECT vdlog.uniqueid,vdlog.call_date,vdc.campaign_name,vdc.campaign_id,vdlists.list_name,vdlog.status,vduser.full_name as user_name from
(Select a.lead_id,a.list_id,a.user,a.uniqueid,a.call_date,a.status from vicidial_log a where a.lead_id=? union all   Select b.lead_id,b.list_id,b.user,b.uniqueid,b.call_date,b.status from vicidial_log_archive b  where b.lead_id=? ) vdlog
                    left join vicidial_users vduser on vduser.user=vdlog.user
                    left join vicidial_lists vdlists on vdlists.list_id=vdlog.list_id
                    left JOIN vicidial_campaigns vdc ON vdc.campaign_id=vdlists.campaign_id
                    where vdlog.lead_id=?
                    group by vdlog.uniqueid 
                    order by vdlog.call_date desc
                    limit 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($lead_id, $lead_id, $lead_id));
        $calls_outbound = $stmt->fetch(PDO::FETCH_ASSOC);
        $query = "SELECT vdclog.uniqueid,vdclog.call_date,vdclog.campaign_id, vdig.group_name,vdclog.status,vduser.full_name as user_name from 
            (Select a.lead_id,a.campaign_id,a.list_id,a.user,a.uniqueid,a.call_date,a.status from vicidial_closer_log a where a.lead_id=? union all   Select b.lead_id,b.campaign_id,b.list_id,b.user,b.uniqueid,b.call_date,b.status from vicidial_closer_log_archive b  where b.lead_id=? ) vdclog
                    left join vicidial_users vduser on vduser.user=vdclog.user
                    left join vicidial_list vdlist on vdlist.lead_id=vdclog.lead_id
                    left JOIN vicidial_inbound_groups vdig ON vdig.group_id=vdclog.campaign_id
                    where vdclog.lead_id=?
                    group by vdclog.uniqueid 
                    order by vdclog.call_date desc
                    limit 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($lead_id, $lead_id, $lead_id));
        $calls_inbound = $stmt->fetch(PDO::FETCH_ASSOC);



        $js["data_last"] = $calls_inbound["call_date"];
        $js["campaign_id"] = $calls_inbound["campaign_id"];
        $js["user_name"] = $calls_inbound["user_name"];
        $js["status"] = $calls_inbound["status"];

        //get count calls
        $query = "SELECT uniqueid from (select a.uniqueid from vicidial_log a where a.lead_id=?  
                     union all select b.uniqueid from vicidial_log_archive b where b.lead_id=?) 
                     calls";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($lead_id, $lead_id));
        $row1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $temp = array();
        foreach ($row1 as $value) {
            $temp[$value["uniqueid"]] = $value;
        }
        $query = "SELECT uniqueid from (select  a.uniqueid from vicidial_closer_log a where a.lead_id=?  
                     union all select b.uniqueid from vicidial_closer_log_archive b where b.lead_id=?) 
                     calls";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($lead_id, $lead_id));
        $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row2 as $value) {
            $temp[$value["uniqueid"]] = $value;
        }
        // to avoid duplicates
        $js["called_count"] = count($temp);



        if (strtotime($calls_outbound["call_date"]) >= strtotime($calls_inbound["call_date"])) {
            $query = "select status_name from (select status,status_name from vicidial_statuses a union all select status,status_name from vicidial_campaign_statuses b) statuses where status=?";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($calls_outbound["status"]));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $js["status_name"] = $row["status_name"];
            $js["data_last"] = $calls_outbound["call_date"];
            $js["campaign_id"] = $calls_outbound["campaign_id"];
            $js["user_name"] = $calls_outbound["user_name"];
            $js["status"] = $calls_outbound["status"];
        } else {
            $query = "select status_name from (select status,status_name from vicidial_statuses a union all select status,status_name from vicidial_campaign_statuses b) statuses where status=?";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($calls_inbound["status"]));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $js["status_name"] = $row["status_name"];
            $js["data_last"] = $calls_inbound["call_date"];
            $js["campaign_id"] = $calls_inbound["campaign_id"];
            $js["user_name"] = $calls_inbound["user_name"];
            $js["status"] = $calls_inbound["status"];
        }
        if (!isset($js["campaign_name"]))
            $js["campaign_name"] = "Sem Campanha";
        if (!isset($js["list_name"]))
            $js["list_name"] = "Sem Base de dados";
        if (!isset($js["user_name"]))
            $js["user_name"] = "Sem Agente";
        if (!isset($js["status"]))
            $js["status"] = "No_Status";
        if (!isset($js["status_name"]))
            $js["status_name"] = "Sem Feedback";
        return $js;
    }

    public function get_dynamic_fields($lead_id, $campaign_id, $list_id) {

        $dfields = array();
        $query = "SELECT Name,Display_name   FROM vicidial_list_ref WHERE campaign_id=:campaign_id AND active=1 Order by field_order ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dfields[$row["Name"]] = array("display_name" => $row["Display_name"], "name" => $row["Name"], "value" => "");
        }
        if (!count($dfields)) {
            $query = "SELECT vlr.Name,vlr.Display_name   FROM vicidial_list_ref vlr left join vicidial_lists vl on vl.campaign_id=vlr.campaign_id where vl.list_id=? and vlr.active=1 Order by vlr.field_order ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($list_id));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dfields[$row["Name"]] = array("display_name" => $row["Display_name"], "name" => $row["Name"], "value" => "");
            }
            if (!count($dfields)) {
                $dfields["FIRST_NAME"] = array("display_name" => "Nome", "name" => "FIRST_NAME", "value" => "");
                $dfields["PHONE_NUMBER"] = array("display_name" => "Telefone", "name" => "PHONE_NUMBER", "value" => "");
                $dfields["ADDRESS3"] = array("display_name" => "Telemóvel", "name" => "ADDRESS3", "value" => "");
                $dfields["ALT_PHONE"] = array("display_name" => "Telefone Alternativo", "name" => "ALT_PHONE", "value" => "");
                $dfields["ADDRESS1"] = array("display_name" => "Morada", "name" => "ADDRESS1", "value" => "");
                $dfields["POSTAL_CODE"] = array("display_name" => "Código Postal", "name" => "POSTAL_CODE", "value" => "");
                $dfields["EMAIL"] = array("display_name" => "E-mail", "name" => "EMAIL", "value" => "");
                $dfields["COMMENTS"] = array("display_name" => "Comentários", "name" => "COMMENTS", "value" => "");
            }
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
        }
        return $dfields;
    }

    public function get_feedbacks($campaign_id, $list_id) {

         $feedback_options = array();
       //FEEDBACKS DE SISTEMA
        $query = "SELECT status,status_name,sale FROM vicidial_statuses";
        $stmt = $this->db->prepare($query);
        $stmt->execute(); 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $feedback_options [] = array("status" => $row["status"], "status_name" => $row["status_name"], "sale" => $row["sale"]);
        }
        //FEEDBACKS DE CAMPANHA
        $query = "SELECT status,status_name,sale from vicidial_campaign_statuses WHERE campaign_id=:campaign_id AND scheduled_callback!=1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count = 1;
            $feedback_options [] = array("status" => $row["status"], "status_name" => $row["status_name"], "sale" => $row["sale"]);
        }
        if ($count == 0) {
            $query = "SELECT vcs.status,vcs.status_name,vcs.sale from vicidial_campaign_statuses vcs left join vicidial_lists vl on vl.campaign_id=vcs.campaign_id WHERE vl.list_id=? AND vcs.scheduled_callback!=1";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($list_id));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $feedback_options [] = array("status" => $row["status"], "status_name" => $row["status_name"], "sale" => $row["sale"]);
            }
        }

        return $feedback_options;
    }

    // VER A CENA DOS USERS QUANDO SAO VDCL E VDAD---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_calls_outbound($lead_id) {
        $output = array();
        $js1 = array();
        $js2 = array();
        $query = "SELECT
                        vl.call_date AS data,
                        vl.length_in_sec,
                        'Sem tempo de espera' as queue_seconds,
                        'Sem fila de espera' as queue_position,
                        vl.term_reason,
                        vl.phone_number,
                        vu.full_name,
                        vstatus.status_name,
                        vc.campaign_name,
                        vls.list_name,
                        vl.comments,
                        '-------------' as type,
                        
                        vl.uniqueid as uniqueid,
                        vl.campaign_id as campaign_id
                FROM 
                        vicidial_log vl
                      
                left JOIN vicidial_users vu ON vl.user=vu.user
                left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                        vl.lead_id=:lead_id
                        group by vl.uniqueid;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[11] = "Outbound";
            $output[] = $row;
        }
        $query = "SELECT
                        vl.call_date AS data,
                        vl.length_in_sec,
                        'Sem tempo de espera' as queue_seconds,
                        'Sem fila de espera' as queue_position,
                        vl.term_reason,
                        vl.phone_number,
                        vu.full_name,
                        vstatus.status_name,
                        vc.campaign_name,
                        vls.list_name,
                        vl.comments,
                        '-------------' as type,
                        
                        vl.uniqueid as uniqueid,
                        vl.campaign_id as campaign_id
                FROM 
                        vicidial_log_archive vl
                        
                left JOIN vicidial_users vu ON vl.user=vu.user
                left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
                left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
                        left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
                WHERE 
                        vl.lead_id=:lead_id
                        group by vl.uniqueid;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[11] = "Outbound";
            $output[] = $row;
        }
        //REMOVE DUPLICATES
        $temp = array();
        foreach ($output as $value) {
            $temp[$value["uniqueid"]] = $value;
        }
        return $temp;
    }

    public function get_calls_inbound($lead_id) {
        $output = array();
        $js1 = array();
        $js2 = array();
        $query = "SELECT
                        vcl.call_date AS data,
                        vcl.length_in_sec,
                        vcl.queue_seconds as queue_seconds,
                        vcl.queue_position,
                        vcl.term_reason,
                        vcl.phone_number,
                        vu.full_name,
                        vstatus.status_name,
                        vc.group_name,
                        'Sem Base de Dados' as list_name,
                        vcl.comments,
                        '-------------' as type,
                        vcl.uniqueid as uniqueid,
                        vcl.campaign_id as campaign_id
                FROM 
                        vicidial_closer_log vcl
                        
                left JOIN vicidial_users vu ON vcl.user=vu.user
                left JOIN vicidial_inbound_groups vc ON vcl.campaign_id=vc.group_id 
                left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vcl.status
                WHERE 
                                   vcl.lead_id=:lead_id
                                    group by vcl.uniqueid";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[11] = "Inbound";
            $output[] = $row;
        }
        $query = "SELECT
                        vcl.call_date AS data,
                        vcl.length_in_sec,
                        vcl.queue_seconds as queue_seconds,
                        vcl.queue_position,
                        vcl.term_reason,
                        vcl.phone_number,
                        vu.full_name,
                        vstatus.status_name,
                        vc.group_name,
                        'Sem Base de Dados' as list_name,
                        vcl.comments,
                        '-------------' as type,
                        vcl.uniqueid as uniqueid,
                        vcl.campaign_id as campaign_id
                FROM 
                        vicidial_closer_log_archive vcl
                        
                left JOIN vicidial_users vu ON vcl.user=vu.user
                left JOIN vicidial_inbound_groups vc ON vcl.campaign_id=vc.group_id 
                left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vcl.status
                WHERE 
                                   vcl.lead_id=:lead_id
                                    group by vcl.uniqueid";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[11] = "Inbound";
            $output[] = $row;
        }

        //REMOVE DUPLICATES
        $temp = array();
        foreach ($output as $value) {
            $temp[$value["uniqueid"]] = $value;
        }
        return $temp;
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
                                location
                        FROM 
                                recording_log rl
                        INNER JOIN vicidial_users vu ON rl.user=vu.user
                        WHERE 
                                lead_id=:lead_id;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {

            $output['aaData'][] = $row;
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

        $query = "Update vicidial_list set validation=:validation where lead_id=:lead_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":validation" => $vl_validation, ":lead_id" => $lead_id));

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
