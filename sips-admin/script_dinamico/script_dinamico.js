

////////TO BE DONE//////////////////

//relatorio
//fazer explode dos values_Text no php e vir ja em array


//forms ->http://www.javaworld.com/jw-06-1996/jw-06-javascript.html?page=2


/*
 demo limesurvey
 admin
 test
 */

//por time


var selected_id = 0;
var selected_type = "";
var array_id = [];
var regex_remove_blank = /^\s*$[\n\r]{1,}/gm;
var regex_replace_textbox_tag = /[^a-zA-Z0-9éçã\s:@§óáà?]/g;
var regex_replace_textbox = /[^a-zA-Z0-9éçã\s:§óáà?]/g;
var regex_replace = /[^a-zA-Z0-9éçã\n§óáà\s?]/g;
var regex_split = /\n/g;


//mostra/esconde os elementos associados ao edit
function editor_toggle(tipo)
{
      $(".item").removeClass("helperPick");
      $("#tabs").tabs("option", "active", 0);
      if (tipo === "on")
      {
            $("#item_edit_comum").show();
            $("#rule_manager").show();
            $("#open_rule_creator").prop('disabled', false);//botoes de edit
            $(".chosen_select").chosen();
            $("#rules_valor_select").empty();
            $(".editor_layout").hide();// esconde os edits de todos
            $(".footer_save_cancel button").prop('disabled', false);//botoes de edit
      }
      if (tipo === "off")
      {
            $(".editor_layout").hide();
            $("#item_edit_comum").hide();
            $("#rule_manager").hide();
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

$(function() {

      $("#tabs").tabs();
      array_id["radio"] = 0;
      array_id["checkbox"] = 0;

      $.get("items.html", function(data) {
            $("#rigth_list").html(data);
            $(".footer_save_cancel button").prop('disabled', true);
            $("#rule_manager").hide();
            $(".footer_rule_save_cancel button").prop('disabled', true);
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
                              item_database("delete_item", $(this).data().uiSortable.currentItem.attr("id"), 0, 0, 0, $(this).data().uiSortable.currentItem.index(), 0, 0, 0, 0, 0, 0);
                              ui.item.remove();
                              $("#tabs").tabs("option", "active", 0);
                              $(".footer_save_cancel button").prop('disabled', true);
                              $(".editor_layout").hide();
                        }
                  },
                  update: function(event, ui) {

                        var items = $(".leftDiv  .item");
                        for (var count = 0; count < items.length; count++)
                        {
                              item_database("edit_item_order", items[count].id, 0, 0, 0, $("#" + items[count].id).index(), 0, 0, 0, 0, 0, 0, 0);
                        }
                  },
                  receive: function(event, ui) {

                        if ($(this).data().uiSortable.currentItem.hasClass("texto_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "texto", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .texto_class .label_texto")[0].innerHTML, $(".rightDiv .texto_class .input_texto")[0].placeholder, $(".rightDiv .texto_class .input_texto")[0].maxLength, 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("pagination_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "pagination", $(this).data().uiSortable.currentItem.index(), "h", 0, 0, 0, 0, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("radio_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "radio", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .label_radio")[0].innerHTML, 0, 0, "Valor1", 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("checkbox_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "checkbox", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .label_checkbox")[0].innerHTML, 0, 0, "Valor1", 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("multichoice_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "multichoice", $(this).data().uiSortable.currentItem.index(), "h", $(".rightDiv .label_multichoice")[0].innerHTML, 0, 0, "Opção1", 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("textfield_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "textfield", $(this).data().uiSortable.currentItem.index(), "h", "textfield", 0, 0, $(".rightDiv .label_textfield")[0].innerHTML, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("tableradio_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "tableradio", $(this).data().uiSortable.currentItem.index(), "h", "tableradio", "mau,médio,bom", 0, "pergunta1", 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("legend_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "legend", $(this).data().uiSortable.currentItem.index(), "h", "legend", 0, 0, $(".rightDiv .label_legend")[0].innerHTML, 0, 0);
                        }
                        if ($(this).data().uiSortable.currentItem.hasClass("datepicker_class"))
                        {
                              item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "datepicker", $(this).data().uiSortable.currentItem.index(), "h", "datepicker", 0, 0, $(".rightDiv .label_datepicker")[0].innerHTML, 0, 0);
                        }
                        editor_toggle("off");
                  }
            });
            update_script();

            item_database("get_tag_fields", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            
           
      });
      $(document).on("click", ".element", function(e) {
            selected_id = $(this).data("id");
            selected_type = $(this).data("type");
            editor_toggle("on");
            $(this).addClass("helperPick");//class HelperPick
            $("#tabs").tabs("option", "active", 1);//tabs

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
            }

      });
      $(document).on("click", ".rule_delete_icon", function(e) {
            rules_database("delete_rule", $(this).data("id"), 0, 0, 0, 0, 0, 0, 0, 0);
            rules_database("get_rules_by_trigger", 0, 0, 0, selected_id, 0, 0, 0, 0, 0);


      });
      $(document).on("click", ".rule_delete_icon", function(e) {//GRAFICO DE BARRAS
            rules_database("get_rules_by_trigger", 0, 0, 0, selected_id, 0, 0, 0, 0, 0);
      });
      editor_toggle("off");



});



//UPDATES DE INFO
function update_script()
{
      $.post("requests.php", {action: "get_scripts"},
      function(data)
      {

            if (data == null)

            {
                  ;
                  $("#page_selector_div button").prop('disabled', true);
                  $("#opcao_script_button").prop('disabled', true);
                  $("#script_selector").empty();
                  $("#page_selector").empty();
                  $(".leftDiv").empty();

            }
            else
            {
                  $("#page_selector_div button").prop('disabled', false);
                  $("#opcao_script_button").prop('disabled', false);
                  $("#script_selector").empty();
                  $.each(data, function(index, value) {
                        $("#script_selector").append("<option value=" + data[index].id + ">" + data[index].name + "</option>");
                  });
                  update_pages();
            }
      }, "json");


}
function update_pages()
{


      $.post("requests.php", {action: "get_pages", id_script: $("#script_selector option:selected").val()},
      function(data)
      {
            if (data == null)
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

                  $.each(data, function(index, value) {
                        if (pag === data[index].id)
                              $("#page_selector").append("<option value=" + data[index].id + " selected>" + data[index].name + "</option>");
                        else
                              $("#page_selector").append("<option value=" + data[index].id + ">" + data[index].name + "</option>");

                        $("#go_to_select").append(new Option(data[index].name, "pag," + data[index].id));
                  });
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

            $(".elements_select").empty();
            $("#rule_target_select").empty();
            $.each(data, function(index, value) {

                  $("#rule_target_select").append(new Option(data[index].id + "--" + data[index].type, data[index].id));//povoar os alvos com os ides e tipos dos elementos


                  switch (data[index].type)
                  {
                        case "texto":
                              var item = $('.rightDiv .texto_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("hidden", data[index].hidden)
                                      .data("type", "texto");
                              insert_element("texto", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;

                        case "pagination":
                              var item = $('.rightDiv .pagination_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("type", "pagination");
                              insert_element("pagination", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;

                        case "radio":
                              var item = $('.rightDiv .radio_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("hidden", data[index].hidden)
                                      .data("type", "radio")
                                      .data("dispo", data[index].dispo);
                              insert_element("radio", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;

                        case "checkbox":
                              var item = $('.rightDiv .checkbox_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("hidden", data[index].hidden)
                                      .data("dispo", data[index].dispo)
                                      .data("type", "checkbox");
                              insert_element("checkbox", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;

                        case "multichoice":
                              var item = $('.rightDiv .multichoice_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("hidden", data[index].hidden)
                                      .data("type", "multichoice");
                              insert_element("multichoice", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;

                        case "textfield":
                              var item = $('.rightDiv .textfield_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("hidden", data[index].hidden)
                                      .data("type", "textfield");
                              insert_element("textfield", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;
                        case "legend":
                              var item = $('.rightDiv .legend_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("hidden", data[index].hidden)
                                      .data("type", "legend");
                              insert_element("legend", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;

                        case "tableradio":
                              var item = $('.rightDiv .tableradio_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("hidden", data[index].hidden)
                                      .data("type", "tableradio");
                              insert_element("tableradio", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;

                        case "datepicker":
                              var item = $('.rightDiv .datepicker_class').clone();
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("hidden", data[index].hidden)
                                      .data("type", "datepicker");
                              insert_element("datepicker", item, data[index]);
                              item.appendTo('.leftDiv');
                              break;
                  }
            });
            $('#rule_target_select').val('').trigger('liszt:updated');
      }, "json");


}





function populate_element(tipo, element)
{

      if (element.data("required"))
            $("#item_required").attr('checked', true);
      else
            $("#item_required").attr('checked', false);

      if (element.data("hidden"))
            $("#item_hidden").attr('checked', true);
      else
            $("#item_hidden").attr('checked', false);


      $("#label_tag").text("@" + element.data("id") + "@");

      rules_manager(tipo, element);
      $("#item_edit_comum div").show();
      $("#tabs").tabs("enable");

      switch (tipo)
      {
            case "texto":

                  $("#texto_edit").val(element.find(".label_geral")[0].innerHTML);
                  $("#placeholder_edit").val(element.find(".input_texto")[0].placeholder);
                  $("#max_length_edit").val(element.find(".input_texto")[0].maxLength);
                  break;

            case "radio":

                  $("#radio_edit").val(element.find(".label_geral")[0].innerHTML);
                  $("#radio_group_edit").val(element.find(":radio")[0].name);
                  var string_elements = "";
                  for (var count = 0; count < element.find(":radio").length; count++)
                  {
                        if (count == element.find(":radio").length - 1)
                              string_elements += element.find(".radio_name")[count].innerHTML;
                        else
                              string_elements += element.find(".radio_name")[count].innerHTML + "\n";
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
                  for (var count = 0; count < element.find(":checkbox").length; count++)
                  {
                        if (count == element.find(":checkbox").length - 1)
                              string_elements += element.find(".checkbox_name")[count].innerHTML;
                        else
                              string_elements += element.find(".checkbox_name")[count].innerHTML + "\n";
                        string_elements = string_elements.replace("<span></span>", "");
                  }
                  $("#checkbox_textarea").val(string_elements);
                  if (element.data("dispo") === "v")
                        $("#vertic_checkbox").attr('checked', true);
                  else
                        $("#horiz_checkbox").attr('checked', true);
                  break;


            case "pagination":
                  $("#tabs").tabs("disable", 1);
                  $("#tabs").tabs("disable", 2);
                  $(".footer_save_cancel button").prop('disabled', true);

                  $("#item_edit_comum").hide();
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


                  $("#tabs").tabs("disable", 2);
                  $("#rule_manager").hide();
                  $(".footer_rule_save_cancel button").prop('disabled', true);
                  $(".required_class").hide();
                  $("#textfield_edit").val(element.find(".label_geral")[0].innerHTML);
                  break;

            case "legend":

                  $("#tabs").tabs("disable", 2);
                  $("#rule_manager").hide();
                  $(".footer_rule_save_cancel button").prop('disabled', true);
                  $(".required_class").hide();
                  $("#legend_edit").val(element.find(".label_geral")[0].innerHTML);
                  break;

            case "tableradio":
                  $("#tableradio_edit").val(element.find(".label_geral")[0].innerHTML);

                  //titulos->texto
                  var string_elements = "";
                  for (var count = 1; count < element.find(".tr_head td").length; count++)
                  {
                        string_elements += element.find(".tr_head td")[count].innerHTML + "\n";
                  }
                  $("#tableradio_th_textarea").val(string_elements.slice(0, -1));
                  string_elements = "";
                  for (var count = 0; count < element.find(".tr_body .td_row").length; count++)
                  {
                        string_elements += element.find(".tr_body .td_row")[count].innerHTML + "\n";
                  }
                  $("#tableradio_td_textarea").val(string_elements.slice(0, -1));
                  break;


            case "datepicker":

                  $("#datepicker_edit").val(element.find(".label_geral")[0].innerHTML);
                  break;

      }
      rules_database("get_rules_by_trigger", 0, 0, 0, element.data("id"), 0, 0, 0, 0, 0);



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
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "texto", element.index(), "h", $("#texto_edit").val(), $("#placeholder_edit").val(), $("#max_length_edit").val(), 0, $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;


            case "radio":
                  if ($("#vertic_radio").is(':checked'))
                        element.data("dispo", "v");
                  else
                        element.data("dispo", "h");
                  element.empty();
                  $("#radio_edit").val($("#radio_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.append($("<label>").addClass("label_radio label_geral").text($("#radio_edit").val()));
                  element.append($("<br>"));
                  $("#radio_textarea").val($("#radio_textarea").val().replace(regex_replace, ''));
                  var radios = $("#radio_textarea").val().split(regex_split);
                  for (var i = radios.length - 1; i >= 0; i--) {
                        if (radios[i] === "") {
                              radios.splice(i, 1);
                        }
                  }
                  for (var count = 0; count < radios.length; count++)
                  {
                        element.append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "radio").attr("name", element.data("id")));
                        element.append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "radio").text(radios[count]).append($("<span>")));
                        if (element.data("dispo") === "v")
                              element.append($("<br>"));
                        array_id["radio"] = array_id["radio"] + 1;
                  }
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "radio", element.index(), element.data("dispo"), $("#radio_edit").val(), 0, 0, radios.join(","), $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;


            case "checkbox":
                  if ($("#vertic_checkbox").is(':checked'))
                        element.data("dispo", "v");
                  else
                        element.data("dispo", "h");
                  element.empty();
                  $("#checkbox_edit").val($("#checkbox_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.append($("<label>").addClass("label_checkbox label_geral").text($("#checkbox_edit").val()));
                  element.append($("<br>"));
                  $("#checkbox_textarea").val($("#checkbox_textarea").val().replace(regex_replace, ''));
                  var checkboxs = $("#checkbox_textarea").val().split(regex_split);
                  for (var i = checkboxs.length - 1; i >= 0; i--) {
                        if (checkboxs[i] === "") {
                              checkboxs.splice(i, 1);
                        }
                  }
                  for (var count = 0; count < checkboxs.length; count++)
                  {
                        element.append($("<input>").attr("type", "checkbox").attr("id", array_id["checkbox"] + "checkbox").attr("name", element.data("id")));
                        element.append($("<label>").addClass("checkbox_name").attr("for", array_id["checkbox"] + "checkbox").text(checkboxs[count]).append($("<span>")));
                        if (element.data("dispo") === "v")
                              element.append($("<br>"));
                        array_id["checkbox"] = array_id["checkbox"] + 1;
                  }
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "checkbox", element.index(), element.data("dispo"), $("#checkbox_edit").val(), 0, 0, checkboxs.join(","), $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
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
                  element.append("<select class = 'multichoice_select' > < /select>");
                  var select = element.find(".multichoice_select");
                  for (var count = 0; count < multichoices.length; count++)
                  {
                        select.append("<option value='" + multichoices[count] + "'>" + multichoices[count] + "</option>");
                  }
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "multichoice", element.index(), "h", $("#multichoice_edit").val(), 0, 0, multichoices.join(","), $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;


            case "textfield":
                  $("#textfield_edit").val($("#textfield_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.find(".label_geral")[0].innerHTML = $("#textfield_edit").val();
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "textfield", element.index(), "h", "textfield", 0, 0, $("#textfield_edit").val(), false, $("#item_hidden").is(':checked'));
                  break;

            case "legend":
                  $("#legend_edit").val($("#legend_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.find(".label_geral")[0].innerHTML = $("#legend_edit").val();
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "legend", element.index(), "h", "legend", 0, 0, $("#legend_edit").val(), false, $("#item_hidden").is(':checked'));
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
                  tr_head.append($("<td>").text("*"));
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
                        tr_body.append($("<tr>").attr("id", perguntas[count])
                                .append($("<td>").text(perguntas[count]).addClass("td_row")));
                        temp = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                              temp.append($("<td>")
                                      .append($("<input>").attr("type", "radio").attr("id", array_id["radio"]).attr("name", perguntas[count]))
                                      .append($("<label>").addClass("radio_name").attr("for", array_id["radio"]).append($("<span>"))));
                              array_id["radio"] = array_id["radio"] + 1;
                        }
                  }


                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "tableradio", element.index(), "h", $("#tableradio_edit").val(), titulos.join(","), 0, perguntas.join(","), $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;



            case "datepicker":
                  if ($("#required_datepicker").is(':checked'))
                        element.data("required", "1");
                  else
                        element.data("required", "0");
                  $("#datepicker_edit").val($("#datepicker_edit").val().replace(regex_replace_textbox_tag, ''));
                  element.find(".label_geral")[0].innerHTML = $("#datepicker_edit").val();
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "datepicker", element.index(), "h", "datepicker", 0, 0, $("#datepicker_edit").val(), $("#item_required").is(':checked'), $("#item_hidden").is(':checked'));
                  break;

      }


      element.find(".hidden_icon").remove();
      if ($("#item_hidden").is(':checked'))
      {
            element.data("hidden", true);
            element.append($("<i>").addClass("icon-eye-close hidden_icon").css("float", "right"));
      }
      else
      {
            element.data("hidden", false);
            element.append($("<i>").addClass("icon-eye-open hidden_icon").css("float", "right"));
      }

      element.find(".required_icon").remove();
      if ($("#item_required").is(':checked'))
      {

            element.data("required", true);
            element.append($("<i>").addClass("icon-star required_icon").css("float", "right"));
      }
      else
      {
            element.data("required", false);
      }

      //ids nos elementos
      element.find(".label_id_item").remove();
      element.prepend($("<label>").addClass("label label-info label_id_item").text(element.data("id")).css("float", "left"));

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
                  element.append($("<br>"));
                  element.find(".label_radio")[0].innerHTML = data.texto;
                  var radios = data.values_text.split(",");
                  for (var count = 0; count < radios.length; count++)
                  {
                        element.append($("<input>")
                                .attr("type", "radio")
                                .attr("value", count + 1)
                                .attr("id", array_id["radio"] + "radio")
                                .attr("name", data.id));
                        element.append($("<label>")
                                .addClass("radio_name")
                                .attr("for", array_id["radio"] + "radio")
                                .text(radios[count])
                                .append($("<span>")));
                        if (data.dispo === "v")
                              element.append($("<br>"));
                        array_id["radio"] = array_id["radio"] + 1;
                  }
                  break;


            case "checkbox":
                  element.empty();
                  element.append($("<label>").addClass("label_checkbox label_geral").text($("#checkbox_edit").val()));
                  element.append($("<br>"));
                  element.find(".label_checkbox")[0].innerHTML = data.texto;
                  var checkboxs = data.values_text.split(",");
                  for (var count = 0; count < checkboxs.length; count++)
                  {
                        element.append($("<input>").attr("type", "checkbox").attr("value", count + 1).attr("id", array_id["checkbox"] + "checkbox").attr("name", data.id));
                        element.append($("<label>").addClass("checkbox_name").attr("for", array_id["checkbox"] + "checkbox").text(checkboxs[count]).append($("<span>")));
                        if (data.dispo === "v")
                              element.append($("<br>"));
                        array_id["checkbox"] = array_id["checkbox"] + 1;
                  }
                  break;


            case "multichoice":
                  element.empty();
                  element.append($("<label>").addClass("label_multichoice label_geral").text(data.texto));
                  var multichoices = data.values_text.split(",");
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
                  var titulos = data.placeholder.split(",");
                  tr_head.append($("<td>").text("*"));
                  for (var count = 0; count < titulos.length; count++)
                  {
                        tr_head.append($("<td>").text(titulos[count]));
                  }
                  var tr_body = element.find(".tr_body");
                  tr_body.empty();
                  var perguntas = data.values_text.split(",");
                  var temp = 0;
                  for (var count = 0; count < perguntas.length; count++)
                  {
                        tr_body.append($("<tr>")
                                .append($("<td>").text(perguntas[count]).addClass("td_row")));
                        temp = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                              temp.append($("<td>")
                                      .append($("<input>").attr("type", "radio").attr("id", array_id["radio"]).attr("value", count2 + 1).attr("name", data.id + "" + count))
                                      .append($("<label>").addClass("radio_name").attr("for", array_id["radio"]).append($("<span>"))));
                              array_id["radio"] = array_id["radio"] + 1;
                        }
                  }
                  break;

            case "datepicker":
                  element.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii'});
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  break;

      }
      if (data.hidden)
            element.append($("<i>").addClass("icon-eye-close hidden_icon").css("float", "right"));
      else
            element.append($("<i>").addClass("icon-eye-open hidden_icon").css("float", "right"));


      if (data.required)
            element.append($("<i>").addClass("icon-star required_icon").css("float", "right"));

      //ids nos elementos

      element.prepend($("<label>").addClass("label label-info label_id_item").text(data.id).css("float", "left"));


}





function item_database(opcao, Id, Id_script, Id_page, Type, Ordem, Dispo, Texto, Placeholder, Max_length, Values_text, Required, Hidden)
{
      $.post("requests.php", {action: opcao,
            id: Id,
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
            hidden: Hidden},
      function(data)
      {
            if (opcao === "get_tag_fields")
            {
                  $("#tag_label").text("§" + data[0].value + "§");
                  $.each(data, function(index, value) {
                        $("#tags_select").append("<option value='" + data[index].value + "'>" + data[index].name + "</option>");
                  });
            }

            if (opcao === "add_item")
                  update_info();
      }
      , "json");

}

function pagescript_database(opcao, Id_script, Id_pagina)
{
      $.post("requests.php", {action: opcao, id_script: Id_script, id_pagina: Id_pagina},
      function(data)
      {
            if (opcao === "delete_page")
                  update_pages();

            if (opcao === "add_script")
                  update_script();

            if (opcao === "add_page")
                  update_pages();

            if (opcao === "delete_script") {
                  update_script();
            }
      }
      , "json");
}



//PAGES
$("#page_add_button").click(function()
{
      pagescript_database("add_page", $("#script_selector option:selected").val(), 0);

});
$("#page_remove_button").click(function()
{

      pagescript_database("delete_page", $("#script_selector option:selected").val(), $("#page_selector option:selected").val());


});
$('#page_selector').change(function() {
      editor_toggle("off");
      $(".item").removeClass("helperPick");
      $("#tabs").tabs("option", "active", 0);//tabs
      update_info();

});

$("#opcao_page_button").click(function()//chama o edit do nome da pagina
{
      $("#pages_name_edit").val($("#page_selector option:selected").text());

});
$("#save_button_page").click(function()//Fecha o dialog e grava as alterações
{
      $.post("requests.php", {action: "edit_page_name", name: $("#pages_name_edit").val(), id_pagina: $("#page_selector option:selected").val()},
      function(data)
      {
            update_pages();
            $('#dialog_page').modal('hide');


      }, "json");

});

//SCRIPTS
$("#script_add_button").click(function()
{
      pagescript_database("add_script", 0, 0);

});
$("#script_remove_button").click(function()
{
      pagescript_database("delete_script", $("#script_selector option:selected").val(), 0);
});
$('#script_selector').change(function() {
      update_pages();
});

$("#opcao_script_button").click(function()//chama o edit do nome do script
{
      $("#script_name_edit").val($("#script_selector option:selected").text());

});
$("#save_button_layout").click(function()//Fecha o dialog e grava as alterações
{
      $.post("requests.php", {action: "edit_script_name", name: $("#script_name_edit").val(), id_script: $("#script_selector option:selected").val()},
      function(data)
      {

            $('#dialog_layout').modal('hide');
            update_script();

      }, "json");

});

//botoes para outras paginas
$("#render_go").click(function()
{
      var window_slave = window.open("/sips-admin/script_dinamico/render.html");
});
$("#report_go").click(function()
{
      var window_slave = window.open("/sips-admin/script_dinamico/report.html");
});




//------------------RULES------------------
function rules_manager(tipo, element)
{
      $("#rule_creator").hide();
      $("#rule_trigger_select").empty();

      switch (tipo)
      {
            case "texto":
                  $("#rule_trigger_select").append(new Option("Resposta", "answer"));
                  $("#rule_trigger_select").append(new Option("Valor especifico", "value_input"));
                  break;


            case "radio":
                  $("#rule_trigger_select").append(new Option("Valor escolhido", "value_select"));
                  break;


            case "checkbox":
                  $("#rule_trigger_select").append(new Option("Valor escolhido", "value_select"));
                  break;


            case "multichoice":
                  $("#rule_trigger_select").append(new Option("Valor escolhido", "value_select"));
                  break;


            case "tableradio":
                  $("#rule_trigger_select").append(new Option("Resposta", "answer"));
                  $("#rule_trigger_select").append(new Option("Valor escolhido", "value_select"));
                  break;

            case "datepicker":
                  $("#rule_trigger_select").append(new Option("Resposta", "answer"));
                  break;
      }
      $("#rule_trigger_select").trigger("change");
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
                  $.post("requests.php", {action: "get_data_individual", id: selected_id},
                  function(data)
                  {

                        var dados = data[0].values_text.split(",");

                        if (selected_type === "tableradio")
                        {
                              var titulos = data[0].placeholder.split(",");
                              var options = "";
                              $.each(dados, function(index1, value1) {
                                    $.each(titulos, function(index2, value2) {
                                          options += "<option value='" + dados[index1] + ";" + titulos[index2] + "'>" + dados[index1] + ";" + titulos[index2] + "</option>";

                                    });
                              });
                              $("#rules_valor_select").append(options);
                        }
                        else
                        {
                              var options = "";

                              $.each(dados, function(index, value) {
                                    options += "<option value='" + dados[index] + "'>" + dados[index] + "</option>";
                                    console.log(options);
                              });
                              $("#rules_valor_select").append(options);
                        }






                        $('#rules_valor_select').trigger('liszt:updated');
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
            id_trigger: Id_trigger,
            id_trigger2: Id_trigger2,
            id_target: Id_target,
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
                        $("#rule_table").show();
                        if (data[index].id_trigger2 == "0")
                              data[index].id_trigger2 = "resposta";

                        $("#rule_manager_list").append($("<tr>")
                                .append($("<td>").text(data[index].id_trigger2))
                                .append($("<td>").text(data[index].tipo))
                                .append($("<td>").text(data[index].id_target))
                                .append($("<td>").append($("<button>").addClass("icon-remove rule_delete_icon btn").data("id", data[index].id).data("id_trigger", data[index].id_trigger)))
                                );
                  });




            }
            if (opcao === "add_rules")
            {
                  rules_database("get_rules_by_trigger", 0, 0, 0, selected_id, 0, 0, 0, 0, 0);
            }
      }
      , "json");
}




$("#open_rule_creator").click(function()//Fecha o dialog e grava as alterações
{
      $("#rule_creator").toggle();

});

$("#add_rule_button").click(function()
{
      switch (selected_type)
      {
            case "texto":
                  switch ($("#rule_trigger_select").val())
                  {
                        case "answer":
                              if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, 0, $("#rule_target_select").val().join(","), $("#regra_select").val(), "answer", "0");
                              else
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, 0, 0, $("#regra_select").val(), "answer", $("#go_to_select").val());
                              break;
                        case "value_input":
                              if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_input").val(), $("#rule_target_select").val().join(","), $("#regra_select").val(), "value_input", "0");
                              else
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_input").val(), 0, $("#regra_select").val(), "value_select", $("#go_to_select").val());
                              break;
                  }

                  break;


            case "radio":
                  if ($("#rule_trigger_select").val() === "value_select")
                        if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                              rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_select").val().join(","), $("#rule_target_select").val().join(","), $("#regra_select").val(), "value_select", "0");
                        else
                              rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_select").val().join(","), 0, $("#regra_select").val(), "value_select", $("#go_to_select").val());
                  break;


            case "checkbox":
                  if ($("#rule_trigger_select").val() === "value_select")
                        if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                              rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_select").val().join(","), $("#rule_target_select").val().join(","), $("#regra_select").val(), "value_select", "0");
                        else
                              rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_select").val().join(","), 0, $("#regra_select").val(), "value_select", $("#go_to_select").val());
                  break;


            case"multichoice":
                  if ($("#rule_trigger_select").val() === "value_select")
                        if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                              rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_select").val().join(","), $("#rule_target_select").val().join(","), $("#regra_select").val(), "value_select", "0");
                        else
                              rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_select").val().join(","), 0, $("#regra_select").val(), "value_select", $("#go_to_select").val());

                  break;

            case "tableradio":

                  switch ($("#rule_trigger_select").val())
                  {
                        case "answer":
                              if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, 0, $("#rule_target_select").val().join(","), $("#regra_select").val(), "answer", "0");
                              else
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, 0, 0, $("#regra_select").val(), "answer", $("#go_to_select").val());
                              break;
                        case "value_select":
                              if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_select").val().join(","), $("#rule_target_select").val().join(","), $("#regra_select").val(), "value_select", "0");
                              else
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, $("#rules_valor_select").val().join(","), 0, $("#regra_select").val(), "value_select", $("#go_to_select").val());
                              break;
                  }


            case "datepicker":
                  switch ($("#rule_trigger_select").val())
                  {
                        case "answer":
                              if ($("#regra_select").val() === "show" || $("#regra_select").val() === "hide")
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, 0, $("#rule_target_select").val().join(","), $("#regra_select").val(), "answer", "0");
                              else
                                    rules_database("add_rules", 0, $("#script_selector option:selected").val(), selected_type, selected_id, 0, 0, $("#regra_select").val(), "answer", $("#go_to_select").val());
                              break;
                  }
      }



});

//FORM MANIPULATION
$("#rule_form").on("submit", function(e)
{
      e.preventDefault();
});