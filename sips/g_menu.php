<?php
############################################################################################
####  Name:             g_menu.php                                                      ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");

$version="1.0";
$build="0030";

if 	(isset($_GET["msg"]))			{$msg=$_GET["msg"];}
	elseif 
	(isset($_POST["msg"]))			{$msg=$_POST["msg"];}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <title>GoAutoDial Menu</title>
    <link type="text/css" href="csslib/gadi_menu.css" rel="stylesheet" />
    <script type="text/javascript" src="ajaxlib/jquery.js"></script>
    <script type="text/javascript" src="ajaxlib/gadi_menu.js"></script>
</head>
<body>
<div id="menu">
    <ul class="menu">
        <li><a href="#" class="parent"><span style="font-weight:bold;">Asterisk</span></a>
            <div><ul>
		<li><a href="#" onclick="self.frames.document.location.href='g_dir_select.php?evnt_type=ast_config'"><span>Edit Config Files</span></a></li>
                <li><a href="#" class="parent"><span>Function</span></a>
                    <div><ul>
                 	<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_ast_cli.php'"><span>Asterisk CLI</span></a></li>
			<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=ast_sta'"><span>Start</span></a></li>
			<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=ast_sto'"><span>Stop</span></a></li>
			<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=ast_res'"><span>Restart</span></a></li>
			<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=ast_rel'"><span>Reload</span></a></li>   
                    </ul></div>
                </li>
		<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=ast_cti'"><span>Realtime Logs</span></a></li>
		<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_ast_stats_cti.php?status_type=01'"><span>Status</span></a></li>
            </ul></div>
        </li>
        <li><a href="#" class="parent"><span style="font-weight:bold;">Apache</span></a>
            <div><ul>
		<li><a tabindex="-1" class="MenuBarItemSubmenu" href="#"><span>Folder Access</span></a>
		    <div><ul>
                      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_httpd_config_recording.php'"><span>Recordings</span></a></li>
                      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_httpd_config_backup.php'"><span>Backup</span></a></li>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=httpd_restore'"><span>Restore Default</span></a></li>    
		    </ul></div>
		</li>
                <li><a tabindex="-1" class="MenuBarItemSubmenu" href="#"><span>Function</span></a>
                    <div><ul>
                        <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=apa_res'"><span>Restart</span></a></li>    
			<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=apa_rel'"><span>Reload</span></a></li>                        
		    </ul></div>
		</li>
		<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_ast_stats_cti.php?status_type=03'"><span>Status</span></a></li>
                </li>
            </ul></div>
        </li>
	<li><a href="#" class="parent"><span style="font-weight:bold;">Mysql</span></a>
            <div><ul>
                <li><a tabindex="-1" class="MenuBarItemSubmenu" href="#"><span>Function</span></a>
                    <div><ul>
                        <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=my_res'"><span>Restart</span></a></li>
			<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=ast_opt'"><span>Optimize</span></a></li>
		    </li>
		    </ul></div>
		</li>
		<li><a tabindex="-1" class="MenuBarItemSubmenu" href="#" onclick="self.frames.document.location.href='g_ast_stats_cti.php?status_type=02'"><span>Status</span></a></li>
                </li>
            </ul></div>
        </li>
        <li><a href="#" class="parent"><span style="font-weight:bold;">System / Network</span></a>
            <div><ul>
		<li><a tabindex="-1" class="MenuBarItemSubmenu" href="#"><span>Backup</span></a>
		    <div><ul>
                      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_dir_select.php?evnt_type=db_backup'"><span>Browse Files</span></a></li>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=my_backup'"><span>Run System Backup</span></a></li>    
		    </ul></div>
		</li>
		<li><a tabindex="-1" class="MenuBarItemSubmenu" href="#"><span>Configuration</span></a>
		    <div><ul>
                 	<?
			if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))   	
				{
			?>      
				<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=net_eth0_upd'"><span>eth0 Config</span></a></li>
			<?}?>
			
			<?
			if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0")))   	
				{
			?>      
				<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=net_eth1_upd'"><span>eth1 Config</span></a></li>
			<?}?>
			
			<?
			if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))   	
				{
			?>
				<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=net_eth0_upd'"><span>eth0 Config</span></a></li>
				<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=net_eth1_upd'"><span>eth1 Config</span></a></li>
			<?}?>       	
				<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_dir_select.php?evnt_type=net_config'"><span>Edit Config Files</span></a></li>  
                    </ul></div>
		</li>
		<li><a tabindex="-1" class="MenuBarItemSubmenu" href="#"><span>Firewall</span></a>
                    <div><ul>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_fire_interfaces.php'"><span>Interfaces</span></a></li>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_fire_ruleset.php'"><span>Rule Set</span></a></li>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_fire_blocklist.php'"><span>Block List</span></a></li>
		       <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=fire_restore'"><span>Restore Default </span></a></li>  
		    </ul></div>
		</li>	
		<li><a href="#" class="parent"><span>Function</span></a>
                    <div><ul>
		       <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=net_res'"><span>Network Restart</span></a></li>
		       <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=sys_reb'"><span>System Reboot</span></a></li>
		       <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=sys_shut'"><span>System Shutdown</span></a></li>  
		       <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=fire_res'"><span>Firewall Restart</span></a></li>  
		    </ul></div>
                </li>
		<li><a tabindex="-1" class="MenuBarItemSubmenu" href="#"><span>Linux Shell</span></a>
                    <div><ul>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_konsole.php'"><span>gKonsole v1.0</span></a></li>
		    </ul></div>
		</li>
		<li><a tabindex="-1" class="MenuBarItemSubmenu" href="#"><span>Status</span></a>
                    <div><ul>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_ast_stats_cti.php?status_type=04'"><span>Network Status</span></a></li>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_if_stats.php'"><span>Network Traffic</span></a></li>
		      <li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_sys_stats_cti.php'"><span>System Status</span></a></li>
		    </ul></div>
		</li>
		<li><a tabindex="-1" href="#" onclick="self.frames.document.location.href='g_confirmation.php?evnt_cmd=ast_upd_ip'"><span>Update Database IP</span></a></li>
            </ul></div>
        </li>
    </ul>
</div>
<div id="copyright">Copyright &copy; 2011 <a href="http://apycom.com/">Apycom jQuery Menus</a></div>
</body>
</html>
