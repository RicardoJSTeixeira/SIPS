<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Fecho Mensal</title>
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

<?php
function calculateWorkingDaysInMonth($year = '', $month = '')
{
	//in case no values are passed to the function, use the current month and year
	if ($year == '')
	{
		$year = date('Y');
	}
	if ($month == '')
	{
		$month = date('m');
	}	
	//create a start and an end datetime value based on the input year 
	$startdate = strtotime($year . '-' . $month . '-01');
	$enddate = strtotime('+' . (date('t',$startdate) - 1). ' days',$startdate);
	$currentdate = $startdate;
	//get the total number of days in the month	
	$return = intval((date('t',$startdate)),10);
	//loop through the dates, from the start date to the end date
	while ($currentdate <= $enddate)
	{
		//if you encounter a Saturday or Sunday, remove from the total days count
		if ((date('D',$currentdate) == 'Sat') || (date('D',$currentdate) == 'Sun'))
		{
			$return = $return - 1;
		}
		$currentdate = strtotime('+1 day', $currentdate);
	} //end date walk loop
	//return the number of working days
	return $return;
}
?>
</head>

<body>
<table align="center" width="100%">
<tr valign="top">
   <td width="100%" align="left">
   	
<?php 
	$mes = $_POST['mes']; 
	$ano = $_POST['ano'];
		if ($mes == NULL) { $mes = date('m'); }
		if ($ano == NULL) { $ano = date('o'); }
		
		
?>
<form method="post" target="_self">
   <table align="center" >
   <tr><td><select name="mes">
                    	<option value="01" <?php if ($mes == '01') { echo "selected=selected"; }?>>Janeiro</option>
                        <option value="02" <?php if ($mes == '02') { echo "selected=selected"; }?>>Fevereiro</option>
                        <option value="03" <?php if ($mes == '03') { echo "selected=selected"; }?>>Março</option>
                        <option value="04" <?php if ($mes == '04') { echo "selected=selected"; }?>>Abril</option>
                        <option value="05" <?php if ($mes == '05') { echo "selected=selected"; }?>>Maio</option>
                        <option value="06" <?php if ($mes == '06') { echo "selected=selected"; }?>>Junho</option>
                        <option value="07" <?php if ($mes == '07') { echo "selected=selected"; }?>>Julho</option>
                        <option value="08" <?php if ($mes == '08') { echo "selected=selected"; }?>>Agosto</option>
                        <option value="09" <?php if ($mes == '09') { echo "selected=selected"; }?>>Setembro</option>
                        <option value="10" <?php if ($mes == '10') { echo "selected=selected"; }?>>Outubro</option>
                        <option value="11" <?php if ($mes == '11') { echo "selected=selected"; }?>>Novembro</option>
                        <option value="12" <?php if ($mes == '12') { echo "selected=selected"; }?>>Dezembro</option>
                    </select>
      </td>
      <td> 
      	<select name='ano'>
        	<option value='2011' <?php if ($ano == '2011') { echo "selected=selected"; }?>>2011</option>
            <option value='2012' <?php if ($ano == '2012') { echo "selected=selected"; }?>>2012</option>
        </select>
      </td>
      <td><input type="submit" value="Mudar de mês" /></td></tr>             
   </table>
   </form>
   
   
   <?php
   		
   		
   		$datainicio = $ano."-".$mes."-01";
		$datafim = $ano."-".$mes."-31";
   
   		$con = mysql_connect("localhost","sipsadmin", "sipsps2012");
				if (!$con)
				{
					die('Não me consegui ligar' . mysql_error());
				}
				mysql_select_db("asterisk", $con);
		
		$dbusers = mysql_query("SELECT DISTINCT user FROM vicidial_user_log WHERE event_date > '$datainicio' AND event_date < '$datafim' ORDER BY user") or die(mysql_error());
		
		echo "<table align='center'>";
		echo "<tr><td align='center'>Utilizador</td><td align='center'>Nº de faltas</td></tr>";
		
		
		
		$diasuteis = calculateWorkingDaysInMonth($ano, $mes);
		
		for ($i=0;$i<mysql_num_rows($dbusers);$i++){		
		$dbu = mysql_fetch_assoc($dbusers);
		$user = $dbu['user'];
		$pres = 0;
		
			for ($a=1;$a<31;$a++){
			$idata = $ano."-".$mes."-".$a;
			$fdata = $ano."-".$mes."-".($a+1);
			
			$presenca = mysql_query("SELECT event_date FROM vicidial_user_log WHERE user='$user' AND event_date > '$idata' AND event_date < '$fdata'"); 
			$date = mysql_fetch_assoc($presenca);
			$b = $date['event_date'];
			
			if ( $b !== NULL) { $pres = $pres +1; }
		
			}
			
			
			$faltas = $diasuteis - $pres;
			
			echo "<tr><td align='center'>";
			echo "<a href='presencas.php?user=".$user."&mes=".$mes."&ano=".$ano."' target='_self'>".$user."</a>";
			echo "</td><td align='center'>";
			echo $faltas;
			echo "</td></tr>";
		
			
		}
		
		echo "</table>";
				
   	mysql_close($con);
   ?>
    
    
    
    </td>
</tr>
</table>

</body>
</html>
