<!DOCTYPE html>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>SIPS</title>
</head>
<?php
date_default_timezone_set('Europe/Lisbon');
require("../../ini/dbconnect.php");
require("../../ini/functions.php");
$campaign_id=$_GET['campaign_id'];

##########################################
# QUERY - Dados/Opções Gerais da Campanha
##########################################
$sQuery = "
		SELECT
			campaign_id,
			campaign_name, 
			campaign_description,
			DATE_FORMAT(campaign_changedate,'%d-%m-%Y às %H:%i:%s') as campaign_changedate,
			DATE_FORMAT(campaign_logindate,'%d-%m-%Y às %H:%i:%s') as campaign_logindate,
			
			active,
			
			dial_method,
			campaign_allow_inbound,
			
			next_agent_call,
			
			auto_dial_level,
			
			lead_order,
			lead_order_secondary,
			hopper_level,
			use_auto_hopper
		FROM
			vicidial_campaigns
		WHERE
			campaign_id='$campaign_id'
		";
$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
$aCampInfo = mysql_fetch_assoc($rQuery);
?>
<script>
/* VARS  */
var campaign_id = "<?php echo $campaign_id; ?>";
var campaign_name = "<?php echo $aCampInfo['campaign_name']; ?>";
var campaign_description = "<?php echo $aCampInfo['campaign_description']; ?>";



function UpdateLog(data){
	if( $('#table-campaign-changedate tbody').html().length == 0) 
		{
			$('#td-campaign-changedate').html(data.reply[1])
		} 
		else 
		{
			$('#td-campaign-changedate').html(data.reply[1]); 
			$('#table-campaign-changedate').dataTable().fnAddData([  data.reply[0],data.reply[1],data.reply[2],data.reply[3]  ]); 
		}
}
function ButtonRadioVertical(container, button, active_text, inactive_text){
	$("#" + container + " .ui-button-text").html(inactive_text).removeClass("button-radio-vertical-selected");
	$("#" + button + " .ui-button-text").html(active_text).addClass("button-radio-vertical-selected"); 
	$("*").blur();
}



/* ERROR DIALOG */ 
var $error = $('<div></div>')
	.html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ')
	.dialog({
		autoOpen: false,
		title: "<span style='float:left; margin-right: 4px;' class='ui-icon ui-icon-alert'></span> Erro",
		width: "550",
		height: "250",
		show: "fade",
		hide: "fade",
		buttons: { "OK": function(){ $(this).dialog("close"); } }
});

</script>

<style>
.div-title { width:100%; border-bottom: 1px solid #DDDDDD; font-weight:bold; margin:8px 0 10px 0; }
.td-text { text-align: right; padding:0 6px 0 0; }
.td-indent { padding-left:12px; }
.td-text-justify { text-align: justify; padding:0 12px 0 6px; vertical-align:top; }
.td-text-justify-snippet { font-weight:700; }
.td-text-justify-link { color:#0073EA; cursor:pointer; } 
.td-subitem { padding: 0 0 0 12px; }
.td-icon { width: 15px; }
.td-image-help { margin-top:0px; cursor:pointer; }
.td-image-edit { margin-top:0px; cursor:pointer; }
.spacer16 { height: 16px; }
.spacer8 { height: 8px; }
/* button.smaller { float:right; bottom:-7px; right:-1px; } */
button.smaller .ui-button-text {
    line-height:.55em;
}

/* label.smaller .ui-button-text { line-height:.35em !important; } */

.dt-fixed-12lines {min-height:290px;}
.dt-fixed-10lines {min-height:250px;}
.dt-fixed-8lines {min-height:210px;}

.dt-div-wrapper-10lines { min-height:285px; }
.dt-div-wrapper-8lines { min-height:235px; }
.dt-div-wrapper-6lines { min-height:185px; }
.dt-col-center { text-align:center; }

.div-radio-wrapper { float:right; margin-right:3px; } 

.dataTables_paginate { margin-right:2px; }
.dataTables_filter { margin: 0 0 3px 0; }
.dataTables_filter input { height:20px; border:1px solid #DDDDDD; -moz-border-radius-topleft: 2px; -webkit-border-top-left-radius: 2px; -khtml-border-top-left-radius: 2px; border-top-left-radius: 2px; }


.td-half-left { text-align: justify; padding:0 16px 0 16px; width:50%; vertical-align:top; border-right: 1px solid #DDDDDD; }
.td-half-right { text-align: justify; padding:0 16px 0 16px; width:50%; vertical-align:top; }

.input-text-dialog { height:20px; text-align:center; float:right; border:1px solid #DDDDDD; -moz-border-radius-topleft: 2px; -webkit-border-top-left-radius: 2px; -khtml-border-top-left-radius: 2px; border-top-left-radius: 2px; }
.textarea-dialog60 { height:60px; width: 425px; resize:none; }

.button-campaign-nextcall { margin: auto; width:75px; }

.ui-spinner { width: 65px; float:right; }


.button-radio-vertical-selected {border: 1px solid #dddddd; background: #ffffff url(images/ui-bg_glass_65_ffffff_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #ff0084;}
/* div { border:1px solid black; } */


.edit-icon { float:right; margin: 0 0 0 3px; height:16px; width:16px; cursor:pointer; }
.help-icon { float:right; margin: 0 0 0 3px; height:16px; width:16px; cursor:pointer; }
.div-spacer { width:100%; margin: 8px 0 8px 0;}
.td-spacer-16 { height:16px; }
.td-spacer-8 { height:8px; }
.option { width:100%; height:16px; float:left; margin: 0 0 3px 0;}
.option-popup { width:100%; height:20px; float:left; margin: 0 0 3px 0;}
.options-left { float:left; height:100%; }
.options-right { float:right; }
.options-content { margin: 8px 0 0 0; }


</style>


<body>

<table>
<tr>
<td class="td-half-left"> 

<div class="div-title">Opções Gerais</div>
<table>
<tr>
<td>Activa</i></td> <td id="td-campaign-active" class="td-text"><?php if($aCampInfo['active']=="Y"){ echo "Sim"; } else { echo "Não"; } ?></td> <td class="td-icon"> <img id="icon-campaign-active" class="td-image-help" src="/images/icons/document_prepare_15.png"></td>
</tr>
<tr>
<td>Tipo de Campanha</td> <td id="td-campaign-dialmethod" class="td-text"><?php switch($aCampInfo['dial_method']){case "RATIO": echo "Automática"; break; case "MANUAL": echo "Manual"; break; case "INBOUND_MAN": echo "Manual";  } if($aCampInfo['campaign_allow_inbound']=="Y") {echo " com Inbound";} else {echo " sem Inbound"; } ?> </td> <td class="td-icon"> <img id="icon-campaign-dialmethod" class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
<tr class="spacer8"></tr>
<tr>
<td>Atribuição de Chamadas</i></td> <td id="td-campaign-nextcall" class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-nextcall" class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
<tr>
<td>Nível de Marcação</td> <td id="td-campaign-ratio" class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-ratio" class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
<tr>
<td>Campanha <i>Inbound</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Apenas Operadores Disponíveis</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Apenas Operadores Disponíveis</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>No Hopper Leads Login</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Get Call Launch</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Disable Alter Customer Data</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Disable Alter Customer Phone</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Dial Timeout</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Auto alt number Dialing</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Next Agent call</i></td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
<tr>
<td>Available only tally</td> <td class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
</tr>
</table>

</td>
<td class="td-half-right"> 

<div class="div-title">Configurador</div>

</td>
</tr>
</table>



<!-- 
	DIALOG ITEM:	"Campanha Activa/Inactiva"
-->
<div id="dialog-campaign-active">
	        <div class="div-title">Informação</div>
            <table>
            <tr>
            <td class="td-text-justify"> filler  </td>
            </tr>
            <tr class="spacer16"></tr>
            </table>
			<div class="div-title">Alterar o Estado da Campanha</div>
            
            <table>
            <tr>
            <td> Campanha: </td><td>
            <div class="div-radio-wrapper" id="radio-campaign-active">
           
            <input class="radio-campaign-active" type="radio" id="radio-campaign-active-yes" name="radio-campaign-active" <?php if($aCampInfo['active']=="Y"){ echo "checked"; } ?> /><label class="smaller" for="radio-campaign-active-yes">Activa</label>
            <input class="radio-campaign-active" type="radio" id="radio-campaign-active-no" name="radio-campaign-active" <?php if($aCampInfo['active']=="N"){ echo "checked"; } ?> /><label class="smaller" for="radio-campaign-active-no">Inactiva</label>
            
           
            </div>
            </td>
            </tr>
            </table>    

       		</div>

</div>

<!-- 
	DIALOG ITEM:	"Tipo de Campanha"
-->
<div id="dialog-campaign-dialmethod">
	        <div class="div-title">Informação</div>
            <table>
            <tr>
            <td class="td-text-justify"> filler  </td>
            </tr>
            <tr class="spacer16"></tr>
            </table>
			<div class="div-title">Alterar o tipo de Campanha</div>
            
            <table>
            <tr>
            <td> Tipo de Campanha: </td><td>
            <div class="div-radio-wrapper" id="radio-campaign-dialmethod">
           
            <input class="radio-campaign-dialmethod" type="radio" id="radio-campaign-dialmethod-ratio" name="radio-campaign-dialmethod" <?php if($aCampInfo['dial_method']=="RATIO"){ echo "checked"; } ?> /><label for="radio-campaign-dialmethod-ratio">Chamadas Automáticas</label>
            <input class="radio-campaign-dialmethod" type="radio" id="radio-campaign-dialmethod-manual" name="radio-campaign-dialmethod" <?php if(($aCampInfo['dial_method']=="MANUAL") OR ($aCampInfo['dial_method']=="INBOUND_MAN") ){ echo "checked"; } ?> /><label  for="radio-campaign-dialmethod-manual">Chamadas Manuais</label>
            
           
            </div>
            </td>
            </tr>
            <tr class="spacer8"></tr>
            <tr>
            <td> Inbound: </td><td>
            <div class="div-radio-wrapper" id="radio-campaign-inbound">
           
            <input class="radio-campaign-dialmethod" type="radio" id="radio-campaign-inbound-yes" name="radio-campaign-inbound" <?php if($aCampInfo['campaign_allow_inbound']=="Y"){ echo "checked"; } ?> /><label for="radio-campaign-inbound-yes">Activo</label>
            <input class="radio-campaign-dialmethod" type="radio" id="radio-campaign-inbound-no" name="radio-campaign-inbound" <?php if($aCampInfo['campaign_allow_inbound']=="N"){ echo "checked"; } ?> /><label  for="radio-campaign-inbound-no">Inactivo</label>
            
           
            </div>
            </td>
            </tr>
            </table>
            
                

       		</div>

</div>

<!-- 
	DIALOG ITEM:	"Atribuição de Chamadas"
-->
<div id="dialog-campaign-nextcall">
        <div class="div-title">Informação</div>
        <table>
            <tr>
            <td class="td-text-justify"> Quando uma Campanha está configurada para fazer Chamadas Automáticas, apenas as chamadas que tenham sido atendidas são atribuidas aos Operadores. <br><br> O menu de Atribuição de Chamadas permite que a ordem da atribuição de chamadas seja configurada para aumentar a eficiência, adequando melhor o SIPS à realidade do seu negócio. </td>
            </tr>
            <tr class="spacer16"></tr>
		</table>

		<div id="div-campaign-nextcall-radio">

            <div class="div-title">Maior Tempo à Espera</div>
            <table>
            <tr>
            <td class="td-text-justify">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean vulputate ultricies metus a pharetra. Sed eget ipsum lectus. Morbi sagittis lorem vitae dui posuere ultricies. Aliquam sed mi lacus. Nunc sit amet sagittis sem. Curabitur pellentesque purus at libero ultricies a ullamcorper nibh cursus.</td>
            <td><button class="button-campaign-nextcall" id="button-campaign-nextcall-option1">Inactivo</button></td>
            </tr>
            </table>

			<div class="div-title">Menos Chamadas Realizadas</div>
            <table>
            <tr>
           	<td class="td-text-justify">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean vulputate ultricies metus a pharetra. Sed eget ipsum lectus. Morbi sagittis lorem vitae dui posuere ultricies. Aliquam sed mi lacus. Nunc sit amet sagittis sem. Curabitur pellentesque purus at libero ultricies a ullamcorper nibh cursus.</td>
            <td><button class="button-campaign-nextcall" id="button-campaign-nextcall-option2">Inactivo</button></td>
            </tr>
            </table>

			<div class="div-title">Nível do Operador</div>
            <table>
            <tr>
            <td class="td-text-justify">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean vulputate ultricies metus a pharetra. Sed eget ipsum lectus. Morbi sagittis lorem vitae dui posuere ultricies. Aliquam sed mi lacus. Nunc sit amet sagittis sem. Curabitur pellentesque purus at libero ultricies a ullamcorper nibh cursus.</td>
            <td><button class="button-campaign-nextcall" id="button-campaign-nextcall-option3">Inactivo</button></td>
            </tr>
            </table>

            <div class="div-title">Aleatória</div>
            <table>
            <tr>
            <td class="td-text-justify">Esta opção distribui aleatóriamente as chamadas pelos Operadores que estejam prontos para receber chamadas. <br><br> Esta opção é adequada para quando um Administrador quer que todos os Operadores recebam, em média, um número igual de chamadas.</td>
            <td><button class="button-campaign-nextcall" id="button-campaign-nextcall-option4">Inactivo</button></td>
            </tr>
            </table>

		</div>
</div>

<!-- 
	DIALOG ITEM:	"Rácio de Marcação"
-->
<div id="dialog-campaign-ratio">



	        <div class="div-title">Informação</div>
            <table>
            <tr>
            <td class="td-text-justify"> filler  </td>
            </tr>
            <tr class="spacer16"></tr>
            </table>
			<div class="div-title">Alterar o Rácio da Campanha</div>
            
            <table>
            <tr>
            <td> Rácio: </td><td>
            
           
           
    		<input id="spinner-campaign-ratio" name="spinner" value="5" />
           
            
            </td>
            </tr>
            </table>    

       		</div>

</div>


<script>

/* 
ITEM:	 	"Campanha Activa/Inactiva"
 */
 /* $( "#radio-campaign-active" ).buttonset(); */
$("#dialog-campaign-active").dialog({ 
	title: ' <span style="font-size:13px; color:black">Campanha Activa/Inactiva</span> ',
	autoOpen: false,
	height: 300,
	width: 450,
	resizable: false,
	buttons: {
		"Fechar" : function() { $(this).dialog("close"); }
	},
	open: function(){ $("*").blur(); }
});
$('#icon-campaign-active').click(function() {
	$("#dialog-campaign-active").dialog( "open" );
});


$( ".radio-campaign-active" ).change(function(){
	if($( "#radio-campaign-active-yes" ).prop("checked")){var state="Y";} else {var state="N";}
	
	$.ajax({
	type: "POST",
	dataType: "JSON",
	url: "_requests.php",
	data: {action: "change_campaign_active", sent_campaign_id: campaign_id, sent_state: state },
	error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
	success: function(data, textStatus, jqXHR) { if(state=="Y") { $("#td-campaign-active").html("Sim"); } else { $("#td-campaign-active").html("Não"); }  UpdateLog(data);  }
	});
});

/* 
ITEM:	 	"Tipo de Campanha e Inbound"
 */
$("#dialog-campaign-dialmethod").dialog({ 
	title: ' <span style="font-size:13px; color:black">Campanha Activa/Inactiva</span> ',
	autoOpen: false,
	height: 300,
	width: 450,
	resizable: false,
	buttons: {
		"Fechar" : function() { $(this).dialog("close"); }
	},
	open: function(){ $("*").blur(); }
});
$('#icon-campaign-dialmethod').click(function() {
	$("#dialog-campaign-dialmethod").dialog( "open" );
});
/*
$( "#radio-campaign-inbound" ).buttonset();
$( "#radio-campaign-dialmethod" ).buttonset();
*/
	
$( ".radio-campaign-dialmethod" ).change(function(){
	var dial;
	var inbound;
	if($( "#radio-campaign-dialmethod-ratio" ).prop("checked")){ dial="RATIO"; } else { if($( "#radio-campaign-inbound-yes" ).prop("checked")){ dial="INBOUND_MAN"; } else { dial="MANUAL"; }}
	if($( "#radio-campaign-inbound-yes" ).prop("checked")){var inbound="Y";} else {var inbound="N";}
	$.ajax({
	type: "POST",
	dataType: "JSON",
	url: "_requests.php",
	data: {action: "change-campaign-dialmethod", sent_campaign_id: campaign_id, sent_dial: dial, sent_inbound: inbound },
	error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
	success: function(data, textStatus, jqXHR) { if(dial=="RATIO"){ dial="Automática"; } else { dial="Manual"; } if(inbound=="Y"){ inbound= " com Inbound";} else {inbound=" sem Inbound";} $("#td-campaign-dialmethod").html(dial + inbound); UpdateLog(data);  }
	});
});

/* 
ITEM:	 	"Atribuição de Chamadas"
 */
$("#dialog-campaign-nextcall").dialog({ 
	title: ' <span style="font-size:13px; color:black">Atribuição de Chamadas</span> ',
	autoOpen: false,
	height: 600,
	width: 550,
	modal: true,
	resizable: false,
	buttons: {
		"Fechar" : function() { $(this).dialog("close"); }
	},
	open: function(){ $("*").blur(); }
});
$('#icon-campaign-nextcall').click(function() {
	$("#dialog-campaign-nextcall").dialog( "open" );
});

var loaded_state= "<? echo $aCampInfo['next_agent_call']; ?>";


$("#button-campaign-nextcall-option1").button().click(function(){ ButtonRadioVertical("div-campaign-nextcall-radio",  "button-campaign-nextcall-option1",  "Activo", "Inactivo" );  });
$("#button-campaign-nextcall-option2").button().click(function(){ ButtonRadioVertical("div-campaign-nextcall-radio",  "button-campaign-nextcall-option2",  "Activo", "Inactivo" );  });
$("#button-campaign-nextcall-option3").button().click(function(){ ButtonRadioVertical("div-campaign-nextcall-radio",  "button-campaign-nextcall-option3",  "Activo", "Inactivo" );  });;
$("#button-campaign-nextcall-option4").button().click(function(){ ButtonRadioVertical("div-campaign-nextcall-radio",  "button-campaign-nextcall-option4",  "Activo", "Inactivo" );  });;

switch(loaded_state)
{
	case "longest_wait_time": $("#button-campaign-nextcall-option1 .ui-button-text").addClass("button-radio-vertical-selected").html("Activo"); $("#td-campaign-nextcall").html("Maior Tempo à Espera"); break;
	case "fewest_calls": $("#button-campaign-nextcall-option2 .ui-button-text").addClass("button-radio-vertical-selected").html("Activo"); $("#td-campaign-nextcall").html("Menos Chamadas Realizadas"); break;
	case "campaign_rank": $("#button-campaign-nextcall-option3 .ui-button-text").addClass("button-radio-vertical-selected").html("Activo"); $("#td-campaign-nextcall").html("Nível do Operador"); break;
	case "random": $("#button-campaign-nextcall-option4 .ui-button-text").addClass("button-radio-vertical-selected").html("Activo"); $("#td-campaign-nextcall").html("Aleatória"); break;
}
$( ".button-campaign-nextcall" ).click(function(){
	var nextcall;
	var nextcall_text;
	if($("#button-campaign-nextcall-option1 span").html()=="Activo"){ nextcall = "longest_wait_time"; nextcall_text = "Maior Tempo à Espera"; }
	if($("#button-campaign-nextcall-option2 span").html()=="Activo"){ nextcall = "fewest_calls"; nextcall_text = "Menos Chamadas Realizadas"; }
	if($("#button-campaign-nextcall-option3 span").html()=="Activo"){ nextcall = "campaign_rank"; nextcall_text = "Nível do Operador"; }
	if($("#button-campaign-nextcall-option4 span").html()=="Activo"){ nextcall = "random"; nextcall_text = "Aleatória"; } 
	
	$.ajax({
	type: "POST",
	dataType: "JSON",
	url: "_requests.php",
	data: {action: "change-campaign-nextcall", sent_campaign_id: campaign_id, sent_nextcall: nextcall },
	error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
	success: function(data, textStatus, jqXHR) { $("#td-campaign-nextcall").html(nextcall_text); UpdateLog(data);  }
	}); 
});

/* 
ITEM:	 	"Racio da Marcação"
 */
$("#td-campaign-ratio").html("<?php echo $aCampInfo['auto_dial_level']; ?>");
var spinner_campaign_ratio = $( "#spinner-campaign-ratio" ).spinner({ min:0, max:5 });
spinner_campaign_ratio.spinner("value", <?php echo $aCampInfo['auto_dial_level']; ?>);  

$('#icon-campaign-ratio').click(function(){ $("#dialog-campaign-ratio").dialog( "open" ); });
$("#dialog-campaign-ratio").dialog({ 
	title: ' <span style="font-size:13px; color:black">Descrição da Campanha</span> ',
	autoOpen: false,
	height: 425,
	width: 450,
	resizable: false,
	buttons: { 
		"OK" : function() { 
		var new_ratio = spinner_campaign_ratio.spinner("value");
		$.ajax({
		type: "POST",
		dataType: "JSON",
		url: "_requests.php",
		data: {action: "change-campaign-ratio", sent_campaign_id: campaign_id, sent_ratio: new_ratio },
		error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
		success: function(data, textStatus, jqXHR) { $("#td-campaign-ratio").html(new_ratio); UpdateLog(data);  }
		}); 
		
		
		$(this).dialog("close"); },
		"Cancelar" : function() { $(this).dialog("close"); }
		},
	open: function(){ $("*").blur(); }
});


  











</script>
</body>
</html>