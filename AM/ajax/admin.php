<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require '../../ini/db.php';
require '../../ini/dbconnect.php';
require '../../ini/user.php';
require '../lib/products.php';
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new users($db);

$products = new products($db);

switch ($action) {



    case "download_excel_csm":
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=Report_Sem_marcaçao_' . date("Y-m-d_H:i:s") . '.csv');
        $output = fopen('php://output', 'w');
        $query = "SELECT lead_id,first_name,address1,entry_date from  vicidial_list 
                    where user=? and status='NEW' and list_id=99800002  and lead_id not in (select lead_id from sips_sd_reservations)";
        $variables[] = $user->id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        fputcsv($output, array("Lead", "Nome", "Morada", "Data"), ";", '"');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row, ";", '"');
        }
        fclose($output);
        break;

    case "listar_produtos_to_datatable":
        $temp = $products->get_products_to_datatable();
        foreach ($temp as &$value) {
            foreach ($value as &$value2) {
                $value2[3] = $value2[3] == "1" ? "sim" : "nao";
                $value2[7] = $value2[7] . "<div class='view-button input-append'><span   data-product_id='" . $value2["id"] . "' class='btn item_edit_button btn-primary'>Ver/Editar</span><span   data-product_id='" . $value2["id"] . "' class='btn item_delete_button btn-danger '>Remover</span></div>";
            };
        };
        echo(json_encode($temp));
        break;

    case "listar_produtos_to_datatable_by_parent":

        $temp = ($products->get_products_to_datatable_by_id($parent));
        foreach ($temp as &$value) {
            foreach ($value as &$value2) {
                $value2[2] = $value2[2] == "1" ? "sim" : "nao";
            };
        };
        echo(json_encode($temp));

        break;

    case "listar_produtos":
        echo(json_encode($products->get_products()));
        break;


    case "apagar_produto":
        echo(json_encode($products->remove_product($id)));
        break;
    case "apagar_produtos":
        echo(json_encode($products->remove_products( )));
        break;
    case "criar_produto":
        echo(json_encode($products->add_product($name, $parent, $alone, $max_req_m, $max_req_w, $category)));
        break;


    case "editar_produto":
        $product = new product($db, $id);
        echo($product->edit_product($name, $parent, $alone, $max_req_m, $max_req_w, $category));
        break;
    case "listar_produto":
        $product = new product($db, $id, 0);
        echo(json_encode($product->get_info()));
        break;
}
