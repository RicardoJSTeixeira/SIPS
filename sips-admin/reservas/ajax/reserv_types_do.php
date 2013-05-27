<?php
require_once('../func/reserve_utils.php');

if(isset($_POST['nr'])){
$nr=preg_replace($only_nr,'',$_POST[nr]);
$query="SELECT count(id_reservation_type) a FROM `sips_sd_reservations` WHERE `id_reservation_type`='$nr';";
$result=mysql_query($query, $link) or die( __json_encode(array(sucess=>0))."  ".mysql_error());
$row=mysql_fetch_assoc($result);
if($row[a]>0){
    echo  __json_encode(array(sucess=>2));
    exit;
}
$query="DELETE FROM `sips_sd_reservations_types` WHERE `id_reservations_types`='$nr';";
mysql_query($query, $link) or die( __json_encode(array(sucess=>0))."  ".mysql_error());
echo  __json_encode(array(sucess=>1));
exit;
}

if(isset($_POST['id']) AND isset($_POST['act'])){
$id=preg_replace($only_nr,'',$_POST[id]);
$act=preg_replace($only_nr,'',$_POST[act]);
$query="UPDATE `sips_sd_reservations_types` SET active='$act'  WHERE `id_reservations_types`='$id';";
mysql_query($query, $link) or die( __json_encode(array(sucess=>0))."  ".mysql_error());
echo  __json_encode(array(sucess=>1));
exit;
}

if (!(isset($_POST[display_text]) && isset($_POST[color]) )) {
 echo "error";
    exit;
 }

$text=$_POST[display_text];
$color=$_POST[color];

	$query="INSERT INTO `sips_sd_reservations_types` ( `display_text`, `color`,`active` )
            VALUES ('".mysql_real_escape_string($text)."',
                        '".mysql_real_escape_string($color)."',1);";

	mysql_query($query,$link) or die( __json_encode(array(sucess=>0))."  ".mysql_error());   
	echo __json_encode(array(sucess=>1,text=>$text,color=>$color,id=> mysql_insert_id($link))); 
?>
