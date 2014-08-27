<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "novas_leads_followUp" . $curTime;
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
    'Tipo Cliente'), ";");

if ($type == "dispenser") {
    $dispens_cc = "NO";
} else {
    $dispens_cc = "YES";
}

$query_log = "SELECT "
        . "a.title,"
        . "a.extra1,"
        . "a.first_name,"
        . "a.middle_initial,"
        . "a.last_name,"
        . "a.address1,"
        . "a.address2,"
        . "a.address3,"
        . "a.state,"
        . "a.postal_code,"
        . "a.extra3,"
        . "a.extra10,"
        . "a.city,"
        . "a.province,"
        . "a.country_code,"
        . "a.phone_number,"
        . "a.alt_phone,"
        . "a.email,"
        . "a.date_of_birth,"
        . "a.extra2,"
        . "a.comments "
        . "FROM vicidial_list a "
        . "INNER JOIN vicidial_users b ON a.user=b.user "
        . "WHERE a.entry_date BETWEEN :data_inicial AND :data_final AND a.extra6=:type AND b.user_group=:user_group;";
$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59", ":type" => $dispens_cc, ":user_group" => $u->user_group));

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
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        $row['comments'],
        "",
        "",
            ), ";");
}

fclose($output);
