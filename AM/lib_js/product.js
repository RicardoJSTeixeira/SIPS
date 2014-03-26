
var products = function(options_ext)
{


    var me = this;

    this.file_uploaded = false;
    this.config = new Object();
    this.config.product_editable = true;
    $.extend(true, this.config, options_ext);



    this.init_to_datatable = function(datatable_path, product_path, product_modal)
    {
        var product_id = 0;
        datatable_path.off();

        //PRODUTOS-----------------------------------------------------------------------------------------------------------
        $.get("/AM/view/products/product.html", function(data) {

            product_path.off().empty();
            product_path.append(data);
            product_path.find(".form_datetime_day").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
            product_path.find("#product_parent").chosen({no_results_text: "Sem resultados"});
            populate_parent(product_path.find("#product_parent"));

            update_products_datatable(datatable_path);


        });

        function  populate_modal(callback)
        {
            $.post('/AM/ajax/products.php', {action: "get_produto_by_id", "id": product_id}, function(data) {

                product_path.find("#add_promotion_toggle").show();
                get_promocao();
                product_path.find("#product_name").val(data.name);
                product_path.find("#product_category").val(data.category);
                product_path.find("#product_parent option").prop("disabled", false);
                product_path.find("#product_parent option[value='" + product_id + "']").prop("disabled", true);
                product_path.find("#product_parent").val(data.parent_ids).trigger("chosen:updated");
                $.each(data.type, function()
                {
                    product_path.find(":checkbox[name='tipo_user'][value='" + this + "']").prop("checked", true);
                });
                product_path.find("#product_mrm").val(data.max_req_m);
                product_path.find("#product_mrw").val(data.max_req_s);

                product_path.find("#child_product_datatable").find("tbody").empty();
                if (data.children.length)
                {
                    product_path.find("#product_children_div").show();
                    $.each(data.children, function()
                    {
                        product_path.find("#child_product_datatable").find("tbody").append("<tr><td>" + this.name + "</td><td>" + this.category + "</td></tr>");
                    });
                }
                else
                {
                    product_path.find("#product_children_div").hide();
                }
//--------------------------------------------------------------------------COLOR-----------------------------------------
                if (data.category == "molde" || data.category == "aparelho")
                {
                    if (data.color)
                    {
                        product_path.find("#product_color_div").show();
                        product_path.find("#table_tbody_color").empty();
                        $.each(data.color, function()
                        {
                            product_path.find("#table_tbody_color").append("<tr><td><select class='color_picker_select'></select></td><td><input type='text' class='color_name input-small validate[required]' value='" + this.name + "'></td><td><button class='btn remove_color'><i class='icon icon-remove'></i></button></td></tr>");
                            $("#table_tbody_color").find("select:last").append(product_path.find("#colour_picker").find("option").clone()).val(this.color).colourPicker({
                                ico: '/jquery/colourPicker/colourPicker.gif',
                                title: false
                            });
                        });
                    }
                }
                else
                {
                    product_path.find("#product_color_div").hide();
                }



                product_modal.modal("show");

                if (typeof callback === "function")
                    callback();
            }, "json");
        }


        datatable_path.on("click", ".btn_ver_produto", function()
        {
            product_id = $(this).data("product_id");
            populate_modal(function()
            {
                product_path.find("#add_promotion_toggle").hide();
                product_path.find("#button_criar_produto").hide();
                product_path.find("#button_editar_produto").hide();
                product_path.find("#add_promotion_toggle").hide();
                product_path.find(":input").prop("disabled", true);
                product_path.find("select").prop("disabled", true).trigger("chosen:updated");
            });

        });


        datatable_path.on("click", ".btn_editar_produto", function()
        {
            product_id = $(this).data("product_id");
            populate_modal(function()
            {
                product_path.find("#add_promotion_toggle").show();
                product_path.find("#button_criar_produto").hide();
                product_path.find("#button_editar_produto").show();
                product_path.find("#add_promotion_toggle").show();
                product_path.find(":input").prop("disabled", false);
                product_path.find("select").prop("disabled", false).trigger("chosen:updated");
            });

            product_path.on("click", "#edit_product_button", function(e)
            {
                e.preventDefault();
                var types = [];
                $.each(product_path.find(":input[name='tipo_user']:checked"), function()
                {
                    types.push($(this).val());
                });
                var parents = [];
                $.each(product_path.find("#product_parent option:selected"), function()
                {
                    parents.push($(this).val());
                });
                var color = [];
                $.each(product_path.find("#table_tbody_color tr"), function()
                {
                    color.push({color: $(this).find(".color_picker_select").val(), name: $(this).find(".color_name").val()});
                });
                $.post('/AM/ajax/products.php', {action: "edit_product", "id": product_id,
                    name: product_path.find("#product_name").val(),
                    max_req_m: product_path.find("#product_mrm").val(),
                    max_req_s: product_path.find("#product_mrw").val(),
                    category: product_path.find("#product_category").val(),
                    parent: parents,
                    type: types,
                    color: color}, function(data) {

                    product_modal.modal("hide");
                    update_products_datatable(datatable_path);
                }, "json");
            });
        });



        product_modal.on("click", "#add_promotion_toggle", function(e)
        {
            e.preventDefault();
            $(this).hide();
            product_path.find("#promotion_div").show();
            product_path.find("#promotion_active").prop("checked", false);
            product_path.find("#promotion_highlight").prop("checked", false);
            product_path.find("#data_promoçao1").val("");
            product_path.find("#data_promoçao2").val("");

        });
        product_modal.on("click", "#add_promotion_button", function(e)
        {
            e.preventDefault();
            if (product_path.find("#edit_product_form").validationEngine("validate"))
            {
                $.post('/AM/ajax/products.php', {action: "add_promotion", "id": product_id,
                    active: product_path.find("#promotion_active").is(":checked"),
                    highlight: product_path.find("#promotion_highlight").is(":checked"),
                    data_inicio: product_path.find("#data_promoçao1").val(),
                    data_fim: product_path.find("#data_promoçao2").val()
                }, function(data) {
                    get_promocao();
                    product_modal.find("#add_promotion_toggle").show();
                    product_modal.find("#promotion_div").hide();
                }, "json");
            }

        });
        product_modal.on("change", "#product_category", function()
        {

            if ($(this).val() == "molde" || $(this).val() == "aparelho")
            {
                product_modal.find("#product_color_div").show();
            }
            else
            {
                product_path.find("#table_tbody_color").empty();
                product_modal.find("#product_color_div").hide();
            }
        });
        product_modal.off("click", "#button_color_add_line");
        product_modal.on("click", "#button_color_add_line", function(e)
        {
            e.preventDefault();
            product_modal.find("#table_tbody_color").append("<tr><td><select class='color_picker_select'></select></td><td><input type='text' class='color_name input-small validate[required]'></td><td><button class='btn remove_color'><i class='icon icon-remove'></i></button></td></tr>");
            $("#table_tbody_color").find("select:last").append(product_modal.find("#colour_picker").find("option").clone()).colourPicker({
                ico: '/jquery/colourPicker/colourPicker.gif',
                title: false
            });
        });

        product_modal.on("click", ".remove_color", function(e)
        {
            e.preventDefault();


            $(this).parent().parent().remove();
        });

        function get_promocao()
        {
            $.post('/AM/ajax/products.php', {action: "get_promotion", "id": product_id}, function(data) {
                var tbody = product_path.find("#promotion_table_tbody");
                tbody.empty();
                if (data.length)
                {
                    $.each(data, function()
                    {
                        if (this.activo)
                            this.activo = "sim";
                        else
                            this.activo = "nao";

                        if (this.highlight)
                            this.highlight = "sim";
                        else
                            this.highlight = "nao";
                        tbody.append("<tr><td>" + this.activo + "</td><td>" + this.highlight + "</td><td>" + this.data_inicio + "</td><td>" + this.data_fim + "</td><td><button class='btn remove_promotion_button' data-id_promotion='" + this.id + "'>Remover</button></td></tr>")
                    });
                }
                else
                {
                    tbody.append("<tr><td>Sem promoções</td></tr>");
                }

            }, "json");
        }

        product_modal.on("click", ".remove_promotion_button", function(e)
        {
            e.preventDefault();
            var this_button = $(this);
            e.preventDefault();
            $.post('/AM/ajax/products.php', {action: "remove_promotion", "id": product_id, "id_promotion": this_button.data("id_promotion")
            }, function(data) {
                this_button.parent().parent().remove();
            }, "json");

        });

        datatable_path.on("click", ".btn_apagar_produto", function()
        {
            var this_button = $(this);
            var product_id = $(this).data("product_id");
            $.post('/AM/ajax/products.php', {action: "apagar_produto_by_id", "id": product_id}, function(data) {

                this_button.parent().parent().remove();
            }, "json");
        });


    };



    this.init_new_product = function(path, callback)
    {


        $.get("/AM/view/products/product.html", function(data) {
            path.empty().off();
            path.append(data);
            path.find("#product_parent").chosen({no_results_text: "Sem resultados"});
            populate_parent(path.find("#product_parent"));

            path.find("#button_editar_produto").hide();
            path.find("#product_children_div").hide();
            path.find("#product_promotion_div").hide();
            path.find("#product_color_div").hide();
//--------------------------------------------------------------------------------------CREATE PRODUCT-----------------------------------------------------------
            path.find("#create_product_button").click(function(e)
            {
                e.preventDefault();
                var types = [];
                $.each(path.find(":input[name='tipo_user']:checked"), function()
                {
                    types.push($(this).val());
                });
                var parents = [];
                $.each(path.find("#product_parent option:selected"), function()
                {
                    parents.push($(this).val());
                });
                var color = [];
                $.each(path.find("#table_tbody_color tr"), function()
                {
                    color.push({color: $(this).find(".color_picker_select").val(), name: $(this).find(".color_name").val()})
                });
                if (path.find("#create_product_form").validationEngine("validate"))
                    $.post('/AM/ajax/products.php', {action: "criar_produto",
                        name: path.find("#product_name").val(),
                        max_req_m: path.find("#product_mrm").val(),
                        max_req_s: path.find("#product_mrw").val(),
                        category: path.find("#product_category").val(),
                        parent: parents,
                        type: types,
                        color: color
                    }, function() {
                        if (typeof callback === "function")
                            callback();
                    }, "json");
            });
//------------------------------------------------------------- ON CHANGE para mostrar COLOR DIV se for aparelho ou molde--------------------------------------------------------
            path.on("change", "#product_category", function()
            {

                if ($(this).val() == "molde" || $(this).val() == "aparelho")
                {
                    path.find("#product_color_div").show();
                }
                else
                {
                    path.find("#product_color_div").hide();
                }
            });

            path.off("click", "#button_color_add_line");
            path.on("click", "#button_color_add_line", function(e)
            {
                e.preventDefault();
                path.find("#table_tbody_color").append("<tr><td><select class='color_picker_select'></select></td><td><input type='text' class='color_name input-small validate[required]'></td><td><button class='btn remove_color'><i class='icon icon-remove'></i></button></td></tr>");
                $("#table_tbody_color").find("select:last").append(path.find("#colour_picker").find("option").clone()).colourPicker({
                    ico: '/jquery/colourPicker/colourPicker.gif',
                    title: false
                });
            });

            path.on("click", ".remove_color", function(e)
            {
                e.preventDefault();


                $(this).parent().parent().remove();
            });


        });

    };



    function populate_parent(select)
    {
        $.post('/AM/ajax/products.php', {action: "get_produtos"},
        function(data)
        {
            select.empty();
            var
                    temp = "<optgroup value='1' label='Aparelhos'></optgroup>\n\
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
            $.each(data, function()
            {
                switch (this.category)
                {
                    case "aparelho":
                        aparelho.push("<option id=" + this.id + " value='" + this.id + "'>" + this.name + "</option>");
                        break;
                    case "pilha":

                        pilha.push("<option id=" + this.id + " value='" + this.id + "'>" + this.name + "</option>");
                        break;
                    case "acessorio":
                        acessorio.push("<option id=" + this.id + " value='" + this.id + "'>" + this.name + "</option>");
                        break;
                    case "molde":
                        molde.push("<option id=" + this.id + " value='" + this.id + "'>" + this.name + "</option>");
                        break;
                    case "economato":
                        economato.push("<option id=" + this.id + " value='" + this.id + "'>" + this.name + "</option>");
                        break;
                }
            });
            select.find("optgroup[value='1']").append(aparelho).end()
                    .find("optgroup[value='2']").append(pilha).end()
                    .find("optgroup[value='3']").append(acessorio).end()
                    .find("optgroup[value='4']").append(molde).end()
                    .find("optgroup[value='5']").append(economato).end().trigger("chosen:updated");
        }, "json");
    }



    function update_products_datatable(datatable_path)
    {

        var Table_view_product = datatable_path.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/products.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "listar_produtos_to_datatable"}, {"name": "product_editable", "value": me.config.product_editable});
            },
            "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Max requisições mensais"}, {"sTitle": "Max requisições especiais"}, {"sTitle": "Categoria"}, {"sTitle": "Tipo"}, {"sTitle": "Opções"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });
    }
    ;
}