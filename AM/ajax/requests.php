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
require "$root/swiftemail/lib/swift_required.php";
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
$apoio_marketing = new apoio_marketing($db, $userID->user_level, $userID->username);
$relatorio_correio = new correio($db, $userID->user_level, $userID->username);
$relatorio_frota = new frota($db, $userID->user_level, $userID->username);
$relatorio_mensal_stock = new mensal_stock($db, $userID->user_level, $userID->username);
$relatorio_movimentacao_stock = new movimentacao_stock($db, $userID->user_level, $userID->username);
$alert = new alerts($db, $userID->username);
$log = new Logger($db, $user->getUser());
switch ($action) {
    //ADDs
    case "criar_relatorio_frota":
        echo json_encode($relatorio_frota->create($data, $matricula, $km, $viatura, $ocorrencias, $comments));
        break;

    case "criar_relatorio_correio":
        echo json_encode($relatorio_correio->create($carta_porte, $data, $input_doc_obj_assoc, $comments));
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

        $msg = "
         <h3>PEDIDO DE APOIO MKT - RASTREIOS</h3>

<strong>Dispenser:</strong>$userID->username
<br>
<br>

<strong>Data de rastreio:</strong>$data_inicial
<br>
<br>

<strong>Horário de rastreio:</strong> " . horario2mail($horario) . "
<br>
<br>

<strong>Localidade:</strong> $localidade
<br>
<br>

<strong>Local de rastreio:</strong> $local
<br>
<br>

<strong>Morada:</strong> $morada
<br>
<br>

<strong>Observações:</strong>
<br>
$comments
<br>
<br>

<table>
    <thead>
        <tr>
            <th width='100' bgcolor='#000000'>
                <p style='color:#fff;margin:0;'>Código Postal</p>
            </th>	
            <th width='450' bgcolor='#000000'>
                <p style='color:#fff;margin:0;'>Freguesia</p>
            </th>	
        </tr>
    </thead>
    <tbody>
    " . postal2tr($local_publicidade) . "
    </tbody>
</table>
<br>

<strong>Submetido por:</strong> $userID->username - $userID->name";
//marketing@acusticamedica.pt
        send_email("marketing@acusticamedica.pt", "Marketing Acústica Médica", $msg, "PEDIDO DE APOIO MKT - RASTREIOS - $userID->username - $ap->data_inicial");

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

    case "set_mkt_report";
        $calendar = new Calendars($db);
        $idRst = $apoio_marketing->get_reservations($id);
        while ($rst = array_pop($idRst)) {
            $calendar->closeMKT($rst);
        }

        $ap = $apoio_marketing->get_one($id);

        $msg = "
<h3>RELATÓRIO DE RASTREIO - APOIO MKT</h3>

    <strong>Localidade:</strong>
<br>
    $ap->local
<br>
<br>
    <strong>Data de Rastreio:</strong>
<br>
    $ap->data_inicial
<br>
<br>
    <strong>Cod. MKT:</strong>
<br>
    $cod
<br>
<br>
    <strong>Dispenser:</strong>
<br>
    $userID->username
<br>
<br>
    <strong>Rastreios efectuados:</strong>
<br>
    $total_rastreios
<br>
<br>
    <strong>Rastreios com perda:</strong>
<br>
    $rastreios_perda
<br>
<br>
    <strong>Vendas (QT):</strong>
<br>
    $vendas
<br>
<br>
    <strong>Valor (€):</strong>
<br>
    $valor
<br>
<br>
    <strong>Observações:</strong>
<br>
    $ap->data_inicial
<br>
<br>
    <strong>Submetido por:</strong> $userID->username - $userID->name";
//marketing@acusticamedica.pt
        send_email("marketing@acusticamedica.pt", "Marketing Acústica Médica", $msg, "RELATÓRIO DE RASTREIO - APOIO MKT - $userID->username - $ap->data_inicial");
        echo json_encode($apoio_marketing->set_report($id, $cod, $total_rastreios, $rastreios_perda, $vendas, $valor));
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
            if ($message)
                $alert->make($result->user, "Apoio Mkt. Aceite  Obs. $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_APMKT, json_encode(array("obs" => "Apoio Mkt. Aceite", "msg" => "$message")));
        break;

    case "decline_apoio_marketing":
        $calendar = new Calendars($db);
        $idRst = $apoio_marketing->get_reservations($id);
        while ($rst = array_pop($idRst)) {
            $calendar->removeReserva($rst);
        }
        $result = $apoio_marketing->decline($id);
        if ($result) {
            if ($message) {
                $message = "Motivo: " . $message;
            }
            $alert->make($result->user, "Apoio Mkt. Recusado $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_APMKT, json_encode(array("obs" => "Apoio Mkt. Recusado", "msg" => "$message")));
        break;

    case "accept_report_correio":
        $result = $relatorio_correio->accept($id);
        if ($result) {
            if ($message) {
                $alert->make($result->user, "Correio Aceite Obs. $message");
            }
        }
        $log->set($id, Logger::T_UPD, Logger::S_MAIL, json_encode(array("obs" => "Correio Aceite", "msg" => "$message")));
        break;

    case "decline_report_correio":
        $result = $relatorio_correio->decline($id);

        if ($result) {
            if ($message) {
                $message = "Motivo: " . $message;
            }
            $alert->make($result->user, "Correio Recusado Motivo: $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_MAIL, json_encode(array("obs" => "Correio Recusado", "msg" => "$message")));
        break;

    case "accept_report_frota":
        $result = $relatorio_frota->accept($id);
        if ($result) {
            if ($message)
                $alert->make($result->user, "Frota Aceite Obs. $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_FROTA, json_encode(array("obs" => "Frota Aceite", "msg" => "$message")));
        break;

    case "decline_report_frota":
        $result = $relatorio_frota->decline($id);
        if ($result) {
            if ($message) {
                $message = "Motivo: " . $message;
            }
            $alert->make($result->user, "Frota Recusado Motivo: $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_FROTA, json_encode(array("obs" => "Frota Recusado", "msg" => "$message")));
        break;

    case "accept_report_stock":
        $result = $relatorio_mensal_stock->accept($id);
        if ($result) {
            if ($message)
                $alert->make($result->user, "Stock Aceite Obs. $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_STOCK, json_encode(array("obs" => "Stock Aceite", "msg" => "$message")));
        break;

    case "decline_report_stock":
        $result = $relatorio_mensal_stock->decline($id);
        if ($result) {
            if ($message)
                $message = "Motivo: " . $message;
            $alert->make($result->user, "Stock Recusado $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_STOCK, json_encode(array("obs" => "Stock Recusado", "msg" => "$message")));
        break;

    case "accept_report_movimentacao":
        $result = $relatorio_movimentacao_stock->accept($id);
        if ($result) {
            if ($message)
                $alert->make($result->user, "Movimentação stock Aceite Obs. $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_MOVSTOCK, json_encode(array("obs" => "Movimentação stock Aceite", "msg" => "$message")));
        break;

    case "decline_report_movimentacao":
        $result = $relatorio_movimentacao_stock->decline($id);
        if ($result) {
            if ($message) {
                $message = "Motivo: " . $message;
            }
            $alert->make($result->user, "Movimentação Recusado: $message");
        }
        $log->set($id, Logger::T_UPD, Logger::S_MOVSTOCK, json_encode(array("obs" => "Movimentação stock recusado", "msg" => "$message")));
        break;

    default:
        echo 'Are you an hacker? if so, then please come to purosinonimo, where the company parties are full of alcohol and beautifull vanias';
}

function send_email($email_address, $email_name, $msg, $assunto) {
    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername('ccamemail@gmail.com')
            ->setPassword('ccamemail1234');

    $mailer = Swift_Mailer::newInstance($transport);

    $message = Swift_Message::newInstance($assunto)
            ->setFrom(array('ccamemail@gmail.com' => 'Acústica Médica'))
            ->setTo(array($email_address => $email_name));

    $message->setBody($msg, 'text/html');

    $result = $mailer->send($message);

    return ($result >= 1);
}

function postal2tr($postal) {
    $trs = "";
    foreach ($postal as $value) {
        $trs.="<tr><td>$value[cp]</td><td>$value[freguesia]</td></tr>";
    }
    return $trs;
}

function horario2mail($horario) {
    switch ($horario[tipo]) {
        case 1:
            return "das $horario[inicio1] às $horario[inicio2] e das $horario[fim1] às $horario[fim2]";
        case 2:
            return "das $horario[inicio1] às $horario[inicio2]";
        case 3:
            return "das $horario[fim1] às $horario[fim2]";
        default:
            break;
    }
}
