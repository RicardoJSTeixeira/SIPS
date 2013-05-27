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

<?
 	$estado = $_POST['estado'];
	
	if ($estado == NULL) { $estado = 'activo'; };
	
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	if ($estado == 'activo') {
	$colab = mysql_query("SELECT * FROM t_colaborador WHERE activo = 1 ORDER BY htrab ASC") or die(mysql_error()); 
	$manha = mysql_query("SELECT * FROM t_colaborador WHERE activo = 1 AND htrab = 'int'") or die(mysql_error());
	$tarde = mysql_query("SELECT * FROM t_colaborador WHERE activo = 1 AND htrab = 'noite'") or die(mysql_error());
	$manha = mysql_num_rows($manha);
	$tarde = mysql_num_rows($tarde);
	
	} else {
		if ($estado == 'inactivo') {
			$colab = mysql_query("SELECT * FROM t_colaborador WHERE activo = 0 ORDER BY htrab ASC") or die(mysql_error());
			$manha = mysql_query("SELECT * FROM t_colaborador WHERE activo = 0 AND htrab = 'int'") or die(mysql_error());
	$tarde = mysql_query("SELECT * FROM t_colaborador WHERE activo = 0 AND htrab = 'noite'") or die(mysql_error());
	$manha = mysql_num_rows($manha);
	$tarde = mysql_num_rows($tarde); }
		else { 

				$colab = mysql_query("SELECT * FROM t_colaborador ORDER BY htrab ASC") or die(mysql_error());
				$manha = mysql_query("SELECT * FROM t_colaborador WHERE htrab = 'int'") or die(mysql_error());
				$tarde = mysql_query("SELECT * FROM t_colaborador WHERE htrab = 'noite'") or die(mysql_error());
				$manha = mysql_num_rows($manha);
				$tarde = mysql_num_rows($tarde); } 
			
		}
mysql_close($con);

?>

</head>

<body>
<table align="center" width="100%" border="1">
<tr valign="top">
    <td width="192">

    <iframe width="186" height="600" src="menu.html" frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0">
    </iframe> 
   </td>
   <td width="100%" align="left">

   <form name="festado" action="listacolab.php" target="_self" method="post">
   <table align="center" width="100%">
   <tr>
   		<td align="center" class="header">Estado: 
   		  <input type="radio" name="estado" value="activo" <? if ($estado == 'activo') { echo "checked='checked'"; } ?>>
   		  Activos</input>
   		  <input type="radio" name="estado" value="inactivo" <? if ($estado == 'inactivo') { echo "checked='checked'"; } ?>>Inactivos</input>
   		  <input type="radio" name="estado"  value="todos" <? if ($estado == 'todos') { echo "checked='checked'"; } ?>>Todos</input> 		  
		</td>
   </tr>
   <tr>
   		<td align="center"><input type="submit" value="Submeter" /></td>
   </tr>
   
   </table>
   </form>
   <?php
  
	
	echo "<table align='center' border='1' cellpadding='5'><tr>
		<td align='center' bgcolor='#9999CC' class='header'>Nome Colaborador</td>
		<td align='center' bgcolor='#9999CC' class='header'>Username</td>
		<td align='center' bgcolor='#9999CC' class='header'>Horário</td>
		<td align='center' bgcolor='#9999CC' class='header'>Telemovel</td>
		<td align='center' bgcolor='#9999CC' class='header'>Telefone</td>
		<td align='center' bgcolor='#9999CC' class='header'>Data Entrada</td>
		<td align='center' bgcolor='#9999CC' class='header'>Acções</td>
		</tr>
		<tr><td colspan='7' align='center' class='header'><br>Turno da Manhã: ".$manha." Operadores</td></tr>
		
	";
	
	$flag = false;
	
	for ($i=0;$i<mysql_num_rows($colab);$i++){
		$rcolab = mysql_fetch_assoc($colab);
		
		if ($rcolab['htrab'] == 'noite' && $flag == false ) { echo "<tr><td colspan='7' align='center' class='header'><br>Turno da Noite: ".$tarde." Operadores</td></tr>"; $flag = true; }

		if ($rcolab['htrab'] == 'int') { $htrab = '13h-17h'; } else { $htrab = '17h-21h'; }
		
		$hoje = explode('-', date('o-n-d'));
		$dataInicio = explode('-', $rcolab['datainsc']);
		
		$difDias = (intval($hoje[2]) - intval($dataInicio[2]));
		$difMes = (intval($hoje[1]) - intval($dataInicio[1]));
		
		$colabExp = "";
		
		if ($difMes < 1) { $colabExp = "bgcolor='#FFFF99'"; }
		if ($difMes == 1 &&  $difDias < 0) { $colabExp = "bgcolor='#FFFF99'"; }
		if ($difMes == 5 || $difMes == 11 || $difMes == 17) { $colabExp ="bgcolor='#339966'"; }
		
		
		echo "<tr ".$colabExp.">";
		echo "<td align='center' >".utf8_encode($rcolab['nome'])."</td>";
		echo "<td align='center'>".$rcolab['uservici']."</td>";
		echo "<td align='center'>".$htrab."</td>";
		echo "<td align='center'>".$rcolab['telemovel']."</td>";
		echo "<td align='center'>".$rcolab['telefone']."</td>";
		
		echo "<td align='center'>".$rcolab['datainsc']."</td>";

		
		echo "<td align='center'>";
		echo "<a href='contratotrabalho.php?id=".$rcolab['uservici']."' target='_new' >Contrato Trabalho</a>";
		echo "<br>";
		echo "<a href='editacolab.php?user=".$rcolab['uservici']."' target='_new' >Ficha Inscrição</a>";
		echo "<br>";
		echo "<a href='presencas.php?user=".$rcolab['uservici']."' target='_self' >Faltas</a>";
		echo "</td>";
		echo "</tr>";
		
	}
	echo "</table>";
	
	
?>
   
   </td>
</tr>
</table>

</body>
</html>
