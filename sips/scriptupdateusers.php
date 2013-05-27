<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<?
	$con = mysql_connect("localhost","root","admin");
			if (!$con)
			{
				die('Não me consegui ligar' . mysql_error());
			}
			mysql_select_db("sips", $con);


			$query = mysql_query("SELECT uservici, activo FROM t_colaborador where activo = 0") or die(mysql_error());
			
			$con = mysql_connect("serverintegra.dyndns.org","integra","admin");
			if (!$con)
			{
				die('Não me consegui ligar' . mysql_error());
			}
			mysql_select_db("integra_bd_2", $con);
			
			

			for ($i=0; $i<mysql_num_rows($query); $i++) {
				

				$l = mysql_fetch_assoc($query);

				
				$user = mysql_query("SELECT ID_Colaborador from t_utilizador WHERE Utilizador = '$l[uservici]'") or die(mysql_error()); 
				$b = mysql_fetch_assoc($user);
				mysql_query("UPDATE t_estrutura SET Activo = 0 WHERE ID_Estrutura = '$b[ID_Colaborador]'") or die(mysql_error());
				


				$a = $a + 1; 
				
			}

		mysql_close($con);
			echo "pos inactivo ".$a." linhas <br><br>";
			
			
	




?>
</head>

<body>
</body>
</html>