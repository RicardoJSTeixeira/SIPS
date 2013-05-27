
<?php
require('dbconnect.php');
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {$header.="../";}
define("ROOT", $header);
$today=date("o-m-d");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Formulário - Novo Cliente</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo ROOT; ?>css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../calendar/calendar_db.js"></script>
<link rel="stylesheet" href="../calendar/calendar.css" />
<?php


if (isset($_POST['operador'])) {$operador = $_POST['operador'];}
elseif (isset($_GET['operador'])) {$operador = $_GET['operador'];}
if (isset($_POST['campanha'])) {$campanha = $_POST['campanha'];}
elseif (isset($_GET['campanha'])) {$campanha = $_GET['campanha'];}

if (isset($_POST['lead_id'])) {$lead_id = $_POST['lead_id'];}
elseif (isset($_GET['lead_id'])) {$lead_id = $_GET['lead_id'];}

$query = "SELECT list_id, extra1, extra2 FROM vicidial_list WHERE lead_id='$lead_id'";
$query = mysql_query($query, $link);
$fetch_row = mysql_fetch_row($query);

$list_id = $fetch_row[0];
$extra1 = $fetch_row[1];
$extra2 = $fetch_row[2];

if (isset($_POST['inout'])) {$inout = $_POST['inout'];}
elseif (isset($_GET['inout'])) {$inout = $_GET['inout'];}

if (isset($_POST['owner'])) {$owner = $_POST['owner'];}
elseif (isset($_GET['owner'])) {$owner = $_GET['owner'];}
if (isset($_POST['security_phrase'])) {$security_phrase = $_POST['security_phrase'];}
elseif (isset($_GET['security_phrase'])) {$security_phrase = $_GET['security_phrase'];}
if (isset($_POST['title'])) {$title = $_POST['title'];}
elseif (isset($_GET['title'])) {$title = $_GET['title'];}
if (isset($_POST['first_name'])) {$first_name = $_POST['first_name'];}
elseif (isset($_GET['first_name'])) {$first_name = $_GET['first_name'];}
if (isset($_POST['middle_initial'])) {$middle_initial = $_POST['middle_initial'];}
elseif (isset($_GET['middle_initial'])) {$middle_initial = $_GET['middle_initial'];}
if (isset($_POST['last_name'])) {$last_name = $_POST['last_name'];}
elseif (isset($_GET['last_name'])) {$last_name = $_GET['last_name'];}
if (isset($_POST['address1'])) {$address1 = $_POST['address1'];}
elseif (isset($_GET['address1'])) {$address1 = $_GET['address1'];}
if (isset($_POST['vendor_lead_code'])) {$vendor_lead_code = $_POST['vendor_lead_code'];}
elseif (isset($_GET['vendor_lead_code'])) {$vendor_lead_code = $_GET['vendor_lead_code'];}
if (isset($_POST['address2'])) {$address2 = $_POST['address2'];}
elseif (isset($_GET['address2'])) {$address2 = $_GET['address2'];}
if (isset($_POST['address3'])) {$address3 = $_POST['address3'];}
elseif (isset($_GET['address3'])) {$address3 = $_GET['address3'];}
if (isset($_POST['city'])) {$city = $_POST['city'];}
elseif (isset($_GET['city'])) {$city = $_GET['city'];}
if (isset($_POST['province'])) {$province = $_POST['province'];}
elseif (isset($_GET['province'])) {$province = $_GET['province'];}

if (isset($_POST['state'])) {$state = $_POST['state'];}
elseif (isset($_GET['state'])) {$state = $_GET['state'];}
if (isset($_POST['postal_code'])) {$postal_code = $_POST['postal_code'];}
elseif (isset($_GET['postal_code'])) {$postal_code = $_GET['postal_code'];}
if (isset($_POST['country_code'])) {$country_code = $_POST['country_code'];}
elseif (isset($_GET['country_code'])) {$country_code = $_GET['country_code'];}
if (isset($_POST['date_of_birth'])) {$date_of_birth = $_POST['date_of_birth'];}
elseif (isset($_GET['date_of_birth'])) {$date_of_birth = $_GET['date_of_birth'];}
if (isset($_POST['phone_number'])) {$phone_number = $_POST['phone_number'];}
elseif (isset($_GET['phone_number'])) {$phone_number = $_GET['phone_number'];}
if (isset($_POST['alt_phone'])) {$alt_phone = $_POST['alt_phone'];}
elseif (isset($_GET['alt_phone'])) {$alt_phone = $_GET['alt_phone'];}
if (isset($_POST['comments'])) {$comments = $_POST['comments'];}
elseif (isset($_GET['comments'])) {$comments = $_GET['comments'];}

if (isset($_POST['tipoconsulta'])) {$tipoconsulta = $_POST['tipoconsulta'];}
elseif (isset($_GET['tipoconsulta'])) {$tipoconsulta = $_GET['tipoconsulta'];}
if (isset($_POST['consultorio'])) {$consultorio = $_POST['consultorio'];}
elseif (isset($_GET['consultorio'])) {$consultorio = $_GET['consultorio'];}
if (isset($_POST['consultoriodois'])) {$consultoriodois = $_POST['consultoriodois'];}
elseif (isset($_GET['consultoriodois'])) {$consultoriodois = $_GET['consultoriodois'];}

if (isset($_POST['obs'])) {$obs = $_POST['obs'];}
elseif (isset($_GET['obs'])) {$obs = $_GET['obs'];}

if (isset($_POST['marcdata'])) {$marcdata = $_POST['marcdata'];}
elseif (isset($_GET['marcdata'])) {$marcdata = $_GET['marcdata'];}



###########################################################################################

if (isset($_POST['marchora_horas'])) {$marchora_horas = $_POST['marchora_horas'];}
elseif (isset($_GET['marchora_horas'])) {$marchora_horas = $_GET['marchora_horas'];}
if (isset($_POST['marchora_minutos'])) {$marchora_minutos = $_POST['marchora_minutos'];}
elseif (isset($_GET['marchora_minutos'])) {$marchora_minutos = $_GET['marchora_minutos'];}
$marchora = $marchora_horas.":".$marchora_minutos.":00";

###########################################################################################






if ($_POST['nc_flag'] == "nc_post")
{
	if($list_id<1) { $list_id='999'; }
$query = "	INSERT INTO vicidial_list (entry_date, status, user, vendor_lead_code, list_id, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, date_of_birth, alt_phone, security_phrase, comments, source_id, owner, last_local_call_time, extra1, extra2) VALUES 
			('$today','NOVOCL','$operador','$vendor_lead_code','$list_id','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$date_of_birth','$alt_phone','$security_phrase','$comments', '$comments', '$owner', NOW(), '$extra1', '$extra2')";
$query = mysql_query($query) or die (mysql_error());
####################################################


$query = "SELECT LAST_INSERT_ID();";
$query = mysql_query($query);
$row = mysql_fetch_assoc($query);
$last_insert_id = $row['LAST_INSERT_ID()']; 
####################################################

$query = " INSERT INTO custom_".$campanha."(lead_id, tipoconsulta, consultorio, consultoriodois, marchora, obs, marcdata)
			VALUES ('$last_insert_id','$tipoconsulta[0]','$consultorio[0]','$consultoriodois[0]','$marchora','$obs','$marcdata')";
$query = mysql_query($query);

if($inout=="OUT") {
	
$rand = mt_rand(0,255);	
	
$unique_id = date("U").".".$rand;	

$NOW_TIME = date("Y-m-d H:i:s");

	
$query = "INSERT INTO vicidial_log (
	uniqueid,
	lead_id,
	list_id,
	campaign_id,
	call_date,

	status,
	phone_code,
	phone_number,
	user,
	comments,
	processed,

	alt_dial
	
	) values (
	
	'$unique_id',
	'$last_insert_id',
	'$list_id',
	'$campanha',
	'$NOW_TIME',

	'NOVOCL',
	'1',
	'$phone_number',
	'$operador',
	'NOVOCL',
	'N',

	'NOVOCL'
	
);";

$query = mysql_query($query) or die(mysql_error());

} else {

$rand = mt_rand(0,255);	

$unique_id = date("U").".".$rand;

$NOW_TIME = date("Y-m-d H:i:s");

$query = "INSERT INTO vicidial_closer_log (
	closecallid,
	lead_id,
	list_id,
	campaign_id,
	call_date,

	status,
	phone_code,
	phone_number,
	user,
	comments,
	processed,

	uniqueid
	
	) values (
	
	'',
	'$last_insert_id',
	'$list_id',
	'$campanha',
	'$NOW_TIME',

	'NOVOCL',
	'1',
	'$phone_number',
	'$operador',
	'NOVOCL',
	'N',

	'$unique_id'
	
);";

$query = mysql_query($query) or die(mysql_error());

}
echo "<script type=text/javascript>self.close();</script>";
}


?>

</head>
<body>
<br>
<div class=cc-mstyle>
<table>
<tr>
<td id='icon32'><img src='<?php echo ROOT; ?>images/icons/group.png' /></td>
<td id='submenu-title'> Novo Cliente </td>
<td></td>
<td onclick="javascript:document.getElementById('form_custom_fields').submit();" style='text-align:right; width:30px;'>Gravar</td>
<td id='icon32'><img onclick="javascript:document.getElementById('form_custom_fields').submit();" src='<?php echo ROOT; ?>images/icons/group_add.png' /></td>
</tr>
</table>
</div>
<div id=work-area> 
<br>
 
	
<span id="dados" class=cc-mstyle style='border:none; width:70%;'>
<form id="form_custom_fields" name="form_custom_fields" action="novo_cliente.php" method=POST>
<input type=hidden value='<?php echo $lead_id; ?>' name=lead_id>	
<input type=hidden value='<?php echo $operador; ?>' name=operador>
<input type=hidden value='<?php echo $campanha; ?>' name=campanha>	
<input type=hidden value='<?php echo $inout; ?>' name=inout>		
<input type=hidden value='nc_post' name="nc_flag">
<table>
<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Ref. Cliente </p></div></td>
<td style='width:225px'><input type=text name=owner id=owner value=""></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Pref. Marcação </p></div></td>
<td><input type=text name=security_phrase id=security_phrase value="<?php echo $security_phrase; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Title </p></div></td>
<td><input type=text name=title id=title value=""></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Nome </p></div></td>
<td><input type=text name=first_name id=first_name value=""></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Nome(s) do Meio </p></div></td>
<td><input type=text name=middle_initial id=middle_initial value=""></td>
</tr>


<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Apelido </p></div></td>
<td><input type=text name=last_name id=last_name value=""></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Morada </p></div></td>
<td><input type=text name=address1 id=address1 value="<?php echo $address1; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Nº da Porta </p></div></td>
<td><input type=text name=vendor_lead_code id=vendor_lead_code value="<?php echo $vendor_lead_code; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Morada 2 </p></div></td>
<td><input type=text name=address2 id=address2 value="<?php echo $address2; ?>"></td>
</tr>

 <tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Morada 3 </p></div></td>
<td><input type=text name=address3 id=address3 value="<?php echo $address3; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Localidade </p></div></td>
<td><input type=text name=city id=city value="<?php echo $city; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Concelho </p></div></td>
<td><input type=text name=province id=province value="<?php echo $province; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Distrito </p></div></td>
<td><input type=text name=state id=state value="<?php echo $state; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Codigo Postal </p></div></td>
<td><input type=text name=postal_code id=postal_code value="<?php echo $postal_code; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Cod. País </p></div></td>
<td><input type=text name=country_code id=country_code value="<?php echo $country_code; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Data Nascimento </p></div></td>
<td><input type=text name=date_of_birth id=date_of_birth value=""></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Telefone </p></div></td>
<td>
<input type=text name=phone_number id=phone_number value="<?php echo $phone_number; ?>">
</td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Telefone Alternativo </p></div></td>
<td><input type=text name=alt_phone id=alt_phone value="<?php echo $alt_phone; ?>"></td>
</tr>

<tr>
<td style='width:225px'> <div class=cc-mstyle style='height:28px;border-color:#c0c0c0;'><p> Cod. Campanha </p></div></td>
<td><input type='text' name=comments id=comments value="<?php echo $comments; ?>"></input></td>
</tr>
<tr>

</td>
</tr>
</table>
</span>

<br>
<div class=cc-mstyle>
<table>
<tr>
<td id='icon32'><img src='<?php echo ROOT; ?>images/icons/group.png' /></td>
<td id='submenu-title'> Dados da Marcação </td>
<td></td>
<td onclick="javascript:document.getElementById('form_custom_fields').submit();" style='text-align:right; width:30px;'>Gravar</td>
<td id='icon32'><img onclick="javascript:document.getElementById('form_custom_fields').submit();" src='<?php echo ROOT; ?>images/icons/group_add.png' /></td>
</tr>
</table>
</div>

<table>
<tr>
<td align="right">
<b>Tipo de Consulta:</b>
</td>
<td align="left">
<input id="tipoconsulta[]" type="RADIO" value="Home" name="tipoconsulta[]">
Casa
<input id="tipoconsulta[]" type="RADIO" value="CATOS" name="tipoconsulta[]">
CATO
<input id="tipoconsulta[]" type="RADIO" value="Branch" name="tipoconsulta[]">
Consultorio
<input id="tipoconsulta[]" type="RADIO" value="semconsulta" checked name="tipoconsulta[]">
Nenhum
</tr>
<tr>	
<td align="right">
<b>CATO mais perto da área de residência do cliente</b>
</td>
<td align="left">
<select id="consultorio[]" name="consultorio[]" size="50" style="height:90px; width:375px;" multiple="">
<option selected="" value="nenhum">Nenhum </option>
<option value="C/AGD"> Águeda </option>
<option value="C/AGS"> Algés </option>
<option value="C/AMR"> Amarante </option>
<option value="C/AND"> Anadia </option>
<option value="C/AVZ"> Arcos de Valdevez </option>
<option value="C/BRC"> Barcelos </option>
<option value="C/BEL"> Bebém </option>
<option value="C/CTH"> Cantanhede </option>
<option value="C/CTX"> Cartaxo </option>
<option value="C/CHV"> Chaves </option>
<option value="C/COR"> Coruche </option>
<option value="C/ELV"> Elvas </option>
<option value="C/ENT"> Entroncamento </option>
<option value="C/ESP"> Espinho </option>
<option value="C/ETJ"> Estarreja </option>
<option value="C/FAF"> Fafe </option>
<option value="C/FUD"> Fundão </option>
<option value="C/GOV"> Gouveia </option>
<option value="C/GDL"> Grândola </option>
<option value="C/LGS"> Lagos </option>
<option value="C/LMG"> Lamego </option>
<option value="C/LRJ"> Laranjeiro </option>
<option value="C/LOL"> Loulé </option>
<option value="C/LOR"> Loures </option>
<option value="C/LSA"> Lousã </option>
<option value="C/MFA"> Mafra </option>
<option value="C/MGL"> Mangualde </option>
<option value="C/MGR"> Marinha Grande </option>
<option value="C/MED"> Mêda </option>
<option value="C/MRD"> Mirandela </option>
<option value="C/MTN"> Montemor-o-Novo </option>
<option value="C/MOC"> Moscavide </option>
<option value="C/MUR"> Moura </option>
<option value="C/NIS"> Nisa </option>
<option value="C/OEI"> Oeiras </option>
<option value="C/OLH"> Olhão </option>
<option value="C/OLV"> Olivais </option>
<option value="C/OAZ"> Oliveira de Azeméis </option>
<option value="C/ODB"> Oliveira do Bairro </option>
<option value="C/ORE"> Ourém </option>
<option value="C/OVR"> Ovar </option>
<option value="C/PDG"> Pedrógão Grande </option>
<option value="C/PRD"> Parede </option>
<option value="C/PNE"> Peniche </option>
<option value="C/PNL"> Pinhel </option>
<option value="C/QLZ"> Queluz </option>
<option value="C/SMC"> Samora Correia </option>
<option value="C/SCD"> Santa Comba Dão </option>
<option value="C/STG"> Santiago do Cacém </option>
<option value="C/STR"> Santo Tirso </option>
<option value="C/SEI"> Seia </option>
<option value="C/SSB"> Sesimbra </option>
<option value="C/SIV"> Silves </option>
<option value="C/TVR"> Tavira </option>
<option value="C/TDL"> Tondela </option>
<option value="C/TRN"> Torres Novas </option>
<option value="C/VCB"> Vale de Cambra </option>
</select>
</td>	
</tr>	
<tr>
<td align="right">
<b>Consultorio mais perto da área de residencia do cliente</b>
</td>
<td align="left">
<select id="consultoriodois[]" name="consultoriodois[]" size="1" style="height:90px; width:375px;" multiple="">
<option selected="" value="nenhum">Nenhum </option>
<option value="B/ABN"> Consultório Abrantes </option>
<option value="B/ALM"> Consultório Almada </option>
<option value="B/ALV"> Consultório Lisboa - Alvalade </option>
<option value="B/AMD"> Consultório Amadora </option>
<option value="B/AMS"> Consultório Amora </option>
<option value="B/AVE"> Consultório Aveiro </option>
<option value="B/BEJ"> Consultório Beja </option>
<option value="B/BEN"> Consultório Lisboa - Benfica </option>
<option value="B/BIO"> Consultório Ponta Delgada </option>
<option value="B/BLX"> Consultório Lisboa - Baixa </option>
<option value="B/BRA"> Consultório Braga </option>
<option value="B/BRN"> Consultório Bragança </option>
<option value="B/BRR"> Consultório Barreiro </option>
<option value="B/CAB"> Consultório Castelo Branco </option>
<option value="B/CAC"> Consultório Cacém </option>
<option value="B/CDR"> Consultório Caldas da Rainha </option>
<option value="B/CAS"> Consultório Cascais </option>
<option value="B/CED"> Consultório Porto - Cedofeita </option>
<option value="B/COI"> Consultório Coimbra </option>
<option value="B/COP"> Consultório Porto - Bolhão </option>
<option value="B/COV"> Consultório Covilhã </option>
<option value="B/EVO"> Consultório Évora </option>
<option value="B/FAR"> Consultório Faro </option>
<option value="B/FDF"> Consultório Figueira da Foz </option>
<option value="B/GAI"> Consultório Gaia </option>
<option value="B/GRD"> Consultório Guarda </option>
<option value="B/GUI"> Consultório Guimarães </option>
<option value="B/HHPOR"> Consultório Porto - Boavista </option>
<option value="B/LEI"> Consultório Leiria </option>
<option value="B/MAD"> Consultório Funchal </option>
<option value="B/MIC"> Consultório Lisboa - Arroios </option>
<option value="B/MMT"> Consultório Mem Martins </option>
<option value="B/MTJ"> Consultório Montijo </option>
<option value="B/MTS"> Consultório Matosinhos </option>
<option value="B/ODI"> Consultório Odivelas </option>
<option value="B/OMT"> Consultório Lisboa - Avenidas Novas </option>
<option value="B/OUR"> Consultório Lisboa - Campo de Ourique </option>
<option value="B/PEN"> Consultório Penafiel </option>
<option value="B/PMB"> Consultório Pombal </option>
<option value="B/PTL"> Consultório Portalegre </option>
<option value="B/PTM"> Consultório Portimão </option>
<option value="B/SAN"> Consultório Santarém </option>
<option value="B/SMF"> Consultório Santa Maria da Feira </option>
<option value="B/STB"> Consultório Setúbal </option>
<option value="B/TMR"> Consultório Tomar </option>
<option value="B/TVD"> Consultório Torres Vedras </option>
<option value="B/VCA"> Consultório Viana do Castelo </option>
<option value="B/VFX"> Consultório Vila Franca de Xira </option>
<option value="B/VIS"> Consultório Viseu </option>
<option value="B/VLR"> Consultório Vila Real </option>
</select>
</td>	
</tr>	
<tr>
<td align="right">
<b>
Data em que se realizará o Rastreio Gratuito:
<br>
<ul>
<li>Casa: a partir de 5 dias úteis / máximo 2 semanas </li>
<li>Consultório: a partir de 3 dias úteis / máximo 2 semanas </li>
</ul>
</b>
</td>
<td align="left">
<input id="marcdata" type="text" value="" name="marcdata" style="width:150px"><td>
<script language="JavaScript">
var o_cal = new tcal ({
// form name
'formname': 'form_custom_fields',
// input name 
'controlname': 'marcdata'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Monday week start
</script>
<script language='Javascript'>
var user = '<?php echo $_GET[operador] ?>';
  var marcado=false;
  function getRadioValue() {
    var colRadio = document.getElementsByName('tipoconsulta[]');
    for (var i = 0; i < colRadio.length; i++) {
      if (colRadio[i].checked == true) {
        return colRadio[i].value;
      }
    }
    return null;
  }
  function calendarOpener() {
    var mtd = getRadioValue();
    if (mtd != 'semconsulta') {
      if (mtd != 'Home') {
        if (mtd == 'Branch') {
          var e = document.getElementById('consultoriodois[]');
          var ref = e.options[e.selectedIndex].value;
        }
  	 	else  		{
          if (mtd == 'CATOS') {
            var e = document.getElementById('consultorio[]');
            var ref = e.options[e.selectedIndex].value;
          }
        } 	var url = '../sips-admin/reservas/views/calendar_container.php?ref=' + encodeURIComponent(ref) +'&user='+encodeURIComponent(user);
 }
  	else {
      var cp = parent.document.getElementById('postal_code').value;
      var url = '../sips-admin/reservas/views/calendar_container.php?cp=' + encodeURIComponent(cp) +'&user='+encodeURIComponent(user);
    }
    window.open(url,'Calendario','fullscreen=yes, scrollbars=auto,status=1');
  }
  }
  
</script>

<h1>
  
  <a href='#' onclick='calendarOpener(); return false;' >
  Calendário 
  </a>
  
</h1>
</td>	
<tr>
	<tr>
<td align="right">
<b>
Hora em que se realizará o Rastreio Gratuito:
<br>
<ul>
<li>Casa: das 9:00 às 19:00 </li>
<li>Consultório: das 10:00 às 17:30 </li>
</ul>
</b>
</td>
<td><select name='marchora_horas' id='HOUR_marchora'><option>09</option><option>10</option><option>11</option><option>12</option><option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option><option>19</option><option>20</option></select></td>

<td><select name='marchora_minutos'  id='MINUTE_marchora'><option>00</option><option>05</option><option>10</option><option>15</option><option>20</option><option>25</option><option>30</option><option>35</option><option>40</option><option>45</option><option>50</option><option>55</option></select></td>	
<tr>
<td align="left" colspan="3">
<b>Campo de Observações (nome de quem irá acompanhar durante a consulta/referências da morada/outros)</b>
<br>
<textarea id="obs" style="width:400px" cols="10" rows="1" name="obs"></textarea>
</td>	
	
	
	
	
</tr>	
</table>



</form>
</div>
</body>
</html>
