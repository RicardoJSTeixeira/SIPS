<!DOCTYPE html>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>SIPS</title>
</head>
<body>
<?php
$campaign_id=$_GET['campaign_id'];
$campaign_name=$_GET['campaign_name'];
?>
<br>
<div class="demo" style='margin:0 auto;'>
<button id="add-new-pause" style='float:left; margin-bottom:12px;'>Adicionar Pausas</button>
<button id="create-new-pause" style='float:right; margin-bottom:12px;'>Gerir Pausas</button>
<br>
<table id='campaign_pause_list'>
<thead></thead>
<tbody></tbody>
<tfoot></tfoot>
</table>
</div>

        <!-- DIV com os elementos do popup "Gestão de Pausas" -->
        <div id="dialog-pause-manager">
        	<div style='min-height:275px;'>
        	<b>Pausas Configuradas no SIPS</b>
        	<table id='all-pauses-list'>
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
        	</table>
       		</div>
        	<div style='margin-bottom:6px; border-bottom:1px solid #DDDDDD'>
            	<b>Criar uma Nova Pausa no SIPS</b>
			</div>
        <table style='width:100%'>
        	<tr>
        	<td class="td-label-form">
        		<label for="nome-pausa">Nome da Pausa</label>
            </td>
        	<td>
        		<input type="text" id="nome-pausa" class="text ui-widget-content ui-corner-all input-form">
            </td>
            </tr>
        	<tr>
        	<td class="td-label-form">
        		<label for="nome-pausa">Tempo da Pausa</label>
        	</td>
            <td>
        		<input type="text" id="tempo-pausa" style="width:32px" class="text ui-widget-content ui-corner-all input-form">&nbsp;em minutos.
            </td>
            </tr>
        </table>
        </div>

<style>
.input-form { height:20px; text-align:center;  }
.td-label-form { text-align:left; padding:10px; }
.elem-pointer { cursor:pointer; }
.dataTables_filter { margin-bottom:6px; }
.dataTables_filter input[type="text"] { width:150px; height:20px; }
.dataTables_paginate { margin-top:6px; margin-right:1px; float:none; }
.dataTables_info { margin-top:6px; }
.dt-column-center { text-align:center; }
.dt-column-main { color:black; }
</style>
<script>

$("#dialog-pause-manager").dialog({ 
	title: ' <span style="font-size:14px">Gestão de Pausas</span> ',
	autoOpen: false,
	height: 500,
	width: 450,
	buttons: {
		"OK" : function() { $(this).dialog("close");},
		"Cancelar" : function() { $(this).dialog("close");}
		}
});

$(function(){
$( "#add-new-pause" ).button();
$( "#create-new-pause" ).button();
$( "#create-new-pause" ).click(function(){
	$("#dialog-pause-manager").dialog( "open" );
	})
});

function PauseEnabler(pause_code)
{
	var cross = "/images/icons/cross_16.png";
	var tick =  "/images/icons/tick_16.png";
	var img_id = "#pause-enabler-" + pause_code;
	var state;
	if($(img_id).attr("src")==cross){ state=1; } else { state=0; }
	$.ajax({
			type: "POST",
			dataType: "json",
			url: "_requests.php",
			data: {action: "change_pause_state", sent_pause_code: pause_code, sent_state: state},
			error: function()
			{
				alert("Ocorreu um Erro.");
			},
			success: function(data)	
			{
				if(state==1) {$(img_id).attr("src", tick);} else {$(img_id).attr("src", cross);}
			}
		});
}

$(document).ready
(

	
	function() 
	{
	$('#campaign_pause_list').dataTable( {
		"aaSorting": [[0, 'asc']],
		"iDisplayLength": 100,
		"sDom": '<"top">rt<"bottom"><"cont"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bDestroy": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php',
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "get_campaign_pauses" },
						{ "name": "sent_campaign_id", "value": "<?php echo $campaign_id; ?>" }
					   )
		},
		"aoColumns": [ 
						{ "sTitle": "Descrição", "sWidth":"200px", "sClass":"dt-column-main" },
						{ "sTitle": "Tempo da Pausa", "sWidth":"16px", "sClass":"dt-column-center" }, 
						{ "sTitle": "Activa", "sWidth": "16px", "sClass": "dt-column-center", "sType" : "string" }						
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" }
		});
		$('#all-pauses-list').dataTable( {
		"aaSorting": [[0, 'asc']],
		"iDisplayLength": 10,
		"sDom": '<"top">rt<"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bDestroy": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php',
		"fnServerParams": function (aoData) 
		{
			aoData.push( 
						{ "name": "action", "value": "get_all_pause_codes" }
					   )
		},
		"aoColumns": [ 
						{ "sTitle": "Nome da Pausa", "sWidth":"100px", "sClass":"dt-column-main" },
						{ "sTitle": "Tempo da Pausa", "sWidth":"64px", "sClass":"dt-column-center" }, 
						{ "sTitle": "Activa", "sWidth": "12px", "sClass": "dt-column-center", "sType" : "string" }						
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": function(oSettings, json){ $('#all-pauses-list').css({"width":"100%"}) }
		});
	}
	
	


);
</script>
</body>
</html>
