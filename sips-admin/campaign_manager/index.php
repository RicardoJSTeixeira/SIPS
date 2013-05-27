<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");

/*

Alterações na Base de Dados

- vicidial_campaigns | campaign_description TYPE = TEXT

#################################

Bugs

- Detalhes da Campanha | Ultima Alteração e Ultima Chamada | 1 hora de adiantada.

- Adminlogger - O update da tabela po fnAddRows adiciona o user em vez do fullname.

#################################

Campos que faltam nas Opções da Campanha vs o detail view do vicidial

- Park Music on Hold - arranjar/criar wizard de upload de musicas.
- Web Forms/Target - Supostamente para trabalhar com scripts externos, explorar melhor esta opção, escolher 3-4 progs do genero do Lime survey e integrar.
- Allow Closers - Closers supostamente sao super agentes que estao a espera de reencaminhamentos de chamadas de outros agentes, basicamente tratam casos especificos dentro do call center, são agentes com mais experiencia ou outro tipo de formação para tratar de casos ou clientes especiacias, não são agentes para receber as "cold calls" ou seja chamadas que acabaram de chegar, tem de ser melhor explorado/testado.
- ListMix 
- Lead Filter
- Drop Percentage Limit: 	
- Maximum Adapt Dial Level: 
- Latest Server Time: 
- Adapt Intensity Modifier: 	
- Dial Level Difference Target: 
- Concurrent Transfers: 
- Queue Priority: 	  
- Multiple Campaign Drop Rate Group: 	
- Local Call Time: 	  
- Dial Prefix: 
- Manual Dial Prefix: 	
- Omit Phone Code: 	
- Campaign CallerID: 	
- Custom CallerID: 	 
- Routing Extension:
- Call Notes per Call
- Campaign Rec exten
- Script
- Answering Machine Message: 	
- WaitForSilence Options: 	 
- AMD Send to VM exten: 	
- CPD AMD Action:
- Transfer-Conf DTMF 1
- Transfer-Conf Number 1
- Transfer-Conf DTMF 2
- Transfer-Conf Number 2
- Transfer-Conf Number 3
- Transfer-Conf Number 4
- Transfer-Conf Number 5
- Enable Transfer Presets
- Hide Transfer Number to Dial
- Quick Transfer Button
- PrePopulate Transfer Preset
- Park Call IVR
- Park Call IVR AGI
- Timer Action
- Timer Action Message  
- Timer Action Seconds 
- Timer Action Destination
- Alt Number Dialing
- My Callbacks Checkbox Default
- safe Harbor Exten
- Wrap Up Seconds
- Wrap Up MEssage
- Campaign stats refresh
- Real-time Agent Time Stats
- No Hopper Dialing
- Owner Only Dialing
- Manual Call Time Check
- Manual Dial CID
- Phone Post Time Difference Alert 
-*Agent Screen Extended Alt Dial
- Agent Screen Clipboard Copy
*3-Way Call Options
- Group Alias Allowed
- CRM Popup Login
- CRM Popup Address
- Start Call URL
- Dispo Call URL
- Extension Append CID
- Blind Monitor Warning
- Blind Monitor Notice
- Blind Monitor Filename

Agent Call Re-Queue Button 		
Agent Grab Calls in Queue 		
View Calls in Queue Launch 		
Agent View Calls in Queue 		
Agent Display Queue Count 		
Agent Display Dialable Leads 	

Auto Pause Pre-Call Work 		
Auto Resume Pre-Call Work 		
Auto Pause Pre-Call Code

Drop Transfer Group	

#################################

Ideias

- Contactos disponiveis da campanha | Adicionar campo com o numero total de contactos à vicidial_lists que é updated cada x que se carrega contactos em vez de contar a vicidial_list toda.

#################################

To-Do List

- Verificar os lead search methods dos agentes em chamadas manuais, re implementar o sistema para ser mais intuitivo.
- Alterar alguns campos do vicidial_admin_log para serem ENUM, para evitar erros na função "AdminLogger" @ functions/functions.php
- Alterar todas as queryes que escrevem na event_notes da vicidial_admin_log para Portugues

*/
?>

<div class="cc-mstyle">
	<table>
		<tr>
			<td id="icon32"><img src='/images/icons/construction_32.png' /></td>
			<td id='submenu-title' style='width:400px'> Gestor de Campanhas | <span style="font-weight:normal">Págna Inicial</span></td> 
		<!--	<td style='text-align:right'><table style="width:30%; cursor:pointer" onclick="parent.mbody.location='wizard'"><tr><td id="icon32"><img height="28px" width="28px" src="/images/icons/world_add_32.png"></td><td style="text-align:left">Criar Nova Campanha</td></tr></table></td> -->
			<td style='text-align:right'><table style="width:30%; cursor:pointer" onclick="parent.mbody.location='wizard_intra_justicia'"><tr><td id="icon32"><img height="28px" width="28px" src="/images/icons/world_add_32.png"></td><td style="text-align:left">Criar Nova Campanha</td></tr></table></td>

        </tr>
	</table>
</div>

<br>
<div style='width:90%; margin:0 auto;'>
<table id='campaign_list'>
<thead></thead>
<tbody></tbody>
<tfoot></tfoot>
</table>
</div> 

<style>
.elem-pointer { cursor:pointer; }
.dataTables_filter { margin-bottom:6px; }
.dataTables_filter input[type="text"] { width:150px; height:20px; }
/*.dataTables_paginate { margin-top:10px; margin-right:1px; float:none; } */
.dataTables_info { margin-top:6px; }
.dt-column-center { text-align:center; }
.dt-column-camp { color:black; }
</style>
<script>

function CampaignEnabler(campaign)
{
	var cross = "/images/icons/cross_16.png";
	var tick =  "/images/icons/tick_16.png";
	var img_id = "#cmp-enabler-" + campaign;
	var state;

	
	if($(img_id).attr("src")==cross){ state="Y"; } else { state="N"; }
	$.ajax({
			
			type: "POST",
			dataType: "json",
			url: "_requests.php",
			data: {action: "change_campaign_state", sent_campaign: campaign, sent_state: state},
			error: function()
			{
				alert("Ocorreu um Erro.");
			},
			success: function(data)	
			{
				if(state=="Y") {$(img_id).attr("src", tick);} else {$(img_id).attr("src", cross);}
			}
		});
}


$(document).ready
(
	
	function()
	{
	var oTable = $('#campaign_list').dataTable( {
		"aaSorting": [[1, 'asc'], [2, 'desc']],
		"iDisplayLength": 50,
		"sDom": '<"top"f>rt<"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php',
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "get_campaign_list" }
					   )
		},
		"aoColumns": [ 
						{ "sTitle": "Editar", "sWidth":"16px", "sClass":"dt-column-center" },
						{ "sTitle": "Campanha", "sType": "string", "sClass":"dt-column-camp" }, 
						{ "sTitle": "Activa", "sWidth": "16px", "sClass": "dt-column-center", "sType": "string" },
						{ "sTitle": "Método de Marcação", "sClass": "dt-column-center", "sWidth": "100px" },
						{ "sTitle": "Rácio de Marcação", "sClass": "dt-column-center", "sWidth": "100px" }
						
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" }
		});
	}

);

/* "fnDrawCallback": function(oSettings, json){ $('#campaign_list').show(); $('#campaign_list').css({"width":"100%"}) }, */ 




</script>
<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>