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
        $query = "SELECT a.id_script,b.name from script_assoc a inner join script_dinamico_master b on a.id_script=b.id where a.id_camp_linha=:campaign_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $script_id = $row["id_script"];

        if (isset($script_id)) {
            echo json_encode(array($row["id_script"], $row["name"]));
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
        $js = array();
        $query = "SELECT Name,Display_name  FROM vicidial_list_ref where campaign_id = :campaign_id and active='1' order by field_order asc";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));



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

        if (isset($script_id)) {

            $query = "SELECT a.tag,a.type,a.texto, a.values_text  FROM `script_dinamico` a left join script_dinamico_pages b on b.id=a.id_page  where type not in ('pagination','textfield','scheduler','legend','button','ipl')  and a.id_script=:script_id order by b.pos,a.ordem asc ";
            $stmt = $db->prepare($query);
            $stmt->execute(array(":script_id" => $script_id));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row["type"] == "tableradio") {
                    foreach (json_decode($row["values_text"]) as $value) {
                        $js[] = array("id" => $row["tag"], "type" => $row["type"], "texto" => $value, "param_1" => $value);
                    }
                } else
                    $js[] = array("id" => $row["tag"], "type" => $row["type"], "texto" => $row["texto"], "param_1" => "");
            }
        }


        $query = "INSERT INTO `report_order`(`id`, `elements`, `campaign`, `template`) VALUES (NULL, :js,:campaign_id,:template)";
        $stmt = $db->prepare($query);
        echo( $stmt->execute(array(":js" => json_encode($js), ":campaign_id" => $campaign_id, ":template" => $template)));
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


        $name_script = "";
        $campaign_name = "";
//GET ELEMENTS
        $query = "SELECT elements from report_order where id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $field_data));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $field_data = json_decode($row["elements"]);

//GET ID SCRIPT
        $query = "SELECT a.id_script from script_assoc a where a.id_camp_linha=:campaign_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_script = $row["id_script"];



        $script_elements = array();
        $client_elements = array();
        $fields = array();
        $titles = array();


        foreach ($field_data as $key => $value) {

            if ($value->type == "campo_dinamico") {
                $client_elements[] = "b." . $value->id;
                $fields[] = "`$value->id` as '" . preg_replace('~[^\p{L}\p{N}]++~u', ' ', $value->texto) . "'";
            } else {
                if ($result_filter != 3) {
                    if ($value->type == "tableradio") {
                        $tableradio = rand();
                        $script_elements[] = " MAX(IF(`tag_elemento`=' $value->id ' AND  `param_1`= '$value->param_1',valor,'') ) AS '$value->id$tableradio' ";
                        $fields[] = "`$value->id$tableradio` as '" . preg_replace('~[^\p{L}\p{N}]++~u', ' ', $value->texto) . "'";
                    } else {
                        $script_elements[] = " MAX(IF(`tag_elemento`=' $value->id ',valor,'') ) AS ' $value->id' ";
                        $fields[] = "`$value->id`  as '" . preg_replace('~[^\p{L}\p{N}]++~u', ' ', $value->texto) . "'";
                    }
                }
            }
        }






        $date_filter_client = "  ";


        $lists = "";


        if (isset($list_id)) {
            $lists_log = "and a.list_id in('" . implode("','", $list_id) . "')";
            $lists_log1 = "Where a.list_id in('" . implode("','", $list_id) . "')";
            $lists_log2 = "and b.list_id in('" . implode("','", $list_id) . "')";
            $lists_log3 = "Where b.list_id in('" . implode("','", $list_id) . "')";
        } else {
            $query = "SELECT list_id from vicidial_lists where campaign_id=:campaign_id";
            $stmt = $db->prepare($query);
            $stmt->execute(array(":campaign_id" => $campaign_id));

            $temp_list = array();
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $temp_list[] = $row[0];
            }
            $lists_log = "and a.list_id in('" . implode("','", $temp_list) . "')";
            $lists_log1 = "Where a.list_id in('" . implode("','", $temp_list) . "')";
            $lists_log2 = "and b.list_id in('" . implode("','", $temp_list) . "')";
            $lists_log3 = "Where b.list_id in('" . implode("','", $temp_list) . "')";
        }
        $data_inicio = $data_inicio . " 00:00:00";
        $data_fim = $data_fim . " 23:59:59";

        $scriptoffset = "rep_script_offset" . rand();

        $logscriptoffset = "rep_log_script_offset" . rand();

        $logscriptstatus = "rep_log_script_status" . rand();

        $logscriptstatususer = "rep_log_script_status_user" . rand();

        $logsscriptgrouplead = "rep_script_group_lead" . rand();

        $final = "rep_final" . rand();

        $query_sql = "query_report" . rand() . ".sql";





        switch ($result_filter) {
            case 1:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = "," . implode(",", $script_elements);
                    $query = "CREATE TABLE  $scriptoffset   ENGINE=MYISAM  select  id_script, user_id, campaign_id, unique_id, lead_id, param_1 $script_elements_temp from script_result FORCE INDEX (unique_id) WHERE campaign_id =? and date between ? and ?   group by unique_id; ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $data_inicio, $data_fim));
                    $query = "create table $logscriptoffset ENGINE=MYISAM select a.call_date,a.length_in_sec, a.status, a.user_group, b.* from vicidial_log a inner join $scriptoffset b on a.uniqueid = b.unique_id where  a.call_date between ? and ?  $lists_log;";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $twoMonthsBefore = strtotime("-2 months", time());
                    $temp_data_inicio = strtotime($data_inicio);
                    if ($twoMonthsBefore > $temp_data_inicio) {
                        $query = " insert into $logscriptoffset (select a.call_date,a.length_in_sec, a.status, a.user_group, b.* from vicidial_log_archive a inner join $scriptoffset b on a.uniqueid = b.unique_id where a.call_date between ? and ? $lists_log);";
                        $stmt = $db->prepare($query);
                        $temp_data_fim = strtotime($data_fim);
                        if ($twoMonthsBefore > $temp_data_fim) {
                            $stmt->execute(array($data_inicio, $data_fim));
                        } else {
                            $stmt->execute(array($data_inicio, $twoMonthsBefore));
                        }
                    }
                    $query = "create table $logscriptstatus ENGINE=MYISAM select a.*, b.status_name from $logscriptoffset a inner join (select status, status_name, campaign_id from vicidial_campaign_statuses x where campaign_id = ? union all select status, status_name, ? from vicidial_statuses z) b where a.status = b.status ";
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
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; select lead_id `Id do Cliente`, user_group `Grupo de user`, call_date `Data da chamada`,user_id,SEC_TO_TIME( length_in_sec ) `Duração Chamada`,  status_name `Feedback`, " . implode(", ", $fields) . " from $final";
                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' <  /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl  /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                         echo($ex);exit;
                }
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
            case 2:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = "," . implode(",", $script_elements);
                    $query = "CREATE TABLE  $scriptoffset   ENGINE=MYISAM  select  id_script, campaign_id, unique_id,  param_1 $script_elements_temp from script_result FORCE INDEX (unique_id) WHERE campaign_id =? and date between ? and ?   group by unique_id; ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $data_inicio, $data_fim));
                    $query = "  create index uniqueid on $scriptoffset (unique_id); ";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "create table $logscriptoffset ENGINE=MYISAM select a.call_date,a.length_in_sec, a.status,a.lead_id, a.user_group,a.user user_id,c.list_name, b.* from vicidial_log a left join $scriptoffset b on a.uniqueid = b.unique_id left join vicidial_lists c on c.list_id=a.list_id where a.length_in_sec > 0 and a.status <> 'DROP' and a.call_date between ? and ?   $lists_log ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $twoMonthsBefore = strtotime("-2 months", time());
                    $temp_data_inicio = strtotime($data_inicio);
                    if ($twoMonthsBefore > $temp_data_inicio) {
                        $query = " insert into $logscriptoffset (select a.call_date,a.length_in_sec, a.status,a.lead_id, a.user_group,a.user user_id,c.list_name, b.* from vicidial_log_archive a left join $scriptoffset b on a.uniqueid = b.unique_id  left join vicidial_lists c on c.list_id=a.list_id where a.length_in_sec > 0 and a.status <> 'DROP' and a.call_date between ? and ? $lists_log);";
                        $stmt = $db->prepare($query);
                        $temp_data_fim = strtotime($data_fim);
                        if ($twoMonthsBefore > $temp_data_fim) {
                            $stmt->execute(array($data_inicio, $data_fim));
                        } else {

                            $stmt->execute(array($data_inicio, $twoMonthsBefore));
                        }
                    }
                    $query = "create table $logscriptstatus ENGINE=MYISAM select a.*, b.status_name from $logscriptoffset a inner join (select status, status_name, campaign_id from vicidial_campaign_statuses x where campaign_id = ? union all select status, status_name, ? from vicidial_statuses z) b where a.status = b.status ";
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
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; select lead_id `Id do Cliente`, user_group `Grupo de user`, call_date `Data da chamada`,user_id, SEC_TO_TIME( length_in_sec ) `Duração Chamada`,  status_name `Feedback`,list_name  `Base de dados` ," . implode(", ", $fields) . " from $final";
                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' <  /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl  /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                         echo($ex);exit;
                }
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
            case 3:
                try {
                    $query = "create table $logscriptoffset ENGINE = MYISAM select a.call_date, a.length_in_sec, a.status, a.user_group, '$campaign_id' campaign_id, a.user user_id, a.lead_id, c.list_name from vicidial_log a left join vicidial_lists c on c.list_id = a.list_id where (a.length_in_sec = 0 or status = 'DROP') $lists_log and a.call_date between ? and ?;";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $twoMonthsBefore = strtotime("-2 months", time());
                    $temp_data_inicio = strtotime($data_inicio);
                    if ($twoMonthsBefore > $temp_data_inicio) {
                        $query = " insert into $logscriptoffset (select a.call_date, a.length_in_sec, a.status, a.user_group, '$campaign_id' campaign_id, a.user user_id, a.lead_id, c.list_name from vicidial_log_archive a left join vicidial_lists c on c.list_id = a.list_id where a.call_date between ? and ? $lists_log and (a.length_in_sec = 0 or status = 'DROP'))";
                        $stmt = $db->prepare($query);
                        $temp_data_fim = strtotime($data_fim);
                        if ($twoMonthsBefore > $temp_data_fim) {
                            $stmt->execute(array($data_inicio, $data_fim));
                        } else {

                            $stmt->execute(array($data_inicio, $twoMonthsBefore));
                        }
                    }
                    $query = "create table $logscriptstatus ENGINE = MYISAM select a.*, b.status_name from $logscriptoffset a inner join (select status, status_name, campaign_id from vicidial_campaign_statuses x where campaign_id = ? union all select status, status_name, ? from vicidial_statuses z) b where a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "create table $logscriptstatususer ENGINE = MYISAM select a.*, b.full_name from $logscriptstatus a left join vicidial_users b on a.user_id = b . user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = ", " . implode(", ", $client_elements);
                    $query = "create table $final ENGINE = MYISAM select a.* $client_elements_temp from $logscriptstatususer a left join vicidial_list b on a.lead_id = b.lead_id order by b.lead_id, call_date asc;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8';
select lead_id `Id do Cliente`, user_group `Grupo de user`, call_date `Data da chamada`, SEC_TO_TIME( length_in_sec ) `Duração Chamada`,user_id, status_name `Feedback`, list_name `Base de dados`, " . implode(", ", $fields) . " from $final";
                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl  /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                         echo($ex);exit;
                }
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
            case 4:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = ", " . implode(", ", $script_elements);
                    $query = "CREATE TABLE $scriptoffset ENGINE = MYISAM select id_script,  campaign_id, unique_id, lead_id script_lead, date, param_1 $script_elements_temp from script_result FORCE INDEX (unique_id) WHERE campaign_id = ?  group by unique_id;";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id));
                    $query = "create table $logsscriptgrouplead ENGINE = MYISAM select *, max(date) as MaxDate from $scriptoffset group by script_lead;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = " create index script_lead on $logsscriptgrouplead (script_lead);";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = ", " . implode(", ", $client_elements);
                    $query = "create table $logscriptoffset ENGINE = MYISAM select b.entry_date, b.modify_date, b.status, b.user user_id,b.lead_id, b.list_id $client_elements_temp, b.called_since_last_reset, b.called_count, b.last_local_call_time, a.* from vicidial_list b left join $logsscriptgrouplead a on b.lead_id = a.script_lead  $lists_log3 ";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "create table $logscriptstatus ENGINE = MYISAM select a.*, b.status_name from $logscriptoffset a inner join (select status, status_name, campaign_id from vicidial_campaign_statuses x where campaign_id = ? union all select status, status_name, ? from vicidial_statuses z) b where a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "create table $logscriptstatususer ENGINE = MYISAM select a.*, b.full_name from $logscriptstatus a left join vicidial_users b on a.user_id = b.user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "create table $final ENGINE = MYISAM select a.*,b.list_name from $logscriptstatususer a left join vicidial_lists b on a.list_id = b.list_id order by a.lead_id, date asc;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; select lead_id `Id do Cliente`, last_local_call_time `Data da chamada`,user_id,  status_name `Feedback`,list_name  `Base de dados` ," . implode(", ", $fields) . " from $final";
                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl  /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                         echo($ex);exit;
                }
                $query1 = "drop table $scriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "drop table $logsscriptgrouplead;";
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
            case 5:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = ", " . implode(", ", $script_elements);
                    $query = "CREATE TABLE $scriptoffset ENGINE = MYISAM select id_script,  campaign_id, unique_id, lead_id script_lead, date, param_1 $script_elements_temp from script_result FORCE INDEX (unique_id) WHERE campaign_id = ?  group by unique_id;";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id));
                    $query = "create table $logsscriptgrouplead ENGINE = MYISAM select *, max(date) as MaxDate from $scriptoffset group by script_lead;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = " create index script_lead on $logsscriptgrouplead (script_lead);";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = ", " . implode(", ", $client_elements);
                    $query = "create table $logscriptoffset ENGINE = MYISAM select b.entry_date, b.modify_date, b.status, b.user user_id,b.lead_id, b.list_id $client_elements_temp, b.called_since_last_reset, b.called_count, b.last_local_call_time, a.* from vicidial_list b left join $logsscriptgrouplead a on b.lead_id = a.script_lead where b.entry_date between ? and ?   $lists_log2 ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $query = "create table $logscriptstatus ENGINE = MYISAM select a.*, b.status_name from $logscriptoffset a inner join (select status, status_name, campaign_id from vicidial_campaign_statuses x where campaign_id = ? union all select status, status_name, ? from vicidial_statuses z) b where a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "create table $logscriptstatususer ENGINE = MYISAM select a.*, b.full_name from $logscriptstatus a left join vicidial_users b on a.user_id = b.user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "create table $final ENGINE = MYISAM select a.*,b.list_name from $logscriptstatususer a left join vicidial_lists b on a.list_id = b.list_id order by a.lead_id, date asc;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; select lead_id `Id do Cliente`, last_local_call_time `Data da chamada`,user_id,  status_name `Feedback`,list_name  `Base de dados` ,entry_date `Data de carregamento` " . implode(", ", $fields) . " from $final";
                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl  /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                    echo($ex);exit;
                }
                $query1 = "drop table $scriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "drop table $logsscriptgrouplead;";
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
        }
        break;



    case "get_report_file":
        $file = $file . "-utf8.csv";
        $file_path = "/srv/www/htdocs/report_files/$file";
        if (!$file) { // file does not exist
            die('file not found');
        } else {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment;filename = $file");
            header("Content-Type: application/csv");
            header("Content-Transfer-Encoding: binary");
            // read the file from disk
            readfile($file_path);
        }
        break;
}


    