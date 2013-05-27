<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="../../../css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../calendar/calendar_db.js"></script>
<link rel="stylesheet" href="../../../calendar/calendar.css" />
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
require('../../../ini/dbconnect.php');
$today = date("o-m-d");
##################################################

$query = "	SELECT 	group_id, group_name 
			FROM 	vicidial_inbound_groups where active='Y'";
$query = mysql_query($query, $link);

$num_camps = mysql_num_rows($query);

for ($i=0;$i<$num_camps;$i++)
{
	
	$row = mysql_fetch_assoc($query);
	$camp_options .= "<option value=$row[group_id]>$row[group_name]</option>";
	
	
}

$query = "select status, status_name from ((select status, status_name from vicidial_campaign_statuses) UNION ALL (select status, status_name from vicidial_statuses)) a group by status";
$query = mysql_query($query, $link);

$num_camps = mysql_num_rows($query);

for ($i=0;$i<$num_camps;$i++)
{
	if ($i == 0) 
	{
	$row = mysql_fetch_assoc($query);
	$feedbacks .= "<option selected value=$row[status]>$row[status_name]</option>";
	}
	else
	{
	$row = mysql_fetch_assoc($query);
	$feedbacks .= "<option value=$row[status]>$row[status_name]</option>";
	}
	
}
##################################################
?>
</head>
<body>
	
<form name="report" action="export_csv.php" target="_self" method="post">
<div class="cc-mstyle">	
<table>
<tr>
<td id='icon32'><img src='/images/icons/document_inspector_32.png' /></td>
<td id='submenu-title'> Report Inbound por Feedback </td>
<td style="text-align:right">Obter Report</td>
<td id='icon32'><input type="image" src='/images/icons/document_export_32.png'/><input type="hidden" id="report_feedback_inbound" name="report_feedback_inbound" value="1" /></td>
</tr>
</table>
</div>


<div id="work-area" style="min-height:0px"><br><br>
<div class=cc-mstyle style="border:none">
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
<td>Linhas:</td><td><select name="camp_options[]" id="camp_options[]" multiple style="height:150px;width:250px" ><?php echo $camp_options; ?></select></td>	
</tr>
    
<tr> 
<td>Feedback:</td><td><select name="feedbacks[]" id="feedbacks[]" multiple style="height:150px;width:250px" ><?php echo $feedbacks; ?><option value="NAOEX">Não Existe</option></select></td>	
</tr>    

<tr><td>&nbsp;</td></tr>
</table>
</div>
</div>
</div>
<br><br>
</form>
</body>
</html>
