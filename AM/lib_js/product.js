var products = function(geral_path, options_ext) {
    var me = this,
            product_id = 0,
            datatable_path = "";

    this.file_uploaded = false;
    this.config = {};
    this.config.product_editable = true;
    this.Table_view_product;

    $.extend(true, this.config, options_ext);
    this.init = function(callback) {
        $.get("/AM/view/products/product.html", function(data) {
            geral_path.empty().off().append(data);
            geral_path.find(".chosen-select").chosen({no_results_text: "Sem resultados", width: "100%"});
            $("#edit_product_data_promoçao1").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, startDate: moment().format("YYYY-MM-DD")})
                    .on('changeDate', function() {
                        $("#edit_product_data_promoçao2").datetimepicker('setStartDate', moment($(this).val()).format('YYYY-MM-DD'));
                    });
            $("#edit_product_data_promoçao2").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2, startDate: moment().format("YYYY-MM-DD")})
                    .on('changeDate', function() {
                        $("#edit_product_data_promoçao1").datetimepicker('setEndDate', moment($(this).val()).format('YYYY-MM-DD'));
                    });

            if (typeof callback === "function")
                callback();
        });
    };

    this.init_to_datatable = function(datatable_path1) {
        datatable_path = datatable_path1;
        datatable_path.off();
        var edit_product_modal = geral_path.find("#edit_product_modal");
        update_products_datatable(datatable_path);
        datatable_path.on("click", ".btn_ver_produto", function() {
            product_id = $(this).data("product_id");
            populate_modal(edit_product_modal, function() {
                get_promocao(edit_product_modal, function() {
                    edit_product_modal.find("#edit_product_button").hide();
                    edit_product_modal.find(".modal-body").find("#edit_product_new_promotion_div").hide();
                    edit_product_modal.find(".modal-body").find("#edit_product_add_promotion_toggle").hide();
                    edit_product_modal.find(".modal-body").find("#edit_product_button_color_add_line").hide();
                    edit_product_modal.find(".modal-body").find(":input").prop("disabled", true);
                    edit_product_modal.find(".modal-body").find("select").prop("disabled", true).trigger("chosen:updated");
                    edit_product_modal.find(".modal-body").find(".color_picker_select").parent().find("a").hide();
                });
            });
        });
        datatable_path.on("click", ".btn_editar_produto", function() {

            product_id = $(this).data("product_id");
            populate_modal(edit_product_modal, function() {
                get_promocao(edit_product_modal, function() {
                    edit_product_modal.find("#edit_product_button").show();
                    edit_product_modal.find(".modal-body").find("#edit_product_new_promotion_div").hide();
                    edit_product_modal.find(".modal-body").find("#edit_product_add_promotion_toggle").show();
                    edit_product_modal.find(".modal-body").find("#edit_product_button_color_add_line").show();
                    edit_product_modal.find(".modal-body").find(":input").prop("disabled", false);
                    edit_product_modal.find(".modal-body").find("select").prop("disabled", false).trigger("chosen:updated");
                });
            });
            edit_product_modal.on("click", "#edit_product_button", function(e) {
                e.preventDefault();
                var types = [];
                $.each(edit_product_modal.find(":input[name='edit_product_tipo_user']:checked"), function() {
                    types.push($(this).val());
                });
                var parents = [];
                $.each(edit_product_modal.find("#edit_product_parent option:selected"), function() {
                    parents.push($(this).val());
                });
                var color = [];
                $.each(edit_product_modal.find("#edit_product_table_tbody_color tr"), function() {
                    color.push({color: $(this).find(".color_picker_select").val(), name: $(this).find(".color_name").val()});
                });

                if (edit_product_modal.find("#edit_product_form").validationEngine("validate")) {
                    if (types.length) {
                        $.post('/AM/ajax/products.php', {action: "edit_product",
                            id: product_id,
                            name: edit_product_modal.find("#edit_product_name").val(),
                            max_req_m: edit_product_modal.find("#edit_product_mrm").val(),
                            max_req_s: edit_product_modal.find("#edit_product_mrw").val(),
                            category: edit_product_modal.find("#edit_product_category").val(),
                            parent: parents,
                            type: types,
                            color: color,
                            active: edit_product_modal.find("#edit_product_active").is(":checked"),
                            size: edit_product_modal.find("#edit_product_size").val()},
                        function(data) {
                            edit_product_modal.modal("hide");
                            update_products_datatable(datatable_path);
                            edit_product_modal.find("#edit_product_table_tbody_color tr").remove();
                        }, "json").fail(function(data) {
                            $.msg();
                            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                            $.msg('unblock', 5000);
                        });
                    }
                    else {
                        $.jGrowl("Escolha um tipo, Branch ou Dispenser", {life: 3500});
                    }
                }
            });
        });
        datatable_path.on("click", ".btn_apagar_produto", function() {
            geral_path.find("#remove_product_modal").modal("show");
            geral_path.find("#remove_product_button").data("button", $(this));
            geral_path.find("#remove_product_button").data("product_id", $(this).data("product_id"));


        });
        geral_path.off("click", "#remove_product_button");
        geral_path.on("click", "#remove_product_button", function() {
            var this_button = $(this).data("button");
            var product_id = $(this).data("product_id");
            $.msg();
            $.post('/AM/ajax/products.php', {action: "apagar_produto_by_id", "id": product_id}, function(data) {
                $.jGrowl("Produto apagado com sucesso", {life: 3500});
                if ($("#new_product_parent").length)//actualizar a lista de parents do novo produto
                    populate_parent($("#new_product_parent"), null, null, null);
                datatable_path.dataTable().fnDeleteRow(this_button.closest('tr')[0]);
                geral_path.find("#remove_product_modal").modal("hide");
                $.msg('unblock');
            }, "json").fail(function(data) {
                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                $.msg('unblock', 5000);
            });
        });
        edit_product_modal.on("click", "#edit_product_add_promotion_toggle", function(e) {
            e.preventDefault();
            $(this).hide();
            edit_product_modal.find("#edit_product_new_promotion_div").show();
            edit_product_modal.find("#edit_product_promotion_active").prop("checked", false);
            edit_product_modal.find("#edit_product_promotion_highlight").prop("checked", false);
            edit_product_modal.find("#edit_product_data_promoçao1").val("");
            edit_product_modal.find("#edit_product_data_promoçao2").val("");
        });
        edit_product_modal.on("click", "#edit_product_add_promotion_button", function(e) {
            e.preventDefault();
            if (edit_product_modal.find("#edit_product_form").validationEngine("validate")) {
                $.msg();
                $.post('/AM/ajax/products.php', {action: "add_promotion", "id": product_id,
                    active: edit_product_modal.find("#edit_product_promotion_active").is(":checked"),
                    highlight: edit_product_modal.find("#edit_product_promotion_highlight").is(":checked"),
                    data_inicio: edit_product_modal.find("#edit_product_data_promoçao1").val(),
                    data_fim: edit_product_modal.find("#edit_product_data_promoçao2").val()
                }, function(data) {
                    get_promocao(edit_product_modal);
                    edit_product_modal.find("#edit_product_add_promotion_toggle").show();
                    edit_product_modal.find("#edit_product_new_promotion_div").hide();
                    $.msg('unblock');
                }, "json").fail(function(data) {
                    $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                    $.msg('unblock', 5000);
                });
            }
        });

        edit_product_modal.on("click", "#edit_product_cancel_promotion_button", function(e) {
            e.preventDefault();
            $("#edit_product_new_promotion_div").hide();
            $("#edit_product_add_promotion_toggle").show();
        });
        edit_product_modal.on("change", "#edit_product_category", function() {
            if ($(this).val() === "Molde" || $(this).val() === "BTE" || $(this).val() === "RITE" || $(this).val() === "INTRA") {
                edit_product_modal.find("#edit_product_color_div").show();
            }
            else {
                edit_product_modal.find("#edit_product_table_tbody_color").empty();
                edit_product_modal.find("#edit_product_color_div").hide();
            }
        });
        edit_product_modal.on("click", "#edit_product_button_color_add_line", function(e) {
            e.preventDefault();
            edit_product_modal.find("#edit_product_table_tbody_color").append("<tr><td><select class='color_picker_select'></select></td><td><input type='text' class='color_name input-small validate[required]'></td><td><button class='btn remove_color icon-alone btn-danger'><i class='icon icon-trash'></i></button></td></tr>");
            $("#edit_product_table_tbody_color").find("select:last").append(geral_path.find("#colour_picker").find("option").clone()).colourPicker({
                ico: '/jquery/colourPicker/colourPicker.gif',
                title: false
            });
        });
        edit_product_modal.on("click", ".remove_color", function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
        edit_product_modal.on("click", ".remove_promotion_button", function(e) {
            e.preventDefault();
            var this_button = $(this);
            e.preventDefault();
            $.msg();
            $.post('/AM/ajax/products.php', {action: "remove_promotion", "id": product_id, "id_promotion": this_button.data("id_promotion")
            }, function() {
                this_button.parent().parent().remove();
                $.msg('unblock');
            }, "json").fail(function(data) {
                $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                $.msg('unblock', 5000);
            });
        });
    };
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------NEW PRODUCT---------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    this.init_new_product = function(new_product_path1, callback) {
        new_product_path1.append(geral_path.find("#new_product_form"));
        var new_product_path = new_product_path1;
        clear_new_product_area(new_product_path);

        populate_parent(new_product_path.find("#new_product_parent"), null, null, function() {
            new_product_path.find("#new_product_category").trigger("change");
        });

        new_product_path.find("#create_new_product_button").click(function(e) {
            e.preventDefault();
            var types = [];
            $.each(new_product_path.find("input[name='new_product_tipo_user']:checked"), function() {
                types.push($(this).val());
            });
            var parents = [];
            $.each(new_product_path.find("#new_product_parent option:selected"), function() {
                parents.push($(this).val());
            });
            var color = [];
            $.each(new_product_path.find("#new_product_table_tbody_color tr"), function() {
                color.push({color: $(this).find(".color_picker_select").val(), name: $(this).find(".color_name").val()});
            });

            if (new_product_path.find("#new_product_form").validationEngine("validate"))
                if (types.length) {
                    $.msg();
                    $.post('/AM/ajax/products.php', {action: "criar_produto",
                        name: new_product_path.find("#new_product_name").val(),
                        max_req_m: new_product_path.find("#new_product_mrm").val(),
                        max_req_s: new_product_path.find("#new_product_mrw").val(),
                        category: new_product_path.find("#new_product_category").val(),
                        parent: parents,
                        type: types,
                        color: color,
                        active: 1,
                        size: new_product_path.find("#new_product_size").val()
                    }, function(data) {
                        new_product_path.modal("hide");
                        if (datatable_path)
                        {
                            datatable_path.dataTable().fnAddData(data);
                            $.jGrowl("Produto criado com sucesso", {life: 3500});
                            clear_new_product_area(new_product_path);
                        }
                        $.msg('unblock');
                    }, "json").fail(function(data) {
                        $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
                        $.msg('unblock', 5000);
                    });
                }
                else {
                    $.jGrowl("Escolha um tipo, Branch ou Dispenser", {life: 3500});
                }
        });
        new_product_path.off("change", "#new_product_category");
        new_product_path.on("change", "#new_product_category", function() {
            new_product_path.find("#new_product_table_tbody_color").empty();
            new_product_path.find("#new_product_size_div").hide();
            new_product_path.find("#new_product_color_div").hide();
            switch ($(this).val()) {
                case "Molde":
                case "BTE":
                case "RITE":
                case "INTRA":
                    new_product_path.find("#new_product_color_div").show();
                    new_product_path.find("#new_product_table_tbody_color").append("<tr><td><select class=' input-small color_picker_select'></select></td><td><input type='text' class='color_name input-small validate[required]' value='Beje'></td><td><button class='btn btn-danger remove_color icon-alone'><i class='icon icon-trash'></i></button></td></tr>");
                    $("#new_product_table_tbody_color").find("select:last").append(geral_path.find("#colour_picker").find("option").clone()).colourPicker({
                        ico: '/jquery/colourPicker/colourPicker.gif',
                        title: false
                    });
                    break;
                case "Acessório":
                    new_product_path.find("#new_product_size_div").show();
                    break;
                default:
                    new_product_path.find("#new_product_table_tbody_color").empty();
                    break;
            }
        });
        new_product_path.off("click", "#new_product_button_color_add_line");
        new_product_path.on("click", "#new_product_button_color_add_line", function(e) {
            e.preventDefault();
            new_product_path.find("#new_product_table_tbody_color").append("<tr><td><select class=' input-small color_picker_select'></select></td><td><input type='text' class='color_name input-small validate[required]'></td><td><button class='btn remove_color icon-alone btn-danger'><i class='icon icon-trash'></i></button></td></tr>");
            $("#new_product_table_tbody_color").find("select:last").append(geral_path.find("#colour_picker").find("option").clone()).colourPicker({
                ico: '/jquery/colourPicker/colourPicker.gif',
                title: false
            });
        });
        new_product_path.off("click", ".remove_color");
        new_product_path.on("click", ".remove_color", function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
        if (typeof callback === "function")
            callback();
    };

    function clear_new_product_area(new_product_path) {
        new_product_path.find("input:not(:checkbox)").val("");
        new_product_path.find("select").val("").trigger("chosen:updated");
        populate_parent(new_product_path.find("#new_product_parent"), null, null, null);
        new_product_path.find("#new_product_mrm").val(5);
        new_product_path.find("#new_product_mrw").val(5);
        new_product_path.find("#new_product_category").trigger("change");
        new_product_path.find(":checkbox").prop("checked", true);
        new_product_path.find(":radio").prop("checked", true);
        new_product_path.find("#new_product_form").show();
        new_product_path.find("#new_product_size").val(0);
    }

    function update_products_datatable(datatable_path) {
        if (!me.Table_view_product) {
            me.Table_view_product = datatable_path.dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/products.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "listar_produtos_to_datatable"}, {"name": "product_editable", "value": me.config.product_editable});
                },
                "fnDrawCallback": function() {
                    $.each(me.Table_view_product.find(".btn_ver_produto"), function()
                    {
                        if ($(this).data().deleted)
                        {
                            $(this).closest("tr").addClass("error");
                        }
                        else if (!$(this).data().active)
                        {
                            $(this).closest("tr").addClass("warning");
                        }
                        else if ($(this).data().highlight) {
                            $(this).closest("tr").addClass("success");
                        }
                    });
                },
                "aoColumns": [{"sTitle": "Id", "sWidth": "25px"}, {"sTitle": "Nome"}, {"sTitle": "M.Mensal", "sWidth": "50px"}, {"sTitle": "M.Especial", "sWidth": "50px"}, {"sTitle": "Categoria", "sWidth": "70px"}, {"sTitle": "Tipo", "sWidth": "110px"}, {"sTitle": "Opções", "sWidth": "50px"}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
        } else {
            me.Table_view_product.fnReloadAjax();
        }
    }

    function  populate_modal(modal, callback) {
        $.msg();
        $.post('/AM/ajax/products.php', {action: "get_produto_by_id", "id": product_id}, function(data) {
            populate_parent(geral_path.find("#edit_product_parent"), data.parent_level, get_children(data), function()
            {
                modal.find("#edit_product_name").val(data.name);
                modal.find("#edit_product_category").val(data.category);
                modal.find("#edit_product_parent option[value='" + product_id + "']").prop("disabled", true);
                modal.find("#edit_product_parent").val(data.parents_id).trigger("chosen:updated");
                modal.find("#edit_product_active").prop("checked", ~~data.active);
                modal.find("#edit_product_size").val(data.size);
                $.each(data.type, function()
                {
                    modal.find(":checkbox[name='edit_product_tipo_user'][value='" + this + "']").prop("checked", true);
                });
                modal.find("#edit_product_mrm").val(data.max_req_m);
                modal.find("#edit_product_mrw").val(data.max_req_s);
                modal.find("#edit_product_child_datatable").find("tbody").empty();

                if (data.children.length) {
                    modal.find("#edit_product_children_div").show();
                    $.each(data.children, function() {
                        modal.find("#edit_product_child_datatable").find("tbody").append("<tr><td>" + this.name + "</td><td>" + this.category + "</td></tr>");
                    });
                }
                else {
                    modal.find("#edit_product_children_div").hide();
                }

                modal.find("#edit_product_size_div").hide();
                switch (data.category) {
                    case "Molde":
                    case "BTE":
                    case "RITE":
                    case "INTRA":
                        modal.find("#edit_product_color_div").show();
                        if (data.color) {
                            modal.find("#edit_product_table_tbody_color").empty();
                            $.each(data.color, function() {
                                modal.find("#edit_product_table_tbody_color").append("<tr><td><select class='color_picker_select'></select></td><td><input type='text' class='color_name input-small validate[required]' value='" + this.name + "'></td><td><button class='btn icon-alone remove_color  btn-danger'><i class='icon icon-trash'></i></button></td></tr>");
                                $("#edit_product_table_tbody_color").find("select:last").append(geral_path.find("#colour_picker").find("option").clone()).val(this.color).colourPicker({
                                    ico: '/jquery/colourPicker/colourPicker.gif',
                                    title: false
                                });
                            });
                        }
                        break;
                    case "Acessório":
                        modal.find("#edit_product_size_div").show();
                    default:
                        modal.find("#edit_product_color_div").hide();
                        break;
                }
                modal.modal("show");
                if (typeof callback === "function")
                    callback();
            });
            $.msg('unblock');
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    }

    function get_promocao(modal, callback) {
        $.msg();
        $.post('/AM/ajax/products.php', {action: "get_promotion", "id": product_id}, function(data) {
            var tbody = modal.find("#edit_product_promotion_table_tbody");
            tbody.empty();
            if (data.length) {
                $.each(data, function() {
                    if (this.active)
                        this.active = "sim";
                    else
                        this.active = "nao";
                    if (this.highlight)
                        this.highlight = "sim";
                    else
                        this.highlight = "nao";
                    tbody.append("<tr><td>" + this.active + "</td><td>" + this.highlight + "</td><td>" + this.data_inicio + "</td><td>" + this.data_fim + "</td><td><button class='btn btn-danger icon-alone remove_promotion_button' data-id_promotion='" + this.id + "'><i class='icon-trash'></i></button></td></tr>");
                });
            }
            else {
                tbody.append("<tr><td colspan='5'>Sem promoções</td></tr>");
            }
            if (typeof callback === "function")
                callback();
            $.msg('unblock');
        }, "json").fail(function(data) {
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    }

    function populate_parent(select, level_out, children, callback) {
        var out_level = level_out;
        var level = 0;

        $.post('/AM/ajax/products.php', {action: "get_produtos"},
        function(data) {
            var option = "";
            select.empty();
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
            select.append(temp);
            $.each(data, function() {
                if (out_level) {
                    level = level + out_level - 1;
                }
                option = "<option  id=" + this.id + "  value='" + this.id + "'>" + this.name + "</option>";
                if (this.parent_level >= 4)
                    option = "<option disabled id=" + this.id + "  value='" + this.id + "'>Max.Lvl. " + this.name + "</option>";
                if (children) {
                    if (children.indexOf(this.id) !== -1)
                        option = "<option disabled id=" + this.id + "  value='" + this.id + "'>Assoc. " + this.name + "</option>";
                }
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
            select.find("optgroup[value='1']").append(BTE).end()
                    .find("optgroup[value='2']").append(RITE).end()
                    .find("optgroup[value='3']").append(INTRA).end()
                    .find("optgroup[value='4']").append(pilha).end()
                    .find("optgroup[value='5']").append(acessorio).end()
                    .find("optgroup[value='6']").append(molde).end()
                    .find("optgroup[value='7']").append(economato).end()
                    .find("optgroup[value='8']").append(gama).end().trigger("chosen:updated");
            if (typeof callback === "function")
                callback();

        }, "json").fail(function(data) {
            $.msg();
            $.msg('replace', ((data.responseText.length) ? data.responseText : 'Ocorreu um erro, por favor verifique a sua ligação à internet e tente novamente.'));
            $.msg('unblock', 5000);
        });
    }

    function get_children(element) {
        var children = [];
        if (element.children) {
            $.each(element.children, function() {
                children.push(this.id);
                get_children_extra(this, children);
            });
        }
        return children;
    }

    function get_children_extra(element, children) {
        if (element.children) {
            $.each(element.children, function() {
                children.push(this.id);
                get_children_extra(this, children);
            });
        }
    }
};