<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

date_default_timezone_set('Europe/Lisbon');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$start = filter_var($_POST["start"]);
$end = filter_var($_POST["end"]);

require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/calendar.php";
require "$root/AM/lib_php/user.php";

$user = new UserLogin($db);
$user->confirm_login();

set_time_limit(1);

$resource = filter_var($_POST["resource"]);
$lead_id = filter_var($_POST["lead_id"]);
$rtype = filter_var($_POST["rtype"]);
$obs = filter_var($_POST["obs"]);
$id = filter_var($_POST["id"]);

switch (filter_var($_POST["action"])) {
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
            getResourceContent($db, $resource, $start, $end, ($u->user_level < 5) ? $u->username : "");
        }
        break;
    case "remove":
        $calendar = new Calendars($db);
        $return = $calendar->removeReserva($id);
        echo json_encode($return);
        break;
    case "change":
        $calendar = new Calendars($db);
        $return = $calendar->changeReserva($id, $start, $end);
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
        echo json_encode($id);
        break;
    case "changeReservationResource":
        $calendar = new Calendars($db);
        $ok = $calendar->changeReservaResource($id, $resource);
        echo json_encode($ok);
        break;
    case "special-event":
        $calendar = new Calendars($db);
        $system_types = $calendar->getSystemTypes();
        $userID = $user->getUser();
        $refs = $calendar->_getRefs($userID->username);
        $id = array();
        while ($ref = array_pop($refs)) {
            $id = $calendar->newReserva($userID->username, "", $start, $end, $system_types[$rtype], $ref->id, $obs);
        }
        echo json_encode(true);
        break;

    default:
        echo "Are U an Hacker? if yes then please don't hurt my feelings :-)";
        break;
}

function startTotal($db, $resource) {
    $calendar = new Calendar($db, $resource, "rsc");
    $js = (object) array("tipo" => array(), "config" => array());
    $js->tipo = $calendar->getTipoReservas();
    $js->config = $calendar->getConfigs();
    echo json_encode($js);
}

function startDash($db, $user) {
    $calendar = new Calendars($db);
    $js = (object) array("refs" => "", "tipo" => "", "config" => (object) array("header" => array("center" => "")));
    $js->refs = $calendar->getNames($user->getUser()->username);
    $js->config->defaultView = "agendaDay";
    $js->tipo = $calendar->getTipoReservas();
    echo json_encode($js);
}

function startDefault($db, $user) {
    $calendar = new Calendars($db);
    $js = (object) array("refs" => "", "tipo" => "");
    $js->refs = $calendar->getNames($user->getUser()->username);
    //$js->config = $calendar->getConfigs();
    $js->tipo = $calendar->getTipoReservas();

    echo json_encode($js);
}

function getAllReservations($db, $user, $start, $end) {
    $calendar = new Calendars($db);
    $reservas = $calendar->getByRefs($user->getUser()->username, $start, $end);
    echo json_encode($reservas);
}

function getResourceContent($db, $resource, $start, $end, $username) {
    $id = $resource;
    $calendar = new Calendar($db, $id, "rsc");
    $reservas = $calendar->getReservas($start, $end, $username);
    $bloqueios = $calendar->getBloqueios($start, $end);
    $js = array_merge($reservas, $bloqueios);
    echo json_encode($js);
}
