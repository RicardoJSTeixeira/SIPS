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
        $u = $user->getUser();
        if ($u->user_level > 5) {
            $query = "SELECT a.lead_id, b.first_name, extra1, extra2, middle_initial, postal_code, b.address1, a.start_date, a.id_user from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
            inner join vicidial_users c on b.user=c.user
            where c.user_group=:user_group group by a.lead_id limit 20000";
            $variables[":user_group"] = $u->user_group;
        } else {
            $query = "SELECT a.lead_id, b.first_name, extra1, extra2, middle_initial, postal_code, b.address1, a.start_date from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
            where a.id_user=:user group by a.lead_id limit 20000";
            $variables[":user"] = $u->username;
        }
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[7] = $row[7] . "<div class='view-button'><span class='btn btn-mini ver_cliente' data-lead_id='" . $row[0] . "'><i class='icon-edit'></i>Cliente</span><span class='btn btn-mini criar_encomenda' data-lead_id='" . $row[0] . "'><i class='icon-edit'></i>Encomenda</span><span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Marcação</span></div>";
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
        break;

    case "populate_ncsm"://NOVOS CLIENTES SEM MARCAÇÂO
        $u = $user->getUser();
        if ($u->user_level > 5) {
            $query = "SELECT lead_id, first_name, extra1, extra2, middle_initial, postal_code, address1, entry_date, a.user from  vicidial_list a
                inner join vicidial_users b on a.user=b.user
                where b.user_group=:user_group and status='NEW' and list_id=99800002 and lead_id not in (select lead_id from sips_sd_reservations) limit 20000";
            $variables[":user_group"] = $u->user_group;
        } else {
            $query = "SELECT lead_id, first_name, extra1, extra2, middle_initial, postal_code, address1, entry_date from  vicidial_list 
                where user=:user and status='NEW' and list_id=99800002 and lead_id not in (select lead_id from sips_sd_reservations) limit 20000";
            $variables[":user"] = $u->username;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[7] = $row[7] . "<div class='view-button'><span class='btn btn-mini criar_marcacao' data-lead_id='$row[0]'><i class='icon-edit'></i>Marcação</span></div>";
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        break;

    case "populate_mp"://MARCAÇÕES PENDENTES
        $query = "SELECT b.first_name, a.start_date from sips_sd_reservations a 
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
        $query = "SELECT a.id_reservation, a.start_date, a.end_date, a.lead_id, b.first_name, b.address1, b.date_of_birth, c.consulta, c.exame, c.venda from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
            left join spice_consulta c on c.lead_id=a.lead_id 
            where id_user=?";
        $variables[] = "sandraaguiar";
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($row);
        break;
}