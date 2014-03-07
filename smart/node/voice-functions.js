// flags
function voiceControlButtons (action) {
    switch (action) {
    case "show-all":
        showall();
        break;
    case "hide-all":
        hideall();
        break;
    case "ready":
        ready();
        break;
    case "fade-all":
        fadeall();
        break;
    case "unfade-all":
        unfadeall();
        break;
    case "ringing":
        ringing();
        break;
    case "talking":
        talking();
        break;
    case "hold":
        hold();
        break;
    case "resume":
        resume();
        break;
    case "cancel-manual":
        cancelmanual();
        break;
    case "dial-manual":
        dialmanual();
        break;
    case "new-contact-info-manual":
        newcontactinfomanual();
        break;
    }
    function fadeBtn (btn) {
        switch (btn) {
        case "start":
            $('#voiceControls-start').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        case "manual":
            $('#voiceControls-manual').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        case "cancel":
            $('#voiceControls-manualCancel').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        case "hold":
            $('#voiceControls-hold').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        case "resume":
            $('#voiceControls-resume').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        case "close":
            $('#voiceControls-close').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        case "transfer":
            $('#voiceControls-transfer').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        case "redial":
            $('#voiceControls-redial').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        case "dtmf":
            $('#voiceControls-dtmf').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
            break;
        }
    }

    function unfadeBtn (btn) {
        switch (btn) {
        case "start":
            $('#voiceControls-start').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        case "manual":
            $('#voiceControls-manual').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        case "cancel":
            $('#voiceControls-manualCancel').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        case "hold":
            $('#voiceControls-hold').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        case "resume":
            $('#voiceControls-resume').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        case "close":
            $('#voiceControls-close').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        case "transfer":
            $('#voiceControls-transfer').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        case "redial":
            $('#voiceControls-redial').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        case "dtmf":
            $('#voiceControls-dtmf').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
            break;
        }
    }

    function showall () {
        $('.ribbon-control-button').removeClass('hidden');
    }

    function hideall () {
        $('.ribbon-control-button').addClass('hidden');
    }

    function fadeall () {
        $('.ribbon-control-button').attr('disabled', true).removeClass('ribbon-control-button-disabled').addClass('ribbon-control-button-faded');
    }

    function unfadeall () {
        $('.ribbon-control-button').attr('disabled', false).removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded');
    }

    function ready () {
        fadeall();
        unfadeBtn('manual');
        if (!$('#voiceControls-start').hasClass('locked')) {
            unfadeBtn('start');
        }
        if (existsRedial) {
            unfadeBtn('redial');
        }
    }

    function ringing () {
        fadeall();
        unfadeBtn('close');
    }

    function talking () {
        fadeall();
        unfadeBtn('transfer');
        unfadeBtn('hold');
        unfadeBtn('close');
        unfadeBtn('dtmf');
    }

    function hold () {
        $('#voiceControls-hold').addClass('hidden');
        $('#voiceControls-resume').removeClass('hidden').removeClass('ribbon-control-button-disabled').removeClass('ribbon-control-button-faded').attr('disabled', false);
        fadeBtn('transfer');
        fadeBtn('dtmf');
    }

    function resume () {
        $('#voiceControls-hold').removeClass('hidden');
        $('#voiceControls-resume').addClass('hidden');
        unfadeBtn('transfer');
        unfadeBtn('dtmf');
    }

    function cancelmanual () {
        $('#voiceControls-manual').removeClass('hidden');
        $('#voiceControls-manualCancel').addClass('hidden');
        if (existsRedial && outboundSelected) {
            unfadeBtn('redial');
        }
    }

    function dialmanual () {
        $('#voiceControls-manual').removeClass('hidden');
        $('#voiceControls-manualCancel').addClass('hidden');
        fadeBtn('manual');
        unfadeBtn('close');
    }

    function newcontactinfomanual () {
        $('#voiceControls-manual').addClass('hidden');
        fadeBtn('redial');
        $('#voiceControls-manualCancel').removeClass('hidden ribbon-control-button-disabled ribbon-control-button-faded').attr('disabled', false);
    }

}

$voiceWorkspace.on('click', '.voice-active-outbound-campaign', function (e) {
    if (campaignChangeLock) {
        if ($('.voice-active-outbound-campaign').hasClass('editable-open')) {
            $('.voice-active-outbound-campaign').editable('hide');
        } else {
            $('.voice-active-outbound-campaign').editable('show');
        }
    }
})
// contact info
function buildExtraFields (fields) {
    var extraFieldsHTML = '', readOnly;
    for (var i = 0; i < fields.length; i++) {
        if (fields[i].readOnly) {
            readOnly = 'readonly';
        } else {
            readOnly = '';
        }
        extraFieldsHTML += '<section><label class="label">' + fields[i].displayLabel + '</label><label class="input"><input ' + readOnly + ' id="voice-field-' + fields[i].fieldName + '" class="voice-contact-input" type="text"></label>\</section>';
    }
    $('#voice-extra-fields').html(extraFieldsHTML);
}

function newContactInfo (fieldsStructure, fieldsValue, contactID, callType, callbackID, outcomes, onDemand) {
    if (onDemand) {
        $('#voiceControls-record').removeClass('hidden');
    }
    currentcontactID = contactID;
    currentCallbackID = callbackID;
    voiceOutcomes = outcomes;
    buildOutcomeGroups();
    var extraFieldsHTML = '', readOnly;
    for (var i = 0; i < fieldsStructure.length; i++) {
        if (fieldsStructure[i].readOnly) {
            readOnly = 'readonly';
        } else {
            readOnly = '';
        }
        extraFieldsHTML += '<section><label class="label">' + fieldsStructure[i].displayLabel + '</label><label class="input"><input ' + readOnly + ' id="voice-field-' + fieldsStructure[i].fieldName + '" class="voice-contact-input" type="text"></label>\</section>';
    }
    $('#voice-extra-fields').html(extraFieldsHTML);
    switch (callType) {
    case "manual" :
        voiceControlButtons('new-contact-info-manual');
        break;
    case "queue" :
        voiceControlButtons('talking');
        break;
    case "dialer" :
        voiceControlButtons('talking');
        break;
    }
    for (var i = 0; i < fieldsValue.length; i++) {
        $('#voice-field-' + fieldsValue[i].fieldName).val(fieldsValue[i].value);
        if (!$('#voice-contact-search').hasClass('replace') && callType == 'manual') {
            if (fieldsValue[i].fieldName === 'first_phone' || fieldsValue[i].fieldName === 'second_phone' || fieldsValue[i].fieldName === 'third_phone') {
                if (fieldsValue[i].value.length) {
                    $('.contact-info-label-' + fieldsValue[i].fieldName).addClass('hidden');
                    $('#voice-field-' + fieldsValue[i].fieldName + '-btn').attr('phone', fieldsValue[i].value).html('<i class="fa fa-phone"></i> ' + fieldsValue[i].value).removeClass('hidden');
                }
            }
        }
    }
}

// outcomes ////////////////////////////////////////////////////////////////////////////////////////////
$voiceWorkspace.on('click', '.voice-outcomes-group', function () {
    buildOutcomeItems($(this).attr('group'))
}).on('click', '#voice-outcomes-back', function () {
    buildOutcomeGroups();
}).on('click', '#voice-outcomes-submit', function () {
    submitContact();
})
function buildOutcomeGroups () {
    var i, f, html = '';
    for ( i = 0; i < voiceOutcomes.length; i++) {
        if (i === 0 || i % 2 === 0) {
            html += '<div class="row">';
        }
        html += '<section class="col col-6 text-align-center">';
        html += '<button group="' + voiceOutcomes[i].groupID + '" class="btn btn-block btn-lg btn-default voice-outcomes-group">' + voiceOutcomes[i].groupName + '</button>';
        html += '</section>';
        if ((i - 1) % 2 === 0) {
            html += '</div>';
        }
    }
    $('#voice-outcomes-footer').remove();
    $('.voice-outcomes-comments').removeClass('hidden');
    $('.voice-outcomes-callback').addClass('hidden');
    $('#voice-outcomes').html(html);
}

function buildOutcomeItems (group) {
    var i, f, html = '', footer = '', disabledState = '';
    for ( i = 0; i < voiceOutcomes.length; i++) {
        if (voiceOutcomes[i].groupID === parseInt(group)) {
            for ( f = 0; f < voiceOutcomes[i].outcomes.length; f++) {
                if (f === 0 || f % 3 === 0) {
                    html += '<div class="row">';
                }
                html += '<section class="col col-4">';
                html += '<label class="radio">';
                html += '<input type="radio" outcome-id="' + voiceOutcomes[i].outcomes[f].id + '" outcome-name = "' + voiceOutcomes[i].outcomes[f].name + '" callback="' + voiceOutcomes[i].outcomes[f].callback + '" name="voice-outcomes-items">';
                html += '<i></i>' + voiceOutcomes[i].outcomes[f].name + '</label>';
                html += '</section>';
                if ((f + 1) % 3 === 0 || voiceOutcomes[i].outcomes.length === f + 1) {
                    html += '</div>';
                }
            }
        }
    }
    if (callEnded) {
        disabledState = '';
    } else {
        disabledState = 'disabled="disabled"';
    }
    footer += '<footer id="voice-outcomes-footer">';
    footer += '<span id="voice-outcomes-footer-msg"></span>';
    footer += '<button id="voice-outcomes-submit" ' + disabledState + ' class="btn btn-primary">Submit</button>';
    footer += '<button id="voice-outcomes-back" class="btn btn-default">Back</button>';
    footer += '</footer>';
    $('#voice-outcomes').html(html).parent().append(footer);
}

$voiceWorkspace.on('click', 'input[name="voice-outcomes-items"]', function () {
    if ($(this).attr('callback') === 'false') {
        $('.voice-outcomes-comments').removeClass('hidden');
        $('.voice-outcomes-callback').addClass('hidden');
    } else {
        $('.voice-outcomes-callback').removeClass('hidden');
        $('.voice-outcomes-comments').addClass('hidden');
    }
});
$voiceWorkspace.on('change', '#voice-outcomes-callback-date', function () {
    $('#voice-outcomes-footer-msg').html('');
})
function submitContact () {
    //on demand recording control
    $('#voice-recording-toggle').removeClass('btn-success').addClass('btn-danger hidden').html('<i class="fa fa-bullhorn"></i> Record Call').attr('disabled', false);
    $('#voice-recording-pause i').removeClass('fa-play').addClass('fa-pause');
    $('#voice-recording-pause').removeClass('btn-success').addClass('btn-warning hidden');
    // search control
    enableSearchNewCall();
    campaignChangeLock = false;
    var contactFields = [], callback = false, callbackDate = false, callbackType = false, callComments = $('#voice-outcomes-comments').val();
    if ($('input[name="voice-outcomes-items"]:checked').length) {
        $.each($('.voice-contact-input'), function (index, value) {
            var field = $(this).attr('id').replace('voice-field-', '');
            contactFields.push({
                fieldName : field,
                fieldValue : $(this).val()
            });
        });
        if ($('input[name="voice-outcomes-items"]:checked').attr('callback') === 'true') {
            if ($('#voice-outcomes-callback-date').val().length) {
                callback = true;
                callbackDate = moment($('#voice-outcomes-callback-date').val()).format('YYYY-MM-DD HH:mm:ss');
                callbackType = $('.voice-outcomes-callback-type:checked').attr('value');
                callComments = $('#voice-outcomes-callback-comments').val();
            } else {
                $('#voice-outcomes-footer-msg').html('<i class="fa fa-warning"></i> Please choose a Callback Date.');
                return false;
            }
        }
        voiceSocketWrite({
            action : 'Voice->submitContact',
            user : user,
            domain : domain,
            contactID : currentcontactID,
            contactFields : contactFields,
            outcomeID : $('input[name="voice-outcomes-items"]:checked').attr('outcome-id'),
            outcomeName : $('input[name="voice-outcomes-items"]:checked').attr('outcome-name'),
            callComments : callComments,
            callback : callback,
            callbackID : currentCallbackID,
            callbackDate : callbackDate,
            callbackType : callbackType
        });
        callEnded = false;
        buildOutcomeGroups();
        voiceControlButtons('ready');
        $('.voice-contact-input').val('');
        $('#voice-extra-fields').html('');
        $('a[href="#tab-voice-outcomes"]').addClass('hidden');
        $('a[href="#tab-voice-script"]').click();
        $('#voice-outcomes-comments').val('');
        $('#voice-outcomes-callback-comments').val('');
        $('.voice-outcomes-callback-type[value="personal"]').click();
        $('#voice-outcomes-callback-date').val('');
    } else {
        $('#voice-outcomes-footer-msg').html('<i class="fa fa-warning"></i> Please choose an Outcome.');
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////
// manual call
$('#voice-channel-control').on('keyup', '#voice-manual-call-number', startManualCall).on('focusout', '#voice-manual-call-number', function () {
    $('#voiceControls-manual').popover('hide');
});
function startManualCall (e) {
    campaignChangeLock = true;
    e.preventDefault();
    existsRedial = true;
    if (e.keyCode == 13 && $(this).val().replace(/ /g, '').length === 9) {
        voiceSocketWrite({
            action : 'Voice->startManualCall',
            user : user,
            domain : domain,
            phone : $('#voice-manual-call-number').val()
        });
        $('#voiceControls-manual').popover('hide');
        $('#voice-manual-call-number').blur();
    }
}


$('#voice-channel-control').on('click', '#voiceControls-manualCancel', cancelManualCall)
function cancelManualCall () {
    campaignChangeLock = false;
    $('.voice-contact-input').val('');
    voiceControlButtons('cancel-manual');
    $('.contact-info-btn').addClass('hidden');
    $('.contact-info-label').removeClass('hidden');
    $('#voice-extra-fields').html('');
    voiceSocketWrite({
        action : 'Voice->cancelManualCall',
        user : user,
        domain : domain
    });
}

$voiceWorkspace.on('click', '.contact-info-btn', dialManualCall)
function dialManualCall (e) {
    //search controls
    disableSearch();
    e.preventDefault();
    $('.contact-info-btn').addClass('hidden');
    $('.contact-info-label').removeClass('hidden');
    //  $(this).next().addClass('voice-label-dialing').find('i').addClass('fa-cog fa-spin').removeClass('fa-phone');
    voiceControlButtons('dial-manual');
    voiceSocketWrite({
        action : 'Voice->dialManualCall',
        user : user,
        domain : domain,
        phone : $(this).attr('phone'),
        altDial : $(this).attr('alt-dial')
    })
}

function callRinging (onDemand) {
    console.log('ON DEMAND: ' + onDemand)
    voiceControlButtons('ringing');
    if (onDemand) {
        //   $('#voice-recording-toggle').removeClass('hidden');
    }
    //  $('.voice-label-dialing').removeClass('voice-label-dialing').find('i').removeClass('fa-cog fa-spin').addClass('fa-phone');
}

function errorManualCall (error) {
    // search controls
    enableSearchNewCall();
    $.smallBox({
        title : "Theres was a problem with your call!",
        content : "An error was found, please try again.\n (Error Code: " + error + " )",
        color : "#B22222",
        iconSmall : "fa fa-exclamation",
        timeout : 5000
    });
    $('.voice-contact-input').val('');
    $('a[href="#tab-voice-script"]').click();
    $('.voice-label-dialing').removeClass('voice-label-dialing').find('i').removeClass('fa-cog fa-spin').addClass('fa-phone');
    voiceControlButtons('ready');
}

// call control
$('#voice-channel-control').on('click', '#voiceControls-close', hangup).on('click', '#voiceControls-hold', hold).on('click', '#voiceControls-resume', resume)
function hold () {
    voiceControlButtons('hold')
    voiceSocketWrite({
        action : 'Voice->hold',
        user : user,
        domain : domain
    })
}

function resume () {
    voiceControlButtons('resume');
    voiceSocketWrite({
        action : 'Voice->resume',
        user : user,
        domain : domain
    })
}

function answer () {
    // search controls
    enableSearchReplace();
    voiceControlButtons('talking');
    $('a[href="#tab-voice-outcomes"]').removeClass('hidden');
    buildOutcomeGroups();
}

function hangup () {
    // search controls
    disableSearch();
    // call recording
    $('.voice-recording-btn').addClass('hidden');
    callEnded = true;
    voiceControlButtons('fade-all');
    $('a[href="#tab-voice-outcomes"]').removeClass('hidden');
    buildOutcomeGroups();
    $('a[href="#tab-voice-outcomes"]').click();
    $('#voice-outcomes-submit').attr('disabled', false);
    voiceSocketWrite({
        action : 'Voice->hangupCall',
        user : user,
        domain : domain
    })
}

function calleeHangup () {
    // search controls
    disableSearch();
    // call recording
    $('.voice-recording-btn').addClass('hidden');
    callEnded = true;
    voiceControlButtons('fade-all');
    $('#voice-outcomes-submit').attr('disabled', false);
    $('a[href="#tab-voice-outcomes"]').removeClass('hidden');
    $('a[href="#tab-voice-outcomes"]').click();
    if (!$('#voice-transfer-panel').hasClass('hidden')) {
        $('#voice-transfer-submit').attr('disabled', true)
        $.smallBox({
            title : "Original call has been lost!",
            content : "Please cancel your transference, and start a new call.",
            color : "#B22222",
            iconSmall : "fa fa-exclamation",
            timeout : 5000
        });
    }
}


$('#voice-channel-control').on('click', '#voiceControls-redial', redial)
function redial () {
    voiceSocketWrite({
        action : 'Voice->startManualCall',
        user : user,
        domain : domain,
        phone : 'redial',
    });
}

// transfer call
var voiceExistsDestination = false;
$('#voice-channel-control').on('click', '#voiceControls-transfer', startTransferCall);
$('#voice-channel-control').on('click', '#voice-transfer-cancel', function () {
    cancelTransferCall();
});
$('#voice-channel-control').on('click', '#voice-transfer-original-hold', holdOriginalCall);
$('#voice-channel-control').on('click', '#voice-transfer-original-resume', resumeOriginalCall);
$('#voice-channel-control').on('click', '#voice-transfer-destination-call', dialDestination);
function startTransferCall () {
    //search controls
    disableSearch();
    voiceControlButtons('fade-all');
    $('#voice-transfer-panel').removeClass('hidden');
    voiceSocketWrite({
        action : 'Voice->startTransfer',
        user : user,
        domain : domain
    });
}

function cancelTransferCall (flag) {
    // search controls
    enableSearchReplace();
    voiceExistsDestination = false;
    voiceControlButtons('talking');
    $('#voice-transfer-panel').addClass('hidden');
    $('#voice-transfer-submit').attr('disabled', true);
    $('#voice-transfer-destination-status').removeClass('bg-color-green bg-color-yellow').addClass('bg-color-blue').html('...');
    $('#voice-transfer-original-status').removeClass('bg-color-blue bg-color-yellow').addClass('bg-color-green').html('Talking');
    $('#voice-transfer-destination-close').attr('disabled', true);
    $('#voice-transfer-destination-dial').attr('disabled', true);
    $('.voice-transfer-revert-destination').attr('id', 'voice-transfer-destination-hold').attr('disabled', true).html('<i class="fa fa-microphone"></i>')
    $('.voice-transfer-revert-original').attr('id', 'voice-transfer-original-hold').attr('disabled', false).html('<i class="fa fa-microphone"></i>')
    if (!flag) {
        voiceSocketWrite({
            action : 'Voice->cancelTransfer',
            user : user,
            domain : domain
        });
    } else {
    }
}

function holdOriginalCall () {
    $('#voice-transfer-original-hold').html('<i class="fa fa-microphone-slash"></i>').attr('id', 'voice-transfer-original-resume');
    $('#voice-transfer-original-status').html('On Hold').removeClass('bg-color-green').addClass('bg-color-blue');
    if (voiceExistsDestination) {
        $('#voice-transfer-destination-resume').attr('disabled', false);
    } else {
        $('#voice-transfer-destination-dial').attr('disabled', false);
    }
    voiceSocketWrite({
        action : 'Voice->hold',
        user : user,
        domain : domain
    });
}

function resumeOriginalCall () {
    $('#voice-transfer-original-resume').html('<i class="fa fa-microphone"></i>').attr('id', 'voice-transfer-original-hold');
    $('#voice-transfer-original-status').html('Talking').addClass('bg-color-green').removeClass('bg-color-blue');
    if (voiceExistsDestination) {
        $('#voice-transfer-destination-resume').attr('disabled', true);
    } else {
        $('#voice-transfer-destination-dial').attr('disabled', true);
    }
    voiceSocketWrite({
        action : 'Voice->resume',
        user : user,
        domain : domain
    });
}


$voiceWorkspace.on('keyup', '#voice-transfer-dial-number', dialDestination).on('focusout', '#voice-transfer-dial-number', function () {
    $('#voice-transfer-destination-dial').popover('hide');
})
function dialDestination (e) {
    e.preventDefault();
    if (e.keyCode == 13 && $(this).val().replace(/ /g, '').length === 9) {
        voiceSocketWrite({
            action : 'Voice->dialDestination',
            user : user,
            domain : domain,
            phone : $('#voice-transfer-dial-number').val()
        });
        $('#voice-transfer-dial-number').blur();
    }
}

function transferDialRinging () {
    $('#voice-transfer-original-resume').attr('disabled', true);
    $('#voice-transfer-destination-close').attr('disabled', false);
    $('#voice-transfer-destination-dial').attr('disabled', true);
    $('#voice-transfer-destination-status').html('Dialing...').addClass('bg-color-yellow').removeClass('bg-color-blue');
}

function transferAnswer () {
    voiceExistsDestination = true;
    $('#voice-transfer-destination-hold').attr('disabled', false);
    $('#voice-transfer-destination-status').html('Talking').addClass('bg-color-green').removeClass('bg-color-yellow');
    $('#voice-transfer-submit').attr('disabled', false);
}


$voiceWorkspace.on('click', '#voice-transfer-destination-hold', holdDestination).on('click', '#voice-transfer-destination-resume', resumeDestination)
function holdDestination () {
    $('#voice-transfer-destination-hold').html('<i class="fa fa-microphone-slash"></i>').attr('id', 'voice-transfer-destination-resume');
    $('#voice-transfer-destination-status').html('On Hold').removeClass('bg-color-green').addClass('bg-color-blue');
    $('#voice-transfer-original-resume').attr('disabled', false);
    voiceSocketWrite({
        action : 'Voice->destinationHold',
        user : user,
        domain : domain
    });
}

function resumeDestination () {
    $('#voice-transfer-destination-resume').html('<i class="fa fa-microphone"></i>').attr('id', 'voice-transfer-destination-hold');
    $('#voice-transfer-destination-status').html('Talking').addClass('bg-color-green').removeClass('bg-color-blue');
    $('#voice-transfer-original-resume').attr('disabled', true);
    voiceSocketWrite({
        action : 'Voice->destinationResume',
        user : user,
        domain : domain
    });
}

$voiceWorkspace.on('click', '#voice-transfer-destination-close', closeDestination)
function closeDestination (flag) {
    voiceExistsDestination = false;
    if ($('.voice-transfer-revert-original').attr('id') === 'voice-transfer-original-hold') {
        $('#voice-transfer-destination-dial').attr('disabled', true);
    } else {
        $('#voice-transfer-destination-dial').attr('disabled', false);
    }
    $('#voice-transfer-original-resume').attr('disabled', false);
    $('#voice-transfer-destination-status').html('...').removeClass('bg-color-green bg-color-yellow').addClass('bg-color-blue');
    $('.voice-transfer-revert-destination').attr('id', 'voice-transfer-destination-hold').attr('disabled', true).html('<i class="fa fa-microphone"></i>')
    $('#voice-transfer-destination-close').attr('disabled', true);
    $('#voice-transfer-submit').attr('disabled', true);
    if (!flag) {
        voiceSocketWrite({
            action : 'Voice->destinationClose',
            user : user,
            domain : domain
        });
    }
}

function destinationHangup () {
    closeDestination(true);
}

$voiceWorkspace.on('click', '#voice-transfer-submit', submitTransfer)
function submitTransfer () {
    // search controls
    disableSearch();
    cancelTransferCall(true);
    voiceSocketWrite({
        action : 'Voice->transferCall',
        user : user,
        domain : domain
    });
}

// dtmf
$('#voice-channel-control').on('focusout', '#voiceControls-dtmf', function () {
    $('#voiceControls-dtmf').popover('hide');
}).on('click', '.voice-dtmf-digit', sendDTMF);
function sendDTMF () {
    voiceSocketWrite({
        action : 'Voice->sendDTMF',
        user : user,
        domain : domain,
        digit : $(this).html()
    });
    $("#dtmf-grid").hide();
}

// callbacks
$voiceWorkspace.on('click', 'a[href="#tab-voice-callbacks"]', getCallbacks)
$voiceWorkspace.on('click', '.right-panel-tab', function () {
    $('#voice-callback-list-toolbar').addClass('hidden');
}).on('click', '#voice-callbacks-phone-search-switch', function (e) {
    e.preventDefault();
    $('#voice-callbacks-phone-search-switch').addClass('hidden');
    $('#voice-callbacks-date-search-switch').removeClass('hidden');
    $('#voice-callbacks-date-search').parent().addClass('hidden');
    $('#voice-callbacks-phone-search').parent().removeClass('hidden');
}).on('click', '#voice-callbacks-date-search-switch', function (e) {
    e.preventDefault();
    $('#voice-callbacks-phone-search-switch').removeClass('hidden');
    $('#voice-callbacks-date-search-switch').addClass('hidden');
    $('#voice-callbacks-date-search').parent().removeClass('hidden');
    $('#voice-callbacks-phone-search').parent().addClass('hidden');
}).on('click', '.callback-filters', function () {
    getCallbacksFiltered();
}).on('click', '#voice-callbacks-clear-search', function (e) {
    e.preventDefault();
    $('#voice-callbacks-date-search').val('');
    $('#voice-callbacks-phone-search').val('');
    $('.callback-filters').attr('checked', false);
    getCallbacksFiltered();
}).on('keyup', '#voice-callbacks-phone-search', function (e) {
    console.log($('#voice-callbacks-phone-search').val().length)
    if ($('#voice-callbacks-phone-search').val().length === 9) {
        getCallbacksFiltered();
        $('#voice-callbacks-phone-search').blur()
    }
}).on('focus', '#voice-callbacks-phone-search', function () {
    $('#voice-callbacks-phone-search').val('')
})
function getCallbacks () {
    $('#voice-callback-list-toolbar').removeClass('hidden');
    voiceSocketWrite({
        action : 'Voice->getCallbacks',
        user : user,
        domain : domain
    });
}

function getCallbacksFiltered () {
    var ready = false, hold = false, deleted = false, closed = false, date = false, phone = false;
    switch ($('.callback-filters:checked').val()) {
    case "ready" :
        ready = true;
        break;
    case "hold" :
        hold = true;
        break;
    case "deleted" :
        deleted = true;
        break;
    case "closed" :
        closed = true;
        break;
    }
    if ($('#voice-callbacks-date-search').parent().hasClass('hidden')) {
        if ($('#voice-callbacks-phone-search').val().length) {
            phone = $('#voice-callbacks-phone-search').val()
        }
    } else {
        if ($('#voice-callbacks-date-search').val().length) {
            date = moment($('#voice-callbacks-date-search').val()).format('YYYY-MM-DD');
        }
    }
    voiceSocketWrite({
        action : 'Voice->getCallbacksFiltered',
        user : user,
        domain : domain,
        ready : ready,
        hold : hold,
        deleted : deleted,
        closed : closed,
        date : date,
        phone : phone
    });
}

function buildCallbacks () {
    var i, background, callbackHTML = '', callbackDropdown, stateLabel, stateColor, actionCall, actionEdit, actionDelete, actionClose, noActions = '<li><a href="javascript:void(0);"><i class="fa fa-ban"></i> No Actions Available</a></li>';
    for ( i = 0; i < voiceCallbacks.length; i++) {
        actionCall = '<li><a class="voice-callback-actions-call" contact-id="' + voiceCallbacks[i].contact_id + '" callback-id="' + voiceCallbacks[i].id + '" href="javascript:void(0);"><i class="fa fa-phone"></i> Call</a></li>';
        actionEdit = '<li><a class="voice-callback-actions-edit" callback-id="' + voiceCallbacks[i].id + '" href="javascript:void(0);"><i class="fa fa-edit"></i> Edit</a></li>';
        actionDelete = '<li><a class="voice-callback-actions-delete" callback-id="' + voiceCallbacks[i].id + '" href="javascript:void(0);"><i class="fa fa-trash-o"></i> Delete</a></li>';
        actionClose = '<li class="divider"></li><li><a class="voice-callback-actions-close" callback-id="' + voiceCallbacks[i].id + '" href="javascript:void(0);"><i class="fa fa-ban"></i> Close</a></li>';
        if (!voiceCallbacks[i].active) {
            stateLabel = 'Closed';
            stateColor = 'btn-default';
            callbackDropdown = noActions;
        } else if (voiceCallbacks[i].deleted) {
            stateLabel = 'Deleted';
            stateColor = 'btn-danger';
            callbackDropdown = actionCall;
        } else if (moment(voiceCallbacks[i].callback_date).isBefore(moment())) {
            stateLabel = 'Ready';
            stateColor = 'btn-success';
            callbackDropdown = actionCall + actionDelete;
        } else {
            stateLabel = 'Hold';
            stateColor = 'btn-primary';
            callbackDropdown = actionCall + actionDelete;
        }
        if (i % 2 !== 0) {
            background = 'background-color: rgb(242, 242, 242)';
        } else {
            background = '';
        }
        callbackHTML += '<fieldset style="' + background + '">';
        callbackHTML += '<div class="row">';
        callbackHTML += '<section class="col col-9">';
        callbackHTML += '<label class="input"> <i class="icon-prepend fa fa-user"></i>';
        callbackHTML += '<input readonly="readonly" type="text" value="' + voiceCallbacks[i].contact + '" placeholder="Contact Name">';
        callbackHTML += ' </label>';
        callbackHTML += ' </section>';
        callbackHTML += '<section class="col col-3">';
        callbackHTML += '<div class="btn-group dropdown-inside-smart-form pull-right">';
        callbackHTML += '<a href="javascript:void(0);" class="btn ' + stateColor + ' ">' + stateLabel + '</a>';
        callbackHTML += '<a href="javascript:void(0);" data-toggle="dropdown" class="btn ' + stateColor + ' dropdown-toggle"><span class="caret"></span></a>';
        callbackHTML += '<ul class="dropdown-menu pull-right">';
        callbackHTML += callbackDropdown;
        callbackHTML += '</ul>';
        callbackHTML += '</div>';
        callbackHTML += '</section>';
        callbackHTML += '</div>';
        callbackHTML += '<div class="row">';
        callbackHTML += '<section class="col col-4">';
        callbackHTML += '<label class="input"> <i class="icon-prepend fa fa-phone"></i>';
        callbackHTML += '<input readonly="readonly" name="" value="' + voiceCallbacks[i].contact_phone + '" placeholder="Phone" >';
        callbackHTML += '</label>';
        callbackHTML += '</section>';
        callbackHTML += '<section class="col col-4">';
        callbackHTML += '<label class="input"> <i class="icon-prepend fa fa-calendar"></i>';
        callbackHTML += '<input readonly="readonly" name="" value="' + moment(voiceCallbacks[i].callback_date, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY') + '" placeholder="Date">';
        callbackHTML += '</label>';
        callbackHTML += '</section>';
        callbackHTML += '<section class="col col-4">';
        callbackHTML += '<label class="input"> <i class="icon-prepend fa fa-clock-o"></i>';
        callbackHTML += '<input readonly="readonly" name="" value="' + moment(voiceCallbacks[i].callback_date, 'YYYY-MM-DD HH:mm:ss').format('HH:mm') + '" placeholder="Hours">';
        callbackHTML += '</label>';
        callbackHTML += '</section>';
        callbackHTML += '</div>';
        callbackHTML += '<section>';
        callbackHTML += '<label class="textarea"> <i class="icon-append fa fa-comment"></i>';
        callbackHTML += '<textarea readonly="readonly" rows="3" id="voice-outcomes-callback-comments" placeholder="Callback Comments" class="">' + voiceCallbacks[i].callback_comments + '</textarea>';
        callbackHTML += '</label>';
        callbackHTML += '</section>';
        callbackHTML += '</fieldset>';
    }
    $('#voice-form-callback-list').html(callbackHTML)
}

$voiceWorkspace.on('click', '.voice-callback-actions-call', callbackOptionsCall)
$voiceWorkspace.on('click', '.voice-callback-actions-edit', callbackOptionsEdit)
$voiceWorkspace.on('click', '.voice-callback-actions-delete', callbackOptionsDelete)
$voiceWorkspace.on('click', '.voice-callback-actions-close', callbackOptionsClose)
function callbackOptionsCall () {
    campaignChangeLock = true;
    existsRedial = true;
    voiceSocketWrite({
        action : 'Voice->startManualCall',
        user : user,
        domain : domain,
        phone : 'callback',
        contactID : $(this).attr('contact-id'),
        callbackID : $(this).attr('callback-id')
    });
}

function callbackOptionsEdit () {
}

function callbackOptionsDelete () {
    var dropdownHTML = '';
    voiceSocketWrite({
        action : 'Voice->callbackOptionsDelete',
        user : user,
        domain : domain,
        callbackID : $(this).attr('callback-id')
    });
    dropdownHTML += '<a href="javascript:void(0);" class="btn btn-danger">Deleted</a>';
    dropdownHTML += '<a href="javascript:void(0);" data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><span class="caret"></span></a>';
    dropdownHTML += '<ul class="dropdown-menu pull-right">';
    dropdownHTML += '<li><a class="voice-callback-actions-call" callback-id="' + $(this).attr('callback-id') + '" href="javascript:void(0);"><i class="fa fa-phone"></i> Call</a></li> <li><a class="voice-callback-actions-edit" callback-id="' + $(this).attr('callback-id') + '" href="javascript:void(0);"><i class="fa fa-edit"></i> Edit</a></li> <li class="divider"></li><li><a class="voice-callback-actions-close" callback-id="' + $(this).attr('callback-id') + '" href="javascript:void(0);"><i class="fa fa-ban"></i> Close</a></li>';
    dropdownHTML += '</ul>';
    $(this).closest('.dropdown-inside-smart-form').html(dropdownHTML)
}

function callbackOptionsClose () {
    var dropdownHTML = ''
    voiceSocketWrite({
        action : 'Voice->callbackOptionsClose',
        user : user,
        domain : domain,
        callbackID : $(this).attr('callback-id')
    });
    dropdownHTML += '<a href="javascript:void(0);" class="btn btn-default">Closed</a>';
    dropdownHTML += '<a href="javascript:void(0);" data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span></a>';
    dropdownHTML += '<ul class="dropdown-menu pull-right">';
    dropdownHTML += '<li><a href="javascript:void(0);"><i class="fa fa-ban"></i> No Actions Available</a></li>';
    dropdownHTML += '</ul>';
    $(this).closest('.dropdown-inside-smart-form').html(dropdownHTML)
}

// search
$voiceWorkspace.on('click', '#voice-contact-search', openSearch);
$voiceWorkspace.on('click', '#voice-search-submit', function (e) {
    e.preventDefault();
    $('#voice-search-results').dataTable().fnReloadAjax();
}).on('click', 'div[id="wid-id-6003"] header div a.jarviswidget-fullscreen-btn', function () {
    if ($(this).hasClass('insert-open')) {
        $('#wid-id-6003').addClass('hidden');
        $(this).removeClass('insert-open')
    }
}).on('click', '#voice-search-results tbody tr', function () {
    var flag;
    $('div[id="wid-id-6003"] header div a.jarviswidget-fullscreen-btn').click();
    if ($('#voice-contact-search').hasClass('replace')) {
        flag = 'replace'
    } else {
        flag = 'new';
    }
    voiceSocketWrite({
        action : 'Voice->searchSelectContact',
        user : user,
        domain : domain,
        contactID : $(this).find('td').filter(':first').html(),
        flag : flag
    });
})
function openSearch () {
    if (!$(this).hasClass('no-search')) {
        $('#wid-id-6003').removeClass('hidden');
        $('div[id="wid-id-6003"] header div a.jarviswidget-fullscreen-btn').click().addClass('insert-open').focus();
    }
}

// new ready
$('#voice-channel-control').on('click', '#voiceControls-start', startCampaigns)
function startCampaigns () {
    $(this).addClass('locked hidden');
    voiceSocketWrite({
        action : 'Voice->startCampaigns',
        user : user,
        domain : domain,
    });
}

// call recording
$('#voice-recording-toggle').off().on('click', onDemandRecordingStart)
function onDemandRecordingBtn () {
    $('#voice-recording-toggle').removeClass('hidden');
}

function onDemandRecordingStart () {
    var that = $(this);
    $('#voice-recording-toggle').removeClass('btn-danger').addClass('btn-success').html('<i class="fa fa-check-square-o"></i> Recording...').attr('disabled', true);
    $('#voice-recording-pause').removeClass('hidden');
    voiceSocketWrite({
        action : 'Voice->startRecording',
        user : user,
        domain : domain,
    });
}


$('#voice-recording-pause').off().on('click', onDemandRecordingPause)
function onDemandRecordingPause () {
    $pauseIcon = $('#voice-recording-pause i');
    $pauseBtn = $('#voice-recording-pause')
    if ($pauseIcon.hasClass('fa-pause')) {
        voiceSocketWrite({
            action : 'Voice->pauseRecording',
            user : user,
            domain : domain,
        });
        $pauseBtn.removeClass('btn-warning').addClass('btn-success');
        $pauseIcon.removeClass('fa-pause').addClass('fa-play');
    } else {
        voiceSocketWrite({
            action : 'Voice->resumeRecording',
            user : user,
            domain : domain,
        });
        $pauseBtn.removeClass('btn-success').addClass('btn-warning');
        $pauseIcon.removeClass('fa-play').addClass('fa-pause');
    }
}
