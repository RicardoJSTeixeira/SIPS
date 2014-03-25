
var products = function(options_ext)
{


    var me = this;

    this.file_uploaded = false;
    this.config = new Object();
    this.config.product_editable = true;
    $.extend(true, this.config, options_ext);



    this.init_to_datatable = function(datatable_path, edit_product_path, edit_product_modal)
    {
        datatable_path.off();

        //PRODUTOS-----------------------------------------------------------------------------------------------------------
        $.get("/AM/view/products/edit_product.html", function(data) {
           
                edit_product_path.off().empty();
                edit_product_path.append(data);
                edit_product_path.find("#cp_parent").chosen({no_results_text: "Sem resultados"});
                populate_parent(edit_product_path.find("#cp_parent"));
            
            update_products_datatable(datatable_path);


        });




        datatable_path.on("click", ".btn_ver_produto", function()
        {
            var product_id = $(this).data("product_id");
            $.post('/AM/ajax/products.php', {action: "get_produto_by_id", "id": product_id}, function(data) {
                edit_product_path.find("#cp_name").val(data.name);
                edit_product_path.find("#cp_category").val(data.category);
                edit_product_path.find("#cp_parent").val(data.parent_ids).trigger("chosen:updated");
                $.each(data.type, function()
                {
                    edit_product_path.find(":checkbox[name='tipo_user'][value='" + this + "']").prop("checked", true);
                });
                edit_product_path.find("#cp_mrm").val(data.max_req_m);
                edit_product_path.find("#cp_mrw").val(data.max_req_s);
                edit_product_path.find(":input").prop("disabled", true);
                edit_product_path.find("select").prop("disabled", true).trigger("chosen:updated");
                edit_product_path.find("#child_product_datatable").find("tbody").empty();
                if (Object.size(data.children))
                {
                    edit_product_path.find("#product_children_div").show();
                    $.each(data.children, function()
                    {
                        edit_product_path.find("#child_product_datatable").find("tbody").append("<tr><td>" + this.name + "</td><td>" + this.category + "</td></tr>");
                    });
                }
                else
                {
                    edit_product_path.find("#product_children_div").hide();
                }
                edit_product_path.find("#edit_product_button").hide();
                edit_product_modal.modal("show");
            }, "json");
        });


        datatable_path.on("click", ".btn_editar_produto", function()
        {
            var product_id = $(this).data("product_id");
            edit_product_path.find("#cp_parent option").prop("disabled", false);
            $.post('/AM/ajax/products.php', {action: "get_produto_by_id", "id": product_id}, function(data) {
                edit_product_path.find("#cp_name").val(data.name);
                edit_product_path.find("#cp_category").val(data.category);
                edit_product_path.find("#cp_parent option[value='" + product_id + "']").prop("disabled", true);
                edit_product_path.find("#cp_parent").val(data.parent_ids).trigger("chosen:updated");
                $.each(data.type, function()
                {
                    edit_product_path.find(":checkbox[name='tipo_user'][value='" + this + "']").prop("checked", true);
                });
                edit_product_path.find("#cp_mrm").val(data.max_req_m);
                edit_product_path.find("#cp_mrw").val(data.max_req_s);
                edit_product_path.find(":input").prop("disabled", false);
                edit_product_path.find("select").prop("disabled", false).trigger("chosen:updated");
                edit_product_path.find("#child_product_datatable").find("tbody").empty();
                if (Object.size(data.children))
                {
                    edit_product_path.find("#product_children_div").show();
                    $.each(data.children, function()
                    {
                        edit_product_path.find("#child_product_datatable").find("tbody").append("<tr><td>" + this.name + "</td><td>" + this.category + "</td></tr>");
                    });
                }
                else
                {
                    edit_product_path.find("#product_children_div").hide();
                }
                edit_product_path.find("#edit_product_button").show();
                edit_product_modal.modal("show");
                edit_product_path.on("click", "#edit_product_button", function(e)
                {
                    e.preventDefault();
                    var types = [];
                    $.each(edit_product_path.find(":input[name='tipo_user']:checked"), function()
                    {
                        types.push($(this).val());
                    });
                    var parents = [];
                    $.each(edit_product_path.find("#cp_parent option:selected"), function()
                    {
                        parents.push($(this).val());
                    });
                    $.post('/AM/ajax/products.php', {action: "edit_product", "id": product_id,
                        name: edit_product_path.find("#cp_name").val(),
                        max_req_m: edit_product_path.find("#cp_mrm").val(),
                        max_req_s: edit_product_path.find("#cp_mrw").val(),
                        category: edit_product_path.find("#cp_category").val(),
                        parent: parents,
                        type: types,
                        color: "all"}, function(data) {

                        edit_product_modal.modal("hide");
                        update_products_datatable(datatable_path);
                    }, "json");
                });

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
        path.empty();
        $.get("/AM/view/products/new_product.html", function(data) {
            path.empty();
            path.append(data);

            path.find("#new_parent").chosen({no_results_text: "Sem resultados"});
            populate_parent(path.find("#new_parent"));
            path.find("#create_new_product").click(function()
            {
                var types = [];
                $.each(path.find(":input[name='new_tipo_user']:checked"), function()
                {
                    types.push($(this).val());
                });
                var parents = [];
                $.each(path.find("#new_parent option:selected"), function()
                {
                    parents.push($(this).val());
                });
                if (path.find("#create_product_form").validationEngine("validate"))
                    $.post('/AM/ajax/products.php', {action: "criar_produto",
                        name: path.find("#new_name").val(),
                        max_req_m: path.find("#new_mrm").val(),
                        max_req_s: path.find("#new_mrw").val(),
                        category: path.find("#new_category").val(),
                        parent: parents,
                        type: types,
                        color: "all"
                    }, function() {
                        if (typeof callback === "function")
                            callback();
                    }, "json");
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
<optgroup value='3' label='Peças'></optgroup>",
                    aparelho = [],
                    pilha = [],
                    peça = [];
            select.append(temp);
            $.each(data, function()
            {
                switch (this.category)
                {
                    case "Aparelho":
                        aparelho.push("<option id=" + this.id + " value='" + this.id + "'>" + this.name + "</option>");
                        break;
                    case "Pilha":

                        pilha.push("<option id=" + this.id + " value='" + this.id + "'>" + this.name + "</option>");
                        break;
                    case "Peça":
                        peça.push("<option id=" + this.id + " value='" + this.id + "'>" + this.name + "</option>");
                        break;
                }
            });
            select.find("optgroup[value='1']").append(aparelho).end()
                    .find("optgroup[value='2']").append(pilha).end()
                    .find("optgroup[value='3']").append(peça).end().trigger("chosen:updated");
        }, "json");
    }

    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key))
                size++;
        }
        return size;
    };


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