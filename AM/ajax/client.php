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
switch ($action) {
    case 'byName':
        $query = "SELECT first_name, middle_initial, last_name, address1, address2, city 'local', postal_code, date_of_birth FROM vicidial_list WHERE lead_id=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = array(
            (object) array("name" => "Cod. Mkt.", "value" => (string) $row->extra1),
            (object) array("name" => "Nome", "value" => (string) $row->first_name . " " . $row->last_name),
            (object) array("name" => "Localidade", "value" => (string) $row->city),
            (object) array("name" => "Codigo Postal", "value" => (string) $row->postal_code),
            (object) array("name" => "Morada", "value" => (string) $row->address . " " . $row->address1)
        );
        break;
    case 'default':
        $query = "SELECT lead_id, first_name, middle_initial, last_name, address1, address2, extra4 'address4', city 'local', postal_code, date_of_birth, extra1 'codmkt', extra2 'refClient', comments FROM vicidial_list WHERE lead_id=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = (object) array(
                    "id" => (int) $row->lead_id,
                    "name" => (string) $row->first_name . " " . $row->last_name,
                    "address" => (string) $row->address . " " . $row->address1,
                    "postalCode" => (string) $row->postal_code,
                    "bDay" => (string) $row->date_of_birth,
                    "codCamp" => (string) $row->codmkt);
        break;
    case 'byReserv':
        $query = "SELECT a.lead_id, first_name, middle_initial, last_name, phone_number, address1, address2, extra4 'address4', city 'local', postal_code, date_of_birth, extra1 'codmkt', extra2 'refClient', comments, start_date, display_text  "
                . "FROM vicidial_list a "
                . "INNER JOIN sips_sd_reservations b ON a.lead_id=b.lead_id "
                . "INNER JOIN sips_sd_resources c ON b.id_resource=c.id_resource "
                . "WHERE id_reservation=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = (object) array(
                    "id" => (int) $row->lead_id,
                    "phone" => (int) $row->phone_number,
                    "name" => (string) $row->first_name . " " . $row->last_name,
                    "address" => (string) $row->address1 . " " . $row->address2 . " " . $row->address4,
                    "local" => (string) $row->local,
                    "postalCode" => (string) $row->postal_code,
                    "bDay" => (string) $row->date_of_birth,
                    "refClient" => (string) $row->refClient,
                    "codCamp" => (string) $row->codmkt,
                    "date" => (string) $row->start_date,
                    "rscName" => (string) $row->display_text,
                    "comments" => (string) $row->comments);
        break;
    case 'byLeadToInfo':
        $dfields = array();
        $query = "SET CHARACTER SET utf8;";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $query = "SELECT Name,Display_name   FROM vicidial_list_ref WHERE campaign_id=:campaign_id AND active=1 Order by field_order ASC";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":campaign_id" => $user->getUser()->campaign));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dfields[$row["Name"]] = array("display_name" => $row["Display_name"], "name" => $row["Name"], "value" => "");
        }
        $query = "SELECT " . implode(",", array_keys($dfields)) . "  FROM  vicidial_list where lead_id=:lead_id";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":lead_id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($dfields as $key => $value) {

            $dfields[$key]["value"] = $row[$key];
        }
        echo json_encode($dfields);
    default:
        break;
}



