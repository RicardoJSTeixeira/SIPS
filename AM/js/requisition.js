var aparelho = [], pilha = [], peça = [], optgroups = [];
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
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Nome"}, {"sTitle": "Parente"}, {"sTitle": "Venda individual"}, {"sTitle": "Max requisições mês"}, {"sTitle": "Max requisições semana"}, {"sTitle": "Categoria"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
    var Table_view_requisition = $('#main_requisition #view_requisition_datatable').dataTable({
        "bSortClasses": false,
        "bProcessing": true,
        "bDestroy": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sAjaxSource": 'ajax/requisition.php',
        "fnServerParams": function(aoData) {
            aoData.push({"name": "action", "value": "listar_requisition_to_datatable"});
        },
        "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Tipo"}, {"sTitle": "Lead_id"}, {"sTitle": "Data"}, {"sTitle": "Número de contrato"}, {"sTitle": "Anexo"}, {"sTitle": "Produtos"}],
        "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
    });
    $.post('ajax/admin.php', {action: "listar_produtos"},
    function(data)
    {
        $("#main_requisition #new_requisition_products").empty();
        optgroups.push("<optgroup value='1' label='Aparelhos'></optgroup>");
        optgroups.push(" < optgroup value = '2' label = 'Pilhas' > < /optgroup>");
        optgroups.push("< optgroup value = '3' label = 'Peças' > < /optgroup>");
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

    }, "json");
});
$("#main_requisition #new_requisition_product_add_line").on("click", function()
{
    $("#main_requisition #new_requisition_product_tbody").append("<tr><td></td></tr>")
    



//things to add after
    $("#main_requisition #new_requisition_products").find("optgroup[value='1']").append(aparelho).end()
            .find("optgroup[value='2']").append(pilha).end()
            .find("optgroup[value='3']").append(peça).end().trigger("chosen:updated");
    $("#main_requisition .chosen-select").chosen({no_results_text: "Sem resultados"});
})


$("#main_requisition  input[name='req_radio']").on("change", function()
{
    if ($(this).val() == 2)
        $("#main_requisition #special_req_div").show("blind");
    else
        $("#main_requisition #special_req_div").hide("blind");
});
$("#main_requisition #new_requisition_submit_button").click(function()
{
    if ($("#main_requisition #requisition_form").validationEngine("validate"))
    {
        $.post('ajax/requisition.php', {action: "criar_encomenda",
            type: $("#main_requisition #req_m_radio").is(":checked") == true ? "month" : "special",
            lead_id: $("#main_requisition #new_requisition_lead_id").val(),
            contract_number: $("#main_requisition #new_requisition_contract").val(),
            attachment: "aaaaaa",
            products_list: $("#main_requisition #new_requisition_products").val()},
        "json");
    }
});
$("#main_requisition #requisition_form").on("submit", function(e)
{
    e.preventDefault();
});