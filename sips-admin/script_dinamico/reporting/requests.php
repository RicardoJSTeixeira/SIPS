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

        //  $js[] = array("id" => "lead_id", "type" => "campo_dinamico", "texto" => "Lead_id do Cliente");


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


        $query = "SELECT a.tag,a.type,a.texto, a.values_text  FROM `script_dinamico` a left join script_dinamico_pages b on b.id=a.id_page  where type not in ('pagination','textfield','scheduler','legend','button','ipl')  and a.id_script=:script_id order by b.pos,a.ordem asc ";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":script_id" => $script_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row["type"] == "tableradio") {
                foreach (json_decode($row["values_text"]) as $value) {
                    $js[] = array("id" => $row["tag"], "type" => $row["type"], "texto" => $row["texto"] . ":" . $value, "param_1" => $value);
                }
            } else
                $js[] = array("id" => $row["tag"], "type" => $row["type"], "texto" => $row["texto"], "param_1" => "");
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
        $query = "SELECT vl.list_id,vl.list_name,vl.campaign_id,vc.campaign_name,vl.active FROM vicidial_lists vl left join vicidial_campaigns vc on vc.campaign_id=vl.campaign_id  order by vc.campaign_name asc";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js["bd"][] = array("id" => $row["list_id"], "name" => $row["list_name"], "campaign_id" => $row["campaign_id"], "campaign_name" => $row["campaign_name"], "active" => $row["active"]);
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



        $script_elements = array();
        $client_elements = array();
        $fields = array();
        $titles = array();



        $titles[] = "Grupo";
        $titles[] = "Data chamada";
        $titles[] = "Agente";
        $titles[] = "Feedback";
        foreach ($field_data as $key => $value) {

            if ($value->type == "campo_dinamico") {
                $client_elements[] = "b." . $value->id;
                $fields[] = $value->id;
                $titles[] = $value->texto;
            } else {

                if ($value->type == "tableradio") {

                    $script_elements[] = " MAX(IF(`tag_elemento`=' $value->id ' AND  `param_1`= '$value->param_1',valor,'') ) AS ' $value->texto' ";
                } else {
                    $script_elements[] = " MAX(IF(`tag_elemento`=' $value->id ',valor,'') ) AS ' $value->texto' ";
                }

                $fields[] = "`" . $value->texto . "`";
                $titles[] = $value->texto;
            }
        }




        if ($allctc == "false") {
            $date_filter_client = " and date between '$data_inicio 00:00:00' and '$data_fim 23:59:59' ";
        } else {
            $date_filter_client = "";
        }

        $lists = "";
        if (isset($list_id)) {
            $lists_log = "where a.list_id in('" . implode("','", $list_id) . "')";
            $lists_archive = "and a.list_id in('" . implode("','", $list_id) . "')";
        }


        $scriptoffset = "scriptoffset" . rand();

        $logscriptoffset = "logscriptoffset" . rand();

        $logscriptstatus = "logscriptstatus" . rand();

        $logscriptstatususer = "logscriptstatususer" . rand();

        $final = "final" . rand();
        if (count($script_elements) > 0)
            $script_elements_temp = "," . implode(",", $script_elements);

        $query = "CREATE TABLE  $scriptoffset   ENGINE=MYISAM  select  id_script, user_id, campaign_id, unique_id, lead_id, param_1 $script_elements_temp from script_result FORCE INDEX (unique_id) WHERE campaign_id =? $date_filter_client  group by unique_id; ";
        $stmt = $db->prepare($query);
        $stmt->execute(array($campaign_id));

        $query = "create table $logscriptoffset ENGINE=MYISAM select a.call_date,a.length_in_sec, a.status, a.user_group, b.* from vicidial_log a inner join $scriptoffset b on a.uniqueid = b.unique_id  $lists_log;";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $today = time();
        $twoMonthsBefore = strtotime("-2 months", $today);
        $query = " insert into $logscriptoffset (select a.call_date,a.length_in_sec, a.status, a.user_group, b.* from vicidial_log_archive a inner join $scriptoffset b on a.uniqueid = b.unique_id where a.call_date < ? $lists_archive)";
        $stmt = $db->prepare($query);
        $stmt->execute(array($twoMonthsBefore));


        $query = "create table $logscriptstatus ENGINE=MYISAM select a.*, b.status_name from $logscriptoffset a inner join (select status, status_name, campaign_id from vicidial_campaign_statuses x where campaign_id = ? union all select status, status_name, ? from vicidial_statuses z) b where a.status = b.status and a.campaign_id = b.campaign_id;";
        $stmt = $db->prepare($query);
        $stmt->execute(array($campaign_id, $campaign_id));


        $query = "create table $logscriptstatususer ENGINE=MYISAM select a.*, b.full_name from $logscriptstatus a left join vicidial_users b on a.user_id = b.user;";
        $stmt = $db->prepare($query);
        $stmt->execute();

        if (count($client_elements) > 0)
            $client_elements_temp = "," . implode(",", $client_elements);
        $query = "create table $final ENGINE=MYISAM select a.* $client_elements_temp  from $logscriptstatususer a left join vicidial_list b on a.lead_id = b.lead_id  order by b.lead_id,call_date asc; ";
        $stmt = $db->prepare($query);
        $stmt->execute();



        $file = "report" . date("Y-m-d_H-i-s") . ".csv";
        $query = "select '" . implode("','", $titles) . "' union all      select  user_group,call_date,full_name,status_name, " . implode(",", $fields) . " from $final";
        $stmt = $db->prepare($query);
        $stmt->execute();


        $content = "some text here";
        $fp = fopen("/tmp/query.sql", "wb");
        fwrite($fp, $query);
        fclose($fp);


        system("mysql asterisk  -usipsadmin -psipsps2012 -h 172.16.7.25 < /tmp/query.sql > /srv/www/htdocs/report_files/teste.csv ");


        $query1 = "drop table $scriptoffset;";
        $stmt1 = $db->prepare($query1);
        $stmt1->execute();

        $query1 = "drop table $logscriptoffset;";
        $stmt1 = $db->prepare($query1);
        $stmt1->execute();

        $query1 = "drop table $logscriptstatus;";
        $stmt1 = $db->prepare($query1);
        $stmt1->execute();

        $query1 = "drop table $logscriptstatususer;";
        $stmt1 = $db->prepare($query1);
        $stmt1->execute();

        $query1 = "drop table $final;";
        $stmt1 = $db->prepare($query1);
        $stmt1->execute();

        echo(json_encode($file));
        break;



    case "get_report_file":
        $file_path = "/srv/www/htdocs/report_files/teste.csv";
        if (!$file) { // file does not exist
            die('file not found');
        } else {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$file");
            header("Content-Type: application/csv");
            header("Content-Transfer-Encoding: binary");
            // read the file from disk
            readfile($file_path);
        }
        break;
}

    
    