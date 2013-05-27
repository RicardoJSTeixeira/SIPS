<?
date_default_timezone_set('Europe/Lisbon');

require('../dbconnect.php');


foreach ($_POST as $key => $value) {
	${$key} = $value;
}
foreach ($_GET as $key => $value) {
	${$key} = $value;
}

if($action=="check_remote_active")
{
	$query = mysql_query("SELECT status FROM vicidial_remote_agents WHERE campaign_id='$sent_campaign_id'", $link);
	
	$row = mysql_fetch_row($query);
	
	if($row[0]=="ACTIVE"){$result = 1;} else {$result = 0;}
	
	echo $result;
	
}

if($action=="start_stop_campaign")
{
	$query = mysql_query("UPDATE vicidial_remote_agents SET status='$sent_status' WHERE campaign_id='$sent_campaign_id'", $link);
	
}





?>