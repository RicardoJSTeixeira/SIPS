
var requisition = function(geral_path, options_ext)
{


    var me = this;
    this.file_uploaded = false;
    this.config = new Object();
    var product = 1;
    var product_sub = 1;
    var modal = "";
    var table_path = "";
    var show_admin = 0;
    var tipo = "mensal";
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

        new_requisition_zone.off();
        new_requisition_zone.find('.fileupload').fileupload();
        new_requisition_zone.find("#requisition_form").validationEngine();
        new_requisition_zone.find("#requisition_form").show();
        //preencher com lead_id enviado
        new_requisition_zone.find(".tipo_div").hide();
        if (lead_id)
        {
            me.tipo = "especial";
            new_requisition_zone.find("#tipo_especial").show();
            new_requisition_zone.find("#new_requisition_lead_id").val(lead_id);
        }
        else
        {
            me.tipo = "mensal";
            new_requisition_zone.find("#tipo_mensal").show();
        }
        // ADICIONAR E REMOVER ITEMS DA LISTA DE PRODUTOS NA NOVA ENCOMENDA----------------------------
        $(new_requisition_zone).on("click", "#new_requisition_product_add_line", function(e)
        {
            e.preventDefault();
            new_requisition_zone.append(new_requisition_zone.find("#placeholder_new_product_div").clone().prop("id", "").show());
            add_sub_product(new_requisition_zone.find(".product_div").last().find(".level_div").first());

        });

        $(new_requisition_zone).on("click", ".add_second_sub_product", function(e)
        {
            e.preventDefault();
            add_second_sub_product($(this).closest(".level_div"));
        });

        $(new_requisition_zone).on("click", ".remove_second_sub_product", function(e)
        {
            e.preventDefault();

            $(this).closest(".sub_product_div").remove();
        });

        $(new_requisition_zone).on("change", ".select_product", function()
        {
            var this_div = $(this).closest(".sub_product_div");
 

            if ($(this).val() == 0)
            {
                this_div.find(".select_color_picker").empty();
                this_div.find(".product_span_max").text("");
                this_div.find(".quantity_input").val("");
                if (!get_previous_product(this_div).length + get_next_product(this_div).length)
                    remove_sub_product(this_div.closest(".level_div").next());
            }
            else
            {
                if (me.tipo == "mensal")
                {
                    this_div.find(".product_span_max").text($(this).find("option:selected").data().max_month);
                    this_div.find(".quantity_input").attr("max", ($(this).find("option:selected").data().max_month));
                    if (this_div.find(".quantity_input").val() > $(this).find("option:selected").data().max_month)
                        this_div.find(".quantity_input").val($(this).find("option:selected").data().max_month);
                }
                else
                {
                    this_div.find(".product_span_max").text($(this).find("option:selected").data().max_special);
                    this_div.find(".quantity_input").attr("max", ($(this).find("option:selected").data().max_special));
                    if (this_div.find(".quantity_input").val() > $(this).find("option:selected").data().max_special)
                        this_div.find(".quantity_input").val($(this).find("option:selected").data().max_special);
                }
                var select = this_div.find(".select_color_picker");
                select.empty();
                if ($(this).find("option:selected").data().color)
                {
                    $.each($(this).find("option:selected").data().color, function()
                    {
                        select.append("<option data-color_id='" + this.color + "' data-color_name='" + this.name + "'>" + this.name + "</option>");
                    });
                }
                else
                    select.append("<option>Padrão</option>");


                //Verifica se ha + children, se sim, adiciona novo sub produto
                $.post('/AM/ajax/products.php', {action: "get_produto_by_id", id: $(this).val()},
                function(data1)
                {
                    if (get_children(data1).length)
                    {

                        if (!get_next_level(this_div.closest(".level_div")).find(".select_product").length)
                            add_sub_product(this_div.closest(".level_div").next(), get_parents(get_previous_level(this_div.closest(".level_div"))));
                    }
                }, "json");

            }
        });


        function get_previous_level(div)
        {
            return div.prev(".level_div");
        }
        function get_next_level(div)
        {
            return div.next(".level_div");
        }
        function get_next_product(div)
        {
            return div.next(".sub_product_div");
        }
        function get_previous_product(div)
        {
            return div.prev(".sub_product_div");
        }
        function get_parents(div)
        {
            var parents = [];

            if (div.closest(".level_div").find(".select_product").length)
                $.each(div.closest(".level_div").find(".select_product"), function()
                {
                    parents.push(parseInt($(this).find("option:selected").val()));
                });


            return parents;
        }
        function add_sub_product(div, parents)
        {
            div.append(new_requisition_zone.find("#placeholder_new_product").clone().prop("id", "").show());

            prepare_new_sub_product(div.find(".sub_product_div").last(), parents);
            product_sub++;
        }

        function add_second_sub_product(div, parents)
        {
            div.append(new_requisition_zone.find("#placeholder_new_product").clone().prop("id", "").show());

            prepare_new_sub_product(div.find(".sub_product_div").last(), parents);
            product_sub++;
        }

        function prepare_new_sub_product(this_div, parents)
        {
            if (parents)
                var parents = parents;
            else
                var parents = get_parents(this_div);


            //select
            this_div.find(".select_div").append("<select  class='chosen-select select_product'></select>");
            //color    
            this_div.find(".color_div").append("<select class='input-medium select_color_picker'></select>");
            //Max    
            this_div.find(".max_div").append("<span class='product_span_max'></span>");
            //Quantity    
            this_div.find(".quantity_div").append("<input class='quantity_input input-mini validate[required,custom[onlyNumberSp]]'  type='number' min='1' value='1'>");
            this_div.find(".select_product").chosen({no_results_text: "Sem resultados"});

            product_sub++;


            if (parents.length)
            {
                $.post('/AM/ajax/products.php', {action: "get_produtos"},
                function(data1)
                {
                    var children = [];
                    var that = [];
                    $.each(data1, function()
                    {
                        var that = this;
                        if (parents.indexOf(that.id) != -1)
                        {
                            var temp = get_children(that);

                            for (var i = 0; i < temp.length; i++)
                            {

                                children.push(temp[i]);
                            }
                        }
                    });

                    populate_select(this_div.find(".select_product"), children, parents, null);
                }, "json");
            }
            else
                populate_select(this_div.find(".select_product"), null, parents, null);
        }

        function update_selects(div)
        {

            $.each(div.find(".select_product"), function()
            {
                populate_select(div.find(".select_product"), null, get_parents(div), null);
            });



        }

        function remove_sub_product(div)
        {

            if (div.next(".level_div").length)
            {
                remove_sub_product(div.next(".level_div"));
            }
            div.empty();
        }

        function populate_select(select, children, parents, callback)
        {
            $.post('/AM/ajax/products.php', {action: "get_produtos"},
            function(data)
            {
                var optgroups = [];
                select.empty();
                optgroups.push("<option value='0'>Escolha um produto</option><optgroup value='1' label='Aparelhos'></optgroup>");
                var temp = "<option value='0'>Escolha um produto</option>\n\
                            <optgroup value='1' label='Aparelhos'></optgroup>\n\
                            <optgroup value='2' label='Pilhas'></optgroup>\n\
                            <optgroup value='3' label='Acessórios'></optgroup>\n\
                            <optgroup value='4' label='Moldes'></optgroup>\n\
                            <optgroup value='5' label='Economato'></optgroup>",
                        aparelho = [],
                        pilha = [],
                        acessorio = [],
                        molde = [],
                        economato = [];
                select.append(temp);

                console.log(children);
                console.log(parents);


                $.each(data, function()
                {


                    if (children)
                    {
                        if (children.length)
                            if (children.indexOf(this.id) === -1)
                                return true;
                    }

                    if (parents)
                    {
                        console.log(parents.indexOf(this.id) != -1);
                        if (parents.indexOf(this.id) != -1)
                            return true;
                    }

                    switch (this.category)
                    {
                        case "aparelho":
                            aparelho.push("<option data-color='" + JSON.stringify(this.color) + "' data-max_month='" + this.max_req_m + "' data-max_special='" + this.max_req_s + "'  value='" + this.id + "'>" + this.name + "</option>");
                            break;
                        case "pilha":
                            pilha.push("<option data-color='" + JSON.stringify(this.color) + "' data-max_month='" + this.max_req_m + "' data-max_special='" + this.max_req_s + "'  value='" + this.id + "'>" + this.name + "</option>");
                            break;
                        case "acessorio":
                            acessorio.push("<option data-color='" + JSON.stringify(this.color) + "' data-max_month='" + this.max_req_m + "' data-max_special='" + this.max_req_s + "'  value='" + this.id + "'>" + this.name + "</option>");
                            break;
                        case "molde":
                            molde.push("<option data-color='" + JSON.stringify(this.color) + "' data-max_month='" + this.max_req_m + "' data-max_special='" + this.max_req_s + "'  value='" + this.id + "'>" + this.name + "</option>");
                            break;
                        case "economato":
                            economato.push("<option data-color='" + JSON.stringify(this.color) + "' data-max_month='" + this.max_req_m + "' data-max_special='" + this.max_req_s + "'  value='" + this.id + "'>" + this.name + "</option>");
                            break;
                    }
                });
                select.find("optgroup[value='1']").append(aparelho).end()
                        .find("optgroup[value='2']").append(pilha).end()
                        .find("optgroup[value='3']").append(acessorio).end()
                        .find("optgroup[value='4']").append(molde).end()
                        .find("optgroup[value='5']").append(economato).end().trigger("chosen:updated");
                if (typeof callback === "function")
                    callback();
            }
            , "json");
        }

// SUBMITAR A ENCOMENDA------------------------------------------------
        $(new_requisition_zone).on("click", "#new_requisition_submit_button", function()
        {
            var produtos = [];
            var count = 0;
            $.each(new_requisition_zone.find(" #new_requisition_product_tbody tr"), function()
            {
                var this_option_selected = $(this).find("select").find("option:selected");
                if (this_option_selected.val() !== "0")
                {
                    count++;
                    produtos.push({"id": this_option_selected.attr("id"), "quantity": $(this).find("input[type='number']").val(), "color": $(this).find(".select_color_picker").find("option:selected").data().color_id || "", "color_name": $(this).find(".select_color_picker").find("option:selected").data().color_name || ""});
                }
                else
                {
                    $.jGrowl('Existe um ou mais produtos para selecionar', {life: 4000});
                    count = 0;
                    return false;
                }
            });
            if (!count)
                $.jGrowl('Certifique-se que os produtos estão correctamente escolhidos', {life: 4000});
            else
            {
                if (new_requisition_zone.find(" #requisition_form").validationEngine("validate"))
                {
                    if ((new_requisition_zone.find(".fileupload-preview").text().length > 0 && me.file_uploaded) || !new_requisition_zone.find(".fileupload-preview").text().length)
                    {
                        $.post('ajax/requisition.php', {action: "criar_encomenda",
                            type: me.tipo,
                            lead_id: new_requisition_zone.find("#new_requisition_lead_id").val(),
                            contract_number: new_requisition_zone.find(" #new_requisition_contract").val(),
                            attachment: new_requisition_zone.find(".fileupload-preview").text(),
                            products_list: produtos},
                        function(data) {
                            if (table_path)
                            {
                                table_path.dataTable().fnAddData(data);
                            }
                            $.jGrowl('Encomenda realizada com sucesso', {life: 4000});
                            new_requisition_zone.find(" #new_requisition_lead_id").val("");
                            new_requisition_zone.find(" #new_requisition_contract").val("");
                            new_requisition_zone.find(" #new_requisition_product_tbody").empty();
                            new_requisition_zone.find("#label_anexo_info").text("");
                            new_requisition_zone.find('.fileupload').fileupload("clear");
                        }, "json");
                    }
                    else
                        $.jGrowl('Certifique-se de que o ficheiro de anexo foi carregado para o servidor (botao "upload")', {life: 4000});
                }
            }
        });
        $(new_requisition_zone).on("click", ".remove_item_requisition_table", function()
        {
            $(this).closest(".grid").remove();
            $.jGrowl('Linha de produtos removida com sucesso', {life: 4000});
        });
        $(new_requisition_zone).on("submit", " #requisition_form", function(e)
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
    }
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
                "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Tipo"}, {"sTitle": "Id Cliente"}, {"sTitle": "Data"}, {"sTitle": "Número de contrato"}, {"sTitle": "Código de cliente"}, {"sTitle": "Anexo"}, {"sTitle": "Produtos"}, {"sTitle": "Status"}, {"sTitle": "Opções"}],
                "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
            });
            Table_view_requisition.on("change", ".cod_cliente_input", function()
            {
                var requisition_number = $(this).data("requisition_id");
                var value = $(this).val();
                if (!$(this).validationEngine("validate"))
                {
                    if (value.length)
                        $(this).parent().parent().removeClass("error");
                    else
                        $(this).parent().parent().addClass("error");
                    $.post('/AM/ajax/requisition.php', {action: "editar_encomenda", "req_id": requisition_number, "cod_cliente": value}, function(data) {
                    }, "json");
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
                    modal_tbody.append("<tr><td>" + this.name + "</td><td>" + this.category + "</td><td>" + this.color_name + "</td><td>" + this.quantity + "</td></tr>");
                });
                modal.modal("show");
            }, "json");
        });
        table_path.on("click", ".accept_requisition", function()
        {
            var this_button = $(this);
            $.post('ajax/requisition.php', {action: "accept_requisition", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Aprovado");
            }, "json");
        });
        table_path.on("click", ".decline_requisition", function()
        {
            var this_button = $(this);
            $.post('ajax/requisition.php', {action: "decline_requisition", id: $(this).val()}, function() {
                this_button.parent().prev().text("Rejeitado");
            }, "json");
        });
    }









    function find_level(element)
    {
        var level = 1;
        if (element.children)
        {
            $.each(element.children, function()
            {
                level = level + find_level(this);
            });
        }

        return level;
    }

    function get_children(element)
    {
        var children = [];
        if (element.children)
        {
            $.each(element.children, function()
            {
                children.push(this.id);
                get_children_extra(this, children);
            });
        }
        return children;
    }

    function get_children_extra(element, children)
    {
        if (element.children)
        {
            $.each(element.children, function()
            {
                children.push(this.id);
                get_children_extra(this, children);
            });
        }
    }

}
;




