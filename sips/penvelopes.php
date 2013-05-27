<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<STYLE TYPE="text/css">
     H2 {page-break-before: always}
	 .texto {
	font-size:16px;
	font:Arial, Helvetica, sans-serif;
	padding:3px;
}
</STYLE>

<?php
	$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("integra_bd_2", $con);

	
	$dia = $_POST['dia'];
	$dinicio = $_POST['dinicio'];
	$dfim = $_POST['dfim'];
	$edia = $_POST['edia'];
	

	
	if ($edia[0] == 'a') {
	
	$contrato = mysql_query("SELECT * FROM t_contrato WHERE Data_Estado_Interno = '$dia' AND ID_Estado_Interno = 2") or die(mysql_error());  }
	else { $contrato = mysql_query("SELECT * FROM t_contrato WHERE Data_Estado_Interno >= '$dinicio' AND Data_Estado_Interno <= '$dfim' AND ID_Estado_Interno = 2") or die(mysql_error()); }
	
	
	
	
	$cnumrows = mysql_num_rows($contrato);
	

	
	for($i; $i<$cnumrows; $i++) {
	
	
	
	$rcontrato = mysql_fetch_assoc($contrato);

	$idm = $rcontrato['ID_Morada'];
	$idc = $rcontrato['ID_Colaborador'];
	$idcli = $rcontrato['ID_Cliente'];
	$morada = mysql_query("SELECT * FROM t_morada WHERE ID_Morada = '$idm'") or die(mysql_error());
	$rmorada = mysql_fetch_assoc($morada);
	$colab = mysql_query("SELECT Nome FROM t_estrutura WHERE ID_Estrutura = '$idc'") or die(mysql_error());
	$rcolab = mysql_fetch_assoc($colab);
	$cli = mysql_query("SELECT Nome FROM t_cliente WHERE ID_Cliente = '$idcli'") or die(mysql_error());
	$rcli = mysql_fetch_assoc($cli);
	
	echo "<table width='700px' border='0' align='center'>";
    echo "<tr>";
    echo"<td class='texto' width='281' height='24'><p class='texto'>".utf8_encode($rcolab['Nome'])." - Formulários ZON</p>";
    echo "<p class='texto'>Rua da Estação Nº 22 1ºA <br /> 2725 - Mem-Martins </p></td>";
    echo "<td width='132'>&nbsp;</td>";
    echo "<td width='273'>&nbsp;</td>";
  	echo "</tr>";
    echo "<tr>";
    echo "<td height='155'>&nbsp;</td>";
    echo "<td>&nbsp;</td>";
    echo "<td  valign='bottom'><p class='texto'>".utf8_encode($rcli['Nome'])."</p>";
    echo "<p class='texto'>".utf8_encode($rmorada['Rua'].", ".$rmorada['Porta'].", ".$rmorada['Andar'])."<br />".
    utf8_encode($rmorada['Cod_Postal']."-".$rmorada['Cod_Rua']."  ".$rmorada['Localidade'])."</p></td>";
    echo "</tr>";
    echo "</table>";
	echo "<H2></H2>";
	

	
	}
	
	
	
	mysql_close($con);

		
	
	

?>
</head>

<body>





</body>
</html>