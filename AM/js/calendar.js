var calendar = (typeof calendar !== "undefined") ? calendar :
        function(selector, data, modal_ext, ext, lead_id) {
            var me = this;
            this.selector = selector;
            this.lead_id = lead_id;
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
                buttonText: {
                    today: 'hoje',
                    month: 'mês',
                    week: 'semana',
                    day: 'dia'
                },
                eventClick: function(calEvent, jsEvent, view) {
                    if (calEvent.className[0] === "bloqueado") {
                        return false;
                    }
                    me.openClient(calEvent.id, calEvent.lead_id);
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
                                if (moment(this.start).unix() === moment(date).unix())
                                {
                                    exist = true;
                                    return true;
                                }
                            });
                    if (exist) {
                        return false;
                    }

                    var cEO = $(this).data('eventObject');
                    cEO.start = moment(date).unix();
                    cEO.end = moment(date).add("minutes", config.defaultEventMinutes).unix();
                    cEO.allDay = allDay;
                    $.post("/AM/ajax/calendar.php",
                            {
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
                    if (!event.url)
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
                            content: event.client_name,
                            trigger: 'hover'
                        });


                    }
                },
                eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
                    me.change(event, dayDelta, minuteDelta, revertFunc);
                },
                eventResize: function(event, dayDelta, minuteDelta, revertFunc) {
                    me.change(event, dayDelta, minuteDelta, revertFunc);
                }
            };
            this.change = function(event, dayDelta, minuteDelta, revertFunc) {
                alert(
                        "The end date of " + event.title + "has been moved " +
                        dayDelta + " days and " +
                        minuteDelta + " minutes."
                        );

                if (!confirm("is this okay?")) {
                    revertFunc();
                } else {
                    $.post("/AM/ajax/calendar.php",
                            {
                                id: event.id,
                                change: true,
                                start: moment(event.start).unix(),
                                end: moment(event.start).unix()
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
                                title: $.trim($(this).text()),
                                color: data.color,
                                rtype: data.rtype,
                                lead_id: me.lead_id
                            };

                            $(this).data('eventObject', eventObject);

                            $(this).draggable({
                                zIndex: 999,
                                revert: true,
                                revertDuration: 0
                            });

                        })
                        .end();

                if (typeof me.lead_id !== "undefined" && me.resource !== "all") {
                    ext.show();
                } else {
                    ext.hide();
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
                    $.post("/AM/jax/calendar.php",
                            {
                                resource: selected[0].id,
                                is_scheduler: selected.data().is_scheduler,
                                init: true
                            },
                    function(dat) {
                        me.destroy();
                        me = new calendar(me.selector, dat, me.modal_ext, me.ext, me.lead_id);
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
                            content: '<select id="select_no_consult" name=""><option value="">Desistiu</option><option value="">Faleceu</option><option value="">Ninguém em casa</option><option value="">No Show</option></select><button class="btn btn-primary no_consult_button">Fechar</button>',
                            trigger: 'click'
                        })
                        .end()
                        .find("#btn_init_consult")
                        .click(function() {
                            me.modal_ext.modal("hide");
                            $("#c_master").hide();
                            $("#c_consult").load("view/consulta.html").show();
                        })
                        .end()
                        .find("#btn_trash")
                        .click(function() {
                            me.modal_ext.modal("hide");
                            var data = me.modal_ext.data();
                            $.post("/AM/ajax/calendar.php",
                                    {
                                        id: data.id,
                                        remove: true
                                    },
                            function(ok) {
                                if (ok) {
                                    me.calendar.fullCalendar('removeEvents', data.id);
                                }
                            }, "json");
                        });
            };
            this.openClient = function(id, lead_id) {
                $.post("/AM/ajax/client.php", {id: lead_id}, function(data) {
                    var tmp = "";
                    $.each(data, function() {
                        tmp = tmp + "<dt>" + this.name + "</dt><dd>" + this.value + "</dd>";
                    });

                    me.modal_ext.find("#client_info")
                            .html(tmp);
                    me.modal_ext.modal().data().id = id;
                    me.modal_ext.modal().data().lead_id = lead_id;
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