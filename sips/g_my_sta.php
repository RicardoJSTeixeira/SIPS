<?php
############################################################################################
####  Name:             g_my_sta.php                                                    ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>goaddons v1.0 © by  | GOAutoDial Inc.</title>
<style type="text/css">    
#bbg {
     background: url(images/vstripe3.png);
     width: 800px;
	 min-height: 800px;     
 	 border: 0px;
}
</style>
</head>
<body>
<div id='bbg'>
<br>
<?	
$startme = exec('/usr/share/goautodial/goautodialc.pl "/usr/bin/nohup /etc/init.d/mysqld status"');	
if (preg_match("/\brunning\b/i", $startme))
	 	{
			echo "<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_ok.png'>";
	  		echo "&nbsp;<FONT FACE='VERDANA' COLOR=BLACK SIZE=2><b>MYSQLD is currently <FONT FACE='VERDANA' COLOR=GREEN SIZE=2><B>RUNNING</B></font>!</font>";
  		}
 		else
		{
			echo "<img style='border-style:none; border-width:0px;'  width=15px  src='images/g_status_no.png'>";
	  		echo "&nbsp;<FONT FACE='VERDANA' COLOR=BLACK SIZE=2><b>MYSQLD is currently not <FONT FACE='VERDANA' COLOR=RED SIZE=2><B>RUNNING</B></font>!</font>";
  		}
?>
</div>
</body> 
</html>