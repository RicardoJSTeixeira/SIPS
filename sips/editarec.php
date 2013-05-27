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
    	<table align="center">
        	<tr>
            	<td><label>Nome:</label></td>
            	<td><input type="text" name="nome" value="<?php echo $nome; ?>" /></td>
                <td><label>Contacto:</label></td>
            	<td><input type="text" name="tlml" value="<?php echo $tlml; ?>" /></td>
            </tr>
            <tr>
            	<td><label>E-Mail:</label></td>
            	<td><input type="email" name="email" value="<?php echo $email; ?>" /></td>
                <td><label>Idade:</label></td>
            	<td><input type="text" name="idade" value="<?php echo $idade; ?>" /></td>
            </tr>
            <tr>
            	<td><label>Localidade:</label></td>
            	<td><input type="text" name="localidade" value="<?php echo $localidade; ?>" /></td>
                <td><label>Nacionalidade:</label></td>
            	<td><input type="text" name="nacionalidade" value="<?php echo $nacionalidade; ?>" /></td>
            </tr>
            <tr>
            	<td colspan="2"><label>Data Insc:</label></td>
            	<td colspan="2"><input type="text" name="data" value="<?php echo $data; ?>" /></td>
                
            </tr>
            
            <tr>
            	
                <td><label>Data Entrev:</label></td>
            	<td><input type="text" name="dataent" value="<?php echo $dataent; ?>" /></td>
                <td><label>Hora Entrev:</label></td>
            	<td><input type="text" name="horaent" value="<?php echo $horaent; ?>" /></td>
            </tr>
            
            <tr>
            	
                <td><label>Nota Tlf:</label></td>
            	<td><input type="text" name="notatlf" value="<?php echo $notatlf; ?>" /></td>
                <td><label>Nota Presencial:</label></td>
            	<td><input type="text" name="notapres" value="<?php echo $notapres; ?>" /></td>
            </tr>
            
            <tr>
            	
                <td colspan="2"><label>Horário</label></td>
            	<td colspan="2"><select>
                		<option value="13/17">13 - 17h</option>
                        <option value="17/21">17 - 21h</option>
                    </select>
                </td>
                
            </tr>
            
            <tr>
            	<td colspan="4"><textarea name="obs" cols="70" rows="10"></textarea>
            </tr>
        </table>
   	</td>
</tr>
</table>

</body>
</html>
