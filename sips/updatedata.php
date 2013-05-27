<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<?

	


	$con = mysql_connect("mysql5.host-services.com","joaocam_admin","admin1234");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("joaocam_sips", $con);
	
	$entrevistas = mysql_query("SELECT * FROM t_entrevista") or die(mysql_error());
	
	$numrows = mysql_num_rows($entrevistas);

	for ($i=0; $i<$numrows; $i++) {
		
		$linha = mysql_fetch_assoc($entrevistas);
		$data = $linha['data_e'];
		$dc = explode('-',$data);
		
		$dataok = $dc[2]."-".$dc[1]."-".$dc[0];
		$id = $linha['id'];
		mysql_query("UPDATE t_entrevista SET data_e = '$dataok' WHERE id = $id ");



		echo "Data do id ".$id."updated para ".$dataok." <br />";
		
	}




?>





</head>

<body>
</body>
</html>