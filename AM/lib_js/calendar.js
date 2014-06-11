var calendar = function(selector, data, modals, ext, client, user) {
    var me = this;
    this.user = user;
    this.selector = selector;
    this.client = client || {};
    this.ext = ext;
    this.resource = "all";
    this.modals = modals;
    this.modal_ext = modals.client;
    this.modal_special = modals.special;
    this.calendar = undefined;
    this.config = {
        header: {
            center: 'agendaDay agendaWeek month'
        },
        events: {
            url: "/AM/ajax/calendar.php",
            type: "POST",
            data: {
                action: "GetReservations",
                resource: "all"
            }
        },
        columnFormat: {
            month: 'ddd',
            week: 'ddd d/M',
            day: 'dddd'
        },
        titleFormat: {
            month: 'MMMM yyyy',
            week: "MMMM yyyy",
            day: 'd MMMM yyyy'
        },
        allDaySlot: false,
        defaultView: "agendaWeek",
        allDayDefault: false,
        unselectAuto: true,
        slotEventOverlap: false,
        timeFormat: 'H:mm{ - H:mm}',
        axisFormat: 'H:mm',
        dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
        dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        firstDay: 1,
        firstHour: (function() {
            return~~new Date().getUTCHours() - 2;
        })(),
        buttonText: {
            today: 'hoje',
            month: 'mês',
            week: 'semana',
            day: 'dia'
        },
        eventClick: function(calEvent, jsEvent, view) {
            if (isBlocked() && calEvent.start > new Date().getTime()) {
                $.jGrowl("Devido às consultas em atraso por fechar, esta funcionalidade não lhe permite qualquer tipo de acção com marcações posteriores a hoje.", {
                    sticky: 4000
                });
                return false;
            }
            if (calEvent.bloqueio && calEvent.system) {
                me.openMkt(calEvent);
            }
            if (calEvent.bloqueio || calEvent.del) {
                return false;
            }
            if (calEvent.system) {
                me.openSpecialEvent(calEvent);
            } else {
                me.openClient(calEvent);
            }
        },
        droppable: {
            agenda: true,
            month: false
        },
        drop: function(date, allDay) {
            $.msg();
            var cEO = $.extend({}, $(this).data('eventobject'));
            cEO.start = moment(date).unix();
            if (cEO.min) {
                cEO.end = moment(date).add("minutes", cEO.min).unix();
            } else {
                cEO.end = moment(date).add("minutes", config.defaultEventMinutes).unix();
            }

            cEO.allDay = allDay;

            if (!me.calendar.fullCalendar('getView').name.match("agenda")) {
                return false;
            }
            if (date < new Date().getTime()) {
                return false;
            }
            var exist = false;
            $.each(me.calendar.fullCalendar('clientEvents'),
                    function() {
                        if (this.del)
                            return true;

                        if (
                                ((moment(this.start).unix() < cEO.end) && (moment(this.start).unix() > cEO.start)) ||
                                ((moment(this.end).unix() > cEO.start) && (moment(this.end).unix() < cEO.end)) ||
                                ((moment(this.start).unix() <= cEO.start) && (moment(this.end).unix() >= cEO.end))
                                ) {
                            exist = true;
                            $.jGrowl("Não é permitido marcações concorrentes.", {
                                sticky: 4000
                            });
                            return false;
                        }
                    });
            if (exist) {
                return false;
            }

            $.post("/AM/ajax/calendar.php", {
                action: "newReservation",
                resource: me.resource,
                rtype: cEO.rtype,
                lead_id: cEO.lead_id,
                start: cEO.start,
                end: cEO.end
            },
            function(id) {
                cEO.id = id;
                me.calendar.fullCalendar('renderEvent', cEO, true);
                $("#external-events").remove();
                $.msg('unblock');
            },
                    "json").fail(function(data) {
                $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                $.msg('unblock', 5000);
            });
        },
        eventRender: function(event, element, view) {
            var d = {
                bloqueio: false,
                changed: 0,
                className: "",
                client_name: "",
                closed: false,
                codCamp: "",
                del: false,
                editable: false,
                end: "",
                id: 0,
                lead_id: 0,
                max: 0,
                min: 0,
                obs: "",
                rsc: 0,
                start: "",
                system: false,
                title: "",
                user: ""
            };

            event = $.extend(d, event);

            if (!event.url && !event.bloqueio) {
                element.popover({
                    placement: function(context, source) {
                        var position = $(source).position();

                        if (position.top < 110) {
                            return "bottom";
                        }

                        if (me.calendar.fullCalendar('getView').name === "agendaDay") {
                            return "top";
                        }

                        if (position.left > 515) {
                            return "left";
                        }

                        if (position.left < 515) {
                            return "right";
                        }

                        return "top";
                    },
                    html: true,
                    title: event.title,
                    content: (function() {
                        if (!event.system) {
                            return '<dl class="dl-horizontal"><dt>Nome</dt><dd>' + event.client_name + '</dd><dt>Cod. Mkt.</dt><dd>' + event.codCamp + '</dd><dt>Cod Postal</dt><dd>' + event.postal + '</dd></dl>';
                        } else {
                            return event.obs;
                        }
                    })(),
                    trigger: 'hover'
                });
            }
            element
                    .find(".fc-event-time")
                    .before($("<span>", {
                        class: "fc-event-icons"
                    })
                            .append(function() {
                                return (event.changed) ? $("<b>", {
                                    text: "R" + event.changed + " "
                                }) : "";
                            })
                            .append(function() {
                                return (!event.system || (event.bloqueio && event.system)) ? $("<i>", {
                                    class: ((event.closed) ? "icon-lock" : "icon-unlock")
                                }) : "";
                            }))
                    .append($("<span>", {
                        text: " " + event.obs,
                        class: "fc-event-obs"
                    }));

        },
        eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
            if (event.start < new Date().getTime()) {
                revertFunc();
                return false;
            }
            var exist = false;
            $.each(me.calendar.fullCalendar('clientEvents'),
                    function() {
                        if (this.id === event.id) {
                            return true;
                        }

                        if (this.del)
                            return true;

                        if (
                                ((moment(this.start).unix() < moment(event.end).unix()) && (moment(this.start).unix() > moment(event.start).unix())) ||
                                ((moment(this.end).unix() > moment(event.start).unix()) && (moment(this.end).unix() < moment(event.end).unix())) ||
                                ((moment(this.start).unix() <= moment(event.start).unix()) && (moment(this.end).unix() >= moment(event.end).unix()))
                                ) {
                            exist = true;
                            $.jGrowl("Não é permitido marcações concorrentes.", {
                                sticky: 4000
                            });
                            return false;
                        }
                    });
            if (exist) {
                revertFunc();
                return false;
            }

            me.change(event, dayDelta, minuteDelta, revertFunc);
        },
        eventResize: function(event, dayDelta, minuteDelta, revertFunc) {

            if (event.max) {
                if (moment.duration(moment(event.end).diff(moment(event.start))).asMinutes() > event.max) {
                    $.jGrowl("A duração maxima deste tipo de maracação é: " + event.max + "m.", {
                        sticky: 4000
                    });
                    revertFunc();
                    return false;
                }
            }
            if (event.min) {
                if (moment.duration(moment(event.end).diff(moment(event.start))).asMinutes() < event.min) {
                    $.jGrowl("A duração minima deste tipo de maracação é: " + event.min + "m.", {
                        sticky: 4000
                    });
                    revertFunc();
                    return false;
                }
            }

            if (event.start < new Date().getTime()) {
                $.jGrowl("Não é permitido alterar o passado.", {
                    sticky: 4000
                });
                revertFunc();
                return false;
            }
            var exist = false;
            $.each(me.calendar.fullCalendar('clientEvents'),
                    function() {
                        if (this.id === event.id) {
                            return true;
                        }

                        if (this.del)
                            return true;

                        if (
                                ((moment(this.start).unix() < moment(event.end).unix()) && (moment(this.start).unix() > moment(event.start).unix())) ||
                                ((moment(this.end).unix() > moment(event.start).unix()) && (moment(this.end).unix() < moment(event.end).unix())) ||
                                ((moment(this.start).unix() <= moment(event.start).unix()) && (moment(this.end).unix() >= moment(event.end).unix()))
                                ) {
                            exist = true;
                            $.jGrowl("Não é permitido marcações concorrentes.", {
                                sticky: 4000
                            });
                            return false;
                        }
                    });
            if (exist) {
                revertFunc();
                return false;
            }
            me.change(event, dayDelta, minuteDelta, revertFunc);
        }
    };
    this.change = function(event, dayDelta, minuteDelta, revertFunc) {
        if (!confirm("Pretende mesmo mudar a data?")) {
            revertFunc();
        } else {
            $.msg();
            $.post("/AM/ajax/calendar.php", {
                id: event.id,
                action: "change",
                start: moment(event.start).unix(),
                end: moment(event.end).unix()
            },
            function(ok) {
                if (!ok) {
                    revertFunc();
                }
                event.changed++;
                me.calendar.fullCalendar('updateEvent', event);
                $.msg('unblock');
            }, "json").fail(function() {
                $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                $.msg('unblock', 5000);
                revertFunc();
            });
        }
    };
    this.reserveConstruct = function(tipo) {
        var
                n,
                temp_elements = "",
                temp_classes = "";
        $.each(tipo, function() {
            if (this.active) {
                n = {
                    color: this.color,
                    rtype: this.id,
                    min: this.min,
                    max: this.max
                };
                temp_elements = temp_elements + "<div class=\"external-event\" style=\"background-color: " + this.color + "\" data-eventobject=" + JSON.stringify(n) + " >" + this.text + "</div>";
            }
            temp_classes = temp_classes + ".t" + this.id + " {background-color: " + this.color + "}";
        });
        $("#external-events .grid-content").html(temp_elements);
        $("#reserve_types").html(temp_classes);

        var
                eventobject;
        me.ext
                .find('div.external-event')
                .each(function() {
                    eventobject = $.extend({
                        editable: true,
                        title: $.trim($(this).text()),
                        lead_id: me.client.id,
                        client_name: me.client.name,
                        codCamp: me.client.codCamp,
                        closed: false
                    }, $(this).data().eventobject);

                    $(this).data('eventobject', eventobject);

                    $(this).draggable({
                        zIndex: 999,
                        revert: true,
                        revertDuration: 0
                    });

                });
        if (typeof me.client.id !== "undefined" && me.resource !== "all") {
            me.ext.show();
        } else {
            me.ext.hide();
        }
    };
    this.makeRefController = function(Refs) {
        var temp = "<tr><td class=\"chex-table\"><input type=\"radio\" checked name=\"single-refs\" value=\"all\" id=\"all\" ><label for=\"all\"><span></span></label></td><td><label for=\"all\" class=\"btn-link\">Todos</label></td></tr>";
        $.each(Refs, function() {
            temp = temp + "<tr><td class=\"chex-table\"><input type=\"radio\" name=\"single-refs\" value=\"" + this.id + "\" id=\"" + this.id + "\" ><label for=\"" + this.id + "\"><span></span></label></td><td><label for=\"" + this.id + "\" class=\"btn-link\">" + this.name + "</label></td></tr>";
        });
        $("#refs tbody").html(temp);
        $("#refs tbody [name=single-refs]").change(function() {
            $.post("/AM/ajax/calendar.php", {
                resource: $(this).val(),
                action: "getRscContent"
            },
            function(dat) {
                me.destroy();
                me = new calendar(me.selector, dat, me.modals, me.ext, me.client, me.user);
                me.reserveConstruct(dat.tipo);
            }, "json");
        });
    };
    this.initModal = function(Refs) {
        me.modal_ext
                .find("#btn_no_consult")
                .popover({
                    placement: "top",
                    html: true,
                    title: "Não há consulta",
                    content: '<form  id="no_consult_confirm">\n\
                                <select id="select_no_consult" class="validate[required]">\n\
                                    <option value="">Seleccione um opção</option>\n\
                                    <option value="DEST">Desistiu</option>\n\
                                    <option value="FAL">Faleceu</option>\n\
                                    <option value="TINV">Telefone Invalido</option>\n\
                                    <option value="NOSHOW">No Show</option>\n\
                                    <option value="NAT">Ninguém em casa</option>\n\
                                    <option value="MOR">Morada Errada</option>\n\
                                    <option value="NTEC">Técnico não foi</option>\n\
                                </select>\n\
                                <button class="btn btn-primary">Fechar</button>\n\
                            </form>',
                    trigger: 'click'
                })
                .on("hidden", function(e) {
                    e.stopPropagation();
                })
                .end()
                .on("submit", "#no_consult_confirm", function(e) {
                    e.preventDefault();
                    var calendar_client = me.modal_ext.data(),
                            cResult = $("#select_no_consult").val();
                    if ($(this).validationEngine('validate')) {
                        $.post("/AM/ajax/consulta.php", {
                            action: "insert_consulta",
                            reserva_id: calendar_client.calEvent.id,
                            lead_id: calendar_client.calEvent.lead_id,
                            closed: 1,
                            consulta: 0,
                            consulta_razao: cResult,
                            exame: "0",
                            exame_razao: "",
                            venda: 0,
                            venda_razao: "",
                            left_ear: 0,
                            right_ear: 0,
                            tipo_aparelho: "",
                            produtos: "",
                            descricao_aparelho: "",
                            feedback: "SCONS"
                        },
                        function() {
                            calendar_client.calEvent.editable = false;
                            calendar_client.calEvent.closed = true;
                            calendar_client.calEvent.del = (cResult === 'DEST' || cResult === 'NOSHOW');
                            calendar_client.calEvent.className += (cResult === 'DEST' || cResult === 'NOSHOW') ? ' del' : '';
                            me.calendar.fullCalendar('updateEvent', calendar_client.calEvent);
                            dropOneConsult();
                        }, "json");
                        me.modal_ext.modal("hide").find(".popover").hide();
                    }
                })
                .find("#btn_change")
                .popover({
                    placement: "top",
                    html: true,
                    title: "Mudar de calendário",
                    content: function() {
                        var
                                opt = "",
                                rsc = me.modal_ext.data().calEvent.rsc;
                        $.each(Refs, function() {
                            if (~~this.id !== rsc) {
                                opt += "<option value='" + this.id + "'>" + this.name + "</option>\n";
                            }
                        });
                        return '<select id="select_change">\n' + opt + '</select>\n\
                            <button class="btn btn-primary" id="change_confirm_button">Mudar</button>';
                    },
                    trigger: 'click'
                })
                .on("hidden", function(e) {
                    e.stopPropagation();
                })
                .end()
                .on("click", "#change_confirm_button", function() {
                    var calendar_client = me.modal_ext.data().calEvent;
                    $.post("/AM/ajax/calendar.php", {
                        action: "changeReservationResource",
                        id: calendar_client.id,
                        resource: $("#select_change").val()
                    },
                    function() {
                        me.calendar.fullCalendar('removeEvents', calendar_client.id);
                    }, "json");
                    me.modal_ext.modal("hide").find(".popover").hide();
                })
                .on("hidden", function() {
                    $(this)
                            .find("#btn_change")
                            .popover('hide');
                    $(this)
                            .find("#btn_no_consult")
                            .popover('hide');
                })
                .find(".btn_trash")
                .click(function() {
                    me.modal_ext.modal("hide");
                    var data = me.modal_ext.data().calEvent;
                    $.post("/AM/ajax/calendar.php", {
                        id: data.id,
                        action: "remove"
                    },
                    function(ok) {
                        if (ok) {
                            me.calendar.fullCalendar('removeEvents', data.id);
                            dropOneConsult();
                        }
                    }, "json");
                })
                .end()
                .css({
                    overflow: "visible"
                })
                .find("#btn_init_consult")
                .add("#btn_view_consult")
                .click(function() {
                    var
                            data = me.modal_ext.modal("hide").data().calEvent,
                            en = btoa(data.lead_id),
                            rs = btoa(data.id);
                    $.history.push("view/consulta.html?id=" + encodeURIComponent(en) + "&rs=" + encodeURIComponent(rs));
                });


        me.modal_special
                .find(".btn_trash")
                .click(function() {
                    me.modal_special.modal("hide");
                    var id = me.modal_special.data().calEvent.id;
                    $.post("/AM/ajax/calendar.php", {
                        id: id,
                        action: "remove"
                    },
                    function(ok) {
                        if (ok) {
                            me.calendar.fullCalendar('removeEvents', id);
                        }
                    }, "json");
                });

        me.modals.mkt
                .find("form").submit(function(e) {
            e.preventDefault();
            if (me.modals.mkt.find("form").validationEngine('validate')) {
                $("#save_mkt").prop('disabled', true);
                $.msg();
                $.post("ajax/requests.php", {
                    action: 'set_mkt_report',
                    id: me.modals.mkt.data().calEvent.extra_id,
                    cod: $(this).find('#cod').val(),
                    total_rastreios: $(this).find('#total_rastreios').val(),
                    rastreios_perda: $(this).find('#rastreios_perda').val(),
                    vendas: $(this).find('#vendas').val(),
                    valor: $(this).find('#valor').val()
                }, function() {
                    me.modals.mkt.modal("hide");
                    $("#save_mkt").prop('disabled', false);
                    $.jGrowl("Relatório enviado com sucesso!");
                    $.msg('unblock');
                    $.each($("#calendar").fullCalendar("clientEvents", function(a) {
                        return a.extra_id === me.modals.mkt.data().calEvent.extra_id;
                    }), function() {
                        this.closed = true;
                        me.calendar.fullCalendar('updateEvent', this);
                    });
                }, 'json').fail(function(data) {
                    $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                    $.msg('unblock', 5000);
                });
                ;
            }
        })
                .find("input:not(:eq(0))").autotab('number');

    };
    this.openClient = function(calEvent) {
        $.msg();
        $.post("/AM/ajax/client.php", {
            id: calEvent.lead_id,
            action: 'byName'
        }, function(data) {
            $.msg('unblock');
            var tmp = "";
            $.each(data, function() {
                tmp = tmp + "<dt>" + this.name + "</dt><dd>- " + this.value + "</dd>";
            });
            if (calEvent.closed) {
                me.modal_ext
                        .find(".modal-footer span")
                        .hide()
                        .end()
                        .find("#btn_view_consult")
                        .show();
            } else if (calEvent.user !== me.user.username && me.user.user_level < 5) {
                me.modal_ext
                        .find(".modal-footer span.left")
                        .hide()
                        .end()
                        .find("#btn_view_consult")
                        .hide();
            } else {
                me.modal_ext
                        .find(".modal-footer span")
                        .show()
                        .end()
                        .find("#btn_view_consult")
                        .hide();
            }
            me.modal_ext
                    .find("#client_info")
                    .html(tmp);
            me.modal_ext
                    .data({
                        calEvent: calEvent
                    })
                    .modal();
        }, "json").fail(function(data) {
            $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
            $.msg('unblock', 5000);
        });
    };
    this.openSpecialEvent = function(calEvent) {

        if (calEvent.user !== me.user.username && me.user.user_level < 5) {
            me.modal_special
                    .find(".btn_trash")
                    .hide();
        } else {
            me.modal_special
                    .find(".btn_trash")
                    .show();
        }

        me.modal_special
                .find(".modal-body")
                .html(calEvent.obs);
        me.modal_special
                .data({
                    calEvent: calEvent
                })
                .modal();
    };

    this.openMkt = function(calEvent) {
        $.msg();
        $.post("ajax/requests.php", {
            action: 'get_one_mkt',
            id: calEvent.extra_id
        }, function(data) {
            var postal = (function() {
                var pt = "";
                $.each(data.local_publicidade, function() {
                    pt += '<dd>' + this.cp + ' - ' + this.freguesia + '</dd>';
                });
                return pt;
            })();
            var html = '<dl class="dl-horizontal">\n\
                            <dt>Pedido</dt>\n\
                            <dd>-' + moment(data.data_criacao).fromNow() + '</dd>\n\
                            <dt>Localidade</dt>\n\
                            <dd>-' + data.localidade + '</dd>\n\
                            <dt>Local</dt>\n\
                            <dd>-' + data.local + '</dd>\n\
                            <dt>Morada</dt>\n\
                            <dd>-' + data.morada + '</dd>\n\
                            <dt>Observações</dt>\n\
                            <dd>-' + data.comments + '</dd>\n\
                            <dt>Códigos Postais</dt>\n\
                            ' + postal + '\n\
                        </dl>';
            me.modals.mkt
                    .find("#tab_mkt_info").html(html)
                    .end()
                    .data({
                        calEvent: calEvent
                    })
                    .find("#tab_mkt_rel")
                    .find("#cod").val((~~data.closed) ? data.cod : '').prop('readonly', ~~data.closed).end()
                    .find("#total_rastreios").val((~~data.closed) ? data.total_rastreios : '').prop('readonly', ~~data.closed).end()
                    .find("#rastreios_perda").val((~~data.closed) ? data.rastreios_perda : '').prop('readonly', ~~data.closed).end()
                    .find("#vendas").val((~~data.closed) ? data.vendas : '').prop('readonly', ~~data.closed).end()
                    .find("#valor").val((~~data.closed) ? data.valor : '').prop('readonly', ~~data.closed).end()
                    .find("#save_mkt").prop('disabled', ~~data.closed).end()
                    .end()
                    .modal('show');
            $.msg('unblock');
        }, 'json').fail(function(data) {
            $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
            $.msg('unblock', 5000);
        });
    };
    this.destroy = function() {
        this.calendar.fullCalendar('destroy');
        $("#external-events .grid-content > div").empty();
        $("#reserve_types").empty();
    };
    this.resource = (typeof data.config !== "undefined" && typeof data.config.events !== "undefined") ? data.config.events.data.resource : "all";
    var config = $.extend(true, this.config, data.config);
    this.calendar = selector.fullCalendar(config);

    if (me.user.user_level > 5) {
        me.modal_ext.find("#btn_change").removeClass("hide");
    }
};