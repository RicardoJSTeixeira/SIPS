<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Lisbon');

set_time_limit(1);

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/user.php";
require "$root/AM/lib_php/logger.php";

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();

$userID = $user->getUser();

$users = new UserControler($db, $userID);
$log = new Logger($db, $userID);

switch ($action) {
    case "getAllDT":
        $allU = $users->getAll();
        while ($Cuser = array_pop($allU)) {
            $js["aaData"][] = array($Cuser->user, $Cuser->full_name, $Cuser->alias, $users->_ULalias[$Cuser->user_level] . '<div class="view-button"><a href="#" data-user=' . $Cuser->user . ' data-active=' . $Cuser->active . ' class="btn btn-mini activator"><i class="icon-check' . (($Cuser->active == "Y") ? "" : "-empty") . '" ></i><span>' . (($Cuser->active == "Y") ? "Activo" : "Inactivo") . '</span></a><a href="#" data-user=' . $Cuser->user . ' class="btn btn-mini editor"><i class="icon-edit" ></i><span>Editar</span></a></div>');
        }
        echo json_encode($js);
        break;

    case "getActive":
        echo json_encode($users->getActive());
        break;

    case "get":
        echo json_encode($users->get($username));
        break;

    case "create":
        try {
            echo json_encode($users->set($username, $pass, $desc, $alias, $ulevel));
        } catch (PDOException $exc) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            if ($exc->getCode() == 23000) {
                echo "Operador: $username, já existe.";
            }
        }
        $log->set($username, Logger::T_INS, Logger::S_USER, json_encode(array("username" => $username, "pass" => $pass, "desc" => $desc, "ulevel" => $ulevel)), logger::A_APV);
        break;

    case "active":
        echo json_encode($users->editActive($username, $active));
        $status = logger::A_APV;
        if ($active == "N")
            $status = logger::A_DECL;
        $log->set($username, Logger::T_UPD, Logger::S_USER, json_encode(array("username" => $username, "active" => $active)), $status);
        break;

    case "edit":
        echo json_encode($users->edit($username, $pass, $desc, $alias, $ulevel, $active, $siblings));
        $log->set($username, Logger::T_UPD, Logger::S_USER, json_encode(array("username" => $username, "pass" => $pass, "desc" => $desc, "ulevel" => $ulevel, "active" => $active, "siblings" => $siblings)), logger::A_NCHANGE);
        break;

    case "getTypes":
        echo json_encode($users->getTypes());
        break;

    case "save_proposta":
        echo json_encode($users->save_proposta($lead_id, $reserva_id, $proposta));
        break;

    case "get_propostas":
        echo json_encode($users->get_propostas($lead_id));
        break;

    case "get_notes":
        echo json_encode($users->get_notes($lead_id));
        break;

    case "get_notes_to_datatable":
        echo json_encode($users->get_notes_to_datatable($lead_id));
        break;
    case "insert_notes":
        echo json_encode($users->insert_notes($lead_id, $note, $title));
        break;


    case "edit_notes":
        echo json_encode($users->edit_notes($note_id, $note, $title));
        break;

    case "delete_notes":
        echo json_encode($users->delete_notes($note_id));
        break;

    default:
        break;
}