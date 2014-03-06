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
                title: 'Configuration',
                content: $('<form>', {class: 'form-group', role: 'form', id: 'popoverForm', style: 'margin-bottom:0'})
                        .append(
                                $('<div>', {class: 'row'})
                                .append(
                                        $('<div>', {class: 'col-sm-12'})
                                        .append(
                                                $("<label>", {class: 'control-label'}).text('New name'))
                                        .append(
                                                $('<input>', {class: 'form-control input-sm validate[required]', type: 'text', id: 'popInputName', maxlength: "45"})
                                                )
                                        )
                                )
                        .append(
                                $('<div>', {class: 'row', style: 'margin-top:4px'}).append(
                                $('<div>', {class: 'col-sm-12'})
                                .append(
                                        $('<button>', {class: 'btn btn-primary btn-xs', id: 'popSave'}).text('Save'))
                                .append(
                                        $('<button>', {class: 'btn btn-default btn-xs pull-right', id: 'popCancel', style: 'padding: 2px 2px;'}).text('Cancel'))
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
                        .append($("<a>", {class: "fa fa-trash-o color iconSlaveMargin"}))
                        .append($("<a>", {class: "fa fa-wrench color iconSlaveMargin", style: "margin-right: 25px;"}).popover(popObj))

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

                var ok = true;
                if (typeof item.data().status != 'undefined') {

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
            options.push(new Option('New Template', 'default'));
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

        //SPINNER
        $(".spinner").spinner({min: 0});
        //validation
        $('#popoverForm').validationEngine();
        $('#form-template').validationEngine({promptPosition: "topRight:100"});
        //datepickers
        $("#dateStart").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>',
            onClose: function(selectedDate) {
                $("#dateEnd").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#dateEnd").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>',
            onClose: function(selectedDate) {
                $("#dateStart").datepicker("option", "maxDate", selectedDate);
            }
        });
        //selects
        $(".select2").select2();
        $.post("../../php/report_builder/constructor.php", {action: 'getTemplateList'}, function(data) {
            $("#selectTemplateList").append(makeOptions(true, data)).change();
        }, 'json');
        $.post("../../php/report_builder/constructor.php", {action: 'getCampaign'}, function(data) {
            $("#selectcampaign").append(makeOptions(false, data)).change();
        }, 'json');
        $.post("../../php/report_builder/constructor.php", {action: 'getList'}, function(data) {
            $("#selectlist").append(makeOptions(false, data)).change();
        }, 'json');
        $.post("../../php/report_builder/constructor.php", {action: 'getInbound'}, function(data) {
            $("#selectinbound").append(makeOptions(false, data)).change();
        }, 'json');
        $.post("../../php/report_builder/constructor.php", {action: 'getUser'}, function(data) {
            $("#selectUser").append(makeOptions(false, data)).change();
        }, 'json');
        //Events
        $('#mightyNest').on('click', '#popSave', function(e) {
            e.preventDefault();
            if ($('#popoverForm').validationEngine("validate")) {
                $('#mightyNest').find('#' + itemData.id).data().text = $(this).parent().parent().parent().find('#popInputName').val();
                $('#mightyNest').find('#' + itemLabel).text($(this).parent().parent().parent().find('#popInputName').val());
                $('#mightyNest').find('.fa-wrench').popover('hide');
                $('.popover').remove();
                saveTemplate('editTemplate');
            }
        }).on('click', '.fa-wrench', function() {

            itemData = $(this).parent().data();
            itemLabel = $(this).parent().find('.handler').attr('id');
            $('#mightyNest').find('#popInputName').val(itemData.text);
        }).on('click', '#popCancel', function(e) {
            e.preventDefault();
            $('#mightyNest').find('.fa-wrench').popover('hide');
            $('.popover').remove();
        }).on("click", ".fa-trash-o", function() {

            $(this).parent().tooltip('destroy').remove();
            saveTemplate('editTemplate');
        }).on('click', '#graphTypeButton', function(e) {

            $(this).parent().parent().data().graphType = $('#selectGraphType').find(':selected').data('graph');
        }).on('mouseover', '.master', function() {
            $(this).find('.groupIco').show();
            $(this).find('.groupIco').show();
        }).on('mouseout', '.master', function() {

            $(this).find('.groupIco').hide();
            $(this).find('.groupIco').hide();
        }).on('mouseover', '.highlight', function() {
            $(this).find('.color').show();
            $(this).find('.color').show();

        }).on('mouseout', '.highlight', function() {
            $(this).find('.color').hide();
            $(this).find('.color').hide();
        });

        //temporario
        $('#selectAgrupador').on('change', function() {
            $('.slave').remove();
            if ($('#selectAgrupador').find(':selected').val() !== 'default') {
                makeSideLists();
            } else {
                $('#accordion').css('display', 'none');
            }
        });

        //temporario
        /*$('#selectAgrupador').on('change', function() {
         $('.slave').remove();
         if ($('#selectAgrupador').find(':selected').val() !== 'default') {
         $('.panel.panel-default').remove();
         makeSideLists();
         } else {
         $('#accordion').hide();
         }
         });*/

        $('#selectTemplateList').on('change', function() {

            var myTemplateId = $('#selectTemplateList').val();
            $('.master').remove();
            $('.slave').remove();
            if (myTemplateId !== 'default') {
                $('#accordion').hide();
                $('#deleteTempModal').show();
                //  $('#artConstructor').show();
                //$('#artPreview').show();
                $('#selectTemplateSource').prop("disabled", true);
                flagEdit = 'edit';
                $.post("../../php/report_builder/constructor.php", {action: 'getTemplate', templateId: myTemplateId}, function(data) {

                    if (data.tipo === 'campaign') {

                        selectedCabi = '#selectcampaign';
                    } else if (data.tipo === 'list') {

                        selectedCabi = '#selectlist';
                    } else {
                        selectedCabi = '#selectinbound';
                    }

                    $.post("../../php/report_builder/constructor.php", {action: 'getTemplate' + data.tipo + 'Name', idSeries: data.tipo_id}, function(data) {

                        $('#selectAgrupador')
                                .find('option')
                                .remove()
                                .end()
                                .append(new Option('Select outcome Source', 'default'))
                                .append(makeOptions(false, data))
                                .change();
                    }, 'json');
                    $('#selectUser').val(data.users).change();
                    $('.filtroModalDateDiv').hide();
                    if (data.start_date == '1337-10-01') {
                        $('#dateStart').val('');
                        $('#dateEnd').val('');
                        $('#selectTemplateDate').val('dynamicDate').change();
                        $('.spinner').val(data.dynamic_date);
                        $('#dateRangeDayDynamic2').show();
                    } else {
                        $('.spinner').val('1');
                        $('#selectTemplateDate').val('fixedDate').change();
                        $('#dateRangeFixed1').show();
                        $('#dateRangeFixed2').show();
                        $('#dateStart').val(data.start_date);
                        $('#dateEnd').val(data.end_date);
                    }
                    $.post("../../php/report_builder/constructor.php", {action: 'constructPreview', templateId: myTemplateId, localNow: moment().local().format('YYYY-MM-DD'), localSubtract: moment().local().subtract('d', $('#dateRangeDayDynamic2').find('input').val()).format('YYYY-MM-DD')}, function(data) {

                        browserPreview(data);
                    }, 'json');
                    $("#selectTemplateSource").val(data.tipo).change();
                    $('#select' + data.tipo).val(data.tipo_id).change();
                    $('.filtroModalDiv').hide();
                    $('#' + data.tipo + 'ModalDiv').show();
                    $('#daterange').val(moment(data.start).format('YYYY-MM-DD') + ' - ' + moment(data.end).format('YYYY-MM-DD')).change();

                    initiateConstructor(data.template);
                }, 'json');


            } else {
                $('#selectTemplateSource').prop("disabled", false);
                $('#form-template')[0].reset();
                // $('#artConstructor').hide();
                //$('#artPreview').hide();
                $('#deleteTempModal').hide();
                $('#form-template').find('#selectUser').val('').change();
                $('#form-template').find(selectedCabi).val('').change();
            }
        });
        $('.widget-toolbar').on('click', '#addStapleIco', function() {

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
                        $.smallBox({
                            title: "Template saved successfully",
                            color: "#C46A69",
                            iconSmall: "fa fa-times fa-2x fadeInRight animated",
                            timeout: 4000
                        });
                        if ($('#selectTemplateDate').val() == 'dynamicDate') {

                            $('#dateStart').val();
                            $('#dateEnd').val();
                        } else {
                            $('.spinner').val('1');
                        }
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
                $.smallBox({
                    title: "New Template created",
                    color: "#C46A69",
                    iconSmall: "fa fa-times fa-2x fadeInRight animated",
                    timeout: 4000
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
        $('#selectTemplateSource').change(function() {

            $('.filtroModalSourceDiv').hide();
            switch ($(this).val())
            {
                case "campaign":
                    $('#campaignModalDiv').show();
                    selectedCabi = '#selectcampaign';
                    break;
                case "list":
                    $('#listModalDiv').show();
                    selectedCabi = '#selectlist';
                    break;
                case "inbound":
                    $('#inboundModalDiv').show();
                    selectedCabi = '#selectinbound';
                    break;
            }

        });
        $('#selectTemplateDate').change(function() {

            $('.filtroModalDateDiv').hide();

            switch ($(this).val())
            {
                case "fixedDate":

                    $('#dateRangeFixed1').show();
                    $('#dateRangeFixed2').show();
                    break;
                case "dynamicDate":

                    $('#dateRangeDayDynamic2').show();
                    break;
            }
        });
        $('#downloadTemplate').click(function() {

            if ($('#selectTemplateList option:selected').val() != 'default') {

                var url = "../../php/report_builder/constructor.php?templateId=" + $('#selectTemplateList').val() + "&action=templateDownload";
                document.location.href = url;
            } else {
                $('#selectTemplateList').tooltip('show');
            }

        });
        $('#delete').on('click', function() {

            //$('#artConstructor').hide();
            //$('#artPreview').hide();
            $('.master').remove();
            $('.slave').remove();
            $.post("../../php/report_builder/constructor.php", {action: 'deleteTemplate', templateId: $('#selectTemplateList').val()}, function(data) {

                if (data) {
                    $('#selectTemplateList')
                            .find('option:selected')
                            .remove();
                    $('#form-template')[0].reset();
                    $('#form-template').find('select').trigger('chosen:updated');
                    $.smallBox({
                        title: "Template deleted successfully",
                        color: "#C46A69",
                        iconSmall: "fa fa-times fa-2x fadeInRight animated",
                        timeout: 4000
                    });
                }

            }, 'json');
            $('#delete').css('display', 'none');
            $('#modalDeletTemplate').css('display', 'none');
            $('#deleteTempModal').css('display', 'none');
            $('#editTemplate').css('display', 'inline');
            $('#selectTemplateSource').prop("disabled", false);

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

                $('#selectTemplateSource').prop("disabled", false);

            } else {
                $('#selectTemplateSource').prop("disabled", true);

            }

        });
        //sortable lists For the fucking win
        $('#mightyNest').sortable(sortableConfigs);
    }
    function initiateConstructor(myTemplate) {

        $.each(myTemplate, function() {

            $("#mightyNest").append($("<li>", {class: "master group"}).append($("<a>", {class: "fa fa-trash-o groupIco", style: "display:none;"})).append($("<a>", {class: "fa fa-wrench groupIco", style: "display:none;"}).popover(popObj))

                    .append($("<div>", {class: "item_content"})
                            .append($("<label>", {Class: 'handler', id: 'handler' + guid()}).text(this.text)))
                    .append($("<ul>", {style: " padding-right: -6px; padding-left: 8px; "}))
                    .data({id: this.id, graphType: this.graphType, itemType: 'master', text: this.text}).attr('id', this.id)

                    );
            var liId = this.id;
            $.each(this.children, function() {

                $("#mightyNest").find("#" + liId).find('ul').append($("<li>", {id: this.id, class: "highlight master"}).append($("<a>", {class: "fa fa-trash-o color", style: "display:none;"})).append($("<a>", {class: "fa fa-wrench color", style: "display:none;"}).popover(popObj))

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
            //$('#selectAgrupador').find(':selected').val()
            $.post("../../php/report_builder/constructor.php", {action: 'getFeedBack', campId: 'W00003'}, function(data) {

                selectFeeds(data);
            }, 'json');
        } else if (selectedCabi == '#selectlist') {

            $.post("../../php/report_builder/constructor.php", {action: 'getCampaignId', cabiId: selectedCabi, id: $('#selectAgrupador').val()}, function(data) {

                // var campIdList = data;
                var campIdList = 'W00003';
                $.post("../../php/report_builder/constructor.php", {action: 'getFeedBack', campId: campIdList}, function(data) {

                    selectFeeds(data);
                }, 'json');
            }, 'json');
        } else {
            //$('#selectAgrupador').find(':selected').val()
            $.post("../../php/report_builder/constructor.php", {action: 'getFeedBack', id: 'W00003'}, function(data) {
                selectFeeds(data);
            }, 'json');
        }

        $('#list').sortable({group: 'no-drop', drop: false, handle: '.item_content'});
    }
    /*
     function selectFeeds(data) {
     
     var
     sitioId,
     now = '';
     $.each(data, function(index) {
     if (now !== this.group_name) {
     
     $('#accordion').append($('<div>', {class: 'panel panel-default'})
     .append($('<div>', {class: "panel-heading", 'data-toggle': "collapse", 'data-parent': "#accordion", 'href': "#collapse" + index})
     .append($('<h4>', {class: "panel-title"})
     .append($('<a>', {class: "panel-heading", 'data-toggle': "collapse", 'data-parent': "#accordion", 'href': "#collapse" + index}).text(this.group_name)
     .append($('<i>', {class: "fa fa-lg fa-angle-down pull-right"}))
     .append($('<i>', {class: "fa fa-lg fa-angle-up pull-right"}))
     )
     )
     )
     .append($('<div>', {id: "collapse" + index, class: "panel-collapse collapse "})
     .append($('<div>', {class: "panel-body"})
     .append($('<ol>', {class: "simple_with_no_drop vertical", id: index}))
     )
     
     ));
     
     now = this.group_name;
     sitioId = index;
     }
     makeFeeds(sitioId, this, this, guid());
     
     });
     
     $('#accordion').show();
     }
     
     function makeFeeds(sitio, isto, id) {
     
     $("#" + sitio).append($("<li>", {id: 'feed' + id, class: "highlight slave"})
     .append($("<div>", {class: "item_content"})
     .append($("<label>", {Class: 'handler', id: 'handler' + (guid())}).text(isto.name)))
     .data({id: isto.id, status: isto.id, originalText: isto.name, text: isto.name, itemType: 'slave', raw: isto, title: "Original:" + isto.name, propertyOf: $('#selectAgrupador').find(':selected').val(), dataType: 'calls'})
     
     );
     }*/

//temporario
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
                        .append($("<label>", {Class: 'handler', id: 'handler' + (guid())}).text(isto.name)))        //$('#selectAgrupador').find(':selected').val()
                .data({id: isto.id, status: isto.id, originalText: isto.name, text: isto.name, itemType: 'slave', raw: isto, title: "Original:" + isto.name, propertyOf: 'W00003', dataType: 'calls'})

                );
    }
//temporario ^
    function addStaple(callback) {

        var myId = 'Staple' + guid();
        if ($('#selectTemplateList option:selected').val() != 'default') {
            $("#mightyNest").append($("<li>", {class: "master", id: myId}).append($("<a>", {class: "fa fa-trash-o groupIco", style: 'display: none;'})).append($("<a>", {class: "fa fa-wrench groupIco", style: 'display: none;'}).popover(popObj))
                    .append($("<div>", {class: "item_content"})
                            .append($("<label>", {Class: 'handler', id: 'handler' + (guid())}).text('Group')))
                    .append($("<ul>", {style: " padding-right: -6px; padding-left: 8px; "}))
                    .data({id: myId, graphType: 'bars', itemType: 'master', text: 'Group'})
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
                        title: this.title

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
        if ($('#selectTemplateDate').val() == 'dynamicDate') {

            dateRange.start = '1337-10-01';
            dateRange.end = '1337-10-01';
            dateRange.dyn = $('.spinner').val();
        } else {
            dateRange.dyn = $('.spinner').val();

            dateRange.start = moment($('#dateStart').val()).format('YYYY-MM-DD');
            dateRange.end = moment($('#dateEnd').val()).format('YYYY-MM-DD');
        }

        var typeId = [];
        if ($(selectedCabi).val() !== null) {
            typeId = $(selectedCabi).val();
        }

        $.post("../../php/report_builder/constructor.php",
                {
                    action: action,
                    users: $('#selectUser').val(),
                    name: $('#inputName').val(),
                    dateRange: dateRange,
                    type: $('#selectTemplateSource').val(),
                    typeId: JSON.stringify(typeId),
                    template: JSON.stringify(finalSerie),
                    templateId: $('#selectTemplateList').val()

                }, function(data) {

            if (flagEdit == 'save') {

                $("#selectTemplateList").append($('<option>', {value: data.id, text: $('#inputName').val()}));
                $("#selectTemplateList").val(data.id);
                $('#selectTemplateList').change();
                //$('#artConstructor').show();
                //$('#artPreview').show();
                addStaple(function() {
                    saveTemplate('editTemplate');
                });
            } else {
                var teste = $('#selectTemplateList option:selected').val();
                $("#selectTemplateList").append($('<option>', {value: $('#selectTemplateList option:selected').val(), text: $('#inputName').val()}));
                $('#selectTemplateList option:selected').remove();
                $('#selectTemplateList').val(teste);
                if (selectedCabichange) {
                    $.post("../../php/report_builder/constructor.php", {action: 'getTemplate', templateId: $('#selectTemplateList').val()}, function(data) {

                        $.post("../../php/report_builder/constructor.php", {action: 'getTemplate' + data.tipo + 'Name', idSeries: data.tipo_id}, function(data) {

                            var options = [];
                            options.push(new Option('Select', 'default'));
                            $.each(data, function() {

                                options.push(new Option(this.name, this.id));
                            });
                            $('#selectAgrupador')
                                    .find('option')
                                    .remove()
                                    .end()
                                    .append(options).change();
                        }, 'json');
                    }, 'json');
                    selectedCabichange = false;
                }

                $.post("../../php/report_builder/constructor.php", {action: 'constructPreview', templateId: $('#selectTemplateList').val(), localNow: moment().local().format('YYYY-MM-DD'), localSubtract: moment().local().subtract('d', $('#dateRangeDayDynamic2').find('input').val()).format('YYYY-MM-DD')}, function(data) {
                    browserPreview(data);
                }, 'json');
            }

        }, 'json');
    }
    function browserPreview(data) {

        $('.tab-content').remove();
        //variaveis
        var tmp;
        //for each , vai percorrer o array de objectos do ficheiro rphandle.json
        $.each(data, function(index) {
            //var graph e um array
            var
                    graphData = [],
                    graphTicket = [],
                    myGraph = new graph();

            // construção da pagina com Jquery, da tabela
            tmp = $('<div>', {class: 'tab-content'})
                    .append($('<div>', {class: 'tab-pane active', id: index}).append($('<div>', {class: 'tabbable tabs-below'})

                            .append($('<div>', {class: 'tab-content'})
                                    .append($('<div>', {class: 'tab-pane active', id: 'graph' + index}).append(
                                            $("<h2>")
                                            .append($('<span>', {class: 'label bg-color-blue txt-color-white'}).text(this.name))).append($("<div>", {id: "xGraph" + index, class: 'chart'}))
                                            )
                                    .append($('<div>', {class: 'tab-pane', id: 'list' + index}).append(
                                            $("<h2>")
                                            .append($('<span>', {class: 'label bg-color-blue txt-color-white'}).text(this.name)))
                                            .append(
                                                    $("<ul>", {class: "unstyled"})
                                                    ))
                                    )
                            .append($('<ul>', {class: 'nav nav-tabs'})
                                    .append($('<li>', {class: 'active'})
                                            .append($('<a>', {href: '#graph' + index, 'data-toggle': 'tab'}).text('Graph View ').append($('<i>', {class: 'fa fa-bar-chart-o'}))))
                                    .append($('<li>')
                                            .append($('<a>', {href: '#list' + index, 'data-toggle': 'tab'}).text('Table View ').append($('<i>', {class: 'fa fa-list-ul'}))))
                                    )
                            )
                            ).find(".unstyled");
            //dentro desse  abjecto existe um array com uma lista de  objectos, for each para percorrer essa lista. 
            $.each(this.values, function(index) {
                //array que esta a ser feito o push(inserção de informação) com nome e o valor de cada objecto do array
                if (this.value == null) {
                    this.value = 0;
                }

                graphTicket.push([index, this.name.substring(0, 12)]);
                graphData.push([index, this.value]);
                // continuação da construção da pagina em Jquery
                tmp.append(
                        $("<li>", {class: 'text-info', style: 'list-style-type: none;'})
                        // nome do objecto
                        .append(
                                $("<span>", {class: "label label-default"}).append(this.value))// valor desse objecto
                        .append($('<b>').append(":" + this.name))
                        );
            });
            //.append($("<button>"), {class:"btn btn-large btn-inverse"});
            $('#widPreview').find('#contentGraph').append(tmp.end());
            /* vai chamar o função para criar o grafico, no graph vai a lista de objectos, cada objecto  com 1 nome e valor, 
             o #graph+index á o id do grafico, nome do grafico */

            var dataSet = [{label: "", data: graphData, color: "#57889C"}];
            $("#xGraph" + index).is(':visible')
            // if ($("#xGraph" + index).is(':visible')) {
            console.log(graphData);
            myGraph.floatBar("#xGraph" + index, dataSet, graphTicket);
            //}
        });
    }
    /*  function graphConstruct(data, selector, name) {   
     
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
     }*/

    init();
}
);