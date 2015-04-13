<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

#date_default_timezone_set('Europe/Lisbon');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$offset = filter_var($_POST["offset"]);
$start = filter_var($_POST["start"]);
$end = filter_var($_POST["end"]);
$start += $offset;
$end += $offset;


require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/calendar.php";
require "$root/AM/lib_php/user.php";
require "$root/AM/lib_php/logger.php";

$user = new UserLogin($db);
$user->confirm_login();

$log = new Logger($db, $user->getUser());

set_time_limit(1);

$resource = filter_var($_POST["resource"]);
$lead_id = filter_var($_POST["lead_id"]);
$rtype = filter_var($_POST["rtype"]);
$obs = filter_var($_POST["obs"]);
$id = filter_var($_POST["id"]);

switch (filter_var($_POST["action"])) {
    //consultas, Fitting  e Assistencias
    case "set_reservation_obs":
        $obs = filter_var($_POST["obs"]);
        $id_reservation = filter_var($_POST["id_reservation"]);
        $calendar = new Calendars($db);

        echo json_encode($calendar->set_obs($obs, $id_reservation));
        break;

    case "get_reservation_obs":
        $id_reservation = filter_var($_POST["id_reservation"]);
        $calendar = new Calendars($db);
        echo json_encode($calendar->get_obs($id_reservation));
        break;

    case "dashboardInit":
        startDash($db, $user);
        break;
    case "Init":
        startDefault($db, $user);
        break;
    case "GetReservations":
        $u = $user->getUser();
        if ($resource == "all") {
            getAllReservations($db, $user, $start, $end);
        } else {
            getResourceContent($db, $resource, $start, $end);
        }
        break;
    case "remove":
        $calendar = new Calendars($db);
        $return = $calendar->removeReserva($id);
        $log->set($id, Logger::T_RM, Logger::S_CAL, "", logger::A_DECL);
        echo json_encode($return);
        break;
    case "change":
        $calendar = new Calendars($db);
        $return = $calendar->changeReserva($id, $start, $end);
        $log->set($id, Logger::T_UPD, Logger::S_CAL, json_encode(array("start_date" => $start, "end_date" => $end, "obs" => "Remarcação")), logger::A_NCHANGE);
        echo json_encode($return);
        break;
    case "getRscContent":
        if ($resource != "all") {
            startTotal($db, $resource);
        } else {
            startDefault($db, $user);
        }
        break;
    case "newReservation":
        $calendar = new Calendars($db);
        $id = $calendar->newReserva($user->getUser()->username, $lead_id, $start, $end, $rtype, $resource);
        $log->set($id, Logger::T_INS, Logger::S_CAL, json_encode(array("lead_id" => $lead_id, "start_date" => $start, "end_date" => $end, "reservation_type" => $rtype, "resource_id" => $resource)), logger::A_SENT);
        echo json_encode($id);
        break;
    case "changeReservationResource":
        $calendar = new Calendars($db);
        $ok = $calendar->changeReservaResource($id, $resource);
        $log->set($id, Logger::T_UPD, Logger::S_CAL, json_encode(array("resource_id" => $resource, "obs" => "Alterado o resource")), logger::A_NCHANGE);
        echo json_encode($ok);
        break;
    case "changeReservationType":
        $calendar = new Calendars($db);
        $ok = $calendar->changeReservaType($id, $rtype);
        $log->set($id, Logger::T_UPD, Logger::S_CAL, json_encode(array("reservation_id" => $type, "obs" => "Alterado o resource")), logger::A_NCHANGE);
        echo json_encode($ok);
        break;
    case "special-event":
        $calendar = new Calendars($db);
        $system_types = $calendar->getSystemTypes();
        $userID = $user->getUser();
        if ($resource == "all") {
            $refs = $calendar->_getRefs($userID->username);
            $id = array();
            while ($ref = array_pop($refs)) {
                $id = $calendar->newReserva($userID->username, "", $start, $end, $system_types[$rtype], $ref->id, $obs);
                $log->set($id, Logger::T_INS, Logger::S_CAL, json_encode(array("start_date" => $start, "end_date" => $end, "reservation_type" => $system_types[$rtype], "resource_id" => $ref->id, "comments" => $obs, "obs" => "Special Events")), logger::A_SENT);
            }
        } else {
            $id = $calendar->newReserva($userID->username, "", $start, $end, $system_types[$rtype], $resource, $obs);
            $log->set($id, Logger::T_INS, Logger::S_CAL, json_encode(array("start_date" => $start, "end_date" => $end, "reservation_type" => $system_types[$rtype], "resource_id" => $resource, "comments" => $obs, "obs" => "Special Events")), logger::A_SENT);
        }

        echo json_encode(true);
        break;

    default:
        echo "Are U an Hacker? if yes then please don't hurt my feelings :-)";
        break;
}

function startTotal($db, $resource)
{
    $calendar = new Calendar($db, $resource, "rsc");
    $js = (object)array("tipo" => array(), "config" => array());
    $js->tipo = $calendar->getTipoReservas();
    $js->config = $calendar->getConfigs();
    echo json_encode($js);
}

function startDash($db, $user)
{
    $calendar = new Calendars($db);
    $js = (object)array("refs" => "", "tipo" => "", "config" => (object)array("header" => array("center" => "")));
    $js->refs = $calendar->getNames($user->getUser()->username);
    $js->config->defaultView = "agendaDay";
    $js->tipo = $calendar->getTipoReservas();
    echo json_encode($js);
}

function startDefault($db, UserLogin $user)
{
    $calendar = new Calendars($db);
    $js = (object)array("refs" => "", "tipo" => "");
    $js->refs = $calendar->getNames($user->getUser()->username);
    //$js->config = $calendar->getConfigs();
    $js->tipo = $calendar->getTipoReservas();

    echo json_encode($js);
}

function getAllReservations($db, UserLogin $user, $start, $end)
{
    $calendar = new Calendars($db);
    $reservas = $calendar->getByRefs($user->getUser()->username, $start, $end);
    echo json_encode($reservas);
}

function getResourceContent($db, $resource, $start, $end)
{
    $id = $resource;
    $calendar = new Calendar($db, $id, "rsc");
    $reservas = $calendar->getReservas($start, $end);
    $bloqueios = $calendar->getBloqueios($start, $end);
    $js = array_merge($reservas, $bloqueios);
    echo json_encode($js);
}
