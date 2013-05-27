<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

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
}
body label.header {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 20px;
	color:#006;
}

body tr {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 10px;
	border:thin;
	border-width:thick;
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

}

.text {
	font-size: 12px;
	font: Verdana, Geneva, sans-serif;
}
.lista {
	font-size:12px;
	font:Verdana, Geneva, sans-serif;
	font-style:normal;
	border:1px;
	border-style:solid;
	border-color:#000;
	padding:3px;
	text-align:left;
}
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


<?php
	
	
	$id = $_GET['id'];

	
	$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("integra_bd_2", $con);

	$cliente = mysql_query("
	select 
    `t_cl`.`Nome`,
    `t_cl`.`BI`,
    `t_cl`.`NIF`,
    `t_cl`.`Contacto`,
    `t_cl`.`contacto_alt` as '2º Contacto',
    `t_cl`.`email` as 'E-Mail',
    `t_mo`.`Rua`,
    `t_mo`.`Porta`,
    `t_mo`.`Andar`,
    cast(concat(`t_mo`.`cod_postal`,
            '-',
            `t_mo`.`cod_rua`)as char) as 'Código Postal',
    `t_mo`.`Localidade`
from
    `t_contrato` as `t_co`
        inner join
    `t_cliente` as `t_cl` ON `t_co`.`id_cliente` = `t_cl`.`id_cliente`
        inner join
    `t_morada` as `t_mo` ON `t_co`.`id_morada` = `t_mo`.`id_morada`
where
    `id_contrato` = '$id';
	
	");
	
	mysql_close($con);
	
	$rcliente = mysql_fetch_assoc($cliente);

	
?>
</head>

<body>

	<table align="center">
    	<tr>
        	<td class="header">Nome</td>
            <td class="header">BI</td>
            <td class="header">NIF</td>
            <td class="header">Contacto</td>
            <td class="header">2º Contacto</td>
            <td class="header">E-Mail</td>
    	</tr>
    	<tr>
        	<td class="lista"><?php echo utf8_encode($rcliente['Nome']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['BI']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['NIF']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['Contacto']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['2º Contacto']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['E-Mail']); ?></td>
        </tr>
    </table>
<bR />
<br />
<br />
	<table align="center">
    	<tr>
        	<td class="header">Rua</td>
            <td class="header">Porta</td>
            <td class="header">Andar</td>
            <td class="header">Código Postal</td>
            <td class="header">Localidade</td>
        </tr>
        <tr>
        	<td class="lista"><?php echo utf8_encode($rcliente['Rua']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['Porta']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['Andar']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['Código Postal']); ?></td>
            <td class="lista"><?php echo utf8_encode($rcliente['Localidade']); ?></td>
        </tr>
    </table>
</body>
</html>