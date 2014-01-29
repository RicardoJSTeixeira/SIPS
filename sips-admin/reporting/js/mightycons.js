$(function() {

    var flagEdit = 'edit';
    var myAutoIncrement = 0;
    var selectedCabi = '#selectcampaign';
    $('#modalNewTemplate').validationEngine();

    $('#preView').tooltip();
    $('#saveTemplateList').tooltip();
    $('#addStaple').tooltip();
    $('#chosenSelectDiv').tooltip();

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

    $("#selectAgrupador").chosen({no_results_text: "Sem resultados", placeholder_text: 'Selecione uma Campanha'});

    $('#selectAgrupador').on('change', function() {
        $('.slave').remove();

        if ($('#selectAgrupador').find(':selected').val() !== 'default') {
            makeSideLists();
        } else {
            $('.slave').remove();

            for (var i = 0; i < 8; i++) {

                $('#totalFeed' + i).text('');
            }
        }
    });

    $.post("../constructor.php", {action: 'getTemplateList'}, function(data) {

        var options = [];
        $.each(data, function() {

            options.push(new Option(this.name, this.id_template));

        });
        $("#selectTemplateList").append(options).trigger("chosen:updated");

    }, 'json');

    $('#selectTemplateList').on('change', function() {

        if ($("#selectTemplateList").val() != 'default') {

            $('#divSelectTemplate').tooltip('destroy');
            $('.master').remove();
            $('.slave').remove();
            flagEdit = 'edit';

            $.post("../constructor.php", {action: 'getTemplate', templateId: $('#selectTemplateList').val()}, function(data) {

                var myTemplate;
                var options = [];
                var myArr = data.tipo_id;

                if (data.tipo == 'campaign') {

                    selectedCabi = '#selectcampaign';

                } else if (data.tipo == 'list') {

                    selectedCabi = '#selectlist';

                } else {
                    selectedCabi = '#selectinbound';
                }

                $.post("../constructor.php", {action: 'getTemplate' + data.tipo + 'Name', idSeries: data.tipo_id}, function(data) {

                    var options = [];

                    options.push(new Option('Selecionar Template', 'default'));
                    $.each(data, function() {

                        options.push(new Option(this.name, this.id));

                    });

                    $('#selectAgrupador').find('option').remove();
                    $("#selectAgrupador").append(options).trigger("chosen:updated");

                }, 'json');

                $('#selectUser').val(data.users).trigger("chosen:updated");

                $('#inputName').val(data.name);

                $('#dateStart').val(data.start);

                $('#dateEnd').val(data.end);

                $("#radio" + data.tipo).prop("checked", true);

                $('#select' + data.tipo).val(data.tipo_id).trigger('chosen:updated');

                $('#daterange').val(moment(data.start).format('YYYY/MM/DD') + ' - ' + moment(data.end).format('YYYY/MM/DD')).change();

                myTemplate = data.template;

                $.each(myTemplate, function() {

                    $("#mightyNest").append($("<li>", {class: "master"})
                            .append(
                                    $("<div>", {class: "item_handle"})
                                    .append($("<i>", {class: "icon-paper-clip"})))
                            .append($("<div>", {class: "item_content"})
                                    .append($("<input>", {class: "inputNinja", type: "text", value: this.text}))
                                    .append($("<a>", {class: "icon icon-trash"}))
                                    /* .append(
                                     $("<a>", {class: "icon icon-pencil"})
                                     
                                     .popover({
                                     html: true,
                                     title: 'Select graph',
                                     placement: 'left',
                                     content: function() {
                                     
                                     return $('#graphType').html();
                                     
                                     }
                                     })
                                     )*/)
                            .append($("<ul>"))
                            .data({id: this.id, graphType: this.graphType, itemType: 'master', text: this.text}).attr('id', this.id)

                            );

                    var liId = this.id;
                    $.each(this.children, function() {

                        $("#mightyNest").find("#" + liId).find('ul').append($("<li>", {id: this.id, class: "highlight master"})
                                .append(
                                        $("<div>", {class: "item_handle"})
                                        .append($("<i>", {class: "icon-circle"})))
                                .append($("<div>", {class: "item_content"}).append($("<a>", {class: "icon icon-trash color"}))
                                        .append($("<input>", {class: "inputNinja color", type: "text", value: this.text})))
                                .data({id: this.id, status: this.status, originalText: this.originalText, text: this.text, itemType: 'slave', raw: this.raw, title: this.title}).tooltip()
                                );
                    });

                });


            }, 'json');

        } else {
            $('.master').remove();
            $('.slave').remove();
        }
    });

    $("#selectcampaign").chosen({no_results_text: "Sem resultados", placeholder_text: 'Selecione uma Campanha'});

    $.post("../constructor.php", {action: 'getCampaign'}, function(data) {

        var options = [];
        $.each(data, function() {

            options.push(new Option(this.name, this.id));

        });

        $("#selectcampaign").append(options).trigger("chosen:updated");


    }, 'json');

    $("#selectlist").chosen({no_results_text: "Sem resultados", placeholder_text: 'Selecione uma Lista'});

    $.post("../constructor.php", {action: 'getList'}, function(data) {

        var options = [];
        $.each(data, function() {

            options.push(new Option(this.list_name, this.list_id));

        });

        $("#selectlist").append(options).trigger("chosen:updated");

    }, 'json');

    $("#selectinbound").chosen({no_results_text: "Sem resultados", placeholder_text: 'Selecione um Inbound'});

    $.post("../constructor.php", {action: 'getInbound'}, function(data) {

        var options = [];
        $.each(data, function() {

            options.push(new Option(this.name, this.id));

        });

        $("#selectinbound").append(options).trigger("chosen:updated");


    }, 'json');

    $("#selectUser").chosen({placeholder_text_multiple: 'Selecionar Utilizadores'});

    $.post("../constructor.php", {action: 'getUser'}, function(data) {
        var options = [];

        $.each(data, function() {

            options.push(new Option(this.user));

        });

        $("#selectUser").append(options).trigger("chosen:updated");


    }, 'json');

    /*
     $.getJSON('../rphandle.json', function(data) {
     
     var nest,
     feedList,
     rawFeed = [];
     
     $.each(data, function(index) {
     
     nest = $("<li>", {class: "itemli"})
     .append(
     $("<div>", {class: "item_handle"})
     .append($("<i>", {class: "icon-paper-clip"})))
     .append($("<div>", {class: "item_content"})
     .append($("<input>", {class: "inputNinja", type: "text", value: this.name}))
     .append($("<a>", {class: "icon icon-trash"}))
     .append($("<a>", {class: "icon icon-pencil", id: "graphTypeButton"})
     .popover({
     html: true,
     title: 'Select graph',
     placement: 'left',
     content: function() {
     
     return $('#graphType').html();
     
     }
     })
     
     ))
     .append($("<ul>"))
     .data({id: this.name, graphType: 'bars', itemType: 'master'});
     
     $("#mightyNest").append(nest);
     
     
     $.each(this.values, function() {
     
     rawFeed.push(
     $("<li>", {class: "highlight itemli", title: "Original Data:" + this.name}).tooltip()
     
     .append(
     $("<div>", {class: "item_handle"})
     .append($("<i>", {class: "icon-circle"})))
     .append($("<div>", {class: "item_content"})
     .append($("<input>", {class: "inputNinja color", type: "text", value: this.name})))
     .data({id: this.name, originalText: this.name, itemType: 'slave'})
     
     );
     
     });
     
     feedList = $("<div>", {class: "box"})
     .append($("<div>", {class: "box-header"})
     .append($("<b>")
     .append($("<i>", {class: "icon-align-justify"}))
     .append($("<span>", {class: "break"}))
     .append(this.name))
     .append($("<div>", {class: "box-icon"})
     .append($("<a>", {class: "btn-minimize"})
     .append($("<i>", {class: "icon-chevron-down"})))))
     .append($("<div>", {class: "box-content clearfix", style: "display: none;"})
     .append($("<div>", {class: "", id: "list" + index})
     .append($("<ol>", {class: "simple_with_no_drop vertical", id: "nest" + index})
     .append(rawFeed))).append($("<div>", {class: "clearfix"})));
     
     $("#list").append(feedList);
     
     $('#nest' + index).sortable({group: 'no-drop', drop: false, handle: '.item_handle'});
     rawFeed = [];
     });
     
     });
     */
    $('#mightyNest').on("click", ".icon-trash", function() {

        $(this).parent().parent().tooltip('destroy').remove();
    });
    $('#preview').on('click', function() {

        document.location.href = "./index.html";

    });

    $('#mightyNest').on('change', '.inputNinja', function(e) {

        $(this).parent().parent().data().text = $(this).val();

    });

    $('#mightyNest').on('click', '#graphTypeButton', function(e) {

        $(this).parent().parent().data().graphType = $('#selectGraphType').find(':selected').data('graph');

    });
    function makeSideLists() {

        if (selectedCabi == '#selectcampaign') {

            $.post("../constructor.php", {action: 'getFeedBack', campId: $('#selectAgrupador').find(':selected').val()}, function(data) {

                selectFeeds(data, $('#selectAgrupador').find(':selected').val());

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

        $('#list').sortable({group: 'no-drop', drop: false, handle: '.item_handle'});

    }

    function selectFeeds(data) {

        var count = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);

        $.each(data, function() {
            var flag = false;

            if (this.human_answered === 'Y') {
                count[0] += 1;
                makeFeeds('respHumana', this, myAutoIncrement);
                flag = true;
                myAutoIncrement++;
            }

            if (this.sale === 'Y') {
                count[1] += 1;
                makeFeeds('sucesso', this, myAutoIncrement);
                flag = true;
                myAutoIncrement++;
            }

            if (this.dnc === 'Y') {
                count[2] += 1;
                makeFeeds('listaNegra', this, myAutoIncrement);
                flag = true;
                myAutoIncrement++;
            }

            if (this.customer_contact === 'Y') {
                count[3] += 1;
                makeFeeds('contUtil', this, myAutoIncrement);
                flag = true;
                myAutoIncrement++;
            }

            if (this.not_interested === 'Y') {
                count[4] += 1;
                makeFeeds('negativo', this, myAutoIncrement);
                flag = true;
                myAutoIncrement++;
            }

            if (this.unworkable === 'Y') {
                count[5] += 1;
                makeFeeds('naoUtil', this, myAutoIncrement);
                flag = true;
                myAutoIncrement++;
            }

            if (this.scheduled_callback === 'Y') {
                count[6] += 1;
                makeFeeds('callback', this, myAutoIncrement);
                flag = true;
                myAutoIncrement++;
            }

            if (this.completted === 'Y') {
                count[7] += 1;
                makeFeeds('contFechado', this, myAutoIncrement);
                flag = true;
                myAutoIncrement++;
            }

            if (!flag) {
                count[8] += 1;
                makeFeeds('feedSistema', this, myAutoIncrement);
                myAutoIncrement++;

            }

        });

        $.each(count, function(index) {

            $('#totalFeed' + index).text(this);
        });

    }

    function makeFeeds(sitio, isto, id) {

        $("#" + sitio).append($("<li>", {id: 'feed' + id, class: "highlight slave"})
                .append(
                        $("<div>", {class: "item_handle"})
                        .append($("<i>", {class: "icon-circle"})))
                .append($("<div>", {class: "item_content"})
                        .append($("<input>", {class: "inputNinja color", type: "text", value: isto.name, readonly: ''})))
                .data({id: isto.id, status: isto.id, originalText: isto.name, text: isto.name, itemType: 'slave', raw: isto, title: "Original:" + isto.name, propertyOf: $('#selectAgrupador').find(':selected').val(), dataType: 'calls'})

                );
    }
    ;

    $('.box-icon').on('click', '#addStapleIco', function(e) {

        if ($('#selectTemplateList option:selected').val() != 'default') {
            $("#mightyNest").append($("<li>", {class: "master"})
                    .append(
                            $("<div>", {class: "item_handle"})
                            .append($("<i>", {class: "icon-paper-clip"})))
                    .append($("<div>", {class: "item_content"})
                            .append($("<input>", {class: "inputNinja", type: "text", value: 'Staple'}))
                            .append($("<a>", {class: "icon icon-trash"}))
                            /*.append(
                             $("<a>", {class: "icon icon-pencil"})
                             
                             .popover({
                             html: true,
                             title: 'Select graph',
                             placement: 'left',
                             content: function() {
                             
                             return $('#graphType').html();
                             
                             }
                             })
                             )*/)
                    .append($("<ul>"))
                    .data({id: 'Staple' + myAutoIncrement, graphType: 'bars', itemType: 'master', text: 'Staple'})
                    );
            myAutoIncrement++;
        } else {
            $('#selectTemplateList').tooltip('show');
        }

    });

    //sortable lists For the fucking win
    $('#mightyNest').sortable({
        group: 'no-drop',
        handle: '.item_handle',
        onDragStart: function(item, container, _super) {

            // Duplicate items of the no drop area
            if (!container.options.drop) {
                item.clone(true).insertAfter(item);
                _super(item);

                $(item).find("div.item_content")
                        .append($("<a>", {class: "icon icon-trash color"}))
                        .end()
                        .find('input')
                        .removeAttr('readonly')
                        .end()
                        .removeClass('slave').addClass('master')
                        .attr('id', 'feed' + myAutoIncrement)
                        .data('id', 'feed' + myAutoIncrement)

                        .tooltip();

                myAutoIncrement++;

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

            if (item.data('itemType') === 'slave' && container.options.group === 'no-drop') {

                item.remove();

            }
        }
    });

    $("#editTemp").click(function() {

        if ($('#selectTemplateList option:selected').val() != 'default') {

            flagEdit = 'edit';
            $('#modalEditTemplate').modal('show');

            $('#modalEditTemplate').find('#saveTempModal').css('display', 'inline');
            $('#modalEditTemplate').find('#deleteTempModal').css('display', 'inline');
            $("#radio" + selectedCabi).prop('checked', true);


            $.post("../constructor.php", {action: 'getTemplate', templateId: $('#selectTemplateList').val()}, function(data) {
                var myCampaign;
                var myTemplate;

                $('#selectUser').val(data.users).trigger("chosen:updated");

                $('#inputName').val(data.name);

                $('#dateStart').val(data.start);

                $('#dateEnd').val(data.end);

                $('#disabledInput').val();

            }, 'json');
        } else {
            $('#selectTemplateList').tooltip('show');
        }
    });

    $("#saveTempModal").click(function(e) {
        e.preventDefault();

        $('#modalEditTemplate').find('#deleteTempModal').css('display', 'none');

        if ($('#modalNewTemplate').validationEngine("validate")) {

            if ($(selectedCabi + ' option:selected').length && $('#selectUser option:selected').length) {

                $('#modalEditTemplate').modal('hide');
                $("#radiocampaign").prop('disabled', true);
                $("#radiolist").prop('disabled', true);
                $("#radio" + selectedCabi).prop('checked', false);

                if (flagEdit === 'save') {
                    saveTemplate('saveTemplate');

                } else {
                    saveTemplate('editTemplate');

                }
            } else if ($('#selectUser option:selected').length == 0) {

                $('#chosenMultiDivUser').tooltip('show');

            } else if ($(selectedCabi + ' option:selected').length == 0) {

                $('#chosenSelectDiv' + selectedCabi.substring(7, 15)).tooltip('show');

            }
        }
    });

    $('#saveTemplateListIco').click(function() {

        if ($('#selectTemplateList option:selected').val() != 'default') {
            saveTemplate('editTemplate');
            
            $('#campTime').jGrowl("Hello world!");
            
        } else {

            $('#selectTemplateList').tooltip('show');
        }
    });

    function saveTemplate(action) {

        var dateRange = {},
                temp = {},
                serie = $('#mightyNest').sortable('serialize').get(),
                finalSerie = [];

        dateRange.start = moment($('#dateStart').val()).format('YYYY-MM-DD');
        dateRange.end = moment($('#dateEnd').val()).format('YYYY-MM-DD');

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

        $.post("../constructor.php",
                {
                    action: action,
                    users: $('#selectUser').val(),
                    name: $('#inputName').val(),
                    dateRange: dateRange,
                    type: $('input[name=cabi]:checked', '#modalNewTemplate').val(),
                    typeId: JSON.stringify($(selectedCabi).val()),
                    template: JSON.stringify(finalSerie),
                    templateId: $('#selectTemplateList').val()
                }, function(data) {

            if (flagEdit == 'save') {

                $("#selectTemplateList").append($('<option>', {value: data, text: $('#inputName').val()}));
                
                $("#selectTemplateList").val(data);

                $('#selectTemplateList').change();

            } else {
                var teste = $('#selectTemplateList option:selected').val();

                $("#selectTemplateList").append($('<option>', {value: $('#selectTemplateList option:selected').val(), text: $('#inputName').val()}));

                $('#selectTemplateList option:selected').remove();

                // $('#selectTemplateList option:contains("'+$('#selectTemplateList option:selected').val() +'")').prop('selected', true);

                $('#selectTemplateList').val(teste);

            }

        }, 'json');
    }

    $('#deleteTempModal').click(function() {
        var that = this;
        $('.master').remove();
        $('.slave').remove();
        $.post("../constructor.php", {action: 'deleteTemplate', templateId: $('#selectTemplateList').val()}, function(data) {

            if (data) {

                $('#selectTemplateList')
                        .find('option:selected')
                        .remove()
                        .end()
                        .trigger("chosen:updated");

            }

        }, 'json');

        $('#selectAgrupador').find('option').remove().end().trigger("chosen:updated");

    });

    $('#newTemplate').click(function() {

        $('.master').remove();
        $('.slave').remove();
        $('#modalEditTemplate').find('#deleteTempModal').css('display', 'none');
        $('#modalEditTemplate').modal('show');
        // $("#selectCampaign").prop("disabled", false).val('default').trigger("chosen:updated");
        $("#selectcampaign").val('default').trigger("chosen:updated");
        $("#selectUser").val('default').trigger("chosen:updated");
        $("#selectTemplateList").val('default').trigger("chosen:updated");
        $("#selectlist").val('default').trigger("chosen:updated");
        $('#inputName').val('');
        $('#dateStart').val('');
        $('#dateEnd').val('');
        $("#radiocampaign").prop('disabled', false);
        $("#radiolist").prop('disabled', false);
        $('#selectAgrupador').find('option').remove().end().trigger("chosen:updated");

        flagEdit = 'save';

    });

    $('#preViewIco').click(function() {

        if ($('#selectTemplateList option:selected').val() != 'default') {
            var url = "../html/index.html?templateId=" + $('#selectTemplateList option:selected').val();

            document.location.href = url;
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

    $('#modalClose1').click(function() {

        $("#radiocampaign").prop('disabled', true);
        $("#radiolist").prop('disabled', true);
        $("#radio" + selectedCabi.substring(7, 15)).prop('checked', false);
    });
    $('#modalClose2').click(function() {
        $('#modalClose1').click();
    });

});





