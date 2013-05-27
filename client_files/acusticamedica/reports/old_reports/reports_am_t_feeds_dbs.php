<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="../../../../css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../../calendar/calendar_db.js"></script>
<link rel="stylesheet" href="../../../../calendar/calendar.css" />
<?
require('../../../../ini/dbconnect.php');
$today = date("o-m-d");
$query = "	SELECT	list_id, 
					list_name 
			FROM 	vicidial_lists
			WHERE 	list_id<>998;";
$query = mysql_query($query, $link);

for ($i=0;$i<mysql_num_rows($query);$i++)
{
	if ($i == 0) 
	{
	$row = mysql_fetch_assoc($query);
	$db_options .= "<option selected value=$row[list_id]>$row[list_name]</option>";
	}
	else
	{
	$row = mysql_fetch_assoc($query);
	$db_options .= "<option value=$row[list_id]>$row[list_name]</option>";
	}
	
}
?>
</head>
<body>
<form name="totais_db" action="exportcsv_fc.php" target="_self" method="post">
<input type="hidden" value="go" name="totais_db">
<div class="cc-mstyle">	
<table>
<tr>
<td id='icon32'><img src='/images/icons/document_inspector_32.png' /></td>
<td id='submenu-title'> Total de Feedbacks por Base de Dados e Operador </td>
<td style="text-align:right">Obter Report</td>
<td id='icon32'><input type="image" src='/images/icons/document_export_32.png'/></td>
</tr>
</table>
</div>


<div id="work-area" style="min-height:0px"><br><br>
<div class=cc-mstyle style=border:none>
<table>
<tr>
<td>Dia Inicial:</td>
<td><input style="width:200px; text-align:center;" type="text" name='data_inicial' id='data_inicial' value='<?php echo $today; ?>' /><td>
<script language="JavaScript">
var o_cal = new tcal ({
// form name
'formname': 'totais_db',
// input name 
'controlname': 'data_inicial'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Monday week start
</script>
</td>
</tr>

<tr>
<td>Dia Final:</td>
<td><input style="width:200px; text-align:center;" type="text" name='data_final' id='data_final' value='<?php echo $today; ?>' /><td>
<script language="JavaScript">
var o_cal = new tcal ({
// form name
'formname': 'totais_db',
// input name 
'controlname': 'data_final'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Monday week start
</script>
</td>
</tr>
<tr>
<td>Base de Dados:</td><td><select multiple name="db_options[]" id="db_options[]" style="width:202px; height:202px;"><?php echo $db_options; ?></select></td>	
</tr>
<tr>
<td>Opções do Report:</td><td><select name="flag" id="flag" style="width:315px;"><option value="encontrados">Apenas os Feedbacks Encontrados na Pesquisa</option><option value="todos">Todos os Feedbacks em Sistema</option></select></td>	
</tr>
</table>
  
</form>	
<br><br>
</body>
</html>