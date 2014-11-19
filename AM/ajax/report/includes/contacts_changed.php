<?php

require "$root/AM/lib_php/calendar.php";
$calendar = new Calendars($db);

$curTime = date("Y-m-d_H:i:s");
$filename = "novas_marc_" . $curTime;

$rs = $calendar->getResTypeRaw();
$rs = implode(",", $rs);

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
    'Insurance Scheme Presc.',
    'Date of Birth',
    'No.',
    'Update contact'), ";");


$query_log = "SELECT
                *
                FROM vicidial_list
                WHERE modify_date BETWEEN :data_inicial AND :data_final AND validation='1'";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));

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
        $row['extra5'],
        $row['date_of_birth'],
        $row['extra2'],
        "",
        "1"), ";");
}

fclose($output);