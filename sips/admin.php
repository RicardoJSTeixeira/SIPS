<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS Administração</title>


</head>
<body>
<form action="result.php" method="post" name="filtro" target="_blank">
<table name="escolha">
	<tr>
		<td>
        	<select name="satisf" onchange="satisfmuda(this)">
            <option value="---">---</option>
          	<option value="Satisfeito">Satisfeito</option>
          	<option value="Indiferente">Indiferente</option>
          	<option value="Insatisfeito">Insatisfeito</option>
          	<option value="Quer mudar de operador" >Quer mudar de operador</option>
            </select>
		</td>
        <td>
        	<input type="submit" name="submit" value="filtrar" id="submit" />
        </td>
	</tr>
	<tr>
		<td><select name="opactual" id="opactual">
		  <option value="---" >---</option>
		  <option value="ZON TVCabo" >ZON TVCabo</option>
		  <option value="MEO" >MEO</option>
		  <option value="Cabovisão" >Cabovisão</option>
		  <option value="Vodafone" >Vodafone</option>
		  <option value="Clix">Clix</option>
		  <option value="PT Comunicações" >PT Comunicações</option>
		  <option value="Outro">Outro</option>
	    </select></td>
    </tr>
    <tr>
    <td><select name="preco" id="preco">
      <option value='---' >---</option>
      <option value="10" >&lt; 10 €</option>
      <option value="20" >10 - 20€</option>
      <option value="30" >20 - 30€</option>
      <option value="40" >30 - 40€</option>
      <option value="50" >40 - 50€</option>
      <option value="60" >50 - 60€</option>
      <option value="70" >&gt; 60 €</option>
    </select></td>
    </tr>
</table>
</form>
</body>
</html>