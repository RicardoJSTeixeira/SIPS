$(function()
{

    $('#new_client_form').stepy({backLabel: "Anterior", nextLabel: "Seguinte", next: function() {

            if ($("#new_client_form").validationEngine("validate"))
                return true;
            else
                return false;

        }, finishButton: false});



    $("#verif_client_data").validationEngine();
    $.post("ajax/create_client.php", {action: "get_fields", campaign_id: "W00002"},
    function(data)
    {
        $("#inputs_div1").empty();
        $("#inputs_div2").empty();
        var controler = 0;
        var input;
        var custom_class = "";
        $.each(data, function()
        {
            switch (this.name)
            {

                case "PHONE_NUMBER":
                    custom_class = "validate[required,custom[onlyNumberSp]]";
                    input = $("#inputs_div1");
                    break;
                case "ADDRESS3":
                case "ALT_PHONE":
                    custom_class = "validate[custom[onlyNumberSp]]";
                    input = $("#inputs_div1");
                    break;

                case "FIRST_NAME":
                    custom_class = "validate[required]";
                    input = $("#inputs_div1");
                    break;
                case "DATE_OF_BIRTH":
                    custom_class = "form_datetime  input-small";
                    input = $("#inputs_div1");
                    break;
                case "EMAIL":
                    input = $("#inputs_div1");
                    custom_class = "validate[custom[email]]";
                case "MIDDLE_INITIAL":
                    input = $("#inputs_div1");

                    break;


                case "ADDRESS1":
                case "ADDRESS2":
                case "POSTAL_CODE":
                case "CITY":
                case "PROVINCE":
                case "STATE":
                case "COUNTRY_CODE":
                case "extra3":
                case "extra4":
                case "extra10":
                    input = $("#inputs_div2");
                    break;


                default:
                    input = $("#inputs_div3");
                    break;

            }



            input.append($("<div>").addClass("formRow")
                    .append($("<label>").text(this.display_name))
                    .append($("<div>").addClass(" formRight")
                            .append($("<input>").addClass(custom_class).attr("type", "text").attr("id", this.name).attr("name", this.name))));
            custom_class = "";





        });
        $("#inputs_div1").append($("<div>").addClass("clear"));
        $("#inputs_div2").append($("<div>").addClass("clear"));
        $("#inputs_div3").append($("<div>").addClass("clear"));
        $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
    }
    , "json");

});

$("#form_create_client").click(function(e)
{
    e.preventDefault();
    if ($("#new_client_form").validationEngine("validate"))
    {
        $.post("ajax/create_client.php", {action: "create_client", info: $("#new_client_form").serializeArray()},
        function(data)
        {
            $("#criar_marcacao").modal("show");
            $("#verif_client_data input").val("");
            $.jGrowl("Cliente criado com Sucesso", {sticky: 8000});
        }
        , "json");
    }
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