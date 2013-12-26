<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require("../lib/db.php");

require("../lib/user.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);

$user->confirm_login();


$variables=array();
switch ($action) {
    case 'campanha':
        $query = "SELECT  campaign_id id,campaign_name name FROM  vicidial_campaigns where active='y'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $js = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($js);
        break;


    case 'bd':


        $query = "SELECT  list_id id,list_name name from vicidial_lists where active='Y' and campaign_id=? ";
        $stmt = $db->prepare($query);
        $stmt->execute(array($campaign_id));
        $js = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($js);
        break;


    case 'agent':
        $query = "SELECT user id, full_name name FROM vicidial_users where active='y'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $js = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($js);
        break;


    case "feedbacks":
        $query = "select status id, status_name name from ((SELECT status ,status_name FROM vicidial_campaign_statuses where campaign_id=?) union all (SELECT status, status_name FROM vicidial_statuses)) a group by status order by status_name asc";
        $stmt = $db->prepare($query);
        $stmt->execute(array($campaign_id));

        $js = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($js);
        break;


    case "campos_dinamicos":
        $query = "SELECT Name id,Display_name name  FROM vicidial_list_ref where campaign_id =? and active='1' ";

        $stmt = $db->prepare($query);
        $stmt->execute(array($campaign_id));

        $js = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($js);
        break;



    case "script":
        $query = "SELECT a.id id,a.id_script,a.texto name,a.values_text,a.type,a.tag   FROM script_dinamico a  left join script_assoc b on a.id_script=b.id_script where b.id_camp_linha =? and a.type not in ('legend','textfield','datepicker','scheduler','ipl','pagination','tableinput')";

        $stmt = $db->prepare($query);
        $stmt->execute(array($campaign_id));

        $js = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($js);
        break;

    case "get_script_individual":
        $query = "SELECT id,tag,id_script,type,texto,placeholder,values_text FROM `script_dinamico` WHERE id=?";
        $stmt = $db->prepare($query);
        $stmt->execute(array($id));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        $js = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "type" => $row["type"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "values_text" => json_decode($row["values_text"]));
        echo json_encode($js);
        break;

    case "get_info":
        $js['aaData'] = array();

        $variables = array();
        $join = "";
//----------------------------------------------------------------------- CAMPANHA E BASE DE DADOS
        if (!empty($bd)) {

            $cbd = " list_id =?";
            $variables[] = $bd;
        } else {
            $query = "SELECT list_id FROM vicidial_lists WHERE campaign_id=:campanha";
            $stmt = $db->prepare($query);
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

            $cbd = " list_id in(" . rtrim($temp1, ",") . ")";
        }


        $where = $cbd;


//--------------------------------------------------------------------------------DATAS
        if (!empty($data_inicio) && !empty($data_fim)) {
            $where = $where . " and last_local_call_time between ? and ?";
            $variables[] = $data_inicio;
            $variables[] = $data_fim;
        }


//----------------------------------------------------------------------AGENTES
        if (!empty($agente)) {
            $where = $where . " and user=?";
            $variables[] = $agente;
        }


//----------------------------------------------------------------------FEEDBACKS
        if (!empty($feedback)) {
            $where = $where . " and status=?";
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
        if (!empty($script)) {
            $script = json_decode($script);
            if (sizeof($script) > 0) {
                $join = "left join script_result b on b.lead_id=a.lead_id";
                $where = $where . " and  b.campaign_id=?";
                $variables[] = $campanha;
                $ao = " and ";
                foreach ($script as $cp) {

                    $aaa = split(";", $cp->value);
                    if (isset($aaa[1])) {
                        $where = $where . $ao . " b.tag_elemento=? and b.valor=? and b.param_1=?";
                        $variables[] = $cp->name;
                        $variables[] = $aaa[1];
                        $variables[] = $aaa[0];
                        
                        $ao = " or ";
                    } else {

                        $where = $where . $ao . " b.tag_elemento=? and b.valor=?";
                        $variables[] = $cp->name;
                        $variables[] = $cp->value;
                        $ao = " or ";
                    }
                }
            }
        }

        $query = "select a.lead_id,a.first_name,a.phone_number, a.address1 ,a.last_local_call_time  from vicidial_list a $join where $where group by a.lead_id ";

        $stmt = $db->prepare($query);
        $stmt->execute($variables);

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[4] = $row[4] . "<div class='view-button' ><span data-lead_id='$row[0]' class='btn btn-mini ver_cliente' ><i class='icon-edit'></i>Ver</span></div>";
            $js['aaData'][] = $row;
        }
        echo json_encode($js);
        break;
}
?>
