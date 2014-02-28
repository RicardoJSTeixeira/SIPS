<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="report_sms_'.date('o-m-d_h_m_s').'.csv"');
if (isset($_POST['csv'])) {
    $csv = $_POST['csv'];
    echo $csv;
}
?>