<?php

header('Content-Encoding: UTF-8');
//header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="tabledata.csv"');
echo "\xEF\xBB\xBF";
if (isset($_POST['csv'])) {
    $csv = $_POST['csv'];
    echo $csv;
}