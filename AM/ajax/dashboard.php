<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require '../lib_php/db.php';

require '../lib_php/user.php';
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$user->confirm_login();

$variables = array();


$output = array("aaData" => array());
switch ($action) {

    case "populate_allm"://ALL MARCAÇOES
        $query = "SELECT a.lead_id,b.first_name,b.address1,a.start_date from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
                           where a.id_user=? group by a.lead_id";
        $variables[] = $user->getUser()->username;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[3] = $row[3] . "<div class='view-button'><span class='btn btn-mini ver_cliente' data-lead_id='" . $row[0] . "'><i class='icon-edit'></i>Ver Cliente</span><span class='btn btn-mini criar_encomenda' data-lead_id='" . $row[0] . "'><i class='icon-edit'></i>Criar encomenda</span></div>";
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
        break;


    case "populate_ncsm"://NOVOS CLIENTES SEM MARCAÇÂO
        /*$query = "SELECT lead_id,first_name,address1,entry_date from  vicidial_list 
 where user=? and status='NEW' and list_id=99800002 and entry_date between :beg and :end and lead_id not in (select lead_id from sips_sd_reservations)";*/
        $query = "SELECT lead_id,first_name,address1,entry_date from  vicidial_list 
 where user=:user and status='NEW' and list_id=99800002 and lead_id not in (select lead_id from sips_sd_reservations)";

        $variables["user"] = $user->getUser()->username;
        //$variables["beg"] = date("Y-m-d") . " 00:00:00";
        //$variables["end"] = date("Y-m-d") . " 23:59:59";
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[3] = $row[3] . "<div class='view-button'><span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Criar Marcação</span></div>";
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        break;



    case "populate_mp"://MARCAÇÕES PENDENTES
        $query = "SELECT b.first_name,a.start_date from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
                           where a.id_user=? and a.end_date<? order by a.end_date asc";
        $variables[] = $user->getUser()->username;
        $variables[] = date("Y-m-d");
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($row);
        break;



    case "get_consultas":
        $query = "SELECT a.id_reservation,a.start_date,a.end_date,a.lead_id,b.first_name,b.address1,b.date_of_birth,c.consulta,c.exame,c.venda from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
              left join spice_consulta c on c.lead_id=a.lead_id 
            where id_user=?";
        $variables[] = "sandraaguiar";
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($row);
        break;
};
