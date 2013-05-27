<?
date_default_timezone_set('Europe/Lisbon');
require("../../../ini/_json_convert.php");
require("../../../ini/dbconnect.php");
require("../../../ini/functions.php");

foreach ($_POST as $key => $value) {
	${$key} = $value;
}
foreach ($_GET as $key => $value) {
	${$key} = $value;
}







//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if($action== "add_new_pause")
{
    $query = mysql_query("SELECT distinct(pause_code) FROM vicidial_pause_codes", $link);
    $num_pauses = (mysql_num_rows($query) + $counter);
    while(strlen($num_pauses) < 5) { $num_pauses = "0".$num_pauses; }
    $id_pausa = "P".$num_pauses; 
    echo $id_pausa;
}

if($action== "edit_pause_time") 
{
	mysql_query("UPDATE vicidial_pause_codes SET max_time='$sent_new_time' WHERE pause_code='$sent_pause_id'", $link);
}

if($action== "add_new_feed") 
{
    $query = mysql_query("SELECT distinct(status) FROM vicidial_campaign_statuses", $link);
    $num_feeds = (mysql_num_rows($query) + $counter);
    while(strlen($num_feeds) < 5) { $num_feeds = "0".$num_feeds; }
    $id_feed = "S".$num_feeds; 
    echo $id_feed;
}
if($action=="add_new_database")
{
	$query = mysql_query("SELECT count(*) FROM vicidial_lists", $link);
    $num_lists = mysql_fetch_row($query);
    $list_id = ($num_lists[0] + 100000 + 30);
	 
	mysql_query("INSERT INTO vicidial_lists (list_id, list_name, campaign_id, active) VALUES ('$list_id', '$sent_database_name', '$sent_campaign_id', 'Y')") or die (mysql_error());
	echo $list_id;
}


// SUBMITS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if($action== "submit_opcoesgerais")
{
			
	$camp_exists = mysql_fetch_row(mysql_query("SELECT count(*) FROM vicidial_campaigns WHERE campaign_id='$sent_campaign_id'",$link));
	$camp_exists = $camp_exists[0];
	
	
	if($camp_exists > 0){
		$query = "UPDATE vicidial_campaigns SET 
					campaign_name='$sent_campaign_name', 
					campaign_description='$sent_campaign_description', 
					active='$sent_campaign_active', 
					lead_order='$sent_campaign_lead_order',
					auto_dial_level='$sent_campaign_ratio',
					next_agent_call='$sent_campaign_next_call',
					campaign_recording='$sent_campaign_recording',
					dial_method='$sent_campaign_type'
					WHERE campaign_id='$sent_campaign_id'
					
					";
		mysql_query($query, $link);
	} else {	

	
	$query = "INSERT INTO vicidial_campaigns
		(
		campaign_id,
		campaign_name,
		campaign_description,
		active,
		lead_order,
		allow_closers,
		hopper_level,
		auto_dial_level,
		next_agent_call,
		local_call_time,
		dial_timeout,
		dial_prefix,
		allcalls_delay,
		campaign_recording,
		campaign_rec_filename,
		scheduled_callbacks,
		drop_call_seconds,
		drop_action,
		dial_method,
		adaptive_dropped_percentage,
		no_hopper_leads_logins,
		scheduled_callbacks_count,
		scheduled_callbacks_alert,
		dial_statuses,
		agent_pause_codes_active,
		omit_phone_code,
		auto_alt_dial
		)
		VALUES		(
		'$sent_campaign_id',
		'".mysql_real_escape_string($sent_campaign_name)."',
		'".mysql_real_escape_string($sent_campaign_description)."',
		'".mysql_real_escape_string($sent_campaign_active)."',
		'".mysql_real_escape_string($sent_campaign_lead_order)."',
		'Y',
		'50',
		'".mysql_real_escape_string($sent_campaign_ratio)."',
		'".mysql_real_escape_string($sent_campaign_next_call)."',
		'24hours',
		'35',
		'X',
		'0',
		'".mysql_real_escape_string($sent_campaign_recording)."',
		'FULLDATE_CUSTPHONE',
		'Y',
		'0',
		'HANGUP',
		'".mysql_real_escape_string($sent_campaign_type)."',
		'3',
		'Y',
		'LIVE',
		'BLINK_RED',
		' DC PU PDROP ERI NA DROP B NEW -',
		'FORCE',
		'Y',
		'ALT_AND_ADDR3'); ";
		mysql_query($query,$link);
		$query = "INSERT INTO	vicidial_campaign_stats
		(campaign_id)
		VALUES		
		('$sent_campaign_id');";
		mysql_query($query,$link);
		
		$query = "SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]'";
        $query = mysql_query($query, $link);
        $user_type = mysql_fetch_row($query);
        $user_type = strtoupper($user_type[0]);
        	
        if($user_type=='ADMIN') {
           $query = "SELECT allowed_campaigns, user_group FROM vicidial_user_groups ";
           $query = mysql_query($query);
               
        for($i=0;$i<mysql_num_rows($query);$i++)
        {
            $row=mysql_fetch_assoc($query);
            $allowed_campaigns = $row['allowed_campaigns'];
            $new_allowed_campaigns = " $sent_campaign_id$allowed_campaigns";
            $user_group = $row['user_group'];
            mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns='$new_allowed_campaigns' WHERE user_group='$user_group'",$link);
        }
        } else {
            $query="SELECT allowed_campaigns, vug.user_group FROM vicidial_user_groups vug INNER JOIN vicidial_users vu ON vug.user_group=vu.user_group WHERE user='$user'; ";
            $query = mysql_query($query);
            $row = mysql_fetch_assoc($query);
            $allowed_campaigns = $row['allowed_campaigns'];
            $new_allowed_campaigns = " $sent_campaign_id$allowed_campaigns";
            $user_group = $row['user_group'];
            mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns='$new_allowed_campaigns' WHERE user_group='$user_group'",$link);
        } 
	}
		
		
		
}
if($action== "submit_pausas")
{
	mysql_query("DELETE FROM vicidial_pause_codes WHERE campaign_id='$sent_campaign_id'");
	for($i=0;$i<count($sent_pause_ids);$i++){
		mysql_query("INSERT INTO vicidial_pause_codes (pause_code, pause_code_name, campaign_id, max_time, active) VALUES ('$sent_pause_ids[$i]','$sent_pause_names[$i]','$sent_campaign_id','$sent_pause_time[$i]','$sent_pause_active[$i]' )",$link);
	}
}
if($action== "submit_feeds")
{
	mysql_query("DELETE FROM vicidial_campaign_statuses WHERE campaign_id='$sent_campaign_id'");
		for($i=0;$i<count($sent_feed_ids);$i++){
		mysql_query("INSERT INTO vicidial_campaign_statuses (status, status_name, selectable, campaign_id, human_answered, scheduled_callback) VALUES ('$sent_feed_ids[$i]','$sent_feed_names[$i]','$sent_feed_active[$i]','$sent_campaign_id','$sent_feed_ishuman[$i]','$sent_feed_callback[$i]' )",$link);
	}	
}
if($action== "submit_script")
{
	
	$con = mysql_connect("localhost","sipsadmin", "sipsps2012");
	mysql_select_db("asterisk", $con);
	
	mysql_query("DROP TABLE IF EXISTS custom_$sent_campaign_id") or die (mysql_error());
	mysql_query("CREATE TABLE IF NOT EXISTS custom_$sent_campaign_id LIKE custom_$sent_campaign_copy") or die (mysql_error());
	
	mysql_query("DELETE FROM vicidial_lists_fields WHERE campaign_id='$sent_campaign_id'") or die(mysql_error());
	mysql_query("INSERT INTO vicidial_lists_fields 
                        (SELECT '', `list_id`, `field_label`, `field_name`, `field_description`, `field_rank`, `field_help`, `field_type`, `field_options`, `field_size`, `field_max`, `field_default`, `field_cost`, `field_required`, `name_position`, `multi_position`, `field_order`, '$sent_campaign_id', `action` 
                        FROM vicidial_lists_fields where campaign_id='$sent_campaign_copy'); ") or die(mysql_error());
	
	mysql_close($con);
	
}
if($action== "submit_dfields")
{
	mysql_query("DELETE FROM vicidial_list_ref WHERE campaign_id='$sent_campaign_id'") or die(mysql_error());
	
	for($i=0; $i<count($sent_sortedIDs); $i++)
	{
		$sent_sortedIDs[$i] = strtoupper($sent_sortedIDs[$i]);
		mysql_query("INSERT INTO vicidial_list_ref (Name, Display_name, readonly, active, campaign_id, field_order) VALUES ('$sent_sortedIDs[$i]', '$sent_sortedLabels[$i]', '$sent_sortedReadOnly[$i]', '1', '$sent_campaign_id', '$sent_sortedOrder[$i]')") or die(mysql_error());	
	}
	for($i=0; $i<count($sent_fillers); $i++)
	{
		$sent_fillers[$i] = strtoupper($sent_fillers[$i]);
		mysql_query("INSERT INTO vicidial_list_ref (Name, active, campaign_id) VALUES ('$sent_fillers[$i]', '0', '$sent_campaign_id')") or die(mysql_error());	

	}
	
}
if($action=="submit_reciclagem")
{
	mysql_query("DELETE FROM vicidial_lead_recycle WHERE campaign_id='$sent_campaign_id'") or die(mysql_error());
	
	for($i=0;count($sent_recycle_id);$i++)
	{
		mysql_query("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES ('$sent_campaign_id','$sent_recycle_id', '$sent_recycle_')");
		
	}
	
	
}

if($action== "remove_script")
{
	
	$con = mysql_connect("localhost","sipsadmin", "sipsps2012");
	mysql_select_db("asterisk", $con);
	
	mysql_query("DROP TABLE IF EXISTS custom_$sent_campaign_id") or die (mysql_error());
		
	mysql_query("DELETE FROM vicidial_lists_fields WHERE campaign_id='$sent_campaign_id'") or die(mysql_error());
	
	mysql_close($con);
	
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($action=="add_new_database_campaign")
{
	
	if($sent_new_campaign)
	{
		$query = "INSERT INTO vicidial_campaigns
		(
		campaign_id,
		campaign_name,
		campaign_description,
		active,
		lead_order,
		allow_closers,
		hopper_level,
		auto_dial_level,
		next_agent_call,
		local_call_time,
		dial_timeout,
		dial_prefix,
		allcalls_delay,
		campaign_recording,
		campaign_rec_filename,
		scheduled_callbacks,
		drop_call_seconds,
		drop_action,
		dial_method,
		adaptive_dropped_percentage,
		no_hopper_leads_logins,
		scheduled_callbacks_count,
		scheduled_callbacks_alert,
		dial_statuses,
		agent_pause_codes_active,
		omit_phone_code,
		auto_alt_dial
		)
		VALUES		(
		'$sent_campaign_id',
		'$sent_campaign_name',
		'',
		'Y',
		'DOWN',
		'Y',
		'50',
		'1',
		'longest_wait_time',
		'24hours',
		'35',
		'0134',
		'0',
		'ALLFORCE',
		'FULLDATE_CUSTPHONE',
		'Y',
		'0',
		'HANGUP',
		'RATIO',
		'3',
		'Y',
		'LIVE',
		'BLINK_RED',
		' DC PU PDROP ERI NA DROP B NEW -',
		'FORCE',
		'Y',
		'ALT_AND_ADDR3'); ";
		mysql_query($query,$link);
		$query = "INSERT INTO	vicidial_campaign_stats
		(campaign_id)
		VALUES		
		('$sent_campaign_id');";
		mysql_query($query,$link);
		
		$query = "SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]'";
        $query = mysql_query($query, $link);
        $user_type = mysql_fetch_row($query);
        $user_type = strtoupper($user_type[0]);
        	
        if($user_type=='ADMIN') {
           $query = "SELECT allowed_campaigns, user_group FROM vicidial_user_groups ";
           $query = mysql_query($query);
               
        for($i=0;$i<mysql_num_rows($query);$i++)
        {
            $row=mysql_fetch_assoc($query);
            $allowed_campaigns = $row['allowed_campaigns'];
            $new_allowed_campaigns = " $sent_campaign_id$allowed_campaigns";
            $user_group = $row['user_group'];
            mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns='$new_allowed_campaigns' WHERE user_group='$user_group'",$link);
        }
        } else {
            $query="SELECT allowed_campaigns, vug.user_group FROM vicidial_user_groups vug INNER JOIN vicidial_users vu ON vug.user_group=vu.user_group WHERE user='$_SERVER[PHP_AUTH_USER]'; ";
            $query = mysql_query($query) or die(mysql_error());
            $row = mysql_fetch_assoc($query);
            $allowed_campaigns = $row['allowed_campaigns'];
            $new_allowed_campaigns = " $sent_campaign_id$allowed_campaigns";
            $user_group = $row['user_group'];
            mysql_query("UPDATE vicidial_user_groups SET allowed_campaigns='$new_allowed_campaigns' WHERE user_group='$user_group'",$link);
        } 
	mysql_query("INSERT INTO vicidial_list_ref (Name, Display_name, readonly, active, campaign_id, field_order) VALUES ('PHONE_NUMBER', 'Telefone', '0', '1', '$sent_campaign_id', '0')") or die(mysql_error());	
	mysql_query("INSERT INTO vicidial_list_ref (Name, Display_name, readonly, active, campaign_id, field_order) VALUES ('COMMENTS', 'Mensagem', '0', '1', '$sent_campaign_id', '2')") or die(mysql_error());	
	mysql_query("INSERT INTO vicidial_list_ref (Name, Display_name, readonly, active, campaign_id, field_order) VALUES ('EMAIL', 'Mensagem Inicial', '0', '1', '$sent_campaign_id', '1')") or die(mysql_error());	
	


	$query = mysql_query("SELECT count(*) FROM vicidial_lists", $link);
    $num_lists = mysql_fetch_row($query);
    $list_id = ($num_lists[0] + 100000 + 30);
	 
	mysql_query("INSERT INTO vicidial_lists (list_id, list_name, campaign_id, active) VALUES ('$list_id', '$sent_database_name', '$sent_campaign_id', 'Y')") or die (mysql_error());

	mysql_query("INSERT INTO vicidial_lead_recycle 
		(campaign_id, status, attempt_delay, attempt_maximum, active) 
		VALUES
		('$sent_campaign_id','B','1800','10','Y'),
		('$sent_campaign_id','DC','1800','10','Y'),
		('$sent_campaign_id','DROP','120','10','Y'),
		('$sent_campaign_id','ERI','1800','10','Y'),
		('$sent_campaign_id','NA','3600','10','Y'),
		('$sent_campaign_id','PDROP','120','10','Y'),
		('$sent_campaign_id','PU','120','10','Y'); ",$link); 
	
	$query = mysql_query("SELECT server_ip FROM servers", $link);
	$server = mysql_fetch_row($query);
	
	$temp_explode = explode("C", $sent_campaign_id);
	
	$remote_agent = "1".$temp_explode[1];
		
	mysql_query("INSERT INTO vicidial_remote_agents (user_start,number_of_lines,server_ip,conf_exten,status,campaign_id) values('$remote_agent','1','$server[0]','787778','INACTIVE','$sent_campaign_id')", $link);	
	mysql_query("INSERT INTO vicidial_users (user, pass, full_name, user_group, active) VALUES ('$remote_agent', '1234', '$sent_campaign_id', '$user_group', 'Y')");
	
	
	mysql_query("INSERT INTO vicidial_campaign_statuses (status, status_name, selectable, campaign_id, human_answered, scheduled_callback) VALUES 
				('MSG001', 'Ouviu Mensagem', 'N', '$sent_campaign_id', 'Y', 'N'),
				('MSG002', 'Declinou Mensagem', 'N', '$sent_campaign_id', 'Y', 'N'),
				('MSG003', 'Atendeu e Declinou', 'N', '$sent_campaign_id', 'Y', 'N'),
				('MSG004', 'Ouviu Mensagem e SMS', 'N', '$sent_campaign_id', 'Y', 'N'),
				('MSG005', 'Ouviu Mensagem e EMAIL', 'N', '$sent_campaign_id', 'Y', 'N'),
				('MSG006', 'Tranferência para Call-Center', 'N', '$sent_campaign_id', 'Y', 'N'),
				('MSG007', 'Solicitou Contacto', 'N', '$sent_campaign_id', 'Y', 'N')") or die(mysql_error());
				
	//mysql_query("INSERT ");
	
		
	} else {
		
		$query = mysql_query("SELECT count(*) FROM vicidial_lists", $link);
    $num_lists = mysql_fetch_row($query);
    $list_id = ($num_lists[0] + 100000 + 30);
	 
	mysql_query("INSERT INTO vicidial_lists (list_id, list_name, campaign_id, active) VALUES ('$list_id', '$sent_database_name', '$sent_campaign_id', 'Y')") or die (mysql_error());


		
		}
	
	
echo $list_id;
}





?>