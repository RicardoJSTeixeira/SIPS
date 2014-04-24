<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";

require "$root/AM/lib_php/user.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();


switch ($action) {

    case "populate_consults"://ALL MARCAÇOES
        $u = $user->getUser();

        $query = "SELECT extra2 'cod cliente', '' as 'interaction log', a.lead_id 'sugar ref', id_reservation , a.entry_date, consulta_razao ,start_date,exame_razao,venda_razao, id_user, '' as 'salesperson code', extra1 'camp cod', IF(exame,'YES','NO') "
                . "FROM sips_sd_reservations a "
                . "INNER JOIN vicidial_list d ON a.lead_id = d.lead_id "
                . "INNER JOIN spice_consulta f ON a.id_reservation=f.reserva_id "
                . "WHERE f.closed=1 limit 20000";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $output['aaData']= $stmt->fetchAll(PDO::FETCH_NUM);
        echo json_encode($output);
        break;

    default :
        break;
}

