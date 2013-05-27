<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS Report ZON - Report Call-Center</title>

<style>

	body {
		font-family:"Courier New", Courier, monospace;
		font-size:15px;
	}


	table {
		border:2px #000 solid;
		
	}
	
	table td {
		font-size:13px;
		font-family:"Courier New", Courier, monospace;
		padding:2px, 2px, 2px, 2px;
		border:1px #c0c0c0 solid; }

</style>

<? $data = $_POST['data']; ?>

</head>

<body>
<table align="center" width="100%" border="0">
<form target='_self' method='post' action='reportzoncc.php'>
<tr>
<td>
	Insira a data:
</td>
<td><input type='text' id='data' name='data' value='AAAA-MM-DD' onclick="this.value = '';" /></td>

<td><input type='submit' name='submit' value='Submeter'  /></td>
<td>Data actual:</td>
<td><? echo $data; ?></td>
</tr>
</form>
	<tr>
    	<td>Data</td>
    	<td>Contactos Efectuados</td>
        <td>Não Contactos</td>
        <td>%</td>
        <td>Contactos Terceiro</td>
        <td>%</td>
        <td>Contactos Decisor</td>
        <td>%</td>
        <td>Contactos Venda</td>
        <td>%</td>
        <td>Contactos Recusa</td>
        <td>%</td>
        <td>Callback Decisor</td>
        <td>%</td>
        <td>Total Vendas</td>
        <td>HR Core</td>
        <td>Vendas CORE TV</td>
        <td>HR TV</td>
        <td>Vendas CORE Voz</td>
        <td>HR Voz</td>
        <td>Vendas CORE Net</td>
        <td>HR Net</td>
        <td>Nº Horas Login</td>
        <td>Nº Horas Comunicação</td>
        <td>Nº Op</td>
        <td>Tempo Médio Conversação</td>
        <td>Tempo Formação</td>
        <td>Tempo Queda entre Script</td>
        <td>Vds/Hr</td>
              
    </tr>
	<?php
$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	
	
	mysql_select_db("sips", $con);
	
	
	

	$naoContactos = mysql_num_rows(mysql_query("SELECT id
	FROM `t_leads`
	WHERE `DContacto` = '$data'
	AND `tcontacto` LIKE 'naocontacto'"));
	$contactTerceiro = mysql_num_rows(mysql_query("SELECT id
	FROM `t_leads`
	WHERE `DContacto` = '$data'
	AND `tcontacto` LIKE 'terceiro'"));
	$contactDecisor = mysql_num_rows(mysql_query("SELECT id
	FROM `t_leads`
	WHERE `DContacto` = '$data'
	AND `tcontacto` LIKE 'decisor'"));
	$contactVenda = mysql_num_rows(mysql_query("SELECT id
	FROM `t_leads`
	WHERE `DContacto` = '$data'
	AND `resultado` LIKE 'venda'"));
	$contactRecusa = mysql_num_rows(mysql_query("SELECT id
	FROM `t_leads`
	WHERE `DContacto` = '$data'
	AND `resultado` LIKE 'recusa'"));
	$contactCB = mysql_num_rows(mysql_query("SELECT id
	FROM `t_leads`
	WHERE `DContacto` = '$data'
	AND `resultado` LIKE 'callback'"));
	
	// Nº horas login
	
	mysql_select_db("asterisk", $con);
	
	
	$dataFim = date('Y-m-d', strtotime('+1 day', strtotime($data))); 
	
	
	$horasConv=mysql_query("select sum(length_in_sec) from vicidial_log where call_date >= '$data' and call_date < '$dataFim' and status IN('CBHOLD','CALLBK','NI','DNC','NP','A','INCALL','SALE','DEC')") or die(mysql_error());
	
	$row = mysql_fetch_row($horasConv);
	$horasConv = $row[0];
	
	
	    function secondsToTime($seconds)
    {
        // extract hours
        $hours = floor($seconds / (60 * 60));
     
        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);
     
        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);
     
        // return the final array
        $obj = array(
            "h" => (int) $hours,
            "m" => (int) $minutes,
            "s" => (int) $seconds,
        );
        return $obj;
    }
	$totContactos = $naoContactos + $contactTerceiro + $contactDecisor;
	$tempoMedio = $horasConv / $totContactos;
	$tempoMedio = secondsToTime($tempoMedio);
	
	
	$horasConv = secondsToTime($horasConv); 
	
	$totUSers = mysql_num_rows(mysql_query("select distinct(user) from vicidial_log where `call_date` >= '$data' and `call_date` < '$dataFim'"))-1;
	
	
	// Tempo total de Login
	
	$pause=mysql_query("select sum(pause_sec) from vicidial_agent_log where event_time <= '$dataFim' and event_time >= '$data'");
	$row = mysql_fetch_row($pause);
	$pause = $row[0];
	$dispo=mysql_query("select sum(dispo_sec) from vicidial_agent_log where event_time <= '$dataFim' and event_time >= '$data'");
	$row = mysql_fetch_row($dispo);
	$dispo = $row[0];
	$talk=mysql_query("select sum(talk_sec) from vicidial_agent_log where event_time <= '$dataFim' and event_time >= '$data'");
	$row = mysql_fetch_row($talk);
	$talk = $row[0];
	$wait=mysql_query("select sum(wait_sec) from vicidial_agent_log where event_time <= '$dataFim' and event_time >= '$data'");
	$row = mysql_fetch_row($wait);
	$wait = $row[0];
	$dead=mysql_query("select sum(dead_sec) from vicidial_agent_log where event_time <= '$dataFim' and event_time >= '$data'");
	$row = mysql_fetch_row($dead);
	$dead = $row[0];
	$TOTALtime = ($pause + $dispo + $talk + $wait + $dead);
	
	$TOTALtime = secondsToTime($TOTALtime);
	
	
	$avgWait=mysql_query("select avg(wait_sec) from vicidial_agent_log where event_time <= '$dataFim' and event_time >= '$data'");
	$row = mysql_fetch_row($avgWait);
	$avgWait = $row[0];
	$avgWait = secondsToTime($avgWait);
	
		
	mysql_select_db("sips", $con);
	
	$tipoRecusa = mysql_query("select mrecusa from t_leads where DContacto = '$data' AND resultado LIKE 'recusa'") or die(mysql_error());
	
	$resumoVendas = mysql_query("select * from t_vendas where data = '$data'") or die(mysql_error());
	$resumoVendas = mysql_fetch_assoc($resumoVendas);
	
	
	
	for ($i=0;$i<21;$i++) {
		$totRecusa[$i] = 0;
		
	}
	
	for ($i=0;$i<mysql_num_rows($tipoRecusa);$i++) {
		$xRecusa = mysql_fetch_assoc($tipoRecusa);
		
		if (utf8_encode($xRecusa['mrecusa']) == '2ª Habitação') 						{ $totRecusa[0] = $totRecusa[0] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Cliente Estrangeiro') 					{ $totRecusa[1] = $totRecusa[1] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'É caro') 								{ $totRecusa[2] = $totRecusa[2] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Falta confiança no serviço') 			{ $totRecusa[3] = $totRecusa[3] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Insatisfação com a Zon') 				{ $totRecusa[4] = $totRecusa[4] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Interesse para mais tarde') 			{ $totRecusa[5] = $totRecusa[5] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Já tem CLIX/OPTIMUS') 					{ $totRecusa[6] = $totRecusa[6] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Já tem MEO') 							{ $totRecusa[7] = $totRecusa[7] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Já tem outros') 						{ $totRecusa[8] = $totRecusa[8] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Já tem PT') 							{ $totRecusa[9] = $totRecusa[9] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Já tem ZON 1P recusa mudar') 			{ $totRecusa[10] = $totRecusa[10] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Já tem ZON 2P recusa mudar') 			{ $totRecusa[11] = $totRecusa[11] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Já tem ZON 3P') 						{ $totRecusa[12] = $totRecusa[12] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Já tem ZON,  sem PC') 					{ $totRecusa[13] = $totRecusa[13] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Melhor oferta de outro operador') 		{ $totRecusa[14] = $totRecusa[14] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Não quer Telemarketing') 				{ $totRecusa[15] = $totRecusa[15] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Não usa telefone fixo') 				{ $totRecusa[16] = $totRecusa[16] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'NS/NR') 								{ $totRecusa[17] = $totRecusa[17] + 1; }
		if (utf8_encode($xRecusa['mrecusa']) == 'Tem fidelização com outro operador')	{ $totRecusa[18] = $totRecusa[18] + 1; }
	

	
	
	}
	
	

	
	//print_r($totRecusa);
	

	$totalTV = $resumoVendas['sd'] + $resumoVendas['sdsptv'] + $resumoVendas['sdsptvhd'] + $resumoVendas['dhd'] + $resumoVendas['fhd'] + $resumoVendas['base'] + $resumoVendas['total'] + $resumoVendas['max']
				+ $resumoVendas['maxtvc'] + $resumoVendas['basesptv'] + $resumoVendas['totalsptv'] + $resumoVendas['2hab'];
	$totalNET = $resumoVendas['6mb'] + $resumoVendas['12mb'] + $resumoVendas['24mb'] + $resumoVendas['30mb'] + $resumoVendas['60mb'] + $resumoVendas['120mb'];
	$totalVOZ = $resumoVendas['ilimitado'] + $resumoVendas['nfds'] + $resumoVendas['noites'];
	$totalCORE = $totalTV + $totalNET + $totalVOZ;
	
	
	
?>	

	<tr>
    	<td>Data</td>
    	<td><? echo $totContactos; ?></td>
        <td><? echo $naoContactos; ?></td>
        <td><? echo (number_format((($naoContactos/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $contactTerceiro; ?></td>
        <td><? echo (number_format((($contactTerceiro/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $contactDecisor; ?></td>
        <td><? echo (number_format((($contactDecisor/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $contactVenda; ?></td>
        <td><? echo (number_format((($contactVenda/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $contactRecusa; ?></td>
        <td><? echo (number_format((($contactRecusa/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $contactCB; ?></td>
        <td><? echo (number_format((($contactCB/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $totalCORE; ?></td>
        <td><? echo (number_format((($totalCORE/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $totalTV; ?></td>
        <td><? echo (number_format((($totalTV/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $totalVOZ; ?></td>
        <td><? echo (number_format((($totalVOZ/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $totalNET; ?></td>
        <td><? echo (number_format((($totalNET/$totContactos)*100),'2',',','')."%"); ?></td>
        <td><? echo $TOTALtime['h'].":".$TOTALtime['m'].":".$TOTALtime['s']; ?></td>
        <td><? echo $horasConv['h'].":".$horasConv['m'].":".$horasConv['s']; ?></td>
        <td><? echo $totUSers; ?></td>
        <td><? echo $tempoMedio['h'].":".$tempoMedio['m'].":".$tempoMedio['s']; ?></td>
        <td>Tempo Formação</td>
        <td><? echo $avgWait['h'].":".$avgWait['m'].":".$avgWait['s']; ?></td>
        <td>Vds/Hr</td>
              
    </tr>

</table>
<br>
Vendas diárias
<table border='0'>
<tr>
	<td>Selecção Digital</td>
	<td>Selecção SportTv</td>
	<td>Selecção SportTv HD</td>
	<td>Digital HD</td>
	<td>Funtastic HD</td>
	<td>Base</td>
	<td>Total</td>
	<td>Max</td>
	<td>Max TvCine</td>
	<td>Base SportTv</td>
	<td>Total SportTv</td>
	<td>2º Habitação</td>
	<td>Brava HD TV</td>
	<td>TV Cine</td>
	<td>Sport TV</td>
	<td>Sport TV HD</td>
	<td>Sport TV Golf</td>
	<td>Sport TV 3</td>
	<td>Disney</td>
	<td>Playboy</td>
	<td>Hot</td>
	<td>Festa Brava</td>
	<td>Caça e Pesca</td>
	<td>TV Globo</td>
	<td>PFC</td>
	<td>Zee TV</td>
	<td>Max</td>
	<td>Set Asia</td>
	<td>6Mb</td>
	<td>12Mb</td>
	<td>24Mb</td>
	<td>Fibra 30Mb</td>
	<td>Fibra 60Mb</td>
	<td>Fibra 120Mb</td>
	<td>Ilimitado</td>
	<td>Noites e FDS</td>
	<td>Noites</td>
	<td>UP TV</td>
	<td>UP Net</td>
	<td>UP Voz</td>
</tr>
<tr>
	<td><? echo $resumoVendas['sd']; ?></td>
	<td><? echo $resumoVendas['sdsptv']; ?></td>
	<td><? echo $resumoVendas['sdsptvhd']; ?></td>
	<td><? echo $resumoVendas['dhd']; ?></td>
	<td><? echo $resumoVendas['fhd']; ?></td>
	<td><? echo $resumoVendas['base']; ?></td>
	<td><? echo $resumoVendas['total']; ?></td>
	<td><? echo $resumoVendas['max']; ?></td>
	<td><? echo $resumoVendas['maxtvc']; ?></td>
	<td><? echo $resumoVendas['basesptv']; ?></td>
	<td><? echo $resumoVendas['totalsptv']; ?></td>
	<td><? echo $resumoVendas['2hab']; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo ""; ?></td>
	<td><? echo $resumoVendas['6mb']; ?></td>
	<td><? echo $resumoVendas['12mb']; ?></td>
	<td><? echo $resumoVendas['24mb']; ?></td>
	<td><? echo $resumoVendas['30mb']; ?></td>
	<td><? echo $resumoVendas['60mb']; ?></td>
	<td><? echo $resumoVendas['120mb']; ?></td>
	<td><? echo $resumoVendas['ilimitado']; ?></td>
	<td><? echo $resumoVendas['nfds']; ?></td>
	<td><? echo $resumoVendas['noites']; ?></td>
	<td><? echo $resumoVendas['uptv']; ?></td>
	<td><? echo $resumoVendas['upnet']; ?></td>
	<td><? echo $resumoVendas['upvoz']; ?></td>
</tr>
</table>
Lotes:
<br>
<table border='0'>
<tr>
	<td>Lote</td>
   <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	<td>Total Registos por Trabalhar</td>
	<td>Registos Inseridos</td>
	<td>Excesso Tentativas</td>
	<td>Anomalia</td>
	<td>Registos Fechados</td>
	<td>Vendas</td>
	<td>Recusas</td>
	<td>Contactos Uteis</td>
	
	<? 
	mysql_select_db("asterisk", $con);
	$listasProspect = mysql_query("select list_name, list_id from vicidial_lists where list_name LIKE '%ZON%' ") or die(mysql_error());
	for ($i=0; $i<mysql_num_rows($listasProspect); $i++) {
		
		$curListId = mysql_fetch_assoc($listasProspect);
		
		$curListNewLeads = mysql_query("select COUNT(lead_id) from vicidial_list WHERE list_id = '$curListId[list_id]' and status IN ('NEW')") or die(mysql_error());
		$curListNewLeads = mysql_fetch_assoc($curListNewLeads);
		
		$curListTotalLeads = mysql_query("select COUNT(lead_id) from vicidial_list WHERE list_id = '$curListId[list_id]'") or die(mysql_error());
		$curListTotalLeads = mysql_fetch_assoc($curListTotalLeads);
		
		$curListLeadsRetry = mysql_query("select SUM(called_count) from vicidial_list WHERE list_id = '$curListId[list_id]' AND called_count > 1 AND status LIKE 'NA' ") or die(mysql_error());
		$curListLeadsRetry = mysql_fetch_assoc($curListLeadsRetry);
		
		$curListTotalErrors = mysql_query("select COUNT(lead_id) from vicidial_list WHERE list_id = '$curListId[list_id]' AND status LIKE 'ERI' ") or die(mysql_error());
		$curListTotalErrors = mysql_fetch_assoc($curListTotalErrors);
		
		$curListTotalClosed = mysql_query("select COUNT(lead_id) from vicidial_list WHERE list_id ='$curListId[list_id]' AND status NOT LIKE 'NA' AND status NOT LIKE 'NEW' AND status NOT LIKE 'N'") or die(mysql_error());
		$curListTotalClosed = mysql_fetch_assoc($curListTotalClosed);
		
		$curListTotalSale = mysql_query("select COUNT(lead_id) from vicidial_list WHERE list_id = '$curListId[list_id]' AND status LIKE 'SALE' ") or die(mysql_error());
		$curListTotalSale = mysql_fetch_assoc($curListTotalSale);
		
		$curListTotalDec = mysql_query("select COUNT(lead_id) from vicidial_list WHERE list_id = '$curListId[list_id]' AND status LIKE 'NI' or status LIKE 'DEC' ") or die(mysql_error());
		$curListTotalDec = mysql_fetch_assoc($curListTotalDec);
		
		echo "<tr><td>".$curListId['list_name']."</td>";
		echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td>".$curListNewLeads['COUNT(lead_id)']."</td>";
		echo "<td>".$curListTotalLeads['COUNT(lead_id)']."</td>";
		echo "<td>".$curListLeadsRetry['SUM(called_count)']."</td>";
		echo "<td>".$curListTotalErrors['COUNT(lead_id)']."</td>";
		echo "<td>".$curListTotalClosed['COUNT(lead_id)']."</td>";
		echo "<td>".$curListTotalSale['COUNT(lead_id)']."</td>";
		echo "<td>".$curListTotalDec['COUNT(lead_id)']."</td>";
		echo "</tr>";	
	}
	
	?>
</table>
<br><br>
Recusas
<table border='0'>

<?

	$curMotive[0] =	"<td> 2ª Habitação</td>";
	$curMotive[1] =	"<td> Cliente Estrangeiro</td>";
	$curMotive[2] =	"<td> É caro</td>";
	$curMotive[3] =	"<td> Falta confiança no serviço</td>";
	$curMotive[4] =	"<td> Insatisfação com a Zon</td>";
	$curMotive[5] =	"<td> Interesse para mais tarde</td>";
	$curMotive[6] =	"<td> Já tem CLIX/OPTIMUS</td>";
	$curMotive[7] =	"<td> Já tem MEO</td>";
	$curMotive[8] =	"<td> Já tem outros</td>";
	$curMotive[9] =	"<td> Já tem PT</td>";
	$curMotive[10] =	"<td> Já tem ZON 1P recusa mudar</td>";
	$curMotive[11] =	"<td> Já tem ZON 2P recusa mudar</td>";
	$curMotive[12] =	"<td> Já tem ZON 3P</td>";
	$curMotive[13] =	"<td> Já tem ZON,  sem PC</td>";
	$curMotive[14] =	"<td> Melhor oferta de outro operador</td>";
	$curMotive[15] =	"<td> Não quer Telemarketing</td>";
	$curMotive[16] =	"<td> Não usa telefone fixo</td>";
	$curMotive[17] =	"<td> NS/NR</td>";
	$curMotive[18] =	"<td> Tem fidelização com outro operador</td>";

//".$curMotive[$i]."

for ($i=0;$i<19;$i++) {
	
	
	
echo "
<tr>
	<td>&nbsp;".$totRecusa[$i]."</td>
</tr> "; }

mysql_close($con);
?>
</table>
</body>
</html>