
<?
#HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
 ?>

<script>
function dadoscli(valor) 
{

var url = "manpower_print.php?id=" + valor;

window.open(url, '_blank');
}
</script>

<?
	
	
	if (isset($_POST['data'])) { $data = $_POST['data']; } else { $data = date('Y-m-d'); }
	$datafim = date("Y-m-d", strtotime("+1 day".$_POST['data']));
	
	$query = "show tables like 'custom_%'";
	$query = mysql_query($query) or die(mysql_error());

	for($l=0;$l<mysql_num_rows($query);$l++)
	{
		$row = mysql_fetch_array($query);
		$camp_id=str_replace("CUSTOM_","",strtoupper($row[0]));
		
		$left_joins .= " LEFT JOIN custom_$camp_id ON vicidial_list.lead_id=custom_$camp_id.lead_id ";
		$marc_data .= " custom_$camp_id.marcdata as data_$camp_id,";
		$marc_hora .= " custom_$camp_id.marchora as hora_$camp_id,";
	}	
	//echo $left_joins;
	$stmt = "
			SELECT		$marc_hora $marc_data user, first_name, phone_number, vicidial_list.lead_id
			FROM		vicidial_list 
			
			$left_joins
			
			WHERE 		(status='Marc' OR status='s_Administradores_7')
			AND 		last_local_call_time >= '$data' 
			AND 		last_local_call_time < '$datafim'";
			
			//echo $stmt;
			
	$query_build = mysql_query($stmt) or die (mysql_error());
	
	for($l=0;$l<mysql_num_rows($query_build);$l++)
	{
		$row=mysql_fetch_assoc($query_build);
		
		foreach($row as $key=>$value)
		{
			//echo $key."-".$value."<br>";
			if( (eregi("data",$key)) && ($value!=NULL)) {$f_data[$l] = $value;}
			if( (eregi("hora",$key)) && ($value!=NULL)) {$f_hora[$l] = $value;}
			if( (eregi("first_name",$key)) && ($value!=NULL)) {$f_nome[$l] = $value;}
			if( (eregi("phone_number",$key)) && ($value!=NULL)) {$f_tlf[$l] = $value;}
			if( (eregi("user",$key)) && ($value!=NULL)) {$f_user[$l] = $value;}
			
		}
		$lead_id[$l] = $row['lead_id'];
		
	}
	
/*	$query = "SELECT list_id from vicidial_lists where campaign_id='05'";
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
	
	#$query = "SELECT a.lead_id, user, first_name, status, reuniao, hora, phone_number from vicidial_list a inner join custom_05 b on a.lead_id=b.lead_id where list_id IN($dbs_IN) and status='VENDA' AND last_local_call_time >= '$data' AND last_local_call_time < '$datafim'";
	#$query = mysql_query($query) or die (mysql_error());

	
	
	#$query = mysql_query("SELECT vicidial_list.lead_id, vicidial_list.campaign_id, call_date, vicidial_log.phone_number, vicidial_log.user, vicidial_list.status FROM vicidial_list RIGHT JOIN vicidial_log on vicidial_list.lead_id = vicidial_log.lead_id WHERE call_date >= '$data' AND call_date < '$datafim' AND vicidial_log.status IN ('VENDA') and vicidial_log.campaign_id='05'") or die(mysql_error());
	
	#$query = "SELECT c.letra, c.reuniao, c.hora, c.faleicom, c.decisor, c.contactomovel, c.pontoref, c.operadorfixo, c.operadorfixovalor, c.operadornetfixa, c.operador";
	*/
	?>

<? 
	
	
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../../images/icons/to_do_list_32.png' /></td>";
	echo "<td id='submenu-title'>Reuniões Manpower </td>";
?>
<form name="report" action="manpower_reunioes.php" target="_self" method="post">
<td>Escolha o dia:</td>
<td><input type="date" name='data' id='data' value='<? echo $data; ?>' />

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
<th colspan="6">Total de marcações: <? echo $l; ?></th>
<tr><th>Lead ID</th><th>Operador</th><th>Cliente</th><th>Telefone</th><th>Data Reunião</th><th>Hora Reunião</th><th>Ver Folha de Marcação</th></tr>

	<? for ($i=0; $i<mysql_num_rows($query_build); $i++) {

		echo "<tr><td>".$lead_id[$i]."</td><td>".$f_user[$i]."</td><td>".$f_nome[$i]."</td><td>".$f_tlf[$i]."</td><td>".$f_data[$i]."</td><td>".$f_hora[$i]."</td><td><input style='cursor:pointer' type='image' src='../../images/icons/to_do_list_32.png' value='Ver' onclick=dadoscli('".$lead_id[$i]."') /></td></tr>";
		
	}
	?>



</table>
</div>
<script>
$(function(){
$("#data").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"yy-mm-dd",
            showOn: "button",
	buttonImage: "/images/icons/date_32.png",
            buttonImageOnly: true});
});
</script>
</body>
</html>
