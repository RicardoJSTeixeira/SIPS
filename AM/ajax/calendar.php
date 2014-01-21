<?php
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$start = filter_var($_POST["start"]);
$end = filter_var($_POST["end"]);

require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/calendar.php";
require "$root/AM/lib_php/user.php";

$user = new UserLogin($db);
$user->confirm_login();

set_time_limit(1);

if (filter_var($_POST["id"]) AND filter_var($_POST["remove"])) {
    $calendar = new Calendars($db);
    $id = filter_var($_POST["id"]);
    $return = $calendar->removeReserva($id);
    echo json_encode($return);
    exit;
}

if (filter_var($_POST["id"]) AND filter_var($_POST["change"]) AND filter_var($_POST["start"]) AND filter_var($_POST["end"])) {
    $calendar = new Calendars($db);
    $id = filter_var($_POST["id"]);
    $start = date('Y-m-d H:i:s', filter_var($_POST["start"]));
    $end = date('Y-m-d H:i:s', filter_var($_POST["end"]));
    $return = $calendar->changeReserva($id, $start, $end);
    echo json_encode($return);
    exit;
}

if (filter_var($_POST["resource"]) AND filter_var($_POST["rtype"]) AND filter_var($_POST["start"]) AND filter_var($_POST["end"])) {
    $resource = filter_var($_POST["resource"]);
    $lead_id = filter_var($_POST["lead_id"]);
    $rtype = filter_var($_POST["rtype"]);
    $start = date('Y-m-d H:i:s', filter_var($_POST["start"]));
    $end = date('Y-m-d H:i:s', filter_var($_POST["end"]));
    $calendar = new Calendars($db);
    $id = $calendar->newReserva($user->getUser()->username, $lead_id, $start, $end, $rtype, $resource);
    echo json_encode($id);
    exit;
}

if (filter_var($_POST["init"]) == true AND filter_var($_POST["resource"]) AND filter_var($_POST["resource"]) != "all") {
    startTotal($db);
    exit;
}

if (filter_var($_POST["init"]) == true AND filter_var($_POST["dash"]) == true) {
    startDash($db, $user);
    exit;
}

if (filter_var($_POST["init"]) == true) {
    startDefault($db, $user);
    exit;
}

if (filter_var($_POST["resource"]) == "all") {
    getAllReservations($db, $user, $start, $end);
    exit;
}

if (filter_var($_POST["resource"])) {
    getResourceContent($db, filter_var($_POST["resource"]), $start, $end);
    exit;
}

function startTotal($db) {
    $id = filter_var($_POST["resource"]);
    $calendar = new Calendar($db, $id, "rsc");
    $js = (object) array("tipo" => array(), "config" => array());
    $js->tipo = $calendar->getTipoReservas();
    $js->config = $calendar->getConfigs();
    echo json_encode($js);
}

function startDash($db, $user) {
    $calendar = new Calendars($db);
    $js = (object) array("refs" => "", "tipo" => "", "config" => (object) array());
    $js->refs = $calendar->getNames($user->getUser()->username);
    $js->config->defaultView = "agendaDay";
    //$js->config = $calendar->getConfigs();
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

function getResourceContent($db, $resource, $start, $end) {
    $id = $resource;
    $calendar = new Calendar($db, $id, "rsc");
    $reservas = $calendar->getReservas($start, $end);
    $bloqueios = $calendar->getBloqueios($start, $end);
    $js = array_merge($reservas, $bloqueios);
    echo json_encode($js);
}
