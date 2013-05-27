<?php
############################################################################################
####  Name:             g_net_eth0_upd.php                                              ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

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
	
if ((!$new_ip)||(!$net_mask)||(!validateIpAddress($new_ip))||(!validateIpAddress($net_mask)))
	{
	  	echo "<br><br><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=BLACK SIZE=2>Error changing eth0 IP configuration!<br />\n";
		echo "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=BLACK SIZE=2>Please type-in a valid eth0 new ip and netmask!<br />\n"; 
	}
	else
	{		

#exec('sudo -u root /bin/cp -f /etc/sysconfig/network-scripts/ifcfg-eth0 /etc/sysconfig/network-scripts/ifcfg-eth0.bak.gad');	
	
if (isset($_POST['submit'])) 
 {
   if ($_POST['submit'] == "Save") 
   	{
      $data=$_POST['data'];
	  function count_words($data)
		{
			$dline = count(explode("\r",$data));
			return $dline;
		} 
	  $lineme=count_words(); 
      $file = fopen("/etc/sysconfig/network-scripts/ifcfg-eth0", "wb") or exit("Unable to open file!");
      if ($file != FALSE) {
	  $o=0;
	  while($lineme > $o)
  		{
	  		$datar = $data; 
	  		$datar = preg_replace("/\r/","",$datar);
	  		#fwrite($file, utf8_decode($data));
	  		system("/bin/echo '$datar' >> /etc/sysconfig/network-scripts/ifcfg-eth0");
	  		$o++;
  		}
	echo "<br><br><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=BLACK SIZE=2>eth0 IP configuration changes was successfully saved!<br />\n"; 		  		
	echo "<FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=BLACK SIZE=2>To fully apply the network configuration changes, you must <b><a  href='g_confirmation.php?evnt_cmd=sys_reb' target='mainFrame'>REBOOT</a></b> your system!<br />\n"; 		
	fclose($file);
	}
	else 
	{
	  	echo "<br><br><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=BLACK SIZE=2>Error saving eth0 IP configuration!<br />\n";
		echo "<br><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=BLACK SIZE=2>Please make sure the web server is authorized to save this file!<br />\n"; 
    }
  }
 exit();	
 }
 
if ( file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0") )
{
$ethconf = file("/etc/sysconfig/network-scripts/ifcfg-eth0");
foreach ($ethconf as $ethline) 
	{
	if (ereg("^#", $ethline))	
		{$ethdevicealias = $ethline;}
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

echo "<html>\n";
echo "<body>\n";
?>
<br><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=BLACK SIZE=2><b><B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=RED SIZE=2>WARNING: </FONT></B>Are these settings correct? Click <B><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;'   COLOR=RED SIZE=2>SAVE</FONT></B> to apply these changes!</b></font><br><br>	
<form method="post" action="<?php $_SERVER['PHP_SELF']?>">
<input type="hidden" name="file"   value=<?php echo "$fname";?>>   </input>
<?
echo "<textarea name=data cols=94 rows=9>\r";
echo "$ethdevicealias";
echo "DEVICE=$ethdevicename\r";
echo "BOOTPROTO=$ethbootproto\r";
#echo "DHCPCLASS=$ethdhcpclass\r";
echo "HWADDR=$ethhwaddr\r";
echo "IPADDR=$new_ip\r";
echo "NETMASK=$net_mask\r";
echo "ONBOOT=$ethonboot\r";
if (($net_gw)&&(validateIpAddress($net_gw)))
{
echo "GATEWAY=$net_gw\r";
}
#echo "TYPE=$ethtype";
echo "</textarea>\r";
?>
<br><br>
<input type="submit" name="submit" value="Save">   </input>
<input type="button" value="Discard" onclick="location.href='./g_dead.php'">
</form>
<?
echo "</body>\n";
echo "</html>\n";
}
?>