
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
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["Name"], "type" => "campo_dinamico", "texto" => $row["Display_name"]);
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
                
            $js["campanha"] =$user->get_campaigns();
        $query = "SELECT list_id,list_name,campaign_id FROM vicidial_lists where active='Y'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js["bd"][] = array("id" => $row["list_id"], "name" => $row["list_name"], "campaign_id" => $row["campaign_id"]);
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

    case "report":
        ini_set('memory_limit', '-1');
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');

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
        if ($allctc == "false") {
            $date_filter = "and sr.date between '$data_inicio 00:00:00' and '$data_fim 23:59:59'";
        } else {
            $date_filter = "";
        }

        if (isset($list_id)) {
            $tmp = $list_id;
            $list_id = array();
            $list_id[] = $tmp;
        } else {
$only_active_db=json_decode($only_active_db);
            if (!$only_active_db) {
                $onlyActive = " and active='Y'";
            }

            $query = "SELECT list_id from vicidial_lists where campaign_id=:campaign_id $onlyActive";
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

        $query = "SELECT campaign_name from vicidial_campaigns where campaign_id=:campaign_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $campaign_name = $row["campaign_name"];


//GET COLUMN SORT
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
        $data_row = array_merge(array("id" => "ID", "entry_date" => "Data Entrada", "date" => "Data", "name" => "Nome do Script", "full_name" => "Agente", "campaign_name" => "Nome da campanha","list_name"=>"Nome da Base Dados", "status_name" => "Feedback", "max_tries" => "Máximo Tentativas"), $data_row);

        $titulos = array();
        $titulos = $data_row;
        foreach ($data_row as $key => $value) {
            $data_row[$key] = "";
        }

        // MAX TRIES RECYCLE
        $recycle = array();
        $query = "select status,attempt_maximum from vicidial_lead_recycle where campaign_id=:campaign_id and active='Y'";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recycle[$row["status"]] = $row["attempt_maximum"];
        }

        foreach ($list_id as $value) {
            if ($only_with_result == "true") {
                $query = "SELECT a.lead_id id,status_name,vcs.status,vl.list_name, a.entry_date,vu.full_name , modify_date date ,called_since_last_reset  max_tries, " . implode(",", $temp_lead_data) . " from vicidial_list a left join vicidial_lists vl on vl.list_id=a.list_id left join (SELECT status,status_name FROM vicidial_campaign_statuses group by status UNION ALL SELECT status,status_name FROM vicidial_statuses) vcs on vcs.status=a.status left join vicidial_users vu on vu.user=a.user left join script_result sr on a.lead_id=sr.lead_id where a.list_id =:value $date_filter";
            } else {
                $query = "SELECT a.lead_id id,status_name,vcs.status,vl.list_name, a.entry_date,vu.full_name , modify_date date ,called_since_last_reset  max_tries, " . implode(",", $temp_lead_data) . " from vicidial_list a left join vicidial_lists vl on vl.list_id=a.list_id left join (SELECT status,status_name FROM vicidial_campaign_statuses group by status UNION ALL SELECT status,status_name FROM vicidial_statuses) vcs on vcs.status=a.status left join vicidial_users vu on vu.user=a.user where a.list_id =:value";
            }
            $stmt = $db->prepare($query);
            $stmt->execute(array(":value" => $value));

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $temp_d = $data_row;
                $temp = (int) str_replace("Y", "", $row["max_tries"]);
                if (isset($recycle[$row["status"]])) {
                    if ($temp >= $recycle[$row["status"]]) {
                        $row["max_tries"] = "Sim";
                    } else {
                        $row["max_tries"] = "Não";
                    }
                } else {
                    $row["max_tries"] = "";
                }
                foreach ($row as $key => $value) {
                    $temp_d[$key] = $value;
                }
                unset($temp_d["status"]);
                $temp_d["campaign_name"] = $campaign_name;
          
                $final_row[$row['id']] = $temp_d;
            }
        }

        unset($lead_tmp);
        unset($temp_d);
        fputcsv($output, $titulos, ";", '"');
        if (count($tags)) {
            //DADOS DO SCRIPT
            $query = "SELECT sr.lead_id,sr.tag_elemento,sr.valor,sr.param_1,sd.param1,sd.type "
                    . "FROM `script_result` sr FORCE INDEX (lead_id)"
                    . "left join  vicidial_list vl "
                    . "on vl.lead_id=sr.lead_id "
                    . "left join script_dinamico sd "
                    . "on sd.tag=sr.tag_elemento "
                    . "and sd.id_script=sr.id_script "
                    . "where sr.id_script=:id_script "
                    . "and sr.campaign_id = :campaign_id "
                    . "$date_filter "
                    . "and vl.list_id in ('" . implode("','", $list_id) . "') "
                    . "and sr.tag_elemento in ('" . implode("','", $tags) . "') "
                    . "order by sr.lead_id ";

            $stmt = $db->prepare($query);
            $stmt->execute(array(":id_script" => $id_script, ":campaign_id" => $campaign_id));

            $count_results = 0;


            $lead_id = false;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count_results++;

                if ($lead_id != $row["lead_id"]) {
                    if ($lead_id) {
                        fputcsv($output, $temp_d, ";", '"');
                        unset($final_row[$lead_id]);
                    }
                    $query1 = "SELECT sr.date, sdm.name, vu.full_name, vc.campaign_name,vcs.status_name,vcs.status "
                            . "FROM `script_result` sr "
                            . "left join script_dinamico_master sdm "
                            . "on sdm.id=sr.id_script "
                            . "left join vicidial_users vu "
                            . "on sr.user_id=vu.user "
                            . "left join vicidial_campaigns vc on vc.campaign_id=sr.campaign_id "
                            . "left join vicidial_log vlg on vlg.uniqueid=sr.unique_id "
                            . "left join vicidial_campaign_statuses vcs on vcs.status=vlg.status "
                            . "where "
                            . "sr.lead_id=:lead_id $date_filter order by date DESC limit 1";
                    //echo $query1;
                    $stmt1 = $db->prepare($query1);
                    $stmt1->execute(array(":lead_id" => $row["lead_id"]));
                    $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

                    $temp_d = $final_row[$row["lead_id"]];

                    $lead_id = $row["lead_id"];
                    $temp_d["id"] = $row["lead_id"];
                    $temp_d["date"] = $row1["date"];
                    $temp_d["name"] = $row1["name"];
                    $temp_d["full_name"] = $row1["full_name"];
                    $temp_d["campaign_name"] = $row1["campaign_name"];
                    $temp_d["status_name"] = $row1["status_name"];
                }

                if ($row["type"] == "tableradio") {
                    $temp_d["m" . $row["tag_elemento"] . $row["param_1"]] = $row["valor"];
                } elseif ($row["type"] == "tableinput") {
                    $temp = explode(";", $row["param_1"]);
                    $temp_d["m" . $row["tag_elemento"] . $temp[1] . $temp[0]] = $row["valor"];
                } else {
                    $temp_d["m" . $row["tag_elemento"]] = ($row["param1"] == "nib") ? "" . $row["valor"] . "" : $row["valor"];
                }
            }

            if (!$count_results) {
                echo("sem resultados");
                exit;
            }

            fputcsv($output, $temp_d, ";", '"');
            unset($final_row[$lead_id]);
        }
        if ($only_with_result != "true" or !count($tags)) {
            foreach ($final_row as $value) {
                fputcsv($output, $value, ";", '"');
            }
        }
        fclose($output);
        break;
}
