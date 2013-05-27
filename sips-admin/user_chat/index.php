<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>

<div class="cc-mstyle">
    <table>
        <tr>
            <td id="icon32"><img src='/images/icons/construction_32.png' /></td>
            <td id='submenu-title' style='width:400px'> Mensagens para Operadores </td> 
            <td style='text-align:right'></td>
        </tr>
    </table>
</div>
<div id="work-area">
    <br><br>
    <center>
        <table style="width: 400px;" border=0> 
        <tr>
            <td style="width: 65px; padding-left: 3px">
                <b>Enviar para:</b>
            </td>
            <td style="text-align: center; width: 150px">
                <input type="radio" id="send-to-group" name="send-to[]" checked="checked"></input><label for="send-to-group">Grupos</label> 
                <input type="radio" id="send-to-user" name="send-to[]"></input><label for="send-to-user">Operadores</label>  
            </td>
            <td style="width: 16px"></td>
            <td style="width: 110px; padding-left: 3px">
                <b>Tipo de Mensagem:</b>
            </td>
            <td>
                <input type="radio" id="msg-type-alert" name="msg-type[]" checked="checked"></input><label for="msg-type-alert">Alerta</label> 
                <input type="radio" id="msg-type-scroll" name="msg-type[]"></input><label for="msg-type-scroll">Rodapé</label>  
            </td>
            <tr>
                <td colspan="2" valign="top" style="text-align: center; padding: 10px 3px 0px 3px">
                    <table>
                        <tr>
                            <td style="text-align: left"><b>Procurar:</b></td><td style="text-align: right"><input id="search-field" type="text"></td>
                        </tr>
                    </table>
                    <select style="width:225px; height:250px; margin-top:6px" multiple id="send-to-options"></select> 
                </td>
                <td></td>
                <td colspan="3" valign="bottom">
                <div style="margin-left:3px; float:right;" id="tag-container" ></div> 
                <textarea id="msg" style="margin: 8px 0px 0px 3px; padding:6px; resize: none; height: 160px; width: 400px;"></textarea>
                </td>
                <td valign="top"><img id="clear-tags" style="cursor:pointer; visibility: hidden;" src="/images/icons/cross_16.png"></td>
            </tr>
            <tr>
                <td colspan="2" style="padding:2px">
                    <button id="clear-options" style="float: left; font-size: 12px">Limpar</button>
                    <button id="add-items" style="float: right; font-size: 12px">Adicionar</button>
                </td>
                <td colspan="3" style="padding:1px">
                    <span id="info-msg" style="float: left; margin-left: 3px"></span><button id="send-msg" style="float: right; font-size: 12px; margin-right:-2px">Enviar</button>
                </td>  
            </tr>        
        </tr>
        </table>
    </center>
</div>

    <script src="/jquery/jquerytags/tags.js"></script>
    <link rel="stylesheet" type="text/css" href="/jquery/jquerytags/tags.css" />



<script>

// Inicialization
    var UsersValue;
    var UsersDescription;
    
    var GroupsValue;
    var GroupsDescription;
    
    var SelectedGroups = [];
    var SelectedUsers = [];
    
    var VisibleList = "groups";

// Uniform 
$("input[type=checkbox], input[type=radio]").uniform();

// Click Capture
$("body").click(function(e){
    //console.log(e.target); 
}); 


// Tags    
$("#tag-container").tagsInput({
    'defaultText' : "",
    'interactive' : false,
    'onAddTag' : function(a){
        var Tag = a;
        if(VisibleList === "groups"){
            $.each(GroupsDescription, function(index, value){
                if($.trim(Tag) === $.trim(value)){SelectedGroups[index] = 1;}
            })  
        } else {
            $.each(UsersDescription, function(index, value){
				//console.log(value)
				//console.log(index)
				//console.log(Tag)
                if($.trim(Tag) === $.trim(value)){SelectedUsers[index] = 1;}
            })
        }
        $("#clear-tags").css("visibility", "visible");
       // console.log(SelectedGroups);
       // console.log(SelectedUsers); 
    },
    'onRemoveTag' : function(a){
        var Tag = a;
        
            $.each(GroupsDescription, function(index, value){
                if($.trim(Tag) === $.trim(value)){SelectedGroups[index] = undefined;}

            })  

            $.each(UsersDescription, function(index, value){
                if($.trim(Tag) === $.trim(value)){SelectedUsers[index] = undefined;}

            })
            
     //   console.log(SelectedGroups);
     //   console.log(SelectedUsers); 
    }
});

// Submit Message
$("#send-msg").click(function(){
    var SentUsers = [];
    var SentGroups = [];
    var SentMsg = $("#msg").val();
    var SentFrom = "<?php echo $_SERVER['PHP_AUTH_USER']; ?>";
    var SentMsgType;
    
    if( $("#tag-container").tagExist('') ){ $("#info-msg").html("<b>Por favor escolha pelo menos um destinatário.</b>"); console.log("destination"); return false; }
    
    if($("#msg").val().length < 1){ $("#info-msg").html("<b>Por favor escreva uma mensagem.</b>"); console.log("msg"); return false; }
    
    
    
    if($( "#uniform-msg-type-alert span" ).hasClass("checked")){
        SentMsgType = "alert";
    } else {
        SentMsgType = "fixed";
    }
    
    
    if($( "#uniform-send-to-user span" ).hasClass("checked")){
        $.each(SelectedUsers, function(index, value){
            if(SelectedUsers[index] === 1){SentUsers.push(UsersValue[index])}
        }) 
    } else {
        $.each(SelectedGroups, function(index, value){
            if(SelectedGroups[index] === 1){SentGroups.push(GroupsValue[index])}
        }) 
    }

    
    $.ajax({
        type: "POST",
        url: "requests.php",
        data: { action: "submit_msg", sent_users: SentUsers, sent_groups: SentGroups, sent_msg: SentMsg, sent_from: SentFrom, sent_msg_type: SentMsgType },
        success: function(data) {

        $("#info-msg").html("<b>Mensagem enviada com sucesso.</b>");

        }
    });
})

  
  
// Double Click Add tags 
$("#send-to-options").dblclick(function () {
    $("#send-to-options option:selected").each(function () {
        if(!$("#tag-container").tagExist($(this).text())) { $("#tag-container").addTag($(this).text()); }
    });
})

// Button 'Adicionar' Add tags
$("#add-items").click(function(){
    $("#send-to-options option:selected").each(function () {
        if(!$("#tag-container").tagExist($(this).text())) { $("#tag-container").addTag($(this).text()); }
    });
})  
    
// Clear all Tags
$("#clear-tags").click(function(){
    $('#tag-container').importTags('');
    SelectedGroups = [];
    SelectedUsers = [];
})

// Clear search Options
$("#clear-options").click(function(){
    $("#search-field").val("");
    $("#msg").val("");
    $("#clear-tags").click();
    $("#send-to-options").empty();
    if($( "#uniform-send-to-user span" ).hasClass("checked")){
    $.each(UsersValue, function(index, value){
        $("#send-to-options").append("<option value='"+UsersValue[index]+"'>"+UsersDescription[index]+"</option>");
    })
    VisibleList = "users"; 
    } else {
    $.each(GroupsValue, function(index, value){
        $("#send-to-options").append("<option value='"+GroupsValue[index]+"'>"+GroupsDescription[index]+"</option>");
    })       
    VisibleList = "groups";
    }
}) 
    
// Radio change to Groups
$("#send-to-group").click(function(){
    $("#send-to-options").empty();
    $.each(GroupsValue, function(index, value){
        $("#send-to-options").append("<option value='"+GroupsValue[index]+"'>"+GroupsDescription[index]+"</option>");
    })
    VisibleList = "groups";
    $("#search-field").val("");
    $("#clear-tags").click();
})
    
//Radio change to USers    
$("#send-to-user").click(function(){
    $("#send-to-options").empty();
    $.each(UsersValue, function(index, value){
        $("#send-to-options").append("<option value='"+UsersValue[index]+"'>"+UsersDescription[index]+"</option>");
    })
    VisibleList = "users";
    $("#search-field").val("");
    $("#clear-tags").click();
})

// Search field
$("#search-field").bind("input", function(){
    var WorkListValue;
    var WorkListDescription;
    if(VisibleList == "users"){ WorkListValue = UsersValue; WorkListDescription = UsersDescription; } else { WorkListValue = GroupsValue; WorkListDescription = GroupsDescription; }
    $("#send-to-options").empty();
    $.each(WorkListValue, function(index, value){
        if(WorkListDescription[index].toLowerCase().match($("#search-field").val().toLowerCase())){$("#send-to-options").append("<option value='"+WorkListValue[index]+"'>"+WorkListDescription[index]+"</option>");}
    })
}) 

// Load Groups and Users
$(document).ready(function(){
   
    $.ajax({
        type: "POST",
        url: "requests.php",
        dataType : "JSON",
        data: { action: "get_user_groups" },
        success: function(data) {
            $.each(data.user_groups.value, function(index, value){
                $("#send-to-options").append("<option value='"+data.user_groups.value[index]+"'>"+data.user_groups.description[index]+"</option>");
            })
            GroupsValue = data.user_groups.value;
            GroupsDescription = data.user_groups.description; 
        }    
    });
        
    $.ajax({
        type: "POST",
        url: "requests.php",
        dataType : "JSON",
        data: { action: "get_users" },
        success: function(data) {
            //console.log(data);
            UsersValue = data.users.value;
            UsersDescription = data.users.description; 
        }
    });
})
        
    
    
</script>


<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>