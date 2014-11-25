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
        $calendar = new Calendars($db);
        $rs = $calendar->getResTypeRaw();
        $rs = implode(",", $rs);
        if ($u->user_level > 6) {
            $query = "SELECT 
                a.lead_id,
                extra1, 
                extra2, 
                extra8 'nif', 
                CONCAT(first_name, ' ', middle_initial, ' ', last_name),
                CONCAT(b.address1, ' ', IFNULL(b.address2,'')),
                postal_code, 
                b.city,
                b.phone_number,
                b.alt_phone,
                IF(d.closed=1,'Fechada',IF(a.start_date>NOW(),'Marcada','Aberta')) estado,
                a.start_date, 
                d.feedback, 
                d.consulta_razao,
                d.venda_razao,
                d.exame_razao, 
                d.user,
                a.id_reservation,
                e.alias_code
        FROM sips_sd_reservations a
   INNER JOIN sips_sd_resources e ON a.id_resource=e.id_resource
   INNER JOIN vicidial_list b ON a.lead_id=b.lead_id
   INNER JOIN vicidial_users c ON b.user=c.user
   LEFT JOIN spice_consulta d ON d.reserva_id=a.id_reservation
   WHERE c.user_group=:user_group AND DATE(a.start_date) > DATE(NOW() - INTERVAL 3 MONTH) AND a.id_reservation_type IN ($rs) AND a.gone=0 limit 20000";
            $variables[":user_group"] = $u->user_group;
        } else {
            $calendar = new Calendars($db);
            $refs = $calendar->_getRefs($u->username);
            $refs = array_map(function($a) {
                return $a->id;
            }, $refs);
            $refs=implode("','", $refs);
            $query = "SELECT 
                a.lead_id, 
                extra1, 
                extra2,
                extra8 'nif',
                CONCAT(first_name, ' ', middle_initial, ' ', last_name),
                CONCAT(b.address1, ' ', IFNULL(b.address2,'')),
                postal_code,
                b.city,
                b.phone_number,
                b.alt_phone,
                IF(c.closed=1,'Fechada',IF(a.start_date>NOW(),'Marcada','Aberta')) estado,
                a.start_date, 
                c.feedback, 
                c.consulta_razao,
                c.venda_razao,
                c.exame_razao,
                c.user,
                a.id_reservation,
                e.alias_code
        FROM sips_sd_reservations a
   INNER JOIN sips_sd_resources e ON a.id_resource=e.id_resource
   INNER JOIN vicidial_list b on a.lead_id=b.lead_id 
   LEFT JOIN spice_consulta c on c.reserva_id=a.id_reservation
   WHERE a.id_resource in ('$refs') AND DATE(a.start_date)>DATE(NOW() - INTERVAL 3 MONTH) AND a.id_reservation_type in ($rs) AND a.gone=0 limit 20000";
        }
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {

            if (strlen($row[14]))
                $row[13] = $row[14];
            else if (strlen($row[15]))
                $row[13] = $row[15];
            
            $row[15] = $row[16] . "<div class='view-button'>"
                    . "<button class='btn btn-mini icon-alone ver_cliente' data-lead_id='$row[0]' title='Ver Cliente'><i class='icon-edit'></i></button>"
                                       . "<button class='btn btn-mini icon-alone ver_consulta' data-lead_id='$row[0]' data-reserva_id='$row[17]' title='Ver Consulta'><i class='icon-user-md'></i></button>"
//. "<button class='btn btn-mini icon-alone recomendacoes' data-lead_id='$row[0]' title='Recomendados'><i class='icon-plus-sign'></i></button>"
                    . "</div>";

            $row[14]=$row[18];
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
        break;

    case "populate_ncsm"://NOVOS CLIENTES SEM MARCAÇÂO
        $u = $user->getUser();
        $variables[":list"] = $u->list_id;
        if ($u->user_level > 6) {
            $query = "SELECT 
                a.lead_id,
                extra1,
                extra2, 
                extra8 'nif',
                CONCAT(first_name, ' ', middle_initial, ' ', last_name), 
                CONCAT(address1, ' ', IFNULL(address2,'')),
                postal_code, 
                city,
                phone_number,
                alt_phone,
                a.entry_date,
                a.user FROM vicidial_list a
    INNER JOIN vicidial_users b ON a.user=b.user
    LEFT JOIN sips_sd_reservations c ON a.lead_id=c.lead_id
    WHERE b.user_group=:user_group AND list_id=:list AND (c.lead_id IS NULL OR c.gone=0) AND extra6='NO' limit 20000";
            $variables[":user_group"] = $u->user_group;
        } else {
            $siblings=implode("','", $u->siblings);
            $query = "SELECT
            a.lead_id,
            extra1,
            extra2,
            extra8 'nif',
            CONCAT(first_name, ' ', middle_initial, ' ', last_name),
            CONCAT(address1, ' ', IFNULL(address2,'')),
            postal_code,
            city,
            phone_number,
            alt_phone,
            a.entry_date,
            a.user
            FROM vicidial_list a
    LEFT JOIN sips_sd_reservations b ON a.lead_id=b.lead_id
    WHERE a.user IN ('$siblings') AND list_id=:list AND (b.lead_id IS NULL or b.gone=0) AND extra6='NO' limit 20000";
        }

        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[10] = $row[10] . "<div class='view-button'>"
                    . "<button class='btn btn-mini icon-alone ver_cliente' data-lead_id='$row[0]' title='Ver Cliente'><i class='icon-edit'></i></button>"

                    //. "<button class='btn btn-mini icon-alone recomendacoes' data-lead_id='$row[0]' title='Recomendados'><i class='icon-plus-sign'></i></button>"
                    . "</div>";
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        break;

    case "populate_ncsm_r"://NOVOS CLIENTES SEM MARCAÇÂO Recomendados
        $u = $user->getUser();
        $variables[":list"] = $u->list_id;
        if ($u->user_level > 6) {
            $query = "SELECT 
                CONCAT(b.first_name, ' ', b.middle_initial, ' ', b.last_name),
                b.extra2, 
                a.lead_id, 
                CONCAT(a.first_name, ' ', a.middle_initial, ' ', a.last_name),
                a.extra1,
                a.extra2, 
                a.extra8 'nif', 
                a.postal_code, 
                a.city,
                a.phone_number, 
                a.alt_phone, 
                a.address1, 
                a.entry_date,
                a.user 
    FROM vicidial_list a 
    INNER JOIN vicidial_list b on a.extra7=b.lead_id 
    INNER JOIN vicidial_users c on a.user=c.user
    LEFT JOIN sips_sd_reservations d on a.lead_id=d.lead_id
    WHERE c.user_group=:user_group AND a.list_id=:list AND (d.lead_id IS NULL or d.gone=0) AND a.extra6='NO' AND DATE(d.start_date) > DATE(NOW() - INTERVAL 3 MONTH) limit 20000";
            $variables[":user_group"] = $u->user_group;
        } else {
            $siblings=implode("','", $u->siblings);
            $query = "SELECT
                CONCAT(b.first_name, ' ', b.middle_initial, ' ', b.last_name),
                b.extra2,
                a.lead_id,
                CONCAT(a.first_name, ' ', a.middle_initial, ' ', a.last_name), 
                a.extra1,
                a.extra2,
                a.extra8 'nif',
                a.postal_code, 
                a.city,
                a.phone_number,
                a.alt_phone, 
                a.address1, 
                a.entry_date
    FROM vicidial_list a 
    INNER JOIN vicidial_list b on a.extra7=b.lead_id 
    LEFT JOIN sips_sd_reservations c on a.lead_id=c.lead_id
    WHERE c.id_user in ('$siblings') AND a.list_id=:list AND (c.lead_id IS NULL or c.gone=0) AND a.extra6='NO' AND DATE(c.start_date) > DATE(NOW() - INTERVAL 3 MONTH) limit 20000";
        }

        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $row[12] = $row[12] . "<div class='view-button'>"
                    . "<button class='btn btn-mini icon-alone ver_cliente' data-lead_id='$row[2]' title='Ver Cliente'><i class='icon-edit'></i></button>"
                  
                    //. "<button class='btn btn-mini icon-alone recomendacoes' data-lead_id='$row[2]' title='Recomendados'><i class='icon-plus-sign'></i></button>"
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

        $rs = $calendar->getResTypeRaw();
        $rs = implode(",", $rs);
        $query = "SELECT * FROM (SELECT first_name, middle_initial, last_name, a.start_date, a.lead_id, a.id_reservation, a.end_date, '' closed, e.alias_code
                       FROM sips_sd_reservations a
                       INNER JOIN sips_sd_resources e on a.id_resource=e.id_resource
                       INNER JOIN vicidial_list b on a.lead_id=b.lead_id
                       LEFT JOIN spice_consulta c on c.reserva_id=a.id_reservation
                       WHERE a.id_resource IN ($refs) AND a.id_reservation_type in ($rs) AND a.end_date<:date AND c.id IS NULL AND DATE(a.start_date) > DATE(NOW() - INTERVAL 3 MONTH) AND a.gone=0
                       UNION ALL
                       SELECT first_name, middle_initial, last_name, a.start_date, a.lead_id, a.id_reservation,a.end_date,'...por fechar' closed, e.alias_code
                       FROM sips_sd_reservations a
                       INNER JOIN sips_sd_resources e on a.id_resource=e.id_resource
                       INNER JOIN vicidial_list b on a.lead_id=b.lead_id
                       LEFT JOIN spice_consulta c on c.reserva_id=a.id_reservation
                       WHERE a.id_resource IN ($refs) AND a.id_reservation_type in ($rs) AND a.end_date<:date1 AND c.closed=0 AND DATE(a.start_date) > DATE(NOW() - INTERVAL 3 MONTH) AND a.gone=0 ) a ORDER BY a.end_date ASC";
        $variables[":date"] = date("Y-m-d");
        $variables[":date1"] = date("Y-m-d");
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $data[] = array("first_name" => (string) $row->first_name . " " . $row->middle_initial . " " . $row->last_name . "<span class='right'>$row->closed</span>". "<span class='right'>$row->alias_code</span>", "start_date" => $row->start_date, "lead_id" => $row->lead_id, "id_reservation" => $row->id_reservation);
        }
        echo json_encode($data);
        break;
}