<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Mudar Password</title>
<style>
.header {
	font-family: Tahoma, Geneva, sans-serif;
	font-size: 14px;
	border:1px;
	border-style:solid;
	border-color:#000;
	background-color:#FFC;
	text-align:center;
}
</style>
<?php $user = $_GET["user"]; ?>

</head>
<body>
<form name="mudapass" action="mudapassaction.php" method="post" target="_self" >
<table align="center">
	<tr>
    	<td align="left"><label class="header">Insira a sua password:</label></td>
        <td align="center"><input type="password" name="oldpass" /></td>
    </tr>
	<tr>
    	<td align="left"><label class="header">Insira a sua nova password:</label></td>
        <td align="center"><input type="password" name="pass1" /></td>
    </tr>
    <tr>
    	<td align="left"><label class="header">Repita a sua nova password:</label></td>
        <td align="center"><input type="password" name="pass2" />
        <input type="hidden" name="user" value="<?php echo $user; ?>"  />
        
        </td>
    </tr>
    
    <tr>
    	<td align="center"><br /><input type="submit" value="Enviar"  /></td>
        <td align="center"><br /><input type="reset" value="Apagar" /></td>
    </tr>

</table>
</form>
</body>
</html>