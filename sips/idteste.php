<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
	<?php
	
	$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("integra_bd_2011", $con);
    $idint = mysql_query("SELECT ID_Colaborador FROM t_colaborador WHERE Nome = 'Margarida Sousa'") or die(mysql_error());
	$rid = mysql_fetch_assoc($idint);
	$idintegra = $rid['ID_Colaborador'];
	echo "Ola";
	echo $idintegra;
	?>
    
<body>
</body>
</html>