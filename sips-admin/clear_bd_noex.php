<?php
require("dbconnect.php");
if (isset($_POST['stage'])) {
	$stage=$_POST['stage'];
}elseif (isset($_GET['stage'])){
	$stage=$_GET['stage'];
}
	
if (isset($_POST['noex_bd_id'])){
	$noex_bd_id=$_POST['noex_bd_id'];
if($noex_bd_id == 0){
	mysql_query("UPDATE LOW_PRIORITY vicidial_list INNER JOIN vicidial_carrier_log ON vicidial_list.lead_id = vicidial_carrier_log.lead_id SET vicidial_list.status='NAOEX' WHERE hangup_cause='1';",$link) 
	OR die (mysql_error());
	echo mysql_affected_rows();
	}
else{	
	mysql_query("UPDATE LOW_PRIORITY vicidial_list INNER JOIN vicidial_carrier_log ON vicidial_list.lead_id = vicidial_carrier_log.lead_id SET vicidial_list.status='NAOEX' WHERE hangup_cause='1' AND list_id = ".mysql_real_escape_string($noex_bd_id).";",$link) 
	OR die (mysql_error());
	echo mysql_affected_rows();
	}
exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>SIPS - Sistemas de Informação da PuroSinónimo</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-1.7.2.js"></script>
<script>
	function limpa_bd_noex(id_BD, nome_BD){
		if (id_BD == 0){
			var rsp=confirm('De certeza que quer remover os números \"Não atribuídos\" de todas as bases de dados?');
			}
		else{
			var rsp=confirm('De certeza que quer remover os números \"Não atribuídos\" desta base de dados? (BD: ' + id_BD + ' - ' + nome_BD + ')');
			}	
		if (rsp){
			var jqxhr = $.post("clear_bd_noex.php",{ noex_bd_id: id_BD})
		   	.error(function() { 
		   		alert("Sucedeu-se um erro, tente novamente mais tarde."); 
		   		})
		   	.success(function(returned_data) {
		   		alert("A Base de Dados foi limpa com sucesso. \nForam eliminados "+returned_data+" contactos.\n\n");
		   		document.reload();
		   		});
		}	
	} 	
</script>
</head>
<body>

<?php

	$LISTlink='stage=LISTIDDOWN';
	$NAMElink='stage=LISTNAMEDOWN';
	$TALLYlink='stage=TALLYDOWN';
	$ACTIVElink='stage=ACTIVEDOWN';
	$CAMPAIGNlink='stage=CAMPAIGNDOWN';
	$CALLDATElink='stage=CALLDATEDOWN';
	$SQLorder='order by list_id';
	if (eregi("LISTIDUP",$stage))		{$SQLorder='order by list_id asc';				$LISTlink='stage=LISTIDDOWN';}
	if (eregi("LISTIDDOWN",$stage))		{$SQLorder='order by list_id desc';				$LISTlink='stage=LISTIDUP';}
	if (eregi("LISTNAMEUP",$stage))		{$SQLorder='order by list_name asc';			$NAMElink='stage=LISTNAMEDOWN';}
	if (eregi("LISTNAMEDOWN",$stage))	{$SQLorder='order by list_name desc';			$NAMElink='stage=LISTNAMEUP';}
	if (eregi("TALLYUP",$stage))		{$SQLorder='order by tally asc';				$TALLYlink='stage=TALLYDOWN';}
	if (eregi("TALLYDOWN",$stage))		{$SQLorder='order by tally desc';				$TALLYlink='stage=TALLYUP';}
	if (eregi("ACTIVEUP",$stage))		{$SQLorder='order by active asc';				$ACTIVElink='stage=ACTIVEDOWN';}
	if (eregi("ACTIVEDOWN",$stage))		{$SQLorder='order by active desc';				$ACTIVElink='stage=ACTIVEUP';}
	if (eregi("CAMPAIGNUP",$stage))		{$SQLorder='order by campaign_id asc';			$CAMPAIGNlink='stage=CAMPAIGNDOWN';}
	if (eregi("CAMPAIGNDOWN",$stage))	{$SQLorder='order by campaign_id desc';			$CAMPAIGNlink='stage=CAMPAIGNUP';}
	if (eregi("CALLDATEUP",$stage))		{$SQLorder='order by list_lastcalldate asc';	$CALLDATElink='stage=CALLDATEDOWN';}
	if (eregi("CALLDATEDOWN",$stage))	{$SQLorder='order by list_lastcalldate desc';	$CALLDATElink='stage=CALLDATEUP';}
	
	$stmt=" SELECT vls.list_id, vls.list_name, vls.list_description, SUM( if( vcl.hangup_cause =1, 1, 0 ) ) tally, vls.active, vls.list_lastcalldate, vls.campaign_id, vls.reset_time
			FROM vicidial_lists vls INNER JOIN vicidial_list vl ON vls.list_id = vl.list_id
			INNER JOIN vicidial_carrier_log vcl ON vl.lead_id = vcl.lead_id
			WHERE vl.status != 'NAOEX' AND vcl.hangup_cause = 1 $LOGallowed_campaignsSQL GROUP BY vls.list_id $SQLorder";

	$rslt=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rslt);
	
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td><img src='../images/icons/database_delete_32.png' /></td>";
	echo "<td id='submenu-title'> Limpar Bases de Dados </td>";
	echo "<td style='text-align:left'>Listagem das Bases de Dados que se encontram com nºs invalidos no sistema. </td>";
	echo "<td id='submenu-title' > <span style='cursor:pointer;float:right;' onclick=limpa_bd_noex(0,''); > Limpar Todas <img src='../images/icons/database_delete_32.png' style='vertical-align:middle;' /> </span> </td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br>";
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	#echo "<th><a href=\"$PHP_SELF?$LISTlink\"> ID da BD </a></th>"; 
	echo "<th><a href=\"$PHP_SELF?$NAMElink\"> Nome da BD </a></th>";
	echo "<th> Descrição </th>";
/*	echo "<th> RTIME </th>"; */
	echo "<th> Nº de Contactos </a></th>";
	echo "<th><a href=\"$PHP_SELF?$ACTIVElink\"> Activa </a></th>";
	echo "<th><a href=\"$PHP_SELF?$CALLDATElink\"> Última Chamada </a></th>";
	echo "<th><a href=\"$PHP_SELF?$CAMPAIGNlink\"> Campanha </a></th>";
	echo "<th> Limpar números errados </th>"; 
	echo "</tr>";

	$lists_printed = '';
	$o=0;
	while ($lists_to_print > $o)
		{
		$row=mysql_fetch_row($rslt);
		#echo "<tr><td><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">$row[0]</a></td>"; 
		echo "<td> $row[1]</td>";
		echo "<td> $row[2]</td>";
		echo "<td> $row[3]</td>"; 
		
		if($row[4] == 'Y') {
			 echo "<td> <img src='../images/icons/tick_16.png' /></td>"; } 
		else {
			 echo "<td> <img src='../images/icons/cross_16.png' /></td>"; }
		
		echo "<td> $row[5]</td>";
		echo "<td> $row[6]</td>";
		echo "<td><span onclick=\"limpa_bd_noex(".$row[0].",'".$row[1]."')\"; ><img style=\"cursor:pointer\"; src=\"../images/icons/database_delete_32.png\" /></span></td></tr>"; 
		$lists_printed .= "'$row[0]',";
		$o++;
		}

?>
    <tr><th colspan=7></th></tr>
	</table>
	</div>
</body>
</html>


	