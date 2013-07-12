<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registo de Venda</title>
</head>

<style type="text/css">
.cc-mstyle {
 
	font-family: "Calibri";
	font-size: 12px; 
	background-color:#FFF;
	border: 2px solid #90C0E8;
	border-right: 2px solid #90C0E8;
	border-bottom: 2px solid #90C0E8;
	border-radius: 15px;


	margin-top:0;
	cursor:default;
}


.cc-mstyle label {
	color:#333;
}
.cc-mstyle table { 
	border:none; 
	padding:0;

	border-collapse: collapse;
	margin-left:auto;
	margin-right:auto;
}
.cc-mstyle tr:hover td {
	/* background-color:#e8edff; */
}
.cc-mstyle th {
	color: #000;
	font-size:12px;
	padding:10px 3px 10px 3px;
	width:auto;
}
.cc-mstyle th a {
	color: #000;
	font-size:12px;
	padding:2px 3px 2px 3px;
	text-decoration:none;
}
.cc-mstyle td {
	color: #000;
	font-size:12px;
	padding:1px 1px 1px 1px;
	width:auto;
	text-align:center;
}
.cc-mstyle td a {
	color: #000;
	font-size:12px;
	padding:0;
	text-decoration:none;
}


.cc-mstyle label {
	display:block;
	margin:0;
	padding:0px 0 0 0;
	margin:6px 0 -2px 0px; 
}
.cc-mstyle textarea {
	width:561px;
	border:1px solid #c0c0c0;
	margin:0;
	height:200px;
	background-color:#fff;
	resize:none;
	font:normal 12px/1.5em "Liberation sans", Arial, Helvetica, sans-serif;
}
.cc-mstyle input[type="text"] {
	width:400px;
	border:1px solid #c0c0c0;
	margin:0;
	height:28px;
	background-color:#fff;


}
.cc-mstyle input[type="password"] { 
	width:396px;
	border:1px solid #c0c0c0;
	margin:0;
	height:20px;
	background-color:#fff;


}

.cc-mstyle input[type="email"] { 
	width:396px;
	border:1px solid #c0c0c0;
	margin:0;
	height:20px;
	background-color:#fff;


}


.cc-mstyle select { 
	width:400px;
	padding: 4px;
	background-color: #FFF;
	border: 1px solid #c0c0c0;
	color: #000;
	height: 28px;
	width: 145px;
	margin-top:0px;
	text-align:center;
	font-size:12px;

}  
.cc-mstyle .checkbox { 
	margin: 4px 0; 
	padding: 0; 
	width: 14px;
	border: 0;
	background: none;
}


.full-table {border:none; border-radius:0px; width:635px; table-layout:fixed; border-collapse:collapse;}

.left {text-align:left !important; text-indent:10px;}
.right {text-align:right !important;}
.black {border: 1px solid black;}
.blacker {border: 2px solid black;}
.black-bottom {border-bottom:1px solid black !important;}

</style>

<body>
    
<?   
require("../../ini/dbconnect.php");
$lead_id=$_GET['id'];


$query = "

SELECT * FROM vicidial_list A INNER JOIN vicidial_users B ON A.user=B.user WHERE lead_id='$lead_id'


";

$query = mysql_query($query);
$row = mysql_fetch_assoc($query);


?>
<table class="cc-mstyle full-table">
<tr><td colspan="8"><b>VISITAS / PROSPECÇÃO</b></td></td></tr>

<tr style="height:50px"><td></td></tr>

<tr><td class='black right' colspan='1'><b>EMPRESA:</b></td><td class='black left' colspan='5'><?=$row['first_name']?></td><td class='black left' colspan='2'>Nif: <?=$row['middle_initial']?></td></tr>

<tr><td class='black right' colspan='1'><b>OPERADOR:</b></td><td class='black left' colspan='7'><?=$row['full_name']?></td></tr>


<tr><td class='black right' colspan='1'>Actividade: </td><td class='black left' colspan='3'><?=$row['extra13']?></td><td class='black left' colspan='2'>Data: <?=$row['extra14']?></td><td class='black left' colspan='2'>Hora: <?=$row['extra15']?></td></tr>


<tr><td class='black right' colspan='1'>Responsável:</td><td class='black left' colspan='5'><?=$row['vendor_lead_code']?></td><td class='black left' colspan='2'>Telemóvel: <?=$row['address3']?></td></tr>

<tr><td class='black right' colspan='1'>Telefone:</td><td class='black left' colspan='2'><?=$row['phone_number']?></td><td class='black left' colspan='3'>Email:</td><td class='black left' colspan='2'>Fax:</td></tr>

<tr><td class='black right' colspan='1'>Morada: </td><td class='black left' colspan='7'><?=$row['address1']?></td></tr>

<tr><td class='black right' colspan='1'>Cód. Postal:</td><td class='black left' colspan='3'> <?=$row['postal_code']?></td><td class='black left' colspan='4'>Localidade: <?=$row['city']?></td></tr>

<tr height="100px"><td colspan="8" valign="top"  class="black left">Observações:</td></tr>

<tr style="height:25px"><td></td></tr>

<tr><td class='black left' colspan='5'><b>VOZ FIXA</b></td><td class='black left' colspan='3' rowspan="4" valign="top">Observações:</td></tr>

<tr><td class='black left' colspan='2'>Operador: <?=$row['title']?></td><td class='black left' colspan='3'>Fid. e Data: <?=$row['last_name']?></td></tr>

<tr><td class='black left' colspan='5'>Qtd. de Linhas: <?=$row['extra1']?></td></tr>

<tr><td class='black left' colspan='5'>Mens. Actual: <?=$row['country_code']?></td></tr>

<tr><td class='black' colspan='2'>Multibanco: S ou N</td><td class='black' colspan='1'>Fax: S ou N</td><td class='black' colspan='1'>TV: S ou N</td><td class='black' colspan='2'>Alarme: S ou N</td><td class='black' colspan='2'>Central Telefónica: S ou N</td></tr>

<tr height="40px"><td class='black' colspan='8'><?=$row['extra2']?></tr>


<tr style="height:25px"><td></td></tr>

<tr><td class='black left' colspan='5'><b>NET FIXA</b></td><td class='black left' colspan='3' rowspan="4" valign="top">Observações:</td></tr>

<tr><td class='black left' colspan='2'>Operador: <?=$row['address2']?></td><td class='black left' colspan='3'>Fid. e Data: <?=$row['province']?></td></tr>

<tr><td class='black left' colspan='2'>Velocidade: <?=$row['extra3']?></td><td class='black left' colspan='3'>Tráfego: <?=$row['extra4']?></td></tr>

<tr><td class='black left' colspan='5'>Mens. Actual: <?=$row['date_of_birth']?></td></tr>

<tr style="height:25px"><td></td></tr>

<tr><td class='black left' colspan='5'><b>VOZ MÓVEL</b></td><td class='black left' colspan='3' rowspan="5" valign="top">Observações:</td></tr>

<tr><td class='black left' colspan='2'>Operador: <?=$row['email']?></td><td class='black left' colspan='3'>Fid. e Data: <?=$row['security_phrase']?></td></tr>

<tr><td class='black left' colspan='5'>Carregamentos: </td></tr>

<tr><td class='black left' colspan='5'>Qtd. Tlm: <?=$row['extra5']?></td></tr>

<tr><td class='black left' colspan='2'>Mensalidade: <?=$row['extra6']?></td><td class='black left' colspan='3'>Minutos:</td></tr>

<tr style="height:25px"><td></td></tr>

<tr><td class='black left' colspan='5'><b>NET MÓVEL</b></td><td class='black left' colspan='3' rowspan="4" valign="top">Observações:</td></tr>

<tr><td class='black left' colspan='2'>Operador: <?=$row['extra7']?></td><td class='black left' colspan='3'>Fid. e Data: <?=$row['extra8']?></td></tr>

<tr><td class='black left' colspan='2'>Velocidade: <?=$row['extra9']?></td><td class='black left' colspan='3'>Tráfego: <?=$row['extra10']?></td></tr>

<tr><td class='black left' colspan='2'>Mens. Actual: <?=$row['extra12']?></td><td class='black left' colspan='3'>Qtd Pen's: <?=$row['extra11']?></td></tr>

<tr style="height:25px"><td></td></tr>

<tr height="100px"><td colspan="8" valign="top"  class="black left">Informações de Cobertura: <?=$row['comments']?></td></tr>


<!--

<tr><td colspan="8" class="left"><b>Dados do Cliente:</b></td>
<tr><td></td></tr>
<tr><td class="right">Nome:</td><td class="left" colspan="7"><? echo $lead_info['nomecliente']; ?></td></tr>
<tr><td class="right">Contacto 1:</td><td class="left" colspan="3"><? echo $lead_info['contactocliente']; ?></td><td class="right">Email:</td><td class="left" colspan="3"><? echo $lead_info['emailcliente']; ?></td></tr>


<tr><td></td></tr>


<tr><td colspan="8" class="left"><b>Dados da Factura / Firma:</b></td>
<tr><td></td></tr>
<tr><td class="right">Nome:</td><td class="left" colspan="4"><? echo $lead_info['nomefirma']; ?></td><td class="right">Email:</td><td class="left" colspan="2"><? echo $lead_info['emailfirma']; ?></td></tr>
<tr><td class="right">Contacto 1:</td><td class="left" colspan="3"><? echo $lead_info['contactoumfirma']; ?></td><td class="right">Contacto 2:</td><td class="left" colspan="3"><? echo $lead_info['contactodoisfirma']; ?></td></tr>

<tr><td></td></tr>

<tr><td colspan="8" class="left"><b>Morada de Instalação:</b></td>
<tr><td></td></tr>
<tr><td class="right">Av/Rua/Praça:</td><td class="left" colspan="7"><? echo $lead_info['address1']. " " .$lead_info['country_code'] ; ?></td></tr>
<tr><td class="right">Cod. Postal:</td><td class="left" colspan="3"><? echo $lead_info['postal_code']; ?></td><td class="right">Localidade:</td><td class="left" colspan="3"><? echo $lead_info['city']; ?></td></tr>

<tr><td></td></tr>

<tr><td colspan="8" class="left"><b>Outras Informações:</b></td>
<tr><td></td></tr>


<tr><td><b>Consumo</b></td><td class="black">Gás</td><td class="black">Energia</td><td class="black">Água</td><td class="black">Outros</td><td></td><td class="blacker" colspan="2"><b>Produto EcoEnergy</b></td></tr>
<tr><td><b>Mensal</b></td><td class="black"><? echo $lead_info['gas']; ?></td><td class="black"><? echo $lead_info['energia']; ?></td><td class="black"><? echo $lead_info['agua']; ?></td><td class="black"><? echo $lead_info['outros']; ?></td><td></td><td class="blacker"><b>Português</b></td><td class="blacker"><? echo $lead_info['portugues']; ?></tr>
<tr><td><b></b></td><td></td><td></td><td></td><td></td><td></td><td class="blacker"><b>Internacional</b></td><td class="blacker"><? echo $lead_info['internacional']; ?></tr>

<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>

<tr><td class="right" colspan="3">Nome do Titular da Conta Eléctrica:</td><td class="left" colspan="5"><? echo $lead_info['nometitular']; ?></td></tr>
<tr><td class="right">BI:</td><td class="left" colspan="3"><? echo $lead_info['bi']; ?></td><td class="right">NIF:</td><td class="left" colspan="3"><? echo $lead_info['nif']; ?></td></tr>

<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>

<tr><td class="right" colspan="2">Companhia Eléctrica:</td><td class="left" colspan="2"><? echo $lead_info['companhiaelec']; ?></td><td class="right" colspan="2">Potência Contratada:</td><td class="left" colspan="2"><? echo $lead_info['potenciacont']; ?></td></tr>
<tr><td class="right" colspan="2">CPE* (Cod Ponto Entrega):</td><td class="left" colspan="6"><? echo $lead_info['cpe']; ?></td></tr>

<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>

<tr><td class="blacker" colspan="2"><b>Referência</b></td><td class="blacker"><b>Quantidade</b></td><td class="blacker" colspan="3"><b>Designação</b></td><td class="blacker"><b>Valor</b></td><td class="blacker"><b>IVA</b></td></tr>
<tr><td class="black" colspan="2"><b>ECOENER <? echo $lead_info['ecoener']; ?></b></td><td class="black"><? echo $lead_info['ecoenerq']; ?></td><td class="black" colspan="3">Eco Energy <? echo $lead_info['ecoenerd']; ?> Kw</td><td class="black"><? echo $lead_info['ecoenerv']; ?></td><td class="black"></td></tr>
<tr><td class="black" colspan="2"><b>DESL+ADJU.</b></td><td class="black"><? echo $lead_info['desadjq']; ?></td><td class="black" colspan="3">Deslocação + Adjudicação + Demonstração</td><td class="black"><? echo $lead_info['desadjv']; ?></td><td class="black"></td></tr>
<tr><td class="black" colspan="2"><b>INSTAL.</b></td><td class="black"><? echo $lead_info['installq']; ?></td><td class="black" colspan="3">Instalação Técnico Credenciado</td><td class="black"><? echo $lead_info['installv']; ?></td><td class="black"></td></tr>
<tr><td class="black" colspan="2"><b>HPENER <? echo $lead_info['hpener']; ?></b></td><td class="black"><? echo $lead_info['hpenerq']; ?></td><td class="black" colspan="3">HP Energy <? echo $lead_info['hpenerd']; ?> CC</td><td class="black"><? echo $lead_info['hpenerv']; ?></td><td class="black"></td></tr>
<tr><td class="black" colspan="2">&nbsp;</td><td class="black"></td><td class="black" colspan="3"></td><td class="black"></td><td class="black"></td></tr>
<tr><td class="black" colspan="2">&nbsp;</td><td class="black"></td><td class="black" colspan="3"></td><td class="black"></td><td class="black"></td></tr>
<tr><td colspan="2"></td><td></td><td colspan="3"></td><td class="blacker"><b>TOTAL</b></td><td class="blacker"></td></tr>

<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>

<tr><td class="right" colspan="1">A quantia de:</td><td class="left" colspan="6">___________________________________________________________________________________________</td></tr>


<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>

<tr><td class="blacker" colspan="8"><b>Forma de Pagamento</b></td></tr>
<tr><td class="black"><b>Cheque</b></td><td class="black left" colspan="1"><b>S( <? echo $cheque; ?>  ) N(  )</b></td><td class="black left" colspan="4"><b>Nº: <? echo $numero; ?></b></td><td class="black"><b>Dinheiro</b></td><td class="black" colspan="1"><b>S( <? echo $dinheiro; ?>  ) N(  )</b></td></tr>
<tr><td class="black"><b>Crédito</b></td><td class="black left" colspan="1"><b>S( <? echo $credito; ?>  ) N(  )</b></td><td class="black left" colspan="2"><b>Nº Prest.: <? echo $vprest; ?></b></td><td class="black left" colspan="2"><b>V. Prest.: <? echo $numprest; ?></b></td><td class="black"><b>Multibanco</b></td><td class="black" colspan="1"><b>S( <? echo $multibanco; ?> ) N(  )</b></td></tr>
<tr><td class="black"><b>Cartao Crédito</b></td><td class="black left"><b>S( <? echo $cartaocredito; ?> ) N(  )</b></td><td class="black left" colspan="4"><b>Nº: <? echo $numero; ?></b></td><td class="black"><b>Outros</b></td><td class="black" colspan="1"><b>S( <? echo $outros; ?> ) N(  )</b></td></tr>

<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>

</table>
<table class="cc-mstyle full-table">
<tr><td class="left" colspan="8"><b>Observações</b></td></tr>
<tr height="100px"><td colspan="8" class="blacker"><? echo $lead_info['observacoes']; ?></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td class="left" colspan="8"><b>Registo da Venda</b></td></tr>
<tr><td class="right">Hora:</td><td class="left" colspan="1"><? echo $lead_info['hora']; ?></td><td class="right">Dia:</td><td class="left" colspan="2"><? echo $lead_info['data'] ?></td><td class="right">Comercial:</td><td class="left" colspan="2"><? echo $lead_info['comercial']; ?></td></tr>
<tr><td class="right">OBS.:</td><td class="left" colspan="7"><? echo $lead_info['obsmarc']; ?></td></tr>

<tr><td></td></tr>
<tr><td></td></tr>

</table>


<table   class="cc-mstyle full-table">
<tr></tr>
<tr height="100px"><td valign="top" colspan="8" class="blacker">


<table class="cc-mstyle" style="width:100%; border-radius:0px; table-layout:fixed; border-collapse:collapse;">
<tr><td class="right"><b>Hora:</b></td><td class="left" colspan="2">___________________</td><td class="right"><b>Dia:</b></td><td class="left" colspan="2">___________________</td><td class="right"><b>Coordenador:</b></td><td class="left" colspan="2">___________________</td></tr>
<tr><td class="right"></td><td class="left" colspan="2"></td><td class="right"></td><td class="left" colspan="2"></td><td class="right"><b>Processo Nº</b></td><td class="left" colspan="2">___________________</td></tr>

<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>

<tr><td class="left"><b>ESTAFETA</b></td></tr>

<tr><td class="left"></td><td class="left"><b>DIA</b></td><td class="left"><? echo $lead_info['estafetadiadois']; ?></td><td class="left"><b>LOCAL</b></td><td colspan="3" class="left"><? echo $lead_info['estafetalocal']; ?></td><td class="left"><b>HORA</b></td><td class="left"><? echo $lead_info['estafetahoradois']; ?></td></tr>
<tr><td class="right" colspan="2">Pontos de Referência:</td><td class="left" colspan="7"><? echo $lead_info['estafetapontos']; ?></td></tr>


</table>




</td></tr> -->
</table>

</body>
</html>