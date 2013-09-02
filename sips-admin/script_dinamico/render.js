

//licença: 1339
//operador:blitz
//pass: 1234
//sflphone

var array_id = [];
var tag_regex = /\@([^@]+)\@/g;
var page_info = 0;
var items = [];
$(function() {
      array_id["radio"] = 0;
      array_id["checkbox"] = 0;
      $.get("items.html", function(data) {
            page_info = getUrlVars();
            $("#dummie").html(data);
            update_script();
      });
});





function insert_element(opcao, element, data)
{
      element.removeAttr("title");
      switch (opcao)
      {
            case "texto":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  var input = element.find(".input_texto")[0];
                  input.placeholder = data.placeholder;
                  input.maxLength = data.max_length;
                  input.name = data.id;

                  var pattern = [];
                  if (data.required)
                        pattern.push("required");
                  switch (data.param1)
                  {
                        case "normal":
                              pattern.push("[custom[onlyLetNumSpec]]");
                              break;
                        case "letter":
                              pattern.push("[custom[onlyLetterSp]]");
                              break;
                        case "email":
                              pattern.push("[custom[email]]");
                              break;
                        case "postal":
                              pattern.push("[custom[postcodePT]]");
                              break;
                        case "nib":
                              pattern.push("funcCall[checknib]");
                              break;
                        case "nif":
                              pattern.push("funcCall[checknif]");
                              break;
                  }
                  element.find(".input_texto").addClass("validate[" + pattern.join(",") + "]");

                  break;
            case "radio":
                  element.empty();
                  element.append($("<label>").addClass("label_radio label_geral").text($("#radio_edit").val()));
                  element.find(".label_radio")[0].innerHTML = data.texto;
                  var radios = data.values_text;
                  for (var count = 0; count < radios.length; count++)
                  {
                        if (data.required)
                              element.append($("<label>")
                                      .addClass("radio_name radio inline")
                                      .attr("for", array_id["radio"] + "radio")
                                      .text(radios[count]).append($("<input>")
                                      .attr("type", "radio")
                                      .addClass("validate[required]")
                                      .attr("value", radios[count])
                                      .attr("id", array_id["radio"] + "radio")
                                      .attr("name", data.id))
                                      );
                        else
                              element.append($("<label>")
                                      .addClass("radio_name radio inline")
                                      .attr("for", array_id["radio"] + "radio")
                                      .text(radios[count]).append($("<input>")
                                      .attr("type", "radio")
                                      .attr("value", radios[count])
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

                        if (data.required)
                              element.append($("<label>")
                                      .addClass("checkbox_name checkbox inline")
                                      .attr("for", array_id["checkbox"] + "checkbox")
                                      .text(checkboxs[count]).
                                      append($("<input>")
                                      .attr("type", "checkbox")
                                      .addClass("validate[minCheckbox[1]]")
                                      .attr("value", checkboxs[count])
                                      .attr("id", array_id["checkbox"] + "checkbox")
                                      .attr("name", data.id))
                                      );
                        else
                              element.append($("<label>")
                                      .addClass("checkbox_name checkbox inline")
                                      .attr("for", array_id["checkbox"] + "checkbox")
                                      .text(checkboxs[count])
                                      .append($("<input>")
                                      .attr("type", "checkbox")
                                      .attr("value", checkboxs[count])
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
                  if (data.required)
                        element.append($("<select>").addClass("multichoice_select validate[required]").attr("name", data.id));
                  else
                        element.append($("<select>").addClass("multichoice_select").attr("name", data.id));
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
                  element.find(".label_geral")[0].name = data.id;
                  break;
            case "legend":
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  element.find(".label_geral")[0].name = data.id;
                  break;
            case "pagination":

                  element.find("#previous_pag").bind("click", function()
                  {
                        var temp = $(".pag_div:visible").prev(".pag_div");
                        if (temp.length)
                        {
                              $(".pag_div").hide();
                              temp.show();
                        }
                  });
                  element.find("#next_pag").bind("click", function()
                  {

                        var temp = $(".pag_div:visible").next(".pag_div");
                        if (temp.length)
                        {
                              $(".pag_div").hide();
                              temp.show();
                        }
                  });
                  break;
            case "tableradio":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  var tr_head = element.find(".tr_head");
                  tr_head.empty();
                  var titulos = data.placeholder;
                  tr_head.append($("<td>").text("*"));
                  for (var count = 0; count < titulos.length; count++)
                  {
                        tr_head.append($("<td>").text(titulos[count]));
                  }
                  var tr_body = element.find(".tr_body");
                  tr_body.empty();
                  var perguntas = data.values_text;
                  var trbody_last;
                  for (var count = 0; count < perguntas.length; count++)
                  {
                        tr_body.append($("<tr>")
                                .append($("<td>").text(perguntas[count]).addClass("td_row")));
                        trbody_last = element.find(".tr_body tr:last");
                        for (var count2 = 0; count2 < titulos.length; count2++)
                        {
                              if (data.required)
                              {
                                    trbody_last.append($("<td>")
                                            .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").addClass("validate[required]").attr("value", titulos[count2]).attr("name", data.id + "," + count))
                                            .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio")));
                              }
                              else
                              {
                                    trbody_last.append($("<td>")
                                            .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").attr("value", titulos[count2]).attr("name", data.id + "," + count))
                                            .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio")));
                              }
                              array_id["radio"] = array_id["radio"] + 1;
                        }
                  }
                  break;
            case "datepicker":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  if (data.required)
                        element.find(".form_datetime").addClass("validate[required]");
                  element.find(".form_datetime")[0].name = data.id;
                  break;
      }
      if (data.hidden)
            element.css("display", "none");
}

function populate_script()
{
      $.post("requests.php", {action: "get_results_to_populate", lead_id: page_info.lead_id},
      function(data)
      {
            if (data !== null)
            {
                  $.each(data, function(index, value) {

                        switch (this.type)
                        {
                              case "texto":
                                    $("#" + this.id_elemento + " :input").val(this.valor);
                                    break;
                              case "radio":
                              case "checkbox":
                                    $("#" + this.id_elemento + " :input[value='" + this.valor + "']").attr('checked', true);
                                    break;
                              case "multichoice":
                                    $("#" + this.id_elemento + " :input").val(this.valor);
                                    break;
                              case "tableradio":

                                    var temp = this.id_elemento.split(",");
                                    $("#" + temp[0] + " tbody tr:eq(" + temp[1] + ") :input[value='" + this.valor + "']").attr('checked', true);
                                    break;
                              case "datepicker":
                                    $("#" + this.id_elemento + " :input").val(this.valor);
                                    break;
                        }
                  });
            }

      }, "json");
}


//UPDATES DE INFO
function update_script()
{




      if (page_info.script_id !== undefined)
      {
            $.post("requests.php", {action: "get_scripts_by_id_script", id_script: page_info.script_id},
            function(data)
            {
                  if (data !== null)
                  {
                        page_info.script_id = data.id;
                        update_info();
                  }

            }, "json");
      }
      else
      {
            var camp_linha = 0;
            if (page_info.in_group_id !== "")
                  camp_linha = page_info.in_group_id;
            else if (page_info.campaign_id !== "")
                  camp_linha = page_info.campaign_id;


            $.post("requests.php", {action: "get_scripts_by_campaign", id_campaign: camp_linha},
            function(data)
            {
                  if (data !== null)
                  {
                        page_info.script_id = data.id;
                        update_info();
                  }

            }, "json");
      }


}

function update_info()
{
      $(".datetimepicker").remove();
      $.post("requests.php", {action: "get_data_render", id_script: page_info.script_id},
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
                                      .data("required", this.required)
                                      .data("type", "texto");

                              items.push([item, this.id_page]);
                              break;
                        case "pagination":
                              item = $('#dummie .pagination_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "pagination");
                              items.push([item, this.id_page]);
                              break;
                        case "radio":
                              item = $('#dummie .radio_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "radio")
                                      .data("dispo", this.dispo);
                              items.push([item, this.id_page]);
                              break;
                        case "checkbox":
                              item = $('#dummie .checkbox_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("dispo", this.dispo)
                                      .data("type", "checkbox");
                              items.push([item, this.id_page]);
                              break;
                        case "multichoice":
                              item = $('#dummie .multichoice_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "multichoice");
                              items.push([item, this.id_page]);

                              break;
                        case "textfield":
                              item = $('#dummie .textfield_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "textfield");
                              items.push([item, this.id_page]);
                              break;
                        case "legend":
                              item = $('#dummie .legend_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "legend");
                              items.push([item, this.id_page]);
                              break;
                        case "tableradio":
                              item = $('#dummie .tableradio_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "tableradio");
                              items.push([item, this.id_page]);
                              break;
                        case "datepicker":
                              item = $('#dummie .datepicker_class').clone();
                              item.attr("id", this.id)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "datepicker");
                              items.push([item, this.id_page]);
                              break;
                  }

                  insert_element(this.type, item, this);


            });


            $("#myform").validationEngine();




//FAZER O APPEND DOS ITEMS A LISTA
            $.each(items, function()
            {
                  if (!$("#" + this[1] + "pag").length) {
                        $("#script_div").append($("<div>").addClass("pag_div").attr("id", this[1] + "pag"));
                  }
                  $("#" + this[1] + "pag").append(this[0]);
            });
            $(".pag_div").hide().first().show();
            $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true}).keypress(function(e) {
                  e.preventDefault();
            }).bind("cut copy paste", function(e) {
                  e.preventDefault();
            });
            rules();
            tags();
            populate_script();

      }, "json");
}


//RULES 
function rules_work(data)
{
      switch (data.tipo)
      {
            case "hide":
                  var target = data.id_target;
                  for (var count2 = 0; count2 < target.length; count2++)
                  {
                        $("#" + target[count2]).fadeOut(400);
                  }
                  break;
            case "show":
                  var target = data.id_target;
                  for (var count2 = 0; count2 < target.length; count2++)
                  {
                        $("#" + target[count2]).fadeIn(400);
                  }
                  break;
            case "goto":
                  $(".pag_div").hide();
                  $("#" + data.param2 + "pag").show();
                  break;
      }
}
function rules()
{
      $.post("requests.php", {action: "get_rules", id_script: page_info.script_id},
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
                                          var values = this.id_trigger2;
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
                                          var values = this.id_trigger2;
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
                                          var values = this.id_trigger2;
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
                                          var linhas = this.id_trigger2;
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



//FORM MANIPULATION
$("#myform").on("submit", function(e)
{
      e.preventDefault();
});
function submit_manual()
{
      $.post("requests.php", {action: "save_form_result", id_script: page_info.script_id, results: $("#myform").serializeArray(), user_id: page_info.user_id, unique_id: page_info.unique_id, campaign_id: page_info.campaign_id, lead_id: page_info.lead_id}, function() {
            return true;
      }, "json").fail(function() {
            return false;
      });
}

function validate_manual()
{
      return $("#myform").validationEngine('validate');
}

function checknib(field, rules, i, options) {
      if (field.val().match(/^\d+$/))
      {
            var pin_nib = field.val();
            var w_dig_controlo = pin_nib.substr(19, 2) * 1;
            var w_total = 0;
            for (w_index = 0; w_index <= 18; w_index++) {
                  var w_digito = pin_nib.substr(w_index, 1) * 1;
                  w_total = ((w_total + w_digito) * 10) % 97;
            }
            w_total = 98 - ((w_total * 10) % 97);
            if (w_total !== w_dig_controlo) {
                  return "Introduza um nib correto";
            }
      }
      else
            return "Introduza um nib correto";

}

function checknif(field, rules, i, options) {

      var nif = field.val();
      var c;
      var checkDigit = 0;
      if (nif != null && nif.length == 9) {
            c = nif.charAt(0);
            if (c == '1' || c == '2' || c == '5' || c == '6' || c == '8' || c == '9') {
                  checkDigit = c * 9;
                  for (i = 2; i <= 8; i++) {
                        checkDigit += nif.charAt(i - 1) * (10 - i);
                  }
                  checkDigit = 11 - (checkDigit % 11);
                  if (checkDigit >= 10) {
                        checkDigit = 0;
                  }

                  if (checkDigit === nif.charAt(8)) {

                        return "Introduza um NIF correto";
                  }
            }
            else
                  return "Introduza um NIF correto";
      }
      else
            return "Introduza um NIF correto";
}

function getUrlVars() {
      var vars = {};
      var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
            vars[key] = value;
      });
      return vars;
}