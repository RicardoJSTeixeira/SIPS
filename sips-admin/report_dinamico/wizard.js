


$(function() {
      $('#form_wizard').stepy({
            finishButton: false
      });
      $(".chosen_select").chosen();
//load selects
      //Inicializatinon-------------------------------------------------------------------------------------
      db_calls("get_user", $("#select_fieldset1_param2"));
      $("#select_fieldset1_param3")
              .append("<option value='feedback'>feedbacks</option>")
              .append("<option value='tempo'>tempo</option>").trigger("liszt:updated");
      db_calls("get_feedbacks", $("#select_fieldset1_param4"));


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


//-----------------------------------------_----------------------__---__--------_----------
});


$("#select_fieldset1_param1").change(function()
{

      $("#select_fieldset1_param3").empty();


      switch (this.value)
      {
            case "user":
                  db_calls("get_user", $("#select_fieldset1_param2"));
                  $("#select_fieldset1_param3")
                          .append("<option value='feedback'>feedbacks</option>")
                          .append("<option value='tempo'>tempo</option>").trigger("liszt:updated");
                  break;
            case "user_group":
                  db_calls("get_user_group", $("#select_fieldset1_param2"));
                  $("#select_fieldset1_param3")
                          .append("<option value='feedback'>feedbacks</option>")
                          .append("<option value='tempo'>tempo</option>").trigger("liszt:updated");
                  break;
            case "campanha":
                  db_calls("get_campaign", $("#select_fieldset1_param2"));
                  $("#select_fieldset1_param3")
                          .append("<option value='feedback'>feedbacks</option>")
                          .append("<option value='tempo'>tempo</option>")
                          .append("<option value='script'>scripts</option>").trigger("liszt:updated");
                  break;
            case "linha_inbound":
                  db_calls("get_linha_inbound", $("#select_fieldset1_param2"));
                  $("#select_fieldset1_param3")
                          .append("<option value='feedback'>feedbacks</option>")
                          .append("<option value='tempo'>tempo</option>")
                          .append("<option value='script'>scripts</option>").trigger("liszt:updated");
                  break;
            case "totalcc":
                  db_calls("get_totalcc", $("#select_fieldset1_param2"));
                  $("#select_fieldset1_param3")
                          .append("<option value='feedback'>feedbacks</option>")
                          .append("<option value='tempo'>tempo</option>")
                          .append("<option value='script'>scripts</option>").trigger("liszt:updated");
                  break;
            case "script":
                  db_calls("get_scripts", $("#select_fieldset1_param2"));
                  $("#select_fieldset1_param3")
                          .append("<option value='media_resposta'>Média e Soma de respostas</option>").trigger("liszt:updated");
                  break;
      }


});


$("#select_fieldset1_param3").change(function()
{

      if (this.value === "tempo")
      {
            $("#select_fieldset1_param4,#select_fieldset1_param4_chzn").addClass("hidden");
            $("#div_datepicker").removeClass("hidden");
      }
      else
      {
            $("#select_fieldset1_param4,#select_fieldset1_param4_chzn").removeClass("hidden");
            $("#div_datepicker").addClass("hidden");
      }


      switch (this.value)
      {
            case "feedback":
                  db_calls("get_feedbacks", $("#select_fieldset1_param4"));
                  break;
            case "tempo":
                  
                  break;
            case "script":
                  db_calls("get_scripts", $("#select_fieldset1_param4"));
                  break;
      }
});



function db_calls(opção, element)
{
      element.empty();
      $.post("requests.php", {action: opção},
      function(data)
      {
            $.each(data, function(index, value) {
                  element.append("<option value=" + this.id + ">" + this.name + "</option>");
            });
            element.trigger("liszt:updated");
      }, "json");

}