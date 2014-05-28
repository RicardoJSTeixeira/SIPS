<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require '../lib_php/db.php';
require "../lib_php/calendar.php";

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
            $query = "SELECT a.lead_id, b.first_name, extra1, extra2, middle_initial, postal_code, b.city, b.phone_number, b.alt_phone, b.address1, IF(d.closed=1,'Fechada',IF(a.start_date>NOW(),'Marcada','Aberta')) estado, a.start_date, a.id_user from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
            inner join vicidial_users c on b.user=c.user
            left join spice_consulta d on d.reserva_id=a.id_reservation
            where c.user_group=:user_group and DATE(a.start_date)>'2014-05-01' group by a.lead_id limit 20000";
            $variables[":user_group"] = $u->user_group;
        } else {
            $query = "SELECT a.lead_id, b.first_name, extra1, extra2, middle_initial, postal_code, b.city, b.phone_number, b.alt_phone, b.address1, IF(c.closed=1,'Fechada',IF(a.start_date>NOW(),'Marcada','Aberta')) estado, a.start_date from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
            left join spice_consulta c on c.reserva_id=a.id_reservation
            where a.id_user=:user and DATE(a.start_date)>'2014-05-01' group by a.lead_id limit 20000";
            $variables[":user"] = $u->username;
        }
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[11] = $row[11] . "<div class='view-button'>"
                    . "<button class='btn btn-mini icon-alone ver_cliente' data-lead_id='$row[0]' title='Ver Cliente'><i class='icon-edit'></i></button>"
                    . "<button class='btn btn-mini icon-alone criar_encomenda".(($row[10]!=='Fechada')?" hide":"")."' data-lead_id='$row[0]' title='Nova Encomenda'><i class='icon-shopping-cart'></i></button>"
                    . "<button class='btn btn-mini icon-alone criar_marcacao' data-lead_id='$row[0]' title='Marcar Consulta'><i class='icon-calendar'></i></button>"
                    . "<button class='btn btn-mini icon-alone recomendacoes' data-lead_id='$row[0]' title='Recomendados'><i class='icon-plus-sign'></i></button>"
                    . "</div>";
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
        break;

    case "populate_ncsm"://NOVOS CLIENTES SEM MARCAÇÂO
        $u = $user->getUser();
        $variables[":list"] = $u->list_id;
        if ($u->user_level > 5) {
            $query = "SELECT a.lead_id, first_name, extra1, extra2, middle_initial, postal_code, city, phone_number, alt_phone, address1, a.entry_date, a.user from  vicidial_list a
                inner join vicidial_users b on a.user=b.user
                left join sips_sd_reservations c on a.lead_id=c.lead_id
                where b.user_group=:user_group and list_id=:list and c.lead_id is null and extra6='NO' and DATE(c.start_date)>'2014-05-01' limit 20000";
            $variables[":user_group"] = $u->user_group;
        } else {
            $query = "SELECT a.lead_id, first_name, extra1, extra2, middle_initial, postal_code, city, phone_number, alt_phone, address1, a.entry_date from  vicidial_list a
                left join sips_sd_reservations b on a.lead_id=b.lead_id
                where user=:user and list_id=:list and b.lead_id is null and extra6='NO' and DATE(c.start_date)>'2014-05-01' limit 20000";
            $variables[":user"] = $u->username;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[10] = $row[10] . "<div class='view-button'>"
                    . "<button class='btn btn-mini icon-alone ver_cliente' data-lead_id='$row[0]' title='Ver Cliente'><i class='icon-edit'></i></button>"
                    . "<button class='btn btn-mini icon-alone criar_marcacao' data-lead_id='$row[0]' title='Marcar Consulta'><i class='icon-calendar'></i></button>"
                    . "<button class='btn btn-mini icon-alone recomendacoes' data-lead_id='$row[0]' title='Recomendados'><i class='icon-plus-sign'></i></button>"
                    . "</div>";
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        break;

    case "populate_ncsm_r"://NOVOS CLIENTES SEM MARCAÇÂO Recomendados
        $u = $user->getUser();
        $variables[":list"] = $u->list_id;
        if ($u->user_level > 5) {
            $query = "SELECT b.first_name, b.extra2, a.lead_id, a.first_name, a.extra1, a.extra2, a.middle_initial, a.postal_code, a.city, a.phone_number, a.alt_phone, a.address1, a.entry_date, a.user 
                FROM `vicidial_list` a 
                inner join `vicidial_list` b on a.extra7=b.lead_id 
                inner join vicidial_users c on a.user=c.user
                left join sips_sd_reservations d on a.lead_id=d.lead_id
                where c.user_group=:user_group and a.list_id=:list and d.lead_id is null and a.extra6='NO' and DATE(d.start_date)>'2014-05-01' limit 20000";
            $variables[":user_group"] = $u->user_group;
        } else {
            $query = "SELECT b.first_name, b.extra2, a.lead_id, a.first_name, a.extra1, a.extra2, a.middle_initial, a.postal_code, a.city, a.phone_number, a.alt_phone, a.address1, a.entry_date
                FROM `vicidial_list` a 
                inner join `vicidial_list` b on a.extra7=b.lead_id 
                left join sips_sd_reservations c on a.lead_id=c.lead_id
                where a.user=:user and a.list_id=:list and c.lead_id is null and a.extra6='NO' and DATE(c.start_date)>'2014-05-01' limit 20000";
            $variables[":user"] = $u->username;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[12] = $row[12] . "<div class='view-button'>"
                    . "<button class='btn btn-mini icon-alone ver_cliente' data-lead_id='$row[2]' title='Ver Cliente'><i class='icon-edit'></i></button>"
                    . "<button class='btn btn-mini icon-alone criar_marcacao' data-lead_id='$row[2]' title='Marcar Consulta'><i class='icon-calendar'></i></button>"
                    . "<button class='btn btn-mini icon-alone recomendacoes' data-lead_id='$row[2]' title='Recomendados'><i class='icon-plus-sign'></i></button>"
                    . "</div>";
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        break;

    case "populate_mp"://MARCAÇÕES PENDENTE
        $username = $user->getUser()->username;
        $calendar = new Calendars($db);
        $refs = $calendar->_getRefs($username);
        $refs = array_map(function($a) {
            return $a->id;
        }, $refs);
        $refs = implode(",", $refs);
        $query = "SELECT * FROM (SELECT b.first_name, a.start_date, a.lead_id, a.id_reservation,a.end_date,'' closed from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
            left join spice_consulta c on c.reserva_id=a.id_reservation
            where a.id_resource in ($refs) and a.end_date<:date and c.id is NULL and DATE(a.start_date)>'2014-05-01'
            UNION ALL
            SELECT b.first_name, a.start_date, a.lead_id, a.id_reservation,a.end_date,'...por fechar' closed from sips_sd_reservations a 
            left join vicidial_list b on a.lead_id=b.lead_id 
            left join spice_consulta c on c.reserva_id=a.id_reservation
            where a.id_resource in ($refs) and a.end_date<:date1 and c.closed=0 and DATE(a.start_date)>'2014-05-01') a order by a.end_date asc";
        $variables[":date"] = date("Y-m-d");
        $variables[":date1"] = date("Y-m-d");
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $data[] = array("first_name" => (string) $row->first_name . "<span class='right'>$row->closed</span>", "start_date" => $row->start_date, "lead_id" => $row->lead_id, "id_reservation" => $row->id_reservation);
        }
        echo json_encode($data);
        break;
}