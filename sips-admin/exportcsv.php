<?



// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings

if (isset($_POST['data'])) {$data = $_POST['data'];} else {$data = date('o-m-d');}
$datafim = date("o-m-d", strtotime("+1 day".$data));

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
        'Tipo Cliente'),";");


// fetch the data



$con = mysql_connect("localhost","sipsadmin","sipsps2012");
	if (!$con)
	{
		die('Não me consegui ligar' . mysql_error());
	}
	
	//mysql_query("SET NAMES 'UTF8'");
		
	$datafim = date("o-m-d", strtotime("+1 day".$data)); 
	$datainicio = $data;
	mysql_select_db("asterisk", $con);
	
	$curCamp = mysql_query("SELECT campaign_id from vicidial_campaigns WHERE active LIKE 'Y'") or die(mysql_error());
				
	for($i=0;$i<mysql_num_rows($curCamp);$i++) {
        	$thisCampaign = mysql_fetch_assoc($curCamp);
			
			
			
			$rows = mysql_query("SELECT * FROM vicidial_list INNER JOIN custom_".$thisCampaign['campaign_id']." ON vicidial_list.lead_id = custom_".$thisCampaign['campaign_id'].".lead_id WHERE vicidial_list.status = 'Marc' AND vicidial_list.last_local_call_time >= '$data' AND vicidial_list.last_local_call_time <= '$datafim'") or die(mysql_error());
			
			
			while ($row = mysql_fetch_assoc($rows)) {
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
				""
			
			),";"); }

			
	}
?>