<?php 
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {$far.="../";}
define("ROOT", $far);
require(ROOT . "ini/header.php");

require("functions.php");

### 

if (isset($_GET["user_group"]))				{$grupos=$_GET["user_group"];}
	elseif (isset($_POST["user_group"]))	{$grupos=$_POST["user_group"];}
if (isset($_GET["group"]))					{$campanhas=$_GET["group"];}
	elseif (isset($_POST["group"]))			{$campanhas=$_POST["group"];}

### END



$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["group"]))					{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))			{$group=$_POST["group"];}
if (isset($_GET["user_group"]))				{$user_group=$_GET["user_group"];}
	elseif (isset($_POST["user_group"]))	{$user_group=$_POST["user_group"];}
if (isset($_GET["shift"]))					{$shift=$_GET["shift"];}
	elseif (isset($_POST["shift"]))			{$shift=$_POST["shift"];}
if (isset($_GET["stage"]))					{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))			{$stage=$_POST["stage"];}
if (isset($_GET["file_download"]))			{$file_download=$_GET["file_download"];}
	elseif (isset($_POST["file_download"]))	{$file_download=$_POST["file_download"];}
if (isset($_GET["DB"]))						{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))			{$DB=$_POST["DB"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

if (strlen($shift)<2) {$shift='ALL';}
if (strlen($stage)<2) {$stage='NAME';}



$report_name = 'Agent Time Detail';
$db_source = 'M';

$user_case = '';
if (file_exists('../goautodial-admin/options.php'))
	{
	require('../goautodial-admin/options.php');
	}
if ($user_case == '1')
	{$userSQL = 'ucase(user)';}
if ($user_case == '2')
	{$userSQL = 'lcase(user)';}
if (strlen($userSQL)<2)
	{$userSQL = 'user';} 

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
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
##### END SETTINGS LOOKUP #####
###########################################

if ( (strlen($slave_db_server)>5) and (preg_match("/$report_name/",$reports_use_slave_db)) )
	{
	mysql_close($link);
	$use_slave_server=1;
	$db_source = 'S';
	require("dbconnect.php");
#	echo "<!-- Using slave server $slave_db_server $db_source -->\n";
	}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) { $rslt=mysql_query("SET NAMES 'UTF8'");}
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
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$stmt="SELECT user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGuser_group =		$row[0];

$stmt="SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$LOGuser_group';";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGallowed_campaigns = $row[0];
$LOGallowed_reports =	$row[1];

if ( (!preg_match("/$report_name/",$LOGallowed_reports)) and (!preg_match("/ALL REPORTS/",$LOGallowed_reports)) )
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "You are not allowed to view this report: |$PHP_AUTH_USER|$report_name|\n";
    exit;
	}

$LOGallowed_campaignsSQL='';
$whereLOGallowed_campaignsSQL='';
if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
	{
	$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
	$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
	$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
	}
$regexLOGallowed_campaigns = " $LOGallowed_campaigns ";

$MT[0]='';
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($group)) {$group = '';}
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}



$i=0;
$group_string='|';
$group_ct = count($group);
while($i < $group_ct)
	{
	$group_string .= "$group[$i]|";
	$i++;
	}

$stmt="select campaign_id,campaign_name from vicidial_campaigns $whereLOGallowed_campaignsSQL order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =$row[0];
	$camp_name[$i]=$row[1];
	if (ereg("-ALL",$group_string) )
		{$group[$i] = $groups[$i];}
	$i++;
	}

$i=0;
$group_string='|';
$group_ct = count($group);
while($i < $group_ct)
	{
	if ( (preg_match("/ $group[$i] /",$regexLOGallowed_campaigns)) or (preg_match("/-ALL/",$LOGallowed_campaigns)) )
		{
		$group_string .= "$group[$i]|";
		$group_SQL .= "'$group[$i]',";
		$groupQS .= "&group[]=$group[$i]";
		}
	$i++;
	}
if ( (ereg("--TODOS--",$group_string) ) or ($group_ct < 1) )
	{$group_SQL = "";}
else
	{
	$group_SQL = eregi_replace(",$",'',$group_SQL);
	$group_SQL = "and campaign_id IN($group_SQL)";
	}

$stmt="select user_group from vicidial_users where user = '$_SERVER[PHP_AUTH_USER]';";
$rslt=mysql_query($stmt, $link);
$usr = mysql_fetch_assoc($rslt);
$user_group = $usr['user_group'];

if ($user_group == 'ADMIN') {

$stmt="select user_group from vicidial_user_groups order by user_group;"; } else {
	$stmt="select user_group from vicidial_user_groups WHERE user_group = '$user_group' order by user_group;"; }


$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$user_groups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $user_groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$user_groups[$i] =$row[0];
	$i++;
	}

$i=0;
$user_group_string='|';
$user_group_ct = count($user_group);
while($i < $user_group_ct)
	{
	$user_group_string .= "$user_group[$i]|";
	$user_group_SQL .= "'$user_group[$i]',";
	$user_groupQS .= "&user_group[]=$user_group[$i]";
	$i++;
	}
if ( (ereg("--TODOS--",$user_group_string) ) or ($user_group_ct < 1) )
	{$user_group_SQL = "";}
else
	{
	$user_group_SQL = eregi_replace(",$",'',$user_group_SQL);
	$user_group_SQL = "and vicidial_agent_log.user_group IN($user_group_SQL)";
	$TCuser_group_SQL = eregi_replace(",$",'',$TCuser_group_SQL);
	$TCuser_group_SQL = "and user_group IN($TCuser_group_SQL)";
	}

if ($DB) {echo "$user_group_string|$user_group_ct|$user_groupQS|$i<BR>";}

$stmt="select distinct pause_code,pause_code_name from vicidial_pause_codes;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$statha_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $statha_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$pause_code[$i] =		"$row[0]";
	$pause_code_name[$i] =	"$row[1]";
	$i++;
	}

$LINKbase = "$PHP_SELF?query_date=$query_date&end_date=$end_date$groupQS$user_groupQS&shift=$shift&DB=$DB";







if ($file_download < 1)
	{
	?>

	<STYLE type="text/css">
	<!--
	   .yellow {color: white; background-color: yellow}
	   .red {color: white; background-color: red}
	   .blue {color: white; background-color: blue}
	   .purple {color: white; background-color: purple}
	-->
	 </STYLE>

	<?php

//	echo "<span style=\"position:absolute;left:0px;top:0px;z-index:20;\" id=admin_header>";

	$short_header=1;

	#require("admin_header.php");

//	echo "</span>\n";
//	echo "<span style=\"position:absolute;left:3px;top:3px;z-index:19;\" id=agent_status_stats>\n";
#	echo "<PRE><FONT SIZE=2>\n";
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='/images/icons/chart_pie.png' /></td>";
	echo "<td id='submenu-title'> Estatísticas por Agente</td>";
	echo "<td style='text-align:left'></td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	
	echo "<div id=work-area style='min-height:300px'>";
	echo "<br><br>";
	
	echo "<div class=cc-mstyle style='border:none;'>";


echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET name=vicidial_report id=vicidial_report>\n";
echo "<table style='width:90%'>";
echo "<TR>";
echo "<TD style='min-width:190px;' VALIGN=TOP><label> Data Inicio: </label>";
echo "<INPUT TYPE=hidden NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=TEXT style='width:145px; text-align:center; text:indent:0;' MAXLENGTH=10 NAME=query_date id=sd VALUE=\"$query_date\">";


echo "<label> Data Final: </label><INPUT TYPE=TEXT style='width:145px; text-align:center; text:indent:0;' NAME=end_date id=ed  MAXLENGTH=10 VALUE=\"$end_date\">";

?>
<script language="JavaScript">
$(function() {
        $( "#sd" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"yy-mm-dd",
            onClose: function( selectedDate ) {
                $( "#ed" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $( "#ed" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"yy-mm-dd",
            onClose: function( selectedDate ) {
                $( "#sd" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
        });
</script>
<?php

echo "</TD><TD VALIGN=TOP> <label>Campanhas: </label>";
echo "<SELECT style='width:175px; height:120px;' SIZE=5 NAME=group[] multiple>\n";
if  (eregi("--TODOS--",$group_string))
	{echo "<option value=\"--TODOS--\" selected> Todas as Campanhas </option>\n";}
else
	{echo "<option value=\"--TODOS--\"> Todas as Campanhas </option>\n";}
$o=0;
while ($campaigns_to_print > $o)
{
	if (eregi("$groups[$o]\|",$group_string)) {echo "<option selected value=\"$groups[$o]\">$camp_name[$o]</option>\n";}
	  else {echo "<option value=\"$groups[$o]\">$camp_name[$o]</option>\n";}
	$o++;
}
echo "</SELECT>\n";
echo "</TD><TD VALIGN=TOP><label>Grupos de Utilizadores:</label>";
echo "<SELECT style='width:175px; height:120px;' SIZE=5 NAME=user_group[] multiple>\n";
if (isset($_GET["user_group"]))				{$grupos=$_GET["user_group"];}
	elseif (isset($_POST["user_group"]))	{$grupos=$_POST["user_group"];}
	
	/*$current_admin = $_SERVER['PHP_AUTH_USER'];
	$query = mysql_query("select user_group from vicidial_users where user='$current_admin'") or die(mysql_error());
	$query = mysql_fetch_assoc($query);
	$grupos = $query['user_group'];	*/
	
	
if  (eregi("--TODOS--",$user_group_string))
	{echo "<option value=\"--TODOS--\" selected> Todos os Grupos </option>\n";}
else
	{echo "<option value=\"--TODOS--\"> Todos os Grupos  </option>\n";}
$o=0;
while ($user_groups_to_print > $o)
	{
	if  (eregi("$user_groups[$o]\|",$user_group_string)) {echo "<option selected value=\"$user_groups[$o]\">$user_groups[$o]</option>\n";}
	  else {echo "<option value=\"$user_groups[$o]\">$user_groups[$o]</option>\n";}
	$o++;
	}
echo "</SELECT>\n";
/*echo "</TD><TD VALIGN=TOP><label>Turno</label>";
echo "<SELECT SIZE=1 NAME=shift>\n";


switch ($shift) {
case "AM": $selected = "selected"; break;
case "PM": $selected1 = "selected"; break;
case "ALL": $selected2 = "selected"; break;
}


#echo "<option selected value=\"$shift\">$shift</option>\n";
echo "<option $selected2 value=\"ALL\">Todos</option>\n";
echo "<option $selected value=\"AM\">Manhã</option>\n";
echo "<option $selected1 value=\"PM\">Tarde</option>\n";

echo "</SELECT>\n"; */
#echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
#echo "<br><br> <a href=\"$LINKbase&stage=$stage&file_download=1\"><img src='../images/icons/box_down_32.png' /></a><br> Fazer Download \n";

echo "</TD>";

#echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
#echo " <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
#echo "</FONT>\n";
echo "</TR></TABLE>";

//echo "</FORM>\n\n<BR>$db_source";


echo "<br><br><table ><tr>
		<td><span style='float:right'><label for=sub style='display:inline;cursor:pointer'> Submeter</label><input id=sub type=image style='float:left' src='../images/icons/shape_square_add_32.png' alt=Gravar name=SUBMIT></span></td>
		</tr></table>";



echo "</FORM></div></div></div></div></div>";


	}

if ( (strlen($group[0]) < 1) or (strlen($user_group[0]) < 1) )
	{
	#echo "\n";
	#echo "PLEASE SELECT A CAMPAIGN OR USER GROUP AND DATE-TIME ABOVE AND CLICK SUBMIT\n";
	#echo " NOTE: stats taken from shift specified\n";
	}

else
	{
	if ($shift == 'TEST') 
		{
		$time_BEGIN = "09:45:00";  
		$time_END = "10:00:00";
		}
	if ($shift == 'AM') 
		{
		$time_BEGIN=$AM_shift_BEGIN;
		$time_END=$AM_shift_END;
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "03:45:00";}   
		if (strlen($time_END) < 6) {$time_END = "15:15:00";}
		}
	if ($shift == 'PM') 
		{
		$time_BEGIN=$PM_shift_BEGIN;
		$time_END=$PM_shift_END;
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "15:15:00";}
		if (strlen($time_END) < 6) {$time_END = "23:15:00";}
		}
	if ($shift == 'ALL') 
		{
		if (strlen($time_BEGIN) < 6) {$time_BEGIN = "00:00:00";}
		if (strlen($time_END) < 6) {$time_END = "23:59:59";}
		}
	$query_date_BEGIN = "$query_date $time_BEGIN";   
	$query_date_END = "$end_date $time_END";


		$archive = (strtotime("2 month ago") > strtotime($query_date_END)) ? "_archive" : "";

	if (strlen($user_group)>0) {$ugSQL="and vicidial_agent_log$archive.user_group='$user_group'";}
	else {$ugSQL='';}

	if ($file_download < 1)
		{
		#echo "Agent Time Detail                     $NOW_TIME\n";

		#echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
		}
	else
		{
		$file_output .= "Agent Time Detail                     $NOW_TIME\n";
		$file_output .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
		}

####################################################################################################################################
### NEW CODE
####################################################################################################################################

### Construção dos Filtros escolhidos pelo utilizador (Campanhas e Grupos de Utilizadores)
$campanhas_count = count($campanhas);
for ($i=0;$i<$campanhas_count;$i++)
	{
	if ($campanhas_count == 1) 
		{
		$campanhas_IN = "'".$campanhas[$i]."'"; 
		}
	elseif ($campanhas_count-1 == $i)
		{
		$campanhas_IN .= "'".$campanhas[$i]."'";	
		}	
	else
		{
		$campanhas_IN .= "'".$campanhas[$i]."',";
		}
	}
if ((ereg("--TODOS--",$campanhas_IN)) or ($campanhas_count < 1))
	{
	$campanhas_SQL = "";
	}
else
	{
	$campanhas_SQL = "AND campaign_id IN($campanhas_IN)";
	}
###############################################################
$grupos_count = count($grupos);
for ($i=0;$i<$grupos_count;$i++)
	{
	if ($grupos_count == 1) 
		{
		$grupos_IN = "'".$grupos[$i]."'"; 
		}
	elseif ($grupos_count-1 == $i)
		{
		$grupos_IN .= "'".$grupos[$i]."'";	
		}	
	else
		{
		$grupos_IN .= "'".$grupos[$i]."',";
		}
	}

if ((ereg("--TODOS--",$grupos_IN)) or ($grupos_count < 1))
	{
	$grupos_SQL = "";
	}
else
	{
	$grupos_SQL = "AND user_group IN($grupos_IN)";
	}
###  QUERY - Numero de Operadores a mostrar
$query = "	SELECT 	user,
					full_name,
					user_group
			FROM 	vicidial_users
			WHERE 	active='Y'
			AND user NOT IN('VDAD','8888','1000','admin','operador')
			

			$grupos_SQL 
			ORDER BY user;";
			
					
$query = mysql_query($query, $link) or die(mysql_error());
$num_users = mysql_num_rows($query);

for ($i=0;$i<$num_users;$i++)
	{
	$row = mysql_fetch_row($query);
	$grupo_[$i] = $row[2]; 
	$user[$i] =	$row[0];
	$lowercase_user[$i] = strtolower($user[$i]);
	
	$full_name_parts = explode(" ",$row[1]);
	$full_name_parts_count = count($full_name_parts);
	if ($full_name_parts_count == 1) 
		{
		$full_name[$i] = $full_name_parts[0];
		} 
	else 
		{
		$full_name[$i] = $full_name_parts[0]." ".$full_name_parts[$full_name_parts_count-1];
		}
	}
### Construção do SQL IN dos users	
$users_count = count($user);
for ($i=0;$i<$users_count;$i++)
	{
	if ($users_count == 1) 
		{
		$users_IN = "'".$lowercase_user[$i]."'"; 
		}
	elseif ($users_count-1 == $i)
		{
		$users_IN .= "'".$lowercase_user[$i]."'";	
		}	
	else
		{
		$users_IN .= "'".$lowercase_user[$i]."',";
		}
	}
$users_SQL = "AND user IN($users_IN)";

### QUERY - Dados estatisticos dos Operadores
$query = "	SELECT	user,
					wait_sec,
					talk_sec,
					dispo_sec,
					pause_sec,
					lead_id,
					status,
					dead_sec
			FROM 	vicidial_agent_log$archive
			WHERE 	event_time <= '$query_date_END' 
			AND 	event_time >= '$query_date_BEGIN' 
			$grupos_SQL 
			$campanhas_SQL 
			LIMIT	10000000;";

$query = mysql_query($query) or die(mysql_error());
$num_logs = mysql_num_rows($query);	
for ($i=0;$i<$num_logs;$i++)
{
$row = mysql_fetch_assoc($query);

for ($o=0;$o<$num_users;$o++)
	{
	if (strtolower($row['user']) == $lowercase_user[$o]) 
		{
		### Soma dos totais de wait|talk|dispo|pause|dead de cada operador	
		$user_wait_time[$lowercase_user[$o]] = $user_wait_time[$lowercase_user[$o]] + $row['wait_sec'];
		$user_talk_time[$lowercase_user[$o]] = $user_talk_time[$lowercase_user[$o]] + $row['talk_sec'];
		$user_dispo_time[$lowercase_user[$o]] = $user_dispo_time[$lowercase_user[$o]] + $row['dispo_sec'];
		$user_pause_time[$lowercase_user[$o]] = $user_pause_time[$lowercase_user[$o]] + $row['pause_sec'];
		$user_dead_time[$lowercase_user[$o]] = $user_dead_time[$lowercase_user[$o]] + $row['dead_sec'];
		### Numero de chamadas de cada operador
		if ( ($row['lead_id'] > 0) and ((!eregi("NULL",$row['status'])) and (strlen($row['status']) > 0)) ) 
			{
			$user_calls[$lowercase_user[$o]]++;
			}
		
		}
	}

}
### Calculo de "Em Chamada"  de cada operador (talk_time - dead_time)
for ($o=0;$o<$num_users;$o++)
{
$user_customer_time[$lowercase_user[$o]] = ($user_talk_time[$lowercase_user[$o]] - $user_dead_time[$lowercase_user[$o]]);	
}
### Calculo do tempo login de cada operador (wait_time + talk_time + dispo_time + pause_time)
for ($o=0;$o<$num_users;$o++)
{
$user_total_time[$lowercase_user[$o]] = ($user_dead_time[$lowercase_user[$o]] + $user_customer_time[$lowercase_user[$o]] + $user_wait_time[$lowercase_user[$o]] + $user_dispo_time[$lowercase_user[$o]] + $user_pause_time[$lowercase_user[$o]]);	
}
### Tempo médio em Chamada
for ($o=0;$o<$num_users;$o++)
{
$user_avg_talk_time[$lowercase_user[$o]] = $user_customer_time[$lowercase_user[$o]] / $user_calls[$lowercase_user[$o]];	
}
### Tempo médio em Espera
for ($o=0;$o<$num_users;$o++)
{
$user_avg_wait_time[$lowercase_user[$o]] = $user_wait_time[$lowercase_user[$o]] / $user_calls[$lowercase_user[$o]];	
}
### Chamadas por Hora
for ($o=0;$o<$num_users;$o++)
{
$user_calls_per_hour[$lowercase_user[$o]] = $user_calls[$lowercase_user[$o]] / ($user_total_time[$lowercase_user[$o]] / 3600);	
}
### Somatório e Conversão em Horas:Minutos:Segundos dos Totais
for ($o=0;$o<$num_users;$o++)
{
	if($user_calls[$lowercase_user[$o]]!=NULL)
	{
	$T_user_calls += $user_calls[$lowercase_user[$o]];
	$T_user_total_time += $user_total_time[$lowercase_user[$o]];
	$T_user_wait_time += $user_wait_time[$lowercase_user[$o]];
	$T_user_customer_time += $user_customer_time[$lowercase_user[$o]];
	$T_user_dispo_time += $user_dispo_time[$lowercase_user[$o]];
	$T_user_pause_time += $user_pause_time[$lowercase_user[$o]]; 
    $T_user_dead_time += $user_dead_time[$lowercase_user[$o]];
    
	$M_user_calls_per_hour += $user_calls_per_hour[$lowercase_user[$o]]; 
	
	$M_user_avg_talk_time += $user_avg_talk_time[$lowercase_user[$o]];
	
	$M_user_avg_wait_time += $user_avg_wait_time[$lowercase_user[$o]];
	
	$M_counter++;
	}
}
$M_user_calls_per_hour = $M_user_calls_per_hour / $M_counter;

$M_user_avg_talk_time = $M_user_avg_talk_time / $M_counter;
$M_user_avg_talk_time = sec_convert($M_user_avg_talk_time,"H");

$M_user_avg_wait_time = $M_user_avg_wait_time / $M_counter;
$M_user_avg_wait_time = sec_convert($M_user_avg_wait_time,"H");

$T_user_total_time = sec_convert($T_user_total_time,"H"); 
$T_user_wait_time = sec_convert($T_user_wait_time,"H"); 
$T_user_customer_time = sec_convert($T_user_customer_time,"H"); 
$T_user_dispo_time = sec_convert($T_user_dispo_time,"H"); 
$T_user_pause_time = sec_convert($T_user_pause_time,"H");
$T_user_dead_time = sec_convert($T_user_dead_time,"H");
### Conversão em Horas:Minutos:Segundos dos wait|talk|dispo|pause|dead de cada operador
for ($o=0;$o<$num_users;$o++) 
{
    if($user_calls[$lowercase_user[$o]]!=NULL)
    {
$user_avg_wait_time[$lowercase_user[$o]] = sec_convert($user_avg_wait_time[$lowercase_user[$o]],"H");
$user_avg_talk_time[$lowercase_user[$o]] = sec_convert($user_avg_talk_time[$lowercase_user[$o]],"H");
$user_total_time[$lowercase_user[$o]] = sec_convert($user_total_time[$lowercase_user[$o]],"H"); 
$user_wait_time[$lowercase_user[$o]] = sec_convert($user_wait_time[$lowercase_user[$o]],"H"); 
$user_talk_time[$lowercase_user[$o]] = sec_convert($user_talk_time[$lowercase_user[$o]],"H"); 
$user_dispo_time[$lowercase_user[$o]] = sec_convert($user_dispo_time[$lowercase_user[$o]],"H"); 
$user_pause_time[$lowercase_user[$o]] = sec_convert($user_pause_time[$lowercase_user[$o]],"H"); 
$user_dead_time[$lowercase_user[$o]] = sec_convert($user_dead_time[$lowercase_user[$o]],"H"); 
$user_customer_time[$lowercase_user[$o]] = sec_convert($user_customer_time[$lowercase_user[$o]],"H"); 
    }
}
### QUERY - Codigos de pausa em sistema

$query = "	SELECT DISTINCT	pause_code
			FROM 			vicidial_pause_codes 
			WHERE			pause_code IS NOT NULL
							$campanhas_SQL 
			ORDER BY		pause_code;";
$query = mysql_query($query) or die(mysql_error());
$num_logs = mysql_num_rows($query);


for ($i=0;$i<$num_logs;$i++) 
{
$row = mysql_fetch_assoc($query);
$pause_codes[$i] = $row["pause_code"];
}
$num_pause_codes = count($pause_codes);

### Construção do SQL IN dos pause codes
$pc_count = count($pause_codes);
for ($i=0;$i<$pc_count;$i++)  
	{
	if ($pc_count == 1) 
		{
		$pause_codes_IN = "'".$pause_codes[$i]."'"; 
		}
	elseif ($pc_count-1 == $i)
		{
		$pause_codes_IN .= "'".$pause_codes[$i]."'";	
		}	
	else
		{
		$pause_codes_IN .= "'".$pause_codes[$i]."',";
		}
	}
$pause_codes_SQL = "AND sub_status IN($pause_codes_IN)";
### QUERY - Dados estatisticos das pausas

$query = "	SELECT 		user,
						sum(pause_sec),
						sub_status 
			FROM 		vicidial_agent_log$archive
			WHERE 		event_time <= '$query_date_END' 
			AND 		event_time >= '$query_date_BEGIN' 
			AND			pause_sec > 0 
			AND			pause_sec < 65000 
			AND			sub_status IS NOT NULL 
						$grupos_SQL 
						$campanhas_SQL 
						$users_SQL 
						$pause_codes_SQL 
			GROUP BY 	user,
						sub_status 
			ORDER BY 	user,
						sub_status 
			 
			LIMIT 		10000000;";
			
$query = mysql_query($query);
$num_logs = mysql_num_rows($query);

for ($i=0;$i<$num_logs;$i++)
{
	$row = mysql_fetch_assoc($query);
	#print_r($row); echo "<br>";
	$pc_array[$i] = array(
						"user" => strtolower($row['user']),
						"time" => $row['sum(pause_sec)'],
						"sub_status" => $row['sub_status'],
					);

}

$ttt = count($pc_array);
for ($t=0;$t<$ttt;$t++)
{
#print_r($pc_array[$t]); echo "<br>";	
	
}


### Inicio da área de output dos dados
echo "<div class=cc-mstyle>";
echo "<table>";
echo "	<tr>
		<th>Operador</th>
		<th>Nº de Chamadas</th>
		<th>Tempo Online</th>
		<th>Em Chamada</th>
		<th>Em Espera</th>
		<th>Em Feedback</th>
		<th>Em Chamadas Mortas<img title='Tempo entre o cliente desligar e o operador clicar no botão \"Desligar Chamada\" e entrar no menu de Feedbacks' src='../images/icons/info_rhombus_16.png'></th>
		<th>Em Pausa</th>
		<th>Tempo Médio em Chamada</th>
		<th>Tempo Médio em Espera</th>
		<th>Chamadas por Hora</th>
		<th></th>
		
		
		
		
		
		
		</tr>";
for ($i=0;$i<$num_users;$i++) 
	{
		
	if($user_calls[$lowercase_user[$i]]!=NULL)
	{
	echo "<tr>"; 
	echo "<td>".$full_name[$i]."</td>";
	echo "<td>"; if ($user_calls[$lowercase_user[$i]] == NULL) {echo "0";} else {echo $user_calls[$lowercase_user[$i]];} echo "</td>"; 
	echo "<td>"; if ($user_total_time[$lowercase_user[$i]] == NULL) {echo "0:00";} else {echo $user_total_time[$lowercase_user[$i]];} echo "</td>";	
	echo "<td>"; if ($user_customer_time[$lowercase_user[$i]] == NULL) {echo "0:00";} else {echo $user_customer_time[$lowercase_user[$i]];} echo "</td>";
	echo "<td>"; if ($user_wait_time[$lowercase_user[$i]] == NULL) {echo "0:00";} else {echo $user_wait_time[$lowercase_user[$i]];} echo "</td>";
	echo "<td>"; if ($user_dispo_time[$lowercase_user[$i]] == NULL) {echo "0:00";} else {echo $user_dispo_time[$lowercase_user[$i]];} echo "</td>";
	echo "<td>"; if ($user_dead_time[$lowercase_user[$i]] == NULL) {echo "0:00";} else {echo $user_dead_time[$lowercase_user[$i]];} echo "</td>";
	echo "<td>"; if ($user_pause_time[$lowercase_user[$i]] == NULL) {echo "0:00";} else {echo $user_pause_time[$lowercase_user[$i]];} echo "</td>";
	echo "<td>".$user_avg_talk_time[$lowercase_user[$i]]."</td>";
	echo "<td>".$user_avg_wait_time[$lowercase_user[$i]]."</td>";
	echo "<td>"; if ($user_calls_per_hour[$lowercase_user[$i]] == NULL) {echo "0";} else {echo round($user_calls_per_hour[$lowercase_user[$i]],2);} echo "</td>"; 
	}

 



	echo "</tr>"; 
	}
echo "<tr style='border-top:1px solid #c0c0c0'>";
echo "<td><b>TOTAIS/MÉDIAS</b></td>";
echo "<td>$T_user_calls</td>";
echo "<td>$T_user_total_time</td>";
echo "<td>$T_user_customer_time</td>";
echo "<td>$T_user_wait_time</td>";
echo "<td>$T_user_dispo_time</td>";
echo "<td>$T_user_dead_time</td>";
echo "<td>$T_user_pause_time</td>";
echo "<td>$M_user_avg_talk_time</td>";
echo "<td>$M_user_avg_wait_time</td>";
echo "<td>".round($M_user_calls_per_hour,2)."</td>";
echo "</tr>";
echo "</table>";
echo "</div>";
echo "<br>";
############################
/*echo "<div class=cc-mstyle>";
echo "<table>";
echo "	<tr>
		<th>Operador</th>";
for ($i=0;$i<$num_pause_codes;$i++)
{
	echo "<th>$pause_codes[$i]</th>";
}
	
echo "</tr>";


for ($i=0;$i<$num_users;$i++)
	{
	echo "<tr>"; 
	echo "<td>".$full_name[$i]."</td>";
$p = 0;	
$pc_count = count($pc_array);	
for ($o=0;$o<$pc_count;$o++) 
{
		
		if ($pc_array[$o]["user"] == $lowercase_user[$i]) 
		{
			
			
			 
			if (($pc_array[$o]["sub_status"] == $pause_codes[$p])) 
			{
				echo "<td>";
				
				
				 $out = sec_convert($pc_array[$o]["time"],"H");
				 echo $out;
				  
				echo "</td>"; 
				
				$p++;
				
				
				
			} 
			else { $p++; echo "<td></td>"; $o--;}
			
			
			 
		}
		
	}
	
	
	
}


#echo "<tr style='border-top:1px solid #c0c0c0'>";
#echo "<td><b>TOTAIS</b></td>";

echo "</tr>";
echo "</table>";
echo "</div>";*/
####################################################################################################################################
### END 
####################################################################################################################################


	############################################################################
	##### BEGIN gathering information from the database section
	############################################################################



	### BEGIN gather user IDs and names for matching up later
	$stmt="select full_name,user from vicidial_users order by user limit 100000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$users_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $users_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$ULname[$i] =	$row[0];
		$ULuser[$i] =	$row[1];
		$i++;
		}
	### END gather user IDs and names for matching up later


	### BEGIN gather timeclock records per agent
	$stmt="select $userSQL,sum(login_sec) from vicidial_timeclock_log where event IN('LOGIN','START') and event_date >= '$query_date_BEGIN' and event_date <= '$query_date_END' $TCuser_group_SQL group by user limit 10000000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$punches_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $punches_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$TCuser[$i] =	$row[0];
		$TCtime[$i] =	$row[1];
		$i++;
		}
	### END gather timeclock records per agent


	### BEGIN gather pause code information by user IDs
	$sub_statuses='-';
	$sub_statusesTXT='';
	$sub_statusesHEAD='';
	$sub_statusesHTML='';
	$sub_statusesFILE='';
	$sub_statusesARY=$MT;
	$sub_status_count=0;
	$PCusers='-';
	$PCusersARY=$MT;
	$PCuser_namesARY=$MT;
	$user_count=0;
	$stmt="select $userSQL,sum(pause_sec),sub_status from vicidial_agent_log$archive where event_time <= '$query_date_END' and event_time >= '$query_date_BEGIN' and pause_sec > 0 and pause_sec < 65000 $group_SQL $user_group_SQL group by user,sub_status order by user,sub_status desc limit 10000000;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$subs_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $subs_to_print)
		{
		$row=mysql_fetch_row($rslt);
		

		
		$PCuser[$i] =		$row[0];
		$PCpause_sec[$i] =	$row[1];
		$sub_status[$i] =	$row[2];

		if (!eregi("-$sub_status[$i]-", $sub_statuses))
			{
			$sub_statusesTXT = sprintf("%10s", $sub_status[$i]);
			$sub_statusesHEAD .= "------------+";
			$sub_statusesHTML .= " $sub_statusesTXT |";
			$sub_statusesFILE .= ",$sub_status[$i]";
			$sub_statuses .= "$sub_status[$i]-";
			$sub_statusesARY[$sub_status_count] = $sub_status[$i];
			$sub_status_count++;
			}
		if (!eregi("-$PCuser[$i]-", $PCusers))
			{
			$PCusers .= "$PCuser[$i]-";
			$PCusersARY[$user_count] = $PCuser[$i];
			$user_count++;
			}

		$i++;
		}
	### END gather pause code information by user IDs


	##### BEGIN Gather all agent time records and parse through them in PHP to save on DB load
	$stmt="select $userSQL,wait_sec,talk_sec,dispo_sec,pause_sec,lead_id,status,dead_sec from vicidial_agent_log$archive where event_time <= '$query_date_END' and event_time >= '$query_date_BEGIN' $group_SQL $user_group_SQL limit 10000000;";
	
 
	
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$rows_to_print = mysql_num_rows($rslt);
	$i=0;
	$j=0;
	$k=0;
	$uc=0;
	while ($i < $rows_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$user =			$row[0];
		$wait =			$row[1];
		$talk =			$row[2];
		$dispo =		$row[3];
		$pause =		$row[4];
		$lead =			$row[5];
		$status =		$row[6];
		$dead =			$row[7];
		if ($wait > 65000) {$wait=0;}  
		if ($talk > 65000) {$talk=0;}
		if ($dispo > 65000) {$dispo=0;}
		if ($pause > 65000) {$pause=0;}
		if ($dead > 65000) {$dead=0;}
		$customer =		($talk - $dead);
		if ($customer < 1)
			{$customer=0;}
		$TOTwait =	($TOTwait + $wait);
		$TOTtalk =	($TOTtalk + $talk);
		$TOTdispo =	($TOTdispo + $dispo);
		$TOTpause =	($TOTpause + $pause);
		$TOTdead =	($TOTdead + $dead);
		$TOTcustomer =	($TOTcustomer + $customer);
		$TOTALtime = ($TOTALtime + $pause + $dispo + $talk + $wait);
		if ( ($lead > 0) and ((!eregi("NULL",$status)) and (strlen($status) > 0)) ) {$TOTcalls++;}
		
		$user_found=0;
		if ($uc < 1) 
			{
			$Suser[$uc] = $user;
			$uc++;
			}
		$m=0;
		while ( ($m < $uc) and ($m < 50000) )
			{
			if ($user == "$Suser[$m]")
				{
				$user_found++;

				$Swait[$m] =	($Swait[$m] + $wait);
				$Stalk[$m] =	($Stalk[$m] + $talk);
				$Sdispo[$m] =	($Sdispo[$m] + $dispo);
				$Spause[$m] =	($Spause[$m] + $pause);
				$Sdead[$m] =	($Sdead[$m] + $dead);
				$Scustomer[$m] =	($Scustomer[$m] + $customer);
				if ( ($lead > 0) and ((!eregi("NULL",$status)) and (strlen($status) > 0)) ) {$Scalls[$m]++;}
				}
			$m++;
			}
		if ($user_found < 1)
			{
			$Scalls[$uc] =	0;
			$Suser[$uc] =	$user;
			$Swait[$uc] =	$wait;
			$Stalk[$uc] =	$talk;
			$Sdispo[$uc] =	$dispo;
			$Spause[$uc] =	$pause;
			$Sdead[$uc] =	$dead;
			$Scustomer[$uc] =	$customer;
			if ($lead > 0) {$Scalls[$uc]++;}
			$uc++;
			}

		$i++;
		}
	if ($DB) {echo "Done gathering $i records, analyzing...<BR>\n";}
	##### END Gather all agent time records and parse through them in PHP to save on DB load

	############################################################################
	##### END gathering information from the database section
	############################################################################




	##### BEGIN print the output to screen or put into file output variable
	if ($file_download < 1)
		{
		
		
/*		echo "<div class=cc-mstyle>";
		echo "<table border=1>"; #working
		echo "<tr>";
		echo "<th>Username</th>";
	#	echo "<th>ID</th>";
		echo "<th>Calls</th>";
	#	echo "<th>Time Clock</th>";
		echo "<th>Agent time</th>";
			echo "<th>Wait</th>";
				echo "<th>Talk</th>";
					echo "<th>Dispo</th>";
					echo "<th>Pause</th>";
					echo "<th>Dead</th>";
					echo "<th>Costumer</th>";
					echo "<th>Mdial</th>";
					echo "<th>Login</th>";
					echo "<th>Lagged</th>";
					echo "<th>Interv</th>";
					echo "<th>Form</th>";
					echo "<th>Cback</th>";
					echo "<th>???</th>";
		
		echo "</tr>"; */
		
		
	/*	echo "AGENT TIME BREAKDOWN:\n";
		echo "+-----------------+----------+----------+------------+------------+------------+------------+------------+------------+------------+------------+   +$sub_statusesHEAD\n";
		echo "| <a href=\"$LINKbase&stage=NAME\">USER NAME</a>       | <a href=\"$LINKbase&stage=ID\">ID</a>       | <a href=\"$LINKbase&stage=LEADS\">CALLS</a>    | <a href=\"$LINKbase&stage=TCLOCK\">TIME CLOCK</a> | <a href=\"$LINKbase&stage=TIME\">AGENT TIME</a> | WAIT       | TALK       | DISPO      | PAUSE      | DEAD       | CUSTOMER   |   |$sub_statusesHTML\n";
		echo "+-----------------+----------+----------+------------+------------+------------+------------+------------+------------+------------+------------+   +$sub_statusesHEAD\n"; */
		}
	else
		{
		$file_output .= "USER,ID,CALLS,TIME CLOCK,AGENT TIME,WAIT,TALK,DISPO,PAUSE,DEAD,CUSTOMER$sub_statusesFILE\n";
		}
	##### END print the output to screen or put into file output variable





	############################################################################
	##### BEGIN formatting data for output section
	############################################################################

	##### BEGIN loop through each user formatting data for output
	$AUTOLOGOUTflag=0;
	$m=0;
	while ( ($m < $uc) and ($m < 50000) )
		{
		$SstatusesHTML='';
		$SstatusesFILE='';
		$Stime[$m] = ($Swait[$m] + $Stalk[$m] + $Sdispo[$m] + $Spause[$m]);
		$RAWuser = $Suser[$m];
		$RAWcalls = $Scalls[$m];
		$RAWtimeSEC = $Stime[$m];

		$Swait[$m]=		sec_convert($Swait[$m],'H'); 
		$Stalk[$m]=		sec_convert($Stalk[$m],'H'); 
		$Sdispo[$m]=	sec_convert($Sdispo[$m],'H'); 
		$Spause[$m]=	sec_convert($Spause[$m],'H'); 
		$Sdead[$m]=	sec_convert($Sdead[$m],'H'); 
		$Scustomer[$m]=	sec_convert($Scustomer[$m],'H'); 
		$Stime[$m]=		sec_convert($Stime[$m],'H'); 

		$RAWtime = $Stime[$m];
		$RAWwait = $Swait[$m];
		$RAWtalk = $Stalk[$m];
		$RAWdispo = $Sdispo[$m];
		$RAWpause = $Spause[$m];
		$RAWdead = $Sdead[$m];
		$RAWcustomer = $Scustomer[$m];

		$n=0;
		$user_name_found=0;
		while ($n < $users_to_print)
			{
			if ($Suser[$m] == "$ULuser[$n]")
				{
				$user_name_found++;
				$RAWname = $ULname[$n];
				$Sname[$m] = $ULname[$n];
				}
			$n++;
			}
	/*	if ($user_name_found < 1)
			{
			$RAWname =		"NOT IN SYSTEM";
			$Sname[$m] =	$RAWname;
			} */

		$n=0;
		$punches_found=0;
		while ($n < $punches_to_print)
			{
			if ($Suser[$m] == "$TCuser[$n]")
				{
				$punches_found++;
				$RAWtimeTCsec =		$TCtime[$n];
				$TOTtimeTC =		($TOTtimeTC + $TCtime[$n]);
				$StimeTC[$m]=		sec_convert($TCtime[$n],'H'); 
				$RAWtimeTC =		$StimeTC[$m];
				$StimeTC[$m] =		sprintf("%10s", $StimeTC[$m]);
				}
			$n++;
			}
		if ($punches_found < 1)
			{
			$RAWtimeTCsec =		"0";
			$StimeTC[$m]=		"0:00"; 
			$RAWtimeTC =		$StimeTC[$m];
			$StimeTC[$m] =		sprintf("%10s", $StimeTC[$m]);
			}

		### Check if the user had an AUTOLOGOUT timeclock event during the time period
		$TCuserAUTOLOGOUT = ' ';
		$stmt="select count(*) from vicidial_timeclock_log where event='AUTOLOGOUT' and user='$Suser[$m]' and event_date >= '$query_date_BEGIN' and event_date <= '$query_date_END';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$autologout_results = mysql_num_rows($rslt);
		if ($autologout_results > 0)
			{
			$row=mysql_fetch_row($rslt);
			if ($row[0] > 0)
				{
				$TCuserAUTOLOGOUT =	'*';
				$AUTOLOGOUTflag++;
				}
			}

		### BEGIN loop through each status ###
		$n=0;
		while ($n < $sub_status_count)
			{
			$Sstatus=$sub_statusesARY[$n];
			$SstatusTXT='';
			### BEGIN loop through each stat line ###
			$i=0; $status_found=0;
			while ( ($i < $subs_to_print) and ($status_found < 1) )
				{
				if ( ($Suser[$m]=="$PCuser[$i]") and ($Sstatus=="$sub_status[$i]") )
					{
					$USERcodePAUSE_MS =		sec_convert($PCpause_sec[$i],'H');
					if (strlen($USERcodePAUSE_MS)<1) {$USERcodePAUSE_MS='0';}
					$pfUSERcodePAUSE_MS =	sprintf("%10s", $USERcodePAUSE_MS);

					$SstatusTXT = sprintf("%10s", $pfUSERcodePAUSE_MS);
					$SstatusesHTML .= "<td> $SstatusTXT </td>";
					$SstatusesFILE .= ",$USERcodePAUSE_MS";
					$status_found++;
					}
				$i++;
				}
			if ($status_found < 1)
				{
				$SstatusesHTML .= "  <td>     0:00 </td>";
				$SstatusesFILE .= ",0:00";
				}
			### END loop through each stat line ###
			$n++;
			}
		### END loop through each status ###

		$Swait[$m]=		sprintf("%10s", $Swait[$m]); 
		$Stalk[$m]=		sprintf("%10s", $Stalk[$m]); 
		$Sdispo[$m]=	sprintf("%10s", $Sdispo[$m]); 
		$Spause[$m]=	sprintf("%10s", $Spause[$m]); 
		$Sdead[$m]=		sprintf("%10s", $Sdead[$m]); 
		$Scustomer[$m]=		sprintf("%10s", $Scustomer[$m]);
		$Scalls[$m]=	sprintf("%8s", $Scalls[$m]); 
		$Stime[$m]=		sprintf("%10s", $Stime[$m]); 

		if ($non_latin < 1)
			{
			$Sname[$m]=	sprintf("%-15s", $Sname[$m]); 
			while(strlen($Sname[$m])>15) {$Sname[$m] = substr("$Sname[$m]", 0, -1);}
			$Suser[$m] =		sprintf("%-8s", $Suser[$m]);
			while(strlen($Suser[$m])>8) {$Suser[$m] = substr("$Suser[$m]", 0, -1);}
			}
		else
			{	
			$Sname[$m]=	sprintf("%-45s", $Sname[$m]); 
			while(mb_strlen($Sname[$m],'utf-8')>15) {$Sname[$m] = mb_substr("$Sname[$m]", 0, -1,'utf-8');}
			$Suser[$m] =	sprintf("%-24s", $Suser[$m]);
			while(mb_strlen($Suser[$m],'utf-8')>8) {$Suser[$m] = mb_substr("$Suser[$m]", 0, -1,'utf-8');}
			}


		if ($file_download < 1) #<a href=\"./user_stats.php?user=$RAWuser\">$Suser[$m]</a>  | <td> $StimeTC[$m]$TCuserAUTOLOGOUT
			{
			#$Toutput = "<tr><td> $Sname[$m] <td> $Scalls[$m]  <td> $Stime[$m] <td> $Swait[$m] <td> $Stalk[$m] <td> $Sdispo[$m] <td> $Spause[$m] <td>$Sdead[$m] <td> $Scustomer[$m]  $SstatusesHTML\n"; #values
			}
		else
			{
			if (strlen($RAWtime)<1) {$RAWtime='0';}
			if (strlen($RAWwait)<1) {$RAWwait='0';}
			if (strlen($RAWtalk)<1) {$RAWtalk='0';}
			if (strlen($RAWdispo)<1) {$RAWdispo='0';}
			if (strlen($RAWpause)<1) {$RAWpause='0';}
			if (strlen($RAWdead)<1) {$RAWdead='0';}
			if (strlen($RAWcustomer)<1) {$RAWcustomer='0';}
			#$fileToutput = "$RAWname,$RAWuser,$RAWcalls,$RAWtimeTC,$RAWtime,$RAWwait,$RAWtalk,$RAWdispo,$RAWpause,$RAWdead,$RAWcustomer$SstatusesFILE\n";
			}

		$TOPsorted_output[$m] = $Toutput;
		$TOPsorted_outputFILE[$m] = $fileToutput;

		if ($stage == 'NAME')
			{
			$TOPsort[$m] =	'' . sprintf("%020s", $RAWname) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'ID')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWuser) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'LEADS')
			{
			$TOPsort[$m] =	'' . sprintf("%08s", $RAWcalls) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWcalls;
			}
		if ($stage == 'TIME')
			{
			$TOPsort[$m] =	'' . sprintf("%010s", $RAWtimeSEC) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWtimeSEC;
			}
		if ($stage == 'TCLOCK')
			{
			$TOPsort[$m] =	'' . sprintf("%010s", $RAWtimeTCsec) . '-----' . $m . '-----' . sprintf("%020s", $RAWuser);
			$TOPsortTALLY[$m]=$RAWtimeTCsec;
			}
		if (!ereg("NAME|ID|TIME|LEADS|TCLOCK",$stage))
			if ($file_download < 1)
				{/*echo "$Toutput";*/}
			else
				{/*$file_output .= "$fileToutput";*/}

		if ($TOPsortMAX < $TOPsortTALLY[$m]) {$TOPsortMAX = $TOPsortTALLY[$m];}

#		echo "$Suser[$m]|$Sname[$m]|$Swait[$m]|$Stalk[$m]|$Sdispo[$m]|$Spause[$m]|$Scalls[$m]\n";
		$m++;
		}
	##### END loop through each user formatting data for output


	$TOT_AGENTS = $m;
	$hTOT_AGENTS = sprintf("%4s", $TOT_AGENTS);
	$k=$m;

	if ($DB) {echo "Done analyzing...   $TOTwait|$TOTtalk|$TOTdispo|$TOTpause|$TOTdead|$TOTcustomer|$TOTALtime|$TOTcalls|$uc|<BR>\n";}


	### BEGIN sort through output to display properly ###
	if ( ($TOT_AGENTS > 0) and (ereg("NAME|ID|TIME|LEADS|TCLOCK",$stage)) )
		{
		if (ereg("ID",$stage))
			{sort($TOPsort, SORT_NUMERIC);}
		if (ereg("TIME|LEADS|TCLOCK",$stage))
			{rsort($TOPsort, SORT_NUMERIC);}
		if (ereg("NAME",$stage))
			{rsort($TOPsort, SORT_STRING);}

		$m=0;
		while ($m < $k)
			{
			$sort_split = explode("-----",$TOPsort[$m]);
			$i = $sort_split[1];
			$sort_order[$m] = "$i";
			if ($file_download < 1)
				{echo "$TOPsorted_output[$i]";}
			else
				{/*$file_output .= "$TOPsorted_outputFILE[$i]";*/}
			$m++;
			}
		}
	### END sort through output to display properly ###

	############################################################################
	##### END formatting data for output section
	############################################################################




	############################################################################
	##### BEGIN last line totals output section
	############################################################################
	$SUMstatusesHTML='';
	$SUMstatusesFILE='';
	$TOTtotPAUSE=0;
	$n=0;
	while ($n < $sub_status_count)
		{
		$Scalls=0;
		$Sstatus=$sub_statusesARY[$n];
		$SUMstatusTXT='';
		### BEGIN loop through each stat line ###
		$i=0; $status_found=0;
		while ($i < $subs_to_print)
			{
			if ($Sstatus=="$sub_status[$i]")
				{
				$Scalls =		($Scalls + $PCpause_sec[$i]);
				$status_found++;
				}
			$i++;
			}
		### END loop through each stat line ###
		if ($status_found < 1)
			{
			$SUMstatusesHTML .= "          0 |";
			}
		else
			{
			$TOTtotPAUSE = ($TOTtotPAUSE + $Scalls);

			$USERsumstatPAUSE_MS =		sec_convert($Scalls,'H'); 
			$pfUSERsumstatPAUSE_MS =	sprintf("%11s", $USERsumstatPAUSE_MS);

			$SUMstatusTXT = sprintf("%10s", $pfUSERsumstatPAUSE_MS);
			$SUMstatusesHTML .= "$SUMstatusTXT |";
			$SUMstatusesFILE .= ",$USERsumstatPAUSE_MS";
			}
		$n++;
		}
	### END loop through each status ###

	### call function to calculate and print dialable leads
	$TOTwait = sec_convert($TOTwait,'H');
	$TOTtalk = sec_convert($TOTtalk,'H');
	$TOTdispo = sec_convert($TOTdispo,'H');
	$TOTpause = sec_convert($TOTpause,'H');
	$TOTdead = sec_convert($TOTdead,'H');
	$TOTcustomer = sec_convert($TOTcustomer,'H');
	$TOTALtime = sec_convert($TOTALtime,'H');
	$TOTtimeTC = sec_convert($TOTtimeTC,'H');

	$hTOTcalls = sprintf("%8s", $TOTcalls);
	$hTOTwait =	sprintf("%11s", $TOTwait);
	$hTOTtalk =	sprintf("%11s", $TOTtalk);
	$hTOTdispo =	sprintf("%11s", $TOTdispo);
	$hTOTpause =	sprintf("%11s", $TOTpause);
	$hTOTdead =	sprintf("%11s", $TOTdead);
	$hTOTcustomer =	sprintf("%11s", $TOTcustomer);
	$hTOTALtime = sprintf("%11s", $TOTALtime);
	$hTOTtimeTC = sprintf("%11s", $TOTtimeTC);
	###### END LAST LINE TOTALS FORMATTING ##########

echo "</table>";

	if ($file_download < 1)
		{
		
		
		#echo "<table>";
		#echo "<tr><td>Total Agents:$hTOT_AGENTS<td>$hTOTcalls<td>$hTOTtimeTC<td>$hTOTALtime<td>$hTOTwait<td>$hTOTtalk<td>$hTOTdispo<td>$hTOTpause<td>$hTOTdead<td>$hTOTcustomer<td>$SUMstatusesHTML</tr>";
		#echo "</table>";
		
		
	/*	echo "+-----------------+----------+----------+------------+------------+------------+------------+------------+------------+------------+------------+   +$sub_statusesHEAD\n";
		echo "|  TOTALS        AGENTS:$hTOT_AGENTS | $hTOTcalls |$hTOTtimeTC |$hTOTALtime |$hTOTwait |$hTOTtalk |$hTOTdispo |$hTOTpause |$hTOTdead |$hTOTcustomer |   |$SUMstatusesHTML\n";
		echo "+-----------------+----------+----------+------------+------------+------------+------------+------------+------------+------------+------------+   +$sub_statusesHEAD\n"; */
		if ($AUTOLOGOUTflag > 0)
			{echo "     * denotes AUTOLOGOUT from timeclock\n";}
		echo "\n\n</PRE>";
		}
	else
		{
		#$file_output .= "TOTALS,$TOT_AGENTS,$TOTcalls,$TOTtimeTC,$TOTALtime,$TOTwait,$TOTtalk,$TOTdispo,$TOTpause,$TOTdead,$TOTcustomer$SUMstatusesFILE\n";
		}
	}

	############################################################################
	##### END formatting data for output section
	############################################################################





if ($file_download > 0)
	{
	$FILE_TIME = date("Ymd-His");
	$CSVfilename = "AGENT_TIME$US$FILE_TIME.csv";

	// We'll be outputting a TXT file
	header('Content-type: application/octet-stream');

	// It will be called LIST_101_20090209-121212.txt
	header("Content-Disposition: attachment; filename=\"$CSVfilename\"");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	ob_clean();
	flush();

	#echo "$file_output";

	exit;
	}


############################################################################
##### BEGIN HTML form section
############################################################################



############################################################################
##### END HTML form section
############################################################################


$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);
echo "<font size=1 color=white>$RUNtime</font>\n";


##### BEGIN horizontal yellow transparent bar graph overlay on top of agent stats
echo "</span>\n";
echo "<span style=\"position:absolute;left:3px;top:3px;z-index:18;\"  id=agent_status_bars>\n";
echo "<PRE><FONT SIZE=2>\n\n\n\n\n\n\n\n";

if ($stage == 'NAME') {$k=0;}
$m=0;
while ($m < $k)
	{
	$sort_split = explode("-----",$TOPsort[$m]);
	$i = $sort_split[1];
	$sort_order[$m] = "$i";

	if ( ($TOPsortTALLY[$i] < 1) or ($TOPsortMAX < 1) )
		{echo "                              \n";}
	else
		{
		echo "                              <SPAN class=\"yellow\">";
		$TOPsortPLOT = ( ($TOPsortTALLY[$i] / $TOPsortMAX) * 110 );
		$h=0;
		while ($h <= $TOPsortPLOT)
			{
			echo " ";
			$h++;
			}
		echo "</SPAN>\n";
		}
	$m++;
	}

echo "</FONT></PRE></span>\n";
##### END horizontal yellow transparent bar graph overlay on top of agent stats


?>

</BODY></HTML>
