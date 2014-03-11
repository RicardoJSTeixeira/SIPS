'use strict';
// global vars
var voiceSocket;
var voiceSocketStatus = false;
var voiceSipPhoneStatus = false;
var inboundSelected = 0;
var outboundSelected = 0;
var voiceOutcomes = '123';
var callEnded = false;
var currentcontactID;
var campaignChangeLock = false;
var existsRedial = false;
var voiceCallbacks;
var allVoiceCallbacks;
var currentCallbackID;
// socket management
function startVoiceSocket () {
    voiceSocket = new Primus (myip, '50001');
    voiceSocket.on('error', function () {
        voiceSocketStatus = false;
    });
    voiceSocket.on('open', function () {
        voiceSocketStatus = true;
        voiceSocketWrite({
            action : 'Voice->register',
            user : user,
            domain : domain
        });
    });
    voiceSocket.on('end', function () {
        voiceSocketStatus = false;
    });
    voiceSocket.on('reconnecting', function () {
        voiceSocketStatus = false;
    });
    voiceSocket.on('data', function (msg) {
        voiceSocketSwitch(msg);
    });
}

function stopVoiceSocket () {
    voiceSocket.end();
    jsArray = jsArray.replace('[../node/voice-functions.js]', '');
}

function voiceSocketWrite (obj) {
    voiceSocket.write(obj);
}

function voiceSocketSwitch (msg) {
    console.log(msg);
    switch (msg.action) {
    // login
    case "Voice->manualCallStartRecordingButton":
        onDemandRecordingBtn();
        break;
    case "Voice->sipPhoneLost":
        sipPhoneLost(msg.extension);
        break;
    // init
    /*    case "Main->loadAvailCampaigns":
    loadVoice(msg.outboundInfo, msg.inboundInfo);
    break; */
    /*   case "Voice->buildExtraFields":
    buildExtraFields(msg.fields);
    break; */
    // in call
    case "Voice->newContactInfo":
        newContactInfo(msg.fieldsStructure, msg.fieldsValue, msg.contactID, msg.callType, msg.callbackID, msg.outcomes, msg.onDemand);
        break;
    case "Voice->callRinging":
        callRinging(msg.onDemand);
        break;
    case "Voice->answer":
        answer();
        break;
    case "Voice->buildOutcomes":
        break;
    case "Voice->calleeHangup":
        calleeHangup();
        break;
    case "Voice->errorManualCall":
        errorManualCall(msg.error);
        break;
    // transfer
    case "Voice->transferDialRinging":
        transferDialRinging();
        break;
    case "Voice->transferAnswer":
        transferAnswer();
        break;
    case "Voice->transferDestinationHangup":
        destinationHangup();
        break;
    case "Voice->sendCallbacks":
        voiceCallbacks = msg.callbacks;
        buildCallbacks();
        break;
    }
}

// voice login
function showVoiceLogin (e) {
    if (!voiceSocketStatus) {
        //      startVoiceSocket();
    }
    if (!voiceSipPhoneStatus) {
        $.SmartMessageBox({
            title : "<i class='fa fa-comments txt-color-orangeDark'></i> Voice  <span class='txt-color-orangeDark'><strong>Login</strong></span>",
            content : "Please enter your local extension",
            buttons : "[Login][Cancel]",
            input : "text",
            placeholder : "Enter your extension",
            voiceLogin : true
        }, function (ButtonPress) {
            if (ButtonPress == "Cancel") {
                if (!voiceSipPhoneStatus) {
                    //   stopVoiceSocket();
                }
                return 0;
            }
        });
    } else {
        showVoice();
    }
}

function logoutVoice () {
    $voiceWorkspace.off();
    $('#voice-channel-control').off();
    $('.voice-active-outbound-campaign').editable('destroy');
    $('.voice-active-inbound-campaign').editable('destroy');
    $('#voiceControls-manual').popover('destroy');
    $('#voiceControls-dtmf').popover('destroy');
    $('#voice-workspace').empty().addClass('hidden');
    $('#voice-channel-control').empty().addClass('hidden');
    $('#voiceLoginMenu').addClass('hidden');
    $('#voice-search-results').dataTable('destroy')
    stopVoiceSocket();
    voiceSipPhoneStatus = false;
    inboundSelected = 0;
    outboundSelected = 0;
    callEnded = false;
    currentcontactID = '';
    campaignChangeLock = false;
    existsRedial = false;
    $('nav ul li a:first').click();
}

function validateSipPhone () {
    if ($('#validateSipPhoneInput').val() && userSocketStatus) {
        $('#validateSipPhoneError').html('<i class="fa fa-cog fa-spin"></i> Calling your Sip Phone...');
        $('#validateSipPhone').prop('disabled', true);
        $('#cancelValidateSipPhone').prop('disabled', true);
        userSocketWrite({
            action : 'Main->callSipPhone',
            user : user,
            domain : domain,
            reconnect : false,
            extension : $('#validateSipPhoneInput').val().replace(/ /g, '')
        });
    } else if (!userSocketStatus) {
        $('#validateSipPhoneError').html('<i class="fa fa-warning"></i> Theres a problem with the connection to the Main Server.');
    } else if (!$('#validateSipPhoneInput').val()) {
        $('#validateSipPhoneError').html('<i class="fa fa-warning"></i> Please write your Sip Phone Extension.');
    }
}

function sipPhoneNoPickup () {
    $('#validateSipPhoneError').html('<i class="fa fa-warning"></i> Sip Phone Error! It was not possible to find a Sip Phone with that Extension.');
    $('#validateSipPhone').prop('disabled', false);
    $('#cancelValidateSipPhone').prop('disabled', false);
    $('#reValidateSipPhone').prop('disabled', false);
    $('#cancelreValidateSipPhone').prop('disabled', false);
}

function sipPhoneNoAnswer () {
    $('#validateSipPhoneError').html('<i class="fa fa-warning"></i> Sip Phone Error! A call was placed to your Sip Phone but it was not answered.');
    $('#validateSipPhone').prop('disabled', false);
    $('#cancelValidateSipPhone').prop('disabled', false);
    $('#reValidateSipPhone').prop('disabled', false);
    $('#cancelreValidateSipPhone').prop('disabled', false);
}

function sipPhoneRejected () {
    $('#validateSipPhoneError').html('<i class="fa fa-warning"></i> Sip Phone Error! A call was placed to your Sip Phone but it was rejected.');
    $('#validateSipPhone').prop('disabled', false);
    $('#cancelValidateSipPhone').prop('disabled', false);
    $('#reValidateSipPhone').prop('disabled', false);
    $('#cancelreValidateSipPhone').prop('disabled', false);
}


$body.on('click', '#validateSipPhone', validateSipPhone).on('click', '#cancelreValidateSipPhone', reValidateSipPhoneCancel).on('click', '#reValidateSipPhone', reValidateSipPhone)
function sipPhoneLost (extension) {
    voiceSipPhoneStatus = false;
    $.SmartMessageBox({
        title : "<i class='fa fa-comments txt-color-orangeDark'></i> Sip Phone  <span class='txt-color-orangeDark'><strong>Reconnect</strong></span>",
        content : "Please enter your local extension",
        buttons : "[Reconnect][Logout][hidden]",
        input : "text",
        placeholder : "Enter your extension",
        sipPhoneReconnect : true,
        sipPhoneExtension : extension
    });
}

function reValidateSipPhoneCancel (e) {
    console.log('entrou')
    e.preventDefault();
    logoutVoice();
    $('.botTempo').click();
}

function sipPhoneReconnect () {
    $('.botTempo').click();
}

function reValidateSipPhone () {
    if ($('#reValidateSipPhoneInput').val() && userSocketStatus) {
        $('#reValidateSipPhoneError').html('<i class="fa fa-cog fa-spin"></i> Calling your Sip Phone...');
        $('#reValidateSipPhone').prop('disabled', true);
        $('#cancelreValidateSipPhone').prop('disabled', true);
        userSocketWrite({
            action : 'Main->callSipPhone',
            user : user,
            domain : domain,
            reconnect : true,
            extension : $('#reValidateSipPhoneInput').val().replace(/ /g, '')
        });
    } else if (!userSocketStatus) {
        $('#validateSipPhoneError').html('<i class="fa fa-warning"></i> Theres a problem with the connection to the Main Server.');
    } else if (!$('#validateSipPhoneInput').val()) {
        $('#validateSipPhoneError').html('<i class="fa fa-warning"></i> Please write your Sip Phone Extension.');
    }
}

// voice channel startup
function loadVoice (outboundInfo, inboundInfo, callbackInfo) {
    voiceSipPhoneStatus = true;
    startVoiceSocket();
    $('.botTempo').click();
    $.ajax({
        type : 'GET',
        url : 'ajax/voice-controls.html',
        dataType : 'html',
        success : function (data) {
            $('#voice-channel-control').html(data);
            $('#voice-channel-control').removeClass('hidden');
        }
    })
    $.ajax({
        type : 'GET',
        url : 'ajax/voice-body.html',
        dataType : 'html',
        success : function (data) {
            $('#default-workspace').addClass('hidden').html('');
            $('#voice-workspace').html(data);
            $('#voice-workspace').removeClass('hidden');
            $('#content').attr('channel-loaded', 'voice');
            $('#voiceLoginMenu').removeClass('hidden');
            bypassNav('voice');
            loadVoicePlugins(outboundInfo, inboundInfo);
        }
    })
}

function showVoice () {
    if (window.location.hash.indexOf('voice') === -1) {
        bypassNav('voice');
        $('#voice-channel-control').removeClass('hidden');
        $('#voice-workspace').removeClass('hidden')
        $('#default-workspace').addClass('hidden').html('');
        ;
    }
}

function loadVoicePlugins (outboundInfo, inboundInfo) {
    $('.voice-active-outbound-campaign').editable({
        highlight : false,
        type : 'select',
        title : "Select Outbound Campaign",
        mode : 'popup',
        placement : 'right',
        emptytext : 'Click here to choose a Campaign!',
        emptyclass : 'xedit-empty-color',
        source : outboundInfo,
    }).on('hidden', function (e, action) {
        if (action === 'cancel') {
            $('.editable-input select').empty();
            $('.editable-submit').click();
            $(this).text('No Active Outbound');
            disableSearch();
            disableAddContact();
            outboundSelected = 0;
            if (inboundSelected === 0)
                voiceControlButtons('fade-all');
            $('#voice-extra-fields').html('');
        }
    }).on('save', function (e, params) {
        voiceSocketWrite({
            action : 'Voice->changeOutboundCampaign',
            user : user,
            domain : domain,
            campaignID : params.newValue
        });
        outboundSelected = 1;
        voiceControlButtons('ready');
        enableSearchNewCall();
        enableAddContact();
    });
    $('.voice-active-inbound-campaign').editable({
        highlight : false,
        emptytext : 'Click here to choose an Inbound Group!',
        emptyclass : 'xedit-empty-class',
        type : 'checklist',
        title : "Select Inbound Group",
        mode : 'popup',
        placement : 'left',
        source : inboundInfo
    }).on('hidden', function (e, action) {
        if (action === 'save') {
            $(this).text('Groups Selected: ' + inboundSelected);
        } else {
            $('.editable-checklist div label input').prop('checked', false);
            $('.editable-submit').click();
            $(this).text('No Groups Selected');
            inboundSelected = 0;
            if (outboundSelected === 0)
                voiceControlButtons('fade-all');
            $('#voice-extra-fields').html('');
        }
    }).on('save', function (e, params) {
        voiceSocketWrite({
            action : 'Voice->changeInboundGroups',
            user : user,
            domain : domain,
            groups : params.newValue
        });
        inboundSelected = params.newValue.length;
        voiceControlButtons('ready');
    });
    var manualDialContent = '<div class="smart-form"><label class="input"><i class="icon-prepend fa fa-phone"></i><input autocomplete="off" id="voice-manual-call-number" type="tel" placeholder="phone"></label></div>';
    $('#voiceControls-manual').popover({
        html : true,
        placement : 'bottom',
        trigger : 'click',
        title : '',
        content : manualDialContent
    }).on('shown.bs.popover', function () {
        $('#voice-manual-call-number').mask('999999999', {
            placeholder : ""
        }).focus();
    }).attr('disabled', true);
    var transferCallContent = '<div class="smart-form"><label class="input"><i class="icon-prepend fa fa-phone"></i><input style="width:120px !important" autocomplete="off" id="voice-transfer-dial-number" type="tel" placeholder="phone"></label></div>';
    $('#voice-transfer-destination-dial').popover({
        html : true,
        placement : 'bottom',
        trigger : 'click',
        title : '',
        content : transferCallContent
    }).on('shown.bs.popover', function () {
        $('#voice-transfer-dial-number').mask('999999999', {
            placeholder : ""
        }).focus();
    })
    var dtmfContent = '<table id="dtmf-grid"><tr><td><button class="btn btn-default voice-dtmf-digit">1</button></td><td><button class="btn btn-default voice-dtmf-digit">2</button></td><td><button class="btn btn-default voice-dtmf-digit">3</button></td></tr><tr><td><button class="btn btn-default voice-dtmf-digit">4</button></td><td><button class="btn btn-default voice-dtmf-digit">5</button></td><td><button class="btn btn-default voice-dtmf-digit">6</button></td></tr><tr><td><button class="btn btn-default voice-dtmf-digit">7</button></td><td><button class="btn btn-default voice-dtmf-digit">8</button></td><td><button class="btn btn-default voice-dtmf-digit">9</button></td></tr></table>';
    $('#voiceControls-dtmf').popover({
        html : true,
        placement : 'bottom',
        trigger : 'click',
        title : '',
        content : dtmfContent,
        container : 'body'
    }).on('shown.bs.popover', function () {
        $('#voiceControls-dtmf').focus()
    })
    $('#voice-field-first_phone').mask('999999999', {
        placeholder : ""
    });
    $('#voice-field-second_phone').mask('999999999', {
        placeholder : ""
    });
    $('#voice-field-third_phone').mask('999999999', {
        placeholder : ""
    });
    $('#voice-callbacks-phone-search').mask('999999999', {
        placeholder : ""
    });
    $('#voice-outcomes-callback-date').datetimepicker({
        format : "dd MM yyyy - hh:ii",
        autoclose : true,
        todayBtn : false,
        pickerPosition : "bottom-left",
        startDate : moment().subtract('minutes', 10).format('YYYY-MM-DD HH:mm')
    });
    $('#voice-callbacks-date-search').datetimepicker({
        format : "dd MM yyyy",
        autoclose : true,
        todayBtn : false,
        pickerPosition : "bottom-left",
        minView : 2,
        maxView : 3
    }).on('changeDate', function (e) {
        getCallbacksFiltered();
    });
    $('#voice-form-callback-list').slimScroll({
        height : '668px'
    })
    $('#voice-outcomes-callback-date').datetimepicker({
        format : "dd MM yyyy - hh:ii",
        autoclose : true,
        todayBtn : false,
        pickerPosition : "bottom-left",
        startDate : moment().subtract('minutes', 10).format('YYYY-MM-DD HH:mm')
    });
    disableSearch();
    disableAddContact();
}

function disableSearch () {
    $('#voice-contact-search').addClass('no-search').removeClass('replace').attr('data-original-title', 'Search is disabled');
    $('#voice-contact-search i').css('color', '#C0C0C0');
}

function enableSearchNewCall () {
    $('#voice-contact-search').removeClass('no-search replace').attr('data-original-title', 'Search');
    $('#voice-contact-search i').css('color', '#838383');
}

function enableSearchReplace () {
    $('#voice-contact-search').removeClass('no-search').addClass('replace').attr('data-original-title', 'Search');
    $('#voice-contact-search i').css('color', '#838383');
}

function disableAddContact () {
    $('#voice-add-contact').addClass('no-create').attr('data-original-title', 'Add Contact is disabled');
    $('#voice-add-contact i').css('color', '#C0C0C0');
}

function enableAddContact () {
    $('#voice-add-contact').removeClass('no-create').attr('data-original-title', 'Add Contact');
    $('#voice-add-contact i').css('color', '#838383');
}

// listeners
$('#channel-list').off().on('click', '#voiceLogin', showVoiceLogin).on('click', '#logoutVoice', logoutVoice);
