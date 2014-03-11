var $body = $('body');
var $voiceWorkspace = $('#voice-workspace');
var myself, user, domain, myip, bypassNavigation = false, userSocket, userSocketStatus;

//socket
function startUserSocket (ip, sentUser, sentDomain) {
    userSocket = new Primus (ip, '50000');
    userSocketStatus = false;
    myself = user + '@' + domain;
    user = sentUser;
    domain = sentDomain;
    myip = ip;

    // user socket connection status
    userSocket.on('error', function () {
        if (userSocketStatus) {
            $.smallBox({
                title : "Can't connect to main server!",
                content : "Inform system administrator please",
                color : "#B22222",
                iconSmall : "fa fa-exclamation",
                timeout : 5000
            });
        }
        userSocketStatus = false;
        disconnectUser();
    });
    userSocket.on('open', function () {
        connectUser();
        userSocketStatus = true;

    });
    userSocket.on('end', function () {
        $.smallBox({
            title : "Unable to restablish connection to main server!",
            content : "Inform system administrator please",
            color : "#B22222",
            iconSmall : "fa fa-exclamation",
            timeout : 5000
        });
        userSocketStatus = false;
        disconnectUser();
    });
    userSocket.on('reconnecting', function () {
        if (userSocketStatus) {
            $.smallBox({
                title : "You have been disconnected from the server!",
                content : "Trying to reconnect...",
                color : "#B22222",
                iconSmall : "fa fa-exclamation",
                timeout : 5000
            });
        }
        userSocketStatus = false;
        reconnectUser();
    });

    userSocket.on('data', function (msg) {
        userSocketSwitch(msg);
    });
    userSocketWrite({
        action : 'User->register',
        user : user,
        domain : domain
    });

}

var userSocketWrite = function (obj) {
    userSocket.write(obj);
}
function userSocketSwitch (msg) {
    console.log(msg);
    switch (msg.action) {
    case 'friendList' :
        populateFriends(msg);
        break;

    case 'Main->sendBreaks' :
        mBreaks.build(msg.oData, msg.iUsersOnBreak, msg.iGroupSize, msg.sGroupName);
        break;

    case 'Main->sendUserList' :
        headerGraphs(msg.list);
        break;

    case "Main->unlockSuccessful":
        mLockScreen.unlock();
        break;

    case "Main->unlockUnsuccessful":
        mLockScreen.unlockFail();
        break;


     case "Main->startVoice":
        loadVoice(msg.outboundInfo, msg.inboundInfo);
        break;


            case "Voice->sipPhoneNoPickup":
        sipPhoneNoPickup();
        break;
    case "Voice->sipPhoneNoAnswer":
        sipPhoneNoAnswer();
        break;
    case "Voice->sipPhoneRejected":
        sipPhoneRejected();
        break;



    case "Voice->sipPhoneReconnectOK":
        sipPhoneReconnect();
        break;

    }
}

function bypassNav (channel) {
    $('nav li.active').removeClass("active");
    bypassNavigation = true;
    newHash = window.location.hash.split('#');
    window.location.hash = newHash[0] + channel;
    $('#content').attr('channel-loaded', channel);
}

function disconnectUser () {
    $("#connectionStatus").html('<a href="javascript:void(0);" id="server-connection-status" rel="tooltip" data-placement="left" data-original-title="Disconnect from main server"><i class="fa fa-code icon-pulse"></i> </a>');

    $("[rel=tooltip]").tooltip();
}

function connectUser () {
    if (!userSocketStatus) {
        $.smallBox({
            title : "Connected to main server!",
            content : "All systems are online",
            color : "#228B22",
            iconSmall : "fa fa-exclamation",
            timeout : 2000
        });
    }
    $("#connectionStatus").html('<a href="javascript:void(0);" id="server-connection-status" rel="tooltip" data-placement="left" data-original-title="Connected to main server"><i class="fa fa-cloud"></i> </a>');
    $("[rel=tooltip]").tooltip();
}

function reconnectUser () {
    $("#connectionStatus").html('<a href="javascript:void(0);" id="server-connection-status" rel="tooltip" data-placement="left" data-original-title="Reconnecting to main server"><i class="fa fa-cog fa-spin"></i> </a>');
    $("[rel=tooltip]").tooltip();

}

/*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*  */

// module breaks
var mBreaks = ( function () {

        var oBreak = {}, oCurrentClock = {
            id : '',
            fTime : '',
            sCurrentTime : ''
        }, eDropdown = $('#shortcut'), eMenu = $('#dropdown-breaks'), eHeaderBtns = $('.break-header-btn'), eLockScreenTime = $('#lock-screen-time'), eUserLabel = $('#show-shortcut'), eBreakBoxes, eBreaksBtn = $('#breaks-show-btn'), iUsersOnBreak, iGroupSize, sGroupName;

        function fShow () {
            eDropdown.animate({
                height : "show"
            }, 200, "easeOutCirc")
        }

        function fHide () {
            eDropdown.animate({
                height : "hide"
            }, 300, "easeOutCirc");
        }

        function fBuild () {
            var sIndex, sClasses, sIcons, sLabel, sTimeStatusLabel, sProduct = '', sStyle, sTime;
            for (sIndex in oBreak) {
                sClasses = '';
                sIcons = '';

                if (moment().isBefore(moment().format('YYYY-MM-DD') + ' ' + oBreak[sIndex].dStartTime) || moment().isAfter(moment().format('YYYY-MM-DD') + ' ' + oBreak[sIndex].dEndTime)) {
                    sClasses += ' break-out-of-schedule bg-color-darken';
                }

                if (oBreak[sIndex].bHasPermission) {
                    sClasses += ' break-with-permission ';
                    sIcons += '<div class="menu-breaks-icon fa fa-comment"></div>';
                } else {
                    sIcons += '<div class="menu-breaks-icon fa fa-coffee"></div>';
                }

                if (oBreak[sIndex].iTime > 0) {
                    sClasses += ' break-with-time ';
                    sIcons += '<div class="menu-breaks-icon fa fa-clock-o"></div>';
                    sTimeStatusLabel = ' left'
                } else {
                    sClasses += ' break-without-time bg-color-red ';
                    sIcons += '<div class="menu-breaks-icon fa fa-ban"></div>';
                    sTimeStatusLabel = ' over';
                }

                if (oCurrentClock.id === sIndex) {
                    sStyle = 'style="font-size: 28px; margin-top:24px"';
                    sTime = oCurrentClock.sCurrentTime;

                    if (oBreak[sIndex].oTime.asMilliseconds() < 0) {
                        sLabel = 'time over';
                        sClasses += ' bg-color-red break-active ';
                    } else {
                        sLabel = 'time left';
                        sClasses += ' bg-color-orange break-active ';
                    }

                } else {
                    sStyle = '';
                    sTime = Math.ceil(Math.abs(oBreak[sIndex].iTime / 60))
                    sClasses += ' bg-color-greenLight ';
                    oBreak[sIndex].oTime = moment.duration(oBreak[sIndex].iTime, 'seconds')

                    if (Math.abs(oBreak[sIndex].oTime.asMinutes()) <= 1 && Math.abs(oBreak[sIndex].oTime.asMinutes()) > 0) {
                        sLabel = 'minute' + sTimeStatusLabel;
                    } else {
                        sLabel = 'minutes' + sTimeStatusLabel;
                    }

                }

                sProduct += '<li><a id="break-id-' + sIndex + '" href="javascript:void(0)" class="jarvismetro-tile big-cubes break-box ' + sClasses + '"><span class="iconbox"> <span class="menu-breaks-time" ' + sStyle + ' >' + sTime + '</span>  <span class="menu-breaks-time-label"> ' + sLabel + ' </span><div class="menu-breaks-icon-container">' + sIcons + '</div> <span class="menu-breaks-name">' + oBreak[sIndex].sName + '</span> </span></a></li>';
            }
            eMenu.html(sProduct);
            eBreakBoxes = $('.break-box');
            eBreakBoxes.off().on('click', fControl);
            fShow();
            fBuildGroupInfo();
        }

        function fBuildGroupInfo () {
            var sProduct;
            sProduct = '<li style="float:right"><a href="javascript:void(0)" class="jarvismetro-tile big-cubes bg-color-blue"><span class="iconbox"><div class="menu-breaks-user-count"><div id="menu-breaks-user-count-number" style="display:inline">' + iUsersOnBreak + '</div><div style="display:inline">/' + iGroupSize + '</div></div>  <span class="menu-breaks-user-count-label">on break</span></span><div style="margin-top: 24px">' + sGroupName + '</div></a></li>';
            eMenu.append(sProduct)

        }

        function fIncreaseUsersOnBreak () {
            $('#menu-breaks-user-count-number').html(parseInt($('#menu-breaks-user-count-number').html()) + 1)
        }

        function fDecreaseUsersOnBreak () {
            $('#menu-breaks-user-count-number').html(parseInt($('#menu-breaks-user-count-number').html()) - 1)
        }

        function fControl () {
            var eThis = $(this), sSplitContainer = eThis.attr('id').split('break-id-'), id = sSplitContainer[1], fStartBreak = function () {
                if (eThis.hasClass('break-with-permission')) {
                    fEnableWithPermission(eThis)
                } else {
                    fEnableWithoutPermission(eThis, id);
                    fStartClock(id);
                    fToggleHeaderBtnss();
                    fIncreaseUsersOnBreak();
                }
            }
            // break out of schedule
            if (eThis.hasClass('break-out-of-schedule')) {
                return;
            }

            // break without time & active
            if (eThis.hasClass('break-without-time') && eThis.hasClass('break-active')) {
                fDisableBreak(eThis, id);
                fStopClock(id);
                fToggleHeaderBtnss();
                return;
            }
            // break without time
            if (eThis.hasClass('break-without-time')) {
                return;
            }
            // stop break
            if (eThis.hasClass('break-active')) {
                fDisableBreak(eThis, id);
                fStopClock(id);
                fToggleHeaderBtnss();
                return;
            }
            // switch break
            if (eBreakBoxes.hasClass('break-active') && !eThis.hasClass('break-active')) {

                var eActiveBreakBox = $('.break-box.break-active'), sSplitContainerTwo = eActiveBreakBox.attr('id').split('break-id-'), idActiveBreakBox = sSplitContainerTwo[1];

                fSwitchClock(idActiveBreakBox, id)
                fDisableBreak(eActiveBreakBox, idActiveBreakBox);
                fStopClock(id);
                fToggleHeaderBtnss();
                fStartBreak();
                return;
            }
            // permission pending
            if (eBreakBoxes.hasClass('break-asking-for-permission') && !eThis.hasClass('break-asking-for-permission')) {
                return;
            }

            // cancel asking permission
            if (eThis.hasClass('break-asking-for-permission')) {
                fDisableBreak(eThis, id);
                return;
            }
            // start break
            fStartBreak();
        }

        function fEnableWithoutPermission (eElement, id) {
            eElement.removeClass('bg-color-teal bg-color-greenLight').addClass('break-active bg-color-orange').find('.menu-breaks-time').css('font-size', '28px').css('margin-top', '24px').html(fHours(id) + ':' + fMinutes(id) + ':' + fSeconds(id)).next().html('time left');
        }

        function fEnableWithPermission (eElement) {
            eElement.removeClass('bg-color-orange bg-color-greenLight').addClass('break-asking-for-permission bg-color-teal');
        }

        function fDisableBreak (eElement, id) {
            var sLabel, sColor, sTimeStatusLabel;
            if (oBreak[id].oTime.asMinutes() <= 1) {
                sLabel = 'minute left';
                sColor = 'bg-color-greenLight';
            } else if (oBreak[id].oTime.asMinutes() > 1) {
                sLabel = 'minutes left';
                sColor = 'bg-color-greenLight';
            }
            if (oBreak[id].oTime.asMinutes() < 0 && Math.abs(oBreak[id].oTime.asMinutes()) <= 1) {
                sLabel = 'minute over';
                sColor = 'bg-color-red';
            } else if (oBreak[id].oTime.asMinutes() < 0 && Math.abs(oBreak[id].oTime.asMinutes()) >= 1) {
                sLabel = 'minutes over';
                sColor = 'bg-color-red';
            }
            eElement.removeClass('bg-color-orange bg-color-teal break-asking-for-permission break-active').addClass(sColor).find('.menu-breaks-time').css('font-size', '40px').css('margin-top', '8px').html(Math.ceil(Math.abs(oBreak[id].oTime.asMinutes()))).next().html(sLabel);

            eUserLabel.css('color', '#A8A8A8').html(fullName).removeClass('on-break');
            fDecreaseUsersOnBreak();
        }

        function fStartClock (id) {

            userSocketWrite({
                action : 'Main->startBreak',
                user : user,
                domain : domain,
                id : id,
                name : oBreak[id].sName
            });

            oCurrentClock = {
                id : id,
                fTimer : setInterval(function () {
                    fClock(id);
                }, 1000),
                sCurrentTime : ''
            }
        }

        function fStopClock (id) {

            userSocketWrite({
                action : 'Main->stopBreak',
                user : user,
                domain : domain,
                id : id,
                name : oBreak[id].sName

            });

            clearInterval(oCurrentClock.fTimer);
            oCurrentClock = {
                id : '',
                fTimer : '',
                sCurrentTime : ''
            }

        }

        function fSwitchClock (idToStop, idToStart) {

            userSocketWrite({
                action : 'Main->switchBreaks',
                user : user,
                domain : domain,
                stopBreakID : idToStop,
                stopBreakName : oBreak[idToStop].sName,
                startBreakID : idToStart,
                startBreakName : oBreak[idToStart].sName
            });
        }

        function fClock (id) {

            var sTimeResult;

            oBreak[id].oTime = moment.duration(oBreak[id].oTime.asMilliseconds() - 1000, 'milliseconds');
            sTimeResult = fHours(id) + ':' + fMinutes(id) + ':' + fSeconds(id);
            oCurrentClock.sCurrentTime = sTimeResult;

            // break box update
            if (oBreak[id].oTime.asMilliseconds() < 0 && !$('#break-id-' + id).hasClass('break-with-no-time')) {
                $('#break-id-' + id).removeClass('bg-color-orange break-with-time').addClass('bg-color-red break-without-time').find('.menu-breaks-time').html(sTimeResult).next().html('time over').next().children().eq(1).removeClass('fa-clock-o').addClass('fa-ban');

                eLockScreenTime.html('<i class="fa fa-ban" style="font-color:red"></i>&nbsp;&nbsp;<span style="color:red">' + sTimeResult + '</span> &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-coffee"></i>&nbsp;' + oBreak[id].sName)

                eUserLabel.css('color', 'red').html('Overtime: ' + sTimeResult).addClass('on-break');

            } else {
                $('#break-id-' + id).find('.menu-breaks-time').html(sTimeResult);
                eLockScreenTime.html('<i class="fa fa-clock-o"></i>&nbsp;&nbsp;' + sTimeResult + ' &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-coffee"></i>&nbsp;' + oBreak[id].sName)
                eUserLabel.html('Paused: ' + sTimeResult).addClass('on-break');
            }
        }

        function fToggleHeaderBtnss () {
            if (eHeaderBtns.css('color') === 'rgb(199, 145, 33)') {
                eHeaderBtns.css('color', 'rgb(109, 106, 105)')
            } else {
                eHeaderBtns.css('color', 'rgb(199, 145, 33)')
            }
        }

        function fHours (id) {
            var hours = Math.abs(oBreak[id].oTime.hours()).toString();
            if (hours.length === 1) {
                return '0' + hours;
            } else if (hours.length === 2) {
                return hours;
            } else {
                return '00';
            }
        }

        function fMinutes (id) {
            var minutes = Math.abs(oBreak[id].oTime.minutes()).toString();
            if (minutes.length === 1) {
                return '0' + minutes;
            } else if (minutes.length === 2) {
                return minutes;
            } else {
                return '00'
            }
        };
        function fSeconds (id) {
            var seconds = Math.abs(oBreak[id].oTime.seconds()).toString();
            if (seconds.length === 1) {
                return '0' + seconds;
            } else if (seconds.length === 2) {
                return seconds;
            } else {
                return '00'
            }
        };

        function fRequestData () {
            userSocketWrite({
                action : 'Main->getBreaks',
                user : user,
                domain : domain,
            });
        }

        function fCloseDropdown (e) {
            if (!eDropdown.is(e.target) && eDropdown.has(e.target).length === 0) {
                fHide();
            }
        }

        return {
            init : function () {
                eBreaksBtn.off().on('click', fRequestData)
                $(document).on('mouseup', fCloseDropdown);
                console.warn('Breaks Module started Successfully')
            },
            build : function (oData, oUsersOnBreak, oGroupSize, oGroupName) {
                var sIndex, tDuration;
                for (sIndex in oData) {
                    if ( typeof oBreak[sIndex] !== 'undefined') {
                        tDuration = oBreak[sIndex].oTime;
                        if (oBreak[sIndex].iTime !== oData[sIndex].iTime) {
                            if (oCurrentClock.id === sIndex) {
                                if (oBreak[sIndex].iTime > oData[sIndex].iTime) {
                                    tDuration = moment.duration(oBreak[sIndex].oTime.asMilliseconds() - (parseInt(oBreak[sIndex].iTime) - parseInt(oData[sIndex].iTime)) * 1000, 'milliseconds');
                                } else {
                                    tDuration = moment.duration(oBreak[sIndex].oTime.asMilliseconds() + (parseInt(oData[sIndex].iTime) - parseInt(oBreak[sIndex].iTime) ) * 1000, 'milliseconds');
                                }
                            }
                        }
                    } else {
                        tDuration = '';
                    }
                    oBreak[sIndex] = {
                        sName : oData[sIndex].sName,
                        iTime : oData[sIndex].iTime,
                        dStartTime : oData[sIndex].dStartTime,
                        dEndTime : oData[sIndex].dEndTime,
                        bHasPermission : oData[sIndex].bHasPermission,
                        oTime : tDuration
                    }

                }

                iUsersOnBreak = oUsersOnBreak;
                iGroupSize = oGroupSize;
                sGroupName = oGroupName;

                fBuild();
            },
            isPaused : function () {
                if (eBreakBoxes.hasClass('break-active')) {
                    return true;
                } else {
                    return false;
                }
            },
            stopBreak : function () {
                $('.break-active').click();
            }
        };
    }());
mBreaks.init();
/*
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *  */

var mLockScreen = ( function () {
        var eLockScreenBtn = $('#lock-screen'), eLockScreenHidden = $('.lock-screen-hidden'), eLockScreenContent = $('#lock-screen-content'), eAvatar = $('#lock-screen-avatar'), eUser = $('#lock-screen-user'), eTime = $('#lock-screen-time'), eSubmit = $('#lock-screen-submit'), eLockScreenForm = $('.lock-screen-form'), ePassword = $('#lock-screen-password');

        function fShowLockScreen () {
            if (mBreaks.isPaused()) {
                eLockScreenHidden.addClass('hidden');
                eLockScreenContent.removeClass('hidden');

                eAvatar.attr('src', avatar);
                eUser.html(fullName);
                ePassword.val('').focus();
                eLockScreenForm.addClass('animated flipInY').removeClass('animated flipInY');

                eSubmit.off().on('click', fTryUnlock);
                $(document).on("keydown", fDisableReload)
            }

        }

        function fTryUnlock (e) {
            e.preventDefault();
            eLockScreenForm.removeClass('animated flipInY')
            userSocketWrite({
                action : 'Main->unlockScreen',
                user : user,
                domain : domain,
                password : hex_md5(ePassword.val())
            });
        }

        function fUnlockSuccess () {
            $(document).off("keydown", fDisableReload);
            eSubmit.off('click', fTryUnlock)
            eLockScreenContent.addClass('hidden');
            eLockScreenHidden.removeClass('hidden')
            mBreaks.stopBreak();

        }

        function fUnlockFail () {

            eLockScreenForm.addClass('animated flipInY')
            ePassword.val('').focus();
        }

        function fDisableReload (e) {
            if ((e.which || e.keyCode) == 116) {
                e.preventDefault();
            }
        }

        return {
            init : function () {
                eLockScreenBtn.off().on('click', fShowLockScreen);
                console.warn('Lock Screen Module started successfully.')
            },
            unlock : function () {
                fUnlockSuccess();
            },
            unlockFail : function () {
                fUnlockFail();
            }
        }
    }());
mLockScreen.init();

/*
var breakTimes = [];
var activeBreakTimer = {};

function breakControl () {

var $break = $(this);

// break start
if ($break.hasClass('break-permission')) {
$break.removeClass('break-ready bg-color-greenLight').addClass('break-asking-for-permission bg-color-teal').find('.menu-breaks-icon').removeClass('fa-coffee').addClass('fa-comment-o');
} else {
$break.removeClass('break-ready bg-color-greenLight').addClass('break-active bg-color-orange').find('.menu-breaks-icon').removeClass('fa-coffee').addClass('fa-clock-o');
breakStart($break.attr('id'), $break.find('.menu-breaks-name').html());
$('#main-show-breaks span a i').css('color', '#C79121 !important')
$('#lock-screen span a i').css('color', '#C79121 !important')
}

// break stop
if ($break.hasClass('break-active')) {
if ($break.hasClass('break-overtime')) {
$break.removeClass('break-active bg-color-orange').addClass('bg-color-red').find('.menu-breaks-icon').removeClass('fa-clock-o').addClass('fa-ban');
} else {
$break.removeClass('break-active bg-color-orange').addClass('bg-color-greenLight').find('.menu-breaks-icon').removeClass('fa-clock-o').addClass('fa-coffee');
}

breakStop($break.attr('id'), $break.find('.menu-breaks-name').html())
$('#main-show-breaks span a i').css('color', '#6D6A69 !important')
$('#lock-screen span a i').css('color', '#6D6A69 !important')
return false;
}

if ($break.hasClass('break-asking-for-permission') && !$break.hasClass('break-overtime')) {
$break.removeClass('break-asking-for-permission bg-color-teal').addClass('bg-color-greenLight').find('.menu-breaks-icon').removeClass('fa-comment-o').addClass('fa-coffee');
return false;
}

if ($('.break-box').hasClass('break-asking-for-permission') && !$break.hasClass('break-asking-for-permission')) {
return false;
}

if ($('.break-box').hasClass('break-active') && !$break.hasClass('break-active')) {

breakSwitch($('.break-box.break-active').attr('id'), $('.break-box.break-active').find('.menu-breaks-name').html(), $break.attr('id'), $break.find('.menu-breaks-name').html())

if ($('.break-box.break-active').hasClass('break-overtime')) {
$('.break-box.break-active').removeClass('break-active');
} else {
$('.break-box.break-active').removeClass('break-active bg-color-orange').addClass('bg-color-greenLight').find('.menu-breaks-icon').removeClass('fa-clock-o').addClass('fa-coffee');
}

if ($break.hasClass('break-permission')) {
$break.removeClass('break-ready bg-color-greenLight').addClass('break-asking-for-permission bg-color-teal').find('.menu-breaks-icon').removeClass('fa-coffee').addClass('fa-comment-o');
} else {
$break.removeClass('break-ready bg-color-greenLight').addClass('break-active bg-color-orange').find('.menu-breaks-icon').removeClass('fa-coffee').addClass('fa-clock-o');
$('#main-show-breaks span a i').css('color', '#C79121 !important')
$('#lock-screen span a i').css('color', '#C79121 !important')
}

return false;
}

}

function breakStart (breakID, breakName) {

}

function breakStop (breakID, breakName) {

var split = breakID.split('break-id-'), breakID2 = split[1];

userSocketWrite({
action : 'Main->stopBreak',
user : user,
domain : domain,
breakID : breakID2,
breakName : breakName

});

clearInterval(activeBreakTimer.timer);
activeBreakTimer = {
id : '',
timer : ''
}

$('#show-shortcut').html(firstname + ' ' + lastname).removeClass('on-break');

}

function breakSwitch (stopBreakID, stopBreakName, startBreakID, startBreakName) {

var split1 = stopBreakID.split('break-id-'), breakID1 = split1[1];
var split2 = startBreakID.split('break-id-'), breakID2 = split2[1];

userSocketWrite({
action : 'Main->switchBreaks',
user : user,
domain : domain,
stopBreakID : breakID1,
stopBreakName : stopBreakName,
startBreakID : breakID2,
startBreakName : startBreakName
});
}

function breakClock (breakID, breakName) {

var overtime = false, minutesText;

breakTimes[breakID] = moment.duration(breakTimes[breakID].asMilliseconds() - 1000, 'milliseconds');

if (breakTimes[breakID] < 0) {
overtime = true;
}

var hoursCalc = function () {
var hours = Math.abs(breakTimes[breakID].hours()).toString();
if (hours.length === 1) {
return '0' + hours;
} else if (hours.length === 2) {
return hours;
} else {
return '00'
}
};
var minutesCalc = function () {
var minutes = Math.abs(breakTimes[breakID].minutes()).toString();
if (minutes.length === 1) {
return '0' + minutes;
} else if (minutes.length === 2) {
return minutes;
} else {
return '00'
}
};
var secondsCalc = function () {
var seconds = Math.abs(breakTimes[breakID].seconds()).toString();
if (seconds.length === 1) {
return '0' + seconds;
} else if (seconds.length === 2) {
return seconds;
} else {
return '00'
}
};

// main break menu updating

$('#' + breakID).find('.menu-breaks-time').css('font-size', '28px').css('margin-top', '24px').find('.menu-breaks-time-label').html('time left');

var timeString = hoursCalc() + ':' + minutesCalc() + ':' + secondsCalc();

if (parseInt(hoursCalc()) >= 1) {

if (breakTimes[breakID].asMinutes() === 1) {
minutesText = 'minute';
} else {
minutesText = 'minutes';
}

$('#' + breakID).find('.menu-breaks-time').html(Math.abs(breakTimes[breakID].asMinutes()))
if (overtime) {
$('#' + breakID).removeClass('bg-color-orange').addClass('bg-color-red break-overtime').find('.menu-breaks-time-label').html(minutesText + ' overtime').find('.');
}
}

if ($('#lock-screen-break').length) {
if (overtime) {

}
$('#lock-screen-break').html('<i class="fa fa-clock-o"></i>&nbsp;&nbsp;' + hoursCalc() + ':' + minutesCalc() + ':' + secondsCalc() + ' &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-coffee"></i>&nbsp;' + breakName)
}

$('#show-shortcut').html('Paused: ' + hoursCalc() + ':' + minutesCalc() + ':' + secondsCalc()).addClass('on-break');

}

*/

/*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*  */
// debug
$('body').on('click', '#debug-info', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->info",
            user : user,
            domain : domain
        });
    }
}).on('click', '#debug-sparks', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->sparks",
            user : user,
            domain : domain
        });
    }
}).on('click', '#debug-sip-calls', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->sipCalls",
            user : user,
            domain : domain
        });
    }
}).on('click', '#debug-voice-calls', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->voiceCalls",
            user : user,
            domain : domain
        });
    }
    ///////////////////////////////////////////////////////////
}).on('click', '#debug-show-sparks', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showSparks"
        });
    } else {
        userSocketWrite({
            action : "Debug->showSparks"
        });
    }
}).on('click', '#debug-show-calls', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showCalls"
        });
    } else {
        userSocketWrite({
            action : "Debug->showCalls"
        });
    }
}).on('click', '#debug-show-inbound-calls', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showInboundCalls"
        });
    } else {
        userSocketWrite({
            action : "Debug->showInboundCalls"
        });
    }
}).on('click', '#debug-show-inbound-extensions', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showInboundExtensions"
        });
    } else {
        userSocketWrite({
            action : "Debug->showInboundExtensions"
        });
    }
}).on('click', '#debug-show-inbound-queue', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showInboundQueue"
        });
    } else {
        userSocketWrite({
            action : "Debug->showInboundQueue"
        });
    }
}).on('click', '#debug-show-inbound-groups', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showInboundGroups"
        });
    } else {
        userSocketWrite({
            action : "Debug->showInboundGroups"
        });
    }
}).on('click', '#debug-show-inbound-vipblack', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showInboundVipBlack"
        });
    } else {
        userSocketWrite({
            action : "Debug->showInboundVipBlack"
        });
    }
}).on('click', '#debug-show-inbound-schedules', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showInboundSchedules"
        });
    } else {
        userSocketWrite({
            action : "Debug->showInboundSchedules"
        });
    }
}).on('click', '#debug-show-inbound-dtmf', function () {
    if (userSocketStatus) {
        userSocketWrite({
            action : "Debug->showInboundDtmf"
        });
    } else {
        userSocketWrite({
            action : "Debug->showInboundDtmf"
        });
    }
})
