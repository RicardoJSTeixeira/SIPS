
var CallHistoryMode = "lostcalls";
var oTable_CallHistory;

$(document).on("click",".LostCallDial",function(){

    $("#CallHistoryDialog").dialog("close");
    document.vicidial_form.MDPhonENumbeR.value = $(this).data().phone;
    NeWManuaLDiaLCalLSubmiT('PREVIEW');
});


$(function() {

$("#CallHistoryStart").on("click",function() {
    var go_on = divchecker("CallHistoryDialog");
    if (!go_on) {
        return;
    }
    if (AgentDispoing > 0) {
        alert_box('Termine Wrap-up da chamada.');
        return;
    }

    if (AutoDialReady == 0 && auto_dial_level > 0 && pause_code_counter == 0) {
        alert_box('Seleccione o motivo de pausa por favor.');
        return;
    }

    var move_on = 1;
    if ((AutoDialWaiting == 1) || (VD_live_customer_call == 1) || (alt_dial_active == 1) || (MD_channel_look == 1) || (in_lead_preview_state == 1))
    {
        if ((auto_pause_precall == 'Y') && ((agent_pause_codes_active == 'Y') || (agent_pause_codes_active == 'FORCE')) && (AutoDialWaiting == 1) && (VD_live_customer_call != 1) && (alt_dial_active != 1) && (MD_channel_look != 1) && (in_lead_preview_state != 1))
        {
            agent_log_id = AutoDial_ReSume_PauSe("VDADpause", '', '', '', '', '1', auto_pause_precall_code);
        }
        else
        {
            move_on = 0;
            alert_box("Tem que estar em pausa para fazer chamadas manuais");
        }
    }
    if (document.vicidial_form.lead_id.value.length !== 0)
    {
        move_on = 0;
        alert_box("Tem que estar em pausa para fazer chamadas manuais");
    }

    if (move_on == 1)
    {
        if (typeof oTable_CallHistory !== "undefined") {
            oTable_CallHistory.fnReloadAjax();
        }

        oTable_CallHistory = $('#table-CallHistory').dataTable({
            "aaSorting": [[0, "desc"]],
            "iDisplayLength": 9,
            "sDom": '<"top"f><rt><"bottom"p>',
            "bSortClasses": false,
            "bProcessing": true,
            "bRetrieve": true,
            "bDestroy": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": 'ajax/historico.php',
            "fnServerParams": function(aoData)
            {
                aoData.push(
                        {"name": "action", "value": CallHistoryMode},
                {"name": "sent_user_id", "value": user},
                {"name": "sent_campaign_id", "value": campaign}
                );
            },
            "aoColumns": [{"sTitle": "Nome", "sWidth": "100px"},
                {"sTitle": "Número", "sWidth": "100px"},
                {"sTitle": "Data", "sWidth": "100px"},
                {"sTitle": "Motivo", "sWidth": "175px"},
                {"sTitle": "Marcar", "sWidth": "20px"},
                {"sTitle": "Contactado", "sWidth": "20px"}
            ],
            "oLanguage": {"sUrl": "../jquery/jsdatatable/language/pt-pt.txt"},
            "fnPreDrawCallback": function() {
                if (!$("#table-CallHistory-title").length) {
                    $("#table-CallHistory_wrapper .top").prepend("<div id='table-CallHistory-title' style='float:left; margin:5px 0 0 0; font-weight:bold;'>Chamadas Perdidas</div>");
                }
            },
            "fnDrawCallback": function() {
                $("#table-CallHistory").find("time").each(
                        function() {
                            var that = $(this);
                            that.text(moment(that.attr("datetime")).fromNow());
                            that.attr("title", (moment(that.attr("datetime")).format("HH:mm:ss")));
                        });
            }
        });
        $("#CallHistoryDialog").dialog("open");
    }
});


    $("#change-lostcalls").click(function() {
        $("#table-CallHistory-title").text("Chamadas Perdidas");
        CallHistoryMode = "lostcalls";
        oTable_CallHistory.fnReloadAjax();
    });
    $("#change-manual").click(function() {
        $("#table-CallHistory-title").text("Chamadas Manuais");
        CallHistoryMode = "manual";
        oTable_CallHistory.fnReloadAjax();
        CallHistoryMode = "lostcalls";
    });
    $("#change-outbound").click(function() {
        $("#table-CallHistory-title").text("Outbound");
        CallHistoryMode = "outbound";
        oTable_CallHistory.fnReloadAjax();
        CallHistoryMode = "lostcalls";
    });
    $("#change-inbound").click(function() {
        $("#table-CallHistory-title").text("Inbound");
        CallHistoryMode = "inbound";
        oTable_CallHistory.fnReloadAjax();
        CallHistoryMode = "lostcalls";
    });

    $("#CallHistoryDialog").dialog({
        title: 'Histórico de Chamadas',
        autoOpen: false,
        height: 500,
        width: 1200,
        resizable: false,
        open: function() {
            $("#table-CallHistory-title").text("Chamadas Perdidas");
        }
    });

});
