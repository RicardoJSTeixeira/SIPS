<?php

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tabledata.csv"');
if (isset($_POST['csv'])) {
    $csv = $_POST['csv'];
    echo $csv;
}