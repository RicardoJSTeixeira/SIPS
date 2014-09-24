<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";
require "$root/AM/lib_php/user.php";
require "$root/AM/lib_php/products.php";
require "$root/AM/lib_php/logger.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();

$products = new products($db);

$log = new Logger($db, $user->getUser());
switch ($action) {



    case "listar_produtos_to_datatable":
        echo json_encode($products->get_products_to_datatable($product_editable));
        break;

    case "criar_produto":
        echo json_encode($products->add_product($name, $max_req_m, $max_req_s, $parent, $category, $type, $color, $active, $size));

        break;

    case "get_produtos":
        echo json_encode($products->get_products());
        break;

    case "get_produto_by_id":
        echo json_encode($products->get_products($id));
        break;

    case "apagar_produto_by_id":
        echo json_encode($products->remove_product($id));
        $log->set($id, Logger::T_RM, Logger::S_PROD, json_encode(array("product" => $products->get_product_name($id))),0);
        break;

    case "edit_product":
        $produto = new product($db, $id);
        echo json_encode($produto->edit_product($name, $max_req_m, $max_req_s, $parent, $category, $type, $color, $active, $size));
        $log->set($id, Logger::T_UPD, Logger::S_PROD, json_encode(array("product" => $name)),3);
        break;

    case "add_promotion":
        $produto = new product($db, $id);
        $last_insert_id = $produto->add_promotion($active, $highlight, $data_inicio, $data_fim);
        $log->set($last_insert_id, Logger::T_INS, Logger::S_PROM, json_encode(array("type" => "Criar Promoção", "active" => $active, "highlight" => $highlight, "data_inicio" => $data_inicio, "data_fim" => $data_fim, "produto" => $products->get_product_name($id))),1);
        echo json_encode(true);
        break;

    case "remove_promotion":
        $produto = new product($db, $id);
        echo $produto->remove_promotion($id_promotion);
        $log->set($id_promotion, Logger::T_RM, Logger::S_PROM, json_encode(array("type" => "Remover Promoção", "produto" => $products->get_product_name($id))),0);
        break;

    case "get_promotion":
        $produto = new product($db, $id);
        echo json_encode($produto->get_promotion());
        break;
}

    