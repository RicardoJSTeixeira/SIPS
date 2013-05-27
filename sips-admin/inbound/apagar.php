<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");


				$stmtA = "SELECT * FROM `vicidial_list` WHERE list_id in (319,341,413,466,471,110327,110331,110364,110365,110386,110387,110406,110407,110410,110545,110546,110549,110602) and status = 'NAOEX'";
				
				$result = mysql_query($stmtA);
if (!$result) die('Couldn\'t fetch records');
$num_fields = mysql_num_fields($result);
$headers = array();
for ($i = 0; $i < $num_fields; $i++) {
    $headers[] = mysql_field_name($result , $i);
}
$fp = fopen('php://output', 'w');
if ($fp && $result) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    fputcsv($fp, $headers);
    while ($row = mysql_fetch_row($result)) {
        fputcsv($fp, array_values($row));
    }
    die;
}
?>
