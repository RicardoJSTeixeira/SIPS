<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Controlo de Charge-Back</title>

<?
$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);

$numrows = $_POST['numrows'];

$a = 0;

for ($i=0;$i<$numrows;$i++) {
	
	
	
$idc = $_POST['casa_'.$i]; 
$nome = $_POST['nome_'.$i];
$cserv = $_POST['cserv_'.$i];
$ecob = $_POST['ecob_'.$i]; 
$valor = $_POST['valor_'.$i];
$nfact = $_POST['nf_'.$i];
$inact = $_POST['in_'.$i];
$data = date('o-m-d');



$checker = mysql_query("SELECT * FROM t_chargeback WHERE id = $idc") or die(mysql_error());



if ($inact != NULL) { $inact = TRUE; } else { $inact = FALSE; }



if (mysql_num_rows($checker) != 0) {
	$a = $a+1;
	mysql_query("UPDATE t_chargeback SET
	
	estado = '$ecob', 
	valor = '$valor', 
	facturas = '$nfact',
	inactivo = '$inact', 
	data = '$data'
	WHERE
	id = $idc ") or die("sitio1".mysql_error()); 
	 

	 
	 } else {
	
	if ($ecob != '---') {
	mysql_query("INSERT INTO t_chargeback 
	(id, 
	colab, 
	conta_servico, 
	estado, 
	valor, 
	facturas, 
	inactivo, 
	data) 
	
	VALUES 
	
	($idc, 
	'$nome', 
	'$cserv', 
	'$ecob', 
	'$valor', 
	'$nfact', 
	'$inact', 
	'$data')") or die(mysql_error()); 
	 
	 $a = $a+1;
	 
	 }
	 }
	
	
}


echo "<script type='text/javascript'>alert('Nº de contratos actualizados: ".$a."');</script>";




mysql_close($con);

?>

<script type="text/javascript">
	window.location = 'chargeback.php';

</script>

</head>

<body>



</body>
</html>