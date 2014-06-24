<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "novas_leads_followUp" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');


fputcsv($output, array('Title',
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
    'Operador',
    'Feedback',
    'Campanha',
    'Data da Chamada',
    'Avisos'), ";");
if ($type = "dispenser") {
    $dispens_cc = "and c.extra6='NO'";
} else {
    $dispens_cc = "and c.extra6='YES'";
}

$query_log = "SELECT a.lead_id,a.campaign_id AS linhainbound,a.call_date AS data,a.status AS resultado,a.user as utilizador, b.*, c.*, d.campaign_name AS campanha FROM vicidial_closer_log a JOIN vicidial_agent_log b ON a.uniqueid = b.uniqueid JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_campaigns d ON b.campaign_id = d.campaign_id where a.status IN ('NL') AND a.call_date BETWEEN :data_inicial AND :data_final AND a.campaign_id ='w00003' $dispens_cc";
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
        $row['date_of_birth'],
        $no,
        "",
        "",
        "",
        $cod,
        "",
        "",
        "",
        "",
        $custom_row['marchora'],
        $custom_row['marcdata'],
        $custom_row['tipoconsulta'],
        "",
        $custom_row['obs'],
        "",
        "",
        $row['utilizador'],
        $row['resultado'],
        $row['linhainbound'],
        $row['data']
            ), ";");
};
fclose($output);

