<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
set_time_limit(1);
require '../lib_php/db.php';
require '../lib_php/calendar.php';
require '../lib_php/user.php';
require '../lib_php/logger.php';
$user = new UserLogin($db);
$user->confirm_login();
$id = filter_var($_POST['id']);
$action = filter_var($_POST['action']);
$what = filter_var($_POST['what']);
$value = filter_var($_POST['value']);
$postal_code = filter_var($_POST['postal_code']);
$codmkt = filter_var($_POST['codmkt']);
$stringas = filter_var($_POST['stringas']);
$js = array();
$log = new Logger($db, $user->getUser());
switch ($action) {
    case 'byName':
        $query = "SELECT first_name, middle_initial, last_name, address1, address2, city 'local', phone_number, postal_code, date_of_birth, extra1 'codmkt', extra2 'refClient', comments FROM vicidial_list WHERE lead_id=:id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = array(
            array("name" => "Cod. Mkt.", "value" => (String)$row->codmkt),
            array("name" => "Ref. Cliente", "value" => (String)$row->refClient),
            array("name" => "Nome", "value" => (String)$row->first_name . " " . $row->middle_initial . " " . $row->last_name),
            array("name" => "Localidade", "value" => (String)$row->local),
            array("name" => "Telefone", "value" => (String)$row->phone_number),
            array("name" => "Codigo Postal", "value" => (String)$row->postal_code),
            array("name" => "Morada", "value" => (String)$row->address1 . " " . $row->address2),
            array("name" => "Comentários", "value" => (String)$row->comments)
        );
        break;
    case 'default':
        $query = "SELECT lead_id, first_name, middle_initial, last_name, address1, address2, extra4 'address4', city 'local', postal_code, date_of_birth, extra1 'codmkt', extra2 'refClient', comments FROM vicidial_list WHERE lead_id=:id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = array(
            "id" => (int)$row->lead_id,
            "name" => (String)$row->first_name . " " . $row->middle_initial . " " . $row->last_name,
            "address" => (String)$row->address1 . " " . $row->address2 . " " . $row->address4,
            "postalCode" => (String)$row->postal_code,
            "bDay" => (String)$row->date_of_birth,
            "codCamp" => (String)$row->codmkt);
        break;
    case 'byReserv':
        $query = "SELECT a.lead_id, first_name, middle_initial, last_name, phone_number, alt_phone, address1, address2, address3, extra4 'address4', city 'local', postal_code, date_of_birth, extra1 'codmkt', extra2 'refClient', extra5 'compart', comments, start_date, display_text, b.extra_id
            FROM vicidial_list a
            INNER JOIN sips_sd_reservations b ON a.lead_id=b.lead_id
            INNER JOIN sips_sd_resources c ON b.id_resource=c.id_resource
            WHERE id_reservation=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = array(
            "id" => (int)$row->lead_id,
            "phone" => (int)$row->phone_number,
            "phone1" => (int)$row->alt_phone,
            "phone2" => (int)$row->address3,
            "name" => (String)$row->first_name . " " . $row->middle_initial . " " . $row->last_name,
            "address" => (String)$row->address1 . " " . $row->address2 . " " . $row->address4,
            "local" => (String)$row->local,
            "postalCode" => (String)$row->postal_code,
            "bDay" => (String)$row->date_of_birth,
            "refClient" => (String)$row->refClient,
            "codCamp" => (String)$row->codmkt,
            "compart" => (String)$row->compart,
            "date" => (String)$row->start_date,
            "rscName" => (String)$row->display_text,
            "navId" => (String)$row->extra_id,
            "comments" => (String)$row->comments);
        break;
    case 'byLeadToInfo':
        $js = array();
        $query = "SELECT Name, Display_name FROM vicidial_list_ref WHERE campaign_id=:campaign_id AND active=1 ORDER BY field_order ASC";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $user->getUser()->campaign));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[$row["Name"]] = array("display_name" => $row["Display_name"], "name" => $row["Name"], "value" => "");
        }
        $query = "SELECT " . implode(",", array_keys($js)) . " FROM vicidial_list where lead_id=:lead_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":lead_id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($js as $key => $value) {
            $js[$key]["value"] = $row[$key];
        }
        $js = array_values($js);
        break;
    case 'byWhat':
        if (strlen($value) == 0)
            break;

        $query = "SELECT lead_id, first_name, middle_initial, last_name, phone_number, date_of_birth, extra2 'refClient', address1, postal_code, city, extra8 'nif' FROM vicidial_list WHERE $what=:value LIMIT 100";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":value" => $value));
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $js[] = array(
                "id" => (string)$row->lead_id,
                "name" => (string)$row->first_name . " " . $row->middle_initial . " " . $row->last_name,
                "nif" => (string)$row->nif,
                "phone" => (string)$row->phone_number,
                "date_of_birth" => (string)$row->date_of_birth,
                "refClient" => (string)$row->refClient,
                "address1" => (string)$row->address1,
                "postal_code" => (string)$row->postal_code,
                "city" => (string)$row->city);
        }
        break;

    case 'check_postal_code':
        $query = "SELECT rua, cod_postal, localidade, zona, freguesia, distrito, concelho FROM cp7 WHERE cod_postal LIKE :postal_code  LIMIT 300";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":postal_code" => "%" . $postal_code . "%"));
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $js[] = array(
                "rua" => (string)$row->rua,
                "cod_postal" => (string)$row->cod_postal,
                "localidade" => (string)$row->localidade,
                "zona" => (string)$row->zona,
                "freguesia" => (string)$row->freguesia,
                "distrito" => (string)$row->distrito,
                "concelho" => (string)$row->concelho);
        }
        break;

    case 'update_cod_mkt':
        $query = "UPDATE vicidial_list SET extra1=:codmkt WHERE lead_id=:id";
        $stmt = $db->prepare($query);
        $js = $stmt->execute(array(":codmkt" => $codmkt, ":id" => $id));
        break;

    case "edit_info":
        $query_string = "";
        $stringas = json_decode($stringas);
        $query_log=array();
        foreach ($stringas as $string) {
            $query_string .=  strtolower($string->key) . " = '" . mysql_real_escape_string($string->value) . "',";
            $query_log[$string->key]= $string->value;
        }
        #$query_string = rtrim($query_string, ",");
        $query = "UPDATE vicidial_list SET $query_string validation=1 where lead_id=:id";
        $stmt = $db->prepare($query);
        $js = $stmt->execute(array(":id" => $id));
        $log->set($id, Logger::T_UPD, Logger::S_CLT, json_encode( $query_log ),Logger::A_NCHANGE);
        break;

    default:
        echo 'Are you a noob hacker? Or just a noob?';
        break;
}



echo json_encode($js);
