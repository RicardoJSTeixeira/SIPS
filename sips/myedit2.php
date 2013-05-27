<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Edição de Colaborador</title>


<?php
	
	$user = $_POST['user'];
	$nome = utf8_decode($_POST['nome']);
	$morada = $_POST['morada'];
	$codpostal = $_POST['codpostal'];
	$tlf = $_POST['tlf'];
	$tlml = $_POST['tlml'];
	$email = $_POST['email'];
	$bi = $_POST['bi'];
	$nif = $_POST['nif'];
	$segsocial = $_POST['segsocial'];
	$datanasc = $_POST['datanasc'];
	$hablit = $_POST['hablit'];
	$banco = $_POST['banco'];
	$nib = $_POST['nib'];
	$estcivil = $_POST['estcivil'];
	$dependentes = $_POST['dependentes'];
	
	
	
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	$upuser = mysql_query("UPDATE t_colaborador SET
	nome = '".$nome."',
	morada = '".$morada."',
	codpostal = '$codpostal',
	telefone = '$tlf',
	telemovel = '$tlml',
	email = '$email',
	bi = '$bi',
	nif = '$nif',
	segsocial = '$segsocial',
	datanasc  = '$datanasc',
	hablit = '$hablit',
	banco = '".$banco."',
	nib = '".$nib."',
	estcivil = '$estcivil',
	ndepend = '$dependentes'
	WHERE uservici = '$user'") or die(mysql_error());
	
	
	

	
	
	mysql_close($con);
	
?>

<script type="text/javascript">
	alert("A sua informação foi editada c/ sucesso");
	window.close();

</script>

</head>

<body>
</body>
</html>