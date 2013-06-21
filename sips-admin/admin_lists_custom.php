<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {$far.="../";}
define("ROOT", $far);
require(ROOT . "ini/header.php");
# admin_lists_custom.php
# 
# Copyright (C) 2010  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# this screen manages the custom lists fields in ViciDial
#
# changes:
# 100506-1801 - First Build
# 100507-1027 - Added name position and options position, added extra space for name and help
# 100508-1855 - Added field_order to allow for multiple fields on the same line
# 100509-0922 - Added copy fields options
# 100510-1130 - Added DISPLAY field type option
# 100629-0200 - Added SCRIPT field type option
# 100722-1313 - Added field validation for label and name
# 100728-1724 - Added field validation for select lists and checkbox/radio buttons
# 100916-1754 - Do not show help in example form if help is empty
# 101228-2049 - Fixed missing PHP long tag
#

$admin_version = '2.4-10';
$build = '101228-2049';



require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["DB"]))							{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))				{$DB=$_POST["DB"];}
if (isset($_GET["action"]))						{$action=$_GET["action"];}
	elseif (isset($_POST["action"]))			{$action=$_POST["action"];}
if (isset($_GET["list_id"]))					{$list_id=strtoupper($_GET["list_id"]);}
	elseif (isset($_POST["list_id"]))			{$list_id=strtoupper($_POST["list_id"]);}
if (isset($_GET["field_id"]))					{$field_id=$_GET["field_id"];}
	elseif (isset($_POST["field_id"]))			{$field_id=$_POST["field_id"];}
if (isset($_GET["field_label"]))				{$field_label=$_GET["field_label"];}
	elseif (isset($_POST["field_label"]))		{$field_label=$_POST["field_label"];}
if (isset($_GET["field_name"]))					{$field_name=$_GET["field_name"];}
	elseif (isset($_POST["field_name"]))		{$field_name=$_POST["field_name"];}
if (isset($_GET["field_description"]))			{$field_description=$_GET["field_description"];}
	elseif (isset($_POST["field_description"]))	{$field_description=$_POST["field_description"];}
if (isset($_GET["field_rank"]))					{$field_rank=$_GET["field_rank"];}
	elseif (isset($_POST["field_rank"]))		{$field_rank=$_POST["field_rank"];}
if (isset($_GET["field_help"]))					{$field_help=$_GET["field_help"];}
	elseif (isset($_POST["field_help"]))		{$field_help=$_POST["field_help"];}
if (isset($_GET["field_type"]))					{$field_type=$_GET["field_type"];}
	elseif (isset($_POST["field_type"]))		{$field_type=$_POST["field_type"];}
if (isset($_GET["field_options"]))				{$field_options=$_GET["field_options"];}
	elseif (isset($_POST["field_options"]))		{$field_options=$_POST["field_options"];}
if (isset($_GET["field_size"]))					{$field_size=$_GET["field_size"];}
	elseif (isset($_POST["field_size"]))		{$field_size=$_POST["field_size"];}
if (isset($_GET["field_max"]))					{$field_max=$_GET["field_max"];}
	elseif (isset($_POST["field_max"]))			{$field_max=$_POST["field_max"];}
if (isset($_GET["field_default"]))				{$field_default=$_GET["field_default"];}
	elseif (isset($_POST["field_default"]))		{$field_default=$_POST["field_default"];}
if (isset($_GET["field_cost"]))					{$field_cost=$_GET["field_cost"];}
	elseif (isset($_POST["field_cost"]))		{$field_cost=$_POST["field_cost"];}
if (isset($_GET["field_required"]))				{$field_required=$_GET["field_required"];}
	elseif (isset($_POST["field_required"]))	{$field_required=$_POST["field_required"];}
if (isset($_GET["name_position"]))				{$name_position=$_GET["name_position"];}
	elseif (isset($_POST["name_position"]))		{$name_position=$_POST["name_position"];}
if (isset($_GET["multi_position"]))				{$multi_position=$_GET["multi_position"];}
	elseif (isset($_POST["multi_position"]))	{$multi_position=$_POST["multi_position"];}
if (isset($_GET["field_order"]))				{$field_order=$_GET["field_order"];}
	elseif (isset($_POST["field_order"]))		{$field_order=$_POST["field_order"];}
if (isset($_GET["source_list_id"]))				{$source_list_id=$_GET["source_list_id"];}
	elseif (isset($_POST["source_list_id"]))	{$source_list_id=$_POST["source_list_id"];}
if (isset($_GET["copy_option"]))				{$copy_option=$_GET["copy_option"];}
	elseif (isset($_POST["copy_option"]))		{$copy_option=$_POST["copy_option"];}
if (isset($_GET["ConFiRm"]))					{$ConFiRm=$_GET["ConFiRm"];}
	elseif (isset($_POST["ConFiRm"]))			{$ConFiRm=$_POST["ConFiRm"];}
if (isset($_GET["SUBMIT"]))						{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))			{$SUBMIT=$_POST["SUBMIT"];}
	
	if (isset($_GET["navigation"]))						{$navigation=$_GET["navigation"];}
	elseif (isset($_POST["navigation"]))			{$navigation=$_POST["navigation"];}
	if (isset($_GET["id_campo"]))						{$id_campo=$_GET["id_campo"];}
	elseif (isset($_POST["id_campo"]))			{$id_campo=$_POST["id_campo"];}

	
	/*print_r($_GET); echo "<br><br>";

print_r($_POST);	echo "<br><br>";
	
echo $navigation; */

if ( (strlen($action) < 2) and ($list_id > 99) )
	{$action = 'MODIFY_CUSTOM_FIELDS';}
if (strlen($action) < 2)
	{$action = 'LIST';}
if (strlen($DB) < 1)
	{$DB=0;}
if ($field_size > 100)
	{$field_size = 100;}
if ( (strlen($field_size) < 1) or ($field_size < 1) )
	{$field_size = 1;}
if ( (strlen($field_max) < 1) or ($field_max < 1) )
	{$field_max = 1;}


if ($non_latin < 1)
	{
	$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

	#$list_id = ereg_replace("[^0-9]","",$list_id);
/*	$field_id = ereg_replace("[^0-9]","",$field_id);
	$field_rank = ereg_replace("[^0-9]","",$field_rank);
	$field_size = ereg_replace("[^0-9]","",$field_size);
	$field_max = ereg_replace("[^0-9]","",$field_max);
	$field_order = ereg_replace("[^0-9]","",$field_order);
	$source_list_id = ereg_replace("[^0-9]","",$source_list_id);

	$field_required = ereg_replace("[^NY]","",$field_required);

	$field_type = ereg_replace("[^0-9a-zA-Z]","",$field_type);
	$ConFiRm = ereg_replace("[^0-9a-zA-Z]","",$ConFiRm);
	$name_position = ereg_replace("[^0-9a-zA-Z]","",$name_position);
	$multi_position = ereg_replace("[^0-9a-zA-Z]","",$multi_position);

	$field_label = ereg_replace("[^_0-9a-zA-Z]","",$field_label);
	$copy_option = ereg_replace("[^_0-9a-zA-Z]","",$copy_option);

	$field_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$field_name);
	$field_description = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$field_description);
	$field_options = ereg_replace("[^ \.\n\,-\_0-9a-zA-Z]","",$field_options);
	$field_default = ereg_replace("[^ \.\n\,-\_0-9a-zA-Z]","",$field_default); */
	}	# end of non_latin
else
	{
	$PHP_AUTH_USER = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_PW);
	}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

$vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$ss_conf_ct = mysql_num_rows($rslt);
if ($ss_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =						$row[0];
	$SScustom_fields_enabled =			$row[1];
	}
##### END SETTINGS LOOKUP #####
###########################################


$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_leads='1';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0)
	{$fp = fopen ("./project_auth_entries.txt", "a");}

$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");
$user = $PHP_AUTH_USER;

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
else
	{
	if ($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
		$stmt="SELECT full_name,modify_leads,custom_fields_modify,user_level from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname =				$row[0];
		$LOGmodify_leads =			$row[1];
		$LOGcustom_fields_modify =	$row[2];
		$LOGuser_level =			$row[3];

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

?>
<html>
<head>

<?php
if ($action != "HELP")
	{
?>

<link href="../css/style.css" rel="stylesheet" type="text/css" />

<script language="Javascript">
function open_help(taskspan,taskhelp) 
	{
	document.getElementById("P_" + taskspan).innerHTML = " &nbsp; <a href=\"javascript:close_help('" + taskspan + "','" + taskhelp + "');\">help-</a><BR> &nbsp; ";
	document.getElementById(taskspan).innerHTML = "<B>" + taskhelp + "</B>";
	document.getElementById(taskspan).style.background = "#FFFF99";
	}
function close_help(taskspan,taskhelp) 
	{
	document.getElementById("P_" + taskspan).innerHTML = "";
	document.getElementById(taskspan).innerHTML = " &nbsp; <a href=\"javascript:open_help('" + taskspan + "','" + taskhelp + "');\">help+</a>";
	document.getElementById(taskspan).style.background = "white";
	}
	
	function alpha(e) {
var k;
document.all ? k = e.keyCode : k = e.which;
return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8); }
</script>

<?php
	}
?>

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ADMINISTRATION: Lists Custom Fields
<?php 

################################################################################
##### BEGIN help section
if ($action == "HELP")
	{
	?>
	</title>
	</head>
	<body bgcolor=white>
	<center>
	<TABLE WIDTH=98% BGCOLOR=#E6E6E6 cellpadding=2 cellspacing=4><TR><TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>
	<BR>
	<B>ViciDial Lists Custom Fields Help</B>
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_label">
	<BR>
	 <B>Field Label -</B> This is the database field identifier for this field. This needs to be a unique identifier within the custom fields for this list. Do not use any spaces or punctuation for this field. max 50 characters, minimum of 2 characters. You can also include the default ViciDial fields in a custom field setup, and you will see them in red in the list. These fields will not be added to the custom list database table, the agent interface will instead reference the vicidial_list table directly. The labels that you can use to include the default fieds are - 
	lead_id, vendor_lead_code, source_id, list_id, gmt_offset_now, called_since_last_reset, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, called_count, last_local_call_time, rank, owner
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_name">
	<BR>
	 <B>Field Name -</B> This is the name of the field as it will appear to an agent through their interface. You can use spaces in this field, but no punctuation characters, maximum of 50 characters and minimum of 2 characters.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_description">
	<BR>
	 <B>Field Description -</B> The description of this field as it will appear in the administration interface. This is an optional field with a maximum of 100 characters.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_rank">
	<BR>
	 <B>Field Rank -</B> The order in which these fields is displayed to the agent from lowest on top to highest on the bottom.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_order">
	<BR>
	 <B>Field Order -</B> If more than one field has the same rank, they will be placed on the same line and they will be placed in order by this value from lowest to highest, left to right.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_help">
	<BR>
	 <B>Field Help -</B> Optional field, if you fill it in, the agent will be able to see this text when they click on a help link next to the field in their agent interface.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_type">
	<BR>
	 <B>Field Type -</B> This option defines the type of field that will be displayed. TEXT is a standard single-line entry form, AREA is a multi-line text box, SELECT is a single-selection pull-down menu, MULTI is a multiple-select box, RADIO is a list of radio buttons where only one option can be selected, CHECKBOX is a list of checkboxes where multiple options can be selected, DATE is a year month day calendar popup where the agent can select the date and TIME is a time selection box. The default is TEXT. For the SELECT, MULTI, RADIO and CHECKBOX options you must define the option values below in the Field Options box. DISPLAY will display only and not allow for modification by the agent. SCRIPT will also display only, but you are able to use script variables just like in the Scripts feature. SCRIPT fields will also only display the content in the Options, and not the field name like the DISPLAY type does.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_options">
	<BR>
	 <B>Field Options -</B> For the SELECT, MULTI, RADIO and CHECKBOX field types, you must define the option values in this box. You must put a list of comma separated option label and option text here with each option one its own line. The first value should have no spaces in it, and neither values should have any punctuation. For example - electric_meter, Electric Meter
	<BR><BR>

	<A NAME="vicidial_lists_fields-multi_position">
	<BR>
	 <B>Option Position -</B> For CHECKBOX and RADIO field types only, if set to HORIZONTAL the options will appear on the same line possibly wrapping to the line below if there are many options. If set to VERTICAL there will be only one option per line. Default is HORIZONTAL.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_size">
	<BR>
	 <B>Field Size -</B> This setting will mean different things depending on what the field type is. For TEXT fields, the size is the number of characters that will show in the field. For AREA fields, the size is the width of the text box in characters. For MULTI fields, this setting defines the number of options to be shown in the multi select list. For SELECT, RADIO, CHECKBOX, DATE and TIME this setting is ignored.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_max">
	<BR>
	 <B>Field Max -</B> This setting will mean different things depending on what the field type is. For TEXT fields, the size is the maximum number of characters that are allowed in the field. For AREA fields, this field defines the number of rows of text visible in the text box. For MULTI, SELECT, RADIO, CHECKBOX, DATE and TIME this setting is ignored.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_default">
	<BR>
	 <B>Field Default -</B> This optional field lets you define what value to assign to a field if nothing is loaded into that field. Default is NULL which disables the default function. For DATE field types, the default is always set to today unless a number is put in in which case the date will be that many days plus or minus today. For TIME field types, the default is always set to the current server time unless a number is put in in which case the time will be that many minutes plus or minus current time.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_cost">
	<BR>
	 <B>Field Cost -</B> This read only field tells you what the cost of this field is in the custom field table for this list. There is no hard limit for the number of custom fields you can have in a list, but the total of the cost of all fields for the list must be below 65000. This typically allows for hundreds of fields, but if you specify several TEXT fields that are hundreds or thousands of characters in length then you may hit this limit quickly. If you need that much text in a field you should choose an AREA type, which are stored differently and do not use as much table space.
	<BR><BR>

	<A NAME="vicidial_lists_fields-field_required">
	<BR>
	 <B>Field Required -</B> If set to Y, this field will force the agent to enter text or select an option for this field. Default is N.
	<BR><BR>

	<A NAME="vicidial_lists_fields-name_position">
	<BR>
	 <B>Field Name Position -</B> If set to LEFT, this field name will appear to the left of the field, if set to TOP the field name will take up the entire line and appear above the field. Default is LEFT.
	<BR><BR>

	<A NAME="vicidial_lists_fields-copy_option">
	<BR>
	 <B>Copy Option -</B> When copying field definitions from one list to another, you have a few options for how the copying process works. APPEND will add the fields that are not present in the destination list, if there are matching field labels those will remained untouched, no custom field data will be deleted or modified using this option. UPDATE will update the common field_label fields in the destination list to the field definitions from the source list. custom field data may be modified or lost using this option. REPLACE will remove all existing custom fields in the destination list and replace them with the custom fields from the source list, all custom field data will be deleted using this option.
	<BR><BR>

	</TD></TR></TABLE>
	</BODY>
	</HTML>
	<?php
	exit;
	}
### END help section





##### BEGIN Set variables to make header show properly #####
$ADD =					'100';
$hh =					'lists';
$LOGast_admin_access =	'1';
$SSoutbound_autodial_active = '1';
$ADMIN =				'admin.php';
$page_width='770';
$section_width='750';
$header_font_size='3';
$subheader_font_size='2';
$subcamp_font_size='2';
$header_selected_bold='<b>';
$header_nonselected_bold='';
$lists_color =		'#FFFF99';
$lists_font =		'BLACK';
$lists_color =		'#E6E6E6';
$subcamp_color =	'#C6C6C6';
##### END Set variables to make header show properly #####

 require("admin_header.php"); 

if ( ($LOGcustom_fields_modify < 1) or ($LOGuser_level < 8) )
	{
	echo "You are not authorized to view this section\n";
	exit;
	}

if ($SScustom_fields_enabled < 1)
	{
	echo "ERROR: Custom Fields are not active on this system\n";
	exit;
	}


$NWB = "<a style='padding:0;' href=\"javascript:openNewWindow('$PHP_SELF?action=HELP";
$NWE = "')\"><IMG style='padding:0: margin:0;' SRC=\"../images/icons/info_rhombus.png\" WIDTH=28 HEIGHT=28 BORDER=0 ALT=\"HELP\"></A>";


if ($DB > 0)
{
echo "$DB,$action,$ip,$user,$copy_option,$field_id,$list_id,$source_list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order";
}





################################################################################
##### BEGIN copy fields to a list form
if ($action == "COPY_FIELDS_FORM")
	{
	##### get lists listing for dynamic pulldown
	$stmt="SELECT list_id,list_name from vicidial_lists order by list_id";
	$rsltx=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rsltx);
	$lists_list='';
	$o=0;
	while ($lists_to_print > $o)
		{
		$rowx=mysql_fetch_row($rsltx);
		$lists_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
		
	echo "<table id='sub-menu'>";
	echo "<tr>";
	echo "<td> COPIAR Campos Dinâmicos </td>";
	echo "</tr>";	
	echo "</table>"; 

	echo "<form action=$PHP_SELF method=POST>";
	echo "<input type=hidden name=DB value=\"$DB\">";
	echo "<input type=hidden name=action value=COPY_FIELDS_SUBMIT>";
	echo "<TABLE id=form-list>";
	echo "<tr bgcolor=#bcbcbc><td align=right>List ID to Copy Fields From: </td><td align=left><select size=1 name=source_list_id>\n";
	echo "$lists_list";
	echo "</select></td></tr>\n";
	echo "<tr bgcolor=#bcbcbc><td align=right>List ID to Copy Fields to: </td><td align=left><select size=1 name=list_id>\n";
	echo "$lists_list";
	echo "</select></td></tr>\n";
	echo "<tr bgcolor=#bcbcbc><td align=right>Copy Option: </td><td align=left><select size=1 name=copy_option>\n";
	echo "<option selected>APPEND</option>";
	echo "<option>UPDATE</option>";
	echo "<option>REPLACE</option>";
	echo "</select> $NWB#vicidial_lists_fields-copy_option$NWE</td></tr>\n";
	echo "<tr><td style=border-bottom:none colspan=2><input type=submit class=styled-button name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TD></TR></TABLE>\n";
	}
### END copy fields to a list form




################################################################################
##### BEGIN copy list fields submit
if ( ($action == "COPY_FIELDS_SUBMIT") and (strlen($copy_option) > 2) )
	{
	if ($list_id=="$source_list_id")
		{echo "ERROR: You cannot copy fields to the same list: $list_id|$source_list_id";}
	else
		{
		$table_exists=0;
		$linkCUSTOM=mysql_connect("$VARDB_server:$VARDB_port", "$VARDB_custom_user","$VARDB_custom_pass");
		if (!$linkCUSTOM) {die("Could not connect: $VARDB_server|$VARDB_port|$VARDB_database|$VARDB_custom_user|$VARDB_custom_pass" . mysql_error());}
		mysql_select_db("$VARDB_database", $linkCUSTOM);

		$stmt="SELECT count(*) from vicidial_lists_fields where list_id='$source_list_id';";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$source_field_exists =	$rowx[0];
			}
		
		$stmt="SELECT count(*) from vicidial_lists_fields where campaign_id='$list_id';";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$field_exists =	$rowx[0];
			}
		
		$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
		$rslt=mysql_query($stmt, $link);
		$tablecount_to_print = mysql_num_rows($rslt);
		if ($tablecount_to_print > 0) 
			{$table_exists =	1;}
		if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
		
		if ($source_field_exists < 1)
			{echo "ERROR: Source list has no custom fields\n<BR>";}
		else
			{
			##### REPLACE option #####
			if ($copy_option=='REPLACE')
				{
				if ($DB > 0) {echo "Starting REPLACE copy\n<BR>";}
				if ($table_exists > 0)
					{
					$stmt="SELECT field_id,field_label from vicidial_lists_fields where campaign_id='$list_id' order by field_rank,field_order,field_label;"; #sql
					$rslt=mysql_query($stmt, $link);
					$fields_to_print = mysql_num_rows($rslt);
			
					$fields_list='';
					$o=0;
					while ($fields_to_print > $o) 
						{
						$rowx=mysql_fetch_row($rslt);
						$A_field_id[$o] =			$rowx[0];
						$A_field_label[$o] =		$rowx[1];
						$o++;
						}

					$o=0;
					while ($fields_to_print > $o) 
						{
						### delete field function
						delete_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$A_field_id[$o],$list_id,$A_field_label[$o],$A_field_name[$o],$A_field_description[$o],$A_field_rank[$o],$A_field_help[$o],$A_field_type[$o],$A_field_options[$o],$A_field_size[$o],$A_field_max[$o],$A_field_default[$o],$A_field_required[$o],$A_field_cost[$o],$A_multi_position[$o],$A_name_position[$o],$A_field_order[$o],$vicidial_list_fields);

						echo "SUCCESS: Custom Field Deleted - $list_id|$A_field_label[$o]\n<BR>";
						$o++;
						}
					}
				$copy_option='APPEND';
				}
			##### APPEND option #####
			if ($copy_option=='APPEND')
				{
				if ($DB > 0) {echo "Starting APPEND copy\n<BR>";}
				$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$source_list_id' order by field_rank,field_order,field_label;";
				$rslt=mysql_query($stmt, $link);
				$fields_to_print = mysql_num_rows($rslt);
				$fields_list='';
				$o=0;
				while ($fields_to_print > $o) 
					{
					$rowx=mysql_fetch_row($rslt);
					$A_field_id[$o] =			$rowx[0];
					$A_field_label[$o] =		$rowx[1];
					$A_field_name[$o] =			$rowx[2];
					$A_field_description[$o] =	$rowx[3];
					$A_field_rank[$o] =			$rowx[4];
					$A_field_help[$o] =			$rowx[5];
					$A_field_type[$o] =			$rowx[6];
					$A_field_options[$o] =		$rowx[7];
					$A_field_size[$o] =			$rowx[8];
					$A_field_max[$o] =			$rowx[9];
					$A_field_default[$o] =		$rowx[10];
					$A_field_cost[$o] =			$rowx[11];
					$A_field_required[$o] =		$rowx[12];
					$A_multi_position[$o] =		$rowx[13];
					$A_name_position[$o] =		$rowx[14];
					$A_field_order[$o] =		$rowx[15];

					$o++;
					$rank_select .= "<option>$o</option>";
					}

				$o=0;
				while ($fields_to_print > $o) 
					{
					$new_field_exists=0;
					if ($table_exists > 0)
						{
						$stmt="SELECT count(*) from vicidial_lists_fields where campaign_id='$list_id' and field_label='$A_field_label[$o]';";
						if ($DB>0) {echo "$stmt";}
						$rslt=mysql_query($stmt, $link);
						$fieldscount_to_print = mysql_num_rows($rslt);
						if ($fieldscount_to_print > 0) 
							{
							$rowx=mysql_fetch_row($rslt);
							$new_field_exists =	$rowx[0];
							}
						}
					if ($new_field_exists < 1)
						{
						### add field function
						add_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$A_field_id[$o],$list_id,$A_field_label[$o],$A_field_name[$o],$A_field_description[$o],$A_field_rank[$o],$A_field_help[$o],$A_field_type[$o],$A_field_options[$o],$A_field_size[$o],$A_field_max[$o],$A_field_default[$o],$A_field_required[$o],$A_field_cost[$o],$A_multi_position[$o],$A_name_position[$o],$A_field_order[$o],$vicidial_list_fields);

						echo "SUCCESS: Custom Field Added - $list_id|$A_field_label[$o]\n<BR>";

						if ($table_exists < 1) {$table_exists=1;}
						}
					$o++;
					}
				}
			##### UPDATE option #####
			if ($copy_option=='UPDATE')
				{
				if ($DB > 0) {echo "Starting UPDATE copy\n<BR>";}
				if ($table_exists < 1)
					{echo "ERROR: Table does not exist custom_$list_id\n<BR>";}
				else
					{
					$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$source_list_id' order by field_rank,field_order,field_label;";
					$rslt=mysql_query($stmt, $link);
					$fields_to_print = mysql_num_rows($rslt);
					$fields_list='';
					$o=0;
					while ($fields_to_print > $o) 
						{
						$rowx=mysql_fetch_row($rslt);
						$A_field_id[$o] =			$rowx[0];
						$A_field_label[$o] =		$rowx[1];
						$A_field_name[$o] =			$rowx[2];
						$A_field_description[$o] =	$rowx[3];
						$A_field_rank[$o] =			$rowx[4];
						$A_field_help[$o] =			$rowx[5];
						$A_field_type[$o] =			$rowx[6];
						$A_field_options[$o] =		$rowx[7];
						$A_field_size[$o] =			$rowx[8];
						$A_field_max[$o] =			$rowx[9];
						$A_field_default[$o] =		$rowx[10];
						$A_field_cost[$o] =			$rowx[11];
						$A_field_required[$o] =		$rowx[12];
						$A_multi_position[$o] =		$rowx[13];
						$A_name_position[$o] =		$rowx[14];
						$A_field_order[$o] =		$rowx[15];
						$o++;
						}

					$o=0;
					while ($fields_to_print > $o) 
						{
						$stmt="SELECT field_id from vicidial_lists_fields where campaign_id='$list_id' and field_label='$A_field_label[$o]';";
						if ($DB>0) {echo "$stmt";}
						$rslt=mysql_query($stmt, $link);
						$fieldscount_to_print = mysql_num_rows($rslt);
						if ($fieldscount_to_print > 0) 
							{
							$rowx=mysql_fetch_row($rslt);
							$current_field_id =	$rowx[0];

							### modify field function
							modify_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$current_field_id,$list_id,$A_field_label[$o],$A_field_name[$o],$A_field_description[$o],$A_field_rank[$o],$A_field_help[$o],$A_field_type[$o],$A_field_options[$o],$A_field_size[$o],$A_field_max[$o],$A_field_default[$o],$A_field_required[$o],$A_field_cost[$o],$A_multi_position[$o],$A_name_position[$o],$A_field_order[$o],$vicidial_list_fields);

							echo "SUCCESS: Custom Field Modified - $list_id|$A_field_label[$o]\n<BR>";
							}
						$o++;
						}
					}
				}
			}

		$action = "MODIFY_CUSTOM_FIELDS";
		}
	}
### END copy list fields submit





################################################################################
##### BEGIN delete custom field confirmation
if ( ($action == "DELETE_CUSTOM_FIELD_CONFIRMATION") and ($field_id > 0) and (strlen($field_label) > 0) )
	{
	$stmt="SELECT count(*) from vicidial_lists_fields where campaign_id='$list_id' and field_label='$field_label';";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
	$fieldscount_to_print = mysql_num_rows($rslt);
	if ($fieldscount_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$field_exists =	$rowx[0];
		}
	
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
	
	if ($field_exists < 1)
		{echo "<script type=text/javascript> alert('Erro - Este campo não existe.'); </script>";}
	else
		{
		if ($table_exists < 1) 
			{echo "<script type=text/javascript> alert('Erro - A tabela (custom_$list_id) não existe.'); </script>"; }
		else
			{
			echo "<script type=text/javascript>window.location = \"$PHP_SELF?action=DELETE_CUSTOM_FIELD&list_id=$list_id&field_id=$field_id&field_label=$field_label&ConFiRm=YES&DB=$DB\"</script>"; #<BR><BR><B><a href=\"$PHP_SELF?action=DELETE_CUSTOM_FIELD&list_id=$list_id&field_id=$field_id&field_label=$field_label&ConFiRm=YES&DB=$DB\">CLICK HERE TO CONFIRM DELETION OF THIS CUSTOM FIELD: $field_label - $field_id - $list_id</a></B><BR><BR>
			}
		}

	$action = "MODIFY_CUSTOM_FIELDS";
	}
### END delete custom field confirmation




################################################################################
##### BEGIN delete custom field
if ( ($action == "DELETE_CUSTOM_FIELD") and ($field_id > 0) and (strlen($field_label) > 0) and ($ConFiRm=='YES') )
	{
	$table_exists=0;
	$linkCUSTOM=mysql_connect("$VARDB_server:$VARDB_port", "$VARDB_custom_user","$VARDB_custom_pass");
	if (!$linkCUSTOM) {die("Could not connect: $VARDB_server|$VARDB_port|$VARDB_database|$VARDB_custom_user|$VARDB_custom_pass" . mysql_error());}
	mysql_select_db("$VARDB_database", $linkCUSTOM);

	$stmt="SELECT count(*) from vicidial_lists_fields where campaign_id='$list_id' and field_label='$field_label';";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
	$fieldscount_to_print = mysql_num_rows($rslt);
	if ($fieldscount_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$field_exists =	$rowx[0];
		}
	
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
	
	if ($field_exists < 1)
		{echo "<script type=text/javascript> alert('Erro - Este campo não existe.'); </script>";}
	else
		{
		if ($table_exists < 1)
			{echo "<script type=text/javascript> alert('Erro - A tabela (custom_$list_id) não existe.'); </script>";}
		else
			{
			### delete field function
			delete_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields);

			echo "<script type=text/javascript>alert('Campo Eliminado com Sucesso.'); window.location = 'admin_lists_custom.php?navigation=resumo&action=MODIFY_CUSTOM_FIELDS&list_id=$list_id'; </script>"; #SUCCESS: Custom Field Deleted - $list_id|$field_label\n<BR>
			}
		}

	$action = "MODIFY_CUSTOM_FIELDS";
	}
### END delete custom field




################################################################################
##### BEGIN add new custom field
if ( ($action == "ADD_CUSTOM_FIELD") )
	{
	$stmt="SELECT count(*) from vicidial_lists_fields where campaign_id='$list_id' and field_label='$field_label';";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
	$fieldscount_to_print = mysql_num_rows($rslt);
	if ($fieldscount_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$field_exists =	$rowx[0];
		}
	
	if ( (strlen($field_label)<1) or (strlen($field_name)<2) or (strlen($field_size)<1) )
		{
		
		echo "<script type=text/javascript> alert('Erro - Tem de inserir um ID de Campo, uma Descrição de Campo e um Conteudo de Campo. '); window.location = 'admin_lists_custom.php?navigation=resumo&action=MODIFY_CUSTOM_FIELDS&list_id=$list_id'</script>";
		
		#echo "ERROR: You must enter a field label, field name and field size - $list_id|$field_label|$field_name|$field_size\n<BR>";
		}
	else
		{
		$TEST_valid_options=0;
		if ( ($field_type=='SELECT') or ($field_type=='MULTI') or ($field_type=='RADIO') or ($field_type=='CHECKBOX') )
			{
			$TESTfield_options_array = explode("\n",$field_options);
			$TESTfield_options_count = count($TESTfield_options_array);
			$te=0;
			while ($te < $TESTfield_options_count)
				{
				if (preg_match("/,/",$TESTfield_options_array[$te]))
					{
					$TESTfield_options_value_array = explode(",",$TESTfield_options_array[$te]);
					if ( (strlen($TESTfield_options_value_array[0]) > 0) and (strlen($TESTfield_options_value_array[1]) > 0) )
						{$TEST_valid_options++;}
					}
				$te++;
				}
			$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
			}

		if ( ( ($field_type=='SELECT') or ($field_type=='MULTI') or ($field_type=='RADIO') or ($field_type=='CHECKBOX') ) and ( (!preg_match("/,/",$field_options)) or (!preg_match("/\n/",$field_options)) or (strlen($field_options)<6) or ($TEST_valid_options < 1) ) )
			{echo "ERROR: You must enter field options when adding a SELECT, MULTI, RADIO or CHECKBOX field type  - $list_id|$field_label|$field_type|$field_options\n<BR>";}
		else
			{
			if ($field_exists > 0)
				{
				
				echo "<script type=text/javascript> alert('Erro - Este campo já existe nesta lista.'); </script>";
				
				#echo "ERROR: Field already exists for this list - $list_id|$field_label\n<BR>";
				}
			else
				{
				$table_exists=0;
				$linkCUSTOM=mysql_connect("$VARDB_server:$VARDB_port", "$VARDB_custom_user","$VARDB_custom_pass");
				if (!$linkCUSTOM) {die("Could not connect: $VARDB_server|$VARDB_port|$VARDB_database|$VARDB_custom_user|$VARDB_custom_pass" . mysql_error());}
				mysql_select_db("$VARDB_database", $linkCUSTOM);

				$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
				$rslt=mysql_query($stmt, $link);
				$tablecount_to_print = mysql_num_rows($rslt);
				if ($tablecount_to_print > 0) 
					{$table_exists =	1;}
				if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
			
				### add field function
				add_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields);

				echo "<script type=text/javascript>alert('Campo Adicionado com Sucesso.'); window.location='admin_lists_custom.php?action=MODIFY_CUSTOM_FIELDS&navigation=resumo&list_id=$list_id';</script>"; #SUCCESS: Custom Field Added - $list_id|$field_label\n<BR>
				}
			}
		}

	$action = "MODIFY_CUSTOM_FIELDS";
	}
### END add new custom field




################################################################################
##### BEGIN modify custom field submission
if ( ($action == "MODIFY_CUSTOM_FIELD_SUBMIT") and ($field_id > 0) )
	{
	### connect to your vtiger database
	$linkCUSTOM=mysql_connect("$VARDB_server:$VARDB_port", "$VARDB_custom_user","$VARDB_custom_pass");
	if (!$linkCUSTOM) {die("Could not connect: $VARDB_server|$VARDB_port|$VARDB_database|$VARDB_custom_user|$VARDB_custom_pass" . mysql_error());}
	mysql_select_db("$VARDB_database", $linkCUSTOM);

	$stmt="SELECT count(*) from vicidial_lists_fields where campaign_id='$list_id' and field_id='$field_id';";
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_query($stmt, $link);
	$fieldscount_to_print = mysql_num_rows($rslt);
	if ($fieldscount_to_print > 0) 
		{
		$rowx=mysql_fetch_row($rslt);
		$field_exists =	$rowx[0];
		}
	$list_id=strtoupper($list_id);
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	echo $stmt;
	
	
	
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}

	if ($field_exists < 1)
		{
		
		echo "<script type=text/javascript> alert('Erro - O Campo não existe.'); </script>";
		
		#echo "ERROR: Field does not exist\n<BR>";
		
		}
	else
		{
		if ($table_exists < 1)
			{
			echo "<script type=text/javascript> alert('Erro - A Tabela não existe.'); </script>";
			
			#echo "ERROR: Table does not exist\n<BR>";
			
			}
		else
			{
			$TEST_valid_options=0;
			if ( ($field_type=='SELECT') or ($field_type=='MULTI') or ($field_type=='RADIO') or ($field_type=='CHECKBOX') )
				{
				$TESTfield_options_array = explode("\n",$field_options);
				$TESTfield_options_count = count($TESTfield_options_array);
				$te=0;
				while ($te < $TESTfield_options_count)
					{
					if (preg_match("/,/",$TESTfield_options_array[$te]))
						{
						$TESTfield_options_value_array = explode(",",$TESTfield_options_array[$te]);
						if ( (strlen($TESTfield_options_value_array[0]) > 0) and (strlen($TESTfield_options_value_array[1]) > 0) )
							{$TEST_valid_options++;}
						}
					$te++;
					}
				$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
				}

			if ( ( ($field_type=='SELECT') or ($field_type=='MULTI') or ($field_type=='RADIO') or ($field_type=='CHECKBOX') ) and ( (!preg_match("/,/",$field_options)) or (!preg_match("/\n/",$field_options)) or (strlen($field_options)<6) or ($TEST_valid_options < 1) ) )
				{echo "ERROR: You must enter field options when updating a SELECT, MULTI, RADIO or CHECKBOX field type  - $list_id|$field_label|$field_type|$field_options\n<BR>";}
			else
				{
				### modify field function
				modify_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields);

				echo "<script type=text/javascript>alert('Campo Editado com Sucesso.'); window.location = 'admin_lists_custom.php?list_id=$list_id&navigation=resumo&action=MODIFY_CUSTOM_FIELDS';</script>"; #SUCCESS: Custom Field Modified - $list_id|$field_label\n<BR>
				}
			}
		}

	$action = "MODIFY_CUSTOM_FIELDS";
	}
### END modify custom field submission





################################################################################
##### BEGIN modify custom fields for list



if ( ($action == "MODIFY_CUSTOM_FIELDS") )
	{
	
	
	echo "</TITLE></HEAD><BODY BGCOLOR=white>\n";
	
	#echo "<script type='text/javascript'>alert('".$list_id."'); </script>";
	$custom_records_count=0;
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	#echo "<script type='text/javascript'>alert('".$tablecount_to_print."'); </script>";
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}
	
	if ($table_exists > 0)
		{
		$stmt="SELECT count(*) from custom_$list_id;";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$custom_records_count =	$rowx[0];
			}
		}
		
	$stmt="SELECT list_name from vicidial_lists WHERE list_id='$list_id' ";
	$rslt=mysql_query($stmt, $link);
	$rowj=mysql_fetch_row($rslt);

	
/**	echo "<table id='sub-menu'>";
	echo "<tr>";
	#echo "<td> EDITAR Campos Dinâmicos </td>";
	echo "<td style='border-right:1px solid #6678b1;'> $rowj[0] </td>";
	echo "<td><a href=#novo_campo> Novo Campo Personalizado </a></td>";

	/* echo "<td><a href=\"./admin.php?ADD=311&list_id=$list_id\"> Editar esta Lista </a> </td>"; */
	
/*	echo "<td><a href=\"$PHP_SELF?action=ADMIN_LOG&list_id=$list_id\"> Admin Log </a> </td>";


	echo "</tr>";	
	echo "</table>"; */
	
	#sql

	$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where campaign_id='$list_id' order by field_rank,field_order,field_label;";
	$rslt=mysql_query($stmt, $link);
    
	$fields_to_print = mysql_num_rows($rslt);
	$fields_list='';
	$o=0;
	while ($fields_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
      
        
        
		$A_field_id[$o] =			$rowx[0];
		$A_field_label[$o] =		$rowx[1];
		$A_field_name[$o] =			$rowx[2];
		$A_field_description[$o] =	$rowx[3];
		$A_field_rank[$o] =			$rowx[4];
		$A_field_help[$o] =			$rowx[5];
		$A_field_type[$o] =			$rowx[6];
		$A_field_options[$o] =		$rowx[7];
		$A_field_size[$o] =			$rowx[8];
		$A_field_max[$o] =			$rowx[9];
		$A_field_default[$o] =		$rowx[10];
		$A_field_cost[$o] =			$rowx[11];
		$A_field_required[$o] =		$rowx[12];
		$A_multi_position[$o] =		$rowx[13];
		$A_name_position[$o] =		$rowx[14];
		$A_field_order[$o] =		$rowx[15];

		$o++;
		$rank_select .= "<option>$o</option>";
		}
	$o++;
	#$rank_select .= "<option>$o</option>";
	$last_rank = $o;

	for($k=$o;$k<50;$k++)
	{
		$rank_select .= "<option>$k</option>"; 
	}
	
	
	### SUMMARY OF FIELDS ### 
if ($navigation == "resumo") {
	
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../images/icons/script_edit.png' /></td>";
	echo "<td id='submenu-title'> Campos Dinâmicos </td>";
	echo "<td style='text-align:left'>Listagem de todos os Campos Dinâmicos existentes nesta Campanha.</td>";
	echo "<td id=icon32><a href=\"$PHP_SELF?action=MODIFY_CUSTOM_FIELDS&list_id=$list_id&navigation=novo\"> <img src='../images/icons/script_add.png' /></a></td>";
		echo "<td style='text-align:left'>Criar Novo Campo</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br>";

	echo "<div class=cc-mstyle>";

	
	
	echo "<table>";
	echo "<tr>";
	echo "<th>Ordem</th>";
	echo "<th>Nome</th>";
	#echo "<th>NAME</th>";
	echo "<th>Tipo</th>";
	echo "<th>Editar</th>";
	echo "</tr>";

	$o=0;
	while ($fields_to_print > $o) 
		{
		$LcolorB='';   $LcolorE='';
		if (preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
			{
			$LcolorB='<font color=red>';
			$LcolorE='</font>';
			}
		
		echo "<tr><td><font size=1>$A_field_rank[$o] - $A_field_order[$o] </td>";
		echo "<td> $A_field_label[$o]</a> </td>";
		#echo "<td> $A_field_name[$o] </td>";
		
		switch ($A_field_type[$o]) {
		
		case "TEXT" : $ftype = "Texto"; break;
		case "AREA" : $ftype = "Caixa de Texto"; break;
		case "SELECT" : $ftype = "Combo-Box"; break;
		case "MULTI" : $ftype = "Multi Selecção"; break;
		case "RADIO" : $ftype = "Botão Rádio"; break;
		case "CHECKBOX" : $ftype = "Checkbox"; break;
		case "DATE" : $ftype = "Data"; break;
		case "TIME" : $ftype = "Hora"; break;
		case "DISPLAY" : $ftype = "Script sem variáveis"; break;
		case "SCRIPT" : $ftype = "Script com variáveis"; break;
		
		
		}
		
		
		/* echo "<tr><td>Tipo do Campo</td><td colspan=4><select size=1 name=field_type>\n";
		echo "<option $selected value=TEXT>Texto</option>\n";
		echo "<option $selected1 value=AREA>Caixa de Texto</option>\n";
		echo "<option $selected2 value=SELECT>Combo-Box</option>\n";
		echo "<option $selected3 value=MULTI>Combo-Box com vários valores</option>\n";
		echo "<option $selected4 value=RADIO>Botão Rádio</option>\n";
		echo "<option $selected5 value=CHECKBOX>Checkbox</option>\n";
		echo "<option $selected6 value=DATE>Data</option>\n";
		echo "<option $selected7 value=TIME>Hora</option>\n";
		echo "<option $selected8 value=DISPLAY>Script sem variáveis</option>\n";
		echo "<option $selected9 value=SCRIPT>Script com variáveis</option>\n";
		echo "</select><td style=width:30px>  $NWB#vicidial_lists_fields-field_type$NWE </td></tr>\n"; */
		
		
		
		
		echo "<td> $ftype  </td>";
		echo "<td><a href=\"$PHP_SELF?navigation=editar&action=MODIFY_CUSTOM_FIELDS&list_id=$list_id&id_campo=$A_field_label[$o]\">  <img src='../images/icons/livejournal.png' /></td></tr>";

		$total_cost = ($total_cost + $A_field_cost[$o]);
		$o++;
		}

/*	if ($fields_to_print < 1) 
		{echo "<tr><td colspan=5> Esta lista não contem Campos Dinâmicos. </td></tr>";}
	else
		{
		echo "<tr><td> TOTALS: </td>";
		echo "<td> $o </td>";
		echo "<td>  </td>";
		echo "<td>  </td>";
		echo "<td> $total_cost </td></tr>";
		} */
		
		echo "<tr><th></th></tr>";
	echo "</table></div>";
}


if ($navigation == "editar") 

{	

$stmt = "SELECT MAX(field_rank) FROM vicidial_lists_fields where campaign_id='$list_id'";
$rslt=mysql_query($stmt, $link);
$max_rank=mysql_fetch_row($rslt);

$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where campaign_id='$list_id' AND field_label='$id_campo' order by field_rank,field_order,field_label;";
	$rslt=mysql_query($stmt, $link);
	#$fields_to_print = mysql_num_rows($rslt);
	
	#echo $fields_to_print;
	
	$fields_list='';
	$o=0;
	$rank_select_edit = "";
	#while ($fields_to_print > $o) 
		#{
		$rowx=mysql_fetch_row($rslt);
        
               
		$A_field_id[$o] =			$rowx[0];
		$A_field_label[$o] =		$rowx[1];
		$A_field_name[$o] =			$rowx[2];
		$A_field_description[$o] =	$rowx[3];
		$A_field_rank =			$rowx[4];
		$A_field_help[$o] =			$rowx[5];
		$A_field_type[$o] =			$rowx[6];
		$A_field_options[$o] =		$rowx[7];
		$A_field_size[$o] =			$rowx[8];
		$A_field_max[$o] =			$rowx[9];
		$A_field_default[$o] =		$rowx[10];
		$A_field_cost[$o] =			$rowx[11];
		$A_field_required[$o] =		$rowx[12];
		$A_multi_position[$o] =		$rowx[13];
		$A_name_position[$o] =		$rowx[14];
		$A_field_order[$o] =		$rowx[15];

		
		#$o++;


		
for ($h=1;$max_rank[0]+1>=$h;$h++) 

{

	if ($h == $A_field_rank) 
	{ 
	$rank_select_edit .= "<option selected>$h</option>"; 
	} 
	else 
	{
	$rank_select_edit .= "<option>$h</option>";
	}
		
}
		
		
		



		
		
		
		
		#}
	
	
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../images/icons/database_table.png' /></td>";
	echo "<td id='submenu-title'> Editar Campo Dinâmico </td>";
	echo "<td style='text-align:left'></td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";

	echo "<div id=work-area>";
	echo "<br><br>";
	
	
	
	
/*	$o=0;
	while ($fields_to_print > $o) 
		{
		$LcolorB='';   $LcolorE='';
		if (preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
			{
			$LcolorB='<font color=red>';
			$LcolorE='</font>';
			}*/
		
		echo "<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=action value=MODIFY_CUSTOM_FIELD_SUBMIT>\n";
		echo "<input type=hidden name=list_id value=$list_id>\n";
		echo "<input type=hidden name=DB value=$DB>\n";
		echo "<input type=hidden name=field_id value=\"$A_field_id[$o]\">\n";
		echo "<input type=hidden name=field_label value=\"$A_field_label[$o]\">\n";
		
		
		echo "<div class=cc-mstyle style='border:none;'>";
			echo "<table>";
		
		
		echo "<tr><td align=right>Ordem Horizontal </td><td><select size=1 name=field_rank>\n";
		
		
		
		echo "$rank_select_edit";
		echo "</select><td style='width:30px;'>$NWB#vicidial_lists_fields-field_rank$NWE</td>";
		
		switch ($A_field_order[$o]) {
		case 1: $selected1 = "selected"; break;
		case 2: $selected2 = "selected"; break;
		case 3: $selected3 = "selected"; break;
		case 4: $selected4 = "selected"; break;
		case 5: $selected5 = "selected"; break;
		
		}
		
		
		echo "<td>Ordem Lateral</td><td><select size=1 name=field_order>\n";
		echo "<option $selected1>1</option>\n";
		echo "<option $selected2>2</option>\n";
		echo "<option $selected3>3</option>\n";
		echo "<option $selected4>4</option>\n";
		echo "<option $selected5>5</option>\n";
		echo "</select><td style=width:30px>$NWB#vicidial_lists_fields-field_order$NWE </td></tr>\n";
		
		
		
	
		echo "<tr><td>ID do Campo</td><td colspan=4>$A_field_label[$o] <td style=width:30px> $NWB#vicidial_lists_fields-field_label$NWE </td></tr>\n";
		
		
		
		
		
		
		$selected ="";
		$selected1="";
		$selected2="";
		$selected3="";
		$selected4="";
		$selected5="";
		$selected6="";
		$selected7="";
		$selected8="";
				$selected9="";
		
		switch ($A_field_type[$o]) {
		
		case "TEXT" : $selected = "selected"; break;
		case "AREA" : $selected1 = "selected"; break;
		case "SELECT" : $selected2 = "selected"; break;
		case "MULTI" : $selected3 = "selected"; break;
		case "RADIO" : $selected4 = "selected"; break;
		case "CHECKBOX" : $selected5 = "selected"; break;
		case "DATE" : $selected6 = "selected"; break;
		case "TIME" : $selected7 = "selected"; break;
		case "DISPLAY" : $selected8 = "selected"; break;
		case "SCRIPT" : $selected9 = "selected"; break;
		
		
		}
		
		
		
		
		
		echo "<tr><td>Tipo do Campo</td><td colspan=4><select size=1 name=field_type>\n";
		echo "<option $selected value=TEXT>Texto</option>\n";
		echo "<option $selected1 value=AREA>Caixa de Texto</option>\n";
		echo "<option $selected2 value=SELECT>Combo-Box</option>\n";
		echo "<option $selected3 value=MULTI>Combo-Box com vários valores</option>\n";
		echo "<option $selected4 value=RADIO>Botão Rádio</option>\n";
		echo "<option $selected5 value=CHECKBOX>Checkbox</option>\n";
		echo "<option $selected6 value=DATE>Data</option>\n";
		echo "<option $selected7 value=TIME>Hora</option>\n";
		echo "<option $selected8 value=DISPLAY>Script sem variáveis</option>\n";
		echo "<option $selected9 value=SCRIPT>Script com variáveis</option>\n";
		echo "</select><td style=width:30px>  $NWB#vicidial_lists_fields-field_type$NWE </td></tr>\n";
		
		echo "<tr><td>Descrição do Campo</td><td colspan=4><input type=text name=field_description size=70 maxlength=100 value=\"$A_field_description[$o]\"><td style=width:30px;> $NWB#vicidial_lists_fields-field_description$NWE </td></tr>\n";
		
		
		$selected = "";
		$selected1 = "";
		
		if ($A_name_position[$o] == "LEFT") { $selected = "selected";} else {$selected1 = "selected";}
		
		echo "<tr><td>Posição do Conteudo</td><td colspan=4><select size=1 name=name_position>\n";
		echo "<option $selected value=\"LEFT\">Esquerda</option>\n";
		echo "<option $selected1 value=\"TOP\">Topo</option>\n";
		echo "</select><td style='width:30px';  $NWB#vicidial_lists_fields-name_position$NWE </td></tr>\n";
		
		
		
		echo "<tr><td>Texto do Campo</td><td colspan=4><textarea style='height:125px' name=field_name rows=2 cols=60>$A_field_name[$o]</textarea><td style=width:30px;> $NWB#vicidial_lists_fields-field_name$NWE </td></tr>\n";
		
		
		$selected = "";
		$selected1 = "";
                
                
		if ($A_multi_position[$o] == "HORIZONTAL") {$selected = "selected";} else {$selected1 = "selected";} 
		
		echo "<tr><td>Posição da Opção asdasdasdasdasd</td><td colspan=4><select size=1 name=multi_position>\n";
		echo "<option $selected value=\"HORIZONTAL\">Horizontal</option>\n";
		echo "<option $selected1 value=\"VERTICAL\">Vertical</option>\n";   
		echo "</select><td style=width:30px;>  $NWB#vicidial_lists_fields-multi_position$NWE </td></tr>\n";
                
                echo "<tr><td>Funcionalidades da Opção</td><td colspan=4><select size=1 name=multi_position>\n";
		echo "<option value=\"\">&nbsp</option>\n";
                echo "<option $selected value=\"HORIZONTAL\">Esconde o elemento abaixo</option>\n";
		echo "</select><td style=width:30px;>  $NWB#vicidial_lists_fields-multi_position$NWE </td></tr>\n"; 
		
		echo "<tr><td>Opções do Campo</td><td colspan=4><textarea style=height:125px name=field_options ROWS=5 COLS=60>$A_field_options[$o]</textarea><td style=width:30px;>  $NWB#vicidial_lists_fields-field_options$NWE </td></tr>\n";
		
		echo "<tr><td>Tamanho do Campo</td><td colspan=4><input type=text name=field_size size=5 maxlength=3 value=\"$A_field_size[$o]\"><td style=width:30px;>  $NWB#vicidial_lists_fields-field_size$NWE </td></tr>\n";
                
		echo "<tr><td>Tamanho Máximo</td><td colspan=4><input type=text name=field_max size=5 maxlength=3 value=\"$A_field_max[$o]\"><td style=width:30px;>  $NWB#vicidial_lists_fields-field_max$NWE </td></tr>\n";
		
		echo "<tr><td>Valor por Defeito</td><td colspan=4><input type=text name=field_default size=50 maxlength=255 value=\"$A_field_default[$o]\"><td style=width:30px;>  $NWB#vicidial_lists_fields-field_default$NWE </td></tr>\n";
		
		$selected = "";
		$selected1 = "";
		
		if ($A_field_required == "Y") { $selected = "selected";} else { $selected1 = "selected";}
		
		
		echo "<tr><td>Campo Obrigatório</td><td colspan=4><select size=1 name=field_required>\n";
		echo "<option $selected value=\"Y\">Sim</option>\n";
		echo "<option $selected1 value=\"N\">Não</option>\n";
		echo "</select><td style=width:30px>  $NWB#vicidial_lists_fields-field_required$NWE </td></tr>\n";
		
		
		
		echo "<tr><td>Ajuda do Campo</td><td colspan=4><textarea style='height:125px' name=field_help rows=2 cols=60>$A_field_help[$o]</textarea> <td style=width:32px>$NWB#vicidial_lists_fields-field_help$NWE </td></tr>\n";
		
		echo "</table>";
		
		
		echo "<table><tr>
		<td style=text-align:right>Apagar</td>
		<td><a style='top:16px; position:relative;' onclick=window.parent.ConfirmDelete(this,\"$PHP_SELF?action=DELETE_CUSTOM_FIELD_CONFIRMATION&list_id=$list_id&field_id=$A_field_id[$o]&field_label=$A_field_label[$o]&DB=$DB\"); href=><img style=float:left src='../images/icons/shape_square_delete.png' /></a></td>
		
		<td style=text-align:right>Gravar</td>
		<td><input type=image style='float:left' src='../images/icons/shape_square_add.png' alt=Gravar name=SUBMIT></td>
		</tr></table>";
		
		
	/*	echo "<tr><td style=border-bottom:none colspan=2>";
		echo "<a style='top:16px; position:relative;' onclick=window.parent.ConfirmDelete(this,\"$PHP_SELF?action=DELETE_CUSTOM_FIELD_CONFIRMATION&list_id=$list_id&field_id=$A_field_id[$o]&field_label=$A_field_label[$o]&DB=$DB\"); href=><img style=float:left src='../images/icons/shape_square_delete.png' /></a>";
		echo "<input type=submit class=styled-button name=submit value=\"SUBMIT\">";
		echo "</td></tr>\n"; */
		
		
		
		
		
		
		echo "</table></form>";

	/*	$o++;
		}*/
}

}	

########################################
#	NOVO CAMPO PERSONALIZADO
########################################


if ($navigation == "novo") 
{	
	

	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../images/icons/script_add.png' /></td>";
	echo "<td id='submenu-title'> Adicionar Novo Campo Dinâmico </td>";
	echo "<td style='text-align:left'>Menu que permite a criação de Campos Dinâmicos.</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";

	echo "<div id=work-area>";
	echo "<br><br>";
	
	echo "<div class=cc-mstyle style='border:none'>";
	
	echo "<form action=$PHP_SELF method=POST>\n";
	echo "<table>";


	echo "<input type=hidden name=action value=ADD_CUSTOM_FIELD>\n";
	echo "<input type=hidden name=list_id value=$list_id>\n";
	echo "<input type=hidden name=DB value=$DB>\n";
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Ordem Horizontal</p></div></td><td><select size=1 name=field_rank>";
	echo "$rank_select";
	echo "<option selected>$last_rank</option>";
	echo "</select><td style=width:30px>$NWB#vicidial_lists_fields-field_rank$NWE</td> \n";
	echo "<td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Ordem Lateral</p></div></td><td><select size=1 name=field_order>\n";
	echo "<option selected>1</option>\n";
	echo "<option>2</option>\n";
	echo "<option>3</option>\n";
	echo "<option>4</option>\n";
	echo "<option>5</option>\n";
	echo "</select></td><td style=width:30px>$NWB#vicidial_lists_fields-field_order$NWE </td></tr>\n";
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>ID do Campo</p></div></td><td colspan=4><input onkeypress='return alpha(event);'type=text name=field_label style=width:300px maxlength=50><td style=width:30px;> $NWB#vicidial_lists_fields-field_label$NWE </td></tr>";
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Tipo de Campo</p></div></td><td colspan=4><select size=1 style=width:300px name=field_type>\n";
	echo "<option selected value=TEXT>Texto</option>\n";
	echo "<option value=AREA>Caixa de Texto</option>\n";
	echo "<option value=SELECT>Combo-Box</option>\n";
	echo "<option value=MULTI>Combo-Box com vários valores</option>\n";
	echo "<option value=RADIO>Botão Radio</option>\n";
	echo "<option value=CHECKBOX>Check box</option>\n";
	echo "<option value=DATE>Campo de Data</option>\n";
	echo "<option value=TIME>Campo de Hora</option>\n";
	echo "<option value=DISPLAY>Mostrar Texto sem Variáveis</option>\n";
	echo "<option value=SCRIPT>Mostrar Texto com Variáveis</option>\n";
	echo "</select><td style=width:30px;>  $NWB#vicidial_lists_fields-field_type$NWE </td></tr>\n";
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Descrição do Campo</p></div></td><td colspan=4><input name=field_description type=text size=70 maxlength=100><td style=width:30px;> $NWB#vicidial_lists_fields-field_description$NWE </td></tr>\n";
	
		echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Posição do Conteudo</p></div></td><td colspan=4><select size=1 name=name_position>\n";
	echo "<option value=\"LEFT\">Esquerda</option>\n";
	echo "<option value=\"TOP\">Topo</option>\n";
	echo "</select><td style=width:30px;>  $NWB#vicidial_lists_fields-name_position$NWE </td></tr>\n";
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Texto do Campo</p></div></td><td colspan=4><textarea style=height:125px name=field_name rows=2 cols=60></textarea><td style=width:30px;> $NWB#vicidial_lists_fields-field_name$NWE </td></tr>\n";
	
	


	
	
	
	
	
	
	
	
	
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Posição da Opção</p></div></td><td colspan=4><select size=1 name=multi_position>\n";
	echo "<option selected value=\"HORIZONTAL\">Horizontal</option>\n";
	echo "<option value=\"VERTICAL\">Vertical</option>\n";
	echo "</select><td style=width:30px;>  $NWB#vicidial_lists_fields-multi_position$NWE </td></tr>\n";
	
	echo "<tr><td>Funcionalidades da Opção</td><td colspan=4><select size=1 name=multi_position>\n";
		echo "<option value=\"\">&nbsp</option>\n";
                echo "<option $selected value=\"HORIZONTAL\">Esconde o elemento abaixo</option>\n";
		echo "</select><td style=width:30px;>  $NWB#vicidial_lists_fields-multi_position$NWE </td></tr>\n";
	
	
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Opções do Campo</p></div></td><td colspan=4><textarea style=height:125px name=field_options></textarea><td style=width:30px;>  $NWB#vicidial_lists_fields-field_options$NWE <a href=\"javascript:openNewWindow('/sipsproject/sips-admin/help/custom_help.php','Ajuda Scripts', 'scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');\" ><img src='../images/icons/script_add.png' title='Ajuda HTML'></a></td></tr>\n";
	

	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Tamanho do Campo</p></div></td><td colspan=4><input type=text name=field_size size=5 maxlength=3><td style=width:30px;>  $NWB#vicidial_lists_fields-field_size$NWE </td></tr>\n";
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Tamanho Máximo</p></div></td><td colspan=4><input type=text name=field_max size=5 maxlength=3><td style=width:30px>  $NWB#vicidial_lists_fields-field_max$NWE </td></tr>\n";
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Valor por Defeito</p></div></td><td colspan=4><input type=text name=field_default size=50 maxlength=255 value=\"NULL\"> <td style='width:30px'> $NWB#vicidial_lists_fields-field_default$NWE </td></tr>\n";
	
	echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Campo Óbrigatório</p></div></td><td colspan=4><select size=1 name=field_required>\n";
	echo "<option value=\"Y\">Sim</option>\n";
	echo "<option value=\"N\" SELECTED>Não</option>\n";
	echo "</select><td style=width:30px>  $NWB#vicidial_lists_fields-field_required$NWE </td></tr>\n";
	
		echo "<tr><td style='min-width:175px'> <div class=cc-mstyle style='height:28px;  '><p>Ajuda do Campo</p></div></td><td colspan=4><textarea name=field_help style=height:125px></textarea><td style=width:30px;> $NWB#vicidial_lists_fields-field_help$NWE </td></tr>\n";
	
	

	echo "</TABLE>";
	
	echo "<br>";
	
	echo "<table><tr>
		<td style=text-align:right>Cancelar</td>
		<td><img style=float:left src='../images/icons/shape_square_delete.png' /></td>
		
		<td style=text-align:right>Gravar</td>
		<td><input type=image style='float:left' src='../images/icons/shape_square_add.png' alt=Gravar name=SUBMIT></td>
		</tr></table>";
	
echo "</form>";
	} 
####################################
#	
####################################

### END modify custom fields for list




################################################################################
##### BEGIN list lists as well as the number of custom fields in each list
if ($action == "LIST")
	{
    
    $grupos = mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups INNER JOIN vicidial_users ON vicidial_user_groups.user_group = vicidial_users.user_group WHERE user = '$user'",$link) or die(mysql_error());

	$row=mysql_fetch_row($grupos);
	$LOGallowed_campaigns = $row[0];
  
    
        $rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
  
  	$campanhas = "'".$rawLOGallowed_campaignsSQL."'";
        
	$stmt="SELECT campaign_id,campaign_name,active from vicidial_campaigns WHERE campaign_id IN ($campanhas) order by campaign_id;";
	$rslt=mysql_query($stmt, $link) or die(mysql_error());
	$lists_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($lists_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$A_campaign_id[$o] =		$rowx[0];
		$A_campaign_name[$o] =		$rowx[1];
		$A_active[$o] =			$rowx[2];
		$A_type[$o] = 'Outbound';
		$o++;
		}
        $stmt="SELECT group_id,group_name,active from vicidial_inbound_groups;";
	$rslt=mysql_query($stmt, $link) or die(mysql_error());
	$inb_to_print = mysql_num_rows($rslt); 
        $counter = 0;
        $total_camps = $lists_to_print + $inb_to_print;
        while ($inb_to_print > $counter) 
		{
		$rowx=mysql_fetch_row($rslt);
		$A_campaign_id[$o] =		$rowx[0];
		$A_campaign_name[$o] =		$rowx[1];
		$A_active[$o] =			$rowx[2];
		$A_type[$o] = 'Inbound';
                $o++;
                $counter++;
		}

	echo "<div class=cc-mstyle>";
	echo "<table >";
	echo "<tr>";
	echo "<td id='icon32'><img src='../images/icons/script_edit.png' /></td>";
	echo "<td id='submenu-title'> Campos Dinâmicos </td>";
	echo "<td style='text-align:left'>Menu que permite a configuração dinâmica de Scripts. </td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br>";
		
	echo "<div class=cc-mstyle>";	
		
		
	echo "<table id='custom_fields'>\n";
	echo "<thead><tr>";
	#echo "<th> Nome da BD </th>";
	echo "<th> Nome da Campanha </th>";
	echo "<th> Activa </th>";
	
	echo "<th> Campos Dinâmicos </th>\n";
        echo "<th> Tipo </th>\n";
	echo "<th> Editar </th>\n";
	echo "</TR></thead><tbody>\n";

	$o=0;
	while ($total_camps > $o) 
		{
		$A_list_fields_count[$o]=0;
		$stmt="SELECT count(*) from vicidial_lists_fields where campaign_id='$A_campaign_id[$o]';";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_query($stmt, $link);
		$fieldscount_to_print = mysql_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysql_fetch_row($rslt);
			$A_list_fields_count[$o] =	$rowx[0];
			}
		
		#echo "<tr><td><font size=1><a href=\"admin.php?ADD=311&list_id=$A_campaign_id[$o]\">$A_campaign_id[$o]</a></td>";
		echo "<td> $A_campaign_name[$o]</td>";
		
		if ($A_active[$o] == "Y") { $img_active = "<img src=../images/icons/tick_16.png />"; } else { $img_active = "<img src=../images/icons/cross_16.png />"; }
		
		echo "<td> $img_active </td>";
		
		echo "<td> $A_list_fields_count[$o]</td>";
                echo "<td> $A_type[$o] </td>";
		echo "<td><a href=\"$PHP_SELF?action=MODIFY_CUSTOM_FIELDS&list_id=$A_campaign_id[$o]&navigation=resumo\"><img src='../images/icons/livejournal.png' /></a></td></tr>\n";

		$o++;
		}
	echo "</tbody>";
	echo "</TABLE></center>\n";
	}
### END list lists as well as the number of custom fields in each list





################################################################################
##### BEGIN admin log display
if ($action == "ADMIN_LOG")
	{
	if ($LOGuser_level >= 9)
		{
		echo "<TABLE><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$stmt="SELECT admin_log_id,event_date,user,ip_address,event_section,event_type,record_id,event_code from vicidial_admin_log where event_section='CUSTOM_FIELDS' and record_id='$list_id' order by event_date desc limit 10000;";
		$rslt=mysql_query($stmt, $link);
		$logs_to_print = mysql_num_rows($rslt);

		echo "<br>ADMIN CHANGE LOG: Section Records - $category - $stage\n";
		echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
		echo "<TR BGCOLOR=BLACK>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>ID</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>DATE TIME</B></TD>";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>USER</B></TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>IP</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>SECTION</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>TYPE</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>RECORD ID</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>DESCRIPTION</TD>\n";
		echo "<TD><B><FONT FACE=\"Arial,Helvetica\" size=1 color=white>GOTO</TD>\n";
		echo "</TR>\n";

		$logs_printed = '';
		$o=0;
		while ($logs_to_print > $o)
			{
			$row=mysql_fetch_row($rslt);

			if (eregi("USER|AGENT",$row[4])) {$record_link = "ADD=3&user=$row[6]";}
			if (eregi('CAMPAIGN',$row[4])) {$record_link = "ADD=31&campaign_id=$row[6]";}
			if (eregi('LIST',$row[4])) {$record_link = "ADD=311&list_id=$row[6]";}
			if (eregi('SCRIPT',$row[4])) {$record_link = "ADD=3111111&script_id=$row[6]";}
			if (eregi('FILTER',$row[4])) {$record_link = "ADD=31111111&lead_filter_id=$row[6]";}
			if (eregi('INGROUP',$row[4])) {$record_link = "ADD=3111&group_id=$row[6]";}
			if (eregi('DID',$row[4])) {$record_link = "ADD=3311&did_id=$row[6]";}
			if (eregi('USERGROUP',$row[4])) {$record_link = "ADD=311111&user_group=$row[6]";}
			if (eregi('REMOTEAGENT',$row[4])) {$record_link = "ADD=31111&remote_agent_id=$row[6]";}
			if (eregi('PHONE',$row[4])) {$record_link = "ADD=10000000000";}
			if (eregi('CALLTIME',$row[4])) {$record_link = "ADD=311111111&call_time_id=$row[6]";}
			if (eregi('SHIFT',$row[4])) {$record_link = "ADD=331111111&shift_id=$row[6]";}
			if (eregi('CONFTEMPLATE',$row[4])) {$record_link = "ADD=331111111111&template_id=$row[6]";}
			if (eregi('CARRIER',$row[4])) {$record_link = "ADD=341111111111&carrier_id=$row[6]";}
			if (eregi('SERVER',$row[4])) {$record_link = "ADD=311111111111&server_id=$row[6]";}
			if (eregi('CONFERENCE',$row[4])) {$record_link = "ADD=1000000000000";}
			if (eregi('SYSTEM',$row[4])) {$record_link = "ADD=311111111111111";}
			if (eregi('CATEGOR',$row[4])) {$record_link = "ADD=331111111111111";}
			if (eregi('GROUPALIAS',$row[4])) {$record_link = "ADD=33111111111&group_alias_id=$row[6]";}

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#dddddd"';} 
			else
				{$bgcolor='bgcolor="#cccccc"';}
			echo "<tr $bgcolor><td><font size=1><a href=\"admin.php?ADD=730000000000000&stage=$row[0]\">$row[0]</a></td>";
			echo "<td><font size=1> $row[1]</td>";
			echo "<td><font size=1> <a href=\"admin.php?ADD=710000000000000&stage=$row[2]\">$row[2]</a></td>";
			echo "<td><font size=1> $row[3]</td>";
			echo "<td><font size=1> $row[4]</td>";
			echo "<td><font size=1> $row[5]</td>";
			echo "<td><font size=1> $row[6]</td>";
			echo "<td><font size=1> $row[7]</td>";
			echo "<td><font size=1> <a href=\"admin.php?$record_link\">GOTO</a></td>";
			echo "</tr>\n";
			$logs_printed .= "'$row[0]',";
			$o++;
			}
		echo "</TABLE><BR><BR>\n";
		echo "\n";
		echo "</center>\n";
		}
	else
		{
		echo "You do not have permission to view this page\n";
		exit;
		}
	}





/*$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "\n\n\n<br><br><br>\n<font size=1> runtime: $RUNtime seconds &nbsp; &nbsp; &nbsp; &nbsp; Version: $admin_version &nbsp; &nbsp; Build: $build</font>"; */

echo "</TD></TR></TABLE>\n";
echo "</TD></TR></TABLE>\n";
echo "</TD></TR></TABLE>\n";
/* require("admin_footer.php"); */
?>

</body>
</html>


<?php
################################################################################
################################################################################
##### Functions
################################################################################
################################################################################




################################################################################
##### BEGIN add field function
function add_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields)
	{
	$table_exists=0;
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";"; 
	$rslt=mysql_query($stmt, $link);
	$tablecount_to_print = mysql_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{$table_exists =	1;}
	if ($DB>0) {echo "$stmt|$tablecount_to_print|$table_exists";}

	if ($table_exists < 1)
		{$field_sql = "CREATE TABLE custom_$list_id (lead_id INT(9) UNSIGNED PRIMARY KEY NOT NULL, $field_label ";}
	else
		{$field_sql = "ALTER TABLE custom_$list_id ADD $field_label ";}

	$field_options_ENUM='';
	$field_cost=1;
	if ( ($field_type=='SELECT') or ($field_type=='RADIO') )
		{
		$field_options_array = explode("\n",$field_options);
		$field_options_count = count($field_options_array);
		$te=0;
		while ($te < $field_options_count)
			{
			if (preg_match("/,/",$field_options_array[$te]))
				{
				$field_options_value_array = explode(",",$field_options_array[$te]);
				$field_options_ENUM .= "'$field_options_value_array[0]',";
				}
			$te++;
			}
		$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
		$field_sql .= "ENUM($field_options_ENUM) ";
		$field_cost = strlen($field_options_ENUM);
		}
	if ( ($field_type=='MULTI') or ($field_type=='CHECKBOX') )
		{
		$field_options_array = explode("\n",$field_options);
		$field_options_count = count($field_options_array);
		$te=0;
		while ($te < $field_options_count)
			{
			if (preg_match("/,/",$field_options_array[$te]))
				{
				$field_options_value_array = explode(",",$field_options_array[$te]);
				$field_options_ENUM .= "'$field_options_value_array[0]',";
				}
			$te++;
			}
		$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
		$field_cost = strlen($field_options_ENUM);
		if ($field_cost < 1) {$field_cost=1;};
		$field_sql .= "VARCHAR($field_cost) ";
		}
	if ($field_type=='TEXT') 
		{
		if ($field_max < 1) {$field_max=1;};
		$field_sql .= "VARCHAR($field_max) ";
		$field_cost = ($field_max + $field_cost);
		}
	if ($field_type=='AREA') 
		{
		$field_sql .= "TEXT ";
		$field_cost = 15;
		}
	if ($field_type=='DATE') 
		{
		$field_sql .= "DATE ";
		$field_cost = 10;
		}
	if ($field_type=='TIME') 
		{
		$field_sql .= "TIME ";
		$field_cost = 8;
		}
	$field_cost = ($field_cost * 3); # account for utf8 database

	if ( ($field_default != 'NULL') and ($field_type!='AREA') and ($field_type!='DATE') and ($field_type!='TIME') )
		{$field_sql .= "default '$field_default'";}

	if ($table_exists < 1)
		{$field_sql .= ");";}
	else
		{$field_sql .= ";";}

	if ( ($field_type=='DISPLAY') or ($field_type=='SCRIPT') or (preg_match("/\|$field_label\|/",$vicidial_list_fields)) )
		{
		if ($DB) {echo "Non-DB $field_type field type, $field_label\n";} 
		}
	else
		{
		$stmtCUSTOM="$field_sql"; 
		$rsltCUSTOM=mysql_query($stmtCUSTOM, $linkCUSTOM);
		$table_update = mysql_affected_rows($linkCUSTOM);
		if ($DB) {echo "$table_update|$stmtCUSTOM\n";}
		if (!$rsltCUSTOM) {echo('Could not execute: ' . mysql_error());}
		}

	#working
	
		#$list_id = ereg_replace("[^0-9]","",$list_id);
/*	$field_id = ereg_replace("[^0-9]","",$field_id);
	
	$field_size = ereg_replace("[^0-9]","",$field_size);
	$field_max = ereg_replace("[^0-9]","",$field_max);
	$field_order = ereg_replace("[^0-9]","",$field_order);
	$source_list_id = ereg_replace("[^0-9]","",$source_list_id);

	$field_required = ereg_replace("[^NY]","",$field_required);

	$field_type = ereg_replace("[^0-9a-zA-Z]","",$field_type);
	$ConFiRm = ereg_replace("[^0-9a-zA-Z]","",$ConFiRm);
	$name_position = ereg_replace("[^0-9a-zA-Z]","",$name_position);
	$multi_position = ereg_replace("[^0-9a-zA-Z]","",$multi_position);


	$copy_option = ereg_replace("[^_0-9a-zA-Z]","",$copy_option);

	
	
	$field_options = ereg_replace("[^ \.\n\,-\_0-9a-zA-Z]","",$field_options);
	$field_default = ereg_replace("[^ \.\n\,-\_0-9a-zA-Z]","",$field_default); */
	
/*	$field_label = utf8_decode($field_label);
	$field_name = utf8_decode($field_name);
	$field_description = utf8_decode($field_description);
	$field_rank = utf8_decode($field_rank);
	$field_help = utf8_decode($field_help);
	$field_type = utf8_decode($field_type);
	$field_options = utf8_decode($field_options); */
	

	$stmt="INSERT INTO vicidial_lists_fields set field_label='$field_label',field_name='$field_name',field_description='$field_description',field_rank='$field_rank',field_help='$field_help',field_type='$field_type',field_options='$field_options',field_size='$field_size',field_max='$field_max',field_default='$field_default',field_required='$field_required',field_cost='$field_cost',campaign_id='$list_id',multi_position='$multi_position',name_position='$name_position',field_order='$field_order';";
	$rslt=mysql_query($stmt, $link);
	
	$field_update = mysql_affected_rows($link);
	if ($DB) {echo "$field_update|$stmt\n";}
	if (!$rslt) {echo('Could not execute: ' . mysql_error());}

	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|$stmtCUSTOM";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date=NOW(), user='$user', ip_address='$ip', event_section='CUSTOM_FIELDS', event_type='ADD', record_id='$list_id', event_code='ADMIN ADD CUSTOM LIST FIELD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	}
##### END add field function





################################################################################
##### BEGIN modify field function
function modify_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields)
	{
	$field_sql = "ALTER TABLE custom_$list_id MODIFY $field_label ";
	$field_options_ENUM='';
	$field_cost=1;
	if ( ($field_type=='SELECT') or ($field_type=='RADIO') )
		{
		$field_options_array = explode("\n",$field_options);
		$field_options_count = count($field_options_array);
		$te=0;
		while ($te < $field_options_count)
			{
			if (preg_match("/,/",$field_options_array[$te]))
				{
				$field_options_value_array = explode(",",$field_options_array[$te]);
				$field_options_ENUM .= "'$field_options_value_array[0]',";
				}
			$te++;
			}
		$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
		$field_sql .= "ENUM($field_options_ENUM) ";
		$field_cost = strlen($field_options_ENUM);
		}
	if ( ($field_type=='MULTI') or ($field_type=='CHECKBOX') )
		{
		$field_options_array = explode("\n",$field_options);
		$field_options_count = count($field_options_array);
		$te=0;
		while ($te < $field_options_count)
			{
			if (preg_match("/,/",$field_options_array[$te]))
				{
				$field_options_value_array = explode(",",$field_options_array[$te]);
				$field_options_ENUM .= "'$field_options_value_array[0]',";
				}
			$te++;
			}
		$field_options_ENUM = preg_replace("/.$/",'',$field_options_ENUM);
		$field_cost = strlen($field_options_ENUM);
		$field_sql .= "VARCHAR($field_cost) ";
		}
	if ($field_type=='TEXT') 
		{
		$field_sql .= "VARCHAR($field_max) ";
		$field_cost = ($field_max + $field_cost);
		}
	if ($field_type=='AREA') 
		{
		$field_sql .= "TEXT ";
		$field_cost = 15;
		}
	if ($field_type=='DATE') 
		{
		$field_sql .= "DATE ";
		$field_cost = 10;
		}
	if ($field_type=='TIME') 
		{
		$field_sql .= "TIME ";
		$field_cost = 8;
		}
	$field_cost = ($field_cost * 3); # account for utf8 database

	if ( ($field_default == 'NULL') or ($field_type=='AREA') or ($field_type=='DATE') or ($field_type=='TIME') )
		{$field_sql .= ";";}
	else
		{$field_sql .= "default '$field_default';";}

	if ( ($field_type=='DISPLAY') or ($field_type=='SCRIPT') or (preg_match("/\|$field_label\|/",$vicidial_list_fields)) )
		{
		if ($DB) {echo "Non-DB $field_type field type, $field_label\n";} 
		}
	else
		{
		$stmtCUSTOM="$field_sql";
		$rsltCUSTOM=mysql_query($stmtCUSTOM, $linkCUSTOM);
		$field_update = mysql_affected_rows($linkCUSTOM);
		if ($DB) {echo "$field_update|$stmtCUSTOM\n";}
		if (!$rsltCUSTOM) {echo('Could not execute: ' . mysql_error());}
		}

	$stmt="UPDATE vicidial_lists_fields set field_label='$field_label',field_name='$field_name',field_description='$field_description',field_rank='$field_rank',field_help='$field_help',field_type='$field_type',field_options='$field_options',field_size='$field_size',field_max='$field_max',field_default='$field_default',field_required='$field_required',field_cost='$field_cost',multi_position='$multi_position',name_position='$name_position',field_order='$field_order' where campaign_id='$list_id' and field_id='$field_id';";
	$rslt=mysql_query($stmt, $link);
	$field_update = mysql_affected_rows($link);
	if ($DB) {echo "$field_update|$stmt\n";}
	if (!$rslt) {echo('Could not execute: ' . mysql_error());}

	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|$stmtCUSTOM";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date=NOW(), user='$user', ip_address='$ip', event_section='CUSTOM_FIELDS', event_type='MODIFY', record_id='$list_id', event_code='ADMIN MODIFY CUSTOM LIST FIELD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	}
##### END modify field function





################################################################################
##### BEGIN delete field function
function delete_field_function($DB,$link,$linkCUSTOM,$ip,$user,$table_exists,$field_id,$list_id,$field_label,$field_name,$field_description,$field_rank,$field_help,$field_type,$field_options,$field_size,$field_max,$field_default,$field_required,$field_cost,$multi_position,$name_position,$field_order,$vicidial_list_fields)
	{
	if ( ($field_type=='DISPLAY') or ($field_type=='SCRIPT') or (preg_match("/\|$field_label\|/",$vicidial_list_fields)) )
		{
		if ($DB) {echo "Non-DB $field_type field type, $field_label\n";} 
		}
	else
		{
		$stmtCUSTOM="ALTER TABLE custom_$list_id DROP $field_label;";
		$rsltCUSTOM=mysql_query($stmtCUSTOM, $linkCUSTOM);
		$table_update = mysql_affected_rows($linkCUSTOM);
		if ($DB) {echo "$table_update|$stmtCUSTOM\n";}
		if (!$rsltCUSTOM) {echo('Could not execute: ' . mysql_error());}
		}

	$stmt="DELETE FROM vicidial_lists_fields WHERE field_label='$field_label' and field_id='$field_id' and campaign_id='$list_id' LIMIT 1;";
	$rslt=mysql_query($stmt, $link);
	$field_update = mysql_affected_rows($link);
	if ($DB) {echo "$field_update|$stmt\n";}
	if (!$rslt) {echo('Could not execute: ' . mysql_error());}

	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|$stmtCUSTOM";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date=NOW(), user='$user', ip_address='$ip', event_section='CUSTOM_FIELDS', event_type='DELETE', record_id='$list_id', event_code='ADMIN DELETE CUSTOM LIST FIELD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	}
##### END delete field function

/* NEEDS TO BE REDONE --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	### EXAMPLE OF CUSTOM FORM ###
	echo "<form action=$PHP_SELF method=POST name=form_custom_$list_id id=form_custom_$list_id>\n";
	echo "<h10> EXEMPLO DO FORMULÁRIO </h10>";
	echo "<center><TABLE cellspacing=2 cellpadding=2>\n";
	if ($fields_to_print < 1) 
		{echo "<tr bgcolor=white align=center><td colspan=4><font size=1>There are no custom fields for this list</td></tr>";}

	$o=0;
	$last_field_rank=0;
	while ($fields_to_print > $o) 
		{
		if ($last_field_rank=="$A_field_rank[$o]")
			{echo " &nbsp; &nbsp; &nbsp; &nbsp; ";}
		else
			{
			echo "</td></tr>\n";
			echo "<tr bgcolor=white><td align=";
			if ($A_name_position[$o]=='TOP') 
				{echo "left colspan=2";}
			else
				{echo "right";}
			echo "><font size=2>";
			}
		echo "<a href=\"#ANCHOR_$A_field_label[$o]\"><B>$A_field_name[$o]</B></a>";
		if ($A_name_position[$o]=='TOP') 
			{
			$helpHTML = "<a href=\"javascript:open_help('HELP_$A_field_label[$o]','$A_field_help[$o]');\">help+</a>";
			if (strlen($A_field_help[$o])<1)
				{$helpHTML = '';}
			echo " &nbsp; <span style=\"position:static;\" id=P_HELP_$A_field_label[$o]></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label[$o]> &nbsp; $helpHTML</span><BR>";
			}
		else
			{
			if ($last_field_rank=="$A_field_rank[$o]")
				{echo " &nbsp;";}
			else
				{echo "</td><td align=left><font size=2>";}
			}
		$field_HTML='';

		if ($A_field_type[$o]=='SELECT')
			{
			$field_HTML .= "<select size=1 name=$A_field_label[$o] id=$A_field_label[$o]>\n";
			}
		if ($A_field_type[$o]=='MULTI')
			{
			$field_HTML .= "<select MULTIPLE size=$A_field_size[$o] name=$A_field_label[$o] id=$A_field_label[$o]>\n";
			}
		if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') or ($A_field_type[$o]=='RADIO') or ($A_field_type[$o]=='CHECKBOX') )
			{
			$field_options_array = explode("\n",$A_field_options[$o]);
			$field_options_count = count($field_options_array);
			$te=0;
			while ($te < $field_options_count)
				{
				if (preg_match("/,/",$field_options_array[$te]))
					{
					$field_selected='';
					$field_options_value_array = explode(",",$field_options_array[$te]);
					if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') )
						{
						if ($A_field_default[$o] == "$field_options_value_array[0]") {$field_selected = 'SELECTED';}
						$field_HTML .= "<option value=\"$field_options_value_array[0]\" $field_selected>$field_options_value_array[1]</option>\n";
						}
					if ( ($A_field_type[$o]=='RADIO') or ($A_field_type[$o]=='CHECKBOX') )
						{
						if ($A_multi_position[$o]=='VERTICAL') 
							{$field_HTML .= " &nbsp; ";}
						if ($A_field_default[$o] == "$field_options_value_array[0]") {$field_selected = 'CHECKED';}
						$field_HTML .= "<input type=$A_field_type[$o] name=$A_field_label[$o][] id=$A_field_label[$o][] value=\"$field_options_value_array[0]\" $field_selected> $field_options_value_array[1]\n";
						if ($A_multi_position[$o]=='VERTICAL') 
							{$field_HTML .= "<BR>\n";}
						}
					}
				$te++;
				}
			}
		if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') )
			{
			$field_HTML .= "</select>\n";
			}
		if ($A_field_type[$o]=='TEXT') 
			{
			if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
			$field_HTML .= "<input type=text size=$A_field_size[$o] maxlength=$A_field_max[$o] name=$A_field_label[$o] id=$A_field_label[$o] value=\"$A_field_default[$o]\">\n";
			}
		if ($A_field_type[$o]=='AREA') 
			{
			$field_HTML .= "<textarea name=$A_field_label[$o] id=$A_field_label[$o] ROWS=$A_field_max[$o] COLS=$A_field_size[$o]></textarea>";
			}
		if ($A_field_type[$o]=='DISPLAY')
			{
			if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
			$field_HTML .= "$A_field_default[$o]\n";
			}
		if ($A_field_type[$o]=='SCRIPT')
			{
			if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
			$field_HTML .= "$A_field_options[$o]\n";
			}
		if ($A_field_type[$o]=='DATE') 
			{
			if ( (strlen($A_field_default[$o])<1) or ($A_field_default[$o]=='NULL') ) {$A_field_default[$o]=0;}
			$day_diff = $A_field_default[$o];
			$default_date = date("Y-m-d", mktime(date("H"),date("i"),date("s"),date("m"),date("d")+$day_diff,date("Y")));

			$field_HTML .= "<input type=text size=11 maxlength=10 name=$A_field_label[$o] id=$A_field_label[$o] value=\"$default_date\">\n";
			$field_HTML .= "<script language=\"JavaScript\">\n";
			$field_HTML .= "var o_cal = new tcal ({\n";
			$field_HTML .= "	'formname': 'form_custom_$list_id',\n";
			$field_HTML .= "	'controlname': '$A_field_label[$o]'});\n";
			$field_HTML .= "o_cal.a_tpl.yearscroll = false;\n";
			$field_HTML .= "</script>\n";
			}
		if ($A_field_type[$o]=='TIME') 
			{
			$minute_diff = $A_field_default[$o];
			$default_time = date("H:i:s", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
			$default_hour = date("H", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
			$default_minute = date("i", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
			$field_HTML .= "<input type=hidden name=$A_field_label[$o] id=$A_field_label[$o] value=\"$default_time\">";
			$field_HTML .= "<SELECT name=HOUR_$A_field_label[$o] id=HOUR_$A_field_label[$o]>";
			$field_HTML .= "<option>00</option>";
			$field_HTML .= "<option>01</option>";
			$field_HTML .= "<option>02</option>";
			$field_HTML .= "<option>03</option>";
			$field_HTML .= "<option>04</option>";
			$field_HTML .= "<option>05</option>";
			$field_HTML .= "<option>06</option>";
			$field_HTML .= "<option>07</option>";
			$field_HTML .= "<option>08</option>";
			$field_HTML .= "<option>09</option>";
			$field_HTML .= "<option>10</option>";
			$field_HTML .= "<option>11</option>";
			$field_HTML .= "<option>12</option>";
			$field_HTML .= "<option>13</option>";
			$field_HTML .= "<option>14</option>";
			$field_HTML .= "<option>15</option>";
			$field_HTML .= "<option>16</option>";
			$field_HTML .= "<option>17</option>";
			$field_HTML .= "<option>18</option>";
			$field_HTML .= "<option>19</option>";
			$field_HTML .= "<option>20</option>";
			$field_HTML .= "<option>21</option>";
			$field_HTML .= "<option>22</option>";
			$field_HTML .= "<option>23</option>";
			$field_HTML .= "<OPTION value=\"$default_hour\" selected>$default_hour</OPTION>";
			$field_HTML .= "</SELECT>";
			$field_HTML .= "<SELECT name=MINUTE_$A_field_label[$o] id=MINUTE_$A_field_label[$o]>";
			$field_HTML .= "<option>00</option>";
			$field_HTML .= "<option>05</option>";
			$field_HTML .= "<option>10</option>";
			$field_HTML .= "<option>15</option>";
			$field_HTML .= "<option>20</option>";
			$field_HTML .= "<option>25</option>";
			$field_HTML .= "<option>30</option>";
			$field_HTML .= "<option>35</option>";
			$field_HTML .= "<option>40</option>";
			$field_HTML .= "<option>45</option>";
			$field_HTML .= "<option>50</option>";
			$field_HTML .= "<option>55</option>";
			$field_HTML .= "<OPTION value=\"$default_minute\" selected>$default_minute</OPTION>";
			$field_HTML .= "</SELECT>";
			}

		if ($A_name_position[$o]=='LEFT') 
			{
			$helpHTML = "<a href=\"javascript:open_help('HELP_$A_field_label[$o]','$A_field_help[$o]');\">help+</a>";
			if (strlen($A_field_help[$o])<1)
				{$helpHTML = '';}
			echo " $field_HTML <span style=\"position:static;\" id=P_HELP_$A_field_label[$o]></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label[$o]> &nbsp; $helpHTML</span>";
			}
		else
			{
			echo " $field_HTML\n";
			}

		$last_field_rank=$A_field_rank[$o];
		$o++;
		}
	echo "</td></tr></table></form></center><BR><BR>\n"; */


	### MODIFY FIELDS ###

 ?>
<script>
 var otable = $('#custom_fields').dataTable({
     "bJQueryUI": true,
     "sDom": 'l<"top"f>rt<"bottom"p>',
     "sPaginationType": "full_numbers",
     "aoColumns": [{
         "bSortable": true
     }, {
         "bSortable": true,
         "sType": "string"
     }, {
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": false
     }],
     "oLanguage": {
         "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
     }  
 });
</script>