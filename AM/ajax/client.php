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
        $query = "SELECT lead_id, first_name, middle_initial, last_name, phone_number, address1,city, address2, address3, city , postal_code, date_of_birth, email ,comments, extra1  , extra2    FROM vicidial_list WHERE lead_id=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $js = array();
        $js[] = array("name" => "first_name", "original_texto" => "Nome", "value" => $row["first_name"]);
        $js[] = array("name" => "phone_number", "original_texto" => "telefone", "value" => $row["phone_number"]);
        $js[] = array("name" => "address1", "original_texto" => "Morada", "value" => $row["address1"]);
         $js[] = array("name" => "city", "original_texto" => "Cidade", "value" => $row["city"]);
        $js[] = array("name" => "alt_phone", "original_texto" => "Telefoe alternativ", "value" => $row["alt_phone"]);
        $js[] = array("name" => "postal_code", "original_texto" => "Codigo postal", "value" => $row["postal_code"]);
        $js[] = array("name" => "email", "original_texto" => "Email", "value" => $row["email"]);
        $js[] = array("name" => "comments", "original_texto" => "Commentarios", "value" => $row["comments"]);
        $js[] = array("name" => "extra1", "original_texto" => "Codigo de marketing", "value" => $row["extra1"]);
        $js[] = array("name" => "extra2", "original_texto" => "Referência de cliente", "value" => $row["extra2"]);

    default:
        break;
}

echo json_encode($js);

