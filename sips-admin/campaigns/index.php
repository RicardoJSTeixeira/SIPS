<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>
<br>

<div style='width:90%; margin:0 auto;'>
    <div class='dt-div-wrapper-10lines'>
        <table id='campaign_list'>
        <thead></thead>
        <tbody></tbody>
        <tfoot></tfoot>
        </table>
    </div>
</div> 

<div id="dialog-audio-chooser" style="display:none;"></div>

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
		"iDisplayLength": 10,
		"sDom": '<"top"f><"dt-fixed-10lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": 'requests.php',
		"fnServerParams": function (aoData)  
		{
			aoData.push( 
						{ "name": "action", "value": "GetCampaigns" }
					   )
		},
		"aoColumns": [ 
						{ "sTitle": "Editar", "sWidth":"16px", "sClass":"dt-column-center" },
						{ "sTitle": "Campanha", "sType": "string", "sClass":"dt-column-camp" }, 
						{ "sTitle": "Activa", "sWidth": "16px", "sClass": "dt-column-center", "sType": "string" },
						{ "sTitle": "Método de Marcação", "sClass": "dt-column-center", "sWidth": "100px" },
						{ "sTitle": "Rácio de Marcação", "sClass": "dt-column-center", "sWidth": "100px" }
						
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": function(){ if(typeof $("#table-campaign-changedate-title").html() == 'undefined'){ $("#campaign_list_wrapper .top").append("<div id='table-campaign-changedate-title' style='float:left; font-weight:bold;'><button style='margin-bottom: 6px' id='btn-new-campaign'>Nova Campanha</button></div>"); } $("#btn-new-campaign").button().click(function(){ window.location = 'new_campaign.php'; }); }
		});
	
	}

);

</script>

<style>
.elem-pointer { cursor:pointer; }


.dataTables_filter input[type="text"] { width:170px; height:20px; margin-top:6px; }
.dataTables_paginate { margin-top:10px; margin-right:0px; float:none; } 

.dt-fixed-10lines {min-height:335px;}
.dt-div-wrapper-10lines { min-height:285px; }


.dt-column-center { text-align:center; }
.dt-column-camp { color:black; }
</style>


<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>