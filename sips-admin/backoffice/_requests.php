<?php
date_default_timezone_set('Europe/Lisbon');

require("../../ini/_json_convert.php");
require("../../ini/dbconnect.php");
require("../../ini/functions.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

if($action=="user_change_status")
{
   $query = "UPDATE vicidial_users set active='$active' WHERE user_id='$user'";
   $query = mysql_query($query, $link) or die(mysql_error());
   echo json_encode(array($active));
}

?>