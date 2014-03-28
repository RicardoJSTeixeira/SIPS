var calendar = function(selector, data, modal_ext, ext, client) {
    var me = this;
    this.selector = selector;
    this.client = client || {};
    this.ext = ext;
    this.resource = "all";
    this.modal_ext = modal_ext;
    this.calendar = undefined;
    this.config = {
        header: {center: 'agendaDay agendaWeek month'},
        events: {
            url: "/AM/ajax/calendar.php",
            type: "POST",
            data: {
                action: "GetReservations",
                resource: "all",
                is_scheduler: false
            }
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
            return ~~new Date().getUTCHours() - 2;
        })(),
        buttonText: {
            today: 'hoje',
            month: 'mês',
            week: 'semana',
            day: 'dia'
        },
        eventClick: function(calEvent, jsEvent, view) {
            if (calEvent.className[0] === "bloqueado" || !calEvent.editable) {
                return false;
            }
            me.openClient(calEvent.id, calEvent.lead_id, calEvent);
        },
        droppable: {
            agenda: true,
            month: false
        },
        drop: function(date, allDay) {
            if (!me.calendar.fullCalendar('getView').name.match("agenda")) {
                return false;
            }
            if (date < new Date().getTime()) {
                return false;
            }
            var exist = false;
            $.each(me.calendar.fullCalendar('clientEvents'),
                    function() {
                        if (((moment(this.start).unix() <= moment(date).unix()) && (moment(this.end).unix() >= moment(date).unix())) || ((moment(this.start).unix() >= moment(date).unix()) && (moment(this.end).unix() >= moment(date).unix())))
                        {
                            exist = true;
                            $.jGrowl("Não é permitido marcações concorrentes.", {sticky: 4000});
                            return false;
                        }
                    });
            if (exist) {
                return false;
            }

            var cEO = $.extend({}, $(this).data('eventObject'));
            ;
            cEO.start = moment(date).unix();
            cEO.end = moment(date).add("minutes", config.defaultEventMinutes).unix();
            cEO.allDay = allDay;
            $.post("/AM/ajax/calendar.php",
                    {
                        action: "newReservation",
                        resource: me.resource,
                        rtype: cEO.rtype,
                        lead_id: cEO.lead_id,
                        start: cEO.start,
                        end: cEO.end
                    },
            function(id) {
                cEO.id = id;
                $('#calendar').fullCalendar('renderEvent', cEO, true);
            },
                    "json");
        },
        eventRender: function(event, element, view) {
            console.log(event.className)
            if (!event.url && !event.bloqueio)
            {
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
                    content: '<dl class="dl-horizontal"><dt>Nome</dt><dd>' + event.client_name + '</dd><dt>Campanha</dt><dd>' + event.codCamp + '</dd></dl>',
                    trigger: 'hover'
                });


            }
        },
        eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
            if (event.start < new Date().getTime()) {
                revertFunc();
                return false;
            }
            var exist = false;
            $.each(me.calendar.fullCalendar('clientEvents'),
                    function() {
                        if (this.id === event.id)
                        {
                            return true;
                        }
                        if (
                                ((moment(this.start).unix() < moment(event.end).unix()) && (moment(this.start).unix() > moment(event.start).unix()))
                                ||
                                ((moment(this.end).unix() > moment(event.start).unix()) && (moment(this.end).unix() < moment(event.end).unix()))
                                ||
                                ((moment(this.start).unix() <= moment(event.start).unix()) && (moment(this.end).unix() >= moment(event.end).unix()))
                                )
                        {
                            exist = true;
                            $.jGrowl("Não é permitido marcações concorrentes.", {sticky: 4000});
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
            me.change(event, dayDelta, minuteDelta, revertFunc);
        }
    };
    this.change = function(event, dayDelta, minuteDelta, revertFunc) {
        if (!confirm("Pretende mesmo mudar a data?")) {
            revertFunc();
        } else {
            $.post("/AM/ajax/calendar.php",
                    {
                        id: event.id,
                        action: "change",
                        start: moment(event.start).unix(),
                        end: moment(event.end).unix()
                    },
            function(ok) {
                if (!ok) {
                    revertFunc();
                }
            }, "json").fail(revertFunc);
        }
    };
    this.reserveConstruct = function(tipo) {
        var
                temp_elements = "",
                temp_classes = "";
        $.each(tipo, function() {
            temp_elements = temp_elements + "<div class=\"external-event\" style=\"background-color: " + this.color + "\" data-color=\"" + this.color + "\" data-rtype=\"" + this.id + "\">" + this.text + "</div>";
            temp_classes = temp_classes + ".t" + this.id + " {background-color: " + this.color + "}";
        });
        $("#external-events .grid-content").html(temp_elements);
        $("#reserve_types").html(temp_classes);

        var
                data,
                eventObject;
        me.ext
                .find('div.external-event')
                .each(function() {
                    data = $(this).data();
                    eventObject = {
                        editable: true,
                        title: $.trim($(this).text()),
                        color: data.color,
                        rtype: data.rtype,
                        lead_id: me.client.id,
                        client_name: me.client.name,
                        codCamp: me.client.codCamp,
                        close: false
                    };

                    $(this).data('eventObject', eventObject);

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
        var temp = "<tr><td class=\"chex-table\"><input type=\"radio\" checked name=\"single-refs\" id=\"all\" data-is_scheduler=\"0\" ><label for=\"all\"><span></span></label></td><td><label for=\"all\" class=\"btn-link\">Todos</label></td></tr>";
        $.each(Refs, function() {
            temp = temp + "<tr><td class=\"chex-table\"><input type=\"radio\" name=\"single-refs\" id=\"" + this.id + "\" data-is_scheduler=\"" + this.is_scheduler + "\"><label for=\"" + this.id + "\"><span></span></label></td><td><label for=\"" + this.id + "\" class=\"btn-link\">" + this.name + "</label></td></tr>";
        });
        $("#refs tbody").html(temp);
        $("#refs tbody input").change(function() {
            var selected = $(this);
            $.post("/AM/ajax/calendar.php",
                    {
                        resource: selected[0].id,
                        is_scheduler: selected.data().is_scheduler,
                        action: "getRscContent"
                    },
            function(dat) {
                me.destroy();
                me = new calendar(me.selector, dat, me.modal_ext, me.ext, me.client);
                me.reserveConstruct(dat.tipo);
            }, "json");
        });
    };
    this.initModal = function() {
        me.modal_ext
                .find("#btn_no_consult")
                .popover({
                    placement: "top",
                    html: true,
                    title: "Não há consulta",
                    content: '<select id="select_no_consult" name="">\n\
                                <option value="DEST">Desistiu</option>\n\
                                <option value="RM">Remarcou</option>\n\
                                <option value="FAL">Faleceu</option>\n\
                                <option value="TINV">Telefone Invalido</option>\n\
                                <option value="NOSHOW">No Show</option>\n\
                                <option value="NATEN">Ninguém em casa</option>\n\
                                <option value="MOR">Morada Errada</option>\n\
                                <option value="NTEC">Técnico não foi</option>\n\
                            </select>\n\
                            <button class="btn btn-primary" id="no_consult_confirm_button">Fechar</button>',
                    trigger: 'click'
                })
                .end()
                .find("#btn_init_consult")
                .click(function() {
                    var
                            en = btoa(me.modal_ext.modal("hide").data().lead_id),
                            rs = btoa(me.modal_ext.modal("hide").data().lead_id);
                    $.history.push("view/consulta.html?id=" + encodeURIComponent(en) + "&rs=" + encodeURIComponent(rs));
                })
                .end()
                .find("#btn_trash")
                .click(function() {
                    me.modal_ext.modal("hide");
                    var data = me.modal_ext.data();
                    $.post("/AM/ajax/calendar.php",
                            {
                                id: data.id,
                                action: "remove"
                            },
                    function(ok) {
                        if (ok) {
                            me.calendar.fullCalendar('removeEvents', data.id);
                        }
                    }, "json");
                })
                .end()
                .on("click", "#no_consult_confirm_button", function()
                    {var calendar_client = me.modal_ext.data();
                    $.post("/AM/ajax/consulta.php",
                            {
                                action: "insert_consulta",
                                reserva_id: calendar_client.id,
                                lead_id: calendar_client.lead_id,
                                consulta: 0,
                                consulta_razao: $("#select_no_consult").val(),
                                exame: "0",
                                exame_razao: "",
                                venda: 0,
                                venda_razao: "",
                                left_ear: 0,
                                right_ear: 0,
                                tipo_aparelho: "",
                                descricao_aparelho: "",
                                feedback: "Sem consulta"
                            },
                            function(){
                                calendar_client.calEvent.editable=false;
                                me.calendar.fullCalendar('updateEvent', calendar_client.calEvent);
                            }
                    , "json");
                    me.modal_ext.modal("hide").find(".popover").hide();
                });
        ;
    };
    this.openClient = function(id, lead_id, calEvent) {
        $.post("/AM/ajax/client.php", {id: lead_id, action: 'byName'}, function(data) {
            var tmp = "";
            $.each(data, function() {
                tmp = tmp + "<dt>" + this.name + "</dt><dd>" + this.value + "</dd>";
            });

            me.modal_ext
                    .find("#client_info")
                    .html(tmp);
            me.modal_ext
                    .data({id: id, lead_id: lead_id, calEvent: calEvent})
                    .modal();
        }, "json");
    };
    this.userWidgetPopulate = function() {
        $.post("/AM/ajax/client.php", {id: me.client.id, action: 'default'}, function(data) {
            $("#client")
                    .find(".user-name").text(data.name)
                    .end()
                    .find(".user-email").text(data.address)
                    .end()
                    .find(".user-date").text(data.bDay)
                    .end()
                    .show();
        }, "json");
    };
    this.destroy = function() {
        this.calendar.fullCalendar('destroy');
        $("#external-events .grid-content > div").empty();
        $("#reserve_types").empty();
    };
    this.resource = (typeof data.config !== "undefined" && typeof data.config.events !== "undefined") ? data.config.events.data.resource : "all";
    var config = $.extend(true, this.config, data.config);
    this.calendar = selector.fullCalendar(config);
};
