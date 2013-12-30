<?
require('dbconnect.php');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_new.csv');
###################################################
if (isset($_POST['camp_options'])) {$campanhas = $_POST['camp_options'];}
if (isset($_POST['camp_options3'])) {$campanhas = $_POST['camp_options3'];}
$campanhas_count = count($campanhas);
if (isset($_POST['camp_options2'])) {$campanhas = $_POST['camp_options2']; $flag=999;}

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
if ($flag==999)
	{
	$campanhas_SQL = "campaign_id='$campanhas'";
	}
else
	{
	$campanhas_SQL = "campaign_id IN($campanhas_IN)";
	}

###################################################
if (isset($_POST['feed_options'])) {$feedbacks = $_POST['feed_options'];}
if (isset($_POST['feed_options2'])) {$feedbacks = $_POST['feed_options2'];}
if (isset($_POST['feed_options3'])) {$feedbacks = $_POST['feed_options3'];}
$feedbacks_count = count($feedbacks);
for ($i=0;$i<$feedbacks_count;$i++)
	{
	if ($feedbacks_count == 1) 
		{
		$feedbacks_IN = "'".$feedbacks[$i]."'"; 
		}
	elseif ($feedbacks_count-1 == $i)
		{
		$feedbacks_IN .= "'".$feedbacks[$i]."'";	
		}	
	else
		{
		$feedbacks_IN .= "'".$feedbacks[$i]."',";
		}
	}
if ((ereg("--ALL--",$feedbacks_IN)) or ($feedbacks_count < 1))
	{
	$feedbacks_SQL = "";
	}
else
	{
	$feedbacks_SQL = "status IN($feedbacks_IN)";
	}

###################################################
if (isset($_POST['db_options'])) {$dbs = $_POST['db_options'];}
$flag=0;
$dbs_count = count($dbs);

for ($i=0;$i<$dbs_count;$i++)
	{
	if ($dbs_count == 1) 
		{
		$dbs_IN = "'".$dbs[$i]."'"; 
		}
	elseif ($dbs_count-1 == $i)
		{
		$dbs_IN .= "'".$dbs[$i]."'";	
		}	
	else
		{
		$dbs_IN .= "'".$dbs[$i]."',";
		}
	}
if ($flag==999)
	{
	$dbs_SQL = "list_id='$dbs'";
	}
else
	{
	$dbs_SQL = "list_id IN($dbs_IN)";
	}
#######################################################################################################################
#######################################################################################################################

#######################################################################################################################
### REPORT GERAL POR CAMPANHA
#######################################################################################################################
if (isset($_POST['geral_camp'])) {
$query = "	SELECT 	list_id
			FROM 	vicidial_lists
			WHERE	$campanhas_SQL
			AND 	list_id<>'998'";
$query = mysql_query($query, $link) or die(mysql_error());			
$listas_count = mysql_num_rows($query);
for ($i=0;$i<$listas_count;$i++)
{
	$listas = mysql_fetch_row($query);
	if ($listas_count == 1) 
		{
		$listas_IN = "'".$listas[0]."'"; 
		}
	elseif ($listas_count-1 == $i)
		{
		$listas_IN .= "'".$listas[0]."'";	
		}	
	else
		{
		$listas_IN .= "'".$listas[0]."',";
		}
	$listas_SQL = "list_id IN($listas_IN)";	
}	
// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings

if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("Y-m-d", strtotime("+1 day".$_POST['data_final']));} 
#if ($data_inicial == $data_final) {$data_final = date("Y-m-d", strtotime("+1 day".$data_inicial));}


#$datafim = date("Y-m-d", strtotime("+1 day".$data));

fputcsv($output, array('Title',
        'Campaign Number',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No',
        'Mobile Phone No',
        'Work Phone No',
        'Date of Birth',
        'Contact No',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
		'Operador',
		'Feedback',
		'Campanha'),";");


// fetch the data



$con = mysql_connect("localhost","sipsadmin","sipsps2012");
	if (!$con)
	{
		die('Não me consegui ligar' . mysql_error());
	}
	
	#mysql_query("SET NAMES 'UTF8'"); */
		
	$datafim = date("Y-m-d", strtotime("+1 day".$data)); 
	$datainicio = $data;
	mysql_select_db("asterisk", $con);
	
	$curCamp = mysql_query("SELECT campaign_id from vicidial_campaigns WHERE active LIKE 'Y'", $link) or die(mysql_error());
				
	for($i=0;$i<mysql_num_rows($curCamp);$i++) {
			
        	$camp_id = mysql_fetch_assoc($curCamp); 
			$thisCampaign = $camp_id['campaign_id'];
			$stmt="SHOW TABLES LIKE \"custom_$thisCampaign\";"; 
			$rslt=mysql_query($stmt, $link);
			$tablecount_to_print = mysql_num_rows($rslt);
			if ($tablecount_to_print > 0) 
				{	
				
        	  
			
			
			
			$rows = mysql_query("SELECT * FROM vicidial_list INNER JOIN custom_".$thisCampaign." ON vicidial_list.lead_id = custom_".$thisCampaign.".lead_id WHERE vicidial_list.status IN($feedbacks_IN) AND vicidial_list.list_id IN($listas_IN) AND vicidial_list.last_local_call_time >= '$data_inicial' AND vicidial_list.last_local_call_time <= '$data_final'") or die(mysql_error());
			
			
			
			while ($row = mysql_fetch_assoc($rows)) {
			$feedback_full = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE status='".$row['status']."'", $link);
			$feedback_full = mysql_fetch_assoc($feedback_full);
			$feedback_full = $feedback_full['status_name'];	
			$camp_full = mysql_query("SELECT campaign_name FROM vicidial_campaigns INNER JOIN vicidial_lists ON vicidial_lists.campaign_id=vicidial_campaigns.campaign_id WHERE vicidial_lists.list_id=".$row['list_id'], $link) or die(mysql_error());
			$camp_full = mysql_fetch_assoc($camp_full);
			$camp_full = $camp_full['campaign_name'];
			$cod = "";
			if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
			fputcsv($output, array(
			
				$row['title'],
				$row['source_id'],
				$row['first_name'],
				$row['middle_initial'],
				$row['last_name'],
				$row['address1'],
				$row['address2'],
				$row['address3'],
				$row['state'],
				$row['postal_code'],
				"",
				$row['vendor_lead_code'],
				$row['city'],
				$row['province'],
				$row['country_code'],
				$row['phone_number'],
				$row['alt_phone'],
				"",
				$row['date_of_birth'],
				$row['owner'],
				"",
				"",
				"",
				$cod,
				"",
				"",
				"",
				"",
				$row['marchora'],
				$row['marcdata'],
				$row['tipoconsulta'],
				"",
				$row['obs'],
				"",
				"",
				$row['user'],
				$feedback_full,
				$camp_full
			
			),";"); }

			}
			}
}
#######################################################################################################################
### FIM REPORT GERAL POR CAMPANHA
#######################################################################################################################
#######################################################################################################################
### REPORT GERAL POR BASE DE DADOS
#######################################################################################################################
if (isset($_POST['geral_db'])) {

$db_options = $_POST['db_options'];

		
$listas_count = count($db_options);
for ($i=0;$i<$listas_count;$i++)
{
	$listas = $db_options[$i];
	if ($listas_count == 1) 
		{
		$listas_IN = "'".$db_options[$i]."'"; 
		}
	elseif ($listas_count-1 == $i)
		{
		$listas_IN .= "'".$db_options[$i]."'";	
		}	
	else
		{
		$listas_IN .= "'".$db_options[$i]."',";
		}
	$listas_SQL = "list_id IN($listas_IN)";	
}	
// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings

if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("Y-m-d", strtotime("+1 day".$_POST['data_final']));} 
#if ($data_inicial == $data_final) {$data_final = date("Y-m-d", strtotime("+1 day".$data_inicial));}


#$datafim = date("Y-m-d", strtotime("+1 day".$data));

fputcsv($output, array('Title',
        'Campaign Number',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No',
        'Mobile Phone No',
        'Work Phone No',
        'Date of Birth',
        'Contact No',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
		'Operador',
		'Feedback',
		'Base de Dados'),";");


// fetch the data



$con = mysql_connect("localhost","sipsadmin","sipsps2012");
	if (!$con)
	{
		die('Não me consegui ligar' . mysql_error());
	}
	
	#mysql_query("SET NAMES 'UTF8'"); */
		
	$datafim = date("Y-m-d", strtotime("+1 day".$data)); 
	$datainicio = $data;
	mysql_select_db("asterisk", $con);
	
	$curCamp = mysql_query("SELECT campaign_id from vicidial_campaigns WHERE active LIKE 'Y'", $link) or die(mysql_error());
				
	for($i=0;$i<mysql_num_rows($curCamp);$i++) {
			
        	$camp_id = mysql_fetch_assoc($curCamp); 
			$thisCampaign = $camp_id['campaign_id'];
			$stmt="SHOW TABLES LIKE \"custom_$thisCampaign\";"; 
			$rslt=mysql_query($stmt, $link);
			$tablecount_to_print = mysql_num_rows($rslt);
			if ($tablecount_to_print > 0) 
				{	
				
        	  
			
			
			
			$rows = mysql_query("SELECT * FROM vicidial_list INNER JOIN custom_".$thisCampaign." ON vicidial_list.lead_id = custom_".$thisCampaign.".lead_id WHERE vicidial_list.status IN($feedbacks_IN) AND vicidial_list.list_id IN($listas_IN) AND vicidial_list.last_local_call_time >= '$data_inicial' AND vicidial_list.last_local_call_time <= '$data_final'") or die(mysql_error());
			
			
			
			while ($row = mysql_fetch_assoc($rows)) {
			$feedback_full = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE status='".$row['status']."'", $link);
			$feedback_full = mysql_fetch_assoc($feedback_full);
			$feedback_full = $feedback_full['status_name'];	
			$camp_full = mysql_query("SELECT campaign_name FROM vicidial_campaigns INNER JOIN vicidial_lists ON vicidial_lists.campaign_id=vicidial_campaigns.campaign_id WHERE vicidial_lists.list_id=".$row['list_id'], $link) or die(mysql_error());
			$camp_full = mysql_fetch_assoc($camp_full);
			$camp_full = $camp_full['campaign_name'];
			$cod = "";
			if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
			fputcsv($output, array(
			
				$row['title'],
				$row['source_id'],
				$row['first_name'],
				$row['middle_initial'],
				$row['last_name'],
				$row['address1'],
				$row['address2'],
				$row['address3'],
				$row['state'],
				$row['postal_code'],
				"",
				$row['vendor_lead_code'],
				$row['city'],
				$row['province'],
				$row['country_code'],
				$row['phone_number'],
				$row['alt_phone'],
				"",
				$row['date_of_birth'],
				$row['owner'],
				"",
				"",
				"",
				$cod,
				"",
				"",
				"",
				"",
				$row['marchora'],
				$row['marcdata'],
				$row['tipoconsulta'],
				"",
				$row['obs'],
				"",
				"",
				$row['user'],
				$feedback_full,
				$camp_full
			
			),";"); }

			}
			}
}
#######################################################################################################################
### FIM REPORT GERAL POR BASE DE DADOS
#######################################################################################################################
#######################################################################################################################
# TOTAL POR BASE DE DADOS E OPERADOR
#######################################################################################################################
if (isset($_POST['totais_db'])) {
if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("Y-m-d", strtotime("+1 day".$_POST['data_final']));} 
if (isset($_POST['db_options'])) {$db_options = $_POST['db_options'];}
##################################################### Nome da Base de Dados
$query = "	SELECT 	list_name 
			FROM 	vicidial_lists
			WHERE 	list_id='$db_options'";
$query = mysql_query($query, $link);
$list_name = mysql_fetch_assoc($query);
$list_name = $list_name['list_name'];
##################################################### Contagem dos Registos da DB
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	list_id='$db_options'";
$query = mysql_query($query, $link);	
$total_leads = mysql_fetch_assoc($query);
$total_leads = $total_leads['count(list_id)'];
#####################################################
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	list_id='$db_options'
			AND 	status='CBHOLD';";
$query = mysql_query($query, $link);	
$total_cbhold = mysql_fetch_assoc($query);
$total_cbhold = $total_cbhold['count(list_id)'];
#####################################################	
$query = "	SELECT 	user 
			FROM 	vicidial_users
			WHERE 	user_level<9;";
$query = mysql_query($query, $link);
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	$user[$i]= $row['user'];
}
#####################################################
$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Base de Dados:", $list_name),";");
fputcsv($output, array(" ","Total de Registos:",$total_leads),";");	
fputcsv($output, array(" ","Callbacks Agendados:",$total_cbhold),";");	
##################################################### Construção das linhas 
foreach ($user as $key=>$value)
{
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	user='$value'
			AND 	list_id='$db_options'
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)";
$query = mysql_query($query, $link);	
$total_calls = mysql_fetch_assoc($query);
$total_calls = $total_calls['count(list_id)'];	

fputcsv($output, array(" "),";");	
fputcsv($output, array(" ","$value", "Total Chamadas:", $total_calls),";");		

$query = "	SELECT	count(status),
					status,
					user
			FROM	vicidial_list  
			WHERE	user='$value'
			AND 	list_id='$db_options'
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)
			GROUP BY user,status
			;";
$query = mysql_query($query, $link) or die (mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name']; }
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name'];}
	fputcsv($output, array(" ", " ", $feedback_full, $row['count(status)'] ),";");
		
}
}		
}	
#######################################################################################################################
# FIM TOTAL POR BASE DE DADOS E OPERADOR
#######################################################################################################################
#######################################################################################################################
# TOTAL POR CAMPANHAS E OPERADOR
#######################################################################################################################
if (isset($_POST['totais_camp'])) {
if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("Y-m-d", strtotime("+1 day".$_POST['data_final']));} 
if (isset($_POST['camp_options'])) {$camp_options = $_POST['camp_options'];}
#####################################################
$query = "	SELECT 	list_id
			FROM 	vicidial_lists
			WHERE	campaign_id='$camp_options'
			AND 	list_id<>'998'";
$query = mysql_query($query, $link) or die(mysql_error());			
$listas_count = mysql_num_rows($query);
for ($p=0;$p<$listas_count;$p++)
{
	$listas = mysql_fetch_row($query);
	if ($listas_count == 1) 
		{
		$listas_IN = "'".$listas[0]."'"; 
		}
	elseif ($listas_count-1 == $p)
		{
		$listas_IN .= "'".$listas[0]."'";	
		}	
	else
		{
		$listas_IN .= "'".$listas[0]."',"; 
		}
	$listas_SQL = "list_id IN($listas_IN)";	
}	
##################################################### Nome da Base de Dados
$query = "	SELECT 	campaign_name 
			FROM 	vicidial_campaings
			WHERE 	campaign_id='$camp_options'";
$query = mysql_query($query, $link);
$camp_name = mysql_fetch_assoc($query);
$camp_name = $camp_name['campaign_name'];
##################################################### Contagem dos Registos da DB
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	$listas_SQL";
$query = mysql_query($query, $link);	
$total_leads = mysql_fetch_assoc($query);
$total_leads = $total_leads['count(list_id)'];
#####################################################
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	$listas_SQL
			AND 	status='CBHOLD';";
$query = mysql_query($query, $link);	
$total_cbhold = mysql_fetch_assoc($query);
$total_cbhold = $total_cbhold['count(list_id)'];
#####################################################	
$query = "	SELECT 	user 
			FROM 	vicidial_users
			WHERE 	user_level<9;";
$query = mysql_query($query, $link);
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	$user[$i]= $row['user'];
}
#####################################################
$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Campanha:", $camp_name),";");
fputcsv($output, array(" ","Total de Registos:",$total_leads),";");	
fputcsv($output, array(" ","Callbacks Agendados:",$total_cbhold),";");	
##################################################### Construção das linhas 
foreach ($user as $key=>$value)
{
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	user='$value'
			AND 	list_id IN(SELECT list_id FROM vicidial_lists WHERE campaign_id='$camp_options' AND list_id<>998)
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)";
$query = mysql_query($query, $link);	
$total_calls = mysql_fetch_assoc($query);
$total_calls = $total_calls['count(list_id)'];	

fputcsv($output, array(" "),";");	
fputcsv($output, array(" ","$value", "Total Chamadas:", $total_calls),";");		

$query = "	SELECT	count(status),
					status,
					user
			FROM	vicidial_list  
			WHERE	user='$value'
			AND 	list_id IN(SELECT list_id FROM vicidial_lists WHERE campaign_id='$camp_options' AND list_id<>998)
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)
			GROUP BY user,status
			;";
$query = mysql_query($query, $link) or die (mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name']; }
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name'];}
	fputcsv($output, array(" ", " ", $feedback_full, $row['count(status)'] ),";");
		
}
}		
}	
#######################################################################################################################
# FIM TOTAL POR BASE DE DADOS E OPERADOR
#######################################################################################################################
#######################################################################################################################
# TOTAL POR CAMPANHAS
#######################################################################################################################
if (isset($_POST['totais_camp2'])) {
if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("Y-m-d", strtotime("+1 day".$_POST['data_final']));} 
if (isset($_POST['camp_options'])) {$camp_options = $_POST['camp_options'];}
#####################################################
$query = "	SELECT 	list_id
			FROM 	vicidial_lists
			WHERE	campaign_id='$camp_options'
			AND 	list_id<>'998'";
$query = mysql_query($query, $link) or die(mysql_error());			
$listas_count = mysql_num_rows($query);
for ($p=0;$p<$listas_count;$p++)
{
	$listas = mysql_fetch_row($query);
	if ($listas_count == 1) 
		{
		$listas_IN = "'".$listas[0]."'"; 
		}
	elseif ($listas_count-1 == $p)
		{
		$listas_IN .= "'".$listas[0]."'";	
		}	
	else
		{
		$listas_IN .= "'".$listas[0]."',"; 
		}
	$listas_SQL = "list_id IN($listas_IN)";	
}	
##################################################### Nome da Base de Dados
$query = "	SELECT 	campaign_name 
			FROM 	vicidial_campaigns
			WHERE 	campaign_id='$camp_options'";
$query = mysql_query($query, $link) or die(mysql_error());
$camp_name = mysql_fetch_assoc($query);
$camp_name = $camp_name['campaign_name'];
##################################################### Contagem dos Registos da DB
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	$listas_SQL";
$query = mysql_query($query, $link);	
$total_leads = mysql_fetch_assoc($query);
$total_leads = $total_leads['count(list_id)'];
#####################################################
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	$listas_SQL
			AND 	status='CBHOLD';";
$query = mysql_query($query, $link);	
$total_cbhold = mysql_fetch_assoc($query);
$total_cbhold = $total_cbhold['count(list_id)'];
#####################################################	
$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Campanha:",$camp_name),";");
fputcsv($output, array(" ","Total de Registos:",$total_leads),";");	
fputcsv($output, array(" ","Callbacks Agendados:",$total_cbhold),";");	
fputcsv($output, array(" "),";");	
		

$query = "	SELECT	count(status),
					status
			FROM	vicidial_list  
			
			WHERE 	list_id IN(SELECT list_id FROM vicidial_lists WHERE campaign_id='$camp_options' AND list_id<>998)
			
			AND		(last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			OR		last_local_call_time = '2008-01-01')
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)
			GROUP BY status
			;";
$query = mysql_query($query, $link) or die (mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name']; }
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name'];}
	fputcsv($output, array(" ", $feedback_full, $row['count(status)'] ),";");
		
}
		
}	
#######################################################################################################################
# FIM TOTAL POR CAMPANHAS
#######################################################################################################################
#######################################################################################################################
# TOTAL POR BASE DE DADOS
#######################################################################################################################
if (isset($_POST['totais_db2'])) {
if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("Y-m-d", strtotime("+1 day".$_POST['data_final']));} 
if (isset($_POST['db_options'])) {$db_options = $_POST['db_options'];}
##################################################### Nome da Base de Dados
$query = "	SELECT 	list_name 
			FROM 	vicidial_lists
			WHERE 	list_id='$db_options'";
$query = mysql_query($query, $link) or die(mysql_error());
$list_name = mysql_fetch_assoc($query);
$list_name = $list_name['list_name'];
##################################################### Contagem dos Registos da DB
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	list_id='$db_options'";
$query = mysql_query($query, $link);	
$total_leads = mysql_fetch_assoc($query);
$total_leads = $total_leads['count(list_id)'];
#####################################################
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	list_id='$db_options'
			AND 	status='CBHOLD';";
$query = mysql_query($query, $link);	
$total_cbhold = mysql_fetch_assoc($query);
$total_cbhold = $total_cbhold['count(list_id)'];
#####################################################	
$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Base de Dados:",$list_name),";");
fputcsv($output, array(" ","Total de Registos:",$total_leads),";");	
fputcsv($output, array(" ","Callbacks Agendados:",$total_cbhold),";");	
fputcsv($output, array(" "),";");	
		

$query = "	SELECT	count(status),
					status
			FROM	vicidial_list  
			
			WHERE 	list_id='$db_options'
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)
			GROUP BY status
			;";
$query = mysql_query($query, $link) or die (mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name']; }
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name'];}
	fputcsv($output, array(" ", $feedback_full, $row['count(status)'] ),";");
		
}
		
}	
#######################################################################################################################
# FIM TOTAL POR BASE DE DADOS
#######################################################################################################################
?>