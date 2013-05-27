<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<?
	$con = mysql_connect("localhost","root","admin");
	
	if (!$con)
	{ die('Não me consegui ligar' . mysql_error()); }
	mysql_select_db("sips", $con);

	$dia = 1;
	$mes = 1;
	$ano = 2010;

	for ($i=0; $i<1500; $i++) {
		
		$data = $ano."-".$mes."-".$dia;
		
		$chckDia = date("l", mktime(0, 0, 0, $mes, $dia, $ano));
		
		
		
		if ($chckDia == "Sunday" || $chckDia == "Saturday") {
			mysql_query("INSERT INTO t_calendario (data, fds) VALUES ('$data', 1)") or die(mysql_error());
			
		} else { mysql_query("INSERT INTO t_calendario (data) VALUES ('$data')") or die(mysql_error()); }
		
		
		echo $data."   ".$chckDia."<br>";
		
		$dia = $dia + 1;
		
		
		if ($dia == 32) { $dia = 1; $mes = $mes + 1; }
		if ($mes == 13) { $mes = 1; $ano = $ano + 1; }
		
		
	} mysql_close($con);

	
	
?>
</head>

<body>
</body>
</html>