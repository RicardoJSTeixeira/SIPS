<?php

date_default_timezone_set('Europe/Lisbon');
require("../../ini/dbconnect.php");
require("../../ini/functions.php");
$campaign_id=$_GET['campaign_id'];
##########################################
# INI - Todas as Listas da Campanha 
##########################################
$sQuery = "SELECT list_id FROM vicidial_lists WHERE	campaign_id='$campaign_id'";
$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
$nTotalDBs = mysql_num_rows($rQuery);
$inAllLists = Query2IN($rQuery, 0);
##########################################
# INI - Listas Activas da Campanha 
##########################################
$sQuery = "SELECT list_id FROM vicidial_lists WHERE	campaign_id='$campaign_id' AND active='Y'";
$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
$nActiveDBs = mysql_num_rows($rQuery);
$inAllActiveLists = Query2IN($rQuery, 0);
##########################################
# INI - Listas Inactivas da Campanha 
##########################################
$sQuery = "SELECT list_id FROM vicidial_lists WHERE	campaign_id='$campaign_id' AND active='N'";
$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
$nInactiveDBs = mysql_num_rows($rQuery);
$inAllInactiveLists = Query2IN($rQuery, 0);
##########################################
# QUERY - Dados/Opções Gerais da Campanha
##########################################
$sQuery = "
		SELECT
			campaign_id,
			campaign_name, 
			campaign_description,
			DATE_FORMAT(campaign_logindate,'%d-%m-%Y às %H:%i:%s') as campaign_logindate
		FROM
			vicidial_campaigns
		WHERE
			campaign_id='$campaign_id'
		";
$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
$aCampInfo = mysql_fetch_assoc($rQuery);

	$sQuery = "SELECT DATE_FORMAT(event_date,'%d-%m-%Y às %H:%i:%s') as data FROM vicidial_admin_log WHERE record_id='$campaign_id' ORDER BY event_date DESC LIMIT 1";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	$rQuery = mysql_fetch_row($rQuery);
	$nLastChange = $rQuery[0];

?>


<table>
<tr>
<td class="td-half-left"> 

    <div class="div-title">Identificação</div>
        <table>
            <tr>
            <td> ID da Campanha </td> <td class="td-text"> <?php echo $aCampInfo['campaign_id']; ?> </td> <td class="td-icon"> <img id="icon-campaign-id" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr>
            <td> Nome da Campanha </td> <td id="td-campaign-name" class="td-text"> <?php echo $aCampInfo['campaign_name']; ?> </td> <td class="td-icon"> <img id="icon-campaign-name" class="td-image-edit" src="/images/icons/document_prepare_15.png"> </td>
            </tr>
            <tr>
            <td> Descrição da Campanha </td> <td id="td-campaign-description" class="td-text"><?php $temp_camp_description = $aCampInfo['campaign_description']; if($temp_camp_description=='') { echo "<i>Sem Descrição</i>"; } else { if(strlen($temp_camp_description) > 20) { while (strlen($temp_camp_description) > 20) { $temp_camp_description = substr($temp_camp_description, 0, -1); }; echo $temp_camp_description."..."; } else { echo $temp_camp_description; } } ?> </td> <td class="td-icon"> <img id="icon-campaign-description" class="td-image-edit" src="/images/icons/document_prepare_15.png"> </td>
            </tr>
            <tr class="spacer16"></tr>
        </table>
    
    <div class="div-title">Eventos</div>
        <table>
            <tr>
            <td> Última Alteração </td> <td id="td-campaign-changedate" class="td-text"><?php echo $nLastChange; ?> </td> <td class="td-icon"> <img id="icon-campaign-changedate" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr>
            <td> Último Log In </td> <td class="td-text"><?php if($aCampInfo['campaign_logindate']=='') { echo "<i>Sem Data</i>"; } else { echo $aCampInfo['campaign_logindate']; } ?> </td> <td class="td-icon"> <img id="icon-campaign-logindate" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr>
            <td> Última Chamada </td> <td id="td-campaign-calldate" class="td-text"></td> <td class="td-icon"> <img id="icon-campaign-calldate" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr class="spacer16"></tr>
        </table>

</td>
<td class="td-half-right"> 

    <div class="div-title">Bases de Dados</div>
        <table>
            <tr>
            <td>Nº de Bases de Dados </td> <td class="td-text"><?php echo $nTotalDBs; ?> </td> <td class="td-icon"> <img id="icon-campaign-dbs" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr>
            <td>- Activas </td> <td id="td-active-lists" class="td-text"><?php echo $nActiveDBs; ?> </td> <td class="td-icon"> <img class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr>
            <td>- Inactivas </td> <td id="td-inactive-lists" class="td-text"><?php echo $nInactiveDBs; ?> </td> <td class="td-icon"> <img class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr class="spacer16"></tr>
        </table>


    <div class="div-title">Contactos</div>
        <table>
            <tr>
            <td>Nº de Contactos</td> <td id="td-campaign-leads" class="td-text loading"></td> <td class="td-icon"> <img id="icon-campaign-leads" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr class="spacer8"></tr>
            <tr>
            <td>Contactos no <i>Dialer</i></td> <td id="td-campaign-dialer" class="td-text loading"></td> <td class="td-icon"> <img id="icon-campaign-dialer" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
            <tr>
            <td>Contactos em Reciclagem </td> <td id="td-campaign-recycle" class="td-text loading"></td> <td class="td-icon"> <img id="icon-campaign-recycle" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
	        <tr>
            <td>Nº de Callbacks</td> <td id="td-campaign-callback" class="td-text loading"></td> <td class="td-icon"> <img id="icon-campaign-callbacks" class="td-image-help" src="/images/icons/document_properties_16.png"> </td>
            </tr>
        <tr class="spacer16"></tr>
    </table>

</td>
</tr>
</table>


<!-- 
	DIALOG ITEM:	"ID da Campanha"
-->
<div id="dialog-campaign-id">
        <div class="div-title">Informação</div>
        <table>
        <tr>
        <td class="td-text-justify"> O ID da Campanha é um identificador único para a Campanha em todo o SIPS. Para um Administrador é um valor que não requer nenhuma atenção, pois este valor é gerado automáticamente pelo Configurador de Campanhas. <br><br> Este código pode ajudar a identificar esta Campanha quando solicitar uma assistencia por telefone ou quando submeter um Ticket de Assistência.</td>
        </tr>
        <tr class="spacer16"></tr>
        </table>
</div>

<!-- 
	DIALOG ITEM:	"Nome da Campanha"
-->
<div id="dialog-campaign-name">
		<div class="div-title">Informação</div>
        <table>
        <tr>
        <td class="td-text-justify">As campanhas são identificadas pelos Administradores e Operadores por este nome. O nome da Campanha pode ser alterado neste menu e a sua alteração reflecte-se em todo o software. <br><br> Uma correcta tipificação dos nomes das Campanhas ajuda na diferenciação e organização de Administradores e Operadores, em casos como a visualização de relatórios ou a escolha da Campanha no Log In dos Operadores. <br><br>  O nome da Campanha tem de conter entre 2 a 16 caracteres e não pode conter caracteres especiais.</td>
        </tr>
        <tr class="spacer8"></tr>
        </table>
        <div class="div-title">Modificar o Nome da Campanha</div>
        <table>
        <tr>
        <td>Novo Nome para a Campanha:</td>
        <td><input type="text" id="edit-campaign-name" value="<?php echo $aCampInfo['campaign_name']; ?>" class="input-text-dialog"></td>
        </tr>
        </table>
        <br>
</div>

<!-- 
	DIALOG ITEM:	"Descrição da Campanha"
-->
<div id="dialog-campaign-description">
        <div class="div-title">Informação</div>
        <table>
        <tr>
        <td class="td-text-justify">Lorem ipsum dolor sit amet, eu has quem impetus, est ex prima blandit suavitate. Ne eam propriae nominavi intellegam, vidit vitae cum ne, pri no illud nominati. Eu soluta lucilius prodesset eum, ne qui appetere accusamus inciderint. No sed quot detraxit, qui ea movet meliore fierent. Postea suscipiantur vis et. <br><br> Pri stet conclusionemque ut. Sit utroque nostrum ne, vide atqui quaerendum no mel. Sit inani vituperata instructior id. His eu harum nonumes, ullum oratio duo ad, id alienum detracto moderatius sea. Mea prompta fierent quaestio te.</td>
        </tr>
        <tr class="spacer8"></tr>
        </tr>
        </table>
        <div class="div-title">Modificiar a Descrição da Campanha</div>
        <table>
        <tr class="spacer8"></tr>
        <tr>
        <td>Descrição</td>
        </tr>
        <tr>
        <td><textarea id="new-campaign-description" class="ui-widget-content ui-corner-all textarea-dialog60"><?php  /* while (strlen($aCampInfo['campaign_description']) > 20) { $aCampInfo['campaign_description'] = substr($aCampInfo['campaign_description'], 0, -1); } */ echo $aCampInfo['campaign_description']; ?></textarea>
        </tr>
        </table>

</div>

<!-- 
	DIALOG ITEM:	"Última Alteração"
-->
<div id="dialog-campaign-changedate">
        <div class="div-title">Informação</div>
        <table>
        <tr>
        <td class="td-text-justify">Lorem ipsum dolor sit amet, eu has quem impetus, est ex prima blandit suavitate. Ne eam propriae nominavi intellegam, vidit vitae cum ne, pri no illud nominati. Eu soluta lucilius prodesset eum, ne qui appetere accusamus inciderint. No sed quot detraxit, qui ea movet meliore fierent. Postea suscipiantur vis et. <br><br> Pri stet conclusionemque ut. Sit utroque nostrum ne, vide atqui quaerendum no mel. Sit inani vituperata instructior id. His eu harum nonumes, ullum oratio duo ad, id alienum detracto moderatius sea. Mea prompta fierent quaestio te.</td>
        </tr>
        <tr class="spacer8"></tr>
        </tr>
        </table>
        <div class="div-spacer"></div>
        <div class="dt-div-wrapper-8lines">
        	<table id='table-campaign-changedate'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>
        </div>
</div>

<!-- 
	DIALOG ITEM:	"Último LogIn"
-->
<div id="dialog-campaign-logindate">
        <div class="div-title">Informação</div>
        <table>
        <tr>
        <td class="td-text-justify">Lorem ipsum dolor sit amet, eu has quem impetus, est ex prima blandit suavitate. Ne eam propriae nominavi intellegam, vidit vitae cum ne, pri no illud nominati. Eu soluta lucilius prodesset eum, ne qui appetere accusamus inciderint. No sed quot detraxit, qui ea movet meliore fierent. Postea suscipiantur vis et. <br><br> Pri stet conclusionemque ut. Sit utroque nostrum ne, vide atqui quaerendum no mel. Sit inani vituperata instructior id. His eu harum nonumes, ullum oratio duo ad, id alienum detracto moderatius sea. Mea prompta fierent quaestio te.</td>
        </tr>
        <tr class="spacer8"></tr>
        </tr>
        </table>
        <div class="div-spacer"></div>
        <div class="dt-div-wrapper-8lines">
        	<table id='table-campaign-logindate'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>
        </div>
</div>

<!-- 
	DIALOG ITEM:	"Última Chamada"
-->
<div id="dialog-campaign-calldate">
        <div class="div-title">Informação</div>
        <table>
        <tr>
        <td class="td-text-justify">Lorem ipsum dolor sit amet, eu has quem impetus, est ex prima blandit suavitate. Ne eam propriae nominavi intellegam, vidit vitae cum ne, pri no illud nominati. Eu soluta lucilius prodesset eum, ne qui appetere accusamus inciderint. No sed quot detraxit, qui ea movet meliore fierent. Postea suscipiantur vis et. <br><br> Pri stet conclusionemque ut. Sit utroque nostrum ne, vide atqui quaerendum no mel. Sit inani vituperata instructior id. His eu harum nonumes, ullum oratio duo ad, id alienum detracto moderatius sea. Mea prompta fierent quaestio te.</td>
        </tr>
        <tr class="spacer8"></tr>
        </tr>
        </table>
        
        <div class="dt-div-wrapper-8lines">
        	<table id='table-campaign-calldate'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>
        </div>
</div>

<!-- 
	DIALOG ITEM:	"Nº de Bases de Dados"
-->
<div id="dialog-campaign-dbs">
        <div class="div-title">Informação</div>
        <table>
        <tr>
        <td class="td-text-justify">Lorem ipsum dolor sit amet, eu has quem impetus, est ex prima blandit suavitate. Ne eam propriae nominavi intellegam, vidit vitae cum ne, pri no illud nominati. Eu soluta lucilius prodesset eum, ne qui appetere accusamus inciderint. No sed quot detraxit, qui ea movet meliore fierent. Postea suscipiantur vis et. <br><br> Pri stet conclusionemque ut. Sit utroque nostrum ne, vide atqui quaerendum no mel. Sit inani vituperata instructior id. His eu harum nonumes, ullum oratio duo ad, id alienum detracto moderatius sea. Mea prompta fierent quaestio te.</td>
        </tr>
        <tr class="spacer8"></tr>
        </tr>
        </table>
        <div class="dt-div-wrapper-8lines">
        	<table id='table-campaign-dbs'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>
        </div>
</div>

<!-- 
	DIALOG ITEM:	"Total de Contactos"
-->
<div id="dialog-campaign-leads">
            <div class="div-title">Informação</div>
            <table>
            <tr>
            <td class="td-text-justify">  Na tabela seguinte é possivel visualizar os Feedbacks dos contactos de todas as Bases de Dados que se encontram associadas a esta Campanha. <br><br> É possivel filtrar por Bases de Dados <span class="td-text-justify-snippet"> activas, inactivas ou todas</span>, utilizando o navegador que se encontra abaixo.</td>
            </tr>
            <tr class="spacer8"></tr>
            </table>

            <center><div><button id="change-to-all" class="smaller">Todas</button><button id="change-to-inactive" class="smaller">Inactivas</button><button id="change-to-active" class="smaller">Activas</button><br></div></center>
			<br>
        	<table id='table-campaign-leads'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>

</div>

<!-- 
	DIALOG ITEM:	"Contactos no Dialer"
-->
<div id="dialog-campaign-dialer">
            <div class="div-title">Informação</div>
            <table>
            <tr>
            <td class="td-text-justify">Lorem ipsum dolor sit amet, eu has quem impetus, est ex prima blandit suavitate. Ne eam propriae nominavi intellegam, vidit vitae cum ne, pri no illud nominati. Eu soluta lucilius prodesset eum, ne qui appetere accusamus inciderint. No sed quot detraxit, qui ea movet meliore fierent. Postea suscipiantur vis et. <br><br> Pri stet conclusionemque ut. Sit utroque nostrum ne, vide atqui quaerendum no mel. Sit inani vituperata instructior id. His eu harum nonumes, ullum oratio duo ad, id alienum detracto moderatius sea. Mea prompta fierent quaestio te.</td>
       		</tr>
            <tr class="spacer8"></tr>
            </table>

            <table id='table-campaign-dialer'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>

</div>


<!-- 
	DIALOG ITEM:	"Contactos em Reciclagem"
-->
<div id="dialog-campaign-recycle">
	        <div class="div-title">Informação</div>
            <table>
            <tr>
            <td class="td-text-justify"> filler  </td>
            </tr>
            <tr class="spacer16"></tr>
            </table>

			<div class="dt-div-wrapper-6lines"> 
        	<table id='table-campaign-recycle'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>
       		</div>

</div>

<!-- 
	DIALOG ITEM:	"Callbacks"
-->
<div id="dialog-campaign-callbacks">
	        <div class="div-title">Informação</div>
            <table>
            <tr>
            <td class="td-text-justify"> Neste menu é possivel visualizar o numero de contactos que estão reservados como Callback nesta Campanha. <br><br> A tabela abaixo mostra a mesma informação mas agrupada por Operador. </td>
            </tr>
            <tr class="spacer16"></tr>
            </table>

			<div class="dt-div-wrapper-8lines"> 
        	<table id='table-campaign-callbacks'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>
       		</div>

</div>

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
.dt-fixed-6lines {min-height:210px;}

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

<script>

/* VARS  */
var campaign_id = "<?php echo $campaign_id; ?>";
var campaign_name = "<?php echo $aCampInfo['campaign_name']; ?>";
var campaign_description = "<?php echo $aCampInfo['campaign_description']; ?>";
/* UTILS */
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
/* QUERYS PRE-LOAD */
$(".loading").html("<img src='/images/icons/ajax-loader.gif'>");
$.ajax({
		type: "POST",
		url: "_requests.php",
		data: {action: "pre_all_leads", sent_all_lists: "<?php echo $inAllLists; ?>" },
		success: function(aData) { $("#td-campaign-leads").html(aData);  }
		});
$.ajax({
		type: "POST",
		url: "_requests.php",
		data: {action: "pre_last_call", sent_campaign_id: campaign_id },
		success: function(aData) { $("#td-campaign-calldate").html(aData);  }
		});
$.ajax({
		type: "POST",
		url: "_requests.php",
		data: {action: "pre_dialer_leads", sent_campaign_id: campaign_id },
		success: function(aData) { $("#td-campaign-dialer").html(aData);  }
		});
$.ajax({
		type: "POST",
		url: "_requests.php",
		data: {action: "pre_recycling_leads", sent_campaign_id: campaign_id, sent_all_lists: "<?php echo $inAllLists; ?>" },
		success: function(aData) { $("#td-campaign-recycle").html(aData);  }
});
$.ajax({
		type: "POST",
		url: "_requests.php",
		data: {action: "pre_callback_leads", sent_campaign_id: campaign_id },
		success: function(aData) { $("#td-campaign-callback").html(aData);  }
		});




/* 
ITEM:	 	"ID da Campanha"
*/
$("#dialog-campaign-id").dialog({ 
	title: ' <span style="font-size:13px; color:black">ID da Campanha</span> ',
	autoOpen: false,
	height: 300,
	width: 450,
	resizable: false,
	buttons: {
		"Fechar" : function() { $(this).dialog("close"); }
	},
	open : function() { $("button").blur(); }
});
$('#icon-campaign-id').click(function() {
	$("#dialog-campaign-id").dialog( "open" );
});

/* 
ITEM:	 	"Nome da Campanha"
*/
$('#icon-campaign-name').click(function(){ $("#dialog-campaign-name").dialog( "open" ); });
$("#dialog-campaign-name").dialog({ 
	title: ' <span style="font-size:13px; color:black">Nome da Campanha</span> ',
	autoOpen: false,
	height: 350,
	width: 450,
	resizable: false,
	open : function() { $("input").blur(); },
	buttons: {
		"OK" : function() {
				if($("#edit-campaign-name").val()!=campaign_name){
					campaign_name = $("#edit-campaign-name").val();
					$.ajax({
					type: "POST",
					dataType: "json",
					url: "_requests.php",
					data: {action: "change_campaign_name", sent_campaign_id: campaign_id, sent_campaign_name: campaign_name },
					error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
					success: function(data, textStatus, jqXHR) { $("#td-campaign-name").html(campaign_name); if( $('#table-campaign-changedate tbody').html().length == 0) {$('#td-campaign-changedate').html(data.reply[1])} else {$('#td-campaign-changedate').html(data.reply[1]); $('#table-campaign-changedate').dataTable().fnAddData([  data.reply[0],data.reply[1],data.reply[2],data.reply[3]  ]); }  }
					});
				}
			$(this).dialog("close");
	    },
		"Fechar" : function() { $(this).dialog("close");}
		}
});

/* 
ITEM:	 	"Descrição da Campanha"
*/
$('#icon-campaign-description').click(function(){ $("#dialog-campaign-description").dialog( "open" ); });
$("#dialog-campaign-description").dialog({ 
	title: ' <span style="font-size:13px; color:black">Descrição da Campanha</span> ',
	autoOpen: false,
	height: 425,
	width: 450,
	resizable: false,
	buttons: {
		"OK" : function() {
			if($("#new-campaign-description").val()!=campaign_description){
				campaign_description = $("#new-campaign-description").val();
				$.ajax({
				type: "POST",
				dataType: "json",
				url: "_requests.php",
				data: {action: "change_campaign_description", sent_campaign_id: campaign_id, sent_campaign_description: campaign_description },
				error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
				success: function(data, textStatus, jqXHR) {var span_campaign_description = campaign_description; while(span_campaign_description.length > 20 ){span_campaign_description = span_campaign_description.substring(0, span_campaign_description.length - 1);} if(span_campaign_description!=campaign_description){span_campaign_description = span_campaign_description + " ...";} $('#td-campaign-description').html(span_campaign_description); if( $('#table-campaign-changedate tbody').html().length == 0) {$('#td-campaign-changedate').html(data.reply[1])} else {$('#td-campaign-changedate').html(data.reply[1]); $('#table-campaign-changedate').dataTable().fnAddData([  data.reply[0],data.reply[1],data.reply[2],data.reply[3]  ]); }  }
				});
			
			}
			$(this).dialog("close");
	    },
		"Cancelar" : function() { $(this).dialog("close");}
		}
});

/* 
ITEM:	 	"Última Alteração"
*/
$('#icon-campaign-changedate').click(function(){
		$('#table-campaign-changedate').dataTable( {
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 8,
		"sDom": '<"top"f><"dt-fixed-8lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php', 
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "table-campaign-changedate" },
						{ "name": "sent_campaign_id", "value": campaign_id }
					   )
		},
		"aoColumns": [ 	{ "sTitle": "data_hidden", "bVisible": false },
						{ "sTitle": "Data", "sWidth":"95px", "sClass":"dt-col-center", "iDataSort": 0 },
						{ "sTitle": "Utilizador", "sWidth":"85px", "sClass":"dt-col-center" },
						{ "sTitle": "Alteração", "sWidth":"250px" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": function(oSettings, json){ $('#table-campaign-changedate').css({"width":"100%"});  },
		"fnPreDrawCallback": function ( oSettings ){ if($("#table-campaign-changedate-title").length == 0){ $("#table-campaign-changedate_wrapper .top").prepend("<div id='table-campaign-changedate-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Alterações feitas à Campanha</div>"); }  } 
	});
	$("#dialog-campaign-changedate").dialog( "open" ); });
$("#dialog-campaign-changedate").dialog({ 
	title: ' <span style="font-size:13px; color:black">Alterações à Campanha</span> ',
	autoOpen: false,
	height: 500,
	width: 900,
	resizable: false,
	buttons: { "Fechar" : function() { $(this).dialog("close");} },
	open: function(){ $("button").blur(); }
});

/* 
ITEM:	 	"Último Login"
 */
$('#icon-campaign-logindate').click(function(){
		$('#table-campaign-logindate').dataTable( {
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 8,
		"sDom": '<"top"f><"dt-fixed-8lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php', 
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "table-campaign-logindate" },
						{ "name": "sent_campaign_id", "value": campaign_id }
					   )
		},
		"aoColumns": [ 	{ "sTitle": "data_hidden", "bVisible": false },
						{ "sTitle": "Data", "sWidth":"95px", "sClass":"dt-col-center", "iDataSort": 0 },
						{ "sTitle": "Utilizador", "sWidth":"85px", "sClass":"dt-col-center" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": function(oSettings, json){ $('#table-campaign-logindate').css({"width":"100%"});  },
		"fnPreDrawCallback": function ( oSettings ){ if($("#table-campaign-logindate-title").length == 0){ $("#table-campaign-logindate_wrapper .top").prepend("<div id='table-campaign-logindate-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Logins na Campanha</div>"); }  } 
	});
	$("#dialog-campaign-logindate").dialog( "open" ); });
$("#dialog-campaign-logindate").dialog({ 
	title: ' <span style="font-size:13px; color:black">Acessos à Campanha</span> ',
	autoOpen: false,
	height: 525,
	width: 450,
	resizable: false,
	buttons: { "Fechar" : function() { $(this).dialog("close");} },
	open: function(){ $("button").blur(); }
});

/* 
ITEM:	 	"Última Chamada"
*/
$('#icon-campaign-calldate').click(function(){
		$('#table-campaign-calldate').dataTable( {
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 8,
		"sDom": '<"top"f><"dt-fixed-8lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php', 
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "table-campaign-calldate" },
						{ "name": "sent_campaign_id", "value": campaign_id }
					   )
		},
		"aoColumns": [ 	{ "sTitle": "data_hidden", "bVisible": false },
						{ "sTitle": "Data", "sWidth":"95px", "sClass":"dt-col-center", "iDataSort": 0 },
						{ "sTitle": "Utilizador", "sWidth":"85px", "sClass":"dt-col-center" },
						{ "sTitle": "Telefone", "sWidth":"85px", "sClass":"dt-col-center" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": function(oSettings, json){ $('#table-campaign-calldate').css({"width":"100%"});  },
		"fnPreDrawCallback": function ( oSettings ){ if($("#table-campaign-calldate-title").length == 0){ $("#table-campaign-calldate_wrapper .top").prepend("<div id='table-campaign-calldate-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Últimas Chamadas</div>"); }  } 
	});
	$("#dialog-campaign-calldate").dialog( "open" ); });
$("#dialog-campaign-calldate").dialog({ 
	title: ' <span style="font-size:13px; color:black">Últimas Chamadas</span> ',
	autoOpen: false,
	height: 525,
	width: 450,
	resizable: false,
	buttons: { "Fechar" : function() { $(this).dialog("close");} },
	open: function(){ $("button").blur(); }
});

/* 
ITEM:	 	"Nº de DBs"
*/
$('#icon-campaign-dbs').click(function(){
		$('#table-campaign-dbs').dataTable( {
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 8,
		"sDom": '<"top"f><"dt-fixed-8lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php', 
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "table-campaign-dbs" },
						{ "name": "sent_campaign_id", "value": campaign_id }
					   )
		},
		"aoColumns": [ 	{ "sTitle": "Base de Dados", "sWidth":"130px", "sClass":"dt-col-center" },
						{ "sTitle": "Activa", "sWidth":"16px", "sClass":"dt-col-center" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": function(oSettings, json){ $('#table-campaign-calldate').css({"width":"100%"});  },
		"fnPreDrawCallback": function ( oSettings ){ if($("#table-campaign-dbs-title").length == 0){ $("#table-campaign-dbs_wrapper .top").prepend("<div id='table-campaign-dbs-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Bases de Dados</div>"); }  } 
	});
	$("#dialog-campaign-dbs").dialog( "open" ); });
$("#dialog-campaign-dbs").dialog({ 
	title: ' <span style="font-size:13px; color:black">Bases de Dados</span> ',
	autoOpen: false,
	height: 525,
	width: 450,
	resizable: false,
	buttons: { "Fechar" : function() { $(this).dialog("close");} },
	open: function(){ $("button").blur(); }
});
function DBEnabler(list)
{
	var cross = "/images/icons/cross_16.png";
	var tick =  "/images/icons/tick_16.png";
	var img_id = "#db-enabler-" + list
	var state;

	
	if($(img_id).attr("src")==cross){ state="Y"; } else { state="N"; }
	$.ajax({
			
			type: "POST",
			dataType: "json",
			url: "_requests.php",
			data: {action: "db-enabler", sent_list: list, sent_state: state},
			error: function()
			{
				alert("Ocorreu um Erro.");
			},
			success: function(data)	
			{
				if(state=="Y") {$(img_id).attr("src", tick); $('#td-active-lists').html(parseInt($('#td-active-lists').html())+1); $('#td-inactive-lists').html(parseInt($('#td-inactive-lists').html())-1); } else {$(img_id).attr("src", cross); $('#td-active-lists').html(parseInt($('#td-active-lists').html())-1); $('#td-inactive-lists').html(parseInt($('#td-inactive-lists').html())+1);}
			}
		});
}

/* 
ITEM:	 	"Total de Contactos"
*/
var current_search = "ACTIVE";
$("#dialog-campaign-leads").dialog({ 
	title: ' <span style="font-size:13px; color:black">Contactos da Campanha</span> ',
	autoOpen: false,
	height: 550,
	width: 450,
	resizable: false,
	buttons: {
		"Fechar" : function() { $(this).dialog("close"); }
	},
	open: function() { 	switch(current_search)
							{
								case "ACTIVE": $("#change-to-active").focus(); break;
								case "INACTIVE": $("#change-to-inactive").focus(); break;
								case "ALL": $("#change-to-ALL").focus(); break;
							}  
					 } 
});
$('#icon-campaign-leads').click(function(){
	var oTable_campaign_leads = $('#table-campaign-leads').dataTable( {
		"aaSorting": [[0, 'asc']],
		"iDisplayLength": 12,
		"sDom": '<"top"f><"dt-fixed-12lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php',
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "table-campaign-leads" },
						{ "name": "sent_campaign_id", "value": campaign_id },
						{ "name": "sent_list_filter", "value": "<?php echo $inAllLists; ?>" }
					   )
		},
		"aoColumns": [ 
						{ "sTitle": "Feedback", "sWidth":"150px" },
						{ "sTitle": "Nº de Leads", "sWidth":"32px", "sClass":"dt-col-center" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnPreDrawCallback": function ( oSettings ){ if($("#table-campaign-leads-title").length == 0){ $("#table-campaign-leads_wrapper .top").prepend("<div id='table-campaign-leads-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Estados dos Contactos</div>"); }  } 

	});
	$("#change-to-active").button().click(function(){ if(current_search!="ACTIVE") { current_search="ACTIVE"; list_filter="<?php echo $inAllActiveLists; ?>"; oTable_campaign_leads.fnReloadAjax();} });
	$("#change-to-inactive").button().click(function(){  if(current_search!="INACTIVE"){ current_search="INACTIVE"; list_filter="<?php echo $inAllInactiveLists; ?>"; oTable_campaign_leads.fnReloadAjax();} });
	$("#change-to-all").button().click(function(){  if(current_search!="ALL"){ current_search="ALL"; list_filter="<?php echo $inAllLists; ?>"; oTable_campaign_leads.fnReloadAjax();} });
	$("#dialog-campaign-leads").dialog( "open" ); 
});

/* 
ITEM:	 	"Contactos no Dialer"
*/
$('#icon-campaign-dialer').click(function(){
		$('#table-campaign-dialer').dataTable( {
		"aaSorting": [[0, "desc"]],
		"iDisplayLength": 8,
		"sDom": '<"top"f><"dt-fixed-8lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php', 
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "table-campaign-dialer" },
						{ "name": "sent_campaign_id", "value": campaign_id }
					   )
		},
		"aoColumns": [ 	{ "sTitle": "Telefone", "sWidth":"50px", "sClass":"dt-col-center", "iDataSort": 0 },
						{ "sTitle": "Nome", "sWidth":"150px", "sClass":"dt-col-center" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": function(oSettings, json){ $('#table-campaign-logindate').css({"width":"100%"});  },
		"fnPreDrawCallback": function ( oSettings ){ if($("#table-campaign-dialer-title").length == 0){ $("#table-campaign-dialer_wrapper .top").prepend("<div id='table-campaign-dialer-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Contactos no Dialer</div>"); }  } 
	});
	$("#dialog-campaign-dialer").dialog( "open" ); });
$("#dialog-campaign-dialer").dialog({ 
	title: ' <span style="font-size:13px; color:black">Contactos no Dialer</span> ',
	autoOpen: false,
	height: 505,
	width: 450,
	resizable: false,
	buttons: { "Fechar" : function() { $(this).dialog("close");} },
	open: function(){ $("button").blur(); }
});

/* 
ITEM:	 	"Contactos em Reciclagem"
*/
$("#dialog-campaign-recycle").dialog({ 
	title: ' <span style="font-size:13px; color:black">Contactos em Reciclagem</span> ',
	autoOpen: false,
	height: 400,
	width: 450,
	resizable: false,
	buttons: {
		"Fechar" : function() { $(this).dialog("close"); }
	}
});
$('#icon-campaign-recycle').click(function() {
	$('#table-campaign-recycle').dataTable( {
		"aaSorting": [[0, 'asc']],
		"iDisplayLength": 8,
		"sDom": '<"top"f><"dt-fixed-8lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php', 
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "get_table-campaign-recycle" },
						{ "name": "sent_campaign_id", "value": campaign_id },
						{ "name": "sent_list_filter", "value": "<?php echo $inAllLists; ?>" }
					   )
		},
		"aoColumns": [ 
						{ "sTitle": "Feedback", "sWidth":"150px" },
						{ "sTitle": "Em Reciclagem", "sWidth":"32px", "sClass":"dt-col-center" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnPreDrawCallback": function ( oSettings ){ if($("#table-campaign-recycle-title").length == 0){ $("#table-campaign-recycle_wrapper .top").prepend("<div id='table-campaign-recycle-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Reciclagem na Campanha</div>"); }  } 
	});
	$("#dialog-campaign-recycle").dialog( "open" );
});

/* 
ITEM:	 	"Callbacks"
*/
$("#dialog-campaign-callbacks").dialog({ 
	title: ' <span style="font-size:13px; color:black">Callbacks</span> ',
	autoOpen: false,
	height: 450,
	width: 550,
	resizable: false,
	buttons: {
		"Fechar" : function() { $(this).dialog("close"); }
	},
	open: function(){ $("*").blur(); }
});
$('#icon-campaign-callbacks').click(function() {
	$('#table-campaign-callbacks').dataTable( {
		"aaSorting": [[0, 'asc']],
		"iDisplayLength": 8,
		"sDom": '<"top"f><"dt-fixed-8lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php', 
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "table-campaign-callbacks" },
						{ "name": "sent_campaign_id", "value": campaign_id },
						{ "name": "sent_list_filter", "value": "<?php echo $inAllLists; ?>" }
					   )
		},
		"aoColumns": [ 
						{ "sTitle": "Operador", "sWidth":"200px" },
						{ "sTitle": "Nº Callbacks", "sWidth":"24px", "sClass":"dt-col-center" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnPreDrawCallback": function ( oSettings ){ if($("#table-campaign-callbacks-title").length == 0){ $("#table-campaign-callbacks_wrapper .top").prepend("<div id='table-campaign-callbacks-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Callbacks dos Utilizadores</div>"); }  } 
	});
	$("#dialog-campaign-callbacks").dialog( "open" );
});

</script>
