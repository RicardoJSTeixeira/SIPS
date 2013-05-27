<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Página de Login</title>



<style type="text/css">
a:link,a:visited
{
display:block;
font-weight:bold;
color:#FFFFFF;
background-color:#98bf21;
width:120px;
text-align:center;
padding:4px;
text-decoration:none;
}
a:hover,a:active
{
background-color:#7A991A;
}
body label {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	text-align: center;
}
body label.header {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 20px;
	color:#006;
}
body td {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
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




<script type="text/javascript">

function validalogin() {
	

	var x=document.forms["flogin"]["VD_login"].value
if (x==null || x=='')
  {
  alert("Username é obrigatório");
  	return false;
  }
  
  var x=document.forms["flogin"]["VD_pass"].value
if (x==null || x=='')
  {
  alert("Password é obrigatório");
 	return false;
  }
	

}
</script>


</head>

<body>

<form name="flogin" id="flogin" onsubmit="validalogin()" method="post" action="validalogin.php"  target="_self" >



<table align="center" width="100%" cellpadding="1" cellspacing="1" border="0">
<tr><td colspan="2" align="center"><img src="img/sipslogo.jpg" align="middle" /><br /><br /><br /></td></tr>
<tr>
	<td width="50%" align="right"><label>Username:</label></td>
    <td width="50%" align="left"><input name="VD_login" type="text" size="22" /></td>
</tr>
<tr>
	<td align="right"><label>Password:</label></td>
    <td align="left"><input name="VD_pass" type="password" size="22" /></td>
</tr>
<tr>
  <td align="right">Computador:</td>
  <td>
  <select name="pc">
	<option value="---">---</option>
  	<option value="PC-01-PC">101</option>
    <option value="PC-02-PC">102</option>
    <option value="PC-03-PC">103</option>
    <option value="PC-04-PC">104</option>
    <option value="PC-05-PC">105</option>
    <option value="PC-06-PC">106</option>
    <option value="PC-07-PC">107</option>
    <option value="PC-08-PC">108</option>
    <option value="PC-09-PC">109</option>
    <option value="PC-10-PC">110</option>
    <option value="PC-11-PC">111</option>
    <option value="PC-12-PC">112</option>
    <option value="PC-13-PC">113</option>
    <option value="PC-14-PC">114</option>
    <option value="PC-15-PC">115</option>
    <option value="PC-16-PC">116</option>
    <option value="PC-17-PC">117</option>
    <option value="PC-18-PC">118</option>
    <option value="PC-19-PC">119</option>
    <option value="PC-20-PC">120</option>
  </select>
  </td>
</tr>
<tr>
  <td align="right">&nbsp;</td>
  <td>&nbsp;</td>
</tr>

<tr>
	<td align="right"><input type="submit" value="Login" style="height: 25px; width: 170px"  /></td>
    <td align="left"><input type="button" value="Registar Novo Utilizador" style="height: 25px; width: 170px" onclick="window.location = 'novocolab.php';" /></td>
</tr>
<tr><td colspan="2" align="center"><br /><br /></td></tr>


</table>
</form>






</body>
</html>