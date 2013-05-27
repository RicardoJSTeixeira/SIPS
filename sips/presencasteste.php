<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Controlo de Faltas</title>
<style type="text/css">
body td {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
</style>
</head>

<body>
<?php $mes = $_POST['mes']; 
		if ($mes == NULL) { $mes = date('m'); }
?>
   <td width="100%" align="left">
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
      <td><input type="submit" value="Mudar de mês" /></td></tr>             
   </table>
   </form>
   	<?php
	
	$user = $_GET['user'];
	
	$con = mysql_connect("localhost","root","admin");
	
	if (!$con)
	{ die('Não me consegui ligar' . mysql_error()); }
	mysql_select_db("asterisk", $con);
	
	
	$ano = date('o');
	$semidata = $ano."-".$mes."-";
	$faltas = 0;
	
	echo "<table border='1' align='center'><tr>";
	for ($i=1; $i<31; $i++) {
	
	$datainicio = $ano."-".$mes."-".$i;
	$diafim = $i + 1;
	$datafim = $ano."-".$mes."-".$diafim;
	
	$login = mysql_query("SELECT * FROM vicidial_user_log WHERE event_date > '$datainicio' AND event_date < '$datafim' AND user = '$user' AND event='LOGIN' ORDER BY event_epoch") or die(mysql_error());
	$rlogin = mysql_fetch_assoc($login);
	
	if ($rlogin['user'] == "") { $presenca[$i] = "Falta"; } else { $presenca[$i] = "Presente"; }
	
	
	
	echo "<td height='120' width='120'";
	
	$estedia = $semidata.$i;
	
	$h  = mktime(0, 0, 0, $mes, date($i), date("Y"));
	$d = date("F dS, Y", $h) ;
	$w= date("l", $h);
	
	if ($w == "Sunday" || $w == "Saturday") {
		echo "bgcolor='#CCC'"; 
		echo ("align='center'><br><br>".$w."<br>".$d."</td>"); }
		 
	else{
		if ($mes == date('m')){
			if ($i > date('d')) { echo (" align='center'><br><br>".$w."<br>".$d."</td>"); }
			else {
				if ($presenca[$i] == 'Falta') { echo "bgcolor='#FF0000'"; $faltas = $faltas +1; }
				echo (" align='center'>".$presenca[$i]."<br><br>".$w."<br>".$d."</td>"); 
				}
			}
		
			else { 
			 
				if ($presenca[$i] == 'Falta') { echo "bgcolor='#FF0000'"; $faltas = $faltas +1; }
				echo (" align='center'>".$presenca[$i]."<br><br>".$w."<br>".$d."</td>"); 
			}
		}
	
	
	if ($i == 7 || $i == 14 || $i == 21 || $i == 28) { echo "</tr><tr>"; }
	
	}
	echo "</tr></table>";
	mysql_close($con);				
?>			
<br />
<table align="center">
<tr>
	<td>Nº de Faltas total</td>
    <td><?php echo $faltas ?></td>
</tr>
</table>
		


</body>
</html>