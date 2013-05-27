<?php
require('dbconnect.php');
if (isset($_GET["ACTION"])) {$ACTION= $_GET["ACTION"];
} elseif (isset($_POST["ACTION"])) {$ACTION = $_POST["ACTION"];}
if (isset($_GET["user"])) {$user= $_GET["user"];
} elseif (isset($_POST["user"])) {$user = $_POST["user"];}

################################################################################
### update force ready
################################################################################
if ($ACTION == 'updateforceready')
{
$query = "INSERT INTO sips_forceready_control (user) VALUES ('$user')";
$query = mysql_query($query, $link);
//echo "completed";
}
?>