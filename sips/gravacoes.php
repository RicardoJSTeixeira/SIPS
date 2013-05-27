<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Backoffice</title>

<script type="text/javascript" src="audio-player.js"></script>  
        <script type="text/javascript">  
            AudioPlayer.setup("https://sipscloud.dyndns.org/sips/player.swf", {  
                width: 290  
            });  
        </script>  

<?

function secondsToTime($seconds)
{
    // extract hours
    $hours = floor($seconds / (60 * 60));
 
    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);
 
    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);
 
    // return the final array
    $obj = array(
        "h" => (int) $hours,
        "m" => (int) $minutes,
        "s" => (int) $seconds,
    );
    return $obj;
}


?>
<style type="text/css">

body label {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
body label.header {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 20px;
	color:#006;
}

body td.header, th {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	color:#006;
}

body td {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 10px;
	text-align:center;
}
span{
cursor:pointer;
color:white;
background:#09F;
font-size:26px;
font:"Courier New", Courier, monospace
}
counter{
	font-size:24px;
}



input.bbuttons
{
   font-size:16px;
   width:80px;
   height:80px;
   white-space:normal;
}

input.abuttons
{
   background-color:#99CC66;
  
   width:130px;
   height:40px;
   border:2px;
   
	border-left: solid 2px #c3f83a;
	border-top: solid 2px #c3f83a;
	border-right: solid 2px #82a528;
	border-bottom: solid 2px #58701b;
}




textomaior {
	font-size: 16px;
}
textogrande {
	font-size: 16px;
}
textogrande {
	font-size: 18px;
}

</style>

			

<?

	$dia = date('j');
	$mes = date('m');
	$ano = date('o');
	
	
	
	$diainicio = $dia;
	$mesinicio = $mes;
	$anoinicio = $ano;
	$diafim = $dia;
	$mesfim = $mes;
	$anofim = $ano;
	
	$con = mysql_connect("localhost","root","admin");
				if (!$con)
				{
					die('Não me consegui ligar' . mysql_error());
				}
				mysql_select_db("asterisk", $con);
				
	$activeUsers = mysql_query("SELECT user FROM vicidial_users WHERE active='Y'") or die(mysql_error());
	$inactiveUsers = mysql_query("SELECT user FROM vicidial_users WHERE active='N'") or die(mysql_error());		
	$callResults = mysql_query("SELECT DISTINCT(status) FROM vicidial_list") or die(mysql_error());

    if ($_POST['user'] != '') {
	
	if($_POST['user'] != 'todos') { $user = "AND user = '".$_POST['user']."' "; 
	
		
	
	} else { $user = ""; }
	//if($_POST['callstatus'] != 'todos') { $callStatus = "AND status = '".$_POST['callstatus']."' "; } else { $callStatus = ""; }
	
	if ($_POST['diainicio'] == $_POST['diafim']) { $_POST['diafim'] = ($_POST['diafim'] + 1); }
	
	$diainicio = $_POST['diainicio'];
	$mesinicio = $_POST['mesinicio'];
	$anoinicio = $_POST['anoinicio'];
	$diafim = $_POST['diafim'];
	$mesfim = $_POST['mesfim'];
	$anofim = $_POST['anofim'];
	
	$dataInicio = "AND start_time >= '".$_POST['anoinicio']."-".$_POST['mesinicio']."-".$_POST['diainicio']."'";
	$dataFim = "AND end_time <= '".$_POST['anofim']."-".$_POST['mesfim']."-".$_POST['diafim']."'";
	//if ($_POST['ntlf'] != '') { $nTlf = "AND phone_number LIKE '".$_POST['ntlf']."'"; 
//								$user = ""; 
//								$callStatus = "";  
//								$dataInicio = "";
//								$dataFim = "";
//								$diainicio = $dia;
//								$mesinicio = $mes;
//								$anoinicio = $ano;
//								$diafim = $dia;
//								$mesfim = $mes;
//								$anofim = $ano;
//								$_POST['callstatus'] = "";
//								$_POST['user'] = "";
//								} 
//	else { $nTlf = ""; }
	
	if ($_POST['curPage'] != '') { $pageNumb = $_POST['curPage']; } else { $pageNumb = 1; }
	$totalPages = (mysql_num_rows(mysql_query("SELECT * FROM
				recording_log WHERE 				
				length_in_sec >= '20'
				".$dataInicio." ".$dataFim."
			".$user." "))/15)+1;
	$totalPages = intval($totalPages);		
	if ($pageNumb < 1) { $pageNumb = 1; }
	if ($pageNumb > $totalPages ) { $pageNumb = $totalPages; }		
	$limitNumb = $pageNumb*15;
	
	$qryStr = "SELECT * FROM (SELECT * FROM (SELECT start_time, end_time, length_in_sec, filename, user FROM
				recording_log WHERE 				
				length_in_sec >= '20'
				".$dataInicio." ".$dataFim."
				".$user." ORDER BY start_time ASC LIMIT $limitNumb) 
				as t ORDER BY t.start_time DESC LIMIT 15) as b ORDER BY b.start_time ASC";
			  
		  
	$recordList = mysql_query($qryStr) or die(mysql_error());
	
		
	
	}
	
	mysql_close($con);	

?>
<script type="text/javascript">
	function previouspage(y) {
		var x = parseInt(document.getElementById('curPage').value);
		x = x-1;
		document.getElementById('curPage').value = x;
		document.forms.item('filtragravacoes').submit();
	}
	function nextpage(y) {
		var x = parseInt(document.getElementById('curPage').value);
		x = x+1;
		document.getElementById('curPage').value = x;
		document.forms.item('filtragravacoes').submit();
	}
	
	function lastpage(y) {
		var x = parseInt(y);
		document.getElementById('curPage').value = x;
		document.forms.item('filtragravacoes').submit();	
	}
	
	function firstpage(y) {
	
		document.getElementById('curPage').value = 1;
		document.forms.item('filtragravacoes').submit();	
	}
	
	function enableAudio(y) {
		document.getElementById(y).style.display='block';
		var x = y+'a';
		document.getElementById(x).style.display='none';
			
	}
	
</script>
</head>

<body>
<table align="center" width="100%" border="1">
<tr>
 <!--  <td width="186" valign="top">
   	<iframe width="186" height="600" src="menu.html" frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0">
    </iframe> 
   </td> -->
   <td width="200" valign="top">
   <form action='gravacoes.php' target="_self" method="post" name='filtragravacoes' id='filtragravacoes' >
   <table align="center" width="100%">
   	<tr>
    	<td colspan="3">User</td>
    </tr>
    <tr>
    	<td colspan="3">
        	<select name='user' id='user' >
            	<? 
					$curUser = mysql_fetch_assoc($activeUsers);
					echo "<option value = 'todos'>Todos</option>";
					while($curUser != '') {
						if ($_POST['user'] == $curUser['user']) { $selUser = "selected = selected"; } else { $selUser = ""; }
						echo "<option value='".$curUser['user']."' ".$selUser.">".$curUser['user']."</option>";
						$curUser = mysql_fetch_assoc($activeUsers); }
					echo "<option value=''></option>";	
					echo "<option value=''>---Users Inactivos---</option>";
					echo "<option value=''></option>";
					$curUser = mysql_fetch_assoc($inactiveUsers);
					while($curUser != '') {
						if ($_POST['user'] == $curUser['user']) { $selUser = "selected = selected"; } else { $selUser = ""; }
						echo "<option value='".$curUser['user']."' ".$selUser.">".$curUser['user']."</option>";
						$curUser = mysql_fetch_assoc($inactiveUsers); }	
				?>
            </select><br /><br />
           
        </td>    
    </tr>    
   <tr>
   	<td colspan="3">Call Status</td>
   </tr>
   <tr>
  	<td colspan="3"> <select name='callstatus' disabled="disabled">
            	<?
					$curStatus = mysql_fetch_assoc($callResults);
					echo "<option value = 'todos'>Todos</option>";
					while($curStatus != '') {
					if ($_POST['callstatus'] == $curStatus['status']) { $selStatus = "selected = selected"; } else { $selStatus = ""; }	
						echo "<option value='".$curStatus['status']."' ".$selStatus.">".$curStatus['status']."</option>";
						$curStatus = mysql_fetch_assoc($callResults); }
				?>
            </select><br /><br />
    </td> 
   </tr>
   <tr>
   	<td colspan="3">Data Inicio</td>
   </tr>
   <tr>
   	<td><select name="diainicio">
        	<? for($i=1; $i<32; $i++) {
				
				if ($i==$diainicio) { $a = 'selected=selected'; } else { $a=''; }
				echo "<option value=".$i." ".$a." >$i</option>"; } ?>
        </select></td><td>
        <select name="mesinicio">
        	<? for($i=1; $i<13; $i++) {
				if ($mesinicio==$i) { $a = 'selected=selected'; } else { $a=''; }
				echo "<option value=".$i." ".$a." >$i</option>"; } ?>
        </select></td><td>
        <select name="anoinicio">
        	<? for($i=2011; $i<2015; $i++) {
				if ($anoinicio==$i) { $a = 'selected=selected'; } else { $a=''; }
				echo "<option value=".$i." ".$a." >$i</option>"; } ?>
        </select>   
     </td>
   </tr>
   <tr>
   	<td colspan="3"><br /><br />Data Fim</td>
   </tr>
   <tr>
   	<td><select name="diafim">
        	<? for($i=1; $i<32; $i++) {
				if ($i==$diafim) { $a = 'selected=selected'; } else { $a=''; }
				echo "<option value=".$i." ".$a." >$i</option>"; } ?>
        </select></td><td>
        <select name="mesfim">
        	<? for($i=1; $i<13; $i++) {
				if ($mesfim==$i) { $a = 'selected=selected'; } else { $a=''; }
				echo "<option value=".$i." ".$a." >$i</option>"; } ?>
        </select></td><td>
        <select name="anofim">
        	<? for($i=2011; $i<2015; $i++) {
				if ($anofim==$i) { $a = 'selected=selected'; } else { $a=''; }
				echo "<option value=".$i." ".$a." >$i</option>"; } ?>
        </select>   
     </td>
   </tr>
   <tr>
   	<td colspan="3"><br /><br />Nº de Telefone</td>
   </tr>
   <tr>
   	<td colspan="3"><input type="text" name='ntlf' disabled="disabled" /><br /><br /></td>
   </tr>
    <tr>
   	<td colspan="3"><input type="submit" value='Filtrar' />
    <input type="hidden" name='curPage' id='curPage' value='<? echo $pageNumb; ?>'  />
    
    </td>
   </tr>
   </table>
   </form>
   </td>
   <td width="100%" align="left" valign="top">
   <?

	
								
		$numrows = mysql_num_rows($recordList);
		echo "<table align='center' width='100%' cellpadding='10'>";
		echo("<tr><th>Data Chamada</th><th>Duração</th><th>Filename</th><th>User</th><th>Gravação</th><th>Link</th>");
		for ($i=0; $i<$numrows; $i++) {
			$curRecord = mysql_fetch_assoc($recordList);
			$filename = $curRecord['filename'];
			$mp3File = "https://sipscloud.dyndns.org/RECORDINGS/MP3/".$filename."-all.mp3";
			
			if (file_exists("/var/spool/asterisk/monitorDONE/MP3/".$filename."-all.mp3")) {
			$audioPlayer = " <p id='".$curRecord['filename']."'>Alternative content</p>  
        <script type='text/javascript'>  
        AudioPlayer.embed('".$curRecord['filename']."', {soundFile: '$mp3File'});  
        </script> "; } 
							
			
			
			
			
			//"<audio controls='controls' >
				//  <source src=http://192.168.1.2/RECORDINGS/MP3/".$curRecord['phone_number']."_".$curRecord['user']."-all.mp3  type='audio/mpeg' /></audio>"; } 
				  
				  
				  
				 else { $audioPlayer = "Não há gravação! :("; }
				$lenghtInMin = secondsToTime($curRecord['length_in_sec']);
				//if ($curRecord['status'] == 'SALE') { $saleMade = "bgcolor='#00CC00'"; } else { $saleMade = ""; }
			echo "<tr ".$saleMade."><td>".$curRecord['start_time']."</td>
			      <td>".$lenghtInMin['m'].":".$lenghtInMin['s']."</td>
				  
				  <td>".$curRecord['filename']."</td>
				  <td>".$curRecord['user']."</td>
				  <td><div id='".$curRecord['filename']."a' style='DISPLAY: block' ><a href='#' onclick='enableAudio(".$curRecord['filename'].")'>Ouvir Audio</a></div>
				  <div id='".$curRecord['filename']."' style='DISPLAY: none' > ".$audioPlayer." </div></td><td><a href='$mp3File' >Download</a></td></tr>";

		}
		
	echo "<tr><td colspan='6' align='right'><a href='#' onclick='lastpage()' >1</a>&nbsp;&nbsp;<a href='#' onclick='previouspage();'><<</a><label>&nbsp;".$pageNumb."&nbsp;</label><a href='#' onclick='nextpage();'>>></a>&nbsp;&nbsp;<a href='#' onclick='lastpage(".$totalPages.")' >$totalPages</a></td></tr>";						
	echo "</table>";							
?>
   
   </td>
</tr>
</table>

</body>
</html>
