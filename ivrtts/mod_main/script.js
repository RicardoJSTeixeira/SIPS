var CurrentCampaign;

var CurrentCampaignID;
var dash = new dashboard();
var CurrentMessages = new Array();


$(function() {

    function PopulateDropDownAndCurrentCampaign(Reload)
    {
        $.post("requests.php", {zero: "GetActiveCampaignsDropDownList"}, function(data) {
            $(".dropdown-scroll").empty();
            if (data === null)
            {
                if (typeof CurrentCampaign !== 'undefined') {
                    $(".dropdown-scroll").append("<li><a href='#'>There are no Campaigns created.</li>");
                    $("#kant").html("<br><center><b>No Campaigns.</b></a><img style='margin-left:8px' src='../images/users/icon_emotion_sad_32.png'></center>");
                    $('.dropdown-scroll').slimScroll({
                        position: 'right',
                        height: '30px',
                        railVisible: false,
                        wheelStep: 8
                    });
                    $(".ul-dropdown").css("width", "210px");
                }
            }
            else
            {
                $.each(data.camp_list, function(index, value) {
                    if (index === 0 && !Reload) {
                        CurrentCampaignID = value['campaign_id'];
                        CurrentCampaign = value['campaign_name'];
                        $(".active-campaign").html(value['campaign_name']);
                        if (value['active'] === 'Y') {
                            $(".status-campaign").html("<span style='background-color:#468847' class='label label-success'>Running</span>");
                        } else {
                            $(".status-campaign").html("<span style='background-color:#b94a48' class='label label-important'>Stopped</span>");
                        }
                    } else {
                        if (Reload && value['campaign_name'] === CurrentCampaign)
                        {
                            $(".active-campaign").html(value['campaign_name']);
                            if (value['active'] === 'Y') {
                                $(".status-campaign").html("<span style='background-color:#468847' class='label label-success'>Running</span>");
                            } else {
                                $(".status-campaign").html("<span style='background-color:#b94a48' class='label label-important'>Stopped</span>");
                            }
                        }
                    }
                    $(".dropdown-scroll").append("<li><a href='#' class='quick-choose-campaign' campaign-active='" + value['active'] + "' campaign='" + value['campaign_id'] + "'>" + value['campaign_name'] + "</a></li>");
                });
                $('.dropdown-scroll').slimScroll({
                    position: 'right',
                    height: '210px',
                    railVisible: false,
                    wheelStep: 8
                });

                if ($(".sidebar-page-loader[pagetoload='../intra_realtime/index.php']").parent().hasClass("active")) {
                    dashboardMain();
                }

            }

        },
                "json");
    }

    $(".quick-choose-campaign").live("click", function() {

        CurrentCampaign = $(this).html();
        CurrentCampaignID = $(this).attr("campaign");

        $(".active-campaign").html(CurrentCampaign);
        if ($(this).attr("campaign-active") === 'Y') {
            $(".status-campaign").html("<span style='background-color:#468847' class='label label-success'>Running</span>");
        } else {
            $(".status-campaign").html("<span style='background-color:#b94a48' class='label label-important'>Stopped</span>");
        }

        $(".sidebar-page-loader[pagetoload='../intra_realtime/index.php']").click();

    });

    $("#a-logoff").click(function() {
        LogOut(User);
    });

    $(".speedbar-nav").click(function() {
        $.get($(this).attr("href"), function(data) {
            $("#kant").html(data);
        });
        $(".speedbar-nav").removeClass("act_link");
        $(this).addClass("act_link");
        return false;
    });

    $(".sidebar-page-loader").click(function() {

        if ($(this).attr("pagetoload").match("realtime"))
        {
            if (typeof CurrentCampaign !== 'undefined') {
                dashboardMain();
            } else {
                $(".dropdown-scroll").append("<li><a href='#'>There are no Campaigns created.</li>");
                $("#kant").html("<br><center><b>No Campaigns.</b></a><img style='margin-left:8px' src='../images/users/icon_emotion_sad_32.png'></center>");
                $('.dropdown-scroll').slimScroll({
                    position: 'right',
                    height: '30px',
                    railVisible: false,
                    wheelStep: 8
                });
                $(".ul-dropdown").css("width", "210px");
            }
        }
        else
        {
            $.get($(this).attr("pagetoload"),
                    function(data) {
                        $("#kant").html(data);
                    }
            );

        }

        $.post('requests.php',{zero: "SpeedbarLinks", link_id: $(this).attr("menuid")});

        $(".sidebar-nav").removeClass("active");

        $(this).closest('a').addClass("active");

        return false;

    });

    $(".show-messages").live("mousedown", function() {
        ReadMessagesArray();
        $('.slimscroll').slimScroll({
            position: 'right',
            height: '345px', // 345px
            railVisible: false,
            wheelStep: 8
        });
        $('.slimscroll2').slimScroll({
            position: 'right',
            height: '335px', // 345px
            railVisible: false,
            wheelStep: 8
        });
    });

    $("#campaign-enabler-search").live("input", function() {
        ReadMessagesArray("enabler");
    });


    $(".read-message").live("click", function() {
        DeleteMessageArray($(this));
    });

    (function() {

        if (!$("#get-campaign-enabler").parent().hasClass("open")) {
            BuildNotifications(User); 
        }
        PopulateDropDownAndCurrentCampaign(true); 

        setTimeout(arguments.callee, 10000);


    })();


    BuildNotifications(User);
    PopulateDropDownAndCurrentCampaign(false);
});