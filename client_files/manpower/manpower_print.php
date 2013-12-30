<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<style type="text/css">
.cc-mstyle {
 
	font-family: "Calibri";
	font-size: 12px; 
	background-color:#FFF;
	border: 2px solid #90C0E8;
	border-right: 2px solid #90C0E8;
	border-bottom: 2px solid #90C0E8;
	border-radius: 15px;


	margin-top:0;
	cursor:default;
}


.cc-mstyle label {
	color:#333;
}
.cc-mstyle table { 
	border:none; 
	padding:0;

	border-collapse: collapse;
	margin-left:auto;
	margin-right:auto;
}
.cc-mstyle tr:hover td {
	/* background-color:#e8edff; */
}
.cc-mstyle th {
	color: #000;
	font-size:12px;
	padding:10px 3px 10px 3px;
	width:auto;
}
.cc-mstyle th a {
	color: #000;
	font-size:12px;
	padding:2px 3px 2px 3px;
	text-decoration:none;
}
.cc-mstyle td {
	color: #000;
	font-size:12px;
	padding:2px 3px 2px 3px;
	width:auto;
	text-align:center;
}
.cc-mstyle td a {
	color: #000;
	font-size:12px;
	padding:0;
	text-decoration:none;
}


.cc-mstyle label {
	display:block;
	margin:0;
	padding:0px 0 0 0;
	margin:6px 0 -2px 0px; 
}
.cc-mstyle textarea {
	width:561px;
	border:1px solid #c0c0c0;
	margin:0;
	height:200px;
	background-color:#fff;
	resize:none;
	font:normal 12px/1.5em "Liberation sans", Arial, Helvetica, sans-serif;
}
.cc-mstyle input[type="text"] {
	width:400px;
	border:1px solid #c0c0c0;
	margin:0;
	height:28px;
	background-color:#fff;


}
.cc-mstyle input[type="password"] { 
	width:396px;
	border:1px solid #c0c0c0;
	margin:0;
	height:20px;
	background-color:#fff;


}

.cc-mstyle input[type="email"] { 
	width:396px;
	border:1px solid #c0c0c0;
	margin:0;
	height:20px;
	background-color:#fff;


}


.cc-mstyle select { 
	width:400px;
	padding: 4px;
	background-color: #FFF;
	border: 1px solid #c0c0c0;
	color: #000;
	height: 28px;
	width: 145px;
	margin-top:0px;
	text-align:center;
	font-size:12px;

}  
.cc-mstyle .checkbox { 
	margin: 4px 0; 
	padding: 0; 
	width: 14px;
	border: 0;
	background: none;
}
</style>
<?
	require('../../sips-admin/dbconnect.php');
	$lead_id = $_GET['id'];
	$query = "SELECT campaign_id FROM vicidial_campaigns";
	$query = mysql_query($query) or die(mysql_error());
	
	for($l=0;$l<mysql_num_rows($query);$l++)
	{
		$row=mysql_fetch_assoc($query);
		$query_a = "SELECT lead_id FROM custom_$row[campaign_id] where lead_id='$lead_id'";
		$query_a = mysql_query($query_a);
		if(mysql_num_rows($query_a)>0){$cur_camp=$row['campaign_id'];}
	}
	
	$query = "SELECT * FROM vicidial_list a INNER JOIN custom_$cur_camp b ON a.lead_id=b.lead_id where a.lead_id='$lead_id'";
	$query = mysql_query($query);
	$info = mysql_fetch_assoc($query);	

	$temp1 = explode(" ",$info['last_local_call_time']);
	$temp2 = explode("-",$temp1[0]);
	$m_data = $temp2[2]."-".$temp2[1]."-".$temp2[0];
	$m_hora = $temp1[1];

	$temp1 = explode("-",$info['marcdata']);
	$r_data = $temp1[2]."-".$temp1[1]."-".$temp1[0];



	$datafim = date("Y-m-d", strtotime("+1 day".$_POST['data']));
	
	$query = "SELECT * FROM vicidial_list a INNER JOIN custom_05 b ON a.lead_id=b.lead_id WHERE a.lead_id='$lead_id' AND last_local_call_time >= '$data' AND last_local_call_time < '$datafim'";
	$query = mysql_query($query, $link);
	$row=mysql_fetch_assoc($query);
	
	#print_r($row);
	
	$query = "SELECT full_name FROM vicidial_users WHERE user='$row[user]'";
	$query = mysql_query($query, $link);
	$row_user = mysql_fetch_row($query);	
	
	$exp_data = explode(" ", $row['last_local_call_time']);
	$exp_data = explode("-", $exp_data[0]);
	$data_re = $exp_data[2]."-".$exp_data[1]."-".$exp_data[0];
	
	$exp_reuniao = explode("-", $row['reuniao']);
	$reuniao_re = $exp_reuniao[2]."-".$exp_reuniao[1]."-".$exp_reuniao[0];
	
	
/*	$campanha = "custom_".$curRecord['campaign_id'];
		
		$marcacao = mysql_query("SELECT * FROM $campanha WHERE lead_id = '$lead'") or die(mysql_error());
		$curM = mysql_fetch_assoc($marcacao); */
	
	?>  
<title>Reunião - <? echo $row['first_name']; ?></title>
</head>

<body> <!-- onload="window.print(); window.close();" -->
    
    
  

<table border=0 class="cc-mstyle" style='border:none; border-radius:0px; width:635px; table-layout:fixed'>
<tr>
  <td colspan="7" align="center"><img src="logo_manpower.jpg" width="298" height="86" /></td></tr>
<tr><td style='border:1px solid black;'><b>Marcação:</b></td><td colspan=2>Data: <? echo $m_data; ?></td><td></td><td colspan=2>Hora: <? echo $m_hora; ?></td><td>ID Reunião: <? echo $lead_id; ?></td></tr>
<tr><td></td></tr>
<tr><td style='border:1px solid black;'><b>Comercial:</b></td><td style="text-align:left;" colspan="7">__________________________________________</td></tr>
<tr><td></td></tr>
<tr><td style='border:1px solid black;'><b>Reunião:</b></td><td colspan=2>Data: <? echo $r_data; ?></td><td></td><td colspan=2>Hora: <? echo $info['marchora']; ?></td><td></td></tr>
<tr><td></td></tr>
<tr><td style='border:1px solid black;'><b>Cliente:</b></td><td style='text-align:left' colspan="7"><? echo $info['first_name']; ?></td>
<tr><td></td></tr>
<tr><td style='border:1px solid black;'><b>Responsável:</b></td><td  colspan="7" style='text-align:left'><? echo $info['iresponsavel']; ?></td></tr>
<tr><td></td></tr>
<tr><td style='border:1px solid black;'><b>Morada:</b></td><td colspan=7 style='text-align:left'><? echo $info['address1']; ?></td></tr>
<tr><td></td></tr>
<tr><td style='border:1px solid black;'><b>Cod. Postal:</b></td><td colspan=3><? echo $info['postal_code']; ?></td><td style='border:1px solid black;'><b>Localidade:</b></td><td colspan=3><? echo $info['city']; ?></td></tr>
<tr><td></td></tr>
<tr><td style='border:1px solid black;'><b>Telefone Fixo:</b></td><td colspan=3><? echo $info['phone_number']; ?></td><td style='border:1px solid black;'><b>Telemóvel:</b></td><td colspan=3><? echo $info['itelemovel']; ?></td></tr>
</table>
<br>
<table border=0 class="cc-mstyle" style='border:none; border-radius: 0px; width:635px; table-layout:fixed'>
<tr><td></td><td colspan="3" style='border:1px solid black;'><b>Serviços Actuais</b></td><td></td><td colspan="3" style='border:1px solid black;'><b>Serviços Propostos</b></td></tr>
<tr><td></td><td style='border:1px solid black;' colspan=2>Operador</td><td style='border:1px solid black;'>Valor</td><td></td><td style='border:1px solid black;' colspan=2>Operador</td><td style='border:1px solid black;'>Valor</td></tr>
<tr><td style='border:1px solid black;'>Telefone</td><td style='border:1px solid black;' colspan=2><? echo $info['clitelefone']; ?></td><td style='border:1px solid black;'><? echo $info['clitelefonevalor']; ?></td><td style='border:1px solid black;'>Telefone</td><td style='border:1px solid black;' colspan=2><? echo $info['protelefone']; ?></td><td style='border:1px solid black;'><? echo $info['protelefonevalor']; ?></td></tr>
<tr><td style='border:1px solid black;'>Telemóvel</td><td style='border:1px solid black;' colspan=2><? echo $info['clitelemovel']; ?></td><td style='border:1px solid black;'><? echo $info['clitelemovelvalor']; ?></td><td style='border:1px solid black;'>Telemóvel</td><td style='border:1px solid black;' colspan=2><? echo $info['protelemovel']; ?></td><td style='border:1px solid black;'><? echo $info['protelemovelvalor']; ?></td></tr>
<tr><td style='border:1px solid black;'>Internet Fixa</td><td style='border:1px solid black;' colspan=2><? echo $info['cliinternetfixa']; ?></td><td style='border:1px solid black;'><? echo $info['cliinternetfixavalor']; ?></td ><td style='border:1px solid black;'>Internet Fixa</td><td style='border:1px solid black;' colspan=2><? echo $info['prointernetfixa']; ?><td style='border:1px solid black;'><? echo $info['prointernetfixavalor']; ?></td></td></tr>
<tr><td style='border:1px solid black;'>Internet Móvel</td><td style='border:1px solid black;' colspan=2><? echo $info['cliinternetmovel']; ?></td><td style='border:1px solid black;'><? echo $info['cliinternetmovelvalor']; ?></td><td style='border:1px solid black;'>Internet Móvel</td><td style='border:1px solid black;' colspan=2><? echo $info['prointernetmovel']; ?></td><td style='border:1px solid black;'><? echo $info['prointernetmovelvalor']; ?></td></tr>
</table>
<br>
<table class="cc-mstyle" style='border:none; border-radius: 0px; width:635px; table-layout:fixed'>
<tr><td style='border:1px solid black;' colspan=1><b>Ponto Ref.:</b></td><td style='border:1px solid black;' rowspan=3 colspan=7><? echo $info['pontoref']; ?></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
<br>
<table border=0 class="cc-mstyle" style='border:none; border-radius: 0px; width:635px; table-layout:fixed'>
<tr><td style='border:1px solid black;' colspan=1><b>Obs:</b></td><td style='border:1px solid black;' rowspan=3 colspan=7><? echo $info['obs']; ?></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
<br>
<table border=0 class="cc-mstyle" style='border:1px solid black; border-radius: 0px; width:635px; table-layout:fixed'>
<tr><td colspan=8><b>SUPERVISORA - Confirmação Reuniões</b></td></tr>
</table>
<table border=0 class="cc-mstyle" style='margin-top:8px; border:1px solid black; border-radius: 0px; width:635px; table-layout:fixed'>
<tr><td></td></tr>
<tr><td>___/___/___/</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
<tr><td></td></tr>
<tr ><td>___/___/___/</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
<tr><td></td></tr>
<tr><td>___/___/___/</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
<tr><td colspan=8>___________________________________________________________________________________________________</td></tr>
</table>




<?php 
/*	$seleccao = explode(',', $curM['servico']);
	
	//print_r($seleccao);
	for ($i=1; $i<15; $i++) {
		
		$cs = explode('-', $seleccao[$i-1]);
		$finalarray[$cs[0]] = $cs[1];
		if ($finalarray[$i] != NULL) { 
		
		$total = $total + $finalarray[$i];
		
		echo "<tr><td>".$finalarray[$i]." € </td><td>Sugerido</td></tr>"; } else { echo "<tr><td></td><td>&nbsp;</td></tr>"; }
		
		
		
	}*/
	
?>


</body>
</html>