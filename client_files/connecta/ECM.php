<?php

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

if (strlen($fieldValue) != 9) {
    echo (json_encode(array($fieldId, false)));
} else {

    $calc = 9 * $fieldId[0] + 8 * $fieldId[1] + 7 * $fieldId[2] + 6 * $fieldId[3] + 5 * $fieldId[4] + 4 * $fieldId[5] + 3 * $fieldId[6] + 2 * $fieldId[7] + $fieldId[8];
    $calc = $calc % 11;

    if ((int) $calc == 0) {
        $link = mssql_connect('172.16.5.2', 'gocontact', '') or die(mssql_get_last_message());
        mssql_select_db('dbo', $link);
        $sql = @mssql_query("SELECT NIF FROM dbo.NIF_CITI WHERE NIF = '$fieldValue'", $link) or die(mssql_get_last_message());
        if (mssql_num_rows($sql) > 0) {
            echo (json_encode(array($fieldId, false)));
        } else {
            echo (json_encode(array($fieldId, true)));
        }
        mssql_close($link);
    } else {
        echo (json_encode(array($fieldId, false)));
    }
}
?>
