<?php
############################################################################################
####  Name:             g_sys_stats_logs.php                                            ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");

if 	(isset($_GET["status_type"]))			{$status_type=$_GET["status_type"];}
	elseif 
	(isset($_POST["status_type"]))			{$status_type=$_POST["status_type"];}

function validateIpAddress($ip_addr)
{
  if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
  {
    $parts=explode(".",$ip_addr);
    foreach($parts as $ip_parts)
    {
      if(intval($ip_parts)>255 || intval($ip_parts)<0)
      return false; 
    }
    return true;
  }
  else
    return false; 
}

$hostp = $_SERVER['SERVER_ADDR'];
$hostn = $_SERVER['SERVER_NAME'];
$wwwabsolute=dirname(__FILE__);
$wwwroot='/var/www/html';
$realabsolute = str_replace($wwwroot, '', $wwwabsolute);
$realabsolute = $realabsolute.'/';
$phpsysinfo = 'phpsysinfo/index.php';

if (validateIpAddress($hostn))
{
$phpsysinfolink = "http://$hostn$realabsolute$phpsysinfo";
$hostn = exec('hostname');
}
else
{
$phpsysinfolink = "$phpsysinfo";
}

$s='&nbsp;';
$a='&raquo;';
?>
<head>
<link href="csslib/gadi_content.css" rel="stylesheet" type="text/css">
</head>
<?
###########################################################
#### ALL SYSTEM STATUS + PHPSYSINFO
###########################################################
if ($status_type=="ALL")
	{
?>
<div class='myfontb2'>
<? 
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/sbin/service asterisk status"');
 	if (preg_match("/\brunning\b/i", $startme))
	 	{	
			echo "$s$s<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s Asterisk";
  		}
 		else
		{
			echo "$s$s<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s Asterisk";
		}
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/mysqld status"');
	if (preg_match("/\brunning\b/i", $startme))
		{	
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s MySql";
  		}
 		else
		{
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s MySql";
  		}
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/httpd status"');
	if (preg_match("/\brunning\b/i", $startme))
	 
		{	
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s Apache";
  		}
 		else
		{
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s Apache";
  		}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)&&preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		} 
		else 
		{
		if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth1";
	  		
		} 
	if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth0";
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		}
	}
	}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))   	
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
		} 
		else
		{ 
			echo "<b  id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth0";
		}
	}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0")))   	
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "$s$s<b id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		} 
		else
		{ 
			echo "$s$s<b id='pfont2'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth1";
		}
	}
echo "</div>\n";
echo "<div class='myfontbsys'>\n";
require($phpsysinfolink);
echo "</div>\n";
exit();
}
?>                                                                            