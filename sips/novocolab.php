<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />



<?php
	
	
	$data = date('o-m-d');
	
	
?>

<title>SIPS - Inserção de novo colaborador</title>

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
body td {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
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

textomaior {
	font-size: 16px;
}
</style>
<script type="text/javascript">

 



function validateForm()
{
var x=document.forms["novocolab"]["user"].value
if (x==null || x=='')
  {
  alert("Username é obrigatório");
  return false;
  }


var y=document.forms["novocolab"]["nif"].value
if (y==null || y=='')
  {
  alert("NIF é obrigatório");
  return false;
  }

var z=document.forms["novocolab"]["bi"].value
if (z==null || z=="")
  {
  alert("BI é obrigatório");
  return false;
  }
var w=document.forms["novocolab"]["password"].value
if (w==null || w=="")
  {
  alert("Password é obrigatório");
  return false;
  }

}

</script>
</head>



<body>
<form action="inserecolab.php" name="novocolab" id="novocolab" method="post" target="_self" onsubmit="return validateForm()">
<table width="1030" align="center" id="main_site" border="1">
<tr>
<td width="208" height="80">
<table width="200px" align="left" id="barra_esquerda">
<tr>
<td align="center" height="70"><img src="img/sipslogo.jpg" width="200" height="68" />
<p>Bem vindo!</p>
<input type="hidden" name="data" value="<?php echo $data; ?>"  />
</td>
</tr>
</table>
</td>
<td width="600" rowspan="2">
<table width="600" align="center">

	<tr align="center">
	  <td colspan="6" align="center"><label class="header">Dados Pessoais</label><hr /></td>
    </tr>
	<tr>
    	<td width="74" align="center"><label>Nome:</label></td><td colspan="5" align="left"><input size="50" type="text" name="nome" id="nome" value="<?php echo $nome ?>"/>
        </td>
    </tr>
    <tr>
    <td height="25" align="center"><label>Rua:</label></td><td colspan="3" align="left"><input size="50" type="text" name="morada" id="morada" value="Nome da Rua" onclick="this.value='';"  />
    </td>
    <td colspan="2" align="left"><input type="text" name="local" id="local" size="30" value="Localidade" onclick="this.value='';" /></td>
    
    </tr>
    <tr>
    <td height="25" align="center"><label>Andar:</label></td><td colspan="1" align="left"><input size="10" type="text" name="porta" id="porta" value="Nº da Porta" onclick="this.value='';" />
    </label></td>
    <td align="left"><input type="text" name="andar" id="andar" value="Piso/Andar" onclick="this.value='';" size="10"/></td>
    <td align="center">Cód. Postal:</td>
    <td width="48" align="left"><input type="text" name="codpostal" id="codpostal" size="7" value="<?php echo $codpostal ?>" maxlength="4" /></td>
    <td width="136" align="left"><input type="text" name="codrua" id="codrua" size="5" value="<?php echo $codrua ?>" maxlength="3" /></td>
    </tr>
    
    <tr>
    <td align="center"><label>Telefone:</label></td><td width="202" colspan="2" align="left"><input size="20
    " type="text" name="tlf" id="tlf" value="<?php echo $tlf ?>" /> </td>
    <td width="116" align="center">Telemovel:</td>
    <td colspan="2" align="left"><input size="20" type="text" name="tlml" id="tlml" value="<?php echo $tlml ?>" /></td>
    </tr>
    <tr>
      <td align="center">E-mail:</td>
      <td colspan="2" align="left">
      <input name="email" type="text" id="email" value="<?php echo $email ?>" /></td>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="center">BI:</td>
      <td colspan="2" align="left"><label for="bi"></label>
        <input type="text" name="bi" id="bi" value="<?php echo $bi ?>" /></td>
      <td align="center">NIF:</td>
      <td colspan="2" align="left">
        <input type="text" name="nif" id="nif" value="<?php echo $nif ?>" /></td>
    </tr>
    <tr>
      <td align="center">Segurança Social:</td>
      <td colspan="2" align="left">
        <input type="text" name="segsocial" id="segsocial" value="<?php echo $segsocial ?>" /></td>
      <td align="center">Data nascimento</td>
      <td colspan="2" align="left">
        <input type="text" name="datanasc" id="datanasc" value="<?php echo $datanasc ?>"/></td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr align="center">
      <td colspan="6" align="center"><label class="header">Outras Informações<br />
      </label><Hr /></td>
    </tr>
    <tr>
      <td colspan="3" align="center">Habilitações Literárias:</td>
      <td colspan="3" align="center">
        <select name="hablit" id="hablit">
        	<option value="---">---</option>
        	<option value="menos9">Inferior 9º Ano</option>
            <option value="9ano">9º Ano</option>
            <option value="12ano">12º Ano</option>
            <option value="Lic">Licenciatura</option>
            <option value="Mestre">Mestrado</option>
        </select></td>
      </tr>
    <tr>
      <td colspan="3" align="center">Banco:</td>
      <td colspan="3" align="center"><p>
        
        <input name="banco" type="text" id="banco" size="40" value="<?php echo $banco ?>" />
        <br />
      </p></td>
      </tr>
    <tr>
      <td colspan="3" align="center">NIB:</td>
      <td colspan="3" align="center">
        <input name="nib" type="text" id="nib" size="40" value="<?php echo $nib ?>"/></td>
      </tr>
    <tr>
      <td colspan="3" align="center">Estado Civil:</td>
      <td colspan="3" align="center">
        <select name="estcivil" id="estcivil">
        	<option value="---">---</option>
            <option value="Solteiro(a)">Solteiro(a)</option>
            <option value="Casado(a)">Casado(a)</option>
            <option value="União Facto">União Facto</option>
            <option value="Divorciado(a)">Divorciado(a)</option>
            <option value="Viúvo(a)">Viúvo(a)</option>
        </select></td>
      </tr>
    <tr>
      <td colspan="3" align="center"><p>Nº de dependentes:<br />
      </p>        </td>
      <td colspan="3" align="center">
        <input name="dependentes" type="text" id="dependentes" value="<?php echo $dependentes ?>" size="5"/></td>
      </tr>
    <tr>
      <td colspan="6" align="center">
      <label class="header"><br />
        Informações sobre o trabalho<br />
      </label><Hr />
      </td>
      </tr>
    <tr>
      <td colspan="6" align="center"><table width="100%" border="0">
        <tr>
          <td width="52%" align="center">Horário de trabalho:</td>
          <td width="48%" align="center">
            <select name="horario" id="horario">
            	<option value="---">---</option>
            	<option value="manha">09h - 13h</option>
                <option value="int">13h - 17h</option>
                <option value="noite">17h - 21h</option>
            </select></td>
        </tr>
        <tr>
          <td align="center">Experiência em call-center:</td>
          <td align="center">
            <select name="expcc" id="expcc">
            	<option value="---">---</option>
            	<option value="0">Sem Experiência</option>
                <option value="1">Menos de 1 Ano</option>
                <option value="2">Mais de 1 Ano</option>
            </select></td>
        </tr>
        <tr>
          <td align="center">Experiência em vendas:</td>
          <td align="center">
            <select name="expvendas" id="expvendas">
            	<option value="---">---</option>
            	<option value="0">Sem Experiência</option>
                <option value="1">Menos de 1 Ano</option>
                <option value="2">Mais de 1 Ano</option>
            </select></td>
        </tr>
        <tr>
          <td align="center">Experiência com ZON:</td>
          <td align="center">
            <select name="expzon" id="expzon">
            	<option value="---">---</option>
                <option value="0">Sem Experiência</option>
                <option value="1">Menos de 1 Ano</option>
                <option value="2">Mais de 1 Ano</option>
            </select></td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
        </tr>
        <tr>
          <td align="center" bgcolor="#669999">Escolha o seu username para sistemas internos:</td>
          <td align="center" bgcolor="#669999"><input type="text" name="user" value="Exemplo: joaosilva" onclick="this.value='';"    /></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#669999">Escolha a sua password para sistemas internos:</td>
          <td align="center" bgcolor="#669999"><input type="password" name='password'  /></td>
        </tr>
      </table>        
        <p>
        <input type="submit" name="Submeter" id="Submeter" value="Submeter"  />
        &nbsp;&nbsp;
         &nbsp;&nbsp;
         <input type="reset" name="reset" id="reset" value="Reiniciar" />
      </p></td>
      </tr>
    
  </table>
  </td>
  <td width="200" rowspan="2" valign="top">
  <table width="200" align="right" id="barra_direita">
  <tr>
	<td align="center" valign="top"><br /><br /></td>
	</tr>
    <tr>
    <td align="center" valign="top"><br /><br />
    </td>
    </tr>
    <tr>
   
    	<td align="center"></tr>
  </table>  
      </td>
  </tr>
<tr>
  <td align="center" valign="top">
    <p>&nbsp;</p>
    <p>&nbsp;</p></td>
</tr>
</table>
</form>
</body>
</html>