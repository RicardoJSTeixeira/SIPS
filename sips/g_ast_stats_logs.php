<?php
############################################################################################
####  Name:             g_ast_stats_logs.php                                            ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################
require("includes/g_authenticate.php");
require("includes/g_hpage.php");

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
#### ASTERISK SERVICE STATUS
###########################################################
if ($status_type=="01")
	{
?>
<div class='myfontb'><?echo $a;$s;?> ASTERISK STATUS:
<p id='pfont'></p>
<p id='pfont'><font color="#dddddd">______________________________________________________________________________</font></p><br>
<p class='pfontt' id='pfont'>Server Name<?echo $s.$s;?>: <?echo $hostn;?></p>
<p class='pfontt' id='pfont'>Server IP<?echo $s.$s.$s.$s.$s.$s.$s.$s;?>: <?echo $hostp;?></p>
<p id='pfont'><font color="#dddddd">______________________________________________________________________________</font></p><br>
<? 
$startme = exec('/usr/share/goautodial/goautodialc.pl "/sbin/service asterisk status"');
 	if (preg_match("/\brunning\b/i", $startme))
	 	{	
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s Asterisk";
  		}
 		else
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s Asterisk";
		}
?>
<br><font style="font-weight: bold;">
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________</font>
<br><?echo $s.$s.$s.$s.$s.$s.$s.$s;?>LEGEND:<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'><?echo $s;?>UP<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px src='images/g_status_no.png'><?echo $s;?>DOWN
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________</font>
<?
exit();
}
###########################################################
#### MYSQLD SERVICE STATUS
###########################################################
if ($status_type=="02")
	{
?>
<div class='myfontb'><?echo $a;$s;?> MYSQL STATUS:
<p id='pfont'></p>
<p id='pfont'><font color="#dddddd">______________________________________________________________________________</font></p><br>
<p class='pfontt' id='pfont'>Server Name<?echo $s.$s;?>: <?echo $hostn;?></p>
<p class='pfontt' id='pfont'>Server IP<?echo $s.$s.$s.$s.$s.$s.$s.$s;?>: <?echo $hostp;?></p>
<p id='pfont'><font color="#dddddd">______________________________________________________________________________</font></p><br>
<? 
$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/mysqld status"');
	if (preg_match("/\brunning\b/i", $startme))
		{	
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s MYSQL";
  		}
 		else
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s MYSQL";
  		}
?>
<br><font style="font-weight: bold;">
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________</font>
<br><?echo $s.$s.$s.$s.$s.$s.$s.$s;?>LEGEND:<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'><?echo $s;?>UP<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px src='images/g_status_no.png'><?echo $s;?>DOWN
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________</font>
<?
exit();
}
###########################################################
#### APACHE/WEB SERVICE STATUS
###########################################################
if ($status_type=="03")
	{
?>
<div class='myfontb'><?echo $a;$s;?> APACHE/WEB STATUS:
<p id='pfont'></p>
<p id='pfont'><font color="#dddddd">______________________________________________________________________________</font></p><br>
<p class='pfontt' id='pfont'>Server Name<?echo $s.$s;?>: <?echo $hostn;?></p>
<p class='pfontt' id='pfont'>Server IP<?echo $s.$s.$s.$s.$s.$s.$s.$s;?>: <?echo $hostp;?></p>
<p id='pfont'><font color="#dddddd">______________________________________________________________________________</font></p><br>
<? 			
$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/httpd status"');
 	if (preg_match("/\brunning\b/i", $startme))
	 
		{	
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s Apache/WEB";
  		}
 		else
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s Apache/WEB";
  		}
?>
<br><font style="font-weight: bold;">
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________</font>
<br><?echo $s.$s.$s.$s.$s.$s.$s.$s;?>LEGEND:<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'><?echo $s;?>UP<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px src='images/g_status_no.png'><?echo $s;?>DOWN
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________</font>
<?
exit();
}
###########################################################
#### NETWORK SERVICE STATUS
###########################################################
if ($status_type=="04")
	{
?>
<div class='myfontb'><?echo $a;$s;?> NETWORK STATUS:
<p id='pfont'></p>
<p id='pfont'><font color="#dddddd">______________________________________________________________________________</font></p><br>
<p class='pfontt' id='pfont'>Server Name<?echo $s.$s;?>: <?echo $hostn;?></p>
<p class='pfontt' id='pfont'>Server IP<?echo $s.$s.$s.$s.$s.$s.$s.$s;?>: <?echo $hostp;?></p>
<p id='pfont'><font color="#dddddd">______________________________________________________________________________</font></p><br>
<? 			
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)&&preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		} 
		else 
		{
		if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth1";
	  		
		} 
	if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth0";
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		}
	}
	}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))   	
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
		} 
		else
		{ 
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth0";
		}
	}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0")))   	
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		} 
		else
		{ 
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth1";
		}
	}	
?>
<br><font style="font-weight: bold;">
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________</font>
<br><?echo $s.$s.$s.$s.$s.$s.$s.$s;?>LEGEND:<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'><?echo $s;?>UP<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px src='images/g_status_no.png'><?echo $s;?>DOWN
<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________</font>
<?
#echo "netstat.log";
$startme = system('/bin/netstat -antpu > "netstat.log"');
$startme = system('echo "" >> "netstat.log"');
$startme = system('echo "" >> "netstat.log"');
$startme = system('echo "<br><FONT FACE=VERDANA COLOR=RED SIZE=2>REALTIME: </FONT><FONT FACE=VERDANA COLOR=GREEN SIZE=2><b>netstat -i</b></font>" >> "netstat.log"');
$startme = system('echo "<font color=#dddddd>_________________________________________________________________________________</font><br>" >> "netstat.log"');
$startme = system('/bin/netstat -i >> "netstat.log"');
$file = fopen("netstat.log", "r") or exit("Unable to open file!");
echo "<br><br><br><FONT FACE='VERDANA' COLOR=RED SIZE=2>REALTIME: </FONT><FONT FACE='VERDANA' COLOR=GREEN SIZE=2><b><?echo $s.$s.$s.$s.$s.$s.$s.$s;?>netstat -antpu</b></font>\n";
echo "<font color='#dddddd'>_________________________________________________________________________________</font><br><br>";
while(!feof($file))
  {
  echo "<?echo $s.$s.$s.$s.$s.$s.$s.$s;?><?echo $s.$s;?>". fgets($file);
  }
fclose($file); 
exit(); 
}
###########################################################
#### ALL SYSTEM STATUS + PHPSYSINFO
###########################################################
if ($status_type=="ALL")
	{
?>
<div class='myfontb'>
<?echo $a.$s;?>SYSTEM STATUS:
<p id='pfont'></p>
<p id='pfont'><font color="#dddddd">_______________________________________________________________________________________________________</font></p><br>
<p class='pfontt' id='pfont'>Server Name<?echo $s.$s;?>: <?echo $hostn;?></p>
<p class='pfontt' id='pfont'>Server IP<?echo $s.$s.$s.$s.$s.$s.$s.$s;?>: <?echo $hostp;?></p>
<p id='pfont'><font color="#dddddd">_______________________________________________________________________________________________________</font></p>
<br>
<? 
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/sbin/service asterisk status"');
 	if (preg_match("/\brunning\b/i", $startme))
	 	{	
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s Asterisk";
  		}
 		else
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s Asterisk";
		}
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/mysqld status"');
	if (preg_match("/\brunning\b/i", $startme))
		{	
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s MYSQL";
  		}
 		else
		{
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s MYSQL";
  		}
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/httpd status"');
	if (preg_match("/\brunning\b/i", $startme))
	 
		{	
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s Apache/WEB";
  		}
 		else
		{
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s Apache/WEB";
  		}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)&&preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		} 
		else 
		{
		if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth1";
	  		
		} 
	if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth0";
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		}
	}
	}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))   	
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth0";
		} 
		else
		{ 
			echo "<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth0";
		}
	}
	if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0")))   	
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
	if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
		{
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'>";
	  		echo "$s eth1";
		} 
		else
		{ 
			echo "$s$s<b class='pfontt' id='pfont'/><img style='border-style:none; border-width:0px; margin-bottom: -5px'  width=18px  src='images/g_status_no.png'>";
	  		echo "$s eth1";
		}
	}	
	?>
<br><br><font style="font-weight: bold;">
<?echo $s.$s.$s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________________________________</font>
<br><?echo $s.$s.$s.$s.$s.$s.$s.$s.$s.$s;?>LEGEND:<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px  src='images/g_status_ok.png'><?echo $s;?>UP<br>
<?echo $s.$s.$s.$s.$s.$s.$s.$s.$s.$s;?><img style='border-style:none; border-width:0px; margin-bottom: -5px;'  width=18px src='images/g_status_no.png'><?echo $s;?>DOWN
<?echo $s.$s.$s.$s.$s.$s.$s.$s.$s.$s;?><font color="#dddddd">_____________________________________________________________________________________________________</font>
<br><br>
<font style='font-weight: bold; font-family: Verdana,Helvetica,sans-serif; color: #000000; font-size: 12px;'><?echo $a.$s;?>SYSTEM INFORMATION (via <a href='http://phpsysinfo.sourceforge.net/'>phpSysInfo</a></font> - <font style='font-weight: bold; font-family: Verdana,Helvetica,sans-serif; color: RED; font-size: 12px;'>REALTIME</FONT> by  Team | GOAutoDial Inc.)</FONT>
<?
echo "</div>\n";
echo "<div class='myfontbsys'>\n";
require($phpsysinfolink);
echo "</div>\n";
exit();
}
?>                                                                            