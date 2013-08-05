
var AgentMsgFlag = true;
var Marquee_Count = 0;

$(function() {
    $("#dialog-agent-msg").dialog({
        title: '<span style="font-size:13px; color:black">Mensagem Recebida</span> ',
        autoOpen: false,
        height: 300,
        width: 450,
        resizable: false,
        buttons: {
            "Marcar como vista": function() {
                $.post("ajax/mensagens.php", {action: "read_msg", sent_agent: user});

                AgentMsgFlag = true;
                $(this).dialog("close");
            }
        },
        create: function() {
            $(this).closest(".ui-dialog")
                    .find("button")
                    .addClass("btn btn-primary");
        },
        open: function() {
            $('button').blur();
        },
        close: function() {
            AgentMsgFlag = true;
        }
    });


});

function MsgReader() {
    $.post("ajax/mensagens.php", {action: "get_msgs", sent_agent: user}, function(data) {
        var HTML_marquee = "";
        if (data) {
            if (data.msg_alert) {
                $("#dialog-agent-msg").dialog('option', 'title', '<span style="font-size:13px; color:black">Mensagem Recebida de <span style="color: #0073EA">' + data.msg_alert.from + '</span> enviada a <span style="color: #0073EA">' + moment(data.msg_alert.date).fromNow() + '</span></span>');
                $("#dialog-agent-msg").dialog("open");
                AgentMsgFlag = false;
                $("#dialog-agent-msg").html("<div class='div-title'>Mensagem</div>" + data.msg_alert.body);

            }
            if (!Marquee_Count) {
                if (data.msg_marquee.messages.length) {
                    $("#marquee-msg").show();
                    Marquee_Count = data.msg_marquee.messages.length;
                    $.each(data.msg_marquee.messages, function() {
                        HTML_marquee += "<span class=\"msg-marquee\"><b><span style='color: #0073EA'>" + moment(this.date).format('HH:mm:ss') + " </span></b>" + this.body + "</span>";
                    });
                    $("#marquee-msg").html("<marquee style='margin-top:6px' scrolldelay='150'>" + HTML_marquee + "</marquee>");
                }

            }
            if (data.msg_marquee.messages) {
                $(".div-marquee").show();

                if (parseInt(Marquee_Count) != parseInt(data.msg_marquee.messages.length)) {

                    $.each(data.msg_marquee.messages, function() {
                        HTML_marquee += "<span class=\"msg-marquee\"><b><span style='color: #0073EA'>" + moment(this.date).format('HH:mm:ss') + " </span></b>" + this.body + "</span>";
                    });
                    $("#marquee-msg").html("<marquee style='margin-top:6px' scrolldelay='150'>" + HTML_marquee + "</marquee>");
                    Marquee_Count = data.msg_marquee.messages.length;
                }

            }

        }
    }
    , "json");
}
