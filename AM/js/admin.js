

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
});

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

$("#admin_zone #button_filtro_requisition").click(function()
{
    $("#admin_zone #requisition_master_div").toggle("blind");
    $(this).toggleClass("icon-chevron-down").toggleClass("icon-chevron-up");
});

//------------




function get_info()
{

    //PRODUTOS-----------------------------------------------------------------------------------------------------------
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
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Parente"}, {"sTitle": "Max requisições mensais"}, {"sTitle": "Max requisições especiais"}, {"sTitle": "Categoria"}, {"sTitle": "Tipo"}, {"sTitle": "Opções"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
    $.post('ajax/admin.php', {action: "listar_produtos"},
    function(data)
    {
        $("#admin_zone #ep_parent").empty().append("<option value='0'>Escolha um parente</option>");
        $("#admin_zone #cp_parent").empty().append("<option value='0'>Sem Parente</option>");
        var
                temp = "<optgroup value='1' label='Aparelhos'></optgroup>\n\
<optgroup value='2' label='Pilhas'></optgroup>\n\
<optgroup value='3' label='Peças'></optgroup>",
                aparelho = [],
                pilha = [],
                peça = [];
        $("#admin_zone #ep_parent").append(temp);
        $("#admin_zone #cp_parent").append(temp);
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
        $("#admin_zone #ep_parent").find("optgroup[value='1']").append(aparelho).end()
                .find("optgroup[value='2']").append(pilha).end()
                .find("optgroup[value='3']").append(peça).end().trigger("chosen:updated");
        $("#admin_zone #cp_parent").find("optgroup[value='1']").append(aparelho).end()
                .find("optgroup[value='2']").append(pilha).end()
                .find("optgroup[value='3']").append(peça).end().trigger("chosen:updated");
    }, "json");
    //ENCOMENDAS--------------------------------------------------------------------------------------
     
    requisition1 = new requisition($("#admin_extra_zone"));

    requisition1.init();
    requisition1.get_current_requisitions($("#view_requisition_datatable"),1 );
}
//PRODUTOS--------------------------------------------------------------------------------------------------------------------------------------------------
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
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Max requisições mensais"}, {"sTitle": "Max requisições especiais"}, {"sTitle": "Categoria"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
    $.post('ajax/admin.php', {action: "listar_produto", id: $(this).data("product_id")},
    function(data)
    {
        $("#admin_zone #ep_name").val(data.name);
        $("#admin_zone #ep_parent optgroup option[value='" + data.parent + "']").prop("selected", true).trigger("chosen:updated");
        $("#admin_zone #ep_mrm").val(data.max_req_m);
        $("#admin_zone #ep_mrw").val(data.max_req_s);
        $("#admin_zone #ep_category option[value='" + data.category + "']").prop("selected", true);
        $("#admin_zone #ep_type option[value='" + data.type + "']").prop("selected", true);
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
            max_req_m: $("#admin_zone #ep_mrm").val(),
            max_req_s: ($("#admin_zone #ep_mrw").val() > $("#admin_zone #ep_mrm").val()) ? $("#admin_zone #ep_mrm").val() : $("#admin_zone #ep_mrw").val(),
            category: $("#admin_zone #ep_category").val(),
            type: $("#admin_zone #ep_type option:selected").val()
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
            max_req_m: $("#admin_zone #cp_mrm").val(),
            max_req_s: $("#admin_zone #cp_mrw").val(),
            category: $("#admin_zone #cp_category").val(),
            type: $("#admin_zone #cp_type option:selected").val()

        }, function() {
            get_info();
            $("#admin_zone #create_product_modal").modal("hide");
        }, "json");
    }
});
///------------------------------------------



// CLIENTES-------------------------------------------------------------------------------------------------------------------------------------------------
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

//ENCOMENDAS -----------------------------------------------------------------------------------------------------------------------------------------------
//VER PRODUTOS DE ENCOMENDAS FEITAS
$("#admin_zone").on("click", ".ver_requisition_products", function()
{
    $.post('ajax/requisition.php', {action: "listar_produtos_por_encomenda",
        id: $(this).val()}, function(data)
    {
        $("#admin_zone #ver_product_modal #show_requisition_products_tbody").empty();
        $.each(data, function()
        {
            $("#admin_zone #ver_product_modal #show_requisition_products_tbody").append("<tr><td>" + this.name + "</td><td>" + this.category + "</td><td>" + this.quantity + "</td></tr>");
        })
        $("#admin_zone #ver_product_modal").modal("show");
    },
            "json");



});

$("#admin_zone").on("click", ".accept_requisition", function()
{
    var this_button = $(this);
    $.post('ajax/requisition.php', {action: "accept_requisition", id: $(this).val()}, function() {
        this_button.parent("div").parent("td").prev().text("Aprovado");
    }, "json");
});

$("#admin_zone").on("click", ".decline_requisition", function()
{
    var this_button = $(this);
    $.post('ajax/requisition.php', {action: "decline_requisition", id: $(this).val()}, function() {
        this_button.parent("div").parent("td").prev().text("Rejeitado");
    }, "json");
});