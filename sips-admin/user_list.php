<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<title>SIPS</title>
<?php
require("../sips-admin/dbconnect.php");

######################
# ADD=3 modify user info in the system
######################

$ADD = $_GET['ADD'];
$user = $_GET['user'];

$LOGuser_level = 9;

if ($ADD==3)
	{
	if ($LOGmodify_users==1)
		{
		if ( ($SSadmin_modify_refresh > 1) and ($modify_refresh_set < 1) )
			{
			$modify_url = "$PHP_SELF?ADD=3&user=$user";
			$modify_footer_refresh=1;
			}

		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$stmt="SELECT user_id,user,pass,full_name,user_level,user_group,phone_login,phone_pass,delete_users,delete_user_groups,delete_lists,delete_campaigns,delete_ingroups,delete_remote_agents,load_leads,campaign_detail,ast_admin_access,ast_delete_phones,delete_scripts,modify_leads,hotkeys_active,change_agent_campaign,agent_choose_ingroups,closer_campaigns,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,delete_filters,alter_agent_interface_options,closer_default_blended,delete_call_times,modify_call_times,modify_users,modify_campaigns,modify_lists,modify_scripts,modify_filters,modify_ingroups,modify_usergroups,modify_remoteagents,modify_servers,view_reports,vicidial_recording_override,alter_custdata_override,qc_enabled,qc_user_level,qc_pass,qc_finish,qc_commit,add_timeclock_log,modify_timeclock_log,delete_timeclock_log,alter_custphone_override,vdc_agent_api_access,modify_inbound_dids,delete_inbound_dids,active,alert_enabled,download_lists,agent_shift_enforcement_override,manager_shift_enforcement_override,shift_override_flag,export_reports,delete_from_dnc,email,user_code,territory,allow_alerts,agent_choose_territories,custom_one,custom_two,custom_three,custom_four,custom_five,voicemail_id,agent_call_log_view_override,callcard_admin,agent_choose_blended,realtime_block_user_info,custom_fields_modify,force_change_password,agent_lead_search_override from vicidial_users where user='$user';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$user_id =				$row[0];
		$user =					$row[1];
		$pass =					$row[2];
		$full_name =			$row[3];
		$user_level =			$row[4];
		$user_group =			$row[5];
		$phone_login =			$row[6];
		$phone_pass =			$row[7];
		$delete_users =			$row[8];
		$delete_user_groups =	$row[9];
		$delete_lists =			$row[10];
		$delete_campaigns =		$row[11];
		$delete_ingroups =		$row[12];
		$delete_remote_agents =	$row[13];
		$load_leads =			$row[14];
		$campaign_detail =		$row[15];
		$ast_admin_access =		$row[16];
		$ast_delete_phones =	$row[17];
		$delete_scripts =		$row[18];
		$modify_leads =			$row[19];
		$hotkeys_active =		$row[20];
		$change_agent_campaign =$row[21];
		$agent_choose_ingroups =$row[22];
		$scheduled_callbacks =	$row[24];
		$agentonly_callbacks =	$row[25];
		$agentcall_manual =		$row[26];
		$vicidial_recording =	$row[27];
		$vicidial_transfers =	$row[28];
		$delete_filters =		$row[29];
		$alter_agent_interface_options =$row[30];
		$closer_default_blended =		$row[31];
		$delete_call_times =	$row[32];
		$modify_call_times =	$row[33];
		$modify_users =			$row[34];
		$modify_campaigns =		$row[35];
		$modify_lists =			$row[36];
		$modify_scripts =		$row[37];
		$modify_filters =		$row[38];
		$modify_ingroups =		$row[39];
		$modify_usergroups =	$row[40];
		$modify_remoteagents =	$row[41];
		$modify_servers =		$row[42];
		$view_reports =			$row[43];
		$vicidial_recording_override =	$row[44];
		$alter_custdata_override = $row[45];
		$qc_enabled =			$row[46];
		$qc_user_level =		$row[47];
		$qc_pass =				$row[48];
		$qc_finish =			$row[49];
		$qc_commit =			$row[50];
		$add_timeclock_log =	$row[51];
		$modify_timeclock_log = $row[52];
		$delete_timeclock_log = $row[53];
		$alter_custphone_override = $row[54];
		$vdc_agent_api_access = $row[55];
		$modify_inbound_dids =	$row[56];
		$delete_inbound_dids =	$row[57];
		$active =				$row[58];
		$alert_enabled =		$row[59];
		$download_lists =		$row[60];
		$agent_shift_enforcement_override =	$row[61];
		$manager_shift_enforcement_override =	$row[62];
		$export_reports =		$row[64];
		$delete_from_dnc =		$row[65];
		$email =				$row[66];
		$user_code =			$row[67];
		$territory =			$row[68];
		$allow_alerts =			$row[69];
		$agent_choose_territories = $row[70];
		$user_custom_one =		$row[71];
		$user_custom_two =		$row[72];
		$user_custom_three =	$row[73];
		$user_custom_four =		$row[74];
		$user_custom_five =		$row[75];
		$voicemail_id =			$row[76];
		$agent_call_log_view_override = $row[77];
		$callcard_admin =		$row[78];
		$agent_choose_blended = $row[79];
		$realtime_block_user_info = $row[80];
		$custom_fields_modify =	$row[81];
		$force_change_password = $row[82];
		$agent_lead_search_override = $row[83];

		if ( ($user_level >= $LOGuser_level) and ($LOGuser_level < 9) )
			{
			echo "<br>You do not have permissions to modify this user: $user\n";
			}
		else
			{
			echo "<br>MODIFY A USERS RECORD: $user<form action=$PHP_SELF method=POST>\n";
			if ($LOGuser_level > 8)
				{echo "<input type=hidden name=ADD value=4A>\n";}
			else
				{
				if ($LOGalter_agent_interface == "1")
					{echo "<input type=hidden name=ADD value=4B>\n";}
				else
					{echo "<input type=hidden name=ADD value=4>\n";}
				}
			if ($SScustom_fields_enabled < 1)
				{
				echo "<input type=hidden name=custom_fields_modify value=\"$custom_fields_modify\">\n";
				}

			echo "<input type=hidden name=user value=\"$user\">\n";
			echo "<center><TABLE width=$section_width cellspacing=3>\n";
			echo "<tr bgcolor=#dddddd><td align=right>User Number: </td><td align=left><b>$user</b>$NWB#vicidial_users-user$NWE</td></tr>\n";

			echo "<tr bgcolor=#dddddd><td align=right>Password: </td><td align=left style=\"display:table-cell; vertical-align:middle;\"><input type=text id=reg_pass name=pass size=20 maxlength=20 value=\"$pass\" onkeyup=\"return pwdChanged('reg_pass','reg_pass_img');\">$NWB#vicidial_users-pass$NWE &nbsp; &nbsp; Strength: <IMG id=reg_pass_img src='images/pixel.gif' style=\"vertical-align:middle;\" onLoad=\"return pwdChanged('reg_pass','reg_pass_img');\"></td></tr>\n";

			echo "<tr bgcolor=#dddddd><td align=right>Force Change Password: </td><td align=left><select size=1 name=force_change_password><option>Y</option><option>N</option><option SELECTED>$force_change_password</option></select>$NWB#vicidial_users-force_change_password$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=30 maxlength=30 value=\"$full_name\">$NWB#vicidial_users-full_name$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>User Level: </td><td align=left><select size=1 name=user_level>";
			$h=1;
			while ($h<=$LOGuser_level)
				{
				echo "<option>$h</option>";
				$h++;
				}
			echo "<option SELECTED>$user_level</option></select>$NWB#vicidial_users-user_level$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right><A HREF=\"$PHP_SELF?ADD=311111&user_group=$user_group\">User Group</A>: </td><td align=left><select size=1 name=user_group>\n";

			$stmt="SELECT user_group,group_name from vicidial_user_groups order by user_group";
			$rslt=mysql_query($stmt, $link);
			$Ugroups_to_print = mysql_num_rows($rslt);
			$Ugroups_list='';
			$o=0;
			while ($Ugroups_to_print > $o) 
				{
				$rowx=mysql_fetch_row($rslt);
				$Ugroups_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				$o++;
				}
			echo "$Ugroups_list";
			echo "<option SELECTED>$user_group</option>\n";
			echo "</select>$NWB#vicidial_users-user_group$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>Phone Login: </td><td align=left><input type=text name=phone_login size=20 maxlength=20 value=\"$phone_login\">$NWB#vicidial_users-phone_login$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>Phone Pass: </td><td align=left><input type=text name=phone_pass size=20 maxlength=20 value=\"$phone_pass\">$NWB#vicidial_users-phone_pass$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$active</option></select>$NWB#vicidial_users-active$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>Voicemail ID: </td><td align=left><input type=text name=voicemail_id id=voicemail_id size=12 maxlength=10 value=\"$voicemail_id\"> <a href=\"javascript:launch_vm_chooser('voicemail_id','vm',100);\">voicemail chooser</a>$NWB#vicidial_users-voicemail_id$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>Email: </td><td align=left><input type=text name=email size=40 maxlength=100 value=\"$email\">$NWB#vicidial_users-optional$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>User Code: </td><td align=left><input type=text name=user_code size=40 maxlength=100 value=\"$user_code\">$NWB#vicidial_users-optional$NWE</td></tr>\n";
			echo "<tr bgcolor=#dddddd><td align=right>Main Territory: </td><td align=left><input type=text name=territory size=40 maxlength=100 value=\"$territory\">$NWB#vicidial_users-optional$NWE</td></tr>\n";

			if ($SSuser_territories_active > 0)
				{
				$stmt="SELECT vut.territory,vt.territory_description from vicidial_user_territories vut,vicidial_territories vt where user='$user' and vut.territory=vt.territory;";
				$rslt=mysql_query($stmt, $link);
				$Uterrs_to_print = mysql_num_rows($rslt);
				$Uterrs_list='';
				$o=0;
				while ($Uterrs_to_print > $o) 
					{
					$rowx=mysql_fetch_row($rslt);
					$Uterrs_list .= "$rowx[0] - $rowx[1]<BR>\n";
					$o++;
					}
				echo "<tr bgcolor=#dddddd><td align=right><a href=\"user_territories.php\">User Territories</a>: </td><td align=left>$Uterrs_list</tr>\n";
				}
			$LOGalter_agent_interface = "1";
			if ( ($LOGuser_level > 8) or ($LOGalter_agent_interface == "1") )
				{
				echo "<tr bgcolor=#616161><td colspan=2 align=center><font color=white><B>AGENT INTERFACE OPTIONS:</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Agent Choose Ingroups: </td><td align=left><select size=1 name=agent_choose_ingroups><option>0</option><option>1</option><option SELECTED>$agent_choose_ingroups</option></select>$NWB#vicidial_users-agent_choose_ingroups$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Agent Choose Blended: </td><td align=left><select size=1 name=agent_choose_blended><option>0</option><option>1</option><option SELECTED>$agent_choose_blended</option></select>$NWB#vicidial_users-agent_choose_blended$NWE</td></tr>\n";
				if ($SSuser_territories_active > 0)
					{
					echo "<tr bgcolor=#dddddd><td align=right>Agent Choose Territories: </td><td align=left><select size=1 name=agent_choose_territories><option>0</option><option>1</option><option SELECTED>$agent_choose_territories</option></select>$NWB#vicidial_users-agent_choose_territories$NWE</td></tr>\n";
					}
				echo "<tr bgcolor=#dddddd><td align=right>Hot Keys Active: </td><td align=left><select size=1 name=hotkeys_active><option>0</option><option>1</option><option SELECTED>$hotkeys_active</option></select>$NWB#vicidial_users-hotkeys_active$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Scheduled Callbacks: </td><td align=left><select size=1 name=scheduled_callbacks><option>0</option><option>1</option><option SELECTED>$scheduled_callbacks</option></select>$NWB#vicidial_users-scheduled_callbacks$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Agent-Only Callbacks: </td><td align=left><select size=1 name=agentonly_callbacks><option>0</option><option>1</option><option SELECTED>$agentonly_callbacks</option></select>$NWB#vicidial_users-agentonly_callbacks$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Agent Call Manual: </td><td align=left><select size=1 name=agentcall_manual><option>0</option><option>1</option><option SELECTED>$agentcall_manual</option></select>$NWB#vicidial_users-agentcall_manual$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Vicidial Recording: </td><td align=left><select size=1 name=vicidial_recording><option>0</option><option>1</option><option SELECTED>$vicidial_recording</option></select>$NWB#vicidial_users-vicidial_recording$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Vicidial Transfers: </td><td align=left><select size=1 name=vicidial_transfers><option>0</option><option>1</option><option SELECTED>$vicidial_transfers</option></select>$NWB#vicidial_users-vicidial_transfers$NWE</td></tr>\n";
				if ($SSoutbound_autodial_active > 0)
					{
					echo "<tr bgcolor=#dddddd><td align=right>Closer Default Blended: </td><td align=left><select size=1 name=closer_default_blended><option>0</option><option>1</option><option SELECTED>$closer_default_blended</option></select>$NWB#vicidial_users-closer_default_blended$NWE</td></tr>\n";
					}
				echo "<tr bgcolor=#dddddd><td align=right>VICIDIAL Recording Override: </td><td align=left><select size=1 name=vicidial_recording_override><option>DISABLED</option><option>NEVER</option><option>ONDEMAND</option><option>ALLCALLS</option><option>ALLFORCE</option><option SELECTED>$vicidial_recording_override</option></select>$NWB#vicidial_users-vicidial_recording_override$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Agent Alter Customer Data Override: </td><td align=left><select size=1 name=alter_custdata_override><option>NOT_ACTIVE</option><option>ALLOW_ALTER</option><option SELECTED>$alter_custdata_override</option></select>$NWB#vicidial_users-alter_custdata_override$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Agent Alter Customer Phone Override: </td><td align=left><select size=1 name=alter_custphone_override><option>NOT_ACTIVE</option><option>ALLOW_ALTER</option><option SELECTED>$alter_custphone_override</option></select>$NWB#vicidial_users-alter_custphone_override$NWE</td></tr>\n";

				echo "<tr bgcolor=#dddddd><td align=right>Agent Shift Enforcement Override: </td><td align=left><select size=1 name=agent_shift_enforcement_override><option>DISABLED</option><option>OFF</option><option>START</option><option>ALL</option><option SELECTED>$agent_shift_enforcement_override</option></select>$NWB#vicidial_users-agent_shift_enforcement_override$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Agent Call Log View Override: </td><td align=left><select size=1 name=agent_call_log_view_override><option>DISABLED</option><option>Y</option><option>N</option><option SELECTED>$agent_call_log_view_override</option></select>$NWB#vicidial_users-agent_call_log_view_override$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Agent Lead Search Override: </td><td align=left><select size=1 name=agent_lead_search><option>DISABLED</option><option>ENABLED</option><option>NOT_ACTIVE</option><option SELECTED>$agent_lead_search_override</option></select>$NWB#vicidial_users-agent_lead_search_override$NWE</td></tr>\n";

				echo "<tr bgcolor=#dddddd><td align=right>Alert Enabled: </td><td align=left>$alert_enabled $NWB#vicidial_users-alert_enabled$NWE</td></tr>\n";

				echo "<tr bgcolor=#dddddd><td align=right>Allow Alerts: </td><td align=left><select size=1 name=allow_alerts><option>0</option><option>1</option><option SELECTED>$allow_alerts</option></select>$NWB#vicidial_users-allow_alerts$NWE</td></tr>\n";

				echo "<tr bgcolor=#dddddd><td align=center colspan=2>Campaign Ranks: $NWB#vicidial_users-campaign_ranks$NWE<BR>\n";
				echo "<table border=0>\n";
				echo "$RANKcampaigns_list";
				echo "</table>\n";
				echo "</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=center colspan=2>Inbound Groups: $NWB#vicidial_users-closer_campaigns$NWE<BR>\n";
				echo "<table border=0>\n";
				echo "$RANKgroups_list";
				echo "</table>\n";
				echo "</td></tr>\n";

				echo "<tr bgcolor=#dddddd><td align=right>Custom 1: </td><td align=left><input type=text name=custom_one size=50 maxlength=100 value=\"$user_custom_one\">$NWB#vicidial_users-custom_one$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Custom 2: </td><td align=left><input type=text name=custom_two size=50 maxlength=100 value=\"$user_custom_two\">$NWB#vicidial_users-custom_two$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Custom 3: </td><td align=left><input type=text name=custom_three size=50 maxlength=100 value=\"$user_custom_three\">$NWB#vicidial_users-custom_three$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Custom 4: </td><td align=left><input type=text name=custom_four size=50 maxlength=100 value=\"$user_custom_four\">$NWB#vicidial_users-custom_four$NWE</td></tr>\n";
				echo "<tr bgcolor=#dddddd><td align=right>Custom 5: </td><td align=left><input type=text name=custom_five size=50 maxlength=100 value=\"$user_custom_five\">$NWB#vicidial_users-custom_five$NWE</td></tr>\n";
				$SSqc_features_active = 1;
				if ($SSqc_features_active > 0)
					{
					echo "<tr bgcolor=#bcbcbc><td align=right>QC Enabled: </td><td align=left><select size=1 name=qc_enabled><option>0</option><option>1</option><option SELECTED>$qc_enabled</option></select>$NWB#vicidial_users-qc_enabled$NWE</td></tr>\n";
					echo "<tr bgcolor=#bcbcbc><td align=right>QC User Level: </td><td align=left><select size=1 name=qc_user_level><option value=1>1 - Modify Nothing</option><option value=2>2 - Modify Nothing Except Status</option><option value=3>3 - Modify All Fields</option><option value=4>4 - Verify First Round of QC</option><option value=5>5 - View QC Statistics</option><option value=6>6 - Ability to Modify FINISHed records</option><option value=7>7 - Manager Level</option><option SELECTED>$qc_user_level</option></select>$NWB#vicidial_users-qc_user_level$NWE</td></tr>\n";
					echo "<tr bgcolor=#bcbcbc><td align=right>QC Pass: </td><td align=left><select size=1 name=qc_pass><option>0</option><option>1</option><option SELECTED>$qc_pass</option></select>$NWB#vicidial_users-qc_pass$NWE</td></tr>\n";
					echo "<tr bgcolor=#bcbcbc><td align=right>QC Finish: </td><td align=left><select size=1 name=qc_finish><option>0</option><option>1</option><option SELECTED>$qc_finish</option></select>$NWB#vicidial_users-qc_finish$NWE</td></tr>\n";
					echo "<tr bgcolor=#bcbcbc><td align=right>QC Commit: </td><td align=left><select size=1 name=qc_commit><option>0</option><option>1</option><option SELECTED>$qc_commit</option></select>$NWB#vicidial_users-qc_commit$NWE</td></tr>\n";
					}
				}
			if ($LOGuser_level > 8)
				{
				echo "<tr bgcolor=#616161><td colspan=2 align=center><font color=white><B>ADMIN REPORT OPTIONS:</td></tr>\n";

				echo "<tr bgcolor=#bcbcbc><td align=right>Realtime Block User Info: </td><td align=left><select size=1 name=realtime_block_user_info><option>0</option><option>1</option><option SELECTED>$realtime_block_user_info</option></select>$NWB#vicidial_users-realtime_block_user_info$NWE</td></tr>\n";

				echo "<tr bgcolor=#616161><td colspan=2 align=center><font color=white><B>ADMIN INTERFACE OPTIONS:</td></tr>\n";

				#bcbcbc
				#cccccc
				echo "<tr bgcolor=#bcbcbc><td align=right>View Reports: </td><td align=left><select size=1 name=view_reports><option>0</option><option>1</option><option SELECTED>$view_reports</option></select>$NWB#vicidial_users-view_reports$NWE</td></tr>\n";

				echo "<tr bgcolor=#cccccc><td align=right>Alter Agent Interface Options: </td><td align=left><select size=1 name=alter_agent_interface_options><option>0</option><option>1</option><option SELECTED>$alter_agent_interface_options</option></select>$NWB#vicidial_users-alter_agent_interface_options$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Modify Users: </td><td align=left><select size=1 name=modify_users><option>0</option><option>1</option><option SELECTED>$modify_users</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Change Agent Campaign: </td><td align=left><select size=1 name=change_agent_campaign><option>0</option><option>1</option><option SELECTED>$change_agent_campaign</option></select>$NWB#vicidial_users-change_agent_campaign$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Delete Users: </td><td align=left><select size=1 name=delete_users><option>0</option><option>1</option><option SELECTED>$delete_users</option></select>$NWB#vicidial_users-delete_users$NWE</td></tr>\n";

				echo "<tr bgcolor=#bcbcbc><td align=right>Modify User Groups: </td><td align=left><select size=1 name=modify_usergroups><option>0</option><option>1</option><option SELECTED>$modify_usergroups</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#bcbcbc><td align=right>Delete User Groups: </td><td align=left><select size=1 name=delete_user_groups><option>0</option><option>1</option><option SELECTED>$delete_user_groups</option></select>$NWB#vicidial_users-delete_user_groups$NWE</td></tr>\n";

				echo "<tr bgcolor=#cccccc><td align=right>Modify Lists: </td><td align=left><select size=1 name=modify_lists><option>0</option><option>1</option><option SELECTED>$modify_lists</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Delete Lists: </td><td align=left><select size=1 name=delete_lists><option>0</option><option>1</option><option SELECTED>$delete_lists</option></select>$NWB#vicidial_users-delete_lists$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Load Leads: </td><td align=left><select size=1 name=load_leads><option>0</option><option>1</option><option SELECTED>$load_leads</option></select>$NWB#vicidial_users-load_leads$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Modify Leads: </td><td align=left><select size=1 name=modify_leads><option>0</option><option>1</option><option SELECTED>$modify_leads</option></select>$NWB#vicidial_users-modify_leads$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Download Lists: </td><td align=left><select size=1 name=download_lists><option>0</option><option>1</option><option SELECTED>$download_lists</option></select>$NWB#vicidial_users-modify_leads$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Export Reports: </td><td align=left><select size=1 name=export_reports><option>0</option><option>1</option><option SELECTED>$export_reports</option></select>$NWB#vicidial_users-export_reports$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Delete From DNC Lists: </td><td align=left><select size=1 name=delete_from_dnc><option>0</option><option>1</option><option SELECTED>$delete_from_dnc</option></select>$NWB#vicidial_users-delete_from_dnc$NWE</td></tr>\n";

				if ($SScustom_fields_enabled > 0)
					{
					echo "<tr bgcolor=#cccccc><td align=right>Custom Fields Modify: </td><td align=left><select size=1 name=custom_fields_modify><option>0</option><option>1</option><option SELECTED>$custom_fields_modify</option></select>$NWB#vicidial_users-custom_fields_modify$NWE</td></tr>\n";
					}

				echo "<tr bgcolor=#bcbcbc><td align=right>Modify Campaigns: </td><td align=left><select size=1 name=modify_campaigns><option>0</option><option>1</option><option SELECTED>$modify_campaigns</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#bcbcbc><td align=right>Campaign Detail: </td><td align=left><select size=1 name=campaign_detail><option>0</option><option>1</option><option SELECTED>$campaign_detail</option></select>$NWB#vicidial_users-campaign_detail$NWE</td></tr>\n";
				echo "<tr bgcolor=#bcbcbc><td align=right>Delete Campaigns: </td><td align=left><select size=1 name=delete_campaigns><option>0</option><option>1</option><option SELECTED>$delete_campaigns</option></select>$NWB#vicidial_users-delete_campaigns$NWE</td></tr>\n";

				echo "<tr bgcolor=#cccccc><td align=right>Modify In-Groups: </td><td align=left><select size=1 name=modify_ingroups><option>0</option><option>1</option><option SELECTED>$modify_ingroups</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Delete In-Groups: </td><td align=left><select size=1 name=delete_ingroups><option>0</option><option>1</option><option SELECTED>$delete_ingroups</option></select>$NWB#vicidial_users-delete_ingroups$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Modify DIDs: </td><td align=left><select size=1 name=modify_inbound_dids><option>0</option><option>1</option><option SELECTED>$modify_inbound_dids</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Delete DIDs: </td><td align=left><select size=1 name=delete_inbound_dids><option>0</option><option>1</option><option SELECTED>$delete_inbound_dids</option></select>$NWB#vicidial_users-delete_ingroups$NWE</td></tr>\n";

				echo "<tr bgcolor=#bcbcbc><td align=right>Modify Remote Agents: </td><td align=left><select size=1 name=modify_remoteagents><option>0</option><option>1</option><option SELECTED>$modify_remoteagents</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#bcbcbc><td align=right>Delete Remote Agents: </td><td align=left><select size=1 name=delete_remote_agents><option>0</option><option>1</option><option SELECTED>$delete_remote_agents</option></select>$NWB#vicidial_users-delete_remote_agents$NWE</td></tr>\n";

				echo "<tr bgcolor=#cccccc><td align=right>Modify Scripts: </td><td align=left><select size=1 name=modify_scripts><option>0</option><option>1</option><option SELECTED>$modify_scripts</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Delete Scripts: </td><td align=left><select size=1 name=delete_scripts><option>0</option><option>1</option><option SELECTED>$delete_scripts</option></select>$NWB#vicidial_users-delete_scripts$NWE</td></tr>\n";

				if ($SSoutbound_autodial_active > 0)
					{
					echo "<tr bgcolor=#bcbcbc><td align=right>Modify Filters: </td><td align=left><select size=1 name=modify_filters><option>0</option><option>1</option><option SELECTED>$modify_filters</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
					echo "<tr bgcolor=#bcbcbc><td align=right>Delete Filters: </td><td align=left><select size=1 name=delete_filters><option>0</option><option>1</option><option SELECTED>$delete_filters</option></select>$NWB#vicidial_users-delete_filters$NWE</td></tr>\n";
					}
				echo "<tr bgcolor=#cccccc><td align=right>AGC Admin Access: </td><td align=left><select size=1 name=ast_admin_access><option>0</option><option>1</option><option SELECTED>$ast_admin_access</option></select>$NWB#vicidial_users-ast_admin_access$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>AGC Delete Phones: </td><td align=left><select size=1 name=ast_delete_phones><option>0</option><option>1</option><option SELECTED>$ast_delete_phones</option></select>$NWB#vicidial_users-ast_delete_phones$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Modify Call Times: </td><td align=left><select size=1 name=modify_call_times><option>0</option><option>1</option><option SELECTED>$modify_call_times</option></select>$NWB#vicidial_users-modify_call_times$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Delete Call Times: </td><td align=left><select size=1 name=delete_call_times><option>0</option><option>1</option><option SELECTED>$delete_call_times</option></select>$NWB#vicidial_users-delete_call_times$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Modify Servers: </td><td align=left><select size=1 name=modify_servers><option>0</option><option>1</option><option SELECTED>$modify_servers</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>CallCard Access: </td><td align=left><select size=1 name=callcard_admin><option>0</option><option>1</option><option SELECTED>$callcard_admin</option></select>$NWB#vicidial_users-modify_sections$NWE</td></tr>\n";
				echo "<tr bgcolor=#cccccc><td align=right>Agent API Access: </td><td align=left><select size=1 name=vdc_agent_api_access><option>0</option><option>1</option><option SELECTED>$vdc_agent_api_access</option></select>$NWB#vicidial_users-vdc_agent_api_access$NWE</td></tr>\n";

				echo "<tr bgcolor=#bcbcbc><td align=right>Add Timeclock Log Record: </td><td align=left><select size=1 name=add_timeclock_log><option>0</option><option>1</option><option SELECTED>$add_timeclock_log</option></select>$NWB#vicidial_users-add_timeclock_log$NWE</td></tr>\n";
				echo "<tr bgcolor=#bcbcbc><td align=right>Modify Timeclock Log Record: </td><td align=left><select size=1 name=modify_timeclock_log><option>0</option><option>1</option><option SELECTED>$modify_timeclock_log</option></select>$NWB#vicidial_users-modify_timeclock_log$NWE</td></tr>\n";
				echo "<tr bgcolor=#bcbcbc><td align=right>Delete Timeclock Log Record: </td><td align=left><select size=1 name=delete_timeclock_log><option>0</option><option>1</option><option SELECTED>$delete_timeclock_log</option></select>$NWB#vicidial_users-delete_timeclock_log$NWE</td></tr>\n";

				echo "<tr bgcolor=#cccccc><td align=right>Manager Shift Enforcement Override: </td><td align=left><select size=1 name=manager_shift_enforcement_override><option>0</option><option>1</option><option SELECTED>$manager_shift_enforcement_override</option></select>$NWB#vicidial_users-manager_shift_enforcement_override$NWE</td></tr>\n";
				}
			echo "<tr bgcolor=#bcbcbc><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
			echo "</TABLE></center>\n";

			if ($LOGdelete_users > 0)
				{
				echo "<br><br><a href=\"$PHP_SELF?ADD=5&user=$user\">DELETE THIS USER</a>\n";
				}
			echo "<br><br><a href=\"./AST_agent_time_sheet.php?agent=$user\">Click here for user time sheet</a>\n";
			echo "<br><br><a href=\"./user_status.php?user=$user\">Click here for user status</a>\n";
			echo "<br><br><a href=\"./user_stats.php?user=$user\">Click here for user stats</a>\n";
			echo "<br><br><a href=\"./AST_agent_days_detail.php?user=$user&query_date=$REPORTdate&end_date=$REPORTdate&group[]=--ALL--&shift=ALL\">Click here for user multiple day status detail report</a>\n";
			echo "<br><br><a href=\"$PHP_SELF?ADD=8&user=$user\">Click here for user CallBack Holds</a>\n";
			if ($LOGuser_level >= 9)
				{
				echo "<br><br><a href=\"$PHP_SELF?ADD=720000000000000&category=USERS&stage=$user\">Click here to see Admin changes to this record</FONT>\n";
				}
			}
		}
	else
		{
		echo "You do not have permission to view this page\n";
		echo $LOGuser_level;
		exit;
		}
	} ?>
</head>

<body style="background:url(../images/bg1.jpg);" onload="parent.bload('SIPS - Listagem de Users');">
<?php



echo "<TABLE align='center'><TR><TD>\n";
	echo "<br>Listagem de Utilizadores:";
	if (ereg('display_all',$status)) 
		{
		$SQLstatus = ' ';
		echo " &nbsp; <a href=\"$PHP_SELF?ADD=0\">utilizadores activos apenas</a>\n";
		}
	else
		{
		$SQLstatus = "and active='Y' ";
		echo " &nbsp; <a href=\"$PHP_SELF?ADD=0&status=display_all\">todos os utilizadores</a>\n";
		}

	$USERlink='stage=USERIDDOWN';
	$NAMElink='stage=NAMEDOWN';
	$LEVELlink='stage=LEVELDOWN';
	$GROUPlink='stage=GROUPDOWN';
	$SQLorder='order by full_name';
	if (eregi("USERIDUP",$stage)) {$SQLorder='order by user asc';   $USERlink='stage=USERIDDOWN';}
	if (eregi("USERIDDOWN",$stage)) {$SQLorder='order by user desc';   $USERlink='stage=USERIDUP';}
	if (eregi("NAMEUP",$stage)) {$SQLorder='order by full_name asc';   $NAMElink='stage=NAMEDOWN';}
	if (eregi("NAMEDOWN",$stage)) {$SQLorder='order by full_name desc';   $NAMElink='stage=NAMEUP';}
	if (eregi("LEVELUP",$stage)) {$SQLorder='order by user_level asc';   $LEVELlink='stage=LEVELDOWN';}
	if (eregi("LEVELDOWN",$stage)) {$SQLorder='order by user_level desc';   $LEVELlink='stage=LEVELUP';}
	if (eregi("GROUPUP",$stage)) {$SQLorder='order by user_group asc';   $GROUPlink='stage=GROUPDOWN';}
	if (eregi("GROUPDOWN",$stage)) {$SQLorder='order by user_group desc';   $GROUPlink='stage=GROUPUP';}
	if ($LOGuser_level == '') { $LOGuser_level = 9; } // NÂO É SUPOSTO ESTAR AQUI
	$stmt="SELECT user,full_name,user_level,user_group,active from vicidial_users where user_level <= $LOGuser_level $SQLstatus  $SQLorder";
	$rslt=mysql_query($stmt, $link) or die(mysql_error());
	$people_to_print = mysql_num_rows($rslt);

	echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1  class='userlist' >\n";
	echo "<tr >";
	echo "<td><a href=\"$PHP_SELF?ADD=0&status=$status&$USERlink\"><B>ID Utilizador</B></a></td>";
	echo "<td><a href=\"$PHP_SELF?ADD=0&status=$status&$NAMElink\"><B>Nome Completo</B></a></td>";
	echo "<td><a href=\"$PHP_SELF?ADD=0&status=$status&$LEVELlink\"><B>Nível</B></a></td>";
	echo "<td><a href=\"$PHP_SELF?ADD=0&status=$status&$GROUPlink\"><B>Grupo</B></a></td>";
	echo "<td><B>Activo</B></td>";
	echo "<td align=center><B>Links</B></td></tr>\n";

	$o=0;
	while ($people_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		
		echo "<tr><td><a href=\"$PHP_SELF?ADD=3&user=$row[0]\">$row[0]</a></td>";
		echo "<td>$row[1]</td>";
		echo "<td>$row[2]</td>";
		echo "<td>$row[3]</td>";
		echo "<td>$row[4]</td>";
		echo "<td><CENTER><a href=\"$PHP_SELF?ADD=3&user=$row[0]\">Editar</a> | <a href=\"./user_stats.php?user=$row[0]\">Estatísticas</a> | <a href=\"./user_status.php?user=$row[0]\">Status</a> | <a href=\"./AST_agent_time_sheet.php?agent=$row[0]\">Tempos</a></CENTER></td></tr>\n";
		$o++;
		}

	echo "</TABLE></center>\n";
	?>

</body>
</html>