

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SIPS - Sistemas de Informação da PuroSinónimo</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require ("dbconnect.php");
require ("dbconnect_noutf8.php");
require ("functions.php");
###

	echo "<script language=\"JavaScript\" src=\"../calendar/calendar_db.js\"></script>\n";
	echo "<link rel=\"stylesheet\" href=\"../calendar/calendar.css\">\n";
	echo "<link href='../css/style.css' rel='stylesheet' type='text/css' />";

$PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];
$PHP_SELF = $_SERVER['PHP_SELF'];

?>
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-1.7.2.js"></script>
</head>
<body>

<?php 

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($end_date)) {$end_date = $NOW_DATE;}

echo "<div class=cc-mstyle>";
echo "<table>";
echo "<tr>";
echo "<td id='icon32'><img src='/images/icons/report_magnify_32.png' /></td>";
echo "<td id='submenu-title'> Report de Script </td>";
echo "<td style='text-align:left'></td>";
echo "</tr>";
echo "</table>";
echo "</div>";
echo "<div id=work-area style='min-height:300px'>";
echo "<br><br>";
echo "<div class=cc-mstyle style='border:none;'>";

echo "<FORM ACTION='reports_cria_csv.php' METHOD=POST name=vicidial_report_script id=vicidial_report_script>\n";
echo "<table style='width:90%'>";
echo "<TR>";
echo "<TD VALIGN=TOP>";

echo "<label> Data Inicio: </label>";
?>
<script language="JavaScript">	
	var o_cal = new tcal({
		// form name
		'formname' : 'vicidial_report_script',
		// input name
		'controlname' : 'query_date'
	});
	o_cal.a_tpl.yearscroll = false;
</script>
<?php

echo "<INPUT READONLY TYPE=TEXT style='width:175px; text-align:center; text:indent:0;' MAXLENGTH=10 NAME=query_date VALUE=\"$query_date\">";
echo "<label> Data Final: </label>";

?>
<script language="JavaScript">	
	var o_cal = new tcal({
		// form name
		'formname' : 'vicidial_report_script',
		// input name
		'controlname' : 'end_date'
	});
	o_cal.a_tpl.yearscroll = false;
</script>
<?php
echo "<INPUT READONLY TYPE=TEXT style='width:175px; text-align:center; text:indent:0;' MAXLENGTH=10 NAME=end_date VALUE=\"$end_date\"></TD><TD VALIGN=TOP> <label>Campanhas: </label>";
echo "<SELECT style='width:200px;min-heigth:120px; ' SIZE=1 NAME=campanha id=campanhas onchange=\"get_campaign_feedbacks();\">\n";

$user = $_SERVER['PHP_AUTH_USER'];

$grupo = mysql_fetch_assoc(mysql_query("SELECT user_group from vicidial_users where user = '$user'", $link));
$grupo = $grupo['user_group'];
$stmt="SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$grupo'";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$LOGallowed_campaigns = $row[0];
$LOGallowed_campaignsSQL='';
$whereLOGallowed_campaignsSQL='';
if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
	{
	$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
	$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
	$LOGallowed_campaignsSQL = "AND vc.campaign_id IN('$rawLOGallowed_campaignsSQL')";
	$whereLOGallowed_campaignsSQL = "WHERE vc.campaign_id IN('$rawLOGallowed_campaignsSQL')";
	}
$regexLOGallowed_campaigns = " $LOGallowed_campaigns ";


$stmt="SELECT DISTINCT(vc.campaign_id), vc.campaign_name FROM vicidial_campaigns vc INNER JOIN vicidial_campaign_statuses vcs ON vc.campaign_id = vcs.campaign_id $whereLOGallowed_campaignsSQL ORDER BY campaign_id";
$rslt=mysql_query($stmt, $link) or die(mysql_error());
if ($DB) {echo "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);

		echo "<option value=\"--SELECIONE--\">-- Selecione Campanha --</option>";
$o = 0;
while ($campaigns_to_print > $o) {
		$campActual = mysql_fetch_assoc($rslt);
		echo "<option value='".strtoupper($campActual[campaign_id])."'>$campActual[campaign_name]</option>";
		$o++; 
}
echo "</SELECT><br>\n";
echo "<Span style='color:grey;font-size: 10px;'><i>(Apenas aparecem as Campanhas<br>que têm Feedbacks)</i></Span>";
echo "</TD>\n";

echo "<TD VALIGN=TOP> <label>Feedbacks: </label>";
echo "<SELECT style='width:200px; height:200px;' SIZE=5 NAME=feedback[] id=feedback multiple>\n";
echo "<option value=\"--SELECIONE--\">-- Selecione Campanha --</option>\n";
$stmt="SELECT vcs.status, vcs.status_name, vcs.campaign_id FROM vicidial_campaign_statuses vcs INNER JOIN (select campaign_id from vicidial_campaigns $whereLOGallowed_campaignsSQL ) vc on vcs.campaign_id = vc.campaign_id UNION ALL (select status,status_name,'BANANA' from vicidial_statuses where status NOT IN('QUEUE','INCALL','CALLBK','CBHOLD')) ORDER BY status_name";
$rslt=mysql_query($stmt, $link) or die(mysql_error());

if ($DB) {echo "$stmt\n";}
$feedbacks_to_print = mysql_num_rows($rslt);

///////////////////////////////////////////////////////////////
//echo "<script> alert('$stmt'); </script>";
///////////////////////////////////////////////////////////////

echo "</SELECT>";
echo "<Span style='color:grey;font-size: 10px;'><br><i>(Use a tecla 'Ctrl' para selecionar vários)</i></Span>";
echo "<br><br><center><label for=usar_sistema ><input onclick=\"get_campaign_feedbacks();\" type=checkbox id=usar_sistema name=usar_sistema /><Span style='color:grey;font-size: 11px;'> Usar Feedbacks de Sistema </span></label>";

echo "</TD>";
echo "</TR>";
echo "</TABLE>";
echo "<br><br><table style='width:500px'>
	<tr>
		<span style='color:black;cursor:pointer;float:right;' onclick=\"Verifica_Seleccao();\">
		Criar Relatório
		<img src='/images/icons/shape_square_add.png' alt='Criar' style='vertical-align:middle;'>
		</span>
		</tr></table></FORM></div></div></div></div></div>";
		//echo "<tr style='cursor:pointer' onclick=parent.mbody.location='".$curLink['url']."' ><td width=32px><img src=$curLink[imgpath] /></td><td>$curLink[label]</td></tr>";

		//<td style='width:100px;cursor:pointer;color:black'>Criar Relatório</td>		
		//<td style='width:50px;cursor:pointer;'><input type=image style='float:left;' src='/images/icons/shape_square_add.png' alt='Criar Relatório' name=SUBMIT></td>
		
?>
		
<script language="JavaScript">
//

function Verifica_Seleccao()
{
	if(document.getElementById("campanhas").selectedIndex===0){
		alert("Selecione primeiro uma Campanha.");	
	}else if($("#feedback option:selected").length===0){
		alert("Selecione pelo menos um Feedback.");
	}else{
		$('#vicidial_report_script').submit();
	}
	
}

function ClearOptionsFast(id)
{
	var selectObj = document.getElementById(id);
	var selectParentNode = selectObj.parentNode;
	var newSelectObj = selectObj.cloneNode(false); // Make a shallow copy
	selectParentNode.replaceChild(newSelectObj, selectObj);
	return newSelectObj;
}

var resultado = new Array();
<?php 
for($i=0; $i<mysql_num_rows($rslt); $i++) {				
	$curRow = mysql_fetch_assoc($rslt);		
	echo " resultado[$i] = new Array();";
	echo " resultado[$i][0] = '".$curRow['status']."' ; ";
	echo " resultado[$i][1] = '".$curRow['status_name']."' ; ";
	echo " resultado[$i][2] = '".strtoupper($curRow['campaign_id'])."' ; ";
} 
?>


function get_campaign_feedbacks() {
	ClearOptionsFast('feedback');
	if(document.getElementById("campanhas").selectedIndex===0){
		$("#feedback").append(new Option( "-- Selecione Campanha --" , "--SELECIONE--"));	
	}else{
		$("#feedback").append(new Option( "* Todos os Feedbacks *" , "--TODOS--"));
		
			for(i=0; i<resultado.length;i++)
			{
				
					if($("#usar_sistema").is(":checked"))
					{
						if(resultado[i][2] == $("#campanhas").val() || resultado[i][2] == "BANANA")
						{
							$("#feedback").append(new Option(resultado[i][1], resultado[i][0])); 
						
						}
					} 
					else 
					{	
						if(resultado[i][2] == $("#campanhas").val())
						{
							$("#feedback").append(new Option(resultado[i][1], resultado[i][0])); 
						
						} 
					}	
			}
		}
}




</script>
