<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<?php
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	if ( is_array($_POST['tvnetvoz']) ) { 
	$_POST['tvnetvoz'] = implode(',', $_POST['tvnetvoz']); 
	}
	
	if ($_POST['tvnetvoz'] == "") { $_POST['tvnetvoz'] = '---'; }
	
	$tcli = $_POST['tcli'];
	
	$nome = $_POST['nome'];
	$morada = $_POST['morada'];
	$tlf = $_POST['tlf'];
	$email = $_POST['email'];
	$bi = $_POST['bi'];
	$codpostal = $_POST['codpostal'];
	$tlml = $_POST['tlml'];
	$nif = $_POST['nif'];
	$dcontacto = $_POST['dcontacto'];
	$opactual = $_POST['opactual'];
	$satisf = $_POST['satisf'];
	$tecnologia = $_POST['tecnologia'];
	$tvnetvoz = $_POST['tvnetvoz'];
	$preco = $_POST['preco'];
	$fid = $_POST['fid'];
	$fidmes = $_POST['fidmes'];
	$fidano = $_POST['fidano'];
	$coment = $_POST['coment'];
	$resultvenda = $_POST['resultvenda'];
	$coment_log = $_POST['coment_log'];
	$resultcham = $_POST['resultvenda'];
	$user = $_POST['user'];
	$tmorada = $_POST['tmorada'];
	
	$tcontacto = $_POST['tcontacto'];
	$resultado = $_POST['resultado'];
	$mrecusa  = utf8_decode($_POST['mrecusa']);
	$venda = $_POST['venda'];
	
	$data = date('l jS \of F Y h:i:s A');
	if ($coment !== "") { $coment = '*******  '.$data.'  *******'.'\n\n'.$user.': '.$coment; 
	$coment = $coment.'\n\n'.$coment_log;
	} else { $coment = $coment_log; }
	
	
	
	$data = date("y-m-d");
	
	if ($tcli == 'true') {
	$sqllead=mysql_query("UPDATE t_leads SET 
	Nome='$nome', 
	Morada='$morada', 
	CodPostal='$codpostal', 
	BI='$bi', 
	Nif='$nif', 
	Tlf='$tlf', 
	Tlml='$tlml',
	Email='$email', 
	tvnetvoz='$tvnetvoz', 
	Tecnologia='$tecnologia', 
	PPaga='$preco', 
	OPActual='$opactual', 
	Satisf='$satisf', 
	DContacto='$data',
	Coment='$coment', 
	Fid='$fid',
	FidAno='$fidano',
	FidMes='$fidmes',
	ResultCham='$resultcham',
	tmorada='$tmorada',
	user='$user',
	tcontacto = '$tcontacto',
	resultado = '$resultado',
	mrecusa = '$mrecusa',
	venda = '$venda' 
	WHERE Tlf='$tlf'") or die("erro na inserção:".mysql_error());
	} else {
	
	$sqllead=mysql_query("INSERT INTO t_leads 
	(nome, 
	morada,
	codpostal,
	bi,
	nif,
	tlf,
	tlml,
	email,
	tvnetvoz,
	tecnologia,
	ppaga,
	opactual,
	satisf,
	dcontacto,
	coment,
	fid,
	fidano,
	fidmes,
	resultcham,
	tmorada,
	user,
	tcontacto,
	resultado,
	mrecusa,
	venda
	)
	
	VALUES (
		 
	'$nome', 
	'$morada', 
	'$codpostal', 
	'$bi', 
	'$nif', 
	'$tlf', 
	'$tlml',
	'$email', 
	'$tvnetvoz', 
	'$tecnologia', 
	'$preco', 
	'$opactual', 
	'$satisf', 
	'$data',
	'$coment', 
	'$fid',
	'$fidano',
	'$fidmes',
	'$resultcham',
	'$tmorada',
	'$user',
	'$tcontacto',
	'$resultado',
	'$mrecusa',
	'$venda'
	)") or die("erro na inserção:".mysql_error());

	}
	
	
	
	
	
	mysql_close($con);
	
	
?>
<script type="text/javascript">
window.close()
</script>

</head>

<body>
<? echo $nome;
echo $morada; ?>
Feche esta janela
</body>
</html>