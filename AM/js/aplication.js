$(function(){
    
      $("#sidebar a").click(function(e)
      {
            e.preventDefault();
            if($(this).hasClass("active"))
                return false;
            
            $("#sidebar .active").removeClass("active");
            var href=$(this).addClass("active").attr("href");
            
            if(href==="#")
                return false;
            
            $("#principal").load(href);
      });
    $("#principal").load("view/dashboard.html");
    
    $.post("ajax/user_info.php",function(user){
        $.jGrowl('Bem vindo '+user.name, {life: 4000});
        $("#user-name").text(user.name);
    },"json")
    .fail(function(){window.location="logout.php";});
});

