<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Últimos Contactos</title>
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<?php 

$user = $_GET["user"];
$con = mysql_connect("localhost","sipsadmin", "sipsps2012");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("asterisk", $con);
	mysql_query('SET NAMES utf8'); 

        $list = mysql_query("SELECT * FROM vicidial_list WHERE user = '$user' ORDER BY last_local_call_time DESC LIMIT 0 , 30") or die (mysql_error());
	
	mysql_close($con);

	
	$count = 21;
	echo "<div class='cc-mstyle'>";
	echo "<table align='center' style='width:100%'>";
	echo "<tr>";
	echo "<td class='header'># Contacto</td>";
	echo "<td class='header'>Nome Cliente</td>";
	echo "<td class='header'>Nº Telefone</td>";
	echo "<td class='header'>Comentários</td>";
	echo "</tr>";
	

	
	for ($i=1; $i<$count; $i++) {
		$rlist = mysql_fetch_assoc($list);
		echo "<tr>";
		echo "<td >".$i."</td>";
		if ($rlist['first_name'] != NULL) {
		echo "<td >$rlist[first_name]</td>"; } else {
			echo "<td >$rlist[address3]</td>"; }
		echo "<td >".$rlist['phone_number']."</td>";
		echo "<td >".$rlist['comments']."</td>";
		echo "</tr>";
		
	}
	

	echo "</table></div>";
	
?>



</head>

<body>
<br /> 
<br />
<p align="center">
<a onclick="window.close()" href="#">Fechar Janela</a>
</p>
</body>
</html>