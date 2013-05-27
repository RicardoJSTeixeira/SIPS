<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Sistemas Internos da Purosinónimo</title>
<style type="text/css">
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

body td.header {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	font-style:italic;
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
</style>



<script language ="javascript">


function dadoscli(valor)
{

var url = valor;

window.open (url, "Janela", "status=no, width=550, height=200, menubar =no, titlebar=no");
}

function mudapass(valor)
{
	var url = valor;
	window.open (url, "Janela", "status=no, width=550, height=200, menubar =no, titlebar=no");
}

function ultimoscontactos(valor)
{
	var url = valor;
	window.open(url, "Janela", "status=no, width=550, height=700, menubar =no, titlebar=no, scrollbars=yes")
}

function estatisticas(valor)
{
	var url = valor;
	window.open (url, "Janela", "status=no, width=550, height=400, menubar =no, titlebar=no");
}


</script> 


</head>

<?php
	$user = $_GET['user'];
	$mes = $_POST['mes'];
	if ($mes == NULL) { $mes = date('m'); }
	$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("integra_bd_2", $con);
	
	//echo base64_encode(strtoupper(md5($password)));
 

	$users = mysql_query("SELECT * FROM t_utilizador WHERE Utilizador = '$user'")or die (mysql_error());
	
	$rusers = mysql_fetch_assoc($users);
	$id = $rusers['ID_Colaborador'];

	
	/** **/
	$curYear  = date('Y');
	if ($mes>0){$curMonth = $mes;} else { $curMonth = date('n'); $mes = $curMonth; }
	$data = $curYear.'-'.$curMonth.'-01';
	$datafim = $curYear.'-'.$curMonth.'-31';
	
	mysql_select_db("integra_bd_2", $con);
	
	

	
	$contratos = mysql_query("
	
select 
    `tc`.`id_contrato` as 'ID_Contrato',
    `t_cliente`.`nome` as 'Nome Cliente',
    `t_estado_interno`.`tipo_estado` as Estado,
    `tc`.`data_estado_interno` as 'Data do Estado',
    `TV`,
    `NET`,
    `VOZ`,
    (if(tc.id_estado_interno = 2 or tc.id_estado_interno = 3 or tc.id_estado_interno = 11, if(`g_tv` = 1,if(`TV` = 'TDT', 0, 1), 0)+if(`g_net` = 1, 1, 0)+if(`g_voz` = 1, 1, 0),0)) as 'Nº de pontos',
    `tc`.`comentario` as 'Comentários'
from
    `t_contrato` as `tc`
        inner join
    `t_estrutura` ON `tc`.`id_colaborador` = `t_estrutura`.`id_estrutura`
        inner join
    `t_cliente` ON `tc`.`id_cliente` = `t_cliente`.`id_cliente`
        inner join
    `t_estado_interno` ON `tc`.`id_estado_interno` = `t_estado_interno`.`id_estado_interno`
        inner join
    (select 
        `servico` as `tv`, `id_grade` as `g_tv`, `id_contrato`
    from
        `t_estado_servico`
    inner join `t_servicos` ON `t_servicos`.`id_servico` = `t_estado_servico`.`id_servico`
    where
        `t_servicos`.`id_categoria` = 1 ) as `tab_tv` ON `tc`.`id_contrato` = `tab_tv`.`id_contrato`
        left join
    (select 
        `servico` as `net`, `id_grade` as `g_net`, `id_contrato`
    from
        `t_estado_servico`
    inner join `t_servicos` ON `t_servicos`.`id_servico` = `t_estado_servico`.`id_servico`
    where
        `t_servicos`.`id_categoria` = 2) as `tab_net` ON `tc`.`id_contrato` = `tab_net`.`id_contrato`
        left join
    (select 
        `servico` as `voz`, `id_grade` as `g_voz`, `id_contrato`
    from
        `t_estado_servico`
    inner join `t_servicos` ON `t_servicos`.`id_servico` = `t_estado_servico`.`id_servico`
    where
        `t_servicos`.`id_categoria` = 3) as `tab_voz` ON `tc`.`id_contrato` = `tab_voz`.`id_contrato`
WHERE
    `tc`.`id_colaborador` = '$id' and `data_estado_interno` >= '$data' and `data_estado_interno` <= '$datafim'
order by `data_estado_interno` ASC;") or die(mysql_error());
	
	

	mysql_close($con);
?>
<body>
<table width="100%" border="1">
  <tr>
    <td>
      <table align="center">
        <tr>
        
          <form name="filtra" action="listaclientes.php?user=<?php echo $user; ?>" method="post" target="_self">
            <td>
              <select id="mes" name="mes">
                <option value="1" <?php if ($mes=='1') {echo 'selected=selected';} ?>>Janeiro</option>
                <option value="2" <?php if ($mes=='2') {echo 'selected=selected';} ?>>Fevereiro</option>
                <option value="3" <?php if ($mes=='3') {echo 'selected=selected';} ?>>Março</option>
                <option value="4" <?php if ($mes=='4') {echo 'selected=selected';} ?>>Abril</option>
                <option value="5" <?php if ($mes=='5') {echo 'selected=selected';} ?>>Maio</option>
                <option value="6" <?php if ($mes=='6') {echo 'selected=selected';} ?>>Junho</option>
                <option value="7" <?php if ($mes=='7') {echo 'selected=selected';} ?>>Julho</option>
                <option value="8" <?php if ($mes=='8') {echo 'selected=selected';} ?>>Agosto</option>
                <option value="9" <?php if ($mes=='9') {echo 'selected=selected';} ?>>Setembro</option>
                <option value="10" <?php if ($mes=='10') {echo 'selected=selected';} ?>>Outubro</option>
				<option value="11" <?php if ($mes=='11') {echo 'selected=selected';} ?>>Novembro</option>
				<option value="12" <?php if ($mes=='12') {echo 'selected=selected';} ?>>Dezembro</option>
              </select>
              <input type="submit" name="mudames" value="Mudar o mês"/>
              <input type="hidden" name='user' id='user' value="<?php echo $user; ?>" />
              <input type="hidden" name='password' id='password' value="<?php echo $password; ?>" />
            </td>
          </form>
        </tr>
        <tr>
          <td>
            <?php
		$numrows = mysql_num_rows($contratos);
	
	echo "<table border='2' cellspacing='0' align='center'>";
	echo "<tr>";
	echo "<td class='header' align='center'>Nome Cliente</td><td align='center' class='header'>Estado</td><td align='center' class='header'>Data do Estado</td><td align='center' class='header'>TV</td><td align='center' class='header'>NET</td><td class='header' align='center'>VOZ</td><td align='center' class='header'>Nº de pontos</td><td align='center' class='header'>Comentários</td><td class='header' align='center'>Dados do Cliente</td>";
	echo "</tr>";
	$totalpontos = 0;
	$pintor = true;
	for($i=0; $i<$numrows; $i++){
	
	$rcontratos = mysql_fetch_assoc($contratos);
	$pontos = $rcontratos['Nº de pontos'];
	$totalpontos = $totalpontos + $pontos;
	
	
	
	
	if ($pintor) { echo "<tr style='border:thin'>"; $pintor = false; } else { echo "<tr style='border:thin' bgcolor='#FFFF99'>"; $pintor = true; };
	
	echo "<td  align='center' class='lista'>";
	echo utf8_encode($rcontratos['Nome Cliente']);
	echo "</td>";
	echo "<td align='center' class='lista'>";
	echo utf8_encode($rcontratos['Estado']);
	echo "</td>";
	echo "<td  align='center' class='lista'>";
	
	$cdata = strtotime($rcontratos['Data do Estado']);
	$rdata = date('d-m-o', $cdata);
	
	echo utf8_encode($rdata);
	echo "</td>";
	echo "<td align='center' class='lista'>";
	echo utf8_encode($rcontratos['TV']);
	echo "</td>";
	echo "<td  align='center' class='lista'>";
	echo utf8_encode($rcontratos['NET']);
	echo "</td>";
	echo "<td align='center' class='lista'>";
	echo utf8_encode($rcontratos['VOZ']);
	echo "</td>";
	echo "<td align='center' class='lista'>";
	echo utf8_encode($rcontratos['Nº de pontos']);
	echo "</td>";
	echo "<td  align='center' class='lista'>";
	if ($rcontratos['Comentários'] == NULL) { echo '-'; } else {
	echo utf8_encode($rcontratos['Comentários']); }
	echo "</td>";
	echo "<td align='center' class='lista'>";
	$link = "dadoscliente.php?id=".$rcontratos['ID_Contrato'];
	echo "<input type='button' onclick=dadoscli('".$link."'); value='Ver Dados'>";
	echo "</td>";
	echo "</tr>";
	}
	echo "</table>";
	
	   
	
	?>
    

    
          </td>
        </tr>
        <tr><td colspan="9" align="center"><br /><br /><?php echo "Total de pontos : ".$totalpontos; ?></td></tr>
      </table>
    </td>
  </tr>
</table>



</body>
</html>