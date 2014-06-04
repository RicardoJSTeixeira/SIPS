<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
set_time_limit(1);
require '../lib_php/db.php';
require '../lib_php/calendar.php';
require '../lib_php/user.php';
$user = new UserLogin($db);
$user->confirm_login();
$id = filter_var($_POST['id']);
$action = filter_var($_POST['action']);
$what = filter_var($_POST['what']);
$value = filter_var($_POST['value']);
switch ($action) {
    case 'byName':
        $query = "SELECT first_name, last_name, address1, address2, city 'local', postal_code, date_of_birth, extra1 'codmkt', extra2 'refClient', FROM vicidial_list WHERE lead_id=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = array(
            array("name" => "Cod. Mkt.", "value" => (String) $row->codmkt),
            array("name" => "Ref. Cliente", "value" => (String) $row->refClient),
            array("name" => "Nome", "value" => (String) $row->first_name . " " . $row->last_name),
            array("name" => "Localidade", "value" => (String) $row->local),
            array("name" => "Codigo Postal", "value" => (String) $row->postal_code),
            array("name" => "Morada", "value" => (String) $row->address1 . " " . $row->address2)
        );
        break;
    case 'default':
        $query = "SELECT lead_id, first_name, last_name, address1, address2, extra4 'address4', city 'local', postal_code, date_of_birth, extra1 'codmkt', extra2 'refClient', comments FROM vicidial_list WHERE lead_id=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = array(
            "id" => (int) $row->lead_id,
            "name" => (String) $row->first_name . " " . $row->last_name,
            "address" => (String) $row->address1 . " " . $row->address2 . " " . $row->address4,
            "postalCode" => (String) $row->postal_code,
            "bDay" => (String) $row->date_of_birth,
            "codCamp" => (String) $row->codmkt);
        break;
    case 'byReserv':
        $query = "SELECT a.lead_id, first_name, last_name, phone_number, address1, address2, extra4 'address4', city 'local', postal_code, date_of_birth, extra1 'codmkt', extra2 'refClient', comments, start_date, display_text  "
                . "FROM vicidial_list a "
                . "INNER JOIN sips_sd_reservations b ON a.lead_id=b.lead_id "
                . "INNER JOIN sips_sd_resources c ON b.id_resource=c.id_resource "
                . "WHERE id_reservation=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = array(
            "id" => (int) $row->lead_id,
            "phone" => (int) $row->phone_number,
            "name" => (String) $row->first_name . " " . $row->last_name,
            "address" => (String) $row->address1 . " " . $row->address2 . " " . $row->address4,
            "local" => (String) $row->local,
            "postalCode" => (String) $row->postal_code,
            "bDay" => (String) $row->date_of_birth,
            "refClient" => (String) $row->refClient,
            "codCamp" => (String) $row->codmkt,
            "date" => (String) $row->start_date,
            "rscName" => (String) $row->display_text,
            "comments" => (String) $row->comments);
        break;
    case 'byLeadToInfo':
        $js = array();
        // $query = "SET CHARACTER SET utf8;";
        // $stmt = $db->prepare($query);
        // $stmt->execute();
        $query = "SELECT Name, Display_name FROM vicidial_list_ref WHERE campaign_id=:campaign_id AND active=1 Order by field_order ASC";
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
        break;
    case 'byWhat':
        $query = "SELECT lead_id, first_name, middle_initial, last_name, phone_number, date_of_birth, extra2 'refClient' FROM vicidial_list where $what=:value";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":value" => $value));
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $js[] = array(
                "id" => (string) $row->lead_id,
                "name" => (string) $row->first_name . " " . $row->last_name,
                "nif" => (string) $row->middle_initial,
                "phone" => (string) $row->phone_number,
                "date_of_birth" => (string) $row->date_of_birth,
                "refClient" => (string) $row->refClient);
        }
        break;
    default:
        echo 'Are you an hacker? Or just a noob?';
        break;
}



echo json_encode($js);
