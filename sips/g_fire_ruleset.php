<?php
############################################################################################
####  Name:             g_fire_ruleset.php                                              ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");
require("includes/dbconnect_goautodial.php");

$PHP_SELF=$_SERVER['PHP_SELF'];

if (isset($_GET["rule_id"]))			{$rule_id=$_GET["rule_id"];}
elseif (isset($_POST["rule_id"]))		{$rule_id=$_POST["rule_id"];}

if (isset($_GET["rule_number"]))		{$rule_number=$_GET["rule_number"];}
elseif (isset($_POST["rule_number"]))		{$rule_number=$_POST["rule_number"];}

if (isset($_GET["command"]))			{$command=$_GET["command"];}
elseif (isset($_POST["command"]))		{$command=$_POST["command"];}

if (isset($_GET["type"]))			{$type=$_GET["type"];}
elseif (isset($_POST["type"]))			{$type=$_POST["type"];}

if (isset($_GET["state"]))			{$state=$_GET["state"];}
elseif (isset($_POST["state"]))			{$state=$_POST["state"];}

if (isset($_GET["protocol"]))			{$protocol=$_GET["protocol"];}
elseif (isset($_POST["protocol"]))		{$protocol=$_POST["protocol"];}

if (isset($_GET["match_protocol"]))		{$match_protocol=$_GET["match_protocol"];}
elseif (isset($_POST["match_protocol"]))	{$match_protocol=$_POST["match_protocol"];}

if (isset($_GET["source"]))			{$source=$_GET["source"];}
elseif (isset($_POST["source"]))		{$source=$_POST["source"];}

if (isset($_GET["destination"]))		{$destination=$_GET["destination"];}
elseif (isset($_POST["destination"]))		{$destination=$_POST["destination"];}

if (isset($_GET["port_from"]))			{$port_from=$_GET["port_from"];}
elseif (isset($_POST["port_from"]))		{$port_from=$_POST["port_from"];}

if (isset($_GET["port_to"]))			{$port_to=$_GET["port_to"];}
elseif (isset($_POST["port_to"]))		{$port_to=$_POST["port_to"];}

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
		$stmt="UPDATE go_firewall_rules SET command='".$command."',type='".$type."',state='".$state."',protocol='".$protocol."',match_protocol='".$match_protocol."',source='".$source."',destination='".$destination."',port_from='".$port_from."',port_to='".$port_to."',target='".$target."',active='".$status."' WHERE rule_id='".$rule_id."'";
		$rslt=mysql_query($stmt, $linkd);
		exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_firewall.pl"');
		break;
	
	case "ADD":
		if($source=="") {
		  $source="0.0.0.0";
		}
		if($destination=="") {
		  $destination="0.0.0.0";
		}
	    
		$stmt="INSERT INTO go_firewall_rules VALUES ('','".$rule_number."','".$command."','".$type."','".$state."','".$protocol."','".$match_protocol."','".$source."','".$destination."','".$port_from."','".$port_to."','".$target."','".$active."')";
		$rslt=mysql_query($stmt, $linkd);
		exec ('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_firewall.pl"');
		break;
		
	case "DELETE":
		$stmt="DELETE FROM go_firewall_rules WHERE rule_id='".$rule_id."'";
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
<title>GO FIREWALL:&nbsp;RULESET (c) | GOAutoDial Inc.</title>
<link href="csslib/gadi_content.css" rel="stylesheet" type="text/css">
<script>
function delThis(num) {
	var answer = confirm("Do you really want to delete this rule_number?");
	if (answer) {
	document.location='?rule_id='+num+'&stage=DELETE';
	return true;
	}
}

function validateForm() {

  var rule_number=document.forms["myForm"]["rule_number"].value;
  var type=document.forms["myForm"]["type"].value;
  var command=document.forms["myForm"]["command"].value;
  var protocol=document.forms["myForm"]["protocol"].value;
  var match_protocol=document.forms["myForm"]["match_protocol"].value;
  var target=document.forms["myForm"]["target"].value;
  var status=document.forms["myForm"]["status"].value;


  if (rule_number==null || rule_number=="")
    {
    alert("Rule Number must be filled out.");
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

  if (protocol==null || protocol=="")
    {
    alert("Protocol must be filled out.");
    return false; 
    }
  if (match_protocol==null || match_protocol=="")
    {
    alert("Match Protocol must be filled out.");
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
<body onLoad="alert('Dont change the settings unless you really know what you are doing')">
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
	<tr><td class='myfontb'>&raquo;&nbsp;GO&nbsp;FIREWALL&nbsp: RULE SET</td></tr>
</table>  	
<br>	
<table border="1" cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:8px">
  <tr style="text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Rule ID</td>  
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Rule Number</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Command</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Type</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">State</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Protocol</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Match Protocol</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Source</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Destination</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Port From</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Prot To</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Target</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Status</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Action</td>
  </tr>
<?php
$stmt='SELECT * FROM go_firewall_rules order by rule_id asc';
$rslt=mysql_query($stmt, $linkd);
$num=mysql_num_rows($rslt);

for ($i=0;$i<$num;$i++) {
	$row=mysql_fetch_row($rslt);
	echo '  <tr style="font-weight:normail; color:#626262">';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[0].'&nbsp;</td>';	
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[1].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[2].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[3].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[4].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[5].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[6].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[7].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[8].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[9].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[10].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[11].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">'.$row[12].'&nbsp;</td>';
	echo '    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;"><a href="?rule_id='.$row[0].'&rule_number='.$row[1].'&command='.$row[2].'&type='.$row[3].'&state='.$row[4].'&protocol='.$row[5].'&match_protocol='.$row[6].'&source='.$row[7].'&destination='.$row[8].'&port_from='.$row[9].'&port_to='.$row[10].'&target='.$row[11].'&status='.$row[12].'&stage=MODIFY">MODIFY</a> | <a href="#" onClick="delThis('.$row[0].')">DELETE</a></td>';
	echo '  </tr>';
}

?>
</table>
<br>
<br>
<table>
	<tr><td class='myfontb'>&raquo;&nbsp;GO&nbsp;FIREWALL&nbsp: ADD NEW / MODIFY RULE</td></tr>
</table>  	
<br>
<form action="<?php echo $PHP_SELF; ?>" name="myForm" id="myForm" method="POST" style="font-family:Arial, Helvetica, sans-serif; font-size:8px">
<table cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:8px">
  <tr style="border:10px;text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Rule Number</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Command</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Type</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">State</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Protocol</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Match Protocol</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Source</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Destination</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Port From</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Prot To</td>
  </tr>

  <tr style="font-weight:normail; color:#626262">
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<input type="text" name="rule_number" id="rule_number"  value="" style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'rule_number\').value=\''.$rule_number.'\';</script>';
	    }
	    else
	    {
		echo '';
	    }
	    ?>
	</input>
    </td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<select name="command" id="command"  style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
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
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<select name="type" id="type"  style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
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
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<input type="text" name="state" id="state"  value="" style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'state\').value=\''.$state.'\';</script>';
	    }
	    else
	    {
		echo 'ESTABLISHED/RELATED/NEW/INVALID';
	    }
	    ?>
	</input>	
    </td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<select name="protocol" id="protocol"  style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
	    <option value="icmp">ICMP</option>
	    <option value="tcp">TCP</option>
	    <option value="udp">UDP</option>
	     <option value="all">ALL</option>
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'protocol\').value=\''.$protocol.'\';</script>';
	    }
	    else
	    {
		echo '<option value="" selected></option>';
	    }
	    ?>
	</select>	
    </td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<select name="match_protocol" id="match_protocol"  style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
	    <option value="N">N</option>
	    <option value="Y">Y</option>
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'match_protocol\').value=\''.$match_protocol.'\';</script>';
	    }
	    else
	    {
		echo '<option value="" selected></option>';
	    }
	    ?>
	</select>	
    </td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<input type="text" name="source" id="source"  value="" style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
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
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<input type="text" name="destination" id="destination"  value="" style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'destination\').value=\''.$destination.'\';</script>';
	    }
	    else
	    {
		echo '';
	    }
	    ?>
	</input>
    </td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<input type="text" name="port_from" id="port_from"  value="" style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'port_from\').value=\''.$port_from.'\';</script>';
	    }
	    else
	    {
		echo '';
	    }
	    ?>
	</input>	
    </td>  
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<input type="text" name="port_to" id="port_to"  value="" style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
	    <?php
	    if ($stage=="MODIFY") {
		echo '<script>document.getElementById(\'port_to\').value=\''.$port_to.'\';</script>';
	    }
	    else
	    {
		echo '';
	    }
	    ?>
	</input>		
    </td>
  </tr>
</table>

<table cellspacing="1" cellpadding="1" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:8px">
  <tr style="border:10px;text-transform:uppercase; font-weight:bold; background-color:#626262; color:#FFFFFF">
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Target</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Status</td>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">Action</td>
  </tr>
  <tr>
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<select name="target" id="target"  style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
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
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<select name="status" id="status"  style="font-family:Arial, Helvetica, sans-serif; font-size:8px" style="width:10px;">
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
    <td  style="margin-left: 10px; padding: 3 3 3 3; align:left;">
	<input size='10' type="submit" name="submit" id="submit" value="SUBMIT" onclick="return validateForm();" />
    </td>
  </tr>
</table>
<br>
<br>
<input type="hidden" name="rule_id" id="rule_id" value="<?php echo ($stage=="MODIFY") ? $rule_id : ''; ?>" />
<input type="hidden" name="stage" id="stage" value="<?php echo ($stage=="MODIFY") ? 'EDIT' : 'ADD'; ?>" />
</form>
</center>
</div>
</div>
</body>
</html>
