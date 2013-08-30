


//licença: 1339
//operador:blitz
//pass: 1234
//sflphone

var array_id = [];
var tag_regex = /\@([^@]+)\@/g;
var user_id = getUrlVars()["user_id"];
var unique_id = getUrlVars()["unique_id"];
var campaign_id = getUrlVars()["campaign_id"];
var lead_id = getUrlVars()["lead_id"];
var script_id = getUrlVars()["id_script"];
$(function() {
      array_id["radio"] = 0;
      array_id["checkbox"] = 0;
      $.get("items.html", function(data) {
            $("#dummie").html(data);
            update_script();
      });
});







function getUrlVars() {
      var vars = {};
      var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
            vars[key] = value;
      });
      return vars;
}



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
                  if (data.required)
                        element.find(".input_texto").addClass("validate[required]");
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
      $.post("requests.php", {action: "get_results_to_populate", lead_id: lead_id},
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

      if (campaign_id !== undefined)
      {

            $.post("requests.php", {action: "get_scripts_by_campaign", id_campaign: campaign_id},
            function(data)
            {
                  if (data !== null)
                  {
                        script_id = data.id;
                        update_info();
                  }

            }, "json");
      }
      else

      {

            $.post("requests.php", {action: "get_scripts_by_id_script", id_script: script_id},
            function(data)
            {
                  if (data == null)

                  {
                        alert("Não existe script");
                  }
                  else
                  {
                        script_id = data.id;
                        update_info();
                  }
            }, "json");
      }
}

function update_info()
{
      $(".datetimepicker").remove();

      $.post("requests.php", {action: "get_data_render", id_script: script_id},
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




                  if (!$("#" + this.id_page + "pag").length) {
                        $("#script_div").append($("<div>").addClass("pag_div").attr("id", this.id_page + "pag"));
                  }
                   $("#" + this.id_page + "pag").append(item);



            });
            tags();
            rules();

            $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true}).keypress(function(e) {
                  e.preventDefault();
            }).bind("cut copy paste", function(e) {
                  e.preventDefault();
            });
            $("#myform").validationEngine();



            $(".pag_div").hide().first().show();

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
      $.post("requests.php", {action: "get_rules", id_script: script_id},
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
      $.post("requests.php", {action: "save_form_result", id_script: script_id, results: $("#myform").serializeArray(), user_id: user_id, unique_id: unique_id, campaign_id: campaign_id, lead_id: lead_id}, function() {
            return true;
      }, "json").fail(function() {
            return false;
      });

}

function validate_manual()
{
      return $("#myform").validationEngine('validate');

}