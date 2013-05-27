<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="../../../../css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../../calendar/calendar_db.js"></script>
<link rel="stylesheet" href="../../../../calendar/calendar.css" />
<?php
require('../../../../ini/dbconnect.php');
$today = date("o-m-d");
##################################################
$query = "	SELECT 	campaign_id, campaign_name 
			FROM 	vicidial_campaigns";
$query = mysql_query($query, $link);

$num_camps = mysql_num_rows($query);

for ($i=0;$i<$num_camps;$i++)
{
	if ($i == 0) 
	{
	$row = mysql_fetch_assoc($query);
	$camp_options .= "<option selected value=$row[campaign_id]>$row[campaign_name]</option>";
	}
	else
	{
	$row = mysql_fetch_assoc($query);
	$camp_options .= "<option value=$row[campaign_id]>$row[campaign_name]</option>";
	}
	
}
##################################################
?>
</head>
<body>
<form name="report" action="exportcsv_fc.php" target="_self" method="post">
<input type="hidden" value='go' name="totais_camp2">
<div class="cc-mstyle">	
<table>
<tr>
<td id='icon32'><img src='/images/icons/document_inspector_32.png' /></td>
<td id='submenu-title'> Total de Feedbacks por Campanha </td>
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
'formname': 'report',
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
'formname': 'report',
// input name 
'controlname': 'data_final'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Monday week start
</script>
</td>
</tr>
<tr> 
<td>Campanha:</td><td><select name="camp_options" id="camp_options" style="width:200px;"><?php echo $camp_options; ?></select></td>	
</tr>
<tr>
<td>Opções do Report:</td><td><select name="flag" id="flag" style="width:315px;"><option value="todos">Todos os Feedbacks em Sistema</option><option value="encontrados">Apenas os Feedbacks Encontrados na Pesquisa</option></select></td>	
</tr>
</table>
<br><br>
</div>

</div>

</div>
<br><br>
</form>

	
</body>
</html>