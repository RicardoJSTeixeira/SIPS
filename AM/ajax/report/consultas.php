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
        $output['aaData'] = array();
        $query = "SELECT extra2 'cod cliente',  a.extra_id as 'interaction log', a.lead_id 'sugar ref', id_reservation , a.entry_date, consulta_razao ,start_date, exame_razao, venda_razao, f.user, alias_code as 'salesperson code', extra1 'camp cod', IF(exame,'YES','NO'), feedback, terceira_pessoa "
                . "FROM sips_sd_reservations a "
                . "INNER JOIN sips_sd_resources g ON a.id_resource = g.id_resource "
                . "INNER JOIN vicidial_list d ON a.lead_id = d.lead_id "
                . "INNER JOIN spice_consulta f ON a.id_reservation=f.reserva_id "
                . "WHERE f.closed=1 limit 20000";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $terceira_pessoa = json_decode(array_pop($row));
            
            if (count($terceira_pessoa)) {
                $row[] = $terceira_pessoa->tipo;
                $row[] = $terceira_pessoa->nome;
            } else {
                $row[] = "";
                $row[] = "";
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        break;

    default :
        break;
}

