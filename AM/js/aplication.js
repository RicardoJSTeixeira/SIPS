var SpiceU = {};
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

    $.post("ajax/user_info.php", function(user) {
        SpiceU = user;
        $.jGrowl('Bem vindo ' + user.name, {life: 4000});
        $("#user-name").text(user.name);

        if (user.user_level > 1) {
            $("#sidebar li.role-dispenser").hide();
        } else {
            $("#sidebar li.role-admin").hide();
        }

    }, "json")
            .fail(function() {
                window.location = "logout.php";
            });

    get_messages();

});

$(".ichat").on("click", ".dismiss_msg", function()
{
    var id_msg = $(this).data().msg_id;
    $.post("ajax/general_functions.php", {action: "edit_message_status", id_msg: id_msg}, function()
    {
        get_messages();
    }, "json");
});


$("#mark_all_read").click(function()
{
    $.post("ajax/general_functions.php", {action: "edit_message_status_by_user"}, function()
    {
        get_messages();
    }, "json");
});


function get_messages()
{
    //GET NEW MESSAGES

    $.post("ajax/general_functions.php", {action: "get_unread_messages"}, function(data) {
        $("#imessage_placeholder").empty();
        var msg = "";
        var msg_count = 0;
        $.each(data, function()
        {
            msg_count++;
            msg += "<div class='imessage'>\n\
                    <div class='imes'>\n\
                    <div class='iauthor'>" + this.from + "</div>\n\
                    <div class='itext'>" + this.msg + "</div>\n\
                    </div>\n\
                    <div class='idelete'><a><span data-msg_id='" + this.id_msg + "' class='dismiss_msg'><i class='icon-remove'></i></span></a></div>\n\
                    <div class='clear'></div>\n\
                    </div>";
        });
        $("#msg_count").text(msg_count);
        $("#imessage_placeholder").append(msg);
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