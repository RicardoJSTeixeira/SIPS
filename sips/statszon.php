<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Estatistica de Produto</title>
<style type="text/css">

body label {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
body label.header {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 20px;
	color:#006;
}

body td.header {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	color:#006;
}

body td {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 10px;
	text-align:center;
}
span{
cursor:pointer;
color:white;
background:#09F;
font-size:26px;
font:"Courier New", Courier, monospace
}
counter{
	font-size:24px;
}



input.bbuttons
{
   font-size:16px;
   width:80px;
   height:80px;
   white-space:normal;
}

input.abuttons
{
   background-color:#99CC66;
  
   width:130px;
   height:40px;
   border:2px;
   
	border-left: solid 2px #c3f83a;
	border-top: solid 2px #c3f83a;
	border-right: solid 2px #82a528;
	border-bottom: solid 2px #58701b;
}




textomaior {
	font-size: 16px;
}
textogrande {
	font-size: 16px;
}
textogrande {
	font-size: 18px;
}

</style>
<?php
	$dia = $_POST['dia'];
	$dinicio = $_POST['dinicio'];
	$dfim = $_POST['dfim'];
	$tipo = $_POST['edia'];
	
	
	
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	if ($tipo[0] == 'a') { 
	
	$sips = mysql_query("SELECT * FROM t_leads WHERE DContacto = '$dia' ") or die(mysql_error()); } else {
	$sips = mysql_query("SELECT * FROM t_leads WHERE DContacto >= '$dinicio' AND DContacto <= '$dfim' ") or die(mysql_error()); }
	
	$clip = mysql_num_rows($sips);
	
	function percent($num_amount, $num_total) {
	$count1 = $num_amount / $num_total;
	$count2 = $count1 * 100;
	$count = number_format($count2, 0);
	
	}
	
	
	$tvf = 0;
	$tmn = 0;
	$tms = 0;
	$zon = 0;
	$meo = 0;
	$cbv = 0;
	$vdf = 0;
	$clix = 0;
	$ptc = 0;
	
	for ($i=0; $i<$clip; $i++) {
		
		$rsips = mysql_fetch_assoc($sips);
		
		if ($rsips['ResultCham'] == 'venda') { $tvf = $tvf + 1; }
		if ($rsips['tmorada'] == 'Cabo/Fibra') { $tmc = $tmc + 1; }
		if ($rsips['tmorada'] == 'Satelite/DTH') { $tms = $tms + 1; }
		if ($rsips['OPActual'] == 'ZON TVCabo') { $zon = $zon + 1; }
		if ($rsips['OPActual'] == 'MEO') { $meo = $meo + 1; }
		if ($rsips['OPActual'] == 'Cabovisão') { $cbv = $cbv + 1; }
		if ($rsips['OPActual'] == 'Vodafone') { $vdf = $vdf + 1; }
		if ($rsips['OPActual'] == 'Clix') { $clix = $clix + 1; }
		if ($rsips['OPActual'] == 'PT Comunicações') { $ptc = $ptc + 1; }	
	}
	
	echo $tmc."<br>";
	echo $tms;
	
	$tmc = percent($tmc, $clip);
	$tms = percent($tms, $clip);
	
	mysql_close($con);
	
?>
</head>

<body>

<form action="statszon.php" method="post" name="cservs" target="_self">

<table align="center">
<tr>
	<td colspan="2">Quer visualizar estatisticas de quando?</td>	
</tr>
<tr>
	<td><input type="checkbox" name="edia[]" value="a" />1 dia:</td>
    <td><input name="dia" type="text" onclick="this.value = '';" value="AAAA-MM-DD" size="15" /></td>
</tr>
<tr>
	<td><input type="checkbox" name="edia[]" value="b" />Entre: <input name="dinicio" type="text" onclick="this.value = '';" value="AAAA-MM-DD" size="15" /></td>
    <td>e: <input name="dfim" type="text" onclick="this.value = '';" value="AAAA-MM-DD" size="15" /></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Enviar" name="submit" />
    </td>
</tr>
<tr>
<td>Nº de clientes no período:</td>
<td><? echo $clip; ?></td>
<td>Total de vendas feitas:</td>
<td><? echo $tvf; ?></td>
<td>Tipo de Morada:</td>
<td><? echo "Total de Cabo: ".$tmc." e Sat: ".$tms; ?></td>
</tr>
<tr>
<th colspan="6">Estatistica de Operadores</th>
</tr>
<tr>
<td>Nº Clientes ZON:</td>
<td><? echo $zon; ?></td>
<td>Satisfação:</td>
<td>&nbsp;</td>
<td>Serviços:</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Nº Clientes MEO:</td>
<td><? echo $meo; ?></td>
<td>Satisfação:</td>
<td>&nbsp;</td>
<td>Serviços:</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Nº Clientes Cabovisão:</td>
<td><? echo $cbv; ?></td>
<td>Satisfação:</td>
<td>&nbsp;</td>
<td>Serviços:</td>
<td>&nbsp;</td>
</tr>

<tr>
<td>Nº Clientes Vodafone:</td>
<td><? echo $vdf; ?></td>
<td>Satisfação:</td>
<td>&nbsp;</td>
<td>Serviços:</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Nº Clientes Clix:</td>
<td><? echo $clix; ?></td>
<td>Satisfação:</td>
<td>&nbsp;</td>
<td>Serviços:</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Nº Clientes PT Comunicações:</td>
<td><? echo $ptc ?></td>
<td>Satisfação:</td>
<td>&nbsp;</td>
<td>Serviços:</td>
<td>&nbsp;</td>
</tr>

<th colspan="6">Outros Dados</th>
<tr>
<td>Valor Médio:</td>
<td>&nbsp;</td>
<td>Fidelizados:</td>
<td>&nbsp;</td>
<td>Clientes 3Play</td>
<td>&nbsp;</td>
</tr>

</table>

</form>


</body>
</html>