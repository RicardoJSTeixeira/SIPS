<?php

require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$js = array();
$query = "SELECT count(callback_id) as total FROM `vicidial_callbacks` WHERE campaign_id='$campaign_id' and user='$user' and status <> 'INACTIVE'";
$query = mysql_query($query, $link) or die(mysql_error());
$row = mysql_fetch_assoc($query);
echo json_encode($row["total"]);
?>
