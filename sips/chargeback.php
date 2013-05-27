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
$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("integra_bd_2", $con);

$mes = $_POST['mes'];
$datainicio = "2011-".$mes."-01";
$datafim = "2011-".$mes."-31";




	$contratos = mysql_query("
	SELECT t_contrato.Conta_Servico, t_contrato.ID_Contrato, t_estrutura.Nome
	FROM t_contrato
	INNER JOIN t_estrutura ON t_contrato.ID_Colaborador = `t_estrutura`.`ID_Estrutura`
	WHERE t_contrato.Data_Estado_Interno >= '$datainicio'
	AND t_contrato.Data_Estado_Interno <= '$datafim'
	AND t_contrato.ID_Estado_Interno = 2") or die(mysql_error());






mysql_close($con);

?>
</head>

<body>
<table align="center" width="100%" border="1">
<tr>
    <td width="186" valign="top">
        <iframe width="186" height="600" src="menu.html" frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0">
    </iframe> 
   </td>
   <td width="100%" align="left">
   <table align="center" width="100%">
<form name="escolhemes" action="chargeback.php" target="_self" method="post">
<tr>
	<td colspan="1">Escolha o mês que pretende controlar: </td>
    <td colspan="1">
    	
        <select name="mes">
    		<option value="01" <? if ($mes == '01') { echo "selected=selected"; } ?>>Janeiro</option>
            <option value="02" <? if ($mes == '02') { echo "selected=selected"; } ?>>Fevereiro</option>
            <option value="03" <? if ($mes == '03') { echo "selected=selected"; } ?>>Março</option>
            <option value="04" <? if ($mes == '04') { echo "selected=selected"; } ?>>Abril</option>
            <option value="05" <? if ($mes == '05') { echo "selected=selected"; } ?>>Maio</option>
            <option value="06" <? if ($mes == '06') { echo "selected=selected"; } ?>>Junho</option>
            <option value="07" <? if ($mes == '07') { echo "selected=selected"; } ?>>Julho</option>
            <option value="08" <? if ($mes == '08') { echo "selected=selected"; } ?>>Agosto</option>
            <option value="09" <? if ($mes == '09') { echo "selected=selected"; } ?>>Setembro</option>
            <option value="10" <? if ($mes == '10') { echo "selected=selected"; } ?>>Outubro</option>
            <option value="11" <? if ($mes == '11') { echo "selected=selected"; } ?>>Novembro</option>
            <option value="12" <? if ($mes == '12') { echo "selected=selected"; } ?>>Dezembro</option>
    	</select>
    </td>
    <td colspan="1">
    	<input type="submit" value="Enviar" />
    </td>
</tr>
<tr>
<td colspan="6"><hr /></td>
</tr>
</form> 

<form name="guardaxb" action="chargeback2.php" method="post" target="_self">

<tr>
	<td>Colaborador</td>
    <td>Conta Serviço</td>
    <td>Estado de Cobranças</td>
    <td>Valor em divida</td>
    <td>Nº facturas</td>
    <td>Inactivo</td>
</tr>
<tr>
	<td colspan="6"><hr /></td>
</tr>


<?
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);

	$numrows = mysql_num_rows($contratos);
	
	for ($i=0;$i<$numrows;$i++) {
		
		$rcontrato = mysql_fetch_assoc($contratos);
		$id = $rcontrato['ID_Contrato'];
		
		$ct = mysql_query("SELECT * FROM t_chargeback WHERE id = $id") or die(mysql_error());
		
		$ct = mysql_fetch_assoc($ct);
		
		
		
		
		echo "<tr>
				<td>".utf8_encode($rcontrato['Nome'])."
				<input type='hidden' name='nome_".$i."' value='".utf8_encode($rcontrato['Nome'])."' />
				</td>
				<td>".$rcontrato['Conta_Servico']."
				<input type='hidden' name='casa_".$i."' value=".$id." />
				<input type='hidden' name='cserv_".$i."' value='".utf8_encode($rcontrato['Conta_Servico'])."' />
				</td>
				<td>
					<select name='ecob_".$i."'>"; ?>
						<option value='---' <? if ($ct['estado'] == '---') { echo 'selected=selected'; } ?> >---</option>
						<option value='Exit collections' <? if ($ct['estado'] == 'Exit collections') { echo 'selected=selected'; } ?>>Exit collections</option>
						<option value='Carta + SMS' <? if ($ct['estado'] == 'Carta + SMS') { echo 'selected=selected'; } ?>>Carta + SMS</option>
						<option value='Mail/Prefetch' <? if ($ct['estado'] == 'Mail/Prefetch') { echo 'selected=selected'; } ?>>Mail/Prefetch</option>
						<option value='Soft Disconnect' <? if ($ct['estado'] == 'Soft Disconnect') { echo 'selected=selected'; } ?>>Soft Disconnect</option>
						<option value='Hard Disconnect' <? if ($ct['estado'] == 'Hard Disconnect') { echo 'selected=selected'; } ?>>Hard Disconnect</option>
					</select>
				</td> <? 
					if ($ct['inactivo'] == 1) { $chk = 'checked=checked'; } else { $chk = ''; }
					if ($ct['estado']!= NULL) { 
						
						if ($ct['valor'] == NULL) { $valor = ''; } else { $valor = "value=".$ct['valor']; }
						if ($ct['facturas'] == NULL) { $fact = ''; } else { $fact =  "value=".$ct['facturas']; } 
						}
						else {
						$valor = '';
						$fact =  ''; }
							
				echo "
				<td><input type='text' size='5' name='valor_".$i."' ".$valor." /></td>
				<td><input type='text' size='5' name='nf_".$i."' ".$fact." /></td>
				<td><input type='checkbox' name='in_".$i."' ".$chk." /></td>
			  </tr>  ";
		
		
		
			
	}

mysql_close($con);

?>
<tr><td colspan="6"><Hr /></td></tr>
<tr>
<td>&nbsp;<input type="hidden" name="numrows" value="<? echo $i; ?>" /></td>
<td>&nbsp;</td>
<td align="right"><input type="submit" value="Enviar" /></td>
<td align="left"><input type="button" value="Descartar alterações" /></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>

</form>
</table>
   
   </td>
</tr>
</table>

</body>
</html>
