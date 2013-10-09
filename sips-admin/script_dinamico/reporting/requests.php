<?php

require("../../../ini/dbconnect.php");
require("../../../ini/user.php");


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



$temp="";
if(!$user->is_all_campaigns)
{
   $temp="and campaign_id in('".implode("','",$user->allowed_campaigns)."')";
}
    switch ($action) {

        case "get_select_options":
            $js=array("campanha"=>array(),"bd"=>array(),"linha_inbound"=>array());
            
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



        case "report":

            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            echo "\xEF\xBB\xBF";
            header('Content-Disposition: attachment; filename=data_new.csv');

            $output = fopen('php://output', 'w');



            
            
            $filtro = "";
            if ($tipo == "1"||$tipo == "2") {
                $filtro = "and sr.campaign_id = '$campaign_id'";
            }
            else
               $filtro = "and vl.list_id = '$list_id'";

            
            
            
             $query = "SELECT id_script from script_assoc where id_camp_linha='$campaign_id'";
            $query = mysql_query($query, $link) or die(mysql_error());
           $row = mysql_fetch_assoc($query);
                        $id_script=$row["id_script"];
            
            
            
            $titles = array('ID', 'Data', 'Script', 'Nome agente', 'Unique id', 'Campanha', 'Lead id', "feedback");
            $campos = array();
            $ref_name = array();
            $query = "SELECT Name,Display_name  FROM vicidial_list_ref where campaign_id = '$campaign_id' and active='1' ";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                array_push($titles, $row['Display_name']);
                $campos[$row['Name']] = "";
                array_push($ref_name, $row['Name']);
            }
            $tags = array();
            $query = "SELECT tag,type,texto,values_text  FROM `script_dinamico` where type not in ('pagination','textfield','scheduler')  and id_script='$id_script' order by tag asc ";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                if ($row['type'] == "tableradio") {
                    $temp = json_decode($row['values_text']);
                    foreach ($temp as $value) {
                        array_push($titles, $row['texto'] . "-" . $value);
                        $tags["m" . $row['tag'] . $value] = "";
                    }
                } else {
                    array_push($titles, $row['texto']);
                    $tags["m" . $row['tag']] = "";
                }
            }
            fputcsv($output, $titles, ";", '"');
            $query = "SELECT a.lead_id, " . implode(",", $ref_name) . " from vicidial_list a left join `script_result` b on a.lead_id=b.lead_id where id_script='$id_script' and  b.campaign_id = '$campaign_id'  group by b.lead_id";
            $result = mysql_query($query, $link) or die(mysql_error());
            $lead_info = array();
            while ($row3 = mysql_fetch_assoc($result)) {
                $lead_info[$row3["lead_id"]] = $row3;
            }
            
            if($allctc=="false")
            $date_filter="and sr.date between '$data_inicio' and '$data_fim'";
            else
                 $date_filter="";
            
            $query = "SELECT sr.id,sr.date, sdm.name, vu.full_name, sr.unique_id, vc.campaign_name, sr.lead_id,sr.param_1,vcs.status_name, sr.tag_elemento,sr.valor,sd.param1,sd.type FROM `script_result` sr
left join vicidial_campaigns vc on vc.campaign_id=sr.campaign_id
left join vicidial_users vu on sr.user_id=vu.user_id
left join script_dinamico_master sdm on sdm.id=sr.id_script
left join vicidial_list vl on vl.lead_id=sr.lead_id
left join vicidial_log vlg on vlg.uniqueid=sr.unique_id
left join vicidial_campaign_statuses vcs on vcs.status=vlg.status
left join script_dinamico sd on sd.tag=sr.tag_elemento and sd.id_script=sr.id_script 
where sr.campaign_id='$campaign_id' and sr.id_script='$id_script' $filtro $date_filter  order by sr.lead_id,tag_elemento";
            $result = mysql_query($query, $link) or die(mysql_error());
            $final_row = array("id" => "", "date" => "", "name" => "", "full_name" => "", "unique_id" => "", "campaign_name" => "", "lead_id" => "", "status_name" => "");
            $lead_id = false;
            $client = array();
            while ($row1 = mysql_fetch_assoc($result)) {
                if ($lead_id != $row1["lead_id"]) {
                    if ($lead_id) {
                        fputcsv($output, $client, ";", '"');
                    }
                    $lead_id = $row1["lead_id"];
                    $client = array_merge($final_row, $lead_info[$lead_id], $tags);
                    $client["id"] = $row1["id"];
                    $client["date"] = $row1["date"];
                    $client["name"] = $row1["name"];
                    $client["full_name"] = $row1["full_name"];
                    $client["unique_id"] = $row1["unique_id"];
                    $client["campaign_name"] = $row1["campaign_name"];
                    $client["lead_id"] = $row1["lead_id"];
                    $client["status_name"] = $row1["status_name"];
                }

                if ($row1["type"] == "tableradio")
                    $client["m" . $row1["tag_elemento"] . $row1["param_1"]] = $row1["valor"];
                else
                    $client["m" . $row1["tag_elemento"]] = ($row1["param1"] == "nib") ? "" . $row1["valor"] . "" : $row1["valor"];
            }
            fputcsv($output, $client, ";", '"'); // necessário para imprimir a info da ultima lead0
            fclose($output);
            break;
    }
?>
