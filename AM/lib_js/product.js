
var products = function(options_ext)
{


    var me = this;

    this.file_uploaded = false;
    this.config = new Object();
    this.config.product_editable = true;

    $.extend(true, this.config, options_ext);



    this.init_to_datatable = function(datatable_path, edit_product_path, edit_product_modal)
    {



        //PRODUTOS-----------------------------------------------------------------------------------------------------------
        var Table_view_product = datatable_path.dataTable({
            "aaSorting": [[6, "asc"]],
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

        datatable_path.on("click", ".btn_ver_produto", function()
        {
            var product_id = $(this).data("product_id");
            $.get("/AM/view/products/edit_product.html", function(data) {
                edit_product_path.empty();
                edit_product_path.append(data);
                $.post('/AM/ajax/products.php', {action: "get_produto_by_id", "id": product_id}, function(data) {

                    edit_product_path.find("#ep_name").val(data.name);
                    //get dos produtos pra popular select multi do parent
                    $.post('/AM/ajax/products.php', {action: "get_produtos"}, function(data1) {
                        var parent = edit_product_path.find("#cp_parent");
                        parent.empty();
                        $.each(data1, function()
                        {
                                parent.append("<option id='" + this.id + "' data-id='" + this.id + "'>" + this.name + "</option>");
                        });
                        
                        parent.chosen({no_results_text: "Sem resultados"}).val(data.parent).trigger("chosen:updated");



                        edit_product_path.find("#cp_category").val(data.category);
                        
                        edit_product_path.find("#cp_category").val(data.category);
                        
                        edit_product_path.find("#cp_mrm").val(data.max_req_m);
                        edit_product_path.find("#cp_mrw").val(data.max_req_s);
                        
                        
                        edit_product_path.find(":input").prop("readonly", true);
                    }, "json");
                }, "json");
                edit_product_modal.modal("show");
            });
        });


        datatable_path.on("click", ".btn_editar_produto", function()
        {
            $.get("/AM/view/products/edit_product.html", function(data) {
                edit_product_path.empty();
                edit_product_path.append(data);

                edit_product_modal.modal("show");
            });
        });
    };

    this.init_new_product = function(path, callback)
    {
        path.empty();
        $.get("/AM/view/products/new_product.html", function(data) {
            path.append(data);


            path.find(".chosen-select").chosen({no_results_text: "Sem resultados"});
            $.post('/AM/ajax/products.php', {action: "get_produtos"}, function(data) {
                var parent = path.find("#cp_parent");
                parent.empty();
                $.each(data, function()
                {
                    parent.append("<option data-id='" + this.id + "'>" + this.name + "</option>");
                });
                parent.trigger("chosen:updated");

            }, "json");







            path.find("#create_new_product").click(function()
            {
                var types = [];
                $.each($("#admin_zone :input[name='tipo_user']:checked"), function()
                {
                    types.push($(this).val());
                });
                var parents = [];
                $.each($("#admin_zone #cp_parent option:selected"), function()
                {
                    parents.push($(this).data("id"));
                });

                if (path.find("#create_product_form").validationEngine("validate"))
                    $.post('/AM/ajax/products.php', {action: "criar_produto",
                        name: $("#admin_zone #cp_name").val(),
                        max_req_m: $("#admin_zone #cp_mrm").val(),
                        max_req_s: $("#admin_zone #cp_mrw").val(),
                        category: $("#admin_zone #cp_category").val(),
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

    this.edit_product = function(path)
    {





        $.post('ajax/admin.php', {action: "listar_produtos"},
        function(data)
        {
            path.find("#ep_parent").empty().append("<option value='0'>Escolha um parente</option>");
            path.find("#cp_parent").empty().append("<option value='0'>Sem Parente</option>");
            var
                    temp = "<optgroup value='1' label='Aparelhos'></optgroup>\n\
<optgroup value='2' label='Pilhas'></optgroup>\n\
<optgroup value='3' label='Peças'></optgroup>",
                    aparelho = [],
                    pilha = [],
                    peça = [];
            path.find("#ep_parent").append(temp);
            path.find("#cp_parent").append(temp);
            $.each(data, function()
            {
                switch (this.category)
                {
                    case "Aparelho":
                        aparelho.push("<option id=" + this.id + ">" + this.name + "</option>");
                        break;
                    case "Pilha":
                        pilha.push("<option id=" + this.id + ">" + this.name + "</option>");
                        break;
                    case "Peça":
                        peça.push("<option id=" + this.id + ">" + this.name + "</option>");
                        break;
                }
            });
            path.find("#ep_parent").find("optgroup[value='1']").append(aparelho).end()
                    .find("optgroup[value='2']").append(pilha).end()
                    .find("optgroup[value='3']").append(peça).end().trigger("chosen:updated");
            path.find("#cp_parent").find("optgroup[value='1']").append(aparelho).end()
                    .find("optgroup[value='2']").append(pilha).end()
                    .find("optgroup[value='3']").append(peça).end().trigger("chosen:updated");
        }, "json");
    };


}