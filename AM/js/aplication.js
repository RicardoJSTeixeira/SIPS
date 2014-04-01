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
        $.jGrowl('Bem vindo ' + user.name, {life: 4000});
        $("#user-name").text(user.name);
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
    },
            "json");
});


$("#mark_all_read").click(function()
{
    $.post("ajax/general_functions.php", {action: "edit_message_status_by_user"}, function()
    {
        get_messages();
    },
            "json");
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
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
    });
    return vars;
}