var ClientBox = function (configs) {
    var
        me = this,
        defaults = {
            target: "#clientBox",
            id: 0,
            byReserv: false
        },
        config = $.extend(defaults, configs),
        template = "<div class='grid'>\n\
                            <div class='grid-title' style='overflow: visible;'>\n\
                                <div class='pull-left'>Cliente</div>\n\
                                <div class='pull-right'>\n\
                                     <div class='dropdown' style='display:inline-block;'>\n\
                                        <span class='btn icon-alone dropdown-toggle' data-toggle='dropdown' >\n\
                                            <i class='icon-cog'></i>\n\
                                        </span>\n\
                                        <div class='dropdown-menu'>\n\
                                            <ul>\n\
                                                <li><a tabindex='-1' href='#' id='button_proposta_comercial' ><i class='icon-money'></i>Propostas comerciais</a></li>\n\
                                                <li><a tabindex='-1' href='#' id='button_open_pdf' ><i class='icon-user'></i>Abrir PDF</a></li>\n\
                                                <li><a tabindex='-1' href='#' id='button_edit_client_info' ><i class='icon-edit'></i>Editar Info. Cliente</a></li>\n\
                                                <li><a tabindex='-1' href='#' id='button_notes' ><i class='icon-file'></i>Notas</a></li>\n\
                                            </ul>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                            <div class='user c_bg-2'>\n\
                                <div class='user-name'  id='client_cod_camp'></div>\n\
                                <div class='user-email' id='client_ref_client'></div>\n\
                                <div class='user-email' id='client_compart'></div>\n\
                                <div class='user-email' id='client_name' style='height:auto;'></div>\n\
                                <div class='user-date'  id='client_address'></div>\n\
                                <div class='user-date'  id='client_postal'></div>\n\
                                <div class='user-date'  id='client_local'></div>\n\
                                <div class='user-date'  id='client_tel'></div>\n\
                                <div class='user-date'  id='client_tel1'></div>\n\
                                <div class='user-date'  id='client_tel2'></div>\n\
                                <div class='user-date'  id='client_birth_date'></div>\n\
                                <div class='user-date'  id='client_date'></div>\n\
                                <div class='user-date'  id='client_rsc'></div>\n\
                                <div class='user-date'  id='client_comments'></div>\n\
                                <div class='clear'></div>\n\
                            </div>\n\
                        </div>";


    this.client_info = {};

    this.init = function (callback) {
        var action = (config.byReserv) ? 'byReserv' : 'default';
        $.msg();
        $.post("/AM/ajax/client.php", {action: action, id: config.id}, function (clientI) {
            me.client_info = clientI;
            $(config.target)
                .append(template)
                .find("#client_name")
                .text(clientI.name)
                .end()
                .find("#client_address")
                .text(clientI.address)
                .end()
                .find("#client_postal")
                .text(clientI.postalCode)
                .end()
                .find("#client_local")
                .text(clientI.local)
                .end()
                .find("#client_ref_client")
                .text(clientI.refClient)
                .end()
                .find("#client_cod_camp")
                .text(clientI.codCamp)
                .end()
                .find("#client_compart")
                .text(clientI.compart)
                .end()
                .find("#client_tel")
                .text(clientI.phone)
                .end()
                .find("#client_tel1")
                .text(clientI.phone1)
                .end()
                .find("#client_tel2")
                .text(clientI.phone2)
                .end()
                .find("#client_birth_date")
                .text(clientI.bDay)
                .end()
                .find("#client_date")
                .text(function () {
                    return (clientI.date) ? moment(clientI.date).format('LLLL') : '';
                })
                .end()
                .find("#client_rsc")
                .text(clientI.rscName)
                .end()
                .find("#client_comments")
                .text(clientI.comments)
                .end().on("click", "#button_proposta_comercial", function (e) {
                    e.preventDefault();
                    me.getProposta();
                }).on("click", "#button_open_pdf", function (e) {
                    e.preventDefault();
                    me.getPdf();
                }).on("click", "#button_edit_client_info", function (e) {
                    e.preventDefault();
                    me.client_info_editing(function () {
                        me.refresh();
                    });

                }).on("click", "#button_notes", function (e) {
                    e.preventDefault();
                    me.notas();
                });
            if (typeof callback === 'function') {
                callback(clientI);
            }
            $.msg('unblock');
        }, "json").fail(function (data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    };

    this.initModal = function (lead_id, callback) {
        var client_info = $("<div>"), client_address = $("<div>"), client_extra = $("<div>");
        var client_extra_count = 0;
        var temp = "";
        $.msg();
        $.post("/AM/ajax/client.php", {
                action: "byLeadToInfo",
                id: lead_id
            },
            function (clientI) {
                me.client_info = clientI;
                me.client_info.id = lead_id;
                $.each(clientI, function () {
                    if (this.value) {
                        temp = ($("<div>", {class: "formRow"})
                            .append($("<span>").text(this.display_name + ":"))
                            .append($("<div>", {class: "right"})
                                .append($("<span>").text(this.value))));
                        switch (this.name.toLowerCase()) {
                            case "first_name":
                            case "middle_initial":
                            case "last_name":
                            case "phone_number":
                            case "alt_phone":
                            case "email":
                            case "date_of_birth":
                                client_info.append(temp);
                                break;
                            case "address1":
                            case "address2":
                            case "country_code":
                            case "province":
                            case "state":
                            case "city":
                            case "postal_code":
                            case "extra10":
                                client_address.append(temp);
                                break;
                            case "comments":
                            case "extra1":
                            case "extra2":
                            case "extra8":
                            case "extra5":
                                client_extra.append(temp);
                                client_extra_count++;
                                break;
                        }
                    }
                });


                var modal_html = ($("<div>", {class: " modal fade", role: "dialog"})
                    .append($("<div>", {class: "modal-dialog"})
                        .append($("<div>", {class: "modal-content"})
                            .append($("<div>", {class: "modal-header"})
                                .append($("<div>", {class: "modal-title"})
                                    .append($("<h4>").text("Informação de Cliente"))))
                            .append($("<div>", {class: "modal-body"})
                                .append($("<div>", {class: "row-fluid"})
                                    .append($("<div>", {class: "span6"})
                                        .append($("<h4>", {class: "icon-user"}).text(" Info de cliente")).append(client_info))
                                    .append($("<div>", {class: "span6  "})
                                        .append($("<h4>", {class: "icon-home"}).text(" Morada")).append(client_address)))
                                .append($("<div>", {class: "row-fluid"})
                                    .append($("<div>", {class: "span7 ", id: "extra_info_div"})
                                        .append($("<h4>", {class: "icon-star"}).text(" Info Extra")).append(client_extra)))))
                        .append($("<div>", {class: "modal-footer"})
                            .append($("<div>", {class: " btn-group"})
                                .append($("<button>", {class: "btn btn-success left dropdown-toggle"}).attr("data-toggle", "dropdown").text("Opções Extra").append($("<span>", {class: "caret"})))
                                .append($("<div>", {class: " dropdown-menu"})
                                    .append($("<ul>")
                                        .append($("<li>").append($("<a>", {id: "button_nova_marcacao"}).text("Nova Marcação").append($("<a>", {class: "icon-calendar"}))))
                                        .append($("<li>").append($("<a>", {id: "button_propostas_comerciais"}).text("Propostas comerciais").append($("<a>", {class: "icon-money"}))))
                                        .append($("<li>").append($("<a>", {id: "button_abrir_pdf"}).text("Abrir Pdf").append($("<a>", {class: "icon-user"}))))
                                        .append($("<li>").append($("<a>", {id: "button_notas"}).text("Notas").append($("<a>", {class: "icon-file"}))))
                                )
                            ))
                            .append($("<button>", {class: "btn "}).attr("data-dismiss", "modal").text("Fechar"))
                            .append($("<button>", {class: "btn btn-primary ", id: "button_editar_info_cliente"}).text("Editar Info. Cliente"))
                    )
                )).on("click", "#button_nova_marcacao", function () {
                        var en = btoa(lead_id);
                        $.history.push("view/calendar.html?id=" + en);
                        modal_html.modal("hide");
                    })
                    .on("click", "#button_editar_info_cliente", function () {
                        me.client_info_editing(function () {
                            me.destroy().initModal(lead_id);
                            $(".datables_updatable").each(function () {
                                $(this).dataTable().fnReloadAjax();
                            })
                        });
                        modal_html.modal("hide");
                    })
                    .on("click", "#button_propostas_comerciais", function () {
                        me.getProposta();
                    })
                    .
                    on("click", "#button_abrir_pdf", function () {
                        me.getPdf();
                    })
                    .
                    on("click", "#button_notas", function () {
                        me.notas();
                    });

                if (!client_extra_count)
                    modal_html.find("#extra_info_div").parent().remove();
                modal_html.modal("show");


                $.msg('unblock');
            }, "json").fail(function (data) {
                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                $.msg('unblock', 5000);
            });
        if (typeof callback === "function")
            callback();

    };

    this.destroy = function () {
        $(config.target).empty();
        return me;
    };

    this.refresh = function () {
        me.destroy().init();
        return me;
    };


    this.getPdf = function () {
        $.msg();
        $.post('/AM/ajax/upload_file.php', {
            action: "get_pdfs",
            navid: me.client_info.navId || "",
            ref_cliente: me.client_info.refClient
        }, function (data) {
            if (data) {
                var c = encodeURIComponent(data);
                document.location = '/AM/ajax/downloader.php?file=' + c;
            }
            else {
                $.jGrowl("Cliente sem ficheiro associado", 3000);
            }
            $.msg('unblock');
        }, "json").fail(function (data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    };

    this.getProposta = function () {
        $.msg();
        var propostas = "";
        var final = "<h5>Data de criação da proposta comercial</h5>";
        $.post('/AM/ajax/users.php', {action: "get_propostas", lead_id: me.client_info.id},
            function (data) {
                if (data.length) {
                    var menu = "";
                    $.each(data, function () {
                        $.each(this.proposta, function () {
                            propostas += " <tr><td>" + this.modelo + "</td><td>" + this.valor + "</td><td>" + this.quantidade + "</td><td>" + this.entrada + "</td><td>" + this.meses + "</td></tr>"
                        });
                        menu = "<div>\n\
                                        <div class='formRow'>\n\
                                            <label>" + this.data + "</label><button class='btn icon-alone right btnPropToggle' ><i class='icon-eye-open'></i></button>\n\
                                            <div class='clear'></div>\n\
                                            <div class='dTableProposta' style='display:none; margin-top:5px'>\n\
                                                <table class='table table-striped table-mod table-bordered'>\n\
                                                    <thead>\n\
                                                        <tr>\n\
                                                            <th>Modelo</th>\n\
                                                            <th>Valor</th>\n\
                                                            <th>Quantidade</th>\n\
                                                            <th>Entrada</th>\n\
                                                            <th>Meses</th>\n\
                                                        </tr>\n\
                                                    </thead>\n\
                                                    <tbody id='tbody_proposta_comercial'>" + propostas + "</tbody>\n\
                                                </table>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                    <div class='clear'></div>";
                        final += menu;
                        propostas = "";
                    });
                    bootbox.alert(final);
                    $(".btnPropToggle").click(function () {
                        $(this)
                            .find("i")
                            .toggleClass("icon-eye-open")
                            .toggleClass("icon-eye-close")
                            .end()
                            .parent()
                            .find(".dTableProposta")
                            .toggle();
                    });
                }
                else {
                    bootbox.alert("Cliente sem propostas comerciais.");
                }
                $.msg('unblock');
            }, "json").fail(function (data) {

                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                $.msg('unblock', 5000);
            });

    };

    this.notas = function () {
        var final = $("<div>", {class: "container-fluid"})
            .append("<div>", {class: "row-fluid"})
            .append($("<div>", {id: "new_note_area_div", class: "span7"}))
            .append($("<div>", {id: "note_area_div", class: "span7"}))
            .append($("<div>", {id: "note_selection_div", class: "span4"}));
        var note_area_div = final.find("#note_area_div"),
            new_note_area_div = final.find("#new_note_area_div");
        note_area_div.hide();

        //Create Table===============================================================
        final.find("#note_selection_div").append(
            "<table id='note_table' class='table table-mod-2 table-bordered'></table>");
        var table = final.find("#note_selection_div #note_table");
        table.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bLengthChange": false,
            "iDisplayLength": 5,
            "sAjaxSource": '/AM/ajax/users.php',
            "fnServerParams": function (aoData) {
                aoData.push({"name": "action", "value": "get_notes_to_datatable"}, {"name": "lead_id", "value": me.client_info.id});
            }, "aoColumns": [
                {"sTitle": "ID", bVisible: false},
                {"sTitle": "Titulo", "sWidth": "50px"},
                {"sTitle": "Nota", bVisible: false},
                {"sTitle": "Data criação", "sWidth": "50px"},
                {"sTitle": "Data modificação", "sWidth": "50px"}
            ], "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"},
            "aoColumnDefs": [
                {
                    "aTargets": [3, 4],
                    "fnRender": function (data, value, row) {
                        if (value.length) {
                            return moment(value).fromNow();
                        }
                        else {
                            return "N/A";
                        }
                    }
                }
            ]
        }).on('click', 'tr', function (event) {
            var data = table.fnGetData(table.fnGetPosition(this));
            note_area_div.find("#note_title").val(data[1]).data("selected_note", data[0]).end()
                .find("#note_textarea").val(data[2]);
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                $(".edit_buttons_class").addClass("hidden");
                clear_note_preview();
                note_area_div.hide();
                new_note_area_div.show();
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                $(".edit_buttons_class").removeClass("hidden");
                note_area_div.show();
                new_note_area_div.hide();
            }
        });
        //==========================================================================
        //preview area
        note_area_div.append(
            "<div id='preview_note_div'>\
                <div class='page-header'>\
                    <h3 id='page_header'>Notas de Cliente</h3>\
                </div>\
                    <form id='note_form'>\
                        <label>Titulo</label>\
                            <input class='validate[required] input-xlarge' id='note_title' maxlength='45'  type='text' placeholder='Titulo da Nota'> \
                        <label>Nota</label>\
                            <textarea class='validate[required]' id='note_textarea' placeholder='Visualização de notas' style='width: 100%;height: 160px'></textarea>\
                                <button class='btn btn-success  'id='save_note_edit'>Gravar alterações</button>\
                                <button class='btn' id='cancel_note_edit'>Cancelar</button>\
                                <button class='btn btn-danger  ' id='delete_note_edit'>Apagar Nota</button>\
                     </form> \
                    <div>\
                </div>\
            </div>");

        //new note area
        new_note_area_div.append(
            "<div id='new_note_div'>\
                <div class='page-header'>\
                    <h3 id='page_header'>Criação de notas de cliente</h3>\
                </div>\
                <form id='new_note_form'>\
                     <label>Titulo</label>\
                        <input class='validate[required] input-xlarge' id='new_note_title' maxlength='45' type='text' placeholder='Escreva aqui o titulo da nota'> \
                     <label>Nota</label>\
                         <textarea class='validate[required]' id='new_note_textarea' placeholder='Texto da nota' style='width: 100%;height: 160px'></textarea>\
                              <button class='btn btn-success  'id='save_note_new'>Criar Nota</button>\
                              <button class='btn btn-danger  ' id='cancel_note_new'>Limpar</button>\
                </form>\
                <div>\
                </div>\
            </div>");

        //PREVIEW AND EDIT NOTE----------------------------------------------------------------------------------
        note_area_div.on("click", "#save_note_edit", function (e) {
            e.preventDefault();
            if (final.find("#note_form").validationEngine('validate')) {
                $.msg();
                $.post('/AM/ajax/users.php', {
                        action: "edit_notes",
                        note_id: note_area_div.find("#note_title").data("selected_note"),
                        note: note_area_div.find("#note_textarea").val(),
                        title: note_area_div.find("#note_title").val()
                    },
                    function (data) {
                        $.jGrowl("Nota editada com sucesso", 3000);
                        table.fnReloadAjax();
                        clear_note_preview();
                        $.msg('unblock');
                    }, "json"
                ).fail(function (data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
            }
        });
        note_area_div.on("click", "#cancel_note_edit", function (e) {
            e.preventDefault();
            clear_note_preview();
        });
        note_area_div.on("click", "#delete_note_edit", function (e) {
            e.preventDefault();
            bootbox.confirm("Tem a certeza que quer remover esta nota?", function (result) {
                if (result) {
                    $.msg();
                    $.post('/AM/ajax/users.php', {action: "delete_notes", note_id: note_area_div.find("#note_title").data("selected_note")},
                        function (data) {
                            $.jGrowl("Nota removida com sucesso", 3000);
                            table.fnReloadAjax();
                            clear_note_preview();
                            $.msg('unblock');
                        }, "json").fail(function (data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                }
            })
        });

        function clear_note_preview() {
            note_area_div.find("#note_title").val("");
            note_area_div.find("#note_textarea").val("");
            toggle_menus();
            table.$('tr.selected').removeClass('selected');
        }

        //-----------------------------------------------------------------------------------------------------------
        //NEW NOTE//////////////////////////////////////////////////////////////////////////////////////////////////////////
        new_note_area_div.on("click", "#save_note_new", function (e) {
            e.preventDefault();
            if (new_note_area_div.find("#new_note_form").validationEngine('validate')) {
                $.msg();
                $.post('/AM/ajax/users.php', {
                        action: "insert_notes",
                        lead_id: me.client_info.id,
                        note: new_note_area_div.find("#new_note_textarea").val(),
                        title: new_note_area_div.find("#new_note_title").val()
                    },
                    function (data) {
                        $.jGrowl("Nota criada com sucesso", 3000);
                        table.fnReloadAjax();
                        clear_note_new();
                        $.msg('unblock');
                    }, "json").fail(function (data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
            }
        });

        new_note_area_div.on("click", "#cancel_note_new", function (e) {
            e.preventDefault();
            clear_note_new();
        });

        function clear_note_new() {
            new_note_area_div.find("#new_note_title").val("").end()
                .find("#new_note_textarea").val("");
        }


        function toggle_menus() {
            note_area_div.toggle();
            new_note_area_div.toggle();
        }

        bootbox.dialog(final, [
            {'OK': true, "label": "OK"}
        ], {customClass: 'container'});

    };

    this.get_info = function () {
        return me.client_info;
    };

    this.client_info_editing = function (callback) {
        $.post("ajax/create_client.php", {action: "get_fields"},
            function (data) {
                $("#inputs_div1,#inputs_div2,#inputs_div3").empty();
                var input,
                    custom_class = "",
                    div = $("<div>", {id: "master", class: "row-fluid"})
                        .append($("<div>", {class: "span4  grid-transparent"}).append($("<div>", {class: "grid-title"}).append($("<div>", {class: "pull-left"}).text("Dados Pessoais")).append($("<div>", {class: "clear"}))).append($("<div>", {
                            id: "input1",
                            class: "grid-content"
                        })))
                        .append($("<div>", {class: "span4  grid-transparent"}).append($("<div>", {class: "grid-title"}).append($("<div>", {class: "pull-left"}).text("Local/Morada")).append($("<div>", {class: "clear"}))).append($("<div>", {
                            id: "input2",
                            class: "grid-content"
                        })))
                        .append($("<div>", {class: "span4  grid-transparent"}).append($("<div>", {class: "grid-title"}).append($("<div>", {class: "pull-left"}).text("Extras")).append($("<div>", {class: "clear"}))).append($("<div>", {
                            id: "input3",
                            class: "grid-content"
                        }))),
                    elmt,
                    specialE,
                    hide = "";
                $.each(data, function () {
                    if (this.name === "extra5") {
                        custom_class = "span ";
                        elmt = $("<select>", {id: this.name, name: this.name});
                        var optionsRaw = ["", "ADM (ADME/ADMA/ADMFA)", "ADSE", "APL", "CGD", "Centro Nac. de Protecção Contra Riscos Profissionais", "EDP", "PETROGAL", "PT/CTT ACS", "SAD-PSP", "SAD/GNR (ADMG)", "SAMS", "SEG. SOCIAL", "Serviços Sociais do Ministério da Justiça", "OUTRAS"];
                        options = optionsRaw.map(function (v) {
                            return new Option(v, v);
                        });
                        magia = function () {
                            if ($(this).val() === "OUTRAS") {
                                $(this).replaceWith(
                                    $('<div>', {class: 'input-append'})
                                        .append(
                                        $("<input>", {type: "text", id: $(this).prop("id"), name: $(this).prop("name")})
                                    )
                                        .append(
                                        $('<btn>', {class: 'btn icon-alone'})
                                            .append(
                                            $('<i>', {class: 'icon-undo'})
                                        )
                                            .click(function () {
                                                $(this).parent().replaceWith(specialE.val("").change(magia));
                                            })
                                    )
                                );
                            }
                        };
                        elmt
                            .append(options)
                            .change(magia);
                        specialE = elmt;
                    } else if (this.name === "TITLE") {
                        elmt = $("<select>", {
                            id: this.name,
                            name: this.name,
                            class: "input-mini"
                        }).attr('data-prompt-position', 'topRight:120').append([new Option("", ""), new Option("Sr.", "Sr."), new Option("Sra. D.", "Sra. D.")]);
                    } else if (this.name === "extra6") {
                        elmt = $("<input>", {type: "text", readonly: true, id: this.name, name: this.name, value: "NO"});
                    } else if (this.name === "SECURITY_PHRASE") {
                        elmt = $("<input>", {type: "text", readonly: true, id: this.name, name: this.name, value: "SPICE"});
                    } else if (this.name === "POSTAL_CODE") {
                        elmt = $("<input>", {type: "text", id: this.name, name: this.name}).change(function () {
                            if ((this.value.length)) {
                                $.post("ajax/client.php", {action: "check_postal_code", postal_code: this.value}, function (data1) {
                                    var postal_codes = "";
                                    $.each(data1, function () {
                                        postal_codes += "<tr>\n\
                                 <td>" + this.rua + "</td>\n\
                                 <td>" + this.zona + "</td>\n\
                                 <td>" + this.localidade + "</td>\n\
                                 <td>" + this.concelho + "</td>\n\
                                 <td>" + this.distrito + "</td>\n\
                                 <td>" + this.cod_postal + "<div class='view-button'><button class='btn btn-mini postal_code_populate' data-mor='" + JSON.stringify(this) + "'><i class='icon-copy'></i> Copiar</button></div></td>\n\
                                 </tr>";
                                    });
                                    bootbox.dialog("<div class='alert alert-warning'>Foi encontrado um/varios codigos postais semelhantes.</div>\n\
                                        <table id='postal_code_table_check' class='table table-mod table-bordered table-striped table-condensed'>\n\
                                            <thead>\n\
                                                <tr>\n\
                                                    <td>Rua</td>\n\
                                                    <td>Zona</td>\n\
                                                    <td>Localidade</td>\n\
                                                   <td>Concelho</td>\n\
                                                    <td>Distrito</td>\n\
                                                    <td>Codigo Postal</td>\n\
                                                    </tr>\n\
                                            </thead>\n\
                                            <tbody>\n\
                                            " + postal_codes + "\n\
                                            </tbody>\n\
                                        </table><div class='clear'></div>", [
                                        {'OK': true, "label": "OK"}
                                    ], {customClass: 'container'});
                                    $("#postal_code_table_check").on("click", ".postal_code_populate", function (e) {
                                        e.preventDefault();
                                        var that = $(this).data().mor;
                                        $("[name='ADDRESS1']").val(that.rua);
                                        $("[name='POSTAL_CODE']").val(that.cod_postal);
                                        $("[name='CITY']").val(that.localidade);
                                        $("[name='PROVINCE']").val(that.concelho);
                                        $("[name='STATE']").val(that.distrito);
                                        bootbox.hideAll();
                                    }).DataTable();
                                }, "json");
                            }
                        });
                    } else if (this.name === "COMMENTS") {
                        elmt = $("<textarea>", {id: this.name, name: this.name});
                    } else {
                        elmt = $("<input>", {type: "text", id: this.name, name: this.name});
                    }
                    if (this.name === "PHONE_NUMBER" || this.name === "extra2" || this.name === "extra8") {
                        elmt.change(function () {
                            if (this.value.length < 9 && (this.name === "PHONE_NUMBER" || this.name === "extra8"))
                                return false;
                            $.post("ajax/client.php", {action: "byWhat", what: this.name, value: this.value}, function (clients) {
                                if (!clients.length)
                                    return false;
                                var trs = "";
                                $.each(clients, function () {
                                    trs += "<tr>\n\
                                        <td>" + this.refClient + "</td>\n\
                                        <td>" + this.nif + "</td>\n\
                                        <td>" + this.name + "</td>\n\
                                        <td>" + this.address1 + "</td>\n\
                                        <td>" + this.postal_code + "</td>\n\
                                        <td>" + this.city + "</td>\n\
                                        <td>" + this.phone + "</td>\n\
                                        <td>" + this.date_of_birth + "\n\
                                            <div class='view-button'>\n\
                                                <button class='btn btn-mini icon-alone ver_cliente' data-lead_id='" + this.id + "' title='Ver Cliente'><i class='icon-edit'></i></button>\n\
                                                <button class = 'btn btn-mini icon-alone criar_encomenda' data-lead_id ='" + this.id + "' title='Nova Encomenda'> <i class='icon-shopping-cart'></i></button>\n\
                                                <button class = 'btn btn-mini icon-alone criar_marcacao' data-lead_id ='" + this.id + "' title='Marcar Consulta'> <i class='icon-calendar'></i></button>\n\
                                            </div>\n\
                                        </td>\n\
                                   </tr>";
                                });
                                bootbox.dialog("<div class='alert alert-warning'>Foi encontrado um cliente com estes dados.</div>\n\
                                        <table class='table table-mod table-bordered table-striped table-condensed'>\n\
                                            <thead>\n\
                                                <tr>\n\
                                                    <td>Ref. Cliente</td>\n\
                                                    <td>Nif</td>\n\
                                                    <td>Nome</td>\n\
                                                    <td>Morada</td>\n\
                                                    <td>Cod. Postal</td>\n\
                                                    <td>Localidade</td>\n\
                                                    <td>Telefone</td>\n\
                                                    <td style='width:170px';>Data de Nasc.</td>\n\
                                               </tr>\n\
                                            </thead>\n\
                                            <tbody>\n\
                                            " + trs + "\n\
                                            </tbody>\n\
                                   </table>", [
                                    {'OK': true, "label": "OK"}
                                ], {customClass: 'container'}).on("click", ".criar_marcacao", function () {
                                    bootbox.hideAll();
                                    var en = btoa($(this).data().lead_id);
                                    $.history.push("view/calendar.html?id=" + en);
                                }).on("click", ".criar_encomenda", function () {
                                    bootbox.hideAll();
                                    var
                                        data = $(this).data(),
                                        en = btoa(data.lead_id);
                                    $.history.push("view/new_requisition.html?id=" + en);
                                }).on("click", ".ver_cliente", function () {
                                    var client = new Cliente_info($(this).data().lead_id, null);
                                    client.init(null);
                                });
                            }, "json");
                        });
                    }
                    switch (this.name) {
                        case "PHONE_NUMBER":
                            custom_class = "validate[required,custom[onlyNumberSp],minSize[9]]";
                            input = div.find("#input1");
                            break;
                        case "ADDRESS3":
                        case "ALT_PHONE":
                            custom_class = "validate[custom[onlyNumberSp]]";
                            input = div.find("#input1");
                            break;
                        case "FIRST_NAME":
                            custom_class = "validate[required] span";
                            input = div.find("#input1");
                            break;
                        case "DATE_OF_BIRTH":
                            custom_class = "form_datetime input-small validate[required]";
                            input = div.find("#input1");
                            break;
                        case "EMAIL":
                            input = div.find("#input1");
                            custom_class = "validate[custom[email]] span";
                            break;
                        case "TITLE":
                            custom_class = "validate[required]";
                            input = div.find("#input1");
                            break;
                        case "extra8":
                            custom_class = "span";
                            input = div.find("#input1");
                            break;
                        case "extra2":
                            custom_class = "validate[custom[spice_ref_cliente]]";
                            input = div.find("#input1");
                            break;
                        case "LAST_NAME":
                        case "MIDDLE_INITIAL":
                            custom_class = "span";
                            input = div.find("#input1");
                            break;
                        case "ADDRESS1":
                        case "CITY":
                            custom_class = "validate[required] span";
                            input = div.find("#input2");
                            break;
                        case "POSTAL_CODE":
                            custom_class = "validate[required]";
                            input = div.find("#input2");
                            break;
                        case "ADDRESS2":
                        case "PROVINCE":
                        case "STATE":
                        case "COUNTRY_CODE":
                        case "extra3":
                        case "extra4":
                        case "extra10":
                            custom_class = "span";
                            input = div.find("#input2");
                            break;
                        case "extra6":
                        case "extra7":
                        case "SECURITY_PHRASE":
                            custom_class = "span";
                            hide = " hide";
                            input = div.find("#input3");
                            break;
                        case "extra1":
                            custom_class = "span";
                            custom_class = "validate[required]";
                            input = div.find("#input3");
                            break;
                        default:
                            hide = "";
                            input = div.find("#input3");
                            break;
                    }
                    elmt.addClass(custom_class);
                    input.append($("<div>", {class: "control-group" + hide})
                        .append($("<label>", {class: "control-label"}).text(this.display_name))
                        .append($("<div>", {class: "controls"})
                            .append(elmt)));
                    custom_class = "";
                });
                $.post("ajax/client.php", {action: "byLeadToInfo", id: me.client_info.id}, function (data) {
                    bootbox.dialog(div, [{
                        "label": "Gravar Alterações",
                        "class": "btn-success",
                        "callback": function () {
                            $.post("ajax/client.php", {
                                action: "edit_info", id: me.client_info.id, stringas: function () {
                                    var strings = [];
                                    $.each($("#master :input"), function () {
                                        strings.push({key: $(this).prop("name"), value: $(this).val()});
                                    })
                                    return JSON.stringify(strings);
                                }
                            }, function () {
                                $.jGrowl("Info de cliente editada com sucesso!", 3000)
                                if (typeof callback === "function")
                                    callback();
                            });
                        }
                    },
                        {
                            "label": "Cancelar",
                            "class": "btn",
                            "callback": function () {
                                if (typeof callback === "function")
                                    callback();
                            }
                        }
                    ], {customClass: 'container'});
                    data = JSON.parse(data)
                    $("#PHONE_NUMBER").autotab('numeric');
                    $(".form_datetime").datetimepicker({
                        format: 'dd-mm-yyyy',
                        autoclose: true,
                        language: "pt",
                        minView: 2
                    }).attr('data-prompt-position', 'topRight:120');
                    $.each(data, function () {
                        div.find("#" + this.name).val(this.value);
                    })
                });
                $.msg('unblock');
                return true;
            },
            "json").fail(function (data) {
                $.msg('replace', 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.');
                $.msg('unblock', 5000);
            });

    }
};



