function PauseListBuilder(Flag) 
{

if(typeof Flag == 'object'){ Flag = Flag.data.Flag; }

PauseElemInit();	
	
$(".span-active-campaign-name").html($("#campaign-name").val()) 	
	
if( ($("#tbl-pauses tr").length == 0 && Flag == "ALL") || (Flag == "REFRESH") || (Flag == "DISABLED") || (Flag == "NEW") || ($("#tbl-pauses tr").length == 0 && Flag == "EDIT"))
{
    if(Flag != "NEW") { $("#tbl-pauses").hide(); }
    

    $.ajax({
        type: "POST",
        url: "_pausas-requests.php",
        dataType: "JSON",
        data: { action: "PauseListBuilder",
                CampaignID: CampaignID,
                AllowedCampaigns: AllowedCampaigns,
                PauseName: $.trim($("#input-new-pause-name").val()),
                PauseTime: $('#spinner-new-pause-time').val(),
                Flag: Flag
                },
        success: function(data) 
        {
			
			
			
            if(Flag == 'ALL' || Flag == "REFRESH" || Flag == "DISABLED" || Flag == "EDIT"){ $('#tbl-pauses').empty(); }
            if(data){
                
                var OddEven;
                var Checked;
				var Billable;
                
                if($( "#span-no-pauses" ).length > 0){ $( "#span-no-pauses" ).html(""); }
        
                
                $.each(data.pause_code, function(index, value){
                
                if(Flag == "EDIT" || Flag == "REFRESH")
                {
                    if(data.active[index] == 1)
                    {
                        Checked = "checked='checked'";
                    }
                    else
                    {
                        Checked = "";
                    }
					
					
					if(data.billable[index] == "YES")
					{
						Billable = "checked='checked'";
					} 
					else 
					{
						Billable = "";
					
					}
					
                }
                    
                if(Flag == "DISABLED")
                { 
                    var PauseCheckBox = ""; 
                    var PauseConfig = ""; 
                    var PauseEnable = "<td width='16px' class='td-icon'><img class='mono-icon edit-pause-activate' to-enable='"+ data.pause_code[index] + "' title='Activar' style='cursor:pointer;' src='icons/mono_plus_16.png'></td>"; 
                	var PauseBillable = "";
				} 
                else
                {
                    var PauseCheckBox = "<td class='odd-even-ignore' style='width:10px'><input "+Checked+" class='pause-active-inactive pause-checkbox' type='checkbox' value='"+ data.pause_code[index] + "' name='"+ data.pause_code[index] + "' id='"+ data.pause_code[index] + "'></td>";
                    var PauseConfig = "<td width='32px' style='text-align:center' class=''><img style='float:none; cursor:pointer' pause-to-edit='"+ data.pause_code[index] + "' class='mono-icon edit-pause' title='Configurar' style='cursor:pointer;' src='icons/mono_wrench_16.png'></td>";
                    var PauseEnable = "<td width='16px' class='td-icon'><img class='mono-icon edit-pause-deactivate' to-remove='"+ data.pause_code[index] + "' title='Desactivar' style='cursor:pointer;' src='icons/mono_trash_16.png'></td>";
                
					var PauseBillable = "<td class='odd-even-ignore' width='20px'><input "+Billable+" class='pause-billable' type='checkbox'></td><td width='30px'><img class='mono-icon' style='float:left !important' title='Pausa Paga' src='icons/mono_icon_euro_16.png'></td>";
				
				}
            
                            
                    $("#tbl-pauses").prepend("<tr class='tr-pause-rows' pause-id='"+ data.pause_code[index] + "' style='height:22px;'>"+PauseCheckBox+"<td style='padding:1px 0px 0px 3px; text-align:left;'><label class='label-pause-name' for='" + data.pause_code[index] + "'>"+ data.pause_code_name[index] + "</label></td>" + PauseBillable + "<td class='td-icon'><img class='mono-icon' style='margin-top:-3px' title='Tempo de Pausa' src='icons/mono_cup_16.png'></td><td width='24px' style='text-align:left; padding:1px 0px 0px 3px;'><span><span id='span-pause-time-"+ data.pause_code[index] + "'>"+ data.max_time[index] + "</span></span>m</td><td width='24px'></td>"+PauseEnable+PauseConfig+"\n\</tr>")
                });


				$(".pause-billable").uniform(); 	
                $(".pause-checkbox").uniform(); 
                OddEvenRefresh("", "tbl-pauses");
                $("#input-new-pause-name").val("");
                $("#spinner-new-pause-time").val("0");
                
            }
            else
            {
                if(Flag == "NEW"){
                    $("#td-new-pause-error").html("<b>Já existe uma pausa com esse nome. Por favor altere o nome, e tente novamente.<b>");
                    $("#input-new-pause-name").css("border-color", "red");
                }
                else
                {
                    if(Flag == "ALL" || Flag == "EDIT"){ $('#span-no-pauses').html("Não existem Pausas em sistema."); }
                    if(Flag == "DISABLED") { $('#span-no-pauses').html("Não existem Pausas inactivas em sistema."); }
                    
                }
            }

        if(Flag != "NEW") { $("#tbl-pauses").fadeIn(200); }         
        }
    });
}
}

function PauseElemInit()
{
	$("#btn-select-all-pauses").button();
		$("#btn-pauses-active-inactive-switch").button();
			$("#btn-select-no-pauses").button();
			$("#btn-edit-pause-all-campaigns").button();
			$("#btn-edit-pause-no-campaigns").button();
			$("#btn-new-pause").button();
			$("#btn-pause-apply-to-all-campaigns").button();
			
		$("#dialog-edit-pause").dialog({ 
		title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_wrench_16.png'></td><td><span class='dialog-title'>Configuração de Pausas</span></td></tr></table>",
		autoOpen: false,
		height: 525,
		width: 550,
		resizable: false,
		buttons: 	{ 	"Gravar" : DialogEditPauseOnSave,
						"Fechar" : DialogClose                    
					},
		open: DialogEditPauseOnOpen
	});
	
	$("#dialog-confirm-pause-apply-to-all-campaigns").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_alert_16.png'></td><td><span class='dialog-title'>Alerta!</span></td></tr></table>",
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { 	"OK" : DialogConfirmApplyPausesOnSave , 
    			"Cancelar": DialogClose  }
	}); 
	
	
	
	$('#spinner-new-pause-time').spinner({
    min: 0,
    max: 120,
    step: 5
});

$('#spinner-pause-time-edit').spinner({
    min: 0,
    max: 120,
    step: 5
});

	
	
}

function PauseActiveSwitch()
{
	var Checked;
    var NumPauses;
    if($(this).parent().hasClass("checked"))
    {
        Checked = 1;
        NumPauses = 1;
    }
    else 
    {
        Checked = 0;
        NumPauses = -1;
    }
    $.ajax({
            type: "POST",
            url: "_pausas-requests.php",
            data: 
            { 
                action: "PauseActiveInactive",
                CampaignID: CampaignID,
                PauseID: $(this).attr("id"),
                PauseStatus: Checked,
                NumPauses: NumPauses
            },
            success: function(data) {}
    });

}

function HidePause()
{
    var toRemove = $(this).attr("to-remove");   
    $.ajax({
            type: "POST",
            url: "_pausas-requests.php",
            data: { action: "HidePause",
                    PauseID: toRemove
            },
            success: function(data) {
                $("tr[pause-id='"+toRemove+"']").remove()
                OddEvenRefresh("REMOVE", "tbl-pauses");
            }
    });
}

function SelectAllPauses()
{
	console.log("go")
	var Counter = 0;
    $.each($(".pause-active-inactive"), function(){
        $(this).parent().addClass("checked");
        Counter++;
    })
    $.ajax({
            type: "POST",
            url: "_pausas-requests.php",
            data: { action: "SelectAllPauses",
                    CampaignID: CampaignID,
                    TotalPauses: Counter
            },
            success: function(data) {
                
            }
    });
}

function ActiveInactivePauseSwitch()
{
	
	var obj = $(this);
	
	if(obj.hasClass("pause-hidden-switch"))
	{
		PauseListBuilder("DISABLED");
		obj.removeClass("pause-hidden-switch").addClass("pause-visible-switch").children().children().attr("src", "icons/mono_connected_16.png");
	
		$("#btn-select-all-pauses").addClass("ui-state-disabled").attr("disabled", "disabled");
		$("#btn-select-no-pauses").addClass("ui-state-disabled").attr("disabled", "disabled");
		$("#span-pauses-active-inactive").html("activas")
	
	}
	else 
	{
		PauseListBuilder("REFRESH");
		obj.removeClass("pause-visible-switch").addClass("pause-hidden-switch").children().children().attr("src", "icons/mono_notconnected_16.png");
		
		$("#btn-select-all-pauses").removeClass("ui-state-disabled").removeAttr("disabled");
		$("#btn-select-no-pauses").removeClass("ui-state-disabled").removeAttr("disabled");
		$("#span-pauses-active-inactive").html("inactivas")

	}
	
}

function UncheckAllPauses()
{
	$.each($(".pause-active-inactive"), function(){
	$(this).parent().removeClass("checked");
    })
    $.ajax({
            type: "POST",
            url: "_pausas-requests.php",
            data: { action: "DeactivateAllPauses",
                    CampaignID: CampaignID
            },
            success: function(data) {
                
            }
    });

}

function ReactivatePause()
{
	var toEnable = $(this).attr("to-enable");   
    $.ajax({
            type: "POST",
            url: "_pausas-requests.php",
            data: { action: "ReactivatePause",
                    PauseID: toEnable
            },
            success: function(data) {
                $("tr[pause-id='"+toEnable+"']").remove()
                OddEvenRefresh("REMOVE", "tbl-pauses");
            }
    });
}

function DialogEditPauseOnOpen()
{

	$("#spinner-pause-time-edit").val($("#span-pause-time-"+editedPause).html())
            $("#input-edit-pause-name").val($("label[for='"+editedPause+"']").html());
            
            $("#table-edit-pause-campaigns-1").hide();
            $("#table-edit-pause-campaigns-2").hide();
            
             
            $.ajax({
                    type: "POST",
                    url: "_pausas-requests.php",
                    dataType: "JSON",
                    data: { action: "GetPauseEditCampaigns",
                            AllowedCampaigns: AllowedCampaigns, 
                            PauseID: editedPause     
                            },
                    success: function(data) {
                        
                        alteredPauses = {};
                        var checked_code;
                        var BoldCurrent;
                        
                        $("#table-edit-pause-campaigns-1").empty();
                        $("#table-edit-pause-campaigns-2").empty();
                        
                        $.each(data.c_id, function(index, value){
                            
                            alteredPauses[data.c_id[index]] = data.active[index];
                            
                            if(data.active[index] == "1"){ checked_code = "checked='checked'";} else { checked_code = "";}
                            if(data.c_id[index] == CampaignID) { BoldCurrent = "font-weight:bold; font-size:13px"; } else { BoldCurrent = "";}  
                            
                            if( ((index/2) % 1) != 0 )
                            {
                                $("#table-edit-pause-campaigns-2").append("<tr><td><input class='checkbox-edit-pause-campaigns' "+checked_code+" type='checkbox' value='"+data.c_id[index]+"' name='"+data.c_id[index]+"' id='"+data.c_id[index]+"'><label style='display:inline; "+BoldCurrent+"' for='"+data.c_id[index]+"'>"+data.c_name[index]+"</label></td></tr>")
                            }
                            else
                            {
                                $("#table-edit-pause-campaigns-1").append("<tr><td><input class='checkbox-edit-pause-campaigns' "+checked_code+" type='checkbox' value='"+data.c_id[index]+"' name='"+data.c_id[index]+"' id='"+data.c_id[index]+"'><label style='display:inline; "+BoldCurrent+"' for='"+data.c_id[index]+"'>"+data.c_name[index]+"</label></td></tr>")
                            }

                        })
                                 
                        $(".checkbox-edit-pause-campaigns").uniform();
                        
                        $("#table-edit-pause-campaigns-1").fadeIn(200);
                        $("#table-edit-pause-campaigns-2").fadeIn(200);
                        
                        
                        }
                });
	
}

function DialogEditPauseOnSave()
{
	
	                var editedPause_save_flag = true;
                    $.each($(".label-pause-name"), function(){
                        if(editedPause != $(this).attr("for"))
                        {
                            if($(this).html() == $("#input-edit-pause-name").val()){ editedPause_save_flag = false;}
                        }
                                
                        
    
                        })
                
                    if(editedPause_save_flag)
                    {
                        
                        $(this).dialog("close"); 
                     
                     
                    var edited_campaigns_id = new Array();
                    var edited_campaigns_active = new Array();
                    
                    
                    $.each($(".checkbox-edit-pause-campaigns"), function(){
                    
                    edited_campaigns_id.push($(this).attr("name"));
                    
                    if($(this).parent().hasClass("checked"))
                    {
                        if($(this).attr("name") == CampaignID) { $("input[name='"+editedPause+"']").parent().addClass("checked")}
                        edited_campaigns_active.push(1);
                    }
                    else
                    {
                        if($(this).attr("name") == CampaignID) { $("input[name='"+editedPause+"']").parent().removeClass("checked")}
                        edited_campaigns_active.push(0);
                    }
                    
                    });
                    
                    
                    
                    $.each(edited_campaigns_id, function(index, value){

                        if(alteredPauses[value] != edited_campaigns_active[index] && edited_campaigns_active[index] == 1)
                        {
                            alteredPauses[value] = 1;
                        }
                        else if(alteredPauses[value] != edited_campaigns_active[index] && edited_campaigns_active[index] == 0)
                        {
                            alteredPauses[value] = -1;
                        }               
                        else
                        {
                            alteredPauses[value] = 0;
                        }
        
                    
                        })
                        
                    //console.log(alteredPauses);
                    
                    
                    
                     $.ajax({
                        type: "POST",
                        url: "_pausas-requests.php",
                        data: { action: "SaveEditedPause",
                                PauseID: editedPause,
                                PauseName: $("#input-edit-pause-name").val(),
                                PauseTime: $("#spinner-pause-time-edit").val(),
                                EditedCampaignsID: edited_campaigns_id,
                                EditedCampaignsActive: edited_campaigns_active,
                                AlteredPauses: alteredPauses
                                },
                        success: function(data) {
                            
                            
                                                
                    $("#span-pause-time-"+editedPause).html($("#spinner-pause-time-edit").val());
                    $("label[for='"+editedPause+"']").html($("#input-edit-pause-name").val());
                    
                            
                            }
                    });
                        
                    }   

	
}

function EditPauseAllCampaigns()
{
    $(".checkbox-edit-pause-campaigns").attr("checked", "checked").parent().addClass("checked").uniform.update(".checkbox-edit-pause-campaigns");
}

function EditPauseNoCampaigns()
{
    $(".checkbox-edit-pause-campaigns").removeAttr("checked").parent().removeClass("checked").uniform.update(".checkbox-edit-pause-campaigns");
}

function ClearNewPauseErrors()
{
	if($("#td-new-pause-error").html().length > 0)
    {
        $("#td-new-pause-error").empty();
        $("#input-new-pause-name").css("border-color", "#C0C0C0");
    }  
	
}

function DialogConfirmApplyPausesOnSave()
{
    var pause_ids = new Array();
    var pause_names = new Array();
    var pause_active = new Array();
    var pause_time = new Array();  
    
    
    
    $("#tbl-pauses tr td div span").each(function(){
        pause_ids.push($(this).children().attr("id"));
        pause_names.push($("label[for="+$(this).children().attr("id")+"]").html());
        if($(this).hasClass("checked")){pause_active.push(1)} else {pause_active.push(0)}
    });
    
    $("#tbl-pauses tr td span span").each(function(){     pause_time.push($(this).html()) })
    
    
    $.ajax({
            type: "POST",
            url: "_pausas-requests.php",
            data: { action: "ApplyPausesToAllCampaings",
                    PauseIDs: pause_ids, 
                    PauseNames: pause_names,
                    PauseStatus: pause_active,
                    PauseTimes: pause_time
                    },
            success: function(data) {}
        });         
        
        $(this).dialog("close");
}

function PauseBillable(){ 
	var Checked;
   
 
   
    if($(this).parent().hasClass("checked"))
    {
        Checked = "YES";
    }
    else 
    {
        Checked = "NO";
    }
    $.ajax({
            type: "POST",
            url: "_pausas-requests.php",
            data: 
            { 
                action: "PauseBillable",
                CampaignID: CampaignID,
                PauseID: $(this).closest("tr").attr("pause-id"),
                PauseBillable: Checked,

            },
            success: function(data) {}
    }); 

}

$("body")
.on("click", ".pause-active-inactive", PauseActiveSwitch)
.on("click", ".edit-pause-deactivate", HidePause)
.on("click", "#btn-select-all-pauses", SelectAllPauses)
.on("click", "#btn-pauses-active-inactive-switch", ActiveInactivePauseSwitch)
.on("click", "#btn-select-no-pauses", UncheckAllPauses)
.on("click", ".edit-pause-activate", ReactivatePause)
.on("click", ".edit-pause", {dialog: "#dialog-edit-pause" } , DialogOpen)
.on("click", "#btn-edit-pause-all-campaigns", EditPauseAllCampaigns)
.on("click", "#btn-edit-pause-no-campaigns", EditPauseNoCampaigns)
.on("click", "#btn-new-pause", {Flag: "NEW" }, PauseListBuilder)
.on("input", "#input-new-pause-name", ClearNewPauseErrors)
.on("click", "#btn-pause-apply-to-all-campaigns", {dialog: "#dialog-confirm-pause-apply-to-all-campaigns" }, DialogOpen)
.on("click", ".pause-billable", PauseBillable)

