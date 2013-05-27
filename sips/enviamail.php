<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body td {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
</style>
<title>SIPS - Pedido de Envio de E-Mail</title>
<script type="text/javascript">
function validateForm()
{
var x=document.forms["enviamail"]["cli"].value
if (x==null || x=="")
  {
  alert("Preencha o nome do cliente");
  return false;
  }
var x=document.forms["enviamail"]["mail"].value
if (x==null || x=="")
  {
  alert("Preencha o e-mail do cliente");
  return false;
  }
  
}

</script>
<?php
	$user = $_GET['user'];
?>

</head>

<body>
<form name="enviamail" action="gravarmail.php" method="post" onsubmit="return validateForm()">

<table width="300px" align="center">
<tr>
  <td colspan="2" align="center"><p>Atenção: Este e-mail não vai directamente para o cliente! Será enviado pela supervisão.</p>
    <p>&nbsp;</p></td>
  </tr>
<tr>
	<td>Nome do Cliente: </td><td><input type="text" name="cli" /></td>
</tr>
<tr>    
    <td>E-mail do cliente: </td><td><input type="email"  name="mail" /></td>
</tr>
<tr>    
    <td>Pacote a enviar: </td><td>
    	<select name="pacotes">
        	<option value="---">---</option>
        	<option value="Seleccao Digital SO TV">Só TV - Selecção Digital</option>
            <option value="Digital HD SO TV">Só TV - Digital HD</option>
            <option value="Selec SportTV SO TV">Só TV - Selecção SportTV</option>
            <option value="2p TDT">2P - TDT + Ilimitado</option>
            <option value="2p SD TALK+">2P - SD Talk+</option>
            <option value="2p TALK">2P - TALK</option>
            <option value="2p SMART">2P - SMART</option>
            <option value="3p TDT">3P - TDT + 12Mb + Ilim</option>
            <option value="3p SDNETPLUS">3P - SD + 6Mb + Ilim</option>
            <option value="3p FUN">3P - DHD + 12Mb + Ilim</option>
            <option value="IRIS 30">3P - IRIS 30 sem Telefone</option>
            <option value="IRIS 30+">3P - IRIS 30+ Tlf Ilim</option>
            <option value="IRIS 60">3P - IRIS 60</option>
            <option value="IRIS 120">3P - IRIS 120</option>
        </select></td>        
</tr>
<tr>   
    <th colspan="2">Observações adicionais (o teu contacto, preço do pacote, etc.):</th>    
</tr>    
</tr>
<tr>
	<th colspan="2"><textarea cols="100" rows="5" name="obs"></textarea></th>
</tr>
<tr>
	<td align="right"><input type="submit" value="Enviar" /></td><td align="left"><input type="button" value="Cancelar" onclick="window.close();"/><input name="user" type="hidden" value="<?php echo $user; ?>" /></td>
</tr>
</table>



</form>


</body>
</html>