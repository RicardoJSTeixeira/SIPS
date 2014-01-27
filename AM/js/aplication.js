$(function() {

    $.history.on('load change pushed', function(event, url, type) {
        $("#sidebar .active").removeClass("active");
        $("#sidebar").find("[href='" + url + "']").addClass("active");

        if (url.length) {
            $("#principal").load(url);
        } else {
            $.history.push("view/dashboard.html");
            $("#principal").load("view/dashboard.html");
        }
    }).listen('hash');

    if (!window.location.hash.length) {
        $.history.push("view/dashboard.html");
        $("#principal").load("view/dashboard.html");
        $("#sidebar").find("[href='view/dashboard.html']").addClass("active");
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
});

