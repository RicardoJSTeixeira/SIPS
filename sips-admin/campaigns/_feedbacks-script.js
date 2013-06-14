function FeedListBuilder(Flag)
{

FeedElemInit()
	
$(".span-active-campaign-name").html($("#campaign-name").val()) 
	
	
if( ($("#tbl-feeds tr").length == 0 && Flag == "ALL") || (Flag == "REFRESH") || (Flag == "DISABLED") || (Flag == "NEW") || ($("#tbl-feeds tr").length == 0 && Flag == "EDIT" ))
{
    if(Flag != "NEW") { $("#tbl-feeds").hide(); }
    
    $.ajax({
        type: "POST",
        url: "_feedbacks-requests.php",
        dataType: "JSON",
        data: { action: "FeedListBuilder",
                CampaignID: CampaignID,
                AllowedCampaigns: AllowedCampaigns,
                FeedName: $.trim($("#input-new-feed-name").val()),
                Human: function() { if($("#input-new-feed-human").parent().hasClass("checked")){ return "Y";} else { return "N"; } },
                Callback: function() { if($("#input-new-feed-callback").parent().hasClass("checked")){ return "Y";} else { return "N"; } },
                Flag: Flag
                },
        success: function(data) 
        {
            if(Flag == 'ALL' || Flag == "REFRESH" || Flag == "DISABLED" || Flag == "EDIT"){ $('#tbl-feeds').empty(); }
			
            if(data !== null){   
                var OddEven;
                var Checked;
                var human_checked;
                var callback_checked;
                
                if($( "#span-no-feeds" ).length > 0){ $( "#span-no-feeds" ).html(""); }
        
                
                $.each(data.status, function(index, value){
                    
                if(Flag == "EDIT")
                {
                    if(data.selectable[index] == "Y")
                    {
                        Checked = "checked='checked'";
                    }
                    else
                    {
                        Checked = "";
                    }
                }
                    
                if(data.human[index] == "Y"){ human_checked = "checked"; }  else { human_checked = ""; }
                if(data.callback[index] == "Y"){ callback_checked = "checked"; } else { callback_checked = ""; }
                    
                    
                if(Flag == "DISABLED")
                { 
                    var FeedCheckBox = ""; 
                    var FeedConfig = ""; 
                    var FeedEnable = "<td width='32px' style='text-align:center !important'><img class='mono-icon edit-feed-activate' to-enable='"+ data.status[index] + "' title='Activar' style='cursor:pointer; float:none' src='icons/mono_plus_16.png'></td>"; 
                } 
                else
                {
                    var FeedCheckBox = "<td class='odd-even-ignore' style='width:10px'><input "+Checked+" class='feed-active-inactive  feed-checkbox' type='checkbox' value='"+ data.status[index] + "' name='"+ data.status[index] + "' id='"+ data.status[index] + "'></td>";
                    var FeedConfig = "<td class='css-td-list-actions'><img feed-to-edit='"+ data.status[index] + "' class='css-img-list-actions edit-feed' title='Configurar' src='icons/mono_wrench_16.png'></td>";
                    var FeedEnable = "<td width='32px' style='text-align:center !important'><img class='css-img-list-actions edit-feed-deactivate' to-remove='"+ data.status[index] + "' title='Desactivar' src='icons/mono_trash_16.png'></td>";
                }
            
    
                    $("#tbl-feeds").prepend("\n\
                    <tr class='tr-feed-rows' feed-id='"+ data.status[index] + "' style='height:22px;'>"+FeedCheckBox+"<td style='padding:1px 0px 0px 3px; text-align:left;'><label class='label-feed-name' for='"+ data.status[index] + "'>"+ $.trim(data.status_name[index]) + "</label></td><td class='odd-even-ignore' width='20px'><input class='input-feed-human feed-checkbox' type='checkbox' "+human_checked+" human='"+data.status[index]+"'></td><td width='22px'><img class='mono-icon' title='Resposta Humana' src='icons/mono_speech_dual_16.png'></td><td width='12px'></td><td class='odd-even-ignore' width='20px'><input class='input-feed-callback feed-checkbox' type='checkbox' "+callback_checked+" callback='"+data.status[index]+"'></td><td width='22px'><img class='mono-icon' title='Callback' src='icons/mono_phone_inverse_16.png'></td><td width='32px'></td>"+FeedEnable+FeedConfig+"</tr>")
                
                })
                $(".feed-checkbox").uniform();
                OddEvenRefresh("", "tbl-feeds");
                $("#input-new-feed-name").val("");
                $("#input-new-feed-human").parent().addClass("checked");
                $("#input-new-feed-callback").parent().removeClass("checked");
                
            }
            else
            {
                if(Flag == "NEW"){
                    $("#td-new-feed-error").html("<b>Já existe um Feedback com esse nome. Por favor altere o nome, e tente novamente.<b>");
                    $("#input-new-feed-name").css("border-color", "red");
                }
                else
                {
                    if(Flag == "ALL" || Flag == "EDIT" || Flag == "REFRESH"){ $('#span-no-feeds').html("Não existem Feedbacks disponiveis em sistema."); }
                    if(Flag == "DISABLED") { $('#span-no-feeds').html("Não existem Feedbacks inactivos em sistema."); }
                    
                }
            }

        if(Flag != "NEW") { $("#tbl-feeds").fadeIn(200); }          
        }
    });
}
}

function FeedElemInit()
{
	
	$("#btn-select-all-feeds").button();
		$("#btn-select-no-feeds").button();
				$("#btn-feeds-active-inactive-switch").button();
				
				
				$("#btn-edit-feed-all-campaigns").button();
				$("#btn-edit-feed-no-campaigns").button();
				
				$("#btn-new-feed").button();
				$("#btn-feed-apply-to-all-campaigns").button();
	
	
	$("#dialog-edit-feed").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_wrench_16.png'></td><td><span class='dialog-title'>Configuração de Feedbacks</span></td></tr></table>",
    autoOpen: false,
    height: 525,
    width: 550,
    resizable: false,
    buttons: { "Gravar" : DialogEditFeedOnSave,
                "Fechar" : DialogClose
            },
    open: DialogEditFeedOnOpen
}); 

$("#dialog-confirm-feed-apply-to-all-campaigns").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alerta!</span> ',
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { "OK" : DialogConfirmFeedApplyToAllCampaingsOnSave, 
    "Cancelar": DialogClose }
}); 


	
	
}

function SelectAllFeeds()
{
	var affectedRows = 0;
	$.each($(".feed-active-inactive"), function(){
		$(this).parent().addClass("checked");
		affectedRows++;
    })
    $.ajax({
            type: "POST",
            url: "_feedbacks-requests.php",
            data: { action: "ActivateAllFeeds",
                    CampaignID: CampaignID,
					affectedRows: affectedRows
            },
            success: function(data) {
                
            }
    });

	
}

function SelectNoFeeds()
{
	var affectedRows = 0;	
	$.each($(".feed-active-inactive"), function(){
		$(this).parent().removeClass("checked");
		affectedRows++;
    })
    $.ajax({
            type: "POST",
            url: "_feedbacks-requests.php",
            data: { action: "DeactivateAllFeeds",
                    CampaignID: CampaignID,
					affectedRows: affectedRows
            },
            success: function(data) {
                
            }
    });
}

function ActiveInactiveFeedSwitch()
{
	var obj = $(this);
	
	if(obj.hasClass("feed-hidden-switch"))
	{
		FeedListBuilder("DISABLED");
		obj.removeClass("feed-hidden-switch").addClass("feed-visible-switch").children().children().attr("src", "icons/mono_connected_16.png");
	
		$("#btn-select-all-feeds").addClass("ui-state-disabled").attr("disabled", "disabled");
		$("#btn-select-no-feeds").addClass("ui-state-disabled").attr("disabled", "disabled");
		$("#span-feeds-active-inactive").html("activos")
	
	}
	else 
	{
		FeedListBuilder("REFRESH");
		obj.removeClass("feed-visible-switch").addClass("feed-hidden-switch").children().children().attr("src", "icons/mono_notconnected_16.png");
		
		$("#btn-select-all-feeds").removeClass("ui-state-disabled").removeAttr("disabled");
		$("#btn-select-no-feeds").removeClass("ui-state-disabled").removeAttr("disabled");
		$("#span-feeds-active-inactive").html("inactivos")

	}

}

function FeedSwitch()
{
	var Checked;
    var NumFeeds;
    if($(this).parent().hasClass("checked"))
    {
        Checked = "Y";
        NumFeeds = 1;
    }
    else 
    {
        Checked = "N";
        NumFeeds = -1;
    }

    $.ajax({
            type: "POST",
            url: "_feedbacks-requests.php",
            data: 
            { 
                action: "FeedActiveInactive",
                CampaignID: CampaignID,
                FeedID: $(this).attr("id"),
                FeedStatus: Checked,
                NumFeeds: NumFeeds
            },
            success: function(data) {}
    });

}

function HumanSwitch()
{
	var Checked;
    if(typeof $(this).attr("checked") == 'undefined')
    {
        Checked = "N";
    }
    else 
    {
        Checked = "Y";
    }

    $.ajax({
            type: "POST",
            url: "_feedbacks-requests.php",
            data: 
            { 
                action: "HumanActiveInactive",
                CampaignID: CampaignID,
                FeedID: $(this).attr("human"),
                FeedStatus: Checked
            },
            success: function(data) {}
    });

}

function CallbackSwitch()
{
	var Checked;
    if(typeof $(this).attr("checked") == 'undefined')
    {
        Checked = "N";
    }
    else 
    {
        Checked = "Y";
    }

    $.ajax({
            type: "POST",
            url: "_feedbacks-requests.php",
            data: 
            { 
                action: "CallbackActiveInactive",
                CampaignID: CampaignID,
                FeedID : $(this).attr("callback"),
                FeedStatus: Checked
            },
            success: function(data) {}
    });

}

function DeactivateFeed()
{
	var toRemove = $(this).attr("to-remove");   
    $.ajax({
            type: "POST",
            url: "_feedbacks-requests.php",
            data: { action: "HideFeed",
                    FeedID: toRemove
            },
            success: function(data) {
                $("tr[feed-id='"+toRemove+"']").remove()
                OddEvenRefresh("REMOVE", "tbl-feeds");
            }
    });

}

function ActivateFeed()
{
	var toEnable = $(this).attr("to-enable");   
    $.ajax({
            type: "POST",
            url: "_feedbacks-requests.php",
            data: { action: "ShowFeed",
                    FeedID: toEnable
            },
            success: function(data) {
                $("tr[feed-id='"+toEnable+"']").remove()
                OddEvenRefresh("REMOVE", "tbl-feeds");
            }
    });

}

function DialogEditFeedOnSave()
{
	var editedFeed_save_flag = true;
                    $.each($(".label-feed-name"), function(){
                        if(editedFeed != $(this).attr("for"))
                        {
                            if($(this).html() == $("#input-edit-feed-name").val()){ editedFeed_save_flag = false;}
                        }
                                
                        
    
                        })
                
                    if(editedFeed_save_flag)
                    {
                        
                        $(this).dialog("close"); 
                     
                     
                    var edited_campaigns_id = new Array();
                    var edited_campaigns_active = new Array();
                    
                    
                    $.each($(".checkbox-edit-feed-campaigns"), function(index, value){
                    
                    edited_campaigns_id.push($(this).attr("name"));
                    
                    
                    
                    if($(this).parent().hasClass("checked"))
                    {
                        if($(this).attr("name") == CampaignID) { $("input[name='"+editedFeed+"']").parent().addClass("checked")}
                        edited_campaigns_active.push("Y");
                    }
                    else
                    {
                        if($(this).attr("name") == CampaignID) { $("input[name='"+editedFeed+"']").parent().removeClass("checked")}
                        edited_campaigns_active.push("N");
                    }
                    

                    
                    });
                    
                    
                    
                        $.each(edited_campaigns_id, function(index, value){

                        if(alteredFeeds[value] != edited_campaigns_active[index] && edited_campaigns_active[index] == "Y")
                        {
                            alteredFeeds[value] = 1;
                        }
                        else if(alteredFeeds[value] != edited_campaigns_active[index] && edited_campaigns_active[index] == "N")
                        {
                            alteredFeeds[value] = -1;
                        }               
                        else
                        {
                            alteredFeeds[value] = 0;
                        }
        
                    
                        })
                        
                    //console.log(alteredFeeds);
                    
                    
                     $.ajax({
                        type: "POST",
                        url: "_feedbacks-requests.php",
                        data: { action: "SaveEditedFeed",
                                FeedID: editedFeed,
                                FeedName: $("#input-edit-feed-name").val(),
                                EditedCampaignID: edited_campaigns_id,
                                EditedCampaignActive: edited_campaigns_active,
                                AlteredFeeds: alteredFeeds
                                },
                        success: function(data) {
                            
                            
                                                
                    
                    $("label[for='"+editedFeed+"']").html($("#input-edit-feed-name").val());
                    
                            
                            }
                    });
                        
                    }  
}

function DialogEditFeedOnOpen()
{
	

	 $("#input-edit-feed-name").val($("label[for='"+editedFeed+"']").html());
            
            $("#table-edit-feed-campaigns-1").hide();
            $("#table-edit-feed-campaigns-2").hide();
            
             
            $.ajax({
                    type: "POST",
                    url: "_feedbacks-requests.php",
                    dataType: "JSON",
                    data: { action: "GetFeedEditCampaigns",
                            AllowedCampaigns: AllowedCampaigns, 
                            FeedID: editedFeed
                            },
                    success: function(data) {
                        alteredFeeds = {};
                        var checked_code;
                        var BoldCurrent;
                        
                        $("#table-edit-feed-campaigns-1").empty();
                        $("#table-edit-feed-campaigns-2").empty();
                        
                        $.each(data.c_id, function(index, value){

                            alteredFeeds[data.c_id[index]] = data.selectable[index];
                                                    
                            if(data.selectable[index] == "Y"){ checked_code = "checked='checked'";} else { checked_code = "";}  
                            if(data.c_id[index] == CampaignID){ CurrentBold = "font-weight:bold; font-size:13px";} else { CurrentBold = "";}    
                            
                            if( ((index/2) % 1) != 0 )
                            {
                                $("#table-edit-feed-campaigns-2").append("<tr><td><input class='checkbox-edit-feed-campaigns' "+checked_code+" type='checkbox' value='"+data.c_id[index]+"' name='"+data.c_id[index]+"' id='"+data.c_id[index]+"'><label style='display:inline; "+CurrentBold+"' for='"+data.c_id[index]+"'>"+data.c_name[index]+"</label></td></tr>")
                            }
                            else
                            {
                                $("#table-edit-feed-campaigns-1").append("<tr><td><input class='checkbox-edit-feed-campaigns' "+checked_code+" type='checkbox' value='"+data.c_id[index]+"' name='"+data.c_id[index]+"' id='"+data.c_id[index]+"'><label style='display:inline; "+CurrentBold+"' for='"+data.c_id[index]+"'>"+data.c_name[index]+"</label></td></tr>")
                            }

                        })
                        
                        //console.log(alteredFeeds);
                                 
                        $(".checkbox-edit-feed-campaigns").uniform();
                        
                        $("#table-edit-feed-campaigns-1").fadeIn(200);
                        $("#table-edit-feed-campaigns-2").fadeIn(200);
                        
                        
                        }
                });
	
}

function EditFeedAllCampaigns()
{    
	$.each($(".checkbox-edit-feed-campaigns").parent(), function(){
        $(this).addClass("checked");
    })
}

function EditFeedNoCampaigns()
{
	$.each($(".checkbox-edit-feed-campaigns").parent(), function(){
        $(this).removeClass("checked");
    })
}

function NewFeed()
{
	FeedListBuilder("NEW");
    $("#tbl-recycle").empty();
    $("#select-new-recycle").empty();
}

function ClearNewFeedError()
{
    if($("#td-new-feed-error").html().length > 0)
    {
        $("#td-new-feed-error").empty();
        $("#input-new-feed-name").css("border-color", "#C0C0C0");
    }   	
}

function DialogConfirmFeedApplyToAllCampaingsOnSave()
{
	    var feed_ids = new Array(), feed_names = new Array(), feed_callback = new Array(), feed_ishuman = new Array(), feed_active = new Array();
  
    $(".feed-active-inactive").each(function()
    {
        feed_ids.push($(this).attr("id"));
        feed_names.push($("label[for="+$(this).attr("id")+"]").html());
        if($(this).parent().hasClass("checked"))
        {
            feed_active.push("Y")
        } 
        else 
        {
            feed_active.push("N")
        }
    });
    



    $(".input-feed-human").each(function()
    {
        if($(this).parent().hasClass("checked"))
        {
            feed_ishuman.push("Y")
        }
        else 
        {
            feed_ishuman.push("N")
        }
    })


    $(".input-feed-callback").each(function()
    {
        if($(this).parent().hasClass("checked"))
        {
            feed_callback.push("Y")
        }
        else 
        {
            feed_callback.push("N")
        }
    })

   
    $.ajax({
            type: "POST",
            url: "_feedbacks-requests.php",
            data: { action: "ApplyFeedsToAllCampaings",
                    CampaignID: CampaignID,
                    FeedID: feed_ids, 
                    FeedName: feed_names,
                    FeedActive: feed_active,
                    Callback: feed_callback,
                    Human: feed_ishuman
                    },
            success: function(data) {}
        }); 
    
    
        
        
        $(this).dialog("close"); 
     

}


$("body")
.on("click", "#btn-select-all-feeds", SelectAllFeeds)
.on("click", "#btn-select-no-feeds", SelectNoFeeds)
.on("click", "#btn-feeds-active-inactive-switch", ActiveInactiveFeedSwitch)
.on("click", ".feed-active-inactive", FeedSwitch)
.on("click", ".input-feed-human", HumanSwitch) 
.on("click", ".input-feed-callback", CallbackSwitch)
.on("click", ".edit-feed-deactivate", DeactivateFeed)
.on("click", ".edit-feed-activate", ActivateFeed)
.on("click", ".edit-feed" , {dialog: "#dialog-edit-feed" }, DialogOpen)
.on("click", "#btn-edit-feed-all-campaigns", EditFeedAllCampaigns)
.on("click", "#btn-edit-feed-no-campaigns", EditFeedNoCampaigns)
.on("click", "#btn-new-feed", NewFeed)
.on("input", "#input-new-feed-name", ClearNewFeedError)
.on("click", "#btn-feed-apply-to-all-campaigns", {dialog: "#dialog-confirm-feed-apply-to-all-campaigns"}, DialogOpen)


















