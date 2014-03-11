
var chatSocket, chatSocketStatus, chatHistory = new Array();
function startChatSocket(ip, sentUser, sentDomain) {

    chatSocket = new Primus(ip, '50002');
    chatSocketStatus = false;
    user = sentUser;
    domain = sentDomain;
    myip = ip;

    // user socket connection status
    chatSocket.on('error', function() {
        if (chatSocketStatus) {
            $.smallBox({
                title: "Can't connect to chat server!",
                content: "Inform system administrator please",
                color: "#B22222",
                iconSmall: "fa fa-msg",
                timeout: 5000
            });
        }
        chatSocketStatus = false;
    });
    chatSocket.on('open', function() {
        chatSocketStatus = true;
        getFriends(user, domain);
    });
    chatSocket.on('end', function() {
        $.smallBox({
            title: "Unable to restablish connection to chat server!",
            content: "Inform system administrator please",
            color: "#B22222",
            iconSmall: "fa fa-msg",
            timeout: 5000
        });
        chatSocketStatus = false;
    });
    chatSocket.on('reconnecting', function() {
        if (chatSocketStatus) {
            $.smallBox({
                title: "You have been disconnected from the chat server!",
                content: "Trying to reconnect...",
                color: "#B22222",
                iconSmall: "fa fa-msg",
                timeout: 5000
            });
        }
        chatSocketStatus = false;
    });

    chatSocket.on('data', function(msg) {
        chatSocketSwitch(msg);
    });
    chatSocket.write({action: 'Chat->init', user: user, domain: domain})
}

function getFriends(user, domain) {
    chatSocket.write({action: 'Chat->getFriends', user: user, domain: domain});
}

function sendMessage(target, domain, message, user) {
    if (target && domain && message && user) {
        chatSocket.write({action: 'Chat->newMessage', user: user, target: target, domain: domain, message: message});
    }

}

function chatSocketSwitch(msg) {
    switch (msg.action) {
        case 'newMessage' :
            newMessage(msg);
            break;
        case 'friendList' :
            buildFriendList(msg.friendList);
            break;
    }
}

function buildFriendList(friendList) {
    $(".friends-body").html('');
    for (val in friendList) {
        if (friendList[val].user) {
            if (friendList[val].user == user) {
                fullName = friendList[val].firstName + ' ' + friendList[val].lastName;
            } else {
                $(".friends-body").append('<li class="chat-user" data-avatar="' + friendList[val].avatar + '" data-name="' + friendList[val].firstName + ' ' + friendList[val].lastName + '" data-username="' + friendList[val].user + '" ><span><a href="javascript:void(0);" class="msg"><img src="' + friendList[val].avatar + '" alt="" class="air air-top-left margin-top-5" width="40" height="40" /><span class="from">' + friendList[val].firstName + ' ' + friendList[val].lastName + '</span><time>' + moment(friendList[val].time, 'YYYYMMDD HH:mm:ss').fromNow() + '</time><span class="online-bullet" ></span><span class="subject">Online</span></span></li>')
            }
        }
    }

    $(".chat-user").off();
    $(".chat-user").on('click', function() {
        openChatBox(this);
    })

    $(".friends-body").show();



}


function newMessage(msg) {
    msg.time = moment().format('YYYYMMDD HH:mm:ss');

    if ($("." + msg.from)) {
        $("." + msg.from).remove();
    }

    var thisMessage = '<li class="' + msg.from + '" data-avatar="' + msg.avatar + '" data-username="' + msg.from + '" data-name="' + msg.firstName + ' ' + msg.lastName + '" ><span class="unread"><a href="javascript:void(0);" class="msg"><img src="' + msg.avatar + '" alt="" class="air air-top-left margin-top-5" width="40" height="40" /><span class="from">' + msg.firstName + ' ' + msg.lastName + ' <i class="icon-paperclip"></i></span><time>' + moment(msg.time, 'YYYYMMDD HH:mm:ss').fromNow() + '</time><span class="subject">' + msg.msg + '</span>';
    $(".quick-chat").append(thisMessage);
    $(".quick-chat ." + msg.from).addClass('animated shake');
    setTimeout(function() {
        $(".quick-chat ." + msg.from).removeClass('animated shake');
    }, 2000);
    if (!$(".quick-chat").is(':visible')) {
        $("#activity").addClass('animated wobble');
        setTimeout(function() {
            $("#activity").removeClass('animated wobble');
        }, 4000);
    }
    var fromElement = $("." + msg.from);
    $("." + msg.from).off();
    $("." + msg.from).on('click', function() {
        openChatBox(fromElement);
    })


    openChatBox(fromElement, function() {
        addNewChatMessage(msg.from, msg.msg, msg.firstName + ' ' + msg.lastName, msg.avatar, 'receive');
    });


}

function openChatBox(element, callback) {
    if (!$("#chat-window-" + $(element).data().username)[0]) {
        var newChatBox = '';
        newChatBox = newChatBox + '<div data-avatar="' + $(element).data().avatar + '" data-name="' + $(element).data().name + '" data-username="' + $(element).data().username + '" class="chat-box animated fadeInDown " id="chat-window-' + $(element).data().username + '" style="margin-left: 10px">';
        newChatBox = newChatBox + '<label class="cursor-pointer chat-box-title" data-expanded="' + $(element).data().name + '" data-collapsed="' + $(element).data().name + '">';
        newChatBox = newChatBox + '<i class="fa fa-power-off pull-right padding-5 chat-box-off"></i>';
        newChatBox = newChatBox + '</label>';
        newChatBox = newChatBox + '<div class="chat-box-content" style="display: inline">';
        newChatBox = newChatBox + '<div class="chat-message-body" style="height: 200px; max-height: 200px; overflow-y: scroll"></div>';
        newChatBox = newChatBox + '<div class="row smart-form">';
        newChatBox = newChatBox + '<div class="col col-sm-12">';

        newChatBox = newChatBox + '<label class="input">';
        newChatBox = newChatBox + '<input type="text" placeholder="new message..." class="new-chat-message" />';
        newChatBox = newChatBox + '</label>';
        newChatBox = newChatBox + '</div>';
        newChatBox = newChatBox + '</div>';
        newChatBox = newChatBox + '</div>';
        newChatBox = newChatBox + '</div>';

        $("#chat-box-holder").prepend(newChatBox);
        $(".chat-box-title").off();
        $(".chat-box-title").on('click', function() {
            var chatBoxContent = $(this).parent().find('.chat-box-content');
            if (chatBoxContent.hasClass('closed-chat-window')) {
                chatBoxContent.removeClass('closed-chat-window');
                chatBoxContent.add().attr('style', 'display: inline');
                chatBoxContent.addClass('open-chat-window');
            } else {
                chatBoxContent.addClass('closed-chat-window');
                chatBoxContent.add().attr('style', 'display: none');
                chatBoxContent.removeClass('open-chat-window');
            }
        });
        $(".new-chat-message").unbind();
        $(".new-chat-message").bind('keydown', function(e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                var msg = $(this).val();
                var msgElement = $(this).parent().parent().parent().parent().parent().data();
                $(this).val('');

                addNewChatMessage(msgElement.username, msg, fullName, avatar, 'sending');

            }
        });
        $(".chat-box-off").off();
        $(".chat-box-off").on('click', function() {
            $(this).parent().parent().remove();
        });

        var targetUser = $(element).data().username;
        if (!chatHistory[targetUser]) {
            chatHistory[targetUser] = new Array();
        } else {
            var currentLog = chatHistory[targetUser];
            if (currentLog) {
                for (val in currentLog) {
                    if (!currentLog[val].fromMe) {
                        addNewChatMessage(currentLog[val].user, currentLog[val].msg, currentLog[val].fullName, currentLog[val].avatar, 'receive', true)
                    } else {
                        addNewChatMessage(currentLog[val].user, currentLog[val].msg, currentLog[val].fullName, currentLog[val].avatar, 'sending', true)
                    }
                }
            }
        }


    } else {
        var chatWindowContent = $("#chat-window-" + $(element).data().username);
        if ($("#chat-window-kant").children().hasClass("closed-chat-window")) {
            chatWindowContent.removeClass('fadeInDown');
            chatWindowContent.removeClass('animated shake');
            setTimeout(function() {
                chatWindowContent.addClass('animated shake')
            }, 200);
        }
    }
    if (callback) {
        callback();
    }

}

function addNewChatMessage(target, msg, targetName, targetAvatar, action, history) {
    var time = moment().format('YYYYMMDD HH:mm:ss')
    if (action == 'receive') {
        // receiving message
        var newMessage = '';
        newMessage = newMessage + '<div class="from-them text-align-left padding-5">';
        newMessage = newMessage + '<span >';
        newMessage = newMessage + '<img src="' + targetAvatar + '" alt="me" class="online chat-message-img">';
        newMessage = newMessage + '<a href="javascript:void(0);" class="margin-left-5"> ' + targetName + '</a>';
        newMessage = newMessage + '</span>';
        newMessage = newMessage + '<p class="margin-top-5 margin-left-5">' + msg + '</p>';
        newMessage = newMessage + '<hr>';
        newMessage = newMessage + '</div>';
        var msgBody = $("#chat-window-" + target).find('.chat-message-body');
        msgBody.append(newMessage);
        msgBody.scrollTop(msgBody[0].scrollHeight);
        if (!history) {
            if (!chatHistory[target]) {
                chatHistory[target] = new Array();
            }
            chatHistory[target].push({msg: msg, fullName: targetName, avatar: targetAvatar, time: time, user: target, fromMe: false});
        }
        //$("#activity .badge").html(chatHistory.length);

    } else {

        // sending message
//        var textMessage = element.find('.new-chat-message').val();
//        element.find('.new-chat-message').val('');
        var newMessage = '';
        newMessage = newMessage + '<div class="from-me text-align-right padding-5">';
        newMessage = newMessage + '<span >';
        newMessage = newMessage + '<a href="javascript:void(0);" class="margin-right-5"> ' + fullName + ' </a>';
        newMessage = newMessage + '<img src="' + avatar + '" alt="me" class="online chat-message-img ">';
        newMessage = newMessage + '</span>';
        newMessage = newMessage + '<p class="margin-top-5 margin-right-5">' + msg + '</p>';
        newMessage = newMessage + '<hr>';
        newMessage = newMessage + '</div>';
        var msgBody = $("#chat-window-" + target).find('.chat-message-body');
        msgBody.append(newMessage);
        msgBody.scrollTop(msgBody[0].scrollHeight);
        if (!history) {
            if (!chatHistory[target]) {
                chatHistory[target] = new Array();
            }
            chatHistory[target].push({msg: msg, name: fullName, avatar: avatar, time: time, user: target, fromMe: true});
            sendMessage(target, domain, msg, user);
        }
    }




}

function chatLoadDivs(elem) {
    switch (elem[0].id) {
        case 'chat-friends' :
            getFriends(user, domain);
            break;
        case 'chat-notify' :
            break;
        case 'chat-history' :
            break;
    }

}