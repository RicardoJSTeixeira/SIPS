


var array_id = [];
var tag_regex = /\@([^@]+)\@/g;
$(function() {
      array_id["radio"] = 0;
      array_id["checkbox"] = 0;
      $.get("items.html", function(data) {
            $("#dummie").html(data);
            update_script();
      });
});










function insert_element(opcao, element, data)
{
      $('#script_div').append(element);
      switch (opcao)
      {
            case "texto":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  element.find(".input_texto")[0].placeholder = data.placeholder;
                  element.find(".input_texto")[0].maxLength = data.max_length;
                  element.find(".input_texto")[0].name = "texto," + data.id;
                  if (data.required)
                        element.find(".input_texto").prop("required", true);
                  break;


            case "radio":
                  element.empty();
                  element.append($("<div>").addClass("item radio_class form-inline").attr("id", data.id));
                  element = element.find(".radio_class");
                  element.append($("<label>").addClass("label_radio label_geral").text($("#radio_edit").val()));
                  element.append($("<br>"));
                  element.find(".label_radio")[0].innerHTML = data.texto;
                  var radios = data.values_text;
                  for (var count = 0; count < radios.length; count++)
                  {
                        if (data.required)
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
                  element.append($("<div>").addClass("item checkbox_class form-inline").attr("id", data.id));
                  element = element.find(".checkbox_class");
                  element.append($("<label>").addClass("label_checkbox label_geral").text($("#checkbox_edit").val()));
                  element.append($("<br>"));
                  element.find(".label_checkbox")[0].innerHTML = data.texto;
                  var checkboxs = data.values_text;
                  for (var count = 0; count < checkboxs.length; count++)
                  {
                        if (data.required)
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
                  element.append($("<div>").addClass("item multichoice_class").attr("id", data.id));
                  element = element.find(".multichoice_class");
                  element.append($("<label>").addClass("label_multichoice label_geral").text(data.texto));
                  var multichoices = data.values_text;
                  if (data.required)
                        element.append($("<select>").addClass("multichoice_select").attr("name", "multichoice," + data.id).prop("required", true));
                  else
                        element.append($("<select>").addClass("multichoice_select").attr("name", "multichoice," + data.id));
                  var select = element.find(".multichoice_select");
                  var options = "<option value=''>Selecione uma opção</option>";
                  for (var count = 0; count < multichoices.length; count++)
                  {
                        options += "<option value='" + multichoices[count] + "'>" + multichoices[count] + "</option>";
                  }
                  select.append(options);
                  break;


            case "textfield":
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  element.find(".label_geral")[0].name = "textfield," + data.id;
                  break;

            case "legend":
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  element.find(".label_geral")[0].name = "legend," + data.id;
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
                  var perguntas = data.values_text;
                  for (var count = 0; count < perguntas.length; count++)
                  {
                        tr_body.append($("<tr>")
                                .append($("<td>").text(perguntas[count]).addClass("td_row")));
                        temp = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                              if (data.required)
                              {
                                    temp.append($("<td>")
                                            .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").prop("required", true).attr("value", titulos[count2]).attr("name", "tableradio," + data.id + "," + count))
                                            .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio").append($("<span>"))));
                              }
                              else
                              {
                                    temp.append($("<td>")
                                            .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").attr("value", titulos[count2]).attr("name", "tableradio," + data.id + "," + count))
                                            .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio").append($("<span>"))));
                              }
                              array_id["radio"] = array_id["radio"] + 1;
                        }
                  }
                  break;


            case "datepicker":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  if (data.required)
                        element.find(".form_datetime").prop("required", true);
                  element.find(".form_datetime")[0].name = "datepicker," + data.id;

                  break;
      }


      if (data.hidden)
            element.css("display", "none");



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
                        $("#script_selector").append("<option value=" + this.id + ">" + this.name + "</option>");
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
                        if (pag === this.id)
                              $("#page_selector").append("<option value=" + this.id + " selected>" + this.name + "</option>");
                        else
                              $("#page_selector").append("<option value=" + this.id + ">" + this.name + "</option>");
                  });
                  update_info();
            }
      }, "json");
}
function update_info()
{
         $(".datetimepicker").remove();
      $("#page_title").text($("#script_selector option:selected").text() + "---" + $("#page_selector option:selected").text());

      $.post("requests.php", {action: "get_data", id_script: $("#script_selector option:selected").val(), id_page: $("#page_selector option:selected").val()},
      function(data)
      {

            $("#script_div").empty();
            $.each(data, function(index, value) {
                  var item;
                  switch (this.type)
                  {
                        case "texto":
                              item = $('#dummie .texto_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .addClass("element")
                                      .data("required", this.required)
                                      .data("type", "texto");
                              insert_element("texto", item, this);
                              break;


                        case "pagination":
                              item = $('#dummie .pagination_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "pagination");
                              insert_element("pagination", item, this);
                              break;


                        case "radio":
                              item = $('#dummie .radio_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "radio")
                                      .data("dispo", this.dispo);
                              insert_element("radio", item, this);
                              break;


                        case "checkbox":
                              item = $('#dummie .checkbox_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("dispo", this.dispo)
                                      .data("type", "checkbox");
                              insert_element("checkbox", item, this);
                              break;


                        case "multichoice":
                              item = $('#dummie .multichoice_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "multichoice");
                              insert_element("multichoice", item, this);
                              break;


                        case "textfield":
                              item = $('#dummie .textfield_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "textfield");
                              insert_element("textfield", item, this);
                              break;


                        case "legend":
                              item = $('#dummie .legend_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "legend");
                              insert_element("legend", item, this);
                              break;


                        case "tableradio":
                              item = $('#dummie .tableradio_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "tableradio");
                              insert_element("tableradio", item, this);
                              break;


                        case "datepicker":
                              item = $('#dummie .datepicker_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "datepicker");
                              insert_element("datepicker", item, this);
                              break;
                  }
            });
            tags();
            rules();
         
            $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii'}).keypress(function(e) {
                  e.preventDefault();
            }).bind("cut copy paste", function(e) {
                  e.preventDefault();
            });

      }, "json");




}



function rules()
{
      $.post("requests.php", {action: "get_rules", id_script: $("#script_selector option:selected").val()},
      function(data)
      {
            $.each(data, function(index, value) {
                  switch (this.tipo_elemento)
                  {
                        case "texto":
                              switch (this.param1)
                              {
                                    case "value_input":
                                          $("#" + this.id_trigger).bind("keyup", function()//atribuir os binds a cada value
                                          {
                                                var pattern = new RegExp('\\b' + data[index].id_trigger2, 'i');
                                                if ($("#" + data[index].id_trigger + " input").val().match(pattern))
                                                {
                                                      rules_work(data[index]);
                                                }
                                          }
                                          );
                                          break;
                                    case "answer":
                                          $("#" + this.id_trigger).bind("focusout", function()//atribuir os binds a cada value
                                          {
                                                rules_work(data[index]);
                                          }
                                          );
                                          break;
                              }
                              break;


                        case "radio":
                              switch (this.param1)
                              {
                                    case "value_select":
                                          var values = this.id_trigger2.split(",");
                                          for (var count = 0; count < values.length; count++)
                                          {
                                                $("#" + this.id_trigger).find("input[value='" + values[count] + "']").bind("click", function()//atribuir os binds a cada value
                                                {
                                                      rules_work(data[index]);
                                                }
                                                );
                                          }
                                          break;
                              }
                              break;


                        case "checkbox":
                              switch (this.param1)
                              {
                                    case "value_select":
                                          var values = this.id_trigger2.split(",");
                                          for (var count = 0; count < values.length; count++)
                                          {
                                                $("#" + this.id_trigger).find("input[value='" + values[count] + "']").bind("click", function()//atribuir os binds a cada value
                                                {
                                                      rules_work(data[index]);
                                                }
                                                );
                                          }
                                          break;
                              }
                              break;


                        case"multichoice":
                              switch (this.param1)
                              {
                                    case "value_select":
                                          var values = this.id_trigger2.split(",");
                                          for (var count = 0; count < values.length; count++)
                                          {
                                                $("#" + this.id_trigger).bind("change", function()//atribuir os binds a cada value
                                                {
                                                      if ($("#" + data[index].id_trigger + " option:selected").val() === data[index].id_trigger2)
                                                            rules_work(data[index]);
                                                }
                                                );
                                          }
                                          break;
                              }
                              break;


                        case "tableradio":
                              switch (this.param1)
                              {
                                    case "value_select":
                                          var linhas = this.id_trigger2.split(",");
                                          for (var count = 0; count < linhas.length; count++)
                                          {
                                                var values = linhas[count].split(";");
                                                $("#" + this.id_trigger).find("tr:contains('" + values[0] + "') input[value='" + values[1] + "']").bind("click", function()//atribuir os binds a cada value
                                                {
                                                      rules_work(data[index]);
                                                }
                                                );
                                          }
                                          break;
                                    case "answer":
                                          $("#" + this.id_trigger).find("input").on("click", function()
                                          {
                                                if ($("#" + data[index].id_trigger).find("input:checked").length === ($("#" + data[index].id_trigger).find("tr").length - 1))
                                                      rules_work(data[index]);
                                          });
                                          break;
                             }
                              break;


                        case "datepicker":
                              switch (this.param1)
                              {
                                    case "answer":
                                          $("#" + this.id_trigger).bind("change", function()//atribuir os binds a cada value
                                          {
                                                rules_work(data[index]);
                                          }
                                          );
                                          break;
                              }
                              break;
                  }
            });
      }
      , "json");
}
function rules_work(data)
{
      switch (data.tipo)
      {
            case "hide":
                  var target = data.id_target.split(",");
                  for (var count2 = 0; count2 < target.length; count2++)
                  {

                        $("#" + target[count2]).hide();
                  }
                  break;

            case "show":
                  var target = data.id_target.split(",");
                  for (var count2 = 0; count2 < target.length; count2++)
                  {
                        $("#" + target[count2]).show();
                  }
                  break;

            case "goto":
                  var param = data.param2.split(",");
                  switch (param[0])
                  {
                        case "pag":
                              $("#page_selector option[value='" + param[1] + "']").attr("selected", "selected");
                              $("#page_selector").trigger("change");
                              break;
                  }
                  break;
      }
}
function tags()
{
      var rz = $("#render_zone");
      if (rz.html().match(tag_regex))
      {
            var temp2 = rz.html().match(tag_regex);
            var temp = [];
            $.each(temp2, function() {
                  if ($.inArray(this, temp) === -1)
                        temp.push(this);
            });
            $.each(temp, function() {
                  var id = this;
                  id = id.replace(/\@/g, '');
                  var regExp = new RegExp(this, "g");
                  rz.html(rz.html().replace(regExp, "<span class='" + id + "tag'></span>"));
                  $(document).on("change", "#" + id + " input,#" + id + " select", function() {
                        $("." + id + "tag").text($(this).val());
                  });
            });
      }
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
$("#myform").on("submit", function(e)
{
      e.preventDefault();
      var result = $("#myform").serializeArray();
      $.post("requests.php", {action: "save_form_result", id_script: $('#script_selector').val(), results: result}, "json");
});


