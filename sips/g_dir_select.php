<?php
############################################################################################
####  Name:             g_dir_select.php                                                ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("g_files_list.php");

if 	(isset($_GET["path"]))			{$path=$_GET["path"];}
	elseif 
	(isset($_POST["path"]))			{$path=$_POST["path"];}
if 	(isset($_GET["evnt_type"]))			{$evnt_type=$_GET["evnt_type"];}
	elseif 
	(isset($_POST["evnt_type"]))			{$evnt_type=$_POST["evnt_type"];}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GOAutoDial Admin v2.0 © by  | GOAutoDial Inc.</title>
<script type="text/javascript" src="ajaxlib/gadi_plock.js"></script>
<script type="text/javascript" src="ajaxlib/ajaxtabs.js"></script>
<script type="text/javascript" src="ajaxlib/kamotetabs.js"></script>
<link href="csslib/gadi_content.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="webtemp19">
<?require("g_menu.php");?>
</div>
<br>
<div id="webtemp20">
</div>
<br>
<div id='bbg' >
<div class='aleft'>
<?
if ($evnt_type=='ast_config')
{	  	
?>
<br>
	<TABLE>
	<form action='' method='get'>
	<tr><td class='pfontt'>&raquo;&nbsp;ASTERISK CONFIGURATION FILES QUERY:</td></tr>
	<tr><td class='pfontt'><b>&nbsp;</td></tr>
	<tr><td class='pfontt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Directory:</td>
	    <td class='pfontt'>
			<SELECT ID="path" NAME="path" style='width: 100px; font-size: 11px;'>
				<OPTION SELECTED VALUE="">Select...											</OPTION>
  				<OPTION VALUE="<?php echo $ASTCONFIGPATH;?>"><?php echo $ASTCONFIGPATH;?>	</OPTION>
  			</SELECT>
<?
echo "<input id='page1' class='sicon' type='button' name='GO' value='GO' onclick=\"javascript:loadintoIframe('myframe1', 'g_ast_dir.php', 'arg_query_files')\"></td></tr>\n";			
?>
 	</form>
    </table>  	
<?
}
?>
<?
if ($evnt_type=='net_config')
{	  	
?>	
<br>
	<TABLE>
	<form action='' method='get'>
	<tr><td class='pfontt'>&raquo;&nbsp;NETWORK/SYSTEM CONFIGURATION FILES QUERY:</td></td></tr>
	<tr><td class='pfontt'>&nbsp;</td></tr>
	<tr><td class='pfontt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Directory:</td>
		<td class='pfontt'>
			<SELECT ID="path" NAME="path" style='width: 100px; font-size: 11px;'>
  				<OPTION SELECTED VALUE="">Select...											</OPTION>
  				<OPTION VALUE="<?php echo $NICDIR1;?>"><?php echo $NICDIR1;?>				</OPTION>
  				<OPTION VALUE="<?php echo $NICDIR2;?>">		 <?php echo $NICDIR2;?>			</OPTION>
  	  			<OPTION VALUE="<?php echo $ETCDIR;?>">		 <?php echo $ETCDIR;?>			</OPTION>
			</SELECT>
<?
echo "<input id='page1' class='sicon' type='button' name='GO' value='GO' onclick=\"javascript:loadintoIframe('myframe1', 'g_net_dir.php', 'arg_query_files')\"></td></tr>\n";			
?>
 	</form>
    </table>
<?
}
?>
<?
if ($evnt_type=='db_backup')
{	  	
?>	
<br>
	<TABLE>
	<form action='' method='get'>
	<tr><td class='pfontt'>&raquo;&nbsp;SYSTEM BACKUP FILES QUERY:</td></td></tr>
	<tr><td class='pfontt'>&nbsp;</td></tr>
	<tr><td class='pfontt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Directory:</td>
		<td class='pfontt'>
			<SELECT ID="path" NAME="path" style='width: 100px; font-size: 11px;'>
  				<OPTION SELECTED VALUE="">Select...											</OPTION>
  				<OPTION VALUE="<?php echo $GODBBACKUPDIR;?>"><?php echo $GODBBACKUPDIR;?>				</OPTION>
			</SELECT>
<?
echo "<input id='page1' class='sicon' type='button' name='GO' value='GO' onclick=\"javascript:loadintoIframe('myframe1', 'g_db_dir.php', 'arg_query_files')\"></td></tr>\n";			
?>
 	</form>
    </table>
<?
}
?> 
<br>  
<hr align=left color='#efefcb' width='100%'>
<span id="span_loader">&nbsp;</span>
<iframe id="myframe1" src="about:blank" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="overflow:visible; border:0px solid gray; width:99%; min-height:5%;"></iframe>
</div>
</div>
</body>
</html>