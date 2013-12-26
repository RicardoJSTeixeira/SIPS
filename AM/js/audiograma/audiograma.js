var audiograma = function(lead_id) {

      var values_regex = /[^0-9 \-\+\<\>]+/g;
      var contas_regex = /[^0-9\-]+/g;
      var lead_id = lead_id;


      $("#audiograma_form").on("submit", function(e)
      {
            e.preventDefault();
      });

      function calculate(lead_id)
      {
            var bcr = 0;
            var bcl = 0;
            $.each($("#bcr_tr input"), function()
            {
                  if (!$(this).val())
                        bcr = 1;
            });
            $.each($("#bcl_tr input"), function()
            {
                  if (!$(this).val())
                        bcl = 1;
            });
            if (bcr && bcl)
            {
                  $('#bc_tooltip').tooltip('show');
            }
            else
                  $('#bc_tooltip').tooltip('hide');

            var right_ear = {"value": 0, "text": "Sem Perda"};
            var left_ear = {"value": 0, "text": "Sem Perda"};
            var all_ear = {"value": 0, "text": "Ambos os ouvidos: Sem Perda"};
            if (validate())
            {
                  //Grava na BASE DE DADOS
                  $.post("ajax/audiograma/audiograma.php", {action: "save_audiograma", lead_id: lead_id, info: $("#audiograma_form").serializeArray()}, "json");

//APRESENTA SO VALORES CALCULADOS DE CADA OUVIDO

                  var ar500 = (($("#AR500").val().replace(contas_regex, "")));
                  var al500 = (($("#AL500").val().replace(contas_regex, "")));
                  var ar1000 = (($("#AR1000").val().replace(contas_regex, "")));
                  var al1000 = (($("#AL1000").val().replace(contas_regex, "")));
                  var ar2000 = (($("#AR2000").val().replace(contas_regex, "")));
                  var al2000 = (($("#AL2000").val().replace(contas_regex, "")));
                  var ar4000 = (($("#AR4000").val().replace(contas_regex, "")));
                  var al4000 = (($("#AL4000").val().replace(contas_regex, "")));

                  right_ear.value = ((ar500 * 4) + (ar1000 * 3) + (ar2000 * 2) + (ar4000 * 1)) / 10;
                  left_ear.value = ((al500 * 4) + (al1000 * 3) + (al2000 * 2) + (al4000 * 1)) / 10;








                  if (right_ear.value < 35 && left_ear.value < 35)
                  {
                        all_ear.text = "Ambos os ouvidos: Sem perda";
                        all_ear.value = 0;
                  }
                  else
                  {
                        all_ear.text = "";
                        all_ear.value = 1;
                        if (right_ear.value >= 35 && right_ear.value < 65)
                        {
                              right_ear.text = "Perda";
                        } else if (right_ear.value >= 65)
                        {
                              right_ear.text = "Perda Power";
                        }
                        if (left_ear.value >= 35 && left_ear.value < 65)
                        {
                              left_ear.text = "Perda";
                        } else if (left_ear.value >= 65)
                        {
                              left_ear.text = "Perda Power";
                        }
                  }


                  return {"right_ear": right_ear, "left_ear": left_ear, "all_ear": all_ear};

            }


            return false;


      }
      ;

      $("#calcular_audiograma").on("click", function(e)
      {

          console.log(lead_id);
            e.preventDefault();
            var ears = calculate(lead_id);


            $("#right_ear").text(ears.right_ear.text);
            $("#right_ear_value").val(ears.right_ear.value);
            $("#left_ear").text(ears.left_ear.text);
            $("#left_ear_value").val(ears.left_ear.value);
            $("all_ear").text(ears.all_ear.text);
            $("#all_ear_value").val(ears.all_ear.value);
      });




      function validate()
      {
            return $("#audiograma_form").validationEngine('validate');
      }



//VALIDATE DOS MAX E MIN VALUES
      $("#audiograma_table input").on("focusout", function()
      {
            var element = $(this);
            var min = element.data("min");
            var max = element.data("max");
            element.val(element.val().replace(values_regex, ""));

            if (element.val() > max)
            {
                  element.val("+" + max);
            }

            if (element.val() < min)
            {
                  if (min <= "0")
                        element.val(min);
                  else
                        element.val("-" + min);
            }
      });



      $("#right_ear").text("Sem dados");
      $("#left_ear").text("Sem dados");



      $.post("ajax/audiograma/audiograma.php", {action: "populate", lead_id: lead_id},
      function(data)
      {
            $.each(data, function()
            {
                  $.each(this.value, function()
                  {
                        $("#" + this.name).val(this.value);
                  });
            });
      }, "json");
};