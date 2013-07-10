$(document).ready(function() {
      $('#from').datepicker({
            maxDate: "+0",
            defaultDate: "-1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: "yy-mm-dd",
            onClose: function(selectedDate) {
                  $("#to").datepicker("option", "minDate", selectedDate);
            },
            onSelect: function() {

                  document.getElementById('button1').disabled = false;

            }



      });
      $('#to').datepicker({
            maxDate: "+0",
            defaultDate: "+0w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: "yy-mm-dd",
            onClose: function(selectedDate) {
                  $("#from").datepicker("option", "maxDate", selectedDate);
            },
            onSelect: function() {

                  document.getElementById('button1').disabled = false;

            }
      });
      $("#grupo_user_div").hide();
      select_campanha();
      select_user_group();
      document.getElementById('button1').disabled = true;
});



$("#radio_filtro1").change(function()
{
      if ($(this).prop('checked'))
      {
            $("#campanha_div").show();
            $("#grupo_user_div").hide();
      }
});
$("#radio_filtro2").change(function()
{
      if ($(this).prop('checked'))
      {
            $("#campanha_div").hide();
            $("#grupo_user_div").show();
      }
});
$("#radio_filtro3").change(function()
{
      if ($(this).prop('checked'))
      {
            $("#campanha_div").hide();
            $("#grupo_user_div").hide();
      }
});





function select_campanha()
{
      $.post("Requests.php", {action: "campaign"},
      function(data)
      {

            var object = $([]).add($("#campanha_select"));
            $.each(data, function(index, value) {
                  if (value !== "") {
                        object.append(new Option(this.campaign_name, this.campaign_id));
                  }
            });
            object.chosen({no_results_text: "Não foi encontrado."});
      }, "json");
}

function select_user_group()
{
      $.post("Requests.php", {action: "user_group"},
      function(data1)
      {
            var object1 = $([]).add($("#grupo_user_select"));
            $.each(data1, function(index, value) {
                  if (value !== "") {
                        object1.append(new Option(this.group_name, this.user_group));
                  }
            });
            object1.chosen({no_results_text: "Não foi encontrado."});
      }, "json");
}



$("#button1").click(function()
{
      if ($('#from').datepicker({dateFormat: 'yy-mm-dd'}).val() == "" || $('#to').datepicker({dateFormat: 'yy-mm-dd'}).val() == "")
            alert("Falta preencher as datas");
      else
      {


            $("#info_table").show();
            var filtro_data = 0;
            var Opcao = 1;
            if ($("#radio_filtro1").prop('checked'))
            {
                  Opcao = 1;
                  filtro_data = $("#campanha_select").val();
            }
            else if ($("#radio_filtro2").prop('checked'))
            {
                  Opcao = 2;
                  filtro_data = $("#grupo_user_select").val();
            }
            else if ($("#radio_filtro3").prop('checked'))
            {
                  Opcao = 3;
                  filtro_data = "";
            }



            $.post("Requests.php", {action: "search", opcao: Opcao, filtro_val: filtro_data, data_inicio: $('#from').datepicker({dateFormat: 'yy-mm-dd'}).val(), data_fim: $('#to').datepicker({dateFormat: 'yy-mm-dd'}).val()},
            function(data)
            {
                  $("#table_body").empty();
                  $("#table_body").append($("<tr>")
                          .append($("<td>").text(secondstotime(data.TMN)))
                          .append($("<td>").text(secondstotime(data.VODAFONE)))
                          .append($("<td>").text(secondstotime(data.OPTIMUS)))
                          .append($("<td>").text(secondstotime(data.FIXO)))
                          .append($("<td>").text(secondstotime(data.outros)))
                          .append($("<td>").text(secondstotime(data.outros + data.OPTIMUS + data.VODAFONE + data.TMN + data.FIXO))));//total por rede
            }, "json");
      }

});

function secondstotime(seconds)
{
      var numhours = Math.floor((seconds) / 3600);
      var numminutes = Math.floor(((seconds) % 3600) / 60);
      var numseconds = ((seconds) % 3600) % 60;

      if (numhours < 10)
            numhours = "0" + numhours;
      if (numminutes < 10)
            numminutes = "0" + numminutes;
      if (numseconds < 10)
            numseconds = "0" + numseconds;

      return numhours + ":" + numminutes + ":" + numseconds;
}




