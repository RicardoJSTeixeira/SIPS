<?php
# admin_search_lead.php   version 2.4
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# AST GUI database administration search for lead info
# admin_modify_lead.php
#
# this is the administration lead information search screen, the administrator 
# just needs to enter the leadID and then they can view and modify the information
# in the record for that lead
#
# changes:
# 60620-1055 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
#            - Changed results to multi-record
# 80710-0023 - Added searching by list, user, status
# 90121-0500 - Added filter for phone to remove non-digits
# 90309-1828 - Added admin_log logging
# 90310-2146 - Added admin header
# 90508-0644 - Changed to PHP long tags
# 90917-2307 - Added alternate phone number searching option
# 90921-0713 - Removed SELECT STAR
# 100224-1621 - Added first/last name search and changed format of the page
# 100405-1331 - Added log search ability
# 100622-0928 - Added field labels
# 110218-1237 - Added vicidial_lead_search_log logging
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["vendor_id"]))			{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))	{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))	{$first_name=$_POST["first_name"];}
if (isset($_GET["last_name"]))			{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))	{$last_name=$_POST["last_name"];}
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["lead_id"]))			{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))	{$lead_id=$_POST["lead_id"];}
if (isset($_GET["log_phone"]))				{$log_phone=$_GET["log_phone"];}
	elseif (isset($_POST["log_phone"]))		{$log_phone=$_POST["log_phone"];}
if (isset($_GET["log_lead_id"]))			{$log_lead_id=$_GET["log_lead_id"];}
	elseif (isset($_POST["log_lead_id"]))	{$log_lead_id=$_POST["log_lead_id"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["status"]))				{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))	{$status=$_POST["status"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["list_id"]))			{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))	{$list_id=$_POST["list_id"];}
if (isset($_GET["alt_phone_search"]))			{$alt_phone_search=$_GET["alt_phone_search"];}
	elseif (isset($_POST["alt_phone_search"]))	{$alt_phone_search=$_POST["alt_phone_search"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
$phone = ereg_replace("[^0-9]","",$phone);
if (strlen($alt_phone_search) < 2) {$alt_phone_search='No';}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

$vicidial_list_fields = 'lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner';

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
		$stmt="SELECT full_name,modify_leads,user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname =		$row[0];
		$LOGmodify_leads =	$row[1];
		$LOGuser_group =	$row[2];

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
        $stmt="SELECT allowed_campaigns,allowed_reports,admin_viewable_groups,admin_viewable_call_times from vicidial_user_groups where user_group='$LOGuser_group';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGallowed_campaigns =			$row[0];
$LOGallowed_reports =			$row[1];
$LOGadmin_viewable_groups =		$row[2];
$LOGadmin_viewable_call_times =	$row[3];

$camp_lists='';
$LOGallowed_campaignsSQL='';
$whereLOGallowed_campaignsSQL='';
$LOGallowed_listsSQL='';
$whereLOGallowed_listsSQL='';
if (!eregi("-ALL",$LOGallowed_campaigns))
	{
	$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
	$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
	$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";

	$stmt="SELECT list_id from vicidial_lists $whereLOGallowed_campaignsSQL;";
	$rslt=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($lists_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$camp_lists .= "'$rowx[0]',";
		$o++;
		}
	$camp_lists = eregi_replace(".$","",$camp_lists);
	if (strlen($camp_lists)<2) {$camp_lists="''";}
	$LOGallowed_listsSQL = "and list_id IN($camp_lists)";
	$LOGallowed_listsSQL2 = "and a.list_id IN($camp_lists)";
	$whereLOGallowed_listsSQL = "where list_id IN($camp_lists)";
	}
$regexLOGallowed_campaigns = " $LOGallowed_campaigns ";

?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<title>ADMINISTRATION: Lead Search
<?php 

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


#echo " Lead search: $vendor_id $phone $lead_id $status $list_id $user\n";
#echo date("l F j, Y G:i:s A");
#echo "<BR>\n";

if ( (!$vendor_id) and (!$phone)  and (!$lead_id) and (!$log_phone)  and (!$log_lead_id) and ( (strlen($status)<1) and (strlen($list_id)<1) and (strlen($user)<1) ) and ( (strlen($first_name)<1) and (strlen($last_name)<1) )) 
	{
	### Lead search
	
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../images/icons/application_form_magnify.png' /></td>";
	echo "<td id='submenu-title'> Procura de Contactos </td>";
	echo "<td style='text-align:left'></td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	
	echo "<div id=work-area>";
	echo "<br>";
	
	echo "<div class=cc-mstyle style='border:none'>";
	
	
	echo "<form method=post name=search action=\"$PHP_SELF\">\n";
	echo "<input type=hidden name=DB value=\"$DB\">\n";
	echo "<table>";
	echo "<tr>";
	echo "<TD colspan=3 align=center><b>Procura nas Base de Dados</td>";
	echo "</tr><tr>";

	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> ID do Vendedor </p></div></td><TD ALIGN=left><input type=text name=vendor_id size=10 maxlength=20></td>";
	#echo "<TD><input type=image src='../images/icons/application_form_magnify.png' name=submit value=SUBMIT></td>\n";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </td>";
	echo "</TR><TR>";

	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Telefone </p></div></td><TD ALIGN=left><input type=text name=phone size=14 maxlength=18></td>";
	#echo "<TD rowspan=2><input type=image src='../images/icons/application_form_magnify.png' name=submit value=SUBMIT></td>\n";
	echo "</TR><TR>";
	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Telefone Alternativo </p></div></td><TD ALIGN=left><select size=1 name=alt_phone_search><option>No</option><option>Yes</option><option SELECTED>$alt_phone_search</option></select></td>";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </td>";
	echo "</TR><TR>";

	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> ID do Contacto </p></div></td><TD ALIGN=left><input type=text name=lead_id size=10 maxlength=10></td>";
	#echo "<TD><input type=image src='../images/icons/application_form_magnify.png' name=submit value=SUBMIT></td>\n";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </td>";
	echo "</TR><TR>";

	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Estado </p></div></td><TD ALIGN=left><input type=text name=status size=7 maxlength=6></td>";
	#echo "<TD rowspan=3><input type=image src='../images/icons/application_form_magnify.png' name=submit value=SUBMIT></td>\n";
	echo "</TR><TR >";
	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> ID da Lista </p></div></td><TD ALIGN=left><input type=text name=list_id size=15 maxlength=14></td>";
	echo "</TR><TR >";
	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Colaborador </p></div></td><TD ALIGN=left><input type=text name=user size=15 maxlength=20></td>";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center> &nbsp; </td>";
	echo "</TR><TR >";

	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Primeiro Nome </p></div></td><TD ALIGN=left><input type=text name=first_name size=15 maxlength=30></td>";
	#echo "<TD rowspan=2><input type=image src='../images/icons/application_form_magnify.png' name=submit value=SUBMIT></td>\n";
	echo "</TR><TR >";
	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Ultimo Nome </p></div></td><TD ALIGN=left><input type=text name=last_name size=15 maxlength=30></td>";
	echo "</TR>";


	### Log search
	echo "<br><center>\n";
	echo "<TD colspan=3 align=center> &nbsp; </td>";
	echo "</TR><TR>";
	echo "<TD colspan=3 align=center><b>Procura nos Logs</td>";
	echo "</TR><TR>";

	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> ID do Contacto </p></div></td><TD ALIGN=left><input type=text name=log_lead_id size=10 maxlength=10></td>";
	#echo "<TD><input type=image src='../images/icons/application_form_magnify.png' name=submit value=SUBMIT></td>\n";
	echo "</TR>";
	echo "<TR>";

	echo "<td style='width:225px'> <div class=cc-mstyle style='height:28px;  '><p> Telefone </p></div></td><TD ALIGN=left><input type=text name=log_phone size=18 maxlength=18></td>";
	#echo "<TD><input type=image src='../images/icons/application_form_magnify.png' name=submit value=SUBMIT></td>\n";
	echo "</TR>";

echo "<tr><td>&nbsp;</td></tr>";
echo "<tr><td colspan=2 ><img style=float:right src='../images/icons/shape_square_delete.png' /></td><td><input type=image style='float:right' src='../images/icons/shape_square_add.png' alt=Gravar name=SUBMIT></td></tr>";

	
	echo "</TABLE>\n";
	echo "</form>\n</center>\n";
	echo "</td></TR></TABLE>\n";
	echo "</td></TR></TABLE>\n";
	echo "</td></TR></TABLE>\n";
	#require("admin_footer.php");
	echo "</body></html>\n";
	exit;
	}

else
	{
	##### BEGIN Log search #####
	if ( (strlen($log_lead_id)>0) or (strlen($log_phone)>0) )
		{
		if (strlen($log_lead_id)>0)
			{
			$stmtA="SELECT lead_id,phone_number,a.campaign_id,call_date,a.status,user,a.list_id,length_in_sec,alt_dial,b.status_name,c.list_name,d.campaign_name from 
			vicidial_log a inner join vicidial_campaign_statuses b on a.status = b.status 
			inner join vicidial_lists c on a.list_id = c.list_id
			inner join vicidial_campaigns d on a.campaign_id = d.campaign_id
			where lead_id='" . mysql_real_escape_string($log_lead_id) . "'  $LOGallowed_listsSQL2";
			$stmtB="SELECT lead_id,phone_number,campaign_id,call_date,status,user,list_id,length_in_sec from vicidial_closer_log where lead_id='" . mysql_real_escape_string($log_lead_id) . "'  $LOGallowed_listsSQL";
			}
		if (strlen($log_phone)>0)
			{
			$stmtA="SELECT lead_id,phone_number,a.campaign_id,call_date,a.status,user,a.list_id,length_in_sec,alt_dial,b.status_name,c.list_name,d.campaign_name from 
			vicidial_log a inner join vicidial_campaign_statuses b on a.status = b.status 
			inner join vicidial_lists c on a.list_id = c.list_id
			inner join vicidial_campaigns d on a.campaign_id = d.campaign_id
			where phone_number='" . mysql_real_escape_string($log_phone) . "' AND b.campaign_id = a.campaign_id  $LOGallowed_listsSQL2" ;
			$stmtB="SELECT lead_id,phone_number,campaign_id,call_date,status,user,list_id,length_in_sec from vicidial_closer_log where phone_number='" . mysql_real_escape_string($log_phone) . "'  $LOGallowed_listsSQL";
			$stmtC="SELECT extension,caller_id_number,did_id,call_date from vicidial_did_log where caller_id_number='" . mysql_real_escape_string($log_phone) . "'  $LOGallowed_listsSQL";
			}
		
		$rslt=mysql_query("$stmtA", $link) or die(mysql_error());
		$results_to_print = mysql_num_rows($rslt);
		if ( ($results_to_print < 1) and ($results_to_printX < 1) )
			{
			
			
			echo "<div class=cc-mstyle>";
			echo "<table>";
			echo "<tr>";
			echo "<td id='icon32'><img src='../images/icons/application_form_magnify.png' /></td>";
			echo "<td id='submenu-title'> Procura de Contactos </td>";
			echo "<td style='text-align:left'>Inserir Texto.</td>";
			echo "</tr>";
			echo "</table>";
			echo "</div>";
			
			
			echo "\n<br><br><center>\n";
			echo "<b>Não existem chamadas outbound para os critérios de procura inseridos.</b><br><br>\n";
			echo "</center>\n";
			}
		else
			{
			
			echo "<div class=cc-mstyle>";
			echo "<table>";
			echo "<tr>";
			echo "<td id='icon32'><img src='../images/icons/application_form_magnify.png' /></td>";
			echo "<td id='submenu-title'> Procura de Contactos </td>";
			echo "<td style='text-align:left'></td>";
			echo "</tr>";
			echo "</table>";
			echo "</div>";
			
			echo "<br>";
			
			echo "<div class=cc-mstyle>";
			
			

			echo "<table>";
			echo "<tr>";
			echo "<th>#</th>";
			echo "<th>ID do Contacto</th>";
			echo "<th>Telefone</th>";
			echo "<th>Campanha</th>";
			echo "<th>Data da Chamada</th>";
			echo "<th>Estado</th>";
			echo "<th>Colaborador</th>";
			echo "<th>Base de Dados</th>";
			echo "<th>Duração</th>";
			echo "<th>Dial</th>";
			echo "</TR>";
			$o=0;
			while ($results_to_print > $o)
				{
				$row=mysql_fetch_row($rslt);
				$o++;
				$search_lead = $row[0];
			
				echo "<tr>";
				echo "<td>$o</td>";
				echo "<td><a href=\"admin_modify_lead.php?lead_id=$row[0]\" target=\"_blank\">$row[0]</a></td>";
				echo "<td>$row[1]</td>";
				echo "<td>$row[11]</td>";
				echo "<td>$row[3]</td>";
				echo "<td>$row[9]</td>";
				echo "<td>$row[5]</td>";
				echo "<td>$row[10]</td>";
				echo "<td>".gmdate("H:i:s", $row[7])."</td>";
				if ($row[8] == "MANUAL") { $dM = "Chamada Manual"; } elseif ($row[8] == "MAIN") { $dM = "Telefone Principal"; } elseif ($row[8] == "ALT") { $dM = "Telefone Alternativo"; } elseif ($row[8]=="NONE") { $dM = "Nova Lead"; }
				echo "<td>$dM</td>";
				echo "</tr>";
				}
			echo "<th></th>";
			echo "</table>";
			echo "</div>";
			}

		$rslt=mysql_query("$stmtB", $link);
		$results_to_print = mysql_num_rows($rslt);
		if ( ($results_to_print < 1) and ($results_to_printX < 1) )
			{
			echo "\n<br><br><center>\n";
			echo "<b>Não existem chamadas inbound para os critérios de procura inseridos.</b><br><br>\n";
			echo "</center>\n";
			}
		else
			{
			echo "<div class=cc-mstyle>";
			echo "<table>";
			echo "<tr>";
			echo "<td id='icon32'><img src='../images/icons/application_form_magnify.png' /></td>";
			echo "<td id='submenu-title'> Procura de Contactos </td>";
			echo "<td style='text-align:left'></td>";
			echo "</tr>";
			echo "</table>";
			echo "</div>";
			
			echo "<br>";
			
			echo "<div class=cc-mstyle>";
			
			

			echo "<table>";
			echo "<tr>";
			echo "<th>#</th>";
			echo "<th>ID do Contacto</B></th>";
			echo "<th>Telephone</B> </th>";
			echo "<th>INGROUP</B></th>";
			echo "<th>Data da Chamada</B> </th>";
			echo "<th>Estado</th>";
			echo "<th>Colaborador</th>";
			echo "<th>ID do Contacto</th>";
			echo "<th>Duração</th>";
			echo "</TR>\n";
			$o=0;
			while ($results_to_print > $o)
				{
				$row=mysql_fetch_row($rslt);
				$o++;
				$search_lead = $row[0];
				if (eregi("1$|3$|5$|7$|9$", $o))
					{$bgcolor='bgcolor="#CCCCCC"';} 
				else
					{$bgcolor='bgcolor="#9BB9FB"';}
				echo "<tr>";
				echo "<td>$o</td>";
				echo "<td><a href=\"admin_modify_lead.php?lead_id=$row[0]\" target=\"_blank\">$row[0]</a></td>";
				echo "<td>$row[1]</td>";
				echo "<td>$row[2]</td>";
				echo "<td>$row[3]</td>";
				echo "<td>$row[4]</td>";
				echo "<td>$row[5]</td>";
				echo "<td>$row[6]</td>";
				echo "<td>".gmdate("H:i:s", $row[7])."</td>";
				echo "</tr>";
				}
			echo "</table>";
			}

		if (strlen($stmtC) > 10)
			{
			$rslt=mysql_query("$stmtC", $link);
			$results_to_print = mysql_num_rows($rslt);
			if ( ($results_to_print < 1) and ($results_to_printX < 1) )
				{
				echo "\n<br><br><center>\n";
				echo "<b>Não existem chamadas DID para os critérios de procura inseridos.</b><br><br>\n";
				echo "</center>\n";
				}
			else
				{
				echo "<div class=cc-mstyle>";
				echo "<table>";
				echo "<tr>";
				echo "<td id='icon32'><img src='../images/icons/application_form_magnify.png' /></td>";
				echo "<td id='submenu-title'> Pesquisa de Contactos </td>";
				echo "<td style='text-align:left'>Inserir Texto.</td>";
				echo "</tr>";
				echo "</table>";
				echo "</div>";
				
				echo "<BR><b>INBOUND DID LOG RESULTS: $results_to_print</b><BR>\n";
				echo "<TABLE BGCOLOR=WHITE CELLPADDING=1 CELLSPACING=0 WIDTH=770>\n";
				echo "<TR BGCOLOR=BLACK>\n";
				echo "<TD ALIGN=LEFT VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>#</B></FONT></td>\n";
				echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>DID</B> &nbsp;</FONT></td>\n";
				echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>PHONE</B> &nbsp;</FONT></td>\n";
				echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>DID ID</B> &nbsp;</FONT></td>\n";
				echo "<TD ALIGN=CENTER VALIGN=TOP><FONT FACE=\"ARIAL,HELVETICA\" COLOR=WHITE><B>CALL DATE</B> &nbsp;</FONT></td>\n";
				echo "</TR>\n";
				$o=0;
				while ($results_to_print > $o)
					{
					$row=mysql_fetch_row($rslt);
					$o++;
					$search_lead = $row[0];
					if (eregi("1$|3$|5$|7$|9$", $o))
						{$bgcolor='bgcolor="#CCCCCC"';} 
					else
						{$bgcolor='bgcolor="#9BB9FB"';}
					echo "<TR $bgcolor>\n";
					echo "<TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$o</FONT></td>\n";
					echo "<td><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[0]</FONT></td>\n";
					echo "<td><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[1]</FONT></td>\n";
					echo "<td><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[2]</FONT></td>\n";
					echo "<td><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[3]</FONT></td>\n";
					echo "</TR>\n";
					}
				echo "</TABLE>\n";
				}
			}

		### LOG INSERTION Admin Log Table ###
		$SQL_log = "$stmt|";
		$SQL_log = ereg_replace(';','',$SQL_log);
		$SQL_log = addslashes($SQL_log);
		$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LEADS', event_type='SEARCH', record_id='$search_lead', event_code='ADMIN SEARCH LEAD', event_sql=\"$SQL_log\", event_notes='';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);

		$ENDtime = date("U");

		$RUNtime = ($ENDtime - $STARTtime);

		
		#echo "\n\n\n<br><br><br>\n<a href=\"$PHP_SELF\">NEW SEARCH</a>";

		#echo "\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds";

		echo "</td></TR></TABLE>\n";
		echo "</td></TR></TABLE>\n";
		echo "</td></TR></TABLE>\n";
		#require("admin_footer.php");

		echo "\n\n\n</body></html>";

		exit;
		}
	##### END Log search #####





	##### BEGIN Lead search #####
	if ($vendor_id)
		{
		$stmt="SELECT $vicidial_list_fields from vicidial_list where vendor_lead_code='" . mysql_real_escape_string($vendor_id) . "'  $LOGallowed_listsSQL";
		}
	else
		{
		if ($phone)
			{
			if ($alt_phone_search=="Yes")
				{
				$stmt="SELECT $vicidial_list_fields from vicidial_list where phone_number='" . mysql_real_escape_string($phone) . "' or alt_phone='" . mysql_real_escape_string($phone) . "' or address3='" . mysql_real_escape_string($phone) . "'  $LOGallowed_listsSQL";
				}
			else
				{
				$stmt="SELECT $vicidial_list_fields from vicidial_list where phone_number='" . mysql_real_escape_string($phone) . "' $LOGallowed_listsSQL";
				}
			}
		else
			{
			if ($lead_id)
				{
				$stmt="SELECT $vicidial_list_fields from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "' $LOGallowed_listsSQL";
				}
			else
				{
				if ( (strlen($status)>0) or (strlen($list_id)>0) or (strlen($user)>0) )
					{
					$statusSQL = '';
					$list_idSQL = '';
					$userSQL = '';
					if (strlen($status)>0)	
						{
						$statusSQL = "status='" . mysql_real_escape_string($status) . "'"; $SQLctA++;
						}
					if (strlen($list_id)>0) 
						{
						if ($SQLctA > 0) {$andA = 'and';}
						$list_idSQL = "$andA list_id='" . mysql_real_escape_string($list_id) . "'"; $SQLctB++;
						}
					if (strlen($user)>0)	
						{
						if ( ($SQLctA > 0) or ($SQLctB > 0) ) {$andB = 'and';}
						$userSQL = "$andB user='" . mysql_real_escape_string($user) . "'";
						}
					$stmt="SELECT $vicidial_list_fields from vicidial_list where $statusSQL $list_idSQL $userSQL  $LOGallowed_listsSQL";
					}
				else
					{
					if ( (strlen($first_name)>0) or (strlen($last_name)>0) )
						{
						$first_nameSQL = '';
						$last_nameSQL = '';
						if (strlen($first_name)>0)	
							{
							$first_nameSQL = "first_name='" . mysql_real_escape_string($first_name) . "'"; $SQLctA++;
							}
						if (strlen($last_name)>0) 
							{
							if ($SQLctA > 0) {$andA = 'and';}
							$last_nameSQL = "$andA last_name='" . mysql_real_escape_string($last_name) . "'";
							}
						$stmt="SELECT $vicidial_list_fields from vicidial_list where $first_nameSQL $last_nameSQL  $LOGallowed_listsSQL";
						}
					else
						{
						print "ERROR: you must search for something! Go back and search for something";
						exit;
						}
					}
				}
			}
		}

	$stmt_alt='';
	$results_to_printX=0;
	if ( ($alt_phone_search=="Yes") and (strlen($phone) > 4) )
		{
		$stmtX="SELECT lead_id from vicidial_list_alt_phones where phone_number='" . mysql_real_escape_string($phone) . "' $LOGallowed_listsSQL limit 1000;";
		$rsltX=mysql_query($stmtX, $link);
		$results_to_printX = mysql_num_rows($rsltX);
		if ($DB)
			{echo "\n\n$results_to_printX|$stmtX\n\n";}
		$o=0;
		while ($results_to_printX > $o)
			{
			$row=mysql_fetch_row($rsltX);
			if ($o > 0) {$stmt_alt .= ",";}
			$stmt_alt .= "'$row[0]'";
			$o++;
			}
		if (strlen($stmt_alt) > 2)
			{$stmt_alt = "or lead_id IN($stmt_alt)";}
		}

	$stmt = "$stmt$stmt_alt order by modify_date desc limit 1000;";

	if ($DB)
		{
		echo "\n\n$stmt\n\n";
		}

	### LOG INSERTION Search Log Table ###
	$SQL_log = "$stmt|";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmtL="INSERT INTO vicidial_lead_search_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', source='admin', results='0', search_query=\"$SQL_log\";";
	if ($DB) {echo "|$stmtL|\n";}
	$rslt=mysql_query($stmtL, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
	$search_log_id = mysql_insert_id($link);

	$rslt=mysql_query("$stmt", $link);
	$results_to_print = mysql_num_rows($rslt);
	if ( ($results_to_print < 1) and ($results_to_printX < 1) )
		{
		#echo date("l F j, Y G:i:s A");
		
		echo "<div class=cc-mstyle>";
		echo "<table>";
		echo "<tr>";
		echo "<td id='icon32'><img src='../images/icons/application_form_magnify.png' /></td>";
		echo "<td id='submenu-title'> Bases de Dados </td>";
		echo "<td style='text-align:left'>Inserir Texto.</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		

		
		
		echo "\n<br><br><center>\n";
		echo "<b>Não foram encontrados resultados para os dados inseridos.</b><br><br>\n";
		echo "<b>Please go back and double check the information you entered and submit again</b>\n";
		echo "</center>\n";
		echo "</td></TR></TABLE>\n";
		echo "</td></TR></TABLE>\n";
		echo "</td></TR></TABLE>\n";
		#require("admin_footer.php");
		echo "</body></html>\n";
		exit;
		}
	else
		{
		
		echo "<div class=cc-mstyle>";
		echo "<table>";
		echo "<tr>";
		echo "<td id='icon32'><img src='../images/icons/application_form_magnify.png' /></td>";
		echo "<td id='submenu-title'> Pesquisa de Contactos </td>";
		echo "<td style='text-align:left'>Inserir Texto.</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		
		echo "<br>";
		
		echo "<div class=cc-mstyle>";
		
		#echo "<b>RESULTS: $results_to_print</b><BR><BR>\n";
		echo "<table>";
		echo "<tr>";
		echo "<th>#</th>\n";
		echo "<th> ID do Contacto </th>\n";
		echo "<th> Estado </th>\n";
		echo "<th> ID do Colaborador </th>\n";
		echo "<th> Ultimo Colaborador</th>\n";
		echo "<th>ID da BD </th>\n";
		echo "<th>Telefone</th>\n";
		echo "<th>Nome </th>\n";
		echo "<th>Cidade</th>\n";
		echo "<th>Segurança </th>\n";
		echo "<th>Ultima Chamada</th>\n";
		echo "</TR>\n";
		$o=0;
		while ($results_to_print > $o)
			{
			$row=mysql_fetch_row($rslt);
			$o++;
			$search_lead = $row[0];
		
			echo "<tr>\n";
			echo "<td>$o</FONT></td>\n";
			echo "<td><a href=\"admin_modify_lead.php?lead_id=$row[0]\" target=\"_blank\">$row[0]</a></FONT></td>\n";
			echo "<td>$row[3]</FONT></td>\n";
			echo "<td>$row[5]</FONT></td>\n";
			echo "<td>$row[4]</FONT></td>\n";
			echo "<td>$row[7]</FONT></td>\n";
			echo "<td>$row[11]</FONT></td>\n";
			echo "<td>$row[13] $row[15]</FONT></td>\n";
			echo "<td>$row[19]</FONT></td>\n";
			echo "<td>$row[28]</FONT></td>\n";
			echo "<td>$row[31]</FONT></td>\n";
			echo "</TR>\n";
			}
		echo "</TABLE>\n";
		}

	### LOG INSERTION Admin Log Table ###
	$SQL_log = "$stmt|";
	$SQL_log = ereg_replace(';','',$SQL_log);
	$SQL_log = addslashes($SQL_log);
	$stmt="INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$PHP_AUTH_USER', ip_address='$ip', event_section='LEADS', event_type='SEARCH', record_id='$search_lead', event_code='ADMIN SEARCH LEAD', event_sql=\"$SQL_log\", event_notes='';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);

	$end_process_time = date("U");
	$search_seconds = ($end_process_time - $STARTtime);

	$stmtL="UPDATE vicidial_lead_search_log set results='$o', seconds='$search_seconds' where search_log_id='$search_log_id';";
	if ($DB) {echo "|$stmtL|\n";}
	$rslt=mysql_query($stmtL, $link);
	}
	##### END Lead search #####




$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

#echo "\n\n\n<br><br><br>\n<a href=\"$PHP_SELF\">NEW SEARCH</a>";


#echo "\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds";

echo "</td></TR></TABLE>\n";
echo "</td></TR></TABLE>\n";
echo "</td></TR></TABLE>\n";
#require("admin_footer.php");
?>



</body>
</html>
