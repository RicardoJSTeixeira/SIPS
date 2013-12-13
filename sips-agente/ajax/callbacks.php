<?php

require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$js = array("ANYONE"=>0,"USERONLY"=>0);
$query = "SELECT count(callback_id) as total,recipient FROM `vicidial_callbacks` WHERE campaign_id='$campaign_id' and user='$user'  and status <> 'INACTIVE' group by recipient";
$query = mysql_query($query, $link) or die(mysql_error());
while ($row = mysql_fetch_assoc($query)) {

    $js[$row["recipient"]] =  (int)$row["total"];
}
echo json_encode($js);
