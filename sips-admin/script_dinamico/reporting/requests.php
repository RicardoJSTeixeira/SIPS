<?php

require("../../../ini/dbconnect.php");
require("../../../ini/user.php");
require("../../../ini/db.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

error_reporting();
ini_set('display_errors', '1');

header('Content-Disposition: attachment; filename=Report_Script_' . date("Y-m-d_H:i:s") . '.csv');

$user = new mysiblings($db);

$temp = "";
if (!$user->is_all_campaigns) {
    $temp = "and campaign_id in('" . implode("','", $user->allowed_campaigns) . "')";
}
$js = array();
switch ($action) {

    case "check_has_script":
        $query = "SELECT id_script from script_assoc where id_camp_linha=:campaign_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $script_id = $row["id_script"];

        if (isset($script_id)) {
            echo json_encode($script_id);
        } else {
            echo json_encode(array());
        }
        break;

    case "get_template":

//Se 0 vai buscar defaults
        $query = "SELECT id,template from report_order where campaign=:campaign_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $js = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id"], "template" => $row["template"]);
        }
        echo json_encode($js);
        break;

    case "delete_template":
        $query = "Delete from report_order where id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        break;

    case "edit_template":
        $query = "UPDATE `report_order` set `template`=:template WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":template" => $template, ":id" => $id));
        break;
    case "create_template":
        $query = "SELECT Name,Display_name  FROM vicidial_list_ref where campaign_id = :campaign_id and active='1' order by field_order asc";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));

        $js[] = array("id" => "lead_id", "type" => "campo_dinamico", "texto" => "Lead_id do Cliente");


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["Name"], "type" => "campo_dinamico", "texto" => $row["Display_name"]);
        }
        if (!count($js)) {
            $js[] = array("id" => "FIRST_NAME", "type" => "campo_dinamico", "texto" => "Nome");
            $js[] = array("id" => "PHONE_NUMBER", "type" => "campo_dinamico", "texto" => "Telefone");
            $js[] = array("id" => "ADDRESS3", "type" => "campo_dinamico", "texto" => "Telemóvel");
            $js[] = array("id" => "ALT_PHONE", "type" => "campo_dinamico", "texto" => "Telefone Alternativo");
            $js[] = array("id" => "ADDRESS1", "type" => "campo_dinamico", "texto" => "Morada");
            $js[] = array("id" => "POSTAL_CODE", "type" => "campo_dinamico", "texto" => "Código Postal");
            $js[] = array("id" => "EMAIL", "type" => "campo_dinamico", "texto" => "EMAIL");
            $js[] = array("id" => "COMMENTS", "type" => "campo_dinamico", "texto" => "Comentários");
        }


        $query = "SELECT a.tag,a.type,a.texto  FROM `script_dinamico` a left join script_dinamico_pages b on b.id=a.id_page  where type not in ('pagination','textfield','scheduler','legend','button','ipl')  and a.id_script=:script_id order by b.pos,a.ordem asc ";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":script_id" => $script_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["tag"], "type" => $row["type"], "texto" => $row["texto"]);
        }

        $query = "INSERT INTO `report_order`(`id`, `elements`, `campaign`, `template`) VALUES (NULL, :js,:campaign_id,:template)";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":js" => json_encode($js), ":campaign_id" => $campaign_id, ":template" => $template));
        break;

    case "get_elements_by_template":
        $query = "SELECT elements from report_order where id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $js = json_decode($row["elements"]);

        echo json_encode($js);
        break;

    case "get_select_options":
        $js = array("campanha" => array(), "bd" => array(), "linha_inbound" => array());

        $js["campanha"] = $user->get_campaigns();
        $query = "SELECT vl.list_id,vl.list_name,vl.campaign_id,vc.campaign_name FROM vicidial_lists vl left join vicidial_campaigns vc on vc.campaign_id=vl.campaign_id  where vl.active='Y' order by vc.campaign_name asc";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js["bd"][] = array("id" => $row["list_id"], "name" => $row["list_name"], "campaign_id" => $row["campaign_id"], "campaign_name" => $row["campaign_name"]);
        }
        $query = "SELECT group_id,group_name FROM vicidial_inbound_groups where active='Y'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js["linha_inbound"][] = array("id" => $row["group_id"], "name" => $row["group_name"]);
        }
        echo json_encode($js);
        break;

    case "update_elements_order":
        $query = "update report_order set elements=:elements  where id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id, ":elements" => json_encode($elements)));
        break;







// por filtros pra feedbacks e fazer inbound
    case "report_outbound":
        ini_set('memory_limit', '-1');
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');

//GET ELEMENTS
        $query = "SELECT elements from report_order where id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $field_data));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $field_data = json_decode($row["elements"]);

//GET ID SCRIPT
        $query = "SELECT id_script from script_assoc where id_camp_linha=:campaign_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_script = $row["id_script"];


// MAX TRIES RECYCLE
        $recycle = array();
        $query = "select status,attempt_maximum from vicidial_lead_recycle where campaign_id=:campaign_id and active='Y'";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recycle[$row["status"]] = $row["attempt_maximum"];
        }

//LIST/BASES DE DADOS
        if (!isset($list_id)) {
            if ($only_active_db == false) {
                $onlyActive = " and active='Y'";
            }
            $query = "SELECT list_id from vicidial_lists where campaign_id=:campaign_id  and visible='1' $onlyActive";
            $stmt = $db->prepare($query);
            $stmt->execute(array(":campaign_id" => $campaign_id));

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $list_id[] = $row["list_id"];
            }
            if (!isset($list_id)) {
                echo "Não ha base de dados activa para esta campanha";
                exit;
            }
        }
//NOME DA CAMPANHA
        $query = "SELECT campaign_name from vicidial_campaigns where campaign_id=:campaign_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $campaign_name = $row["campaign_name"];

//GET TITLES-----------------GET TITLES---------------GET TITLES---------------GET TITLES---------------GET TITLES-------------------GET TITLES-------------GET TITLES---------GET TITLES---------GET TITLES---------GET TITLES---------------------------------GET TITLES
        $data_row = array();
        $temp_lead_data = array();
        foreach ($field_data as $key => $value) {
            if ($value->type == "campo_dinamico") {
                $temp_lead_data[] = "a." . $value->id;
                $data_row[$value->id] = $value->texto;
            } else {
                $tags[] = $value->id;
                $data_row["m" . $value->id] = $value->texto;
            }
        }
        if (count($tags)) {
//GET COLUMNS FROM DB
            $query = "SELECT a.tag,a.type,a.texto,a.values_text,a.placeholder "
                    . "FROM `script_dinamico` a "
                    . "left join script_dinamico_pages b "
                    . "on b.id=a.id_page "
                    . "where "
                    . "type not in ('pagination','textfield','scheduler','legend','button','ipl') "
                    . "and a.id_script=:id_script "
                    . "and a.tag in ('" . implode("','", $tags) . "')   ";

            $stmt = $db->prepare($query);
            $stmt->execute(array(":id_script" => $id_script));

//MAKE COLUMNS STRUCTURE WITH DATA PROVIDED EM DATA FROM DB
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $script_values = array();
                if ($row['type'] == "tableradio") {
                    $temp = json_decode($row['values_text']);
                    foreach ($temp as $value) {
                        $script_values["m" . $row['tag'] . $value] = $data_row["m" . $row["tag"]] . "-" . $value;
                    }
                    $data_row = array_slice($data_row, 0, array_search("m" . $row["tag"], array_keys($data_row)), true) + $script_values + array_slice($data_row, array_search("m" . $row["tag"], array_keys($data_row)), count($data_row) - 1, true);
                    unset($data_row["m" . $row["tag"]]);
                } elseif ($row['type'] == "tableinput") {
                    $temp = json_decode($row['values_text']);
                    $temp2 = json_decode($row['placeholder']);
                    foreach ($temp as $value) {
                        foreach ($temp2 as $value2) {
                            $script_values["m" . $row['tag'] . $value . $value2] = $data_row["m" . $row["tag"]] . "-" . $value . "-" . $value2;
                        }
                    }
                    $data_row = array_slice($data_row, 0, array_search("m" . $row["tag"], array_keys($data_row)), true) + $script_values + array_slice($data_row, array_search("m" . $row["tag"], array_keys($data_row)), count($data_row) - 1, true);
                    unset($data_row["m" . $row["tag"]]);
                } else {
                    $data_row["m" . $row["tag"]] = ($row['texto'] == "") ? "Sem titulo" : $data_row["m" . $row["tag"]];
                }
            }

            unset($row);
            unset($temp);
            unset($temp2);
            unset($script_values);
        }
        $data_row = array_merge(array("lead_id" => "ID", "entry_date" => "Data Entrada", "date" => "Data chamada", "name" => "Nome do Script", "full_name" => "Agente", "campaign_name" => "Nome da campanha", "list_name" => "Nome da Base Dados", "status_name" => "Feedback", "length_in_sec" => "Duração Chamada", "max_tries" => "Máximo Tentativas"), $data_row);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




        $titulos = array();
        $titulos = $data_row;
        foreach ($data_row as $key => $value) {
            $data_row[$key] = "";
        }
        $statuses = get_statuses($db, $campaign_id);
        if (count($temp_lead_data))
            $temp_lead_data = " , " . implode(" , ", $temp_lead_data);
        else
            $temp_lead_data = "";


//DATE LIMIT
        if ($allctc == "false") {
            $date_filter_client = " and a.call_date between '$data_inicio 00:00:00' and '$data_fim 23:59:59' ";
            $date_filter_client2 = " and b.call_date between '$data_inicio 00:00:00' and '$data_fim 23:59:59' ";
            $date_filter_script = " and sr.date between '$data_inicio 00:00:00' and '$data_fim 23:59:59' ";
        } else {
            $date_filter_client = "";
            $date_filter_client2 = "";
            $date_filter_script = "";
        }

//ESCREVE TITULOS
        fputcsv($output, $titulos, ";", '"');

        foreach ($list_id as $value) {
            
        }
// CAMPANHAS E BASES DE DADOS


        if ($only_with_result == "true") {
            $query = "select calls.lead_id,calls.call_date,calls.length_in_sec,calls.user,calls.status,calls.uniqueid from (SELECT a.lead_id,a.call_date,a.length_in_sec,a.user,a.status,a.uniqueid from vicidial_log a where a.campaign_id=? $date_filter_client union all SELECT b.lead_id,b.call_date,b.length_in_sec,b.user,b.status,b.uniqueid from vicidial_log b where b.campaign_id=? $date_filter_client2) calls inner join script_result sr on sr.lead_id=calls.lead_id group by calls.lead_id  order by calls.call_date desc";
        } else {
            $query = "select calls.lead_id,calls.call_date,calls.length_in_sec,calls.user,calls.status,calls.uniqueid from (SELECT a.lead_id,a.call_date,a.length_in_sec,a.user,a.status,a.uniqueid from vicidial_log a where a.campaign_id=? $date_filter_client union all SELECT b.lead_id,b.call_date,b.length_in_sec,b.user,b.status,b.uniqueid from vicidial_log b where b.campaign_id=? $date_filter_client2) calls group by calls.lead_id order by calls.call_date desc";
        }
        $stmt = $db->prepare($query);
        $stmt->execute(array($campaign_id, $campaign_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $temp_info = $data_row;
            $query1 = "select vl.list_name,a.status, a.entry_date,modify_date date ,called_since_last_reset  max_tries $temp_lead_data from vicidial_list  a left join vicidial_lists vl on vl.list_id=a.list_id where a.lead_id=?";
            $stmt1 = $db->prepare($query1);
            $stmt1->execute(array($row ["lead_id"]));
            $client = $stmt1->fetch(PDO::FETCH_ASSOC);
            $client["lead_id"] = $row["lead_id"];
            $client["date"] = $row["call_date"];
            $client["length_in_sec"] = gmdate("H:i:s", $row["length_in_sec"]);
            $client["full_name"] = get_user_name($row["user"], $db);
            $client["campaign_name"] = $campaign_name;

            foreach ($statuses as $value1) {
                if ($value1["status"] == $row["status"]) {
                    $client["status_name"] = $value1["status_name"];
                }
            }
            if (!isset($client["status_name"]))
                $client["status_name"] = $client["status"];

            //RECYCLES
            $temp = (int) str_replace("Y", "", $client["max_tries"]);
            if (isset($recycle[$row["status"]])) {
                if ($temp >= $recycle[$row["status"]]) {
                    $client["max_tries"] = "Sim";
                } else {
                    $client["max_tries"] = "Não";
                }
            } else {
                $client["max_tries"] = "";
            }
            foreach ($client as $key => $value) {
                $temp_info[$key] = $value;
            }

            unset($temp_info["status"]);

            //SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----
            $query1 = "SELECT sdm.name FROM `script_dinamico_master` sdm "
                    . "left join script_result sr on sdm.id=sr.id_script "
                    . "where sr.lead_id=? $date_filter_script ";
            $stmt1 = $db->prepare($query1);
            $stmt1->execute(array($row["lead_id"]));
            $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
            if (count($tags) && $stmt1->rowCount() > 0) {
//DADOS DO SCRIPT
//DATE LIMIT
                $temp_info["name"] = $row1["name"];

                $query = "SELECT sr.lead_id,sr.tag_elemento,sr.valor,sr.param_1,sd.param1,sd.type "
                        . "FROM `script_result` sr FORCE INDEX (lead_id)"
                        . "left join script_dinamico sd on sd.tag=sr.tag_elemento and sd.id_script=sr.id_script "
                        . "where sr.id_script=:id_script and sr.campaign_id = :campaign_id "
                        . " $date_filter_script "
                        . "and sr.tag_elemento in ('" . implode("','", $tags) . "') and sr.unique_id=:uniqueid ";
                $stmt2 = $db->prepare($query);
                $stmt2->execute(array(":id_script" => $id_script, ":campaign_id" => $campaign_id, ":uniqueid" => $row["uniqueid"]));

                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    if ($row2["type"] == "tableradio") {
                        $temp_info["m" . $row2["tag_elemento"] . $row2["param_1"]] = $row2["valor"];
                    } elseif ($row2["type"] == "tableinput") {
                        $temp = explode(";", $row2["param_1"]);
                        $temp_info["m" . $row2["tag_elemento"] . $temp[1] . $temp[0]] = $row2["valor"];
                    } else {
                        $temp_info["m" . $row2["tag_elemento"]] = ($row2["param1"] == "nib") ? "" . $row2["valor"] . "" : $row2["valor"];
                    }
                }
                //SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----SCRIPT----
            }
            $final_row[$row["lead_id"]] = $temp_info;
        }
        foreach ($final_row as $value) {
            fputcsv($output, $value, ";", '"');
        }
        fclose($output);
        break;
}

function get_statuses($db, $campaign_id) {

    $query1 = "select a.status,a.status_name from vicidial_statuses a union all select b.status,b.status_name from vicidial_campaign_statuses b where b.campaign_id=? ";
    $stmt = $db->prepare($query1);
    $stmt->execute(array($campaign_id));

    while ($row_status = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $js[] = array("status" => $row_status["status"], "status_name" => $row_status["status_name"]);
    }

    return $js;
}

function get_user_name($user, $db) {
    $query = "select full_name from vicidial_users where user=?";
    $stmt = $db->prepare($query);
    $stmt->execute(array($user));
    $user_name = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user_name["full_name"];
}
