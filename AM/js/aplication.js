var SpiceU = {};
$.post("ajax/user_info.php", function(user) {
    SpiceU = user;
    $("#user-name").text(user.name);

    if (user.user_level > 1) {
        $("#sidebar li.role-dispenser:not(.role-admin)").hide();
    } else {
        $("#sidebar li.role-admin:not(.role-dispenser)").hide();
    }

}, "json")
        .fail(function() {
            window.location = "logout.php";
        });

$(function() {
    function setFavicon() {
        var link = $('link[type="image/vnd\.microsoft\.icon"]').remove().attr("href");
        $('<link href="' + link + '" rel="shortcut icon" type="image/vnd.microsoft.icon" />').appendTo('head');
    }
    $.ajaxSetup({cache: false});
    moment.lang('pt');
    $.history.on('load change pushed', function(event, url, type) {
        $("#sidebar .active").removeClass("active");
        $("#sidebar").find("[href='" + url.split("?")[0] + "']").addClass("active");

        if (url.length) {
            $("#principal").load(url);
            setFavicon();
        } else {
            $.history.push("view/dashboard.html");
        }
    }).listen('hash');

    if (!window.location.hash.length) {
        $.history.push("view/dashboard.html");
    }


    $('#sidebar a').click(function(e) {
        e.preventDefault();

        if ($(this).hasClass("active"))
            return false;

        var href = $(this).attr("href");

        if (href === "#")
            return false;

        $.history.push(href);

    });
    get_messages();
    var messages_timeout = setInterval(get_messages, 1000 * 60);
    get_alerts();
    var alerts_timeout = setInterval(get_alerts, 1000 * 60);

});

$(".ichat").on("click", ".dismiss_msg", function(){
    $.post("ajax/general_functions.php", {action: "edit_message_status", id_msg: $(this).data().msg_id}, function()
    {
        get_messages();
    }, "json");
});


$("#mark_all_read").click(function(){
    $.post("ajax/general_functions.php", {action: "edit_message_status_by_user"}, function()
    {
        get_messages();
    }, "json");
});

$(".ichat").on("click", ".ok_alert", function(){
    $.post("ajax/general_functions.php", {action: "set_readed", id_msg: $(this).data().id}, function()
    {
        get_alerts();
    }, "json");
});


$("#mark_all_alerts_read").click(function(){
    $.post("ajax/general_functions.php", {action: "set_all_readed"}, function()
    {
        get_alerts();
    }, "json");
});

$("#notifications").click(function()
{
    var a=$("#alert_time");
        a.text(a.data().update.fromNow());
});


function get_messages()
{
    //GET NEW MESSAGES

    $.post("ajax/general_functions.php", {action: "get_unread_messages"}, function(data) {
        $("#imessage_placeholder").empty();
        var msg = "";
        $.each(data, function()
        {
            msg += "<div class='imessage'>\n\
                        <div class='imes'>\n\
                            <div class='iauthor'>" + this.from + "</div>\n\
                            <div class='itext'>" + this.msg + "</div>\n\
                        </div>\n\
                        <div class='idelete'><a><span data-msg_id='" + this.id_msg + "' class='dismiss_msg'><i class='icon-remove'></i></span></a></div>\n\
                        <div class='clear'></div>\n\
                    </div>";
        });
        $("#msg_count").text(data.length);
        $("#imessage_placeholder").append(msg);
    }, "json");
}

function get_alerts()
{
    $.post("ajax/general_functions.php", {action: "get_alerts"}, function(data) {
        $("#alerts-content").empty();
        $("#alert_time").data("update",moment());
        var msg = "";
        $.each(data, function()
        {
            msg += "<div class='imessage'>\n\
                        <div class='r_icon'><a href='javascript:void(0)' class='ok_alert' data-id='" + this.id + "'><i class='icon-comment'></i></a></div>\n\
                        <div class='r_info'>\n\
                            <div class='r_text'>" + this.alert + "</div>\n\
                            <div class='r_text'><i class='icon-time'></i>"+moment(this.entry_date).fromNow()+"</div>\n\
                        </div>\n\
                        <div class='clear'></div>\n\
                    </div>";
        });
        $("#alerts-count").text(data.length);
        $("#alerts-content").append(msg);
    }, "json");
}

function getUrlVars() {
    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
    });
    return vars;
}

$.fn.dataTableExt.oApi.fnReloadAjax = function(oSettings, sNewSource, fnCallback, bStandingRedraw)
{
    // DataTables 1.10 compatibility - if 1.10 then versionCheck exists.
    // 1.10s API has ajax reloading built in, so we use those abilities
    // directly.
    if ($.fn.dataTable.versionCheck) {
        var api = new $.fn.dataTable.Api(oSettings);

        if (sNewSource) {
            api.ajax.url(sNewSource).load(fnCallback, !bStandingRedraw);
        }
        else {
            api.ajax.reload(fnCallback, !bStandingRedraw);
        }
        return;
    }

    if (sNewSource !== undefined && sNewSource !== null) {
        oSettings.sAjaxSource = sNewSource;
    }

    // Server-side processing should just call fnDraw
    if (oSettings.oFeatures.bServerSide) {
        this.fnDraw();
        return;
    }

    this.oApi._fnProcessingDisplay(oSettings, true);
    var that = this;
    var iStart = oSettings._iDisplayStart;
    var aData = [];

    this.oApi._fnServerParams(oSettings, aData);

    oSettings.fnServerData.call(oSettings.oInstance, oSettings.sAjaxSource, aData, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable(oSettings);

        /* Got the data - add it to the table */
        var aData = (oSettings.sAjaxDataProp !== "") ?
                that.oApi._fnGetObjectDataFn(oSettings.sAjaxDataProp)(json) : json;

        for (var i = 0; i < aData.length; i++)
        {
            that.oApi._fnAddData(oSettings, aData[i]);
        }

        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();

        that.fnDraw();

        if (bStandingRedraw === true)
        {
            oSettings._iDisplayStart = iStart;
            that.oApi._fnCalculateEnd(oSettings);
            that.fnDraw(false);
        }

        that.oApi._fnProcessingDisplay(oSettings, false);

        /* Callback user function - for event handlers etc */
        if (typeof fnCallback === 'function' && fnCallback !== null)
        {
            fnCallback(oSettings);
        }
    }, oSettings);
};

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};