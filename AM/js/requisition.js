var aparelho = [], pilha = [], peça = [], optgroups = [], product = 1;
$(function()
{



    $("#main_requisition #requisition_form").validationEngine();
    var Table_view_product = $('#main_requisition #view_product_datatable').dataTable({
        "aaSorting": [[6, "asc"]],
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/requisition.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "listar_produtos_to_datatable"});
        },
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Parente"}, {"sTitle": "Max requisições mensais"}, {"sTitle": "Max requisições especiais"}, {"sTitle": "Categoria"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
    get_encomendas_atuais();
    $.post('ajax/admin.php', {action: "listar_produtos"},
    function(data)
    {
        $("#main_requisition #new_requisition_products").empty();
        aparelho = [];
        pilha = [];
        peça = [];
        optgroups = [];
        optgroups.push("<optgroup value='1' label='Aparelhos'></optgroup>");
        optgroups.push("<optgroup value = '2' label = 'Pilhas' > </optgroup>");
        optgroups.push("<optgroup value = '3' label = 'Peças' > </optgroup>");
        $.each(data, function()
        {

            switch (this.category)
            {
                case "Aparelho":
                    aparelho.push("<option data-max_month=" + this.max_req_m + " data-max_special=" + this.max_req_s + "  id=" + this.id + ">" + this.name + "</option>");
                    break;
                case "Pilha":
                    pilha.push("<option data-max_month=" + this.max_req_m + " data-max_special=" + this.max_req_s + " id=" + this.id + ">" + this.name + "</option>");
                    break;
                case "Peça":
                    peça.push("<option data-max_month=" + this.max_req_m + " data-max_special=" + this.max_req_s + " id=" + this.id + ">" + this.name + "</option>");
                    break;
            }
        });

    }, "json");
});




// ADICIONAR E REMOVER ITEMS DA LISTA DE PRODUTOS NA NOVA ENCOMENDA
$("#main_requisition #new_requisition_product_add_line").on("click", function(e)
{
    e.preventDefault();
    $("#main_requisition #new_requisition_product_tbody")
            .append("<tr id='tr" + product + "'>\n\
                                <td ><select data-linha_id=" + product + " id='product_select" + product + "' class='chosen-select new_requisition_select_product'></select></td>\n\
                                <td ><span id='product_span_max" + product + "'></span></td>\n\
                                <td> <input class='input-mini' id='product_input" + product + "' type='number' min='1' value='1'></td>\n\
                                <td><button class='btn  remove_item_requisition_table' value='" + product + "'><i  class='icon-remove'></i> </button></td></tr>");

    $("#main_requisition #product_select" + product).chosen({no_results_text: "Sem resultados"}).append(optgroups);

    $("#main_requisition #product_select" + product)
            .find("optgroup[value='1']").append(aparelho).end()
            .find("optgroup[value='2']").append(pilha).end()
            .find("optgroup[value='3']").append(peça).end().trigger("chosen:updated").trigger("change");


    product += 1;
});



$("#main_requisition").on("change", ".new_requisition_select_product", function()
{
    if ($("#main_requisition #req_m_radio").is(":checked"))
    {
        $("#main_requisition #product_span_max" + $(this).data().linha_id).text($(this).find("option:selected").data().max_month);
        $("#main_requisition #product_input" + $(this).data().linha_id).attr("max", ($(this).find("option:selected").data().max_month));
        if ($("#main_requisition #product_input" + $(this).data().linha_id).val() > $(this).find("option:selected").data().max_month)
            $("#main_requisition #product_input" + $(this).data().linha_id).val($(this).find("option:selected").data().max_month);
    }
    else
    {
        $("#main_requisition #product_span_max" + $(this).data().linha_id).text($(this).find("option:selected").data().max_special);
        $("#main_requisition #product_input" + $(this).data().linha_id).attr("max", ($(this).find("option:selected").data().max_special));
        if ($("#main_requisition #product_input" + $(this).data().linha_id).val() > $(this).find("option:selected").data().max_special)
            $("#main_requisition #product_input" + $(this).data().linha_id).val($(this).find("option:selected").data().max_special);
    }
});


$("#main_requisition").on("click", ".remove_item_requisition_table", function()
{
    $("#main_requisition #new_requisition_product_tbody tr[id='tr" + $(this).val() + "']").empty();
});
//-------------------------------------------------------------------------------------


$("#main_requisition  input[name='req_radio']").on("change", function()
{
    $("#main_requisition .new_requisition_select_product").trigger("change");


    if ($(this).val() == 2)
        $("#main_requisition #special_req_div").show("blind");
    else
    {
        $.post('ajax/requisition.php', {action: "check_month_requisitions",
            id: $(this).val()}, function(data)
        {
            if (data > 0)
            {
                $.jGrowl('Ja efectuou pelo menos 1 encomenda mensal este mês', {life: 4000});
                $("#main_requisition  #req_s_radio").prop("checked", true);
            }
            else
                $("#main_requisition #special_req_div").hide("blind");
        }, "json");
    }
});
$("#main_requisition #new_requisition_submit_button").click(function()
{
    var produtos = [];
    var count = 0;
    $.each($("#main_requisition #new_requisition_product_tbody tr"), function()
    {
        count++;
        produtos.push({"id": $(this).find("select").find("option:selected").attr("id"), "quantity": $(this).find("input[type='number']").val()});
    });





    if (!count)
        $.jGrowl('Escolha produtos para encomendar', {life: 4000});
    else
    {
        if ($("#main_requisition #requisition_form").validationEngine("validate"))
        {
            $.post('ajax/requisition.php', {action: "criar_encomenda",
                type: $("#main_requisition #req_m_radio").is(":checked") == true ? "month" : "special",
                lead_id: $("#main_requisition #new_requisition_lead_id").val(),
                contract_number: $("#main_requisition #new_requisition_contract").val(),
                attachment: "aaaaaa",
                products_list: produtos},
            function() {
                $("#main_requisition #button_filtro_new_requisition").trigger("click");
                get_encomendas_atuais();
                $.jGrowl('Encomenda realizada com sucesso', {life: 4000});
                $("#main_requisition #new_requisition_lead_id").val("");
                $("#main_requisition #new_requisition_contract").val("");
                $("#main_requisition #new_requisition_product_tbody").empty();
            }, "json");
        }
    }
});

//VER PRODUTOS DE ENCOMENDAS FEITAS
$("#main_requisition").on("click", ".ver_requisition_products", function()
{
    $.post('ajax/requisition.php', {action: "listar_produtos_por_encomenda",
        id: $(this).val()}, function(data)
    {
        $("#main_requisition #ver_product_modal #show_requisition_products_tbody").empty();
        $.each(data, function()
        {
            $("#main_requisition #ver_product_modal #show_requisition_products_tbody").append("<tr><td>" + this.name + "</td><td>" + this.category + "</td><td>" + this.quantity + "</td></tr>")
        })
        $("#main_requisition #ver_product_modal").modal("show");
    },
            "json");



});





//EXTRA FUNCTIONS---------------------------------------------------------
$("#main_requisition #requisition_form").on("submit", function(e)
{
    e.preventDefault();
});

$("#main_requisition #button_filtro_new_requisition").click(function()
{
    $("#main_requisition #new_requisition_div").toggle("blind");
    $(this).toggleClass("icon-chevron-down").toggleClass("icon-chevron-up");
});



function get_encomendas_atuais()
{
    var Table_view_requisition = $('#main_requisition #view_requisition_datatable').dataTable({
          "aaSorting": [[4, "desc"]],
        "bSortClasses": false,
        "fnDrawCallback": function(oSettings) {
            $("#main_requisition .accept_requisition").hide();
            $("#main_requisition .decline_requisition").hide();
        },
        "bProcessing": true,
        "bDestroy": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/requisition.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "listar_requisition_to_datatable"});
        },
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Tipo"}, {"sTitle": "Id Cliente"}, {"sTitle": "Data"}, {"sTitle": "Número de contrato"}, {"sTitle": "Anexo"}, {"sTitle": "Status"}, {"sTitle": "Produtos"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
}
