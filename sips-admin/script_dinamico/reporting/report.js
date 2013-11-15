$(function() {
      $("#report_bd").hide();
      $("#report_linha_inbound").hide();
      $(".chzn-select").chosen({no_results_text: "Sem resultados"});

      $("#form_filter").validationEngine();
      $(".datetime_range").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).keypress(function(e) {
            e.preventDefault();
      }).bind("cut copy paste", function(e) {
            e.preventDefault();
      });


      $.post("requests.php", {action: "get_select_options"},
      function(data)
      {
            var temp = "";
            $("#select_campanha").empty();
            $.each(data.campanha, function()
            {
                  temp = temp + "<option value=" + this.id + " > " + this.name + "</option>";
            });
            $("#select_campanha").append(temp);
            temp = "";
            $("#select_campanha").val("").trigger("liszt:updated");
            $("#select_campanha").trigger("change");

            $("#select_linha_inbound").empty();
            $.each(data.linha_inbound, function()
            {
                  temp = temp + "<option value=" + this.id + " > " + this.name + "</option>";
            });
            $("#select_linha_inbound").append(temp);
            temp = "";
            $("#select_linha_inbound").val("").trigger("liszt:updated");

            $("#select_base_dados").empty();
            $.each(data.bd, function()
            {
                  temp = temp + "<option data-campaign_id=" + this.campaign_id + " value=" + this.id + " > " + this.name + "</option>";
            });
            $("#select_base_dados").append(temp);
            $("#select_base_dados").val("").trigger("liszt:updated");

      }, "json");


      $("#column_order").sortable({
            stop: function(event, ui) {
                  var elements = new Array();
                  var items = $("#column_order  li");
                  for (var count = 0; count < items.length; count++)
                  {
                        elements.push(items[count].id);
                  }
                  $.post("requests.php", {action: "update_elements_order", elements: elements, campaign: $("#select_campanha option:selected").val()},
                  function(data)
                  {


                  }, "json");






            }});



});






$("#download_report").on("click", function(e)
{

      e.preventDefault();

      var
              ordered_tags = new Array(),
              that;
      $("#column_order li").each(function()
      {
            that = $(this).data();

            ordered_tags.push({id: that.id, type: that.type, text: that.text});
      });


      if ($("#form_filter").validationEngine('validate'))
      {
            if ($("#radio1").is(":checked"))
            {
                  document.location.href = "requests.php?action=report&tipo=1&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&campaign_id=" + $("#select_campanha option:selected").val() + "&allctc=" + $("#allcontacts").is(":checked") + "&field_data=" + JSON.stringify(ordered_tags);
            }
            else if ($("#radio2").is(":checked"))
            {
                  document.location.href = "requests.php?action=report&tipo=2&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&campaign_id=" + $("#select_linha_inbound option:selected").val() + "&allctc=" + $("#allcontacts").is(":checked");
            }
            else
            {
                  document.location.href = "requests.php?action=report&tipo=3&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&list_id=" + $("#select_base_dados option:selected").val() + "&campaign_id=" + $("#select_base_dados option:selected").data("campaign_id") + "&allctc=" + $("#allcontacts").is(":checked") + "&field_data=" + JSON.stringify(ordered_tags);
            }
      }
});

$("#allcontacts").on("click", function()
{
      $(".time_div").toggle(500);
});

$(".radio_opcao").on("click", function()
{
      $(".select_opcao").hide();
      if ($(this).val() == "1")
      {
            $("#report_campanha").show();
      }
      else if ($(this).val() == "2")
            $("#report_linha_inbound").show();
      else
      {
            $("#report_bd").show();
      }


});


function get_campaign_fields(campaign)
{

      $.post("requests.php", {action: "get_fields_to_order", campaign_id: campaign},
      function(data)
      {
            $("#column_order").empty();
            if (data.length)
            {
                  $.each(data, function()
                  {
                        if ($.isNumeric(this.id))
                              $("#column_order").append("<li class='ui-state-default' id=" + this.id + " data-id='m" + this.id + "' data-type='" + this.type + "' data-text='0'>" + this.id + ":" + get_name_by_type(this.type) + "->" + this.texto + "</li>");
                        else
                              $("#column_order").append("<li class='ui-state-default' id=" + this.id + " data-id='" + this.id + "' data-type='campo_dinamico' data-text='" + this.display_name + "'>" + (this.display_name) + "</li>");
                  });
            }
            else
                  $("#column_order").append("<li>Sem Script</li>");
      }, "json");
}


function get_name_by_type(type)
{
      switch (type)
      {
            case "texto":
                  return "caixa de texto";
                  break;
            case "pagination":
                  return "Paginação";
                  break;
            case "radio":
                  return "Botão radio";
                  break;
            case "checkbox":
                  return "Botão resposta multipla";
                  break;
            case "multichoice":
                  return "Lista de Opções";
                  break;
            case "textfield":
                  return "Campo de Texto";
                  break;
            case "legend":
                  return "Titulo";
                  break;
            case "tableradio":
                  return "Tabela botões radio";
                  break;
            case "datepicker":
                  return "Seletor tempo e hora";
                  break;
            case "scheduler":
                  return "Calendário";
                  break;
            case "textarea":
                  return "Input de texto";
                  break;
            case "ipl":
                  return  "Imagem/PDF/Link";
                  break;
      }

}


$("#order_columns_modal").on("click", function()
{
      var campaign = "";
      if ($("#radio1").is(":checked"))
            campaign = $("#select_campanha option:selected").val();
      if ($("#radio3").is(":checked"))
            campaign = $("#select_base_dados option:selected").data("campaign_id");

      $("#co_reset_button").data("campaign", campaign);
      get_campaign_fields(campaign);
      $("#co_modal").modal("show");

});

$("#co_reset_button").on("click", function() {
      
      console.log($("#co_modal #co_reset_button").data("campaign"));
      $.post("requests.php", {action: "reset_elements_order", campaign_id: $("#co_modal #co_reset_button").data("campaign")},function(data)
      {
            get_campaign_fields($("#co_modal #co_reset_button").data("campaign"));
      });
});