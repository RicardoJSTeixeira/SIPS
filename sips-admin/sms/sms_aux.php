<?php
require ("../dbconnect.php");

if (isset($_POST['info']))
	$info=$_POST['info'];
if (isset($_POST['id_camp']))
	$id_camp=$_POST['id_camp'];
//get contactos sms_edit
if (isset($_POST['id_camp']) AND isset($_POST['info'])){
		$info_tmn = ($info == 96) ? "OR ( a.`phone_number`  REGEXP '^92[024567]' )" : "";
		$listas_brute = mysql_query("SELECT 'X' , a.`list_id`, b.list_name FROM `vicidial_list` a INNER JOIN `vicidial_lists` b ON a.list_id=b.list_id WHERE a.`phone_number` LIKE '$info%' $info_tmn AND b.active='Y' GROUP BY `list_id` ORDER BY `list_id`", $link) OR die(mysql_error());
						
                $opt="";
                for ($i = 0; $i < mysql_num_rows($listas_brute); $i++) {
					$listas = mysql_fetch_assoc($listas_brute);
					$opt[$i]=array('list_id'=>$listas['list_id'],'list_name'=>$listas['list_name'],'rows'=>$listas['Rows']) ;
				}
                                echo json_encode($opt);
	exit;
}

//get contactos sms normal
if (isset($_POST['info'])){
		$info_tmn = ($info == 96) ? "OR (`phone_number`  REGEXP '^92[024567]')" : "";
		$listas_brute = mysql_query("SELECT COUNT(*) AS `Rows` , a.`list_id`, b.list_name FROM `vicidial_list` a INNER JOIN `vicidial_lists` b ON a.list_id=b.list_id WHERE `phone_number` LIKE '$info%' $info_tmn  AND b.active='Y' GROUP BY `list_id` ORDER BY `list_id`", $link);
				
                $opt="";
                for ($i = 0; $i < mysql_num_rows($listas_brute); $i++) {
					$listas = mysql_fetch_assoc($listas_brute);
					$opt[$i]=array('list_id'=>$listas['list_id'],'list_name'=>$listas['list_name'],'rows'=>$listas['Rows']) ;
				}
                                echo json_encode($opt);
	exit;
}
?>
