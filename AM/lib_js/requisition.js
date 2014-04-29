var requisition = function(geral_path, options_ext)
{
    this.file_uploaded = false;
    this.config = {};
    this.tipo = "mensal";
    var
            me = this,
            product_tree,
            modal = "",
            table_path = "",
            show_admin = 0,
            produtos = [];

    $.extend(true, this.config, options_ext);

    this.init = function(callback)
    {
        $.get("/AM/view/requisitions/requisition.html", function(data) {
            geral_path.off().empty().append(data);
            geral_path.find("#new_requisition_div").hide();
            modal = geral_path.find("#ver_product_requisition_modal");
            if (typeof callback === "function")
                callback();
        });
    };
    this.get_current_requisitions = function(table_path1, show_admin1)
    {
        table_path = table_path1;
        show_admin = show_admin1;
        get_encomendas_atuais(table_path, show_admin);
    };
//NEW REQUISITION------------------------------------------------------------------------------------------------------------------------------------------------
    this.new_requisition = function(new_requisition_zone, lead_id) {
        $.post('/AM/ajax/products.php', {action: "get_produtos"},
        function(data)
        {
            var options = "<option value='0'>Escolha um produto</option>";
            $.each(data, function()
            {
                produtos[this.id] = (this);
                if (this.parent_level <= 0)
                    options += "<option value='" + this.id + "'>" + this.name + "</option>";
            });
            $("#product_selector").append(options);
        }, "json");
        new_requisition_zone.off();
        new_requisition_zone.find('.fileupload').fileupload().end()
                .find("#form_encomenda_especial").validationEngine().end()
                .find("#form_encomenda_especial").show().end()
                .find(".tipo_div").hide();
        if (lead_id)
        {
            me.tipo = "especial";
            new_requisition_zone.find("#tipo_especial").show();
            new_requisition_zone.find("#tipo_encomenda").text("Encomenda Especial");
        }
        else
        {
            me.tipo = "mensal";
            new_requisition_zone.find("#tipo_encomenda").text("Encomenda Mensal");
        }
        new_requisition_zone.find("#save_single_product").prop("disabled", true);
        $(new_requisition_zone).on("change", "#product_selector", function()
        {
            if (product_tree)
                product_tree.destroy();

            if (~~$(this).val() === 0)
            {
                new_requisition_zone.find("#save_single_product").prop("disabled", true);
                return false;
            }
            else
            {
                new_requisition_zone.find("#save_single_product").prop("disabled", false);
            }
            product_tree = new tree("#tree", [produtos[$(this).val()]], me.tipo, $(this).val());
            product_tree.init();
        });
// SUBMITAR A ENCOMENDA------------------------------------------------
        $(new_requisition_zone).on("click", "#new_requisition_submit_button", function()
        {
            var produtos_encomenda = [];
            var count = 0;
            $.each(new_requisition_zone.find(" #produtos_encomendados tr"), function()
            {
                if ($(this).hasClass("product_line"))
                {
                    count++;
                    produtos_encomenda.push({id: $(this).find(".td_name").attr("id_product"), quantity: ~~$(this).find(".td_quantity").text(), color: $(this).find(".td_color").attr("color")});
                }
            });

            if (!count)
                $.jGrowl('Escolha pelo menos 1 produto', {life: 4000});
            else
            {
                if (new_requisition_zone.find(" #form_encomenda_especial").validationEngine("validate"))
                {
                    if ((new_requisition_zone.find(".fileupload-preview").text().length > 0 && me.file_uploaded) || !new_requisition_zone.find(".fileupload-preview").text().length)
                    {
                        $.post('ajax/requisition.php', {action: "criar_encomenda",
                            type: me.tipo,
                            lead_id: lead_id,
                            contract_number: new_requisition_zone.find("#new_requisition_contract").val(),
                            attachment: new_requisition_zone.find(".fileupload-preview").text(),
                            products_list: produtos_encomenda},
                        function(data) {
                            $.jGrowl('Encomenda realizada com sucesso', {life: 4000});
                            new_requisition_zone.find(" #new_requisition_contract").val("").end()
                                    .find(" #new_requisition_product_tbody").empty().end()
                                    .find("#label_anexo_info").text("").end()
                                    .find('.fileupload').fileupload("clear");
                            history.back();
                            if (table_path)
                            {
                                table_path.dataTable().fnAddData(data);
                            }

                        }, "json");
                    }
                    else
                        $.jGrowl('Certifique-se de que o ficheiro de anexo foi carregado para o servidor (botao "upload")', {life: 4000});
                }
            }
        });

        $(new_requisition_zone).on("click", '#save_single_product', function(e)
        {
            var produtos_single = [],
                    has_products = 0;
            $.each($("#tree").find(".product_item"), function()
            {
                if ($(this).find("input[type=checkbox]").is(":checked"))
                {
                    has_products++;
                    produtos_single.push({
                        id: $(this).prop("id_product"),
                        name: $(this).prop("name_product"),
                        category: $(this).prop("category_product"),
                        quantity: ~~$(this).find(".input_quantity").val(),
                        color_id: $(this).find(".color_select").val(),
                        color_name: $(this).find(".color_select option:selected").text()
                    });
                    if (me.tipo === "especial")
                        produtos[$(this).prop("id_product")].max_req_s -= ~~$(this).find(".input_quantity").val();
                    else
                        produtos[$(this).prop("id_product")].max_req_m -= ~~$(this).find(".input_quantity").val();
                }
            });
            if (!has_products)
            {
                $.jGrowl('Selecione pelo menos 1 produto', {life: 4000});
                return false;
            }
            new_requisition_zone.find("#product_selector").val(0).trigger("change");
            var new_product = "";

            new_product = ($("<div>", {class: "grid"}).append($("<div>")));

            var produtos_in_text = new_product
                    .find("div:last")
                    .append($("<table>", {class: "table table-mod table-striped table-condensed"})
                            .append($("<thead>")
                                    .append($("<tr>")
                                            .append($("<th>").text("Nome").css("width", "420px"))
                                            .append($("<th>").text("Categoria").css("width", "120px"))
                                            .append($("<th>").text("#").attr("title", "Quantidade").css("width", "50px"))
                                            .append($("<th>").text("Cor").css("width", "150px").append($("<button>", {class: "btn btn-danger btn-mini icon-alone right remove_produto_encomendado"}).append($("<i>", {class: "icon icon-trash"}))))
                                            ))
                            .append($("<tbody>")))
                    .find("tbody");
            $.each(produtos_single, function()
            {
                produtos_in_text.append($("<tr>", {class: "product_line"})
                        .append($("<td>", {class: "td_name", id_product: this.id}).text(this.name))
                        .append($("<td>", {class: "td_category"}).text(this.category.capitalize()))
                        .append($("<td>", {class: "td_quantity"}).text(this.quantity))
                        .append($("<td>", {class: "td_color", color: this.color_id}).text(this.color_name))
                        );
            });
            new_requisition_zone.find("#produtos_encomendados").append(new_product);
        });

        $(new_requisition_zone).on("click", " .remove_produto_encomendado", function(e)
        {
            $.each($(this).closest(".grid").find("tr"), function()
            {
                if ($(this).hasClass("product_line"))
                {
                    if (me.tipo === "especial")
                        produtos[$(this).find(".td_name").attr("id_product")].max_req_s = produtos[$(this).find(".td_name").attr("id_product")].max_req_s + ~~$(this).find(".td_quantity").text();
                    else
                        produtos[$(this).find(".td_name").attr("id_product")].max_req_m = produtos[$(this).find(".td_name").attr("id_product")].max_req_m + ~~$(this).find(".td_quantity").text();
                }
            });
            $(this).closest(".grid").remove();
        });

        $(new_requisition_zone).on("submit", " #form_encomenda_especial", function(e)
        {
            e.preventDefault();
        });

        //FILE UPLOAD
        $(new_requisition_zone).on("change", '#file_upload', function() {
            // var re_ext = new RegExp("(gif|jpeg|jpg|png|pdf)", "i");
            var file = this.files[0];
            var name = file.name;
            var size = (Math.round((file.size / 1024 / 1024) * 100) / 100);
            var type = file.type;
            if (size > 10) {
                new_requisition_zone.find("#label_anexo_info").text("O tamanho do ficheiro ultrapassa os 10mb permitidos.");
                $(this).fileupload('clear');
            }
            /*  if (!re_ext.test(type)) {
             $("#label_ipl_info").text("A extensão do ficheiro seleccionado não é valida.");
             $(this).fileupload('clear');
             }*/
            new_requisition_zone.find("#label_anexo_info").text("");
            me.file_uploaded = false;
        });
        $(new_requisition_zone).on("click", '#anexo_upload_button', function(e)
        {
            e.preventDefault();
            var form = new_requisition_zone.find("#anexo_input_form");
            if (form.find('input[type="file"]').val() === '')
                return false;
            var formData = new FormData(form[0]);
            formData.append("action", "upload");
            $.ajax({
                url: 'ajax/upload_file.php',
                type: 'POST',
                data: formData,
                dataType: "json",
                cache: false,
                complete: function(data) {
                    new_requisition_zone.find("#label_anexo_info").text(data.responseText);
                    me.file_uploaded = true;
                },
                contentType: false,
                processData: false
            });
        });
    };
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    function get_encomendas_atuais(table_path, show_admin)
    {
        if (show_admin)
        {
            var Table_view_requisition = table_path.dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requisition.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "listar_requisition_to_datatable"}, {"name": "show_admin", "value": show_admin});
                },
                "fnDrawCallback": function() {
                    $.each(Table_view_requisition.find(".cod_cliente_input"), function()
                    {
                        if (!$(this).val())
                            $(this).parent().parent().addClass("error");
                    });
                },
                "aoColumns": [{"sTitle": "ID"}, {"sTitle": "Dispenser"}, {"sTitle": "Tipo"}, {"sTitle": "ID Cliente"}, {"sTitle": "Data"}, {"sTitle": "Número de contrato"}, {"sTitle": "Código de cliente"}, {"sTitle": "Anexo"}, {"sTitle": "Produtos"}, {"sTitle": "Status"}, {"sTitle": "Opções", "sWidth": "60px"}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            Table_view_requisition.on("change", ".cod_cliente_input", function()
            {
                var that = $(this);
                if (!$(this).validationEngine("validate"))
                {
                    if (that.val().length)
                        bootbox.confirm("Tem a certeza que pretende actualizar para o codigo:" + that.val() + "?", function(result) {
                            if (result) {
                                $.post('/AM/ajax/requisition.php', {action: "editar_encomenda", "clientID": that.data().clientid, "cod_cliente": that.val()}, function(data) {
                                    that
                                            .closest("tr")
                                            .removeClass("error")
                                            .end()
                                            .replaceWith(that.val());
                                    Table_view_requisition.fnReloadAjax();
                                }, "json");
                            }
                        });
                }
            });
        }
        else
        {
            var Table_view_requisition = table_path.dataTable({
                "bSortClasses": false,
                "bProcessing": true,
                "bDestroy": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "sAjaxSource": '/AM/ajax/requisition.php',
                "fnServerParams": function(aoData) {
                    aoData.push({"name": "action", "value": "listar_requisition_to_datatable"}, {"name": "show_admin", "value": show_admin});
                },
                "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Tipo"}, {"sTitle": "Id Cliente"}, {"sTitle": "Data"}, {"sTitle": "Número de contrato"}, {"sTitle": "Código de cliente"}, {"sTitle": "Anexo"}, {"sTitle": "Produtos"}, {"sTitle": "Status"}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
        }
        //VER PRODUTOS DE ENCOMENDAS FEITAS
        table_path.on("click", ".ver_requisition_products", function()
        {
            $.post('ajax/requisition.php', {action: "listar_produtos_por_encomenda", id: $(this).val()}, function(data)
            {
                var modal_tbody = modal.find("#show_requisition_products_tbody");
                modal_tbody.empty();
                $.each(data, function()
                {
                    if (!this.color_name)
                        this.color_name = "Padrão";
                    modal_tbody.append("<tr><td>" + this.name + "</td><td>" + this.category.capitalize() + "</td><td>" + this.color_name + "</td><td>" + this.quantity + "</td></tr>");
                });
                modal.modal("show");
            }, "json");
        });
        table_path.on("click", ".accept_requisition", function()
        {
            var this_button = $(this);
            $.post('ajax/requisition.php', {action: "accept_requisition", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Aprovado");
                Table_view_requisition.fnReloadAjax();
            }, "json");
        });
        table_path.on("click", ".decline_requisition", function()
        {
            var this_button = $(this);
            bootbox.confirm("Tem a certeza?", function(result) {
                if (result) {
                    $.post('ajax/requisition.php', {action: "decline_requisition", id: this_button.val()}, function() {
                        this_button.parent().prev().text("Rejeitado");
                        Table_view_requisition.fnReloadAjax();
                    }, "json");
                }
            });
        });
    }

};