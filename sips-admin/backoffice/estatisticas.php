<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Backoffice</title>
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

<body>
<table align="center" width="100%" border="1">
<tr valign="top">
    <td width="186">
        <iframe width="186" height="600" src="menu.html" frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0">
    </iframe> 
   </td>
   <td width="100%" align="left">
 <?php  		
$user = $_GET["user"];
$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("integra_bd_2011", $con);
	
	$cavs = mysql_query("SELECT DISTINCT CAV from t_contrato");
	
		
		$numcavs = mysql_num_rows($cavs);
		for ($i=0; $i<$numcavs; $i++) {
			$rcavs = mysql_fetch_assoc($cavs);
			$listacav[$i] = $rcavs['CAV'];
		}
		
		
		
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td class='header'>CAV</td>";
		echo "<td class='header'>TV</td>";
		echo "<td class='header'>NET</td>";
		echo "<td class='header'>VOZ</td>";
		echo "<td class='header'>Total CAV</td>";
		echo "</tr>";
		
		$curYear  = date('Y');
		$curMonth = date('n');
		$data = $curYear.'-'.$curMonth.'-01';
		
		
		for ($i=0; $i<$numcavs; $i++) {
			
			$tv = mysql_query("SELECT * FROM t_contrato WHERE Serv_tv_Tipo = 'NS' AND CAV = '".$listacav[$i]."' AND (Estado_Interno = 'Agendado' OR Estado_Interno = 'Em agendamento' OR Estado_Interno = 'Instalado') AND Data_Resolucao >= '$data'") or die(mysql_error());
			
			$net = mysql_query("SELECT * FROM t_contrato WHERE Serv_net_Tipo = 'NS' AND CAV = '".$listacav[$i]."' AND (Estado_Interno = 'Agendado' OR Estado_Interno = 'Em agendamento' OR Estado_Interno = 'Instalado') AND Data_Resolucao >= '$data'") or die(mysql_error());
			
			$voz = mysql_query("SELECT * FROM t_contrato WHERE Serv_voz_Tipo = 'NS' AND CAV = '".$listacav[$i]."' AND (Estado_Interno = 'Agendado' OR Estado_Interno = 'Em agendamento' OR Estado_Interno = 'Instalado') AND Data_Resolucao >= '$data'") or die(mysql_error());
		
			
			
			$totaltv = $totaltv + mysql_num_rows($tv);
			$totalnet = $totalnet + mysql_num_rows($net);
			$totalvoz = $totalvoz + mysql_num_rows($voz);
			$totalcav = (mysql_num_rows($tv) + mysql_num_rows($net) + mysql_num_rows($voz));
			$totalvendas = $totalvendas + $totalcav;
			
			echo "<tr>";
			echo "<td class='lista'>".$listacav[$i]."</td>";
			echo "<td class='lista'>".mysql_num_rows($tv)."</td>";
			echo "<td class='lista'>".mysql_num_rows($net)."</td>";
			echo "<td class='lista'>".mysql_num_rows($voz)."</td>";
			echo "<td class='lista'>".$totalcav."</td>";
			echo "</tr>";

		}
		
		echo "</table>";
		echo "<br>";
		echo "<br>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td class='header'>Total de TV</td>";
		echo "<td class='header'>Total de NET</td>";
		echo "<td class='header'>Total de VOZ</td>";
		echo "<td class='header'>Total de Vendas</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='lista'>".$totaltv."</td>";
		echo "<td class='lista'>".$totalnet."</td>";
		echo "<td class='lista'>".$totalvoz."</td>";
		echo "<td class='lista'>".$totalvendas."</td>";
		echo "</tr>";
		echo "</table>";
	
	mysql_close($con);
	
	
	
	
	
	
?>	
        </td>
</tr>
</table>

</body>
</html>
