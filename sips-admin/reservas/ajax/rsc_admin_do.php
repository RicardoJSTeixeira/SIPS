<?php
require_once('../func/reserve_utils.php');

$id_scheduler=preg_replace($only_nr, '', $_POST["id"]);
$display_text=trim($_POST["display_text"]);
$alias_code=trim($_POST["alias_code"]);

	$query="INSERT INTO `sips_sd_resources` (`id_scheduler`, `display_text`, `alias_code`, `active`)
			VALUES
			('".mysql_real_escape_string($id_scheduler)."',
			'".mysql_real_escape_string($display_text)."',
			'".mysql_real_escape_string($alias_code)."',
			1);";

	mysql_query($query,$link) or die("Sucedeu-se um Erro. ".mysql_error());   
	echo json_encode(array("id"=>mysql_insert_id(),"display_text"=>$display_text,"alias_code"=>$alias_code));                                               
?>  