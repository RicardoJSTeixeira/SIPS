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
$today = date("Y-m-d");
##################################################

$query1 = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]';", $link)) or die(mysql_error());
$query2 = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$query1[user_group]';", $link)) or die(mysql_error());
$AllowedCampaigns = "'" . preg_replace("/ /","','" , preg_replace("/ -/",'',$query2['allowed_campaigns'])) . "'";

$query = "	SELECT 	campaign_id, campaign_name 
			FROM 	vicidial_campaigns WHERE active='Y' AND campaign_id IN ($AllowedCampaigns)";
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
	
<form name="report" action="export_csv.php" target="_self" method="post">
<div class="cc-mstyle">	
<table>
<tr>
<td id='icon32'><img src='/images/icons/document_inspector_32.png' /></td>
<td id='submenu-title'> Report Marcações e Novos Clientes Outbound </td>
<td style="text-align:right">Obter Report</td>
<td id='icon32'><input type="image" src='/images/icons/document_export_32.png'/><input type="hidden" id="report_marc_outbound" name="report_marc_outbound" value="1" /></td>
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
<td>Campanha:</td><td><select name="camp_options[]" id="camp_options[]" multiple style="height:150px;width:250px" ><?php echo $camp_options; ?></select></td>	
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
