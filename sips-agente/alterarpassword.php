<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Alterar Password</title>

<link rel="stylesheet" type="text/css" href="../css/style.css" />

<?php

	$user = $_GET['user'];

	$con1 = mysql_connect("localhost","sipsadmin", "sipsps2012");
	
	if (!$con1)
	{ die('Não me consegui ligar' . mysql_error()); }
	mysql_select_db("asterisk", $con1);
	
	
	$curPass = mysql_query("SELECT pass from vicidial_users where user = '$user'") or die(mysql_error());
	
	$curPass = mysql_fetch_assoc($curPass);
	
	$newpass = $_POST['newpass_one'];
	
	if (isset($_POST['user'])) {
		
		$user = $_POST['user'];
		mysql_query("UPDATE vicidial_users set pass = '$newpass' WHERE user = '$user'") or die(mysql_error);	
		mysql_close($con1);
		echo "<script type='text/javascript'>
			alert('Password alterada com sucesso!');
			window.close();
			</script>";
		
	}
	
	mysql_close($con1);

?>



<script type="text/javascript">
function validaform(y) {
	
	var curPass = "<? echo $curPass['pass']; ?>";
	if (document.getElementById('oldpass').value != curPass) {
		alert('A password actual está errada');
		return false; } else {
			
	if (document.getElementById('newpass_one').value == "") { alert('Preencha a nova password'); return false; } else {		
	
	if (document.getElementById('newpass_one').value != document.getElementById('newpass_two').value) {
		
		alert('As passwords novas não correspondem');
		return false;	
		
	} else { return true; } } }
	
	
	
	
}


</script>


</head>

<body>
<br />

<form target="_self" action="alterarpassword.php" method="post" onsubmit="return validaform()" class="cc-mstyle">
<input type="hidden" name="user" value="<? echo $user; ?>" />

<table align="center" border="0">
<tr><td colspan="2">&nbsp;<br /><br /><br /><br /></td></tr>

	<tr><td>Insira a sua password actual:</td><td><input type="password" style="width:150px; height:22px" name='oldpass' id='oldpass' /></td></tr>
    <tr><td>Escolha a sua nova password:</td><td><input type="password" style="width:150px; height:22px" name='newpass_one' id='newpass_one' /></td></tr>
    <tr><td>Repita a sua nova password:</td><td><input type="password" style="width:150px; height:22px" name='newpass_two' id='newpass_two' /></td></tr>
	<tr><td colspan="2">&nbsp;<br /></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" value='Alterar' />&nbsp;&nbsp;<input type="reset" value="Cancelar" /></td></tr>
    
<tr><td colspan="2">&nbsp;<br /><br /><br /></td></tr>
</table>

</form>
</body>
</html>