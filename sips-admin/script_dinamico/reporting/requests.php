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

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('display_errors', '1');


$user = new mysiblings($db);

$temp = "";
if (!$user->is_all_campaigns) {
    $temp = "and campaign_id in('" . implode("','", $user->allowed_campaigns) . "')";
}
$js = array();
switch ($action) {

    case "check_has_script":
        $query = "SELECT a.id_script,b.name FROM script_assoc a INNER JOIN script_dinamico_master b on a.id_script=b.id WHERE a.id_camp_linha=:campaign_id";
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
        $query = "SELECT id,template FROM report_order WHERE campaign=:campaign_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        $js = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id"], "template" => $row["template"]);
        }
        echo json_encode($js);
        break;

    case "delete_template":
        $query = "Delete FROM report_order WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        break;

    case "edit_template":
        $query = "UPDATE report_order set template=:template WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":template" => $template, ":id" => $id));
        break;
    case "create_template":
        $js = array();
        $query = "SELECT Name,Display_name FROM vicidial_list_ref WHERE campaign_id = :campaign_id and active='1' ORDER BY field_order asc";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));


        $js[] = array("id" => "lead_id", "field" => "lead_id", "type" => "default", "original_texto" => "ID do Cliente", "texto" => "ID do Cliente");
        $js[] = array("id" => "user_group", "field" => "user_group", "type" => "default", "original_texto" => "Grupo de user", "texto" => "Grupo de user");
        $js[] = array("id" => "full_name", "field" => "full_name", "type" => "default", "original_texto" => "Agente", "texto" => "Agente");
        $js[] = array("id" => "call_date3", "field" => "call_date", "type" => "default", "original_texto" => "Data/Hora da chamada", "texto" => "Data/Hora da chamada", "param_1" => "data_hora_chamada");
        $js[] = array("id" => "call_date1", "field" => "call_date", "type" => "default", "original_texto" => "Data da Chamada", "texto" => "Data da Chamada", "param_1" => "data_chamada");
        $js[] = array("id" => "call_date2", "field" => "call_date", "type" => "default", "original_texto" => "Hora da chamada", "texto" => "Hora da chamada", "param_1" => "hora_chamada");
        $js[] = array("id" => "length_in_sec", "field" => "length_in_sec", "type" => "default", "original_texto" => "Duração da Chamada", "texto" => "Duração da Chamada", "param_1" => "length_in_sec");
        $js[] = array("id" => "status_name", "field" => "status_name", "type" => "default", "original_texto" => "Feedback", "texto" => "Feedback");
        $js[] = array("id" => "list_name", "field" => "list_name", "type" => "default", "original_texto" => "Base de Dados", "texto" => "Base de Dados");
        $js[] = array("id" => "entry_date", "field" => "entry_date", "type" => "default", "original_texto" => "Data de Carregamento", "texto" => "Data de Carregamento");
        $js[] = array("id" => "called_count", "field" => "called_count", "type" => "default", "original_texto" => "Nº de Chamadas", "texto" => "Nº de Chamadas");
        $js[] = array("id" => "called_since_last_reset", "field" => "called_since_last_reset", "type" => "default", "original_texto" => "Máximo de tentativas", "texto" => "Máximo de tentativas");


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["Name"], "field" => $row["Name"], "type" => "campo_dinamico", "original_texto" => $row["Display_name"], "texto" => $row["Display_name"]);
        }

        if (!count($js)) {

            $js[] = array("id" => "FIRST_NAME", "field" => "FIRST_NAME", "type" => "campo_dinamico", "original_texto" => "Nome", "texto" => "Nome");
            $js[] = array("id" => "PHONE_NUMBER", "field" => "PHONE_NUMBER", "type" => "campo_dinamico", "original_texto" => "Telefone", "texto" => "Telefone");
            $js[] = array("id" => "ADDRESS3", "field" => "ADDRESS3", "type" => "campo_dinamico", "original_texto" => "Telemóvel", "texto" => "Telemóvel");
            $js[] = array("id" => "ALT_PHONE", "field" => "ALT_PHONE", "type" => "campo_dinamico", "original_texto" => "Telefone Alternativo", "texto" => "Telefone Alternativo");
            $js[] = array("id" => "ADDRESS1", "field" => "ADDRESS1", "type" => "campo_dinamico", "original_texto" => "Morada", "texto" => "Morada");
            $js[] = array("id" => "POSTAL_CODE", "field" => "POSTAL_CODE", "type" => "campo_dinamico", "original_texto" => "Código Postal", "texto" => "Código Postal");
            $js[] = array("id" => "EMAIL", "field" => "EMAIL", "type" => "campo_dinamico", "original_texto" => "E-Mail", "texto" => "E-Mail");
            $js[] = array("id" => "COMMENTS", "field" => "COMMENTS", "type" => "campo_dinamico", "original_texto" => "Comentários", "texto" => "Comentários");
        }

        if (isset($script_id)) {

            $query = "SELECT a.tag,a.type,a.texto, a.values_text FROM script_dinamico a LEFT JOIN script_dinamico_pages b on b.id=a.id_page WHERE type not in ('pagination','textfield','scheduler','legend','button','ipl') and a.id_script=:script_id ORDER BY b.pos,a.ordem ASC ";
            $stmt = $db->prepare($query);
            $stmt->execute(array(":script_id" => $script_id));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row["type"] == "tableradio") {
                    foreach (json_decode($row["values_text"]) as $value) {
                        $js[] = array("id" => $row["tag"], "field" => "lead_id", "type" => $row["type"], "original_texto" => $value, "texto" => $value, "param_1" => $value);
                    }
                } else
                    $js[] = array("id" => $row["tag"], "field" => $row["tag"], "type" => $row["type"], "original_texto" => $value, "texto" => $row["texto"], "param_1" => "");
            }
        }

        if ($insert == "true") {
            $query = "INSERT INTO report_order(id, elements, campaign, template) VALUES (NULL, :js,:campaign_id,:template)";
            $stmt = $db->prepare($query);
            echo($stmt->execute(array(":js" => json_encode($js), ":campaign_id" => $campaign_id, ":template" => $template)));
        } else
            echo json_encode($js);

        break;

    case "get_elements_by_template":
        $query = "SELECT elements FROM report_order WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $js = json_decode($row["elements"]);

        echo json_encode($js);
        break;

    case "get_select_options":
        $js = array("campanha" => array(), "bd" => array(), "linha_inbound" => array());

        $js["campanha"] = $user->get_campaigns();

        $temp_allowed_campaigns = array();
        foreach ($js["campanha"] as $value) {
            $temp_allowed_campaigns[] = $value["id"];
        }

        $temp_allowed_campaigns = implode("','", $temp_allowed_campaigns);
        $query = "SELECT vl.list_id,vl.list_name,vl.campaign_id,vc.campaign_name,vl.active FROM vicidial_lists vl LEFT JOIN vicidial_campaigns vc on vc.campaign_id=vl.campaign_id WHERE vl.campaign_id in ('$temp_allowed_campaigns') ORDER BY vc.campaign_name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js["bd"][] = array("id" => $row["list_id"], "name" => $row["list_name"], "campaign_id" => $row["campaign_id"], "campaign_name" => $row["campaign_name"], "active" => $row["active"]);
        }
        $query = "SELECT group_id,group_name FROM vicidial_inbound_groups WHERE active='Y'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js["linha_inbound"][] = array("id" => $row["group_id"], "name" => $row["group_name"]);
        }
        echo json_encode($js);
        break;

    case "update_elements_order":
        $query = "UPDATE report_order set elements=:elements WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id, ":elements" => json_encode($elements)));
        break;


    // por filtros pra feedbacks e fazer inbound
    case "report_outbound":
        $name_script = "";
        $campaign_name = "";
        //GET ELEMENTS
        $query = "SELECT elements FROM report_order WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $field_data));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $field_data = json_decode($row["elements"]);

        //GET ID SCRIPT
        $query = "SELECT a.id_script FROM script_assoc a WHERE a.id_camp_linha=:campaign_id";
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
            } else if ($value->type == "default") {


                switch ($value->param_1) {
                    case "data_chamada":
                        $fields[] = "date_format(`$value->field`,'%Y-%m-%d') as '" . preg_replace('~[^\p{L}\p{N}]++~u', ' ', $value->texto) . "'";
                        break;
                    case "hora_chamada":
                        $fields[] = "date_format(`$value->field`,'%H:%i') as '" . preg_replace('~[^\p{L}\p{N}]++~u', ' ', $value->texto) . "'";
                        break;
                    case "data_hora_chamada":
                        $fields[] = "date_format(`$value->field`,'%Y-%m-%d %H:%i') as '" . preg_replace('~[^\p{L}\p{N}]++~u', ' ', $value->texto) . "'";
                        break;
                    case "length_in_sec":
                        $fields[] = "SEC_TO_TIME(`$value->field`) as '" . preg_replace('~[^\p{L}\p{N}]++~u', ' ', $value->texto) . "'";
                        break;
                    default:
                        $fields[] = "`$value->id` as '" . preg_replace('~[^\p{L}\p{N}]++~u', ' ', $value->texto) . "'";
                        break;
                }
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


        if (isset($list_id)) {
            $lists_log = "and a.list_id in('" . implode("','", $list_id) . "')";
            $lists_log1 = "WHERE a.list_id in('" . implode("','", $list_id) . "')";
            $lists_log2 = "and b.list_id in('" . implode("','", $list_id) . "')";
            $lists_log3 = "WHERE b.list_id in('" . implode("','", $list_id) . "')";
        } else {
            $query = "SELECT list_id FROM vicidial_lists WHERE campaign_id=:campaign_id";
            $stmt = $db->prepare($query);
            $stmt->execute(array(":campaign_id" => $campaign_id));

            $temp_list = array();
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $temp_list[] = $row[0];
            }
            $lists_log = "and a.campaign_id='$campaign_id'";
            $lists_log1 = "WHERE a.campaign_id='$campaign_id'";
            $lists_log2 = "and b.list_id in('" . implode("','", $temp_list) . "')";
            $lists_log3 = "WHERE b.list_id in('" . implode("','", $temp_list) . "')";
        }
        $data_inicio = $data_inicio . " 00:00:00";
        $data_fim = $data_fim . " 23:59:59";

        $scriptoffset = "rep_script_offset" . rand();

        $logscriptoffset = "rep_log_script_offset" . rand();

        $logscriptstatus = "rep_log_script_status" . rand();

        $logscriptstatususer = "rep_log_script_status_user" . rand();

        $logs_estado_bd = "rep_log_estado_db" . rand();

        $logsscriptgrouplead = "rep_script_group_lead" . rand();

        $final = "rep_final" . rand();

        $query_sql = "query_report" . rand() . ".sql";

        $script_elements_temp = "";


//maximo tentativas
// MAX TRIES RECYCLE
        $recycle = array();
        $query = "SELECT status,attempt_maximum FROM vicidial_lead_recycle WHERE campaign_id=:campaign_id and active='Y'";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $campaign_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recycle[$row["status"]] = $row["attempt_maximum"];
        }


        /*
         if (isset($recycle[$row["status"]])) {
         if ($temp >= $recycle[$row["status"]]) {
         $row["max_tries"] = "Sim";
         } else {
         $row["max_tries"] = "Não";
         }
         }

         */


        switch ($result_filter) {


//Apenas chamadas com resposta a script
            case 1:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = "," . implode(",", $script_elements);
                    $query = "CREATE TABLE $scriptoffset  ENGINE=MYISAM SELECT id_script, user_id, campaign_id, unique_id, lead_id, param_1 $script_elements_temp FROM script_result FORCE INDEX (unique_id) WHERE campaign_id =? and date between ? and ?  GROUP BY unique_id; ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $data_inicio, $data_fim));
                    $query = "CREATE TABLE $logscriptoffset ENGINE=MYISAM SELECT a.call_date,a.length_in_sec, a.status, a.user_group,c.list_name, b.* FROM vicidial_log a INNER JOIN $scriptoffset b on a.uniqueid = b.unique_id LEFT JOIN vicidial_lists c on c.list_id=a.list_id WHERE a.call_date between ? and ? $lists_log;";

                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $twoMonthsBefore = strtotime("-2 months", time());
                    $temp_data_inicio = strtotime($data_inicio);
                    if ($twoMonthsBefore > $temp_data_inicio) {
                        $query = " INSERT INTO $logscriptoffset (SELECT a.call_date,a.length_in_sec, a.status, a.user_group,c.list_name, b.* FROM vicidial_log_archive a INNER JOIN $scriptoffset b on a.uniqueid = b.unique_id LEFT JOIN vicidial_lists c on c.list_id=a.list_id WHERE a.call_date between ? and ? $lists_log);";
                        $stmt = $db->prepare($query);
                        $temp_data_fim = strtotime($data_fim);
                        if ($twoMonthsBefore > $temp_data_fim) {
                            $stmt->execute(array($data_inicio, $data_fim));
                        } else {
                            $stmt->execute(array($data_inicio, $twoMonthsBefore));
                        }
                    }
                    $query = "CREATE TABLE $logscriptstatus ENGINE=MYISAM SELECT a.*, b.status_name FROM $logscriptoffset a INNER JOIN (SELECT status, status_name, campaign_id FROM vicidial_campaign_statuses x WHERE campaign_id = ? UNION ALL SELECT status, status_name, ? FROM vicidial_statuses z) b WHERE a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "CREATE TABLE $logscriptstatususer ENGINE=MYISAM SELECT a.*, b.full_name FROM $logscriptstatus a LEFT JOIN vicidial_users b on a.user_id = b.user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = "," . implode(",", $client_elements);
                    $query = "CREATE TABLE $final ENGINE=MYISAM SELECT a.* $client_elements_temp,b.entry_date,b.called_count,IF(SUBSTRING_INDEX(b.called_count,'Y', -1) > c.attempt_maximum, 'Sim', 'Não') called_since_last_reset FROM $logscriptstatususer a LEFT JOIN vicidial_list b on a.lead_id = b.lead_id LEFT JOIN (SELECT status, attempt_maximum FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' GROUP BY status) c on a.status=c.status ORDER BY b.lead_id,call_date ASC; ";

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; SELECT " . implode(", ", $fields) . " FROM $final";

                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                    echo($ex);
                    exit;
                }
                system("rm /tmp/$query_sql");
                system("rm /srv/www/htdocs/report_files/$file.txt");
                system("rm /srv/www/htdocs/report_files/$file.csv");
                $query1 = "DROP TABLE $scriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatus;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatususer;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $final;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();

                echo(json_encode($file));
                break;
            case 2:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = "," . implode(",", $script_elements);
                    $query = "CREATE TABLE $scriptoffset  ENGINE=MYISAM SELECT id_script, campaign_id, unique_id, param_1 $script_elements_temp FROM script_result FORCE INDEX (unique_id) WHERE campaign_id =? and date between ? and ?  GROUP BY unique_id; ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $data_inicio, $data_fim));
                    $query = " CREATE INDEX uniqueid on $scriptoffset (unique_id); ";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "CREATE TABLE $logscriptoffset ENGINE=MYISAM SELECT a.call_date,a.length_in_sec, a.status,a.lead_id, a.user_group,a.user user_id,c.list_name, b.* FROM vicidial_log a LEFT JOIN $scriptoffset b on a.uniqueid = b.unique_id LEFT JOIN vicidial_lists c on c.list_id=a.list_id WHERE a.length_in_sec > 0 and a.status <> 'DROP' and a.call_date between ? and ?  $lists_log ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $twoMonthsBefore = strtotime("-2 months", time());
                    $temp_data_inicio = strtotime($data_inicio);
                    if ($twoMonthsBefore > $temp_data_inicio) {
                        $query = " INSERT INTO $logscriptoffset (SELECT a.call_date,a.length_in_sec, a.status,a.lead_id, a.user_group,a.user user_id,c.list_name, b.* FROM vicidial_log_archive a LEFT JOIN $scriptoffset b on a.uniqueid = b.unique_id LEFT JOIN vicidial_lists c on c.list_id=a.list_id WHERE a.length_in_sec > 0 and a.status <> 'DROP' and a.call_date between ? and ? $lists_log);";
                        $stmt = $db->prepare($query);
                        $temp_data_fim = strtotime($data_fim);
                        if ($twoMonthsBefore > $temp_data_fim) {
                            $stmt->execute(array($data_inicio, $data_fim));
                        } else {

                            $stmt->execute(array($data_inicio, $twoMonthsBefore));
                        }
                    }
                    $query = "CREATE TABLE $logscriptstatus ENGINE=MYISAM SELECT a.*, b.status_name FROM $logscriptoffset a INNER JOIN (SELECT status, status_name, campaign_id FROM vicidial_campaign_statuses x WHERE campaign_id = ? UNION ALL SELECT status, status_name, ? FROM vicidial_statuses z) b WHERE a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "CREATE TABLE $logscriptstatususer ENGINE=MYISAM SELECT a.*, b.full_name FROM $logscriptstatus a LEFT JOIN vicidial_users b on a.user_id = b.user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = "," . implode(",", $client_elements);
                    $query = "CREATE TABLE $final ENGINE=MYISAM SELECT a.* $client_elements_temp,b.entry_date,b.called_count,IF(SUBSTRING_INDEX(b.called_count,'Y', -1) > c.attempt_maximum, 'Sim', 'Não') called_since_last_reset  FROM $logscriptstatususer a LEFT JOIN vicidial_list b on a.lead_id = b.lead_id LEFT JOIN (SELECT status, attempt_maximum FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' GROUP BY status) c on a.status=c.status ORDER BY b.lead_id,call_date ASC; ";

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; SELECT " . implode(", ", $fields) . " FROM $final";

                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                    echo($ex);
                    exit;
                }
                system("rm /tmp/$query_sql");
                system("rm /srv/www/htdocs/report_files/$file.txt");
                system("rm /srv/www/htdocs/report_files/$file.csv");
                $query1 = "DROP TABLE $scriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatus;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatususer;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $final;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                echo(json_encode($file));
                break;
            case 3:
                try {
                    $query = "CREATE TABLE $logscriptoffset ENGINE = MYISAM SELECT a.call_date, a.length_in_sec, a.status, a.user_group, '$campaign_id' campaign_id, a.user user_id, a.lead_id, c.list_name FROM vicidial_log a LEFT JOIN vicidial_lists c on c.list_id = a.list_id WHERE (a.length_in_sec = 0 or status = 'DROP') $lists_log and a.call_date between ? and ?;";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $twoMonthsBefore = strtotime("-2 months", time());
                    $temp_data_inicio = strtotime($data_inicio);
                    if ($twoMonthsBefore > $temp_data_inicio) {
                        $query = "INSERT INTO $logscriptoffset (SELECT a.call_date, a.length_in_sec, a.status, a.user_group, '$campaign_id' campaign_id, a.user user_id, a.lead_id, c.list_name FROM vicidial_log_archive a LEFT JOIN vicidial_lists c on c.list_id = a.list_id WHERE a.call_date between ? and ? $lists_log and (a.length_in_sec = 0 or status = 'DROP'))";
                        $stmt = $db->prepare($query);
                        $temp_data_fim = strtotime($data_fim);
                        if ($twoMonthsBefore > $temp_data_fim) {
                            $stmt->execute(array($data_inicio, $data_fim));
                        } else {
                            $stmt->execute(array($data_inicio, $twoMonthsBefore));
                        }
                    }
                    $query = "CREATE TABLE $logscriptstatus ENGINE = MYISAM SELECT a.*, b.status_name FROM $logscriptoffset a INNER JOIN (SELECT status, status_name, campaign_id FROM vicidial_campaign_statuses x WHERE campaign_id = ? UNION ALL SELECT status, status_name, ? FROM vicidial_statuses z) b WHERE a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "CREATE TABLE $logscriptstatususer ENGINE = MYISAM SELECT a.*, b.full_name FROM $logscriptstatus a LEFT JOIN vicidial_users b on a.user_id = b . user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = ", " . implode(", ", $client_elements);

                    $query = "CREATE TABLE $final ENGINE = MYISAM SELECT a.* $client_elements_temp,b.entry_date,b.called_count,IF(SUBSTRING_INDEX(b.called_count,'Y', -1) > c.attempt_maximum, 'Sim', 'Não') called_since_last_reset FROM $logscriptstatususer a LEFT JOIN vicidial_list b on a.lead_id = b.lead_id LEFT JOIN (SELECT status, attempt_maximum FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' GROUP BY status) c on a.status=c.status ORDER BY b.lead_id, call_date ASC;";

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; SELECT " . implode(", ", $fields) . " FROM $final";
                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                    echo($ex);
                    exit;
                }
                system("rm /tmp/$query_sql");
                system("rm /srv/www/htdocs/report_files/$file.txt");
                system("rm /srv/www/htdocs/report_files/$file.csv");
                $query1 = "DROP TABLE $logscriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatus;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatususer;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $final;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                echo(json_encode($file));
                break;
            case 4:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = ", " . implode(", ", $script_elements);
                    $query = "CREATE TABLE $scriptoffset ENGINE = MYISAM SELECT id_script, campaign_id, unique_id, lead_id script_lead, param_1 $script_elements_temp FROM script_result FORCE INDEX (unique_id) WHERE campaign_id = ? and date between ? and ?  GROUP BY unique_id;";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $data_inicio, $data_fim));

                    $query = "CREATE TABLE $logs_estado_bd ENGINE = MYISAM SELECT a.*,b.call_date,b.length_in_sec FROM $scriptoffset a INNER JOIN vicidial_log b on a.unique_id=b.uniqueid ";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $twoMonthsBefore = strtotime("-2 months", time());
                    $temp_data_inicio = strtotime($data_inicio);
                    if ($twoMonthsBefore > $temp_data_inicio) {
                        $query = "INSERT INTO $logs_estado_bd (SELECT a.*,b.call_date,b.length_in_sec FROM $scriptoffset a INNER JOIN vicidial_log_archive b on a.unique_id=b.uniqueid) ";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                    }
                    $query = "CREATE TABLE $logsscriptgrouplead ENGINE = MYISAM SELECT * FROM (SELECT * FROM $logs_estado_bd ORDER BY call_date desc) a GROUP BY script_lead;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "CREATE INDEX script_lead on $logsscriptgrouplead (script_lead);";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = ", " . implode(", ", $client_elements);
                    $query = "CREATE TABLE $logscriptoffset ENGINE = MYISAM SELECT b.entry_date, b.modify_date, b.status, b.user user_id,b.lead_id, b.list_id $client_elements_temp, b.called_count,IF(SUBSTRING_INDEX(b.called_count,'Y', -1) > c.attempt_maximum, 'Sim', 'Não') called_since_last_reset, 'Sem grupo User' user_group, a.* FROM vicidial_list b LEFT JOIN $logsscriptgrouplead a on b.lead_id = a.script_lead LEFT JOIN (SELECT status, attempt_maximum FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' GROUP BY status) c on b.status=c.status WHERE a.call_date between ? and ? $lists_log2 ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $query = "CREATE TABLE $logscriptstatus ENGINE = MYISAM SELECT a.*, b.status_name FROM $logscriptoffset a INNER JOIN (SELECT status, status_name, campaign_id FROM vicidial_campaign_statuses x WHERE campaign_id = ? UNION ALL SELECT status, status_name, ? FROM vicidial_statuses z) b WHERE a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "CREATE TABLE $logscriptstatususer ENGINE = MYISAM SELECT a.*, b.full_name FROM $logscriptstatus a LEFT JOIN vicidial_users b on a.user_id = b.user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "CREATE TABLE $final ENGINE = MYISAM SELECT a.*,b.list_name FROM $logscriptstatususer a LEFT JOIN vicidial_lists b on a.list_id = b.list_id ORDER BY a.lead_id, call_date ASC;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; SELECT " . implode(", ", $fields) . " FROM $final";

                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                    echo($ex);
                    exit;
                }
                system("rm /tmp/$query_sql");
                system("rm /srv/www/htdocs/report_files/$file.txt");
                system("rm /srv/www/htdocs/report_files/$file.csv");
                $query1 = "DROP TABLE $scriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logs_estado_bd;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logsscriptgrouplead;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatus;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatususer;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $final;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                echo(json_encode($file));
                break;
            case 5:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = ", " . implode(", ", $script_elements);
                    $query = "CREATE TABLE $scriptoffset ENGINE = MYISAM SELECT id_script, campaign_id, unique_id, lead_id script_lead, param_1 $script_elements_temp FROM script_result FORCE INDEX (unique_id) WHERE campaign_id = ? GROUP BY unique_id;";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id));

                    $query = "CREATE TABLE $logs_estado_bd ENGINE = MYISAM SELECT a.*,b.call_date,b.length_in_sec FROM $scriptoffset a INNER JOIN vicidial_log b on a.unique_id=b.uniqueid ";
                    $stmt = $db->prepare($query);
                    $stmt->execute();


                    $query = "CREATE TABLE $logsscriptgrouplead ENGINE = MYISAM SELECT * FROM (SELECT * FROM $logs_estado_bd ORDER BY call_date desc) a GROUP BY script_lead;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = " CREATE INDEX script_lead on $logsscriptgrouplead (script_lead);";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = ", " . implode(", ", $client_elements);
                    $query = "CREATE TABLE $logscriptoffset ENGINE = MYISAM SELECT b.entry_date, b.modify_date, b.status, b.user user_id,b.lead_id, b.list_id $client_elements_temp,b.called_count,IF(SUBSTRING_INDEX(b.called_count,'Y', -1) > c.attempt_maximum, 'Sim', 'Não') called_since_last_reset,'Sem grupo User' user_group, a.* FROM vicidial_list b LEFT JOIN $logsscriptgrouplead a on b.lead_id = a.script_lead LEFT JOIN (SELECT status, attempt_maximum FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' GROUP BY status) c on b.status=c.status WHERE b.entry_date between ? and ?  $lists_log2 ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $query = "CREATE TABLE $logscriptstatus ENGINE = MYISAM SELECT a.*, b.status_name FROM $logscriptoffset a INNER JOIN (SELECT status, status_name, campaign_id FROM vicidial_campaign_statuses x WHERE campaign_id = ? UNION ALL SELECT status, status_name, ? FROM vicidial_statuses z) b WHERE a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "CREATE TABLE $logscriptstatususer ENGINE = MYISAM SELECT a.*, b.full_name FROM $logscriptstatus a LEFT JOIN vicidial_users b on a.user_id = b.user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "CREATE TABLE $final ENGINE = MYISAM SELECT a.*,b.list_name FROM $logscriptstatususer a LEFT JOIN vicidial_lists b on a.list_id = b.list_id ORDER BY a.lead_id, call_date ASC;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; SELECT " . implode(", ", $fields) . " FROM $final";
                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                    echo($ex);
                    exit;
                }
                system("rm /tmp/$query_sql");
                system("rm /srv/www/htdocs/report_files/$file.txt");
                system("rm /srv/www/htdocs/report_files/$file.csv");
                $query1 = "DROP TABLE $scriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logs_estado_bd;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logsscriptgrouplead;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatus;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatususer;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $final;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                echo(json_encode($file));
                break;

            //Todas as Chamadas
            case 6:
                try {
                    if (count($script_elements) > 0)
                        $script_elements_temp = "," . implode(",", $script_elements);
                    $query = "CREATE TABLE $scriptoffset  ENGINE=MYISAM SELECT id_script, campaign_id, unique_id, param_1 $script_elements_temp FROM script_result FORCE INDEX (unique_id) WHERE campaign_id =? and date between ? and ?  GROUP BY unique_id; ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $data_inicio, $data_fim));
                    $query = " CREATE INDEX uniqueid on $scriptoffset (unique_id); ";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $query = "CREATE TABLE $logscriptoffset ENGINE=MYISAM SELECT a.call_date,a.length_in_sec, a.status,a.lead_id, a.user_group,a.user user_id,c.list_name, b.* FROM vicidial_log a LEFT JOIN $scriptoffset b on a.uniqueid = b.unique_id LEFT JOIN vicidial_lists c on c.list_id=a.list_id WHERE a.call_date between ? and ?  $lists_log ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($data_inicio, $data_fim));
                    $twoMonthsBefore = strtotime("-2 months", time());
                    $temp_data_inicio = strtotime($data_inicio);
                    if ($twoMonthsBefore > $temp_data_inicio) {
                        $query = " INSERT INTO $logscriptoffset (SELECT a.call_date,a.length_in_sec, a.status,a.lead_id, a.user_group,a.user user_id,c.list_name, b.* FROM vicidial_log_archive a LEFT JOIN $scriptoffset b on a.uniqueid = b.unique_id LEFT JOIN vicidial_lists c on c.list_id=a.list_id WHERE a.call_date between ? and ? $lists_log);";
                        $stmt = $db->prepare($query);
                        $temp_data_fim = strtotime($data_fim);
                        if ($twoMonthsBefore > $temp_data_fim) {
                            $stmt->execute(array($data_inicio, $data_fim));
                        } else {

                            $stmt->execute(array($data_inicio, $twoMonthsBefore));
                        }
                    }
                    $query = "CREATE TABLE $logscriptstatus ENGINE=MYISAM SELECT a.*, b.status_name FROM $logscriptoffset a INNER JOIN (SELECT status, status_name, campaign_id FROM vicidial_campaign_statuses x WHERE campaign_id = ? UNION ALL SELECT status, status_name, ? FROM vicidial_statuses z) b WHERE a.status = b.status ";
                    $stmt = $db->prepare($query);
                    $stmt->execute(array($campaign_id, $campaign_id));
                    $query = "CREATE TABLE $logscriptstatususer ENGINE=MYISAM SELECT a.*, b.full_name FROM $logscriptstatus a LEFT JOIN vicidial_users b on a.user_id = b.user;";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    if (count($client_elements) > 0)
                        $client_elements_temp = "," . implode(",", $client_elements);
                    $query = "CREATE TABLE $final ENGINE=MYISAM SELECT a.* $client_elements_temp,b.entry_date,b.called_count,IF(SUBSTRING_INDEX(b.called_count,'Y', -1) > c.attempt_maximum, 'Sim', 'Não') called_since_last_reset FROM $logscriptstatususer a LEFT JOIN vicidial_list b on a.lead_id = b.lead_id LEFT JOIN (SELECT status, attempt_maximum FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' GROUP BY status) c on a.status=c.status ORDER BY b.lead_id,call_date ASC; ";

                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $file = "report" . date("Y-m-d_H-i-s");
                    $query = "set names 'UTF8'; SELECT " . implode(", ", $fields) . " FROM $final";

                    $fp = fopen("/tmp/$query_sql", "wb");
                    fwrite($fp, $query);
                    fclose($fp);
                    system("mysql asterisk -u$varDbUser -p$varDbPass -h $VARDB_server < /tmp/$query_sql > /srv/www/htdocs/report_files/$file.txt");
                    system("perl -lpe 's/\"/\"\"/g; s/^|$/\"/g; s/\t/\";\"/g' < /srv/www/htdocs/report_files/$file.txt > /srv/www/htdocs/report_files/$file.csv");
                    system("perl /srv/www/htdocs/report_files/convert.pl /srv/www/htdocs/report_files/$file.csv /srv/www/htdocs/report_files/$file-utf8.csv");
                } catch (Exception $ex) {
                    echo($ex);
                    exit;
                }
                system("rm /tmp/$query_sql");
                system("rm /srv/www/htdocs/report_files/$file.txt");
                system("rm /srv/www/htdocs/report_files/$file.csv");
                $query1 = "DROP TABLE $scriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptoffset;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatus;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $logscriptstatususer;";
                $stmt1 = $db->prepare($query1);
                $stmt1->execute();
                $query1 = "DROP TABLE $final;";
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
// read the file FROM disk
            readfile($file_path);
        }
        break;
}



  