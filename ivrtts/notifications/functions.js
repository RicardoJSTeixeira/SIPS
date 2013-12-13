function BuildNotifications(User) {
    $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "../notifications/requests.php",
        data: {ZERO: "BuildNotifications", User: User},
        success: function(data) {
            $(".notifications-head").empty();

            if (typeof data.camp_enabler !== 'undefined') {

                $(".notifications-head").prepend("<div class='btn-group m_left hide-mobile' >\n\
                                    <div><a id='get-campaign-enabler' class='dropdown-toggle show-messages pointer' data-toggle='dropdown' data-target='#'>\n\
                                        <span class='notification campaign-enabler-message-counter'>" + data.camp_enabler[0] + "</span><span class='triangle-1'></span><i class='icon-flag'></i><span class='caret'></span>\n\
                                    </a>\n\
                                    <div class='dropdown-menu'>\n\
                                        <span class='triangle-2'></span>\n\
                                         <div class='ichat ichat-600 notification-box-600'>\n\
                                            <div class='ichat-messages'>\n\
                                                <div class='ichat-title '>\n\
                                                    <div class='pull-left '>Campaign List</div>\n\
                                                    <div class='pull-right '><span style='margin-right:3px'>Search</span><input id='campaign-enabler-search' type='text' style='margin:0px -3px 0px 0px; width:150px; height:12px' ></div>\n\
                                                    <div class='clear'></div>\n\
                                                </div>\n\
                                                <div id='campaign-enabler-messages' class='slimscroll2'></div>\n\
                                            </div>\n\
                                            <a class='iview pointer cc-enable-all' style='width:234px;'>Start All</a> <a class='imark pointer cc-disable-all' style='width:234px;'>Stop All</a>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
							</div>");


                BuildMessagesArray("get-campaign-enabler");
            }


        }
    });

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function BuildMessagesArray(Get) {
    switch (Get) {
        case "get-admin-global-messages":
            {
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "../notifications/requests.php",
                    data: {ZERO: "GetMessages", User: User, Get: Get},
                    success: function(Data) {
                        CurrentMessages.adminglobal = Data;

                    }

                });
                break;
            }
        case "get-campaign-enabler":
            {
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "../notifications/requests.php",
                    data: {ZERO: "GetMessages", User: User, Get: Get},
                    success: function(Data) {
                        CurrentMessages.campaign_enabler = Data;
                        //console.log(CurrentMessages.campaign_enabler);
                    }

                });
                break;
            }
    }

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ReadMessagesArray(Msg) {

    if (typeof CurrentMessages.adminglobal !== 'undefined' && typeof Msg == 'undefined') {

        $("#admin-global-messages").html("");
        $.each(CurrentMessages.adminglobal.messages, function(Index, Message) {



            switch (Message.message_type) {
                case "user_auth_log":
                    {
                        var EventUser = Message.name + " " + Message.last_name;
                        var EventText = "",
                                CssFaded = "",
                                IconChange = "";
                        switch (Message.event) {
                            case "login":
                                EventText = " as logged in";
                                break;
                            case "logout":
                                EventText = " as logged out";
                                break;
                        }

                        if (Message.viewed === 1) {
                            CssFaded = "opacity";
                            IconChange = "icon-ok";
                        } else {
                            CssFaded = "";
                            IconChange = "icon-envelope";
                        }
                        $("#admin-global-messages").append("<div class='imessage " + CssFaded + "' style='padding-bottom:8px !important;'>\n\
                                <div class='iavatar'><img src='../images/users/icon_user_big.png' alt=''></div>\n\
                                <div class='imes notification-message-400'>\n\
                                    <div class='iauthor'>" + EventUser + "<span class='notification-user-secondary-text'>" + EventText + "</span></div>\n\
                                    <div class='itext'><i class='icon-time notification-icon'></i>" + DateToTimeAgo(Message.event_time) + "</div>\n\
                                </div>\n\
                                <div class='idelete' style='margin-right:12px;'><a id='delete-admin-global'><span><i style='margin-bottom:2px' delete-type='adminglobal' delete-index='" + Index + "' delete-id='" + Message.id_notification + "' class='" + IconChange + " read-message pointer'></i></span></a></div>\n\
                                <div class='clear'></div>\n\
                            </div>");
                    }
            }



        });
    }

    if (typeof CurrentMessages.campaign_enabler !== 'undefined' && CurrentMessages.campaign_enabler !== null)
    {

        $("#campaign-enabler-messages").html("");
        $.each(CurrentMessages.campaign_enabler.messages, function(Index, Message) {



            if (Message.active == "Y")
            {
                var AvatarImage = "src='../images/users/icon_on_48.png'";

            }
            else
            {
                var AvatarImage = "src='../images/users/icon_off_48.png'";
            }

            var CE_HTML = "<div class='imessage' style='padding-bottom:8px !important;'>\n\
                                <div class='iavatar ' style='border:none; box-shadow:none;' ><img campaign_id='" + Message.campaign_id + "' class='pointer click-avatar-image' " + AvatarImage + " style='width:38px; height:38px; margin:2px'></div>\n\
                                <div class='imes notification-message-600 '>\n\
                                    <div class='iauthor '><span  style='color:#000 !important;'>" + Message.campaign_name + " - " + Message.campaign_id + "</span></div>\n\
                                    <div class='itext '><i class='icon-envelope notification-icon'></i>Total messages: <b>" + Message.list_description + "</b></div>\n\
                                </div>\n\
                                <div class='idelete ' style='margin:0px 12px 0px 0px; !important;'>\n\
								<table style='width:120px'>\n\
								<tr>\n\
								<td><img campaign_id='" + Message.campaign_id + "' campaign='" + Message.campaign_name + "' campaign_active='" + Message.active + "' class='pointer click-live-report' style='margin-top:-4px' src='../images/users/icon_chart_16.png'></td><td style='padding-left:6px'><span campaign_id='" + Message.campaign_id + "' campaign='" + Message.campaign_name + "' campaign_active='" + Message.active + "' class='pointer click-live-report'>Live Report</span></td>\n\
								</tr>\n\
								<tr style='height:3px'></tr>\n\
								<tr>\n\
								<td><img  class='pointer' style='margin-top:-4px' src='../images/users/icon_download_16.png'></td><td  class='pointer' style='padding-left:6px'><span class='pointer unselectable' onclick=getDB('" + Message.campaign_id + "'); >Download DB</span></td>\n\
								</tr>\n\
								</table>\n\
								</div>\n\
                                <div class='clear'></div>\n\
                            </div>";


            if ($("#campaign-enabler-search").val() == "")
            {
                $("#campaign-enabler-messages").append(CE_HTML);
            }
            else
            {
                if (Message.campaign_name.toLowerCase().match($("#campaign-enabler-search").val().toLowerCase()))
                {
                    $("#campaign-enabler-messages").append(CE_HTML);
                }
            }




        });
    }

}

function getDB(campaign) {
    
    var date = new Date();
    var dia = date.getUTCDate();
    var ano = date.getFullYear();
    var mes = date.getUTCMonth() + 1;

    var date = ano + '-' + mes + '-' + dia;
    
    //var url = "http://sipscloud.dyndns.org/oldb/vicidial/call_report_export.php?campaign[]=" + campaign + "&query_date=2013-01-01&end_date=" + date + "&status[]=---ALL---&list[]=---ALL---&run_export=1&SUBMIT=SUBMIT&export_fields=EXTENDED&header_row=YES";
    
   var url = "http://192.168.1.101/ivrtts/report/reportexcel.php?1" ;
    
    window.open(url);
    
}


$(".click-live-report").live("click", function() {

    CurrentCampaign = $(this).attr("campaign");
    CurrentCampaignID = $(this).attr("campaign_id");

    $(".active-campaign").html(CurrentCampaign);
    if ($(this).attr("campaign_active") == 'Y') {
        $(".status-campaign").html("<span style='background-color:#468847' class='label label-success'>Running</span>")
    } else {
        $(".status-campaign").html("<span style='background-color:#b94a48' class='label label-important'>Stopped</span>")
    }


    // GetRealtime();

    $(".sidebar-page-loader[pagetoload='../intra_realtime/index.php']").click();

})

$(".click-avatar-image").live("click", function() {

    if ($(this).attr("src").match("off"))
    {
        $(this).attr("src", "../images/users/icon_on_48.png");

        $.ajax({
            type: "POST",
            url: "../tools/notifications/requests.php",
            data: {ZERO: "CampaignEnableNotifications", sent_campaign: $(this).attr("campaign_id"), sent_active: "Y", sent_status: "ACTIVE"},
            success: function(data) {

            }
        });



    }
    else
    {
        $(this).attr("src", "../images/users/icon_off_48.png");



        $.ajax({
            type: "POST",
            url: "../tools/notifications/requests.php",
            data: {ZERO: "CampaignEnableNotifications", sent_campaign: $(this).attr("campaign_id"), sent_active: "N", sent_status: "INACTIVE"},
            success: function(data) {

            }
        });



    }


})

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function DeleteMessageArray(ClickedElement) {

    //console.log(ClickedElement.closest(".imessage").css("opacity"));

    if (ClickedElement.closest(".imessage").hasClass("opacity") || ClickedElement.closest(".imessage").css("opacity") === '0.5') {
        return false;
    }

    CurrentMessages[ClickedElement.attr("delete-type")].messages[ClickedElement.attr("delete-index")].viewed = 1;

    ClickedElement.removeClass("icon-envelope").addClass("icon-ok");

    ClickedElement.closest(".imessage").animate({'opacity': 0.5}, 300);

    var counter = parseInt($("." + ClickedElement.attr("delete-type") + "-message-counter").html(), 10);
    $("." + ClickedElement.attr("delete-type") + "-message-counter").html(counter - 1);

    $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "../notifications/requests.php",
        data: {ZERO: "ReadMessage", MessageID: ClickedElement.attr("delete-id")},
        success: function(data) {

        }
    });

}
























function DateToTimeAgo(time) {
    var date = new Date((time || "").replace(/-/g, "/").replace(/[TZ]/g, " ")),
            diff = (((new Date()).getTime() - date.getTime()) / 1000),
            day_diff = Math.floor(diff / 86400);

    if (day_diff < 0)
        return "just now";

    if (isNaN(day_diff) || day_diff < 0 || day_diff >= 31)
        return;

    return day_diff === 0 && (
            diff < 60 && "just now" ||
            diff < 120 && "1 minute ago" ||
            diff < 3600 && Math.floor(diff / 60) + " minutes ago" ||
            diff < 7200 && "1 hour ago" ||
            diff < 86400 && Math.floor(diff / 3600) + " hours ago") ||
            day_diff === 1 && "Yesterday" ||
            day_diff < 7 && day_diff + " days ago" ||
            day_diff < 31 && Math.ceil(day_diff / 7) + " weeks ago";
}
