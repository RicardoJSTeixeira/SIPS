<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Fecho Mensal</title>

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



tr.linha:hover { background-color:#66FF00; }

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


</head>

<body>
<table align="center" width="100%" border="1">
<tr valign="top">
    <td width="186">
        <table align="left" width="26%" border="1">
        <tr>
        	<td><a href="backoffice.html"><img src="img/sipslogo.jpg" width="178" height="67" /></a></td>
        </tr>    
        <tr>
            <td align="center"><br /><br /><br /><a href="mails.php" >Envio de e-mails</a></td>
        </tr>
        <tr>
            <td align="center"><br /><a href="envelopes.html" >Envio de cartas</a></td>
        
        </tr>
        <tr>
            <td align="center"><br /><a href="pbi.php" >Ferramenta PBI</a></td>
        
        </tr>
        <tr>
            <td align="center"><br /><a href="faltas.php" >Faltas e Pausas</a></td>
        
        </tr>
        <tr>
            <td align="center"><br /><a href="estatisticas.php" >Estatisticas</a></td>
        
        </tr>
        <tr>
            <td align="center"><br /><a href="listacolab.php" >Colaboradores</a></td>
        
        </tr>
        <tr>
            <td align="center"><br /><a href="fecho.php" >Fecho Mensal</a></td>
        
        </tr>
        <tr>
            <td align="center"><br /><a href="upload.php" >Upload de Formações</a></td>
        
        </tr>
        </table>
   </td>
   <td width="100%" align="left">
    	<?php
			
			$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
				if (!$con)
				{
					die('Não me consegui ligar' . mysql_error());
				}
				mysql_select_db("sips", $con);
				
				
				$rec = mysql_query("SELECT * FROM t_recruta WHERE processado = '0'");
				
				echo "<form name='actualizar' action='actent.php' method='post'>
						<table align='center'>
						<tr>
							<td>Nome</td>
							<td>Contacto</td>
							<td>Data Entr.</td>
							<td>Hora Entr.</td>
							<td>Nota Tlf</td>
							<td>Nº Tentativas</td>
							<td>E-Mail</td>
							<td>Localidade</td>
							<td>Nacionalidade</td>
							<td>Idade</td>
							<td>Data</td>
							<td>Proc.</td>
							<td>Apagar<td>
						</tr>";
						
				for($i=0;$i<mysql_num_rows($rec);$i++) {
					$candidato = mysql_fetch_assoc($rec);
					
					echo "
						<tr class='linha' >
							<td><a href='editarec.php?id=".$candidato['id']."' target='_self'>".utf8_encode($candidato['nome'])."</a></td>
							<td>".$candidato['tlml']."</td>
							<td><input type='text' size='8' name='dataent' readonly='readonly'  /></td>
							<td><input type='text' size='3' name='horaent'  readonly='readonly' /></td>
							<td><input type='text' size='3' name='notatlf'  readonly='readonly' /></td>
							<td><input type='text' size='3' name='tentativas' readonly='readonly' /></td>
							<td>".$candidato['email']."</td>
							<td>".$candidato['localidade']."</td>
							<td>".$candidato['nacionalidade']."</td>
							<td>".$candidato['idade']."</td>
							<td>".$candidato['data']."</td>
							<td><input type='checkbox' name='proc[]' value='".$candidato['id']."'/></td>
							<td><input type='checkbox' name='apagar[]' value='".$candidato['id']."'</td> 
						</tr>";
				}
				
				echo "<tr>
						<td colspan='13' align='center'>
							<input type='submit' value='Actualizar' />
						</td>
					  </tr>
					  </table>
					  </form>";										
				
			mysql_close($con);
		?>
   	</td>
</tr>
</table>

</body>
</html>
