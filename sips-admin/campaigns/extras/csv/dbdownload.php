<?php
require("../../../../ini/dbconnect.php");
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}




header("Content-Disposition: attachment; filename=base_de_dados_".$download_db_id.".csv");
$output = fopen('php://output', 'w');



$query = "SELECT phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, date_of_birth, alt_phone, email, security_phrase, comments, extra1, extra2, extra3, extra4, extra5, extra6, extra7, extra8, extra9, extra10, extra11, extra12, extra13, extra14, extra15 FROM vicidial_list WHERE list_id='$download_db_id'";
$result = mysql_query($query, $link) or die(mysql_error());

while($row = mysql_fetch_assoc($result))
{
    
    fputcsv($output, array($row['phone_number'], $row['title'], $row['first_name'], $row['middle_initial'], $row['last_name'], $row['address1'], $row['address2'], $row['address3'], $row['city'], $row['state'], $row['province'], $row['postal_code'], $row['country_code'], $row['date_of_birth'], $row['alt_phone'], $row['email'], $row['security_phrase'], $row['comments'], $row['extra1'], $row['extra2'], $row['extra3'], $row['extra4'], $row['extra5'], $row['extra6'], $row['extra7'], $row['extra8'], $row['extra9'], $row['extra10'], $row['extra11'], $row['extra12'], $row['extra13'], $row['extra14'], $row['extra15']), ";");
}






   


?>