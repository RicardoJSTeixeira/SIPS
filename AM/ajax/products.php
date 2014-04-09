<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/ini/db.php";
require "$root/ini/dbconnect.php";
require "$root/ini/user.php";
require "$root/AM/lib_php/products.php";

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users($db);

$products = new products($db);


switch ($action) {

    

    case "listar_produtos_to_datatable":
        echo json_encode($products->get_products_to_datatable($product_editable));
        break;

    case "criar_produto":
        echo json_encode($products->add_product($name,$price, $max_req_m, $max_req_s, $parent, $category, $type, $color, $active));
        break;

    case "get_produtos":
        echo json_encode($products->get_products());
        break;

    case "get_produto_by_id":
        $produto = new product($db, $id);
        echo json_encode($produto->get_info());
        break;

    case "apagar_produto_by_id":
        echo json_encode($products->remove_product($id));
        break;

    case "edit_product":
        $produto = new product($db, $id);
        echo json_encode($produto->edit_product($name,$price, $max_req_m, $max_req_s, $parent, $category, $type, $color, $active));
        break;

    case "add_promotion":
        $produto = new product($db, $id);
        echo $produto->add_promotion($active, $highlight, $data_inicio, $data_fim);
        break;

    case "remove_promotion":
        $produto = new product($db, $id);
        echo $produto->remove_promotion($id_promotion);
        break;

    case "get_promotion":
        $produto = new product($db, $id);
        echo json_encode($produto->get_promotion());
        break;
}

    