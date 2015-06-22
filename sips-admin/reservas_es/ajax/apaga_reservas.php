<?php 
require_once('../func/reserve_utils.php');

if (isset($_POST['start']) && isset($_POST['end']) && isset($_POST['resource'])) {
	$start=$_POST['start'];
	$end=$_POST['end'];
	$resource=preg_replace($only_nr,'',$_POST['resource']);
}else{
	exit;
}

if (!(checkDateTime($start) && checkDateTime($end)) ){
        exit;
}

    $query="DELETE FROM sips_sd_reservations 
    WHERE id_resource='".mysql_real_escape_string($resource)."' 
    AND start_date='".$start."' AND end_date='".$end."';";

    $result=mysql_query($query,$link) or die(json_encode(array("sucess" => "1")).mysql_error());
    
            echo json_encode(array("sucess" => "1"));

