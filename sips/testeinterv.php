<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>teste intervalos</title>
</head>

<body>

<?php
	
	$user = $_GET['user'];
	
	
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
	{
		die('Não me consegui ligar' . mysql_error());
	}
	mysql_select_db("asterisk", $con);
	
	
	
	$dia = date('d');
	$mes = date('m');
	$ano = date('o');
	
	
	echo $user;
	
	for ($i=0;$i<5;$i++)
	{
	$diainicio = ($dia - $i);
	$diafim = ($dia -$i + 1);
	
	$datainicio = $ano."-".$mes."-".$diainicio;
	$datafim = $ano."-".$mes."-".$diafim;
	
	
	
	$interv = mysql_query("SELECT Sum(pause_sec) FROM vicidial_agent_log WHERE event_time > '$datainicio' AND event_time < '$datafim' AND user = ".$user." AND sub_status='Interv'") or die(mysql_error());
	
	
	
					
	$rinterv = mysql_fetch_assoc($interv);
	$intervalo[$i] = number_format($rinterv['Sum(pause_sec)'] / 60, 0);
	
	}
	
	$dia = date('d');
	
	
	mysql_close($con);
?>


<table width="200" border="0">
  <tr>
    <td>Dia <?php echo ($dia - 4); ?></td>
    <td <?php if($intervalo[4]>20) { echo ("bgcolor='#FF0000'"); } ?>><?php echo $intervalo[4]; ?></td>
  </tr>
  <tr>
    <td>Dia <?php echo ($dia - 3); ?></td>
    <td><?php echo $intervalo[3]; ?></td>
  </tr>
  <tr>
    <td>Dia <?php echo ($dia - 2); ?></td>
    <td><?php echo $intervalo[2]; ?></td>
  </tr>
  <tr>
    <td>Dia <?php echo ($dia - 1); ?></td>
    <td><?php echo $intervalo[1]; ?></td>
  </tr>
  <tr>
    <td>Dia <?php echo ($dia); ?></td>
    <td><?php echo $intervalo[0]; ?></td>
  </tr>
</table>



</body>
</html>