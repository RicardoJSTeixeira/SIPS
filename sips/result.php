<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
<?php

	$satisf = $_POST['satisf'];
	$opactual = $_POST['opactual'];
	$preco = $_POST['preco'];
	
	$con = mysql_connect("localhost","root","admin");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	if ($satisf != "---"){ $satisfstr = "Satisf = $satisf"; }
	
	$sipsdb = mysql_query("SELECT * FROM t_leads WHERE Satisf = 'Satisfeito'") or die (mysql_error());
	
	$rsips = mysql_fetch_assoc($sipsdb);
	
	



$row_num = mysql_num_rows($sipsdb);
//echo $row_num;
//$sips = array();
$counter = 0;

echo "<table>";

while($row = mysql_fetch_array($sipsdb, MYSQL_NUM))
{
	//if ($row['0'] == $satisf){
		echo "<tr>";
		//echo "<td>";
//		echo $row['1'];
//		echo "</td>";
//		echo "<td>";
//		echo $row['2'];
//		echo "</td>";
		echo "<td>";
		echo $row['0'];
		echo "</td>";
		//echo "<td>";
//		echo $row['4'];
//		echo "</td>";			
		echo "<tr>";
		$counter = $counter + 1;
	//}
}
echo "</table>";

?>


</body>
</html>