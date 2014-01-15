<?php

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

require "$root/ini/db.php";
require "$root/ini/dbconnect.php";
require "$root/ini/user.php";
require "$root/AM/lib/products.php";
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
        $temp = $products->get_products_to_datatable();
        foreach ($temp as &$value) {
            foreach ($value as &$value2) {
                $value2[3] = $value2[3] == "1" ? "sim" : "nao";
            };
        };
        echo(json_encode($temp));
        break;

    case "listar_requisition_to_datatable":
        $query = "SELECT id,user,type,lead_id,date,contract_number,attachment,products from spice_requisition where user=:user";
        $stmt = $db->prepare($query);

        $stmt->execute(array(":user" => $user->id));
        $result = [];
        $contracts = [];

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[2] = $row[2] == "month" ? "Mensal" : "Especial";
    
            $row[7] = json_decode($row[7]);
            $result['aaData'][] = $row;
            $contracts = [];
        }
        echo json_encode($result);
        break;

    case "listar_produtos":
        echo(json_encode($products->get_products()));
        break;

    case "criar_encomenda":
        $query = "INSERT INTO `spice_requisition`( `user`, `type`, `lead_id`, `date`, `contract_number`, `attachment`, `products`) VALUES ( :user,:type,:lead_id,:date,:contract_number,:attachment,:products)";
        $stmt = $db->prepare($query);
        echo $stmt->execute(array(":user" => $user->id, ":type" => $type, ":lead_id" => $lead_id, ":date" => date('Y-m-d H:i:s'), ":contract_number" => $contract_number, ":attachment" => $attachment, ":products" => json_encode($products_list)));
        break;
}