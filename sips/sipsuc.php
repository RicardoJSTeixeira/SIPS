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

</script> 


</head>

<?php
	$user = $_GET["user"];
$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("asterisk", $con);
	
	$list = mysql_query("SELECT * FROM vicidial_list WHERE user = '$user' ORDER BY last_local_call_time DESC") or die (mysql_error());
	
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
        <?php $link3 = "sipsuc.php?user=".$user; ?>
        <a target="_new" href="<?php echo $link3; ?>">Últimos Contactos</a>
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
        <input type="text" name="user" id="user" />
        </td>
     </tr>
     <tr>   
        <td align="center">
        <label>Password:</label>
        <br />
        <input type="password" name="password" id="password" />
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
    <?php
    $count = 21;
	
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td class='header'># Contacto</td>";
	echo "<td class='header'>Nome Cliente</td>";
	echo "<td class='header'>Nº Telefone</td>";
	echo "<td class='header'>Comentários</td>";
	echo "</tr>";
	

	
	for ($i=1; $i<$count; $i++) {
		$rlist = mysql_fetch_assoc($list);
		echo "<tr>";
		echo "<td class='lista'>".$i."</td>";
		if ($rlist['first_name'] != null) {
		echo "<td class='lista'>".utf8_encode($rlist['first_name'])."</td>"; } else {
			echo "<td class='lista'>".utf8_encode($rlist['address3'])."</td>"; }
		echo "<td class='lista'>".$rlist['phone_number']."</td>";
		echo "<td class='lista'>".$rlist['comments']."</td>";
		echo "</tr>";
		
	}
	
	echo "</table>";
	
?>
    </td>
    <td>&nbsp;</td>
  </tr>
</table>



</body>
</html>