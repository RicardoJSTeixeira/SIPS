<?php

require("../../../ini/dbconnect.php");
require("../../../ini/user.php");
require("PHPExcel.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

error_reporting();
ini_set('display_errors', '1');

header('Content-Disposition: attachment; filename=Report_Script_' . date("Y-m-d_H:i:s") . '.csv');



$user = new users;



$temp = "";
if (!$user->is_all_campaigns) {
    $temp = "and campaign_id in('" . implode("','", $user->allowed_campaigns) . "')";
}
switch ($action) {

    case "get_select_options":
        $js = array("campanha" => array(), "bd" => array(), "linha_inbound" => array());

        $query = "SELECT campaign_id,campaign_name FROM `vicidial_campaigns` where active='Y' $temp";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js["campanha"][] = array("id" => $row["campaign_id"], "name" => $row["campaign_name"]);
        }
        $query = "SELECT list_id,list_name,campaign_id FROM vicidial_lists where active='Y' $temp";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js["bd"][] = array("id" => $row["list_id"], "name" => $row["list_name"], "campaign_id" => $row["campaign_id"]);
        }
        $query = "SELECT group_id,group_name FROM vicidial_inbound_groups where active='Y'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js["linha_inbound"][] = array("id" => $row["group_id"], "name" => $row["group_name"]);
        }
        echo json_encode($js);
        break;


    case "update_elements_order":
        $query = "SELECT count(id) as count from report_order where campaign='$campaign'";
        $result = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($result);
        if ($row["count"] == 0) {
            $query = "insert into report_order (id,elements,campaign) values (NULL, '" . json_encode($elements) . "','$campaign')";
            $query = mysql_query($query, $link) or die(mysql_error());
        } else {
            $query = "update report_order set elements='" . json_encode($elements) . "'  where campaign='$campaign'";
            $query = mysql_query($query, $link) or die(mysql_error());
        }
        break;


    case "reset_elements_order":
        $query = "delete from report_order where campaign='$campaign_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        break;

    case "get_fields_to_order":

        $js = array();

        $query = "SELECT Name,Display_name  FROM vicidial_list_ref where campaign_id = '$campaign_id' and active='1' order by field_order asc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["Name"], "type" => "campo_dinamico", "display_name" => $row["Display_name"]);
        }

        $query = "SELECT id_script from script_assoc where id_camp_linha='$campaign_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        $id_script = $row["id_script"];
        $query = "SELECT a.tag,a.type,a.texto  FROM `script_dinamico` a left join script_dinamico_pages b on b.id=a.id_page  where type not in ('pagination','textfield','scheduler','legend','button','ipl')  and a.id_script='$id_script' order by b.pos,a.ordem asc ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["tag"], "type" => $row["type"], "texto" => $row["texto"]);
        }
        if (mysql_num_rows($query) < 1) {
            echo json_encode(array());
            exit;
        }

        $oo = array();
        $query = "SELECT id,elements,campaign  FROM  report_order where campaign='$campaign_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        $oo = json_decode($row["elements"]);

        if (mysql_num_rows($query) > 0) {
            $temp = $js;
            $js = array();
            foreach ($oo as $key) {
                foreach ($temp as $value) {
                    if ($key == $value['id']) {
                        $js[] = $value;
                        continue;
                    }
                }
            }

            //  $js = $js+ $temp;
        }
        echo json_encode($js);
        break;



    case "report":

        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');

// Nome das tabelas



        $cd_title = array();
        $cd_name = array();
        $cd_values = array();

        $script_title = array();
        $script_values = array();
        $field_data = json_decode($field_data);

        //GET ID SCRIPT
        $query = "SELECT id_script from script_assoc where id_camp_linha='$campaign_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        $id_script = $row["id_script"];

        if ($allctc == "false")
            $date_filter = "and sr.date between '$data_inicio 00:00:00' and '$data_fim 23:59:59'";
        else
            $date_filter = "";




        $contact_filter = "left join `script_result` sr on a.lead_id=sr.lead_id where id_script='$id_script' and  sr.campaign_id = '$campaign_id' $date_filter";






        $titulos = array();
        $data_row = array();
        foreach ($field_data as $key => $value) {
            if ($value->type == "campo_dinamico") {
                $data_row[$value->id] = $value->text;
            } else {
                $data_row[$value->id] = "";
            }
        }



        $query = "SELECT a.tag,a.type,a.texto,a.values_text,a.placeholder  FROM `script_dinamico` a left join script_dinamico_pages b on b.id=a.id_page  where type not in ('pagination','textfield','scheduler','legend','button','ipl')  and a.id_script='$id_script'   ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $script_values = array();
            if ($row['type'] == "tableradio") {
                $temp = json_decode($row['values_text']);
                foreach ($temp as $value) {
                    $script_values["m" . $row['tag'] . $value] = $row['texto'] . "-" . $value;
                }
                $data_row = array_slice($data_row, 0, array_search("m" . $row["tag"], array_keys($data_row)), true) + $script_values + array_slice($data_row, array_search("m" . $row["tag"], array_keys($data_row)), count($data_row) - 1, true);
                unset($data_row["m" . $row["tag"]]);
            } elseif ($row['type'] == "tableinput") {
                $temp = json_decode($row['values_text']);
                $temp2 = json_decode($row['placeholder']);
                foreach ($temp as $value) {
                    foreach ($temp2 as $value2) {

                        $script_values["m" . $row['tag'] . $value . $value2] = $row['texto'] . "-" . $value . "-" . $value2;
                    }
                }
                $data_row = array_slice($data_row, 0, array_search("m" . $row["tag"], array_keys($data_row)), true) + $script_values + array_slice($data_row, array_search("m" . $row["tag"], array_keys($data_row)), count($data_row) - 1, true);
                unset($data_row["m" . $row["tag"]]);
            }
            else
                $data_row["m" . $row["tag"]] = ($row['texto'] == "") ? "Sem titulo" : $row['texto'];
        };
        $data_row = array_merge(array("id" => "ID", "date" => "Data", "name" => "Nome", "full_name" => "Agente", "campaign_name" => "Nome da campanha", "status_name" => "Feedback"), $data_row);

        $titulos = $data_row;



        foreach ($data_row as &$value) {
            $value = "";
        }

        $temp = array();
        foreach ($field_data as $key => $value) {
            if ($value->type == "campo_dinamico")
                $temp[] = $value->id;
        }
        // DADOS DA LEAD
        $query = "SELECT a.lead_id, " . implode(",", $temp) . " from vicidial_list a  $contact_filter  group by a.lead_id";
        $result = mysql_query($query, $link) or die(mysql_error());

        while ($row3 = mysql_fetch_assoc($result)) {
            $lead_tmp = $row3["lead_id"];
            unset($row3["lead_id"]);
            $cd_values[$lead_tmp] = $row3;

            $temp_d = $data_row;



            foreach ($row3 as $key => $value) {
                $temp_d[$key] = $value;
            }
            $final_row[$lead_tmp] = $temp_d;
        }


        //DADOS DO SCRIPT
        $query = "SELECT sr.id,sr.date, sdm.name, vu.full_name,  vc.campaign_name, sr.lead_id,sr.param_1,vcs.status_name, sr.tag_elemento,sr.valor,sd.param1,sd.type FROM `script_result` sr
          left join vicidial_campaigns vc on vc.campaign_id=sr.campaign_id
          left join vicidial_users vu on sr.user_id=vu.user
          left join script_dinamico_master sdm on sdm.id=sr.id_script
          left join  vicidial_list vl on vl.lead_id=sr.lead_id
          left join vicidial_log vlg on vlg.uniqueid=sr.unique_id
          left join vicidial_campaign_statuses vcs on vcs.status=vlg.status
          left join script_dinamico sd on sd.tag=sr.tag_elemento and sd.id_script=sr.id_script
          where sr.id_script='$id_script' and sr.campaign_id = '$campaign_id'  $date_filter order by sr.lead_id ";
        $result = mysql_query($query, $link) or die(mysql_error());
        if (mysql_num_rows($result) < 1) {
            echo("Sem resultados");
            exit;
        }

        fputcsv($output, $titulos, ";", '"');
        $lead_id = false;
        while ($row1 = mysql_fetch_assoc($result)) {
            if ($lead_id != $row1["lead_id"]) {
                if ($lead_id) {
                    fputcsv($output, $temp_d, ";", '"');
                }
                $temp_d = $final_row[$row1["lead_id"]];
                unset($final_row[$row1["lead_id"]]);

                $lead_id = $row1["lead_id"];
                $temp_d["id"] = $row1["lead_id"];
                $temp_d["date"] = $row1["date"];
                $temp_d["name"] = $row1["name"];
                $temp_d["full_name"] = $row1["full_name"];
                $temp_d["campaign_name"] = $row1["campaign_name"];
                $temp_d["status_name"] = $row1["status_name"];
            }

            if ($row1["type"] == "tableradio")
                $temp_d["m" . $row1["tag_elemento"] . $row1["param_1"]] = $row1["valor"];
            elseif ($row1["type"] == "tableinput") {
                $temp = split(";", $row1["param_1"]);
                $temp_d["m" . $row1["tag_elemento"] . $temp[1] . $temp[0]] = $row1["valor"];
            }
            else
                $temp_d["m" . $row1["tag_elemento"]] = ($row1["param1"] == "nib") ? "" . $row1["valor"] . "" : $row1["valor"];
        }
        fputcsv($output, $temp_d, ";", '"');

        fclose($output);




        break;
}

