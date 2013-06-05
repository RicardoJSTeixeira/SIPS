<?
define('sugarEntry', TRUE);  
require_once('../crm/include/nusoap/nusoap.php');

$soapclient = new nusoap_client('http://192.168.1.250/crm/soap.php?wsdl',true); 
 $err = $soapclient->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}

 $user_auth = array(
                 'user_auth' => array(
                       'user_name' => 'admin',
                       'password' => md5('joao1234'),
                       'version' => '0.1'
                       ),
                 'application_name' => 'soapleadcapture');


//login
 $result_array = $soapclient->call('login',$user_auth);
  $err = $soapclient->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}
 $session_id =  $result_array['id'];

 $user_guid = $soapclient->call('get_user_id',$session_id);
   $err = $soapclient->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}




//	$vendor_lead_code - Principal gestor
//	$phone_number - Nº tlf
//	$title - Data do Contrato
//	$first_name - Nome
//	$middle_initial - Nº contribuinte
//	$last_name - E-Mail
//	$address1 - Morada
//	$address2 - site
//	$address3 - tlf 2
//	$city - Localidade
//	$stage distrito
//	$province actividade
//	$postal_code codigo postal
//	$country_code porta
//	$alt_phone telefone alt
//	$comments comentarios
//
//
// 
//
//
//$firstName = 'Teste API';
//$lastName = 'API Teste';
//$phone = '21000000';
//$city = 'Cidade';
//$acc = 'Account';
//$desc = 'Descricao';

 // create lead
 
	$con = mysql_connect("localhost","sipsadmin", "sipsps2012");
	if (!$con)
  	{
  		die('Não me consegui ligar 1' . mysql_error());
  	}
	mysql_select_db("sugarcrm", $con);
 
 $qry = "SELECT id from leads where refered_by LIKE '$lead_id'";
 $qry = mysql_query($qry, $con);
 $qryRows = mysql_num_rows($qry);
 
 if ($qryRows < 1) {
 
 $qry = "SELECT comments, first_name, vendor_lead_code, alt_phone, phone_number, address3, address1, country_code, city, state, postal_code, address2, province
 		FROM vicidial_list
		WHERE lead_id = $lead_id";
		
 $qryRslt = mysql_query($qry, $link);		
 $row = mysql_fetch_row($qryRslt);
 
 mysql_select_db("sugarcrm", $link);
 
 $set_entry_params = array(
                       'session' => $session_id,
                       'module_name' => 'Leads',
                       'name_value_list'=>array(
                           	array('name'=>'description','value'=>$row[0]),
							array('name'=>'first_name','value'=>$row[1]),
							array('name'=>'last_name','value'=>$row[2]),
							array('name'=>'phone_mobile','value'=>$row[3]),
							array('name'=>'phone_work','value'=>$row[4]),
							array('name'=>'phone_other','value'=>$row[5]),
							array('name'=>'primary_address_street','value'=>$row[6].$row[7]),
							array('name'=>'primary_address_city','value'=>$row[8]),
							array('name'=>'primary_address_state','value'=>$row[9]),
							array('name'=>'primary_address_postalcode','value'=>$row[10]),
							array('name'=>'website','value'=>$row[11]),
							array('name'=>'lead_source_description','value'=>'Call-Center Energy'),
							array('name'=>'lead_source','value'=>'Cold Call'),
							array('name'=>'refered_by','value'=>$lead_id),
							array('name'=>'department','value'=>$row[12])));
                           

	 $result = $soapclient->call('set_entry',$set_entry_params);
  
  $err = $soapclient->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}

} 
?>