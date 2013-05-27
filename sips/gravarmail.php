<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<?php
$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	$data = date('Y-n-j');
	$user = $_POST['user'];
	$cli = $_POST['cli'];
	$email = $_POST['mail'];
	$pacote = $_POST['pacotes'];
	$obs = $_POST['obs'];
	
	
	
	
	mysql_query("INSERT INTO t_mail (user, cli, email, pacote, obs, data) VALUES ('$user', '$cli', '$email', '$pacote', '$obs', '$data')") or die (mysql_error());
	
	mysql_close($con);
	
?>
<script type="text/javascript">
window.close();

</script>

</head>

<body>
</body>
</html>