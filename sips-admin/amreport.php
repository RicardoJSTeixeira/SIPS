<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="calendar_db.js"></script>
<link rel="stylesheet" href="calendar.css" />


</head>

<body>
 
<? 
//werwer	lalala lalalla
	
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='/images/icons/AM-icon.jpg' /></td>";
	echo "<td id='submenu-title'> Report Acústica Médica </td>";
?>




<form name="report" action="exportcsv.php" target="_self" method="post">
<td>Escolha o dia:</td>
<td><input type="date" name='data' id='data' value='<? echo $data; ?>' />
<script language="JavaScript">
var o_cal = new tcal ({
// form name
'formname': 'report',
// input name
'controlname': 'data'
});
o_cal.a_tpl.yearscroll = false;
// o_cal.a_tpl.weekstart = 1; // Monday week start
</script>
</td>

<td>
<input type="submit" value="Fazer Download" />
</td>
</form>
</tr>
</table>
</div>


<?	
	
	
				
				
				mysql_close($con);
?>
	
   
   
    </table>
</span>
</body>
</html>
