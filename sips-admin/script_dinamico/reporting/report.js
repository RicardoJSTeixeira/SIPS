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


      $("#column_order").sortable({});



});


$(".radio_opcao").on("change", function()
{
      $("#column_order_div").hide();
      $(".select_opcao").hide();
      if ($(this).val() == "1")
      {
            $("#report_campanha").show();
            $("#column_order_div").show();
      }
      else if ($(this).val() == "2")
            $("#report_linha_inbound").show();
      else
            $("#report_bd").show();
});





$("#download_report").on("click", function(e)
{

      e.preventDefault();

      var ordered_tags = new Array();
      $("#column_order li").each(function()
      {

            ordered_tags.push( $(this).data("id"));
      });



      if ($("#form_filter").validationEngine('validate'))
      {


            if ($("#radio1").is(":checked"))
            {

                  document.location.href = "requests.php?action=report&tipo=1&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&campaign_id=" + $("#select_campanha option:selected").val() + "&allctc=" + $("#allcontacts").is(":checked")+"&column_order="+ordered_tags;
            }
            else if ($("#radio2").is(":checked"))
            {
                  document.location.href = "requests.php?action=report&tipo=2&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&campaign_id=" + $("#select_linha_inbound option:selected").val() + "&allctc=" + $("#allcontacts").is(":checked");
            }
            else
            {
                  document.location.href = "requests.php?action=report&tipo=3&data_inicio=" + $("#datetime_from").val() + "&data_fim=" + $("#datetime_to").val() + "&list_id=" + $("#select_base_dados option:selected").val() + "&campaign_id=" + $("#select_base_dados option:selected").data("campaign_id") + "&allctc=" + $("#allcontacts").is(":checked");
            }
      }
});

$("#allcontacts").on("click", function()
{
      $(".time_div").toggle(500);
});

$("#select_campanha").on("change", function()
{
      $("#column_order").empty();
      $.post("requests.php", {action: "get_fields_to_order", campaign_id: $("#select_campanha option:selected").val()},
      function(data)
      {
            if (data.length)
            {
                  $.each(data, function()
                  {
                        if ($.isNumeric(this.id))
                              $("#column_order").append("<li data-id=" + this.id + ">" + this.id + ":" + get_name_by_type(this.type) + "->" + this.texto + "</li>");
                        else
                              $("#column_order").append("<li data-id=" + this.id + ">"+ (this.display_name)+"</li>");
                  });
            }
            else
                  $("#column_order").append("<li>Sem Script</li>");

      }, "json");
      
       
});


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