<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Lisbon');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/requests.php";
require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/calendar.php";
require "$root/AM/lib_php/user.php";

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();

$userID = $user->getUser();
$apoio_marketing = new apoio_marketing($db, $userID->user_level, $userID->username);
$relatorio_correio = new correio($db, $userID->user_level, $userID->username);
$relatorio_frota = new frota($db, $userID->user_level, $userID->username);
switch ($action) {
    case "criar_relatorio_frota":
        echo json_encode($relatorio_frota->create($data, $matricula, $km, $viatura, $ocorrencias, $comments));
        break;

    case "criar_relatorio_correio":
        echo json_encode($relatorio_correio->create($carta_porte, $data, $doc, $lead_id, $input_doc_obj_assoc, $comments));
        break;

    case "criar_apoio_marketing":
        $calendar = new Calendars($db);
        $refs = $calendar->_getRefs($userID->username);
        $system_types=$calendar->getSystemTypes();
        $id = array();
        while ($ref = array_pop($refs)) {
            $id[] = $calendar->newReserva($userID->username, "", strtotime($data_inicial . " " . $horario["inicio1"]), strtotime($data_final . " " . $horario["fim2"]), $system_types["Apoio Markting"], $ref->id);
        }
        $ok = $apoio_marketing->create($data_inicial, $data_final, $horario, $localidade, $local, $morada, $comments, $local_publicidade, $id);
        echo json_encode($ok);
        break;

    case "remover_apoio_marketing":

        echo json_encode($apoio_marketing->delete($id));
        break;


    case "get_apoio_marketing_to_datatable":

        echo json_encode($apoio_marketing->get_to_datatable($show_admin));
        break;

    case "get_relatorio_correio_to_datatable":

        echo json_encode($relatorio_correio->get_to_datatable($show_admin));
        break;


    case "get_relatorio_frota_to_datatable":

        echo json_encode($relatorio_frota->get_to_datatable($show_admin));
        break;

    case "get_anexo_correio":
        echo json_encode($relatorio_correio->get_anexo_correio($id));
        break;

    case "get_horario_from_apoio_marketing":
        echo json_encode($apoio_marketing->get_horario($id));
        break;

    case "get_locais_publicidade_from_apoio_marketing":
        echo json_encode($apoio_marketing->get_locais_publicidade($id));
        break;


    case "get_ocorrencias_frota":
        echo json_encode($relatorio_frota->get_ocorrencias($id));
        break;

    //Admin

    case "accept_apoio_marketing":
        echo json_encode($apoio_marketing->accept_apoio_marketing($id));
        break;

    case "decline_apoio_marketing":
        $calendar = new Calendars($db);
        $idRst = json_decode($apoio_marketing->get_reservation($id));
        while ($rst = array_pop($idRst)) {
            $calendar->removeReserva($rst);
        }
        echo json_encode($apoio_marketing->decline_apoio_marketing($id));
        break;


    case "accept_report_correio":
        echo json_encode($relatorio_correio->accept_report_correio($id));
        break;

    case "decline_report_correio":
        echo json_encode($relatorio_correio->decline_report_correio($id));
        break;


    case "accept_report_frota":
        echo json_encode($relatorio_frota->accept_report_frota($id));
        break;

    case "decline_report_frota":
        echo json_encode($relatorio_frota->decline_report_frota($id));
        break;

    default:
        echo 'Are you an hacker? if yes then please go change your underpants, it stinks!';
}