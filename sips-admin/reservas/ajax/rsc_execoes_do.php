<?php
require_once('../func/reserve_utils.php');

if(isset($_POST['nr'])){
$nr=preg_replace($only_nr,'',$_POST[nr]);
$query="DELETE FROM `sips_sd_execoes` WHERE `id_execao`='$nr';";
mysql_query($query, $link) or die( __json_encode(array(sucess=>0))."  ".mysql_error());
echo  __json_encode(array(sucess=>1));
exit;
}

if (!(isset($_POST[beg]) && isset($_POST[end]) && isset($_POST[id]))) {
 echo "error";
    exit;  
}

$beg=preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $_POST[beg].":00");
$end=preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $_POST[end].":00");
$id_resource=preg_replace($only_nr,'',$_POST[id]);

	$query="INSERT INTO `sips_sd_execoes` (`start_date`, `end_date`, `id_resource`)
            VALUES ( '".mysql_real_escape_string($beg)."',
			'".mysql_real_escape_string($end)."',
                        ".mysql_real_escape_string($id_resource).");";

	mysql_query($query,$link) or die( __json_encode(array(sucess=>0))."  ".mysql_error());   
	echo __json_encode(array(sucess=>1,beg=>substr($beg,0,-3),end=>substr($end,0,-3),id=> mysql_insert_id($link))); 
?>
