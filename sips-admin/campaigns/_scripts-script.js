function ScriptListBuilder()
{
$(".span-active-campaign-name").html($("#campaign-name").val()) 	

	
	$.ajax({
            type: "POST",
            url: "_scripts-requests.php",
			dataType: "JSON",
            data: { action: "ScriptListBuilder",
					CampaignID: CampaignID,
                    AllowedCampaigns: AllowedCampaigns
                    },
            success: function(data) 
			{

				if(data)
				{
					if(data.current_script_fields > 0)
					{
						hasScript = true;
						$("#span-current-script").css("color", "green").html("<b>Com Script associado.</b>");
						
						$(".img-script-add").attr("src", "icons/mono_into_16.png");
						$(".td-script-add").html("Substituir");
						$("#td-new-script-img").fadeIn(200);
						$("#td-new-script-text").fadeIn(200);
						
					}
					else
					{
						hasScript = false;
						$("#span-current-script").css("color", "rgb(240, 5, 5)").html("<b>Nenhum Script associado.</b>");
						$(".img-script-add").attr("src", "icons/mono_plus_16.png");
						$(".td-script-add").html("Associar");
						$("#td-new-script-img").fadeIn(200);
						$("#td-new-script-text").fadeIn(200);
						//return false;
					}
					
					$("#tbl-scripts").empty();
					$.each(data.campaign_id, function(index, value){
					if(data.campaign_id[index] != CampaignID)
					{
					
					$("#tbl-scripts").append("\n\
                    <tr class='tr-script-rows' campaign-id='"+ data.campaign_id[index] + "' style='height:22px;'>\n\
                        <td class='css-td-list-check odd-even-ignore'><input class='radio-scripts' type='radio' name='radio-scripts' id='"+data.campaign_id[index]+"'></td>\n\
                        <td class='css-td-list-label'><label class='' for='"+ data.campaign_id[index] + "'>"+data.campaign_name[index]+"</label></td>\n\
                        <td class='css-td-list-icon'><img class='css-img-list-icon' title='Nº de Campos' src='icons/mono_notepad_16.png'></td>\n\
                        <td class='css-td-list-text'>"+data.num_fields[index]+"</td>\n\
                        <td class='css-td-list-actions'><img class='css-img-list-actions' title='Configurar' src='icons/mono_wrench_16.png'></td>\n\
                    </tr>\n\
                    ")
					}
					})
					
					$(".radio-scripts").uniform();
					OddEvenRefresh("", "tbl-scripts");
					
					
					
				}
				else
				{}
				
				
				
				
                
			}
	});
	
	
}

function ScriptElemInit()
{
	$("#btn-new-script").button();
	$("#btn-script-remove").button();
	
	
	$("#dialog-confirm-copy-script").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_alert_16.png'></td><td><span class='dialog-title'>Alerta!</span></td></tr></table>",
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { "Sim" : SubmitCopyScript, 
    "Não": DialogClose }
	}); 
	
	
	$("#dialog-confirm-remove-script").dialog({ 
    title: "<table><tr><td><img class='dialog-icon-title' src='icons/mono_alert_16.png'></td><td><span class='dialog-title'>Alerta!</span></td></tr></table>",
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { "Sim" : SubmitRemoveScript, 
    "Não": DialogClose }
	}); 
	
	
}

function ErrorMsgClear()
{
	$("#td-new-script-error").html("")
}


function CopyScript()
{
	ErrorMsgClear()
	ScriptToCopy = $(".radio-scripts:checked").attr("id");
	
	if(typeof ScriptToCopy == "undefined")
	{
		$("#td-new-script-error").html("<b>Nenhum Script escolhido para ser copiado. Por favor escolha um Script.</b>").focus(); 
	}
	else
	{
	
		if(hasScript)
		{
			$("#dialog-confirm-copy-script").dialog("open");
		}
		else
		{
			SubmitCopyScript();
		}

	}
	


}

function SubmitCopyScript()
{
	$("#dialog-confirm-copy-script").dialog("close")
	$.ajax({
            type: "POST",
            url: "_scripts-requests.php",
			dataType: "JSON",
            data: { action: "SubmitCopyScript",
                    CampaignID: CampaignID, 
                    ScriptToCopy: ScriptToCopy 
                    },
            success: function(data) {
			
			hasScript = true;	
			$("#span-current-script").css("color", "green").html("<b>Com Script associado.</b>");
			$("#td-new-script-error").html("Script associado com Sucesso.")
			$(".img-script-add").attr("src", "icons/mono_into_16.png")
			$(".td-script-add").html("Substituir")
			}
		});
}

function RemoveScript()
{ 	ErrorMsgClear()
	if(hasScript){
		$("#dialog-confirm-remove-script").dialog("open")
	}
}

function SubmitRemoveScript()
{
	$("#dialog-confirm-remove-script").dialog("close")
	$.ajax({
            type: "POST",
            url: "_scripts-requests.php",
            data: { action: "SubmitRemoveScript",
                    CampaignID: CampaignID
                    },
            success: function(data) {
			
			hasScript = false;
			
			
			$("#span-current-script").css("color", "rgb(240, 5, 5)").html("<b>Nenhum Script associado.</b>");
			$(".img-script-add").attr("src", "icons/mono_plus_16.png")
			$(".td-script-add").html("Associar")
			
			
			}
            });
}

$("body")
.on("click", "#btn-new-script", CopyScript)
.on("click", ".radio-scripts", 	ErrorMsgClear)
.on("click", "#btn-script-remove", RemoveScript)
