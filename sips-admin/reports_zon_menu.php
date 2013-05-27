<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<?php
require('dbconnect.php');

$query = "select list_id from vicidial_list where (title like '%PUROSINONIMO%' or title > '6666666') and list_id>'1000' group by list_id";
$query= mysql_query($query) or die(mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	$select_lotes .= "<option value=$row[list_id]>$row[list_id]</option>";
}

$query = "select list_id from vicidial_lists where list_name like '%ZON%'";
$query= mysql_query($query) or die(mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query);
	$select_big .= "<option value=$row[list_id]>$row[list_id]</option>";
}

?>
</head>
<body>
	
	
	<div class=cc-mstyle>
	<table>
	<tr>
	<td id='icon32'><img src='../images/icons/export_excel_32.png' /></td>
	<td id='submenu-title'> Reports ZON </td>
	<td style='text-align:left'></td>
	</tr>
	</table>
	</div>
	
	<div id=work-area>
	<br><br>
		
	<div class=cc-mstyle style='border:none'>
		<table border=0>
		<!--		<tr onclick="window.location='reports_am_geral_camp.php'">
				
				<td id='icon32'><img src='/images/icons/document_move.png' /></td>
				
				<td style='text-align:left; cursor:pointer;'> Report Geral AM por Campanha</td>
				
			</tr>
				<tr onclick="window.location='reports_am_geral_db.php'">
				
				<td id='icon32'><img src='/images/icons/document_move.png' /></td>
				
				<td style='text-align:left; cursor:pointer;'> Report Geral AM por Base de Dados</td>
				
			</tr> 
			<tr><td>&nbsp;</td></tr> -->
			<tr><b><td style=text-align:left colspan=4><font size=4></font></b></tr>
			<tr>
				<form action="exportcsv_fc.php" method="POST">
				<input type="hidden" value="go" name="zon_diario">	
				<td id='icon32'><input type="image" src='../images/icons/document_move_32.png' /></td>
				
				<td> Report Zon - Diário</td>
				<td><select name='mes'><option value="1">Janeiro</option><option value="2">Fevereiro</option><option value="3">Março</option><option value="4">Abril</option><option value="5">Maio</option><option value="6">Junho</option><option value="7">Julho</option><option value="8">Agosto</option><option value="9">Setembro</option><option value="10">Outubro</option><option value="11">Novembro</option><option value="12">Dezembro</option></select></td>
				<td><select name='ano'><option>2012</option><option>2013</option><option>2014</option><option>2015</option></select></td>
				</form>
			</tr>
			
			
			
			
			<tr><td>&nbsp;</td></tr>
			<tr><b><td style=text-align:left colspan=4><font size=4></font></b></tr>
			<tr>
				
				<form action="exportcsv_fc.php" method="POST">
				<input type="hidden" value="go" name="zon_vendas">	
				<td id='icon32'><input type="image" src='../images/icons/document_move_32.png' /></td>
				
				<td> Report Zon - Vendas</td>
				<td><select name='mes'><option value="1">Janeiro</option><option value="2">Fevereiro</option><option value="3">Março</option><option value="4">Abril</option><option value="5">Maio</option><option value="6">Junho</option><option value="7">Julho</option><option value="8">Agosto</option><option value="9">Setembro</option><option value="10">Outubro</option><option value="11">Novembro</option><option value="12">Dezembro</option></select></td>
				<td><select name='ano'><option>2012</option><option>2013</option><option>2014</option><option>2015</option></select></td>
				</form>
			</tr>
				<tr><td>&nbsp;</td></tr>
			<tr><b><td style=text-align:left colspan=4><font size=4></font></b></tr>
			<tr>
				
				<form action="exportcsv_fc.php" method="POST">
				<input type="hidden" value="go" name="zon_recusas">	
				<td id='icon32'><input type="image" src='../images/icons/document_move_32.png' /></td>
				
				<td> Report Zon - Recusas (PROSPECT)</td>
				<td><select name='mes'><option value="1">Janeiro</option><option value="2">Fevereiro</option><option value="3">Março</option><option value="4">Abril</option><option value="5">Maio</option><option value="6">Junho</option><option value="7">Julho</option><option value="8">Agosto</option><option value="9">Setembro</option><option value="10">Outubro</option><option value="11">Novembro</option><option value="12">Dezembro</option></select></td>
				<td><select name='ano'><option>2012</option><option>2013</option><option>2014</option><option>2015</option></select></td>
				</form>
			</tr>
				<tr>
					
				<tr>
				
				<form action="exportcsv_fc.php" method="POST">
				<input type="hidden" value="go" name="zon_recusaswb">	
				<td id='icon32'><input type="image" src='../images/icons/document_move_32.png' /></td>
				
				<td> Report Zon - Recusas (WB)</td>
				<td><select name='mes'><option value="1">Janeiro</option><option value="2">Fevereiro</option><option value="3">Março</option><option value="4">Abril</option><option value="5">Maio</option><option value="6">Junho</option><option value="7">Julho</option><option value="8">Agosto</option><option value="9">Setembro</option><option value="10">Outubro</option><option value="11">Novembro</option><option value="12">Dezembro</option></select></td>
				<td><select name='ano'><option>2012</option><option>2013</option><option>2014</option><option>2015</option></select></td>
				</form>
			</tr>
				<tr><td>&nbsp;</td></tr>
			<tr><b><td style=text-align:left colspan=4><font size=4></font></b></tr>
				<tr>
				
				<form action="exportcsv_fc.php" method="POST">
				<input type="hidden" value="go" name="zon_bd">	
				<td id='icon32'><input type="image" src='../images/icons/document_move_32.png' /></td>
				
				<td> Report Zon - Estado da BD</td>
				<td></td>
				<td></td>
				</form>
			</tr>
			
				<tr><td>&nbsp;</td></tr>
			<tr><b><td style=text-align:left colspan=4><font size=4></font></b></tr>
			<tr>
				
				<form action="exportcsv_fc.php" method="POST">
				<input type="hidden" value="go" name="zon_list">	
				<td id='icon32'><input type="image" src='../images/icons/document_move_32.png' /></td>
				
				<td> Report Zon - Listagem</td>
				<td><select name=lote><?php echo $select_lotes; ?>'</select></td>
				<td></td>
				</form>
			</tr>
		
		
		
		</table>
		
		
		
	</div>	
	</div>
	
	
	
	
</body>
</html>