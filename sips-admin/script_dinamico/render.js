var array_id = [];
var items = [];
var tag_regex = /\@([^@]+)\@/;
$(function() {
    
      array_id["radio"] = 0;
      array_id["checkbox"] = 0;
      update_script();
});




/*
                  if (data.texto.match(tag_regex))
                  {


                        var temp = data.texto.match(tag_regex);
                        var temp2 = $("#" + temp[1]);
console.log($("#" + temp[1]+" .val").name);

                        data.texto = data.texto.replace(tag_regex, $("#" + temp[1]).val());
                  }
*/






function insert_element(opcao, element, data)
{

      switch (opcao)
      {
            case "texto":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  element.find(".input_texto")[0].placeholder = data.placeholder;
                  element.find(".input_texto")[0].maxLength = data.max_length;
                  element.find(".input_texto")[0].name = "texto," + data.id;
                  if (data.required === "1")
                        element.find(".input_texto").prop("required", true);
                  break;
                  
                  
            case "radio":
                  element.empty();
                  element.append($("<div>").addClass("radio_class item form-inline"));
                  element = element.find(".radio_class");
                  element.append($("<label>").addClass("label_radio label_geral").text($("#radio_edit").val()));
                  element.append($("<br>"));
                  element.find(".label_radio")[0].innerHTML = data.texto;
                  var radios = data.values_text.split(",");
                  for (var count = 0; count < radios.length; count++)
                  {
                        if (data.required === "1")
                              element.append($("<input>").attr("type", "radio").prop("required", true).attr("value", radios[count]).attr("id", array_id["radio"] + "radio").attr("name", "radio," + data.id));
                        else
                              element.append($("<input>").attr("type", "radio").attr("value", radios[count]).attr("id", array_id["radio"] + "radio").attr("name", "radio," + data.id));
                        element.append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "radio").text(radios[count]).append($("<span>")));
                        if (data.dispo === "v")
                              element.append($("<br>"));
                        array_id["radio"] = array_id["radio"] + 1;
                  }
                  break;
                  
                  
            case "checkbox":
                  element.empty();
                  element.append($("<div>").addClass("checkbox_class item form-inline"));
                  element = element.find(".checkbox_class");
                  element.append($("<label>").addClass("label_checkbox label_geral").text($("#checkbox_edit").val()));
                  element.append($("<br>"));
                  element.find(".label_checkbox")[0].innerHTML = data.texto;
                  var checkboxs = data.values_text.split(",");
                  for (var count = 0; count < checkboxs.length; count++)
                  {
                        if (data.required === "1")
                              element.append($("<input>").attr("type", "checkbox").prop("required", true).attr("value", checkboxs[count]).attr("id", array_id["checkbox"] + "checkbox").attr("name", "checkbox," + data.id));
                        else
                              element.append($("<input>").attr("type", "checkbox").attr("value", checkboxs[count]).attr("id", array_id["checkbox"] + "checkbox").attr("name", "checkbox," + data.id));
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
                  element.append($("<select>").addClass("multichoice_select").attr("name", "multichoice," + data.id));
                  var select = element.find(".multichoice_select");
                  for (var count = 0; count < multichoices.length; count++)
                  {
                        select.append("<option value='" + multichoices[count] + "'>" + multichoices[count] + "</option>");
                  }
                  break;
                  
                  
            case "textfield":
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  element.find(".label_geral")[0].name = "textfield," + data.id;
                  break;
                  
                  
            case "tableradio":
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
                  for (var count = 0; count < perguntas.length; count++)
                  {
                        tr_body.append($("<tr>")
                                .append($("<td>").text(perguntas[count]).addClass("td_row")));
                        temp = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                              if (data.required === "1") {
                                    temp.append($("<td>")
                                            .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").prop("required", true).attr("value", titulos[count2]).attr("name", "tableradio," + data.id + "," + count))
                                            .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio").append($("<span>"))));
                                    array_id["radio"] = array_id["radio"] + 1;
                              } else
                              {
                                    temp.append($("<td>")
                                            .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").attr("value", titulos[count2]).attr("name", "tableradio," + data.id + "," + count))
                                            .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio").append($("<span>"))));
                                    array_id["radio"] = array_id["radio"] + 1;
                              }
                        }
                  }
                  break;
      }
}








//UPDATES DE INFO
function update_script()
{
      $.post("requests.php", {action: "get_scripts"},
      function(data)
      {

            if (data == null)

            {
                  alert("no data");
            }
            else
            {


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
                  alert("no page");
            }
            else
            {
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
      $.post("requests.php", {action: "get_data", id_script: $("#script_selector option:selected").val(), id_page: $("#page_selector option:selected").val()},
      function(data)
      {

            $("#script_div").empty();
            $("#render_zone").append($("<div>").attr("id", "dummie"));
            var dummie = $("#dummie");
            $.each(data, function(index, value) {

                  items.push(data[index]);

                  switch (data[index].type)
                  {
                        case "texto":
                              var item = dummie.load('index.html .texto_class', function() {
                                    item.attr("id", data[index].id)
                                            .data("id", data[index].id)
                                            .data("required", data[index].required)
                                            .data("type", "texto");
                                    insert_element("texto", item, data[index]);
                                    $('#script_div').append(item[0].innerHTML);
                              });
                              break;

                        case "pagination":
                              var item = dummie.load('index.html .pagination_class', function() {


                                    item.attr("id", data[index].id)
                                            .data("id", data[index].id)
                                            .data("required", data[index].required)
                                            .data("type", "pagination");

                                    $('#script_div').append(item[0].innerHTML);
                              });
                              break;

                        case "radio":
                              var item = dummie.load('index.html .radio_class', function() {
                                    item.attr("id", data[index].id)
                                            .data("id", data[index].id)
                                            .data("required", data[index].required)
                                            .data("type", "radio")
                                            .data("dispo", data[index].dispo);
                                    insert_element("radio", item, data[index]);
                                    $('#script_div').append(item[0].innerHTML);
                              });
                              break;
                        case "checkbox":
                              var item = dummie.load('index.html .checkbox_class', function() {
                                    item.attr("id", data[index].id)
                                            .data("id", data[index].id)
                                            .data("required", data[index].required)
                                            .data("dispo", data[index].dispo)
                                            .data("type", "checkbox");
                                    insert_element("checkbox", item, data[index]);
                                    $('#script_div').append(item[0].innerHTML);
                              });
                              break;
                        case "multichoice":
                              var item = dummie.load('index.html .multichoice_class', function() {
                                    item.attr("id", data[index].id)
                                            .data("id", data[index].id)
                                            .data("required", data[index].required)
                                            .data("type", "multichoice");
                                    insert_element("multichoice", item, data[index]);
                                    $('#script_div').append(item[0].innerHTML);
                              });
                              break;
                        case "textfield":
                              var item = dummie.load('index.html .textfield_class', function() {
                                    item.attr("id", data[index].id)
                                            .data("id", data[index].id)
                                            .data("required", data[index].required)
                                            .data("type", "textfield");
                                    insert_element("textfield", item, data[index]);
                                    $('#script_div').append(item[0].innerHTML);
                              });
                              break;
                        case "tableradio":
                              var item = dummie.load('index.html .tableradio_class', function() {
                                    item.attr("id", data[index].id)
                                            .data("id", data[index].id)
                                            .data("required", data[index].required)
                                            .data("type", "tableradio");
                                    insert_element("tableradio", item, data[index]);
                                    $('#script_div').append(item[0].innerHTML);
                              });
                              break;
                  }


                  dummie.remove();
            });
      
      }, "json");
}



//PAGES
$('#page_selector').change(function() {
      update_info();
});
//SCRIPTS
$('#script_selector').change(function() {
      update_pages();
});
//FORM MANIPULATION
$("#submit_button").click(function()
{

      $.post("requests.php", {action: "save_form_result", id_script: $('#script_selector').val(), results: $("#myform").serializeArray()},
      function(data)
      {

      }, "json");
});


