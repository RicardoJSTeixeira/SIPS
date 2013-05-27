<?php
############################################################################################
####  Name:             g_confirmation.php                                              ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

if 	(isset($_GET["evnt_cmd"]))			{$evnt_cmd=$_GET["evnt_cmd"];}
	elseif 
	(isset($_POST["evnt_cmd"]))			{$evnt_cmd=$_POST["evnt_cmd"];}
	
if 	(isset($_GET["confirm_msg"]))		{$confirm_msg=$_GET["confirm_msg"];}
	elseif 
	(isset($_POST["confirm_msg"]))		{$confirm_msg=$_POST["confirm_msg"];}	
	
if 	(isset($_GET["evnt_script"]))		{$evnt_script=$_GET["evnt_script"];}
	elseif 
	(isset($_POST["evnt_script"]))		{$evnt_script=$_POST["evnt_script"];}	
	
if 	(isset($_GET["evnt_arg"]))			{$evnt_arg=$_GET["evnt_arg"];}
	elseif 
	(isset($_POST["evnt_arg"]))			{$evnt_arg=$_POST["evnt_arg"];}			

if 	(isset($_GET["old_ip"]))			{$old_ip=$_GET["old_ip"];}
	elseif 
	(isset($_POST["old_ip"]))			{$old_ip=$_POST["old_ip"];}	
			
if 	(isset($_GET["new_ip"]))			{$new_ip=$_GET["new_ip"];}
	elseif 
	(isset($_POST["new_ip"]))			{$new_ip=$_POST["new_ip"];}			

if 	(isset($_GET["net_mask"]))			{$net_mask=$_GET["net_mask"];}
	elseif 
	(isset($_POST["net_mask"]))			{$net_mask=$_POST["net_mask"];}				

if 	(isset($_GET["net_gw"]))			{$net_gw=$_GET["net_gw"];}
	elseif 
	(isset($_POST["net_gw"]))			{$net_gw=$_POST["net_gw"];}			
		
if ($evnt_cmd=='ast_rel')
	{
		$confirm_msg = "Clique para actualizar o Asterisk Telephony.";
		$evnt_script = "g_ast_reload.php";
		$evnt_arg	 = "arg_ast_rel";
	}
	
if ($evnt_cmd=='ast_res')
	{
		$confirm_msg = "Clique para reinicializar o Asterisk Telephony.";	
		$evnt_script = "g_ast_restart.php";
		$evnt_arg	 = "arg_ast_res";		
	}	
	
if ($evnt_cmd=='ast_sta')
	{
		$confirm_msg = "Clique para Inicializar o Asterisk Telephony. ";	
		$evnt_script = "g_ast_start.php";
		$evnt_arg	 = "arg_ast_sta";			
	}
		
if ($evnt_cmd=='ast_sto')
	{
		$confirm_msg = "Clique para Parar o Asterisk Telephony. ";	
		$evnt_script = "g_ast_stop.php";
		$evnt_arg	 = "arg_ast_sto";			
	}		
	
if ($evnt_cmd=='ast_opt')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >OPTIMIZE</FONT></B> Asterisk/Vicidial Database Tables!</b></font>";	
		$evnt_script = "g_ast_optimize.php";
		$evnt_arg	 = "arg_ast_opt";			
	}		

if ($evnt_cmd=='ast_upd_ip')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >UPDATE</FONT></B> Database IP!</b></font>";	
		$evnt_script = "g_upd_ip.php";
		$evnt_arg	 = "arg_ast_upd_ip";			
	}		
	
if ($evnt_cmd=='net_eth0_upd')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >UPDATE</FONT></B> eth0 IP Configuration!</b></font>";	
		$evnt_script = "g_net_eth0_upd.php";
		$evnt_arg	 = "arg_net_eth0_upd";			
	}	

if ($evnt_cmd=='net_eth1_upd')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >UPDATE</FONT></B> eth1 IP Configuration!</b></font>";	
		$evnt_script = "g_net_eth1_upd.php";
		$evnt_arg	 = "arg_net_eth1_upd";			
	}				

if ($evnt_cmd=='net_res')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >RESTART</FONT></B> your network!</b></font>";	
		$evnt_script = "g_net_res.php";
		$evnt_arg	 = "arg_net_res";			
	}		
	
if ($evnt_cmd=='fire_restore')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >RESTORE</FONT></B> your firewall rules!</b></font>";	
		$evnt_script = "g_fire_restore.php";
		$evnt_arg	 = "arg_fire_restore";			
	}

if ($evnt_cmd=='httpd_restore')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >RESTORE</FONT></B> your folder access rules!</b></font>";	
		$evnt_script = "g_httpd_config_restore.php";
		$evnt_arg	 = "arg_httpd_restore";			
	}		

if ($evnt_cmd=='net_sta')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to check network <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >STATUS</FONT></B>!</b></font>";	
		$evnt_script = "g_net_sta.php";
		$evnt_arg	 = "arg_net_sta";			
	}		
	
if ($evnt_cmd=='my_res')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >RESTART</FONT></B> MySQL Database!</b></font>";	
		$evnt_script = "g_my_restart.php";
		$evnt_arg	 = "arg_my_res";			
	}	
	
if ($evnt_cmd=='apa_res')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >RESTART</FONT></B> Apache Webserver!</b></font>";	
		$evnt_script = "g_apa_res.php";
		$evnt_arg	 = "arg_apa_res";			
	}

if ($evnt_cmd=='apa_rel')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >RELOAD</FONT></B> Apache Webserver!</b></font>";	
		$evnt_script = "g_apa_rel.php";
		$evnt_arg	 = "arg_apa_rel";			
	}
	
if ($evnt_cmd=='ast_cti')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO | STOP accordingly to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >GO | STOP</FONT></B> ASTERISK realtime logs!</b></font>";	
		$evnt_script = "g_ast_cti.php";
		$evnt_arg	 = "arg_ast_cti";			
	}

if ($evnt_cmd=='sys_reb')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >REBOOT</FONT></B> your SYSTEM!</b></font>";	
		$evnt_script = "g_sys_reb.php";
		$evnt_arg	 = "arg_sys_reb";		
	}	
	
if ($evnt_cmd=='sys_shut')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >SHUTDOWN</FONT></B> your SYSTEM!</b></font>";	
		$evnt_script = "g_sys_shut.php";
		$evnt_arg	 = "arg_sys_shut";		
	}

if ($evnt_cmd=='my_backup')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to run systems <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >BACKUP</FONT></B>!</b></font>";	
		$evnt_script = "g_my_backup.php";
		$evnt_arg	 = "arg_my_backup";		
	}

if ($evnt_cmd=='fire_res')
	{
		$confirm_msg = "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>Click GO to <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED >RESTART</FONT></B> your firewall!</b></font>";	
		$evnt_script = "g_fire_res.php";
		$evnt_arg	 = "arg_fire_res";			
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>goaddons v1.0 © by  | GOAutoDial Inc.</title>
<script type="text/javascript" src="ajaxlib/ajaxtabs.js"></script>
<script type="text/javascript" src="ajaxlib/kamotetabs.js"></script>
<link href="csslib/gadi_content.css" rel="stylesheet" type="text/css">
<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<!--<div id="webtemp19">
<?#require("g_menu.php");?>
</div>
<br>
<!-- <div id="webtemp20">
</div> -->



<?		
$S="&nbsp;";
if ($evnt_cmd=='ast_cti')
		{
		
		
		echo "<div class=cc-mstyle>";
		echo "<table>";
		echo "<tr>";
		echo "<td id='icon32'><img src='../images/icons/report_32.png' /></td>";
		echo "<td id='submenu-title'> Relatório em Tempo Real </td>";
		echo "<td id=icon32><img style=cursor:pointer; onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', '$evnt_arg')\" src='../images/icons/clock_go_32.png' /></td>";
				echo "<td  style=text-align:left;><a href=# id='page1' name='GO' onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', '$evnt_arg')\"> Iniciar o Relatório </a>   </td>";
				echo "<td id=icon32><img style=cursor:pointer; onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', 'arg_ast_cti_stop')\" src='../images/icons/clock_stop_32.png' /></td>";
						echo "<td  style=text-align:left;><a href=# id='page1' name='STOP' onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', 'arg_ast_cti_stop')\"> Parar o Relatório </a></td>";


		echo "</tr>";
		echo "</table>";
		echo "</div>";
		
		echo "<span style=display:none id='span_loader'>&nbsp;</span>";
		

		
		
/*		echo "<TABLE class=mytableclass width=600px height=88px cellspacing=0 cellpadding=0>\n";
		echo "<form id='myForm' action='' method='get'>\n";			
		echo "<tr><td align=left> &nbsp;&nbsp;&nbsp;$confirm_msg$S<input id='page1' class='sicon' type='button' name='GO' value='GO' onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', '$evnt_arg')\">&nbsp;&nbsp;&nbsp;<input id='page1' class='sicons' type='button' name='STOP' value='STOP' onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', 'arg_ast_cti_stop')\"></td></tr>\n";
		echo "</form>\n";
		echo "</table>\n";*/
		}
		
if ($evnt_cmd=='ast_upd_ip')
		{
		echo "<TABLE class=mytableclass width=400px cellspacing=0 cellpadding=0>\n";
		echo "<form id='myForm' action='' method='get'>\n";
		echo "<tr ><td colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><b>&raquo;&nbsp;UPDATE DATABASE - IP ENTRIES</td></tr>\n";		
		echo "<tr ><td colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><b>&nbsp;</td></tr>\n";
		echo "<tr ><td  class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>Old Server IP: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><select id='old_ip' style='width: 100px; font-size: 11px; height: 19px;' size=1.9 name='old_ip'>\n";
		$stmt="SELECT server_id,server_ip from servers;";
				$rslt=mysql_query($stmt, $link);
			$rslt_to_print = mysql_num_rows($rslt);
				$rslt_list='';
				$o=0;
				while ($rslt_to_print > $o) 
					{
					$row=mysql_fetch_row($rslt);
					$rslt_list .= "<option value='$row[1]'>$row[0] - $row[1]</option>\n";
					$o++;
					}
		echo "$rslt_list";
		echo "<option selected='selected'>$row[1]</option>\n";
		echo "</select></td></tr>\n";			
		echo "<tr ><td  class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>New Server IP: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='new_ip'  style='margin-top: 5px; width: 100px; font-size: 11px;' name='new_ip' />\n";	
		echo "<input id='page1' class='sicon' type='button' name='GO' value='GO' onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', '$evnt_arg')\"></td></tr>\n";			
		echo "</form>\n";
		echo "</table>\n";
		}
		
if ($evnt_cmd=='net_eth0_upd')
		{
		if ( file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0") )
		{
		$ethconf = file("/etc/sysconfig/network-scripts/ifcfg-eth0");
		foreach ($ethconf as $ethline) 
			{
			$ethline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$ethline);
			if (ereg("^DEVICE", $ethline))
				{$ethdevicename = $ethline;   $ethdevicename = preg_replace("/.*=/","",$ethdevicename);}
			if (ereg("^BOOTPROTO", $ethline))
				{$ethbootproto = $ethline;   $ethbootproto = preg_replace("/.*=/","",$ethbootproto);}
			if (ereg("^DHCPCLASS", $ethline))
				{$ethdhcpclass = $ethline;   $ethdhcpclass = preg_replace("/.*=/","",$ethdhcpclass);}
			if (ereg("^HWADDR", $ethline))
				{$ethhwaddr = $ethline;   $ethhwaddr = preg_replace("/.*=/","",$ethhwaddr);}
			if (ereg("^IPADDR", $ethline))
				{$ethipaddr = $ethline;   $ethipaddr = preg_replace("/.*=/","",$ethipaddr);}
			if (ereg("^NETMASK", $ethline))
				{$ethnetmask = $ethline;   $ethnetmask = preg_replace("/.*=/","",$ethnetmask);}
			if (ereg("^ONBOOT", $ethline))
				{$ethonboot = $ethline;   $ethonboot = preg_replace("/.*=/","",$ethonboot);}	
			if (ereg("^GATEWAY", $ethline))
				{$ethgateway = $ethline;   $ethgateway = preg_replace("/.*=/","",$ethgateway);}	
			if (ereg("^TYPE", $ethline))
				{$ethtype = $ethline;   $ethtype = preg_replace("/.*=/","",$ethtype);}	
			}
		}			
		echo "<TABLE class='' width=400px cellspacing=0 cellpadding=0>\n";
		echo "<form id='myForm' action='' method='get'>\n";
		echo "<tr ><td colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><b>&raquo;&nbsp;CHANGE eth0 - IP CONFIGURATION:</td></tr>\n";	
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><b>&nbsp;</td></tr>\n";
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;eth0 Old IP: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='old_ip' style='margin-top: 5px; width: 100px; font-size: 11px; '  name='old_ip' value='$ethipaddr' disabled/></td></tr>\n";					
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;eth0 New IP: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='new_ip' style='margin-top: 5px; width: 100px; font-size: 11px; '  name='new_ip' /></td></tr>\n";	
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Netmask: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='net_mask' style='margin-top: 5px; width: 100px; font-size: 11px; '  name='net_mask' value='$ethnetmask'/></td></tr>\n";	
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gateway: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='net_gw' style='margin-top: 5px; width: 100px; font-size: 11px; '  name='net_gw' value='$ethgateway'/>\n";
		echo "<input id='page1' class='sicon' type='button' name='GO' value='GO' onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', '$evnt_arg')\"></td></tr>\n";			
		echo "</form>\n";
		echo "</table>\n";
		}				

if ($evnt_cmd=='net_eth1_upd')
		{
		if ( file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1") )
		{
		$ethconf = file("/etc/sysconfig/network-scripts/ifcfg-eth1");
		foreach ($ethconf as $ethline) 
			{
			$ethline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$ethline);
			if (ereg("^DEVICE", $ethline))
				{$ethdevicename = $ethline;   $ethdevicename = preg_replace("/.*=/","",$ethdevicename);}
			if (ereg("^BOOTPROTO", $ethline))
				{$ethbootproto = $ethline;   $ethbootproto = preg_replace("/.*=/","",$ethbootproto);}
			if (ereg("^DHCPCLASS", $ethline))
				{$ethdhcpclass = $ethline;   $ethdhcpclass = preg_replace("/.*=/","",$ethdhcpclass);}
			if (ereg("^HWADDR", $ethline))
				{$ethhwaddr = $ethline;   $ethhwaddr = preg_replace("/.*=/","",$ethhwaddr);}
			if (ereg("^IPADDR", $ethline))
				{$ethipaddr = $ethline;   $ethipaddr = preg_replace("/.*=/","",$ethipaddr);}
			if (ereg("^NETMASK", $ethline))
				{$ethnetmask = $ethline;   $ethnetmask = preg_replace("/.*=/","",$ethnetmask);}
			if (ereg("^ONBOOT", $ethline))
				{$ethonboot = $ethline;   $ethonboot = preg_replace("/.*=/","",$ethonboot);}	
			if (ereg("^GATEWAY", $ethline))
				{$ethgateway = $ethline;   $ethgateway = preg_replace("/.*=/","",$ethgateway);}	
			if (ereg("^TYPE", $ethline))
				{$ethtype = $ethline;   $ethtype = preg_replace("/.*=/","",$ethtype);}	
			}
		}			
		echo "<TABLE class='' width=400px cellspacing=0 cellpadding=0>\n";
		echo "<form id='myForm' action='' method='get'>\n";
		echo "<tr ><td colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><b>&raquo;&nbsp;CHANGE eth1 - IP CONFIGURATION:</td></tr>\n";	
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><b>&nbsp;</td></tr>\n";
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;eth1 Old IP: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='old_ip' style='margin-top: 5px; width: 100px; font-size: 11px; '  name='old_ip' value='$ethipaddr' disabled/></td></tr>\n";					
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;eth1 New IP: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='new_ip' style='margin-top: 5px; width: 100px; font-size: 11px; '  name='new_ip' /></td></tr>\n";	
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Netmask: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='net_mask' style='margin-top: 5px; width: 100px; font-size: 11px; '  name='net_mask' value='$ethnetmask'/></td></tr>\n";	
		echo "<tr ><td class='pfontt' colspan=0 align=left style='font-size: 12px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gateway: </td><td style='font-size: 11px;'><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK><input type='text' id='net_gw' style='margin-top: 5px; width: 100px; font-size: 11px; '  name='net_gw' value='$ethgateway'/>\n";
		echo "<input id='page1' class='sicon' type='button' name='GO' value='GO' onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', '$evnt_arg')\"></td></tr>\n";			
		echo "</form>\n";
		echo "</table>\n";
		}			
		
if (($evnt_cmd!='ast_upd_ip')&&($evnt_cmd!='ast_cti')&&($evnt_cmd!='net_eth0_upd')&&($evnt_cmd!='net_eth1_upd'))
		{
		
		if ($evnt_cmd=='net_sta')
	{
	echo "<table width=780px style='margin-bottom: -40px;'>\n";
	echo "<tr><td align=left>\n";
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))
	{
	$startme = exec('sudo -u root /etc/init.d/network status');
	if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)&&preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_ok.png'>";
	  		echo "&nbsp;<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>eth0 is currently <FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=GREEN ><B>RUNNING</B></font>!</font>";
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_ok.png'>";
	  		echo "&nbsp;<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>eth1 is currently <FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=GREEN ><B>RUNNING</B></font>!</font>";
		} 
		else 
		{
				if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_ok.png'>";
	  		echo "&nbsp;<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>eth0 <FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=GREEN ><B></B></font></font>";
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_no.png'>";
	  		echo "&nbsp;<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>eth1 <FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED ><B></b></font></B></font>";
	  		
		} 

	if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_no.png'>";
	  		echo "&nbsp;<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>eth0 is currently  <FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED ><B>NOT</b></font> running</B>!</font>";
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_ok.png'>";
	  		echo "&nbsp;<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>eth1 is currently <FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=GREEN ><B>RUNNING</B></font>!</font>";
		}
		}
		
	}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))   	
	{
	$startme = exec('sudo -u root /etc/init.d/network status');
	if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_ok.png'>";
	  		echo "&nbsp;<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>eth0<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=GREEN ><B></B></font></font>";
		} 
		else 
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_no.png'>";
	  		echo "&nbsp;<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=BLACK ><b>eth0<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'  COLOR=RED ><B></b></font></B></font>";
		}
		
	}
	echo "</td></tr>\n";
	echo "</table>\n";
} 

###### echos ########

	echo "<div class=cc-mstyle>";
	echo "<table>";
	echo "<tr>";
	echo "<td id='icon32'><img src='../images/icons/brick_32.png' /></td>";
	echo "<td id='submenu-title'> Consola Asterisk </td>";
	echo "<td style=width:60%></td>";
	echo "<td id='icon32'><img style=cursor:pointer; onclick=\"javascript:document.location='g_ast_cli.php'\" src='../images/icons/resultset_previous_32.png' /></td>";
	echo "<td onclick=\"javascript:document.location='g_ast_cli.php'\"  style='text-align:left; cursor:pointer;'>Voltar</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	
	echo "<div id=work-area>";
	echo "<br><br>";

	echo "<div class=cc-mstyle style='border:none;'>";
	echo "<table>";			
	
	echo "<form id='myForm' action='' method='get'>\n";	

	if ($evnt_arg == "arg_ast_sta") { $brick_used = " src='../images/icons/brick_go_32.png' "; }
	if ($evnt_arg == "arg_ast_sto") { $brick_used = " src='../images/icons/brick_delete_32.png' "; }
	if ($evnt_arg == "arg_ast_res") { $brick_used = " src='../images/icons/arrow_rotate_clockwise_32.png' "; }
	if ($evnt_arg == "arg_ast_rel") { $brick_used = " src='../images/icons/arrow_change_32.png' "; }	
	
	echo "<tr><td align=left> $confirm_msg </td></tr>";		
	echo "<tr><td><img id='page1' style='cursor:pointer'  name='GO' $brick_used onclick=\"javascript:loadintoIframe('myframe1', '$evnt_script', '$evnt_arg')\"></td></tr>";
		
	echo "</form>";
		echo "</table>";
		echo "</div>";
		}
		
if ($evnt_cmd=='ast_cti')
{
?>



<div align=center>
<iframe id="myframe1" src="about:blank" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="overflow:visible; scrollableborder:0px solid gray; border:1px solid gray; width:95%; max-width:900px; height: 100%; max-height:450px;"></iframe>
</div>
<?
}
else
{
?>
<br>

<div class=cc-mstyle style='border:none;'>
<table>

<tr>

<td><iframe id="myframe1" src="about:blank" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="overflow:visible; scrollableborder:0px solid gray; width:500px; min-height:100px; margin-left:auto; margin-right:auto;"></iframe></td>
<td style='width:150px'><span  id="span_loader">&nbsp;</span></td>

</tr>


</table>
</div>

<?
}
?>
</body>
</html>
