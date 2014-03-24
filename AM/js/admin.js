var product;

$(function()
{
    $("#admin_zone .form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
    $("#admin_zone .check_form").validationEngine();

    //get_info_product();

    get_files_to_anexo();
    //ENCOMENDAS--------------------------------------------------------------------------------------
    var config = new Object();
    requisition1 = new requisition($("#admin_extra_zone"), config);

    requisition1.init();
    requisition1.get_current_requisitions($("#view_requisition_datatable"), 1);

    config = new Object();
    requests = new requests_class($("#admin_extra_zone"), config);
    requests.init_apoio_marketing();
    requests.get_apoio_marketing_to_datatable($("#admin_zone #pedidos_apoio_marketing_datatable"));
    requests.init_relatorio_correio();
    requests.get_relatorio_correio_to_datatable($("#admin_zone #pedidos_correio_datatable"));
    requests.init_relatorio_frota();
    requests.get_relatorio_frota_to_datatable($("#admin_zone #pedidos_frota_datatable"));

    config = new Object();
    
    product = new products(config);

    $("#admin_zone .chosen-select").chosen({no_results_text: "Sem resultados"});
    $.post('ajax/admin.php', {action: "get_agentes"},
    function(data)
    {
        var options = "";
        $.each(data, function()
        {
            options += "<option value='" + this.user + "'>" + this.full_name + "</option>";
        });
        $("#admin_zone #select_agent_transfer1,#select_agent_transfer2").append(options).trigger("chosen:updated").css("width", "225px");

    }, "json");
    get_info_product();
});


$("#admin_zone .check_form").submit(function(e)
{
    e.preventDefault();
});

//OpçÔES TOGGLE
$("#admin_zone .button_toggle_divs").click(function()
{

    $(this).parent().parent().parent().find(".div_admin_edit").toggle("blind");
    $(this).toggleClass("icon-chevron-down").toggleClass("icon-chevron-up");
});

//------------



function get_info_product()
{
    product.init_to_datatable($("#admin_zone #view_product_datatable"), $("#admin_zone #edit_product_div"),$("#admin_zone #edit_product_modal"));
}
//AGENTES------------------------------------------------------------------------------------------------------------------------------
$("#admin_zone #agent_marc_transfer_button").click(function()
{
    $("#admin_zone #agent_marc_transfer_modal").modal("show");
});

//botao de confirmação de transferencia de marcaçoes
$("#admin_zone #confirm_marc_transfer_button").click(function()
{
    $.post('ajax/admin.php', {action: "transferir_marcaçao_caledario", new_user: $("#admin_zone #select_agent_transfer1 option:selected").val(), old_user: $("#admin_zone #select_agent_transfer2 option:selected").val()},
    function(data)
    {
        $.jGrowl("Transferencia de " + data + " marcações de " + $("#admin_zone #select_agent_transfer1 option:selected").text() + " para " + $("#admin_zone #select_agent_transfer2 option:selected").text() + " realizada com sucesso", {life: 8000});
    }, "json");
});
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
        $("#admin_zone #ep_parent optgroup option[id='" + data.parent + "']").prop("selected", true).trigger("chosen:updated");
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
            get_info_product();
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
        get_info_product();
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
        get_info_product();
        $("#admin_zone #removeAll_product_modal").modal("hide");
    }, "json");
});
//-------------------------------------------------------------------------------------
// CRIAR PRODUTO

$("#admin_zone #create_product_modal_button").click(function()
{




    product.init_new_product($("#admin_zone #create_product_div"), function() {
        $("#admin_zone #create_product_modal").modal("hide");
    });
    $("#admin_zone #create_product_modal").modal("show");
    $("#admin_zone .chosen-container").css("width", "250px");



});

///------------------------------------------



// CLIENTES--------CLIENTES-----------------------CLIENTES-------------------CLIENTES--------------------------CLIENTES----------------------CLIENTES-----------------------CLIENTES------------------------
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

//ENCOMENDAS ---------------ENCOMENDAS--------------ENCOMENDAS--------------------------ENCOMENDAS-------------ENCOMENDAS----------------ENCOMENDAS---------------------ENCOMENDAS--------------------------------------
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

//ANEXOS-----------------ANEXOS--------------------------------ANEXOS--------------------------------------ANEXOS-------------------------------ANEXOS------------------------ANEXOS---------


//FILE UPLOAD
$("#admin_zone #file_upload").change(function()
{
    // var re_ext = new RegExp("(gif|jpeg|jpg|png|pdf)", "i");
    var file = this.files[0];
    var name = file.name;
    var size = (Math.round((file.size / 1024 / 1024) * 100) / 100);
    var type = file.type;
    if (size > 10) {
        $("#label_anexo_info").text("O tamanho do ficheiro ultrapassa os 10mb permitidos.");
        $(this).fileupload('clear');
    }
    /*  if (!re_ext.test(type)) {
     $("#label_ipl_info").text("A extensão do ficheiro seleccionado não é valida.");
     $(this).fileupload('clear');
     }*/
    $("#label_ipl_info").text("");
});
$("#admin_zone #anexo_upload_button").click(function(e)
{
    e.preventDefault();
    var form = $("#anexo_input_form");

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
            $("#label_anexo_info").text(data.responseText);
            $("#anexo_file_select").empty();
            get_files_to_anexo();
        },
        contentType: false,
        processData: false
    });
});



function get_files_to_anexo()
{
    $.post('ajax/upload_file.php', {action: "get_anexos"}, function(data)
    {
        $("#select_uploaded_files").empty();

        $.each(data, function()
        {
            $("#select_uploaded_files").append("<option value='" + this.toString() + "'>" + this.toString() + "</option>");
        });
    }, "json");
}
$("#admin_zone #remove_uploaded_file").on("click", function()
{
    $.post("ajax/upload_file.php", {action: "delete", name: $("#select_uploaded_files option:selected").val()}, function(data) {
        $("#label_anexo_info").text(data);
        get_files_to_anexo();
    });
});

//PEDIDOS---------------PEDIDOS------------------------PEDIDOS-------------------------------PEDIDOS----------------------PEDIDOS-----------------------------PEDIDOS-----------------------PEDIDOS------------------------
