<?php 
# AST_timeonVDADall.php
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# live real-time stats for the VICIDIAL Auto-Dialer all servers
#
# STOP=4000, SLOW=40, GO=4 seconds refresh interval
# 
# CHANGELOG:
# 50406-0920 - Added Paused agents < 1 min
# 51130-1218 - Modified layout and info to show all servers in a vicidial system
# 60421-1043 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60511-1343 - Added leads and drop info at the top of the screen
# 60608-1539 - Fixed CLOSER tallies for active calls
# 60619-1658 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 60626-1453 - Added display of system load to bottom (Angelito Manansala)
# 60901-1123 - Changed display elements at the top of the screen
# 60905-1342 - Fixed non INCALL|QUEUE timer column
# 61002-1642 - Added TRUNK SHORT/FILL stats
# 61101-1318 - Added SIP and IAX Listen and Barge links option
# 61101-1647 - Added Usergroup column and user name option as well as sorting
# 61102-1155 - Made display of columns more modular, added ability to hide server info
# 61215-1131 - Added answered calls and drop percent taken from answered calls
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70123-1151 - Added non_latin options for substr in display variables, thanks Marin Blu
# 70206-1140 - Added call-type statuses to display(A-Auto, M-Manual, I-Inbound/Closer)
# 70619-1339 - Added Status Category tally display
# 71029-1900 - Changed CLOSER-type to not require campaign_id restriction
# 80227-0418 - Added priority to waiting calls display
# 80311-1550 - Added calls_today on all agents and wait time/in-group for inbound calls
# 80422-0033 - Added phonediaplay option, allow for toggle-sorting on sortable fields
# 80422-1001 - Fixed sort by phone login
# 80424-0515 - Added non_latin lookup from system_settings
# 80525-1040 - Added IVR status display and summary for inbound calls
# 80619-2047 - Added DISPO status for post-call-work while paused
# 80704-0543 - Added DEAD status for agents INCALL with no live call
# 80822-1222 - Added option for display of customer phone number
# 81011-0335 - Fixed remote agent display bug
# 81022-1500 - Added inbound call stats display option
# 81029-1023 - Changed drop percent calculation for multi-stat reports
# 81029-1706 - Added pause code display if enabled per campaign
# 81108-2337 - Added inbound-only section
# 90105-1153 - Changed monitor links to use 0 prefix instead of 6
# 90202-0108 - Changed options to pop-out frame, added outbound_autodial_active option
# 90310-0906 - Added admin header
# 90428-0727 - Changed listen and barge to use the API and manager must enter phone
# 90508-0623 - Changed to PHP long tags
# 90518-0930 - Fixed $CALLSdisplay static assignment bug for some links(bug #210)
# 90524-2231 - Changed to use functions.php for seconds to HH:MM:SS conversion
# 90602-0405 - Added list mix display in statuses and order if active
# 90603-1845 - Fixed color coding bug
# 90627-0608 - Some Formatting changes, added in-group name display
# 90701-0657 - Fixed inbound=No calculation issues
# 90808-0212 - Fixed inbound only non-ALL bug, changed times to use agent last_state_change
# 90907-0915 - Added PARK status
# 90914-1154 - Added AgentOnly display column to waiting calls section
# 91102-2013 - Changed in-group color styles for incoming calls waiting
# 91204-1548 - Added ability to change agent in-groups and blended
# 100214-1127 - Added no-dialable-leads alert and in-groups stats option
# 100301-1229 - Added 3-WAY status for consultative transfer agents
# 100303-0930 - Added carrier stats display option
# 100424-0943 - Added realtime_block_user_info option
# 100709-1054 - Added system setting slave server option
# 100802-2347 - Added User Group Allowed Reports option validation and allowed campaigns restrictions
# 100805-0704 - Fixed minor bug in campaigns restrictions
# 100815-0002 - Added optional display of preset dials if presets are enabled in the campaign
# 100912-0839 - Changed several stats to limit to 2 or 3 decimal spaces
# 100914-1326 - Added lookup for user_level 7 users to set to reports only which will remove other admin links
# 101024-0832 - Added Agent time stats option and agents-in-dispo counter
# 101109-1448 - Added Auto Hopper Level display (MikeC)
# 101216-1358 - Added functions to work with new realtime_report.php script
# 110218-1037 - Fixed query that was causing load spikes on systems with millions of log entries
# 110303-2125 - Added agent on-hook phone indication and RING status and color
# 110314-1735 - Fixed another query that was causing load spikes on systems with millions of log entries
#

$version = '2.4-65';
$build = '110314-1735';


require("../dbconnect.php");
require("../functions.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))			{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))	{$server_ip=$_POST["server_ip"];}
if (isset($_GET["RR"]))					{$RR=$_GET["RR"];}
	elseif (isset($_POST["RR"]))		{$RR=$_POST["RR"];}
if (isset($_GET["inbound"]))			{$inbound=$_GET["inbound"];}
	elseif (isset($_POST["inbound"]))	{$inbound=$_POST["inbound"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["groups"]))				{$groups=$_GET["groups"];}
	elseif (isset($_POST["groups"]))	{$groups=$_POST["groups"];}
if (isset($_GET["usergroup"]))			{$usergroup=$_GET["usergroup"];}
	elseif (isset($_POST["usergroup"]))	{$usergroup=$_POST["usergroup"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["adastats"]))			{$adastats=$_GET["adastats"];}
	elseif (isset($_POST["adastats"]))	{$adastats=$_POST["adastats"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["SIPmonitorLINK"]))				{$SIPmonitorLINK=$_GET["SIPmonitorLINK"];}
	elseif (isset($_POST["SIPmonitorLINK"]))	{$SIPmonitorLINK=$_POST["SIPmonitorLINK"];}
if (isset($_GET["IAXmonitorLINK"]))				{$IAXmonitorLINK=$_GET["IAXmonitorLINK"];}
	elseif (isset($_POST["IAXmonitorLINK"]))	{$IAXmonitorLINK=$_POST["IAXmonitorLINK"];}
if (isset($_GET["UGdisplay"]))			{$UGdisplay=$_GET["UGdisplay"];}
	elseif (isset($_POST["UGdisplay"]))	{$UGdisplay=$_POST["UGdisplay"];}
if (isset($_GET["UidORname"]))			{$UidORname=$_GET["UidORname"];}
	elseif (isset($_POST["UidORname"]))	{$UidORname=$_POST["UidORname"];}
if (isset($_GET["orderby"]))			{$orderby=$_GET["orderby"];}
	elseif (isset($_POST["orderby"]))	{$orderby=$_POST["orderby"];}
if (isset($_GET["SERVdisplay"]))			{$SERVdisplay=$_GET["SERVdisplay"];}
	elseif (isset($_POST["SERVdisplay"]))	{$SERVdisplay=$_POST["SERVdisplay"];}
if (isset($_GET["CALLSdisplay"]))			{$CALLSdisplay=$_GET["CALLSdisplay"];}
	elseif (isset($_POST["CALLSdisplay"]))	{$CALLSdisplay=$_POST["CALLSdisplay"];}
if (isset($_GET["PHONEdisplay"]))			{$PHONEdisplay=$_GET["PHONEdisplay"];}
	elseif (isset($_POST["PHONEdisplay"]))	{$PHONEdisplay=$_POST["PHONEdisplay"];}
if (isset($_GET["CUSTPHONEdisplay"]))			{$CUSTPHONEdisplay=$_GET["CUSTPHONEdisplay"];}
	elseif (isset($_POST["CUSTPHONEdisplay"]))	{$CUSTPHONEdisplay=$_POST["CUSTPHONEdisplay"];}
if (isset($_GET["NOLEADSalert"]))			{$NOLEADSalert=$_GET["NOLEADSalert"];}
	elseif (isset($_POST["NOLEADSalert"]))	{$NOLEADSalert=$_POST["NOLEADSalert"];}
if (isset($_GET["DROPINGROUPstats"]))			{$DROPINGROUPstats=$_GET["DROPINGROUPstats"];}
	elseif (isset($_POST["DROPINGROUPstats"]))	{$DROPINGROUPstats=$_POST["DROPINGROUPstats"];}
if (isset($_GET["ALLINGROUPstats"]))			{$ALLINGROUPstats=$_GET["ALLINGROUPstats"];}
	elseif (isset($_POST["ALLINGROUPstats"]))	{$ALLINGROUPstats=$_POST["ALLINGROUPstats"];}
if (isset($_GET["with_inbound"]))			{$with_inbound=$_GET["with_inbound"];}
	elseif (isset($_POST["with_inbound"]))	{$with_inbound=$_POST["with_inbound"];}
if (isset($_GET["monitor_active"]))				{$monitor_active=$_GET["monitor_active"];}
	elseif (isset($_POST["monitor_active"]))	{$monitor_active=$_POST["monitor_active"];}
if (isset($_GET["monitor_phone"]))				{$monitor_phone=$_GET["monitor_phone"];}
	elseif (isset($_POST["monitor_phone"]))		{$monitor_phone=$_POST["monitor_phone"];}
if (isset($_GET["CARRIERstats"]))			{$CARRIERstats=$_GET["CARRIERstats"];}
	elseif (isset($_POST["CARRIERstats"]))	{$CARRIERstats=$_POST["CARRIERstats"];}
if (isset($_GET["PRESETstats"]))			{$PRESETstats=$_GET["PRESETstats"];}
	elseif (isset($_POST["PRESETstats"]))	{$PRESETstats=$_POST["PRESETstats"];}
if (isset($_GET["AGENTtimeSTATS"]))				{$AGENTtimeSTATS=$_GET["AGENTtimeSTATS"];}
	elseif (isset($_POST["AGENTtimeSTATS"]))	{$AGENTtimeSTATS=$_POST["AGENTtimeSTATS"];}
if (isset($_GET["RTajax"]))				{$RTajax=$_GET["RTajax"];}
	elseif (isset($_POST["RTajax"]))	{$RTajax=$_POST["RTajax"];}
if (isset($_GET["RTuser"]))				{$RTuser=$_GET["RTuser"];}
	elseif (isset($_POST["RTuser"]))	{$RTuser=$_POST["RTuser"];}
if (isset($_GET["RTpass"]))				{$RTpass=$_GET["RTpass"];}
	elseif (isset($_POST["RTpass"]))	{$RTpass=$_POST["RTpass"];}


$report_name = 'Real-Time Main Report';
$db_source = 'M';





#############################################
##### START SYSTEM_SETTINGS LOOKUP      #####
$stmt = "SELECT use_non_latin,outbound_autodial_active,slave_db_server,reports_use_slave_db FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$outbound_autodial_active =		$row[1];
	$slave_db_server =				$row[2];
	$reports_use_slave_db =			$row[3];
	}
##### END SETTINGS LOOKUP             #####
###########################################

if ( (strlen($slave_db_server)>5) and (preg_match("/$report_name/",$reports_use_slave_db)) )
	{
	mysql_close($link);
	$use_slave_server=1;
	$db_source = 'S';
	require("dbconnect.php");
	echo "<!-- Using slave server $slave_db_server $db_source -->\n";
	}

if (!isset($DB))			{$DB=0;}
if (!isset($RR))			{$RR=40;}
if (!isset($group))			{$group='ALL-ACTIVE';}
if (!isset($usergroup))		{$usergroup='';}
if (!isset($UGdisplay))		{$UGdisplay=0;}	# 0=no, 1=yes
if (!isset($UidORname))		{$UidORname=1;}	# 0=id, 1=name
if (!isset($orderby))		{$orderby='timeup';}
if (!isset($SERVdisplay))	{$SERVdisplay=0;}	# 0=no, 1=yes
if (!isset($CALLSdisplay))	{$CALLSdisplay=1;}	# 0=no, 1=yes
if (!isset($PHONEdisplay))	{$PHONEdisplay=0;}	# 0=no, 1=yes
if (!isset($CUSTPHONEdisplay))	{$CUSTPHONEdisplay=0;}	# 0=no, 1=yes
if (!isset($PAUSEcodes))	{$PAUSEcodes='N';}  # 0=no, 1=yes
if (!isset($with_inbound))	
	{
	if ($outbound_autodial_active > 0)
		{$with_inbound='Y';}  # N=no, Y=yes, O=only
	else
		{$with_inbound='O';}  # N=no, Y=yes, O=only
	}
$ingroup_detail='';

if ( (strlen($group)>1) and (strlen($groups[0])<1) ) {$groups[0] = $group;  $RR=40;}
else {$group = $groups[0];}

function get_server_load($windows = false) 
	{
	$os = strtolower(PHP_OS);
	if(strpos($os, "win") === false) 
		{
		if(file_exists("/proc/loadavg")) 
			{
			$load = file_get_contents("/proc/loadavg");
			$load = explode(' ', $load);
			return $load[0] . '% | '  . $load[1] . '% | ' . $load[2]. '%';
			}
		elseif(function_exists("shell_exec")) 
			{
			$load = explode(' ', `uptime`);
			return $load[count($load)-3] . '% ' . $load[count($load)-2] . '% ' . $load[count($load)-1] . '%';
			}
		else 
			{
		return false;
			}
		}
	elseif($windows) 
		{
		if(class_exists("COM")) 
			{
			$wmi = new COM("WinMgmts:\\\\.");
			$cpus = $wmi->InstancesOf("Win32_Processor");

			$cpuload = 0;
			$i = 0;
			while ($cpu = $cpus->Next()) 
				{
				$cpuload += $cpu->LoadPercentage;
				$i++;
				}

			$cpuload = round($cpuload / $i, 2);
			return "$cpuload%";
			}
		else 
			{
			return false;
			}
		}
	}

#$load_ave = get_server_load(true);

$NOW_TIME = date("Y-m-d H:i:s");
$NOW_DAY = date("Y-m-d");
$NOW_HOUR = date("H:i:s");
$STARTtime = date("U");
$epochONEminuteAGO = ($STARTtime - 60);
$timeONEminuteAGO = date("Y-m-d H:i:s",$epochONEminuteAGO);
$epochFIVEminutesAGO = ($STARTtime - 300);
$timeFIVEminutesAGO = date("Y-m-d H:i:s",$epochFIVEminutesAGO);
$epochFIFTEENminutesAGO = ($STARTtime - 900);
$timeFIFTEENminutesAGO = date("Y-m-d H:i:s",$epochFIFTEENminutesAGO);
$epochONEhourAGO = ($STARTtime - 3600);
$timeONEhourAGO = date("Y-m-d H:i:s",$epochONEhourAGO);
$epochSIXhoursAGO = ($STARTtime - 21600);
$timeSIXhoursAGO = date("Y-m-d H:i:s",$epochSIXhoursAGO);
$epochTWENTYFOURhoursAGO = ($STARTtime - 86400);
$timeTWENTYFOURhoursAGO = date("Y-m-d H:i:s",$epochTWENTYFOURhoursAGO);

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level='7' and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$reports_only_user=$row[0];

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
#  and (preg_match("/MONITOR|BARGE|HIJACK/",$monitor_active) ) )
if ( (!isset($monitor_phone)) or (strlen($monitor_phone)<1) )
	{
	$stmt="select phone_login from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and active='Y';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$monitor_phone = $row[0];
	}

$stmt="SELECT realtime_block_user_info,user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$realtime_block_user_info = $row[0];
$LOGuser_group =			$row[1];

$stmt="SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$LOGuser_group';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGallowed_campaigns = $row[0];
$LOGallowed_reports =	$row[1];

if ( (!preg_match("/$report_name/",$LOGallowed_reports)) and (!preg_match("/ALL REPORTS/",$LOGallowed_reports)) )
	{
    Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "You are not allowed to view this report: |$PHP_AUTH_USER|$report_name|\n";
    exit;
	}

$LOGallowed_campaignsSQL='';
$whereLOGallowed_campaignsSQL='';
if ( (!preg_match("/ALL-/",$LOGallowed_campaigns)) )
	{
	$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
	$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
	$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
	}
$regexLOGallowed_campaigns = " $LOGallowed_campaigns ";

$allactivecampaigns='';
$stmt="select campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
$LISTgroups[$i]='ALL-ACTIVE';
$i++;
$groups_to_print++;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$LISTgroups[$i] =$row[0];
	$LISTnames[$i] =$row[1];
	$allactivecampaigns .= "'$LISTgroups[$i]',";
	$i++;
	}
$allactivecampaigns .= "''";

$i=0;
$group_string='';
$group_ct = count($groups);
while($i < $group_ct)
	{
	if ( (preg_match("/ $groups[$i] /",$regexLOGallowed_campaigns)) or (preg_match("/ALL-/",$LOGallowed_campaigns)) )
		{
		$group_string .= "$groups[$i]";
		$group_SQL .= "'$groups[$i]',";
		$groupQS .= "&groups[]=$groups[$i]";
		}

	$i++;
	}
$group_SQL = eregi_replace(",$",'',$group_SQL);

### if no campaigns selected, display all
if ( ($group_ct < 1) or (strlen($group_string) < 2) )
	{
	$groups[0] = 'ALL-ACTIVE';
	$group_string = '|ALL-ACTIVE|';
	$group = 'ALL-ACTIVE';
	$groupQS .= "&groups[]=ALL-ACTIVE";
	}

if ( (ereg("--NONE--",$group_string) ) or ($group_ct < 1) )
	{
	$all_active = 0;
	$group_SQL = "''";
	$group_SQLand = "and FALSE";
	$group_SQLwhere = "where FALSE";
	}
elseif ( eregi('ALL-ACTIVE',$group_string) )
	{
	$all_active = 1;
	$group_SQL = $allactivecampaigns;
	$group_SQLand = "and campaign_id IN($allactivecampaigns)";
	$group_SQLwhere = "where campaign_id IN($allactivecampaigns)";
	}
else
	{
	$all_active = 0;
	$group_SQLand = "and campaign_id IN($group_SQL)";
	$group_SQLwhere = "where campaign_id IN($group_SQL)";
	}


$stmt="select user_group from vicidial_user_groups;";
$rslt=mysql_query($stmt, $link);
if (!isset($DB))   {$DB=0;}
if ($DB) {echo "$stmt\n";}
$usergroups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $usergroups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$usergroups[$i] =$row[0];
	$i++;
	}

if (!isset($RR))   {$RR=4;}

$F=''; $FG=''; $B=''; $BG='';

$select_list = "<TABLE WIDTH=700 CELLPADDING=5 BGCOLOR=\"#EFEFEF\"><TR><TD VALIGN=TOP>Select Campaigns: <BR>";
/*$select_list .= "<SELECT SIZE=15 NAME=groups[] >";
$o=0;
while ($groups_to_print > $o)
	{
	if (ereg("\|$LISTgroups[$o]\|",$group_string)) 
		{$select_list .= "<option selected value=\"$LISTgroups[$o]\">$LISTgroups[$o] - $LISTnames[$o]</option>";}
	else
		{$select_list .= "<option value=\"$LISTgroups[$o]\">$LISTgroups[$o] - $LISTnames[$o]</option>";}
	$o++;
	}
$select_list .= "</SELECT>";*/
$select_list .= "<BR>(To select more than 1 campaign, hold down the Ctrl key and click)<font>";
$select_list .= "</TD><TD VALIGN=TOP ALIGN=CENTER>";
$select_list .= "<a href=\"#\" onclick=\"closeDiv(\'campaign_select_list\');\">Close Panel</a><BR><BR>";
$select_list .= "<TABLE CELLPADDING=2 CELLSPACING=2 BORDER=0>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Inbound:  </TD><TD align=left><SELECT SIZE=1 NAME=with_inbound>";
$select_list .= "<option value=\"N\"";
	if ($with_inbound=='N') {$select_list .= " selected";} 
$select_list .= ">Não</option>";
$select_list .= "<option value=\"Y\"";
	if ($with_inbound=='Y') {$select_list .= " selected";} 
$select_list .= ">Sim</option>";
$select_list .= "<option value=\"O\"";
	if ($with_inbound=='O') {$select_list .= " selected";} 
$select_list .= ">Só</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Monitor:  </TD><TD align=left><SELECT SIZE=1 NAME=monitor_active>";
$select_list .= "<option value=\"\"";
	if (strlen($monitor_active) < 2) {$select_list .= " selected";} 
$select_list .= ">NONE</option>";
$select_list .= "<option value=\"MONITOR\"";
	if ($monitor_active=='MONITOR') {$select_list .= " selected";} 
$select_list .= ">MONITOR</option>";
$select_list .= "<option value=\"BARGE\"";
	if ($monitor_active=='BARGE') {$select_list .= " selected";} 
$select_list .= ">BARGE</option>";
#$select_list .= "<option value=\"HIJACK\"";
#	if ($monitor_active=='HIJACK') {$select_list .= " selected";} 
#$select_list .= ">HIJACK</option>";
$select_list .= "</SELECT></TD></TR>";

#$select_list .= "<TR><TD align=right>";
#$select_list .= "Phone:  </TD><TD align=left>";
$select_list .= "<INPUT type=hidden size=10 maxlength=20 NAME=monitor_phone VALUE=\"$monitor_phone\">";
#$select_list .= "</TD></TR>";
$select_list .= "<TR><TD align=center COLSPAN=2> </TD></TR>";

if ($UGdisplay > 0)
	{
	$select_list .= "<TR><TD align=right>";
	$select_list .= "Select User Group:  </TD><TD align=left>";
	$select_list .= "<SELECT SIZE=1 NAME=usergroup>";
	$select_list .= "<option value=\"\">ALL USER GROUPS</option>";
	$o=0;
	while ($usergroups_to_print > $o)
		{
		if ($usergroups[$o] == $usergroup) {$select_list .= "<option selected value=\"$usergroups[$o]\">$usergroups[$o]</option>";}
		else {$select_list .= "<option value=\"$usergroups[$o]\">$usergroups[$o]</option>";}
		$o++;
		}
	$select_list .= "</SELECT></TD></TR>";
	}

$select_list .= "<TR><TD align=right>";
$select_list .= "Dialable Leads Alert:  </TD><TD align=left><SELECT SIZE=1 NAME=NOLEADSalert>";
$select_list .= "<option value=\"\"";
	if (strlen($NOLEADSalert) < 2) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"YES\"";
	if ($NOLEADSalert=='YES') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Show Drop In-Group Row:  </TD><TD align=left><SELECT SIZE=1 NAME=DROPINGROUPstats>";
$select_list .= "<option value=\"0\"";
	if ($DROPINGROUPstats < 1) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
	if ($DROPINGROUPstats=='1') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Show Carrier Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=CARRIERstats>";
$select_list .= "<option value=\"0\"";
	if ($CARRIERstats < 1) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
	if ($CARRIERstats=='1') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

## find if any selected campaigns have presets enabled
$presets_enabled=0;
$stmt="select count(*) from vicidial_campaigns where enable_xfer_presets='ENABLED' $group_SQLand;";
$rslt=mysql_query($stmt, $link);
if ($DB) {$OUToutput .= "$stmt\n";}
$presets_enabled_count = mysql_num_rows($rslt);
if ($presets_enabled_count > 0)
	{
	$row=mysql_fetch_row($rslt);
	$presets_enabled = $row[0];
	}
if ($presets_enabled > 0)
	{
	$select_list .= "<TR><TD align=right>";
	$select_list .= "Show Presets Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=PRESETstats>";
	$select_list .= "<option value=\"0\"";
		if ($PRESETstats < 1) {$select_list .= " selected";} 
	$select_list .= ">NO</option>";
	$select_list .= "<option value=\"1\"";
		if ($PRESETstats=='1') {$select_list .= " selected";} 
	$select_list .= ">YES</option>";
	$select_list .= "</SELECT></TD></TR>";
	}

$select_list .= "<TR><TD align=right>";
$select_list .= "Agent Time Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=AGENTtimeSTATS>";
$select_list .= "<option value=\"0\"";
	if ($AGENTtimeSTATS < 1) {$select_list .= " selected";} 
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
	if ($AGENTtimeSTATS=='1') {$select_list .= " selected";} 
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "</TABLE><BR>";
$select_list .= "<INPUT type=submit NAME=SUBMIT VALUE=SUBMIT><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> ";
$select_list .= "</TD></TR>";
$select_list .= "<TR><TD ALIGN=CENTER>";
$select_list .= " ";
$select_list .= "</TD>";
$select_list .= "<TD NOWRAP align=right>";
$select_list .= "VERSION: $version BUILD: $build";
$select_list .= "</TD></TR></TABLE>";

$open_list = "<TABLE WIDTH=250 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#EFEFEF\"><TR><TD CLASS='rdo' ALIGN=CENTER><a href=\"#\" onclick=\"openDiv(\'campaign_select_list\');\">Choose Report Display Options</a></TD></TR></TABLE>";

?>

<?php 

if ($RTajax > 0)
	{
	echo "<!-- ajax-mode -->\n";
	}
else
	{
	?>
	<script language="Javascript">

	window.onload = startup;

	// function to detect the XY position on the page of the mouse
	function startup() 
		{
		hide_ingroup_info();
		if (window.Event) 
			{
			document.captureEvents(Event.MOUSEMOVE);
			}
		document.onmousemove = getCursorXY;
		}

	function getCursorXY(e) 
		{
		document.getElementById('cursorX').value = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
		document.getElementById('cursorY').value = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
		}

	var select_list = '<?php echo $select_list ?>';
	var open_list = '<?php echo $open_list ?>';
	var monitor_phone = '<?php echo $monitor_phone ?>';
	var user = '<?php echo $PHP_AUTH_USER ?>';
	var pass = '<?php echo $PHP_AUTH_PW ?>';

	// functions to hide and show different DIVs
	function openDiv(divvar) 
		{
		document.getElementById(divvar).innerHTML = select_list;
		document.getElementById(divvar).style.left = '43%';
		}
	function closeDiv(divvar)
		{
		document.getElementById(divvar).innerHTML = open_list;
		document.getElementById(divvar).style.left = '50%';
		}
	function closeAlert(divvar)
		{
		document.getElementById(divvar).innerHTML = '';
		}
	// function to launch monitoring calls

	function send_monitor(session_id,server_ip,stage)
		{
			
		//	alert(session_id + "|" + server_ip + "|" + monitor_phone + "|" + stage + "|" + user);
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		
		
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{
			var monitorQuery = "source=realtime&function=blind_monitor&user=" + user + "&pass=" + pass + "&phone_login=" + monitor_phone + "&session_id=" + session_id + '&server_ip=' + server_ip + '&stage=' + stage;
			xmlhttp.open('POST', '../non_agent_api.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(monitorQuery); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					var Xoutput = null;
					Xoutput = xmlhttp.responseText;
					var regXFerr = new RegExp("ERROR","g");
					var regXFscs = new RegExp("SUCCESS","g");
					if (Xoutput.match(regXFerr))
						{}
					if (Xoutput.match(regXFscs))
						{alert("SUCCESS: calling " + monitor_phone);}
					}
				}
			delete xmlhttp;
			}
		}

	// function to change in-groups selected for a specific agent
	function submit_ingroup_changes(temp_agent_user)
		{
		var temp_ingroup_add_remove_changeIndex = document.getElementById("ingroup_add_remove_change").selectedIndex;
		var temp_ingroup_add_remove_change =  document.getElementById('ingroup_add_remove_change').options[temp_ingroup_add_remove_changeIndex].value;

		var temp_set_as_defaultIndex = document.getElementById("set_as_default").selectedIndex;
		var temp_set_as_default =  document.getElementById('set_as_default').options[temp_set_as_defaultIndex].value;

		var temp_blendedIndex = document.getElementById("blended").selectedIndex;
		var temp_blended =  document.getElementById('blended').options[temp_blendedIndex].value;

		var temp_ingroup_choices = '';
		var txtSelectedValuesObj = document.getElementById('txtSelectedValues');
		var selectedArray = new Array();
		var selObj = document.getElementById('ingroup_new_selections');
		var i;
		var count = 0;
		for (i=0; i<selObj.options.length; i++) 
			{
			if (selObj.options[i].selected) 
				{
			//	selectedArray[count] = selObj.options[i].value;
				temp_ingroup_choices = temp_ingroup_choices + '+' + selObj.options[i].value;
				count++;
				}
			}

		temp_ingroup_choices = temp_ingroup_choices + '+-';

		//	alert(session_id + "|" + server_ip + "|" + monitor_phone + "|" + stage + "|" + user);
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{
			var changeQuery = "source=realtime&function=change_ingroups&user=" + user + "&pass=" + pass + "&agent_user=" + temp_agent_user + "&value=" + temp_ingroup_add_remove_change + '&set_as_default=' + temp_set_as_default + '&blended=' + temp_blended + '&ingroup_choices=' + temp_ingroup_choices;
			xmlhttp.open('POST', '../agc/api.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(changeQuery); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(changeQuery);
					var Xoutput = null;
					Xoutput = xmlhttp.responseText;
					var regXFerr = new RegExp("ERROR","g");
					if (Xoutput.match(regXFerr))
						{alert(xmlhttp.responseText);}
					else
						{
						alert(xmlhttp.responseText);
						hide_ingroup_info();
						}
					}
				}
			delete xmlhttp;
			}
		}

	// function to display in-groups selected for a specific agent
	function ingroup_info(agent_user,count)
		{
		var cursorheight = (document.REALTIMEform.cursorY.value - 0);
		var newheight = (cursorheight + 10);
		document.getElementById("agent_ingroup_display").style.top = newheight;
		//	alert(session_id + "|" + server_ip + "|" + monitor_phone + "|" + stage + "|" + user);
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{
			var monitorQuery = "source=realtime&function=agent_ingroup_info&stage=change&user=" + user + "&pass=" + pass + "&agent_user=" + agent_user;
			xmlhttp.open('POST', '../non_agent_api.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(monitorQuery); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var Xoutput = null;
					Xoutput = xmlhttp.responseText;
					var regXFerr = new RegExp("ERROR","g");
					if (Xoutput.match(regXFerr))
						{alert(xmlhttp.responseText);}
					else
						{
						document.getElementById("agent_ingroup_display").visibility = "visible";
						document.getElementById("agent_ingroup_display").innerHTML = Xoutput;
						}
					}
				}
			delete xmlhttp;
			}
		}

	// function to display in-groups selected for a specific agent
	function hide_ingroup_info()
		{
		document.getElementById("agent_ingroup_display").visibility = "hidden";
		document.getElementById("agent_ingroup_display").innerHTML = '';
		}
	</script>
        
<?php
	echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
	echo"<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?RR=$RR&DB=$DB$groupQS&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS\">\n";
	echo "<TITLE>$report_name: $group</TITLE></HEAD><BODY BGCOLOR=WHITE marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

		$short_header=1;

		require("admin_header.php");

	}

$stmt = "select count(*) from vicidial_campaigns where active='Y' and campaign_allow_inbound='Y' $group_SQLand;";
$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$campaign_allow_inbound = $row[0];


if ($RTajax < 1)
	{
	echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";

	echo "<FORM ACTION='$PHP_SELF' METHOD=GET NAME=REALTIMEform ID=REALTIMEform>\n";
	echo "<INPUT TYPE=HIDDEN NAME=RR VALUE=\"$RR\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=cursorX ID=cursorX>\n";
	echo "<INPUT TYPE=HIDDEN NAME=cursorY ID=cursorY>\n";
	echo "<INPUT TYPE=HIDDEN NAME=adastats VALUE=\"$adastats\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=SIPmonitorLINK VALUE=\"$SIPmonitorLINK\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=IAXmonitorLINK VALUE=\"$IAXmonitorLINK\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=usergroup VALUE=\"$usergroup\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=UGdisplay VALUE=\"$UGdisplay\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=UidORname VALUE=\"$UidORname\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=orderby VALUE=\"$orderby\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=SERVdisplay VALUE=\"$SERVdisplay\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=CALLSdisplay VALUE=\"$CALLSdisplay\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=PHONEdisplay VALUE=\"$PHONEdisplay\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=CUSTPHONEdisplay VALUE=\"$CUSTPHONEdisplay\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=DROPINGROUPstats VALUE=\"$DROPINGROUPstats\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=ALLINGROUPstats VALUE=\"$ALLINGROUPstats\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=CARRIERstats VALUE=\"$CARRIERstats\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=PRESETstats VALUE=\"$PRESETstats\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=AGENTtimeSTATS VALUE=\"$AGENTtimeSTATS\">\n";
	echo "Real-Time Report \n";
        echo "<span id=campaign_select_list>\n";
	echo "<TABLE WIDTH=250 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#EFEFEF\"><TR><TD ALIGN=CENTER>\n";
	echo "<a href='#' onclick=\"openDiv('campaign_select_list');\">Choose Report Display Options</a>";
	echo "</TD></TR></TABLE>\n";
	echo "</span>\n";
        echo "<span style=\"position:absolute;left:10px;top:120px;z-index:18;\" id=agent_ingroup_display>\n";
	echo " ";
	echo "</span>\n";
	echo "<a href='../sips-admin/$PHP_SELF?RR=4000$groupQS&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>STOP</a> | ";
	echo "<a href=../sips-admin/$PHP_SELF?RR=40$groupQS&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS>SLOW</a> | ";
	echo "<a href=../sips-admin/$PHP_SELF?RR=4$groupQS&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS>GO</a>";
	if (eregi('ALL-ACTIVE',$group_string))
		{
		echo " <a href='../sips-admin/admin.php?ADD=10'>MODIFY</a>\n";
		}
	else
		{
		echo " <a href='../sips-admin/admin.php?ADD=34&campaign_id=$group'>MODIFY</a> \n";
		}
	echo "<a href='../sips-admin/AST_timeonVDADallSUMMARY.php?RR=$RR&DB=$DB&adastats=$adastats'>SUMMARY</a> </FONT>\n";
	echo "\n\n";
	}

if (!$group) 
	{echo "<BR><BR>please select a campaign from the pulldown above</FORM>\n"; exit;}
else
{
$multi_drop=0;
### Gather list of all Closer group ids for exclusion from stats
$stmt = "select group_id from vicidial_inbound_groups;";
$rslt=mysql_query($stmt, $link);
$ingroups_to_print = mysql_num_rows($rslt);
while ($ingroups_to_print > $c)
	{
	$row=mysql_fetch_row($rslt);
	$ALLcloser_campaignsSQL .= "'$row[0]',";
	$c++;
	}
$ALLcloser_campaignsSQL = preg_replace("/,$/","",$ALLcloser_campaignsSQL);
if (strlen($ALLcloser_campaignsSQL)<2)
	{$ALLcloser_campaignsSQL="''";}
if ($DB > 0) {echo "\n|$ALLcloser_campaignsSQL|$stmt|\n";}


##### INBOUND #####
if ( ( ereg('Y',$with_inbound) or ereg('O',$with_inbound) ) and ($campaign_allow_inbound > 0) )
	{
	### Gather list of Closer group ids
	$stmt = "select closer_campaigns from vicidial_campaigns where active='Y' $group_SQLand;";
	$rslt=mysql_query($stmt, $link);
	$ccamps_to_print = mysql_num_rows($rslt);
	$c=0;
	while ($ccamps_to_print > $c)
		{
		$row=mysql_fetch_row($rslt);
		$closer_campaigns = $row[0];
		$closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
		$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
		$closer_campaignsSQL .= "'$closer_campaigns',";
		$c++;
		}
	$closer_campaignsSQL = preg_replace("/,$/","",$closer_campaignsSQL);
	}
if (strlen($closer_campaignsSQL)<2)
	{$closer_campaignsSQL="''";}

if ($DB > 0) {echo "\n|$closer_campaigns|$closer_campaignsSQL|$stmt|\n";}


##### SHOW IN-GROUP STATS OR INBOUND ONLY WITH VIEW-MORE ###
if ($campaign_allow_inbound == "1"  ) #($ALLINGROUPstats > 0) or ( (ereg('O',$with_inbound)) and ($adastats > 1) )
	{
	$stmtB="select calls_today,drops_today,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4,hold_sec_stat_one,hold_sec_stat_two,hold_sec_answer_calls,hold_sec_drop_calls,hold_sec_queue_calls,campaign_id from vicidial_campaign_stats where campaign_id IN ($closer_campaignsSQL) order by campaign_id;";

	if ($DB > 0) {echo "\n|$stmtB|\n";}
# working
	$r=0;
	$rslt=mysql_query($stmtB, $link);
	$ingroups_to_print = mysql_num_rows($rslt);
/*	if ($ingroups_to_print > 0)
		{$ingroup_detail .= "<table cellpadding=0 cellspacing=0>";} */
	while ($ingroups_to_print > $r)
		{
		$row=mysql_fetch_row($rslt);
		$callsTODAY =				$row[0];
		$dropsTODAY =				$row[1];
		$answersTODAY =				$row[2];
		$VSCcat1 =					$row[3];
		$VSCcat1tally =				$row[4];
		$VSCcat2 =					$row[5];
		$VSCcat2tally =				$row[6];
		$VSCcat3 =					$row[7];
		$VSCcat3tally =				$row[8];
		$VSCcat4 =					$row[9];
		$VSCcat4tally =				$row[10];
		$hold_sec_stat_one =		$row[11];
		$hold_sec_stat_two =		$row[12];
		$hold_sec_answer_calls =	$row[13];
		$hold_sec_drop_calls =		$row[14];
		$hold_sec_queue_calls =		$row[15];
		$ingroupdetail =			$row[16];
		if ( ($dropsTODAY > 0) and ($answersTODAY > 0) )
			{
			$drpctTODAY = ( ($dropsTODAY / $callsTODAY) * 100);
			$drpctTODAY = round($drpctTODAY, 2);
			$drpctTODAY = sprintf("%01.2f", $drpctTODAY);
			}
		else
			{$drpctTODAY=0;}

		if ($callsTODAY > 0)
			{
			$AVGhold_sec_queue_calls = ($hold_sec_queue_calls / $callsTODAY);
			$AVGhold_sec_queue_calls = round($AVGhold_sec_queue_calls, 0);
			}
		else
			{$AVGhold_sec_queue_calls=0;}

		if ($dropsTODAY > 0)
			{
			$AVGhold_sec_drop_calls = ($hold_sec_drop_calls / $dropsTODAY);
			$AVGhold_sec_drop_calls = round($AVGhold_sec_drop_calls, 0);
			}
		else
			{$AVGhold_sec_drop_calls=0;}

		if ($answersTODAY > 0)
			{
			$PCThold_sec_stat_one = ( ($hold_sec_stat_one / $answersTODAY) * 100);
			$PCThold_sec_stat_one = round($PCThold_sec_stat_one, 2);
			$PCThold_sec_stat_one = sprintf("%01.2f", $PCThold_sec_stat_one);
			$PCThold_sec_stat_two = ( ($hold_sec_stat_two / $answersTODAY) * 100);
			$PCThold_sec_stat_two = round($PCThold_sec_stat_two, 2);
			$PCThold_sec_stat_two = sprintf("%01.2f", $PCThold_sec_stat_two);
			$AVGhold_sec_answer_calls = ($hold_sec_answer_calls / $answersTODAY);
			$AVGhold_sec_answer_calls = round($AVGhold_sec_answer_calls, 0);
			if ($agent_non_pause_sec > 0)
				{
				$AVG_ANSWERagent_non_pause_sec = (($answersTODAY / $agent_non_pause_sec) * 60);
				$AVG_ANSWERagent_non_pause_sec = round($AVG_ANSWERagent_non_pause_sec, 2);
				$AVG_ANSWERagent_non_pause_sec = sprintf("%01.2f", $AVG_ANSWERagent_non_pause_sec);
				}
			else
				{$AVG_ANSWERagent_non_pause_sec=0;}
			}
		else
			{
			$PCThold_sec_stat_one=0;
			$PCThold_sec_stat_two=0;
			$AVGhold_sec_answer_calls=0;
			$AVG_ANSWERagent_non_pause_sec=0;
			}

		$ingroup_detail .= "<div class='cc-mstyle' style='margin-top:8px;'><table>";
		$ingroup_detail .= "<tr>";
		$ingroup_detail .= "<td>Nº de Chamadas Efectuadas</td><td> $callsTODAY </td>";
		$ingroup_detail .= "<td>TMA 1:</td><td> $PCThold_sec_stat_one% </td>";
		$ingroup_detail .= "<td>Tempo Médio em Espera para Chamadas Atendidas</td><td> $AVGhold_sec_answer_calls </td>";
		$ingroup_detail .= "</tr>";
		
                $ingroup_detail .= "<tr>";
		$ingroup_detail .= "<td>Nº de Chamadas Perdidas:</td><td> $dropsTODAY </td>";
		$ingroup_detail .= "<td>TMA 2:</td><td> $PCThold_sec_stat_two% </td>";
		$ingroup_detail .= "<td>Tempo Médio em Espera para Chamadas Perdidas</td><td> $AVGhold_sec_drop_calls </td>";
		$ingroup_detail .= "</tr>";
		
		$ingroup_detail .= "<tr>";
		$ingroup_detail .= "<td>Nº de Chamadas Atendidas:</td><td> $answersTODAY </td>";
		$ingroup_detail .= "<td>Percentagem de Chamadas Perdidas</td><td> $drpctTODAY%</td>";
		$ingroup_detail .= "<td>Tempo Médio em Espera para todas as Chamadas</td><td> $AVGhold_sec_queue_calls </td>";
		$ingroup_detail .= "</tr>";
                
                // só para escavacar
                $ingroup_detail = "";    
		$r++;
		}

	} 


##### DROP IN-GROUP ONLY TOTALS ROW ###
$DROPINGROUPstatsHTML='';
if ( ($DROPINGROUPstats > 0) and (!preg_match("/ALL-ACTIVE/",$group_string)) )
	{
	$DIGcampaigns='';
	$stmtB="select drop_inbound_group from vicidial_campaigns where campaign_id IN($group_SQL) and drop_inbound_group NOT IN('---NONE---','');";
	if ($DB > 0) {echo "\n|$stmtB|\n";}
	$rslt=mysql_query($stmtB, $link);
	$dig_to_print = mysql_num_rows($rslt);
	$dtp=0;
	while ($dig_to_print > $dtp)
		{
		$row=mysql_fetch_row($rslt);
		$DIGcampaigns .=		"'$row[0]',";
		$dtp++;
		}
	$DIGcampaigns = preg_replace("/,$/",'',$DIGcampaigns);
	if (strlen($DIGcampaigns) < 2) {$DIGcampaigns = "''";}

	$stmtB="select sum(calls_today),sum(drops_today),sum(answers_today) from vicidial_campaign_stats where campaign_id IN($DIGcampaigns);";
	if ($DB > 0) {echo "\n|$stmtB|\n";}

	$rslt=mysql_query($stmtB, $link);
	$row=mysql_fetch_row($rslt);
	$callsTODAY =				$row[0];
	$dropsTODAY =				$row[1];
	$answersTODAY =				$row[2];
	if ( ($dropsTODAY > 0) and ($callsTODAY > 0) )
		{
		$drpctTODAY = ( ($dropsTODAY / $callsTODAY) * 100);
		$drpctTODAY = round($drpctTODAY, 2);
		$drpctTODAY = sprintf("%01.2f", $drpctTODAY);
		}
	else
		{$drpctTODAY=0;}

	$DROPINGROUPstatsHTML .= "<TR BGCOLOR=\"#E6E6E6\">";
	$DROPINGROUPstatsHTML .= "<TD ALIGN=RIGHT COLSPAN=2><B>DROP IN-GROUP STATS -</B></TD>";
	$DROPINGROUPstatsHTML .= "<TD ALIGN=RIGHT><B>DROP PERCENT:</B></TD><TD ALIGN=LEFT>&nbsp; $drpctTODAY% </TD>";
	$DROPINGROUPstatsHTML .= "<TD ALIGN=RIGHT><B>CALLS:</B></TD><TD ALIGN=LEFT>&nbsp; $callsTODAY </TD>";
	$DROPINGROUPstatsHTML .= "<TD ALIGN=RIGHT><B>DROPS/ANSWERS:</B></TD><TD ALIGN=LEFT>&nbsp; $dropsTODAY / $answersTODAY </TD>";
	$DROPINGROUPstatsHTML .= "</TR>";
	}


##### CARRIER STATS TOTALS ###
$CARRIERstatsHTML='';
if ($CARRIERstats > 0)
	{
	$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeTWENTYFOURhoursAGO\" group by dialstatus;";
	if ($DB > 0) {echo "\n|$stmtB|\n";}
	$rslt=mysql_query($stmtB, $link);
	$car_to_print = mysql_num_rows($rslt);
	$ctp=0;
	while ($car_to_print > $ctp)
		{
		$row=mysql_fetch_row($rslt);
		$TFhour_status[$ctp] =	$row[0];
		$TFhour_count[$ctp] =	$row[1];
		$dialstatuses .=		"'$row[0]',";
		$ctp++;
		}
	$dialstatuses = preg_replace("/,$/",'',$dialstatuses);

	$CARRIERstatsHTML .= "<div style='margin-top:8px;' class=''cc-mstyle''>";
	$CARRIERstatsHTML .= "<table >";
	$CARRIERstatsHTML .= "<TR>";
	$CARRIERstatsHTML .= "<TD>Motivo</TD>";
	$CARRIERstatsHTML .= "<TD>24 Horas </TD>";
	$CARRIERstatsHTML .= "<TD> 6 Horas </TD>";
	$CARRIERstatsHTML .= "<TD> 1 Hora </TD>";
	$CARRIERstatsHTML .= "<TD> 15 Minutos </TD>";
	$CARRIERstatsHTML .= "<TD> 5 Minutos </TD>";
	$CARRIERstatsHTML .= "<TD> 1 minuto </TD>";
	$CARRIERstatsHTML .= "</TR>";

	if (strlen($dialstatuses) > 1)
		{
		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeSIXhoursAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$SIXhour_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}

		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeONEhourAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$ONEhour_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}

		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeFIFTEENminutesAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$FTminute_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}

		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeFIVEminutesAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$FIVEminute_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}

		$stmtB="select dialstatus,count(*) from vicidial_carrier_log where call_date >= \"$timeONEminuteAGO\" group by dialstatus;";
		if ($DB > 0) {echo "\n|$stmtB|\n";}
		$rslt=mysql_query($stmtB, $link);
		$scar_to_print = mysql_num_rows($rslt);
		$print_sctp=0;
		while ($scar_to_print > $print_sctp)
			{
			$row=mysql_fetch_row($rslt);
			$print_ctp=0;
			while ($print_ctp < $ctp)
				{
				if ($TFhour_status[$print_ctp] == $row[0])
					{$ONEminute_count[$print_ctp] = $row[1];}
				$print_ctp++;
				}
			$print_sctp++;
			}


		$print_ctp=0;
		while ($print_ctp < $ctp)
			{
			if (strlen($TFhour_count[$print_ctp])<1) {$TFhour_count[$print_ctp]=0;}
			if (strlen($SIXhour_count[$print_ctp])<1) {$SIXhour_count[$print_ctp]=0;}
			if (strlen($ONEhour_count[$print_ctp])<1) {$ONEhour_count[$print_ctp]=0;}
			if (strlen($FTminute_count[$print_ctp])<1) {$FTminute_count[$print_ctp]=0;}
			if (strlen($FIVEminute_count[$print_ctp])<1) {$FIVEminute_count[$print_ctp]=0;}
			if (strlen($ONEminute_count[$print_ctp])<1) {$ONEminute_count[$print_ctp]=0;}




			$CARRIERstatsHTML .= "<TR>";
			#$CARRIERstatsHTML .= "<TD BGCOLOR=white>&nbsp;</TD>";
			
			switch ($TFhour_status[$print_ctp]) {
				
			case "ANSWER" : $TFhour_status[$print_ctp] = "Respostas"; break;	
			case "BUSY" : 	$TFhour_status[$print_ctp] = "Ocupado";	break;
			case "CANCEL" : 	$TFhour_status[$print_ctp] = "Canceladas"; break;	
			case "CHANUNAVAIL" : 	$TFhour_status[$print_ctp] = "Sem Canal Disponível";	break;
			case "CONGESTION" : 	$TFhour_status[$print_ctp] = "Marcações com Erro"; break;
			case "NOANSWER" : 	$TFhour_status[$print_ctp] = "Sem Resposta"; break;
				
			}
			
			$CARRIERstatsHTML .= "<TD>$TFhour_status[$print_ctp]</TD>";
			$CARRIERstatsHTML .= "<TD> $TFhour_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD> $SIXhour_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD> $ONEhour_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD> $FTminute_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD> $FIVEminute_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "<TD> $ONEminute_count[$print_ctp] </TD>";
			$CARRIERstatsHTML .= "</TR>";
			$print_ctp++;
			}
		}
	else
		{
		$CARRIERstatsHTML .= "<TR><TD BGCOLOR=white colspan=7>no log entries</TD></TR>";
		}
	$CARRIERstatsHTML .= "</TABLE>";
	$CARRIERstatsHTML .= "</TD></TR>";
	}


##### PRESET STATS TOTALS ###
$PRESETstatsHTML='';
if ($PRESETstats > 0)
	{
	$PRESETstatsHTML .= "<TR BGCOLOR=white><TD ALIGN=left COLSPAN=8>";
	$PRESETstatsHTML .= "<TABLE CELLPADDING=1 CELLSPACING=1 BORDER=0 BGCOLOR=white>";
	$PRESETstatsHTML .= "<TR BGCOLOR=\"#E6E6E6\">";
	$PRESETstatsHTML .= "<TD ALIGN=LEFT><B> AGENT DIAL PRESETS: </B></TD>";
	$PRESETstatsHTML .= "<TD ALIGN=LEFT><B> PRESET NAMES </B></TD>";
	$PRESETstatsHTML .= "<TD ALIGN=LEFT><B> CALLS </B></TD>";
	$PRESETstatsHTML .= "</TR>";
	$stmtB="select preset_name,xfer_count from vicidial_xfer_stats where preset_name!='' and preset_name is not NULL  $group_SQLand order by preset_name;";
	if ($DB > 0) {echo "\n|$stmtB|\n";}
	$rslt=mysql_query($stmtB, $link);
	$pre_to_print = mysql_num_rows($rslt);
	$ctp=0;
	while ($pre_to_print > $ctp)
		{
		$row=mysql_fetch_row($rslt);
		$PRESETstatsHTML .= "<TR>";
		$PRESETstatsHTML .= "<TD> </TD>";
		$PRESETstatsHTML .= "<TD ALIGN=LEFT BGCOLOR=\"#E6E6E6\"><B> $row[0] </B></TD>";
		$PRESETstatsHTML .= "<TD ALIGN=RIGHT BGCOLOR=\"#E6E6E6\"> $row[1] </TD>";
		$PRESETstatsHTML .= "</TR>";
		$ctp++;
		}
	if ($ctp < 1)
		{
		$PRESETstatsHTML .= "<TR><TD BGCOLOR=white colspan=2>no log entries</TD></TR>";
		}
	$PRESETstatsHTML .= "</TABLE>";
	$PRESETstatsHTML .= "</TD></TR>";
	}


#	http://server/sips-admin/AST_timeonVDADall.php?&groups[]=ALL-ACTIVE&RR=4000&DB=0&adastats=&SIPmonitorLINK=&IAXmonitorLINK=&usergroup=&UGdisplay=1&UidORname=1&orderby=timeup&SERVdisplay=0&CALLSdisplay=1&PHONEdisplay=0&CUSTPHONEdisplay=0&with_inbound=Y&monitor_active=&monitor_phone=350a&ALLINGROUPstats=1&DROPINGROUPstats=0&NOLEADSalert=&CARRIERstats=1

##### INBOUND ONLY ###
if (ereg('O',$with_inbound))
	{
	$multi_drop++;

	$stmt="select agent_pause_codes_active from vicidial_campaigns $group_SQLwhere;";

	$stmtB="select sum(calls_today),sum(drops_today),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4),sum(hold_sec_stat_one),sum(hold_sec_stat_two),sum(hold_sec_answer_calls),sum(hold_sec_drop_calls),sum(hold_sec_queue_calls) from vicidial_campaign_stats where campaign_id IN ($closer_campaignsSQL);";

	if (eregi('ALL-ACTIVE',$group_string))
		{
		$inboundSQL = "where campaign_id IN ($closer_campaignsSQL)";
		$stmtB="select sum(calls_today),sum(drops_today),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4),sum(hold_sec_stat_one),sum(hold_sec_stat_two),sum(hold_sec_answer_calls),sum(hold_sec_drop_calls),sum(hold_sec_queue_calls) from vicidial_campaign_stats $inboundSQL;";
		}

	$stmtC="select agent_non_pause_sec from vicidial_campaign_stats $group_SQLwhere;";


	if ($DB > 0) {echo "\n|$stmt|$stmtB|$stmtC|\n";}

	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$agent_pause_codes_active = $row[0];

	$rslt=mysql_query($stmtC, $link);
	$row=mysql_fetch_row($rslt);
	$agent_non_pause_sec = $row[0];

	$rslt=mysql_query($stmtB, $link);
	$row=mysql_fetch_row($rslt);
	$callsTODAY =				$row[0];
	$dropsTODAY =				$row[1];
	$answersTODAY =				$row[2];
	$VSCcat1 =					$row[3];
	$VSCcat1tally =				$row[4];
	$VSCcat2 =					$row[5];
	$VSCcat2tally =				$row[6];
	$VSCcat3 =					$row[7];
	$VSCcat3tally =				$row[8];
	$VSCcat4 =					$row[9];
	$VSCcat4tally =				$row[10];
	$hold_sec_stat_one =		$row[11];
	$hold_sec_stat_two =		$row[12];
	$hold_sec_answer_calls =	$row[13];
	$hold_sec_drop_calls =		$row[14];
	$hold_sec_queue_calls =		$row[15];
	if ( ($dropsTODAY > 0) and ($answersTODAY > 0) )
		{
		$drpctTODAY = ( ($dropsTODAY / $callsTODAY) * 100);
		$drpctTODAY = round($drpctTODAY, 2);
		$drpctTODAY = sprintf("%01.2f", $drpctTODAY);
		}
	else
		{$drpctTODAY=0;}

	if ($callsTODAY > 0)
		{
		$AVGhold_sec_queue_calls = ($hold_sec_queue_calls / $callsTODAY);
		$AVGhold_sec_queue_calls = round($AVGhold_sec_queue_calls, 0);
		}
	else
		{$AVGhold_sec_queue_calls=0;}

	if ($dropsTODAY > 0)
		{
		$AVGhold_sec_drop_calls = ($hold_sec_drop_calls / $dropsTODAY);
		$AVGhold_sec_drop_calls = round($AVGhold_sec_drop_calls, 0);
		}
	else
		{$AVGhold_sec_drop_calls=0;}

	if ($answersTODAY > 0)
		{
		$PCThold_sec_stat_one = ( ($hold_sec_stat_one / $answersTODAY) * 100);
		$PCThold_sec_stat_one = round($PCThold_sec_stat_one, 2);
		$PCThold_sec_stat_one = sprintf("%01.2f", $PCThold_sec_stat_one);
		$PCThold_sec_stat_two = ( ($hold_sec_stat_two / $answersTODAY) * 100);
		$PCThold_sec_stat_two = round($PCThold_sec_stat_two, 2);
		$PCThold_sec_stat_two = sprintf("%01.2f", $PCThold_sec_stat_two);
		$AVGhold_sec_answer_calls = ($hold_sec_answer_calls / $answersTODAY);
		$AVGhold_sec_answer_calls = round($AVGhold_sec_answer_calls, 0);
		if ($agent_non_pause_sec > 0)
			{
			$AVG_ANSWERagent_non_pause_sec = (($answersTODAY / $agent_non_pause_sec) * 60);
			$AVG_ANSWERagent_non_pause_sec = round($AVG_ANSWERagent_non_pause_sec, 2);
			$AVG_ANSWERagent_non_pause_sec = sprintf("%01.2f", $AVG_ANSWERagent_non_pause_sec);
			}
		else
			{$AVG_ANSWERagent_non_pause_sec=0;}
		}
	else
		{
		$PCThold_sec_stat_one=0;
		$PCThold_sec_stat_two=0;
		$AVGhold_sec_answer_calls=0;
		$AVG_ANSWERagent_non_pause_sec=0;
		}

	echo "<BR><table cellpadding=0 cellspacing=0><TR>";
	echo "<TD ALIGN=RIGHT><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT>&nbsp; $callsTODAY&nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><B>TMA 1:</B></TD><TD ALIGN=LEFT>&nbsp; $PCThold_sec_stat_one% </TD>";
	echo "<TD ALIGN=RIGHT><B>Average Hold time for Answered Calls:</B></TD><TD ALIGN=LEFT>&nbsp; $AVGhold_sec_answer_calls </TD>";
	echo "<TD ALIGN=RIGHT><B> TIME:</B> </TD><TD ALIGN=LEFT> $NOW_TIME </TD>";
	echo "";
	echo "</TR>";
	echo "<TR>";
	echo "<TD ALIGN=RIGHT><B>DROPS TODAY:</B></TD><TD ALIGN=LEFT>&nbsp; $dropsTODAY&nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><B>TMA 2:</B></TD><TD ALIGN=LEFT>&nbsp; $PCThold_sec_stat_two% </TD>";
	echo "<TD ALIGN=RIGHT><B>Average Hold time for Dropped Calls:</B></TD><TD ALIGN=LEFT>&nbsp; $AVGhold_sec_drop_calls </TD>";
	echo "<TD ALIGN=RIGHT> </TD><TD ALIGN=LEFT> </TD>";
	echo "";
	echo "</TR>";
	echo "<TR>";
	echo "<TD ALIGN=RIGHT><B>ANSWERS TODAY:</B></TD><TD ALIGN=LEFT>&nbsp; $answersTODAY&nbsp; </TD>";
	echo "<TD ALIGN=RIGHT COLSPAN=2><B>(Agent non-pause time / Answers)</B></TD>";
	echo "<TD ALIGN=RIGHT><B>Average Hold time for All Calls:</B></TD><TD ALIGN=LEFT>&nbsp; $AVGhold_sec_queue_calls </TD>";
	echo "<TD ALIGN=RIGHT> </TD><TD ALIGN=LEFT> </TD>";
	echo "";
	echo "</TR>";
	echo "<TR>";
	echo "<TD ALIGN=RIGHT><B>DROP PERCENT:</B></TD><TD ALIGN=LEFT>&nbsp; $drpctTODAY%&nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><B>PRODUCTIVITY:</B></TD><TD ALIGN=LEFT>&nbsp; $AVG_ANSWERagent_non_pause_sec </TD>";
	echo "<TD ALIGN=RIGHT></TD><TD ALIGN=LEFT></TD>";
	echo "<TD ALIGN=RIGHT></TD><TD ALIGN=LEFT></TD>";
	echo "";
	echo "</TR>";
	}

##### NOT INBOUND ONLY ###
else
	{
	if (eregi('ALL-ACTIVE',$group_string))
		{
		$non_inboundSQL='';
		if (ereg('N',$with_inbound))
			{$non_inboundSQL = "and campaign_id NOT IN($ALLcloser_campaignsSQL)";}
		else
			{$non_inboundSQL = "and campaign_id IN($group_SQL,$closer_campaignsSQL)";}
		$multi_drop++;
		$stmt="select avg(auto_dial_level),min(dial_status_a),min(dial_status_b),min(dial_status_c),min(dial_status_d),min(dial_status_e),min(lead_order),min(lead_filter_id),sum(hopper_level),min(dial_method),avg(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),min(available_only_ratio_tally),min(adaptive_latest_server_time),min(local_call_time),avg(dial_timeout),min(dial_statuses),max(agent_pause_codes_active),max(list_order_mix),max(auto_hopper_level) from vicidial_campaigns where active='Y' $group_SQLand;";

		$stmtB="select sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4),sum(agent_calls_today),sum(agent_wait_today),sum(agent_custtalk_today),sum(agent_acw_today),sum(agent_pause_today) from vicidial_campaign_stats where calls_today > -1 $non_inboundSQL;";
		}
	else
		{
		if ($DB > 0) {echo "\n|$with_inbound|$campaign_allow_inbound|\n";}

		if ( (ereg('Y',$with_inbound)) and ($campaign_allow_inbound > 0) )
			{
			$multi_drop++;
			if ($DB) {echo "with_inbound|$with_inbound|$campaign_allow_inbound\n";}

			$stmt="select auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout,dial_statuses,agent_pause_codes_active,list_order_mix,auto_hopper_level from vicidial_campaigns where campaign_id IN ($group_SQL,$closer_campaignsSQL);";

			$stmtB="select sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4),sum(agent_calls_today),sum(agent_wait_today),sum(agent_custtalk_today),sum(agent_acw_today),sum(agent_pause_today) from vicidial_campaign_stats where campaign_id IN ($group_SQL,$closer_campaignsSQL);";
			}
		else
			{
			$stmt="select avg(auto_dial_level),max(dial_status_a),max(dial_status_b),max(dial_status_c),max(dial_status_d),max(dial_status_e),max(lead_order),max(lead_filter_id),max(hopper_level),max(dial_method),max(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),max(available_only_ratio_tally),max(adaptive_latest_server_time),max(local_call_time),max(dial_timeout),max(dial_statuses),max(agent_pause_codes_active),max(list_order_mix),max(auto_hopper_level) from vicidial_campaigns where campaign_id IN($group_SQL);";

			$stmtB="select sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),max(status_category_1),sum(status_category_count_1),max(status_category_2),sum(status_category_count_2),max(status_category_3),sum(status_category_count_3),max(status_category_4),sum(status_category_count_4),sum(agent_calls_today),sum(agent_wait_today),sum(agent_custtalk_today),sum(agent_acw_today),sum(agent_pause_today) from vicidial_campaign_stats where campaign_id IN($group_SQL);";
			}
		}
	if ($DB > 0) {echo "\n|$stmt|$stmtB|\n";}

	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$DIALlev =		$row[0];
	$DIALstatusA =	$row[1];
	$DIALstatusB =	$row[2];
	$DIALstatusC =	$row[3];
	$DIALstatusD =	$row[4];
	$DIALstatusE =	$row[5];
	$DIALorder =	$row[6];
	$DIALfilter =	$row[7];
	$HOPlev =		$row[8];
	$DIALmethod =	$row[9];
	$maxDIALlev =	$row[10];
	$DROPmax =		$row[11];
	$targetDIFF =	$row[12];
	$ADAintense =	$row[13];
	$ADAavailonly =	$row[14];
	$TAPERtime =	$row[15];
	$CALLtime =		$row[16];
	$DIALtimeout =	$row[17];
	$DIALstatuses =	$row[18];
	$agent_pause_codes_active = $row[19];
	$DIALmix =		$row[20];
	$AHOPlev =      $row[21];


	$stmt="select count(*) from vicidial_hopper $group_SQLwhere;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$VDhop = $row[0];

	$rslt=mysql_query($stmtB, $link);
	$row=mysql_fetch_row($rslt);
	$DAleads =		$row[0];
	$callsTODAY =	$row[1];
	$dropsTODAY =	$row[2];
	$drpctTODAY =	$row[3];
	$diffONEMIN =	$row[4];
	$agentsONEMIN = $row[5];
	$balanceFILL =	$row[6];
	$answersTODAY = $row[7];
	/*if ($multi_drop > 0)
		{
		if ( ($dropsTODAY > 0) and ($answersTODAY > 0) )
			{
			$drpctTODAY = ( ($dropsTODAY / $callsTODAY) * 100);
			*/$drpctTODAY = round($drpctTODAY, 2);
			$drpctTODAY = sprintf("%01.2f", $drpctTODAY);/*
			}
		else
			{$drpctTODAY=0;}
		}*/
	$VSCcat1 =		$row[8];
	$VSCcat1tally = $row[9];
	$VSCcat2 =		$row[10];
	$VSCcat2tally = $row[11];
	$VSCcat3 =		$row[12];
	$VSCcat3tally = $row[13];
	$VSCcat4 =		$row[14];
	$VSCcat4tally = $row[15];
	$VSCagentcalls =	$row[16];
	$VSCagentwait =		$row[17];
	$VSCagentcust =		$row[18];
	$VSCagentacw =		$row[19];
	$VSCagentpause =	$row[20];

	if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) )
		{
		$diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
		$diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
		}
	else {$diffpctONEMIN = '0.00';}

	$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats $group_SQLwhere;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$balanceSHORT = $row[0];

	if (ereg('DISABLED',$DIALmix))
		{
		$DIALstatuses = (preg_replace("/ -$|^ /","",$DIALstatuses));
		$DIALstatuses = (ereg_replace(' ',', ',$DIALstatuses));
		}
	else
		{
		$stmt="select vcl_id from vicidial_campaigns_list_mix where status='ACTIVE' $group_SQLand limit 1;";
		$rslt=mysql_query($stmt, $link);
		$Lmix_to_print = mysql_num_rows($rslt);
		if ($Lmix_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$DIALstatuses = "List Mix: $row[0]";
			$DIALorder =	"List Mix: $row[0]";
			}
		}
	$DIALlev = sprintf("%01.3f", $DIALlev);
	$agentsONEMIN = sprintf("%01.2f", $agentsONEMIN);
	$diffONEMIN = sprintf("%01.2f", $diffONEMIN);
	
	
######################################################################################
#			estatisticas do realtime	
######################################################################################
	echo "<table style='width:97%; margin-left:auto; margin-right:auto;' >";
	echo "<tr>";
######################################################################		
	echo "<td>";
	echo "<div class='cc-mstyle' style='width:100%;margin-left:-1px'>";
	echo "<table>";
	echo "<tr>";
	
	switch ($DIALmethod) {
			case "MANUAL": $DIALmethod = "Manual"; break;
			case "RATIO": $DIALmethod = "Rácio"; break;
			case "ADAPT_HARD_LIMIT": $DIALmethod = "Adapt. Limite"; break;
			case "ADAPT_TAPERED": $DIALmethod = "Adapt. Alterada"; break;
			case "ADAPT_AVERAGE": $DIALmethod = "Adapt. Média"; break;
			case "INBOUND_MAN": $DIALmethod = "Inbound"; break;
			}

	
	echo "<td id=icon16><img src='/images/icons/fax_16.png'>  <td style='text-align:left'>Metodo de Marcação:</td><td style='text-align:left'> $DIALmethod   </td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td id=icon16><img src='/images/icons/layer_aspect_arrow_inverted_16.png'>  <td style='text-align:left'> Racio de Marcação: </td> <td style='text-align:left'>$DIALlev <img style='cursor:pointer' title='Alterar o Rácio de Marcação.' src='/images/icons/livejournal_16.png' onclick=javascript:change_auto_dial_level(\"$group_string\"); /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td id=icon16><img src='/images/icons/participation_rate_16.png'><td style='text-align:left'>Contactos Disponiveis:</td><td style='text-align:left'> $DAleads   </td>";
	echo "</tr>";
	echo "<tr>";
	
	
	switch ($DIALorder) {
				
				
				case "RANDOM": $DIALorder = "Aleatória"; break;
				case "DOWN": $DIALorder = "Descendente"; break;
				case "UP": $DIALorder = "Ascendente"; break;
				case "DOWN PHONE": $DIALorder = "Tlf. Descendente"; break;
				case "UP PHONE": $DIALorder = "Tlf. Ascendente"; break;
				case "DOWN LAST NAME": $DIALorder = "Ult. Nome Descendente"; break;
				case "UP LAST NAME": $DIALorder = "Ult. Nome Ascendente"; break;
				
				}
	
	
	echo "<td id=icon16><img src='/images/icons/text_list_numbers_16.png'>  <td style='text-align:left'>Ordem de Marcação</td><td style='text-align:left'> $DIALorder </td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "</td>";
#####################################################################	
	echo "<td>";
	echo "<div class='cc-mstyle'>";
	echo "<table>";
	echo "<tr>";
	echo "<td id=icon16><img src='/images/icons/date_next_16.png'>  <td style='text-align:left'>Chamadas Efectuadas Hoje:</td><td style='text-align:left'> $callsTODAY   </td>";	
	echo "<tr>";
	echo "<td id=icon16><img src='/images/icons/chart_curve_16.png'>  <td style='text-align:left'>Atendidas:</td><td style='text-align:left'> $answersTODAY  </td>";
	echo "<tr>";
	echo "<tr>";
	echo "<td id=icon16><img src='/images/icons/chart_curve_error_16.png'>  <td style='text-align:left'>Chamadas Perdidas:</td><td style='text-align:left'> $dropsTODAY </td>";
	echo "<tr>";
	echo "<td id=icon16><img src='/images/icons/chart_curve_error_16.png'>  <td style='text-align:left'>Percentagem de Chamadas Perdidas:</td><td style='text-align:left'> ";
	if ($drpctTODAY >= $DROPmax)
		{echo "<font color=red><B>$drpctTODAY%</B>";}
	else
		{echo "$drpctTODAY%";}
	echo "</tr>";
	
	echo "</table>";
	echo "</div>";
	echo "</td>";
######################################################################	
	echo "<td>";
	echo "<div class='cc-mstyle' style='width:100%;margin-right:1px'>";
	echo "<table>";
	echo "<tr>";
	
	#	if ($AGENTtimeSTATS>0)
	#	{
		if ( ($VSCagentcalls > 0) and ($VSCagentpause > 0) )
			{
			$avgpauseTODAY = ($VSCagentpause / $VSCagentcalls);
			$avgpauseTODAY = round($avgpauseTODAY, 0);
                        $avgpauseTODAY = sec_convert($avgpauseTODAY,'M'); 			
                        #$avgpauseTODAY = sprintf("%01.0f", $avgpauseTODAY);
			}
		else
			{$avgpauseTODAY=0;}

		if ( ($VSCagentcalls > 0) and ($VSCagentwait > 0) )
			{
				$datainicio = $NOW_DAY;
				$datafim = date("Y-m-d", strtotime("+1 day".$datainicio));
				
				$stmt="select avg(wait_sec) from vicidial_agent_log where event_time >= '$datainicio' and event_time <= '$datafim' and pause_sec<65000 and wait_sec<65000 and talk_sec<65000 and dispo_sec<65000 $group_SQLand;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$OUToutput .= "$stmt\n";}
	$row=mysql_fetch_row($rslt);

	$AVGwait = $row[0];
	$AVGwait =	round($AVGwait);
	$AVGwait =	sec_convert($AVGwait,'M'); 
	#$AVGwait =		sprintf("%6s", $AVGwait_MS);
				
				$avgwaitTODAY = $AVGwait;
				
			#$avgwaitTODAY = ($VSCagentwait / $VSCagentcalls);
			#$avgwaitTODAY = round($avgwaitTODAY, 0);
			#$avgwaitTODAY = sprintf("%01.0f", $avgwaitTODAY);
			}
		else
			{$avgwaitTODAY=0;}

		if ( ($VSCagentcalls > 0) and ($VSCagentcust > 0) )
			{
			$avgcustTODAY = ($VSCagentcust / $VSCagentcalls);
			$avgcustTODAY = round($avgcustTODAY, 0);
                        $avgcustTODAY = sec_convert($avgcustTODAY,'M'); 
			#$avgcustTODAY = sprintf("%01.0f", $avgcustTODAY);
			}
		else
			{$avgcustTODAY=0;}

		if ( ($VSCagentcalls > 0) and ($VSCagentacw > 0) )
			{
			$avgacwTODAY = ($VSCagentacw / $VSCagentcalls);
			$avgacwTODAY = round($avgacwTODAY, 0);
                        $avgacwTODAY = sec_convert($avgacwTODAY,'M'); 
			#$avgacwTODAY = sprintf("%01.0f", $avgacwTODAY);
			}
		else
			{$avgacwTODAY=0;}


		echo "<tr><td id=icon16><img src='/images/icons/clock_red_16.png'>  <td style='text-align:left'>Tempo médio em Espera: </TD><td style='text-align:left'>$avgwaitTODAY</TD>";
		echo "<tr><td id=icon16><img src='/images/icons/clock_go_16.png'>  <td style='text-align:left'>Tempo médio em Chamada:</B></TD><td style='text-align:left'>$avgcustTODAY</TD>";
		echo "<tr><td id=icon16><img src='/images/icons/clock_edit_16.png'>  <td style='text-align:left'>Tempo médio gasto depois de uma Chamada:</TD><td style='text-align:left'>$avgacwTODAY</TD>";
		echo "<tr><td id=icon16><img src='/images/icons/clock_pause_16.png'>  <td style='text-align:left'>Tempo médio em Pausa:</TD><td style='text-align:left'>$avgpauseTODAY</TD>";
	
	#	}
	echo "</table>";
	echo "</div>";
	echo "</td>";
########################################################################################	
	echo "</tr>";
	
	echo "</table>";
########################################################################################
#
########################################################################################

	echo "$DROPINGROUPstatsHTML\n";
	echo "$CARRIERstatsHTML\n";
	echo "$PRESETstatsHTML\n";
	}

echo "<TR>";
echo "<TD ALIGN=LEFT COLSPAN=8 style='border-bottom:none;'>";
if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) )
	{echo "<B>$VSCcat1:</B> $VSCcat1tally   \n";}
if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) )
	{echo "<B>$VSCcat2:</B> $VSCcat2tally   \n";}
if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) )
	{echo "<B>$VSCcat3:</B> $VSCcat3tally   \n";}
if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) )
	{echo "<B>$VSCcat4:</B> $VSCcat4tally   \n";}
echo "</TD></TR>";

echo "</table></div>";

echo $ingroup_detail; 

if ($RTajax < 1)
	{
	if ($adastats<2)
		{
		echo "<a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=2&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>+ VIEW MORE</a>";
		}
	else
		{
		echo "<a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=1&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>+ VIEW LESS</a>";
		}
	if ($UGdisplay>0)
		{
		echo "<a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=0&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>HIDE USER GROUP</a>";
		}
	else
		{
		echo "<a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=1&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>VIEW USER GROUP</a>";
		}
	if ($SERVdisplay>0)
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=0&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>HIDE SERVER INFO</a>";
		}
	else
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=1&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>SHOW SERVER INFO</a>";
		}
	if ($CALLSdisplay>0)
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=0&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>HIDE WAITING CALLS</a>";
		}
	else
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=1&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>SHOW WAITING CALLS</a>";
		}

	if ($ALLINGROUPstats>0)
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=0&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>HIDE IN-GROUP STATS</a>";
		}
	else
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=1&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>SHOW IN-GROUP STATS</a>";
		}

	if ($PHONEdisplay>0)
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=0&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>HIDE PHONES</a>";
		}
	else
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=1&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>SHOW PHONES</a>";
		}
	if ($CUSTPHONEdisplay>0)
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=0&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>HIDE CUSTPHONES</a>";
		}
	else
		{
		echo " <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=1&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS'>SHOW CUSTPHONES</a>";
		}
	}

echo "</TD>";
echo "</TR>";
echo "</TABLE>";
echo "</div>";

echo "</FORM>\n\n";

##### check for campaigns with no dialable leads if enabled #####
if ( ($with_inbound != 'O') and ($NOLEADSalert == 'YES') )
	{
	$NDLcampaigns='';
	$stmtB="select campaign_name from vicidial_campaign_stats a inner join  vicidial_campaigns b on a.campaign_id=b.campaign_id where a.campaign_id IN($group_SQL) and dialable_leads < 1 order by a.campaign_id;";
	if ($DB > 0) {echo "\n|$stmt|$stmtB|\n";}
	$rslt=mysql_query($stmtB, $link);
	$campaigns_to_print = mysql_num_rows($rslt);
	$ctp=0;
	while ($campaigns_to_print > $ctp)
		{
		$row=mysql_fetch_row($rslt);
		$NDLcampaigns .=		" <a href='../admin.php?ADD=34&campaign_id=$row[0]\">$row[0]</a> \n";
		$ctp++;
		if (preg_match("/0$|5$/",$ctp))
			{$NDLcampaigns .= "<BR>";}
		}
	if ($ctp > 0)
		{
		echo "<span style=\"box-shadow:0px 0px 1px 1000000px rgba(0, 0, 0, 0.5);position:absolute;left:50%;top:50%;margin-left:-150px;margin-top:-100px;z-index:15;width:300px;padding:20px;\" id=no_dialable_leads_span class=cc-mstyle>\n";
		echo "\n";
		echo "<div style='float:right;'><img style='cursor:pointer;' src='/images/icons/cross_32.png' onclick=\"closeAlert('no_dialable_leads_span');\"></div>";
		echo "<div><b>Campanhas sem leads:$NDLcampaigns<b></div>";
		echo "";
		echo "\n";
		echo "</span>\n";
		}
	}
}



###################################################################################
###### INBOUND/OUTBOUND CALLS
###################################################################################
if ($campaign_allow_inbound > 0)
	{
	if (eregi('ALL-ACTIVE',$group_string)) 
		{
		$stmt="select closer_campaigns from vicidial_campaigns where active='Y' $group_SQLand";
		$rslt=mysql_query($stmt, $link);
		$closer_campaigns="";
		while ($row=mysql_fetch_row($rslt)) 
			{
			$closer_campaigns.="$row[0]";
			}
		$closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
		$closer_campaigns = preg_replace("/ - /"," ",$closer_campaigns);
		$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
		$closer_campaignsSQL = "'$closer_campaigns'";
		}	
	$stmtB="from vicidial_auto_calls where status NOT IN('XFER') and ( (call_type='IN' and campaign_id IN($closer_campaignsSQL)) or (call_type IN('OUT','OUTBALANCE') $group_SQLand) ) order by queue_priority desc,campaign_id,call_time;";
	}
else
	{
	$stmtB="from vicidial_auto_calls where status NOT IN('XFER') $group_SQLand order by queue_priority desc,campaign_id,call_time;";
	}
if ($CALLSdisplay > 0)
	{
	$stmtA = "SELECT status,campaign_id,phone_number,server_ip,UNIX_TIMESTAMP(call_time),call_type,queue_priority,agent_only";
	}
else
	{
	$stmtA = "SELECT status";
	}


$out_total=0;
	$out_ring=0;
	$out_live=0;
	$in_ivr=0;	
	
$k=0;
$agentonlycount=0;
$stmt = "$stmtA $stmtB";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0)
	{
	$i=0;
	
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);

		if (eregi("LIVE",$row[0])) 
			{
			$out_live++;

			if ($CALLSdisplay > 0)
				{
				$CDstatus[$k] =			$row[0];
				$CDcampaign_id[$k] =	$row[1];
				$CDphone_number[$k] =	$row[2];
				$CDserver_ip[$k] =		$row[3];
				$CDcall_time[$k] =		$row[4];
				$CDcall_type[$k] =		$row[5];
				$CDqueue_priority[$k] =	$row[6];
				$CDagent_only[$k] =		$row[7];
				if (strlen($CDagent_only[$k]) > 0) {$agentonlycount++;}
				$k++;
				}
			}
		else
			{
			if (eregi("IVR",$row[0])) 
				{
				$in_ivr++;

				if ($CALLSdisplay > 0)
					{
					$CDstatus[$k] =			$row[0];
					$CDcampaign_id[$k] =	$row[1];
					$CDphone_number[$k] =	$row[2];
					$CDserver_ip[$k] =		$row[3];
					$CDcall_time[$k] =		$row[4];
					$CDcall_type[$k] =		$row[5];
					$CDqueue_priority[$k] =	$row[6];
					$CDagent_only[$k] =		$row[7];
					if (strlen($CDagent_only[$k]) > 0) {$agentonlycount++;}
					$k++;
					}
				}
			if (eregi("CLOSER",$row[0])) 
				{$nothing=1;}
			else 
				{$out_ring++;}
			}

		$out_total++;
		$i++;
		}

	##### MIDI alert audio file test #####
	#	$test_midi=1;
	#	if ($test_midi > 0)
	#		{
	#	#	echo "<bgsound src=../sips-admin//"../sips-admin/up_down.mid/" loop=\"-1\">";
	#	#	echo "<embed src=../sips-admin//"../sips-admin/up_down.mid/" loop=\"-1\">";
	#		echo "<object type=\"audio/x-midi\" data=../sips-admin//"../sips-admin/up_down.mid/" width=200 height=20>";
	#		echo "  <param name=\"src\" value=\"../sips-admin/up_down.mid\">";
	#		echo "  <param name=\"autoplay\" value=\"true\">";
	#		echo "  <param name=\"autoStart\" value=\"1\">";
	#		echo "  <param name=\"loop\" value=\"1\">";
	#		echo "	alt : <a href='../sips-admin/../sips-admin/up_down.mid/">test.mid</a>";
	#		echo "</object>";
	#		}

		if ($out_live > 0) {$F='<FONT class="r1">'; $FG='</FONT>';}
		if ($out_live > 4) {$F='<FONT class="r2">'; $FG='</FONT>';}
		if ($out_live > 9) {$F='<FONT class="r3">'; $FG='</FONT>';}
		if ($out_live > 14) {$F='<FONT class="r4">'; $FG='</FONT>';}

	/*	echo "<br>";
		echo "<div style='margin-bottom:8px;' class='cc-mstyle'>";
		echo "<table>";
		
		
		if ($campaign_allow_inbound > 0)
			{echo "<tr><td id=icon16><img src='/images/icons/phone_16.png' /></td><td style=text-align:left>Chamadas Activas: $out_total</td> ";}
		else
			{echo "<tr><td id=icon16><img src='/images/icons/phone_16.png' /></td><td style=text-align:left>Chamadas em Colocação:  $out_total </td> ";}
		
		echo "<td id=icon16><img src='/images/icons/phone_sound_16.png' /></td><td style=text-align:left>A Chamar: $out_ring </td>";
		echo "<td id=icon16><img src='/images/icons/phone_delete_16.png' /></td><td style=text-align:left>Chamadas à Espera de Users:  $out_live </td> ";
		echo "<td id=icon16><img src='/images/icons/blackberry_16.png' /></td><td style=text-align:left>Chamadas em IVR:  $in_ivr </td> "; */
		}
	else
	{
	# "<br> "; #NO LIVE CALLS WAITING 
	}

	echo "</table></div>"; 
	#echo "<br>";

###################################################################################
###### CALLS WAITING
###################################################################################
$agentonlyheader = '';
if ($agentonlycount > 0)
	{$agentonlyheader = 'AGENTONLY';}
$Cecho = '<br>';
$Cecho .= "<div class='cc-mstyle'><table><tr><th>Descrição</th><th>Telefone</th><th>Tempo de Dial</th><th>Tipo Chamada</th><th>Prioridade</th></tr>";
#$Cecho .= " STATUS | CAMPAIGN             | PHONE NUMBER | SERVER_IP       | DIALTIME| CALL TYPE  | PRIORITY | $agentonlyheader\n";
#$Cecho .= "+--------+----------------------+--------------+-----------------+---------+------------+----------+\n";

$p=0;
while($p<$k)
	{
	$Cstatus =			$CDstatus[$p];
	$Ccampaign_id =		$CDcampaign_id[$p];
	$Cphone_number =	$CDphone_number[$p];
	$Cserver_ip =		$CDserver_ip[$p];
	$Ccall_type =		$CDcall_type[$p];
	$Cqueue_priority =	$CDqueue_priority[$p];
	$Cagent_only =		$CDagent_only[$p];

	$Ccall_time_S = ($STARTtime - $CDcall_time[$p]);
	$Ccall_time_MS =		sec_convert($Ccall_time_S,'M'); 
	$Ccall_time_MS =		$Ccall_time_MS;

	$G = '';		$EG = '';
	if ($CDcall_type[$p] == 'IN')
		{
		$G="<SPAN class=\"csc$CDcampaign_id[$p]\"><B>"; $EG='</B></SPAN>';
		}
	if (strlen($CDagent_only[$p]) > 0)
		{$Gcalltypedisplay = "$G$Cagent_only$EG";}
	else
		{$Gcalltypedisplay = '';}



              $pnqry = trim($Cphone_number);
              $getLoja = mysql_query("SELECT b.did_description, a.campaign_id from vicidial_auto_calls a join vicidial_inbound_dids b on a.agent_only = b.user where a.phone_number LIKE '$pnqry'", $link) or die(mysql_error());
              $getLoja = mysql_fetch_assoc($getLoja);
              if ($getLoja['campaign_id'] == 'AGENTDIRECT') {
                    $nome_campanha = $getLoja['did_description'];
                     
                } else {
                    
                    $getLoja = mysql_query("select campaign_id from vicidial_auto_calls where phone_number LIKE '$pnqry'", $link) or die(mysql_error());
                    $getLoja = mysql_fetch_assoc($getLoja);

                    $nome_campanha = $getLoja['campaign_id'];
                    
                }
                                  
                                  
          
                
	$Cecho .= "<tr><td> $nome_campanha </td> <td>$Cphone_number </td> <td>$Ccall_time_MS </td> <td> ".$Ccall_type."BOUND </td> <td> $Cqueue_priority </td> </tr>";
	#$G$Cserver_ip$EG $Gcalltypedisplay 
	$p++;
	}
$Cecho .= "</table></div><br>";

if ($p<1)
	{$Cecho='';} 
	
	
	

###################################################################################
###### AGENT TIME ON SYSTEM
###################################################################################

$agent_incall=0;
$agent_ready=0;
$agent_paused=0;
$agent_dispo=0;
$agent_dead=0;
$agent_total=0;


$phoneord=$orderby;
$userord=$orderby;
$groupord=$orderby;
$timeord=$orderby;
$campaignord=$orderby;

if ($phoneord=='phoneup') {$phoneord='phonedown';}
  else {$phoneord='phoneup';}
if ($userord=='userup') {$userord='userdown';}
  else {$userord='userup';}
if ($groupord=='groupup') {$groupord='groupdown';}
  else {$groupord='groupup';}
if ($timeord=='timeup') {$timeord='timedown';}
  else {$timeord='timeup';}
if ($campaignord=='campaignup') {$campaignord='campaigndown';}
  else {$campaignord='campaignup';}
 
#$Aecho .= "VICIDIAL: Agents Time On Calls Campaign: $group_string            $NOW_TIME\n";
#	$Aecho .= "  $agentcount agents logged in on all servers\n";
	#	$Aecho .= "  System Load Average: $load_ave \n\n";

$Aecho .= "<div class='cc-mstyle'>";
$Aecho .= "<table>";

$HDbegin =			"";
$HTbegin =			"";
$HDstation =		"";
$HTstation =		" <tr><th>Estação</th>";
$HDphone =		"";
$HTphone =		" <th><a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$phoneord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS\">PHONE</a></th>";
if ($RTajax > 0)
	{$HTphone =		" <th><a href='#' onclick=\"update_variables('orderby','phone');\">PHONE</a></th>";}



/*$HTuser =			" <th><a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$userord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS\">USER</a></th>  ";
if ($RTajax > 0)
	{$HTuser =	" <th><a href='#' onclick=\"update_variables('orderby','user');\">USER</a><img src=/images/icons/clock_16.png onclick=\"javascript:ingroup_info('$Luser','$j');\" /><img src=/images/icons/user_16.png onclick=\"update_variables('UidORname','');\" /></th>     ";} */
	
	
	
	
	
if ($UidORname>0)
	{
	if ($RTajax > 0)
	{$HTuser =	" <th><a href='#' onclick=\"update_variables('orderby','user');\">Operador</a><img src=/images/icons/user_16.png onclick=\"update_variables('UidORname','');\" /></th>     ";}	#<img src=/images/icons/google_adsense_16.png onclick=\"javascript:ingroup_info('$Luser','$j');\" />
	else
		{
		$HTuser =			" <th><a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$userord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS\">Operador</a></th>  ";
		}
	}
else
	{
	if ($RTajax > 0)
	{$HTuser =	" <th><a href='#' onclick=\"update_variables('orderby','user');\">Operador</a><img src=/images/icons/user_gray_16.png onclick=\"update_variables('UidORname','');\" /></th>     ";}	#<img src=/images/icons/google_adsense_16.png onclick=\"javascript:ingroup_info('$Luser','$j');\" />
	else
		{
		$HTuser =			" <th><a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$userord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS\">Operador</a></th>  ";
		}
	}

	
	

$HDusergroup =		"";
$HTusergroup =		" <th><a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$groupord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS\">Grupo</a></th>";
if ($RTajax > 0) {
$HTusergroup =	"<th><a href='#' onclick=\"update_variables('orderby','group');\">Grupo</a></th>";}
$HDsessionid =		"";
$HTsessionid =		"<th> Auditoria </th><th>Acções</th>";
$HDbarge =			"";
$HTbarge =			"<th> Entrar na Chamada </th>";
$HDstatus =			"";
$HTstatus =			"<th> Estado </th>";
$HDcustphone =		"";
$HTcustphone =		"<th> Nº do Cliente  </th>";
$HDserver_ip =		"";
$HTserver_ip =		" <th> SERVER IP  </th>";
$HDcall_server_ip =	"";
$HTcall_server_ip =	"<th> CALL SERVER IP  </th>";
$HDtime =			"";
$HTtime =			"<th> <a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$timeord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS\">Tempo</a></th>";
if ($RTajax > 0)
	{$HTtime =	"<th> <a href='#' onclick=\"update_variables('orderby','time');\">Tempo</a> </th>";}
$HDcampaign =		"";
$HTcampaign =		" <th><a href='../sips-admin/$PHP_SELF?$groupQS&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$campaignord&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&PHONEdisplay=$PHONEdisplay&CUSTPHONEdisplay=$CUSTPHONEdisplay&with_inbound=$with_inbound&monitor_active=$monitor_active&monitor_phone=$monitor_phone&ALLINGROUPstats=$ALLINGROUPstats&DROPINGROUPstats=$DROPINGROUPstats&NOLEADSalert=$NOLEADSalert&CARRIERstats=$CARRIERstats&PRESETstats=$PRESETstats&AGENTtimeSTATS=$AGENTtimeSTATS\">Campanha</a></th>";
if ($RTajax > 0)
	{$HTcampaign =	"<th> <a href='#' onclick=\"update_variables('orderby','campaign');\">Campanha</a></th>";}
$HDcalls =			"";
$HTcalls =			"<th> Nº de Chamadas </th>";
$HDpause =	'';
$HTpause =	'';
$HDigcall =			"";
$HTigcall =			"";  #<th> Em Espera </th>

if (!ereg("N",$agent_pause_codes_active))
	{
	$HDstatus =			"";
	$HTstatus =			"<th> Estado </th>   ";
	$HDpause =			"";
	$HTpause =			"";
	}
if ($PHONEdisplay < 1)
	{
	$HDphone =	'';
	$HTphone =	'';
	}
/*if ($CUSTPHONEdisplay < 1)
	{
	$HDcustphone = '';
	$HTcustphone =	'';
	}*/
/*if ($UGdisplay < 1)
	{
	$HDusergroup =	'';
	$HTusergroup =	'';
	} */
/*if ( ($SIPmonitorLINK<1) and ($IAXmonitorLINK<1) and (!preg_match("/MONITOR|BARGE/",$monitor_active) ) ) 
	{
	$HDsessionid =	"";
	$HTsessionid =	"";
	} */
if ( ($SIPmonitorLINK<2) and ($IAXmonitorLINK<2) and (!preg_match("/BARGE/",$monitor_active) ) ) 
	{
	$HDbarge =		'';
	$HTbarge =		'';
	}
if ($SERVdisplay < 1)
	{
	$HDserver_ip =		'';
	$HTserver_ip =		'';
	$HDcall_server_ip =	'';
	$HTcall_server_ip =	'';
	}


if ($realtime_block_user_info > 0)
	{
	$Aline  = "$HDbegin$HDusergroup$HDsessionid$HDbarge$HDstatus$HDpause$HDcustphone$HDserver_ip$HDcall_server_ip$HDtime$HDcampaign$HDcalls$HDigcall\n";
	$Bline  = "$HTbegin$HTusergroup$HTsessionid$HTbarge$HTstatus$HTpause$HTcustphone$HTserver_ip$HTcall_server_ip$HTtime$HTcampaign$HTcalls$HTigcall\n";
	}
else
	{
	$Aline  = "$HDbegin$HDstation$HDphone$HDuser$HDsessionid$HDbarge$HDusergroup$HDstatus$HDpause$HDcustphone$HDserver_ip$HDcall_server_ip$HDtime$HDcampaign$HDcalls$HDigcall\n";
	$Bline  = "$HTbegin$HTstation$HTphone$HTuser$HTsessionid$HTbarge$HTusergroup$HTstatus$HTpause$HTcustphone$HTserver_ip$HTcall_server_ip$HTtime$HTcampaign$HTcalls$HTigcall\n";
	}
$Aecho .= "$Aline";
$Aecho .= "$Bline";
$Aecho .= "$Aline";




if ($orderby=='timeup') {$orderSQL='vicidial_live_agents.status,last_call_time';}
if ($orderby=='timedown') {$orderSQL='vicidial_live_agents.status desc,last_call_time desc';}
if ($orderby=='campaignup') {$orderSQL='vicidial_live_agents.campaign_id,vicidial_live_agents.status,last_call_time';}
if ($orderby=='campaigndown') {$orderSQL='vicidial_live_agents.campaign_id desc,vicidial_live_agents.status desc,last_call_time desc';}
if ($orderby=='groupup') {$orderSQL='user_group,vicidial_live_agents.status,last_call_time';}
if ($orderby=='groupdown') {$orderSQL='user_group desc,vicidial_live_agents.status desc,last_call_time desc';}
if ($orderby=='phoneup') {$orderSQL='extension,server_ip';}
if ($orderby=='phonedown') {$orderSQL='extension desc,server_ip desc';}
if ($UidORname > 0)
	{
	if ($orderby=='userup') {$orderSQL='full_name,status,last_call_time';}
	if ($orderby=='userdown') {$orderSQL='full_name desc,status desc,last_call_time desc';}
	}
else
	{
	if ($orderby=='userup') {$orderSQL='vicidial_live_agents.user';}
	if ($orderby=='userdown') {$orderSQL='vicidial_live_agents.user desc';}
	}

if ( (eregi('ALL-ACTIVE',$group_string)) and (strlen($group_SQL) < 3) ) {$UgroupSQL = '';}
else {$UgroupSQL = " and vicidial_live_agents.campaign_id IN($group_SQL)";}
if (strlen($usergroup)<1) {$usergroupSQL = '';}
else {$usergroupSQL = " and user_group='" . mysql_real_escape_string($usergroup) . "'";}



$ring_agents=0;
$stmt="select extension,vicidial_live_agents.user,conf_exten,vicidial_live_agents.status,vicidial_live_agents.server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,vicidial_live_agents.campaign_id,vicidial_users.user_group,vicidial_users.full_name,vicidial_live_agents.comments,vicidial_live_agents.calls_today,vicidial_live_agents.callerid,lead_id,UNIX_TIMESTAMP(last_state_change),on_hook_agent,ring_callerid,agent_log_id from vicidial_live_agents,vicidial_users where vicidial_live_agents.user=vicidial_users.user $UgroupSQL $usergroupSQL order by $orderSQL;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}








$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0)
	{
	$i=0;
	while ($i < $talking_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$Aextension[$i] =		$row[0];
		$Auser[$i] =			$row[1];
		$Asessionid[$i] =		$row[2];
		$Astatus[$i] =			$row[3];
		$Aserver_ip[$i] =		$row[4];
		$Acall_time[$i] =		($row[5]);
		$Acall_finish[$i] =		($row[6]);
		$Acall_server_ip[$i] =	$row[7];
		$Acampaign_id[$i] =		$row[8];
		$Auser_group[$i] =		$row[9];
		$Afull_name[$i] =		$row[10];
		$Acomments[$i] = 		$row[11];
		$Acalls_today[$i] =		$row[12];
		$Acallerid[$i] =		$row[13];
		$Alead_id[$i] =			$row[14];
		$Astate_change[$i] =	($row[15]);
		$Aon_hook_agent[$i] =	$row[16];
		$Aring_callerid[$i] =	$row[17];
		$Aagent_log_id[$i] =	$row[18];
		$Aring_note[$i] =		' ';

		if ($Aon_hook_agent[$i] == 'Y')
			{
			#$Aring_note[$i] = '*';
			$ring_agents++;
			if (strlen($Aring_callerid[$i]) > 18)
				{$Astatus[$i]="RING";}
			}


		### 3-WAY Check ###
		if ($Alead_id[$i]!=0) 
			{
			$threewaystmt="select UNIX_TIMESTAMP(last_call_time) from vicidial_live_agents where lead_id='$Alead_id[$i]' and status='INCALL' order by UNIX_TIMESTAMP(last_call_time) desc";
			$threewayrslt=mysql_query($threewaystmt, $link);
			if (mysql_num_rows($threewayrslt)>1) 
				{
				$Astatus[$i]="3-WAY";
				$srow=mysql_fetch_row($threewayrslt);
				$Acall_mostrecent[$i]=$srow[0];
				}
			}
		### END 3-WAY Check ###

		$i++;
		}

	$callerids='';
	$pausecode='';
	$stmt="select callerid,lead_id,phone_number from vicidial_auto_calls;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$calls_to_list = mysql_num_rows($rslt);
	if ($calls_to_list > 0)
		{
		$i=0;
		while ($i < $calls_to_list)
			{
			$row=mysql_fetch_row($rslt);
			$callerids .=	"$row[0]";
			$VAClead_ids[$i] =	$row[1];
			$VACphones[$i] =	$row[2];
			$i++;
			}
		}

	### Lookup phone logins
	$i=0;
	while ($i < $talking_to_print)
		{
		if (eregi("R/",$Aextension[$i])) 
			{
			$protocol = 'EXTERNAL';
			$dialplan = eregi_replace('R/',"",$Aextension[$i]);
			$dialplan = eregi_replace("\@.*",'',$dialplan);
			$exten = "dialplan_number='$dialplan'";
			}
		if (eregi("Local/",$Aextension[$i])) 
			{
			$protocol = 'EXTERNAL';
			$dialplan = eregi_replace('Local/',"",$Aextension[$i]);
			$dialplan = eregi_replace("\@.*",'',$dialplan);
			$exten = "dialplan_number='$dialplan'";
			}
		if (eregi('SIP/',$Aextension[$i])) 
			{
			$protocol = 'SIP';
			$dialplan = eregi_replace('SIP/',"",$Aextension[$i]);
			$dialplan = eregi_replace("-.*",'',$dialplan);
			$exten = "extension='$dialplan'";
			}
		if (eregi('IAX2/',$Aextension[$i])) 
			{
			$protocol = 'IAX2';
			$dialplan = eregi_replace('IAX2/',"",$Aextension[$i]);
			$dialplan = eregi_replace("-.*",'',$dialplan);
			$exten = "extension='$dialplan'";
			}
		if (eregi('Zap/',$Aextension[$i])) 
			{
			$protocol = 'Zap';
			$dialplan = eregi_replace('Zap/',"",$Aextension[$i]);
			$exten = "extension='$dialplan'";
			}
		if (eregi('DAHDI/',$Aextension[$i])) 
			{
			$protocol = 'Zap';
			$dialplan = eregi_replace('DAHDI/',"",$Aextension[$i]);
			$exten = "extension='$dialplan'";
			}

		$stmt="select login from phones where server_ip='$Aserver_ip[$i]' and $exten and protocol='$protocol';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$phones_to_print = mysql_num_rows($rslt);
		if ($phones_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$Alogin[$i] = "$row[0]-----$i";
			}
		else
			{
			$Alogin[$i] = "$Aextension[$i]-----$i";
			}
		$i++;
		}

### Sort by phone if selected
	if ($orderby=='phoneup')
		{
		sort($Alogin);
		}
	if ($orderby=='phonedown')
		{
		rsort($Alogin);
		}

	
	
### Run through the loop to display agents
                
         #check for inbound guys
                $inb_qry = mysql_query("select group_concat(user) from vicidial_live_inbound_agents") or die(mysql_error());
                $inb_qry = mysql_fetch_array($inb_qry);
                $inb_users = $inb_qry[0];
                
	$j=0;
	$agentcount=0;
	while ($j < $talking_to_print)
		{
		$n=0;
		$custphone='';
		while ($n < $calls_to_list)

			{
			if ( (ereg("$VAClead_ids[$n]", $Alead_id[$j])) and (strlen($VAClead_ids[$n]) == strlen($Alead_id[$j])) )
				{$custphone = $VACphones[$n];}
			$n++;
			}

		$phone_split = explode("-----",$Alogin[$j]);
		$i = $phone_split[1];

		if (eregi("READY|PAUSED",$Astatus[$i]))
			{
			$Acall_time[$i]=$Astate_change[$i];

			if ($Alead_id[$i] > 0)
				{
				$Astatus[$i] =	'DISPO';
				$Lstatus =		'DISPO';
				$status =		' DISPO';
				}
			}
		if ($non_latin < 1)
			{
			$extension = eregi_replace('Local/',"",$Aextension[$i]);
			$extension =		sprintf("%-14s", $extension);
			while(strlen($extension)>14) {$extension = substr("$extension", 0, -1);}
			}
		else
			{
			$extension = eregi_replace('Local/',"",$Aextension[$i]);
			$extension =		sprintf("%-48s", $extension);
			while(mb_strlen($extension, 'utf-8')>14) {$extension = mb_substr("$extension", 0, -1,'utf8');}
			}

		$phone =			sprintf("%-12s", $phone_split[0]);
		$custphone =		sprintf("%-11s", $custphone);
		$Luser =			$Auser[$i];
		$user =				sprintf("%-20s", $Auser[$i]);
		$Lsessionid =		$Asessionid[$i];
		$sessionid =		sprintf("%-9s", $Asessionid[$i]);
		$Lstatus =			$Astatus[$i];
		$status =			sprintf("%-6s", $Astatus[$i]);
		$Lserver_ip =		$Aserver_ip[$i];
		$server_ip =		sprintf("%-15s", $Aserver_ip[$i]);
		$call_server_ip =	sprintf("%-15s", $Acall_server_ip[$i]);
		$campaign_id =	$Acampaign_id[$i];
		$comments=		$Acomments[$i];
		$calls_today =	sprintf("%-5s", $Acalls_today[$i]);

		if (!ereg("N",$agent_pause_codes_active))
			{$pausecode='       ';}
		else
			{$pausecode='';}

		if (eregi("INCALL",$Lstatus)) 
			{
			$stmtP="select count(*) from parked_channels where channel_group='$Acallerid[$i]';";
			$rsltP=mysql_query($stmtP,$link);
			$rowP=mysql_fetch_row($rsltP);
			$parked_channel = $rowP[0];

			if ($parked_channel > 0)
				{
				$Astatus[$i] =	'PARK';
				$Lstatus =		'PARK';
				$status =		' PARK ';
				}
			else
				{
			/*	if (!ereg("$Acallerid[$i]\|",$callerids))
					{
					$Acall_time[$i]=$Astate_change[$i];

					$Astatus[$i] =	'DEAD';
					$Lstatus =		'DEAD';
					$status =		'DEAD ';
					} */
				}

			if ( (eregi("AUTO",$comments)) or (strlen($comments)<1) )
				{
                            
                            $CM="Auto";}
			else
				{
				if (eregi("INBOUND",$comments)) 
					{$CM='Inb';}
				else
					{$CM='Man';}
				}
			}
		else {$CM=' ';}

	/*	if ($UGdisplay > 0)
			{ */
			if ($non_latin < 1)
				{
				$user_group =		sprintf("%-12s", $Auser_group[$i]);
				while(strlen($user_group)>12) {$user_group = substr("$user_group", 0, -1);}
				}
			else
				{
				$user_group =		sprintf("%-40s", $Auser_group[$i]);
				while(mb_strlen($user_group, 'utf-8')>12) {$user_group = mb_substr("$user_group", 0, -1,'utf8');}
				}
			/*}*/
		if ($UidORname > 0)
			{
			if ($non_latin < 1)
				{
					
				$exploded_name = explode(" ", $Afull_name[$i]);	
				$num_name = count($exploded_name);
				
				#print_r($exploded_name);
				
				if ($num_name == 1) { $user = $exploded_name[0]; } else {
				
				$user = $exploded_name[0]." ".$exploded_name[$num_name-1]; }
					
			/*	$user =		sprintf("%-20s", $Afull_name[$i]);
				while(strlen($user)>20) {$user = substr("$user", 0, -1);} */
				}
			else
				{
					$exploded_name = explode($Afull_name[$i]);	
				$num_name = count($exploded_name);
				
				if ($num_name == 1) { $user = $exploded_name[0]; } else {
				
				$user = $exploded_name[0]." ".$exploded_name[$num_name-1]; }
				/*$user =		sprintf("%-60s", $Afull_name[$i]);
				while(mb_strlen($user, 'utf-8')>20) {$user = mb_substr("$user", 0, -1,'utf8');} */
				}
			}
		if (!eregi("INCALL|QUEUE|PARK|3-WAY",$Astatus[$i]))
			{$call_time_S = ($STARTtime - $Astate_change[$i]);}
		else if (eregi("3-WAY",$Astatus[$i]))
			{$call_time_S = ($STARTtime - $Acall_mostrecent[$i]);}
		else
			{$call_time_S = ($STARTtime - $Acall_time[$i]);}

		$call_time_MS =		sec_convert($call_time_S,'M'); 
		$call_time_MS =		sprintf("%7s", $call_time_MS);
		$call_time_MS =		" $call_time_MS";
		$G = '';		$EG = '';
		if ( ($Lstatus=='INCALL') or ($Lstatus=='PARK') )
			{
			if ($call_time_S >= 10) {$G='<PRE class="thistle"><B>'; $EG='</B></PRE>';}
			if ($call_time_S >= 60) {$G='<PRE class="violet"><B>'; $EG='</B></PRE>';}
			if ($call_time_S >= 300) {$G='<PRE class="purple"><B>'; $EG='</B></PRE>';}
	#		if ($call_time_S >= 600) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
			}
		if ($Lstatus=='3-WAY')
			{
			if ($call_time_S >= 10) {$G='<PRE class="lime"><B>'; $EG='</B></PRE>';}
			}
		if ($Lstatus=='DEAD')
			{
			if ($call_time_S >= 21600) 
				{$j++; continue;} 
			else
				{
				$agent_dead++;  $agent_total++;
				$G=''; $EG='';
				if ($call_time_S >= 10) {$G='<PRE class="black"><B>'; $EG='</B></PRE>';}
				}
			}
		if ($Lstatus=='DISPO')
			{
			if ($call_time_S >= 21600) 
				{$j++; continue;} 
			else
				{
				$agent_dispo++;  $agent_total++;
				$G=''; $EG='';
				if ($call_time_S >= 10) {$G='<PRE class="khaki"><B>'; $EG='</B></PRE>';}
				if ($call_time_S >= 60) {$G='<PRE class="yellow"><B>'; $EG='</B></PRE>';}
				if ($call_time_S >= 300) {$G='<PRE class="olive"><B>'; $EG='</B></PRE>';}
				}
			}
		if ($Lstatus=='PAUSED') 
			{
			if (!ereg("N",$agent_pause_codes_active))
				{
				$twentyfour_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-24,date("i"),date("s"),date("m"),date("d"),date("Y")));
				$stmtC="select pause_code_name from vicidial_agent_log a INNER JOIN vicidial_pause_codes b ON a.sub_status=b.pause_code where agent_log_id >= '$Aagent_log_id[$i]' and user='$Luser' order by agent_log_id desc limit 1;";
				$rsltC=mysql_query($stmtC,$link);
				$rowC=mysql_fetch_row($rsltC);
				$pausecode = $rowC[0];
				}
			else
				{$pausecode='';}

			if ($call_time_S >= 21600) 
				{$j++; continue;} 
			else
				{
				$agent_paused++;  $agent_total++;
				$G=''; $EG='';
				if ($call_time_S >= 10) {$G='<PRE class="khaki"><B>'; $EG='</B></PRE>';}
				if ($call_time_S >= 60) {$G='<PRE class="yellow"><B>'; $EG='</B></PRE>';}
				if ($call_time_S >= 300) {$G='<PRE class="olive"><B>'; $EG='</B></PRE>';}
				}
			}

		if ( (eregi("INCALL",$status)) or (eregi("QUEUE",$status))  or (eregi("3-WAY",$status)) or (eregi("PARK",$status))) {$agent_incall++;  $agent_total++;}
		if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {$agent_total++;}
		if ( (eregi("PARK",$status))) {$agent_ready++; }
		if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) 
			{
			 
			$G='<PRE class="lightblue"><B>'; $EG='</B></PRE>';
			if ($call_time_S >= 60) {$G='<PRE class="blue"><B>'; $EG='</B></PRE>';}
			if ($call_time_S >= 300) {$G='<PRE class="midnightblue"><B>'; $EG='</B></PRE>';}
			}

		if ($Astatus[$i] == 'RING')
			{
			$agent_total++;
			$G=''; $EG='';
			if ($call_time_S >= 0) {$G='<PRE class="salmon"><B>'; $EG='</B></PRE>';}
			}

		$L='';
		$R='';
		if ($SIPmonitorLINK>0) {$L="<td><a href=\"sip:0$Lsessionid@$server_ip\">LISTEN</a>";   $R='';}
		if ($IAXmonitorLINK>0) {$L="<td><a href=\"iax:0$Lsessionid@$server_ip\">LISTEN</a>";   $R='';}
		if ($SIPmonitorLINK>1) {$R="<td><a href=\"sip:$Lsessionid@$server_ip\">BARGE</a>";}
		if ($IAXmonitorLINK>1) {$R="<td><a href=\"iax:$Lsessionid@$server_ip\">BARGE</a>";}
		
		
		
		if ( (strlen($monitor_phone)>1) and (preg_match("/MONITOR|BARGE/",$monitor_active) ) )
			{$L="<td> <a href=\"javascript:send_monitor('$Lsessionid','$Lserver_ip','MONITOR');\"><img src='/images/icons/sound_add_16.png' /></a>";   $R='';}
			
			$L="<td> <a href=\"javascript:send_monitor('$Lsessionid','$Lserver_ip','MONITOR');\"><img title='Ouvir Chamada' src='/images/icons/sound_add_16.png' /></a>&nbsp;&nbsp;&nbsp;&nbsp;";
			
			
			$R="<a href=\"javascript:send_monitor('$Lsessionid','$Lserver_ip','BARGE');\"><img title='Entrar na Chamada' src='/images/icons/comments_add_16.png' /></a></td>";
			
			
		if ( (strlen($monitor_phone)>1) and (preg_match("/BARGE/",$monitor_active) ) )
			{$R="<td> <a href=\"javascript:send_monitor('$Lsessionid','$Lserver_ip','BARGE');\"><img src='/images/icons/comments_add_16.png' /></a>";}
			
			
			

		if ($CUSTPHONEdisplay > 0)	{$CP = " <td>$G$custphone$EG</td> ";}
		#else	{$CP = "<td>$G$custphone$EG</td>";}
		else	{$CP = "<td>$custphone</td>";}

		$UGD = "<td>$user_group</td>";

		if ($SERVdisplay > 0)	{$SVD = " <td>$G$server_ip$EG</td> <td>$G$call_server_ip$EG</td>";}
		else	{$SVD = "";}

		if ($PHONEdisplay > 0)	{$phoneD = "<td>$G$phone$EG</td> ";}
		else	{$phoneD = "";}

		$vac_stage='';
		$vac_campaign='';
		$INGRP='';
		if ($CM == 'I') 
			{
			$stmt="select vac.campaign_id,vac.stage,vig.group_name from vicidial_auto_calls vac,vicidial_inbound_groups vig where vac.callerid='$Acallerid[$i]' and vac.campaign_id=vig.group_id LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$ingrp_to_print = mysql_num_rows($rslt);
				if ($ingrp_to_print > 0)
				{
				$row=mysql_fetch_row($rslt);
				$vac_campaign =	sprintf("%-20s", "$row[0] - $row[2]");
				$row[1] = eregi_replace(".*-",'',$row[1]);
				$vac_stage =	sprintf("%-4s", $row[1]);
				}

			$INGRP = " $G$vac_campaign$EG ";
			}



$agentcount++;

if ($campaign_allow_inbound == '1' ) { $HTigcall .=  "<th>IN-GROUP</th>";    }


		if ($realtime_block_user_info > 0)
			{
			$Aecho .= "|$UGD $G$sessionid$EG$L$R$Aring_note[$i] $G$status$EG $CM $pausecode|$CP$SVD$G$call_time_MS$EG | $G$campaign_id$EG | $G$calls_today$EG |$INGRP\n";
			}
		if ($realtime_block_user_info < 1)
			{
				
				
				
			$stmt = "SELECT campaign_name FROM vicidial_campaigns WHERE campaign_id = '$campaign_id';";
			$query = mysql_query($stmt, $link);
			$result = mysql_fetch_row($query); 
			 	
			$query="SELECT dial_method from vicidial_campaigns where campaign_id='$campaign_id'";
			$query=mysql_query($query);
			$query_r=mysql_fetch_row($query);	
			
			if(($status=='PAUSED') && ($query_r[0]=='RATIO')){$force_var = "<span style=cursor:pointer; onclick=ForceReady('$Auser[$i]','$user')><img title='Forçar Ready' src='/images/icons/user_go_16.png'></span>";}
			else {$force_var = "<span><img title='Não é possivel Forçar READY neste User. \n - O Operador tem de estar PAUSED. \n - A Campanha não pode ser MANUAL.' src='/images/icons/user_delete_16.png'></span>";}
			
			$kick_var="<span style=cursor:pointer;  onclick=\"ForceLogout('$Auser[$i]','$user')\" ><img title='Forçar logout.' src='/images/icons/cut_red_16.png'></span>";	
			#working	<a href=\"./user_status.php?user=$Luser\" target=\"_blank\"></a>
			$estado="";
                       
                        if (eregi("READY", $status)) {
                            $estado = "Pronto";
                        } elseif (eregi("PAUSED", $status)) {
                            if (!ereg("N",$agent_pause_codes_active)) {
                                $trimedguy = trim($pausecode);
                                if ($trimedguy == null) {
                                    $estado = "Disponível";
                                } else {
                                    $estado = "";
                                }
                            } else {
                                $estado = "Pausa";
                            }
                        } elseif (eregi("DISPO", $status)) {
                            $estado = "Wrap Up";
                        } elseif (eregi("INCALL", $status)) {
                            $estado = "Chamada";
                        } elseif (eregi("QUEUE", $status)) {
                            $estado = "A ligar-se...";
                        } elseif (eregi("PARK", $status)) {
                            $estado = "Em Espera";
                        } elseif (eregi("3-WAY", $status)) {
                            $estado = "Conferência";
                        } elseif (eregi("RING", $status)) {
                            $estado = "A Chamar...";
                        } elseif (eregi("CLOSER", $status)) {
                            $estado = "Pronto IN & OUT";    
                        } else {
                            $estado = $status;
                        }
                        
                       if (strpos($inb_users,$Luser) !== false ) { $inb_flag = 'style=background-color:#99CCFF'; } else { $inb_flag = 'teste'; }                       
                        $Aecho .= "<tr>
							<td>$extension</td> 
							$Aring_note[$i] 
							$phoneD 
							<td> $user </td> 
							
							$L$R
							
							<td>$force_var $kick_var</td>
							<td>$user_group</td>
							
							<td $inb_flag >$estado $CM $pausecode </td>
							$CP
							 
							
							<td>$call_time_MS</td>
							<td>$result[0]</td>
							<td> $calls_today </td>
							
							"; #<td> $vac_stage </td>
				if ($campaign_allow_inbound == "1") { $Aecho .=  "<td> $INGRP </td>"; }
                                $Aecho .= "</tr>";
			}
		
		$j++;
		}
		$Aecho .=  "</table> </DIV>"; 
		$Aecho .= "$Aline";
	#	$Aecho .= "  $agentcount agents logged in on all servers\n";
	#	$Aecho .= "  System Load Average: $load_ave \n\n";
                /*$Aecho .= "<div class='cc-mstyle' style='margin-top:10px'>";
		$Aecho .= "  <SPAN style='display:inline' class=\"orange\"><PRE>          </PRE> - Chamada equilibrada</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"lightblue\"><PRE>          </PRE> - Agente à espera de chamada</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"blue\"><PRE>          </PRE> - Agente à espera de chamada> 1 minuto</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"midnightblue\"><PRE>          </PRE> - Agente à espera de chamada> 5 minutos</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"thistle\"><PRE>          </PRE> - Agente em chamada > 10 seconds</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"violet\"><PRE>          </PRE> - Agente em chamada > 1 minuto</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"purple\"><PRE>          </PRE> - Agente em chamada > 5 minutos</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"khaki\"><PRE>          </PRE> - Agente em pausa > 10 seconds</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"yellow\"><PRE>          </PRE> - Agente em pausa > 1 minuto</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"olive\"><PRE>          </PRE> - Agente em pausa > 5 minutos</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"lime\"><PRE>          </PRE> - Agente em conferência > 10 segundos</SPAN>\n";
		$Aecho .= "  <SPAN style='display:inline' class=\"black\"><PRE>          </PRE> - Agente em chamada morta</SPAN>\n";

		if ($ring_agents > 0)
			{
			$Aecho .= "  <SPAN style='display:inline' class=\"salmon\"><PRE>          </PRE> - Telefone a tocar </SPAN>\n";
			$Aecho .= "  <SPAN><B>* Denotes on-hook agent</B></SPAN>\n";
			}
                $Aecho .=  "</DIV>";*/
		if ($agent_ready > 0) {$B='<FONT class="b1">'; $BG='</FONT>';}
		if ($agent_ready > 4) {$B='<FONT class="b2">'; $BG='</FONT>';}
		if ($agent_ready > 9) {$B='<FONT class="b3">'; $BG='</FONT>';}
		if ($agent_ready > 14) {$B='<FONT class="b4">'; $BG='</FONT>';}



		
		
		#echo "\n<BR>\n";
		
		
		#### CODIGO NOSSO #####
if ($campaign_allow_inbound > 0)

{ $BBB = "<tr><td id=icon16><img src='/images/icons/phone_16.png' /></td><td style=text-align:left>Chamadas Activas: $out_total</td>" ;}
		else
			{ $BBB = "<tr><td id=icon16><img src='/images/icons/phone_16.png' /></td><td style=text-align:left>Chamadas em Colocação:  $out_total </td>" ;}

###################

echo "






<table style='margin-left:auto; margin-right:auto; width:97%;'>
<tr>
<td>


			<table>
			<tr>
			<td>

				<div class='cc-mstyle' style='margin-bottom:8px; margin-left:-1px; margin-top:10px; width:100%;'>
				<table>
				
				<tr><td id=icon32 ><img src='/images/icons/user_green.png' /></td><td style='text-align:left; width:150px;'>Operadores Online:<td> <font size=6>$agent_total</td><td style='width:24px;'></td>
				
				<td id=icon32 ><img src='/images/icons/comment.png' /></td><td style='text-align:left; width:140px;'>Em Chamada:<td> <font size=6>$agent_incall </td>
				
				<tr><td id=icon32 ><img src='/images/icons/clock.png' /></td><td style='text-align:left;'>Em Espera: <td><font size=6>$agent_ready</td> <td style='width:16px;'></td>
				
				<td id=icon32 ><img src='/images/icons/cup.png' /></td><td style='text-align:left;'>Em Pausa:<td> <font size=6>$agent_paused</td> 
				
				<tr><td id=icon32 ><img src='/images/icons/pirate_flag.png' /></td><td style='text-align:left;'>Em Chamadas Mortas:<td> <font size=6>$agent_dead</td><td style='width:16px;'></td>
				
				<td id=icon32 ><img src='/images/icons/application_edit.png' /></td><td style='text-align:left;'>Feedback:<td> <font size=6>$agent_dispo </td>
				
				</table>
				</div>

			</td>
			</tr>
			</table>


</td>
<td>


			<table>
			<tr>

			<td>

			<div class='cc-mstyle' style='margin-top:12px; margin-bottom:8px; margin-right:-4px; width:95%;'>
			<table>";
			 if($group_string=="ALL-ACTIVE")
                             {$group_string_alterado = "Todos Activos";}
                          else
                             {$nome_campanha="SELECT group_concat(campaign_name) FROM vicidial_campaigns WHERE campaign_id IN ($group_SQL);";
                              $nome_campanha=mysql_query($nome_campanha, $link) or die(mysql_error());
                              $nome_campanha=mysql_fetch_array($nome_campanha);
                              
                              $group_string_alterado = $nome_campanha[0]; }
			echo"
			<tr><td id=icon16><img src='/images/icons/group_16.png' /></td><td style=text-align:left;>Total de Operadores Online: $agentcount</td></tr>
			
			$BBB
			<tr><td id=icon16><img src='/images/icons/phone_sound_16.png' /></td><td style=text-align:left>A Chamar: $out_ring </td>
			<tr><td id=icon16><img src='/images/icons/phone_delete_16.png' /></td><td style=text-align:left>Chamadas à Espera de Users:  $out_live </td> 
			<tr><td id=icon16><img src='/images/icons/blackberry_16.png' /></td><td style=text-align:left>Chamadas em IVR:  $in_ivr </td> 
			</table></div>

			</td>
			</tr>
			</table>

</td>
</tr>
</table>

";	
		
	
		echo "$Cecho";
	
		echo "$Aecho";
	}
	else
	{
	echo "<center><br><br> Neste momento não se encontram Colaboradores online. </center>";
	echo "$Cecho";
	}








if ($RTajax < 1)
	{
	echo "</TD></TR></TABLE>";
	}
?>



