
/*
 sip.conf
 iax.conf
 
 
 
 vicidial_carrier_log com vicidial log
 
 
 filtrar por campanha ou grupo de user
 
 
 /oni/red1/red2/
 fixo
 vodafone
 tmn
 
 */





$("#radio_filtro1").change(function()
{
      if ($("#radio_filtro1").prop('checked'))
      {
            $("#campanha_div").show();
            $("#grupo_user_div").hide();
      }
});

$("#radio_filtro2").change(function()
{
      if ($("#radio_filtro2").prop('checked'))
      {
            $("#campanha_div").hide();
            $("#grupo_user_div").show();
      }
});



$(document).ready(function() {


      $("#grupo_user_div").hide();

      select_campanha();
      select_user_group();

      document.getElementById('button1').disabled = true;

      var ready = false;//para distinguir entre update de informação e renovação.


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

$(document).ajaxStart(function() {
      $("#Ajax_Loader").show();
});
$(document).ajaxStop(function() {
      $(".Search").fadeIn(600);
      $("#Ajax_Loader").hide();
});







//De (data) a (data)·············································································
$('#from').datepicker({
      defaultDate: "-1w",
      changeMonth: true,
      numberOfMonths: 1,
      dateFormat: "yy-mm-dd",
      onClose: function(selectedDate) {
            $("#to").datepicker("option", "minDate", selectedDate);
      },
      onSelect: function() {
            ready = false;
      }
});
$('#to').datepicker({
      defaultDate: "+0w",
      changeMonth: true,
      numberOfMonths: 1,
      dateFormat: "yy-mm-dd",
      onClose: function(selectedDate) {
            $("#from").datepicker("option", "maxDate", selectedDate);
      },
      onSelect: function() {
            ready = false;
            document.getElementById('button1').disabled = false;
      }
});


// ···················································································································




//Check all or diferent choise in dropboxes
function checkCampos()
{
      if ($("#Mv2").val() === '1')
      {
            channel = "%SIP%";
      }
      else
      {
            channel = "%" + $("#Mv2").val() + "%";
      }
      if ($("#Mv3").val() === '1')
      {
            campanha = "%%";
      }
      else
      {
            campanha = "%" + $("#Mv3").val() + "%";
      }
      if ($("#Mv5").val() === '1')
      {
            userGroup = "%%";
      }
      else
      {
            userGroup = "%" + $("#Mv5").val() + "%";
      }



      /*<option value =1>All</option> 
       <option value =0>Outros</option> 
       <option value =91>Vodafone</option> 
       <option value =92>Tmn</option> 
       <option value =93>Optimus</option> 
       <option value =21>Fixo</option> */
      switch ($("#Mv4").val())
      {
            case '0':
                  dialledNumber = "NOT REGEXP '(91|92|93|96|21)'";
                  break;
            case '1':
                  dialledNumber = "REGEXP '[0-9][0-9]'";
                  break;
            case '91':
                  dialledNumber = "REGEXP '(91)'";
                  break;
            case '92':
                  dialledNumber = "REGEXP '(92|96)'";
                  break;
            case '93':
                  dialledNumber = "REGEXP '(93)'";
                  break;
            case '21':
                  dialledNumber = "REGEXP '(21)'";
                  break;
      }
}
//····································································

$("#button1").click(function()
{

      $("#info_table").show();

      var Opcao = 1;
      if ($("#radio_filtro1").prop('checked'))
      {
            Opcao = 1;
      }
      else
            Opcao = 2;


      $.post("Requests.php", {action: "search", opcao: Opcao, campaign: $("#campanha_select").val(), user_group: $("#grupo_user_select").val(), data_inicio: $('#from').datepicker({dateFormat: 'yy-mm-dd'}).val(), data_fim: $('#to').datepicker({dateFormat: 'yy-mm-dd'}).val()},
      function(data)
      {

            $("#table_body").empty();



            var count = 0;

            var redes = {fixo: 0, tmn: 0, vodafone: 0, optimus: 0, n929: 0, outros: 0};


            $.each(data, function(index, value)

            {

                  var trunc = data[index];


                  $("#table_body").append($("<tr>")
                          .append($("<td>").text(index))
                          .append($("<td>").text(secondstotime(trunc.FIXO)))
                          .append($("<td>").text(secondstotime(trunc.TMN)))
                          .append($("<td>").text(secondstotime(trunc.VODAFONE)))
                          .append($("<td>").text(secondstotime(trunc.OPTIMUS)))
                          .append($("<td>").text(secondstotime(trunc.n929)))
                          .append($("<td>").text(secondstotime(trunc.outros)))
                          .append($("<td>").text(secondstotime(trunc.outros + trunc.n929 + trunc.OPTIMUS + trunc.VODAFONE + trunc.TMN + trunc.FIXO))));//total por rede

                  redes.fixo += trunc.FIXO;
                  redes.tmn += trunc.TMN;
                  redes.vodafone += trunc.VODAFONE;
                  redes.optimus += trunc.OPTIMUS;
                  redes.n929 += trunc.n929;
                  redes.outros += trunc.outros;



                  count++;
            });
            console.log(redes);

//total por trunc
            $("#table_body").append($("<tr>")
                    .append($("<td>").text(("")))
                    .append($("<td>").text(secondstotime(redes.fixo)))
                    .append($("<td>").text(secondstotime(redes.tmn)))
                    .append($("<td>").text(secondstotime(redes.vodafone)))
                    .append($("<td>").text(secondstotime(redes.optimus)))
                    .append($("<td>").text(secondstotime(redes.n929)))
                    .append($("<td>").text(secondstotime(redes.outros)))
                    .append($("<td>").text((""))));


            count = 0;


      }, "json");


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




