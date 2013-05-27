<?php
############################################################################################
####  Name:             g_httpd_config.php                                           	####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Jerico James Milo <james@goautodial.com>  	####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");
require("includes/dbconnect_goautodial.php");

$PHP_SELF=$_SERVER['PHP_SELF'];

if (isset($_GET["access_id"]))		{$access_id=$_GET["access_id"];}
elseif (isset($_POST["access_id"]))		{$access_id=$_POST["access_id"];}

if (isset($_GET["ip_address"]))			{$ip_address=$_GET["ip_address"];}
elseif (isset($_POST["ip_address"]))		{$ip_address=$_POST["ip_address"];}

if (isset($_GET["ip_block_list"]))			{$ip_block_list=$_GET["ip_block_list"];}
elseif (isset($_POST["ip_block_list"]))		{$ip_block_list=$_POST["ip_block_list"];}

if (isset($_GET["folder"]))			{$folder=$_GET["folder"];}
elseif (isset($_POST["folder"]))			{$folder=$_POST["folder"];}

if (isset($_GET["folder_alias"]))			{$folder_alias=$_GET["folder_alias"];}
elseif (isset($_POST["folder_alias"]))		{$folder_alias=$_POST["folder_alias"];}

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
		$stmt="UPDATE go_sysbackup_access SET ip_block_list='".$ip_block_list."',folder='".$folder."',folder_alias='".$folder_alias."',active='".$status."' WHERE access_id='".$access_id."'";
		$rslt=mysql_query($stmt, $linkd);
		exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_httpd_conf.pl"');
		break;
	    
	case "ADD":
		$stmt2="SELECT * FROM go_sysbackup_access where ip_address='".$ip_address."' ";
		$rslt2=mysql_query($stmt2, $linkd);
		$nums_result=mysql_num_rows($rslt2);
		
		if($nums_result > 0) {
		?>
		    <script language="javascript">
		      alert("Duplicate Entry");
		    </script>
		<?
		} else {
		$stmt="INSERT INTO go_sysbackup_access VALUES ('','".$ip_address."','".$ip_block_list."','".$folder."','".$folder_alias."','".$status."')";
		$rslt=mysql_query($stmt, $linkd);
		exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_httpd_conf.pl"');
		break;
		}
		
	case "DELETE":
		$stmt="DELETE FROM go_sysbackup_access WHERE access_id='".$access_id."'";
		$rslt=mysql_query($stmt, $linkd);
		exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_httpd_conf.pl"');
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
	var answer = confirm("Do you really want to delete this ip_address?");
	if (answer) {
	document.location='?access_id='+num+'&stage=DELETE';
	return true;
	}
}

function validateForm() {

  var ip_address=document.forms["myForm"]["ip_address"].value;
  var status=document.forms["myForm"]["status"].value;
  var dot = ip_address.split(".");


  if(dot[0] > 255) {
    errorString = "Invalid IP";  
    alert(errorString);
    return false;
  }
  if(dot[1] > 255) {
    errorString = "Invalid IP";  
    alert(errorString);
    return false;
  }
  if(dot[2] > 255) {
    errorString = "Invalid IP";  
    alert(errorString);
    return false;
  }
  if(dot[3] > 255) {
    errorString = "Invalid IP";  
    alert(errorString);
    return false;
  }

  //if(fnValidateIPAddress(ip_address)==false) {
  //  return false;
  //}

  if (ip_address==null || ip_address=="")
    {
    alert("IP Address must be filled out.");
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
	<tr><td class='myfontb'>&raquo;&nbsp;GO&nbsp;HTTPD&nbsp: BACKUP ACCESS</td></tr>
</table>  	
<br> 
<table border="1" cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">
  <tr style="text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Acess ID</td>  
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">IP Address</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Status</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Action</td>
  </tr>
<?php
$stmt='SELECT * FROM go_sysbackup_access order by active desc';
$rslt=mysql_query($stmt, $linkd);
$num=mysql_num_rows($rslt);

for ($i=0;$i<$num;$i++) {
	$row=mysql_fetch_row($rslt);
	echo '  <tr style="font-weight:normail; color:#626262">';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[0].'</td>';	
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[1].'</td>';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">'.$row[5].'</td>';
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;"><a href="?access_id='.$row[0].'&ip_address='.$row[1].'&ip_block_list='.$row[2].'&folder='.$row[3].'&folder_alias='.$row[4].'&status='.$row[5].'&stage=MODIFY">MODIFY</a> | <a href="#" onClick="delThis('.$row[0].')">DELETE</a></td>';
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
<form action="<?php echo $PHP_SELF; ?>" name="myForm" id="myForm" method="POST" style="font-family:Arial, Helvetica, sans-serif; font-size:12px">
<table cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">
  <tr style="border:10px;text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">IP Address</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Status</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Action</td>
  </tr>

  <tr style="font-weight:normail; color:#626262">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">
	  <input type="text" name="ip_address" id="ip_address"  value="" style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'ip_address\').value=\''.$ip_address.'\';</script>';
	    }
	    else
	    {
		echo '';
	    }
	    ?>
	</input>
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
	<input size='10' type="submit" name="submit" id="submit" value="SUBMIT" onclick="return validateForm();" />
    </td>
  </tr>
</table>
<br>
<br>
<input type="hidden" name="access_id" id="access_id" value="<?php echo ($stage=="MODIFY") ? $access_id : ''; ?>" />
<input type="hidden" name="stage" id="stage" value="<?php echo ($stage=="MODIFY") ? 'EDIT' : 'ADD'; ?>" />
</form>
</center>
</div>
</div>
</body>
</html>
