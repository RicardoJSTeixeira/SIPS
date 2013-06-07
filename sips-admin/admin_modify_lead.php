<?php



require("dbconnect.php");

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");



$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];

if (isset($_GET["vendor_id"]))				{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))		{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["old_phone"]))				{$old_phone=$_GET["old_phone"];}
	elseif (isset($_POST["old_phone"]))		{$old_phone=$_POST["old_phone"];}
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["title"]))				{$title=$_GET["title"];}
	elseif (isset($_POST["title"]))		{$title=$_POST["title"];}
if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))		{$first_name=$_POST["first_name"];}
if (isset($_GET["middle_initial"]))				{$middle_initial=$_GET["middle_initial"];}
	elseif (isset($_POST["middle_initial"]))	{$middle_initial=$_POST["middle_initial"];}
if (isset($_GET["last_name"]))				{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))		{$last_name=$_POST["last_name"];}
if (isset($_GET["phone_number"]))				{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))		{$phone_number=$_POST["phone_number"];}
if (isset($_GET["end_call"]))				{$end_call=$_GET["end_call"];}
	elseif (isset($_POST["end_call"]))		{$end_call=$_POST["end_call"];}
if (isset($_GET["DB"]))				{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["dispo"]))				{$dispo=$_GET["dispo"];}
	elseif (isset($_POST["dispo"]))		{$dispo=$_POST["dispo"];}
if (isset($_GET["list_id"]))				{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))		{$list_id=$_POST["list_id"];}
if (isset($_GET["campaign_id"]))				{$campaign_id=$_GET["campaign_id"];}
	elseif (isset($_POST["campaign_id"]))		{$campaign_id=$_POST["campaign_id"];}
if (isset($_GET["phone_code"]))				{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))		{$phone_code=$_POST["phone_code"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["extension"]))				{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$extension=$_POST["extension"];}
if (isset($_GET["channel"]))				{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))		{$channel=$_POST["channel"];}
if (isset($_GET["call_began"]))				{$call_began=$_GET["call_began"];}
	elseif (isset($_POST["call_began"]))		{$call_began=$_POST["call_began"];}
if (isset($_GET["parked_time"]))				{$parked_time=$_GET["parked_time"];}
	elseif (isset($_POST["parked_time"]))		{$parked_time=$_POST["parked_time"];}
if (isset($_GET["tsr"]))				{$tsr=$_GET["tsr"];}
	elseif (isset($_POST["tsr"]))		{$tsr=$_POST["tsr"];}
if (isset($_GET["address1"]))				{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))		{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))				{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))		{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))				{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))		{$address3=$_POST["address3"];}
if (isset($_GET["city"]))				{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))		{$city=$_POST["city"];}
if (isset($_GET["state"]))				{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))		{$state=$_POST["state"];}
if (isset($_GET["postal_code"]))				{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))		{$postal_code=$_POST["postal_code"];}
if (isset($_GET["province"]))				{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))		{$province=$_POST["province"];}
if (isset($_GET["country_code"]))				{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))		{$country_code=$_POST["country_code"];}
if (isset($_GET["alt_phone"]))				{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))		{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["email"]))				{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))		{$email=$_POST["email"];}
if (isset($_GET["security"]))				{$security=$_GET["security"];}
	elseif (isset($_POST["security"]))		{$security=$_POST["security"];}
if (isset($_GET["comments"]))				{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))		{$comments=$_POST["comments"];}
if (isset($_GET["status"]))				{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))		{$status=$_POST["status"];}
if (isset($_GET["rank"]))				{$rank=$_GET["rank"];}
	elseif (isset($_POST["rank"]))		{$rank=$_POST["rank"];}
if (isset($_GET["owner"]))				{$owner=$_GET["owner"];}
	elseif (isset($_POST["owner"]))		{$owner=$_POST["owner"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["CBchangeUSERtoANY"]))				{$CBchangeUSERtoANY=$_GET["CBchangeUSERtoANY"];}
	elseif (isset($_POST["CBchangeUSERtoANY"]))		{$CBchangeUSERtoANY=$_POST["CBchangeUSERtoANY"];}
if (isset($_GET["CBchangeUSERtoUSER"]))				{$CBchangeUSERtoUSER=$_GET["CBchangeUSERtoUSER"];}
	elseif (isset($_POST["CBchangeUSERtoUSER"]))		{$CBchangeUSERtoUSER=$_POST["CBchangeUSERtoUSER"];}
if (isset($_GET["CBchangeANYtoUSER"]))				{$CBchangeANYtoUSER=$_GET["CBchangeANYtoUSER"];}
	elseif (isset($_POST["CBchangeANYtoUSER"]))		{$CBchangeANYtoUSER=$_POST["CBchangeANYtoUSER"];}
if (isset($_GET["CBchangeDATE"]))				{$CBchangeDATE=$_GET["CBchangeDATE"];}
	elseif (isset($_POST["CBchangeDATE"]))		{$CBchangeDATE=$_POST["CBchangeDATE"];}
if (isset($_GET["callback_id"]))				{$callback_id=$_GET["callback_id"];}
	elseif (isset($_POST["callback_id"]))		{$callback_id=$_POST["callback_id"];}
if (isset($_GET["CBuser"]))				{$CBuser=$_GET["CBuser"];}
	elseif (isset($_POST["CBuser"]))		{$CBuser=$_POST["CBuser"];}
if (isset($_GET["modify_logs"]))			{$modify_logs=$_GET["modify_logs"];}
	elseif (isset($_POST["modify_logs"]))	{$modify_logs=$_POST["modify_logs"];}
if (isset($_GET["modify_closer_logs"]))			{$modify_closer_logs=$_GET["modify_closer_logs"];}
	elseif (isset($_POST["modify_closer_logs"]))	{$modify_closer_logs=$_POST["modify_closer_logs"];}
if (isset($_GET["modify_agent_logs"]))			{$modify_agent_logs=$_GET["modify_agent_logs"];}
	elseif (isset($_POST["modify_agent_logs"]))	{$modify_agent_logs=$_POST["modify_agent_logs"];}
if (isset($_GET["add_closer_record"]))			{$add_closer_record=$_GET["add_closer_record"];}
	elseif (isset($_POST["add_closer_record"]))	{$add_closer_record=$_POST["add_closer_record"];}
if (isset($_POST["appointment_date"]))			{$appointment_date=$_POST["appointment_date"];}
	elseif (isset($_GET["appointment_date"]))	{$appointment_date=$_GET["appointment_date"];}
if (isset($_POST["appointment_time"]))			{$appointment_time=$_POST["appointment_time"];}
	elseif (isset($_GET["appointment_time"]))	{$appointment_time=$_GET["appointment_time"];}
if (isset($_GET["CBstatus"]))				{$CBstatus=$_GET["CBstatus"];}
	elseif (isset($_POST["CBstatus"]))		{$CBstatus=$_POST["CBstatus"];}

$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =				$row[0];
	$custom_fields_enabled =	$row[1];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

	$old_phone = ereg_replace("[^0-9]","",$old_phone);
	$phone_number = ereg_replace("[^0-9]","",$phone_number);
	$alt_phone = ereg_replace("[^0-9]","",$alt_phone);
	}	# end of non_latin
else
	{
	$PHP_AUTH_USER = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_PW);
	}

if (strlen($phone_number)<6) {$phone_number=$old_phone;}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_leads='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0)
	{$fp = fopen ("./project_auth_entries.txt", "a");}

$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
else
	{

	if($auth>0)
		{
		$stmt="SELECT full_name,modify_leads from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname				=$row[0];
		$LOGmodify_leads			=$row[1];

		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}

$label_title =				'Title';
$label_first_name =			'First';
$label_middle_initial =		'MI';
$label_last_name =			'Last';
$label_address1 =			'Address1';
$label_address2 =			'Address2';
$label_address3 =			'Address3';
$label_city =				'City';
$label_state =				'State';
$label_province =			'Province';
$label_postal_code =		'Postal Code';
$label_vendor_lead_code =	'Vendor ID';
$label_gender =				'Gender';
$label_phone_number =		'Phone';
$label_phone_code =			'DialCode';
$label_alt_phone =			'Alt. Phone';
$label_security_phrase =	'Show';
$label_email =				'Email';
$label_comments =			'Comments';

### find any custom field labels
$stmt="SELECT label_title,label_first_name,label_middle_initial,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_postal_code,label_vendor_lead_code,label_gender,label_phone_number,label_phone_code,label_alt_phone,label_security_phrase,label_email,label_comments from system_settings;";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
if (strlen($row[0])>0)	{$label_title =				$row[0];}
if (strlen($row[1])>0)	{$label_first_name =		$row[1];}
if (strlen($row[2])>0)	{$label_middle_initial =	$row[2];}
if (strlen($row[3])>0)	{$label_last_name =			$row[3];}
if (strlen($row[4])>0)	{$label_address1 =			$row[4];}
if (strlen($row[5])>0)	{$label_address2 =			$row[5];}
if (strlen($row[6])>0)	{$label_address3 =			$row[6];}
if (strlen($row[7])>0)	{$label_city =				$row[7];}
if (strlen($row[8])>0)	{$label_state =				$row[8];}
if (strlen($row[9])>0)	{$label_province =			$row[9];}
if (strlen($row[10])>0) {$label_postal_code =		$row[10];}
if (strlen($row[11])>0) {$label_vendor_lead_code =	$row[11];}
if (strlen($row[12])>0) {$label_gender =			$row[12];}
if (strlen($row[13])>0) {$label_phone_number =		$row[13];}
if (strlen($row[14])>0) {$label_phone_code =		$row[14];}
if (strlen($row[15])>0) {$label_alt_phone =			$row[15];}
if (strlen($row[16])>0) {$label_security_phrase =	$row[16];}
if (strlen($row[17])>0) {$label_email =				$row[17];}
if (strlen($row[18])>0) {$label_comments =			$row[18];}


### find out if status(dispo) is a scheduled callback status
$scheduled_callback='';
$stmt="SELECT scheduled_callback from vicidial_statuses where status='$dispo';";
$rslt=mysql_query($stmt, $link);
$scb_count_to_print = mysql_num_rows($rslt);
if ($scb_count_to_print > 0) 
	{
	$row=mysql_fetch_row($rslt);
	if (strlen($row[0])>0)	{$scheduled_callback =	$row[0];}
	}
$stmt="SELECT scheduled_callback from vicidial_campaign_statuses where status='$dispo';";
$rslt=mysql_query($stmt, $link);
$scb_count_to_print = mysql_num_rows($rslt);
if ($scb_count_to_print > 0) 
	{
	$row=mysql_fetch_row($rslt);
	if (strlen($row[0])>0)	{$scheduled_callback =	$row[0];}
	}

?>

<?php #HEADER
/*
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");*/


include("functions.php");

?>



<?php
####################################################################### 
### BEGIN  - Created by kant
#######################################################################
if(($_POST['stage'])=='modify_lead_info')
{ 
	$lead_id=$_POST['lead_id'];
	$i=0;
	$post_count=(count($_POST)-2);
	foreach ($_POST as $key => $value)
	{
		if(eregi('leadinfo#', $key))
		{
			$i++;
			$temp=explode("#", $key);
			$row=$temp[1];
			if ($post_count == 1) 
				{
				$mli_set = $row."='".$value."'";
				}
			elseif ($post_count == $i)
				{
				$mli_set .= $row."='".$value."'";
				}	
			else
				{
				$mli_set .= $row."='".$value."',";
				}
		}
	}
	mysql_query("UPDATE vicidial_list SET $mli_set WHERE lead_id='$lead_id'") or die(mysql_error());
}
if(($_POST['stage'])=='modify_status')
{
	$explode_array=explode("###",$_POST['feedback_list']);
	$new_feedback=$explode_array[0];
	$is_callback=$explode_array[1];
	
	
	
	/* 	### update last record in vicidial_log table
       if (($dispo != $status) and ($modify_logs > 0)) 
		{
		$stmt="UPDATE vicidial_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by call_date desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	### update last record in vicidial_closer_log table
       if (($dispo != $status) and ($modify_closer_logs > 0)) 
		{
		$stmt="UPDATE vicidial_closer_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by call_date desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	### update last record in vicidial_agent_log table
       if (($dispo != $status) and ($modify_agent_logs > 0)) 
		{
		$stmt="UPDATE vicidial_agent_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by agent_log_id desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	if ($add_closer_record > 0)
		{
		### insert a NEW record to the vicidial_closer_log table 
		$stmt="INSERT INTO vicidial_closer_log (lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed) values('" . mysql_real_escape_string($lead_id) . "','" . mysql_real_escape_string($list_id) . "','" . mysql_real_escape_string($campaign_id) . "','" . mysql_real_escape_string($parked_time) . "','$NOW_TIME','$STARTtime','1','" . mysql_real_escape_string($status) . "','" . mysql_real_escape_string($phone_code) . "','" . mysql_real_escape_string($phone_number) . "','$PHP_AUTH_USER','" . mysql_real_escape_string($comments) . "','Y')";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}
	 * 
	 	### update last record in vicidial_log table
       if (($dispo != $status) and ($modify_logs > 0)) 
		{
		$stmt="UPDATE vicidial_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by call_date desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	### update last record in vicidial_closer_log table
       if (($dispo != $status) and ($modify_closer_logs > 0)) 
		{
		$stmt="UPDATE vicidial_closer_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by call_date desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	### update last record in vicidial_agent_log table
       if (($dispo != $status) and ($modify_agent_logs > 0)) 
		{
		$stmt="UPDATE vicidial_agent_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by agent_log_id desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	if ($add_closer_record > 0)
		{
		### insert a NEW record to the vicidial_closer_log table 
		$stmt="INSERT INTO vicidial_closer_log (lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed) values('" . mysql_real_escape_string($lead_id) . "','" . mysql_real_escape_string($list_id) . "','" . mysql_real_escape_string($campaign_id) . "','" . mysql_real_escape_string($parked_time) . "','$NOW_TIME','$STARTtime','1','" . mysql_real_escape_string($status) . "','" . mysql_real_escape_string($phone_code) . "','" . mysql_real_escape_string($phone_number) . "','$PHP_AUTH_USER','" . mysql_real_escape_string($comments) . "','Y')";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}
	 */
	
}
### Dados da Lead
$query="SELECT 
			vdlf.campaign_id,
			vdc.campaign_name, 
			vdlf.list_id, 
			vdlf.list_name, 
			vu.full_name, 
			vdl.called_count, 
			DATE_FORMAT(vdl.last_local_call_time,'%H:%i:%s') AS hora_last,
			DATE_FORMAT(vdl.last_local_call_time,'%d-%m-%Y') AS data_last,
			vdl.status, 
			vds.status_name as status_name_one,
			vdcs.status_name as status_name_two,
			DATE_FORMAT(vdl.entry_date,'%H:%i:%s') AS hora_load,
			DATE_FORMAT(vdl.entry_date,'%d-%m-%Y') AS data_load,
			vdl.phone_number
			
		FROM 
			vicidial_lists vdlf 
		INNER JOIN 
			vicidial_list vdl 
		ON 
			vdl.list_id=vdlf.list_id 						
		INNER JOIN 
			vicidial_campaigns vdc 
		ON 
			vdc.campaign_id=vdlf.campaign_id
		LEFT JOIN 
			vicidial_users vu 
		ON 
			vu.user=vdl.user
		LEFT JOIN 
			vicidial_statuses vds 
		ON 
			vds.status=vdl.status
		LEFT JOIN 
			vicidial_campaign_statuses vdcs 
		ON 
			vdcs.status=vdl.status
		WHERE 
			lead_id='$lead_id'
		LIMIT 1";
			
$query=mysql_query($query,$link) or die(mysql_error());
$lead_info=mysql_fetch_assoc($query);

if($lead_info['status_name_one']==NULL)
{$status_name=$lead_info['status_name_two'];} 
else 
{$status_name=$lead_info['status_name_one'];}

### Dados do Contacto
$query="SELECT 
			Name,
			Display_name
		FROM 
			vicidial_list_ref
		WHERE
			campaign_id='$lead_info[campaign_id]'
		AND
			active=1";
            
$query=mysql_query($query,$link) or die(mysql_error());
$fields_count = mysql_num_rows($query);
for ($i=0;$i<$fields_count;$i++)
{
	$row = mysql_fetch_row($query);
	if ($fields_count == 1) 
		{
		$fields_NAME[$i]= strtolower($row[0]);
		$fields_SELECT = $row[0]; 
		$fields_LABEL[$i] = $row[1]; 
		}
	elseif ($fields_count-1 == $i)
		{
		$fields_NAME[$i]= strtolower($row[0]);
		$fields_SELECT .= $row[0];	
		$fields_LABEL[$i] = $row[1]; 
		}	
	else
		{
		$fields_NAME[$i]= strtolower($row[0]);
		$fields_SELECT .= $row[0].","; 
		$fields_LABEL[$i] = $row[1]; 
		}
}	
$query="SELECT 
			$fields_SELECT 
		FROM 
			vicidial_list
		WHERE
			lead_id='$lead_id' 
		LIMIT 1";
        
$query=mysql_query($query,$link);
$fields = mysql_fetch_row($query);

### Construção da Lista de Feedbacks
$query="SELECT status,status_name FROM vicidial_campaign_statuses WHERE campaign_id='$lead_info[campaign_id]' AND scheduled_callback!=1";
$query=mysql_query($query,$link) or die(mysql_error());
$is_campaign_feedback=0;

for($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query) or die(mysql_query());
	
	if($row['status']==$lead_info['status'])
	{ # feedback actual (selected)
		$status_options .= "<option selected value='$row[status]'>$row[status_name]</option>\n";
		$is_campaign_feedback=1;
	}
	else
	{ # outros feedbacks da campanha
		$status_options .= "<option value='$row[status]'>$row[status_name]</option\n>";
	}
}

if(!$is_campaign_feedback)
{ # caso se o feedback actual seja de sistema
	$query="SELECT status,status_name FROM vicidial_statuses WHERE status='$lead_info[status]'";
	$query=mysql_query($query,$link) or die(mysql_error());
	$row=mysql_fetch_assoc($query);
	$status_options .= "<option selected value='$row[status]'>$row[status_name]</option>";
}


### Chamadas Feitas
$query = "SELECT 									
			vl.uniqueid, 
			vl.lead_id, 
			vl.list_id,
			vl.campaign_id,
			DATE_FORMAT(vl.call_date,'%d-%m-%Y') AS data,
			DATE_FORMAT(vl.call_date,'%H:%i:%s') AS hora,
			vl.start_epoch,
			vl.end_epoch,
			vl.length_in_sec,
			vl.status,
			vl.phone_code,
			vl.phone_number,
			vl.user,
			ifnull(vl.comments,'AUTO') comments,
			vl.processed,
			vl.user_group,
			vl.term_reason,
			vl.alt_dial,
			vu.full_name,
			(select status_name from vicidial_campaign_statuses where status=vl.status limit 1) as status_name1,
			(select status_name from vicidial_statuses where status=vl.status limit 1) as status_name2,
			vc.campaign_name,
			vls.list_name
		FROM 
			vicidial_log vl
		INNER JOIN vicidial_users vu ON vl.user=vu.user
		INNER JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
		INNER JOIN vicidial_lists vls ON vl.list_id=vls.list_id
			
		WHERE 
			vl.lead_id='$lead_id' 
		ORDER BY
			uniqueid 
		DESC LIMIT 500;";
$chamadas_feitas=mysql_query($query, $link) or die(mysql_error());

#Gravações da lead
$query = "	SELECT 
				DATE_FORMAT(start_time,'%d-%m-%Y') AS data,
				DATE_FORMAT(start_time,'%H:%i:%s') AS hora_inicio,
				DATE_FORMAT(end_time,'%H:%i:%s') AS hora_fim,
				length_in_sec,
				filename,
				location,
				lead_id,
				rl.user,
				full_name

			FROM 
				recording_log rl
			INNER JOIN vicidial_users vu ON rl.user=vu.user
			WHERE 
				lead_id='$lead_id' 
			ORDER BY 
				recording_id 
			DESC LIMIT 500;";
$gravacoes = mysql_query($query, $link) or die(mysql_error());
?>



<?php

### Cabeçalho
echo "<div class=cc-mstyle>";
echo "<table>";
echo "<tr>";
echo "<td id='icon32'><img src='../../images/icons/book_edit_32.png' /></td>";
echo "<td id='submenu-title'> Gestão de Leads </td>";
echo "<td><img style='float:right' src='../../images/icons/cross_32.png' onclick='CloseHTML();'></td>";
echo "</tr>";
echo "</table>";
echo "</div>";
echo "<br><br>";


### Informações do Contacto
echo "
<div class='datagrid' style='width:90%'>
<table>
<thead> 
	<th>ID do Contacto</th>
	<th>Número de Telefone</th>
	<th>Base de Dados</th>
	<th>Campanha</th>
	<th>Operador</th>
	<th>Feedback</th>
	<th>Nº de Chamadas</th>
</thead>
<tbody>
<tr>
	<td>$lead_id</td>
	<td>$lead_info[phone_number]</td>
	<td>$lead_info[list_name]</td>
	<td>$lead_info[campaign_name]</td>
	<td>$lead_info[full_name]</td>
	<td><span id='lead_info_status'>$status_name</span></td>
	<td>$lead_info[called_count]</td>
</tr>
</tbody>
</table>
</div>
";
### Datas de Carregamento/Ultima Chamada
echo "
<br>
<div class='datagrid' style='width:90%'>
<table>
<thead> 
	<th>Data de Carregamento</th>
	<th>Hora de Carregamento</th>
	<th>Data da Última Chamada</th>
	<th>Hora da Última Chamada</th>
</thead>
<tbody>
<tr>
	<td>$lead_info[data_load]</td>
	<td>$lead_info[hora_load]</td>
	<td>$lead_info[data_last]</td>
	<td>$lead_info[hora_last]</td>
</tr>
</tbody>
</table>
</div>
";


echo "<div id=work-area>";
echo "<br>";



### Lista dos Campos/Dados Contacto
echo "<div class='table-title'><center>Dados do Contacto</center></div>";
echo "<div class=cc-mstyle style='border:none; width:90%;'>";
echo "<div id='inputcontainer'>";
echo "<table border=0>";
for($i=0;$i<count($fields);$i++)
{
echo "<tr>";
echo "
<td style='width:375px'> <div class=cc-mstyle style='height:28px;  '><p> $fields_LABEL[$i] </p></div></td>
<td><input type=text style='text-align:center' name='$fields_NAME[$i]' id='$fields_NAME[$i]' size=40 maxlength=40 value='$fields[$i]'></td>
<td style='min-width:130px' id='td_$fields_NAME[$i]'></td>
";
echo "</tr>";
}
echo "</table>";

echo "</form>";
echo "<br>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "<br>";

### Alteração do Feedback/NEW/CALLBACK
echo "<div id=work-area style='min-height:0;'>";
echo "<br>";

echo "<div class=cc-mstyle style='border:none'>";
echo "<div class='table-title'><center>Alteração do Feedback</center></div>";
echo "<table>";

echo "<tr>";
echo "
<td id=icon16><img src='../images/icons/report_edit_16.png'></td>
<td style='text-align:left; width:100px'>Feedback Actual:</td>
<td style='text-align:left'><center><select style='width:200px' name=feedback_list id=feedback_list>$status_options</select></td>
<td style='min-width:275px'><span id=modify_feedback_status style='color:grey;font-size:11px;'><i>(O feedback deste contacto pode ser alterado neste menu.)</i></Span></td>
";
echo "</tr>";

/*echo "<tr>";
echo "
<td id=icon16><img src='../images/icons/book_add_16.png'></td>
<td style='text-align:left; width:100px'>Tornar o Contacto NEW:</td>
<td style='text-align:left'><table><tr><td style='width:50%;'><img id='resetfeedback' style='float:right' src=../images/icons/book_add_32.png><td style='width:50%; text-align:left;'>Alterar</table></td>
<td></td>
";
echo "</tr>";*/

echo "</table>";
echo "<br>";
echo "</div></div>";


### Chamadas feitas para a Lead
echo '
<br>
<div class="table-title"><center>Chamadas realizadas para este Contacto</center></div>
<div class="datagrid" style="width:90%">
<table>
<thead><th>Data</th><th>Hora</th><th>Duração</th><th>Número</th><th>Operador</th><th>Feedback</th><th>Man/Auto</th><th>Campanha</th><th>Base de Dados</th></thead>';
echo "<tbody>";
for($i=0;$i<mysql_num_rows($chamadas_feitas);$i++){
$row = mysql_fetch_assoc($chamadas_feitas);

$duracao = sec_convert($row['length_in_sec'],"H");
if($row[status_name1]){$status_name=$row['status_name1'];}else{$status_name=$row['status_name2'];}

echo "
<tr>
<td>$row[data]</td>
<td>$row[hora]</td>
<td>$duracao</td>
<td>$row[phone_number]</td>
<td>$row[full_name]</td>
<td>$status_name</td>
<td>$row[comments]</td>
<td>$row[campaign_name]</td>
<td>$row[list_name]</td>
</tr>
";
}
echo "</tbody>";
echo '
</table>
</div>
';
### Gravações associadas ao Contacto
echo '
<br>
<div class="table-title"><center>Gravações deste Contacto</center></div>
<div class="datagrid" style="width:90%">
<table>
<thead> <th>Data</th> <th>Inicio da Gravação</th> <th>Fim da Gravação</th> <th>Duração</th> <th>Ouvir Gravação</th> <th>Operador</th> </thead>';
echo "<tbody>";
for($i=0;$i<mysql_num_rows($gravacoes);$i++){
$row = mysql_fetch_assoc($gravacoes);
$duracao = sec_convert($row['length_in_sec'],"H");
echo "
<tr>
<td>$row[data]</td>
<td>$row[hora_inicio]</td>
<td>$row[hora_fim]</td>
<td>$duracao</td>
<td><a href='$row[location]'><img src='../images/icons/sound_add_16.png'></a></td>
<td>$row[full_name]</td>

</tr>
";
}
echo "</tbody>";
echo '
</table>
</div>
';
?>
<script type="text/javascript">

/* VARS/DIALOGS */
var lead_id = <?php echo $lead_id;  ?>;
var $error = $('<div></div>')
		.html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ')
		.dialog({
			autoOpen: false,
			title: "<span style='float:left; margin-right: 4px;' class='ui-icon ui-icon-alert'></span> Erro",
			width: "550",
			height: "250",
			show: "fade",
			hide: "fade",
			buttons: { "OK": function(){ $(this).dialog("close"); } }
});
/*var $feedback_reset_confirm = $('<div></div>')
		.html('Se fizer reset ao contacto, todos os dados referentes ao mesmo serão apagados.<br><br>De certeza que pretende continuar com esta operação?')
		.dialog({
			autoOpen: false,
			title: "<span style='float:left; margin-right: 4px;' class='ui-icon ui-icon-alert'></span> Atenção",
			width: "550",
			height: "250",
			show: "fade",
			hide: "fade",
			buttons: 
				{ "Sim": 
				function() 
				{
					$.ajax({
					type: "POST",
					url: "../requests/admin_lead_modify_requests.php",
					data: {action: "feedback_reset", send_lead_id: lead_id},
					success: function(msg){ $feedback_reset_confirm.dialog("close"); }
					})	
				}, "Não": 
				function()
				{ 
					$(this).dialog("close"); 
				} 
			}
});*/

/* FUNCTIONS */
$("#inputcontainer input").focus(function() 
{
	$(this).css({"border":"1px solid green"}); 

}); 
/*$('#resetfeedback').click(function() {
	$feedback_reset_confirm.dialog('open');
});*/
$("#inputcontainer input").blur(function()
{
	
	var field = this.name;
	var field_value = $(this).val();
	$(this).css({"border":"1px solid #c0c0c0"}); 	
	
	$.ajax({
		type: "POST",
		url: "../../requests/admin_lead_modify_requests.php",
		data: {action: "update_contact_field", send_field: field, send_field_value: field_value, send_lead_id: lead_id },
		error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
		success: function(data, textStatus, jqXHR)
		{  
			if((textStatus=='success') && (data=='')) 
			{
			$("#td_"+field).html("<span id='img_fade" + field + "'><table><tr><td style='width:18px'><img src='../../images/icons/clock_add_16.png'><td style='text-align:left;'>A Gravar</span></tr></table></span>");
			$("#img_fade"+field).fadeOut(2500);
			}  
			else
			{
			$("#td_"+field).html("<span id='img_fade" + field + "'><table><tr><td style='width:18px'><img src='../../images/icons/clock_red_16.png'><td style='text-align:left;'>Erro a Gravar</tr></table></span>");
			$("#img_fade"+field).fadeOut(2500);
			}
		}
		});

});
$("#feedback_list").change(function()
{
	var feedback_id = $("#feedback_list").val();
	var feedback_name = $("#feedback_list option:selected").text();
	$.ajax({
	  type: "POST",
	  url: "../../requests/admin_lead_modify_requests.php",
	  data: {action: "update_feedback", send_lead_id: lead_id, send_feedback: feedback_id},
	  error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
	  success: function(data, textStatus, jqXHR)
	  {   
	
	  if((textStatus=='success') && (data=='')) {
		  $("#modify_feedback_status").html("<span id='feedback_fade'><table><tr><td style='width:18px'><img src='../../images/icons/clock_add_16.png'><td style='text-align:left;'>A Gravar</span></tr></table></span>");
		  $("#feedback_fade").fadeOut(2000, function(){$("#modify_feedback_status").html("<i>(O feedback deste contacto pode ser alterado neste menu.)</i>");});
		  $("#lead_info_status").html(feedback_name);
		  } else {
			  $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + data);
	  }
	
	  }
	  });	
	
});






	


	


</script>

<?php
####################################################################### 
### END - Created by kant
#######################################################################
$footer=ROOT."ini/footer.php";
require($footer);
?>

<?php


/* echo "<a href=\"./admin.php?ADD=100\">ADMINISTRATION</a>: Lead record modification<BR>\n";

if ($lead_id == 'NEW')
	{
	$stmt="INSERT INTO vicidial_list set status='" . mysql_real_escape_string($status) . "',title='" . mysql_real_escape_string($title) . "',first_name='" . mysql_real_escape_string($first_name) . "',middle_initial='" . mysql_real_escape_string($middle_initial) . "',last_name='" . mysql_real_escape_string($last_name) . "',address1='" . mysql_real_escape_string($address1) . "',address2='" . mysql_real_escape_string($address2) . "',address3='" . mysql_real_escape_string($address3) . "',city='" . mysql_real_escape_string($city) . "',state='" . mysql_real_escape_string($state) . "',province='" . mysql_real_escape_string($province) . "',postal_code='" . mysql_real_escape_string($postal_code) . "',country_code='" . mysql_real_escape_string($country_code) . "',alt_phone='" . mysql_real_escape_string($alt_phone) . "',phone_number='$phone_number',phone_code='$phone_code',email='" . mysql_real_escape_string($email) . "',security_phrase='" . mysql_real_escape_string($security) . "',comments='" . mysql_real_escape_string($comments) . "',rank='" . mysql_real_escape_string($rank) . "',owner='" . mysql_real_escape_string($owner) . "',vendor_lead_code='" . mysql_real_escape_string($vendor_id) . "', list_id='" . mysql_real_escape_string($list_id) . "'";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$affected_rows = mysql_affected_rows($link);
	if ($affected_rows > 0)
		{
		$lead_id = mysql_insert_id($link);
		echo "Lead has been added: $lead_id<BR><BR>\n";
		$end_call=0;
		}
	else
		{echo "ERROR: Lead not added, please go back and look at what you entered<BR><BR>\n";}
	}

if (strlen($lead_id) < 1)
	{$lead_id = 'NEW';}

if ($end_call > 0)
	{
	### update the lead record in the vicidial_list table 
	$stmt="UPDATE vicidial_list set status='" . mysql_real_escape_string($status) . "',title='" . mysql_real_escape_string($title) . "',first_name='" . mysql_real_escape_string($first_name) . "',middle_initial='" . mysql_real_escape_string($middle_initial) . "',last_name='" . mysql_real_escape_string($last_name) . "',address1='" . mysql_real_escape_string($address1) . "',address2='" . mysql_real_escape_string($address2) . "',address3='" . mysql_real_escape_string($address3) . "',city='" . mysql_real_escape_string($city) . "',state='" . mysql_real_escape_string($state) . "',province='" . mysql_real_escape_string($province) . "',postal_code='" . mysql_real_escape_string($postal_code) . "',country_code='" . mysql_real_escape_string($country_code) . "',alt_phone='" . mysql_real_escape_string($alt_phone) . "',phone_number='$phone_number',phone_code='$phone_code',email='" . mysql_real_escape_string($email) . "',security_phrase='" . mysql_real_escape_string($security) . "',comments='" . mysql_real_escape_string($comments) . "',rank='" . mysql_real_escape_string($rank) . "',owner='" . mysql_real_escape_string($owner) . "',vendor_lead_code='" . mysql_real_escape_string($vendor_id) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "'";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	echo "information modified<BR><BR>\n";
	echo "<a href=\"$PHP_SELF?lead_id=$lead_id&DB=$DB\">Go back to the lead modification page</a><BR><BR>\n";
	echo "<form><input type=button value=\"Close This Window\" onClick=\"javascript:window.close();\"></form>\n";
	
	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LEADS', event_type='MODIFY', record_id='$lead_id', event_code='ADMIN MODIFY LEAD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	if ( ($dispo != $status) and ($dispo == 'CBHOLD') )
		{
		### inactivate vicidial_callbacks record for this lead 
		$stmt="UPDATE vicidial_callbacks set status='INACTIVE' where lead_id='" . mysql_real_escape_string($lead_id) . "' and status='ACTIVE';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record inactivated: $lead_id<BR>\n";
		}
	if ( ($dispo != $status) and ( ($dispo == 'CALLBK') or ($scheduled_callback == 'Y') ) )
		{
		### inactivate vicidial_callbacks record for this lead 
		$stmt="UPDATE vicidial_callbacks set status='INACTIVE' where lead_id='" . mysql_real_escape_string($lead_id) . "' and status IN('ACTIVE','LIVE');";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record inactivated: $lead_id<BR>\n";
		}

	if ( ($dispo != $status) and ($status == 'CBHOLD') )
		{
		### find any vicidial_callback records for this lead 
		$stmt="select callback_id from vicidial_callbacks where lead_id='" . mysql_real_escape_string($lead_id) . "' and status IN('ACTIVE','LIVE') order by callback_id desc LIMIT 1;";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$CBM_to_print = mysql_num_rows($rslt);
		if ($CBM_to_print > 0)
			{
			$rowx=mysql_fetch_row($rslt);
			$callback_id = $rowx[0];
			}
		else
			{
			$tomorrow = date("Y-m-d", mktime(date("H"),date("i"),date("s"),date("m"),date("d")+1,date("Y")));

			$stmt="INSERT INTO vicidial_callbacks SET lead_id='" . mysql_real_escape_string($lead_id) . "',recipient='ANYONE',status='ACTIVE',user='$PHP_AUTH_USER',user_group='ADMIN',list_id='" . mysql_real_escape_string($list_id) . "',callback_time='$tomorrow 12:00:00',entry_time='$NOW_TIME',comments='',campaign_id='" . mysql_real_escape_string($campaign_id) . "';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);

			echo "<BR>Scheduled Callback added: $lead_id - $phone_number<BR>\n";
			}
		}


	if ( ($dispo != $status) and ($status == 'DNC') )
		{
		### add lead to the internal DNC list 
		$stmt="INSERT INTO vicidial_dnc (phone_number) values('" . mysql_real_escape_string($phone_number) . "');";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>Lead added to DNC List: $lead_id - $phone_number<BR>\n";
		}
	### update last record in vicidial_log table
       if (($dispo != $status) and ($modify_logs > 0)) 
		{
		$stmt="UPDATE vicidial_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by call_date desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	### update last record in vicidial_closer_log table
       if (($dispo != $status) and ($modify_closer_logs > 0)) 
		{
		$stmt="UPDATE vicidial_closer_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by call_date desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	### update last record in vicidial_agent_log table
       if (($dispo != $status) and ($modify_agent_logs > 0)) 
		{
		$stmt="UPDATE vicidial_agent_log set status='" . mysql_real_escape_string($status) . "' where lead_id='" . mysql_real_escape_string($lead_id) . "' order by agent_log_id desc limit 1";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}

	if ($add_closer_record > 0)
		{
		### insert a NEW record to the vicidial_closer_log table 
		$stmt="INSERT INTO vicidial_closer_log (lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed) values('" . mysql_real_escape_string($lead_id) . "','" . mysql_real_escape_string($list_id) . "','" . mysql_real_escape_string($campaign_id) . "','" . mysql_real_escape_string($parked_time) . "','$NOW_TIME','$STARTtime','1','" . mysql_real_escape_string($status) . "','" . mysql_real_escape_string($phone_code) . "','" . mysql_real_escape_string($phone_number) . "','$PHP_AUTH_USER','" . mysql_real_escape_string($comments) . "','Y')";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		}


	}
else
	{

	if ($CBchangeUSERtoANY == 'YES')
		{
		### set vicidial_callbacks record to an ANYONE callback for this lead 
		$stmt="UPDATE vicidial_callbacks set recipient='ANYONE' where callback_id='" . mysql_real_escape_string($callback_id) . "';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record changed to ANYONE<BR>\n";
		}
	if ($CBchangeUSERtoUSER == 'YES')
		{
		### set vicidial_callbacks record to a different USERONLY callback record for this lead 
		$stmt="UPDATE vicidial_callbacks set user='" . mysql_real_escape_string($CBuser) . "' where callback_id='" . mysql_real_escape_string($callback_id) . "';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record user changed to $CBuser<BR>\n";
		}	
	if ($CBchangeANYtoUSER == 'YES')
		{
		### set vicidial_callbacks record to an USERONLY callback for this lead 
		$stmt="UPDATE vicidial_callbacks set user='" . mysql_real_escape_string($CBuser) . "',recipient='USERONLY' where callback_id='" . mysql_real_escape_string($callback_id) . "';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record changed to USERONLY, user: $CBuser<BR>\n";
		}	
	
	if ($CBchangeDATE == 'YES')
		{
		### change date/time of vicidial_callbacks record for this lead 
		$stmt="UPDATE vicidial_callbacks set callback_time='" . mysql_real_escape_string($appointment_date) . " " . mysql_real_escape_string($appointment_time) . "',comments='" . mysql_real_escape_string($comments) . "',lead_status='" . mysql_real_escape_string($CBstatus) . "' where callback_id='" . mysql_real_escape_string($callback_id) . "';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<BR>vicidial_callback record changed to $appointment_date $appointment_time $CBstatus<BR>\n";
		}	


	$stmt="SELECT count(*) from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "'";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$lead_count = $row[0];

	if ($lead_count > 0)
		{
		##### grab vicidial_list_alt_phones records #####
		$stmt="select phone_code,phone_number,alt_phone_note,alt_phone_count,active from vicidial_list_alt_phones where lead_id='" . mysql_real_escape_string($lead_id) . "' order by alt_phone_count limit 500;";
		$rslt=mysql_query($stmt, $link);
		$alts_to_print = mysql_num_rows($rslt);

		$c=0;
		$alts_output = '';
		while ($alts_to_print > $c) 
			{
			$row=mysql_fetch_row($rslt);
			if (eregi("1$|3$|5$|7$|9$", $c))
				{$bgcolor='bgcolor="#B9CBFD"';} 
			else
				{$bgcolor='bgcolor="#9BB9FB"';}

			$c++;
			$alts_output .= "<tr $bgcolor>";
			$alts_output .= "<td><font size=1>$c</td>";
			$alts_output .= "<td><font size=2>$row[0] $row[1]</td>";
			$alts_output .= "<td align=left><font size=2> $row[2]</td>\n";
			$alts_output .= "<td align=left><font size=2> $row[3]</td>\n";
			$alts_output .= "<td align=left><font size=2> $row[4] </td></tr>\n";
			}

		}
	else
		{
		echo "lead lookup FAILED for lead_id $lead_id &nbsp; &nbsp; &nbsp; $NOW_TIME\n<BR><BR>\n";
#		echo "<a href=\"$PHP_SELF\">Close this window</a>\n<BR><BR>\n";
		}

	##### grab vicidial_log records #####
	$stmt="select uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,user_group,term_reason,alt_dial from vicidial_log where lead_id='" . mysql_real_escape_string($lead_id) . "' order by uniqueid desc limit 500;";
	$rslt=mysql_query($stmt, $link);
	$logs_to_print = mysql_num_rows($rslt);

	$u=0;
	$call_log = '';
	$log_campaign = '';
	while ($logs_to_print > $u) 
		{
		$row=mysql_fetch_row($rslt);
		if (strlen($log_campaign)<1) {$log_campaign = $row[3];}
		if (eregi("1$|3$|5$|7$|9$", $u))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$u++;
		$call_log .= "<tr $bgcolor>";
		$call_log .= "<td><font size=1>$u</td>";
		$call_log .= "<td><font size=2>$row[4]</td>";
		$call_log .= "<td align=left><font size=2> $row[7]</td>\n";
		$call_log .= "<td align=left><font size=2> $row[8]</td>\n";
		$call_log .= "<td align=left><font size=2> <A HREF=\"user_stats.php?user=$row[11]\" target=\"_blank\">$row[11]</A> </td>\n";
		$call_log .= "<td align=right><font size=2> $row[3] </td>\n";
		$call_log .= "<td align=right><font size=2> $row[2] </td>\n";
		$call_log .= "<td align=right><font size=2> $row[1] </td>\n";
		$call_log .= "<td align=right><font size=2> $row[15] </td>\n";
		$call_log .= "<td align=right><font size=2>&nbsp; $row[10] </td></tr>\n";

		$stmtA="SELECT call_notes FROM vicidial_call_notes WHERE lead_id='" . mysql_real_escape_string($lead_id) . "' and vicidial_id='$row[0]';";
		$rsltA=mysql_query($stmtA, $link);
		$out_notes_to_print = mysql_num_rows($rslt);
		if ($out_notes_to_print > 0)
			{
			$rowA=mysql_fetch_row($rsltA);
			if (strlen($rowA[0]) > 0)
				{
				$call_log .= "<TR>";
				$call_log .= "<td></td>";
				$call_log .= "<TD $bgcolor COLSPAN=9><font style=\"font-size:11px;font-family:sans-serif;\"> NOTES: &nbsp; $rowA[0] </font></TD>";
				$call_log .= "</TR>";
				}
			}

		$campaign_id = $row[3];
		}

	##### grab vicidial_agent_log records #####
	$stmt="select agent_log_id,user,server_ip,event_time,lead_id,campaign_id,pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec,status,user_group,comments,sub_status from vicidial_agent_log where lead_id='" . mysql_real_escape_string($lead_id) . "' order by agent_log_id desc limit 500;";
	$rslt=mysql_query($stmt, $link);
	$Alogs_to_print = mysql_num_rows($rslt);

	$y=0;
	$agent_log = '';
	$Alog_campaign = '';
	while ($Alogs_to_print > $y) 
		{
		$row=mysql_fetch_row($rslt);
		if (strlen($Alog_campaign)<1) {$Alog_campaign = $row[5];}
		if (eregi("1$|3$|5$|7$|9$", $y))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$y++;
		$agent_log .= "<tr $bgcolor>";
		$agent_log .= "<td><font size=1>$y</td>";
		$agent_log .= "<td><font size=2>$row[3]</td>";
		$agent_log .= "<td align=left><font size=2> $row[5]</td>\n";
		$agent_log .= "<td align=left><font size=2> <A HREF=\"user_stats.php?user=$row[1]\" target=\"_blank\">$row[1]</A> </td>\n";
		$agent_log .= "<td align=right><font size=2> $row[7]</td>\n";
		$agent_log .= "<td align=right><font size=2> $row[9] </td>\n";
		$agent_log .= "<td align=right><font size=2> $row[11] </td>\n";
		$agent_log .= "<td align=right><font size=2> $row[13] </td>\n";
		$agent_log .= "<td align=right><font size=2> &nbsp; $row[14] </td>\n";
		$agent_log .= "<td align=right><font size=2> &nbsp; $row[15] </td>\n";
		$agent_log .= "<td align=right><font size=2> &nbsp; $row[17] </td></tr>\n";

		$campaign_id = $row[5];
		}

	##### grab vicidial_closer_log records #####
	$stmt="select closecallid,lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed,queue_seconds,user_group,xfercallid,term_reason,uniqueid,agent_only from vicidial_closer_log where lead_id='" . mysql_real_escape_string($lead_id) . "' order by closecallid desc limit 500;";
	$rslt=mysql_query($stmt, $link);
	$Clogs_to_print = mysql_num_rows($rslt);

	$y=0;
	$closer_log = '';
	$Clog_campaign = '';
	while ($Clogs_to_print > $y) 
		{
		$row=mysql_fetch_row($rslt);
		if (strlen($Clog_campaign)<1) {$Clog_campaign = $row[3];}
		if (eregi("1$|3$|5$|7$|9$", $y))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$y++;
		$closer_log .= "<tr $bgcolor>";
		$closer_log .= "<td><font size=1>$y</td>";
		$closer_log .= "<td><font size=2>$row[4]</td>";
		$closer_log .= "<td align=left><font size=2> $row[7]</td>\n";
		$closer_log .= "<td align=left><font size=2> $row[8]</td>\n";
		$closer_log .= "<td align=left><font size=2> <A HREF=\"user_stats.php?user=$row[11]\" target=\"_blank\">$row[11]</A> </td>\n";
		$closer_log .= "<td align=right><font size=2> $row[3] </td>\n";
		$closer_log .= "<td align=right><font size=2> $row[2] </td>\n";
		$closer_log .= "<td align=right><font size=2> $row[1] </td>\n";
		$closer_log .= "<td align=right><font size=2> &nbsp; $row[14] </td>\n";
		$closer_log .= "<td align=right><font size=2> &nbsp; $row[17] </td></tr>\n";

		$stmtA="SELECT call_notes FROM vicidial_call_notes WHERE lead_id='" . mysql_real_escape_string($lead_id) . "' and vicidial_id='$row[0]';";
		$rsltA=mysql_query($stmtA, $link);
		$in_notes_to_print = mysql_num_rows($rslt);
		if ($in_notes_to_print > 0)
			{
			$rowA=mysql_fetch_row($rsltA);
			if (strlen($rowA[0]) > 0)
				{
				$closer_log .= "<TR>";
				$closer_log .= "<td></td>";
				$closer_log .= "<TD $bgcolor COLSPAN=9><font style=\"font-size:11px;font-family:sans-serif;\"> NOTES: &nbsp; $rowA[0] </font></TD>";
				$closer_log .= "</TR>";
				}
			}

		$campaign_id = $row[3];
		}

	##### grab vicidial_list data for lead #####
	$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "'";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	if (strlen($row[0]) > 0)
		{$lead_id		= $row[0];}
	$dispo				= $row[3];
	$tsr				= $row[4];
	$vendor_id			= $row[5];
	$list_id			= $row[7];
	$gmt_offset_now		= $row[8];
	$phone_code			= $row[10];
	$phone_number		= $row[11];
	$title				= $row[12];
	$first_name			= $row[13];
	$middle_initial		= $row[14];
	$last_name			= $row[15];
	$address1			= $row[16];
	$address2			= $row[17];
	$address3			= $row[18];
	$city				= $row[19];
	$state				= $row[20];
	$province			= $row[21];
	$postal_code		= $row[22];
	$country_code		= $row[23];
	$gender				= $row[24];
	$date_of_birth		= $row[25];
	$alt_phone			= $row[26];
	$email				= $row[27];
	$security			= $row[28];
	$comments			= $row[29];
	$called_count		= $row[30];
	$last_local_call_time = $row[31];
	$rank				= $row[32];
	$owner				= $row[33];
	$entry_list_id		= $row[34];

	if ($lead_id == 'NEW')
		{
		##### create a select list of lists if a NEW lead_id #####
		$stmt="select list_id,campaign_id,list_name from vicidial_lists order by list_id limit 5000;";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$lists_to_print = mysql_num_rows($rslt);

		$Lc=0;
		$select_list = '<select size=1 name=list_id>';
		while ($lists_to_print > $Lc) 
			{
			$row=mysql_fetch_row($rslt);
			$select_list .= "<option value='$row[0]'>$row[0] - $row[1] - $row[2]</option>";

			$Lc++;
			}
		$select_list .= "</select>";

		$list_id=$select_list;
		}

	if ($lead_id == 'NEW')
		{echo "<br><b>Add A New Lead</B>\n";}
	else
		{echo "<br>Call information: $first_name $last_name - $phone_number\n";}

	echo "<br><br><form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=end_call value=1>\n";
	echo "<input type=hidden name=DB value=\"$DB\">\n";
	echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
	echo "<input type=hidden name=dispo value=\"$dispo\">\n";
	echo "<input type=hidden name=list_id value=\"$list_id\">\n";
	echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
	echo "<input type=hidden name=old_phone value=\"$phone_number\">\n";
	echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
	echo "<input type=hidden name=extension value=\"$extension\">\n";
	echo "<input type=hidden name=channel value=\"$channel\">\n";
	echo "<input type=hidden name=call_began value=\"$call_began\">\n";
	echo "<input type=hidden name=parked_time value=\"$parked_time\">\n";
	echo "<table cellpadding=1 cellspacing=0>\n";
	echo "<tr><td colspan=2>Lead ID: $lead_id &nbsp; &nbsp; List ID: $list_id</td></tr>\n";
	echo "<tr><td colspan=2>Fronter: <A HREF=\"user_stats.php?user=$tsr\">$tsr</A> &nbsp; &nbsp; Called Count: $called_count</td></tr>\n";
	if ($lead_id == 'NEW') {$list_id='';}

	echo "<tr><td align=right>$label_title: </td><td align=left><input type=text name=title size=4 maxlength=4 value=\"$title\"> &nbsp; \n";
	echo "$label_first_name: <input type=text name=first_name size=15 maxlength=30 value=\"$first_name\"> </td></tr>\n";
	echo "<tr><td align=right>$label_middle_initial:  </td><td align=left><input type=text name=middle_initial size=4 maxlength=1 value=\"$middle_initial\"> &nbsp; \n";
	echo " $label_last_name: <input type=text name=last_name size=15 maxlength=30 value=\"$last_name\"> </td></tr>\n";
	echo "<tr><td align=right>$label_address1 : </td><td align=left><input type=text name=address1 size=30 maxlength=30 value=\"$address1\"></td></tr>\n";
	echo "<tr><td align=right>$label_address2 : </td><td align=left><input type=text name=address2 size=30 maxlength=30 value=\"$address2\"></td></tr>\n";
	echo "<tr><td align=right>$label_address3 : </td><td align=left><input type=text name=address3 size=30 maxlength=30 value=\"$address3\"></td></tr>\n";
	echo "<tr><td align=right>$label_city : </td><td align=left><input type=text name=city size=30 maxlength=30 value=\"$city\"></td></tr>\n";
	echo "<tr><td align=right>$label_state: </td><td align=left><input type=text name=state size=2 maxlength=2 value=\"$state\"> &nbsp; \n";
	echo " $label_postal_code: <input type=text name=postal_code size=10 maxlength=10 value=\"$postal_code\"> </td></tr>\n";

	echo "<tr><td align=right>$label_province : </td><td align=left><input type=text name=province size=30 maxlength=30 value=\"$province\"></td></tr>\n";
	echo "<tr><td align=right>Country : </td><td align=left><input type=text name=country_code size=3 maxlength=3 value=\"$country_code\"></td></tr>\n";
	echo "<tr><td align=right>$label_phone_number : </td><td align=left><input type=text name=phone_number size=20 maxlength=20 value=\"$phone_number\"></td></tr>\n";
	echo "<tr><td align=right>$label_phone_code : </td><td align=left><input type=text name=phone_code size=10 maxlength=10 value=\"$phone_code\"></td></tr>\n";
	echo "<tr><td align=right>$label_alt_phone : </td><td align=left><input type=text name=alt_phone size=20 maxlength=20 value=\"$alt_phone\"></td></tr>\n";
	echo "<tr><td align=right>$label_email : </td><td align=left><input type=text name=email size=30 maxlength=50 value=\"$email\"></td></tr>\n";
	echo "<tr><td align=right>$label_security_phrase : </td><td align=left><input type=text name=security size=30 maxlength=100 value=\"$security\"></td></tr>\n";
	echo "<tr><td align=right>$label_vendor_lead_code : </td><td align=left><input type=text name=vendor_id size=30 maxlength=100 value=\"$vendor_id\"></td></tr>\n";
	echo "<tr><td align=right>Rank : </td><td align=left><input type=text name=rank size=7 maxlength=5 value=\"$rank\"></td></tr>\n";
	echo "<tr><td align=right>Owner : </td><td align=left><input type=text name=owner size=22 maxlength=20 value=\"$owner\"></td></tr>\n";
	echo "<tr><td align=right>$label_comments : </td><td align=left><TEXTAREA name=comments ROWS=3 COLS=65>$comments</TEXTAREA></td></tr>\n";

	if ($lead_id != 'NEW') 
		{
		echo "<tr bgcolor=#B6D3FC><td align=right>Disposition: </td><td align=left><select size=1 name=status>\n";

		### find out if status(dispo) is a scheduled callback status
		$scheduled_callback='';
		$stmt="SELECT scheduled_callback from vicidial_statuses where status='$dispo';";
		$rslt=mysql_query($stmt, $link);
		$scb_count_to_print = mysql_num_rows($rslt);
		if ($scb_count_to_print > 0) 
			{
			$row=mysql_fetch_row($rslt);
			if (strlen($row[0])>0)	{$scheduled_callback =	$row[0];}
			}
		$stmt="SELECT scheduled_callback from vicidial_campaign_statuses where status='$dispo';";
		$rslt=mysql_query($stmt, $link);
		$scb_count_to_print = mysql_num_rows($rslt);
		if ($scb_count_to_print > 0) 
			{
			$row=mysql_fetch_row($rslt);
			if (strlen($row[0])>0)	{$scheduled_callback =	$row[0];}
			}

		$list_campaign='';
		$stmt="SELECT campaign_id from vicidial_lists where list_id='$list_id'";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$Cstatuses_to_print = mysql_num_rows($rslt);
		if ($Cstatuses_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$list_campaign = $row[0];
			}

		$stmt="SELECT status,status_name,selectable,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable from vicidial_statuses where selectable='Y' order by status";
		$rslt=mysql_query($stmt, $link);
		$statuses_to_print = mysql_num_rows($rslt);
		$statuses_list='';

		$o=0;
		$DS=0;
		while ($statuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			if ( (strlen($dispo) ==  strlen($rowx[0])) and (eregi($dispo,$rowx[0])) )
				{$statuses_list .= "<option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n"; $DS++;}
			else
				{$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
			$o++;
			}

		$stmt="SELECT status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable from vicidial_campaign_statuses where selectable='Y' and campaign_id='$list_campaign' order by status";
		$rslt=mysql_query($stmt, $link);
		$CAMPstatuses_to_print = mysql_num_rows($rslt);

		$o=0;
		$CBhold_set=0;
		while ($CAMPstatuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			if ( (strlen($dispo) ==  strlen($rowx[0])) and (eregi($dispo,$rowx[0])) )
				{$statuses_list .= "<option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n"; $DS++;}
			else
				{$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
			if ($rowx[0] == 'CBHOLD') {$CBhold_set++;}
			$o++;
			}

		if ($dispo == 'CBHOLD') {$CBhold_set++;}

		if ($DS < 1) 
			{$statuses_list .= "<option SELECTED value=\"$dispo\">$dispo</option>\n";}
		if ($CBhold_set < 1)
			{$statuses_list .= "<option value=\"CBHOLD\">CBHOLD - Scheduled Callback</option>\n";}
		echo "$statuses_list";
		echo "</select> <i>(with $list_campaign statuses)</i></td></tr>\n";


		echo "<tr bgcolor=#B6D3FC><td align=left>Modify vicidial log </td><td align=left><input type=checkbox name=modify_logs value=\"1\" CHECKED></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=left>Modify agent log </td><td align=left><input type=checkbox name=modify_agent_logs value=\"1\" CHECKED></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=left>Modify closer log </td><td align=left><input type=checkbox name=modify_closer_logs value=\"1\"></td></tr>\n";
		echo "<tr bgcolor=#B6D3FC><td align=left>Add closer log record </td><td align=left><input type=checkbox name=add_closer_record value=\"1\"></td></tr>\n";
		}
	else
		{
		echo "<input type=hidden name=status value=\"NEW\">\n";
		}

	echo "<tr><td colspan=2 align=center><input type=submit name=submit value=\"SUBMIT\"></td></tr>\n";
	echo "</table></form>\n";
	echo "<BR><BR><BR>\n";

	if ($lead_id != 'NEW') 
		{
		echo "<TABLE BGCOLOR=#B6D3FC WIDTH=750><TR><TD>\n";
		echo "Callback Details:<BR><CENTER>\n";
		if ( ($dispo == 'CALLBK') or ($dispo == 'CBHOLD') or ($scheduled_callback == 'Y') )
			{
			### find any vicidial_callback records for this lead 
			$stmt="select callback_id,lead_id,list_id,campaign_id,status,entry_time,callback_time,modify_date,user,recipient,comments,user_group,lead_status from vicidial_callbacks where lead_id='" . mysql_real_escape_string($lead_id) . "' and status IN('ACTIVE','LIVE') order by callback_id desc LIMIT 1;";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			$CB_to_print = mysql_num_rows($rslt);
			$rowx=mysql_fetch_row($rslt);

			if ($CB_to_print>0)
				{
				if ($rowx[9] == 'USERONLY')
					{
					echo "<br><form action=$PHP_SELF method=POST>\n";
					echo "<input type=hidden name=CBchangeUSERtoANY value=\"YES\">\n";
					echo "<input type=hidden name=DB value=\"$DB\">\n";
					echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
					echo "<input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					echo "<input type=submit name=submit value=\"CHANGE TO ANYONE CALLBACK\"></form><BR>\n";

					echo "<br><form action=$PHP_SELF method=POST>\n";
					echo "<input type=hidden name=CBchangeUSERtoUSER value=\"YES\">\n";
					echo "<input type=hidden name=DB value=\"$DB\">\n";
					echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
					echo "<input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					echo "New Callback Owner UserID: <input type=text name=CBuser size=8 maxlength=10 value=\"$rowx[8]\"> \n";
					echo "<input type=submit name=submit value=\"CHANGE USERONLY CALLBACK USER\"></form><BR>\n";
					}
				else
					{
					echo "<br><form action=$PHP_SELF method=POST>\n";
					echo "<input type=hidden name=CBchangeANYtoUSER value=\"YES\">\n";
					echo "<input type=hidden name=DB value=\"$DB\">\n";
					echo "<input type=hidden name=lead_id value=\"$lead_id\">\n";
					echo "<input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					echo "New Callback Owner UserID: <input type=text name=CBuser size=8 maxlength=10 value=\"$rowx[8]\"> \n";
					echo "<input type=submit name=submit value=\"CHANGE TO USERONLY CALLBACK\"></form><BR>\n";
					}
				$callback_id = $rowx[0];
				$CBcomments = $rowx[10];
				$lead_status = $rowx[12];
				$appointment_datetimeARRAY = explode(" ",$rowx[6]);
				$appointment_date = $appointment_datetimeARRAY[0];
				$appointment_timeARRAY = explode(":",$appointment_datetimeARRAY[1]);
				$appointment_hour = $appointment_timeARRAY[0];
				$appointment_min = $appointment_timeARRAY[1];



				$stmt="SELECT status,status_name from vicidial_statuses where scheduled_callback='Y' and selectable='Y' and status NOT IN('CBHOLD') order by status";
				$rslt=mysql_query($stmt, $link);
				$statuses_to_print = mysql_num_rows($rslt);
				$statuses_list='';

				$o=0;
				$DS=0;
				while ($statuses_to_print > $o) 
					{
					$rowx=mysql_fetch_row($rslt);
					if ( (strlen($lead_status) == strlen($rowx[0])) and (eregi($lead_status,$rowx[0])) )
						{$statuses_list .= "<option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n"; $DS++;}
					else
						{$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
					$o++;
					}

				$stmt="SELECT status,status_name from vicidial_campaign_statuses where scheduled_callback='Y' and selectable='Y' and status NOT IN('CBHOLD') and campaign_id='$list_campaign' order by status";
				$rslt=mysql_query($stmt, $link);
				$CAMPstatuses_to_print = mysql_num_rows($rslt);

				$o=0;
				$CBhold_set=0;
				while ($CAMPstatuses_to_print > $o) 
					{
					$rowx=mysql_fetch_row($rslt);
					if ( (strlen($lead_status) ==  strlen($rowx[0])) and (eregi($lead_status,$rowx[0])) )
						{$statuses_list .= "<option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n"; $DS++;}
					else
						{$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
					$o++;
					}

				if ($DS < 1) 
					{$statuses_list .= "<option SELECTED value=\"$lead_status\">$lead_status</option>\n";}

				?>

				<FORM METHOD=POST NAME=vsn ID=vsn ACTION="<?php echo $PHP_SELF ?>">
				<BR>Change Scheduled Callback Date:<BR>

				<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=2 WIDTH=700>
				<TR><TD COLSPAN=2 ALIGN=CENTER>
				<input type=hidden name=DB id=DB value=<?php echo $DB ?>>
				<input type=hidden name=CBchangeDATE value="YES">
				<input type=hidden name=lead_id id=lead_id value="<?php echo $lead_id ?>">
				<input type=hidden name=callback_id value="<?php echo $callback_id ?>">

				<TR BGCOLOR="#E6E6E6">
				<TD ALIGN=RIGHT><FONT FACE="ARIAL,HELVETICA">CallBack Date/Time: </TD><TD ALIGN=LEFT><input type=text name=appointment_date id=appointment_date size=10 maxlength=10 value="<?php echo $appointment_date ?>">

				<script language="JavaScript">
				var o_cal = new tcal ({
					// form name
					'formname': 'vsn',
					// input name
					'controlname': 'appointment_date'
				});
				o_cal.a_tpl.yearscroll = false;
				// o_cal.a_tpl.weekstart = 1; // Monday week start
				</script>
				&nbsp; &nbsp;  
				<input type=hidden name=appointment_time id=appointment_time value="<?php echo $appointment_time ?>">
				<SELECT name=appointment_hour id=appointment_hour>
				<option>00</option>
				<option>01</option>
				<option>02</option>
				<option>03</option>
				<option>04</option>
				<option>05</option>
				<option>06</option>
				<option>07</option>
				<option>08</option>
				<option>09</option>
				<option>10</option>
				<option>11</option>
				<option>12</option>
				<option>13</option>
				<option>14</option>
				<option>15</option>
				<option>16</option>
				<option>17</option>
				<option>18</option>
				<option>19</option>
				<option>20</option>
				<option>21</option>
				<option>22</option>
				<option>23</option>
				<OPTION value="<?php echo $appointment_hour ?>" selected><?php echo $appointment_hour ?></OPTION>
				</SELECT>:
				<SELECT name=appointment_min id=appointment_min>
				<option>00</option>
				<option>05</option>
				<option>10</option>
				<option>15</option>
				<option>20</option>
				<option>25</option>
				<option>30</option>
				<option>35</option>
				<option>40</option>
				<option>45</option>
				<option>50</option>
				<option>55</option>
				<OPTION value="<?php echo $appointment_min ?>" selected><?php echo $appointment_min ?></OPTION>
				</SELECT>

				</TD>
				</TR>
				<TR BGCOLOR="#E6E6E6">
				<TD align=center colspan=2>
				<FONT FACE="ARIAL,HELVETICA">Callback Disposition: <select size=1 name=CBstatus>\n";
				<?php echo "$statuses_list"; ?>
				</select>
				</TD>
				</TR>

				<TR BGCOLOR="#E6E6E6">
				<TD align=center colspan=2>
				<FONT FACE="ARIAL,HELVETICA">Comments: 

				<TEXTAREA name=comments ROWS=3 COLS=65><?php echo $CBcomments ?></TEXTAREA>
				</TD>
				</TR>

				<TR BGCOLOR="#E6E6E6">
				<TD align=center colspan=2>

				<SCRIPT LANGUAGE="JavaScript">

				function submit_form()
					{
					var appointment_hourFORM = document.getElementById('appointment_hour');
					var appointment_hourVALUE = appointment_hourFORM[appointment_hourFORM.selectedIndex].text;
					var appointment_minFORM = document.getElementById('appointment_min');
					var appointment_minVALUE = appointment_minFORM[appointment_minFORM.selectedIndex].text;

					document.vsn.appointment_time.value = appointment_hourVALUE + ":" + appointment_minVALUE + ":00";

					document.vsn.submit();
					}

				</SCRIPT>

				<input type=button value="SUBMIT" name=smt id=smt onClick="submit_form()">
				</TD>
				</TR>

				</TABLE>

				</FORM>

				<?php
				}
			else
				{
				echo "<BR>No Callback records found<BR>\n";
				}

			}
		else
			{
			echo "<BR>If you want to change this lead to a scheduled callback, first change the Disposition to CBHOLD, then submit and you will be able to set the callback date and time.<BR>\n";
			}
		echo "</TD></TR></TABLE>\n";

		echo "<br><br>\n";

		echo "<center>\n";

		if ($c > 0)
			{
			echo "<B>EXTENDED ALTERNATE PHONE NUMBERS FOR THIS LEAD:</B>\n";
			echo "<TABLE width=550 cellspacing=0 cellpadding=1>\n";
			echo "<tr><td><font size=1># </td><td><font size=2>ALT PHONE </td><td align=left><font size=2>ALT NOTE</td><td align=left><font size=2> ALT COUNT</td><td align=left><font size=2> ACTIVE</td></tr>\n";

			echo "$alts_output\n";

			echo "</TABLE>\n";
			echo "<BR><BR>\n";
			}



		### iframe for custom fields display/editing
		if ($custom_fields_enabled > 0)
			{
			$CLlist_id = $list_id;
			if (strlen($entry_list_id) > 2)
				{$CLlist_id = $entry_list_id;}
			$stmt="SHOW TABLES LIKE \"custom_$CLlist_id\";";
			if ($DB>0) {echo "$stmt";}
			$rslt=mysql_query($stmt, $link);
			$tablecount_to_print = mysql_num_rows($rslt);
			if ($tablecount_to_print > 0) 
				{
				$stmt="SELECT count(*) from custom_$CLlist_id where lead_id='$lead_id';";
				if ($DB>0) {echo "$stmt";}
				$rslt=mysql_query($stmt, $link);
				$fieldscount_to_print = mysql_num_rows($rslt);
				if ($fieldscount_to_print > 0) 
					{
					$rowx=mysql_fetch_row($rslt);
					$custom_records_count =	$rowx[0];

					echo "<B>CUSTOM FIELDS FOR THIS LEAD:</B><BR>\n";
					echo "<iframe src=\"../agc/vdc_form_display.php?lead_id=$lead_id&list_id=$CLlist_id&stage=DISPLAY&submit_button=YES&user=$PHP_AUTH_USER&pass=$PHP_AUTH_PW&bgcolor=E6E6E6\" style=\"background-color:transparent;\" scrolling=\"auto\" frameborder=\"2\" allowtransparency=\"true\" id=\"vcFormIFrame\" name=\"vcFormIFrame\" width=\"740\" height=\"300\" STYLE=\"z-index:18\"> </iframe>\n";
					echo "<BR><BR>";
					}
				}
			}


		echo "<B>CALLS TO THIS LEAD:</B>\n";
		echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
		echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>LENGTH</td><td align=left><font size=2> STATUS</td><td align=left><font size=2> TSR</td><td align=right><font size=2> CAMPAIGN</td><td align=right><font size=2> LIST</td><td align=right><font size=2> LEAD</td><td align=right><font size=2> HANGUP REASON</td><td align=right><font size=2> PHONE</td></tr>\n";

		echo "$call_log\n";

		echo "</TABLE>\n";
		echo "<BR><BR>\n";

		echo "<B>CLOSER RECORDS FOR THIS LEAD:</B>\n";
		echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
		echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>LENGTH</td><td align=left><font size=2> STATUS</td><td align=left><font size=2> TSR</td><td align=right><font size=2> CAMPAIGN</td><td align=right><font size=2> LIST</td><td align=right><font size=2> LEAD</td><td align=right><font size=2> WAIT</td><td align=right><font size=2> HANGUP REASON</td></tr>\n";

		echo "$closer_log\n";

		echo "</TABLE></center>\n";
		echo "<BR><BR>\n";


		echo "<B>AGENT LOG RECORDS FOR THIS LEAD:</B>\n";
		echo "<TABLE width=750 cellspacing=0 cellpadding=1>\n";
		echo "<tr><td><font size=1># </td><td><font size=2>DATE/TIME </td><td align=left><font size=2>CAMPAIGN</td><td align=left><font size=2> TSR</td><td align=left><font size=2> PAUSE</td><td align=right><font size=2> WAIT</td><td align=right><font size=2> TALK</td><td align=right><font size=2> DISPO</td><td align=right><font size=2> STATUS</td><td align=right><font size=2> GROUP</td><td align=right><font size=2> SUB</td></tr>\n";

			echo "$agent_log\n";

		echo "</TABLE>\n";
		echo "<BR><BR>\n";


		echo "<B>RECORDINGS FOR THIS LEAD:</B>\n";
		echo "<TABLE width=750 cellspacing=1 cellpadding=1>\n";
		echo "<tr><td><font size=1># </td><td align=left><font size=2> LEAD</td><td><font size=2>DATE/TIME </td><td align=left><font size=2>SECONDS </td><td align=left><font size=2> &nbsp; RECID</td><td align=center><font size=2>FILENAME</td><td align=left><font size=2>LOCATION</td><td align=left><font size=2>TSR</td></tr>\n";

		$stmt="select recording_id,channel,server_ip,extension,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,filename,location,lead_id,user,vicidial_id from recording_log where lead_id='" . mysql_real_escape_string($lead_id) . "' order by recording_id desc limit 500;";
		$rslt=mysql_query($stmt, $link);
		$logs_to_print = mysql_num_rows($rslt);
		if ($DB) {echo "$logs_to_print|$stmt|\n";}

		$u=0;
		while ($logs_to_print > $u) 
			{
			$row=mysql_fetch_row($rslt);
			if (eregi("1$|3$|5$|7$|9$", $u))
				{$bgcolor='bgcolor="#B9CBFD"';} 
			else
				{$bgcolor='bgcolor="#9BB9FB"';}

			$location = $row[11];

			if (strlen($location)>2)
				{
				$URLserver_ip = $location;
				$URLserver_ip = eregi_replace('http://','',$URLserver_ip);
				$URLserver_ip = eregi_replace('https://','',$URLserver_ip);
				$URLserver_ip = eregi_replace("\/.*",'',$URLserver_ip);
				$stmt="select count(*) from servers where server_ip='$URLserver_ip';";
				$rsltx=mysql_query($stmt, $link);
				$rowx=mysql_fetch_row($rsltx);
				
				if ($rowx[0] > 0)
					{
					$stmt="select recording_web_link,alt_server_ip,external_server_ip from servers where server_ip='$URLserver_ip';";
					$rsltx=mysql_query($stmt, $link);
					$rowx=mysql_fetch_row($rsltx);
					
					if (eregi("ALT_IP",$rowx[0]))
						{
						$location = eregi_replace($URLserver_ip, $rowx[1], $location);
						}
					if (eregi("EXTERNAL_IP",$rowx[0]))
						{
						$location = eregi_replace($URLserver_ip, $rowx[2], $location);
						}
					}
				}

			if (strlen($location)>30)
				{$locat = substr($location,0,27);  $locat = "$locat...";}
			else
				{$locat = $location;}
			if ( (eregi("ftp",$location)) or (eregi("http",$location)) )
				{$location = "<a href=\"$location\">$locat</a>";}
			else
				{$location = $locat;}
			$u++;
			echo "<tr $bgcolor>";
			echo "<td><font size=1>$u</td>";
			echo "<td align=left><font size=2> $row[12] </td>";
			echo "<td align=left><font size=1> $row[4] </td>\n";
			echo "<td align=left><font size=2> $row[8] </td>\n";
			echo "<td align=left><font size=2> $row[0] &nbsp;</td>\n";
			echo "<td align=center><font size=1> $row[10] </td>\n";
			echo "<td align=left><font size=2> $location </td>\n";
			echo "<td align=left><font size=2> <A HREF=\"user_stats.php?user=$row[13]\" target=\"_blank\">$row[13]</A> </td>";
			echo "</tr>\n";

			}


		echo "</TABLE><BR><BR>\n";


		$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level >= 9 and modify_leads='1';";
		if ($DB) {echo "|$stmt|\n";}
		if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$admin_display=$row[0];
		if ($admin_display > 0)
			{
			echo "<a href=\"./admin.php?ADD=720000000000000&stage=$lead_id&category=LEADS\">Click here to see Lead Modify changes to this lead</a>\n";
			}

		echo "</center>\n";
		}
	}

$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds</font>";
*/
?>






