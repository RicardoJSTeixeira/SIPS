<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIPS - Nova Venda</title>
<style type="text/css">
<!--
.cabecalhos {
	font-style: italic;
}
.labels {
	font-size: 12px;
	text-align: left;
	
}
.botoes {
	height: 30px;
	width: 90px;
	
}
.botoessmall {
	background-image: url(img/calendar_icon.png);
	height: 30px;
	width: 25px;
}
.botoesbig {
	height: 30px;
	width: 230px;
}

-->
</style>



	



</head>

<body>

<form id="novocliente" name="novocliente" method="post" action="afterpost.php" enctype="multipart/form-data">
<table width="100%" align="left">
<tr>
  <td colspan="4">
  <table width="100%" align="center" border="0" style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;">
    <tr>
      <td align="left">
        <input  type="radio" name="grupo1" id="grupo1" onclick="clientechange(this);" value="1">Novo Cliente</input>
        <br />	
        <input  type="radio" name="grupo1" id="grupo1" onclick="clientechange(this);" value="0">Actual Cliente</input>
      </td>
      </tr>
    </table>
</td>
 </tr>


  <tr>
    <td width="45%" colspan="2" style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;"
    ><table width="100%" height="80" border="0"  align="left">
      <tr>
        <td width="28%" align="left"><label class="labels">Conta de cliente</label></td>
        <td><input  name="Cli_Numero_Cliente" type="text" id="Cli_Numero_Cliente" size="12" maxlength="10" /></td>
        </tr>
      <tr>
        <td align="center" class="labels"><label>Conta de Servi&ccedil;o</label></td>
        <td><input  name="Cont_Conta_Servico" type="text" id="Cont_Conta_Servico" size="12" maxlength="10" /></td>
        </tr>
      <tr>
        <td  align="center" class="labels"><label>Nº de actividade</label></td>
        <td><input  name="N_Activ" type="text" id="N_Activ" size="12" maxlength="9" /></td>
        </tr>
    </table></td>
    <td width="55%" colspan="2" style="border-width: 1px; border-color:#000000; border-style: dotted; border-color: gray;"><table width="100%" border="0" height="80" align="right">
      <tr>
        <td width="15%" align="center" class="labels">&nbsp;</td>
        <td width="19%" align="right">&nbsp;</td>
        <td width="66%" align="left">&nbsp;</td>
        </tr>
      <tr>
        <td align="center" class="labels">&nbsp;</td>
        <td colspan="2" align="left">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
  <tr>
  <td colspan="2" align="center" bgcolor="CCCCCC" class="cabecalhos" style="border-width: 1px; border-color:#000000; border-style: dotted; border-color: gray;">Identificação do Cliente</td>
  <td colspan="2" align="center" bgcolor="CCCCCC" class="cabecalhos" style="border-width: 1px; border-color:#000000; border-style: dotted; border-color: gray;">Morada de Instalação e Facturação</td>
  </tr>
  <tr>
  <td colspan="2" style="border-width: 1px; border-color:#000000; border-style: dotted; border-color: gray;"><table align="center" width="100%">
    <tr>
      <td width="13%" class="labels"> Nome </td>
      <td colspan="2"><input  name="nome" type="text" id="nome" size="35"/></td>
    </tr>
    <tr>
      <td class="labels">Contacto </td>
      <td colspan="5"><input  name="contacto" type="text" id="contacto" size="12" maxlength="9" /></td>
    </tr>
    <tr>
      <td class="labels">Contacto Alternativo </td>
      <td width="16%"><input  name="contacto_alt" type="text" id="contacto_alt" size="12" maxlength="9" /></td>
      <td colspan="3" style="text-align: center"><input  type="checkbox" name="portab" /><span class="labels">Portabilidade</span></td>
      </tr>
    <tr>
    <td><label class="labels">E-Mail</label></td>
    <td colspan="2"><input  name="email" type="text" id="email" size="35" /></td>
    </tr>
    <tr>
    <td><label class="labels">N.I.F.</label></td>
    <td colspan="4"><input  name="nif" type="text" id="nif" size="12" maxlength="9" />    
    </tr>
    <tr>
    <td><label class="labels">B.I.</label></td>
    <td colspan="4"><input  name="bi" type="text" id="bi" size="12" maxlength="9" />    </td>
    </tr>
  </table>
  </td>
  <td colspan="2" style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;">
    <table width="100%" align="center">
      <tr>
        <td width="10%"><label class="labels">Morada</label></td>
        <td colspan="4"><input  name="morada" type="text" id="morada" size="59" /></td>
        </tr>
      <tr>
        <td><label class="labels">Porta</label></td>
        <td colspan="2"><input  name="porta" type="text" id="porta" size="10" /></td>
        <td width="12%" align="right"><label class="labels">Andar</label></td>
        <td width="57%"><input   type="text" name="andar" id="andar" /></td>
        </tr>
      <tr>
        <td><label class="labels">Cód. Postal</label></td>
        <td width="9%"><input  name="codpostal" type="text" id="codpostal" size="6" maxlength="4" /></td>
        <td width="12%"><input  name="codrua" type="text" id="codrua" size="5" maxlength="3" /></td>
        <td align="right"><label class="labels">Localidade</label></td>
        <td><input type="text"  name="localidade" id="localidade" /></td>
        </tr>
      <tr>
        <td colspan="5" align="center"><input type="checkbox"  name="moradafact" /><label>Mesma morada para facturação</label></td>
        </tr>
      <tr>
        <td colspan="5" align="center">&nbsp;</td>
        </tr>
      </table>
  </td>
  </tr>
  <tr>
  <td colspan="4" align="center" bgcolor="CCCCCC" class="cabecalhos" style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;">Produtos e Serviços Subscritos</td>
  </tr>
  <tr>
  <td height="214" colspan="2" style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;">
      <table width="100%" height="242" >
      <tr>
      <td align="center"><label class="labels">Tipo</label></td>
      </tr>
      <tr>
      <td align="center">
      </td>
      </tr>
      <tr>
      <td >
      	<table align="center" style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;" width="100%">
        <tr>
        <td><label class="labels">TV</label></td>
        <td><label class="labels">NET</label></td>
        <td><label class="labels">VOZ</label></td>
        </tr>
        <tr>
        
         <? 
		$con = mysql_connect("serverintegra.dyndns.org","admin","integra");
	
		if (!$con)
		{ die('Não me consegui ligar' . mysql_error()); }
		mysql_select_db("integra_bd_2", $con);
        
		$tv = mysql_query("SELECT servico FROM t_servicos WHERE activo = '1' AND categoria = '1' ORDER BY servico ASC");
		$net = mysql_query("SELECT servico FROM t_servicos WHERE activo = '1' AND categoria = '2' ORDER BY servico ASC");
		$voz = mysql_query("SELECT servico FROM t_servicos WHERE activo = '1' AND categoria = '3' ORDER BY servico ASC");
		$box = mysql_query("SELECT equipamento FROM t_equipamentos WHERE categoria = '1' AND activo = '1' ORDER BY equipamento ASC");
		$net = mysql_query("SELECT equipamento FROM t_equipamentos WHERE categoria = '2' AND activo = '1' ORDER BY equipamento ASC");
		mysql_close($con);
		
		
	
		?>
        
        
        <td><select name="servtv" id="servtv"  readonly="readonly" />
        <option value='---'>---</option>
        		<? for($i=0;$i<mysql_num_rows($tv);$i++){
					$rtemp = mysql_fetch_assoc($tv);
					echo "<option value=".utf8_encode($rtemp['servico']).">".utf8_encode($rtemp['servico'])."</option>";	
				} ?>
            	
			</select>
        
        
        
        </td>
        <td>
        <select name="servnet" id="servnet"  readonly="readonly" />
        <option value='---'>---</option>
        		<? for($i=0;$i<mysql_num_rows($net);$i++){
					$rtemp = mysql_fetch_assoc($net);
					echo "<option value=".utf8_encode($rtemp['servico']).">".utf8_encode($rtemp['servico'])."</option>";	
				} ?>
            	
			</select>
        </td>
        <td>
        	<select name="servvoz" id="servvoz"  readonly="readonly" />
        		<option value='---'>---</option>
				
				<? for($i=0;$i<mysql_num_rows($voz);$i++){
					$rtemp = mysql_fetch_assoc($voz);
					echo "<option value=".utf8_encode($rtemp['servico']).">".utf8_encode($rtemp['servico'])."</option>";	
				} ?>
            	
			</select>
        
        </td>
        </tr>
        <tr>
        <td><input type="checkbox" name="novoservtv" id="novoservtv" onclick="if(this.checked) {document.novocliente.upservtv.checked=false; document.novocliente.mservtv.checked=false;}" /><label class="labels">Novo Serviço</label></td>
        <td><input type="checkbox" name="novoservnet" onclick="if(this.checked) {document.novocliente.upservnet.checked=false; document.novocliente.mservnet.checked=false;}" /><label class="labels">Novo Serviço</label></td>
        <td><input type="checkbox" name="novoservvoz" onclick="if(this.checked) {document.novocliente.upservvoz.checked=false; document.novocliente.mservvoz.checked=false;}" /><label class="labels">Novo Serviço</label></td>
        </tr>
        <tr>
        <td><input type="checkbox" name="upservtv" onclick="if(this.checked) {document.novocliente.novoservtv.checked=false; document.novocliente.mservtv.checked=false;}" /><label class="labels">Upgrade</label></td>
        <td><input type="checkbox" name="upservnet" onclick="if(this.checked) {document.novocliente.novoservnet.checked=false; document.novocliente.mservnet.checked=false;}" /><label class="labels">Upgrade</label></td>
        <td><input type="checkbox" name="upservvoz" onclick="if(this.checked) {document.novocliente.novoservvoz.checked=false; document.novocliente.mservvoz.checked=false;}" /><label class="labels">Upgrade</label></td>
        </tr>
        <tr>
        <td><input type="checkbox" name="mservtv" onclick="if(this.checked) {document.novocliente.upservtv.checked=false; document.novocliente.novoservtv.checked=false;}" /><label class="labels">Mantém</label></td>
        <td><input type="checkbox" name="mservnet" onclick="if(this.checked) {document.novocliente.upservnet.checked=false; document.novocliente.novoservnet.checked=false;}" /><label class="labels">Mantém</label></td>
        <td><input type="checkbox" name="mservvoz" onclick="if(this.checked) {document.novocliente.upservvoz.checked=false; document.novocliente.novoservvoz.checked=false;}" /><label class="labels">Mantém</label></td>
        </tr>
        </table> 
      </td>
      </tr>
      </table>
   </td>
   <td colspan="2" style="border-width: 1px; border-color:#000; border-style: dotted; border-color: gray;">
     <table width="100%">
       <tr>
         <td width="60%" align="center"><label class="labels">TV</label></td>
         <td width="40%" rowspan="6" align="center">
           <div style="overflow:auto;width:180px;height:175px;border:1px dotted #000;padding-left:5px" align="left">
           
			</div>

           </td>
         </tr>
       <tr>
         <td align="left"><label class="labels" style="padding:20px">1ª Box</label>&nbsp;
         <select name="1box" id="1box"  readonly="readonly" />
        		<option value='---'>---</option>
				
				<? for($i=0;$i<mysql_num_rows($box);$i++){
					$rtemp = mysql_fetch_assoc($box);
					echo "<option value=".utf8_encode($rtemp['equipamento']).">".utf8_encode($rtemp['equipamento'])."</option>";	
				} ?>
            	
			</select>
         
         </td>
         </tr>
       <tr>
         <td align="left"><label class="labels" style="padding:20px">2ª Box</label>&nbsp;
         <select name="2box" id="2box"  readonly="readonly" />
        		<option value='---'>---</option>
				
				<? 
					reset($box);
					for($i=0;$i<mysql_num_rows($box);$i++){
					$rtempa = mysql_fetch_assoc($box);
					echo "<option value=".utf8_encode($rtempa['equipamento']).">".utf8_encode($rtempa['equipamento'])."</option>";	
				} ?>
            	
			</select>
         </td>
         </tr>
       <tr>
         <td align="left"><label class="labels" style="padding:20px">3ª Box</label>&nbsp;
         <select name="3box" id="3box"  readonly="readonly" />
        		<option value='---'>---</option>
				
				<? 
				reset($box);
				for($i=0;$i<mysql_num_rows($box);$i++){
					$rtempb = mysql_fetch_assoc($box);
					echo "<option value=".utf8_encode($rtempb['equipamento']).">".utf8_encode($rtempb['equipamento'])."</option>";	
				} ?>
            	
			</select>
         
         
         </td>
         </tr>
       <tr>
         <td align="center"><label class="labels">Net+Voz</label></td>
         </tr>
       <tr>
         <td height="59" align="center"><label class="labels">Modem</label>&nbsp;
         <select name="netvoz" id="netvoz"  readonly="readonly" />
        		<option value='---'>---</option>
				
				<? 
				reset($box);
				for($i=0;$i<mysql_num_rows($net);$i++){
					$rtempc = mysql_fetch_row($net);
					echo "<option value=".utf8_encode($rtempc['equipamento']).">".utf8_encode($rtempc['equipamento'])."</option>";	
				} ?>
            	
			</select>
         
         </td>
         </tr>
       </table>
   </td>   
  </tr>
<tr>
<td colspan="2" style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;">
	<label class="labels">Observações</label><br />
	<textarea rows="4" style="width:455px"></textarea>
</td>
<td colspan="2" style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;">
  <br /><span class="labels">Nas observações devem ser indicados pedidos do cliente como tomadas adicionais, equipamentos, etc. e informações extra sobre o contrato como pedidos de net ou voz móvel.</span></td>
</tr>
<tr style="border-width: 1px; border-color:000000; border-style: dotted; border-color: gray;">

<td height="52" align="center" style="border-bottom-color:gray; border-bottom-width:1px; border-bottom-style:dotted; border-top-color:gray; border-top-width:1px; border-top-style:dotted; border-left-color:gray; border-left-width:1px; border-left-style:dotted;"><input type="submit" value="" name="submit" style="background-image:url(/img/submit.jpg); width:58px; height:50px;" title="Validar e enviar venda" /></td>
<td align="center" style="border-bottom-color:gray; border-bottom-width:1px; border-bottom-style:dotted; border-top-color:gray; border-top-width:1px; border-top-style:dotted;"><input type="button" value="" name="rascunho" style="background-image:url(/img/rascunho.jpg); width:58px; height:50px;" title="Guardar como rascunho" /></td>
<td align="center" style="border-bottom-color:gray; border-bottom-width:1px; border-bottom-style:dotted; border-top-color:gray; border-top-width:1px; border-top-style:dotted;"><input type="reset" value="" name="reset" style="background-image:url(/img/apagar.jpg); width:53px; height:50px;" title="Limpar todos os campos" /></td>
<td align="center" style="border-bottom-color:gray; border-bottom-width:1px; border-bottom-style:dotted; border-top-color:gray; border-top-width:1px; border-top-style:dotted; border-right-color:gray; border-right-width:1px; border-right-style:dotted;"><input type="button" value="" name="descartar" style="background-image:url(/img/delete.jpg); width:53px; height:50px;" title="Descartar venda" /></td>
</tr>
</table>
</form>
</body>
</html>
