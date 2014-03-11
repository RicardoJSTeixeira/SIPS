<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/ini/db.php";
require "$root/ini/dbconnect.php";
require "$root/ini/user.php";
require "$root/AM/lib_php/requests_class.php";

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

        echo json_encode($apoio_marketing->create($data, $horario, $localidade, $local, $morada, $comments, $local_publicidade));
        break;

    case "get_apoio_marketing_to_datatable":

        echo json_encode($apoio_marketing->get_to_datatable());
        break;

    case "get_relatorio_correio_to_datatable":
      
        echo json_encode($relatorio_correio->get_to_datatable());
        break;
    
    
    case "get_relatorio_frota_to_datatable":
      
        echo json_encode($relatorio_frota->get_to_datatable());
        break;
}

    