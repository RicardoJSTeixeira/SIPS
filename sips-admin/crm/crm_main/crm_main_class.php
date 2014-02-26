<?php

class crm_main_class {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function get_bd($campaign_id) {
        $query = "SELECT  list_id id,list_name name from vicidial_lists where active='Y' and campaign_id=? ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($campaign_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_linha_inbound() {
        $query = "SELECT  group_id id,group_name name from vicidial_inbound_groups where active='Y'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
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

    function get_status_name($list, $status) {

        $query1 = "select a.campaign_id from vicidial_campaigns a inner join vicidial_lists b on a.campaign_id=b.campaign_id where b.list_id=?";
        $stmt = $this->db->prepare($query1);
        $stmt->execute(array($list));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $campaign = $row["campaign_id"];


        $query1 = "select * from (select a.status,a.status_name from vicidial_statuses a union all select b.status,b.status_name from vicidial_campaign_statuses b where b.campaign_id=?) statuses where statuses.status=? ";
        $stmt = $this->db->prepare($query1);
        $stmt->execute(array($campaign, $status));
        $row_status = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row_status["status_name"];
    }

    function get_last_call($lead_id) {
        $query = "
            SELECT * from (select a.call_date,a.status,a.list_id,a.length_in_sec from vicidial_log a where a.lead_id=? union all select b.call_date,b.status,b.list_id,b.length_in_sec from vicidial_log_archive b where b.lead_id=? union all
            select c.call_date,c.status,c.list_id,c.length_in_sec from vicidial_closer_log c where c.lead_id=? union all select d.call_date,d.status,d.list_id,d.length_in_sec from vicidial_closer_log_archive d where d.lead_id=?) calls order by call_date desc limit 1 ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($lead_id, $lead_id, $lead_id, $lead_id));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_info_client($data_inicio, $data_fim, $campanha, $linha_inbound, $campaign_linha_inbound, $bd, $agente, $feedback, $cd, $script_info, $lead_id, $phone_number, $type_search) {
        $js['aaData'] = array();
        $variables = array();
        $join = "";
        $script_fields = "";
        if ($lead_id != "" && $lead_id != null) {
            $query = "
            SELECT lead_id,first_name,phone_number,status,'last_call_date',list_id
            FROM   vicidial_list  
            WHERE  lead_id= ?";
            $variables[] = $lead_id;
            $stmt = $this->db->prepare($query);
            $stmt->execute($variables);
            if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $temp = $this->get_last_call($row[0]);
                if (!$temp) {
                    $row[3] = $this->get_status_name($row[5], $row[3]);
                    $row[4] = "Sem chamada";
                } else {
                    $row[3] = $this->get_status_name($temp["list_id"], $temp["status"]);
                    $row[4] = $temp["call_date"];
                }
                $row[4] = $row[4] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span>"
                        . "<span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Criar Marcação</span></div>";
                $js['aaData'][] = $row;
            }
            return $js;
        } elseif ($phone_number != "" && $phone_number != null) {
            $query = "
            SELECT lead_id,first_name, phone_number, status,'last_call_date' 
            FROM   vicidial_list
            WHERE   phone_number= ? or address3=? or alt_phone=?  ";
            $variables[] = $phone_number;
            $variables[] = $phone_number;
            $variables[] = $phone_number;
            $stmt = $this->db->prepare($query);
            $stmt->execute($variables);
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $temp = $this->get_last_call($row[0]);
                if (!$temp) {
                    $row[3] = $this->get_status_name($row[5], $row[3]);
                    $row[4] = "Sem chamada";
                } else {
                    $row[3] = $this->get_status_name($temp["list_id"], $temp["status"]);
                    $row[4] = $temp["call_date"];
                }
                $row[4] = $row[4] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span>"
                        . "<span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Criar Marcação</span></div>";
                $js['aaData'][] = $row;
            }
            return $js;
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
                            $ao = " and ";
                        } else {
                            $script_fields = $script_fields . $ao . " (b.tag_elemento=? and b.valor=?)";
                            $variables[] = $cp->name;
                            $variables[] = $cp->value;
                            $ao = " and ";
                        }
                    }
                    $script_fields = "(" . $script_fields . ")";
                }
            }
            $query = "select a.lead_id,a.first_name,a.phone_number,a.status,a.last_local_call_time,a.list_id  from vicidial_list a"
                    . " where $where $script_fields group by a.lead_id   limit 20000";
            $stmt2 = $this->db->prepare($query);
            $stmt2->execute($variables);
            while ($row = $stmt2->fetch(PDO::FETCH_NUM)) {
                $clients[$row[0]] = $row;
            }
            if (isset($clients)) {
                foreach ($clients as $value) {
                    $value[3] = $this->get_status_name($value[5], $value[3]);
                    $value[4] = $value[4] . "<div class='view-button' ><span data-lead_id='$value[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span>"
                            . "<span class='btn btn-mini criar_marcacao' data-lead_id='$value[0]'><i class='icon-edit'></i>Criar Marcação</span></div>";
                    $js['aaData'][] = $value;
                }
                return $js;
            } else {
                $js['aaData'] = array();
                return $js;
            }
        }
        return false;
    }

    public function get_info_calls($data_inicio, $data_fim, $campanha, $linha_inbound, $campaign_linha_inbound, $bd, $agente, $feedback, $cd, $script_info, $lead_id, $phone_number) {
        $js['aaData'] = array();
        $variables = array();
        $join = "";

        $script_fields = "";

        $table = "";

        if ($campaign_linha_inbound == 1)
            $table = "(select a.uniqueid,a.list_id, a.lead_id,a.phone_number,a.length_in_sec,a.call_date,a.user,a.status from vicidial_log a union all select b.uniqueid,b.list_id,b.lead_id,b.phone_number,b.length_in_sec,b.call_date,b.user,b.status from vicidial_log_archive b) calls ";
        else
            $table = "(select a.uniqueid,a.list_id,a.campaign_id,a.lead_id,a.phone_number,a.length_in_sec,a.call_date,a.user,a.status from vicidial_closer_log a union all select b.uniqueid,b.list_id,b.campaign_id,b.lead_id,b.phone_number,b.length_in_sec,b.call_date,b.user,b.status from vicidial_closer_log_archive b) calls";



        if ($lead_id != "" && $lead_id != null) {
            $query = "
            SELECT c.lead_id,c.first_name,  calls.phone_number,calls.status,calls.length_in_sec,calls.call_date ,calls.list_id
            FROM   $table 
            left join vicidial_list c on c.lead_id=calls.lead_id
            WHERE  calls.lead_id= ? group by calls.uniqueid";

            $variables[] = $lead_id;

            $stmt = $this->db->prepare($query);
            $stmt->execute($variables);
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
   
                $row[3] = $this->get_status_name($row[6], $row[3]);
                $row[4] = gmdate("H:i:s", $row[4]);

                $row[5] = $row[5] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span>"
                        . "<span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Criar Marcação</span></div>";
                $js['aaData'][] = $row;
            }
            return $js;
        } elseif ($phone_number != "" && $phone_number != null) {
            $query = "
            SELECT c.lead_id,c.first_name, calls.phone_number,calls.status,calls.length_in_sec,calls.call_date ,calls.list_id
            FROM    $table 
              left join vicidial_list c on c.lead_id=calls.lead_id
            WHERE   c.phone_number= ? or c.address3=? or c.alt_phone=? group by calls.uniqueid ";

            $variables[] = $phone_number;
            $variables[] = $phone_number;
            $variables[] = $phone_number;
            $stmt = $this->db->prepare($query);
            $stmt->execute($variables);
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
             
                $row[3] = $this->get_status_name($row[6], $row[3]);
                $row[4] = gmdate("H:i:s", $row[4]);
                $row[5] = $row[5] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span>"
                        . "<span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Criar Marcação</span></div>";
                $js['aaData'][] = $row;
            }
            return $js;
        } else {



            if ($campaign_linha_inbound == 1) {
                if (!empty($bd)) {
                    $cbd = " c.list_id =?";
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
                    $cbd = " c.list_id in(" . rtrim($temp1, ",") . ")";
                }
            } else {
                $cbd = " calls.campaign_id=? ";
                $variables[] = $linha_inbound;
            }
            $where = $cbd;
//--------------------------------------------------------------------------------DATAS
            if (!empty($data_inicio) && !empty($data_fim)) {
                $where = $where . " and calls.call_date between ? and ?";
                $variables[] = $data_inicio . " 00:00:00";
                $variables[] = $data_fim . " 23:59:59";
            }
//----------------------------------------------------------------------AGENTES
            if (!empty($agente)) {
                $where = $where . " and calls.user=?";
                $variables[] = $agente;
            }
//----------------------------------------------------------------------FEEDBACKS
            if (!empty($feedback)) {
                $where = $where . " and calls.status=?";
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
                    if ($campaign_linha_inbound == 1)
                        $join = "left join script_result b on b.unique_id=a.uniqueid";
                    else
                        $join = "left join script_result b on b.unique_id=a.closecallid";
                    $where = $where . " and  b.campaign_id=?";
                    $variables[] = $campanha;
                    $ao = " and ";
                    foreach ($script_info as $cp) {
                        $aaa = split(";", $cp->value);
                        if (isset($aaa[1])) {
                            $script_fields = $script_fields . $ao . " (b.tag_elemento=? and b.valor=? and b.param_1=?)";
                            $variables[] = $cp->name;
                            $variables[] = $aaa[1];
                            $variables[] = $aaa[0];
                            $ao = " and ";
                        } else {
                            $script_fields = $script_fields . $ao . " (b.tag_elemento=? and b.valor=?)";
                            $variables[] = $cp->name;
                            $variables[] = $cp->value;
                            $ao = " and ";
                        }
                    }
                    $script_fields = "(" . $script_fields . ")";
                }
            }

            $query = "select calls.lead_id,c.first_name,  calls.phone_number,calls.status,calls.length_in_sec,calls.call_date,calls.list_id  from $table  left join vicidial_list c on c.lead_id=calls.lead_id"
                    . "  $join where $where $script_fields   limit 20000 ";



            $stmt = $this->db->prepare($query);
            $stmt->execute($variables);


            if ($campaign_linha_inbound == 1) {
                 //InBound
                while ($row = $stmt->fetch(PDO::FETCH_NUM)) {

                    $row[3] = $this->get_status_name($row[6], $row[3]);
                    $row[4] = gmdate("H:i:s", $row[4]);
                    $row[5] = $row[5] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span>"
                            . "<span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Criar Marcação</span></div>";
                    $js['aaData'][] = $row;
                }
            } else {
                //OutBound
                while ($row = $stmt->fetch(PDO::FETCH_NUM)) {

                    $row[3] = $this->get_status_name($row[6], $row[3]);
                    $row[4] = gmdate("H:i:s", $row[4]);
                    $row[5] = $row[5] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span>"
                            . "<span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Criar Marcação</span></div>";
                    $js['aaData'][] = $row;
                }
            }





            return $js;
        }
        return false;
    }

}
