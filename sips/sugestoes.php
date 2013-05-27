<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Sugestões</title>
<style type="text/css">
.td {
	font-family: Arial, Helvetica, sans-serif;
}
</style>

<?php

	$ola = $_GET['user'];
	$user = $_POST['user'];
	
	
	if ($user != NULL) { 
	
	$sugest = $_POST['sugest'];
	$data = date('o-m-d');
	
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	mysql_query("INSERT INTO t_sugestoes (user, sugestao, data) VALUES ('$user', '$sugest', '$data')") or die(mysql_error());
	
	mysql_close($con);
	
	echo "<script type='text/javascript'>";
	echo "window.close()";
	echo "</script>";
 

	}
?>


</head>
<body>
<form target="_self" method="post" name="sugestoes">
<table width="600" border="0">
  <tr>
    <td colspan="2" align="center" class="td"><br />Dá-nos as tuas sugestões ou ideias! Vale tudo :)<br /><br /></td>
  </tr>
  <tr>
    <td colspan="2"><textarea name="sugest" cols="100" rows="10"></textarea></td>
  </tr>
  <tr>
    <td width="295" align="center"><input type="submit" name="submit" value="Enviar" /></td>
    <td width="295" align="center"><input type="button" name="cancelar" value="Fechar" onclick="window.close();" /><input type="hidden" name="user" value="<?php echo $ola; ?>" /></td>
  </tr>
</table>
</form>

</body>
</html>