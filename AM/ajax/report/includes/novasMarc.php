<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "novas_marc_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

fputcsv($output, array(
    'Title',
    'Campaign No.',
    'First Name',
    'Middle Name',
    'Surname',
    'Address 1',
    'Address 2',
    'Address 3',
    'County',
    'Post Code',
    'Area Code',
    'No. Porta',
    'City',
    'Concelho',
    'Country Code',
    'Phone No.',
    'Mobile Phone No.',
    'Work Phone No.',
    'Email',
    'Date of Birth',
    'No.',
    'Update contact',
    'Service Request',
    'Territory Code',
    'Salesperson Code',
    'On Hold',
    'Exclude Reason Code',
    'Pensionner',
    'Want Info from other companies',
    'Appointment time',
    'Appointment date',
    'Visit Location',
    'Branch',
    'Comments',
    'Salesperson Team',
    'Tipo Cliente',
    'To be Issued',
    'ID Consulta SPICE'), ";");

$query_log = "SELECT "
        . "a.start_date, "
        . "a.id_reservation, "
        . "b.*, "
        . "c.display_text reservation_text, "
        . "d.alias_code "
        . " FROM sips_sd_reservations a "
        . "INNER JOIN vicidial_list b ON a.lead_id = b.lead_id "
        . "INNER JOIN sips_sd_reservations_types c ON a.id_reservation_type = c.id_reservations_types "
        . "INNER JOIN sips_sd_resources d ON a.id_resource = d.id_resource "
        . "INNER JOIN vicidial_users e ON a.id_user = e.user "
        . "AND a.entry_date BETWEEN :data_inicial AND :data_final AND e.user_group=:user_group";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59", ":user_group" => $u->user_group));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    fputcsv($output, array(
        $row['title'],
        $row['extra1'],
        $row['first_name'],
        $row['middle_initial'],
        $row['last_name'],
        $row['address1'],
        $row['address2'],
        $row['address3'],
        $row['state'],
        $row['postal_code'],
        $row['extra3'],
        $row['extra10'],
        $row['city'],
        $row['province'],
        $row['country_code'],
        $row['phone_number'],
        $row['alt_phone'],
        "",
        $row['email'],
        $row['date_of_birth'],
        $row['extra2'],
        "",
        "",
        "",
        ((stripos($row['alias_code'], "B/") !== FALSE) ? "Branch" : ((stripos($row['alias_code'], "C/") !== FALSE) ? "Cato" : "Casa")),
        "",
        "",
        "",
        "",
        date("H:i", strtotime($row['start_date'])),
        date("d-m-Y", strtotime($row['start_date'])),
        $row['reservation_text'],
        "",
        $row['comments'],
        "",
        "",
        $row['extra6'],
        $row['id_reservation']
            ), ";");
};
fclose($output);
