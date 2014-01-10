$(function()
{


    $("#verif_client_data").validationEngine();
    $.post("ajax/create_client.php", {action: "get_fields", campaign_id: "W00002"},
    function(data)
    {
        var controler = 0;
        var input
        var custom_class = "";
        $.each(data, function()
        {
            switch (this.name)
            {
                case "ADDRESS3":
                case "PHONE_NUMBER":
                    custom_class = "validate[custom[onlyNumberSp]]";
                    break;

                case "FIRST_NAME":
                    custom_class = "validate[required]";
                    break;

            }




            if (controler) {
                controler = 0;
                input = $("#inputs_div1");


                ;
            }
            else
            {
                controler = 1;
                input = $("#inputs_div2");


            }

            input.append($("<div>").addClass("formRow")
                    .append($("<label>").text(this.display_name))
                    .append($("<div>").addClass(" formRight")
                            .append($("<input>").addClass("span " + custom_class).attr("type", "text").attr("id", this.name).attr("name", this.name))));
            custom_class = "";





        });
    }
    , "json");

});



$("#create_client_button").on("click", function()
{
    if ($("#verif_client_data").validationEngine('validate'))
        $.post("ajax/create_client.php", {action: "create_client", info: $("#verif_client_data").serializeArray()},
        function(data)
        {
            $("#criar_marcacao").modal("show");
            $("#verif_client_data input").val("");
            $.jGrowl("Cliente criado com Sucesso", {sticky: 8000});
        }
        , "json");

});


$("#verif_client_data").on("submit", function(e)
{
    e.preventDefault();
});


$("#btn_criar_marcacao").on("click", function()
{
    $("#div_master").hide();
    $("#div_calendar").load("view/calendar.html").show();
    $("#div_calendar").show();

});