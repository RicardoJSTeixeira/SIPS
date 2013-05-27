<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="../../../../css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../../calendar/calendar_db.js"></script>
<link rel="stylesheet" href="../../../../calendar/calendar.css" />
<script type=text/javascript>
function SelectAll(elemvar) 
{
var aSelect = document.getElementById(elemvar);
var aSelectLen = aSelect.length;
for(i = 0; i < aSelectLen; i++) {
aSelect.options[i].selected = true;
}
aSelect.options[0].selected = false;
}
</script>
<?php
require('../../../../ini/dbconnect.php');
$today = date("o-m-d");
##################################################
$query = "	SELECT 	list_id, list_name 
			FROM 	vicidial_lists WHERE list_id NOT IN ('0', '998', '999')";
$query = mysql_query($query, $link);

$num_lists = mysql_num_rows($query);

for ($i=0;$i<$num_lists;$i++)
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
##################################################
##################################################
$query = "	SELECT DISTINCT status,
							status_name 
			FROM 			vicidial_campaign_statuses
			ORDER BY		status_name;"; 
$query = mysql_query($query, $link) or die(mysql_error());

$num_statuses = mysql_num_rows($query); 
for ($i=0;$i<$num_statuses;$i++)
{
	if ($i==0)
	{
	$row = mysql_fetch_assoc($query);
	$statuses_options .= "<option selected value=$row[status]>$row[status_name]</option>";
	}
	else
	{
	$row = mysql_fetch_assoc($query);
	$statuses_options .= "<option value=$row[status]>$row[status_name]</option>";
	}
	
}
##################################################
##################################################
$query = "	SELECT DISTINCT status,
							status_name 
			FROM 			vicidial_statuses
			ORDER BY		status_name;"; 
$query = mysql_query($query, $link) or die(mysql_error());

$num_statuses = mysql_num_rows($query); 
for ($i=0;$i<$num_statuses;$i++)
{
	if ($i==0)
	{
	$row = mysql_fetch_assoc($query);
	$statuses_options .= "<option selected value=$row[status]>$row[status_name]</option>";
	}
	else
	{
	$row = mysql_fetch_assoc($query);
	$statuses_options .= "<option value=$row[status]>$row[status_name]</option>";
	}
	
}
##################################################
?>
</head>
<body>
	
<form name="report" action="exportcsv_fc.php" target="_self" method="post">
<input type="hidden" value='go' name="geral_db">
<div class="cc-mstyle">	
<table>
<tr>
<td id='icon32'><img src='/images/icons/document_inspector_32.png' /></td>
<td id='submenu-title'> Report Geral AM por Base de Dados </td>
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
<td>Base de Dados:</td><td><select name="db_options[]" id="db_options[]" style="height:95px; width:200px;" multiple><option onclick="SelectAll('db_options[]');">Todas as Base de Dados</option><?php echo $db_options; ?></select></td>	
</tr>
<tr>
<td>Feedbacks:</td><td><select name="feed_options[]" id="feed_options[]" style="height:188px; width:200px;" multiple><option onclick="SelectAll('feed_options[]');">Todos os Feedbacks</option><?php echo $statuses_options; ?></select></td>	
</tr>
</table>
</div>
</div>
</div>
<br><br>
</form>
</body>
</html>