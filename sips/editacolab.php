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
<?php
	
	$user = $_GET['user'];
	
	
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	$dbuser = mysql_query("SELECT * FROM t_colaborador WHERE uservici='$user'") or die(mysql_error());
	
	$a = mysql_fetch_assoc($dbuser);
	
	$nome = utf8_encode($a['nome']);
	$morada = utf8_encode($a['morada']);
	$codpostal = $a['codpostal'];
	$tlf = $a['telefone'];
	$tlml = $a['telemovel'];
	$email = $a['email'];
	$bi = $a['bi'];
	$nif = $a['nif'];
	$segsocial = $a['segsocial'];
	$datanasc = $a['datanasc'];
	$hablit = $a['hablit'];
	$banco = $a['banco'];
	$nib = $a['nib'];
	$estcivil = $a['estcivil'];
	$dependentes = $a['ndepend'];
	$act = $a['activo'];
	$data = $a['datainsc'];
	$turno = $a['htrab'];
	$cav = $a['cav'];
	$datasaida = $a['datasaida'];
	
	
	mysql_close($con);
	
?>


</head>

<body>
<table align="center" width="100%" border="1">
<tr>
   
   <td width="100%" align="left">
   <form action="editacolab2.php" name="editacolab" id="editacolab" method="post" target="_self" >
<table width="610" align="center" id="main_site" border="1">
<tr>
<td width="600" height="80">
  <table width="600" align="center">
    
    <tr align="center">
      <td colspan="4" align="center"><label class="header">Dados Pessoais</label><hr />
      <input type="hidden" name="user" value="<? echo $user; ?>" />
      
      </td>
      </tr>
    <tr>
      <td width="91" align="center"><label>Nome:</label></td><td colspan="3" align="left"><input size="50" type="text" name="nome" id="nome" value="<?php echo $nome; ?>"/>
        </td>
      </tr>
    <tr>
      <td height="25" align="center"><label>Morada:</label></td><td colspan="2" align="left"><input size="50" type="text" name="morada" id="morada" value="<?php echo $morada ?>" />
        </label></td>
      <td align="left"><input type="text" name="codpostal" id="codpostal" size="30" value="<?php echo $codpostal ?>" /></td>
      </tr>
    
    <tr>
      <td align="center"><label>Telefone:</label></td><td width="158" align="left"><input size="20
    " type="text" name="tlf" id="tlf" value="<?php echo $tlf ?>" /> </td>
      <td width="65" align="center">Telemovel:</td>
      <td width="266" align="left"><input size="20" type="text" name="tlml" id="tlml" value="<?php echo $tlml ?>" /></td>
      </tr>
    <tr>
      <td align="center">E-mail:</td>
      <td align="left">
        <input name="email" type="text" id="email" value="<?php echo $email ?>" /></td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      </tr>
    <tr>
      <td align="center">BI:</td>
      <td align="left"><label for="bi"></label>
        <input type="text" name="bi" id="bi" value="<?php echo $bi ?>" /></td>
      <td align="center">NIF:</td>
      <td align="left">
        <input type="text" name="nif" id="nif" value="<?php echo $nif ?>" /></td>
      </tr>
    <tr>
      <td align="center">Segurança Social:</td>
      <td align="left">
        <input type="text" name="segsocial" id="segsocial" value="<?php echo $segsocial ?>" /></td>
      <td align="center">Data nascimento</td>
      <td align="left">
        <input type="text" name="datanasc" id="datanasc" value="<?php echo $datanasc ?>"/></td>
      </tr>
    <tr>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      </tr>
    <tr>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      </tr>
    <tr align="center">
      <td colspan="4" align="center"><label class="header">Outras Informações<br />
        </label><Hr /></td>
      </tr>
    <tr>
      <td colspan="2" align="center">Habilitações Literárias:</td>
      <td colspan="2" align="center">
        <select name="hablit" id="hablit">
          <option <?php if ($hablit == '---') { echo 'selected=selected'; } ?>value="---">---</option>
          <option <?php if ($hablit == 'menos9') { echo 'selected=selected'; } ?> value="menos9">Inferior 9º Ano</option>
          <option <?php if ($hablit == '9ano') { echo 'selected=selected'; } ?> value="9ano">9º Ano</option>
          <option <?php if ($hablit == '12ano') { echo 'selected=selected'; } ?> value="12ano">12º Ano</option>
          <option <?php if ($hablit == 'Lic') { echo 'selected=selected'; } ?> value="Lic">Licenciatura</option>
          <option  <?php if ($hablit == 'Mestre') { echo 'selected=selected'; } ?> value="Mestre">Mestrado</option>
          </select></td>
      </tr>
    <tr>
      <td colspan="2" align="center">Banco:</td>
      <td colspan="2" align="center"><p>
        
        <input name="banco" type="text" id="banco" size="40" value="<?php echo $banco ?>" />
        <br />
        </p></td>
      </tr>
    <tr>
      <td colspan="2" align="center">NIB:</td>
      <td colspan="2" align="center">
        <input name="nib" type="text" id="nib" size="40" value="<?php echo $nib ?>"/></td>
      </tr>
    <tr>
      <td colspan="2" align="center">Estado Civil:</td>
      <td colspan="2" align="center">
        <select name="estcivil" id="estcivil">
          <option <?php if ($estcivil == '---') { echo 'selected=selected'; } ?> value="---">---</option>
          <option <?php if ($estcivil == 'Solteiro(a)') { echo 'selected=selected'; } ?> value="Solteiro(a)">Solteiro(a)</option>
          <option <?php if ($estcivil == 'Casado(a)') { echo 'selected=selected'; } ?> value="Casado(a)">Casado(a)</option>
          <option <?php if ($estcivil == 'União Facto') { echo 'selected=selected'; } ?> value="União Facto">União Facto</option>
          <option <?php if ($estcivil == 'Divorciado(a)') { echo 'selected=selected'; } ?> value="Divorciado(a)">Divorciado(a)</option>
          <option  <?php if ($estcivil == 'Viúvo(a)') { echo 'selected=selected'; } ?>value="Viúvo(a)">Viúvo(a)</option>
          </select></td>
      </tr>
    <tr>
      <td colspan="2" align="center"><p>Nº de dependentes:<br />
        </p>        </td>
      <td colspan="2" align="center">
        <input name="dependentes" type="text" id="dependentes" value="<?php echo $dependentes ?>" size="5"/></td>
      </tr>
      <tr>
      <td colspan="2" align="center"><p>Valor Remuneração:<br />
        </p>        </td>
      <td colspan="2" align="center">
        <input name="valor" type="text" id="valor" value="242,5€" /></td>
      </tr>
    <tr>
      <td colspan="2" align="center">
        Data Entrada:
        </td>
       <td colspan="2" align="center">
       	<input type="text" name="dentrada" value="<? echo $data ?>" />
       </td> 
      </tr>
      <tr>
      <td colspan="2" align="center">
        Data Saida:
        </td>
       <td colspan="2" align="center">
       	<input type="text" name="dsaida" value="<? echo $datasaida ?>" />
       </td> 
      </tr>
      <tr>
      <td colspan="2" align="center">
        CAV:
        </td>
       <td colspan="2" align="center">
       	<input type="text" name="cav" value="<? echo $cav ?>" />
       </td> 
      </tr>
      <tr>
      <td colspan="2" align="center">
       Turno:
       </td>
       <td colspan="2" align="center">
       <select name="turno">
       	<option value="manha" <? if ($turno == 'manha') { echo "selected=selected"; } ?>>09h-13h</option>
       	<option value="int" <? if ($turno == 'int') { echo "selected=selected"; } ?>>13h-17h</option>
        	<option value="noite" <? if ($turno == 'noite') { echo "selected=selected"; } ?>>17h-21h</option>
       </select>
       </td>
      </tr>
    <tr>
      <td colspan="2" align="center">
       Activo:
       </td>
       <td colspan="2" align="center">
       <select name="activo">
       	<option value="1" <? if ($act == '1') { echo "selected=selected"; } ?>>Activo</option>
       	<option value="0" <? if ($act == '0') { echo "selected=selected"; } ?>>Inactivo</option>
       </select>
       </td>
      </tr>
    <tr>
    	<td colspan="2" align="center">
        <br />
        	<input type="submit" value="Enviar" />
        </td>
        <td colspan="2" align="center">
        <br />
        	<input type="button" value="Voltar" onclick='location.replace("listacolab.php");'  />
        </td> 
    </tr>
    </table>
</td>
  </tr>
</table>
</form>
   
   </td>
</tr>
</table>

</body>
</html>
