<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Operador Call-Center</title>


<script type="text/javascript">

function sugestoes(valor)
{
	var url = valor;
	window.open(url, "Janela", "status=no, width=900, height=350, menubar =no, titlebar=no, scrollbars=yes")
}

function vendas(user)
{
	var url = "../../sips/listaclientes.php?user=" + user;
	window.open (url, "status=yes, menubar =yes, titlebar=yes, scrollbars=yes");
}


function mail(user)
{
	var url = "../../sips/enviamail.php?user=" + user;
	window.open (url,"Janela","status=no, menubar =no, titlebar=yes, scrollbars=no, width=850, height=350");
	
}

function ultimoscontactos(valor)
{
	var url = valor;
	window.open(url, "Janela", "status=no, width=550, height=700, menubar =no, titlebar=no, scrollbars=yes")
}



	
</script>

<?php
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$pc = $_POST['pc'];
	
	
	
	
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
		$trick = $pc;
	
		$phoneget = mysql_query("SELECT tlf FROM t_ref WHERE pc = '$trick'") or die(mysql_error());
		$phone = mysql_fetch_assoc($phoneget);
		
	mysql_select_db("asterisk", $con);

		$campanha = mysql_query("SELECT user_group FROM vicidial_users WHERE user = '$user'") or die(mysql_error());
		
		$rcamp = mysql_fetch_assoc($campanha);	
	
	mysql_close($con);
	
	$ppass = "goautodial";
	$camp = 'ZON001';
	
	$viciurl = "/sipsproject/sips-agente/agente.php?VD_login=".$user."&VD_pass=".$pass."&phone_login=".$phone['tlf']."&phone_pass=".$ppass."&VD_campaign=".$camp;
	

?>
<style type="text/css">

body label {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
body label.header {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 20px;
	color:#006;
}

body td.header {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	color:#006;
}

body td {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 10px;
	text-align:center;
}
span{
cursor:pointer;
color:white;
background:#09F;
font-size:26px;
font:"Courier New", Courier, monospace
}
counter{
	font-size:24px;
}



input.bbuttons
{
   font-size:16px;
   width:80px;
   height:80px;
   white-space:normal;
}

input.abuttons
{
   background-color:#99CC66;
  
   width:130px;
   height:40px;
   border:2px;
   
	border-left: solid 2px #c3f83a;
	border-top: solid 2px #c3f83a;
	border-right: solid 2px #82a528;
	border-bottom: solid 2px #58701b;
}




textomaior {
	font-size: 16px;
}
textogrande {
	font-size: 16px;
}
textogrande {
	font-size: 18px;
}

</style>
<script type="text/javascript">
var cookies = document.cookie.split(";");
		
					for (var i = 0; i < cookies.length; i++) {
					var cookie = cookies[i];
					var eqPos = cookie.indexOf("=");
					var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
					document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
			
					}
</script>
</head>
<!--""-->
<body onload="clearcookies();">
<table align="center" width="100%" border="0">
<tr>
    
   <td width="100%" align="left">
  
   <iframe name="vici" align="left" width="100%" height="1000px" style='overflow:none; border:none;' src="<? echo $viciurl; ?>" />
   
   
   
   </td>
</tr>
</table>

</body>
</html>
