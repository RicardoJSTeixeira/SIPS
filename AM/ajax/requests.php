<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/ini/db.php";
require "$root/ini/dbconnect.php";
require "$root/ini/user.php";
require "$root/AM/lib_php/requests.php";

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users($db);

$apoio_marketing = new apoio_marketing($db, $user->user_level, $user->id);
$relatorio_correio = new correio($db, $user->user_level, $user->id);
$relatorio_frota = new frota($db, $user->user_level, $user->id);
switch ($action) {
    case "criar_relatorio_frota":
        echo json_encode($relatorio_frota->create($data, $matricula, $km, $viatura, $ocorrencias, $comments));
        break;

    case "criar_relatorio_correio":
        echo json_encode($relatorio_correio->create($carta_porte, $data, $doc, $lead_id, $input_doc_obj_assoc, $comments));
        break;

    case "criar_apoio_marketing":

        echo json_encode($apoio_marketing->create($data_inicial, $data_final, $horario, $localidade, $local, $morada, $comments, $local_publicidade));
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

















    case "accept_apoio_marketing":
        echo json_encode($apoio_marketing->accept_apoio_marketing($id));
        break;

    case "decline_apoio_marketing":
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

    