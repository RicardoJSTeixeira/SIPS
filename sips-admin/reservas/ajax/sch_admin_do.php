<?php
require('../func/reserve_utils.php');

if (!(isset($_POST["display_text"]) && isset($_POST["alias_code"]) && isset($_POST["display_days"]) && isset($_POST["blocks"]) && isset($_POST["begin_time"]) && isset($_POST["end_time"]))) {
 echo "error";
    exit;  
}

$user = new UserLogin($db);
$user->confirm_login();
$u=$user->getUser();

$display_text=trim($_POST["display_text"]);
$alias_code=trim($_POST["alias_code"]);
$display_days=preg_replace($only_nr, '', $_POST["display_days"]);
$blocks=preg_replace($only_nr, '', $_POST["blocks"]);
$begin_time=preg_replace($only_nr, '',$_POST["begin_time"]);
$end_time=preg_replace($only_nr, '' , $_POST["end_time"]);

	$query="INSERT INTO `sips_sd_schedulers` (`display_text`, `alias_code`, `days_visible`, `blocks`, `begin_time`, `end_time`, `active`,`user_group`)
			VALUES
			('".mysql_real_escape_string($display_text)."',
			'".mysql_real_escape_string($alias_code)."',
			".mysql_real_escape_string($display_days).",
			".mysql_real_escape_string($blocks).",
			".mysql_real_escape_string($begin_time).",
			".mysql_real_escape_string($end_time).",
			1,'".$u->user_group."');";

	mysql_query($query,$link) or die(json_encode(array(sucess=>1))."Sucedeu-se um Erro. ".mysql_error());   
	echo json_encode(array("sucess"=>1,"id"=>mysql_insert_id(),"display_text"=>$display_text,"alias_code"=>$alias_code,"display_days"=>$display_days,"blocks"=>m2h($blocks),"begin_time"=>m2h($begin_time,true),"end_time"=>m2h($end_time,true)));                                               
