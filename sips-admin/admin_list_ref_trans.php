<?php 
if (isset($_POST['camp_ori'])){$camp_ori=$_POST['camp_ori'];}
elseif ($_GET['camp_ori']) {$camp_ori=$_GET['camp_ori'];}
else{exit;}

if (isset($_POST['camp_des'])){$camp_des=$_POST['camp_des'];}
elseif ($_GET['camp_des']) {$camp_des=$_GET['camp_des'];}
else{exit;}
	
require ("dbconnect.php");

		mysql_query("DELETE FROM vicidial_list_ref WHERE campaign_id LIKE '".mysql_real_escape_string($camp_des)."';",$link) OR die (mysql_error());
		mysql_query("INSERT INTO `vicidial_list_ref`  (`Name`, `Display_name`, `readonly`, `active`, `campaign_id`,`field_order`)  (SELECT `Name`, `Display_name`, `readonly`, `active`, '".mysql_real_escape_string($camp_des)."' AS 'campaign_id',`field_order` FROM `vicidial_list_ref` WHERE campaign_id LIKE '".mysql_real_escape_string($camp_ori)."' ORDER BY indice);",$link);
		

?>