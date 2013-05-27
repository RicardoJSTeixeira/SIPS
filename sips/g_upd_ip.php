<?php
############################################################################################
####  Name:             g_upd_ip.php                                                    ####
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
	
if (($old_ip=='Select...') || ($old_ip=='') || ($new_ip=='') || (!validateIpAddress($new_ip)))
	{
		echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>WARNING: Server IP invalid! Please select and type-in the appropriate IP addresses values!<br />\n";
	}
	else
	{
		exec("/usr/share/goautodial/ADMIN_update_server_ip.pl --auto --old-server_ip=$old_ip --server_ip=$new_ip");
		sleep (5);
		echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Update server IP successful!<br />\n";
}
exit;
?>
