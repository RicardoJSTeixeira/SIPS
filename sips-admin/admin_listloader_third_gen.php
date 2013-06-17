<?php
# admin_listloader_third_gen.php - version 2.4
#  (based upon - new_listloader_superL.php script)
# 
# Copyright (C) 2011  Matt Florell,Joe Johnson <vicidial@gmail.com>    LICENSE: AGPLv2
#
# ViciDial web-based lead loader from formatted file
# 
# CHANGES
# 50602-1640 - First version created by Joe Johnson
# 51128-1108 - Removed PHP global vars requirement
# 60113-1603 - Fixed a few bugs in Excel import
# 60421-1624 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60616-1240 - added listID override
# 60616-1604 - added gmt lookup for each lead
# 60619-1651 - Added variable filtering to eliminate SQL injection attack threat
# 60822-1121 - fixed for nonwritable directories
# 60906-1100 - added filter of non-digits in alt_phone field
# 61110-1222 - added new USA-Canada DST scheme and Brazil DST scheme
# 61128-1149 - added postal code GMT lookup and duplicate check options
# 70417-1059 - Fixed default phone_code bug
# 70510-1518 - Added campaign and system duplicate check and phonecode override
# 80428-0417 - UTF8 changes
# 80514-1030 - removed filesize limit and raised number of errors to be displayed
# 80713-0023 - added last_local_call_time field default of 2008-01-01
# 81011-2009 - a few bug fixes
# 90309-1831 - Added admin_log logging
# 90310-2128 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 90522-0506 - Security fix
# 90721-1339 - Added rank and owner as vicidial_list fields
# 91112-0616 - Added title/alt-phone duplicate checking
# 100118-0543 - Added new Australian and New Zealand DST schemes (FSO-FSA and LSS-FSA)
# 100621-1026 - Added admin_web_directory variable
# 100630-1609 - Added a check for invalid ListIds and filtered out ' " ; ` \ from the field <mikec>
# 100705-1507 - Added custom fields to field chooser, only when liast_id_override is used and only with TXT and CSV file formats
# 100706-1250 - Forked script to create new script that will only load TXT(tab-
#				delimited files) and use a perl script to convert others to TXT
# 100707-1040 - Converted List Id Override and Phone Code Override to drop downs <mikec>
# 100707-1156 - Made it so you cannot submit with no lead file selected. Also fixed Start Over Link <mikec>
# 100712-1416 - Added entry_list_id field to vicidial_list to preserve link to custom fields if any
# 100728-0900 - Filtered uploaded filenames for unsupported characters
# 110424-0926 - Added option for time zone code in the owner field
#

$version = '2.4-40';
$build = '110424-0926';


require("dbconnect.php");


$US='_';

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
$leadfile=$_FILES["leadfile"];
	$LF_orig = $_FILES['leadfile']['name'];
	$LF_path = $_FILES['leadfile']['tmp_name'];
if (isset($_GET["submit_file"]))			{$submit_file=$_GET["submit_file"];}
	elseif (isset($_POST["submit_file"]))           {$submit_file=$_POST["submit_file"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))                {$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))                {$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["leadfile_name"]))			{$leadfile_name=$_GET["leadfile_name"];}
	elseif (isset($_POST["leadfile_name"]))         {$leadfile_name=$_POST["leadfile_name"];}
if (isset($_FILES["leadfile"]))				{$leadfile_name=$_FILES["leadfile"]['name'];}
if (isset($_GET["file_layout"]))			{$file_layout=$_GET["file_layout"];}
	elseif (isset($_POST["file_layout"]))		{$file_layout=$_POST["file_layout"];}
if (isset($_GET["OK_to_process"]))			{$OK_to_process=$_GET["OK_to_process"];}
	elseif (isset($_POST["OK_to_process"]))		{$OK_to_process=$_POST["OK_to_process"];}
if (isset($_GET["vendor_lead_code_field"]))		{$vendor_lead_code_field=$_GET["vendor_lead_code_field"];}
	elseif (isset($_POST["vendor_lead_code_field"])){$vendor_lead_code_field=$_POST["vendor_lead_code_field"];}
if (isset($_GET["source_id_field"]))			{$source_id_field=$_GET["source_id_field"];}
	elseif (isset($_POST["source_id_field"]))	{$source_id_field=$_POST["source_id_field"];}
if (isset($_GET["list_id_field"]))			{$list_id_field=$_GET["list_id_field"];}
	elseif (isset($_POST["list_id_field"]))		{$list_id_field=$_POST["list_id_field"];}
if (isset($_GET["phone_code_field"]))			{$phone_code_field=$_GET["phone_code_field"];}
	elseif (isset($_POST["phone_code_field"]))	{$phone_code_field=$_POST["phone_code_field"];}
if (isset($_GET["phone_number_field"]))			{$phone_number_field=$_GET["phone_number_field"];}
	elseif (isset($_POST["phone_number_field"]))	{$phone_number_field=$_POST["phone_number_field"];}
if (isset($_GET["title_field"]))			{$title_field=$_GET["title_field"];}
	elseif (isset($_POST["title_field"]))		{$title_field=$_POST["title_field"];}
if (isset($_GET["first_name_field"]))			{$first_name_field=$_GET["first_name_field"];}
	elseif (isset($_POST["first_name_field"]))	{$first_name_field=$_POST["first_name_field"];}
if (isset($_GET["middle_initial_field"]))		{$middle_initial_field=$_GET["middle_initial_field"];}
	elseif (isset($_POST["middle_initial_field"]))	{$middle_initial_field=$_POST["middle_initial_field"];}
if (isset($_GET["last_name_field"]))			{$last_name_field=$_GET["last_name_field"];}
	elseif (isset($_POST["last_name_field"]))	{$last_name_field=$_POST["last_name_field"];}
if (isset($_GET["address1_field"]))			{$address1_field=$_GET["address1_field"];}
	elseif (isset($_POST["address1_field"]))	{$address1_field=$_POST["address1_field"];}
if (isset($_GET["address2_field"]))			{$address2_field=$_GET["address2_field"];}
	elseif (isset($_POST["address2_field"]))	{$address2_field=$_POST["address2_field"];}
if (isset($_GET["address3_field"]))			{$address3_field=$_GET["address3_field"];}
	elseif (isset($_POST["address3_field"]))	{$address3_field=$_POST["address3_field"];}
if (isset($_GET["city_field"]))				{$city_field=$_GET["city_field"];}
	elseif (isset($_POST["city_field"]))		{$city_field=$_POST["city_field"];}
if (isset($_GET["state_field"]))			{$state_field=$_GET["state_field"];}
	elseif (isset($_POST["state_field"]))		{$state_field=$_POST["state_field"];}
if (isset($_GET["province_field"]))			{$province_field=$_GET["province_field"];}
	elseif (isset($_POST["province_field"]))	{$province_field=$_POST["province_field"];}
if (isset($_GET["postal_code_field"]))			{$postal_code_field=$_GET["postal_code_field"];}
	elseif (isset($_POST["postal_code_field"]))	{$postal_code_field=$_POST["postal_code_field"];}
if (isset($_GET["country_code_field"]))			{$country_code_field=$_GET["country_code_field"];}
	elseif (isset($_POST["country_code_field"]))	{$country_code_field=$_POST["country_code_field"];}
if (isset($_GET["gender_field"]))			{$gender_field=$_GET["gender_field"];}
	elseif (isset($_POST["gender_field"]))		{$gender_field=$_POST["gender_field"];}
if (isset($_GET["date_of_birth_field"]))		{$date_of_birth_field=$_GET["date_of_birth_field"];}
	elseif (isset($_POST["date_of_birth_field"]))	{$date_of_birth_field=$_POST["date_of_birth_field"];}
if (isset($_GET["alt_phone_field"]))			{$alt_phone_field=$_GET["alt_phone_field"];}
	elseif (isset($_POST["alt_phone_field"]))	{$alt_phone_field=$_POST["alt_phone_field"];}
if (isset($_GET["email_field"]))			{$email_field=$_GET["email_field"];}
	elseif (isset($_POST["email_field"]))		{$email_field=$_POST["email_field"];}
if (isset($_GET["security_phrase_field"]))		{$security_phrase_field=$_GET["security_phrase_field"];}
	elseif (isset($_POST["security_phrase_field"]))	{$security_phrase_field=$_POST["security_phrase_field"];}
if (isset($_GET["comments_field"]))			{$comments_field=$_GET["comments_field"];}
	elseif (isset($_POST["comments_field"]))	{$comments_field=$_POST["comments_field"];}
if (isset($_GET["rank_field"]))				{$rank_field=$_GET["rank_field"];}
	elseif (isset($_POST["rank_field"]))		{$rank_field=$_POST["rank_field"];}
if (isset($_GET["owner_field"]))			{$owner_field=$_GET["owner_field"];}
	elseif (isset($_POST["owner_field"]))		{$owner_field=$_POST["owner_field"];}
if (isset($_GET["list_id_override"]))			{$list_id_override=$_GET["list_id_override"];}
	elseif (isset($_POST["list_id_override"]))	{$list_id_override=$_POST["list_id_override"];}
	$list_id_override = (preg_replace("/\D/","",$list_id_override));
if (isset($_GET["lead_file"]))				{$lead_file=$_GET["lead_file"];}
	elseif (isset($_POST["lead_file"]))		{$lead_file=$_POST["lead_file"];}
if (isset($_GET["dupcheck"]))				{$dupcheck=$_GET["dupcheck"];}
	elseif (isset($_POST["dupcheck"]))		{$dupcheck=$_POST["dupcheck"];}
if (isset($_GET["postalgmt"]))				{$postalgmt=$_GET["postalgmt"];}
	elseif (isset($_POST["postalgmt"]))		{$postalgmt=$_POST["postalgmt"];}
if (isset($_GET["phone_code_override"]))		{$phone_code_override=$_GET["phone_code_override"];}
	elseif (isset($_POST["phone_code_override"]))	{$phone_code_override=$_POST["phone_code_override"];}
	$phone_code_override = (preg_replace("/\D/","",$phone_code_override));
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
	
if (isset($_GET["extra1_field"]))			{$extra1_field=$_GET["extra1_field"];}
	elseif (isset($_POST["extra1_field"]))		{$extra1_field=$_POST["extra1_field"];}
	
if (isset($_GET["extra2_field"]))			{$extra2_field=$_GET["extra2_field"];}
	elseif (isset($_POST["extra2_field"]))		{$extra2_field=$_POST["extra2_field"];}

if (isset($_GET["extra3_field"]))			{$extra3_field=$_GET["extra3_field"];}
	elseif (isset($_POST["extra3_field"]))		{$extra3_field=$_POST["extra3_field"];}
	
if (isset($_GET["extra4_field"]))			{$extra4_field=$_GET["extra4_field"];}
	elseif (isset($_POST["extra4_field"]))		{$extra4_field=$_POST["extra4_field"];}
	
if (isset($_GET["extra5_field"]))			{$extra5_field=$_GET["extra5_field"];}
	elseif (isset($_POST["extra5_field"]))		{$extra5_field=$_POST["extra5_field"];}

if (isset($_GET["extra6_field"]))			{$extra6_field=$_GET["extra6_field"];}
	elseif (isset($_POST["extra6_field"]))		{$extra6_field=$_POST["extra6_field"];}	
	
if (isset($_GET["extra7_field"]))			{$extra7_field=$_GET["extra7_field"];}
	elseif (isset($_POST["extra7_field"]))		{$extra7_field=$_POST["extra7_field"];}

if (isset($_GET["extra8_field"]))			{$extra8_field=$_GET["extra8_field"];}
	elseif (isset($_POST["extra8_field"]))		{$extra8_field=$_POST["extra8_field"];}
	
if (isset($_GET["extra9_field"]))			{$extra9_field=$_GET["extra9_field"];}
	elseif (isset($_POST["extra9_field"]))		{$extra9_field=$_POST["extra9_field"];}
	
if (isset($_GET["extra10_field"]))			{$extra10_field=$_GET["extra10_field"];}
	elseif (isset($_POST["extra10_field"]))		{$extra10_field=$_POST["extra10_field"];}
	
if (isset($_GET["extra11_field"]))			{$extra11_field=$_GET["extra11_field"];}
	elseif (isset($_POST["extra11_field"]))		{$extra11_field=$_POST["extra11_field"];}
	
if (isset($_GET["extra12_field"]))			{$extra12_field=$_GET["extra12_field"];}
	elseif (isset($_POST["extra12_field"]))		{$extra12_field=$_POST["extra12_field"];}
	
if (isset($_GET["extra13_field"]))			{$extra13_field=$_GET["extra13_field"];}
	elseif (isset($_POST["extra13_field"]))		{$extra13_field=$_POST["extra13_field"];}
	
if (isset($_GET["extra14_field"]))			{$extra14_field=$_GET["extra14_field"];}
	elseif (isset($_POST["extra14_field"]))		{$extra14_field=$_POST["extra14_field"];}
	
if (isset($_GET["extra15_field"]))			{$extra15_field=$_GET["extra15_field"];}
	elseif (isset($_POST["extra15_field"]))		{$extra15_field=$_POST["extra15_field"];}
	
# if the didnt select an over ride wipe out in_file
if ( $list_id_override == "in_file" ) { $list_id_override = ""; }
if ( $phone_code_override == "in_file" ) { $phone_code_override = ""; }

# $country_field=$_GET["country_field"];					if (!$country_field) {$country_field=$_POST["country_field"];}

### REGEX to prevent weird characters from ending up in the fields
$field_regx = "['\"`\\;]";

$vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|entry_list_id|extra1|extra2|extra3|extra4|extra5|extra6|extra7|extra8|extra9|extra10|extra11|extra12|extra13|extra14|extra15|';

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,admin_web_directory,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
		
		
		
	$row=mysql_fetch_row($rslt);
	$non_latin =				1;
	$admin_web_directory =		$row[1];
	$custom_fields_enabled =	$row[2];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
	$list_id_override = ereg_replace("[^0-9]","",$list_id_override);
	}
else
	{
	$PHP_AUTH_PW = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_PW);
	$PHP_AUTH_USER = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_USER);
	}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_datetime = $STARTtime;

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7;";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0) {$fp = fopen ("./project_auth_entries.txt", "a");}
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
	header ("Content-type: text/html; charset=utf-8");
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT load_leads from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGload_leads				=$row[0];

		if ($LOGload_leads < 1)
			{
			echo "Não tem permissão para carregar contactos.";
			exit;
			}
		if ($WeBRooTWritablE > 0) 
			{
			fwrite ($fp, "LIST_LOAD|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0) 
			{
			fwrite ($fp, "LIST_LOAD|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}


$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
	else {$HTTPprotocol = 'http://';}
$admDIR = "$HTTPprotocol$server_name$script_name";
$admDIR = eregi_replace('admin_listloader_third_gen.php','',$admDIR);
$admSCR = 'admin.php';
$NWB = "<a href=\"javascript:openNewWindow('$admDIR$admSCR?ADD=99999";
$NWE = "')\"><IMG SRC=\"../images/icons/info_rhombus.png\" BORDER=0 ALT=\"HELP\"  WIDTH=28 HEIGHT=28></A>";

$secX = date("U");
$hour = date("H");
$min = date("i");
$sec = date("s");
$mon = date("m");
$mday = date("d");
$year = date("Y");
$isdst = date("I");
$Shour = date("H");
$Smin = date("i");
$Ssec = date("s");
$Smon = date("m");
$Smday = date("d");
$Syear = date("Y");
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$inSD = $pulldate0;
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

### Grab Server GMT value from the database
$stmt="SELECT local_gmt FROM servers where server_ip = '$server_ip';";
$rslt=mysql_query($stmt, $link);
$gmt_recs = mysql_num_rows($rslt);
if ($gmt_recs > 0)
	{
	$row=mysql_fetch_row($rslt);
	$DBSERVER_GMT		=		"$row[0]";
	if (strlen($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
	if ($isdst) {$SERVER_GMT++;} 
	}
else
	{
	$SERVER_GMT = date("O");
	$SERVER_GMT = eregi_replace("\+","",$SERVER_GMT);
	$SERVER_GMT = ($SERVER_GMT + 0);
	$SERVER_GMT = ($SERVER_GMT / 100);
	}

$LOCAL_GMT_OFF = $SERVER_GMT;
$LOCAL_GMT_OFF_STD = $SERVER_GMT;

#if ($DB) {print "SEED TIME  $secX      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n";}


echo "<html>\n";
echo "<head>\n";
echo "<!-- VERSION: $version     BUILD: $build -->\n";
echo "<!-- SEED TIME  $secX:   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF  DST: $isdst -->\n";


?>


<script language="JavaScript1.2">
function openNewWindow(url) 
	{
	window.open (url,"",'width=700,height=300,scrollbars=yes,menubar=yes,address=yes');
	}
function ShowProgress(good, bad, total, dup, post) 
	{
	parent.lead_count.document.open();
	parent.lead_count.document.write('<html><body><table border=0 width=200 cellpadding=10 cellspacing=0 align=center valign=top><tr bgcolor="#000000"><th colspan=2><font face="arial, helvetica" size=3 color=white>Current file status:</font></th></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Good:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+good+'</B></font></td></tr><tr bgcolor="#990000"><td align=right><font face="arial, helvetica" size=2 color=white><B>Bad:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+bad+'</B></font></td></tr><tr bgcolor="#000099"><td align=right><font face="arial, helvetica" size=2 color=white><B>Total:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+total+'</B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B> &nbsp; </B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B> &nbsp; </B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Duplicate:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+dup+'</B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Postal Match:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+post+'</B></font></td></tr></table><body></html>');
	parent.lead_count.document.close();
	}
function ParseFileName() 
	{
	if (!document.forms[0].OK_to_process) 
		{	
		var endstr=document.forms[0].leadfile.value.lastIndexOf('\\');
		if (endstr>-1) 
			{
			endstr++;
			var filename=document.forms[0].leadfile.value.substring(endstr);
			document.forms[0].leadfile_name.value=filename;
			}
		}
	}

</script>
<title>ADMINISTRATION: Lead Loader</title>
<link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
<BODY>

<?php
$short_header=1;

require("admin_header.php");


if ( (!$OK_to_process) or ( ($leadfile) and ($file_layout!="standard") ) )
	{
	?>
	
	<div class=cc-mstyle>
	<table>
	<tr>
	<td id='icon32'><img src='../images/icons/database_save_32.png' /></td>
	<td id='submenu-title'> Carregar Contactos </td>
	<td style='text-align:left'> Permite o Upload de Novos Contactos. </td>
	</tr>
	</table>
	</div>

	<div id=work-area>
	<br><br>
	
	
	<form action="<?=$PHP_SELF?>" method="post" onSubmit="ParseFileName()" enctype="multipart/form-data">
	<input type=hidden name='leadfile_name' value="<?=$leadfile_name ?>">
	<input type=hidden name='DB' value="<?= $DB ?>">
	<?php 
	if ($file_layout!="custom") 
		{
		?>
		<div class=cc-mstyle style='border:none; width:90%'>
		<table>
		<tr>
			<td style='min-width:225px'> <div class=cc-mstyle style='height:28px; '><p> Escolher Ficheiro  </p></div></td>
			<td><input type=file name="leadfile" id="leadfile" size=50 value="<?=$leadfile ?>"><td style='width:30px'><?php echo "$NWB#vicidial_list_loader$NWE"; ?></td>
		  </tr>
		  <tr>
			
			<td><div class=cc-mstyle style='height:28px; '><p> Acrescentar à Base de Dados </p></div></td>
			<td><select style='width:400px' name='list_id_override'>
			<?php
			
			$user_logado = $_SERVER['PHP_AUTH_USER'];
			$grupos = mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups INNER JOIN vicidial_users ON vicidial_user_groups.user_group = vicidial_users.user_group WHERE user = '$user_logado'",$link) or die(mysql_error());

	$row=mysql_fetch_row($grupos);
	$LOGallowed_campaigns = $row[0];
  
    
    $rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
  
  	$campanhas = "'".$rawLOGallowed_campaignsSQL."'";
        
			
			$stmt="SELECT list_id, list_name from vicidial_lists WHERE campaign_id IN ($campanhas) order by list_id;";
			$rslt=mysql_query($stmt, $link);
			$num_rows = mysql_num_rows($rslt);

			$count=0;
			while ( $num_rows > $count ) 
				{
				$row = mysql_fetch_row($rslt);
				echo "<option value=\'$row[0]\'>$row[0] - $row[1]</option>\n";
				$count++;
				}
			?>
			</select>
			</td>
		  </tr>
		  <tr>
			<td><div class=cc-mstyle style='height:28px; '><p> Cód. Telefone País </p></div></td>
			<td>
			<select style='width:400px' name='phone_code_override'>
                        <option value='in_file' selected='yes'>Selecione Cód. País</option>
			<?php
			$stmt="select distinct country_code, country from vicidial_phone_codes;";
			$rslt=mysql_query($stmt, $link);
			$num_rows = mysql_num_rows($rslt);
			
			$count=0;
	                while ( $num_rows > $count )
				{
				$row = mysql_fetch_row($rslt);
				echo "<option value='$row[0]'>$row[0] - $row[1]</option>\n";
				$count++;
				}
			?>
			</select>
			</td>
		  </tr>
		  <tr>
			<td><input type=hidden name="file_layout" value="standard"><input type=hidden name="file_layout" value="custom" checked></td>
		  </tr>
			<tr>
			<td><div class=cc-mstyle style='height:28px; '><p> Verificação de Duplicados  </p></div></td>
			<td><select style='width:400px' size=1 name=dupcheck>
			<option selected value="NONE">Sem Verificação</option>
			<option value="DUPLIST">Verificar números duplicados nesta Base de Dados</option>
			
			<option value="DUPCAMP">Verificar números duplicados em todas as Campanhas</option>
			<option value="DUPSYS">Verificar números duplicados no sistema todo</option>
			</select></td>
		  </tr>
	
		
<tr><td>&nbsp;</td></tr>
<tr><td colspan=2 ></td><td><input type=image name='submit_file' style='float:right' src='../images/icons/shape_square_add.png' alt=Gravar name=SUBMIT></td></tr>



		</table>
		<?php } } else { ?>
	
	
	
	<div class=cc-mstyle>
	<table>
	<tr>
	<td id='icon32'><img src='../images/icons/database_save_32.png' /></td>
	<td id='submenu-title'> Carregar Contactos </td>
	<td style='text-align:left'> Permite o Upload de Novos Contactos. </td>
	</tr>
	</table>
	</div>
	
	<?php
	}



##### BEGIN custom fields submission #####
if ($OK_to_process) 
	{
	print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true;document.forms[0].list_id_override.disabled=true;document.forms[0].phone_code_override.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
	flush();
	$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';

	$file=fopen("$lead_file", "r");
	if ($WeBRooTWritablE > 0)
		{
		$stmt_file=fopen("listloader_stmts.txt", "w");
		}
	$buffer=fgets($file, 4096);
	$tab_count=substr_count($buffer, "\t");
	$pipe_count=substr_count($buffer, "|");

	if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
	$field_check=explode($delimiter, $buffer);

	if (count($field_check)>=2) 
		{
		flush();
		$file=fopen("$lead_file", "r");
		print "<center><font face='arial, helvetica' size=3 color='#009900'><B>\n"; 

		if ($custom_fields_enabled > 0)
			{
			$tablecount_to_print=0;
			$fieldscount_to_print=0;
			$fields_to_print=0;

			$stmt="SHOW TABLES LIKE \"custom_$list_id_override\";";
			if ($DB>0) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$tablecount_to_print = mysql_num_rows($rslt);

			if ($tablecount_to_print > 0) 
				{
				$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id_override';";
				if ($DB>0) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$fieldscount_to_print = mysql_num_rows($rslt);

				if ($fieldscount_to_print > 0) 
					{
					$stmt="SELECT field_label,field_type from vicidial_lists_fields where list_id='$list_id_override' order by field_rank,field_order,field_label;";
					if ($DB>0) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					$fields_to_print = mysql_num_rows($rslt);
					$fields_list='';
					$o=0;
					while ($fields_to_print > $o) 
						{
						$rowx=mysql_fetch_row($rslt);
						$A_field_label[$o] =	$rowx[0];
						$A_field_type[$o] =		$rowx[1];
						$A_field_value[$o] =	'';
						$o++;
						}
					}
				}
			}

		while (!feof($file)) 
			{
				
			$record++;
			$buffer=rtrim(fgets($file, 4096));
			$buffer=stripslashes($buffer);
			
			if (strlen($buffer)>0) 
				{
				$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));

				$pulldate=date("Y-m-d H:i:s");
				$entry_date =			"$pulldate";
				$modify_date =			"";
				$status =				"NEW";
				$user ="";
				$vendor_lead_code =		$row[$vendor_lead_code_field];
				$source_code =			$row[$source_id_field];
				$source_id=$source_code;
				$list_id =				$row[$list_id_field];
				$gmt_offset =			'0';
				$called_since_last_reset='N';
				$phone_code =			$row[$phone_code_field];
				$phone_number =			eregi_replace("[^0-9]", "", $row[$phone_number_field]);
				$title =				$row[$title_field];
				$first_name =			$row[$first_name_field];
				$middle_initial =		$row[$middle_initial_field];
				$last_name =			$row[$last_name_field];
				$address1 =				$row[$address1_field];
				$address2 =				$row[$address2_field];
				$address3 =				$row[$address3_field];
				$city =					$row[$city_field];
				$state =				$row[$state_field];
				$province =				$row[$province_field];
				$postal_code =			$row[$postal_code_field];
				$country_code =			$row[$country_code_field];
				$gender =				$row[$gender_field];
				$date_of_birth =		$row[$date_of_birth_field];
				$alt_phone =			eregi_replace("[^0-9]", "", $row[$alt_phone_field]);
				$email =				$row[$email_field];
				$security_phrase =		$row[$security_phrase_field];
				$comments =				trim($row[$comments_field]);
				$rank =					$row[$rank_field];
				$owner =				$row[$owner_field];
				$extra1 = 				$row[$extra1_field];
				$extra2 = 				$row[$extra2_field];
				$extra3 = 				$row[$extra3_field];
				$extra4 = 				$row[$extra4_field];
				$extra5 = 				$row[$extra5_field];
				$extra6 = 				$row[$extra6_field];
				$extra7 = 				$row[$extra7_field];
				$extra8 = 				$row[$extra8_field];
				$extra9 = 				$row[$extra9_field];
				$extra10 = 				$row[$extra10_field];
				$extra11 = 				$row[$extra11_field];
				$extra12 = 				$row[$extra12_field];
				$extra13 = 				$row[$extra13_field];
				$extra14 = 				$row[$extra14_field];
				$extra15 = 				$row[$extra15_field];
				
				# replace ' " ` \ ; with nothing
				$vendor_lead_code =		eregi_replace($field_regx, "", $vendor_lead_code);
				$source_code =			eregi_replace($field_regx, "", $source_code);
				$source_id = 			eregi_replace($field_regx, "", $source_id);
				$list_id =				eregi_replace($field_regx, "", $list_id);
				$phone_code =			eregi_replace($field_regx, "", $phone_code);
				$phone_number =			eregi_replace($field_regx, "", $phone_number);
				$title =				eregi_replace($field_regx, "", $title);
				$first_name =			eregi_replace($field_regx, "", $first_name);
				$middle_initial =		eregi_replace($field_regx, "", $middle_initial);
				$last_name =			eregi_replace($field_regx, "", $last_name);
				$address1 =				eregi_replace($field_regx, "", $address1);
				$address2 =				eregi_replace($field_regx, "", $address2);
				$address3 =				eregi_replace($field_regx, "", $address3);
				$city =					eregi_replace($field_regx, "", $city);
				$state =				eregi_replace($field_regx, "", $state);
				$province =				eregi_replace($field_regx, "", $province);
				$postal_code =			eregi_replace($field_regx, "", $postal_code);
				$country_code =			eregi_replace($field_regx, "", $country_code);
				$gender =				eregi_replace($field_regx, "", $gender);
				$date_of_birth =		eregi_replace($field_regx, "", $date_of_birth);
				$alt_phone =			eregi_replace($field_regx, "", $alt_phone);
				$email =				eregi_replace($field_regx, "", $email);
				$security_phrase =		eregi_replace($field_regx, "", $security_phrase);
				$comments =				eregi_replace($field_regx, "", $comments);
				$rank =					eregi_replace($field_regx, "", $rank);
				$owner =				eregi_replace($field_regx, "", $owner);
				$extra1 = 				eregi_replace($field_regx, "", $extra1);
				$extra2 = 				eregi_replace($field_regx, "", $extra2);
				$extra3 = 				eregi_replace($field_regx, "", $extra3);
				$extra4 = 				eregi_replace($field_regx, "", $extra4);
				$extra5 = 				eregi_replace($field_regx, "", $extra5);
				$extra6 = 				eregi_replace($field_regx, "", $extra6);
				$extra7 = 				eregi_replace($field_regx, "", $extra7);
				$extra8 = 				eregi_replace($field_regx, "", $extra8);
				$extra9 = 				eregi_replace($field_regx, "", $extra9);
				$extra10 = 				eregi_replace($field_regx, "", $extra10);
				$extra11 = 				eregi_replace($field_regx, "", $extra11);
				$extra12 = 				eregi_replace($field_regx, "", $extra12);
				$extra13 = 				eregi_replace($field_regx, "", $extra13);
				$extra14 = 				eregi_replace($field_regx, "", $extra14);
				$extra15 = 				eregi_replace($field_regx, "", $extra15);
				
				$USarea = 			substr($phone_number, 0, 3);

				if (strlen($list_id_override)>0) 
					{
					$list_id = $list_id_override;
					}
				if (strlen($phone_code_override)>0) 
					{
					$phone_code = $phone_code_override;
					}

				##### BEGIN custom fields columns list ###
				$custom_SQL='';
				if ($custom_fields_enabled > 0)
					{
					if ($tablecount_to_print > 0) 
						{
						if ($fieldscount_to_print > 0)
							{
							$o=0;
							while ($fields_to_print > $o) 
								{
								$A_field_value[$o] =	'';
								$field_name_id = $A_field_label[$o] . "_field";

								if ( ($A_field_type[$o]!='DISPLAY') and ($A_field_type[$o]!='SCRIPT') )
									{
									if (!preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
										{
										if (isset($_GET["$field_name_id"]))				{$form_field_value=$_GET["$field_name_id"];}
											elseif (isset($_POST["$field_name_id"]))	{$form_field_value=$_POST["$field_name_id"];}

										if ($form_field_value >= 0)
											{
											$A_field_value[$o] =	$row[$form_field_value];
											# replace ' " ` \ ; with nothing
											$A_field_value[$o] =	eregi_replace($field_regx, "", $A_field_value[$o]);

											$custom_SQL .= "$A_field_label[$o]='$A_field_value[$o]',";
											}
										}
									}
								$o++;
								}
							}
						}
					}
				##### END custom fields columns list ###

				$custom_SQL = preg_replace("/,$/","",$custom_SQL);


				##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
				if (eregi("DUPCAMP",$dupcheck))
					{
					$dup_lead=0;
					$dup_lists='';
					$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
					$rslt=mysql_query($stmt, $link);
					$ci_recs = mysql_num_rows($rslt);
					if ($ci_recs > 0)
						{
						$row=mysql_fetch_row($rslt);
						$dup_camp =			$row[0];

						$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
						$rslt=mysql_query($stmt, $link);
						$li_recs = mysql_num_rows($rslt);
						if ($li_recs > 0)
							{
							$L=0;
							while ($li_recs > $L)
								{
								$row=mysql_fetch_row($rslt);
								$dup_lists .=	"'$row[0]',";
								$L++;
								}
							$dup_lists = eregi_replace(",$",'',$dup_lists);

							$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
							$rslt=mysql_query($stmt, $link);
							$pc_recs = mysql_num_rows($rslt);
							if ($pc_recs > 0)
								{
								$dup_lead=1;
								$row=mysql_fetch_row($rslt);
								$dup_lead_list =	$row[0];
								}
							if ($dup_lead < 1)
								{
								if (eregi("$phone_number$US$list_id",$phone_list))
									{$dup_lead++; $dup++;}
								}
							}
						}
					}

				##### Check for duplicate phone numbers in vicidial_list table entire database #####
				if (eregi("DUPSYS",$dupcheck))
					{
					$dup_lead=0;
					$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
					$rslt=mysql_query($stmt, $link);
					$pc_recs = mysql_num_rows($rslt);
					if ($pc_recs > 0)
						{
						$dup_lead=1;
						$row=mysql_fetch_row($rslt);
						$dup_lead_list =	$row[0];
						}
					if ($dup_lead < 1)
						{
						if (eregi("$phone_number$US$list_id",$phone_list))
							{$dup_lead++; $dup++;}
						}
					}

				##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
				if (eregi("DUPLIST",$dupcheck))
					{
					$dup_lead=0;
					$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
					$rslt=mysql_query($stmt, $link);
					$pc_recs = mysql_num_rows($rslt);
					if ($pc_recs > 0)
						{
						$row=mysql_fetch_row($rslt);
						$dup_lead =			$row[0];
						$dup_lead_list =	$list_id;
						}
					if ($dup_lead < 1)
						{
						if (eregi("$phone_number$US$list_id",$phone_list))
							{$dup_lead++; $dup++;}
						}
					}

				##### Check for duplicate title and alt-phone in vicidial_list table for one list_id #####
				if (eregi("DUPTITLEALTPHONELIST",$dupcheck))
					{
					$dup_lead=0;
					$stmt="select count(*) from vicidial_list where title='$title' and alt_phone='$alt_phone' and list_id='$list_id';";
					$rslt=mysql_query($stmt, $link);
					$pc_recs = mysql_num_rows($rslt);
					if ($pc_recs > 0)
						{
						$row=mysql_fetch_row($rslt);
						$dup_lead =			$row[0];
						$dup_lead_list =	$list_id;
						}
					if ($dup_lead < 1)
						{
						if (eregi("$alt_phone$title$US$list_id",$phone_list))
							{$dup_lead++; $dup++;}
						}
					}

				##### Check for duplicate phone numbers in vicidial_list table entire database #####
				if (eregi("DUPTITLEALTPHONESYS",$dupcheck))
					{
					$dup_lead=0;
					$stmt="select list_id from vicidial_list where title='$title' and alt_phone='$alt_phone';";
					$rslt=mysql_query($stmt, $link);
					$pc_recs = mysql_num_rows($rslt);
					if ($pc_recs > 0)
						{
						$dup_lead=1;
						$row=mysql_fetch_row($rslt);
						$dup_lead_list =	$row[0];
						}
					if ($dup_lead < 1)
						{
						if (eregi("$alt_phone$title$US$list_id",$phone_list))
							{$dup_lead++; $dup++;}
						}
					}


				if ( (strlen($phone_number)>6) and ($dup_lead<1) and ($list_id >= 100 ))
					{
					if (strlen($phone_code)<1) {$phone_code = '1';}

					if (eregi("TITLEALTPHONE",$dupcheck))
						{$phone_list .= "$alt_phone$title$US$list_id|";}
					else
						{$phone_list .= "$phone_number$US$list_id|";}

					$gmt_offset = 0;

					if (strlen($custom_SQL)>3)
						{
						$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id,extra1,extra2,extra3,extra4,extra5,extra6,extra7,extra8,extra9,extra10,extra11,extra12,extra13,extra14,extra15) values('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','$list_id', '$extra1', '$extra2', '$extra3', '$extra4', '$extra5', '$extra6', '$extra7', '$extra8', '$extra9', '$extra10', '$extra11', '$extra12', '$extra13', '$extra14', '$extra15');";
						$rslt=mysql_query($stmtZ, $link);
						$affected_rows = mysql_affected_rows($link);
						$lead_id = mysql_insert_id($link);
						if ($DB > 0) {echo "<!-- $affected_rows|$lead_id|$stmtZ -->";}
						if ($WeBRooTWritablE > 0) 
							{fwrite($stmt_file, $stmtZ."\r\n");}
						$multistmt='';

						$custom_SQL_query = "INSERT INTO custom_$list_id_override SET lead_id='$lead_id',$custom_SQL;";
						$rslt=mysql_query($custom_SQL_query, $link);
						$affected_rows = mysql_affected_rows($link);
						if ($DB > 0) {echo "<!-- $affected_rows|$custom_SQL_query -->";}
						}
					else
						{
						if ($multi_insert_counter > 8) 
							{
							### insert good record into vicidial_list table ###
							$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id,extra1,extra2,extra3,extra4,extra5,extra6,extra7,extra8,extra9,extra10,extra11,extra12,extra13,extra14,extra15) values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0', '$extra1', '$extra2', '$extra3', '$extra4', '$extra5', '$extra6', '$extra7', '$extra8', '$extra9', '$extra10', '$extra11', '$extra12', '$extra13', '$extra14', '$extra15');";
							$rslt=mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0) 
								{fwrite($stmt_file, $stmtZ."\r\n");}
							$multistmt='';
							$multi_insert_counter=0;
							}
						else
							{
							$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0', '$extra1', '$extra2', '$extra3', '$extra4', '$extra5', '$extra6', '$extra7', '$extra8', '$extra9', '$extra10', '$extra11', '$extra12', '$extra13', '$extra14', '$extra15'),";
							$multi_insert_counter++;
							}
						}
					$good++;
					}
				else
					{
					$bad++;
					}
				$total++;
				if ($total%100==0) 
					{
					print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
					usleep(1000);
					flush();
					}
				}
			}
		if ($multi_insert_counter!=0) 
			{
			$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id,extra1, extra2, extra3, extra4, extra5, extra6, extra7, extra8, extra9, extra10, extra11, extra12, extra13, extra14, extra15) values".substr($multistmt, 0, -1).";";
			mysql_query($stmtZ, $link);
			if ($WeBRooTWritablE > 0) 
				{fwrite($stmt_file, $stmtZ."\r\n");}
			}

		### LOG INSERTION Admin Log Table ###
		$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LISTS', event_type='LOAD', record_id='$list_id_override', event_code='ADMIN LOAD LIST CUSTOM', event_sql='', event_notes='File Name: $leadfile_name, GOOD: $good, BAD: $bad, TOTAL: $total';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		echo "<div id=work-area>";
	    echo "<br><br>";
		
		echo "<div class=cc-mstyle style='border:none; width:70%'>";
		#debug
		print "
		
		<table>
		<tr>
		<td style='min-width:225px'> <div class=cc-mstyle style='height:28px; '><p>Contactos Carregados com Sucesso </p></div></td>
		<td style='min-width:50px'> <div class=cc-mstyle style='height:28px; '><p> $good  </p></div></td>

		</tr>
		
		<tr>
		<td style='min-width:225px'> <div class=cc-mstyle style='height:28px; '><p> Contactos Carregados sem Sucesso  </p></div></td>
		<td style='min-width:50px'> <div class=cc-mstyle style='height:28px; '><p> $bad </p></div></td>

		</tr>
		
		<tr>
		<td style='min-width:225px'> <div class=cc-mstyle style='height:28px; '><p> Total de Contactos  </p></div></td>
		<td style='min-width:50px'> <div class=cc-mstyle style='height:28px; '><p> $total</p></div></td>

		</tr>
		
		<tr><td> $testing </td></tr>
		
		</table></div>";
		} 
	
	}
##### END custom fields submission #####



if (($leadfile) && ($LF_path))
	{
	$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';

	### LOG INSERTION Admin Log Table ###
	$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LISTS', event_type='LOAD', record_id='$list_id_override', event_code='ADMIN LOAD LIST', event_sql='', event_notes='File Name: $leadfile_name';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);



	##### BEGIN process standard file layout #####
	if ($file_layout=="standard") 
		{
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
		flush();


		$delim_set=0;
		# csv xls xlsx ods sxc conversion
		if (preg_match("/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$/i", $leadfile_name)) 
			{
			$leadfile_name = ereg_replace("[^-\.\_0-9a-zA-Z]","_",$leadfile_name);
			copy($LF_path, "/tmp/$leadfile_name");
			$new_filename = preg_replace("/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$/i", '.txt', $leadfile_name);
			$convert_command = "$WeBServeRRooT/$admin_web_directory/sheet2tab.pl /tmp/$leadfile_name /tmp/$new_filename";
			passthru("$convert_command");
			$lead_file = "/tmp/$new_filename";
			if ($DB > 0) {echo "|$convert_command|";}

			if (preg_match("/\.csv$/i", $leadfile_name)) {$delim_name="CSV: Comma Separated Values";}
			if (preg_match("/\.xls$/i", $leadfile_name)) {$delim_name="XLS: MS Excel 2000-XP";}
			if (preg_match("/\.xlsx$/i", $leadfile_name)) {$delim_name="XLSX: MS Excel 2007+";}
			if (preg_match("/\.ods$/i", $leadfile_name)) {$delim_name="ODS: OpenOffice.org OpenDocument Spreadsheet";}
			if (preg_match("/\.sxc$/i", $leadfile_name)) {$delim_name="SXC: OpenOffice.org First Spreadsheet";}
			$delim_set=1;
			}
		else
			{
			copy($LF_path, "/tmp/vicidial_temp_file.txt");
			$lead_file = "tmp/vicidial_temp_file.txt";
			}
		$file=fopen("$lead_file", "r");
		if ($WeBRooTWritablE > 0)
			{$stmt_file=fopen("$WeBServeRRooT/$admin_web_directory/listloader_stmts.txt", "w");}

		$buffer=fgets($file, 4096);
		$tab_count=substr_count($buffer, "\t");
		$pipe_count=substr_count($buffer, "|");

		if ($delim_set < 1)
			{
			if ($tab_count>$pipe_count)
				{$delim_name="tab-delimited";} 
			else 
				{$delim_name="pipe-delimited";}
			} 
		if ($tab_count>$pipe_count)
			{$delimiter="\t";}
		else 
			{$delimiter="|";}

		$field_check=explode($delimiter, $buffer);

		if (count($field_check)>=2) 
			{
			flush();
			$file=fopen("$lead_file", "r");
					
			$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
			print "<center><font face='arial, helvetica' size=3 color='#009900'><B>"; #Processing $delim_name file... ($tab_count|$pipe_count)\n
			
			while (!feof($file)) 
				{
				$record++; $testing++;
				$buffer=rtrim(fgets($file, 4096));
				$buffer=stripslashes($buffer);
				
			

				if (strlen($buffer)>0) 
					{
					$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));
					
					
					
					

					$pulldate=date("Y-m-d H:i:s");
					$entry_date =			"$pulldate";
					$modify_date =			"";
					$status =				"NEW";
					$user ="";
					$vendor_lead_code =		$row[0];
					$source_code =			$row[1];
					$source_id=$source_code;
					$list_id =				$row[2];
					$gmt_offset =			'0';
					$called_since_last_reset='N';
					$phone_code =			eregi_replace("[^0-9]", "", $row[3]);
					$phone_number =			eregi_replace("[^0-9]", "", $row[4]);
					$title =				$row[5];
					$first_name =			$row[6];
					$middle_initial =		$row[7];
					$last_name =			$row[8];
					$address1 =				$row[9];
					$address2 =				$row[10];
					$address3 =				$row[11];
					$city =$row[12];
					$state =				$row[13];
					$province =				$row[14];
					$postal_code =			$row[15];
					$country_code =			$row[16];
					$gender =				$row[17];
					$date_of_birth =		$row[18];
					$alt_phone =			eregi_replace("[^0-9]", "", $row[19]);
					$email =				$row[20];
					$security_phrase =		$row[21];
					$comments =				trim($row[22]);
					$rank =					$row[23];
					$owner =				$row[24];
					$extra1 = 				$row[25];
					$extra2 = 				$row[26];
					$extra3 = 				$row[27];
					$extra4 = 				$row[28];
					$extra5 = 				$row[29];
					$extra6 = 				$row[30];
					$extra7 = 				$row[31];
					$extra8 = 				$row[32];
					$extra9 = 				$row[33];
					$extra10 = 				$row[34];
					$extra11 = 				$row[35];
					$extra12 = 				$row[36];
					$extra13 = 				$row[37];
					$extra14 = 				$row[38];
					$extra15 = 				$row[39];
						
					# replace ' " ` \ ; with nothing
					$vendor_lead_code =		eregi_replace($field_regx, "", $vendor_lead_code);
					$source_code =			eregi_replace($field_regx, "", $source_code);
					$source_id = 			eregi_replace($field_regx, "", $source_id);
					$list_id =				eregi_replace($field_regx, "", $list_id);
					$phone_code =			eregi_replace($field_regx, "", $phone_code);
					$phone_number =			eregi_replace($field_regx, "", $phone_number);
					$title =				eregi_replace($field_regx, "", $title);
					$first_name =			eregi_replace($field_regx, "", $first_name);
					$middle_initial =		eregi_replace($field_regx, "", $middle_initial);
					$last_name =			eregi_replace($field_regx, "", $last_name);
					$address1 =				eregi_replace($field_regx, "", $address1);
					$address2 =				eregi_replace($field_regx, "", $address2);
					$address3 =				eregi_replace($field_regx, "", $address3);
					$city =					eregi_replace($field_regx, "", $city);
					$state =				eregi_replace($field_regx, "", $state);
					$province =				eregi_replace($field_regx, "", $province);
					$postal_code =			eregi_replace($field_regx, "", $postal_code);
					$country_code =			eregi_replace($field_regx, "", $country_code);
					$gender =				eregi_replace($field_regx, "", $gender);
					$date_of_birth =		eregi_replace($field_regx, "", $date_of_birth);
					$alt_phone =			eregi_replace($field_regx, "", $alt_phone);
					$email =				eregi_replace($field_regx, "", $email);
					$security_phrase =		eregi_replace($field_regx, "", $security_phrase);
					$comments =				eregi_replace($field_regx, "", $comments);
					$rank =					eregi_replace($field_regx, "", $rank);
					$owner =				eregi_replace($field_regx, "", $owner);
					$extra1 =				eregi_replace($field_regx, "", $extra1);
					$extra2 =				eregi_replace($field_regx, "", $extra2);
					$extra3 =				eregi_replace($field_regx, "", $extra3);
					$extra4 =				eregi_replace($field_regx, "", $extra4);
					$extra5 =				eregi_replace($field_regx, "", $extra5);
					$extra6 =				eregi_replace($field_regx, "", $extra6);
					$extra7 =				eregi_replace($field_regx, "", $extra7);
					$extra8 =				eregi_replace($field_regx, "", $extra8);
					$extra9 =				eregi_replace($field_regx, "", $extra9);
					$extra10 =				eregi_replace($field_regx, "", $extra10);
					$extra11 =				eregi_replace($field_regx, "", $extra11);
					$extra12 =				eregi_replace($field_regx, "", $extra12);
					$extra13 =				eregi_replace($field_regx, "", $extra13);
					$extra14 =				eregi_replace($field_regx, "", $extra14);
					$extra15 =				eregi_replace($field_regx, "", $extra15);
					
					
					$USarea = 			substr($phone_number, 0, 3);

					if (strlen($list_id_override)>0) 
						{
						$list_id = $list_id_override;
						}
					if (strlen($phone_code_override)>0) 
						{
						$phone_code = $phone_code_override;
						}

					##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
					if (eregi("DUPCAMP",$dupcheck))
						{
							$dup_lead=0;
							$dup_lists='';
						$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$ci_recs = mysql_num_rows($rslt);
						if ($ci_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_camp =			$row[0];

							$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
							$rslt=mysql_query($stmt, $link);
							$li_recs = mysql_num_rows($rslt);
							if ($li_recs > 0)
								{
								$L=0;
								while ($li_recs > $L)
									{
									$row=mysql_fetch_row($rslt);
									$dup_lists .=	"'$row[0]',";
									$L++;
									}
								$dup_lists = eregi_replace(",$",'',$dup_lists);

								$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPSYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
					if (eregi("DUPLIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate title and alt-phone in vicidial_list table for one list_id #####
					if (eregi("DUPTITLEALTPHONELIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where title='$title' and alt_phone='$alt_phone' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							$dup_lead_list =	$list_id;
							}
						if ($dup_lead < 1)
							{
							if (eregi("$alt_phone$title$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPTITLEALTPHONESYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where title='$title' and alt_phone='$alt_phone';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$alt_phone$title$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					if ( (strlen($phone_number)>6) and ($dup_lead<1) and ($list_id >= 100 ))
						{
						if (strlen($phone_code)<1) {$phone_code = '1';}

						if (eregi("TITLEALTPHONE",$dupcheck))
							{$phone_list .= "$alt_phone$title$US$list_id|";}
						else
							{$phone_list .= "$phone_number$US$list_id|";}

						$gmt_offset = 0;

						if ($multi_insert_counter > 8) 
							{
							### insert good deal into pending_transactions table ###
							$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id,extra1,extra2,extra3,extra4,extra5,extra6,extra7,extra8,extra9,extra10,extra11,extra12,extra13,extra14,extra15) values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0', '$extra1', '$extra2', '$extra3', '$extra4', '$extra5', '$extra6', '$extra7', '$extra8', '$extra9', '$extra10', '$extra11', '$extra12', '$extra13', '$extra14', '$extra15');";
							$rslt=mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0) 
								{fwrite($stmt_file, $stmtZ."\r\n");}
							$multistmt='';
							$multi_insert_counter=0;
							} 
						else 
							{
							$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0', '$extra1', '$extra2', '$extra3', '$extra4', '$extra5', '$extra6', '$extra7', '$extra8', '$extra9', '$extra10', '$extra11', '$extra12', '$extra13', '$extra14', '$extra15'),";
							$multi_insert_counter++;
							}
						$good++;
						} 
					else
						{
						if ($bad < 1000000)
							{
							if ( $list_id < 100 )
								{
								print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| INVALID LIST ID</font><b>\n";
								}
							else
								{
								print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead  $dup_lead_list</font><b>\n";
								}
							}
						$bad++;
						}
					$total++;
					if ($total%100==0) 
						{
						print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
						usleep(1000);
						flush();
						}
					}
				}
			if ($multi_insert_counter!=0) 
				{
				$stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id,extra1, extra2, extra3, extra4, extra5, extra6, extra7, extra8, extra9, extra10, extra11, extra12, extra13, extra14, extra15) values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) 
					{fwrite($stmt_file, $stmtZ."\r\n");}
				} 
			### LOG INSERTION Admin Log Table ###
			$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LISTS', event_type='LOAD', record_id='$list_id_override', event_code='ADMIN LOAD LIST STANDARD', event_sql='', event_notes='File Name: $leadfile_name, GOOD: $good, BAD: $bad, TOTAL: $total';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);

			print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";
			}
		else 
			{
			print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: The file does not have the required number of fields to process it.</B></font></center>";
			}
		}
	##### END process standard file layout #####

		
	##### BEGIN field chooser #####
	else 
		{
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
		flush();
		
		#working

		print "<div class=cc-mstyle style='border:none; width:75%;'>";
		
		print "<table>";
		print "  <tr>";
		print "    <th>Coluna no SIPS</th>";
		print "    <th>Dados no Ficheiro</th>";
		print "  </tr>";
		
	

		$fields_stmt = "SELECT vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, rank, owner, extra1, extra2, extra3, extra4, extra5, extra6, extra7, extra8, extra9, extra10, extra11, extra12, extra13, extra14, extra15 from vicidial_list limit 1";

		##### BEGIN custom fields columns list ###
		if ($custom_fields_enabled > 0)
			{ 
			$stmt="SHOW TABLES LIKE \"custom_$list_id_override\";";
			
			
			if ($DB>0) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$tablecount_to_print = mysql_num_rows($rslt);
			if ($tablecount_to_print > 0) 
				{
				$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id_override';";
				if ($DB>0) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$fieldscount_to_print = mysql_num_rows($rslt);
				if ($fieldscount_to_print > 0) 
					{
					$rowx=mysql_fetch_row($rslt);
					$custom_records_count =	$rowx[0];

					$custom_SQL='';
					$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$list_id_override' order by field_rank,field_order,field_label;";
				
					
					if ($DB>0) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					$fields_to_print = mysql_num_rows($rslt);
					$fields_list='';
					$o=0;
					while ($fields_to_print > $o) 
						{
						$rowx=mysql_fetch_row($rslt);
						
						
						$A_field_label[$o] =	$rowx[1];
						$A_field_type[$o] =		$rowx[6];

						if ($DB>0) {echo "$A_field_label[$o]|$A_field_type[$o]\n";}

						if ( ($A_field_type[$o]!='DISPLAY') and ($A_field_type[$o]!='SCRIPT') )
							{
							if (!preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
								{
								$custom_SQL .= ",$A_field_label[$o]";
								}
							}
						$o++;
						}

					$fields_stmt = "SELECT vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, rank, owner, extra1, extra2, extra3, extra4, extra5, extra6, extra7, extra8, extra9, extra10, extra11, extra12, extra13, extra14, extra15 $custom_SQL from vicidial_list, custom_$list_id_override limit 1";

					}
				}
			}
		##### END custom fields columns list ###


		$rslt=mysql_query("$fields_stmt", $link);

		# csv xls xlsx ods sxc conversion
		$delim_set=0;
		if (preg_match("/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$/i", $leadfile_name)) 
			{
			$leadfile_name = ereg_replace("[^-\.\_0-9a-zA-Z]","_",$leadfile_name);
			copy($LF_path, "/tmp/$leadfile_name");
			$new_filename = preg_replace("/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$/i", '.txt', $leadfile_name);
			$convert_command = "$WeBServeRRooT/$admin_web_directory/sheet2tab.pl /tmp/$leadfile_name /tmp/$new_filename";
			
			passthru("$convert_command");
			$lead_file = "/tmp/$new_filename";
			if ($DB > 0) {echo "|$convert_command|";}

			if (preg_match("/\.csv$/i", $leadfile_name)) {$delim_name="CSV: Comma Separated Values";}
			if (preg_match("/\.xls$/i", $leadfile_name)) {$delim_name="XLS: MS Excel 2000-XP";}
			if (preg_match("/\.xlsx$/i", $leadfile_name)) {$delim_name="XLSX: MS Excel 2007+";}
			if (preg_match("/\.ods$/i", $leadfile_name)) {$delim_name="ODS: OpenOffice.org OpenDocument Spreadsheet";}
			if (preg_match("/\.sxc$/i", $leadfile_name)) {$delim_name="SXC: OpenOffice.org First Spreadsheet";}
			$delim_set=1;
			}
		else
			{
			copy($LF_path, "/tmp/vicidial_temp_file.txt");
			$lead_file = "/tmp/vicidial_temp_file.txt";
			}
		$file=fopen("$lead_file", "r");
		if ($WeBRooTWritablE > 0)
			{$stmt_file=fopen("$WeBServeRRooT/$admin_web_directory/listloader_stmts.txt", "w");}

		$buffer=fgets($file, 4096);
		$tab_count=substr_count($buffer, "\t");
		$pipe_count=substr_count($buffer, "|");

		if ($delim_set < 1)
			{
			if ($tab_count>$pipe_count)
				{$delim_name="tab-delimited";} 
			else 
				{$delim_name="pipe-delimited";}
			} 
		if ($tab_count>$pipe_count)
			{$delimiter="\t";}
		else 
			{$delimiter="|";}

		$field_check=explode($delimiter, $buffer);
		flush();
		$file=fopen("$lead_file", "r");
		print "<center><font face='arial, helvetica' size=3 color='#009900'>"; # <B>Processing $delim_name file...\n

		$buffer=rtrim(fgets($file, 4096));
		$buffer=stripslashes($buffer);
		$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));
		
                $q="Select `dial_method` from vicidial_campaigns a inner join vicidial_lists b on a.campaign_id=b.campaign_id WHERE b.list_id='$list_id_override';";
                $is_inbound_man=mysql_fetch_assoc(mysql_query($q,$link));
		$is_inbound_man=$is_inbound_man[dial_method]=="INBOUND_MAN";
                
		$q="Select Name,Display_name,a.active from vicidial_list_ref a inner join vicidial_lists b on a.campaign_id=b.campaign_id WHERE b.list_id='$list_id_override' ORDER BY field_order asc;";
		$sips_fields_brute=mysql_query($q,$link);
		$sips_fields=array();
		    $r=0;
		    while($linha = mysql_fetch_assoc($sips_fields_brute)){
		      		$sips_fields[$r] = $linha;
		        $r++;
		    }    
				
                    for ($i = 0; $i < mysql_num_fields($rslt); $i++) {
            $a = true;
            for ($index = 0; $index < count($sips_fields); $index++) {

                if (($sips_fields[$i]['Name'] == "list_id" and $list_id_override != "") or ($sips_fields[$i]['Name'] == "phone_code" and $phone_code_override != "") or (strtoupper($sips_fields[$i]['Name']) == strtoupper(mysql_field_name($rslt, $index)) AND $sips_fields[$i]['active'] == 0)) {
                    print "<!-- skipping " . mysql_field_name($rslt, $index) . " -->\n";
                    $a = false;
                    break;
                } elseif ((strtoupper($sips_fields[$i]['Name']) == strtoupper(mysql_field_name($rslt, $index))) or (strtoupper($sips_fields[$i]['Name'])=="OWNER" and $is_inbound_man)) {
                    print "  <tr>";
                    print "    <td style='min-width:225px'> <div class=cc-mstyle style='height:28px; '><p>" . $sips_fields[$i]['Display_name'] . "</p></div></td>";
                    print "    <td><select style='width:400px' name='" . mysql_field_name($rslt, $index) . "_field'>";
                    print "     <option value='-1'>(none)</option>\r\n";

                    for ($j = 0; $j < count($row); $j++) {
                        eregi_replace("\"", "", $row[$j]);
                        print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
                    }

                    print "    </select></td>\r\n";
                    print "  </tr>\r\n";
                    $a = false;
                    break;
                }
            }
            if ($a) {
                print "  <tr>";
                print "    <td style='min-width:225px'> <div class=cc-mstyle style='height:28px; '><p>" . strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))) . "</p></div></td>";
                print "    <td><select style='width:400px' name='" . mysql_field_name($rslt, $i) . "_field'>";
                print "     <option value='-1'>(none)</option>\r\n";

                for ($j = 0; $j < count($row); $j++) {
                    eregi_replace("\"", "", $row[$j]);
                    print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
                }

                print "    </select></td>\r\n";
                print "  </tr>\r\n";
            }
        }

		print "  <input type=hidden name=dupcheck value=\"$dupcheck\">\r\n";
		print "  <input type=hidden name=postalgmt value=\"$postalgmt\">\r\n";
		print "  <input type=hidden name=lead_file value=\"$lead_file\">\r\n";
		print "  <input type=hidden name=list_id_override value=\"$list_id_override\">\r\n";
		print "  <input type=hidden name=phone_code_override value=\"$phone_code_override\">\r\n";
		print "    <tr><th colspan=2>
		
		<input class=styled-button type=submit name='OK_to_process' value='Concluir'>
		
		<input class=styled-button type=button onClick=\"javascript:document.location='admin_listloader_third_gen.php'\" value=\"Recomeçar\" name='reload_page'></th></tr>\r\n";

		print "  </tr>\r\n";
		print "</table>\r\n";
		print "</form>";

		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
		}
	##### END field chooser #####

	}

?>
</form>

</body>
</html>


</TD></TR></TABLE>
