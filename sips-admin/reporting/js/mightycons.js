$(function() {

    var
            itemData,
            itemLabel,
            flagEdit = 'edit',
            selectedCabi = '#selectcampaign',
            selectedCabichange = false,
            popObj = {
                trigger: "click",
                placement: "top",
                html: true,
                title: 'Opções de item',
                content: $('<form>', {class: 'form-group', role: 'form', id: 'popoverForm', style: 'margin-bottom:0'})
                        .append(
                                $('<div>', {class: 'row'})
                                .append(
                                        $('<div>', {class: 'col-sm-12'})
                                        .append(
                                                $("<label>", {class: 'control-label'}).text('Novo nome'))
                                        .append(
                                                $('<input>', {class: 'form-control input-sm validate[required]', type: 'text', id: 'popInputName', maxlength: "45"})
                                                )
                                        )
                                )
                        .append(
                                $('<div>', {class: 'row', style: 'margin-top:4px'}).append(
                                $('<div>', {class: 'col-sm-12'})
                                .append(
                                        $('<button>', {class: 'btn btn-primary btn-xs', id: 'popSave'}).text('Gravar'))
                                .append(
                                        $('<button>', {class: 'btn btn-default btn-xs pull-right', id: 'popCancel', style: 'padding: 2px 2px;'}).text('Cancelar'))
                                )
                                )
            },
    sortableConfigs = {
        group: 'no-drop',
        handle: '.item_content',
        onDragStart: function(item, container, _super) {
            var myId = 'feed' + guid();
            // Duplicate items of the no drop area
            if (!container.options.drop) {
                item.clone(true).insertAfter(item);
                _super(item);
                $(item)
                        .append($("<a>", {class: "icon-trash color"}))
                        .append($("<a>", {class: "icon-wrench color"}).popover(popObj))

                        .removeClass('slave').addClass('master')
                        .attr('id', myId)
                        .data('id', myId)

                        .tooltip({placement: 'rigth'});
            }

            item.css({
                height: item.height(),
                width: item.width()
            });
            item.addClass("dragged");
            $("body").addClass("dragging");
        },
        isValidTarget: function(item, container) {

            if (item.data('itemType') === 'master' && container.options.group !== 'no-drop') {

                return item.parent("ol")[0] === container.el[0];
            } else {
                return true;
            }
        },
        onDrop: function(item, container, _super) {

            item.removeClass("dragged").removeAttr("style");
            $("body").removeClass("dragging");
            container.el.removeClass("active");
            _super(item);
            item.data();
            if (item.data('itemType') === 'slave' && container.options.group === 'no-drop') {
                item.remove();
            } else {

                if (typeof item.data().status != 'undefined') {
                    var ok = true;
                    $.each(container.items, function() {
                        thata = $(this).data();
                        itemdata = item.data();
                        if (thata.propertyOf == itemdata.propertyOf && thata.status == itemdata.status) {

                            item.remove();
                            ok = false;
                        }

                    });
                    if (ok) {
                        saveTemplate('editTemplate');
                    }
                }
            }

        }
    };

    function makeOptions(isDefault, data) {

        var options = [];
        if (isDefault) {
            options.push(new Option('Criar nova Template...', 'default'));
        }
        $.each(data, function() {

            options.push(new Option(this.name, this.id));
        });
        return options;
    }

    function guid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    function init() {
        //validation
        $('#popoverForm').validationEngine();
        $('#form-template').validationEngine({promptPosition: "topRight:100"});

        //datepickers
        $("#dateStart").datepicker({
            changeMonth: true,
            onClose: function(selectedDate) {
                $("#dateEnd").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#dateEnd").datepicker({
            defaultDate: "+1d",
            changeMonth: true,
            onClose: function(selectedDate) {
                $("#dateStart").datepicker("option", "maxDate", selectedDate);
            }
        });

        //selects
        // $('#selectTemplateList').chosen
        $(".chosen-select").chosen({no_results_text: "Sem resultados"});

        $.post("../constructor.php", {action: 'getTemplateList'}, function(data) {
            $("#selectTemplateList").append(makeOptions(true, data)).trigger("chosen:updated");
        }, 'json');

        $.post("../constructor.php", {action: 'getCampaign'}, function(data) {
            $("#selectcampaign").append(makeOptions(false, data)).trigger("chosen:updated");
        }, 'json');

        $.post("../constructor.php", {action: 'getList'}, function(data) {
            $("#selectlist").append(makeOptions(false, data)).trigger("chosen:updated");
        }, 'json');

        $.post("../constructor.php", {action: 'getInbound'}, function(data) {
            $("#selectinbound").append(makeOptions(false, data)).trigger("chosen:updated");
        }, 'json');

        $.post("../constructor.php", {action: 'getUser'}, function(data) {

            $("#selectUser").append(makeOptions(false, data)).trigger("chosen:updated");

        }, 'json');

        //Events
        $('#mightyNest').on('click', '#popSave', function(e) {
            e.preventDefault();
            if ($('#popoverForm').validationEngine("validate")) {
                $('#mightyNest').find('#' + itemData.id).data().text = $(this).parent().parent().parent().find('#popInputName').val();
                $('#mightyNest').find('#' + itemLabel).text($(this).parent().parent().parent().find('#popInputName').val());
                $('#mightyNest').find('.icon-wrench').popover('hide');
                $('.popover').remove();
                saveTemplate('editTemplate');
            }
        }).on('click', '.icon-wrench', function() {

            itemData = $(this).parent().data();
            itemLabel = $(this).parent().find('.handler').attr('id');
        }).on('click', '#popCancel', function(e) {
            e.preventDefault();
            $('#mightyNest').find('.icon-wrench').popover('hide');
            $('.popover').remove();
        }).on("click", ".icon-trash", function() {
            $(this).parent().tooltip('destroy').remove();
            saveTemplate('editTemplate');

        }).on('click', '#graphTypeButton', function(e) {
            $(this).parent().parent().data().graphType = $('#selectGraphType').find(':selected').data('graph');
        });

        $('#selectAgrupador').on('change', function() {
            $('.slave').remove();
            if ($('#selectAgrupador').find(':selected').val() !== 'default') {
                makeSideLists();
            } else {
                $('#accordion').css('display', 'none');
            }
        });

        $('#selectTemplateList').on('change', function() {
            var myTemplateId = $('#selectTemplateList').val();
            $('.master').remove();
            $('.slave').remove();
            if (myTemplateId !== 'default') {
                $('#accordion').hide();
                $('#deleteTempModal').show();
                $('#bottom').show();
                $('#radioselectcampaign').prop("disabled", true);
                $('#radioselectlist').prop("disabled", true);
                flagEdit = 'edit';
                $.post("../constructor.php", {action: 'constructPreview', templateId: myTemplateId}, function(data) {
                    browserPreview(data);
                }, 'json');
                $.post("../constructor.php", {action: 'getTemplate', templateId: myTemplateId}, function(data) {

                    if (data.tipo === 'campaign') {

                        selectedCabi = '#selectcampaign';
                    } else if (data.tipo === 'list') {

                        selectedCabi = '#selectlist';
                    } else {
                        selectedCabi = '#selectinbound';
                    }

                    $.post("../constructor.php", {action: 'getTemplate' + data.tipo + 'Name', idSeries: data.tipo_id}, function(data) {
                        $('#selectAgrupador')
                                .find('option')
                                .remove()
                                .end()
                                .append(new Option('Seleccione', 'default'))
                                .append(makeOptions(false, data))
                                .trigger("chosen:updated");
                    }, 'json');

                    $('#selectUser').val(data.users).trigger("chosen:updated");
                    $('#dateStart').val(data.start);
                    $('#dateEnd').val(data.end);
                    $("#radioselect" + data.tipo).prop("checked", true);
                    $('#select' + data.tipo).val(data.tipo_id).trigger('chosen:updated');
                    $('.filtroModalDiv').hide();
                    $('#' + data.tipo + 'ModalDiv').show();
                    $('#daterange').val(moment(data.start).format('YYYY/MM/DD') + ' - ' + moment(data.end).format('YYYY/MM/DD')).change();
                    initiateConstructor(data.template)
                }, 'json');
            } else {
                $('#radioselectcampaign').prop("disabled", false);
                $('#radioselectlist').prop("disabled", false);
                $('#form-template')[0].reset();
                $('#form-template').find('select').trigger('chosen:updated');
                $('#bottom').hide();
                $('#deleteTempModal').hide();
            }
        });

        $('.box-icon').on('click', '#addStapleIco', function() {
            addStaple(function() {
                saveTemplate('editTemplate');
            });
        });

        $("#editTemplate").click(function(e) {
            e.preventDefault();
            if ($('#selectTemplateList').val() == 'default') {

                if ($('#form-template').validationEngine("validate")) {

                    if ($(selectedCabi + ' option:selected').length && $('#selectUser option:selected').length) {

                        $('.master').remove();
                        $('.slave').remove();
                        flagEdit = 'save';
                        $('#modalEditTemplate').modal('show');
                        $('#modalNewTemplate').show();
                        $('#saveTempModal').css('display', 'inline');
                    } else if ($('#selectUser option:selected').length == 0) {

                        $('#chosenMultiDivUser').tooltip('show');
                    } else if ($(selectedCabi + ' option:selected').length == 0) {
                        $('#chosenSelectDiv' + selectedCabi.substring(1)).tooltip('show');
                    }
                }
            } else {
                if ($('#form-template').validationEngine("validate")) {
                    if ($(selectedCabi + ' option:selected').length && $('#selectUser option:selected').length) {
                        flagEdit = 'edit';
                        saveTemplate('editTemplate');
                        $.gritter.add({
                            time: 4000,
                            text: 'Template gravada com sucesso.'

                        });
                        if (selectedCabichange) {
                            $('#accordion').css('display', 'none');
                        }
                    } else if ($('#selectUser option:selected').length == 0) {

                        $('#chosenMultiDivUser').tooltip('show');
                    } else if ($(selectedCabi + ' option:selected').length == 0) {
                        $('#chosenSelectDiv' + selectedCabi.substring(1)).tooltip('show');
                    }
                }
            }
        });
        $("#saveTempModal").click(function(e) {

            e.preventDefault();
            if ($('#inputName').val()) {

                $('#modalEditTemplate').modal('hide');
                saveTemplate('saveTemplate');
                $.gritter.add({
                    time: 4000,
                    text: 'Template criada com sucesso.'
                });
                $('#modalNewTemplate').css('display', 'none');
                $('#saveTempModal').css('display', 'none');
            }
        });
        $("#form-template,#modalNewTemplate").submit(function(e) {
            e.preventDefault();
        });

        $('#deleteTempModal').click(function() {

            if ($('#selectTemplateList option:selected').val() != 'default') {
                $('#modalEditTemplate').modal('show');
                $('#modalDeletTemplate').css('display', 'block');
                $('#delete').css('display', 'inline');
            } else {
                $('#selectTemplateList').tooltip('show');
            }
        });

        $('input[name="cabi"]').change(function() {

            $('.filtroModalDiv').hide();
            switch ($(this).val())
            {
                case "1":
                    $('#campaignModalDiv').show();
                    selectedCabi = '#selectcampaign';
                    break;
                case "2":
                    $('#listModalDiv').show();
                    selectedCabi = '#selectlist';
                    break;
                case "3":
                    $('#inboundModalDiv').show();
                    selectedCabi = '#selectinbound';
                    break;
            }

        });
        $('#downloadTemplate').click(function() {

            if ($('#selectTemplateList option:selected').val() != 'default') {

                var url = "../constructor.php?templateId=" + $('#selectTemplateList').val() + "&action=templateDownload";
                document.location.href = url;
            } else {
                $('#selectTemplateList').tooltip('show');
            }

        });
        $('#delete').on('click', function() {

            $('#bottom').css('display', 'none');
            $('.master').remove();
            $('.slave').remove();
            $.post("../constructor.php", {action: 'deleteTemplate', templateId: $('#selectTemplateList').val()}, function(data) {

                if (data) {
                    $('#selectTemplateList')
                            .find('option:selected')
                            .remove();
                    $('#form-template')[0].reset();
                    $('#form-template').find('select').trigger('chosen:updated');
                    $.gritter.add({
                        time: 4000,
                        text: 'Template apagada com sucesso.'
                    });
                }

            }, 'json');
            $('#delete').css('display', 'none');
            $('#modalDeletTemplate').css('display', 'none');
            $('#deleteTempModal').css('display', 'none');
            $('#editTemplate').css('display', 'inline');
            $('#radioselectcampaign').prop("disabled", false);
            $('#radioselectlist').prop("disabled", false);
        });

        $(selectedCabi).on('change', function() {
            if ($("#selectTemplateList").val() !== 'default') {
                var feedCheck = false;
                var feed;
                if ($('#mightyNest').sortable('serialize').length !== 0) {

                    $.each($('#mightyNest').sortable('serialize'), function() {

                        if (this.hasOwnProperty('children')) {
                            $.each(this.children, function() {
                                feed = this;
                                if ($('#selectcampaign').serializeArray().length !== 0) {
                                    $.each($('#selectcampaign').serializeArray(), function() {

                                        if (feed.propertyOf !== this.value) {
                                            feedCheck = true;
                                        } else {
                                            feedCheck = false;
                                            return;
                                        }
                                    });
                                } else {

                                    $('#mightyNest').find('.highlight.master').remove();
                                    saveTemplate('editTemplate');
                                    $('#accordion').css('display', 'none');
                                }

                                if (feedCheck) {

                                    $('#mightyNest').find('#' + this.id).remove();
                                    saveTemplate('editTemplate');
                                    feedCheck = false;
                                    $('#accordion').css('display', 'none');
                                }
                            });
                        }
                    }
                    );
                }
            }
            selectedCabichange = true;
            if ($('#selectcampaign').serializeArray().length == 0) {

                $('#radioselectcampaign').prop("disabled", false);
                $('#radioselectlist').prop("disabled", false);
            } else {
                $('#radioselectcampaign').prop("disabled", true);
                $('#radioselectlist').prop("disabled", true);
            }

        });
        //sortable lists For the fucking win
        $('#mightyNest').sortable(sortableConfigs);
    }
    function initiateConstructor(myTemplate) {

        $.each(myTemplate, function() {

            $("#mightyNest").append($("<li>", {class: "master"}).append($("<a>", {class: "icon-trash"})).append($("<a>", {class: "icon-wrench"}).popover(popObj))

                    .append($("<div>", {class: "item_content"})
                            .append($("<label>", {Class: 'handler', id: 'handler' + guid()}).text(this.text)))
                    .append($("<ul>", {style: " padding-right: -6px; padding-left: 8px; "}))
                    .data({id: this.id, graphType: this.graphType, itemType: 'master', text: this.text}).attr('id', this.id)

                    );
            var liId = this.id;
            $.each(this.children, function() {

                $("#mightyNest").find("#" + liId).find('ul').append($("<li>", {id: this.id, class: "highlight master"}).append($("<a>", {class: "icon-trash color"})).append($("<a>", {class: "icon-wrench color"}).popover(popObj))

                        .append($("<div>", {class: "item_content"})
                                .append($("<label>", {Class: 'handler', id: 'handler' + guid()}).text(this.text)))
                        .data({id: this.id, status: this.status, originalText: this.originalText, text: this.text, itemType: 'slave', raw: this.raw, title: this.title, propertyOf: this.propertyOf, stapleId: this.stapleId}).tooltip({placement: 'rigth'})
                        );
            });
        });
    }
    currentMousePos = {};
    $(document).mousemove(function(event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
    });
    function makeSideLists() {

        if (selectedCabi == '#selectcampaign') {

            $.post("../constructor.php", {action: 'getFeedBack', campId: $('#selectAgrupador').find(':selected').val()}, function(data) {

                selectFeeds(data);
            }, 'json');
        } else if (selectedCabi == '#selectlist') {

            $.post("../constructor.php", {action: 'getCampaignId', cabiId: selectedCabi, id: $('#selectAgrupador').val()}, function(data) {

                var campIdList = data;
                $.post("../constructor.php", {action: 'getFeedBack', campId: campIdList}, function(data) {

                    selectFeeds(data);
                }, 'json');
            }, 'json');
        } else {
            $.post("../constructor.php", {action: 'getInboundFeeds', id: $('#selectAgrupador').find(':selected').val()}, function(data) {
                selectFeeds(data, 'inbound');
            }, 'json');
        }

        $('#list').sortable({group: 'no-drop', drop: false, handle: '.item_content'});
    }

    function selectFeeds(data) {

        var count = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
        $.each(data, function() {
            var flag = false;
            if (this.human_answered === 'Y') {
                count[0] += 1;
                makeFeeds('respHumana', this, guid());
                flag = true;
            }

            if (this.sale === 'Y') {
                count[1] += 1;
                makeFeeds('sucesso', this, guid());
                flag = true;
            }

            if (this.dnc === 'Y') {
                count[2] += 1;
                makeFeeds('listaNegra', this, guid());
                flag = true;
            }

            if (this.customer_contact === 'Y') {
                count[3] += 1;
                makeFeeds('contUtil', this, guid());
                flag = true;
            }

            if (this.not_interested === 'Y') {
                count[4] += 1;
                makeFeeds('negativo', this, guid());
                flag = true;
            }

            if (this.unworkable === 'Y') {
                count[5] += 1;
                makeFeeds('naoUtil', this, guid());
                flag = true;
            }

            if (this.scheduled_callback === 'Y') {
                count[6] += 1;
                makeFeeds('callBack', this, guid());
                flag = true;
            }

            if (this.completted === 'Y') {
                count[7] += 1;
                makeFeeds('contFechado', this, guid());
                flag = true;
            }

            if (!flag) {
                count[8] += 1;
                makeFeeds('feedSistema', this, guid());
            }

        });
        $.each(count, function(index) {
            $('#accordion').css('display', 'block');

            if (this > 0) {

                $('#accordion').find('#' + index).css('display', 'block');
                $('#totalFeed' + index).text(this);
            } else {
                $('#accordion').find('#' + index).css('display', 'none');
            }

        });
    }

    function makeFeeds(sitio, isto, id) {

        $("#" + sitio).append($("<li>", {id: 'feed' + id, class: "highlight slave"})
                .append($("<div>", {class: "item_content"})
                        .append($("<label>", {Class: 'handler', id: 'handler' + (guid())}).text(isto.name)))
                .data({id: isto.id, status: isto.id, originalText: isto.name, text: isto.name, itemType: 'slave', raw: isto, title: "Original:" + isto.name + "\n" + "Pertence:" + $('#selectAgrupador').find(':selected').text(), propertyOf: $('#selectAgrupador').find(':selected').val(), dataType: 'calls'})

                );
    }

    function addStaple(callback) {

        var myId = 'Staple' + guid();
        if ($('#selectTemplateList option:selected').val() != 'default') {
            $("#mightyNest").append($("<li>", {class: "master", id: myId}).append($("<a>", {class: "icon-trash"})).append($("<a>", {class: "icon-wrench"}).popover(popObj))
                    .append($("<div>", {class: "item_content"})
                            .append($("<label>", {Class: 'handler', id: 'handler' + (guid())}).text('Agrupador')))
                    .append($("<ul>", {style: " padding-right: -6px; padding-left: 8px; "}))
                    .data({id: myId, graphType: 'bars', itemType: 'master', text: 'Agrupador'})
                    );
            if (typeof callback === "function") {
                callback();
            }

        } else {
            $('#selectTemplateList').tooltip('show');
        }
    }
    function manualSerialize() {
        var
                temp = {},
                serie = $('#mightyNest').sortable('serialize').get(),
                finalSerie = [];
        $.each(serie, function() {
            temp = {
                graphType: this.graphType,
                id: this.id,
                itemType: this.itemType,
                text: this.text,
                children: []
            };
            if (typeof this.children === 'object') {
                $.each(this.children, function() {
                    temp.children.push({
                        id: this.id,
                        itemType: this.itemType,
                        originalText: this.originalText,
                        propertyOf: this.propertyOf,
                        raw: this.raw,
                        status: this.status,
                        text: this.text,
                        title: this.title,
                        stapleId: this.stapleId
                    });
                });
            }
            finalSerie.push(temp);
        });
        return finalSerie;
    }

    function saveTemplate(action) {

        var
                dateRange = {},
                finalSerie = manualSerialize();
        dateRange.start = moment($('#dateStart').val()).format('YYYY-MM-DD');
        dateRange.end = moment($('#dateEnd').val()).format('YYYY-MM-DD');

        var typeId = [];
        if ($(selectedCabi).val() !== null) {
            typeId = $(selectedCabi).val();
        }
        $.post("../constructor.php",
                {
                    action: action,
                    users: $('#selectUser').val(),
                    name: $('#inputName').val(),
                    dateRange: dateRange,
                    type: $('input[name=cabi]:checked', '#form-template').val(),
                    typeId: JSON.stringify(typeId),
                    template: JSON.stringify(finalSerie),
                    templateId: $('#selectTemplateList').val()
                }, function(data) {

            if (flagEdit == 'save') {

                $("#selectTemplateList").append($('<option>', {value: data, text: $('#inputName').val()}));
                $("#selectTemplateList").val(data);
                $('#selectTemplateList').trigger('chosen:updated');
                $('#bottom').css('display', 'block');
                $('#selectTemplateList').change();
                addStaple(function() {
                    saveTemplate('editTemplate');
                });
            } else {
                var teste = $('#selectTemplateList option:selected').val();
                $("#selectTemplateList").append($('<option>', {value: $('#selectTemplateList option:selected').val(), text: $('#inputName').val()}));
                $('#selectTemplateList option:selected').remove();
                $('#selectTemplateList').val(teste);
                if (selectedCabichange) {
                    $.post("../constructor.php", {action: 'getTemplate', templateId: $('#selectTemplateList').val()}, function(data) {

                        $.post("../constructor.php", {action: 'getTemplate' + data.tipo + 'Name', idSeries: data.tipo_id}, function(data) {

                            var options = [];
                            options.push(new Option('Seleccione', 'default'));
                            $.each(data, function() {

                                options.push(new Option(this.name, this.id));
                            });
                            $('#selectAgrupador')
                                    .find('option')
                                    .remove()
                                    .end()
                                    .append(options).trigger("chosen:updated");
                        }, 'json');
                    }, 'json');
                    selectedCabichange = false;
                }
                $.post("../constructor.php", {action: 'constructPreview', templateId: $('#selectTemplateList').val()}, function(data) {

                    browserPreview(data);
                }, 'json');
            }

        }, 'json');
    }

    function browserPreview(data) {

        $('.agrupador').remove();
        //variaveis
        var tmp;
        //for each , vai percorrer o array de objectos do ficheiro rphandle.json
        $.each(data, function(index) {
            //var graph e um array
            graph = [];
            // construção da pagina com Jquery, da tabela
            tmp = $("<div>", {class: "row agrupador", style: " zoom: 0.85; "})
                    .append($("<div>", {class: "widget col-sm-3"})
                            .append(
                                    $("<h2>")
                                    .append(
                                            $("<span>", {class: "glyphicons charts"})
                                            .append(
                                                    $("<i>"))
                                            ).append(this.name)// nome do primeiro objecto dentro do array
                                    )
                            .append(
                                    $("<hr>"))
                            .append(
                                    $("<div>", {class: "content"})
                                    .append(
                                            $("<div>", {class: "sparkLineStats"})
                                            .append(
                                                    $("<ul>", {class: "unstyled"}))
                                            )
                                    )
                            )
                    // Jquery do Grafico          
                    .append($("<div>", {class: "col-sm-9"})
                            .append(
                                    $("<div>", {class: "box"})
                                    ).append(($("<div>", {class: "box-header"})).append($("<h2>")).append($("<i>", {class: "icon-bar-chart"})).append($("<span>", {class: "break"})))

                            .append((
                                    $("<div>", {class: "box-content"})
                                    ).append(
                                    $("<figure>", {'class': "demo", id: "graph" + index, style: " height: 300px;"})
                                    ))


                            ).find("ul");
            //dentro desse  abjecto existe um array com uma lista de  objectos, for each para percorrer essa lista. 
            $.each(this.values, function() {
                //array que esta a ser feito o push(inserção de informação) com nome e o valor de cada objecto do array
                if (this.value == null) {
                    this.value = 0;
                }
                graph.push({"x": this.name.substring(0, 12) + "...", "y": this.value});
                // continuação da construção da pagina em Jquery
                tmp.append(
                        $("<li>")
                        // nome do objecto
                        .append(
                                $("<span>", {class: "number"}).append(this.value))// valor desse objecto
                        .append($('<b>').append(":" + this.name))
                        );
            });
            //.append($("<button>"), {class:"btn btn-large btn-inverse"});

            $("#content").append(tmp.end());
            /* vai chamar o função para criar o grafico, no graph vai a lista de objectos, cada objecto  com 1 nome e valor, 
             o #graph+index á o id do grafico, nome do grafico */

            var x = graphConstruct(graph, "#graph" + index, ".graph" + index);
        });
    }

    function graphConstruct(data, selector, name) {

        var obj = {
            "xScale": "ordinal",
            "yScale": "linear",
            "main": [
                {
                    "className": name,
                    "data": data}
            ]
        };
        var tt = document.createElement('div'),
                leftOffset, //= -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
                topOffset; // = -32;
        tt.className = 'ex-tooltip';
        document.body.appendChild(tt);
        tt.style.zIndex = 1000;
        var opts = {
            "tickHintX": 10,
            'yMin': 0,
            "mouseover": function(d, i) {
                var pos = $(this).offset();
                $(tt).text((d.x) + ': ' + d.y)
                        .css({top: currentMousePos.y + 5, left: currentMousePos.x - 2})
                        .show();
            },
            "mouseout": function() {
                tt.style.display = 'none';
            }
        };
        return new xChart("bar", obj, selector, opts);
    }
    init();
}
);