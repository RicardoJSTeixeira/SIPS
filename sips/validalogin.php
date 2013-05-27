<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Login</title>

<script type="text/javascript">
	function logame(y) {				
    	document.getElementById('fazlogin').submit();
	}
</script>
	
	
<?php 
		$user = $_POST['VD_login'];
		$pass = $_POST['VD_pass'];
		$pc = $_POST['pc'];
		if ($user != NULL) {
		
		
		$con = mysql_connect("localhost","root","admin");
		if (!$con)
  		{
  			die('Não me consegui ligar' . mysql_error());
  		}
		mysql_select_db("asterisk", $con);
		
		$result = mysql_query("SELECT user, pass, user_level FROM vicidial_users WHERE user = '$user' AND pass = '$pass' ") or die(mysql_error());
		
		$rr = mysql_fetch_assoc($result);
		
		
		
		
		
		mysql_close($con);
		
		 
?>


</head>

<body>

<form action="operador.php" method="post" id="fazlogin" target="_self">
    <input type="hidden" name="user" value="<? echo $user ?>" />
    <input type="hidden" name="pass" value="<? echo $pass ?>" />
    <input type="hidden" name="pc" value="<? echo $pc ?>" />
</form>


<?
if ($rr['user'] == '') { 
		 	?> 
            	<script type="text/javascript">
					alert('User ou password errados!');
					window.location = "login.php";
				</script>
				
            <?    
			} 
		 else {
			 
			if ($rr['user_level'] > 1) { 
				$url = "backoffice.html"
			
			?>
				<script type="text/javascript">
                window.location = "<? echo $url ?>"; 
                </script>
			<?
			} else { 
			?>
				<script type="text/javascript">
					logame();
				</script>
            <?
			}
			
			 
			} ;
		 
		
		
		
		}
		
		?>
</body>
</html>