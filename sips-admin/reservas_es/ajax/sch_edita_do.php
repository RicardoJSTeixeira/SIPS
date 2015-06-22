<?php
require_once('../func/reserve_utils.php');
if (!(isset($_POST["display_text"]) && isset($_POST["alias_code"]) && isset($_POST["display_days"]) && isset($_POST["blocks"]) && isset($_POST["begin_time"]) && isset($_POST["end_time"]) && isset($_POST["active"]) && isset($_POST["id"]))) {
 echo "error";
    exit;  
}

$display_text=trim($_POST["display_text"]);
$alias_code=trim($_POST["alias_code"]);
$display_days=preg_replace($only_nr, '', $_POST["display_days"]);
$blocks=preg_replace($only_nr, '', $_POST["blocks"]);
$begin_time=preg_replace($only_nr, '',$_POST["begin_time"]);
$end_time=preg_replace($only_nr, '' , $_POST["end_time"]);
$active=preg_replace($only_nr, '' , $_POST["active"]);
$id_scheduler=preg_replace($only_nr, '' , $_POST["id"]);
$postal=(isset($_POST["postal"])?"1":"0");

	$query="UPDATE `sips_sd_schedulers` SET `display_text`='".mysql_real_escape_string($display_text)."',
			`alias_code`='".mysql_real_escape_string($alias_code)."',
			`days_visible`=".mysql_real_escape_string($display_days).",
			`blocks`=".mysql_real_escape_string($blocks).",
			`begin_time`=".mysql_real_escape_string($begin_time).",
			`end_time`=".mysql_real_escape_string($end_time).",
			`view_postal`=".mysql_real_escape_string($postal).",
			`active`=".mysql_real_escape_string($active)."
                        WHERE
                        id_scheduler=".mysql_real_escape_string($id_scheduler).";";

	mysql_query($query,$link) or die( json_encode(array(sucess=>0))."  ".mysql_error());   
	echo json_encode(array(sucess=>1));                                               
