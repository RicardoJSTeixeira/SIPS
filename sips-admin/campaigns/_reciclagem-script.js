function RecycleListBuilder(Flag)
{
	
$(".span-active-campaign-name").html($("#campaign-name").val()) 	
	 
if( ($("#tbl-recycle tr").length == 0 && Flag == "ALL") || ((Flag == "NEW") && ($("#select-new-recycle option:selected").val() != "---")) || Flag == "REFRESH" )
{
    if(Flag != "NEW") { $("#tbl-recycle").hide(); }
    
    $.ajax({
        type: "POST",
        url: "_reciclagem-requests.php",
        dataType: "JSON",
        data: { action: "RecycleListBuilder",
                CampaignID: CampaignID,
                CampaignLists: CampaignLists,
                RecycleID: $("#select-new-recycle option:selected").val(),
                RecycleName: $("#select-new-recycle option:selected").html(),
                RecycleDelay: $("#spinner-recycle-time").val(),
                RecycleTries: $("#spinner-recycle-tries").val(),
                AllowedCampaigns: AllowedCampaigns,
                Flag: Flag
                },
        success: function(data) 
        {

            if(Flag == 'ALL' || Flag == 'REFRESH'){ $('#tbl-recycle').empty(); }
            if(data){
                
                var OddEven;
                var Checked;
                
                $.each(data.recycle, function(index, value){


				if(typeof data.recycle_count[index] == 'undefined') { data.recycle_count[index] = 0;}

                if(data.active[index] == "Y")
                {
                    Checked = "checked='checked'";
                }   
                else
                {
                    Checked = "";
                }
                
                if(data.recycle[index] == "ERI" || data.recycle[index] == "PDROP" || data.recycle[index] == "DC" || data.recycle[index] == "PU" || data.recycle[index] == "NA" || data.recycle[index] == "DROP" || data.recycle[index] == "B")
                {
                    Disabled = "disabled='disabled'";
                }
                else
                {
                    Disabled = "";
                }   

                $("#tbl-recycle").prepend("\n\
                    <tr class='tr-recycle-rows' recycle-id='"+ data.recycle[index] + "' style='height:22px;'>\n\
                        <td class='css-td-list-check odd-even-ignore'><input "+Disabled+" "+Checked+" class='recycle-active-inactive recycle-checkbox' type='checkbox' id='"+ data.recycle[index] + "'></td>\n\
                        <td class='css-td-list-label'><label class='label-recycle-name' for='"+ data.recycle[index] + "'>"+data.recycle_name[index]+"</label></td>\n\
                        <td class='css-td-list-icon'><img class='css-img-list-icon' title='Contactos para Reciclar' src='icons/mono_refresh_16.png'></td>\n\
                        <td class='css-td-list-text' id='td-to-recycle'>"+data.recycle_count[index]+"</td>\n\
                        <td class='css-td-list-icon'><img class='css-img-list-icon' title='Intervalo de Reciclagem' src='icons/mono_stopwatch_16.png'></td>\n\
                        <td class='css-td-list-text recycle-attempt-delay'>"+data.attempt_delay[index]+" m</td>\n\
                        <td class='css-td-list-icon'><img class='css-img-list-icon' title='Nº máximo de Tentativas' src='icons/mono_reload_16.png'></td>\n\
                        <td class='css-td-list-text recycle-attempt-maximum'>"+data.attempt_maximum[index]+"</td>\n\
                        <td class='css-td-list-actions'><img class='css-img-list-actions edit-recycle' recycle-to-edit='"+ data.recycle[index] + "' title='Configurar' src='icons/mono_wrench_16.png'></td>\n\
                    </tr>\n\
                    ")
                })
        
                
                $(".recycle-checkbox").uniform();
                OddEvenRefresh("", "tbl-recycle");
                
                if(Flag != "NEW"){
                    $("#select-new-recycle").append("<option value='---'>---</option>")
                    $.ajax({
                        type: "POST",
                        url: "_reciclagem-requests.php",
                        dataType: "JSON",
                        data: { action: "RecycleAvailFeeds",
                                CampaignID: CampaignID
                                },
                        success: function(data){
                        
                        if(data)
                        {
                            $.each(data.status, function(index, value){
                        
                            $("#select-new-recycle").append("<option value='"+data.status[index]+"'>"+data.status_name[index]+"</option>")
                        
                        })
                        }
                        

                                
                        }
                    })
                } else { $("#select-new-recycle option:selected").remove(); }
            }
            else
            {
                if(Flag == "NEW"){

                }
                else
                {
                    
                } 
            }

        if(Flag != "NEW") { $("#tbl-recycle").fadeIn(200); }    
        }
    });

    }
    else
    {
        if(Flag == "NEW"){
            $("#td-new-recycle-error").html("<b>Por favor escolha um Feedback válido.</b>")
        }
        
    }
}

function RecycleElemInit()
{
	
$("#btn-recycle-reset-feedback").button();
$("#btn-edit-recycle-contact-details-reset-all").button();
$("#btn-edit-recycle-contact-details-disable-all").button();	
$("#btn-new-recycle").button();	
$("#btn-recycle-apply-to-all-campaigns").button();	
$("#btn-recycle-view-disabled").button();		
	
	
	
$('#spinner-recycle-time').spinner({
    min: 5,
    max: 180,
    step: 5
});

$('#spinner-recycle-tries').spinner({
    min: 1,
    max: 10,
    step: 1
});	
	
	
EditRecycleTimeSpinner = $('#spinner-edit-recycle-time').spinner({
    min: 5,
    max: 180,
    step: 5
});

EditRecycleTriesSpinner = $('#spinner-edit-recycle-tries').spinner({
    min: 1,
    max: 10,
    step: 1
});

$("#dialog-edit-recycle").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_wrench_16.png'></td><td><span class='dialog-title'>Detalhes da Reciclagem > Feedback: </span><span style='color:#0073ea' class='span-dialog-title-edit-recycle-feed'></span></td></tr></table>",
    autoOpen: false,
    height: 635,
    width: 550,
    resizable: false,
    buttons: { 	"Gravar" : DialogEditRecycleOnSave,
                "Fechar" : DialogClose },
    open: DialogEditRecyleOnOpen
}); 


$("#dialog-edit-recycle-contact-details").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_right_expand_16.png'></td><td><span class='dialog-title'>Lista de Contactos em Reciclagem > Feedback: </span><span style='color:#0073ea' class='span-dialog-title-edit-recycle-feed'></span><span class='dialog-title'> > Tentativa: </span><span style='color:#0073ea' id='span-dialog-title-edit-recycle-try'></span></td></tr></table>",
    autoOpen: false,
    height: 570,
    width: 950,
    resizable: false,
	position: { my: "center+10%", of: "#recycle-details" },
    buttons: { "Fechar" : DialogClose },
    open: DialogEditRecycleContactDetailsOnOpen
    
}); 

$("#dialog-confirm-recycle-apply-to-all-campaigns").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alerta!</span> ',
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { 	"OK" : DialogRecycleApplyToAllCampaignsOnSave, 
    			"Cancelar": DialogClose
	},
    open: function(){}
}); 

}

function RecycleActiveSwitch()
{
	var Checked;
    var NumRecycle;
    if($(this).parent().hasClass("checked"))
    {
        Checked = "Y";
        NumRecycle = 1;
    }
    else 
    {
        Checked = "N";
        NumRecycle = -1;
    }

    $.ajax({
            type: "POST",
            url: "_reciclagem-requests.php",
            data: 
            { 
                action: "RecycleActiveInactive",
                CampaignID : CampaignID,
                RecycleID : $(this).attr("id"),
                RecycleActive: Checked,
                NumRecycle: NumRecycle
            },
            success: function(data) {}
    });

	
}

function NewRecycle()
{
	RecycleListBuilder('NEW');
}

function ClearRecycleErrorMsg()
{
	$("#td-new-recycle-error").html("");
}

function DialogRecycleApplyToAllCampaignsOnSave()
{
	 var recycle_ids = new Array();
    var recycle_active = new Array();
    var recycle_time = new Array();
    var recycle_tries = new Array();  
    
    
    
    $("#tbl-recycle tr").each(function(){

    recycle_ids.push($(this).attr("recycle-id"));
    
    if($(this).find(".checker").children().hasClass("checked"))
    { recycle_active.push("Y") }
    else
    { recycle_active.push("N") }
    
    recycle_time.push($(this).find(".recycle-attempt-delay").html().replace(" m", ""));
    recycle_tries.push($(this).find(".recycle-attempt-maximum").html())
    

    


    });

    
    $.ajax({
            type: "POST",
            url: "_reciclagem-requests.php",
            data: { action: "ApplyRecycleToAllCampaings",
                    RecycleID: recycle_ids, 
                    RecycleActive: recycle_active,
                    RecycleDelay: recycle_time,
                    RecycleTries: recycle_tries
                    },
            success: function(data) {}
        });     
        
        $(this).dialog("close");
	
}

// dialog edit recycle

function TableEditRecycleInit()
{

    tableEditRecycle = $('#recycle-details').dataTable( {
        "aaSorting": [[4, 'asc']],
        "iDisplayLength": 12,
        "sDom": '<"top"><rt><"bottom">',
        "bSortClasses": false,
        "bJQueryUI": true,  
        "bProcessing": true, 
        "bRetrieve": false,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": '_reciclagem-requests.php',
        "fnServerParams": function (aoData)  
        {
            aoData.push( 
                        { "name": "action", "value": "GetRecycleDetails" },
                        { "name": "RecycleID", "value": editedRecycle },
                        { "name": "CampaignID", "value": CampaignID },
                        { "name": "CampaignLists", "value": CampaignLists }
                       )
        },
        "aoColumnDefs": [
                        { "bSearchable": false, "bVisible": false, "aTargets": [ 4 ] },
						{ "bSearchable": false, "bSortable" : false, "bVisible": true, "aTargets": [ 0, 1, 2, 3 ] }
                    ],
        "aoColumns": [ 
                        
                        { "sTitle": "Tentativas", "sWidth":"16px", "sClass":"css-dt-column-align-center" },
                        { "sTitle": "Nº de Contactos", "sWidth": "64px", "sClass":"css-dt-column-align-center tempclass" }, 
                        { "sTitle": "Reset", "sWidth": "16px", "sClass":"css-dt-column-align-center" },
                        { "sTitle": "Ver Detalhes", "sWidth": "32px", "sClass":"css-dt-column-align-center" },
                        { "sTitle": "index" }
                     ], 
        "oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
        "fnDrawCallback":   function()
                            { 
                                $("#recycle-details").css("width", "100%");                     
                            }
        });
    

    
}

function DialogEditRecyleOnOpen()
{
	$("button").blur();
	$("#div-recycle-details-container").hide();
	
	$(".span-dialog-title-edit-recycle-feed").html($("label[for='"+editedRecycle+"']").html());
	
	EditRecycleTimeSpinner.spinner("value", $.trim($("tr[recycle-id='"+editedRecycle+"']").find(".recycle-attempt-delay").html().replace(" m", "")));
	EditRecycleTriesSpinner.spinner("value", $.trim($("tr[recycle-id='"+editedRecycle+"']").find(".recycle-attempt-maximum").html()));


if(tableEditRecycle === undefined)
{
	console.log("init")
	TableEditRecycleInit();
	$("#div-recycle-details-container").fadeIn(200);
}
else
{
	console.log("reload")
	tableEditRecycle.fnReloadAjax();
	$("#div-recycle-details-container").fadeIn(200);
}



}

function DialogEditRecycleOnSave()
{
	  $("tr[recycle-id='"+editedRecycle+"']").find(".recycle-attempt-delay").html(EditRecycleTimeSpinner.spinner("value") + " m")
                $("tr[recycle-id='"+editedRecycle+"']").find(".recycle-attempt-maximum").html(EditRecycleTriesSpinner.spinner("value"))


                 $.ajax({
                        type: "POST",
                        url: "_reciclagem-requests.php",
                        data: { action: "DialogEditRecycleOnSave",
                                RecycleID: editedRecycle,
                                RecycleDelay: EditRecycleTimeSpinner.spinner("value"),
                                RecycleTries: EditRecycleTriesSpinner.spinner("value"),
                                CampaignID: CampaignID
                                },
                        success: function(data) {}
                    }); 

                $(this).dialog("close");
	
}

function EditRecycleResetSingle()
{
	var clickedRow = $(this).closest("tr")[0]._DT_RowIndex;
var clickedIndex = tableEditRecycle.fnGetData( clickedRow );

var zeroTriesValue = $(this).closest("tbody")[0].rows[0].cells[1];
var resetedValue = $(this).closest("tr")[0].cells[1];


if(clickedIndex[4] != 0){
    
    $.ajax({
            type: "POST",
            url: "_reciclagem-requests.php",
            data: { action: "EditRecycleResetSingleTries",
                    RecycleID: editedRecycle,
                    Index: clickedIndex[4],
                    CampaignLists: CampaignLists
                    },
            success: function(data) { }
        }); 
        
        zeroTriesValue.innerHTML = parseInt(resetedValue.innerHTML) + parseInt(zeroTriesValue.innerHTML);
        resetedValue.innerHTML = 0;
}

}

function EditRecycleResetFeedback()
{
	var totalTries = 0;

	$.each($("#recycle-details tbody tr"), function(){
		var currentCell = $(this)[0].cells[1];
		totalTries += parseInt(currentCell.innerHTML)
		currentCell.innerHTML = 0;
	})
	$("#recycle-details tbody")[0].rows[0].cells[1].innerHTML = totalTries;

	$.ajax({
        type: "POST",
        url: "_reciclagem-requests.php",
        data: { action: "EditRecycleResetAllTries",
                RecycleID: editedRecycle,
                CampaignLists: CampaignLists
                },
        success: function(data) { }
        }); 


}

// dialog recycle contact details

function TableRecycleContactDetailsInit()
{
    tableRecycleContactDetails = $('#recycle-contact-details').dataTable( {
        "aaSorting": [[0, 'asc']],
        "iDisplayLength": 10,
        "sDom": '<"top"f><"dt-fixed-10lines-with-icon"rt><"bottom"p>',
        "bSortClasses": false,
        "bJQueryUI": true,  
        "bProcessing": true, 
        "bRetrieve": false,
        "bDestroy": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": '_reciclagem-requests.php',
        "fnServerParams": function (aoData)  
        {
            aoData.push( 
                        { "name": "action", "value": "TableRecycleContactDetailsInit" },
                        { "name": "RecycleID", "value": editedRecycle },
                        { "name": "TryType", "value": editedTryType },
                        { "name": "CampaignLists", "value": CampaignLists }
                       )
        },
        "aoColumnDefs": [
                        
                    ],
        "aoColumns": [ 
                        { "sTitle": "Nome", "sClass":"css-dt-column-align-left" },
                        { "sTitle": "Telefone", "sWidth": "16px", "sClass":"css-dt-column-align-center" }, 
                        { "sTitle": "Total de Chamadas", "sWidth": "16px", "sClass":"css-dt-column-align-center" },
                        { "sTitle": "Última Chamada", "sWidth": "125px", "sClass":"css-dt-column-align-center" },
                        { "sTitle": "Reset", "sWidth": "16px", "sClass":"css-dt-column-align-center" },
                        { "sTitle": "Retirar da Reciclagem", "sWidth": "16px", "sClass":"css-dt-column-align-center" }
                     ], 
        "fnDrawCallback":   function()
                            { 
                            if(typeof $("#div-recycle-contact-details-title").html() == 'undefined') $("#recycle-contact-details_wrapper .top").append("<div class='css-dt-title' id='div-recycle-contact-details-title'>Lista de Contactos<span class='css-orange-caret'>></span>Detalhes dos Contactos</div>"); 
                            },
        "oLanguage": { "sUrl": "extras/datatables/language.txt" }
        });
    
    
}

function DialogEditRecycleContactDetailsOnOpen()
{
	
	
	$("button").blur();
	$("#div-recycle-contact-details-container").hide(); 
	
	switch(editedTryType)
	{
		case "N": $("#span-dialog-title-edit-recycle-try").html("Não Chamado"); $("#btn-edit-recycle-contact-details-reset-all").addClass("ui-state-disabled").attr("disabled", "disabled"); break;
		case "Y": $("#span-dialog-title-edit-recycle-try").html("Chamado"); $("#btn-edit-recycle-contact-details-reset-all").removeClass("ui-state-disabled").removeAttr("disabled", "disabled"); break;
		default: var TempSplit = editedTryType.split("Y"); $("#span-dialog-title-edit-recycle-try").html(TempSplit[1] + "ª Reciclagem"); $("#btn-edit-recycle-contact-details-reset-all").removeClass("ui-state-disabled").removeAttr("disabled", "disabled"); break;
	}
	
	
	
	
	if(tableRecycleContactDetails === undefined)
	{

		TableRecycleContactDetailsInit()
		$("#div-recycle-contact-details-container").fadeIn(200);  
	}
	else
	{

		tableRecycleContactDetails.fnReloadAjax();
		$("#div-recycle-contact-details-container").fadeIn(200);  
	}	
	
}

function EditRecycleContactDetailsResetAll()
{
	tableRecycleContactDetails.fnClearTable();
	
	 $.ajax({
            type: "POST",
            url: "_reciclagem-requests.php",
            data: { action: "EditRecycleContactDetailsResetAll",
                    RecycleID: editedRecycle,
                    TryType: editedTryType,
                    CampaignLists: CampaignLists
                    },
            success: function(data) { }
        }); 
	
	tableEditRecycle.fnReloadAjax();
	

	
}

function EditRecycleContactDetailsDisableAll()
{
	
	
		 $.ajax({
            type: "POST",
            url: "_reciclagem-requests.php",
			dataType: "JSON",
            data: { action: "EditRecycleContactDetailsDisableAll",
                    RecycleID: editedRecycle,
                    TryType: editedTryType,
                    CampaignLists: CampaignLists
                    },
            success: function(data) {
				
				tableRecycleContactDetails.fnClearTable();
				tableEditRecycle.fnReloadAjax();
				
				
				var TempValue = $("tr[recycle-id='"+editedRecycle+"']").find("#td-to-recycle").html();
				
				TempValue = TempValue - data.affected_rows;
				
				$("tr[recycle-id='"+editedRecycle+"']").find("#td-to-recycle").html(TempValue);
				
				

				
				}
        }); 
	
}

function EditRecycleContactDetailsResetSingle()
{
	tableRecycleContactDetails.fnDeleteRow($(this).closest("tr")[0]._DT_RowIndex)
	
	
	 $.ajax({
            type: "POST",
            url: "_reciclagem-requests.php",
			dataType: "JSON",
            data: { action: "EditRecycleContactDetailsResetSingle",
                    LeadID: $(this).attr("lead-id")
                    },
            success: function(data) { tableEditRecycle.fnReloadAjax();
			}
        }); 
	
	
}

function EditRecycleContactDetailsDisableSingle()
{
	tableRecycleContactDetails.fnDeleteRow($(this).closest("tr")[0]._DT_RowIndex)
	
	
	 $.ajax({
            type: "POST",
            url: "_reciclagem-requests.php",
			dataType: "JSON",
            data: { action: "EditRecycleContactDetailsDisableSingle",
                    LeadID: $(this).attr("lead-id")
                    },
            success: function(data) { tableEditRecycle.fnReloadAjax();
			
			
			var TempValue = $("tr[recycle-id='"+editedRecycle+"']").find("#td-to-recycle").html();
				
				TempValue = TempValue - 1;
				
				$("tr[recycle-id='"+editedRecycle+"']").find("#td-to-recycle").html(TempValue);
			
			
			
			
			}
        }); 
	
	
}

$("body")
.on("click", ".recycle-active-inactive", RecycleActiveSwitch)
.on("click", ".edit-recycle", {dialog: "#dialog-edit-recycle"}, DialogOpen)
.on("click", ".recycle-details-reset-single", EditRecycleResetSingle)
.on("click", ".recycle-details-view-contacts", {dialog: "#dialog-edit-recycle-contact-details" }, DialogOpen)
.on("click", "#btn-edit-recycle-contact-details-reset-all", EditRecycleContactDetailsResetAll)
.on("click", "#btn-edit-recycle-contact-details-disable-all", EditRecycleContactDetailsDisableAll)
.on("click", "#btn-recycle-reset-feedback", EditRecycleResetFeedback )
.on("click", ".recycle-contact-details-single-reset", EditRecycleContactDetailsResetSingle)
.on("click", ".recycle-contact-details-single-disable", EditRecycleContactDetailsDisableSingle)
.on("click", "#btn-new-recycle", NewRecycle)
.on("click", "#select-new-recycle", ClearRecycleErrorMsg)
.on("click", "#btn-recycle-apply-to-all-campaigns", {dialog: "#dialog-confirm-recycle-apply-to-all-campaigns"}, DialogOpen )