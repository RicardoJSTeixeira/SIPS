<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SIPS - Gestão de Pausas</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<?php
require('dbconnect.php');
if (isset($_POST['navigation'])) {$navigation=$_POST['navigation'];}
elseif (isset($_GET['navigation'])) {$navigation=$_GET['navigation'];}
elseif (!isset($_GET['navigation']) && !isset($_POST['navigation'])) {$navigation='list_pause_codes';}
?>
</head>
<body>
<?php
	echo "<div class=cc-mstyle>";
	echo "<table border=0>";
	echo "<tr>";
	echo "<td id='td32'><img id='img32' onclick=\"window.location='sips_pause_codes.php'\" src='../images/icons/clock_pause_32.png' /></td>";
	
	echo "<td><div class='miniheader-titletext' onclick=\"window.location='sips_pause_codes.php'\"> Gestão de Pausas </div></td>";
	
	echo "<td id='icon16'><img id='icon24' onclick=\"window.location='sips_pause_codes.php?navigation=new_pause_code?navigation=new_pause_code'\" src='../images/icons/clock_add_32.png' /></td>";
	echo "<td style='text-align:left'>Inserir Nova Pausa no Sistema </td>";
	echo "<td id='icon16'><img width=24px height=24px src='../images/icons/clock_edit_32.png' /></td>";
	echo "<td style='text-align:left'> Editar Pausas de Sistema </td>";
	
	echo "</tr>";
	echo "</table>";
	echo "</div>";
if ($navigation=='list_pause_codes')
	{
		echo "<br>";
	
	echo "<div class=cc-mstyle>";
	
	echo "<table>";
	echo "<tr>";
	#echo "<th><B>ID da Campanha</B></th>";
	echo "<th><B>Nome da Campanha</B></th>";
	echo "<th><B>Pausas Associadas</B></th>";
	echo "<th><B>Alterar</B></th>";
	echo "</tr>";

		$query="SELECT campaign_id, campaign_name FROM vicidial_campaigns ORDER BY campaign_id";
		$query=mysql_query($query, $link);
		$campaigns_to_print = mysql_num_rows($query);

		$o=0;
		while ($campaigns_to_print > $o) 
			{
			$row=mysql_fetch_row($query);
			$campaigns_id_list[$o] = $row[0];
			$campaigns_name_list[$o] = $row[1];
			$o++;
			}

		$o=0;
		while ($campaigns_to_print > $o) 
			{

			echo "<tr>";
			#echo "<td>$campaigns_id_list[$o]</td>";
			echo "<td>$campaigns_name_list[$o]</td>";
			echo "<td>";

			$query="SELECT pause_code_name FROM vicidial_pause_codes WHERE campaign_id='$campaigns_id_list[$o]' ORDER BY pause_code_name;";
			$query=mysql_query($query, $link);
			$campstatus_to_print = mysql_num_rows($query);
			$p=0;
			while ( ($campstatus_to_print > $p) and ($p < 10) )
				{
				$row=mysql_fetch_row($query);
				echo "$row[0] | ";
				$p++;
				}
			if ($p<1) 
				{echo "<font color=grey>Sem Pausas Associadas</font>";}
			echo "</td>";
			echo "<td><img src='../images/icons/livejournal.png' /></td></tr>";
			$o++;
			}
	echo "<tr><th></th></tr>";
	echo "</table>";
	}
if ($navigation=='new_pause_code')
	{
		
	}
?>	
</body>
</html>