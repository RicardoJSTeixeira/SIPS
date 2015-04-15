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
        echo json_encode($products->get_products_to_datatable($domain));
        break;

    case "listar_requisition_to_datatable":
        echo json_encode($requisitions->get_requisitions_to_datatable($domain, $show_aproved));
        break;

    case "listar_produtos":
        echo json_encode($products->get_products($domain));
        break;

    case "criar_encomenda":
        $encomenda = $requisitions->create_requisition($domain, $type, $lead_id, $contract_number, $attachment, $products_list, $comments);
        $log->set($encomenda[0], Logger::T_INS, Logger::S_ENC, "", logger::A_SENT);
        echo json_encode($encomenda);

        break;

    case "get_encomenda":
        echo json_encode($requisitions->get_requisitions_by_id($id_req));
        break;


    case "editar_encomenda":
        echo json_encode($requisitions->edit_requisition($clientID, $cod_cliente));
        $log->set("various", Logger::T_UPD, Logger::S_ENC, json_encode(array("obs" => "Codigo de Cliente editado", "lead_id" => "$clientID")), logger::A_NCHANGE);
        break;

    case "listar_produtos_por_encomenda":
        echo json_encode($requisitions->get_products_by_requisiton($id));
        break;

    case "listar_comments_por_encomenda":
        echo json_encode($requisitions->get_comments_by_requisiton($id));
        break;

    case "accept_requisition":
        $result = $requisitions->accept_requisition($id);
        if ($result) {
            $alert->update("S_ENC", $id);
            if ($message) {
                $alert->make($result->user, "Encomenda Aprovada Obs. $message ID:$id", "S_ENC", $id, 1);
            }
        }
        $log->set($id, Logger::T_UPD, Logger::S_ENC, json_encode(array("obs" => "Encomenda Aceite", "msg" => "$message")), logger::A_APV);
        echo json_encode($result);
        break;

    case "decline_requisition":
        $result = $requisitions->decline_requisition($id);
        if ($result) {
            $alert->make($result->user, "Encomenda Rejeitada  Motivo: $message ID:$id", "S_ENC", $id, 0);
        }
        $log->set($id, Logger::T_UPD, Logger::S_ENC, json_encode(array("obs" => "Encomenda Rejeitada", "msg" => "$message")), logger::A_DECL);
        echo json_encode($result);
        break;

    case "check_month_requisitions":
        echo json_encode($requisitions->check_month_requisitions());
        break;
    case "validate_audiograma":
        $stmt = $db->prepare("SELECT count(*) FROM `spice_audiograma` WHERE lead_id=:id AND date > date_sub(now(),INTERVAL 6 MONTH);");
        $stmt->execute(array(":id" => $lead_id));
        $row = $stmt->fetch(PDO::FETCH_NUM);

        echo json_encode($row[0] != "0");

        break;
}

