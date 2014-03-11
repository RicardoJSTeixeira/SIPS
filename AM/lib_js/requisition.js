
var requisition = function(basic_path, options_ext)
{


    var me = this;
    this.file_uploaded = false;
    this.config = new Object();
    this.config.mensal = true;
    this.config.especial = true;


    $.extend(true, this.config, options_ext);
    this.basic_path = basic_path;
    var aparelho = [], pilha = [], peça = [], optgroups = [], product = 1;
    this.init = function()
    {
        $.get("/AM/view/requisitions/requisition_modal.html", function(data) {
            me.basic_path.append(data);


        });

    };
    this.get_current_requisitions = function(table_path, show_admin)
    {
        get_encomendas_atuais(table_path, show_admin);
    };

    //NEW REQUISITION------------------------------------------------------------------------------------------------------------------------------------------------
    this.new_requisition = function(new_requisition_zone, current_requisition_zone, lead_id) {
        new_requisition_zone.empty().off();
        $.get("/AM/view/requisitions/new_requisition.html", function(data) {
            new_requisition_zone.append(data);
            new_requisition_zone.find('.fileupload').fileupload();
            if (!me.config.mensal)
            {
                new_requisition_zone.find("#req_m_radio").prop("disabled", true);
            }
            if (!me.config.especial)
            { //Nao se força o selected nem hides no mensal pk por default começa o especial
                new_requisition_zone.find("#req_m_radio").prop("checked", true);
                new_requisition_zone.find("#req_s_radio").prop("disabled", true);
                new_requisition_zone.find("#special_req_div").remove();
            }
            new_requisition_zone.find("#requisition_form").validationEngine();
            new_requisition_zone.find("#requisition_form").show();


            //preencher com lead_id enviado
            if (lead_id)
                new_requisition_zone.find("#new_requisition_lead_id").val(lead_id);

            $.post('ajax/admin.php', {action: "listar_produtos"},
            function(data)
            {
                new_requisition_zone.find("#new_requisition_products").empty();
                aparelho = [];
                pilha = [];
                peça = [];
                optgroups = [];
                optgroups.push("<option value='0'>Escolha um produto</option><optgroup value='1' label='Aparelhos'></optgroup>");
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
        // ADICIONAR E REMOVER ITEMS DA LISTA DE PRODUTOS NA NOVA ENCOMENDA----------------------------
        $(new_requisition_zone).on("click", "#new_requisition_product_add_line", function(e)
        {
            e.preventDefault();
            new_requisition_zone.find(" #new_requisition_product_tbody")
                    .append("<tr id='tr" + product + "'>\n\
                                <td ><select data-linha_id=" + product + " id='product_select" + product + "' class='chosen-select new_requisition_select_product'></select></td>\n\
                                <td ><span id='product_span_max" + product + "'></span></td>\n\
                                <td> <input class='input-mini validate[required,custom[onlyNumberSp]]' id='product_input" + product + "' type='number' min='1' value='1'></td>\n\
                                <td><button class='btn  remove_item_requisition_table' value='" + product + "'><i  class='icon-remove'></i> </button></td></tr>");
            new_requisition_zone.find(" #product_select" + product).chosen({no_results_text: "Sem resultados"}).append(optgroups);
            new_requisition_zone.find(" #product_select" + product)
                    .find("optgroup[value='1']").append(aparelho).end()
                    .find("optgroup[value='2']").append(pilha).end()
                    .find("optgroup[value='3']").append(peça).end().trigger("chosen:updated").trigger("change");
            product += 1;
        });
        $(new_requisition_zone).on("change", ".new_requisition_select_product", function()
        {
            if (new_requisition_zone.find(" #req_m_radio").is(":checked"))
            {
                new_requisition_zone.find(" #product_span_max" + $(this).data().linha_id).text($(this).find("option:selected").data().max_month);
                new_requisition_zone.find(" #product_input" + $(this).data().linha_id).attr("max", ($(this).find("option:selected").data().max_month));
                if (new_requisition_zone.find(" #product_input" + $(this).data().linha_id).val() > $(this).find("option:selected").data().max_month)
                    new_requisition_zone.find(" #product_input" + $(this).data().linha_id).val($(this).find("option:selected").data().max_month);
            }
            else
            {
                new_requisition_zone.find(" #product_span_max" + $(this).data().linha_id).text($(this).find("option:selected").data().max_special);
                new_requisition_zone.find(" #product_input" + $(this).data().linha_id).attr("max", ($(this).find("option:selected").data().max_special));
                if (new_requisition_zone.find(" #product_input" + $(this).data().linha_id).val() > $(this).find("option:selected").data().max_special)
                    new_requisition_zone.find(" #product_input" + $(this).data().linha_id).val($(this).find("option:selected").data().max_special);
            }
            update_product_selects();
        });
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
                    produtos.push({"id": this_option_selected.attr("id"), "quantity": $(this).find("input[type='number']").val()});
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
                    if ((new_requisition_zone.find(".fileupload-preview").text().length > 0 && me.file_uploaded) || new_requisition_zone.find(".fileupload-preview").text().length == 0)
                    {
                        $.post('ajax/requisition.php', {action: "criar_encomenda",
                            type: new_requisition_zone.find(" #req_m_radio").is(":checked") == true ? "month" : "special",
                            lead_id: new_requisition_zone.find(" #new_requisition_lead_id").val(),
                            contract_number: new_requisition_zone.find(" #new_requisition_contract").val(),
                            attachment: new_requisition_zone.find(".fileupload-preview").text(),
                            products_list: produtos},
                        function() {
                            if (current_requisition_zone !== undefined && current_requisition_zone !== 0 && current_requisition_zone != false)
                                get_encomendas_atuais(current_requisition_zone);
                            $.jGrowl('Encomenda realizada com sucesso', {life: 4000});
                            new_requisition_zone.find(" #new_requisition_lead_id").val("");
                            new_requisition_zone.find(" #new_requisition_contract").val("");
                            new_requisition_zone.find(" #new_requisition_product_tbody").empty();
                            new_requisition_zone.find("#req_s_radio").trigger("change").prop("checked", true);
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
            new_requisition_zone.find(" #new_requisition_product_tbody tr[id='tr" + $(this).val() + "']").empty();
        });
        $(new_requisition_zone).on("change", "input[name='req_radio']", function()
        {
            new_requisition_zone.find(" .new_requisition_select_product").trigger("change");
            if ($(this).val() == 2)
                new_requisition_zone.find(" #special_req_div").show("blind");
            else
            {
                $.post('ajax/requisition.php', {action: "check_month_requisitions",
                    id: $(this).val()}, function(data)
                {
                    if (data > 0)
                    {
                        $.jGrowl('Já efectuou pelo menos 1 encomenda mensal este mês', {life: 4000});
                        new_requisition_zone.find("#req_s_radio").prop("checked", true);
                    }
                    else
                        new_requisition_zone.find("#special_req_div").hide("blind");
                }, "json");
            }
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
    };//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  
//-------------------------------------------------------------------------------------




    //EXTRA FUNCTIONS---------------------------------------------------------
    function  update_product_selects()
    {

        var selected_options = [];
        $("#new_requisition_product_tbody select option").prop("disabled", false);
        $.each($("#new_requisition_product_tbody select"), function()
        {
            selected_options.push($(this).find("option:selected").attr("id"));
        });

        $.each(selected_options, function()
        {
            $("#new_requisition_product_tbody select option[id='" + this + "']").prop("disabled", true);

        });


        $("#new_requisition_product_tbody select").trigger("chosen:updated");
    }

    function get_encomendas_atuais(table_path, show_admin)
    {

        var Table_view_requisition = table_path.dataTable({
            "aaSorting": [[4, "desc"]],
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/requisition.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "listar_requisition_to_datatable"}, {"name": "show_admin", "value": show_admin});
            },
            "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Tipo"}, {"sTitle": "Id Cliente"}, {"sTitle": "Data"}, {"sTitle": "Número de contrato"}, {"sTitle": "Anexo"},  {"sTitle": "Produtos"},{"sTitle": "Status"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });

        //VER PRODUTOS DE ENCOMENDAS FEITAS
        table_path.on("click", ".ver_requisition_products", function()
        {
            $.post('ajax/requisition.php', {action: "listar_produtos_por_encomenda",
                id: $(this).val()}, function(data)
            {
                $(" #ver_product_modal #show_requisition_products_tbody").empty();
                $.each(data, function()
                {
                    $(" #ver_product_modal #show_requisition_products_tbody").append("<tr><td>" + this.name + "</td><td>" + this.category + "</td><td>" + this.quantity + "</td></tr>");
                });

                $("#ver_product_modal").modal("show");
            },
                    "json");
        });
    }








};