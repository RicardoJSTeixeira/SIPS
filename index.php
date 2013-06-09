<!DOCTYPE html>
<html>
<head>
<title>SIPS Call-Center | Home Page</title> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
          
<?php    

    require("ini/dbconnect.php");
	

	
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];
	
	if (isset($_GET['logout'])) {
		
	               
			
		
		Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
		Header("HTTP/1.0 401 Unauthorized");
	?>
    
	You have now logged out. Thank you
	<script>
    $(function(){top.location='index.php';})
    </script>
	<?php
    unset($_GET['logout']);
	exit;                                  
			
	} else {
	
	if(empty($_SERVER['PHP_AUTH_USER'])) {
    header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
    header('HTTP/1.0 401 Unauthorized');
    exit; }  
	}

	$query="SELECT user_level FROM vicidial_users where user='$user' and pass='$pass'";
	$query=mysql_query($query,$link);
	$row=mysql_fetch_row($query);
	

$logout = $_GET['logout'];
  
if ($logout) { session_start();
session_unset();
session_destroy(); echo "<script> alert('entrou'); </script>"; }


require('sips-admin/dbconnect.php');
if(isset($_POST['first_login']))
{
	
	
	$username=$_POST['sips_username'];
	$password=$_POST['sips_password'];
	$query="SELECT user_level FROM vicidial_users where user='$username' and pass='$password'";
	$query=mysql_query($query,$link);
	$row=mysql_fetch_row($query);
	if(mysql_num_rows($query)==0){$navigation='fail';} elseif($row[0]>5) 
	{ 
	
	echo "
	<form id='adminlogin' action='sips-admin/index.php' method='post'>
	<input type='hidden' name='useradmin' value='$username' />
	<input type='hidden' name='passadmin' value='$password' />
	</form> 
	<script> document.getElementById(\"adminlogin\").submit(); </script>"; } else {
	echo "	
	<form id='agentlogin' action='sips-agente/agente.php' method='post'>
	<input type='hidden' name='sips_login' value='$username' />
	<input type='hidden' name='sips_pass' value='$password' />
	</form> 
	<script> document.getElementById(\"agentlogin\").submit(); </script>"; }
	
	
	
}
if(isset($_POST['reset_login'])){$navigation='';} 





if ($navigation=='') {	
echo "<center><img style='margin-top:150px; margin-bottom:32px;' src='images/pictures/go_logo_35.png'>";
echo "<div id=work-area style='width:40%; min-height:0px; min-width:500px'>";

echo "<div class=cc-mstyle style='border:none; margin-top:32px;'>";
echo "<form name='sips_login' id=sips_login action='index.php' method=POST>";
echo "<input type=hidden value=go name=first_login>";
echo "<table width=100%>";
echo "<tr>
<td style='min-width:150px'> <div class=cc-mstyle style='height:28px;'><p> Username </p></div></td>
<td><input type='text' name='sips_username' id='sips_username' style='width:200px' value=''></td>
</tr>";
echo "<tr><td style='min-width:150px'> <div class=cc-mstyle style='height:28px;'><p> Password </p></div></td>
<td><input type='password' name='sips_password' id='sips_password' style='width:200px'  value=''></td>";
echo "<td><input type=image src='images/icons/key_go_32.png' onclick='javascript:document.getElementById(\"sips_login\").submit();' value=submit></td><td>Log-in</td></tr>";
echo "</table><br><br></div></div>";
echo "</form>";	} 


if($navigation=='second_login') {
	
echo "<center>
<img style='margin-top:150px;' src='images/client/sipslogo_agentlog_default.png' />
<div id='work-area' style='width:40%; min-height:0px;'>
<br><br>
<div class='cc-mstyle' style='border:none'>
<table style='width:35%'>
<tr>
<td style='min-width:32px; cursor:pointer;' onclick=window.location='sips-agente/agente.php';><img style='float:right' src=images/icons/premium_support_32.png /></td>
<td style='min-width:60px; text-align:left; cursor:pointer;' onclick=window.location='sips-agente/agente.php';>Log-In como Operador</td>
</tr>
</table>
<br><br>
<table style='width:40%'>
<tr>
<td style='min-width:32px; cursor:pointer;' onclick=window.location='sips-admin/index.php';><img style='float:right' src=images/icons/cog_edit_32.png /></td>
<td style='min-width:65px; text-align:left; cursor:pointer;' onclick=window.location='sips-admin/index.php';>Log-In como Administrador</td>
</tr>
</table>
<br><br>
</div>
</div>
</center>";	
}
if($navigation=='fail')
{
echo "<center><img style='margin-top:150px; margin-bottom:32px;' src='images/pictures/go_logo_35.png'>";
echo "<div id=work-area style='width:40%; min-height:0px; min-width:500px'>";

echo "<div class=cc-mstyle style='border:none; margin-top:32px;'>";
echo "<form name=sips_login id=sips_login action=index.php method=POST>";
echo "<input type=hidden value=go name=reset_login>";
echo "<table width=100% border=0>";
echo "<tr>
<td style='min-width:150px'> Log In Errado </td>
";
echo "<tr><td>&nbsp;</td>
<tr><td>&nbsp;</td>";
echo "<tr><td>Tentar Novamente</td><td><a href='index.php'><img src='images/icons/key_go_32.png'></a></td>";
echo "</table><br><br></div></div>";
echo "</form>";
}	



echo "
	<script>
	document.getElementById(\"sips_username\").value = '$user';
	document.getElementById(\"sips_password\").value = '$pass';
	</script>
	";

	?>
	
</body>
</html>