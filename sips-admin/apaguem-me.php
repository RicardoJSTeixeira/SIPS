<?php

$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");


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
	$stmt="SELECT vls.list_id,list_name,list_description,count(*) as tally,active,list_lastcalldate,campaign_id,reset_time from vicidial_lists vls,vicidial_list vl where vls.list_id=vl.list_id $LOGallowed_campaignsSQL group by list_id $SQLorder";
	echo $stmt;
	$rslt=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rslt);
	
	
	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../images/icons/database_table.png' /></td>";
	echo "<td id='submenu-title'> Bases de Dados </td>";
	echo "<td style='text-align:left'>Listagem das Bases de Dados que se encontram carregadas no sistema. </td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	
	


	
	echo "<br>";
	
	echo "<div class=cc-mstyle>";
	
	echo "<table>";
	echo "<tr>";
	echo "<th><a href=\"$PHP_SELF?ADD=100&$LISTlink\"> ID da BD </a></th>"; 
	echo "<th><a href=\"$PHP_SELF?ADD=100&$NAMElink\"> Nome da BD </a></th>";
	echo "<th> DescriÃ§Ã£o </th>";
/*	echo "<th> RTIME </th>"; */
	echo "<th> NÂº de Contactos </a></th>";
	echo "<th><a href=\"$PHP_SELF?ADD=100&$ACTIVElink\"> Activa </a></th>";
	echo "<th><a href=\"$PHP_SELF?ADD=100&$CALLDATElink\"> Ãšltima Chamada </a></th>";
	echo "<th><a href=\"$PHP_SELF?ADD=100&$CAMPAIGNlink\"> Campanha </a></th>";
	echo "<th> Editar </th>"; 
	echo "</tr>";

	$lists_printed = '';
	$o=0;
	while ($lists_to_print > $o)
		{
		$row=mysql_fetch_row($rslt);
			echo "<tr><td><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">$row[0]</a></td>"; 
		echo "<td> $row[1]</td>";
		echo "<td> $row[2]</td>";
	/*	echo "<td> $row[7]</td>"; */
		echo "<td> $row[3]</td>"; 
		
		if($row[4] == 'Y') { echo "<td> <img src='../images/icons/tick_16.png' /></td>"; } else {  echo "<td> <img src='../images/icons/cross_16.png' /></td>"; }
		
		echo "<td> $row[5]</td>";
		echo "<td> $row[6]</td>";
		echo "<td><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\"><img src=\"../images/icons/livejournal.png\" /></a></td></tr>"; 
		$lists_printed .= "'$row[0]',";
		$o++;
		}

	$stmt="SELECT list_id,list_name,list_description,0,active,list_lastcalldate,campaign_id,reset_time from vicidial_lists where list_id NOT IN($lists_printed'') $LOGallowed_campaignsSQL;";
	$rslt=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($lists_to_print > $o)
		{
		$row=mysql_fetch_row($rslt);
		
		echo "<tr><td><font size=1><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">$row[0]</a></td>"; 
		echo "<td> $row[1]</td>";
		echo "<td> $row[2]</td>";
	/*	echo "<td> $row[7]</td>"; */
		echo "<td> $row[3]</td>"; 
		
		if ($row[4] == 'Y') { $row[4] = "<img src=\"../images/icons/tick_16.png\" />";} else { $row[4] = "<img src=\"../images/icons/cross_16.png\" />"; }
		
		
		echo "<td> $row[4]</td>";
		
		if ($row[5] == NULL ) { $row[5] = "<font color=grey>Sem Registo</font>"; }
		
		echo "<td> $row[5]</td>";
		echo "<td> $row[6]</td>";
		echo "<td><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\"><img src=\"../images/icons/livejournal.png\" /></a></a></td></tr>"; 
		$o++;
		}
    echo "<tr><th colspan=7></th></tr>";
	echo "</table>";
	echo "</div>";

?>