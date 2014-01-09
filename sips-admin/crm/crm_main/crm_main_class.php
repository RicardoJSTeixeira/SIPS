<?php

class crm_main_class {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function get_campanha() {
        $query = "SELECT  campaign_id id,campaign_name name FROM  vicidial_campaigns where active='y'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_bd($campaign_id) {
        $query = "SELECT  list_id id,list_name name from vicidial_lists where active='Y' and campaign_id=? ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($campaign_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_agent() {

        $query = "SELECT user id, full_name name FROM vicidial_users where active='y'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_feedbacks($campaign_id) {
        $query = "select status id, status_name name from ((SELECT status ,status_name FROM vicidial_campaign_statuses where campaign_id=?) union all (SELECT status, status_name FROM vicidial_statuses)) a group by status order by status_name asc";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($campaign_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_campos_dinamicos($campaign_id) {
        $query = "SELECT Name id,Display_name name  FROM vicidial_list_ref where campaign_id =? and active='1' ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($campaign_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_script($campaign_id) {
        $query = "SELECT a.id id,a.id_script,a.texto name,a.values_text,a.type,a.tag   FROM script_dinamico a  left join script_assoc b on a.id_script=b.id_script where b.id_camp_linha =? and a.type not in ('legend','textfield','datepicker','scheduler','ipl','pagination','tableinput')";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($campaign_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_script_individual($id) {
        $query = "SELECT id,tag,id_script,type,texto,placeholder,values_text FROM `script_dinamico` WHERE id=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $js = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "type" => $row["type"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "values_text" => json_decode($row["values_text"]));
        return $js;
    }

    public function get_info_client($data_inicio, $data_fim, $campanha, $bd, $agente, $feedback, $cd, $script_info, $lead_id, $phone_number, $type_search) {
        $js['aaData'] = array();
        $variables = array();
        $join = "";
        $group = "";
        $script_fields = "";
        if ($lead_id != "" && $lead_id != null) {
            $query = "
            SELECT lead_id,first_name, phone_number, address1 ,last_local_call_time 
            FROM   vicidial_list
            WHERE  lead_id= ?";
            $variables = array();
            $variables[] = $lead_id;
        } elseif ($phone_number != "" && $phone_number != null) {
            $query = "
            SELECT lead_id,first_name, phone_number, address1 ,last_local_call_time 
            FROM   vicidial_list
            WHERE   phone_number= ? or address3=? or alt_phone=? group by lead_id";
            $variables = array();
            $variables[] = $phone_number;
            $variables[] = $phone_number;
            $variables[] = $phone_number;
        } else {
            if (!empty($bd)) {
                $cbd = " a.list_id =?";
                $variables[] = $bd;
            } else {
                $query = "SELECT list_id FROM vicidial_lists WHERE campaign_id=:campanha";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(":campanha" => $campanha));
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $variables[] = $row["list_id"];
                    $temp[] = $row["list_id"];
                }
                if (empty($temp)) {
                    echo json_encode($js);
                    exit();
                }
                for ($index = 0; $index < count($temp); $index++) {
                    $temp1.="?,";
                }
                $cbd = " a.list_id in(" . rtrim($temp1, ",") . ")";
            }
            $where = $cbd;
//--------------------------------------------------------------------------------DATAS
            if (!empty($data_inicio) && !empty($data_fim)) {

                if ($type_search == "last_call") {
                    $where = $where . " and a.last_local_call_time between ? and ?";
                } else {
                    $where = $where . " and a.entry_date between ? and ?";
                }

                $variables[] = $data_inicio . " 00:00:00";
                $variables[] = $data_fim . " 23:59:59";
            }
//----------------------------------------------------------------------AGENTES
            if (!empty($agente)) {
                $where = $where . " and a.user=?";
                $variables[] = $agente;
            }
//----------------------------------------------------------------------FEEDBACKS
            if (!empty($feedback)) {
                $where = $where . " and a.status=?";
                $variables[] = $feedback;
            }
            //----------------------------------------------------------------------CAMPOS DINAMICOS
            if (!empty($cd)) {
                $cd = json_decode($cd);
                $ao = " and ";
                foreach ($cd as $cp) {
                    $where = $where . $ao . $cp->name . " = ?";
                    $variables[] = $cp->value;
                    $ao = " or ";
                }
            }
            //----------------------------------------------------------------------SCRIPT
            if (!empty($script_info)) {
                $script_info = json_decode($script_info);
                if (sizeof($script_info) > 0) {
                    $join = "left join script_result b on b.lead_id=a.lead_id";
                    $where = $where . " and  b.campaign_id=? and ";
                    $variables[] = $campanha;
                    $ao = "";
                    foreach ($script_info as $cp) {
                        $aaa = split(";", $cp->value);
                        if (isset($aaa[1])) {
                            $script_fields = $script_fields . $ao . " (b.tag_elemento=? and b.valor=? and b.param_1=?)";
                            $variables[] = $cp->name;
                            $variables[] = $aaa[1];
                            $variables[] = $aaa[0];
                            $ao = " or ";
                        } else {
                            $script_fields = $script_fields . $ao . " (b.tag_elemento=? and b.valor=?)";
                            $variables[] = $cp->name;
                            $variables[] = $cp->value;
                            $ao = " or ";
                        }
                    }
            
                }
            }
            $query = "select a.lead_id,a.first_name,a.phone_number, a.address1 ,a.last_local_call_time  from vicidial_list a $join where $where ($script_fields)  $group limit 20000 ";
         }
      

        $stmt = $this->db->prepare($query);
        $stmt->execute($variables);

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[4] = $row[4] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span></div>";
            $js['aaData'][] = $row;
        }
        return $js;
    }

    public function get_info_calls($data_inicio, $data_fim, $campanha, $bd, $agente, $feedback, $cd, $script_info, $lead_id, $phone_number) {
        $js['aaData'] = array();
        $variables = array();
        $join = "";
        $group = "";
        $script_fields = "";
        if ($lead_id != "" && $lead_id != null) {
            $query = "
            SELECT a.lead_id,c.first_name,  a.phone_number,a.call_date 
            FROM   vicidial_log a
            left join vicidial_list c on c.lead_id=a.lead_id
            WHERE  a.lead_id= ?";
            $variables = array();
            $variables[] = $lead_id;
        } elseif ($phone_number != "" && $phone_number != null) {
            $query = "
            SELECT a.lead_id,c.first_name,  a.phone_number,a.call_date 
            FROM   vicidial_log a
              left join vicidial_list c on c.lead_id=a.lead_id
            WHERE   c.phone_number= ? or c.address3=? or c.alt_phone=? ";
            $variables = array();
            $variables[] = $phone_number;
            $variables[] = $phone_number;
            $variables[] = $phone_number;
        } else {
            if (!empty($bd)) {
                $cbd = " a.list_id =?";
                $variables[] = $bd;
            } else {
                $query = "SELECT list_id FROM vicidial_lists WHERE campaign_id=:campanha";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(":campanha" => $campanha));
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $variables[] = $row["list_id"];
                    $temp[] = $row["list_id"];
                }
                if (empty($temp)) {
                    echo json_encode($js);
                    exit();
                }
                for ($index = 0; $index < count($temp); $index++) {
                    $temp1.="?,";
                }
                $cbd = " a.list_id in(" . rtrim($temp1, ",") . ")";
            }
            $where = $cbd;
//--------------------------------------------------------------------------------DATAS
            if (!empty($data_inicio) && !empty($data_fim)) {
                $where = $where . " and a.call_date between ? and ?";
                $variables[] = $data_inicio . " 00:00:00";
                $variables[] = $data_fim . " 23:59:59";
            }
//----------------------------------------------------------------------AGENTES
            if (!empty($agente)) {
                $where = $where . " and a.user=?";
                $variables[] = $agente;
            }
//----------------------------------------------------------------------FEEDBACKS
            if (!empty($feedback)) {
                $where = $where . " and a.status=?";
                $variables[] = $feedback;
            }
            //----------------------------------------------------------------------CAMPOS DINAMICOS
            if (!empty($cd)) {
                $cd = json_decode($cd);
                $ao = " and ";
                foreach ($cd as $cp) {
                    $where = $where . $ao . $cp->name . "=?";
                    $variables[] = $cp->value;
                    $ao = " or ";
                }
            }
            //----------------------------------------------------------------------SCRIPT
            if (!empty($script_info)) {
                $script_info = json_decode($script_info);
                if (sizeof($script_info) > 0) {
                    $join = "left join script_result b on b.unique_id=a.uniqueid";
                    $where = $where . " and  b.campaign_id=?";
                    $variables[] = $campanha;
                    $ao = " and ";
                    foreach ($script_info as $cp) {
                        $aaa = split(";", $cp->value);
                        if (isset($aaa[1])) {

                            $where = $where . $ao . " (b.tag_elemento=? and b.valor=? and b.param_1=?)";
                            $variables[] = $cp->name;
                            $variables[] = $aaa[1];
                            $variables[] = $aaa[0];
                            $ao = " or ";
                        } else {

                            $where = $where . $ao . " (b.tag_elemento=? and b.valor=?)";
                            $variables[] = $cp->name;
                            $variables[] = $cp->value;
                            $ao = " or ";
                        }
                    }
                    $group = " group by a.lead_id";
                }
            }

            $query = "select a.lead_id,c.first_name,  a.phone_number,a.call_date  from vicidial_log a left join vicidial_list c on c.lead_id=a.lead_id  $join where $where $script_fields $group limit 20000 ";
        }


        $stmt = $this->db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[3] = $row[3] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span></div>";
            $js['aaData'][] = $row;
        }
        return $js;
    }

}
