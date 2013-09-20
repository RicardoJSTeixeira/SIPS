<?php 
require_once('../func/reserve_utils.php');
if (isset($_POST['start']) && isset($_POST['end']) && isset($_POST['resource']) && isset($_POST['user']) && isset($_POST['lead']) && isset($_POST['rtype'])) {
	$start=$_POST['start'];
	$end=$_POST['end'];
	$resource=preg_replace($only_nr,'',$_POST['resource']);
        $user=$_POST['user'];
        $lead_id=preg_replace($only_nr,'',$_POST['lead']);
        $rtype=preg_replace($only_nr,'',$_POST['rtype']);
}else{
	exit;
}
		if (!(checkDateTime($start) && checkDateTime($end)) ){
			exit;
		}
		
		$query="Select count(*) existe FROM sips_sd_reservations 
		WHERE id_resource='".mysql_real_escape_string($resource)."' 
		AND start_date='".$start."' AND end_date='".$end."';";
		
		$result=mysql_query($query,$link) or die(mysql_error());
		$row=mysql_fetch_assoc($result);
		
		if ($row[existe]>0) {
			echo json_encode(array("sucess" => "0","message" => "Já existe."));
			exit;
		}
		
		$query="INSERT INTO `sips_sd_reservations` 
		(`start_date`, `end_date`, `has_accessories`, `id_reservation_type`, `id_resource`,`id_user`,`lead_id`)
		 VALUES 
		('".mysql_real_escape_string($start)."', '".mysql_real_escape_string($end)."', '0', '".  mysql_real_escape_string($rtype)."', '".mysql_real_escape_string($resource)."','".mysql_real_escape_string($user)."','".mysql_real_escape_string($lead_id)."');";
		mysql_query($query) or die(mysql_error());
                echo json_encode(array("sucess" => "1","message" => "Sucesso"));
?>