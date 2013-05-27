<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Backoffice</title>
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
	border-bottom:thin 1px #000;
	
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
</head>

<body>
<table align="center" width="100%" border="1">
<tr valign="top">
    <td width="186" valign="top">
        <iframe width="186" height="600" src="menu.html" frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0">
    </iframe> 
   </td>
   <td width="100%" align="left">
   <form name="actualizamails" action="actmails.php" method="post" >
   		<?php
        $con = mysql_connect("localhost","sipsadmin", "sipsps2012");
		if (!$con)
		{
			die('Não me consegui ligar' . mysql_error());
		}
		mysql_select_db("sips", $con);
		
		$mails = mysql_query("SELECT * FROM t_mail WHERE enviado = '0'") or die('Erro no query' . mysql_error());;
        
			echo ("<table align='center' border='0' cellpadding='1' cellspacing='4'>
				 <tr>
				 	<td bgcolor='#FFFFCC'>Utilizador</td>
					<td bgcolor='#FFFFCC'>Nome Cliente</td>
					<td bgcolor='#FFFFCC'>E-Mail</td>
					<td bgcolor='#FFFFCC'>Pacote</td>
					<td bgcolor='#FFFFCC'>Observações</td>
					<td bgcolor='#FFFFCC'>Data</td>
					<td bgcolor='#FFFFCC'>Enviado</td>
					
				</tr>");
				
		$pintor = true;
		for ($i=0;$i<mysql_num_rows($mails);$i++){
			$rmails = mysql_fetch_assoc($mails);
			if ($pintor) { echo "<tr style='border:thin'>"; $pintor = false; } else { echo "<tr style='border:thin' bgcolor='#9999FF'>"; $pintor = true; };
			
			echo ("
				  <td>".$rmails['user']."</td>
				  <td>".$rmails['cli']."</td>
				  <td>".$rmails['email']."</td>
				  <td>".$rmails['pacote']."</td>
				  <td>".$rmails['obs']."</td>
				  <td>".$rmails['data']."</td>
				  <td><input type='checkbox' name='actualiza[]' value=".$rmails['id']."></td>
				  
				  </tr><tr><td colspan='7'><br></td></tr>");
		}
			echo ("</tr><tr><td colspan='7'><input type='submit' value='Actualizar' /></td></tr>
				  </table>");
		
		mysql_close($con);		  
        ?>
        

</form>
</body>
</html>
