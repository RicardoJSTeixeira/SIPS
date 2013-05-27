<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript">
window.moveTo(0,0);
window.resizeTo(screen.width,screen.height);

function sugestoes(valor)
{
	var url = valor;
	window.open(url, "Janela", "status=no, width=900, height=350, menubar =no, titlebar=no, scrollbars=yes")
}

function vendas(user)
{
	var url = "listaclientes.php?user=" + user;
	window.open (url, "status=yes, menubar =yes, titlebar=yes, scrollbars=yes");
}


function mail(user)
{
	var url = "enviamail.php?user=" + user;
	window.open (url,"Janela","status=no, menubar =no, titlebar=yes, scrollbars=no, width=850, height=350");
	
}

function ultimoscontactos(valor)
{
	var url = valor;
	window.open(url, "Janela", "status=no, width=550, height=700, menubar =no, titlebar=no, scrollbars=yes")
}


	
</script>

<?php
	$tlf = $_GET['num_tlf'];
	$user = $_GET['user'];
	
	$nome = $_GET['nome'];
	$morada = $_GET['morada'];
	$codpostal = $_GET['codpostal'];
	
	$con = mysql_connect("localhost","root","vicidialnow");
	if (!$con)
  	{
  		die('Não me consegui ligar' . mysql_error());
  	}
	mysql_select_db("sips", $con);
	
	$formacoes = mysql_query("SELECT * FROM t_formacoes 
								WHERE activo = '1' 
								ORDER by id DESC 
								LIMIT 5") or die(mysql_error());
	$lead = mysql_query("SELECT * FROM t_leads WHERE Tlf = '$tlf'") or 			 	die (mysql_error());
	$rlead = mysql_fetch_assoc($lead);
	
	
	$actual = 'false';

	if ($rlead['Nome'] != '') {
	
	$actual = 'true';	
	$nome = $rlead['Nome'];
	$morada = $rlead['Morada'];
	$tlf = $rlead['Tlf'];
	$tlml = $rlead['Tlml'];
	$email = $rlead['Email'];
	$codpostal = $rlead['CodPostal'];
	$bi = $rlead['BI'];
	$nif = $rlead['NIF'];
	$tvnetvoz = explode(',', $rlead['tvnetvoz']);
	$tecnologia = $rlead['Tecnologia'];
	$canais = $rlead['Canais'];
	$vnet = $rlead['VNet'];
	$pcham = $rlead['PCham'];
	$ppaga = $rlead['PPaga'];
	$opactual = $rlead['OPActual'];
	$satisf = $rlead['Satisf'];
	$dcontacto = $rlead['DContacto'];
	$dagend = $rlead['DAgend'];
	$coment = $rlead['Coment'];
	$fid = $rlead['Fid'];
	$fidano = $rlead['FidAno'];
	$fidmes = $rlead['FidMes'];
	$filhos = $rlead['Filhos'];
	$ntv = $rlead['NTv'];
	$cobertura = $rlead['Cobertura'];
	$casa = $rlead['Casa'];
	$resultcham = $rlead['ResultCham'];
	$tmorada = $rlead['tmorada'];
	
	}
	
	$data = date('j-n-Y');
	
	
	
	
	mysql_close($con);	
	
	for($i=0; $i<3; $i++) {
		if ($tvnetvoz[$i] == 'tv') { $tv = '1'; }
		else { 
			if ($tvnetvoz[$i] == 'net') { $net = '1'; }
			else {
				if ($tvnetvoz[$i] == 'voz') { $voz = '1'; }
			}
		}
	}
	
?>

<title><?php echo $nome ?></title>

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
body {
	background-color:#39C;
	background-repeat: no-repeat;
	background-position:right;
}

body input, select, textarea {
	background-color:#CCCCCC; }

</style>




</head>


<?php  
 $sorte = rand(1, 300);
 
 if ($sorte == 13) { echo "<body bgcolor='#FFFF00'>"; }
 else { echo "<body>"; }

?>

<form action="gravar_lead.php" name="vicilead" method="post" target="_self">
<table width="1030" align="center" id="main_site" border="1">
<tr>
<td width="208" height="80">
<table width="200px" align="left" id="barra_esquerda">
<tr>
<td align="center" height="70"><img src="img/sipslogo.jpg" width="200" height="68" />
<p><label class="header"><?php echo $user ?></label></p>
</td>
</tr>
</table>
</td>
<td width="600" rowspan="2">
<table width="600" align="center">

	<tr align="center">
	  <td colspan="5" align="center"><label class="header">Dados do Cliente - Preencher sempre que possível!</label><hr /></td>
    </tr>
	<tr>
    	<td width="91" align="center"><label>Nome:</label></td><td colspan="4" align="left"><input size="50" type="text" name="nome" id="nome" value="<?php echo $nome ?>"/>
        </td>
    </tr>
    <tr>
    <td height="25" align="center"><label>Morada:</label></td><td colspan="3" align="center">
      <input size="50" type="text" name="morada" id="morada" value="<?php echo $morada ?>" />
    </label></td>
    <td align="left"><input type="text" name="codpostal" id="codpostal" size="30" value="<?php echo $codpostal ?>" /></td>
    </tr>
    
    <tr>
    <td align="center"><label>Telefone:</label></td><td width="158" colspan="2" align="left"><input size="20
    " type="text" name="tlf" id="tlf" value="<?php echo $tlf ?>" /> </td>
    <td width="65" align="center">Telemovel:</td>
    <td width="266" align="left"><input size="20" type="text" name="tlml" id="tlml" value="<?php echo $tlml ?>" /></td>
    </tr>
    <tr>
      <td align="center">E-mail:</td>
      <td colspan="2" align="left">
      <input name="email" type="text" id="email" value="<?php echo $email ?>" /></td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="center">BI:</td>
      <td colspan="2" align="left"><label for="bi"></label>
        <input type="text" name="bi" id="bi" value="<?php echo $bi ?>" /></td>
      <td align="center">NIF:</td>
      <td align="left">
        <input type="text" name="nif" id="nif" value="<?php echo $nif ?>" /></td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" align="right">Data do último contacto</td>
      <td colspan="2" align="left">
        <input name="dcontacto" type="text" id="dcontacto" readonly="readonly" value="<?php echo $dcontacto; ?>" />
        <input type="hidden" name='tcli' value="<? echo $actual; ?>"  />
        
        </td>
      </tr>
    <tr>
      <td colspan="3" align="right" bgcolor="#66FF00" >Tipo de Morada</td>
      <td colspan="2" align="left" bgcolor="#66FF00">
        <select name="tmorada" id="tmorada">
          <option value="---" <?php if ($tmorada == '---') { echo 'selected="selected"';}?>>---</option>
          <option value="Cabo/Fibra" <?php if ($tmorada == 'Cabo/Fibra') { echo 'selected="selected"';}?>>Cabo/Fibra</option>
          <option value="Satelite/DTH" <?php if ($tmorada == 'Satelite/DTH') { echo 'selected="selected"';}?>>Satélite/DTH</option> 
          </select>
      </td>
      </tr>
    <tr align="center">
      <td colspan="5" align="center"><label class="header">Dados de Despiste - Preencher o máximo possível!<br />
      </label><Hr /></td>
    </tr>
    <tr>
      <td align="center">Actual Operador</td>
      <td colspan="2" align="center"><select name="opactual" id="opactual">
        <option value="---" <?php if ($opactual == '---') { echo 'selected="selected"';}?>>---</option> 
        <option value="ZON TVCabo" <?php if ($opactual == 'ZON TVCabo') { echo 'selected="selected"';}?>>ZON TVCabo</option>
        <option value="MEO" <?php if ($opactual == 'MEO') { echo 'selected="selected"';}?>>MEO</option>
        <option value="Cabovisão" <?php if ($opactual == 'Cabovisão') { echo 'selected="selected"';}?>>Cabovisão</option>
        <option value="Vodafone" <?php if ($opactual == 'Vodafone') { echo 'selected="selected"';}?>>Vodafone</option>
        <option value="Clix" <?php if ($opactual == 'Clix') { echo 'selected="selected"';}?>>Clix</option>
        <option value="PT Comunicações" <?php if ($opactual == 'PT Comunicações') { echo 'selected="selected"';}?>>PT Comunicações</option>
        <option value="Outro" <?php if ($opactual == 'Outro') { echo 'selected="selected"';}?>>Outro</option>
      </select></td>
      <td align="center">Grau de Satisfação</td>
      <td align="center">
        <select name="satisf" id="satisf">
          <option value="---" <?php if ($satisf == '---') { echo 'selected="selected"';}?>>---</option>
          <option value="Satisfeito" <?php if ($satisf == 'Satisfeito') { echo 'selected="selected"';}?>>Satisfeito</option>
          <option value="Indiferente" <?php if ($satisf == 'Indiferente') { echo 'selected="selected"';}?>>Indiferente</option>
          <option value="Insatisfeito" <?php if ($satisf == 'Insatisfeito') { echo 'selected="selected"';}?>>Insatisfeito</option>
          <option value="Quer mudar de operador" <?php if ($satisf == 'Quer mudar de operador') { echo 'selected="selected"';}?>>Quer mudar de operador</option>
      </select></td>
    </tr>
    <tr>
      <td align="center">Tipo de Serviço</td>
      <td colspan="2" align="center">
        <select name="tecnologia" id="tecnologia">
          <option value="---"  <?php if ($tecnologia == '---') { echo 'selected="selected"';}?>>---</option>	
          <option value="Cabo" <?php if ($tecnologia == 'Cabo') { echo 'selected="selected"';}?>>Cabo</option>
          <option value="Fibra" <?php if ($tecnologia == 'Fibra') { echo 'selected="selected"';}?>>Fibra</option>
          <option value="ADSL" <?php if ($tecnologia == 'ADSL') { echo 'selected="selected"';}?>>ADSL</option>
          <option value="Sat" <?php if ($tecnologia == 'Sat') { echo 'selected="selected"';}?>>Satélite</option>
          <option value="Tlf" <?php if ($tecnologia == 'Tlf') { echo 'selected="selected"';}?>>Só Telefone</option>
      </select></td>
      <td align="center">Serviços</td>
      <td align="left"><p>
        <label>
          <input type="checkbox" name="tvnetvoz[]" value="tv" <?php if ($tv == '1') { echo 'checked = "yes"'; } ?>/>
          TV</label>
        <br />
        <label>
          <input type="checkbox" name="tvnetvoz[]" value="net" <?php if ($net == '1') { echo 'checked = "yes"'; } ?> />
          NET</label>
        <br />
        <label>
          <input type="checkbox" name="tvnetvoz[]" value="voz" <?php if ($voz == '1') { echo 'checked = "yes"'; } ?> />
          VOZ</label>
        <br />
      </p></td>
    </tr>
    <tr>
      <td align="center">Preço que paga</td>
      <td colspan="2" align="center">
        <select name="preco" id="preco">
          <option value='---' <?php if ($ppaga == '---') { echo 'selected="selected"';}?>>---</option>
          <option value="10" <?php if ($ppaga == '10') { echo 'selected="selected"';}?>>&lt; 10 €</option>
          <option value="20" <?php if ($ppaga == '20') { echo 'selected="selected"';}?>>10 - 20€</option>
          <option value="30" <?php if ($ppaga == '30') { echo 'selected="selected"';}?>>20 - 30€</option>
          <option value="40" <?php if ($ppaga == '40') { echo 'selected="selected"';}?>>30 - 40€</option>
          <option value="50" <?php if ($ppaga == '50') { echo 'selected="selected"';}?>>40 - 50€</option>
          <option value="60" <?php if ($ppaga == '60') { echo 'selected="selected"';}?>>50 - 60€</option>
          <option value="70" <?php if ($ppaga == '70') { echo 'selected="selected"';}?>>&gt; 60 €</option>
      </select></td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="center">Fidelização</td>
      <td align="left"><p><br />
      </p>        </td>
      <td align="left"><label>
        <input type="radio" name="fid" value="1" id="Fid_0" <?php if ($fid == '1') { echo 'checked="checked"';}?> />
        Sim</label>
        <br />
        <label>
          <input type="radio" name="fid" value="0" id="Fid_1" <?php if ($fid == '0') { echo 'checked="checked"';}?> />
          Não</label></td>
      <td align="center">Data que termina</td>
      <td align="center">
        <select name="fidmes" id="fidmes">
          <option value="---" <?php if ($fidmes == '---') { echo 'selected="selected"';}?>>---</option>	
          <option value="Jan" <?php if ($fidmes == 'Jan') { echo 'selected="selected"';}?>>Janeiro</option>
          <option value="Fev" <?php if ($fidmes == 'Fev') { echo 'selected="selected"';}?>>Fevereiro</option>
          <option value="Mar" <?php if ($fidmes == 'Mar') { echo 'selected="selected"';}?>>Março</option>
          <option value="Abr" <?php if ($fidmes == 'Abr') { echo 'selected="selected"';}?>>Abril</option>
          <option value="Mai"<?php if ($fidmes == 'Mai') { echo 'selected="selected"';}?>>Maio</option>
          <option value="Jun" <?php if ($fidmes == 'Jun') { echo 'selected="selected"';}?>>Junho</option>
          <option value="Jul" <?php if ($fidmes == 'Jul') { echo 'selected="selected"';}?>>Julho</option>
          <option value="Ago" <?php if ($fidmes == 'Ago') { echo 'selected="selected"';}?>>Agosto</option>
          <option value="Set" <?php if ($fidmes == 'Set') { echo 'selected="selected"';}?>>Setembro</option>
          <option value="Out" <?php if ($fidmes == 'Out') { echo 'selected="selected"';}?>>Outubro</option>
          <option value="Nov" <?php if ($fidmes == 'Nov') { echo 'selected="selected"';}?>>Novembro</option>
          <option value="Dez" <?php if ($fidmes == 'Dez') { echo 'selected="selected"';}?>>Dezembro</option>
      </select>
       
        <select name="fidano" id="fidano">
          <option value="---" <?php if ($fidano == '---') { echo 'selected="selected"';}?>>---</option>
          <option value="2010" <?php if ($fidano == '2010') { echo 'selected="selected"';}?>>2010</option>
          <option value="2011" <?php if ($fidano == '2011') { echo 'selected="selected"';}?>>2011</option>
          <option value="2012" <?php if ($fidano == '2012') { echo 'selected="selected"';}?>>2012</option>
          <option value="2013" <?php if ($fidano == '2013') { echo 'selected="selected"';}?>>2013</option>
      </select></td>
    </tr>
    <tr>
      <td colspan="5" align="center">
      <label class="header">Comentários<br />
      </label><Hr />
      </td>
      </tr>
    <tr>
      <td colspan="5" align="center"><p><label>Insira aqui os seus comentários</label></p>
        <p>
          <textarea name="coment" id="coment" cols="80" rows="3" style="border:double"></textarea>
        </p>
        <p><label>Histórico de comentários</label></p>
        <p><textarea name="coment_log" cols="80" rows="8" readonly="readonly" id="coment_log" style="border:double" ><?php echo $coment; ?></textarea></p></td>
      </tr>
    <tr>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">Resultado da chamada:</td>
      <td colspan="2" align="left"><p>
        <label>
          <input type="radio" name="resultvenda" value="cback" id="ResultVenda_0" <?php if ($resultcham == 'cback') { echo 'checked="checked"';}?> />
          Callback</label>
        <br />
        <label>
          <input type="radio" name="resultvenda" value="venda" id="ResultVenda_1" <?php if ($resultcham == 'venda') { echo 'checked="checked"';}?>/>
          Venda feita</label>
        <br />
        <label>
          <input type="radio" name="resultvenda" value="jacli" id="ResultVenda_2" <?php if ($resultcham == 'jacli') { echo 'checked="checked"';}?>/>
          Já cliente</label>
        <br />
        <label>
          <input type="radio" name="resultvenda" value="ni" id="ResultVenda_3" <?php if ($resultcham == 'ni') { echo 'checked="checked"';}?>/>
          Não interessado</label>
        <br />
        <label>
          <input type="radio" name="resultvenda" value="decline" id="ResultVenda_4" <?php if ($resultcham == 'decline') { echo 'checked="checked"';}?>/>
          Rejeitou chamada</label>
        <br />
        <label>
          <input type="radio" name="resultvenda" value="Não atendeu" id="ResultVenda_5" <?php if ($resultcham == 'na') { echo 'checked="checked"';}?>/>
          Não Atendeu</label>
        <br />
      </p></td>
      </tr>
    <tr>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
      <td align="left">&nbsp;</td>
      <td align="center"><input type="hidden" name="user" id="user" value="<?php echo $user ?>" /></td>
    </tr>
    <tr>
      <td colspan="5" align="center"><input type="submit" name="Submeter" id="Submeter" value="Submeter" />&nbsp;&nbsp;
        <input type="button" disabled="disabled" name="Converter em venda" id="Converter em venda" value="Converter em venda" /> &nbsp;&nbsp;       <input type="submit" name="Descartar alterações" id="Descartar alterações" disabled="disabled" value="Descartar alterações" /></td>
      </tr>
    <tr><td><br /><br /></td></tr>
  </table>
  </td>
  <td width="200" rowspan="2" valign="top">
  <table width="200" align="right" id="barra_direita">
  
  <!--<tr>
  	<td align="center"><input type="button" class="bbuttons" value="Pausa"  name="pause" disabled="disabled"/></td>
    <td align="center"><input type="button" class="bbuttons" value="Pronto"  name="ready" disabled="disabled"/></td>
  </tr>
  <tr>
  	<td align="center"><input type="button" class="bbuttons" value="Desligar"  name="hangup" disabled="disabled"/></td>
    <td align="center"><input type="button" class="bbuttons" value="Chamada Manual"  name="mdial" disabled="disabled"/></td>
    
  </tr>
  <tr>
  	<td align="center"><input type="button" class="bbuttons" value="Callback"  name="cback" disabled="disabled"/></td>
    <td align="center"><input type="button" class="bbuttons" value="Mute"  name="mute" disabled="disabled"/></td>
  </tr> !-->
  <tr>
	<td align="center" valign="top" colspan="2"><br /><br /><input class="abuttons" type="button" value="As minhas vendas" onclick="vendas(&quot;<?php echo $user; ?>&quot;)" /><br /><br /></td>
	</tr>
    <tr>
    <td align="center" valign="top" colspan="2"><input class="abuttons" type="button" value="Enviar E-Mail" onclick="mail(&quot;<?php echo $user; ?>&quot;)" /><br /><br />
    </td>
    </tr>
    <tr>
    <?php $url = "presencasteste.php?user=".$user; ?>
    	<td align="center" colspan="2"><input class="abuttons" type="button" value="Mapa Faltas" onclick="window.open('<?php echo $url; ?>', 'Name');" />
    </tr>
    <tr>
    <td align="center" colspan="2">
    <br />
     <?php $link3 = "ultimoscontactos.php?user=".$user; ?>
        <input class="abuttons" type="button" name="uc" value="Últimos Contactos" onclick=ultimoscontactos("<?php echo $link3; ?>") />
    </td>
    </tr>
    <tr>
    <td align="center" colspan="2">
    <br />
    <?php $link4 = "sugestoes.php?user=".$user; ?>
        <input class="abuttons" type="button" name="sugestoesasd" value="Sugestões" onclick="sugestoes('<?php echo $link4; ?>')" />
    </td>
    </tr>
  <tr><td align="center" colspan="2"><br /><br /><br />
  Últimas novidades ZON:</td></tr>
  <?php
  	
  	for ($i=0; $i<mysql_num_rows($formacoes); $i++){
			$form = mysql_fetch_assoc($formacoes);
			echo "<tr><td align='center' colspan='2'><br>";
			echo "<a href='".$form['path']."' target='_new' >".$form['descr']."</a>";
			echo "</td></tr>";
	}
  
  ?>
  
  
  </table>
  
  
     </td>
  </tr>
<tr>
  <td align="center" valign="top" >
    <p>&nbsp;</p>
    <p>Tempo utilizado em intervalo</p>
    
    <?php
	
	$user = $_GET['user'];
	
	
	$con = mysql_connect("localhost","sipsadmin", "sipsps2012");
	if (!$con)
	{
		die('Não me consegui ligar' . mysql_error());
	}
	mysql_select_db("asterisk", $con);
	
	
	
	$dia = date('d');
	$mes = date('m');
	$ano = date('o');
	
	

	
	for ($i=0;$i<6;$i++)
	{
	$diainicio = ($dia - $i);
	$diafim = ($dia -$i + 1);
	
	$datainicio = $ano."-".$mes."-".$diainicio;
	$datafim = $ano."-".$mes."-".$diafim;
	
	
	
	$interv = mysql_query("SELECT Sum(pause_sec) FROM vicidial_agent_log WHERE event_time > '$datainicio' AND event_time < '$datafim' AND user = '$user' AND sub_status='Interv'") or die(mysql_error());
	
	
	
					
	$rinterv = mysql_fetch_assoc($interv);
	$intervalo[$i] = number_format($rinterv['Sum(pause_sec)'] / 60, 0);
	
	}
	
	$dia = date('d');
	
	
	mysql_close($con);
?>
    
    
    <table width="200" border="0">
      <tr>
        <td align="center"><p>Dia</p>
          <p>&nbsp;</p></td>
        <td align="center"><p>Minutos em intervalo</p>
          <p>&nbsp;</p></td>
        </tr>
      <tr>
        <td width="54" align="center">Dia <?php echo ($dia - 5); ?></td>
        <td width="136" align="center" <?php if($intervalo[5]>20) { echo ("bgcolor='#FF0000'"); } ?>><?php echo $intervalo[4]; ?> Mins</td>
        </tr>
      <tr>
        <td align="center">Dia <?php echo ($dia - 4); ?></td>
        <td align="center" <?php if($intervalo[4]>20) { echo ("bgcolor='#FF0000'"); } ?>><?php echo $intervalo[4]; ?> Mins</td>
        </tr>
      <tr>
        <td align="center">Dia <?php echo ($dia - 3); ?></td>
        <td align="center" <?php if($intervalo[3]>20) { echo ("bgcolor='#FF0000'"); } ?>><?php echo $intervalo[3]; ?> Mins</td>
        </tr>
      <tr>
        <td align="center">Dia <?php echo ($dia - 2); ?></td>
        <td align="center" <?php if($intervalo[2]>20) { echo ("bgcolor='#FF0000'"); } ?>><?php echo $intervalo[2]; ?> Mins</td>
        </tr>
      <tr>
        <td align="center">Dia <?php echo ($dia - 1); ?></td>
        <td align="center" <?php if($intervalo[1]>20) { echo ("bgcolor='#FF0000'"); } ?>><?php echo $intervalo[1]; ?> Mins</td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
        </tr>
        <tr>
        <td align="center" style="font-size:18px">Hoje</td>
        <td align="center" <?php if($intervalo[0]>20) { echo ("bgcolor='#FF0000'"); } ?> style="font-size:18px"><?php echo $intervalo[0]; ?> Mins</td>
        </tr>
        <tr><td colspan="2"><br /><br /></td>
        <tr>

        	<td colspan=2 align="center" bgcolor="#CCFF00"><hr />
        	  <p>
       	      Tem as suas informações actualizadas?</p>
        	  <p>Verifique <a href="myedit.php?user=<? echo $user; ?>" target="_blank">aqui!</a><br />
      	      </p><hr />
            </td>
        </tr>
        <tr><td colspan="2" align="center"><br /><br />
        <a href="Campanhas 2011.pdf" target="_blank" name="camp2011">Campanhas ZON</a></td></tr>
        <tr><td colspan="2" align="center"><br /><br />
        <a href="Campanhas A.pdf" target="_blank" name="campA">Campanhas Células Vermelhas</a></td></tr>
        <tr><td colspan="2" align="center"><br /><br />
        <a href="http://www.cabovisao.pt" target="_blank">Consultar moradas Cabovisão</a></td></tr>
        
      </table>
    </td>
</tr>
</table>
</form>
</body>
</html>