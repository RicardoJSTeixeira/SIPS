

//////////NOW///////////
// a ordem ainda da problemas-fucking get this done
////meter regexs    -done mas ainda falta tirar linhas em branco//falta remover espaços brancos das textareas e textboxes



////////TO BE DONE//////////////////
//fazer render dos elementos dentro dos elementos(tag)


//gerir mudança de scripts e paginas no render.js

////////LATER///////////
//regras de edição(novo separador)
//    -go to
//    -hide
//    -show


/*
 id
 id_element-> elemento geral
 id_subelement -> por exe. diferentes radios
 nome->nome da regra
 tipo(go_to/hide/show)
 go_to
 
 
 
 
 
 
 */






//fazer explode dos values_Text no php e vir ja em array

//elemento navegação








//regex para obter tags
//\[BEGINTAG\](.+?)\[/ENDTAG\]



/*
 demo limesurvey
 admin
 test
 */


var selected_id = 0;
var selected_type = "";
var array_id = [];
var regex_remove_blank = /^\s*$[\n\r]{1,}/gm;
var regex_replace_textbox = /[^a-zA-Z0-9éçã\s:@§]/g;

var regex_replace = /[^a-zA-Z0-9éçã\n§@]/g;
var regex_split = /\n/g;



$(document).ready(function() {
      array_id["textbox"] = 0;
      array_id["radio"] = 0;
      array_id["checkbox"] = 0;
      array_id["multichoice"] = 0;

      $(".footer_save_cancel button").prop('disabled', true);
      $("#tabs").tabs();
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
            stop: function(event, ui) {
                  var items = $(".leftDiv  .item");
                  for (var count = 0; count < items.length; count++)
                  {
                                   item_database("edit_item_order", items[count].id, 0, 0, 0, 0, $("#" + items[count].id).index(), 0, 0, 0, 0);
                  }

            },
            beforeStop: function(event, ui) {
                  if (removeIntent == true) {
                        ui.item.remove();
                        item_database("delete_item", ui.item.attr("id"), 0, 0, 0, 0, 0, 0, 0);
                        $("#tabs").tabs("option", "active", 0);
                        $(".footer_save_cancel button").prop('disabled', true);
                        $(".editor_layout").hide();
                  }
            },
            receive: function(event, ui) {

                  if ($(this).data().uiSortable.currentItem.hasClass("texto_class"))
                  {
                        item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "texto", 0, ui.item.index(), "h", $(".texto_class .label_texto")[0].innerHTML, $(".texto_class .input_texto")[0].placeholder, $(".texto_class .input_texto")[0].maxLength, 0, 0);
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("password_class"))
                  {
                        item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "password", 0, ui.item.index(), "h", $(".label_texto_password")[0].innerHTML, $(".input_texto_password")[0].placeholder, $(".input_texto_password")[0].maxLength, 0, 0);
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("radio_class"))
                  {
                        item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "radio", 0, ui.item.index(), "h", $(".label_radio")[0].innerHTML, 0, 0, "Valor1", 0);
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("checkbox_class"))
                  {
                        item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "checkbox", 0, ui.item.index(), "h", $(".label_checkbox")[0].innerHTML, 0, 0, "Valor1", 0);
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("multichoice_class"))
                  {
                        item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "multichoice", 0, ui.item.index(), "h", $(".label_multichoice")[0].innerHTML, 0, 0, "Opção1", 0);
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("textfield_class"))
                  {
                        item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "textfield", 0, ui.item.index(), "h", "textfield", 0, 0, $(".label_textfield")[0].innerHTML, 0);
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("tableradio_class"))
                  {
                        item_database("add_item", 0, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "tableradio", 0, ui.item.index(), "h", "mau,médio,bom", 0, 0, "pergunta1", 0);
                  }




            }

      });
      update_script();

      item_database("get_tag_fields", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

      $(document).on("click", ".element", function(e) {
            $(".footer_save_cancel button").prop('disabled', false);
            $(".item").removeClass("helperPick");
            $(this).addClass("helperPick");
            $("#tabs").tabs("option", "active", 1);
            $(".editor_layout").hide();
            $(".tag_div").show();
            $(".group_div").show();
            switch ($(this).data("type"))
            {
                  case "texto":
                        $("#text_layout_editor").show();
                        populate_element("texto", $(this));
                        break;
                  case "password":
                        $("#password_layout_editor").show();
                        populate_element("password", $(this));
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
                  case "tableradio":
                        $("#tableradio_layout_editor").show();
                        populate_element("tableradio", $(this));
                        break;

            }
            selected_id = $(this).data("id");
            selected_type = $(this).data("type");
      });


});

$("#tags_select").change(function()
{

      $("#tag_label").text("§" + $(this).val() + "§");
});

$("#regra_select").change(function()
{
      $(".rules_option").hide();
      switch ($(this).val())
      {
            case "goto":
                  $("#goto_div").show();
                  break;
            case "show":
                  $("#show_div").show();
                  break;
            case "hide":
                  $("#hide_div").show();
                  break;
      }
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

                  $(".leftDiv").hide();
                  $("#opcao_page_button").prop('disabled', true);
            }
            else
            {
                  $(".leftDiv").show();
                  $("#opcao_page_button").prop('disabled', false);
                  var pag = $("#page_selector").val();
                  $("#page_selector").empty();
                  $.each(data, function(index, value) {
                        if (pag === data[index].id)
                              $("#page_selector").append("<option value=" + data[index].id + " selected>" + data[index].name + "</option>");
                        else
                              $("#page_selector").append("<option value=" + data[index].id + ">" + data[index].name + "</option>");
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
            $.each(data, function(index, value) {
                  switch (data[index].type)
                  {
                        case "texto":
                              var item = $('.rightDiv .texto_class').clone();

                              insert_element("texto", item, data[index]);
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("type", "texto")
                                      .data("grupo", data[index].grupo);
                              item.appendTo('.leftDiv');
                              break;

                        case "password":
                              var item = $('.rightDiv .password_class').clone();
                              insert_element("password", item, data[index]);

                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("type", "password")
                                      .data("grupo", data[index].grupo);
                              item.appendTo('.leftDiv');
                              break;

                        case "radio":
                              var item = $('.rightDiv .radio_class').clone();
                              insert_element("radio", item, data[index]);

                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("type", "radio")
                                      .data("dispo", data[index].dispo)
                                      .data("grupo", data[index].grupo);

                              item.appendTo('.leftDiv');
                              break;

                        case "checkbox":
                              var item = $('.rightDiv .checkbox_class').clone();
                              insert_element("checkbox", item, data[index]);

                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("dispo", data[index].dispo)
                                      .data("grupo", data[index].grupo)
                                      .data("type", "checkbox");

                              item.appendTo('.leftDiv');
                              break;

                        case "multichoice":
                              var item = $('.rightDiv .multichoice_class').clone();
                              insert_element("multichoice", item, data[index]);

                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("type", "multichoice")
                                      .data("grupo", data[index].grupo);

                              item.appendTo('.leftDiv');
                              break;

                        case "textfield":
                              var item = $('.rightDiv .textfield_class').clone();
                              insert_element("textfield", item, data[index]);
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("type", "textfield")
                                      .data("grupo", data[index].grupo);

                              item.appendTo('.leftDiv');
                              break;

                        case "tableradio":
                              var item = $('.rightDiv .tableradio_class').clone();
                              insert_element("tableradio", item, data[index]);
                              item.attr("id", data[index].id)
                                      .data("id", data[index].id)
                                      .addClass("element")
                                      .data("required", data[index].required)
                                      .data("type", "tableradio")
                                      .data("grupo", data[index].grupo);

                              item.appendTo('.leftDiv');
                              break;


                  }



            });

      }, "json");




}


//FOOTER EDIT BUTTONS
$("#cancel_edit").click(function()
{
      $(".footer_save_cancel button").prop('disabled', true);
      $(".item").removeClass("helperPick");
      $("#tabs").tabs("option", "active", 0);
      $(".editor_layout").hide();
      $(".tag_div").hide();
      $(".group_div").hide();
});
$("#save_edit").click(function()
{
      edit_element(selected_type, $("#" + selected_id), 0);
      $(".footer_save_cancel button").prop('disabled', true);
      $(".item").removeClass("helperPick");
      $("#tabs").tabs("option", "active", 0);
      $(".editor_layout").hide();
      $(".tag_div").hide();
      $(".group_div").hide();

});


function populate_element(tipo, element)
{
      switch (tipo)
      {
            case "texto":


                  $("#grupo_edit").val(element.data("grupo"));

                  $("#label_tag_texto").text("@" + element.data("id") + "@");

                  if (element.data("required") === "1")
                        $("#required_texto").attr('checked', true);
                  else
                        $("#required_texto").attr('checked', false);

                  $("#texto_edit").val(element.find(".label_geral")[0].innerHTML);
                  $("#placeholder_edit").val(element.find(".input_texto")[0].placeholder);
                  $("#max_length_edit").val(element.find(".input_texto")[0].maxLength);
                  break;



            case "password":

                  $("#grupo_edit").val(element.data("grupo"));
                  $("#label_tag_password").text("@" + element.data("id") + "@");
                  if (element.data("required") === "1")
                        $("#required_password").attr('checked', true);
                  else
                        $("#required_password").attr('checked', false);
                  $("#passwordtexto_edit").val(element.find(".label_geral")[0].innerHTML);
                  $("#passwordplaceholder_edit").val(element.find(".input_texto_password")[0].placeholder);
                  $("#passwordmax_length_edit").val(element.find(".input_texto_password")[0].maxLength);
                  break;



            case "radio":

                  $("#grupo_edit").val(element.data("grupo"));
                  $("#label_tag_radio").text("@" + element.data("id") + "@");
                  if (element.data("required") === "1")
                        $("#required_radio").attr('checked', true);
                  else
                        $("#required_radio").attr('checked', false);
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

                  $("#grupo_edit").val(element.data("grupo"));
                  $("#label_tag_checkbox").text("@" + element.data("id") + "@");
                  if (element.data("required") === "1")
                        $("#required_checkbox").attr('checked', true);
                  else
                        $("#required_checkbox").attr('checked', false);
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



            case "multichoice":

                  $("#grupo_edit").val(element.data("grupo"));
                  $("#label_tag_multichoice").text("@" + element.data("id") + "@");
                  if (element.data("required") === "1")
                        $("#required_multichoice").attr('checked', true);
                  else
                        $("#required_multichoice").attr('checked', false);
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
                  $("#grupo_edit").val(element.data("grupo"));
                  $("#label_tag_textfield").text("@" + element.data("id") + "@");
                  if (element.data("required") === "1")
                        $("#required_textfield").attr('checked', true);
                  else
                        $("#required_textfield").attr('checked', false);
                  $("#textfield_edit").val(element.find(".label_geral")[0].innerHTML);
                  break;


            case "tableradio":
                  $("#grupo_edit").val(element.data("grupo"));
                  $("#label_tag_tableradio").text("@" + element.data("id") + "@");
                  if (element.data("required") === "1")
                        $("#required_tableradio").attr('checked', true);
                  else
                        $("#required_tableradio").attr('checked', false);
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

      }
}


function edit_element(opcao, element, data)
{

      switch (opcao)
      {
            case "texto":

                  $("#texto_edit").val($("#texto_edit").val().replace(regex_replace_textbox, ''));
                  $("#placeholder_edit").val($("#placeholder_edit").val().replace(regex_replace_textbox, ''));

                  $("#max_length_edit").val($("#max_length_edit").val().replace(/[^0-9]/g, ''));

                  if ($("#required_texto").is(':checked'))
                        element.data("required", "1");
                  else
                        element.data("required", "0");

                  element.find(".label_geral")[0].innerHTML = $("#texto_edit").val();
                  element.find(".input_texto")[0].placeholder = $("#placeholder_edit").val();
                  element.find(".input_texto")[0].maxLength = $("#max_length_edit").val();


                  $("#grupo_edit").val($("#grupo_edit").val().replace(/[^0-9]/g, ''));
                  element.data("grupo", $("#grupo_edit").val());



                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "texto", $("#grupo_edit").val(), element.index(), "h", $("#texto_edit").val(), $("#placeholder_edit").val(), $("#max_length_edit").val(), 0, $("#required_texto").is(':checked'));

                  break;
            case "password":

                  if ($("#required_password").is(':checked'))
                        element.data("required", "1");
                  else
                        element.data("required", "0");


                  $("#passwordtexto_edit").val($("#passwordtexto_edit").val().replace(regex_replace_textbox, ''));
                  $("#passwordplaceholder_edit").val($("#passwordplaceholder_edit").val().replace(regex_replace_textbox, ''));
                  $("#passwordmax_length_edit").val($("#passwordmax_length_edit").val().replace(/[^0-9]/g, ''));
                  element.find(".label_geral")[0].innerHTML = $("#passwordtexto_edit").val();
                  element.find(".input_texto_password")[0].placeholder = $("#passwordplaceholder_edit").val();
                  element.find(".input_texto_password")[0].maxLength = $("#passwordmax_length_edit").val();

                  $("#grupo_edit").val($("#grupo_edit").val().replace(/[^0-9]/g, ''));
                  element.data("grupo", $("#grupo_edit").val());
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "password", $("#grupo_edit").val(), element.index(), "h", $("#passwordtexto_edit").val(), $("#passwordplaceholder_edit").val(), $("#passwordmax_length_edit").val(), 0, $("#required_password").is(':checked'));

                  break;

            case "radio":
                  if ($("#required_radio").is(':checked'))
                        element.data("required", "1");
                  else
                        element.data("required", "0");

                  if ($("#vertic_radio").is(':checked'))
                        element.data("dispo", "v");
                  else
                        element.data("dispo", "h");

                  element.empty();
                  $("#radio_edit").val($("#radio_edit").val().replace(regex_replace_textbox, ''));
                  element.append($("<label>").addClass("label_radio label_geral").text($("#radio_edit").val()));
                  element.append($("<br>"));
                  $("#radio_textarea").val($("#radio_textarea").val().replace(regex_replace, ''));
                  var radios = $("#radio_textarea").val().split(regex_split);
                  for (var count = 0; count < radios.length; count++)
                  {
                        element.append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "radio").attr("name", element.data("id")));
                        element.append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "radio").text(radios[count]).append($("<span>")));

                        if (element.data("dispo") === "v")
                              element.append($("<br>"));
                        array_id["radio"] = array_id["radio"] + 1;
                  }


                  $("#grupo_edit").val($("#grupo_edit").val().replace(/[^0-9]/g, ''));
                  element.data("grupo", $("#grupo_edit").val());
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "radio", $("#grupo_edit").val(), element.index(), element.data("dispo"), $("#radio_edit").val(), 0, 0, radios.join(","), $("#required_radio").is(':checked'));
                  break;
            case "checkbox":
                  if ($("#required_checkbox").is(':checked'))
                        element.data("required", "1");
                  else
                        element.data("required", "0");

                  if ($("#vertic_checkbox").is(':checked'))
                        element.data("dispo", "v");
                  else
                        element.data("dispo", "h");

                  element.empty();
                  $("#checkbox_edit").val($("#checkbox_edit").val().replace(regex_replace_textbox, ''));
                  element.append($("<label>").addClass("label_checkbox label_geral").text($("#checkbox_edit").val()));
                  element.append($("<br>"));
                  $("#checkbox_textarea").val($("#checkbox_textarea").val().replace(regex_replace, ''));
                  var checkboxs = $("#checkbox_textarea").val().split(regex_split);
                  for (var count = 0; count < checkboxs.length; count++)
                  {
                        element.append($("<input>").attr("type", "checkbox").attr("id", array_id["checkbox"] + "checkbox").attr("name", element.data("id")));
                        element.append($("<label>").addClass("checkbox_name").attr("for", array_id["checkbox"] + "checkbox").text(checkboxs[count]).append($("<span>")));

                        if (element.data("dispo") === "v")
                              element.append($("<br>"));
                        array_id["checkbox"] = array_id["checkbox"] + 1;
                  }

                  $("#grupo_edit").val($("#grupo_edit").val().replace(/[^0-9]/g, ''));
                  element.data("grupo", $("#grupo_edit").val());
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "checkbox", $("#grupo_edit").val(), element.index(), element.data("dispo"), $("#checkbox_edit").val(), 0, 0, checkboxs.join(","), $("#required_checkbox").is(':checked'));


                  break;
            case "multichoice":

                  if ($("#required_multichoice").is(':checked'))
                        element.data("required", "1");
                  else
                        element.data("required", "0");

                  element.empty();
                  $("#multichoice_edit").val($("#multichoice_edit").val().replace(regex_replace_textbox, ''));
                  element.append($("<label>").addClass("label_multichoice label_geral").text($("#multichoice_edit").val()));
                  $("#multichoice_textarea").val($("#multichoice_textarea").val().replace(regex_replace, ''));
                  var multichoices = $("#multichoice_textarea").val().split(regex_split);
                  element.append("<select class = 'multichoice_select' > < /select>");
                  var select = element.find(".multichoice_select");
                  for (var count = 0; count < multichoices.length; count++)
                  {
                        select.append("<option value='" + multichoices[count] + "'>" + multichoices[count] + "</option>");
                  }

                  $("#grupo_edit").val($("#grupo_edit").val().replace(/[^0-9]/g, ''));
                  element.data("grupo", $("#grupo_edit").val());
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "multichoice", $("#grupo_edit").val(), element.index(), "h", $("#multichoice_edit").val(), 0, 0, multichoices.join(","), $("#required_multichoice").is(':checked'));



                  break;
            case "textfield":

                  if ($("#required_textfield").is(':checked'))
                        element.data("required", "1");
                  else
                        element.data("required", "0");

                  $("#textfield_edit").val($("#textfield_edit").val().replace(regex_replace_textbox, ''));
                  element.find(".label_geral")[0].innerHTML = $("#textfield_edit").val();

                  $("#grupo_edit").val($("#grupo_edit").val().replace(/[^0-9]/g, ''));
                  element.data("grupo", $("#grupo_edit").val());
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "textfield", $("#grupo_edit").val(), element.index(), "h", "textfield", 0, 0, $("#textfield_edit").val(), $("#required_textfield").is(':checked'));
                  break;
            case "tableradio":

                  if ($("#required_tableradio").is(':checked'))
                        element.data("required", "1");
                  else
                        element.data("required", "0");

                  var tr_head = element.find(".tr_head");
                  tr_head.empty();
                  $("#tableradio_th_textarea").val($("#tableradio_th_textarea").val().replace(regex_remove_blank, ""));
                  $("#tableradio_th_textarea").val($("#tableradio_th_textarea").val().replace(regex_replace, ''));
                  var titulos = $("#tableradio_th_textarea").val().split(regex_split);
                  tr_head.append($("<td>").text("*"));
                  for (var count = 0; count < titulos.length; count++)
                  {
                        tr_head.append($("<td>").text(titulos[count]));
                  }
                  var tr_body = element.find(".tr_body");
                  tr_body.empty();
                  $("#tableradio_td_textarea").val($("#tableradio_td_textarea").val().replace(regex_remove_blank, ""));
                  $("#tableradio_td_textarea").val($("#tableradio_td_textarea").val().replace(/[^a-zA-Z0-9éçã\s:@§]/g, ''));
                  var perguntas = $("#tableradio_td_textarea").val().split(regex_split);
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

                  $("#grupo_edit").val($("#grupo_edit").val().replace(/[^0-9]/g, ''));
                  element.data("grupo", $("#grupo_edit").val());
                  item_database("edit_item", selected_id, $("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "tableradio", $("#grupo_edit").val(), element.index(), "h", titulos.join(","), 0, 0, perguntas.join(","), $("#required_tableradio").is(':checked'));
                  break;


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
            case "password":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  element.find(".input_texto_password")[0].placeholder = data.placeholder;
                  element.find(".input_texto_password")[0].maxLength = data.max_length;

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
                  element.append($("<label>").addClass("label_multichoice label_geral").text($("#multichoice_edit").val()));
                  element.find(".label_multichoice")[0].innerHTML = data.texto;
                  var multichoices = data.values_text.split(",");
                  element.append("<select class = 'multichoice_select' > < /select>");
                  var select = element.find(".multichoice_select");
                  for (var count = 0; count < multichoices.length; count++)
                  {
                        select.append("<option value='" + multichoices[count] + "'>" + multichoices[count] + "</option>");
                  }
                  break;
            case "textfield":
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  break;
            case "tableradio":
                  var tr_head = element.find(".tr_head");
                  tr_head.empty();
                  var titulos = data.texto.split(",");
                  tr_head.append($("<td>").text("*"));
                  for (var count = 0; count < titulos.length; count++)
                  {
                        tr_head.append($("<td>").text(titulos[count]));
                  }
                  var tr_body = element.find(".tr_body");
                  tr_body.empty();
                  var perguntas = data.values_text.split(",");
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


      }
}



function item_database(opcao, Id, Id_script, Id_page, Type, Grupo, Ordem, Dispo, Texto, Placeholder, Max_length, Values_text, Required)
{
      $.post("requests.php", {action: opcao,
            id: Id,
            id_script: Id_script,
            id_page: Id_page,
            type: Type,
            grupo: Grupo,
            ordem: Ordem,
            dispo: Dispo,
            texto: Texto,
            placeholder: Placeholder,
            max_length: Max_length,
            values_text: Values_text,
            required: Required},
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
            if (opcao === "delete_item")
                  update_info();
            if (opcao === "edit_item")
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
      update_info();
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
      update_info();
});