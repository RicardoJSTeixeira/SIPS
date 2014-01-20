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
        echo(json_encode($products->get_products_to_datatable()));
        break;

    case "listar_requisition_to_datatable":
        $result['aaData'] = [];
        $query = "SELECT id,user,type,lead_id,date,contract_number,attachment,status  from spice_requisition where user=:user";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":user" => "admin"/* $user->id */));
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[2] = $row[2] == "month" ? "Mensal" : "Especial";
            $row[3] = $row[3] == "0" ? "Não utilizado" : $row[3];

            switch ($row[7]) {
                case "0":
                    $row[7] = "Pedido enviado";
                    break;
                case "1":
                    $row[7] = "Aprovado";
                    break;
                case "2":
                    $row[7] = "Rejeitado";
                    break;
            }


            if ($user->user_level > 5) {
                $row[8] = "<div > <button class='btn ver_requisition_products' value='" . $row["id"] . "'><i class='icon-eye-open'></i>Ver</button></div>";
                $row[9] = " <div class='input-append'> <button class='btn accept_requisition btn-success' value='" . $row["id"] . "'><i class= 'icon-ok'></i>Aceitar</button><button class='btn decline_requisition btn-warning' value='" . $row["id"] . "'><i class= 'icon-remove'></i>Rejeitar</button></div>";
            } else
                $row[8] = "<div> <button class='btn ver_requisition_products' value='" . $row["id"] . "'><i class='icon-eye-open'></i>Ver</button></div>";
            $result['aaData'][] = $row;
        }
        echo json_encode($result);
        break;

    case "listar_produtos":
        echo(json_encode($products->get_products()));
        break;

    case "criar_encomenda":
        $query = "INSERT INTO `spice_requisition`( `user`, `type`, `lead_id`, `date`, `contract_number`, `attachment`, `products`,`status`) VALUES ( :user,:type,:lead_id,:date,:contract_number,:attachment,:products,:status)";
        $stmt = $db->prepare($query);
        echo $stmt->execute(array(":user" => $user->id, ":type" => $type, ":lead_id" => $lead_id, ":date" => date('Y-m-d H:i:s'), ":contract_number" => $contract_number, ":attachment" => $attachment, ":products" => json_encode($products_list), ":status" => 0));
        break;


    case "listar_produtos_por_encomenda":
        $query = "SELECT products from spice_requisition where id=:id";
        $stmt = $db->prepare($query);
        $stmt->execute(array("id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $productalia = [];

        foreach (json_decode($row["products"]) as $value) {
            $productalia[$value->id] = $value->quantity;
        }
        $query = "SELECT id,name,category from spice_product where id in ('" . join("','", array_keys($productalia)) . "') order by category asc";
        $stmt = $db->prepare($query);
        $stmt->execute();

        while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productalia[$row1["id"]] = ["name" => $row1["name"], "quantity" => $productalia[$row1["id"]], "category" => $row1["category"]];
        }
        echo(json_encode($productalia));

        break;

    case "accept_requisition":
        $query = "Update  spice_requisition set status=1 where id=:id";
        $stmt = $db->prepare($query);
        echo $stmt->execute(array("id" => $id));

        break;

    case "decline_requisition":
        $query = "Update  spice_requisition set status=2 where id=:id";
        $stmt = $db->prepare($query);
        echo $stmt->execute(array("id" => $id));
        break;

    case "check_month_requisitions":
        $query = "select count(id) count from  spice_requisition  where user=:user and date between :date_first and :date_last and type='month' and status != '2' ";
        $stmt = $db->prepare($query);
        $stmt->execute(array(":user" => $user->id, ":date_first" => date("Y-m-01") . " 00:00:00", ":date_last" => date("Y-m-t") . " 23:59:59"));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($row["count"]);
        break;
}

    