<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Go Contact Center</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-tagmanager.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-tagmanager.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/colorpicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
            <script src="/jquery/jquerytags/tags.js"></script>
            <link rel="stylesheet" type="text/css" href="/jquery/jquerytags/tags.css" />
        <style>
            .chzn-select{
                width: 350px;
            }
            #loader{
                background: #fff;
                top: 0px;
                left: 0px;
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 2;
            }
            #loader > img{
                position:absolute;
                left:50%;
                top:50%;
                margin-left: -33px;
                margin-top: -33px;
            }
            #newRef-modal .modal-body, #newRef-modal{
                overflow:visible;
            }
            .inline{
                margin-right: 6px;
            }
            .formRight{
                max-width: 72% !important;
            }
            div.tagsinput{
                width: 100% !important;
            }
        </style>
    </head>
    <body>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <div class=content>

            <div class="grid-transparent">
                <div class="grid-title">
                    <div class="pull-left">Mensagens para Operadores</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                        <div class="offset1 span4 grid-transparent">

                            <div class="formRow">
                                <label class="control-label">Enviar para:</label>
                                <div class="formRight">
                                    <input type="radio" id="send-to-group" name="send-to[]" checked="checked"/>
                                    <label for="send-to-group" class="inline"><span></span>Grupos</label>
                                    <input type="radio" id="send-to-user" name="send-to[]"/>
                                    <label for="send-to-user" class="inline"><span></span>Operadores</label>
                                </div>
                            </div>

                            <div class="formRow">
                                <label class="control-label" for="search-field">Procurar:</label>
                                <div class="formRight">
                                    <input id="search-field" type="text">
                                </div>
                            </div>  

                            <div class="formRow">
                                <div class="formRight">
                                    <select multiple id="send-to-options" style="height:250px;"></select> 
                                </div>
                            </div>        

                            <div class="btn-group right">
                                <button id="clear-options" class="btn btn-warning b2"><i class="icon-remove"></i>Limpar</button>
                                <button id="add-items" class="btn btn-success b2"><i class="icon-plus"></i>Adicionar</button>
                            </div>             

                        </div>

                        <div class="span6 grid-transparent">

                            <div class="formRow">
                                <label class="control-label">Tipo de Mensagem:</label>
                                <div class="formRight">
                                    <input type="radio" id="msg-type-alert" name="msg-type[]" checked="checked"/>
                                    <label for="msg-type-alert" class="inline"><span></span>Alerta</label>
                                    <input type="radio" id="msg-type-scroll" name="msg-type[]"/>
                                    <label for="msg-type-scroll" class="inline"><span></span>Rodapé</label>
                                </div>
                            </div>
                            
                            <div class="formRow">
                                <div class="product-delete" id="clear-tags"  style="left: 50px;position: relative;top: 40px;display:none"><a href="#"><span><i class="icon-remove"></i></span></a></div>
                                <div class="right" id="tag-container" ></div> 
                            </div>

                            <div class="formRow">
                                <textarea id="msg" style="resize: none; width: 100%; height: 188px"></textarea>
                            </div> 

                            <div class="formRow">    
                               <span id="info-msg" style="float: left; margin-left: 3px"></span> <button id="send-msg" class="btn btn-primary right"><i class="icon-comments"></i>Enviar</button>
                            </div>  

                        </div>



                        <div class="clear"></div>
                    </div>
                </div>
            </div>




            <script>

                // Inicialization
                var UsersValue;
                var UsersDescription;

                var GroupsValue;
                var GroupsDescription;

                var SelectedGroups = [];
                var SelectedUsers = [];

                var VisibleList = "groups";
                var curUser = "<?= $_SERVER['PHP_AUTH_USER']; ?>";

                // Tags    
                $("#tag-container").tagsInput({
                    'width' :"100%",
                    'defaultText': "",
                    'interactive': false,
                    'onAddTag': function(a) {
                        var Tag = a;
                        if (VisibleList === "groups") {
                            $.each(GroupsDescription, function(index, value) {
                                if ($.trim(Tag) === $.trim(value)) {
                                    SelectedGroups[index] = 1;
                                }
                            });
                        } else {
                            $.each(UsersDescription, function(index, value) {
                                if ($.trim(Tag) === $.trim(value)) {
                                    SelectedUsers[index] = 1;
                                }
                            });
                        }
                        $("#clear-tags").show();
                    },
                    'onRemoveTag': function(a) {
                        var Tag = a;

                        $.each(GroupsDescription, function(index, value) {
                            if ($.trim(Tag) === $.trim(value)) {
                                SelectedGroups[index] = undefined;
                            }

                        });

                        $.each(UsersDescription, function(index, value) {
                            if ($.trim(Tag) === $.trim(value)) {
                                SelectedUsers[index] = undefined;
                            }

                        });
                    }
                });

                // Submit Message
                $("#send-msg").click(function() {
                    var SentUsers = [];
                    var SentGroups = [];
                    var SentMsg = $("#msg").val();
                    var SentMsgType;

                    if ($("#tag-container").tagExist('')) {
                        $("#info-msg").html("<b>Por favor escolha pelo menos um destinatário.</b>");
                        console.log("destination");
                        return false;
                    }

                    if ($("#msg").val().length < 1) {
                        $("#info-msg").html("<b>Por favor escreva uma mensagem.</b>");
                        console.log("msg");
                        return false;
                    }



                    if ($("#msg-type-alert").prop("checked")) {
                        SentMsgType = "alert";
                    } else {
                        SentMsgType = "fixed";
                    }


                    if ($("#send-to-user").prop("checked")) {
                        $.each(SelectedUsers, function(index, value) {
                            if (SelectedUsers[index] === 1) {
                                SentUsers.push(UsersValue[index]);
                            }
                        });
                    } else {
                        $.each(SelectedGroups, function(index, value) {
                            if (SelectedGroups[index] === 1) {
                                SentGroups.push(GroupsValue[index]);
                            }
                        });
                    }


                    $.post("requests.php", {action: "submit_msg", sent_users: SentUsers, sent_groups: SentGroups, sent_msg: SentMsg, sent_from: curUser, sent_msg_type: SentMsgType}, function(data) {
                        $("#info-msg").html("<b>Mensagem enviada com sucesso.</b>");
                           }
                    , "json");
                });



                // Double Click Add tags 
                $("#send-to-options").dblclick(function() {
                    $("#send-to-options option:selected").each(function() {
                        if (!$("#tag-container").tagExist($(this).text())) {
                            $("#tag-container").addTag($(this).text());
                        }
                    });
                });

                // Button 'Adicionar' Add tags
                $("#add-items").click(function() {
                    $("#send-to-options option:selected").each(function() {
                        if (!$("#tag-container").tagExist($(this).text())) {
                            $("#tag-container").addTag($(this).text());
                        }
                    });
                });

                // Clear all Tags
                $("#clear-tags").click(function() {
                    $('#tag-container').importTags('');
                    SelectedGroups = [];
                    SelectedUsers = [];
                });

                // Clear search Options
                $("#clear-options").click(function() {
                    $("#search-field").val("");
                    $("#msg").val("");
                    $("#clear-tags").click();
                    $("#send-to-options").empty();
                    if ($("#send-to-user").prop("checked")) {
                        $.each(UsersValue, function(index, value) {
                            $("#send-to-options").append($("<option>").val(UsersValue[index]).text(UsersDescription[index]));
                        })
                        VisibleList = "users";
                    } else {
                        $.each(GroupsValue, function(index, value) {
                            $("#send-to-options").append($("<option>").val(GroupsValue[index]).text(GroupsDescription[index]));
                        });
                        VisibleList = "groups";
                    }
                });

                // Radio change to Groups
                $("#send-to-group").click(function() {
                    $("#send-to-options").empty();
                    $.each(GroupsValue, function(index, value) {
                        $("#send-to-options").append($("<option>").val(GroupsValue[index]).text(GroupsDescription[index]));
                    });
                    VisibleList = "groups";
                    $("#search-field").val("");
                    $("#clear-tags").click();
                });

                //Radio change to USers    
                $("#send-to-user").click(function() {
                    $("#send-to-options").empty();
                    $.each(UsersValue, function(index, value) {
                        $("#send-to-options").append($("<option>").val(UsersValue[index]).text(UsersDescription[index]));
                    });
                    VisibleList = "users";
                    $("#search-field").val("");
                    $("#clear-tags").click();
                });

                // Search field
                $("#search-field").bind("input", function() {
                    var WorkListValue;
                    var WorkListDescription;
                    var that=this;
                    if (VisibleList == "users") {
                        WorkListValue = UsersValue;
                        WorkListDescription = UsersDescription;
                    } else {
                        WorkListValue = GroupsValue;
                        WorkListDescription = GroupsDescription;
                    }
                    $("#send-to-options").empty();
                    $.each(WorkListValue, function(index, value) {
                        if (WorkListDescription[index].toLowerCase().match($(that).val().toLowerCase())) {
                            $("#send-to-options").append($("<option>").val(WorkListValue[index]).text(WorkListDescription[index]));
                        }
                    });
                });

                // Load Groups and Users
                $(function() {

                    $.post("requests.php", {action: "get_user_groups",curUser:curUser}, function(data) {
                        $.each(data.user_groups.value, function(index, value) {
                            $("#send-to-options").append($("<option>").val(data.user_groups.value[index]).text(data.user_groups.description[index]));
                        });
                        GroupsValue = data.user_groups.value;
                        GroupsDescription = data.user_groups.description;
                    }
                    , "json");

                    $.post("requests.php", {action: "get_users",curUser:curUser}, function(data) {
                        //console.log(data);
                        UsersValue = data.users.value;
                        UsersDescription = data.users.description;
                    }
                    , "json");

                    $("#loader").fadeOut("slow");
                });



            </script>


    </body>
</html>