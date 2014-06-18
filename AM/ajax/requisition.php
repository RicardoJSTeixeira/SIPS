<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/user.php";
require "$root/AM/lib_php/products.php";
require "$root/AM/lib_php/requisitions.php";
require "$root/AM/lib_php/msg_alerts.php";
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
$alert = new alerts($db, $userID->username);
$products = new products($db);
$requisitions = new requisitions($db, $userID->user_level, $userID->username, $userID->siblings);
$log = new Logger($db, $user->getUser());
switch ($action) {
    case "listar_produtos_to_datatable":
        echo json_encode($products->get_products_to_datatable());
        break;

    case "listar_requisition_to_datatable":
        echo json_encode($requisitions->get_requisitions_to_datatable());
        break;

    case "listar_produtos":
        echo json_encode($products->get_products());
        break;

    case "criar_encomenda":
        echo json_encode($requisitions->create_requisition($type, $lead_id, $contract_number, $attachment, $products_list));
        break;

    case "editar_encomenda":
        echo json_encode($requisitions->edit_requisition($clientID, $cod_cliente));
        $log->set("various", Logger::T_UPD, Logger::S_ENC, json_encode(array("obs" => "Codigo de Cliente editado", "lead_id" => "$clientID")));
        break;

    case "listar_produtos_por_encomenda":
        echo json_encode($requisitions->get_products_by_requisiton($id));
        break;

    case "accept_requisition":
        $result = $requisitions->accept_requisition($id);
        if ($result) {
            if ($message) {
                $alert->make($result->user, "Encomenda Aprovada Obs. $message ID:$id");
            }
        }
        $log->set($id, Logger::T_UPD, Logger::S_ENC, json_encode(array("obs" => "Requesição aceitada", "msg" => "$message")));
        echo json_encode($result);
        break;

    case "decline_requisition":
        $result = $requisitions->decline_requisition($id);
        if ($result) {
            $alert->make($result->user, "Encomenda Rejeitada  Motivo: $message ID:$id");
        }
        $log->set($id, Logger::T_UPD, Logger::S_ENC, json_encode(array("obs" => "Requesição Rejeitada", "msg" => "$message")));
        echo json_encode($result);
        break;

    case "check_month_requisitions":
        echo json_encode($requisitions->check_month_requisitions());
        break;
}

