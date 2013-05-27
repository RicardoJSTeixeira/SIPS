<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Últimos Contactos</title>
<style type="text/css">
.text {
	font-size: 12px;
	font: Verdana, Geneva, sans-serif;
}
.lista {
	font-size:12px;
	font:Verdana, Geneva, sans-serif;
	font-style:normal;
	border:1px;
	border-style:solid;
	border-color:#000;
	padding:3px;
	text-align:left;
}
.header {
	font-family: Tahoma, Geneva, sans-serif;
	font-size: 14px;
	border:1px;
	border-style:solid;
	border-color:#000;
	background-color:#FFC;
	text-align:center;
}
</style>
<?php 

$user = $_GET["user"];
$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("asterisk", $con);
	
	$list = mysql_query("SELECT * FROM vicidial_list WHERE user = '$user' ORDER BY last_local_call_time DESC") or die (mysql_error());
	
	mysql_close($con);

	
	$count = 21;
	
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td class='header'># Contacto</td>";
	echo "<td class='header'>Nome Cliente</td>";
	echo "<td class='header'>Nº Telefone</td>";
	echo "<td class='header'>Comentários</td>";
	echo "</tr>";
	

	
	for ($i=1; $i<$count; $i++) {
		$rlist = mysql_fetch_assoc($list);
		echo "<tr>";
		echo "<td class='lista'>".$i."</td>";
		if ($rlist['first_name'] != NULL) {
		echo "<td class='lista'>".utf8_decode($rlist['first_name'])."</td>"; } else {
			echo "<td class='lista'>".utf8_decode($rlist['address3'])."</td>"; }
		echo "<td class='lista'>".$rlist['phone_number']."</td>";
		echo "<td class='lista'>".$rlist['comments']."</td>";
		echo "</tr>";
		
	}
	

	echo "</table>";
	
?>



</head>

<body>
<br /> 
<br />
<p align="center">
<a href="javascript:window.close()">Fechar Janela</a>
</p>
</body>
</html>