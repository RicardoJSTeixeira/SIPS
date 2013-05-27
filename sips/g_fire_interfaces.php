<?php
############################################################################################
####  Name:             g_fire_interfaces.php                                           ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");
require("includes/dbconnect_goautodial.php");

$PHP_SELF=$_SERVER['PHP_SELF'];

if (isset($_GET["interface_id"]))		{$interface_id=$_GET["interface_id"];}
elseif (isset($_POST["interface_id"]))		{$interface_id=$_POST["interface_id"];}

if (isset($_GET["interface"]))			{$interface=$_GET["interface"];}
elseif (isset($_POST["interface"]))		{$interface=$_POST["interface"];}

if (isset($_GET["command"]))			{$command=$_GET["command"];}
elseif (isset($_POST["command"]))		{$command=$_POST["command"];}

if (isset($_GET["type"]))			{$type=$_GET["type"];}
elseif (isset($_POST["type"]))			{$type=$_POST["type"];}

if (isset($_GET["target"]))			{$target=$_GET["target"];}
elseif (isset($_POST["target"]))		{$target=$_POST["target"];}

if (isset($_GET["status"]))			{$status=$_GET["status"];}
elseif (isset($_POST["status"]))		{$status=$_POST["status"];}

if (isset($_GET["stage"]))			{$stage=$_GET["stage"];}
elseif (isset($_POST["stage"]))			{$stage=$_POST["stage"];}

if (isset($_GET["submit"]))			{$submit=$_GET["submit"];}
elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}

if (isset($_GET["SUBMIT"]))			{$SUBMIT=$_GET["SUBMIT"];}
elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}




switch($stage) {						
	case "EDIT":
		$stmt="UPDATE go_firewall_interfaces SET command='".$command."',type='".$type."',target='".$target."',active='".$status."' WHERE interface_id='".$interface_id."'";
		$rslt=mysql_query($stmt, $linkd);
		exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_firewall.pl"');
		break;
	    
	case "ADD":
	
		
			    $stmt="INSERT INTO go_firewall_interfaces VALUES ('','".$interface."','".$command."','".$type."','".$target."','".$status."')";
			    $rslt=mysql_query($stmt, $linkd);
			    exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_firewall.pl"');
		break;
		
	case "DELETE":
		$stmt="DELETE FROM go_firewall_interfaces WHERE interface_id='".$interface_id."'";
		$rslt=mysql_query($stmt, $linkd);
		exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_firewall.pl"');
		break;
	    
	default:
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GO FIREWALL:&nbsp;INTERFACES (c) | GOAutoDial Inc.</title>
<link href="csslib/gadi_content.css" rel="stylesheet" type="text/css">
<script>
function delThis(num) {
	var answer = confirm("Do you really want to delete this interface?");
	if (answer) {
	document.location='?interface_id='+num+'&stage=DELETE';
	return true;
	}
}

function validateForm()
{

var interface=document.forms["myForm"]["interface"].value;
var command=document.forms["myForm"]["command"].value;
var type=document.forms["myForm"]["type"].value;
var target=document.forms["myForm"]["target"].value;
var status=document.forms["myForm"]["status"].value;


if (interface==null || interface=="")
  {
  alert("Interface must be filled out.");
  return false;
  }
if (interface=="lo")
  {
  alert("Duplicate entry for lo");
  return false;
  }
if (interface=="eth0")
  {
  alert("Duplicate entry for eth0");
  return false;
  }
if (interface=="eth1")
  {
  alert("Duplicate entry for eth1");
  return false;
  }
if (command==null || command=="")
  {
  alert("Command must be filled out.");
  return false;
  }
if (type==null || type=="")
  {
  alert("Type must be filled out.");
  return false;
  }
if (target==null || target=="")
  {
  alert("Target must be filled out.");
  return false;
  }
if (status==null || status=="")
  {
  alert("Status must be filled out.");
  return false; 
  }

}
</script>
<body>
<div id="webtemp19">
<?require("g_menu.php");?>
</div>
<br>
<div id="webtemp20">
</div>
<div id='bbg' >
<div class='aleft'>
<br>
<table>
	<tr><td class='myfontb'>&raquo;&nbsp;GO&nbsp;FIREWALL&nbsp: INTERFACES</td></tr>
</table>  	
<br> 
<table border="1" cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">
  <tr style="text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Interface ID</td>  
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Interface</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Command</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Type</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Target</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Status</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Action</td>
  </tr>
<?php
$stmt='SELECT * FROM go_firewall_interfaces order by active desc';
$rslt=mysql_query($stmt, $linkd);
$num=mysql_num_rows($rslt);

for ($i=0;$i<$num;$i++) {
	$row=mysql_fetch_row($rslt);
	echo '  <tr style="font-weight:normail; color:#626262">';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[0].'</td>';	
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[1].'</td>';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[2].'</td>';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[3].'</td>';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[4].'</td>';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[5].'</td>';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;"><a href="?interface_id='.$row[0].'&interface='.$row[1].'&command='.$row[2].'&type='.$row[3].'&target='.$row[4].'&status='.$row[5].'&stage=MODIFY">MODIFY</a> | <a href="#" onClick="delThis('.$row[0].')">DELETE</a></td>';
	echo '  </tr>';
}

?>
</table>
<br>
<br>
<table>
	<tr><td class='myfontb'>&raquo;&nbsp;GO&nbsp;FIREWALL&nbsp: ADD NEW / MODIFY INTERFACE</td></tr>
</table>  	
<br>
<form action="<?php echo $PHP_SELF; ?>" method="POST" name="myForm" style="font-family:Arial, Helvetica, sans-serif; font-size:12px">
<table cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">

  <tr style="border:10px;text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Interface</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Command</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Type</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Target</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Status</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Action</td>
  </tr>
  
  <tr style="font-weight:normail; color:#626262">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">
	<select name="interface" id="interface"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px" style="width:20px;">
	    <option value="lo">lo</option>
	    <option value="eth0">eth0</option>
	    <option value="eth1">eth1</option>
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'interface\').value=\''.$interface.'\';document.getElementById(\'interface\').disabled=\'true\';</script>';
		
	    }
	    else
	    {
		echo '<option value="" selected></option>';
	    }
	    ?>
	</select>
    </td>	
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">
	<select name="command" id="command"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px" style="width:20px;">
	    <option value="-I">-I</option>
	    <option value="-A">-A</option>
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'command\').value=\''.$command.'\';</script>';
	    }
	    else
	    {
		echo '<option value="" selected></option>';
	    }
	    ?>
	</select>	
    </td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">
	<select name="type" id="type"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px" style="width:20px;">
	    <option value="INPUT">INPUT</option>
	    <option value="OUTPUT">OUTPUT</option>
	    <option value="FORWARD">FORWARD</option>
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'type\').value=\''.$type.'\';</script>';
	    }
	    else
	    {
		echo '<option value="" selected></option>';
	    }
	    ?>
	</select>	
    </td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">
	<select name="target" id="target"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px" style="width:20px;">
	    <option value="ACCEPT">ACCEPT</option>
	    <option value="DROP">DROP</option>
	    <option value="LOG">LOG</option>
	    <option value="REJECT">REJECT</option>
	    <option value="DNAT">DNAT</option>
	    <option value="SNAT">SNAT</option>
	    <option value="MASQUERADE">MASQUERADE</option>
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'target\').value=\''.$target.'\';</script>';
	    }
	    else
	    {
		echo '<option value="" selected></option>';
	    }
	    ?>
	</select>	
	
    </td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">
	<select name="status" id="status"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px" style="width:20px;">
	    <option value="N">N</option>
	    <option value="Y">Y</option>
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'status\').value=\''.$status.'\';</script>';
	    }
	    else
	    {
		echo '<option value="" selected></option>';
	    }
	    ?>
	</select>	
    </td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">

	<input size='10' type="submit" name="submit" id="submit" value="SUBMIT" onclick="return validateForm();"/>
    </td>
  </tr>
</table>
<br>
<br>
<input type="hidden" name="interface_id" id="interface_id" value="<?php echo ($stage=="MODIFY") ? $interface_id : ''; ?>" />
<input type="hidden" name="stage" id="stage" value="<?php echo ($stage=="MODIFY") ? 'EDIT' : 'ADD'; ?>" />
</form>
</center>
</div>
</div>
</body>
</html>
