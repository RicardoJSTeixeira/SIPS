<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Upload</title>
</head>

<body>

<?php
$descr = $_POST['descr'];

$uploaddir = 'formacoes/';
$uploadfile = $uploaddir . $_FILES['userfile']['name'];
print "<pre>";
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir . $_FILES['userfile']['name'])) {
    print "O arquivo é valido e foi carregado com sucesso. Aqui esta alguma informação:\n";
    print_r($_FILES);
	
	echo "<br><br><br>
		<a href='http://sips.dyndns.info/sips/upload.php' target='_self'>Voltar</a>";
	
} else {
    print "Possivel ataque de upload! Aqui esta alguma informação:\n";
    print_r($_FILES);
	
}
print "</pre>";

$nome = $_FILES['userfile']['name'];

$data = date('j-n-Y');

$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	mysql_query("INSERT INTO t_formacoes (nome, path, descr, activo) VALUES ('$nome', '$uploadfile', '$descr', '1');") or die(mysql_error());

mysql_close($con);
?>

</body>
</html>