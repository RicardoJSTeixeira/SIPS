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
			agent_pause_codes_active,
			pause_after_each_call,
			scheduled_callbacks,
			scheduled_callbacks_count,
			campaign_id
		FROM
			vicidial_campaigns
		WHERE
			campaign_id='$campaign_id'
		";
$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
$aCampInfo = mysql_fetch_assoc($rQuery);
?>
<style>
.div-title { width:100%; border-bottom: 1px solid #DDDDDD; font-weight:bold; margin:8px 0 10px 0; }
.td-text { text-align: right; padding:0 6px 0 0; }
.td-text-justify { text-align: justify; padding:0 12px 0 6px; vertical-align:top; }
.td-icon { width: 15px; }
.td-image-help { margin-top:0px; cursor:pointer; }
.td-image-edit { margin-top:0px; cursor:pointer; }
.spacer16 { height: 16px; }
.spacer8 { height: 8px; }

.div-radio-wrapper { float:right; margin-right:3px; } 

.dataTables_paginate { margin-right:2px; }
.dataTables_filter { margin: 0 0 3px 0; }
.dataTables_filter input { height:20px; border:1px solid #DDDDDD; -moz-border-radius-topleft: 2px; -webkit-border-top-left-radius: 2px; -khtml-border-top-left-radius: 2px; border-top-left-radius: 2px; }


.td-half-left { text-align: justify; padding:0 16px 0 16px; width:50%; vertical-align:top; border-right: 1px solid #DDDDDD; }
.td-half-right { text-align: justify; padding:0 16px 0 16px; width:50%; vertical-align:top; }

.dt-fixed-12lines {min-height:290px;}
.dt-fixed-10lines {min-height:250px;}
.dt-fixed-8lines {min-height:210px;}

.dt-div-wrapper-10lines { min-height:285px; }
.dt-div-wrapper-8lines { min-height:235px; }
.dt-div-wrapper-6lines { min-height:185px; }

</style>




<table>
<tr>
<td class="td-half-left"> 

<div class="div-title">Procura de Contactos</div>
<table>
<tr>
<td>Procura de Contactos</td> <td class="td-text"><i>opção inactiva</i></td> <td class="td-icon"> <img class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
<tr>
<td>Método de Procura</td> <td class="td-text"><i>opção inactiva</i></td> <td class="td-icon"> <img class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
</table>

<div class="div-title">Pausas</div>
<table>
<tr>
<td>Pausas dos Operadores</td> <td id="td-campaign-agentpausecodes" class="td-text"><?php if($aCampInfo['agent_pause_codes_active']=="N") { echo "Inactivas"; } else { echo "Activas"; } ?></td> <td class="td-icon"> <img id="icon-campaign-agentpausecodes" class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
<tr>
<td>Pausa Depois de Cada Chamada</td> <td id="td-campaign-pauseaftercall" class="td-text"><?php if($aCampInfo["pause_after_each_call"]=="Y"){ echo "Sim"; } else { echo "Não"; } ?></td> <td class="td-icon"> <img id="icon-campaign-pauseaftercall" class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
</table>

</td>
<td class="td-half-right"> 

<div class="div-title">Callbacks</div>
<table>
<tr>
<td>Callbacks</td> <td id="td-campaign-cbactive" class="td-text"><?php if($aCampInfo['scheduled_callbacks']=="Y") { echo "Activos"; } else { echo "Inactivos"; } ?></td> <td class="td-icon"> <img id="icon-campaign-cbactive" class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
<tr>
<td>Alerta dos Callbacks</td> <td class="td-text"><i>opção inactiva</i></td> <td class="td-icon"> <img class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
<tr>
<td>Contagem de Callbacks</td> <td id="td-campaign-callbacks-count" class="td-text"><?php if($aCampInfo['scheduled_callbacks_count']=="LIVE") { echo "Apenas Activos"; } else { echo "Todos"; } ?></td> <td class="td-icon"> <img id="icon-campaign-callbacks-count" class="td-image-help" src="/images/icons/document_prepare_15.png"> </td>
</tr>
</table>


</td>
</tr>
</table>


<!-- 
	DIALOG ITEM:	"Codigos de Pausa Activos/Inactivos"
-->
<div id="dialog-campaign-agentpausecodes">
        <div class="div-title">Informação</div>
            <table>
                <tr>
                <td class="td-text-justify"> Esta opção permite escolher se os Operadores têm de escolher um motivo de pausa quando se colocam em pausa. <br><br> Se a opção está activa os Operadores têm sempre de escolher o motivo pelo qual estão em pausa, o que permite fazer uma contabilização dos tempos de pausas dos Operadores.  <br><br> Se a opção estiver inactiva os operadores não têm de escolher um motivo quando se colocam em pausa, deixando de haver uma contabilização estatística das pausas dos Operadores. </td>
                </tr>
                <tr class="spacer16"></tr>
            </table>
        <div class="div-title">Alterar Opção</div>
        <form>
            <table>
                <tr>
                <td> Pausas dos Operadores: </td><td>
                <div class="div-radio-wrapper" id="radio-campaign-agentpausecodes">
               
                <input type="radio" id="radio-campaign-agentpausecodes-yes" name="name-radio-campaign-agentpausecodes" /><label id="label-campaign-agentpausecodes-yes" class="smaller" for="radio-campaign-agentpausecodes-yes">Activas</label>
                <input type="radio" id="radio-campaign-agentpausecodes-no" name="name-radio-campaign-agentpausecodes" /><label id="label-campaign-agentpausecodes-no" class="smaller" for="radio-campaign-agentpausecodes-no">Inactivas</label>
              
                </div>
                </td>
                </tr>
            </table>   
        </form> 
        
</div>

<!-- 
	DIALOG ITEM:	"Pausa Depois de cada Chamada"
-->
<div id="dialog-campaign-pauseaftercall">
        <div class="div-title">Informação</div>
            <table>
                <tr>
                <td class="td-text-justify"> Esta opção permite definir se os Operadores são automáticamente colocados em pausa no fim de cada Chamada. </td>
                </tr>
                <tr class="spacer16"></tr>
            </table>
        <div class="div-title">Alterar Opção</div>
        <form>
            <table>
                <tr>
                <td> Pausas Automáticas: </td><td>
                <div class="div-radio-wrapper" id="radio-campaign-pauseaftercall">
               
                <input type="radio" id="radio-campaign-pauseaftercall-yes" name="name-radio-campaign-pauseaftercall" /><label id="label-campaign-pauseaftercall-yes" class="smaller" for="radio-campaign-pauseaftercall-yes">Sim</label>
                <input type="radio" id="radio-campaign-pauseaftercall-no" name="name-radio-campaign-pauseaftercall" /><label id="label-campaign-pauseaftercall-no" class="smaller" for="radio-campaign-pauseaftercall-no">Não</label>
              
                </div>
                </td>
                </tr>
            </table>   
        </form> 
        
</div>

<!-- 
	DIALOG ITEM:	"Callbacks"
-->
<div id="dialog-campaign-cbactive">
        <div class="div-title">Informação</div>
            <table>
                <tr>
                <td class="td-text-justify"> Esta opção permite escolher se os Operadores podem ou não marcar Callbacks no fim das Chamadas. </td>
                </tr>
                <tr class="spacer16"></tr>
            </table>
        <div class="div-title">Alterar Opção</div>
        <form>
            <table>
                <tr>
                <td> Callbacks: </td><td>
                <div class="div-radio-wrapper" id="radio-campaign-cbactive">
               
                <input type="radio" id="radio-campaign-cbactive-yes" name="name-radio-campaign-cbactive" /><label id="label-campaign-cbactive-yes" class="smaller" for="radio-campaign-cbactive-yes">Activo</label>
                <input type="radio" id="radio-campaign-cbactive-no" name="name-radio-campaign-cbactive" /><label id="label-campaign-cbactive-no" class="smaller" for="radio-campaign-cbactive-no">Inactivo</label>
              
                </div>
                </td>
                </tr>
            </table>   
        </form> 
        
</div>

<!-- 
	DIALOG ITEM:	"Contagem de Callbacks"
-->
<div id="dialog-campaign-callbacks-count">
        <div class="div-title">Informação</div>
            <table>
                <tr>
                <td class="td-text-justify"> Esta opção permite configurar de que forma são contados os Callbacks no Menu de Operador. <br><br> Com a opção "Todos", o Menu de Operador vai apresentar uma contagem de todos os Callbacks que o Operador tenha, estejam eles Activos ou Agendados. <br><br> Com a opção "Apenas Activos" a contagem que vai ser apresentada é a dos Callbacks que estejam prontos.</td>
                </tr>
                <tr class="spacer16"></tr>
            </table>
        <div class="div-title">Alterar Opção</div>
        <form>
            <table>
                <tr>
                <td> Callbacks: </td><td>
                <div class="div-radio-wrapper" id="radio-campaign-callbacks-count">
               
                <input type="radio" id="radio-campaign-callbacks-count-yes" name="name-radio-campaign-callbacks-count" /><label id="label-campaign-callbacks-count-yes" class="smaller" for="radio-campaign-callbacks-count-yes">Todos</label>
                <input type="radio" id="radio-campaign-callbacks-count-no" name="name-radio-campaign-callbacks-count" /><label id="label-campaign-callbacks-count-no" class="smaller" for="radio-campaign-callbacks-count-no">Apenas Activos</label>
              
                </div>
                </td>
                </tr>
            </table>   
        </form> 
        
</div>

<script>
/* VARS  */
var campaign_id = "<?php echo $campaign_id; ?>";
/* 
ITEM:	 	"Códigos de Pausa Activos/Inactivos"
STATUS: 	
UPGRADES: 	
 */
var current_campaign_agentpausecodes = "<?php echo $aCampInfo['agent_pause_codes_active']; ?>";
$("#dialog-campaign-agentpausecodes").dialog({ 
	title: ' <span style="font-size:13px; color:black">Pausas dos Operadores</span> ',
	autoOpen: false,
	height: 400,
	width: 450,
	resizable: false,
	buttons: {
		"OK" : function() {
		if($( "#label-campaign-agentpausecodes-yes" ).hasClass("ui-state-active")){var state="FORCE";} else {var state="N";}
		$.ajax({
			type: "POST",
			url: "_requests.php",
			dataType: "JSON",
			data: {action: "change_campaign_agentpausecodes", sent_campaign_id: campaign_id, sent_state: state },
			error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
			success: function(data, textStatus, jqXHR) { if(state=="FORCE") { $("#td-campaign-agentpausecodes").html("Activas"); current_campaign_agentpausecodes = "FORCE"; } else { $("#td-campaign-agentpausecodes").html("Inactivas"); current_campaign_agentpausecodes = "N"; } UpdateTableLog(data); }
		});	
		$(this).dialog("close"); 
		},
		"Cancelar" : function() { $(this).dialog("close"); }
	},
	open: function(){ $("#dialog-campaign-agentpausecodes input").blur(); $("#dialog-campaign-agentpausecodes label").removeClass("ui-state-active"); if(current_campaign_agentpausecodes=="FORCE"){ $("#label-campaign-agentpausecodes-yes").addClass("ui-state-active");  } else { $("#label-campaign-agentpausecodes-no").addClass("ui-state-active");  } }
});
$('#icon-campaign-agentpausecodes').on("click", function() { $("#dialog-campaign-agentpausecodes").dialog( "open" ); });
$("#radio-campaign-agentpausecodes").buttonset();
 
 
 /* 
ITEM:	 	"Pausa Automatica a seguir a cada Chamada"
STATUS: 	
UPGRADES: 	
 */
var current_campaign_pauseaftercall = "<?php echo $aCampInfo['pause_after_each_call']; ?>";
$("#dialog-campaign-pauseaftercall").dialog({ 
	title: ' <span style="font-size:13px; color:black">Pausas dos Operadores</span> ',
	autoOpen: false,
	height: 300,
	width: 450,
	resizable: false,
	buttons: {
		"OK" : function() {
		if($( "#label-campaign-pauseaftercall-yes" ).hasClass("ui-state-active")){var state="Y";} else {var state="N";}
		$.ajax({
			type: "POST",
			url: "_requests.php",
			dataType: "JSON",
			data: {action: "change_campaign_pauseaftercall", sent_campaign_id: campaign_id, sent_state: state },
			error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
			success: function(data, textStatus, jqXHR) { if(state=="Y") { $("#td-campaign-pauseaftercall").html("Sim"); current_campaign_pauseaftercall = "Y"; } else { $("#td-campaign-pauseaftercall").html("Não"); current_campaign_pauseaftercall = "N"; } UpdateTableLog(data); }
		});	
		$(this).dialog("close"); 
		},
		"Cancelar" : function() { $(this).dialog("close"); }
	},
	open: function(){ $("#dialog-campaign-pauseaftercall input").blur(); $("#dialog-campaign-pauseaftercall label").removeClass("ui-state-active"); if(current_campaign_pauseaftercall=="Y"){ $("#label-campaign-pauseaftercall-yes").addClass("ui-state-active");  } else { $("#label-campaign-pauseaftercall-no").addClass("ui-state-active");  } }
});
$('#icon-campaign-pauseaftercall').click(function() { $("#dialog-campaign-pauseaftercall").dialog( "open" ); });
$("#radio-campaign-pauseaftercall").buttonset();
 
 
 /* 
ITEM:	 	"Callbacks"
STATUS: 	
UPGRADES: 	
 */
var current_campaign_callbacks = "<?php echo $aCampInfo['scheduled_callbacks']; ?>";
$("#dialog-campaign-cbactive").dialog({ 
	title: ' <span style="font-size:13px; color:black">Callbacks</span> ',
	autoOpen: false,
	height: 300,
	width: 450,
	resizable: false,
	buttons: {
		"OK" : function() {
		if($( "#label-campaign-cbactive-yes" ).hasClass("ui-state-active")){var state="Y";} else {var state="N";}
		$.ajax({
			type: "POST",
			url: "_requests.php",
			dataType: "JSON",
			data: {action: "change_campaign_callbacks_active", sent_campaign_id: campaign_id, sent_state: state },
			error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
			success: function(data, textStatus, jqXHR) { if(state=="Y") { $("#td-campaign-cbactive").html("Activos"); current_campaign_callbacks = "Y"; } else { $("#td-campaign-cbactive").html("Inactivos"); current_campaign_callbacks = "N"; } UpdateTableLog(data); }
		});	
		$(this).dialog("close"); 
		},
		"Cancelar" : function() { $(this).dialog("close"); }
	},
	open: function(){ $("#dialog-campaign-cbactive input").blur(); $("#dialog-campaign-cbactive label").removeClass("ui-state-active"); if(current_campaign_callbacks=="Y"){ $("#label-campaign-cbactive-yes").addClass("ui-state-active");  } else { $("#label-campaign-cbactive-no").addClass("ui-state-active");  } }
});
$("#icon-campaign-cbactive").click( function(){$("#dialog-campaign-cbactive").dialog( "open" ); });
$("#radio-campaign-cbactive").buttonset();
 
 /* 
ITEM:	 	"Contagem de Callbakcs"
STATUS: 	
UPGRADES: 	
 */
var current_campaign_callbacks_count = "<?php echo $aCampInfo['scheduled_callbacks_count']; ?>";
$("#dialog-campaign-callbacks-count").dialog({ 
	title: ' <span style="font-size:13px; color:black">Contagem de Callbacks</span> ',
	autoOpen: false,
	height: 400,
	width: 450,
	resizable: false,
	buttons: {
		"OK" : function() {
		if($( "#label-campaign-callbacks-count-yes" ).hasClass("ui-state-active")){var state="ALL_ACTIVE";} else {var state="LIVE";}
		$.ajax({
			type: "POST",
			url: "_requests.php",
			dataType: "JSON",
			data: {action: "change_campaign_callbacks_count", sent_campaign_id: campaign_id, sent_state: state },
			error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
			success: function(data, textStatus, jqXHR) { if(state=="ALL_ACTIVE") { $("#td-campaign-callbacks-count").html("Todos"); current_campaign_callbacks_count = "ALL_ACTIVE"; } else { $("#td-campaign-callbacks-count").html("Apenas Activos"); current_campaign_callbacks_count = "LIVE"; } UpdateTableLog(data); }
		});	
		$(this).dialog("close"); 
		},
		"Cancelar" : function() { $(this).dialog("close"); }
	},
	open: function(){ $("#dialog-campaign-callbacks-count input").blur(); $("#dialog-campaign-callbacks-count label").removeClass("ui-state-active"); if(current_campaign_callbacks_count=="ALL_ACTIVE"){ $("#label-campaign-callbacks-count-yes").addClass("ui-state-active");  } else { $("#label-campaign-callbacks-count-no").addClass("ui-state-active");  } }
});
$("#icon-campaign-callbacks-count").live("click", function(){$("#dialog-campaign-callbacks-count").dialog( "open" ); });
$("#radio-campaign-callbacks-count").buttonset();
</script>
