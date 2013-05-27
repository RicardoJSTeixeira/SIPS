<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Gestão de entrevistas</title>
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


table.sample {
	border-width: 1px;
	border-spacing: 2px;
	border-style: dotted;
	border-color: blue;
	border-collapse: collapse;
	background-color: rgb(250, 240, 230);
}
table.sample th {
	border-width: 1px;
	padding: 3px;
	border-style: dotted;
	border-color: black;
	background-color: white;
	-moz-border-radius: ;
	font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	text-align:center;
}
table.sample td {
	border-width: 1px;
	padding: 3px;
	border-style: dotted;
	border-color: black;
	background-color: white;
	-moz-border-radius: ;
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
<?php

	$data = date('d-m-o');


	$con = mysql_connect("mysql5.host-services.com","joaocam_admin","admin1234");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("joaocam_sips", $con);
	
	$entrevistas = mysql_query("SELECT * FROM t_recruta INNER JOIN t_entrevista ON t_recruta.id = t_entrevista.idrecruta WHERE t_entrevista.data_e >= '$data' GROUP BY t_recruta.tlml ORDER BY t_entrevista.data_e ASC ") or die(mysql_error());
	
	$numrows = mysql_num_rows($entrevistas);
	

	
	

	mysql_close($con);
?>



</head>

<body>

<table align="center" width="100%" class="sample">

<tr >
	<th>Nome</th>
    <th>Telemóvel</th>
    <th>Localidade</th>
    <th>E-Mail</th>
    <th>Nacionalidade</th>
    <th>Idade</th>
    <th>Data Entrevista</th>
    <th>Compareceu?</th>
<tr>

<?
	for ($i=0;$i<$numrows;$i++) {
		
		$data1 = $a['data_e'];
		
		$a = mysql_fetch_assoc($entrevistas);
		
		if ($data1 != $a['data_e']) { 
			echo "<tr><th colspan='8'>Entrevistas para dia ".$a['data_e']." <hr/></th></tr>";
		
		}
		
		echo "	
			<tr>
				<td>".utf8_encode($a['nome'])."</td>
				<td>".$a['tlml']."</td>
				<td>".utf8_encode($a['localidade'])."</td>
				<td>".$a['email']."</td>
				<td>".utf8_encode($a['nacionalidade'])."</td>
				<td>".$a['idade']."</td>
				<td>".$a['data_e']."</td>
				<td><input type='checkbox' name='compareceu' /></td>
			</tr>
		";
		
	}

?>

</table>

</body>
</html>