$(function()
{

    $('#form_create_client').stepy({backLabel: "Anterior", nextLabel: "Seguinte", next: function() {
            return ($(this).validationEngine("validate"));
        }, finishButton: false});

    $("#verif_client_data").validationEngine();
    $.post("ajax/create_client.php", {action: "get_fields", campaign_id: "W00002"},
    function(data)
    {
        $("#inputs_div1").empty();
        $("#inputs_div2").empty();
        $("#inputs_div3").empty();
        var input,
                custom_class = "",
                input1 = $("#inputs_div1"),
                input2 = $("#inputs_div2"),
                input3 = $("#inputs_div3");
        $.each(data, function()
        {
            elmt = $("<input>", {type: "text", id: this.name, name: this.name});
            switch (this.name)
            {
                case "PHONE_NUMBER":
                    custom_class = "validate[required,custom[onlyNumberSp],minSize[9]]";
                    input = input1;
                    break;
                case "ADDRESS3":
                case "ALT_PHONE":
                    custom_class = "validate[custom[onlyNumberSp]]";
                    input = input1;
                    break;

                case "FIRST_NAME":
                    custom_class = "validate[required]";
                    input = input1;
                    break;
                case "DATE_OF_BIRTH":
                    custom_class = "form_datetime  input-small";
                    input = input1;
                    break;
                case "EMAIL":
                    input = input1;
                    custom_class = "validate[custom[email]]";
                case "MIDDLE_INITIAL":
                    input = input1;
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
                    input = input2;
                    break;
                default:
                    input = input3;
                    break;
            }
            elmt.attr("class", custom_class);

            input.append($("<div>", {class: "formRow"})
                    .append($("<label>").text(this.display_name))
                    .append($("<div>", {class: "formRight"})
                            .append(elmt)));
            custom_class = "";
        });
        $("#inputs_div1").append($("<div>", {class: "clear"}));
        $("#inputs_div2").append($("<div>", {class: "clear"}));
        $("#inputs_div3").append($("<div>", {class: "clear"}));
        $("#extra5").autocomplete({source: ["ADSE", "ADM (ADME/ADMA/ADMFA)", "SAD/GNR (ADMG)", "SAD-PSP", "Serviços Sociais do Ministério da Justiça", "SAMS", "PT/CTT ACS", "Centro Nac. de Protecção Contra Riscos Profissionais", "APL", "SEG. SOCIAL", "PETROGAL", "EDP", "CGD", "OUTRAS"]});
        $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
    }
    , "json");

});

$("#form_create_client").submit(function(e)
{
    e.preventDefault();
    if ($("#form_create_client").validationEngine("validate"))
    {
        $.post("ajax/create_client.php", {action: "create_client", info: $("#form_create_client").serializeArray()},
        function(id)
        {
            $.jGrowl("Cliente '" + $("#FIRST_NAME").val() + "' criado com Sucesso", {sticky: 8000});
            $("#criar_marcacao").data("client_id", id).modal("show");
        }
        , "json");
    }
});


$("#btn_criar_marcacao").on("click", function()
{
    var en = btoa($("#criar_marcacao").modal("hide").data("client_id"));
    $.history.push("view/calendar.html?id=" + en);
});