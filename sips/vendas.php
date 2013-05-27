
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Vendas</title>
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




</head>


<?php
		$con = mysql_connect("localhost","root","admin");
		if (!$con)
		{
			die('Nao me consegui ligar' . mysql_error());
		}
		mysql_select_db("sips", $con);
		
		
		
		
		if($_POST['data'] != '') {
	
			$curSale = mysql_query("SELECT * FROM t_vendas WHERE data = '$_POST[data]'");
		
			if (mysql_num_rows($curSale) > 0) {
				
			$curLine =	mysql_fetch_assoc($curSale);
			
			$curLine['sd'] = $curLine['sd'] + $_POST['sd'];
			$curLine['sdsptv'] = $curLine['sdsptv'] + $_POST['sdsptv'];
			$curLine['sdsptvhd'] = $curLine['sdsptvhd'] + $_POST['sdsptvhd'];
			$curLine['dhd'] = $curLine['dhd'] + $_POST['dhd'];
			$curLine['fhd'] = $curLine['fhd'] + $_POST['fhd'];
			$curLine['base'] = $curLine['base'] + $_POST['base'];
			$curLine['total'] = $curLine['total'] + $_POST['total'];
			$curLine['max'] = $curLine['max'] + $_POST['max'];
			$curLine['maxtvc'] = $curLine['maxtvc'] + $_POST['maxtvc'];
			$curLine['basesptv'] = $curLine['basesptv'] + $_POST['basesptv'];
			$curLine['totalsptv'] = $curLine['totalsptv'] + $_POST['totalsptv'];
			$curLine['2hab'] = $curLine['2hab'] + $_POST['2hab'];
			$curLine['6mb'] = $curLine['6mb'] + $_POST['6mb'];
			$curLine['12mb'] = $curLine['12mb'] + $_POST['12mb'];
			$curLine['24mb'] = $curLine['24mb'] + $_POST['24mb'];
			$curLine['30mb'] = $curLine['30mb'] + $_POST['30mb'];
			$curLine['60mb'] = $curLine['60mb'] + $_POST['60mb'];
			$curLine['120mb'] = $curLine['120mb'] + $_POST['120mb'];
			$curLine['ilimitado'] = $curLine['ilimitado'] + $_POST['ilimitado'];
			$curLine['nfds'] = $curLine['nfds'] + $_POST['nfds'];
			$curLine['noites'] = $curLine['noites'] + $_POST['noites'];
			$curLine['uptv'] = $curLine['uptv'] + $_POST['uptv'];
			$curLine['upnet'] = $curLine['upnet'] + $_POST['upnet'];
			$curLine['upvoz'] = $curLine['upvoz'] + $_POST['upvoz'];
			
			mysql_query("UPDATE t_vendas SET 
			sd = '$curLine[sd]',
			sdsptv = '$curLine[sdsptv]',
			sdsptvhd = '$curLine[sdsptvhd]',
			dhd = '$curLine[dhd]',
			fhd = '$curLine[fhd]',
			base = '$curLine[base]',
			total = '$curLine[total]',
			max = '$curLine[max]',
			maxtvc = '$curLine[maxtvc]',
			basesptv = '$curLine[basesptv]',
			totalsptv = '$curLine[totalsptv]',
			2hab = '".$curLine['2hab']."',	
			6mb = '".$curLine['6mb']."',
			12mb = '".$curLine['12mb']."',
			24mb = '".$curLine['24mb']."',
			30mb = '".$curLine['30mb']."',
			60mb = '".$curLine['60mb']."',
			120mb = '".$curLine['120mb']."',
			ilimitado = '$curLine[ilimitado]',
			nfds = '$curLine[nfds]',
			noites = '$curLine[noites]',
			uptv = '$curLine[uptv]',
			upnet = '$curLine[upnet]',
			upvoz = '$curLine[upvoz]'
			
			where data = '$_POST[data]'") or die(mysql_error());
			
			
			
			} else { 
			
			mysql_query("INSERT INTO t_vendas
				(data, sd, sdsptv, sdsptvhd, dhd, fhd, base, total, max, maxtvc, basesptv, totalsptv, 2hab, 6mb, 12mb, 24mb, 30mb, 60mb, 120mb, ilimitado, nfds, noites, uptv, upnet, upvoz) 
				VALUES
				('$_POST[data]', '$_POST[sd]', '$_POST[sdsptv]', '$_POST[sdsptvhd]', '$_POST[dhd]', '$_POST[fhd]', '$_POST[base]', '$_POST[total]', '$_POST[max]', 
				'$_POST[maxtvc]', '$_POST[basesptv]', '$_POST[totalsptv]', '".$_POST['2hab']."', '".$_POST['6mb']."', '".$_POST['12mb']."', '".$_POST['24mb']."', '".$_POST['30mb']."', '".$_POST['60mb']."', '".$_POST['120mb']."', '$_POST[ilimitado]', '$_POST[nfds]',
				 '$_POST[noites]', '$_POST[uptv]', '$_POST[upnet]', '$_POST[upvoz]') ") or die(mysql_error());	
			
			
			}
					
		
		}
		
		
		mysql_close($con);

?>

<body>
<table align="center" width="100%" border="1">
<tr>
   <td width="186">
   	<iframe width="186" height="600" src="menu.html" frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0">
    </iframe> 
   </td>
   <td width="100%" align="left">
   <form name='inserevendas' method='post' action='vendas.php' target='_self'>
	<table>
	<tr><td colspan='1'>Data</td><td><input type='text' value='<? echo $_POST['data']; ?>' name='data' id='data' onclick="this.value='';" /></td><td colspan='1'><input type='submit' value='Actualizar' /></td></tr>
	
		<tr><td colspan='2'>Televisão</td><td>Total do Dia</td></tr>
		<tr><td>Selecção Digital</td><td><input type='text' name='sd' id='sd' /></td><td><? echo $curLine['sd']; ?></td></tr>
		<tr><td>Selecção SportTv</td><td><input type='text' name='sdsptv' id='sdsptv' /></td><td><? echo $curLine['sdsptv']; ?></td></tr>
		<tr><td>Selecção SportTv HD</td><td><input type='text' name='sdsptvhd' id='' /></td><td><? echo $curLine['sdsptvhd']; ?></td></tr>
		<tr><td>Digital HD</td><td><input type='text' name='dhd' id='' /></td><td><? echo $curLine['dhd']; ?></td></tr>
		<tr><td>Funtastic HD</td><td><input type='text' name='fhd' id='' /></td><td><? echo $curLine['fhd']; ?></td></tr>
		<tr><td colspan='2'>Internet</td></tr>
		<tr><td>6Mb</td><td><input type='text' name='6mb' id='' /></td><td><? echo $curLine['6mb']; ?></td></tr>
		<tr><td>12Mb</td><td><input type='text' name='12mb' id='' /></td><td><? echo $curLine['12mb']; ?></td></tr>
		<tr><td>24Mb</td><td><input type='text' name='24mb' id='' /></td><td><? echo $curLine['24mb']; ?></td></tr>
		<tr><td>Fibra 30Mb</td><td><input type='text' name='30mb' id='' /></td><td><? echo $curLine['30mb']; ?></td></tr>
		<tr><td>Fibra 60Mb</td><td><input type='text' name='60mb' id='' /></td><td><? echo $curLine['60mb']; ?></td></tr>
		<tr><td>Fibra 120Mb</td><td><input type='text' name='120mb' id='' /></td><td><? echo $curLine['120mb']; ?></td></tr>
		<tr><td colspan='2'>Telefone</td></tr>
		<tr><td>Ilimitado</td><td><input type='text' name='ilimitado' id='' /></td><td><? echo $curLine['ilimitado']; ?></td></tr>
		<tr><td>Noites e FDS</td><td><input type='text' name='nfds' id='' /></td><td><? echo $curLine['nfds']; ?></td></tr>
		<tr><td>Noites</td><td><input type='text' name='noites' id='' /></td><td><? echo $curLine['noites']; ?></td></tr>
		<tr><td colspan='2'>Upgrade</td></tr>
		<tr><td>UpGrade TV</td><td><input type='text' name='uptv' id='' /></td><td><? echo $curLine['uptv']; ?></td></tr>
		<tr><td>UpGrade NET</td><td><input type='text' name='upnet' id='' /></td><td><? echo $curLine['upnet']; ?></td></tr>
		<tr><td>UpGrade VOZ</td><td><input type='text' name='upvoz' id='' /></td><td><? echo $curLine['upvoz']; ?></td></tr>
		<tr><td colspan='2'>Satélite</td></tr>
		<tr><td>Base</td><td><input type='text' name='base' id='' /></td><td><? echo $curLine['base']; ?></td></tr>
		<tr><td>Total</td><td><input type='text' name='total' id='' /></td><td><? echo $curLine['total']; ?></td></tr>
		<tr><td>Max</td><td><input type='text' name='max' id='' /></td><td><? echo $curLine['max']; ?></td></tr>
		<tr><td>Max TvCine</td><td><input type='text' name='maxtvc' id='' /></td><td><? echo $curLine['maxtvc']; ?></td></tr>
		<tr><td>Base SportTv</td><td><input type='text' name='basesptv' id='' /></td><td><? echo $curLine['basesptv']; ?></td></tr>
		<tr><td>Total SportTv</td><td><input type='text' name='totalsptv' id='' /></td><td><? echo $curLine['totalsptv']; ?></td></tr>
		<tr><td>2º Habitação</td><td><input type='text' name='2hab' id='' /></td><td><? echo $curLine['2hab']; ?></td></tr>
		
	</table>
   </form>
   </td>
</tr>
</table>

</body>
</html>
