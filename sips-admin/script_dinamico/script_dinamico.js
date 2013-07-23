

////definir tag
//passar todos os appends de html para .css/.append etc
//meter regexs
//    -done mas ainda falta tirar linhas em branco
//
//BD INTERACTION
//falta gravar
//    -text done
//falta ler
//    -text done
//    
//adicionar opção para o radio e check pra mostrar em linha horizontal ou vertical







/*
 * 
 $.each(data, function(index, value) {
 $("#script_selector").append("<option value=" + value + ">Script " + value + "</option>");
 });
 
 
 
 
 * 
 * 
 */



/*
 * id
 * id_script
 * id_pagina
 * type
 * ordem
 * texto
 * placeholder
 * max_length
 * values_text
 */


var id = 0;
var selected_id = 0;
var selected_type = "";
var array_id = [];
var regex_replace_textbox = /[^a-zA-Z0-9çã\s:]/g;

var regex_replace = /[^a-zA-Z0-9çã\n]/g;
var regex_split = /\n/g;
var pagina_count = 2;
var script_count = 2;

var script = [];
$(document).ready(function() {
      array_id["textbox"] = 0;
      array_id["radio"] = 0;
      array_id["checkbox"] = 0;
      array_id["multichoice"] = 0;
      // $(".rightDiv :input").attr("disabled", "disabled");
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
            beforeStop: function(event, ui) {
                  if (removeIntent == true) {
                        ui.item.remove();
                  }
            },
            receive: function(event, ui) {

                  if ($(this).data().uiSortable.currentItem.hasClass("texto_class"))
                  {
                        $(this).data().uiSortable.currentItem.attr("id", id).data("id", id).addClass("element").data("type", "texto");
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("password_class"))
                  {
                        $(this).data().uiSortable.currentItem.attr("id", id).data("id", id).addClass("element").data("type", "password");
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("radio_class"))
                  {
                        $(this).data().uiSortable.currentItem.attr("id", id).data("id", id).addClass("element").data("type", "radio");
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("checkbox_class"))
                  {
                        $(this).data().uiSortable.currentItem.attr("id", id).data("id", id).addClass("element").data("type", "checkbox");
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("multichoice_class"))
                  {
                        $(this).data().uiSortable.currentItem.attr("id", id).data("id", id).addClass("element").data("type", "multichoice");
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("textfield_class"))
                  {
                        $(this).data().uiSortable.currentItem.attr("id", id).data("id", id).addClass("element").data("type", "textfield");
                  }
                  if ($(this).data().uiSortable.currentItem.hasClass("tag_class"))
                  {
                        $(this).data().uiSortable.currentItem.attr("id", id).data("id", id).addClass("element").data("type", "tag");
                  }




                  id++;
            }
      });
      update_script();
      $(document).on("click", ".element", function(e) {
            $(".footer_save_cancel button").prop('disabled', false);

            $(".item").removeClass("helperPick");
            $(this).addClass("helperPick");
            $("#tabs").tabs("option", "active", 1);
            $(".editor_layout").hide();

            switch ($(this).data("type"))
            {
                  case "password":
                        $("#password_layout_editor").show();
                        populate_password_edit($(this));
                        break;
                  case "texto":
                        $("#text_layout_editor").show();
                        populate_text_edit($(this));
                        break;
                  case "radio":
                        $("#radio_layout_editor").show();
                        populate_radio_edit($(this));
                        break;
                  case "checkbox":
                        $("#checkbox_layout_editor").show();
                        populate_checkbox_edit($(this));
                        break;
                  case "multichoice":
                        $("#multichoice_layout_editor").show();
                        populate_multichoice_edit($(this));
                        break;
                  case "textfield":
                        $("#textfield_layout_editor").show();
                        populate_textfield_edit($(this));
                        break;
                  case "tag":
                        $("#tag_layout_editor").show();
                        populate_tag_edit($(this));
                        break;
            }
            selected_id = $(this).data("id");
            selected_type = $(this).data("type");
      });
});



$("#opcao_script_button").click(function()
{
      $("#script_name_edit").val($("#script_selector option:selected").text());

});

$("#save_button_layout").click(function()
{
      $.post("requests.php", {action: "edit_script_name", name: $("#script_name_edit").val(), id: $("#script_selector option:selected").val()},
      function(data)
      {
            $('#dialog_layout').modal('hide');
            update_script();

      }, "json");

});



//UPDATES DE INFO
function update_script()
{
      $.post("requests.php", {action: "get_scripts"},
      function(data)
      {
            $("#script_selector").empty();
            $.each(data, function(index, value) {
                  $("#script_selector").append("<option value=" + data[index].id + ">" + data[index].name + "</option>");
            });

            update_info();
      }, "json");
}
function update_info()
{
      $.post("requests.php", {action: "get_data", id_script: $("#script_selector option:selected").val()},
      function(data)
      {
            script = [];
            $.each(data, function(index, value) {
                  script.push([data[index].id, data[index].id_script, data[index].id_pagina, data[index].type, data[index].ordem, data[index].texto, data[index].placeholder, data[index].max_length, data[index].values_text]);


                  var item;
                  switch (data[index].type)
                  {
                        case "texto":
                              item = $('.texto_class').clone();
                              item.attr("id", data[index].id).data("id", data[index].id).addClass("element").data("type", "texto").appendTo('.leftDiv');
                              item.find(".label_geral")[0].innerHTML = data[index].texto;
                              item.find(".input_texto")[0].placeholder = data[index].placeholder;
                              item.find(".input_texto")[0].maxLength = data[index].max_length;
                              populate_text_edit(item);
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
});
$("#save_edit").click(function()
{
      var element = $("#" + selected_id);
      switch (selected_type)
      {
            case"password":
                  save_password_element(element);
                  break;
            case"texto":
                  save_text_element(element);
                  break;
            case "radio":
                  save_radio_element(element);
                  break;
            case "checkbox":
                  save_checkbox_element(element);
                  break;
            case "multichoice":
                  save_multichoice_element(element);
                  break;
            case "textfield":
                  save_textfield_element(element);
                  break;
            case "tag":
                  save_tag_element(element);
                  break;
      }



});


//PAGES
$("#page_add_button").click(function()
{
      $("#page_selector").append("<option value=" + pagina_count + ">Pagina " + pagina_count + "</option>");
      pagina_count++;
});

$("#page_remove_button").click(function()
{
      $("#page_selector option:selected").remove();

});
$('page_selector').change(function() {
//The option has been changed.
});



//SCRIPTS
$("#script_add_button").click(function()
{

      $.post("requests.php", {action: "add_script", name: "name"},
      function(data)
      {
      }, "json");
});

$("#script_remove_button").click(function()
{
      $("#script_selector option:selected").remove();

});

$('script_selector').change(function() {
      update_info();
});


//------------TEXT---------------------------------------------
function populate_text_edit(element)
{

      $("#texto_edit").val(element.find(".label_geral")[0].innerHTML);
      $("#placeholder_edit").val(element.find(".input_texto")[0].placeholder);
      $("#max_length_edit").val(element.find(".input_texto")[0].maxLength);

}
function save_text_element(element)
{
      $("#texto_edit").val($("#texto_edit").val().replace(regex_replace_textbox, ''));
      $("#placeholder_edit").val($("#placeholder_edit").val().replace(regex_replace_textbox, ''));
      $("#max_length_edit").val($("#max_length_edit").val().replace(/[^0-9]/g, ''));
      element.find(".label_geral")[0].innerHTML = $("#texto_edit").val();
      element.find(".input_texto")[0].placeholder = $("#placeholder_edit").val();
      element.find(".input_texto")[0].maxLength = $("#max_length_edit").val();
      save_item($("#script_selector option:selected").val(), $("#page_selector option:selected").val(), "texto", 1, $("#texto_edit").val(), $("#placeholder_edit").val(), $("#max_length_edit").val(), 0);

}
//------------PASSWORD--------------------------------------------
function populate_password_edit(element)
{
      $("#passwordtexto_edit").val(element.find(".label_geral")[0].innerHTML);
      $("#passwordplaceholder_edit").val(element.find(".input_texto_password")[0].placeholder);
      $("#passwordmax_length_edit").val(element.find(".input_texto_password")[0].maxLength);

}
function save_password_element(element)
{
      $("#passwordtexto_edit").val($("#passwordtexto_edit").val().replace(regex_replace_textbox, ''));
      $("#passwordplaceholder_edit").val($("#passwordplaceholder_edit").val().replace(regex_replace_textbox, ''));
      $("#passwordmax_length_edit").val($("#passwordmax_length_edit").val().replace(/[^0-9]/g, ''));
      element.find(".label_geral")[0].innerHTML = $("#passwordtexto_edit").val();
      element.find(".input_texto_password")[0].placeholder = $("#passwordplaceholder_edit").val();
      element.find(".input_texto_password")[0].maxLength = $("#passwordmax_length_edit").val();

}

//------------RADIO--------------------------------------------
function populate_radio_edit(element)
{
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
}

function save_radio_element(element)
{
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
            array_id["radio"] = array_id["radio"] + 1;
      }
}

//------------CHECKBOX--------------------------------------------
function populate_checkbox_edit(element)
{
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
}

function save_checkbox_element(element)
{
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


            array_id["checkbox"] = array_id["checkbox"] + 1;
      }
}
//------------MULTICHOICE--------------------------------------------
function populate_multichoice_edit(element)
{
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
}

function save_multichoice_element(element)
{
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
}
//------------TEXTFIELD-------------------------------------------
function populate_textfield_edit(element)
{
      $("#textfield_edit").val(element.find(".label_geral")[0].innerHTML);
}

function save_textfield_element(element)
{
      $("#textfield_edit").val($("#textfield_edit").val().replace(regex_replace_textbox, ''));
      element.find(".label_geral")[0].innerHTML = $("#textfield_edit").val();
}
//------------TAG-------------------------------------------
function populate_tag_edit(element)
{

}

function save_tag_element(element)
{

}





function save_item(Id_script, Id_pagina, Type, Ordem, Texto, Placeholder, Max_length, Values_text)
{
      $.post("requests.php", {action: "edit_item",
            id_script: Id_script,
            id_pagina:Id_pagina,
                    type: Type,
            ordem: Ordem,
            texto: Texto,
            placeholder: Placeholder,
            max_length: Max_length,
            values_text: Values_text},
      function(data)
      {
            update_info();

      }
      , "json");
}