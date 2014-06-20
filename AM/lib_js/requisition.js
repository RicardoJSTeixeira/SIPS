var requisition = function(geral_path, options_ext) {

    var me = this;
    this.file_uploaded = false;
    this.config = {};
    this.tipo = "mensal";
    var
            me = this,
            product_tree,
            modal = "",
            modal_anexo = "",
            table_path = "",
            produtos = [],
            EData;
    $.extend(true, this.config, options_ext);
    this.init = function(callback) {
        $.get("/AM/view/requisitions/requisition.html", function(data) {
            geral_path.append(data);
            geral_path.find("#new_requisition_div").hide();
            modal = geral_path.find("#ver_product_requisition_modal");
            modal_anexo = geral_path.find("#ver_anexo_requisition_modal");
            if (typeof callback === "function")
                callback();
        });
    };
    this.get_current_requisitions = function(table_path1) {
        table_path = table_path1;
        get_encomendas_atuais(table_path);
    };
    //NEW REQUISITION------------------------------------------------------------------------------------------------------------------------------------------------
    this.new_requisition = function(new_requisition_zone, lead_id) {
        var must_have_anexo = false;
        $("#product_selector").chosen({
            no_results_text: "Sem resultados"
        });
        if (lead_id) {
            me.tipo = "especial";
            new_requisition_zone.find("#tipo_especial").show();
            var client_box;
            client_box = new clientBox({
                id: lead_id,
                byReserv: false
            });
            client_box.init();
        } else {
            me.tipo = "mensal";
            new_requisition_zone.find("#tipo_especial").hide();
        }
        $.msg();
        $.post('/AM/ajax/products.php', {
            action: "get_produtos"
        },
        function(data) {
            var option = "<option value='0'>Escolha um produto</option>";
            $("#product_selector").append(option);
            var temp = "<optgroup value='1' label='BTE'></optgroup>\n\
                        <optgroup value='2' label='RITE'></optgroup>\n\
                        <optgroup value='3' label='INTRA'></optgroup>\n\
                        <optgroup value='4' label='Pilhas'></optgroup>\n\
                        <optgroup value='5' label='Acessórios'></optgroup>\n\
                        <optgroup value='6' label='Moldes'></optgroup>\n\
                        <optgroup value='7' label='Economato'></optgroup>\n\
                        <optgroup value='8' label='Gama'></optgroup>",
                    BTE = [],
                    RITE = [],
                    INTRA = [],
                    pilha = [],
                    acessorio = [],
                    molde = [],
                    economato = [],
                    gama = [];
            $("#product_selector").append(temp);

            $.each(data, function() {
                produtos[this.id] = (this);
                if (me.tipo === "especial") {
                    if (this.max_req_s < 1)
                        return true;
                } else {
                    if (this.max_req_m < 1)
                        return true;
                }
                option = "<option value='" + this.id + "'>" + this.name + "</option>";
                switch (this.category) {
                    case "BTE":
                        BTE.push(option);
                        break;
                    case "RITE":
                        RITE.push(option);
                        break;
                    case "INTRA":
                        INTRA.push(option);
                        break;
                    case "Pilha":
                        pilha.push(option);
                        break;
                    case "Acessório":
                        acessorio.push(option);
                        break;
                    case "Molde":
                        molde.push(option);
                        break;
                    case "Economato":
                        economato.push(option);
                    case "Gama":
                        gama.push(option);
                        break;
                }
            });
            $("#product_selector").find("optgroup[value='1']").append(BTE).end()
                    .find("optgroup[value='2']").append(RITE).end()
                    .find("optgroup[value='3']").append(INTRA).end()
                    .find("optgroup[value='4']").append(pilha).end()
                    .find("optgroup[value='5']").append(acessorio).end()
                    .find("optgroup[value='6']").append(molde).end()
                    .find("optgroup[value='7']").append(economato).end()
                    .find("optgroup[value='8']").append(gama).end().trigger("chosen:updated");
            $.msg('unblock');
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
        new_requisition_zone.off();
        new_requisition_zone.find('.fileupload').fileupload().end()
                .find("#form_encomenda_especial").validationEngine().end()
                .find("#form_encomenda_especial").show().end()
                .find(".tipo_div").hide();
        new_requisition_zone.find("#save_single_product").prop("disabled", true);
        $(new_requisition_zone).on("change", "#product_selector", function() {
            if (product_tree)
                product_tree.destroy();
            if (~~$(this).val() === 0) {
                new_requisition_zone.find("#save_single_product").prop("disabled", true);
                return false;
            } else {
                new_requisition_zone.find("#save_single_product").prop("disabled", false);
            }
            product_tree = new tree("#tree", [produtos[$(this).val()]], me.tipo, $(this).val(), produtos);
            product_tree.init();
        });
        //----------------------------------------------------------------------------------------------------------------------------------------
        //-------------------------------------------------------------------------------------------SAVE SINGLE PRODUCT--------------------------
        $(new_requisition_zone).on("click", '#save_single_product', function(e) {
            var produtos_single = [],
                    has_products = 0;
            $.each($("#tree").find(".product_item"), function() {
                if ($(this).find("input[type=checkbox]").is(":checked")) {
                    if ($(this).prop("category_product") === "Molde" || $(this).prop("category_product") === "INTRA")
                        must_have_anexo = true;
                    has_products++;
                    produtos_single.push({
                        id: $(this).prop("id_product"),
                        name: $(this).prop("name_product"),
                        category: $(this).prop("category_product"),
                        quantity: ~~$(this).find(".input_quantity").val(),
                        color_id: $(this).find(".color_select").val(),
                        color_name: $(this).find(".color_select option:selected").text()
                    });
                    if (me.tipo === "especial") {
                        produtos[$(this).prop("id_product")].max_req_s -= ~~$(this).find(".input_quantity").val();
                        if (produtos[$(this).prop("id_product")].max_req_s < 1)
                            new_requisition_zone.find("#product_selector").find("option[value='" + $(this).prop("id_product") + "']").prop("disabled", true).trigger("chosen:updated");
                    } else {
                        produtos[$(this).prop("id_product")].max_req_m -= ~~$(this).find(".input_quantity").val();
                        if (produtos[$(this).prop("id_product")].max_req_m < 1)
                            new_requisition_zone.find("#product_selector").find("option[value='" + $(this).prop("id_product") + "']").prop("disabled", true).trigger("chosen:updated");
                    }
                }
            });
            if (!has_products) {
                $.jGrowl('Selecione pelo menos 1 produto', {
                    life: 4000
                });
                return false;
            }
            new_requisition_zone.find("#product_selector").val(0).trigger("change");
            var new_product = "";
            new_product = ($("<div>", {
                class: "grid"
            }).append($("<div>")));
            var produtos_in_text = new_product
                    .find("div:last")
                    .append($("<table>", {
                        class: "table table-mod table-striped table-condensed"
                    })
                            .append($("<thead>")
                                    .append($("<tr>")
                                            .append($("<th>").text("Nome").css("width", "420px"))
                                            .append($("<th>").text("Categoria").css("width", "120px"))
                                            .append($("<th>").text("#").attr("title", "Quantidade").css("width", "50px"))
                                            .append($("<th>").text("Cor").css("width", "150px").append($("<button>", {
                                                class: "btn btn-danger btn-mini icon-alone right remove_produto_encomendado"
                                            }).append($("<i>", {
                                                class: "icon icon-trash"
                                            }))))
                                            ))
                            .append($("<tbody>")))
                    .find("tbody");
            $.each(produtos_single, function() {
                produtos_in_text.append($("<tr>", {
                    class: "product_line"
                })
                        .append($("<td>", {
                            class: "td_name",
                            id_product: this.id
                        }).text(this.name))
                        .append($("<td>", {
                            class: "td_category"
                        }).text(this.category.capitalize()))
                        .append($("<td>", {
                            class: "td_quantity"
                        }).text(this.quantity))
                        .append($("<td>", {
                            class: "td_color",
                            color: this.color_id
                        }).text(this.color_name))
                        );
            });
            new_requisition_zone.find("#produtos_encomendados").append(new_product);
            new_requisition_zone.find("#product_selector").val(0).trigger("chosen:updated");
        });
        //-------------------------------------------------------------------------------------------------------------------------------------------------------------
        //---------------------------------------------------------------------------------------------------------- SUBMITAR A ENCOMENDA------------------------------
        $(new_requisition_zone).on("click", "#new_requisition_submit_button", function() {
            var upload_complete = 1;
            $.each(me.config.uploader.files, function() {
                if (this.percent !== 100) {
                    upload_complete = 0;
                    $.jGrowl('Certifique-se de que os ficheiros de anexo foram carregados para o servidor', {
                        life: 4000
                    });
                    return false;
                }
            });
            if (upload_complete) {
                var anexo_random_number = $(this).data().anexo_random_number;
                var produtos_encomenda = [];
                var count = 0;
                $.each(new_requisition_zone.find(" #produtos_encomendados tr"), function() {
                    if ($(this).hasClass("product_line")) {
                        count++;
                        produtos_encomenda.push({
                            id: $(this).find(".td_name").attr("id_product"),
                            quantity: ~~$(this).find(".td_quantity").text(),
                            color: $(this).find(".td_color").attr("color")
                        });
                    }
                });
                if (!count)
                    $.jGrowl('Escolha pelo menos 1 produto', {
                        life: 4000
                    });
                else {
                    if (new_requisition_zone.find(" #form_encomenda_especial").validationEngine("validate")) {
                        if (must_have_anexo) {
                            if (!me.config.w.files.length) {
                                bootbox.alert("Certifique-se de que envia pelo menos um anexo pelos produtos Molde/Intra");
                                return false;
                            } else
                                must_have_anexo = false;
                        }
                        $("#form_encomenda_especial :input").attr('readonly', true);
                        $("#new_requisition_submit_button").prop("disabled", true);
                        $.msg();
                        $.post('ajax/requisition.php', {
                            action: "criar_encomenda",
                            type: me.tipo,
                            lead_id: lead_id,
                            contract_number: new_requisition_zone.find("#new_requisition_contract").val(),
                            attachment: me.config.uploader.files.length,
                            products_list: produtos_encomenda
                        },
                        function(data) {
                            $("#form_encomenda_especial :input").attr('readonly', false);
                            $("#new_requisition_submit_button").prop("disabled", false);
                            $.jGrowl('Encomenda realizada com sucesso', {
                                life: 4000
                            });
                                                     $.post('/AM/ajax/upload_file.php', {
                                action: "move_files_to_new_folder",
                                old_id: anexo_random_number,
                                new_id: data[0]
                            },
                            function(data) {
                                $.history.push("view/admin/pedidos.html?enc=0");
                                $.msg('unblock');
                            }, "json").fail(function(data) {
                                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                $.msg('unblock', 5000);
                            });
                        }, "json").fail(function(data) {
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                }
            }
        });

        $(new_requisition_zone).on("click", " .remove_produto_encomendado", function(e) {
            var this_button = $(this);
            bootbox.confirm("Tem a certeza que pretende remover esta encomenda? Certifique-se que não tem produtos selecionados na zona da Hierarquia", function(result) {
                if (result) {
                    $.each(this_button.closest(".grid").find("tr"), function() {
                        if ($(this).hasClass("product_line")) {
                            if (me.tipo === "especial")
                                produtos[$(this).find(".td_name").attr("id_product")].max_req_s = produtos[$(this).find(".td_name").attr("id_product")].max_req_s + ~~$(this).find(".td_quantity").text();
                            else
                                produtos[$(this).find(".td_name").attr("id_product")].max_req_m = produtos[$(this).find(".td_name").attr("id_product")].max_req_m + ~~$(this).find(".td_quantity").text();
                            new_requisition_zone.find("#product_selector").find("option[value='" + $(this).find(".td_name").attr("id_product") + "']").prop("disabled", false).trigger("chosen:updated");
                        }
                    });
                    new_requisition_zone.find("#product_selector").val(0).trigger("chosen:updated").trigger("change");
                    this_button.closest(".grid").remove();
                    //verificar se ainda ficou algum molde ou intra na encomenda (obrigatoriadade dos anexos)
                    must_have_anexo = false;
                    $.each($("#produtos_encomendados .product_line").find(".td_category"), function() {
                        if ($(this).text() === "INTRA" || $(this).text() === "Molde") {
                            must_have_anexo = true;
                            return false;
                        }
                    });
                }
            });
        });
        $(new_requisition_zone).on("submit", " #form_encomenda_especial", function(e) {
            e.preventDefault();
        });
    };

    function get_encomendas_atuais(table_path) {
        var Table_view_requisition = table_path.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/requisition.php',
            "fnServerParams": function(aoData) {
                aoData.push({
                    "name": "action",
                    "value": "listar_requisition_to_datatable"
                });
            },
            "fnDrawCallback": function() {
                $.each(Table_view_requisition.find(".cod_cliente_input"), function() {
                    if (!$(this).val())
                        $(this).parent().parent().addClass("error");
                });
            },
            "aoColumns": [{
                    "sTitle": "Id"
                }, {
                    "sTitle": "Dispenser",
                    "bVisible": SpiceU.user_level > 5
                }, {
                    "sTitle": "Tipo"
                }, {
                    "sTitle": "ClientID",
                    "sType": 'numeric'
                }, {
                    "sTitle": "Data"
                }, {
                    "sTitle": "Nº de contrato"
                }, {
                    "sTitle": "Ref. Cliente"
                }, {
                    "sTitle": "Anexo"
                }, {
                    "sTitle": "Produtos"
                }, {
                    "sTitle": "Estado"
                }, {
                    "sTitle": "Opções",
                    "sWidth": "60px",
                    "bVisible": SpiceU.user_level > 5
                }],
            "oLanguage": {
                "sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"
            }
        });
        if (SpiceU.user_level > 5) {
            Table_view_requisition.on("change", ".cod_cliente_input", function() {
                var that = $(this);
                if (!$(this).validationEngine("validate")) {
                    if (that.val().length)
                        bootbox.confirm("Tem a certeza que pretende actualizar para o codigo:" + that.val() + "?", function(result) {
                            if (result) {
                                $.msg();
                                $.post('/AM/ajax/requisition.php', {
                                    action: "editar_encomenda",
                                    "clientID": that.data().clientid,
                                    "cod_cliente": that.val()
                                }, function(data) {
                                    that
                                            .closest("tr")
                                            .removeClass("error")
                                            .end()
                                            .replaceWith(that.val());
                                    Table_view_requisition.fnReloadAjax();
                                    $.msg('unblock');
                                }, "json").fail(function(data) {
                                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                                    $.msg('unblock', 5000);
                                });
                            }
                        });
                }
            });
        }

        $('#export_ENC').click(function(event) {
            event.preventDefault();
            table2csv(Table_view_requisition, 'full', '#' + table_path[0].id);
        });
        table_path.on("click", ".ver_cliente", function() {
            var client = new cliente_info($(this).data("lead_id"), null);
            client.init(null);
        });
        //VER PRODUTOS DE ENCOMENDAS FEITAS
        table_path.on("click", ".ver_requisition_products", function() {
            var that = this;
            $.msg();
            $.post('ajax/requisition.php', {
                action: "listar_produtos_por_encomenda",
                id: $(this).val()
            }, function(data) {
                var
                        modal_tbody = modal.find("#show_requisition_products_tbody").empty(),
                        EInfotmp = [],
                        EInfo = [];
                EData = {
                    bInfo: [],
                    products: []
                };
                $(that).parents("tr").find('td:not(:eq(0)):not(:eq(6)):not(:eq(6)):not(:eq(7))').each(function(i) {
                    EInfotmp.push(this.innerText);
                });
                EInfo = {
                    'Dispenser': EInfotmp[0] + "",
                    'Tipo': EInfotmp[1] + "",
                    'Id Cliente': EInfotmp[2] + "",
                    'Data': EInfotmp[3] + "",
                    'Nr de contrato': EInfotmp[4] + "",
                    'Referencia': EInfotmp[5] + "",
                    'Estado': EInfotmp[6] + ""
                };
                EData.bInfo.push(EInfo);
                $.each(data, function() {
                    this.color_name = (!this.color_name) ? "Padrão" : this.color_name;
                    EData.products.push({
                        Nome: this.name,
                        Categoria: this.category.capitalize(),
                        Cor: this.color_name,
                        Qt: this.quantity
                    });
                    modal_tbody.append("<tr><td>" + this.name + "</td><td>" + this.category.capitalize() + "</td><td>" + this.color_name + "</td><td>" + this.quantity + "</td></tr>");
                });
                modal.modal("show");
                $.msg('unblock');
            }, "json").fail(function(data) {
                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                $.msg('unblock', 5000);
            });
        });
        table_path.on("click", ".ver_requisition_anexo", function() {
            modal_anexo.modal("show");
            var this_folder = $(this).val() + "_encomenda";
            $.msg();
            $.post('/AM/ajax/upload_file.php', {
                action: "get_anexos",
                folder: this_folder
            }, function(data) {
                var options = "";
                $.each(data, function() {
                    options += "<tr><td>" + this + "<div class='view-button'><a class='btn btn-mini' href='/AM/ajax/files/" + this_folder + "/" + this + "' download='" + this + "'><i class='icon-download'></i>Download</a></div></td></tr>";
                });
                modal_anexo.find("#show_requisition_anexos_tbody").html(options);
                $.msg('unblock');
            }, "json").fail(function(data) {
                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                $.msg('unblock', 5000);
            });
        });
        modal.on("click", "#print_requisition", function() {
            var doc = new jsPDF('p', 'pt', 'a4', true);
            last = doc.table(20, 20, EData.bInfo, ['Dispenser', 'Tipo', 'Id Cliente', 'Data', 'Nr de contrato', 'Referencia', 'Estado'], {
                autoSize: true,
                printHeaders: true,
                fontSize: 10
            });
            doc.table(20, 80, EData.products, ['Nome', 'Categoria', 'Cor', 'Qt'], {
                autoSize: true,
                printHeaders: true
            });
            doc.save(moment().format());
        });
        table_path.on("click", ".accept_requisition", function() {
            var this_button = $(this);
            bootbox.prompt("Comentários?", function(result) {
                if (result !== null) {
                    $.msg();
                    $.post('ajax/requisition.php', {
                        action: "accept_requisition",
                        id: this_button.val(),
                        message: result
                    }, function() {
                        Table_view_requisition.fnReloadAjax();
                        $.msg('unblock');
                    }).fail(function(data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
                }
            });
        });
        table_path.on("click", ".decline_requisition", function() {
            var this_button = $(this);
            bootbox.prompt("Motivo?", function(result) {
                if (result !== null) {
                    $.msg();
                    $.post('ajax/requisition.php', {
                        action: "decline_requisition",
                        id: this_button.val(),
                        message: result
                    }, function() {
                        Table_view_requisition.fnReloadAjax();
                        $.msg('unblock');
                    }).fail(function(data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
                }
            });
        });
    }
};