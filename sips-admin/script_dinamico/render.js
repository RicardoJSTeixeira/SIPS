

var array_id = [];
var tag_regex = /\@(\d{1,5})\@/g;
var tag_regex2 = /\§(.*?)\§/g;
var page_info = [];
var items = [];
var unique_id = 0;
var id_script = 0;
var admin_review = 0;

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




//UPDATES DE INFO
function update_script(callback)
{
      if (page_info.script_id !== undefined)
      {
            $.post("requests.php", {action: "get_scripts_by_id_script", id_script: page_info.script_id},
            function(data)
            {
                  if (data !== null)
                  {
                        page_info.script_id = data.id;

                        if (typeof callback === "function")
                        {
                              callback();
                        }
                  }
            }, "json");
      }
      else
      {

            var camp_linha = 0;
            if (page_info.in_group_id !== "")
            {
                  camp_linha = page_info.in_group_id;
            }
            if (page_info.campaign_id !== "")
            {
                  camp_linha = page_info.campaign_id;
            }
            $.post("requests.php", {action: "get_scripts_by_campaign", id_campaign: camp_linha},
            function(data)
            {
                  if (data !== null)
                  {
                        page_info.script_id = data.id;

                        if (typeof callback === "function")
                        {
                              callback();
                        }
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
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "texto");
                              items.push([item, this.id_page]);
                              break;
                        case "pagination":
                              item = $('#dummie .pagination_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "pagination");
                              items.push([item, this.id_page]);
                              break;
                        case "radio":
                              item = $('#dummie .radio_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "radio")
                                      .data("dispo", this.dispo);
                              items.push([item, this.id_page]);
                              break;
                        case "checkbox":
                              item = $('#dummie .checkbox_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("dispo", this.dispo)
                                      .data("type", "checkbox");
                              items.push([item, this.id_page]);
                              break;
                        case "multichoice":
                              item = $('#dummie .multichoice_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "multichoice");
                              items.push([item, this.id_page]);
                              break;
                        case "textfield":
                              item = $('#dummie .textfield_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "textfield");
                              items.push([item, this.id_page]);
                              break;
                        case "legend":
                              item = $('#dummie .legend_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "legend");
                              items.push([item, this.id_page]);
                              break;
                        case "tableradio":
                              item = $('#dummie .tableradio_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "tableradio");
                              items.push([item, this.id_page]);
                              break;
                        case "tableinput":
                              item = $('#dummie .tableinput_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "tableinput");
                              items.push([item, this.id_page]);
                              break;
                        case "datepicker":
                              item = $('#dummie .datepicker_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "datepicker")
                                      .data("data_format", this.placeholder);

                              items.push([item, this.id_page]);
                              break;
                        case "scheduler":
                              item = $('#dummie .scheduler_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "scheduler");
                              items.push([item, this.id_page]);
                              break;
                        case "textarea":
                              item = $('#dummie .textarea_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("required", this.required)
                                      .data("type", "textarea");
                              items.push([item, this.id_page]);
                              break;
                        case "ipl":
                              item = $('#dummie .ipl_class').clone();
                              item.attr("id", this.tag)
                                      .data("id", this.id)
                                      .data("option", this.param1)
                                      .data("type", "ipl");
                              items.push([item, this.id_page]);
                              break;
                  }
                  var last_insert = insert_element(this.type, item, this);

            });




//FAZER O APPEND DOS ITEMS A LISTA
            $.each(items, function()
            {
                  if (!$("#" + this[1] + "pag").length) {
                        $("#script_div").append($("<div>").addClass("pag_div").attr("id", this[1] + "pag"));
                  }
                  $("#" + this[1] + "pag").append(this[0]);
            });

            if (page_info.isadmin !== "1")
            {
                  $(".pag_div").hide().first().show();
                  $("#admin_submit").hide();
                  rules(tags(populate_script(function() {

                        setTimeout(function() {
                              $("#myform").validationEngine();
                        }, 100);
                  })));
            }
            else
            {
                  $(".pagination_class").remove();
                  $("#admin_submit").show();
                  populate_script(function() {
                        setTimeout(function() {
                              $("#myform").validationEngine();
                        }, 100);
                  });
                  $(".item").show();

            }



      


      }, "json");

}



function insert_element(opcao, element, data)
{
      element.removeAttr("title");
      element.find(".label_titulo").remove();
 

      switch (opcao)
      {
            case "texto":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  var input = element.find(".input_texto")[0];
                  input.placeholder = data.placeholder;
                  input.maxLength = data.max_length;
                  input.name = data.tag;


                  if (data.default_value.toString().length > 2)
                  {
               
                        $.post("requests.php", {action: "get_client_info_by_lead_id", lead_id: page_info.lead_id, user_logged: page_info.user_id},
                        function(data1)
                        {
        
                              if (Object.size(data1))
                                    input.value = data1[data.default_value.toString().toLowerCase()];
 

                        }, "json");



                  }
                  var pattern = [];
                  if (data.required)
                        pattern.push("required");

                  switch (data.param1)
                  {
                        case "normal":
                              pattern.push("[custom[onlyLetterNumberSymbol]]");
                              break;
                        case "letter":
                              pattern.push("[custom[onlyLetters]]");
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
                        case "credit_card_m":
                              pattern.push("funcCall[isValidMastercard]");
                              break;
                        case "credit_card_v":
                              pattern.push("funcCall[isValidVISA]");
                              break;
                        case "credit_card_d":
                              pattern.push("funcCall[isValidDebit]");
                        case "ajax":
                              pattern.push("ajax[rule" + data.tag + "]]");
                              $.validationEngineLanguage.allRules["rule" + data.tag] = {
                                    "url": "../script_dinamico/files/" + data.values_text.file,
                                    "alertText": data.values_text.not_validado,
                                    "alertTextOk": data.values_text.validado,
                                    "alertTextLoad": "* A validar, por favor aguarde"
                              };
                              break;

                  }
                  if (data.param1 != "none")
                        element.find(".input_texto").addClass("validate[" + pattern.join(",") + "]");

                  $(document).on("blur", "#" + data.tag + " :input", function()
                  {

                        $("#" + data.tag + " :input").validationEngine("validate");

                  });
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
                                      .attr("name", data.tag))
                                      );
                        else
                              element.append($("<label>")
                                      .addClass("radio_name radio inline")
                                      .attr("for", array_id["radio"] + "radio")
                                      .text(radios[count]).append($("<input>")
                                      .attr("type", "radio")
                                      .attr("value", radios[count])
                                      .attr("id", array_id["radio"] + "radio")
                                      .attr("name", data.tag))
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
                                      .attr("name", data.tag))
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
                                      .attr("name", data.tag))
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
                        element.append($("<select>").addClass("multichoice_select validate[required]").attr("name", data.tag));
                  else
                        element.append($("<select>").addClass("multichoice_select").attr("name", data.tag));
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
                  element.find(".label_geral")[0].name = data.tag;
                  break;
            case "legend":
                  element.find(".label_geral")[0].innerHTML = data.values_text;
                  element.find(".label_geral")[0].name = data.tag;
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
                                            .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").addClass("validate[required]").attr("value", titulos[count2]).attr("name", data.tag + "," + perguntas[count]))
                                            .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio")));
                              }
                              else
                              {
                                    trbody_last.append($("<td>")
                                            .append($("<input>").attr("type", "radio").attr("id", array_id["radio"] + "tableradio").attr("value", titulos[count2]).attr("name", data.tag + "," + perguntas[count]))
                                            .append($("<label>").addClass("radio_name").attr("for", array_id["radio"] + "tableradio")));
                              }
                              array_id["radio"] = array_id["radio"] + 1;
                        }
                  }
                  break;

            case "tableinput":
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
                                            .append($("<input>").attr("type", "text").attr("titulo", titulos[count2]).attr("id", array_id["input"] + "tableinput").addClass("validate[required] input-mini").attr("name", data.tag + "," + perguntas[count] + "," + titulos[count2])));
                              }
                              else
                              {
                                    trbody_last.append($("<td>")
                                            .append($("<input>").attr("type", "text").attr("titulo", titulos[count2]).attr("id", array_id["input"] + "tableinput").addClass("input-mini").attr("name", data.tag + "," + perguntas[count] + "," + titulos[count2])));
                              }
                              array_id["input"] = array_id["input"] + 1;
                        }
                  }
                  break;

            case "datepicker":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  if (data.required)
                        element.find(".form_datetime").addClass("validate[required]");
                  element.find(".form_datetime")[0].name = data.tag;
                  var data_format = "yyyy-mm-dd hh:ii";
                  var min_view = 0;

                  switch (element.data("data_format"))
                  {
                        case "0":
                              data_format = 'yyyy-mm-dd hh:ii';
                              min_view = 0;
                              break;
                        case "1":
                              data_format = 'yyyy-mm-dd hh';
                              min_view = 1;
                              break;
                        case "2":
                              data_format = 'yyyy-mm-dd';
                              min_view = 2;
                              break;
                  }

                  element.find(".form_datetime").datetimepicker({format: data_format, autoclose: true, language: "pt", minView: min_view}).keypress(function(e) {
                        e.preventDefault();
                  }).bind("cut copy paste", function(e) {
                        e.preventDefault();
                  });
                  break;
            case "scheduler":
                  element.find(".scheduler_button_go").attr("id", element.data("id") + "go_button");
                  var select = element.find(".scheduler_select");
                  $.post("requests.php", {action: "get_schedule_by_id", ids: data.values_text.join(",")},
                  function(data3)
                  {
                        $.each(data3, function(index, value) {
                              select.append("<option value=" + this.id + ">" + this.text + "</option>");
                        });
                        select.val("").trigger("liszt:updated");
                  }, "json");
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  if (data.required)
                        element.find(".scheduler_select").addClass("validate[required]");
                  break;
            case "textarea":
                  element.find(".label_geral")[0].innerHTML = data.texto;
                  element.find(".input_textarea")[0].name = data.tag;
                  if (data.required)
                        element.find(".input_textarea").addClass("validate[required]");
                  break;
            case "ipl":

                  element.find(".label_geral")[0].innerHTML = data.texto;
                  element.find("span").remove();
                  if (element.data("option") == "1")
                  {
                        if (data.values_text.length > 3)
                        {
                              element.append($("<img>").attr("src", 'files\\' + data.values_text));
                        }
                  }
                  else if (element.data("option") == "2")
                  {
                        if (data.values_text.length > 0)
                        {
                              element.append($("<button>").addClass("pdf_button").attr("file", data.values_text).text("Ver PDF"));
                        }
                  }
                  else
                  {
                        element.append($("<a>").attr("href", "http://" + data.values_text).text(data.values_text));
                  }
                  break;
      }
      if (data.hidden)
            element.css("display", "none");
      return true;
}


function populate_script(callback)
{
      $.post("requests.php", {action: "get_results_to_populate", lead_id: page_info.lead_id, id_script: page_info.script_id},
      function(data)
      {


            if (Object.size(data))
            {


                  $.each(data, function() {

                        switch (this.type)
                        {
                              case "texto":
                                    $("#" + this.tag_elemento + " :input").val(this.valor);
                                    break;
                              case "radio":
                              case "checkbox":
                                    $("#" + this.tag_elemento + " :input[value='" + this.valor + "']").attr('checked', true);
                                    break;
                              case "multichoice":
                                    $("#" + this.tag_elemento + " :input").val(this.valor);
                                    break;
                              case "tableradio":
                                    $("#" + this.tag_elemento + " tbody tr:contains(" + this.param1 + ") :input[value='" + this.valor + "']").attr('checked', true);
                                    break;
                              case "tableinput":
                                    var temp = this.param1.split(";");
                                    $("#" + this.tag_elemento + " tbody tr:contains(" + temp[1] + ") :input[titulo='" + temp[0] + "']").val(this.valor);
                                    break;
                              case "datepicker":
                                    $("#" + this.tag_elemento + " :input").val(this.valor);
                                    break;
                        }
                  });
            }

            if (typeof callback === "function")
            {
                  callback();
            }
      }, "json");

}

//RULES  
function rules_work(data)
{

      switch (data.tipo)
      {
            case "hide":
                  var target = data.tag_target;
                  for (var count2 = 0; count2 < target.length; count2++)
                  {
                        $("#" + target[count2]).fadeOut(400);
                  }
                  break;
            case "show":
                  var target = data.tag_target;
                  for (var count2 = 0; count2 < target.length; count2++)
                  {
                        $("#" + target[count2]).fadeIn(400);
                  }
                  break;
            case "goto":
                  if (page_info.isadmin != "1")
                  {
                        if ($("#myform").validationEngine('validate'))
                        {

                              $(".pag_div").hide();
                              $("#" + data.tag_target + "pag").show();
                        }
                  }
                  break;
      }
}
function rules(callback)
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

                                          $(document).on("keyup", "#" + this.tag_trigger, function()//atribuir os ons a cada value
                                          {
                                                var pattern = new RegExp('\\b' + data[index].tag_trigger2, 'i');
                                                if ($("#" + data[index].tag_trigger + " input").val().match(pattern))
                                                {
                                                      rules_work(data[index]);
                                                }
                                          }
                                          );
                                          break;
                                    case "answer":
                                          $(document).on("focusout", "#" + this.tag_trigger, function()//atribuir os ons a cada value
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

                                          var values = this.tag_trigger2;
                                          for (var count = 0; count < values.length; count++)
                                          {
                                                $(document).on("click", "#" + this.tag_trigger + " input[value='" + values[count] + "']", function()//atribuir os ons a cada value
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
                                          var values = this.tag_trigger2;
                                          for (var count = 0; count < values.length; count++)
                                          {
                                                $(document).on("click", "#" + this.tag_trigger + " input[value='" + values[count] + "']", function()//atribuir os ons a cada value
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
                                          var values = this.tag_trigger2;
                                          for (var count = 0; count < values.length; count++)
                                          {
                                                $(document).on("change", "#" + this.tag_trigger, function()//atribuir os ons a cada value
                                                {
                                                      if ($("#" + data[index].tag_trigger + " option:selected").val() === data[index].tag_trigger2)
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
                                          var linhas = this.tag_trigger2;
                                          for (var count = 0; count < linhas.length; count++)
                                          {
                                                var values = linhas[count].split(";");
                                                $(document).on("click", "#" + this.tag_trigger + " tr:contains('" + values[0] + "') input[value='" + values[1] + "']", function()
                                                {
                                                      rules_work(data[index]);
                                                }
                                                );
                                          }
                                          break;
                                    case "answer":
                                          $(document).on("click", "#" + this.tag_trigger + " input", function()
                                          {

                                                if ($("#" + data[index].tag_trigger).find("input:checked").length === ($("#" + data[index].tag_trigger + " .tr_body").find("tr").length))
                                                      rules_work(data[index]);
                                          });
                                          break;
                              }
                              break;
                        case "tableinput":
                              if (this.param1 == "answer")
                              {
                                    $(document).on("focusout", "#" + this.tag_trigger + " input", function()
                                    {
                                          console.log($("#" + data[index].tag_trigger).find("input[value!='']").length + "--->" + $("#" + data[index].tag_trigger + " .tr_body").find("td").find("input").length);


                                          if ($("#" + data[index].tag_trigger).find("input[value!='']").length === ($("#" + data[index].tag_trigger + " .tr_body").find("td").find("input").length))
                                                rules_work(data[index]);
                                    });
                              }

                              break;
                        case "datepicker":
                              switch (this.param1)
                              {
                                    case "answer":
                                          $(document).on("change", "#" + this.tag_trigger, function()//atribuir os ons a cada value
                                          {
                                                rules_work(data[index]);
                                          }
                                          );
                                          break;


                                    case "date":
                                          $(document).on("change", "#" + this.tag_trigger + " .form_datetime", function()//atribuir os ons a cada value
                                          {
                                                var temp = data[index];

                                                if (temp.param2.fd == "fixed") {
                                                      switch (temp.param2.tipo)
                                                      {
                                                            case "menor":
                                                                  if ($(this).val() < temp.param2.data_inicio)
                                                                        rules_work(data[index]);
                                                                  break;
                                                            case "igual":
                                                                  if ($(this).val() == temp.param2.data_inicio)
                                                                        rules_work(data[index]);
                                                                  break;
                                                            case "maior":

                                                                  if ($(this).val() > temp.param2.data_inicio)
                                                                        rules_work(data[index]);
                                                                  break;
                                                            case "entre":
                                                                  if ($(this).val() > temp.param2.data_inicio && $(this).val() < temp.param2.data_fim)
                                                                        rules_work(data[index]);
                                                                  break;
                                                      }
                                                }
                                                else
                                                {
                                                      var tempo1 = temp.param2.data_inicio.split("/");

                                                      var time1 = moment();
                                                      time1.add('year', tempo1[0]).add('month', tempo1[1]).add('day', tempo1[2]).add('hour', tempo1[3]);
                                                      switch (temp.param2.tipo)
                                                      {
                                                            case "menor":
                                                                  if (time1.isAfter(moment($(this).val())))
                                                                        rules_work(data[index]);
                                                                  break;
                                                            case "igual":
                                                                  if (time1.isSame(moment($(this).val()), "day"))
                                                                        rules_work(data[index]);
                                                                  break;
                                                            case "maior":
                                                                  if (time1.isBefore(moment($(this).val())))
                                                                        rules_work(data[index]);
                                                                  break;
                                                            case "entre":
                                                                  var tempo2 = temp.param2.data_fim.split("/");
                                                                  var time2 = moment();
                                                                  time2.add('year', tempo2[0]).add('month', tempo2[1]).add('day', tempo2[2]).add('hour', tempo2[3]);
                                                                  if (time1.isBefore(moment($(this).val())) && time2.isAfter(moment($(this).val())))
                                                                        rules_work(data[index]);
                                                                  break;
                                                      }
                                                }

                                          }
                                          );
                                          break;
                              }
                              break;
                        case "textarea":

                              switch (this.param1)
                              {
                                    case "answer":
                                          $(document).on("focusout", "#" + this.tag_trigger, function()//atribuir os ons a cada value
                                          {
                                                rules_work(data[index]);
                                          }
                                          );
                                          break;
                              }
                              break;
                  }
            });

            if (typeof callback === "function")
            {
                  callback();
            }

      }, "json");
}
function tags(callback)
{
      var rz = $("#render_zone");
      //tags de valores de elementos da mesma pagina

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

//Tags de nome/morada/telefone etc

      if (rz.html().match(tag_regex2))
      {
            var temp2 = rz.html().match(tag_regex2);
            var temp = [];

            $.each(temp2, function() {

                  if ($.inArray(this.toString(), temp) === -1)
                        temp.push(this.toString());

            });

            $.post("requests.php", {action: "get_client_info_by_lead_id", lead_id: page_info.lead_id, user_logged: page_info.user_id},
            function(data)
            {

                  if (Object.size(data))
                        $.each(temp, function() {
                              var id = this;
                              id = id.replace(/\§/g, '');
                              var regExp = new RegExp(this, "g");
                              rz.html(rz.html().replace(regExp, data[id.toLowerCase()]));

                        });

            }, "json");
      }

      if (typeof callback === "function")
      {
            callback();
      }

}



//FORM MANIPULATION
$("#myform").on("submit", function(e)
{
      e.preventDefault();
});
$("#admin_submit").on("click", function()
{
      admin_review = 1;
      submit_manual();
});

function submit_manual(callback)
{
      $.post("requests.php", {action: "save_form_result", id_script: page_info.script_id, results: $("#myform").serializeArray(), user_id: page_info.user_id, unique_id: unique_id, campaign_id: page_info.campaign_id, lead_id: page_info.lead_id, admin_review: admin_review},
      function() {
            admin_review = 0;
            if (typeof callback === "function")
            {
                  callback();
            }
            return true;


      }, "json").fail(function() {
            console.log("FAIL saving data");
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
                  return "Introduza um NIB correto";
            }
      }
      else
            return "Introduza um NIB correto";
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
                  if (checkDigit !== parseInt(nif.charAt(8))) {

                        return "Introduza um NIF correto";
                  }
            }
            else
                  return "Introduza um NIF correto";
      }
      else
            return "Introduza um NIF correto";
}

function isValidCard(cardNumber) {


      var ccard = new Array(cardNumber.length);
      var i = 0;
      var sum = 0;

      // 6 digit is issuer identifier
      // 1 last digit is check digit
      // most card number > 11 digit
      if (cardNumber.length < 11) {
            return false;
      }
      // Init Array with Credit Card Number
      for (i = 0; i < cardNumber.length; i++) {
            ccard[i] = parseInt(cardNumber.charAt(i));
      }
      // Run step 1-5 above above
      for (i = 0; i < cardNumber.length; i = i + 2) {
            ccard[i] = ccard[i] * 2;
            if (ccard[i] > 9) {
                  ccard[i] = ccard[i] - 9;
            }
      }
      for (i = 0; i < cardNumber.length; i++) {
            sum = sum + ccard[i];
      }
      return ((sum % 10) == 0);
}

function isValidVISA(field, rules, i, options) {
      cardNumber = field.val();
      if (cardNumber.charAt(0) == '4' && (cardNumber.length == 13 || cardNumber.length == 16)) {
            if (!isValidCard(cardNumber))
                  return "Nº de Cartão invalido";
      }
      else
            return "Insira um número de cartão Visa Válido";
}

function isValidMastercard(field, rules, i, options) {
      cardNumber = field.val();
      if (cardNumber.charAt(0) == '5' && (cardNumber.charAt(1) == '1' || cardNumber.charAt(1) == '5') && cardNumber.length == 16) {
            if (!isValidCard(cardNumber))
                  return "Nº de Cartão invalido";
      }
      else
            return "Insira um número de cartão Mastercard Válido";
}

function isValidDebit(field, rules, i, options) {
      cardNumber = field.val();
      if (!isValidCard(cardNumber))
            return "Nº de Cartão de Débito invalido";
}


function getUrlVars() {
      var vars = {};
      var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
            vars[key] = value;
      });
      return vars;
}

$("#close_render_admin").on("click", function()
{
      window.close();
});



$(function() {


      array_id["radio"] = 0;
      array_id["checkbox"] = 0;
      array_id["input"] = 0;
      $.get("items.html", function(data) {
            page_info = getUrlVars();
            $("#dummie").html(data);
            update_script(update_info);
      });
      $(document).on("click", ".previous_pag", function(e) {
            e.preventDefault();
            var temp = $(".pag_div:visible").prev(".pag_div");
            if (temp.length)
            {
                  $(".pag_div").hide();
                  temp.show();
            }
      });
      $(document).on("click", ".next_pag", function(e) {
            e.preventDefault();

            var temp = $(".pag_div:visible").next(".pag_div");
            if (temp.length)
            {
                  $(".pag_div").hide();
                  temp.show();
            }

      });
      $(document).on("click", ".scheduler_button_go", function(e) {
            e.preventDefault();
            var url = '../reservas/views/calendar_container.php?sch=' + $(this).prev("select").val() + '&user=' + page_info.user_id + '&lead=' + page_info.lead_id;
            window.open(url, 'Calendario', 'fullscreen=yes, scrollbars=auto,status=1');
      });
      $(document).on("click", ".pdf_button", function(e)
      {
            var url = "../script_dinamico/files/" + $(this).attr("file");
            window.open(url, 'PDF', 'fullscreen=no, scrollbars=auto');
      });









});