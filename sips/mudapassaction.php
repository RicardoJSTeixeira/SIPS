<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
	$user = $_POST["user"];
	$oldpass = $_POST["oldpass"];
	$pass1 = $_POST["pass1"];
	$pass2 = $_POST["pass2"];
	
	$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("integra_bd_2011", $con);
	
	$users = mysql_query("SELECT * FROM t_utilizador WHERE Utilizador = '$user'")or die (mysql_error());
	
	$rusers = mysql_fetch_assoc($users);
	$passmd5 = md5($oldpass);
	
	
	
	if ($rusers['Passwd_MD5']!= $passmd5) { echo("User/Pass errados, volte atrás e tente outra vez!"); } else {
	
	if ($pass1 != $pass2) { echo("As duas novas passwords não coincidem, volte atrás e tente outra vez!"); } else { 
	$passmd5 = md5($pass1);
	mysql_query("UPDATE t_utilizador SET Passwd_MD5 = '$passmd5' WHERE Utilizador='$user'") or die (mysql_error());
	echo "password actualizada com sucesso!";
	}
	}
	
	mysql_close($con);

?>


</head>

<body>
<br /> 
<br />
<a href="javascript:window.close()">Fechar Janela</a>
</body>
</html>