

$(function()
{
    $("#admin_zone .form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
    $("#admin_zone .check_form").validationEngine();
    get_info();
    $("#admin_zone .chosen-select").chosen({no_results_text: "Sem resultados"});
});


$("#admin_zone .check_form").submit(function(e)
{
    e.preventDefault();
})

//OpçÔES TOGGLE
$("#admin_zone #button_filtro_produto").click(function()
{
    $("#admin_zone #product_master_div").toggle("blind");
    $(this).toggleClass("icon-chevron-down").toggleClass("icon-chevron-up");
});

$("#admin_zone #button_filtro_cliente").click(function()
{
    $("#admin_zone #client_master_div").toggle("blind");
    $(this).toggleClass("icon-chevron-down").toggleClass("icon-chevron-up");
});

$("#admin_zone #button_filtro_children_cliente").click(function()
{
    $("#admin_zone #child_product_datatable_div").toggle("blind");
    $(this).toggleClass("icon-chevron-down").toggleClass("icon-chevron-up");
});
//------------




function get_info()
{
    var Table_view_product = $('#admin_zone #view_product_datatable').dataTable({
        "aaSorting": [[6, "asc"]],
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/admin.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "listar_produtos_to_datatable"});
        },
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Parente"}, {"sTitle": "Venda individual"}, {"sTitle": "Max requisições mês"}, {"sTitle": "Max requisições semana"}, {"sTitle": "Categoria"}, {"sTitle": "Opções"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });


    $.post('ajax/admin.php', {action: "listar_produtos"},
    function(data)
    {
        $("#admin_zone #ep_parent").empty();
        $("#admin_zone #ep_parent").append("<option value='0'>Escolha um parente</option>");
        $("#admin_zone #cp_parent").empty();
        $("#admin_zone #cp_parent").append("<option value='0'>Escolha um parente</option>");
        var temp = "<optgroup value='1' label='Aparelhos'></optgroup>\n\
<optgroup value='2' label='Pilhas'></optgroup>\n\
<optgroup value='3' label='Peças'></optgroup>";
        $("#admin_zone #ep_parent").append(temp);
        $("#admin_zone #cp_parent").append(temp);
        $.each(data, function()
        {
            switch (this.category)
            {
                case "Aparelho":
                    $("#admin_zone #ep_parent optgroup[value='1']").append("<option value='" + this.id + "'>" + this.name + "</option>");
                    $("#admin_zone #cp_parent optgroup[value='1']").append("<option value='" + this.id + "'>" + this.name + "</option>");
                    break;
                case "Pilha":
                    $("#admin_zone #ep_parent optgroup[value='2']").append("<option value='" + this.id + "'>" + this.name + "</option>");
                    $("#admin_zone #cp_parent  optgroup[value='2']").append("<option value='" + this.id + "'>" + this.name + "</option>");
                    break;
                case "Peça":
                    $("#admin_zone #ep_parent optgroup[value='3']").append("<option value='" + this.id + "'>" + this.name + "</option>");
                    $("#admin_zone #cp_parent optgroup[value='3']").append("<option value='" + this.id + "'>" + this.name + "</option>");
                    break;
            }
        });

        $("#admin_zone #ep_parent").trigger("chosen:updated");
        $("#admin_zone #cp_parent").trigger("chosen:updated");
    }, "json");
}

//EDITAR PRODUTO

$("#admin_zone").on("click", ".item_edit_button", function()
{
    var item_id = $(this).data("product_id");
    $("#admin_zone #edit_product_modal").data("product_id", item_id);
    $("#admin_zone #ep_name").val();


    var Table_child_product = $('#admin_zone #child_product_datatable').dataTable({
        "aaSorting": [[0, "desc"]],
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/admin.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "listar_produtos_to_datatable_by_parent"},
            {"name": "parent", "value": item_id});
        },
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Venda individual"}, {"sTitle": "Max requisições mês"}, {"sTitle": "Max requisições semana"}, {"sTitle": "Categoria"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });



    $.post('ajax/admin.php', {action: "listar_produto", id: $(this).data("product_id")},
    function(data)
    {
        $("#admin_zone #ep_name").val(data.name);

        $("#admin_zone #ep_parent optgroup option[value='" + data.parent + "']").prop("selected", true).trigger("chosen:updated");

        if (data.alone == "1")
            $("#ep_alone").prop("checked", true);
        else
            $("#ep_alone").prop("checked", false);
        $("#admin_zone #ep_mrm").val(data.max_req_m);
        $("#admin_zone #ep_mrw").val(data.max_req_w);
        $("#admin_zone #ep_category").val(data.category);
        $("#admin_zone .chosen-container").css("width", "250px");
        $("#admin_zone #edit_product_modal").modal("show");
    }, "json");

});
$("#admin_zone #edit_product_button").click(function()
{
    if ($("#admin_zone #edit_product_form").validationEngine("validate"))
    {
        $.post('ajax/admin.php', {action: "editar_produto",
            id: $("#admin_zone #edit_product_modal").data("product_id"),
            name: $("#admin_zone #ep_name").val(),
            parent: $("#admin_zone #ep_parent option:selected").val(),
            alone: $("#admin_zone #ep_alone").is(":checked") ? 1 : 0,
            max_req_m: $("#admin_zone #ep_mrm").val(),
            max_req_w: ($("#admin_zone #ep_mrm").val() > $("#admin_zone #ep_mrw").val()) ? $("#admin_zone #ep_mrw").val() : $("#admin_zone #ep_mrm").val(),
            category: $("#admin_zone #ep_category").val()
        }, function() {
            get_info();
            $("#admin_zone #edit_product_modal").modal("hide");
        }, "json");
    }
});
//---------------------------------------------------------------------------------------------

//REMOVER PRODUTO

$("#admin_zone").on("click", ".item_delete_button", function()
{
    $("#admin_zone  #remove_product_modal").data("product_id", $(this).data("product_id")).modal("show");
});

$("#admin_zone #remove_product_button").click(function()
{
    $.post('ajax/admin.php', {action: "apagar_produto",
        id: $("#admin_zone #remove_product_modal").data("product_id")

    }, function() {
        get_info();
        $("#admin_zone #remove_product_modal").modal("hide");
    }, "json");
});

//remover todos
$("#admin_zone #removeAll_product_modal_button").click(function()
{
    $("#admin_zone #removeAll_product_modal").modal("show");
});

$("#admin_zone #removeAll_product_button").click(function()
{
    $.post('ajax/admin.php', {action: "apagar_produtos"}, function() {
        get_info();
        $("#admin_zone #removeAll_product_modal").modal("hide");
    }, "json");
});

//-------------------------------------------------------------------------------------
// CRIAR PRODUTO

$("#admin_zone #create_product_modal_button").click(function()
{
    $("#admin_zone #create_product_modal").modal("show");
    $("#admin_zone .chosen-container").css("width", "250px");
});


$("#admin_zone #create_product_button").click(function()
{

    if ($("#admin_zone #create_product_form").validationEngine("validate"))
    {
        $.post('ajax/admin.php', {action: "criar_produto",
            name: $("#admin_zone #cp_name").val(),
            parent: $("#admin_zone #cp_parent option:selected").val(),
            alone: $("#admin_zone #cp_alone").is(":checked"),
            max_req_m: $("#admin_zone #cp_mrm").val(),
            max_req_w: $("#admin_zone #cp_mrw").val(),
            category: $("#admin_zone #cp_category").val()

        }, function() {
            get_info();
            $("#admin_zone #create_product_modal").modal("hide");
        }, "json");
    }
});

///------------------------------------------



// CLIENTES
// relatorio de clientes sem marcação
$("#admin_zone #download_csm_button").click(function()
{
    $("#admin_zone #download_csm_div").toggle("blind");
});
$("#admin_zone #download_excel_csm_button").click(function()
{
    if ($("#admin_zone #date_form").validationEngine("validate"))
    {
        document.location.href = "ajax/admin.php?action=download_excel_csm";
    }
});
  