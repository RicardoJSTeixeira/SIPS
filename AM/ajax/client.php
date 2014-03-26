<?php

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
        $query = "SELECT first_name, middle_initial, last_name, address1, address2, date_of_birth FROM vicidial_list WHERE lead_id=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = array(
            (object) array("name" => "Nome", "value" => $row->first_name . " " . $row->middle_initial . " " . $row->last_name),
            (object) array("name" => "Morada", "value" => $row->address . " " . $row->address1),
            (object) array("name" => "Data de nascimento", "value" => $row->date_of_birth)
        );

        break;
    case 'default':
        $query = "SELECT lead_id, first_name, middle_initial, last_name, address1, address2, postal_code, date_of_birth, extra1  FROM vicidial_list WHERE lead_id=:id limit 1";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $js = (object) array("id"=>$row->lead_id, "name" => $row->first_name . " " . $row->middle_initial . " " . $row->last_name, "address" => $row->address . " " . $row->address1, "postalCode"=>$row->postal_code, "bDay" => $row->date_of_birth, "codCamp" => $row->extra1);

        break;

    default:
        break;
}

echo json_encode($js);

