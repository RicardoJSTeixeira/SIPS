<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Insere Colaborador</title>

<?php
	
	$nome = $_POST['nome'];
	$morada = $_POST['morada'];
	$porta = $_POST['porta'];
	$andar = $_POST['andar'];
	$codpostal = $_POST['codpostal'];
	$codrua = $_POST['codrua'];
	$local = $_POST['local'];
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
	$horario = $_POST['horario'];
	$expcc = $_POST['expcc'];
	$expvendas = $_POST['expvendas'];
	$expzon = $_POST['expzon'];
	$data = $_POST['data'];
	$user = $_POST['user'];
	$pass = $_POST['password'];
	
	
	if ($horario = '1317') { $iddep = 125; $horario = 'int'; } else { if ($horario = '1721') { $iddep = 126; $horario = 'noite'; } }
	
		
	if ($tlf == "") { $tlf = 0; }
	if ($tlml == "") { $tlml = 0; }
	if ($segsocial == "") { $segsocial = 0; }
	
	// ID INTEGRA
	$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	
	$nome = utf8_decode($nome);
	
	mysql_select_db("integra_bd_2", $con);
	
	$passmd5 = md5($pass);
	
	mysql_query("begin");
	
	mysql_query("INSERT INTO t_morada (Rua, Porta, Andar, Cod_Postal, Cod_Rua, Localidade, Facturar) VALUES ('$morada', '$porta', '$andar', $codpostal, $codrua, '$local', 0);") or die("morada".mysql_error());
	
	$lid = mysql_insert_id(); 
	
	mysql_query("INSERT INTO t_estrutura (ID_Tipo, ID_Dependente, ID_Morada, Nome, Activo, Data_Entrada, Contacto, Contact_Alt, Objectivo) VALUES (11, $iddep, $lid, '$nome', 1, '$data', $tlml, $tlf, 20);") or die("estrutura".mysql_error());
	
	$lid = mysql_insert_id();
		
	mysql_query("INSERT INTO t_colaborador (ID_Colaborador, BI, NIF, Seg_Social, Hab_Literarias, Carta_Conducao, Email, NIB,  ID_Tipo_Vendas) VALUES ('$lid', '$bi', '$nif',  '$segsocial', '$hablit', 0, '$email', '$nib', 1);") or die("colab".mysql_error());
	
	
		
	mysql_query("INSERT INTO t_utilizador (ID_Colaborador, Utilizador, Passwd_MD5) 
				VALUES ('$lid','$user', '$passmd5')") or die("user".mysql_error());
			
	if (mysql_error()) { mysql_query("rollback"); echo "rollback".mysql_error(); } else { mysql_query("commit");  }
	
	mysql_close($con);
	
	// user vicidial
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("asterisk", $con);
	
	mysql_query("INSERT INTO vicidial_users (
	user,
	pass,
	full_name,
	user_group,
	agentonly_callbacks,
	agentcall_manual,
	custom_one)
	VALUE
	('$user', '$pass', '$nome', 'ZON', '1', '1', '$user')
	") or die(mysql_error());
	
	
	// user SIPS
	
	mysql_select_db("sips", $con);
	
	$uservici = $user;
	
	$insercolab = mysql_query("INSERT INTO t_colaborador (
				nome,
				morada,
				codpostal,
				telefone,
				telemovel,
				email,
				bi,
				nif,
				segsocial,
				datanasc,
				hablit,
				banco,
				nib,
				estcivil,
				ndepend,
				htrab,
				ecc,
				evendas,
				ezon,
				idintegra,
				uservici,
				datainsc) VALUES
				
				('$nome',
				'$morada',
				'$codpostal',
				'$tlf',
				'$tlml',
				'$email',
				'$bi',
				'$nif',
				'$segsocial',
				'$datanasc',
				'$hablit',
				'$banco',
				'$nib',
				'$estcivil',
				'$dependentes',
				'$horario',
				'$expcc',
				'$expvendas',
				'$expzon',
				'$idintegra',
				'$uservici',
				'$data');") or die(mysql_error());

	mysql_close($con);

?>
<style type="text/css">
.td {
	font-family: Verdana, Geneva, sans-serif;
}
</style>
</head>

<body>
<table width="100%" align="center" class="td">
<tr><td align="center">Foi registado com sucesso! Feche esta Janela!</td></tr>
</table>
</body>
</html>