<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HeartWeb Sales</title>
<link href="../../css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../calendar/calendar_db.js"></script>
<link rel="stylesheet" href="../../calendar/calendar.css" />
<script language ="javascript">


function dadoscli(valor) 
{

var url = "blogpost_print.php?id=" + valor;

//window.open (url, 'Janela', 'status=no, menubar =no, titlebar=no, scrollbars=yes');
window.open(url, '_blank');
}
</script>
</head>

<?
	require('../../sips-admin/dbconnect.php');
	
    if (isset($_POST['data'])) { $data = $_POST['data']; } else { $data = date('Y-m-d'); }
    $datafim = date("Y-m-d", strtotime("+1 day".$_POST['data']));
    
    $current_admin = $_SERVER['PHP_AUTH_USER'];
    $query = mysql_query("select user_group from vicidial_users where user='$current_admin'") or die(mysql_error());
    $query = mysql_fetch_assoc($query);
    $usrgrp = $query['user_group'];
    $stmt="SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$usrgrp';";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $LOGallowed_campaigns = $row[0];
    $LOGallowed_reports =    $row[1];


    $LOGallowed_campaignsSQL='';
    $whereLOGallowed_campaignsSQL='';
    if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
        {
            
        $rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
        $rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
        $LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
        $whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
        //echo $whereLOGallowed_campaignsSQL;
        }
    
    
    $custom_allowed = explode("','", $rawLOGallowed_campaignsSQL);
   
    
    
    for($f=1;$f<count($custom_allowed);$f++)
    {
    if(($f+1)==count($custom_allowed)){ $build_query .= " (SELECT * FROM custom_$custom_allowed[$f]) "; } else { $build_query .= " (SELECT * FROM custom_$custom_allowed[$f]) UNION ALL ";}
    } 
    
    
   // echo $build_query."<br><br>";
    
    
	$query = "SELECT list_id FROM vicidial_lists WHERE campaign_id IN ('$rawLOGallowed_campaignsSQL')";
	$query = mysql_query($query);
	$dbs_count=mysql_num_rows($query);

	for ($i=0;$i<$dbs_count;$i++)
	{
	$dbs = mysql_fetch_row($query);	
	if ($dbs_count == 1)  
		{
		$dbs_IN = "'".$dbs[0]."'"; 
		}
	elseif ($dbs_count-1 == $i)
		{
		$dbs_IN .= "'".$dbs[0]."'";	
		}	
	else
		{
		$dbs_IN .= "'".$dbs[0]."',";
		}
	}
	
	$row = mysql_fetch_assoc($query);
	
	$query = "SELECT A.lead_id, user, first_name, status, reuniao, hora, phone_number from vicidial_list A LEFT JOIN ( $build_query ) B ON B.lead_id=A.lead_id WHERE list_id IN($dbs_IN ,'998') and status IN ('VENDA','AG') AND last_local_call_time >= '$data' AND last_local_call_time < '$datafim'";
	
    //echo $query."<br><br>";
    
    $query = mysql_query($query) or die (mysql_error());

	
	
	#$query = mysql_query("SELECT vicidial_list.lead_id, vicidial_list.campaign_id, call_date, vicidial_log.phone_number, vicidial_log.user, vicidial_list.status FROM vicidial_list RIGHT JOIN vicidial_log on vicidial_list.lead_id = vicidial_log.lead_id WHERE call_date >= '$data' AND call_date < '$datafim' AND vicidial_log.status IN ('VENDA') and vicidial_log.campaign_id='05'") or die(mysql_error());
	
	#$query = "SELECT c.letra, c.reuniao, c.hora, c.faleicom, c.decisor, c.contactomovel, c.pontoref, c.operadorfixo, c.operadorfixovalor, c.operadornetfixa, c.operador";
	
	?>

<body>
<? 
	
	
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../../images/icons/to_do_list_32.png' /></td>";
	echo "<td id='submenu-title'>Reuniões Blogpost </td>";
?>
<form name="report" action="blogpost_reunioes.php" target="_self" method="post">
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
<input type="submit" value="Ver Reuniões" />
</td>
</form>
</tr>
</table>
</div>
<br />
<br />
<div class=cc-mstyle>
<table align="center" border="0" >
<th colspan="6">Total de marcações: <? echo mysql_num_rows($query); ?></th>
<tr><th>Operador</th><th>Cliente</th><th>Telefone</th><th>Data Reunião</th><th>Hora Reunião</th><th>Ver Folha de Marcação</th></tr>

	<? for ($i=0; $i<mysql_num_rows($query); $i++) {
		$row = mysql_fetch_assoc($query);
		
		/*$lead_id = $curRecord['lead_id'];
		
		$curLead = mysql_query("SELECT * FROM vicidial_list WHERE lead_id = '$lead_id'") or die(mysql_error());
		$curLead = mysql_fetch_assoc($curLead);
		
		$campanha = "custom_".$curRecord['campaign_id'];
		
		$marcacao = mysql_query("SELECT * FROM $campanha WHERE lead_id = '$lead_id'") or die(mysql_error());
		$curM = mysql_fetch_assoc($marcacao);*/
		
		echo "<tr><td>".$row['user']."</td><td>".$row['first_name']."</td><td>".$row['phone_number']."</td><td>".$row['reuniao']."</td><td>".$row['hora']."</td><td><input style='cursor:pointer' type='image' src='../../images/icons/to_do_list_32.png' value='Ver' onclick=dadoscli('".$row['lead_id']."') /></td></tr>";
		
		
	}
	?>



</table>
</div>
</body>
</html>