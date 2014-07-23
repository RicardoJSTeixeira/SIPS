var SpiceU = {};

var alerts = new alerts_class();
$(function() {
    $.post("ajax/user_info.php", function(user) {
        SpiceU = user;
        types = {
            "1": "branch",
            "2": "dispenser",
            "5": "asm",
            "6": "sbo",
            "7": "abo",
            "8": "mkt",
            "9": "admin"};

        $("#user-name").text(user.name);
        $("#sidebar li.role-" + types[user.user_level]).show();
        init();
        alerts.init();

        if (!window.location.hash.length) {
            $(".menu-sidebar a:visible:eq(0)").click();
        }
    }, "json")
            .fail(function() {
                window.location = "logout.php";
            });
});

function init() {
    function setFavicon() {
        var link = $('link[type="image/vnd\.microsoft\.icon"]').remove().attr("href");
        $('<link href="' + link + '" rel="shortcut icon" type="image/vnd.microsoft.icon" />').appendTo('head');
    }
    $.ajaxSetup({
        cache: false
    });
    moment.lang('pt');
    $.history.on('load change pushed', function(event, url, type) {
        if (event.type === "load" && url !== "view/dashboard.html") {
            consultasMais();
        }
        $("#sidebar .active").removeClass("active");
        $("#sidebar").find("[href='" + url.split("?")[0] + "']").addClass("active");
        if (url.length) {
            $("#principal").load(url);
            setFavicon();
        } else {
            $(".menu-sidebar a:visible:eq(0)").click();
        }
    }).listen('hash');

    $('#sidebar a').click(function(e) {
        e.preventDefault();
        if ($(this).hasClass("active") || $(this).parent().hasClass("disabled"))
            return false;
        var href = $(this).attr("href");
        if (href === "#")
            return false;
        $.history.push(href);
    });

    get_messages();
    var messages_timeout = setInterval(get_messages, 1000 * 60);
    get_alerts();
    var alerts_timeout = setInterval(get_alerts, 1000 * 60);



    //Init all the modals | for the multiples datatoggles..
    $(document).on('click.modal.data-api', '[data-toggle!="modal"][data-toggle~="modal"]', function(e) {
        var $this = $(this),
                href = $this.attr('href'),
                $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) //strip for ie7
                ,
                option = $target.data('modal') ? 'toggle' : $.extend({
            remote: !/#/.test(href) && href
        }, $target.data(), $this.data());
        e.preventDefault();
        $target
                .modal(option)
                .one('hide', function() {
                    $this.focus();
                });
    });
    $('#alerts-content,#imessage_placeholder').slimScroll({
        railDraggable: !1
    });

    $(".ichat").on("click", ".dismiss_msg", function() {
        $.msg();
        $.post("ajax/general_functions.php", {
            action: "edit_message_status",
            id_msg: $(this).data().msg_id
        }, function() {
            get_messages();
            $.msg('unblock');
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    });
    $("#mark_all_read").click(function() {
        $.msg();
        $.post("ajax/general_functions.php", {
            action: "edit_message_status_by_user"
        }, function() {
            get_messages();
            $.msg('unblock');
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    });
    $(".ichat").on("click", ".ok_alert", function() {
        /*
         const S_ENC = "Encomenda";
         const S_APMKT = "Apoio Mkt";
         const S_FROTA = "Frota";
         const S_MAIL = "Correio";
         const S_MOVSTOCK = "Mov. Stock";
         const S_STOCK = "Stock";*/

        var table = $(this).data().record_section == "S_ENC" ? "ajax/requisition.php" : "ajax/requests.php";
        $.msg();
        $.post(table, {
            action: "set_readed",
            id_msg: $(this).data().id
        }, function() {
            get_alerts();
            $.msg('unblock');
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });













        $.msg();
        $.post("ajax/general_functions.php", {
            action: "set_readed",
            id_msg: $(this).data().id
        }, function() {
            get_alerts();
            $.msg('unblock');
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    });
    $("#mark_all_alerts_read").click(function() {
        $.msg();
        $.post("ajax/general_functions.php", {
            action: "set_all_readed"
        }, function() {
            get_alerts();
            $.msg('unblock');
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    });
    $("#notifications").click(function() {
        var a = $("#alert_time");
        a.text(a.data().update.fromNow());
    });


    $.fn.dataTableExt.oApi.fnReloadAjax = function(oSettings, sNewSource, fnCallback, bStandingRedraw) {
// DataTables 1.10 compatibility - if 1.10 then versionCheck exists.
// 1.10s API has ajax reloading built in, so we use those abilities
// directly.
        if ($.fn.dataTable.versionCheck) {
            var api = new $.fn.dataTable.Api(oSettings);
            if (sNewSource) {
                api.ajax.url(sNewSource).load(fnCallback, !bStandingRedraw);
            } else {
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
            for (var i = 0; i < aData.length; i++) {
                that.oApi._fnAddData(oSettings, aData[i]);
            }

            oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
            that.fnDraw();
            if (bStandingRedraw === true) {
                oSettings._iDisplayStart = iStart;
                that.oApi._fnCalculateEnd(oSettings);
                that.fnDraw(false);
            }

            that.oApi._fnProcessingDisplay(oSettings, false);
            /* Callback user function - for event handlers etc */
            if (typeof fnCallback === 'function' && fnCallback !== null) {
                fnCallback(oSettings);
            }
        }, oSettings);
    };
    $.msg('overwriteGlobal', 'clickUnblock', false);
    $.msg('overwriteGlobal', 'autoUnblock', false);
    $.msg('overwriteGlobal', 'content', 'Por favor aguarde...');
}
function get_messages() {
    //GET NEW MESSAGES
    $.post("ajax/general_functions.php", {
        action: "get_unread_messages"
    }, function(data) {
        $("#imessage_placeholder").empty();
        var msg = "";
        $.each(data, function() {
            msg = "<div class='imessage'>\n\
                        <div class='imes'>\n\
                            <div class='iauthor'>" + this.from + "</div>\n\
                            <div class='itext'>" + this.msg + "</div>\n\
                        </div>\n\
                        <div class='idelete'><a><span data-msg_id='" + this.id_msg + "' class='dismiss_msg'><i class='icon-remove'></i></span></a></div>\n\
                        <div class='clear'></div>\n\
                    </div>" + msg;
        });
        $("#msg_count").text(data.length);
        $("#imessage_placeholder").append(msg);
    }, "json").fail(function(data) {
        $.msg();
        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
        $.msg('unblock', 5000);
    });
}

function get_alerts(callback) {

    $.post("ajax/general_functions.php", {
        action: "get_alerts"
    }, function(data) {
        $("#alerts-content").empty();
        $("#alert_time").data("update", moment());
        var msg = "";
        var dismiss_icon = "";
        $.each(data, function() {
            if (SpiceU.user_level < 5) {
                if (this.alert.search(/Apoio Mkt./i) !== -1) {
                    alerts.add({id: this.id, message: "Á " + moment(this.entry_date).fromNow() + " - " + this.alert, callback: function() {
                            $.post("ajax/general_functions.php", {action: "set_readed", id_msg: this.id});
                        }});
                    return true;
                }
            }

            if (!~~this.cancel)
                dismiss_icon = "<a  class='' data-id='" + this.id + "' ><i class=' icon-remove'></i></a>";
            else
                dismiss_icon = "<a href='javascript:void(0)' class='ok_alert' data-id='" + this.id + "' ><i class='icon-ok'></i></a>";

            msg = "<div class='imessage'>\n\
                        <div class='r_icon'>" + dismiss_icon + "</div>\n\
                        <div class='r_info'>\n\
                            <div class='r_text'>" + this.alert + "</div>\n\
                            <div class='r_text'><i class='icon-time'></i>" + moment(this.entry_date).fromNow() + "</div>\n\
                        </div>\n\
                        <div class='clear'></div>\n\
                    </div>" + msg;
        });

        $("#alerts-count").text(data.length);
        $("#alerts-content").append(msg);
        if (typeof callback === "function") {
            callback();
        }
    }, "json").fail(function(data) {
        $.msg();
        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
        $.msg('unblock', 5000);
    });
}


function getUrlVars() {
    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
    });
    return vars;
}


String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};
function consultasMais() {
    if (!localStorage.length) {
        return false;
    }
    if (SpiceU.user_level > 1) {
        return false;
    }

    if (~~localStorage.v6 > 1) {
        alerts.add({id: 0, message: "Devido a ter <i class='label label-important'>" + localStorage.v6 + "</i> consultas com mais de 6 dias de atraso, só poderá usar o <i>Spice</i> para consultar e fechar consultas.", callback: function() {
                $(".menu-sidebar").find("li:not(:eq(0)):not(:eq(0))").addClass("disabled");
                $(".criar_marcacao, .recomendacoes, .criar_encomenda").prop("disabled", true);
                if ($(".menu-sidebar").find('.active').parent().index() > 1)
                    $.history.push("view/dashboard.html");
            }});
        return false;
    }
    if (~~localStorage.v3 > 3) {
        alerts.add({id: 0, message: "Cuidado que já tem <i class='label label-important'>" + localStorage.v3 + "</i> consultas com mais de 3 dias de atraso."});
        return false;
    }
    $(".menu-sidebar").find("li:not(:eq(0)):not(:eq(0))").removeClass("disabled");
    $(".criar_marcacao, .recomendacoes, .criar_encomenda").prop("disabled", false);

}

function dropOneConsult() {
    //localStorage.v3 = ~~localStorage.v3 - 1;
    //localStorage.v6 = ~~localStorage.v6 - 1;
}

function isBlocked() {
    return localStorage.v6 > 0;
}

function scrollTop() {

    $("html, body").animate({
        scrollTop: 0
    }, "fast");

}