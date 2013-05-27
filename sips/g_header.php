<?php
############################################################################################
####  Name:             g_header.php                                                    ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");
$stmt="SELECT full_name from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$full_name				=$row[0];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>goaddons v1.0 © by  | GOAutoDial Inc.</title>
<style type="text/css">   
#bbg {
     background: url(images/vstripe1.png);
     width: 1010px;
 	 min-height: 300px;
 	 border: 0px;
	text-align: right;
	font-family: Arial, Helvetica, sans-serif; 
	font-size: 13px; 
	color: #27400d;
}
</style>
<style type="text/css">
<!--
A{font-family: Verdana,Helvetica,sans-serif; font-size: 13px; font-weight: bold; text-decoration: none; color: #81a160;}
A:active  {color: #d9a60e; 	}
A:hover   {cursor: hand; color: #d9a60e;}
//-->
</style>
</head>
<body>
<div id='bbg'><b>
 <i>Welcome <?echo "$full_name"; ?>!</i>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a style="cursor: pointer;" onclick="top.frames.document.location.href='index.php'">MAIN</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a style="cursor: pointer;" onclick="top.frames.document.location.href='index.php?force_logout=1;'">LOGOUT</a></b>&nbsp;&nbsp;&nbsp;
<img style="cursor: pointer;" onclick="javascript:window.open('http://www.goautodial.com', '_blank');" src='images/g_banner.png'>
</div>
</body>
</html>