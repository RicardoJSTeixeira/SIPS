<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Lisbon');

set_time_limit(1);

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/requests.php";
require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/calendar.php";
require "$root/AM/lib_php/user.php";
require "$root/AM/lib_php/msg_alerts.php";

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
$relatorio_mensal_stock = new mensal_stock($db, $userID->user_level, $userID->username);
$relatorio_movimentacao_stock = new movimentacao_stock($db, $userID->user_level, $userID->username);
$alert = new alerts($db, $userID->username);
switch ($action) {
    //ADDs
    case "criar_relatorio_frota":
        echo json_encode($relatorio_frota->create($data, $matricula, $km, $viatura, $ocorrencias, $comments));
        break;

    case "criar_relatorio_correio":
        echo json_encode($relatorio_correio->create($carta_porte, $data, $doc, $lead_id, $input_doc_obj_assoc, $comments));
        break;

    case "criar_apoio_marketing":
        $calendar = new Calendars($db);
        $refs = $calendar->_getRefs($userID->username);
        $system_types = $calendar->getSystemTypes();
        $id = array();

        $start = "";
        $end = "";
        switch ($horario["tipo"]) {
            case "1":
                $start = $horario["inicio1"];
                $end = $horario["fim2"];
                break;
            case "2":
                $start = $horario["inicio1"];
                $end = $horario["inicio2"];
                break;
            case "3":
                $start = $horario["fim1"];
                $end = $horario["fim2"];
                break;
        }

        $apoioID = $apoio_marketing->create($data_inicial, $data_final, $horario, $localidade, $local, $morada, $comments, $local_publicidade);

        while ($ref = array_pop($refs)) {
            $id[] = $calendar->newReserva($userID->username, "", strtotime($data_inicial . " " . $start), strtotime($data_final . " " . $end), $system_types["Rastreio c/ MKT"], $ref->id, '', $apoioID);
        }

        echo json_encode($apoio_marketing->setReservation($apoioID, $id));
        break;

    case "criar_relatorio_mensal_stock":
        echo json_encode($relatorio_mensal_stock->create($data, $produtos));
        break;

    case "criar_relatorio_movimentacao_stock":
        echo json_encode($relatorio_movimentacao_stock->create($data, $produtos));
        break;


    //Gets to Datatables
    case "get_apoio_marketing_to_datatable":

        echo json_encode($apoio_marketing->get_to_datatable());
        break;

    case "get_relatorio_correio_to_datatable":

        echo json_encode($relatorio_correio->get_to_datatable());
        break;

    case "get_relatorio_frota_to_datatable":
        echo json_encode($relatorio_frota->get_to_datatable());
        break;

    case "get_relatorio_stock_to_datatable":
        echo json_encode($relatorio_mensal_stock->get_to_datatable());
        break;

    case "get_relatorio_movimentacao_to_datatable":
        echo json_encode($relatorio_movimentacao_stock->get_to_datatable());
        break;


    //Get extras
    case "get_anexo_correio":
        echo json_encode($relatorio_correio->get_anexo_correio($id));
        break;

    case "save_anexo_correio":
        echo json_encode($relatorio_correio->save_anexo_correio($id, $anexos));
        break;

    case "save_stock":
        echo json_encode($relatorio_mensal_stock->save_stock($id, $produtos));
        break;

    case "save_mov_stock":
        echo json_encode($relatorio_movimentacao_stock->save_mov_stock($id, $produtos));
        break;

    case "get_one_mkt";
        echo json_encode($apoio_marketing->get_one($id));
        exit;

    case "get_horario_from_apoio_marketing":
        echo json_encode($apoio_marketing->get_horario($id));
        break;

    case "get_locais_publicidade_from_apoio_marketing":
        echo json_encode($apoio_marketing->get_locais_publicidade($id));
        break;

    case "get_ocorrencias_frota":
        echo json_encode($relatorio_frota->get($id));
        break;

    case "get_itens_stock":
        echo json_encode($relatorio_mensal_stock->get($id));
        break;

    case "get_itens_movimentacao":
        echo json_encode($relatorio_movimentacao_stock->get($id));
        break;

    //Accepts Declines

    case "accept_apoio_marketing":
        $result = $apoio_marketing->accept($id);
        if ($result) {
            $alert->make($result->user, 'Apoio Mkt. Aprovado');
        }
        echo json_encode(true);
        break;

    case "decline_apoio_marketing":
        $calendar = new Calendars($db);
        $idRst = json_decode($apoio_marketing->get_reservation($id));
        while ($rst = array_pop($idRst)) {
            $calendar->removeReserva($rst);
        }
        $result = $apoio_marketing->decline($id);
        if ($result) {
            $alert->make($result->user, "Apoio Mkt. Recusado Motivo:$motivo");
        }
        echo json_encode(true);
        break;

    case "accept_report_correio":
        $result = $relatorio_correio->accept($id);
        if ($result) {
            $alert->make($result->user, 'Correio Aprovado');
        }
        echo json_encode(true);
        break;

    case "decline_report_correio":
        $result = $relatorio_correio->decline($id);
        if ($result) {
            $alert->make($result->user, 'Correio Recusado');
        }
        echo json_encode(true);
        break;

    case "accept_report_frota":
        $result = $relatorio_frota->accept($id);
        if ($result) {
            $alert->make($result->user, 'Frota Aceite');
        }
        echo json_encode(true);
        break;

    case "decline_report_frota":
        $result = $relatorio_frota->decline($id);
        if ($result) {
            $alert->make($result->user, 'Frota Recusado');
        }
        echo json_encode(true);
        break;

    case "accept_report_stock":
        $result = $relatorio_mensal_stock->accept($id);
        if ($result) {
            $alert->make($result->user, 'Frota Aceite');
        }
        echo json_encode(true);
        break;

    case "decline_report_stock":
        $result = $relatorio_mensal_stock->decline($id);
        if ($result) {
            $alert->make($result->user, 'Mensal Recusado');
        }
        echo json_encode(true);
        break;

    case "accept_report_movimentacao":
        $result = $relatorio_movimentacao_stock->accept($id);
        if ($result) {
            $alert->make($result->user, 'Movimentação Aceite');
        }
        echo json_encode(true);
        break;

    case "decline_report_movimentacao":
        $result = $relatorio_movimentacao_stock->decline($id);
        if ($result) {
            $alert->make($result->user, 'Movimentação Recusado');
        }
        echo json_encode(true);
        break;



    default:
        echo 'Are you an hacker? if yes then please go change your underpants, it stinks!';
}
