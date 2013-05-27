<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {$far.="../";}
define("ROOT", $far);
require(ROOT . "ini/header.php");


# ADD=100 display all lists ||| edited by: kant <=fag sadface... || retirei o 'RTIME' e 'ID' da tabela
##############################################################
/*$user_logado = $_SERVER['PHP_AUTH_USER'];
			$grupos = mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups INNER JOIN vicidial_users ON vicidial_user_groups.user_group = vicidial_users.user_group WHERE user = '$user_logado'",$link) or die(mysql_error());

	$row=mysql_fetch_row($grupos);
	$LOGallowed_campaigns = $row[0];
  
    
    $rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
  
  	$campanhas = (strlen($rawLOGallowed_campaignsSQL))?"WHERE campaign_id IN ($rawLOGallowed_campaignsSQL)":"";
 */



$allowed_camp="";
			$current_admin = $_SERVER['PHP_AUTH_USER'];
			$query = mysql_query("select user_group from vicidial_users where user='$current_admin'") or die(mysql_error());
       		$query = mysql_fetch_assoc($query);
			$usrgrp = $query['user_group'];
			$stmt="SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$usrgrp';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGallowed_campaigns = $row[0];
			$LOGallowed_reports =	$row[1];
		
			$whereLOGallowed_campaignsSQL='';
			if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
				{
				$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
				$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
				$whereLOGallowed_campaignsSQL = "and vc.campaign_id IN('$rawLOGallowed_campaignsSQL')";
				$allowed_camp2 = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
				}
			$allowed_camp = $whereLOGallowed_campaignsSQL;

	$stmt="SELECT vls.list_id,list_name,list_description,count(*) as tally,vls.active,list_lastcalldate,campaign_name,reset_time from vicidial_lists vls,vicidial_list vl,vicidial_campaigns vc where vls.list_id=vl.list_id AND vls.campaign_id=vc.campaign_id $allowed_camp group by list_id";
    
   
    
	$rslt=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rslt);
	?>
	
	<div class=cc-mstyle>
	<table>
	<tr>
	<td id='icon32'><img src='/images/icons/database_table.png' /></td>
	<td id='submenu-title'> Bases de Dados </td>
	<td style='text-align:left'>Listagem das Bases de Dados que se encontram carregadas no sistema. </td>
	</tr>
	</table>
	</div>
	<br>
	
	<div id=work-area >
	
	<table id='lists'>
	<thead>
	<tr>
	<th> Nome da BD </th>
	<th> Descrição </th>
	<th> Nº de Contactos </th>
	<th> Activa </th>
	<th> Última Chamada </th>
	<th> Campanha </th>
	<th> Editar </th>
	</tr>
	</thead>
	<tbody>
<?php
	$lists_printed = '';
	$o=0;
	while ($lists_to_print > $o)
		{
		$row=mysql_fetch_row($rslt);
		echo "<td> $row[1]</td>";
		echo "<td> $row[2]</td>";
		echo "<td> $row[3]</td>"; 
		
		if($row[4] == 'Y') { echo "<td> <img src='/images/icons/tick_16.png' /></td>"; } else {  echo "<td> <img src='/images/icons/cross_16.png' /></td>"; }
		
		echo "<td> $row[5]</td>";
		echo "<td> $row[6]</td>";
		echo "<td><a href=\"admin_list_edit.php?list_id=".urlencode($row[0])."\"><img src=\"/images/icons/livejournal.png\" /></a></td></tr>"; 
		

		$o++;
            if($lists_to_print == $o)
            {
             $lists_printed .= "'$row[0]'";   
            }
            else
            {
              $lists_printed .= "'$row[0]',";  
            }
		}
		
		
		if ($lists_printed=='' || $lists_printed==NULL) {
	$stmt="SELECT list_id,list_name,list_description,0,active,list_lastcalldate,campaign_id,reset_time from vicidial_lists $allowed_camp2;"; } 
        else { 
	$stmt="SELECT list_id,list_name,list_description,0,active,list_lastcalldate,campaign_id,reset_time from vicidial_lists vc where list_id NOT IN($lists_printed) $allowed_camp;"; }
	
    
    $rslt=mysql_query($stmt, $link) or die(mysql_error());
	$lists_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($lists_to_print > $o)
		{
		$row=mysql_fetch_row($rslt);
		echo '<tr>';
		echo "<td> $row[1]</td>";
		echo "<td> $row[2]</td>";
		echo "<td> $row[3]</td>"; 
		
		if ($row[4] == 'Y') { $row[4] = "<img src=\"/images/icons/tick_16.png\" />";} else { $row[4] = "<img src=\"/images/icons/cross_16.png\" />"; }
		
		
		echo "<td> $row[4]</td>";
		
		if ($row[5] == NULL ) { $row[5] = "<font color=grey>Sem Registo</font>"; }
		
		echo "<td> $row[5]</td>";
		echo "<td> $row[6]</td>";
		echo "<td><a href=\"admin_list_edit.php?list_id=".urlencode($row[0])."\"><img src=\"/images/icons/livejournal.png\" /></a></a></td>"; 
		echo '</tr>';
                $o++;
		}
	
?>
	</tbody>
	</table>
	</div>
<script>
 var otable = $('#lists').dataTable({
     "bJQueryUI": true,
     "sDom": 'l<"top"f>rt<"bottom"p>',
     "sPaginationType": "full_numbers",
     "aoColumns": [{
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": true,
         "sType": "string"
     }, {
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": false
     }],
     "oLanguage": {
         "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
     }  
 });
</script>
</BODY>
</html>