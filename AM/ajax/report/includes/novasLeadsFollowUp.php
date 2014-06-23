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
    'Comments'
        ), ";");

$query_log = "SELECT  a.* from vicidial_list a where  a.entry_date BETWEEN :data_inicial AND :data_final  ";

$stmt = $db->prepare($query_log);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:0", ":data_final" => "$data_final 23:59:59"));

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
        $row['comments']
            ), ";");
};
fclose($output);

