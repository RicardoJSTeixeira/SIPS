<?
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {$far.="../";}
define("ROOT", $far);
require(ROOT . "ini/header.php");
?>

	<div class=cc-mstyle>
		<form name="users" action="faltas.php" target="_self">
		<table>
			<tr>
				<td id='icon32'><img src='../../images/icons/calendar.png' /></td>
				<td id='submenu-title'>  Colaboradores </td>


				

					<?php
			
					if (isset($_GET['data'])) {$data = $_GET['data'];} else {$data = date('o-m-d');}
					?> 
					
					<td>Escolha o dia:</td>
					<td><input type="date" name='data' id='data' value='<?= $data ?>' />
				

	<script language="JavaScript">
        $(function(){
            $("#data").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"yy-mm-dd"
        });
        })
	</script>
	
</td>

<td>
	<input type="submit" value="Pesquisar" />
</td>

</tr>
</table>
</form>
</div>

<br>


<?php
// Ligações às bases de dados #################################

        if (file_exists("/etc/astguiclient.conf")) {
            $DBCagc = file("/etc/astguiclient.conf");
            foreach ($DBCagc as $DBCline) {
                $DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/", "", $DBCline);
                if (ereg("^VARDB_server", $DBCline)) {
                    $VARDB_server = $DBCline;
                    $VARDB_server = preg_replace("/.*=/", "", $VARDB_server);
                }
            }
        } else {
            #defaults for DB connection
            $VARDB_server = 'localhost';
        }
        $con = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");
if (!$con)
{
	die('Não me consegui ligar' . mysql_error());
}



$datafim = date("o-m-d", strtotime("+1 day".$data)); 
$datainicio = $data;


mysql_select_db("asterisk", $con);
mysql_query("SET NAMES utf8");


$current_admin = $_SERVER['PHP_AUTH_USER'];
	
	   $query = mysql_query("select user_group from vicidial_users where user='$current_admin'") or die(mysql_error());
       $query = mysql_fetch_assoc($query);
	   if ($query['user_group']=="ADMIN") {
		  $ret= "";
	   }
	   else {
           $ret= "WHERE user_group='$query[user_group]'"; 
	   } 
          
          
$usergroups = mysql_query("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ") or die(mysql_error());
?>

<div class=datagrid style="margin-bottom: 20px">


<?php


for ($ii=0;$ii<mysql_num_rows($usergroups);$ii++) {

$rugroups= mysql_fetch_assoc($usergroups);
	$tabela="";
	$campaigns="";
	if (!eregi("ALL-CAMPAIGNS",$rugroups['allowed_campaigns'])) {
		$campaigns=" WHERE campaign_id IN ('".str_replace(" ", "','", trim($rugroups['allowed_campaigns']))."') ";
	}
	
		$tabela.= "<div align='center'><h2>$rugroups[user_group]</h2></div>\n";
		$tabela.= "<div style='overflow-x:auto;'><table>\n";
		$tabela.= "<thead><tr><th style=text-align:left;padding-left:40px;>User</th><th>Login</th><th>Logout</th>\n";
		
		
		
		$pausecodes=mysql_query("SELECT `pause_code`, `pause_code_name`, `max_time` FROM `vicidial_pause_codes` $campaigns GROUP BY `pause_code` ORDER BY `pause_code`") or die(mysql_error());
		$pausequery1="";
		$pausequery2="";
		$tempos="";
		for ($i=0; $i < mysql_num_rows($pausecodes); $i++) {
			$tmp=mysql_fetch_assoc($pausecodes); 
			$tempos[$i]=$tmp['max_time'];
			$tabela.= "<th>$tmp[pause_code_name]</th>\n";
			$pausequery1.=",IFNULL(a.`$tmp[pause_code]`,0) '$tmp[pause_code]' ";
			$pausequery2.=",SUM(IF (`sub_status`='$tmp[pause_code]',`pause_sec`,0)) '$tmp[pause_code]' ";
		}
		$tabela.="</thead><tbody>";
	
	$query= "SELECT b.`user`,b.`full_name`,IFNULL(c.`login`,'Falta') LOGIN, IFNULL(d.`LOGOUT`,'') LOGOUT
		$pausequery1
			FROM `vicidial_users` b LEFT JOIN 
			(SELECT user
		$pausequery2
			FROM `vicidial_agent_log`
		WHERE `event_time` between '$datainicio' AND '$datafim'
		GROUP BY `user`
			) a 
		ON a.`user`=b.`user` 
		LEFT JOIN
			(SELECT user,`event_date` LOGIN
			FROM `vicidial_user_log` WHERE `event_date` between '$datainicio' AND '$datafim' AND event='LOGIN' ORDER BY event_epoch ASC ) c
		ON b.user=c.user
		LEFT JOIN
			(SELECT user,`event_date` LOGOUT
			FROM `vicidial_user_log` WHERE `event_date` between '$datainicio' AND '$datafim' AND event='LOGOUT' ORDER BY event_epoch DESC ) d
		ON b.user=d.user
		WHERE b.`user_group`='$rugroups[user_group]' AND b.`active`='Y' 
		GROUP BY b.`user`";
	//echo $query;
	
		$query= mysql_query($query) or die(mysql_error());
		
		if (mysql_num_rows($query)==0) {
			continue;
		}else
		{echo $tabela;}
		
	while($row = mysql_fetch_row($query)) { 
        echo "<TR>"; 
        for($column_num = 0; $column_num < mysql_num_fields($query); $column_num++) { 
         $red = ($row[$column_num]=="Falta" or ($column_num>3 AND $row[$column_num]>$tempos[$column_num-4] AND $tempos[$column_num-4]!=0)) ? " style=color:#ff8125 " : "" ;
		   
		   $rslt = ($column_num<4) ? $row[$column_num] : gmdate("i:s",$row[$column_num]) ;
		   
		    if ($column_num==0) {
				$useredit1="<span >";
				$useredit2="</span>";
				$align=" style='text-align:left;padding-left:20px' ";
				continue;
			}elseif($column_num==1)
			{}
			else
			{
				$useredit1="";
				$useredit2="";
				$align="";
			}
			
			
			
		    echo "<TD$red$align>$useredit1 $rslt $useredit2</TD>\n"; 
         } 
        echo "</TR>\n"; 
    } 
					
	
	
	echo "</tbody></table></div>";
			}
			
			mysql_close($con);
			?>
			

</div>
</body>
</html>
