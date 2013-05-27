<?
require('../../../../ini/dbconnect_noutf8.php');
require('../../../../ini/functions.php');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_new.csv');
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
	
$SelectedCampaigns = $_POST['camp_options'];
$CountSelectedCampaigns = count($SelectedCampaigns);
for ($i=0;$i<$CountSelectedCampaigns;$i++)
	{
	if ($CountSelectedCampaigns == 1) 
		{
		$campaigns_IN = "'".$SelectedCampaigns[$i]."'"; 
		}
	elseif ($CountSelectedCampaigns - 1 == $i)
		{
		$campaigns_IN .= "'".$SelectedCampaigns[$i]."'";	
		}	
	else
		{
		$campaigns_IN .= "'".$SelectedCampaigns[$i]."',";
		}
	}

$SelectedFeedbacks = $_POST['feed_options'];
$CountSelectedFeedbacks = count($SelectedFeedbacks);
for ($i=0;$i<$CountSelectedFeedbacks;$i++)
	{
	if ($CountSelectedFeedbacks == 1) 
		{
		$feedbacks_IN = "'".$SelectedFeedbacks[$i]."'"; 
		}
	elseif ($CountSelectedFeedbacks - 1 == $i)
		{
		$feedbacks_IN .= "'".$SelectedFeedbacks[$i]."'";	
		}	
	else
		{
		$feedbacks_IN .= "'".$SelectedFeedbacks[$i]."',";
		}
	} 

//if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
//if (isset($_POST['data_final'])) {$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));} 


$data_inicial = $_POST['data_inicial'];
if($_POST['data_final']==$data_inicial) { $data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final'])); } else { $data_final = $_POST['data_final']; }



$output = fopen('php://output', 'w');
fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
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
		'',
		'Operador',
		'Feedback',
		'Campanha',
		'Data da Chamada',
		'Avisos'),";");



	//INBOUND
	
	// B.tipoconsulta IN ('CATOS','Branch','Home') AND B.marcdata IS NOT NULL AND B.marcdata <> '0000-00-00' AND

	$queryshow = "SHOW TABLES LIKE 'custom_%'";
	$queryshow = mysql_query($queryshow, $link);
	
	for($f=0;$f<mysql_num_rows($queryshow);$f++)
	{
	$rowshow = mysql_fetch_row($queryshow);
	if(($f+1)==mysql_num_rows($queryshow)){ $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) "; } else { $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) UNION ALL ";}
	} 
	
 	$query = "SELECT * FROM `vicidial_list` A LEFT JOIN ( $build_query ) B on A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status WHERE  A.list_id IN('998', '0', '999') AND A.status IN($feedbacks_IN) AND A.last_local_call_time BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time";
	$query = mysql_query($query) or die(mysql_error());

	while ($row = mysql_fetch_assoc($query)) {
				
			$cod = "";
			
			if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
			
			$campid=$row['extra1'];

			$no=$row['extra2'];
			
			//if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
			//if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
			$c_message = "Sem Campanha/Inbound/Chamada Manual";
			if($row['tipoconsulta']==null || $row['tipoconsulta']=="" || $row['tipoconsulta']=="semconsulta") { $c_message = "Lead Duplicada - Ignorar/Dados Incompletos"; }
			if(		(preg_match("/", $row['consultorio'])===1) 	) {$c_message = "bom";}
		
		
			fputcsv($output, array(
			
				$row['title'],
				$campid,
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
				$no,
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
				"",
				$row['user'],
				$row['status_name'],
				$c_message,
				$row['last_local_call_time']
			
			),";"); }

	//OUTBOUND

	for($i=0;$i<count($SelectedCampaigns);$i++) {
	
	$sQuery = "SELECT list_id FROM vicidial_lists WHERE campaign_id = '$SelectedCampaigns[$i]' ";
	$sQuery = mysql_query($sQuery, $link) or die(mysql_error());			
	$CountDBs = mysql_num_rows($sQuery);
	$lists_IN = "";
	for ($p=0;$p<$CountDBs;$p++)
	{
		$row = mysql_fetch_row($sQuery);
		if ($CountDBs == 1) 
			{
			$lists_IN = "'".$row[0]."'"; 
			}
		elseif ($CountDBs - 1 == $p)
			{
			$lists_IN .= "'".$row[0]."'";	
			}	
		else
			{
			$lists_IN .= "'".$row[0]."',";
			}
	}
		
	
	$SelectedCampaigns[$i] = strtoupper($SelectedCampaigns[$i]);
	
	$sQuery = "SHOW TABLES LIKE 'custom_$SelectedCampaigns[$i]'"; 

	$rQuery = mysql_query($sQuery, $link);
	$TablesToPrint = mysql_num_rows($rQuery);
	if ($TablesToPrint > 0) 
		{	

		$sQuery = "SELECT * FROM vicidial_list A LEFT JOIN custom_$SelectedCampaigns[$i] B ON A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status INNER JOIN vicidial_lists D ON A.list_id=D.list_id INNER JOIN (SELECT campaign_id, campaign_name FROM vicidial_campaigns GROUP BY campaign_name) E ON D.campaign_id=E.campaign_id WHERE A.list_id IN($lists_IN) AND A.status IN($feedbacks_IN) AND A.last_local_call_time BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time                       ";
		$rQuery = mysql_query($sQuery, $link) or die(mysql_error()); 

			while ($row = mysql_fetch_assoc($rQuery)) {
				
			$cod = "";
			
			
			
			$campid=$row['extra1'];


			$no=$row['extra2'];
			
			if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
			if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
			

			// controlo de qualidade dos dados
			if($row['status']=="MARC" || $row['status']=="NOVOCL") {
			
			$c_message = "";
			$e_message = "";
			$f_message = "";
			switch($row['tipoconsulta']){
			
			case "CATOS": { $cod = $row['consultorio']; 
				if(		($row['consultorio'] == "" || $row['consultorio'] == "nenhum" || $row['consultorio'] == null)	 	){  $c_message = " Dados Incompletos "; $e_message .= "| Sem CATOS Associado "; } 
				if(		($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)	 	){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
			
			case "Branch": { $cod = $row['consultoriodois']; 
				if(		($row['consultoriodois'] == "" || $row['consultoriodois'] == "nenhum" || $row['consultoriodois'] == null)	 	){  $c_message = " Dados Incompletos "; $e_message .= "| Sem Branch Associado "; } 
				if(		($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)	 	){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
			case "Home": { $cod = ""; if(		($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)	 	){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
			case "": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
			case "semconsulta": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
			case null: { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
			}
			
			$f_message = $c_message . $e_message; }
			
	/*		if($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") { $c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: Tipo Consulta"; }
			if(		($row['consultorio'] != "" && $row['consultorio']!= "nenhum" && $row['consultorio'] != null) && ($row['tipoconsulta']!="CATOS")		 	) {$c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: CATOS";} 
			if(		($row['consultoriodois'] != "" && $row['consultoriodois']!= "nenhum" && $row['consultoriodois'] != null) && ($row['tipoconsulta']!="Branch")		 	) {$c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: Branch";} 

	*/		
			fputcsv($output, array(
			
				$row['title'],
				$campid,
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
				$no,
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
				"",
				$row['user'],
				$row['status_name'],
				$row['campaign_name'],
				$row['last_local_call_time'],
				$f_message
			
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
	
$SelectedDBs = $_POST['db_options'];

$CountSelectedDBs = count($SelectedDBs);
for ($i=0;$i<$CountSelectedDBs;$i++)
	{
	if ($CountSelectedDBs == 1) 
		{
		$dbs_IN = "'".$SelectedDBs[$i]."'"; 
		}
	elseif ($CountSelectedDBs - 1 == $i)
		{
		$dbs_IN .= "'".$SelectedDBs[$i]."'";	
		}	
	else
		{
		$dbs_IN .= "'".$SelectedDBs[$i]."',";
		}
	}


$data_inicial = $_POST['data_inicial'];
if($_POST['data_final']==$data_inicial) { $data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final'])); } else { $data_final = $_POST['data_final']; }



$output = fopen('php://output', 'w');
fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
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
		'',
		'Operador',
		'Feedback',
		'Campanha',
		'Data da Chamada'),";");



	//INBOUND

	$queryshow = "SHOW TABLES LIKE 'custom_%'";
	$queryshow = mysql_query($queryshow, $link);
	
	for($f=0;$f<mysql_num_rows($queryshow);$f++)
	{
	$rowshow = mysql_fetch_row($queryshow);
	if(($f+1)==mysql_num_rows($queryshow)){ $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) "; } else { $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) UNION ALL ";}
	} 
	
 	$query = "SELECT * FROM `vicidial_list` A LEFT JOIN ( $build_query ) B on A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status WHERE B.tipoconsulta IN ('CATOS','Branch','Home') AND B.marcdata IS NOT NULL AND B.marcdata <> '0000-00-00' AND A.list_id IN('998', '0', '999') AND A.status IN('MARC','NOVOCL') AND A.modify_date BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time";
	$query = mysql_query($query) or die(mysql_error());

	while ($row = mysql_fetch_assoc($query)) {
				
			$cod = "";
			
			if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
			
			$campid=$row['extra1'];

			$no=$row['extra2'];
			
			//if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
			//if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
		
			fputcsv($output, array(
			
				$row['title'],
				$campid,
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
				$no,
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
				"",
				$row['user'],
				$row['status_name'],
				"Sem Campanha/Inbound/Chamada Manual",
				$row['last_local_call_time']
			
			),";"); }

	//OUTBOUND

	for($i=0;$i<count($SelectedDBs);$i++) {
	
	
		
	$sQuery = "SELECT campaign_id FROM vicidial_lists WHERE list_id = '$SelectedDBs[$i]' ";
	$sQuery = mysql_query($sQuery, $link) or die(mysql_error());
	$row_campid = mysql_fetch_row($sQuery);
	$row_campid = strtoupper($row_campid[0]);
	
	//echo $row_campid;
				
/*	$CountDBs = mysql_num_rows($sQuery);
	$lists_IN = "";
	for ($p=0;$p<$CountDBs;$p++)
	{
		$row = mysql_fetch_row($sQuery);
		if ($CountDBs == 1) 
			{
			$lists_IN = "'".$row[0]."'"; 
			}
		elseif ($CountDBs - 1 == $p)
			{
			$lists_IN .= "'".$row[0]."'";	
			}	
		else
			{
			$lists_IN .= "'".$row[0]."',";
			}
	} */
		
	
//	$SelectedCampaigns[$i] = strtoupper($SelectedCampaigns[$i]);
	
	$sQuery = "SHOW TABLES LIKE 'custom_$row_campid'"; 
	
	

	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	$DBsToPrint = mysql_num_rows($rQuery);
	if ($DBsToPrint > 0) 
		{	

		$sQuery = "SELECT * FROM vicidial_list A LEFT JOIN custom_$row_campid[0] B ON A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status INNER JOIN vicidial_lists D ON A.list_id=D.list_id INNER JOIN (SELECT campaign_id, campaign_name FROM vicidial_campaigns GROUP BY campaign_name) E ON D.campaign_id=E.campaign_id WHERE A.list_id = '$SelectedDBs[$i]' AND A.status IN('MARC','NOVOCL') AND A.modify_date BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time                       ";
		$rQuery = mysql_query($sQuery, $link) or die(mysql_error()); 

			while ($row = mysql_fetch_assoc($rQuery)) {
				
			$cod = "";
			
			if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
			
			$campid=$row['extra1'];


			$no=$row['extra2'];
			
			if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
			if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
			

			
			fputcsv($output, array(
			
				$row['title'],
				$campid,
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
				$no,
				"",
				"",
				"",
				$cod,
				"",
				"",
				$TEMPVAR,
				"",
				$row['marchora'],
				$row['marcdata'],  
				$row['tipoconsulta'],
				"",
				$row['obs'],
				"",
				"",
				"",
				$row['user'],
				$row['status_name'],
				$row['campaign_name'],
				$row['last_local_call_time']
			
			),";"); }

			}
		}
}
#######################################################################################################################
### FIM REPORT GERAL POR BASE DE DADOS
#######################################################################################################################

#######################################################################################################################
# TOTAIS TODAS AS CAMPANHAS
#######################################################################################################################
if (isset($_POST['totais_camp3'])) {
if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));} 
#####################################################
##################################################### Nome da Base de Dados
##################################################### Contagem dos Registos da DB
/*$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	status='CBHOLD';";
$query = mysql_query($query, $link);	
$total_cbhold = mysql_fetch_assoc($query);
$total_cbhold = $total_cbhold['count(list_id)'];*/
#####################################################	
if ($_POST['flag'] == "todos") 
{
$query = "SELECT ifnull(a.conta,0) as 'count(a.status)', c.status from (SELECT count(status) conta,status FROM	vicidial_list  
			WHERE	 	
			last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final' 
GROUP BY status) as a			
right JOIN (SELECT distinct  status FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status FROM `vicidial_statuses` WHERE status NOT IN('NEW', 'INCALL', 'CALLBK', 'QUEUE')) c on a. status =c.status order by status";  
} 
else 
{

		$query = "	SELECT	count(a.status),
					a.status
					
			FROM	vicidial_list as a  INNER JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses` WHERE status NOT IN('NEW', 'INCALL', 'CALLBK', 'QUEUE')) as b ON a.status = b.status 
			WHERE		a.last_local_call_time >= '$data_inicial' 
			AND		a.last_local_call_time <= '$data_final'
			GROUP BY a.status
			ORDER BY b.status_name";			
				
}




$query = mysql_query($query, $link) or die (mysql_error());

for ($i=0;$i<mysql_num_rows($query);$i++)
{$row=mysql_fetch_row($query); $total_leads += $row[0]; }

$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Report:","Totais das Campanhas em Sistema"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total de Registos Encontrados:",$total_leads),";");	
#fputcsv($output, array(" ","Callbacks Agendados:",$total_cbhold),";");	
fputcsv($output, array(" "),";");	
mysql_data_seek($query,0);	

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
	fputcsv($output, array(" ", $feedback_full, $row['count(a.status)'] ),";");
		
}
		
}	
#######################################################################################################################
# TOTAIS TODAS AS CAMPANHAS
#######################################################################################################################

#######################################################################################################################
# TOTAL DE FEEDBACKS POR CAMPANHA
#######################################################################################################################
if (isset($_POST['totais_camp2'])) {

    // POSTS
$data_inicial = $_POST['data_inicial']; 
$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final'])); 
$camp_options = $_POST['camp_options'];

    // LISTS IN
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

$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	$listas_SQL
			AND 	entry_date >= '$data_inicial' 
			AND		entry_date <= '$data_final'";
$query = mysql_query($query, $link);	
$total_leads_data = mysql_fetch_assoc($query);
$total_leads_data = $total_leads_data['count(list_id)'];

	
$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Report:","Totais por Campanha"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Campanha:",$camp_name),";");
fputcsv($output, array(" ","Total de Registos:",$total_leads),";");	
fputcsv($output, array(" ","Total Registos (por Data):",$total_leads_data),";");	
fputcsv($output, array(" "),";");	
if ($_POST['flag'] == "todos") 
{
$query = "SELECT ifnull(a.conta,0) as 'count(a.status)', c.status from (SELECT count(status) conta,status FROM	vicidial_list  
			WHERE	 	
			(last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final' 
			OR last_local_call_time = '2008-01-01')
			AND list_id IN(SELECT list_id FROM vicidial_lists WHERE campaign_id='$camp_options' AND list_id<>998)
			
GROUP BY status) as a			
right JOIN (SELECT distinct  status FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status FROM `vicidial_statuses` WHERE status NOT IN('INCALL', 'CALLBK', 'QUEUE')) c on a. status =c.status order by status";  
} 
else 
{
		$query = "	SELECT	count(a.status),
					a.status
					
			FROM	vicidial_list as a  INNER JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses` WHERE status NOT IN('INCALL', 'CALLBK', 'QUEUE')) as b ON a.status = b.status 
			WHERE 	list_id IN(SELECT list_id FROM vicidial_lists WHERE campaign_id='$camp_options' AND list_id<>998)
			AND		(a.last_local_call_time >= '$data_inicial' 
			AND		a.last_local_call_time <= '$data_final'
			OR		a.last_local_call_time = '2008-01-01')
			GROUP BY a.status
			ORDER BY b.status_name";			
				
}
		

/*$query = "	SELECT	count(status),
					status
			FROM	vicidial_list  
			
			WHERE 	list_id IN(SELECT list_id FROM vicidial_lists WHERE campaign_id='$camp_options' AND list_id<>998)
			
			AND		(last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			OR		last_local_call_time = '2008-01-01')
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)
			GROUP BY status
			;"; */
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
	
	
	if($feedback_full=='Contactos Ainda Nao Marcados (S)')
	{
$query_new = "	SELECT 	count(list_id) 
	FROM 	vicidial_list
	WHERE 	$listas_SQL
	AND 	status='NEW'
	AND 	entry_date >= '$data_inicial' 
	AND		entry_date <= '$data_final'";
$query_new = mysql_query($query_new, $link);	
$tempvar = mysql_fetch_assoc($query_new);
$tempvar = $tempvar['count(list_id)'];
fputcsv($output, array(" ", $feedback_full, $tempvar ),";");} else { fputcsv($output, array(" ", $feedback_full, $row['count(a.status)'] ),";"); }
	
	
	
	
		
}
		
}	

#######################################################################################################################
# TOTAL DE FEEDBACKS POR BASE DE DADOS
#######################################################################################################################
if (isset($_POST['totais_db2'])) {

if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));} 
if (isset($_POST['db_options'])) {$db_options = $_POST['db_options'];}

$listas_count = count($db_options);
for ($p=0;$p<$listas_count;$p++)
{
	if ($listas_count == 1) 
		{
		$listas_IN = "'".$db_options[$p]."'"; 
		}
	elseif ($listas_count-1 == $p)
		{
		$listas_IN .= "'".$db_options[$p]."'";	
		}	
	else
		{
		$listas_IN .= "'".$db_options[$p]."',"; 
		}
	//$listas_SQL = "list_id IN($listas_IN)";	
}

##################################################### Nome da Base de Dados
$query = "	SELECT 	list_name 
			FROM 	vicidial_lists
			WHERE 	list_id IN ($listas_IN)";
$query = mysql_query($query, $link) or die(mysql_error());
for($i=0;$i<mysql_num_rows($query);$i++)
{
$row = mysql_fetch_assoc($query);
$list_name[$i] = $row['list_name'];
}
##################################################### Contagem dos Registos da DB
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	list_id='$db_options'";
$query = mysql_query($query, $link);	
$total_leads = mysql_fetch_assoc($query);
$total_leads = $total_leads['count(list_id)'];
#####################################################
/*$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	list_id='$db_options'
			AND 	status='CBHOLD';";
$query = mysql_query($query, $link);	
$total_cbhold = mysql_fetch_assoc($query);
$total_cbhold = $total_cbhold['count(list_id)'];*/
#####################################################	
$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Report:","Totais por Base de Dados"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");
fputcsv($output, array(" "),";");

foreach ($db_options as $db_options)
{
	
}

fputcsv($output, array(" ","Base de Dados:",$list_name),";");
fputcsv($output, array(" ","Total de Registos:",$total_leads),";");	
#fputcsv($output, array(" ","Callbacks Agendados:",$total_cbhold),";");	
fputcsv($output, array(" "),";");	



if ($_POST['flag'] == "todos") 
{
$query = "SELECT ifnull(a.conta,0) as 'count(a.status)', c.status from (SELECT count(status) conta,status FROM	vicidial_list  
			WHERE	 	

list_id ='$db_options'
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final' 
GROUP BY status) as a			
right JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses` WHERE status NOT IN('QUEUE', 'NEW', 'INCALL', 'DROP', 'NA', 'CALLBK')) c on a. status =c.status order by status_name";  
} 
else 
{

$query = "	SELECT	count(a.status),
					a.status
					
			FROM	vicidial_list as a  INNER JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses`) as b ON a.status = b.status 
			WHERE	list_id ='$db_options'
			AND		a.last_local_call_time >= '$data_inicial' 
			AND		a.last_local_call_time <= '$data_final'
			GROUP BY a.status
			ORDER BY b.status_name";			
			
			
			
			
}
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
	fputcsv($output, array(" ", $feedback_full, $row['count(a.status)'] ),";");
		
}
		
}	


#######################################################################################################################
# TOTAL POR CAMPANHA E OPERADOR
#######################################################################################################################
if (isset($_POST['totais_camp'])) {
    
$data_inicial = $_POST['data_inicial']; 
$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));
$camp_options = $_POST['camp_options'];

$campanhas_count = count($camp_options);

for ($i=0;$i<$campanhas_count;$i++)
	{
	if ($campanhas_count == 1) 
		{
		$camps_IN = "'".$camp_options[$i]."'"; 
		}
	elseif ($campanhas_count-1 == $i)
		{
		$camps_IN .= "'".$camp_options[$i]."'";	
		}	
	else
		{
		$camps_IN .= "'".$camp_options[$i]."',";
		}
	}

	
$query = "	SELECT 	user 
			FROM 	vicidial_users
			WHERE 	user_level = 1
            AND     active = 'Y'";
$query = mysql_query($query, $link);
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	$user[$i]= $row['user'];
}

$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Report:","Feedbacks por Campanhas"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");
fputcsv($output, array(" "),";");



foreach ($camp_options as $key1=>$value1)
{
$query = "	SELECT 	list_id
	FROM 	vicidial_lists
	WHERE 	campaign_id ='$value1'
	AND 	list_id<>'998'";
		
$query = mysql_query($query, $link) or die(mysql_error());			
$listas_count = mysql_num_rows($query);
$listas_IN='';
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
	 
}
	
	
	
$query = "	SELECT 	campaign_name
			FROM 	vicidial_campaigns
			WHERE 	campaign_id = '$value1'";
$query = mysql_query($query, $link) or die(mysql_error());	
$camp_name= mysql_fetch_assoc($query);	


fputcsv($output, array(" "),";");	
fputcsv($output, array(" ", "Campanha:", $camp_name['campaign_name']),";");


$counter=0;
$counter_marc=0;
foreach ($user as $key=>$value)
{

fputcsv($output, array(" "),";");	
fputcsv($output, array(" ","$value", "Feedback", "Contagem"),";");		



if ($_POST['flag'] == "todos") 
{
$query = "SELECT ifnull(a.conta,0) as 'count(a.status)', c.status,  '$value' from (SELECT count(status) conta,status FROM	vicidial_list  
			WHERE	 	
user='$value' AND
list_id IN($listas_IN) 
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final' 
GROUP BY user,status) as a			
right JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses` WHERE status NOT IN('QUEUE', 'NEW', 'INCALL', 'DROP', 'NA', 'CALLBK')) c on a. status =c.status order by status_name
"; 

//echo $query; 
} 
else 
{
/*$query = "	SELECT	count(status),
					status,
					user
			FROM	vicidial_list  
			WHERE	user='$value'
			AND 	list_id IN(SELECT list_id FROM vicidial_lists WHERE campaign_id='$camp_options' AND list_id<>998)
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)
			GROUP BY user,status
			;";*/
			
						$query = "	SELECT	count(a.status),
					a.status,
					a.user
			FROM	vicidial_list as a  INNER JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses`) as b ON a.status = b.status 
			WHERE	a.user='$value'
			AND 	list_id IN($listas_IN)
			AND		a.last_local_call_time >= '$data_inicial' 
			AND		a.last_local_call_time <= '$data_final'
			GROUP BY a.status
			ORDER BY b.status_name";
}



#############################################

//fputcsv($output, array($query),";");
$master_count=0;
$counter_marc=0;
$counter_total=0;
$query = mysql_query($query, $link) or die (mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);

	$master_count += $row['count(a.status)'];
	
	if($row['status'] == "MARC" || $row['status']== "NOVOCL") {$counter_marc +=$row['count(a.status)'];}

	if( ($row['status']!="VM") && ($row['status']!="I") && ($row['status']!="B") && ($row['status']!="P") && ($row['status']!="PDROP") && ($row['status']!="DNC") && ($row['status']!="I") && ($row['status']!="NAT") && ($row['status']!="FAX") && ($row['status']!="ERRO") && ($row['status']!="ERI") && ($row['status']!="PU") && ($row['status']!="DC"))
	{$counter_total+=$row['count(a.status)'];}
	$queryf = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name']; }
	
	$queryf = mysql_query("SELECT status_name FROM vicidial_statuses WHERE status='".$row['status']."'", $link);
	if (mysql_num_rows($queryf) > 0) {
	$queryf = mysql_fetch_assoc($queryf);
	$feedback_full = $queryf['status_name'];}
	fputcsv($output, array(" ", " ", $feedback_full, $row['count(a.status)'] ),";");
		
}

fputcsv($output, array(" ","", "Total de Feedbacks:", $master_count),";");	

$r_user_name[$counter] = $value;
$r_marc[$counter] = $counter_marc;
$r_total_calls[$counter]= $counter_total;
$counter++;


}

fputcsv($output, array(" "),";");	
fputcsv($output, array(" ", $camp_name['campaign_name'], "Total Marcacoes", "Total Contactos Uteis"),";");
fputcsv($output, array(" "),";");

for($h=0;$h<count($r_user_name);$h++)
{
	fputcsv($output, array(" ", $r_user_name[$h], $r_marc[$h], $r_total_calls[$h]),";");
}

fputcsv($output, array(" ", " "),";");
fputcsv($output, array(" ", " "),";");
fputcsv($output, array(" ", " "),";");
		
}	
}



#######################################################################################################################
# TOTAL POR BASE DE DADOS E OPERADOR
#######################################################################################################################
if (isset($_POST['totais_db'])) {
if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
if (isset($_POST['data_final'])) {$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));} 
if (isset($_POST['db_options'])) {$db_options = $_POST['db_options'];}
$db_count = count($db_options);

for ($i=0;$i<$db_count;$i++)
	{
	if ($db_count == 1) 
		{
		$db_IN = "'".$db_options[$i]."'"; 
		}
	elseif ($db_count-1 == $i)
		{
		$db_IN .= "'".$db_options[$i]."'";	
		}	
	else
		{
		$db_IN .= "'".$db_options[$i]."',";
		}
	}
##################################################### Nome da Base de Dados
$query = "	SELECT 	list_name 
			FROM 	vicidial_lists
			WHERE 	list_id IN($db_IN)";
$query = mysql_query($query, $link);
for ($p=0;$p<mysql_num_rows($query);$p++)
{
	$db_name = mysql_fetch_assoc($query);
	$db_names .= "| ".$db_name['list_name']; 
}
##################################################### Contagem dos Registos da DB
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	list_id='$db_options'";
$query = mysql_query($query, $link);	
$total_leads = mysql_fetch_assoc($query);
$total_leads = $total_leads['count(list_id)'];
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
fputcsv($output, array(" ", "Report:","Feedbacks por Base de Dados"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");
fputcsv($output, array(" "),";");

#fputcsv($output, array(" ","Total de Registos:",$total_leads),";");	
#fputcsv($output, array(" ","Callbacks Agendados:",$total_cbhold),";");	
##################################################### Construção das linhas 
foreach ($db_options as $key1=>$value1)
{
$query = "	SELECT 	list_name 
			FROM 	vicidial_lists
			WHERE 	list_id IN($value1)";
$query = mysql_query($query, $link);
$db_name = mysql_fetch_assoc($query);
fputcsv($output, array(" "),";");
fputcsv($output, array(" "),";");
fputcsv($output, array("","##############"),";");
fputcsv($output, array(" ", "Bases de Dados:", $db_name['list_name']),";");
fputcsv($output, array("","##############"),";");

foreach ($user as $key=>$value)
{
$query = "	SELECT 	count(list_id) 
			FROM 	vicidial_list
			WHERE 	user='$value'
			AND 	list_id IN($value1)
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final'
			AND 	status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)";
$query = mysql_query($query, $link);	
$total_calls = mysql_fetch_assoc($query);
$total_calls = $total_calls['count(list_id)'];	 

fputcsv($output, array(" "),";");	
fputcsv($output, array(" ","$value", "Total Chamadas:", $total_calls),";");		



if ($_POST['flag'] == "todos") 
{
$query = "SELECT ifnull(a.conta,0) as 'count(a.status)', c.status,  '$value' from (SELECT count(status) conta,status FROM	vicidial_list  
			WHERE	 	
user='$value' AND
list_id IN('$value1')
			AND		last_local_call_time >= '$data_inicial' 
			AND		last_local_call_time <= '$data_final' 
GROUP BY user,status) as a			
right JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses` WHERE status NOT IN('QUEUE', 'NEW', 'INCALL', 'DROP', 'NA', 'CALLBK')) c on a. status =c.status order by status_name
";  
} 
else 
{
$query = "	SELECT	count(a.status),
					a.status,
					a.user 
			FROM	vicidial_list as a  INNER JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses`) as b ON a.status = b.status 
			WHERE	a.user='$value' AND
			list_id IN('$value1')
			AND		a.last_local_call_time >= '$data_inicial' 
			AND		a.last_local_call_time <= '$data_final'
			GROUP BY a.status
			ORDER BY b.status_name";
}
 

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
	fputcsv($output, array(" ", " ", $feedback_full, $row['count(a.status)'] ),";");
		
}
}		
}
}


#######################################################################################################################
### REPORT RESUMO GERAL POR CAMPANHA - OLD
#######################################################################################################################
if (isset($_POST['resumo_geral_old'])) {
$output = fopen('php://output', 'w');
$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
	
if ($data_inicial==$data_final){$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));}
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Report:","Resumo Geral"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");

foreach ($_POST['camp_options'] as $key=>$campanha)
	{
	### Contrucao do list_id IN para se ler dados da vicidial_list 
	$query = "select list_id from vicidial_lists where campaign_id='$campanha'";
	$query = mysql_query($query, $link);	
	$dbs_count = mysql_num_rows($query);
	$dbs_IN="";	
	for ($i=0;$i<$dbs_count;$i++)
	{
	$row=mysql_fetch_row($query);
	if ($dbs_count == 1) 
		{
		$dbs_IN = "'".$row[0]."'"; 
		}
	elseif ($dbs_count-1 == $i)
		{
		$dbs_IN .= "'".$row[0]."'";	
		}	
	else
		{
		$dbs_IN .= "'".$row[0]."',";
		}
	}
	
	##############################################################
	$query_n = "SELECT campaign_name from vicidial_campaigns where campaign_id='$campanha'";
	$query_n = mysql_query($query_n);
	$row_n = mysql_fetch_row($query_n);
	##############################################################
	### Contagem do Total de Registos
	$query_b = "	SELECT 	count(*) 
	FROM 	vicidial_list
	WHERE 	list_id IN($dbs_IN) and last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00'";
	#echo $query_b;
	$query_b = mysql_query($query_b, $link);	
	$row = mysql_fetch_row($query_b);
	$total_leads = $row[0];
	##############################################################
	### Contagem dos Agendamentos/Callbacks
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('CALLBK', 'CBHOLD') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$agendamentos2 = $row[0];	
	##############################################################
	### Contagem dos Agendamentos/Callbacks
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('MARC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$marcacoes2 = $row[0];	
	##############################################################
	### Contagem dos Novos Clientes
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('NOVOCL') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$novos_clientes2 = $row[0];	
	##############################################################
	### Contagem do Feedback Amostra
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('A') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$amostra2 = $row[0];	
	##############################################################
	### Contagem do Feedback NI
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('NI') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$ni2 = $row[0];	
	##############################################################
	### Contagem do Feedback Concorrencia
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('C') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$conc2 = $row[0];					
	##############################################################
	### Contagem do Feedback Realizou Exame
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('ER') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$exame_recente2 = $row[0];			
	##############################################################
	### Contagem do Feedback Otorrino
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('O') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$otorrino2 = $row[0];	
	##############################################################
	### Contagem do Feedback Nao contactar
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('DNC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$nao_contactar2 = $row[0];		
	##############################################################
	### Contagem do Feedback Falecido
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('F') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$falecido2 = $row[0];		
	##############################################################
	### Contagem do Feedback Futuras Acçoes
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('FA') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$futuras2 = $row[0];		
	##############################################################
	### Contagem do Feedback idade < 45 anos
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('IDD') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$idade2 = $row[0];		
	##############################################################
	### Contagem do ja foi ao consultorio
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('JFC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$jconsultorio2 = $row[0];		
	##############################################################
	### Contagem do Feedback outros
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('OUTROS') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$outros2 = $row[0];		
	##############################################################
	### Contagem do Feedback reclamacao
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('R') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$reclamacao2 = $row[0];	
	##############################################################
	### Contagem do Feedback Ja tem aparelho AM
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('JA') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$jatem2 = $row[0];		
	##############################################################
	### Contagem do Feedback estabelecimento comercial
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('EC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$comercial2 = $row[0];			
	##############################################################
	### Contagem do Feedback disponivel apos 20:00
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('D') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$disponivel2 = $row[0];	
	##############################################################
	### Contagem do Feedback contactado recente
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('CR') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$contactado2 = $row[0];
	##############################################################
	### Contagem do Feedback nao reside
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('RM') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$naoreside = $row[0];	
	##############################################################
	### Contagem do Feedback nao reside
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('TINV') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$tlfinv = $row[0];
	##############################################################
	### Contagem de numeros invalidos
	$query = "SELECT DISTINCT(vicidial_log.lead_id) from vicidial_log INNER JOIN vicidial_carrier_log ON vicidial_log.lead_id = vicidial_carrier_log.lead_id where vicidial_log.call_date >= '$data_inicial 00:00:00' AND vicidial_log.call_date <= '$data_final 24:00:00' AND hangup_cause = '1' and campaign_id = '$campanha'";
	$query = mysql_query($query, $link) or die(mysql_error());
	$inv = mysql_num_rows($query);
	
	##############################################################
	### Contagem dos dificuldades
	$query = "	SELECT	count(*),status from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('FAX','I','B','NA','NAT','VM','RD','ERI','TINV') group by status ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$b2=0;
	$eri2=0;
	$fax2=0;
	$i2=0;
	$na2=0;
	$nat2=0;
	$rd2=0;
	$vm2=0;
	$tinv=0;
	
	for($i=0;$i<mysql_num_rows($query);$i++)
	{
	$row = mysql_fetch_row($query);
	switch($row[1])
	{
	case "B": $b2=$row[0]; break;
	case "ERI": $eri2=$row[0]; break;
	case "FAX": $fax2=$row[0]; break;
	case "I": $i2=$row[0]; break;
	case "NA": $na2=$row[0]; break;
	case "NAT": $nat2=$row[0];break;
	case "RD": $rd2=$row[0];break;
	case "VM": $vm2=$row[0];break;
	case "TINV": $tinv=$row[0];break;
	
	}
	}
	
	$contactos_negativos = $contactado2+$disponivel2+$comercial2+$jatem2+$reclamacao2+$outros2+$jconsultorio2+
	$idade2+$futuras2+$falecido2+$nao_contactar2+$otorrino2+$exame_recente2+$conc2+$ni2+$amostra2+$naoreside;
	
	$contactos_efectuados = $agendamentos2+$marcacoes2+$novo_cliente2+$contactos_negativos+$fax2+$i2+$rd2+$eri2;
	
	$contactos_uteis = $marcacoes2+$novocliente2+$agendamentos2+$contactos_negativos-($exame_recente2+$falecido2+$idade2+$jconsultorio2+$jatem2);
	
	
	$dificuldades = $fax2+$i2+$b2+$na2+$nat2+$vm2+$rd2+$eri2+$inv+$tinv;

$marc_total = $marcacoes2 + $novos_clientes2;

$m_TR = number_format($marc_total/$total_leads*100, 2)." %";
$m_CE = number_format($marc_total/$contactos_efectuados*100, 2)." %";
$m_CU = number_format($marc_total/$contactos_uteis*100, 2)." %";

$na_total=$na2+$nat2;
$inv_total = $inv+$i2;	

fputcsv($output, array(" "),";");
fputcsv($output, array(" "),";");	
fputcsv($output, array(" ","Campanha: ", $row_n[0]),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Registos: ", $total_leads),";");
fputcsv($output, array(" ","Contactos Efectuados: ", $contactos_efectuados),";");
fputcsv($output, array(" ","Contactos Uteis: ", $contactos_uteis),";");
fputcsv($output, array(" ","Agendamentos: ", $agendamentos2),";");
fputcsv($output, array(" ","Marcacoes: ", $marcacoes2),";");
fputcsv($output, array(" ","Novas Leads: ", $novos_clientes2),";");
fputcsv($output, array(" ","Total Marcacoes: ", $marc_total),";");
fputcsv($output, array(" ","Contactos Negativos: ", $contactos_negativos),";");
fputcsv($output, array(" ","Dificuldades: ", $dificuldades),";");
fputcsv($output, array(" ","Marcacoes/Total Registos: ", $m_TR),";");
fputcsv($output, array(" ","Marcacoes/Contactos Efectuados: ", $m_CE),";");
fputcsv($output, array(" ","Marcacoes/Contactos Uteis: ", $m_CU),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Contactos Negativos: ", $contactos_negativos),";");
fputcsv($output, array(" ","Pedido de Envio de Amostra: ", $amostra2),";");
fputcsv($output, array(" ","Nao Interessado ", $ni2),";");
fputcsv($output, array(" ","Concorrencia ", $conc2),";");
fputcsv($output, array(" ","Realizou Exame < 5 Meses: ", $exame_recente2),";");
fputcsv($output, array(" ","Otorrino: ", $otorrino2),";");
fputcsv($output, array(" ","Nao Contactar: ", $nao_contactar2),";");
fputcsv($output, array(" ","Falecido: ", $falecido2),";");
fputcsv($output, array(" ","Futuras Accoes: ", $futuras2),";");
fputcsv($output, array(" ","Idade < 45 Anos: ", $idade2),";");
fputcsv($output, array(" ","Ja foi ao consultorio: ", $jconsultorio2),";");
fputcsv($output, array(" ","Outros: ", $outros2),";");
fputcsv($output, array(" ","Reclamacao: ", $reclamacao2),";");
fputcsv($output, array(" ","Tem aparelho AM: ", $jatem2),";");
fputcsv($output, array(" ","Estabelecimento Comercial: ", $comercial2),";");
fputcsv($output, array(" ","Disponivel apos as 20:00: ", $disponivel2),";");
fputcsv($output, array(" ","Contactado Recentemente: ", $contactado2),";");
fputcsv($output, array(" ","Nao Reside/Mudou: ", $naoreside),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Dificuldades: ", $dificuldades),";");
fputcsv($output, array(" ","Fax: ", $fax2),";");
fputcsv($output, array(" ","Numeros Invalidos: ", $inv_total),";");
fputcsv($output, array(" ","Sinal Ocupado: ", $b2),";");
fputcsv($output, array(" ","Nao Atende: ", $na_total),";");
fputcsv($output, array(" ","Voice Mail: ", $vm2),";");
fputcsv($output, array(" ","Referencia Duplicada: ", $rd2),";");
fputcsv($output, array(" ","Erros de Sistema: ", $eri2),";");
fputcsv($output, array(" ","Telefone Invalido: ", $tinv),";");
}
}

#######################################################################################################################
### REPORT RESUMO GERAL POR CAMPANHA - NOVO
#######################################################################################################################
if (isset($_POST['resumo_geral'])) {
$output = fopen('php://output', 'w');
$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
	
if ($data_inicial==$data_final){$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));}
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Report:","Resumo Geral"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");

foreach ($_POST['camp_options'] as $key=>$campanha)
	{
	### Contrucao do list_id IN para se ler dados da vicidial_list 
	$query = "select list_id from vicidial_lists where campaign_id='$campanha'";
	$query = mysql_query($query, $link);	
	$dbs_count = mysql_num_rows($query);
	$dbs_IN="";	
	for ($i=0;$i<$dbs_count;$i++)
	{
	$row=mysql_fetch_row($query);
	if ($dbs_count == 1) 
		{
		$dbs_IN = "'".$row[0]."'"; 
		}
	elseif ($dbs_count-1 == $i)
		{
		$dbs_IN .= "'".$row[0]."'";	
		}	
	else
		{
		$dbs_IN .= "'".$row[0]."',";
		}
	}
	
        
$query= "       select 
    d.status_name, c.soma
from
    (select 
        status, count(status) as soma
    from
        (select 
        lead_id, status
    from
        (select 
        lead_id, status
    from
        vicidial_log
    where
        campaign_id IN ('C00033')
    order by call_date DESC) a
    group by lead_id) b
    group by status) c
        inner join
    (select 
        (status), status_name
    from
        vicidial_campaign_statuses UNION ALL select 
        status, status_name
    from
        vicidial_statuses) d ON c.status = d.status
group by status_name";




        
	##############################################################
	$query_n = "SELECT campaign_name from vicidial_campaigns where campaign_id='$campanha'";
	$query_n = mysql_query($query_n);
	$row_n = mysql_fetch_row($query_n);
	##############################################################
	### Contagem do Total de Registos
	$query_b = "	SELECT 	count(*) 
	FROM 	vicidial_list
	WHERE 	list_id IN($dbs_IN) and last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00'";
	#echo $query_b;
	$query_b = mysql_query($query_b, $link);	
	$row = mysql_fetch_row($query_b);
	$total_leads = $row[0];
	##############################################################
	### Contagem dos Agendamentos/Callbacks
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('CALLBK', 'CBHOLD') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$agendamentos2 = $row[0];	
	##############################################################
	### Contagem dos Agendamentos/Callbacks
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('MARC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$marcacoes2 = $row[0];	
	##############################################################
	### Contagem dos Novos Clientes
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('NOVOCL') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$novos_clientes2 = $row[0];	
	##############################################################
	### Contagem do Feedback Amostra
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('A') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$amostra2 = $row[0];	
	##############################################################
	### Contagem do Feedback NI
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('NI') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$ni2 = $row[0];	
	##############################################################
	### Contagem do Feedback Concorrencia
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('C') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$conc2 = $row[0];					
	##############################################################
	### Contagem do Feedback Realizou Exame
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('ER') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$exame_recente2 = $row[0];			
	##############################################################
	### Contagem do Feedback Otorrino
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('O') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$otorrino2 = $row[0];	
	##############################################################
	### Contagem do Feedback Nao contactar
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('DNC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$nao_contactar2 = $row[0];		
	##############################################################
	### Contagem do Feedback Falecido
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('F') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$falecido2 = $row[0];		
	##############################################################
	### Contagem do Feedback Futuras Acçoes
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('FA') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$futuras2 = $row[0];		
	##############################################################
	### Contagem do Feedback idade < 45 anos
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('IDD') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$idade2 = $row[0];		
	##############################################################
	### Contagem do ja foi ao consultorio
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('JFC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$jconsultorio2 = $row[0];		
	##############################################################
	### Contagem do Feedback outros
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('OUTROS') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$outros2 = $row[0];		
	##############################################################
	### Contagem do Feedback reclamacao
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('R') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$reclamacao2 = $row[0];	
	##############################################################
	### Contagem do Feedback Ja tem aparelho AM
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('JA') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$jatem2 = $row[0];		
	##############################################################
	### Contagem do Feedback estabelecimento comercial
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('EC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$comercial2 = $row[0];			
	##############################################################
	### Contagem do Feedback disponivel apos 20:00
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('D') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$disponivel2 = $row[0];	
	##############################################################
	### Contagem do Feedback contactado recente
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('CR') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$contactado2 = $row[0];
	##############################################################
	### Contagem do Feedback nao reside
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('RM') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$naoreside = $row[0];	
	##############################################################
	### Contagem do Feedback nao reside
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('TINV') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$tlfinv = $row[0];
	##############################################################
	### Contagem de numeros invalidos
	$query = "SELECT DISTINCT(vicidial_log.lead_id) from vicidial_log INNER JOIN vicidial_carrier_log ON vicidial_log.lead_id = vicidial_carrier_log.lead_id where vicidial_log.call_date >= '$data_inicial 00:00:00' AND vicidial_log.call_date <= '$data_final 24:00:00' AND hangup_cause = '1' and campaign_id = '$campanha'";
	$query = mysql_query($query, $link) or die(mysql_error());
	$inv = mysql_num_rows($query);
	
	##############################################################
	### Contagem dos dificuldades
	$query = "	SELECT	count(*),status from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($dbs_IN) AND status IN ('FAX','I','B','NA','NAT','VM','RD','ERI','TINV') group by status ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$b2=0;
	$eri2=0;
	$fax2=0;
	$i2=0;
	$na2=0;
	$nat2=0;
	$rd2=0;
	$vm2=0;
	$tinv=0;
	
	for($i=0;$i<mysql_num_rows($query);$i++)
	{
	$row = mysql_fetch_row($query);
	switch($row[1])
	{
	case "B": $b2=$row[0]; break;
	case "ERI": $eri2=$row[0]; break;
	case "FAX": $fax2=$row[0]; break;
	case "I": $i2=$row[0]; break;
	case "NA": $na2=$row[0]; break;
	case "NAT": $nat2=$row[0];break;
	case "RD": $rd2=$row[0];break;
	case "VM": $vm2=$row[0];break;
	case "TINV": $tinv=$row[0];break;
	
	}
	}
	
	$contactos_negativos = $contactado2+$disponivel2+$comercial2+$jatem2+$reclamacao2+$outros2+$jconsultorio2+
	$idade2+$futuras2+$falecido2+$nao_contactar2+$otorrino2+$exame_recente2+$conc2+$ni2+$amostra2+$naoreside;
	
	$contactos_efectuados = $agendamentos2+$marcacoes2+$novo_cliente2+$contactos_negativos+$fax2+$i2+$rd2+$eri2;
	
	$contactos_uteis = $marcacoes2+$novocliente2+$agendamentos2+$contactos_negativos-($exame_recente2+$falecido2+$idade2+$jconsultorio2+$jatem2);
	
	
	$dificuldades = $fax2+$i2+$b2+$na2+$nat2+$vm2+$rd2+$eri2+$inv+$tinv;

$marc_total = $marcacoes2 + $novos_clientes2;

$m_TR = number_format($marc_total/$total_leads*100, 2)." %";
$m_CE = number_format($marc_total/$contactos_efectuados*100, 2)." %";
$m_CU = number_format($marc_total/$contactos_uteis*100, 2)." %";

$na_total=$na2+$nat2;
$inv_total = $inv+$i2;	

fputcsv($output, array(" "),";");
fputcsv($output, array(" "),";");	
fputcsv($output, array(" ","Campanha: ", $row_n[0]),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Registos: ", $total_leads),";");
fputcsv($output, array(" ","Contactos Efectuados: ", $contactos_efectuados),";");
fputcsv($output, array(" ","Contactos Uteis: ", $contactos_uteis),";");
fputcsv($output, array(" ","Agendamentos: ", $agendamentos2),";");
fputcsv($output, array(" ","Marcacoes: ", $marcacoes2),";");
fputcsv($output, array(" ","Novas Leads: ", $novos_clientes2),";");
fputcsv($output, array(" ","Total Marcacoes: ", $marc_total),";");
fputcsv($output, array(" ","Contactos Negativos: ", $contactos_negativos),";");
fputcsv($output, array(" ","Dificuldades: ", $dificuldades),";");
fputcsv($output, array(" ","Marcacoes/Total Registos: ", $m_TR),";");
fputcsv($output, array(" ","Marcacoes/Contactos Efectuados: ", $m_CE),";");
fputcsv($output, array(" ","Marcacoes/Contactos Uteis: ", $m_CU),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Contactos Negativos: ", $contactos_negativos),";");
fputcsv($output, array(" ","Pedido de Envio de Amostra: ", $amostra2),";");
fputcsv($output, array(" ","Nao Interessado ", $ni2),";");
fputcsv($output, array(" ","Concorrencia ", $conc2),";");
fputcsv($output, array(" ","Realizou Exame < 5 Meses: ", $exame_recente2),";");
fputcsv($output, array(" ","Otorrino: ", $otorrino2),";");
fputcsv($output, array(" ","Nao Contactar: ", $nao_contactar2),";");
fputcsv($output, array(" ","Falecido: ", $falecido2),";");
fputcsv($output, array(" ","Futuras Accoes: ", $futuras2),";");
fputcsv($output, array(" ","Idade < 45 Anos: ", $idade2),";");
fputcsv($output, array(" ","Ja foi ao consultorio: ", $jconsultorio2),";");
fputcsv($output, array(" ","Outros: ", $outros2),";");
fputcsv($output, array(" ","Reclamacao: ", $reclamacao2),";");
fputcsv($output, array(" ","Tem aparelho AM: ", $jatem2),";");
fputcsv($output, array(" ","Estabelecimento Comercial: ", $comercial2),";");
fputcsv($output, array(" ","Disponivel apos as 20:00: ", $disponivel2),";");
fputcsv($output, array(" ","Contactado Recentemente: ", $contactado2),";");
fputcsv($output, array(" ","Nao Reside/Mudou: ", $naoreside),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Dificuldades: ", $dificuldades),";");
fputcsv($output, array(" ","Fax: ", $fax2),";");
fputcsv($output, array(" ","Numeros Invalidos: ", $inv_total),";");
fputcsv($output, array(" ","Sinal Ocupado: ", $b2),";");
fputcsv($output, array(" ","Nao Atende: ", $na_total),";");
fputcsv($output, array(" ","Voice Mail: ", $vm2),";");
fputcsv($output, array(" ","Referencia Duplicada: ", $rd2),";");
fputcsv($output, array(" ","Erros de Sistema: ", $eri2),";");
fputcsv($output, array(" ","Telefone Invalido: ", $tinv),";");
}
}

#######################################################################################################################
### RESUMO GERAL POR BASE DE DADOS
#######################################################################################################################
if (isset($_POST['resumo_geral_db'])) {	
$output = fopen('php://output', 'w');
$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
if ($data_inicial==$data_final){$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));}

fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Report:","Resumo Geral"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");

	foreach ($_POST['db_options'] as $key=>$db)
	
	{
	
	##############################################################
	$query_n = "SELECT list_name from vicidial_lists where list_id='$db'";
	$query_n = mysql_query($query_n);
	$row_n = mysql_fetch_row($query_n);
 

	##############################################################
	### Contagem do Total de Registos
	$query_b = "	SELECT 	count(*) 
	FROM 	vicidial_list
	WHERE 	list_id IN($db)
	AND status<>'NOVOCL' AND last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00";
	#echo $query_b;
	$query_b = mysql_query($query_b, $link);	
	$row = mysql_fetch_row($query_b);
	$total_leads = $row[0];
	##############################################################
	### Contagem dos Agendamentos/Callbacks
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('CALLBK', 'CBHOLD') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$agendamentos2 = $row[0];	
	##############################################################
	### Contagem dos Agendamentos/Callbacks
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('MARC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$marcacoes2 = $row[0];	
	##############################################################
	### Contagem dos Novos Clientes
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('NOVOCL') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$novos_clientes2 = $row[0];	
	##############################################################
	### Contagem do Feedback Amostra
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('A') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$amostra2 = $row[0];	
	##############################################################
	### Contagem do Feedback NI
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('NI') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$ni2 = $row[0];	
	##############################################################
	### Contagem do Feedback Concorrencia
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('C') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$conc2 = $row[0];					
	##############################################################
	### Contagem do Feedback Realizou Exame
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('ER') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$exame_recente2 = $row[0];			
	##############################################################
	### Contagem do Feedback Otorrino
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('O') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$otorrino2 = $row[0];	
	##############################################################
	### Contagem do Feedback Nao contactar
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('DNC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$nao_contactar2 = $row[0];		
	##############################################################
	### Contagem do Feedback Falecido
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('F') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$falecido2 = $row[0];		
	##############################################################
	### Contagem do Feedback Futuras Acçoes
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('FA') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$futuras2 = $row[0];		
	##############################################################
	### Contagem do Feedback idade < 45 anos
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('IDD') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$idade2 = $row[0];		
	##############################################################
	### Contagem do ja foi ao consultorio
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('JFC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$jconsultorio2 = $row[0];		
	##############################################################
	### Contagem do Feedback outros
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('OUTROS') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$outros2 = $row[0];		
	##############################################################
	### Contagem do Feedback reclamacao
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('R') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$reclamacao2 = $row[0];	
	##############################################################
	### Contagem do Feedback Ja tem aparelho AM
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('JA') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$jatem2 = $row[0];		
	##############################################################
	### Contagem do Feedback estabelecimento comercial
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('EC') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$comercial2 = $row[0];			
	##############################################################
	### Contagem do Feedback disponivel apos 20:00
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('D') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$disponivel2 = $row[0];	
	##############################################################
	### Contagem do Feedback contactado recente
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('CR') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$contactado2 = $row[0];	
	
	##############################################################
	### Contagem do Feedback nao reside
	$query = "	SELECT	count(*) from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('RM') ";
	$query = mysql_query($query, $link) or die(mysql_error());
	$row = mysql_fetch_row($query);
	$naoreside = $row[0];	
	##############################################################
	### Contagem de numeros invalidos
	$query = "SELECT DISTINCT(vicidial_list.lead_id) from vicidial_list INNER JOIN vicidial_carrier_log ON vicidial_list.lead_id = vicidial_carrier_log.lead_id where vicidial_list.last_local_call_time >= '$data_inicial 00:00:00' AND vicidial_list.last_local_call_time <= '$data_final 24:00:00' AND hangup_cause = '1' AND list_id IN($db)";
	$query = mysql_query($query, $link) or die(mysql_error());
	$inv = mysql_num_rows($query);
	
	##############################################################
	### Contagem dos dificuldades
	$query = "	SELECT	count(*),status from vicidial_list where last_local_call_time >= '$data_inicial 00:00:00' AND last_local_call_time <= '$data_final 24:00:00' AND list_id IN($db) AND status IN ('FAX','I','B','NA','NAT','VM','RD','ERI','TINV') group by status ";

	$query = mysql_query($query, $link) or die(mysql_error());
	$b2=0;
	$eri2=0;
	$fax2=0;
	$i2=0;
	$na2=0;
	$nat2=0;
	$rd2=0;
	$vm2=0;
	$tinv=0;
	
	for($i=0;$i<mysql_num_rows($query);$i++)
	{
	$row = mysql_fetch_row($query);
	switch($row[1])
	{
	case "B": $b2=$row[0]; break;
	case "ERI": $eri2=$row[0]; break;
	case "FAX": $fax2=$row[0]; break;
	case "I": $i2=$row[0]; break;
	case "NA": $na2=$row[0]; break;
	case "NAT": $nat2=$row[0];break;
	case "RD": $rd2=$row[0];break;
	case "VM": $vm2=$row[0];break;
	case "TINV": $tinv=$row[0];break;
	
	}
	

	}
	
	$contactos_negativos = $contactado2+$disponivel2+$comercial2+$jatem2+$reclamacao2+$outros2+$jconsultorio2+
	$idade2+$futuras2+$falecido2+$nao_contactar2+$otorrino2+$exame_recente2+$conc2+$ni2+$amostra2+$naoreside;
	
	$contactos_efectuados = $agendamentos2+$marcacoes2+$novos_clientes2+$contactos_negativos+$fax2+$i2+$rd2+$eri2;
	
	$contactos_uteis = $marcacoes2+$novocliente2+$agendamentos2+$contactos_negativos-($exame_recente2+$falecido2+$idade2+$jconsultorio2+$jatem2);
	
	
	$dificuldades = $fax2+$i2+$b2+$na2+$nat2+$vm2+$rd2+$eri2+$inv+$tinv;

$marc_total = $marcacoes2 + $novos_clientes2;

$m_TR = number_format($marc_total/$total_leads*100, 2)." %";
$m_CE = number_format($marc_total/$contactos_efectuados*100, 2)." %";
$m_CU = number_format($marc_total/$contactos_uteis*100, 2)." %";

$na_total=$na2+$nat2;

$inv_total = $inv + $i2;	

fputcsv($output, array(" "),";");
fputcsv($output, array(" "),";");	
fputcsv($output, array(" ","Base de Dados: ", $row_n[0]),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Registos: ", $total_leads),";");
fputcsv($output, array(" ","Contactos Efectuados: ", $contactos_efectuados),";");
fputcsv($output, array(" ","Contactos Uteis: ", $contactos_uteis),";");
fputcsv($output, array(" ","Agendamentos: ", $agendamentos2),";");
fputcsv($output, array(" ","Marcacoes: ", $marcacoes2),";");
fputcsv($output, array(" ","Novas Leads: ", $novos_clientes2),";");
fputcsv($output, array(" ","Total Marcacoes: ", $marc_total),";");
fputcsv($output, array(" ","Contactos Negativos: ", $contactos_negativos),";");
fputcsv($output, array(" ","Dificuldades: ", $dificuldades),";");
fputcsv($output, array(" ","Marcacoes/Total Registos: ", $m_TR),";");
fputcsv($output, array(" ","Marcacoes/Contactos Efectuados: ", $m_CE),";");
fputcsv($output, array(" ","Marcacoes/Contactos Uteis: ", $m_CU),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Contactos Negativos: ", $contactos_negativos),";");
fputcsv($output, array(" ","Pedido de Envio de Amostra: ", $amostra2),";");
fputcsv($output, array(" ","Nao Interessado ", $ni2),";");
fputcsv($output, array(" ","Concorrencia ", $conc2),";");
fputcsv($output, array(" ","Realizou Exame < 5 Meses: ", $exame_recente2),";");
fputcsv($output, array(" ","Otorrino: ", $otorrino2),";");
fputcsv($output, array(" ","Nao Contactar: ", $nao_contactar2),";");
fputcsv($output, array(" ","Falecido: ", $falecido2),";");
fputcsv($output, array(" ","Futuras Accoes: ", $futuras2),";");
fputcsv($output, array(" ","Idade < 45 Anos: ", $idade2),";");
fputcsv($output, array(" ","Ja foi ao consultorio: ", $jconsultorio2),";");
fputcsv($output, array(" ","Outros: ", $outros2),";");
fputcsv($output, array(" ","Reclamacao: ", $reclamacao2),";");
fputcsv($output, array(" ","Tem aparelho AM: ", $jatem2),";");
fputcsv($output, array(" ","Estabelecimento Comercial: ", $comercial2),";");
fputcsv($output, array(" ","Disponivel apos as 20:00: ", $disponivel2),";");
fputcsv($output, array(" ","Contactado Recentemente: ", $contactado2),";");
fputcsv($output, array(" ","Nao Reside/Mudou: ", $naoreside),";");
fputcsv($output, array(" "),";");
fputcsv($output, array(" ","Total Dificuldades: ", $dificuldades),";");
fputcsv($output, array(" ","Fax: ", $fax2),";");
fputcsv($output, array(" ","Numeros Invalidos: ", $inv_total),";");
fputcsv($output, array(" ","Sinal Ocupado: ", $b2),";");
fputcsv($output, array(" ","Nao Atende: ", $na_total),";");
fputcsv($output, array(" ","Voice Mail: ", $vm2),";");
fputcsv($output, array(" ","Referencia Duplicada: ", $rd2),";");
fputcsv($output, array(" ","Erros de Sistema: ", $eri2),";");
fputcsv($output, array(" ","Telefones Invalidos: ", $tinv),";");
}
}











#######################################################################################################################
## NOVO REPORT AM 
#######################################################################################################################
if (isset($_POST['geral_camp_TESTES'])) {
    
$SelectedCampaigns = $_POST['camp_options'];
$CountSelectedCampaigns = count($SelectedCampaigns);
for ($i=0;$i<$CountSelectedCampaigns;$i++)
    {
    if ($CountSelectedCampaigns == 1) 
        {
        $campaigns_IN = "'".$SelectedCampaigns[$i]."'"; 
        }
    elseif ($CountSelectedCampaigns - 1 == $i)
        {
        $campaigns_IN .= "'".$SelectedCampaigns[$i]."'";    
        }    
    else
        {
        $campaigns_IN .= "'".$SelectedCampaigns[$i]."',";
        }
    }

$SelectedFeedbacks = $_POST['feed_options'];
$CountSelectedFeedbacks = count($SelectedFeedbacks);
for ($i=0;$i<$CountSelectedFeedbacks;$i++)
    {
    if ($CountSelectedFeedbacks == 1) 
        {
        $feedbacks_IN = "'".$SelectedFeedbacks[$i]."'"; 
        }
    elseif ($CountSelectedFeedbacks - 1 == $i)
        {
        $feedbacks_IN .= "'".$SelectedFeedbacks[$i]."'";    
        }    
    else
        {
        $feedbacks_IN .= "'".$SelectedFeedbacks[$i]."',";
        }
    } 

//if (isset($_POST['data_inicial'])) {$data_inicial = $_POST['data_inicial'];} 
//if (isset($_POST['data_final'])) {$data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final']));} 


$data_inicial = $_POST['data_inicial'];
if($_POST['data_final']==$data_inicial) { $data_final = date("o-m-d", strtotime("+1 day".$_POST['data_final'])); } else { $data_final = $_POST['data_final']; }



$output = fopen('php://output', 'w');
fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
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
        'Campanha',
        'Data da Chamada',
        'Avisos'),";");

    //MANUAIS
    
    $queryshow = "SHOW TABLES LIKE 'custom_%'";
    $queryshow = mysql_query($queryshow, $link);
    $build_query = "";
    
    for($f=0;$f<mysql_num_rows($queryshow);$f++)
    {
    $rowshow = mysql_fetch_row($queryshow);
    if(($f+1)==mysql_num_rows($queryshow)){ $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) "; } else { $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) UNION ALL ";}
    } 
    
	//echo $build_query;
	
     //$query = "SELECT * FROM `vicidial_list` A LEFT JOIN ( $build_query ) B on A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status WHERE  A.list_id IN('998', '0', '999') AND A.status IN($feedbacks_IN) AND A.last_local_call_time BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time";
    //$query = mysql_query($query) or die(mysql_error());

    
    $query = "SELECT *, B.status AS 'RealStatus',  D.status_name FROM vicidial_list A INNER JOIN vicidial_log B ON A.lead_id=B.lead_id LEFT JOIN ( $build_query ) C on A.lead_id=C.lead_id LEFT JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) D on B.status=D.status WHERE B.status IN($feedbacks_IN) AND B.list_id IN('998','0') AND B.call_date BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.lead_id";
    //echo $query;
    $query = mysql_query($query) or die(mysql_error());

    while ($row = mysql_fetch_assoc($query)) {
                
            $cod = "";
            
            if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
            
            $campid=$row['extra1'];

            if($row['status']=="NOVOCL"){ $no = ''; } else {$no = $row['extra2']; }
            
            //if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
            //if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
            $c_message = "Manual";
            if($row['status']=="MARC" || $row['status']=="NOVOCL") {
            $c_message = "";
            $e_message = "";
            $f_message = "";
            switch($row['tipoconsulta']){
            
            case "CATOS": { $cod = $row['consultorio']; 
                if(        ($row['consultorio'] == "" || $row['consultorio'] == "nenhum" || $row['consultorio'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem CATOS Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            
            case "Branch": { $cod = $row['consultoriodois']; 
                if(        ($row['consultoriodois'] == "" || $row['consultoriodois'] == "nenhum" || $row['consultoriodois'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem Branch Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "Home": { $cod = ""; if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case "semconsulta": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case null: { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            }
            }
        
            fputcsv($output, array(
            
                $row['title'],
                $campid,
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
                $no,
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
                $row['status_name'],
                $c_message,
                $row['call_date'],
                "Manual"
                
            
            ),";"); }

    //INBOUND
    
    // B.tipoconsulta IN ('CATOS','Branch','Home') AND B.marcdata IS NOT NULL AND B.marcdata <> '0000-00-00' AND

    $queryshow = "SHOW TABLES LIKE 'custom_%'";
    $queryshow = mysql_query($queryshow, $link);
    $build_query = "";
    
    
    for($f=0;$f<mysql_num_rows($queryshow);$f++)
    {
    $rowshow = mysql_fetch_row($queryshow);
    if(($f+1)==mysql_num_rows($queryshow)){ $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) "; } else { $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) UNION ALL ";}
    } 
    
     //$query = "SELECT * FROM `vicidial_list` A LEFT JOIN ( $build_query ) B on A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status WHERE  A.list_id IN('998', '0', '999') AND A.status IN($feedbacks_IN) AND A.last_local_call_time BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time";
    //$query = mysql_query($query) or die(mysql_error());

    
    $query = "SELECT  E.campaign_name, D.status_name, B.call_date, C.tipoconsulta, C.consultorio, C.consultoriodois, C.obs,  C.marcdata, C.marchora,  A.title, A.first_name, A.middle_initial, A.last_name, A.address1, A.address2, A.address3, A.state, A.postal_code, A.vendor_lead_code, A.city, A.province, A.country_code, A.phone_number, A.alt_phone, A.date_of_birth, A.user, A.extra1, A.extra2, A.status, B.status as 'Real Status' FROM vicidial_list A INNER JOIN vicidial_closer_log B ON A.lead_id=B.lead_id LEFT JOIN ( $build_query ) C on A.lead_id=C.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) D on B.status=D.status LEFT JOIN vicidial_campaigns E ON E.campaign_id=B.campaign_id WHERE B.status IN($feedbacks_IN) AND B.call_date BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.lead_id";
    //echo $query;
    $query = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($query)) {
                
            $cod = "";
            
            if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
            
            $campid=$row['extra1'];

           if($row['status']=="NOVOCL"){ $no = ''; } else {$no = $row['extra2']; }
          
            //if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
            //if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
            $c_message = "Inbound";
            if($row['status']=="MARC" || $row['status']=="NOVOCL") {
            $c_message = "";
            $e_message = "";
            $f_message = "";
            switch($row['tipoconsulta']){
            
            case "CATOS": { $cod = $row['consultorio']; 
                if(        ($row['consultorio'] == "" || $row['consultorio'] == "nenhum" || $row['consultorio'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem CATOS Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            
            case "Branch": { $cod = $row['consultoriodois']; 
                if(        ($row['consultoriodois'] == "" || $row['consultoriodois'] == "nenhum" || $row['consultoriodois'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem Branch Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "Home": { $cod = ""; if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case "semconsulta": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case null: { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            }
            }
        
            fputcsv($output, array(
            
                $row['title'],
                $campid,
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
                $no,
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
                $row['status_name'],
                $row['campaign_name'],
                $row['call_date'],
                "Inbound"
            
            ),";"); }

    //OUTBOUND

    for($i=0;$i<count($SelectedCampaigns);$i++) {
    
    $sQuery = "SELECT list_id FROM vicidial_lists WHERE campaign_id = '$SelectedCampaigns[$i]' ";
    $sQuery = mysql_query($sQuery, $link) or die(mysql_error());            
    $CountDBs = mysql_num_rows($sQuery);
    $lists_IN = "";
    for ($p=0;$p<$CountDBs;$p++)
    {
        $row = mysql_fetch_row($sQuery);
        if ($CountDBs == 1) 
            {
            $lists_IN = "'".$row[0]."'"; 
            }
        elseif ($CountDBs - 1 == $p)
            {
            $lists_IN .= "'".$row[0]."'";    
            }    
        else
            {
            $lists_IN .= "'".$row[0]."',";
            }
    }
        
    
    $SelectedCampaigns[$i] = strtoupper($SelectedCampaigns[$i]);
    
    $sQuery = "SHOW TABLES LIKE 'custom_$SelectedCampaigns[$i]'"; 

    $rQuery = mysql_query($sQuery, $link);
    $TablesToPrint = mysql_num_rows($rQuery);
    if ($TablesToPrint > 0) 
        {    

        //$sQuery = "SELECT * FROM vicidial_list A LEFT JOIN custom_$SelectedCampaigns[$i] B ON A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status INNER JOIN vicidial_lists D ON A.list_id=D.list_id INNER JOIN (SELECT campaign_id, campaign_name FROM vicidial_campaigns GROUP BY campaign_name) E ON D.campaign_id=E.campaign_id WHERE A.list_id IN($lists_IN) AND A.status IN($feedbacks_IN) AND A.last_local_call_time BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time                       ";
        
        $sQuery = "SELECT F.campaign_name, B.call_date, C.tipoconsulta, C.consultorio, C.consultoriodois, C.obs,  C.marcdata, C.marchora,  A.title, A.first_name, A.middle_initial, A.last_name, A.address1, A.address2, A.address3, A.state, A.postal_code, A.vendor_lead_code, A.city, A.province, A.country_code, A.phone_number, A.alt_phone, A.date_of_birth, A.user, A.extra1, A.extra2, A.status, D.status_name   FROM vicidial_list A INNER JOIN vicidial_log B ON A.lead_id=B.lead_id LEFT JOIN custom_$SelectedCampaigns[$i] C on A.lead_id=C.lead_id LEFT JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) D on B.status=D.status LEFT JOIN vicidial_lists E ON A.list_id=E.list_id LEFT JOIN (SELECT campaign_id, campaign_name FROM vicidial_campaigns GROUP BY campaign_name) F ON E.campaign_id=F.campaign_id  WHERE B.status IN($feedbacks_IN) AND B.call_date BETWEEN '$data_inicial' AND '$data_final' AND B.campaign_id IN('$SelectedCampaigns[$i]') ORDER BY A.lead_id";
        //echo $sQuery."                                          ------<br>";
        $rQuery = mysql_query($sQuery, $link) or die(mysql_error()); 
 
            while ($row = mysql_fetch_assoc($rQuery)) {
                
            $cod = "";
            
            
            
            $campid=$row['extra1'];


           if($row['status']=="NOVOCL"){ $no = ''; } else {$no = $row['extra2']; }
            
            if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
            if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
            

            // controlo de qualidade dos dados
            if($row['status']=="MARC" || $row['status']=="NOVOCL") {
            
            $c_message = "";
            $e_message = "";
            $f_message = "";
            switch($row['tipoconsulta']){
            
            case "CATOS": { $cod = $row['consultorio']; 
                if(        ($row['consultorio'] == "" || $row['consultorio'] == "nenhum" || $row['consultorio'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem CATOS Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            
            case "Branch": { $cod = $row['consultoriodois']; 
                if(        ($row['consultoriodois'] == "" || $row['consultoriodois'] == "nenhum" || $row['consultoriodois'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem Branch Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "Home": { $cod = ""; if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case "semconsulta": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case null: { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            }
            
            $f_message = $c_message . $e_message; }
            
    /*        if($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") { $c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: Tipo Consulta"; }
            if(        ($row['consultorio'] != "" && $row['consultorio']!= "nenhum" && $row['consultorio'] != null) && ($row['tipoconsulta']!="CATOS")             ) {$c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: CATOS";} 
            if(        ($row['consultoriodois'] != "" && $row['consultoriodois']!= "nenhum" && $row['consultoriodois'] != null) && ($row['tipoconsulta']!="Branch")             ) {$c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: Branch";} 

    */        
            fputcsv($output, array(
            
                $row['title'],
                $campid,
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
                $no,
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
                $row['status_name'],
                $row['campaign_name'],
                $row['call_date'],
                "Outbound"
            
            ),";"); }

            }
        }
}

?>