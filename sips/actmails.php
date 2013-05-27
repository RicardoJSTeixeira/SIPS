<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Backoffice</title>




<?php 

        $con = mysql_connect("localhost","root","admin");
		if (!$con)
		{
			die('Não me consegui ligar' . mysql_error());
		}
		mysql_select_db("sips", $con);
	
	$actualiza = $_POST["actualiza"];
	for ($i=0;$i<count($actualiza);$i++) {
		
		$indice = (int)$actualiza[$i];
		mysql_query("UPDATE t_mail SET enviado = '1' WHERE id =$indice ") or die(mysql_error());
	}
		
		
		
		mysql_close($con);
		
		
		echo "<script type='text/javascript'>
				window.open('http://sips.dyndns.info/sips/mails.php', '_self');
			  </script>"; 	
	

?>
</head>
<body>

</body>
</html>