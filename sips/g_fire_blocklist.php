<?php
############################################################################################
####  Name:             g_fire_blocklist.php                                            ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");
require("includes/dbconnect_goautodial.php");

$PHP_SELF=$_SERVER['PHP_SELF'];

if (isset($_GET["block_id"]))			{$block_id=$_GET["block_id"];}
elseif (isset($_POST["block_id"]))		{$block_id=$_POST["block_id"];}

if (isset($_GET["command"]))			{$command=$_GET["command"];}
elseif (isset($_POST["command"]))		{$command=$_POST["command"];}

if (isset($_GET["type"]))			{$type=$_GET["type"];}
elseif (isset($_POST["type"]))			{$type=$_POST["type"];}

if (isset($_GET["source"]))			{$source=$_GET["source"];}
elseif (isset($_POST["source"]))		{$source=$_POST["source"];}

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
		$stmt="UPDATE go_firewall_blocklist SET command='".$command."',type='".$type."',source='".$source."',target='".$target."',active='".$status."' WHERE block_id='".$block_id."'";
		$rslt=mysql_query($stmt, $linkd);
		exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_firewall.pl"');
		break;
	    
	case "ADD":
		$stmt2="SELECT * FROM go_firewall_blocklist where source='".$source."' ";
		$rslt2=mysql_query($stmt2, $linkd);
		$nums_result=mysql_num_rows($rslt2);
		
		if($nums_result > 0) {
	      ?>
		  <script language="javascript">
		    alert("Duplicate Entry");
		  </script>
	      <?
		} else {
		  $stmt="INSERT INTO go_firewall_blocklist VALUES ('','".$command."','".$type."','".$source."','".$target."','".$status."')";
		  $rslt=mysql_query($stmt, $linkd);
		  exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_firewall.pl"');
		  break;
		}
		
		
	case "DELETE":
		$stmt="DELETE FROM go_firewall_blocklist WHERE block_id='".$block_id."'";
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
<title>GO FIREWALL:&nbsp;BLOCK LIST (c) | GOAutoDial Inc.</title>
<link href="csslib/gadi_content.css" rel="stylesheet" type="text/css">
<script>

function duplicate() {
    alert("Duplicate entry.");
    return false;
}

function delThis(num) {
	var answer = confirm("Do you really want to delete this ip blocking?");
	if (answer) {
	document.location='?block_id='+num+'&stage=DELETE';
	return true;
	}
}

function fnValidateIPAddress(ipaddr) {
    ipaddr = ipaddr.replace( /\s/g, "")
    var re = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/; 
    if (re.test(ipaddr)) {
        var parts = ipaddr.split(".");
        if (parseInt(parseFloat(parts[0])) == 0) {
            return false;
        }
        if (parseInt(parseFloat(parts[3])) == 0) {
            return false;
        }
        for (var i=0; i<parts.length; i++) {
            if (parseInt(parseFloat(parts[i])) > 255){
                return false;
            }
        }
	return true;
    } else {
	errorString = "Invalid IP"; 
	alert(errorString);
        return false;
    }
}

function ipcheckslash(ipcheck) {
  errorString = "";
  var dot = ipcheck.split(".");
  var checkslash = ipcheck.split("/");

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
  if(ipcheck == "255.255.255.255") {
    errorString = ipcheck + ' is a special IP address and cannot be used here';
    alert(errorString);
  return false;
  }
  if(ipcheck == "0.0.0.0") {
    errorString = ipcheck + ' is a special IP address and cannot be used here';
    alert(errorString);
  return false;
  }
  if(checkslash[1] > 28) {
   errorString = "Invalid IP"; 
    alert(errorString);
    return false;
  }
  if(checkslash[1] < 1) {
   errorString = "Invalid IP"; 
    alert(errorString);
    return false;
  }
  if(errorString != "") {
  alert(errorString);
  return false;
  }
}

function validateForm(duplicate)
{
  var source=document.forms["myForm"]["source"].value;
  var command=document.forms["myForm"]["command"].value;
  var type=document.forms["myForm"]["type"].value;
  var target=document.forms["myForm"]["target"].value;
  var status=document.forms["myForm"]["status"].value;
  var checkslash = source.split("/");

  
  if(checkslash[1]==null || checkslash[1]=="") {
    if(fnValidateIPAddress(source)==false) {
    return false;
    }
      } else {
    if (ipcheckslash(source)==false) {
    return false;
    }
  }

  if (source==null || source=="")
    {
    alert("Source must be filled out.");
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
	<tr><td class='myfontb'>&raquo;&nbsp;GO&nbsp;FIREWALL&nbsp: BLOCK LIST</td></tr>
</table>  	
<br>	
<table border="1" cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">
  <tr style="text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Block ID</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Command</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Type</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Source/IP</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Target</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Status</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Action</td>
  </tr>
<?php
$stmt='SELECT * FROM go_firewall_blocklist order by active desc';
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
	echo '    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;"><a href="?block_id='.$row[0].'&source='.$row[3].'&command='.$row[2].'&type='.$row[3].'&target='.$row[4].'&status='.$row[5].'&stage=MODIFY">MODIFY</a> | <a href="#" onClick="delThis('.$row[0].')">DELETE</a></td>';
	echo '  </tr>';
}

?>
</table>
<br>
<br>
<table>
	<tr><td class='myfontb'>&raquo;&nbsp;GO&nbsp;FIREWALL&nbsp: ADD NEW / MODIFY BLOCK LIST</td></tr>
</table>  	
<br>
<form action="<?php echo $PHP_SELF; ?>" method="POST" name="myForm" style="font-family:Arial, Helvetica, sans-serif; font-size:12px">
<table cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">
  <tr style="border:10px;text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Command</td>   
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Type</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Source/IP</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Target</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Status</td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">Action</td>
  </tr>
  <tr style="font-weight:normail; color:#626262">
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">
	<select name="command" id="command"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px" style="width:20px;">
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
	<input type="text" name="source" id="source"  value="" style="font-family:Arial, Helvetica, sans-serif; font-size:12px" style="width:20px;">
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'source\').value=\''.$source.'\';</script>';
	    }
	    else
	    {
		echo '';
	    }
	    ?>
	</input>
	
    </td>
    <td  style="margin-left: 20px; padding: 3 3 3 3; align:left;">
	<select name="target" id="target"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px" style="width:20px;">
	    <option value="DROP">DROP</option>
	    <option value="REJECT">REJECT</option>
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
 <?php
	
	?>
	<input size='10' type="submit" name="submit" id="submit" value="SUBMIT" onclick="return validateForm();" />

	 
    </td>
  </tr>
</table>
<br>
<br>
<input type="hidden" name="block_id" id="block_id" value="<?php echo ($stage=="MODIFY") ? $block_id : ''; ?>" />
<input type="hidden" name="stage" id="stage" value="<?php echo ($stage=="MODIFY") ? 'EDIT' : 'ADD'; ?>" />
</form>
</center>
</div>
</div>
</body>
</html>
