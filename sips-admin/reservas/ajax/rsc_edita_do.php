<?php
require_once('../func/reserve_utils.php');
if (!(isset($_POST[display_text]) && isset($_POST[alias_code]) && isset($_POST[active]) && isset($_POST[id]) && isset($_POST[inverted]))) {
 echo "error";
    exit;  
}

$display_text=trim($_POST[display_text]);
$alias_code=trim($_POST[alias_code]);
$active=preg_replace($only_nr, '' , $_POST[active]);
$inverted=preg_replace($only_nr, '' , $_POST[inverted]);
$id_resource=preg_replace($only_nr, '' , $_POST[id]);

	$query="UPDATE `sips_sd_resources` SET `display_text`='".mysql_real_escape_string($display_text)."',
			`alias_code`='".mysql_real_escape_string($alias_code)."',
			`active`=".mysql_real_escape_string($active).",
			`restrict_days`=".mysql_real_escape_string($inverted)."
                        WHERE
                        id_resource=".mysql_real_escape_string($id_resource).";";

	mysql_query($query,$link) or die( __json_encode(array(sucess=>0))."  ".mysql_error());   
	echo __json_encode(array(sucess=>1)); 
?>

