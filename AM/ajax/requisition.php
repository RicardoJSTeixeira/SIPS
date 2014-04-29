<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/ini/db.php";
require "$root/ini/dbconnect.php";
require "$root/ini/user.php";
require "$root/AM/lib_php/products.php";
require "$root/AM/lib_php/requisitions.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users($db);

$products = new products($db);
$requisitions = new requisitions($db, $user->user_level, $user->id);

switch ($action) {
    case "listar_produtos_to_datatable":
        echo json_encode($products->get_products_to_datatable());
        break;

    case "listar_requisition_to_datatable":
        echo json_encode($requisitions->get_requisitions_to_datatable($show_admin));
        break;

    case "listar_produtos":
        echo json_encode($products->get_products());
        break;

    case "criar_encomenda":
        echo json_encode($requisitions->create_requisition($type, $lead_id, $contract_number, $attachment, $products_list));
        break;

    case "editar_encomenda":
        echo json_encode($requisitions->edit_requisition($clientID, $cod_cliente));
        break;

    case "listar_produtos_por_encomenda":
        echo json_encode($requisitions->get_products_by_requisiton($id));
        break;

    case "accept_requisition":
        echo json_encode($requisitions->accept_requisition($id));
        break;

    case "decline_requisition":
        echo json_encode($requisitions->decline_requisition($id));
        break;

    case "check_month_requisitions":
        echo json_encode($requisitions->check_month_requisitions());
        break;
}

    