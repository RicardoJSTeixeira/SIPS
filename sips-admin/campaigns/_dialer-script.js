function DialerOptionsBuilder(){
    
    DialerElemInit();
    
    $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "DialerOptionsBuilder",
                CampaignID: CampaignID

            },
            success: function(data){
                
                
                if(data.inbound_queue == "ENABLED") {
                    $("#inbound-queue-no").parent().addClass("checked");
                } else {
                    $("#inbound-queue-yes").parent().addClass("checked");
                }
                
                
                if(data.avail_agents == "Y") {
                    $("#avail-agents-yes").parent().addClass("checked");
                } else {
                    $("#avail-agents-no").parent().addClass("checked");
                }
                
                $("#predictive-adaptive-diff-target").slider("option", "value", data.difftarget)
                
                if(data.dialmethod == "ADAPT_AVERAGE"){
                    $("#method-average").parent().addClass("checked");
                } else {
                    $("#method-limit").parent().addClass("checked");
                }
                
                $("#predictive-drop-percentage").val(data.droppercent);
                
                $("#predictive-max-ratio").val(data.maxadaptiveratio);
                
                $("#predictive-intensity-slider").slider("option", "value", data.adaptiveintensity)
                
                $("#call-timeout-advanced").val(data.dialtimeout)
                
                $("#campaign-callerid").val(data.cid)
            }
        });
        
        
        
        
}

function DialerElemInit(){
    $("#predictive-intensity-slider").slider({
        min: -40,
        max: 40,
        change: function(event, ui){
            if(ui.value == 0){
                $("#predictive-intensity-slider-info").html("Equilibrado");
            }
            
            if(ui.value > 0){
                $("#predictive-intensity-slider-info").html("Mais Intenso: " + ui.value);
            }
            
            if(ui.value < 0){
                $("#predictive-intensity-slider-info").html("Menos Intenso: " + ui.value);
            }
            
            
            $.ajax({
                type: "POST",
                url: "_dialer-requests.php",
                data: 
                { 
                    action: "PredictiveIntensitySwitch",
                    CampaignID: CampaignID,
                    SliderValue: ui.value
                }
        });
            
            
        },
        slide: function(event, ui){
            if(ui.value == 0){
                $("#predictive-intensity-slider-info").html("Equilibrado");
            }
            
            if(ui.value > 0){
                $("#predictive-intensity-slider-info").html("Mais Intenso: " + ui.value);
            }
            
            if(ui.value < 0){
                $("#predictive-intensity-slider-info").html("Menos Intenso: " + ui.value);
            }
            
        }
    });
    
    
    $("#predictive-adaptive-diff-target").slider({
        min: -50,
        max: 50,
        change: function(event, ui){
            if(ui.value == 0){
                $("#predictive-adaptive-diff-target-info").html("Desligado");
            }
            
            if(ui.value > 0){
                $("#predictive-adaptive-diff-target-info").html("Operadores em Espera: " + ui.value);
            }
            
            if(ui.value < 0){
                $("#predictive-adaptive-diff-target-info").html("Chamadas em Espera: " + ui.value);
            }
            
            
            $.ajax({
                type: "POST",
                url: "_dialer-requests.php",
                data: 
                { 
                    action: "PredictiveDiffTarget",
                    CampaignID: CampaignID,
                    SliderValue: ui.value
                }
        });
            
            
        },
        slide: function(event, ui){
            if(ui.value == 0){
                $("#predictive-adaptive-diff-target-info").html("Desligado");
            }
            
            if(ui.value > 0){
                $("#predictive-adaptive-diff-target-info").html("Operadores em Espera: " + ui.value);
            }
            
            if(ui.value < 0){
                $("#predictive-adaptive-diff-target-info").html("Chamadas em Espera: " + ui.value);
            }
            
        }
    });
    
    
}

function PredictiveInboundQueueSwitch(){
    var NoOutbound;
    if($(this).attr("id") == 'inbound-queue-yes')
    {
        NoOutbound = "ENABLED";
    }
    else
    {
        NoOutbound = "DISABLED";
    }
    $.ajax({
            type: "POST",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "PredictiveInboundQueueSwitch",
                CampaignID: CampaignID,
                NoOutbound: NoOutbound
            }
        });
}

function PredictiveAvailAgentsSwitch(){
    var AvailAgents;
    if($(this).attr("id") == 'avail-agents-yes')
    {
        AvailAgents = "Y";
    }
    else
    {
        AvailAgents = "N";
    }
    $.ajax({
            type: "POST",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "PredictiveAvailAgentsSwitch",
                CampaignID: CampaignID,
                AvailAgents: AvailAgents
            }
        });
}

function PredictiveDiffTarget(){
    
    var DiffTarget = $(this).val(); 
    $.ajax({
            type: "POST",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "PredictiveDiffTarget",
                CampaignID: CampaignID,
                DiffTarget: DiffTarget
            }
        });
    
    
}

function PredictiveMethodSwitch(){
    var PredictiveMethod;
    if($(this).attr("id") == 'method-average')
    {
        PredictiveMethod = "ADAPT_AVERAGE";
    }
    else
    {
        PredictiveMethod = "ADAPT_HARD_LIMIT";
    }
    $.ajax({
            type: "POST",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "PredictiveMethodSwitch",
                CampaignID: CampaignID,
                PredictiveMethod: PredictiveMethod
            }
        });
}

function PredictiveDropPercentage(){
    var DropPercentage = $(this).val();
    
    if(!isNaN(DropPercentage) && DropPercentage <= 100){

        $(this).css("border", "1px solid #C0C0C0");
        
        $.ajax({
            type: "POST",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "PredictiveDropPercentage",
                CampaignID: CampaignID,
                DropPercentage: DropPercentage
            }
        });
        
        
        
    } else {

        $(this).css("border", "1px solid red");
    }
    
    
}

function PredictiveMaxRatio(){
    var MaxRatio = $(this).val();
    
    if(!isNaN(MaxRatio)){
        console.log("success");
        $(this).css("border", "1px solid #C0C0C0");
        
        $.ajax({
            type: "POST",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "PredictiveMaxRatio",
                CampaignID: CampaignID,
                MaxRatio: MaxRatio
            }
        });
   } else {
        $(this).css("border", "1px solid red");
    }
}


function CampaignTimeout(){
    var Timeout = $(this).val();
    
    if(!isNaN(Timeout)){
        $(this).css("border", "1px solid #C0C0C0");
        
        $.ajax({
            type: "POST",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "CampaignTimeout",
                CampaignID: CampaignID,
                Timeout: Timeout
            }
        });
   } else {
        $(this).css("border", "1px solid red");
    }
}


function CampaignCID(){
    var CID = $(this).val();
    
    if(!isNaN(CID)){
        $(this).css("border", "1px solid #C0C0C0");
        
        $.ajax({
            type: "POST",
            url: "_dialer-requests.php",
            data: 
            { 
                action: "CampaignCID",
                CampaignID: CampaignID,
                CID: CID
            }
        });
   } else {
        $(this).css("border", "1px solid red");
    }
}

$("body")
.on("click", ".predictive-inbound-queue", PredictiveInboundQueueSwitch)
.on("click", ".predictive-avail-agents", PredictiveAvailAgentsSwitch)
.on("change", "#predictive-adaptive-diff-target", PredictiveDiffTarget)
.on("click", ".predictive-method-switch", PredictiveMethodSwitch)
.on("focusout", "#predictive-drop-percentage", PredictiveDropPercentage)
.on("focusout", "#predictive-max-ratio", PredictiveMaxRatio)
.on("focusout", "#call-timeout-advanced", CampaignTimeout)
.on("focusout", "#campaign-callerid", CampaignCID)



