<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Backoffice</title>
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
</head>

<body>
<table align="center" width="100%" border="1">
<tr valign="top">
    <td width="186">
        <iframe width="186" height="600" src="menu.html" frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0">
    </iframe> 
   </td>
   <td width="100%" align="left">
   		<form name="users" action="faltas.php" target="_self">
        <table align="center" width="100%">
        <?php
			$dia = $_GET['dia'];
			if ($dia == '') { $dia = date('d'); }
			$mes = $_GET['mes'];
			if ($mes == '') { $mes = date('m'); }
			$ano = $_GET['ano'];
			if ($ano == '') { $ano = date('o'); }
		?>
        	<tr>
            	<td>Escolha o dia:</td>
                <td><select name="dia">
                	<option value="1" <?php if ($dia == '01') { echo "selected=selected"; }?>>01</option>
                    <option value="2" <?php if ($dia == '02') { echo "selected=selected"; }?>>02</option>
                    <option value="3" <?php if ($dia == '03') { echo "selected=selected"; }?>>03</option>
                    <option value="4" <?php if ($dia == '04') { echo "selected=selected"; }?>>04</option>
                    <option value="5" <?php if ($dia == '05') { echo "selected=selected"; }?>>05</option>
                    <option value="6" <?php if ($dia == '06') { echo "selected=selected"; }?>>06</option>
                    <option value="7" <?php if ($dia == '07') { echo "selected=selected"; }?>>07</option>
                    <option value="8" <?php if ($dia == '08') { echo "selected=selected"; }?>>08</option>
                    <option value="9" <?php if ($dia == '09') { echo "selected=selected"; }?>>09</option>
                    <option value="10" <?php if ($dia == '10') { echo "selected=selected"; }?>>10</option>
                    <option value="11" <?php if ($dia == '11') { echo "selected=selected"; }?>>11</option>
                    <option value="12" <?php if ($dia == '12') { echo "selected=selected"; }?>>12</option>
                    <option value="13" <?php if ($dia == '13') { echo "selected=selected"; }?>>13</option>
                    <option value="14" <?php if ($dia == '14') { echo "selected=selected"; }?>>14</option>
                    <option value="15" <?php if ($dia == '15') { echo "selected=selected"; }?>>15</option>
                    <option value="16" <?php if ($dia == '16') { echo "selected=selected"; }?>>16</option>
                    <option value="17" <?php if ($dia == '17') { echo "selected=selected"; }?>>17</option>
                    <option value="18" <?php if ($dia == '18') { echo "selected=selected"; }?>>18</option>
                    <option value="19" <?php if ($dia == '19') { echo "selected=selected"; }?>>19</option>
                    <option value="20" <?php if ($dia == '20') { echo "selected=selected"; }?>>20</option>
                    <option value="21" <?php if ($dia == '21') { echo "selected=selected"; }?>>21</option>
                    <option value="22" <?php if ($dia == '22') { echo "selected=selected"; }?>>22</option>
                    <option value="23" <?php if ($dia == '23') { echo "selected=selected"; }?>>23</option>
                    <option value="24" <?php if ($dia == '24') { echo "selected=selected"; }?>>24</option>
                    <option value="25" <?php if ($dia == '25') { echo "selected=selected"; }?>>25</option>
                    <option value="26" <?php if ($dia == '26') { echo "selected=selected"; }?>>26</option>
                    <option value="27" <?php if ($dia == '27') { echo "selected=selected"; }?>>27</option>
                    <option value="28" <?php if ($dia == '28') { echo "selected=selected"; }?>>28</option>
                    <option value="29" <?php if ($dia == '29') { echo "selected=selected"; }?>>29</option>
                    <option value="30" <?php if ($dia == '30') { echo "selected=selected"; }?>>30</option>
                    <option value="31" <?php if ($dia == '31') { echo "selected=selected"; }?>>31</option>
                    </select>
               </td>
               <td>
               		<select name="mes">
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
               		<select name="ano">
                        <option value="<?php echo date('o'); ?>"><?php echo date('o'); ?></option>
                    	<option value="2011">2011</option>
                        <option value="2012">2012</option>
                        <option value="2013">2011</option>
                    </select>
               </td>
               <td>
               		<input type="submit" value="Pesquisar" />
               </td>
        	</tr>
        <tr>
        
        	
            	<?php
				$con = mysql_connect("localhost","root","admin");
				if (!$con)
				{
					die('Não me consegui ligar' . mysql_error());
				}
				mysql_select_db("sips", $con);
				
				
				
				//if ($dia = "") {
//					$dia = date('d');
//					$mes = date('m');
//					$ano = date('o');
//				}
				
				$diafim = $dia + 1;
				
				$datainicio = $ano."-".$mes."-".$dia;
				$datafim = $ano."-".$mes."-".$diafim;
				
				$dbusers = mysql_query("SELECT DISTINCT uservici, htrab FROM t_colaborador WHERE activo = '1' ORDER BY htrab ASC") or die(mysql_error());
				
				echo "<td align='center'>Horário</td><td align='center'>User</td><td align='center'>Login</td><td align='center'>Logout</td><td align='center'>Minutos Intervalo</td>";
                
				mysql_select_db("asterisk", $con);
				
				$tp = 0;
				$sem1 = true;
				$sem2 = true;
				
				for ($i=0;$i<mysql_num_rows($dbusers);$i++) {
					$ruser = mysql_fetch_assoc($dbusers);
					$user = $ruser['uservici'];
					
					$login = mysql_query("SELECT * FROM vicidial_user_log WHERE event_date > '$datainicio' AND event_date < '$datafim' AND user = '$user' AND event='LOGIN' ORDER BY event_epoch") or die(mysql_error());
					$rlogin = mysql_fetch_assoc($login);
					
					$logout = mysql_query("SELECT * FROM vicidial_user_log WHERE event_date > '$datainicio' AND event_date < '$datafim' AND user = '$user' AND event='LOGOUT' ORDER BY event_epoch DESC") or die(mysql_error());
					
					$rlogout = mysql_fetch_assoc($logout);
					
					$interv = mysql_query("SELECT Sum(pause_sec) FROM vicidial_agent_log WHERE event_time > '$datainicio' AND event_time < '$datafim' AND user = '$user' AND sub_status='Interv'") or die(mysql_error());
					
					$rinterv = mysql_fetch_assoc($interv);
					$intervalo = number_format($rinterv['Sum(pause_sec)'] / 60, 0);
					
					if ($ruser['htrab'] == 'noite' && $sem1 == true) {  $tm = $i; echo "<tr><td colspan='5' align='center' class='header'>Turno Intermédio: ".$tm." <br><br></td></tr>"; $sem1 = false; }
					
					echo "<tr>";
					echo "<td align='center'>".$ruser['htrab']."</td>";
					echo "<td align='center'>";
					echo "<a href='presencas.php?user=".$user."' target='_self'>".$user."</a>";
					echo "</td>";
					if ($rlogin['event_date'] == NULL) { echo "<td align='center' bgcolor='#FF0000'> FALTA"; } else { echo "<td align='center' >".$rlogin['event_date']; $tp = $tp + 1; }
					echo "</td><td align='center'>";
					echo $rlogout['event_date'];
					
					if ($intervalo > 20) { echo "</td><td align='center' bgcolor='#FF0000'>"; }
					else { echo "</td><td align='center'>"; }
					echo $intervalo;
					echo "</td></tr>";
					if ($i == (mysql_num_rows($dbusers) - 1)  && $sem2 == true) { $tn = $i - $tm + 1; echo "<tr><td colspan='5' align='center' class='header'>Turno da Noite: ".$tn."</td></tr>"; $sem2 = false; }
					
					//$login = "";
					//$rlogin = "";
					//$user = "";
				}
				
				mysql_close($con);
				?>
           
        </tr>
        <tr>
        	<td><?php echo "<br><br><br>";
				echo "total presencas: ".$tp; ?>
        </tr>
        </table>
        </form>
        </td>
</tr>
</table>

</body>
</html>
