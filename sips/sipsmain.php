<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Sistemas Internos da Purosinónimo</title>
<style type="text/css">
.text {
	font-size: 12px;
	font: Verdana, Geneva, sans-serif;
}
.lista {
	font-size:12px;
	font:Verdana, Geneva, sans-serif;
	font-style:normal;
	border:1px;
	border-style:solid;
	border-color:#000;
	padding:3px;
	text-align:left;
}
.header {
	font-family: Tahoma, Geneva, sans-serif;
	font-size: 14px;
	border:1px;
	border-style:solid;
	border-color:#000;
	background-color:#FFC;
	text-align:center;
}
</style>

<script language ="javascript">


function dadoscli(valor)
{

var url = valor;

window.open (url, "Janela", "status=no, width=550, height=200, menubar =no, titlebar=no");
}

function mudapass(valor)
{
	var url = valor;
	window.open (url, "Janela", "status=no, width=550, height=200, menubar =no, titlebar=no");
}

function ultimoscontactos(valor)
{
	var url = valor;
	window.open(url, "Janela", "status=no, width=550, height=700, menubar =no, titlebar=no, scrollbars=yes")
}

function estatisticas(valor)
{
	var url = valor;
	window.open (url, "Janela", "status=no, width=550, height=400, menubar =no, titlebar=no");
}


</script> 


</head>

<?php
	$user = $_POST['user'];
	$password = $_POST['password'];
	$mes = $_POST['mes'];
	$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("integra_bd_2011", $con);
	
	//echo base64_encode(strtoupper(md5($password)));
    $passmd5 = md5($password);

	$users = mysql_query("SELECT * FROM t_utilizador WHERE Utilizador = '$user'")or die (mysql_error());
	
	$rusers = mysql_fetch_assoc($users);
	
	if ($user != NULL) { 
	if ($rusers['Passwd_MD5']!= $passmd5) { echo("User/Pass errados, volte atrás e tente outra vez!"); } else {
	
	$a = $rusers['Utilizador'];
	$b = $rusers['Password'];
	$id = $rusers['ID_Colaborador'];
	$nome = $rusers['Nome_Colaborador'];
	$cargo = $rusers['Cargo'];
	
	}
	}
	
	
	/** **/
	$curYear  = date('Y');
	if ($mes>0){$curMonth = $mes;} else { $curMonth = date('n'); $mes = $curMonth; }
	$data = $curYear.'-'.$curMonth.'-01';
	$datafim = $curYear.'-'.$curMonth.'-31';
	
	if ($cargo == "Vendedor" ) {
	
	$contratos = mysql_query("SELECT * FROM T_Contrato WHERE ID_Colaborador = '$id' AND Data_Resolucao >= '$data' AND Data_Resolucao <= '$datafim'") or die (mysql_error());
	} elseif ($cargo == "Chefe Equipa")  {
		$contratos = mysql_query("SELECT * FROM T_Contrato WHERE ID_Colaborador = '$id' AND Data_Resolucao >= '$data' AND Data_Resolucao <= '$datafim'") or die (mysql_error());
	} else {
		if ($id != NULL){
	$contratos = mysql_query("SELECT * FROM T_Contrato WHERE Data_Resolucao > '$data' AND Data_Resolucao < '$datafim'") or die (mysql_error());		}
	}

	mysql_close($con);
?>
<body>
<table width="100%" border="1">
  <tr>
    <th scope="row"><table width="100%" border="0">
      <tr>
        <td align="center"><img src="img/sipslogo.jpg" width="165" height="68" /></td>
      </tr>
      <tr>
        <td align="center" class="text"><p>&nbsp;</p>
          <p><a href="#">Listagem de contratos</a></p></td>
      </tr>
      <tr>
        <td align="center" class="text"><p>&nbsp;</p>
          <p><a href="#">Ranking</a></p></td>
      </tr>
      <tr>
        <td align="center" class="text"><p>&nbsp;</p>
          <p><a href="#">Controlo de Horário</a></p></td>
      </tr>
      <tr>
        <td align="center" class="text">&nbsp;
        <br /><br />
        <a target="_new" href="https://m.extranet.zon.pt/consm/qW0El19A.asp">Verificar Moradas</a>
        </td>
      </tr>
      <tr>
        <td align="center" class="text">&nbsp;
        <br /><br />
        <?php $link3 = "ultimoscontactos.php?user=".$user; ?>
        <input type="button" name="uc" value="Últimos Contactos" onclick=ultimoscontactos("<?php echo $link3; ?>") />
        </td>
      </tr>
      <tr>
        <td align="center" class="text">
        <br />
        	<?php $link1 = "mudarpass.php?user=".$user; ?>
            
          <input type="button" value="Mudar Password" onclick=mudapass("<?php echo $link1; ?>") />
          
          </td>
      </tr>
      <tr>
        <td align="center" class="text">
        <br />
        	<?php $link4 = "estatisticas.php?user=".$user; ?>
            
          <input type="button" value="Estatisticas" onclick=estatisticas("<?php echo $link4; ?>") />
          
          </td>
      </tr>
      <tr>
        <td align="center" class="text"></td>
      </tr>
      <tr>
        <td align="center" class="text">Utilizador: <?php echo utf8_encode($nome); ?><br /><br /></td>
      </tr>
      <tr>
        <td align="center" class="text">Username: <?php echo utf8_encode($a); ?><br /><br /></td>
      </tr>
      <tr>
        <td align="center" class="text">
        <form action="sipsmain.php" method="post" name="login" target="_self">
<table>
	<tr>
    	<td align="center">
        <label>User:</label>
        <br />
        <input name="user" type="text" id="user" value="Insira o user" />
        </td>
     </tr>
     <tr>   
        <td align="center">
        <label>Password:</label>
        <br />
        <input name="password" type="password" id="password" value="Insira a pass" />
        </td>
    </tr>
    <tr>
    	<td align="center">
        	<input type="submit" value="Login" name="login"/>
        </td>
    </tr>
    <tr>
    	<td align="center">
        	<a href="sipsmain.php">Logout</a>
        </td>
    </tr>
</table>

</form>
        </td>
      </tr>
    </table width="100%" height="100%"></th>
    <td>
    <table align="center">
    <tr>
    <form name="filtra" action="sipsmain.php" method="post" target="_self">
    <td>
    <select id="mes" name="mes">
    <option value="1" <?php if ($mes=='1') {echo 'selected=selected';} ?>>Janeiro</option>
    <option value="2" <?php if ($mes=='2') {echo 'selected=selected';} ?>>Fevereiro</option>
    <option value="3" <?php if ($mes=='3') {echo 'selected=selected';} ?>>Março</option>
    <option value="4" <?php if ($mes=='4') {echo 'selected=selected';} ?>>Abril</option>
    <option value="5" <?php if ($mes=='5') {echo 'selected=selected';} ?>>Maio</option>
	<option value="6" <?php if ($mes=='6') {echo 'selected=selected';} ?>>Junho</option>
	<option value="7" <?php if ($mes=='7') {echo 'selected=selected';} ?>>Julho</option>
    </select>
    <input type="submit" name="mudames" value="Mudar o mês"/>
    <input type="hidden" name='user' id='user' value="<?php echo $user; ?>" />
    <input type="hidden" name='password' id='password' value="<?php echo $password; ?>" />
    </td>
    </form>
    </tr>
    <tr>
    <td>
    	<?php
		$numrows = mysql_num_rows($contratos);
	
	echo "<table border='0' cellspacing='0' align='center'>";
	echo "<tr>";
	echo "<td class='header'>Nome Cliente</td><td class='header'>Estado</td><td class='header'>Data do Estado</td><td class='header'>TV</td><td class='header'>NET</td><td class='header'>VOZ</td><td class='header'>Comentários</td><td class='header'>Dados do Cliente</td>";
	echo "</tr>";
	
	for($i=0; $i<$numrows; $i++){
	
	$rcontratos = mysql_fetch_assoc($contratos);
	echo "<tr style='border:thin'>";
	echo "<td  align='center' class='lista'>";
	echo utf8_encode($rcontratos['Nome_Cliente']);
	echo "</td>";
	echo "<td align='center' class='lista'>";
	echo utf8_encode($rcontratos['Estado_Interno']);
	echo "</td>";
	echo "<td  align='center' class='lista'>";
	echo utf8_encode($rcontratos['Data_Resolucao']);
	echo "</td>";
	echo "<td align='center' class='lista'>";
	echo utf8_encode($rcontratos['Serv_tv']);
	echo "</td>";
	echo "<td  align='center' class='lista'>";
	echo utf8_encode($rcontratos['Serv_net']);
	echo "</td>";
	echo "<td align='center' class='lista'>";
	echo utf8_encode($rcontratos['Serv_voz']);
	echo "</td>";
	echo "<td  align='center' class='lista'>";
	if ($rcontratos['Comentario'] == NULL) { echo '-'; } else {
	echo utf8_encode($rcontratos['Comentario']); }
	echo "</td>";
	echo "<td align='center' class='lista'>";
	$link = "dadoscliente.php?idcli=".$rcontratos['ID_Cliente']."&idmor=".$rcontratos['ID_Morada'];
	echo "<input type='button' onclick=dadoscli('".$link."'); value='Ver Dados'>";
	echo "</td>";
	echo "</tr>";
	}
	echo "</table>";
	?>
    </td>
    </tr>
    </table>
    </td>
    <td>&nbsp;</td>
  </tr>
</table>



</body>
</html>