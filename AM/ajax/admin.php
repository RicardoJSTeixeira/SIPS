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

$product = new products($db);

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

    case "listar_produtos":
        echo(json_encode($product->get_products()));
        break;
    case "apagar_produto":
         echo(json_encode($product->remove_product()));
        break;
    case "criar_produto":
         echo(json_encode($product->add_product()));
        break;
    case "editar_produto":
         echo(json_encode($product->edit_product()));
        break;
}
