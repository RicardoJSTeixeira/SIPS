
var AgentMsgFlag = true;
var Marquee_Count;

$(document).ready(function(){
   $("#dialog-agent-msg").dialog({ 
    title: '<span style="font-size:13px; color:black">Mensagem Recebida</span> ',
    autoOpen: false,
    height: 300,
    width: 450,
    resizable: false,
    buttons: {
        "Marcar como vista" : function() { 
            
        $.ajax({
            type: "POST",
            url: "msg_requests.php",
            dataType : "JSON",
            data: { action: "read_msg", sent_agent: user },
            success: function(data) {
                
            }
        });

            AgentMsgFlag = true; 
            $(this).dialog("close"); 
            }
    },
    open: function(){ $('button').blur(); },
    close: function(){ AgentMsgFlag = true; } 
});
 
    
});




        
function MsgReader(){
    $.ajax({
        type: "POST",
        url: "msg_requests.php",
        dataType : "JSON",
        data: { action: "get_msgs", sent_agent: user },
        success: function(data) {
            var HTML_marquee = "";
            if(data){            
            if(data.msg_alert.from[0]!=null){
                $("#dialog-agent-msg").dialog('option', 'title', '<span style="font-size:13px; color:black">Mensagem Recebida de <span style="color: #0073EA">'+data.msg_alert.from+'</span> enviada a <span style="color: #0073EA">'+data.msg_alert.date+'</span></span>');
                $("#dialog-agent-msg").dialog("open");
                AgentMsgFlag = false;
                $("#dialog-agent-msg").html("<div class='div-title'>Mensagem</div>"+data.msg_alert.body); 

            }
            if(Marquee_Count == null){
                if(data.msg_marquee.count > 0){
                    $(".div-marquee").show();
                    Marquee_Count = data.msg_marquee.count; 
                    $.each(data.msg_marquee.from, function(index, value){
                        HTML_marquee += "<b><span style='color: #0073EA'>"+data.msg_marquee.date[index]+" </span></b>" + data.msg_marquee.body[index] + "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
                    })
                    $("#marquee-msg").html("<marquee style='margin-top:6px' scrolldelay='150'>" + HTML_marquee + "</marquee>");
                } 
                  
            }
            if(typeof data.msg_marquee.from != 'undefined'){
                            if(data.msg_marquee.from[0]){
                $(".div-marquee").show();

                if(parseInt(Marquee_Count) != parseInt(data.msg_marquee.count)){

                    
                    $.each(data.msg_marquee.from, function(index, value){
                        HTML_marquee += "<b><span style='color: #0073EA'>"+data.msg_marquee.date[index]+" </span></b>" + data.msg_marquee.body[index] + "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
                    })
                    $("#marquee-msg").html("<marquee style='margin-top:6px' scrolldelay='150'>" + HTML_marquee + "</marquee>");   
                    Marquee_Count = data.msg_marquee.count;
                    }

            } 
            }

        }
        }
    });
}
