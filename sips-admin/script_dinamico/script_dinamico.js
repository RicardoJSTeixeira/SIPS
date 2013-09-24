

////////TO BE DONE//////////////////

//relatorio

//tratar das rules quando o elemento n as tem
//schedule ta cabenenariazado

//
//forms ->http://www.javaworld.com/jw-06-1996/jw-06-javascript.html?page=2


/*
 demo limesurvey
 admin
 test
 */
// NOS COMMITS!------------------------
// Issue #13

///////NAO FAZER COMMIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!



//falta required ou metodo de validação nas regras quando os selects por vezes vao vazios

//duplicar script - falta regras, dependente da criação das tags
//tags individuais por scripts-working


var temp_value_holder = "";
var current_page_pos = 0;
var selected_id = 0;
var selected_tag = 0; 
var selected_type = "";
var array_id = [];
var regex_remove_blank = /^\s*$[\n\r]{1,}/gm; 
var regex_replace_textbox_tag = /[^a-zA-Z0-9éÉçÇãÃâÂóÓõÕáÁàÀíÍêÊúÚôÔºª\_\s:,?\/\-\.\,()@§]/g;
var regex_replace_textbox = /[^a-zA-Z0-9éÉçÇãÃâÂóÓõÕáÁàÀíÍêÊúÚôÔºª\_\s:,?\/\-\.\,(),]/g;
var regex_replace = /[^a-zA-Z0-9éÉçÇãÃâÂóÓõÕáÁàÀíÍêÊúÚôÔºª\_\s:,?\/\-\.\,()]/g;
var regex_text = /[^a-zA-Z0-9éÉçÇãÃâÂóÓõÕáÁàÀíÍêÊúÚôÔºª\_\s:,?\/\-\.\,()@§]/g;
var regex_split = /\n/g;


//mostra/esconde os elementos associados ao edit
function editor_toggle(tipo)
{

      $(".item").removeClass("helperPick");
      $("#tabs").tabs("option", "active", 0);
      if (tipo === "on")
      {
            $("#tabs").tabs("enable");
            $("#item_edit_comum").show();
            $("#rule_manager").show();
            $("#open_rule_creator").prop('disabled', false);//botoes de edit
            $(".editor_layout").hide();// esconde os edits de todos
            $(".footer_save_cancel button").prop('disabled', false);//botoes de edit
            $(".chosen-select").chosen({no_results_text: "Sem resultados"});
      }
      if (tipo === "off")
      {
            $("#tabs").tabs("disable", 1);
            $("#tabs").tabs("disable", 2);
            $("#item_edit_comum").hide();
            $("#rule_manager").hide();
            $("#open_rule_creator").prop('disabled', true);
            $(".editor_layout").hide();
            $(".footer_save_cancel button").prop('disabled', true);
      }
}


//FOOTER EDIT BUTTONS
$("#cancel_edit").click(function()
{
      editor_toggle("off");
});
$("#save_edit").click(function()
{
      editor_toggle("off");
      edit_element(selected_type, $("#" + selected_id), 0);
});
$("#tags_select").change(function()
{
      $("#tag_label").text("§" + $(this).val() + "§");
});
$("#regra_select").change(function()
{
      if ($(this).val() === "goto")
      {
            $("#go_to_div").show();
            $(".rule_target").hide();
      } else
      {
            $("#go_to_div").hide();
            $(".rule_target").show();
      }
});

$("#checkbox_scheduler_all").click(function()
{

      if (this.checked)
            $("#scheduler_edit_select option").prop("selected", true);

      else
            $("#scheduler_edit_select option").prop("selected", false);

      $("#scheduler_edit_select").trigger("liszt:updated");
});


//file:///home/ricardo/Desktop/bootstrap-wysiwyg-master/index.html

$(function() {
      $("#tabs").tabs();
      array_id["radio"] = 0;
      array_id["checkbox"] = 0;
      $.get("items.html", function(data) {
            $("#rigth_list").html(data);
            $(".rightDiv .item").draggable({
                  helper: function(ev, ui) {
                        return "<span class='helperPick'>" + $(this).html() + "</span>";
                  },
                  connectToSortable: ".leftDiv"
            });
            var removeIntent = false;
            $(".leftDiv").sortable({
                  'items': ".item",
                  over: function() {
                        removeIntent = false;
                  },
                  out: function() {
                        removeIntent = true;
                  },
                  beforeStop: function(event, ui) {
                        if (removeIntent == true) {
                              item_database("delete_item", $(this).data().uiSortable.currentItem.attr("id"), 0, 0, 0, 0, $(this).data().uiSortable.currentItem.index(), 0, 0, 0, 0, 0, 0);
                              ui.item.remove();
                              editor_toggle("off");
                              $("#rule_target_select option[value=" + $(this).data().uiSortable.currentItem.attr("id") + "] ").remove();
                              $('#rule_target_select').val('').trigger('liszt:updated');
                        }
                  },
                  update: function(event, ui) {
                        var items = $(".leftDiv  .item");
                        for (var count = 0; count < items.length; count++)
                        {
                              item_database("edit_item_order", items[count].id, 0, 0, 0, 0, $("#" + items[count].id).index(), 0, 0, 0, 0, 0, 0, 0);
                        }
                        editor_toggle("off");
                  },
                  receive: function(event, ui) {

                        if ($(this).data().uiSortable.currentItem.hasClass("texto_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "texto", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .texto_class .label_texto")[0].innerHTML, $(".rightDiv .texto_class .input_texto")[0].placeholder, $(".rightDiv .texto_class .input_texto")[0].maxLength, 0, 0, 0, "normal");
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("pagination_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "pagination", $(this).data().uiSortable.currentItem.index(), "h", 0, 0, 0, 0, 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("radio_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "radio", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .label_radio")[0].innerHTML, 0, 0, ["Valor1"], 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("checkbox_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "checkbox", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .label_checkbox")[0].innerHTML, 0, 0, ["Valor1"], 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("multichoice_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "multichoice", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .label_multichoice")[0].innerHTML, 0, 0, ["Opção1"], 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("textfield_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "textfield", $(this).data().uiSortable.currentItem.index(), "h", "textfield", 0, 0, $(".rightDiv .label_textfield")[0].innerHTML, 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("tableradio_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "tableradio", $(this).data().uiSortable.currentItem.index(), "h", "tableradio", ["mau", "médio", "bom"], 0, ["pergunta1"], 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("legend_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "legend", $(this).data().uiSortable.currentItem.index(), "h", "legend", 0, 0, $(".rightDiv .label_legend")[0].innerHTML, 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("datepicker_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "datepicker", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .label_datepicker")[0].innerHTML, 0, 0, 0, 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("scheduler_class"))
                        {
                              item_database("add_item", 0, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "scheduler", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .label_scheduler")[0].innerHTML, 0, 0, [], 0, 0, 1);
                        }
                        editor_toggle("off");
                  }
            });
            //campaigns and linhas inbound
            $.post("requests.php", {action: "get_campaign"},
            function(data1)
            {
                  $.each(data1, function(index, value) {
                        $("#script_campanha_selector").append("<option value=" + this.id + ">" + this.name + "</option>");
                  });
                  $("#script_campanha_selector").chosen(({no_results_text: "Sem resultados"}));
            }, "json");
            $.post("requests.php", {action: "get_linha_inbound"},
            function(data2)
            {
                  $.each(data2, function(index, value) {
                        $("#script_linha_inbound_selector").append("<option value=" + this.id + ">" + this.name + "</option>");
                  });
                  $("#script_linha_inbound_selector").chosen(({no_results_text: "Sem resultados"}));

//VERIFICAR SÉ É CLOUD
                  $.post("requests.php", {action: "iscloud"},
                  function(data4)
                  {
                        if (data4.iscloud)
                              $(".linha_inbound_div").hide();
                        else
                              $(".linha_inbound_div").show();
                  }, "json");
            }, "json");




            $.post("requests.php", {action: "get_schedule"},
            function(data3)
            {

                  $.each(data3, function(index, value) {

                        $("#scheduler_edit_select").append("<option value=" + this.id + ">" + this.text + "</option>");
                  });
                  $("#scheduler_edit_select").val("").trigger("liszt:updated");
            }, "json");







            //--------------------------------------//
            editor_toggle("off");
            update_script();





      });

      $("#tag_label").text("§nome§");


      $(document).on("click", ".element", function(e) {
            selected_id = $(this).data("id");
            selected_tag = $(this).data("tag");
            selected_type = $(this).data("type");
            editor_toggle("on");
            $(this).addClass("helperPick"); //class HelperPick
            $("#tabs").tabs("option", "active", 1); //tabs

            switch ($(this).data("type"))
            {
                  case "texto":
                        $("#text_layout_editor").show();
                        populate_element("texto", $(this));
                        break;
                  case "radio":
                        $("#radio_layout_editor").show();
                        populate_element("radio", $(this));
                        break;
                  case "checkbox":
                        $("#checkbox_layout_editor").show();
                        populate_element("checkbox", $(this));
                        break;
                  case "multichoice":
                        $("#multichoice_layout_editor").show();
                        populate_element("multichoice", $(this));
                        break;
                  case "textfield":
                        $("#textfield_layout_editor").show();
                        populate_element("textfield", $(this));
                        break;
                  case "legend":
                        $("#legend_layout_editor").show();
                        populate_element("legend", $(this));
                        break;
                  case "tableradio":
                        $("#tableradio_layout_editor").show();
                        populate_element("tableradio", $(this));
                        break;
                  case "pagination":
                        populate_element("pagination", $(this));
                        break;
                  case "datepicker":
                        $("#datepicker_layout_editor").show();
                        populate_element("datepicker", $(this));
                        break;
                  case "scheduler":
                        $("#scheduler_layout_editor").show();
                        populate_element("scheduler", $(this));
                        break;
            }

      });
      $(document).on("click", ".rule_delete_icon", function(e) {
            rules_database("delete_rule", $(this).data("id"), 0, 0, 0, 0, 0, 0, 0, 0);
            rules_database("get_rules_by_trigger", 0, $("#script_selector option:selected").val(), 0, selected_tag, 0, 0, 0, 0, 0);
      });
});
//UPDATES DE INFO


Object.size = function(a)
{
      var count = 0;
      var i;

      for (i in a) {
            if (a.hasOwnProperty(i)) {
                  count++;
            }
      }

      return count;
};
function update_script(callback)
{

      $(".chosen-select").chosen(({no_results_text: "Sem resultados"}));
      $.post("requests.php", {action: "get_scripts"},
      function(data)
      {
            if (!Object.size(data))
            {
                  $("#page_selector_div button").prop('disabled', true);
                  $("#opcao_script_button").prop('disabled', true);
                  $("#script_remove_button").prop('disabled', true);
                  $("#script_selector").empty();
                  $("#page_selector").empty();
                  $(".leftDiv").empty();
            }
            else
            {
                  $("#script_remove_button").prop('disabled', false);
                  $("#page_selector_div button").prop('disabled', false);
                  $("#opcao_script_button").prop('disabled', false);
                  var script = $("#script_selector option:selected").val();
                  $("#script_selector").empty();
                  $.each(data, function(index, value) {
                        if (script == this.id)
                        {
                              $("#script_selector").append("<option value=" + this.id + " selected>" + this.name + "</option>");
                        }
                        else
                              $("#script_selector").append("<option value=" + this.id + ">" + this.name + "</option>");
                  });
                  if (typeof callback === "function")
                        callback();
                  update_pages();
            }




            $.post("requests.php", {action: "get_tag_fields", id_script: $("#script_selector option:selected").val()},
            function(data4)
            {
                  $("#tags_select .tag_group").remove();
                  $.each(data4, function() {
                        $("#tags_select").append("<optgroup class='tag_group' label='" + this.text + "'></optgroup>");
                        var temp = "";
                        $.each(this.lista, function() {
                              temp = temp + "<option value='" + this.value + "'>" + this.name + "</option>";
                        });
                        $("#tags_select optgroup:last").append(temp);
                  });
                  $("#tags_select").val("").trigger("liszt:updated");
            }

            , "json");




      }, "json");
}
function update_pages(callback)
{
      $.post("requests.php", {action: "get_pages", id_script: $("#script_selector option:selected").val()},
      function(data)
      {

            if (!Object.size(data))
            {
                  $("#page_selector").empty();
                  $(".leftDiv").hide();
                  $("#opcao_page_button").prop('disabled', true);
            }
            else
            {
                  $(".leftDiv").show();
                  $("#opcao_page_button").prop('disabled', false);
                  var pag = $("#page_selector").val();
                  $("#page_selector").empty();
                  $("#go_to_select").empty();
                  $("#page_position").empty();
                  $.each(data, function(index, value) {
                        if (pag === this.id)
                              $("#page_selector").append("<option data-pos=" + this.pos + " value=" + this.id + " selected>" + this.name + "</option>");
                        else
                              $("#page_selector").append("<option data-pos=" + this.pos + " value=" + this.id + ">" + this.name + "</option>");

                        $("#page_position").append("<option value=" + this.pos + ">" + this.pos + "</option>");

                        $("#go_to_select").append(new Option(this.name, this.id));
                  });


                  if (typeof callback === "function")
                        callback();

                  update_info();
            }
      }, "json");
}
function update_info()
{

      $(".leftDiv").empty();
      $.post("requests.php", {action: "get_data", id_script: $("#script_selector option:selected").val(), id_page: $("#page_selector option:selected").val()},
      function(data)
      {
            var temp_type="";
            $("#rule_target_select").empty();
            $.each(data, function(index, value) {
                  

                  switch (this.type)
                  {
                        case "texto":
                              temp_type="caixa de texto";
                              var item = $('.rightDiv .texto_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "texto")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden)
                                      .data("regex", this.param1);
                              insert_element("texto", item, this);
                              item.appendTo('.leftDiv');
                                                     break;
                        case "pagination":
                               temp_type="Paginação";
                              var item = $('.rightDiv .pagination_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .data("type", "pagination");
                              insert_element("pagination", item, this);
                              item.appendTo('.leftDiv');
                              break;
                        case "radio":
                               temp_type="Botão radio";
                              var item = $('.rightDiv .radio_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "radio")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden)
                                      .data("dispo", this.dispo);
                              insert_element("radio", item, this);
                              item.appendTo('.leftDiv');
                              break;
                        case "checkbox":
                               temp_type="Botão resposta multipla";
                              var item = $('.rightDiv .checkbox_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "checkbox")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden)
                                      .data("dispo", this.dispo);
                              insert_element("checkbox", item, this);
                              item.appendTo('.leftDiv');
                              break;
                        case "multichoice":
                               temp_type="Lista de Opções";
                              var item = $('.rightDiv .multichoice_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "multichoice")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden);
                              insert_element("multichoice", item, this);
                              item.appendTo('.leftDiv');
                              break;
                        case "textfield":
                               temp_type="Campo de Texto";
                              var item = $('.rightDiv .textfield_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "textfield")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden);
                              insert_element("textfield", item, this);
                              item.appendTo('.leftDiv');
                              break;
                        case "legend":
                               temp_type="Titulo";
                              var item = $('.rightDiv .legend_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "legend")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden);
                              insert_element("legend", item, this);
                              item.appendTo('.leftDiv');
                              break;
                        case "tableradio":
                               temp_type="Tabela botões radio";
                              var item = $('.rightDiv .tableradio_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "tableradio")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden);
                              insert_element("tableradio", item, this);
                              item.appendTo('.leftDiv');
                              break;
                        case "datepicker":
                               temp_type="Seletor tempo e hora";
                              var item = $('.rightDiv .datepicker_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "datepicker")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden);
                              insert_element("datepicker", item, this);
                              item.appendTo('.leftDiv');
                              break;
                        case "scheduler":
                               temp_type="Calendário";
                              var item = $('.rightDiv .scheduler_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("tag", this.tag)
                                      .addClass("element")
                                      .data("type", "scheduler")
                                      .data("required", this.required)
                                      .data("hidden", this.hidden);
                              insert_element("scheduler", item, this);
                              item.appendTo('.leftDiv');
                              break;
                  }
                  $("#rule_target_select").append(new Option(this.tag + " --- " + temp_type, this.tag)); //povoar os alvos com as tags e tipos dos elementos
            });
            $('#rule_target_select').val('').trigger('liszt:updated');
      }, "json");
}


function validate_manual()
{
      return $("#rule_form").validationEngine('validate');
}


function populate_element(tipo, element)
{
      $("#item_edit_comum div").show();
      if (element.data("required"))
            $("#item_required").attr('checked', true);
      else
            $("#item_required").attr('checked', false);
      if (element.data("hidden"))
            $("#item_hidden").attr('checked', true);
      else
            $("#item_hidden").attr('checked', false);
      $("#label_tag").text("@" + element.data("tag") + "@");
      rules_manager(tipo, element);
      $("#tabs").tabs("enable");
      switch (tipo)
      {
            case "texto":
                  $("#texto_edit").val(element.find(".label_geral")[0].innerHTML);
                  $("#placeholder_edit").val(element.find(".input_texto")[0].placeholder);
                  $("#max_length_edit").val(element.find(".input_texto")[0].maxLength);
                  $(".validation input:radio[name='regex_texto'][value=" + element.data("regex") + "]").prop("checked", true);
                  break;
            case "radio":
                  $("#radio_edit").val(element.find(".label_geral")[0].innerHTML);
                  $("#radio_group_edit").val(element.find(":radio")[0].name);
                  var string_elements = "";
                  var element_rlength = element.find(":radio").length;
                  var rname;
                  for (var count = 0; count < element_rlength; count++)
                  {
                        rname = element.find(".radio_name")[count].innerText;
                        if (count == element_rlength - 1)
                              string_elements += rname;
                        else
                              string_elements += rname + "\n";
                        string_elements = string_elements.replace("<span></span>", "");
                  }
                  $("#radio_textarea").val(string_elements);
                  if (element.data("dispo") === "v")
                        $("#vertic_radio").attr('checked', true);
                  else
                        $("#horiz_radio").attr('checked', true);
                  break;
            case "checkbox":
                  $("#checkbox_edit").val(element.find(".label_geral")[0].innerHTML);
                  $("#checkbox_group_edit").val(element.find(":checkbox")[0].name);
                  var string_elements = "";
                  var element_clength = element.find(":checkbox").length;
                  var cname;
                  for (var count = 0; count < element_clength; count++)
                  {
                        cname = element.find(".checkbox_name")[count].innerText;
                        if (count == element_clength - 1)
                              string_elements += cname;
                        else
                              string_elements += cname + "\n";
                        string_elements = string_elements.replace("<span></span>", "");
                  }
                  $("#checkbox_textarea").val(string_elements);
                  if (element.data("dispo") === "v")
                        $("#vertic_checkbox").attr('checked', true);
                  else
                        $("#horiz_checkbox").attr('checked', true);
                  break;
            case "multichoice":
                  $("#multichoice_edit").val(element.find(".label_geral")[0].innerHTML);
                  var string_elements = "";
                  var count = 1;
                  var max = element.find(".multichoice_select option").length;
                  element.find(".multichoice_select option").each(function()
                  {
                        if (count == max)
                              string_elements += $(this).val();
                        else
                              string_elements += $(this).val() + "\n";
                        count++;
                  });
                  $("#multichoice_textarea").val(string_elements);
                  break;
            case "textfield":
                  $("#tag_edit").hide();
                  $("#tabs").tabs("disable", 2);
                  $("#rule_manager").hide();
                  $("#open_rule_creator").prop('disabled', true);
                  $(".required_class").hide();
                  $("#textfield_edit").val(element.find(".label_geral")[0].innerHTML);
                  break;
            case "legend":
                  $("#tag_edit").hide();
                  $("#tabs").tabs("disable", 2);
                  $("#rule_manager").hide();
                  $("#open_rule_creator").prop('disabled', true);
                  $(".required_class").hide();
                  $("#legend_edit").val(element.find(".label_geral")[0].innerHTML);
                  break;
            case "tableradio":
                  $("#tableradio_edit").val(element.find(".label_geral")[0].innerHTML);
                  //titulos->texto
                  var string_elements = "";
                  var head_td = element.find(".tr_head td");
                  for (var count = 1; count < head_td.length; count++)
                  {
                        string_elements += head_td[count].innerHTML + "\n";
                  }
                  $("#tableradio_th_textarea").val(string_elements.slice(0, -1));
                  string_elements = "";
                  var body_tdrow = element.find(".tr_body .td_row");
                  for (var count = 0; count < body_tdrow.length; count++)
                  {
                        string_elements += body_tdrow[count].innerHTML + "\n";
                  }
                  $("#tableradio_td_textarea").val(string_elements.slice(0, -1));
                  break;
            case "datepicker":
                  $("#datepicker_edit").val(element.find(".label_geral")[0].innerHTML);
                  break;
            case "scheduler":
                  $("#tabs").tabs("disable", 2);
                  $("#scheduler_edit").val(element.find(".label_geral")[0].innerHTML);
                  $("#checkbox_scheduler_all").prop("checked", false);
                  var select = element.find(".scheduler_select>option");
                  var values = select.map(function() {
                        return $(this).val();
                  });
                  $("#scheduler_edit_select").val(values).trigger("liszt:updated");
                  break;
      }
      rules_database("get_rules_by_trigger", 0, $("#script_selector option:selected").val(), 0, element.data("tag"), 0, 0, 0, 0, 0);
}

function edit_element(opcao, element, data)
{
      switch (opcao)
      {
            case "texto":
                  $("#texto_edit").val($("#texto_edit").val().replace(regex_replace_textbox_tag, ''));
                  $("#placeholder_edit").val($("#placeholder_edit").val().replace(regex_replace_textbox, ''));
                  $("#max_length_edit").val($("#max_length_edit").val().replace(/[^0-9]/g, ''));
                  element.find(".label_geral")[0].innerHTML = $("#texto_edit").val();
                  element.find(".input_texto")[0].placeholder = $("#placeholder_edit").val();
                  element.find(".input_texto")[0].maxLength = $("#max_length_edit").val();
                  element.data("regex", $(".validation input:radio[name='regex_texto']:checked").val());
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "texto", element.index(), "h", $("#texto_edit").val(), $("#placeholder_edit").val(), $("#max_length_edit").val(), 0, $("#item_required").is(':checked'), $("#item_hidden").is(':checked'), $(".validation input:radio[name='regex_texto']:checked").val());
                  break;
            case "radio":
                  if ($("#vertic_radio").is(':checked'))
                        element.data("dispo", "v");
                  else
                        element.data("dispo", "h");
                  element.empty();
                  $("#radio_edit").val($("#radio_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.append($("<label>").addClass("label_radio label_geral").text($("#radio_edit").val()));
                  $("#radio_textarea").val($("#radio_textarea").val().replace(regex_replace, ''));
                  var radios = $("#radio_textarea").val().split(regex_split);
                  for (var i = radios.length - 1; i >= 0; i--) {
                        if (radios[i] === "") {
                              radios.splice(i, 1);
                        }
                  }
                  for (var count = 0; count < radios.length; count++)
                  {
                        element.append($("<label>")
                                .addClass("radio_name radio inline")
                                .attr("for", array_id["radio"] + "radio")
                                .text(radios[count]).append($("<input>")
                                .attr("type", "radio")
                                .attr("id", array_id["radio"] + "radio")
                                .attr("name", element.data("id")))
                                );
                        if (element.data("dispo") === "v")
                              element.append($("<br>"));
                        array_id["radio"] = array_id["radio"] + 1;
                  }
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "radio", element.index(), element.data("dispo"), $("#radio_edit").val(), 0, 0, radios, $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;
            case "checkbox":
                  if ($("#vertic_checkbox").is(':checked'))
                        element.data("dispo", "v");
                  else
                        element.data("dispo", "h");
                  element.empty();
                  $("#checkbox_edit").val($("#checkbox_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.append($("<label>").addClass("label_checkbox label_geral").text($("#checkbox_edit").val()));
                  $("#checkbox_textarea").val($("#checkbox_textarea").val().replace(regex_replace, ''));
                  var checkboxs = $("#checkbox_textarea").val().split(regex_split);
                  for (var i = checkboxs.length - 1; i >= 0; i--) {
                        if (checkboxs[i] === "") {
                              checkboxs.splice(i, 1);
                        }
                  }
                  for (var count = 0; count < checkboxs.length; count++)
                  {
                        element.append($("<label>")
                                .addClass("checkbox_name checkbox inline")
                                .attr("for", array_id["checkbox"] + "checkbox")
                                .text(checkboxs[count]).append($("<input>")
                                .attr("type", "checkbox")
                                .attr("id", array_id["checkbox"] + "checkbox")
                                .attr("name", element.data("id")))
                                );
                        if (element.data("dispo") === "v")
                              element.append($("<br>"));
                        array_id["checkbox"] = array_id["checkbox"] + 1;
                  }
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "checkbox", element.index(), element.data("dispo"), $("#checkbox_edit").val(), 0, 0, checkboxs, $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;
            case "multichoice":
                  element.empty();
                  $("#multichoice_edit").val($("#multichoice_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.append($("<label>").addClass("label_multichoice label_geral").text($("#multichoice_edit").val()));
                  $("#multichoice_textarea").val($("#multichoice_textarea").val().replace(regex_replace, ''));
                  var multichoices = $("#multichoice_textarea").val().split(regex_split);
                  for (var i = multichoices.length - 1; i >= 0; i--) {
                        if (multichoices[i] === "") {
                              multichoices.splice(i, 1);
                        }
                  }
                  element.append($("<select>").addClass("multichoice_select"));
                  var select = element.find(".multichoice_select");
                  for (var count = 0; count < multichoices.length; count++)
                  {
                        select.append("<option value='" + multichoices[count] + "'>" + multichoices[count] + "</option>");
                  }
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "multichoice", element.index(), "h", $("#multichoice_edit").val(), 0, 0, multichoices, $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;
            case "textfield":
                  $("#textfield_edit").val($("#textfield_edit").val().replace(regex_text, ''));
                  element.find(".label_geral")[0].innerHTML = $("#textfield_edit").val();
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "textfield", element.index(), "h", "textfield", 0, 0, $("#textfield_edit").val(), false, $("#item_hidden").is(':checked'));
                  break;
            case "legend":
                  $("#legend_edit").val($("#legend_edit").val().replace(regex_text, ''));
                  element.find(".label_geral")[0].innerHTML = $("#legend_edit").val();
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "legend", element.index(), "h", "legend", 0, 0, $("#legend_edit").val(), false, $("#item_hidden").is(':checked'));
                  break;
            case "tableradio":
                  element.find(".label_geral")[0].innerHTML = $("#tableradio_edit").val();
                  var tr_head = element.find(".tr_head");
                  tr_head.empty();
                  $("#tableradio_th_textarea").val($("#tableradio_th_textarea").val().replace(regex_remove_blank, ""));
                  $("#tableradio_th_textarea").val($("#tableradio_th_textarea").val().replace(regex_replace, ''));
                  var titulos = $("#tableradio_th_textarea").val().split(regex_split);
                  for (var i = titulos.length - 1; i >= 0; i--) {
                        if (titulos[i] === "") {
                              titulos.splice(i, 1);
                        }
                  }
                  tr_head.append($("<td>"));
                  for (var count = 0; count < titulos.length; count++)
                  {
                        tr_head.append($("<td>").text(titulos[count]));
                  }
                  var tr_body = element.find(".tr_body");
                  tr_body.empty();
                  $("#tableradio_td_textarea").val($("#tableradio_td_textarea").val().replace(regex_remove_blank, ""));
                  $("#tableradio_td_textarea").val($("#tableradio_td_textarea").val().replace(regex_replace, ''));
                  var perguntas = $("#tableradio_td_textarea").val().split(regex_split);
                  for (var i = perguntas.length - 1; i >= 0; i--) {
                        if (perguntas[i] === "") {
                              perguntas.splice(i, 1);
                        }
                  }
                  for (var count = 0; count < perguntas.length; count++)
                  {
                        tr_body.append($("<tr>").append($("<td>").text(perguntas[count]).addClass("td_row")));
                        temp = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                              temp.append($("<td>")
                                      .append($("<input>").attr("type", "radio").attr("id", array_id["radio"]).attr("name", perguntas[count]))
                                      .append($("<label>").addClass("radio_name radio inline").attr("for", array_id["radio"])));
                              array_id["radio"] = array_id["radio"] + 1;
                        }
                  }
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "tableradio", element.index(), "h", $("#tableradio_edit").val(), titulos, 0, perguntas, $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;
            case "datepicker":
                  $("#datepicker_edit").val($("#datepicker_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.find(".label_geral")[0].innerHTML = $("#datepicker_edit").val();
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "datepicker", element.index(), "h", $("#datepicker_edit").val(), 0, 0, 0, $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;
            case "scheduler":
                  var options = $("#scheduler_edit_select > option:selected").clone();
                  var valores = [];
                  $.each(options, function()
                  {
                        valores.push(this.value);
                  });
                  var select = element.find(".scheduler_select");
                  select.empty();
                  select.append("<option value='' selected>Selecione um calendário</option>");
                  if (options.length > 0)
                        select.append(options);
                  $("#scheduler_edit").val($("#scheduler_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.find(".label_geral")[0].innerHTML = $("#scheduler_edit").val();
                  item_database("edit_item", selected_id, 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "scheduler", element.index(), "h", $("#scheduler_edit").val(), "0", 0, valores, $("#item_required").is(':checked'), $("#item_hidden").is(':checked'), $("#scheduler_edit_marcação option:selected").val());
                  break;
      }


      element.find(".div_info_item").remove();
      element.prepend($("<div>").css("float", "right").addClass("div_info_item span1"));
      var temp = element.find(".div_info_item");
      //ids nos elementos
      temp.append($("<label>").addClass("label label-inverse label_id_item").text(element.data("tag")));
      if ($("#item_required").is(':checked'))
      {
            element.data("required", true);
            temp.append($("<i>").addClass("icon-star required_icon info_icon"));
      }
      else
      {
            element.data("required", false);
      }
      if ($("#item_hidden").is(':checked'))
      {
            element.data("hidden", true);
            temp.append($("<i>").addClass("icon-eye-close hidden_icon info_icon"));
      }
      else
      {
            element.data("hidden", false);
            temp.append($("<i>").addClass("icon-eye-open hidden_icon info_icon"));
      }







}

function insert_element(opcao, element, data)
{

      switch (opcao)
      {
            case "texto":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  element.find(".input_texto")[0].placeholder = data.placeholder;
                  element.find(".input_texto")[0].maxLength = data.max_length;
                  break;
            case "radio":
                  element.empty();
                  element.append($("<label>").addClass("label_radio label_geral").text($("#radio_edit").val()));
                  element.find(".label_radio")[0].innerHTML = data.texto;
                  var radios = data.values_text;
                  for (var count = 0; count < radios.length; count++)
                  {
                        element.append($("<label>")
                                .addClass("radio_name radio inline")
                                .attr("for", array_id["radio"] + "radio")
                                .text(radios[count]).append($("<input>")
                                .attr("type", "radio")
                                .attr("value", count + 1)
                                .attr("id", array_id["radio"] + "radio")
                                .attr("name", data.id))
                                );
                        if (data.dispo === "v")
                              element.append($("<br>"));
                        array_id["radio"] = array_id["radio"] + 1;
                  }
                  break;
            case "checkbox":
                  element.empty();
                  element.append($("<label>").addClass("label_checkbox label_geral").text($("#checkbox_edit").val()));
                  element.find(".label_checkbox")[0].innerHTML = data.texto;
                  var checkboxs = data.values_text;
                  for (var count = 0; count < checkboxs.length; count++)
                  {
                        element.append($("<label>")
                                .addClass("checkbox_name checkbox inline")
                                .attr("for", array_id["checkbox"] + "checkbox")
                                .text(checkboxs[count]).append($("<input>")
                                .attr("type", "checkbox")
                                .attr("value", count + 1)
                                .attr("id", array_id["checkbox"] + "checkbox")
                                .attr("name", data.id))
                                );
                        if (data.dispo === "v")
                              element.append($("<br>"));
                        array_id["checkbox"] = array_id["checkbox"] + 1;
                  }
                  break;
            case "multichoice":
                  element.empty();
                  element.append($("<label>").addClass("label_multichoice label_geral").text(data.texto));
                  var multichoices = data.values_text;
                  element.append($("<select>").addClass("multichoice_select"));
                  var select = element.find(".multichoice_select");
                  var options = "";
                  for (var count = 0; count < multichoices.length; count++)
                  {
                        options += "<option value='" + multichoices[count] + "'>" + multichoices[count] + "</option>";
                  }
                  select.append(options);
                  break;
            case "textfield":
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  break;
            case "legend":
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  break;
            case "tableradio":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  var tr_head = element.find(".tr_head");
                  tr_head.empty();
                  var titulos = data.placeholder;
                  tr_head.append($("<td>"));
                  for (var count = 0; count < titulos.length; count++)
                  {
                        tr_head.append($("<td>").text(titulos[count]));
                  }
                  var tr_body = element.find(".tr_body");
                  tr_body.empty();
                  var perguntas = data.values_text;
                  var temp = 0;
                  for (var count = 0; count < perguntas.length; count++)
                  {
                        tr_body.append($("<tr>").append($("<td>").text(perguntas[count]).addClass("td_row")));
                        temp = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                              temp.append($("<td>")
                                      .append($("<input>")
                                      .attr("type", "radio")
                                      .attr("id", array_id["radio"])
                                      .attr("value", count2 + 1)
                                      .attr("name", data.id + "" + count))

                                      .append($("<label>")
                                      .addClass("radio_name radio inline")
                                      .attr("for", array_id["radio"])
                                      ));
                              array_id["radio"] = array_id["radio"] + 1;
                        }
                  }
                  break;
            case "datepicker":
                  element.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii',autoclose:true,language:"pt"});
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  break;
            case "scheduler":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  var select = element.find(".scheduler_select");
                  var calendarios = data.values_text;
                  var options = [];
                  $.each(calendarios, function() {
                        options.push("<option value='" + this + "'>" + $("#scheduler_edit_select option[value=" + this + "]").text() + "</option>");
                  });
                  if (options.length > 0)
                        select.append(options);
                  break;
      }


      element.prepend($("<div>").css("float", "right").addClass("div_info_item span1"));
      var temp = element.find(".div_info_item");
      //IDs nos elementos 
      temp.append($("<label>").addClass("label label-inverse label_id_item").text(data.tag));
      if (data.required)
            temp.append($("<i>").addClass("icon-star required_icon info_icon"));
      if (opcao != "pagination")
      {
            if (data.hidden)
                  temp.append($("<i>").addClass("icon-eye-close hidden_icon info_icon"));
            else
                  temp.append($("<i>").addClass("icon-eye-open hidden_icon info_icon"));
      }


}





function item_database(opcao, Id, Tag, Id_script, Id_page, Type, Ordem, Dispo, Texto, Placeholder, Max_length, Values_text, Required, Hidden, Param1)
{
      $.post("requests.php", {action: opcao,
            id: Id,
            tag: Tag,
            id_script: Id_script,
            id_page: Id_page,
            type: Type,
            ordem: Ordem,
            dispo: Dispo,
            texto: Texto,
            placeholder: Placeholder,
            max_length: Max_length,
            values_text: Values_text,
            required: Required,
            hidden: Hidden,
            param1: Param1},
      function(data)
      {


            if (opcao === "add_item")
                  update_info();
      }
      , "json");
}

function pagescript_database(opcao, Id_script, Id_pagina, Pos)
{
      $.post("requests.php", {action: opcao, id_script: Id_script, id_pagina: Id_pagina, pos: Pos},
      function(data)
      { 
            editor_toggle("off");
            if (opcao === "delete_page")
                  update_pages(function() {
                        $("#page_selector option:last-child").prop("selected", true);
                  });
            if (opcao === "add_script")
                  update_script(function() {
                 
                        $("#script_selector option:last-child").prop("selected", true);
                  });
            if (opcao === "add_page")
            {
                  update_pages(function() {
                        $("#page_selector option:last-child").prop("selected", true);
                  });

            }
            if (opcao === "delete_script") {
                  update_script();
            }
      }
      , "json");
}



//PAGES
$("#page_add_button").click(function()
{

      var value1 = 0;

      if ($("#page_selector option").length > 0)
            value1 = $("#page_selector option:last-child").data("pos");

      pagescript_database("add_page", $("#script_selector option:selected").val(), 0, value1 + 1);
      $.jGrowl('Página adicionada com sucesso', {life: 3000});
});
$("#page_remove_button").click(function()
{
      $('#page_modal').modal('show');
});
$("#page_remove_button_modal").click(function()
{
      editor_toggle("off");
      pagescript_database("delete_page", $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), $("#page_selector option:selected").data("pos"));
      $.jGrowl('Página removida com sucesso', {life: 3000});
      $('#page_modal').modal('hide');
});
$('#page_selector').change(function() {
      editor_toggle("off");
      update_info();
});
$("#opcao_page_button").click(function()//chama o edit do nome da pagina
{
      current_page_pos = $("#page_selector option:selected").data("pos");
      $("#pages_name_edit").val($("#page_selector option:selected").text());
      $("#page_position option[value=" + $("#page_selector option:selected").data("pos") + "] ").prop("selected", true);
});
$("#save_button_page").click(function()//Fecha o dialog e grava as alterações
{

      $.post("requests.php", {action: "edit_page", id_script: $("#script_selector option:selected").val(), name: $("#pages_name_edit").val(), id_pagina: $("#page_selector option:selected").val(), old_pos: current_page_pos, new_pos: $("#page_position option:selected").val()},
      function(data)
      {
            $('#dialog_page').modal('hide');
            update_pages();
      }, "json");

});
//SCRIPTS
$("#script_add_button").click(function()
{
      pagescript_database("add_script", 0, 0);
      $.jGrowl('Script adicionado com sucesso', {life: 3000});
});
$("#script_remove_button").click(function()
{
      $('#script_modal').modal('show');

});

$("#script_remove_button_modal").click(function()
{
      editor_toggle("off");
      pagescript_database("delete_script", $("#script_selector option:selected").val(), 0);
      $.jGrowl('Script removido com sucesso', {life: 3000});
      $('#script_modal').modal('hide');
});

//script duplicate
$("#copy_script_button").on("click", function()
{
      $('#dialog_layout').modal('hide');

      $.post("requests.php", {action: "duplicate_script", id_script: $("#script_selector option:selected").val(), nome_script: $("#script_selector option:selected").text()},
      function(data)
      {
            update_script();
      }
      , "json");

});

$('#script_selector').change(function() {
      $("#script_campanha_selector").val("").trigger("liszt:updated");
      $("#script_linha_inbound_selector").val("").trigger("liszt:updated");
      editor_toggle("off");
      update_script();
});
$("#opcao_script_button").click(function()//chama o edit do nome do script
{
      $("#script_campanha_selector").val("").trigger("liszt:updated");
      $("#script_linha_inbound_selector").val("").trigger("liszt:updated");
      $.post("requests.php", {action: "get_camp_linha_by_id_script", id_script: $("#script_selector option:selected").val()},
      function(data)
      {
            var campaign = [];
            var linha_inbound = [];
            $.each(data, function(index, value) {
                  if (this.tipo === "campaign")
                        campaign.push(this.id_camp_linha);
                  else
                        linha_inbound.push(this.id_camp_linha);
            });
            $("#script_campanha_selector").val(campaign).trigger("liszt:updated");
            $("#script_linha_inbound_selector").val(linha_inbound).trigger("liszt:updated");
      }, "json");
      $("#script_name_edit").val($("#script_selector option:selected").text());
});
$("#save_button_layout").click(function()//Fecha o dialog e grava as alterações
{
      $.post("requests.php", {action: "edit_script", name: $("#script_name_edit").val(), id_script: $("#script_selector option:selected").val(), campaign: $("#script_campanha_selector").val(), linha_inbound: $("#script_linha_inbound_selector").val()},
      function(data)
      {
            $('#dialog_layout').modal('hide');
            update_script();
      }, "json")
              .fail(function(xhr, textStatus, errorThrown) {
            alert("A campanha/linha de inbound escolhida ja tem um script associado");
      });
});
//botoes para outras paginas
$("#render_go").click(function()
{
      var window_slave = window.open("/sips-admin/script_dinamico/render.html?script_id=" + $("#script_selector option:selected").val());
});
//------------------RULES------------------
function rules_manager(tipo, element)
{
      $("#rule_creator").hide();
      var rts = $("#rule_trigger_select");
      rts.empty();
      switch (tipo)
      {
            case "texto":
                  rts.append(new Option("Resposta", "answer"));
                  rts.append(new Option("Valor especifico", "value_input"));
                  break;
            case "radio":
            case "checkbox":
            case "multichoice":
                  rts.append(new Option("Valor escolhido", "value_select"));
                  break;
            case "tableradio":
                  rts.append(new Option("Resposta", "answer"));
                  rts.append(new Option("Valor escolhido", "value_select"));
                  break;
            case "datepicker":
                  rts.append(new Option("Resposta", "answer"));
                  break;
      }
      rts.trigger("change");
}
$("#rule_trigger_select").change(function()
{
      $(".rules_valor").hide();
      switch ($("#rule_trigger_select option:selected").val())
      {
            case "value_input":
                  $("#rules_valor_input_div").show();
                  $("#rules_valor_input").val("");
                  break;
            case "value_select":
                  $("#rules_valor_select_div").show();
                  $("#rules_valor_select").empty();
                  $.post("requests.php", {action: "get_data_individual", id: selected_id},
                  function(data)
                  {
                        $('#rules_valor_select').empty();
                        var dados = data[0].values_text;
                        if (selected_type === "tableradio")
                        {
                              var titulos = data[0].placeholder;
                              var options = "";
                              $.each(dados, function(index1, value1) {
                                    $.each(titulos, function(index2, value2) {
                                          options += "<option value='" + dados[index1] + ";" + titulos[index2] + "'>" + dados[index1] + "---" + titulos[index2] + "</option>";
                                    });
                              });
                              $("#rules_valor_select").append(options);
                        }
                        else
                        {
                              var options = "";
                              $.each(dados, function(index, value) {
                                    options += "<option value='" + this + "'>" + this + "</option>";
                              });
                              $("#rules_valor_select").append(options);
                        }
                        $('#rules_valor_select').val("").trigger('liszt:updated');
                  }
                  , "json");
                  break;
      }
});
function rules_database(opcao, Id, Id_script, Tipo_elemento, Id_trigger, Id_trigger2, Id_target, Tipo, Param1, Param2)
{
      $.post("requests.php", {action: opcao,
            id: Id,
            id_script: Id_script,
            tipo_elemento: Tipo_elemento,
            tag_trigger: Id_trigger,
            tag_trigger2: Id_trigger2,
            tag_target: Id_target,
            tipo: Tipo,
            param1: Param1,
            param2: Param2},
      function(data)
      {
            if (opcao === "get_rules_by_trigger")
            {
                  $("#rule_table").hide();
                  $("#rule_manager_list").empty();
                  $.each(data, function(index, value) {

                        switch (this.tipo)
                        {
                              case "show":
                                    this.tipo = "mostrar";
                                    break;
                              case "hide":
                                    this.tipo = "esconder";
                                    break;
                              case "goto":
                                    this.tipo = "ir para página";
                                    break;
                        }

                        $("#rule_table").show();
                        if (this.tag_trigger2 == "1")
                              this.tag_trigger2 = "resposta"; //por questões visuais na tabela


                        if (this.tipo == "ir para página")
                        {
                              $("#rule_manager_list").append($("<tr>")
                                      .append($("<td>").text(this.tag_trigger2))
                                      .append($("<td>").text(this.tipo))
                                      .append($("<td>").text($("#go_to_select option[value=" + this.param2 + "]").text()))
                                      .append($("<td>").append($("<button>").addClass("icon-remove rule_delete_icon btn btn-inverse span").data("id", this.id).data("tag_trigger", this.tag_trigger)))
                                      );
                        }
                        else
                        {
                              $("#rule_manager_list").append($("<tr>")
                                      .append($("<td>").text(this.tag_trigger2))
                                      .append($("<td>").text(this.tipo))
                                      .append($("<td>").text(this.tag_target))
                                      .append($("<td>").append($("<button>").addClass("icon-remove rule_delete_icon btn btn-inverse span").data("id", this.id).data("tag_trigger", this.tag_trigger)))
                                      );
                        }


                  });
            }
            if (opcao === "add_rules")
            {
                  rules_database("get_rules_by_trigger", 0, $("#script_selector option:selected").val(), 0, selected_tag, 0, 0, 0, 0, 0);
            }
      }
      , "json");
}
//--------------Rules safety

$(".values_edit_textarea").on("focus", function()
{
      temp_value_holder = $(this).val();

});


$(".values_edit_textarea").on("blur", function()
{
      if (temp_value_holder !== $(this).val())
      {
            $.post("requests.php", {action: "has_rules", id: selected_id},
            function(data)
            {
                  if (data != "0")
                        $.jGrowl('Alterou dados que podem ter regras associadas,procure no separador das regras por conflitos/diferença de dados', {life: 6000});
            }, "json");
      }



});


$("#open_rule_creator").click(function()//Fecha o dialog e grava as alterações 
{
      $("#rule_creator").toggle(800);
});
$("#add_rule_button").click(function()
{

      if (validate_manual())
            switch (selected_type)
            {
                  case "texto":
                        switch ($("#rule_trigger_select").val())
                        {
                              case "answer":


                                    if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, 1, $("#rule_target_select").val(), $("#regra_select").val(), "answer", "0");
                                    else
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, 1, 1, $("#regra_select").val(), "answer", $("#go_to_select").val());
                                    break;
                              case "value_input":
                                    if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_input").val(), $("#rule_target_select").val(), $("#regra_select").val(), "value_input", "0");
                                    else
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_input").val(), 1, $("#regra_select").val(), "value_input", $("#go_to_select").val());
                                    break;
                        }
                        break;
                  case "radio":
                        if ($("#rule_trigger_select").val() === "value_select")
                              if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_select").val(), $("#rule_target_select").val(), $("#regra_select").val(), "value_select", "0");
                              else
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_select").val(), 1, $("#regra_select").val(), "value_select", $("#go_to_select").val());
                        break;
                  case "checkbox":
                        if ($("#rule_trigger_select").val() === "value_select")
                              if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_select").val(), $("#rule_target_select").val(), $("#regra_select").val(), "value_select", "0");
                              else
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_select").val(), 1, $("#regra_select").val(), "value_select", $("#go_to_select").val());
                        break;
                  case"multichoice":
                        if ($("#rule_trigger_select").val() === "value_select")
                              if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_select").val(), $("#rule_target_select").val(), $("#regra_select").val(), "value_select", "0");
                              else
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_select").val(), 1, $("#regra_select").val(), "value_select", $("#go_to_select").val());
                        break;
                  case "tableradio":
                        switch ($("#rule_trigger_select").val())
                        {
                              case "answer":
                                    if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, 1, $("#rule_target_select").val(), $("#regra_select").val(), "answer", "0");
                                    else
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, 1, 1, $("#regra_select").val(), "answer", $("#go_to_select").val());
                                    break;
                              case "value_select":
                                    if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_select").val(), $("#rule_target_select").val(), $("#regra_select").val(), "value_select", "0");
                                    else
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, $("#rules_valor_select").val(), 1, $("#regra_select").val(), "value_select", $("#go_to_select").val());
                                    break;
                        }
                        break;
                  case "datepicker":
                        switch ($("#rule_trigger_select").val())
                        {
                              case "answer":
                                    if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, 1, $("#rule_target_select").val(), $("#regra_select").val(), "answer", "0");
                                    else
                                          rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_tag, 1, 1, $("#regra_select").val(), "answer", $("#go_to_select").val());
                                    break;
                        }
                        break;
            }
      $("#rule_target_select").val("").trigger("liszt:updated");
      $("#rules_valor_select").val("").trigger("liszt:updated");
});
//FORM MANIPULATION
$("#rule_form").on("submit", function(e)
{
      e.preventDefault();
});










