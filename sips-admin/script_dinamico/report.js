/* 
Falta tuto
 */
$(function() {

      update_script();


});

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
                        $("#script_selector").append("<option value=" + data[index].id + ">" + data[index].name + "</option>");
                  });
                  get_results();
            }
      }, "json");


}




function get_results()
{
      $.post("requests.php", {action: "get_results", id_script: $("#script_selector option:selected").val()},
      function(data)
      {
            var ids = "";
            $.each(data, function(index, value) {
                  ids = ids + data[index].id_elemento.split(",")[1] + ",";
            });
            ids = ids.slice(0, -1);

            $.post("requests.php", {action: "get_reduced_data", ids: ids},
            function(data1)
            {
                
                  $.each(data1, function(index, value) {

//falta ligar os dados e mostrar tudo

                  });

            }, "json");







      }, "json");

}